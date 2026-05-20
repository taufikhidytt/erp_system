<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (! defined('INPUT_NUMBER_THOUSAND'))    define('INPUT_NUMBER_THOUSAND',    ',');
if (! defined('INPUT_NUMBER_DECIMAL_SEP')) define('INPUT_NUMBER_DECIMAL_SEP', '.');

if (! function_exists('numb_format')) {
    function numb_format($value, $decimal = null, $thousand = INPUT_NUMBER_THOUSAND, $decimal_sep = INPUT_NUMBER_DECIMAL_SEP)
    {
        if ($decimal === null) {
            $ci = &get_instance();
            $decimal = (int) $ci->session->setup->CUSTOM2;
        }

        $formatted = number_format($value, $decimal, $decimal_sep, $thousand);

        return $formatted;
    }
}

if (! function_exists('numb_unformat')) {
    function numb_unformat($value, $thousand = INPUT_NUMBER_THOUSAND, $decimal_sep = INPUT_NUMBER_DECIMAL_SEP)
    {
        if ($value === null || $value === '') return null;

        if (is_float($value) || is_int($value)) return (float) $value;

        $clean = (string) $value;

        $clean = str_replace($thousand, '', $clean);

        if ($decimal_sep !== '.') {
            $clean = str_replace($decimal_sep, '.', $clean);
        }

        $clean = preg_replace('/[^0-9.\-]/', '', $clean);

        if ($clean === '' || $clean === '-') return 0.0;

        return (float) $clean;
    }
}

if (! function_exists('get_access')) {
    function get_access($url = '')
    {
        $ci         = &get_instance();
        $user_id    = (int) $ci->session->userdata('id');
        if (!$url) {
            $url = $ci->uri->segment(1);
        }

        $access = [];
        if ($url && $user_id) {
            $group_id   = (int) $ci->session->userdata('group');
            $ci->db->select('a.ERP_MENU_NAME,a.PROMPT,a.PERMISSIONS,
                b.VIEW_FLAG,b.INSERT_FLAG,b.UPDATE_FLAG,b.DELETE_FLAG,
                c.PERMISSIONS as USER_PERMISSIONS,
            ');
            $ci->db->from('erp_menu a');
            $ci->db->join('erp_group_menu b', 'b.ERP_MENU_ID = a.ERP_MENU_ID');
            $ci->db->join("erp_user_menu_permission c", "c.ERP_MENU_ID = a.ERP_MENU_ID AND c.ERP_USER_ID = $user_id", 'left');
            $ci->db->where('a.ACTIVE_FLAG', 'Y');
            $ci->db->where('b.VIEW_FLAG', 'Y');
            $ci->db->where('LOWER(a.ERP_MENU_NAME)', $url);
            $ci->db->where('b.ERP_GROUP_ID', $group_id);
            $row = $ci->db->get()->row_array();
            if (!empty($row)) {
                $access = [
                    'ERP_MENU_NAME' => $row['ERP_MENU_NAME'],
                    'PROMPT'        => $row['PROMPT'],
                    'url'           => strtolower($row['ERP_MENU_NAME']),
                    'view'          => $row['VIEW_FLAG'] == 'Y',
                    'insert'        => $row['INSERT_FLAG'] == 'Y',
                    'update'        => $row['UPDATE_FLAG'] == 'Y',
                    'delete'        => $row['DELETE_FLAG'] == 'Y',
                ];

                $permissions        = json_decode($row['PERMISSIONS'] ?: '[]', true);
                $user_permissions   = json_decode($row['USER_PERMISSIONS'] ?: '[]', true);

                if (!empty($permissions) && !empty($user_permissions)) {
                    foreach ($permissions as $type => $content) {
                        if ($type === 'actions' || $type === 'fields') {
                            foreach ($content as $value) {
                                if ($type == 'actions') {
                                    $access[$value] = isset($user_permissions[$type][$value]) && $user_permissions[$type][$value];
                                } else {
                                    $access[$type][$value] = isset($user_permissions[$type][$value]) && $user_permissions[$type][$value];
                                }
                            }
                        } elseif ($type === 'tabs') {
                            foreach ($content as $key => $tab_name) {
                                $real_tab_name = is_array($tab_name) ? $key : $tab_name;
                                $access['tabs'][$real_tab_name] = isset($user_permissions['tabs'][$real_tab_name]) && $user_permissions['tabs'][$real_tab_name];

                                if (is_array($tab_name)) {
                                    foreach ($tab_name as $field) {
                                        $access['tab_fields'][$real_tab_name][$field] = isset($user_permissions['tab_fields'][$real_tab_name][$field])  && $user_permissions['tab_fields'][$real_tab_name][$field];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $access;
    }
}

if (!function_exists('checkAccess')) {
    function checkAccess($action, $url = '')
    {
        $ci     = &get_instance();
        $access = $ci->load->get_var('access');
        $current_url = $ci->uri->segment(1);
        if (!isset($access[$action]) || !$access[$action]) {
            if (strpos($ci->input->server('HTTP_ACCEPT'), 'application/json') !== false) {
                sendWarning('Anda tidak ada akses hapus untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
                die();
            } else {
                $ci->session->set_flashdata('warning', 'Anda tidak ada akses hapus untuk menu ini, silahkan hubungi administrator untuk mendapatkan akses tersebut!');
                redirect($current_url);
                die();
            }
        }
    }
}

if (! function_exists('button_actions')) {
    /*
        <?= button_actions([
            'insert',
            'save',
            // Mode 2: template delete + data-id
            [
                'key'     => 'delete',
                'data-id' => $this->encrypt->encode($data->ITEM_ID),
            ],
            // Mode 3: redirect ke halaman approval
            [
                'key'      => 'approval',
                'redirect' => site_url('approval/form/' . $data->ITEM_ID),
                'class'    => 'btn-warning',
                'title'    => 'Approval',
                'icon'     => 'ri-shield-check-line',
            ],
            // Mode 3: trigger JS via class_custom + data-*
            [
                'key'          => 'send_notif',
                'class'        => 'btn-info',
                'class_custom' => 'btn-send-notif',
                'title'        => 'Kirim Notif',
                'icon'         => 'ri-notification-3-line',
                'data-id'      => $this->encrypt->encode($data->ITEM_ID),
                'data-name'    => $data->ITEM_NAME,
            ],
            'reload',
        ]) ?>
    */
    function button_actions($arr, $type = 'html')
    {
        $ci     = &get_instance();
        $access = $ci->load->get_var('access');
        $p1     = $ci->uri->segment(2);

        $template = [
            'insert' => ['icon' => 'ri-add-circle-fill',   'class' => 'btn-primary',   'class_custom' => '',           'title' => 'Tambah',  'url' => 'add',       'needs_auth' => true],
            'save'   => ['icon' => 'ri-save-3-fill',       'class' => 'btn-success',   'class_custom' => '',           'title' => 'Simpan',  'type' => 'submit',   'needs_auth' => true],
            'export' => ['icon' => 'ri-file-excel-line',   'class' => 'btn-success',   'class_custom' => '',           'title' => 'Export',  'extend' => 'excel',  'needs_auth' => true],
            'print_out'  => ['icon' => 'ri-printer-fill',      'class' => 'btn-danger',    'class_custom' => '',           'title' => 'Print',   'target' => '_blank', 'needs_auth' => true],
            'delete' => ['icon' => 'ri-delete-bin-5-fill', 'class' => 'btn-danger',    'class_custom' => 'btn-delete', 'title' => 'Hapus',   'needs_auth' => true],
            'reload' => ['icon' => 'ri-reply-fill',        'class' => 'btn-warning',   'class_custom' => '',           'title' => 'Reload',  'onclick' => 'window.location.reload()', 'needs_auth' => false],
            'back'   => ['icon' => 'ri-arrow-left-line',   'class' => 'btn-secondary', 'class_custom' => '',           'title' => 'Kembali', 'onclick' => 'history.back()',    'needs_auth' => false],
            'close' => ['icon' => 'ri-close-circle-fill', 'class' => 'btn-dark',    'class_custom' => 'btn-close-header', 'title' => 'Close',   'needs_auth' => true],
        ];

        $map = [
            'add'    => $access['insert'] ?? false,
            'detail' => $access['update'] ?? false,
        ];

        $results = [];

        foreach ($arr as $item) {
            // ── Mode 1: String → pakai template ──────────────────────────────
            if (! is_array($item)) {
                if (! isset($template[$item])) continue;

                $key = $item;
                $btn = $template[$key];
                $hasAccess = $btn['needs_auth']
                    ? ($key === 'save' ? ($map[$p1] ?? false) : ($access[$key] ?? false))
                    : true;

                if (! $hasAccess) continue;
            }
            // ── Mode 2: Array + key ada di template ──────────────────────────
            elseif (isset($item['key']) && isset($template[$item['key']])) {
                $key = $item['key'];
                $btn = $template[$key];

                // Override class jika ada
                if (isset($item['class']))        $btn['class']        = $item['class'];
                if (isset($item['class_custom'])) $btn['class_custom'] = $item['class_custom'];
                if (isset($item['title']))        $btn['title']        = $item['title'];
                if (isset($item['icon']))         $btn['icon']         = $item['icon'];
                if (isset($item['target']))       $btn['target']       = $item['target'];

                if (isset($item['url'])) {
                    $btn['url'] = $item['url'];
                }
                if (isset($item['raw_url'])) {
                    $btn['raw_url'] = $item['raw_url'];
                }
                // redirect → render sebagai <a href>
                if (isset($item['redirect'])) {
                    $btn['url']     = $item['redirect'];
                    $btn['raw_url'] = true;
                }

                if (isset($item['target'])) $btn['target'] = $item['target'];
                if (isset($item['type']))   $btn['type']   = $item['type'];

                // Collect semua data-* attribute
                foreach ($item as $attr => $val) {
                    if (strpos($attr, 'data-') === 0) $btn[$attr] = $val;
                }

                $hasAccess = $access[$key] ?? false;
                if (! $hasAccess) continue;
            }
            // ── Mode 3: Fully custom (key tidak ada di template) ─────────────
            elseif (isset($item['key'])) {
                $btn = [
                    'icon'         => $item['icon']         ?? 'ri-checkbox-circle-fill',
                    'class'        => $item['class']        ?? 'btn-secondary',
                    'class_custom' => $item['class_custom'] ?? '',
                    'title'        => $item['title']        ?? ucfirst($item['key']),
                    'needs_auth'   => $item['needs_auth']   ?? false,
                ];

                // redirect → render sebagai <a href>
                if (isset($item['redirect'])) {
                    $btn['url']     = $item['redirect'];
                    $btn['raw_url'] = true;
                }

                if (isset($item['target'])) $btn['target'] = $item['target'];
                if (isset($item['type']))   $btn['type']   = $item['type'];

                // Collect semua data-* attribute
                foreach ($item as $attr => $val) {
                    if (strpos($attr, 'data-') === 0) $btn[$attr] = $val;
                }

                if ($btn['needs_auth']) {
                    $hasAccess = $access[$item['key']] ?? false;
                    if (! $hasAccess) continue;
                }
            } else {
                continue;
            }

            // ── Render ────────────────────────────────────────────────────────
            $extra_class = $btn['class_custom'] ?? '';

            // Kumpulkan semua data-* untuk render
            $data_attrs = '';
            foreach ($btn as $attr => $val) {
                if (strpos($attr, 'data-') === 0) {
                    $data_attrs .= ' ' . $attr . '="' . htmlspecialchars($val) . '"';
                }
            }

            if ($type === 'dt') {
                $url = null;
                if (isset($btn['raw_url']) && $btn['raw_url']) {
                    $url = $btn['url'];
                } elseif (isset($btn['url'])) {
                    $url = base_url($ci->uri->segment(1) . '/' . $btn['url']);
                }

                $results[] = [
                    'text'      => '<i class="' . $btn['icon'] . '"></i>',
                    'className' => 'btn btn-sm ' . $btn['class'] . ' ' . $extra_class,
                    'url'       => $url,
                    'titleAttr' => $btn['title'],
                    'attr'      => [
                        'data-toggle' => 'tooltip',
                        'data-bs-placement' => 'left'
                    ]
                ];
            } else {
                if (isset($btn['url'])) {
                    $tag  = 'a';
                    $href = 'href="' . (isset($btn['raw_url']) && $btn['raw_url']
                        ? $btn['url']
                        : base_url($ci->uri->segment(1) . '/' . $btn['url'])) . '"';
                } else {
                    $tag  = 'button';
                    $href = '';
                }

                $type_attr = isset($btn['type'])   ? 'type="'   . $btn['type']   . '"' : 'type="button"';
                $target    = isset($btn['target'])  ? 'target="' . $btn['target'] . '"' : '';
                $onclick   = isset($btn['onclick']) ? 'onclick="' . $btn['onclick'] . '"' : '';

                $results[] = "<$tag $href $type_attr $target $onclick $data_attrs class='btn btn-sm {$btn['class']} $extra_class' data-toggle='tooltip' data-bs-placement='left' title='{$btn['title']}'><i class='{$btn['icon']}'></i></$tag>";
            }
        }

        return ($type === 'dt') ? $results : implode(' ', $results);
    }
}

if(! function_exists('folder_key')){
    function folder_key()
    {
        $ci     = &get_instance();
        $db_info = [
            'hostname' => $ci->db->hostname,
            'database' => $ci->db->database,
            'port'     => $ci->db->port,
        ];
        return sha1(sha1(json_encode($db_info)));
    }
}

if(! function_exists('get_bi_data')){
    function get_bi_data()
    {
        $cache_file = APPPATH . 'cache/bi_exchange_rates.json';
        $cache_ttl  = 6 * 3600; // 6 jam dalam detik

        // Cek cache valid
        if (file_exists($cache_file)) {
            $cache = json_decode(file_get_contents($cache_file), true);
            if (
                isset($cache['last_sync']) &&
                (time() - $cache['last_sync']) < $cache_ttl
            ) {
                return $cache;
            }
        }

        try {
            $data = fetch_bi_data();
            $data['last_sync'] = time();

            file_put_contents($cache_file, json_encode($data), LOCK_EX);

            return $data;
        } catch (Exception $e) {
            if (isset($cache['last_sync'])) {
                return $cache;
            }
            throw $e;
        }
    }
}

if(! function_exists('fetch_bi_data')){
    function fetch_bi_data()
    {
        $url = 'https://www.bi.go.id/id/statistik/informasi-kurs/transaksi-bi/Default.aspx';

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
                'Accept-Encoding: gzip, deflate, br',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1',
            ],
        ]);

        $output = curl_exec($ch);
        $errno  = curl_errno($ch);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($errno) {
            $cache_file = APPPATH . 'cache/bi_exchange_rates.json';
            if (file_exists($cache_file)) {
                $cache = json_decode(file_get_contents($cache_file), true);
                if (isset($cache['data'])) {
                    return $cache['data'];
                }
            }
            throw new Exception('Gagal mengambil data BI: ' . $error);
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($output);
        $xpath = new DOMXPath($dom);

        // Parse last_update
        $last_update      = '';
        $last_update_node = $xpath->query(
            "//div[contains(@class,'search-box-wrapper') and contains(text(),'Update Terakhir')]/span"
        )->item(0);

        if ($last_update_node) {
            $raw    = trim($last_update_node->nodeValue);
            $months = [
                'Januari'=>'01','Februari'=>'02','Maret'=>'03','April'=>'04',
                'Mei'=>'05','Juni'=>'06','Juli'=>'07','Agustus'=>'08',
                'September'=>'09','Oktober'=>'10','November'=>'11','Desember'=>'12',
                'Jan'=>'01','Feb'=>'02','Mar'=>'03','Apr'=>'04','May'=>'05',
                'Jun'=>'06','Jul'=>'07','Aug'=>'08','Sep'=>'09',
                'Oct'=>'10','Nov'=>'11','Dec'=>'12',
            ];
            if (preg_match('/(\d+)\s+(\w+)\s+(\d{4})/', $raw, $m)) {
                $last_update = sprintf(
                    '%s-%s-%s',
                    $m[3],
                    $months[$m[2]] ?? '01',
                    str_pad($m[1], 2, '0', STR_PAD_LEFT)
                );
            }
        }

        $result = [
            'last_update'    => $last_update,
            'source'         => 'Bank Indonesia',
            'base_currency'  => 'IDR',
            'exchange_rates' => [],
        ];

        $rows = $xpath->query("//table[contains(@class,'table')]//tbody/tr");
        foreach ($rows as $row) {
            $cols = $xpath->query('td', $row);
            if ($cols->length < 4) continue;

            $currency = trim($cols->item(0)->nodeValue);
            $unit     = (float) str_replace('.', '', trim($cols->item(1)->nodeValue));
            $selling  = (float) str_replace(['.', ','], ['', '.'], trim($cols->item(2)->nodeValue));
            $buying   = (float) str_replace(['.', ','], ['', '.'], trim($cols->item(3)->nodeValue));

            $result['exchange_rates'][$currency] = [
                'unit'         => $unit,
                'selling_rate' => $selling,
                'buying_rate'  => $buying,
                'middle_rate'  => (($selling + $buying) / 2),
            ];
        }

        return $result;
    }
}

if(! function_exists('get_currencies')){
    function get_currencies()
    {
        $cache_file = APPPATH . 'cache/currencies.json';
        $cache_ttl  = 24 * 3600; // 6 jam dalam detik

        if (file_exists($cache_file)) {
            $cache = json_decode(file_get_contents($cache_file), true);
            if (
                isset($cache['last_sync']) &&
                (time() - $cache['last_sync']) < $cache_ttl
            ) {
                return $cache;
            }
        }

        try {
            $data = fetch_currencies();
            $data['last_sync'] = time();

            file_put_contents($cache_file, json_encode($data), LOCK_EX);

            return $data;
        } catch (Exception $e) {
            if (isset($cache['last_sync'])) {
                return $cache;
            }
            throw $e;
        }
    }
}

if(! function_exists('fetch_currencies')){
    function fetch_currencies()
    {
        // https://api.frankfurter.dev/v2/rates?base=USD&quotes=IDR
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => 'https://api.frankfurter.dev/v2/currencies',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_USERAGENT      => 'Mozilla/5.0',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        $output = curl_exec($ch);
        $errno  = curl_errno($ch);
        $error  = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            throw new Exception('Curl error: ' . $error);
        }

        if ($http_code !== 200) {
            throw new Exception('API error: HTTP ' . $http_code);
        }

        $data = json_decode($output, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response');
        }

        $result = [];

        foreach ($data as $v) {
            $result[$v['iso_code']] = $v;
        }

        return $result;
    }
}