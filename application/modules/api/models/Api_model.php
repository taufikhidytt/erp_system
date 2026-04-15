<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends CI_Model
{
    public function getBrand()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME NAME,
                CONCAT('[', b.DESCRIPTION, '] - ',b.DISPLAY_NAME) AS text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'MEREK')
            ->where('b.ACTIVE_FLAG', 'Y')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->or_like('b.DESCRIPTION', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getCategory()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME NAME, CONCAT('[', b.DESCRIPTION, '] - ',b.DISPLAY_NAME) AS text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b','a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'GROUP')
            ->where('b.ACTIVE_FLAG', 'Y')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->or_like('b.DESCRIPTION', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getUom()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (string) $this->input->get('id');

        $this->db
            ->select('a.UOM_CODE as id, a.UOM_CODE as text')
            ->from('uom a')
            ->where('a.ACTIVE_FLAG','Y')
            ->order_by("CASE WHEN a.PRIMARY_FLAG = 'Y' THEN 0 ELSE 1 END");
        
        if ($id) {
            $this->db->where('a.UOM_CODE', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('a.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.UOM_CODE', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getSupplier()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("a.PERSON_ID as id, a.PERSON_NAME Supplier, a.PERSON_CODE Kode, CONCAT('[',a.PERSON_CODE,'] - ',a.PERSON_NAME) as text")
            ->from('person a')
            ->join('person_site b','a.PERSON_ID = b.PERSON_ID')
            ->where('a.FLAG_SUPP',1)
            ->where('a.ACTIVE_FLAG','Y')
            ->group_by('a.PERSON_ID')
            ->order_by('a.PERSON_NAME');

        if ($id) {
            $this->db->where('a.PERSON_ID', $id)->limit(1);
        } else {
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
    
    public function getRak()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b','a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE','RAK')
            ->where('b.ACTIVE_FLAG','Y')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
    
    public function getMadeIn()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b','a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE','MADE_IN')
            ->where('b.ACTIVE_FLAG','Y')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
    
    public function getGrade()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b','a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE','GRADE')
            ->where('b.ACTIVE_FLAG','Y')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
    
    public function getType()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b','a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE','TYPEINVENTORY')
            ->where('b.ACTIVE_FLAG','Y')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
    
    public function getKomoditi()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME Komoditi, CONCAT('[', b.DESCRIPTION, '] - ',b.DISPLAY_NAME) AS text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b','a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE','TIPE')
            ->where('b.ACTIVE_FLAG','Y')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
    
    public function getJenis()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');
        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b','a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE','JENIS')
            ->where('b.ACTIVE_FLAG','Y')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }
}