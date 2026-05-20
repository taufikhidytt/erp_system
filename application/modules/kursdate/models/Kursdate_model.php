<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kursdate_model extends CI_Model
{
    protected $table = 'kurs_detail';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->select('a.*, b.MATA_UANG_CODE, b.MATA_UANG_NAME, c.ERP_USER_NAME');
        $this->db->from($this->table . ' a');
        $this->db->join('mata_uang b', 'a.MATA_UANG_ID = b.MATA_UANG_ID', 'left');
        $this->db->join('erp_user c', 'a.CREATED_BY = c.ERP_USER_ID', 'left');
        $this->db->where('a.KURS_DETAIL_ID', $id);
        return $this->db->get();
    }

    public function update_by_id($id, $post)
    {
        $params = array(
            'NILAI'          => $post['nilai'] !== '' ? (float) $post['nilai'] : 0,
            'NOTE'           => $post['note'] ? htmlspecialchars($post['note']) : null,
            'LAST_UPDATE_BY' => $this->session->userdata('id'),
            'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
        );

        $this->db->where('KURS_DETAIL_ID', $id);
        $this->db->update($this->table, $params);
    }

    public function mata_uang($ids=[])
    {
        $this->db
            ->select('mu.MATA_UANG_ID, mu.MATA_UANG_CODE, mu.MATA_UANG_NAME, mu.STATE, kd.KURS_DETAIL_ID, kd.NILAI AS last_rate, kd.DOCUMENT_DATE AS last_date')
            ->from('mata_uang mu')
            ->join('kurs_detail kd', 'kd.KURS_DETAIL_ID = (SELECT KURS_DETAIL_ID FROM kurs_detail WHERE MATA_UANG_ID = mu.MATA_UANG_ID ORDER BY DOCUMENT_DATE DESC LIMIT 1)', 'left')
            ->where('mu.ACTIVE_FLAG', 'Y');
        if(!empty($ids)){
            $this->db->where_in('mu.MATA_UANG_ID', $ids);
        }

        return $this->db->get();
    }

    public function insert_batch($params)
    {
        if(!empty($params)){
            $this->db->insert_batch($this->table, $params);
        }
    }

    public function update_batch($params){
        if(!empty($params)){
            $this->db->update_batch($this->table, $params,'KURS_DETAIL_ID');
        }
    }
}
