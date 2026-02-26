<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Mrq_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "w.WAREHOUSE_NAME",
        "p.PERSON_NAME",
        "a.UNIT",
        "a.ITEM_DESCRIPTION",
        "a.ENTERED_UOM",
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "w.WAREHOUSE_NAME",
        "p.PERSON_NAME",
        "a.UNIT",
        "a.ITEM_DESCRIPTION",
        "a.ENTERED_UOM",
    );

    var $order = array('a.DOCUMENT_DATE' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->select("
            a.BUILD_ID,
            b.DISPLAY_NAME AS Status,
            a.DOCUMENT_NO AS No_Transaksi,
            a.DOCUMENT_REFF_NO AS No_Referensi,
            a.DOCUMENT_DATE AS Tanggal,
            w.WAREHOUSE_NAME AS Storage,
            a.PERSON_ID,
            p.PERSON_NAME AS Customer,
            a.UNIT AS Unit,
            a.ITEM_ID,
            a.ITEM_DESCRIPTION AS Nama_Item,
            a.ENTERED_UOM AS UoM
        ");
        $this->db->from('build a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);

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
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON (a.TABLE_NAME = b.TABLE_NAME) WHERE b.ERP_MENU_NAME = '{$this->uri->segment(1)}'")->row_array();

        $this->db->select("
            a.BUILD_ID,
            b.DISPLAY_NAME AS Status,
            a.DOCUMENT_NO AS No_Transaksi,
            a.DOCUMENT_REFF_NO AS No_Referensi,
            a.DOCUMENT_DATE AS Tanggal,
            w.WAREHOUSE_NAME AS Storage,
            a.PERSON_ID,
            p.PERSON_NAME AS Customer,
            a.UNIT AS Unit,
            a.ITEM_ID,
            a.ITEM_DESCRIPTION AS Nama_Item,
            a.ENTERED_UOM AS UoM
        ");
        $this->db->from('build a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        return $this->db->count_all_results();
    }

    public function get_detail_by_build_id($build_id, $limit = null, $start = null)
    {
        $sql = "SELECT
                * 
            FROM
                (
                SELECT
                    i.ITEM_DESCRIPTION AS Nama_Item,
                    i.ITEM_CODE AS Kode_Item,
                    bmd.ENTERED_QTY AS Qty,
                    bmd.ENTERED_UOM AS UoM,
                    po.DOCUMENT_NO Reff_Trx,
                    bmd.NOTE AS Note,
                    bmd.BUILD_ID,
                    bmd.BUILD_DETAIL_ID,
                    bmd.PO_DETAIL_ID,
                    NULL AS TAG_DETAIL_ID 
                FROM
                    build_detail bmd
                    JOIN item i ON bmd.ITEM_ID = i.ITEM_ID
                    JOIN po_detail pod ON bmd.PO_DETAIL_ID = pod.PO_DETAIL_ID
                    JOIN po ON pod.PO_ID = po.PO_ID 
                WHERE
                    bmd.BUILD_ID = '{$build_id}'
                    UNION ALL
                SELECT
                    i.ITEM_DESCRIPTION AS Nama_Item,
                    i.ITEM_CODE AS Kode_Item,
                    bmd.ENTERED_QTY AS Qty,
                    bmd.ENTERED_UOM AS UoM,
                    tg.DOCUMENT_NO Reff_Trx,
                    bmd.NOTE AS Note,
                    bmd.BUILD_ID,
                    bmd.BUILD_DETAIL_ID,
                    NULL AS PO_DETAIL_ID,
                    bmd.TAG_DETAIL_ID 
                FROM
                    build_detail bmd
                    JOIN item i ON bmd.ITEM_ID = i.ITEM_ID
                    JOIN tag_detail td ON bmd.TAG_DETAIL_ID = td.TAG_DETAIL_ID
                    JOIN tag tg ON td.TAG_ID = tg.TAG_ID 
                WHERE
                    bmd.BUILD_ID = '{$build_id}'
                    
                ) AS tmp 
            ORDER BY
                tmp.BUILD_DETAIL_ID";

        if ($limit !== null && $start !== null) {
            $sql .= " LIMIT {$start}, {$limit}";
        }

        return $this->db->query($sql);
    }

    public function count_detail_by_build_id($build_id)
    {
        return $this->db
            ->where('BUILD_ID', $build_id)
            ->count_all_results('build_detail');
    }

    public function get_storage()
    {
        return $this->db->query("SELECT DISTINCT
                    a.WAREHOUSE_ID,
                    a.ADDRESS_ID,
                    a.PRIMARY_FLAG,
                    a.WAREHOUSE_NAME,
                    g.PRIMARY_FLAG AS USER_PRIMARY_FLAG 
                FROM
                    warehouse a
                    LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID 
                    AND g.ERP_USER_ID = '{$this->session->userdata('id')}'
                WHERE
                    a.ACTIVE_FLAG = 'Y' 
                ORDER BY
                CASE
                        WHEN g.PRIMARY_FLAG IS NOT NULL THEN
                        g.PRIMARY_FLAG ELSE a.PRIMARY_FLAG 
                    END DESC,
                    a.WAREHOUSE_NAME");
    }

    public function get_item_finish_goods()
    {
        return $this->db->query("SELECT DISTINCT
                    i.ITEM_ID,
                    b.BUILD_ID,
                    b.DOCUMENT_NO,
                    i.ITEM_CODE,
                    LEFT ( i.ITEM_DESCRIPTION, 40 ) AS ITEM_DESCRIPTION,
                    LEFT ( i.ASSY_CODE, 30 ) AS ASSY_CODE,
                    LEFT ( e.DISPLAY_NAME, 30 ) AS CATEGORY,
                    i.UOM_CODE,
                    COALESCE ( s.STOK, 0 ) AS STOK,
                    mr.DISPLAY_NAME AS BRAND,
                    tipe.DISPLAY_NAME AS TIPE,
                    i.JENIS_ID 
                FROM
                    item i
                    JOIN erp_lookup_value e ON e.ERP_LOOKUP_VALUE_ID = i.GROUP_ID
                    JOIN erp_lookup_value tipe ON i.TYPE_ID = tipe.ERP_LOOKUP_VALUE_ID
                    JOIN erp_lookup_value mr ON i.MEREK_ID = mr.ERP_LOOKUP_VALUE_ID
                    LEFT JOIN ( SELECT ITEM_ID, SUM( QTY_AWAL + QTY_MASUK - QTY_KELUAR ) AS STOK FROM item_stok GROUP BY ITEM_ID ) s ON i.ITEM_ID = s.ITEM_ID
                    LEFT JOIN build b ON i.ITEM_ID = b.ITEM_ID 
                WHERE
                    i.ACTIVE_FLAG = 'Y' 
                    AND i.APPROVE_FLAG = 'Y' 
                    AND i.TYPE_ID = FN_GET_VAR_VALUE ( 'INV' ) 
                    AND i.JENIS_ID = FN_GET_VAR_VALUE ( 'GOODS' ) 
                    AND i.ITEM_KMS = 'N' 
                ORDER BY
                    i.ITEM_CODE");
    }

    public function get_ship_to()
    {
        return $this->db->query("SELECT
                a.POINT,
                a.PERSON_ID,
                a.PERSON_CODE,
                a.PERSON_NAME,
                a.LIMIT_PIUTANG,
                a.TUNAI_FLAG,
                b.PAYMENT_TERM_ID,
                COALESCE ( COALESCE ( b.NUMBER_DAYS, 0 ) + COALESCE ( pp.LIMIT_DAY, 0 ), a.CUSTOM1 ) AS CUSTOM1,
                a.CUSTOM2,
                a.TIPE_HARGA_JUAL,
                b.PAYMENT_TERM_NAME,
                b.NUMBER_DAYS,
                k.FIRST_NAME,
                a.KARYAWAN_ID,
                a.MATA_UANG_ID,
                m.MATA_UANG_NAME,
                COALESCE ( a.PERSON_NAME2, a.PERSON_NAME ) AS PERSON_NAME2,
                ps.ADDRESS1,
                ps.SITE_NAME,
                ps.PERSON_SITE_ID,
            CASE
                    
                    WHEN ti.DESCRIPTION IS NULL 
                    OR ti.DESCRIPTION = '' THEN
                        0 
                        WHEN ti.DESCRIPTION REGEXP '^[0-9]+\.?[0-9]*$' THEN
                        CAST(
                        ti.DESCRIPTION AS DECIMAL ( 19, 4 )) ELSE 0 
                    END AS cb,
                    ps.TAX_NAME,
                    a.PPN_CODE,
                    a.APPROVE_FLAG 
                FROM
                    person a
                    INNER JOIN payment_term b ON a.DEFAULT_TERM_ID = b.PAYMENT_TERM_ID
                    INNER JOIN person_site ps ON a.PERSON_ID = ps.PERSON_ID
                    LEFT JOIN karyawan k ON a.KARYAWAN_ID = k.KARYAWAN_ID
                    LEFT JOIN mata_uang m ON m.MATA_UANG_ID = a.MATA_UANG_ID
                    LEFT JOIN erp_lookup_value ti ON a.TIPE_CUSTOMER_ID = ti.ERP_LOOKUP_VALUE_ID
                    LEFT JOIN person_day pp ON a.PERSON_ID = pp.PERSON_ID 
                    AND pp.MEREK_ID IS NULL 
                    AND pp.GROUP_ID IS NULL 
                WHERE
                    a.ACTIVE_FLAG = 'Y' 
                    AND a.FLAG_SUPP = 0 
            ORDER BY
                a.PERSON_NAME");
    }

    public function get_build_id($id)
    {
        $this->db->from('build');
        $this->db->where('BUILD_ID', $id);
        return $this->db->get();
    }

    public function delete($id)
    {
        $this->db->where('BUILD_ID', $id);
        $this->db->delete('build_detail');

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }

    public function updateStatus($id, $status)
    {
        $params = array(
            'STATUS_ID' => $status,
        );
        $this->db->where('BUILD_ID', $id);
        $this->db->update('build', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }
}
