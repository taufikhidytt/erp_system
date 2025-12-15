<style>
    .form-xs textarea.form-control {
        height: 80px !important;
        min-height: 30px !important;
        padding: 2px 6px !important;
        font-size: 0.75rem !important;
    }

    .form-xs textarea {
        resize: vertical;
    }
</style>

<div id="flashSuccess" data-success="<?= $this->session->flashdata('success'); ?>"></div>
<div id="flashWarning" data-warning="<?= $this->session->flashdata('warning'); ?>"></div>
<div id="flashError" data-error="<?= $this->session->flashdata('error'); ?>"></div>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('fpk') ?>" class="text-decoration-underline">FPK</a>
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
                            <div class="row mb-2">
                                <div class="offset-lg-6 offset-md-6 col-lg-6 col-md-6 col-sm-12 text-end">
                                    <button type="button" class="btn btn-primary btn-sm" onclick="window.location.replace(window.location.pathname);" data-toggle="tooltip" data-placement="bottom" title="Tambah">
                                        <i class="ri ri-add-box-fill"></i>
                                    </button>
                                    <button type="submit" class="btn btn-success btn-sm" name="submit" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan">
                                        <i class="ri ri-check-double-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.replace(window.location.pathname);" data-toggle="tooltip" data-placement="bottom" title="Undo">
                                        <i class="ri ri-eraser-fill"></i>
                                    </button>
                                    <a href="<?= base_url('fpk') ?>" class="btn btn-sm btn-secondary" data-toggle="tooltip" data-placement="bottom" title="Kembali">
                                        <i class="ri ri-reply-fill"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="no_transaksi">No Transaksi:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="no_transaksi" id="no_transaksi" class="form-control <?= form_error('no_transaksi') ? 'is-invalid' : null; ?>" placeholder="Auto Generate" disabled readonly>
                                            </div>
                                            <div class="text-danger"><?= form_error('no_transaksi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="no_referensi">No Referensi:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-pantone-line"></i>
                                                </span>
                                                <input type="text" name="no_referensi" id="no_referensi" class="form-control <?= form_error('no_referensi') ? 'is-invalid' : null; ?>" placeholder="Enter No Referensi" value="<?= $this->input->post('no_referensi'); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('no_referensi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="supplier">Supplier:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-pantone-line"></i>
                                                </span>
                                                <select name="supplier" id="supplier" class="form-control select2 <?= form_error('supplier') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Supplier --</option>
                                                    <?php foreach ($supplier->result() as $sp): ?>
                                                        <option value="<?= $sp->PERSON_ID ?>" <?= set_value('supplier') ==  $sp->PERSON_ID ? 'selected' : null ?>>
                                                            <?= strtoupper($sp->Supplier) . ' - [' . strtoupper($sp->Kode) . ']' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('supplier') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="tanggal">Tanggal:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-calendar-2-fill"></i>
                                                        </span>
                                                        <input type="datetime-local" name="tanggal" id="tanggal" class="form-control <?= form_error('tanggal') ? 'is-invalid' : null; ?>" placeholder="Enter Tanggal" value="<?= $this->input->post('tanggal'); ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('tanggal') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="tanggal_dibutuhkan">Tanggal Dibutuhkan:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-calendar-event-fill"></i>
                                                        </span>
                                                        <input type="datetime-local" name="tanggal_dibutuhkan" id="tanggal_dibutuhkan" class="form-control <?= form_error('tanggal_dibutuhkan') ? 'is-invalid' : null; ?>" placeholder="Enter Tanggal Dibutuhkan" value="<?= $this->input->post('tanggal_dibutuhkan'); ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('tanggal_dibutuhkan') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gudang">Gudang:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-home-gear-fill"></i>
                                                </span>
                                                <?php
                                                $defaultValue = null;
                                                foreach ($gudang->result() as $gd) {
                                                    if ($gd->PRIMARY_FLAG == 'Y') {
                                                        $defaultValue = $gd->WAREHOUSE_ID;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="gudang" id="gudang" class="form-control select2 <?= form_error('gudang') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Gudang --</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($gudang->result() as $gd): ?>
                                                        <option
                                                            value="<?= $gd->WAREHOUSE_ID ?>"
                                                            <?= set_value('gudang') ==  $gd->WAREHOUSE_ID ? 'selected' : ($defaultValue == $gd->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($gd->WAREHOUSE_NAME) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('gudang') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sales">Sales:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-2-fill"></i>
                                                </span>
                                                <select name="sales" id="sales" class="form-control select2 <?= form_error('sales') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Sales --</option>
                                                    <?php foreach ($sales->result() as $sl): ?>
                                                        <option value="<?= $sl->KARYAWAN_ID ?>" <?= set_value('sales') ==  $sl->KARYAWAN_ID ? 'selected' : null ?>>
                                                            <?= strtoupper($sl->FIRST_NAME) . ' - [' . strtoupper($sl->LAST_NAME) . ']' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('sales') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-xs">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="mb-3">
                                        <label for="keterangan">Keterangan:</label>
                                        <div class="input-group">
                                            <textarea name="keterangan" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"></textarea>
                                        </div>
                                        <div class="text-danger"><?= form_error('keterangan') ?></div>
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
    });
</script>