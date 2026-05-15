<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Item_import_model
 *
 * Model untuk manajemen CRUD tabel `import_history`.
 * Tabel ini dibuat otomatis (auto-migrate) jika belum ada.
 *
 * Kolom utama:
 *   IMPORT_HISTORY_ID  PK, auto increment
 *   IMPORT_KEY         Identitas jenis import (misal: 'IMPORT_ITEM')
 *   SESSION_ID         Session PHP saat job dibuat
 *   STATUS             pending | queued | running | done | failed | archived
 *   PROGRESS           Jumlah baris yang sudah diproses
 *   MAX_PROGRESS       Total baris yang harus diproses
 *   CHUNK              Nomor chunk terakhir
 *   MESSAGE            Pesan status terakhir (ditampilkan di UI)
 *   PROCESS_ID         PID proses background (untuk monitoring/kill)
 *   JSON_TEXT          LONGTEXT — menyimpan path file atau path result JSON
 *   CREATED_BY / LAST_UPDATE_BY
 *   CREATED_DATE / LAST_UPDATE_DATE / STARTED_AT / FINISHED_AT
 */
class Item_import_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        setVariableMysql();
    }

    public function get_job($job_id)
    {
        $this->db->where('IMPORT_HISTORY_ID', (int) $job_id);
        $query = $this->db->get('import_history');
        return $query->row_array() ?: null;
    }

    public function update_job($job_id, array $data)
    {
        $data['LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');

        $this->db->where('IMPORT_HISTORY_ID', (int)$job_id);
        return $this->db->update('import_history', $data);
    }


    public function get_recent_jobs($import_key = null, $limit = 10)
    {
        $this->db->where('CREATED_BY', $this->session->userdata('id_user') ?? 1);

        if ($import_key) {
            $this->db->where('IMPORT_KEY', $import_key);
        }

        $this->db->order_by('IMPORT_HISTORY_ID', 'DESC');
        $this->db->limit((int)$limit);

        return $this->db->get('import_history')->result_array();
    }

    public function batch_query_null($values, $column)
    {
        if (empty($values)) {
            return [];
        }
 
        $values = array_values(array_unique($values));
 
        $query = $this->db
            ->select("{$column}, PERSON_ID")
            ->from('item')
            ->where('PERSON_ID IS NULL')
            ->where_in($column, $values)
            ->get();
 
        $result = [];
        if ($query && $query->num_rows() > 0) {
            foreach ($query->result_array() as $dbRow) {
                $result[$dbRow[$column] . '_'] = 'db';
            }
        }
        return $result;
    }

    
    public function batch_query_with_person($grouped, $column)
    {
        if (empty($grouped)) {
            return [];
        }

        $lookup_set  = [];
        $all_values  = [];
        $all_persons = [];

        foreach ($grouped as $person_id => $values) {
            if (empty($values)) {
                continue;
            }

            $values      = array_unique($values);
            $person_id   = (int) $person_id;
            $all_persons[] = $person_id;

            foreach ($values as $val) {
                $lookup_set[$person_id][$val] = true;
                $all_values[]                 = $val;
            }
        }

        if (empty($all_persons) || empty($all_values)) {
            return [];
        }

        $all_values  = array_values(array_unique($all_values));
        $all_persons = array_values(array_unique($all_persons));

        $query = $this->db
            ->select("{$column}, PERSON_ID")
            ->from('item')
            ->where_in('PERSON_ID', $all_persons)
            ->where_in($column, $all_values)
            ->get();

        if (!$query || $query->num_rows() === 0) {
            return [];
        }

        $result = [];
        foreach ($query->result_array() as $dbRow) {
            $person_id = (int) $dbRow['PERSON_ID'];
            $val       = $dbRow[$column];

            if (isset($lookup_set[$person_id][$val])) {
                $result[$val . '_' . $person_id] = 'db';
            }
        }

        return $result;
    }
}