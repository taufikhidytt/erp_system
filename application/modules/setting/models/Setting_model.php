<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Setting_model extends CI_Model
{
    protected $table = 'coa_setup';

    public function __construct()
    {
        parent::__construct();
    }

    public function getById($id)
    {
        $this->db->from($this->table);
        if(is_array($id)){
            $this->db->where_in('PROGRAM_ACCOUNT', $id);
        }else{
            $this->db->where('PROGRAM_ACCOUNT', $id);
        }
        return $this->db->get();
    }

    public function insert_batch($params)
    {
        if(!empty($params)){
            $this->db->insert_batch($this->table, $params);
        }
    }

    public function update_batch($params)
    {
        if(!empty($params)){
            $this->db->update_batch($this->table, $params, 'PROGRAM_ACCOUNT');
        }
    }

    public function get_account()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("CONCAT('[',a.COA_CODE,'] - ',a.COA_NAME) as text,a.COA_ID as id, a.COA_NAME")
            ->from('v_account a')
            ->order_by('a.COA_CODE');

        if ($id) {
            $this->db->where('a.COA_ID', $id)->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.COA_CODE', $searchTerm)
                    ->or_like('a.COA_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
}
