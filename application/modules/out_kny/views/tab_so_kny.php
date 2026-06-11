<!-- Filter Supplier -->
<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Customer</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="check_supplier_sk" checked>
            <label class="form-check-label" for="check_supplier_sk">
                All
            </label>
        </div>
    </div>
    <div class="col-md-10">
        <select name="supplier_sk" id="supplier_sk"
            data-url="out_kny/get_supplier_sk"
            data-selected-id="<?= set_value('supplier_sk', '') ?>"
            style="width: 100%;"
            class="select2 w-100 <?= form_error('supplier_sk') ? 'is-invalid' : null; ?>">
        </select>
    </div>
</div>

<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Status</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" name="status_all_sk" value="ALL" class="form-check-input" id="statusAllSk">
            <label class="form-check-label">
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <div class="form-control d-flex flex-wrap gap-3 align-items-center justify-content-center align-items-center" style="min-height: 38px;" id="card-status-sk">
            <label class=" mb-0 me-3">
                <input type="radio" name="status_sk" value="COMPLETE" class="form-check-input">
                COMPLETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_sk" value="NEW" checked class="form-check-input">
                NEW
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_sk" value="PARTIAL" class="form-check-input">
                PARTIAL
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_sk" value="CLOSE" class="form-check-input">
                CLOSE/DELETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_sk" value="OUTSTANDING" class="form-check-input">
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
            <input type="checkbox" class="form-check-input" id="check_period_sk" checked>
            <label class="form-check-label" for="check_period_sk" checked>
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <input type="text" id="daterange_sk" class="form-control">
    </div>
</div>

<div class="row mt-3">
    <div class="table-responsive">
        <table class="table text-center w-100 text-nowrap table-sm" id="table-sk">
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
                    <th>PO Customer</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Sales</th>
                    <th>Terms</th>
                    <th>Storage</th>
                    <th>Total Amount</th>
                    <th>Total Diskon</th>
                    <th>Total PPN</th>
                    <th>Total Net</th>
                    <th>PPN</th>
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
        toggleSupplierSk();
        togglePeriodSk();

        $('#supplier_sk, #check_supplier_sk, #statusAllSk, input[name="status_sk"], #check_period_sk, #daterange_sk').change(function() {
            sk.ajax.reload();
        });

        $("#check_supplier_sk").on("change", function() {
            toggleSupplierSk();
        });

        $('#statusAllSk').on('change', function() {
            if ($(this).is(':checked')) {
                $('input[name="status_sk"]')
                    .prop('checked', false)
                    .prop('disabled', true);

                $('#card-status-sk').css('background-color', '#EFF2F7');
            } else {
                $('input[name="status_sk"]')
                    .prop('disabled', false);

                $('input[name="status_sk"][value="NEW"]')
                    .prop('checked', true);

                $('#card-status-sk').css('background-color', '');
            }

            sk.ajax.reload();
        });

        let start = moment().startOf('month');
        let end = moment().endOf('month');

        $('#daterange_sk').daterangepicker({
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

        updateDisplaySk(start, end);

        $('#daterange_sk').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format('YYYY-MM-DD') +
                ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );

            sk.ajax.reload();
        });

        $('#daterange_sk').prop('disabled', true);

        $('#daterange_sk').on('keydown keypress paste input', function(e) {
            e.preventDefault();
        });

        $('#check_period_sk').on('change', function() {
            togglePeriodSk();
            sk.ajax.reload();
        });

        var sk = $('#table-sk').DataTable({
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
                        supplier: $('#supplier_sk').val(),
                        check_supplier: $('#check_supplier_sk').is(':checked'),
                        status: $('input[name="status_sk"]:checked').val(),
                        check_status: $('#statusAllSk').is(':checked'),
                        check_period: $('#check_period_sk').is(':checked'),
                        daterange: $('#daterange_sk').val(),
                        search_global: sk.search()
                    };

                    sk.columns().every(function(index) {
                        const searchValue = this.search();

                        if (searchValue) {
                            params['columns[' + index + '][search][value]'] = searchValue;
                        }
                    });

                    const order = sk.order();
                    if (order.length) {
                        params['order[0][column]'] = order[0][0];
                        params['order[0][dir]'] = order[0][1];
                    }

                    window.location.href =
                        "<?= site_url('out_kny/export_so_kny') ?>?" + $.param(params);
                }
            }],
            ajax: {
                url: "<?= site_url('out_kny/get_data_so_kny'); ?>",
                type: "POST",
                data: function(d) {
                    d.supplier = $('#supplier_sk').val();
                    d.check_supplier = $('#check_supplier_sk').is(':checked');
                    d.status = $('input[name="status_sk"]:checked').val();
                    d.check_status = $('#statusAllSk').is(':checked');
                    d.check_period = $('#check_period_sk').is(':checked');
                    d.daterange = $('#daterange_sk').val();
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
                {
                    data: "13"
                },
                {
                    data: "14"
                },
            ],
            columnDefs: [{
                    className: 'text-end',
                    targets: [10, 11, 12, 13],
                },
                {
                    className: 'text-center',
                    targets: [1, 2, 5, 15],
                },
                {
                    orderable: false,
                    targets: [0],
                },
            ]
        });

        $('#table-sk tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = sk.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var so_id = rowData[15];

            if (row.child.isShown()) {
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                var childTableId = 'child-sk-' + so_id;

                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                <tr class="align-middle">
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kode Item</th>
                                    <th>Jumlah</th>
                                    <th>Kirim</th>
                                    <th>Sisa</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Diskon</th>
                                    <th>Total</th>
                                    <th>Disc Total</th>
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
                        "url": "<?= site_url('out_kny/get_detail_so_kny'); ?>",
                        "type": "POST",
                        "data": {
                            so_id: so_id
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
                            "render": renderLimitSk,
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "kode_item",
                            "render": renderLimitSk,
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
                            "data": "kirim",
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
                            "data": "harga",
                            className: "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "diskon",
                            className: "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "total",
                            className: "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "disc_total",
                            className: "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "reff_no",
                            "render": renderLimitSk,
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
                            "render": renderLimitSk,
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

        $('#table-sk thead').on('input', '.column_search', debounceSk(function() {
            let index = $(this).parent().index();
            sk.column(index).search(this.value).draw();
        }, 1000));
    });

    function toggleSupplierSk() {
        const isChecked = $("#check_supplier_sk").is(":checked");

        $("#supplier_sk").prop("disabled", isChecked);

        if (isChecked) {
            $("#supplier_sk").val(null).trigger("change");
        }
    }

    function updateDisplaySk(start, end) {
        $('#startDate').text(start.format('YYYY-MM-DD'));
        $('#endDate').text(end.format('YYYY-MM-DD'));
    }

    function togglePeriodSk() {
        const drp = $('#daterange_sk').data('daterangepicker');

        if ($('#check_period_sk').is(':checked')) {
            $('#daterange_sk')
                .val('')
                .prop('disabled', true);

            if (drp) {
                drp.setStartDate(moment());
                drp.setEndDate(moment());
            }
        } else {
            $('#daterange_sk')
                .prop('disabled', false);

            if (drp) {
                let start = moment().startOf('month');
                let end = moment().endOf('month');

                drp.setStartDate(start);
                drp.setEndDate(end);

                $('#daterange_sk').val(
                    start.format('YYYY-MM-DD') +
                    ' - ' +
                    end.format('YYYY-MM-DD')
                );

                updateDisplaySk(start, end);
            }
        }
    }

    function renderLimitSk(data) {
        if (!data) return '';

        const limit = 30;
        if (data.length > limit) {
            return `<span title="${data.replace(/"/g, '"')}">
                ${data.substring(0, limit)}...
            </span>`;
        }

        return data;
    }

    function debounceSk(func, delay) {
        let timer;
        return function() {
            let context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(context, args), delay);
        };
    }
</script>