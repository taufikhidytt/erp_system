<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sync_model extends CI_Model
{
    private $db_sync;
    public function __construct() {
        parent::__construct();
    }

    public function set_database($database, $hostname, $port)
    {
        // Tutup koneksi lama jika sebelumnya sudah ada yang terbuka
        if (isset($this->db_sync) && is_object($this->db_sync) && isset($this->db_sync->conn_id)) {
            $this->db_sync->close();
        }

        if ( ! isset($db)) {
            require(APPPATH.'config/database.php');
        }

        $username = $db['default']['username'];
        $password = $db['default']['password'];

        $config = array(
            'hostname' => $hostname,
            'username' => $username,
            'password' => $password,
            'database' => $database,
            'port'     => $port,
            'dbdriver' => 'mysqli',
            'pconnect' => FALSE,
            'db_debug' => FALSE, // Wajib FALSE agar kita bisa menangkap error-nya sendiri
            'char_set' => 'utf8',
            'dbcollat' => 'utf8_general_ci'
        );

        // MATIKAN ERROR REPORTING PHP SEMENTARA
        // mencegah pesan "mysqli::real_connect()" muncul di layar
        $error_level = error_reporting();
        error_reporting(0);

        $db_obj = $this->load->database($config, TRUE);
        
        //KEMBALIKAN ERROR REPORTING KE ASALNYA
        error_reporting($error_level);

        // Cek koneksi
        if (!$db_obj || !$db_obj->conn_id) {
            $error = ($db_obj) ? $db_obj->error() : ['message' => 'Unable to connect to database with provided settings.'];
            return [
                'status'  => false,
                'message' => $error['message']
            ];
        }

        $this->db_sync = $db_obj;
        return [
            'status'  => true,
            'message' => 'Connection successful'
        ];
    }

    public function db_sync()
    {
        return $this->db_sync;
    }

    public function get_user($username)
    {
        $this->db_sync->select("
            a.ERP_USER_ID,a.ERP_GROUP_ID,a.ERP_USER_NAME,a.ERP_USER_DESC,a.TITLE,a.DIVISI_ID,a.START_DATE,a.END_DATE,
        ");
        $this->db_sync->from('erp_user a');
        $this->db_sync->where('a.ERP_USER_NAME', $username);
        return $this->db_sync->get()->row();
    }

    public function getGroup()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');
        $this->db_sync
            ->select("a.ERP_GROUP_ID as id, a.ERP_GROUP_NAME text")
            ->from('erp_group a');

        if ($id) {
            $this->db_sync->where('a.ERP_GROUP_ID', $id)->limit(1);
        } else {
            $this->db_sync->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db_sync->group_start()
                    ->like('a.ERP_GROUP_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db_sync->limit(50);
        }

        return $this->db_sync->get();
    }

    public function getDivisi()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');
        $this->db_sync
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text")
            ->where("b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET('DIVISI')", NULL, FALSE)
            ->from('erp_lookup_value b')
            ->order_by('b.PRIMARY_FLAG DESC, b.DISPLAY_NAME');
        
        

        if ($id) {
            $this->db_sync->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } else {
            $this->db_sync->where('b.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db_sync->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db_sync->limit(50);
        }

        return $this->db_sync->get();
    }

    public function save_user($dt_server,$user,$post){
        date_default_timezone_set('Asia/Jakarta');
        $params = array(
            'ERP_GROUP_ID'    => $post['group_id'] ? htmlspecialchars($post['group_id']) : null,
            'ERP_USER_NAME'   => $dt_server->USER_NAME,
            'ERP_USER_DESC'   => $post['full_name'] ? htmlspecialchars($post['full_name']) : null,
            'TITLE'           => $post['title'] ? htmlspecialchars($post['title']) : null,
            'DIVISI_ID'       => $post['divisi_id'] ? htmlspecialchars($post['divisi_id']) : null,
            'START_DATE'      => $post['start_date'] ? htmlspecialchars($post['start_date']) : null,
            'END_DATE'        => $post['end_date'] ? htmlspecialchars($post['end_date']) : null,
            'CREATED_DATE'    => date('Y-m-d H:i:s'),
        );
        if($post['password']){
            $params['PASSWORD'] = '*' . strtoupper(sha1(sha1($post['password'], true)));
        }else if($dt_server->PASSWORD && !$user){
            $params['PASSWORD'] = $dt_server->PASSWORD;
        }

        if($user){
            $this->db_sync->where('ERP_USER_ID', $user->ERP_USER_ID);
            $this->db_sync->update('erp_user', $params);
        }else{
            $this->db_sync->insert('erp_user',$params);
        }
    }
}