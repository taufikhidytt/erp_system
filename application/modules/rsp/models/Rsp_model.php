<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rsp_model extends CI_Model
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
        "CONCAT(p . PERSON_NAME, ' - [', p . PERSON_CODE, ']')",
        "w.WAREHOUSE_NAME",
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' )",
        "w.WAREHOUSE_NAME",
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.TAG_PINJAM_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Supplier,
            w.WAREHOUSE_NAME Main_Storage
        ");
        $this->db->from('tag_pinjam a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
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
            a.TAG_PINJAM_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Supplier,
            w.WAREHOUSE_NAME Main_Storage
        ");
        $this->db->from('tag_pinjam a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_tag_pinjam_id($tag_pinjam_id, $limit = null, $start = null)
    {
        $sql = "SELECT
                    tmp.* 
                FROM
                    (
                    SELECT
                        i.ITEM_DESCRIPTION Nama_Item,
                        i.ITEM_CODE Kode_Item,
                        tkd.ENTERED_QTY Qty,
                        tkd.ENTERED_UOM UoM,
                        pr.DOCUMENT_NO No_Reff_1,
                        po.DOCUMENT_NO No_Reff_2,
                        tkd.NOTE Note,
                        tkd.TAG_PINJAM_ID,
                        tkd.TAG_PINJAM_DETAIL_ID,
                        tkd.PO_DETAIL_ID,
                        NULL TAG_DETAIL_ID 
                    FROM
                        tag_pinjam_detail tkd
                        JOIN item i ON tkd.ITEM_ID = i.ITEM_ID
                        JOIN po_detail pod ON tkd.PO_DETAIL_ID = pod.PO_DETAIL_ID
                        JOIN po ON pod.PO_ID = po.PO_ID
                        JOIN pr_detail prd ON pod.PR_DETAIL_ID = prd.PR_DETAIL_ID
                        JOIN pr ON prd.PR_ID = pr.PR_ID UNION ALL
                    SELECT
                        i.ITEM_DESCRIPTION Nama_Item,
                        i.ITEM_CODE Kode_Item,
                        tkd.ENTERED_QTY Qty,
                        tkd.ENTERED_UOM UoM,
                        pr.DOCUMENT_NO No_Reff_1,
                        tg.DOCUMENT_NO No_Reff_2,
                        tkd.NOTE Note,
                        tkd.TAG_PINJAM_ID,
                        tkd.TAG_PINJAM_DETAIL_ID,
                        tkd.PO_DETAIL_ID,
                        td.TAG_DETAIL_ID 
                    FROM
                        tag_pinjam_detail tkd
                        JOIN item i ON tkd.ITEM_ID = i.ITEM_ID
                        JOIN tag_detail td ON tkd.TAG_DETAIL_ID = td.TAG_DETAIL_ID
                        JOIN tag tg ON td.TAG_ID = tg.TAG_ID
                        JOIN po_detail pod ON td.PO_DETAIL_ID = pod.PO_DETAIL_ID
                        JOIN pr_detail prd ON pod.PR_DETAIL_ID = prd.PR_DETAIL_ID
                        JOIN pr ON prd.PR_ID = pr.PR_ID 
                    ) AS tmp 
                WHERE
                    tmp.TAG_PINJAM_ID = '{$tag_pinjam_id}'";

        if ($limit !== null && $start !== null) {
            $sql .= " LIMIT {$start}, {$limit}";
        }

        return $this->db->query($sql);
    }

    public function count_detail_by_tag_pinjam_id($tag_pinjam_id)
    {
        return $this->db
            ->where('TAG_PINJAM_ID', $tag_pinjam_id)
            ->count_all_results('tag_pinjam_detail');
    }

    public function get_main_storage()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = {$this->session->userdata('id')} WHERE ACTIVE_FLAG = 'Y' AND a.JENIS_ID != FN_GET_VAR_VALUE ('KNY') GROUP BY a.WAREHOUSE_ID ORDER BY COALESCE(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function get_supplier()
    {
        return $this->db->query("SELECT a.PERSON_ID, a.PERSON_NAME Supplier, a.PERSON_CODE Kode FROM person a JOIN person_site b ON ( a.PERSON_ID = b.PERSON_ID ) WHERE a.FLAG_SUPP = 1 AND a.ACTIVE_FLAG = 'Y' GROUP BY a.PERSON_ID ORDER BY a.PERSON_NAME");
    }

    public function get_tag_pinjam_id($id)
    {
        $this->db->from('tag_pinjam');
        $this->db->where('tag_pinjam.TAG_PINJAM_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('TAG_PINJAM_ID', $id);
        $this->db->delete('tag_pinjam_detail');

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
        $this->db->where('TAG_PINJAM_ID', $id);
        $this->db->update('tag_pinjam', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }
}
