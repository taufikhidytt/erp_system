<?php
defined('BASEPATH') or exit('No direct script access allowed');

class menu extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Menu_model','menu');
    }

    public function index()
    {
        try {
            $data['title'] = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $this->template->load('template', 'menu/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->menu->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $m) {
            $no++;
            $name = $m->PARENT_ID != 0? '<span class="ps-3">'.$m->ERP_MENU_NAME.'</span>' : $m->ERP_MENU_NAME;
            $row = array();
            $row['no']      = $no;
            $row['seq']     = $m->SEQ;
            $row['name']    = '<a href="'.base_url('menu/'.($m->PARENT_ID ==0?'detail':'detail_child').'/'.base64url_encode($this->encrypt->encode($m->ERP_MENU_ID))).'">'.$name.'</a>';
            $row['prompt']  = $m->PARENT_ID != 0? '<span class="ps-3">'.$m->PROMPT.'</span>' : $m->PROMPT;
            $row['active_flag'] = $m->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            $row['document_no'] = $m->FLAG_ERP_NO == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            $row['ppn']         = $m->PPN == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>';

            $actions = '';
            if($this->access['update']){
                $actions .= $m->PARENT_ID != 0 ? '<a href="'.base_url('menu/permissions/'.base64url_encode($this->encrypt->encode($m->ERP_MENU_ID))).'" class="btn btn-success btn-sm" title="Permissions" data-bs-toggle="tooltip" data-bs-placement="left"><i class="ri ri-key-fill"></i></a>' : '';
            }
            if($this->access['insert']){
                $actions .= $m->PARENT_ID == 0 ? '<a href="'.base_url('menu/add_child/'.base64url_encode($this->encrypt->encode($m->ERP_MENU_ID))).'" class="btn btn-primary btn-sm" title="Add Child" data-bs-toggle="tooltip" data-bs-placement="left"><i class="ri ri-add-fill"></i></a>' : '';
            }
            $row['action'] = $actions;
            $data[]     = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->menu->count_all(),
            "recordsFiltered" => $this->menu->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function permissions($id){
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->CI = &$this;
            $this->form_validation->set_rules('id', 'id', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                $id     = (int) $this->encrypt->decode(base64url_decode($id));
                $menu   = $this->menu->get_by_id($id);
                if ($menu->num_rows() > 0) {
                    $data['title']      = 'Permissions';
                    $data['breadcrumb'] = 'Permissions';
                    $data['menu']       = $menu->row();
                    $data['permissions']= json_decode($data['menu']->PERMISSIONS?:'[]', true);
                    $this->template->load('template', 'menu/permissions', $data);
                }else{
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('menu');
                }
            }else{
                $post = $this->input->post();
                $id     = (int) $this->encrypt->decode(base64url_decode($post['id']));
                $menu   = $this->menu->get_by_id($id);
                if ($menu->num_rows() > 0) {
                    $permissions = [];
                    $clean = function($str) {
                        return htmlspecialchars(strtolower(str_replace(' ', '_', $str)));
                    };
                    
                    if (!empty($post['act'])) $permissions['actions'] = array_map($clean, $post['act']);
                    if (!empty($post['field'])) $permissions['fields'] = array_map($clean, $post['field']);

                    if (!empty($post['tab'])) {
                        foreach ($post['tab'] as $t) {
                            $tabName = $clean($t);
                            
                            if (isset($post['tab_field'][$t])) {
                                $permissions['tabs'][$tabName] = array_map($clean, $post['tab_field'][$t]);
                            } else {
                                $permissions['tabs'][] = $tabName; 
                            }
                        }
                    }
                    
                    $this->menu->update_by_id($id,[
                        'PERMISSIONS' => count($permissions)?json_encode($permissions):null
                    ]);
                    $this->session->set_flashdata('success', 'Data berhasil diperbarui!');
                    redirect('menu/permissions/' . base64url_encode($this->encrypt->encode($id)));
                }else{
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('menu');
                }
            }

        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function add()
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('name', 'Menu Name', 'trim|required');
            $this->form_validation->set_rules('prompt', 'Prompt', 'trim|required');
            $this->form_validation->set_rules('icon', 'Icon', 'trim|required');
            $this->form_validation->set_rules('seq', 'SEQ', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $data['title']      = 'Tambah';
                $data['breadcrumb'] = 'Tambah';
                $this->template->load('template', 'menu/add', $data);
            } else {
                $this->db->trans_start();
                $post = $this->input->post();
                $params = [
                    'PARENT_ID'     => 0,
                    'ERP_MENU_NAME' => $post['name'] ? htmlspecialchars($post['name']) : null,
                    'PROMPT'        => $post['prompt'] ? htmlspecialchars($post['prompt']) : null,
                    'ACTIVE_FLAG'   => $post['active_flag'] ? ($post['active_flag']=='on'?'Y':'N') : 'N',
                    'FLAG_ERP_NO'   => $post['document_no'] ? ($post['document_no']=='on'?'Y':'N') : 'N',
                    'MENU_ICON'     => $post['icon'] ? htmlspecialchars($post['icon']) : null,
                    'SEQ'           => $post['seq'] ? (int) $post['seq'] : null,
                    'CREATED_BY'    => $this->session->userdata('id'),
                    'CREATED_DATE'  => date('Y-m-d H:i:s'),
                ];
                
                $ppn = $post['ppn'] ? ($post['ppn']=='on'?'Y':'N') : 'N';
                if($params['FLAG_ERP_NO'] == 'Y'){
                    $params['PPN'] = $ppn;
                }

                $menu_id        = $this->menu->insert_data('erp_menu',$params);
                $encoded_id     = base64url_encode($this->encrypt->encode($menu_id));

                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                    redirect('menu/add');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect('menu/detail/' . $encoded_id);
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('id', 'ID', 'trim|required');
            $this->form_validation->set_rules('name', 'Menu Name', 'trim|required');
            $this->form_validation->set_rules('prompt', 'Prompt', 'trim|required');
            $this->form_validation->set_rules('icon', 'Icon', 'trim|required');
            $this->form_validation->set_rules('seq', 'SEQ', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->menu->get_by_id($id,0);
                if ($query->num_rows() > 0) {
                    $data['title']      = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data']       = $query->row();
                    $this->template->load('template', 'menu/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('menu');
                }
            } else {
                $id    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->menu->get_by_id($id,0);
                $encoded_id = base64url_encode($this->encrypt->encode($id));
                if ($query->num_rows() > 0) {
                    $this->db->trans_start();
                    $post = $this->input->post();
                    $params = [
                        'ERP_MENU_NAME' => $post['name'] ? htmlspecialchars($post['name']) : null,
                        'PROMPT'        => $post['prompt'] ? htmlspecialchars($post['prompt']) : null,
                        'ACTIVE_FLAG'   => $post['active_flag'] ? ($post['active_flag']=='on'?'Y':'N') : 'N',
                        'FLAG_ERP_NO'   => $post['document_no'] ? ($post['document_no']=='on'?'Y':'N') : 'N',
                        'MENU_ICON'     => $post['icon'] ? htmlspecialchars($post['icon']) : null,
                        'SEQ'           => $post['seq'] ? (int) $post['seq'] : null,
                        'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                    ];
                    $ppn = $post['ppn'] ? ($post['ppn']=='on'?'Y':'N') : 'N';
                    if($params['FLAG_ERP_NO'] == 'Y'){
                        $params['PPN'] = $ppn;
                    }
                    $this->menu->update_by_id($id,$params);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $db_error = $this->db->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('menu/detail/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('menu/detail/' . $encoded_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('menu');
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function add_child($parent_id)
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('parent_id', 'Menu ID', 'trim|required');
            $this->form_validation->set_rules('name', 'Menu Name', 'trim|required');
            $this->form_validation->set_rules('prompt', 'Prompt', 'trim|required');
            $this->form_validation->set_rules('seq', 'SEQ', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $parent_id = $this->encrypt->decode(base64url_decode($parent_id));
                $query = $this->menu->get_by_id($parent_id,0);
                if ($query->num_rows() > 0) {
                    $data['title']      = 'Add Child';
                    $data['breadcrumb'] = 'Add Child';
                    $data['data']       = $query->row();
                    $data['last_seq']   = $this->menu->get_last_seq($parent_id);
                    $this->template->load('template', 'menu/add_child', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('menu');
                }
            } else {
                $parent_id    = (int) $this->encrypt->decode(base64url_decode($this->input->post('parent_id')));
                $query        = $this->menu->get_by_id($parent_id,0);
                $encoded_id = base64url_encode($this->encrypt->encode($parent_id));

                if ($query->num_rows() > 0) {
                    $this->db->trans_start();
                    $post = $this->input->post();
                    $params = [
                        'PARENT_ID'     => $parent_id,
                        'ERP_MENU_NAME' => $post['name'] ? htmlspecialchars($post['name']) : null,
                        'PROMPT'        => $post['prompt'] ? htmlspecialchars($post['prompt']) : null,
                        'ACTIVE_FLAG'   => $post['active_flag'] ? ($post['active_flag']=='on'?'Y':'N') : 'N',
                        'FLAG_ERP_NO'   => $post['document_no'] ? ($post['document_no']=='on'?'Y':'N') : 'N',
                        'SEQ'           => $post['seq'] ? (int) $post['seq'] : null,
                        'CREATED_BY'    => $this->session->userdata('id'),
                        'CREATED_DATE'  => date('Y-m-d H:i:s'),
                    ];
                    $ppn = $post['ppn'] ? ($post['ppn']=='on'?'Y':'N') : 'N';
                    if($params['FLAG_ERP_NO'] == 'Y'){
                        $params['PPN'] = $ppn;
                    }
                    $child_id           = $this->menu->insert_data('erp_menu',$params);
                    $encoded_child_id   = base64url_encode($this->encrypt->encode($child_id));

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $db_error = $this->db->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('menu/add_child/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('menu/detail_child/' . $encoded_child_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('menu');
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function detail_child($id)
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('id', 'ID', 'trim|required');
            $this->form_validation->set_rules('name', 'Menu Name', 'trim|required');
            $this->form_validation->set_rules('prompt', 'Prompt', 'trim|required');
            $this->form_validation->set_rules('seq', 'SEQ', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->menu->get_by_id($id);
                if ($query->num_rows() > 0 && $query->row()->PARENT_ID) {
                    $data['title']      = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data']       = $query->row();
                    $data['parent']     = $this->menu->get_by_id($data['data']->PARENT_ID)->row();
                    $this->template->load('template', 'menu/detail_child', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('menu');
                }
            } else {
                $post = $this->input->post();
                $id    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->menu->get_by_id($id);
                $encoded_id = base64url_encode($this->encrypt->encode($id));
                if ($query->num_rows() > 0 && $query->row()->PARENT_ID) {
                    $this->db->trans_start();
                    $post = $this->input->post();
                    $params = [
                        'ERP_MENU_NAME' => $post['name'] ? htmlspecialchars($post['name']) : null,
                        'PROMPT'        => $post['prompt'] ? htmlspecialchars($post['prompt']) : null,
                        'ACTIVE_FLAG'   => $post['active_flag'] ? ($post['active_flag']=='on'?'Y':'N') : 'N',
                        'FLAG_ERP_NO'   => $post['document_no'] ? ($post['document_no']=='on'?'Y':'N') : 'N',
                        'SEQ'           => $post['seq'] ? (int) $post['seq'] : null,
                        'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                    ];
                    $ppn = $post['ppn'] ? ($post['ppn']=='on'?'Y':'N') : 'N';
                    if($params['FLAG_ERP_NO'] == 'Y'){
                        $params['PPN'] = $ppn;
                    }
                    $this->menu->update_by_id($id,$params);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $db_error = $this->db->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('menu/detail_child/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('menu/detail_child/' . $encoded_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('menu');
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function sort()
    {
        if(!isset($this->access['urutan']) || !$this->access['urutan']){
            $this->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
            redirect('menu');
        }
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;
            $this->form_validation->set_rules('menus', 'List menu', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $data['title']      = 'Urutan';
                $data['breadcrumb'] = 'Urutan';
                $data['menus']      = $this->_get_menus();
                $this->template->load('template', 'menu/sort', $data);
            } else {
                $menus  = json_decode(base64_decode($this->input->post('menus')), true);;
                $params = [];
                foreach ($menus as $m) {
                    $params[] = [
                        'ERP_MENU_ID'   => (int) $m['id'],
                        'SEQ'           => (int) $m['seq']
                    ];
                    if(isset($m['children']) && !empty($m['children'])){
                        foreach ($m['children'] as $m2) {
                            if($m['id'] != $m2['parent_id']) continue;

                            $params[] = [
                                'ERP_MENU_ID'   => (int) $m2['id'],
                                'SEQ'           => (int) $m2['seq']
                            ];
                        }
                    }
                }
                $this->db->trans_start();
                $this->menu->update_batch($params);
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                    redirect('menu/sort');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect('menu/sort');
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function _get_menus(){
        $datas  = $this->menu->get_menus()->result_array();
        $menus   = [];
        foreach ($datas as $m) {
            if($m['PARENT_ID'] == 0){
                $m['child'] = [];
                $menus[$m['ERP_MENU_ID']] = $m;
            }else if(isset($menus[$m['PARENT_ID']])){
                $menus[$m['PARENT_ID']]['child'][] = $m;
            }
        }
        return $menus;
    }
}