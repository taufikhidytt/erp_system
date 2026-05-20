<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Alamat_model extends CI_Model
{
    protected $table = 'address';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('ADDRESS_ID', $id);
        return $this->db->get();
    }

    public function add($post)
    {
        $params = array(
            'ADDRESS_CODE'    => $post['address_code'] ? htmlspecialchars($post['address_code']) : null,
            'ADDRESS1'        => $post['address1'] ? htmlspecialchars($post['address1']) : null,
            'ADDRESS2'        => $post['address2'] ? htmlspecialchars($post['address2']) : null,
            'CITY'            => $post['city'] ? htmlspecialchars($post['city']) : null,
            'PROVINCE'        => $post['province'] ? htmlspecialchars($post['province']) : null,
            'COUNTRY'         => $post['country'] ? htmlspecialchars($post['country']) : null,
            'PHONE'           => $post['phone'] ? htmlspecialchars($post['phone']) : null,
            'FAX'             => $post['fax'] ? htmlspecialchars($post['fax']) : null,
            'SHIP_FLAG'       => isset($post['ship_flag']) && $post['ship_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'     => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'CREATED_BY'      => $this->session->userdata('id'),
            'CREATED_DATE'    => date('Y-m-d H:i:s'),
        );
        $this->db->insert($this->table, $params);
        return $this->db->insert_id();
    }

    public function update_by_id($id, $post)
    {
        $params = array(
            'ADDRESS1'        => $post['address1'] ? htmlspecialchars($post['address1']) : null,
            'ADDRESS2'        => $post['address2'] ? htmlspecialchars($post['address2']) : null,
            'CITY'            => $post['city'] ? htmlspecialchars($post['city']) : null,
            'PROVINCE'        => $post['province'] ? htmlspecialchars($post['province']) : null,
            'COUNTRY'         => $post['country'] ? htmlspecialchars($post['country']) : null,
            'PHONE'           => $post['phone'] ? htmlspecialchars($post['phone']) : null,
            'FAX'             => $post['fax'] ? htmlspecialchars($post['fax']) : null,
            'SHIP_FLAG'       => isset($post['ship_flag']) && $post['ship_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'     => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'LAST_UPDATE_BY'  => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'=> date('Y-m-d H:i:s'),
        );
        
        $this->db->where('ADDRESS_ID', $id);
        $this->db->update($this->table, $params);
    }
}
