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
            'CLOSE' => 'CLOSE,DELETE,CLOSED',
            'OUTSTANDING' => 'NEW,PARTIAL'
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
            6  => 'p.PERSON_NAME',
            7  => 'k.FIRST_NAME',
            8  => 'w.WAREHOUSE_NAME',
            9 => 'a.TOTAL_AMOUNT',
            10 => 'a.PERIOD_NAME',
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
                badge_status($row->Status, $row->Warna_Status),
                '<a target="_blank" href="' . base_url('fpk/detail/' . base64url_encode($this->encrypt->encode($row->PR_ID))) . '">
                    ' . ($row->{'No Transaksi'} ? $row->{'No Transaksi'} : '-') . '
                </a>',
                $row->{'No Referensi'},
                date('Y-m-d H:i', strtotime($row->Tanggal)),
                $row->Supplier,
                $row->Sales,
                $row->Storage,
                numb_format($row->Total),
                $row->Periode,
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
                    "nama_item" => $d->Nama_Item,
                    "kode_item" => $d->Kode_Item,
                    "jumlah"    => numb_format($d->Jumlah),
                    "terima"    => numb_format($d->Terima),
                    "sisa"      => numb_format($d->Sisa),
                    "uom"       => $d->UoM,
                    "harga"     => numb_format($d->Harga),
                    "subtotal"  => numb_format($d->Subtotal),
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

    public function export_fpk()
    {
        $supplier       = $this->input->get('supplier');
        $check_supplier = $this->input->get('check_supplier');

        $status         = $this->input->get('status');
        $check_status   = $this->input->get('check_status');

        $daterange      = $this->input->get('daterange');
        $check_period   = $this->input->get('check_period');

        $search_global  = $this->input->get('search_global');

        $columns        = $this->input->get('columns') ?? [];
        $col_search     = $this->input->get('col_search') ?? [];
        $order          = $this->input->get('order') ?? [];

        // BOOLEAN
        $check_supplier = ($check_supplier === 'true' || $check_supplier === '1');
        $check_status   = ($check_status === 'true' || $check_status === '1');
        $check_period   = ($check_period === 'true' || $check_period === '1');

        // SUPPLIER
        $person_id = (!$check_supplier && !empty($supplier)) ? $supplier : null;

        // STATUS MAP (WAJIB SAMA GET)
        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED',
            'OUTSTANDING' => 'NEW,PARTIAL'
        ];

        $status_code = $status_map[$status] ?? $status;
        if ($check_status) $status_code = null;

        // PERIOD
        $start_date = null;
        $end_date   = null;

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

        $column_map = [
            0  => null,
            1  => null,
            2  => 'b.DISPLAY_NAME',
            3  => 'a.DOCUMENT_NO',
            4  => 'a.DOCUMENT_REFF_NO',
            5  => 'a.DOCUMENT_DATE',
            6  => 'p.PERSON_NAME',
            7  => 'w.WAREHOUSE_NAME',
            8  => 'k.FIRST_NAME',
            9  => 'a.TOTAL_AMOUNT',
            10 => 'a.PERIOD_NAME',
        ];

        $order_column = 'a.DOCUMENT_DATE';
        $order_dir    = 'DESC';

        if (!empty($order)) {
            $col_index = (int) $order[0]['column'];

            if (isset($column_map[$col_index]) && $column_map[$col_index] !== null) {
                $order_column = $column_map[$col_index];
            }

            $order_dir = strtoupper($order[0]['dir'] ?? 'DESC');

            if (!in_array($order_dir, ['ASC', 'DESC'])) {
                $order_dir = 'DESC';
            }
        }

        // BASE QUERY (SAMA PERSIS GET)
        $sql = "
            SELECT DISTINCT
                a.PR_ID,
                b.DISPLAY_NAME AS Status,
                a.DOCUMENT_NO,
                a.DOCUMENT_REFF_NO,
                a.DOCUMENT_DATE,
                p.PERSON_NAME,
                w.WAREHOUSE_NAME,
                k.FIRST_NAME,
                a.TOTAL_AMOUNT,
                a.NOTE,
                a.PERIOD_NAME
            FROM pr a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN person p ON a.PERSON_ID = p.PERSON_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID
            WHERE a.DOCUMENT_TYPE_ID = 3
            AND (? IS NULL OR a.PERSON_ID = ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) >= ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) <= ?)
            AND (
                ? IS NULL OR ? = '' OR EXISTS (
                    SELECT 1 FROM erp_lookup_value elv
                    WHERE elv.ERP_LOOKUP_VALUE_ID = a.STATUS_ID
                    AND FIND_IN_SET(elv.PROGRAM_CODE1, ?)
                )
            )
        ";

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

        // COLUMN SEARCH (SAMA DENGAN GET_DATA_FPK)
        foreach ($column_map as $index => $field) {

            if ($field === null) {
                continue;
            }

            $search_value = trim($columns[$index]['search']['value'] ?? ($col_search[$index] ?? ''));

            if ($search_value === '') {
                continue;
            }

            if ($index == 5) {
                $sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
                $params[] = $search_value;
            } else {
                $sql .= " AND {$field} LIKE ? ";
                $params[] = "%{$search_value}%";
            }
        }

        // GLOBAL SEARCH
        if ($search_global) {
            $sql .= " AND (
            a.DOCUMENT_NO LIKE ?
            OR a.DOCUMENT_REFF_NO LIKE ?
            OR p.PERSON_NAME LIKE ?
            OR w.WAREHOUSE_NAME LIKE ?
            OR k.FIRST_NAME LIKE ?
            OR a.PERIOD_NAME LIKE ?
        )";

            for ($i = 0; $i < 7; $i++) {
                $params[] = "%{$search_global}%";
            }
        }

        $sql .= " ORDER BY {$order_column} {$order_dir} ";

        $all_data = $this->db->query($sql, $params)->result();

        // EXPORT (tetap)
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([[
            'No',
            'Status',
            'No Transaksi',
            'No Referensi',
            'Tanggal',
            'Supplier',
            'Storage',
            'Sales',
            'Total',
            'Periode'
        ]], null, 'A1');

        $no = 1;
        $row = 2;

        foreach ($all_data as $d) {
            $sheet->fromArray([
                $no++,
                $d->Status,
                $d->DOCUMENT_NO,
                $d->DOCUMENT_REFF_NO,
                $d->DOCUMENT_DATE,
                $d->PERSON_NAME,
                $d->WAREHOUSE_NAME,
                $d->FIRST_NAME,
                $d->TOTAL_AMOUNT,
                $d->PERIOD_NAME
            ], null, "A{$row}");
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export_fpk.xlsx"');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function get_supplier_grk()
    {
        $result = $this->out_kny->getSupplierGrk()->result();
        echo json_encode($result);
    }

    public function get_data_grk()
    {
        $supplier        = $this->input->post('supplier');
        $check_supplier  = $this->input->post('check_supplier');

        $status          = $this->input->post('status');
        $check_status    = $this->input->post('check_status');

        $daterange       = $this->input->post('daterange');
        $check_period    = $this->input->post('check_period');

        $check_supplier = ($check_supplier === 'true' || $check_supplier === '1');
        $check_status   = ($check_status === 'true' || $check_status === '1');
        $check_period   = ($check_period === 'true' || $check_period === '1');

        $person_id = null;
        if (!$check_supplier && !empty($supplier)) {
            $person_id = $supplier;
        }

        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED',
            'OUTSTANDING' => 'NEW,PARTIAL'
        ];

        $status_code = $status_map[$status] ?? $status;
        if ($check_status) {
            $status_code = null;
        }

        $start_date = null;
        $end_date   = null;

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
            6  => 'p.PERSON_NAME',
            7  => 'w.WAREHOUSE_NAME',
            8  => 'a.TOTAL_AMOUNT',
            9  => 'a.PERIOD_NAME',
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

        $sql = "
            SELECT DISTINCT
                a.PO_ID,
                a.PERSON_ID,
                a.WAREHOUSE_ID,
                a.CREATED_BY,
                b.DISPLAY_NAME AS Status,
                b.MENU_ICON Warna_Status,
                a.DOCUMENT_NO AS `No Transaksi`,
                a.DOCUMENT_REFF_NO AS `No Referensi`,
                a.DOCUMENT_DATE AS `Tanggal`,
                CONCAT('[', p.PERSON_CODE, '] - ', p.PERSON_NAME) AS Supplier,
                w.WAREHOUSE_NAME AS Storage,
                a.TOTAL_AMOUNT AS Total,
                a.PERIOD_NAME AS Periode,
                e.ERP_USER_NAME AS `Created By`

            FROM po a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN person p ON a.PERSON_ID = p.PERSON_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
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

            if ($search_value === '' || $field === null) continue;

            if ($index == 5) {
                $sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
                $params[] = $search_value;
            } else {
                $sql .= " AND {$field} LIKE ? ";
                $params[] = "%{$search_value}%";
            }
        }

        $sql .= "
                    ORDER BY {$order_column} {$order_dir}
                    LIMIT {$start}, {$length}
                ";

        $query = $this->db->query($sql, $params);

        $data_result = $query->result();

        $count_sql = "
                    SELECT COUNT(DISTINCT a.PO_ID) AS total

                    FROM po a
                    JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
                    JOIN person p ON a.PERSON_ID = p.PERSON_ID
                    JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
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

            if ($search_value === '' || $field === null) {
                continue;
            }

            if ($index == 5) {
                $count_sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
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

        $data = [];
        $no = $start;

        foreach ($data_result as $row) {
            $no++;
            $data[] = [
                $no,
                badge_status($row->Status, $row->Warna_Status),
                '<a target="_blank" href="' . base_url('grk/detail/' . base64url_encode($this->encrypt->encode($row->PO_ID))) . '">
                    ' . ($row->{'No Transaksi'} ? $row->{'No Transaksi'} : '-') . '
                </a>',
                $row->{'No Referensi'},
                date('Y-m-d H:i', strtotime($row->Tanggal)),
                $row->Supplier,
                $row->Storage,
                numb_format($row->Total),
                $row->Periode,
                $this->encrypt->encode($row->PO_ID),
            ];
        }

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $total_filtered,
            "recordsFiltered" => $total_filtered,
            "data" => $data
        ]);
    }

    public function get_detail_grk()
    {
        try {
            $po_id = $this->encrypt->decode($this->input->post('po_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            $totalRecords = $this->out_kny->count_detail_by_po_id($po_id);

            $list = $this->out_kny->get_detail_by_po_id($po_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"        => $no++,
                    "nama_item" => $d->Nama_Item,
                    "kode_item" => $d->Kode_Item,
                    "jumlah"    => numb_format($d->Jumlah),
                    "kirim_retur" => numb_format($d->Kirim_Retur),
                    "sisa"      => numb_format($d->Sisa),
                    "uom"       => $d->UoM,
                    "harga"     => numb_format($d->Harga),
                    "subtotal"  => numb_format($d->Subtotal),
                    "reff_no"   => $d->Reff_No,
                    "sales"     => $d->Sales,
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

    public function export_grk()
    {
        $supplier       = $this->input->get('supplier');
        $check_supplier = $this->input->get('check_supplier');

        $status         = $this->input->get('status');
        $check_status   = $this->input->get('check_status');

        $daterange      = $this->input->get('daterange');
        $check_period   = $this->input->get('check_period');

        $search_global  = $this->input->get('search_global');

        $columns        = $this->input->get('columns') ?? [];
        $col_search     = $this->input->get('col_search') ?? [];
        $order          = $this->input->get('order') ?? [];

        // BOOLEAN
        $check_supplier = ($check_supplier === 'true' || $check_supplier === '1');
        $check_status   = ($check_status === 'true' || $check_status === '1');
        $check_period   = ($check_period === 'true' || $check_period === '1');

        // SUPPLIER
        $person_id = (!$check_supplier && !empty($supplier)) ? $supplier : null;

        // STATUS MAP (WAJIB SAMA GET)
        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED',
            'OUTSTANDING' => 'NEW,PARTIAL'
        ];

        $status_code = $status_map[$status] ?? $status;
        if ($check_status) $status_code = null;

        // PERIOD
        $start_date = null;
        $end_date   = null;

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

        $column_map = [
            0  => null,
            1  => null,
            2  => 'b.DISPLAY_NAME',
            3  => 'a.DOCUMENT_NO',
            4  => 'a.DOCUMENT_REFF_NO',
            5  => 'a.DOCUMENT_DATE',
            6  => 'p.PERSON_NAME',
            7  => 'w.WAREHOUSE_NAME',
            8  => 'a.TOTAL_AMOUNT',
            9  => 'a.PERIOD_NAME',
        ];

        $order_column = 'a.DOCUMENT_DATE';
        $order_dir    = 'DESC';

        if (!empty($order)) {
            $col_index = (int) $order[0]['column'];

            if (isset($column_map[$col_index]) && $column_map[$col_index] !== null) {
                $order_column = $column_map[$col_index];
            }

            $order_dir = strtoupper($order[0]['dir'] ?? 'DESC');

            if (!in_array($order_dir, ['ASC', 'DESC'])) {
                $order_dir = 'DESC';
            }
        }

        // BASE QUERY (SAMA PERSIS GET)
        $sql = "
            SELECT DISTINCT
                a.PO_ID,
                b.DISPLAY_NAME AS Status,
                a.DOCUMENT_NO,
                a.DOCUMENT_REFF_NO,
                a.DOCUMENT_DATE,
                CONCAT('[', p.PERSON_CODE, '] - ', p.PERSON_NAME) AS PERSON_NAME,
                w.WAREHOUSE_NAME,
                a.TOTAL_AMOUNT,
                a.PERIOD_NAME
            FROM po a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN person p ON a.PERSON_ID = p.PERSON_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            WHERE a.DOCUMENT_TYPE_ID = 3
            AND (? IS NULL OR a.PERSON_ID = ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) >= ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) <= ?)
            AND (
                ? IS NULL OR ? = '' OR EXISTS (
                    SELECT 1 FROM erp_lookup_value elv
                    WHERE elv.ERP_LOOKUP_VALUE_ID = a.STATUS_ID
                    AND FIND_IN_SET(elv.PROGRAM_CODE1, ?)
                )
            )
        ";

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

        // COLUMN SEARCH (SAMA DENGAN GET_DATA_FPK)
        foreach ($column_map as $index => $field) {

            if ($field === null) {
                continue;
            }

            $search_value = trim($columns[$index]['search']['value'] ?? ($col_search[$index] ?? ''));

            if ($search_value === '') {
                continue;
            }

            if ($index == 5) {
                $sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
                $params[] = $search_value;
            } else {
                $sql .= " AND {$field} LIKE ? ";
                $params[] = "%{$search_value}%";
            }
        }

        // GLOBAL SEARCH
        if ($search_global) {
            $sql .= " AND (
            a.DOCUMENT_NO LIKE ?
            OR a.DOCUMENT_REFF_NO LIKE ?
            OR p.PERSON_NAME LIKE ?
            OR w.WAREHOUSE_NAME LIKE ?
            OR a.PERIOD_NAME LIKE ?
        )";

            for ($i = 0; $i < 5; $i++) {
                $params[] = "%{$search_global}%";
            }
        }

        $sql .= " ORDER BY {$order_column} {$order_dir} ";

        $all_data = $this->db->query($sql, $params)->result();

        // EXPORT (tetap)
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([[
            'No',
            'Status',
            'No Transaksi',
            'No Referensi',
            'Tanggal',
            'Supplier',
            'Storage',
            'Total',
            'Periode'
        ]], null, 'A1');

        $no = 1;
        $row = 2;

        foreach ($all_data as $d) {
            $sheet->fromArray([
                $no++,
                $d->Status,
                $d->DOCUMENT_NO,
                $d->DOCUMENT_REFF_NO,
                $d->DOCUMENT_DATE,
                $d->PERSON_NAME,
                $d->WAREHOUSE_NAME,
                $d->TOTAL_AMOUNT,
                $d->PERIOD_NAME
            ], null, "A{$row}");
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export_grk.xlsx"');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function get_data_sent_to_site()
    {
        $status       = $this->input->post('status');
        $check_status = $this->input->post('check_status');

        $daterange    = $this->input->post('daterange');
        $check_period = $this->input->post('check_period');

        $check_status = ($check_status === 'true' || $check_status === '1');
        $check_period = ($check_period === 'true' || $check_period === '1');

        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED',
            'OUTSTANDING' => 'NEW,PARTIAL'
        ];

        $status_code = $status_map[$status] ?? $status;
        if ($check_status) {
            $status_code = null;
        }

        $start_date = null;
        $end_date   = null;

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

        $draw   = $this->input->post('draw') ?? 1;
        $start  = (int) ($this->input->post('start') ?? 0);
        $length = (int) ($this->input->post('length') ?? 10);

        $order   = $this->input->post('order') ?? [];
        $columns = $this->input->post('columns') ?? [];

        $column_map = [
            0 => null,
            1 => null,
            2 => 'b.DISPLAY_NAME',
            3 => 'a.DOCUMENT_NO',
            4 => 'a.DOCUMENT_REFF_NO',
            5 => 'a.DOCUMENT_DATE',
            6 => 'w.WAREHOUSE_NAME',
            7 => 'wh.WAREHOUSE_NAME',
            8 => 'a.PERIOD_NAME',
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
            $start_date,
            $start_date,
            $end_date,
            $end_date,
            $status_code,
            $status_code,
            $status_code
        ];

        $sql = "
            SELECT DISTINCT
                a.TAG_KONSI_ID,
                a.WAREHOUSE_ID,
                a.CREATED_BY,
                b.DISPLAY_NAME AS Status,
                b.MENU_ICON Warna_Status,
                a.DOCUMENT_NO AS `No Transaksi`,
                a.DOCUMENT_REFF_NO AS `No Referensi`,
                a.DOCUMENT_DATE AS `Tanggal`,
                w.WAREHOUSE_NAME AS `Main Storage`,
                wh.WAREHOUSE_NAME AS `Site Storage`,
                a.PERIOD_NAME AS Periode
            FROM tag_konsi a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN warehouse wh ON a.TO_WH_ID = wh.WAREHOUSE_ID
            JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID
            WHERE TRUE
            AND a.DOCUMENT_TYPE_ID = 3
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) >= ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) <= ?)
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

            if ($search_value === '' || $field === null) {
                continue;
            }

            if ($index == 5) {
                $sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
                $params[] = $search_value;
            } else {
                $sql .= " AND {$field} LIKE ? ";
                $params[] = "%{$search_value}%";
            }
        }

        $sql .= "
            ORDER BY {$order_column} {$order_dir}
            LIMIT {$start}, {$length}
        ";

        $data_result = $this->db->query($sql, $params)->result();

        $count_sql = "
            SELECT COUNT(DISTINCT a.TAG_KONSI_ID) AS total
            FROM tag_konsi a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN warehouse wh ON a.TO_WH_ID = wh.WAREHOUSE_ID
            JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID
            WHERE TRUE
            AND a.DOCUMENT_TYPE_ID = 3
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) >= ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) <= ?)
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

            if ($search_value === '' || $field === null) {
                continue;
            }

            if ($index == 5) {
                $count_sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
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

        $data = [];
        $no = $start;

        foreach ($data_result as $row) {
            $no++;
            $data[] = [
                $no,
                badge_status($row->Status, $row->Warna_Status),
                '<a target="_blank" href="' . base_url('sts/detail/' . base64url_encode($this->encrypt->encode($row->TAG_KONSI_ID))) . '">
                    ' . ($row->{'No Transaksi'} ? $row->{'No Transaksi'} : '-') . '
                </a>',
                $row->{'No Referensi'},
                date('Y-m-d H:i', strtotime($row->Tanggal)),
                $row->{'Main Storage'},
                $row->{'Site Storage'},
                $row->Periode,
                $this->encrypt->encode($row->TAG_KONSI_ID),
            ];
        }

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $total_filtered,
            "recordsFiltered" => $total_filtered,
            "data" => $data
        ]);
    }

    public function get_detail_sent_to_site()
    {
        try {
            $tag_konsi_id = $this->encrypt->decode($this->input->post('tag_konsi_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            $totalRecords = $this->out_kny->count_detail_by_tag_konsi_id($tag_konsi_id);

            $list = $this->out_kny->get_detail_by_tag_konsi_id($tag_konsi_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"        => $no++,
                    "nama_item" => $d->Nama_Item,
                    "kode_item" => $d->Kode_Item,
                    "jumlah"    => numb_format($d->Jumlah),
                    "terima"    => numb_format($d->Terima),
                    "sisa"      => numb_format($d->Sisa),
                    "uom"       => $d->UoM,
                    "harga"     => numb_format($d->Harga),
                    "subtotal"  => numb_format($d->Subtotal),
                    "batch_no"  => $d->Batch_No,
                    "note"      => $d->Note,
                ];
            }

            echo json_encode([
                "draw" => intval($draw),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $data
            ]);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function export_sent_to_site()
    {
        $status        = $this->input->get('status');
        $check_status  = $this->input->get('check_status');
        $daterange     = $this->input->get('daterange');
        $check_period  = $this->input->get('check_period');
        $search_global = $this->input->get('search_global');
        $columns       = $this->input->get('columns') ?? [];
        $col_search    = $this->input->get('col_search') ?? [];
        $order         = $this->input->get('order') ?? [];

        $check_status = ($check_status === 'true' || $check_status === '1');
        $check_period = ($check_period === 'true' || $check_period === '1');

        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED',
            'OUTSTANDING' => 'NEW,PARTIAL'
        ];

        $status_code = $status_map[$status] ?? $status;
        if ($check_status) {
            $status_code = null;
        }

        $start_date = null;
        $end_date = null;

        if (!$check_period && !empty($daterange)) {
            $date = explode(' - ', $daterange);
            $start_date = trim($date[0] ?? null);
            $end_date = trim($date[1] ?? null);

            if ($start_date == '') {
                $start_date = null;
            }

            if ($end_date == '') {
                $end_date = null;
            }
        }

        $column_map = [
            0 => null,
            1 => null,
            2 => 'b.DISPLAY_NAME',
            3 => 'a.DOCUMENT_NO',
            4 => 'a.DOCUMENT_REFF_NO',
            5 => 'a.DOCUMENT_DATE',
            6 => 'w.WAREHOUSE_NAME',
            7 => 'wh.WAREHOUSE_NAME',
            8 => 'a.PERIOD_NAME',
        ];

        $order_column = 'a.DOCUMENT_DATE';
        $order_dir = 'DESC';

        if (!empty($order)) {
            $col_index = (int) $order[0]['column'];

            if (isset($column_map[$col_index]) && $column_map[$col_index] !== null) {
                $order_column = $column_map[$col_index];
            }

            $order_dir = strtoupper($order[0]['dir'] ?? 'DESC');

            if (!in_array($order_dir, ['ASC', 'DESC'])) {
                $order_dir = 'DESC';
            }
        }

        $sql = "
            SELECT DISTINCT
                a.TAG_KONSI_ID,
                b.DISPLAY_NAME AS Status,
                a.DOCUMENT_NO,
                a.DOCUMENT_REFF_NO,
                a.DOCUMENT_DATE,
                w.WAREHOUSE_NAME AS MAIN_STORAGE,
                wh.WAREHOUSE_NAME AS SITE_STORAGE,
                a.PERIOD_NAME
            FROM tag_konsi a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN warehouse wh ON a.TO_WH_ID = wh.WAREHOUSE_ID
            JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID
            WHERE a.DOCUMENT_TYPE_ID = 3
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) >= ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) <= ?)
            AND (
                ? IS NULL OR ? = '' OR EXISTS (
                    SELECT 1 FROM erp_lookup_value elv
                    WHERE elv.ERP_LOOKUP_VALUE_ID = a.STATUS_ID
                    AND FIND_IN_SET(elv.PROGRAM_CODE1, ?)
                )
            )
        ";

        $params = [
            $start_date,
            $start_date,
            $end_date,
            $end_date,
            $status_code,
            $status_code,
            $status_code
        ];

        foreach ($column_map as $index => $field) {
            if ($field === null) {
                continue;
            }

            $search_value = trim($columns[$index]['search']['value'] ?? ($col_search[$index] ?? ''));

            if ($search_value === '') {
                continue;
            }

            if ($index == 5) {
                $sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
                $params[] = $search_value;
            } else {
                $sql .= " AND {$field} LIKE ? ";
                $params[] = "%{$search_value}%";
            }
        }

        if ($search_global) {
            $sql .= " AND (
                a.DOCUMENT_NO LIKE ?
                OR a.DOCUMENT_REFF_NO LIKE ?
                OR w.WAREHOUSE_NAME LIKE ?
                OR wh.WAREHOUSE_NAME LIKE ?
                OR a.PERIOD_NAME LIKE ?
            )";

            for ($i = 0; $i < 5; $i++) {
                $params[] = "%{$search_global}%";
            }
        }

        $sql .= " ORDER BY {$order_column} {$order_dir} ";

        $all_data = $this->db->query($sql, $params)->result();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([[
            'No',
            'Status',
            'No Transaksi',
            'No Referensi',
            'Tanggal',
            'Main Storage',
            'Site Storage',
            'Periode'
        ]], null, 'A1');

        $no = 1;
        $row = 2;

        foreach ($all_data as $d) {
            $sheet->fromArray([
                $no++,
                $d->Status,
                $d->DOCUMENT_NO,
                $d->DOCUMENT_REFF_NO,
                $d->DOCUMENT_DATE,
                $d->MAIN_STORAGE,
                $d->SITE_STORAGE,
                $d->PERIOD_NAME
            ], null, "A{$row}");
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export_sent_to_site.xlsx"');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function get_data_receive_in_site()
    {
        $status       = $this->input->post('status');
        $check_status = $this->input->post('check_status');

        $daterange    = $this->input->post('daterange');
        $check_period = $this->input->post('check_period');

        $check_status = ($check_status === 'true' || $check_status === '1');
        $check_period = ($check_period === 'true' || $check_period === '1');

        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED',
            'OUTSTANDING' => 'NEW,PARTIAL'
        ];

        $status_code = $status_map[$status] ?? $status;
        if ($check_status) {
            $status_code = null;
        }

        $start_date = null;
        $end_date   = null;

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

        $draw   = $this->input->post('draw') ?? 1;
        $start  = (int) ($this->input->post('start') ?? 0);
        $length = (int) ($this->input->post('length') ?? 10);

        $order   = $this->input->post('order') ?? [];
        $columns = $this->input->post('columns') ?? [];

        $column_map = [
            0 => null,
            1 => null,
            2 => 'b.DISPLAY_NAME',
            3 => 'a.DOCUMENT_NO',
            4 => 'a.DOCUMENT_REFF_NO',
            5 => 'a.DOCUMENT_DATE',
            6 => 'wh.WAREHOUSE_NAME',
            7 => 'w.WAREHOUSE_NAME',
            8 => 'a.PERIOD_NAME',
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
            $start_date,
            $start_date,
            $end_date,
            $end_date,
            $status_code,
            $status_code,
            $status_code
        ];

        $sql = "
            SELECT DISTINCT
                a.TAG_ID,
                a.WAREHOUSE_ID,
                a.CREATED_BY,
                b.DISPLAY_NAME AS Status,
                b.MENU_ICON Warna_Status,
                a.DOCUMENT_NO AS `No Transaksi`,
                a.DOCUMENT_REFF_NO AS `No Referensi`,
                a.DOCUMENT_DATE AS `Tanggal`,
                wh.WAREHOUSE_NAME AS `Site Storage`,
                w.WAREHOUSE_NAME AS `Main Storage`,
                a.PERIOD_NAME AS Periode
            FROM tag a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN warehouse wh ON a.TO_WH_ID = wh.WAREHOUSE_ID
            JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID
            WHERE TRUE
            AND a.DOCUMENT_TYPE_ID = 3
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) >= ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) <= ?)
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

            if ($search_value === '' || $field === null) {
                continue;
            }

            if ($index == 5) {
                $sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
                $params[] = $search_value;
            } else {
                $sql .= " AND {$field} LIKE ? ";
                $params[] = "%{$search_value}%";
            }
        }

        $sql .= "
            ORDER BY {$order_column} {$order_dir}
            LIMIT {$start}, {$length}
        ";

        $data_result = $this->db->query($sql, $params)->result();

        $count_sql = "
            SELECT COUNT(DISTINCT a.TAG_ID) AS total
            FROM tag a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN warehouse wh ON a.TO_WH_ID = wh.WAREHOUSE_ID
            JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID
            WHERE TRUE
            AND a.DOCUMENT_TYPE_ID = 3
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) >= ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) <= ?)
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

            if ($search_value === '' || $field === null) {
                continue;
            }

            if ($index == 5) {
                $count_sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
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

        $data = [];
        $no = $start;

        foreach ($data_result as $row) {
            $no++;
            $data[] = [
                $no,
                badge_status($row->Status, $row->Warna_Status),
                '<a target="_blank" href="' . base_url('tag/detail/' . base64url_encode($this->encrypt->encode($row->TAG_ID))) . '">
                    ' . ($row->{'No Transaksi'} ? $row->{'No Transaksi'} : '-') . '
                </a>',
                $row->{'No Referensi'},
                date('Y-m-d H:i', strtotime($row->Tanggal)),
                $row->{'Site Storage'},
                $row->{'Main Storage'},
                $row->Periode,
                $this->encrypt->encode($row->TAG_ID),
            ];
        }

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => $total_filtered,
            "recordsFiltered" => $total_filtered,
            "data" => $data
        ]);
    }

    public function get_detail_receive_in_site()
    {
        try {
            $tag_id = $this->encrypt->decode($this->input->post('tag_id'));

            $start = $this->input->post('start') ?? 0;
            $length = $this->input->post('length') ?? 10;
            $draw = $this->input->post('draw') ?? 1;

            $totalRecords = $this->out_kny->count_detail_by_tag_id($tag_id);

            $list = $this->out_kny->get_detail_by_tag_id($tag_id, $length, $start);
            $data = [];
            $no = $start + 1;

            foreach ($list->result() as $d) {
                $data[] = [
                    "no"         => $no++,
                    "nama_item"  => $d->Nama_Item,
                    "kode_item"  => $d->Kode_Item,
                    "jumlah"     => numb_format($d->Jumlah),
                    "pakai_retur" => numb_format($d->Pakai_Retur),
                    "sisa"       => numb_format($d->Sisa),
                    "satuan"     => $d->Satuan,
                    "batch_no"   => $d->Batch_No,
                    "note"       => $d->Note,
                ];
            }

            echo json_encode([
                "draw" => intval($draw),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $data
            ]);
        } catch (Exception $err) {
            return sendError('Server Error', $err->getMessage());
        }
    }

    public function export_receive_in_site()
    {
        $status        = $this->input->get('status');
        $check_status  = $this->input->get('check_status');
        $daterange     = $this->input->get('daterange');
        $check_period  = $this->input->get('check_period');
        $search_global = $this->input->get('search_global');
        $columns       = $this->input->get('columns') ?? [];
        $col_search    = $this->input->get('col_search') ?? [];
        $order         = $this->input->get('order') ?? [];

        $check_status = ($check_status === 'true' || $check_status === '1');
        $check_period = ($check_period === 'true' || $check_period === '1');

        $status_map = [
            'CLOSE' => 'CLOSE,DELETE,CLOSED',
            'OUTSTANDING' => 'NEW,PARTIAL'
        ];

        $status_code = $status_map[$status] ?? $status;
        if ($check_status) {
            $status_code = null;
        }

        $start_date = null;
        $end_date = null;

        if (!$check_period && !empty($daterange)) {
            $date = explode(' - ', $daterange);
            $start_date = trim($date[0] ?? null);
            $end_date = trim($date[1] ?? null);

            if ($start_date == '') {
                $start_date = null;
            }

            if ($end_date == '') {
                $end_date = null;
            }
        }

        $column_map = [
            0 => null,
            1 => null,
            2 => 'b.DISPLAY_NAME',
            3 => 'a.DOCUMENT_NO',
            4 => 'a.DOCUMENT_REFF_NO',
            5 => 'a.DOCUMENT_DATE',
            6 => 'wh.WAREHOUSE_NAME',
            7 => 'w.WAREHOUSE_NAME',
            8 => 'a.PERIOD_NAME',
        ];

        $order_column = 'a.DOCUMENT_DATE';
        $order_dir = 'DESC';

        if (!empty($order)) {
            $col_index = (int) $order[0]['column'];

            if (isset($column_map[$col_index]) && $column_map[$col_index] !== null) {
                $order_column = $column_map[$col_index];
            }

            $order_dir = strtoupper($order[0]['dir'] ?? 'DESC');

            if (!in_array($order_dir, ['ASC', 'DESC'])) {
                $order_dir = 'DESC';
            }
        }

        $sql = "
            SELECT DISTINCT
                a.TAG_ID,
                b.DISPLAY_NAME AS Status,
                a.DOCUMENT_NO,
                a.DOCUMENT_REFF_NO,
                a.DOCUMENT_DATE,
                wh.WAREHOUSE_NAME AS SITE_STORAGE,
                w.WAREHOUSE_NAME AS MAIN_STORAGE,
                a.PERIOD_NAME
            FROM tag a
            JOIN erp_lookup_value b ON a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID
            JOIN warehouse w ON a.WAREHOUSE_ID = w.WAREHOUSE_ID
            JOIN warehouse wh ON a.TO_WH_ID = wh.WAREHOUSE_ID
            JOIN erp_user e ON a.CREATED_BY = e.ERP_USER_ID
            WHERE a.DOCUMENT_TYPE_ID = 3
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) >= ?)
            AND (? IS NULL OR DATE(a.DOCUMENT_DATE) <= ?)
            AND (
                ? IS NULL OR ? = '' OR EXISTS (
                    SELECT 1 FROM erp_lookup_value elv
                    WHERE elv.ERP_LOOKUP_VALUE_ID = a.STATUS_ID
                    AND FIND_IN_SET(elv.PROGRAM_CODE1, ?)
                )
            )
        ";

        $params = [
            $start_date,
            $start_date,
            $end_date,
            $end_date,
            $status_code,
            $status_code,
            $status_code
        ];

        foreach ($column_map as $index => $field) {
            if ($field === null) {
                continue;
            }

            $search_value = trim($columns[$index]['search']['value'] ?? ($col_search[$index] ?? ''));

            if ($search_value === '') {
                continue;
            }

            if ($index == 5) {
                $sql .= " AND DATE(a.DOCUMENT_DATE) = ? ";
                $params[] = $search_value;
            } else {
                $sql .= " AND {$field} LIKE ? ";
                $params[] = "%{$search_value}%";
            }
        }

        if ($search_global) {
            $sql .= " AND (
                a.DOCUMENT_NO LIKE ?
                OR a.DOCUMENT_REFF_NO LIKE ?
                OR w.WAREHOUSE_NAME LIKE ?
                OR wh.WAREHOUSE_NAME LIKE ?
                OR a.PERIOD_NAME LIKE ?
            )";

            for ($i = 0; $i < 5; $i++) {
                $params[] = "%{$search_global}%";
            }
        }

        $sql .= " ORDER BY {$order_column} {$order_dir} ";

        $all_data = $this->db->query($sql, $params)->result();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([[
            'No',
            'Status',
            'No Transaksi',
            'No Referensi',
            'Tanggal',
            'Site Storage',
            'Main Storage',
            'Periode'
        ]], null, 'A1');

        $no = 1;
        $row = 2;

        foreach ($all_data as $d) {
            $sheet->fromArray([
                $no++,
                $d->Status,
                $d->DOCUMENT_NO,
                $d->DOCUMENT_REFF_NO,
                $d->DOCUMENT_DATE,
                $d->SITE_STORAGE,
                $d->MAIN_STORAGE,
                $d->PERIOD_NAME
            ], null, "A{$row}");
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="export_receive_in_site.xlsx"');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
