<?php
date_default_timezone_set("Asia/jakarta");
$logo           = file_exists('./assets/logo/' . $this->session->setup->LOGO_FILENAME) ? 'assets/logo/' . $this->session->setup->LOGO_FILENAME : 'assets/logo/logo.png';
$active_theme   = isset($_COOKIE['app-theme']) ? $_COOKIE['app-theme'] : 'light';
$active_lang    = isset($_COOKIE['app-lang']) ? $_COOKIE['app-lang'] : 'id';
$bs_css         = $active_theme !== 'dark' ? 'bootstrap.min.css' : 'bootstrap-dark.min.css';
$app_css        = $active_theme !== 'dark' ? 'app.min.css' : 'app-dark.min.css';
$primary_color  = isset($_COOKIE['app-primary']) ? $_COOKIE['app-primary'] : '#5664d2';
$th_color       = isset($_COOKIE['app-primary']) ? $_COOKIE['app-primary'] : '#3d7bb9';
$show_datetime  = isset($_COOKIE['app-show-datetime'])  ? $_COOKIE['app-show-datetime']  : '1';
$date           = date('d M Y');

$lang_options = [
    'id' => ['flag' => '🇮🇩', 'label' => 'Indonesia'],
    'en' => ['flag' => '🇬🇧', 'label' => 'English'],
];
$color_presets = [
    '#556ee6' => 'Indigo',
    '#34c38f' => 'Hijau',
    '#f46a6a' => 'Merah',
    '#f1b44c' => 'Kuning',
    '#50a5f1' => 'Biru',
    '#74788d' => 'Abu',
    '#e83e8c' => 'Pink',
];
$is_custom_color = !array_key_exists($primary_color, $color_presets);
?>
<!DOCTYPE html>
<html lang="<?= $active_lang ?>">

<head>
    <meta charset="utf-8" />
    <title><?= $title; ?> | <?= $this->session->setup->NAME; ?></title>
    <link rel="icon" type="image/png" href="<?= base_url($logo) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Konsinyasi untuk manajemen data, monitoring transaksi, dan laporan keuangan secara real-time, akurat, dan aman.">

    <!-- DataTables -->
    <link href="<?= base_url() ?>assets/admin/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/admin/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/admin/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="<?= base_url() ?>assets/admin/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="<?= base_url() ?>assets/admin/css/<?= $bs_css ?>" id="bootstrap-style" rel="stylesheet" type="text/css" />

    <!-- Icons Css -->
    <link href="<?= base_url() ?>assets/admin/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- App Css-->
    <link href="<?= base_url() ?>assets/admin/css/<?= $app_css ?>" id="app-style" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert-->
    <link href="<?= base_url() ?>assets/admin/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- Select2-->
    <link href="<?= base_url() ?>assets/admin/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <link href="<?= base_url() ?>assets/admin/libs/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/admin/css/aos.css">
    <?= generateDynamicTheme($primary_color, $th_color); ?>
    <link href="<?= base_url() ?>assets/admin/css/custom-template.css?v=1.7" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/admin/css/custom-dark.css?v=1.6" id="custom-dark-style" rel="stylesheet" type="text/css" />

    <script src="<?= base_url() ?>assets/admin/libs/jquery/jquery.min.js"></script>
    <script>
        var config_app = {
            decimal: <?= $this->session->setup->CUSTOM2 ?? 2; ?>,
            url: '<?= site_url() ?>'
        }
    </script>
    <script src="<?= base_url() ?>assets/admin/js/input_number.js?v=1.6"></script>
    <script src="<?= base_url() ?>assets/admin/js/custom.js?v=1.6"></script>
</head>

<body data-sidebar="dark" data-theme="<?= $active_theme ?>" data-update="<?= $access['update'] ?? false ?>">
    <div id="flashSuccess" data-success="<?= $this->session->flashdata('success'); ?>"></div>
    <div id="flashWarning" data-warning="<?= $this->session->flashdata('warning'); ?>"></div>
    <div id="flashError" data-error="<?= $this->session->flashdata('error'); ?>"></div>
    <div id="loading" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <!-- <body data-layout="horizontal" data-topbar="dark"> -->
    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box align-content-center">
                        <a href="<?= base_url('dashboard') ?>" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="<?= base_url($logo) ?>" alt="logo" style="height: 30px; width: auto; object-fit: contain;">
                            </span>
                            <span class="logo-lg">
                                <h5 class="text-white"><?= strtoupper($this->session->setup->NAME) ?></h5>
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn" aria-label="Toggle Menu">
                        <i class="ri-menu-2-line align-middle"></i>
                    </button>

                    <form class="app-search d-none d-lg-block">
                        <div class="position-relative">
                            <input type="text" class="form-control" placeholder="ketik / untuk mencari..." style="font-family: 'Poppins' !important;">
                            <span class="ri-search-line"></span>
                        </div>
                    </form>
                </div>

                <div class="d-flex">
                    <div class="text-center<?= $show_datetime === '0' ? ' d-none' : '' ?>" id="topbar-datetime" style="font-size: 12px; letter-spacing: 5px; margin: 20px 20px 0px 0px;">
                        <div><?= $date ?></div>
                        <div id="jam"></div>
                    </div>

                    <div class="dropdown d-inline-block d-lg-none ms-2">
                        <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-search-line"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-search-dropdown">

                            <form class="p-3">
                                <div class="mb-3 m-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search ...">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="dropdown d-inline-block d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" id="settings-panel-btn" title="Pengaturan Tampilan">
                            <i class="ri-settings-3-line"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block user-dropdown">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="<?= base_url() ?>assets/upload/photo-profile/default.jpg"
                                alt="Header Avatar">
                            <div class="d-none d-xl-inline-block ms-1">
                                <div style="display: grid;text-align: start;">
                                    <span><?= $this->session->userdata('nama'); ?></span>
                                    <span style="font-size: x-small; color: #555555;"><i class="mdi mdi-circle-medium circle-dot text-success me-1"></i><?= $this->session->userdata('db_alias'); ?></span>
                                </div>
                            </div>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <div class="d-md-none d-sm-block bg-dark border rounded-2 me-2 ms-2 mb-1 px-2 py-1" style="display: grid;">
                                <span class="text-light"><?= $this->session->userdata('nama'); ?></span>
                                <span style="font-size: x-small; color: #555555;"><i class="mdi mdi-circle-medium circle-dot text-success me-1"></i><?= $this->session->userdata('db_alias'); ?></span>
                            </div>
                            <a class="dropdown-item" href="<?= base_url('profile') ?>">
                                <i class="ri-user-line align-middle me-1"></i> Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>">
                                <i class="ri-shut-down-line align-middle me-1 text-danger"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- ===== Settings Drawer Panel ===== -->
        <div id="settings-panel-overlay">
            <div id="settings-panel-backdrop"></div>
            <div id="settings-panel">
                <div class="sp-header">
                    <p class="sp-title"><i class="ri-settings-3-line me-1"></i> Pengaturan Tampilan</p>
                    <button class="sp-close" id="settings-panel-close" aria-label="Tutup">&times;</button>
                </div>
                <div class="sp-body">

                    <!-- Tema -->
                    <div class="sp-section">
                        <div class="sp-section-label">Tema</div>
                        <div class="sp-theme-row">
                            <div class="sp-theme-opt" onclick="spSetTheme('light')">
                                <div class="sp-theme-preview light <?= $active_theme === 'light' ? 'active' : '' ?>" id="sp-theme-light">
                                    <div class="tp-top"></div>
                                    <div class="tp-bottom"></div>
                                </div>
                                <span class="sp-theme-name">Terang</span>
                            </div>
                            <div class="sp-theme-opt" onclick="spSetTheme('dark')">
                                <div class="sp-theme-preview dark <?= $active_theme === 'dark' ? 'active' : '' ?>" id="sp-theme-dark">
                                    <div class="tp-top"></div>
                                    <div class="tp-bottom"></div>
                                </div>
                                <span class="sp-theme-name">Gelap</span>
                            </div>
                        </div>
                    </div>

                    <!-- Warna Primer -->
                    <div class="sp-section">
                        <div class="sp-section-label">Warna Primer</div>
                        <div class="sp-color-row">
                            <?php foreach ($color_presets as $hex => $name): ?>
                                <div class="sp-swatch <?= (!$is_custom_color && $primary_color === $hex) ? 'active' : '' ?>"
                                    style="background: <?= $hex ?>;"
                                    data-color="<?= $hex ?>"
                                    title="<?= $name ?>"
                                    onclick="spSetColor('<?= $hex ?>')">
                                </div>
                            <?php endforeach; ?>
                            <!-- Custom color swatch -->
                            <div class="sp-swatch sp-swatch-custom <?= $is_custom_color ? 'active' : '' ?>"
                                id="sp-swatch-custom"
                                style="background: <?= $is_custom_color ? $primary_color : '#cccccc' ?>;"
                                title="Warna Custom"
                                onclick="document.getElementById('sp-color-picker').click()">
                                <i class="ri-add-line" id="sp-swatch-custom-icon" style="font-size:14px; line-height:22px; display:<?= $is_custom_color ? 'none' : 'block' ?>;"></i>
                            </div>
                            <input type="color" id="sp-color-picker"
                                value="<?= $is_custom_color ? $primary_color : '#556ee6' ?>"
                                style="position:absolute;opacity:0;width:0;height:0;pointer-events:none;"
                                oninput="spSetCustomColor(this.value)"
                                onchange="spSetCustomColor(this.value)">
                        </div>
                        <div class="sp-color-hex-row">
                            <span class="sp-color-hex-label">Hex:</span>
                            <div class="sp-hex-preview"></div>
                            <input type="text" id="sp-color-hex-input" maxlength="7"
                                value="<?= htmlspecialchars($primary_color) ?>"
                                placeholder="#556ee6"
                                oninput="spOnHexInput(this.value)"
                                onblur="spOnHexBlur(this.value)" readonly>
                        </div>
                    </div>

                    <!-- Shortcut / Quick Actions -->
                    <div class="sp-section">
                        <div class="sp-section-label">Aksi Cepat</div>
                        <div class="sp-quick-actions">
                            <button class="sp-quick-btn" onclick="spToggleFullscreen()">
                                <i class="ri-fullscreen-line" id="sp-fs-icon"></i>
                                <span id="sp-fs-label">Fullscreen</span>
                            </button>
                            <button class="sp-quick-btn" onclick="spOpenInfo()">
                                <i class="ri-information-line"></i>
                                <span>Informasi</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tampilan Topbar -->
                    <div class="sp-section">
                        <div class="sp-section-label">Tampilan Topbar</div>
                        <div class="sp-toggle-row">
                            <span class="sp-toggle-label"><i class="ri-time-line me-1"></i> Tampilkan Tanggal &amp; Jam</span>
                            <label class="sp-toggle-switch">
                                <input type="checkbox" id="sp-toggle-datetime" <?= $show_datetime === '1' ? 'checked' : '' ?> onchange="spToggleDatetime(this.checked)">
                                <span class="sp-toggle-slider"></span>
                            </label>
                        </div>
                    </div>

                    <!-- Bahasa -->
                    <div class="sp-section d-none">
                        <div class="sp-section-label">Bahasa / Language</div>
                        <div class="sp-lang-grid">
                            <?php foreach ($lang_options as $code => $opt): ?>
                                <button class="sp-lang-opt <?= $active_lang === $code ? 'active' : '' ?>"
                                    data-lang="<?= $code ?>"
                                    onclick="spSetLang('<?= $code ?>')">
                                    <span class="sp-flag"><?= $opt['flag'] ?></span>
                                    <span><?= $opt['label'] ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
                <div class="sp-footer">
                    <button class="sp-btn" onclick="spClose()">Batal</button>
                    <button class="sp-btn danger" onclick="spReset()">Reset</button>
                </div>
            </div>
        </div>
        <!-- ===== End Settings Drawer Panel ===== -->

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar="init" class="h-100">
                <div class="simplebar-wrapper" style="margin: 0px;">
                    <div class="simplebar-height-auto-observer-wrapper">
                        <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                        <div class="simplebar-offset" style="right: -15px; bottom: 0px;">
                            <div class="simplebar-content-wrapper" style="height: 100%; overflow: hidden scroll;">
                                <div class="simplebar-content" style="padding: 0px;">

                                    <!--- Sidemenu -->
                                    <div id="sidebar-menu">
                                        <!-- Left Menu Start -->
                                        <ul class="metismenu list-unstyled" id="side-menu">
                                            <li class="menu-title">Main Menus</li>
                                            <li>
                                                <a href="<?= base_url('dashboard') ?>" class="waves-effect">
                                                    <i class="ri-dashboard-line"></i>
                                                    <span>Dashboard</span>
                                                </a>
                                            </li>

                                            <!-- isi menu dinamis -->
                                            <?php
                                            $id = $this->checkusers->users_login()->ERP_GROUP_ID;

                                            $master_menu = $this->db->query("SELECT * FROM erp_menu JOIN erp_group_menu ON erp_group_menu.erp_menu_id = erp_menu.erp_menu_id WHERE active_flag = 'y' AND parent_id = 0 AND erp_group_id = $id GROUP BY erp_menu_name ORDER BY seq ASC")->result();

                                            $id_parent = [];

                                            foreach ($master_menu as $mam) {
                                                $id_parent[] = $mam->ERP_MENU_ID;
                                            }

                                            $parent = $this->db->query("SELECT * FROM erp_menu WHERE erp_menu_id IN ('" . implode("', '", $id_parent) . "') AND active_flag = 'Y' ORDER BY SEQ ASC")->result();

                                            $id = $this->checkusers->users_login()->ERP_GROUP_ID;

                                            foreach ($parent as $main) :
                                                $child = $this->db->query("SELECT * FROM erp_menu JOIN erp_group_menu ON erp_group_menu.erp_menu_id = erp_menu.erp_menu_id WHERE erp_menu.parent_id IN ($main->ERP_MENU_ID) AND active_flag = 'Y' AND erp_group_menu.erp_group_id = $id AND erp_group_menu.view_flag = 'Y' ORDER BY seq ASC");
                                            ?>
                                                <li>
                                                    <a href="#" class="has-arrow waves-effect" aria-expanded="false">
                                                        <i class="<?= $main->MENU_ICON; ?>"></i>
                                                        <span><?= $main->PROMPT ?></span>
                                                    </a>
                                                    <?php if ($child->num_rows() > 0): ?>
                                                        <ul class="sub-menu" aria-expanded="false">
                                                            <?php foreach ($child->result() as $ch): ?>
                                                                <li class="<?= $this->uri->segment(1) == strtolower($ch->ERP_MENU_NAME) ? 'mm-active' : null ?>"><a href="<?= base_url(strtolower($ch->ERP_MENU_NAME)) ?>"><?= $ch->PROMPT ?></a></li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </li>

                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <!-- Sidebar -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: auto; height: 1006px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                    <div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: block; height: 286px;"></div>
                </div>
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <?= $contents ?>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6 fw-bold">
                            &copy; <?= date('Y') ?> &middot; PT INTINUSA SEJAHTERA INTERNASIONAL.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block fw-bold">
                                Created by <span class="bold text-primary font-size-15">Team IT.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="<?= base_url() ?>assets/admin/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/metismenu/metisMenu.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/node-waves/waves.min.js"></script>



    <!-- Required datatable js -->
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/jszip/jszip.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/pdfmake/build/pdfmake.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/pdfmake/build/vfs_fonts.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>

    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-select/js/dataTables.select.min.js"></script>

    <!-- Responsive examples -->
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <!-- Datatable init js -->
    <script src="<?= base_url() ?>assets/admin/js/pages/datatables.init.js"></script>

    <!-- Sweet Alerts js -->
    <script src="<?= base_url() ?>assets/admin/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Select 2 -->
    <script src="<?= base_url() ?>assets/admin/libs/select2/js/select2.min.js"></script>

    <script src="<?= base_url() ?>assets/admin/js/app.js"></script>
    <script src="<?= base_url() ?>assets/admin/js/pages/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="<?= base_url() ?>assets/admin/js/shortcut.js?v=1.6"></script>
    <script src="<?= base_url() ?>assets/admin/js/custom-template.js?v=1.8"></script>
</body>

</html>