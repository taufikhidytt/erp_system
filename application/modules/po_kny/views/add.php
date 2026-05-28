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
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('po_kny') ?>" class="text-decoration-underline">PO KNY</a>
                            </li>
                            <li class="breadcrumb-item active text-decoration-underline"><?= $breadcrumb ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card border-2">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row mb-2">
                                <div class="offset-lg-6 offset-md-6 col-lg-6 col-md-6 col-sm-12 text-end">
                                    <?= button_actions(['insert','save','reload']) ?>
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
                                            <label for="supplier">Supplier:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-2-fill"></i>
                                                </span>
                                                <input type="hidden" name="person_site_id" id="person_site_id" value="<?= set_value('person_site_id') ?>">
                                                <select name="supplier" id="supplier"
                                                    data-url="po_kny/get_supplier"
                                                    data-selected-id="<?= set_value('supplier', '') ?>"
                                                    class="form-control select2 <?= form_error('supplier') ? 'is-invalid' : null; ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('supplier') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="location">Location:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-map-2-fill"></i>
                                                </span>
                                                <input type="text" name="location" id="location" class="form-control <?= form_error('location') ? 'selected' : null ?>?>" placeholder="Enter Location" readonly>
                                                <input type="hidden" name="location_id" id="location_id" class="form-control" readonly>
                                            </div>
                                            <div class="text-danger"><?= form_error('location') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="address" id="address" class="form-control" disabled></textarea>
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
                                                        <select name="payment_term" id="payment_term"
                                                            data-url="api/get_payment"
                                                            data-default="Y"
                                                            data-selected-id="<?= set_value('payment_term', '') ?>"
                                                            class="form-control select2 <?= form_error('payment_term') ? 'is-invalid' : null; ?>">
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
                                            <label for="storage">Storage:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-building-fill"></i>
                                                </span>
                                                <select name="storage" id="storage"
                                                    data-url="po_kny/get_storage"
                                                    data-default="Y"
                                                    data-user_id="<?= $this->session->id; ?>"
                                                    data-selected-id="<?= set_value('storage', '') ?>"
                                                    class="form-control select2 <?= form_error('storage') ? 'is-invalid' : null; ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('storage') ?></div>
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
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#detail" role="tab" aria-selected="true">
                                                <span class="d-block d-sm-none"><i class="ri ri-eye-2-fill"></i></span>
                                                <span class="d-none d-sm-block">Detail</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content py-3 text-muted">
                                        <div class="tab-pane active" id="detail" role="tabpanel">
                                            <button type="button" id="removeRow" class="btn btn-danger btn-sm" style="width: 55px;height:29.89px">
                                                <i class="fa fa-trash"></i> Del
                                            </button>
                                            <button type="button" id="btn-modalMrq" class="btn btn-success btn-sm">
                                                <i class="ri ri-add-box-fill"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive overflow-auto" style="max-height: 450px;">
                                        <table class="table table-striped table-bordered table-sm" id="table-detail">
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
                                                        <td style="vertical-align:middle; font-weight:400;">Diskon</td>
                                                        <td>:</td>
                                                        <td class="text-left">
                                                            <input id="cal_diskon_percen" name="TOTAL_DISCOUNT_PERCEN" class="form-control form-control-sm input-container persen-detail" placeholder="Persen" value="<?= $this->input->post('TOTAL_DISCOUNT_PERCEN') ?>" data-mode="false" style="width: 130px;">
                                                        </td>
                                                        <td class="text-center">%</td>
                                                        <td class="text-center">=</td>
                                                        <td class="text-right">
                                                            <input type="text" id="cal_diskon_price" name="TOTAL_DISKON_INPUT" class="form-control form-control-sm input-container text-end input-number" placeholder="Rupiah" value="<?= $this->input->post('TOTAL_DISKON_INPUT') ?>">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="vertical-align:middle; font-weight:400;">PPN</td>
                                                        <td>:</td>
                                                        <td colspan="3" class="text-left">
                                                            <?php
                                                            $defaultValue = null;
                                                            $ppn_data = $ppn_code->result();
                                                            foreach ($ppn_data as $pc) {
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
                                                                <?php foreach ($ppn_data as $pc): ?>
                                                                    <option
                                                                        value="<?= $pc->PERCENTAGE ?>"
                                                                        data-code="<?= $pc->PPN_CODE ?>"
                                                                        data-percentage="<?= $pc->PERCENTAGE ?>"
                                                                        <?= set_value('ppn_code_selected') == $pc->PPN_CODE ? 'selected' : ($defaultValue == $pc->PPN_CODE ? 'selected' : '') ?>>
                                                                        <?= strtoupper($pc->PPN_CODE) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <input type="hidden" name="ppn_code_selected" id="ppn_code_selected" value="<?= set_value('ppn_code_selected') ?>">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class=" col-lg-1 col-md-12 col-sm-12">
                                            <input type="hidden" name="TOTAL_DISKON_INPUT_HIDDEN" id="hid_diskon_input" value="" title="hid_diskon_input">
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
                                                        <td style="font-weight:400;">Total</td>
                                                        <td>:</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-right" style="text-align: right">
                                                            <div id="v_total_amount" style="font-family: monospace;"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight:400;">Diskon</td>
                                                        <td>:</td>
                                                        <td class="text-left">
                                                        </td>
                                                        <td></td>
                                                        <td class="text-right" style="text-align: right">
                                                            <div id="v_diskon" style="font-family: monospace;"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight:400;">PPN</td>
                                                        <td>:</td>
                                                        <td class="text-left">
                                                        </td>
                                                        <td></td>
                                                        <td class="text-right" style="text-align: right">
                                                            <div id="v_ppn_amount" style="font-family: monospace;"></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="font-weight:400;">
                                                            <h6>GRAND TOTAL</h6>
                                                        </td>
                                                        <td>:</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-right text-danger" style="text-align: right">
                                                            <h6>
                                                                <div id="v_total_net" style="font-family: monospace;"></div>
                                                            </h6>
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
</div>
<div id="modalMrq" class="modal fade" style="font-size: 12px;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="modalTitleForm"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    
                    <table class="table table-bordered table-striped table-sm" id="table-item">
                        <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
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
        </div>
    </div>
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

        setTimeout(calculateGrandTotal, 100);

        tableDetail = $('#table-detail').DataTable({
            ordering: false,
            autoWidth: false,
            paging: false,
            columnDefs: [{
                    targets: 0,
                    width: "2%",
                    className: "text-center",
                    createdCell: function(td) { td.style.fontFamily = 'monospace'; }
                }, 
                { targets: 1, width: "2%", className: "text-center", },
                { targets: 2, width: "15%", className: "ellipsis", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 3, width: "20%", className: "ellipsis", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 4, width: "15%", className: "ellipsis text-center", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 5, width: "15%", className: "ellipsis", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 6, width: "10%", className: "ellipsis text-end", createdCell: function(td) { td.style.fontFamily = 'monospace'; td.style.cursor = 'pointer'; } }, 
                { targets: 7, width: "10%", className: "ellipsis", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 8, width: "15%", className: "ellipsis text-end", createdCell: function(td) { td.style.fontFamily = 'monospace'; td.style.cursor = 'pointer'; } }, 
                { targets: 9, width: "15%", className: "ellipsis text-end", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 10, width: "15%", className: "ellipsis text-end", createdCell: function(td) { td.style.fontFamily = 'monospace'; td.style.cursor = 'pointer'; } }, 
                { targets: 11, width: "15%", className: "ellipsis text-end", createdCell: function(td) { td.style.fontFamily = 'monospace'; td.style.cursor = 'pointer'; } }, 
                { targets: 12, width: "15%", className: "ellipsis text-end", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 13, width: "15%", className: "ellipsis", createdCell: function(td) { td.style.fontFamily = 'monospace'; td.style.cursor = 'pointer'; } }, 
            ],
        });

        tableItem = $('#table-item').DataTable({
            autoWidth: false,
            columnDefs: [{
                    targets: 0, className: "text-center",
                }, 
                { targets: 1, className: "text-center", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 2, className: "ellipsis text-center", createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 3, className: "ellipsis text-center", render: function(data) { if (!data) return '-'; let limit = 20; let text = data.length > limit ? data.substring(0, limit) + '...' : data; return `<span title="${data}">${text}</span>`; }, createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 4, className: "ellipsis", render: function(data) { if (!data) return '-'; let limit = 20; let text = data.length > limit ? data.substring(0, limit) + '...' : data; return `<span title="${data}">${text}</span>`; }, createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 5, className: "ellipsis", render: function(data) { if (!data) return '-'; let limit = 20; let text = data.length > limit ? data.substring(0, limit) + '...' : data; return `<span title="${data}">${text}</span>`; }, createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 6, className: "ellipsis", render: function(data) { if (!data) return '-'; let limit = 20; let text = data.length > limit ? data.substring(0, limit) + '...' : data; return `<span title="${data}">${text}</span>`; }, createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 7, className: "ellipsis text-center", render: function(data) { if (!data) return '-'; let limit = 15; let text = data.length > limit ? data.substring(0, limit) + '...' : data; return `<span title="${data}">${text}</span>`; }, createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 8, className: "ellipsis text-end", render: function(data) { if (!data) return '-'; let limit = 20; let text = data.length > limit ? data.substring(0, limit) + '...' : data; return `<span title="${data}">${text}</span>`; }, createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 9, className: "ellipsis text-end", render: function(data) { if (!data) return '-'; let limit = 20; let text = data.length > limit ? data.substring(0, limit) + '...' : data; return `<span title="${data}">${text}</span>`; }, createdCell: function(td) { td.style.fontFamily = 'monospace'; } }, 
                { targets: 10, className: "ellipsis", render: function(data) { if (!data) return '-'; let limit = 20; let text = data.length > limit ? data.substring(0, limit) + '...' : data; return `<span title="${data}">${text}</span>`; }, createdCell: function(td) { td.style.fontFamily = 'monospace'; }, }, 
            ],
            autoWidth: false, paging: true, searching: true, ordering: false,
        });
        var dateFilterHTML = `
            <div class="d-flex align-items-top gap-2 me-3">
                <label class="mt-1 text-nowrap">Periode:</label>
                <input type="date" id="min-date" class="form-control form-control-sm" style="max-width: 130px;height:29px">
                <span class="mt-1">-</span>
                <input type="date" id="max-date" class="form-control form-control-sm" style="max-width: 130px;height:29px">
            </div>
        `;

        $('#table-item_wrapper .dataTables_filter')
            .addClass('d-flex align-items-top justify-content-end')
            .prepend(dateFilterHTML);

        $(document).on('change', '#min-date, #max-date', function() {
            if ($(this).attr('id') == 'min-date') {
                var minDateVal = $(this).val();
                var $maxDateInput = $(document).find('#max-date');
                var currentMaxVal = $maxDateInput.val();
                
                $maxDateInput.attr('min', minDateVal); 
                
                if (currentMaxVal && minDateVal > currentMaxVal) {
                    $maxDateInput.val(minDateVal);
                }
            }
            tableItem.draw();
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            if(settings.sTableId != 'table-item') return true;
            var min = $(document).find('#min-date').val();
            var max = $(document).find('#max-date').val();
            
            var fullDateStr = data[3] || ""; 
            
            var dateStr = fullDateStr.substring(0, 10); 

            if (!min && !max) {
                return true;
            }
            if (!min && dateStr <= max) {
                return true;
            }
            if (min <= dateStr && !max) {
                return true;
            }
            if (min <= dateStr && dateStr <= max) {
                return true;
            }
            
            return false;
        });

        let oldDetail = <?= json_encode($detail ?? []) ?>;

        if (oldDetail && oldDetail.kode_item) {
            oldDetail.kode_item.forEach(function(kode, i) {
                let nomor = tableDetail.rows().count() + 1;

                let inventory_in_detail_id = oldDetail.inventory_in_detail_id[i];
                let inventory_in_id = oldDetail.inventory_in_id[i];
                let coa_suspend_id = oldDetail.coa_suspend_id[i];
                let item_id = oldDetail.item_id[i] ?? '';
                let base_qty = oldDetail.base_qty[i] ?? '';
                let keterangan = oldDetail.keterangan[i] ?? '';
                let berat = oldDetail.berat[i] ?? '';
                let balance = oldDetail.balance[i] ?? '';
                let memo = oldDetail.memo[i] ?? '';
                let harga_input = oldDetail.harga_input[i] ?? '';
                let harga = oldDetail.harga[i] ?? '';
                let diskon_harga = oldDetail.diskon_harga[i] ?? '';
                let diskon_persentase = oldDetail.diskon_persentase[i] ?? '';
                let subtotal = oldDetail.subtotal[i] ?? '';

                let no_transaksi = oldDetail.no_transaksi[i] ?? '';
                let nama_item = oldDetail.nama_item[i] ?? '';
                let jumlah = oldDetail.jumlah[i] ?? '';
                let satuan = oldDetail.satuan[i] ?? '';

                let rowNode = tableDetail.row.add([
                    nomor,
                    `<input type="checkbox" class="chkDetail">`,
                    `<span class="ellipsis" title="${no_transaksi}">
                        ${ellipsis(no_transaksi)}
                    </span>
                    <input type="hidden" name="detail[no_transaksi][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[inventory_in_detail_id][]" value="${inventory_in_detail_id}">
                    <input type="hidden" name="detail[inventory_in_id][]" value="${inventory_in_id}">
                    <input type="hidden" name="detail[coa_suspend_id][]" value="${coa_suspend_id}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${$.inputNumber.format(base_qty)}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">`,
                    `<span class="ellipsis" title="${nama_item}">
                        ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,
                    `<span class="ellipsis" title="${kode}">
                        ${ellipsis(kode)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode}">`,
                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly>${memo}</textarea>`,
                    // jumlah
                    `<span class="view-mode qty-view">${$.inputNumber.format(jumlah)}</span>
                    <input type="text" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab text-end input-number" name="detail[jumlah][]" value="${$.inputNumber.format(jumlah)}" data-balance="${Number(balance)}">`,
                    // satuan
                    `<span class="ellipsis" title="${satuan}">
                        ${ellipsis(satuan)}
                    </span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">`,
                    // harga input
                    `<span class="view-mode harga-view">${$.inputNumber.format(harga_input)}</span>
                    <input type="text" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[harga_input][]" value="${$.inputNumber.format(harga_input)}">`,
                    // harga
                    `<span class="harga-input-b">${$.inputNumber.format(harga)}</span>
                    <input type="hidden" name="detail[harga][]" value="${harga}">`,
                    // diskon rp (Perbaikan BUG 1: Tambah class diskon-harga-view)
                    `<span class="view-mode harga-view diskon-harga-view">${$.inputNumber.format(diskon_harga)}</span>
                    <input type="text" class="form-control form-control-sm diskon-harga edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[diskon_harga][]" value="${$.inputNumber.format(diskon_harga)}">`,
                    // diskon %
                    `<span class="view-mode">${diskon_persentase}</span>
                    <input type="text" class="form-control form-control-sm edit-mode d-none enter-as-tab persen-detail" name="detail[diskon_persentase][]" value="${diskon_persentase}">`,
                    // subtotal
                    `<span class="subtotal">${$.inputNumber.format(subtotal)}</span>
                    <input type="hidden" name="detail[subtotal][]" value="${subtotal}">`,
                    // keterangan
                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly>${keterangan}</textarea>`,
                ]).node();
                
                $(rowNode).addClass('tr-height-30');
                $(rowNode).find('.input-number').inputNumber(); 
            });
            toggleStorageDisabled();
            tableDetail.draw(false);
            
            // Re-trigger perhitungan row untuk sinkronisasi nilai input lama
            setTimeout(refreshPPN, 200);
        }

        var flashsuccess = $('#flashSuccess').data('success');
        var flashwarning = $('#flashWarning').data('warning');
        var flasherror = $('#flashError').data('error');

        if (flashsuccess) { Swal.fire({ icon: 'success', title: 'Success', text: flashsuccess }) }
        if (flashwarning) { Swal.fire({ icon: 'warning', title: 'Warning', text: flashwarning }) }
        if (flasherror)   { Swal.fire({ icon: 'error', title: 'Error', text: flasherror }) }

        $('#location').prop('disabled', true);
        $('#location').on('select2:opening', function(e) { e.preventDefault(); });

        let initialSupplier = $('#suplier option:selected').data('person_site_id');
        let oldLocation = "<?= set_value('location') ?>";
        if (initialSupplier) { loadLocation(initialSupplier, oldLocation); }

        $('#supplier').on('change', function() {
            setTimeout(function() {
                var defaultPaymentTerm = $("#payment_term").val();
                let initialSupplier = $('#supplier option:selected').data('person_site_id');
                $('#person_site_id').val(initialSupplier);
                loadLocation(initialSupplier);

                var paymentTermId = $('#supplier').find(':selected').data('payment_term_id');
                if (paymentTermId) {
                    var paymentTermName = $('#supplier').find(':selected').data('payment_term_name');
                    var newOption = new Option(paymentTermName, paymentTermId, true, true);
                    $('#payment_term').append(newOption).trigger('change');
                } else {
                    $('#payment_term').val(defaultPaymentTerm).trigger('change');
                }
            }, 100);
        });

        $('#tanggal').on('change', function() {
            setTimeout(function() { updateJatuhTempo(); }, 100);
        });

        $('#payment_term').on('change.select2', function() {
            setTimeout(function() { updateJatuhTempo(); }, 100);
        });
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
                        $(this).val(prev).trigger('change.select2', { skipEvent: true });
                    }
                });
            } else {
                $(this).data("prev", current);
            }
        });

        $("#btn-modalMrq").on("click", function() {
            resetModalItem();
            $("#checkAll").prop('checked', false);
            $('#loading').show();
            var storage = $('#storage').val();
            let supplierVal = $("#supplier").val() || '';
            let supplier = supplierVal.includes('_') ? supplierVal.split('_')[0] : supplierVal;

            if (!storage) {
                $('#loading').hide();
                Swal.fire({ icon: 'warning', title: 'Warning', text: 'Storage tidak terisi, Mohon isi terlebih dahulu' });
                return;
            }

            if (!supplier) {
                $('#loading').hide();
                Swal.fire({ icon: 'warning', title: 'Warning', text: 'Supplier tidak terisi, Mohon isi terlebih dahulu' });
                return;
            }

            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>po_kny/getMrq",
                data: { storage: storage, supplier: supplier },
                dataType: "json",
                success: function(response) {
                    $('#loading').hide();
                    tableItem.clear().draw();

                    let existingInventoryInId = new Set();
                    tableDetail.rows().every(function() {
                        let node = this.node();
                        let inventoryInId = $(node).find('input[name="detail[inventory_in_detail_id][]"]').val();
                        if (inventoryInId) existingInventoryInId.add(inventoryInId);
                    });

                    if (response.status === 'success' && Array.isArray(response.data)) {
                        response.data.forEach(function(item, i) {
                            if (existingInventoryInId.has(item.INVENTORY_IN_DETAIL_ID)) return;

                            var checkbox = `
                            <input type="checkbox" class="chkRow"
                                data-inventory_in_detail_id="${item.INVENTORY_IN_DETAIL_ID}"
                                data-inventory_in_id="${item.INVENTORY_IN_ID}"
                                data-coa_suspend_id="${item.COA_SUSPEND_ID}"
                                data-item_id="${item.ITEM_ID}"
                                data-base_qty="${item.BASE_QTY}"
                                data-note="${item.NOTE}"
                                data-berat="${item.BERAT}"
                                data-harga_input="${item.HARGA_INPUT}"
                                data-harga="${item.UNIT_PRICE}"
                                data-diskon_input="${item.DISKON_INPUT}"
                                data-diskon_percen="${item.DISCOUNT_PERCEN}"
                                data-subtotal="${item.SUBTOTAL}"
                                data-status="${item.STATUS_NAME}"
                                data-tanggal="${item.DOCUMENT_DATE}"
                                data-no_transaksi="${item.DOCUMENT_NO}"
                                data-no_referensi="${item.DOCUMENT_REFF_NO}"
                                data-nama_item="${item.ITEM_DESCRIPTION.replace(/"/g, '&quot;')}"
                                data-kode_item="${item.ITEM_CODE}"
                                data-jumlah="${item.ENTERED_QTY}"
                                data-sisa="${item.BALANCE}"
                                data-satuan="${item.ENTERED_UOM}"
                            >`;
                            tableItem.row.add([
                                checkbox, i + 1, badgeStatus(item.STATUS_NAME, item.MENU_ICON), item.DOCUMENT_DATE,
                                item.DOCUMENT_NO, item.DOCUMENT_REFF_NO, item.ITEM_DESCRIPTION, item.ITEM_CODE,
                                $.inputNumber.format(parseFloat(item.ENTERED_QTY)), $.inputNumber.format(parseFloat(item.BALANCE)), item.ENTERED_UOM,
                            ]);
                        });
                        tableItem.draw();
                    }
                    $('#modalTitleForm').text('List Data');
                    $('#modalMrq').modal('show');
                }
            });
        });

        $('#modalMrq').on('shown.bs.modal', function() { $(this).find('.dataTables_filter input').focus(); });
        $("#checkAllParent").change(function() { $(".chkDetail").prop('checked', $(this).prop('checked')); });
        $("#checkAll").change(function() { $(".chkRow").prop('checked', $(this).prop('checked')); });

        $("#btnSubmit").on("click", function(e) {
            e.preventDefault();
            let rowsAdded = false;
            let allRows = tableItem.rows().nodes();
            $(allRows).find('.chkRow:checked:not(:disabled)').each(function() {
                let inventory_in_detail_id = $(this).data("inventory_in_detail_id");
                let inventory_in_id = $(this).data("inventory_in_id");
                let coa_suspend_id = $(this).data("coa_suspend_id");
                let item_id = $(this).data("item_id");
                let base_qty = $(this).data("base_qty");
                let keterangan = $(this).data("note") ?? '';
                let berat = $(this).data("berat");
                let balance = $(this).data("sisa");
                let harga_input = $(this).data('harga_input') ?? '0';
                let harga = $(this).data('harga') ?? '0';
                let diskon_input = $(this).data('diskon_input') ?? '0';
                let diskon_percen = $(this).data('diskon_percen') ?? '';
                let subtotal = $(this).data('subtotal') ?? '0';
                let no_transaksi = $(this).data("no_transaksi");
                let nama_item = $(this).data("nama_item");
                let kode_item = $(this).data("kode_item");
                let satuan = $(this).data("satuan");

                let exists = tableDetail.column(2).data().toArray().includes(inventory_in_detail_id);
                if (exists) {
                    $(this).prop('checked', false).prop('disabled', true);
                    return;
                }

                let rowNode = tableDetail.row.add([
                    "",
                    `<input type="checkbox" class="chkDetail">`,
                    `<span class="ellipsis" title="${no_transaksi}">${ellipsis(no_transaksi)}</span>
                    <input type="hidden" name="detail[no_transaksi][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[inventory_in_detail_id][]" value="${inventory_in_detail_id}">
                    <input type="hidden" name="detail[inventory_in_id][]" value="${inventory_in_id}">
                    <input type="hidden" name="detail[coa_suspend_id][]" value="${coa_suspend_id}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${$.inputNumber.format(base_qty)}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">`,
                    `<span class="ellipsis" title="${nama_item}">${ellipsis(nama_item)}</span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,
                    `<span class="ellipsis" title="${kode_item}">${ellipsis(kode_item)}</span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode_item}">`,
                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly></textarea>`,
                    // qty
                    `<span class="view-mode qty-view">${$.inputNumber.format(balance)}</span>
                    <input type="text" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab text-end input-number" name="detail[jumlah][]" value="${$.inputNumber.format(balance)}" data-balance="${Number(balance)}">`,
                    // satuan
                    `<span class="ellipsis" title="${satuan}">${ellipsis(satuan)}</span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">`,
                    // harga_input
                    `<span class="view-mode harga-view">${$.inputNumber.format(harga_input)}</span>
                    <input type="text" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[harga_input][]" value="${$.inputNumber.format(harga_input)}">`,
                    // harga
                    `<span class="harga-input-b">${$.inputNumber.format(harga)}</span>
                    <input type="hidden" name="detail[harga][]" value="${harga}">`,
                    // diskonrp
                    `<span class="view-mode harga-view diskon-harga-view">${$.inputNumber.format(diskon_input)}</span>
                    <input type="text" class="form-control form-control-sm diskon-harga edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[diskon_harga][]" value="${$.inputNumber.format(diskon_input)}">`,
                    // diskon percen
                    `<span class="view-mode">${diskon_percen}</span>
                    <input type="text" class="form-control form-control-sm edit-mode d-none enter-as-tab persen-detail" name="detail[diskon_persentase][]" value="${diskon_percen}">`,
                    // subtotal
                    `<span class="subtotal">${$.inputNumber.format(subtotal)}</span>
                    <input type="hidden" name="detail[subtotal][]" value="${subtotal}">`,
                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly></textarea>`,
                ]).node();

                let row = $(rowNode);
                let ppn = $('#cal_ppn_code').val();
                let ppn_code = $('#cal_ppn_code option:selected').text().trim();

                $(rowNode).addClass('tr-height-30');
                $(rowNode).find('.input-number').inputNumber();

                hitungSubTotal(row, ppn_code, ppn);
                rowsAdded = true;
            });

            setTimeout(() => { calculateGrandTotal(); }, 300);

            if (rowsAdded) {
                tableDetail.draw(false);
                tableDetail.columns.adjust();
                toggleStorageDisabled();
            }
            $("#modalMrq").modal("hide");
        });

        $(document).on("click", "#table-detail tbody td", function(e) {
            if ($(e.target).is("input, select, textarea")) return;
            let td = $(this);
            let span = td.find(".view-mode");
            let input = td.find(".edit-mode");

            if (span.length && input.length) {
                span.addClass("d-none");
                input.removeClass("d-none").focus().select();
            }
        });

        $(document).on("blur change", ".edit-mode", function() {
            let input = $(this);
            let span = input.prev(".view-mode");
            let value = input.val();

            if (input.hasClass("harga-edit") || input.hasClass("qty-edit")) {
                let unformatted = $.inputNumber.unformat(value) || 0;
                span.text($.inputNumber.format(unformatted));
            } else {
                span.text(value === "" ? "0" : value);
            }
            input.addClass("d-none");
            span.removeClass("d-none");
            updatePpnHidden();
        });

        let timerDetail;
        $(document).on('input change', '.qty-edit, input[name="detail[harga_input][]"], input[name="detail[diskon_harga][]"], input[name="detail[diskon_persentase][]"]', function() {
            clearTimeout(timerDetail);
            let row = $(this).closest('tr');
            let trigger = $(this).attr('name');
            let ppn = $('#cal_ppn_code').val();
            let ppn_code = $('#cal_ppn_code option:selected').text().trim();

            timerDetail = setTimeout(function() {
                hitungSubTotal(row, ppn_code, ppn, trigger);
            }, 300);
        });

        // Validasi Diskon Row agar tidak melebihi harga input (Alert on Input)
        $(document).on('input change', 'input[name="detail[diskon_harga][]"]', function() {
            let row = $(this).closest('tr');
            let diskonVal = parseFloat($.inputNumber.unformat($(this).val())) || 0;
            let hargaInput = parseFloat($.inputNumber.unformat(row.find('input[name="detail[harga_input][]"]').val())) || 0;
            let diskonPersen = row.find('input[name="detail[diskon_persentase][]"]').val();

            if (diskonVal > hargaInput) {
                let recalculatedDisc = 0;
                if (diskonPersen && diskonPersen.trim() !== '' && diskonPersen !== '0') {
                    recalculatedDisc = calcDiscount(hargaInput, diskonPersen);
                }
                
                $(this).val($.inputNumber.format(recalculatedDisc));
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Diskon Melebihi Total! Diskon rupiah tidak boleh melebihi total (' + $.inputNumber.format(hargaInput) + ')',
                    confirmButtonText: 'OK'
                });
                
                let ppn = $('#cal_ppn_code').val();
                let ppn_code = $('#cal_ppn_code option:selected').text().trim();
                hitungSubTotal(row, ppn_code, ppn);
            }
        });

        tableDetail.on("draw.dt", function() {
            tableDetail.column(0).nodes().each(function(cell, i) { cell.innerHTML = i + 1; });
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
                Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Tidak ada item yang dipilih!' });
                return;
            }
            Swal.fire({
                title: 'Yakin mau hapus?', text: `Ada ${rowsToRemove.length} item yang akan dihapus`,
                icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus', cancelButtonText: 'Batal',
                confirmButtonColor: '#d33', cancelButtonColor: '#6ebbff'
            }).then((result) => {
                if (result.isConfirmed) {
                    rowsToRemove.each(function() { tableDetail.row(this).remove(); });
                    tableDetail.draw(false);
                    $("#checkAllParent").prop("checked", false);
                    toggleStorageDisabled();
                    Swal.fire({ icon: 'success', title: 'Success', text: 'Item berhasil dihapus', timer: 1500, showConfirmButton: false });
                    let row = $(rowsToRemove);
                    let ppn = $('#cal_ppn_code').val();
                    let ppn_code = $('#cal_ppn_code option:selected').text().trim();
                    hitungSubTotal(row, ppn_code, ppn);
                }
            });
        });

        $(document).on('input change', '.qty-edit, .harga-input', function() {
            let val = $.inputNumber.unformat($(this).val());
            if (val === '' || isNaN(val)) return;
            if (val < 1) {
                $(this).val($.inputNumber.format(1));
            }
            updatePpnHidden();
        });

        refreshPPN();

        $('#cal_ppn_code').on('select2:select', function(e) { updatePpnHidden(); });

        $('#cal_ppn_code').on('change', function() {
            let ppn = $(this).val();
            let ppn_code = $('#cal_ppn_code option:selected').text();
            $('#table-detail tbody tr').each(function(index) {
                let row = $(this);
                hitungSubTotal(row, ppn_code, parseFloat(ppn));
            });
        });

        $('#cal_ppn_code').on('change', function() {
            if (!$(this).data('skipEvent')) { updatePpnHidden(); }
            refreshPPN();
        });

        $('#cal_ppn_code').on('change', function() {
            let code = $(this).find(':selected').data('code');
            $('#ppn_code_selected').val(code);
        });
    });

    $(document).on('input', '.persen-detail', function() {
        let value = $(this).val();
        value = value.replace(/[^\d+]/g, '');
        let parts = value.split('+');
        parts = parts.map(function(part) {
            if (part === '') return '';
            let num = parseInt(part, 10);
            if (isNaN(num)) return '';
            if (num > 100) num = 100;
            return num;
        });
        $(this).val(parts.join('+'));
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

    let activeMemoInput = null;
    let activeKeteranganInput = null;

    $('#table-detail tbody').on('click', 'textarea[name="detail[memo][]"]', function() {
        activeMemoInput = $(this);
        $('#modalMemoText').val($(this).val());
        $('#modalMemo').modal('show');
    });

    $('#table-detail tbody').on('click', 'textarea[name="detail[keterangan][]"]', function() {
        activeKeteranganInput = $(this);
        $('#modalKeteranganText').val($(this).val());
        $('#modalKeterangan').modal('show');
    });

    $('#btnSaveMemo').on('click', function() {
        if (!activeMemoInput) return;
        activeMemoInput.val($('#modalMemoText').val());
        $('#modalMemo').modal('hide');
    });

    $('#btnSaveKeterangan').on('click', function() {
        if (!activeKeteranganInput) return;
        activeKeteranganInput.val($('#modalKeteranganText').val());
        $('#modalKeterangan').modal('hide');
    });

    $('#modalMemo').on('hidden.bs.modal', function() { activeMemoInput = null; $('#modalMemoText').val(''); });
    $('#modalKeterangan').on('hidden.bs.modal', function() { activeKeteranganInput = null; $('#modalKeteranganText').val(''); });

    document.addEventListener('input', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;
        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text().trim();
        const input = e.target;
        const balance = parseFloat(input.dataset.balance);
        let value = $.inputNumber.unformat(input.value);
        const row = $(input).closest("tr");

        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) span.textContent = $.inputNumber.format(val);
        }

        if (value > balance) {
            Swal.fire({
                icon: 'warning', title: 'Jumlah melebihi balance',
                text: 'Jumlah tidak boleh melebihi balance (' + $.inputNumber.format(balance) + ')',
                confirmButtonText: 'OK'
            }).then(() => {
                input.value = $.inputNumber.format(balance);
                input.focus();
                hitungSubTotal(row, ppn_code, ppn);
                updateSpan(balance);
            });
            return;
        }
    });

    document.addEventListener('blur', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;
        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text().trim();
        const input = e.target;
        const row = $(input).closest("tr");
        const balance = parseFloat(input.dataset.balance);
        let value = $.inputNumber.unformat(input.value);

        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) span.textContent = $.inputNumber.format(val);
        }

        if (value <= 0 || isNaN(value)) {
            Swal.fire({
                icon: 'warning', title: 'Jumlah tidak valid',
                text: 'Jumlah harus lebih dari 0',
                confirmButtonText: 'OK'
            }).then(() => {
                input.value = $.inputNumber.format(balance);
                input.focus();
                hitungSubTotal(row, ppn_code, ppn);
                updateSpan(balance);
            });
            return;
        }
    }, true);

    let diskonHeaderTimer = null;

    $('#cal_diskon_percen').on('input blur', function() {
        let persenVal = $(this).val().trim();
        let priceVal = parseFloat($.inputNumber.unformat($('#cal_diskon_price').val())) || 0;

        if (persenVal === '') {
            $('#hid_diskon_percen').val('');
            $('#cal_diskon_price').val('');
            $('#hid_diskon_input').val('');
            calculateGrandTotal();
            return;
        }
        
        persenVal = persenVal.replace(/[^\d+]/g, '');
        $(this).val(persenVal);
        $('#hid_diskon_percen').val(persenVal);

        if (diskonHeaderTimer) clearTimeout(diskonHeaderTimer);
        diskonHeaderTimer = setTimeout(function() {
            let totalAmount = 0;
            $('input[name="detail[subtotal][]"]').each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });

            if (persenVal !== '') {
                let diskonHasil = calcDiscount(totalAmount, persenVal);
                $('#cal_diskon_price').val($.inputNumber.format(diskonHasil));
                $('#hid_diskon_input').val(diskonHasil);
                calculateGrandTotal();
            }
        }, 300);
    });

    let diskonPriceTimer;

    // Validasi Global Diskon dan Event Input
    $('#cal_diskon_price').on('input change', function() {
        let val = parseFloat($.inputNumber.unformat($(this).val())) || 0;
        let totalAmount = parseFloat($('#hid_total_amount').val()) || 0;
        let persenInput = $('#cal_diskon_percen').val().trim(); // Ambil persentase kalau ada

        if (val > totalAmount) {
            let recalculatedDisc = 0;
            if (persenInput !== '') {
                recalculatedDisc = calcDiscount(totalAmount, persenInput);
            }
            
            $(this).val($.inputNumber.format(recalculatedDisc));
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Diskon Melebihi Total! Diskon rupiah tidak boleh melebihi total (' + $.inputNumber.format(totalAmount) + ')',
                confirmButtonText: 'OK'
            });
            calculateGrandTotal();
            return;
        }

        $('#hid_diskon_input').val(val);
        if (diskonPriceTimer) clearTimeout(diskonPriceTimer);
        diskonPriceTimer = setTimeout(function() {
            calculateGrandTotal();
        }, 300);
    });

    $('#cal_ppn_code').on('change', function() {
        calculateGrandTotal();
        updatePpnHidden();
    });

    function refreshPPN() {
        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text().trim();
        if (ppn !== '' && ppn_code) {
            $('#table-detail tbody tr').each(function() { hitungSubTotal($(this), ppn_code, ppn); });
            calculateGrandTotal();
        }
    }

    function updatePpnHidden() {
        let ppn = $('#cal_ppn_code').val() || '0';
        let ppn_code = $('#cal_ppn_code option:selected').text().trim() || '';

        $('#hid_ppn').val(ppn);
        $('#hid_ppn_code').val(ppn_code);

        $('#table-detail tbody tr').each(function() { hitungSubTotal($(this), ppn_code, parseFloat(ppn)); });
        calculateGrandTotal();
    }

    function hitungSubTotal(row, ppn_code = '', ppn = 0, trigger = '') {
        let qty = parseFloat($.inputNumber.unformat(row.find(".qty-edit").val())) || 0;
        let harga_input = parseFloat($.inputNumber.unformat(row.find('input[name="detail[harga_input][]"]').val())) || 0;
        let diskon_persentase = row.find('input[name="detail[diskon_persentase][]"]').val();

        if (trigger === "detail[diskon_persentase][]") {
            if (!diskon_persentase || diskon_persentase.trim() === '') {
                row.find('input[name="detail[diskon_harga][]"]').val($.inputNumber.format(0));
            }
        }

        let diskon_harga = parseFloat($.inputNumber.unformat(row.find('input[name="detail[diskon_harga][]"]').val())) || 0;
        let isPpnIncl = ppn_code && typeof ppn_code === 'string' && ppn_code.toUpperCase().includes('INCL');
        let ppnRate = parseFloat(ppn) || 0;

        let rawDiskon = diskon_harga;

        // JIKA ADA DISKON PERSENTASE
        if (diskon_persentase && diskon_persentase !== '' && diskon_persentase !== '0') {
            rawDiskon = calcDiscount(harga_input, diskon_persentase); 
            row.find('input[name="detail[diskon_harga][]"]').val($.inputNumber.format(rawDiskon));
        }

        let rawSubtotal = (harga_input - rawDiskon) * qty;
        if (rawSubtotal < 0) rawSubtotal = 0;

        // INCL hanya mengubah display tanpa memotong raw input
        let displayHarga = harga_input;
        let displayDiskon = rawDiskon;
        let displaySubtotal = rawSubtotal;

        if (isPpnIncl && ppnRate > 0) {
            displayHarga = harga_input / (1 + ppnRate / 100);
            displayDiskon = rawDiskon / (1 + ppnRate / 100);
            displaySubtotal = rawSubtotal / (1 + ppnRate / 100);
        }

        // UPDATE VIEW
        row.find(".harga-input-b").text($.inputNumber.format(displayHarga));
        row.find('.diskon-harga-view').text($.inputNumber.format(displayDiskon));
        row.find(".subtotal").text($.inputNumber.format(displaySubtotal));

        // UPDATE HIDDEN FIELD MENGGUNAKAN RAW INPUT
        row.find('input[name="detail[harga][]"]').val(harga_input);
        row.find('input[name="detail[subtotal][]"]').val(rawSubtotal);

        calculateGrandTotal();
    }

    function parseDiscountFormula(formula) {
        if (!formula || formula.trim() === '') return { type: 'none', value: 0 };
        let parts = formula.split('+').map(p => p.trim()).filter(p => p !== '');
        if (parts.length === 0) return { type: 'none', value: 0 };

        let hasRupiah = parts.some(p => /^0\d+$/.test(p));
        if (hasRupiah) {
            let totalRp = parts.reduce((sum, p) => sum + (parseInt(p, 10) || 0), 0);
            return { type: 'rupiah', value: totalRp };
        } else {
            let totalPct = parts.reduce((sum, p) => sum + (parseFloat(p) || 0), 0);
            return { type: 'persen', value: totalPct };
        }
    }

    function calculatePPN(totalAmount, discountAmount, ppnRate, ppnCode, setup = {}) {
        let pembulatan = setup.PEMBULATAN_PPN || 0; 
        let isInclude = ppnCode && ppnCode.toUpperCase().includes('INCL');
        let taxableBase = totalAmount - discountAmount;

        let ppnAmount = 0;
        if (ppnRate > 0) {
            if (isInclude) {
                ppnAmount = taxableBase * (ppnRate / 100);
            } else {
                ppnAmount = (taxableBase * ppnRate) / 100;
            }
            if (pembulatan === 1) ppnAmount = Math.ceil(ppnAmount);
            else if (pembulatan === 2) ppnAmount = Math.floor(ppnAmount);
            else if (pembulatan === 3) ppnAmount = Math.round(ppnAmount);
        }
        return parseFloat(ppnAmount.toFixed(2));
    }

    function calculateGrandTotal() {
        let totalAmount = 0;
        $('input[name="detail[subtotal][]"]').each(function() {
            totalAmount += parseFloat($(this).val()) || 0;
        });

        $('#hid_total_amount').val(totalAmount);

        let ppnRate = parseFloat($('#cal_ppn_code').val()) || 0;
        let ppnCode = $('#cal_ppn_code option:selected').text().trim();
        let isIncl = ppnCode.toUpperCase().includes('INCL') && ppnRate > 0;
        
        $('#hid_ppn').val(ppnRate);
        $('#hid_ppn_code').val(ppnCode);

        let diskonPersenInput = ($('#cal_diskon_percen').val() || '').trim();
        let diskonRpInput = parseFloat($.inputNumber.unformat($('#cal_diskon_price').val())) || 0;
        let diskonPersenParsed = { value: 0 };
        let diskonRpParsed = 0;

        if (diskonPersenInput !== '') {
            let baseAmount = totalAmount; // Menggunakan raw amount
            diskonPersenParsed = parseDiscountFormula(diskonPersenInput);
            diskonRpParsed = calcDiscount(baseAmount, diskonPersenInput);
        } else {
            diskonRpParsed = diskonRpInput; 
            if (totalAmount > 0) {
                diskonPersenParsed.value = (diskonRpParsed / totalAmount) * 100;
            }
        }

        $('#hid_diskon_percen').val(diskonPersenParsed.value);
        $('#hid_diskon_input').val(diskonRpParsed);
        $('#hid_diskon_price').val(diskonRpParsed);

        // Perbaikan BUG 2: Jangan menimpa UI dengan diskonRpInput lama kalau diskonPersenInput ada isi
        if (!$('#cal_diskon_price').is(':focus')) {
            $('#cal_diskon_price').val($.inputNumber.format(diskonRpParsed));
        }

        // TAMPILAN (DISPLAY) vs HIDDEN (RAW INPUT)
        let displayTotalAmount = totalAmount;
        let displayDiskon = diskonRpParsed;
        let displayPpnAmount = 0;
        let displayTotalNet = 0;
        
        let hiddenPpnAmount = 0;
        let hiddenTotalNet = 0;

        if (isIncl) {
            // INCL: Nilai display dikonversi mundur agar PPN sudah di dalam harga aslinya
            displayTotalAmount = totalAmount / (1 + ppnRate / 100);
            displayDiskon = diskonRpParsed / (1 + ppnRate / 100);
            
            let totalNetRaw = totalAmount - diskonRpParsed; // Grand total raw
            let dpp = totalNetRaw / (1 + ppnRate / 100); // Dasar Pengenaan Pajak
            displayPpnAmount = totalNetRaw - dpp; // Amount PPN
            displayTotalNet = totalNetRaw; // Grand Total dari Inclusive adalah angka asli sebelum ditambah apa-apa
            
            hiddenPpnAmount = displayPpnAmount; // PPN value dikirim
            hiddenTotalNet = displayTotalNet; // Net value dikirim
        } else {
            // EXCL atau TANPA PPN:
            displayPpnAmount = calculatePPN(totalAmount, diskonRpParsed, ppnRate, ppnCode, { PEMBULATAN_PPN: 0 });
            displayTotalNet = totalAmount - diskonRpParsed + displayPpnAmount;
            
            hiddenPpnAmount = displayPpnAmount;
            hiddenTotalNet = displayTotalNet;
        }

        // Render Display (Khusus untuk Views / UI)
        $('#v_total_amount').text($.inputNumber.format(displayTotalAmount));
        $('#v_diskon').text($.inputNumber.format(displayDiskon));
        $('#v_ppn_amount').text($.inputNumber.format(displayPpnAmount));
        $('#v_total_net').text($.inputNumber.format(displayTotalNet));

        // Submit Values (HIDDEN - RAW)
        $('#hid_ppn_amount').val(hiddenPpnAmount);
        $('#hid_total_net').val(hiddenTotalNet);

        $('input[name="TOTAL_AMOUNT"]').val(totalAmount);
        $('input[name="TOTAL_DISKON_INPUT_HIDDEN"]').val(diskonRpParsed);
        $('input[name="PPN_AMOUNT"]').val(hiddenPpnAmount);
        $('input[name="TOTAL_NET"]').val(hiddenTotalNet);
    }

    function toggleStorageDisabled() {
        if (!tableDetail) return;
        let hasDetail = tableDetail.rows().count() > 0;
        let $supplier = $("#supplier");
        let $storage = $("#storage");

        if (hasDetail) {
            $supplier.prop("disabled", true).trigger("change.select2");
            $storage.prop("disabled", true).trigger("change.select2");
        } else {
            $supplier.prop("disabled", false).trigger("change.select2");
            $storage.prop("disabled", false).trigger("change.select2");
        }
    }

    function resetModalItem() {
        tableItem.search('').columns().search('').draw();
        $('#checkAll').prop('checked', false);
        $('#tableItem').find('.chkRow').prop('checked', false).prop('disabled', false);
    }

    function ellipsis(text, limit = 25) {
        if (!text) return '-';
        return text.length > limit ? text.substring(0, limit) + '...' : text;
    }

    function loadLocation(supplier, selectedLocation = null) {
        $('#location').removeClass('readonly-select');
        $('#location').empty().prop('disabled', true).trigger('change');
        if (!supplier) return;

        $.ajax({
            url: "<?= base_url('po_kny/get_location_by_supplier') ?>",
            type: "POST",
            data: { supplier: supplier },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    let locations = response.data;
                    $.each(locations, function(i, item) {
                        let selected = "";
                        if (selectedLocation && selectedLocation == item.PERSON_SITE_ID) { selected = "selected"; } 
                        else if (!selectedLocation && item.PRIMARY_SHIP === "Y") { selected = "selected"; }
                        $('#location').val(item.SITE_NAME);
                        $('#location_id').val(item.PERSON_SITE_ID);
                        $('#address').val((item.ADDRESS1 ?? '') + '\n' + (item.CITY ?? ''));
                    });
                }
            }
        });
    }

    function updateJatuhTempo() {
        let tanggal = $('#tanggal').val();
        if (!tanggal) return;
        let days = parseInt($('#payment_term option:selected').data('number_days')) || 0;
        let dateObj = new Date(tanggal);
        dateObj.setDate(dateObj.getDate() + days);
        let year = dateObj.getFullYear();
        let month = ("0" + (dateObj.getMonth() + 1)).slice(-2);
        let day = ("0" + dateObj.getDate()).slice(-2);
        let hours = ("0" + dateObj.getHours()).slice(-2);
        let minutes = ("0" + dateObj.getMinutes()).slice(-2);
        let formatted = `${year}-${month}-${day}T${hours}:${minutes}`;
        $('#jatuh_tempo').val(formatted);
    }

    $("form").on("submit", function(e) {
        $("#supplier").prop("disabled", false);
        $("#storage").prop("disabled", false);
    });
</script>