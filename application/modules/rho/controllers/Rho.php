<?php

use FontLib\Table\Type\post;

defined('BASEPATH') or exit('No direct script access allowed');

class Rho extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Rho_model', 'rho');
    }
    public function index()
    {
        try {
            $data['title'] = 'RHO';
            $data['breadcrumb'] = 'RHO';
            $this->template->load('template', 'rho/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->rho->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $rho) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $rho->STATUS ? $rho->STATUS : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('rho/detail/' . base64url_encode($this->encrypt->encode($rho->REQUEST_QTY_ID))) . '">
                ' . ($rho->No_Transaksi ? $rho->No_Transaksi : '-') . '
            </a>';
            $row['no_referensi'] = $rho->No_Referensi ? $rho->No_Referensi : '-';
            $row['tanggal'] = $rho->Tanggal ? date('Y-m-d H:i', strtotime($rho->Tanggal)) : '-';
            $row['main_storage'] = $rho->Main_Storage ? $rho->Main_Storage : '-';
            $row['site_storage'] = $rho->Site_Storage ? $rho->Site_Storage : '-';

            $row['request_qty_id'] = $this->encrypt->encode($rho->REQUEST_QTY_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->rho->count_all(),
            "recordsFiltered" => $this->rho->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $request_qty_id = $this->encrypt->decode($this->input->post('request_qty_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->rho->count_detail_by_request_qty_id($request_qty_id);

            $list = $this->rho->get_detail_by_request_qty_id($request_qty_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"        => $no++,
                    "nama_item" => $d->Nama_Item,
                    "kode_item" => $d->Kode_Item,
                    "jumlah"    => number_format((float)$d->Qty, 2, '.', ''),
                    "sisa"      => number_format((float)$d->Sisa, 2, '.', ''),
                    "satuan"    => $d->UoM,
                    "no_rcv"    => $d->No_RCV,
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

    public function getRcv()
    {
        try {
            $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

            $site_storage = $this->input->post('site_storage');

            $data = $this->db->query("SELECT
                b.TAG_DETAIL_ID,
                b.TAG_ID,
                b.PO_DETAIL_ID,
                b.TAG_KONSI_DETAIL_ID,
                a.DOCUMENT_TYPE_ID,
                a.STATUS_ID,
                FN_GET_VAR_NAME ( a.STATUS_ID ) STATUS_NAME,
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
                b.ENTERED_QTY - ( b.DELIVERED_ENTERED_QTY / b.BASE_QTY ) AS BALANCE,
                b.ENTERED_UOM,
                b.UNIT_PRICE,
                b.SUBTOTAL,
                b.HARGA_INPUT,
                i.BERAT,
                b.NOTE 
            FROM
                tag a
                JOIN tag_detail b ON a.TAG_ID = b.TAG_ID
                JOIN item i ON b.ITEM_ID = i.ITEM_ID
                JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID 
            WHERE
                ( b.ENTERED_QTY * b.BASE_QTY ) > 0 
                AND ( b.DELIVERED_ENTERED_QTY * b.DELIVERED_BASE_QTY ) < ( b.ENTERED_QTY * b.BASE_QTY ) 
                AND a.STATUS_ID IN ( FN_GET_VAR_VALUE ( 'NEW' ), FN_GET_VAR_VALUE ( 'PARTIAL' ) ) 
                AND a.DOCUMENT_TYPE_ID = '{$tipe_id['TYPE_ID']}'
                AND a.TO_WH_ID = {$site_storage}
            ORDER BY
                a.DOCUMENT_DATE,
                a.DOCUMENT_NO,
                b.TAG_DETAIL_ID;
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
        $request_qty_id = $this->encrypt->decode($this->input->post('request_qty_id'));
        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME FROM request_qty a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.REQUEST_QTY_ID = {$request_qty_id}");
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
                $data['title'] = 'Tambah RHO';
                $data['breadcrumb'] = 'Tambah RHO';
                $data['site_storage'] = $this->rho->get_site_storage();
                $data['main_storage'] = $this->rho->get_main_storage();
                $data['detail'] = $this->input->post('detail');
                $this->template->load('template', 'rho/add', $data);
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
                        $this->session->set_flashdata('warning', 'Jumlah RCV "' . $detail['nama_item'][$i] . '" tidak boleh kosong');
                        redirect('rho/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah RCV "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('rho/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah RCV "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('rho/add');
                    }

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);
                    $harga_input = str_replace([','], '', $detail['harga_input'][$i]);
                    $subtotal = $jumlah * $harga_input;
                    $total += $subtotal;

                    $dataDetail = [
                        'REQUEST_QTY_ID'        => $post['seq'],
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'              => $subtotal,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'TAG_DETAIL_ID'         => $detail['tag_detail_id'][$i],
                        'PO_DETAIL_ID'          => $detail['po_detail_id'][$i],
                        'TAG_KONSI_DETAIL_ID'   => $detail['tag_konsi_detail_id'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'CREATED_BY'            => $this->session->userdata('id'),
                        'CREATED_DATE'          => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('request_qty_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('rho');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'REQUEST_QTY_ID'        => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'WAREHOUSE_ID'          => $post['site_storage'],
                    'TO_WH_ID'              => $post['main_storage'],
                    'KONSINYASI_FLAG'       => "Y",
                    'PPN_CODE'              => "NO PPN",
                    'PPH_CODE'              => "NO PPH",
                    'NOTE'                  => $post['keterangan'],
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('request_qty', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('rho');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('rho');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('rho/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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
                $query = $this->rho->getRequestQtyId($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['site_storage'] = $this->rho->get_site_storage();
                    $data['main_storage'] = $this->rho->get_main_storage();
                    $data['data'] = $query->row();
                    $data['detail'] = $this->input->post('detail');
                    $this->template->load('template', 'rho/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('rho');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $request_qty_id   = $this->encrypt->decode($post['request_qty_id']);
                $detail = $post['detail'] ?? [];

                if (!$request_qty_id || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail RCV tidak boleh kosong dan wajib diisi!');
                    redirect('rho/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('REQUEST_QTY_DETAIL_ID')
                    ->from('request_qty_detail')
                    ->where('REQUEST_QTY_ID', $request_qty_id)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'REQUEST_QTY_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['request_qty_detail_id'])) {
                    foreach ($detail['request_qty_detail_id'] as $val) {
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

                    $request_qty_detail_id = !empty($detail['request_qty_detail_id'][$i])
                        ? $this->encrypt->decode($detail['request_qty_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'UNIT_PRICE'            => str_replace([','], '', $detail['unit_price'][$i]),
                        'SUBTOTAL'              => $subtotal,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'TAG_DETAIL_ID'         => $detail['tag_detail_id'][$i],
                        'PO_DETAIL_ID'          => $detail['po_detail_id'][$i],
                        'TAG_KONSI_DETAIL_ID'   => $detail['tag_konsi_detail_id'][$i],
                        'HARGA_INPUT'           => $harga_input,
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    if ($request_qty_detail_id) {
                        // UPDATE
                        $this->db->update(
                            'request_qty_detail',
                            $dataDetail,
                            ['REQUEST_QTY_DETAIL_ID' => $request_qty_detail_id, 'REQUEST_QTY_ID' => $request_qty_id]
                        );
                    } else {
                        // INSERT
                        $dataDetail['REQUEST_QTY_ID']   = $request_qty_id;
                        $dataDetail['CREATED_BY']       = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE']     = date('Y-m-d H:i:s');

                        $this->db->insert('request_qty_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('rho/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('REQUEST_QTY_ID', $request_qty_id)
                        ->where_in('REQUEST_QTY_DETAIL_ID', $deleteIds)
                        ->delete('request_qty_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('request_qty', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_referensi'],
                    'WAREHOUSE_ID'          => $post['site_storage'],
                    'TO_WH_ID'              => $post['main_storage'],
                    'NOTE'                  => $post['keterangan'],
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['REQUEST_QTY_ID' => $request_qty_id]);

                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                    redirect('rho/detail/' . $id);
                }

                // ===============================
                // COMMIT / ROLLBACK
                // ===============================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal memperbarui RHO!');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'RHO berhasil diperbarui!');
                }
                redirect('rho/detail/' . $id);
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

        $delResult = $this->rho->delete($id);
        if ($delResult !== true) {
            $this->db->trans_rollback();
            $this->_jsonError($delResult);
            return;
        }

        $updResult = $this->rho->updateStatus($id, $status);
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
                'message' => 'RHO berhasil dihapus!'
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
            'table' => 'request_qty_detail b',
            'select' => [
                'b.REQUEST_QTY_DETAIL_ID, i.ITEM_DESCRIPTION Nama_Item, i.ITEM_CODE Kode_Item, b.ENTERED_UOM Satuan, b.ENTERED_QTY RHO',
                ['(b.DELIVER_QTY / b.BASE_QTY) AS RCO', FALSE],
                ['(b.ENTERED_QTY - (b.DELIVER_QTY / b.BASE_QTY)) AS SISA', FALSE],
            ],
            'joins' => [
                ['item i', 'b.ITEM_ID = i.ITEM_ID', 'inner'],
            ],
            'where' => ['b.REQUEST_QTY_ID' => $id],
            'column_search' => ['i.ITEM_DESCRIPTION', 'i.ITEM_CODE','b.ENTERED_UOM', 'b.ENTERED_QTY'],
            'column_order'  => [null,null,'i.ITEM_DESCRIPTION', 'i.ITEM_CODE', 'b.ENTERED_UOM', 'b.ENTERED_QTY', '(b.DELIVER_QTY / b.BASE_QTY)', '(b.ENTERED_QTY - (b.DELIVER_QTY / b.BASE_QTY))'],
            'order' => ['i.ITEM_DESCRIPTION' => 'asc'],
        ];

        echo json_encode($this->datatables->generate($params, function($row, $no) {
            return [
                'no' => $no,
                'request_qty_detail_id' => base64url_encode($this->encrypt->encode($row->REQUEST_QTY_DETAIL_ID)),
                'nama_item' => $row->Nama_Item,
                'kode_item' => $row->Kode_Item,
                'satuan' => $row->Satuan,
                'rho' => number_format((float)$row->RHO, 2, '.', ','),
                'rco' => number_format((float)$row->RCO, 2, '.', ','),
                'sisa' => number_format((float)$row->SISA, 2, '.', ','),
            ];
        }));
    }

    public function get_info_detail($detail_id)
    {
        $detail_id = (int) $this->encrypt->decode(base64url_decode($detail_id));
        $this->load->model('M_datatables', 'datatables');
        $params = [
            'table' => 'request_qty_detail b',
            'select' => [
                'c.DOCUMENT_NO No_Transaksi,c.DOCUMENT_DATE Tanggal,
                    b.ENTERED_UOM Satuan,w.WAREHOUSE_NAME S_Loc,a.TAG_ID',
                ['(a.ENTERED_QTY * b.BASE_QTY) Jumlah', FALSE],
                
            ],
            'joins' => [
                ['tag_detail a', 'b.REQUEST_QTY_DETAIL_ID = a.REQUEST_QTY_DETAIL_ID', 'inner'],
                ['tag c', 'a.TAG_ID = c.TAG_ID', 'inner'],
                ['warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID', 'inner'],
            ],
            'where' => ['b.REQUEST_QTY_DETAIL_ID' => $detail_id],
            'order' => ['b.REQUEST_QTY_DETAIL_ID' => 'asc'],
            'column_search' => ['c.DOCUMENT_NO', 'c.DOCUMENT_DATE','(a.ENTERED_QTY * b.BASE_QTY)','b.ENTERED_UOM', 'w.WAREHOUSE_NAME'],
            'column_order'  => [null,'c.DOCUMENT_NO', 'c.DOCUMENT_DATE','(a.ENTERED_QTY * b.BASE_QTY)','b.ENTERED_UOM', 'w.WAREHOUSE_NAME'],
        ];
        echo json_encode($this->datatables->generate($params, function($row, $no) {
            return [
                'no' => $no,
                'no_transaksi' => '<a href="'.site_url('rco/detail/'.base64url_encode($this->encrypt->encode($row->TAG_ID))).'" target="_blank">'.$row->No_Transaksi.'</a>',
                'tanggal' => date('Y-m-d H:i', strtotime($row->Tanggal)),
                'satuan' => $row->Satuan,
                'jumlah' => number_format((float)$row->Jumlah, 2, '.', ','),
                's_loc' => $row->S_Loc,
            ];
        }));
    }

    public function print($id){
        $id     = (int) $this->encrypt->decode(base64url_decode($id));
        $rho    = $this->rho->get_rho_detail($id)->row();
        if($rho){
            $this->load->library('pdf');
            $data = [
                'dir_view' => 'rho/pdf',
                'data' => [
                    'rho' => $rho,
                    'rho_detail' => $this->rho->get_detail_by_request_qty_id($id)->result()
                ],
                'title' => str_replace('/',' ', $rho->DOCUMENT_NO),
            ];
            $html = $this->load->view('template_pdf', $data, true);
            $this->pdf->generate($html, str_replace('/',' ', $rho->DOCUMENT_NO), 'A4', 'portrait');
        }
    }
}
