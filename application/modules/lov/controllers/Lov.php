<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lov extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Lov_model', 'lov');
    }
    public function index()
    {
        try {
            $data['title'] = 'Daftar Nilai';
            $data['breadcrumb'] = 'Daftar Nilai';
            $this->template->load('template', 'lov/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->lov->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $lov) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['status'] = badge_status(strtoupper($lov->Status), $lov->Status == 'Editable' ? '#68e365' : '#f72b50');
            $row['set_name'] = '
            <a href="' . base_url('lov/detail/' . base64url_encode($this->encrypt->encode($lov->ERP_LOOKUP_SET_ID))) . '">
                ' . ($lov->ERP_LOOKUP_SET_NAME ? $lov->ERP_LOOKUP_SET_NAME : '-') . '
            </a>';
            $row['description'] = ($lov->DESCRIPTION ? $lov->DESCRIPTION : '-');

            $row['erp_lookup_set_id'] = $this->encrypt->encode($lov->ERP_LOOKUP_SET_ID);
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->lov->count_all(),
            "recordsFiltered" => $this->lov->count_filtered(),
            "data" => $data,
        );

        echo json_encode($output);
    }

    public function get_detail()
    {
        try {
            $erp_lookup_set_id = $this->encrypt->decode($this->input->post('erp_lookup_set_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->lov->count_detail_by_erp_lookup_set_id($erp_lookup_set_id);

            $list = $this->lov->get_detail_by_erp_lookup_set_id($erp_lookup_set_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"                => $no++,
                    "nilai"             => $d->DISPLAY_NAME ?? '-',
                    "note"              => $d->DESCRIPTION ?? '-',
                    "default"           => $d->PRIMARY_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Default" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No Default" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
                    "urutan"            => $d->SEQ ?? '-',
                    "sejak_tanggal"     => date('d M Y', strtotime($d->START_DATE)) ?? '-',
                    "sampai_tanggal"    => date('d M Y', strtotime($d->END_DATE)) ?? '-',
                    "aktif"             => $d->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>',
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

    public function getStatus()
    {
        $pr_id = $this->encrypt->decode($this->input->post('pr_id'));
        $data = $this->db->query("SELECT a.STATUS_ID, b.ITEM_FLAG, b.DISPLAY_NAME,b.MENU_ICON FROM pr a JOIN erp_lookup_value as b ON b.erp_lookup_value_id = a.STATUS_ID WHERE b.ERP_LOOKUP_SET_ID = FN_GET_VAR_SET ('STATUS_ORDER') AND a.PR_ID = {$pr_id}");
        if ($data->num_rows() > 0) {
            $rows = $data->result();
            foreach ($rows as $row) {
                $status_code = $this->db->query("SELECT FN_GET_VAR_CODE ($row->STATUS_ID) as status_code")->row();
                $row->status_code = $status_code->status_code;
                $row->badge_status = badge_status($row->DISPLAY_NAME, $row->MENU_ICON);
            }
            $result = array(
                'status' => 'sukses',
                'data' => $rows,
            );
        } else {
            $result = array(
                'status' => 'data tidak ditemukan',
                'data' => 'data tidak ditemukan'
            );
        }
        echo json_encode($result);
    }

    public function detail($id)
    {
        try {
            date_default_timezone_set('Asia/Jakarta');

            $encodedId = $id;
            $id = $this->encrypt->decode(base64url_decode($id));
            $query = $this->lov->get_erp_lookup_set_id($id);

            if ($query->num_rows() === 0) {
                $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                redirect('lov');
            }

            $dataLov = $query->row();

            if ($this->input->method() !== 'post') {
                $data['title'] = 'Detail';
                $data['breadcrumb'] = 'Detail';
                $data['data'] = $dataLov;
                $data['detail'] = $this->input->post('detail');
                $this->template->load('template', 'lov/detail', $data);
                return;
            }

            if (($dataLov->USER_CAN_EDIT_FLAG ?? 'N') !== 'Y') {
                $this->session->set_flashdata('warning', 'Data readonly dan tidak bisa diedit!');
                redirect('lov/detail/' . $encodedId);
            }

            $post = $this->input->post();
            $detail = json_decode($post['detail']??'[]', true);
            $canEdit = $dataLov->USER_CAN_EDIT_FLAG ?? 'N';

            if ($canEdit === 'Y') {
                $nilaiMap = [];
                $noteMap = [];
                $primaryCount = 0;

                foreach (($detail['nilai'] ?? []) as $i => $nilaiRaw) {
                    $nilai = trim($nilaiRaw);
                    $note = trim($detail['description'][$i] ?? '');
                    $programCode = strtoupper(trim($detail['program_code1'][$i] ?? $dataLov->PROGRAM_CODE));

                    if ($nilai === '' && $note === '') {
                        continue;
                    }

                    $nilaiKey = strtoupper($nilai);
                    $noteKey = strtoupper($note);

                    if ($nilai !== '' && isset($nilaiMap[$nilaiKey])) {
                        $this->session->set_flashdata('warning', 'Nilai detail tidak boleh duplikat!');
                        redirect('lov/detail/' . $encodedId);
                    }

                    if ($note !== '' && isset($noteMap[$noteKey])) {
                        $this->session->set_flashdata('warning', 'Note detail tidak boleh duplikat!');
                        redirect('lov/detail/' . $encodedId);
                    }

                    $nilaiMap[$nilaiKey] = true;
                    $noteMap[$noteKey] = true;

                    if (($detail['primary_flag'][$i] ?? 'N') === 'Y') {
                        $primaryCount++;
                    }

                    if ($programCode === 'GROUP' && strlen($note) !== 3) {
                        $this->session->set_flashdata('warning', 'Note untuk GROUP wajib 3 karakter!');
                        redirect('lov/detail/' . $encodedId);
                    }

                    if ($programCode === 'MEREK' && strlen($note) !== 4) {
                        $this->session->set_flashdata('warning', 'Note untuk MEREK wajib 4 karakter!');
                        redirect('lov/detail/' . $encodedId);
                    }

                    if ($programCode === 'TIPE' && strlen($nilai) !== 3) {
                        $this->session->set_flashdata('warning', 'Nilai untuk TIPE wajib 3 karakter!');
                        redirect('lov/detail/' . $encodedId);
                    }
                }

                if ($primaryCount > 1) {
                    $this->session->set_flashdata('warning', 'Default hanya boleh dipilih 1 baris!');
                    redirect('lov/detail/' . $encodedId);
                }
            }

            $this->db->trans_begin();
            setVariableMysql();
            $this->db->update('erp_lookup_set', [
                'DESCRIPTION' => $post['keterangan'] ?? null,
                'LAST_UPDATE_BY' => $this->session->userdata('id'),
                'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
            ], ['ERP_LOOKUP_SET_ID' => $id]);

            if ($canEdit === 'Y') {
                $deletedIds = $post['detail_deleted'] ?? [];
                foreach ($deletedIds as $deletedId) {
                    $detailId = $this->encrypt->decode($deletedId);
                    if ($detailId) {
                        $this->db->delete('erp_lookup_value', [
                            'ERP_LOOKUP_VALUE_ID' => $detailId,
                            'ERP_LOOKUP_SET_ID' => $id,
                        ]);
                    }
                }

                foreach (($detail['nilai'] ?? []) as $i => $nilai) {
                    $nilai = trim($nilai);
                    $note = trim($detail['description'][$i] ?? '');

                    if ($nilai === '' && $note === '') {
                        continue;
                    }

                    $detailId = !empty($detail['erp_lookup_value_id'][$i])
                        ? $this->encrypt->decode($detail['erp_lookup_value_id'][$i])
                        : null;

                    $dataDetail = [
                        'ERP_LOOKUP_SET_ID' => $id,
                        'DISPLAY_NAME'      => strtoupper($nilai),
                        'DESCRIPTION'       => strtoupper($note),
                        'SEQ'               => $detail['urutan'][$i] ?? ($i + 1),
                        'START_DATE'        => !empty($detail['start_date'][$i]) ? $detail['start_date'][$i] : null,
                        'END_DATE'          => !empty($detail['end_date'][$i]) ? $detail['end_date'][$i] : null,
                        'PRIMARY_FLAG'      => ($detail['primary_flag'][$i] ?? 'N') === 'Y' ? 'Y' : 'N',
                        'ACTIVE_FLAG'       => ($detail['active_flag'][$i] ?? 'N') === 'Y' ? 'Y' : 'N',
                        'LAST_UPDATE_BY'    => $this->session->userdata('id'),
                        'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
                    ];

                    if ($detailId) {
                        $this->db->update('erp_lookup_value', $dataDetail, [
                            'ERP_LOOKUP_VALUE_ID' => $detailId,
                            'ERP_LOOKUP_SET_ID' => $id,
                        ]);
                    } else {
                        $dataDetail['CREATED_BY'] = $this->session->userdata('id');
                        $dataDetail['CREATED_DATE'] = date('Y-m-d H:i:s');
                        $this->db->insert('erp_lookup_value', $dataDetail);
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('warning', 'Gagal memperbarui daftar nilai!');
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('success', 'Daftar nilai berhasil diperbarui!');
            }

            redirect('lov/detail/' . $encodedId);
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function print($id)
    {
        checkAccess('print_out');
        $id     = (int) $this->encrypt->decode(base64url_decode($id));
        $fpk    = $this->fpk->get_fpk_detail($id)->row();
        if ($fpk) {
            $this->load->library('pdf');
            $data = [
                'dir_view' => 'fpk/pdf',
                'data' => [
                    'fpk' => $fpk,
                    'fpk_detail' => $this->fpk->get_detail_by_pr_id($id)->result()
                ],
                'title' => str_replace('/', ' ', $fpk->DOCUMENT_NO),
            ];
            $html = $this->load->view('template_pdf', $data, true);
            $this->pdf->generate($html, str_replace('/', ' ', $fpk->DOCUMENT_NO), 'A4', 'portrait');
        }
    }
}
