<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gl_balance_model extends CI_Model
{
    protected $table = 'coa_balance';

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

    public function get_by_id($ids, $period){
        $this->db->select('COA_BALANCE_ID');
        $this->db->where_in('COA_BALANCE_ID', empty($ids)?[0]:$ids);
        $this->db->where('PERIOD_NAME', $period);
        return $this->db->get($this->table);
    }

    public function update_batch($params)
    {
        if(!empty($params)){
            $this->db->update_batch($this->table, $params, 'COA_BALANCE_ID');
        }
    }

    public function get_active_job()
    {
        $this->db->where('IMPORT_KEY','coa_balance');
        $this->db->where('CREATED_BY', (int) $this->session->id);
        $this->db->where_in('STATUS', ['pending', 'queued', 'running']);
        $query = $this->db->get('import_history');
        return $query->row_array();
    }

    public function get_coa_balance($period){
        $this->db->select("
            a.COA_BALANCE_ID,a.PERIOD_NAME,a.COA_ID,a.COA_SALDO,
            b.COA_CODE, b.COA_NAME,b.MATA_UANG_ID,
            c.MATA_UANG_CODE,
            COALESCE(c.SALDO_AWAL,1) as KURS,
            (COALESCE(c.SALDO_AWAL, 1) * a.COA_SALDO)  AS SALDO_AWAL_KURS
        ");
        $this->db->join('v_account b','a.COA_ID = b.COA_ID');
        $this->db->join('mata_uang c','c.MATA_UANG_ID = b.MATA_UANG_ID');
        $this->db->where('a.PERIOD_NAME', $period);
        $this->db->where('c.ACTIVE_FLAG', 'Y');
        $this->db->order_by('b.COA_CODE','asc');
        return $this->db->get($this->table.' as a');
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