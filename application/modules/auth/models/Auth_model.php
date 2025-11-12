<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends CI_Model
{
    public function getDataServer($username)
    {
        $this->db->from('user_db');
        $this->db->where('user_name', $username);
        return $this->db->get();
    }

    public function getData($username)
    {
        $this->db->from('erp_user');
        $this->db->where('erp_user_name', $username);
        return $this->db->get();
    }

    public function insertLogSignin($post)
    {
        date_default_timezone_set('Asia/Jakarta');
        $params = [
            'id_users' => $post->id,
            'ip' => $this->input->ip_address(),
            'os' => $this->agent->platform(),
            'browser' => $this->agent->browser() . '-' . $this->agent->version(),
            'log_date' => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('tb_log_sign_in', $params);
    }

    public function updateLogSignin($post)
    {
        date_default_timezone_set('Asia/Jakarta');
        $params = [
            'id_users' => $post->id,
            'ip' => $this->input->ip_address(),
            'os' => $this->agent->platform(),
            'browser' => $this->agent->browser() . '-' . $this->agent->version(),
            'log_date' => date('Y-m-d H:i:s'),
        ];
        $this->db->where('id_users', $post->id);
        $this->db->update('tb_log_sign_in', $params);
    }
}
