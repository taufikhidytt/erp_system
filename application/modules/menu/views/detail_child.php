<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('menu') ?>" class="text-decoration-underline"><?= $access['PROMPT'] ?></a>
                            </li>
                            <?php if($parent){ ?>
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('menu/detail/'.base64url_encode($this->encrypt->encode($data->PARENT_ID))) ?>" class="text-decoration-underline"><?= $parent->PROMPT ?></a>
                            </li>
                            <?php } ?>
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
                            <div class="row mb-2">
                                <div class="col-sm-12 text-end">
                                    <a href="<?= site_url('menu/add') ?>" 
                                        type="button" class="btn btn-sm btn-primary" title="Add Module" data-toggle="tooltip" data-bs-placement="left">
                                        <i class="ri-add-circle-fill"></i>
                                    </a>
                                    <a href="<?= site_url('menu/add_child/'.base64url_encode($this->encrypt->encode($data->PARENT_ID))) ?>" 
                                        type="button" class="btn btn-sm btn-info" title="Add Child" data-toggle="tooltip" data-bs-placement="left">
                                        <i class="ri-add-circle-fill"></i>
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-success" title="Save" data-toggle="tooltip" data-bs-placement="left">
                                        <i class="ri-save-3-fill"></i>
                                    </button>
                                    <?= button_actions([
                                        [
                                            'key'          => 'log_info',
                                            'class'        => 'btn-info btn-log-info',
                                            'title'        => 'Log & History',
                                            'icon'         => 'ri-question-line',
                                            'data-param'   => base64_encode($this->encrypt->encode(json_encode([
                                                'h' => [
                                                    't' => 'erp_menu',
                                                    'w' => ['a.ERP_MENU_ID' => $data->ERP_MENU_ID]
                                                ],
                                                'where' => [
                                                    'TABLE_NAME'    => 'ERP_MENU',
                                                    'ID'      => $data->ERP_MENU_ID
                                                ],
                                            ]))),
                                        ],
                                    ]) ?>
                                    <button type="button" onclick="location.reload()" class="btn btn-sm btn-warning" title="Reload" data-toggle="tooltip" data-bs-placement="left">
                                        <i class="ri-reply-fill"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <input type="hidden" name="id" id="id" value="<?= base64url_encode($this->encrypt->encode($data->ERP_MENU_ID)); ?>">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="name">Menu Name:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="name" id="name" class="form-control <?= form_error('name') ? 'is-invalid' : null; ?>" value="<?= set_value('name',$data->ERP_MENU_NAME) ?>" placeholder="Enter Menu Name">
                                            </div>
                                            <div class="text-danger"><?= form_error('name') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="prompt">Prompt:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="prompt" id="prompt" class="form-control <?= form_error('prompt') ? 'is-invalid' : null; ?>" value="<?= set_value('prompt',$data->PROMPT) ?>" placeholder="Enter Prompt">
                                            </div>
                                            <div class="text-danger"><?= form_error('prompt') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="seq">Urutan:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="seq" id="seq" data-decimal="0" class="input-number form-control <?= form_error('seq') ? 'is-invalid' : null; ?>" value="<?= set_value('seq',$data->SEQ ?? '') ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('seq') ?></div>
                                        </div>
                                        <div class="d-flex gap-5">
                                            <div class="mb-3">
                                                <label for="active_flag">Active Flag:</label>
                                                <div class="input-group">
                                                    <div class="form-check form-switch mb-3" dir="ltr">
                                                        <input type="checkbox" name="active_flag" class="form-check-input" id="active_flag" <?= set_value('active_flag', $data->ACTIVE_FLAG ?? '') === 'Y' ? 'checked' : '' ?>>
                                                        <label class="form-check-label"></label>
                                                    </div>
                                                </div>
                                                <div class="text-danger"><?= form_error('active_flag') ?></div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="document_no">Document No:</label>
                                                <div class="input-group">
                                                    <div class="form-check form-switch mb-3" dir="ltr">
                                                        <input type="checkbox" name="document_no" class="form-check-input" id="document_no" <?= set_value('document_no',$data->FLAG_ERP_NO ?? '') === 'Y' ? 'checked' : '' ?>>
                                                        <label class="form-check-label"></label>
                                                    </div>
                                                </div>
                                                <div class="text-danger"><?= form_error('document_no') ?></div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="ppn">PPN:</label>
                                                <div class="input-group">
                                                    <div class="form-check form-switch mb-3" dir="ltr">
                                                        <input type="checkbox" name="ppn" class="form-check-input" id="ppn" <?= set_value('ppn',$data->PPN ?? '') === 'Y' ? 'checked' : '' ?>>
                                                        <label class="form-check-label"></label>
                                                    </div>
                                                </div>
                                                <div class="text-danger"><?= form_error('ppn') ?></div>
                                            </div>
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
        setTimeout(function(){
            $('.page-content form').find('input, select, textarea').prop('disabled',false);
            $('#active_flag').trigger('change');
            $('#document_no').trigger('change');
        },300);
    });
    $(document).on('change', '#active_flag,#document_no,#ppn', function(){
        const is_checked = $(this).is(':checked');
        $(this).closest('.input-group').find('.form-check-label').text(is_checked ? 'Yes' : 'No');
    });
    $(document).on('click change','#document_no', function(){
        const is_checked = $(this).is(":checked");
        if(is_checked){
            $('#ppn').prop('disabled',false);
        }else{
            $('#ppn').prop('checked',false).prop('disabled',true);
        }
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