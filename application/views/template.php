<!DOCTYPE html>

<head>
    <meta charset="utf-8" />
    <title><?= $title; ?> | <?= $this->session->userdata('name_ub'); ?></title>
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

    <style>
        body {
            /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important; */
            font-family: Tahoma !important;
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
            font-size: 0.75rem !important;
        }

        /* Input-group icon */
        .form-xs .input-group-text {
            height: 30px !important;
            padding: 2px 6px !important;
            font-size: 0.75rem !important;
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
            /* allowed */
        }
    </style>

    <script src="<?= base_url() ?>assets/admin/libs/jquery/jquery.min.js"></script>
</head>

<body data-sidebar="dark">

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
                        <a href="<?= base_url('dashboard') ?>">
                            <h5 class="text-white"><?= strtoupper($this->session->userdata('name_ub')) ?></h5>
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
                            <span class="d-none d-xl-inline-block ms-1"><?= $this->session->userdata('nama'); ?></span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="<?= base_url('profile') ?>">
                                <i class="ri-user-line align-middle me-1"></i> Profile
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
                                                    DASHBOARD
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
                                            <?php if ($this->session->userdata('id') == 5): ?>
                                                <li class="menu-title"><?= $this->lang->line('developmentSettings'); ?></li>
                                                <li>
                                                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                                                        <i class="ri-code-box-line"></i>
                                                        <span><?= $this->lang->line('developmentCode'); ?></span>
                                                    </a>
                                                    <ul class="sub-menu" aria-expanded="false">
                                                        <li>
                                                            <a href="<?= base_url('managementMenu') ?>" class="waves-effect">
                                                                <span><?= $this->lang->line('managementMenu'); ?></span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            <?php endif; ?>
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
                            &copy; <?= date('Y') ?> &middot; PT INTINUSA SEJAHTERA INTERNATIONAL.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block fw-bold">
                                <?= $this->lang->line('createdBy'); ?> <a href="https://taufikhidytt.github.io/" target="_blank" class="bold">Taufik Hidayat.</a>
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
        $(document).ready(function() {
            $('#loading').hide();

            $("a:not(.has-arrow):not(.page-link):not(.nav-link)").click(function() {
                $('#loading').show();
            });
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
    </script>

</body>

</html>