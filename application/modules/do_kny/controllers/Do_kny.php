<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Do_kny extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Do_kny_model', 'do_kny');
    }

    public function index()
    {
        try {
            $data['title'] = 'DO KNY';
            $data['breadcrumb'] = 'DO KNY';
            $this->template->load('template', 'do_kny/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->do_kny->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $do_kny) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $do_kny->STATUS ? $do_kny->STATUS : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('do_kny/detail/' . base64url_encode($this->encrypt->encode($do_kny->INVENTORY_OUT_ID))) . '">
                ' . ($do_kny->No_Transaksi ? $do_kny->No_Transaksi : '-') . '
            </a>';
            $row['po_customer'] = $do_kny->PO_Customer ? $do_kny->PO_Customer : '-';
            $row['no_so'] = $do_kny->NO_SO ? $do_kny->NO_SO : '-';
            $row['tanggal'] = $do_kny->Tanggal ? date('Y-m-d H:i', strtotime($do_kny->Tanggal)) : '-';
            $row['customer'] = $do_kny->Customer ? $do_kny->Customer : '-';
            $row['sales'] = $do_kny->Sales ? $do_kny->Sales : '-';
            $row['storage'] = $do_kny->S_Loc ? $do_kny->S_Loc : '-';

            $row['inventory_out_id'] = $this->encrypt->encode($do_kny->INVENTORY_OUT_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->do_kny->count_all(),
            "recordsFiltered" => $this->do_kny->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $inventory_out_id = $this->encrypt->decode($this->input->post('inventory_out_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->do_kny->count_detail_by_inventory_out_id($inventory_out_id);

            $list = $this->do_kny->get_detail_by_inventory_out_id($inventory_out_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"            => $no++,
                    "nama_item"     => $d->Nama_Item ?? '-',
                    "kode_item"     => $d->Kode_Item ?? '-',
                    "jumlah"        => number_format((float)$d->Qty, 2, '.', '') ?? '-',
                    "satuan"        => $d->UoM ?? '-',
                    "no_mr"         => $d->No_MR ?? '-',
                    "s_loc"         => $d->S_Loc ?? '-',
                    "note"          => $d->Note ?? '-',
                ];
            }
            $output = [
                "draw" => intval($draw),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $data
            ];

            echo json_encode($output);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_location_by_customer()
    {
        try {
            $customer = $this->input->post("customer");
            $data = $this->db->query("SELECT
                a.PERSON_SITE_ID,
                a.SITE_NAME,
                a.ADDRESS1,
                a.ADDRESS2,
                a.ADDRESS3,
                a.CITY,
                a.PRIMARY_SHIP 
            FROM
                person_site a 
            WHERE
                a.PERSON_SITE_ID = '{$customer}' 
                AND a.ACTIVE_FLAG = 'Y'");

            if ($data->num_rows() > 0) {
                $result = array(
                    'status' => 'success',
                    'data' => $data->result_array(),
                );
            } else {
                $result = array(
                    'status' => 'not found',
                    'data' => [],
                );
            }
            echo json_encode($result);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function getSo()
    {
        try {
            $customer = $this->input->post('customer');
            $storage = $this->input->post('storage');

            $data = $this->db->query("SELECT
                b.SO_DETAIL_ID,
                b.SO_ID,
                b.BUILD_ID,
                a.DOCUMENT_TYPE_ID,
                a.STATUS_ID,
                FN_GET_VAR_NAME ( a.STATUS_ID ) AS STATUS_NAME,
                a.DOCUMENT_DATE,
                a.DOCUMENT_NO,
                a.PO_NO AS DOCUMENT_REFF_NO,
                psn.PERSON_ID,
                psn.PERSON_CODE,
                psn.PERSON_NAME,
                w.WAREHOUSE_ID,
                w.WAREHOUSE_NAME,
                k.KARYAWAN_ID,
                k.FIRST_NAME,
                k.LAST_NAME,
                a.PPN_CODE,
                a.PPN_PERCEN,
                a.PPH_CODE,
                a.PPH_PERCEN
            FROM
                so a
                JOIN so_detail b ON a.SO_ID = b.SO_ID
                JOIN build bl ON b.BUILD_ID = bl.BUILD_ID
                JOIN item i ON b.ITEM_ID = i.ITEM_ID
                JOIN person psn ON a.PERSON_ID = psn.PERSON_ID
                JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
                JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID 
            WHERE
                b.ENTERED_QTY > 0 
                AND b.BASE_QTY > 0 
                AND b.RECEIVED_ENTERED_QTY < b.ENTERED_QTY * b.BASE_QTY / NULLIF( b.RECEIVED_BASE_QTY, 0 ) 
                AND bl.APPROVED_FLAG = 'Y' 
                AND bl.DOCUMENT_TYPE_ID = 3 
                AND w.WAREHOUSE_ID = {$storage}
                AND psn.PERSON_ID = {$customer}
            GROUP BY
                a.SO_ID 
            ORDER BY
                bl.DOCUMENT_DATE DESC,
                b.SO_DETAIL_ID
            ");

            if ($data->num_rows() > 0) {
                $result = array(
                    'status' => 'success',
                    'data' => $data->result_array(),
                );
            } else {
                $result = array(
                    'status' => 'not found',
                    'data' => [],
                );
            }
            echo json_encode($result);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function getDetailSo()
    {
        try {
            $so_id = $this->input->post('so_id');
            $customer = $this->input->post('customer');
            $storage = $this->input->post('storage');

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->do_kny->count_detail_by_so_id($so_id, $customer, $storage);

            $list = $this->do_kny->get_detail_by_so_id($so_id, $customer, $storage, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "checkbox"      => '<input type="checkbox" class="childCheckbox" data-so_detail_id="' . $d->SO_DETAIL_ID . '" data-so_id="' . $d->SO_ID . '" data-no_mr="' . $d->DOCUMENT_NO . '", data-build_id="' . $d->BUILD_ID . '" data-item_id="' . $d->ITEM_ID . '" data-kode_item="' . htmlspecialchars($d->ITEM_CODE ?? '') . '" data-nama_item="' . htmlspecialchars($d->ITEM_DESCRIPTION ?? '') . '" data-jumlah="' . $d->ENTERED_QTY . '" data-base_qty="' . $d->BASE_QTY . '" data-sisa="' . $d->BALANCE . '" data-satuan="' . $d->ENTERED_UOM . '" data-unit_price="' . $d->UNIT_PRICE . '" data-subtotal="' . $d->SUBTOTAL . '" data-harga_input="' . $d->HARGA_INPUT . '" data-berat="' . $d->BERAT . '" data-note="' . htmlspecialchars($d->NOTE ?? '') . '" data-memo="' . htmlspecialchars($d->DESKRIPSI ?? '') . '" data-po_no="' . $d->PO_NO . '" data-karyawan_id="' . $d->KARYAWAN_ID . '" data-ppn_code="' . $d->PPN_CODE . '" data-ppn_percen="' . $d->PPN_PERCEN . '" data-pph_code="' . $d->PPH_CODE . '" data-pph_percen="' . $d->PPH_PERCEN . '" data-karyawan="' . $d->FIRST_NAME . ' [' . $d->LAST_NAME . ']" data-diskon_price="' . $d->DISCOUNT_PRICE . '" data-hpp="' . $d->HPP . '" data-diskon_input="' . $d->DISKON_INPUT . '" data-diskon_persen="' . $d->DISCOUNT_PERCEN . '">',
                    "no"            => $no++,
                    "no_mr"         => $d->DOCUMENT_NO,
                    "kode_item"     => $d->ITEM_CODE,
                    "nama_item"     => $d->ITEM_DESCRIPTION,
                    "jumlah"        => number_format((float)$d->ENTERED_QTY, 2, '.', ''),
                    "sisa"          => number_format((float)$d->BALANCE, 2, '.', ''),
                    "satuan"        => $d->ENTERED_UOM,
                    "note"          => $d->NOTE,
                    "memo"          => $d->DESKRIPSI,
                ];
            }
            $output = [
                "draw" => intval($draw),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $data
            ];

            echo json_encode($output);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function getStatus()
    {
        $inventory_out_id = $this->encrypt->decode($this->input->post('inventory_out_id'));

        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME FROM inventory_out a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.INVENTORY_OUT_ID = {$inventory_out_id}");

        if ($data->num_rows() > 0) {
            $result = array(
                'status' => 'sukses',
                'data' => $data->result_array(),
            );
        } else {
            $result = array(
                'status' => 'data tidak ditemukan',
                'data' => 'data tidak ditemukan'
            );
        }
        echo json_encode($result);
    }

    public function add()
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('customer', 'customer', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('storage', 'storage', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah DO KNY';
                $data['breadcrumb'] = 'Tambah DO KNY';
                $data['customer'] = $this->do_kny->get_customer();
                $data['storage'] = $this->do_kny->get_storage();
                $data['detail'] = $this->input->post('detail');
                $this->template->load('template', 'do_kny/add', $data);
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail wajib diisi!');
                    redirect('do_kny/add');
                }

                $id_menu = $this->db->query("SELECT erp_menu_id FROM erp_menu WHERE erp_menu_name = '{$this->uri->segment('1')}'")->row();

                $erp_table_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_ID = {$id_menu->erp_menu_id}")->row();

                $seq = $this->db->query("SELECT SEQ($erp_table_id->ERP_TABLE_ID) AS SEQ1")->row();

                $post['seq'] = $seq->SEQ1;
                setVariableMysql();
                $new = $this->db->query("SELECT @NEW AS new")->row();
                $post['new'] = $new->new;

                $this->db->trans_begin();

                $total = 0;

                // ======================
                // INSERT DETAIL
                // ======================
                foreach ($detail['nama_item'] as $i => $val) {

                    $jumlah_raw  = $detail['jumlah'][$i] ?? null;
                    $balance_raw = $detail['balance'][$i] ?? null;

                    if (
                        $jumlah_raw === '' || $jumlah_raw === null || !is_numeric($jumlah_raw)
                    ) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" tidak boleh kosong');
                        redirect('do_kny/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('do_kny/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('do_kny/add');
                    }

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);
                    $harga_input = str_replace([','], '', $detail['harga_input'][$i]);
                    $subtotal = $jumlah * $harga_input;
                    $total += $subtotal;

                    $dataDetail = [
                        'INVENTORY_OUT_ID'      => $post['seq'],
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'DISCOUNT_PRICE'        => $detail['diskon_price'][$i],
                        'SUBTOTAL'              => $subtotal,
                        'HPP'                   => $detail['hpp'][$i],
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'WAREHOUSE_ID'          => $post['storage'],
                        'SO_DETAIL_ID'          => $detail['so_detail_id'][$i],
                        'BUILD_ID'              => $detail['build_id'][$i],
                        'DISCOUNT_PERCEN'       => $detail['diskon_persen'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'DISKON_INPUT'          => $detail['diskon_input'][$i],
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'KET'                   => $detail['memo'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'CREATED_BY'            => $this->session->userdata('id'),
                        'CREATED_DATE'          => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('inventory_out_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $data['title'] = 'Tambah DO KNY';
                        $data['breadcrumb'] = 'Tambah DO KNY';
                        $data['customer'] = $this->do_kny->get_customer();
                        $data['storage'] = $this->do_kny->get_storage();
                        $data['detail'] = $this->input->post('detail');
                        $data['warning'] =  $error['message'];
                        $this->template->load('template', 'do_kny/add', $data);
                        return;

                        // $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        // redirect('do_kny/add');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'INVENTORY_OUT_ID'      => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['po_customer'],
                    'PERSON_ID'             => $post['customer'],
                    'PERSON_SITE_ID'        => $post['location_id'],
                    'WAREHOUSE_ID'          => $post['storage'],
                    'KARYAWAN_ID'           => $post['sales_id'],
                    'PPN_CODE'              => $post['ppn_code'],
                    'PPN_PERCEN'            => $post['ppn_percen'],
                    'SO_ID'                 => $post['so_id'],
                    'PPH_CODE'              => $post['pph_code'],
                    'PPH_PERCEN'            => $post['pph_percen'],
                    'NOTE'                  => $post['keterangan'],
                    'KONSINYASI_FLAG'       => 'Y',
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('inventory_out', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $data['title'] = 'Tambah DO KNY';
                    $data['breadcrumb'] = 'Tambah DO KNY';
                    $data['customer'] = $this->do_kny->get_customer();
                    $data['storage'] = $this->do_kny->get_storage();
                    $data['detail'] = $this->input->post('detail');
                    $data['warning'] =  $error['message'];
                    $this->template->load('template', 'do_kny/add', $data);
                    return;

                    // $this->session->set_flashdata('warning', $error['message']);
                    // redirect('do_kny/add');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('do_kny/add');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('do_kny/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('customer', 'customer', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('storage', 'storage', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->do_kny->get_inventory_out_id($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['customer'] = $this->do_kny->get_customer();
                    $data['storage'] = $this->do_kny->get_storage();
                    $data['data'] = $query->row();
                    $data['detail'] = $this->input->post('detail');
                    $this->template->load('template', 'do_kny/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('do_kny');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $inventory_out_id   = $this->encrypt->decode($post['inventory_out_id']);
                $detail = $post['detail'] ?? [];

                if (!$inventory_out_id || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail tidak boleh kosong dan wajib diisi!');
                    redirect('do_kny/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('INVENTORY_OUT_DETAIL_ID')
                    ->from('inventory_out_detail')
                    ->where('INVENTORY_OUT_ID', $inventory_out_id)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'INVENTORY_OUT_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['inventory_out_detail_id'])) {
                    foreach ($detail['inventory_out_detail_id'] as $val) {
                        if (!empty($val)) {
                            $postDetailIds[] = $this->encrypt->decode($val);
                        }
                    }
                }

                // ===============================
                // DETAIL YANG HARUS DIHAPUS
                // ===============================
                $deleteIds = array_diff($dbDetailIds, $postDetailIds);

                // ===============================
                // TRANSACTION
                // ===============================
                $this->db->trans_begin();
                $total = 0;

                // ===============================
                // INSERT / UPDATE DETAIL
                // ===============================
                foreach ($detail['jumlah'] as $i => $qtyRaw) {

                    if (empty($detail['nama_item'][$i])) continue;

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);
                    $harga_input = str_replace([','], '', $detail['harga_input'][$i]);
                    $subtotal = $jumlah * $harga_input;
                    $total += $subtotal;

                    $inventory_out_detail_id = !empty($detail['inventory_out_detail_id'][$i])
                        ? $this->encrypt->decode($detail['inventory_out_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'DISCOUNT_PRICE'        => $detail['diskon_price'][$i],
                        'SUBTOTAL'              => $subtotal,
                        'HPP'                   => $detail['hpp'][$i],
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'WAREHOUSE_ID'          => $post['storage'],
                        'SO_DETAIL_ID'          => $detail['so_detail_id'][$i],
                        'BUILD_ID'              => $detail['build_id'][$i],
                        'DISCOUNT_PERCEN'       => $detail['diskon_persen'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'DISKON_INPUT'          => $detail['diskon_input'][$i],
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'KET'                   => $detail['memo'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    if ($inventory_out_detail_id) {
                        // UPDATE
                        $this->db->update(
                            'inventory_out_detail',
                            $dataDetail,
                            ['INVENTORY_OUT_DETAIL_ID' => $inventory_out_detail_id, 'INVENTORY_OUT_ID' => $inventory_out_id]
                        );
                    } else {
                        // INSERT
                        $dataDetail['INVENTORY_OUT_ID'] = $inventory_out_id;
                        $dataDetail['CREATED_BY']       = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE']     = date('Y-m-d H:i:s');

                        $this->db->insert('inventory_out_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('do_kny/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('INVENTORY_OUT_ID', $inventory_out_id)
                        ->where_in('INVENTORY_OUT_DETAIL_ID', $deleteIds)
                        ->delete('inventory_out_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('inventory_out', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['po_customer'],
                    'PERSON_ID'             => $post['customer'],
                    'PERSON_SITE_ID'        => $post['location_id'],
                    'WAREHOUSE_ID'          => $post['storage'],
                    'KARYAWAN_ID'           => $post['sales_id'],
                    'PPN_CODE'              => $post['ppn_code'],
                    'PPN_PERCEN'            => $post['ppn_percen'],
                    'SO_ID'                 => $post['so_id'],
                    'PPH_CODE'              => $post['pph_code'],
                    'PPH_PERCEN'            => $post['pph_percen'],
                    'NOTE'                  => $post['keterangan'],
                    'KONSINYASI_FLAG'       => 'Y',
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['INVENTORY_OUT_ID' => $inventory_out_id]);

                // ===============================
                // COMMIT / ROLLBACK
                // ===============================
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Data berhasil diperbarui!');
                }
                redirect('do_kny/detail/' . $id);
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function del()
    {
        $id = $this->encrypt->decode($this->input->post('id'));

        $this->db->query("CALL SET_VAR()");
        $this->db->close();
        $this->db->initialize();

        $del = $this->db->query("SELECT FN_GET_VAR_VALUE('DELETE') AS del")->row();
        $status = $del->del;

        $this->db->trans_begin();

        $delResult = $this->do_kny->delete($id);
        if ($delResult !== true) {
            $this->db->trans_rollback();
            $this->_jsonError($delResult);
            return;
        }

        $updResult = $this->do_kny->updateStatus($id, $status);
        if ($updResult !== true) {
            $this->db->trans_rollback();
            $this->_jsonError($updResult);
            return;
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            $this->_jsonError($this->db->error());
            return;
        }

        $this->db->trans_commit();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => true,
                'message' => 'Data berhasil dihapus!'
            ]));
    }

    private function _jsonError($db_error)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => false,
                'message' => 'Gagal menghapus RHO!',
                'error'   => $db_error['message'],
                'code'    => $db_error['code']
            ]));
    }

    public function get_info($id)
    {
        $id = (int) $this->encrypt->decode(base64url_decode($id));
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'inventory_out_detail b',
            'select' => [
                'b.INVENTORY_OUT_DETAIL_ID, i.ITEM_DESCRIPTION Nama_Item, i.ITEM_CODE Kode_Item, b.ENTERED_UOM Satuan, b.ENTERED_QTY DO',
                ['(b.INVOICE_ENTERED_QTY / b.BASE_QTY) AS INV', FALSE],
                ['(b.ENTERED_QTY - (b.INVOICE_ENTERED_QTY / b.BASE_QTY)) AS SISA', FALSE],
            ],
            'joins' => [
                ['item i', 'b.ITEM_ID = i.ITEM_ID', 'inner'],
            ],
            'where' => ['b.INVENTORY_OUT_ID' => $id],
            'column_search' => ['i.ITEM_DESCRIPTION', 'i.ITEM_CODE', 'b.ENTERED_UOM', 'b.ENTERED_QTY'],
            'column_order'  => [null, null, 'i.ITEM_DESCRIPTION', 'i.ITEM_CODE', 'b.ENTERED_UOM', 'b.ENTERED_QTY', '(b.INVOICE_ENTERED_QTY / b.BASE_QTY)', '(b.ENTERED_QTY - (b.INVOICE_ENTERED_QTY / b.BASE_QTY))'],
            'order' => ['i.ITEM_DESCRIPTION' => 'asc'],
        ];

        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            return [
                'no' => $no,
                'do_detail_id' => base64url_encode($this->encrypt->encode($row->INVENTORY_OUT_DETAIL_ID)),
                'nama_item' => $row->Nama_Item,
                'kode_item' => $row->Kode_Item,
                'satuan' => $row->Satuan,
                'do' => number_format((float)$row->DO, 2, '.', ','),
                'inv' => number_format((float)$row->INV, 2, '.', ','),
                'sisa' => number_format((float)$row->SISA, 2, '.', ','),
            ];
        }));
    }

    public function get_info_detail($detail_id)
    {
        $detail_id = (int) $this->encrypt->decode(base64url_decode($detail_id));
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'inventory_out_detail b',
            'select' => [
                'c.DOCUMENT_NO No_Transaksi,c.DOCUMENT_DATE Tanggal,
                    b.ENTERED_UOM Satuan,w.WAREHOUSE_NAME S_Loc',
                ['(a.ENTERED_QTY * b.BASE_QTY) Jumlah', FALSE],

            ],
            'joins' => [
                ['nota_penjualan_detail a', 'b.INVENTORY_OUT_DETAIL_ID = a.INVENTORY_OUT_DETAIL_ID', 'inner'],
                ['nota_penjualan c', 'a.NOTA_PENJUALAN_ID = c.NOTA_PENJUALAN_ID', 'inner'],
                ['warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID', 'inner'],
            ],
            'where' => ['b.INVENTORY_OUT_DETAIL_ID' => $detail_id],
            'order' => ['b.INVENTORY_OUT_DETAIL_ID' => 'asc'],
            'column_search' => ['c.DOCUMENT_NO', 'c.DOCUMENT_DATE', '(a.ENTERED_QTY * b.BASE_QTY)', 'b.ENTERED_UOM', 'w.WAREHOUSE_NAME'],
            'column_order'  => [null, 'c.DOCUMENT_NO', 'c.DOCUMENT_DATE', '(a.ENTERED_QTY * b.BASE_QTY)', 'b.ENTERED_UOM', 'w.WAREHOUSE_NAME'],
        ];
        echo json_encode($this->datatables->generate($params, function ($row, $no) {
            return [
                'no' => $no,
                // 'no_transaksi' => '<a href="' . site_url('do_kny/detail/' . base64url_encode($this->encrypt->encode($row->INVENTORY_OUT_ID))) . '" target="_blank">' . $row->No_Transaksi . '</a>',
                'no_transaksi' => $row->No_Transaksi,
                'tanggal' => date('Y-m-d H:i', strtotime($row->Tanggal)),
                'satuan' => $row->Satuan,
                'jumlah' => number_format((float)$row->Jumlah, 2, '.', ','),
                's_loc' => $row->S_Loc,
            ];
        }));
    }

    public function print($id){
        $id     = (int) $this->encrypt->decode(base64url_decode($id));
        $do    = $this->do_kny->get_do_detail($id)->row();
        if($do){
            $this->load->library('pdf');
            $data = [
                'dir_view' => 'do_kny/pdf',
                'data' => [
                    'do' => $do,
                    'do_detail' => $this->do_kny->get_do_detail_by_inventory_out_id($id)->result()
                ],
                'title' => str_replace('/',' ', $do->DOCUMENT_NO),
            ];
            $html = $this->load->view('template_pdf', $data, true);
            $this->pdf->generate($html, str_replace('/',' ', $do->DOCUMENT_NO), 'A4', 'portrait');
        }
    }
}
