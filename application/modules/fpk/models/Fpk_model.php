<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fpk_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        // "a.NEED_DATE",
        "Supplier",
        "k.FIRST_NAME",
        "w.WAREHOUSE_NAME",
        "a.TOTAL_AMOUNT",
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        // "a.NEED_DATE",
        "CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' )",
        "k.FIRST_NAME",
        "w.WAREHOUSE_NAME",
        "a.TOTAL_AMOUNT",
    );

    var $order = array(
        'a.PR_ID' => 'DESC',
        'a.DOCUMENT_DATE' => 'DESC'
    );

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON ( a.TABLE_NAME = b.TABLE_NAME ) WHERE b.PROMPT = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.PR_ID,
            b.DISPLAY_NAME Status, b.MENU_ICON Warna_Status,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            a.TOTAL_AMOUNT Total,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Supplier,
            w.WAREHOUSE_NAME Gudang,
            k.FIRST_NAME Sales
        ");
        $this->db->from('pr a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('karyawan k', 'a.KARYAWAN_ID = k.KARYAWAN_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);

        $i = 0;
        foreach ($this->column_search as $item) {
            $global_search_value = $this->input->post('search')['value'] ?? '';
            $column_search_value = $this->input->post('columns')[$i]['search']['value'] ?? '';

            if ($column_search_value != '') {
                $this->db->like($item, $column_search_value);
            } elseif ($global_search_value != '') {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $global_search_value);
                } else {
                    $this->db->or_like($item, $global_search_value);
                }
                if (count($this->column_search) - 1 == $i) $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by(
                $this->column_order[$_POST['order']['0']['column']],
                $_POST['order']['0']['dir']
            );
        } elseif (isset($this->order)) {
            $order = $this->order;
            foreach ($order as $field => $direction) {
                $this->db->order_by($field, $direction);
            }
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit(
                $_POST['length'],
                $_POST['start']
            );
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON ( a.TABLE_NAME = b.TABLE_NAME ) WHERE b.PROMPT = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.PR_ID,
            b.DISPLAY_NAME Status, b.MENU_ICON Warna_Status,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            a.TOTAL_AMOUNT Total,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Supplier,
            w.WAREHOUSE_NAME Gudang,
            k.FIRST_NAME Sales
        ");
        $this->db->from('pr a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('karyawan k', 'a.KARYAWAN_ID = k.KARYAWAN_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_pr_id($pr_id, $limit = null, $start = null)
    {
        $this->db->select("COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION) AS Item_Name, i.ITEM_CODE, d.ENTERED_UOM, d.NOTE, d.ENTERED_QTY AS QTY, d.UNIT_PRICE AS PRICE, d.SUBTOTAL AS TOTAL, d.RECEIVED_ENTERED_QTY / NULLIF(d.BASE_QTY, 0) Terima,
        (d.ENTERED_QTY - ( d.RECEIVED_ENTERED_QTY / NULLIF(d.BASE_QTY, 0) ) ) AS Sisa");
        $this->db->from("pr_detail d");
        $this->db->join("item i", "d.ITEM_ID = i.ITEM_ID");
        $this->db->where("d.PR_ID", $pr_id);
        $this->db->order_by('d.PR_DETAIL_ID', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_pr_id($pr_id)
    {
        $this->db->where('PR_ID', $pr_id);
        return $this->db->count_all_results('pr_detail');
    }

    public function getSupplier()
    {
        return $this->db->query("SELECT a.PERSON_ID, a.PERSON_NAME Supplier, a.PERSON_CODE Kode FROM person a JOIN person_site b ON (a.PERSON_ID = b.PERSON_ID) WHERE a.FLAG_SUPP = 1 AND a.ACTIVE_FLAG = 'Y' GROUP BY a.PERSON_ID ORDER BY a.PERSON_NAME");
    }

    public function getGudang()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = {$this->session->userdata('id')} WHERE ACTIVE_FLAG = 'Y' AND a.JENIS_ID = FN_GET_VAR_VALUE ('PST') GROUP BY a.WAREHOUSE_ID ORDER BY IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function getSales()
    {
        return $this->db->query("SELECT k.KARYAWAN_ID, k.FIRST_NAME, k.LAST_NAME, k.KATA_DEPAN, k.DESCRIPTION FROM karyawan k WHERE k.DEPT_ID = @SALES AND ( (k.END_DATE = 0) OR (k.END_DATE IS NULL) OR (k.END_DATE >= CURDATE()) ) AND k.ACTIVE_FLAG = 'Y' ORDER BY k.FIRST_NAME");
    }

    public function getPrId($id)
    {
        $this->db->from('pr');
        $this->db->where('pr.PR_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('PR_ID', $id);
        $this->db->delete('pr_detail');
        return ($this->db->error()['code'] == 0);
    }

    public function updateStatus($id, $status)
    {
        $params = array(
            'STATUS_ID' => $status,
        );
        $this->db->where('PR_ID', $id);
        $this->db->update('pr', $params);
        return ($this->db->error()['code'] == 0);
    }

    public function get_fpk_detail($id)
    {
        $this->db->select("
            a.DOCUMENT_DATE,a.DOCUMENT_NO,a.DOCUMENT_REFF_NO,a.TOTAL_AMOUNT,a.NOTE,
            w.WAREHOUSE_NAME,
            k.FIRST_NAME as SALES_FIRST_NAME,
            k.LAST_NAME as SALES_LAST_NAME,
        ");
        // $this->db->select("CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) as SUPPLIER",true);
        $this->db->select("p.PERSON_NAME as SUPPLIER", true);
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('karyawan k', 'a.KARYAWAN_ID = k.KARYAWAN_ID');
        $this->db->from('pr a');
        $this->db->where('a.PR_ID', $id);
        return $this->db->get();
    }

    public function get_log_user($id)
    {
        $this->db->select('a.CREATED_DATE,a.LAST_UPDATE_DATE,c.ERP_USER_NAME as USER_CREATED,u.ERP_USER_NAME as USER_UPDATED');
        $this->db->from('pr a');
        $this->db->join('erp_user c', 'a.CREATED_BY = c.ERP_USER_ID', 'left');
        $this->db->join('erp_user u', 'a.LAST_UPDATE_BY = u.ERP_USER_ID', 'left');
        $this->db->where('a.PR_ID', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function getApiGudang()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = $this->input->get('id') ? (int) $this->input->get('id') : null;
        $default    = trim($this->input->get('default') ?? '');
        $user_id    = (int) $this->session->id;

        $this->db->select("a.WAREHOUSE_ID as id, a.WAREHOUSE_NAME as text")
            ->from('warehouse a');

        // 2. LEFT JOIN dengan Subquery Aggregasi (Menghindari duplikasi data)
        $subquery_join = "(SELECT WAREHOUSE_ID, MAX(PRIMARY_FLAG) AS PRIMARY_FLAG
                        FROM erp_warehouse
                        WHERE ERP_USER_ID = '$user_id'
                        GROUP BY WAREHOUSE_ID) g";
        $this->db->join($subquery_join, 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'left');

        $this->db->where('a.ACTIVE_FLAG', 'Y');
        $this->db->where("a.JENIS_ID = FN_GET_VAR_VALUE('PST')", NULL, FALSE);

        $this->db->where("
            (
                (EXISTS (SELECT 1 FROM erp_warehouse WHERE ERP_USER_ID = '$user_id') AND g.WAREHOUSE_ID IS NOT NULL)
                OR 
                NOT EXISTS (SELECT 1 FROM erp_warehouse WHERE ERP_USER_ID = '$user_id')
            )
        ", NULL, FALSE);

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) =', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()->like('a.WAREHOUSE_NAME', $searchTerm)->group_end();
            }
            $this->db->limit(50);
        }

        $this->db->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)', 'DESC', FALSE);
        $this->db->order_by('a.WAREHOUSE_NAME', 'ASC');

        return $this->db->get();
    }

    private function getApiGudangAlternatif()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = $this->input->get('id') ? (int) $this->input->get('id') : null;
        $default    = trim($this->input->get('default') ?? '');
        $user_id    = (int) $this->session->id;

        $default_field = 'a.PRIMARY_FLAG';

        // Cek hak akses user
        $user_has_warehouse = $this->db->where('ERP_USER_ID', $user_id)
            ->limit(1)
            ->count_all_results('erp_warehouse') > 0;

        $this->db->select("a.WAREHOUSE_ID as id, a.WAREHOUSE_NAME as text")
            ->from('warehouse a');

        $this->db->where('a.ACTIVE_FLAG', 'Y');
        $this->db->where("a.JENIS_ID = FN_GET_VAR_VALUE('PST')", NULL, FALSE);

        // Jika user punya warehouse, gunakan INNER JOIN ke subquery aggregasi
        if ($user_has_warehouse) {
            $default_field = 'IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)';

            $subquery_join = "(SELECT WAREHOUSE_ID, MAX(PRIMARY_FLAG) AS PRIMARY_FLAG
                            FROM erp_warehouse
                            WHERE ERP_USER_ID = '$user_id'
                            GROUP BY WAREHOUSE_ID) g";

            $this->db->join($subquery_join, 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'inner');
        }

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where($default_field, 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()->like('a.WAREHOUSE_NAME', $searchTerm)->group_end();
            }
            $this->db->limit(50);
        }

        $this->db->order_by($default_field, 'DESC', FALSE);
        $this->db->order_by('a.WAREHOUSE_NAME', 'ASC');

        return $this->db->get();
    }

    public function getApiSales()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = $this->input->get('id') ? (int) $this->input->get('id') : null;
        $user_id    = (int) $this->session->id;

        $this->db->select("k.KARYAWAN_ID as id, CONCAT(k.FIRST_NAME, ' - [', k.LAST_NAME, ']') as text, k.KATA_DEPAN, k.DESCRIPTION")
            ->from('karyawan k');

        $subquery_join = "(SELECT KARYAWAN_ID 
                        FROM erp_group_sales 
                        WHERE ERP_USER_ID = '$user_id' 
                        GROUP BY KARYAWAN_ID) g";
        $this->db->join($subquery_join, 'k.KARYAWAN_ID = g.KARYAWAN_ID', 'left');

        $this->db->where("k.DEPT_ID = FN_GET_VAR_VALUE('SALES')", NULL, FALSE);
        $this->db->where('k.ACTIVE_FLAG', 'Y');
        $this->db->where("(k.END_DATE = 0 OR k.END_DATE IS NULL OR k.END_DATE >= CURDATE())", NULL, FALSE);

        $this->db->where("
            (
                (EXISTS (SELECT 1 FROM erp_group_sales WHERE ERP_USER_ID = '$user_id') AND g.KARYAWAN_ID IS NOT NULL)
                OR 
                NOT EXISTS (SELECT 1 FROM erp_group_sales WHERE ERP_USER_ID = '$user_id')
            )
        ", NULL, FALSE);

        if ($id) {
            $this->db->where('k.KARYAWAN_ID', $id)->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('k.FIRST_NAME', $searchTerm)
                    ->or_like('k.LAST_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        $this->db->order_by('k.FIRST_NAME', 'ASC');

        return $this->db->get();
    }
}
