<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->belum_login();
        $this->load->model('Api_model', 'api');
    }

    private function belum_login()
    {
        if (strpos($this->input->server('HTTP_ACCEPT'), 'application/json') === false) {
            redirect('dashboard');exit();
        }else{
            $session = $this->session->userdata('id');
            if (!$session) {
                sendError('anda belum login!! silahkan login telebih dahulu dengan username dan password anda!');
                exit();
            }
        }
        
    }

    public function get_brand(){
        $result = $this->api->getBrand()->result();
        echo json_encode($result);
    }

    public function get_category(){
        $result = $this->api->getCategory()->result();
        echo json_encode($result);
    }
    
    public function get_uom(){
        $result = $this->api->getUom()->result();
        echo json_encode($result);
    }
    
    public function get_supplier(){
        $result = $this->api->getSupplier()->result();
        echo json_encode($result);
    }
    
    public function get_rak(){
        $result = $this->api->getRak()->result();
        echo json_encode($result);
    }
    
    public function get_made_in(){
        $result = $this->api->getMadeIn()->result();
        echo json_encode($result);
    }
    
    public function get_grade(){
        $result = $this->api->getGrade()->result();
        echo json_encode($result);
    }
    
    public function get_type(){
        $result = $this->api->getType()->result();
        echo json_encode($result);
    }
    
    public function get_komoditi(){
        $result = $this->api->getKomoditi()->result();
        echo json_encode($result);
    }
    
    public function get_jenis(){
        $result = $this->api->getJenis()->result();
        echo json_encode($result);
    }
}
