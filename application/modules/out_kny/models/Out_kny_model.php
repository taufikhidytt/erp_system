<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Out_kny_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        null,
        "a.DOCUMENT_DATE",
        "bd.DOCUMENT_NO",
        "bd.DOCUMENT_REFF_NO",
        "p.PERSON_NAME",
        "i.ITEM_DESCRIPTION",
        "i.ITEM_CODE",
        "b.ENTERED_QTY",
        "b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0)",
        "b.ENTERED_QTY - (b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0))",
        "b.ENTERED_UOM",
    );

    var $column_search = array(
        null,
        "a.DOCUMENT_DATE",
        "bd.DOCUMENT_NO",
        "bd.DOCUMENT_REFF_NO",
        "p.PERSON_NAME",
        "i.ITEM_DESCRIPTION",
        "i.ITEM_CODE",
        "b.ENTERED_QTY",
        "b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0)",
        "b.ENTERED_QTY - (b.INVOICE_ENTERED_QTY / NULLIF(b.BASE_QTY,0))",
        "b.ENTERED_UOM",
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
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
            a.WAREHOUSE_ID,
            w.WAREHOUSE_NAME,
            i.ITEM_ID,
            i.ITEM_CODE,
            i.ITEM_DESCRIPTION,
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
            a.WAREHOUSE_ID,
            w.WAREHOUSE_NAME,
            i.ITEM_ID,
            i.ITEM_CODE,
            i.ITEM_DESCRIPTION,
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

    public function get_datatables_export()
    {
        $this->_get_datatables_query();
        return $this->db->get()->result();
    }
}
