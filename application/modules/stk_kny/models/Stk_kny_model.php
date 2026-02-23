<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Stk_kny_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    public function getGudang()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = '{$this->session->userdata('id')}' WHERE ACTIVE_FLAG = 'Y' GROUP BY a.WAREHOUSE_ID ORDER BY IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function getItem()
    {
        return $this->db->query("SELECT i.ITEM_ID, i.ITEM_CODE, LEFT(i.ITEM_DESCRIPTION, 40) AS ITEM_DESCRIPTION, LEFT(i.ASSY_CODE, 30) AS ASSY_CODE, LEFT(e.DISPLAY_NAME, 30) AS CATEGORY, i.UOM_CODE UOM, COALESCE( (SELECT SUM(QTY_AWAL + QTY_MASUK - QTY_KELUAR) FROM item_stok_konsinyasi WHERE ITEM_ID = i.ITEM_ID), 0 ) AS STOK, mr.DISPLAY_NAME AS BRAND, tipe.DISPLAY_NAME AS TIPE, i.JENIS_ID FROM item i JOIN ERP_LOOKUP_VALUE e ON e.ERP_LOOKUP_VALUE_ID = i.GROUP_ID JOIN ERP_LOOKUP_VALUE tipe ON i.TYPE_ID = tipe.ERP_LOOKUP_VALUE_ID JOIN ERP_LOOKUP_VALUE mr ON i.MEREK_ID = mr.ERP_LOOKUP_VALUE_ID JOIN PRICE_LIST_DETAIL b ON i.ITEM_ID = b.ITEM_ID AND b.ACTIVE_FLAG = 'Y' AND b.ENTERED_UOM = i.UOM_CODE WHERE i.ACTIVE_FLAG = 'Y' AND i.APPROVE_FLAG = 'Y' AND i.TYPE_ID = FN_GET_VAR_VALUE ('INV') AND i.JENIS_ID = FN_GET_VAR_VALUE ('GOODS') AND i.ITEM_KMS = 'Y' AND i.PERSON_ID IS NOT NULL ORDER BY i.ITEM_CODE");
    }

    public function getPeriod()
    {
        return $this->db->query("SELECT A.*, DATE_FORMAT( A.PERIOD_DATE, '%Y-%m-%d' ) PERIOD_STR, DATE_ADD( A.PERIOD_DATE, INTERVAL 1 MONTH ) TGL_BARU FROM period A ORDER BY PERIOD_NAME DESC");
    }
}
