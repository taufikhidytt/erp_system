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
                                    <label for="warehouse" class="form-label">Gudang</label>
                                    <span class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-building-fill"></i>
                                        </span>
                                        <input type="hidden" id="period" value="<?= base64url_encode($this->encrypt->encode($period->PERIOD_NAME)) ?>">
                                        <select name="warehouse" id="warehouse" class="form-control select2"
                                            data-url="item_balance/get_warehouse"
                                            data-default="Y"
                                            data-dropdown-parent="body"
                                            placeholder="Pilih Gudang">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="periode_awal" class="form-label">Periode Awal</label>
                                    <span class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-calendar-fill"></i>
                                        </span>
                                        <input type="text" class="form-control" id="periode_awal" value="<?= $period->PERIOD_NAME ?>" disabled>
                                    </div>
                                    <div class="text-danger"><?= $period->OPEN_FLAG !== 'Y'?'Stok & HPP Awal tidak bisa diubah karena periode awal sudah ditutup.':'' ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" id="d-table" style="display: none;">
                            <table class="table text-center w-100 table-sm" id="table" data-info="true">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <?php for ($i=1; $i <=6 ; $i++) { ?> 
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search<?= in_array($i,[4,5,6])?' input-number text-end':'' ?>" data-allow-negatif="true" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                        <?php } ?>
                                    </tr>
                                    <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                        <th>No</th>
                                        <th>Kode Item</th>
                                        <th>Nama Item</th>
                                        <th>Satuan</th>
                                        <th>Stok Awal</th>
                                        <th>Hpp Awal</th>
                                        <th>Sub Total</th>
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
        $(document).on('change', '#warehouse', function() {
            const val = $(this).val();
            
            if (val && val !== '__empty__') {
                $('#d-table').fadeIn(300);

                if (table === null) {
                    initDataTable();
                } else {
                    table.ajax.reload();
                    editor.setExtraData({
                        warehouse_id: $('#warehouse').val(),
                        period: $('#period').val()
                    });
                }

                setTimeout(() => {
                    $(document).find('.btn-log-info').attr('data-warehouse', val);
                }, 500);
            } else {
                $('#d-table').fadeOut(300);
            }
        });

        function initDataTable() {
            table = $('#table').DataTable({
                dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>rtip',
                buttons: getButtons(<?= json_encode(button_actions([
                    [
                        'key'      => 'import',
                        'redirect' => site_url('item_balance/import'),
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
                                't'     => 'item_balance',
                                'type'  => 'by_one',
                                'id'    => 'ITEM_BALANCE_ID',
                                'attr'  => ['warehouse' => 't1.WAREHOUSE_ID'],

                            ],
                            'where' => [
                                'TABLE_NAME'    => 'ITEM_BALANCE',
                            ],
                            'joins' => [
                                ['item_balance t1','a.ID = t1.ITEM_BALANCE_ID','inner']
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
                drawCallback: function() {
                    this.api().columns.adjust();
                },
                ajax: {
                    url: "<?= base_url('item_balance/get_data') ?>",
                    type: "POST",
                    data: function(d) {
                        d.warehouse_id  = $('#warehouse').val();
                        d.period        = $('#period').val();
                    }
                },
                columns: [
                    { data: 'no', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'item_code' },
                    { data: 'item_name', className: 'text-start' },
                    { data: 'satuan'},
                    { data: 'initial_stock', className: 'text-end' },
                    { data: 'initial_hpp', className: 'text-end' },
                    { data: 'subtotal', className: 'text-end' }
                ],
            });

            editor = InlineEditor.init({
                table : table,
                edit  : <?= $access['update'] && $period->OPEN_FLAG === 'Y'?'true':'false' ?>,
                urls  : {
                    save : '<?= site_url('item_balance/save') ?>',
                },
                fields: [
                    { field: 'item_code',  type: 'text', inputable:false},
                    { field: 'item_name',  type: 'text', inputable:false},
                    { field: 'satuan',  type: 'text', inputable:false},
                    { field: 'initial_stock', type: 'text', required: true, label: 'Stok Awal', 
                        attrs: { 
                            class : 'input-number text-end',
                        }
                    },
                    { field: 'initial_hpp', type: 'text', required: true, label: 'Hpp Awal', 
                        attrs: { 
                            min: 0,
                            class : 'input-number text-end'
                        }
                    },
                    { field: 'subtotal', inputable : false,
                        compute: function(vals) {
                            const initial_stock = $.inputNumber.unformat(vals.initial_stock);
                            const initial_hpp   = $.inputNumber.unformat(vals.initial_hpp);
                            return $.inputNumber.format(initial_stock * initial_hpp);
                        }
                    },
                ],
            });

            editor.setExtraData({
                warehouse_id: $('#warehouse').val(),
                period: $('#period').val()
            });
        }

        $(document).on('input', '.column_search', function() {
            if (table) {
                let colIdx = $(this).data('column');
                table.column(colIdx).search(this.value).draw();
            }
        });
    });
</script>