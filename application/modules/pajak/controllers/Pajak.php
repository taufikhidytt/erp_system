<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pajak extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Pajak_model','pajak');
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
        $this->load->model('M_datatables','datatables');
        $params = [
            'table'     => 'ppn a',
            'select'    => ['
                a.PPN_CODE,a.PERCENTAGE,a.TIPE_PAJAK_ID,a.JUAL_COA_ID,a.BELI_COA_ID,a.PRIMARY_FLAG,a.PRIMARY_FLAG,a.PRIMARY_PPH,a.ACTIVE_FLAG,
                b.COA_CODE as KODE_ACCOUNT_PENJUALAN, b.COA_NAME as NAMA_ACCOUNT_PENJUALAN,
                c.COA_CODE as KODE_ACCOUNT_PEMBELIAN, c.COA_NAME as NAMA_ACCOUNT_PEMBELIAN,
                d.DISPLAY_NAME as NAMA_TIPE_PAJAK,
                '
            ],
            'joins' => [
                ['coa b',"b.COA_ID = a.JUAL_COA_ID",'inner'],
                ['coa c','c.COA_ID = a.BELI_COA_ID','inner'],
                ['erp_lookup_value d','d.ERP_LOOKUP_VALUE_ID = a.TIPE_PAJAK_ID','inner']
            ],
            'column_search' => [null, 'a.PPN_CODE','a.PERCENTAGE','d.DISPLAY_NAME','b.COA_CODE','b.COA_NAME','c.COA_CODE','c.COA_NAME','a.PRIMARY_FLAG','a.PRIMARY_PPH','a.ACTIVE_FLAG'],
            'column_order' => [null, 'a.PPN_CODE','a.PERCENTAGE','d.DISPLAY_NAME','b.COA_CODE','b.COA_NAME','c.COA_CODE','c.COA_NAME','a.PRIMARY_FLAG','a.PRIMARY_PPH','a.ACTIVE_FLAG'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id = base64url_encode($this->encrypt->encode($row->PPN_CODE));

            $res = [
                'id' => $id,
                'no' => $no,
                'kode_pajak' => $row->PPN_CODE,
                'persen' => numb_format($row->PERCENTAGE),
                'tipe_pajak' => ['id' => $row->TIPE_PAJAK_ID, 'label' => $row->NAMA_TIPE_PAJAK],
                'kode_account_penjualan' => ['id' => $row->JUAL_COA_ID, 'label' => $row->KODE_ACCOUNT_PENJUALAN],
                'nama_account_penjualan' => ['id' => $row->JUAL_COA_ID, 'label' => $row->NAMA_ACCOUNT_PENJUALAN],
                'kode_account_pembelian' => ['id' => $row->BELI_COA_ID, 'label' => $row->KODE_ACCOUNT_PEMBELIAN],
                'nama_account_pembelian' => ['id' => $row->BELI_COA_ID, 'label' => $row->NAMA_ACCOUNT_PEMBELIAN],
                'ppn' => $row->PRIMARY_FLAG,
                'pph' => $row->PRIMARY_PPH,
                'active_flag' => $row->ACTIVE_FLAG,
            ];
            return $res;
        }));
    }

    public function save()
    {
        try {
            $rows = json_decode($this->input->post('rows'), true);
            if(!$rows){
                sendWarning('Tidak ada data yang dikirim');die;
            }

            $now        = date('Y-m-d H:i:s');
            $user_id    = (int) $this->session->id;
            $arr_insert = [];
            $arr_update = [];

            // pengecekan untuk update
            $ids = array_column(array_filter($rows, fn($r) => empty($r['isNew'])), 'id');
            $ids = array_map(fn($id) => $this->encrypt->decode(base64url_decode($id)), $ids);
            $existing = [];
            if(!empty($ids)){
                $existing = array_column($this->pajak->getById($ids)->result_array(), null, 'PPN_CODE');
            }

            foreach ($rows as $row) {
                $fields = $row['fields'] ?? [];
                $isNew  = !empty($row['isNew']);
                $id     = $isNew ? null : $this->encrypt->decode(base64url_decode($row['id'] ?? ''));

                if ($isNew) {
                    $arr_insert[] = array_filter([
                        'PPN_CODE'     => strtoupper(trim($fields['kode_pajak'] ?? '')),
                        'PERCENTAGE'   => (float)($fields['persen'] ?? null),
                        'TIPE_PAJAK_ID' => (int) ($fields['tipe_pajak'] ?? 0),
                        'JUAL_COA_ID' => (int) ($fields['kode_account_penjualan'] ?? 0),
                        'BELI_COA_ID' => (int) ($fields['kode_account_pembelian'] ?? 0),
                        'PRIMARY_FLAG'  => ($fields['ppn']  ?? null)=='Y'?'Y':'N',
                        'PRIMARY_PPH'  => ($fields['pph']  ?? null)=='Y'?'Y':'N',
                        'ACTIVE_FLAG'  => ($fields['active_flag']  ?? null)=='Y'?'Y':'N',
                        'CREATED_BY'   => $user_id,
                        'CREATED_DATE' => $now,
                    ]);
                } elseif ($id) {
                    if(!isset($existing[$id])){
                        throw new Exception("Pajak tidak ditemukan untuk melakukan update.");
                    }
                    $arr_update[] = [
                        'PPN_CODE' => $id,
                        'PERCENTAGE'   => (float)($fields['persen'] ?? $existing[$id]['PERCENTAGE']),
                        'TIPE_PAJAK_ID'   => (int)($fields['tipe_pajak'] ?? $existing[$id]['TIPE_PAJAK_ID']),
                        'JUAL_COA_ID'   => (int)($fields['kode_account_penjualan'] ?? $existing[$id]['JUAL_COA_ID']),
                        'BELI_COA_ID'   => (int)($fields['kode_account_pembelian'] ?? $existing[$id]['BELI_COA_ID']),
                        'PRIMARY_FLAG'      => ($fields['ppn']  ?? $existing[$id]['PRIMARY_FLAG'])=='Y'?'Y':'N',
                        'PRIMARY_PPH'      => ($fields['pph']  ?? $existing[$id]['PRIMARY_PPH'])=='Y'?'Y':'N',
                        'ACTIVE_FLAG'      => ($fields['active_flag']  ?? $existing[$id]['ACTIVE_FLAG'])=='Y'?'Y':'N',
                        'LAST_UPDATE_BY'   => $user_id,
                        'LAST_UPDATE_DATE' => $now,
                    ];
                }
            }

            if (!empty($arr_update) && !$this->access['update']) throw new Exception('Tidak ada akses edit!');
            if (!empty($arr_insert) && !$this->access['insert']) throw new Exception('Tidak ada akses tambah!');

            //pengecekan data yang dikirim duplikat
            $all_rows       = array_merge($arr_insert, $arr_update);
            $combinations   = array_map(fn($r) => $r['PPN_CODE'], $all_rows);
            if (count($combinations) !== count(array_unique($combinations))) {
                throw new Exception("Terdapat Kode Pajak yang sama dalam data yang dikirim.");
            }
            
            //pengecekan ke database
            $exclude_ids  = array_column($arr_update, 'PPN_CODE');
            $duplicates   = $this->pajak->check_duplicate($combinations, $exclude_ids);
            if (!empty($duplicates)) {
                $detail = implode(', ', array_map(fn($d) => "{$d['PPN_CODE']}", $duplicates));
                throw new Exception("Kode Pajak sudah ada di database: {$detail}.");
            }

            $this->db->trans_start();
            if (!empty($arr_insert)) $this->pajak->insert_batch($arr_insert);
            if (!empty($arr_update)) $this->pajak->update_batch($arr_update);
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