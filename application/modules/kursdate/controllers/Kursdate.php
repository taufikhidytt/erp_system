<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kursdate extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Kursdate_model', 'kursdate');
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
            'table' => 'kurs_detail a',
            'select' => [
                'a.KURS_DETAIL_ID, a.DOCUMENT_DATE, b.MATA_UANG_CODE, b.MATA_UANG_NAME, a.NILAI, a.NOTE, c.ERP_USER_NAME',
            ],
            'joins' => [
                ['mata_uang b', 'a.MATA_UANG_ID = b.MATA_UANG_ID'],
                ['erp_user c', 'a.CREATED_BY = c.ERP_USER_ID', 'left'],
            ],
            'where' => ['b.ACTIVE_FLAG' => 'Y'],
            'column_search' => [null, 'b.MATA_UANG_CODE', 'b.MATA_UANG_NAME', 'a.DOCUMENT_DATE', 'a.NILAI', 'a.NOTE', 'c.ERP_USER_NAME'],
            'column_order'  => [null, 'b.MATA_UANG_CODE', 'b.MATA_UANG_NAME', 'a.DOCUMENT_DATE', 'a.NILAI', 'a.NOTE', 'c.ERP_USER_NAME'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->KURS_DETAIL_ID));
            $formatted_date = $row->DOCUMENT_DATE;
            $res = [
                'no'            => $no,
                'document_date' => $formatted_date,
                'mata_uang_code' => '<a href="' . base_url($this->access['url'] . '/detail/' . $id) . '">' . $row->MATA_UANG_CODE . '</a>',
                'mata_uang_name' => $row->MATA_UANG_NAME,
                'nilai'         => numb_format($row->NILAI),
                'note'          => $row->NOTE,
                'user_name'     => $row->ERP_USER_NAME,
            ];
            return $res;
        }));
    }

    private function _validation_rules()
    {
        $this->form_validation->CI = &$this;
        $this->form_validation->set_rules('nilai', 'Rate', 'trim|required|numeric');
        $this->form_validation->set_rules('note', 'Keterangan', 'trim');
    }

    public function detail($id)
    {
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == FALSE) {
                $decoded_id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->kursdate->getById($decoded_id);
                if ($query->num_rows() > 0) {
                    if ($this->input->post()) {
                        $this->session->set_flashdata('warning', 'Simpan gagal, silakan periksa kembali form Anda.');
                    }

                    $data['title']      = 'Detail Kurs Date';
                    $data['breadcrumb'] = 'Detail Kurs Date';
                    $data['data']       = $query->row();
                    $this->template->load('template', $this->access['url'] . '/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            } else {
                $decoded_id = $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->kursdate->getById($decoded_id);
                $encoded_id = base64url_encode($this->encrypt->encode($decoded_id));

                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->kursdate->update_by_id($decoded_id, $post);

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

    public function sync()
    {
        checkAccess('sync');
        try {
            $data['title']      = 'Sync Kurs BI';
            $data['breadcrumb'] = 'Sync Kurs BI';
            $this->template->load('template', $this->access['url'] . '/sync', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_sync_preview()
    {
        checkAccess('sync');
        try {
            $bi_data      = get_bi_data();
            $bi_rates     = $bi_data['exchange_rates'];
            $current_date = date('Y-m-d');
            $bi_date      = $bi_data['last_update'];

            $mata_uang = $this->kursdate->mata_uang()->result();

            $preview_data = [];
            foreach ($mata_uang as $mu) {
                $code = strtoupper($mu->MATA_UANG_CODE);

                if ($code === 'IDR') {
                    $selling    = 1.0;
                    $middle     = 1.0;
                    $buying     = 1.0;
                } elseif (isset($bi_rates[$code])) {
                    $r          = $bi_rates[$code];
                    $selling    = $r['selling_rate'];
                    $middle     = $r['middle_rate'];
                    $buying     = $r['buying_rate'];
                } else {
                    continue;
                }

                $is_today = $mu->last_date === $current_date;

                $preview_data[] = [
                    'id'   => base64url_encode($this->encrypt->encode($mu->MATA_UANG_ID.'::'.($is_today ? $mu->KURS_DETAIL_ID : 0))),
                    'code' => $code,
                    'name' => $mu->MATA_UANG_NAME,
                    'state'          => $mu->STATE,
                    'current_date'   => $current_date,
                    'bi_date'        => $bi_date,
                    'selling'        => $selling,
                    'middle'         => $middle,
                    'buying'         => $buying,
                    'last_date'      => $mu->last_date,
                    'last_rate'      => $mu->last_rate,
                    'status'         => $is_today ? 'UPDATE' : 'INSERT',
                    'is_match'       => numb_format($middle) == numb_format($mu->last_rate),
                ];
            }

            sendSuccess([
                'data'         => $preview_data,
                'bi_date'      => $bi_date,
                'current_date' => $current_date,
            ],' success get data');

        } catch (Exception $e) {
            sendWarning($e->getMessage());
        }
    }

    public function save_sync()
    {
        checkAccess('sync');
        try {
            $post = $this->input->post();
            if (!isset($post['sync_data']) || !is_array($post['sync_data'])) {
                throw new Exception('Invalid data received.');
            }

            $ids        = [];
            foreach ($post['sync_data'] as $v) {
                $decoded_id = $this->encrypt->decode(base64url_decode($v));
                $x          = explode('::', $decoded_id);
                $ids[]      = (int) $x[0];
            }

            $bi_data      = get_bi_data();
            $bi_rates     = $bi_data['exchange_rates'];
            $current_date = date('Y-m-d');
            $bi_date      = $bi_data['last_update'];
            $user_id      = (int) $this->session->id;
            $now          = date('Y-m-d H:i:s');
            $period_name  = date('Ym');    

            $mata_uang  = $this->kursdate->mata_uang($ids)->result();
            $arr_insert = [];
            $arr_update = [];
            foreach ($mata_uang as $mu) {
                $code = strtoupper($mu->MATA_UANG_CODE);
                if ($code === 'IDR') {
                    $rate   = 1.0;
                } elseif (isset($bi_rates[$code])) {
                    $r      = $bi_rates[$code];
                    $rate   = (float) $r['middle_rate'];
                } else {
                    continue;
                }

                $is_today = $mu->last_date === $current_date;

                if($is_today && $mu->KURS_DETAIL_ID){
                    $arr_update[] = [
                        'KURS_DETAIL_ID' => (int) $mu->KURS_DETAIL_ID,
                        'NILAI'          => $rate,
                        'NOTE'           => 'Auto-generated from bi dated '.$bi_date,
                        'LAST_UPDATE_BY' => $user_id,
                        'LAST_UPDATE_DATE' => $now,
                    ];
                }else{
                    $arr_insert[] = [
                        'NILAI'          => $rate,
                        'NOTE'           => 'Auto-generated from bi dated '.$bi_date,
                        'DOCUMENT_DATE'  => $current_date,
                        'MATA_UANG_ID'   => $mu->MATA_UANG_ID,
                        'PERIOD_NAME'    => $period_name,
                        'CREATED_BY'     => $user_id,
                        'CREATED_DATE'   => $now,
                    ];
                }
            }

            $this->db->trans_start();
            $this->kursdate->insert_batch($arr_insert);
            $this->kursdate->update_batch($arr_update);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database error during sync.');
            }

            sendSuccess(base_url($this->access['url']),'Sync berhasil!');
        } catch (Exception $e) {
            sendWarning($e->getMessage());
        }
    }
}
