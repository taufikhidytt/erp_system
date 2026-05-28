<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item_balance_import_model extends CI_Model
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

    public function get_item($arr_item_code, $warehouse_id, $period)
    {
        if (empty($arr_item_code)) {
            return [];
        }

        $query = $this->db
            ->select('a.ITEM_BALANCE_ID,i.ITEM_CODE,i.UOM_CODE,
                COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) AS ITEM_NAME')
            ->join('item i','a.ITEM_ID = i.ITEM_ID')
            ->where('a.PERIOD_NAME', $period)
            ->where('a.WAREHOUSE_ID', $warehouse_id)
            ->where('i.ITEM_ID !=', 1010)
            ->where('i.JENIS_ID = FN_GET_VAR_VALUE("GOODS")', null, false)
            ->where_in('i.ITEM_CODE', $arr_item_code)
            ->order_by('i.ITEM_CODE asc')
            ->get('item_balance a');

        if (!$query || $query->num_rows() === 0) {
            return [];
        }

        $result = [];
        foreach ($query->result_array() as $d) {
            $item_code  = $d['ITEM_CODE']??'';
            $item_name  = $d['ITEM_NAME']??'';
            $uom        = $d['UOM_CODE']??'';
            $result[$this->_clean_key($item_code.'_'.$item_name.'_'.$uom)] = (int) $d['ITEM_BALANCE_ID'];
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