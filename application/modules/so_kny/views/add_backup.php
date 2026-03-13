<style>
    .form-xs textarea.form-control {
        height: 80px !important;
        min-height: 30px !important;
        padding: 2px 6px !important;
        font-size: 0.75rem !important;
    }

    .view-mode {
        cursor: pointer;
    }

    .form-xs textarea {
        resize: vertical;
    }

    /* class untuk text yang mau di-ellipsis */
    .ellipsis {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .tr-height-30 td {
        padding-top: 1px !important;
        padding-bottom: 1px !important;
        line-height: 25px;
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid #dee2e6 !important;
    }
</style>

<div id="flashSuccess" data-success="<?= $this->session->flashdata('success'); ?>"></div>
<div id="flashWarning" data-warning="<?= $this->session->flashdata('warning'); ?>"></div>
<div id="flashError" data-error="<?= $this->session->flashdata('error'); ?>"></div>

<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('mrq') ?>" class="text-decoration-underline">SO KNY</a>
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
                                            <label for="customer">Customer:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-2-fill"></i>
                                                </span>
                                                <select name="customer" id="customer" class="form-control select2 <?= form_error('customer') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Customer --</option>
                                                    <?php foreach ($customer->result() as $cs): ?>
                                                        <option value="<?= $cs->PERSON_ID ?>" <?= set_value('customer') ==  $cs->PERSON_ID ? 'selected' : null ?> data-person_site_id="<?= $cs->PERSON_SITE_ID ?>">
                                                            <?= strtoupper($cs->PERSON_NAME) . ' - [' . strtoupper($cs->PERSON_CODE) . '] - ' . strtoupper($cs->SITE_NAME) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('customer') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="location">Location:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-map-2-fill"></i>
                                                </span>
                                                <input type="text" name="location" id="location" class="form-control" placeholder="Enter Location" readonly>
                                                <input type="hidden" name="location_id" id="location_id" class="form-control" readonly>
                                            </div>
                                            <div class="text-danger"><?= form_error('location') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="address" id="address" class="form-control" disabled></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tanggal">Tanggal:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-calendar-2-fill"></i>
                                                </span>
                                                <?php date_default_timezone_set('Asia/Jakarta'); ?>
                                                <input type="datetime-local" name="tanggal" id="tanggal" class="form-control <?= form_error('tanggal') ? 'is-invalid' : null; ?>" placeholder="Enter Tanggal" value="<?= $this->input->post('tanggal') ?? date('Y-m-d\TH:i') ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('tanggal') ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="payment_term">Payment Term:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-money-dollar-box-fill"></i>
                                                        </span>
                                                        <?php
                                                        $defaultValue = null;
                                                        foreach ($payment_term->result() as $pt) {
                                                            if ($pt->PRIMARY_FLAG == 'Y') {
                                                                $defaultValue = $pt->PAYMENT_TERM_ID;
                                                                break;
                                                            }
                                                        }
                                                        ?>
                                                        <select name="payment_term" id="payment_term" class="form-control select2 <?= form_error('payment_term') ? 'is-invalid' : null; ?>">
                                                            <?php if (!$defaultValue): ?>
                                                                <option value="">-- Selected payment_term --</option>
                                                            <?php endif; ?>
                                                            <?php foreach ($payment_term->result() as $pt): ?>
                                                                <option
                                                                    value="<?= $pt->PAYMENT_TERM_ID ?>"
                                                                    <?= set_value('payment_term') ==  $pt->PAYMENT_TERM_ID ? 'selected' : ($defaultValue == $pt->PAYMENT_TERM_ID ? 'selected' : '') ?> data-number="<?= $pt->NUMBER_DAYS ?>">
                                                                    <?= strtoupper($pt->PAYMENT_TERM_NAME) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="text-danger"><?= form_error('payment_term') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="jatuh_tempo">Jatuh Tempo:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-calendar-2-fill"></i>
                                                        </span>
                                                        <?php date_default_timezone_set('Asia/Jakarta'); ?>
                                                        <input type="datetime-local" name="jatuh_tempo" id="jatuh_tempo" class="form-control <?= form_error('jatuh_tempo') ? 'is-invalid' : null; ?>" placeholder="Enter Jatuh Tempo" value="<?= $this->input->post('jatuh_tempo') ?? date('Y-m-d\TH:i') ?>" readonly>
                                                    </div>
                                                    <div class="text-danger"><?= form_error('jatuh_tempo') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="storage">Storage:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-building-fill"></i>
                                                </span>
                                                <?php
                                                $defaultValue = null;
                                                foreach ($storage->result() as $st) {
                                                    if ($st->PRIMARY_FLAG == 'Y') {
                                                        $defaultValue = $st->WAREHOUSE_ID;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="storage" id="storage" class="form-control select2 <?= form_error('storage') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Storage --</option>
                                                    <?php endif; ?>
                                                    <?php foreach ($storage->result() as $ms): ?>
                                                        <option
                                                            value="<?= $ms->WAREHOUSE_ID ?>"
                                                            <?= set_value('storage') ==  $ms->WAREHOUSE_ID ? 'selected' : ($defaultValue == $ms->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($ms->WAREHOUSE_NAME) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('storage') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sales">Sales:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-2-fill"></i>
                                                </span>
                                                <select name="sales" id="sales" class="form-control select2">
                                                    <option value="">-- Selected Sales --</option>
                                                    <?php foreach ($sales->result() as $sl): ?>
                                                        <option value="<?= $sl->KARYAWAN_ID ?>" <?= set_value('sales') == $sl->KARYAWAN_ID ? 'selected' : null ?>><?= $sl->FIRST_NAME . " " . $sl->LAST_NAME ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('sales') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="po_customer">PO Customer:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-profile-fill"></i>
                                                </span>
                                                <input type="text" name="po_customer" id="po_customer" class="form-control <?= form_error('po_customer') ? 'is-invalid' : null; ?>" placeholder="Enter PO Customer" value="<?= $this->input->post('po_customer'); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('po_customer') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="no_reff">No Reff:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-price-tag-3-fill"></i>
                                                </span>
                                                <input type="text" name="no_reff" id="no_reff" class="form-control <?= form_error('no_reff') ? 'is-invalid' : null; ?>" placeholder="Enter No Reff" value="<?= $this->input->post('no_reff'); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('no_reff') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan">Keterangan:</label>
                                            <div class="input-group">
                                                <textarea name="keterangan" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"><?= $this->input->post('keterangan'); ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('keterangan') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="card-body">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#detail" role="tab" aria-selected="true">
                                                <span class="d-block d-sm-none"><i class="ri ri-eye-2-fill"></i></span>
                                                <span class="d-none d-sm-block">Detail</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
                                    <div class="tab-content py-3 text-muted">
                                        <div class="tab-pane active" id="detail" role="tabpanel">
                                            <button type="button" id="removeRow" class="btn btn-danger btn-sm" style="width: 55px;">
                                                <i class="fa fa-trash"></i> Del
                                            </button>
                                            <button type="button" id="btn-modalMrq" class="btn btn-success btn-sm">
                                                <i class="ri ri-add-box-fill"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive overflow-auto" style="max-height: 450px;">
                                        <table class="table table-striped table-bordered" id="table-detail">
                                            <thead style="position: sticky; top: 0; background: #3d7bb9; z-index: 10; color:#ffff;">
                                                <tr>
                                                    <th>No</th>
                                                    <th>
                                                        <input type="checkbox" name="checkAllParent" id="checkAllParent" class="">
                                                    </th>
                                                    <th>No MR</th>
                                                    <th>Nama Item</th>
                                                    <th>Kode Item</th>
                                                    <th>Memo</th>
                                                    <th>Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>Harga Input</th>
                                                    <th>Harga</th>
                                                    <th>Disc.Rp</th>
                                                    <th>Disc.%</th>
                                                    <th>Subtotal</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <table class="table ">
                                                <tbody>
                                                    <tr>
                                                        <td style="vertical-align:middle; font-weight:bold;">Diskon</td>
                                                        <td>:</td>
                                                        <td class="text-left">
                                                            <input id="cal_diskon_percen" name="TOTAL_DISCOUNT_PERCEN" class="form-control form-control-sm input-container" placeholder="Persen" data-mode="false" style="width: 130px;">
                                                        </td>
                                                        <td class="text-center">%</td>
                                                        <td class="text-center">=</td>
                                                        <td class="text-right">
                                                            <input type="text" id="cal_diskon_price" name="TOTAL_DISKON_INPUT" class="form-control form-control-sm input-container" placeholder="Rupiah">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="vertical-align:middle; font-weight:bold;">PPN</td>
                                                        <td>:</td>
                                                        <td colspan="3" class="text-left">
                                                            <?php
                                                            $defaultValue = null;
                                                            foreach ($ppn_code->result() as $pc) {
                                                                if ($pc->PRIMARY_FLAG == 'Y') {
                                                                    $defaultValue = $pc->PPN_CODE;
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <select name="cal_ppn_code" id="cal_ppn_code" class="form-control form-select-sm select2 <?= form_error('storage') ? 'is-invalid' : null; ?>">
                                                                <?php if (!$defaultValue): ?>
                                                                    <option value="">-- Selected PPN Code --</option>
                                                                <?php endif; ?>
                                                                <?php foreach ($ppn_code->result() as $pc): ?>
                                                                    <option
                                                                        value="<?= $pc->PERCENTAGE ?>"
                                                                        <?= set_value('cal_ppn_code') ==  $pc->PERCENTAGE ? 'selected' : ($defaultValue == $pc->PPN_CODE ? 'selected' : '') ?>>
                                                                        <?= strtoupper($pc->PPN_CODE) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-lg-1 col-md-12 col-sm-12">
                                            <input type="hidden" name="TOTAL_DISKON_INPUT" id="hid_diskon_input" value="" title="hid_diskon_input">
                                            <input type="hidden" name="DISKON_PERCEN" id="hid_diskon_percen" value="" title="hid_diskon_percen">
                                            <input type="hidden" name="DISKON_PRICE" id="hid_diskon_price" value="" title="hid_diskon_price">
                                            <input type="hidden" name="PPN_PERCEN" id="hid_ppn" value="" title="hid_ppn">
                                            <input type="hidden" name="PPN_CODE" id="hid_ppn_code" value="" title="hid_ppn_code">
                                            <input type="hidden" name="PPN_AMOUNT" id="hid_ppn_amount" value="" title="hid_ppn_amount">
                                            <input type="hidden" name="TOTAL_AMOUNT" id="hid_total_amount" value="" title="hid_total_amount">
                                            <input type="hidden" name="TOTAL_NET" id="hid_total_net" value="" title="hid_total_net">
                                        </div>
                                        <div class="col-lg-5 col-md-12 col-sm-12">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <td style="font-weight:bold;">Total</td>
                                                        <td>:</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-right" style="text-align: right">
                                                            <div id="v_total_amount"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight:bold;">Diskon</td>
                                                        <td>:</td>
                                                        <td class="text-left">
                                                        </td>
                                                        <td></td>
                                                        <td class="text-right" style="text-align: right">
                                                            <div id="v_diskon"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight:bold;">PPN</td>
                                                        <td>:</td>
                                                        <td class="text-left">
                                                        </td>
                                                        <td></td>
                                                        <td class="text-right" style="text-align: right">
                                                            <div id="v_ppn_amount"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight:bold;">
                                                            <h5>GRAND TOTAL</h5>
                                                        </td>
                                                        <td>:</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-right text-danger" style="text-align: right">
                                                            <h5>
                                                                <div id="v_total_net"></div>
                                                            </h5>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
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

<!-- modal -->
<div id="modalMrq" class="modal fade" style="font-size: 12px;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="modalTitleForm"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="table-item">
                        <thead>
                            <tr class="text-nowrap">
                                <th>
                                    <input type="checkbox" name="checkAll" id="checkAll" class="">
                                </th>
                                <th>No</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>No Transaksi</th>
                                <th>No Referensi</th>
                                <th>Nama Item</th>
                                <th>Kode Item</th>
                                <th>Jumlah</th>
                                <th>Sisa</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="btnSubmit">Selected</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- / modal -->

<!-- modal memo -->
<div class="modal fade" id="modalMemo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Memo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <textarea id="modalMemoText"
                    class="form-control"
                    rows="5"
                    placeholder="Masukkan Memo..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveMemo">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- modal keterangan -->
<div class="modal fade" id="modalKeterangan" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Keterangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <textarea id="modalKeteranganText"
                    class="form-control"
                    rows="5"
                    placeholder="Masukkan keterangan..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveKeterangan">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let tableDetail;
    let tableItem;
    $(document).ready(function() {
        tableDetail = $('#table-detail').DataTable({
            ordering: false,
            autoWidth: false,
            paging: false,
            columnDefs: [{
                    targets: 0,
                    width: "2%",
                    className: "text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no
                {
                    targets: 1,
                    width: "2%",
                    className: "text-center",
                }, // checkbox
                {
                    targets: 2,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no transaksi
                {
                    targets: 3,
                    width: "20%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // nama item
                {
                    targets: 4,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // kode item
                {
                    targets: 5,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // memo
                {
                    targets: 6,
                    width: "10%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // jumlah
                {
                    targets: 7,
                    width: "10%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // satuan
                {
                    targets: 8,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // harga input
                {
                    targets: 9,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // harga
                {
                    targets: 10,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // diskon rp
                {
                    targets: 11,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // diskon %
                {
                    targets: 12,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // subtotal
                {
                    targets: 13,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // keterangan
            ],
        });

        tableItem = $('#table-item').DataTable({
            autoWidth: false,
            columnDefs: [{
                    targets: 0,
                }, // checkbox
                {
                    targets: 1,
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no
                {
                    targets: 2,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // status
                {
                    targets: 3,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // tanggal
                {
                    targets: 4,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no transaksi
                {
                    targets: 5,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no refrensi
                {
                    targets: 6,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // nama item
                {
                    targets: 7,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 15;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // kode item
                {
                    targets: 8,
                    className: "ellipsis text-end",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // jumlah
                {
                    targets: 9,
                    className: "ellipsis text-end",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // sisa
                {
                    targets: 10,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ?
                            data.substring(0, limit) + '...' :
                            data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    },
                }, // satuan
            ],
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: false,
        });

        let oldDetail = <?= json_encode($detail ?? []) ?>;

        if (oldDetail && oldDetail.kode_item) {
            oldDetail.kode_item.forEach(function(kode, i) {
                let nomor = tableDetail.rows().count() + 1;

                let item_id = oldDetail.item_id[i] ?? '';
                let po_detail_id = oldDetail.po_detail_id[i] ?? '';
                let tag_detail_id = oldDetail.tag_detail_id[i] ?? '';
                let base_qty = oldDetail.base_qty[i] ?? 0;
                let unit_price = oldDetail.unit_price[i] ?? 0;
                let subtotal = oldDetail.subtotal[i] ?? 0;
                let warehouse_id = oldDetail.warehouse_id[i] ?? '';
                let harga_input = oldDetail.harga_input[i] ?? 0;
                let keterangan = oldDetail.keterangan[i] ?? '';
                let berat = oldDetail.berat[i] ?? 0;
                let balance = oldDetail.balance[i] ?? 0;

                let no_transaksi = oldDetail.no_transaksi[i] ?? '';
                let nama_item = oldDetail.nama_item[i] ?? '';
                let jumlah = oldDetail.jumlah[i] ?? 0;
                let satuan = oldDetail.satuan[i] ?? '';

                let rowNode = tableDetail.row.add([
                    nomor,

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${no_transaksi}">
                        ${ellipsis(no_transaksi)}
                    </span>
                    <input type="hidden" name="detail[no_transaksi][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${formatNumber(base_qty)}">
                    <input type="hidden" name="detail[unit_price][]" value="${unit_price}">
                    <input type="hidden" name="detail[subtotal][]" value="${subtotal}">
                    <input type="hidden" name="detail[warehouse_id][]" value="${warehouse_id}">
                    <input type="hidden" name="detail[po_detail_id][]" value="${po_detail_id}">
                    <input type="hidden" name="detail[tag_detail_id][]" value="${tag_detail_id}">
                    <input type="hidden" name="detail[harga_input][]" value="${harga_input}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">
                    `,

                    `<span class="ellipsis" title="${nama_item}">
                        ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,

                    `<span class="ellipsis" title="${kode}">
                        ${ellipsis(kode)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode}">
                    `,

                    `<span class="view-mode qty-view">${formatNumber(jumlah)}</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab" name="detail[jumlah][]" value="${Math.floor(Number(jumlah))}" min="0" step="any" data-balance="${Math.floor(Number(balance))}">`,

                    `<span class="ellipsis" title="${satuan}">
                        ${ellipsis(satuan)}
                    </span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">
                    `,

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly>${keterangan}</textarea>`,
                ]).node();
                $(rowNode).addClass('tr-height-30');
            });
            toggleStorageDisabled();
            tableDetail.draw(false);
        }

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

        $('#location').prop('disabled', true);

        $('#location').on('select2:opening', function(e) {
            e.preventDefault();
        });

        let initialCustomer = $('#customer option:selected').data('person_site_id');

        let oldLocation = "<?= set_value('location') ?>";

        if (initialCustomer) {
            loadLocation(initialCustomer, oldLocation);
        }

        $('#customer').on('change', function() {
            let initialCustomer = $('#customer option:selected').data('person_site_id');
            loadLocation(initialCustomer);
        });

        // Event untuk input normal
        $('#tanggal').on('change', updateJatuhTempo);

        // Event untuk Select2
        $('#payment_term').on('change.select2', updateJatuhTempo);

        // Inisialisasi
        updateJatuhTempo();

        $("#storage").data("prev", $("#storage").val());
        $("#storage").on("change", function(e, data) {
            let prev = $(this).data("prev");
            let current = $(this).val();
            if (prev && current !== prev && tableDetail.rows().count() > 0) {
                Swal.fire({
                    title: "Ganti Storage?",
                    text: "Data yang sudah dipilih akan dihapus.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, ganti",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        tableDetail.clear().draw();
                        $(this).data("prev", current);
                        toggleStorageDisabled();
                    } else {
                        $(this).val(prev).trigger('change.select2', {
                            skipEvent: true
                        });
                    }
                });
            } else {
                $(this).data("prev", current);
            }
        });

        // modal
        $("#btn-modalMrq").on("click", function() {
            resetModalItem();
            $("#checkAll").prop('checked', false);
            $('#loading').show();
            var storage = $('#storage').val();
            var customer = $('#customer').val();

            if (!storage) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Storage tidak terisi, Mohon isi terlebih dahulu',
                });
                return;
            }

            if (!customer) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Customer tidak terisi, Mohon isi terlebih dahulu',
                });
                return;
            }

            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>so_kny/getMrq",
                data: {
                    storage: storage,
                    customer: customer,
                },
                dataType: "json",
                success: function(response) {
                    $('#loading').hide();
                    tableItem.clear().draw();

                    let existingBuildId = new Set();
                    tableDetail.rows().every(function() {
                        let node = this.node();
                        let buildId = $(node).find('input[name="detail[build_id][]"]').val();
                        if (buildId) existingBuildId.add(buildId);
                    });

                    if (response.status === 'success' && Array.isArray(response.data)) {
                        response.data.forEach(function(item, i) {

                            if (existingBuildId.has(item.BUILD_ID)) {
                                return;
                            }

                            var checkbox = `
                            <input type="checkbox" class="chkRow"
                                data-build_id="${item.BUILD_ID}"
                                data-item_id="${item.ITEM_ID}"
                                data-base_qty="${item.BASE_QTY}"
                                data-unit_price="${item.UNIT_PRICE}"
                                data-note="${item.NOTE}"
                                data-berat="${item.BERAT}"

                                data-status="${item.STATUS_NAME}"
                                data-tanggal="${item.DOCUMENT_DATE}"
                                data-no_transaksi="${item.DOCUMENT_NO}"
                                data-no_referensi="${item.DOCUMENT_REFF_NO}"
                                data-nama_item="${item.ITEM_DESCRIPTION.replace(/"/g, '&quot;')}"
                                data-kode_item="${item.ITEM_CODE}"
                                data-jumlah="${item.ENTERED_QTY}"
                                data-sisa="${item.BALANCE}"
                                data-satuan="${item.ENTERED_UOM}"
                            >
                            `;
                            tableItem.row.add([
                                checkbox,
                                i + 1,
                                item.STATUS_NAME,
                                item.DOCUMENT_DATE,
                                item.DOCUMENT_NO,
                                item.DOCUMENT_REFF_NO,
                                item.ITEM_DESCRIPTION,
                                item.ITEM_CODE,
                                parseFloat(item.ENTERED_QTY).toFixed(2),
                                parseFloat(item.BALANCE).toFixed(2),
                                item.ENTERED_UOM,
                            ]);
                        });
                        tableItem.draw();
                    }
                    $('#modalTitleForm').text('List Data');
                    $('#modalMrq').modal('show');
                }
            });
        });

        // Centang semua
        $("#checkAllParent").change(function() {
            $(".chkDetail").prop('checked', $(this).prop('checked'));
        });

        $("#checkAll").change(function() {
            $(".chkRow").prop('checked', $(this).prop('checked'));
        });

        // submit
        $("#btnSubmit").on("click", function(e) {
            e.preventDefault();
            let rowsAdded = false;
            let allRows = tableItem.rows().nodes();
            $(allRows).find('.chkRow:checked:not(:disabled)').each(function() {
                let build_id = $(this).data("build_id");
                let item_id = $(this).data("item_id");
                let base_qty = $(this).data("base_qty");
                let unit_price = $(this).data("unit_price");
                let keterangan = $(this).data("note") ?? '';
                let berat = $(this).data("berat");
                let balance = $(this).data("sisa");

                let status = $(this).data("status");
                let tanggal = $(this).data("tanggal");
                let no_transaksi = $(this).data("no_transaksi");
                let no_referensi = $(this).data("no_referensi");
                let nama_item = $(this).data("nama_item");
                let kode_item = $(this).data("kode_item");
                let jumlah = $(this).data("jumlah");
                let satuan = $(this).data("satuan");

                // Cegah double di tableDetail
                let exists = tableDetail
                    .column(2)
                    .data()
                    .toArray()
                    .includes(build_id);

                if (exists) {
                    $(this).prop('checked', false).prop('disabled', true);
                    return;
                }

                let rowNode = tableDetail.row.add([
                    "",

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${no_transaksi}">
                        ${ellipsis(no_transaksi)}
                    </span>
                    <input type="hidden" name="detail[no_transaksi][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[build_id][]" value="${build_id}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${formatNumber(base_qty)}">
                    <input type="hidden" name="detail[unit_price][]" value="${unit_price}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">
                    `,

                    `<span class="ellipsis" title="${nama_item}">
                        ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,

                    `<span class="ellipsis" title="${kode_item}">
                        ${ellipsis(kode_item)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode_item}">
                    `,

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly></textarea>`,
                    // memo

                    `<span class="view-mode qty-view">${formatNumber(balance)}</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab" name="detail[jumlah][]" value="${Math.floor(Number(balance))}" min="0" step="any" data-balance="${Math.floor(Number(balance))}">`,

                    `<span class="ellipsis" title="${satuan}">
                        ${ellipsis(satuan)}
                    </span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">
                    `,

                    `<span class="view-mode harga-view">0.00</span>
                    <input type="number" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab" min="0" step="any" name="detail[harga_input][]" value="">`,
                    // harga input

                    `<span class="harga-input-b">0.00</span>
                    <input type="hidden" name="detail[harga][]" value="">`,
                    // harga

                    `<span class="view-mode harga-view diskon-harga"></span>
                    <input type="number" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab" min="0" step="any" name="detail[diskon_harga][]" value="">`,
                    // diskon rp

                    `<span class="view-mode"></span>
                    <input type="text" class="form-control form-control-sm edit-mode d-none enter-as-tab persen-detail" step="any" name="detail[diskon_persentase][]" value="">`,
                    // diskon %

                    `<span class="subtotal">0.00</span>
                    <input type="hidden" name="detail[subtotal][]" value="">`,
                    // subtotal

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly></textarea>`,
                ]).node();

                $(rowNode).addClass('tr-height-30');

                rowsAdded = true;
            });


            if (rowsAdded) {
                tableDetail.draw(false);
                tableDetail.columns.adjust();
                toggleStorageDisabled();
            }
            $("#modalMrq").modal("hide");
        });

        $(document).on("click", "#table-detail tbody td", function(e) {

            // kalau yang diklik memang input, biarkan normal
            if ($(e.target).is("input, select, textarea")) return;

            let td = $(this);
            let span = td.find(".view-mode");
            let input = td.find(".edit-mode");

            // hanya jalan kalau memang ada view/edit mode
            if (span.length && input.length) {
                span.addClass("d-none");
                input.removeClass("d-none").focus().select();
            }
        });

        // keluar input
        $(document).on("blur change", ".edit-mode", function() {
            let input = $(this);
            let span = input.prev(".view-mode");

            let value = input.val();
            if (input.hasClass("harga-edit") || input.hasClass("qty-edit")) {
                span.text(formatNumber(value, 2));
            } else {
                span.text(value === "" ? "0" : value);
            }

            input.addClass("d-none");
            span.removeClass("d-none");
        });

        $(document).on("input", ".qty-edit, input[name='detail[harga_input][]'], input[name='detail[diskon_harga][]'], input[name='detail[diskon_persentase][]']", function() {
            let row = $(this).closest("tr");
            calculate(row);
        });

        tableDetail.on("draw.dt", function() {
            tableDetail
                .column(0)
                .nodes()
                .each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
        });

        $("#checkAllParent").prop("checked", false);

        $("#checkAllParent").on("change", function() {
            let isChecked = $(this).is(":checked");
            $("#table-detail .chkDetail").prop("checked", isChecked);
        });

        $(document).on("change", ".chkDetail", function() {
            let total = $("#table-detail .chkDetail").length;
            let checked = $("#table-detail .chkDetail:checked").length;

            $("#checkAllParent").prop("checked", total > 0 && total === checked);
        });

        $("#removeRow").on("click", function() {
            let rowsToRemove = tableDetail.rows().nodes().to$().filter(function() {
                return $(this).find(".chkDetail").is(":checked");
            });
            if (rowsToRemove.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Tidak ada item yang dipilih!'
                });
                return;
            }
            Swal.fire({
                title: 'Yakin mau hapus?',
                text: `Ada ${rowsToRemove.length} item yang akan dihapus`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6ebbff'
            }).then((result) => {
                if (result.isConfirmed) {

                    rowsToRemove.each(function() {
                        tableDetail.row(this).remove();
                    });
                    tableDetail.draw(false);

                    $("#checkAllParent").prop("checked", false);

                    toggleStorageDisabled();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Item berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        $(document).on('keydown', '.jumlah, .harga-input', function(e) {
            if (
                e.key === 'e' || e.key === 'E' ||
                e.key === '+' || e.key === '-'
            ) {
                e.preventDefault();
            }
        });

        $(document).on('input change', '.jumlah, .harga-input', function() {
            let val = $(this).val();
            if (val === '') return;

            val = parseFloat(val);
            if (val < 1) {
                $(this).val(1);
            }
        });

        updatePpnHidden();
    });

    // persen detail
    $(document).on('input', '.persen-detail', function() {
        let value = $(this).val();

        // Hanya izinkan angka dan +
        value = value.replace(/[^\d+]/g, '');

        // Pisahkan berdasarkan +
        let parts = value.split('+');

        // Validasi tiap angka
        parts = parts.map(function(part) {
            if (part === '') return '';

            let num = parseInt(part, 10);

            if (isNaN(num)) return '';

            // Maksimal 100
            if (num > 100) num = 100;

            return num;
        });

        // Gabungkan kembali
        $(this).val(parts.join('+'));

        // Hitung total jika perlu
        let total = parts.reduce((sum, val) => {
            return sum + (parseFloat(val) || 0);
        }, 0);
    });

    $(document).on('keydown', '.enter-as-tab', function(e) {
        if (e.which !== 13) return;

        e.preventDefault();
        const $row = $(this).closest('tr');

        if ($(this).hasClass('qty-edit')) {
            const $harga = $row.find('.harga-edit');

            $row.find('.harga-view').addClass('d-none');
            $harga.removeClass('d-none');
            $harga.focus();
            return;
        }

        if ($(this).hasClass('harga-edit')) {
            $row.find('textarea[name="detail[keterangan][]"]').focus();
            return;
        }
    });

    let openedSelect = null;
    let isOpening = false;

    let activeKeteranganInput = null;

    $('#table-detail tbody').on(
        'click',
        'textarea[name="detail[memo][]"]',
        function() {

            activeMemoInput = $(this);

            // isi modal dengan nilai input saat ini
            $('#modalMemoText').val($(this).val());

            $('#modalMemo').modal('show');
        }
    );

    $('#table-detail tbody').on(
        'click',
        'textarea[name="detail[keterangan][]"]',
        function() {

            activeKeteranganInput = $(this);

            // isi modal dengan nilai input saat ini
            $('#modalKeteranganText').val($(this).val());

            $('#modalKeterangan').modal('show');
        }
    );

    $('#btnSaveMemo').on('click', function() {
        if (!activeMemoInput) return;

        activeMemoInput.val(
            $('#modalMemoText').val()
        );

        $('#modalMemo').modal('hide');
    });

    $('#btnSaveKeterangan').on('click', function() {
        if (!activeKeteranganInput) return;

        activeKeteranganInput.val(
            $('#modalKeteranganText').val()
        );

        $('#modalKeterangan').modal('hide');
    });

    $('#modalMemo').on('hidden.bs.modal', function() {
        activeMemoInput = null;
        $('#modalMemoText').val('');
    });

    $('#modalKeterangan').on('hidden.bs.modal', function() {
        activeKeteranganInput = null;
        $('#modalKeteranganText').val('');
    });

    document.addEventListener('input', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;

        const input = e.target;
        const balance = parseFloat(input.dataset.balance);
        let value = parseFloat(input.value);

        const row = $(input).closest("tr");

        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) {
                span.textContent = val.toFixed(2).replace('.', '.');
            }
        }

        // Tidak boleh lebih dari balance
        if (value > balance) {
            Swal.fire({
                icon: 'warning',
                title: 'Jumlah melebihi balance',
                text: 'Jumlah tidak boleh melebihi balance (' + balance + ')',
                confirmButtonText: 'OK'
            }).then(() => {
                input.value = balance;
                input.focus();
                hitungSubTotal(row);
                updateSpan(balance);
            });
            return;
        }
    });

    // jika jumlah kosong
    document.addEventListener('blur', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;

        const input = e.target;
        const row = $(input).closest("tr");
        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) {
                span.textContent = val.toFixed(2).replace('.', '.');
            }
        }

        const balance = parseFloat(input.dataset.balance);

        // Tidak boleh minus atau nol
        if (input.value <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Jumlah tidak valid',
                text: 'Jumlah harus lebih dari 0',
                confirmButtonText: 'OK'
            }).then(() => {
                input.value = input.dataset.balance;
                input.focus();
                hitungSubTotal(row);
                updateSpan(balance);
            });
            return;
        }

        if (input.value === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Input kosong',
                text: 'Jumlah tidak boleh kosong',
                confirmButtonText: 'OK'
            }).then(() => {
                input.value = input.dataset.balance;
                input.focus();
                hitungSubTotal(row);
                updateSpan(balance);
            });
            return;
        }
    }, true);

    // diskon persentase header
    $('#cal_diskon_percen').on('input', function() {
        let value = $(this).val();

        // Hanya izinkan angka dan +
        value = value.replace(/[^0-9+]/g, '');

        // Pisahkan formula
        let parts = value.split('+').map(x => {
            let trimmed = x.trim();

            // Jika kosong, biarkan tetap ''
            if (trimmed === '') return '';

            let num = parseFloat(trimmed);

            if (isNaN(num)) return '';

            // Batasi tiap angka max 100
            if (num > 100) num = 100;

            return num;
        });

        // Gabungkan kembali
        $(this).val(parts.join('+'));

        // Hitung total, hanya jumlahkan angka valid
        let total = parts.reduce((sum, n) => sum !== '' ? sum + n : sum + 0, 0);
        $('#hid_diskon_percen').val(total);
        calculateGrandTotal();
    });

    $('#cal_diskon_percen').on('blur', function() {
        let input = $(this).val().trim();

        if (input === "") {
            $(this).val('');
            $('#hid_diskon_percen').val('');
            return;
        }

        // Pisahkan tiap angka berdasarkan +
        let parts = input.split('+').map(x => {
            let num = parseFloat(x.trim()) || 0;

            // Batasi tiap angka max 100
            if (num > 100) num = 100;

            return num;
        });

        // Update input dengan formula yang sudah dibatasi
        $(this).val(parts.join('+'));

        // Update hidden input dengan total
        let total = parts.reduce((a, b) => a + b, 0);
        $('#hid_diskon_percen').val(total);
        calculateGrandTotal();
    });

    // diskon angka header
    $('#cal_diskon_price').on('input', function() {
        let value = $(this).val();

        value = value.replace(/[^0-9.]/g, '');

        $(this).val(value);

        $('#hid_diskon_input').val(value);
        calculateGrandTotal();
    });

    $('#cal_diskon_price').on('blur', function() {
        let value = $(this).val();
        if (value === "" || isNaN(value)) {
            $(this).val('');
            $('#total_diskon_hidden').val('0');
            return;
        }

        // Update hidden input untuk submit
        $('#total_diskon_hidden').val(value);

        // Format input text agar terlihat ribuan
        $(this).val(formatNumber(value));
        calculateGrandTotal();
    });

    $('#cal_ppn_code').on('change', function() {
        updatePpnHidden();
    });

    function calculate(row) {
        var ppn_code = $("#hid_ppn_code").val();
        var ppn = $("#hid_ppn").val();

        hitungSubTotal(row, ppn_code, ppn);
        calculateGrandTotal(ppn_code, ppn);
    }

    let hitungTimerDetail = null;

    function hitungSubTotal(row, ppn_code, ppn) {

        let qty = parseFloat(row.find(".qty-edit").val()) || 0;
        let harga_input = parseFloat(row.find('input[name="detail[harga_input][]"]').val()) || 0;
        let persen_harga = parseFloat(row.find('input[name="detail[diskon_harga][]"]').val()) || 0;
        let persen = row.find('input[name="detail[diskon_persentase][]"]').val();

        if (persen != 0) {
            clearTimeout(hitungTimerDetail);
            hitungTimerDetail = setTimeout(function() {
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('so_kny/get_hitung_diskon_bertingkat') ?>",
                    data: {
                        harga_input,
                        persen
                    },
                    dataType: "json",
                    success: function(response) {
                        let diskon_harga = parseFloat(response.data[0].harga) || 0;

                        // Hitung subtotal
                        let subtotal = (harga_input - diskon_harga) * qty;
                        if (subtotal < 0) subtotal = 0;

                        // Update field
                        row.find(".harga-input-b").text(harga_input.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        row.find('input[name="detail[harga][]"]').val(harga_input.toFixed(2));

                        // **Update input diskon**
                        row.find('input[name="detail[diskon_harga][]"]').val(diskon_harga.toFixed(2));

                        // **Update view span diskon (opsional)**
                        row.find('span.diskon-harga').text(diskon_harga.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));

                        // Update subtotal
                        row.find(".subtotal").text(subtotal.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        row.find('input[name="detail[subtotal][]"]').val(subtotal.toFixed(2));

                        calculateGrandTotal(row, ppn_code, ppn)
                    },
                    error: function(err) {
                        console.error(err);
                    }
                });
            }, 500);
        } else {
            // Hitung subtotal
            let subtotal = (harga_input - persen_harga) * qty;
            if (subtotal < 0) subtotal = 0;

            // Update field
            row.find(".harga-input-b").text(harga_input.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            row.find('input[name="detail[harga][]"]').val(harga_input.toFixed(2));

            // Update subtotal
            row.find(".subtotal").text(subtotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            row.find('input[name="detail[subtotal][]"]').val(subtotal.toFixed(2));

            calculateGrandTotal(row, ppn_code, ppn)
        }

    }

    function updatePpnHidden() {
        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text();

        $('#hid_ppn').val(ppn);
        $('#hid_ppn_code').val(ppn_code);

        calculatePPN(ppn_code, ppn);

        calculateGrandTotal(ppn_code, ppn);
    }

    let hitungTimerHeader = null;

    function calculateGrandTotal(ppn_code, ppn) {
        let total = 0;

        $('input[name="detail[subtotal][]"]').each(function() {
            total += parseFloat($(this).val()) || 0;
        });

        $('#hid_total_amount').val(total);

        $('#v_total_amount').text(total.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }));

        clearTimeout(hitungTimerHeader);

        let total_amount = parseFloat($('#hid_total_amount').val()) || 0;
        let price_persen = parseFloat($('#hid_diskon_input').val()) || 0;
        let persen = parseFloat($('#hid_diskon_percen').val()) || 0;

        if (persen != 0) {
            hitungTimerHeader = setTimeout(function() {
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('so_kny/get_hitung_diskon_bertingkat_header') ?>",
                    data: {
                        total_amount,
                        persen
                    },
                    dataType: "json",
                    success: function(response) {
                        let diskon_harga = parseFloat(response.data[0].harga) || 0;
                        $('#v_diskon').text(diskon_harga.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#hid_diskon_price').val(diskon_harga);

                        $('#cal_diskon_price').val(diskon_harga.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }));
                        $('#hid_diskon_input').val(diskon_harga);
                    },
                    error: function(err) {
                        console.error(err);
                    }
                });
            }, 500);
        } else {
            $('#v_diskon').text(price_persen.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
            $('#hid_diskon_price').val(price_persen);

            $('#cal_diskon_price').text(price_persen.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));

            $('#hid_diskon_input').val(price_persen);
        }

    }

    function calculatePPN(ppn_code, ppn) {

        // console.log("ppn: " + ppn + "ppn_code: " + ppn_code);

    }

    // function calculateGrandTotal(ppn_code, ppn) {
    //     var V_AMOUNT2 = 0;
    //     var V_AMOUNT = 0;
    //     var V_AMOUNT_INCL = 0;
    //     var V_AMOUNT_INCL2 = 0;
    //     var V_AMOUNT_DISC = 0;

    //     let dataDetail = $('.table-detail').DataTable().rows().data();
    //     dataDetail.each(function(value, index) {
    //         var total = parseFloat('input[name="detail[subtotal][]"]');
    //         var harga_input = parseFloat('input[name="detail[subtotal][]"]');
    //         var persen = parseFloat('input[name="detail[diskon_persentase][]"]');
    //         var qty = parseFloat('input[name="detail[jumlah][]"]');

    //         V_AMOUNT2 += total;
    //         if (value.GRADE_ID != 73) {
    //             V_AMOUNT += total;
    //             V_AMOUNT_INCL += ((harga_input - persen) * qty);
    //         }
    //         V_AMOUNT_INCL2 += ((harga_input - persen) * qty);
    //         if (value.ITEM_ID != 1) V_AMOUNT_DISC += ((harga_input - persen) *
    //             qty);
    //     });

    //     var prorata = V_AMOUNT / V_AMOUNT2;

    //     //set diskon
    //     var diskonPrice = $("#cal_diskon_price").val();
    //     diskonPrice = formatNumber(diskonPrice);
    //     if (!diskonPrice) diskonPrice = 0;
    //     var TOTAL_DISKON_INPUT = parseFloat(diskonPrice);
    //     var TOTAL_DISCOUNT = 0;
    //     var diskon = $("#cal_diskon_percen").val();
    //     if (diskon != "") {
    //         TOTAL_DISKON_INPUT = c_hitung_total_diskon_input(V_AMOUNT_DISC, diskon);
    //         $('#cal_diskon_price').val(accounting.format(TOTAL_DISKON_INPUT, desimal));
    //         calculate_from_disc_percen = accounting.format(TOTAL_DISKON_INPUT, desimal);
    //     } else {
    //         if ($("#cal_diskon_percen").attr('data-mode') == "true" && diskon == "") $('#cal_diskon_price').val('');
    //         c_discount_price_head();
    //     }

    //     // SJ DISCOUNT
    //     const toNum = v => (typeof v === 'number' ? v : parseFloat(String(v).replace(/,/g, ''))) || 0;
    //     let SjDiskon = 0;
    //     const dt = $('.table-detail').DataTable();
    //     // let setup = {
    //     //     "NAME": "DEV BANGUNAN",
    //     //     "ADDRESS_ID": 7,
    //     //     "LOGO_FILENAME": "https:\/\/web.ninesoft.id\/site\/assets\/img\/ui-sam7.png",
    //     //     "TEMPLATE_FOLDER": "TEMPLATE",
    //     //     "GAMBAR_FOLDER": "GAMBAR\\",
    //     //     "FREPORT_FOLDER": null,
    //     //     "EXCEL_PASS": null,
    //     //     "NO_TRANS": 3,
    //     //     "STOCK_MIN": 1,
    //     //     "LIMIT_PIUT_ORDER": 4,
    //     //     "LIMIT_PIUT_JUAL": 4,
    //     //     "QTYBD_PO": 3,
    //     //     "QTYSJ_SO": 3,
    //     //     "HARGA_SO": 2,
    //     //     "HARGA_JUAL": 4,
    //     //     "DISC_JUAL": 2,
    //     //     "DIRECT_PRINT": 1,
    //     //     "EXPORT_REPORT": 0,
    //     //     "ERP_TABLE_ID": null,
    //     //     "EMPTY_REPORT": 1,
    //     //     "DISC_TOTAL_PERCEN": "15",
    //     //     "TOOLBAR_COLOR": "#0E4061",
    //     //     "HEADER_COLOR": "#479ACC",
    //     //     "DETAIL_COLOR": "#41799A",
    //     //     "GRID_COLOR": null,
    //     //     "ROW_COLOR": null,
    //     //     "REPORT_COLOR": null,
    //     //     "BILL_ROWCOUNT": 10,
    //     //     "PEMBULATAN_PPN": 0,
    //     //     "FLAG_TGL_ACC": 0,
    //     //     "TGL_ACC": null,
    //     //     "SCREEN_STOCK": 1,
    //     //     "CHECK_MATCHING": 2,
    //     //     "SHOW_HPP": 3,
    //     //     "JURNAL_AWAL": 1,
    //     //     "SIKLUS_BELI": 2,
    //     //     "SIKLUS_JUAL": 5,
    //     //     "SHOW_JUAL_BARCODE": 2,
    //     //     "SHOW_JUAL_CASH": 2,
    //     //     "QTY": 2,
    //     //     "START_DATE": "2018-07-01",
    //     //     "CUSTOM1": "*QMRXo2qsC9\u003EYNiZhjs4JGKU[Zjl\u00e2\u20ac\u00a69\u003COZaZmcz\u00c6\u201979",
    //     //     "CUSTOM2": "2",
    //     //     "CUSTOM3": "N",
    //     //     "CUSTOM4": "N",
    //     //     "CUSTOM5": "D:\\SEVENTHSOFT\\Seventhsoft Demo\\ROTI- Seventhsoft\\",
    //     //     "CUSTOM6": "D31A",
    //     //     "CUSTOM7": "21",
    //     //     "CUSTOM8": "1",
    //     //     "CUSTOM9": "1",
    //     //     "CUSTOM10": "250",
    //     //     "EMAIL": "anugerahtrisnahadi@gmail.com",
    //     //     "FLAG_EO_PREMIUM": "N",
    //     //     "PERSEN_UKURAN_QR": 75,
    //     //     "NO_PAJAK": 165,
    //     //     "LITE_VERSION": 0,
    //     //     "MODE_PRINT": "Y",
    //     //     "SUNTIK_AWAL": 0,
    //     //     "NEW_WAY_HPP": null,
    //     //     "REPORT_HISTORY": 7,
    //     //     "SO_APPROVE": 1,
    //     //     "ADDRESS1": "JL. TEUKU UMAR 24",
    //     //     "PHONE": null
    //     // };

    //     if (setup.SIKLUS_JUAL == 5 || setup.SIKLUS_BELI == 2) {
    //         dt.rows().data().each((row) => {
    //             if (getUri().includes('faktur')) {
    //                 if (getUri().includes('sales') && !row.NO_SJ) return;
    //                 if (getUri().includes('purchasing') && !row.NO_BPB) return;
    //             }

    //             const disc = toNum(row.TOTAL_DISKON_INPUT);
    //             SjDiskon += disc;
    //         });
    //         if (getUri().includes('faktur')) {
    //             if ((getUri().includes('sales') || getUri().includes('purchasing')) && $('#invoice_type_id').val() ==
    //                 400) {
    //                 if (SjDiskon > 0) {
    //                     TOTAL_DISKON_INPUT = SjDiskon;
    //                     $('#cal_diskon_price').val(accounting.format(TOTAL_DISKON_INPUT, desimal));
    //                 }
    //             }
    //         }
    //     }


    //     TOTAL_DISCOUNT = TOTAL_DISKON_INPUT;

    //     //set ppn
    //     var PPN_AMOUNT = 0;
    //     var PPN_PERCEN = parseFloat(ppn);
    //     if (PPN_PERCEN > 0) {
    //         var VPPN = 0;
    //         if (!ppn_code.includes("INCL")) {
    //             VPPN = (V_AMOUNT - TOTAL_DISCOUNT * prorata) * (PPN_PERCEN / 100);
    //         } else {
    //             var vtotal_diskon_ppn = TOTAL_DISCOUNT * prorata / (1 + PPN_PERCEN / 100);
    //             var vtotal_diskon_ppn = (vtotal_diskon_ppn * PPN_PERCEN / 100);
    //             VPPN = (V_AMOUNT_INCL - V_AMOUNT - vtotal_diskon_ppn);
    //         }

    //         var VPPN_TEMP = 0;
    //         if (VPPN < 0) VPPN_TEMP = VPPN * -1;
    //         else VPPN_TEMP = VPPN;

    //         if (setup.PEMBULATAN_PPN == 1) {
    //             VPPN_TEMP = Math.ceil(VPPN_TEMP);
    //         } else if (setup.PEMBULATAN_PPN == 2) {
    //             VPPN_TEMP = Math.floor(VPPN_TEMP);
    //         } else if (setup.PEMBULATAN_PPN == 3) {
    //             VPPN_TEMP = Math.round(VPPN_TEMP);
    //         }

    //         if (VPPN < 0) PPN_AMOUNT = VPPN_TEMP * -1;
    //         else PPN_AMOUNT = VPPN_TEMP;
    //     }

    //     //set total net
    //     V_AMOUNT = V_AMOUNT2;
    //     var TOTAL_AMOUNT = V_AMOUNT;
    //     var TOTAL_NET = TOTAL_AMOUNT - TOTAL_DISCOUNT + PPN_AMOUNT;
    //     if (ppn_code.includes("INCL")) {
    //         V_AMOUNT = V_AMOUNT_INCL2 - TOTAL_DISKON_INPUT;
    //         TOTAL_DISCOUNT = TOTAL_DISCOUNT + (TOTAL_NET - V_AMOUNT);
    //         TOTAL_NET = V_AMOUNT;
    //     }

    //     // database
    //     document.getElementById("hid_diskon_input").value = TOTAL_DISKON_INPUT;
    //     document.getElementById("hid_total_amount").value = TOTAL_AMOUNT;
    //     document.getElementById("hid_diskon_price").value = TOTAL_DISCOUNT;
    //     document.getElementById("hid_ppn_amount").value = PPN_AMOUNT;
    //     document.getElementById("hid_total_net").value = TOTAL_NET;

    //     // tampilan
    //     document.getElementById("v_total_amount").innerHTML = formatNumber(TOTAL_AMOUNT);
    //     document.getElementById("v_diskon").innerHTML = formatNumber(TOTAL_DISCOUNT);
    //     document.getElementById("v_ppn_amount").innerHTML = formatNumber(PPN_AMOUNT);
    //     document.getElementById("v_total_net").innerHTML = formatNumber(TOTAL_NET);
    //     $('#total_nett').val(formatNumber(TOTAL_NET));
    //     $('#grand_total').val(formatNumber(TOTAL_NET)).trigger('change');
    // }

    // function c_hitung_total_diskon_input(total_amount_input, diskon_persen) {
    //     let total_amount = Number(total_amount_input);
    //     let diskon_rp = 0;

    //     let diskon_split = diskon_persen.split('+');

    //     for (let index = 0; index < diskon_split.length; ++index) {
    //         let diskon = diskon_split[index];

    //         if (/^0\d+$/.test(diskon)) { // Cek apakah formatnya 0xxx (diskon rupiah)
    //             let diskon_nominal = parseInt(diskon, 10); // Konversi string ke angka
    //             diskon_rp += diskon_nominal;
    //         } else { // Jika bukan, maka dianggap sebagai diskon persen
    //             let dt = (total_amount - diskon_rp) * parseFloat(diskon) / 100;
    //             total_amount -= dt;
    //             diskon_rp += dt;
    //         }
    //     }

    //     return diskon_rp;
    // }

    function formatNumber(value, decimal = 2) {
        if (value === "" || isNaN(value)) return "0.00";
        return parseFloat(value).toLocaleString("en-US", {
            minimumFractionDigits: decimal,
            maximumFractionDigits: decimal
        });
    }

    function toggleStorageDisabled() {
        if (!tableDetail) return;

        let hasDetail = tableDetail.rows().count() > 0;
        let $customer = $('#customer');
        let $storage = $('#storage');

        if (hasDetail) {
            $customer.prop('disabled', true).trigger('change.select2');
            $storage.prop('disabled', true).trigger('change.select2');

            // Buat hidden input agar value tetap dikirim ke server
            if ($('#customer-hidden').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'customer-hidden',
                    name: $customer.attr('name'),
                    value: $customer.val()
                }).appendTo('form');
            } else {
                $('#customer-hidden').val($customer.val());
            }

            if ($('#storage-hidden').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'storage-hidden',
                    name: $storage.attr('name'),
                    value: $storage.val()
                }).appendTo('form');
            } else {
                $('#storage-hidden').val($storage.val());
            }
        } else {
            $customer.prop('disabled', false).trigger('change.select2');
            $storage.prop('disabled', false).trigger('change.select2');
            $('#customer-hidden').remove();
            $('#storage-hidden').remove();
        }
        $customer.trigger('change.select2');
        $storage.trigger('change.select2');
    }

    function resetModalItem() {
        tableItem.search('').columns().search('').draw();

        $('#checkAll').prop('checked', false);

        $('#tableItem').find('.chkRow')
            .prop('checked', false)
            .prop('disabled', false);
    }

    function ellipsis(text, limit = 25) {
        if (!text) return '-';
        return text.length > limit ?
            text.substring(0, limit) + '...' :
            text;
    }

    function loadLocation(customer, selectedLocation = null) {
        $('#location').removeClass('readonly-select');

        $('#location')
            .empty()
            .prop('disabled', true)
            .trigger('change');
        if (!customer) return;

        $.ajax({
            url: "<?= base_url('so_kny/get_location_by_customer') ?>",
            type: "POST",
            data: {
                customer: customer
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    let locations = response.data;
                    $.each(locations, function(i, item) {
                        let selected = "";

                        if (selectedLocation && selectedLocation == item.PERSON_SITE_ID) {
                            selected = "selected";
                        } else if (!selectedLocation && item.PRIMARY_SHIP === "Y") {
                            selected = "selected";
                        }

                        $('#location').val(item.SITE_NAME);
                        $('#location_id').val(item.PERSON_SITE_ID);

                        $('#address').val((item.ADDRESS1 ?? '') + '\n' + (item.CITY ?? ''));
                    });
                }
            }
        });
    }

    function updateJatuhTempo() {
        // Ambil tanggal awal
        let tanggal = $('#tanggal').val();
        if (!tanggal) return;

        // Ambil jumlah hari dari payment term yang dipilih
        let days = parseInt($('#payment_term option:selected').data('number')) || 0;

        // Buat object Date dari tanggal
        let dateObj = new Date(tanggal);

        // Tambahkan jumlah hari
        dateObj.setDate(dateObj.getDate() + days);

        // Format ke yyyy-MM-ddTHH:mm (format datetime-local)
        let year = dateObj.getFullYear();
        let month = ("0" + (dateObj.getMonth() + 1)).slice(-2);
        let day = ("0" + dateObj.getDate()).slice(-2);
        let hours = ("0" + dateObj.getHours()).slice(-2);
        let minutes = ("0" + dateObj.getMinutes()).slice(-2);

        let formatted = `${year}-${month}-${day}T${hours}:${minutes}`;

        // Set nilai jatuh tempo
        $('#jatuh_tempo').val(formatted);
    }

    function totalDiskonInput(harga, diskon_persentase) {

    }
</script>