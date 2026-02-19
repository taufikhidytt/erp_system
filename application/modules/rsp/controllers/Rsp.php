<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rsp extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Rsp_model', 'rsp');
    }

    public function index()
    {
        try {
            $data['title'] = 'RSP';
            $data['breadcrumb'] = 'RSP';
            $this->template->load('template', 'rsp/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->rsp->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $rsp) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $rsp->STATUS ? $rsp->STATUS : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('rsp/detail/' . base64url_encode($this->encrypt->encode($rsp->TAG_PINJAM_ID))) . '">
                ' . ($rsp->No_Transaksi ? $rsp->No_Transaksi : '-') . '
            </a>';
            $row['no_referensi'] = $rsp->No_Referensi ? $rsp->No_Referensi : '-';
            $row['tanggal'] = $rsp->Tanggal ? date('Y-m-d H:i', strtotime($rsp->Tanggal)) : '-';
            $row['main_storage'] = $rsp->Main_Storage ? $rsp->Main_Storage : '-';

            $row['tag_pinjam_id'] = $this->encrypt->encode($rsp->TAG_PINJAM_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->rsp->count_all(),
            "recordsFiltered" => $this->rsp->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $tag_pinjam_id = $this->encrypt->decode($this->input->post('tag_pinjam_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->rsp->count_detail_by_tag_pinjam_id($tag_pinjam_id);

            $list = $this->rsp->get_detail_by_tag_pinjam_id($tag_pinjam_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"            => $no++,
                    "nama_item"     => $d->Nama_Item,
                    "kode_item"     => $d->Kode_Item,
                    "jumlah"        => number_format((float)$d->Qty, 2, '.', ''),
                    "satuan"        => $d->UoM,
                    "no_reff_1"     => $d->No_Reff_1,
                    "no_reff_2"     => $d->No_Reff_2,
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

    public function getRsp()
    {
        try {
            $supplier = $this->input->post('supplier');
            $main_storage = $this->input->post('main_storage');

            $data = $this->db->query("SELECT
                tmp.*
            FROM
                (SELECT
                    b.PO_DETAIL_ID,
                    NULL TAG_DETAIL_ID,
                    a.DOCUMENT_TYPE_ID,
                    a.STATUS_ID,
                    FN_GET_VAR_NAME (a.STATUS_ID) STATUS_NAME,
                    pr.DOCUMENT_NO No_Reff_1,
                    a.DOCUMENT_NO No_Reff_2,
                    a.DOCUMENT_REFF_NO,
                    psn.PERSON_ID,
                    w.WAREHOUSE_ID,
                    w.WAREHOUSE_NAME,
                    i.ITEM_ID,
                    i.ITEM_CODE,
                    i.ITEM_DESCRIPTION,
                    b.ENTERED_QTY,
                    b.BASE_QTY,
                    b.ENTERED_QTY - (
                        b.RECEIVED_ENTERED_QTY / b.BASE_QTY
                    ) AS BALANCE,
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
                    JOIN pr_detail pd
                        ON b.PR_DETAIL_ID = pd.PR_DETAIL_ID
                    JOIN pr pr
                        ON pd.PR_ID = pr.PR_ID
                    JOIN item i
                        ON b.ITEM_ID = i.ITEM_ID
                    JOIN person psn
                        ON i.PERSON_ID = psn.PERSON_ID
                    JOIN warehouse w
                        ON b.GUDANG_ID = w.WAREHOUSE_ID
                WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
                    AND (
                        b.RECEIVED_ENTERED_QTY * b.RECEIVED_BASE_QTY
                    ) < (b.ENTERED_QTY * b.BASE_QTY)
                    AND a.STATUS_ID IN (
                        FN_GET_VAR_VALUE ('NEW'),
                        FN_GET_VAR_VALUE ('PARTIAL')
                    )
                    AND a.DOCUMENT_TYPE_ID = 3
                    AND b.GUDANG_ID = '{$main_storage}'
                    AND psn.PERSON_ID = '{$supplier}'
                GROUP BY b.PO_DETAIL_ID
                UNION
                ALL
                SELECT
                    NULL,
                    b.TAG_DETAIL_ID,
                    a.DOCUMENT_TYPE_ID,
                    a.STATUS_ID,
                    FN_GET_VAR_NAME (a.STATUS_ID) STATUS_NAME,
                    pr.DOCUMENT_NO No_Reff_1,
                    a.DOCUMENT_NO No_Reff_2,
                    a.DOCUMENT_REFF_NO,
                    psn.PERSON_ID,
                    w.WAREHOUSE_ID,
                    w.WAREHOUSE_NAME,
                    i.ITEM_ID,
                    i.ITEM_CODE,
                    i.ITEM_DESCRIPTION,
                    b.ENTERED_QTY,
                    b.BASE_QTY,
                    b.ENTERED_QTY - (
                        b.DELIVERED_ENTERED_QTY / b.BASE_QTY
                    ) AS BALANCE,
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
                    JOIN po_detail pod
                        ON b.PO_DETAIL_ID = pod.PO_DETAIL_ID
                    JOIN po po
                        ON pod.PO_ID = po.PO_ID
                    JOIN pr_detail pd
                        ON pod.PR_DETAIL_ID = pd.PR_DETAIL_ID
                    JOIN pr pr
                        ON pd.PR_ID = pr.PR_ID
                    JOIN item i
                        ON b.ITEM_ID = i.ITEM_ID
                    JOIN person psn
                        ON i.PERSON_ID = psn.PERSON_ID
                    JOIN warehouse w
                        ON b.TO_WH_ID = w.WAREHOUSE_ID
                WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
                    AND (
                        b.DELIVERED_ENTERED_QTY * b.DELIVERED_BASE_QTY
                    ) < (b.ENTERED_QTY * b.BASE_QTY)
                    AND a.STATUS_ID IN (
                        FN_GET_VAR_VALUE ('NEW'),
                        FN_GET_VAR_VALUE ('PARTIAL')
                    )
                    AND a.DOCUMENT_TYPE_ID = 5
                    AND b.TO_WH_ID = '{$main_storage}'
                    AND psn.PERSON_ID = '{$supplier}'
                GROUP BY b.TAG_DETAIL_ID) AS tmp
            ORDER BY tmp.PO_DETAIL_ID
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

    // public function getRsp()
    // {
    //     try {
    //         $supplier = $this->input->post('supplier');
    //         $main_storage = $this->input->post('main_storage');

    //         $data = $this->db->query("SELECT tmp.*
    //         FROM
    //             (SELECT
    //                 b.PO_DETAIL_ID,
    //                 NULL TAG_DETAIL_ID,
    //                 a.DOCUMENT_TYPE_ID,
    //                 a.STATUS_ID,
    //                 FN_GET_VAR_NAME (a.STATUS_ID) STATUS_NAME,
    //                 pr.DOCUMENT_NO FPK_NO,
    //                 a.DOCUMENT_NO GRK_NO,
    //                 tk.DOCUMENT_NO STS_NO,
    //                 pr.DOCUMENT_DATE FPK_DATE,
    //                 a.DOCUMENT_DATE GRK_DATE,
    //                 tk.DOCUMENT_DATE STS_DATE,
    //                 a.DOCUMENT_REFF_NO,
    //                 a.PERSON_ID,
    //                 b.GUDANG_ID,
    //                 w.WAREHOUSE_NAME,
    //                 b.ITEM_ID,
    //                 i.ITEM_CODE,
    //                 i.ITEM_DESCRIPTION,
    //                 b.ENTERED_QTY,
    //                 b.BASE_QTY,
    //                 b.ENTERED_QTY - (
    //                     b.RECEIVED_ENTERED_QTY / b.BASE_QTY
    //                 ) AS BALANCE,
    //                 b.ENTERED_UOM,
    //                 b.UNIT_PRICE,
    //                 b.SUBTOTAL,
    //                 b.HARGA_INPUT,
    //                 i.BERAT,
    //                 b.NOTE
    //             FROM
    //                 po a
    //                 JOIN po_detail b
    //                     ON a.PO_ID = b.PO_ID
    //                 JOIN pr_detail pd
    //                     ON b.PR_DETAIL_ID = pd.PR_DETAIL_ID
    //                 JOIN pr pr
    //                     ON pd.PR_ID = pr.PR_ID
    //                 JOIN item i
    //                     ON b.ITEM_ID = i.ITEM_ID
    //                 JOIN person psn
    //                     ON i.PERSON_ID = psn.PERSON_ID
    //                 JOIN warehouse w
    //                     ON b.GUDANG_ID = w.WAREHOUSE_ID
    //                 LEFT JOIN tag_konsi_detail tkd
    //                     ON b.PO_DETAIL_ID = tkd.PO_DETAIL_ID
    //                 LEFT JOIN tag_konsi tk
    //                     ON tkd.TAG_KONSI_ID = tk.TAG_KONSI_ID
    //             WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
    //                 AND (
    //                     b.RECEIVED_ENTERED_QTY * b.RECEIVED_BASE_QTY
    //                 ) < (b.ENTERED_QTY * b.BASE_QTY)
    //                 AND a.STATUS_ID IN (
    //                     FN_GET_VAR_VALUE ('NEW'),
    //                     FN_GET_VAR_VALUE ('PARTIAL')
    //                 )
    //                 AND a.DOCUMENT_TYPE_ID = 3
    //                 AND b.GUDANG_ID = '{$main_storage}'
    //                 AND psn.PERSON_ID = '{$supplier}'
    //             GROUP BY b.PO_DETAIL_ID
    //             UNION
    //             ALL
    //             SELECT
    //                 NULL,
    //                 b.TAG_DETAIL_ID,
    //                 a.DOCUMENT_TYPE_ID,
    //                 a.STATUS_ID,
    //                 FN_GET_VAR_NAME (a.STATUS_ID) STATUS_NAME,
    //                 pr.DOCUMENT_NO FPK_NO,
    //                 po.DOCUMENT_NO PO_NO,
    //                 tk.DOCUMENT_NO STS_NO,
    //                 pr.DOCUMENT_DATE FPK_DATE,
    //                 po.DOCUMENT_DATE PO_DATE,
    //                 tk.DOCUMENT_DATE STS_DATE,
    //                 a.DOCUMENT_REFF_NO,
    //                 a.PERSON_ID,
    //                 b.TO_WH_ID,
    //                 w.WAREHOUSE_NAME,
    //                 b.ITEM_ID,
    //                 i.ITEM_CODE,
    //                 i.ITEM_DESCRIPTION,
    //                 b.ENTERED_QTY,
    //                 b.BASE_QTY,
    //                 b.ENTERED_QTY - (
    //                     b.DELIVERED_ENTERED_QTY / b.BASE_QTY
    //                 ) AS BALANCE,
    //                 b.ENTERED_UOM,
    //                 b.UNIT_PRICE,
    //                 b.SUBTOTAL,
    //                 b.HARGA_INPUT,
    //                 i.BERAT,
    //                 b.NOTE
    //             FROM
    //                 tag a
    //                 JOIN tag_detail b
    //                     ON a.TAG_ID = b.TAG_ID
    //                 JOIN po_detail pod
    //                     ON b.PO_DETAIL_ID = pod.PO_DETAIL_ID
    //                 JOIN po po
    //                     ON pod.PO_ID = po.PO_ID
    //                 JOIN pr_detail pd
    //                     ON pod.PR_DETAIL_ID = pd.PR_DETAIL_ID
    //                 JOIN pr pr
    //                     ON pd.PR_ID = pr.PR_ID
    //                 JOIN item i
    //                     ON b.ITEM_ID = i.ITEM_ID
    //                 JOIN person psn
    //                     ON i.PERSON_ID = psn.PERSON_ID
    //                 JOIN warehouse w
    //                     ON b.TO_WH_ID = w.WAREHOUSE_ID
    //                 JOIN tag_konsi_detail tkd
    //                     ON b.PO_DETAIL_ID = tkd.PO_DETAIL_ID
    //                 JOIN tag_konsi tk
    //                     ON tkd.TAG_KONSI_ID = tk.TAG_KONSI_ID
    //             WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
    //                 AND (
    //                     b.DELIVERED_ENTERED_QTY * b.DELIVERED_BASE_QTY
    //                 ) < (b.ENTERED_QTY * b.BASE_QTY)
    //                 AND a.STATUS_ID IN (
    //                     FN_GET_VAR_VALUE ('NEW'),
    //                     FN_GET_VAR_VALUE ('PARTIAL')
    //                 )
    //                 AND a.DOCUMENT_TYPE_ID = 5
    //                 AND b.TO_WH_ID = '{$main_storage}'
    //                 AND psn.PERSON_ID = '{$supplier}'
    //             GROUP BY b.TAG_DETAIL_ID) AS tmp
    //             ORDER BY tmp.PO_DETAIL_ID;
    //         ");

    //         if ($data->num_rows() > 0) {
    //             $result = array(
    //                 'status' => 'success',
    //                 'data' => $data->result_array(),
    //             );
    //         } else {
    //             $result = array(
    //                 'status' => 'not found',
    //                 'data' => [],
    //             );
    //         }
    //         echo json_encode($result);
    //     } catch (Exception $err) {
    //         return sendError('Server Error', $err->getMessage());
    //     }
    // }

    public function getStatus()
    {
        $tag_pinjam_id = $this->encrypt->decode($this->input->post('tag_pinjam_id'));
        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME FROM tag_pinjam a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.TAG_PINJAM_ID = {$tag_pinjam_id}");
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
            $this->form_validation->set_rules('main_storage', 'main storage', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah RSP';
                $data['breadcrumb'] = 'Tambah RSP';
                $data['main_storage'] = $this->rsp->get_main_storage();
                $data['supplier'] = $this->rsp->get_supplier();
                $this->template->load('template', 'rsp/add', $data);
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail wajib diisi!');
                    redirect('rsp/add');
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
                        redirect('rsp/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('rsp/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('rsp/add');
                    }

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);
                    $harga_input = str_replace([','], '', $detail['harga_input'][$i]);
                    $subtotal = $jumlah * $harga_input;
                    $total += $subtotal;

                    $dataDetail = [
                        'TAG_PINJAM_ID'         => $post['seq'],
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'              => $subtotal,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'WAREHOUSE_ID'          => $post['main_storage'],
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

                    $this->db->insert('tag_pinjam_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('rsp');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'TAG_PINJAM_ID'         => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'WAREHOUSE_ID'          => $post['main_storage'],
                    'PERSON_ID'             => $post['supplier'],
                    'KONSINYASI_FLAG'       => 'Y',
                    'PPN_CODE'              => "NO PPN",
                    'PPH_CODE'              => "NO PPH",
                    'NOTE'                  => $post['keterangan'],
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('tag_pinjam', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('rsp');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('rsp');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('rsp/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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

            $this->form_validation->set_rules('supplier', 'supplier', 'trim|required');
            $this->form_validation->set_rules('main_storage', 'main storage', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->rsp->get_tag_pinjam_id($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['main_storage'] = $this->rsp->get_main_storage();
                    $data['supplier'] = $this->rsp->get_supplier();
                    $data['data'] = $query->row();
                    $this->template->load('template', 'rsp/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('rsp');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $tag_pinjam_id   = $this->encrypt->decode($post['tag_pinjam_id']);
                $detail = $post['detail'] ?? [];

                if (!$tag_pinjam_id || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail tidak boleh kosong dan wajib diisi!');
                    redirect('rsp/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('TAG_PINJAM_DETAIL_ID')
                    ->from('tag_pinjam_detail')
                    ->where('TAG_PINJAM_ID', $tag_pinjam_id)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'TAG_PINJAM_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['tag_pinjam_detail_id'])) {
                    foreach ($detail['tag_pinjam_detail_id'] as $val) {
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

                    $tag_pinjam_detail_id = !empty($detail['tag_pinjam_detail_id'][$i])
                        ? $this->encrypt->decode($detail['tag_pinjam_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'              => $subtotal,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'WAREHOUSE_ID'          => $post['main_storage'],
                        'TAG_DETAIL_ID'         => $detail['tag_detail_id'][$i],
                        'PO_DETAIL_ID'          => $detail['po_detail_id'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    if ($tag_pinjam_detail_id) {
                        // UPDATE
                        $this->db->update(
                            'tag_pinjam_detail',
                            $dataDetail,
                            ['TAG_PINJAM_DETAIL_ID' => $tag_pinjam_detail_id, 'TAG_PINJAM_ID' => $tag_pinjam_id]
                        );
                    } else {
                        // INSERT
                        $dataDetail['TAG_PINJAM_ID']    = $tag_pinjam_id;
                        $dataDetail['CREATED_BY']       = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE']     = date('Y-m-d H:i:s');

                        $this->db->insert('tag_pinjam_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('rsp/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('TAG_PINJAM_ID', $tag_pinjam_id)
                        ->where_in('TAG_PINJAM_DETAIL_ID', $deleteIds)
                        ->delete('tag_pinjam_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('tag_pinjam', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'WAREHOUSE_ID'          => $post['main_storage'],
                    'PERSON_ID'             => $post['supplier'],
                    'NOTE'                  => $post['keterangan'],
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['TAG_PINJAM_ID' => $tag_pinjam_id]);

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
                redirect('rsp/detail/' . $id);
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

        $delResult = $this->rsp->delete($id);
        if ($delResult !== true) {
            $this->db->trans_rollback();
            $this->_jsonError($delResult);
            return;
        }

        $updResult = $this->rsp->updateStatus($id, $status);
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
