<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Do_kny_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        null,
        "DISPLAY_NAME",
        "DOCUMENT_NO",
        "DOCUMENT_REFF_NO",
        "DOCUMENT_DATE",
        "Customer",
        "FIRST_NAME",
        "WAREHOUSE_NAME",
    );

    var $column_search = array(
        null,
        "DISPLAY_NAME",
        "DOCUMENT_NO",
        "DOCUMENT_REFF_NO",
        "DOCUMENT_DATE",
        "CONCAT(p.PERSON_NAME, ' ', p.PERSON_CODE)",
        "FIRST_NAME",
        "WAREHOUSE_NAME",
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.INVENTORY_OUT_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO PO_Customer,
            a.DOCUMENT_DATE Tanggal,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Customer,
            k.KARYAWAN_ID,
            k.FIRST_NAME Sales,
            w.WAREHOUSE_ID,
            w.WAREHOUSE_NAME `S_Loc`
        ");
        $this->db->from('inventory_out a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
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
            a.INVENTORY_OUT_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO PO_Customer,
            a.DOCUMENT_DATE Tanggal,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Customer,
            k.KARYAWAN_ID,
            k.FIRST_NAME Sales,
            w.WAREHOUSE_ID,
            w.WAREHOUSE_NAME `S_Loc`
        ");
        $this->db->from('inventory_out a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('karyawan k', 'a.KARYAWAN_ID = k.KARYAWAN_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_inventory_out_id($inventory_out_id, $limit = null, $start = null)
    {
        $sql = "SELECT
            i.ITEM_DESCRIPTION Nama_Item,
            i.ITEM_CODE Kode_Item,
            a.ENTERED_QTY Qty,
            a.ENTERED_UOM UoM,
            b.DOCUMENT_NO No_MR,
            w.WAREHOUSE_NAME S_Loc,
            a.NOTE Note,
            a.INVENTORY_OUT_DETAIL_ID,
            a.INVENTORY_OUT_ID,
            a.BUILD_ID,
            w.WAREHOUSE_ID 
        FROM
            inventory_out_detail a
            JOIN item i ON a.ITEM_ID = i.ITEM_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN build b ON a.BUILD_ID = b.BUILD_ID 
        WHERE
            a.INVENTORY_OUT_ID = '{$inventory_out_id}'";

        if ($limit !== null && $start !== null) {
            $sql .= " LIMIT {$start}, {$limit}";
        }

        return $this->db->query($sql);
    }

    public function get_detail_by_so_id($so_id, $customer, $storage, $limit = null, $start = null)
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $sql = "SELECT DISTINCT
            a.DOCUMENT_REFF_NO,
            a.KARYAWAN_ID,
            a.PO_NO,
            a.PPN_CODE,
            a.PPN_PERCEN,
            b.SO_DETAIL_ID,
            b.SO_ID,
            b.BUILD_ID,
            i.ITEM_ID,
            bl.DOCUMENT_NO,
            i.ITEM_CODE,
            i.ITEM_DESCRIPTION,
            b.ENTERED_QTY,
            b.BASE_QTY,
            k.FIRST_NAME,
            k.LAST_NAME,
            b.DISCOUNT_PERCEN,
        CASE
                WHEN b.BASE_QTY = 0
                OR b.BASE_QTY IS NULL THEN
                    b.ENTERED_QTY ELSE b.ENTERED_QTY - ( b.RECEIVED_ENTERED_QTY / b.BASE_QTY )
                    END AS BALANCE,
                b.ENTERED_UOM,
                b.UNIT_PRICE,
                b.DISCOUNT_PRICE,
                b.SUBTOTAL,
                    IF(
                    bl.ITEM_ID IS NULL,
                    bd.UNIT_PRICE - bd.DISCOUNT_PRICE,
                    bl.TOTAL_AMOUNT
                ) AS HPP,
                bl.TOTAL_AMOUNT AS HPP,
                b.HARGA_INPUT,
                b.DISKON_INPUT,
                i.BERAT,
                b.NOTE,
                b.DESKRIPSI
            FROM
                so a
                JOIN so_detail b ON a.SO_ID = b.SO_ID
                JOIN build bl ON b.BUILD_ID = bl.BUILD_ID
                JOIN build_detail bd ON bl.BUILD_ID = bd.BUILD_ID
                JOIN item i ON b.ITEM_ID = i.ITEM_ID
                JOIN person psn ON a.PERSON_ID = psn.PERSON_ID
                JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
                JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID
            WHERE
                b.ENTERED_QTY > 0
                AND b.BASE_QTY > 0
                AND b.RECEIVED_ENTERED_QTY < b.ENTERED_QTY * b.BASE_QTY / NULLIF( b.RECEIVED_BASE_QTY, 0 )
                AND bl.APPROVED_FLAG = 'Y'
                AND bl.DOCUMENT_TYPE_ID = {$tipe_id['TYPE_ID']}
                AND w.WAREHOUSE_ID = {$storage}
                AND psn.PERSON_ID = {$customer}
                AND a.SO_ID = {$so_id}
            ORDER BY
            bl.DOCUMENT_DATE DESC,
            b.SO_DETAIL_ID";

        if ($limit !== null && $start !== null) {
            $sql .= " LIMIT {$start}, {$limit}";
        }

        return $this->db->query($sql);
    }

    public function count_detail_by_inventory_out_id($inventory_out_id)
    {
        $sql = "SELECT count(*) as total
        FROM
            inventory_out_detail a
            JOIN item i ON a.ITEM_ID = i.ITEM_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN build b ON a.BUILD_ID = b.BUILD_ID 
        WHERE
            a.INVENTORY_OUT_ID = '{$inventory_out_id}'";

        return $this->db->query($sql)->row()->total;
    }

    public function count_detail_by_so_id($so_id, $customer, $storage)
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $sql = "SELECT COUNT(*) as total
            FROM
                so a
                JOIN so_detail b ON a.SO_ID = b.SO_ID
                JOIN build bl ON b.BUILD_ID = bl.BUILD_ID
                JOIN item i ON b.ITEM_ID = i.ITEM_ID
                JOIN person psn ON a.PERSON_ID = psn.PERSON_ID
                JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
                JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID 
            WHERE
                b.ENTERED_QTY > 0 
                AND b.BASE_QTY > 0 
                AND b.RECEIVED_ENTERED_QTY < b.ENTERED_QTY * b.BASE_QTY / NULLIF( b.RECEIVED_BASE_QTY, 0 ) 
                AND bl.APPROVED_FLAG = 'Y' 
                AND bl.DOCUMENT_TYPE_ID = {$tipe_id['TYPE_ID']}
                AND w.WAREHOUSE_ID = {$storage}
                AND psn.PERSON_ID = {$customer}
                AND a.SO_ID = {$so_id}
            ORDER BY
            bl.DOCUMENT_DATE DESC,
            b.SO_DETAIL_ID";

        return $this->db->query($sql)->row()->total;
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

    public function get_customer()
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
                    AND a.FLAG_SUPP = 0 
            ORDER BY
                a.PERSON_NAME");
    }

    public function get_inventory_out_id($id)
    {
        $this->db->select('inventory_out.*,karyawan.FIRST_NAME, karyawan.LAST_NAME');
        $this->db->from('inventory_out');
        $this->db->join('karyawan', 'karyawan.KARYAWAN_ID = inventory_out.KARYAWAN_ID');
        $this->db->where('INVENTORY_OUT_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('INVENTORY_OUT_ID', $id);
        $this->db->delete('inventory_out_detail');

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
        $this->db->where('INVENTORY_OUT_ID', $id);
        $this->db->update('inventory_out', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }
}
