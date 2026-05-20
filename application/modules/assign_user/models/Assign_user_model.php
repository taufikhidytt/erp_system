<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Assign_user_model extends CI_Model
{
    private $db_srv;
    private $column_order = array(
        null,
        "USER_NAME",
        "CREATED_DATE",
        "LAST_UPDATE_DATE"
    );
    private $column_search = array(
        "USER_NAME",
        "CREATED_DATE",
        "LAST_UPDATE_DATE"
    );
    private $order = array('USER_ID' => 'ASC');

    public function __construct()
    {
        parent::__construct();
        $this->db_srv = $this->load->database('server', TRUE);
    }

    private function _get_datatables_query()
    {
        $this->db_srv->select("
            USER_ID,USER_NAME,CREATED_DATE,LAST_UPDATE_DATE
        ");
        $this->db_srv->from('users a');

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
        $this->db_srv->select("a.USER_ID");
        $this->db_srv->from('users a');
        return $this->db_srv->count_all_results();
    }

    public function db_srv()
    {
        return $this->db_srv;
    }

    public function insert_data($table,$data)
    {
        $this->db_srv->insert($table, $data);
        return $this->db_srv->insert_id();
    }

    public function insert_batch($table, $datas){
        if(!empty($datas)){
            $this->db_srv->insert_batch($table,$datas);
        }
    }

    public function update_data($table,$data,$where)
    {
        $this->db_srv->where($where);
        $this->db_srv->update($table,$data);
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

    public function get_servers()
    {
        $this->db_srv->where('ACTIVE_FLAG', 'Y');
        return $this->db_srv->get('servers')->result();
    }

    public function getServerWithUser($username)
    {
        $this->db_srv->select('
            s.SERVER_ID,s.DB_NAME, s.DB_ALIAS, s.HOSTNAME, s.PORT,
            us.PRIMARY_FLAG, us.ACTIVE_FLAG, us.USER_SERVER_ID,
            u.USER_NAME,
        ');
        $this->db_srv->from('servers s');
        $this->db_srv->join('users u', "u.USER_NAME = '" . strtoupper(htmlspecialchars($username)) . "'", 'left');
        $this->db_srv->join('user_servers us', 'us.SERVER_ID = s.SERVER_ID AND us.USER_ID = u.USER_ID', 'left');
        $this->db_srv->where('s.ACTIVE_FLAG', 'Y');
        return $this->db_srv->get();
    }

    public function userServer($user_id,$server_id,$user_server_id)
    {
        $this->db_srv->select('
            us.USER_SERVER_ID,
            u.USER_ID, u.USER_NAME, u.PASSWORD, u.START_DATE,
            s.SERVER_ID, s.DB_NAME, s.DB_ALIAS, s.HOSTNAME, s.PORT,
        ');
        $this->db_srv->from('user_servers us');
        $this->db_srv->join('users u','u.USER_ID = us.USER_ID');
        $this->db_srv->join('servers s','s.SERVER_ID = us.SERVER_ID');
        $this->db_srv->where('u.USER_ID', $user_id);
        $this->db_srv->where('s.SERVER_ID', $server_id);
        $this->db_srv->where('us.USER_SERVER_ID', $user_server_id);
        $this->db_srv->where('s.ACTIVE_FLAG', 'Y');
        $this->db_srv->where('us.ACTIVE_FLAG', 'Y');
        return $this->db_srv->get();
    }
    
    public function userServerById($user_server_id)
    {
        $this->db_srv->select('
            us.USER_SERVER_ID,
            u.USER_ID, u.USER_NAME, u.PASSWORD, u.START_DATE,
            s.SERVER_ID, s.DB_NAME, s.HOSTNAME, s.PORT,
        ');
        $this->db_srv->from('user_servers us');
        $this->db_srv->join('users u','u.USER_ID = us.USER_ID');
        $this->db_srv->join('servers s','s.SERVER_ID = us.SERVER_ID');
        $this->db_srv->where('us.USER_SERVER_ID', $user_server_id);
        $this->db_srv->where('s.ACTIVE_FLAG', 'Y');
        $this->db_srv->where('us.ACTIVE_FLAG', 'Y');
        return $this->db_srv->get();
    }
}