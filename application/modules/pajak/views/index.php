<style>
    .dt-buttons .btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
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
    /* Cari tag select yang punya class .select2 DAN memiliki atribut data-lebar="minimal" */
    select.select2[data-width="minimal"] + .select2-container {
        min-width: 333px !important;
        width: auto !important; /* Mematikan kalkulasi lebar otomatis dari JS */
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
                                    <thead class="align-middle">
                                        <tr>
                                            <th></th>
                                            <?php for ($i=1; $i <=10 ; $i++) { ?> 
                                                <th>
                                                    <?php if($i>7){ ?>
                                                        <select class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                            <option value="">All</option>
                                                            <option value="Y">✔</option>
                                                            <option value="N">✖</option>
                                                        </select>
                                                    <?php } else { ?>
                                                        <input type="text" placeholder="Cari.." class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    <?php } ?>
                                                </th>
                                            <?php } ?>
                                        </tr>
                                        <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Kode Pajak</th>
                                            <th rowspan="2">Persen</th>
                                            <th rowspan="2">Tipe Pajak</th>
                                            <th colspan="2" class="text-center">Account Penjualan</th>
                                            <th colspan="2" class="text-center">Account Pembelian</th>
                                            <th colspan="2" class="text-center">Default</th>
                                            <th rowspan="2">Aktif</th>
                                        </tr>
                                        <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>PPN</th>
                                            <th>PPH</th>
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
                "data": "kode_pajak",
            },
            {
                "data": "persen",
                "className" : "text-end"
            },
            {
                "data": "tipe_pajak",
            },
            {
                "data": "kode_account_penjualan",
                "className" : "text-center",
            },
            {
                "data": "nama_account_penjualan",
            },
            {
                "data": "kode_account_pembelian",
                "className" : "text-center"
            },
            {
                "data": "nama_account_pembelian",
            },
            {
                "data": "ppn",
                "className" : "text-center",
                "width" : "70px"
            },
            {
                "data": "pph",
                "className" : "text-center",
                "width" : "70px"
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
                    'key'          => 'log_info',
                    'class'        => 'btn-info btn-log-info',
                    'title'        => 'Log & History',
                    'icon'         => 'ri-question-line',
                    'data-param'   => base64_encode($this->encrypt->encode(json_encode([
                        'h' => [
                            't'     => 'ppn',
                            'type'  => 'by_one',
                            'id'    => 'PPN_CODE',

                        ],
                        'where' => [
                            'TABLE_NAME'    => 'PPN',
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
                "url": "<?= site_url('pajak/get_data'); ?>",
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
                save : '<?= site_url('pajak/save') ?>',
            },
            fields: [
                { field: 'kode_pajak',         type: 'text', maxlength: 10, required: true, label: 'Kode Pajak', editable: false,
                    attrs: { 
                        oninput: "let p = this.selectionStart; this.value = this.value.toUpperCase(); this.setSelectionRange(p, p);"
                    }
                },
                { field: 'persen',       type: 'text', required: true, label: 'Persen', 
                    attrs: { 
                        min: 0,
                        max: 100,
                        class : 'input-number text-end'
                    },
                    compute: function(vals) {
                        let persen  = vals.persen ?? 1;
                        if(!persen) return;
                        persen = $.inputNumber.unformat(persen);
                        if(persen>100){
                            persen = 100;
                            Swal.fire('Peringatan', `Persen tidak boleh lebih dari 100.`, 'warning');
                        }
                        return $.inputNumber.format(persen);
                    }
                },
                { field: 'tipe_pajak',     type: 'select2', required: true, label: 'Tipe Pajak', 
                    select2: {
                        url         : '/api/get_tipe_pajak',
                        placeholder : 'Pilih Tipe Pajak',
                    },
                    attrs: {
                        'data-dropdown-parent' : 'body' 
                    },
                },
                { field: 'kode_account_penjualan',     type: 'select2', required: true, label: 'Kode Account Penjualan', 
                    select2: {
                        url         : '/api/get_coa',
                        placeholder : 'Pilih Kode Account',
                        labelFn: row => row.code,
                        syncTo      : 'nama_account_penjualan',
                    },
                    attrs: {
                        'data-dropdown-parent' : 'body',
                        'data-width' : 'minimal'
                    },
                },
                { field: 'nama_account_penjualan',       type: 'select2',required: true, label: 'Nama Account Penjualan', 
                    select2: {
                        url         : '/api/get_coa',
                        placeholder : 'Pilih Nama Account',
                        labelFn: row => row.name,
                        syncTo      : 'kode_account_penjualan',
                    },
                    attrs: {
                        'data-dropdown-parent' : 'body',
                        'data-width' : 'minimal'
                    }
                },
                { field: 'kode_account_pembelian',     type: 'select2', required: true, label: 'Kode Account Pembelian', 
                    select2: {
                        url         : '/api/get_coa',
                        placeholder : 'Pilih Kode Account',
                        labelFn: row => row.code,
                        syncTo      : 'nama_account_pembelian',
                    },
                    attrs: {
                        'data-dropdown-parent' : 'body',
                        'data-width' : 'minimal'
                    }
                },
                { field: 'nama_account_pembelian',       type: 'select2',required: true, label: 'Nama Account Pembelian', 
                    select2: {
                        url         : '/api/get_coa',
                        placeholder : 'Pilih Nama Account',
                        labelFn: row => row.name,
                        syncTo      : 'kode_account_pembelian',
                    },
                    attrs: {
                        'data-dropdown-parent' : 'body',
                        'data-width' : 'minimal'
                    }
                },
                { field: 'ppn',  type: 'checkbox', value: 'N', exclusive: true},
                { field: 'pph',  type: 'checkbox', value: 'N', exclusive: true},
                { field: 'active_flag',  type: 'checkbox', value: 'Y'},
            ],
        });
    });
</script>
