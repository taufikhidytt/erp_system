<!-- Filter Supplier -->
<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Supplier</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="check_supplier_pk" checked>
            <label class="form-check-label" for="check_supplier_pk">
                All
            </label>
        </div>
    </div>
    <div class="col-md-10">
        <select name="supplier_pk" id="supplier_pk"
            data-url="out_kny/get_supplier_pk"
            data-selected-id="<?= set_value('supplier_pk', '') ?>"
            style="width: 100%;"
            class="select2 w-100 <?= form_error('supplier_pk') ? 'is-invalid' : null; ?>">
        </select>
    </div>
</div>

<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Status</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" name="status_all_pk" value="ALL" class="form-check-input" id="statusAllPk">
            <label class="form-check-label">
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <div class="form-control d-flex flex-wrap gap-3 align-items-center justify-content-center align-items-center" style="min-height: 38px;" id="card-status-pk">
            <label class=" mb-0 me-3">
                <input type="radio" name="status_pk" value="LUNAS" class="form-check-input">
                LUNAS
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_pk" value="NEW" checked class="form-check-input">
                NEW
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_pk" value="BELUM_LUNAS" class="form-check-input">
                BELUM LUNAS
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_pk" value="CLOSE" class="form-check-input">
                CLOSE/DELETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_pk" value="OUTSTANDING" class="form-check-input">
                OUTSTANDING
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_pk" value="TERTAGIH" class="form-check-input">
                TERTAGIH
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
            <input type="checkbox" class="form-check-input" id="check_period_pk" checked>
            <label class="form-check-label" for="check_period_pk" checked>
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <input type="text" id="daterange_pk" class="form-control">
    </div>
</div>

<div class="row mt-3">
    <div class="table-responsive">
        <table class="table text-center w-100 text-nowrap table-sm" id="table-pk">
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
                </tr>
                <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                    <th></th>
                    <th>No</th>
                    <th>Status</th>
                    <th>No Transaksi</th>
                    <th>No Referensi</th>
                    <th>Tanggal</th>
                    <th>Supplier</th>
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
        toggleSupplierPk();
        togglePeriodPk();

        $('#supplier_pk, #check_supplier_pk, #statusAllPk, input[name="status_pk"], #check_period_pk, #daterange_pk').change(function() {
            pk.ajax.reload();
        });

        $("#check_supplier_pk").on("change", function() {
            toggleSupplierPk();
        });

        $('#statusAllPk').on('change', function() {
            if ($(this).is(':checked')) {
                $('input[name="status_pk"]')
                    .prop('checked', false)
                    .prop('disabled', true);

                $('#card-status-pk').css('background-color', '#EFF2F7');
            } else {
                $('input[name="status_pk"]')
                    .prop('disabled', false);

                $('input[name="status_pk"][value="PARTIAL"]')
                    .prop('checked', true);

                $('#card-status-pk').css('background-color', '');
            }

            pk.ajax.reload();
        });

        let start = moment().startOf('month');
        let end = moment().endOf('month');

        $('#daterange_pk').daterangepicker({
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

        updateDisplayPk(start, end);

        $('#daterange_pk').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format('YYYY-MM-DD') +
                ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );

            pk.ajax.reload();
        });

        $('#daterange_pk').prop('disabled', true);

        $('#daterange_pk').on('keydown keypress paste input', function(e) {
            e.preventDefault();
        });

        $('#check_period_pk').on('change', function() {
            togglePeriodPk();
            pk.ajax.reload();
        });

        var pk = $('#table-pk').DataTable({
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
                        supplier: $('#supplier_pk').val(),
                        check_supplier: $('#check_supplier_pk').is(':checked'),
                        status: $('input[name="status_pk"]:checked').val(),
                        check_status: $('#statusAllPk').is(':checked'),
                        check_period: $('#check_period_pk').is(':checked'),
                        daterange: $('#daterange_pk').val(),
                        search_global: pk.search()
                    };

                    pk.columns().every(function(index) {
                        const searchValue = this.search();

                        if (searchValue) {
                            params['columns[' + index + '][search][value]'] = searchValue;
                        }
                    });

                    const order = pk.order();
                    if (order.length) {
                        params['order[0][column]'] = order[0][0];
                        params['order[0][dir]'] = order[0][1];
                    }

                    window.location.href =
                        "<?= site_url('out_kny/export_po_kny') ?>?" + $.param(params);
                }
            }],
            ajax: {
                url: "<?= site_url('out_kny/get_data_po_kny'); ?>",
                type: "POST",
                data: function(d) {
                    d.supplier = $('#supplier_pk').val();
                    d.check_supplier = $('#check_supplier_pk').is(':checked');
                    d.status = $('input[name="status_pk"]:checked').val();
                    d.check_status = $('#statusAllPk').is(':checked');
                    d.check_period = $('#check_period_pk').is(':checked');
                    d.daterange = $('#daterange_pk').val();
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
            ],
            columnDefs: [{
                    className: 'text-end',
                    targets: [9, 10, 11, 12],
                },
                {
                    className: 'text-center',
                    targets: [1, 2, 5, 14],
                },
                {
                    orderable: false,
                    targets: [0],
                },
            ]
        });

        $('#table-pk tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = pk.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var invoice_id = rowData[14];

            if (row.child.isShown()) {
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                var childTableId = 'child-pk-' + invoice_id;

                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                <tr class="align-middle">
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kode Item</th>
                                    <th>Jumlah</th>
                                    <th>Retur</th>
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
                        "url": "<?= site_url('out_kny/get_detail_po_kny'); ?>",
                        "type": "POST",
                        "data": {
                            invoice_id: invoice_id
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
                            "render": renderLimitPk,
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "kode_item",
                            "render": renderLimitPk,
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
                            "data": "retur",
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
                            "render": renderLimitPk,
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
                            "render": renderLimitPk,
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

        $('#table-pk thead').on('input', '.column_search', debouncePk(function() {
            let index = $(this).parent().index();
            pk.column(index).search(this.value).draw();
        }, 1000));
    });

    function toggleSupplierPk() {
        const isChecked = $("#check_supplier_pk").is(":checked");

        $("#supplier_pk").prop("disabled", isChecked);

        if (isChecked) {
            $("#supplier_pk").val(null).trigger("change");
        }
    }

    function updateDisplayPk(start, end) {
        $('#startDate').text(start.format('YYYY-MM-DD'));
        $('#endDate').text(end.format('YYYY-MM-DD'));
    }

    function togglePeriodPk() {
        const drp = $('#daterange_pk').data('daterangepicker');

        if ($('#check_period_pk').is(':checked')) {
            $('#daterange_pk')
                .val('')
                .prop('disabled', true);

            if (drp) {
                drp.setStartDate(moment());
                drp.setEndDate(moment());
            }
        } else {
            $('#daterange_pk')
                .prop('disabled', false);

            if (drp) {
                let start = moment().startOf('month');
                let end = moment().endOf('month');

                drp.setStartDate(start);
                drp.setEndDate(end);

                $('#daterange_pk').val(
                    start.format('YYYY-MM-DD') +
                    ' - ' +
                    end.format('YYYY-MM-DD')
                );

                updateDisplayPk(start, end);
            }
        }
    }

    function renderLimitPk(data) {
        if (!data) return '';

        const limit = 30;
        if (data.length > limit) {
            return `<span title="${data.replace(/"/g, '"')}">
                ${data.substring(0, limit)}...
            </span>`;
        }

        return data;
    }

    function debouncePk(func, delay) {
        let timer;
        return function() {
            let context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(context, args), delay);
        };
    }
</script>