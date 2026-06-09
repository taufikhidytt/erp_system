<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gl_account extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Gl_account_model','gl_account');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $data['account']    = $this->get_account();
            $data['mata_uang']  = $this->gl_account->get_mata_uang_default()->row();
            $this->template->load('template', $this->access['url'] . '/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function get_account() {
        // 1. Ambil data dari database sesuai query Anda
        $this->db->select('a.ACCOUNT_ID, a.ACCOUNT_CODE, a.PARENT_FLAG, a.PARENT_ID, a.ACCOUNT_NAME, a.ACCOUNT_TYPE_ID, a.ACTIVE_FLAG, a.MATA_UANG_ID, a.KATA,
            b.DISPLAY_NAME AS ACCOUNT_TYPE, b.PROGRAM_CODE1,
            c.MATA_UANG_CODE,
        ');
        $this->db->join('erp_lookup_value b','b.ERP_LOOKUP_VALUE_ID = a.ACCOUNT_TYPE_ID');
        $this->db->join('mata_uang c','c.MATA_UANG_ID = a.MATA_UANG_ID');
        $this->db->order_by('a.ACCOUNT_CODE', 'ASC');
        $account = $this->db->get('account a')->result_array();

        $tree = [];
        $references = [];

        foreach ($account as $key => $row) {
            $id = trim($row['ACCOUNT_ID']);
            $account[$key]['children'] = [];
            $references[$id] = &$account[$key];
        }

        foreach ($references as $id => &$node) {
            $parentId = trim($node['PARENT_ID']);

            if ($parentId === '0' || $parentId === '' || $parentId === null) {
                $tree[] = &$node;
            } else {
                if (isset($references[$parentId])) {
                    $references[$parentId]['children'][] = &$node;
                } else {
                    $node['ACCOUNT_NAME'] = '[DATA YATIM] ' . $node['ACCOUNT_NAME']; 
                    $node['ERROR_INFO'] = "Parent ID {$parentId} tidak ada di database!";
                    
                    $tree[] = &$node;
                }
            }
        }
        unset($node);

        return $tree;
    }

    public function get_type(){
        $result = $this->gl_account->get_type()->result();
        echo json_encode($result);
    }
    public function get_mata_uang(){
        $result = $this->gl_account->get_mata_uang()->result();
        echo json_encode($result);
    }

    public function save(){
        try {
            $rows = json_decode($this->input->post('rows'), true);
            if (!$rows){
                throw new Exception("Tidak ada data yang dikirim");
            };

            $parent_id  = (int) $this->encrypt->decode(base64url_decode($this->input->post('parent_id')));
            $parent     = $this->gl_account->get_by_id($parent_id)->row();
            if(!$parent){
                throw new Exception("Data Parent tidak ditemukan");
            }
            

            //validasi penggunaan code
            $parent_code = strtoupper(trim($parent->ACCOUNT_CODE));
            $parent_len  = strlen($parent_code);

            // Mapping Aturan 3: [panjang_parent => panjang_child_yang_seharusnya]
            $length_rule_map = [
                1 => 2,
                2 => 3,
                3 => 4,
                4 => 6
            ];
            $expected_child_len     = $length_rule_map[$parent_len] ?? null;
            $arr_prefix_not_match   = []; // 1. Gagal karena digit awal beda
            $arr_length_too_short   = []; // 2. Gagal karena length <= parent
            $arr_hierarchy_invalid  = []; // 3. Gagal karena tidak sesuai pola relasi digit
            foreach ($rows as $row) {
                $fields = $row['fields'] ?? [];
                $code   = strtoupper(trim($fields['code'] ?? ''));
                
                if (strpos($code, $parent_code) !== 0) {
                    $arr_prefix_not_match[] = $code;
                    continue;
                }

                $code_len = strlen($code);
                if ($code_len <= $parent_len) {
                    $arr_length_too_short[] = $code;
                    continue; 
                }

                if ($expected_child_len !== null && $code_len !== $expected_child_len) {
                    $arr_hierarchy_invalid[] = $code;
                }
            }

            if (!empty($arr_prefix_not_match)) {
                throw new Exception("Kode [" . implode(', ', $arr_prefix_not_match) . "] salah karena digit awal tidak sama dengan Parent ({$parent_code}).");
            }
            if (!empty($arr_length_too_short)) {
                throw new Exception("Kode [" . implode(', ', $arr_length_too_short) . "] salah karena jumlah digit sama atau kurang dari Parent.");
            }
            if (!empty($arr_hierarchy_invalid)) {
                throw new Exception("Kode [" . implode(', ', $arr_hierarchy_invalid) . "] tidak sesuai. harus {$expected_child_len} digit.");
            }

            $now     = date('Y-m-d H:i:s');
            $user_id = (int) $this->session->id;
            $arr_insert = [];
            $arr_update = [];

            //validasi untuk insert
            $new_codes = array_column(array_filter($rows, fn($r) => !empty($r['isNew'])), 'fields');
            $new_codes = array_map(fn($f) => strtoupper(trim($f['code'] ?? '')), $new_codes);
            $new_codes = array_filter($new_codes);
            if(!empty($new_codes)){
                $duplicate_codes = $this->gl_account->get_duplicate_codes($new_codes);
                if ($duplicate_codes) {
                    $list = implode(', ', array_column($duplicate_codes, 'ACCOUNT_CODE'));
                    throw new Exception("Account sudah ada: {$list}");
                }
            }

            //validasi untuk update
            $ids      = array_column(array_filter($rows, fn($r) => empty($r['isNew'])), 'id');
            $ids      = array_map(fn($id) => $this->encrypt->decode(base64url_decode($id)), $ids);
            $existing = [];
            if(!empty($ids)){
                $existing = array_column($this->gl_account->get_by_id($ids)->result_array(), null, 'ACCOUNT_ID');
            }

            foreach ($rows as $row) {
                $fields = $row['fields'] ?? [];
                $isNew  = !empty($row['isNew']);
                $id     = $isNew ? null : $this->encrypt->decode(base64url_decode($row['id'] ?? ''));
                $code   = strtoupper(trim($fields['code'] ?? ''));

                if ($isNew) {
                    $arr_insert[] = array_filter([
                        'PARENT_ID'         => $parent_id,
                        'ACCOUNT_CODE'      => (int) ($fields['code']  ?? 0),
                        'ACCOUNT_NAME'      => $fields['name']  ?? null,
                        'ACCOUNT_TYPE_ID'   => (int) ($fields['type'] ?? 0),
                        'MATA_UANG_ID'      => (int) ($fields['mata_uang'] ?? 0),
                        'KATA'              => $fields['kata']  ?? null,
                        'ACTIVE_FLAG'       => $fields['active_flag']  ?? null,
                        'CREATED_BY'        => $user_id,
                        'CREATED_DATE'      => $now,
                    ]);
                } elseif ($id) {
                    if(!isset($existing[$id])){
                        throw new Exception("Account '{$code}' tidak ditemukan untuk melakukan update.");
                    }
                    $arr_update[] = [
                        'ACCOUNT_ID'       => $id,
                        'ACCOUNT_CODE'     => $fields['code']  ?? $existing[$id]['ACCOUNT_CODE'],
                        'ACCOUNT_NAME'     => $fields['name']  ?? $existing[$id]['ACCOUNT_NAME'],
                        'ACCOUNT_TYPE_ID'  => $fields['type'] ?? $existing[$id]['ACCOUNT_TYPE_ID'],
                        'MATA_UANG_ID'     => $fields['mata_uang'] ?? $existing[$id]['MATA_UANG_ID'],
                        'KATA'             => $fields['kata']  ?? $existing[$id]['KATA'],
                        'ACTIVE_FLAG'      => $fields['active_flag']  ?? $existing[$id]['ACTIVE_FLAG'],
                        'LAST_UPDATE_BY'   => $user_id,
                        'LAST_UPDATE_DATE' => $now,
                    ];
                }
            }

            if (!empty($arr_update) && !$this->access['update']) throw new Exception('Tidak ada akses edit!');
            if (!empty($arr_insert) && !$this->access['insert']) throw new Exception('Tidak ada akses tambah!');

            $this->db->trans_start();

            if (!empty($arr_insert)) $this->gl_account->insert_batch($arr_insert);
            if (!empty($arr_update)) $this->gl_account->update_batch($arr_update);

            if($parent->PARENT_FLAG !== 'Y'){
                $this->gl_account->update($parent_id, ['PARENT_FLAG' => 'Y']);
            }

            $this->db->trans_complete();

            if($this->db->trans_status() === false){
                sendWarning('Gagal menyimpan data.');
            }else{
                $this->session->set_flashdata($this->access['url'].'_saved', $parent_id);
                sendSuccess('', 'Data berhasil disimpan!');
            }

        } catch (Exception $err) {
            $this->db->trans_rollback();
            sendWarning($err->getMessage());
        }
    }
}