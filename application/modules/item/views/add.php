<style>
    #tableSatuan tbody td{
        font-family: monospace !important;
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
                                <a href="<?= base_url('item') ?>" class="text-decoration-underline">Item</a>
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
                                        <i class="ri ri-save-3-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.replace(window.location.pathname);" data-toggle="tooltip" data-placement="bottom" title="Reload">
                                        <i class="ri ri-reply-fill"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="code_item">Kode Item:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="code_item" id="code_item" class="form-control <?= form_error('code_item') ? 'is-invalid' : null; ?>" placeholder="Auto Generate" disabled readonly>
                                            </div>
                                            <div class="text-danger"><?= form_error('code_item') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="brand">Brand:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-database-2-fill"></i>
                                                </span>
                                                <select name="brand" id="brand" 
                                                    data-url="api/get_brand"
                                                    data-default="Y"
                                                    data-selected-id="<?= set_value('brand','') ?>"
                                                    class="form-control select2 <?= form_error('brand') ? 'is-invalid' : null; ?>">
                                                    
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('brand') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="category">Category:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-server-fill"></i>
                                                </span>
                                                <select name="category" id="category" class="form-control select2 <?= form_error('category') ? 'is-invalid' : null; ?>"
                                                    data-url="api/get_category"
                                                    data-default="Y"
                                                    placeholder="Select Category"
                                                    data-selected-id="<?= set_value('category','') ?>"
                                                >
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('category') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="part_number">Part Number:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-line"></i>
                                                </span>
                                                <input type="text" name="part_number" id="part_number" class="form-control <?= form_error('part_number') ? 'is-invalid' : null; ?>" placeholder="Enter Part Number" data-value="<?= $this->input->post('part_number'); ?>" value="<?= $this->input->post('part_number'); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('part_number') ?></div>
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
                                            <div class="text-danger"><?= form_error('description') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="assy_code">Assy Code:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-line"></i>
                                                </span>
                                                <input type="text" name="assy_code" id="assy_code" class="form-control <?= form_error('assy_code') ? 'is-invalid' : null; ?>" placeholder="Enter Assy Code" value="<?= $this->input->post('assy_code'); ?>">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="satuan">Satuan:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-quill-pen-fill"></i>
                                                </span>
                    
                                                <select name="satuan" id="satuan" class="form-control select2 <?= form_error('satuan') ? 'is-invalid' : null; ?>"
                                                    data-url="api/get_uom"
                                                    data-default="Y"
                                                    data-selected-id="<?= set_value('satuan','') ?>"
                                                    placeholder="Select Satuan"
                                                    >
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('satuan') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="type">Type:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-swap-fill"></i>
                                                </span>
                                                <select name="type" id="type" class="form-control select2 <?= form_error('type') ? 'is-invalid' : null; ?>"
                                                    data-url="api/get_type"
                                                    data-default="Y"
                                                    data-selected-id="<?= set_value('type','') ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('type') ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="min_stock">Min Stock:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="text" name="min_stock" id="min_stock" 
                                                            data-min="0"
                                                            class="input-number form-control <?= form_error('min_stock') ? 'is-invalid' : null; ?>" placeholder="Enter Min Stock" value="<?= $this->input->post('min_stock') ?? '0'; ?>">
                                                    </div>
                                                    <div id="error-min_stock" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="lead_time">Lead Time:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="row">
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="ri ri-rocket-2-fill"></i>
                                                            </span>
                                                            <input type="text" name="lead_time" id="lead_time" 
                                                                data-min="1"
                                                                data-decimal="0"
                                                                class="input-number form-control <?= form_error('lead_time') ? 'is-invalid' : null; ?>" placeholder="Enter Lead Time" value="<?= $this->input->post('lead_time') ?? '1'; ?>">
                                                            <span class="input-group-text">Week</span>
                                                        </div>
                                                    </div>
                                                    <div class="text-danger"><?= form_error('lead_time') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="rak">Rak:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-archive-drawer-fill"></i>
                                                </span>
                                                <select name="rak" id="rak" class="form-control select2 <?= form_error('rak') ? 'is-invalid' : null; ?>"
                                                    data-url="api/get_rak"
                                                    data-default="Y"
                                                    data-selected-id="<?= set_value('rak','') ?>"
                                                    placeholder="Select Rak">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="border p-2">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-12 col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="length">Length:</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="ri ri-increase-decrease-fill"></i>
                                                            </span>
                                                            <input type="text" min="1" name="length" id="length" 
                                                                data-min="1"
                                                                class="input-number form-control <?= form_error('length') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('length') ?? '1'; ?>">
                                                            <span class="input-group-text">M</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-12 col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="width">Width:</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="ri ri-increase-decrease-fill"></i>
                                                            </span>
                                                            <input type="text" name="width" id="width" 
                                                                data-min="1"
                                                                class="input-number form-control <?= form_error('width') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('width') ?? '1'; ?>">
                                                            <span class="input-group-text">M</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 col-md-12 col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="height">Height:</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="ri ri-increase-decrease-fill"></i>
                                                            </span>
                                                            <input type="text" name="height" id="height"
                                                                data-min="1"
                                                                class="input-number form-control <?= form_error('height') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('height') ?? '1'; ?>">
                                                            <span class="input-group-text">M</span>
                                                        </div>
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
                                                            <input type="text" name="kubikasi" id="kubikasi" 
                                                                data-min="1"
                                                                class="input-number form-control <?= form_error('kubikasi') ? 'is-invalid' : null; ?>" placeholder="" value="<?= $this->input->post('kubikasi'); ?>" disabled>
                                                            <span class="input-group-text">M<sup>3</sup></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="weight">Weight:</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="ri ri-increase-decrease-fill"></i>
                                                            </span>
                                                            <input type="text" name="weight" id="weight" 
                                                                data-min="1" data-decimal="0"
                                                                class="input-number form-control <?= form_error('weight') ? 'is-invalid' : null; ?>" placeholder="Kilogram" value="<?= $this->input->post('weight'); ?>">
                                                            <span class="input-group-text">KG</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="jenis">Jenis:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-arrow-up-down-fill"></i>
                                                </span>
                                                <select name="jenis" id="jenis" class="form-control select2 <?= form_error('jenis') ? 'is-invalid' : null; ?>"
                                                    data-url="api/get_jenis"
                                                    data-default="Y"
                                                    data-selected-id="<?= set_value('jenis','') ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('jenis') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="grade">Grade:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-arrow-up-down-fill"></i>
                                                </span>
                                                <select name="grade" id="grade" class="form-control select2 <?= form_error('grade') ? 'is-invalid' : null; ?>"
                                                    data-url="api/get_grade"
                                                    data-default="Y"
                                                    data-selected-id="<?= set_value('grade','') ?>"
                                                    placeholder="Select Grade">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('grade') ?></div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="hpp">HPP:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-money-dollar-box-fill"></i>
                                                </span>
                                                <?php $hpp_flag = $this->db->query("SELECT OPEN_FLAG FROM period ORDER BY PERIOD_NAME LIMIT 1");
                                                if ($hpp_flag->num_rows() > 0) {
                                                    if ($hpp_flag->row()->OPEN_FLAG == 'Y') {
                                                        $status_hpp_flag = '';
                                                    } else {
                                                        $status_hpp_flag = 'disabled';
                                                    }
                                                }
                                                ?>
                                                <input type="text" name="hpp" id="hpp" class="form-control input-number <?= form_error('hpp') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Enter Hpp" value="<?= $this->input->post('hpp') ?? '0'; ?>" <?= $status_hpp_flag ?> step="any">
                                            </div>
                                            <div class="text-danger"><?= form_error('hpp') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan">Keterangan:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-sticky-note-fill"></i>
                                                </span>
                                                <textarea name="note" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"><?= $this->input->post('keterangan'); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('keterangan') ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="min_order_quantity">Min. Ord Qty:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="text" name="min_order_quantity" id="min_order_quantity"
                                                            data-decimal="0"
                                                            class="input-number form-control <?= form_error('min_order_quantity') ? 'is-invalid' : null ?>" value="<?= $this->input->post('min_order_quantity') ?? '1'; ?>" placeholder="Enter Min. Ord Qty">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('min_order_quantity') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label class="mb-4"></label>
                                                    <div class="input-group">
                                                        <select name="satuan2" id="satuan2" class="form-control select2 <?= form_error('satuan2') ? 'is-invalid' : null; ?>" disabled>
                                                        </select>
                                                    </div>
                                                    <div class="text-danger"><?= form_error('satuan2') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="made_in">Made In:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-swap-fill"></i>
                                                </span>
                                                <select name="made_in" id="made_in" class="form-control select2 <?= form_error('made_in') ? 'is-invalid' : null; ?>"
                                                    data-url="api/get_made_in"
                                                    data-default="Y"
                                                    data-selected-id="<?= set_value('made_in','') ?>"
                                                    placeholder="Select Made In"
                                                >
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="komoditi">Komoditi:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-pen-nib-fill"></i>
                                                </span>
                                                <select name="komoditi" id="komoditi" class="form-control select2 <?= form_error('komoditi') ? 'is-invalid' : null; ?>"
                                                    data-url="api/get_komoditi"
                                                    data-default="Y"
                                                    data-selected-id="<?= set_value('komoditi','') ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('komoditi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="supplier">Supplier:</label>
                                            <span class="text-danger" id="supplier_required"></span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-arrow-up-down-fill"></i>
                                                </span>
                                                <select name="supplier" id="supplier" class="form-control select2 <?= form_error('supplier') ? 'is-invalid' : null; ?>" 
                                                    data-url="api/get_supplier"
                                                    data-selected-id="<?= set_value('supplier','') ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('supplier') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="konsinyasi">Konsinyasi:</label>
                                            <div class="input-group">
                                                <div class="form-check form-switch mb-3" dir="ltr">
                                                    <input type="checkbox" name="konsinyasi" class="form-check-input" id="konsinyasi" <?= set_value('konsinyasi') == 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="konsinyasi-text"></label>
                                                </div>
                                            </div>
                                            <div class="text-danger"><?= form_error('konsinyasi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="obsolete">Obsolete:</label>
                                            <div class="input-group">
                                                <div class="form-check form-switch" dir="ltr">
                                                    <input type="checkbox" name="obsolete" class="form-check-input" id="obsolete" <?= set_value('obsolete') == 'Y' ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="obsolete-text"></label>
                                                </div>
                                            </div>
                                            <div class="text-danger"><?= form_error('obsolete') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="new_product_name">New Product Name:</label>
                                            <span class="text-danger" id="new_product_name_required"></span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-sticky-note-2-fill"></i>
                                                </span>
                                                <textarea name="new_product_name" id="new_product_name" class="form-control <?= form_error('new_product_name') ? 'is-invalid' : null ?>" placeholder="Enter New Product Name"><?= $this->input->post('new_product_name'); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('new_product_name') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <hr class="m-3">
                                <div class="card-body border m-3">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#detail" role="tab" aria-selected="true">
                                                <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Detail"><i class="ri ri-eye-2-fill"></i></span>
                                                <span class="d-none d-sm-block">Detail</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#account" role="tab" aria-selected="false">
                                                <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Account"><i class=" ri ri-book-mark-fill"></i></span>
                                                <span class="d-none d-sm-block">Account</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#saldo_awal_program" role="tab" aria-selected="false">
                                                <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Saldo Awal Program"><i class="ri ri-money-dollar-box-fill"></i></span>
                                                <span class="d-none d-sm-block">Saldo Awal Program</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#harga" role="tab" aria-selected="false">
                                                <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Harga"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Harga</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#diskon" role="tab" aria-selected="false">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                <span class="d-none d-sm-block">Diskon</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content p-3 text-muted">
                                        <div class="tab-pane active form-xs" id="detail" role="tabpanel">
                                            <button type="button" id="addRow" class="btn btn-success btn-sm" style="width: 30px;">+</button>
                                            <button type="button" id="removeRow" class="btn btn-danger btn-sm" style="width: 30px;">-</button>
                                            <div class="table-responsive">
                                                <table id="tableSatuan" class="table mt-3 w-100 table-sm align-middle">
                                                    <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
                                                        <tr>
                                                            <th class="text-center"><input type="checkbox" id="chkAll"></th>
                                                            <th class="text-center">No</th>
                                                            <th>Satuan Lain</th>
                                                            <th>Konversi</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($uomChild)): ?>
                                                            <?php foreach ($uomChild as $index => $row): ?>
                                                                <tr>
                                                                    <td class="text-center">
                                                                        <input type="checkbox" class="chkRow">
                                                                        <input type="hidden" name="id[]" value="<?= $row['ITEM_UOM_ID']; ?>">
                                                                    </td>

                                                                    <td class="rowNo text-center"><?= $index + 1; ?></td>

                                                                    <td>
                                                                        <input type="text" name="satuan_lain[]" class="form-control auto-save"
                                                                            value="<?= htmlspecialchars($row['UOM_CODE']); ?>" readonly>
                                                                    </td>

                                                                    <td>
                                                                        <input type="text" name="konversi[]" class="input-number form-control auto-save"
                                                                            value="<?= htmlspecialchars($row['TO_QTY']); ?>" readonly>
                                                                    </td>

                                                                    <td>
                                                                        <input type="text" name="note[]" class="form-control auto-save" value="1 <?= $row['UOM_CODE'] . ' = ' . $row['TO_QTY'] . ' ' . $data->UOM_CODE ?>" readonly>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane form-xs" id="account" role="tabpanel">
                                            <div class="form-box" id="barang">
                                                <div class="row">
                                                    <label for="acc_persediaan" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Persediaan</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_persediaan" id="acc_persediaan" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_persediaan');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_ID) ? $data->COA_ID : $acc_persediaan->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_persediaan" id="code_acc_persediaan" class="form-control" value="<?= $this->input->post('code_acc_persediaan'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_utang_suspend" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Utang Suspend</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_utang_suspend" id="acc_utang_suspend" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_utang_suspend');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_SUSPEND_ID) ? $data->COA_SUSPEND_ID : $acc_utang_suspend->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_utang_suspend" id="code_acc_utang_suspend" class="form-control" value="<?= $this->input->post('code_acc_utang_suspend'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_hpp" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. HPP</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_hpp" id="acc_hpp" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_hpp');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_HPP_ID) ? $data->COA_HPP_ID : $acc_hpp->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_hpp" id="code_acc_hpp" class="form-control" value="<?= $this->input->post('code_acc_hpp'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_penjualan_barang" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Penjualan Barang</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_penjualan_barang" id="acc_penjualan_barang" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_penjualan_barang');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_JUAL_ID) ? $data->COA_JUAL_ID : $acc_penjualan_barang->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_penjualan_barang" id="code_acc_penjualan_barang" class="form-control" value="<?= $this->input->post('code_acc_penjualan_barang'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_retur_penjualan" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Ret. Penjualan</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_retur_penjualan" id="acc_retur_penjualan" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_retur_penjualan');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_RET_JUAL_ID) ? $data->COA_RET_JUAL_ID : $acc_retur_penjualan->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_retur_penjualan" id="code_acc_retur_penjualan" class="form-control" value="<?= $this->input->post('code_acc_retur_penjualan'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_retur_pembelian" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Ret. Pembelian</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_retur_pembelian" id="acc_retur_pembelian" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_retur_pembelian');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_RET_BELI_ID) ? $data->COA_RET_BELI_ID : $acc_retur_pembelian->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_retur_pembelian" id="code_acc_retur_pembelian" class="form-control" value="<?= $this->input->post('code_acc_retur_pembelian'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_disc_penjualan" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Disc. Penjualan</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_disc_penjualan" id="acc_disc_penjualan" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_disc_penjualan');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_DISC_JUAL_ID) ? $data->COA_DISC_JUAL_ID : $acc_disc_penjualan->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_disc_penjualan" id="code_acc_disc_penjualan" class="form-control" value="<?= $this->input->post('code_acc_disc_penjualan'); ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-box" id="jasa">
                                                <div class="row">
                                                    <label for="acc_penjualan_jasa" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Penjualan Jasa</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_penjualan_jasa" id="acc_penjualan_jasa" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_penjualan_jasa');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_JUAL_ID) ? $data->COA_JUAL_ID : $acc_penjualan_jasa->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_penjualan_jasa" id="code_acc_penjualan_jasa" class="form-control" value="<?= $this->input->post('code_acc_penjualan_jasa'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_pembelian" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Pembelian</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_pembelian" id="acc_pembelian" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_pembelian');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_ID) ? $data->COA_ID : $acc_pembelian->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_pembelian" id="code_acc_pembelian" class="form-control" value="<?= $this->input->post('code_acc_pembelian'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_disc_penjualan_jasa" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Disc. Penjualan</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_disc_penjualan_jasa" id="acc_disc_penjualan_jasa" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_disc_penjualan_jasa');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_DISC_JUAL_ID) ? $data->COA_DISC_JUAL_ID : $acc_disc_penjualan_jasa->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_disc_penjualan_jasa" id="code_acc_disc_penjualan_jasa" class="form-control" value="<?= $this->input->post('code_acc_disc_penjualan_jasa'); ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-box" id="uang_muka">
                                                <div class="row">
                                                    <label for="acc_pembelian_uang_muka" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Pembelian</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_pembelian_uang_muka" id="acc_pembelian_uang_muka" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_pembelian_uang_muka');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_ID) ? $data->COA_ID : $acc_pembelian_uang_muka->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_pembelian_uang_muka" id="code_acc_pembelian_uang_muka" class="form-control" value="<?= $this->input->post('code_acc_pembelian_uang_muka'); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label for="acc_penjualan_uang_muka" class="col-lg-2 col-md-2 col-sm-12 col-form-label">Acc. Penjualan</label>
                                                    <div class="col-lg-8 col-md-8 col-sm-12 mb-3 mb-sm-3">
                                                        <select name="acc_penjualan_uang_muka" id="acc_penjualan_uang_muka" class="form-control select2">
                                                            <?php
                                                            $param = $this->input->post('acc_penjualan_uang_muka');

                                                            if (empty($param)) {
                                                                $param = !empty($data->COA_JUAL_ID) ? $data->COA_JUAL_ID : $acc_penjualan_uang_muka->COA_ID;
                                                            }
                                                            ?>
                                                            <?php foreach ($account->result() as $ac): ?>
                                                                <option value="<?= $ac->COA_ID ?>" <?= $ac->COA_ID == $param ? 'selected' : null ?> data-code="<?= $ac->COA_CODE ?>"><?= $ac->COA_NAME ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-2 col-md-2 col-sm-12">
                                                        <input type="text" name="code_acc_penjualan_uang_muka" id="code_acc_penjualan_uang_muka" class="form-control" value="<?= $this->input->post('code_acc_penjualan_uang_muka'); ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="saldo_awal_program" role="tabpanel">
                                            <h5 class="mb-0">Module Saldo Awal Coming Soon</h5>
                                        </div>
                                        <div class="tab-pane" id="harga" role="tabpanel">
                                            <h5 class="mb-0">Module Harga Coming Soon</h5>
                                        </div>
                                        <div class="tab-pane" id="diskon" role="tabpanel">
                                            <h5 class="mb-0">Module Diskon Coming Soon</h5>
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
<!-- container-fluid -->
</div>
<!-- End Page-content -->

<script>
    let xhr = null;
    let temp_uom = {};
    $(document).ready(function() {
        var length = $('#length').inputNumber('getValue') || 0;
        var width = $('#width').inputNumber('getValue') || 0;
        var height = $('#height').inputNumber('getValue') || 0;

        var jumlah = length * width * height;
        $('#kubikasi').val(jumlah);

        $('#new_product_name').prop('disabled', !$('#obsolete').is(':checked'));

        const obsolete = $('#obsolete').val();

        if (obsolete.trim() === 'Y') {
            $('#new_product_name_required').html('*');
        } else {
            $('#new_product_name_required').html('');
        }

        $('#supplier').prop('disabled', !$('#konsinyasi').is(':checked'));

        const konsinyasi = $('#konsinyasi').val();

        if (konsinyasi.trim() === 'Y') {
            $('#supplier_required').html('*');
        } else {
            $('#supplier_required').html('');
        }

        $('#brand, #category').on('change', updateDescription);
        $('#part_number').on('keyup', updateDescription);

        $('#satuan').on('change', function() {
            let value = $(this).val();
            $('#satuan2').empty().append($(this).find('option:selected').clone()).val(value).trigger('change');


            $('#tableSatuan tbody').empty();
            getKonversiUom();
        });

        //Initialize Select2 Elements
        // $('.select2, .select-uom').each(function() {
        //     $(this).select2({
        //         theme: 'bootstrap-5',
        //         dropdownParent: $(this).parent(),
        //     });
        // });

        $(".select-uom").select2({
            width: '100%',
            templateResult: function(data) {
                var to_qty = parseFloat($(data.element).data('to_qty')) || 0;
                if (!data.element || to_qty === 0) { return data.text; }

                
                const to_qty_format = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                    }).format(to_qty);
                
                return $('<span>' + data.text.toUpperCase() + ' - [' + to_qty_format + ']</span>');
            },
            templateSelection: function(data) {
                return data.text; 
            }
        });

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

        $('#min_stock, #lead_time, #length, #width, #height, #weight, #min_order_quantity').on('keydown', function(e) {
            if (
                e.key === 'e' || e.key === 'E' ||
                e.key === '+' || e.key === '-'
            ) {
                e.preventDefault();
            }
        });

        $('#lead_time, #length, #width, #height, #weight, #min_order_quantity').on('input change', function() {
            let val = $(this).inputNumber('getValue');
            if (val === '') return;
            val = parseFloat(val);
            if (val < 1) {
                $(this).val(1);
            }

            let p = parseFloat($('#length').inputNumber('getValue')) || 0;
            let l = parseFloat($('#width').inputNumber('getValue')) || 0;
            let t = parseFloat($('#height').inputNumber('getValue')) || 0;

            let kubikasi = p * l * t;

            if (kubikasi > 0) {
                $('#kubikasi').val(kubikasi).inputNumber();
            } else {
                $('#kubikasi').val('');
            }
        });

        $('#obsolete').on('change', function() {
            if ($(this).is(':checked')) {
                $('#new_product_name_required').html('*');
                $('#new_product_name').prop('disabled', false);
            } else {
                $('#new_product_name_required').html('');
                $('#new_product_name').prop('disabled', true);
            }
        });

        $('#konsinyasi').on('change', function() {
            if ($(this).is(':checked')) {
                $('#supplier_required').html('*');
                $('#supplier').prop('disabled', false);
            } else {
                $('#supplier_required').html('');
                $('#supplier').prop('disabled', true);
            }
        });

        $('#acc_persediaan').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_persediaan').val(code);
        });
        $('#acc_persediaan').change();

        $('#acc_utang_suspend').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_utang_suspend').val(code);
        });
        $('#acc_utang_suspend').change();

        $('#acc_hpp').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_hpp').val(code);
        });
        $('#acc_hpp').change();

        $('#acc_penjualan_barang').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_penjualan_barang').val(code);
        });
        $('#acc_penjualan_barang').change();

        $('#acc_retur_penjualan').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_retur_penjualan').val(code);
        });
        $('#acc_retur_penjualan').change();

        $('#acc_retur_pembelian').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_retur_pembelian').val(code);
        });
        $('#acc_retur_pembelian').change();

        $('#acc_disc_penjualan').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_disc_penjualan').val(code);
        });
        $('#acc_disc_penjualan').change();

        $('#acc_penjualan_jasa').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_penjualan_jasa').val(code);
        });
        $('#acc_penjualan_jasa').change();

        $('#acc_pembelian').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_pembelian').val(code);
        });
        $('#acc_pembelian').change();

        $('#acc_disc_penjualan_jasa').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_disc_penjualan_jasa').val(code);
        });
        $('#acc_disc_penjualan_jasa').change();

        $('#acc_pembelian_uang_muka').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_pembelian_uang_muka').val(code);
        });
        $('#acc_pembelian_uang_muka').change();

        $('#acc_penjualan_uang_muka').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#code_acc_penjualan_uang_muka').val(code);
        });
        $('#acc_penjualan_uang_muka').change();

        $('#jenis').on('change', function() {

            // let formName = $(this).find('option:selected').data('name');
            let formName = $(this).find('option:selected').text().toLowerCase();

            // sembunyikan form & hapus name input
            $('.form-box').hide().find('input, select, textarea').each(function() {
                $(this).data('name', $(this).attr('name')).removeAttr('name');
            });

            // $('.form-box').hide();

            if (formName) {
                let name = formName.replace(/\s+/g, '_');
                $('#' + name).show().find('input, select, textarea').each(function() {
                    $(this).attr('name', $(this).data('name'));
                });
                // $('#' + name).show();
            }
        });

        $('#jenis').trigger('change');

        // Tambah baris baru
        $("#addRow").click(function() {
            let option_uom = '<option value="">&nbsp;</option>';
            $.each(temp_uom, function(k, uom){
                const to_qty = parseFloat(uom.TO_QTY) || 0;
                option_uom += `<option value="${uom.UOM_CODE}" class="text-uppercase" data-to_qty="${to_qty}">${uom.UOM_CODE}</option>`;
            });

            var rowCount = $("#tableSatuan tbody tr").length + 1;
            var newRow = `<tr>
            <td class="text-center"><input type="checkbox" class="chkRow"></td>
            <td class="rowNo text-center">${rowCount}</td>
            <td>
                <select name="satuan_lain[]" class="form-select select-uom auto-save">${option_uom}</select>
            </td>
            <td><input type="text" name="konversi[]" class="input-number form-control auto-save"></td>
            <td><input type="text" name="note[]" class="form-control auto-save" disabled></td>
        </tr>`;
            $("#tableSatuan tbody").append(newRow);
            const last_tr = $("#tableSatuan tbody tr:last");
            last_tr.find(".select-uom").select2({
                width: '100%',
                templateResult: function(data) {
                    var to_qty = parseFloat($(data.element).data('to_qty')) || 0;
                    if (!data.element || to_qty === 0) { return data.text; }

                    
                    const to_qty_format = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                        }).format(to_qty);
                    
                    return $('<span>' + data.text.toUpperCase() + ' - [' + to_qty_format + ']</span>');
                },
                templateSelection: function(data) {
                    return data.text; 
                }
            });
            $('[data-input-number], .input-number').inputNumber();
        });

        // Centang semua
        $("#chkAll").change(function() {
            $(".chkRow").prop('checked', $(this).prop('checked'));
        });

        $("#removeRow").click(function() {

            var idsToDelete = [];

            $("#tableSatuan tbody input.chkRow:checked").each(function() {
                var row = $(this).closest("tr");
                var id = row.find('input[name="id[]"]').val();

                if (id > 0) {
                    idsToDelete.push(id);
                }

                row.remove();
            });

            updateRowNumber();

            if (idsToDelete.length > 0) {
                $('#loading').show();
                $.ajax({
                    url: "<?= site_url('item/ajax_delete'); ?>",
                    type: "POST",
                    data: {
                        ids: idsToDelete
                    },
                    dataType: "json",
                    success: function(res) {
                        $('#loading').hide();
                        Swal.fire({
                            title: 'Selamat!',
                            text: 'Anda berhasil menghapus data!',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $('#loading').hide();
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Hapus data!',
                            icon: 'error',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            location.reload();
                        });
                    }
                });
            }

            checkKonversiUom();
            $('#chkAll').prop('checked', false);
        });

        // Blok input & format tampilan
        $(document).on("keyup", 'input[name="konversi[]"]', function(e) {

            // Blok e, E, +, -
            if (['e', 'E', '+', '-'].includes(e.key)) {
                e.preventDefault();
                this.value = this.value.replace(/[eE+\-]/g, "");
                return;
            }
        });
    });

    const $switch = $('#konsinyasi');
    const $label = $('label[for="konsinyasi-text"]');

    updateLabel();
    $switch.on('change', updateLabel);

    function updateLabel() {
        $label.text($switch.is(':checked') ? 'Yes' : 'No');
    }

    let descriptionTimeout = null;
    let select2LoadCount = 0;
    const select2Total = 2;

    function updateDescription() {
        clearTimeout(descriptionTimeout);
        descriptionTimeout = setTimeout(function() {
            const d_brand       = $('#brand').attr('data-selected-id') || '';
            const d_category    = $('#category').attr('data-selected-id') || '';
            const d_part_number = $('#part_number').attr('data-value') || '';

            const o_brand       = $('#brand option:selected').val() || '';
            const o_category    = $('#category option:selected').val() || '';
            const o_part_number = $('#part_number').val() || '';

            // Jika semua masih sama dengan default, skip (masih load awal)
            if (o_brand === '' && o_category === '') return;

            if (
                d_brand     !== o_brand     ||
                d_category  !== o_category  ||
                d_part_number !== o_part_number
            ) {
                let brandText    = $('#brand option:selected').data('name') || '';
                let categoryText = $('#category option:selected').data('name') || '';
                let partNumber   = o_part_number;

                let parts = [brandText, categoryText, partNumber].filter(val => val !== '');
                $('#description').val(parts.join(' '));
            }
        }, 300);
    }

    // Update nomor urut
    function updateRowNumber() {
        $("#tableSatuan tbody tr").each(function(index) {
            $(this).find(".rowNo").text((index + 1));
        });
    }

    $(document).ready(function(){
        getKonversiUom();
        $('#obsolete').trigger('change');
    })
    function getKonversiUom(){
        $('#loading').show();
        if(xhr){
            xhr.abort();
        }
        xhr = $.ajax({
            url: "<?= site_url('item/get_konversi_uom'); ?>",
            type: "POST",
            data: {
                uom : $('#satuan option:selected').val()
            },
            dataType: "json",
            success: function(res) {
                $('#loading').hide();
                temp_uom = res.result;
                if($('#tableSatuan tbody tr').length===0){
                    $('#addRow').trigger('click');
                }
            }
        });
    }

    
    $(document).on('change', '#tableSatuan tbody select', function(){
        checkKonversiUom();
        const e_opt     = $(this).find('option:selected');
        const to_qty    = parseFloat(e_opt.attr('data-to_qty')) || 0;
        $(this).closest('tr').find('[name="konversi[]"]').inputNumber('setValue', to_qty);
    });
    $(document).on('input change', '#tableSatuan tbody select, #tableSatuan tbody [name="konversi[]"]', function(){
        const tr        = $(this).closest('tr');

        const to_uom    = $('#satuan option:selected').val();

        const from_opt  = tr.find('select option:selected');
        const to_qty    = parseFloat(tr.find('[name="konversi[]"]').inputNumber('getValue')) || 0;
        const from_uom  = from_opt.val();
        
        const to_qty_format = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
            }).format(to_qty);
        tr.find('[name="note[]"]').val(`1 ${from_uom} = ${to_qty_format} ${to_uom}`);
    });
    // cek apakah konversi satuan sudah ada data yang dipilih, jika ada read only di #satuan
    function checkKonversiUom(){
        let is_disabled = false;
        $.each($(document).find('#tableSatuan tbody select'), function(){
            const val = $(this).find('option:selected').val();
            if(val){
                is_disabled = true;
            }
        });
        $('#satuan').attr('disabled',is_disabled);
    }

    $('form').on('submit', function(e){
        $('#satuan').prop('disabled', false);
        $.each($(document).find('[data-input-number], .input-number'), function(){
            $(this).val($(this).inputNumber('getValue'));
        });
        HTMLFormElement.prototype.submit.call(this);
    });

    $(document).on('change', '#obsolete', function(){
        $('label[for="obsolete-text"]').text($(this).is(':checked') ? 'Yes' : 'No');
    });
</script>