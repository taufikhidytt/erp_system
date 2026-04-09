<style>
    .dt-buttons .btn {
        background-color: #0d6efd;
        /* warna biru Bootstrap primary */
        border-color: #0d6efd;
        color: white;
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
        height: 30px !important;
        min-height: 30px !important;
        padding-top: 12px !important;
        padding-bottom: 1px !important;
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
</style>

<div id="flashSuccess" data-success="<?= $this->session->flashdata('success'); ?>"></div>
<div id="flashWarning" data-warning="<?= $this->session->flashdata('warning'); ?>"></div>
<div id="flashError" data-error="<?= $this->session->flashdata('error'); ?>"></div>

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
                                <a class="nav-link active" data-bs-toggle="tab" href="#stok" role="tab" aria-selected="true">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Stok"><i class="ri ri-stock-fill"></i></span>
                                    <span class="d-none d-sm-block">Stok</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#summary-stok" role="tab" aria-selected="false">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Summary Stok"><i class="ri ri-file-paper-2-fill"></i></span>
                                    <span class="d-none d-sm-block">Summary Stok</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#kartu-stok" role="tab" aria-selected="false">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Kartu Stok"><i class="ri ri-file-list-fill"></i></span>
                                    <span class="d-none d-sm-block">Kartu Stok</span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="stok" role="tabpanel">
                                <div class="row g-3 align-items-center">
                                    <label for="gudang" class="col-lg-1 col-md-2 col-sm-12 col-form-label">Gudang</label>
                                    <div class="col-lg-1 col-md-2 col-sm-2">
                                        <input type="checkbox" name="check_gudang" id="check_gudang">
                                        <label for="check_gudang">All</label>
                                    </div>
                                    <div class="col-lg-8 col-md-4 col-sm-12 mb-3 mb-sm-3">
                                        <?php
                                        $defaultValue = null;
                                        foreach ($gudang->result() as $gd) {
                                            if ($gd->PRIMARY_FLAG == 'Y') {
                                                $defaultValue = $gd->WAREHOUSE_ID;
                                                break;
                                            }
                                        }
                                        ?>
                                        <select name="gudang" id="gudang" class="form-control select2 <?= form_error('gudang') ? 'is-invalid' : null; ?>">
                                            <?php if (!$defaultValue): ?>
                                                <option value="">-- Selected Gudang --</option>
                                            <?php endif; ?>
                                            <?php foreach ($gudang->result() as $gd): ?>
                                                <option
                                                    value="<?= $gd->WAREHOUSE_ID ?>"
                                                    <?= set_value('gudang') ==  $gd->WAREHOUSE_ID ? 'selected' : ($defaultValue == $gd->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                    <?= strtoupper($gd->WAREHOUSE_NAME) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-12">
                                        <div class="d-flex mb-2">
                                            <button id="export_excel_stok" class="btn btn-primary btn-sm">Export Excel Stok</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center w-100 text-nowrap" id="table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Nama"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kode"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Stok"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Satuan"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Price"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Nilai"></th>
                                                </tr>
                                                <tr class="align-content-center">
                                                    <th>No</th>
                                                    <th>Nama Item</th>
                                                    <th>Kode Item</th>
                                                    <th>Stok</th>
                                                    <th>Satuan</th>
                                                    <th>Price</th>
                                                    <th>Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" style="text-align:right">Total Stok :</th>
                                                    <th id="total_stok"></th>
                                                    <th colspan="3"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="summary-stok" role="tabpanel">
                                <div class="d-flex justify-content-end mb-2">
                                    <button id="export_excel_summary_stok" class="btn btn-primary btn-sm">Export Summary Stok</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped text-center w-100 text-nowrap" id="table-summary">
                                        <!-- <thead>
                                            <tr>
                                                <th></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Nama"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kode"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Satuan"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Pusat"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Pinjam"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Uncomplete"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Booked"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons JIA"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons BBI"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons BKI"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons HSS"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons THC"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons GD Hydraulic"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons TFP"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Teknikal"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons GUN"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons HS (CIMONE)"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons NHC"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Kons SAMPIT"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Neglasari"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Total Stok"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Price"></th>
                                                <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Nilai"></th>
                                            </tr>
                                            <tr class="align-content-center text-nowrap">
                                                <th>No</th>
                                                <th>Nama Item</th>
                                                <th>Kode Item</th>
                                                <th>Satuan</th>
                                                <th>Pusat</th>
                                                <th>Pinjam</th>
                                                <th>Uncomplete</th>
                                                <th>Booked</th>
                                                <th>Kons JIA</th>
                                                <th>Kons BBI</th>
                                                <th>Kons BKI</th>
                                                <th>Kons HSS</th>
                                                <th>Kons THC</th>
                                                <th>Kons GD HYDRAULIC</th>
                                                <th>Kons TFP</th>
                                                <th>Teknikal</th>
                                                <th>Kons GUN</th>
                                                <th>Kons HS (CIMONE)</th>
                                                <th>Kons NHC</th>
                                                <th>Kons SAMPIT</th>
                                                <th>Neglasari</th>
                                                <th>Total Stok</th>
                                                <th>Price</th>
                                                <th>Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" style="text-align:right">Total :</th>
                                                <th id="total_pusat"></th>
                                                <th id="total_pinjam"></th>
                                                <th id="total_uncomplete"></th>
                                                <th id="total_booked"></th>
                                                <th id="total_kons_jia"></th>
                                                <th id="total_kons_bbi"></th>
                                                <th id="total_kons_bki"></th>
                                                <th id="total_kons_hss"></th>
                                                <th id="total_kons_thc"></th>
                                                <th id="total_kons_gd_hydraulic"></th>
                                                <th id="total_kons_tfp"></th>
                                                <th id="total_teknikal"></th>
                                                <th id="total_kons_gun"></th>
                                                <th id="total_kons_hs"></th>
                                                <th id="total_kons_nhc"></th>
                                                <th id="total_kons_sampit"></th>
                                                <th id="total_neglasari"></th>
                                                <th id="total_total_stok"></th>
                                            </tr>
                                        </tfoot> -->

                                        <thead>
                                            <tr id="search-row">
                                                <!-- Column search akan di-generate JS -->
                                            </tr>
                                            <tr id="header-row">
                                                <!-- Header akan di-generate JS -->
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr id="footer-row">
                                                <!-- Footer summary akan di-generate JS -->
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="kartu-stok" role="tabpanel">
                                <div class="row">
                                    <label for="item" class="col-lg-1 col-md-4 col-sm-12 col-form-label">Item</label>
                                    <div class="col-lg-6 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                        <select name="item" id="item" class="form-control select2 <?= form_error('item') ? 'is-invalid' : null; ?>">
                                            <option value="">-- Selected Item --</option>
                                            <?php foreach ($item->result() as $im): ?>
                                                <option
                                                    value="<?= $im->ITEM_ID ?>"
                                                    <?= set_value('gudang') ==  $im->ITEM_ID ? 'selected' : '' ?>>
                                                    <?= strtoupper($im->ITEM_DESCRIPTION . " ~ " . $im->ITEM_CODE) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <label for="period" class="col-lg-1 col-md-4 col-sm-12 col-form-label">s.d Period</label>
                                    <div class="col-lg-4 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                        <select name="period" id="period" class="form-control select2 <?= form_error('period') ? 'is-invalid' : null; ?>">
                                            <option value="">-- Selected Period --</option>
                                            <?php foreach ($period->result() as $pri): ?>
                                                <option
                                                    value="<?= $pri->PERIOD_NAME ?>"
                                                    <?= set_value('period') ==  $pri->PERIOD_NAME ? 'selected' : '' ?>>
                                                    <?= strtoupper($pri->PERIODE) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-3 align-items-center">
                                    <label for="gudang_kartu_stok" class="col-lg-1 col-md-4 col-sm-12 col-form-label">Gudang</label>
                                    <div class="col-lg-1 col-md-1 col-sm-2">
                                        <input type="checkbox" name="check_gudang_kartu_stok" id="check_gudang_kartu_stok">
                                        <label for="check_gudang_kartu_stok">All</label>
                                    </div>
                                    <div class="col-lg-6 col-md-4 col-sm-6 mb-3 mb-sm-3">
                                        <?php
                                        $defaultValue = null;
                                        foreach ($gudang->result() as $gd) {
                                            if ($gd->PRIMARY_FLAG == 'Y') {
                                                $defaultValue = $gd->WAREHOUSE_ID;
                                                break;
                                            }
                                        }
                                        ?>
                                        <select name="gudang_kartu_stok" id="gudang_kartu_stok" class="form-control select2 <?= form_error('gudang_kartu_stok') ? 'is-invalid' : null; ?>">
                                            <?php if (!$defaultValue): ?>
                                                <option value="">-- Selected Main Storage --</option>
                                            <?php endif; ?>
                                            <?php foreach ($gudang->result() as $gd): ?>
                                                <option
                                                    value="<?= $gd->WAREHOUSE_ID ?>"
                                                    <?= set_value('gudang_kartu_stok') ==  $gd->WAREHOUSE_ID ? 'selected' : ($defaultValue == $gd->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                    <?= strtoupper($gd->WAREHOUSE_NAME) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 col-md-3 col-sm-4">
                                        <div class="d-flex justify-content-end mb-2">
                                            <button id="export_excel_kartu_stok" class="btn btn-primary btn-sm" style="display:none;">Export Kartu Stok</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center w-100 text-nowrap" id="table-kartu-stok">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><input type="date" class="column_search form-control form-control-sm" placeholder="Cari Tanggal"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari No Transaksi"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari No Reff"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Supplier"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Transaksi"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Masuk"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Keluar"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Stok"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Gudang"></th>
                                                    <th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari Note"></th>
                                                </tr>
                                                <tr class="align-content-center">
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>No Transaksi</th>
                                                    <th>No Reff</th>
                                                    <th>Supplier</th>
                                                    <th>Transaksi</th>
                                                    <th>Masuk</th>
                                                    <th>Keluar</th>
                                                    <th>Saldo</th>
                                                    <th>Gudang</th>
                                                    <th>Note</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label>Masuk</label>
                                        <input type="text" id="total_masuk" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Keluar</label>
                                        <input type="text" id="total_keluar" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Saldo Akhir</label>
                                        <input type="text" id="stok_akhir" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<script>
    $(document).ready(function() {
        // Tab stok
        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [],
            ajax: {
                url: "<?= site_url('stk_kny/get_data'); ?>",
                type: "POST",
                data: function(d) {
                    d.gudang = $('#gudang').val();
                    d.check_gudang = $('#check_gudang').is(':checked');
                }
            },
            drawCallback: function(settings) {
                var json = settings.json;
                if (json && json.summary) {
                    $('#total_stok').text(json.summary.total_stok);
                }
            },
            columnDefs: [{
                className: 'text-end',
                targets: [3, 5, 6],
            }]
        });

        $('#table thead').on('keyup change', '.column_search', function() {
            let index = $(this).parent().index();
            table.column(index).search(this.value).draw();
        });

        $('#gudang, #check_gudang').change(function() {
            table.ajax.reload();
        });

        $("#check_gudang").change(function() {
            $("#gudang").prop("disabled", this.checked);
        });

        $('#export_excel_stok').on('click', function() {
            var params = $.param({
                gudang: $('#gudang').val(),
                check_gudang: $('#check_gudang').is(':checked'),
            });
            window.location.href = "<?= site_url('stk_kny/export_excel_stok') ?>?" + params;
        });

        // ===============================================================

        // Tab summary stok
        var tableSummary;

        $.ajax({
            url: "<?= site_url('stk_kny/get_columns'); ?>",
            method: "GET",
            dataType: "json",
            success: function(columns) {

                function formatColumn(col) {
                    return col.replace(/_/g, ' ');
                }

                function formatKey(col) {
                    return col.toLowerCase().replace(/[^a-z0-9]/g, '_');
                }

                // HEADER
                $('#header-row').append('<th>No</th>');
                columns.forEach(col => {
                    $('#header-row').append('<th>' + formatColumn(col) + '</th>');
                });

                // SEARCH ROW
                $('#search-row').append('<th></th>');
                columns.forEach(col => {
                    $('#search-row').append(
                        '<th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari ' + formatColumn(col) + '"></th>'
                    );
                });

                // FOOTER
                $('#footer-row').append('<th style="text-align:right">Total :</th>');
                columns.forEach(col => {
                    $('#footer-row').append('<th id="total_' + formatKey(col) + '"></th>');
                });

                // COLUMNS
                let dtColumns = [{
                    data: 'NO',
                    className: 'text-start'
                }];

                const leftAlignColumns = ['NO', 'NAMA_ITEM', 'KODE_ITEM', 'SATUAN'];

                columns.forEach(col => {
                    dtColumns.push({
                        data: col,
                        className: leftAlignColumns.includes(col) ? 'text-start' : 'text-end',
                        render: function(data, type) {

                            // format hanya untuk numeric
                            if (!leftAlignColumns.includes(col) && type === 'display' && !isNaN(data) && data !== null) {
                                return parseFloat(data).toLocaleString('en-US', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }

                            return data;
                        }
                    });
                });

                // INIT DATATABLE
                tableSummary = $('#table-summary').DataTable({
                    processing: true,
                    serverSide: true,
                    autoWidth: false,
                    order: [
                        [1, 'asc']
                    ],
                    ajax: {
                        url: "<?= site_url('stk_kny/get_data_summary'); ?>",
                        type: "POST",
                    },
                    columns: dtColumns,
                    drawCallback: function(settings) {
                        var json = settings.json;
                        if (json && json.summary) {
                            for (let key in json.summary) {
                                $('#total_' + key).text(json.summary[key]);
                            }
                        }
                    }
                });

                // SEARCH PER COLUMN
                $('#table-summary thead').on('keyup change', '.column_search', function() {
                    let index = $(this).parent().index();
                    tableSummary.column(index).search(this.value).draw();
                });
            }
        });

        // var tableSummary = $('#table-summary').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     autoWidth: false,
        //     order: [],
        //     ajax: {
        //         url: "<?= site_url('stk_kny/get_data_summary'); ?>",
        //         type: "POST",
        //     },
        //     drawCallback: function(settings) {
        //         var json = settings.json;
        //         if (json && json.summary) {
        //             // Dynamic update footer
        //             for (const key in json.summary) {
        //                 $('#' + key).text(json.summary[key]);
        //             }
        //         }
        //     },
        //     columnDefs: [{
        //         className: 'text-end',
        //         targets: "_all" // semua kolom numeric akan rata kanan, bisa sesuaikan jika perlu
        //     }]
        // });

        // --------------------------
        // Column search
        // --------------------------

        // $('#table-summary thead').on('keyup change', '.column_search', function() {
        //     let index = $(this).parent().index();
        //     tableSummary.column(index).search(this.value).draw();
        // });

        $('#export_excel_summary_stok').on('click', function() {
            window.location.href = "<?= site_url('stk_kny/export_excel_summary_stok') ?>";
        });

        // ================================================================

        // Tab kartu stok
        var tableKartuStok = null;
        $('#item, #period').on('change', function() {
            var item_id = $('#item').val();
            var period = $('#period').val();
            if (item_id && period) {
                $('#export_excel_kartu_stok').show();
                if (!$.fn.DataTable.isDataTable('#table-kartu-stok')) {
                    tableKartuStok = $('#table-kartu-stok').DataTable({
                        processing: true,
                        serverSide: true,
                        autoWidth: false,
                        order: [],
                        ajax: {
                            url: "<?= site_url('stk_kny/get_data_kartu_stok'); ?>",
                            type: "POST",
                            data: function(d) {
                                d.item = item_id;
                                d.period = period;
                                d.gudang_kartu_stok = $('#gudang_kartu_stok').val();
                                d.check_gudang_kartu_stok = $('#check_gudang_kartu_stok').is(':checked');
                            }
                        },
                        drawCallback: function(settings) {

                            var json = settings.json;

                            if (json && json.summary) {
                                $('#total_masuk').val(json.summary.masuk);
                                $('#total_keluar').val(json.summary.keluar);
                                $('#stok_akhir').val(json.summary.stok_akhir);
                            }
                        },
                        columnDefs: [{
                            className: 'text-end',
                            targets: [6, 7, 8],
                        }]
                    });
                    $('#table-kartu-stok thead').on('keyup change', '.column_search', function() {
                        let index = $(this).parent().index();
                        tableKartuStok.column(index).search(this.value).draw();
                    });
                } else {
                    $('#table-kartu-stok').DataTable().ajax.reload();
                }
            } else {
                $('#export_excel_kartu_stok').hide();
                if ($.fn.DataTable.isDataTable('#table-kartu-stok')) {
                    $('#table-kartu-stok').DataTable().clear().destroy();
                }
            }
        });

        $('#item, #period, #gudang_kartu_stok, #check_gudang_kartu_stok').change(function() {
            if ($.fn.DataTable.isDataTable('#table-kartu-stok')) {
                $('#table-kartu-stok').DataTable().ajax.reload();
            } else {
                if ($.fn.DataTable.isDataTable('#table-kartu-stok')) {
                    $('#table-kartu-stok').DataTable().clear().destroy();
                    tableKartuStok = null;
                }
            }
        });

        $("#check_gudang_kartu_stok").change(function() {
            $("#gudang_kartu_stok").prop("disabled", this.checked);
        });

        $('#export_excel_kartu_stok').on('click', function() {
            var params = $.param({
                item: $('#item').val(),
                period: $('#period').val(),
                gudang_kartu_stok: $('#gudang_kartu_stok').val(),
                check_gudang_kartu_stok: $('#check_gudang_kartu_stok').is(':checked'),
            });
            window.location.href = "<?= site_url('stk_kny/export_excel_kartu_stok') ?>?" + params;
        });

        //Initialize Select2 Elements
        $('.select2').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this).parent(),
            });
        });

        var flashsuccess = $('#flashSuccess').data('success');
        var flashwarning = $('#flashWarning').data('warning');
        var flasherror = $('#flashError').data('error');

        if (flashsuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: flashsuccess,
            })
        }

        if (flashwarning) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: flashwarning,
            })
        }

        if (flasherror) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: flasherror,
            })
        }
    });
</script>