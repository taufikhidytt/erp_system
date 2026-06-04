<style>
    .dt-buttons .btn-primary {
        background-color: #0d6efd;
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
                                <table class="table text-center w-100 table-sm align-middle" id="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="1" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="2" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="3" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="4" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;min-height:35.66px;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="5" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;min-height:35.66px;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="6" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;min-height:35.66px;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th></th>
                                        </tr>
                                        <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                            <th>No</th>
                                            <th>SEQ</th>
                                            <th>Menu Name</th>
                                            <th>Prompt</th>
                                            <th>Active Flag</th>
                                            <th>Document No</th>
                                            <th>PPN</th>
                                            <th>Action</th>
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
            buttons: getButtons(<?= json_encode(button_actions(['insert',[
                    'key'      => 'urutan',
                    'redirect' => site_url('management_menu/sort'),
                    'class'    => 'btn-success',
                    'title'    => 'Urutan',
                    'icon'     => 'ri-sort-asc',
                    'needs_auth'=> true,
                ],], 'dt')) ?>),
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [[ 1, "asc" ]],
            "ajax": {
                "url": "<?= site_url('management_menu/get_data'); ?>",
                "type": "POST"
            },
            "columns": [
                {
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                    "className" : "text-center",
                },
                {
                    "data": "seq",
                    "width": "5%",
                    "className" : "text-center",
                },
                {
                    "data": "name",
                },
                {
                    "data": "prompt",
                },
                {
                    "data": "active_flag",
                    "className" : "text-center",
                    "width": "5%",
                },
                {
                    "data": "document_no",
                    "className" : "text-center",
                    "width": "5%",
                },
                {
                    "data": "ppn",
                    "className" : "text-center",
                    "width": "5%",
                },
                {
                    "data": "action",
                    "width": "5%",
                    "className" : "text-center",
                    "orderable": false,
                    "searchable": false,
                },
            ]
        });

        $('.column_search').on('input', function() {
            // let i = $(this).data('column');
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
        });
    });
</script>