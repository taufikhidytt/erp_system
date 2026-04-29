<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Group_menu_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    public function get_by_id($id)
    {
        $this->db->select("
            a.ERP_GROUP_ID,a.ERP_GROUP_NAME,a.ACTIVE_FLAG,a.NOTE
        ");
        $this->db->from('erp_group a');
        $this->db->where('a.ERP_GROUP_ID', $id);
        return $this->db->get();
    }

    public function insert($post)
    {
        $params = array(
            'ERP_GROUP_NAME' => $post['name'] ? htmlspecialchars($post['name']) : null,
            'NOTE'          => $post['note'] ? htmlspecialchars($post['note']) : null,
            'ACTIVE_FLAG'   => $post['active_flag'] == 'on'? 'Y':'N',
            'CREATED_BY'    => $this->session->userdata('id'),
            'CREATED_DATE'  => date('Y-m-d H:i:s')
        );
        $this->db->insert('erp_group', $params);
        return $this->db->insert_id();
    }

    public function update_by_id($id, $post){
        $params = array(
            'ERP_GROUP_NAME' => $post['name'] ? htmlspecialchars($post['name']) : null,
            'NOTE' => $post['note'] ? htmlspecialchars($post['note']) : null,
            'ACTIVE_FLAG' => $post['active_flag'] == 'on'? 'Y':'N',
            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s')
        );
        $this->db->where('ERP_GROUP_ID', $id);
        $this->db->update('erp_group', $params);
        return ($this->db->error()['code'] == 0);
    }

    public function get_menus()
    {
        $this->db->select('a.ERP_MENU_ID,a.PARENT_ID,a.ERP_MENU_NAME,a.PROMPT,a.MENU_ICON');
        $this->db->from('erp_menu a');
        $this->db->where('a.ACTIVE_FLAG', 'Y');
        $this->db->order_by('a.ERP_MENU_ID');
        return $this->db->get();
    }

    public function delete_group_menu($group_id)
    {
        $this->db->where('ERP_GROUP_ID', $group_id);
        $this->db->delete('erp_group_menu');
        $error = $this->db->error();

        if ($error['code'] != 0) {
            return [
                'status'  => 'error',
                'message' => $error['message']
            ];
        }

        return [
            'status' => 'success',
            'affected' => $this->db->affected_rows()
        ];
    }

    public function insert_batch($table, $data)
    {
        if(count($data)){
            return $this->db->insert_batch($table, $data);
        }else{
            return null;
        }
        
    }

    public function get_group_menu($group_id)
    {
        $this->db->select('ERP_MENU_ID,VIEW_FLAG,INSERT_FLAG,UPDATE_FLAG,DELETE_FLAG');
        $this->db->from('erp_group_menu');
        $this->db->where('ERP_GROUP_ID', $group_id);
        return $this->db->get();
    }
}