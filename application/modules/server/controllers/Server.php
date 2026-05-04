<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Server extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Server_model', 'server');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $this->template->load('template', 'server/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->server->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $s) {
            $no++;
            $row = array();
            $row['no']      = $no;
            $row['name']    = '<a href="'.base_url('server/detail/'.base64url_encode($this->encrypt->encode($s->SERVER_ID))).'">'.$s->DB_NAME.'</a>';
            $row['alias']   = $s->DB_ALIAS;
            $row['hostname']= $s->HOSTNAME;
            $row['port']    = $s->PORT;
            $row['active_flag'] = $s->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            $data[]     = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->server->count_all(),
            "recordsFiltered" => $this->server->count_filtered(),
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
                $this->template->load('template', 'server/add', $data);
            } else {
                $db_srv = $this->server->db_srv();
                $db_srv->trans_start();
                $post = $this->input->post();
                $params = [
                    'DB_NAME'       => $post['name'] ? htmlspecialchars($post['name']) : null,
                    'DB_ALIAS'      => $post['alias'] ? htmlspecialchars($post['alias']) : null,
                    'HOSTNAME'      => $post['hostname'] ? htmlspecialchars($post['hostname']) : null,
                    'PORT'          => $post['port'] ? htmlspecialchars($post['port']) : null,
                    'ACTIVE_FLAG'   => $post['active_flag'] ? ($post['active_flag']=='on'?'Y':'N') : 'N',
                    'CREATED_DATE'  => date('Y-m-d H:i:s'),
                ];
                $server_id      = $this->server->insert_data('servers',$params);
                $encoded_id     = base64url_encode($this->encrypt->encode($server_id));

                $db_srv->trans_complete();
                if ($db_srv->trans_status() === FALSE) {
                    $db_error = $db_srv->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                    redirect('server/add');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect('server/detail/' . $encoded_id);
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function _validation(){
        // untuk fungsi validation callback HMVC
        $this->form_validation->CI = &$this;

        $this->form_validation->set_rules('name', 'DB Name', 'trim|required');
        $this->form_validation->set_rules('hostname', 'Hostname', 'trim|required');
        $this->form_validation->set_rules('port', 'Port', 'trim|required');
    }

    public function detail($id)
    {
        try {
            $this->_validation();

            $this->form_validation->set_rules('id', 'ID', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->server->get_data('servers', ['SERVER_ID' => $id]);
                if ($query->num_rows() > 0) {
                    $data['title']      = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data']       = $query->row();
                    $this->template->load('template', 'server/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('server');
                }
            } else {
                $id    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->server->get_data('servers', ['SERVER_ID' => $id]);
                $encoded_id = base64url_encode($this->encrypt->encode($id));
                if ($query->num_rows() > 0) {
                    $db_srv = $this->server->db_srv();
                    $db_srv->trans_start();
                    $post = $this->input->post();
                    $params = [
                        'DB_NAME'       => $post['name'] ? htmlspecialchars($post['name']) : null,
                        'DB_ALIAS'      => $post['alias'] ? htmlspecialchars($post['alias']) : null,
                        'HOSTNAME'      => $post['hostname'] ? htmlspecialchars($post['hostname']) : null,
                        'PORT'          => $post['port'] ? htmlspecialchars($post['port']) : null,
                        'ACTIVE_FLAG'   => $post['active_flag'] ? ($post['active_flag']=='on'?'Y':'N') : 'N',
                        'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                    ];
                    
                    $this->server->update_data('servers',$params,['SERVER_ID' => $id]);

                    $db_srv->trans_complete();
                    if ($db_srv->trans_status() === FALSE) {
                        $db_error = $db_srv->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('server/detail/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('server/detail/' . $encoded_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('server');
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }
}