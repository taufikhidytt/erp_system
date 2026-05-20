<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Job extends MX_Controller {

	public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        date_default_timezone_set('Asia/Jakarta');
    }
    
    public function cli_process(){
        $server = $this->load->database('server', TRUE);
        $server->select('DB_NAME,HOSTNAME,PORT');
        $server->where('ACTIVE_FLAG', 'Y');
        $server->from('servers');
        $db_list = $server->get()->result_array();
        $server->close();

        $bi_data      = get_bi_data();
        if (empty($bi_data) || empty($bi_data['exchange_rates'])) {
            log_message('error', '[cron job] Gagal mendapatkan data dari BI. Proses sinkronisasi dibatalkan.');
            return;
        }
        $bi_rates     = $bi_data['exchange_rates'];
        $current_date = date('Y-m-d');
        $bi_date      = $bi_data['last_update'];
        $now          = date('Y-m-d H:i:s');
        $period_name  = date('Ym'); 

        //matikan error yang menyebabkan aplikasi berhenti
        $error_level = error_reporting();
        error_reporting(0);

        $db_config = [
            'username' => $this->db->username,
            'password' => $this->db->password,
        ];
        foreach ($db_list as $item) {
            $db_config['hostname'] = $item['HOSTNAME'];
            $db_config['database'] = $item['DB_NAME'];
            $db_config['port']     = $item['PORT'];
            $db_config['dbdriver'] = 'mysqli';
            $db_config['db_debug'] = FALSE;

            $dynamic_db  = $this->load->database($db_config, TRUE);
            if (empty($dynamic_db->conn_id)) {
                log_message('error', '[cron job] Gagal koneksi ke database pada: ' . $item['DB_NAME'] . ' di ' . $item['HOSTNAME']);
                continue; 
            }

            try {
                $mata_uang  = $this->mata_uang($dynamic_db);
                $arr_insert = [];
                $arr_update = [];

                foreach ($mata_uang as $mu) {
                    $code = strtoupper($mu->MATA_UANG_CODE);
                    if ($code === 'IDR') {
                        $rate   = 1.0;
                    } elseif (isset($bi_rates[$code])) {
                        $r      = $bi_rates[$code];
                        $rate   = (float) $r['middle_rate'];
                    } else {
                        continue;
                    }

                    $is_today = $mu->last_date === $current_date;

                    if($is_today && $mu->KURS_DETAIL_ID){
                        $arr_update[] = [
                            'KURS_DETAIL_ID' => (int) $mu->KURS_DETAIL_ID,
                            'NILAI'          => $rate,
                            'NOTE'           => 'Auto-generated from bi dated '.$bi_date,
                            'LAST_UPDATE_BY' => 1,
                            'LAST_UPDATE_DATE' => $now,
                        ];
                    }else{
                        $arr_insert[] = [
                            'NILAI'          => $rate,
                            'NOTE'           => 'Auto-generated from bi dated '.$bi_date,
                            'DOCUMENT_DATE'  => $current_date,
                            'MATA_UANG_ID'   => $mu->MATA_UANG_ID,
                            'PERIOD_NAME'    => $period_name,
                            'CREATED_BY'     => 1,
                            'CREATED_DATE'   => $now,
                        ];
                    }
                }

                $dynamic_db->trans_start();

                if(!empty($arr_insert)){
                    $dynamic_db->insert_batch('kurs_detail', $arr_insert);
                }
                if(!empty($arr_update)){
                    $dynamic_db->update_batch('kurs_detail', $arr_update,'KURS_DETAIL_ID');
                }

                $dynamic_db->trans_complete();

                if ($dynamic_db->trans_status() === FALSE) {
                    $db_error = $dynamic_db->error();
                    log_message('error', '[cron job] Gagal koneksi ke database pada: ' . $item['DB_NAME'] . ' di ' . $item['HOSTNAME'].' -- '.$db_error['message']);
                }

                $dynamic_db->close();
            } catch (\Throwable $e) {
                log_message('error', '[cron job] Crash Terisolasi di DB ' . $item['DB_NAME'] . ' - Error: ' . $e->getMessage());
                if (isset($dynamic_db) && !empty($dynamic_db->conn_id)) {
                    $dynamic_db->close();
                }
                continue;
            }
        }

        error_reporting($error_level);
    }

    private function mata_uang($dynamic_db)
    {
        $dynamic_db->select('mu.MATA_UANG_ID, mu.MATA_UANG_CODE, mu.MATA_UANG_NAME, mu.STATE, kd.KURS_DETAIL_ID, kd.NILAI AS last_rate, kd.DOCUMENT_DATE AS last_date')
            ->from('mata_uang mu')
            ->join('kurs_detail kd', 'kd.KURS_DETAIL_ID = (SELECT KURS_DETAIL_ID FROM kurs_detail WHERE MATA_UANG_ID = mu.MATA_UANG_ID ORDER BY DOCUMENT_DATE DESC LIMIT 1)', 'left')
            ->where('mu.ACTIVE_FLAG', 'Y');

        return $dynamic_db->get()->result();
    }
}