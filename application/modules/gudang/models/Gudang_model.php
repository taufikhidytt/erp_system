<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang_model extends CI_Model
{
    protected $table = 'warehouse';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->from($this->table);
        $this->db->where('WAREHOUSE_ID', $id);
        return $this->db->get();
    }

    public function add($post)
    {
        $params = array(
            'WAREHOUSE_NAME'  => $post['warehouse_name'] ? strtoupper(htmlspecialchars($post['warehouse_name'])) : null,
            'DESCRIPTION'     => $post['description'] ? strtoupper(htmlspecialchars($post['description'])) : null,
            'ADDRESS_ID'      => !empty($post['address_id']) ? (int) htmlspecialchars($post['address_id']) : 1,
            'JENIS_ID'        => !empty($post['jenis_id']) ? (int) htmlspecialchars($post['jenis_id']) : null,
            'SALES_FLAG'      => isset($post['sales_flag']) && $post['sales_flag'] == 'Y' ? 'Y' : 'N',
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
        return $this->db->insert_id();
    }

    public function update_by_id($id, $post)
    {
        $params = array(
            'DESCRIPTION'     => $post['description'] ? strtoupper(htmlspecialchars($post['description'])) : null,
            'ADDRESS_ID'      => !empty($post['address_id']) ? (int) htmlspecialchars($post['address_id']) : 1,
            'JENIS_ID'        => !empty($post['jenis_id']) ? (int) htmlspecialchars($post['jenis_id']) : null,
            'SALES_FLAG'      => isset($post['sales_flag']) && $post['sales_flag'] == 'Y' ? 'Y' : 'N',
            'PRIMARY_FLAG'    => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'     => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'LAST_UPDATE_BY'  => $this->session->userdata('id'),
            'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
        );

        if($params['PRIMARY_FLAG'] == 'Y'){
            $this->db->where('WAREHOUSE_ID !=',$id);
            $this->db->where('PRIMARY_FLAG','Y');
            $this->db->update($this->table, ['PRIMARY_FLAG' => 'N']);
        }

        $this->db->where('WAREHOUSE_ID', $id);
        $this->db->update($this->table, $params);
    }

    public function getAddress()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("a.ADDRESS_ID as id, a.ADDRESS_CODE as text, a.ADDRESS1,a.ADDRESS2,a.CITY,a.PROVINCE,a.COUNTRY")
            ->from('address a');

        if ($id) {
            $this->db->where('a.ADDRESS_ID', $id)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.ADDRESS_CODE', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getJenis()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("a.ERP_LOOKUP_VALUE_ID as id, a.DISPLAY_NAME as text")
            ->from('erp_lookup_value a')
            ->where("a.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET('JENIS_GUDANG')", null, false);

        if ($id) {
            $this->db->where('a.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } else {
            $this->db->group_start()
                ->where('a.END_DATE', 0)
                ->or_where('a.END_DATE IS NULL', null, false)
                ->or_where('a.END_DATE >= CURDATE()', null, false)
            ->group_end()
            ->where('a.ACTIVE_FLAG', 'Y')
            ->limit(50);
            
            if ($default) {
                $this->db->where('a.PRIMARY_FLAG', 'Y')->limit(1);
            }else if ($searchTerm) {
                $this->db->group_start()
                    ->like('p.ADDRESS_CODE', $searchTerm)
                    ->group_end();
            }
        }

        return $this->db->get();
    }
}
