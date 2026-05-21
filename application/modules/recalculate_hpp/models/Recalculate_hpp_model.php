<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recalculate_hpp_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
        $this->load->dbforge();
        $this->init_database();
    }

    private function init_database()
    {
        $this->create_sync_jobs();
    }

    private function create_sync_jobs()
    {
        if (!$this->db->table_exists('job')) {
            $fields = [
                'JOB_ID'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE, 'auto_increment' => TRUE],
                'PROCEDURE_KEY'     => ['type' => 'VARCHAR', 'constraint' => '30'],
                'SESSION_ID'        => ['type' => 'VARCHAR', 'constraint' => '128'],
                'STATUS'            => ['type' => 'ENUM("pending","queued","running","done","failed")', 'default' => 'pending'],
                'PROGRESS'          => ['type' => 'TINYINT', 'constraint' => 3, 'default' => 0],
                'MESSAGE'           => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE],
                'PROCESS_ID'        => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'THREAD_ID'         => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'DURATION_SEC'      => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'CREATED_BY'        => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'CREATED_DATE'      => ['type' => 'DATETIME', 'null' => TRUE],
                'LAST_UPDATE_BY'    => ['type' => 'INT', 'constraint' => 11, 'null' => TRUE],
                'LAST_UPDATE_DATE'  => ['type' => 'DATETIME', 'null' => TRUE],
                'STARTED_AT'          => ['type' => 'DATETIME', 'null' => TRUE],
                'FINISHED_AT'       => ['type' => 'DATETIME', 'null' => TRUE],
            ];

            $this->dbforge->add_field($fields);
            $this->dbforge->add_key('JOB_ID', TRUE);
            $this->dbforge->add_key('STATUS');
            $this->dbforge->add_key(['PROCEDURE_KEY', 'STATUS']);

            $attributes = ['ENGINE' => 'InnoDB', 'COMMENT' => '"Tracking background sync jobs"'];
            $this->dbforge->create_table('job', FALSE, $attributes);
        }
    }

    public function get_active_job()
    {
        $this->db->where('PROCEDURE_KEY', 'RECALCULATE_HPP_ITEM');
        $this->db->where_in('STATUS', ['pending', 'queued', 'running']);
        $query = $this->db->get('job');
        return $query->row_array();
    }

    public function get_history($limit = 5)
    {
        $this->db->select('STATUS as status, CREATED_DATE as created_at, MESSAGE as message, DURATION_SEC as duration_sec');
        $this->db->where_in('STATUS', ['done', 'failed']);
        $this->db->where('PROCEDURE_KEY', 'RECALCULATE_HPP_ITEM');
        $this->db->order_by('JOB_ID', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get('job');
        return $query->result_array();
    }

    public function create_job($procedure_key)
    {
        $data = [
            'PROCEDURE_KEY' => $procedure_key,
            'SESSION_ID' => session_id(),
            'STATUS' => 'queued',
            'PROGRESS' => 0,
            'MESSAGE' => 'Menunggu antrian...',
            'CREATED_DATE' => date('Y-m-d H:i:s'),
            'CREATED_BY' => $this->session->userdata('id_user') ?? 1 // Adjust if needed
        ];
        $this->db->insert('job', $data);
        return $this->db->insert_id();
    }

    public function get_job($job_id)
    {
        $this->db->where('JOB_ID', $job_id);
        $query = $this->db->get('job');
        return $query->row_array();
    }

    public function update_job($job_id, $data)
    {
        $data['LAST_UPDATE_DATE'] = date('Y-m-d H:i:s');
        $this->db->where('JOB_ID', $job_id);
        return $this->db->update('job', $data);
    }

    public function get_avg_duration($key)
    {
        $row = $this->db->query("
            SELECT AVG(DURATION_SEC) AS avg_dur
            FROM (
                SELECT DURATION_SEC
                FROM job
                WHERE PROCEDURE_KEY = ?
                AND DURATION_SEC   > 0
                ORDER BY JOB_ID DESC
                LIMIT 5
            ) t
        ", [$key])->row();

        return ($row && $row->avg_dur > 0) ? (float) $row->avg_dur : null;
    }

    public function is_cancelled($job_id)
    {
        $job = $this->get_job($job_id);
        return !$job || $job['STATUS'] === 'failed';
    }
}
