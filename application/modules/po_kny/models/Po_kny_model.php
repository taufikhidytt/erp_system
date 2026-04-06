<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Po_kny_model extends CI_Model
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
        "Supplier",
        "WAREHOUSE_NAME",
        "PAYMENT_TERM_NAME",
        "Total"
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "CONCAT(p . PERSON_NAME, ' - [', p . PERSON_CODE, ']')",
        "WAREHOUSE_NAME",
        "PAYMENT_TERM_NAME",
        "TOTAL_NET"
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.INVOICE_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Supplier,
            w.WAREHOUSE_ID,
            w.WAREHOUSE_NAME S_Loc,
            py.PAYMENT_TERM_ID,
            py.PAYMENT_TERM_NAME Terms,
            a.TOTAL_NET Total
        ");
        $this->db->from('invoice a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('payment_term py', 'a.PAYMENT_TERM_ID = py.PAYMENT_TERM_ID');
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
            $this->db->order_by(key($order), $order[key($order)]);
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
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.INVOICE_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Supplier,
            w.WAREHOUSE_ID,
            w.WAREHOUSE_NAME S_Loc,
            py.PAYMENT_TERM_ID,
            py.PAYMENT_TERM_NAME Terms,
            a.TOTAL_NET Total
        ");
        $this->db->from('invoice a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('payment_term py', 'a.PAYMENT_TERM_ID = py.PAYMENT_TERM_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_po_id($invoice_id, $limit = null, $start = null)
    {
        $sql = "SELECT
            i.ITEM_DESCRIPTION Nama_Item,
            i.ITEM_CODE Kode_Item,
            a.ENTERED_QTY Qty,
            a.ENTERED_UOM UoM,
            a.UNIT_PRICE Harga,
            a.DISCOUNT_PRICE Diskon,
            a.SUBTOTAL Total,
            b.DOCUMENT_NO No_MR,
            w.WAREHOUSE_NAME S_Loc_In,
            a.NOTE Note,
            a.INVOICE_DETAIL_ID,
            a.INVOICE_ID,
            a.INVENTORY_IN_DETAIL_ID,
            w.WAREHOUSE_ID 
        FROM
            invoice_detail a
            JOIN item i ON a.ITEM_ID = i.ITEM_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN inventory_in_detail iid ON a.INVENTORY_IN_DETAIL_ID = iid.INVENTORY_IN_DETAIL_ID
            JOIN build_detail bd ON iid.BUILD_DETAIL_ID = bd.BUILD_DETAIL_ID
            JOIN build b ON bd.BUILD_ID = b.BUILD_ID 
        WHERE
            a.INVOICE_ID = {$invoice_id}";

        if ($limit !== null && $start !== null) {
            $sql .= " LIMIT {$start}, {$limit}";
        }

        return $this->db->query($sql);
    }

    public function count_detail_by_po_id($invoice_id)
    {
        return $this->db
            ->where('INVOICE_ID', $invoice_id)
            ->count_all_results('invoice_detail');
    }

    public function get_storage()
    {
        return $this->db->query("SELECT DISTINCT
                    a.WAREHOUSE_ID,
                    a.ADDRESS_ID,
                    a.PRIMARY_FLAG,
                    a.WAREHOUSE_NAME,
                    g.PRIMARY_FLAG AS USER_PRIMARY_FLAG 
                FROM
                    warehouse a
                    LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID 
                    AND g.ERP_USER_ID = '{$this->session->userdata('id')}'
                WHERE
                    a.ACTIVE_FLAG = 'Y' 
                ORDER BY
                CASE
                        WHEN g.PRIMARY_FLAG IS NOT NULL THEN
                        g.PRIMARY_FLAG ELSE a.PRIMARY_FLAG 
                    END DESC,
                    a.WAREHOUSE_NAME");
    }

    public function get_payment_term()
    {
        $sql = "SELECT
                p.PAYMENT_TERM_ID,
                p.PAYMENT_TERM_NAME,
                p.DESCRIPTION,
                p.NUMBER_DAYS,
                p.PRIMARY_FLAG,
                p.ACTIVE_FLAG 
            FROM
                payment_term p 
            WHERE
                ACTIVE_FLAG = 'Y' 
            ORDER BY
                PRIMARY_FLAG DESC,
                P.NUMBER_DAYS";
        return $this->db->query($sql);
    }

    public function get_supplier()
    {
        return $this->db->query("SELECT
                a.POINT,
                a.PERSON_ID,
                a.PERSON_CODE,
                a.PERSON_NAME,
                a.LIMIT_PIUTANG,
                a.TUNAI_FLAG,
                b.PAYMENT_TERM_ID,
                COALESCE ( COALESCE ( b.NUMBER_DAYS, 0 ) + COALESCE ( pp.LIMIT_DAY, 0 ), a.CUSTOM1 ) AS CUSTOM1,
                a.CUSTOM2,
                a.TIPE_HARGA_JUAL,
                b.PAYMENT_TERM_NAME,
                b.NUMBER_DAYS,
                k.FIRST_NAME,
                a.KARYAWAN_ID,
                a.MATA_UANG_ID,
                m.MATA_UANG_NAME,
                COALESCE ( a.PERSON_NAME2, a.PERSON_NAME ) AS PERSON_NAME2,
                ps.ADDRESS1,
                ps.SITE_NAME,
                ps.PERSON_SITE_ID,
            CASE
                    
                    WHEN ti.DESCRIPTION IS NULL 
                    OR ti.DESCRIPTION = '' THEN
                        0 
                        WHEN ti.DESCRIPTION REGEXP '^[0-9]+\.?[0-9]*$' THEN
                        CAST(
                        ti.DESCRIPTION AS DECIMAL ( 19, 4 )) ELSE 0 
                    END AS cb,
                    ps.TAX_NAME,
                    a.PPN_CODE,
                    a.APPROVE_FLAG 
                FROM
                    person a
                    INNER JOIN payment_term b ON a.DEFAULT_TERM_ID = b.PAYMENT_TERM_ID
                    INNER JOIN person_site ps ON a.PERSON_ID = ps.PERSON_ID
                    LEFT JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID
                    LEFT JOIN mata_uang m ON m.MATA_UANG_ID = a.MATA_UANG_ID
                    LEFT JOIN erp_lookup_value ti ON a.TIPE_CUSTOMER_ID = ti.ERP_LOOKUP_VALUE_ID
                    LEFT JOIN person_day pp ON a.PERSON_ID = pp.PERSON_ID 
                    AND pp.MEREK_ID IS NULL 
                    AND pp.GROUP_ID IS NULL 
                WHERE
                    a.ACTIVE_FLAG = 'Y' 
                    AND a.FLAG_SUPP = 1 
            ORDER BY
                a.PERSON_NAME");
    }

    public function get_ppn_code()
    {
        return $this->db->query("SELECT a.PPN_CODE, a.PERCENTAGE, a.PRIMARY_FLAG FROM ppn a WHERE a.ACTIVE_FLAG = 'Y' AND a.TIPE_PAJAK_ID = FN_GET_VAR_VALUE ('PPN') ORDER BY a.PRIMARY_FLAG DESC, a.PPN_CODE");
    }

    public function get_po_id($id)
    {
        $this->db->from('invoice');
        $this->db->where('INVOICE_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('INVOICE_ID', $id);
        $this->db->delete('invoice_detail');

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }

    public function updateStatus($id, $status)
    {
        $params = array(
            'STATUS_ID' => $status,
        );
        $this->db->where('INVOICE_ID', $id);
        $this->db->update('invoice', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }

    public function get_po_detail($id){
        $this->db->select("
            a.DOCUMENT_DATE,a.DOCUMENT_NO,a.DOCUMENT_REFF_NO,a.TOTAL_AMOUNT,a.NOTE,
            w.WAREHOUSE_NAME,
            ps.SITE_NAME, ps.ADDRESS1, ps.ADDRESS2, ps.ADDRESS3, ps.CITY,
            py.PAYMENT_TERM_NAME,
        ");
        $this->db->select("CONCAT(p.PERSON_NAME,' - [',p.PERSON_CODE,']',' - ',ps.SITE_NAME) Customer", true);
        $this->db->from('invoice a');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('person_site ps', 'a.PERSON_SITE_ID = ps.PERSON_SITE_ID');
        $this->db->join('payment_term py', 'a.PAYMENT_TERM_ID = py.PAYMENT_TERM_ID');
        $this->db->where('a.INVOICE_ID',$id);
        return $this->db->get();
    }
}
