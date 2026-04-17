<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Po_kny extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Po_kny_model', 'po_kny');
    }

    public function index()
    {
        try {
            $data['title'] = 'PO KNY';
            $data['breadcrumb'] = 'PO KNY';
            $this->template->load('template', 'po_kny/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->po_kny->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $po_kny) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['status'] = badge_status($po_kny->STATUS, $po_kny->WARNA_STATUS);
            $row['no_transaksi'] = '
            <a href="' . base_url('po_kny/detail/' . base64url_encode($this->encrypt->encode($po_kny->INVOICE_ID))) . '">
                ' . ($po_kny->No_Transaksi ? $po_kny->No_Transaksi : '-') . '
            </a>';
            $row['no_refrensi'] = $po_kny->No_Referensi ? $po_kny->No_Referensi : '-';
            $row['tanggal'] = $po_kny->Tanggal ? date('Y-m-d H:i', strtotime($po_kny->Tanggal)) : '-';
            $row['supplier'] = $po_kny->Supplier ? $po_kny->Supplier : '-';
            $row['s_loc'] = $po_kny->S_Loc ? $po_kny->S_Loc : '-';
            $row['terms'] = $po_kny->Terms ? $po_kny->Terms : '-';
            $row['total'] = $po_kny->Total ? number_format($po_kny->Total, 2, '.', ',') : '-';

            $row['invoice_id'] = $this->encrypt->encode($po_kny->INVOICE_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->po_kny->count_all(),
            "recordsFiltered" => $this->po_kny->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $invoice_id = $this->encrypt->decode($this->input->post('invoice_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->po_kny->count_detail_by_po_id($invoice_id);

            $list = $this->po_kny->get_detail_by_po_id($invoice_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"            => $no++,
                    "nama_item"     => $d->Nama_Item,
                    "kode_item"     => $d->Kode_Item,
                    "jumlah"        => number_format((float)$d->Qty, 2, '.', ''),
                    "satuan"        => $d->UoM,
                    "harga"         => number_format($d->Harga, 2, '.', ','),
                    "diskon"        => number_format($d->Diskon, 2, '.', ','),
                    "total"         => number_format($d->Total, 2, '.', ','),
                    "no_mr"         => $d->No_MR,
                    "s_loc_in"      => $d->S_Loc_In,
                    "note"          => $d->Note,
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

    public function get_location_by_supplier()
    {
        try {
            $supplier = $this->input->post("supplier");
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
                a.PERSON_SITE_ID = '${supplier}' 
                AND a.ACTIVE_FLAG = 'Y' 
            ORDER BY
                PRIMARY_SHIP");

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

    public function getMrq()
    {
        try {
            $storage = $this->input->post('storage');
            $supplier = $this->input->post('supplier');

            $data = $this->db->query("SELECT
                b.INVENTORY_IN_DETAIL_ID,
                b.INVENTORY_IN_ID,
                i.COA_SUSPEND_ID,
                a.DOCUMENT_TYPE_ID,
                a.STATUS_ID,
                s.DISPLAY_NAME as STATUS_NAME,
                s.MENU_ICON,
                bl.DOCUMENT_NO,
                bl.DOCUMENT_DATE,
                bl.DOCUMENT_REFF_NO,
                psn.PERSON_ID,
                psn.PERSON_CODE,
                psn.PERSON_NAME,
                w.WAREHOUSE_ID,
                w.WAREHOUSE_NAME,
                i.ITEM_ID,
                i.ITEM_CODE,
                i.ITEM_DESCRIPTION,
                b.ENTERED_QTY,
                b.BASE_QTY,
            CASE
                    WHEN b.BASE_QTY = 0 
                    OR b.BASE_QTY IS NULL THEN
                        b.ENTERED_QTY ELSE b.ENTERED_QTY - ( b.INVOICE_ENTERED_QTY / b.BASE_QTY ) 
                        END AS BALANCE,
                    b.ENTERED_UOM,
                    b.UNIT_PRICE,
                    b.SUBTOTAL,
                    b.HARGA_INPUT,
                    b.DISCOUNT_PERCEN,
                    b.DISKON_INPUT,
                    i.BERAT,
                    b.NOTE 
                FROM
                    inventory_in a
                    JOIN inventory_in_detail b ON a.INVENTORY_IN_ID = b.INVENTORY_IN_ID
                    JOIN build_detail pd ON b.BUILD_DETAIL_ID = pd.BUILD_DETAIL_ID
                    JOIN build bl ON pd.BUILD_ID = bl.BUILD_ID
                    JOIN item i ON b.ITEM_ID = i.ITEM_ID
                    JOIN person psn ON a.PERSON_ID = psn.PERSON_ID
                    JOIN warehouse w ON b.WAREHOUSE_ID = w.WAREHOUSE_ID
                    JOIN erp_lookup_value s ON s.ERP_LOOKUP_VALUE_ID = a.STATUS_ID
                WHERE
                    b.ENTERED_QTY > 0 
                    AND b.BASE_QTY > 0 
                    AND b.INVOICE_ENTERED_QTY < b.ENTERED_QTY * b.BASE_QTY / NULLIF( b.INVOICE_BASE_QTY, 0 ) 
                    AND bl.DOCUMENT_TYPE_ID = 3
                    AND w.WAREHOUSE_ID = {$storage}
                    AND psn.PERSON_ID = {$supplier}
                ORDER BY
                bl.DOCUMENT_DATE DESC,
                b.INVENTORY_IN_DETAIL_ID;
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

    public function get_hitung_diskon_bertingkat()
    {
        try {
            $harga_input = $this->input->post('harga_input') ?? "";
            $persen = $this->input->post('persen') ?? "";

            $data = $this->db->query("SELECT FN_HITUNGDISKONBERTINGKAT('$harga_input', '$persen') as harga");

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

    public function get_hitung_diskon_bertingkat_header()
    {
        try {
            $total_amount = $this->input->post('total_amount') ?? "";
            $persen = $this->input->post('persen') ?? "";

            $data = $this->db->query("SELECT FN_HITUNGDISKONBERTINGKAT('$total_amount', '$persen') as harga");

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

    public function getStatus()
    {
        $invoice_id = $this->encrypt->decode($this->input->post('invoice_id'));

        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME, b.MENU_ICON FROM invoice a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.INVOICE_ID = {$invoice_id}");

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

            $this->form_validation->set_rules('supplier', 'supplier', 'trim|required');
            $this->form_validation->set_rules('location_id', 'location', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('payment_term', 'payment term', 'trim|required');
            $this->form_validation->set_rules('jatuh_tempo', 'jatuh tempo', 'trim|required');
            $this->form_validation->set_rules('storage', 'storage', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah PO KNY';
                $data['breadcrumb'] = 'Tambah PO KNY';
                $data['supplier'] = $this->po_kny->get_supplier();
                $data['storage'] = $this->po_kny->get_storage();
                $data['payment_term'] = $this->po_kny->get_payment_term();
                $data['ppn_code'] = $this->po_kny->get_ppn_code();
                $data['detail'] = $this->input->post('detail');
                $this->template->load('template', 'po_kny/add', $data);
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail wajib diisi!');
                    redirect('po_kny/add');
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
                        redirect('po_kny/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('po_kny/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('po_kny/add');
                    }

                    if (stripos($post['PPN_CODE'], 'INCL') !== false) {
                        $diskon_price = $detail['diskon_harga'][$i] / (1 + ($detail['diskon_persentase'][$i] / 100));
                    } else {
                        $diskon_price = $detail['diskon_harga'][$i];
                    }

                    $dataDetail = [
                        'INVOICE_ID'             => $post['seq'],
                        'ITEM_ID'                => $detail['item_id'][$i],
                        'ENTERED_QTY'            => $jumlah,
                        'BASE_QTY'               => $detail['base_qty'][$i],
                        'UNIT_PRICE'             => str_replace([','], '', $detail['harga'][$i]),
                        'DISCOUNT_PRICE'         => $diskon_price,
                        'SUBTOTAL'               => $detail['subtotal'][$i],
                        'ENTERED_UOM'            => $detail['satuan'][$i],
                        'COA_ID'                 => $detail['coa_suspend_id'][$i],
                        'WAREHOUSE_ID'           => $post['storage'],
                        'INVENTORY_IN_ID'        => $detail['inventory_in_id'][$i],
                        'INVENTORY_IN_DETAIL_ID' => $detail['inventory_in_detail_id'][$i],
                        'DISCOUNT_PERCEN'        => $detail['diskon_persentase'][$i],
                        'HARGA_INPUT'            => str_replace([','], '', $detail['harga_input'][$i]),
                        'DISKON_INPUT'           => $detail['diskon_harga'][$i],
                        'ITEM_DESCRIPTION'       => $detail['nama_item'][$i],
                        'KET'                    => $detail['memo'][$i],
                        'NOTE'                   => $detail['keterangan'][$i],
                        'BERAT'                  => $detail['berat'][$i],
                        'CREATED_BY'             => $this->session->userdata('id'),
                        'CREATED_DATE'           => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'         => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'       => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('invoice_detail', $dataDetail);
                }

                if (stripos($post['PPN_CODE'], 'INCL') !== false) {
                    $total_diskon_header = $post['TOTAL_DISKON_INPUT_HIDDEN'] / (1 + ($post['PPN_PERCEN'] / 100));
                } else {
                    $total_diskon_header = $post['TOTAL_DISKON_INPUT_HIDDEN'];
                }

                $invoice_type_id = $this->db->query("SELECT FN_GET_VAR_VALUE('PO') as po")->row();

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'INVOICE_ID'            => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_reff'],
                    'PERSON_ID'             => $post['supplier'],
                    'PERSON_SITE_ID'        => $post['person_site_id'],
                    'PAYMENT_TERM_ID'       => $post['payment_term'],
                    'WAREHOUSE_ID'          => $post['storage'],
                    'JTEMPO'                => date('Y-m-d H:i:s', strtotime($post['jatuh_tempo'])),
                    'PPN_CODE'              => $post['PPN_CODE'],
                    'PPN_PERCEN'            => $post['PPN_PERCEN'],
                    'INVOICE_TYPE_ID'       => $invoice_type_id->po,
                    'TOTAL_DISCOUNT_PERCEN' => $post['TOTAL_DISCOUNT_PERCEN'],
                    'TOTAL_DISKON_INPUT'    => $post['TOTAL_DISKON_INPUT_HIDDEN'],

                    'TOTAL_DISCOUNT'        => $total_diskon_header,

                    'TOTAL_AMOUNT'          => $post['TOTAL_AMOUNT'],
                    'PPN_AMOUNT'            => $post['PPN_AMOUNT'],
                    'TOTAL_NET'             => $post['TOTAL_NET'],
                    'NOTE'                  => $post['keterangan'],
                    'KONSINYASI_FLAG'       => 'Y',
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('invoice', $dataHeader);

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    // Ambil error info SEBELUM rollback
                    $error = $this->db->error();
                    $lastQuery = $this->db->last_query();

                    // Coba dapatkan error message langsung dari mysqli connection
                    $mysqliError = '';
                    if (isset($this->db->conn_id) && is_object($this->db->conn_id)) {
                        if (method_exists($this->db->conn_id, 'error')) {
                            $mysqliError = $this->db->conn_id->error;
                        } elseif (isset($this->db->conn_id->error)) {
                            $mysqliError = $this->db->conn_id->error;
                        }
                    }

                    log_message('error', 'Database Error in po_kny/add: ' . print_r($error, TRUE));
                    log_message('error', 'MySQLi Error: ' . $mysqliError);
                    log_message('error', 'Last Query: ' . $lastQuery);

                    $this->db->trans_rollback();

                    // Gunakan error message dari mysqli jika tersedia
                    $errorMsg = !empty($mysqliError) ? $mysqliError : (!empty($error['message']) ? $error['message'] : 'Unknown database error');
                    $this->session->set_flashdata('warning', "Error DB: " . $errorMsg);
                    redirect('po_kny/add');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('po_kny/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
                }
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            log_message('error', 'Exception in po_kny/add: ' . $err->getMessage());
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('supplier', 'supplier', 'trim|required');
            $this->form_validation->set_rules('location_id', 'location', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('payment_term', 'payment term', 'trim|required');
            $this->form_validation->set_rules('jatuh_tempo', 'jatuh tempo', 'trim|required');
            $this->form_validation->set_rules('storage', 'storage', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->po_kny->get_po_id($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['supplier'] = $this->po_kny->get_supplier();
                    $data['storage'] = $this->po_kny->get_storage();
                    $data['payment_term'] = $this->po_kny->get_payment_term();
                    $data['ppn_code'] = $this->po_kny->get_ppn_code();
                    $data['data'] = $query->row();
                    $data['detail'] = $this->input->post('detail');
                    $this->template->load('template', 'po_kny/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('po_kny');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $invoice_id   = $this->encrypt->decode($post['invoice_id']);
                $detail = $post['detail'] ?? [];
                // debuging($post);

                if (!$invoice_id || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail tidak boleh kosong dan wajib diisi!');
                    redirect('po_kny/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('INVOICE_DETAIL_ID')
                    ->from('invoice_detail')
                    ->where('INVOICE_ID', $invoice_id)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'INVOICE_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['invoice_detail_id'])) {
                    foreach ($detail['invoice_detail_id'] as $val) {
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

                // ===============================
                // INSERT / UPDATE DETAIL
                // ===============================
                foreach ($detail['jumlah'] as $i => $qtyRaw) {

                    if (empty($detail['nama_item'][$i])) continue;

                    if (stripos($post['PPN_CODE'], 'INCL') !== false) {
                        $diskon_price = $detail['diskon_harga'][$i] / (1 + ($detail['diskon_persentase'][$i] / 100));
                    } else {
                        $diskon_price = $detail['diskon_harga'][$i];
                    }

                    $invoice_detail_id = !empty($detail['invoice_detail_id'][$i])
                        ? $this->encrypt->decode($detail['invoice_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'                => $detail['item_id'][$i],
                        'ENTERED_QTY'            => $detail['jumlah'][$i],
                        'BASE_QTY'               => $detail['base_qty'][$i],
                        'UNIT_PRICE'             => str_replace([','], '', $detail['harga'][$i]),
                        'DISCOUNT_PRICE'         => $diskon_price,
                        'SUBTOTAL'               => $detail['subtotal'][$i],
                        'ENTERED_UOM'            => $detail['satuan'][$i],
                        'COA_ID'                 => $detail['coa_suspend_id'][$i],
                        'WAREHOUSE_ID'           => $post['storage'],
                        'INVENTORY_IN_ID'        => $detail['inventory_in_id'][$i],
                        'INVENTORY_IN_DETAIL_ID' => $detail['inventory_in_detail_id'][$i],
                        'DISCOUNT_PERCEN'        => $detail['diskon_persentase'][$i],
                        'HARGA_INPUT'            => str_replace([','], '', $detail['harga_input'][$i]),
                        'DISKON_INPUT'           => $detail['diskon_harga'][$i],
                        'ITEM_DESCRIPTION'       => $detail['nama_item'][$i],
                        'KET'                    => $detail['memo'][$i],
                        'NOTE'                   => $detail['keterangan'][$i],
                        'BERAT'                  => $detail['berat'][$i],
                        'LAST_UPDATE_BY'         => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'       => date('Y-m-d H:i:s'),
                    ];

                    if ($invoice_detail_id) {
                        // UPDATE
                        $this->db->update(
                            'invoice_detail',
                            $dataDetail,
                            ['INVOICE_DETAIL_ID' => $invoice_detail_id, 'INVOICE_ID' => $invoice_id]
                        );
                        // Cek error setelah update
                        $updError = $this->db->error();
                        if ($updError['code'] != 0) {
                            log_message('error', 'Error after UPDATE detail: ' . print_r($updError, TRUE));
                        }
                    } else {
                        // INSERT
                        $dataDetail['INVOICE_ID']       = $invoice_id;
                        $dataDetail['CREATED_BY']       = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE']     = date('Y-m-d H:i:s');

                        $this->db->insert('invoice_detail', $dataDetail);
                        // Cek error setelah insert
                        $insError = $this->db->error();
                        if ($insError['code'] != 0) {
                            log_message('error', 'Error after INSERT detail: ' . print_r($insError, TRUE));
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('INVOICE_ID', $invoice_id)
                        ->where_in('INVOICE_DETAIL_ID', $deleteIds)
                        ->delete('invoice_detail');

                    // Cek error setelah delete
                    $deleteError = $this->db->error();
                    if ($deleteError['code'] != 0) {
                        log_message('error', 'Error after DELETE: ' . print_r($deleteError, TRUE));
                    }
                }

                if (stripos($post['PPN_CODE'], 'INCL') !== false) {
                    $total_diskon_header = $post['TOTAL_DISKON_INPUT'] / (1 + ($post['PPN_PERCEN'] / 100));
                } else {
                    $total_diskon_header = $post['TOTAL_DISKON_INPUT'];
                }

                $invoice_type_id = $this->db->query("SELECT FN_GET_VAR_VALUE('PO') as po;")->row();
                if (!$invoice_type_id) {
                    log_message('error', 'Failed to get invoice type ID');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('invoice', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_reff'],
                    'PERSON_ID'             => $post['supplier'],
                    'PERSON_SITE_ID'        => $post['person_site_id'],
                    'PAYMENT_TERM_ID'       => $post['payment_term'],
                    'WAREHOUSE_ID'          => $post['storage'],
                    'JTEMPO'                => date('Y-m-d H:i:s', strtotime($post['jatuh_tempo'])),
                    'PPN_CODE'              => $post['PPN_CODE'],
                    'PPN_PERCEN'            => $post['PPN_PERCEN'],
                    'INVOICE_TYPE_ID'       => $invoice_type_id->po,
                    'TOTAL_DISCOUNT_PERCEN' => $post['TOTAL_DISCOUNT_PERCEN'],
                    'TOTAL_DISKON_INPUT'    => $post['TOTAL_DISKON_INPUT_HIDDEN'],

                    'TOTAL_DISCOUNT'        => $total_diskon_header,

                    'TOTAL_AMOUNT'          => $post['TOTAL_AMOUNT'],
                    'PPN_AMOUNT'            => $post['PPN_AMOUNT'],
                    'TOTAL_NET'             => $post['TOTAL_NET'],
                    'NOTE'                  => $post['keterangan'],
                    'KONSINYASI_FLAG'       => 'Y',
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['INVOICE_ID' => $invoice_id]);

                // Cek error setelah update header
                $headerError = $this->db->error();
                if ($headerError['code'] != 0) {
                    log_message('error', 'Error after UPDATE header: ' . print_r($headerError, TRUE));
                }

                // ===============================
                // COMMIT / ROLLBACK
                // ===============================
                if ($this->db->trans_status() === FALSE) {
                    // Ambil error info SEBELUM rollback
                    $error = $this->db->error();
                    $lastQuery = $this->db->last_query();

                    // Coba dapatkan error message langsung dari mysqli connection
                    $mysqliError = '';
                    if (isset($this->db->conn_id) && is_object($this->db->conn_id)) {
                        if (method_exists($this->db->conn_id, 'error')) {
                            $mysqliError = $this->db->conn_id->error;
                        } elseif (isset($this->db->conn_id->error)) {
                            $mysqliError = $this->db->conn_id->error;
                        }
                    }

                    log_message('error', 'Database Error in po_kny/update: ' . print_r($error, TRUE));
                    log_message('error', 'MySQLi Error: ' . $mysqliError);
                    log_message('error', 'Last Query: ' . $lastQuery);

                    $this->db->trans_rollback();

                    // Gunakan error message dari mysqli jika tersedia
                    $errorMsg = !empty($mysqliError) ? $mysqliError : (!empty($error['message']) ? $error['message'] : 'Unknown database error');
                    $this->session->set_flashdata('warning', "Error DB: " . $errorMsg);
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Data berhasil diperbarui!');
                }
                redirect('po_kny/detail/' . $id);
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            log_message('error', 'Exception in po_kny/update: ' . $err->getMessage());
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

        $delResult = $this->po_kny->delete($id);
        if ($delResult !== true) {
            $this->db->trans_rollback();
            $this->_jsonError($delResult);
            return;
        }

        $updResult = $this->po_kny->updateStatus($id, $status);
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

    public function print($id)
    {
        $id     = (int) $this->encrypt->decode(base64url_decode($id));
        $po    = $this->po_kny->get_po_detail($id)->row();
        if ($po) {
            $this->load->library('pdf');
            $data = [
                'dir_view' => 'po_kny/pdf',
                'data' => [
                    'po' => $po,
                    'po_detail' => $this->po_kny->get_detail_by_po_id($id)->result()
                ],
                'title' => str_replace('/', ' ', $po->DOCUMENT_NO),
            ];
            $html = $this->load->view('template_pdf', $data, true);
            $this->pdf->generate($html, str_replace('/', ' ', $po->DOCUMENT_NO), 'A4', 'portrait');
        }
    }
}
