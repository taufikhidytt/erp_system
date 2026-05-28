<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Karyawan extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Karyawan_model', 'karyawan');
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
        $list = $this->karyawan->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $karyawan) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['nama_depan'] = '
            <a href="' . base_url('karyawan/detail/' . base64url_encode($this->encrypt->encode($karyawan->KARYAWAN_ID))) . '">
                ' . ($karyawan->FIRST_NAME ? $karyawan->FIRST_NAME : '-') . '
            </a>';
            $row['nama_belakang'] = $karyawan->LAST_NAME ? $karyawan->LAST_NAME : '-';
            $row['bagian'] = $karyawan->DEPARTMENT_NAME ? $karyawan->DEPARTMENT_NAME : '-';
            $row['divisi'] = $karyawan->DIVISI_NAME ? $karyawan->DIVISI_NAME : '-';
            $row['kategori'] = $karyawan->KATEGORI_NAME ? $karyawan->KATEGORI_NAME : '-';
            $row['type_cu'] = $karyawan->TYPE_CUST ? numb_format($karyawan->TYPE_CUST) : '-';
            $row['pmc'] = $karyawan->PMC ? numb_format($karyawan->PMC) : '-';
            $row['fcp'] = $karyawan->FCP ? numb_format($karyawan->FCP) : '-';
            $row['pjt'] = $karyawan->PROJ_SERV ? numb_format($karyawan->PROJ_SERV) : '-';
            $row['acs'] = $karyawan->ACS_TOOL ? numb_format($karyawan->ACS_TOOL) : '-';
            $row['gudang'] = $karyawan->WAREHOUSE_NAME ? $karyawan->WAREHOUSE_NAME : '-';
            $row['start_date'] = $karyawan->START_DATE ? date('d-M-Y', strtotime($karyawan->START_DATE)) : '-';
            $row['end_date'] = $karyawan->END_DATE ? date('d-M-Y', strtotime($karyawan->END_DATE)) : '-';
            $row['saldo_awal'] = $karyawan->SALDO_AWAL ? numb_format($karyawan->SALDO_AWAL) : '-';
            $row['active_flag']        = $karyawan->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            $row['description'] = $karyawan->DESCRIPTION ? $karyawan->DESCRIPTION : '-';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->karyawan->count_all(),
            "recordsFiltered" => $this->karyawan->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add()
    {
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == false) {
                $data['title']      = 'Tambah Karyawan';
                $data['breadcrumb'] = 'Tambah Karyawan';
                $this->template->load('template', $this->access['url'] . '/add', $data);
            } else {
                $post = $this->input->post();
                debuging($post);

                $this->db->trans_start();
                $insert_id = $this->karyawan->add($post);
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
        $this->form_validation->set_rules('program_code', 'program code', 'trim');
        if ($this->input->post('program_code') == 'SALES') {
            $this->form_validation->set_rules('gudang_sales', 'gudang sales', 'trim|required');
        }
        $this->form_validation->set_rules('nama_depan', 'nama depan', 'trim|required');
        $this->form_validation->set_rules('nama_belakang', 'nama belakang', 'trim|required');
        $this->form_validation->set_rules('bagian', 'bagian', 'trim|required');
        $this->form_validation->set_rules('divisi', 'divisi', 'trim|required');
        $this->form_validation->set_rules('start_date', 'start date', 'trim|required');
    }

    public function detail($id)
    {
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == FALSE) {
                $decoded_id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->karyawan->getById($decoded_id);
                if ($query->num_rows() > 0) {
                    $data['title']      = 'Detail Karyawan';
                    $data['breadcrumb'] = 'Detail Karyawan';
                    $data['data']       = $query->row();
                    $this->template->load('template', $this->access['url'] . '/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            } else {
                $decoded_id = $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->karyawan->getById($decoded_id);
                $encoded_id = base64url_encode($this->encrypt->encode($decoded_id));

                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->karyawan->update_by_id($decoded_id, $post);

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

    public function get_bagian()
    {
        $result = $this->karyawan->getBagian()->result();
        echo json_encode($result);
    }

    public function get_divisi()
    {
        $result = $this->karyawan->getDivisi()->result();
        echo json_encode($result);
    }

    public function get_kategori()
    {
        $result = $this->karyawan->getKategori()->result();
        echo json_encode($result);
    }

    public function get_gudang()
    {
        $result = $this->karyawan->getGudang()->result();
        echo json_encode($result);
    }
}
