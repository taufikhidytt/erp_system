<?php
$logo = file_exists('./assets/logo/' . $this->session->setup->LOGO_FILENAME) ? 'assets/logo/' . $this->session->setup->LOGO_FILENAME : 'assets/logo/logo.png';
?>
<!DOCTYPE html>

<head>
    <meta charset="utf-8" />
    <title><?= $title; ?> | <?= $this->session->setup->NAME; ?></title>
    <link rel="icon" type="image/png" href="<?= base_url($logo) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- DataTables -->
    <link href="<?= base_url() ?>assets/admin/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/admin/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url() ?>assets/admin/libs/datatables.net-select-bs4/css//select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="<?= base_url() ?>assets/admin/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="<?= base_url() ?>assets/admin/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- Icons Css -->
    <link href="<?= base_url() ?>assets/admin/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- App Css-->
    <link href="<?= base_url() ?>assets/admin/css/app.min.css" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert-->
    <link href="<?= base_url() ?>assets/admin/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- Select2-->
    <link href="<?= base_url() ?>assets/admin/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <link href="<?= base_url() ?>assets/admin/libs/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="<?= base_url() ?>assets/admin/css/aos.css">

    <style>
        @font-face {
            font-family: 'Poppins';
            src: url('<?= base_url('assets/admin/fonts/poppins/Poppins-Regular.ttf') ?>') format('truetype');
            font-weight: 400;
            font-style: normal;
        }

        @font-face {
            font-family: 'Poppins';
            src: url('<?= base_url('assets/admin/fonts/poppins/Poppins-Bold.ttf') ?>') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        body {
            /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
            /* font-family: Tahoma !important; */
            font-family: 'Poppins', sans-serif !important;
        }
        #sidebar-menu ul li ul.sub-menu li a, .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6{
            font-family: 'Poppins', sans-serif !important;
        }
        #sidebar-menu ul li a, .select2-container--bootstrap-5 .select2-selection{
            font-family: 'Poppins', sans-serif !important;
        }
        input, optgroup, select, textarea{
            /* font-family: 'Courier New', Courier, monospace; */
            font-family: monospace !important;
            font-size: 0.8rem !important;
        }
        .label-status .badge{
            font-size: 1.25rem !important;
        }
        table thead th input, table thead th select{
            font-family: 'Poppins', sans-serif !important;
            font-size: 0.7rem !important;
        }
        .dataTables_length label, .dataTables_length select, .dataTables_length select option{
            font-family: 'Poppins', sans-serif !important;
            font-size: 0.75rem !important;
        }
        .modal .table thead th, .modal .table tbody td, .modal .dataTables_wrapper{
            font-size: 0.75rem !important;
        }
        button{
            font-family: 'Poppins', sans-serif !important;
            font-size: 0.75rem !important;
        }
        .swal2-popup{
            font-family: 'Poppins', sans-serif !important;
        }
        .swal2-popup pre{
            font-family: 'Poppins', sans-serif !important;
            font-size: 1rem !important;
        }

        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner-border {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        table.table thead,
        tbody {
            text-align: left !important;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .page-content {
            font-size: 12px;
        }

        /* ===================== */
        /* Global XS Form Size   */
        /* ===================== */
        .form-xs .form-control,
        .form-xs .form-select,
        .form-xs textarea.form-control {
            height: 30px !important;
            min-height: 30px !important;
            padding: 2px 6px !important;
            /* font-size: 0.75rem !important; */
            font-size: 0.8rem !important;
        }

        /* Input-group icon */
        .form-xs .input-group-text {
            height: 30px !important;
            padding: 2px 6px !important;
            /* font-size: 0.75rem !important; */
            font-size: 0.8rem !important;
        }

        /* ===================== */
        /* Select2 XS            */
        /* ===================== */
        .form-xs .select2-container .select2-selection--single {
            height: 30px !important;
            min-height: 30px !important;
            padding: 0 6px !important;
            font-size: 0.75rem !important;
            display: flex;
            align-items: center;
        }

        .form-xs .select2-selection__rendered {
            line-height: 22px !important;
            font-size: 0.75rem !important;
        }

        .form-xs .select2-selection__arrow {
            height: 22px !important;
        }

        /* Textarea fix */
        .form-xs textarea {
            resize: vertical;
        }

        .nav-tabs .nav-link.active {
            background-color: #e9ecef;
        }

        .details-control {
            font-weight: bolder;
            color: #1DAA61;
        }

        /* 1. Reset posisi sel header */
        table.dataTable.table-sm thead th {
            position: relative !important;
            vertical-align: middle !important;
        }

        /* 2. Reset total pseudo-elements dan MATIKAN properti 'bottom' bawaan */
        table.dataTable.table-sm thead .sorting::before,
        table.dataTable.table-sm thead .sorting_asc::before,
        table.dataTable.table-sm thead .sorting_desc::before,
        table.dataTable.table-sm thead .sorting::after,
        table.dataTable.table-sm thead .sorting_asc::after,
        table.dataTable.table-sm thead .sorting_desc::after {
            position: absolute !important;
            bottom: auto !important; /* KUNCI UTAMA: Matikan posisi bottom bawaan DataTables */
            top: 50% !important;     /* Tarik ke tepat garis tengah vertikal */
            right: 0.5em !important; /* Jarak dari kanan sel */
            margin-top: 0 !important;
            line-height: 0.1 !important;
        }

        /* 3. Atur panah atas (Before) agar duduk tepat di ATAS garis tengah 50% */
        table.dataTable.table-sm thead .sorting::before,
        table.dataTable.table-sm thead .sorting_asc::before,
        table.dataTable.table-sm thead .sorting_desc::before {
            transform: translateY(-100%) !important;
            padding-bottom: 2px !important; /* Memberi sedikit jarak di tengah */
        }

        /* 4. Atur panah bawah (After) agar menggantung tepat di BAWAH garis tengah 50% */
        table.dataTable.table-sm thead .sorting::after,
        table.dataTable.table-sm thead .sorting_asc::after,
        table.dataTable.table-sm thead .sorting_desc::after {
            transform: translateY(0%) !important;
            padding-top: 2px !important; /* Memberi sedikit jarak di tengah */
        }

        .dataTable tbody td {
            font-family: monospace;   
        }
        .dataTable tbody td, .dataTable thead th{
            white-space: nowrap;
        }

        .dataTable thead th,
        .dataTables_length,
        .dataTables_filter,
        .dataTables_paginate,
        .dataTables_info {
            /* font-family: Tahoma !important; */
            font-family: 'Poppins', sans-serif !important;
        }
        .dataTables_info{
            font-weight: 400 !important;
        }

        .swal2-popup pre {
            font-family: inherit;
            font-size: 1em;
            color: #545454;
            white-space: pre-wrap;
            text-align: center;
            margin: 0;
            font-weight: 400;
        }
        .show-password{
            cursor: pointer;
        }

        .dt-buttons{
            display: inline-grid !important;
            grid-auto-flow: column;
            grid-auto-columns: 1fr;
        }

        .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option, .select2-container--bootstrap-5 .select2-dropdown .select2-search .select2-search__field{
            font-size: 1rem !important;
            white-space: nowrap;
            font-family: monospace !important;
        }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered{
            font-size: 1rem !important;
            font-family: monospace !important;
        }
        .table th{
            font-weight: 400 !important;
        }
        .btn-group-sm>.btn, .btn-sm, .modal .btn{
            font-size: 0.75rem !important;
        }
    </style>

    <script src="<?= base_url() ?>assets/admin/libs/jquery/jquery.min.js"></script>
    <script>
        var config_app = {
            decimal: <?= $this->session->setup->CUSTOM2 ?? 2; ?>,
            url: '<?= site_url() ?>'
        }
    </script>
    <script src="<?= base_url() ?>assets/admin/js/input_number.js?v=1.2"></script>
    <script src="<?= base_url() ?>assets/admin/js/custom.js?v=1.12"></script>
</head>

<body data-sidebar="dark" data-update="<?= $access['update'] ?? false ?>">
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
                                <!-- <img src="<?= base_url('assets/logo/logo.png') ?>" alt="logo" height="30"> -->


                                <img src="<?= base_url($logo) ?>" alt="logo" height="30">
                            </span>
                            <span class="logo-lg">
                                <h5 class="text-white"><?= strtoupper($this->session->setup->NAME) ?></h5>
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                        <i class="ri-menu-2-line align-middle"></i>
                    </button>
                </div>

                <div class="d-flex">

                    <?php
                    date_default_timezone_set("Asia/jakarta");
                    $date = date('d M Y');
                    ?>
                    <div class="text-center" style="font-size: 12px; letter-spacing: 5px; margin: 10px 20px 0px 0px;">
                        <div><?= $date ?></div>
                        <div id="jam"></div>
                    </div>

                    <div class="dropdown d-inline-block d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                            <i class="ri-fullscreen-line"></i>
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
                                    <span class="text-muted" style="font-size:x-small"><i class="mdi mdi-circle-medium circle-dot text-success me-1"></i><?= $this->session->userdata('db_alias'); ?></span>
                                </div>
                            </div>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <div class="d-md-none d-sm-block bg-dark border rounded-2 me-2 ms-2 mb-1 px-2 py-1" style="display: grid;">
                                <span class="text-light"><?= $this->session->userdata('nama'); ?></span>
                                <span class="text-muted" style="font-size:x-small"><i class="mdi mdi-circle-medium circle-dot text-success me-1"></i><?= $this->session->userdata('db_alias'); ?></span>
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
                                                    <a href="javascript: void(0);" class="has-arrow waves-effect">
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

    <script>
        //datatables
        $.extend(true, $.fn.dataTable.defaults, {
            "keys": true,
            "showScrollToggle": true,
            "preDrawCallback": function(settings) {
                var api = new $.fn.dataTable.Api(settings);
                var info = api.page.info();
                settings._oldPage = info ? info.page : 0;
            },
            "initComplete": function(settings) {
                setTimeout(function() { syncTableHeader(settings); }, 150);
            },
            "drawCallback": function(settings) {
                var api   = this.api();
                var $body = $(api.table().body());
                var info  = api.page.info();
                var oldPage = settings._oldPage !== undefined ? settings._oldPage : info.page;
                var newPage = info.page;

                if (!$('#dt-anim').length) {
                    $('head').append(`<style id="dt-anim">
                        .dt-next { animation: dtNext 0.25s cubic-bezier(0.25, 0.8, 0.25, 1) both; }
                        .dt-prev { animation: dtPrev 0.25s cubic-bezier(0.25, 0.8, 0.25, 1) both; }
                        .dt-fade { animation: dtFade 0.25s ease both; }
                        @keyframes dtNext { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: none; } }
                        @keyframes dtPrev { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: none; } }
                        @keyframes dtFade { from { opacity: 0; } to { opacity: 1; } }
                    </style>`);
                }

                $body.removeClass('dt-next dt-prev dt-fade');
                void $body[0].offsetWidth;

                if (settings._isFirstDraw !== false) {
                    settings._isFirstDraw = false;
                    $body.addClass('dt-fade');
                } else if (newPage > oldPage) {
                    $body.addClass('dt-next');
                } else if (newPage < oldPage) {
                    $body.addClass('dt-prev');
                } else {
                    $body.addClass('dt-fade');
                }

                setTimeout(function() { syncTableHeader(settings); }, 100);
            }
        });
        $(document).ready(function() {
            $(document).on('init.dt', function(e, settings) {
                if (e.namespace !== 'dt' || settings.oInit.showScrollToggle === false || !settings.nScrollBody) return;

                const api = new $.fn.dataTable.Api(settings);
                const $wrapper = $(api.table().container());
                let isEnabled = settings.oInit.autoScrollPage === true; 
                settings.oInit.autoScrollPage = isEnabled; 

                if (!$('#dt-switch-style').length) {
                    $('head').append(`
                        <style id="dt-switch-style">
                            .dt-switch { position: relative; display: inline-block; width: 34px; height: 20px; margin: 0; vertical-align: middle; }
                            .dt-scroll-checkbox { opacity: 0; width: 0; height: 0; position: absolute; }
                            .dt-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .3s; border-radius: 20px; }
                            .dt-slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
                            .dt-scroll-checkbox:checked + .dt-slider { background-color: #0d6efd; /* Warna Biru Bootstrap */ }
                            .dt-scroll-checkbox:checked + .dt-slider:before { transform: translateX(14px); }
                            .dt-autoscroll-toggle { display: inline-flex; align-items: center; margin-left: 15px; user-select: none; }
                            .dt-autoscroll-label { margin-right: 8px; font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; }
                        </style>
                    `);
                }

                const $toggleContainer = $(`
                    <div class="dt-autoscroll-toggle align-middle">
                        <label class="dt-switch">
                            <input type="checkbox" id="toggle_${settings.sTableId}" class="dt-scroll-checkbox" ${isEnabled ? 'checked' : ''}>
                            <span class="dt-slider"></span>
                        </label>
                        <label class="dt-autoscroll-label mt-2 ms-1" for="toggle_${settings.sTableId}">Scroll Off</label>
                    </div>
                `);

                $toggleContainer.find('.dt-scroll-checkbox').on('change', function() {
                    $(this).closest('.dt-autoscroll-toggle').find('.dt-autoscroll-label').text('Scroll '+(this.checked?'On':'Off'));
                    settings.oInit.autoScrollPage = this.checked;
                });

                $wrapper.find('.dataTables_length, .dt-length').append($toggleContainer);
            });

            $(document).on('wheel', '.dataTables_scrollBody', function(e) {
                const oe = e.originalEvent;
                if (oe.shiftKey || Math.abs(oe.deltaX) > Math.abs(oe.deltaY)) return;

                const $scrollBody = $(this);
                const $table = $scrollBody.find('table');
                const dt = $table.DataTable();

                if (!dt || dt.settings()[0].oInit.autoScrollPage !== true || $table.data('isPageChanging')) return;

                const scrollTop = $scrollBody.scrollTop();
                const isBottom = Math.ceil(scrollTop + $scrollBody.innerHeight()) >= $scrollBody[0].scrollHeight;
                const isTop = scrollTop === 0;
                const info = dt.page.info();
                
                let action = null;

                if (oe.deltaY > 0 && isBottom && info.page < info.pages - 1) action = 'next';
                else if (oe.deltaY < 0 && isTop && info.page > 0) action = 'previous';

                if (action) {
                    $table.data('isPageChanging', true);
                    dt.one('draw.dt', function() {
                        setTimeout(function() {
                            $scrollBody.scrollTop(action === 'previous' ? $scrollBody[0].scrollHeight : 0);
                            setTimeout(() => $table.data('isPageChanging', false), 800);
                        }, action === 'previous' ? 50 : 0); 
                    });
                    dt.page(action).draw('page');
                }
            });
        });
        function syncTableHeader(settings) {
            if (!settings || settings.oInit.showScrollToggle === false || !settings.nScrollBody) return;

            var api = new $.fn.dataTable.Api(settings);
            var $wrapper = $(api.table().container());

            var $scrollHead = $wrapper.find('.dataTables_scrollHead table');
            var $scrollBody = $wrapper.find('.dataTables_scrollBody table');

            var $bodyTds = $scrollBody.find('tbody tr:first td');
            var $headThs = $scrollHead.find('thead tr');
            
            $.each($bodyTds, function(k,v){
                const $td_w = $(this).outerWidth();
                $.each($headThs, function(){
                    $(this).find('th').eq(k).css({
                        'box-sizing': 'border-box',
                        'width': $td_w + 'px',
                        'min-width': $td_w + 'px',
                    });
                });
            });
        }
        $(document).on('keydown', function(e) {
            if (e.which === 32) {
                const focusedCell = $(document).find('.dataTables_wrapper table tbody td.focus');
                if (focusedCell.length > 0) {
                    const chk = focusedCell.find('input[type="checkbox"]');
                    if (chk.length > 0) {
                        e.preventDefault();
                        const currentState = chk.prop('checked');
                        chk.prop('checked', !currentState);
                        chk.trigger('change');
                    }
                }
            }
        });
        
        $(document).ready(function() {
            $('#loading').hide();

            $("a:not(.has-arrow):not(.page-link):not(.nav-link)").click(function() {
                $('#loading').show();
            });

            $("form").on("submit", function() {
                $('#loading').show();
            });

            const flashsuccess = $('#flashSuccess').data('success');
            const flashwarning = $('#flashWarning').data('warning');
            const flasherror = $('#flashError').data('error');
            if (flashsuccess) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    html: `<pre>${flashsuccess}</pre>`,
                })
            }

            if (flashwarning) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    html: `<pre>${flashwarning}</pre>`,
                })
            }

            if (flasherror) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: `<pre>${flasherror}</pre>`,
                })
            }
        });
    </script>

    <script>
        function updateJam() {
            const sekarang = new Date();
            const jam = String(sekarang.getHours()).padStart(2, '0');
            const menit = String(sekarang.getMinutes()).padStart(2, '0');
            const detik = String(sekarang.getSeconds()).padStart(2, '0');

            const waktuLengkap = `${jam}:${menit}:${detik}`;
            document.getElementById('jam').textContent = waktuLengkap;
        }

        // Panggil pertama kali agar tidak delay 1 detik
        updateJam();

        // Update setiap 1 detik
        setInterval(updateJam, 1000);

        function enableDataTableSearch() {

            $('.dataTables_wrapper').each(function() {

                let wrapper = $(this);

                wrapper.find('input[type="search"]')
                    .prop('disabled', false)
                    .removeAttr('disabled');

                wrapper.find('.dataTables_filter')
                    .css('pointer-events', 'auto');
            });
        }

        $(document).ready(function() {
            enableDataTableSearch();
        });

        $(document).ajaxComplete(function() {
            enableDataTableSearch();
        });

        setTimeout(enableDataTableSearch, 300);
        setTimeout(enableDataTableSearch, 800);
    </script>

    <script src="<?= base_url() ?>assets/admin/js/pages/aos.js"></script>

    <script>
        AOS.init();
    </script>

</body>

</html>