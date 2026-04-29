<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'erp_user';
    public function __construct()
    {
        setVariableMysql();
    }

    public function getUserId($id)
    {
        $this->db->select("
            a.ERP_USER_ID,a.ERP_GROUP_ID,a.ERP_USER_NAME,a.ERP_USER_DESC,a.TITLE,a.DIVISI_ID,a.START_DATE,a.END_DATE,
        ");
        $this->db->from($this->table.' a');
        $this->db->where('a.ERP_USER_ID', $id);
        return $this->db->get();
    }

    public function getGroup(){
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');
        $this->db
            ->select("a.ERP_GROUP_ID as id, a.ERP_GROUP_NAME text")
            ->from('erp_group a');

        if ($id) {
            $this->db->where('a.ERP_GROUP_ID', $id)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.ERP_GROUP_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getDivisi(){
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');
        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text")
            ->where("b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET('DIVISI')", NULL, FALSE)
            ->from('erp_lookup_value b')
            ->order_by('b.PRIMARY_FLAG DESC, b.DISPLAY_NAME');
        
        

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getGroupMenu($group_id,$user_id)
    {
        $this->db->select('a.ERP_MENU_ID,a.PARENT_ID,a.ERP_MENU_NAME,a.PROMPT,a.PERMISSIONS,a.MENU_ICON,
            b.VIEW_FLAG,b.INSERT_FLAG,b.UPDATE_FLAG,b.DELETE_FLAG,
            c.PERMISSIONS as USER_PERMISSIONS,
        ');
        $this->db->from('erp_menu a');
        $this->db->join('erp_group_menu b','b.ERP_MENU_ID = a.ERP_MENU_ID');
        $this->db->join("erp_user_menu_permission c","c.ERP_MENU_ID = a.ERP_MENU_ID AND c.ERP_USER_ID = $user_id",'left');
        $this->db->where('a.ACTIVE_FLAG', 'Y');
        $this->db->where('b.VIEW_FLAG', 'Y');
        $this->db->where('b.ERP_GROUP_ID',$group_id);
        $this->db->order_by('a.ERP_MENU_ID');
        return $this->db->get();
    }

    public function save_permissions($id)
    {
        $current_user   = $this->session->userdata('id');
        $now            = date('Y-m-d H:i:s');

        //menu permissions user exclude crud
        $existing_permissions = $this->db->select('ERP_MENU_ID, ERP_USER_MENU_ID')
            ->where('ERP_USER_ID', $id)
            ->get('erp_user_menu_permission')
            ->result_array();
        $menu_map = array_column($existing_permissions, 'ERP_USER_MENU_ID', 'ERP_MENU_ID');

        $menu_inputs    = $this->input->post('menu') ?: [];
        $arr_insert     = [];
        $arr_update     = [];
        $processed_menu_ids = [];
        foreach ($menu_inputs as $id_menu => $encoded_data) {
            $decoded = json_decode(base64_decode($encoded_data, true), true);
            if (is_array($decoded) && !empty($decoded)) {
                $json_permissions       = json_encode($decoded);
                $processed_menu_ids[]   = $id_menu;
                if(isset($menu_map[$id_menu])){
                    $arr_update[] = [
                        'ERP_USER_MENU_ID'=> $menu_map[$id_menu],
                        'PERMISSIONS'     => $json_permissions,
                        'LAST_UPDATE_BY'  => $current_user,
                        'LAST_UPDATE_DATE'=> $now
                    ];
                }else{
                    $arr_insert[] = [
                        'ERP_MENU_ID'     => $id_menu,
                        'ERP_USER_ID'     => $id,
                        'PERMISSIONS'     => $json_permissions,
                        'CREATED_BY'      => $current_user,
                        'CREATED_DATE'    => $now
                    ];
                }
            }
        }

        //update permission yang tidak terdaftar
        $this->db->where('ERP_USER_ID', $id);
        if (!empty($processed_menu_ids)) {
            $this->db->where_not_in('ERP_MENU_ID', $processed_menu_ids);
        }
        $this->db->update('erp_user_menu_permission', [
            'PERMISSIONS' => null,
            'LAST_UPDATE_BY' => $current_user,
            'LAST_UPDATE_DATE' => $now
        ]);

        //insert permission baru
        if (!empty($arr_insert)) {
            $this->db->insert_batch('erp_user_menu_permission',$arr_insert);
        }

        //update permission baru
        if (!empty($arr_update)) {
            $this->db->update_batch('erp_user_menu_permission',$arr_update,'ERP_USER_MENU_ID');
        }
    }

    public function add($post)
    {
        date_default_timezone_set('Asia/Jakarta');
        $params = array(
            'ERP_GROUP_ID'    => $post['group_id'] ? htmlspecialchars($post['group_id']) : null,
            'ERP_USER_NAME'   => $post['name'] ? htmlspecialchars($post['name']) : null,
            'ERP_USER_DESC'   => $post['full_name'] ? htmlspecialchars($post['full_name']) : null,
            'TITLE'           => $post['title'] ? htmlspecialchars($post['title']) : null,
            'DIVISI_ID'       => $post['divisi_id'] ? htmlspecialchars($post['divisi_id']) : null,
            'START_DATE'      => $post['start_date'] ? htmlspecialchars($post['start_date']) : null,
            'END_DATE'        => $post['end_date'] ? htmlspecialchars($post['end_date']) : null,
            'PASSWORD'        => '*' . strtoupper(sha1(sha1($post['password'], true))),
            'CREATED_BY'      => $this->session->userdata('id'),
            'CREATED_DATE'    => date('Y-m-d H:i:s'),
        );

        $this->db->insert('erp_user', $params);
        return $this->db->insert_id();
    }

    public function update_by_id($id,$post)
    {
        $params = array(
            'ERP_GROUP_ID'    => $post['group_id'] ? htmlspecialchars($post['group_id']) : null,
            'ERP_USER_NAME'   => $post['name'] ? htmlspecialchars($post['name']) : null,
            'ERP_USER_DESC'   => $post['full_name'] ? htmlspecialchars($post['full_name']) : null,
            'TITLE'           => $post['title'] ? htmlspecialchars($post['title']) : null,
            'DIVISI_ID'       => $post['divisi_id'] ? htmlspecialchars($post['divisi_id']) : null,
            'START_DATE'      => $post['start_date'] ? htmlspecialchars($post['start_date']) : null,
            'END_DATE'        => $post['end_date'] ? htmlspecialchars($post['end_date']) : null,
            'LAST_UPDATE_BY'  => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'=> date('Y-m-d H:i:s'),
        );
        if(isset($post['password']) && $post['password']){
            $params['PASSWORD'] = '*' . strtoupper(sha1(sha1($post['password'], true)));
        }
        $this->db->where('ERP_USER_ID',$id);
        $this->db->update('erp_user', $params);
    }

    public function getAccounts()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');
        $this->db
            ->select("a.COA_ID as id, CONCAT('[',a.COA_CODE, '] ', a.COA_NAME) as text, a.COA_CODE, a.COA_NAME, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2")
            ->from('coa a')
            ->join('account ac', 'ac.ACCOUNT_ID = a.ACCOUNT_ID')
            ->order_by('a.COA_CODE');

        if ($id) {
            $this->db->where('a.COA_ID', $id)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.COA_CODE', $searchTerm)
                    ->or_like('a.COA_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getWarehouses()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');
        $this->db
            ->select("a.WAREHOUSE_ID as id, a.WAREHOUSE_NAME as text")
            ->from('warehouse a')
            ->join('erp_warehouse g', 'a.WAREHOUSE_ID = g.WAREHOUSE_ID AND g.ERP_USER_ID = "1"', 'left')
            ->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME')
            ->group_by('a.WAREHOUSE_ID');

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.WAREHOUSE_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function add_user_accounts($user_id, $post)
    {
        $accounts       = $post['account'] ?? [];
        $account_notes  = $post['account_note'] ?? [];
        $created_by     = $this->session->userdata('id');
        $created_date   = date('Y-m-d H:i:s');
        if (!empty($accounts)) {
            $data = [];
            foreach ($accounts as $k => $v) {
                $coa_id = (int) $v;
                if(!$coa_id) continue;
                $data[] = [
                    'ERP_USER_ID' => $user_id,
                    'COA_ID'      => $coa_id,
                    'NOTE'        => $account_notes[$k] ?? null,
                    'CREATED_BY'  => $created_by,
                    'CREATED_DATE'=> $created_date,
                ];
            }
            if(!empty($data)){
                $this->db->insert_batch('erp_user_d_akun', $data);
            }
        }
    }

    public function add_user_warehouses($user_id, $post)
    {
        $warehouses         = $post['warehouses'] ?? [];
        $default_warehouses = $post['default_warehouse'] ?? [];
        $created_by         = $this->session->userdata('id');
        $created_date       = date('Y-m-d H:i:s');
        $have_default       = false;
        if (!empty($warehouses)) {
            $data = [];
            foreach ($warehouses as $k => $v) {
                $warehouse_id = (int) $v;
                if(!$warehouse_id) continue;

                $primary_flag = 'N';
                if(!$have_default && isset($default_warehouses[$k]) && $default_warehouses[$k] == 'Y'){
                    $primary_flag = 'Y';
                    $have_default = true;
                }

                $data[] = [
                    'ERP_USER_ID' => $user_id,
                    'WAREHOUSE_ID' => $warehouse_id,
                    'PRIMARY_FLAG' => $primary_flag,
                    'CREATED_BY'  => $created_by,
                    'CREATED_DATE'=> $created_date,
                ];
            }
            if(!empty($data)){
                $this->db->insert_batch('erp_warehouse', $data);
            }
        }
    }

    public function add_user_sales($user_id, $post)
    {
        $sales         = $post['sales'] ?? [];
        $created_by    = $this->session->userdata('id');
        $created_date  = date('Y-m-d H:i:s');
        if (!empty($sales)) {
            $data = [];
            foreach ($sales as $k => $v) {
                $karyawan_id = (int) $v;
                if(!$karyawan_id) continue;

                $data[] = [
                    'ERP_USER_ID' => $user_id,
                    'ERP_GROUP_ID' => (int) $post['group_id'],
                    'KARYAWAN_ID' => (int) $karyawan_id,
                    'CREATED_BY'  => $created_by,
                    'CREATED_DATE'=> $created_date,
                ];
            }
            if(!empty($data)){
                $this->db->insert_batch('erp_group_sales', $data);
            }
        }
    }

    public function getSales()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');
        $this->db
            ->select("k.KARYAWAN_ID as id, CONCAT('[',k.FIRST_NAME, '] - ', k.LAST_NAME) as text, k.FIRST_NAME, k.LAST_NAME, k.KATA_DEPAN, k.DESCRIPTION")
            ->from('karyawan k')
            ->join('erp_group_sales eud', 'k.KARYAWAN_ID = eud.KARYAWAN_ID', 'left')
            ->where("k.DEPT_ID = FN_GET_VAR_VALUE('SALES')", NULL, FALSE)
            ->where('(k.END_DATE = 0 OR k.END_DATE IS NULL)')
            ->group_by('k.KARYAWAN_ID')
            ->order_by('k.FIRST_NAME');
        
        if ($id) {
            $this->db->where('k.KARYAWAN_ID', $id)->limit(1);
        } else {
            $this->db->where('k.AcTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('k.FIRST_NAME', $searchTerm)
                    ->or_like('k.LAST_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getUserAccounts($user_id)
    {
        $this->db->select('a.COA_ID, a.ERP_USER_D_AKUN_ID,a.NOTE');
        $this->db->from('erp_user_d_akun a');
        $this->db->where('a.ERP_USER_ID', $user_id);
        return $this->db->get();
    }

    public function getUserWarehouses($user_id)
    {
        $this->db->select('a.WAREHOUSE_ID, a.ERP_WAREHOUSE_ID, a.PRIMARY_FLAG');
        $this->db->from('erp_warehouse a');
        $this->db->where('a.ERP_USER_ID', $user_id);
        return $this->db->get();
    }

    public function getUserSales($user_id)
    {
        $this->db->select('a.KARYAWAN_ID, a.ERP_GROUP_SALES_ID');
        $this->db->from('erp_group_sales a');
        $this->db->where('a.ERP_USER_ID', $user_id);
        return $this->db->get();
    }

    public function update_user_accounts($user_id, $post)
    {
        $accounts       = $post['account'] ?? [];
        $account_ids    = $post['account_id'] ?? [];
        $account_notes  = $post['account_note'] ?? [];
        $created_by     = $this->session->userdata('id');
        $created_date   = date('Y-m-d H:i:s');
        if (!empty($accounts)) {
            $arr_insert = [];
            $arr_update = [];
            foreach ($accounts as $k => $v) {
                $coa_id     = (int) $v;
                $detail_id  = (int) ($account_ids[$k] ?? 0);
                if(!$coa_id) continue;
                if($detail_id){
                    $arr_update[] = [
                        'ERP_USER_D_AKUN_ID'=> $detail_id,
                        'ERP_USER_ID'       => $user_id,
                        'COA_ID'            => $coa_id,
                        'NOTE'              => $account_notes[$k] ?? null,
                        'LAST_UPDATE_BY'    => $created_by,
                        'LAST_UPDATE_DATE'  => $created_date,
                    ];
                }else{
                    $arr_insert[] = [
                        'ERP_USER_ID'       => $user_id,
                        'COA_ID'            => $coa_id,
                        'NOTE'              => $account_notes[$k] ?? null,
                        'CREATED_BY'        => $created_by,
                        'CREATED_DATE'      => $created_date,
                    ];
                }
            }
            if(!empty($arr_insert)){
                $this->db->insert_batch('erp_user_d_akun', $arr_insert);
            }
            if(!empty($arr_update)){
                $this->db->update_batch('erp_user_d_akun', $arr_update, 'ERP_USER_D_AKUN_ID');
            }
        }
    }

    public function update_user_warehouses($user_id, $post)
    {
        $warehouses         = $post['warehouses'] ?? [];
        $warehouses_id      = $post['warehouses_id'] ?? [];
        $default_warehouses = $post['default_warehouse'] ?? [];
        $created_by         = $this->session->userdata('id');
        $created_date       = date('Y-m-d H:i:s');
        $have_default       = false;
        if (!empty($warehouses)) {
            $arr_insert = [];
            $arr_update = [];
            foreach ($warehouses as $k => $v) {
                $warehouse_id = (int) $v;
                $detail_id = (int) ($warehouses_id[$k] ?? 0);
                if(!$warehouse_id) continue;

                $primary_flag = 'N';
                if(!$have_default && isset($default_warehouses[$k]) && $default_warehouses[$k] == 'Y'){
                    $primary_flag = 'Y';
                    $have_default = true;
                }

                if($detail_id){
                    $arr_update[] = [
                        'ERP_WAREHOUSE_ID'  => $detail_id,
                        'ERP_USER_ID'       => $user_id,
                        'WAREHOUSE_ID'      => $warehouse_id,
                        'PRIMARY_FLAG'      => $primary_flag,
                        'LAST_UPDATE_BY'    => $created_by,
                        'LAST_UPDATE_DATE' => $created_date,
                    ];
                }else{
                    $arr_insert[] = [
                        'ERP_USER_ID'       => $user_id,
                        'WAREHOUSE_ID'      => $warehouse_id,
                        'PRIMARY_FLAG'      => $primary_flag,
                        'CREATED_BY'        => $created_by,
                        'CREATED_DATE'      => $created_date,
                    ];
                }
            }
            if(!empty($arr_insert)){
                $this->db->insert_batch('erp_warehouse', $arr_insert);
            }
            if(!empty($arr_update)){
                $this->db->update_batch('erp_warehouse', $arr_update, 'ERP_WAREHOUSE_ID');
            }
        }
    }

    public function update_user_sales($user_id, $post)
    {
        $sales         = $post['sales'] ?? [];
        $sales_id      = $post['sales_id'] ?? [];
        $created_by    = $this->session->userdata('id');
        $created_date  = date('Y-m-d H:i:s');
        if (!empty($sales)) {
            $arr_insert = [];
            $arr_update = [];
            foreach ($sales as $k => $v) {
                $karyawan_id    = (int) $v;
                $detail_id      = (int) ($sales_id[$k] ?? 0);
                if(!$karyawan_id) continue;

                if($detail_id){
                    $arr_update[] = [
                        'ERP_GROUP_SALES_ID'    => $detail_id,
                        'ERP_USER_ID'           => $user_id,
                        'ERP_GROUP_ID'          => (int) $post['group_id'],
                        'KARYAWAN_ID'           => (int) $karyawan_id,
                        'LAST_UPDATE_BY' => $created_by,
                        'LAST_UPDATE_DATE' => $created_date,
                    ];
                }else{
                    $arr_insert[] = [
                    'ERP_USER_ID'   => $user_id,
                    'ERP_GROUP_ID'  => (int) $post['group_id'],
                    'KARYAWAN_ID'   => (int) $karyawan_id,
                    'CREATED_BY'    => $created_by,
                    'CREATED_DATE'  => $created_date,
                    ];
                }
            }
            if(!empty($arr_insert)){
                $this->db->insert_batch('erp_group_sales', $arr_insert);
            }
            if(!empty($arr_update)){
                $this->db->update_batch('erp_group_sales', $arr_update, 'ERP_GROUP_SALES_ID');
            }
        }
    }
}