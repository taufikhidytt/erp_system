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
                                            <?php for ($i=1; $i <=5 ; $i++) { ?> 
                                                <th>
                                                    <input type="text" placeholder="Cari.." class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                </th>
                                            <?php } ?>
                                        </tr>
                                        <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                            <th>No</th>
                                            <th>Nama User</th>
                                            <th>Nama Lengkap</th>
                                            <th>Group</th>
                                            <th>Divisi</th>
                                            <th>Jabatan</th>
                                            <?php if(isset($access['assign_database']) && $access['assign_database']){
                                                echo '<th width="50" data-assign_db>Aksi</th>';
                                            } ?>
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
                "className" : "text-center"
            },
            {
                "data": "name",
            },
            {
                "data": "full_name",
            },
            {
                "data": "group_name",
            },
            {
                "data": "divisi",
            },
            {
                "data": "title",
            },
        ];
        if($('#table thead tr th[data-assign_db]').length > 0){
            columns.push({
                "data": "action",
                "orderable": false,
                "searchable": false,
                "className" : "text-center"
            });
        }
        var table = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>frtip',
            buttons: getButtons(<?= json_encode(button_actions(['insert'], 'dt')) ?>),
            "autoWidth": true,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('user/get_data'); ?>",
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