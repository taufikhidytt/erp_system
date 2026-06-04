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
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
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
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'GROUP')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
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
            ->where('a.ACTIVE_FLAG', 'Y')
            ->order_by("CASE WHEN a.PRIMARY_FLAG = 'Y' THEN 0 ELSE 1 END");

        if ($id) {
            $this->db->where('a.UOM_CODE', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->where('a.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
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

    public function getRak()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'RAK')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
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
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'MADE_IN')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
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
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'GRADE')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
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
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'TYPEINVENTORY')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
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
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'TIPE')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
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
            ->select("b.ERP_LOOKUP_VALUE_ID as id, b.DISPLAY_NAME text, b.DISPLAY_NAME as name")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'JENIS')
            ->order_by('b.PRIMARY_FLAG desc, b.DISPLAY_NAME asc');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            $this->db->where('b.PRIMARY_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getGudang()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');
        $user_id    = $this->encrypt->decode('user_id');

        $this->db
            ->select("a.WAREHOUSE_ID as id, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME as text ")
            ->from('warehouse a')
            ->join('erp_warehouse g', 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'left')
            ->where('a.JENIS_ID = FN_GET_VAR_VALUE("PST")', null, false)
            ->group_by('a.WAREHOUSE_ID')
            ->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)', 'DESC', false)
            ->order_by('a.WAREHOUSE_NAME', 'ASC');

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->where('a.PRIMARY_FLAG', 'Y')->limit(1);
        } elseif ($user_id) {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->where('a.ERP_USER_ID', $user_id);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.WAREHOUSE_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getSiteStorage()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');
        $user_id    = $this->encrypt->decode('user_id');

        $this->db
            ->select("a.WAREHOUSE_ID as id, a.ADDRESS_ID, a.PRIMARY_FLAG, a.WAREHOUSE_NAME as text ")
            ->from('warehouse a')
            ->join('erp_warehouse g', 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'left')
            ->where('a.JENIS_ID = FN_GET_VAR_VALUE("KNY")', null, false)
            ->group_by('a.WAREHOUSE_ID')
            ->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)', 'DESC', false)
            ->order_by('a.WAREHOUSE_NAME', 'ASC');

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->where('a.PRIMARY_FLAG', 'Y')->limit(1);
        } elseif ($user_id) {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->where('a.ERP_USER_ID', $user_id);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.WAREHOUSE_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getSales()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');

        setVariableMysql();
        $this->db
            ->select("k.KARYAWAN_ID as id, CONCAT(k.FIRST_NAME, ' - [' , k.LAST_NAME, ']') as text, k.KATA_DEPAN, k.DESCRIPTION")
            ->from('karyawan k')
            ->where('k.DEPT_ID = @SALES', null, false)
            ->group_start()
            ->where('k.END_DATE', 0)
            ->or_where('k.END_DATE IS NULL', null, false)
            ->or_where('k.END_DATE >= CURDATE()', null, false)
            ->group_end()
            ->order_by('k.FIRST_NAME', 'ASC');

        if ($id) {
            $this->db->where('k.KARYAWAN_ID', $id)->limit(1);
        } else {
            $this->db->where('K.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('k.FIRST_NAME', $searchTerm)
                    ->or_like('k.LAST_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getShipTo()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = $this->input->get('id');

        setVariableMysql();
        $this->db
            ->select("a.POINT,
                a.PERSON_ID,
                CONCAT(a.PERSON_ID, '_' , ps.PERSON_SITE_ID) as id,
                CONCAT('[' , a.PERSON_CODE, '] - ',a.PERSON_NAME, ' - ', ps.SITE_NAME) as text,
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
                a.APPROVE_FLAG ")
            ->from('person a')
            ->join('payment_term b', 'a.DEFAULT_TERM_ID = b.PAYMENT_TERM_ID', 'inner')
            ->join('person_site ps', 'a.PERSON_ID = ps.PERSON_ID', 'inner')
            ->join('karyawan k', 'a.KARYAWAN_ID = k.KARYAWAN_ID', 'left')
            ->join('mata_uang m', 'm.MATA_UANG_ID = a.MATA_UANG_ID', 'left')
            ->join('erp_lookup_value ti', 'a.TIPE_CUSTOMER_ID = ti.ERP_LOOKUP_VALUE_ID', 'left')
            ->join('person_day pp', 'a.PERSON_ID = pp.PERSON_ID AND pp.MEREK_ID IS NULL AND pp.GROUP_ID IS NULL', 'left')
            ->where('a.FLAG_SUPP', 0)
            ->order_by('a.PERSON_NAME', 'ASC');

        if ($id) {
            [$person_id, $person_site_id] = explode('_', $id);
            $this->db->where('ps.PERSON_SITE_ID', $person_site_id);
            $this->db->where('a.PERSON_ID', $person_id)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.PERSON_NAME', $searchTerm)
                    ->or_like('a.PERSON_CODE', $searchTerm)
                    ->or_like('ps.SITE_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getPayment()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("p.PAYMENT_TERM_ID as id, p.PAYMENT_TERM_NAME as text, p.DESCRIPTION, p.NUMBER_DAYS, p.PRIMARY_FLAG, p.ACTIVE_FLAG ")
            ->from('payment_term p')
            ->order_by('PRIMARY_FLAG DESC, P.NUMBER_DAYS ASC');

        if ($id) {
            $this->db->where('p.PAYMENT_TERM_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('ACTIVE_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('p.PAYMENT_TERM_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getCoa()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("a.COA_ID as id, CONCAT('[',a.COA_CODE,'] - ',a.COA_NAME) text, a.COA_CODE as code, a.COA_NAME as name")
            ->from('coa a')
            ->order_by('a.COA_CODE');

        if ($id) {
            $this->db->where('a.COA_ID', $id)->limit(1);
        } else {
            $this->db->where('a.ACTIVE_FLAG', 'Y');
            $this->db->group_start()
                ->where('a.END_DATE', 0)
                ->or_where('a.END_DATE IS NULL', null, false)
                ->or_where('a.END_DATE >= CURDATE()', null, false)
                ->group_end();
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('a.COA_CODE', $searchTerm)
                    ->or_like('a.COA_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getTipePajak()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $default    = trim($this->input->get('default') ?? '');
        $id         = (int) $this->input->get('id');

        $this->db
            ->select("b.DISPLAY_NAME as text,b.ERP_LOOKUP_VALUE_ID as id")
            ->from('erp_lookup_set a')
            ->join('erp_lookup_value b', 'a.ERP_LOOKUP_SET_ID = b.ERP_LOOKUP_SET_ID')
            ->where('a.PROGRAM_CODE', 'TIPE_PAJAK')
            ->order_by('b.PRIMARY_FLAG DESC, b.DISPLAY_NAME ASC');

        if ($id) {
            $this->db->where('b.ERP_LOOKUP_VALUE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('b.ACTIVE_FLAG', 'Y')->limit(1);
        } else {
            $this->db->where('b.ACTIVE_FLAG', 'Y');
            if ($searchTerm) {
                $this->db->group_start()
                    ->like('b.DISPLAY_NAME', $searchTerm)
                    ->group_end();
            }
            $this->db->limit(50);
        }

        return $this->db->get();
    }

    public function getStorageUser()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = $this->input->get('id') ? (int) $this->input->get('id') : null;
        $default    = trim($this->input->get('default') ?? '');
        $user_id    = (int) $this->session->id;

        $this->db->select("a.WAREHOUSE_ID as id, a.WAREHOUSE_NAME as text")
                ->from('warehouse a');

        // Subquery
        $subquery_join = "(SELECT WAREHOUSE_ID, MAX(PRIMARY_FLAG) AS PRIMARY_FLAG
                        FROM erp_warehouse
                        WHERE ERP_USER_ID = '$user_id'
                        GROUP BY WAREHOUSE_ID) g";
        $this->db->join($subquery_join, 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'left');

        $this->db->where('a.ACTIVE_FLAG', 'Y');
        $this->db->where("a.JENIS_ID = FN_GET_VAR_VALUE('PST')", NULL, FALSE);

        $this->db->where("
            (
                (EXISTS (SELECT 1 FROM erp_warehouse WHERE ERP_USER_ID = '$user_id') AND g.WAREHOUSE_ID IS NOT NULL)
                OR 
                NOT EXISTS (SELECT 1 FROM erp_warehouse WHERE ERP_USER_ID = '$user_id')
            )
        ", NULL, FALSE);

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) =', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()->like('a.WAREHOUSE_NAME', $searchTerm)->group_end();
            }
            $this->db->limit(50);
        }

        $this->db->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)', 'DESC', FALSE);
        $this->db->order_by('a.WAREHOUSE_NAME', 'ASC');

        return $this->db->get();
    }

    public function getSiteStorageUser()
    {
        $searchTerm = trim($this->input->get('q') ?? '');
        $id         = $this->input->get('id') ? (int) $this->input->get('id') : null;
        $default    = trim($this->input->get('default') ?? '');
        $user_id    = (int) $this->session->id;

        $this->db->select("a.WAREHOUSE_ID as id, a.WAREHOUSE_NAME as text")
                ->from('warehouse a');

        // Subquery
        $subquery_join = "(SELECT WAREHOUSE_ID, MAX(PRIMARY_FLAG) AS PRIMARY_FLAG
                        FROM erp_warehouse
                        WHERE ERP_USER_ID = '$user_id'
                        GROUP BY WAREHOUSE_ID) g";
        $this->db->join($subquery_join, 'a.WAREHOUSE_ID = g.WAREHOUSE_ID', 'left');

        $this->db->where('a.ACTIVE_FLAG', 'Y');
        $this->db->where("a.JENIS_ID = FN_GET_VAR_VALUE('KNY')", NULL, FALSE);

        $this->db->where("
            (
                (EXISTS (SELECT 1 FROM erp_warehouse WHERE ERP_USER_ID = '$user_id') AND g.WAREHOUSE_ID IS NOT NULL)
                OR 
                NOT EXISTS (SELECT 1 FROM erp_warehouse WHERE ERP_USER_ID = '$user_id')
            )
        ", NULL, FALSE);

        if ($id) {
            $this->db->where('a.WAREHOUSE_ID', $id)->limit(1);
        } elseif ($default) {
            $this->db->where('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG) =', 'Y')->limit(1);
        } else {
            if ($searchTerm) {
                $this->db->group_start()->like('a.WAREHOUSE_NAME', $searchTerm)->group_end();
            }
            $this->db->limit(50);
        }

        $this->db->order_by('IFNULL(g.PRIMARY_FLAG, a.PRIMARY_FLAG)', 'DESC', FALSE);
        $this->db->order_by('a.WAREHOUSE_NAME', 'ASC');

        return $this->db->get();
    }
}
