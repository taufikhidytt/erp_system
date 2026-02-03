<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fpk extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Fpk_model', 'fpk');
    }
    public function index()
    {
        try {
            $data['title'] = 'FPK';
            $data['breadcrumb'] = 'FPK';
            $this->template->load('template', 'fpk/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->fpk->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $fpk) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $fpk->Status ? $fpk->Status : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('fpk/detail/' . base64url_encode($this->encrypt->encode($fpk->PR_ID))) . '">
                ' . ($fpk->No_Transaksi ? $fpk->No_Transaksi : '-') . '
            </a>';
            $row['no_referensi'] = $fpk->No_Referensi ? $fpk->No_Referensi : '-';
            $row['tanggal'] = $fpk->Tanggal ? date('Y-m-d H:i', strtotime($fpk->Tanggal)) : '-';
            $row['tanggal_dibutuhkan'] = $fpk->Dibutuhkan ? date('Y-m-d H:i', strtotime($fpk->Dibutuhkan)) : '-';
            $row['supplier'] = $fpk->Supplier ? $fpk->Supplier : '-';
            $row['sales'] = $fpk->Sales ? $fpk->Sales : '-';
            $row['gudang'] = $fpk->Gudang ? $fpk->Gudang : '-';
            $row['total'] = $fpk->Total ? number_format($fpk->Total, 2, '.', ',') : '-';

            $row['pr_id'] = $this->encrypt->encode($fpk->PR_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->fpk->count_all(),
            "recordsFiltered" => $this->fpk->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $pr_id = $this->encrypt->decode($this->input->post('pr_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->fpk->count_detail_by_pr_id($pr_id);

            $list = $this->fpk->get_detail_by_pr_id($pr_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"        => $no++,
                    "item"      => $d->Item_Name,
                    "item_code" => $d->ITEM_CODE,
                    "entered_uom" => $d->ENTERED_UOM,
                    "qty"       => number_format((float)$d->QTY, 2, '.', ''),
                    "price"     => number_format($d->PRICE, 2, '.', ','),
                    "total"     => number_format($d->TOTAL, 2, '.', ','),
                    "note"      => $d->NOTE,
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
            $supplier = $this->input->post('id_supplier');
            $data = $this->db->query("SELECT
                i.ITEM_ID,
                i.ITEM_CODE,
                LEFT(i.ITEM_DESCRIPTION, 40) AS ITEM_DESCRIPTION,
                LEFT(i.ASSY_CODE, 30) AS ASSY_CODE,
                LEFT(e.DISPLAY_NAME, 30) AS CATEGORY,
                i.UOM_CODE UOM,
                COALESCE(
                    (SELECT
                        SUM(QTY_AWAL + QTY_MASUK - QTY_KELUAR)
                    FROM
                        item_stok_konsinyasi
                    WHERE ITEM_ID = i.ITEM_ID),
            0
                ) AS STOK,
                mr.DISPLAY_NAME AS BRAND,
                tipe.DISPLAY_NAME AS TIPE,
                i.JENIS_ID
            FROM
                item i
                JOIN ERP_LOOKUP_VALUE e
                    ON e.ERP_LOOKUP_VALUE_ID = i.GROUP_ID
                JOIN ERP_LOOKUP_VALUE tipe
                    ON i.TYPE_ID = tipe.ERP_LOOKUP_VALUE_ID
                JOIN ERP_LOOKUP_VALUE mr
                    ON i.MEREK_ID = mr.ERP_LOOKUP_VALUE_ID
                JOIN PRICE_LIST_DETAIL b
                    ON i.ITEM_ID = b.ITEM_ID
                    AND b.ACTIVE_FLAG = 'Y'
                    AND b.ENTERED_UOM = i.UOM_CODE
            WHERE i.ACTIVE_FLAG = 'Y'
                AND i.APPROVE_FLAG = 'Y'
                AND i.TYPE_ID = FN_GET_VAR_VALUE ('INV')
                AND i.JENIS_ID = FN_GET_VAR_VALUE ('GOODS')
                AND i.PERSON_ID = {$supplier}
            ORDER BY i.ITEM_CODE");

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

    public function get_uom()
    {
        try {
            $id_item = $this->input->post('item_id');
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
                WHERE i.ITEM_ID = {$id_item}
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
                WHERE iu.ITEM_ID = {$id_item}
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

    public function getStatus()
    {
        $pr_id = $this->encrypt->decode($this->input->post('pr_id'));
        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME FROM pr a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.PR_ID = {$pr_id}");
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
            $this->form_validation->set_rules('tanggal_dibutuhkan', 'tanggal dibutuhkan', 'trim|required');
            $this->form_validation->set_rules('gudang', 'gudang', 'trim|required');
            $this->form_validation->set_rules('sales', 'sales', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah FPK';
                $data['breadcrumb'] = 'Tambah FPK';
                $data['supplier'] = $this->fpk->getSupplier();
                $data['gudang'] = $this->fpk->getGudang();
                $data['sales'] = $this->fpk->getSales();
                $this->template->load('template', 'fpk/add', $data);
            } else {
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail item wajib diisi!');
                    redirect('fpk/add');
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

                    $qty   = (float) $detail['qty'][$i];
                    $harga = (float) $detail['harga_input'][$i];

                    $subtotal   = $qty * $harga;
                    $total += $subtotal;

                    $dataDetail = [
                        'PR_ID'             => $post['seq'],
                        'ITEM_ID'           => $detail['id_item'][$i],
                        'ENTERED_QTY'       => $qty,
                        'BASE_QTY'          => $detail['to_qty'][$i],
                        'UNIT_PRICE'        => $detail['harga'][$i],
                        'HARGA_INPUT'       => $harga,
                        'SUBTOTAL'          => $subtotal,
                        'NOTE'              => $detail['keterangan'][$i],
                        'ENTERED_UOM'       => $detail['uom'][$i],
                        'CREATED_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                        'CREATED_DATE'      => date('Y-m-d H:i:s'),
                        'ITEM_DESCRIPTION'  => $detail['nama_item'][$i],
                    ];

                    $this->db->insert('pr_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('fpk');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'pr_id'                 => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'PERIOD_NAME'           => date('Ym', strtotime($post['tanggal'])),
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'TOTAL_AMOUNT'          => $total,
                    'PERSON_ID'             => $post['supplier'],
                    'WAREHOUSE_ID'          => $post['gudang'],
                    'KARYAWAN_ID'           => $post['sales'],
                    'NEED_DATE'             => $post['tanggal_dibutuhkan'],
                    'NOTE'                  => $post['keterangan'],
                    'TIPE_ID'               => 4,
                    'PO_SUPP'               => 1,
                    'PRINT_COUNT'           => 0,
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('pr', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('fpk');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('fpk');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('fpk/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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
            $this->form_validation->set_rules('tanggal_dibutuhkan', 'tanggal dibutuhkan', 'trim|required');
            $this->form_validation->set_rules('gudang', 'gudang', 'trim|required');
            $this->form_validation->set_rules('sales', 'sales', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->fpk->getPrId($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['supplier'] = $this->fpk->getSupplier();
                    $data['gudang'] = $this->fpk->getGudang();
                    $data['sales'] = $this->fpk->getSales();
                    $data['data'] = $query->row();
                    $this->template->load('template', 'fpk/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('fpk');
                }
            } else {
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $prId   = $this->encrypt->decode($post['pr_id']);
                $detail = $post['detail'] ?? [];

                if (!$prId || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail item tidak boleh kosong dan wajib diisi!');
                    redirect('fpk/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('PR_DETAIL_ID')
                    ->from('pr_detail')
                    ->where('PR_ID', $prId)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'PR_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['pr_detail'])) {
                    foreach ($detail['pr_detail'] as $val) {
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
                foreach ($detail['qty'] as $i => $qtyRaw) {

                    if (empty($detail['nama_item'][$i])) continue;

                    $qty   = (float) $detail['qty'][$i];
                    $harga = (float) $detail['harga_input'][$i];
                    $subtotal = $qty * $harga;
                    $total += $subtotal;

                    $prDetailId = !empty($detail['pr_detail'][$i])
                        ? $this->encrypt->decode($detail['pr_detail'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'          => $detail['id_item'][$i],
                        'ENTERED_QTY'      => $qty,
                        'BASE_QTY'         => $detail['to_qty'][$i],
                        'UNIT_PRICE'       => $detail['harga'][$i] ?? $harga,
                        'HARGA_INPUT'      => $harga,
                        'SUBTOTAL'         => $subtotal,
                        'NOTE'             => $detail['keterangan'][$i],
                        'ENTERED_UOM'      => $detail['uom'][$i],
                        'ITEM_DESCRIPTION' => $detail['nama_item'][$i],
                        'LAST_UPDATE_BY'   => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
                    ];

                    if ($prDetailId) {
                        // UPDATE
                        $this->db->update(
                            'pr_detail',
                            $dataDetail,
                            ['PR_DETAIL_ID' => $prDetailId, 'PR_ID' => $prId]
                        );
                    } else {
                        // INSERT
                        $dataDetail['PR_ID']        = $prId;
                        $dataDetail['CREATED_BY']   = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE'] = date('Y-m-d H:i:s');

                        $this->db->insert('pr_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('fpk/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('PR_ID', $prId)
                        ->where_in('PR_DETAIL_ID', $deleteIds)
                        ->delete('pr_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================
                $this->db->update('pr', [
                    'DOCUMENT_DATE'    => $post['tanggal'],
                    'PERIOD_NAME'      => date('Ym', strtotime($post['tanggal'])),
                    'DOCUMENT_REFF_NO' => $post['no_referensi'],
                    'TOTAL_AMOUNT'     => $total,
                    'PERSON_ID'        => $post['supplier'],
                    'WAREHOUSE_ID'     => $post['gudang'],
                    'KARYAWAN_ID'      => $post['sales'],
                    'NEED_DATE'        => $post['tanggal_dibutuhkan'],
                    'NOTE'             => $post['keterangan'],
                    'LAST_UPDATE_BY'   => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
                ], ['PR_ID' => $prId]);

                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                    redirect('fpk/detail/' . $id);
                }

                // ===============================
                // COMMIT / ROLLBACK
                // ===============================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal memperbarui FPK!');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'FPK berhasil diperbarui!');
                }
                redirect('fpk/detail/' . $id);
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

        $del = $this->fpk->delete($id);
        $upd = $this->fpk->updateStatus($id, $status);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $result = [
                'status'    =>  false,
                'message'   => 'Gagal menghapus FPK, transaksi dibatalkan!'
            ];
        } else {
            $result = [
                'status'    =>  true,
                'message'   => 'FPK berhasil dihapus!',
            ];
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }
}
