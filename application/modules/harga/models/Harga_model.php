<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Harga_model extends CI_Model
{
    protected $table = 'price_list_detail';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_fields()
    {
        $this->db->select("PROGRAM_CODE1,DESCRIPTION,DISPLAY_NAME");
        $this->db->where("LEFT(PROGRAM_CODE1,3)",'LVL');
        $this->db->order_by('PROGRAM_CODE1','ASC');
        return $this->db->get('ERP_LOOKUP_VALUE');
    }

    public function get_setup()
    {
        return $this->db->select("KOLOM_HARGA")->get('setup');
    }
}