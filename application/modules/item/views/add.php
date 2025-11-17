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
                        <form action="" method="post">
                            <div class="row mb-2">
                                <div class="offset-lg-6 offset-md-6 col-lg-6 col-md-6 col-sm-12 text-end">
                                    <button type="submit" class="btn btn-success btn-sm" name="submit" id="submit">
                                        <i class="ri ri-check-double-fill"></i> Submit
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.replace(window.location.pathname);">
                                        <i class="ri ri-eraser-fill"></i> Undo
                                    </button>
                                    <a href="<?= base_url('item') ?>" class="btn btn-sm btn-secondary">
                                        <i class="ri ri-reply-fill"></i> Back
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="code_item">Code Item:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="code_item" id="code_item" class="form-control <?= form_error('code_item') ? 'is-invalid' : null; ?>" disabled readonly>
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
                                                <select name="brand" id="brand" class="form-control select2 <?= form_error('brand') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Brand --</option>
                                                    <?php foreach ($brand->result() as $br): ?>
                                                        <option value="<?= $br->ERP_LOOKUP_VALUE_ID ?>" <?= set_value('brand') == $br->ERP_LOOKUP_VALUE_ID ? 'selected' : null ?>><?= strtoupper($br->Brand_Name) ?></option>
                                                    <?php endforeach; ?>
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
                                                <select name="category" id="category" class="form-control select2 <?= form_error('category') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Category --</option>
                                                    <?php foreach ($category->result() as $ct): ?>
                                                        <option value="<?= $ct->ERP_LOOKUP_VALUE_ID; ?>" <?= set_value('category') == $ct->ERP_LOOKUP_VALUE_ID ? 'selected' : null; ?> data-name="<?= strtoupper($ct->Category_Name); ?>"><?= strtoupper($ct->Category_Code) ?> ~ [<?= strtoupper($ct->Category_Name); ?>]</option>
                                                    <?php endforeach; ?>
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
                                                <input type="text" name="part_number" id="part_number" class="form-control <?= form_error('part_number') ? 'is-invalid' : null; ?>" placeholder="Enter Part Number" value="<?= $this->input->post('part_number'); ?>">
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
                                                <select name="satuan" id="satuan" class="form-control select2 <?= form_error('satuan') ? 'is-invalid' : null; ?>">
                                                    <?php foreach ($uom->result() as $um): ?>
                                                        <option value="<?= $um->UOM_CODE ?>" <?= set_value('satuan') == $um->UOM_CODE ? 'selected' : null ?>><?= strtoupper($um->DESCRIPTION) ?></option>
                                                    <?php endforeach; ?>
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
                                                <select name="type" id="type" class="form-control select2 <?= form_error('type') ? 'is-invalid' : null; ?>">
                                                    <?php foreach ($type->result() as $tp): ?>
                                                        <option value="<?= $tp->ERP_LOOKUP_VALUE_ID ?>" <?= set_value('type') == $tp->ERP_LOOKUP_VALUE_ID ? 'selected' : null ?>><?= strtoupper($tp->Trade_Type) ?></option>
                                                    <?php endforeach; ?>
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
                                                        <input type="number" min="0" name="min_stock" id="min_stock" class="form-control <?= form_error('min_stock') ? 'is-invalid' : null; ?>" placeholder="Enter Min Stock" value="<?= $this->input->post('min_stock'); ?>">
                                                    </div>
                                                    <div id="error-min_stock" class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="lead_time">Lead Time:</label>
                                                    <span class="text-danger">*</span>
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
                                                <select name="rak" id="rak" class="form-control select2 <?= form_error('rak') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Rak --</option>
                                                    <?php foreach ($rak->result() as $rk): ?>
                                                        <option value="<?= $rk->ERP_LOOKUP_VALUE_ID ?>" <?= set_value('rak') == $rk->ERP_LOOKUP_VALUE_ID ? 'selected' : null ?>><?= strtoupper($rk->Grade) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-12 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="length">Length:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="1" name="length" id="length" class="form-control <?= form_error('length') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('length') ?? '1'; ?>">
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
                                                        <input type="number" min="1" name="width" id="width" class="form-control <?= form_error('width') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('width') ?? '1'; ?>">
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
                                                        <input type="number" min="1" name="height" id="height" class="form-control <?= form_error('height') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('height') ?? '1'; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="kubikasi">Kubikasi:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="1" name="kubikasi" id="kubikasi" class="form-control <?= form_error('kubikasi') ? 'is-invalid' : null; ?>" placeholder="" value="<?= $this->input->post('kubikasi'); ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="weight">Weight:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" min="1" name="weight" id="weight" class="form-control <?= form_error('weight') ? 'is-invalid' : null; ?>" placeholder="Kilogram" value="<?= $this->input->post('weight'); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="mb-3">
                                                    <label class="mb-4"></label>
                                                    <div class="input-group">
                                                        <select name="satuan_weight" id="satuan_weight" class="form-control select2 <?= form_error('satuan_weight') ? 'is-invalid' : null; ?>">
                                                            <?php foreach ($uom->result() as $um): ?>
                                                                <option value="<?= $um->UOM_CODE ?>" <?= set_value('satuan_weight') == $um->UOM_CODE ? 'selected' : null ?>><?= strtoupper($um->DESCRIPTION) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="text-danger"><?= form_error('satuan_weight') ?></div>
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
                                                <select name="jenis" id="jenis" class="form-control select2 <?= form_error('jenis') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Jenis --</option>
                                                    <?php foreach ($jenis->result() as $js): ?>
                                                        <option value="<?= $js->ERP_LOOKUP_VALUE_ID ?>" <?= set_value('jenis') == $js->ERP_LOOKUP_VALUE_ID ? 'selected' : null ?>><?= strtoupper($js->Jenis_Item) ?></option>
                                                    <?php endforeach; ?>
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
                                                <select name="grade" id="grade" class="form-control select2 <?= form_error('grade') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Grade --</option>
                                                    <?php foreach ($grade->result() as $gd): ?>
                                                        <option value="<?= $gd->ERP_LOOKUP_VALUE_ID ?>" <?= set_value('grade') == $gd->ERP_LOOKUP_VALUE_ID ? 'selected' : null ?>><?= strtoupper($gd->Grade) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('grade') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="supplier">Supplier:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-arrow-up-down-fill"></i>
                                                </span>
                                                <select name="supplier" id="supplier" class="form-control select2 <?= form_error('supplier') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Supplier --</option>
                                                    <?php foreach ($supplier->result() as $sp): ?>
                                                        <option value="<?= $sp->PERSON_ID ?>" <?= set_value('supplier') == $sp->PERSON_ID ? 'selected' : null ?>><?= strtoupper($sp->Supplier) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('supplier') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-check-right">
                                                <input class="form-check-input" type="checkbox" name="obsolete" id="obsolete" value="Y" <?= set_value('obsolete') == 'Y' ? 'checked' : null ?>>
                                                <label class="form-check-label" for="obsolete" style="margin-right: 20px;">
                                                    Obsolete
                                                </label>
                                                <div class="text-danger"><?= form_error('obsolete') ?></div>
                                            </div>
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
                                                <input type="number" name="hpp" id="hpp" class="form-control <?= form_error('hpp') ? 'is-invalid' : null; ?>" placeholder="Enter Hpp" value="<?= $this->input->post('hpp') ?? '0'; ?>" <?= $status_hpp_flag ?>>
                                            </div>
                                            <div class="text-danger"><?= form_error('hpp') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan">Keterangan:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-sticky-note-fill"></i>
                                                </span>
                                                <textarea name="keterangan" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"><?= $this->input->post('keterangan'); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('keterangan') ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="min_order_quantity">MOQ:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-sticky-note-fill"></i>
                                                        </span>
                                                        <input type="number" name="min_order_quantity" id="min_order_quantity" class="form-control <?= form_error('min_order_quantity') ? 'is-invalid' : null ?>" value="<?= $this->input->post('min_order_quantity'); ?>" placeholder="Enter MOQ">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('min_order_quantity') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label class="mb-4"></label>
                                                    <div class="input-group">
                                                        <select name="satuan2" id="satuan2" class="form-control select2 <?= form_error('satuan2') ? 'is-invalid' : null; ?>">
                                                            <?php foreach ($uom->result() as $um): ?>
                                                                <option value="<?= $um->UOM_CODE ?>" <?= set_value('satuan2') == $um->UOM_CODE ? 'selected' : null ?>><?= strtoupper($um->DESCRIPTION) ?></option>
                                                            <?php endforeach; ?>
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
                                                <select name="made_in" id="made_in" class="form-control select2 <?= form_error('made_in') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Made In --</option>
                                                    <?php foreach ($made_in->result() as $mi): ?>
                                                        <option value="<?= $mi->ERP_LOOKUP_VALUE_ID ?>" <?= set_value('made_in') == $mi->ERP_LOOKUP_VALUE_ID ? 'selected' : null ?>><?= strtoupper($mi->Made_In) ?></option>
                                                    <?php endforeach; ?>
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
                                                <select name="komoditi" id="komoditi" class="form-control select2 <?= form_error('komoditi') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Komoditi --</option>
                                                    <?php foreach ($komoditi->result() as $kd): ?>
                                                        <option value="<?= $kd->ERP_LOOKUP_VALUE_ID ?>" <?= set_value('komoditi') == $kd->ERP_LOOKUP_VALUE_ID ? 'selected' : null ?>><?= strtoupper($kd->Note) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('komoditi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="row justify-content-start">
                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                    <label class="form-check-label" for="konsinyasi" style="margin-right: 20px;">
                                                        Konsinyasi
                                                    </label>
                                                </div>
                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                    <input class="form-check-input" type="checkbox" name="konsinyasi" id="konsinyasi" value="Y" <?= set_value('konsinyasi') == 'Y' ? 'checked' : null ?>>
                                                </div>
                                            </div>
                                            <div class="text-danger"><?= form_error('konsinyasi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="row justify-content-start">
                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                    <label class="form-check-label" for="status_flag">
                                                        Status
                                                    </label>
                                                </div>
                                                <div class="col-lg-2 col-md-4 col-sm-6">
                                                    <input class="form-check-input" type="checkbox" name="status_flag" id="status_flag" value="Y" <?= set_value('status_flag') == 'Y' ? 'checked' : null ?>>
                                                </div>
                                            </div>
                                            <div class="text-danger"><?= form_error('status_flag') ?></div>
                                        </div>
                                        <div class="row">

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
    $(document).ready(function() {
        var length = $('#length').val();
        var width = $('#width').val();
        var height = $('#height').val();

        var jumlah = length * width * height;
        $('#kubikasi').val(jumlah);

        $('#new_product_name').prop('disabled', !$('#obsolete').is(':checked'));

        if ($('#obsolete').val() == 'Y') {
            $('#new_product_name_required').html('*');
        } else {
            $('#new_product_name_required').html('');
        }

        $('#new_product_name_required').html('');

        $('#brand, #category').on('change', updateDescription);
        $('#part_number').on('keyup', updateDescription);

        $('#obsolete').on('change', function() {
            if ($(this).is(':checked')) {
                $('#new_product_name_required').html('*');
            } else {
                $('#new_product_name_required').html('');
            }
        });

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

        $('#min_stock, #lead_time, #length, #width, #height, #weight, #hpp, #min_order_quantity').on('keydown', function(e) {
            if (
                e.key === 'e' || e.key === 'E' ||
                e.key === '+' || e.key === '-'
            ) {
                e.preventDefault();
            }
        });

        $('#min_stock, #hpp').on('input change', function() {
            let val = $(this).val();
            if (val === '') return;
            val = parseFloat(val);
            if (val < 0) {
                $(this).val(0);
            }
        });

        $('#lead_time, #length, #width, #height, #weight, #min_order_quantity').on('input change', function() {
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

        $('#obsolete').on('change', function() {
            if ($(this).is(':checked')) {
                $('#new_product_name').prop('disabled', false);
            } else {
                $('#new_product_name').prop('disabled', true).val('');
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