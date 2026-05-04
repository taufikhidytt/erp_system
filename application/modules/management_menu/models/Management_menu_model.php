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
        "SEQ",
        "ERP_MENU_NAME",
        "PROMPT",
        "ACTIVE_FLAG",
        "FLAG_ERP_NO",
        "PPN",
    );

    var $column_search = array(
        null,
        "SEQ",
        "ERP_MENU_NAME",
        "PROMPT",
        "ACTIVE_FLAG",
        "FLAG_ERP_NO",
        "PPN",
    );

    var $order = array('a.SEQ' => 'ASC');

    private function _get_datatables_query()
    {
        $this->db->select("
            ERP_MENU_ID,PARENT_ID,ERP_MENU_NAME,PROMPT,ACTIVE_FLAG,SEQ,FLAG_ERP_NO,PPN
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

    public function get_by_id($id,$parent_id='')
    {
        $this->db->select("
            a.ERP_MENU_ID,a.PARENT_ID,a.ERP_MENU_NAME,a.PROMPT,a.ACTIVE_FLAG,a.PERMISSIONS,a.MENU_ICON,a.SEQ,a.FLAG_ERP_NO,a.PPN
        ");
        $this->db->from('erp_menu a');
        $this->db->where('a.ERP_MENU_ID', $id);
        if(strlen($parent_id)>0){
            $this->db->where('a.PARENT_ID', $parent_id);
        }
        return $this->db->get();
    }

    public function update_by_id($id,$params)
    {
        $this->db->where('ERP_MENU_ID', $id);
        $this->db->update('erp_menu', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }

    public function insert_data($table,$params)
    {
        $this->db->insert($table,$params);
        return $this->db->insert_id(); 
    }

    public function get_last_seq($parent_id)
    {
        $this->db->select('max(SEQ) as LAST_SEQ');
        $this->db->from('erp_menu');
        $this->db->where('PARENT_ID',$parent_id);
        $query = $this->db->get();
        return $query->num_rows()>0 ? $query->row()->LAST_SEQ+1 : 1;
    }

    public function get_menus()
    {
        $this->db->select('ERP_MENU_ID,PARENT_ID,SEQ,ERP_MENU_NAME,PROMPT,ACTIVE_FLAG,MENU_ICON');
        $this->db->from('erp_menu');
        $this->db->order_by('PARENT_ID ASC, SEQ ASC');
        return $this->db->get();
    }

    public function update_batch($params)
    {
        $this->db->update_batch('erp_menu',$params,'ERP_MENU_ID');
        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }
}