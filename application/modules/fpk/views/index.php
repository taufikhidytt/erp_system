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
        height: 30px !important;
        min-height: 30px !important;
        padding-top: 12px !important;
        padding-bottom: 1px !important;
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
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-striped text-center" id="table">
                                    <thead>
                                        <tr>
                                            <th>
                                            </th>
                                            <th>
                                            </th>
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
                                                <input type="date" placeholder="Cari.." class="column_search" data-column="4" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="date" placeholder="Cari.." class="column_search" data-column="5" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
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
                                        </tr>
                                        <tr class="align-content-center">
                                            <th></th>
                                            <th>No</th>
                                            <th>Status</th>
                                            <th>No Transaksi</th>
                                            <th>No Referensi</th>
                                            <th>Tanggal</th>
                                            <th>Tanggal Dibutuhkan</th>
                                            <th>Supplier</th>
                                            <th>Gudang</th>
                                            <th>Total</th>
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
                text: '<i class="ri ri-add-circle-fill"></i> Tambah',
                className: 'btn btn-sm btn-primary',
                action: function(e, dt, node, config) {
                    window.location.href = "<?= base_url('fpk/add') ?>";
                }
            }],
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('fpk/get_data'); ?>",
                "type": "POST"
            },
            "columns": [{
                    "className": 'details-control',
                    "orderable": false,
                    "data": null,
                    "defaultContent": '<i class="ri ri-add-line" style="cursor:pointer"></i>'
                },
                {
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                },
                {
                    "data": "status"
                },
                {
                    "data": "no_transaksi"
                },
                {
                    "data": "no_referensi"
                },
                {
                    "data": "tanggal"
                },
                {
                    "data": "tanggal_dibutuhkan"
                },
                {
                    "data": "supplier",
                    "render": function(data) {
                        if (!data) return '-';
                        return `<span title="${data}">${data}</span>`;
                    }
                },
                {
                    "data": "gudang"
                },
                {
                    "data": "total",
                    "className": "text-end",
                }
            ]
        });

        // Add event listener for opening and closing details
        $('#table tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var prId = rowData.pr_id;

            if (row.child.isShown()) {
                // Close row
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                // Open row dengan child row datatable
                var childTableId = 'child-' + prId;
                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Item</th>
                                    <th>Kode Item</th>
                                    <th>Jumlah</th>
                                    <th>Satuan</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                    <th>Keterangan</th>
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
                        "url": "<?= site_url('fpk/get_detail'); ?>",
                        "type": "POST",
                        "data": {
                            pr_id: prId
                        }
                    },
                    "columns": [{
                            "data": "no"
                        },
                        {
                            "data": "item"
                        },
                        {
                            "data": "item_code"
                        },
                        {
                            "data": "qty"
                        },
                        {
                            "data": "entered_uom"
                        },
                        {
                            "data": "price",
                            "className": "text-end",
                        },
                        {
                            "data": "total",
                            "className": "text-end",
                        },
                        {
                            "data": "note"
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
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
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

    function dataDetail(details) {
        // Sesuaikan format detail di sini
        return `
            <table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
                ${details.map(d => `
                    <tr>
                        <td>${d.item}</td>
                        <td>${d.qty}</td>
                        <td>${d.price}</td>
                        <td>${d.total}</td>
                    </tr>
                `).join('')}
            </table>
        `;
    }
</script>