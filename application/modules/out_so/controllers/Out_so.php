<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Out_so extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Out_so_model', 'out_so');
    }

    public function index()
    {
        try {
            $data['title'] = 'Info OutStd (MR-SO)';
            $data['breadcrumb'] = 'Info OutStd (MR-SO)';
            $this->template->load('template', 'out_so/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $list = $this->out_so->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $outkny) {
            $no++;
            $row = array();
            $row['no'] = $no . '.';
            $row['tanggal'] = $outkny->DOCUMENT_DATE ? date('Y-m-d H:i', strtotime($outkny->DOCUMENT_DATE)) : '-';
            $row['no_transaksi'] = $outkny->DOCUMENT_NO ? $outkny->DOCUMENT_NO : '-';
            $row['no_referensi'] = $outkny->DOCUMENT_REFF_NO ? $outkny->DOCUMENT_REFF_NO : '-';
            $row['storage'] = $outkny->WAREHOUSE_NAME ? $outkny->WAREHOUSE_NAME : '-';
            $row['supplier'] = $outkny->PERSON_NAME ? $outkny->PERSON_NAME . " [" . $outkny->PERSON_CODE . "]" : '-';
            $row['nama_item'] = $outkny->ITEM_DESCRIPTION ? $outkny->ITEM_DESCRIPTION : '-';
            $row['kode_item'] = $outkny->ITEM_CODE ? $outkny->ITEM_CODE : '-';
            $row['qty_mr'] = $outkny->QTY_MR ? number_format($outkny->QTY_MR, 2, '.', ',') : '-';
            $row['qty_so'] = $outkny->QTY_SO ? number_format($outkny->QTY_SO, 2, '.', ',') : '-';
            $row['sisa'] = $outkny->QTY_SISA ? number_format($outkny->QTY_SISA, 2, '.', ',') : '-';
            $row['satuan'] = $outkny->ENTERED_UOM ? $outkny->ENTERED_UOM : '-';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->out_so->count_all(),
            "recordsFiltered" => $this->out_so->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function export()
    {
        $params = $this->input->get();
        $_POST_backup = $_POST;
        $_POST = $params; // pakai parameter dari DataTables

        $list = $this->out_so->get_datatables_export();

        $_POST = $_POST_backup; // kembalikan $_POST

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $header = ['No', 'Tanggal', 'No Transaksi', 'No Referensi', 'Storage', 'Customer', 'Nama Item', 'Kode Item', 'Qty MR', 'Qty SO', 'Sisa', 'Satuan'];
        $sheet->fromArray($header, NULL, 'A1');

        $row = 2;
        $no = 1;
        foreach ($list as $outSo) {
            $sheet->fromArray([
                $no++,
                $outSo->DOCUMENT_DATE ? date('Y-m-d H:i', strtotime($outSo->DOCUMENT_DATE)) : '-',
                $outSo->DOCUMENT_NO ?: '-',
                $outSo->DOCUMENT_REFF_NO ?: '-',
                $outSo->WAREHOUSE_NAME ?: '-',
                $outSo->PERSON_NAME . " [" . $outSo->PERSON_CODE . "]" ?: '-',
                $outSo->ITEM_DESCRIPTION ?: '-',
                $outSo->ITEM_CODE ?: '-',
                $outSo->QTY_MR ? number_format($outSo->QTY_MR, 2, '.', ',') : '-',
                $outSo->QTY_SO ? number_format($outSo->QTY_SO, 2, '.', ',') : '-',
                $outSo->QTY_SISA ? number_format($outSo->QTY_SISA, 2, '.', ',') : '-',
                $outSo->ENTERED_UOM ?: '-'
            ], NULL, 'A' . $row++);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export_out_so.xlsx"');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
