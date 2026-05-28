<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item_balance_model extends CI_Model
{
    protected $table = 'item_balance';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_period()
    {
        return $this->db->select('PERIOD_NAME,OPEN_FLAG')
            ->order_by('PERIOD_NAME','ASC')
            ->limit(1)
            ->get('period');
    }

    public function get_warehouse()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');
        $user_id    = (int) $this->session->id;
        $default_field = 'a.PRIMARY_FLAG';

        $user_has_warehouse = $this->db->where('ERP_USER_ID', $user_id)
                    ->limit(1)
                    ->count_all_results('erp_warehouse') > 0;

        $this->db
            ->select("a.WAREHOUSE_ID as id, a.WAREHOUSE_NAME as text ")
            ->from('warehouse a')
            ->group_by('a.WAREHOUSE_ID');
            
        if($user_has_warehouse){
            $default_field = 'IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)';
            $this->db->join('erp_warehouse g', 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'left')
                ->where('g.ERP_USER_ID', $user_id)
                ->order_by($default_field, 'DESC', false);
        }
        $this->db->order_by('a.WAREHOUSE_NAME', 'ASC');

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->where($default_field, 'Y')->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.WAREHOUSE_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function get_by_id($ids, $warehouse_id, $period){
        $this->db->select('ITEM_BALANCE_ID');
        $this->db->where_in('ITEM_BALANCE_ID', empty($ids)?[0]:$ids);
        $this->db->where('PERIOD_NAME', $period);
        $this->db->where('WAREHOUSE_ID', $warehouse_id);
        return $this->db->get($this->table);
    }

    public function update_batch($params)
    {
        if(!empty($params)){
            $this->db->update_batch($this->table, $params, 'ITEM_BALANCE_ID');
        }
    }

    public function get_active_job()
    {
        $this->db->where('IMPORT_KEY','item_balance');
        $this->db->where('CREATED_BY', (int) $this->session->id);
        $this->db->where_in('STATUS', ['pending', 'queued', 'running']);
        $query = $this->db->get('import_history');
        return $query->row_array();
    }

    public function get_warehouse_by_id($id){
        $this->db->select('WAREHOUSE_NAME,WAREHOUSE_ID');
        $this->db->where('WAREHOUSE_ID', $id);
        $this->db->limit(1);
        return $this->db->get('warehouse');
    }

    public function get_item_balance($warehouse_id,$period){
        return $this->db
            ->select('a.ITEM_BALANCE_ID,a.PERIOD_NAME,a.ITEM_ID,
                i.ITEM_CODE,i.UOM_CODE,a.HPP,
                COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) AS ITEM_NAME,
                (CASE WHEN a.QTY_AWAL != 0 THEN a.QTY_AWAL WHEN a.QTY_MASUK != 0 THEN a.QTY_MASUK ELSE a.QTY_KELUAR * - 1 END) AS QTY_AWAL,
                (CASE WHEN a.QTY_AWAL != 0 THEN a.QTY_AWAL * a.HPP WHEN a.QTY_MASUK != 0 THEN a.QTY_MASUK * a.HPP ELSE (a.QTY_KELUAR * - 1) * a.HPP END) AS SUBTOTAL')
            ->join('item i','a.ITEM_ID = i.ITEM_ID')
            ->where('a.PERIOD_NAME', $period)
            ->where('a.WAREHOUSE_ID', $warehouse_id)
            ->where('i.ITEM_ID !=', 1010)
            ->where('i.JENIS_ID = FN_GET_VAR_VALUE("GOODS")', null, false)
            ->order_by('i.ITEM_CODE asc')
            ->get($this->table.' a');
    }

    public function create_job($data)
    {
        $data['SESSION_ID'] = session_id();
        $data['STATUS']     = 'queued';
        $data['PROGRESS']   = 0;
        $data['MESSAGE']    = 'Menunggu proses dimulai...';
        $data['CREATED_DATE'] = date('Y-m-d H:i:s');
        $data['CREATED_BY'] = $this->session->userdata('id');
        $this->db->insert('import_history', $data);
        return (int)$this->db->insert_id();
    }

    public function get_job($job_id)
    {
        $this->db->where('IMPORT_HISTORY_ID', (int) $job_id);
        $query = $this->db->get('import_history');
        return $query->row_array() ?: null;
    }

    public function update_job($job_id, array $data)
    {
        $data['LAST_UPDATE_DATE']   = date('Y-m-d H:i:s');
        $data['LAST_UPDATE_BY']     = $this->session->id;

        $this->db->where('IMPORT_HISTORY_ID', (int)$job_id);
        return $this->db->update('import_history', $data);
    }
}