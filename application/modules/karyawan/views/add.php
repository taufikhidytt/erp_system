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
                                            <label for="nama_depan">Nama Depan:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="nama_depan" id="nama_depan" class="form-control <?= form_error('nama_depan') ? 'is-invalid' : null; ?>"
                                                    placeholder="Nama Depan" value="<?= set_value('nama_depan', ''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('nama_depan') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nama_belakang">Nama Belakang:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <input type="text" name="nama_belakang" id="nama_belakang" class="form-control <?= form_error('nama_belakang') ? 'is-invalid' : null; ?>"
                                                    placeholder="Nama Belakang" value="<?= set_value('nama_belakang', ''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('nama_belakang') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="bagian">Bagian</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <select name="bagian" id="bagian" class="form-select select2 <?= form_error('bagian') ? 'is-invalid' : null; ?>"
                                                    data-url="karyawan/get_bagian"
                                                    data-default="Y"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('bagian', '') ?>">
                                                </select>
                                            </div>
                                            <input type="hidden" name="program_code" id="program_code" value="<?= set_value('program_code', ''); ?>">
                                            <div class="text-danger"><?= form_error('bagian') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="divisi">Divisi</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <select name="divisi" id="divisi" class="form-select select2 <?= form_error('divisi') ? 'is-invalid' : null; ?>"
                                                    data-url="karyawan/get_divisi"
                                                    data-default="Y"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('divisi', '') ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('divisi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kategori">Kategori</label>
                                            <div class="input-group">
                                                <select name="kategori" id="kategori" class="form-select select2 <?= form_error('kategori') ? 'is-invalid' : null; ?>"
                                                    data-url="karyawan/get_kategori"
                                                    data-default="Y"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('kategori', '') ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('kategori') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gudang_sales">Gudang Sales:</label>
                                            <span class="text-danger gudang-required"></span>
                                            <div class="input-group">
                                                <select name="gudang_sales" id="gudang_sales" class="form-select select2 <?= form_error('gudang_sales') ? 'is-invalid' : null; ?>"
                                                    data-url="karyawan/get_gudang"
                                                    data-default="Y"
                                                    data-dropdown-parent="body"
                                                    data-selected-id="<?= set_value('gudang_sales', '') ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('gudang_sales') ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="start_date">Start Date:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="input-group">
                                                        <input type="date" name="start_date" id="start_date" class="form-control <?= form_error('start_date') ? 'is-invalid' : null; ?>"
                                                            placeholder="Start Date" value="<?= set_value('start_date', date('Y-m-d')); ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('start_date') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="end_date">End Date:</label>
                                                    <div class="input-group">
                                                        <input type="date" name="end_date" id="end_date" class="form-control <?= form_error('end_date') ? 'is-invalid' : null; ?>"
                                                            placeholder="End Date" value="<?= set_value('end_date', ''); ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('end_date') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="type_cu">Type CU:</label>
                                            <div class="input-group">
                                                <input type="text" name="type_cu" id="type_cu" class="form-control input-number <?= form_error('type_cu') ? 'is-invalid' : null; ?>"
                                                    placeholder="Type CU" value="<?= set_value('type_cu', ''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('type_cu') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pmc">PMC:</label>
                                            <div class="input-group">
                                                <input type="text" name="pmc" id="pmc" class="form-control input-number <?= form_error('pmc') ? 'is-invalid' : null; ?>"
                                                    placeholder="PMC" value="<?= set_value('pmc', ''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('pmc') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="fcp">FCP:</label>
                                            <div class="input-group">
                                                <input type="text" name="fcp" id="fcp" class="form-control input-number <?= form_error('fcp') ? 'is-invalid' : null; ?>"
                                                    placeholder="FCP" value="<?= set_value('fcp', ''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('fcp') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="pjt">PJT:</label>
                                            <div class="input-group">
                                                <input type="text" name="pjt" id="pjt" class="form-control input-number <?= form_error('pjt') ? 'is-invalid' : null; ?>"
                                                    placeholder="PJT" value="<?= set_value('pjt', ''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('pjt') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="acs">ACS:</label>
                                            <div class="input-group">
                                                <input type="text" name="acs" id="acs" class="form-control input-number <?= form_error('acs') ? 'is-invalid' : null; ?>"
                                                    placeholder="ACS" value="<?= set_value('acs', ''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('acs') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="note">Note:</label>
                                            <textarea name="note" id="note" class="form-control <?= form_error('note') ? 'is-invalid' : null; ?>" rows="5" placeholder="Note"><?= set_value('note', ''); ?></textarea>
                                            <div class="text-danger"><?= form_error('note') ?></div>
                                        </div>
                                        <div class="d-flex gap-3">
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
    $(document).ready(function() {
        $('.input-number').inputNumber();
        $('#active_flag').trigger('change');
    });
    $(document).on('change', '#active_flag', function() {
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

    $('#bagian').on('select2:select', function(e) {

        let programCode = e.params.data.PROGRAM_CODE1;

        $('#program_code').val(programCode || '');

        $('.gudang-required').text(programCode === 'SALES' ? '*' : '');
    });
</script>