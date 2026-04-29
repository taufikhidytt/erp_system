<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Server_model extends CI_Model
{
    private $db_srv;
    public function __construct()
    {
        parent::__construct();
        $this->db_srv = $this->load->database('server', TRUE);
    }

    public function db_srv()
    {
        return $this->db_srv;
    }

    public function get_servers()
    {
        $this->db_srv->where('ACTIVE_FLAG', 'Y');
        return $this->db_srv->get('servers')->result();
    }
    
    public function get_user($username)
    {
        $this->db_srv->select('USER_ID,USER_NAME');
        $this->db_srv->where('USER_NAME', $username);
        return $this->db_srv->get('users');
    }

    public function insert_data($table,$data)
    {
        $this->db_srv->insert($table, $data);
        return $this->db_srv->insert_id();
    }
    
    public function get_user_servers($username)
    {
        $this->db_srv->select('s.DB_NAME,a.PRIMARY_FLAG');
        $this->db_srv->from('user_servers a');
        $this->db_srv->join('servers s', 'a.SERVER_ID = s.SERVER_ID');
        $this->db_srv->join('users u', 'a.USER_ID = u.USER_ID');
        $this->db_srv->where('u.USER_NAME', strtoupper(htmlspecialchars($username ?? '')));
        $this->db_srv->where('a.ACTIVE_FLAG', 'Y');
        $this->db_srv->where('s.ACTIVE_FLAG', 'Y');
        $this->db_srv->order_by('a.PRIMARY_FLAG DESC');
        return $this->db_srv->get();
    }

    public function getDataServer($username, $dbName)
    {
        $this->db_srv->select('s.HOSTNAME, s.PORT, s.DB_NAME');
        $this->db_srv->from('user_servers a');
        $this->db_srv->join('servers s', 'a.SERVER_ID = s.SERVER_ID');
        $this->db_srv->join('users u', 'a.USER_ID = u.USER_ID');
        $this->db_srv->where('LOWER(s.DB_NAME)', strtolower(htmlspecialchars($dbName ?? '')));
        $this->db_srv->where('u.USER_NAME', strtoupper(htmlspecialchars($username ?? '')));
        $this->db_srv->where('a.ACTIVE_FLAG', 'Y');
        $this->db_srv->where('s.ACTIVE_FLAG', 'Y');
        return $this->db_srv->get();
    }

    public function getServerWithUser($username)
    {
        $this->db_srv->select('
            s.SERVER_ID,s.DB_NAME, s.HOSTNAME, s.PORT,
            us.PRIMARY_FLAG, us.ACTIVE_FLAG, us.USER_SERVER_ID,
            u.USER_NAME,
        ');
        $this->db_srv->from('servers s');
        $this->db_srv->join('users u', "u.USER_NAME = '" . strtoupper(htmlspecialchars($username)) . "'", 'left');
        $this->db_srv->join('user_servers us', 'us.SERVER_ID = s.SERVER_ID AND us.USER_ID = u.USER_ID', 'left');
        $this->db_srv->where('s.ACTIVE_FLAG', 'Y');
        return $this->db_srv->get();
    }

    public function insert_batch($table, $datas){
        if(!empty($datas)){
            $this->db_srv->insert_batch($table,$datas);
        }
    }
    public function update_batch($table, $datas, $column){
        if(!empty($datas)){
            $this->db_srv->update_batch($table,$datas,$column);
        }
    }
}