<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * M_union_datatables - Model untuk handle UNION query dengan DataTables
 * 
 * Contoh penggunaan di Controller:
 * $this->load->model('M_union_datatables');
 * $result = $this->M_union_datatables->generate($config, $callback);
 */
class M_union_datatables extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate DataTables response dengan UNION query
     * 
     * @param array $config - Konfigurasi query
     * @param callable $callback - Function untuk format data
     * @return array - DataTables response
     * 
     * ────────────────────────────────────────────────────────
     * Struktur $config:
     * ────────────────────────────────────────────────────────
     * [
     *     'queries' => [
     *         // Query pertama (build)
     *         [
     *             'select' => 'b.BUILD_ID, b.BUILD_ID AS BUILD_DETAIL_ID, ...',
     *             'table'  => 'build b',
     *             'join'   => ['item i', 'b.ITEM_ID = i.ITEM_ID', 'inner'],
     *             'where'  => 'b.BUILD_ID = 3',
     *         ],
     *         // Query kedua (build_detail)
     *         [
     *             'select' => 'b.BUILD_ID, b.BUILD_DETAIL_ID, ...',
     *             'table'  => 'build_detail b',
     *             'join'   => ['item i', 'b.ITEM_ID = i.ITEM_ID', 'inner'],
     *             'where'  => 'b.BUILD_ID = 3',
     *         ],
     *     ],
     *     'search_columns' => ['Nama_Item', 'Kode_Item'],
     *     'order_map' => [
     *         0 => null,
     *         1 => null,
     *         2 => 'Nama_Item',
     *         3 => 'Kode_Item',
     *         // dst...
     *     ],
     * ]
     */
    public function generate($config, callable $callback)
    {
        $draw = intval($this->input->post('draw') ?? 0);
        $start = intval($this->input->post('start') ?? 0);
        $length = intval($this->input->post('length') ?? 10);
        $search = trim($this->input->post('search')['value'] ?? '');
        $order = $this->input->post('order') ?? [];

        // ====== BUILD UNION QUERY ======
        $union_query = $this->_build_union_query($config, $search);

        // ====== COUNT TOTAL (tanpa search) ======
        $count_total_query = "SELECT COUNT(*) as cnt FROM (" . $this->_build_union_query($config, '') . ") AS subq";
        $total = $this->db->query($count_total_query)->row()->cnt;

        // ====== COUNT FILTERED (dengan search) ======
        $count_filtered_query = "SELECT COUNT(*) as cnt FROM (" . $union_query . ") AS subq";
        $filtered = $this->db->query($count_filtered_query)->row()->cnt;

        // ====== HANDLE SORTING ======
        $order_by = '';
        if (!empty($order) && !empty($config['order_map'])) {
            $order_parts = [];
            foreach ($order as $o) {
                $col_idx = (int)$o['column'];
                if (isset($config['order_map'][$col_idx]) && $config['order_map'][$col_idx] !== null) {
                    $col_name = $config['order_map'][$col_idx];
                    $direction = strtoupper($o['dir']) === 'DESC' ? 'DESC' : 'ASC';
                    $order_parts[] = "$col_name $direction";
                }
            }
            if (!empty($order_parts)) {
                $order_by = ' ORDER BY ' . implode(', ', $order_parts);
            }
        }

        // ====== FINAL QUERY (dengan pagination) ======
        $limit = '';
        if ($length != -1) {
            $limit = " LIMIT " . (int)$start . ", " . (int)$length;
        }

        $final_query = "SELECT * FROM (" . $union_query . ") AS result" . $order_by . $limit;
        $result = $this->db->query($final_query)->result();

        // ====== FORMAT DATA via CALLBACK ======
        $no = $start + 1;
        $data = [];
        foreach ($result as $row) {
            $data[] = $callback($row, $no);
            $no++;
        }

        // ====== RETURN RESPONSE ======
        return [
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $filtered,
            'data'            => $data,
        ];
    }

    /**
     * Build UNION query dari konfigurasi
     * 
     * @param array $config
     * @param string $search
     * @return string
     */
    private function _build_union_query($config, $search = '')
    {
        $queries = $config['queries'] ?? [];
        $search_columns = $config['search_columns'] ?? [];
        
        if (empty($queries)) {
            return '';
        }

        // Build search condition (LIKE)
        $search_condition = '';
        if (!empty($search) && !empty($search_columns)) {
            $search = $this->db->escape_like_str($search);
            $search_parts = [];
            foreach ($search_columns as $col) {
                $search_parts[] = "$col LIKE '%" . $search . "%'";
            }
            $search_condition = ' AND (' . implode(' OR ', $search_parts) . ')';
        }

        // Build each query
        $union_queries = [];
        foreach ($queries as $q) {
            $select = $q['select'] ?? '';
            $table = $q['table'] ?? '';
            $join = $q['join'] ?? null;
            $where = $q['where'] ?? '';

            if (empty($select) || empty($table)) {
                continue;
            }

            $query_part = "SELECT " . $select . " FROM " . $table;

            // Add JOIN
            if (!empty($join)) {
                if (is_array($join)) {
                    // Check apakah single join atau multiple joins
                    // Single join: ['table t', 'condition', 'type']
                    // Multiple: [['table t', 'condition', 'type'], [...]]
                    
                    if (is_string($join[0])) {
                        // Single join
                        $join_type = strtoupper($join[2] ?? 'INNER');
                        $query_part .= " " . $join_type . " JOIN " . $join[0] . " ON " . $join[1];
                    } elseif (is_array($join[0])) {
                        // Multiple joins
                        foreach ($join as $j) {
                            $join_type = strtoupper($j[2] ?? 'INNER');
                            $query_part .= " " . $join_type . " JOIN " . $j[0] . " ON " . $j[1];
                        }
                    }
                }
            }

            // Add WHERE
            $where_condition = '';
            
            // Handle WHERE (string atau array)
            if (!empty($where)) {
                if (is_string($where)) {
                    // String WHERE
                    $where_condition = $where;
                } elseif (is_array($where)) {
                    // Array WHERE - convert ke string
                    $where_parts = [];
                    foreach ($where as $col => $val) {
                        if (is_int($col)) {
                            $where_parts[] = $val;
                        } elseif (is_int($val) || is_float($val)) {
                            $where_parts[] = "$col = " . $val;
                        } else {
                            $val = $this->db->escape($val);
                            $where_parts[] = "$col = " . $val;
                        }
                    }
                    $where_condition = implode(' AND ', $where_parts);
                }
            }
            
            // Combine WHERE dan SEARCH
            if (!empty($where_condition) && !empty($search_condition)) {
                $query_part .= " WHERE " . $where_condition . $search_condition;
            } elseif (!empty($where_condition)) {
                $query_part .= " WHERE " . $where_condition;
            } elseif (!empty($search_condition)) {
                $query_part .= " WHERE " . ltrim($search_condition, ' AND ');
            }

            $union_queries[] = $query_part;
        }

        // Combine dengan UNION ALL
        $final_query = implode(' UNION ALL ', $union_queries);
        return $final_query;
    }
}
?>