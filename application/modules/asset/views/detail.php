<style>
    #address,
    #description {
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
            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->ASSET_ID)); ?>">
            <div class="row">
                <div class="col-12">
                    <div class="card border-2">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center gap-2 label-status">
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <?= button_actions(['insert', 'save', 'reload']) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="kode_asset">Kode Asset:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="kode_asset" id="kode_asset" class="form-control <?= form_error('kode_asset') ? 'is-invalid' : null; ?>"
                                                    placeholder="Kode Asset" value="<?= set_value('kode_asset', $data->ASSET_CODE); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('kode_asset') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nama_asset">Nama Asset:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="nama_asset" id="nama_asset" class="form-control <?= form_error('nama_asset') ? 'is-invalid' : null; ?>"
                                                    placeholder="Nama Asset" value="<?= set_value('nama_asset', $data->ASSET_NAME); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('nama_asset') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="qty">QTY:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="qty" id="qty" class="form-control input-number <?= form_error('qty') ? 'is-invalid' : null; ?>"
                                                    placeholder="QTY" value="<?= set_value('qty', $data->ENTERED_QTY); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('qty') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="metode_depresiasi">Metode Depresiasi</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <select name="metode_depresiasi" id="metode_depresiasi" class="form-select select2 <?= form_error('metode_depresiasi') ? 'is-invalid' : null; ?>"
                                                    data-url="asset/get_metode_depresiasi"
                                                    data-default="Y"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('metode_depresiasi', $data->METODE_DEPRESIASI_ID) ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('metode_depresiasi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nilai_asset">Nilai Asset:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="nilai_asset" id="nilai_asset" class="form-control input-number <?= form_error('nilai_asset') ? 'is-invalid' : null; ?>"
                                                    placeholder="Nilai Asset" value="<?= set_value('nilai_asset', $data->NILAI_ASSET); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('nilai_asset') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nilai_penyusutan">Nilai Penyusutan:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="nilai_penyusutan" id="nilai_penyusutan" class="form-control input-number <?= form_error('nilai_penyusutan') ? 'is-invalid' : null; ?>"
                                                    placeholder="Nilai Penyusutan" value="<?= set_value('nilai_penyusutan', $data->SUSUT_YEAR); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('nilai_penyusutan') ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="start_date">Start Date:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="input-group">
                                                        <input type="date" name="start_date" id="start_date" class="form-control <?= form_error('start_date') ? 'is-invalid' : null; ?>"
                                                            placeholder="Start Date" value="<?= set_value('start_date', date('Y-m-d', strtotime($data->BUYING_DATE))); ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('start_date') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="end_date">End Date:</label>
                                                    <div class="input-group">
                                                        <input type="date" name="end_date" id="end_date" class="form-control <?= form_error('end_date') ? 'is-invalid' : null; ?>"
                                                            placeholder="End Date" value="<?= set_value('end_date', $data->USING_DATE); ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('end_date') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="asset">Asset</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <select name="asset" id="asset" class="form-select select2 <?= form_error('asset') ? 'is-invalid' : null; ?>"
                                                    data-url="asset/get_asset"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('asset', $data->COA_ID) ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('asset') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="debet">Debet</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <select name="debet" id="debet" class="form-select select2 <?= form_error('debet') ? 'is-invalid' : null; ?>"
                                                    data-url="asset/get_debet"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('debet', $data->COA_DEBET_ID) ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('debet') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kredit">Kredit:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <select name="kredit" id="kredit" class="form-select select2 <?= form_error('kredit') ? 'is-invalid' : null; ?>"
                                                    data-url="asset/get_kredit"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('kredit', $data->COA_KREDIT_ID) ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('kredit') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="umur_asset">Umur Asset:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="umur_asset" id="umur_asset" class="form-control input-number <?= form_error('umur_asset') ? 'is-invalid' : null; ?>"
                                                    placeholder="Umur Asset" value="<?= set_value('umur_asset', $data->UMUR_ASSET); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('umur_asset') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rate_depresiasi">Rate Depresiasi:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="rate_depresiasi" id="rate_depresiasi" class="form-control input-number <?= form_error('rate_depresiasi') ? 'is-invalid' : null; ?>"
                                                    placeholder="Umur Asset" value="<?= set_value('rate_depresiasi', $data->RATE); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('rate_depresiasi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tipe_asset">Tipe Asset:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <select name="tipe_asset" id="tipe_asset" class="form-select select2 <?= form_error('tipe_asset') ? 'is-invalid' : null; ?>"
                                                    data-url="asset/get_tipe_asset"
                                                    data-default="Y"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('tipe_asset', $data->ASSET_TYPE_ID) ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('tipe_asset') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="note">Note:</label>
                                            <textarea name="note" id="note" class="form-control <?= form_error('note') ? 'is-invalid' : null; ?>" rows="5" placeholder="Note"><?= set_value('note', $data->NOTE); ?></textarea>
                                            <div class="text-danger"><?= form_error('note') ?></div>
                                        </div>
                                        <div class="d-flex gap-3">
                                            <div class="mb-3">
                                                <label for="instangible_asset" class="form-label d-block">Instangible Asset</label>
                                                <div class="form-check form-switch form-switch-md mb-3" dir="ltr">
                                                    <input type="checkbox" class="form-check-input" id="instangible_asset" name="instangible_asset" value="Y" <?= set_value('instangible_asset', $data->INTANTANGIBLE_ASSET) == 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="instangible_asset">Yes</label>
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
    $(document).ready(function() {
        $('.input-number').inputNumber();
        $('#active_flag').trigger('change');
        $('#instangible_asset').trigger('change');
    });
    $(document).on('change', '#active_flag, #instangible_asset', function() {
        swith_label($(this), $(this).is(":checked"));
    });

    $(document).on('keydown', '.input-number', function(e) {
        if (
            e.key === 'e' || e.key === 'E' ||
            e.key === '+' || e.key === '-'
        ) {
            e.preventDefault();
        }
    });

    function swith_label(e, checked) {
        e.closest('.form-check').find('label').text(checked ? 'Yes' : 'No');
    }
</script>