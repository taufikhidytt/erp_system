<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Harga extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Harga_model','harga');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $data['fields']     = $this->harga->get_fields()->result();
            
            $data['kolom_harga']= 0;
            $data['rowspan']    = 1;  
            $setup              = $this->harga->get_setup()->row();
            if($setup){
                $data['kolom_harga']  = (int) $setup->KOLOM_HARGA;
                $data['rowspan']      = 2;  
            }
            $this->template->load('template', $this->access['url'] . '/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $kolom_harga    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id_page')));
        $chk_category   = $this->input->post('chk_category')==="true"?1:0;
        $category       = (int) $this->input->post('category');
        $chk_brand      = $this->input->post('chk_brand')==="true"?1:0;
        $brand          = (int) $this->input->post('brand');

        $this->load->model('M_datatables', 'datatables');
        $order_field    = [null, 'i.ITEM_CODE', 'i.ITEM_DESCRIPTION', 'mrk.DISPLAY_NAME', 'a.ENTERED_UOM', 'a.PRICE_BUY','i.HPP'];
        $column         = 'a.PRICE_LIST_DETAIL_ID, i.ITEM_CODE, i.ITEM_DESCRIPTION, i.GROUP_ID, i.MEREK_ID, mrk.DISPLAY_NAME MEREK, a.ENTERED_UOM,a.PRICE_BUY,i.HPP COGS,i.APPROVE_FLAG,';

        for ($i=0; $i < $kolom_harga ; $i++) { 
            $order_field[] = 'a.PRICE_SELL'.($i+1);
            $column .= 'a.PRICE_SELL'.($i+1).',';
        }
        $order_field[] = 'i.APPROVE_FLAG';

        $where = ['a.ITEM_ID >' => 1, 'i.ACTIVE_FLAG' => 'Y', 'i.APPROVE_FLAG' => 'Y'];
        if(!$chk_category){
            $where['i.GROUP_ID'] = $category;
        }
        if(!$chk_brand){
            $where['i.MEREK_ID'] = $brand;
        }

        $params = [
            'table' => 'price_list_detail a',
            'select' => [$column],
            'joins' => [
                ['item i','a.ITEM_ID = i.ITEM_ID','inner'],
                ['erp_lookup_value b','b.ERP_LOOKUP_VALUE_ID = i.GROUP_ID','left'],
                ['erp_lookup_value mrk','mrk.ERP_LOOKUP_VALUE_ID = i.MEREK_ID','left'],
            ],
            'where' => $where,
            'column_search' => $order_field,
            'column_order'  => $order_field,
            'order'         => ['i.ITEM_CODE' => 'asc']
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            $id             = base64url_encode($this->encrypt->encode($row->PRICE_LIST_DETAIL_ID));
            $kolom_harga    = (int) $this->encrypt->decode(base64url_decode($this->input->post('id_page')));
            
            $res = [
                'id' => $id,
                'no' => $no,
                'part_code'     => $row->ITEM_CODE,
                'description'   => $row->ITEM_DESCRIPTION,
                'part_brand'    => $row->MEREK,
                'uom'           => $row->ENTERED_UOM,
                'harga_beli'    => numb_format($row->PRICE_BUY),
                'cogs_idr'      => numb_format($row->COGS),
                'active_flag'   => $row->APPROVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
                
            ];
            for ($i=0; $i < $kolom_harga ; $i++) { 
                $res['lvl'.($i+1)] = numb_format($row->{'PRICE_SELL'.($i+1)});
            }
            return $res;
        }));
    }
}