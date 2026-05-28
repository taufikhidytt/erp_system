<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Asset_model extends CI_Model
{
    protected $table = 'asset';

    public function __construct()
    {
        parent::__construct();
    }

    var $column_order = array(
        null,
        "a.ASSET_CODE",
        "a.ASSET_NAME",
        "a.ENTERED_QTY",
        "b.DISPLAY_NAME",
        "a.NILAI_ASSET",
        "a.SUSUT_YEAR",
        "coa.COA_NAME",
        "coa_debit.COA_NAME",
        "coa_kredit.COA_NAME",
        "a.INTANTANGIBLE_ASSET",
        "a.UMUR_ASSET",
        "a.RATE",
        "d.DISPLAY_NAME",
        "a.BUYING_DATE",
        "a.USING_DATE",
        "a.ACTIVE_FLAG",
        "a.NOTE"
    );

    var $column_search = array(
        null,
        "a.ASSET_CODE",
        "a.ASSET_NAME",
        "a.ENTERED_QTY",
        "b.DISPLAY_NAME",
        "a.NILAI_ASSET",
        "a.SUSUT_YEAR",
        "coa.COA_NAME",
        "coa_debit.COA_NAME",
        "coa_kredit.COA_NAME",
        "a.INTANTANGIBLE_ASSET",
        "a.UMUR_ASSET",
        "a.RATE",
        "d.DISPLAY_NAME",
        "a.BUYING_DATE",
        "a.USING_DATE",
        "a.ACTIVE_FLAG",
        "a.NOTE"
    );

    var $order = array('a.ASSET_ID' => 'ASC');

    private function _get_datatables_query()
    {
        $this->db->select("a.*, b.DISPLAY_NAME as metode_depresiasi, coa.COA_NAME, coa_debit.COA_NAME as name_debit, coa_kredit.COA_NAME as name_kredit, d.DISPLAY_NAME as nama_tipe_asset");
        $this->db->from('asset a');

        // Metode Depresiasi
        $this->db->join('erp_lookup_value b', 'a.metode_depresiasi_id = b.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_set c', 'c.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID', 'left');

        // asset
        $this->db->join('coa coa', 'a.COA_ID = coa.COA_ID', 'left');
        $this->db->join('account ac', 'ac.ACCOUNT_ID = coa.ACCOUNT_ID', 'left');

        // debit
        $this->db->join('coa coa_debit', 'a.COA_DEBET_ID = coa_debit.COA_ID', 'left');
        $this->db->join('account acc', 'acc.ACCOUNT_ID = coa_debit.ACCOUNT_ID', 'left');

        // kredit
        $this->db->join('coa coa_kredit', 'a.COA_KREDIT_ID = coa_kredit.COA_ID', 'left');
        $this->db->join('account ac_kredit', 'ac_kredit.ACCOUNT_ID = coa_kredit.ACCOUNT_ID', 'left');

        // tipe asset
        $this->db->join('erp_lookup_value d', 'a.asset_type_id = d.ERP_LOOKUP_VALUE_ID', 'left');

        $this->db->join('erp_lookup_set e', 'e.ERP_LOOKUP_SET_ID = d.ERP_LOOKUP_SET_ID', 'left');

        $this->db->where('e.PROGRAM_CODE', 'TIPE_ASSET');;

        $this->db->where('c.PROGRAM_CODE', 'DEPRESIASI');

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
        $this->db->select("a.*, b.DISPLAY_NAME as metode_depresiasi, coa.COA_NAME, coa_debit.COA_NAME as name_debit, coa_kredit.COA_NAME as name_kredit, d.DISPLAY_NAME as nama_tipe_asset");
        $this->db->from('asset a');

        // Metode Depresiasi
        $this->db->join('erp_lookup_value b', 'a.metode_depresiasi_id = b.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_set c', 'c.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID', 'left');

        // asset
        $this->db->join('coa coa', 'a.COA_ID = coa.COA_ID', 'left');
        $this->db->join('account ac', 'ac.ACCOUNT_ID = coa.ACCOUNT_ID', 'left');

        // debit
        $this->db->join('coa coa_debit', 'a.COA_DEBET_ID = coa_debit.COA_ID', 'left');
        $this->db->join('account acc', 'acc.ACCOUNT_ID = coa_debit.ACCOUNT_ID', 'left');

        // kredit
        $this->db->join('coa coa_kredit', 'a.COA_KREDIT_ID = coa_kredit.COA_ID', 'left');
        $this->db->join('account ac_kredit', 'ac_kredit.ACCOUNT_ID = coa_kredit.ACCOUNT_ID', 'left');

        // tipe asset
        $this->db->join('erp_lookup_value d', 'a.asset_type_id = d.ERP_LOOKUP_VALUE_ID', 'left');

        $this->db->join('erp_lookup_set e', 'e.ERP_LOOKUP_SET_ID = d.ERP_LOOKUP_SET_ID', 'left');

        $this->db->where('e.PROGRAM_CODE', 'TIPE_ASSET');;

        $this->db->where('c.PROGRAM_CODE', 'DEPRESIASI');
        return $this->db->count_all_results();
    }

    public function getById($id)
    {
        $this->db->from($this->table);
        $this->db->where('ASSET_ID', $id);
        return $this->db->get();
    }

    public function add($post)
    {
        $params = array(
            'ASSET_CODE'  => $post['kode_asset'] ? htmlspecialchars($post['kode_asset']) : null,
            'ASSET_NAME'   => $post['nama_asset'] ? htmlspecialchars($post['nama_asset']) : null,
            'ENTERED_QTY'     => !empty($post['qty']) ? htmlspecialchars($post['qty']) : null,
            'METODE_DEPRESIASI_ID'   => !empty($post['metode_depresiasi']) ? (int) htmlspecialchars($post['metode_depresiasi']) : null,
            'NILAI_ASSET' => !empty($post['nilai_asset']) ? htmlspecialchars($post['nilai_asset']) : null,
            'SUSUT_YEAR' => !empty($post['nilai_penyusutan']) ? htmlspecialchars($post['nilai_penyusutan']) : null,
            'BUYING_DATE'  => !empty($post['start_date']) ? htmlspecialchars($post['start_date']) : null,
            'USING_DATE'    => !empty($post['end_date']) ? htmlspecialchars($post['end_date']) : null,
            'COA_ID'   => !empty($post['asset']) ? (int) htmlspecialchars($post['asset']) : null,
            'COA_DEBET_ID'         => !empty($post['debet']) ? (int) htmlspecialchars($post['debet']) : null,
            'COA_KREDIT_ID'         => !empty($post['kredit']) ? (int) htmlspecialchars($post['kredit']) : null,
            'UMUR_ASSET'   => !empty($post['umur_asset']) ? htmlspecialchars($post['umur_asset']) : null,
            'RATE'    => !empty($post['rate_depresiasi']) ? htmlspecialchars($post['rate_depresiasi']) : null,
            'ASSET_TYPE_ID'    => !empty($post['tipe_asset']) ? (int) htmlspecialchars($post['tipe_asset']) : null,
            'NOTE' => !empty($post['note']) ? htmlspecialchars($post['note']) : null,
            'INTANTANGIBLE_ASSET' => isset($post['instangible_asset']) && $post['instangible_asset'] == 'Y' ? 'Y' : 'N',
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
            'ASSET_CODE'  => $post['kode_asset'] ? htmlspecialchars($post['kode_asset']) : null,
            'ASSET_NAME'   => $post['nama_asset'] ? htmlspecialchars($post['nama_asset']) : null,
            'ENTERED_QTY'     => !empty($post['qty']) ? htmlspecialchars($post['qty']) : null,
            'METODE_DEPRESIASI_ID'   => !empty($post['metode_depresiasi']) ? (int) htmlspecialchars($post['metode_depresiasi']) : null,
            'NILAI_ASSET' => !empty($post['nilai_asset']) ? htmlspecialchars($post['nilai_asset']) : null,
            'SUSUT_YEAR' => !empty($post['nilai_penyusutan']) ? htmlspecialchars($post['nilai_penyusutan']) : null,
            'BUYING_DATE'  => !empty($post['start_date']) ? htmlspecialchars($post['start_date']) : null,
            'USING_DATE'    => !empty($post['end_date']) ? htmlspecialchars($post['end_date']) : null,
            'COA_ID'   => !empty($post['asset']) ? (int) htmlspecialchars($post['asset']) : null,
            'COA_DEBET_ID'         => !empty($post['debet']) ? (int) htmlspecialchars($post['debet']) : null,
            'COA_KREDIT_ID'         => !empty($post['kredit']) ? (int) htmlspecialchars($post['kredit']) : null,
            'UMUR_ASSET'   => !empty($post['umur_asset']) ? htmlspecialchars($post['umur_asset']) : null,
            'RATE'    => !empty($post['rate_depresiasi']) ? htmlspecialchars($post['rate_depresiasi']) : null,
            'ASSET_TYPE_ID'    => !empty($post['tipe_asset']) ? (int) htmlspecialchars($post['tipe_asset']) : null,
            'NOTE' => !empty($post['note']) ? htmlspecialchars($post['note']) : null,
            'INTANTANGIBLE_ASSET' => isset($post['instangible_asset']) && $post['instangible_asset'] == 'Y' ? 'Y' : 'N',
            'ACTIVE_FLAG' => isset($post['active_flag']) && $post['active_flag'] == 'Y' ? 'Y' : 'N',
            'LAST_UPDATE_BY'  => $this->session->userdata('id'),
            'LAST_UPDATE_DATE' => date('Y-m-d H:i:s'),
        );

        $this->db->where('ASSET_ID', $id);
        $this->db->update($this->table, $params);
    }

    public function getMetodeDepresiasi()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME as text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID= b.ERP_LOOKUP_SET_ID', 'left')
            ->where('PROGRAM_CODE', 'DEPRESIASI');

        if ($id) {
            $this->db->where('ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } else {
            $this->db->where('ACTIVE_FLAG', 'Y');
            if ($default) {
                $this->db->where('PRIMARY_FLAG', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('b.DISPLAY_NAME', 'ASC');
        }

        return $this->db->get();
    }

    public function getAsset()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("COA_ID as id, COA_NAME as text")
            ->from('coa a')
            ->join('account ac', 'ac.ACCOUNT_ID = a.ACCOUNT_ID', 'left');

        if ($id) {
            $this->db->where('COA_ID', $id)->limit(1);
        } else {
            $this->db->where('a.active_flag', 'Y');
            if ($default) {
                $this->db->where('PRIMARY_FLAG', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('COA_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('COA_CODE', 'ASC');
        }

        return $this->db->get();
    }

    public function getDebet()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("COA_ID as id, COA_NAME as text")
            ->from('coa a')
            ->join('account ac', 'ac.ACCOUNT_ID = a.ACCOUNT_ID');

        if ($id) {
            $this->db->where('COA_ID', $id)->limit(1);
        } else {
            $this->db->where('a.active_flag', 'Y');
            if ($default) {
                $this->db->where('b.Primary_Flag', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('COA_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('COA_CODE', 'ASC');
        }

        return $this->db->get();
    }

    public function getKredit()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("COA_ID as id, COA_NAME as text")
            ->from('coa a')
            ->join('account ac', 'ac.ACCOUNT_ID = a.ACCOUNT_ID');

        if ($id) {
            $this->db->where('COA_ID', $id)->limit(1);
        } else {
            $this->db->where('a.active_flag', 'Y');
            if ($default) {
                $this->db->where('a.PRIMARY_FLAG', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('COA_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('a.COA_CODE', 'ASC');
        }

        return $this->db->get();
    }

    public function getTipeAsset()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME as text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('PROGRAM_CODE', 'TIPE_ASSET');

        if ($id) {
            $this->db->where('ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } else {
            $this->db->where('ACTIVE_FLAG', 'Y');
            if ($default) {
                $this->db->where('PRIMARY_FLAG', 'Y')->limit(1);
            } else if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->order_by('b.DISPLAY_NAME', 'ASC');
        }

        return $this->db->get();
    }
}
