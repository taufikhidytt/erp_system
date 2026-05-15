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
            $data['breadcrumb'] = 'Item';
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
            $row['item_id'] = base64url_encode($this->encrypt->encode($item->ID));
            $row['kode_item'] = '
            <a href="' . base_url('item/detail/' . base64url_encode($this->encrypt->encode($item->ID))) . '">
                ' . ($item->KODE_ITEM ? $item->KODE_ITEM : '-') . '
            </a>';
            $row['nama_item'] = $item->NAMA_ITEM ? $item->NAMA_ITEM : '-';
            $row['part_number'] = $item->PART_NUMBER ? $item->PART_NUMBER : '-';
            $row['uom'] = $item->UOM ? $item->UOM : '-';
            $row['jenis'] = $item->JENIS ? $item->JENIS : '-';
            $row['kategori'] = $item->KATEGORY ? $item->KATEGORY : '-';
            $row['made_in'] = $item->MADE_IN ? $item->MADE_IN : '-';
            $row['komoditi'] = $item->KOMODITI ? $item->KOMODITI : '-';
            $row['brand'] = $item->BRAND ? $item->BRAND : '-';
            $row['trade'] = $item->TRADE ? $item->TRADE : '-';
            $row['price_last_buy'] = $item->PRICE_LAST_BUY ? numb_format($item->PRICE_LAST_BUY) : '-';
            $row['price_last_sell'] = $item->PRICE_LAST_SELL ? numb_format($item->PRICE_LAST_SELL) : '-';
            $row['lead_time'] = $item->LEAD_TIME ? $item->LEAD_TIME . " Week" : '-';
            if ($item->KONSY == 'Y') {
                $returnKonsy = '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            } elseif ($item->KONSY == 'N') {
                $returnKonsy = '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            } else {
                $returnKonsy = '-';
            }
            $row['konsy'] = $returnKonsy;
            $item->KONSY == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            $row['approved'] = $item->APPROVED == 'Y' ? '<i class="text-success fa fa-check" title="Approved" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Unapproved" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            $row['status'] = $item->OBSOLETE == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
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
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('brand', 'Brand', 'trim|required');
            $this->form_validation->set_rules('category', 'Category', 'trim|required');
            $this->form_validation->set_rules('part_number', 'Part Number', 'trim|required|callback_check_part_supplier_kms');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            $this->form_validation->set_rules('satuan', 'Satuan', 'trim|required');
            $this->form_validation->set_rules('type', 'Type', 'trim|required');
            $this->form_validation->set_rules('lead_time', 'Lead time', 'trim|required');
            $this->form_validation->set_rules('komoditi', 'Komoditi', 'trim|required');
            $this->form_validation->set_rules('jenis', 'Jenis', 'trim|required');
            $this->form_validation->set_rules('grade', 'Grade', 'trim|required');

            if ($this->input->post('obsolete')) {
                $this->form_validation->set_rules('new_product_name', 'New product name', 'trim|required');
            }

            if ($this->input->post('konsinyasi')) {
                $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
            }

            $input_satuan = $this->input->post('satuan_lain');
            if (!empty($input_satuan)) {
                foreach ($input_satuan as $index => $val) {
                    $this->form_validation->set_rules("satuan_lain[$index]", 'Satuan Lain', 'callback_check_satuan_lain');
                }
            }

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah Item';
                $data['breadcrumb'] = 'Tambah Item';
                $data['account'] = $this->item->getAccount();
                $data['acc_persediaan'] = $this->item->getAccPersediaan()->row();
                $data['acc_utang_suspend'] = $this->item->getAccUtangSuspend()->row();
                $data['acc_hpp'] = $this->item->getAccHpp()->row();
                $data['acc_penjualan_barang'] = $this->item->getPenjualanBarang()->row();
                $data['acc_retur_penjualan'] = $this->item->getReturPenjualan()->row();
                $data['acc_retur_pembelian'] = $this->item->getReturPembelian()->row();
                $data['acc_disc_penjualan'] = $this->item->getDiscPenjualan()->row();
                $data['acc_penjualan_jasa'] = $this->item->getPenjualanJasa()->row();
                $data['acc_pembelian'] = $this->item->getPembelian()->row();
                $data['acc_disc_penjualan_jasa'] = $this->item->getDiscPenjualanJasa()->row();
                $data['acc_pembelian_uang_muka'] = $this->item->getPembelianUangMuka()->row();
                $data['acc_penjualan_uang_muka'] = $this->item->getPenjualanUangMuka()->row();
                $this->template->load('template', 'item/add', $data);
            } else {
                $post = $this->input->post();
                if ($post['rak'] != '') {
                    $queryLokasi = $this->db->query("SELECT b.DISPLAY_NAME Grade, b.DESCRIPTION Note, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'RAK' AND b.ACTIVE_FLAG = 'Y' AND b.ERP_LOOKUP_VALUE_ID = {$post['rak']} ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");

                    if ($queryLokasi->num_rows() > 0) {
                        $post['lokasi'] = $queryLokasi->row()->Grade;
                    } else {
                        $post['lokasi'] = null;
                    }
                } else {
                    $post['lokasi'] = null;
                }
                $post['kubikasi'] = $post['length'] * $post['width'] * $post['height'];
                // $post['item_code'] = $this->generateNomor();
                $idItem = $this->item->add($post);
                if ($this->db->affected_rows() > 0) {
                    date_default_timezone_set('Asia/Jakarta');
                    $dataToInsert = [];
                    if (!empty($post['satuan_lain'])) {
                        $count = count($post['satuan_lain']);
                        for ($i = 0; $i < $count; $i++) {
                            $id = isset($post['id'][$i]) ? intval($post['id'][$i]) : 0;

                            if ($id === 0 && !empty(trim($post['satuan_lain'][$i]))) {
                                $validateParentUom = $this->db->query("SELECT UOM_CODE FROM item WHERE ITEM_ID = '{$idItem}' AND UOM_CODE = '{$post['satuan_lain'][$i]}'");

                                $validate = $this->db->query("SELECT UOM_CODE FROM item_uom WHERE ITEM_ID = '{$idItem}' AND UOM_CODE = '{$post['satuan_lain'][$i]}'");
                                if ($validateParentUom->num_rows() > 0) {
                                    $this->session->set_flashdata('warning', 'Data master item berhasil tersimpan, UoM detail sudah terdaftar pada master item silahakan pilih UoM lainnya!');
                                    redirect('item/detail/' . $this->encrypt->encode($idItem));
                                } else if ($validate->num_rows() > 0) {
                                    $this->session->set_flashdata('warning', 'Data master item berhasil tersimpan, UoM detail sudah tersedia silahakan pilih UoM lainnya!');
                                    redirect('item/detail/' . $this->encrypt->encode($idItem));
                                } else {
                                    $dataToInsert[] = [
                                        'ITEM_ID'           => $idItem,
                                        'UOM_CODE'          => $post['satuan_lain'][$i],
                                        'TO_QTY'            => floatval($post['konversi'][$i]),
                                        'BASE_UOM_FLAG'     => $post['status_satuan_detail'][$i] == 'Y'?'Y':'N',
                                        'CREATED_BY'        => $this->session->userdata('id'),
                                        'CREATED_DATE'      => date('Y-m-d H:i:s'),
                                        'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                                        'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                                    ];
                                }
                            }
                        }
                    }
                    if (!empty($dataToInsert)) {
                        $this->item->insert_batch($dataToInsert);
                        if ($this->db->affected_rows() > 0) {
                            $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                            redirect('item/detail/' . base64url_encode($this->encrypt->encode($idItem)));
                        } else {
                            $error = $this->db->error();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['code'] . " ~ " . $error['message']);
                            redirect('item/detail/' . base64url_encode($this->encrypt->encode($idItem)));
                        }
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data baru!');
                        redirect('item/detail/' . base64url_encode($this->encrypt->encode($idItem)));
                    }
                } else {
                    $error = $this->db->error();
                    $this->session->set_flashdata('warning', "Error DB: " . $error['code'] . " ~ " . $error['message']);
                    redirect('item');
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function generateNomor()
    {
        $post = $this->input->post();

        // Get Brand Code
        $brand = $this->db->query("
            SELECT b.DESCRIPTION AS Brand_Code 
            FROM erp_lookup_set a 
            INNER JOIN erp_lookup_value b 
                ON a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID 
            WHERE a.PROGRAM_CODE = 'MEREK' 
                AND b.ACTIVE_FLAG = 'Y' 
                AND b.ERP_LOOKUP_VALUE_ID = '{$post['brand']}'
        ")->row_array();

        // Get Category Code
        $category = $this->db->query("
            SELECT b.DESCRIPTION AS Category_Code 
            FROM erp_lookup_set a 
            INNER JOIN erp_lookup_value b 
                ON a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID 
            WHERE a.PROGRAM_CODE = 'GROUP' 
                AND b.ACTIVE_FLAG = 'Y' 
                AND b.ERP_LOOKUP_VALUE_ID = '{$post['category']}'
        ")->row_array();

        // Cari sequence terbesar berdasarkan BRAND saja
        $seqQuery = $this->db->query("
            SELECT MAX(CAST(SUBSTRING_INDEX(ITEM_CODE, '.', -1) AS UNSIGNED)) AS last_sequence
            FROM item
            WHERE ITEM_CODE LIKE '{$brand['Brand_Code']}.%'
        ")->row_array();

        $lastSeq = $seqQuery['last_sequence'] ? $seqQuery['last_sequence'] : 0;
        $nextSeq = $lastSeq + 1;

        // Format 5 digit
        $sequenceFormatted = sprintf("%05d", $nextSeq);

        // Generate kode baru
        $kode = $brand['Brand_Code'] . '.' . $category['Category_Code'] . '.' . $sequenceFormatted;

        return $kode;
    }

    public function detail($id)
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('brand', 'Brand', 'trim|required');
            $this->form_validation->set_rules('category', 'Category', 'trim|required');
            $this->form_validation->set_rules('part_number', 'Part Number', 'trim|required|callback_update_check_part_supplier_kms');
            $this->form_validation->set_rules('description', 'Description', 'trim|required');
            $this->form_validation->set_rules('satuan', 'Satuan', 'trim|required');
            $this->form_validation->set_rules('type', 'Type', 'trim|required');
            $this->form_validation->set_rules('lead_time', 'Lead time', 'trim|required');
            $this->form_validation->set_rules('komoditi', 'Komoditi', 'trim|required');
            $this->form_validation->set_rules('jenis', 'Jenis', 'trim|required');
            $this->form_validation->set_rules('grade', 'Grade', 'trim|required');

            $input_satuan = $this->input->post('satuan_lain');
            if (!empty($input_satuan)) {
                foreach ($input_satuan as $index => $val) {
                    $this->form_validation->set_rules("satuan_lain[$index]", 'Satuan Lain', 'callback_check_satuan_lain');
                }
            }
        

            if ($this->input->post('obsolete')) {
                $this->form_validation->set_rules('new_product_name', 'New product name', 'trim|required');
            }

            if ($this->input->post('konsinyasi')) {
                $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required');
            }

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->item->getItemId($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data'] = $query->row();
                    $data['account'] = $this->item->getAccount();
                    $data['acc_persediaan'] = $this->item->getAccPersediaan()->row();
                    $data['acc_utang_suspend'] = $this->item->getAccUtangSuspend()->row();
                    $data['acc_hpp'] = $this->item->getAccHpp()->row();
                    $data['acc_penjualan_barang'] = $this->item->getPenjualanBarang()->row();
                    $data['acc_retur_penjualan'] = $this->item->getReturPenjualan()->row();
                    $data['acc_retur_pembelian'] = $this->item->getReturPembelian()->row();
                    $data['acc_disc_penjualan'] = $this->item->getDiscPenjualan()->row();
                    $data['acc_penjualan_jasa'] = $this->item->getPenjualanJasa()->row();
                    $data['acc_pembelian'] = $this->item->getPembelian()->row();
                    $data['acc_disc_penjualan_jasa'] = $this->item->getDiscPenjualanJasa()->row();
                    $data['acc_pembelian_uang_muka'] = $this->item->getPembelianUangMuka()->row();
                    $data['acc_penjualan_uang_muka'] = $this->item->getPenjualanUangMuka()->row();
                    $data['uomChild'] = $this->item->getUomChild($id);
                    $this->template->load('template', 'item/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('item');
                }
            } else {
                $post = $this->input->post();
                $idInput = $this->input->post('id');
                $post['id'] = $this->encrypt->decode($idInput);
                if ($post['rak'] != '') {
                    $queryLokasi = $this->db->query("SELECT b.DISPLAY_NAME Grade, b.DESCRIPTION Note, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'RAK' AND b.ACTIVE_FLAG = 'Y' AND b.ERP_LOOKUP_VALUE_ID = {$post['rak']} ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");

                    if ($queryLokasi->num_rows() > 0) {
                        $post['lokasi'] = $queryLokasi->row()->Grade;
                    } else {
                        $post['lokasi'] = null;
                    }
                } else {
                    $post['lokasi'] = null;
                }
                $post['kubikasi'] = $post['length'] * $post['width'] * $post['height'];

                $this->db->trans_begin();
                $result = $this->item->update($post);
                
                //konversi satuan
                $i_satuan_uom   = $this->input->post('id_satuan_uom_detail');
                $i_satuan_lain  = $this->input->post('satuan_lain');
                $i_konversi     = $this->input->post('konversi');
                $i_status_satuan_detail     = $this->input->post('status_satuan_detail');
                $arr_insert_konversi = [];
                foreach ($i_satuan_lain as $i => $v) {
                    $idx        = (int) $i_satuan_uom[$i];
                    $konversi   = (float) $i_konversi[$i];
                    if($v && $konversi>0){
                        $params = [
                            'ITEM_ID'   => (int) $post['id'],
                            'UOM_CODE'  => $v,
                            'TO_QTY'    => $konversi,
                            'BASE_UOM_FLAG'     => $i_status_satuan_detail[$i] == 'Y'?'Y':'N',
                        ];
                        if($idx){
                            $this->db->where('ITEM_UOM_ID', $idx);
                            $this->db->where('ITEM_ID', (int) $post['id']);
                            $this->db->update('item_uom', $params);
                            $error = $this->db->error();
                            if ($error['code'] != 0) {
                                $this->db->trans_rollback();
                                $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                                redirect('item/detail/' . base64url_encode($idInput));
                            }
                        }else{
                            $arr_insert_konversi[] = $params;
                        }
                    }
                }
                if(count($arr_insert_konversi)>0){
                    $this->db->insert_batch('item_uom',$arr_insert_konversi);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('item/detail/' . base64url_encode($idInput));
                    }
                }

                if ($result['status'] === 'error') {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $result['message']);
                    redirect('item/detail/' . base64url_encode($idInput));
                }

                if ($result['affected'] == 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal ubah data item!');
                    redirect('item/detail/' . base64url_encode($idInput));
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                     redirect('item/detail/' . base64url_encode($idInput));
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect('item/detail/' . base64url_encode($idInput));
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    function check_part_supplier_kms()
    {
        $post = $this->input->post();
        if ($this->input->post('part_number')) {
            $part_number = $post['part_number'];
        } else {
            $part_number = null;
        }

        if ($this->input->post('supplier')) {
            $supplier = $post['supplier'];
        } else {
            $supplier = null;
        }

        if ($this->input->post('konsinyasi')) {
            $konsinyasi = $post['konsinyasi'];
        } else {
            $konsinyasi = null;
        }

        $query = $this->db->query("SELECT PART_NUMBER, PERSON_ID, ITEM_KMS FROM item WHERE PART_NUMBER = '$part_number' AND PERSON_ID = '$supplier' AND ITEM_KMS = '$konsinyasi'");
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('check_part_supplier_kms', 'Data part number, supplier dan konsinyasi sudah tersedia');
            return false;
        } else {
            return true;
        }
    }

    function update_check_part_supplier_kms()
    {
        $post = $this->input->post();
        if ($this->input->post('id')) {
            $id = $this->encrypt->decode($post['id']);
        } else {
            $id = null;
        }

        if ($this->input->post('part_number')) {
            $part_number = $post['part_number'];
        } else {
            $part_number = null;
        }

        if ($this->input->post('supplier')) {
            $supplier = $post['supplier'];
        } else {
            $supplier = null;
        }

        if ($this->input->post('konsinyasi')) {
            $konsinyasi = $post['konsinyasi'];
        } else {
            $konsinyasi = null;
        }

        $query = $this->db->query("SELECT PART_NUMBER, PERSON_ID, ITEM_KMS FROM item WHERE PART_NUMBER = '$part_number' AND PERSON_ID = '$supplier' AND ITEM_KMS = '$konsinyasi' AND ITEM_ID != '$id'");
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('update_check_part_supplier_kms', 'Data part number, supplier dan konsinyasi sudah tersedia');
            return false;
        } else {
            return true;
        }
    }

    function check_satuan_lain($value)
    {
        static $idx = 0; 

        $input_konversi = $this->input->post('konversi');
        $konversi = (isset($input_konversi[$idx])) ? (float)$input_konversi[$idx] : 0;
        
        $satuan = $value;

        if (!$satuan && $konversi > 0) {
            $this->form_validation->set_message('check_satuan_lain', 'Satuan harus dipilih.');
            $idx++; 
            return FALSE;
        } 
        
        if ($satuan && $konversi <= 0) {
            $this->form_validation->set_message('check_satuan_lain', 'Qty harus lebih dari 0.');
            $idx++; 
            return FALSE;
        }

        $idx++;
        return TRUE;
    }

    public function approve()
    {
        try {
            $post = $this->input->post();
            $id = $this->encrypt->decode($post['id']);
            $result = $this->item->approve($id);

            if ($result['status'] === 'error') {
                return sendWarning($result['message']);
            }

            if ($result['affected'] == 0) {
                return sendWarning('Gagal approve data item!');
            }

            return sendSuccess('success', 'Selamat anda berhasil approve data!');
        } catch (Exception $err) {
            return sendError('Server error', $err->getMessage());
        }
    }

    public function approveIndex()
    {
        $post = $this->input->post();
        $id = $this->encrypt->decode($post['idApprove']);
        $this->item->approve($id);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data baru!');
            redirect('item');
        } else {
            $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
            redirect('item');
        }
    }

    public function ajax_save()
    {
        date_default_timezone_set('Asia/Jakarta');
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $post = $this->input->post();

        $dataToInsert = [];
        if (!empty($post['satuan_lain'])) {
            $count = count($post['satuan_lain']);
            for ($i = 0; $i < $count; $i++) {
                $id = isset($post['id_satuan_uom_detail'][$i]) ? intval($post['id_satuan_uom_detail'][$i]) : 0;

                if ($id === 0 && !empty(trim($post['satuan_lain'][$i]))) {
                    $validateParentUom = $this->db->query("SELECT UOM_CODE FROM item WHERE ITEM_ID = '{$this->encrypt->decode($post['id_item'])}' AND UOM_CODE = '{$post['satuan_lain'][$i]}'");

                    $validate = $this->db->query("SELECT UOM_CODE FROM item_uom WHERE ITEM_ID = '{$this->encrypt->decode($post['id_item'])}' AND UOM_CODE = '{$post['satuan_lain'][$i]}'");
                    if ($validateParentUom->num_rows() > 0) {
                        return sendWarning('Uom sudah tersedia pada item!');
                    } else if ($validate->num_rows() > 0) {
                        return sendWarning('Uom sudah tersedia!');
                    } else {
                        $dataToInsert[] = [
                            'ITEM_ID'           => $this->encrypt->decode($post['id_item']),
                            'UOM_CODE'          => $post['satuan_lain'][$i],
                            'TO_QTY'            => floatval($post['konversi'][$i]),
                            'BASE_UOM_FLAG'     => 'N',
                            'CREATED_BY'        => $this->session->userdata('id'),
                            'CREATED_DATE'      => date('Y-m-d H:i:s'),
                            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
        }

        if (!empty($dataToInsert)) {
            $this->item->insert_batch($dataToInsert);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'nothing_to_save']);
        }
    }

    public function ajax_update()
    {
        date_default_timezone_set('Asia/Jakarta');
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $post = $this->input->post();

        $dataToInsert   = [];
        $is_status      = false;
        if (!empty($post['satuan_lain'])) {
            $count = count($post['satuan_lain']);
            for ($i = 0; $i < $count; $i++) {
                $id = isset($post['id_satuan_uom_detail'][$i]) ? intval($post['id_satuan_uom_detail'][$i]) : 0;

                if ($id != 0 && !empty(trim($post['satuan_lain'][$i]))) {
                    $validateParentUom = $this->db->query("SELECT UOM_CODE FROM item WHERE ITEM_ID = '{$this->encrypt->decode($post['id_item'])}' AND UOM_CODE = '{$post['satuan_lain'][$i]}'");

                    $validate = $this->db->query("SELECT UOM_CODE FROM item_uom WHERE ITEM_ID = '{$this->encrypt->decode($post['id_item'])}' AND UOM_CODE = '{$post['satuan_lain'][$i]}' AND ITEM_UOM_ID != '{$id}'");
                    if ($validateParentUom->num_rows() > 0) {
                        return sendWarning('Uom sudah tersedia pada item!');
                    } else if ($validate->num_rows() > 0) {
                        return sendWarning('Uom sudah tersedia!');
                    } else {
                        if($post['status_satuan_detail'][$i] == 'Y'){
                            $is_status = true;
                        }
                        $dataToInsert[] = [
                            'ITEM_UOM_ID'       => $id,
                            'ITEM_ID'           => $this->encrypt->decode($post['id_item']),
                            'UOM_CODE'          => $post['satuan_lain'][$i],
                            'TO_QTY'            => floatval($post['konversi'][$i]),
                            'BASE_UOM_FLAG'     => $post['status_satuan_detail'][$i],
                            'CREATED_BY'        => $this->session->userdata('id'),
                            'CREATED_DATE'      => date('Y-m-d H:i:s'),
                            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                        ];
                    }
                }
            }
        }

        if (!empty($dataToInsert)) {
            if($is_status){
                $this->db->where('ITEM_ID', (int) $this->encrypt->decode($post['id_item']));
                $this->db->update('item_uom',['BASE_UOM_FLAG' => 'N']);
            }
            $this->item->updateSatuanUomDetail($dataToInsert);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'nothing_to_save']);
        }
    }

    public function ajax_delete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $ids = $this->input->post('ids');
        if (!empty($ids)) {
            $this->item->delete_by_ids($ids);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'no_ids']);
        }
    }

    public function deleteItem()
    {
        try {
            checkAccess('delete');
            $id = $this->encrypt->decode($this->input->post('id'));
            $result = $this->item->deleteItem($id);

            if ($result['status'] === 'error') {
                return sendWarning($result['message']);
            }

            if ($result['affected'] == 0) {
                return sendWarning('Gagal hapus data item!');
            }

            return sendSuccess('success', 'Selamat anda berhasil menghapus data!');
        } catch (Exception $err) {
            return sendError('Server error', $err->getMessage());
        }
    }

    public function get_konversi_uom()
    {
        $uom = (string) $this->input->post('uom');
        $res = [];
        if($uom){
            $res = $this->item->get_konversi_uom($uom);
        }
        echo json_encode($res);
        
    }

    public function get_detail()
    {
        try {
            $item_id = (int) $this->encrypt->decode(base64url_decode($this->input->post('item_id')));
            $this->load->model('M_datatables', 'datatables');
            $params = [
                'table' => 'item_uom a',
                'select' => [
                    'a.UOM_CODE AS Satuan_lain,  a.TO_QTY AS Konversi,  a.BASE_UOM_FLAG AS `Default`',
                ],
                'where' => ['a.ITEM_ID' => $item_id],
                'column_search' => ['a.UOM_CODE', 'a.TO_QTY','a.BASE_UOM_FLAG'],
                'column_order'  => [null,'a.UOM_CODE', 'a.TO_QTY',null,'a.BASE_UOM_FLAG'],
            ];
            echo json_encode($this->datatables->generate($params, function($row, $no) {
                $uom        = $this->input->post('uom');
                $konversi   = numb_format((float)$row->Konversi);
                return [
                    'no' => $no,
                    'satuan_lain' => $row->Satuan_lain,
                    'konversi' => $konversi,
                    'keterangan' => "1 {$row->Satuan_lain} = {$konversi} {$uom}",
                    'flag_default' => $row->Default == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>'
                ];
            }));
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function import()
    {
        checkAccess('import');
        try {
            $data['title']      = 'Import';
            $data['breadcrumb'] = 'Import';
            $data['active_job'] = $this->item->get_active_job();
            $this->template->load('template', 'item/import', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function template_import()
    {
        checkAccess('import');
        
        $this->load->library('simpleexcel');

        $masterData = $this->getMasterData();

        $headerTemplate = $this->_headerTemplate();

        $sheets = [];
        
        $sheets[] = [
            'title'       => 'Template Import',
            'header'      => $headerTemplate,
            'data'        => [],
            'header_info' => [
                'h1: TEMPLATE IMPORT DATA ITEM',
                'Kolom dengan tanda (*) WAJIB DIISI.',
                'Kolom dengan tanda (**) WAJIB DIISI bersyarat:',
                '- SUPPLIER wajib diisi jika KONSINYASI bernilai Y.',
                'Gunakan Nama dari sheet referensi di sebelah, BUKAN KODE.'
            ]
        ];
        foreach ($masterData as $sheetName => $data) {
            $data['title'] = strtoupper($sheetName);
            $sheets[] = $data;
        }

        $config = [
            'background'    => '3D7BB9',
            'color'         => 'FFFFFF',
            'freeze_header' => true,
            'auto_filter'   => true,
        ];

        $this->simpleexcel->write($sheets, 'Template_Import_Item_' . date('Ymd'), $config);
    }

    private function getMasterData(){
        $datas = [];
        $ckLastData = $this->item->checkLastData();
        foreach ([
            'brand::merek'      => ['Brand_Code','Brand_Name'],
            'category::group'   => ['Category_Code','Category_Name'],
            'uom'               => ['UOM_CODE' => 'Satuan'],
            'type::typeinventory'      => ['Trade_Type' => 'Type', 'Trade_Note' => 'Note'],
            'rak'               => ['Grade' => 'Rak', 'Note'],
            'jenis'             => ['Jenis_Item' => 'Jenis', 'Note'],
            'grade'             => ['Grade','Note'],
            'madeIn::made_in'   => ['Made_In','Note'],
            'komoditi::tipe'    => ['Komoditi','Note'],
            'supplier'          => ['Kode','Supplier'],
        ] as $k => $header) {
            $x = explode('::',$k);
            $k = $x[0];
            $k_db = isset($x[1])?$x[1]:$k;
            $datas['ref_'.($k=='uom'?'satuan':$k)] = [
                'header'    => $header,
                'data'      => $this->item->cacheData($k, $k_db, $ckLastData)
            ];
        }

        return $datas;
    }

    private function _headerTemplate()
    {
        return [
            'brand'              => 'BRAND (*)',
            'category'           => 'CATEGORY (*)',
            'part_number'        => 'PART NUMBER (*)',
            'description'        => 'DESCRIPTION (*)',
            'assy_code'          => 'ASSY CODE',
            'uom'                => 'SATUAN (*)',
            'type'               => 'TYPE (*)',
            'min_stock'          => 'MIN STOCK',
            'lead_time'          => 'LEAD TIME (Week) (*)',
            'rak'                => 'RAK',
            'length'             => 'LENGTH (M)',
            'width'              => 'WIDTH (M)',
            'height'             => 'HEIGHT (M)',
            'weight'             => 'WEIGHT (KG)',
            'jenis'              => 'JENIS (*)',
            'grade'              => 'GRADE (*)',
            'hpp'                => 'HPP',
            'note'               => 'KETERANGAN',
            'min_ord_qty'        => 'MIN ORDER QTY',
            'made_in'            => 'MADE IN',
            'komoditi'           => 'KOMODITI (*)',
            'konsinyasi'         => 'KONSINYASI (Y/N)',
            'supplier'           => 'SUPPLIER (**)'
        ];
    }

    public function upload()
    {
        checkAccess('import');
        if (!$this->input->is_ajax_request()) {
            redirect('404');
        }

        $upload_dir = FCPATH . 'assets/upload/item_import/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $config = [
            'upload_path'   => $upload_dir,
            'allowed_types' => 'xlsx',
            'max_size'      => 20480, // 20MB
            'encrypt_name'  => TRUE,
        ];
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('import_file')) {
            echo json_encode([
                'status'  => 'error',
                'message' => $this->upload->display_errors('', '')
            ]);
            return;
        }

        $upload_data    = $this->upload->data();
        $file_path      = $upload_data['full_path'];
        $filename       = $upload_data['file_name'];
        $data_start_row = 7;

        try {
            $reader   = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file_path);
            $info     = $reader->listWorksheetInfo($file_path);
            $totalRows = (int)($info[0]['totalRows'] ?? 0);

            $max_progress = max(0, $totalRows - $data_start_row);
        } catch (Exception $e) {
            @unlink($file_path);
            sendWarning('Gagal membaca file Excel: ' . $e->getMessage());die;
        }

        if ($max_progress <= 0) {
            @unlink($file_path);
            sendWarning('File kosong atau tidak ada data (data dimulai dari baris ke-8).');die;
        }else if($max_progress > 1000){
            @unlink($file_path);
            sendWarning('Maksimal item yang boleh diimport adalah 1rb');die;
        }

        $this->db->trans_begin();
        try {
            $job_id = $this->item->create_job([
                'IMPORT_KEY'    => 'item',
                'MAX_PROGRESS'  => (int) $max_progress,
                'CHUNK'         => 100,
                'JSON_TEXT'     => json_encode(['filename' => $filename])
            ]);

            if ($this->db->trans_status() === FALSE) {
                throw new Exception("Database Error");
            }

            $this->db->trans_commit();

            $db_info = [
                'hostname' => $this->db->hostname,
                'username' => $this->db->username,
                'password' => $this->db->password,
                'database' => $this->db->database,
                'port'     => isset($this->db->port) ? $this->db->port : 3306,
            ];
            $encoded_db = base64url_encode($this->encrypt->encode(json_encode($db_info)));

            $encode_id  = base64url_encode($this->encrypt->encode($job_id));
            $app_path   = FCPATH . 'app';
            $cmd = 'php ' . escapeshellarg($app_path)
                . ' item/import_data cli_process '
                . escapeshellarg($encode_id) . ' '
                . escapeshellarg($encoded_db);

            $os = strtoupper(substr(PHP_OS, 0, 3));

            if ($os === 'WIN') {
                pclose(popen('start /B "" ' . $cmd . ' > NUL 2>&1', 'r'));
            } else {
                $descriptors = [
                    0 => ['file', '/dev/null', 'r'],
                    1 => ['file', '/dev/null', 'w'],
                    2 => ['file', '/dev/null', 'w'],
                ];
                $proc = proc_open($cmd . ' &', $descriptors, $pipes);
                if (is_resource($proc)) {
                    proc_close($proc);
                }
            }

            sendSuccess($encode_id, '');

        } catch (Throwable $e) {
            $this->db->trans_rollback();
            
            if (file_exists($file_path)) {
                @unlink($file_path);
            }

            $db_error = $this->db->error();
            $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $e->getMessage() : '';
            sendWarning('Gagal menyimpan data!' . $error_msg);
        }
    }

    public function get_status()
    {
        checkAccess('import');

        if (!$this->input->is_ajax_request()) {
            redirect('404');
        }

        $job_id = (int) $this->encrypt->decode(base64url_decode($this->input->get('job_id')));
        $job    = $this->item->get_job($job_id);

        if (!$job) {
            sendWarning('Data import tidak ditemukan.');die();
        }

        $res = [
            'status'   => $job['STATUS'],
            'progress' => (int)$job['PROGRESS'],
            'total'    => (int)$job['MAX_PROGRESS'],
            'message'  => $job['MESSAGE'],
        ];

        if ($job['STATUS'] === 'done') {
            $result = $this->_read_result_counts($job);
            $res['success_count'] = count($result['success'] ?? []);
            $res['failed_count']  = count($result['failed']  ?? []);

            $data['result'] = $result;
            $data['header'] = $this->_headerTemplate();
            $data['success_count']  = $res['success_count'];
            $data['failed_count']   = $res['failed_count'];
            $res['result_list']     = $this->load->view($this->access['url'].'/import_result',$data,true);
        }

        sendSuccess($res,'');
    }

    private function _read_result_counts(array $job)
    {
        $json        = json_decode($job['JSON_TEXT'], true);
        $upload_dir  = FCPATH . 'assets/upload/item_import/';
        $result_filename = $json['result_filename'];
        $result_path = $upload_dir . $result_filename;
        
        if (!file_exists($result_path)) {
            if(!file_exists($upload_dir.'archived/'.$result_filename)){
                return ['success' => [], 'failed' => []];
            }else{
                $result_path = $upload_dir. 'archived/' . $result_filename;
            }
        }

        $res_json = json_decode(file_get_contents($result_path), true);
        return [
            'success' => $res_json['success'],
            'failed'  => $res_json['failed'],
        ];
    }

    public function download_failed($encode_id)
    {
        checkAccess('import');
        $job_id = (int) $this->encrypt->decode(base64url_decode($encode_id));
        $job    = $this->item->get_job($job_id);
        if (!$job) {
            $this->session->set_flashdata('warning', 'Anda tidak ada akses untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
            redirect($this->access['url']);
            die();
        }
        $sheets = [];
        $result = $this->_read_result_counts($job);
        $headerTemplate = $this->_headerTemplate();
        $headerTemplate['error_reason'] = 'Pesan Gagal';

        $sheets[] = [
            'title'       => 'Template Import',
            'header'      => $headerTemplate,
            'data'        => $result['failed'] ?? [],
            'header_info' => [
                'h1: TEMPLATE IMPORT DATA ITEM',
                'Kolom dengan tanda (*) WAJIB DIISI.',
                'Kolom dengan tanda (**) WAJIB DIISI bersyarat:',
                '- SUPPLIER wajib diisi jika KONSINYASI bernilai Y.',
                'Gunakan Nama dari sheet referensi di sebelah, BUKAN KODE.'
            ]
        ];

        $config = [
            'background'    => '3D7BB9',
            'color'         => 'FFFFFF',
            'freeze_header' => true,
            'auto_filter'   => true,
        ];

        $this->load->library('simpleexcel');
        $this->simpleexcel->write($sheets, 'Template_Import_Item_(Gagal Import)_' . date('Ymd'), $config);
    }

    public function import_cancel()
    {
        checkAccess('import');
        if (!$this->input->is_ajax_request()) redirect('404');
    
        $job_id = (int) $this->encrypt->decode(base64_decode($this->input->post('job_id')));
        if (!$job_id) {
            sendWarning('Data import tidak valid');die;
        }
    
        $job = $this->item->get_job($job_id);
    
        if (!$job) {
            sendWarning('Data import tidak valid');die;
        }
    
        if (!in_array($job['STATUS'], ['running', 'pending', 'queued','done'])) {
            sendWarning('Import sudah selesai atau gagal');die;
        }
    
        //  Kill SP di MySQL via KILL QUERY
        $thread_id = !empty($job['THREAD_ID']) ? (int) $job['THREAD_ID'] : 0;
    
        if ($thread_id > 0) {
            $thread_exists = $this->db->query(
                "SELECT ID FROM information_schema.PROCESSLIST WHERE ID = ?",
                [$thread_id]
            )->row();
    
            if ($thread_exists) {
                $this->db->query("KILL QUERY {$thread_id}");
                usleep(300000);
                $still_alive = $this->db->query(
                    "SELECT ID FROM information_schema.PROCESSLIST WHERE ID = ?",
                    [$thread_id]
                )->row();
    
                if ($still_alive) {
                    $this->db->query("KILL {$thread_id}");
                }
            }
        }
    
        //  Kill PHP process (cli_process + monitor_progress)
        $pid = !empty($job['PROCESS_ID']) ? (int) $job['PROCESS_ID'] : 0;
    
        if ($pid > 0) {
            if (function_exists('posix_getpgid')) {
                $pgid = posix_getpgid($pid);
                if ($pgid !== false && $pgid > 0) {
                    posix_kill(-$pgid, SIGTERM);
                    usleep(200000);
                    posix_kill(-$pgid, SIGKILL);
                } else {
                    posix_kill($pid, SIGTERM);
                    usleep(200000);
                    posix_kill($pid, SIGKILL);
                }
            } else {
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                    proc_open("taskkill /F /T /PID {$pid}", [], $pipes);
                } else {
                    proc_open("kill -TERM -- -{$pid}", [], $pipes);
                    usleep(200000);
                    proc_open("kill -KILL -- -{$pid}", [], $pipes);
                    proc_open("pkill -KILL -P {$pid}", [], $pipes);
                }
            }
        }
    
        //delete file
        $json               = json_decode($job['JSON_TEXT'], true);
        $upload_dir         = FCPATH . 'assets/upload/item_import/';
        $result_filename    = $json['result_filename'];
        $result_path        = $upload_dir . $result_filename;
        $exel_file          = $upload_dir.$json['filename'];
        if(file_exists($result_path)){
            @unlink($result_path);
        }
        if(file_exists($exel_file)){
            @unlink($exel_file);
        }

        //  Update status di DB
        $this->item->update_job($job_id, [
            'STATUS'      => 'failed',
            'MESSAGE'     => 'Dibatalkan oleh user.',
            'FINISHED_AT' => date('Y-m-d H:i:s'),
            'JSON_TEXT'   => null,
            'PROCESS_ID'  => null,
            'THREAD_ID'   => null,
        ]);
    
        sendSuccess('','Import berhasil dibatalkan.');
    }

    public function finalize_import(){
        checkAccess('import');
        if (!$this->input->is_ajax_request()) redirect('404');
    
        $job_id = (int) $this->encrypt->decode(base64_decode($this->input->post('job_id')));
        if (!$job_id) {
            sendWarning('Data import tidak valid');die;
        }
    
        $job    = $this->item->get_job($job_id);
    
        if (!$job) {
            sendWarning('Data import tidak valid');die;
        }
    
        if ($job['STATUS'] != 'done') {
            sendWarning('Import belum selesai atau arsip');die;
        }

        $result = $this->_read_result_counts($job);
        unset($result['failed']);
        if (empty($result['success'])) {
            sendWarning('Tidak ada data success untuk diproses.');die;
        }

        $this->db->trans_begin();
        try {
            $json   = json_decode($job['JSON_TEXT'], true);

            $insert_data    = [];
            $user_id        = (int) $this->session->id;
            $now            = date('Y-m-d H:i:s');
            foreach ($result['success'] as $p) {
                $param = $p['insert'];
                $param['ITEM_CODE']         = null;
                $param['PART_NUMBER']       = $p['part_number'] ? htmlspecialchars($p['part_number']) : null;
                $param['ITEM_DESCRIPTION']  = $p['description'] ? htmlspecialchars($p['description']) : null;
                $param['ASSY_CODE']         = $p['assy_code'] ? htmlspecialchars($p['assy_code']) : null;
                $param['MIN_STOCK']         = (float) $p['min_stock'];
                $param['LEAD_TIME']         = (int) $p['lead_time'];
                $param['LOKASI']            = $p['rak'] ? htmlspecialchars($p['rak']) : null;
                $param['PANJANG']           = (float) $p['length'];
                $param['CUSTOM1']           = 'M';
                $param['LEBAR']             = (float) $p['width'];
                $param['CUSTOM2']           = 'M';
                $param['TINGGI']            = (float) $p['height'];
                $param['CUSTOM3']           = 'M';
                $param['BERAT']             = (int) $p['weight'];
                $param['CUSTOM4']           = 'KG';
                $param['HPP_AWAL']          = (float) $p['hpp'];
                $param['NOTE']              = $p['note'] ? htmlspecialchars($p['note']) : null;
                $param['MOQ']               = (int) $p['min_ord_qty'];
                $param['CUSTOM5']           = $param['UOM_CODE'];
                $param['ACTIVE_FLAG']       = null;
                $param['LAST_UPDATE_BY']    = $user_id;
                $param['LAST_UPDATE_DATE']  = $now;
                $param['ITEM_KMS']          = $p['konsinyasi'] == 'Y'?'Y':'N';
                $insert_data[] = $param;
            }
            if(!empty($insert_data)){
                $this->db->insert_batch('item', $insert_data);
            }

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Database transaction gagal.');
            }

            $json['imported'] = count($insert_data);

            //pindahkan result file ke archived
            $upload_dir         = FCPATH . 'assets/upload/item_import/';
            $result_filename    = $json['result_filename'];
            $result_path        = $upload_dir . $result_filename;
            $result_path_move   = $upload_dir.'archived/'.$result_filename;
            $exel_file          = $upload_dir.$json['filename'];

            if(rename($result_path, $result_path_move)) {}
            if(file_exists($exel_file)){
                @unlink($exel_file);
                unset($json['filename']);
            }

            $this->item->update_job($job_id, [
                'STATUS'   => 'archived',
                'JSON_TEXT' => json_encode($json)
            ]);
            $this->db->trans_commit();
            
            sendSuccess('', numb_format(count($insert_data),0)." data berhasil disimpan.");
        } catch (Exception $e) {
            $this->db->trans_rollback();
            sendWarning('Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function import_history()
    {
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'import_history a',
            'select' => [
                'a.IMPORT_HISTORY_ID,a.MESSAGE,a.JSON_TEXT,a.CREATED_DATE,a.STARTED_AT,a.FINISHED_AT,a.STATUS,b.ERP_USER_NAME',
            ],
            'joins' => [
                ['erp_user b', 'b.ERP_USER_ID = a.CREATED_BY', 'inner'],
            ],
            'where' => [
                'a.IMPORT_KEY'  => 'item',
            ],
            'where_in' => [
                'a.STATUS'      => ['archived','done']
            ],
            'column_search' => [null,'a.MESSAGE','b.ERP_USER_NAME', 'a.CREATED_DATE','a.STATUS'],
            'column_order'  => [null,'a.MESSAGE','b.ERP_USER_NAME', 'a.CREATED_DATE'. 'a.STATUS'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            
            $id   = base64url_encode($this->encrypt->encode($row->IMPORT_HISTORY_ID));
            $json = json_decode($row->JSON_TEXT, true);
            $upload_dir  = FCPATH . 'assets/upload/item_import/archived/';
            $result_path = $upload_dir . (isset($json['result_filename'])?$json['result_filename']:'null');
            $action = '';
            if(file_exists($result_path)){
                $action = '<a href="'.base_url('item/download_failed/'.$id).'" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="left" title="Download data gagal" target="_blank">
                    <i class="ri-download-2-line"></i>
                </a>';
            }
            if($row->STATUS == 'done'){
                $action = '<button type="button "class="btn btn-sm btn-outline-primary btn-proccess-import" data-id="'.$id.'" data-bs-toggle="tooltip" data-bs-placement="left" title="Lanjutkan Proses Import">
                    <i class="ri-refresh-fill"></i>
                </button>';
            }
            
            $res = [
                'no'        => $no,
                'message'   => $row->MESSAGE,
                'user'      => $row->ERP_USER_NAME,
                'tanggal'   => $row->CREATED_DATE,
                'status'    => '<span class="badge bg-'.($row->STATUS=='done'?'primary':'success').' text-uppercase">'.$row->STATUS.'</span>',
                'action'    => $action
            ];
            return $res;
        }));
    }
}
