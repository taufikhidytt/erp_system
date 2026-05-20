<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jangka extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Jangka_model', 'jangka');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $this->template->load('template', $this->access['url'] . '/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'payment_term a',
            'select' => [
                'a.PAYMENT_TERM_ID,a.PAYMENT_TERM_NAME,a.DESCRIPTION,a.NUMBER_DAYS,a.PRIMARY_FLAG,a.ACTIVE_FLAG',
            ],
            'column_search' => [null, 'a.PAYMENT_TERM_NAME', 'a.DESCRIPTION', 'a.NUMBER_DAYS', 'a.PRIMARY_FLAG', 'a.ACTIVE_FLAG'],
            'column_order'  => [null, 'a.PAYMENT_TERM_NAME', 'a.DESCRIPTION', 'a.NUMBER_DAYS', 'a.PRIMARY_FLAG', 'a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->PAYMENT_TERM_ID));
            $res = [
                'no' => $no,
                'name' => '<a href="' . base_url($this->access['url'] . '/detail/' . $id) . '">' . $row->PAYMENT_TERM_NAME . '</a>',
                'description' => $row->DESCRIPTION,
                'number_days' => $row->NUMBER_DAYS . ' hari',
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
                $data['title']      = 'Tambah Jangka';
                $data['breadcrumb'] = 'Tambah Jangka';
                $this->template->load('template', $this->access['url'] . '/add', $data);
            } else {
                $post = $this->input->post();

                $this->db->trans_start();
                $insert_id = $this->jangka->add($post);
                $this->db->trans_complete();

                $encoded_id = base64url_encode($this->encrypt->encode($insert_id));

                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!' . $error_msg);
                    redirect($this->access['url'] . '/add');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect($this->access['url'] . '/detail/' . $encoded_id);
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
            $this->form_validation->set_rules('payment_term_name', 'Nama / Kode', 'trim|required|callback__check_unique_payment_term_name');
        }
        $this->form_validation->set_rules('description', 'Keterangan', 'trim|required');
        $this->form_validation->set_rules('number_days', 'Jangka Waktu', 'required|numeric|callback__check_unique_number_days');
    }

    public function _check_unique_payment_term_name($str)
    {
        $where = ['PAYMENT_TERM_NAME' => $str];
        if ($this->input->post('id')) {
            $decoded_id = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
            $where['PAYMENT_TERM_ID !='] = $decoded_id;
        }
        $query = $this->db->get_where('payment_term', $where);
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_check_unique_payment_term_name', 'The {field} field must contain a unique value.');
            return FALSE;
        }
        return TRUE;
    }
    
    public function _check_unique_number_days($str)
    {
        $where = ['NUMBER_DAYS' => (int) $str];
        if ($this->input->post('id')) {
            $decoded_id = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
            $where['PAYMENT_TERM_ID !='] = $decoded_id;
        }
        $query = $this->db->get_where('payment_term', $where);
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_check_unique_number_days', 'The {field} field must contain a unique value.');
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
                $query = $this->jangka->getById($decoded_id);
                if ($query->num_rows() > 0) {
                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data']       = $query->row();
                    $this->template->load('template', $this->access['url'] . '/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            } else {
                $decoded_id = $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->jangka->getById($decoded_id);
                $encoded_id = base64url_encode($this->encrypt->encode($decoded_id));

                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->jangka->update_by_id($decoded_id, $post);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $db_error = $this->db->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!' . $error_msg);
                        redirect($this->access['url'] . '/detail/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect($this->access['url'] . '/detail/' . $encoded_id);
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
