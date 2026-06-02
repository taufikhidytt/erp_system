<style>
    .dt-buttons .btn-primary {
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
                                        </tr>
                                        <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                            <th>No</th>
                                            <th>Satuan</th>
                                            <th>Deskripsi</th>
                                            <th>Default</th>
                                            <th>Aktif</th>
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

<link href="<?= base_url() ?>assets/admin/libs/inline-editor/inline-editor.css?v=<?= $version['inline-editor'] ?>" rel="stylesheet" type="text/css" />
<script src="<?= base_url() ?>assets/admin/libs/inline-editor/inline-editor.js?v=<?= $version['inline-editor'] ?>"></script>
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
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>rtip',
            buttons: getButtons(<?= json_encode(button_actions([
                [
                    'key'      => 'insert',
                    'class'    => 'btn-insert btn-primary',
                    'url'      => '',
                    'raw_url'  => true
                ],
                [
                    'key'      => 'import',
                    'redirect' => site_url('uom/import'),
                    'class'    => 'btn-success',
                    'title'    => 'Import',
                    'icon'     => 'ri-file-upload-line',
                    'needs_auth' => true,
                ],
                [
                    'key'          => 'log_info',
                    'class'        => 'btn-info btn-log-info',
                    'title'        => 'Log & History',
                    'icon'         => 'ri-question-line',
                    'data-param'   => base64_encode($this->encrypt->encode(json_encode([
                        'h' => [
                            't'     => 'uom',
                            'type'  => 'by_one',

                        ],
                        'where' => [
                            'TABLE_NAME'    => 'UOM',
                        ],
                    ]))),
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
        
        $('.column_search').on('input', function() {
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
        });

        const editor = InlineEditor.init({
            table : table,
            add   : <?= $access['insert']?'true':'false' ?>,
            edit  : <?= $access['update']?'true':'false' ?>,
            urls  : {
                save : '<?= site_url('uom/save') ?>',
            },
            fields: [
                { field: 'name',         type: 'text', maxlength: 18, required: true, label: 'Satuan', editable: false,
                    attrs: { 
                        oninput: "let p = this.selectionStart; this.value = this.value.toUpperCase(); this.setSelectionRange(p, p);"
                    }
                },
                { field: 'description',  type: 'text', maxlength:80, required: true, label : 'Deskripsi',
                    attrs: { 
                        oninput: "let p = this.selectionStart; this.value = this.value.toUpperCase(); this.setSelectionRange(p, p);"
                    }
                },
                { field: 'primary_flag', type: 'checkbox', exclusive: true },
                { field: 'active_flag',  type: 'checkbox', value: 'Y'},
            ],
        });
        console.log(editor);
    });
</script>