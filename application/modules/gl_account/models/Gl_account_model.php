<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Gl_account_model extends CI_Model
{
    protected $table = 'account';

    public function __construct()
    {
        setVariableMysql();
    }

    public function get_type()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME as text, b.PROGRAM_CODE1")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'TIPE_ACCOUNT')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function get_mata_uang()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("MATA_UANG_ID as id, MATA_UANG_CODE as text")
            ->from('mata_uang a')
            ->order_by('PRIMARY_FLAG desc, MATA_UANG_CODE asc');

        if ($id) {
            $this->db->where('MATA_UANG_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('ACTIVE_FLAG', 'Y');
            $this->db->where('PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('MATA_UANG_CODE', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function get_mata_uang_default(){
        return $this->db
            ->select("MATA_UANG_ID as id, MATA_UANG_CODE as text, MATA_UANG_CODE as label")
            ->from('mata_uang a')
            ->where('ACTIVE_FLAG', 'Y')
            ->where('PRIMARY_FLAG', 'Y')
            ->order_by('PRIMARY_FLAG desc, MATA_UANG_CODE asc')
            ->limit(1)
            ->get();
    }

    public function get_by_id($id){
        $this->db->select("ACCOUNT_ID,ACCOUNT_CODE,ACCOUNT_NAME,PARENT_FLAG,ACCOUNT_TYPE_ID,ACTIVE_FLAG, MATA_UANG_ID, KATA");
        if(is_array($id)){
            $this->db->where_in("ACCOUNT_ID", $id);
        }else{
            $this->db->where("ACCOUNT_ID", $id);
            $this->db->limit(1);
        }
        return $this->db->get($this->table);
    }

    public function get_duplicate_codes($codes)
    {
        return $this->db->select('ACCOUNT_CODE')
                        ->where_in('ACCOUNT_CODE', $codes)
                        ->get($this->table)
                        ->result_array();
    }

    public function insert_batch($params)
    {
        if(!empty($params)){
            $this->db->insert_batch($this->table, $params);
        }
    }

    public function update_batch($params)
    {
        if(!empty($params)){
            $this->db->update_batch($this->table, $params, 'ACCOUNT_ID');
        }
    }

    public function update($id,$param){
        $this->db->where('ACCOUNT_ID', $id);
        $this->db->update($this->table, $param);
    }
}