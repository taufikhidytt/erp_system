<style>
    #address, #description{
        height: auto !important;
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
                                <a href="<?= base_url(strtolower($access['ERP_MENU_NAME'])) ?>" class="text-decoration-underline"><?= $access['PROMPT'] ?></a>
                            </li>
                            <li class="breadcrumb-item active text-decoration-underline"><?= $breadcrumb ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <form action="" method="post" id="myForm">
            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->WAREHOUSE_ID)); ?>">
            <div class="row">
                <div class="col-12">
                    <div class="card border-2">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center gap-2 label-status">
                                    <h5 style="width: 100px;" id="statusTagKonsiId"></h5>
                                    <h5 style="width: 100px;" id="readonlyTagKonsiId"></h5>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <?= button_actions(['insert','save','reload']) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="warehouse_name">Nama Gudang:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-store-2-fill"></i>
                                                </span>
                                                <input type="text" name="warehouse_name" id="warehouse_name" class="form-control <?= form_error('warehouse_name') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Nama Gudang" value="<?= set_value('warehouse_name',$data->WAREHOUSE_NAME ?? ''); ?>" maxlength="20" oninput="this.value = this.value.toUpperCase()" disabled readonly>
                                            </div>
                                            <div class="text-danger"><?= form_error('warehouse_name') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description">Deskripsi:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text" style="height: auto !important;">
                                                    <i class="ri ri-file-text-fill"></i>
                                                </span>
                                                <textarea name="description" id="description" class="form-control <?= form_error('description') ? 'is-invalid' : null; ?>" rows="2" placeholder="Deskripsi" maxlength="80" oninput="this.value = this.value.toUpperCase()"><?= set_value('description',$data->DESCRIPTION ?? ''); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('description') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jenis_id">Jenis Gudang:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-list-check"></i>
                                                </span>
                                                <select name="jenis_id" id="jenis_id" class="form-select select2 <?= form_error('jenis_id') ? 'is-invalid' : null; ?>"
                                                    data-url="gudang/get_jenis"
                                                    data-default="Y"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('jenis_id',$data->JENIS_ID ?? '') ?>"
                                                    >
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('jenis_id') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="address_id">Lokasi</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-map-pin-fill"></i>
                                                </span>
                                                <select name="address_id" id="address_id" class="form-select select2 <?= form_error('address_id') ? 'is-invalid' : null; ?>"
                                                    data-url="gudang/get_address"
                                                    data-selected-id="<?= set_value('address_id',$data->ADDRESS_ID ?? '') ?>"
                                                    >
                                                
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('address_id') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address">Alamat</label>
                                            <textarea class="form-control" id="address" placeholder="Alamat lengkap" disabled rows="5"></textarea>
                                        </div>
                                        <div class="d-flex gap-3">
                                            <div class="mb-3">
                                                <label for="primary_flag" class="form-label d-block">Default</label>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input type="checkbox" class="form-check-input" id="primary_flag" name="primary_flag" value="Y" <?= set_value('primary_flag', $data->PRIMARY_FLAG) == 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="primary_flag">Yes</label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="sales_flag" class="form-label d-block">Penjualan</label>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input type="checkbox" class="form-check-input" id="sales_flag" name="sales_flag" value="Y" <?= set_value('sales_flag', $data->SALES_FLAG) == 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="sales_flag">Yes</label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="active_flag" class="form-label d-block">Aktif</label>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input type="checkbox" class="form-check-input" id="active_flag" name="active_flag" value="Y" <?= set_value('active_flag', $data->ACTIVE_FLAG) == 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="active_flag">Yes</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#active_flag, #sales_flag, #primary_flag').trigger('change');
    });
    $(document).on('change','#active_flag, #sales_flag, #primary_flag', function(){
        swith_label($(this), $(this).is(":checked"));
    });
    function swith_label(e,checked){
        e.closest('.form-check').find('label').text(checked?'Yes':'No');
    }
    $(document).on('change','#address_id', function(){
        setTimeout(() => {
            const data = $(this).find("option:selected").data() ?? [];
            console.log(data);
            let lines = [];
            $.each(['address1', 'address2', 'city','province','country'], function(k,key){
                if (data[key]) {
                    lines.push(data[key]);
                }
            });
            let newContent = lines.join('\n');
            $('#address').val(newContent);
        }, 100);
    })
</script>