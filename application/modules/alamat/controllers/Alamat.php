<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Alamat extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Alamat_model', 'alamat');
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
            'table' => 'address a',
            'select' => [
                'a.ADDRESS_ID,a.ADDRESS_CODE,a.ADDRESS1,a.ADDRESS2,a.ADDRESS3,a.SHIP_FLAG,a.ACTIVE_FLAG,a.CITY,a.PROVINCE,a.COUNTRY,a.PHONE,a.FAX',
            ],
            'column_search' => [null,'a.ADDRESS_CODE','a.ADDRESS1', 'a.ADDRESS2', 'a.CITY','a.PROVINCE','a.COUNTRY','a.PHONE','a.FAX','a.SHIP_FLAG','a.ACTIVE_FLAG'],
            'column_order'  => [null,'a.ADDRESS_CODE','a.ADDRESS1', 'a.ADDRESS2', 'a.CITY','a.PROVINCE','a.COUNTRY','a.PHONE','a.FAX','a.SHIP_FLAG','a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->ADDRESS_ID));
            $res = [
                'no' => $no,
                'kode' => '<a href="'.base_url($this->access['url'].'/detail/'.$id).'">'.$row->ADDRESS_CODE.'</a>',
                'address1' => $row->ADDRESS1,
                'address2' => $row->ADDRESS2,
                'kota' => $row->CITY,
                'provinsi' => $row->PROVINCE,
                'negara' => $row->COUNTRY,
                'no_telp' => $row->PHONE,
                'email' => $row->FAX,
                'bisa_kirim' => $row->SHIP_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
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
                $data['title']      = 'Tambah Alamat';
                $data['breadcrumb'] = 'Tambah Alamat';
                $this->template->load('template', $this->access['url'].'/add', $data);
            } else {
                $post = $this->input->post();

                $this->db->trans_start();
                $insert_id = $this->alamat->add($post);
                $this->db->trans_complete();

                $encoded_id = base64url_encode($this->encrypt->encode($insert_id));

                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                    redirect($this->access['url'].'/add');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect($this->access['url'].'/detail/' . $encoded_id);
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
            $this->form_validation->set_rules('address_code', 'Address Code', 'trim|required|callback__check_unique_address_code');
        }
        $this->form_validation->set_rules('address1', 'Address 1', 'trim|required');
    }

    public function _check_unique_address_code($str)
    {
        $where = ['ADDRESS_CODE' => $str];
        if ($this->input->post('id')) {
            $decoded_id = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
            $where['ADDRESS_ID !='] = $decoded_id;
        }
        $query = $this->db->get_where('address', $where);
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_check_unique_address_code', 'The {field} field must contain a unique value.');
            return FALSE;
        }
        return TRUE;
    }

    public function detail($id)
    {
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == FALSE) {
                $decoded_id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->alamat->getById($decoded_id);
                if ($query->num_rows() > 0) {
                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Detail Alamat';
                    $data['breadcrumb'] = 'Detail Alamat';
                    $data['data']       = $query->row();
                    $this->template->load('template', $this->access['url'].'/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            } else {
                $decoded_id = $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->alamat->getById($decoded_id);
                $encoded_id = base64url_encode($this->encrypt->encode($decoded_id));
                
                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->alamat->update_by_id($decoded_id, $post);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $db_error = $this->db->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!'. $error_msg);
                        redirect($this->access['url'].'/detail/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect($this->access['url'].'/detail/' . $encoded_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }
}