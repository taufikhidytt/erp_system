<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile_model extends CI_Model
{
    public function getData()
    {
        $id_users = $this->session->userdata('id');
        $this->db->select('tb_users.*, tb_division.nama as nama_divisi, tb_company.nama as nama_company, tb_position.nama as nama_position');
        $this->db->from('tb_users');
        $this->db->join('tb_division', 'tb_division.id = tb_users.id_division');
        $this->db->join('tb_company', 'tb_company.id = tb_users.id_company');
        $this->db->join('tb_position', 'tb_position.id = tb_users.id_position');
        $this->db->where('tb_users.id', $id_users);
        $this->db->where('tb_users.deleted_at', null);
        return $this->db->get();
    }

    public function update($post)
    {
        date_default_timezone_set("Asia/Jakarta");
        $params = array(
            'nama' => htmlspecialchars($post['nama']),
            'email' => htmlspecialchars(strtolower($post['email'])),
            'no_hp' => htmlspecialchars($post['no_hp']),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if (!empty($post['status_verified_email'])) {
            $params['status_verified_email'] = $post['status_verified_email'];
        }

        if (!empty($post['password'])) {
            $params['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
        }

        if ($post['photo'] != null) {
            $params['photo'] = $post['photo'];
        }

        $this->db->where('id', $post['id']);
        $this->db->update('tb_users', $params);
    }
}
