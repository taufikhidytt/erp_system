<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Out_kny_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    public function get_detail_by_pr_id($pr_id, $limit = null, $start = null)
    {
        $this->db->select("
            pd.PR_ID,
            pd.PR_DETAIL_ID,
            COALESCE(
                i.PART_NUMBER,
                i.ITEM_DESCRIPTION
            ) Nama_Item,
            i.ITEM_CODE Kode_Item,
            pd.ENTERED_QTY Jumlah,
            pd.RECEIVED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0) Terima,
            pd.ENTERED_QTY - (
                pd.RECEIVED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0)
            ) Sisa,
            pd.ENTERED_UOM UoM,
            pd.HARGA_INPUT Harga,
            pd.SUBTOTAL Subtotal,
            pd.NOTE Note
        ");
        $this->db->from("pr_detail pd");
        $this->db->join('item i', 'pd.ITEM_ID = i.ITEM_ID');
        $this->db->where('pd.PR_ID', $pr_id);

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_pr_id($pr_id)
    {
        $this->db->where('PR_ID', $pr_id);
        return $this->db->count_all_results('pr_detail');
    }

    public function getSupplier()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("a.PERSON_ID as id, a.PERSON_NAME Supplier, a.PERSON_CODE Kode, CONCAT('[',a.PERSON_CODE,'] - ',a.PERSON_NAME) as text")
            ->from('person a')
            ->join('person_site b', 'a.PERSON_ID = b.PERSON_ID')
            ->where('a.FLAG_SUPP', 1)
            ->group_by('a.PERSON_ID')
            ->order_by('a.PERSON_NAME');

        if ($id) {
            $this->db->where('a.PERSON_ID', $id)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.PERSON_NAME', $searchTerm)
                    ->or_like('a.PERSON_CODE', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
}
