<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Lov_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        null,
        "CASE 
            WHEN USER_CAN_EDIT_FLAG = 'Y' THEN 'Editable'
            ELSE 'Readonly'
        END",
        "ERP_LOOKUP_SET_NAME",
        "DESCRIPTION",
    );

    var $column_search = array(
        null,
        "CASE 
            WHEN USER_CAN_EDIT_FLAG = 'Y' THEN 'Editable'
            ELSE 'Readonly'
        END",
        "ERP_LOOKUP_SET_NAME",
        "DESCRIPTION",
    );

    var $order = array('ERP_LOOKUP_SET_ID' => 'ASC');

    private function _get_datatables_query()
    {
        $this->db->select("
            ERP_LOOKUP_SET_ID,
            PROGRAM_CODE,
            ERP_LOOKUP_SET_NAME,
            DESCRIPTION,
            CASE 
                WHEN USER_CAN_EDIT_FLAG = 'Y' THEN 'Editable'
                ELSE 'Readonly'
            END as Status
        ");
        $this->db->from("erp_lookup_set");

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
            ERP_LOOKUP_SET_ID,
            PROGRAM_CODE,
            ERP_LOOKUP_SET_NAME,
            DESCRIPTION,
            USER_CAN_EDIT_FLAG,
        ");
        $this->db->from("erp_lookup_set");
        return $this->db->count_all_results();
    }

    public function get_detail_by_erp_lookup_set_id($erp_lookup_set_id, $limit = null, $start = null)
    {
        $this->db->select("
            ERP_LOOKUP_VALUE_ID,
            ERP_LOOKUP_SET_ID,
            PROGRAM_CODE1,
            DISPLAY_NAME,
            DESCRIPTION,
            SEQ,
            START_DATE,
            END_DATE,
            PRIMARY_FLAG,
            ITEM_FLAG,
            TOTAL_FLAG,
            DISKON_FLAG,
            ACTIVE_FLAG,
            MENU_ICON,
            CREATED_BY,
            CREATED_DATE,
            LAST_UPDATE_BY,
            LAST_UPDATE_DATE
        ");
        $this->db->from("erp_lookup_value");
        $this->db->where('ERP_LOOKUP_SET_ID', $erp_lookup_set_id);
        $this->db->order_by('SEQ', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_erp_lookup_set_id($erp_lookup_set_id)
    {
        $this->db->where('ERP_LOOKUP_SET_ID', $erp_lookup_set_id);
        return $this->db->count_all_results('erp_lookup_value');
    }

    public function get_erp_lookup_set_id($id)
    {
        $this->db->from('erp_lookup_set');
        $this->db->where('ERP_LOOKUP_SET_ID', $id);
        return $this->db->get();
    }

    public function updateStatus($id, $status)
    {
        $params = array(
            'STATUS_ID' => $status,
        );
        $this->db->where('PR_ID', $id);
        $this->db->update('pr', $params);
        return ($this->db->error()['code'] == 0);
    }
}
