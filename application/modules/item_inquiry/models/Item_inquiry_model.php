<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Item_inquiry_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    public function getGudang()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = '{$this->session->userdata('id')}' GROUP BY a.WAREHOUSE_ID ORDER BY IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function getItem()
    {
        $searchTerm = trim($this->input->get('q') ?? '');

        $subquery = "(SELECT SUM(QTY_AWAL + QTY_MASUK - QTY_KELUAR) 
                    FROM item_stok_konsinyasi 
                    WHERE ITEM_ID = i.ITEM_ID)";

        $this->db->select([
            'i.ITEM_ID AS id',
            'i.ITEM_CODE',
            'LEFT(i.ITEM_DESCRIPTION, 40) AS ITEM_DESCRIPTION',
            "CONCAT('[',i.ITEM_CODE,'] - ',LEFT(i.ITEM_DESCRIPTION, 40)) AS text",
            'LEFT(i.ASSY_CODE, 30) AS ASSY_CODE',
            'LEFT(e.DISPLAY_NAME, 30) AS CATEGORY',
            'i.UOM_CODE AS UOM',
            "COALESCE($subquery, 0) AS STOK",
            'mr.DISPLAY_NAME AS BRAND',
            'tipe.DISPLAY_NAME AS TIPE',
            'i.JENIS_ID'
        ], FALSE); 

        $this->db->from('item i');

        $this->db->join('ERP_LOOKUP_VALUE e', 'e.ERP_LOOKUP_VALUE_ID = i.GROUP_ID', 'inner');
        $this->db->join('ERP_LOOKUP_VALUE tipe', 'tipe.ERP_LOOKUP_VALUE_ID = i.TYPE_ID', 'inner');
        $this->db->join('ERP_LOOKUP_VALUE mr', 'mr.ERP_LOOKUP_VALUE_ID = i.MEREK_ID', 'inner');
        $this->db->join('PRICE_LIST_DETAIL b', 'b.ITEM_ID = i.ITEM_ID AND b.ACTIVE_FLAG = "Y" AND b.ENTERED_UOM = i.UOM_CODE', 'inner');

        $this->db->where('i.TYPE_ID = FN_GET_VAR_VALUE("INV")', NULL, FALSE);
        $this->db->where('i.JENIS_ID = FN_GET_VAR_VALUE("GOODS")', NULL, FALSE);

        if ($searchTerm) {
            $this->db->group_start()
                ->like('i.ITEM_CODE', $searchTerm)
                ->or_like('i.ITEM_DESCRIPTION', $searchTerm)
                ->group_end();
        }

        $this->db->order_by('i.ITEM_CODE', 'ASC');
        $this->db->limit(50);

        return $this->db->get();
    }

    public function getPeriod()
    {
        return $this->db->query("SELECT A.*, DATE_FORMAT( A.PERIOD_DATE, '%Y-%m-%d' ) PERIOD_STR, DATE_ADD( A.PERIOD_DATE, INTERVAL 1 MONTH ) TGL_BARU FROM period A ORDER BY PERIOD_NAME DESC");
    }
}
