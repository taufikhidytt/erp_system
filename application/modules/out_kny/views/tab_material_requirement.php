<!-- Filter Supplier -->
<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Customer</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="check_supplier_mr" checked>
            <label class="form-check-label" for="check_supplier_mr">
                All
            </label>
        </div>
    </div>
    <div class="col-md-10">
        <select name="supplier_mr" id="supplier_mr"
            data-url="out_kny/get_supplier_mr"
            data-selected-id="<?= set_value('supplier_mr', '') ?>"
            style="width: 100%;"
            class="select2 w-100 <?= form_error('supplier_mr') ? 'is-invalid' : null; ?>">
        </select>
    </div>
</div>

<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Status</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" name="status_all_mr" value="ALL" class="form-check-input" id="statusAllMr">
            <label class="form-check-label">
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <div class="form-control d-flex flex-wrap gap-3 align-items-center justify-content-center align-items-center" style="min-height: 38px;" id="card-status-mr">
            <label class=" mb-0 me-3">
                <input type="radio" name="status_mr" value="COMPLETE" class="form-check-input">
                COMPLETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_mr" value="NEW" checked class="form-check-input">
                NEW
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_mr" value="PARTIAL" class="form-check-input">
                PARTIAL
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_mr" value="CLOSE" class="form-check-input">
                CLOSE/DELETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_mr" value="OUTSTANDING" class="form-check-input">
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
            <input type="checkbox" class="form-check-input" id="check_period_mr" checked>
            <label class="form-check-label" for="check_period_mr" checked>
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <input type="text" id="daterange_mr" class="form-control">
    </div>
</div>

<div class="row mt-3">
    <div class="table-responsive">
        <table class="table text-center w-100 text-nowrap table-sm" id="table-mr">
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
                </tr>
                <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                    <th></th>
                    <th>No</th>
                    <th>Status</th>
                    <th>No Transaksi</th>
                    <th>No Referensi</th>
                    <th>Tanggal</th>
                    <th>Customer</th>
                    <th>Storage</th>
                    <th>Nama Item</th>
                    <th>Satuan</th>
                    <th>Unit</th>
                    <th>Flag</th>
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
        toggleSupplierMr();
        togglePeriodMr();

        $('#supplier_mr, #check_supplier_mr, #statusAllMr, input[name="status_mr"], #check_period_mr, #daterange_mr').change(function() {
            mr.ajax.reload();
        });

        $("#check_supplier_mr").on("change", function() {
            toggleSupplierMr();
        });

        $('#statusAllMr').on('change', function() {
            if ($(this).is(':checked')) {
                $('input[name="status_mr"]')
                    .prop('checked', false)
                    .prop('disabled', true);

                $('#card-status-mr').css('background-color', '#EFF2F7');
            } else {
                $('input[name="status_mr"]')
                    .prop('disabled', false);

                $('input[name="status_mr"][value="NEW"]')
                    .prop('checked', true);

                $('#card-status-mr').css('background-color', '');
            }

            mr.ajax.reload();
        });

        let start = moment().startOf('month');
        let end = moment().endOf('month');

        $('#daterange_mr').daterangepicker({
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

        updateDisplayMr(start, end);

        $('#daterange_mr').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format('YYYY-MM-DD') +
                ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );

            mr.ajax.reload();
        });

        $('#daterange_mr').prop('disabled', true);

        $('#daterange_mr').on('keydown keypress paste input', function(e) {
            e.preventDefault();
        });

        $('#check_period_mr').on('change', function() {
            togglePeriodMr();
            mr.ajax.reload();
        });

        var mr = $('#table-mr').DataTable({
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
                        supplier: $('#supplier_mr').val(),
                        check_supplier: $('#check_supplier_mr').is(':checked'),
                        status: $('input[name="status_mr"]:checked').val(),
                        check_status: $('#statusAllMr').is(':checked'),
                        check_period: $('#check_period_mr').is(':checked'),
                        daterange: $('#daterange_mr').val(),
                        search_global: mr.search()
                    };

                    mr.columns().every(function(index) {
                        const searchValue = this.search();

                        if (searchValue) {
                            params['columns[' + index + '][search][value]'] = searchValue;
                        }
                    });

                    const order = mr.order();
                    if (order.length) {
                        params['order[0][column]'] = order[0][0];
                        params['order[0][dir]'] = order[0][1];
                    }

                    window.location.href =
                        "<?= site_url('out_kny/export_material_requirement') ?>?" + $.param(params);
                }
            }],
            ajax: {
                url: "<?= site_url('out_kny/get_data_material_requirement'); ?>",
                type: "POST",
                data: function(d) {
                    d.supplier = $('#supplier_mr').val();
                    d.check_supplier = $('#check_supplier_mr').is(':checked');
                    d.status = $('input[name="status_mr"]:checked').val();
                    d.check_status = $('#statusAllMr').is(':checked');
                    d.check_period = $('#check_period_mr').is(':checked');
                    d.daterange = $('#daterange_mr').val();
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
            ],
            columnDefs: [{
                    className: 'text-center',
                    targets: [1, 2, 5, 12],
                },
                {
                    orderable: false,
                    targets: [0],
                },
            ]
        });

        $('#table-mr tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = mr.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var build_id = rowData[12];

            if (row.child.isShown()) {
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                var childTableId = 'child-mr-' + build_id;

                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                <tr class="align-middle">
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kode Item</th>
                                    <th>Jumlah</th>
                                    <th>PO</th>
                                    <th>Sisa</th>
                                    <th>Satuan</th>
                                    <th>Batch No</th>
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
                        "url": "<?= site_url('out_kny/get_detail_material_requirement'); ?>",
                        "type": "POST",
                        "data": {
                            build_id: build_id
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
                            "render": renderLimitMr,
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "kode_item",
                            "render": renderLimitMr,
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
                            "data": "po",
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
                            "data": "batch_no",
                            "render": renderLimitMr,
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "note",
                            "render": renderLimitMr,
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

        $('#table-mr thead').on('input', '.column_search', debounceMr(function() {
            let index = $(this).parent().index();
            mr.column(index).search(this.value).draw();
        }, 1000));
    });

    function toggleSupplierMr() {
        const isChecked = $("#check_supplier_mr").is(":checked");

        $("#supplier_mr").prop("disabled", isChecked);

        if (isChecked) {
            $("#supplier_mr").val(null).trigger("change");
        }
    }

    function updateDisplayMr(start, end) {
        $('#startDate').text(start.format('YYYY-MM-DD'));
        $('#endDate').text(end.format('YYYY-MM-DD'));
    }

    function togglePeriodMr() {
        const drp = $('#daterange_mr').data('daterangepicker');

        if ($('#check_period_mr').is(':checked')) {
            $('#daterange_mr')
                .val('')
                .prop('disabled', true);

            if (drp) {
                drp.setStartDate(moment());
                drp.setEndDate(moment());
            }
        } else {
            $('#daterange_mr')
                .prop('disabled', false);

            if (drp) {
                let start = moment().startOf('month');
                let end = moment().endOf('month');

                drp.setStartDate(start);
                drp.setEndDate(end);

                $('#daterange_mr').val(
                    start.format('YYYY-MM-DD') +
                    ' - ' +
                    end.format('YYYY-MM-DD')
                );

                updateDisplayMr(start, end);
            }
        }
    }

    function renderLimitMr(data) {
        if (!data) return '';

        const limit = 30;
        if (data.length > limit) {
            return `<span title="${data.replace(/"/g, '"')}">
                ${data.substring(0, limit)}...
            </span>`;
        }

        return data;
    }

    function debounceMr(func, delay) {
        let timer;
        return function() {
            let context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(context, args), delay);
        };
    }
</script>