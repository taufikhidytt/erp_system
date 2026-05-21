<style>
    .dt-buttons .btn-primary {
        background-color: #0d6efd;
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
                                            <?php for ($i=1; $i <=3 ; $i++) { ?> 
                                                <th>
                                                    <input type="text" placeholder="Cari.." class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                </th>
                                            <?php } ?>
                                            <th></th>
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
                                            <th>Satuan Lain</th>
                                            <th>Satuan Utama</th>
                                            <th>Nilai Konversi</th>
                                            <th>Keterangan</th>
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
                "data": "from_uom",
            },
            {
                "data": "to_uom",
            },
            {
                "data": "to_qty",
                "className" : "text-end",
            },
            {
                "data": "keterangan",
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
            ], 'dt')) ?>),
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('konversi/get_data'); ?>",
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
                save : '<?= site_url('konversi/save') ?>',
            },
            fields: [
                { field: 'from_uom',     type: 'select2', required: true, label: 'Satuan Lain-lain', 
                    select2: {
                        url         : '/api/get_uom',
                        placeholder : 'Pilih Satuan',
                    },
                    attrs: {
                        'data-dropdown-parent' : 'body' 
                    }
                },
                { field: 'to_uom',       type: 'select2',required: true, label: 'Satuan Utama', 
                    select2: {
                        url         : '/api/get_uom',
                        placeholder : 'Pilih Satuan',
                    },
                    attrs: {
                        'data-dropdown-parent' : 'body' 
                    }
                },
                { field: 'to_qty',       type: 'text', required: true, label: 'Nilai Konversi', 
                    attrs: { 
                        min: 1,
                        class : 'input-number text-end'
                    }
                },
                { field: 'keterangan', inputable : false,
                    compute: function(vals) {
                        const from = vals.from_uom?.label ?? '';
                        const to   = vals.to_uom?.label   ?? '';
                        const qty  = vals.to_qty          ?? 1;
                        return $.inputNumber.format(1) + ' ' + from + ' = ' + $.inputNumber.format(qty) + ' ' + to;
                    }
                },
                { field: 'active_flag',  type: 'checkbox', value: 'Y'},
            ],
        });
    });
</script>
