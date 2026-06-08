<style>
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
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#info_outStd_mr_po" role="tab" aria-selected="true">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Info OutStd (MR-PO)"><i class="ri ri-stock-fill"></i></span>
                                    <span class="d-none d-sm-block">Info OutStd (MR-PO)</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#info_outStd_mr_so" role="tab" aria-selected="false">
                                    <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Info OutStd (MR-SO)"><i class="ri ri-file-paper-2-fill"></i></span>
                                    <span class="d-none d-sm-block">Info OutStd (MR-SO)</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content p-3 text-muted">
                            <div class="tab-pane active" id="info_outStd_mr_po" role="tabpanel">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center w-100 table-sm" id="table_mr_po">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th>
                                                        <input type="date" placeholder="Cari.." class="column_search_mr_po" data-column="1" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="2" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="3" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="4" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="5" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="6" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="7" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="8" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="9" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="10" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search_mr_po" data-column="11" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                </tr>
                                                <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>No Transaksi</th>
                                                    <th>No Referensi</th>
                                                    <th>Storage</th>
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
                            <div class="tab-pane" id="info_outStd_mr_so" role="tabpanel">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center w-100 table-sm" id="table">
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
                                                    <th>
                                                        <input type="text" placeholder="Cari.." class="column_search" data-column="11" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    </th>
                                                </tr>
                                                <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>No Transaksi</th>
                                                    <th>No Referensi</th>
                                                    <th>Storage</th>
                                                    <th>Customer</th>
                                                    <th>Nama Item</th>
                                                    <th>Kode Item</th>
                                                    <th>Qty MR</th>
                                                    <th>Qty SO</th>
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
                "url": "<?= site_url('out_so/get_data'); ?>",
                "type": "POST"
            },
            "dom": "<'row'<'col-md-6'l><'col-md-6 text-end'B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                text: '<i class="fas fa-file-excel me-1"></i> Export Excel',
                action: function(e, dt, node, config) {
                    var params = dt.ajax.params();
                    window.open("<?= site_url('out_so/export?') ?>" + $.param(params), '_blank');
                },
                attr: {
                    class: 'btn btn-primary btn-sm'
                }
            }],
            "columns": [{
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                    "className": "text-center"
                },
                {
                    "data": "tanggal",
                    "width": "10%",
                    "searchable": true,
                    "className": "text-center"
                },
                {
                    "data": "no_transaksi",
                    "width": "10%",
                    "searchable": true,
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
                    "searchable": true,
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
                    "data": "storage",
                    "width": "10%",
                    "searchable": true,
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
                    "searchable": true,
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
                    "searchable": true,
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
                    "searchable": true,
                    "className": "text-center",
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
                    "searchable": true,
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
                    "data": "qty_so",
                    "width": "10%",
                    "searchable": true,
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
                    "searchable": true,
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
                    "searchable": true,
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

        $('.column_search').on('input', function() {
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
        });


        var table_mr_po = $('#table_mr_po').DataTable({
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('out_so/get_data_mr_po'); ?>",
                "type": "POST"
            },
            "dom": "<'row'<'col-md-6'l><'col-md-6 text-end'B>>" +
                "<'row'<'col-md-12'tr>>" +
                "<'row'<'col-md-5'i><'col-md-7'p>>",
            "buttons": [{
                text: '<i class="fas fa-file-excel me-1"></i> Export Excel',
                action: function(e, dt, node, config) {
                    var params = dt.ajax.params();
                    window.open("<?= site_url('out_so/export_mr_po?') ?>" + $.param(params), '_blank');
                },
                attr: {
                    class: 'btn btn-primary btn-sm'
                }
            }],
            "columns": [{
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                    "className": "text-center"
                },
                {
                    "data": "tanggal",
                    "width": "10%",
                    "className": "text-center"
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
                    "data": "storage",
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
                    "className": "text-center",
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

        $('.column_search_mr_po').on('input', function() {
            table_mr_po
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