<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Import_data extends MX_Controller
{
    /** Kolom header template, urutan HARUS sama dengan kolom Excel */
    const HEADER_TEMPLATE = [
        'brand', 'category', 'part_number', 'description', 'assy_code',
        'uom', 'type', 'min_stock', 'lead_time', 'rak',
        'length', 'width', 'height', 'weight', 'jenis',
        'grade', 'hpp', 'note', 'min_ord_qty', 'made_in',
        'komoditi', 'konsinyasi', 'supplier'
    ];

    private $dt_part_number = [];
    private $dt_description = [];
    private $dt_assy_code   = [];

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_404();
        }
        date_default_timezone_set('Asia/Jakarta');
    }

    public function cli_process($encode_id, $encoded_db)
    {
        if (!is_cli()) {
            show_404();
        }

        $job_id = (int) $this->encrypt->decode(base64url_decode($encode_id));

        // --- 1. Buka koneksi DB karena setiap user memiliki akses db berbeda
        $db = $this->_init_cli_db($encoded_db);
        if (!$db) {
            exit('DB connection failed');
        }

        // Reload model dengan koneksi baru
        $this->load->model('Item_import_model', 'import_model');
        $this->load->model('Item_model', 'item');

        // --- 2. Ambil job & validasi ---
        $job = $this->import_model->get_job($job_id);
        if (!$job || in_array($job['STATUS'], ['failed', 'done'])) {
            exit('Job tidak valid atau sudah selesai.');
        }

        $this->import_model->update_job($job_id, [
            'STATUS'     => 'running',
            'STARTED_AT' => date('Y-m-d H:i:s'),
            'MESSAGE'    => 'Mulai membaca file...',
            'PROCESS_ID' => getmypid(),
        ]);

        try {
            $json_data  = json_decode($job['JSON_TEXT'], true);
            $upload_dir = FCPATH . 'assets/upload/item_import/';
            $filename   = $json_data['filename'] ?? '';
            $file_path  = $upload_dir.$filename;
            $chunk      = (int) $job['CHUNK'];
            $start_row  = 7;

            if (!file_exists($file_path)) {
                throw new Exception("File Excel tidak ditemukan di: $file_path");
            }

            $this->load->library('simpleexcel');

            // data master
            $lookups = $this->_load_lookups();

            $progress     = 0;
            $max_progress = (int)$job['MAX_PROGRESS'];
            $results      = ['success' => [], 'failed' => []];

            while ($progress < $max_progress) {
                $skip_rows = $start_row + $progress;

                $dataChunk = $this->simpleexcel->read($file_path, self::HEADER_TEMPLATE, [
                    'skip_rows'  => $skip_rows,
                    'chunk_size' => $chunk,
                ]);

                if (empty($dataChunk)) {
                    break;
                }

                // --- Preload duplikat DB untuk chunk ini ---
                $this->_preload_db_duplicates($dataChunk, $lookups);

                foreach ($dataChunk as $idx => $row) {
                    $row_number = $skip_rows + $idx + 1;
                    $this->_validate_and_map_row($row, $row_number, $lookups, $results);
                    $progress++;
                }

                // Update progress ke DB setiap selesai satu chunk
                $this->import_model->update_job($job_id, [
                    'PROGRESS' => $progress,
                    'MESSAGE'  => "Memvalidasi {$progress} / {$max_progress} baris...",
                ]);
            }

            // --- 6. Simpan result ke file JSON
            $result_filename = folder_key().sha1(sha1($job_id)).'.json';
            $result_path = $upload_dir . $result_filename;
            $write_ok    = file_put_contents($result_path, json_encode([
                'file_path' => $file_path,
                'success'   => $results['success'],
                'failed'    => $results['failed'],
            ]));

            if ($write_ok === false) {
                throw new Exception("Gagal menulis file hasil validasi.");
            }

            // --- 7. Update job: done ---
            $json_data['result_filename'] = $result_filename;
            $this->import_model->update_job($job_id, [
                'STATUS'      => 'done',
                'MESSAGE'     => 'Validasi selesai. ' . count($results['success']) . ' success, ' . count($results['failed']) . ' gagal.',
                'PROGRESS'    => $max_progress,
                'JSON_TEXT'   => json_encode($json_data),
                'FINISHED_AT' => date('Y-m-d H:i:s'),
                'PROCESS_ID'  => null,
            ]);

        } catch (Exception $e) {
            $this->import_model->update_job($job_id, [
                'STATUS'      => 'failed',
                'MESSAGE'     => 'Error: ' . $e->getMessage(),
                'FINISHED_AT' => date('Y-m-d H:i:s'),
                'PROCESS_ID'  => null,
            ]);
        }
    }

    private function _init_cli_db($encoded_db)
    {
        $db_config = json_decode($this->encrypt->decode(base64url_decode($encoded_db)), true);
        if (!$db_config) return false;

        $db_config['dbdriver'] = 'mysqli';
        $db_config['db_debug'] = FALSE;

        $prev_error_level = error_reporting(0);
        $db = $this->load->database($db_config, TRUE);
        error_reporting($prev_error_level);

        if (!$db || !$db->conn_id) {
            return false;
        }

        // Ganti koneksi default CI agar model-model menggunakan koneksi ini
        $ci     = &get_instance();
        if (isset($ci->db) && is_object($ci->db)) {
            @$ci->db->close();
        }
        $ci->db = $db;

        return $db;
    }

    private function _load_lookups()
    {
        $lookups = [];

        $ckLastData = $this->item->checkLastData();
        foreach ([
            'brand::merek'      => 'Brand_Name',
            'category::group'   => 'Category_Name',
            'uom'               => 'UOM_CODE',
            'type::typeinventory'      => 'Trade_Type',
            'rak'               => 'Grade',
            'jenis'             => 'Jenis_Item',
            'grade'             => 'Grade',
            'madeIn::made_in'   => 'Made_In',
            'komoditi::tipe'    => 'Komoditi',
            'supplier'          => 'Supplier'
            ] as $code          => $field) {
            $x      = explode("::",$code);
            $code   = $x[0];
            $k_db   = isset($x[1])?$x[1]:$code;
            $data   = $this->item->cacheData($code, $k_db, $ckLastData);
            
            $lookups[$code] = [];
            foreach ($data as $row) {
                $key = $this->_clean_key($row[$field]);
                $lookups[$code][$key] = $row;
            }
        }

        return $lookups;
    }
    private function _clean_key($key)
    {
        $key = trim($key??'');
        $key = strtolower($key);
        return preg_replace('/[^a-z0-9]/', '', $key);
    }

    private function _validate_and_map_row(array $row, $row_number, array $lookups, array &$results)
    {
        $errors = [];

        // --- Validasi required ---
        $required = [
            'brand'       => 'Brand',
            'category'    => 'Category',
            'part_number' => 'Part Number',
            'description' => 'Description',
            'uom'         => 'Satuan',
            'type'        => 'Type',
            'lead_time'   => 'Lead Time',
            'jenis'       => 'Jenis',
            'grade'       => 'Grade',
            'komoditi'    => 'Komoditi',
        ];
        foreach ($required as $field => $label) {
            if (empty(trim($row[$field] ?? ''))) {
                $errors[] = "{$label} wajib diisi";
            }
        }

        // --- Validasi konsinyasi & supplier bersyarat ---
        $is_konsy = strtoupper(trim($row['konsinyasi'] ?? '')) === 'Y' ? 'Y' : 'N';
        if ($is_konsy === 'Y' && empty(trim($row['supplier'] ?? ''))) {
            $errors[] = 'Supplier wajib diisi karena Konsinyasi = Y';
        }

        // --- Resolve ID dari lookup ---
        $val_brand      = $lookups['brand'][$this->_clean_key($row['brand'])] ?? [];
        $val_category   = $lookups['category'][$this->_clean_key($row['category'])] ?? [];
        $val_uom        = $lookups['uom'][$this->_clean_key($row['uom'])] ?? [];
        $val_type       = $lookups['type'][$this->_clean_key($row['type'])] ?? [];
        $val_rak        = $lookups['rak'][$this->_clean_key($row['rak'])] ?? [];
        $val_made_in    = $lookups['madeIn'][$this->_clean_key($row['made_in'])] ?? [];
        $val_komoditi   = $lookups['komoditi'][$this->_clean_key($row['komoditi'])] ?? [];
        $val_jenis      = $lookups['jenis'][$this->_clean_key($row['jenis'])] ?? [];
        $val_grade      = $lookups['grade'][$this->_clean_key($row['grade'])] ?? [];
        $val_supplier   = $lookups['supplier'][$this->_clean_key($row['supplier'])] ?? [];

        if (!empty(trim($row['brand'] ?? '')) && empty($val_brand))    $errors[] = 'Brand tidak ditemukan di master data';
        if (!empty(trim($row['category'] ?? '')) && empty($val_category))    $errors[] = 'Category tidak ditemukan di master data';
        if (!empty(trim($row['uom'] ?? '')) && empty($val_uom))    $errors[] = 'Satuan tidak ditemukan di master data';
        if (!empty(trim($row['type'] ?? '')) && empty($val_type))    $errors[] = 'Type tidak ditemukan di master data';
        if (!empty(trim($row['rak'] ?? '')) && empty($val_rak))    $errors[] = 'Rak tidak ditemukan di master data';
        if (!empty(trim($row['made_in'] ?? '')) && empty($val_made_in))    $errors[] = 'Made In tidak ditemukan di master data';
        if (!empty(trim($row['komoditi'] ?? '')) && empty($val_komoditi))    $errors[] = 'Komoditi tidak ditemukan di master data';
        if (!empty(trim($row['jenis'] ?? '')) && empty($val_jenis))    $errors[] = 'Jenis tidak ditemukan di master data';
        if (!empty(trim($row['grade'] ?? '')) && empty($val_grade))    $errors[] = 'Grade tidak ditemukan di master data';
        if ($is_konsy === 'Y' && !empty(trim($row['supplier'] ?? '')) && empty($val_supplier))    $errors[] = 'Supplier tidak ditemukan di master data';

        //check number format
        foreach ([
            'min_stock' => ['min' => 0],
            'lead_time' => ['min' => 1, 'decimal' => false],
            'length'    => ['min' => 1],
            'width'     => ['min' => 1],
            'height'    => ['min' => 1],
            'weight'    => ['min' => 1, 'decimal' => false],
            'hpp'       => ['min' => 0],
            'min_ord_qty' => ['min' => 1, 'decimal' => false],
        ] as $field => $attr) {
            $val = $row[$field];
            if (!is_numeric($val)) {
                $errors[] = ucwords(str_replace('_',' ', $field)).' harus berupa angka';
                continue;
            }
            if(isset($attr['decimal']) && !$attr['decimal'] && !ctype_digit((string) $val)){
                $errors[] = ucwords(str_replace('_',' ', $field)).' harus berupa angka bilangan bulat';
            }
            if((float) $val < $attr['min']){
                $errors[] = ucwords(str_replace('_',' ', $field)).' minimal nilai adalah '.$attr['min'];
            }
        }

        // --- Cek duplikat ---
        // part item description
        if (empty($errors)) {
            $key_description = trim($row['description']).'_'.($is_konsy=='Y' && !empty($val_supplier)?$val_supplier['PERSON_ID']:'');
            if (isset($this->dt_description[$key_description])) {
                $errors[] = 'Kombinasi item description sudah '.($this->dt_description[$key_description] =='db'?'terdaftar di database':'ada pada baris sebelumnya');
            }else{
                $this->dt_description[$key_description] = 'file';
            }
        }

        // part assy_code
        if (empty($errors) && trim($row['assy_code'])) {
            $key_assy_code = trim($row['assy_code']).'_'.($is_konsy=='Y' && !empty($val_supplier)?$val_supplier['PERSON_ID']:'');
            if (isset($this->dt_assy_code[$key_assy_code])) {
                $errors[] = 'Kombinasi assy code sudah '.($this->dt_assy_code[$key_assy_code] =='db'?'terdaftar di database':'ada pada baris sebelumnya');
            }else{
                $this->dt_assy_code[$key_assy_code] = 'file';
            }
        }
        
        // part number
        if (empty($errors)) {
            $key_pn = trim($row['part_number']).'_'.($is_konsy=='Y' && !empty($val_supplier)?$val_supplier['PERSON_ID']:'');
            if (isset($this->dt_part_number[$key_pn])) {
                $errors[] = 'Kombinasi part number sudah '.($this->dt_part_number[$key_pn] =='db'?'terdaftar di database':'ada pada baris sebelumnya');
            }else{
                $this->dt_part_number[$key_pn] = 'file';
            }
        }
        // --- End cek duplikasi

        $mapped = $row;
        $mapped['row_number']  = $row_number;
        if (!empty($errors)) {
            $mapped['error_reason'] = implode('; ', $errors);
            $results['failed'][]    = $mapped;
        } else {
            $mapped['insert'] = [
                'MEREK_ID'          => (int) $val_brand['ERP_LOOKUP_VALUE_ID'],
                'GROUP_ID'          => (int) $val_category['ERP_LOOKUP_VALUE_ID'],
                'UOM_CODE'          => $val_uom['UOM_CODE'],
                'LOKASI_ID'         => (int) $val_rak['ERP_LOOKUP_VALUE_ID'],
                'TYPE_ID'           => (int) $val_type['ERP_LOOKUP_VALUE_ID'],
                'MADE_IN_ID'        => (int) $val_made_in['ERP_LOOKUP_VALUE_ID'],
                'TIPE_ID'           => (int) $val_komoditi['ERP_LOOKUP_VALUE_ID'],
                'JENIS_ID'          => (int) $val_jenis['ERP_LOOKUP_VALUE_ID'],
                'GRADE_ID'          => (int) $val_grade['ERP_LOOKUP_VALUE_ID'],
                'PERSON_ID'         => $is_konsy=='Y' && !empty($val_supplier)?(int) $val_supplier['PERSON_ID']:null,
            ];
            $results['success'][]   = $mapped;
        }
    }
    
    private function _preload_db_duplicates(array $dataChunk, array $lookups)
    {
        $pn_null          = []; // part_number  konsinyasi = N
        $pn_with_person   = []; // part_number  konsinyasi = Y, key = PERSON_ID
        $desc_null        = [];
        $desc_with_person = [];
        $assy_null        = [];
        $assy_with_person = [];
 
        foreach ($dataChunk as $row) {
            $is_konsy   = strtoupper(trim($row['konsinyasi'] ?? '')) === 'Y' ? 'Y' : 'N';
            $val_supplier = $lookups['supplier'][$this->_clean_key($row['supplier'] ?? '')] ?? [];
            $person_id    = ($is_konsy === 'Y' && !empty($val_supplier))
                            ? (int) $val_supplier['PERSON_ID']
                            : null;
 
            $pn   = trim($row['part_number'] ?? '');
            $desc = trim($row['description'] ?? '');
            $assy = trim($row['assy_code'] ?? '');
 
            if ($person_id === null) {
                if ($pn !== '')   $pn_null[]   = $pn;
                if ($desc !== '')  $desc_null[]  = $desc;
                if ($assy !== '')  $assy_null[]  = $assy;
            } else {
                $pn_with_person[$person_id][]   = $pn;
                $desc_with_person[$person_id][] = $desc;
                if ($assy !== '') $assy_with_person[$person_id][] = $assy;
            }
        }
 
        // --- 2. Query batch untuk kondisi PERSON_ID IS NULL ---
        $this->dt_part_number   += $this->import_model->batch_query_null($pn_null,'PART_NUMBER');
        $this->dt_description   += $this->import_model->batch_query_null($desc_null,'ITEM_DESCRIPTION');
        $this->dt_assy_code     += $this->import_model->batch_query_null($assy_null,'ASSY_CODE');
 
        // --- 3. Query batch untuk kondisi PERSON_ID = nilai tertentu ---
        $this->dt_part_number   += $this->import_model->batch_query_with_person($pn_with_person,'PART_NUMBER');
        $this->dt_description   += $this->import_model->batch_query_with_person($desc_with_person,'ITEM_DESCRIPTION');
        $this->dt_assy_code     += $this->import_model->batch_query_with_person($assy_with_person,'ASSY_CODE');
    }
}