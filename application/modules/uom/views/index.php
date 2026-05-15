<style>
    .dt-buttons .btn {
        background-color: #0d6efd;
        /* warna biru Bootstrap primary */
        border-color: #0d6efd;
        color: white;
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
                                <table class="table text-center w-100 table-sm" id="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <?php for ($i=1; $i <=2 ; $i++) { ?> 
                                                <th>
                                                    <input type="text" placeholder="Cari.." class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                </th>
                                            <?php } ?>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="3" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="4" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="5" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                        </tr>
                                        <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                            <th>No</th>
                                            <th>Satuan</th>
                                            <th>Deskripsi</th>
                                            <th>Base Flag</th>
                                            <th>Primary Flag</th>
                                            <th>Active Flag</th>
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
        let columns = [
            {
                "data": "no",
                "orderable": false,
                "searchable": false,
                "className" : "text-center",
                "width" : "30px"
            },
            {
                "data": "name",
                "width" : "150px"
            },
            {
                "data": "description",
            },
            {
                "data": "base_flag",
                "className" : "text-center",
                "width" : "70px"
            },
            {
                "data": "primary_flag",
                "className" : "text-center",
                "width" : "80px"
            },
            {
                "data": "active_flag",
                "className" : "text-center",
                "width" : "70px"
            },
        ];
        var table = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>frtip',
            buttons: getButtons(<?= json_encode(button_actions(['insert',
                [
                    'key'      => 'import',
                    'redirect' => site_url('uom/import'),
                    'class'    => 'btn-success',
                    'title'    => 'Import',
                    'icon'     => 'ri-file-upload-line',
                    'needs_auth' => true,
                ],
            ], 'dt')) ?>),
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('uom/get_data'); ?>",
                "type": "POST"
            },
            "columns": columns
        });
        
        $('.column_search').on('keyup change', function() {
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
        });
    });
</script>