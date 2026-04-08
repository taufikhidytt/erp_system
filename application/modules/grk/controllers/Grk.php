<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Grk extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Grk_model', 'grk');
    }
    public function index()
    {
        try {
            $data['title'] = 'GRK';
            $data['breadcrumb'] = 'GRK';
            $this->template->load('template', 'grk/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->grk->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $grk) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $grk->Status ? $grk->Status : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('grk/detail/' . base64url_encode($this->encrypt->encode($grk->PO_ID))) . '">
                ' . ($grk->No_Transaksi ? $grk->No_Transaksi : '-') . '
            </a>';
            $row['no_referensi'] = $grk->No_Referensi ? $grk->No_Referensi : '-';
            $row['tanggal'] = $grk->Tanggal ? date('Y-m-d H:i', strtotime($grk->Tanggal)) : '-';
            $row['supplier'] = $grk->Supplier ? $grk->Supplier : '-';
            $row['gudang'] = $grk->Gudang ? $grk->Gudang : '-';

            $row['po_id'] = $this->encrypt->encode($grk->PO_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->grk->count_all(),
            "recordsFiltered" => $this->grk->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $po_id = $this->encrypt->decode($this->input->post('po_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->grk->count_detail_by_pr_id($po_id);

            $list = $this->grk->get_detail_by_pr_id($po_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"        => $no++,
                    "item"      => $d->Nama_Item,
                    "item_code" => $d->Kode_Item,
                    "qty"       => number_format((float)$d->Qty, 2, '.', ''),
                    "sisa"       => number_format((float)$d->Sisa, 2, '.', ''),
                    "uom"       => $d->UoM,
                    "harga"     => number_format($d->Harga, 2, '.', ','),
                    "subtotal"  => number_format($d->Subtotal, 2, '.', ','),
                    "no_fpk"    => $d->No_FPK,
                    "sales"     => $d->Sales,
                    "note"      => $d->Note,
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

    public function getItemBySupplier()
    {
        try {
            $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

            $supplier = $this->input->post('id_supplier');
            $data = $this->db->query("SELECT
                b.PR_DETAIL_ID,
                b.PR_ID,
                a.DOCUMENT_TYPE_ID,
                a.STATUS_ID,
                FN_GET_VAR_NAME (a.STATUS_ID) STATUS_NAME,
                a.DOCUMENT_DATE,
                a.DOCUMENT_NO,
                a.DOCUMENT_REFF_NO,
                a.PERSON_ID,
                a.WAREHOUSE_ID,
                w.WAREHOUSE_NAME,
                a.KARYAWAN_ID,
                k.FIRST_NAME AS SALES,
                b.ITEM_ID,
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
                i.LEAD_TIME,
                i.BERAT,
                b.NOTE
            FROM
                pr a
                JOIN pr_detail b
                    ON a.PR_ID = b.PR_ID
                JOIN karyawan k
                    ON a.KARYAWAN_ID = k.KARYAWAN_ID
                JOIN item i
                    ON b.ITEM_ID = i.ITEM_ID
                JOIN warehouse w
                    ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            WHERE (b.ENTERED_QTY * b.BASE_QTY) > 0
                AND (
                    b.RECEIVED_ENTERED_QTY * b.RECEIVED_BASE_QTY
                ) < (b.ENTERED_QTY * b.BASE_QTY)
                AND a.STATUS_ID IN (
                    FN_GET_VAR_VALUE ('NEW'),
                    FN_GET_VAR_VALUE ('PARTIAL')
                )
                AND a.DOCUMENT_TYPE_ID = '{$tipe_id['TYPE_ID']}'
                AND a.PERSON_ID = {$supplier}
                -- AND a.DOCUMENT_NO = 'FPK/BKI/260100003'
            ORDER BY a.DOCUMENT_DATE DESC
                -- a.DOCUMENT_NO,
                -- b.PR_DETAIL_ID;
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
        $po_id = $this->encrypt->decode($this->input->post('po_id'));
        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME FROM po a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.PO_ID = {$po_id}");
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
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('gudang', 'gudang', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah GRK';
                $data['breadcrumb'] = 'Tambah GRK';
                $data['supplier'] = $this->grk->getSupplier();
                $data['gudang'] = $this->grk->getGudang();
                $data['detail'] = $this->input->post('detail');
                $this->template->load('template', 'grk/add', $data);
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail item wajib diisi!');
                    redirect('grk/add');
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
                        $this->session->set_flashdata('warning', 'Jumlah item "' . $detail['nama_item'][$i] . '" tidak boleh kosong');
                        redirect('grk/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah item "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('grk/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah item "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('grk/add');
                    }

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);
                    $harga_input = str_replace([','], '', $detail['harga_input'][$i]);
                    $subtotal = $jumlah * $harga_input;
                    $total += $subtotal;

                    $dataDetail = [
                        'PO_ID'             => $post['seq'],
                        'ITEM_ID'           => $detail['item_id'][$i],
                        'ENTERED_QTY'       => $detail['jumlah'][$i],
                        'BASE_QTY'          => $detail['base_qty'][$i],
                        'UNIT_PRICE'        => str_replace([','], '', $detail['harga'][$i]),
                        'SUBTOTAL'          => $subtotal,
                        'ENTERED_UOM'       => $detail['satuan'][$i],
                        'GUDANG_ID'         => $detail['warehouse_id'][$i],
                        'PR_DETAIL_ID'      => $detail['pr_detail_id'][$i],
                        'KARYAWAN_ID'       => $detail['sales'][$i],
                        'ETA_LEADTIME'      => $detail['lead_time'][$i],
                        'HARGA_INPUT'       => $harga_input,
                        'ITEM_DESCRIPTION'  => $detail['nama_item'][$i],
                        'NOTE'              => $detail['keterangan'][$i],
                        'BERAT'             => $detail['berat'][$i],
                        'CREATED_BY'        => $this->session->userdata('id'),
                        'CREATED_DATE'      => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('po_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('grk');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'PO_ID'                 => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'PERSON_ID'             => $post['supplier'],
                    'TOTAL_AMOUNT'          => $post['total'],
                    'WAREHOUSE_ID'          => $post['gudang'],
                    'PPN_CODE'              => "NO PPN",
                    'PPH_CODE'              => "NO PPH",
                    'NOTE'                  => $post['keterangan'],
                    'KONSINYASI'            => 1,
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('po', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('grk');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('grk');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('grk/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('gudang', 'gudang', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->grk->getPoId($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['supplier'] = $this->grk->getSupplier();
                    $data['gudang'] = $this->grk->getGudang();
                    $data['data'] = $query->row();
                    $data['detail'] = $this->input->post('detail');
                    $this->template->load('template', 'grk/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('grk');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $poId   = $this->encrypt->decode($post['po_id']);
                $detail = $post['detail'] ?? [];

                if (!$poId || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail item tidak boleh kosong dan wajib diisi!');
                    redirect('grk/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('PO_DETAIL_ID')
                    ->from('po_detail')
                    ->where('PO_ID', $poId)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'PO_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['po_detail_id'])) {
                    foreach ($detail['po_detail_id'] as $val) {
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

                    $poDetailId = !empty($detail['po_detail_id'][$i])
                        ? $this->encrypt->decode($detail['po_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'           => $detail['item_id'][$i],
                        'ENTERED_QTY'       => $detail['jumlah'][$i],
                        'BASE_QTY'          => $detail['base_qty'][$i],
                        'UNIT_PRICE'        => str_replace([','], '', $detail['harga'][$i]),
                        'SUBTOTAL'          => $subtotal,
                        'ENTERED_UOM'       => $detail['satuan'][$i],
                        'GUDANG_ID'         => $detail['warehouse_id'][$i],
                        'PR_DETAIL_ID'      => $detail['pr_detail_id'][$i],
                        'KARYAWAN_ID'       => $detail['sales'][$i],
                        'ETA_LEADTIME'      => $detail['lead_time'][$i],
                        'HARGA_INPUT'       => $harga_input,
                        'ITEM_DESCRIPTION'  => $detail['nama_item'][$i],
                        'NOTE'              => $detail['keterangan'][$i],
                        'BERAT'             => $detail['berat'][$i],
                        'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                    ];

                    if ($poDetailId) {
                        // UPDATE
                        $this->db->update(
                            'po_detail',
                            $dataDetail,
                            ['PO_DETAIL_ID' => $poDetailId, 'PO_ID' => $poId]
                        );
                    } else {
                        // INSERT
                        $dataDetail['PO_ID']        = $poId;
                        $dataDetail['CREATED_BY']   = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE'] = date('Y-m-d H:i:s');

                        $this->db->insert('po_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('grk/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('PO_ID', $poId)
                        ->where_in('PO_DETAIL_ID', $deleteIds)
                        ->delete('po_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('po', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'PERSON_ID'             => $post['supplier'],
                    'TOTAL_AMOUNT'          => $post['total'],
                    'WAREHOUSE_ID'          => $post['gudang'],
                    'NOTE'                  => $post['keterangan'],
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['PO_ID' => $poId]);

                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                    redirect('grk/detail/' . $id);
                }

                // ===============================
                // COMMIT / ROLLBACK
                // ===============================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal memperbarui GRK!');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'GRK berhasil diperbarui!');
                }
                redirect('grk/detail/' . $id);
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

        $this->db->trans_start();

        $del = $this->grk->delete($id);
        $upd = $this->grk->updateStatus($id, $status);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $result = [
                'status'    =>  false,
                'message'   => 'Gagal menghapus GRK, transaksi dibatalkan!'
            ];
        } else {
            $result = [
                'status'    =>  true,
                'message'   => 'GRK berhasil dihapus!',
            ];
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    public function get_info($id)
    {
        $id = (int) $this->encrypt->decode(base64url_decode($id));
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'po_detail b',
            'select' => [
                'b.PO_DETAIL_ID,i.ITEM_DESCRIPTION Nama_Item,i.ITEM_CODE Kode_Item,b.ENTERED_UOM Satuan,b.ENTERED_QTY GRK,',
                ['(b.RECEIVED_ENTERED_QTY / b.BASE_QTY) AS STS_RSP_MR', FALSE],
                ['b.ENTERED_QTY - (b.RECEIVED_ENTERED_QTY / b.BASE_QTY) AS SISA', FALSE],
            ],
            'joins' => [
                ['item i', 'b.ITEM_ID = i.ITEM_ID', 'inner'],
            ],
            'where' => ['b.PO_ID' => $id],
            'column_search' => ['i.ITEM_DESCRIPTION', 'i.ITEM_CODE','b.ENTERED_UOM', 'b.ENTERED_QTY'],
            'column_order'  => [null,null,'i.ITEM_DESCRIPTION', 'i.ITEM_CODE', 'b.ENTERED_UOM', 'b.ENTERED_QTY', '(b.RECEIVED_ENTERED_QTY / b.BASE_QTY)', '(b.ENTERED_QTY - (b.RECEIVED_ENTERED_QTY / b.BASE_QTY))'],
            'order' => ['i.ITEM_DESCRIPTION' => 'asc'],
        ];

        echo json_encode($this->datatables->generate($params, function($row, $no) {
            return [
                'no' => $no,
                'po_detail_id' => base64url_encode($this->encrypt->encode($row->PO_DETAIL_ID)),
                'nama_item' => $row->Nama_Item,
                'kode_item' => $row->Kode_Item,
                'satuan' => $row->Satuan,
                'grk' => number_format((float)$row->GRK, 2, '.', ','),
                'sts_rsp_mr' => number_format((float)$row->STS_RSP_MR, 2, '.', ','),
                'sisa' => number_format((float)$row->SISA, 2, '.', ','),
            ];
        }));
    }

    public function get_info_detail($detail_id){
        $detail_id = (int) $this->encrypt->decode(base64url_decode($detail_id));
        $this->load->model('M_datatables', 'datatables');

        $query = "
            SELECT
                No_Transaksi,
                Tanggal,
                Jumlah,
                Satuan,
                `S.Loc` as S_Loc,
                HEADER_ID
            FROM (SELECT
                    b.PO_ID,
                    b.PO_DETAIL_ID,
                    b.ITEM_ID,
                    c.DOCUMENT_NO No_Transaksi,
                    c.DOCUMENT_DATE Tanggal,
                    (a.ENTERED_QTY * b.BASE_QTY) Jumlah,
                    b.ENTERED_UOM Satuan,
                    w.WAREHOUSE_NAME `S.Loc`,
                    w.WAREHOUSE_ID,
                    a.TAG_KONSI_ID HEADER_ID,
                    a.TAG_KONSI_DETAIL_ID DETAIL_ID
                FROM
                    po_detail b
                    JOIN tag_konsi_detail a
                        ON b.PO_DETAIL_ID = a.PO_DETAIL_ID
                    JOIN tag_konsi c
                        ON a.TAG_KONSI_ID = c.TAG_KONSI_ID
                    JOIN warehouse w
                        ON c.TO_WH_ID = w.WAREHOUSE_ID
                WHERE b.PO_DETAIL_ID = $detail_id
                UNION
                ALL
                SELECT
                    b.PO_ID,
                    b.PO_DETAIL_ID,
                    b.ITEM_ID,
                    c.DOCUMENT_NO No_Transaksi,
                    c.DOCUMENT_DATE Tanggal,
                    (a.ENTERED_QTY * b.BASE_QTY) Jumlah,
                    b.ENTERED_UOM Satuan,
                    w.WAREHOUSE_NAME `S.Loc`,
                    w.WAREHOUSE_ID,
                    a.TAG_PINJAM_ID HEADER_ID,
                    a.TAG_PINJAM_DETAIL_ID DETAIL_ID
                FROM
                    po_detail b
                    JOIN tag_pinjam_detail a
                        ON b.PO_DETAIL_ID = a.PO_DETAIL_ID
                    JOIN tag_pinjam c
                        ON a.TAG_PINJAM_ID = c.TAG_PINJAM_ID
                    JOIN warehouse w
                        ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
                WHERE b.PO_DETAIL_ID = $detail_id
                UNION
                ALL
                SELECT
                    b.PO_ID,
                    b.PO_DETAIL_ID,
                    b.ITEM_ID,
                    c.DOCUMENT_NO No_Transaksi,
                    c.DOCUMENT_DATE Tanggal,
                    (a.ENTERED_QTY * b.BASE_QTY) Jumlah,
                    b.ENTERED_UOM Satuan,
                    w.WAREHOUSE_NAME `S.Loc`,
                    w.WAREHOUSE_ID,
                    a.BUILD_ID HEADER_ID,
                    a.BUILD_DETAIL_ID DETAIL_ID
                FROM
                    po_detail b
                    JOIN build_detail a
                        ON b.PO_DETAIL_ID = a.PO_DETAIL_ID
                    JOIN build c
                        ON a.BUILD_ID = c.BUILD_ID
                    JOIN warehouse w
                        ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
                WHERE b.PO_DETAIL_ID = $detail_id
                UNION
                ALL
                SELECT
                    b.PO_ID,
                    b.PO_DETAIL_ID,
                    b.ITEM_ID,
                    c.DOCUMENT_NO No_Transaksi,
                    c.DOCUMENT_DATE Tanggal,
                    (a.ENTERED_QTY * b.BASE_QTY) Jumlah,
                    b.ENTERED_UOM Satuan,
                    w.WAREHOUSE_NAME `S.Loc`,
                    w.WAREHOUSE_ID,
                    a.INVENTORY_IN_ID HEADER_ID,
                    a.INVENTORY_IN_DETAIL_ID DETAIL_ID
                FROM
                    po_detail b
                    JOIN inventory_in_detail a
                        ON b.PO_DETAIL_ID = a.PO_DETAIL_ID
                    JOIN inventory_in c
                        ON a.INVENTORY_IN_ID = c.INVENTORY_IN_ID
                    JOIN warehouse w
                        ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
                WHERE b.PO_DETAIL_ID = $detail_id
            ) AS table_union
        ";

        $data = $this->db->query($query)->result();
        foreach ($data as $d) {
            $d->Tanggal = date('Y-m-d H:i', strtotime($d->Tanggal));
            $d->Jumlah = number_format((float) $d->Jumlah, 2, '.', ',');
            $No_Transaksi = explode('/',$d->No_Transaksi);
            $d->link = null;
            if(strtoupper($No_Transaksi[0]) == 'SJS'){
                $d->link = site_url('sts/detail/'.base64url_encode($this->encrypt->encode($d->HEADER_ID)));
            }else if(strtoupper($No_Transaksi[0]) == 'RSP'){
                $d->link = site_url('rsp/detail/'.base64url_encode($this->encrypt->encode($d->HEADER_ID)));
            }else if(strtoupper($No_Transaksi[0]) == 'MR'){
                $d->link = site_url('mrq/detail/'.base64url_encode($this->encrypt->encode($d->HEADER_ID)));
            }
            
        }
        echo json_encode($data);
    }

    public function print($id){
        $id     = (int) $this->encrypt->decode(base64url_decode($id));
        $grk    = $this->grk->get_grk_detail($id)->row();
        if($grk){
            $this->load->library('pdf');
            $data = [
                'dir_view' => 'grk/pdf',
                'data' => [
                    'grk' => $grk,
                    'grk_detail' => $this->grk->get_detail_by_pr_id($id)->result()
                ],
                'title' => str_replace('/',' ', $grk->DOCUMENT_NO),
            ];
            $html = $this->load->view('template_pdf', $data, true);
            $this->pdf->generate($html, str_replace('/',' ', $grk->DOCUMENT_NO), 'A4', 'portrait');
        }
    }
}
