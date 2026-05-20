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
                                    <?= button_actions(['insert', 'save', 'reload']) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="address_code">Kode Alamat:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-fill"></i>
                                                </span>
                                                <input type="text" name="address_code" id="address_code" class="form-control <?= form_error('address_code') ? 'is-invalid' : null; ?>"
                                                    placeholder="Kode Alamat" value="<?= set_value('address_code', ''); ?>" maxlength="18" oninput="this.value = this.value.toUpperCase()">
                                            </div>
                                            <div class="text-danger"><?= form_error('address_code') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address1">Alamat 1:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-map-pin-fill"></i>
                                                </span>
                                                <textarea name="address1" id="address1" class="form-control <?= form_error('address1') ? 'is-invalid' : null; ?>" rows="2" placeholder="Alamat 1" maxlength="80"><?= set_value('address1', ''); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('address1') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address2">Alamat 2:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-map-pin-line"></i>
                                                </span>
                                                <textarea name="address2" id="address2" class="form-control <?= form_error('address2') ? 'is-invalid' : null; ?>" rows="2" placeholder="Alamat 2" maxlength="80"><?= set_value('address2', ''); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('address2') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="city">Kota:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-building-fill"></i>
                                                </span>
                                                <input type="text" name="city" id="city" class="form-control <?= form_error('city') ? 'is-invalid' : null; ?>"
                                                    placeholder="Kota" value="<?= set_value('city', ''); ?>" maxlength="40">
                                            </div>
                                            <div class="text-danger"><?= form_error('city') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="province">Provinsi:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-road-map-fill"></i>
                                                </span>
                                                <input type="text" name="province" id="province" class="form-control <?= form_error('province') ? 'is-invalid' : null; ?>"
                                                    placeholder="Provinsi" value="<?= set_value('province', ''); ?>" maxlength="40">
                                            </div>
                                            <div class="text-danger"><?= form_error('province') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="country">Negara:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-earth-fill"></i>
                                                </span>
                                                <input type="text" name="country" id="country" class="form-control <?= form_error('country') ? 'is-invalid' : null; ?>"
                                                    placeholder="Negara" value="<?= set_value('country', ''); ?>" maxlength="40">
                                            </div>
                                            <div class="text-danger"><?= form_error('country') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone">No Telp:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-phone-fill"></i>
                                                </span>
                                                <input type="text" name="phone" id="phone" class="form-control <?= form_error('phone') ? 'is-invalid' : null; ?>"
                                                    placeholder="No Telp" value="<?= set_value('phone', ''); ?>" maxlength="40">
                                            </div>
                                            <div class="text-danger"><?= form_error('phone') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fax">Email:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-mail-send-fill"></i>
                                                </span>
                                                <input type="text" name="fax" id="fax" class="form-control <?= form_error('fax') ? 'is-invalid' : null; ?>"
                                                    placeholder="Email" value="<?= set_value('fax', ''); ?>" maxlength="40">
                                            </div>
                                            <div class="text-danger"><?= form_error('fax') ?></div>
                                        </div>
                                        <div class="d-flex gap-3">
                                            <div class="mb-3">
                                                <label for="ship_flag" class="form-label d-block">Bisa Kirim (Ship Flag)</label>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input type="checkbox" class="form-check-input" id="ship_flag" name="ship_flag" value="Y" <?= set_value('ship_flag', 'N') == 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="ship_flag">Yes</label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="active_flag" class="form-label d-block">Active Flag</label>
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
    $('#active_flag, #ship_flag').trigger('change');
});
$(document).on('change','#active_flag, #ship_flag', function(){
    swith_label($(this), $(this).is(":checked"));
});
function swith_label(e,checked){
    e.closest('.form-check').find('label').text(checked?'Yes':'No');
}
</script>