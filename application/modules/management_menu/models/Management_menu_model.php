<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Management_menu_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        "ERP_MENU_NAME",
        "PROMPT",
        "ACTIVE_FLAG"
    );

    var $column_search = array(
        null,
        "ERP_MENU_NAME",
        "PROMPT",
        "ACTIVE_FLAG"
    );

    var $order = array('a.ERP_MENU_ID' => 'ASC');

    private function _get_datatables_query()
    {
        $this->db->select("
            ERP_MENU_ID,PARENT_ID,ERP_MENU_NAME,PROMPT,ACTIVE_FLAG
        ");
        $this->db->from('erp_menu a');

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

    public function get_datatables()
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

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        
        $this->db->select("
            a.ERP_MENU_ID,
        ");
        $this->db->from('erp_menu a');
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        $this->db->select("
            a.ERP_MENU_ID,a.PARENT_ID,a.ERP_MENU_NAME,a.PROMPT,a.ACTIVE_FLAG,a.PERMISSIONS
        ");
        $this->db->from('erp_menu a');
        $this->db->where('a.ERP_MENU_ID', $id);
        return $this->db->get();
    }

    public function update_by_id($id,$params){
        $this->db->where('ERP_MENU_ID', $id);
        $this->db->update('erp_menu', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }
}