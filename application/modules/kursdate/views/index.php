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
                                            <?php for ($i=1; $i <=6 ; $i++) { ?> 
                                                <th>
                                                    <input type="<?= $i==3?'date':'text' ?>" value="<?= $i==3?date('Y-m-d'):'' ?>" placeholder="Cari.." class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                </th>
                                            <?php } ?>
                                        </tr>
                                        <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Tanggal</th>
                                            <th>Rate</th>
                                            <th>Keterangan</th>
                                            <th>User</th>
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
                "data": "mata_uang_code",
                "className" : "text-center",
                "width" : "80px"
            },
            {
                "data": "mata_uang_name",
            },
            {
                "data": "document_date",
                "className" : "text-center",
                "width" : "100px"
            },
            {
                "data": "nilai",
                "className" : "text-end",
            },
            {
                "data": "note",
            },
            {
                "data": "user_name",
                "className" : "text-center",
            },
        ];
        var table = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>rtip',
            buttons: getButtons(<?= json_encode(button_actions([
                [
                    'key'      => 'sync',
                    'redirect' => site_url('kursdate/sync'),
                    'class'    => 'btn-success',
                    'title'    => 'Sync',
                    'icon'     => 'ri-refresh-fill',
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
                "url": "<?= site_url('kursdate/get_data'); ?>",
                "type": "POST"
            },
            "columns": columns
        });
        
        $('.column_search').on('input', function() {
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
        });

        $('input[data-column="3"]').trigger('input');
    });
</script>
