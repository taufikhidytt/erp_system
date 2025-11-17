<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        'null',
        'null',
        'i.ITEM_CODE',
        'i.ITEM_DESCRIPTION',
        'i.PART_NUMBER',
        'i.UOM_CODE',
        'a.DISPLAY_NAME',
        'b.DISPLAY_NAME',
        'c.DISPLAY_NAME',
        'd.DISPLAY_NAME',
        'e.DISPLAY_NAME',
        'f.DISPLAY_NAME',
        'i.PRICE_LAST_BUY',
        'i.PRICE_LAST_SELL',
        'i.LEAD_TIME',
        'i.ITEM_KMS',
        'i.APPROVE_FLAG',
        'i.ACTIVE_FLAG'
    );

    var $column_search = array(
        'i.ITEM_ID',
        'i.ITEM_CODE',
        'i.ITEM_DESCRIPTION',
        'i.PART_NUMBER',
        'i.UOM_CODE',
        'a.DISPLAY_NAME',
        'b.DISPLAY_NAME',
        'c.DISPLAY_NAME',
        'd.DISPLAY_NAME',
        'e.DISPLAY_NAME',
        'f.DISPLAY_NAME',
        'i.PRICE_LAST_BUY',
        'i.PRICE_LAST_SELL',
        'i.LEAD_TIME',
        'i.ITEM_KMS',
        'i.APPROVE_FLAG',
        'i.ACTIVE_FLAG'
    );

    var $order = array('i.ITEM_ID' => 'DESC');

    private function _get_datatables_query()
    {
        $this->db->select('
            i.ITEM_ID AS ID,
            i.ITEM_CODE AS KODE_ITEM,
            LEFT(i.ITEM_DESCRIPTION, 30) AS NAMA_ITEM,
            i.PART_NUMBER,
            i.UOM_CODE AS UOM,
            a.DISPLAY_NAME AS JENIS,
            b.DISPLAY_NAME AS KATEGORY,
            c.DISPLAY_NAME AS MADE_IN,
            d.DISPLAY_NAME AS KOMODITI,
            e.DISPLAY_NAME AS BRAND,
            f.DISPLAY_NAME AS TRADE,
            i.PRICE_LAST_BUY,
            i.PRICE_LAST_SELL,
            i.LEAD_TIME,
            i.ITEM_KMS AS KONSY,
            i.APPROVE_FLAG AS APPROVED,
            i.ACTIVE_FLAG AS STATUS
        ');
        $this->db->from('item i');
        $this->db->join('erp_lookup_value a', 'i.JENIS_ID = a.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value b', 'i.GROUP_ID = b.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value c', 'i.MADE_IN_ID = c.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value d', 'i.TIPE_ID = d.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value e', 'i.MEREK_ID = e.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value f', 'i.TYPE_ID = f.ERP_LOOKUP_VALUE_ID', 'left');

        $i = 0;
        foreach ($this->column_search as $i => $item) {
            if ($item === 'null') continue;

            $search_value = $_POST['columns'][$i + 1]['search']['value'] ?? '';
            if ($search_value != '') {
                $lower = strtolower(trim($search_value));
                $search_value_db = $search_value;

                if (in_array($lower, ['yes', 'y'])) {
                    $search_value_db = 'Y';
                } elseif (in_array($lower, ['no', 'n'])) {
                    $search_value_db = 'N';
                }

                if (!isset($first_like)) {
                    $this->db->group_start();
                    $this->db->like($item, $search_value_db);
                    $first_like = true;
                } else {
                    $this->db->or_like($item, $search_value_db);
                }
            }
            $i++;
        }

        if (isset($first_like)) {
            $this->db->group_end();
        }

        if (isset($_POST['order'])) {
            $col_index = $_POST['order'][0]['column'];
            if (isset($this->column_order[$col_index]) && $this->column_order[$col_index] != null) {
                $this->db->order_by($this->column_order[$col_index], $_POST['order'][0]['dir']);
            }
        } else if (isset($this->order)) {
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
        $this->db->select('
            i.ITEM_ID AS ID,
            i.ITEM_CODE KODE_ITEM,
            LEFT(i.ITEM_DESCRIPTION, 30) NAMA_ITEM,
            i.PART_NUMBER PART_NUMBER,
            i.UOM_CODE UOM,
            a.DISPLAY_NAME JENIS,
            b.DISPLAY_NAME KATEGORY,
            c.DISPLAY_NAME MADE_IN,
            d.DISPLAY_NAME KOMODITI,
            e.DISPLAY_NAME BRAND,
            f.DISPLAY_NAME TRADE,
            i.PRICE_LAST_BUY,
            i.PRICE_LAST_SELL,
            i.LEAD_TIME,
            i.ITEM_KMS KONSY,
            i.APPROVE_FLAG APPROVED,
            i.ACTIVE_FLAG STATUS');
        $this->db->from('item i');
        $this->db->join('erp_lookup_value a', 'i.JENIS_ID = a.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value b', 'i.GROUP_ID = b.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value c', 'i.MADE_IN_ID = c.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value d', 'i.TIPE_ID = d.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value e', 'i.MEREK_ID = e.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value f', 'i.TYPE_ID = f.ERP_LOOKUP_VALUE_ID', 'left');
        return $this->db->count_all_results();
    }

    public function getBrand()
    {
        return $this->db->query("SELECT b.ERP_LOOKUP_VALUE_ID, b.DISPLAY_NAME Brand_Name, b.DESCRIPTION Brand_Code, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'MEREK' AND b.ACTIVE_FLAG = 'Y' ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");
    }

    public function getCategory()
    {
        return $this->db->query("SELECT b.ERP_LOOKUP_VALUE_ID, b.DISPLAY_NAME Category_Name, b.DESCRIPTION Category_Code, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'GROUP' AND b.ACTIVE_FLAG = 'Y' ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");
    }

    public function getUom()
    {
        return $this->db->query("SELECT a.* FROM uom a WHERE a.ACTIVE_FLAG = 'Y' ORDER BY a.PRIMARY_FLAG DESC, a.UOM_CODE");
    }

    public function getType()
    {
        return $this->db->query("SELECT b.DISPLAY_NAME Trade_Type, b.DESCRIPTION Trade_Note, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'TYPEINVENTORY' AND b.ACTIVE_FLAG = 'Y' ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");
    }

    public function getRak()
    {
        return $this->db->query("SELECT b.DISPLAY_NAME Grade, b.DESCRIPTION Note, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'RAK' AND b.ACTIVE_FLAG = 'Y' ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");
    }

    public function getMadeIn()
    {
        return $this->db->query("SELECT b.DISPLAY_NAME Made_In, b.DESCRIPTION Note, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'MADE_IN' AND b.ACTIVE_FLAG = 'Y' ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");
    }

    public function getKomoditi()
    {
        return $this->db->query("SELECT b.DISPLAY_NAME Komoditi, b.DESCRIPTION Note, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'TIPE' AND b.ACTIVE_FLAG = 'Y' ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");
    }

    public function getJenis()
    {
        return $this->db->query("SELECT b.DISPLAY_NAME Jenis_Item, b.DESCRIPTION Note, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'JENIS' AND b.ACTIVE_FLAG = 'Y' ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");
    }

    public function getGrade()
    {
        return $this->db->query("SELECT b.DISPLAY_NAME Grade, b.DESCRIPTION Note, b.PRIMARY_FLAG Default_Flag, b.ERP_LOOKUP_VALUE_ID FROM erp_lookup_set a INNER JOIN erp_lookup_value b ON ( a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID ) WHERE a.PROGRAM_CODE = 'GRADE' AND b.ACTIVE_FLAG = 'Y' ORDER BY b.PRIMARY_FLAG DESC, b.DISPLAY_NAME");
    }

    public function getSupplier()
    {
        return $this->db->query("SELECT a.PERSON_ID, a.PERSON_NAME Supplier, a.PERSON_CODE Kode FROM person a JOIN person_site b ON (a.PERSON_ID = b.PERSON_ID) WHERE a.FLAG_SUPP = 1 AND a.ACTIVE_FLAG = 'Y' GROUP BY a.PERSON_ID ORDER BY a.PERSON_NAME");
    }

    public function add($post)
    {
        date_default_timezone_set('Asia/Jakarta');
        $params = array(
            'ITEM_CODE'         => $post['item_code'] ? htmlspecialchars($post['item_code']) : null,
            'MEREK_ID'          => $post['brand'] ? htmlspecialchars($post['brand']) : null,
            'GROUP_ID'          => $post['category'] ? htmlspecialchars($post['category']) : null,
            'PART_NUMBER'       => $post['part_number'] ? htmlspecialchars($post['part_number']) : null,
            'ITEM_DESCRIPTION'  => $post['description'] ? htmlspecialchars($post['description']) : null,
            'ASSY_CODE'         => $post['assy_code'] ? htmlspecialchars($post['assy_code']) : null,
            'UOM_CODE'          => $post['satuan'] ? htmlspecialchars($post['satuan']) : null,
            'TYPE_ID'           => $post['type'] ? htmlspecialchars($post['type']) : null,
            'MIN_STOCK'         => $post['min_stock'] ? htmlspecialchars($post['min_stock']) : null,
            'LEAD_TIME'         => $post['lead_time'] ? htmlspecialchars($post['lead_time']) : null,
            'LOKASI'            => $post['lokasi'] ? htmlspecialchars($post['lokasi']) : null,
            'LOKASI_ID'         => $post['rak'] ? htmlspecialchars($post['rak']) : null,
            'PANJANG'           => $post['length'] ? htmlspecialchars($post['length']) : null,
            'CUSTOM1'           => 'M',
            'LEBAR'             => $post['width'] ? htmlspecialchars($post['width']) : null,
            'CUSTOM2'           => 'M',
            'TINGGI'            => $post['height'] ? htmlspecialchars($post['height']) : null,
            'CUSTOM3'           => 'M',
            'BERAT'             => $post['weight'] ? htmlspecialchars($post['weight']) : null,
            'CUSTOM4'           => $post['satuan_weight'] ? htmlspecialchars($post['satuan_weight']) : null,
            'M3'                => $post['kubikasi'] ? htmlspecialchars($post['kubikasi']) : null,
            'MADE_IN_ID'        => $post['made_in'] ? htmlspecialchars($post['made_in']) : null,
            'TIPE_ID'           => $post['komoditi'] ? htmlspecialchars($post['komoditi']) : null,
            'JENIS_ID'          => $post['jenis'] ? htmlspecialchars($post['jenis']) : null,
            'GRADE_ID'          => $post['grade'] ? htmlspecialchars($post['grade']) : null,
            'PERSON_ID'         => $post['supplier'] ? htmlspecialchars($post['supplier']) : null,
            'OBSOLETE_FLAG'     => $post['obsolete'] ? htmlspecialchars($post['obsolete']) : null,
            'PRODUK_BARU'       => $post['new_product_name'] ? htmlspecialchars($post['new_product_name']) : null,
            'HPP_AWAL'          => $post['hpp'] ? htmlspecialchars($post['hpp']) : null,
            'NOTE'              => $post['keterangan'] ? htmlspecialchars($post['keterangan']) : null,
            'MOQ'               => $post['min_order_quantity'] ? htmlspecialchars($post['min_order_quantity']) : null,
            'CUSTOM5'           => $post['satuan2'] ? htmlspecialchars($post['satuan2']) : null,
            'ITEM_KMS'          => $post['konsinyasi'] ? htmlspecialchars($post['konsinyasi']) : null,
            'ACTIVE_FLAG'       => $post['status_flag'] ? htmlspecialchars($post['status_flag']) : null,
            'CREATED_BY'        => $this->session->userdata('id'),
            'CREATED_DATE'      => date('Y-m-d H:i:s'),
        );
        $this->db->insert('item', $params);
    }

    public function getItemId($id)
    {
        $this->db->from('item');
        $this->db->where('item_id', $id);
        return $this->db->get();
    }

    public function update($post)
    {
        date_default_timezone_set('Asia/Jakarta');
        $params = array(
            'MEREK_ID'          => $post['brand'] ? htmlspecialchars($post['brand']) : null,
            'GROUP_ID'          => $post['category'] ? htmlspecialchars($post['category']) : null,
            'PART_NUMBER'       => $post['part_number'] ? htmlspecialchars($post['part_number']) : null,
            'ITEM_DESCRIPTION'  => $post['description'] ? htmlspecialchars($post['description']) : null,
            'ASSY_CODE'         => $post['assy_code'] ? htmlspecialchars($post['assy_code']) : null,
            'UOM_CODE'          => $post['satuan'] ? htmlspecialchars($post['satuan']) : null,
            'TYPE_ID'           => $post['type'] ? htmlspecialchars($post['type']) : null,
            'MIN_STOCK'         => $post['min_stock'] ? htmlspecialchars($post['min_stock']) : null,
            'LEAD_TIME'         => $post['lead_time'] ? htmlspecialchars($post['lead_time']) : null,
            'LOKASI'            => $post['lokasi'] ? htmlspecialchars($post['lokasi']) : null,
            'LOKASI_ID'         => $post['rak'] ? htmlspecialchars($post['rak']) : null,
            'PANJANG'           => $post['length'] ? htmlspecialchars($post['length']) : null,
            'CUSTOM1'           => 'M',
            'LEBAR'             => $post['width'] ? htmlspecialchars($post['width']) : null,
            'CUSTOM2'           => 'M',
            'TINGGI'            => $post['height'] ? htmlspecialchars($post['height']) : null,
            'CUSTOM3'           => 'M',
            'BERAT'             => $post['weight'] ? htmlspecialchars($post['weight']) : null,
            'CUSTOM4'           => $post['satuan_weight'] ? htmlspecialchars($post['satuan_weight']) : null,
            'M3'                => $post['kubikasi'] ? htmlspecialchars($post['kubikasi']) : null,
            'MADE_IN_ID'        => $post['made_in'] ? htmlspecialchars($post['made_in']) : null,
            'TIPE_ID'           => $post['komoditi'] ? htmlspecialchars($post['komoditi']) : null,
            'JENIS_ID'          => $post['jenis'] ? htmlspecialchars($post['jenis']) : null,
            'GRADE_ID'          => $post['grade'] ? htmlspecialchars($post['grade']) : null,
            'PERSON_ID'         => $post['supplier'] ? htmlspecialchars($post['supplier']) : null,
            'OBSOLETE_FLAG'     => $post['obsolete'] ? htmlspecialchars($post['obsolete']) : null,
            'PRODUK_BARU'       => $post['new_product_name'] ? htmlspecialchars($post['new_product_name']) : null,
            'HPP_AWAL'          => $post['hpp'] ? htmlspecialchars($post['hpp']) : null,
            'NOTE'              => $post['keterangan'] ? htmlspecialchars($post['keterangan']) : null,
            'MOQ'               => $post['min_order_quantity'] ? htmlspecialchars($post['min_order_quantity']) : null,
            'CUSTOM5'           => $post['satuan2'] ? htmlspecialchars($post['satuan2']) : null,
            'ITEM_KMS'          => $post['konsinyasi'] ? htmlspecialchars($post['konsinyasi']) : null,
            'ACTIVE_FLAG'       => $post['status_flag'] ? htmlspecialchars($post['status_flag']) : null,
            'COA_ID'            => $post['acc_persediaan'] ? htmlspecialchars($post['acc_persediaan']) : null,
            'COA_SUSPEND_ID'    => $post['acc_utang_suspend'] ? htmlspecialchars($post['acc_utang_suspend']) : null,
            'COA_HPP_ID'        => $post['acc_hpp'] ? htmlspecialchars($post['acc_hpp']) : null,
            'COA_JUAL_ID'       => $post['acc_penjualan_barang'] ? htmlspecialchars($post['acc_penjualan_barang']) : null,
            'COA_RET_JUAL_ID'   =>
            'COA_RET_BELI_ID'
            'COA_DISC_JUAL_ID'
            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
        );
        $this->db->where('ITEM_ID', $post['id']);
        $this->db->update('item', $params);
    }

    public function approve($id)
    {
        $param = array(
            'APPROVE_FLAG'  => 'Y',
        );
        $this->db->where('ITEM_ID', $id);
        $this->db->update('item', $param);
    }

    public function getAccount()
    {
        return $this->db->query("SELECT a.COA_ID, a.COA_CODE, a.COA_NAME FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' ORDER BY a.COA_NAME ASC");
    }

    public function getAccPersediaan()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @INVENTORY");
    }

    public function getAccUtangSuspend()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @HUTANG_S");
    }

    public function getAccHpp()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @HPP");
    }

    public function getPenjualanBarang()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @PENJUALAN");
    }

    public function getReturPenjualan()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @RET_JUAL");
    }

    public function getReturPembelian()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @RET_BELI");
    }

    public function getDiscPenjualan()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @JUAL_DISC");
    }

    public function getPenjualanJasa()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @JASA_JUAL");
    }

    public function getPembelian()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @JASA_BELI");
    }

    public function getDiscPenjualanJasa()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @JUAL_DISC");
    }

    public function getPembelianUangMuka()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @UMUKA_BELI");
    }

    public function getPenjualanUangMuka()
    {
        return $this->db->query("SELECT a.*, SUBSTR( CONCAT(a.COA_CODE, ' ', a.COA_NAME), 1, 50 ) AS ACCOUNT_DESC, IFNULL(ac.ACCOUNT_NAME2, a.COA_NAME) AS ACCOUNT_NAME2 FROM coa a LEFT JOIN account ac ON (ac.ACCOUNT_ID = a.ACCOUNT_ID) WHERE a.ACTIVE_FLAG = 'Y' AND a.COA_ID = @UMUKA_JUAL");
    }
}
