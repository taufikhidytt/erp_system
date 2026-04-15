<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Formula extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Formula_model', 'formula');
    }

    public function index()
    {
        try {
            $data['title'] = 'Formula';
            $data['breadcrumb'] = 'Formula';
            $this->template->load('template', 'formula/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->formula->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $formula) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['status'] = badge_status($formula->Status, $formula->Warna_Status);
            $row['no_transaksi'] = '
            <a href="' . base_url('formula/detail/' . base64url_encode($this->encrypt->encode($formula->BOM_ID))) . '">
                ' . ($formula->No_Transaksi ? $formula->No_Transaksi : '-') . '
            </a>';
            $row['no_referensi'] = $formula->No_Referensi ? $formula->No_Referensi : '-';
            $row['nama_item'] = $formula->Nama_Item ? $formula->Nama_Item : '-';
            $row['satuan'] = $formula->UoM ? $formula->UoM : '-';
            $row['unit'] = $formula->Unit ? $formula->Unit : '-';
            $row['lokasi'] = $formula->Code ? $formula->Code : '-';
            $row['tanggal_mulai'] = $formula->Start_Date ? date('Y-m-d H:i', strtotime($formula->Start_Date)) : '-';
            $row['tanggal_selesai'] = $formula->End_Date ? date('Y-m-d H:i', strtotime($formula->End_Date)) : '-';
            $row['active_flag'] = $formula->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>';

            $row['bom_id'] = $this->encrypt->encode($formula->BOM_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->formula->count_all(),
            "recordsFiltered" => $this->formula->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $bom_id = $this->encrypt->decode($this->input->post('bom_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->formula->count_detail_by_bom_id($bom_id);

            $list = $this->formula->get_detail_by_bom_id($bom_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"            => $no++,
                    "nama_item"     => $d->Nama_Item,
                    "kode_item"     => $d->Kode_Item,
                    "jumlah"        => number_format((float)$d->Qty, 2, '.', ''),
                    "satuan"        => $d->UoM,
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

    public function getItem()
    {
        try {
            $item_finish_goods = $this->input->post('item_finish_goods');

            $data = $this->db->query("  SELECT
                i.ITEM_ID,
                i.ITEM_CODE,
                LEFT ( i.ITEM_DESCRIPTION, 40 ) AS ITEM_DESCRIPTION,
                LEFT ( i.ASSY_CODE, 30 ) AS ASSY_CODE,
                LEFT ( e.DISPLAY_NAME, 30 ) AS CATEGORY,
                i.UOM_CODE UOM,
                COALESCE ( ( SELECT SUM( QTY_AWAL + QTY_MASUK - QTY_KELUAR ) FROM item_stok WHERE ITEM_ID = i.ITEM_ID ), 0 ) AS STOK,
                mr.DISPLAY_NAME AS BRAND,
                tipe.DISPLAY_NAME AS TIPE,
                i.JENIS_ID,
                i.BERAT 
            FROM
                item i
                JOIN erp_lookup_value e ON e.ERP_LOOKUP_VALUE_ID = i.GROUP_ID
                JOIN erp_lookup_value tipe ON i.TYPE_ID = tipe.ERP_LOOKUP_VALUE_ID
                JOIN erp_lookup_value mr ON i.MEREK_ID = mr.ERP_LOOKUP_VALUE_ID
                JOIN price_list_detail b ON i.ITEM_ID = b.ITEM_ID 
                AND b.ACTIVE_FLAG = 'Y' 
                AND b.ENTERED_UOM = i.UOM_CODE 
            WHERE
                i.ACTIVE_FLAG = 'Y' 
                AND i.APPROVE_FLAG = 'Y' 
                AND i.TYPE_ID = FN_GET_VAR_VALUE ( 'INV' ) 
                AND i.JENIS_ID = FN_GET_VAR_VALUE ( 'GOODS' )
                AND NOT i.ITEM_ID = '{$item_finish_goods}'
            ORDER BY
                i.ITEM_CODE");

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
        $bom_id = $this->encrypt->decode($this->input->post('bom_id'));

        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME, b.MENU_ICON FROM bom a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.BOM_ID = {$bom_id}");

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

    public function get_item_finish_goods_ajax()
    {
        $search = $this->input->get('search');
        $data = $this->formula->search_item_finish_goods($search);
        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'id' => $row->ITEM_ID,
                'text' => "[" . $row->ITEM_CODE . "] - " . $row->ITEM_DESCRIPTION,
                'description' => $row->ITEM_DESCRIPTION,
                'note' => $row->NOTE ? $row->NOTE : ''
            ];
        }
        echo json_encode($result);
    }

    public function get_item_by_id()
    {
        $id = $this->input->get('id');
        $data = $this->db->query("
                SELECT 
                    ITEM_ID,
                    ITEM_CODE,
                    ITEM_DESCRIPTION,
                    NOTE
                FROM item
                WHERE ITEM_ID = ?
            ", [$id])->row();

        echo json_encode([
            'id' => $data->ITEM_ID,
            'text' => "[" . $data->ITEM_CODE . "] - " . $data->ITEM_DESCRIPTION,
            'description' => $data->ITEM_DESCRIPTION,
            'note' => $data->NOTE
        ]);
    }

    public function add()
    {
        try {
            // untuk fungsi validation callback HMVC
            $this->form_validation->CI = &$this;

            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('start_date', 'start date', 'trim|required');
            $this->form_validation->set_rules('end_date', 'end date', 'trim|required');
            $this->form_validation->set_rules('item_finish_goods', 'item finish goods', 'trim|required');
            if (!empty($this->input->post('item_finish_goods'))) {
                $this->form_validation->set_rules('satuan', 'satuan', 'trim|required');
            }

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah Formula';
                $data['breadcrumb'] = 'Tambah Formula';
                $data['detail'] = $this->input->post('detail');
                $this->template->load('template', 'formula/add', $data);
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail wajib diisi!');
                    redirect('formula/add');
                }

                $id_menu = $this->db->query("SELECT erp_menu_id FROM erp_menu WHERE erp_menu_name = '{$this->uri->segment('1')}'")->row();

                $erp_table_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_ID = {$id_menu->erp_menu_id}")->row();

                $seq = $this->db->query("SELECT SEQ($erp_table_id->ERP_TABLE_ID) AS SEQ1")->row();

                $post['seq'] = $seq->SEQ1;
                setVariableMysql();
                $new = $this->db->query("SELECT @NEW AS new")->row();
                $post['new'] = $new->new;

                $this->db->trans_begin();

                // ======================
                // INSERT DETAIL
                // ======================
                foreach ($detail['nama_item'] as $i => $val) {

                    $jumlah_raw  = $detail['jumlah'][$i] ?? null;

                    if (
                        $jumlah_raw === '' || $jumlah_raw === null || !is_numeric($jumlah_raw)
                    ) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" tidak boleh kosong');
                        redirect('formula/add');
                    }

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('formula/add');
                    }

                    $dataDetail = [
                        'BOM_ID'                => $post['seq'],
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'RKAP'                  => $jumlah,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'CREATED_BY'            => $this->session->userdata('id'),
                        'CREATED_DATE'          => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('bom_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('formula');
                    }
                }

                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'BOM_ID'                => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'START_DATE'            => $post['start_date'],
                    'END_DATE'              => $post['end_date'],
                    'DOCUMENT_REFF_NO'      => $post['no_reff'],
                    'ITEM_ID'               => $post['item_finish_goods'],
                    'ENTERED_QTY'           => '1',
                    'BASE_QTY'              => $post['base_qty'],
                    'UOM_CODE'              => $post['satuan'],
                    'NOTE'                  => $post['keterangan'],
                    'UNIT'                  => $post['unit'],
                    'LOKASI'                => $post['code'],
                    'ACTIVE_FLAG'           => $post['status'] == 'on' ? 'Y' : 'N',
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('bom', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('formula');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('formula');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('formula/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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

            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('start_date', 'start date', 'trim|required');
            $this->form_validation->set_rules('end_date', 'end date', 'trim|required');
            $this->form_validation->set_rules('item_finish_goods', 'item finish goods', 'trim|required');
            if (!empty($this->input->post('item_finish_goods'))) {
                $this->form_validation->set_rules('satuan', 'satuan', 'trim|required');
            }

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->formula->get_bom_id($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['data'] = $query->row();
                    $data['detail'] = $this->input->post('detail');
                    $this->template->load('template', 'formula/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('formula');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $bom_id   = $this->encrypt->decode($post['bom_id']);
                $detail = $post['detail'] ?? [];

                if (!$bom_id || empty($detail['nama_item'])) {
                    $this->session->set_flashdata('warning', 'Detail tidak boleh kosong dan wajib diisi!');
                    redirect('formula/detail/' . $id);
                }

                // ===============================
                // AMBIL DETAIL DB
                // ===============================
                $dbDetails = $this->db
                    ->select('BOM_DETAIL_ID')
                    ->from('bom_detail')
                    ->where('BOM_ID', $bom_id)
                    ->get()
                    ->result_array();

                $dbDetailIds = array_column($dbDetails, 'BOM_DETAIL_ID');

                // ===============================
                // AMBIL DETAIL POST
                // ===============================
                $postDetailIds = [];
                if (!empty($detail['bom_detail_id'])) {
                    foreach ($detail['bom_detail_id'] as $val) {
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

                    $jumlah = str_replace([','], '', $detail['jumlah'][$i]);

                    $bom_detail_id = !empty($detail['bom_detail_id'][$i])
                        ? $this->encrypt->decode($detail['bom_detail_id'][$i])
                        : null;

                    $dataDetail = [
                        'ITEM_ID'               => $detail['item_id'][$i],
                        'ENTERED_QTY'           => $jumlah,
                        'BASE_QTY'              => $detail['base_qty'][$i],
                        'RKAP'                  => $jumlah,
                        'ENTERED_UOM'           => $detail['satuan'][$i],
                        'ITEM_DESCRIPTION'      => $detail['nama_item'][$i],
                        'NOTE'                  => $detail['keterangan'][$i],
                        'BERAT'                 => $detail['berat'][$i],
                        'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                    ];

                    if ($bom_detail_id) {
                        // UPDATE
                        $this->db->update(
                            'bom_detail',
                            $dataDetail,
                            ['BOM_DETAIL_ID' => $bom_detail_id, 'BOM_ID' => $bom_id]
                        );
                    } else {
                        // INSERT
                        $dataDetail['BOM_ID']           = $bom_id;
                        $dataDetail['CREATED_BY']       = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE']     = date('Y-m-d H:i:s');

                        $this->db->insert('bom_detail', $dataDetail);

                        $error = $this->db->error();
                        if ($error['code'] != 0) {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                            redirect('formula/detail/' . $id);
                        }
                    }
                }

                // ===============================
                // DELETE DETAIL HILANG
                // ===============================
                if (!empty($deleteIds)) {
                    $this->db
                        ->where('BOM_ID', $bom_id)
                        ->where_in('BOM_DETAIL_ID', $deleteIds)
                        ->delete('bom_detail');
                }

                // ===============================
                // UPDATE HEADER
                // ===============================

                $this->db->update('bom', [
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'START_DATE'            => $post['start_date'],
                    'END_DATE'              => $post['end_date'],
                    'DOCUMENT_REFF_NO'      => $post['no_reff'],
                    'ITEM_ID'               => $post['item_finish_goods'],
                    'ENTERED_QTY'           => '1',
                    'BASE_QTY'              => $post['base_qty'],
                    'UOM_CODE'              => $post['satuan'],
                    'NOTE'                  => $post['keterangan'],
                    'UNIT'                  => $post['unit'],
                    'LOKASI'                => $post['code'],
                    'ACTIVE_FLAG'           => $post['status'] == 'on' ? 'Y' : 'N',
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ], ['BOM_ID' => $bom_id]);

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
                redirect('formula/detail/' . $id);
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

        $delResult = $this->formula->delete($id);
        if ($delResult !== true) {
            $this->db->trans_rollback();
            $this->_jsonError($delResult);
            return;
        }

        $updResult = $this->formula->updateStatus($id, $status);
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
                'message' => 'Gagal menghapus Formula!',
                'error'   => $db_error['message'],
                'code'    => $db_error['code']
            ]));
    }

    // public function get_info($id)
    // {
    //     $id = (int) $this->encrypt->decode(base64url_decode($id));
    //     $this->load->model('M_datatables', 'datatables');
    //     $params = [
    //         'table' => 'build_detail b',
    //         'select' => [
    //             'b.BUILD_DETAIL_ID, i.ITEM_DESCRIPTION Nama_Item, i.ITEM_CODE Kode_Item, b.ENTERED_UOM Satuan, b.ENTERED_QTY MR',
    //             ['(b.RECEIVED_ENTERED_QTY / b.BASE_QTY) AS PO', FALSE],
    //             ['(b.ENTERED_QTY - (b.RECEIVED_ENTERED_QTY / b.BASE_QTY)) AS SISA', FALSE],
    //         ],
    //         'joins' => [
    //             ['item i', 'b.ITEM_ID = i.ITEM_ID', 'inner'],
    //         ],
    //         'where' => ['b.BUILD_ID' => $id],
    //         'column_search' => ['i.ITEM_DESCRIPTION', 'i.ITEM_CODE', 'b.ENTERED_UOM', 'b.ENTERED_QTY'],
    //         'column_order'  => [null, null, 'i.ITEM_DESCRIPTION', 'i.ITEM_CODE', 'b.ENTERED_UOM', 'b.ENTERED_QTY', '(b.RECEIVED_ENTERED_QTY / b.BASE_QTY)', '(b.ENTERED_QTY - (b.RECEIVED_ENTERED_QTY / b.BASE_QTY))'],
    //         'order' => ['i.ITEM_DESCRIPTION' => 'asc'],
    //     ];

    //     echo json_encode($this->datatables->generate($params, function ($row, $no) {
    //         return [
    //             'no' => $no,
    //             'build_detail_id' => base64url_encode($this->encrypt->encode($row->BUILD_DETAIL_ID)),
    //             'nama_item' => $row->Nama_Item,
    //             'kode_item' => $row->Kode_Item,
    //             'satuan' => $row->Satuan,
    //             'mr' => number_format((float)$row->MR, 2, '.', ','),
    //             'po' => number_format((float)$row->PO, 2, '.', ','),
    //             'sisa' => number_format((float)$row->SISA, 2, '.', ','),
    //         ];
    //     }));
    // }

    // public function get_info_detail($detail_id)
    // {
    //     $detail_id = (int) $this->encrypt->decode(base64url_decode($detail_id));
    //     $this->load->model('M_datatables', 'datatables');
    //     $params = [
    //         'table' => 'build_detail b',
    //         'select' => [
    //             'c.DOCUMENT_NO No_Transaksi,c.DOCUMENT_DATE Tanggal,
    //                 b.ENTERED_UOM Satuan,w.WAREHOUSE_NAME S_Loc,c.INVOICE_ID',
    //             ['(a.ENTERED_QTY * b.BASE_QTY) Jumlah', FALSE],

    //         ],
    //         'joins' => [
    //             ['inventory_in_detail a', 'b.BUILD_DETAIL_ID = a.BUILD_DETAIL_ID', 'inner'],
    //             ['invoice_detail d', 'a.INVENTORY_IN_DETAIL_ID = d.INVENTORY_IN_DETAIL_ID', 'inner'],
    //             ['invoice c', 'd.INVOICE_ID = c.INVOICE_ID', 'inner'],
    //             ['warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID', 'inner'],
    //         ],
    //         'where' => ['b.BUILD_DETAIL_ID' => $detail_id],
    //         'order' => ['b.BUILD_DETAIL_ID' => 'asc'],
    //         'column_search' => ['c.DOCUMENT_NO', 'c.DOCUMENT_DATE', '(a.ENTERED_QTY * b.BASE_QTY)', 'b.ENTERED_UOM', 'w.WAREHOUSE_NAME'],
    //         'column_order'  => [null, 'c.DOCUMENT_NO', 'c.DOCUMENT_DATE', '(a.ENTERED_QTY * b.BASE_QTY)', 'b.ENTERED_UOM', 'w.WAREHOUSE_NAME'],
    //     ];
    //     echo json_encode($this->datatables->generate($params, function ($row, $no) {
    //         return [
    //             'no' => $no,
    //             'no_transaksi' => '<a href="' . site_url('po_kny/detail/' . base64url_encode($this->encrypt->encode($row->INVOICE_ID))) . '" target="_blank">' . $row->No_Transaksi . '</a>',
    //             'tanggal' => date('Y-m-d H:i', strtotime($row->Tanggal)),
    //             'satuan' => $row->Satuan,
    //             'jumlah' => number_format((float)$row->Jumlah, 2, '.', ','),
    //             's_loc' => $row->S_Loc,
    //         ];
    //     }));
    // }

    public function print($id)
    {
        $id     = (int) $this->encrypt->decode(base64url_decode($id));
        $formula    = $this->formula->get_formula_detail($id)->row();
        if ($formula) {
            $this->load->library('pdf');
            $data = [
                'dir_view' => 'formula/pdf',
                'data' => [
                    'formula' => $formula,
                    'formula_detail' => $this->formula->get_detail_by_bom_id($id)->result()
                ],
                'title' => str_replace('/', ' ', $formula->DOCUMENT_NO),
            ];
            $html = $this->load->view('template_pdf', $data, true);
            $this->pdf->generate($html, str_replace('/', ' ', $formula->DOCUMENT_NO), 'A4', 'portrait');
        }
    }
}
