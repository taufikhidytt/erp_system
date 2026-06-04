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
                                    <label for="periode_awal" class="form-label">Part Brand</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" id="chk-brand">
                                                <label class="form-check-label" for="chk-brand">
                                                    All
                                                </label>
                                            </div>
                                        </span>
                                        <select name="brand" id="brand" 
                                            data-url="api/get_brand"
                                            data-default="Y"
                                            class="form-control select2">
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="periode_awal" class="form-label">Part Category</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" id="chk-category">
                                                <label class="form-check-label" for="chk-category">
                                                    All
                                                </label>
                                            </div>
                                        </span>
                                        <input type="hidden" id="id_page" value="<?= base64url_encode($this->encrypt->encode($kolom_harga)) ?>">
                                        <select name="category" id="category" class="form-control select2"
                                            data-url="api/get_category"
                                            data-default="Y"
                                            placeholder="Select Category"
                                        >
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3" id="d-table">
                            <table class="table text-center w-100 table-sm" id="table">
                                <thead class="align-middle">
                                    <tr>
                                        <th></th>
                                        <?php for ($i=1; $i <=(7+$kolom_harga) ; $i++) { ?> 
                                            <th>
                                                <?php if((7+$kolom_harga) == $i){ ?>
                                                <select class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                                <?php } else { ?>
                                                <input type="text" placeholder="Cari.." class="column_search<?= $i>4?' input-number text-end':'' ?>" data-allow-negatif="true" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                <?php } ?>
                                                
                                            </th>
                                        <?php } ?>
                                    </tr>
                                    <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                        <th rowspan="<?= $rowspan ?>">No</th>
                                        <th rowspan="<?= $rowspan ?>">Kode Item</th>
                                        <th rowspan="<?= $rowspan ?>">Nama Item</th>
                                        <th rowspan="<?= $rowspan ?>">Brand</th>
                                        <th rowspan="<?= $rowspan ?>">Satuan</th>
                                        <th rowspan="<?= $rowspan ?>">Harga Beli</th>
                                        <th rowspan="<?= $rowspan ?>">COGS (IDR)</th>
                                        <?= $kolom_harga?'<th class="text-center" colspan="'.$kolom_harga.'">Harga Jual</th>':'' ?>
                                        <th rowspan="<?= $rowspan ?>">Aktif</th>
                                    </tr>
                                    <?php if($kolom_harga){
                                        $col = '<tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">';
                                        for ($i=0; $i < $kolom_harga ; $i++) { 
                                            if(isset($fields[$i])){
                                                $col .= '<th class="d-field">'.$fields[$i]->DISPLAY_NAME.'</th>';
                                            }
                                        }
                                        $col .= '</tr>';

                                        echo $col;
                                    } ?>
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

<script>
    let table = null;
    let editor= null;
    $(document).ready(function(){
        let columns = [
            { data: 'no', orderable: false, searchable: false, className: 'text-center' },
            { data: 'part_code', className : 'text-center'},
            { data: 'description'},
            { data: 'part_brand'},
            { data: 'uom'},
            { data: 'harga_beli', className : 'text-end'},
            { data: 'cogs_idr', className : 'text-end'},
        ];

        $('.d-field').each(function(k,v){
            columns.push({data : 'lvl'+(k+1), className : 'text-end'});
        });
        columns.push({data : 'active_flag', className : 'text-center'});

        table = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>rtip',
            buttons: getButtons(<?= json_encode(button_actions([
                [
                    'key'      => 'import',
                    'redirect' => site_url('harga/import'),
                    'class'    => 'btn-success',
                    'title'    => 'Import',
                    'icon'     => 'ri-file-upload-line',
                    'needs_auth' => true,
                ],
            ], 'dt')) ?>),
            processing: true,
            serverSide: true,
            searching: true,
            order: [],
            scrollX: true,
            scrollY: '47dvh',
            scrollCollapse: true,
            drawCallback: function() {
                this.api().columns.adjust();
            },
            ajax: {
                url: "<?= base_url('harga/get_data') ?>",
                type: "POST",
                data: function(d) {
                    d.chk_category  = $('#chk-category').is(':checked');
                    d.category      = $('#category').val();
                    d.chk_brand     = $('#chk-brand').is(':checked');
                    d.brand         = $('#brand').val();
                    d.id_page       = $('#id_page').val();
                }
            },
            columns: columns
        });

        $('#chk-category,#chk-brand').on('click', function(e){
            const id = $(this).attr('id').replace('chk-','');
            if($(this).is(':checked')){
                $(`#${id}`).prop('disabled',true);
            }else{
                $(`#${id}`).prop('disabled',false);
            }

            table.ajax.reload();
        });
        $('#category, #brand').on('change', function(){
            setTimeout(() => {
                table.ajax.reload();
            }, 100);
        });

        $(document).on('input', '.column_search', function() {
            if (table) {
                let colIdx = $(this).data('column');
                table.column(colIdx).search(this.value).draw();
            }
        });
        $('#chk-category').trigger('click');
    });
</script>