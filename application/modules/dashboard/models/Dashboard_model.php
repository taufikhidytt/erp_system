<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        "b.ERP_USER_NAME",
        "a.ip",
        "a.os",
        "a.browser",
        "a.log_date",
    );

    var $column_search = array(
        null,
        "b.ERP_USER_NAME",
        "a.ip",
        "a.os",
        "a.browser",
        "a.log_date",
    );

    var $order = array('a.log_date' => 'DESC');

    private function _get_datatables_query()
    {
        $this->db->select("a.*, b.ERP_USER_NAME");
        $this->db->from('log_sign_in a');
        $this->db->join('erp_user b', 'a.user_id = b.ERP_USER_ID');

        $global_search_value = $this->input->post('search')['value'] ?? '';

        // ✅ GLOBAL SEARCH
        if (!empty($global_search_value)) {
            $this->db->group_start();
            $i = 0;
            foreach ($this->column_search as $item) {
                if (empty($item)) continue; // safety

                if ($i == 0) {
                    $this->db->like($item, $global_search_value);
                } else {
                    $this->db->or_like($item, $global_search_value);
                }
                $i++;
            }
            $this->db->group_end();
        }

        // ✅ COLUMN SEARCH
        $i = 0;
        foreach ($this->column_search as $item) {
            if (empty($item)) {
                $i++;
                continue;
            }

            $column_search_value = $this->input->post('columns')[$i]['search']['value'] ?? '';
            if (!empty($column_search_value)) {
                $this->db->like($item, $column_search_value);
            }
            $i++;
        }

        // ORDER
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
        $this->db->select("a.*, b.ERP_USER_NAME");
        $this->db->from('log_sign_in a');
        $this->db->join('erp_user b', 'a.user_id = b.ERP_USER_ID');
        return $this->db->count_all_results();
    }
}
