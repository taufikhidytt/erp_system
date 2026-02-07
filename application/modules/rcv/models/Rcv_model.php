<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rcv_model extends CI_Model
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
        "wh.WAREHOUSE_NAME",
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "wh.WAREHOUSE_NAME",
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.TAG_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            wh.WAREHOUSE_NAME Site
        ");
        $this->db->from('tag a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('warehouse wh', 'a.DEST_WH_ID = wh.WAREHOUSE_ID');
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
            a.TAG_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            wh.WAREHOUSE_NAME Site
        ");
        $this->db->from('tag a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('warehouse wh', 'a.DEST_WH_ID = wh.WAREHOUSE_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_tag_id($tag_id, $limit = null, $start = null)
    {
        $this->db->select("
            i.ITEM_DESCRIPTION Nama_Item,
            i.ITEM_CODE Kode_Item,
            td.ENTERED_QTY Qty,
            td.ENTERED_UOM UoM,
            tk.DOCUMENT_NO No_SJS,
            wh.WAREHOUSE_NAME S_Loc_In,
            td.NOTE Note,
            td.TAG_DETAIL_ID,
            td.TAG_KONSI_DETAIL_ID,
        ");
        $this->db->from("tag_detail td");
        $this->db->join("item i", "td.ITEM_ID = i.ITEM_ID");
        $this->db->join("tag_konsi_detail tkd", "td.TAG_KONSI_DETAIL_ID = tkd.TAG_KONSI_DETAIL_ID");
        $this->db->join("tag_konsi tk", "tkd.TAG_KONSI_ID = tk.TAG_KONSI_ID");
        $this->db->join("warehouse wh", "tk.WAREHOUSE_ID = wh.WAREHOUSE_ID");
        $this->db->where("td.TAG_ID", $tag_id);
        $this->db->order_by('td.TAG_ID', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_tag_id($tag_id)
    {
        $this->db->where('TAG_ID', $tag_id);
        return $this->db->count_all_results('tag_detail');
    }

    public function get_site_storage()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = {$this->session->userdata('id')} WHERE ACTIVE_FLAG = 'Y' AND a.JENIS_ID = FN_GET_VAR_VALUE ('KNY') GROUP BY a.WAREHOUSE_ID ORDER BY COALESCE(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function getTagId($id)
    {
        $this->db->from('tag');
        $this->db->where('tag.TAG_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('TAG_ID', $id);
        $this->db->delete('tag_detail');

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
        $this->db->where('TAG_ID', $id);
        $this->db->update('tag', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }
}
