<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jangka_model extends CI_Model
{
    protected $table = 'payment_term';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->from($this->table);
        $this->db->where('PAYMENT_TERM_ID', $id);
        return $this->db->get();
    }

    public function add($post)
    {
        $params = array(
            'PAYMENT_TERM_NAME' => $post['payment_term_name'] ? htmlspecialchars($post['payment_term_name']) : null,
            'DESCRIPTION'       => $post['description'] ? htmlspecialchars($post['description']) : null,
            'NUMBER_DAYS'       => $post['number_days'] !== '' ? (int)$post['number_days'] : 0,
            'PRIMARY_FLAG'      => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'       => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'CREATED_BY'        => $this->session->userdata('id'),
            'CREATED_DATE'      => date('Y-m-d H:i:s'),
        );
        if($params['PRIMARY_FLAG'] == 'Y'){
            $this->db->where('PRIMARY_FLAG', 'Y');
            $this->db->update($this->table, ['PRIMARY_FLAG' => 'N']);
        }
        $this->db->insert($this->table, $params);
        return $this->db->insert_id();
    }

    public function update_by_id($id, $post)
    {
        $params = array(
            'DESCRIPTION'       => $post['description'] ? htmlspecialchars($post['description']) : null,
            'NUMBER_DAYS'       => $post['number_days'] !== '' ? (int)$post['number_days'] : 0,
            'PRIMARY_FLAG'      => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'       => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
        );
        if($params['PRIMARY_FLAG'] == 'Y'){
            $this->db->where('PAYMENT_TERM_ID !=', $id);
            $this->db->where('PRIMARY_FLAG', 'Y');
            $this->db->update($this->table, ['PRIMARY_FLAG' => 'N']);
        }
        $this->db->where('PAYMENT_TERM_ID', $id);
        $this->db->update($this->table, $params);
    }
}
