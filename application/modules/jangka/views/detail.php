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
            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->PAYMENT_TERM_ID)); ?>">
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
                                            <label for="payment_term_name">Nama / Kode:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-calendar-todo-fill"></i>
                                                </span>
                                                <input type="text" name="payment_term_name" id="payment_term_name" class="form-control" 
                                                    placeholder="Nama / Kode" value="<?= $data->PAYMENT_TERM_NAME ?>" maxlength="40">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description">Keterangan:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-file-text-fill"></i>
                                                </span>
                                                <textarea name="description" id="description" class="form-control <?= form_error('description') ? 'is-invalid' : null; ?>" rows="2" placeholder="Keterangan" maxlength="80" required><?= set_value('description', $data->DESCRIPTION); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('description') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="number_days">Jangka Waktu:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-calendar-2-line"></i>
                                                </span>
                                                <input type="number" name="number_days" id="number_days" class="form-control input-number <?= form_error('number_days') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Jangka Waktu" data-decimal="0" value="<?= set_value('number_days', $data->NUMBER_DAYS); ?>">
                                                <span class="input-group-text">
                                                    Hari
                                                </span>
                                            </div>
                                            <div class="text-danger"><?= form_error('number_days') ?></div>
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
        $('#active_flag, #primary_flag').trigger('change');
    });
    $(document).on('change','#active_flag, #primary_flag', function(){
        swith_label($(this), $(this).is(":checked"));
    });
    function swith_label(e,checked){
        e.closest('.form-check').find('label').text(checked?'Yes':'No');
    }
    $('form').on('submit', function(e){
        $.each($(document).find('[data-input-number], .input-number'), function(){
            $(this).val($(this).inputNumber('getValue'));
        });
        HTMLFormElement.prototype.submit.call(this);
    });
</script>