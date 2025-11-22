<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        $this->load->model('Dashboard_model', 'dashboard');
    }
    public function index()
    {
        try {
            // debuging($this->session->userdata());
            $data['title'] = 'Dashboard';
            $data['breadcrumb'] = 'Dashboard';
            $this->template->load('template', 'dashboard/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }
}
