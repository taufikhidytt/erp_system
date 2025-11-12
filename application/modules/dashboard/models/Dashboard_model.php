<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    public function getDataInspection($data)
    {
        $group_company = $this->db->query("SELECT id_company FROM tb_users WHERE id = {$this->session->userdata('id')}")->row();
        $this->db->from('tb_inspection');
        if ($data->jenis === 'external') {
            $this->db->where('id_group_company', $group_company->id_company);
        }
        $this->db->where('tb_inspection.deleted_at', null);
        return $this->db->get();
    }
}
