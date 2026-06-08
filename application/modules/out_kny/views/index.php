<!-- DateRangePicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

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

    .tab-pane .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        font-size: 1rem !important;
    }
</style>

<!-- Moment.js -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>

<!-- DateRangePicker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

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
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="fpk" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col text-end">
                                        <button id="export_fpk" class="btn btn-primary">
                                            <i class="fas fa-file-excel me-1"></i> Export
                                        </button>
                                    </div>
                                </div>

                                <!-- Filter Supplier -->
                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-2">
                                        <label class="fw-bold mb-0">Supplier</label>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="check_supplier" checked>
                                            <label class="form-check-label" for="check_supplier">
                                                All
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <select name="supplier" id="supplier"
                                            data-url="out_kny/get_supplier"
                                            data-selected-id="<?= set_value('supplier', '') ?>"
                                            class="form-control select2 <?= form_error('supplier') ? 'is-invalid' : null; ?>">
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-2">
                                        <label class="fw-bold mb-0">Status</label>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input type="checkbox" name="status_all" value="ALL" class="form-check-input" id="statusAll">
                                            <label class="form-check-label">
                                                All
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="m-3">
                                            <input type="radio" name="status" value="COMPLETE">
                                            COMPLETE
                                        </label>

                                        <label class="m-3">
                                            <input type="radio" name="status" value="NEW" checked>
                                            NEW
                                        </label>

                                        <label class="m-3">
                                            <input type="radio" name="status" value="PARTIAL">
                                            PARTIAL
                                        </label>

                                        <label class="m-3">
                                            <input type="radio" name="status" value="CLOSE">
                                            CLOSE/DELETE
                                        </label>

                                        <label class="m-3">
                                            <input type="radio" name="status" value="OUTSTANDING">
                                            OUTSTANDING
                                        </label>
                                    </div>
                                </div>

                                <div class="row mb-2 align-items-center">
                                    <div class="col-md-2">
                                        <label class="fw-bold mb-0">Periode</label>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="check_period" checked>
                                            <label class="form-check-label" for="check_period" checked>
                                                All
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <input type="text" id="daterange" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center w-100 text-nowrap table-sm" id="table-fpk">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="date" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="date" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                                                </tr>
                                                <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                                    <th></th>
                                                    <th>No</th>
                                                    <th>Status</th>
                                                    <th>No Transaksi</th>
                                                    <th>No Referensi</th>
                                                    <th>Tanggal</th>
                                                    <th>Dibutuhkan</th>
                                                    <th>Supplier</th>
                                                    <th>Storage</th>
                                                    <th>Sales</th>
                                                    <th>Total</th>
                                                    <th>Periode</th>
                                                    <th>Note</th>
                                                    <th>Created By</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
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
        toggleSupplier();
        togglePeriod();

        $('#supplier, #check_supplier, #statusAll, input[name="status"], #check_period, #daterange').change(function() {
            fpk.ajax.reload();
        });

        $("#check_supplier").on("change", function() {
            toggleSupplier();
        });

        $('#statusAll').on('change', function() {

            if ($(this).is(':checked')) {

                $('input[name="status"]')
                    .prop('checked', false)
                    .prop('disabled', true);

            } else {

                $('input[name="status"]')
                    .prop('disabled', false);

                $('input[name="status"][value="NEW"]')
                    .prop('checked', true);
            }

            fpk.ajax.reload();
        });

        $(function() {
            function updateDisplay(start, end) {
                $('#startDate').text(start.format('YYYY-MM-DD'));
                $('#endDate').text(end.format('YYYY-MM-DD'));
            }

            let start = moment().startOf('month');
            let end = moment().endOf('month');

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
                autoUpdateInput: false,
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    customRangeLabel: 'Custom Range'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [
                        moment().subtract(1, 'days'),
                        moment().subtract(1, 'days')
                    ],
                    'Last 7 Days': [
                        moment().subtract(6, 'days'),
                        moment()
                    ],
                    'Last 30 Days': [
                        moment().subtract(29, 'days'),
                        moment()
                    ],
                    'This Month': [
                        moment().startOf('month'),
                        moment().endOf('month')
                    ],
                    'Last Month': [
                        moment().subtract(1, 'month').startOf('month'),
                        moment().subtract(1, 'month').endOf('month')
                    ]
                }
            });

            // Default saat load = Today
            updateDisplay(start, end);

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {

                $(this).val(
                    picker.startDate.format('YYYY-MM-DD') +
                    ' - ' +
                    picker.endDate.format('YYYY-MM-DD')
                );

                fpk.ajax.reload();
            });
        });

        $('#daterange').prop('disabled', true);

        $('#daterange').on('keydown keypress paste input', function(e) {
            e.preventDefault();
        });

        $('#check_period').on('change', function() {

            togglePeriod();

            fpk.ajax.reload();
        });

        // Tab fpk
        var fpk = $('#table-fpk').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [],
            ajax: {
                url: "<?= site_url('out_kny/get_data_fpk'); ?>",
                type: "POST",
                data: function(d) {
                    d.supplier = $('#supplier').val();
                    d.check_supplier = $('#check_supplier').is(':checked');
                    d.status = $('input[name="status"]:checked').val();
                    d.check_status = $('#statusAll').is(':checked');
                    d.check_period = $('#check_period').is(':checked');
                    d.daterange = $('#daterange').val();
                }
            },
            columns: [{
                    className: 'details-control',
                    orderable: false,
                    data: null,
                    defaultContent: '<i class="ri ri-add-line" style="cursor:pointer"></i>'
                },
                {
                    data: "0"
                },
                {
                    data: "1"
                },
                {
                    data: "2"
                },
                {
                    data: "3"
                },
                {
                    data: "4"
                },
                {
                    data: "5"
                },
                {
                    data: "6"
                },
                {
                    data: "7"
                },
                {
                    data: "8"
                },
                {
                    data: "9"
                },
                {
                    data: "10"
                },
                {
                    data: "11"
                },
                {
                    data: "12"
                },
                // lanjutkan sesuai kolom kamu
            ],
            columnDefs: [{
                    className: 'text-end',
                    targets: [9],
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

        $('#table-fpk tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = fpk.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var pr_id = rowData[13];


            if (row.child.isShown()) {
                // Close row
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                // Open row dengan child row datatable
                var childTableId = 'child-' + pr_id;

                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                <tr class="align-middle">
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kode Item</th>
                                    <th>Jumlah</th>
                                    <th>Terima</th>
                                    <th>Sisa</th>
                                    <th>UoM</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                        </table>`;
                row.child(childHtml).show();
                icon.removeClass('ri-add-line').addClass('ri-subtract-line');

                // Init DataTable pada child row
                $('#' + $.escapeSelector(childTableId)).DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "<?= site_url('out_kny/get_detail_fpk'); ?>",
                        "type": "POST",
                        "data": {
                            pr_id: pr_id
                        }
                    },
                    "columns": [{
                            "data": "no",
                            className: "text-center",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "nama_item",
                            "render": function(data, type, row) {
                                if (!data) return '';

                                const limit = 30;
                                if (data.length > limit) {
                                    return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                                }
                                return data;
                            },
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "kode_item",
                            "render": function(data, type, row) {
                                if (!data) return '';

                                const limit = 30;
                                if (data.length > limit) {
                                    return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                                }
                                return data;
                            },
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "jumlah",
                            className: "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "terima",
                            className: 'text-end',
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "sisa",
                            className: "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "uom",
                            className: "text-center",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "harga",
                            className: "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "subtotal",
                            className: "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "note",
                            "render": function(data, type, row) {
                                if (!data) return '';

                                const limit = 30;
                                if (data.length > limit) {
                                    return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                                }
                                return data;
                            },
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                    ],
                    "paging": true,
                    "searching": false,
                    "ordering": false,
                    "info": true,
                    "autoWidth": false
                });
            }
        });

        $('#table-fpk thead').on('input', '.column_search', debounce(function() {
            let index = $(this).parent().index();
            fpk.column(index).search(this.value).draw();
        }, 1500));

        $('#export_fpk').on('click', function() {
            var exportParams = fpk.ajax.params();
            exportParams.start = 0;
            exportParams.length = -1;

            var params = $.param(exportParams);
            window.location.href = "<?= site_url('out_kny/export_fpk') ?>?" + params;
        });
    });

    function toggleSupplier() {
        const isChecked = $("#check_supplier").is(":checked");

        $("#supplier").prop("disabled", isChecked);

        if (isChecked) {
            $("#supplier").val(null).trigger("change");
        }
    }

    function togglePeriod() {

        const drp = $('#daterange').data('daterangepicker');

        if ($('#check_period').is(':checked')) {

            $('#daterange')
                .val('')
                .prop('disabled', true);

            if (drp) {
                drp.startDate = moment();
                drp.endDate = moment();
            }

        } else {

            $('#daterange')
                .prop('disabled', false);
        }
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