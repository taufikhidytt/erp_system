<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Server_model extends CI_Model
{
    private $db_srv;
    private $column_order = array(
        null,
        "DB_NAME",
        "DB_ALIAS",
        "HOSTNAME",
        "PORT",
        "ACTIVE_FLAG"
    );
    private $column_search = array(
        "DB_NAME",
        "DB_ALIAS",
        "HOSTNAME",
        "PORT",
        "ACTIVE_FLAG"
    );
    private $order = array('SERVER_ID' => 'ASC');

    public function __construct()
    {
        parent::__construct();
        $this->db_srv = $this->load->database('server', TRUE);
    }

    private function _get_datatables_query()
    {
        $this->db_srv->select("
            SERVER_ID,DB_NAME,DB_ALIAS,HOSTNAME,PORT,ACTIVE_FLAG
        ");
        $this->db_srv->from('servers a');

        $global_search_value = $this->input->post('search')['value'] ?? '';
        if ($global_search_value !== '') {
            $this->db_srv->group_start();
            foreach ($this->column_search as $index => $item) {
                $index === 0 ? $this->db_srv->like($item, $global_search_value) : $this->db_srv->or_like($item, $global_search_value);
            }
            $this->db_srv->group_end();
        }

        foreach ($this->column_search as $index => $item) {
            $col_index = $index + 1; 
            $column_search_value = $this->input->post('columns')[$col_index]['search']['value'] ?? '';
            if ($column_search_value !== '') {
                $this->db_srv->like($item, $column_search_value);
            }
        }

        if (isset($_POST['order'])) {
            $this->db_srv->order_by(
                $this->column_order[$_POST['order']['0']['column']],
                $_POST['order']['0']['dir']
            );
        } elseif (isset($this->order)) {
            $order = $this->order;
            $this->db_srv->order_by(key($order), $order[key($order)]);
        }
    }
    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db_srv->limit(
                $_POST['length'],
                $_POST['start']
            );
        $query = $this->db_srv->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db_srv->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db_srv->select("a.SERVER_ID");
        $this->db_srv->from('servers a');
        return $this->db_srv->count_all_results();
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

    public function update_data($table,$data,$where)
    {
        $this->db_srv->where($where);
        $this->db_srv->update($table,$data);
    }
    
    public function get_user_servers($username)
    {
        $this->db_srv->select('s.DB_NAME,s.DB_ALIAS,a.PRIMARY_FLAG');
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
        $this->db_srv->select('s.HOSTNAME, s.PORT, s.DB_NAME, s.DB_ALIAS');
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

    public function get_data($table, $where){
        $this->db_srv->from($table);
        $this->db_srv->where($where);
        return $this->db_srv->get();
    }
}