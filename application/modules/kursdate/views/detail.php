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
            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->KURS_DETAIL_ID)); ?>">
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
                                    <?= button_actions(['save','reload']) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="document_date">Tanggal:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-calendar-line"></i>
                                                </span>
                                                <input type="text" id="document_date" class="form-control" 
                                                    value="<?= $data->DOCUMENT_DATE ?>" disabled readonly>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="mata_uang_code">Kode:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-fill"></i>
                                                </span>
                                                <input type="text" id="mata_uang_code" class="form-control" 
                                                    value="<?= $data->MATA_UANG_CODE ?>" disabled readonly>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="mata_uang_name">Nama:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-file-text-fill"></i>
                                                </span>
                                                <input type="text" id="mata_uang_name" class="form-control" 
                                                    value="<?= $data->MATA_UANG_NAME ?>" disabled readonly>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="user_name">User:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-fill"></i>
                                                </span>
                                                <input type="text" id="user_name" class="form-control" 
                                                    value="<?= $data->ERP_USER_NAME ?>" disabled readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="nilai">Rate:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    Rp
                                                </span>
                                                <input type="text" name="nilai" id="nilai" class="input-number form-control <?= form_error('nilai') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Rate" value="<?= set_value('nilai', (float)$data->NILAI); ?>" required>
                                            </div>
                                            <div class="text-danger"><?= form_error('nilai') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="note">Keterangan:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-file-text-fill"></i>
                                                </span>
                                                <textarea name="note" id="note" class="form-control <?= form_error('note') ? 'is-invalid' : null; ?>" rows="4" placeholder="Keterangan" maxlength="240"><?= set_value('note', $data->NOTE); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('note') ?></div>
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
    $('form').on('submit', function(e){
        $.each($(document).find('[data-input-number], .input-number'), function(){
            $(this).val($(this).inputNumber('getValue'));
        });
        HTMLFormElement.prototype.submit.call(this);
    });
</script>