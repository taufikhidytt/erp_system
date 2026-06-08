<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Out_so_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        "tmp.DOCUMENT_DATE",
        "tmp.DOCUMENT_NO",
        "tmp.DOCUMENT_REFF_NO",
        "w.WAREHOUSE_NAME",
        "CONCAT(p.PERSON_NAME, ' ', p.PERSON_CODE)",
        "COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION)",
        "i.ITEM_CODE",
        "tmp.QTY_MR",
        "tmp.QTY_SO",
        "tmp.QTY_SISA",
        "tmp.ENTERED_UOM",
    );

    var $column_search = array(
        null,
        "tmp.DOCUMENT_DATE",
        "tmp.DOCUMENT_NO",
        "tmp.DOCUMENT_REFF_NO",
        "w.WAREHOUSE_NAME",
        "CONCAT(p.PERSON_NAME, ' ', p.PERSON_CODE)",
        "COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION)",
        "i.ITEM_CODE",
        "tmp.QTY_MR",
        "tmp.QTY_SO",
        "tmp.QTY_SISA",
        "tmp.ENTERED_UOM",
    );

    var $order = array('tmp.DOCUMENT_DATE' => 'DESC');

    var $column_order_mr_po = array(
        null,
        "a.DOCUMENT_DATE",
        "bd.DOCUMENT_NO",
        "bd.DOCUMENT_REFF_NO",
        "W.WAREHOUSE_NAME",
        "supplier",
        "COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION)",
        "i.ITEM_CODE",
        "b.ENTERED_QTY",
        "QTY_PO",
        "QTY_SISA",
        "b.ENTERED_UOM",
    );

    var $column_search_mr_po = array(
        null,
        "a.DOCUMENT_DATE",
        "bd.DOCUMENT_NO",
        "bd.DOCUMENT_REFF_NO",
        "W.WAREHOUSE_NAME",
        "CONCAT(p.PERSON_NAME, ' ', p.PERSON_CODE)",
        "COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION)",
        "i.ITEM_CODE",
        "b.ENTERED_QTY",
        "b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0)",
        "b.ENTERED_QTY - (b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0))",
        "b.ENTERED_UOM",
    );

    var $order_mr_po = array('a.DOCUMENT_DATE' => 'ASC');

    private function _get_datatables_query($count = false, $export = false)
    {
        // --- SUBQUERY 1: Data dari build (header) yang memiliki ITEM_ID ---
        $sub1 = "
            SELECT
                a.BUILD_ID AS ID,
                a.DOCUMENT_TYPE_ID,
                a.STATUS_ID,
                FN_GET_VAR_NAME(a.STATUS_ID) AS STATUS_NAME,
                a.DOCUMENT_DATE,
                a.DOCUMENT_NO,
                a.DOCUMENT_REFF_NO,
                a.PERSON_ID,
                a.WAREHOUSE_ID,
                a.ITEM_ID,
                a.ENTERED_QTY AS QTY_MR,
                a.BASE_QTY,
                a.RECEIVED_ENTERED_QTY / a.BASE_QTY AS QTY_SO,
                a.ENTERED_QTY - (a.RECEIVED_ENTERED_QTY / a.BASE_QTY) AS QTY_SISA,
                a.ENTERED_UOM
            FROM build a
            WHERE (a.ENTERED_QTY * a.BASE_QTY) > 0
              AND (a.RECEIVED_ENTERED_QTY * a.RECEIVED_BASE_QTY) < (a.ENTERED_QTY * a.BASE_QTY)
              AND a.STATUS_ID IN (FN_GET_VAR_VALUE('NEW'), FN_GET_VAR_VALUE('PARTIAL'))
              AND a.DOCUMENT_TYPE_ID = 3
              AND COALESCE(a.ITEM_ID, 0) != 0
        ";

        // --- SUBQUERY 2: Data dari build_detail (item di detail) ---
        $sub2 = "
            SELECT
                b.BUILD_DETAIL_ID AS ID,
                a.DOCUMENT_TYPE_ID,
                a.STATUS_ID,
                FN_GET_VAR_NAME(a.STATUS_ID) AS STATUS_NAME,
                a.DOCUMENT_DATE,
                a.DOCUMENT_NO,
                a.DOCUMENT_REFF_NO,
                a.PERSON_ID,
                b.WAREHOUSE_ID,
                b.ITEM_ID,
                b.ENTERED_QTY AS QTY_MR,
                b.BASE_QTY,
                b.SO_QTY / b.BASE_QTY AS QTY_SO,
                b.ENTERED_QTY - (b.SO_QTY / b.BASE_QTY) AS QTY_SISA,
                b.ENTERED_UOM
            FROM build a
            JOIN build_detail b ON a.BUILD_ID = b.BUILD_ID
            WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
              AND (b.SO_QTY * b.SO_BASE_QTY) < (b.ENTERED_QTY * b.BASE_QTY)
              AND a.STATUS_ID IN (FN_GET_VAR_VALUE('NEW'), FN_GET_VAR_VALUE('PARTIAL'))
              AND a.DOCUMENT_TYPE_ID = 3
              AND COALESCE(a.ITEM_ID, 0) = 0
        ";

        $union = "$sub1 UNION ALL $sub2";

        // --- OUTER QUERY untuk filtering ---
        $sql = "SELECT " . ($count ? "COUNT(*) as total" : "
                tmp.ID,
                tmp.DOCUMENT_TYPE_ID,
                tmp.STATUS_ID,
                tmp.STATUS_NAME,
                tmp.DOCUMENT_DATE,
                tmp.DOCUMENT_NO,
                tmp.DOCUMENT_REFF_NO,
                p.PERSON_ID,
                p.PERSON_CODE,
                p.PERSON_NAME,
                w.WAREHOUSE_ID,
                w.WAREHOUSE_NAME,
                i.ITEM_ID,
                i.ITEM_CODE,
                COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION) AS ITEM_DESCRIPTION,
                tmp.QTY_MR,
                tmp.BASE_QTY,
                tmp.QTY_SO,
                tmp.QTY_SISA,
                tmp.ENTERED_UOM
            ") . "
            FROM ($union) tmp
            JOIN person p ON tmp.PERSON_ID = p.PERSON_ID
            JOIN warehouse w ON tmp.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN item i ON tmp.ITEM_ID = i.ITEM_ID
        ";

        // --- FILTER PENCARIAN PER COLUMN ---
        $search_conditions = [];

        // Loop melalui setiap column dari datatables
        if (isset($_POST['columns']) && is_array($_POST['columns'])) {
            foreach ($_POST['columns'] as $index => $column) {
                // Cek apakah column ini searchable dan ada search value
                if (
                    isset($column['searchable']) && $column['searchable'] == 'true' &&
                    isset($column['search']['value']) && !empty($column['search']['value'])
                ) {

                    // Dapatkan kolom database dari array column_search
                    if (isset($this->column_search[$index]) && $this->column_search[$index] !== null) {
                        $db_column = $this->column_search[$index];
                        $search_value = $column['search']['value'];
                        $search_conditions[] = "$db_column LIKE '%" . $this->db->escape_like_str($search_value) . "%'";
                    }
                }
            }
        }

        // Tambahkan WHERE clause jika ada search conditions
        if (!empty($search_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $search_conditions);
        }

        // --- ORDERING ---
        if (!$count && !$export) {
            if (isset($_POST['order'])) {
                $col_index = $_POST['order']['0']['column'];
                $dir = $_POST['order']['0']['dir'];
                if (isset($this->column_order[$col_index]) && $this->column_order[$col_index] !== null) {
                    $sql .= " ORDER BY " . $this->column_order[$col_index] . " " . $dir;
                }
            } else {
                $sql .= " ORDER BY tmp.DOCUMENT_DATE ASC, tmp.DOCUMENT_NO ASC, tmp.ID ASC";
            }

            // --- LIMIT ---
            if ($_POST['length'] != -1) {
                $sql .= " LIMIT " . $_POST['start'] . ", " . $_POST['length'];
            }
        }

        return $sql;
    }

    function get_datatables()
    {
        $sql = $this->_get_datatables_query(false, false);
        return $this->db->query($sql)->result();
    }

    function count_filtered()
    {
        $sql = $this->_get_datatables_query(true, false);
        $result = $this->db->query($sql)->row();
        return $result->total;
    }

    function count_all()
    {
        $sub1 = "
            SELECT a.BUILD_ID AS ID, a.PERSON_ID, a.WAREHOUSE_ID, a.ITEM_ID
            FROM build a
            WHERE (a.ENTERED_QTY * a.BASE_QTY) > 0
              AND (a.RECEIVED_ENTERED_QTY * a.RECEIVED_BASE_QTY) < (a.ENTERED_QTY * a.BASE_QTY)
              AND a.STATUS_ID IN (FN_GET_VAR_VALUE('NEW'), FN_GET_VAR_VALUE('PARTIAL'))
              AND a.DOCUMENT_TYPE_ID = 3
              AND COALESCE(a.ITEM_ID, 0) != 0
        ";

        $sub2 = "
            SELECT b.BUILD_DETAIL_ID AS ID, a.PERSON_ID, b.WAREHOUSE_ID, b.ITEM_ID
            FROM build a
            JOIN build_detail b ON a.BUILD_ID = b.BUILD_ID
            WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
              AND (b.SO_QTY * b.SO_BASE_QTY) < (b.ENTERED_QTY * b.BASE_QTY)
              AND a.STATUS_ID IN (FN_GET_VAR_VALUE('NEW'), FN_GET_VAR_VALUE('PARTIAL'))
              AND a.DOCUMENT_TYPE_ID = 3
              AND COALESCE(a.ITEM_ID, 0) = 0
        ";

        $sql = "SELECT COUNT(*) as total
            FROM ($sub1 UNION ALL $sub2) tmp
            JOIN person p ON tmp.PERSON_ID = p.PERSON_ID
            JOIN warehouse w ON tmp.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN item i ON tmp.ITEM_ID = i.ITEM_ID
        ";

        $result = $this->db->query($sql)->row();
        return $result->total;
    }

    public function get_datatables_export()
    {
        $sql = $this->_get_datatables_query(false, true);
        return $this->db->query($sql)->result();
    }

    private function _get_datatables_query_mr_po()
    {
        $this->db->select("
            b.INVENTORY_IN_DETAIL_ID,
            b.INVENTORY_IN_ID,
            b.BUILD_DETAIL_ID,
            a.DOCUMENT_TYPE_ID,
            a.STATUS_ID,
            FN_GET_VAR_NAME(a.STATUS_ID) AS STATUS_NAME,
            a.DOCUMENT_DATE,
            bd.DOCUMENT_NO,
            bd.DOCUMENT_REFF_NO,
            p.PERSON_ID,
            p.PERSON_CODE,
            p.PERSON_NAME,
            CONCAT(p.PERSON_NAME, ' ', p.PERSON_CODE) as supplier,
            a.WAREHOUSE_ID,
            w.WAREHOUSE_NAME,
            i.ITEM_ID,
            i.ITEM_CODE,
            COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION) AS ITEM_DESCRIPTION,
            b.ENTERED_QTY AS QTY_MR,
            b.BASE_QTY,
            b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0) AS QTY_PO,
            b.ENTERED_QTY - (b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0)) AS QTY_SISA,
            b.ENTERED_UOM,
            b.UNIT_PRICE,
            b.SUBTOTAL,
            b.HARGA_INPUT,
            i.LEAD_TIME,
            i.BERAT,
            b.NOTE
        ", false);
        $this->db->from('inventory_in a');
        $this->db->join('inventory_in_detail b', 'a.INVENTORY_IN_ID = b.INVENTORY_IN_ID');
        $this->db->join('item i', 'b.ITEM_ID = i.ITEM_ID');
        $this->db->join('warehouse w', 'b.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('build_detail bdl', 'b.BUILD_DETAIL_ID = bdl.BUILD_DETAIL_ID');
        $this->db->join('build bd', 'bdl.BUILD_ID = bd.BUILD_ID');
        $this->db->where('(b.ENTERED_QTY * b.BASE_QTY) > 0', null, false);
        $this->db->where('(b.INVOICE_ENTERED_QTY * b.INVOICE_BASE_QTY) < (b.ENTERED_QTY * b.BASE_QTY)', null, false);
        $this->db->where("a.STATUS_ID IN (FN_GET_VAR_VALUE('NEW'), FN_GET_VAR_VALUE('PARTIAL'))", null, false);
        $this->db->where('bd.DOCUMENT_TYPE_ID', 3);

        $i = 0;
        foreach ($this->column_search_mr_po as $item) {
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
                if (count($this->column_search_mr_po) - 1 == $i) $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by(
                $this->column_order_mr_po[$_POST['order']['0']['column']],
                $_POST['order']['0']['dir']
            );
        } elseif (isset($this->order_mr_po)) {
            $order = $this->order_mr_po;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables_mr_po()
    {
        $this->_get_datatables_query_mr_po();
        if ($_POST['length'] != -1)
            $this->db->limit(
                $_POST['length'],
                $_POST['start']
            );
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_mr_po()
    {
        $this->_get_datatables_query_mr_po();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all_mr_po()
    {
        $this->db->select("
            b.INVENTORY_IN_DETAIL_ID,
            b.INVENTORY_IN_ID,
            b.BUILD_DETAIL_ID,
            a.DOCUMENT_TYPE_ID,
            a.STATUS_ID,
            FN_GET_VAR_NAME(a.STATUS_ID) AS STATUS_NAME,
            a.DOCUMENT_DATE,
            bd.DOCUMENT_NO,
            bd.DOCUMENT_REFF_NO,
            p.PERSON_ID,
            p.PERSON_CODE,
            p.PERSON_NAME,
            CONCAT(p.PERSON_NAME, ' ', p.PERSON_CODE) as supplier,
            a.WAREHOUSE_ID,
            w.WAREHOUSE_NAME,
            i.ITEM_ID,
            i.ITEM_CODE,
            COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION) AS ITEM_DESCRIPTION,
            b.ENTERED_QTY AS QTY_MR,
            b.BASE_QTY,
            b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0) AS QTY_PO,
            b.ENTERED_QTY - (b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0)) AS QTY_SISA,
            b.ENTERED_UOM,
            b.UNIT_PRICE,
            b.SUBTOTAL,
            b.HARGA_INPUT,
            i.LEAD_TIME,
            i.BERAT,
            b.NOTE
        ", false);
        $this->db->from('inventory_in a');
        $this->db->join('inventory_in_detail b', 'a.INVENTORY_IN_ID = b.INVENTORY_IN_ID');
        $this->db->join('item i', 'b.ITEM_ID = i.ITEM_ID');
        $this->db->join('warehouse w', 'b.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('build_detail bdl', 'b.BUILD_DETAIL_ID = bdl.BUILD_DETAIL_ID');
        $this->db->join('build bd', 'bdl.BUILD_ID = bd.BUILD_ID');
        $this->db->where('(b.ENTERED_QTY * b.BASE_QTY) > 0', null, false);
        $this->db->where('(b.INVOICE_ENTERED_QTY * b.INVOICE_BASE_QTY) < (b.ENTERED_QTY * b.BASE_QTY)', null, false);
        $this->db->where("a.STATUS_ID IN (FN_GET_VAR_VALUE('NEW'), FN_GET_VAR_VALUE('PARTIAL'))", null, false);
        $this->db->where('bd.DOCUMENT_TYPE_ID', 3);
        return $this->db->count_all_results();
    }

    public function get_datatables_export_mr_po()
    {
        $this->_get_datatables_query_mr_po();
        return $this->db->get()->result();
    }
}
