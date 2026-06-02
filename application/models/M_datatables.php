<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DataTables Server-Side
 *
 * Mendukung:
 *  - SELECT biasa, ekspresi (raw), subquery
 *  - JOIN (left/right/inner/outer/full outer)
 *  - WHERE, WHERE IN, WHERE raw (custom string)
 *  - HAVING, GROUP BY
 *  - Search & Order dari DataTables
 *  - Callback untuk menyusun kolom di Controller
 */
class M_datatables extends CI_Model {

    // =========================================================
    // PRIVATE: BUILD QUERY
    // =========================================================

    private function _build_query(array $p): void
    {
        // ── FROM ─────────────────────────────────────────────
        $this->db->from($p['table']);

        // ── SELECT ───────────────────────────────────────────
        // Format: string biasa, atau array of [expr, escape]
        // Contoh string  : 'a.id, a.name, b.total'
        // Contoh ekspresi: ['(b.qty / b.base) AS ratio', FALSE]
        // Contoh array   : [
        //     'a.id, a.name',
        //     ['(b.qty / b.base) AS ratio', FALSE],
        //     ['(SELECT MAX(x) FROM tbl) AS mx', FALSE],
        // ]
        if (!empty($p['select'])) {
            $selects = is_array($p['select']) ? $p['select'] : [$p['select']];
            foreach ($selects as $s) {
                is_array($s)
                    ? $this->db->select($s[0], $s[1] ?? TRUE)   // [expr, escape]
                    : $this->db->select($s);
            }
        }

        // ── JOIN ─────────────────────────────────────────────
        // Format: [table, condition, type = 'left']
        if (!empty($p['joins'])) {
            foreach ($p['joins'] as $j) {
                $this->db->join($j[0], $j[1], $j[2] ?? 'left');
            }
        }

        // ── WHERE (array key=>val) ───────────────────────────
        // Kondisi 2: key-value biasa → operator "="
        //   'where' => ['b.STATUS' => 1, 'b.TYPE' => 'A']
        //   → b.STATUS = 1 AND b.TYPE = 'A'
        //
        // Kondisi 3: key sudah mengandung operator
        //   'where' => ['b.AMOUNT >=' => 1000, 'b.STOCK <' => 10, 'b.QTY <>' => 0]
        //   → b.AMOUNT >= 1000 AND b.STOCK < 10 AND b.QTY <> 0
        //
        // Kondisi 4: value NULL → IS NULL
        //   'where' => ['b.DELETED_AT' => null]
        //   → b.DELETED_AT IS NULL
        if (!empty($p['where'])) {
            $this->db->where($p['where']);
        }

        // ── WHERE IN ─────────────────────────────────────────
        // Kondisi 5: value array → IN clause
        //   'where_in' => ['b.STATUS' => [1, 2, 3], 'b.TYPE' => ['A', 'B']]
        //   → b.STATUS IN (1, 2, 3) AND b.TYPE IN ('A', 'B')
        if (!empty($p['where_in'])) {
            foreach ($p['where_in'] as $col => $vals) {
                $this->db->where_in($col, (array) $vals);
            }
        }

        // ── WHERE custom string (raw) ─────────────────────────
        // Kondisi 1: raw string (ekspresi bebas, tidak di-escape)
        //   'where_raw' => 'COALESCE(b.ITEM_ID, 0) <> 0'
        //   'where_raw' => ['b.STATUS IS NOT NULL', 'b.DELETED_AT IS NULL']
        //
        // Kondisi 6: BETWEEN
        //   'where_raw' => 'b.CREATED_AT BETWEEN "2024-01-01" AND "2024-12-31"'
        //
        // Kondisi 7: LIKE (custom)
        //   'where_raw' => 'b.NAME LIKE "%john%"'
        //   * untuk search DataTables gunakan column_search
        //
        // Kondisi 8: subquery
        //   'where_raw' => 'b.ID IN (SELECT ID FROM other_table WHERE x = 1)'
        //
        // ⚠️  Nilai tidak di-escape otomatis — pastikan aman dari SQL injection
        if (!empty($p['where_raw'])) {
            $raws = is_array($p['where_raw']) ? $p['where_raw'] : [$p['where_raw']];
            foreach ($raws as $raw) {
                $this->db->where($raw, NULL, FALSE);
            }
        }

        // ── GROUP BY ─────────────────────────────────────────
        // Format: string | array of string
        if (!empty($p['group_by'])) {
            $groups = is_array($p['group_by']) ? $p['group_by'] : [$p['group_by']];
            foreach ($groups as $g) {
                $this->db->group_by($g);
            }
        }

        // ── HAVING ───────────────────────────────────────────
        // Format: string (raw) | array key=>val
        // Contoh raw  : 'SUM(qty) > 0'
        // Contoh array: ['total >' => 100]
        if (!empty($p['having'])) {
            is_array($p['having'])
                ? $this->db->having($p['having'])
                : $this->db->having($p['having'], NULL, FALSE);
        }

        // ── SEARCH (dari DataTables input) ───────────────────
        $search_val = $this->input->post('search')['value'] ?? '';
        $columns    = $this->input->post('columns') ?? [];

        if (!empty($p['column_search'])) {
            
            if ($search_val !== '') {
                $this->db->group_start();
                $first = true;
                
                foreach ($p['column_search'] as $item) {
                    if (empty(trim($item))) continue;
                    
                    $complex = preg_match('/[\s(]/', $item);
                    $esc     = $this->db->escape_like_str($search_val);
                    
                    if ($first) {
                        $complex ? $this->db->where("($item) LIKE '%{$esc}%' ESCAPE '!'", NULL, FALSE) : $this->db->like($item, $search_val);
                        $first = false;
                    } else {
                        $complex ? $this->db->or_where("($item) LIKE '%{$esc}%' ESCAPE '!'", NULL, FALSE) : $this->db->or_like($item, $search_val);
                    }
                }
                $this->db->group_end();
            }

            foreach ($p['column_search'] as $i => $item) {
                if (empty(trim($item))) continue;
                
                $col_val = $columns[$i]['search']['value'] ?? '';
                
                if ($col_val !== '') {
                    $esc = $this->db->escape_like_str($col_val);
                    preg_match('/[\s(]/', $item) ? $this->db->where("($item) LIKE '%{$esc}%' ESCAPE '!'", NULL, FALSE) : $this->db->like($item, $col_val);
                }
            }
        }

        // ── ORDER (dari DataTables input / default) ──────────
        // column_order[i] = FALSE  → kolom tidak bisa di-sort (kolom aksi, dll)
        $order_post = $this->input->post('order');
        if (!empty($order_post) && !empty($p['column_order'])) {
            foreach ($order_post as $o) {
                $idx = (int) $o['column'];
                if (isset($p['column_order'][$idx]) && $p['column_order'][$idx] !== FALSE) {
                    $this->db->order_by($p['column_order'][$idx], $o['dir'], FALSE);
                }
            }
        } elseif (!empty($p['order'])) {
            foreach ($p['order'] as $col => $dir) {
                $this->db->order_by($col, $dir);
            }
        }
    }

    // =========================================================
    // PRIVATE: COUNT FILTERED
    // =========================================================

    private function _count_filtered(array $p): int
    {
        $this->_build_query($p);
        return $this->db->count_all_results();
    }

    private function _count_total(array $p): int
    {
        $this->db->from($p['table']);

        if (!empty($p['joins']))
            foreach ($p['joins'] as $j)
                $this->db->join($j[0], $j[1], $j[2] ?? 'left');

        if (!empty($p['where']))     $this->db->where($p['where']);
        if (!empty($p['where_in']))  foreach ($p['where_in'] as $col => $vals) $this->db->where_in($col, (array)$vals);
        if (!empty($p['where_raw'])) foreach ((array)$p['where_raw'] as $raw)  $this->db->where($raw, NULL, FALSE);
        if (!empty($p['group_by']))  foreach ((array)$p['group_by'] as $g)     $this->db->group_by($g);
        if (!empty($p['having']))    is_array($p['having']) ? $this->db->having($p['having']) : $this->db->having($p['having'], NULL, FALSE);

        return $this->db->count_all_results();
    }

    // =========================================================
    // PUBLIC: GENERATE (DataTables Response)
    // =========================================================

    /**
     * Generate response DataTables server-side.
     *
     * @param  array    $params    Konfigurasi query (lihat contoh di bawah)
     * @param  callable $callback  fn($row, $no): array  — susun kolom di Controller
     * @return array
     *
     * ── Contoh $params lengkap ──────────────────────────────────────
     *
     * $params = [
     *   'table'  => 'grn_detail b',
     *
     *   // SELECT: string, ekspresi raw, atau campur keduanya
     *   'select' => [
     *     'b.DOC_NO, b.ITEM_CODE, b.ITEM_NAME',
     *     ['(b.RECEIVED_ENTERED_QTY / b.BASE_QTY) AS GRK', FALSE],
     *     ['(SELECT u.name FROM users u WHERE u.id = b.created_by LIMIT 1) AS creator', FALSE],
     *     ['COALESCE(b.REMARK, "-") AS remark', FALSE],
     *     ['DATE_FORMAT(b.DOC_DATE, "%d-%m-%Y") AS doc_date_fmt', FALSE],
     *   ],
     *
     *   // JOIN
     *   'joins'  => [
     *     ['grn_header h', 'h.DOC_NO = b.DOC_NO', 'left'],
     *     ['warehouse w',  'w.id = h.WH_ID',       'left'],
     *   ],
     *
     *   // WHERE biasa
     *   'where'  => ['b.deleted' => 0, 'h.STATUS' => 'APPROVED'],
     *
     *   // WHERE IN
     *   'where_in' => ['b.WH_CODE' => ['WH01', 'WH02']],
     *
     *   // WHERE raw / ekspresi
     *   'where_raw' => [
     *     'DATE(b.DOC_DATE) BETWEEN "2024-01-01" AND "2024-12-31"',
     *     'b.RECEIVED_ENTERED_QTY > 0',
     *   ],
     *
     *   // GROUP BY
     *   'group_by' => 'b.DOC_NO',
     *
     *   // HAVING raw
     *   'having' => 'SUM(b.RECEIVED_ENTERED_QTY) > 0',
     *   // atau HAVING array
     *   // 'having' => ['total >' => 100],
     *
     *   // Kolom untuk search (LIKE)
     *   'column_search' => ['b.DOC_NO', 'b.ITEM_CODE', 'b.ITEM_NAME'],
     *
     *   // Kolom untuk sort (index sesuai kolom DataTables, FALSE = tidak bisa sort)
     *   'column_order'  => [FALSE, 'b.DOC_NO', 'b.ITEM_CODE', 'b.ITEM_NAME', FALSE],
     *
     *   // Default order jika tidak ada input sort dari DataTables
     *   'order' => ['b.DOC_DATE' => 'desc'],
     * ];
     * ────────────────────────────────────────────────────────────────
     */
    public function generate(array $params, callable $callback): array
    {
        $this->_build_query($params);

        $length = $this->input->post('length');
        if ($length != -1) {
            $this->db->limit((int) $length, (int) $this->input->post('start'));
        }

        $list = $this->db->get();

        $no   = (int) $this->input->post('start');
        $data = [];
        foreach ($list ? $list->result():[] as $row) {
            $data[] = $callback($row, ++$no);
        }

        return [
            'draw'            => (int) $this->input->post('draw'),
            'recordsTotal'    => $this->_count_total($params),
            'recordsFiltered' => $this->_count_filtered($params),
            'data'            => $data,
        ];
    }
}