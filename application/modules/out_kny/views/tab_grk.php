<!-- Filter Supplier -->
<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Supplier</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="check_supplier_grk" checked>
            <label class="form-check-label" for="check_supplier_grk">
                All
            </label>
        </div>
    </div>
    <div class="col-md-10">
        <select name="supplier_grk" id="supplier_grk"
            data-url="out_kny/get_supplier_grk"
            data-selected-id="<?= set_value('supplier_grk', '') ?>"
            style="width: 100%;"
            class="select2 w-100 <?= form_error('supplier_grk') ? 'is-invalid' : null; ?>">
        </select>
    </div>
</div>

<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Status</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" name="status_all_grk" value="ALL" class="form-check-input" id="statusAllGrk">
            <label class="form-check-label">
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <div class="form-control d-flex flex-wrap gap-3 align-items-center justify-content-center align-items-center" style="min-height: 38px;" id="card-status-grk">
            <label class=" mb-0 me-3">
                <input type="radio" name="status_grk" value="COMPLETE" class="form-check-input">
                COMPLETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_grk" value="NEW" checked class="form-check-input">
                NEW
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_grk" value="PARTIAL" class="form-check-input">
                PARTIAL
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_grk" value="CLOSE" class="form-check-input">
                CLOSE/DELETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_grk" value="OUTSTANDING" class="form-check-input">
                OUTSTANDING
            </label>
        </div>
    </div>
</div>

<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Periode</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="check_period_grk" checked>
            <label class="form-check-label" for="check_period_grk" checked>
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <input type="text" id="daterange_grk" class="form-control">
    </div>
</div>
<div class="row mt-3">
    <div class="table-responsive">
        <table class="table text-center w-100 text-nowrap table-sm" id="table-grk">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                    <th><input type="text" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
                    <th><input type="date" class="column_search" placeholder="Cari.." style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;"></th>
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
                    <th>Supplier</th>
                    <th>Storage</th>
                    <th>Total</th>
                    <th>Periode</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        toggleSupplierGrk();
        togglePeriodGrk();

        $('#supplier_grk, #check_supplier_grk, #statusAllGrk, input[name="status_grk"], #check_period_grk, #daterange_grk').change(function() {
            grk.ajax.reload();
        });

        $("#check_supplier_grk").on("change", function() {
            toggleSupplierGrk();
        });

        $('#statusAllGrk').on('change', function() {

            if ($(this).is(':checked')) {

                $('input[name="status_grk"]')
                    .prop('checked', false)
                    .prop('disabled', true);

                $('#card-status-grk').css('background-color', '#EFF2F7');

            } else {

                $('input[name="status_grk"]')
                    .prop('disabled', false);

                $('input[name="status_grk"][value="NEW"]')
                    .prop('checked', true);

                $('#card-status-grk').css('background-color', '');
            }

            grk.ajax.reload();
        });

        let start = moment().startOf('month');
        let end = moment().endOf('month');

        $('#daterange_grk').daterangepicker({
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

        $('#daterange_grk').on('apply.daterangepicker', function(ev, picker) {

            $(this).val(
                picker.startDate.format('YYYY-MM-DD') +
                ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );

            grk.ajax.reload();
        });

        $('#daterange_grk').prop('disabled', true);

        $('#daterange_grk').on('keydown keypress paste input', function(e) {
            e.preventDefault();
        });

        $('#check_period_grk').on('change', function() {

            togglePeriodGrk();

            grk.ajax.reload();
        });

        // Tab grk
        var grk = $('#table-grk').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            order: [],
            dom: '<"row mb-2 align-items-center"' +
                '<"col-md-6 d-flex align-items-center"l>' +
                '<"col-md-6 d-flex justify-content-end gap-2"Bf>' +
                '>' +
                'rt' +
                '<"row mt-2"' +
                '<"col-md-6"i>' +
                '<"col-md-6 text-end"p>' +
                '>',
            buttons: [{
                text: '<i class="fas fa-file-excel me-1"></i> Export',
                className: 'btn btn-primary btn-sm',
                action: function() {
                    let params = {
                        supplier: $('#supplier_grk').val(),
                        check_supplier: $('#check_supplier_grk').is(':checked'),
                        status: $('input[name="status_grk"]:checked').val(),
                        check_status: $('#statusAllGrk').is(':checked'),
                        check_period: $('#check_period_grk').is(':checked'),
                        daterange: $('#daterange_grk').val(),
                        search_global: grk.search()
                    };

                    grk.columns().every(function(index) {
                        const searchValue = this.search();

                        if (searchValue) {
                            params['columns[' + index + '][search][value]'] = searchValue;
                        }
                    });

                    const order = grk.order();
                    if (order.length) {
                        params['order[0][column]'] = order[0][0];
                        params['order[0][dir]'] = order[0][1];
                    }

                    window.location.href =
                        "<?= site_url('out_kny/export_grk') ?>?" + $.param(params);
                }
            }],
            ajax: {
                url: "<?= site_url('out_kny/get_data_grk'); ?>",
                type: "POST",
                data: function(d) {
                    d.supplier = $('#supplier_grk').val();
                    d.check_supplier = $('#check_supplier_grk').is(':checked');
                    d.status = $('input[name="status_grk"]:checked').val();
                    d.check_status = $('#statusAllGrk').is(':checked');
                    d.check_period = $('#check_period_grk').is(':checked');
                    d.daterange = $('#daterange_grk').val();
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
            ],
            columnDefs: [{
                    className: 'text-end',
                    targets: [8],
                },
                {
                    className: 'text-center',
                    targets: [1, 2, 5, 9],
                },
                {
                    orderable: false,
                    targets: [0],
                },
            ]
        });

        $('#table-grk tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = grk.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var po_id = rowData[9];


            if (row.child.isShown()) {
                // Close row
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                // Open row dengan child row datatable
                var childTableId = 'child-grk-' + po_id;

                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                <tr class="align-middle">
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kode Item</th>
                                    <th>Jumlah</th>
                                    <th>Kirim/Retur</th>
                                    <th>Sisa</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                    <th>Reff No</th>
                                    <th>Sales</th>
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
                        "url": "<?= site_url('out_kny/get_detail_grk'); ?>",
                        "type": "POST",
                        "data": {
                            po_id: po_id
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
                            "data": "kirim_retur",
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
                            "data": "reff_no",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "sales",
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

        $('#table-grk thead').on('input', '.column_search', debounce(function() {
            let index = $(this).parent().index();
            grk.column(index).search(this.value).draw();
        }, 1000));

        $('#export_grk').on('click', function() {

            let params = {
                supplier: $('#supplier_grk').val(),
                check_supplier: $('#check_supplier_grk').is(':checked'),
                status: $('input[name="status_grk"]:checked').val(),
                check_status: $('#statusAllGrk').is(':checked'),
                check_period: $('#check_period_grk').is(':checked'),
                daterange: $('#daterange_grk').val(),

                // GLOBAL SEARCH
                search_global: grk.search()
            };

            // COLUMN SEARCH
            $('#table-grk thead .column_search').each(function() {
                const index = $(this).parent().index();
                params['columns[' + index + '][search][value]'] = $(this).val();
            });

            const order = grk.order();
            if (order.length) {
                params['order[0][column]'] = order[0][0];
                params['order[0][dir]'] = order[0][1];
            }

            window.location.href =
                "<?= site_url('out_kny/export_grk') ?>?" + $.param(params);
        });
    });

    function toggleSupplierGrk() {
        const isChecked = $("#check_supplier_grk").is(":checked");

        $("#supplier_grk").prop("disabled", isChecked);

        if (isChecked) {
            $("#supplier_grk").val(null).trigger("change");
        }
    }

    function updateDisplay(start, end) {
        $('#startDate').text(start.format('YYYY-MM-DD'));
        $('#endDate').text(end.format('YYYY-MM-DD'));
    }

    function togglePeriodGrk() {

        const drp = $('#daterange_grk').data('daterangepicker');

        if ($('#check_period_grk').is(':checked')) {

            // ALL = ON → disable + clear
            $('#daterange_grk')
                .val('')
                .prop('disabled', true);

            if (drp) {
                drp.setStartDate(moment());
                drp.setEndDate(moment());
            }

        } else {

            // ALL = OFF → enable + reset ke bulan ini
            $('#daterange_grk')
                .prop('disabled', false);

            if (drp) {

                let start = moment().startOf('month');
                let end = moment().endOf('month');

                drp.setStartDate(start);
                drp.setEndDate(end);

                $('#daterange_grk').val(
                    start.format('YYYY-MM-DD') +
                    ' - ' +
                    end.format('YYYY-MM-DD')
                );

                updateDisplay(start, end);
            }
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
