<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <label class="fw-bold mb-0">Status</label>
    </div>

    <div class="col-md-1">
        <div class="form-check">
            <input type="checkbox" name="status_all_ris" value="ALL" class="form-check-input" id="statusAllRis">
            <label class="form-check-label">
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <div class="form-control d-flex flex-wrap gap-3 align-items-center justify-content-center align-items-center" style="min-height: 38px;" id="card-status-ris">
            <label class=" mb-0 me-3">
                <input type="radio" name="status_ris" value="COMPLETE" class="form-check-input">
                COMPLETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_ris" value="NEW" checked class="form-check-input">
                NEW
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_ris" value="PARTIAL" class="form-check-input">
                PARTIAL
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_ris" value="CLOSE" class="form-check-input">
                CLOSE/DELETE
            </label>

            <label class="mb-0 me-3">
                <input type="radio" name="status_ris" value="OUTSTANDING" class="form-check-input">
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
            <input type="checkbox" class="form-check-input" id="check_period_ris" checked>
            <label class="form-check-label" for="check_period_ris" checked>
                All
            </label>
        </div>
    </div>

    <div class="col-md-10">
        <input type="text" id="daterange_ris" class="form-control">
    </div>
</div>

<div class="row mt-3">
    <div class="table-responsive">
        <table class="table text-center w-100 text-nowrap table-sm" id="table-ris">
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
                </tr>
                <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                    <th></th>
                    <th>No</th>
                    <th>Status</th>
                    <th>No Transaksi</th>
                    <th>No Referensi</th>
                    <th>Tanggal</th>
                    <th>Site Storage</th>
                    <th>Main Storage</th>
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
        togglePeriodRis();

        $('#statusAllRis, input[name="status_ris"], #check_period_ris, #daterange_ris').change(function() {
            ris.ajax.reload();
        });

        $('#statusAllRis').on('change', function() {
            if ($(this).is(':checked')) {
                $('input[name="status_ris"]')
                    .prop('checked', false)
                    .prop('disabled', true);

                $('#card-status-ris').css('background-color', '#EFF2F7');
            } else {
                $('input[name="status_ris"]')
                    .prop('disabled', false);

                $('input[name="status_ris"][value="NEW"]')
                    .prop('checked', true);

                $('#card-status-ris').css('background-color', '');
            }

            ris.ajax.reload();
        });

        let start = moment().startOf('month');
        let end = moment().endOf('month');

        $('#daterange_ris').daterangepicker({
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

        updateDisplayRis(start, end);

        $('#daterange_ris').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(
                picker.startDate.format('YYYY-MM-DD') +
                ' - ' +
                picker.endDate.format('YYYY-MM-DD')
            );

            ris.ajax.reload();
        });

        $('#daterange_ris').prop('disabled', true);

        $('#daterange_ris').on('keydown keypress paste input', function(e) {
            e.preventDefault();
        });

        $('#check_period_ris').on('change', function() {
            togglePeriodRis();
            ris.ajax.reload();
        });

        var ris = $('#table-ris').DataTable({
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
                        status: $('input[name="status_ris"]:checked').val(),
                        check_status: $('#statusAllRis').is(':checked'),
                        check_period: $('#check_period_ris').is(':checked'),
                        daterange: $('#daterange_ris').val(),
                        search_global: ris.search()
                    };

                    ris.columns().every(function(index) {
                        const searchValue = this.search();

                        if (searchValue) {
                            params['columns[' + index + '][search][value]'] = searchValue;
                        }
                    });

                    const order = ris.order();
                    if (order.length) {
                        params['order[0][column]'] = order[0][0];
                        params['order[0][dir]'] = order[0][1];
                    }

                    window.location.href =
                        "<?= site_url('out_kny/export_receive_in_site') ?>?" + $.param(params);
                }
            }],
            ajax: {
                url: "<?= site_url('out_kny/get_data_receive_in_site'); ?>",
                type: "POST",
                data: function(d) {
                    d.status = $('input[name="status_ris"]:checked').val();
                    d.check_status = $('#statusAllRis').is(':checked');
                    d.check_period = $('#check_period_ris').is(':checked');
                    d.daterange = $('#daterange_ris').val();
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
            ],
            columnDefs: [{
                    className: 'text-center',
                    targets: [1, 2, 5],
                },
                {
                    orderable: false,
                    targets: [0],
                },
            ]
        });

        $('#table-ris tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = ris.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var tag_id = rowData[8];

            if (row.child.isShown()) {
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                var childTableId = 'child-ris-' + tag_id;

                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                <tr class="align-middle">
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kode Item</th>
                                    <th>Jumlah</th>
                                    <th>Pakai/Retur</th>
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
                        "url": "<?= site_url('out_kny/get_detail_receive_in_site'); ?>",
                        "type": "POST",
                        "data": {
                            tag_id: tag_id
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
                            "render": renderLimitRis,
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "kode_item",
                            "render": renderLimitRis,
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
                            "data": "pakai_retur",
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
                            "render": renderLimitRis,
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "note",
                            "render": renderLimitRis,
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

        $('#table-ris thead').on('input', '.column_search', debounceRis(function() {
            let index = $(this).parent().index();
            ris.column(index).search(this.value).draw();
        }, 1000));
    });

    function updateDisplayRis(start, end) {
        $('#startDate').text(start.format('YYYY-MM-DD'));
        $('#endDate').text(end.format('YYYY-MM-DD'));
    }

    function togglePeriodRis() {
        const drp = $('#daterange_ris').data('daterangepicker');

        if ($('#check_period_ris').is(':checked')) {
            $('#daterange_ris')
                .val('')
                .prop('disabled', true);

            if (drp) {
                drp.setStartDate(moment());
                drp.setEndDate(moment());
            }
        } else {
            $('#daterange_ris')
                .prop('disabled', false);

            if (drp) {
                let start = moment().startOf('month');
                let end = moment().endOf('month');

                drp.setStartDate(start);
                drp.setEndDate(end);

                $('#daterange_ris').val(
                    start.format('YYYY-MM-DD') +
                    ' - ' +
                    end.format('YYYY-MM-DD')
                );

                updateDisplayRis(start, end);
            }
        }
    }

    function renderLimitRis(data) {
        if (!data) return '';

        const limit = 30;
        if (data.length > limit) {
            return `<span title="${data.replace(/"/g, '"')}">
                ${data.substring(0, limit)}...
            </span>`;
        }

        return data;
    }

    function debounceRis(func, delay) {
        let timer;
        return function() {
            let context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(context, args), delay);
        };
    }
</script>