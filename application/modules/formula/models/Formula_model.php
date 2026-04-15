<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Formula_model extends CI_Model
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
        "i.ITEM_DESCRIPTION",
        "a.UOM_CODE",
        "a.UNIT",
        "a.LOKASI",
        "a.START_DATE",
        "a.END_DATE",
        "a.ACTIVE_FLAG"
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "i.ITEM_DESCRIPTION",
        "a.UOM_CODE",
        "a.UNIT",
        "a.LOKASI",
        "a.START_DATE",
        "a.END_DATE",
        "a.ACTIVE_FLAG"
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->select("
            a.BOM_ID,
            a.DOCUMENT_NO AS No_Transaksi,
            a.DOCUMENT_REFF_NO AS No_Referensi,
            i.ITEM_ID,
            i.ITEM_DESCRIPTION AS Nama_Item,
            a.UOM_CODE AS UoM,
            a.UNIT AS Unit,
            a.LOKASI AS Code,
            a.START_DATE AS Start_Date,
            a.END_DATE AS End_Date,
            a.ACTIVE_FLAG,
            b.DISPLAY_NAME Status, b.MENU_ICON Warna_Status,
        ");
        $this->db->from('bom a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('item i', 'a.ITEM_ID = i.ITEM_ID');
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

        $this->db->select("
            a.BOM_ID,
            a.DOCUMENT_NO AS No_Transaksi,
            a.DOCUMENT_REFF_NO AS No_Referensi,
            i.ITEM_ID,
            i.ITEM_DESCRIPTION AS Nama_Item,
            a.UOM_CODE AS UoM,
            a.UNIT AS Unit,
            a.LOKASI AS Code,
            a.START_DATE AS Start_Date,
            a.END_DATE AS End_Date,
            a.ACTIVE_FLAG
        ");
        $this->db->from('bom a');
        $this->db->join('item i', 'a.ITEM_ID = i.ITEM_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_bom_id($bom_id, $limit = null, $start = null)
    {
        $sql = "SELECT
                i.ITEM_DESCRIPTION Nama_Item,
                i.ITEM_CODE Kode_Item,
                a.ENTERED_QTY Qty,
                a.ENTERED_UOM UoM,
                a.NOTE Note,
                a.BOM_DETAIL_ID -- BOM_DETAIL_ID tidak ditampilkan di WEB
            FROM
                bom_detail a
                JOIN item i
                    ON (a.ITEM_ID = i.ITEM_ID)
            WHERE a.BOM_ID = '{$bom_id}'";

        if ($limit !== null && $start !== null) {
            $sql .= " LIMIT {$start}, {$limit}";
        }
        return $this->db->query($sql);
    }

    public function count_detail_by_bom_id($bom_id)
    {
        return $this->db
            ->where('BOM_ID', $bom_id)
            ->count_all_results('bom_detail');
    }

    public function get_item_finish_goods()
    {
        return $this->db->query("SELECT DISTINCT
            i.ITEM_ID,
            i.ITEM_CODE,
            LEFT ( i.ITEM_DESCRIPTION, 40 ) AS ITEM_DESCRIPTION,
            LEFT ( i.ASSY_CODE, 30 ) AS ASSY_CODE,
            LEFT ( e.DISPLAY_NAME, 30 ) AS CATEGORY,
            i.UOM_CODE,
            COALESCE ( s.STOK, 0 ) AS STOK,
            mr.DISPLAY_NAME AS BRAND,
            tipe.DISPLAY_NAME AS TIPE,
            i.JENIS_ID,
            i.NOTE
        FROM
            item i
            JOIN erp_lookup_value e ON e.ERP_LOOKUP_VALUE_ID = i.GROUP_ID
            JOIN erp_lookup_value tipe ON i.TYPE_ID = tipe.ERP_LOOKUP_VALUE_ID
            JOIN erp_lookup_value mr ON i.MEREK_ID = mr.ERP_LOOKUP_VALUE_ID
            JOIN ( SELECT ITEM_ID, SUM( QTY_AWAL + QTY_MASUK - QTY_KELUAR ) AS STOK FROM item_stok GROUP BY ITEM_ID ) s ON i.ITEM_ID = s.ITEM_ID 
        WHERE
            i.ACTIVE_FLAG = 'Y' 
            AND i.APPROVE_FLAG = 'Y' 
            AND i.TYPE_ID = FN_GET_VAR_VALUE ( 'INV' ) 
            AND i.JENIS_ID = FN_GET_VAR_VALUE ( 'GOODS' ) 
            AND i.ITEM_KMS = 'N' 
        ORDER BY
            i.ITEM_CODE");
    }

    public function search_item_finish_goods($search = '')
    {
        $sql = "
            SELECT DISTINCT
                i.ITEM_ID,
                i.ITEM_CODE,
                LEFT(i.ITEM_DESCRIPTION, 40) AS ITEM_DESCRIPTION,
                i.NOTE
            FROM item i
            JOIN erp_lookup_value e ON e.ERP_LOOKUP_VALUE_ID = i.GROUP_ID
            JOIN erp_lookup_value tipe ON i.TYPE_ID = tipe.ERP_LOOKUP_VALUE_ID
            JOIN erp_lookup_value mr ON i.MEREK_ID = mr.ERP_LOOKUP_VALUE_ID
            WHERE
                i.ACTIVE_FLAG = 'Y'
                AND i.APPROVE_FLAG = 'Y'
                AND i.TYPE_ID = FN_GET_VAR_VALUE('INV')
                AND i.JENIS_ID = FN_GET_VAR_VALUE('GOODS')
                AND i.ITEM_KMS = 'N'
        ";

        // 🔍 FILTER SEARCH
        if (!empty($search)) {
            $sql .= " AND (
                i.ITEM_CODE LIKE '%" . $this->db->escape_like_str($search) . "%' 
                OR i.ITEM_DESCRIPTION LIKE '%" . $this->db->escape_like_str($search) . "%'
            )";
        }

        $sql .= " ORDER BY i.ITEM_CODE ASC LIMIT 50";

        return $this->db->query($sql)->result();
    }

    public function get_bom_id($id)
    {
        $this->db->from('bom');
        $this->db->where('BOM_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('BOM_ID', $id);
        $this->db->delete('bom_detail');

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
        $this->db->where('BOM_ID', $id);
        $this->db->update('bom', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }

    public function get_formula_detail($id)
    {
        $this->db->select("a.*, i.ITEM_NAME");
        $this->db->from('bom a');
        $this->db->join('item i', 'a.ITEM_ID = i.ITEM_ID');
        $this->db->where('a.BOM_ID', $id);

        return $this->db->get();
    }
}
