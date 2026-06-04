<?php
date_default_timezone_set("Asia/jakarta");
$logo = file_exists('./assets/logo/' . $this->session->setup->LOGO_FILENAME) ? 'assets/logo/' . $this->session->setup->LOGO_FILENAME : 'assets/logo/logo.png';
$active_theme = isset($_COOKIE['app-theme']) ? $_COOKIE['app-theme'] : 'light';
$bs_css = $active_theme !== 'dark' ? 'bootstrap.min.css' : 'bootstrap-dark.min.css';
$app_css = $active_theme !== 'dark' ? 'app.min.css' : 'app-dark.min.css';
$date = date('d M Y');
?>
<!DOCTYPE html>
<html lang="en">
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
    <link href="<?= base_url() ?>assets/admin/css/custom-template.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/admin/css/custom-dark.css" id="custom-dark-style" rel="stylesheet" type="text/css" />

    <script src="<?= base_url() ?>assets/admin/libs/jquery/jquery.min.js"></script>
    <script>
        var config_app = {
            decimal: <?= $this->session->setup->CUSTOM2 ?? 2; ?>,
            url: '<?= site_url() ?>'
        }
    </script>
    <script src="<?= base_url() ?>assets/admin/js/input_number.js?v=1.2"></script>
    <script src="<?= base_url() ?>assets/admin/js/custom.js?v=1.14"></script>
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
                    <div class="text-center" style="font-size: 12px; letter-spacing: 5px; margin: 20px 20px 0px 0px;">
                        <div><?= $date ?></div>
                        <div id="jam"></div>
                    </div>
                    <div class="dropdown d-inline-block d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen" aria-label="Fullscreen">
                            <i class="ri-fullscreen-line"></i>
                        </button>
                    </div>
                    <div class="dropdown d-inline-block d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" id="theme-toggle-btn" title="Switch Theme" aria-label="Switch Theme">
                            <i class="<?= $active_theme === 'light' ? 'ri-moon-line' : 'ri-sun-line' ?>" id="theme-icon"></i>
                        </button>
                    </div>
                    <div class="dropdown d-inline-block d-lg-inline-block ms-1 d-none">
                        <button type="button" class="btn header-item noti-icon waves-effect" onclick="openSheet()" aria-label="Open Sheet">
                            <i class="ri-information-line"></i>
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
    <script src="<?= base_url() ?>assets/admin/js/shortcut.js?v=1.0"></script>
    <script src="<?= base_url() ?>assets/admin/js/custom-template.js?v=1.0"></script>
</body>

</html>