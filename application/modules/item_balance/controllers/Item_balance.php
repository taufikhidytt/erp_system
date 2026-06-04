<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item_balance extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Item_balance_model','item_balance');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $data['period']     = $this->item_balance->get_period()->row();

            $page = $data['period'] ? 'index' : 'not_found';
            $this->template->load('template', $this->access['url'] . '/'.$page, $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_warehouse()
    {
        $result = $this->item_balance->get_warehouse()->result();
        foreach ($result as $v) {
            $v->id = base64url_encode($this->encrypt->encode($v->id));
        }
        echo json_encode($result);
    }

    public function get_data()
    {
        $this->load->model('M_datatables', 'datatables');
        $warehouse_id   = (int) $this->encrypt->decode(base64_decode($this->input->post('warehouse_id')));
        $period         = (int) $this->encrypt->decode(base64_decode($this->input->post('period')));
        $order_field = [null,
            'i.ITEM_CODE',
            '(COALESCE( i.PART_NUMBER,  i.ITEM_DESCRIPTION ))',
            'i.UOM_CODE',
            '(CASE WHEN a.QTY_AWAL != 0 THEN a.QTY_AWAL WHEN a.QTY_MASUK != 0 THEN a.QTY_MASUK ELSE a.QTY_KELUAR * - 1 END)',
            'a.HPP',
            '(CASE WHEN a.QTY_AWAL != 0 THEN a.QTY_AWAL * a.HPP WHEN a.QTY_MASUK != 0 THEN a.QTY_MASUK * a.HPP ELSE (a.QTY_KELUAR * - 1) * a.HPP END)'
        ];
        $params = [
            'table' => 'item_balance a',
            'select' => ['a.ITEM_BALANCE_ID,a.PERIOD_NAME,a.ITEM_ID,
                i.ITEM_CODE,
                i.UOM_CODE,
                a.HPP',
                ['COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) AS ITEM_NAME', FALSE],
                ['CASE WHEN a.QTY_AWAL != 0 THEN a.QTY_AWAL WHEN a.QTY_MASUK != 0 THEN a.QTY_MASUK ELSE a.QTY_KELUAR * - 1 END AS QTY_AWAL', FALSE],
                ['CASE WHEN a.QTY_AWAL != 0 THEN a.QTY_AWAL * a.HPP WHEN a.QTY_MASUK != 0 THEN a.QTY_MASUK * a.HPP ELSE (a.QTY_KELUAR * - 1) * a.HPP END AS SUBTOTAL', FALSE]
            ],
            'joins' => [
                ['item i','a.ITEM_ID = i.ITEM_ID','inner']
            ],
            'where' => [
                'a.PERIOD_NAME'     => $period,
                'a.WAREHOUSE_ID'    => $warehouse_id,
                'i.ITEM_ID !='      => 1010
            ],
            'where_raw' => "i.JENIS_ID = FN_GET_VAR_VALUE ('GOODS')",
            'column_search' => $order_field,
            'column_order'  => $order_field,
            'order'         => ['i.ITEM_CODE' => 'asc']
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->ITEM_BALANCE_ID));
            $res = [
                'id' => $id,
                'no' => $no,
                'item_code' => $row->ITEM_CODE,
                'item_name' => $row->ITEM_NAME,
                'satuan' => $row->UOM_CODE,
                'initial_stock' => numb_format($row->QTY_AWAL),
                'initial_hpp' => numb_format($row->HPP),
                'subtotal' => numb_format($row->SUBTOTAL),
            ];
            return $res;
        }));
    }

    public function save(){
        try {
            $warehouse_id   = (int) $this->encrypt->decode(base64_decode($this->input->post('warehouse_id')));
            $dt_period      = $this->item_balance->get_period()->row();
            if(!$dt_period || $dt_period->OPEN_FLAG != 'Y'){
                throw new Exception("Stok & HPP Awal tidak bisa diubah karena periode awal sudah ditutup.");
            }
            $period         = (int) $dt_period->PERIOD_NAME;

            $rows = json_decode($this->input->post('rows'), true);
            if (!$rows){
                throw new Exception("Tidak ada data yang dikirim");
            };

            $now     = date('Y-m-d H:i:s');
            $user_id = (int) $this->session->id;
            $arr_update = [];

            //pengecekan untuk update
            $ids      = array_column(array_filter($rows, fn($r) => empty($r['isNew'])), 'id');
            $ids      = array_map(fn($id) => $this->encrypt->decode(base64url_decode($id)), $ids);
            $existing = [];
            if(!empty($ids)){
                $existing = array_column($this->item_balance->get_by_id($ids, $warehouse_id, $period)->result_array(), null, 'ITEM_BALANCE_ID');
            }

            foreach ($rows as $row) {
                $fields = $row['fields'] ?? [];
                $isNew  = !empty($row['isNew']);
                $id     = $isNew ? null : $this->encrypt->decode(base64url_decode($row['id'] ?? ''));

                if(!isset($existing[$id])){
                    throw new Exception("Item tidak ditemukan untuk melakukan update.");
                }
                $arr_update[] = [
                    'ITEM_BALANCE_ID'   => $id,
                    'QTY_AWAL'          => (float)($fields['initial_stock'] ?? null),
                    'HPP'               => (float)($fields['initial_hpp'] ?? null),
                    'LAST_UPDATE_BY'    => $user_id,
                    'LAST_UPDATE_DATE'  => $now,
                ];
            }

            $this->db->trans_start();
            if (!empty($arr_update)) $this->item_balance->update_batch($arr_update);
            $this->db->trans_complete();

            $this->db->trans_status() === false
                ? sendWarning('Gagal menyimpan data.')
                : sendSuccess('', 'Data berhasil disimpan!');

        } catch (Exception $err) {
            $this->db->trans_rollback();
            sendWarning($err->getMessage());
        }
    }

    public function import()
    {
        checkAccess('import');
        try {
            $data['title']      = 'Import';
            $data['breadcrumb'] = 'Import';
            $data['active_job'] = $this->item_balance->get_active_job();
            $data['period']     = $this->item_balance->get_period()->row();

            $page = $data['period'] ? 'import' : 'not_found';
            $this->template->load('template', $this->access['url'] . '/'.$page, $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function template_import()
    {
        try {
            checkAccess('import');
            $dt_period      = $this->item_balance->get_period()->row();
            if(!$dt_period) {
                throw new Exception("Periode tidak ditemukan");
            }else if($dt_period->OPEN_FLAG !== 'Y'){
                throw new Exception("Stok & HPP Awal tidak bisa diubah karena periode awal sudah ditutup.");
            }
            $period = $dt_period->PERIOD_NAME;

            $warehouse_id   = (int) $this->encrypt->decode(base64_decode($this->input->get('w')));
            $warehouse      = $this->item_balance->get_warehouse_by_id($warehouse_id)->row();
            if(!$warehouse){
                throw new Exception("Data gudang tidak ditemukan");
            }

            $this->load->library('simpleexcel');
            $headerTemplate = $this->_headerTemplate();
            $sheets = [];
            $sheets[] = [
                'title'       => 'Template Import',
                'header'      => $headerTemplate,
                'data'        => $this->item_balance->get_item_balance($warehouse_id,$period)->result_array(),
                'header_info' => [
                    'h1: TEMPLATE IMPORT '.strtoupper($this->access['PROMPT']),
                    'Gudang : '.$warehouse->WAREHOUSE_NAME,
                    'Periode : '.$period,
                    'Kolom dengan tanda (*) WAJIB DIISI.',
                    'Diisi hanya kolom "STOK AWAL" dan "HPP AWAL" saja'
                ]
            ];
            $config = [
                'background'    => '3D7BB9',
                'color'         => 'FFFFFF',
                'freeze_header' => true,
                'auto_filter'   => true,
            ];

            $this->simpleexcel->write($sheets, 'Template_Import_'.str_replace(' ','_', ucwords($this->access['PROMPT'])).'_' . date('Ymd'), $config);
        } catch (Exception $err) {
            $this->session->set_flashdata('warning', $err->getMessage());
            redirect($this->access['url'].'/import');
        }
    }

    private function _headerTemplate()
    {
        return [
            'ITEM_CODE' => 'KODE ITEM (*)',
            'ITEM_NAME' => 'NAMA ITEM (*)',
            'UOM_CODE'  => 'SATUAN (*)',
            'QTY_AWAL'  => 'STOK AWAL (*)',
            'HPP'       => 'HPP AWAL (*)',
            'SUBTOTAL'  => 'SUBTOTAL',
        ];
    }

    public function import_history()
    {
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'import_history a',
            'select' => [
                'a.IMPORT_HISTORY_ID,a.MESSAGE,a.JSON_TEXT,a.CREATED_DATE,a.STARTED_AT,a.FINISHED_AT,a.STATUS,b.ERP_USER_NAME',
            ],
            'joins' => [
                ['erp_user b', 'b.ERP_USER_ID = a.CREATED_BY', 'inner'],
            ],
            'where' => [
                'a.IMPORT_KEY'  => 'item_balance',
            ],
            'where_in' => [
                'a.STATUS'      => ['archived','done']
            ],
            'column_search' => [null,'a.MESSAGE','b.ERP_USER_NAME', 'a.CREATED_DATE','a.STATUS'],
            'column_order'  => [null,'a.MESSAGE','b.ERP_USER_NAME', 'a.CREATED_DATE'. 'a.STATUS'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            
            $id   = base64url_encode($this->encrypt->encode($row->IMPORT_HISTORY_ID));
            $json = json_decode($row->JSON_TEXT, true);
            $upload_dir  = FCPATH . 'assets/upload/item_balance_import/archived/';
            $result_path = $upload_dir . (isset($json['result_filename'])?$json['result_filename']:'null');
            $action = '';
            if(file_exists($result_path)){
                $action = '<a href="'.base_url('item_balance/download_failed/'.$id).'" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Download data gagal" target="_blank">
                    <i class="ri-download-2-line"></i>
                </a>';
            }
            if($row->STATUS == 'done'){
                $action = '<button type="button "class="btn btn-sm btn-outline-primary btn-proccess-import" data-id="'.$id.'" data-bs-toggle="tooltip" data-bs-placement="left" title="Lanjutkan Proses Import">
                    <i class="ri-refresh-fill"></i>
                </button>';
            }
            
            $res = [
                'no'        => $no,
                'message'   => $row->MESSAGE,
                'user'      => $row->ERP_USER_NAME,
                'tanggal'   => $row->CREATED_DATE,
                'status'    => '<span class="badge bg-'.($row->STATUS=='done'?'primary':'success').' text-uppercase">'.$row->STATUS.'</span>',
                'action'    => $action
            ];
            return $res;
        }));
    }

    public function upload()
    {
        if (!$this->input->is_ajax_request()) {
            redirect('404');
        }

        $file_path = null;

        try {
            checkAccess('import');
            $dt_period = $this->item_balance->get_period()->row();
            
            if (!$dt_period) {
                throw new Exception("Periode tidak ditemukan");
            } 
            if ($dt_period->OPEN_FLAG !== 'Y') {
                throw new Exception("Stok & HPP Awal tidak bisa diubah karena periode awal sudah ditutup.");
            }

            $warehouse_id = (int) $this->encrypt->decode(base64_decode($this->input->post('warehouse_id')));
            $warehouse    = $this->item_balance->get_warehouse_by_id($warehouse_id)->row();
            
            if (!$warehouse) {
                throw new Exception("Data gudang tidak ditemukan");
            }

            $upload_dir = FCPATH . 'assets/upload/item_balance_import/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $config = [
                'upload_path'   => $upload_dir,
                'allowed_types' => 'xlsx',
                'max_size'      => 20480,
                'encrypt_name'  => TRUE,
            ];
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('import_file')) {
                throw new Exception($this->upload->display_errors('', ''));
            }

            $upload_data = $this->upload->data();
            $file_path   = $upload_data['full_path'];
            $filename    = $upload_data['file_name'];
            $data_start_row = 7;

            $reader    = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_path);
            $info      = $reader->listWorksheetInfo($file_path);
            $totalRows = (int)($info[0]['totalRows'] ?? 0);
            $max_progress = max(0, $totalRows - $data_start_row);

            if ($max_progress <= 0) {
                throw new Exception('File kosong atau tidak ada data (data dimulai dari baris ke-8).');
            }

            $this->db->trans_begin();
            
            $job_id = $this->item_balance->create_job([
                'IMPORT_KEY'    => 'item_balance',
                'MAX_PROGRESS'  => (int) $max_progress,
                'CHUNK'         => 100,
                'JSON_TEXT'     => json_encode(['filename' => $filename, 'warehouse' => $warehouse, 'period' => $dt_period])
            ]);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception("Database Error saat membuat job.");
            }

            $this->db->trans_commit();

            $db_info = [
                'hostname' => $this->db->hostname,
                'username' => $this->db->username,
                'password' => $this->db->password,
                'database' => $this->db->database,
                'port'     => isset($this->db->port) ? $this->db->port : 3306,
            ];
            
            $encoded_db = base64url_encode($this->encrypt->encode(json_encode($db_info)));
            $encode_id  = base64url_encode($this->encrypt->encode($job_id));
            $param  = base64url_encode($this->encrypt->encode(json_encode([
                    'job_id'    => $job_id,
                    'warehouse' => $warehouse,
                    'dt_period' => $dt_period,
                ])));
            $app_path   = FCPATH . 'app';
            $cmd = 'php ' . escapeshellarg($app_path) . ' item_balance/import_data cli_process ' 
                . escapeshellarg($param) . ' ' . escapeshellarg($encoded_db);
                
            $os = strtoupper(substr(PHP_OS, 0, 3));
            if ($os === 'WIN') {
                pclose(popen('start /B "" ' . $cmd . ' > NUL 2>&1', 'r'));
            } else {
                $descriptors = [0 => ['file', '/dev/null', 'r'], 1 => ['file', '/dev/null', 'w'], 2 => ['file', '/dev/null', 'w']];
                $proc = proc_open($cmd . ' &', $descriptors, $pipes);
                if (is_resource($proc)) proc_close($proc);
            }

            sendSuccess($encode_id, '');

        } catch (Throwable $e) {
            $this->db->trans_rollback();

            if ($file_path && file_exists($file_path)) {
                @unlink($file_path);
            }

            sendWarning($e->getMessage());
        }
    }

    public function get_status()
    {
        checkAccess('import');

        if (!$this->input->is_ajax_request()) {
            redirect('404');
        }

        $job_id = (int) $this->encrypt->decode(base64url_decode($this->input->get('job_id')));
        $job    = $this->item_balance->get_job($job_id);

        if (!$job) {
            sendWarning('Data import tidak ditemukan.');die();
        }

        $res = [
            'status'   => $job['STATUS'],
            'progress' => (int)$job['PROGRESS'],
            'total'    => (int)$job['MAX_PROGRESS'],
            'message'  => $job['MESSAGE'],
        ];

        if ($job['STATUS'] === 'done') {
            $result = $this->_read_result_counts($job);
            $res['success_count'] = count($result['success'] ?? []);
            $res['failed_count']  = count($result['failed']  ?? []);

            $data['result'] = $result;
            $data['header'] = $this->_headerTemplate();
            $data['success_count']  = $res['success_count'];
            $data['failed_count']   = $res['failed_count'];
            $res['result_list']     = $this->load->view($this->access['url'].'/import_result',$data,true);
        }

        sendSuccess($res,'');
    }

    private function _read_result_counts(array $job)
    {
        $json        = json_decode($job['JSON_TEXT'], true);
        $upload_dir  = FCPATH . 'assets/upload/item_balance_import/';
        $result_filename = $json['result_filename'];
        $result_path = $upload_dir . $result_filename;
        
        if (!file_exists($result_path)) {
            if(!file_exists($upload_dir.'archived/'.$result_filename)){
                return ['success' => [], 'failed' => []];
            }else{
                $result_path = $upload_dir. 'archived/' . $result_filename;
            }
        }

        $res_json = json_decode(file_get_contents($result_path), true);
        return [
            'success'   => $res_json['success'],
            'failed'    => $res_json['failed'],
            'warehouse' => $res_json['warehouse'],
            'period'    => $res_json['period'],
        ];
    }

    public function download_failed($encode_id)
    {
        checkAccess('import');
        $job_id = (int) $this->encrypt->decode(base64url_decode($encode_id));
        $job    = $this->item_balance->get_job($job_id);
        if (!$job) {
            $this->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
            redirect($this->access['url']);
            die();
        }
        $sheets = [];
        $result = $this->_read_result_counts($job);
        $headerTemplate = $this->_headerTemplate();
        $headerTemplate['error_reason'] = 'Pesan Gagal';

        $sheets[] = [
            'title'       => 'Template Import',
            'header'      => $headerTemplate,
            'data'        => $result['failed'] ?? [],
            'header_info' => [
                'h1: TEMPLATE IMPORT '.strtoupper($this->access['PROMPT']),
                'Gudang : '.($result['warehouse']['WAREHOUSE_NAME'] ?? ''),
                'Periode : '.($result['period']['PERIOD_NAME'] ?? ''),
                'Kolom dengan tanda (*) WAJIB DIISI.',
                'Diisi hanya kolom "STOK AWAL" dan "HPP AWAL" saja'
            ]
        ];

        $config = [
            'background'    => '3D7BB9',
            'color'         => 'FFFFFF',
            'freeze_header' => true,
            'auto_filter'   => true,
        ];

        $this->load->library('simpleexcel');
        $this->simpleexcel->write($sheets, 'Template_Import_Item_(Gagal Import)_' . date('Ymd'), $config);
    }

    public function import_cancel()
    {
        checkAccess('import');
        if (!$this->input->is_ajax_request()) redirect('404');
    
        $job_id = (int) $this->encrypt->decode(base64_decode($this->input->post('job_id')));
        if (!$job_id) {
            sendWarning('Data import tidak valid');die;
        }
    
        $job = $this->item_balance->get_job($job_id);
    
        if (!$job) {
            sendWarning('Data import tidak valid');die;
        }
    
        if (!in_array($job['STATUS'], ['running', 'pending', 'queued','done'])) {
            sendWarning('Import sudah selesai atau gagal');die;
        }
    
        //  Kill SP di MySQL via KILL QUERY
        $thread_id = !empty($job['THREAD_ID']) ? (int) $job['THREAD_ID'] : 0;
    
        if ($thread_id > 0) {
            $thread_exists = $this->db->query(
                "SELECT ID FROM information_schema.PROCESSLIST WHERE ID = ?",
                [$thread_id]
            )->row();
    
            if ($thread_exists) {
                $this->db->query("KILL QUERY {$thread_id}");
                usleep(300000);
                $still_alive = $this->db->query(
                    "SELECT ID FROM information_schema.PROCESSLIST WHERE ID = ?",
                    [$thread_id]
                )->row();
    
                if ($still_alive) {
                    $this->db->query("KILL {$thread_id}");
                }
            }
        }
    
        //  Kill PHP process (cli_process + monitor_progress)
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
                    proc_open("taskkill /F /T /PID {$pid}", [], $pipes);
                } else {
                    proc_open("kill -TERM -- -{$pid}", [], $pipes);
                    usleep(200000);
                    proc_open("kill -KILL -- -{$pid}", [], $pipes);
                    proc_open("pkill -KILL -P {$pid}", [], $pipes);
                }
            }
        }
    
        //delete file
        $json               = json_decode($job['JSON_TEXT'], true);
        $upload_dir         = FCPATH . 'assets/upload/item_balance_import/';
        $result_filename    = (isset($json['result_filename'])?$json['result_filename']:'null');
        $result_path        = $upload_dir . $result_filename;
        $exel_file          = $upload_dir.$json['filename'];
        if(file_exists($result_path)){
            @unlink($result_path);
        }
        if(file_exists($exel_file)){
            @unlink($exel_file);
        }

        //  Update status di DB
        $this->item_balance->update_job($job_id, [
            'STATUS'      => 'failed',
            'MESSAGE'     => 'Gudang '.($json['warehouse']['WAREHOUSE_NAME'] ?? '').' periode '.($json['period']['PERIOD_NAME'] ?? '').' Dibatalkan oleh user.',
            'FINISHED_AT' => date('Y-m-d H:i:s'),
            'PROCESS_ID'  => null,
            'THREAD_ID'   => null,
        ]);
    
        sendSuccess('','Import berhasil dibatalkan.');
    }

    public function finalize_import(){
        checkAccess('import');
        if (!$this->input->is_ajax_request()) redirect('404');
    
        $job_id = (int) $this->encrypt->decode(base64_decode($this->input->post('job_id')));
        if (!$job_id) {
            sendWarning('Data import tidak valid');die;
        }
    
        $job    = $this->item_balance->get_job($job_id);
    
        if (!$job) {
            sendWarning('Data import tidak valid');die;
        }
    
        if ($job['STATUS'] != 'done') {
            sendWarning('Import belum selesai atau arsip');die;
        }

        $result = $this->_read_result_counts($job);
        unset($result['failed']);
        if (empty($result['success'])) {
            sendWarning('Tidak ada data success untuk diproses.');die;
        }

        $this->db->trans_begin();
        try {
            $json   = json_decode($job['JSON_TEXT'], true);

            $update_data    = [];
            $user_id        = (int) $this->session->id;
            $now            = date('Y-m-d H:i:s');
            foreach ($result['success'] as $p) {
                $param = $p['update'];
                $param['QTY_AWAL']          = (float) $p['QTY_AWAL'];
                $param['HPP']               = (float) $p['HPP'];
                $param['LAST_UPDATE_BY']    = $user_id;
                $param['LAST_UPDATE_DATE']  = $now;
                $update_data[] = $param;
            }
            if(!empty($update_data)){
                $this->db->update_batch('item_balance', $update_data, 'ITEM_BALANCE_ID');
            }

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction gagal.');
            }

            $json['imported'] = count($update_data);

            //pindahkan result file ke archived
            $upload_dir         = FCPATH . 'assets/upload/item_balance_import/';
            $result_filename    = (isset($json['result_filename'])?$json['result_filename']:'null');
            $result_path        = $upload_dir . $result_filename;
            $result_path_move   = $upload_dir.'archived/'.$result_filename;
            $exel_file          = $upload_dir.$json['filename'];

            if (!is_dir($upload_dir.'archived')) {
                mkdir($upload_dir.'archived', 0755, true);
            }

            if(rename($result_path, $result_path_move)) {}
            if(file_exists($exel_file)){
                @unlink($exel_file);
                unset($json['filename']);
            }

            $this->item_balance->update_job($job_id, [
                'STATUS'   => 'archived',
                'JSON_TEXT' => json_encode($json)
            ]);
            $this->db->trans_commit();
            
            sendSuccess('', numb_format(count($update_data),0)." data berhasil disimpan.");
        } catch (Exception $e) {
            $this->db->trans_rollback();
            sendWarning('Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}