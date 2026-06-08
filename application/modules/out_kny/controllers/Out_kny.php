<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Out_kny extends Back_Controller
{
    public function __construct()
    {
        parent::__construct();
        belum_login();
        rules();
        $this->load->model('Out_kny_model', 'out_kny');
    }

    public function index()
    {
        try {
            $data['title'] = 'Info Konsinyasi';
            $data['breadcrumb'] = 'Info Konsinyasi';
            $this->template->load('template', 'out_kny/index', $data);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function get_supplier()
    {
        $result = $this->out_kny->getSupplier()->result();
        echo json_encode($result);
    }

    public function get_data_fpk()
    {
        $supplier        = $this->input->post('supplier');
        $check_supplier  = $this->input->post('check_supplier');

        $status          = $this->input->post('status');
        $check_status    = $this->input->post('check_status');

        $daterange       = $this->input->post('daterange');
        $check_period    = $this->input->post('check_period');

        // ==========================
        // BOOLEAN CHECKBOX
        // ==========================
        $check_supplier = ($check_supplier === 'true' || $check_supplier === '1');
        $check_status   = ($check_status === 'true' || $check_status === '1');
        $check_period   = ($check_period === 'true' || $check_period === '1');

        // ==========================
        // SUPPLIER LOGIC
        // ==========================
        $person_id = null;

        if (!$check_supplier && !empty($supplier)) {
            $person_id = $supplier;
        }

        // ==========================
        // STATUS LOGIC
        // ==========================
        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED'
        ];

        $status_code = $status_map[$status] ?? $status;

        // jika ALL dicentang
        if ($check_status) {
            $status_code = null;
        }

        // ==========================
        // PERIOD LOGIC
        // ==========================
        $start_date = null;
        $end_date   = null;

        // jika ALL periode tidak dicentang
        if (!$check_period && !empty($daterange)) {

            $date = explode(' - ', $daterange);

            $start_date = trim($date[0] ?? null);
            $end_date   = trim($date[1] ?? null);

            if ($start_date == '') {
                $start_date = null;
            }

            if ($end_date == '') {
                $end_date = null;
            }
        }

        // ==========================
        // DATATABLE PARAM
        // ==========================
        $draw   = $this->input->post('draw') ?? 1;
        $start  = (int) ($this->input->post('start') ?? 0);
        $length = (int) ($this->input->post('length') ?? 10);

        $order   = $this->input->post('order') ?? [];
        $columns = $this->input->post('columns') ?? [];

        $column_map = [
            0  => null,
            1  => null,
            2  => 'b.DISPLAY_NAME',
            3  => 'a.DOCUMENT_NO',
            4  => 'a.DOCUMENT_REFF_NO',
            5  => 'a.DOCUMENT_DATE',
            6  => 'a.NEED_DATE',
            7  => 'p.PERSON_NAME',
            8  => 'w.WAREHOUSE_NAME',
            9  => 'k.FIRST_NAME',
            10 => 'a.TOTAL_AMOUNT',
            11 => 'a.PERIOD_NAME',
            12 => 'a.NOTE',
            13 => 'e.ERP_USER_NAME'
        ];

        $order_column = 'a.DOCUMENT_DATE';
        $order_dir    = 'DESC';

        if (!empty($order)) {

            $col_index = (int) $order[0]['column'];

            if (isset($column_map[$col_index]) && $column_map[$col_index] !== null) {
                $order_column = $column_map[$col_index];
            }

            $order_dir = strtoupper($order[0]['dir']);

            if (!in_array($order_dir, ['ASC', 'DESC'])) {
                $order_dir = 'DESC';
            }
        }

        $params = [
            $person_id,
            $person_id,

            $start_date,
            $start_date,

            $end_date,
            $end_date,

            $status_code,
            $status_code,
            $status_code
        ];

        // ==========================
        // MAIN SQL
        // ==========================
        $sql = "
            SELECT DISTINCT
                a.PR_ID,
                a.PERSON_ID,
                a.WAREHOUSE_ID,
                a.KARYAWAN_ID,
                a.CREATED_BY,
                b.DISPLAY_NAME AS Status,
                b.MENU_ICON Warna_Status,
                a.DOCUMENT_NO AS `No Transaksi`,
                a.DOCUMENT_REFF_NO AS `No Referensi`,
                a.DOCUMENT_DATE AS `Tanggal`,
                a.NEED_DATE AS `Dibutuhkan`,
                CONCAT('[', p.PERSON_CODE, '] - ', p.PERSON_NAME) AS Supplier,
                w.WAREHOUSE_NAME AS Storage,
                k.FIRST_NAME AS Sales,
                a.TOTAL_AMOUNT AS Total,
                a.NOTE AS Note,
                a.PERIOD_NAME AS Periode,
                e.ERP_USER_NAME AS `Created By`

            FROM pr a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN person p ON a.PERSON_ID = p.PERSON_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID
            JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID

            WHERE TRUE

            AND a.DOCUMENT_TYPE_ID = 3

            AND (
                ? IS NULL
                OR a.PERSON_ID = ?
            )

            AND (
                ? IS NULL
                OR DATE(a.DOCUMENT_DATE) >= ?
            )

            AND (
                ? IS NULL
                OR DATE(a.DOCUMENT_DATE) <= ?
            )

            AND (
                ? IS NULL
                OR ? = ''
                OR EXISTS (
                    SELECT 1
                    FROM ERP_LOOKUP_VALUE elv
                    WHERE elv.ERP_LOOKUP_VALUE_ID = a.STATUS_ID
                    AND FIND_IN_SET(elv.PROGRAM_CODE1, ?)
                )
            )
        ";

        foreach ($column_map as $index => $field) {

            $search_value = trim($columns[$index]['search']['value'] ?? '');

            if ($search_value === '') continue;

            // =========================
            // DOCUMENT_DATE (INDEX 4)
            // =========================
            if ($index == 5) {

                $sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
                $params[] = $search_value;
            }

            // =========================
            // NEED_DATE / DIBUTUHKAN (INDEX 5)
            // =========================
            elseif ($index == 6) {

                $sql .= " AND DATE(a.NEED_DATE) = ? ";
                $params[] = $search_value;
            }

            // =========================
            // OTHER COLUMNS
            // =========================
            else {

                $sql .= " AND {$field} LIKE ? ";
                $params[] = "%{$search_value}%";
            }
        }

        $sql .= "
                    ORDER BY {$order_column} {$order_dir}
                    LIMIT {$start}, {$length}
                ";

        // ==========================
        // EXECUTE QUERY
        // ==========================
        $query = $this->db->query($sql, $params);

        $data_result = $query->result();

        // ==========================
        // COUNT TOTAL (WITHOUT LIMIT)
        // ==========================
        $count_sql = "
                    SELECT COUNT(DISTINCT a.PR_ID) AS total

                    FROM pr a
                    JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
                    JOIN person p ON a.PERSON_ID = p.PERSON_ID
                    JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
                    JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID
                    JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID

                    WHERE TRUE

                    AND a.DOCUMENT_TYPE_ID = 3

                    AND (
                        ? IS NULL
                        OR a.PERSON_ID = ?
                    )

                    AND (
                        ? IS NULL
                        OR a.DOCUMENT_DATE >= ?
                    )

                    AND (
                        ? IS NULL
                        OR a.DOCUMENT_DATE <= ?
                    )

                    AND (
                        ? IS NULL
                        OR ? = ''
                        OR EXISTS (
                            SELECT 1
                            FROM ERP_LOOKUP_VALUE elv
                            WHERE elv.ERP_LOOKUP_VALUE_ID = a.STATUS_ID
                            AND FIND_IN_SET(elv.PROGRAM_CODE1, ?)
                        )
                    )
                ";

        $count_params = [
            $person_id,
            $person_id,
            $start_date,
            $start_date,
            $end_date,
            $end_date,
            $status_code,
            $status_code,
            $status_code
        ];

        foreach ($column_map as $index => $field) {

            $search_value = trim($columns[$index]['search']['value'] ?? '');

            if ($search_value === '') {
                continue;
            }

            if ($index == 5 || $index == 6) {

                $count_sql .= " AND {$field} = ? ";
                $count_params[] = $search_value;
            } else {

                $count_sql .= " AND {$field} LIKE ? ";
                $count_params[] = "%{$search_value}%";
            }
        }

        $count_query = $this->db->query($count_sql, $count_params);

        $total_filtered = 0;

        if ($count_query->num_rows() > 0) {
            $total_filtered = (int)$count_query->row()->total;
        }

        // ==========================
        // FORMAT DATA
        // ==========================
        $data = [];
        $no = $start;

        foreach ($data_result as $row) {
            $no++;
            $data[] = [
                $no,
                $row->Status ? badge_status($row->Status, $row->Warna_Status) : '-',
                $row->{'No Transaksi'} ? $row->{'No Transaksi'} : '-',
                $row->{'No Referensi'} ? $row->{'No Referensi'} : '-',
                $row->Tanggal ? date('d M Y', strtotime($row->Tanggal)) : '-',
                $row->Dibutuhkan ? date('d M Y', strtotime($row->Dibutuhkan)) : '-',
                $row->Supplier ? $row->Supplier : '-',
                $row->Storage ? $row->Storage : '-',
                $row->Sales ? $row->Sales : '-',
                $row->Total ? numb_format($row->Total) : '-',
                $row->Periode ? $row->Periode : '-',
                $row->Note ? $row->Note : '-',
                $row->{'Created By'} ? $row->{'Created By'} : '-',
                $this->encrypt->encode($row->PR_ID),
            ];
        }

        // ==========================
        // OUTPUT JSON
        // ==========================
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $total_filtered,
            "recordsFiltered" => $total_filtered,
            "data" => $data
        ]);
    }

    public function get_detail_fpk()
    {
        try {
            $pr_id = $this->encrypt->decode($this->input->post('pr_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            // Total data sebelum limit (untuk recordsTotal)
            $totalRecords = $this->out_kny->count_detail_by_pr_id($pr_id);

            $list = $this->out_kny->get_detail_by_pr_id($pr_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"        => $no++,
                    "nama_item" => $d->Nama_Item ?? '-',
                    "kode_item" => $d->Kode_Item ?? '-',
                    "jumlah"    => numb_format($d->Jumlah) ?? '-',
                    "terima"    => numb_format($d->Terima) ?? '-',
                    "sisa"      => numb_format($d->Sisa) ?? '-',
                    "uom"       => $d->UoM ?? '-',
                    "harga"     => numb_format($d->Harga) ?? '-',
                    "subtotal"  => numb_format($d->Subtotal) ?? '-',
                    "note"      => $d->Note ?? '-',
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

    public function export_fpk()
    {
        $supplier       = $this->input->get('supplier');
        $check_supplier = $this->input->get('check_supplier');

        $status         = $this->input->get('status');
        $check_status   = $this->input->get('check_status');

        $daterange      = $this->input->get('daterange');
        $check_period   = $this->input->get('check_period');

        // ==========================
        // BOOLEAN NORMALIZATION
        // ==========================
        $check_supplier = ($check_supplier === 'true' || $check_supplier === '1');
        $check_status   = ($check_status === 'true' || $check_status === '1');
        $check_period   = ($check_period === 'true' || $check_period === '1');

        // ==========================
        // SUPPLIER
        // ==========================
        $person_id = null;
        if (!$check_supplier && !empty($supplier)) {
            $person_id = $supplier;
        }

        // ==========================
        // STATUS
        // ==========================
        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED'
        ];

        $status_code = $status_map[$status] ?? $status;

        if ($check_status) {
            $status_code = null;
        }

        // ==========================
        // PERIOD
        // ==========================
        $start_date = null;
        $end_date   = null;

        if (!$check_period && !empty($daterange)) {

            $date = explode(' - ', $daterange);

            $start_date = trim($date[0] ?? null);
            $end_date   = trim($date[1] ?? null);

            if ($start_date === '') $start_date = null;
            if ($end_date === '') $end_date = null;
        }

        // ==========================
        // ORDER (OPTIONAL)
        // ==========================
        $order   = $this->input->get('order') ?? [];

        $column_map = [
            2  => 'b.DISPLAY_NAME',
            3  => 'a.DOCUMENT_NO',
            4  => 'a.DOCUMENT_REFF_NO',
            5  => 'a.DOCUMENT_DATE',
            6  => 'a.NEED_DATE',
            7  => 'p.PERSON_NAME',
            8  => 'w.WAREHOUSE_NAME',
            9  => 'k.FIRST_NAME',
            10 => 'a.TOTAL_AMOUNT',
            11 => 'a.PERIOD_NAME',
            12 => 'a.NOTE',
            13 => 'e.ERP_USER_NAME'
        ];

        $order_column = 'a.DOCUMENT_DATE';
        $order_dir    = 'DESC';

        if (!empty($order)) {

            $col_index = (int) $order[0]['column'];

            if (isset($column_map[$col_index])) {
                $order_column = $column_map[$col_index];
            }

            $order_dir = strtoupper($order[0]['dir'] ?? 'DESC');

            if (!in_array($order_dir, ['ASC', 'DESC'])) {
                $order_dir = 'DESC';
            }
        }

        // ==========================
        // PARAMS (SAMA SEPERTI GET_DATA)
        // ==========================
        $params = [
            $person_id,
            $person_id,

            $start_date,
            $start_date,

            $end_date,
            $end_date,

            $status_code,
            $status_code,
            $status_code
        ];

        // ==========================
        // SQL
        // ==========================
        $sql = "
            SELECT DISTINCT
                a.PR_ID,
                b.DISPLAY_NAME AS Status,
                b.MENU_ICON AS Warna_Status,
                a.DOCUMENT_NO AS `No Transaksi`,
                a.DOCUMENT_REFF_NO AS `No Referensi`,
                a.DOCUMENT_DATE AS `Tanggal`,
                a.NEED_DATE AS `Dibutuhkan`,
                CONCAT('[', p.PERSON_CODE, '] - ', p.PERSON_NAME) AS Supplier,
                w.WAREHOUSE_NAME AS Storage,
                k.FIRST_NAME AS Sales,
                a.TOTAL_AMOUNT AS Total,
                a.PERIOD_NAME AS Periode,
                a.NOTE AS Note,
                e.ERP_USER_NAME AS `Created By`

            FROM pr a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN person p ON a.PERSON_ID = p.PERSON_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID
            JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID

            WHERE TRUE
            AND a.DOCUMENT_TYPE_ID = 3

            AND (
                ? IS NULL
                OR a.PERSON_ID = ?
            )

            AND (
                ? IS NULL
                OR DATE(a.DOCUMENT_DATE) >= ?
            )

            AND (
                ? IS NULL
                OR DATE(a.DOCUMENT_DATE) <= ?
            )

            AND (
                ? IS NULL
                OR ? = ''
                OR EXISTS (
                    SELECT 1
                    FROM ERP_LOOKUP_VALUE elv
                    WHERE elv.ERP_LOOKUP_VALUE_ID = a.STATUS_ID
                    AND FIND_IN_SET(elv.PROGRAM_CODE1, ?)
                )
            )

            ORDER BY {$order_column} {$order_dir}
        ";

        $all_data = $this->db->query($sql, $params)->result();

        // ==========================
        // EXCEL
        // ==========================
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([[
            'No',
            'Status',
            'No Transaksi',
            'No Referensi',
            'Tanggal',
            'Dibutuhkan',
            'Supplier',
            'Storage',
            'Sales',
            'Total',
            'Periode',
            'Note',
            'Created By'
        ]], null, 'A1');

        $rowNum = 2;
        $no = 1;

        foreach ($all_data as $row) {

            $sheet->fromArray([
                $no++,
                $row->Status ?: '-',
                $row->{'No Transaksi'} ?: '-',
                $row->{'No Referensi'} ?: '-',
                $row->Tanggal ? date('d M Y', strtotime($row->Tanggal)) : '-',
                $row->Dibutuhkan ? date('d M Y', strtotime($row->Dibutuhkan)) : '-',
                $row->Supplier ?: '-',
                $row->Storage ?: '-',
                $row->Sales ?: '-',
                $row->Total ?: '-',
                $row->Periode ?: '-',
                $row->Note ?: '-',
                $row->{'Created By'} ?: '-',
            ], null, 'A' . $rowNum);

            $rowNum++;
        }

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export_fpk_konsinyasi.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
