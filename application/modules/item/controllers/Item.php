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
        $approve = '';

        foreach ($list as $item) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';

            // if ($item->APPROVED == 'N') {
            //     $approve = '<form action="' . base_url('item/approveIndex') . '" method="post" class="d-inline">
            //         <input type="hidden" name="idApprove" value="' . $this->encrypt->encode($item->ID) . '">
            //         <button type="submit" id="btn-approve" data-toggle="tooltip" data-placement="bottom" title="Approve" style="background:transparent; border:none;">
            //             <i class="ri ri-thumb-up-fill" style="color: #5664D2;"></i>
            //         </button>
            //     </form> |';
            // } else {
            //     $approve = '';
            // }

            $row['action'] = '
            <div class="d-flex gap-1">
            ' . $approve . '
                <a href="' . base_url('item/detail/' . $this->encrypt->encode($item->ID)) . '" data-toggle="tooltip" data-placement="bottom" title="Detail">
                    <i class="ri ri-zoom-in-fill"></i>
                </a>
            </div>
            ';
            $row['kode_item'] = '
            <a href="' . base_url('item/detail/' . $this->encrypt->encode($item->ID)) . '">
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

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah Item';
                $data['breadcrumb'] = 'Tambah Item';
                $data['brand'] = $this->item->getBrand();
                $data['category'] = $this->item->getCategory();
                $data['uom'] = $this->item->getUom();
                $data['type'] = $this->item->getType();
                $data['rak'] = $this->item->getRak();
                $data['made_in'] = $this->item->getMadeIn();
                $data['komoditi'] = $this->item->getKomoditi();
                $data['jenis'] = $this->item->getJenis();
                $data['grade'] = $this->item->getGrade();
                $data['supplier'] = $this->item->getSupplier();
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
                        if ($this->db->affected_rows() > 0) {
                            $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                            redirect('item/detail/' . $this->encrypt->encode($idItem));
                        } else {
                            $this->session->set_flashdata('warning', 'Gagal menyimpan data detail!');
                            redirect('item/detail/' . $this->encrypt->encode($idItem));
                        }
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data baru!');
                        redirect('item/detail/' . $this->encrypt->encode($idItem));
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
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

            if ($this->input->post('obsolete')) {
                $this->form_validation->set_rules('new_product_name', 'New product name', 'trim|required');
            }
            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode($id);
                $query = $this->item->getItemId($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['brand'] = $this->item->getBrand();
                    $data['category'] = $this->item->getCategory();
                    $data['uom'] = $this->item->getUom();
                    $data['type'] = $this->item->getType();
                    $data['rak'] = $this->item->getRak();
                    $data['made_in'] = $this->item->getMadeIn();
                    $data['komoditi'] = $this->item->getKomoditi();
                    $data['jenis'] = $this->item->getJenis();
                    $data['grade'] = $this->item->getGrade();
                    $data['supplier'] = $this->item->getSupplier();
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
                $this->item->update($post);
                if ($this->db->affected_rows() > 0) {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data baru!');
                    redirect('item/detail/' . $idInput);
                } else {
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('item/detail/' . $idInput);
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

    public function approve()
    {
        $post = $this->input->post();
        $id = $this->encrypt->decode($post['idApprove']);
        $this->item->approve($id);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data baru!');
            redirect('item/detail/' . $post['idApprove']);
        } else {
            $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
            redirect('item/detail/' . $post['idApprove']);
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
}
