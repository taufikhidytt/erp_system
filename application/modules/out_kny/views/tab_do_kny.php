<!-- Filter Supplier -->
<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Customer</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="check_supplier_dk" checked>
            <label class="form-check-label" for="check_supplier_dk">
                All
            </label>
        </div>
    </div>
    <div class="col-md-10">
        <select name="supplier_dk" id="supplier_dk"
            data-url="out_kny/get_supplier_dk"
            data-selected-id="<?= set_value('supplier_dk', '') ?>"
            style="width: 100%;"
            class="select2 w-100 <?= form_error('supplier_dk') ? 'is-invalid' : null; ?>">
        </select>
    </div>
</div>

<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Status</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" name="status_all_dk" value="ALL" class="form-check-input" id="statusAllDk">
            <label class="form-check-label">
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <div class="form-control d-flex flex-wrap gap-3 align-items-center justify-content-center align-items-center" style="min-height: 38px;" id="card-status-dk">
            <label class=" mb-0 me-3">
                <input type="radio" name="status_dk" value="COMPLETE" class="form-check-input">
                COMPLETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_dk" value="NEW" checked class="form-check-input">
                NEW
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_dk" value="PARTIAL" class="form-check-input">
                PARTIAL
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_dk" value="CLOSE" class="form-check-input">
                CLOSE/DELETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_dk" value="OUTSTANDING" class="form-check-input">
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
            <input type="checkbox" class="form-check-input" id="check_period_dk" checked>
            <label class="form-check-label" for="check_period_dk" checked>
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <input type="text" id="daterange_dk" class="form-control">
    </div>
</div>

<div class="row mt-3">
    <div class="table-responsive">
        <table class="table text-center w-100 text-nowrap table-sm" id="table-dk">
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
                    <th>PO Customer</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Sales</th>
                    <th>Storage</th>
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
        toggleSupplierDk();
        togglePeriodDk();

        $('#supplier_dk, #check_supplier_dk, #statusAllDk, input[name="status_dk"], #check_period_dk, #daterange_dk').change(function() {
            dk.ajax.reload();
        });

        $("#check_supplier_dk").on("change", function() {
            toggleSupplierDk();
        });

        $('#statusAllDk').on('change', function() {
            if ($(this).is(':checked')) {
                $('input[name="status_dk"]')
                    .prop('checked', false)
                    .prop('disabled', true);

                $('#card-status-dk').css('background-color', '#EFF2F7');
            } else {
                $('input[name="status_dk"]')
                    .prop('disabled', false);

                $('input[name="status_dk"][value="NEW"]')
                    .prop('checked', true);

                $('#card-status-dk').css('background-color', '');
            }

            dk.ajax.reload();
        });

        let start = moment().startOf('month');
        let end = moment().endOf('month');

        $('#daterange_dk').daterangepicker({
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

        updateDisplayDk(start, end);

        $('#daterange_dk').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format('YYYY-MM-DD') +
                ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );

            dk.ajax.reload();
        });

        $('#daterange_dk').prop('disabled', true);

        $('#daterange_dk').on('keydown keypress paste input', function(e) {
            e.preventDefault();
        });

        $('#check_period_dk').on('change', function() {
            togglePeriodDk();
            dk.ajax.reload();
        });

        var dk = $('#table-dk').DataTable({
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
                        supplier: $('#supplier_dk').val(),
                        check_supplier: $('#check_supplier_dk').is(':checked'),
                        status: $('input[name="status_dk"]:checked').val(),
                        check_status: $('#statusAllDk').is(':checked'),
                        check_period: $('#check_period_dk').is(':checked'),
                        daterange: $('#daterange_dk').val(),
                        search_global: dk.search()
                    };

                    dk.columns().every(function(index) {
                        const searchValue = this.search();

                        if (searchValue) {
                            params['columns[' + index + '][search][value]'] = searchValue;
                        }
                    });

                    const order = dk.order();
                    if (order.length) {
                        params['order[0][column]'] = order[0][0];
                        params['order[0][dir]'] = order[0][1];
                    }

                    window.location.href =
                        "<?= site_url('out_kny/export_do_kny') ?>?" + $.param(params);
                }
            }],
            ajax: {
                url: "<?= site_url('out_kny/get_data_do_kny'); ?>",
                type: "POST",
                data: function(d) {
                    d.supplier = $('#supplier_dk').val();
                    d.check_supplier = $('#check_supplier_dk').is(':checked');
                    d.status = $('input[name="status_dk"]:checked').val();
                    d.check_status = $('#statusAllDk').is(':checked');
                    d.check_period = $('#check_period_dk').is(':checked');
                    d.daterange = $('#daterange_dk').val();
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
                    className: 'text-center',
                    targets: [1, 2, 5, 9],
                },
                {
                    orderable: false,
                    targets: [0],
                },
            ]
        });

        $('#table-dk tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = dk.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var inventory_out_id = rowData[9];

            if (row.child.isShown()) {
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                var childTableId = 'child-dk-' + inventory_out_id;

                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                <tr class="align-middle">
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kode Item</th>
                                    <th>Jumlah</th>
                                    <th>Invoice</th>
                                    <th>Sisa</th>
                                    <th>Satuan</th>
                                    <th>Reff No</th>
                                    <th>Storage</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                        </table>`;
                row.child(childHtml).show();
                icon.removeClass('ri-add-line').addClass('ri-subtract-line');

                $('#' + $.escapeSelector(childTableId)).DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "<?= site_url('out_kny/get_detail_do_kny'); ?>",
                        "type": "POST",
                        "data": {
                            inventory_out_id: inventory_out_id
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
                            "render": renderLimitDk,
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "kode_item",
                            "render": renderLimitDk,
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
                            "data": "invoice",
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
                            "data": "satuan",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "reff_no",
                            "render": renderLimitDk,
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "storage",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "note",
                            "render": renderLimitDk,
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

        $('#table-dk thead').on('input', '.column_search', debounceDk(function() {
            let index = $(this).parent().index();
            dk.column(index).search(this.value).draw();
        }, 1000));
    });

    function toggleSupplierDk() {
        const isChecked = $("#check_supplier_dk").is(":checked");

        $("#supplier_dk").prop("disabled", isChecked);

        if (isChecked) {
            $("#supplier_dk").val(null).trigger("change");
        }
    }

    function updateDisplayDk(start, end) {
        $('#startDate').text(start.format('YYYY-MM-DD'));
        $('#endDate').text(end.format('YYYY-MM-DD'));
    }

    function togglePeriodDk() {
        const drp = $('#daterange_dk').data('daterangepicker');

        if ($('#check_period_dk').is(':checked')) {
            $('#daterange_dk')
                .val('')
                .prop('disabled', true);

            if (drp) {
                drp.setStartDate(moment());
                drp.setEndDate(moment());
            }
        } else {
            $('#daterange_dk')
                .prop('disabled', false);

            if (drp) {
                let start = moment().startOf('month');
                let end = moment().endOf('month');

                drp.setStartDate(start);
                drp.setEndDate(end);

                $('#daterange_dk').val(
                    start.format('YYYY-MM-DD') +
                    ' - ' +
                    end.format('YYYY-MM-DD')
                );

                updateDisplayDk(start, end);
            }
        }
    }

    function renderLimitDk(data) {
        if (!data) return '';

        const limit = 30;
        if (data.length > limit) {
            return `<span title="${data.replace(/"/g, '"')}">
                ${data.substring(0, limit)}...
            </span>`;
        }

        return data;
    }

    function debounceDk(func, delay) {
        let timer;
        return function() {
            let context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(context, args), delay);
        };
    }
</script>