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

    /* Paksa width kolom "Nama Item" */
    #table th:nth-child(4),
    #table td:nth-child(4) {
        width: 500px !important;
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
</style>

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
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table text-center table-sm" id="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="1" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th style="width: 80%;">
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="2" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="3" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="4" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="5" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="6" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="7" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="8" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="9" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="10" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="11" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="12" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="13" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="14" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="15" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="16" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                        </tr>
                                        <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                            <th></th>
                                            <th>No</th>
                                            <th>Kode Item</th>
                                            <th>Nama Item</th>
                                            <th>Part Number</th>
                                            <th>UoM</th>
                                            <th>Jenis</th>
                                            <th>Kategori</th>
                                            <th>Komoditi</th>
                                            <th>Brand</th>
                                            <th>Trade</th>
                                            <th>Last Buy</th>
                                            <th>Last Sell</th>
                                            <th>Lead Time</th>
                                            <th>Made In</th>
                                            <th>Konsy</th>
                                            <th>Approved</th>
                                            <th>Obsolete</th>
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
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<script>
    $(document).ready(function() {
        var table = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>frtip',
            buttons: [{
                text: '<i class="ri ri-add-circle-fill"></i> Tambah Item',
                className: 'btn btn-sm btn-primary',
                action: function(e, dt, node, config) {
                    window.location.href = "<?= base_url('item/add') ?>";
                }
            }],
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('item/get_data'); ?>",
                "type": "POST"
            },
            "columnDefs": [{
                "width": "500px",
                "targets": [3, 4],
            }, {
                "targets": [0,1],
                "orderable": false,
                "searchable": false
            }],
            "columns": [
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": '<i class="ri ri-add-line" style="cursor:pointer"></i>'
                },
                {
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center"
                },
                {
                    "data": "kode_item",
                    "className": "text-center"
                },
                {
                    "data": "nama_item"
                },
                {
                    "data": "part_number"
                },
                {
                    "data": "uom"
                },
                {
                    "data": "jenis"
                },
                {
                    "data": "kategori"
                },
                {
                    "data": "komoditi"
                },
                {
                    "data": "brand"
                },
                {
                    "data": "trade"
                },
                {
                    "data": "price_last_buy"
                },
                {
                    "data": "price_last_sell"
                },
                {
                    "data": "lead_time"
                },
                {
                    "data": "made_in"
                },
                {
                    "data": "konsy",
                    "className": "text-center",
                },
                {
                    "data": "approved",
                    "className": "text-center",
                },
                {
                    "data": "status",
                    "className": "text-center",
                }
            ]
        });

        $('#table tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var item_id = rowData.item_id;

            if (row.child.isShown()) {
                // Close row
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                // Open row dengan child row datatable
                var childTableId = 'child-' + item_id;
                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
                                <tr class="align-middle">
                                    <th>No</th>
                                    <th>Satuan Lain</th>
                                    <th>Konversi</th>
                                    <th>Keterangan</th>
                                    <th>Default</th>
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
                        "url": "<?= site_url('item/get_detail'); ?>",
                        "type": "POST",
                        "data": {
                            item_id: item_id,
                            uom : rowData.uom
                        }
                    },
                    "columns": [{
                            "data": "no",
                            width: "30",
                            className : "text-center",
                        },
                        {
                            "data": "satuan_lain",
                        },
                        {
                            "data": "konversi",
                            width: "100",
                            className : "text-end",
                        },
                        {
                            "data": "keterangan",
                        },
                        {
                            "data": "flag_default",
                            width: "100",
                            "className": "text-center",
                        }
                    ],
                    "paging": true,
                    "searching": false,
                    "ordering": false,
                    "info": true,
                    "autoWidth": true
                });
            }
        });

        $('.column_search').on('keyup change', function() {
            // let i = $(this).data('column');
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
        });

        $(document).on('click', '#btn-approve', function(e) {
            e.preventDefault();
            var link = $(this).parent('form');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin approve data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#43a700ff',
                cancelButtonColor: '#ff0022ff',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    link.submit();
                }
            })
        });
    });
</script>