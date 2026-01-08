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
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "a.NEED_DATE",
        "CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' )",
        "w.WAREHOUSE_NAME",
        "a.TOTAL_AMOUNT",
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "a.NEED_DATE",
        "CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' )",
        "w.WAREHOUSE_NAME",
        "a.TOTAL_AMOUNT",
    );

    var $order = array('a.PR_ID' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON ( a.TABLE_NAME = b.TABLE_NAME ) WHERE b.PROMPT = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.PR_ID,
            b.DISPLAY_NAME Status,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            a.NEED_DATE Dibutuhkan,
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
        $this->db->order_by('a.DOCUMENT_DATE', 'desc');

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
        $this->db->select('
            i.ITEM_ID AS ID,
            i.ITEM_CODE KODE_ITEM,
            LEFT(i.ITEM_DESCRIPTION, 30) NAMA_ITEM,
            i.PART_NUMBER PART_NUMBER,
            i.UOM_CODE UOM,
            a.DISPLAY_NAME JENIS,
            b.DISPLAY_NAME KATEGORY,
            c.DISPLAY_NAME MADE_IN,
            d.DISPLAY_NAME KOMODITI,
            e.DISPLAY_NAME BRAND,
            f.DISPLAY_NAME TRADE,
            i.PRICE_LAST_BUY,
            i.PRICE_LAST_SELL,
            i.LEAD_TIME,
            i.ITEM_KMS KONSY,
            i.APPROVE_FLAG APPROVED,
            i.OBSOLETE_FLAG OBSOLETE');
        $this->db->from('item i');
        $this->db->join('erp_lookup_value a', 'i.JENIS_ID = a.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value b', 'i.GROUP_ID = b.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value c', 'i.MADE_IN_ID = c.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value d', 'i.TIPE_ID = d.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value e', 'i.MEREK_ID = e.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value f', 'i.TYPE_ID = f.ERP_LOOKUP_VALUE_ID', 'left');
        return $this->db->count_all_results();
    }

    public function get_detail_by_pr_id($pr_id)
    {
        $this->db->select("i.ITEM_DESCRIPTION Item_Name, i.ITEM_CODE, d.ENTERED_UOM, d.NOTE, d.ENTERED_QTY AS QTY, d.UNIT_PRICE AS PRICE, d.SUBTOTAL AS TOTAL");
        $this->db->from("pr_detail d");
        $this->db->join("item i", "d.ITEM_ID = i.ITEM_ID");
        $this->db->where("d.PR_ID", $pr_id);
        return $this->db->get();
    }

    public function getSupplier()
    {
        return $this->db->query("SELECT a.PERSON_ID, a.PERSON_NAME Supplier, a.PERSON_CODE Kode FROM person a JOIN person_site b ON (a.PERSON_ID = b.PERSON_ID) WHERE a.FLAG_SUPP = 1 AND a.ACTIVE_FLAG = 'Y' GROUP BY a.PERSON_ID ORDER BY a.PERSON_NAME");
    }

    public function getGudang()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = '1' WHERE ACTIVE_FLAG = 'Y' GROUP BY a.WAREHOUSE_ID ORDER BY IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
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
}
