<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('User_model','user');
    }
    
    public function index()
    {
        try {
            $data['title'] = 'User';
            $data['breadcrumb'] = 'User';
            $this->template->load('template', 'user/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'erp_user a',
            'select' => [
                'a.ERP_USER_ID,a.ERP_USER_NAME,a.ERP_USER_DESC,a.TITLE,b.ERP_GROUP_NAME,c.DISPLAY_NAME AS DIVISI',
            ],
            'joins' => [
                ['erp_group b', 'b.ERP_GROUP_ID = a.ERP_GROUP_ID', 'inner'],
                ['erp_lookup_value c', 'c.ERP_LOOKUP_VALUE_ID = a.DIVISI_ID', 'left'],
            ],
            'column_search' => [null,'a.ERP_USER_NAME','a.ERP_USER_DESC', 'b.ERP_GROUP_NAME', 'c.DISPLAY_NAME','a.TITLE'],
            'column_order'  => [null,'a.ERP_USER_NAME','a.ERP_USER_DESC', 'b.ERP_GROUP_NAME', 'c.DISPLAY_NAME','a.TITLE'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->ERP_USER_ID));
            $res = [
                'no' => $no,
                'name' => '<a href="'.base_url('user/detail/'.$id).'">'.$row->ERP_USER_NAME.'</a>',
                'full_name' => $row->ERP_USER_DESC,
                'group_name' => $row->ERP_GROUP_NAME,
                'divisi' => $row->DIVISI,
                'title' => $row->TITLE,
            ];
            if(isset($this->access['assign_database']) && $this->access['assign_database']){
                $res['action'] = '<a href="'.base_url('user/assign_db/'.$id).'" 
                    class="btn btn-sm btn-success" title="Tetapkan Database" data-bs-toggle="tooltip" data-bs-placement="left"><i class="ri ri-database-2-line"></i></a>';
            }
            return $res;
        }));
    }

    public function add(){
        // add dimatikan sementara karena penambahan user akan dilakukan pada menu yang berbeda
        $this->session->set_flashdata('warning', 'Halaman tidak ditemukan');
        redirect('user');die();

        try {
            
            // untuk fungsi validation callback HMVC
            $this->_validation_rules();

            if ($this->form_validation->run() == false) {
                $data['title']      = 'Tambah User';
                $data['breadcrumb'] = 'Tambah User';
                $data['old_menu']   = $this->input->post('menu') ?? [];
                $this->template->load('template', 'user/add', $data);
            } else {
                $post       = $this->input->post();

                $this->db->trans_start();
                $user_id    = $this->user->add($post);
                $encoded_id = base64url_encode($this->encrypt->encode($user_id));
                $this->user->save_permissions($user_id);
                $this->user->add_user_accounts($user_id, $post);
                $this->user->add_user_warehouses($user_id, $post);
                $this->user->add_user_sales($user_id, $post);

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                    redirect('user/add');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect('user/detail/' . $encoded_id);
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function _validation_rules()
    {
        // untuk fungsi validation callback HMVC
        $this->form_validation->CI = &$this;

        $this->form_validation->set_rules('name', 'Nama User', 'trim|required');
        $this->form_validation->set_rules('group_id', 'Group Name', 'trim|required');
        $this->form_validation->set_rules('divisi_id', 'Divisi', 'trim|required');
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required');

        $pw_required = $this->input->post('id') ? '' : '|required';
        $this->form_validation->set_rules('password', 'Password', 'trim|min_length[3]'.$pw_required);

        $accounts = $this->input->post('account') ?? [];
        if(!empty($accounts)){
            foreach($accounts as $k => $v){
                $this->form_validation->set_rules('account['.$k.']', 'Kas/Bank', 'trim|required');
            }
            $this->form_validation->set_rules('warehouses[]', 'Warehouse', 'callback_check_duplicate_account');
        }
        
        $warehouses = $this->input->post('warehouses') ?? [];
        if(!empty($warehouses)){
            foreach($warehouses as $k => $v){
                $this->form_validation->set_rules('warehouses['.$k.']', 'Warehouse', 'trim|required');
            }
            $this->form_validation->set_rules('warehouses[]', 'Warehouse', 'callback_check_duplicate_warehouse');
        }

        $sales = $this->input->post('sales') ?? [];
        if(!empty($sales)){
            foreach($sales as $k => $v){
                $this->form_validation->set_rules('sales['.$k.']', 'Sales', 'trim|required');
            }
            $this->form_validation->set_rules('sales[]', 'Sales', 'callback_check_duplicate_sales');
        }
    }
    public function check_duplicate_account($post_data)
    {
        $data = is_array($post_data) ? $post_data : [];
        $filtered = array_filter($data);
        if (count($filtered) !== count(array_unique($filtered))) {
            $this->form_validation->set_message('check_duplicate_account', 'Ada duplikat data pada pilihan {field}!');
            return FALSE;
        }

        return TRUE;
    }
    public function check_duplicate_warehouse($post_data)
    {
        $data = is_array($post_data) ? $post_data : [];
        $filtered = array_filter($data);
        if (count($filtered) !== count(array_unique($filtered))) {
            $this->form_validation->set_message('check_duplicate_warehouse', 'Ada duplikat data pada pilihan {field}!');
            return FALSE;
        }

        return TRUE;
    }
    public function check_duplicate_sales($post_data)
    {
        $data = is_array($post_data) ? $post_data : [];
        $filtered = array_filter($data);
        if (count($filtered) !== count(array_unique($filtered))) {
            $this->form_validation->set_message('check_duplicate_sales', 'Ada duplikat data pada pilihan {field}!');
            return FALSE;
        }

        return TRUE;
    }

    public function detail($id)
    {
        
        try {
            // untuk fungsi validation callback HMVC
            $this->_validation_rules();

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->user->getUserId($id);
                if ($query->num_rows() > 0) {

                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data']       = $query->row();
                    $data['accounts']   = $this->_accounts($id);
                    $data['warehouses'] = $this->_warehouses($id);
                    $data['sales']      = $this->_sales($id);
                    $data['old_menu']   = $this->input->post('menu') ?: [];
                    $this->template->load('template', 'user/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('user');
                }
            } else {
                $id    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->user->getUserId($id);
                $encoded_id = base64url_encode($this->encrypt->encode($id));
                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->user->update_by_id($id,$post);
                    $this->user->save_permissions($id);
                    $this->user->update_user_accounts($id, $post);
                    $this->user->update_user_warehouses($id, $post);
                    $this->user->update_user_sales($id, $post);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $db_error = $this->db->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('user/detail/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('user/detail/' . $encoded_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('user');
                }
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function _accounts($user_id)
    {
        $dt_accounts        = $this->user->getUserAccounts($user_id)->result();
        $old_account        = $this->input->post('account') ?? [];
        $old_account_note   = $this->input->post('account_note') ?? [];
        $old_account_id     = $this->input->post('account_id') ?? [];

        $display_list   = [];
        $temp_ids       = [];
        foreach ($old_account as $k => $v) {
            $display_list[] = [
                'account_id' => $old_account_id[$k] ?? 0,
                'account' => $v,
                'account_note' => $old_account_note[$k] ?? null,
            ];
            $temp_ids[] = $v;
        }
        foreach ($dt_accounts as $account) {
            if (!in_array($account->COA_ID, $temp_ids)) {
                $display_list[] = [
                    'account_id' => $account->ERP_USER_D_AKUN_ID,
                    'account' => $account->COA_ID,
                    'account_note' => $account->NOTE,
                ];
            }
        }
        return $display_list;
    }
    private function _warehouses($user_id)
    {
        $dt_warehouses      = $this->user->getUserWarehouses($user_id)->result();
        $old_warehouses     = $this->input->post('warehouses') ?? [];
        $old_warehouses_id  = $this->input->post('warehouses_id') ?? [];
        $old_warehouses_default  = $this->input->post('default_warehouse') ?? [];

        $display_list = [];
        $temp_ids = [];
        foreach ($old_warehouses as $k => $v) {
            $display_list[] = [
                'warehouse' => $v,
                'warehouse_id' => $old_warehouses_id[$k] ?? 0,
                'is_default' => $old_warehouses_default[$k] ?? 'N'
            ];
            $temp_ids[] = $v;
        }
        foreach ($dt_warehouses as $warehouse) {
            if (!in_array($warehouse->WAREHOUSE_ID, $temp_ids)) {
                $display_list[] = [
                    'warehouse' => $warehouse->WAREHOUSE_ID,
                    'warehouse_id' => $warehouse->ERP_WAREHOUSE_ID,
                    'is_default' => $warehouse->PRIMARY_FLAG == 'Y' ? 'Y' : 'N'
                ];
            }
        }
        return $display_list;
    }

    private function _sales($user_id)
    {
        $dt_sales      = $this->user->getUserSales($user_id)->result();
        $old_sales     = $this->input->post('sales') ?? [];
        $old_sales_id  = $this->input->post('sales_id') ?? [];

        $display_list = [];
        $temp_ids = [];
        foreach ($old_sales as $k => $v) {
            $display_list[] = [
                'sales' => $v,
                'sales_id' => $old_sales_id[$k] ?? 0,
            ];
            $temp_ids[] = $v;
        }
        foreach ($dt_sales as $sales) {
            if (!in_array($sales->KARYAWAN_ID, $temp_ids)) {
                $display_list[] = [
                    'sales' => $sales->KARYAWAN_ID,
                    'sales_id' => $sales->ERP_GROUP_SALES_ID,
                ];
            }
        }
        return $display_list;
    }

    public function get_group()
    {
        $result = $this->user->getGroup()->result();
        echo json_encode($result);
    }
    
    public function get_divisi()
    {
        $result = $this->user->getDivisi()->result();
        echo json_encode($result);
    }

    public function menu_permissions()
    {
        $group_id   = (int) $this->input->post('group_id');
        $user_id    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
        $group_menu = $this->user->getGroupMenu($group_id,$user_id)->result_array();
        
        $old_menu   = $this->input->post('old_menu') ? json_decode(base64_decode($this->input->post('old_menu')), true) : [];

        $view       = '';
        if(count($group_menu)){
            $menus = [];
            foreach ($group_menu as $m) {
                if($m['PARENT_ID'] == 0){
                    $m['child']              = [];
                    $menus[$m['ERP_MENU_ID']] = $m;
                }else if(isset($menus[$m['PARENT_ID']])){
                    $menus[$m['PARENT_ID']]['child'][] = $m;
                }
            }
            $view = $this->load->view('user/menu_permissions',[
                'menus' => $menus,
                'old_menu' => $old_menu
            ],true);
        }else{
            $view = '<div class="alert alert-dark mb-0" role="alert">
                Data not found
            </div>';
        }

        echo $view;
    }

    public function get_accounts()
    {
        $result = $this->user->getAccounts()->result();
        echo json_encode($result);
    }
    
    public function get_warehouses()
    {
        $result = $this->user->getWarehouses()->result();
        echo json_encode($result);
    }
    
    public function get_sales()
    {
        $result = $this->user->getSales()->result();
        echo json_encode($result);
    }

    public function assign_db($id){
        // assign_db dimatikan sementara karena assign_db user akan dilakukan pada menu yang berbeda
        $this->session->set_flashdata('warning', 'Halaman tidak ditemukan');
        redirect('user');die();

        try {
            if(!isset($this->access['assign_database']) || !$this->access['assign_database']){
                $this->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
                redirect('user');
            }

            //load database server
            $this->load->model('server/server_model','server');

            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;
            $this->form_validation->set_rules('id', 'ID User', 'trim|required');
            
            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->user->getUserId($id);
                if ($query->num_rows() > 0) {
                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Tetapkan Database';
                    $data['breadcrumb'] = 'Tetapkan Database';
                    $data['data']       = $query->row();
                    $data['servers']    = $this->server->getServerWithUser($data['data']->ERP_USER_NAME)->result();
                    $this->template->load('template', 'user/assign_db', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('user');
                }
            } else {
                $id         = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query      = $this->user->getUserId($id);
                $now        = date('Y-m-d H:i:s');
                $encoded_id = base64url_encode($this->encrypt->encode($id));
                if ($query->num_rows() > 0) {
                    $data = $query->row();
                    
                    $db_srv = $this->server->db_srv();
                    $db_srv->trans_start();
                    
                    //check user di database server
                    $user_id_srv = $this->checkUserServer($data->ERP_USER_NAME);

                    $post       = $this->input->post();
                    $arr_insert = [];
                    $arr_update = [];
                    foreach ($post['servers'] ?? [] as $i => $v) {
                        $server_id      = (int) $this->encrypt->decode(base64url_decode($v));
                        $user_server_id = (int) $this->encrypt->decode(base64url_decode($post['user_servers'][$i] ?? ''));
                        $active_flag    = ($post['active_flag'][$i] ?? 'N') === 'Y' ? 'Y' : 'N';
                        $default_server = ($post['default_server'][$i] ?? 'N') === 'Y' ? 'Y' : 'N';

                        if($server_id && $user_server_id && $user_id_srv){
                            $arr_update[] = [
                                'USER_SERVER_ID'=> $user_server_id,
                                'USER_ID'       => $user_id_srv,
                                'SERVER_ID'     => $server_id,
                                'ACTIVE_FLAG'   => $active_flag,
                                'PRIMARY_FLAG'  => $default_server,
                                'LAST_UPDATE_DATE' => $now
                            ];
                        }else if($server_id && $user_id_srv && !$user_server_id){
                            $arr_insert[] = [
                                'USER_ID'       => $user_id_srv,
                                'SERVER_ID'     => $server_id,
                                'ACTIVE_FLAG'   => $active_flag,
                                'PRIMARY_FLAG'  => $default_server,
                                'CREATED_DATE'  => $now
                            ];
                        }
                    }

                    $this->server->insert_batch('user_servers',$arr_insert);
                    $this->server->update_batch('user_servers',$arr_update,'USER_SERVER_ID');

                    $db_srv->trans_complete();
                    if ($db_srv->trans_status() === FALSE) {
                        $db_error = $db_srv->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('user/assign_db/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('user/assign_db/' . $encoded_id);
                    }

                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('user');
                }
            }

        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function checkUserServer($username){
        $ck_user = $this->server->get_user($username);
        if($ck_user->num_rows()>0){
            $user_id_srv = $ck_user->row()->USER_ID;
        }else{
            $user_id_srv = $this->server->insert_data('users',[
                'USER_NAME'     => $username,
                'CREATED_DATE'  => date('Y-m-d H:i:s')
            ]);
        }
        return (int) $user_id_srv;
    }

    public function get_log_info(){
        $params = json_decode($this->encrypt->decode(base64_decode($this->input->post('params'))), true);
        $id     = (int) ($params['id'] ?? 0);
        $this->load->model('M_union_datatables', 'union_datatables');
        $config = [
            'queries' => [
                [
                    'select' => 'a.LAST_UPDATE_DATE, u.ERP_USER_NAME, a.TRANSAKSI, a.NOTE',
                    'table'  => 'erp_log_edit a',
                    'join'   => ['erp_user u', 'a.LAST_UPDATE_BY = u.ERP_USER_ID'],
                    'where'  => "a.TABLE_NAME = 'ERP_USER' AND a.ID = " . $id,
                ],
                [
                    'select' => 'a.LAST_UPDATE_DATE, u.ERP_USER_NAME, a.TRANSAKSI, a.NOTE',
                    'table'  => 'erp_log_edit a',
                    'join'   => ['erp_user u', 'a.LAST_UPDATE_BY = u.ERP_USER_ID'],
                    'where'  => "a.TABLE_NAME in ('ERP_USER_D_AKUN','ERP_WAREHOUSE') AND a.ORDER_ID = " . $id,
                ],
            ],
            'search_columns' => ['a.LAST_UPDATE_DATE', 'u.ERP_USER_NAME', 'a.TRANSAKSI', 'a.NOTE'],
            'order_map' => [null, 'LAST_UPDATE_DATE', 'ERP_USER_NAME', 'TRANSAKSI', 'NOTE'],
            'order_by' => 'LAST_UPDATE_DATE DESC',
        ];
        $result = $this->union_datatables->generate($config, function ($row, $no) {
            return [
                'no' => $no,
                'tanggal' => $row->LAST_UPDATE_DATE,
                'user' => $row->ERP_USER_NAME,
                'transaksi' => $row->TRANSAKSI,
                'log' => $row->NOTE,
            ];
        });
        $info_header    = $this->user->get_log_user($id);
        $result['header'] = [
            'created_date' => $info_header->CREATED_DATE ?? '-',
            'user_created'   => $info_header->USER_CREATED ?? '-',
            'last_update_date' => $info_header->LAST_UPDATE_DATE ?? '-',
            'user_updated'   => $info_header->USER_UPDATED ?? '-',
        ];
        echo json_encode($result);
    }
}