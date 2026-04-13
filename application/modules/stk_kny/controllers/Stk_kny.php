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

        $order_column = 'KODE_ITEM';
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
        // HITUNG TOTAL
        // ==========================

        $total_stok = 0;

        foreach ($filtered_data as $row) {
            $total_stok  += (float) $row->STOK;
        }

        // ==========================
        // FORMAT UNTUK DATATABLE
        // ==========================
        $data = [];
        $no = $start;
        foreach ($paged_data as $row) {
            $no++;
            $data[] = [
                $no,
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
            "data" => $data,
            "summary" => [
                "total_stok" => number_format($total_stok, 2, '.', ','),
            ]
        ]);
    }

    // Ambil kolom dynamic untuk table
    public function get_columns()
    {
        // Ambil satu row untuk mendapatkan nama kolom dinamis
        $query = $this->db->query("CALL SP_REPORT_KONSINYASI_ALL_PIVOT_DYNAMIC()");
        $row = $query->row();

        $columns = [];
        if ($row) {
            foreach ($row as $key => $val) {
                if ($key !== 'NO') $columns[] = $key;
            }
        }

        echo json_encode($columns);
    }

    // Ambil data untuk DataTable
    public function get_data_summary()
    {
        $param = $this->input->post('param');
        $draw    = $this->input->post('draw') ?? 1;
        $start   = $this->input->post('start') ?? 0;
        $length  = $this->input->post('length') ?? 10;
        $order   = $this->input->post('order') ?? [];
        $columns_post = $this->input->post('columns') ?? [];

        $query = $this->db->query("CALL SP_REPORT_KONSINYASI_ALL_PIVOT_DYNAMIC()");
        $all_data = $query->result();

        $all_columns = [];
        if (!empty($all_data)) {
            $first = $all_data[0];
            foreach ($first as $key => $val) {
                $all_columns[] = $key;
            }
        }

        $column_map = [];
        $column_map[0] = null; // NO

        $idx = 1;
        foreach ($all_columns as $col) {
            $column_map[$idx] = $col;
            $idx++;
        }

        $filtered_data = array_filter($all_data, function ($row) use ($columns_post, $column_map) {

            foreach ($column_map as $idx => $col) {
                if ($col === null) continue;

                $search_val = $columns_post[$idx]['search']['value'] ?? '';

                if ($search_val !== '' && stripos((string)$row->$col, $search_val) === false) {
                    return false;
                }
            }

            return true;
        });

        $order_column = $param;
        $order_dir = 'asc';

        if (!empty($order)) {
            $col_index = $order[0]['column'];

            if ($col_index > 0) {
                $order_column = $column_map[$col_index] ?? $param;
            }

            $order_dir = $order[0]['dir'] ?? 'asc';
        }

        usort($filtered_data, function ($a, $b) use ($order_column, $order_dir) {

            $valA = $a->$order_column;
            $valB = $b->$order_column;

            if (is_numeric($valA) && is_numeric($valB)) {
                return ($order_dir === 'asc') ? ($valA <=> $valB) : ($valB <=> $valA);
            }

            return ($order_dir === 'asc')
                ? strcmp($valA, $valB)
                : strcmp($valB, $valA);
        });

        $paged_data = array_slice($filtered_data, $start, $length);

        $data = [];
        $no = $start;

        foreach ($paged_data as $row) {
            $no++;

            $row_obj = ['NO' => $no];

            foreach ($all_columns as $col) {
                $val = $row->$col;

                // ❗ JANGAN format number di sini
                $row_obj[$col] = is_numeric($val) ? (float)$val : $val;
            }

            $data[] = $row_obj;
        }

        $summary = [];

        foreach ($all_columns as $col) {
            $total = 0;

            foreach ($filtered_data as $row) {
                if (is_numeric($row->$col)) {
                    $total += $row->$col;
                }
            }

            $summary[strtolower(str_replace(' ', '_', $col))] = number_format($total, 2, '.', ',');
        }

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => count($all_data),
            "recordsFiltered" => count($filtered_data),
            "data" => $data,
            "summary" => $summary
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


        // ==========================
        // HITUNG TOTAL
        // ==========================
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
                $no,
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
        $query = $this->db->query("CALL SP_REPORT_KONSINYASI_ALL_PIVOT_DYNAMIC()");
        $all_data = $query->result();

        if (empty($all_data)) {
            echo "Tidak ada data";
            return;
        }

        $all_columns = array_keys((array)$all_data[0]);

        if (in_array('NAMA_ITEM', $all_columns)) {
            usort($all_data, function ($a, $b) {
                return strcmp($a->NAMA_ITEM, $b->NAMA_ITEM);
            });
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $header = ['No'];

        foreach ($all_columns as $col) {
            $header[] = str_replace('_', ' ', $col);
        }

        $sheet->fromArray([$header], null, 'A1');

        $rowNum = 2;
        $no = 1;

        foreach ($all_data as $row) {

            $row_array = [$no++];

            foreach ($all_columns as $col) {
                $val = $row->$col;

                // format angka
                if (is_numeric($val)) {
                    $val = number_format($val, 2, '.', ',');
                }

                $row_array[] = $val;
            }

            $sheet->fromArray($row_array, null, 'A' . $rowNum);
            $rowNum++;
        }

        $colIndex = 'A';
        for ($i = 0; $i < count($header); $i++) {
            $sheet->getColumnDimension($colIndex)->setAutoSize(true);
            $colIndex++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Summary_Stok.xlsx"');
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
