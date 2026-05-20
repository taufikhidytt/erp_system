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
            $this->template->load('template', $this->access['url'] . '/index', $data);
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
                'a.UOM_CODE,a.DESCRIPTION,a.BASE_UOM_FLAG,a.PRIMARY_FLAG,a.ACTIVE_FLAG',
            ],
            'column_search' => [null, 'a.UOM_CODE', 'a.DESCRIPTION', 'a.PRIMARY_FLAG', 'a.ACTIVE_FLAG'],
            'column_order'  => [null, 'a.UOM_CODE', 'a.DESCRIPTION', 'a.PRIMARY_FLAG', 'a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->UOM_CODE));
            $res = [
                'id' => $id,
                'no' => $no,
                'name' => $row->UOM_CODE,
                'description' => $row->DESCRIPTION,
                'primary_flag' => $row->PRIMARY_FLAG,
                'active_flag' => $row->ACTIVE_FLAG,
            ];
            return $res;
        }));
    }

    public function save()
    {
        try {
            $rows = json_decode($this->input->post('rows'), true);
            if (!$rows){
                sendWarning('No data received');die;
            };

            $now     = date('Y-m-d H:i:s');
            $user_id = (int) $this->session->id;
            $arr_insert = [];
            $arr_update = [];

            //pengecekan untuk insert
            $new_codes = array_column(array_filter($rows, fn($r) => !empty($r['isNew'])), 'fields');
            $new_codes = array_map(fn($f) => strtoupper(trim($f['name'] ?? '')), $new_codes);
            $new_codes = array_filter($new_codes);
            if(!empty($new_codes)){
                $duplicate_codes = $this->uom->get_duplicate_codes($new_codes);
                if ($duplicate_codes) {
                    $list = implode(', ', array_column($duplicate_codes, 'UOM_CODE'));
                    throw new Exception("Satuan sudah ada: {$list}");
                }
            }

            //pengecekan untuk update
            $ids      = array_column(array_filter($rows, fn($r) => empty($r['isNew'])), 'id');
            $ids      = array_map(fn($id) => $this->encrypt->decode(base64url_decode($id)), $ids);
            $existing = [];
            if(!empty($ids)){
                $existing = array_column($this->uom->getById($ids)->result_array(), null, 'UOM_CODE');
            }

            foreach ($rows as $row) {
                $fields = $row['fields'] ?? [];
                $isNew  = !empty($row['isNew']);
                $id     = $isNew ? null : $this->encrypt->decode(base64url_decode($row['id'] ?? ''));

                if ($isNew) {
                    $code = strtoupper(trim($fields['name'] ?? ''));
                    $arr_insert[] = array_filter([
                        'UOM_CODE'     => $code,
                        'DESCRIPTION'  => $fields['description']  ?? null,
                        'PRIMARY_FLAG' => $fields['primary_flag'] ?? null,
                        'ACTIVE_FLAG'  => $fields['active_flag']  ?? null,
                        'CREATED_BY'   => $user_id,
                        'CREATED_DATE' => $now,
                    ]);
                } elseif ($id) {
                    if(!isset($existing[$id])){
                        throw new Exception("Satuan '{$id}' tidak ditemukan untuk melakukan update.");
                    }
                    $arr_update[] = [
                        'UOM_CODE'         => $id,
                        'DESCRIPTION'      => $fields['description']  ?? $existing[$id]['DESCRIPTION'],
                        'PRIMARY_FLAG'     => $fields['primary_flag'] ?? $existing[$id]['PRIMARY_FLAG'],
                        'ACTIVE_FLAG'      => $fields['active_flag']  ?? $existing[$id]['ACTIVE_FLAG'],
                        'LAST_UPDATE_BY'   => $user_id,
                        'LAST_UPDATE_DATE' => $now,
                    ];
                }
            }

            if (!empty($arr_update) && !$this->access['update']) throw new Exception('Tidak ada akses edit!');
            if (!empty($arr_insert) && !$this->access['insert']) throw new Exception('Tidak ada akses tambah!');

            $this->db->trans_start();
            if (!empty($arr_insert)) $this->uom->insert_batch($arr_insert);
            if (!empty($arr_update)) $this->uom->update_batch($arr_update);
            $this->db->trans_complete();

            $this->db->trans_status() === false
                ? sendWarning('Gagal menyimpan data.')
                : sendSuccess('', 'Data berhasil disimpan!');

        } catch (Exception $err) {
            $this->db->trans_rollback();
            sendWarning($err->getMessage());
        }
    }

    public function add()
    {
        $this->session->set_flashdata('warning', 'Halaman yang dituju tidak tersedia');
        redirect($this->access['url']);
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == false) {
                $data['title']      = 'Tambah UOM';
                $data['breadcrumb'] = 'Tambah UOM';
                $this->template->load('template', $this->access['url'] . '/add', $data);
            } else {
                $post = $this->input->post();

                $this->db->trans_start();
                $uom_code = $this->uom->add($post);
                $this->db->trans_complete();

                $encoded_id = base64url_encode($this->encrypt->encode($uom_code));

                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!' . $error_msg);
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

    public function _check_unique_uom_code($str)
    {
        $id = $this->input->post('id');
        $this->db->where('UOM_CODE', $str);
        if ($id) {
            $decoded_id = $this->encrypt->decode(base64url_decode($id));
            $this->db->where('UOM_CODE !=', $decoded_id);
        }
        $query = $this->db->get('uom');
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_check_unique_uom_code', 'The {field} field must contain a unique value.');
            return FALSE;
        }
        return TRUE;
    }

    private function _validation_rules()
    {
        $this->form_validation->CI = &$this;
        if (!$this->input->post('id')) {
            $this->form_validation->set_rules('uom_code', 'UOM Code', 'trim|required|callback__check_unique_uom_code');
        }
        $this->form_validation->set_rules('description', 'Description', 'trim|required|max_length[80]');
    }

    public function detail($id)
    {
        $this->session->set_flashdata('warning', 'Halaman yang dituju tidak tersedia');
        redirect($this->access['url']);
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
                    $this->template->load('template', $this->access['url'] . '/detail', $data);
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
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!' . $error_msg);
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
