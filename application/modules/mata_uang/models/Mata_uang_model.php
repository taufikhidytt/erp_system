<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mata_uang_model extends CI_Model
{
    protected $table = 'mata_uang';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->from($this->table);
        $this->db->where('MATA_UANG_ID', $id);
        return $this->db->get();
    }

    public function add($post)
    {
        $params = array(
            'MATA_UANG_CODE' => $post['mata_uang_code'] ? htmlspecialchars(strtoupper($post['mata_uang_code'])) : null,
            'MATA_UANG_NAME' => $post['mata_uang_name'] ? htmlspecialchars($post['mata_uang_name']) : null,
            'SALDO_AWAL'     => $post['saldo_awal'] !== '' ? (float) $post['saldo_awal'] : 0,
            'STATE'          => $post['state'] ? htmlspecialchars($post['state']) : null,
            'SYMBOL'         => $post['symbol'] ? htmlspecialchars($post['symbol']) : null,
            'PRIMARY_FLAG'   => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'    => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'CREATED_BY'     => $this->session->userdata('id'),
            'CREATED_DATE'   => date('Y-m-d H:i:s'),
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
            'MATA_UANG_CODE' => $post['mata_uang_code'] ? htmlspecialchars(strtoupper($post['mata_uang_code'])) : null,
            'MATA_UANG_NAME' => $post['mata_uang_name'] ? htmlspecialchars($post['mata_uang_name']) : null,
            'SALDO_AWAL'     => $post['saldo_awal'] !== '' ? (float) $post['saldo_awal'] : 0,
            'STATE'          => $post['state'] ? htmlspecialchars($post['state']) : null,
            'SYMBOL'         => $post['symbol'] ? htmlspecialchars($post['symbol']) : null,
            'PRIMARY_FLAG'   => isset($post['primary_flag']) && $post['primary_flag'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG'    => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'LAST_UPDATE_BY' => $this->session->userdata('id'),
            'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
        );

        if($params['PRIMARY_FLAG'] == 'Y'){
            $this->db->where('MATA_UANG_ID !=',$id);
            $this->db->where('PRIMARY_FLAG','Y');
            $this->db->update($this->table, ['PRIMARY_FLAG' => 'N']);
        }

        $this->db->where('MATA_UANG_ID', $id);
        $this->db->update($this->table, $params);
    }

    public function get_mata_uang(){
        $this->db->select('MATA_UANG_CODE');
        return $this->db->get($this->table);
    }

    public function insert_batch($params)
    {
        if(!empty($params)){
            $this->db->insert_batch($this->table, $params);
        }
    }

    public function update_batch($params){
        if(!empty($params)){
            $this->db->update_batch($this->table, $params,'MATA_UANG_CODE');
        }
    }
}
