<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Setting_model', 'setting');
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
            'table' => 'coa_setup a',
            'select' => [
                'a.PROGRAM_ACCOUNT,a.NOTE,a.COA_ID,c.COA_NAME as ACCOUNT',
            ],
            'joins' => [
                ['coa c','c.COA_ID = a.COA_ID','inner']
            ],
            'column_search' => [null, 'a.NOTE', 'c.COA_NAME'],
            'column_order'  => [null, 'a.NOTE', 'c.COA_NAME'],
            'order' => [
                'a.NOTE' => 'ASC'
            ]
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->PROGRAM_ACCOUNT));
            $res = [
                'id' => $id,
                'no' => $no,
                'note' => $row->NOTE,
                'account' => $row->ACCOUNT,
            ];
            return $res;
        }));
    }

    public function save()
    {
        try {
            $rows = json_decode($this->input->post('rows'), true);
            if (!$rows){
                sendWarning('Tidak ada data yang dikirim');die;
            };

            $now     = date('Y-m-d H:i:s');
            $user_id = (int) $this->session->id;
            $arr_update = [];

            //pengecekan untuk update
            $ids      = array_column(array_filter($rows, fn($r) => empty($r['isNew'])), 'id');
            $ids      = array_map(fn($id) => $this->encrypt->decode(base64url_decode($id)), $ids);
            $existing = [];
            if(!empty($ids)){
                $existing = array_column($this->setting->getById($ids)->result_array(), null, 'PROGRAM_ACCOUNT');
            }

            foreach ($rows as $row) {
                $fields = $row['fields'] ?? [];
                $isNew  = !empty($row['isNew']);
                $id     = $isNew ? null : $this->encrypt->decode(base64url_decode($row['id'] ?? ''));

                if ($id) {
                    if(!isset($existing[$id])){
                        throw new Exception("Note '{$fields['note']}' tidak ditemukan untuk melakukan update.");
                    }
                    $arr_update[] = [
                        'PROGRAM_ACCOUNT'  => $id,
                        'COA_ID'           => $fields['account']  ?? $existing[$id]['COA_ID'],
                        'LAST_UPDATE_BY'   => $user_id,
                        'LAST_UPDATE_DATE' => $now,
                    ];
                }
            }

            if (!empty($arr_update) && !$this->access['update']) throw new Exception('Tidak ada akses edit!');

            $this->db->trans_start();
            if (!empty($arr_update)) $this->setting->update_batch($arr_update);
            $this->db->trans_complete();

            $this->db->trans_status() === false
                ? sendWarning('Gagal menyimpan data.')
                : sendSuccess('', 'Data berhasil disimpan!');

        } catch (Exception $err) {
            $this->db->trans_rollback();
            sendWarning($err->getMessage());
        }
    }

    public function get_account()
    {
        $result = $this->setting->get_account()->result();
        echo json_encode($result);
    }
}
