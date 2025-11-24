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
                                <a href="<?= base_url('item') ?>">Item</a>
                            </li>
                            <li class="breadcrumb-item active"><?= $breadcrumb ?></li>
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
                                    <a href="<?= base_url('item') ?>" class="btn btn-sm btn-secondary" data-toggle="tooltip" data-placement="bottom" title="Kembali">
                                        <i class="ri ri-reply-fill"></i>
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
                                                <?php
                                                $defaultValue = null;
                                                foreach ($uom->result() as $um) {
                                                    if ($um->PRIMARY_FLAG == 'Y') {
                                                        $defaultValue = $um->UOM_CODE;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="satuan" id="satuan" class="form-control select2 <?= form_error('satuan') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Satuan --</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($uom->result() as $um): ?>
                                                        <option
                                                            value="<?= $um->UOM_CODE ?>"
                                                            <?= set_value('satuan') ==  $um->UOM_CODE ? 'selected' : ($defaultValue == $um->UOM_CODE ? 'selected' : '') ?>>
                                                            <?= strtoupper($um->DESCRIPTION) ?>
                                                        </option>
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

                                                <?php
                                                $defaultValue = null;
                                                foreach ($type->result() as $tp) {
                                                    if ($tp->Default_Flag == 'Y') {
                                                        $defaultValue = $tp->ERP_LOOKUP_VALUE_ID;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="type" id="type" class="form-control select2 <?= form_error('type') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Type --</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($type->result() as $tp): ?>
                                                        <option
                                                            value="<?= $tp->ERP_LOOKUP_VALUE_ID ?>"
                                                            <?= set_value('type') ==  $tp->ERP_LOOKUP_VALUE_ID ? 'selected' : ($defaultValue == $tp->ERP_LOOKUP_VALUE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($tp->Trade_Type) ?>
                                                        </option>
                                                    <?php endforeach; ?>

                                                </select>


                                                <!-- <select name="type" id="type" class="form-control select2 <?= form_error('type') ? 'is-invalid' : null; ?>">
                                                    <?php foreach ($type->result() as $tp): ?>
                                                        <option value="<?= $tp->ERP_LOOKUP_VALUE_ID ?>" <?= set_value('type') == $tp->ERP_LOOKUP_VALUE_ID ? 'selected' : null ?>><?= strtoupper($tp->Trade_Type) ?></option>
                                                    <?php endforeach; ?>
                                                </select> -->
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
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="ri ri-rocket-2-fill"></i>
                                                            </span>
                                                            <input type="number" min="1" name="lead_time" id="lead_time" class="form-control <?= form_error('lead_time') ? 'is-invalid' : null; ?>" placeholder="Enter Lead Time" value="<?= $this->input->post('lead_time') ?? '1'; ?>">
                                                            <span class="input-group-text">Weeks</span>
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
                                        <div class="border p-2">
                                            <div class="row">
                                                <div class="col-lg-4 col-md-12 col-sm-12">
                                                    <div class="mb-3">
                                                        <label for="length">Length:</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">
                                                                <i class="ri ri-increase-decrease-fill"></i>
                                                            </span>
                                                            <input type="number" min="1" name="length" id="length" class="form-control <?= form_error('length') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('length') ?? '1'; ?>">
                                                            <span class="input-group-text">Meter</span>
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
                                                            <span class="input-group-text">Meter</span>
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
                                                            <span class="input-group-text">Meter</span>
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
                                                            <input type="number" min="1" name="kubikasi" id="kubikasi" class="form-control <?= form_error('kubikasi') ? 'is-invalid' : null; ?>" placeholder="" value="<?= $this->input->post('kubikasi'); ?>" disabled>
                                                            <span class="input-group-text">M3</span>
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
                                                            <input type="number" min="1" name="weight" id="weight" class="form-control <?= form_error('weight') ? 'is-invalid' : null; ?>" placeholder="Kilogram" value="<?= $this->input->post('weight'); ?>">
                                                            <span class="input-group-text">Kg</span>
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
                                                <?php
                                                $defaultValue = null;
                                                foreach ($jenis->result() as $js) {
                                                    if ($js->Default_Flag == 'Y') {
                                                        $defaultValue = $js->ERP_LOOKUP_VALUE_ID;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="jenis" id="jenis" class="form-control select2 <?= form_error('jenis') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Jenis --</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($jenis->result() as $js): ?>
                                                        <option
                                                            value="<?= $js->ERP_LOOKUP_VALUE_ID ?>"
                                                            <?= set_value('jenis') ==  $js->ERP_LOOKUP_VALUE_ID ? 'selected' : ($defaultValue == $js->ERP_LOOKUP_VALUE_ID ? 'selected' : '') ?> data-name="<?= strtolower($js->Jenis_Item) ?>">
                                                            <?= strtoupper($js->Jenis_Item) ?>
                                                        </option>
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
                                                <?php
                                                $defaultValue = null;
                                                foreach ($grade->result() as $gd) {
                                                    if ($gd->Default_Flag == 'Y') {
                                                        $defaultValue = $gd->ERP_LOOKUP_VALUE_ID;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="grade" id="grade" class="form-control select2 <?= form_error('grade') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Grade --</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($grade->result() as $gd): ?>
                                                        <option
                                                            value="<?= $gd->ERP_LOOKUP_VALUE_ID ?>"
                                                            <?= set_value('grade') ==  $gd->ERP_LOOKUP_VALUE_ID ? 'selected' : ($defaultValue == $gd->ERP_LOOKUP_VALUE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($gd->Grade) ?>
                                                        </option>
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
                                                    <label for="min_order_quantity">Min. Ord Qty:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-increase-decrease-fill"></i>
                                                        </span>
                                                        <input type="number" name="min_order_quantity" id="min_order_quantity" class="form-control <?= form_error('min_order_quantity') ? 'is-invalid' : null ?>" value="<?= $this->input->post('min_order_quantity'); ?>" placeholder="Enter Min. Ord Qty">
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
                                                <?php
                                                $defaultValue = null;
                                                foreach ($komoditi->result() as $kd) {
                                                    if ($kd->Default_Flag == 'Y') {
                                                        $defaultValue = $kd->ERP_LOOKUP_VALUE_ID;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="komoditi" id="komoditi" class="form-control select2 <?= form_error('komoditi') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Komoditi --</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($komoditi->result() as $kd): ?>
                                                        <option
                                                            value="<?= $kd->ERP_LOOKUP_VALUE_ID ?>"
                                                            <?= set_value('komoditi') ==  $kd->ERP_LOOKUP_VALUE_ID ? 'selected' : ($defaultValue == $kd->ERP_LOOKUP_VALUE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($kd->Note) ?>
                                                        </option>
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
                                        <!-- <div class="mb-3">
                                            <div class="row justify-content-start">
                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                    <label class="form-check-label" for="status_flag">
                                                        Active
                                                    </label>
                                                </div>
                                                <div class="col-lg-2 col-md-4 col-sm-6">
                                                    <input class="form-check-input" type="checkbox" name="status_flag" id="status_flag" value="Y" <?= set_value('status_flag') == 'Y' ? 'checked' : null ?>>
                                                </div>
                                            </div>
                                            <div class="text-danger"><?= form_error('status_flag') ?></div>
                                        </div> -->
                                        <div class="row">

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
                                            <button type="button" id="addRow" class="btn btn-success btn-sm">+</button>
                                            <button type="button" id="removeRow" class="btn btn-danger btn-sm">-</button>
                                            <div class="table-responsive">
                                                <table id="tableSatuan" class="table table-bordered mt-3 w-100">
                                                    <thead>
                                                        <tr>
                                                            <th><input type="checkbox" id="chkAll"></th>
                                                            <th>No</th>
                                                            <th>Satuan Lain</th>
                                                            <th>Konversi</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($uomChild)): ?>
                                                            <?php foreach ($uomChild as $index => $row): ?>
                                                                <tr>
                                                                    <td>
                                                                        <input type="checkbox" class="chkRow">
                                                                        <input type="hidden" name="id[]" value="<?= $row['ITEM_UOM_ID']; ?>">
                                                                    </td>

                                                                    <td class="rowNo"><?= $index + 1; ?>.</td>

                                                                    <td>
                                                                        <input type="text" name="satuan_lain[]" class="form-control auto-save"
                                                                            value="<?= htmlspecialchars($row['UOM_CODE']); ?>" readonly>
                                                                    </td>

                                                                    <td>
                                                                        <input type="number" name="konversi[]" class="form-control auto-save"
                                                                            step="0.01" value="<?= htmlspecialchars($row['TO_QTY']); ?>" readonly>
                                                                    </td>

                                                                    <td>
                                                                        <input type="text" name="keterangan[]" class="form-control auto-save" value="1 <?= $row['UOM_CODE'] . ' = ' . $row['TO_QTY'] . ' ' . $data->UOM_CODE ?>" readonly>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <tr>
                                                                <td>
                                                                    <input type="checkbox" class="chkRow">
                                                                </td>

                                                                <td class="rowNo">1.</td>

                                                                <td>
                                                                    <select name="satuan_lain[]" class="form-select select-uom auto-save">
                                                                        <option value=""></option>
                                                                        <?php foreach ($uom->result() as $um): ?>
                                                                            <option value="<?= $um->UOM_CODE ?>">
                                                                                <?= $um->DESCRIPTION ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="number" name="konversi[]" class="form-control auto-save">
                                                                </td>

                                                                <td>
                                                                    <input type="text" name="keterangan[]" class="form-control auto-save" disabled>
                                                                </td>
                                                            </tr>
                                                        <?php endif; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane form-xs" id="account" role="tabpanel">
                                            <div class="form-box" id="barang">
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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
                                                <div class="row mb-3">
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

        $('#satuan').on('change', function() {
            let value = $(this).val();
            $('#satuan2').val(value).trigger('change');
        });

        $('#obsolete').on('change', function() {
            if ($(this).is(':checked')) {
                $('#new_product_name_required').html('*');
            } else {
                $('#new_product_name_required').html('');
            }
        });

        //Initialize Select2 Elements
        $('.select2, .select-uom').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this).parent(),
            });
        });

        $(".select-uom").select2({
            width: '100%'
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

            let formName = $(this).find(':selected').data('name');

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
            <?php
            $option_uom = "";
            foreach ($uom->result() as $um) {
                $option_uom .= '<option value="' . $um->UOM_CODE . '">' . strtoupper($um->DESCRIPTION) . '</option>';
            }
            ?>
            var rowCount = $("#tableSatuan tbody tr").length + 1;
            var newRow = `<tr>
            <td><input type="checkbox" class="chkRow"></td>
            <td class="rowNo">${rowCount}.</td>
            <td>
                <select name="satuan_lain[]" class="form-select select-uom auto-save">
                    <?= $option_uom; ?>
                </select>
            </td>
            <td><input type="number" name="konversi[]" class="form-control auto-save"></td>
            <td><input type="text" name="keterangan[]" class="form-control auto-save" disabled></td>
        </tr>`;
            $("#tableSatuan tbody").append(newRow);
            $(".select-uom").select2({
                width: '100%'
            });
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

    // Update nomor urut
    function updateRowNumber() {
        $("#tableSatuan tbody tr").each(function(index) {
            $(this).find(".rowNo").text((index + 1) + ".");
        });
    }
</script>