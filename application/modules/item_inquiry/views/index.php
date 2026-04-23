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
                                <div class="row g-3 align-items-center justify-content-center mb-3">
                                    <div class="col-lg-auto col-md-auto col-sm-12 d-flex align-items-center justify-content-center">
                                        <label for="gudang" class="form-label mb-0 me-2 fw-bold">Gudang</label>
                                        <div class="form-check mb-0 d-flex align-items-center">
                                            <input type="checkbox" class="form-check-input mt-0 me-1" name="check_gudang" id="check_gudang">
                                            <label class="form-check-label" for="check_gudang">All</label>
                                        </div>
                                    </div>

                                    <div class="col-lg-6 col-md-5 col-sm-12">
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

                                    <div class="col-lg-auto col-md-auto col-sm-12 text-center">
                                        <button id="export_excel_stok" class="btn btn-primary btn-sm px-3">
                                            <i class="fas fa-file-excel me-1"></i> Export Stok
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center w-100 text-nowrap table-sm" id="table">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                </tr>
                                                <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
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
                                    <button id="export_excel_summary_stok" class="btn btn-primary btn-sm"><i class="fa fa-file-excel me-1"></i>Export Summary Stok</button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped text-center w-100 text-nowrap table-sm" id="table-summary">
                                        <thead>
                                            <tr id="search-row">
                                                <!-- Column search akan di-generate JS -->
                                            </tr>
                                            <tr id="header-row" style="background: #3d7bb9; z-index: 10; color: #ffff">
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
                                <div class="row g-3 align-items-center justify-content-center mb-3">
                                    <div class="col-lg-auto col-md-auto col-sm-12">
                                        <label for="item" class="form-label mb-0 fw-bold">Item</label>
                                    </div>
                                    <div class="col-lg-9 col-md-9 col-sm-12">
                                        <select name="item" id="item" class="form-control select2 <?= form_error('item') ? 'is-invalid' : null; ?>">
                                            <option value="">-- Selected Item --</option>
                                            <?php foreach ($item->result() as $im): ?>
                                                <option value="<?= $im->ITEM_ID ?>" <?= set_value('item') == $im->ITEM_ID ? 'selected' : '' ?>>
                                                    <?= strtoupper($im->ITEM_DESCRIPTION . " ~ " . $im->ITEM_CODE) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row g-3 align-items-center justify-content-center mb-3">
                                    <div class="col-lg-auto col-md-auto col-sm-12">
                                        <label for="period" class="form-label mb-0 fw-bold text-nowrap">s.d Period</label>
                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-12">
                                        <select name="period" id="period" class="form-control select2 <?= form_error('period') ? 'is-invalid' : null; ?>">
                                            <option value="">-- Selected Period --</option>
                                            <?php foreach ($period->result() as $pri): ?>
                                                <option value="<?= $pri->PERIOD_NAME ?>" <?= set_value('period') == $pri->PERIOD_NAME ? 'selected' : '' ?>>
                                                    <?= strtoupper($pri->PERIODE) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-auto col-md-auto col-sm-12 d-flex align-items-center">
                                        <label for="gudang_kartu_stok" class="form-label mb-0 me-2 fw-bold">Gudang</label>
                                        <div class="form-check form-check mb-0 d-flex align-items-center me-2">
                                            <input class="form-check-input mt-0 me-1" type="checkbox" name="check_gudang_kartu_stok" id="check_gudang_kartu_stok">
                                            <label class="form-check-label" for="check_gudang_kartu_stok">All</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
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
                                                <option value="<?= $gd->WAREHOUSE_ID ?>" <?= set_value('gudang_kartu_stok') == $gd->WAREHOUSE_ID ? 'selected' : ($defaultValue == $gd->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                    <?= strtoupper($gd->WAREHOUSE_NAME) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-auto col-md-auto col-sm-12 text-center">
                                        <button id="export_excel_kartu_stok" class="btn btn-primary btn-sm px-3" style="display:none;">
                                            <i class="fas fa-file-excel me-1"></i> Export Kartu Stok
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center w-100 text-nowrap table-sm" id="table-kartu-stok">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th><input type="date" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                </tr>
                                                <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
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
    let tableKartuStok = null;
    $(document).ready(function() {
        // Tab stok
        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [],
            searchDelay: 1500,
            ajax: {
                url: "<?= site_url('item_inquiry/get_data'); ?>",
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
                },
                {
                    className: 'text-center',
                    targets: [0, 2],
                },
                {
                    orderable: false,
                    targets: [0],
                },
            ]
        });

        $('#table thead').on('keyup change', '.column_search', debounce(function() {
            let index = $(this).parent().index();
            table.column(index).search(this.value).draw();
        }, 1500));

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
            window.location.href = "<?= site_url('item_inquiry/export_excel_stok') ?>?" + params;
        });

        // ===============================================================

        // Tab summary stok
        var tableSummary;

        $.ajax({
            url: "<?= site_url('item_inquiry/get_columns'); ?>",
            method: "GET",
            dataType: "json",
            success: function(columns) {
                let param = columns[0];

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
                        // '<th><input type="text" class="column_search form-control form-control-sm" placeholder="Cari ' + formatColumn(col) + '"></th>'
                        '<th><input type="text" class="column_search" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;" placeholder="Cari.."></th>'
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
                    className: 'text-center',
                    orderable: false
                }];

                const leftAlignColumns = [columns[0], columns[2]];
                const centerAlignColumns = ['NO', columns[1]];

                columns.forEach(col => {
                    dtColumns.push({
                        data: col,
                        className: leftAlignColumns.includes(col) ? 'text-start' : (centerAlignColumns.includes(col) ? 'text-center' : 'text-end'),
                        render: function(data, type) {

                            // format hanya untuk numeric
                            if (!leftAlignColumns.includes(col) && !centerAlignColumns.includes(col) && type === 'display' && !isNaN(data) && data !== null) {
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
                    order: [],
                    searchDelay: 1500,
                    ajax: {
                        url: "<?= site_url('item_inquiry/get_data_summary'); ?>",
                        type: "POST",
                        data: {
                            param: param,
                        },
                    },
                    columns: dtColumns,
                    drawCallback: function(settings) {
                        var json = settings.json;
                        if (json && json.summary) {
                            for (let key in json.summary) {
                                let colIndex = dtColumns.findIndex(col => formatKey(col.data) === key);

                                if ([0, 1, 2, 3].includes(colIndex)) continue; // ⛔ skip kolom 0,1,2

                                $('#total_' + key).text(json.summary[key]);
                            }
                        }
                    }
                });

                // SEARCH PER COLUMN
                $('#table-summary thead').on('keyup change', '.column_search', debounce(function() {
                    let index = $(this).parent().index();
                    tableSummary.column(index).search(this.value).draw();
                }, 1500));
            }
        });

        $('#export_excel_summary_stok').on('click', function() {
            window.location.href = "<?= site_url('item_inquiry/export_excel_summary_stok') ?>";
        });

        // ================================================================

        // Tab kartu stok
        $('#item, #period, #gudang_kartu_stok, #check_gudang_kartu_stok').on('change', function() {

            if (!isFilterValid()) {
                $('#export_excel_kartu_stok').hide();

                if ($.fn.DataTable.isDataTable('#table-kartu-stok')) {
                    tableKartuStok.clear().destroy();
                    tableKartuStok = null;
                }

                resetSummary();
                return;
            }

            $('#export_excel_kartu_stok').show();

            if (!tableKartuStok) {
                initTableKartuStok();
            } else {
                resetSummary();

                tableKartuStok
                    .search('')
                    .columns().search('');

                tableKartuStok.ajax.reload(null, true); // reset paging
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
            window.location.href = "<?= site_url('item_inquiry/export_excel_kartu_stok') ?>?" + params;
        });

        //Initialize Select2 Elements
        $('.select2').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this).parent(),
                width: '100%'
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

    function getFilter() {
        return {
            item: $('#item').val(),
            period: $('#period').val(),
            gudang_kartu_stok: $('#gudang_kartu_stok').val(),
            check_gudang_kartu_stok: $('#check_gudang_kartu_stok').is(':checked')
        };
    }

    function initTableKartuStok() {
        if (tableKartuStok) {
            tableKartuStok.destroy();
            tableKartuStok = null;
        }

        tableKartuStok = $('#table-kartu-stok').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [],
            searchDelay: 1500,
            ajax: {
                url: "<?= site_url('item_inquiry/get_data_kartu_stok'); ?>",
                type: "POST",
                data: function(d) {
                    return $.extend({}, d, getFilter());
                }
            },

            drawCallback: function(settings) {
                let json = settings.json;

                if (json && json.summary) {
                    $('#total_masuk').val(json.summary.masuk || 0);
                    $('#total_keluar').val(json.summary.keluar || 0);
                    $('#stok_akhir').val(json.summary.stok_akhir || 0);
                } else {
                    resetSummary();
                }
            },

            columnDefs: [{
                    className: 'text-end',
                    targets: [6, 7, 8]
                },
                {
                    className: 'text-center',
                    targets: [0, 1]
                },
                {
                    targets: [0],
                    orderable: false
                }
            ]
        });
    }

    function resetSummary() {
        $('#total_masuk').val(0);
        $('#total_keluar').val(0);
        $('#stok_akhir').val(0);
    }

    function isFilterValid() {
        let f = getFilter();
        return f.item && f.period;
    }

    function debounce(func, delay) {
        let timer;
        return function() {
            let context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(context, args), delay);
        };
    }
</script>