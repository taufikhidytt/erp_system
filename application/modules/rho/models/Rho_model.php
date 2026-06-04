<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Rho_model extends CI_Model
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
        "w.WAREHOUSE_NAME",
        "wh.WAREHOUSE_NAME",
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "w.WAREHOUSE_NAME",
        "wh.WAREHOUSE_NAME",
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.REQUEST_QTY_ID,
            b.DISPLAY_NAME STATUS, b.MENU_ICON WARNA_STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            w.WAREHOUSE_NAME Main_Storage,
            wh.WAREHOUSE_NAME Site_Storage
        ");
        $this->db->from('request_qty a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('warehouse wh', 'a.TO_WH_ID = wh.WAREHOUSE_ID');
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
            a.REQUEST_QTY_ID,
            b.DISPLAY_NAME STATUS,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            w.WAREHOUSE_NAME Main_Storage,
            wh.WAREHOUSE_NAME Site_Storage
        ");
        $this->db->from('request_qty a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('warehouse wh', 'a.TO_WH_ID = wh.WAREHOUSE_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_request_qty_id($request_qty_id, $limit = null, $start = null)
    {
        $this->db->select("
            COALESCE(i.PART_NUMBER,i.ITEM_DESCRIPTION) AS Nama_Item,
            i.ITEM_CODE Kode_Item,
            rqd.ENTERED_QTY Qty,
            IF(
                rqd.ENTERED_UOM = i.UOM_CODE,
                (
                    rqd.ENTERED_QTY * rqd.BASE_QTY - rqd.DELIVER_QTY * rqd.DELIVER_BASE_QTY
                ),
                (
                    rqd.ENTERED_QTY - (rqd.DELIVER_QTY / rqd.BASE_QTY)
                )
            ) Sisa,
            rqd.ENTERED_UOM UoM,
            tag.DOCUMENT_NO No_RCV,
            rqd.NOTE Note,
            rqd.REQUEST_QTY_DETAIL_ID,
            rqd.TAG_DETAIL_ID
        ");
        $this->db->from("request_qty_detail rqd");
        $this->db->join("item i", "rqd.ITEM_ID = i.ITEM_ID");
        $this->db->join("tag_detail td", "rqd.TAG_DETAIL_ID = td.TAG_DETAIL_ID");
        $this->db->join("tag", "td.TAG_ID = tag.TAG_ID");
        $this->db->where("rqd.REQUEST_QTY_ID", $request_qty_id);
        $this->db->order_by('td.REQUEST_QTY_DETAIL_ID', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_request_qty_id($request_qty_id)
    {
        $this->db->where('REQUEST_QTY_ID', $request_qty_id);
        return $this->db->count_all_results('request_qty_detail');
    }

    public function get_site_storage()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = {$this->session->userdata('id')} WHERE ACTIVE_FLAG = 'Y' AND a.JENIS_ID = FN_GET_VAR_VALUE ('KNY') GROUP BY a.WAREHOUSE_ID ORDER BY COALESCE(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function get_main_storage()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = {$this->session->userdata('id')} WHERE ACTIVE_FLAG = 'Y' AND a.JENIS_ID = FN_GET_VAR_VALUE ('PST') GROUP BY a.WAREHOUSE_ID ORDER BY COALESCE(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function getRequestQtyId($id)
    {
        $this->db->from('request_qty');
        $this->db->where('request_qty.REQUEST_QTY_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('REQUEST_QTY_ID', $id);
        $this->db->delete('request_qty_detail');

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
        $this->db->where('REQUEST_QTY_ID', $id);
        $this->db->update('request_qty', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }

    public function get_rho_detail($id)
    {
        $this->db->select("
            a.DOCUMENT_DATE,a.DOCUMENT_NO,a.DOCUMENT_REFF_NO,a.TOTAL_AMOUNT,a.NOTE,
            w.WAREHOUSE_NAME,
            to_w.WAREHOUSE_NAME as TO_WAREHOUSE_NAME,
        ");
        $this->db->from('request_qty a');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('warehouse to_w', 'a.TO_WH_ID = to_w.WAREHOUSE_ID');
        $this->db->where('a.REQUEST_QTY_ID', $id);
        return $this->db->get();
    }

    public function get_log_user($id)
    {
        $this->db->select('a.CREATED_DATE,a.LAST_UPDATE_DATE,c.ERP_USER_NAME as USER_CREATED,u.ERP_USER_NAME as USER_UPDATED');
        $this->db->from('request_qty a');
        $this->db->join('erp_user c', 'a.CREATED_BY = c.ERP_USER_ID', 'left');
        $this->db->join('erp_user u', 'a.LAST_UPDATE_BY = u.ERP_USER_ID', 'left');
        $this->db->where('a.REQUEST_QTY_ID', $id);
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function getSiteStorage()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');
        $user_id    = $this->session->id;;

        $subquery = "(
                        SELECT
                            WAREHOUSE_ID,
                            MAX(PRIMARY_FLAG) AS PRIMARY_FLAG
                        FROM erp_warehouse
                        WHERE ERP_USER_ID = " . $this->db->escape($user_id) . "
                        GROUP BY WAREHOUSE_ID
                    ) g";

        $this->db
            ->select("a.WAREHOUSE_ID as id, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME as text ")
            ->from('warehouse a')
            ->join($subquery, 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'left')
            ->where('a.JENIS_ID = FN_GET_VAR_VALUE("KNY")', null, false)
            ->group_by('a.WAREHOUSE_ID')
            ->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)', 'DESC', false)
            ->order_by('a.WAREHOUSE_NAME', 'ASC');

        if ($user_id) {
            $this->db->where("
            (
                (
                    EXISTS (
                        SELECT 1
                        FROM erp_warehouse
                        WHERE ERP_USER_ID = " . $this->db->escape($user_id) . "
                    )
                    AND g.WAREHOUSE_ID IS NOT NULL
                )
                OR
                NOT EXISTS (
                    SELECT 1
                    FROM erp_warehouse
                    WHERE ERP_USER_ID = " . $this->db->escape($user_id) . "
                )
            )", null, false);
        }

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->where('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) = "Y"', null, false)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.WAREHOUSE_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        $this->db->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)', 'DESC', false);
        $this->db->order_by('a.WAREHOUSE_NAME', 'ASC');

        return $this->db->get();
    }

    public function getMainStorage()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');
        $user_id    = $this->session->id;

        $subquery = "
                    (
                        SELECT
                            WAREHOUSE_ID,
                            MAX(PRIMARY_FLAG) AS PRIMARY_FLAG
                        FROM erp_warehouse
                        WHERE ERP_USER_ID = " . $this->db->escape($user_id) . "
                        GROUP BY WAREHOUSE_ID
                    ) g";

        $this->db
            ->select("a.WAREHOUSE_ID as id, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME as text ")
            ->from('warehouse a')
            ->join($subquery, 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'left', false)
            ->where('a.JENIS_ID = FN_GET_VAR_VALUE("PST")', null, false)
            ->group_by('a.WAREHOUSE_ID')
            ->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)', 'DESC', false)
            ->order_by('a.WAREHOUSE_NAME', 'ASC');

        if ($user_id) {
            $this->db->where("
            (
                (
                    EXISTS (
                        SELECT 1
                        FROM erp_warehouse
                        WHERE ERP_USER_ID = " . $this->db->escape($user_id) . "
                    )
                    AND g.WAREHOUSE_ID IS NOT NULL
                )
                OR
                NOT EXISTS (
                    SELECT 1
                    FROM erp_warehouse
                    WHERE ERP_USER_ID = " . $this->db->escape($user_id) . "
                )
            )", null, false);
        }


        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->where('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) = "Y"', null, false)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.WAREHOUSE_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
}
