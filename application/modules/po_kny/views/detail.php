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

    #table-detail th:nth-child(2),
    #table-detail td:nth-child(2) {
        display: none !important;
    }

    .table-bordered td,
    .table-bordered th {
        border: 1px solid #dee2e6 !important;
    }

    .label-status span {
        font-size: 1rem !important;
        width: 100% !important;
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
                                <a href="<?= base_url('po_kny') ?>" class="text-decoration-underline">PO KNY</a>
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
                        <form action="" method="post" id="myForm">
                            <div class="row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center gap-2 label-status">
                                    <h5 id="statusPoId" style="width: 100px;"></h5>
                                    <h5 style="width: 100px;" id="readonlyPoId"></h5>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <a href="<?= base_url('po_kny/add') ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Tambah">
                                        <i class="ri ri-add-box-fill"></i>
                                    </a>
                                    <button type="submit" class="btn btn-success btn-sm" name="submit" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan">
                                        <i class="ri ri-save-3-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" name="del-submit" id="del-submit" data-toggle="tooltip" data-placement="bottom" title="hapus" data-id_del="<?= $this->encrypt->encode($data->INVOICE_ID); ?>">
                                        <i class="ri ri-delete-bin-5-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.replace(window.location.pathname);" data-toggle="tooltip" data-placement="bottom" title="Reload">
                                        <i class="ri ri-reply-fill"></i>
                                    </button>
                                    <a href="<?= site_url('po_kny/print/' . base64url_encode($this->encrypt->encode($data->INVOICE_ID))) ?>" id="btn-print" target="_blank" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Print">
                                        <i class="ri ri-printer-fill"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <input type="hidden" name="invoice_id" id="invoice_id" value="<?= $this->encrypt->encode($data->INVOICE_ID); ?>">
                                            <label for="no_transaksi">No Transaksi:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="no_transaksi" id="no_transaksi" class="form-control <?= form_error('no_transaksi') ? 'is-invalid' : null; ?>" placeholder="Auto Generate" readonly value="<?= $this->input->post('no_transaksi') ?? $data->DOCUMENT_NO; ?>">
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
                                                <select name="supplier" id="supplier" class="form-control select2 <?= form_error('supplier') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Supplier --</option>
                                                    <?php
                                                    $selected_supplier = $this->input->post('supplier') ?? ($data->PERSON_ID ?? '');
                                                    $selected_person_site_id = $this->input->post('person_site_id') ?? ($data->PERSON_SITE_ID ?? '');
                                                    ?>
                                                    <?php foreach ($supplier->result() as $sp): ?>
                                                        <option value="<?= $sp->PERSON_ID ?>" <?= ($selected_supplier == $sp->PERSON_ID && $selected_person_site_id == $sp->PERSON_SITE_ID) ? 'selected' : '' ?> data-person_site_id="<?= $sp->PERSON_SITE_ID ?>" data-payment_term_id="<?= $sp->PAYMENT_TERM_ID ?>">
                                                            <?= strtoupper($sp->PERSON_NAME) . ' - [' . strtoupper($sp->PERSON_CODE) . '] - ' . strtoupper($sp->SITE_NAME) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="hidden" name="person_site_id" id="person_site_id" value="<?= $selected_person_site_id ?>">
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
                                                <input type="text" name="location" id="location" class="form-control" placeholder="Enter Location" readonly>
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
                                                        <?php
                                                        $defaultPaymentTerm = null;
                                                        foreach ($payment_term->result() as $pt) {
                                                            if ($pt->PRIMARY_FLAG == 'Y') {
                                                                $defaultPaymentTerm = $pt->PAYMENT_TERM_ID;
                                                                break;
                                                            }
                                                        }
                                                        ?>
                                                        <select name="payment_term" id="payment_term" class="form-control select2 <?= form_error('payment_term') ? 'is-invalid' : null; ?>">
                                                            <?php if (!$defaultPaymentTerm): ?>
                                                                <option value="">-- Selected payment_term --</option>
                                                            <?php endif; ?>
                                                            <?php $param = $this->input->post('payment_term') ?? $data->PAYMENT_TERM_ID; ?>
                                                            <?php foreach ($payment_term->result() as $pt): ?>
                                                                <option
                                                                    value="<?= $pt->PAYMENT_TERM_ID ?>"
                                                                    <?= $param ==  $pt->PAYMENT_TERM_ID ? 'selected' : ($defaultPaymentTerm == $pt->PAYMENT_TERM_ID ? 'selected' : '') ?> data-number="<?= $pt->NUMBER_DAYS ?>">
                                                                    <?= $pt->PAYMENT_TERM_NAME ?>
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
                                                        <input type="datetime-local" name="jatuh_tempo" id="jatuh_tempo" class="form-control <?= form_error('jatuh_tempo') ? 'is-invalid' : null; ?>" placeholder="Enter Jatuh Tempo" value="<?= $this->input->post('jatuh_tempo') ?? $data->JTEMPO ?>" readonly>
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
                                                <input type="datetime-local" name="tanggal" id="tanggal" class="form-control <?= form_error('tanggal') ? 'is-invalid' : null; ?>" placeholder="Enter Tanggal" value="<?= $this->input->post('tanggal') ?? $data->DOCUMENT_DATE ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('tanggal') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="no_reff">No Reff:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-price-tag-3-fill"></i>
                                                </span>
                                                <input type="text" name="no_reff" id="no_reff" class="form-control <?= form_error('no_reff') ? 'is-invalid' : null; ?>" placeholder="Enter No Reff" value="<?= $this->input->post('no_reff') ?? $data->DOCUMENT_REFF_NO; ?>">
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
                                                    <?php $param = $this->input->post('storage') ?? $data->WAREHOUSE_ID; ?>
                                                    <?php foreach ($storage->result() as $ms): ?>
                                                        <option
                                                            value="<?= $ms->WAREHOUSE_ID ?>"
                                                            <?= $param ==  $ms->WAREHOUSE_ID ? 'selected' : ($defaultValue == $ms->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($ms->WAREHOUSE_NAME) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('storage') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan">Keterangan:</label>
                                            <div class="input-group">
                                                <textarea name="keterangan" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"><?= $this->input->post('keterangan') ?? $data->NOTE; ?></textarea>
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
                                            <button type="button" id="removeRow" class="btn btn-danger btn-sm" style="width: 55px;height:29.89px;">
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
                                                    <th style="padding:0; margin:0; border:none; display: none;"></th>
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
                                                <?php $dataDetail = $this->db->query("SELECT
                                                invoice_detail.*,
                                                bl.DOCUMENT_NO no_transaksi,
                                                i.ITEM_CODE,
                                                CASE
                                                    WHEN inventory_in_detail.BASE_QTY = 0 
                                                OR inventory_in_detail.BASE_QTY IS NULL THEN
                                                    inventory_in_detail.ENTERED_QTY ELSE inventory_in_detail.ENTERED_QTY - ( inventory_in_detail.INVOICE_ENTERED_QTY / inventory_in_detail.BASE_QTY ) 
                                                    END AS BALANCE
                                                FROM invoice_detail
                                                JOIN invoice ON invoice.INVOICE_ID = invoice_detail.INVOICE_ID 
                                                JOIN inventory_in_detail ON invoice_detail.INVENTORY_IN_DETAIL_ID = inventory_in_detail.INVENTORY_IN_DETAIL_ID 
                                                JOIN item i ON invoice_detail.ITEM_ID = i.ITEM_ID
                                                JOIN build_detail pd ON inventory_in_detail.BUILD_DETAIL_ID = pd.BUILD_DETAIL_ID
                                                JOIN build bl ON pd.BUILD_ID = bl.BUILD_ID
                                                WHERE invoice_detail.INVOICE_ID = '{$data->INVOICE_ID}'");

                                                if ($dataDetail->num_rows() > 0) { ?>
                                                    <?php
                                                    $no = 1;
                                                    $postDetail = $this->input->post('detail');
                                                    $i = 0;
                                                    foreach ($dataDetail->result() as $dd): ?>
                                                        <tr class="tr-height-30">
                                                            <td><?= $no++ ?></td>
                                                            <td style="display: none;">
                                                                <input type="hidden" name="detail[invoice_detail_id][]" value="<?= $this->encrypt->encode($dd->INVOICE_DETAIL_ID); ?>">
                                                                <input type="hidden" name="detail[inventory_in_detail_id][]" value="<?= $dd->INVENTORY_IN_DETAIL_ID ?>">
                                                                <input type="hidden" name="detail[inventory_in_id][]" value="<?= $dd->INVENTORY_IN_ID ?>">
                                                                <input type="hidden" name="detail[coa_suspend_id][]" value="<?= $dd->COA_ID ?>">
                                                                <input type=" hidden" name="detail[item_id][]" value="<?= $dd->ITEM_ID ?>">
                                                                <input type="hidden" name="detail[base_qty][]" value="<?= number_format(rtrim(rtrim($dd->BASE_QTY, '0'), '.'), 0, '.', ',') ?>">
                                                                <input type="hidden" name="detail[berat][]" value="<?= number_format(rtrim(rtrim($dd->BERAT, '0'), '.'), 0, '.', ',') ?>">
                                                                <input type="hidden" name="detail[balance][]" value="<?= number_format(rtrim(rtrim($dd->BALANCE, '0'), '.'), 0, '.', ',') ?>">
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" class="chkDetail">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class=" ellipsis align-middle" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->no_transaksi ?>">
                                                                    <?= $dd->no_transaksi; ?>
                                                                </span>
                                                                <input type="hidden" name="detail[no_mrq][]" value="<?= $dd->no_transaksi ?>">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class="ellipsis align-middle" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->ITEM_DESCRIPTION ?>">
                                                                    <?= $dd->ITEM_DESCRIPTION; ?>
                                                                </span>
                                                                <input type="hidden" name="detail[nama_item][]" value="<?= $dd->ITEM_DESCRIPTION ?>">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class="ellipsis align-middle" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->ITEM_CODE ?>">
                                                                    <?= $dd->ITEM_CODE; ?>
                                                                </span>
                                                                <input type="hidden" name="detail[kode_item][]" value="<?= $dd->ITEM_CODE ?>">
                                                            </td>
                                                            <td>
                                                                <textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly data-toggle="tooltip" data-placement="bottom" title="<?= $postDetail['memo'][$i] ?? $dd->KET; ?>"><?= $postDetail['memo'][$i] ?? $dd->KET; ?></textarea>
                                                            </td>
                                                            <td class="ellipsis text-end">
                                                                <span class="view-mode qty-view ellipsis align-middle">
                                                                    <?= number_format(rtrim(rtrim($dd->ENTERED_QTY, '0'), '.'), 2, '.', ','); ?>
                                                                </span>
                                                                <input type="number" class="form-control form-control-sm qty auto-width edit-mode qty-edit d-none enter-as-tab" min="0" step="any" name="detail[jumlah][]" data-balance="<?= ($dd->BALANCE == 0) ? '0' : rtrim(rtrim((string)$dd->BALANCE, '0'), '.') ?>" data-invoice_detail_id="<?= $this->encrypt->encode($dd->INVOICE_DETAIL_ID) ?>" data-value_old="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>" value="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class="ellipsis" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->ENTERED_UOM ?>">
                                                                    <?= $dd->ENTERED_UOM ?>
                                                                </span>
                                                                <input type="hidden" name="detail[satuan][]" value="<?= $dd->ENTERED_UOM ?>">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class="view-mode harga-view"><?= number_format(rtrim(rtrim($dd->HARGA_INPUT, '0'), '.'), 2, '.', ','); ?></span>
                                                                <input type="number" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab" min="0" step="any" name="detail[harga_input][]" value="<?= $dd->HARGA_INPUT ?>">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class="harga-input-b"><?= number_format(rtrim(rtrim($dd->UNIT_PRICE, '0'), '.'), 2, '.', ','); ?></span>
                                                                <input type="hidden" name="detail[harga][]" value="<?= number_format(rtrim(rtrim($dd->UNIT_PRICE, '0'), '.'), 2, '.', ','); ?>" step="any">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class="view-mode harga-view diskon-harga-view"><?= number_format(rtrim(rtrim($dd->DISKON_INPUT, '0'), '.'), 2, '.', ','); ?></span>
                                                                <input type="number" class="form-control form-control-sm diskon-harga edit-mode harga-edit d-none enter-as-tab" min="0" step="any" name="detail[diskon_harga][]" value="<?= $dd->DISKON_INPUT ?>">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class="view-mode"><?= $dd->DISCOUNT_PERCEN ?></span>
                                                                <input type="text" class="form-control form-control-sm edit-mode d-none enter-as-tab persen-detail" step="any" name="detail[diskon_persentase][]" value="<?= $dd->DISCOUNT_PERCEN ?>">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class="subtotal"><?= number_format(rtrim(rtrim($dd->SUBTOTAL, '0'), '.'), 2, '.', ','); ?></span>
                                                                <input type="hidden" name="detail[subtotal][]" value="<?= number_format(rtrim(rtrim($dd->SUBTOTAL, '0'), '.'), 2, '.', ','); ?>" step="any">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly data-toggle="tooltip" data-placement="bottom" title="<?= $postDetail['keterangan'][$i] ?? $dd->NOTE; ?>"><?= $postDetail['keterangan'][$i] ?? $dd->NOTE; ?></textarea>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php } ?>
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
                                                            <input id="cal_diskon_percen" name="TOTAL_DISCOUNT_PERCEN" class="form-control form-control-sm input-container persen-detail" placeholder="Persen" value="<?= $this->input->post('TOTAL_DISCOUNT_PERCEN') ?? $data->TOTAL_DISCOUNT_PERCEN ?>" data-mode="false" style="width: 130px;">
                                                        </td>
                                                        <td class="text-center">%</td>
                                                        <td class="text-center">=</td>
                                                        <?php $total_diskon = number_format($data->TOTAL_DISKON_INPUT, 2, '.', ',');
                                                        ?>
                                                        <td class="text-right">
                                                            <input type="text" id="cal_diskon_price" name="TOTAL_DISKON_INPUT" class="form-control form-control-sm input-container" placeholder="Rupiah" value="<?= $total_diskon ?>">
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
                                                                <?php
                                                                $selected_ppn_persen = $this->input->post('cal_ppn_code') ?? ($data->PPN_PERCEN ?? '');
                                                                $selected_ppn_code = $this->input->post('ppn_code_selected') ?? ($data->PPN_CODE ?? '');
                                                                ?>
                                                                <?php foreach ($ppn_code->result() as $pc): ?>
                                                                    <option
                                                                        value="<?= $pc->PERCENTAGE ?>" data-code="<?= $pc->PPN_CODE ?>"
                                                                        <?= set_value('ppn_code_selected') == $pc->PPN_CODE ? 'selected' : ($defaultValue == $pc->PPN_CODE ? 'selected' : '') ?>>
                                                                        <?= strtoupper($pc->PPN_CODE) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                            <?= ($selected_ppn_persen == $pc->PERCENTAGE && $selected_ppn_code == $pc->PPN_CODE) ? 'selected' : '' ?>
                                                            <input type="hidden" name="ppn_code_selected" id="ppn_code_selected" value="<?= set_value('ppn_code_selected') ?>">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class=" col-lg-1 col-md-12 col-sm-12">
                                            <input type="hidden" name="TOTAL_DISKON_INPUT_HIDDEN" id="hid_diskon_input" value="<?= $data->TOTAL_DISKON_INPUT ?>" title="hid_diskon_input">
                                            <input type="hidden" name="DISKON_PERCEN" id="hid_diskon_percen" value="<?= $data->TOTAL_DISCOUNT_PERCEN ?>" title="hid_diskon_percen">
                                            <input type="hidden" name="DISKON_PRICE" id="hid_diskon_price" value="<?= $data->TOTAL_DISKON_INPUT ?>" title="hid_diskon_price">
                                            <input type="hidden" name="PPN_PERCEN" id="hid_ppn" value="<?= $data->PPN_PERCEN ?>" title="hid_ppn">
                                            <input type="hidden" name="PPN_CODE" id="hid_ppn_code" value="<?= $data->PPN_CODE ?>" title="hid_ppn_code">
                                            <input type="hidden" name="PPN_AMOUNT" id="hid_ppn_amount" value="<?= $data->PPN_AMOUNT ?>" title="hid_ppn_amount">
                                            <input type="hidden" name="TOTAL_AMOUNT" id="hid_total_amount" value="<?= $data->TOTAL_AMOUNT ?>" title="hid_total_amount">
                                            <input type="hidden" name="TOTAL_NET" id="hid_total_net" value="<?= $data->TOTAL_NET ?>" title="hid_total_net">
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
        setTimeout(function() {
            calculateGrandTotal();
            // Format diskon nominal awal
            let diskonVal = $('#cal_diskon_price').val();
            if (diskonVal && diskonVal !== '') {
                let num = parseFloat(diskonVal.replace(/,/g, ''));
                if (!isNaN(num)) {
                    $('#cal_diskon_price').val(formatNumber(num));
                }
            }
        }, 100);

        let invoice_id = $('#invoice_id').val();

        $.ajax({
            url: '<?= base_url() ?>po_kny/getStatus',
            type: 'POST',
            dataType: 'json',
            data: {
                invoice_id: invoice_id,
            },
            success: function(response) {
                $('#statusPoId').html(badgeStatus(response.data[0].DISPLAY_NAME, response.data[0].MENU_ICON));
                $('#readonlyPoId').hide();

                if (response.data[0].ITEM_FLAG === 'N') {
                    $('#readonlyPoId').show();
                    $('#readonlyPoId').html('<span class="badge bg-secondary">READ ONLY</span>');
                    $('#myForm')
                        .find('input, select, textarea, #removeRow, #btn-modalItem, td input')
                        .prop('disabled', true);

                    $('#table-detail td').css('pointer-events', 'none');

                    $('#submit').replaceWith(
                        `<span class="btn btn-success btn-sm" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan" disabled" style="pointer-events: none; opacity: 0.6; cursor: not-allowed;">
                            <i class="ri ri-save-3-fill"></i>
                        </span>`
                    );

                    $('#del-submit').replaceWith(
                        `<span class="btn btn-danger btn-sm" id="del-submit" name="del-submit" data-toggle="tooltip" data-placement="bottom" title="hapus" disabled" style="pointer-events: none; opacity: 0.6; cursor: not-allowed;">
                            <i class="ri ri-delete-bin-5-fill"></i>
                        </span>`
                    );

                    $('#removeRow').replaceWith(
                        `<span type="button" id="removeRow" class="btn btn-danger btn-sm" disabled style="width: 55px; height:29.89px; pointer-events: none; opacity: 0.6; cursor: not-allowed;">
                            <i class="fa fa-trash"></i> Del
                        </span>`
                    );

                    $('#btn-modalMrq').replaceWith(
                        `<span type="button" id="btn-modalMrq" class="btn btn-success btn-sm" disabled style="pointer-events: none; opacity: 0.6; cursor: not-allowed;">
                            <i class="ri ri-add-box-fill"></i> Add
                        </span>`
                    );
                }
            }
        });

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
                    targets: 2,
                    width: "2%",
                    className: "text-center",
                }, // checkbox
                {
                    targets: 3,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no transaksi
                {
                    targets: 4,
                    width: "20%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // nama item
                {
                    targets: 5,
                    width: "15%",
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // kode item
                {
                    targets: 6,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // memo
                {
                    targets: 7,
                    width: "10%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // jumlah
                {
                    targets: 8,
                    width: "10%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // satuan
                {
                    targets: 9,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // harga input
                {
                    targets: 10,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // harga
                {
                    targets: 11,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // diskon rp
                {
                    targets: 12,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // diskon %
                {
                    targets: 13,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // subtotal
                {
                    targets: 14,
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
                    className: "text-center"
                }, // checkbox
                {
                    targets: 1,
                    className: "text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no
                {
                    targets: 2,
                    className: "ellipsis text-center",
                    // render: function(data) {
                    //     if (!data) return '-';
                    //     let limit = 20;
                    //     let text = data.length > limit ?
                    //         data.substring(0, limit) + '...' :
                    //         data;
                    //     return `<span title="${data}">${text}</span>`;
                    // },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // status
                {
                    targets: 3,
                    className: "ellipsis text-center",
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
                    className: "ellipsis text-center",
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
                let invoice_detail_id = oldDetail.invoice_detail_id[i] ?? '';

                if (invoice_detail_id !== '') {
                    return;
                }

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

                    `<input type="hidden" name="detail[invoice_detail_id][]" value="">
                    <input type="hidden" name="detail[inventory_in_detail_id][]" value="${inventory_in_detail_id}">
                    <input type="hidden" name="detail[inventory_in_id][]" value="${inventory_in_id}">
                    <input type="hidden" name="detail[coa_suspend_id][]" value="${coa_suspend_id}">
                    <input type="hidden" name="detail[no_mrq][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${formatNumber(base_qty)}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">`,

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${no_transaksi}">
                        ${ellipsis(no_transaksi)}
                    </span>`,

                    `<span class="ellipsis" title="${nama_item}">
                        ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,

                    `<span class="ellipsis" title="${kode}">
                        ${ellipsis(kode)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode}">
                    `,

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly>${memo}</textarea>`,
                    // memo

                    `<span class="view-mode qty-view">${formatNumber(jumlah)}</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab" name="detail[jumlah][]" value="${Number(jumlah)}" min="0" step="any" data-balance="${Number(balance)}">`,

                    `<span class="ellipsis" title="${satuan}">
                        ${ellipsis(satuan)}
                    </span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">
                    `,

                    `<span class="view-mode harga-view">${formatNumber(harga_input)}</span>
                    <input type="number" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab" min="0" step="any" name="detail[harga_input][]" value="${harga_input}">`,
                    // harga input

                    `<span class="harga-input-b">${formatNumber(harga)}</span>
                    <input type="hidden" name="detail[harga][]" value="${harga}" step="any">`,
                    // harga

                    `<span class="view-mode harga-view diskon-harga-view">${formatNumber(diskon_harga)}</span>
                    <input type="number" class="form-control form-control-sm diskon-harga edit-mode harga-edit d-none enter-as-tab" min="0" step="any" name="detail[diskon_harga][]" value="${diskon_harga}">`,
                    // diskon rp

                    `<span class="view-mode">${diskon_persentase}</span>
                    <input type="text" class="form-control form-control-sm edit-mode d-none enter-as-tab persen-detail" step="any" name="detail[diskon_persentase][]" value="${diskon_persentase}">`,
                    // diskon %

                    `<span class="subtotal">${formatNumber(subtotal)}</span>
                    <input type="hidden" name="detail[subtotal][]" value="${formatNumber(subtotal)}" step="any">`,
                    // subtotal

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

        toggleStorageDisabled();

        $('#location').prop('disabled', true);

        $('#location').on('select2:opening', function(e) {
            e.preventDefault();
        });

        let initialSupplier = $('#supplier option:selected').data('person_site_id');

        let oldLocation = "<?= set_value('location') ?>";

        if (initialSupplier) {
            loadLocation(initialSupplier, oldLocation);
        }

        var defaultPaymentTerm = "<?= $defaultPaymentTerm ?>";

        $('#supplier').on('change', function() {
            let initialSupplier = $('#supplier option:selected').data('person_site_id');
            $('#person_site_id').val(initialSupplier);
            loadLocation(initialSupplier);

            var paymentTermId = $(this).find(':selected').data('payment_term_id');
            if (paymentTermId) {
                $('#payment_term').val(paymentTermId).trigger('change');
            } else {
                $('#payment_term').val(defaultPaymentTerm).trigger('change');
            }
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
            var supplier = $('#supplier').val();

            if (!storage) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Storage tidak terisi, Mohon isi terlebih dahulu',
                });
                return;
            }

            if (!supplier) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'supplier tidak terisi, Mohon isi terlebih dahulu',
                });
                return;
            }

            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>po_kny/getMrq",
                data: {
                    storage: storage,
                    supplier: supplier,
                },
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

                            if (existingInventoryInId.has(item.INVENTORY_IN_DETAIL_ID)) {
                                return;
                            }

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
                            >
                            `;
                            tableItem.row.add([
                                checkbox,
                                i + 1,
                                badgeStatus(item.STATUS_NAME, item.MENU_ICON),
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

            let existingInventoryDetailId = new Set();
            tableDetail.rows().every(function() {
                let node = this.node();
                let inventoryDetailId = $(node).find('input[name="detail[inventory_detail_id][]"]').val();

                if (inventoryDetailId) existingInventoryDetailId.add(inventoryDetailId);
            });

            let allRows = tableItem.rows().nodes();
            let nodesToDraw = [];

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
                    .includes(inventory_in_detail_id);

                if (exists) {
                    $(this).prop('checked', false).prop('disabled', true);
                    return;
                }

                let rowNode = tableDetail.row.add([
                    "",

                    `<input type="hidden" name="detail[invoice_detail_id][]" value="">
                    <input type="hidden" name="detail[inventory_in_detail_id][]" value="${inventory_in_detail_id}">
                    <input type="hidden" name="detail[inventory_in_id][]" value="${inventory_in_id}">
                    <input type="hidden" name="detail[coa_suspend_id][]" value="${coa_suspend_id}">
                    <input type="hidden" name="detail[no_mrq][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${formatNumber(base_qty)}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">`,

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${no_transaksi}">
                        ${ellipsis(no_transaksi)}
                    </span>`,

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
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab" name="detail[jumlah][]" value="${Number(balance)}" min="0" step="any" data-balance="${Number(balance)}">`,

                    `<span class="ellipsis" title="${satuan}">
                        ${ellipsis(satuan)}
                    </span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">
                    `,

                    `<span class="view-mode harga-view">${formatNumber(harga_input)}</span>
                    <input type="number" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab" min="0" step="any" name="detail[harga_input][]" value="${harga_input}">`,
                    // harga input

                    `<span class="harga-input-b">${formatNumber(harga)}</span>
                    <input type="hidden" name="detail[harga][]" value="${harga}" step="any">`,
                    // harga

                    `<span class="view-mode harga-view diskon-harga-view">${formatNumber(diskon_input)}</span>
                    <input type="number" class="form-control form-control-sm diskon-harga edit-mode harga-edit d-none enter-as-tab" min="0" step="any" name="detail[diskon_harga][]" value="${diskon_input}">`,
                    // diskon rp

                    `<span class="view-mode">${diskon_percen}</span>
                    <input type="text" class="form-control form-control-sm edit-mode d-none enter-as-tab persen-detail" step="any" name="detail[diskon_persentase][]" value="${diskon_percen}">`,
                    // diskon %

                    `<span class="subtotal">${formatNumber(subtotal)}</span>
                    <input type="hidden" name="detail[subtotal][]" value="${subtotal}" step="any">`,
                    // subtotal

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly></textarea>`,
                ]).node();

                let row = $(rowNode);
                let ppn = $('#cal_ppn_code').val();
                let ppn_code = $('#cal_ppn_code option:selected').text().trim();

                $(rowNode).addClass('tr-height-30');

                hitungSubTotal(row, ppn_code, ppn);

                rowsAdded = true;
            });

            setTimeout(() => {
                calculateGrandTotal();
            }, 300);

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

        // Update saat ada perubahan di tabel detail (jumlah/harga/diskon)
        let timerDetail;

        $(document).on(
            'input change',
            '.qty-edit, input[name="detail[harga_input][]"], input[name="detail[diskon_harga][]"], input[name="detail[diskon_persentase][]"]',
            function() {

                clearTimeout(timerDetail);

                let row = $(this).closest('tr');
                let trigger = $(this).attr('name'); // field yang berubah
                let ppn = $('#cal_ppn_code').val();
                let ppn_code = $('#cal_ppn_code option:selected').text().trim();

                timerDetail = setTimeout(function() {
                    hitungSubTotal(row, ppn_code, ppn, trigger);
                }, 300);

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

                    let row = $(rowsToRemove);
                    let ppn = $('#cal_ppn_code').val();
                    let ppn_code = $('#cal_ppn_code option:selected').text().trim();

                    hitungSubTotal(row, ppn_code, ppn);
                }
            });
        });

        $(document).on('click', '#del-submit', function() {
            let id = $(this).data('id_del');

            Swal.fire({
                title: 'Yakin mau hapus?',
                text: 'Data yang dihapus tidak bisa dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#70bcff',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url() ?>po_kny/del',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: id
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Menghapus...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: res.message,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Warning!',
                                    text: res.error,
                                    icon: 'warning'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Gagal menghapus data!', 'error');
                        }
                    });
                }
            });
        });

        $(document).on('keydown', '.jumlah, .harga-input, .diskon-harga', function(e) {
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

            updatePpnHidden();
        });

        refreshPPN();

        $('#cal_ppn_code').on('select2:select', function(e) {
            updatePpnHidden();
        });

        $('#cal_ppn_code').on('change', function() {
            let ppn = $(this).val();
            let ppn_code = $('#cal_ppn_code option:selected').text();
            // Ambil jumlah row pada table detail
            let totalRows = $('#table-detail tbody tr').length;
            // Loop setiap row
            $('#table-detail tbody tr').each(function(index) {
                let row = $(this);
                hitungSubTotal(row, ppn_code, parseFloat(ppn));
            });
        });

        $('#cal_ppn_code').on('change', function() {
            if (!$(this).data('skipEvent')) {
                updatePpnHidden();
            }
            refreshPPN();
        });

        $('#cal_ppn_code').on('change', function() {

            let code = $(this).find(':selected').data('code');

            $('#ppn_code_selected').val(code);

        });

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

        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text().trim();

        const input = e.target;
        const invoice_detail_id = input.dataset.invoice_detail_id;
        const value_old = parseFloat(input.dataset.value_old);
        const balance = parseFloat(input.dataset.balance);
        let value = parseFloat(input.value);

        const row = $(input).closest("tr");

        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) {
                span.textContent = val.toFixed(2).replace('.', ',');
            }
        }

        // Tidak boleh lebih dari balance
        if (invoice_detail_id) {
            // UPDATE
            const maxAllowed = balance + value_old;

            if (value > maxAllowed) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah melebihi balance',
                    text: 'Jumlah tidak boleh melebihi balance (' + maxAllowed + ')',
                    confirmButtonText: 'OK'
                }).then(() => {
                    input.value = value_old;
                    input.focus();
                    hitungSubTotal(row, ppn_code, ppn);
                    updateSpan(value_old);
                });
                return;
            }
        } else {
            // ADD
            if (value > balance) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah melebihi balance',
                    text: 'Jumlah tidak boleh melebihi balance (' + balance + ')',
                    confirmButtonText: 'OK'
                }).then(() => {
                    input.value = balance;
                    input.focus();
                    hitungSubTotal(row, ppn_code, ppn);
                    updateSpan(balance);
                });
                return;
            }
        }

        updateSpan(value);
    });

    // // jika jumlah kosong
    document.addEventListener('blur', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;

        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text().trim();

        const input = e.target;
        const invoice_detail_id = input.dataset.invoice_detail_id;
        const value_old = parseFloat(input.dataset.value_old);
        const row = $(input).closest("tr");
        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) {
                span.textContent = val.toFixed(2).replace('.', ',');
            }
        }

        const balance = parseFloat(input.dataset.balance);

        if (invoice_detail_id) {
            // UPDATE

            // Tidak boleh minus atau nol
            if (input.value <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah tidak valid',
                    text: 'Jumlah harus lebih dari 0',
                    confirmButtonText: 'OK'
                }).then(() => {
                    input.value = value_old;
                    input.focus();
                    hitungSubTotal(row, ppn_code, ppn);
                    updateSpan(value_old);
                });
                return;
            }

            // Tidak boleh kosong
            if (input.value === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input kosong',
                    text: 'Jumlah tidak boleh kosong',
                    confirmButtonText: 'OK'
                }).then(() => {
                    input.value = value_old;
                    input.focus();
                    hitungSubTotal(row, ppn_code, ppn);
                    updateSpan(value_old);
                });
                return;
            }
            hitungSubTotal(row, ppn_code, ppn);
            updateSpan(balance);
        } else {
            // ADD

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
                    hitungSubTotal(row, ppn_code, ppn);
                    updateSpan(balance);
                });
                return;
            }

            // Tidak boleh kosong
            if (input.value === '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input kosong',
                    text: 'Jumlah tidak boleh kosong',
                    confirmButtonText: 'OK'
                }).then(() => {
                    input.value = input.dataset.balance;
                    input.focus();
                    hitungSubTotal(row, ppn_code, ppn);
                    updateSpan(balance);
                });
                return;
            }
            hitungSubTotal(row, ppn_code, ppn);
            updateSpan(balance);
        }

    }, true);

    // Timer untuk debounce AJAX diskon
    let diskonHeaderTimer = null;

    // Update diskon persentase
    $('#cal_diskon_percen').on('input blur', function() {
        let persenVal = $(this).val().trim();
        let priceVal = $('#cal_diskon_price').val().trim();

        if (persenVal === '') {
            // reset jika kosong
            $('#hid_diskon_percen').val('');
            $('#cal_diskon_price').val('');
            $('#hid_diskon_input').val('');
            calculateGrandTotal();
            return;
        }

        // validasi angka saja
        persenVal = persenVal.replace(/[^\d+]/g, '');
        $(this).val(persenVal);
        $('#hid_diskon_percen').val(persenVal);

        // Debounce 500ms untuk input persen
        if (diskonHeaderTimer) clearTimeout(diskonHeaderTimer);
        diskonHeaderTimer = setTimeout(function() {
            let totalAmount = 0;
            $('input[name="detail[subtotal][]"]').each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });

            // Kondisi 1: hanya persen terisi
            if (persenVal !== '' && priceVal === '') {
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('so_kny/get_hitung_diskon_bertingkat_header') ?>",
                    data: {
                        total_amount: totalAmount,
                        persen: persenVal
                    },
                    dataType: "json",
                    success: function(response) {
                        let diskonHasil = parseFloat(response.data[0].harga) || 0;
                        $('#cal_diskon_price').val(formatNumber(diskonHasil));
                        $('#hid_diskon_input').val(diskonHasil.toFixed(2));
                        calculateGrandTotal();
                    },
                    error: function(err) {
                        console.error("Error hitung diskon bertingkat:", err);
                    }
                });
            }

            // Kondisi 2: persen dan price terisi
            if (persenVal !== '' && priceVal !== '') {
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('so_kny/get_hitung_diskon_bertingkat_header') ?>",
                    data: {
                        total_amount: totalAmount,
                        persen: persenVal,
                        price: priceVal
                    },
                    dataType: "json",
                    success: function(response) {
                        let diskonHasil = parseFloat(response.data[0].harga) || 0;
                        $('#cal_diskon_price').val(formatNumber(diskonHasil));
                        $('#hid_diskon_input').val(diskonHasil.toFixed(2));
                        calculateGrandTotal();
                    },
                    error: function(err) {
                        console.error("Error hitung diskon bertingkat:", err);
                    }
                });
            }
        }, 500);

        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text().trim();
        if (ppn !== '' && ppn_code) {
            $('#table-detail tbody tr').each(function() {
                let row = $(this);
                hitungSubTotal(row, ppn_code, ppn);
            });
            calculateGrandTotal();
        }
    });

    // Jika diskon price diubah
    let diskonPriceTimer = null;

    // Validasi input - hanya angka dan koma
    $('#cal_diskon_price').on('input', function() {
        let val = $(this).val().replace(/[^0-9,.]/g, '');
        $(this).val(val);
        $('#hid_diskon_input').val(val.replace(/,/g, ''));
    });

    // Format dan AJAX saat blur
    $('#cal_diskon_price').on('blur', function() {
        let persenVal = $('#cal_diskon_percen').val().trim();
        let priceVal = $(this).val().trim();

        // Format nilai
        if (priceVal !== '') {
            let num = parseFloat(priceVal.replace(/,/g, ''));
            if (!isNaN(num)) {
                $(this).val(formatNumber(num));
                $('#hid_diskon_input').val(num.toFixed(2));
            }
        }

        // hapus timer sebelumnya
        if (diskonPriceTimer) clearTimeout(diskonPriceTimer);

        // jalankan debounce 500ms
        diskonPriceTimer = setTimeout(function() {
            // Kondisi: kedua field terisi
            if (persenVal !== '' && priceVal !== '') {
                let totalAmount = 0;
                $('input[name="detail[subtotal][]"]').each(function() {
                    totalAmount += parseFloat($(this).val()) || 0;
                });

                $.ajax({
                    type: "POST",
                    url: "<?= site_url('so_kny/get_hitung_diskon_bertingkat_header') ?>",
                    data: {
                        total_amount: totalAmount,
                        persen: persenVal,
                        price: priceVal
                    },
                    dataType: "json",
                    success: function(response) {
                        let diskonHasil = parseFloat(response.data[0].harga) || 0;
                        $('#cal_diskon_price').val(formatNumber(diskonHasil));
                        $('#hid_diskon_input').val(diskonHasil.toFixed(2));
                        calculateGrandTotal();
                    },
                    error: function(err) {
                        console.error("Error hitung diskon bertingkat:", err);
                    }
                });
            }
        }, 500); // debounce 500ms

        calculateGrandTotal();
    });


    // Update PPN saat select berubah
    $('#cal_ppn_code').on('change', function() {
        calculateGrandTotal();
        updatePpnHidden();
    });

    let hitungTimerDetail = null;

    function refreshPPN() {
        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text().trim();
        if (ppn !== '' && ppn_code) {
            $('#table-detail tbody tr').each(function() {
                let row = $(this);
                hitungSubTotal(row, ppn_code, ppn);
            });
            calculateGrandTotal();
        }
    }

    function updatePpnHidden() {
        let ppn = $('#cal_ppn_code').val() || '0';
        let ppn_code = $('#cal_ppn_code option:selected').text().trim() || '';

        $('#hid_ppn').val(ppn);
        $('#hid_ppn_code').val(ppn_code);

        // ✅ TRIGGER UPDATE SEMUA BARIS DI TABEL DETAIL
        $('#table-detail tbody tr').each(function() {
            let row = $(this);
            hitungSubTotal(row, ppn_code, parseFloat(ppn));
        });

        calculateGrandTotal();
    }

    function hitungSubTotal(row, ppn_code = '', ppn = 0, trigger = '') {

        let qty = parseFloat(row.find(".qty-edit").val()) || 0;
        let harga_input = parseFloat(row.find('input[name="detail[harga_input][]"]').val()) || 0;
        let diskon_persentase = row.find('input[name="detail[diskon_persentase][]"]').val();

        // reset diskon hanya jika persen yang diubah
        if (trigger === "detail[diskon_persentase][]") {
            if (!diskon_persentase || diskon_persentase.trim() === '') {
                row.find('input[name="detail[diskon_harga][]"]').val(0);
            }
        }

        let diskon_harga = parseFloat(row.find('input[name="detail[diskon_harga][]"]').val()) || 0;

        let isPpnIncl = ppn_code && typeof ppn_code === 'string' && ppn_code.toUpperCase().includes('INCL');
        let ppnRate = parseFloat(ppn) || 0;

        let nettoHargaInput = harga_input;
        let nettoDiskonHarga = diskon_harga;

        if (isPpnIncl && ppnRate > 0) {
            nettoHargaInput = harga_input / (1 + ppnRate / 100);
            nettoDiskonHarga = diskon_harga / (1 + ppnRate / 100);
        }

        let hargaBersih = Math.max(0, nettoHargaInput - nettoDiskonHarga);

        // =====================================
        // JIKA ADA DISKON PERSENTASE
        // =====================================

        if (diskon_persentase && diskon_persentase !== '') {

            $.ajax({
                type: "POST",
                url: "<?= site_url('po_kny/get_hitung_diskon_bertingkat') ?>",
                data: {
                    harga_input: nettoHargaInput,
                    persen: diskon_persentase
                },
                dataType: "json",

                success: function(response) {

                    let diskonHasil = parseFloat(response.data[0].harga) || 0;

                    let subtotal = (nettoHargaInput - diskonHasil) * qty;
                    if (subtotal < 0) subtotal = 0;

                    row.find(".harga-input-b").text(
                        nettoHargaInput.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    );

                    row.find('input[name="detail[harga][]"]').val(nettoHargaInput.toFixed(2));

                    // auto isi diskon jika pakai persen
                    row.find('input[name="detail[diskon_harga][]"]').val(diskonHasil.toFixed(2));

                    row.find('.diskon-harga-view').text(
                        diskonHasil.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    );

                    row.find(".subtotal").text(
                        subtotal.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    );

                    row.find('input[name="detail[subtotal][]"]').val(subtotal.toFixed(2));

                    calculateGrandTotal();
                },

                error: function(err) {

                    console.error("Error hitung diskon bertingkat:", err);

                    let subtotal = hargaBersih * qty;
                    if (subtotal < 0) subtotal = 0;

                    row.find(".harga-input-b").text(
                        nettoHargaInput.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    );

                    row.find('input[name="detail[harga][]"]').val(nettoHargaInput.toFixed(2));

                    row.find('.diskon-harga-view').text(
                        nettoDiskonHarga.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    );

                    row.find(".subtotal").text(
                        subtotal.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    );

                    row.find('input[name="detail[subtotal][]"]').val(subtotal.toFixed(2));

                    calculateGrandTotal();
                }

            });

        }

        // =====================================
        // TANPA DISKON PERSENTASE
        // =====================================
        else {

            let subtotal = hargaBersih * qty;
            if (subtotal < 0) subtotal = 0;

            row.find(".harga-input-b").text(
                nettoHargaInput.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
            );

            row.find('input[name="detail[harga][]"]').val(nettoHargaInput.toFixed(2));

            // hanya tampilkan diskon manual
            row.find('.diskon-harga-view').text(
                nettoDiskonHarga.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
            );

            row.find(".subtotal").text(
                subtotal.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
            );

            row.find('input[name="detail[subtotal][]"]').val(subtotal.toFixed(2));

            calculateGrandTotal();
        }

    }

    // --- FUNGSI BANTUAN ---
    function parseDiscountFormula(formula) {
        if (!formula || formula.trim() === '') return {
            type: 'none',
            value: 0
        };
        let parts = formula.split('+').map(p => p.trim()).filter(p => p !== '');
        if (parts.length === 0) return {
            type: 'none',
            value: 0
        };

        // Jika ada angka dengan awalan '0' (misal: "05000") → anggap sebagai rupiah
        let hasRupiah = parts.some(p => /^0\d+$/.test(p));
        if (hasRupiah) {
            let totalRp = parts.reduce((sum, p(parseInt(p, 10) || 0), 0));
            return {
                type: 'rupiah',
                value: totalRp
            };
        } else {
            let totalPct = parts.reduce((sum, p) => sum + (parseFloat(p) || 0), 0);
            return {
                type: 'persen',
                value: totalPct
            };
        }
    }

    // --- HITUNG DISKON BERTINGKAT (HEADER) ---
    function DiscountHeader(totalAmount, discountFormula) {
        let parsed = parseDiscountFormula(discountFormula);
        if (parsed.type === 'rupiah') {
            return Math.min(parsed.value, totalAmount); // tidak boleh melebihi total
        }

        let remaining = totalAmount;
        let totalDiscount = 0;
        let parts = discountFormula.split('+').map(p => parseFloat(p.trim()) || 0).filter(p => p > 0);

        for (let pct of parts) {
            let disc = (remaining * pct) / 100;
            totalDiscount += disc;
            remaining -= disc;
        }
        return parseFloat(totalDiscount.toFixed(2));
    }

    // --- HITUNG PPN (EXCLUSIVE & INCLUSIVE) ---
    function calculatePPN(totalAmount, discountAmount, ppnRate, ppnCode, setup = {}) {

        let pembulatan = setup.PEMBULATAN_PPN || 0; // 0=off, 1=ceil, 2=floor, 3=round
        let isInclude = ppnCode && ppnCode.toUpperCase().includes('INCL');

        let taxableBase = totalAmount - discountAmount;

        let ppnAmount = 0;
        if (ppnRate > 0) {
            if (isInclude) {

                // Rumus: Netto = Bruto / (1 + PPN%)
                // Maka PPN = Bruto - Netto
                // let netto = taxableBase / (1 + ppnRate / 100);
                ppnAmount = taxableBase * (ppnRate / 100);

                // ppnAmount = taxableBase - netto;
            } else {
                ppnAmount = (taxableBase * ppnRate) / 100;
            }

            // Pembulatan
            if (pembulatan === 1) ppnAmount = Math.ceil(ppnAmount);
            else if (pembulatan === 2) ppnAmount = Math.floor(ppnAmount);
            else if (pembulatan === 3) ppnAmount = Math.round(ppnAmount);
        }
        return parseFloat(ppnAmount.toFixed(2));
    }

    function calculateGrandTotal() {
        // 1. Hitung total subtotal
        let totalAmount = 0;
        $('input[name="detail[subtotal][]"]').each(function() {
            totalAmount += parseFloat($(this).val()) || 0;
        });

        $('#hid_total_amount').val(totalAmount);
        $('#v_total_amount').text(formatNumber(totalAmount));

        // 2. Ambil PPN
        let ppnRate = parseFloat($('#cal_ppn_code').val()) || 0;
        let ppnCode = $('#cal_ppn_code option:selected').text().trim();
        $('#hid_ppn').val(ppnRate);
        $('#hid_ppn_code').val(ppnCode);
        let isIncl = ppnCode.toUpperCase().includes('INCL') && ppnRate > 0;

        // 3. Ambil input diskon
        let diskonPersenInput = ($('#cal_diskon_percen').val() || '').trim();
        let diskonRpInput = ($('#cal_diskon_price').val() || '').replace(/,/g, '');
        let diskonPersenParsed = {
            value: 0
        };
        let diskonRpParsed = 0;

        if (diskonPersenInput !== '') {
            // Diskon persen
            let baseAmount = isIncl ? totalAmount / (1 + ppnRate / 100) : totalAmount;
            diskonPersenParsed = parseDiscountFormula(diskonPersenInput);
            diskonRpParsed = calculateDiscountHeader(baseAmount, diskonPersenInput);
        } else {
            // Diskon rupiah dari input user, jangan ubah input
            let diskonRp = parseFloat(diskonRpInput) || 0;
            diskonRpParsed = isIncl ? diskonRp / (1 + (ppnRate / 100)) : diskonRp;

            if (totalAmount > 0) {
                diskonPersenParsed.value = (diskonRpParsed / totalAmount) * 100;
            }
        }

        // 4. Update hidden & tampilan
        $('#hid_diskon_percen').val(diskonPersenParsed.value);
        $('#hid_diskon_input').val(diskonRpParsed);
        $('#hid_diskon_price').val(diskonRpParsed);
        $('#v_diskon').text(formatNumber(diskonRpParsed));

        // JANGAN UBAH INPUT USER - tapi format jika perlu
        if (diskonPersenInput === '' && diskonRpInput !== '') {
            let num = parseFloat(diskonRpInput) || 0;
            $('#cal_diskon_price').val(formatNumber(num));
        }

        // 5. Hitung PPN
        let ppnAmount = calculatePPN(totalAmount, diskonRpParsed, ppnRate, ppnCode, {
            PEMBULATAN_PPN: 0
        });
        $('#hid_ppn_amount').val(ppnAmount);
        $('#v_ppn_amount').text(formatNumber(ppnAmount));

        // 6. Grand Total
        let grandTotal = totalAmount - diskonRpParsed + ppnAmount;
        $('#hid_total_net').val(grandTotal);
        $('#v_total_net').text(formatNumber(grandTotal));

        // 7. Hidden field untuk submit
        $('input[name="TOTAL_AMOUNT"]').val(totalAmount);
        $('input[name="TOTAL_DISKON_INPUT_HIDDEN"]').val(diskonRpParsed);
        $('input[name="PPN_AMOUNT"]').val(ppnAmount);
        $('input[name="TOTAL_NET"]').val(grandTotal);
    }

    function calculateDiscountHeader(totalAmount, discountFormula) {
        if (!discountFormula || typeof discountFormula !== 'string') return 0;

        // Bersihkan input: hanya angka, titik, dan +
        let clean = discountFormula.replace(/[^\d.+]/g, '');
        if (!clean) return 0;

        // Pisahkan berdasarkan '+'
        let parts = clean.split('+').map(p => p.trim()).filter(p => p !== '');

        // Jika ada bagian yang dimulai dengan '0' dan panjang > 1 → anggap sebagai nominal rupiah (e.g., "05000")
        let hasRupiah = parts.some(p => /^0\d+$/.test(p));

        if (hasRupiah) {
            // Hitung total nominal rupiah (contoh: "05000+03000" → 500 + 3000 = 8000)
            let totalRp = parts.reduce((sum, p) => {
                let num = parseInt(p, 10) || 0;
                return sum + num;
            }, 0);
            return Math.min(totalRp, totalAmount); // Tidak boleh melebihi total
        } else {
            // Diskon persentase bertingkat (e.g., "10+5" → 10% lalu 5% dari sisa)
            let remaining = totalAmount;
            let totalDiscount = 0;

            for (let part of parts) {
                let pct = parseFloat(part) || 0;
                if (pct <= 0) continue;
                let disc = (remaining * pct) / 100;
                totalDiscount += disc;
                remaining -= disc;
            }
            return parseFloat(totalDiscount.toFixed(2));
        }
    }

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
        let $supplier = $('#supplier');
        let $storage = $('#storage');

        if (hasDetail) {
            $supplier.prop('disabled', true).trigger('change.select2');
            $storage.prop('disabled', true).trigger('change.select2');

            // Buat hidden input agar value tetap dikirim ke server
            if ($('#supplier-hidden').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'supplier-hidden',
                    name: $supplier.attr('name'),
                    value: $supplier.val()
                }).appendTo('form');
            } else {
                $('#supplier-hidden').val($supplier.val());
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
            $supplier.prop('disabled', false).trigger('change.select2');
            $storage.prop('disabled', false).trigger('change.select2');
            $('#supplier-hidden').remove();
            $('#storage-hidden').remove();
        }
        $supplier.trigger('change.select2');
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

    function loadLocation(supplier, selectedLocation = null) {
        $('#location').removeClass('readonly-select');

        $('#location')
            .empty()
            .prop('disabled', true)
            .trigger('change');
        if (!supplier) return;

        $.ajax({
            url: "<?= base_url('po_kny/get_location_by_supplier') ?>",
            type: "POST",
            data: {
                supplier: supplier
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
    $(document).on('click', '#btn-print', function() {
        setTimeout(function() {
            $('#loading').hide();
        }, 300);
    });
</script>