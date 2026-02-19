<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sts extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Sts_model', 'sts');
    }
    public function index()
    {
        try {
            $data['title'] = 'STS';
            $data['breadcrumb'] = 'STS';
            $this->template->load('template', 'sts/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->sts->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $sts) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $sts->STATUS ? $sts->STATUS : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('sts/detail/' . base64url_encode($this->encrypt->encode($sts->TAG_KONSI_ID))) . '">
                ' . ($sts->No_Transaksi ? $sts->No_Transaksi : '-') . '
            </a>';
            $row['no_referensi'] = $sts->No_Referensi ? $sts->No_Referensi : '-';
            $row['tanggal'] = $sts->Tanggal ? date('Y-m-d H:i', strtotime($sts->Tanggal)) : '-';
            $row['main_storage'] = $sts->Main_Storage ? $sts->Main_Storage : '-';
            $row['site_storage'] = $sts->Site_Storage ? $sts->Site_Storage : '-';

            $row['tag_konsi_id'] = $this->encrypt->encode($sts->TAG_KONSI_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->sts->count_all(),
            "recordsFiltered" => $this->sts->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $tag_konsi_id = $this->encrypt->decode($this->input->post('tag_konsi_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->sts->count_detail_by_pr_id($tag_konsi_id);

            $list = $this->sts->get_detail_by_pr_id($tag_konsi_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"        => $no++,
                    "nama_item" => $d->Nama_Item,
                    "kode_item" => $d->Kode_Item,
                    "qty"       => number_format((float)$d->Qty, 2, '.', ''),
                    "sisa"       => number_format((float)$d->Sisa, 2, '.', ''),
                    "uom"       => $d->UoM,
                    "no_fpk"    => $d->No_FPK,
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

    public function getGrk()
    {
        try {
            $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

            $main_storage = $this->input->post('main_storage');
            $data = $this->db->query("SELECT
                    b.PO_DETAIL_ID,
                    b.PO_ID,
                    a.DOCUMENT_TYPE_ID,
                    a.STATUS_ID,
                    FN_GET_VAR_NAME (a.STATUS_ID) STATUS_NAME,
                    a.DOCUMENT_DATE,
                    a.DOCUMENT_NO,
                    a.DOCUMENT_REFF_NO,
                    a.PERSON_ID,
                    a.WAREHOUSE_ID,
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
                    i.BERAT,
                    b.NOTE
                FROM
                    po a
                    JOIN po_detail b
                        ON a.PO_ID = b.PO_ID
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
                    AND a.WAREHOUSE_ID = {$main_storage}
                ORDER BY a.DOCUMENT_DATE,
                    a.DOCUMENT_NO,
                    b.PO_DETAIL_ID;
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
        $tag_konsi_id = $this->encrypt->decode($this->input->post('tag_konsi_id'));
        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME FROM tag_konsi a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.TAG_KONSI_ID = {$tag_konsi_id}");
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

            $this->form_validation->set_rules('main_storage', 'main storage', 'trim|required');
            $this->form_validation->set_rules('site_storage', 'site storage', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah STS';
                $data['breadcrumb'] = 'Tambah STS';
                $data['main_storage'] = $this->sts->get_main_storage();
                $data['site_storage'] = $this->sts->get_site_storage();
                $this->template->load('template', 'sts/add', $data);
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail GRK wajib diisi!');
                    redirect('sts/add');
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
                        $this->session->set_flashdata('warning', 'Jumlah GRK "' . $detail['nama_item'][$i] . '" tidak boleh kosong');
                        redirect('sts/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah GRK "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('sts/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah GRK "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('sts/add');
                    }

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);
                    $harga_input = str_replace([','], '', $detail['harga_input'][$i]);
                    $subtotal = $jumlah * $harga_input;
                    $total += $subtotal;

                    $dataDetail = [
                        'TAG_KONSI_ID'      => $post['seq'],
                        'ITEM_ID'           => $detail['item_id'][$i],
                        'ENTERED_QTY'       => $detail['jumlah'][$i],
                        'BASE_QTY'          => $detail['base_qty'][$i],
                        'UNIT_PRICE'        => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'          => $subtotal,
                        'ENTERED_UOM'       => $detail['satuan'][$i],
                        'PO_DETAIL_ID'      => $detail['po_detail_id'][$i],
                        'HARGA_INPUT'       => $harga_input,
                        'ITEM_DESCRIPTION'  => $detail['nama_item'][$i],
                        'NOTE'              => $detail['keterangan'][$i],
                        'BERAT'             => $detail['berat'][$i],
                        'CREATED_BY'        => $this->session->userdata('id'),
                        'CREATED_DATE'      => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('tag_konsi_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('sts');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'TAG_KONSI_ID'          => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'WAREHOUSE_ID'          => $post['main_storage'],
                    'TO_WH_ID'              => $post['site_storage'],
                    'KONSINYASI_FLAG'       => "Y",
                    'PPN_CODE'              => "NO PPN",
                    'PPH_CODE'              => "NO PPH",
                    'NOTE'                  => $post['keterangan'],
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('tag_konsi', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('sts');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('sts');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('sts/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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

            $this->form_validation->set_rules('main_storage', 'main storage', 'trim|required');
            $this->form_validation->set_rules('site_storage', 'site storage', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->sts->getTagKonsiId($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['main_storage'] = $this->sts->get_main_storage();
                    $data['site_storage'] = $this->sts->get_site_storage();
                    $data['data'] = $query->row();
                    $this->template->load('template', 'sts/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('sts');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $tag_konsi_id   = $this->encrypt->decode($post['tag_konsi_id']);
                $detail = $post['detail'] ?? [];

                if (!$tag_konsi_id || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail item tidak boleh kosong dan wajib diisi!');
                    redirect('sts/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('TAG_KONSI_DETAIL_ID')
                    ->from('tag_konsi_detail')
                    ->where('TAG_KONSI_ID', $tag_konsi_id)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'TAG_KONSI_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['tag_konsi_detail_id'])) {
                    foreach ($detail['tag_konsi_detail_id'] as $val) {
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

                    $poDetailId = !empty($detail['tag_konsi_detail_id'][$i])
                        ? $this->encrypt->decode($detail['tag_konsi_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'           => $detail['item_id'][$i],
                        'ENTERED_QTY'       => $detail['jumlah'][$i],
                        'BASE_QTY'          => $detail['base_qty'][$i],
                        'UNIT_PRICE'        => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'          => $subtotal,
                        'ENTERED_UOM'       => $detail['satuan'][$i],
                        'PO_DETAIL_ID'      => $detail['po_detail_id'][$i],
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
                            'tag_konsi_detail',
                            $dataDetail,
                            ['TAG_KONSI_DETAIL_ID' => $poDetailId, 'TAG_KONSI_ID' => $tag_konsi_id]
                        );
                    } else {
                        // INSERT
                        $dataDetail['TAG_KONSI_ID'] = $tag_konsi_id;
                        $dataDetail['CREATED_BY']   = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE'] = date('Y-m-d H:i:s');

                        $this->db->insert('tag_konsi_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('sts/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('TAG_KONSI_ID', $tag_konsi_id)
                        ->where_in('TAG_KONSI_DETAIL_ID', $deleteIds)
                        ->delete('tag_konsi_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('tag_konsi', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'WAREHOUSE_ID'          => $post['main_storage'],
                    'TO_WH_ID'              => $post['site_storage'],
                    'NOTE'                  => $post['keterangan'],
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['TAG_KONSI_ID' => $tag_konsi_id]);

                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                    redirect('sts/detail/' . $id);
                }

                // ===============================
                // COMMIT / ROLLBACK
                // ===============================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal memperbarui STS!');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'STS berhasil diperbarui!');
                }
                redirect('sts/detail/' . $id);
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

        $delResult = $this->sts->delete($id);
        if ($delResult !== true) {
            $this->db->trans_rollback();
            $this->_jsonError($delResult);
            return;
        }

        $updResult = $this->sts->updateStatus($id, $status);
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
                'message' => 'STS berhasil dihapus!'
            ]));
    }


    private function _jsonError($db_error)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status'  => false,
                'message' => 'Gagal menghapus STS!',
                'error'   => $db_error['message'],
                'code'    => $db_error['code']
            ]));
    }
}
