<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url($access['url']) ?>" class="text-decoration-underline"><?= $access['PROMPT'] ?></a>
                            </li>
                            <li class="breadcrumb-item active text-decoration-underline"><?= $breadcrumb ?></li>
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
                        <form action="" method="post">
                            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->SERVER_ID)) ?>">
                            <div class="row mb-2">
                                <div class="col-sm-12 text-end">
                                    <?= button_actions(['insert','save',
                                    // ['key' => 'delete', 'data-id' => $this->encrypt->encode($data->SERVER_ID)],
                                    'reload']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="name">DB Name:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="name" id="name" class="form-control <?= form_error('name') ? 'is-invalid' : null; ?>" value="<?= set_value('name',$data->DB_NAME ?? '') ?>" placeholder="Enter DB Name">
                                            </div>
                                            <div class="text-danger"><?= form_error('name') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="alias">DB Alias:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="alias" id="alias" class="form-control <?= form_error('alias') ? 'is-invalid' : null; ?>" value="<?= set_value('alias',$data->DB_ALIAS ?? '') ?>" placeholder="Enter DB Alias">
                                            </div>
                                            <div class="text-danger"><?= form_error('alias') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="hostname">Hostname:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="hostname" id="hostname" class="form-control <?= form_error('hostname') ? 'is-invalid' : null; ?>" value="<?= set_value('hostname',$data->HOSTNAME ?? '') ?>" placeholder="Enter Hostname">
                                            </div>
                                            <div class="text-danger"><?= form_error('hostname') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="port">PORT:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="port" id="port" 
                                                    data-decimal="0" data-thousand=""
                                                    class="input-number form-control <?= form_error('port') ? 'is-invalid' : null; ?>" value="<?= set_value('port',$data->PORT ?? '') ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('port') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="active_flag">Active Flag:</label>
                                            <div class="input-group">
                                                <div class="form-check form-switch mb-3" dir="ltr">
                                                    <input type="checkbox" name="active_flag" class="form-check-input" id="active_flag" <?= set_value('active_flag',$data->ACTIVE_FLAG ?? 'N') === 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label"></label>
                                                </div>
                                            </div>
                                            <div class="text-danger"><?= form_error('active_flag') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#active_flag').trigger('change');
    });
    $(document).on('change', '#active_flag', function(){
        const is_checked = $(this).is(':checked');
        $(this).closest('.input-group').find('.form-check-label').text(is_checked ? 'Yes' : 'No');
    });
    $(document).on('click','a', function(){
        setTimeout(function(){
            $('#loading').hide();
        });
    });
    $('#name').on('input', function() {
        var sanitized = $(this).val().replace(/[^a-zA-Z0-9_]/g, '');
        $(this).val(sanitized);
    });
    $('form').on('submit', function(e){
        $.each($(document).find('[data-input-number], .input-number'), function(){
            $(this).val($(this).inputNumber('getValue'));
        });
        HTMLFormElement.prototype.submit.call(this);
    });
</script>