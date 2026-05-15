<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sync extends MX_Controller {

	public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        date_default_timezone_set('Asia/Jakarta');
    }

    public function cli_process($job_id, $encoded_db)
    {
        $job_id    = (int) $job_id;
        $db_config = json_decode(base64_decode($encoded_db), true);
        $db_config['dbdriver'] = 'mysqli';
        $db_config['db_debug'] = FALSE;

        $error_level = error_reporting();
        error_reporting(0);

        // Dua koneksi terpisah:
        // $db_sp  → khusus CALL SP (koneksi ini yang akan idle lama)
        // $db_job → khusus update job status, ping berkala agar tetap hidup
        $db_sp  = $this->load->database($db_config, TRUE);
        $db_job = $this->load->database($db_config, TRUE);

        $ci = &get_instance();
        error_reporting($error_level);

        if (!$db_sp || !$db_sp->conn_id || !$db_job || !$db_job->conn_id) {
            log_message('error', '[SYNC] DB connect failed for job #' . $job_id);
            exit();
        }

        // $ci->db → db_job, agar model (update_job, get_job) pakai koneksi job
        if (isset($ci->db) && is_object($ci->db) && isset($ci->db->conn_id)) {
            $ci->db->close();
        }
        $ci->db = $db_job;

        //  2. Load model
        $this->load->model('Recalculate_stok_model', 'recalculate_stok');

        $job = $this->recalculate_stok->get_job($job_id);
        if (!$job || $job['STATUS'] === 'failed') return;

        //  3. Set running + spawn monitor
        $started_at = date('Y-m-d H:i:s');

        // Thread ID diambil dari $db_sp — koneksi yang akan menjalankan SP
        // Dipakai cancel_sync() untuk KILL QUERY
        $thread_row = $db_sp->query("SELECT CONNECTION_ID() AS tid")->row();
        $thread_id  = $thread_row ? (int) $thread_row->tid : null;

        $this->recalculate_stok->update_job($job_id, [
            'STATUS'     => 'running',
            'STARTED_AT' => $started_at,
            'PROGRESS'   => 3,
            'MESSAGE'    => 'Proses dimulai...',
            'THREAD_ID'  => $thread_id,
            'PROCESS_ID' => getmypid(),
        ]);

        // Spawn monitor_progress
        $os       = strtoupper(substr(PHP_OS, 0, 3));
        $app_path = FCPATH . 'app';
        $cmd      = "php " . escapeshellarg($app_path) . " recalculate_stok/sync monitor_progress " . escapeshellarg($job_id) . " " . escapeshellarg($encoded_db);
        if ($os === 'WIN') {
            $run_cmd = 'start /B "" ' . $cmd . ' > NUL 2>&1';
            pclose(popen($run_cmd, "r"));
        } else {
            $descriptors = [
                0 => ['file', '/dev/null', 'r'],
                1 => ['file', '/dev/null', 'w'],
                2 => ['file', '/dev/null', 'w'],
            ];
            $proc = proc_open($cmd, $descriptors, $pipes);
            // JANGAN proc_close() — blocking, nunggu monitor selesai (deadlock!)
            if (is_resource($proc)) {
                if (isset($pipes[0])) fclose($pipes[0]);
                unset($proc);
            }
        }

        $start_time = time();

        try {
            //  4. Jalankan SP via $db_sp
            //     Update job via $ci->db ($db_job) — koneksi berbeda, selalu aktif

            // === SP 1 ===
            $this->recalculate_stok->update_job($job_id, ['MESSAGE' => 'Menyinkronkan Item Stock...']);
            $db_sp->query("CALL SP_SYNC_ITEM_STOCK()");
            $this->_clear_result_buffer($db_sp);

            if ($this->recalculate_stok->is_cancelled($job_id)) return;

            // === SP 2 ===
            $this->recalculate_stok->update_job($job_id, ['MESSAGE' => 'Menyinkronkan Consignment Stock...']);
            $db_sp->query("CALL SP_SYNC_CONSIGNMENT_STOCK()");
            $this->_clear_result_buffer($db_sp);

            // Tambah SP berikutnya dengan pola yang sama:
            // $this->recalculate_stok->update_job($job_id, ['MESSAGE' => '...']);
            // $db_sp->query("CALL SP_LAINNYA()");
            // $this->_clear_result_buffer($db_sp);

            //  5. Selesai — update via db_job yang bersih
            $duration = time() - $start_time;
            $this->recalculate_stok->update_job($job_id, [
                'STATUS'       => 'done',
                'PROGRESS'     => 100,
                'MESSAGE'      => 'Semua stok berhasil disinkronkan dalam ' . $duration . ' detik.',
                'FINISHED_AT'  => date('Y-m-d H:i:s'),
                'DURATION_SEC' => $duration,
                'PROCESS_ID'   => null,
                'THREAD_ID'    => null,
            ]);

        } catch (Exception $e) {
            $this->recalculate_stok->update_job($job_id, [
                'STATUS'      => 'failed',
                'MESSAGE'     => 'Error: ' . $e->getMessage(),
                'FINISHED_AT' => date('Y-m-d H:i:s'),
                'PROCESS_ID'  => null,
                'THREAD_ID'   => null,
            ]);
            log_message('error', '[SYNC] Job #' . $job_id . ' failed: ' . $e->getMessage());
        } finally {
            // Tutup kedua koneksi
            if ($db_sp  && $db_sp->conn_id)  $db_sp->close();
            if ($db_job && $db_job->conn_id) $db_job->close();
        }
    }

    public function monitor_progress($job_id, $encoded_db)
    {
        if (!is_cli()) exit('CLI only');
    
        $job_id = (int) $job_id;
    
        // Koneksi DB
        $db_config = json_decode(base64_decode($encoded_db), true);
        $db_config['dbdriver'] = 'mysqli';
        $db_config['db_debug'] = FALSE;
    
        $error_level = error_reporting();
        error_reporting(0);
        $db_obj = $this->load->database($db_config, TRUE);
        $ci     = &get_instance();
        error_reporting($error_level);
    
        if (!$db_obj || !$db_obj->conn_id) exit();
    
        if (isset($ci->db) && is_object($ci->db) && isset($ci->db->conn_id)) {
            $ci->db->close();
        }
        $ci->db = $db_obj;
    
        $this->load->model('Recalculate_stok_model', 'recalculate_stok');

        // Register signal handler agar monitor keluar bersih saat di-kill
        // Dipanggil oleh cancel_sync() via posix_kill / kill command
        if (function_exists('pcntl_signal')) {
            $shutdown = function() use ($job_id) {
                log_message('info', "[MONITOR #{$job_id}] terminated by signal.");
                exit(0);
            };
            pcntl_signal(SIGTERM, $shutdown);
            pcntl_signal(SIGINT,  $shutdown);
        }

        // Ambil rata-rata durasi dari histori
        $avg_total = $this->recalculate_stok->get_avg_duration('SYNC_ITEM_STOCK') ?: 144.0;
    
        // Aktifkan async signal handling (PHP 7.1+)
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
        }

        while (true) {
            sleep(3);

            // Dispatch signal yang masuk (fallback jika async tidak tersedia)
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            $job = $this->recalculate_stok->get_job($job_id);
    
            // Berhenti jika sudah terminal
            if (!$job || $job['STATUS'] !== 'running') {
                log_message('info', "[MONITOR #{$job_id}] stop. status=" . ($job['STATUS'] ?? 'null'));
                break;
            }
    
            // Hitung elapsed dari STARTED_AT di DB (bukan dari variabel lokal)
            $elapsed = $job['STARTED_AT']
                ? max(0, time() - strtotime($job['STARTED_AT']))
                : 0;
    
            // ----------------------------------------------------------
            //  Rumus logaritmik: 3% → 98%
            //
            //  f(t) = 3 + 95 * (1 - e^(-3 * t / T))
            //
            //  Sifat kurva:
            //  - t=0       → 3%   (titik awal)
            //  - t=T*0.35  → ~60% (naik cepat di awal)
            //  - t=T*0.7   → ~80% (mulai melambat)
            //  - t=T       → ~92% (mendekati tapi tidak capai 98%)
            //  - t>T       → tetap naik pelan, tidak pernah capai 99%
            //  - 100%      → HANYA dari cli_process() saat done
            // ----------------------------------------------------------
            $progress = (int) min(98, round(3 + 95 * (1 - exp(-3 * $elapsed / $avg_total))));
    
            // Hanya update jika progress naik (cegah turun karena race condition)
            if ($progress > (int) $job['PROGRESS']) {
    
                // Hitung ETA
                $remaining = max(0, (int)($avg_total - $elapsed));
                $m         = (int) floor($remaining / 60);
                $s         = $remaining % 60;
                $eta       = $remaining <= 0
                    ? 'hampir selesai...'
                    : ($m > 0 ? "~{$m} mnt {$s} dtk lagi" : "~{$s} dtk lagi");
    
                // Gabungkan pesan SP aktif + ETA
                // Di sini kita append ETA saja tanpa overwrite label SP-nya
                $current_msg = trim($job['MESSAGE'] ?? '');
                // Hapus ETA lama jika ada (pola: " (~...")
                $base_msg    = preg_replace('/\s*\(~[^)]*\)$/', '', $current_msg);
                $new_msg     = str_replace('(hampir selesai...)','',$base_msg) . " ({$eta})";
    
                $this->recalculate_stok->update_job($job_id, [
                    'PROGRESS' => $progress,
                    'MESSAGE'  => $new_msg,
                ]);
            }
        }
    }

    private function _clear_result_buffer($db)
    {
        if (empty($db->conn_id)) return;
        // Batas iterasi agar tidak infinite loop jika SP bermasalah
        $max = 20;
        $i   = 0;
        while ($i++ < $max && $db->conn_id->more_results() && $db->conn_id->next_result()) {
            $result = $db->conn_id->store_result();
            if ($result) $result->free();
        }
    }
}