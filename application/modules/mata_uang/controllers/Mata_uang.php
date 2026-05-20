<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mata_uang extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Mata_uang_model', 'mata_uang');
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
            'table' => 'mata_uang a',
            'select' => [
                'a.MATA_UANG_ID,a.MATA_UANG_CODE,a.MATA_UANG_NAME,a.SALDO_AWAL,a.STATE,a.SYMBOL,a.PRIMARY_FLAG,a.ACTIVE_FLAG',
            ],
            'column_search' => [null, 'a.MATA_UANG_CODE', 'a.MATA_UANG_NAME', 'a.STATE', 'a.SALDO_AWAL', 'a.SYMBOL', 'a.PRIMARY_FLAG', 'a.ACTIVE_FLAG'],
            'column_order'  => [null, 'a.MATA_UANG_CODE', 'a.MATA_UANG_NAME', 'a.STATE', 'a.SALDO_AWAL', 'a.SYMBOL', 'a.PRIMARY_FLAG', 'a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->MATA_UANG_ID));
            $res = [
                'no' => $no,
                'code' => '<a href="' . base_url($this->access['url'] . '/detail/' . $id) . '">' . $row->MATA_UANG_CODE . '</a>',
                'name' => $row->MATA_UANG_NAME,
                'saldo_awal' => numb_format($row->SALDO_AWAL),
                'state' => $row->STATE,
                'symbol' => $row->SYMBOL,
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
                $data['title']      = 'Tambah Mata Uang';
                $data['breadcrumb'] = 'Tambah Mata Uang';
                $this->template->load('template', $this->access['url'] . '/add', $data);
            } else {
                $post = $this->input->post();

                $this->db->trans_start();
                $insert_id = $this->mata_uang->add($post);
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
        $this->form_validation->set_rules('mata_uang_code', 'Kode', 'trim|required|callback__check_unique_mata_uang_code');
        $this->form_validation->set_rules('mata_uang_name', 'Nama', 'trim|required|callback__check_unique_mata_uang_name');
        $this->form_validation->set_rules('saldo_awal', 'Kurs Awal', 'trim|numeric|required');
        $this->form_validation->set_rules('state', 'Negara', 'trim|required');
    }

    public function _check_unique_mata_uang_code($str)
    {
        $id     = $this->input->post('id');
        $where  = ['MATA_UANG_CODE' => $str];
        if ($id) {
            $decoded_id = (int) $this->encrypt->decode(base64url_decode($id));
            $where['MATA_UANG_ID !='] = $decoded_id;
        }
        $query = $this->db->get_where('mata_uang', $where);
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_check_unique_mata_uang_code', 'The {field} field must contain a unique value.');
            return FALSE;
        }
        return TRUE;
    }

    public function _check_unique_mata_uang_name($str)
    {
        $id = $this->input->post('id');
        $where  = ['MATA_UANG_NAME' => $str];
        if ($id) {
            $decoded_id = (int) $this->encrypt->decode(base64url_decode($id));
            $where['MATA_UANG_ID !='] = $decoded_id;
        }
        $query = $this->db->get_where('mata_uang', $where);
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_check_unique_mata_uang_name', 'The {field} field must contain a unique value.');
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
                $query = $this->mata_uang->getById($decoded_id);
                if ($query->num_rows() > 0) {
                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Detail Mata Uang';
                    $data['breadcrumb'] = 'Detail Mata Uang';
                    $data['data']       = $query->row();
                    $this->template->load('template', $this->access['url'] . '/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            } else {
                $decoded_id = $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->mata_uang->getById($decoded_id);
                $encoded_id = base64url_encode($this->encrypt->encode($decoded_id));
                
                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->mata_uang->update_by_id($decoded_id, $post);

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

    public function get_sync_preview()
    {
        checkAccess('sync');
        try {
            $currencies   = get_currencies();
            $bi_data      = get_bi_data();
            $bi_rates     = $bi_data['exchange_rates'];

            $ls_mata_uang = $this->mata_uang->get_mata_uang()->result_array();
            $mata_uang = array_column($ls_mata_uang,'MATA_UANG_CODE');

            if(in_array('IDR',$mata_uang)){
                $bi_rates['IDR'] = [
                    'code'          => 'IDR',
                    'selling_rate'  => 1,
                    'middle_rate'   => 1,
                    'buying_rate'   => 1,
                ];
            }

            $preview_data = [];
            foreach ($bi_rates as $code => $v) {
                $param = [
                    'code'      => $code,
                    'name'      => $code,
                    'symbol'    => '',
                    'selling'   => (float) $v['selling_rate'],
                    'middle'    => (float) $v['middle_rate'],
                    'buying'    => (float) $v['buying_rate'],
                    'state'     => '',
                    'status'    => 'baru'
                ];
                if(in_array($code,$mata_uang)){
                    $param['status'] = 'perbarui';
                }
                if(isset($currencies[$code])){
                    $dt = $currencies[$code];
                    $param['state']     = $dt['name'];
                    $param['symbol']    = $dt['symbol'];
                }
                $preview_data[] = $param;
            }

            sendSuccess($preview_data,'ok');

        } catch (Exception $e) {
            sendWarning($e->getMessage());
        }
    }

    public function save_sync(){
        checkAccess('sync');
        try {
            $currencies   = get_currencies();
            $bi_data      = get_bi_data();
            $bi_rates     = $bi_data['exchange_rates'];

            $mata_uang = $this->mata_uang->get_mata_uang()->result_array();
            $mata_uang = array_column($mata_uang,'MATA_UANG_CODE');

            if(in_array('IDR',$mata_uang)){
                $bi_rates['IDR'] = [
                    'code'          => 'IDR',
                    'selling_rate'  => 1,
                    'middle_rate'   => 1,
                    'buying_rate'   => 1,
                ];
            }

            $insert_data = [];
            $update_data = [];
            $now         = date('Y-m-d H:i:s');
            $user_id     = (int) $this->session->id;
            foreach ($bi_rates as $code => $v) {
                $param = [
                    'MATA_UANG_CODE' => strtoupper($code),
                    'STATE'     => '',
                    'SYMBOL'    => ''
                ];
                if(isset($currencies[$code])){
                    $dt = $currencies[$code];
                    $param['STATE']     = $dt['name'];
                    $param['SYMBOL']    = $dt['symbol'];
                }

                if(in_array($code,$mata_uang)){
                    $param['LAST_UPDATE_BY']    = $user_id;
                    $param['LAST_UPDATE_DATE']  = $now;
                    $update_data[] = $param;
                }else{
                    $param['MATA_UANG_NAME']= strtoupper($code);
                    $param['SALDO_AWAL']    = (float) $v['middle_rate'];
                    $param['CREATED_BY']    = $user_id;
                    $param['CREATED_DATE']  = $now;
                    $param['ACTIVE_FLAG']   = 'Y';
                    $insert_data[] = $param;
                }
            }

            $this->db->trans_start();
            $this->mata_uang->insert_batch($insert_data);
            $this->mata_uang->update_batch($update_data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database error during sync.');
            }

            sendSuccess('','Selamat anda berhasil menyimpan data!');

        } catch (Exception $e) {
            sendWarning($e->getMessage());
        }
    }
}