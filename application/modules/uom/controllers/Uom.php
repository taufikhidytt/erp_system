<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Uom extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Uom_model', 'uom');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $this->template->load('template', $this->access['url'].'/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'uom a',
            'select' => [
                'a.UOM_CODE,a.DESCRIPTION,a.BASE_UOM_FLAG,a.PRIMARY_FLAG,a.ACTIVE_FLAG,',
            ],
            'column_search' => [null,'a.UOM_CODE','a.DESCRIPTION', 'a.BASE_UOM_FLAG', 'a.PRIMARY_FLAG','a.ACTIVE_FLAG'],
            'column_order'  => [null,'a.UOM_CODE','a.DESCRIPTION', 'a.BASE_UOM_FLAG', 'a.PRIMARY_FLAG','a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->UOM_CODE));
            $res = [
                'no' => $no,
                'name' => '<a href="'.base_url('uom/detail/'.$id).'">'.$row->UOM_CODE.'</a>',
                'description' => $row->DESCRIPTION,
                'base_flag' => $row->BASE_UOM_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
                'primary_flag' => $row->PRIMARY_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
                'active_flag' => $row->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
            ];
            return $res;
        }));
    }

    public function add()
    {
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == false) {
                $data['title']      = 'Tambah UOM';
                $data['breadcrumb'] = 'Tambah UOM';
                $this->template->load('template', $this->access['url'].'/add', $data);
            } else {
                $post = $this->input->post();

                $this->db->trans_start();
                $uom_code = $this->uom->add($post);
                $this->db->trans_complete();

                $encoded_id = base64url_encode($this->encrypt->encode($uom_code));

                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                    redirect('uom/add');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect('uom/detail/' . $encoded_id);
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function _validation_rules()
    {
        $this->form_validation->CI = &$this;
        if (!$this->input->post('id')) {
            $this->form_validation->set_rules('uom_code', 'UOM Code', 'trim|required|is_unique[uom.UOM_CODE]');
        }
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
    }

    public function detail($id)
    {
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == FALSE) {
                $decoded_id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->uom->getById($decoded_id);
                if ($query->num_rows() > 0) {
                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Detail UOM';
                    $data['breadcrumb'] = 'Detail UOM';
                    $data['data']       = $query->row();
                    $this->template->load('template', $this->access['url'].'/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('uom');
                }
            } else {
                $decoded_id = $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->uom->getById($decoded_id);
                $encoded_id = base64url_encode($this->encrypt->encode($decoded_id));
                
                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->uom->update_by_id($decoded_id, $post);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $db_error = $this->db->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect('uom/detail/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect('uom/detail/' . $encoded_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('uom');
                }
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }
}