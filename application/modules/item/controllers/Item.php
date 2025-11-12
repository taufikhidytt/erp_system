<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Item_model', 'item');
    }
    public function index()
    {
        try {
            $data['title'] = 'Item';
            $data['heading'] = 'Item';
            $this->template->load('template', 'item/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->item->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $item) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['action'] = '
                <a href="' . base_url('item/detail') . '">
                    <i class="ri ri-zoom-in-fill"></i>
                </a>
            ';
            $row['kode_item'] = $item->KODE_ITEM ? $item->KODE_ITEM : '-';
            $row['nama_item'] = $item->NAMA_ITEM ? $item->NAMA_ITEM : '-';
            $row['part_number'] = $item->PART_NUMBER ? $item->PART_NUMBER : '-';
            $row['uom'] = $item->UOM ? $item->UOM : '-';
            $row['jenis'] = $item->JENIS ? $item->JENIS : '-';
            $row['kategori'] = $item->KATEGORY ? $item->KATEGORY : '-';
            $row['made_in'] = $item->MADE_IN ? $item->MADE_IN : '-';
            $row['komoditi'] = $item->KOMODITI ? $item->KOMODITI : '-';
            $row['brand'] = $item->BRAND ? $item->BRAND : '-';
            $row['trade'] = $item->TRADE ? $item->TRADE : '-';
            $row['price_last_buy'] = $item->PRICE_LAST_BUY ? number_format($item->PRICE_LAST_BUY, 2) : '-';
            $row['price_last_sell'] = $item->PRICE_LAST_SELL ? number_format($item->PRICE_LAST_SELL, 2) : '-';
            $row['lead_time'] = $item->LEAD_TIME ? $item->LEAD_TIME : '-';
            if ($item->KONSY == 'Y') {
                $returnKonsy = 'Yes';
            } elseif ($item->KONSY == 'N') {
                $returnKonsy = 'No';
            } else {
                $returnKonsy = '-';
            }
            $row['konsy'] = $returnKonsy;
            $item->KONSY == 'Y' ? 'Yes' : 'No';
            $row['approved'] = $item->APPROVED == 'Y' ? 'Yes' : 'No';
            $row['status'] = $item->STATUS == 'Y' ? 'Yes' : 'No';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->item->count_all(),
            "recordsFiltered" => $this->item->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function add()
    {
        try {
            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah Item';
                $data['heading'] = 'Tambah Item';
                $data['brand'] = $this->item->getBrand();
                $data['category'] = $this->item->getCategory();
                $data['uom'] = $this->item->getUom();
                $data['type'] = $this->item->getType();
                $data['rak'] = $this->item->getRak();
                $data['made_in'] = $this->item->getMadeIn();
                $data['komoditi'] = $this->item->getKomoditi();
                // debuging($data['komoditi']->result());
                $this->template->load('template', 'item/add', $data);
            } else {
                $post = $this->input->post();
                debuging($post);
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }
}
