<?php

use phpDocumentor\Reflection\Types\This;

defined('BASEPATH') or exit('No direct script access allowed');

class Karyawan_model extends CI_Model
{
    protected $table = 'karyawan';

    public function __construct()
    {
        parent::__construct();
    }

    var $column_order = array(
        null,
        "a.FIRST_NAME",
        "a.LAST_NAME",
        "d.DISPLAY_NAME",
        "dv.DISPLAY_NAME",
        "k.DISPLAY_NAME",
        "a.TYPE_CUST",
        "a.PMC",
        "a.FCP",
        "a.PROJ_SERV",
        "a.ACS_TOOL",
        "w.WAREHOUSE_NAME",
        "a.SALDO_AWAL",
        "a.START_DATE",
        "a.END_DATE",
        "a.ACTIVE_FLAG",
        "a.DESCRIPTION"
    );

    var $column_search = array(
        null,
        "a.FIRST_NAME",
        "a.LAST_NAME",
        "d.DISPLAY_NAME",
        "dv.DISPLAY_NAME",
        "k.DISPLAY_NAME",
        "a.TYPE_CUST",
        "a.PMC",
        "a.FCP",
        "a.PROJ_SERV",
        "a.ACS_TOOL",
        "w.WAREHOUSE_NAME",
        "a.SALDO_AWAL",
        "a.START_DATE",
        "a.END_DATE",
        "a.ACTIVE_FLAG",
        "a.DESCRIPTION"
    );

    var $order = array('a.KARYAWAN_ID' => 'ASC');

    private function _get_datatables_query()
    {
        $this->db->select("a.KARYAWAN_ID,a.FIRST_NAME,a.LAST_NAME,a.DESCRIPTION,a.START_DATE,a.END_DATE,a.ACTIVE_FLAG,a.SALDO_AWAL,a.WAREHOUSE_ID,a.DEPT_ID,a.DIVISI_ID,a.KATEGORI_ID,a.TYPE_CUST,a.PMC,a.FCP,a.PROJ_SERV,a.ACS_TOOL,d.DISPLAY_NAME AS DEPARTMENT_NAME,dv.DISPLAY_NAME AS DIVISI_NAME,k.DISPLAY_NAME AS KATEGORI_NAME,w.WAREHOUSE_NAME");
        $this->db->from('karyawan a');

        // departemen
        $this->db->join('erp_lookup_value d', 'd.ERP_LOOKUP_VALUE_ID = a.DEPT_ID AND d.ACTIVE_FLAG = "Y"', 'left');
        $this->db->join('erp_lookup_set s', 's.ERP_LOOKUP_SET_ID = d.ERP_LOOKUP_SET_ID AND s.PROGRAM_CODE = "DEPARTMENT"', 'left');

        // divisi
        $this->db->join('erp_lookup_value dv', 'dv.ERP_LOOKUP_VALUE_ID = a.DIVISI_ID AND dv.ACTIVE_FLAG = "Y"', 'left');

        $this->db->join('erp_lookup_set dvs', 'dvs.ERP_LOOKUP_SET_ID = dv.ERP_LOOKUP_SET_ID AND dvs.ERP_LOOKUP_SET_NAME = "DIVISI"', 'left');

        // kategori
        $this->db->join('erp_lookup_value k', 'k.ERP_LOOKUP_VALUE_ID = a.KATEGORI_ID AND k.ACTIVE_FLAG = "Y"', 'left');

        $this->db->join('erp_lookup_set ks', 'ks.ERP_LOOKUP_SET_ID = k.ERP_LOOKUP_SET_ID AND ks.PROGRAM_CODE = "KOMISI"', 'left');

        // warehouse
        $this->db->join('warehouse w', 'w.WAREHOUSE_ID = a.WAREHOUSE_ID', 'left');

        $i = 0;
        foreach ($this->column_search as $item) {
            $global_search_value = $this->input->post('search')['value'] ?? '';
            $column_search_value = $this->input->post('columns')[$i]['search']['value'] ?? '';

            if ($column_search_value != '') {
                $this->db->like($item, $column_search_value);
            } elseif ($global_search_value != '') {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $global_search_value);
                } else {
                    $this->db->or_like($item, $global_search_value);
                }
                if (count($this->column_search) - 1 == $i) $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by(
                $this->column_order[$_POST['order']['0']['column']],
                $_POST['order']['0']['dir']
            );
        } elseif (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit(
                $_POST['length'],
                $_POST['start']
            );
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all()
    {
        $this->db->select("
            a.KARYAWAN_ID,a.FIRST_NAME,a.LAST_NAME,a.DESCRIPTION,a.START_DATE,a.END_DATE,a.ACTIVE_FLAG,a.SALDO_AWAL,a.WAREHOUSE_ID,a.DEPT_ID,a.DIVISI_ID,a.KATEGORI_ID,a.TYPE_CUST,a.PMC,a.FCP,a.PROJ_SERV,a.ACS_TOOL
            ");
        $this->db->from('karyawan a');
        return $this->db->count_all_results();
    }

    public function getById($id)
    {
        $this->db->select('k.*, b.PROGRAM_CODE1');
        $this->db->from('karyawan k');
        $this->db->join('erp_lookup_value b', 'b.ERP_LOOKUP_VALUE_ID = k.DEPT_ID', 'left');
        $this->db->join('erp_lookup_set a', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID', 'left');
        $this->db->where('a.PROGRAM_CODE', 'DEPARTMENT');
        $this->db->where('k.KARYAWAN_ID', $id);
        return $this->db->get();
    }

    public function add($post)
    {
        $params = array(
            'FIRST_NAME'  => $post['nama_depan'] ? strtoupper(htmlspecialchars($post['nama_depan'])) : null,
            'LAST_NAME'   => $post['nama_belakang'] ? strtoupper(htmlspecialchars($post['nama_belakang'])) : null,
            'DEPT_ID'     => !empty($post['bagian']) ? (int) htmlspecialchars($post['bagian']) : null,
            'DIVISI_ID'   => !empty($post['divisi']) ? (int) htmlspecialchars($post['divisi']) : null,
            'KATEGORI_ID' => !empty($post['kategori']) ? (int) htmlspecialchars($post['kategori']) : null,
            'WAREHOUSE_ID' => !empty($post['gudang_sales']) ? (int) htmlspecialchars($post['gudang_sales']) : null,
            'START_DATE'  => !empty($post['start_date']) ? htmlspecialchars($post['start_date']) : null,
            'END_DATE'    => !empty($post['end_date']) ? htmlspecialchars($post['end_date']) : null,
            'TYPE_CUST'   => !empty($post['type_cu']) ? htmlspecialchars($post['type_cu']) : null,
            'PMC'         => !empty($post['pmc']) ? htmlspecialchars($post['pmc']) : null,
            'FCP'         => !empty($post['fcp']) ? htmlspecialchars($post['fcp']) : null,
            'PROJ_SERV'   => !empty($post['pjt']) ? htmlspecialchars($post['pjt']) : null,
            'ACS_TOOL'    => !empty($post['acs']) ? htmlspecialchars($post['acs']) : null,
            'DESCRIPTION' => !empty($post['note']) ? htmlspecialchars($post['note']) : null,
            'ACTIVE_FLAG' => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'CREATED_BY'  => $this->session->userdata('id'),
            'CREATED_DATE' => date('Y-m-d H:i:s'),
        );

        $this->db->insert($this->table, $params);
        return $this->db->insert_id();
    }

    public function update_by_id($id, $post)
    {
        $params = array(
            'FIRST_NAME'  => $post['nama_depan'] ? strtoupper(htmlspecialchars($post['nama_depan'])) : null,
            'LAST_NAME'   => $post['nama_belakang'] ? strtoupper(htmlspecialchars($post['nama_belakang'])) : null,
            'DEPT_ID'     => !empty($post['bagian']) ? (int) htmlspecialchars($post['bagian']) : null,
            'DIVISI_ID'   => !empty($post['divisi']) ? (int) htmlspecialchars($post['divisi']) : null,
            'KATEGORI_ID' => !empty($post['kategori']) ? (int) htmlspecialchars($post['kategori']) : null,
            'WAREHOUSE_ID' => !empty($post['gudang_sales']) ? (int) htmlspecialchars($post['gudang_sales']) : null,
            'START_DATE'  => !empty($post['start_date']) ? htmlspecialchars($post['start_date']) : null,
            'END_DATE'    => !empty($post['end_date']) ? htmlspecialchars($post['end_date']) : null,
            'TYPE_CUST'   => !empty($post['type_cu']) ? htmlspecialchars($post['type_cu']) : null,
            'PMC'         => !empty($post['pmc']) ? htmlspecialchars($post['pmc']) : null,
            'FCP'         => !empty($post['fcp']) ? htmlspecialchars($post['fcp']) : null,
            'PROJ_SERV'   => !empty($post['pjt']) ? htmlspecialchars($post['pjt']) : null,
            'ACS_TOOL'    => !empty($post['acs']) ? htmlspecialchars($post['acs']) : null,
            'DESCRIPTION' => !empty($post['note']) ? htmlspecialchars($post['note']) : null,
            'ACTIVE_FLAG' => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'LAST_UPDATE_BY'  => $this->session->userdata('id'),
            'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
        );

        $this->db->where('KARYAWAN_ID', $id);
        $this->db->update($this->table, $params);
    }

    public function getBagian()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME as text, b.PROGRAM_CODE1")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'DEPARTMENT');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            if ($default) {
                $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('b.DISPLAY_NAME', 'ASC');
        }

        return $this->db->get();
    }

    public function getDivisi()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("v.ERP_LOOKUP_VALUE_ID as id, v.DISPLAY_NAME as text")
            ->from('erp_lookup_set s')
            ->join('erp_lookup_value v', 'v.ERP_LOOKUP_SET_ID = s.ERP_LOOKUP_SET_ID')
            ->where("s.erp_lookup_set_name", 'DIVISI');

        if ($id) {
            $this->db->where('v.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } else {
            $this->db->where('ACTIVE_FLAG', 'Y');
            if ($default) {
                $this->db->where('PRIMARY_FLAG', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('v.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('v.DISPLAY_NAME', 'ASC');
        }

        return $this->db->get();
    }

    public function getKategori()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME as text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where("a.PROGRAM_CODE", 'KOMISI');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } else {
            $this->db->where('ACTIVE_FLAG', 'Y');
            if ($default) {
                $this->db->where('b.Primary_Flag', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('b.DISPLAY_NAME', 'ASC');
        }

        return $this->db->get();
    }

    public function getGudang()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("a.WAREHOUSE_ID AS id, a.WAREHOUSE_NAME AS text")
            ->from('warehouse a')
            ->where('a.JENIS_ID = FN_GET_VAR_VALUE("BOOKED")', null, false);

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($default) {
                $this->db->where('a.PRIMARY_FLAG', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.WAREHOUSE_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('a.WAREHOUSE_NAME', 'ASC');
        }

        return $this->db->get();
    }
}
