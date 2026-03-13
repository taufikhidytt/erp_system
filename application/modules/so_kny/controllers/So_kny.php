<?php
defined('BASEPATH') or exit('No direct script access allowed');

class So_kny extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('So_kny_model', 'so_kny');
    }

    public function index()
    {
        try {
            $data['title'] = 'SO KNY';
            $data['breadcrumb'] = 'SO KNY';
            $this->template->load('template', 'so_kny/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->so_kny->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $so_kny) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['status'] = $so_kny->STATUS ? $so_kny->STATUS : '-';
            $row['no_transaksi'] = '
            <a href="' . base_url('so_kny/detail/' . base64url_encode($this->encrypt->encode($so_kny->SO_ID))) . '">
                ' . ($so_kny->No_Transaksi ? $so_kny->No_Transaksi : '-') . '
            </a>';
            $row['po_customer'] = $so_kny->PO_Customer ? $so_kny->PO_Customer : '-';
            $row['tanggal'] = $so_kny->Tanggal ? date('Y-m-d H:i', strtotime($so_kny->Tanggal)) : '-';
            $row['customer'] = $so_kny->Customer ? $so_kny->Customer : '-';
            $row['sales'] = $so_kny->Sales ? $so_kny->Sales : '-';
            $row['s_loc'] = $so_kny->S_Loc ? $so_kny->S_Loc : '-';
            $row['terms'] = $so_kny->Terms ? $so_kny->Terms : '-';
            $row['total'] = $so_kny->Total ? number_format($so_kny->Total, 2, '.', ',') : '-';

            $row['so_id'] = $this->encrypt->encode($so_kny->SO_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->so_kny->count_all(),
            "recordsFiltered" => $this->so_kny->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $so_id = $this->encrypt->decode($this->input->post('so_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->so_kny->count_detail_by_so_id($so_id);

            $list = $this->so_kny->get_detail_by_so_id($so_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"            => $no++,
                    "nama_item"     => $d->Nama_Item,
                    "kode_item"     => $d->Kode_Item,
                    "jumlah"        => number_format((float)$d->Qty, 2, '.', ''),
                    "satuan"        => $d->UoM,
                    "harga"         => number_format((float)$d->Harga, 2, '.', ''),
                    "diskon"        => number_format((float)$d->Diskon, 2, '.', ''),
                    "total"         => number_format((float)$d->Total, 2, '.', ''),
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
                a.PERSON_SITE_ID = '${customer}' 
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
            $customer = $this->input->post('customer');

            $data = $this->db->query("SELECT
                a.BUILD_ID,
                a.DOCUMENT_TYPE_ID,
                a.STATUS_ID,
                FN_GET_VAR_NAME ( a.STATUS_ID ) AS STATUS_NAME,
                a.DOCUMENT_NO,
                a.DOCUMENT_DATE,
                a.DOCUMENT_REFF_NO,
                psn.PERSON_ID,
                psn.PERSON_CODE,
                psn.PERSON_NAME,
                w.WAREHOUSE_ID,
                w.WAREHOUSE_NAME,
                i.ITEM_ID,
                i.ITEM_CODE,
                i.ITEM_DESCRIPTION,
                a.ENTERED_QTY,
                a.BASE_QTY,
            CASE
                    WHEN a.BASE_QTY IS NULL 
                    OR a.BASE_QTY = 0 THEN
                        a.ENTERED_QTY ELSE a.ENTERED_QTY - ( COALESCE ( a.RECEIVED_ENTERED_QTY, 0 ) / a.BASE_QTY ) 
                        END AS BALANCE,
                    a.ENTERED_UOM,
                    i.BERAT,
                    a.NOTE 
                FROM
                    build a
                    JOIN item i ON a.ITEM_ID = i.ITEM_ID
                    JOIN person psn ON a.PERSON_ID = psn.PERSON_ID
                    JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID 
                WHERE
                    a.ENTERED_QTY > 0 
                    AND a.BASE_QTY > 0 
                    AND COALESCE ( a.RECEIVED_ENTERED_QTY, 0 ) < a.ENTERED_QTY * a.BASE_QTY 
                    AND a.DOCUMENT_TYPE_ID = 3 
                    AND w.WAREHOUSE_ID = '{$storage}'
                    AND psn.PERSON_ID = '{$customer}'
                ORDER BY
                a.DOCUMENT_DATE DESC,
                a.BUILD_ID;
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

            $this->form_validation->set_rules('customer', 'customer', 'trim|required');
            $this->form_validation->set_rules('location_id', 'location', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('payment_term', 'payment term', 'trim|required');
            $this->form_validation->set_rules('jatuh_tempo', 'jatuh tempo', 'trim|required');
            $this->form_validation->set_rules('storage', 'storage', 'trim|required');
            $this->form_validation->set_rules('po_customer', 'po customer', 'trim|required');

            if ($this->form_validation->run() == false) {
                $data['title'] = 'Tambah SO KNY';
                $data['breadcrumb'] = 'Tambah SO KNY';
                $data['customer'] = $this->so_kny->get_customer();
                $data['storage'] = $this->so_kny->get_storage();
                $data['sales'] = $this->so_kny->get_sales();
                $data['payment_term'] = $this->so_kny->get_payment_term();
                $data['ppn_code'] = $this->so_kny->get_ppn_code();
                $data['detail'] = $this->input->post('detail');
                $this->template->load('template', 'so_kny/add', $data);
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $post = $this->input->post();
                $detail = isset($post['detail']) ? $post['detail'] : [];

                if (empty($detail) || empty($detail['nama_item']) || count(array_filter($detail['nama_item'])) == 0) {
                    $this->session->set_flashdata('warning', 'Detail wajib diisi!');
                    redirect('so_kny/add');
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
                        redirect('so_kny/add');
                    }

                    $jumlah  = (float) $jumlah_raw;
                    $balance = (float) $balance_raw;

                    // Tidak boleh <= 0
                    if ($jumlah <= 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" harus lebih dari 0');
                        redirect('so_kny/add');
                    }

                    // Tidak boleh melebihi balance
                    if ($jumlah > $balance) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', 'Jumlah "' . $detail['nama_item'][$i] . '" tidak boleh lebih besar dari balance (' . $balance . ')');
                        redirect('so_kny/add');
                    }

                    if (stripos($post['PPN_CODE'], 'INCL') !== false) {
                        $diskon_price = $detail['diskon_harga'][$i] / (1 + ($detail['diskon_persentase'][$i] / 100));
                    } else {
                        $diskon_price = $post['diskon_harga'][$i];
                    }

                    $dataDetail = [
                        'SO_ID'              => $post['seq'],
                        'ITEM_ID'            => $detail['item_id'][$i],
                        'ENTERED_QTY'        => $jumlah,
                        'BASE_QTY'           => $detail['base_qty'][$i],
                        'UNIT_PRICE'         => str_replace([','], '', $detail['harga'][$i]),
                        'DISCOUNT_PRICE'     => $diskon_price,
                        'SUBTOTAL'           => $detail['subtotal'][[$i]],
                        'ENTERED_UOM'        => $detail['satuan'][$i],
                        'GUDANG_ID'          => $post['storage'],
                        'KARYAWAN_ID'        => $post['sales'],
                        'BUILD_ID'           => $detail['build_id'][$i],
                        'DISCOUNT_PERCEN'    => $detail['diskon_persentase'][$i],
                        'HARGA_INPUT'        => str_replace([','], '', $detail['harga_input'][$i]),
                        'DISKON_INPUT'       => $detail['diskon_harga'][$i],
                        'ITEM_DESCRIPTION'   => $detail['nama_item'][$i],
                        'DESKRIPSI'          => $detail['memo'][$i],
                        'NOTE'               => $detail['keterangan'][$i],
                        'BERAT'              => $detail['berat'][$i],
                        'CREATED_BY'         => $this->session->userdata('id'),
                        'CREATED_DATE'       => date('Y-m-d H:i:s'),
                        'LAST_UPDATE_BY'     => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'   => date('Y-m-d H:i:s'),
                    ];

                    $this->db->insert('so_detail', $dataDetail);
                    $error = $this->db->error();
                    if ($error['code'] != 0) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('warning', "Error DB: " . $error['message']);
                        redirect('so_kny');
                    }
                }

                if (stripos($post['PPN_CODE'], 'INCL') !== false) {
                    $total_diskon_header = $post['TOTAL_DISKON_INPUT'] / (1 + ($post['PPN_PERCEN'] / 100));
                } else {
                    $total_diskon_header = $post['TOTAL_DISKON_INPUT'];
                }


                // ======================
                // INSERT PARENT / HEADER
                // ======================
                $dataHeader = [
                    'SO_ID'                 => $post['seq'],
                    'DOCUMENT_CLASS_CODE'   => $erp_table_id->PROMPT,
                    'DOCUMENT_TYPE_ID'      => $erp_table_id->TYPE_ID,
                    'STATUS_ID'             => $post['new'],
                    'STATUS_DOC_ID'         => $post['new'],
                    'DOCUMENT_DATE'         => $post['tanggal'],
                    'DOCUMENT_REFF_NO'      => $post['no_reff'],
                    'PO_NO'                 => $post['po_customer'],
                    'PERSON_ID'             => $post['customer'],
                    'PERSON_SITE_ID'        => $post['person_site_id'],
                    'WAREHOUSE_ID'          => $post['storage'],
                    'PAYMENT_TERM_ID'       => $post['payment_term'],
                    'JTEMPO'                => date('Y-m-d H:i:s', strtotime($post['jatuh_tempo'])),
                    'KARYAWAN_ID'           => $post['sales'],
                    'PPN_CODE'              => $post['PPN_CODE'],
                    'PPN_PERCEN'            => $post['PPN_PERCEN'],
                    'TOTAL_DISCOUNT_PERCEN' => $post['TOTAL_DISCOUNT_PERCEN'],
                    'TOTAL_DISKON_INPUT'    => $post['TOTAL_DISKON_INPUT'],

                    'TOTAL_DISCOUNT'        => $total_diskon_header,

                    'TOTAL_AMOUNT'          => $post['TOTAL_AMOUNT'],
                    'PPN_AMOUNT'            => $post['PPN_AMOUNT'],
                    'TOTAL_NET'             => $post['TOTAL_NET'],
                    'NOTE'                  => $post['keterangan'],
                    'CABANG_FLAG'           => 'Y',
                    'KONSINYASI_FLAG'       => 'Y',
                    'CREATED_BY'            => $this->session->userdata('id'),
                    'CREATED_DATE'          => date('Y-m-d H:i:s'),
                    'LAST_UPDATE_BY'        => $this->session->userdata('id'),
                    'LAST_UPDATE_DATE'      => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('so', $dataHeader);
                $error = $this->db->error();
                if ($error['code'] != 0) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', $error['message']);
                    redirect('so_kny');
                }

                // ======================
                // TRANSACTION CHECK
                // ======================
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!');
                    redirect('so_kny');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data dan detail baru!');
                    redirect('so_kny/detail/' . base64url_encode($this->encrypt->encode($post['seq'])));
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
            $this->form_validation->set_rules('location_id', 'location', 'trim|required');
            $this->form_validation->set_rules('tanggal', 'tanggal', 'trim|required');
            $this->form_validation->set_rules('payment_term', 'payment term', 'trim|required');
            $this->form_validation->set_rules('jatuh_tempo', 'jatuh tempo', 'trim|required');
            $this->form_validation->set_rules('storage', 'storage', 'trim|required');
            $this->form_validation->set_rules('po_customer', 'po customer', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                $id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->so_kny->get_so_id($id);
                if ($query->num_rows() > 0) {
                    $data['title'] = 'Detail';
                    $data['breadcrumb'] = 'Detail';
                    $data['customer'] = $this->so_kny->get_customer();
                    $data['storage'] = $this->so_kny->get_storage();
                    $data['sales'] = $this->so_kny->get_sales();
                    $data['payment_term'] = $this->so_kny->get_payment_term();
                    $data['ppn_code'] = $this->so_kny->get_ppn_code();
                    $data['data'] = $query->row();
                    $data['detail'] = $this->input->post('detail');
                    $this->template->load('template', 'so_kny/detail', $data);
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect('so_kny');
                }
            } else {
                date_default_timezone_set('Asia/Jakarta');
                // ===============================
                // AMBIL DATA POST
                // ===============================
                $post   = $this->input->post();
                $build_id   = $this->encrypt->decode($post['build_id']);
                $detail = $post['detail'] ?? [];
                debuging($post);

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
