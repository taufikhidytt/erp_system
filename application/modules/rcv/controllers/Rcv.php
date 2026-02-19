<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rcv extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Rcv_model', 'rcv');
    }
    public function index()
    {
        try {
            $data['title'] = 'RCV';
            $data['breadcrumb'] = 'RCV';
            $this->template->load('template', 'rcv/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->rcv->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $sts) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $sts->STATUS ? $sts->STATUS : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('rcv/detail/' . base64url_encode($this->encrypt->encode($sts->TAG_ID))) . '">
                ' . ($sts->No_Transaksi ? $sts->No_Transaksi : '-') . '
            </a>';
            $row['no_referensi'] = $sts->No_Referensi ? $sts->No_Referensi : '-';
            $row['tanggal'] = $sts->Tanggal ? date('Y-m-d H:i', strtotime($sts->Tanggal)) : '-';
            $row['site'] = $sts->Site ? $sts->Site : '-';

            $row['tag_id'] = $this->encrypt->encode($sts->TAG_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->rcv->count_all(),
            "recordsFiltered" => $this->rcv->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $tag_id = $this->encrypt->decode($this->input->post('tag_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->rcv->count_detail_by_tag_id($tag_id);

            $list = $this->rcv->get_detail_by_tag_id($tag_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"        => $no++,
                    "nama_item" => $d->Nama_Item,
                    "kode_item" => $d->Kode_Item,
                    "jumlah"    => number_format((float)$d->Qty, 2, '.', ''),
                    "sisa"    => number_format((float)$d->Sisa, 2, '.', ''),
                    "satuan"    => $d->UoM,
                    "no_sjs"    => $d->No_SJS,
                    "loc_in"    => $d->S_Loc_In,
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

    public function getSjs()
    {
        try {
            $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

            $site_storage = $this->input->post('site_storage');
            $data = $this->db->query("SELECT
                b.TAG_KONSI_DETAIL_ID,
                b.TAG_KONSI_ID,
                b.PO_DETAIL_ID,
                a.DOCUMENT_TYPE_ID,
                a.STATUS_ID,
                FN_GET_VAR_NAME ( a.STATUS_ID ) STATUS_NAME,
                a.DOCUMENT_DATE,
                a.DOCUMENT_NO,
                a.DOCUMENT_REFF_NO,
                a.PERSON_ID,
                a.WAREHOUSE_ID,
                a.TO_WH_ID,
                wh.WAREHOUSE_NAME GUDANG_ASAL,
                w.WAREHOUSE_NAME GUDANG_TUJUAN,
                b.ITEM_ID,
                i.ITEM_CODE,
                i.ITEM_DESCRIPTION,
                b.ENTERED_QTY,
                b.BASE_QTY,
                b.ENTERED_QTY - ( b.RECEIVED_ENTERED_QTY / b.BASE_QTY ) AS BALANCE,
                b.ENTERED_UOM,
                b.UNIT_PRICE,
                b.SUBTOTAL,
                b.HARGA_INPUT,
                i.BERAT,
                b.NOTE 
            FROM
                tag_konsi a
                JOIN tag_konsi_detail b ON a.TAG_KONSI_ID = b.TAG_KONSI_ID
                JOIN item i ON b.ITEM_ID = i.ITEM_ID
                JOIN warehouse w ON a.TO_WH_ID = w.WAREHOUSE_ID
                JOIN warehouse wh ON a.WAREHOUSE_ID = wh.WAREHOUSE_ID 
            WHERE
                ( b.ENTERED_QTY * b.BASE_QTY ) > 0 
                AND ( b.RECEIVED_ENTERED_QTY * b.RECEIVED_BASE_QTY ) < ( b.ENTERED_QTY * b.BASE_QTY ) 
                AND a.STATUS_ID IN ( FN_GET_VAR_VALUE ( 'NEW' ), FN_GET_VAR_VALUE ( 'PARTIAL' ) ) 
                AND a.DOCUMENT_TYPE_ID = '{$tipe_id['TYPE_ID']}'
                AND a.TO_WH_ID = $site_storage 
            ORDER BY
                a.DOCUMENT_DATE,
                a.DOCUMENT_NO,
                b.TAG_KONSI_DETAIL_ID;
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
        $tag_id = $this->encrypt->decode($this->input->post('tag_id'));
        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME FROM tag a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.TAG_ID = {$tag_id}");
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

            $this->form_validation->set_rules('site_storage', 'site storage', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah RCV';
                $data['breadcrumb'] = 'Tambah RCV';
                $data['site_storage'] = $this->rcv->get_site_storage();
                $this->template->load('template', 'rcv/add', $data);
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
                        $this->session->set_flashdata('warning', 'Jumlah SJS "' . $detail['nama_item'][$i] . '" tidak boleh kosong');
                        redirect('rcv/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah SJS "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('rcv/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah SJS "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('rcv/add');
                    }

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);
                    $harga_input = str_replace([','], '', $detail['harga_input'][$i]);
                    $subtotal = $jumlah * $harga_input;
                    $total += $subtotal;

                    $dataDetail = [
                        'TAG_ID'                => $post['seq'],
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'              => $subtotal,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'WAREHOUSE_ID'          => $detail['gudang_id'][$i],
                        'TO_WH_ID'              => $detail['gudang_tujuan_id'][$i],
                        'TAG_KONSI_DETAIL_ID'   => $detail['tag_konsi_detail_id'][$i],
                        'PO_DETAIL_ID'          => $detail['po_detail_id'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'CREATED_BY'            => $this->session->userdata('id'),
                        'CREATED_DATE'          => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('tag_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('rcv');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'TAG_ID'                => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'DEST_WH_ID'            => $post['site_storage'],
                    'TO_WH_ID'              => $post['site_storage'],
                    'KONSINYASI_FLAG'       => "Y",
                    'PPN_CODE'              => "NO PPN",
                    'PPH_CODE'              => "NO PPH",
                    'NOTE'                  => $post['keterangan'],
                    'PERIOD_NAME'           => date('Ym', strtotime($post['tanggal'])),
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('tag', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('rcv');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('rcv');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('rcv/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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

            $this->form_validation->set_rules('site_storage', 'site storage', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->rcv->getTagId($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['site_storage'] = $this->rcv->get_site_storage();
                    $data['data'] = $query->row();
                    $this->template->load('template', 'rcv/detail', $data);
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
                $tag_id   = $this->encrypt->decode($post['tag_id']);
                $detail = $post['detail'] ?? [];

                if (!$tag_id || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail item tidak boleh kosong dan wajib diisi!');
                    redirect('rcv/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('TAG_DETAIL_ID')
                    ->from('tag_detail')
                    ->where('TAG_ID', $tag_id)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'TAG_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['tag_detail_id'])) {
                    foreach ($detail['tag_detail_id'] as $val) {
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

                    $tagDetailId = !empty($detail['tag_detail_id'][$i])
                        ? $this->encrypt->decode($detail['tag_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'              => $subtotal,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'WAREHOUSE_ID'          => $detail['gudang_id'][$i],
                        'TO_WH_ID'              => $detail['gudang_tujuan_id'][$i],
                        'TAG_KONSI_DETAIL_ID'   => $detail['tag_konsi_detail_id'][$i],
                        'PO_DETAIL_ID'          => $detail['po_detail_id'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    if ($tagDetailId) {
                        // UPDATE
                        $this->db->update(
                            'tag_detail',
                            $dataDetail,
                            ['TAG_DETAIL_ID' => $tagDetailId, 'TAG_ID' => $tag_id]
                        );
                    } else {
                        // INSERT
                        $dataDetail['TAG_ID'] = $tag_id;
                        $dataDetail['CREATED_BY']   = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE'] = date('Y-m-d H:i:s');

                        $this->db->insert('tag_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('rcv/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('TAG_ID', $tag_id)
                        ->where_in('TAG_DETAIL_ID', $deleteIds)
                        ->delete('tag_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('tag', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'DEST_WH_ID'            => $post['site_storage'],
                    'TO_WH_ID'              => $post['site_storage'],
                    'NOTE'                  => $post['keterangan'],
                    'PERIOD_NAME'           => date('Ym', strtotime($post['tanggal'])),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['TAG_ID' => $tag_id]);

                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                    redirect('rcv/detail/' . $id);
                }

                // ===============================
                // COMMIT / ROLLBACK
                // ===============================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal memperbarui RCV!');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'RCV berhasil diperbarui!');
                }
                redirect('rcv/detail/' . $id);
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

        $updResult = $this->rcv->updateStatus($id, $status);
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
