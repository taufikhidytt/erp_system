<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fpk_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }

    var $column_order = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "a.NEED_DATE",
        "CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' )",
        "w.WAREHOUSE_NAME",
        "a.TOTAL_AMOUNT",
    );

    var $column_search = array(
        null,
        "b.DISPLAY_NAME",
        "a.DOCUMENT_NO",
        "a.DOCUMENT_REFF_NO",
        "a.DOCUMENT_DATE",
        "a.NEED_DATE",
        "CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' )",
        "w.WAREHOUSE_NAME",
        "a.TOTAL_AMOUNT",
    );

    var $order = array('a.PR_ID' => 'DESC');

    private function _get_datatables_query()
    {
        $tipe_id = $this->db->query("SELECT DISTINCT a.ERP_TABLE_ID, b.PROMPT, b.TYPE_ID FROM erp_table a JOIN erp_menu b ON ( a.TABLE_NAME = b.TABLE_NAME ) WHERE b.PROMPT = '{$this->uri->segment(1)}'")->row_array();

        $this->db->distinct();
        $this->db->select("
            a.PR_ID,
            b.DISPLAY_NAME Status,
            a.DOCUMENT_NO No_Transaksi,
            a.DOCUMENT_REFF_NO No_Referensi,
            a.DOCUMENT_DATE Tanggal,
            a.NEED_DATE Dibutuhkan,
            a.TOTAL_AMOUNT Total,
            CONCAT( p.PERSON_NAME, ' - [', p.PERSON_CODE, ']' ) Supplier,
            w.WAREHOUSE_NAME Gudang,
            k.FIRST_NAME Sales
        ");
        $this->db->from('pr a');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->join('person p', 'a.PERSON_ID = p.PERSON_ID');
        $this->db->join('warehouse w', 'a.WAREHOUSE_ID = w.WAREHOUSE_ID');
        $this->db->join('karyawan k', 'a.KARYAWAN_ID = k.KARYAWAN_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID', $tipe_id['TYPE_ID']);
        $this->db->order_by('a.DOCUMENT_DATE', 'desc');

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
            i.OBSOLETE_FLAG OBSOLETE');
        $this->db->from('item i');
        $this->db->join('erp_lookup_value a', 'i.JENIS_ID = a.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value b', 'i.GROUP_ID = b.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value c', 'i.MADE_IN_ID = c.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value d', 'i.TIPE_ID = d.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value e', 'i.MEREK_ID = e.ERP_LOOKUP_VALUE_ID', 'left');
        $this->db->join('erp_lookup_value f', 'i.TYPE_ID = f.ERP_LOOKUP_VALUE_ID', 'left');
        return $this->db->count_all_results();
    }

    public function getSupplier()
    {
        return $this->db->query("SELECT a.PERSON_ID, a.PERSON_NAME Supplier, a.PERSON_CODE Kode FROM person a JOIN person_site b ON (a.PERSON_ID = b.PERSON_ID) WHERE a.FLAG_SUPP = 1 AND a.ACTIVE_FLAG = 'Y' GROUP BY a.PERSON_ID ORDER BY a.PERSON_NAME");
    }

    public function getGudang()
    {
        return $this->db->query("SELECT a.WAREHOUSE_ID, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME FROM warehouse a LEFT JOIN erp_warehouse g ON a.WAREHOUSE_ID = g.WAREHOUSE_ID AND ERP_USER_ID = '1' WHERE ACTIVE_FLAG = 'Y' GROUP BY a.WAREHOUSE_ID ORDER BY IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) DESC, a.WAREHOUSE_NAME");
    }

    public function getSales()
    {
        return $this->db->query("SELECT k.KARYAWAN_ID, k.FIRST_NAME, k.LAST_NAME, k.KATA_DEPAN, k.DESCRIPTION FROM karyawan k WHERE k.DEPT_ID = @SALES AND ( (k.END_DATE = 0) OR (k.END_DATE IS NULL) OR (k.END_DATE >= CURDATE()) ) AND k.ACTIVE_FLAG = 'Y' ORDER BY k.FIRST_NAME");
    }

    public function add($post)
    {
        date_default_timezone_set('Asia/Jakarta');
        $params = array(
            // 'ITEM_CODE'         => $post['item_code'] ? htmlspecialchars($post['item_code']) : null,
            'ITEM_CODE'         => null,
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
            'CUSTOM4'           => 'KG',
            'M3'                => $post['kubikasi'] ? htmlspecialchars($post['kubikasi']) : null,
            'MADE_IN_ID'        => $post['made_in'] ? htmlspecialchars($post['made_in']) : null,
            'TIPE_ID'           => $post['komoditi'] ? htmlspecialchars($post['komoditi']) : null,
            'JENIS_ID'          => $post['jenis'] ? htmlspecialchars($post['jenis']) : null,
            'GRADE_ID'          => $post['grade'] ? htmlspecialchars($post['grade']) : null,
            'HPP_AWAL'          => $post['hpp'] ? htmlspecialchars($post['hpp']) : null,
            'NOTE'              => $post['keterangan'] ? htmlspecialchars($post['keterangan']) : null,
            'MOQ'               => $post['min_order_quantity'] ? htmlspecialchars($post['min_order_quantity']) : null,
            'CUSTOM5'           => $post['satuan2'] ? htmlspecialchars($post['satuan2']) : null,
            'ACTIVE_FLAG'       => null,
            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
        );

        if (!empty($post['obsolete'])) {
            $params['OBSOLETE_FLAG'] = 'Y';
        } else {
            $params['OBSOLETE_FLAG'] = 'N';
        }

        if (!empty($post['new_product_name'])) {
            $params['PRODUK_BARU'] = htmlspecialchars($post['new_product_name']);
        } else {
            $params['PRODUK_BARU'] = null;
        }

        if (!empty($post['konsinyasi'])) {
            $params['ITEM_KMS'] = 'Y';
        } else {
            $params['ITEM_KMS'] = 'N';
        }

        if (!empty($post['supplier'])) {
            $params['PERSON_ID'] = htmlspecialchars($post['supplier']);
        } else {
            $params['PERSON_ID'] = null;
        }

        // if (!empty($post['status_flag'])) {
        //     $params['ACTIVE_FLAG'] = htmlspecialchars($post['status_flag']);
        // } else {
        //     $params['ACTIVE_FLAG'] = null;
        // }

        $params['COA_ID'] = null;
        $params['COA_JUAL_ID'] = null;
        $params['COA_DISC_JUAL_ID'] = null;

        if (!empty($post['acc_persediaan'])) {
            $params['COA_ID'] = htmlspecialchars($post['acc_persediaan']);
        } elseif (!empty($post['acc_pembelian'])) {
            $params['COA_ID'] = htmlspecialchars($post['acc_pembelian']);
        } elseif (!empty($post['acc_pembelian_uang_muka'])) {
            $params['COA_ID'] = htmlspecialchars($post['acc_pembelian_uang_muka']);
        }


        if (!empty($post['acc_penjualan_barang'])) {
            $params['COA_JUAL_ID'] = htmlspecialchars($post['acc_penjualan_barang']);
        } elseif (!empty($post['acc_penjualan_jasa'])) {
            $params['COA_JUAL_ID'] = htmlspecialchars($post['acc_penjualan_jasa']);
        } elseif (!empty($post['acc_penjualan_uang_muka'])) {
            $params['COA_JUAL_ID'] = htmlspecialchars($post['acc_penjualan_uang_muka']);
        }


        if (!empty($post['acc_disc_penjualan'])) {
            $params['COA_DISC_JUAL_ID'] = htmlspecialchars($post['acc_disc_penjualan']);
        } elseif (!empty($post['acc_disc_penjualan_jasa'])) {
            $params['COA_DISC_JUAL_ID'] = htmlspecialchars($post['acc_disc_penjualan_jasa']);
        }


        // BARANG
        if (!empty($post['acc_utang_suspend'])) {
            $params['COA_SUSPEND_ID'] = htmlspecialchars($post['acc_utang_suspend']);
        } else {
            $params['COA_SUSPEND_ID'] = null;
        }

        if (!empty($post['acc_hpp'])) {
            $params['COA_HPP_ID'] = htmlspecialchars($post['acc_hpp']);
        } else {
            $params['COA_HPP_ID'] = null;
        }

        if (!empty($post['acc_retur_penjualan'])) {
            $params['COA_RET_JUAL_ID'] = htmlspecialchars($post['acc_retur_penjualan']);
        } else {
            $params['COA_RET_JUAL_ID'] = null;
        }

        if (!empty($post['acc_retur_pembelian'])) {
            $params['COA_RET_BELI_ID'] = htmlspecialchars($post['acc_retur_pembelian']);
        } else {
            $params['COA_RET_BELI_ID'] = null;
        }

        $this->db->insert('item', $params);
        return $this->db->insert_id();
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
            'CUSTOM4'           => 'Kg',
            'M3'                => $post['kubikasi'] ? htmlspecialchars($post['kubikasi']) : null,
            'MADE_IN_ID'        => $post['made_in'] ? htmlspecialchars($post['made_in']) : null,
            'TIPE_ID'           => $post['komoditi'] ? htmlspecialchars($post['komoditi']) : null,
            'JENIS_ID'          => $post['jenis'] ? htmlspecialchars($post['jenis']) : null,
            'GRADE_ID'          => $post['grade'] ? htmlspecialchars($post['grade']) : null,
            'PERSON_ID'         => $post['supplier'] ? htmlspecialchars($post['supplier']) : null,
            'HPP_AWAL'          => $post['hpp'] ? htmlspecialchars($post['hpp']) : null,
            'NOTE'              => $post['keterangan'] ? htmlspecialchars($post['keterangan']) : null,
            'MOQ'               => $post['min_order_quantity'] ? htmlspecialchars($post['min_order_quantity']) : null,
            'CUSTOM5'           => $post['satuan2'] ? htmlspecialchars($post['satuan2']) : null,
            'ACTIVE_FLAG'       => NULL,
            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
        );

        if (!empty($post['obsolete'])) {
            $params['OBSOLETE_FLAG'] = 'Y';
        } else {
            $params['OBSOLETE_FLAG'] = 'N';
        }

        if (!empty($post['new_product_name'])) {
            $params['PRODUK_BARU'] = htmlspecialchars($post['new_product_name']);
        } else {
            $params['PRODUK_BARU'] = null;
        }

        if (!empty($post['konsinyasi'])) {
            $params['ITEM_KMS'] = 'Y';
        } else {
            $params['ITEM_KMS'] = 'N';
        }

        $params['COA_ID'] = null;
        $params['COA_JUAL_ID'] = null;
        $params['COA_DISC_JUAL_ID'] = null;

        if (!empty($post['acc_persediaan'])) {
            $params['COA_ID'] = htmlspecialchars($post['acc_persediaan']);
        } elseif (!empty($post['acc_pembelian'])) {
            $params['COA_ID'] = htmlspecialchars($post['acc_pembelian']);
        } elseif (!empty($post['acc_pembelian_uang_muka'])) {
            $params['COA_ID'] = htmlspecialchars($post['acc_pembelian_uang_muka']);
        }


        if (!empty($post['acc_penjualan_barang'])) {
            $params['COA_JUAL_ID'] = htmlspecialchars($post['acc_penjualan_barang']);
        } elseif (!empty($post['acc_penjualan_jasa'])) {
            $params['COA_JUAL_ID'] = htmlspecialchars($post['acc_penjualan_jasa']);
        } elseif (!empty($post['acc_penjualan_uang_muka'])) {
            $params['COA_JUAL_ID'] = htmlspecialchars($post['acc_penjualan_uang_muka']);
        }


        if (!empty($post['acc_disc_penjualan'])) {
            $params['COA_DISC_JUAL_ID'] = htmlspecialchars($post['acc_disc_penjualan']);
        } elseif (!empty($post['acc_disc_penjualan_jasa'])) {
            $params['COA_DISC_JUAL_ID'] = htmlspecialchars($post['acc_disc_penjualan_jasa']);
        }


        // BARANG
        if (!empty($post['acc_utang_suspend'])) {
            $params['COA_SUSPEND_ID'] = htmlspecialchars($post['acc_utang_suspend']);
        } else {
            $params['COA_SUSPEND_ID'] = null;
        }

        if (!empty($post['acc_hpp'])) {
            $params['COA_HPP_ID'] = htmlspecialchars($post['acc_hpp']);
        } else {
            $params['COA_HPP_ID'] = null;
        }

        if (!empty($post['acc_retur_penjualan'])) {
            $params['COA_RET_JUAL_ID'] = htmlspecialchars($post['acc_retur_penjualan']);
        } else {
            $params['COA_RET_JUAL_ID'] = null;
        }

        if (!empty($post['acc_retur_pembelian'])) {
            $params['COA_RET_BELI_ID'] = htmlspecialchars($post['acc_retur_pembelian']);
        } else {
            $params['COA_RET_BELI_ID'] = null;
        }

        $this->db->where('ITEM_ID', $post['id']);
        $this->db->update('item', $params);

        $error = $this->db->error();

        if ($error['code'] != 0) {
            return [
                'status'  => 'error',
                'message' => $error['message']
            ];
        }

        return [
            'status' => 'success',
            'affected' => $this->db->affected_rows()
        ];
    }

    public function approve($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $param = array(
            'APPROVE_FLAG'      => 'Y',
            'LAST_UPDATE_BY'    => $this->session->userdata('id'),
            'LAST_UPDATE_DATE'  => date('Y-m-d H:i:s'),
        );
        $this->db->where('ITEM_ID', $id);
        $this->db->update('item', $param);

        $error = $this->db->error();

        if ($error['code'] != 0) {
            return [
                'status'  => 'error',
                'message' => $error['message']
            ];
        }

        return [
            'status' => 'success',
            'affected' => $this->db->affected_rows()
        ];
    }

    public function getUomChild($id)
    {
        $this->db->select('*');
        $this->db->from('item_uom');
        $this->db->where('ITEM_ID', $id);
        $this->db->order_by('ITEM_UOM_ID', 'ASC');
        return $this->db->get();
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch('item_uom', $data);
    }

    public function updateSatuanUomDetail($data)
    {
        foreach ($data as $row) {

            $id = $row['ITEM_UOM_ID'];

            unset($row['ITEM_UOM_ID']);

            $this->db->where('ITEM_UOM_ID', $id);
            $this->db->update('item_uom', $row);
        }
        return true;
    }

    public function delete_by_ids($ids)
    {
        if (!empty($ids)) {
            $this->db->where_in('ITEM_UOM_ID', $ids);
            return $this->db->delete('item_uom');
        }
        return false;
    }

    public function deleteItem($id)
    {
        $this->db->where('ITEM_ID', $id);
        $this->db->delete('item');

        $error = $this->db->error();

        if ($error['code'] != 0) {
            return [
                'status'  => 'error',
                'message' => $error['message']
            ];
        }

        return [
            'status' => 'success',
            'affected' => $this->db->affected_rows()
        ];
    }
}
