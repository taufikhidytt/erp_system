<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Import_data extends MX_Controller
{
    /** Kolom header template, urutan HARUS sama dengan kolom Excel */
    const HEADER_TEMPLATE = [
        'COA_CODE', 'COA_NAME', 'COA_SALDO', 'KURS', 'MATA_UANG_CODE','SALDO_AWAL_KURS'
    ];

    private $dt_coa = [];

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        date_default_timezone_set('Asia/Jakarta');
    }

    public function cli_process($encode_param, $encoded_db)
    {
        if (!is_cli()) {
            show_404();
        }

        $param  = json_decode($this->encrypt->decode(base64url_decode($encode_param)) ?? '[]', true);
        $job_id = isset($param['job_id'])?(int)$param['job_id']:0;
        $dt_period = isset($param['dt_period'])?$param['dt_period']:[];

        if(!$job_id || !isset($dt_period['PERIOD_NAME'])){
            exit('Parameter is failed');
        }

        
        // --- 1. Buka koneksi DB karena setiap user memiliki akses db berbeda
        $db = $this->_init_cli_db($encoded_db);
        if (!$db) {
            exit('DB connection failed');
        }

        // Reload model dengan koneksi baru
        $this->load->model('Gl_balance_import_model', 'import_model');

        // --- 2. Ambil job & validasi ---
        $job = $this->import_model->get_job($job_id);
        if (!$job || in_array($job['STATUS'], ['failed', 'done'])) {
            exit('Job tidak valid atau sudah selesai.');
        }

        $this->import_model->update_job($job_id, [
            'STATUS'     => 'running',
            'STARTED_AT' => date('Y-m-d H:i:s'),
            'MESSAGE'    => 'Mulai membaca file...',
            'PROCESS_ID' => getmypid(),
        ]);

        try {
            $json_data  = json_decode($job['JSON_TEXT'], true);
            $upload_dir = FCPATH . 'assets/upload/coa_balance_import/';
            $filename   = $json_data['filename'] ?? '';
            $file_path  = $upload_dir.$filename;
            $chunk      = (int) $job['CHUNK'];
            $start_row  = 6;

            if (!file_exists($file_path)) {
                throw new Exception("File Excel tidak ditemukan di: $file_path");
            }

            $this->load->library('simpleexcel');

            $progress     = 0;
            $max_progress = (int)$job['MAX_PROGRESS'];
            $results      = ['success' => [], 'failed' => []];

            while ($progress < $max_progress) {
                $skip_rows = $start_row + $progress;

                $dataChunk = $this->simpleexcel->read($file_path, self::HEADER_TEMPLATE, [
                    'skip_rows'  => $skip_rows,
                    'chunk_size' => $chunk,
                ]);

                if (empty($dataChunk)) {
                    break;
                }

                // --- Preload untuk chunk ini ---
                $lookups = $this->_preload_db($dataChunk, $dt_period);

                foreach ($dataChunk as $idx => $row) {
                    $row_number = $skip_rows + $idx + 1;
                    $this->_validate_and_map_row($row, $row_number,$lookups, $results);
                    $progress++;
                }

                // Update progress ke DB setiap selesai satu chunk
                $this->import_model->update_job($job_id, [
                    'PROGRESS' => $progress,
                    'MESSAGE'  => "Memvalidasi ".numb_format($progress,0)." / ".numb_format($max_progress,0)." baris...",
                ]);
            }

            // --- 6. Simpan result ke file JSON
            $result_filename = folder_key().sha1(sha1($job_id)).'.json';
            $result_path = $upload_dir . $result_filename;
            $write_ok    = file_put_contents($result_path, json_encode([
                'file_path' => $file_path,
                'success'   => $results['success'],
                'failed'    => $results['failed'],
                'period'    => $dt_period
            ]));

            if ($write_ok === false) {
                throw new Exception("Gagal menulis file hasil validasi.");
            }

            // --- 7. Update job: done ---
            $json_data['result_filename'] = $result_filename;
            $this->import_model->update_job($job_id, [
                'STATUS'      => 'done',
                'MESSAGE'     => 'Validasi periode '.$dt_period['PERIOD_NAME'].' telah selesai. ' . count($results['success']) . ' success, ' . count($results['failed']) . ' gagal.',
                'PROGRESS'    => $max_progress,
                'JSON_TEXT'   => json_encode($json_data),
                'FINISHED_AT' => date('Y-m-d H:i:s'),
                'PROCESS_ID'  => null,
            ]);

        } catch (Exception $e) {
            $this->import_model->update_job($job_id, [
                'STATUS'      => 'failed',
                'MESSAGE'     => 'Gudang periode '.$dt_period['PERIOD_NAME'].' Error: ' . $e->getMessage(),
                'FINISHED_AT' => date('Y-m-d H:i:s'),
                'PROCESS_ID'  => null,
            ]);
        }
    }

    private function _init_cli_db($encoded_db)
    {
        $db_config = json_decode($this->encrypt->decode(base64url_decode($encoded_db)), true);
        if (!$db_config) return false;

        $db_config['dbdriver'] = 'mysqli';
        $db_config['db_debug'] = FALSE;

        $prev_error_level = error_reporting(0);
        $db = $this->load->database($db_config, TRUE);
        error_reporting($prev_error_level);

        if (!$db || !$db->conn_id) {
            return false;
        }

        // Ganti koneksi default CI agar model-model menggunakan koneksi ini
        $ci     = &get_instance();
        if (isset($ci->db) && is_object($ci->db)) {
            @$ci->db->close();
        }
        $ci->db = $db;

        return $db;
    }

    private function _clean_key($key)
    {
        $key = trim($key??'');
        $key = strtolower($key);
        return preg_replace('/[^a-z0-9]/', '', $key);
    }

    private function _validate_and_map_row(array $row, $row_number, array $lookups, array &$results)
    {
        $errors = [];

        // --- Validasi required ---
        $required = [
            'COA_CODE'  => 'Kode Account',
            'COA_NAME'  => 'Nama Account',
            'COA_SALDO' => 'Saldo Awal',
            'KURS'      => 'Kurs',
            'MATA_UANG_CODE' => 'Mata Uang'
        ];
        foreach ($required as $field => $label) {
            $val = trim($row[$field] ?? '');
            if ($val === "" ) {
                $errors[] = "{$label} wajib diisi";
            }
        }


        // --- Resolve ID dari lookup ---
        $key_item = $this->_clean_key(trim($row['COA_CODE']).'_'.trim($row['COA_NAME']).'_'.trim($row['KURS']).'_'.trim($row['MATA_UANG_CODE']));
        $val_item = isset($lookups[$key_item])? (int) $lookups[$key_item]: 0;

        if (!$val_item)    $errors[] = 'Account tidak ditemukan di master data';

        //check number format
        foreach ([
            'COA_SALDO'  => [],
            'KURS'       => [],
        ] as $field => $attr) {
            $val = $row[$field];
            if (!is_numeric($val)) {
                $errors[] = ucwords(str_replace('_',' ', $required[$field])).' harus berupa angka';
                continue;
            }
        }

        // --- Cek duplikat ---
        if (empty($errors)) {
            if (isset($this->dt_coa[$key_item])) {
                $errors[] = 'Account sudah '.($this->dt_description[$key_item] =='db'?'terdaftar di database':'ada pada baris sebelumnya');
            }else{
                $this->dt_coa[$key_item] = 'file';
            }
        }
        // --- End cek duplikasi

        $mapped = $row;
        $mapped['SALDO_AWAL_KURS'] = (float) $mapped['COA_SALDO'] * (float) $mapped['KURS'];
        $mapped['row_number']  = $row_number;
        if (!empty($errors)) {
            $mapped['error_reason'] = implode('; ', $errors);
            $results['failed'][]    = $mapped;
        } else {
            $mapped['A_SALDO'] = (float) $mapped['COA_SALDO'] * (float) $mapped['KURS'];
            $mapped['update'] = [
                'COA_BALANCE_ID'   => (int) $val_item,
            ];
            $results['success'][]   = $mapped;
        }
    }
    
    private function _preload_db(array $dataChunk, array $dt_period)
    {
        $arr_coa_code  = [];
 
        foreach ($dataChunk as $row) {
            $coa_code  = trim($row['COA_CODE'] ?? '');
            if($coa_code) $arr_coa_code[] = $coa_code;
        }
 
        return $this->import_model->get_coa($arr_coa_code, (int) $dt_period['PERIOD_NAME']);
    }
}