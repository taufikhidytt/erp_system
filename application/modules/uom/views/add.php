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
                                            <label for="uom_code">Satuan:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-price-tag-3-fill"></i>
                                                </span>
                                                <input type="text" name="uom_code" id="uom_code" class="form-control <?= form_error('uom_code') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Masukan satuan" value="<?= set_value('uom_code',''); ?>" maxlength="18" style="text-transform: uppercase;">
                                            </div>
                                            <div class="text-danger"><?= form_error('uom_code') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description">Deskripsi:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-file-text-fill"></i>
                                                </span>
                                                <input type="text" name="description" id="description" class="form-control <?= form_error('description') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Masukan deskripsi" value="<?= set_value('description',''); ?>" maxlength="80">
                                            </div>
                                            <div class="text-danger"><?= form_error('description') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12 d-flex gap-3">
                                        <div class="d-flex gap-3">
                                            <div class="mb-3">
                                                <label for="primary_flag" class="form-label d-block">Default</label>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input type="checkbox" class="form-check-input" id="primary_flag" name="primary_flag" value="Y" <?= set_value('primary_flag', 'N') == 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="primary_flag">Yes</label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="active_flag" class="form-label d-block">Aktif</label>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input type="checkbox" class="form-check-input" id="active_flag" name="active_flag" value="Y" <?= set_value('active_flag', 'Y') == 'Y' ? 'checked' : '' ?>>
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
        $('#uom_code').on('input', function() {
            $(this).val($(this).val().toUpperCase());
        });
        $('#active_flag, #primary_flag').trigger('change');
    });
    $(document).on('change','#active_flag, #primary_flag', function(){
        swith_label($(this), $(this).is(":checked"));
    });
    function swith_label(e,checked){
        e.closest('.form-check').find('label').text(checked?'Yes':'No');
    }
</script>