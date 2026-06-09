<!-- Filter Supplier -->
<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Supplier</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="check_supplier" checked>
            <label class="form-check-label" for="check_supplier">
                All
            </label>
        </div>
    </div>
    <div class="col-md-10">
        <select name="supplier" id="supplier"
            data-url="out_kny/get_supplier"
            data-selected-id="<?= set_value('supplier', '') ?>"
            style="width: 100%;"
            class="select2 w-100 <?= form_error('supplier') ? 'is-invalid' : null; ?>">
        </select>
    </div>
</div>

<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Status</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" name="status_all" value="ALL" class="form-check-input" id="statusAll">
            <label class="form-check-label">
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <div class="form-control d-flex flex-wrap gap-3 align-items-center justify-content-center align-items-center" style="min-height: 38px;" id="card-status">
            <label class=" mb-0 me-3">
                <input type="radio" name="status" value="COMPLETE" class="form-check-input">
                COMPLETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status" value="NEW" checked class="form-check-input">
                NEW
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status" value="PARTIAL" class="form-check-input">
                PARTIAL
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status" value="CLOSE" class="form-check-input">
                CLOSE/DELETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status" value="OUTSTANDING" class="form-check-input">
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
            <input type="checkbox" class="form-check-input" id="check_period" checked>
            <label class="form-check-label" for="check_period" checked>
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <input type="text" id="daterange" class="form-control">
    </div>
</div>
<div class="row mt-3">
    <div class="table-responsive">
        <table class="table text-center w-100 text-nowrap table-sm" id="table-fpk">
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
                    <th>Sales</th>
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

                $('#card-status').css('background-color', '#EFF2F7');

            } else {

                $('input[name="status"]')
                    .prop('disabled', false);

                $('input[name="status"][value="NEW"]')
                    .prop('checked', true);

                $('#card-status').css('background-color', '');
            }

            fpk.ajax.reload();
        });

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
                        supplier: $('#supplier').val(),
                        check_supplier: $('#check_supplier').is(':checked'),
                        status: $('input[name="status"]:checked').val(),
                        check_status: $('#statusAll').is(':checked'),
                        check_period: $('#check_period').is(':checked'),
                        daterange: $('#daterange').val(),
                        search_global: fpk.search()
                    };

                    fpk.columns().every(function(index) {
                        const searchValue = this.search();

                        if (searchValue) {
                            params['columns[' + index + '][search][value]'] = searchValue;
                        }
                    });

                    const order = fpk.order();
                    if (order.length) {
                        params['order[0][column]'] = order[0][0];
                        params['order[0][dir]'] = order[0][1];
                    }

                    window.location.href =
                        "<?= site_url('out_kny/export_fpk') ?>?" + $.param(params);
                }
            }],
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
            ],
            columnDefs: [{
                    className: 'text-end',
                    targets: [9],
                },
                {
                    className: 'text-center',
                    targets: [1, 2, 5, 10],
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
            var pr_id = rowData[10];


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
                                    <th>Satuan</th>
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
        }, 1000));

        $('#export_fpk').on('click', function() {

            let params = {
                supplier: $('#supplier').val(),
                check_supplier: $('#check_supplier').is(':checked'),
                status: $('input[name="status"]:checked').val(),
                check_status: $('#statusAll').is(':checked'),
                check_period: $('#check_period').is(':checked'),
                daterange: $('#daterange').val(),

                // GLOBAL SEARCH
                search_global: $('.dataTables_filter input').val()
            };

            // COLUMN SEARCH
            $('#table-fpk thead .column_search').each(function() {
                const index = $(this).parent().index();
                params['columns[' + index + '][search][value]'] = $(this).val();
            });

            const order = fpk.order();
            if (order.length) {
                params['order[0][column]'] = order[0][0];
                params['order[0][dir]'] = order[0][1];
            }

            window.location.href =
                "<?= site_url('out_kny/export_fpk') ?>?" + $.param(params);
        });
    });

    function toggleSupplier() {
        const isChecked = $("#check_supplier").is(":checked");

        $("#supplier").prop("disabled", isChecked);

        if (isChecked) {
            $("#supplier").val(null).trigger("change");
        }
    }

    function updateDisplay(start, end) {
        $('#startDate').text(start.format('YYYY-MM-DD'));
        $('#endDate').text(end.format('YYYY-MM-DD'));
    }

    function togglePeriod() {

        const drp = $('#daterange').data('daterangepicker');

        if ($('#check_period').is(':checked')) {

            // ALL = ON → disable + clear
            $('#daterange')
                .val('')
                .prop('disabled', true);

            if (drp) {
                drp.setStartDate(moment());
                drp.setEndDate(moment());
            }

        } else {

            // ALL = OFF → enable + reset ke bulan ini
            $('#daterange')
                .prop('disabled', false);

            if (drp) {

                let start = moment().startOf('month');
                let end = moment().endOf('month');

                drp.setStartDate(start);
                drp.setEndDate(end);

                $('#daterange').val(
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