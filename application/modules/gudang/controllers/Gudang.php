<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gudang extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Gudang_model', 'gudang');
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
            'table' => 'warehouse a',
            'select' => [
                'a.WAREHOUSE_ID,a.WAREHOUSE_NAME,a.DESCRIPTION,a.SALES_FLAG,a.PRIMARY_FLAG,a.ACTIVE_FLAG,
                b.DISPLAY_NAME JENIS,
                c.ADDRESS_CODE, c.ADDRESS1, c.ADDRESS2, c.CITY, c.PROVINCE, c.COUNTRY,
                ',
            ],
            'joins' => [
                ['erp_lookup_value b', "b.ERP_LOOKUP_VALUE_ID = a.JENIS_ID AND b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET('JENIS_GUDANG')", 'left'],
                ['address c', "c.ADDRESS_ID = a.ADDRESS_ID", 'left'],
            ],
            'column_search' => [null, 'a.WAREHOUSE_NAME', 'a.DESCRIPTION', 'b.DISPLAY_NAME', 'c.ADDRESS_CODE', "CONCAT(c.ADDRESS1,c.ADDRESS2,c.CITY, c.PROVINCE, c.COUNTRY)", 'a.SALES_FLAG', 'a.PRIMARY_FLAG', 'a.ACTIVE_FLAG'],
            'column_order'  => [null, 'a.WAREHOUSE_NAME', 'a.DESCRIPTION', 'b.DISPLAY_NAME', 'c.ADDRESS_CODE', 'c.ADDRESS1', 'a.SALES_FLAG', 'a.PRIMARY_FLAG', 'a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->WAREHOUSE_ID));

            $address = '';
            $n = 0;
            foreach (['ADDRESS1', 'ADDRESS2', 'CITY', 'PROVINCE', 'COUNTRY'] as $k => $v) {
                if ($row->{$v}) {
                    $address .= ($n != 0 ? '</br>' : '') . $row->{$v};
                    $n++;
                }
            }

            $res = [
                'no' => $no,
                'name' => '<a href="' . base_url($this->access['url'] . '/detail/' . $id) . '">' . $row->WAREHOUSE_NAME . '</a>',
                'description' => $row->DESCRIPTION,
                'jenis' => $row->JENIS,
                'lokasi' => $row->ADDRESS_CODE,
                'alamat' => $address,
                'sales_flag' => $row->SALES_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
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
                $data['title']      = 'Tambah Gudang';
                $data['breadcrumb'] = 'Tambah Gudang';
                $this->template->load('template', $this->access['url'] . '/add', $data);
            } else {
                $post = $this->input->post();

                $this->db->trans_start();
                $insert_id = $this->gudang->add($post);
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
            $this->form_validation->set_rules('warehouse_name', 'Nama Gudang', 'trim|required|callback__check_unique_warehouse_name');
        }
        $this->form_validation->set_rules('description', 'Description', 'trim|required');
        $this->form_validation->set_rules('jenis_id', 'Jenis Gundag', 'trim|required');
        $this->form_validation->set_rules('address_id', 'Lokasi', 'trim|required');
    }

    public function _check_unique_warehouse_name($str)
    {
        $where = ['WAREHOUSE_NAME' => $str];
        if ($this->input->post('id')) {
            $decoded_id = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
            $where['WAREHOUSE_ID !='] = $decoded_id;
        }
        $query = $this->db->get_where('warehouse', $where);
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_check_unique_warehouse_name', 'The {field} field must contain a unique value.');
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
                $query = $this->gudang->getById($decoded_id);
                if ($query->num_rows() > 0) {
                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Detail Gudang';
                    $data['breadcrumb'] = 'Detail Gudang';
                    $data['data']       = $query->row();
                    $this->template->load('template', $this->access['url'] . '/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            } else {
                $decoded_id = $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->gudang->getById($decoded_id);
                $encoded_id = base64url_encode($this->encrypt->encode($decoded_id));

                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->gudang->update_by_id($decoded_id, $post);

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

    public function get_address()
    {
        $result = $this->gudang->getAddress()->result();
        echo json_encode($result);
    }

    public function get_jenis()
    {
        $result = $this->gudang->getJenis()->result();
        echo json_encode($result);
    }
}
