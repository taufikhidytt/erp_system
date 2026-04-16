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
        $this->db->select("a.*, b.ERP_USER_NAME");
        $this->db->from('log_sign_in a');
        $this->db->join('erp_user b', 'a.user_id = b.ERP_USER_ID');
        return $this->db->count_all_results();
    }
}
