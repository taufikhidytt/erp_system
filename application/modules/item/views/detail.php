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
                                    <input type="hidden" name="id" id="id" value="<?= $this->encrypt->encode($data->ITEM_ID); ?>">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="code_item">Code Item:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="code_item" id="code_item" class="form-control <?= form_error('code_item') ? 'is-invalid' : null; ?>" value="<?= $this->input->post('code_item') ?? $data->ITEM_CODE; ?>" disabled readonly>
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
                                                    <?php $param = $this->input->post('brand') ?? $data->MEREK_ID; ?>
                                                    <?php foreach ($brand->result() as $br): ?>
                                                        <option value="<?= $br->ERP_LOOKUP_VALUE_ID ?>" <?= $br->ERP_LOOKUP_VALUE_ID == $param ? 'selected' : null ?>><?= strtoupper($br->Brand_Name) ?></option>
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
                                                    <?php $param = $this->input->post('category') ?? $data->GROUP_ID; ?>
                                                    <?php foreach ($category->result() as $ct): ?>
                                                        <option value="<?= $ct->ERP_LOOKUP_VALUE_ID; ?>" <?= $ct->ERP_LOOKUP_VALUE_ID == $param ? 'selected' : null ?> data-name="<?= strtoupper($ct->Category_Name); ?>"><?= strtoupper($ct->Category_Code) ?> ~ [<?= strtoupper($ct->Category_Name); ?>]</option>
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
                                                <input type="text" name="part_number" id="part_number" class="form-control <?= form_error('part_number') ? 'is-invalid' : null; ?>" placeholder="Enter Part Number" value="<?= $this->input->post('part_number') ?? $data->PART_NUMBER; ?>">
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
                                                <textarea name="description" id="description" class="form-control <?= form_error('description') ? 'is-invalid' : null ?>" placeholder="Enter Description"><?= $this->input->post('description') ?? $data->ITEM_DESCRIPTION; ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('description') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="assy_code">Assy Code:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-line"></i>
                                                </span>
                                                <input type="text" name="assy_code" id="assy_code" class="form-control <?= form_error('assy_code') ? 'is-invalid' : null; ?>" placeholder="Enter Assy Code" value="<?= $this->input->post('assy_code') ?? $data->ASSY_CODE; ?>">
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
                                                    <?php $param = $this->input->post('satuan') ?? $data->UOM_CODE; ?>
                                                    <?php foreach ($uom->result() as $um): ?>
                                                        <option value="<?= $um->UOM_CODE ?>" <?= $um->UOM_CODE == $param ? 'selected' : null ?>><?= strtoupper($um->DESCRIPTION) ?></option>
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
                                                    <?php $param = $this->input->post('type') ?? $data->TYPE_ID; ?>
                                                    <?php foreach ($type->result() as $tp): ?>
                                                        <option value="<?= $tp->ERP_LOOKUP_VALUE_ID ?>" <?= $tp->ERP_LOOKUP_VALUE_ID == $param ? 'selected' : null ?>><?= strtoupper($tp->Trade_Type) ?></option>
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
                                                        <input type="number" min="0" name="min_stock" id="min_stock" class="form-control <?= form_error('min_stock') ? 'is-invalid' : null; ?>" placeholder="Enter Min Stock" value="<?= $this->input->post('min_stock') ?? rtrim(rtrim($data->MIN_STOCK, '0'), '.'); ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('min_stock') ?></div>
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
                                                                <input type="number" min="1" name="lead_time" id="lead_time" class="form-control <?= form_error('lead_time') ? 'is-invalid' : null; ?>" placeholder="Enter Lead Time" value="<?= $this->input->post('lead_time') ?? ($data->lead_time ?? '1'); ?>">
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
                                                    <?php $param = $this->input->post('rak') ?? $data->LOKASI_ID; ?>
                                                    <?php foreach ($rak->result() as $rk): ?>
                                                        <option value="<?= $rk->ERP_LOOKUP_VALUE_ID ?>" <?= $rk->ERP_LOOKUP_VALUE_ID == $param ? 'selected' : null ?>><?= strtoupper($rk->Grade) ?></option>
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
                                                        <input type="number" min="1" name="length" id="length" class="form-control <?= form_error('length') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('length') ?? (rtrim(rtrim($data->PANJANG, '0'), '.') ?? '1'); ?>">
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
                                                        <input type="number" min="1" name="width" id="width" class="form-control <?= form_error('width') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('width') ?? (rtrim(rtrim($data->LEBAR, '0'), '.') ?? '1'); ?>">
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
                                                        <input type="number" min="1" name="height" id="height" class="form-control <?= form_error('height') ? 'is-invalid' : null; ?>" placeholder="Meter" value="<?= $this->input->post('height') ?? (rtrim(rtrim($data->TINGGI, '0'), '.') ?? '1'); ?>">
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
                                                        <input type="number" min="1" name="kubikasi" id="kubikasi" class="form-control <?= form_error('kubikasi') ? 'is-invalid' : null; ?>" placeholder="" value="<?= $this->input->post('kubikasi') ?? (rtrim(rtrim($data->M3, '0'), '.') ?? '1'); ?>" disabled>
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
                                                        <input type="number" min="1" name="weight" id="weight" class="form-control <?= form_error('weight') ? 'is-invalid' : null; ?>" placeholder="Kilogram" value="<?= $this->input->post('weight') ?? $data->BERAT; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-12">
                                                <div class="mb-3">
                                                    <label class="mb-4"></label>
                                                    <div class="input-group">
                                                        <select name="satuan_weight" id="satuan_weight" class="form-control select2 <?= form_error('satuan_weight') ? 'is-invalid' : null; ?>">
                                                            <?php $param = $this->input->post('satuan_weight') ?? $data->CUSTOM5; ?>
                                                            <?php foreach ($uom->result() as $um): ?>
                                                                <option value="<?= $um->UOM_CODE ?>" <?= $um->UOM_CODE == $param ? 'selected' : null ?>><?= strtoupper($um->DESCRIPTION) ?></option>
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
                                                    <?php $param = $this->input->post('jenis') ?? $data->JENIS_ID; ?>
                                                    <option value="">-- Selected Jenis --</option>
                                                    <?php foreach ($jenis->result() as $js): ?>
                                                        <option value="<?= $js->ERP_LOOKUP_VALUE_ID ?>" <?= $js->ERP_LOOKUP_VALUE_ID == $param ? 'selected' : null ?> data-name="<?= strtolower($js->Jenis_Item) ?>"><?= strtoupper($js->Jenis_Item) ?></option>
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
                                                    <?php $param = $this->input->post('grade') ?? $data->GRADE_ID; ?>
                                                    <?php foreach ($grade->result() as $gd): ?>
                                                        <option value="<?= $gd->ERP_LOOKUP_VALUE_ID ?>" <?= $gd->ERP_LOOKUP_VALUE_ID == $param ? 'selected' : null ?>><?= strtoupper($gd->Grade) ?></option>
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
                                                    <?php $param = $this->input->post('supplier') ?? $data->PERSON_ID; ?>
                                                    <?php foreach ($supplier->result() as $sp): ?>
                                                        <option value="<?= $sp->PERSON_ID ?>" <?= $sp->PERSON_ID == $param ? 'selected' : null ?>><?= strtoupper($sp->Supplier) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('supplier') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check form-check-right">
                                                <input class="form-check-input" type="checkbox" name="obsolete" id="obsolete" value="Y" <?= (set_value('obsolete', $data->OBSOLETE_FLAG ?? '') === 'Y') ? 'checked' : null ?>>
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
                                                <textarea name="new_product_name" id="new_product_name" class="form-control <?= form_error('new_product_name') ? 'is-invalid' : null ?>" placeholder="Enter New Product Name"><?= $this->input->post('new_product_name') ?? $data->PRODUK_BARU; ?></textarea>
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
                                                <input type="number" name="hpp" id="hpp" class="form-control <?= form_error('hpp') ? 'is-invalid' : null; ?>" placeholder="Enter Hpp" value="<?= $this->input->post('hpp') ?? rtrim(rtrim($data->HPP_AWAL, '0'), '.'); ?>" <?= $status_hpp_flag ?>>
                                            </div>
                                            <div class="text-danger"><?= form_error('hpp') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan">Keterangan:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-sticky-note-fill"></i>
                                                </span>
                                                <textarea name="keterangan" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"><?= $this->input->post('keterangan') ?? $data->NOTE; ?></textarea>
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
                                                        <input type="number" name="min_order_quantity" id="min_order_quantity" class="form-control <?= form_error('min_order_quantity') ? 'is-invalid' : null ?>" value="<?= $this->input->post('min_order_quantity') ?? $data->MOQ; ?>" placeholder="Enter MOQ">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('min_order_quantity') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label class="mb-4"></label>
                                                    <div class="input-group">
                                                        <select name="satuan2" id="satuan2" class="form-control select2 <?= form_error('satuan2') ? 'is-invalid' : null; ?>">
                                                            <?php $param = $this->input->post('satuan2') ?? $data->CUSTOM5; ?>
                                                            <?php foreach ($uom->result() as $um): ?>
                                                                <option value="<?= $um->UOM_CODE ?>" <?= $um->UOM_CODE == $param ? 'selected' : null ?>><?= strtoupper($um->DESCRIPTION) ?></option>
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
                                                    <?php $param = $this->input->post('made_in') ?? $data->MADE_IN_ID; ?>
                                                    <?php foreach ($made_in->result() as $mi): ?>
                                                        <option value="<?= $mi->ERP_LOOKUP_VALUE_ID ?>" <?= $mi->ERP_LOOKUP_VALUE_ID == $param ? 'selected' : null ?>><?= strtoupper($mi->Made_In) ?></option>
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
                                                    <?php $param = $this->input->post('komoditi') ?? $data->TIPE_ID; ?>
                                                    <?php foreach ($komoditi->result() as $kd): ?>
                                                        <option value="<?= $kd->ERP_LOOKUP_VALUE_ID ?>" <?= $kd->ERP_LOOKUP_VALUE_ID == $param ? 'selected' : null ?>><?= strtoupper($kd->Note) ?></option>
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
                                                    <input class="form-check-input" type="checkbox" name="konsinyasi" id="konsinyasi" value="Y" <?= (set_value('konsinyasi', $data->ITEM_KMS ?? '') === 'Y') ? 'checked' : null ?>>
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
                                                    <input class="form-check-input" type="checkbox" name="status_flag" id="status_flag" value="Y" <?= (set_value('status_flag', $data->ACTIVE_FLAG ?? '') === 'Y') ? 'checked' : null ?>>
                                                </div>
                                            </div>
                                            <div class="text-danger"><?= form_error('status_flag') ?></div>
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
                                                <span class="d-block d-sm-none"><i class="ri ri-eye-2-fill"></i></span>
                                                <span class="d-none d-sm-block">Detail</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#account" role="tab" aria-selected="false">
                                                <span class="d-block d-sm-none data-toggle=" tooltip" data-placement="bottom" title="Account"" ><i class=" ri ri-book-mark-fill"></i></span>
                                                <span class="d-none d-sm-block">Account</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#saldo_awal_program" role="tab" aria-selected="false">
                                                <span class="d-block d-sm-none"><i class="ri ri-money-dollar-box-fill"></i></span>
                                                <span class="d-none d-sm-block">Saldo Awal Program</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#harga" role="tab" aria-selected="false">
                                                <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
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
                                            <button type="button" id="addRow" class="btn btn-success btn-sm btn-custom">+</button>
                                            <button type="button" id="removeRow" class="btn btn-danger btn-sm btn-custom">-</button>

                                            <table id="tableSatuan" class="table table-bordered mt-3">
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
                                                                    <input type="hidden" name="id_satuan_uom_detail[]" value="<?= $row['ITEM_UOM_ID']; ?>">
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
                                                                <input type="hidden" name="id_satuan_uom_detail[]" value="0">
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
                        <div class="col-lg-6 col-md-6 col-sm-12 text-start">
                            <?php if ($data->APPROVE_FLAG == 'N'): ?>
                                <form action="<?= base_url('item/approve') ?>" method="post" class="d-inline">
                                    <input type="hidden" name="idApprove" value="<?= $this->encrypt->encode($data->ITEM_ID); ?>">
                                    <button type="submit" id="btn-approve" class="btn btn-primary btn-sm">
                                        <i class="ri ri-thumb-up-fill"></i> Approve
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
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

        $('#min_stock,#hpp').on('input change', function() {
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

        $(document).on('click', '#btn-approve', function(e) {
            e.preventDefault();
            var link = $(this).parent('form');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Ingin approve data ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#43a700ff',
                cancelButtonColor: '#ff0022ff',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    link.submit();
                }
            })
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

            if (formName) {
                let name = formName.replace(/\s+/g, '_');
                $('#' + name).show().find('input, select, textarea').each(function() {
                    $(this).attr('name', $(this).data('name'));
                });
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
                <input type="hidden" name="id_satuan_uom_detail[]" value="0">
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

        // / Auto save baris baru saat blur
        $(document).on("blur", ".auto-save", function() {
            var row = $(this).closest("tr");
            var id = row.find('input[name="id_satuan_uom_detail[]"]').val();
            var idItem = $('#id').val();

            var satuan = row.find('select[name="satuan_lain[]"]').val();
            var konversi = row.find('input[name="konversi[]"]').val();

            // Hanya save jika baris baru
            if (id == 0) {

                if (satuan === "" || konversi === "" || konversi == 0) {
                    return;
                }

                var data = {
                    'id_item': idItem,
                    'id_satuan_uom_detail[]': [row.find('input[name="id_satuan_uom_detail[]"]').val()],
                    'satuan_lain[]': [row.find('select[name="satuan_lain[]"]').val()],
                    'konversi[]': [row.find('input[name="konversi[]"]').val()],
                };
                $('#loading').show();
                $.ajax({
                    url: "<?php echo site_url('item/ajax_save'); ?>",
                    type: "POST",
                    data: data,
                    dataType: "json",
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#loading').hide();
                            row.find('input[name="id_satuan_uom_detail[]"]').val('saved');
                            Swal.fire({
                                title: 'Sukses',
                                text: 'Selamat anda berhasil menyimpan data!',
                                icon: 'success',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                location.reload();
                            });
                        } else if (res.message) {
                            $('#loading').hide();
                            Swal.fire({
                                title: 'Warning',
                                text: res.message,
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            $('#loading').hide();
                            Swal.fire({
                                title: 'Warning',
                                text: 'Gagal save data!',
                                icon: 'warning',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    }
                });
            }
        });

        // Centang semua
        $("#chkAll").change(function() {
            $(".chkRow").prop('checked', $(this).prop('checked'));
        });

        $("#removeRow").click(function() {

            var idsToDelete = [];

            $("#tableSatuan tbody input.chkRow:checked").each(function() {
                var row = $(this).closest("tr");
                var id = row.find('input[name="id_satuan_uom_detail[]"]').val();

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