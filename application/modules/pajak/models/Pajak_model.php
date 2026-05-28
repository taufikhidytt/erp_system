<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pajak_model extends CI_Model
{
    protected $table = 'ppn';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->from($this->table);
        if(is_array($id)){
            $this->db->where_in('PPN_CODE', $id);
        }else{
            $this->db->where('PPN_CODE', $id);
        }
        return $this->db->get();
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
            $this->db->update_batch($this->table, $params, 'PPN_CODE');
        }
    }

    public function check_duplicate($combinations, $exclude_ids = [])
    {
        if (empty($combinations)) return [];

        $this->db->select('PPN_CODE');
        $this->db->from($this->table);
        $this->db->where_in('PPN_CODE', $combinations);
        if (!empty($exclude_ids)) {
            $this->db->where_not_in('PPN_CODE', $exclude_ids);
        }

        return $this->db->get()->result_array();
    }
}