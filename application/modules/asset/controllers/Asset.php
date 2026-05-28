<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Asset_model', 'asset');
    }

    public function index()
    {
        try {
            $data['title']      = $this->access['PROMPT'];
            $data['breadcrumb'] = $this->access['PROMPT'];
            $this->template->load('template', $this->access['url'] . '/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->asset->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $asset) {
            $no++;
            $row = array();
            $row['no'] = $no;
            $row['kode_asset'] = '
            <a href="' . base_url('asset/detail/' . base64url_encode($this->encrypt->encode($asset->ASSET_ID))) . '">
                ' . ($asset->ASSET_CODE ? $asset->ASSET_CODE : '-') . '
            </a>';
            $row['nama_asset'] = $asset->ASSET_NAME ? $asset->ASSET_NAME : '-';
            $row['qty'] = $asset->ENTERED_QTY ? numb_format($asset->ENTERED_QTY) : '-';
            $row['metode_depresiasi'] = $asset->metode_depresiasi ? $asset->metode_depresiasi : '-';
            $row['nilai_asset'] = $asset->NILAI_ASSET ? numb_format($asset->NILAI_ASSET) : '-';
            $row['nilai_penyusutan'] = $asset->SUSUT_YEAR ? numb_format($asset->SUSUT_YEAR) : '-';
            $row['asset'] = $asset->COA_NAME ? $asset->COA_NAME : '-';
            $row['debet'] = $asset->name_debit ? $asset->name_debit : '-';
            $row['kredit'] = $asset->name_kredit ? $asset->name_kredit : '-';
            $row['intantangible_asset']        = $asset->INTANTANGIBLE_ASSET == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            $row['umur_asset'] = $asset->UMUR_ASSET ? numb_format($asset->UMUR_ASSET) : '-';
            $row['rate_depresiasi'] = $asset->RATE ? numb_format($asset->RATE) : '-';
            $row['tipe_asset'] = $asset->nama_tipe_asset ? $asset->nama_tipe_asset : '-';
            $row['aktif'] = $asset->BUYING_DATE ? date('d-M-Y', strtotime($asset->BUYING_DATE)) : '-';
            $row['non_aktif'] = $asset->USING_DATE ? date('d-M-Y', strtotime($asset->USING_DATE)) : '-';
            $row['active_flag']        = $asset->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>';
            $row['note'] = $asset->NOTE ? $asset->NOTE : '-';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->asset->count_all(),
            "recordsFiltered" => $this->asset->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add()
    {
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == false) {
                $data['title']      = 'Tambah Asset';
                $data['breadcrumb'] = 'Tambah Asset';
                $this->template->load('template', $this->access['url'] . '/add', $data);
            } else {
                $post = $this->input->post();

                $this->db->trans_start();
                $insert_id = $this->asset->add($post);
                $this->db->trans_complete();

                $encoded_id = base64url_encode($this->encrypt->encode($insert_id));

                if ($this->db->trans_status() === FALSE) {
                    $db_error = $this->db->error();
                    $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                    $this->session->set_flashdata('warning', 'Gagal menyimpan data!' . $error_msg);
                    redirect($this->access['url'] . '/add');
                } else {
                    $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                    redirect($this->access['url'] . '/detail/' . $encoded_id);
                }
            }
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    private function _validation_rules()
    {
        $this->form_validation->CI = &$this;
        if (!$this->input->post('id')) {
            $this->form_validation->set_rules('kode_asset', 'kode asset', 'trim|required|callback__check_unique_kode_asset');
        }
        $this->form_validation->set_rules('nama_asset', 'nama asset', 'trim|required');
        $this->form_validation->set_rules('qty', 'qty', 'trim|required');
        $this->form_validation->set_rules('metode_depresiasi', 'metode depresiasi', 'trim|required');
        $this->form_validation->set_rules('nilai_asset', 'nilai asset', 'trim|required');
        $this->form_validation->set_rules('nilai_penyusutan', 'nilai penyusutan', 'trim|required');
        $this->form_validation->set_rules('start_date', 'start date', 'trim|required');
        $this->form_validation->set_rules('asset', 'asset', 'trim|required');
        $this->form_validation->set_rules('debet', 'debet', 'trim|required');
        $this->form_validation->set_rules('kredit', 'kredit', 'trim|required');
        $this->form_validation->set_rules('umur_asset', 'umur asset', 'trim|required');
        $this->form_validation->set_rules('rate_depresiasi', 'rate depresiasi', 'trim|required');
        $this->form_validation->set_rules('tipe_asset', 'tipe asset', 'trim|required');
        $this->form_validation->set_rules('note', 'note', 'trim|required');
    }

    public function _check_unique_kode_asset($str)
    {
        $where = ['ASSET_CODE' => $str];
        if ($this->input->post('id')) {
            $decoded_id = (int) $this->encrypt->decode(base64url_decode($this->input->post('id')));
            $where['ASSET_ID !='] = $decoded_id;
        }
        $query = $this->db->get_where('asset', $where);
        if ($query->num_rows() > 0) {
            $this->form_validation->set_message('_check_unique_kode_asset', 'The {field} field must contain a unique value.');
            return FALSE;
        }
        return TRUE;
    }

    public function detail($id)
    {
        try {
            $this->_validation_rules();

            if ($this->form_validation->run() == FALSE) {
                $decoded_id = $this->encrypt->decode(base64url_decode($id));
                $query = $this->asset->getById($decoded_id);
                if ($query->num_rows() > 0) {
                    $data['title']      = 'Detail Asset';
                    $data['breadcrumb'] = 'Detail Asset';
                    $data['data']       = $query->row();
                    $this->template->load('template', $this->access['url'] . '/detail', $data);
                    $this->session->unset_userdata('warning');
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            } else {
                $decoded_id = $this->encrypt->decode(base64url_decode($this->input->post('id')));
                $query = $this->asset->getById($decoded_id);
                $encoded_id = base64url_encode($this->encrypt->encode($decoded_id));

                if ($query->num_rows() > 0) {
                    $this->db->trans_start();

                    $post = $this->input->post();
                    $this->asset->update_by_id($decoded_id, $post);

                    $this->db->trans_complete();
                    if ($this->db->trans_status() === FALSE) {
                        $db_error = $this->db->error();
                        $error_msg = (ENVIRONMENT !== 'production') ? ' - ' . $db_error['message'] : '';
                        $this->session->set_flashdata('warning', 'Gagal menyimpan data!' . $error_msg);
                        redirect($this->access['url'] . '/detail/' . $encoded_id);
                    } else {
                        $this->session->set_flashdata('success', 'Selamat anda berhasil menyimpan data!');
                        redirect($this->access['url'] . '/detail/' . $encoded_id);
                    }
                } else {
                    $this->session->set_flashdata('warning', 'Data tidak ditemukan!');
                    redirect($this->access['url']);
                }
            }
        } catch (Exception $err) {
            $this->db->trans_rollback();
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_metode_depresiasi()
    {
        $result = $this->asset->getMetodeDepresiasi()->result();
        echo json_encode($result);
    }

    public function get_asset()
    {
        $result = $this->asset->getAsset()->result();
        echo json_encode($result);
    }

    public function get_debet()
    {
        $result = $this->asset->getDebet()->result();
        echo json_encode($result);
    }

    public function get_kredit()
    {
        $result = $this->asset->getKredit()->result();
        echo json_encode($result);
    }

    public function get_tipe_asset()
    {
        $result = $this->asset->getTipeAsset()->result();
        echo json_encode($result);
    }
}
