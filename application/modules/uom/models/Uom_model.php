<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Uom_model extends CI_Model
{
    protected $table = 'uom';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->from($this->table);
        if(is_array($id)){
            $this->db->where_in('UOM_CODE', $id);
        }else{
            $this->db->where('UOM_CODE', $id);
        }
        return $this->db->get();
    }

    public function add($post)
    {
        $uom_code = $post['uom_code'] ? strtoupper(htmlspecialchars($post['uom_code'])) : null;
        $params = array(
            'UOM_CODE'        => $uom_code,
            'DESCRIPTION'     => $post['description'] ? htmlspecialchars($post['description']) : null,
            'PRIMARY_FLAG'    => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'     => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'CREATED_BY'      => $this->session->userdata('id'),
            'CREATED_DATE'    => date('Y-m-d H:i:s'),
        );

        if($params['PRIMARY_FLAG'] == 'Y'){
            $this->db->where('PRIMARY_FLAG','Y');
            $this->db->update($this->table, ['PRIMARY_FLAG' => 'N']);
        }

        $this->db->insert($this->table, $params);
        return $uom_code;
    }

    public function update_by_id($id, $post)
    {
        $params = array(
            'DESCRIPTION'     => $post['description'] ? htmlspecialchars($post['description']) : null,
            'PRIMARY_FLAG'    => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'     => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'LAST_UPDATE_BY'  => $this->session->userdata('id'),
            'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
        );

        if($params['PRIMARY_FLAG'] == 'Y'){
            $this->db->where('UOM_CODE !=',$id);
            $this->db->where('PRIMARY_FLAG','Y');
            $this->db->update($this->table, ['PRIMARY_FLAG' => 'N']);
        }

        $this->db->where('UOM_CODE', $id);
        $this->db->update($this->table, $params);
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
            $this->db->update_batch($this->table, $params, 'UOM_CODE');
        }
    }

    public function get_duplicate_codes($codes)
    {
        return $this->db->select('UOM_CODE')
                        ->where_in('UOM_CODE', $codes)
                        ->get($this->table)
                        ->result_array();
    }
}
