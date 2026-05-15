<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Recalculate_stok extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Recalculate_stok_model', 'recalculate_stok');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $data['active_job'] = $this->recalculate_stok->get_active_job();

            $this->template->load('template', $this->access['url'] . '/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function start_sync()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');

        $active = $this->recalculate_stok->get_active_job();
        if ($active) {
            echo json_encode(['status' => false, 'message' => 'Masih ada proses sinkronisasi yang berjalan.']);
            return;
        }

        $job_id = $this->recalculate_stok->create_job('SYNC_ITEM_STOCK');

        if (!$job_id) {
            echo json_encode(['status' => false, 'message' => 'Gagal membuat antrian job.']);
            return;
        }

        // Ekstrak koneksi database yang sedang aktif saat ini (dinamis)
        $db_info = [
            'hostname' => $this->db->hostname,
            'username' => $this->db->username,
            'password' => $this->db->password,
            'database' => $this->db->database,
            'port'     => isset($this->db->port) ? $this->db->port : 3306,
        ];
        $encoded_db = base64_encode(json_encode($db_info));

        // Spawn background process
        $os = strtoupper(substr(PHP_OS, 0, 3));
        $app_path = FCPATH . 'app';
        $cmd = "php " . escapeshellarg($app_path) . " recalculate_stok/sync cli_process " . escapeshellarg($job_id) . " " . escapeshellarg($encoded_db);

        if ($os === 'WIN') {
            $run_cmd = 'start /B "" ' . $cmd . ' > NUL 2>&1';
            
            pclose(popen($run_cmd, "r"));
        } else {
            $descriptors = [
                0 => ['file', '/dev/null', 'r'],  // stdin
                1 => ['file', '/dev/null', 'w'],  // stdout
                2 => ['file', '/dev/null', 'w'],  // stderr
            ];
            $proc = proc_open($cmd, $descriptors, $pipes);
            // Langsung lepas handle — proses tetap jalan di background
            if (is_resource($proc)) {
                // Tutup pipe STDIN saja agar proses tidak nunggu input
                if (isset($pipes[0])) fclose($pipes[0]);
                unset($proc);
            }
        }

        echo json_encode(['status' => true, 'job_id' => base64url_encode($this->encrypt->encode($job_id)), 'message' => 'Proses berhasil dimasukkan antrian.']);
    }

    public function check_progress($job_id)
    {
        $job_id = (int) $this->encrypt->decode(base64_decode($job_id));
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
        $job = $this->recalculate_stok->get_job($job_id);
        if ($job) {
            echo json_encode(['status' => true, 'data' => [
                'STATUS'        => $job['STATUS'],
                'MESSAGE'       => $job['MESSAGE'],
                'PROGRESS'      => $job['PROGRESS'],
                'DURATION_SEC'  => $job['DURATION_SEC'],
            ]]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Job tidak ditemukan.']);
        }
    }

    public function cancel_sync()
    {
        if (!$this->input->is_ajax_request()) exit('No direct script access allowed');
    
        $job_id = (int) $this->encrypt->decode(base64_decode($this->input->post('job_id')));
        if (!$job_id) {
            echo json_encode(['status' => false, 'message' => 'Job ID tidak valid']);
            return;
        }
    
        $job = $this->recalculate_stok->get_job($job_id);
    
        if (!$job) {
            echo json_encode(['status' => false, 'message' => 'Job tidak ditemukan']);
            return;
        }
    
        if (!in_array($job['STATUS'], ['running', 'pending', 'queued'])) {
            echo json_encode(['status' => false, 'message' => 'Job sudah selesai atau gagal']);
            return;
        }
    
        //  STEP 1: Kill SP di MySQL via KILL QUERY
        //  KILL QUERY  → hentikan query yang berjalan, koneksi tetap hidup
        $thread_id = !empty($job['THREAD_ID']) ? (int) $job['THREAD_ID'] : 0;
    
        if ($thread_id > 0) {
            // Cek dulu apakah thread masih aktif di MySQL
            $thread_exists = $this->db->query(
                "SELECT ID FROM information_schema.PROCESSLIST WHERE ID = ?",
                [$thread_id]
            )->row();
    
            if ($thread_exists) {
                // Kill query SP yang berjalan
                $this->db->query("KILL QUERY {$thread_id}");
    
                // Tunggu sebentar agar MySQL sempat rollback
                usleep(300000); // 0.3 detik
    
                // Cek lagi — kalau masih ada, kill koneksinya sekalian
                $still_alive = $this->db->query(
                    "SELECT ID FROM information_schema.PROCESSLIST WHERE ID = ?",
                    [$thread_id]
                )->row();
    
                if ($still_alive) {
                    $this->db->query("KILL {$thread_id}");
                }
            }
        }
    
        //  STEP 2: Kill PHP process (cli_process + monitor_progress)
        $pid = !empty($job['PROCESS_ID']) ? (int) $job['PROCESS_ID'] : 0;
    
        if ($pid > 0) {
            if (function_exists('posix_getpgid')) {
                $pgid = posix_getpgid($pid);
                if ($pgid !== false && $pgid > 0) {
                    posix_kill(-$pgid, SIGTERM);
                    usleep(200000);
                    posix_kill(-$pgid, SIGKILL);
                } else {
                    posix_kill($pid, SIGTERM);
                    usleep(200000);
                    posix_kill($pid, SIGKILL);
                }
            } else {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    // Windows — pakai proc_open karena taskkill butuh exec
                    proc_open("taskkill /F /T /PID {$pid}", [], $pipes);
                } else {
                    // Linux tanpa posix extension — kill via proc_open
                    proc_open("kill -TERM -- -{$pid}", [], $pipes);
                    usleep(200000);
                    proc_open("kill -KILL -- -{$pid}", [], $pipes);
                    proc_open("pkill -KILL -P {$pid}", [], $pipes);
                }
            }
        }
    
        //  STEP 3: Update status di DB
        $this->recalculate_stok->update_job($job_id, [
            'STATUS'      => 'failed',
            'MESSAGE'     => 'Dibatalkan oleh user.',
            'FINISHED_AT' => date('Y-m-d H:i:s'),
            'PROCESS_ID'  => null,
            'THREAD_ID'   => null,
        ]);
    
        echo json_encode([
            'status'    => true,
            'message'   => 'Job berhasil dibatalkan.',
        ]);
    }

    public function history()
    {
        $data['history']    = $this->recalculate_stok->get_history();
        $res = $this->load->view($this->access['url'].'/history',$data,true);
        sendSuccess($res, 'success get data');
    }
}
