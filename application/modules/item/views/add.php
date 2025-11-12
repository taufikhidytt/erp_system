<div id="flashSuccess" data-success="<?= $this->session->flashdata('success'); ?>"></div>
<div id="flashWarning" data-warning="<?= $this->session->flashdata('warning'); ?>"></div>
<div id="flashError" data-error="<?= $this->session->flashdata('error'); ?>"></div>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h1><?= $title ?></h1>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('item') ?>">Item</a>
                            </li>
                            <li class="breadcrumb-item active"><?= $heading ?></li>
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
                        <div class="row mb-2">
                            <div class="offset-lg-6 offset-md-6 col-lg-6 col-md-6 col-sm-12 text-end">
                                <a href="<?= base_url('item') ?>" class="btn btn-sm btn-secondary">
                                    <i class="ri ri-reply-fill"></i> Back
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="brand">Brand:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-database-2-fill"></i>
                                                </span>
                                                <select name="brand" id="brand" class="form-control select2 <?= form_error('brand') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Brand --</option>
                                                    <?php foreach ($brand->result() as $br): ?>
                                                        <option value="<?= $this->encrypt->encode($br->ERP_LOOKUP_VALUE_ID); ?>" <?= set_value('brand'); ?>><?= strtoupper($br->Brand_Name) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div id="error-brand" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category">Category:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-server-fill"></i>
                                                </span>
                                                <select name="category" id="category" class="form-control select2 <?= form_error('category') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Category --</option>
                                                    <?php foreach ($category->result() as $ct): ?>
                                                        <option value="<?= $this->encrypt->encode($ct->ERP_LOOKUP_VALUE_ID); ?>" <?= set_value('category'); ?> data-name="<?= strtoupper($ct->Category_Name); ?>"><?= strtoupper($ct->Category_Code) ?> ~ [<?= strtoupper($ct->Category_Name); ?>]</option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div id="error-category" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="part_number">Part Number:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-line"></i>
                                                </span>
                                                <input type="text" name="part_number" id="part_number" class="form-control <?= form_error('part_number') ? 'is-invalid' : null; ?>" placeholder="Enter Part Number" value="<?= $this->input->post('part_number'); ?>">
                                            </div>
                                            <div id="error-part_number" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="description">Description:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-sticky-note-2-fill"></i>
                                                </span>
                                                <textarea name="description" id="description" class="form-control <?= form_error('description') ? 'is-invalid' : null ?>" placeholder="Enter Description"><?= $this->input->post('description'); ?></textarea>
                                            </div>
                                            <div id="error-description" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="assy_code">Assy Code:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-line"></i>
                                                </span>
                                                <input type="text" name="assy_code" id="assy_code" class="form-control <?= form_error('assy_code') ? 'is-invalid' : null; ?>" placeholder="Enter Assy Code" value="<?= $this->input->post('assy_code'); ?>">
                                            </div>
                                            <div id="error-assy_code" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="satuan">Satuan:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-quill-pen-fill"></i>
                                                </span>
                                                <select name="satuan" id="satuan" class="form-control select2 <?= form_error('satuan') ? 'is-invalid' : null; ?>">
                                                    <?php foreach ($uom->result() as $um): ?>
                                                        <option value="<?= $um->UOM_CODE ?>" <?= set_value('satuan') ?>><?= strtoupper($um->DESCRIPTION) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div id="error-satuan" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="type">Type:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-swap-fill"></i>
                                                </span>
                                                <select name="type" id="type" class="form-control select2 <?= form_error('type') ? 'is-invalid' : null; ?>">
                                                    <?php foreach ($type->result() as $tp): ?>
                                                        <option value="<?= $this->encrypt->encode($tp->ERP_LOOKUP_VALUE_ID); ?>" <?= set_value('type') ?>><?= strtoupper($tp->Trade_Type) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div id="error-type" class="invalid-feedback"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="min_stock">Min Stock:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="0" name="min_stock" id="min_stock" class="form-control <?= form_error('min_stock') ? 'is-invalid' : null; ?>" placeholder="Enter Min Stock" value="<?= $this->input->post('min_stock'); ?>">
                                                    </div>
                                                    <div id="error-min_stock" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="lead_time">Lead Time:</label>
                                                    <div class="row">
                                                        <div class="col-lg-8 col-md-6 col-sm-12">
                                                            <div class="input-group">
                                                                <span class="input-group-text">
                                                                    <i class="ri ri-rocket-2-fill"></i>
                                                                </span>
                                                                <input type="number" min="1" name="lead_time" id="lead_time" class="form-control <?= form_error('lead_time') ? 'is-invalid' : null; ?>" placeholder="Enter Lead Time" value="<?= $this->input->post('lead_time') ?? '1'; ?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4 col-md-6 col-sm-12">
                                                            <br>
                                                            <p>Weeks</p>
                                                        </div>
                                                    </div>
                                                    <div id="error-lead_time" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rak">Rak:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-archive-drawer-fill"></i>
                                                </span>
                                                <select name="rak" id="rak" class="form-control select2 <?= form_error('rak') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Rak --</option>
                                                    <?php foreach ($rak->result() as $rk): ?>
                                                        <option value="<?= $this->encrypt->encode($rk->ERP_LOOKUP_VALUE_ID); ?>" <?= set_value('rak') ?>><?= strtoupper($rk->Grade) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div id="error-rak" class="invalid-feedback"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-12 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="length">Length:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="1" name="length" id="length" class="form-control <?= form_error('length') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('length'); ?>">
                                                    </div>
                                                    <div id="error-length" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-12 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="width">Width:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="1" name="width" id="width" class="form-control <?= form_error('width') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('width'); ?>">
                                                    </div>
                                                    <div id="error-width" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-12 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="height">Height:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="1" name="height" id="height" class="form-control <?= form_error('height') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('height'); ?>">
                                                    </div>
                                                    <div id="error-height" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="kubikasi">Kubikasi:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="1" name="kubikasi" id="kubikasi" class="form-control <?= form_error('kubikasi') ? 'is-invalid' : null; ?>" placeholder="" value="<?= $this->input->post('kubikasi'); ?>" disabled>
                                                    </div>
                                                    <div id="error-kubikasi" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="weight">Weight:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="1" name="weight" id="weight" class="form-control <?= form_error('weight') ? 'is-invalid' : null; ?>" placeholder="" value="<?= $this->input->post('weight'); ?>">
                                                    </div>
                                                    <div id="error-weight" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="made_in">Made In:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-swap-fill"></i>
                                                </span>
                                                <select name="made_in" id="made_in" class="form-control select2 <?= form_error('made_in') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Made In --</option>
                                                    <?php foreach ($made_in->result() as $mi): ?>
                                                        <option value="<?= $this->encrypt->encode($mi->ERP_LOOKUP_VALUE_ID); ?>" <?= set_value('made_in') ?>><?= strtoupper($mi->Made_In) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div id="error-made_in" class="invalid-feedback"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="komoditi">Komoditi:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-swap-fill"></i>
                                                </span>
                                                <select name="komoditi" id="komoditi" class="form-control select2 <?= form_error('komoditi') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Komoditi --</option>
                                                    <?php foreach ($komoditi->result() as $kd): ?>
                                                        <option value="<?= $this->encrypt->encode($kd->ERP_LOOKUP_VALUE_ID); ?>" <?= set_value('komoditi') ?>><?= strtoupper($kd->Note) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div id="error-komoditi" class="invalid-feedback"></div>
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
    <!-- container-fluid -->
</div>
<!-- End Page-content -->

<script>
    $(document).ready(function() {

        $('#brand, #category').on('change', updateDescription);
        $('#part_number').on('input', updateDescription);

        //Initialize Select2 Elements
        $('.select2').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this).parent(),
            });
        });

        var flashsuccess = $('#flashSuccess').data('success');
        var flashwarning = $('#flashWarning').data('warning');
        var flasherror = $('#flashError').data('error');

        if (flashsuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: flashsuccess,
            })
        }

        if (flashwarning) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: flashwarning,
            })
        }

        if (flasherror) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: flasherror,
            })
        }

        const input = $('#part_number');
        input.on('input', function(e) {
            if (e.key === ' ' || e.code === 'Space') {
                e.preventDefault();
                let teks = $(this).val();
                teks += '-';
                $(this).val(teks);
            }
        });

        input.on('input', function() {
            let teks = $(this).val();
            teks = teks.replace(/\*/g, 'X');
            teks = teks.replace(/[\s\p{P}\+=~`|$^]/gu, '-');
            $(this).val(teks);
        });

        $('#min_stock, #lead_time, #length, #width, #height, #weight').on('keydown', function(e) {
            if (
                e.key === 'e' || e.key === 'E' ||
                e.key === '+' || e.key === '-'
            ) {
                e.preventDefault();
            }
        });

        $('#min_stock').on('input change', function() {
            let val = $(this).val();
            if (val === '') return;
            val = parseFloat(val);
            if (val < 0) {
                $(this).val(0);
            }
        });

        $('#lead_time, #length, #width, #height, #weight').on('input change', function() {
            let val = $(this).val();
            if (val === '') return;
            val = parseFloat(val);
            if (val < 1) {
                $(this).val(1);
            }

            let p = parseFloat($('#length').val()) || 0;
            let l = parseFloat($('#width').val()) || 0;
            let t = parseFloat($('#height').val()) || 0;

            let kubikasi = p * l * t;

            if (kubikasi > 0) {
                $('#kubikasi').val(kubikasi);
            } else {
                $('#kubikasi').val('');
            }
        });
    });

    function updateDescription() {
        let brandText = $('#brand option:selected').text().trim();
        let categoryText = $('#category option:selected').data('name') || '';
        let partNumber = $('#part_number').val().trim();

        let parts = [brandText, categoryText, partNumber].filter(function(val) {
            return val !== "" && val !== "-- Selected Brand --" && val !== "-- Selected Category --";
        });

        let description = parts.join(' ');

        $('#description').val(description);
    }
</script>