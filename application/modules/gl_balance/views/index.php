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
                        <div class="row form-xs">
                            <div class="col-xxl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="periode_awal" class="form-label">Periode Awal</label>
                                    <span class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-calendar-fill"></i>
                                        </span>
                                        <input type="hidden" id="period" value="<?= base64url_encode($this->encrypt->encode($period->PERIOD_NAME)) ?>">
                                        <input type="text" class="form-control" id="periode_awal" value="<?= $period->PERIOD_NAME ?>" disabled>
                                    </div>
                                    <div class="text-danger"><?= $period->OPEN_FLAG !== 'Y'?'Stok & HPP Awal tidak bisa diubah karena periode awal sudah ditutup.':'' ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" id="d-table">
                            <table class="table text-center w-100 table-sm" id="table" data-info="true">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php for ($i=1; $i <=6 ; $i++) { ?> 
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search<?= in_array($i,[3,4,6])?' input-number text-end':'' ?>" data-allow-negatif="true" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                        <?php } ?>
                                    </tr>
                                    <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                        <th>No</th>
                                        <th>Kode Account</th>
                                        <th>Nama Account</th>
                                        <th>Saldo Awal</th>
                                        <th>Kurs</th>
                                        <th>Mata Uang</th>
                                        <th>Saldo Awal (IDR)</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container-fluid -->
</div>

<link href="<?= base_url() ?>assets/admin/libs/inline-editor/inline-editor.css?v=<?= $version['inline-editor'] ?>" rel="stylesheet" type="text/css" />
<script src="<?= base_url() ?>assets/admin/libs/inline-editor/inline-editor.js?v=<?= $version['inline-editor'] ?>"></script>
<script>
    let table = null;
    let editor= null;

    $(document).ready(function() {
        table = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>rtip',
            buttons: getButtons(<?= json_encode(button_actions([
                [
                    'key'      => 'import',
                    'redirect' => site_url('gl_balance/import'),
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
                            't'     => 'coa_balance',
                            'type'  => 'by_one',
                            'id'    => 'COA_BALANCE_ID',

                        ],
                        'where' => [
                            'TABLE_NAME'    => 'COA_BALANCE',
                        ],
                    ]))),
                ],
            ], 'dt')) ?>),
            processing: true,
            serverSide: true,
            searching: true,
            order: [],
            scrollX:true,
            scrollY:'47dvh',
            scrollCollapse: true,
            drawCallback: function() {
                this.api().columns.adjust();
            },
            ajax: {
                url: "<?= base_url('gl_balance/get_data') ?>",
                type: "POST",
                data: function(d) {
                    d.period        = $('#period').val();
                }
            },
            columns: [
                { data: 'no', orderable: false, searchable: false, className: 'text-center' },
                { data: 'code' },
                { data: 'name', className: 'text-start' },
                { data: 'initial_balance', className: 'text-end' },
                { data: 'kurs', className: 'text-end' },
                { data: 'mata_uang', width:'50px'},
                { data: 'initial_balance_kurs', className: 'text-end' }
            ],
        });

        editor = InlineEditor.init({
            table : table,
            edit  : <?= $access['update'] && $period->OPEN_FLAG === 'Y'?'true':'false' ?>,
            urls  : {
                save : '<?= site_url('gl_balance/save') ?>',
            },
            fields: [
                { field: 'code',  type: 'text', inputable:false},
                { field: 'name',  type: 'text', inputable:false},
                { field: 'initial_balance', type: 'text', required: true, label: 'Stok Awal', 
                    attrs: { 
                        class : 'input-number text-end',
                        'data-allow-negative' : true
                    }
                },
                { field: 'kurs', type: 'text', inputable: false, label: 'Hpp Awal', 
                    attrs: { 
                        min: 0,
                        class : 'input-number text-end'
                    }
                },
                { field: 'mata_uang',  type: 'text', inputable:false},
                { field: 'initial_balance_kurs', inputable : false,
                    compute: function(vals) {
                        const initial_balance = $.inputNumber.unformat(vals.initial_balance);
                        const kurs   = $.inputNumber.unformat(vals.kurs);
                        return $.inputNumber.format(initial_balance * kurs);
                    }
                },
            ],
        });

        editor.setExtraData({
            period: $('#period').val()
        });

        $(document).on('input', '.column_search', function() {
            if (table) {
                let colIdx = $(this).data('column');
                table.column(colIdx).search(this.value).draw();
            }
        });
    });
</script>