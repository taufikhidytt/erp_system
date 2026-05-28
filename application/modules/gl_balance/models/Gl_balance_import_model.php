<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gl_balance_import_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        setVariableMysql();
    }

    public function get_job($job_id)
    {
        $this->db->where('IMPORT_HISTORY_ID', (int) $job_id);
        $query = $this->db->get('import_history');
        return $query->row_array() ?: null;
    }

    public function update_job($job_id, array $data)
    {
        $data['LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');

        $this->db->where('IMPORT_HISTORY_ID', (int)$job_id);
        return $this->db->update('import_history', $data);
    }


    public function get_recent_jobs($import_key = null, $limit = 10)
    {
        $this->db->where('CREATED_BY', $this->session->userdata('id_user') ?? 1);

        if ($import_key) {
            $this->db->where('IMPORT_KEY', $import_key);
        }

        $this->db->order_by('IMPORT_HISTORY_ID', 'DESC');
        $this->db->limit((int)$limit);

        return $this->db->get('import_history')->result_array();
    }

    public function get_coa($arr_coa_code, $period)
    {
        if (empty($arr_coa_code)) {
            return [];
        }

        $this->db->select("
            a.COA_BALANCE_ID,a.PERIOD_NAME,a.COA_ID,a.COA_SALDO,
            b.COA_CODE, b.COA_NAME,b.MATA_UANG_ID,
            c.MATA_UANG_CODE,
            COALESCE(c.SALDO_AWAL,1) as KURS
        ");
        $this->db->join('v_account b','a.COA_ID = b.COA_ID');
        $this->db->join('mata_uang c','c.MATA_UANG_ID = b.MATA_UANG_ID');
        $this->db->where('a.PERIOD_NAME', $period);
        $this->db->where('c.ACTIVE_FLAG', 'Y');
        $this->db->where_in('b.COA_CODE', $arr_coa_code);
        $this->db->order_by('b.COA_CODE','asc');
        $query = $this->db->get('coa_balance a');

        if (!$query || $query->num_rows() === 0) {
            return [];
        }

        $result = [];
        foreach ($query->result_array() as $d) {
            $coa_code  = $d['COA_CODE']??'';
            $coa_name  = $d['COA_NAME']??'';
            $kurs      = (float)$d['KURS']??0;
            $mata_uang = $d['MATA_UANG_CODE']??'';
            $result[$this->_clean_key($coa_code.'_'.$coa_name.'_'.$kurs.'_'.$mata_uang)] = (int) $d['COA_BALANCE_ID'];
        }

        return $result;
    }

    private function _clean_key($key)
    {
        $key = trim($key??'');
        $key = strtolower($key);
        return preg_replace('/[^a-z0-9]/', '', $key);
    }
}