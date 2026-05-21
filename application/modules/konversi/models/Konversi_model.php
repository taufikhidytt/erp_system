<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Konversi_model extends CI_Model
{
    protected $table = 'item_convertion';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->from($this->table);
        if(is_array($id)){
            $this->db->where_in('ITEM_CONVERTION_ID', $id);
        }else{
            $this->db->where('ITEM_CONVERTION_ID', $id);
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
            $this->db->update_batch($this->table, $params, 'ITEM_CONVERTION_ID');
        }
    }

    public function check_duplicate($combinations, $exclude_ids = [])
    {
        if (empty($combinations)) return [];

        $this->db->select('FROM_UOM, TO_UOM');
        $this->db->from($this->table);

        $this->db->group_start();
        foreach ($combinations as $i => $combo) {
            $method = $i === 0 ? 'group_start' : 'or_group_start';
            $this->db->$method();
            $this->db->where('FROM_UOM', $combo['from']);
            $this->db->where('TO_UOM',   $combo['to']);
            $this->db->group_end();
        }
        $this->db->group_end();

        if (!empty($exclude_ids)) {
            $this->db->where_not_in('ITEM_CONVERTION_ID', $exclude_ids);
        }

        return $this->db->get()->result_array();
    }
}
