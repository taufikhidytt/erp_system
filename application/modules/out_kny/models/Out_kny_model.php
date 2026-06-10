<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Out_kny_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
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
        $this->db->order_by('pd.PR_DETAIL_ID');

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

    public function getSupplierGrk()
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

    public function get_detail_by_po_id($po_id, $limit = null, $start = null)
    {
        $this->db->select("
            pd.PO_ID,
            pd.PO_DETAIL_ID,
            pr.PR_ID,
            prd.PR_DETAIL_ID,
            COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) Nama_Item,
            i.ITEM_CODE Kode_Item,
            pd.ENTERED_QTY Jumlah,
            pd.RECEIVED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0) Kirim_Retur,
            pd.ENTERED_QTY - (pd.RECEIVED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0)) Sisa,
            pd.ENTERED_UOM UoM,
            pd.HARGA_INPUT Harga,
            pd.SUBTOTAL Subtotal,
            pr.DOCUMENT_NO Reff_No,
            k.FIRST_NAME Sales,
            pd.NOTE Note
        ");
        $this->db->from("po_detail pd");
        $this->db->join('item i', 'pd.ITEM_ID = i.ITEM_ID');
        $this->db->join('pr_detail prd', 'pd.PR_DETAIL_ID = prd.PR_DETAIL_ID');
        $this->db->join('pr', 'prd.PR_ID = pr.PR_ID');
        $this->db->join('karyawan k', 'pr.KARYAWAN_ID = k.KARYAWAN_ID');
        $this->db->where('pd.PO_ID', $po_id);
        $this->db->order_by('pd.PO_DETAIL_ID');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_po_id($po_id)
    {
        $this->db->where('PO_ID', $po_id);
        return $this->db->count_all_results('po_detail');
    }

    public function get_detail_by_tag_konsi_id($tag_konsi_id, $limit = null, $start = null)
    {
        $this->db->select("
            pd.TAG_KONSI_ID,
            pd.TAG_KONSI_DETAIL_ID,
            prd.PO_ID,
            prd.PO_DETAIL_ID,
            td.TAG_ID,
            td.TAG_DETAIL_ID,
            COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) Nama_Item,
            i.ITEM_CODE Kode_Item,
            pd.ENTERED_QTY Jumlah,
            pd.RECEIVED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0) Terima,
            pd.ENTERED_QTY - (pd.RECEIVED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0)) Sisa,
            pd.ENTERED_UOM UoM,
            pd.HARGA_INPUT Harga,
            pd.SUBTOTAL Subtotal,
            COALESCE(a.DOCUMENT_NO, tg.DOCUMENT_NO) Batch_No,
            pd.NOTE Note
        ");
        $this->db->from('tag_konsi_detail pd');
        $this->db->join('item i', 'pd.ITEM_ID = i.ITEM_ID');
        $this->db->join('po_detail prd', 'pd.PO_DETAIL_ID = prd.PO_DETAIL_ID', 'left');
        $this->db->join('po a', 'prd.PO_ID = a.PO_ID', 'left');
        $this->db->join('tag_detail td', 'pd.TAG_DETAIL_ID = td.TAG_DETAIL_ID', 'left');
        $this->db->join('tag tg', 'td.TAG_ID = tg.TAG_ID', 'left');
        $this->db->where('pd.TAG_KONSI_ID', $tag_konsi_id);
        $this->db->order_by('pd.TAG_KONSI_DETAIL_ID', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_tag_konsi_id($tag_konsi_id)
    {
        $this->db->where('TAG_KONSI_ID', $tag_konsi_id);
        return $this->db->count_all_results('tag_konsi_detail');
    }

    public function get_detail_by_tag_id($tag_id, $limit = null, $start = null)
    {
        $this->db->select("
            pd.TAG_ID,
            pd.TAG_DETAIL_ID,
            prd.TAG_KONSI_ID,
            prd.TAG_KONSI_DETAIL_ID,
            COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) Nama_Item,
            i.ITEM_CODE Kode_Item,
            pd.ENTERED_QTY Jumlah,
            pd.DELIVERED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0) Pakai_Retur,
            pd.ENTERED_QTY - (pd.DELIVERED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0)) Sisa,
            pd.ENTERED_UOM Satuan,
            a.DOCUMENT_NO Batch_No,
            pd.NOTE Note
        ");
        $this->db->from('tag_detail pd');
        $this->db->join('item i', 'pd.ITEM_ID = i.ITEM_ID');
        $this->db->join('tag_konsi_detail prd', 'pd.TAG_KONSI_DETAIL_ID = prd.TAG_KONSI_DETAIL_ID');
        $this->db->join('tag_konsi a', 'prd.TAG_KONSI_ID = a.TAG_KONSI_ID');
        $this->db->where('pd.TAG_ID', $tag_id);
        $this->db->order_by('pd.TAG_DETAIL_ID', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_tag_id($tag_id)
    {
        $this->db->where('TAG_ID', $tag_id);
        return $this->db->count_all_results('tag_detail');
    }

    public function get_detail_by_request_qty_id($request_qty_id, $limit = null, $start = null)
    {
        $this->db->select("
            pd.REQUEST_QTY_ID,
            pd.REQUEST_QTY_DETAIL_ID,
            prd.TAG_ID,
            prd.TAG_DETAIL_ID,
            COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) Nama_Item,
            i.ITEM_CODE Kode_Item,
            pd.ENTERED_QTY Jumlah,
            pd.DELIVER_QTY / NULLIF(pd.BASE_QTY, 0) Terima,
            pd.ENTERED_QTY - (pd.DELIVER_QTY / NULLIF(pd.BASE_QTY, 0)) Sisa,
            pd.ENTERED_UOM Satuan,
            a.DOCUMENT_NO Batch_No,
            pd.NOTE Note
        ");
        $this->db->from('request_qty_detail pd');
        $this->db->join('item i', 'pd.ITEM_ID = i.ITEM_ID');
        $this->db->join('tag_detail prd', 'pd.TAG_DETAIL_ID = prd.TAG_DETAIL_ID');
        $this->db->join('tag a', 'prd.TAG_ID = a.TAG_ID');
        $this->db->where('pd.REQUEST_QTY_ID', $request_qty_id);
        $this->db->order_by('pd.REQUEST_QTY_DETAIL_ID', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function count_detail_by_request_qty_id($request_qty_id)
    {
        $this->db->where('REQUEST_QTY_ID', $request_qty_id);
        return $this->db->count_all_results('request_qty_detail');
    }

    public function get_detail_by_tag_id_receive_in_ho($tag_id, $limit = null, $start = null)
    {
        $this->db->select("
            pd.TAG_ID,
            pd.TAG_DETAIL_ID,
            prd.REQUEST_QTY_ID,
            prd.REQUEST_QTY_DETAIL_ID,
            COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) Nama_Item,
            i.ITEM_CODE Kode_Item,
            pd.ENTERED_QTY Jumlah,
            pd.DELIVERED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0) Kirim_Retur,
            pd.ENTERED_QTY - (pd.DELIVERED_ENTERED_QTY / NULLIF(pd.BASE_QTY, 0)) Sisa,
            pd.ENTERED_UOM Satuan,
            a.DOCUMENT_NO Batch_No,
            pd.NOTE Note
        ");
        $this->db->from('tag_detail pd');
        $this->db->join('item i', 'pd.ITEM_ID = i.ITEM_ID');
        $this->db->join('request_qty_detail prd', 'pd.REQUEST_QTY_DETAIL_ID = prd.REQUEST_QTY_DETAIL_ID');
        $this->db->join('request_qty a', 'prd.REQUEST_QTY_ID = a.REQUEST_QTY_ID');
        $this->db->where('pd.TAG_ID', $tag_id);
        $this->db->order_by('pd.TAG_DETAIL_ID', 'ASC');

        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }

        return $this->db->get();
    }

    public function getSupplierRts()
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

    public function get_detail_by_tag_pinjam_id($tag_pinjam_id, $limit = null, $start = null)
    {
        $sql = "
            SELECT tmp.* FROM (
                SELECT
                    tkd.TAG_PINJAM_ID,
                    tkd.TAG_PINJAM_DETAIL_ID,
                    tkd.PO_DETAIL_ID,
                    NULL AS TAG_DETAIL_ID,
                    COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) AS Nama_Item,
                    i.ITEM_CODE AS Kode_Item,
                    tkd.ENTERED_QTY AS Jumlah,
                    tkd.ENTERED_UOM AS Satuan,
                    pr.DOCUMENT_NO AS Reff_Batch_1,
                    po.DOCUMENT_NO AS Reff_Batch_2,
                    tkd.NOTE AS Note
                FROM tag_pinjam_detail tkd
                JOIN item i ON tkd.ITEM_ID = i.ITEM_ID
                JOIN po_detail pod ON tkd.PO_DETAIL_ID = pod.PO_DETAIL_ID
                JOIN po ON pod.PO_ID = po.PO_ID
                JOIN pr_detail prd ON pod.PR_DETAIL_ID = prd.PR_DETAIL_ID
                JOIN pr ON prd.PR_ID = pr.PR_ID
                WHERE tkd.TAG_PINJAM_ID = ?

                UNION ALL

                SELECT
                    tkd.TAG_PINJAM_ID,
                    tkd.TAG_PINJAM_DETAIL_ID,
                    tkd.PO_DETAIL_ID,
                    td.TAG_DETAIL_ID,
                    COALESCE(i.PART_NUMBER, i.ITEM_DESCRIPTION) AS Nama_Item,
                    i.ITEM_CODE AS Kode_Item,
                    tkd.ENTERED_QTY AS Jumlah,
                    tkd.ENTERED_UOM AS Satuan,
                    pr.DOCUMENT_NO AS Reff_Batch_1,
                    tg.DOCUMENT_NO AS Reff_Batch_2,
                    tkd.NOTE AS Note
                FROM tag_pinjam_detail tkd
                JOIN item i ON tkd.ITEM_ID = i.ITEM_ID
                JOIN tag_detail td ON tkd.TAG_DETAIL_ID = td.TAG_DETAIL_ID
                JOIN tag tg ON td.TAG_ID = tg.TAG_ID
                LEFT JOIN po_detail pod_direct ON td.PO_DETAIL_ID = pod_direct.PO_DETAIL_ID
                LEFT JOIN request_qty_detail rqd ON td.REQUEST_QTY_DETAIL_ID = rqd.REQUEST_QTY_DETAIL_ID AND rqd.PO_DETAIL_ID IS NULL
                LEFT JOIN tag_konsi_detail tdl ON rqd.TAG_KONSI_DETAIL_ID = tdl.TAG_KONSI_DETAIL_ID
                LEFT JOIN tag_detail tdi ON tdl.TAG_DETAIL_ID = tdi.TAG_DETAIL_ID
                LEFT JOIN po_detail pod_indirect ON tdi.PO_DETAIL_ID = pod_indirect.PO_DETAIL_ID
                LEFT JOIN pr_detail prd ON COALESCE(pod_direct.PR_DETAIL_ID, pod_indirect.PR_DETAIL_ID) = prd.PR_DETAIL_ID
                LEFT JOIN pr ON prd.PR_ID = pr.PR_ID
                WHERE tkd.TAG_PINJAM_ID = ?
            ) AS tmp
            ORDER BY tmp.TAG_PINJAM_DETAIL_ID
        ";

        if ($limit !== null && $start !== null) {
            $sql .= " LIMIT {$start}, {$limit}";
        }

        return $this->db->query($sql, [$tag_pinjam_id, $tag_pinjam_id]);
    }

    public function count_detail_by_tag_pinjam_id($tag_pinjam_id)
    {
        $sql = "
            SELECT COUNT(*) AS total FROM (
                SELECT tkd.TAG_PINJAM_DETAIL_ID
                FROM tag_pinjam_detail tkd
                WHERE tkd.TAG_PINJAM_ID = ?

                UNION ALL

                SELECT tkd.TAG_PINJAM_DETAIL_ID
                FROM tag_pinjam_detail tkd
                WHERE tkd.TAG_PINJAM_ID = ?
            ) AS tmp
        ";

        $query = $this->db->query($sql, [$tag_pinjam_id, $tag_pinjam_id]);
        return $query->row()->total;
    }
}
