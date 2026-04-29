<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Management_menu extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        $this->rules();
        $this->load->model('Management_menu_model','menu');
    }

    private function rules(){
        $id = $this->session->id;
        if(!in_array($id, [1, 5]) || ENVIRONMENT === 'production'){
            $this->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
            redirect('dashboard');
        }
    }

    public function index()
    {
        try {
            $data['title'] = 'Management Menu';
            $data['breadcrumb'] = 'Management Menu';
            $this->template->load('template', 'management_menu/index', $data);
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
            $row = array();
            $row['no']      = $no;
            $row['name']    = $m->PARENT_ID != 0? '<span class="ps-3">'.$m->ERP_MENU_NAME.'</span>' : $m->ERP_MENU_NAME;
            $row['prompt']  = $m->PARENT_ID != 0? '<span class="ps-3">'.$m->PROMPT.'</span>' : $m->PROMPT;
            $row['active_flag'] = $m->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>';

            $row['action'] = $m->PARENT_ID != 0 ? '<a href="'.base_url('management_menu/permissions/'.base64url_encode($this->encrypt->encode($m->ERP_MENU_ID))).'" class="btn btn-success btn-sm"><i class="ri ri-key-fill"></i></a>' : '';
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
                    $this->template->load('template', 'management_menu/permissions', $data);
                }else{
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('management_menu');
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
                    redirect('management_menu/permissions/' . base64url_encode($this->encrypt->encode($id)));
                }else{
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('management_menu');
                }
            }

        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }
}