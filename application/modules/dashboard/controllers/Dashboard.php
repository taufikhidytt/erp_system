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

    public function log_modal()
    {
        echo $this->load->view('modal',[],true);
    }

    public function log_data()
    {
        $params = json_decode($this->encrypt->decode(base64_decode($this->input->post('params'))), true);
        $this->load->model('M_datatables', 'datatables');
        
        $order_field = [null, 'a.LAST_UPDATE_DATE', 'u.ERP_USER_NAME', 'a.TRANSAKSI' , 'a.NOTE'];
        $column = 'u.ERP_USER_NAME, a.TRANSAKSI, a.NOTE,';
        if(isset($params['where'])) {
            $where = $params['where'];
        }else{
            $where = ['a.ERP_LOG_EDIT_ID' => -1];
        }

        $id = (int) $this->encrypt->decode(base64_decode($this->input->post('id')));
        if($id) {
            $where['a.ID'] = $id;
            $params['h']['type']    = 'all';
            $params['h']['w']       = [$params['h']['id'] => $id];
        }

        foreach($params['h']['attr'] ?? [] as $key => $field) {
            $val = (int) $this->encrypt->decode(base64_decode($this->input->post($key)));
            if($val) {
                $x = explode('.', $field);
                $where[$x[0].'.'.$x[1]] = $val;
                $params['h']['type']    = 'all';
                $params['h']['w'][$x[1]]= $val;
            }
        }

        $config = [
            'table' => 'erp_log_edit a',
            'select' => [['STRAIGHT_JOIN a.LAST_UPDATE_DATE',FALSE], $column],
            'joins' => [
                ['erp_user u','a.CREATED_BY = u.ERP_USER_ID','inner']
            ],
            'where' => $where,
            'column_search' => $order_field,
            'column_order'  => $order_field,
            'order'         => ['a.ERP_LOG_EDIT_ID' => 'DESC']
        ];
        if(isset($params['select'])) {
            $config['select'][0] .= $params['select'];
        }
        if(isset($params['joins'])) {
            $config['joins'] = array_merge($config['joins'], $params['joins']);
        }

        $result = $this->datatables->generate($config, function ($row, $no) {
            $res = [
                'no' => $no,
                'tanggal'   => $row->LAST_UPDATE_DATE,
                'user'      => $row->ERP_USER_NAME,
                'transaksi' => $row->TRANSAKSI,
                'log'       => (isset($row->text) && $row->NOTE ? $row->text.' = ' : '').$row->NOTE,
           ];
            return $res;
        });

        if(isset($params['h'])) {
            if(isset($params['h']['type']) && $params['h']['type'] == 'by_one') {
                $header_created = $this->dashboard->info_header($params, 'created');
                $header_updated = $this->dashboard->info_header($params, 'updated');
                $result['header'] = [
                    'created_date' => $header_created->CREATED_DATE ?? '-',
                    'user_created'   => $header_created->USER_CREATED ?? '-',
                    'last_update_date' => $header_updated->LAST_UPDATE_DATE ?? '-',
                    'user_updated'   => $header_updated->USER_UPDATED ?? '-',
                ];
            }else{
                $info_header    = $this->dashboard->info_header($params);
                $result['header'] = [
                    'created_date' => $info_header->CREATED_DATE ?? '-',
                    'user_created'   => $info_header->USER_CREATED ?? '-',
                    'last_update_date' => $info_header->LAST_UPDATE_DATE ?? '-',
                    'user_updated'   => $info_header->USER_UPDATED ?? '-',
                ];
            }
        }

        echo json_encode($result);
    }
}
