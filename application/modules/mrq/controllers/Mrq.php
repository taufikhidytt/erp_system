<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mrq extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Mrq_model', 'mrq');
    }

    public function index()
    {
        try {
            $data['title'] = 'MRQ';
            $data['breadcrumb'] = 'MRQ';
            $this->template->load('template', 'mrq/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->mrq->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $mrq) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $mrq->Status ? $mrq->Status : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('mrq/detail/' . base64url_encode($this->encrypt->encode($mrq->BUILD_ID))) . '">
                ' . ($mrq->No_Transaksi ? $mrq->No_Transaksi : '-') . '
            </a>';
            $row['no_referensi'] = $mrq->No_Referensi ? $mrq->No_Referensi : '-';
            $row['tanggal'] = $mrq->Tanggal ? date('Y-m-d H:i', strtotime($mrq->Tanggal)) : '-';
            $row['storage'] = $mrq->Storage ? $mrq->Storage : '-';
            $row['customer'] = $mrq->Customer ? $mrq->Customer : '-';
            $row['unit'] = $mrq->Unit ? $mrq->Unit : '-';
            $row['nama_item'] = $mrq->Nama_Item ? $mrq->Nama_Item : '-';
            $row['satuan'] = $mrq->UoM ? $mrq->UoM : '-';

            $row['build_id'] = $this->encrypt->encode($mrq->BUILD_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->mrq->count_all(),
            "recordsFiltered" => $this->mrq->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $build_id = $this->encrypt->decode($this->input->post('build_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->mrq->count_detail_by_build_id($build_id);

            $list = $this->mrq->get_detail_by_build_id($build_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"            => $no++,
                    "nama_item"     => $d->Nama_Item,
                    "kode_item"     => $d->Kode_Item,
                    "jumlah"        => number_format((float)$d->Qty, 2, '.', ''),
                    "satuan"        => $d->UoM,
                    "no_reff_trx"   => $d->Reff_Trx,
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

    public function get_location_by_shipto()
    {
        try {
            $ship_to = $this->input->post("ship_to");
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
                a.PERSON_SITE_ID = '{$ship_to}' 
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

    public function get_item_uom()
    {
        try {
            $item_id = $this->input->post('item_id');
            $data = $this->db->query("SELECT
                *
            FROM (
                -- Unit dasar (default)
                SELECT
                    0 AS URUT,
                    i.ITEM_ID,
                    i.UOM_CODE,
                    1 AS TO_QTY,
                    CONCAT('1.00 ', i.UOM_CODE) AS KONVERSI
                FROM item i
                WHERE i.ITEM_ID = {$item_id}
                UNION ALL
                -- Unit alternatif (konversi)
                SELECT
                    1 AS URUT,
                    iu.ITEM_ID,
                    iu.UOM_CODE,
                    COALESCE(iu.TO_QTY, 1) AS TO_QTY,
                    CONCAT(
                        ROUND(COALESCE(iu.TO_QTY, 1) * 1, 2),
                        ' ',
                        i.UOM_CODE
                    ) AS KONVERSI
                FROM item_uom iu
                INNER JOIN item i 
                    ON iu.ITEM_ID = i.ITEM_ID
                WHERE iu.ITEM_ID = {$item_id}
            ) AS unit_data
            ORDER BY URUT, TO_QTY");

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

            $data = $this->db->query("SELECT
                PO_DETAIL_ID,
                TAG_DETAIL_ID,
                DOCUMENT_TYPE_ID,
                STATUS_ID,
                FN_GET_VAR_NAME (STATUS_ID) STATUS_NAME,
                DOCUMENT_DATE,
                DOCUMENT_NO,
                DOCUMENT_REFF_NO,
                PERSON_ID,
                WAREHOUSE_ID,
                WAREHOUSE_NAME,
                ITEM_ID,
                ITEM_CODE,
                ITEM_DESCRIPTION,
                ENTERED_QTY,
                BASE_QTY,
                CASE
                    WHEN BASE_QTY = 0
                    THEN ENTERED_QTY
                    ELSE ENTERED_QTY - (
                        RECEIVED_ENTERED_QTY / NULLIF(BASE_QTY, 0)
                    )
                END AS BALANCE,
                ENTERED_UOM,
                UNIT_PRICE,
                SUBTOTAL,
                HARGA_INPUT,
                BERAT,
                NOTE
            FROM
                (-- Query untuk PO
                SELECT
                    b.PO_DETAIL_ID,
                    NULL AS TAG_DETAIL_ID,
                    a.DOCUMENT_TYPE_ID,
                    a.STATUS_ID,
                    a.DOCUMENT_DATE,
                    a.DOCUMENT_NO,
                    a.DOCUMENT_REFF_NO,
                    p.PERSON_ID,
                    w.WAREHOUSE_ID,
                    w.WAREHOUSE_NAME,
                    i.ITEM_ID,
                    i.ITEM_CODE,
                    i.ITEM_DESCRIPTION,
                    b.ENTERED_QTY,
                    b.BASE_QTY,
                    b.RECEIVED_ENTERED_QTY,
                    b.ENTERED_UOM,
                    b.UNIT_PRICE,
                    b.SUBTOTAL,
                    b.HARGA_INPUT,
                    i.BERAT,
                    b.NOTE
                FROM
                    po a
                    JOIN po_detail b
                        ON a.PO_ID = b.PO_ID
                    JOIN item i
                        ON b.ITEM_ID = i.ITEM_ID
                    JOIN warehouse w
                        ON b.GUDANG_ID = w.WAREHOUSE_ID
                    JOIN person p
                        ON i.PERSON_ID = p.PERSON_ID
                WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
                    AND (
                        b.RECEIVED_ENTERED_QTY * b.RECEIVED_BASE_QTY
                    ) < (b.ENTERED_QTY * b.BASE_QTY)
                    AND a.STATUS_ID IN (
                        FN_GET_VAR_VALUE ('NEW'),
                        FN_GET_VAR_VALUE ('PARTIAL')
                    )
                    AND a.DOCUMENT_TYPE_ID = 3
                    AND a.WAREHOUSE_ID = '{$storage}'
                UNION
                ALL
                SELECT
                    NULL AS PO_DETAIL_ID,
                    b.TAG_DETAIL_ID,
                    a.DOCUMENT_TYPE_ID,
                    a.STATUS_ID,
                    a.DOCUMENT_DATE,
                    a.DOCUMENT_NO,
                    a.DOCUMENT_REFF_NO,
                    p.PERSON_ID,
                    w.WAREHOUSE_ID,
                    w.WAREHOUSE_NAME,
                    i.ITEM_ID,
                    i.ITEM_CODE,
                    i.ITEM_DESCRIPTION,
                    b.ENTERED_QTY,
                    b.BASE_QTY,
                    b.DELIVERED_ENTERED_QTY AS RECEIVED_ENTERED_QTY,
                    b.ENTERED_UOM,
                    b.UNIT_PRICE,
                    b.SUBTOTAL,
                    b.HARGA_INPUT,
                    i.BERAT,
                    b.NOTE
                FROM
                    tag a
                    JOIN tag_detail b
                        ON a.TAG_ID = b.TAG_ID
                    JOIN item i
                        ON b.ITEM_ID = i.ITEM_ID
                    JOIN warehouse w
                        ON b.TO_WH_ID = w.WAREHOUSE_ID
                    JOIN person p
                        ON i.PERSON_ID = p.PERSON_ID
                WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
                    AND (
                        b.DELIVERED_ENTERED_QTY * b.DELIVERED_BASE_QTY
                    ) < (b.ENTERED_QTY * b.BASE_QTY)
                    AND a.STATUS_ID IN (
                        FN_GET_VAR_VALUE ('NEW'),
                        FN_GET_VAR_VALUE ('PARTIAL')
                    )
                    AND a.DOCUMENT_TYPE_ID IN (3, 5)
                    AND b.TO_WH_ID = '{$storage}'
                    ) tmp
            ORDER BY PO_DETAIL_ID;
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

    public function getStatus()
    {
        $build_id = $this->encrypt->decode($this->input->post('build_id'));

        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME FROM build a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.BUILD_ID = {$build_id}");

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

            $this->form_validation->set_rules('ship_to', 'ship to', 'trim|required');
            $this->form_validation->set_rules('location', 'location', 'trim|required');
            $this->form_validation->set_rules('storage', 'storage', 'trim|required');
            $this->form_validation->set_rules('item_finish_goods', 'item finish good', 'trim|required');
            $this->form_validation->set_rules('jumlah', 'jumlah', 'trim|required');
            $this->form_validation->set_rules('satuan', 'satuan', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('ship_date', 'ship date', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah MRQ';
                $data['breadcrumb'] = 'Tambah MRQ';
                $data['ship_to'] = $this->mrq->get_ship_to();
                $data['storage'] = $this->mrq->get_storage();
                $data['item_finish_goods'] = $this->mrq->get_item_finish_goods();
                $this->template->load('template', 'mrq/add', $data);
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail wajib diisi!');
                    redirect('mrq/add');
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
                        redirect('mrq/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('mrq/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('mrq/add');
                    }

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);
                    $harga_input = str_replace([','], '', $detail['harga_input'][$i]);
                    $subtotal = $jumlah * $harga_input;
                    $total += $subtotal;

                    $dataDetail = [
                        'BUILD_ID'              => $post['seq'],
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'              => $subtotal,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'WAREHOUSE_ID'          => $detail['warehouse_id'][$i],
                        'TAG_DETAIL_ID'         => $detail['tag_detail_id'][$i],
                        'PO_DETAIL_ID'          => $detail['po_detail_id'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'CREATED_BY'            => $this->session->userdata('id'),
                        'CREATED_DATE'          => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('build_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('mrq');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'BUILD_ID'              => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'SHIP_DATE'             => $post['ship_date'],
                    'DOCUMENT_REFF_NO'      => $post['reff_cust'],
                    'WAREHOUSE_ID'          => $post['storage'],
                    'PERSON_ID'             => $post['ship_to'],
                    'PERSON_SITE_ID'        => $post['location'],
                    'ITEM_ID'               => $post['item_finish_goods'],
                    'ENTERED_QTY'           => $post['jumlah'],
                    'BASE_QTY'              => $post['base_qty'],
                    'ITEM_DESCRIPTION'      => $post['item_description'],
                    'ENTERED_UOM'           => $post['satuan'],
                    'KONSINYASI_FLAG'       => 'Y',
                    'NOTE'                  => $post['keterangan'],
                    'REFF_PR'               => $post['reff_pr'],
                    'HOUR_MINUTES'          => $post['hour_minutes'],
                    'UNIT'                  => $post['unit'],
                    'LOKASI'                => $post['code'],
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('build', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('mrq');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('mrq');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('mrq/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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

            $this->form_validation->set_rules('ship_to', 'ship to', 'trim|required');
            $this->form_validation->set_rules('location', 'location', 'trim|required');
            $this->form_validation->set_rules('storage', 'storage', 'trim|required');
            $this->form_validation->set_rules('item_finish_goods', 'item finish good', 'trim|required');
            $this->form_validation->set_rules('jumlah', 'jumlah', 'trim|required');
            $this->form_validation->set_rules('satuan', 'satuan', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('ship_date', 'ship date', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->mrq->get_build_id($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['ship_to'] = $this->mrq->get_ship_to();
                    $data['storage'] = $this->mrq->get_storage();
                    $data['item_finish_goods'] = $this->mrq->get_item_finish_goods();
                    $data['data'] = $query->row();
                    $this->template->load('template', 'mrq/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('mrq');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $build_id   = $this->encrypt->decode($post['build_id']);
                $detail = $post['detail'] ?? [];

                if (!$build_id || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail tidak boleh kosong dan wajib diisi!');
                    redirect('mrq/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('BUILD_DETAIL_ID')
                    ->from('build_detail')
                    ->where('BUILD_ID', $build_id)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'BUILD_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['build_detail_id'])) {
                    foreach ($detail['build_detail_id'] as $val) {
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

                    $build_detail_id = !empty($detail['build_detail_id'][$i])
                        ? $this->encrypt->decode($detail['build_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'              => $subtotal,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'WAREHOUSE_ID'          => $detail['warehouse_id'][$i],
                        'TAG_DETAIL_ID'         => $detail['tag_detail_id'][$i],
                        'PO_DETAIL_ID'          => $detail['po_detail_id'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    if ($build_detail_id) {
                        // UPDATE
                        $this->db->update(
                            'build_detail',
                            $dataDetail,
                            ['BUILD_DETAIL_ID' => $build_detail_id, 'BUILD_ID' => $build_id]
                        );
                    } else {
                        // INSERT
                        $dataDetail['BUILD_ID']         = $build_id;
                        $dataDetail['CREATED_BY']       = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE']     = date('Y-m-d H:i:s');

                        $this->db->insert('build_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('mrq/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('BUILD_ID', $build_id)
                        ->where_in('BUILD_DETAIL_ID', $deleteIds)
                        ->delete('build_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('build', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'SHIP_DATE'             => $post['ship_date'],
                    'DOCUMENT_REFF_NO'      => $post['reff_cust'],
                    'WAREHOUSE_ID'          => $post['storage'],
                    'PERSON_ID'             => $post['ship_to'],
                    'PERSON_SITE_ID'        => $post['location'],
                    'ITEM_ID'               => $post['item_finish_goods'],
                    'ENTERED_QTY'           => $post['jumlah'],
                    'BASE_QTY'              => $post['base_qty'],
                    'ITEM_DESCRIPTION'      => $post['item_description'],
                    'ENTERED_UOM'           => $post['satuan'],
                    'KONSINYASI_FLAG'       => 'Y',
                    'NOTE'                  => $post['keterangan'],
                    'REFF_PR'               => $post['reff_pr'],
                    'HOUR_MINUTES'          => $post['hour_minutes'],
                    'UNIT'                  => $post['unit'],
                    'LOKASI'                => $post['code'],
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['BUILD_ID' => $build_id]);

                // ===============================
                // COMMIT / ROLLBACK
                // ===============================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $error = $this->db->error();
                    $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Data berhasil diperbarui!');
                }
                redirect('mrq/detail/' . $id);
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

        $delResult = $this->mrq->delete($id);
        if ($delResult !== true) {
            $this->db->trans_rollback();
            $this->_jsonError($delResult);
            return;
        }

        $updResult = $this->mrq->updateStatus($id, $status);
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
}
