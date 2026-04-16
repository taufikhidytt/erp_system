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
            $data['title'] = 'Dashboard';
            $data['breadcrumb'] = 'Dashboard';
            $this->template->load('template', 'dashboard/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_log_sign_in()
    {
        $list = $this->dashboard->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $log_sign_in) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['nama'] = $log_sign_in->ERP_USER_NAME ? $log_sign_in->ERP_USER_NAME : '-';
            $row['ip'] = $log_sign_in->ip ? $log_sign_in->ip : '-';
            $row['os'] = $log_sign_in->os ? $log_sign_in->os : '-';
            $row['browser'] = $log_sign_in->browser ? $log_sign_in->browser : '-';
            $row['date'] = $log_sign_in->log_date ? date('Y-m-d H:i', strtotime($log_sign_in->log_date)) : '-';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->dashboard->count_all(),
            "recordsFiltered" => $this->dashboard->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
}
