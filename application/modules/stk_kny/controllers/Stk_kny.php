<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Stk_kny extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Stk_kny_model', 'stk_kny');
    }

    public function index()
    {
        try {
            $data['title'] = 'Info Stok Konsinyasi';
            $data['breadcrumb'] = 'Info Stok Konsinyasi';
            $data['gudang'] = $this->stk_kny->getGudang();
            $data['item'] = $this->stk_kny->getItem();
            $data['period'] = $this->stk_kny->getPeriod();
            $this->template->load('template', 'stk_kny/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_data()
    {
        $warehouse_id = $this->input->post('gudang');
        $check_gudang = $this->input->post('check_gudang');

        if ($check_gudang == 'true') {
            $warehouse_id = "";
        }

        $draw    = $this->input->post('draw') ?? 1;
        $start   = $this->input->post('start') ?? 0;
        $length  = $this->input->post('length') ?? 10;
        $order   = $this->input->post('order') ?? [];
        $columns = $this->input->post('columns') ?? [];

        $column_map = [
            1 => 'NAMA_ITEM',
            2 => 'KODE_ITEM',
            3 => 'STOK',
            4 => 'SATUAN',
            5 => 'PRICE',
            6 => 'NILAI'
        ];

        $order_column = 'NAMA_ITEM';
        $order_dir    = 'asc';
        if (!empty($order) && isset($order[0]['column'])) {
            $col_index = $order[0]['column'];
            $order_column = $column_map[$col_index] ?? 'NAMA_ITEM';
            $order_dir    = $order[0]['dir'] ?? 'asc';
        }

        // ==========================
        // COLUMN SEARCH
        // ==========================
        $search = [];
        foreach ($column_map as $idx => $col_name) {
            $search[$col_name] = $columns[$idx]['search']['value'] ?? '';
        }

        // ==========================
        // AMBIL DATA DARI STORED PROCEDURE
        // ==========================
        if ($check_gudang == 'true') {
            $query = $this->db->query("CALL SP_REPORT_KONSINYASI_ALL()");
        } else {
            $query = $this->db->query("CALL SP_REPORT_KONSINYASI_WAREHOUSE(?)", [$warehouse_id]);
        }

        $all_data = $query->result();

        // ==========================
        // FILTER DATA DI PHP (Per Column)
        // ==========================
        $filtered_data = array_filter($all_data, function ($row) use ($search) {
            foreach ($search as $col => $value) {
                if ($value !== '' && stripos((string)$row->$col, $value) === false) {
                    return false;
                }
            }
            return true;
        });

        // ==========================
        // SORTING DI PHP
        // ==========================
        usort($filtered_data, function ($a, $b) use ($order_column, $order_dir) {
            if ($a->$order_column == $b->$order_column) return 0;
            return ($order_dir === 'asc') ? ($a->$order_column <=> $b->$order_column) : ($b->$order_column <=> $a->$order_column);
        });

        // ==========================
        // PAGINATION DI PHP
        // ==========================
        $paged_data = array_slice($filtered_data, $start, $length);

        // ==========================
        // FORMAT UNTUK DATATABLE
        // ==========================
        $data = [];
        $no = $start;
        foreach ($paged_data as $row) {
            $no++;
            $data[] = [
                $no . ".",
                $row->NAMA_ITEM,
                $row->KODE_ITEM,
                number_format($row->STOK, 2, '.', ','),
                $row->SATUAN,
                number_format($row->PRICE, 2, '.', ','),
                number_format($row->NILAI, 2, '.', ','),
            ];
        }

        // ==========================
        // OUTPUT JSON
        // ==========================
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => count($all_data),
            "recordsFiltered" => count($filtered_data),
            "data" => $data
        ]);
    }

    public function get_data_summary()
    {
        $draw    = $this->input->post('draw') ?? 1;
        $start   = $this->input->post('start') ?? 0;
        $length  = $this->input->post('length') ?? 10;
        $order   = $this->input->post('order') ?? [];
        $columns = $this->input->post('columns') ?? [];

        $column_map = [
            1 => 'NAMA_ITEM',
            2 => 'KODE_ITEM',
            3 => 'SATUAN',
            4 => 'PUSAT',
            5 => 'PINJAM',
            6 => 'UNCOMPLETE',
            7 => 'BOOKED',
            8 => 'KONS_JIA',
            9 => 'KONS_BBI',
            10 => 'KONS_BKI',
            11 => 'KONS_HSS',
            12 => 'KONS_THC',
            13 => 'KONS_GD_HYDRAULIC',
            14 => 'KONS_TFP',
            15 => 'TEKNIKAL',
            16 => 'KONS_GUN',
            17 => 'KONS_HS',
            18 => 'KONS_NHC',
            19 => 'KONS_SAMPIT',
            20 => 'NEGLASARI',
            21 => 'TOTAL_STOK',
            22 => 'PRICE',
            23 => 'NILAI',
        ];

        $order_column = 'NAMA_ITEM';
        $order_dir    = 'asc';
        if (!empty($order) && isset($order[0]['column'])) {
            $col_index = $order[0]['column'];
            $order_column = $column_map[$col_index] ?? 'NAMA_ITEM';
            $order_dir    = $order[0]['dir'] ?? 'asc';
        }

        // ==========================
        // COLUMN SEARCH
        // ==========================
        $search = [];
        foreach ($column_map as $idx => $col_name) {
            $search[$col_name] = $columns[$idx]['search']['value'] ?? '';
        }

        // ==========================
        // AMBIL DATA DARI STORED PROCEDURE
        // ==========================
        $query = $this->db->query("CALL SP_REPORT_KONSINYASI_ALL_PIVOT_DYNAMIC()");

        $all_data = $query->result();

        // ==========================
        // FILTER DATA DI PHP (Per Column)
        // ==========================
        $filtered_data = array_filter($all_data, function ($row) use ($search) {
            foreach ($search as $col => $value) {
                if ($value !== '' && stripos((string)$row->$col, $value) === false) {
                    return false;
                }
            }
            return true;
        });

        // ==========================
        // SORTING DI PHP
        // ==========================
        usort($filtered_data, function ($a, $b) use ($order_column, $order_dir) {
            if ($a->$order_column == $b->$order_column) return 0;
            return ($order_dir === 'asc') ? ($a->$order_column <=> $b->$order_column) : ($b->$order_column <=> $a->$order_column);
        });

        // ==========================
        // PAGINATION DI PHP
        // ==========================
        $paged_data = array_slice($filtered_data, $start, $length);

        // ==========================
        // FORMAT UNTUK DATATABLE
        // ==========================
        $data = [];
        $no = $start;
        foreach ($paged_data as $row) {
            $no++;
            $data[] = [
                $no . ".",
                $row->NAMA_ITEM,
                $row->KODE_ITEM,
                $row->SATUAN,
                number_format($row->PUSAT, 2, '.', ','),
                number_format($row->PINJAM, 2, '.', ','),
                number_format($row->UNCOMPLETE, 2, '.', ','),
                number_format($row->BOOKED, 2, '.', ','),
                number_format($row->KONS_JIA, 2, '.', ','),
                number_format($row->KONS_BBI, 2, '.', ','),
                number_format($row->KONS_BKI, 2, '.', ','),
                number_format($row->KONS_HSS, 2, '.', ','),
                number_format($row->KONS_THC, 2, '.', ','),
                number_format($row->KONS_GD_HYDRAULIC, 2, '.', ','),
                number_format($row->KONS_TFP, 2, '.', ','),
                number_format($row->TEKNIKAL, 2, '.', ','),
                number_format($row->KONS_GUN, 2, '.', ','),
                number_format($row->KONS_HS, 2, '.', ','),
                number_format($row->KONS_NHC, 2, '.', ','),
                number_format($row->KONS_SAMPIT, 2, '.', ','),
                number_format($row->NEGLASARI, 2, '.', ','),
                number_format($row->TOTAL_STOK, 2, '.', ','),
                number_format($row->PRICE, 2, '.', ','),
                number_format($row->NILAI, 2, '.', ','),
            ];
        }

        // ==========================
        // OUTPUT JSON
        // ==========================
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => count($all_data),
            "recordsFiltered" => count($filtered_data),
            "data" => $data
        ]);
    }

    public function get_data_kartu_stok()
    {
        $item = $this->input->post('item');
        $period = $this->input->post('period');
        $warehouse_id = $this->input->post('gudang_kartu_stok');
        $check_gudang = $this->input->post('check_gudang_kartu_stok');

        if ($check_gudang === "true") {
            $warehouse_id = "";
        }

        $draw    = $this->input->post('draw') ?? 1;
        $start   = $this->input->post('start') ?? 0;
        $length  = $this->input->post('length') ?? 10;
        $order   = $this->input->post('order') ?? [];
        $columns = $this->input->post('columns') ?? [];

        $column_map = [
            1 => 'DOCUMENT_DATE',
            2 => 'DOCUMENT_NO',
            3 => 'DOCUMENT_REFF_NO',
            4 => 'PERSON_NAME',
            5 => 'JENIS_TRANSAKSI',
            6 => 'MASUK',
            7 => 'KELUAR',
            8 => 'SALDO',
            9 => 'WAREHOUSE_NAME',
            10 => 'NOTE',
        ];

        $order_column = 'DOCUMENT_DATE';
        $order_dir    = 'asc';
        if (!empty($order) && isset($order[0]['column'])) {
            $col_index = $order[0]['column'];
            $order_column = $column_map[$col_index] ?? 'DOCUMENT_DATE';
            $order_dir    = $order[0]['dir'] ?? 'asc';
        }

        // ==========================
        // COLUMN SEARCH
        // ==========================
        $search = [];
        foreach ($column_map as $idx => $col_name) {
            $search[$col_name] = $columns[$idx]['search']['value'] ?? '';
        }

        // ==========================
        // AMBIL DATA DARI STORED PROCEDURE
        // ==========================
        if ($warehouse_id == "") {
            $query = $this->db->query("CALL SP_REPORT_KONSINYASI_KARTU_STOK_ALL(?,?)", [$item, $period]);
        } else {
            $query = $this->db->query("CALL SP_REPORT_KONSINYASI_KARTU_STOK(?,?,?)", [$item, $warehouse_id, $period]);
        }

        $all_data = $query->result();

        // ==========================
        // FILTER DATA DI PHP (Per Column)
        // ==========================
        $filtered_data = array_filter($all_data, function ($row) use ($search) {
            foreach ($search as $col => $value) {
                if ($value !== '' && stripos((string)$row->$col, $value) === false) {
                    return false;
                }
            }
            return true;
        });

        // ==========================
        // SORTING DI PHP
        // ==========================
        usort($filtered_data, function ($a, $b) use ($order_column, $order_dir) {
            if ($a->$order_column == $b->$order_column) return 0;
            return ($order_dir === 'asc') ? ($a->$order_column <=> $b->$order_column) : ($b->$order_column <=> $a->$order_column);
        });

        // ==========================
        // PAGINATION DI PHP
        // ==========================
        $paged_data = array_slice($filtered_data, $start, $length);

        $total_masuk = 0;
        $total_keluar = 0;

        foreach ($filtered_data as $row) {
            $total_masuk  += (float) $row->MASUK;
            $total_keluar += (float) $row->KELUAR;
        }
        $stok_akhir = $total_masuk - $total_keluar;

        // ==========================
        // FORMAT UNTUK DATATABLE
        // ==========================
        $data = [];
        $no = $start;
        foreach ($paged_data as $row) {
            $no++;
            $data[] = [
                $no . ".",
                $row->DOCUMENT_DATE ?? "-",
                $row->DOCUMENT_NO ?? "-",
                $row->DOCUMENT_REFF_NO ?? "-",
                $row->PERSON_NAME ?? "-",
                $row->JENIS_TRANSAKSI ?? "-",
                number_format($row->MASUK, 2, '.', ',') ?? "-",
                number_format($row->KELUAR, 2, '.', ',') ?? "-",
                number_format($row->SALDO, 2, '.', ',') ?? "-",
                $row->WAREHOUSE_NAME ?? "-",
                $row->NOTE ?? "-",
            ];
        }

        // ==========================
        // OUTPUT JSON
        // ==========================
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => count($all_data),
            "recordsFiltered" => count($filtered_data),
            "data" => $data,
            "summary" => [
                "masuk" => number_format($total_masuk, 2, '.', ','),
                "keluar" => number_format($total_keluar, 2, '.', ','),
                "stok_akhir" => number_format($stok_akhir, 2, '.', ',')
            ]
        ]);
    }

    public function export_excel_stok()
    {
        $warehouse_id = $this->input->get('gudang');
        $check_gudang = $this->input->get('check_gudang');

        if ($check_gudang == 'true') {
            $warehouse_id = "";
        }

        // Ambil semua data sesuai filter (tanpa pagination)
        if ($check_gudang == 'true') {
            $query = $this->db->query("CALL SP_REPORT_KONSINYASI_ALL()");
        } else {
            $query = $this->db->query("CALL SP_REPORT_KONSINYASI_WAREHOUSE(?)", [$warehouse_id]);
        }

        $all_data = $query->result();

        usort($all_data, function ($a, $b) {
            return strcmp($a->NAMA_ITEM, $b->NAMA_ITEM);
        });

        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->fromArray([
            ['No', 'Nama Item', 'Kode Item', 'Stok', 'Satuan', 'Price', 'Nilai']
        ], null, 'A1');

        // Data
        $rowNum = 2;
        $no = 1;
        foreach ($all_data as $row) {
            $sheet->fromArray([
                $no++,
                $row->NAMA_ITEM,
                $row->KODE_ITEM,
                number_format($row->STOK, 2, '.', ','),
                $row->SATUAN,
                number_format($row->PRICE, 2, '.', ','),
                number_format($row->NILAI, 2, '.', ','),
            ], null, 'A' . $rowNum);
            $rowNum++;
        }

        // Kirim file ke browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Stok.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function export_excel_summary_stok()
    {
        // Ambil semua data sesuai filter (tanpa pagination)
        $query = $this->db->query("CALL SP_REPORT_KONSINYASI_ALL_PIVOT_DYNAMIC()");

        $all_data = $query->result();

        usort($all_data, function ($a, $b) {
            return strcmp($a->NAMA_ITEM, $b->NAMA_ITEM);
        });

        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->fromArray([
            ['No', 'Nama Item', 'Kode Item', 'Satuan', 'Pusat', 'Pinjam', 'Uncomplete', 'Booked', 'Kons JIA', 'Kons BBI', 'Kons BKI', 'Kons HSS', 'Kons THC', 'Kons GD Hydraulic', 'Kons TFP', 'Teknikal', 'Kons GUN', 'KONS HS', 'Kons NHC', 'Kons Sampit', 'Kons Neglasari', 'Total Stok', 'Price', 'Nilai']
        ], null, 'A1');

        // Data
        $rowNum = 2;
        $no = 1;
        foreach ($all_data as $row) {
            $sheet->fromArray([
                $no++,
                $row->NAMA_ITEM,
                $row->KODE_ITEM,
                $row->SATUAN,
                number_format($row->PUSAT, 2, '.', ','),
                number_format($row->PINJAM, 2, '.', ','),
                number_format($row->UNCOMPLETE, 2, '.', ','),
                number_format($row->BOOKED, 2, '.', ','),
                number_format($row->KONS_JIA, 2, '.', ','),
                number_format($row->KONS_BBI, 2, '.', ','),
                number_format($row->KONS_BKI, 2, '.', ','),
                number_format($row->KONS_HSS, 2, '.', ','),
                number_format($row->KONS_THC, 2, '.', ','),
                number_format($row->KONS_GD_HYDRAULIC, 2, '.', ','),
                number_format($row->KONS_TFP, 2, '.', ','),
                number_format($row->TEKNIKAL, 2, '.', ','),
                number_format($row->KONS_GUN, 2, '.', ','),
                number_format($row->KONS_HS, 2, '.', ','),
                number_format($row->KONS_NHC, 2, '.', ','),
                number_format($row->KONS_SAMPIT, 2, '.', ','),
                number_format($row->NEGLASARI, 2, '.', ','),
                number_format($row->TOTAL_STOK, 2, '.', ','),
                number_format($row->PRICE, 2, '.', ','),
                number_format($row->NILAI, 2, '.', ','),
            ], null, 'A' . $rowNum);
            $rowNum++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Summary Stok.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function export_excel_kartu_stok()
    {
        $item = $this->input->get('item');
        $period = $this->input->get('period');
        $warehouse_id = $this->input->get('gudang_kartu_stok');
        $check_gudang = $this->input->get('check_gudang_kartu_stok');

        if ($check_gudang === "true") {
            $warehouse_id = "";
        }

        // Ambil semua data sesuai filter (tanpa pagination)
        if ($warehouse_id == "") {
            $query = $this->db->query("CALL SP_REPORT_KONSINYASI_KARTU_STOK_ALL(?,?)", [$item, $period]);
        } else {
            $query = $this->db->query("CALL SP_REPORT_KONSINYASI_KARTU_STOK(?,?,?)", [$item, $warehouse_id, $period]);
        }

        $all_data = $query->result();

        usort($all_data, function ($a, $b) {
            return strcmp($a->DOCUMENT_DATE, $b->DOCUMENT_DATE);
        });

        // Load PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Excel
        $sheet->fromArray([
            ['No', 'Tanggal', 'No Transaksi', 'No Reff', 'Supplier', 'Transaksi', 'Masuk', 'Keluar', 'Stok', 'Gudang', 'Note']
        ], null, 'A1');

        // Data
        $rowNum = 2;
        $no = 1;
        foreach ($all_data as $row) {
            $sheet->fromArray([
                $no++,
                $row->DOCUMENT_DATE ?? "-",
                $row->DOCUMENT_NO ?? "-",
                $row->DOCUMENT_REFF_NO ?? "-",
                $row->PERSON_NAME ?? "-",
                $row->JENIS_TRANSAKSI ?? "-",
                number_format($row->MASUK, 2, '.', ',') ?? "-",
                number_format($row->KELUAR, 2, '.', ',') ?? "-",
                number_format($row->SALDO, 2, '.', ',') ?? "-",
                $row->WAREHOUSE_NAME ?? "-",
                $row->NOTE ?? "-",
            ], null, 'A' . $rowNum);
            $rowNum++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Kartu Stok.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
