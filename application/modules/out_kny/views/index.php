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

    .font-mono {
        font-family: monospace !important;
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
                                <table class="table table-striped text-center w-100" id="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>
                                                <input type="date" placeholder="Cari.." class="column_search" data-column="1" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
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
                                        </tr>
                                        <tr class="align-content-center">
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>No Transaksi</th>
                                            <th>No Referensi</th>
                                            <th>Supplier</th>
                                            <th>Nama Item</th>
                                            <th>Kode Item</th>
                                            <th>Qty MR</th>
                                            <th>Qty PO</th>
                                            <th>Sisa</th>
                                            <th>Satuan</th>
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
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('out_kny/get_data'); ?>",
                "type": "POST"
            },
            "dom": "<'row'<'col-md-6'l><'col-md-6 text-end'B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                text: 'Export Excel',
                className: 'btn btn-success',
                action: function(e, dt, node, config) {
                    var params = dt.ajax.params();
                    window.open("<?= site_url('out_kny/export?') ?>" + $.param(params), '_blank');
                }
            }],
            "columns": [{
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                },
                {
                    "data": "tanggal",
                    "width": "10%",
                },
                {
                    "data": "no_transaksi",
                    "width": "10%",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
                {
                    "data": "no_referensi",
                    "width": "10%",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
                {
                    "data": "supplier",
                    "width": "10%",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
                {
                    "data": "nama_item",
                    "width": "10%",
                    "className": "elipsis",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
                {
                    "data": "kode_item",
                    "width": "10%",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
                {
                    "data": "qty_mr",
                    "width": "10%",
                    "className": "text-end",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
                {
                    "data": "qty_po",
                    "width": "10%",
                    "className": "text-end",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
                {
                    "data": "sisa",
                    "width": "10%",
                    "className": "text-end",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
                {
                    "data": "satuan",
                    "width": "10%",
                    "render": function(data, type, row) {
                        if (!data) return '';

                        const limit = 20;
                        if (data.length > limit) {
                            return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                        }
                        return data;
                    },
                },
            ]
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
</script>