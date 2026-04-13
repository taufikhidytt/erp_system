<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Val_mr_po_model extends CI_Model
{
    public function __construct()
    {
        setVariableMysql();
    }
    
    public function get_so_list(){
        $searchTerm = $this->input->get('q')?trim($this->input->get('q')):'';
        if (strlen($searchTerm) < 2) {
            return [];
        }

        $this->db->select('a.SO_ID');
        $this->db->select("IF(PO_NO IS NULL OR PO_NO = '', 
           DOCUMENT_NO, 
           CONCAT(DOCUMENT_NO, ' [', PO_NO, ']')
        ) AS text");
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID',3);
        $this->db->where_not_in('b.DISPLAY_NAME',['DELETE','COMPLETE','CLOSE']);

        $this->db->group_start(); 
            $this->db->like('DOCUMENT_NO', $searchTerm);
            $this->db->or_like('PO_NO', $searchTerm);
        $this->db->group_end();

        $this->db->limit(50);
        $this->db->from('so a');
        $result = $this->db->get()->result();
        return $result;
    }

    public function get_so($so_id){
        $this->db->select('a.SO_ID,a.DOCUMENT_NO,a.PO_NO');
        $this->db->join('erp_lookup_value b', 'a.STATUS_ID = b.ERP_LOOKUP_VALUE_ID');
        $this->db->where('a.DOCUMENT_TYPE_ID',3);
        $this->db->where_not_in('b.DISPLAY_NAME',['DELETE','COMPLETE','CLOSE']);
        $this->db->where('a.SO_ID',$so_id);
        $this->db->from('so a');
        return $this->db->get()->row();
    }

    public function get_mr_list($so_id){
        $query = "
            SELECT * FROM
            (
                -- query untuk ITEM_ID pada table build tidak null
                SELECT 
                    s.DOCUMENT_NO AS SO_No,
                    s.PO_NO, b.BUILD_ID,
                    b.DOCUMENT_NO as MR_No,
                    b.ENTERED_QTY as MR_Qty,
                    b.ITEM_ID AS MR_Item_ID,
                    i.ITEM_DESCRIPTION as MR_Nama_Item,
                    IF(sd.DESKRIPSI IS NULL, sd.ITEM_DESCRIPTION, sd.DESKRIPSI) AS SO_Nama_Item,
                    sd.ENTERED_QTY as PO_Qty,
                    (sd.ENTERED_QTY - b.ENTERED_QTY) AS Qty_Difference,
                    CASE 
                        WHEN sd.ITEM_ID = b.ITEM_ID AND sd.ENTERED_QTY = b.ENTERED_QTY THEN 'MATCH' 
                        WHEN sd.ITEM_ID = b.ITEM_ID AND sd.ENTERED_QTY != b.ENTERED_QTY THEN 
                            CASE 
                                WHEN sd.ENTERED_QTY > b.ENTERED_QTY THEN 'OVER_QTY'
                                ELSE 'UNDER_QTY' 
                            END 
                        WHEN sd.ITEM_ID != b.ITEM_ID THEN 'ITEM_MISMATCH' 
                        ELSE 'UNKNOWN' 
                    END AS Match_Status,
                    b.APPROVED_FLAG, 
                    sd.RECEIVED_ENTERED_QTY
                FROM build b 
                JOIN so_detail sd ON sd.BUILD_ID = b.BUILD_ID 
                JOIN so s ON s.SO_ID = sd.SO_ID 
                JOIN item i ON i.ITEM_ID = b.ITEM_ID
                WHERE sd.SO_ID = $so_id AND b.ITEM_ID IS NOT NULL
                
                UNION ALL
                
                -- query untuk ITEM_ID pada table build null
                SELECT 
                    s.DOCUMENT_NO AS SO_No,
                    s.PO_NO, b.BUILD_ID,
                    b.DOCUMENT_NO as MR_No,
                    bd.ENTERED_QTY as MR_Qty,
                    b.ITEM_ID AS MR_Item_ID,
                    i.ITEM_DESCRIPTION as MR_Nama_Item,
                    IF(sd.DESKRIPSI IS NULL, sd.ITEM_DESCRIPTION, sd.DESKRIPSI) AS SO_Nama_Item,
                    sd.ENTERED_QTY as PO_Qty,
                    (sd.ENTERED_QTY - bd.ENTERED_QTY) AS Qty_Difference,
                    CASE 
                        WHEN sd.ITEM_ID = bd.ITEM_ID AND sd.ENTERED_QTY = bd.ENTERED_QTY THEN 'MATCH' 
                        WHEN sd.ITEM_ID = bd.ITEM_ID AND sd.ENTERED_QTY != bd.ENTERED_QTY THEN 
                            CASE 
                                WHEN sd.ENTERED_QTY > bd.ENTERED_QTY THEN 'OVER_QTY'
                                ELSE 'UNDER_QTY' 
                            END 
                        WHEN sd.ITEM_ID != bd.ITEM_ID THEN 'ITEM_MISMATCH' 
                        ELSE 'UNKNOWN' 
                    END AS Match_Status,
                    b.APPROVED_FLAG, 
                    sd.RECEIVED_ENTERED_QTY
                FROM build_detail bd
                JOIN build b ON b.BUILD_ID = bd.BUILD_ID 
                JOIN so_detail sd ON sd.BUILD_DETAIL_ID = bd.BUILD_DETAIL_ID
                JOIN so s ON s.SO_ID = sd.SO_ID
                JOIN item i ON i.ITEM_ID = bd.ITEM_ID
                WHERE sd.SO_ID = $so_id AND b.ITEM_ID IS NULL
            ) AS combine ORDER BY combine.BUILD_ID
        ";
        $result = $this->db->query($query)->result_array();
        return $result;
    }

    public function get_mr_list_backup($so_id){
        $this->db->select("
            s.DOCUMENT_NO AS SO_No, s.PO_NO,
            b.BUILD_ID, b.DOCUMENT_NO as MR_No, b.ENTERED_QTY as MR_Qty,
            i.ITEM_CODE as Kode_Item, i.ITEM_DESCRIPTION as MR_Nama_Item,
            IF(sd.DESKRIPSI IS NULL,sd.ITEM_DESCRIPTION,sd.DESKRIPSI) AS SO_Nama_Item,
            sd.ENTERED_QTY as PO_Qty,
            (sd.ENTERED_QTY - b.ENTERED_QTY) AS Qty_Difference,
            CASE 
                WHEN sd.ITEM_ID = b.ITEM_ID AND sd.ENTERED_QTY = b.ENTERED_QTY THEN 'MATCH'
                WHEN sd.ITEM_ID = b.ITEM_ID AND sd.ENTERED_QTY != b.ENTERED_QTY THEN 
                    CASE 
                        WHEN sd.ENTERED_QTY > b.ENTERED_QTY THEN 'OVER_QTY'
                        ELSE 'UNDER_QTY'
                    END
                WHEN sd.ITEM_ID != b.ITEM_ID THEN 'ITEM_MISMATCH'
                ELSE 'UNKNOWN'
            END AS Match_Status,
            b.APPROVED_FLAG,
            sd.RECEIVED_ENTERED_QTY
            ");
        $this->db->from('build b');
        $this->db->join('so_detail sd','sd.BUILD_ID = b.BUILD_ID');
        $this->db->join('so s','s.SO_ID = sd.SO_ID');
        $this->db->join('item i','i.ITEM_ID = b.ITEM_ID');
        $this->db->where('sd.SO_ID',$so_id);
        return $this->db->get()->result();
    }

    public function get_mr($build_id){
        $this->db->select('b.BUILD_ID,b.APPROVED_FLAG,sd.RECEIVED_ENTERED_QTY');
        $this->db->join('so_detail sd','sd.BUILD_ID = b.BUILD_ID');
        $this->db->where('b.BUILD_ID',$build_id);
        return $this->db->get('build b')->row();
    }

    public function update_mr($build_id,$params){
        $this->db->where('BUILD_ID',$build_id);
        $this->db->update('build', $params);

        if ($this->db->error()['code'] != 0) {
            return $this->db->error();
        }
        return true;
    }

    public function get_mr_detail($arr_build_id){
        $this->db->select('
            bd.BUILD_ID,
            bd.ENTERED_QTY as MRD_Qty,
            bd.ENTERED_UOM as MRD_Satuan,
            i.ITEM_CODE as MRD_Kode_Item, i.ITEM_DESCRIPTION as MRD_Nama_Item,
        ');
        $this->db->from('build_detail bd');
        $this->db->join('item i','bd.ITEM_ID = i.ITEM_ID');
        $this->db->where_in('bd.BUILD_ID',$arr_build_id);
        return $this->db->get()->result_array();
    }
}