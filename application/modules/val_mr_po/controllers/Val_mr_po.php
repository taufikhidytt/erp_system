<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Val_mr_po extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        date_default_timezone_set('Asia/Jakarta');
        $this->load->model('Val_mr_po_model','val_mr_po');
    }
    
    public function index()
    {
        try {
            $data['title'] = 'Validasi MR VS PO';
            $data['breadcrumb'] = 'Validasi MR VS PO';
            $this->template->load('template', 'val_mr_po/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_so_list(){
        $result = $this->val_mr_po->get_so_list();
        foreach ($result as $row) {
            $row->SO_ID = base64url_encode($this->encrypt->encode($row->SO_ID));
        }
        echo json_encode($result);
    }

    public function get_so(){
        $so_id  = (int) $this->encrypt->decode(base64url_decode($this->input->post('so_id')));
        $so     = $this->val_mr_po->get_so($so_id);
        if($so){
            $data['so']         = $so;
            $data['mrq']        = $this->val_mr_po->get_mr_list($so_id);
            $data['mrq_count']  = [];
            $arr_build_id       = []; // diisi oleh MR_Item_ID yang tidak null
            foreach ($data['mrq'] as $k => $v) {
                $data['mrq_count'][$v['BUILD_ID']] = ($data['mrq_count'][$v['BUILD_ID']] ?? 0)+1;
                if($v['MR_Item_ID'] && !in_array($v['BUILD_ID'],$arr_build_id)){
                    $arr_build_id[] = $v['BUILD_ID'];
                }
            }

            //ambil data detail mrq untuk ditampilkan jika MR_Item_ID tidak null
            if(count($arr_build_id)){
                $mrq_detail = $this->val_mr_po->get_mr_detail($arr_build_id);
                $data['mrq_detail'] = [];
                foreach ($mrq_detail as $v) {
                    $key = $v['BUILD_ID'];
                    $data['mrq_detail'][$key][] = $v;
                }
            }

            $res = $this->load->view('val_mr_po/mr',$data,true);
            sendSuccess($res, 'success get data');
        }else{
            sendError('Nomor PO not found');
        }
    }

    public function update_status(){
        $build_id   = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
        $value      = $this->input->post('value');
        if(!in_array($value,['Y','N'])){ sendError('Value not found'); die();}

        $mrq = $this->val_mr_po->get_mr($build_id);
        if(!$mrq){ sendError('Data not found'); die(); }

        if($value == 'N' && $mrq->APPROVED_FLAG == 'Y' && ((float) $mrq->RECEIVED_ENTERED_QTY) == 0){
            $res = $this->val_mr_po->update_mr($build_id,['APPROVED_FLAG' => 'N']);
        }else if($value == 'Y' && $mrq->APPROVED_FLAG == 'N'){
            $res = $this->val_mr_po->update_mr($build_id,['APPROVED_FLAG' => 'Y']);
        }

        if (isset($res) && $res === true) {
            sendSuccess([], 'Data berhasil disimpan');
        } elseif (isset($res) &&  is_array($res)) {
            sendError("Gagal update! Error Code: {$res['code']} - {$res['message']}");
        } else {
            sendError('Data tidak ditemukan atau tidak ada perubahan');
        }

    }
}