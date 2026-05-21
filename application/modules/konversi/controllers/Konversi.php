<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Konversi extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Konversi_model', 'konversi');
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
            'table' => 'item_convertion a',
            'select' => [
                'a.ITEM_CONVERTION_ID,a.FROM_UOM,a.TO_UOM,a.TO_QTY,a.ACTIVE_FLAG',
            ],
            'where' => [
                'a.ITEM_ID' => null
            ],
            'column_search' => [null, 'a.FROM_UOM', 'a.TO_UOM', 'a.TO_QTY', 'a.ACTIVE_FLAG'],
            'column_order'  => [null, 'a.FROM_UOM', 'a.TO_UOM', 'a.TO_QTY', 'a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->ITEM_CONVERTION_ID));
            $res = [
                'id' => $id,
                'no' => $no,
                'from_uom' => ['id' => $row->FROM_UOM, 'label' => $row->FROM_UOM],
                'to_uom' => ['id' => $row->TO_UOM, 'label' => $row->TO_UOM],
                'to_qty' => numb_format($row->TO_QTY),
                'active_flag' => $row->ACTIVE_FLAG,
                'keterangan' => numb_format(1).' '.$row->FROM_UOM.' = '.numb_format($row->TO_QTY).' '.$row->TO_UOM
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

            //pengecekan untuk update
            $ids      = array_column(array_filter($rows, fn($r) => empty($r['isNew'])), 'id');
            $ids      = array_map(fn($id) => $this->encrypt->decode(base64url_decode($id)), $ids);
            $existing = [];
            if(!empty($ids)){
                $existing = array_column($this->konversi->getById($ids)->result_array(), null, 'ITEM_CONVERTION_ID');
            }

            foreach ($rows as $row) {
                $fields = $row['fields'] ?? [];
                $isNew  = !empty($row['isNew']);
                $id     = $isNew ? null : $this->encrypt->decode(base64url_decode($row['id'] ?? ''));

                if ($isNew) {
                    $arr_insert[] = array_filter([
                        'ITEM_ID'      => null,
                        'FROM_UOM'     => strtoupper(trim($fields['from_uom'] ?? '')),
                        'TO_UOM'       => strtoupper(trim($fields['to_uom'] ?? '')),
                        'TO_QTY'       => (float)($fields['to_qty'] ?? null),
                        'ACTIVE_FLAG'  => ($fields['active_flag']  ?? null)=='Y'?'Y':'N',
                        'CREATED_BY'   => $user_id,
                        'CREATED_DATE' => $now,
                    ]);
                } elseif ($id) {
                    if(!isset($existing[$id])){
                        throw new Exception("Konversi tidak ditemukan untuk melakukan update.");
                    }
                    $arr_update[] = [
                        'ITEM_CONVERTION_ID' => $id,
                        'FROM_UOM'         => strtoupper(trim($fields['from_uom'] ?? $existing[$id]['FROM_UOM'])),
                        'TO_UOM'           => strtoupper(trim($fields['to_uom'] ?? $existing[$id]['TO_UOM'])),
                        'TO_QTY'           => (float)($fields['to_qty'] ?? $existing[$id]['TO_QTY']),
                        'ACTIVE_FLAG'      => ($fields['active_flag']  ?? $existing[$id]['ACTIVE_FLAG'])=='Y'?'Y':'N',
                        'LAST_UPDATE_BY'   => $user_id,
                        'LAST_UPDATE_DATE' => $now,
                    ];
                }
            }

            if (!empty($arr_update) && !$this->access['update']) throw new Exception('Tidak ada akses edit!');
            if (!empty($arr_insert) && !$this->access['insert']) throw new Exception('Tidak ada akses tambah!');

            //pengecekan from dan to uom nya sama
            $all_rows = array_merge($arr_insert, $arr_update);
            foreach ($all_rows as $r) {
                if ($r['FROM_UOM'] === $r['TO_UOM']) {
                    throw new Exception("Satuan Lain dan Satuan Utama tidak boleh sama ({$r['FROM_UOM']}).");
                }
            }

            //pengecekan data yang dikirim duplikat
            $combinations = array_map(fn($r) => $r['FROM_UOM'] . '|' . $r['TO_UOM'], $all_rows);
            if (count($combinations) !== count(array_unique($combinations))) {
                throw new Exception("Terdapat kombinasi Satuan Lain dan Satuan Utama yang sama dalam data yang dikirim.");
            }

            $combo_check  = array_map(fn($r) => ['from' => $r['FROM_UOM'], 'to' => $r['TO_UOM']], $all_rows);
            $exclude_ids  = array_column($arr_update, 'ITEM_CONVERTION_ID');
            $duplicates   = $this->konversi->check_duplicate($combo_check, $exclude_ids);
            if (!empty($duplicates)) {
                $detail = implode(', ', array_map(fn($d) => "{$d['FROM_UOM']} ke {$d['TO_UOM']}", $duplicates));
                throw new Exception("Kombinasi konversi sudah ada di database: {$detail}.");
            }

            $this->db->trans_start();
            if (!empty($arr_insert)) $this->konversi->insert_batch($arr_insert);
            if (!empty($arr_update)) $this->konversi->update_batch($arr_update);
            $this->db->trans_complete();

            $this->db->trans_status() === false
                ? sendWarning('Gagal menyimpan data.')
                : sendSuccess('', 'Data berhasil disimpan!');

        } catch (Exception $err) {
            $this->db->trans_rollback();
            sendWarning($err->getMessage());
        }
    }
}
