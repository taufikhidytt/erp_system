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
        "i.ITEM_DESCRIPTION",
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
        "i.ITEM_DESCRIPTION",
        "i.ITEM_CODE",
        "tmp.QTY_MR",
        "tmp.QTY_SO",
        "tmp.QTY_SISA",
        "tmp.ENTERED_UOM",
    );

    var $order = array('tmp.DOCUMENT_DATE' => 'DESC');

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
                i.ITEM_DESCRIPTION,
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
}
