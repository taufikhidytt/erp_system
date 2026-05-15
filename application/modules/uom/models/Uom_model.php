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
        $this->db->where('UOM_CODE', $id);
        return $this->db->get();
    }

    public function add($post)
    {
        $params = array(
            'UOM_CODE'        => $post['uom_code'] ? htmlspecialchars($post['uom_code']) : null,
            'DESCRIPTION'     => $post['description'] ? htmlspecialchars($post['description']) : null,
            'BASE_UOM_FLAG'   => isset($post['base_uom_flag']) && $post['base_uom_flag'] == 'Y' ? 'Y' : 'N',
            'PRIMARY_FLAG'    => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'     => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'CREATED_BY'      => $this->session->userdata('id'),
            'CREATED_DATE'    => date('Y-m-d H:i:s'),
        );
        $this->db->insert($this->table, $params);
        return $post['uom_code'];
    }

    public function update_by_id($id, $post)
    {
        $params = array(
            'DESCRIPTION'     => $post['description'] ? htmlspecialchars($post['description']) : null,
            'BASE_UOM_FLAG'   => isset($post['base_uom_flag']) && $post['base_uom_flag'] == 'Y' ? 'Y' : 'N',
            'PRIMARY_FLAG'    => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'     => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'LAST_UPDATE_BY'  => $this->session->userdata('id'),
            'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
        );

        $this->db->where('UOM_CODE', $id);
        $this->db->update($this->table, $params);
    }
}
