<!-- DateRangePicker CSS -->
<link rel="stylesheet" href="<?= base_url() ?>assets/admin/libs/daterangepicker/css/daterangepicker.css">

<style>
    .dt-buttons .btn {
        background-color: #0d6efd;
        /* warna biru Bootstrap primary */
        border-color: #0d6efd;
        color: white;
    }

    .dt-buttons .btn-sm,
    .dataTables_filter input {
        height: 26px;
    }

    .table-striped>tbody>tr:nth-of-type(odd) {
        --bs-table-accent-bg: #eff2f7;
    }

    #table_filter {
        display: none;
        position: absolute;
    }

    /* Jangan wrap teks agar width stabil */
    #table th {
        white-space: nowrap;
    }

    #table td {
        white-space: nowrap;
        padding-right: 6px !important;
        padding-left: 6px !important;
        font-size: 0.75rem !important;
    }

    /* Agar filter row tetap rapi */
    .column_search {
        width: 100%;
        box-sizing: border-box;
    }

    .font-mono {
        font-family: monospace !important;
    }

    .tab-pane .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        font-size: 1rem !important;
    }
</style>

<!-- Moment.js -->
<script src="<?= base_url() ?>assets/admin/libs/moment/moment.min.js"></script>

<!-- DateRangePicker -->
<script src="<?= base_url() ?>assets/admin/libs/daterangepicker/js/daterangepicker.min.js"></script>

<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="" class="text-decoration-underline">
                                    <?= $breadcrumb ?>
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card border-2">
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#fpk" role="tab" aria-selected="true">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="FPK"><i class="ri ri-stock-fill"></i></span>
                                    <span class="d-none d-sm-block">FPK</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#grk" role="tab" aria-selected="false">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="GRK"><i class="ri ri-stock-fill"></i></span>
                                    <span class="d-none d-sm-block">GRK</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#sent_to_site" role="tab" aria-selected="false">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Sent to Site"><i class="ri ri-stock-fill"></i></span>
                                    <span class="d-none d-sm-block">Sent to Site</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#receive_in_site" role="tab" aria-selected="false">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Receive in Site"><i class="ri ri-stock-fill"></i></span>
                                    <span class="d-none d-sm-block">Receive in Site</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="fpk" role="tabpanel">
                                <?php $this->load->view('tab_fpk'); ?>
                            </div>

                            <div class="tab-pane fade" id="grk">
                                <?php $this->load->view('tab_grk'); ?>
                            </div>

                            <div class="tab-pane fade" id="sent_to_site">
                                <?php $this->load->view('tab_sent_to_site'); ?>
                            </div>

                            <div class="tab-pane fade" id="receive_in_site">
                                <?php $this->load->view('tab_receive_in_site'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Page-content -->