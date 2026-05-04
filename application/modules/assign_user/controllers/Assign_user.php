<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assign_user extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Assign_user_model', 'user');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $this->template->load('template', 'assign_user/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->user->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $s) {
            $no++;
            $row = array();
            $row['no']        = $no;
            $row['username']  = '<a href="'.base_url($this->access['url'].'/detail/'.base64url_encode($this->encrypt->encode($s->USER_ID))).'">'.$s->USER_NAME.'</a>';
            $row['created_at']= $s->CREATED_DATE;
            $row['updated_at']= $s->LAST_UPDATE_DATE;
            $data[]     = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->user->count_all(),
            "recordsFiltered" => $this->user->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add()
    {
        try {
            $this->_validation();

            if ($this->form_validation->run() == FALSE) {
                $data['title']      = 'Tambah';
                $data['breadcrumb'] = 'Tambah';
                $data['servers']    = $this->user->get_servers();
                $this->template->load('template', 'assign_user/add', $data);
            } else {
                $db_srv = $this->user->db_srv();
                $db_srv->trans_start();
                $post = $this->input->post();
                $now  = date('Y-m-d H:i:s');
                $params = [
                    'USER_NAME'     => $post['name'] ? strtoupper(htmlspecialchars($post['name'])) : null,
                    'PASSWORD'      => '*' . strtoupper(sha1(sha1($post['password'], true))),
                    'START_DATE'    => $post['start_date'] ? htmlspecialchars($post['start_date']) : null,
                    'CREATED_DATE'  => $now,
                ];
                $user_id        = $this->user->insert_data('users',$params);
                $encoded_id     = base64url_encode($this->encrypt->encode($user_id));
                
                // assign database
                $arr_insert = [];
                foreach ($post['servers'] ?? [] as $i => $v) {
                    $server_id      = (int) $this->encrypt->decode(base64url_decode($v));
                    $active_flag    = ($post['active_flag'][$i] ?? 'N') === 'Y' ? 'Y' : 'N';
                    $default_server = ($post['default_server'][$i] ?? 'N') === 'Y' ? 'Y' : 'N';

                    if($server_id && $user_id && $active_flag == 'Y'){
                        $arr_insert[] = [
                            'USER_ID'       => $user_id,
                            'SERVER_ID'     => $server_id,
                            'ACTIVE_FLAG'   => $active_flag,
                            'PRIMARY_FLAG'  => $default_server,
                            'CREATED_DATE'  => $now
                        ];
                    }
                }
                $this->user->insert_batch('user_servers',$arr_insert);

                $db_srv->trans_complete();
                if ($db_srv->trans_status() === FALSE) {
                    $db_error = $db_srv->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                    redirect('assign_user/add');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect('assign_user/detail/' . $encoded_id);
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function _validation(){
        // untuk fungsi validation callback HMVC
        $this->form_validation->CI = &$this;

        $this->form_validation->set_rules('name', 'User Name', 'trim|required|callback_check_unique');
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required');
        $pw_required = $this->input->post('id') ? '' : '|required';
        $this->form_validation->set_rules('password', 'Password', 'trim|min_length[3]'.$pw_required);
    }

    public function check_unique($str)
    {
        $id    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
        $query = $this->user->get_data('users', ['USER_NAME' => $str]);

        if ($query->num_rows() > 0 && $query->row()->USER_ID != $id) {
            $this->form_validation->set_message('check_unique', '{field} "'.$str.'" already exists.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function detail($id)
    {
        
        try {
            // untuk fungsi validation callback HMVC
            $this->_validation();

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->user->get_data('users', ['USER_ID' => $id]);
                if ($query->num_rows() > 0) {

                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data']       = $query->row();
                    $data['servers']    = $this->user->getServerWithUser($data['data']->USER_NAME)->result();
                    $this->template->load('template', 'assign_user/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('assign_user');
                }
            } else {
                $user_id    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->user->get_data('users', ['USER_ID' => $user_id]);
                $encoded_id = base64url_encode($this->encrypt->encode($user_id));
                if ($query->num_rows() > 0) {
                    $db_srv = $this->user->db_srv();
                    $db_srv->trans_start();
                    $post = $this->input->post();
                    $now  = date('Y-m-d H:i:s');
                    $params = [
                        'USER_NAME'     => $post['name'] ? strtoupper(htmlspecialchars($post['name'])) : null,
                        'START_DATE'    => $post['start_date'] ? htmlspecialchars($post['start_date']) : null,
                        'LAST_UPDATE_DATE'  => $now,
                    ];
                    if($post['password']){
                        $params['PASSWORD'] = '*' . strtoupper(sha1(sha1($post['password'], true)));
                    }
                    $this->user->update_data('users',$params,['USER_ID' => $user_id]);
                    
                    // assign database
                    $arr_insert = [];
                    $arr_update = [];
                    foreach ($post['servers'] ?? [] as $i => $v) {
                        $server_id      = (int) $this->encrypt->decode(base64url_decode($v));
                        $user_server_id = (int) $this->encrypt->decode(base64url_decode($post['user_servers'][$i] ?? ''));
                        $active_flag    = ($post['active_flag'][$i] ?? 'N') === 'Y' ? 'Y' : 'N';
                        $default_server = ($post['default_server'][$i] ?? 'N') === 'Y' ? 'Y' : 'N';

                        if($server_id && $user_server_id && $user_id){
                            $arr_update[] = [
                                'USER_SERVER_ID'=> $user_server_id,
                                'USER_ID'       => $user_id,
                                'SERVER_ID'     => $server_id,
                                'ACTIVE_FLAG'   => $active_flag,
                                'PRIMARY_FLAG'  => $default_server,
                                'LAST_UPDATE_DATE' => $now
                            ];
                        }else if($server_id && $user_id && !$user_server_id && $active_flag == 'Y'){
                            $arr_insert[] = [
                                'USER_ID'       => $user_id,
                                'SERVER_ID'     => $server_id,
                                'ACTIVE_FLAG'   => $active_flag,
                                'PRIMARY_FLAG'  => $default_server,
                                'CREATED_DATE'  => $now
                            ];
                        }
                    }
                    $this->user->insert_batch('user_servers',$arr_insert);
                    $this->user->update_batch('user_servers',$arr_update,'USER_SERVER_ID');

                    $db_srv->trans_complete();
                    if ($db_srv->trans_status() === FALSE) {
                        $db_error = $db_srv->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('assign_user/detail/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('assign_user/detail/' . $encoded_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('assign_user');
                }
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function clone($id)
    {
        try {
            $decrypt = (string) $this->encrypt->decode(base64url_decode($id));
            $arr     = explode('-', $decrypt);
            $user_id = $arr[0];
            $server_id      = $arr[1] ?? 0;
            $user_server_id = $arr[2] ?? 0;
            $encode_user_id = base64url_encode($this->encrypt->encode($user_id));
            $encode_url     = base64url_encode($this->encrypt->encode($user_id.'-'.$server_id.'-'.$user_server_id));
            
            if(!$this->access['update']){
                $this->session->set_flashdata('warning', 'Anda tidak ada akses hapus untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
                redirect('assign_user/detail/'.$encode_user_id);die();
            }

            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;
            $this->form_validation->set_rules('group_id', 'Group Name', 'trim|required');
            $this->form_validation->set_rules('divisi_id', 'Division', 'trim|required');

            $query = $this->user->userServer($user_id,$server_id,$user_server_id);
            if ($query->num_rows() > 0) {
                $dt_server = $query->row();
                $this->load->model('Sync_model','sync');
                $res_db = $this->sync->set_database($dt_server->DB_NAME, $dt_server->HOSTNAME, $dt_server->PORT);
                if(!$res_db['status']){
                    $this->session->set_flashdata('warning', $res_db['message']);
                    redirect('assign_user/detail/'.$encode_user_id);die();
                }
                
                $user =  $this->sync->get_user($dt_server->USER_NAME);
                $pw_required = '';
                if(!$dt_server->PASSWORD && !$user){
                    $pw_required = '|required';
                }
                $this->form_validation->set_rules('password', 'Password', 'trim|min_length[3]'.$pw_required);

                if ($this->form_validation->run() == false) {
                    $data['title']      = 'Assign Database';
                    $data['breadcrumb'] = 'Assign Database';
                    $data['data']       = $dt_server;
                    $data['user']       = $user;
                    $data['old_menu']   = $this->input->post('menu') ?: [];
                    $this->template->load('template', 'assign_user/clone', $data);
                }else{
                    $post = $this->input->post();
                    $db_sync = $this->sync->db_sync();
                    $db_sync->trans_start();
                    $this->sync->save_user($dt_server,$user,$post);

                    $db_sync->trans_complete();
                    if ($db_sync->trans_status() === FALSE) {
                        $db_error = $db_sync->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('assign_user/clone/' . $encode_url);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('assign_user/clone/' . $encode_url);
                    }
                }

            } else {
                $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                $append = $user_id?'/detail/'.$encode_user_id:'';
                redirect('assign_user'.$append);
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function _checkUserServer($id){
        $id = (int) $this->encrypt->decode(base64url_decode($id));
        $query = $this->user->userServerById($id);
        if ($query->num_rows() > 0) {
            $dt_server = $query->row();
            $this->load->model('Sync_model','sync');
            $res_db = $this->sync->set_database($dt_server->DB_NAME, $dt_server->HOSTNAME, $dt_server->PORT);
            if(!$res_db['status']){
                echo json_encode([]);die;
            }
            return $dt_server;
        }else{
            echo json_encode([]);die;
        }
    }
    
    public function get_group($id)
    {
        $dt_server  = $this->_checkUserServer($id);
        $result     = $this->sync->getGroup()->result();
        echo json_encode($result);
    }

    public function get_divisi($id)
    {
        $dt_server  = $this->_checkUserServer($id);
        $result     = $this->sync->getDivisi()->result();
        echo json_encode($result);
    }
}