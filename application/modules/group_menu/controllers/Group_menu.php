<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Group_menu extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Group_menu_model','group');
    }
    
    public function index()
    {
        try {
            $data['title'] = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $this->template->load('template', 'group_menu/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'erp_group a',
            'select' => [
                'a.ERP_GROUP_ID,a.ERP_GROUP_NAME,a.ACTIVE_FLAG,a.NOTE',
            ],
            'column_search' => [null, 'a.ERP_GROUP_NAME', 'a.NOTE', 'a.ACTIVE_FLAG'],
            'column_order'  => [null, 'a.ERP_GROUP_NAME', 'a.NOTE', 'a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->ERP_GROUP_ID));
            return [
                'no' => $no,
                'name' => '<a href="'.base_url('group_menu/detail/'.$id).'">'.$row->ERP_GROUP_NAME.'</a>',
                'note' => $row->NOTE,
                'active_flag' => $row->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
            ];
        }));
    }

    public function add()
    {
        try {

            $this->form_validation->CI = &$this;
            $this->form_validation->set_rules('name', 'Group Name', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $data['title']      = 'Tambah';
                $data['breadcrumb'] = 'Tambah';
                $data['menus'] = $this->get_menus();
                $this->template->load('template', 'group_menu/add', $data);
                return;
            } else {
                $post = $this->input->post();
                $this->db->trans_begin();
                
                //insert group
                $group_id = $this->group->insert($post);
                
                $access = [];
                if(isset($post['view']) && is_array($post['view'])){
                    foreach ($post['view'] as $id_menu => $v) {
                        $res_acc = $this->check_access($id_menu);
                        $res_acc['ERP_GROUP_ID']    = $group_id;
                        $access[] = $res_acc;

                        if(isset($post['view_child'][$id_menu])){
                            foreach ($post['view_child'][$id_menu] as $id_menu_child => $v2) {
                                $res_acc = $this->check_access($id_menu_child, $id_menu);
                                $res_acc['ERP_GROUP_ID']    = $group_id;
                                $access[] = $res_acc;
                            }
                        }
                    }
                }

                //detele data group menu by role
                $this->group->delete_group_menu($group_id);

                //insert data group menu by role
                $this->group->insert_batch('erp_group_menu', $access);

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('fpk');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Berhasil menyimpan data dan detail baru!');
                    redirect('group_menu/detail/' . base64url_encode($this->encrypt->encode($group_id)));
                }
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->CI = &$this;
            $this->form_validation->set_rules('id', 'id', 'trim|required');
            $this->form_validation->set_rules('name', 'Group Name', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $id         = (int) $this->encrypt->decode(base64url_decode($id));
                $group      = $this->group->get_by_id($id);
                if ($group->num_rows() > 0) {
                    $data['title']      = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data']       = $group->row();
                    $data['menus']      = $this->get_menus();
                    $data['group_menu'] = $this->get_group_menu($id);
                    $this->template->load('template', 'group_menu/detail', $data);
                }else{
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('group_menu');
                }
            }else{
                $post   = $this->input->post();
                $id     = (int) $this->encrypt->decode(base64url_decode($post['id']));
                $group  = $this->group->get_by_id($id);
                if ($group->num_rows() > 0) {
                    $this->db->trans_begin();

                    //update group
                    $this->group->update_by_id($id,$post);
                    
                    $access = [];
                    if(isset($post['view']) && is_array($post['view'])){
                        foreach ($post['view'] as $id_menu => $v) {
                            $res_acc = $this->check_access($id_menu);
                            $res_acc['ERP_GROUP_ID']    = $id;
                            $access[] = $res_acc;

                            if(isset($post['view_child'][$id_menu])){
                                foreach ($post['view_child'][$id_menu] as $id_menu_child => $v2) {
                                    $res_acc = $this->check_access($id_menu_child, $id_menu);
                                    $res_acc['ERP_GROUP_ID']    = $id;
                                    $access[] = $res_acc;
                                }
                            }
                        }
                    }

                    //detele data group menu by role
                    $this->group->delete_group_menu($id);

                    //insert data group menu by role
                    $this->group->insert_batch('erp_group_menu', $access);

                    // ======================
                    // TRANSACTION CHECK
                    // ======================
                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                        redirect('fpk');
                    } else {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('success', 'Berhasil menyimpan data dan detail baru!');
                        redirect('group_menu/detail/' . base64url_encode($this->encrypt->encode($id)));
                    }
                    
                }else{
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('group_menu');
                }
            }

        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function get_menus()
    {
        $menus = $this->group->get_menus()->result_array();
        $result= [];
        foreach ($menus as $m) {
            if($m['PARENT_ID'] == 0){
                $m['child']              = [];
                $result[$m['ERP_MENU_ID']] = $m;
            }else if(isset($result[$m['PARENT_ID']])){
                $result[$m['PARENT_ID']]['child'][] = $m;
            }
        }
        return $result;
    }

    private function get_group_menu($group_id)
    {
        $group_menu = $this->group->get_group_menu($group_id)->result_array();
        $result     = [];
        foreach ($group_menu as $v) {
            $result[$v['ERP_MENU_ID']] = $v;
        }
        return $result;
    }

    private function check_access($id_menu,$id_parent='')
    {
        $post   = $this->input->post();
        $res = [
            'ERP_MENU_ID'       => (int) $id_menu,
            'CREATED_BY'        => $this->session->userdata('id'),
            'CREATED_DATE'      => date('Y-m-d H:i:s'),
            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s')
        ];
        foreach (['view' => 'VIEW_FLAG','add' => 'INSERT_FLAG','edit' => 'UPDATE_FLAG','delete' => 'DELETE_FLAG'] as $post_name => $field) {
            $res[$field] = 'N';
            if(isset($post[$post_name][$id_menu]) && !$id_parent){
                $res[$field] = $post[$post_name][$id_menu] == '1'?'Y':'N';
            }else if($id_parent && isset($post[$post_name.'_child'][$id_parent][$id_menu])){
                $res[$field] = $post[$post_name.'_child'][$id_parent][$id_menu] == '1'?'Y':'N';
            }
        }
        return $res;
    }
}