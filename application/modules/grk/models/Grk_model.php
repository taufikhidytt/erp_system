<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Grk_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        null,
        "a.PO_ID",
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "CONCAT(
            p . PERSON_NAME,
            ' - [',
            p . PERSON_CODE,
            ']'
        )",
        "w.WAREHOUSE_NAME"
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "CONCAT(
            p . PERSON_NAME,
            ' - [',
            p . PERSON_CODE,
            ']'
        )",
        "w.WAREHOUSE_NAME"
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.PO_ID,
            b.DISPLAY_NAME `Status`,
            a.DOCUMENT_NO `No_Transaksi`,
            a.DOCUMENT_REFF_NO `No_Referensi`,
            a.DOCUMENT_DATE `Tanggal`,
            a.NEED_DATE `Dibutuhkan`,
            CONCAT(
                p.PERSON_NAME,
                ' - [',
                p.PERSON_CODE,
                ']'
            ) `Supplier`,
            w.WAREHOUSE_NAME `Gudang`
        ");
        $this->db->from('po a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
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
            a.PO_ID,
            b.DISPLAY_NAME `Status`,
            a.DOCUMENT_NO `No_Transaksi`,
            a.DOCUMENT_REFF_NO `No_Referensi`,
            a.DOCUMENT_DATE `Tanggal`,
            a.NEED_DATE `Dibutuhkan`,
            CONCAT(
                p.PERSON_NAME,
                ' - [',
                p.PERSON_CODE,
                ']'
            ) `Supplier`,
            w.WAREHOUSE_NAME `Gudang`
        ");
        $this->db->from('po a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_pr_id($po_id, $limit = null, $start = null)
    {
        $this->db->select("
            i.ITEM_DESCRIPTION Nama_Item,
            i.ITEM_CODE Kode_Item,
            pd.ENTERED_QTY Qty,
            pd.ENTERED_UOM UoM,
            pd.HARGA_INPUT Harga,
            pd.SUBTOTAL Subtotal,
            pr.DOCUMENT_NO No_FPK,
            k.FIRST_NAME Sales,
            pd.NOTE Note,
            pd.PO_DETAIL_ID,
            pd.PR_DETAIL_ID,
            IF(pd.ENTERED_UOM = i.UOM_CODE,(pd.ENTERED_QTY * pd.BASE_QTY - pd.RECEIVED_ENTERED_QTY * pd.RECEIVED_BASE_QTY),(pd.ENTERED_QTY - (pd.RECEIVED_ENTERED_QTY / pd.BASE_QTY))) AS Sisa
        ");
        $this->db->from("po_detail pd");
        $this->db->join("item i", "pd.ITEM_ID = i.ITEM_ID");
        $this->db->join("pr_detail prd", "pd.PR_DETAIL_ID = prd.PR_DETAIL_ID");
        $this->db->join("pr", "prd.PR_ID = pr.PR_ID");
        $this->db->join("karyawan k", "pr.KARYAWAN_ID = k.KARYAWAN_ID");
        $this->db->where("pd.PO_ID", $po_id);
        $this->db->order_by('pd.PR_DETAIL_ID', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_pr_id($po_id)
    {
        $this->db->where('PO_ID', $po_id);
        return $this->db->count_all_results('po_detail');
    }

    public function getSupplier()
    {
        return $this->db->query("SELECT a.PERSON_ID, a.PERSON_NAME Supplier, a.PERSON_CODE Kode FROM person a JOIN person_site b ON (a.PERSON_ID = b.PERSON_ID) WHERE a.FLAG_SUPP = 1 AND a.ACTIVE_FLAG = 'Y' GROUP BY a.PERSON_ID ORDER BY a.PERSON_NAME");
    }

    public function getGudang()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = {$this->session->userdata('id')} WHERE ACTIVE_FLAG = 'Y' GROUP BY a.WAREHOUSE_ID ORDER BY IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function getPoId($id)
    {
        $this->db->from('po');
        $this->db->where('po.PO_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('PO_ID', $id);
        $this->db->delete('po_detail');
        return ($this->db->error()['code'] == 0);
    }

    public function updateStatus($id, $status)
    {
        $params = array(
            'STATUS_ID' => $status,
        );
        $this->db->where('PO_ID', $id);
        $this->db->update('po', $params);
        return ($this->db->error()['code'] == 0);
    }
}
