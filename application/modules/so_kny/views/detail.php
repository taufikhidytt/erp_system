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

    .table-sub tbody td {
        font-family: monospace;
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
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('so_kny') ?>" class="text-decoration-underline">SO KNY</a>
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
                        <form action="" method="post" id="myForm">
                            <div class="row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center gap-2 label-status">
                                    <h5 id="statusSoId" style="width: 100px;"></h5>
                                    <h5 style="width: 100px;" id="readonlySoId"></h5>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <?= button_actions([
                                        'insert',
                                        'save',
                                        ['key' => 'delete', 'data-id' => $this->encrypt->encode($data->SO_ID)],
                                        'reload',
                                        ['key' => 'print_out', 'redirect' => site_url('so_kny/print/' . base64url_encode($this->encrypt->encode($data->SO_ID)))]
                                    ]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <input type="hidden" name="so_id" id="so_id" value="<?= $this->encrypt->encode($data->SO_ID); ?>">
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
                                            <label for="customer">Customer:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-2-fill"></i>
                                                </span>
                                                <select name="customer" id="customer"
                                                    data-url="so_kny/get_customer"
                                                    data-selected-id="<?= set_value('customer', $data->PERSON_ID . "_" . $data->PERSON_SITE_ID) ?>"
                                                    class="form-control select2 <?= form_error('customer') ? 'is-invalid' : null; ?>">
                                                </select>
                                                <input type="hidden" name="person_site_id" id="person_site_id" value="<?= $this->input->post('person_site_id') ?? ($data->PERSON_SITE_ID ?? '') ?>">
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
                                                            data-selected-id="<?= set_value('payment_term', $data->PAYMENT_TERM_ID) ?>"
                                                            class="form-control select2 <?= form_error('payment_term') ? 'is-invalid' : null; ?>">
                                                        </select>
                                                    </div>
                                                    <div class="text-danger"><?= form_error('payment_term') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="po_customer">PO Customer:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-profile-fill"></i>
                                                        </span>
                                                        <input type="text" name="po_customer" id="po_customer" class="form-control <?= form_error('po_customer') ? 'is-invalid' : null; ?>" placeholder="Enter PO Customer" value="<?= $this->input->post('po_customer') ?? $data->PO_NO; ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('po_customer') ?></div>
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
                                            <label for="sales">Sales:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-2-fill"></i>
                                                </span>
                                                <select name="sales" id="sales"
                                                    data-url="api/get_sales"
                                                    data-selected-id="<?= set_value('sales', $data->KARYAWAN_ID) ?>"
                                                    class="form-control select2 <?= form_error('sales') ? 'is-invalid' : null; ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('sales') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="storage">Storage:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-building-fill"></i>
                                                </span>
                                                <select name="storage" id="storage"
                                                    data-url="so_kny/get_storage"
                                                    data-default="Y"
                                                    data-user_id="<?= $this->encrypt->encode($this->session->id); ?>"
                                                    data-selected-id="<?= set_value('storage', $data->WAREHOUSE_ID) ?>"
                                                    class="form-control select2 <?= form_error('storage') ? 'is-invalid' : null; ?>">
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('storage') ?></div>
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
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#detail" role="tab" aria-selected="true">
                                                <span class="d-block d-sm-none"><i class="ri ri-eye-2-fill"></i></span>
                                                <span class="d-none d-sm-block">Detail</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#info-detail" role="tab" aria-selected="true">
                                                <span class="d-block d-sm-none"><i class="ri ri-eye-2-fill"></i></span>
                                                <span class="d-none d-sm-block">Info</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content py-3 text-muted">
                                        <div class="tab-pane active" id="detail" role="tabpanel">
                                            <div class="mb-3">
                                                <button type="button" id="removeRow" class="btn btn-danger btn-sm" style="width: 55px;height:29.89px">
                                                    <i class="fa fa-trash"></i> Del
                                                </button>
                                                <button type="button" id="btn-modalMrq" class="btn btn-success btn-sm">
                                                    <i class="ri ri-add-box-fill"></i> Add
                                                </button>
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
                                                        so_detail.*,
                                                        so.DOCUMENT_NO,
                                                        a.DOCUMENT_NO no_mrq,
                                                        i.ITEM_CODE,
                                                        CASE
                                                            WHEN a.BASE_QTY IS NULL 
                                                            OR a.BASE_QTY = 0 THEN
                                                                a.ENTERED_QTY ELSE a.ENTERED_QTY - ( COALESCE ( a.RECEIVED_ENTERED_QTY, 0 ) / a.BASE_QTY ) 
                                                                END AS BALANCE
                                                        FROM so_detail
                                                        JOIN so ON so.SO_ID = so_detail.SO_ID 
                                                        JOIN build a ON a.BUILD_ID = so_detail.BUILD_ID
                                                        JOIN item i ON so_detail.ITEM_ID = i.ITEM_ID
                                                        WHERE so_detail.SO_ID = '{$data->SO_ID}'");

                                                        if ($dataDetail->num_rows() > 0) { ?>
                                                            <?php
                                                            $no = 1;
                                                            $postDetail = $this->input->post('detail');
                                                            $i = 0;
                                                            foreach ($dataDetail->result() as $dd): ?>
                                                                <tr class="tr-height-30">
                                                                    <td><?= $no++ ?></td>
                                                                    <td style="display: none;">
                                                                        <input type="hidden" name="detail[so_detail_id][]" id="so_detail_id" value="<?= $this->encrypt->encode($dd->SO_DETAIL_ID); ?>">
                                                                        <input type="hidden" name="detail[build_id][]" value="<?= $dd->BUILD_ID ?>">
                                                                        <input type="hidden" name="detail[build_detail_id][]" value="<?= $dd->BUILD_DETAIL_ID ?>">
                                                                        <input type="hidden" name="detail[item_id][]" value="<?= $dd->ITEM_ID ?>">
                                                                        <input type="hidden" name="detail[base_qty][]" value="<?= number_format(rtrim(rtrim($dd->BASE_QTY, '0'), '.'), 0, '.', ',') ?>">
                                                                        <input type="hidden" name="detail[berat][]" value="<?= number_format(rtrim(rtrim($dd->BERAT, '0'), '.'), 0, '.', ',') ?>">
                                                                        <input type="hidden" name="detail[balance][]" value="<?= number_format(rtrim(rtrim($dd->BALANCE, '0'), '.'), 0, '.', ',') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="checkbox" class="chkDetail">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class=" ellipsis align-middle" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->no_mrq ?>">
                                                                            <?= $dd->no_mrq; ?>
                                                                        </span>
                                                                        <input type="hidden" name="detail[no_mrq][]" value="<?= $dd->no_mrq ?>">
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
                                                                        <textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly><?= $dd->DESKRIPSI ?></textarea>
                                                                    </td>
                                                                    <td class="ellipsis text-end">
                                                                        <span class="view-mode qty-view ellipsis align-middle">
                                                                            <?= number_format(rtrim(rtrim($dd->ENTERED_QTY, '0'), '.'), 2, '.', ','); ?>
                                                                        </span>
                                                                        <input type="text" class="form-control form-control-sm qty auto-width edit-mode qty-edit d-none enter-as-tab text-end input-number" name="detail[jumlah][]" data-balance="<?= ($dd->BALANCE == 0) ? '0' : rtrim(rtrim((string)$dd->BALANCE, '0'), '.') ?>" data-so_detail_id="<?= $this->encrypt->encode($dd->SO_DETAIL_ID) ?>" data-value_old="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>" value="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class="ellipsis" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->ENTERED_UOM ?>">
                                                                            <?= $dd->ENTERED_UOM ?>
                                                                        </span>
                                                                        <input type="hidden" name="detail[satuan][]" value="<?= $dd->ENTERED_UOM ?>">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class="view-mode harga-view"><?= number_format(rtrim(rtrim($dd->HARGA_INPUT, '0'), '.'), 2, '.', ','); ?></span>
                                                                        <input type="text" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[harga_input][]" value="<?= $dd->HARGA_INPUT ?>">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class="harga-input-b"><?= number_format(rtrim(rtrim($dd->UNIT_PRICE, '0'), '.'), 2, '.', ','); ?></span>
                                                                        <input type="hidden" name="detail[harga][]" value="<?= rtrim(rtrim($dd->UNIT_PRICE, '0'), '.'); ?>">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class="view-mode harga-view diskon-harga-view"><?= number_format(rtrim(rtrim($dd->DISKON_INPUT, '0'), '.'), 2, '.', ','); ?></span>
                                                                        <input type="text" class="form-control form-control-sm diskon-harga edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[diskon_harga][]" value="<?= $dd->DISKON_INPUT ?>">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class="view-mode"><?= $dd->DISCOUNT_PERCEN ?></span>
                                                                        <input type="text" class="form-control form-control-sm edit-mode d-none enter-as-tab persen-detail" name="detail[diskon_persentase][]" value="<?= $dd->DISCOUNT_PERCEN ?>">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class="subtotal"><?= number_format(rtrim(rtrim($dd->SUBTOTAL, '0'), '.'), 2, '.', ','); ?></span>
                                                                        <input type="hidden" name="detail[subtotal][]" value="<?= rtrim(rtrim($dd->SUBTOTAL, '0'), '.'); ?>">
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
                                                                <td style="vertical-align:middle; font-weight:600;">Diskon</td>
                                                                <td>:</td>
                                                                <td class="text-left">
                                                                    <input id="cal_diskon_percen" name="TOTAL_DISCOUNT_PERCEN" class="form-control form-control-sm input-container persen-detail" placeholder="Persen" value="<?= $this->input->post('TOTAL_DISCOUNT_PERCEN') ?? $data->TOTAL_DISCOUNT_PERCEN ?>" data-mode="false" style="width: 130px;">
                                                                </td>
                                                                <td class="text-center">%</td>
                                                                <td class="text-center">=</td>
                                                                <?php $total_diskon = rtrim(rtrim((string)$data->TOTAL_DISKON_INPUT, '0'), '.'); ?>
                                                                <td class="text-right">
                                                                    <input type="text" id="cal_diskon_price" name="TOTAL_DISKON_INPUT" class="form-control form-control-sm input-container text-end input-number" placeholder="Rupiah" value="<?= $total_diskon; ?>">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="vertical-align:middle; font-weight:600;">PPN</td>
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
                                                                                <?= set_value('ppn_code_selected', ($data->PPN_CODE ?? '')) == $pc->PPN_CODE ? 'selected' : ($defaultValue == $pc->PPN_CODE ? 'selected' : '') ?>>
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
                                                                <td style="font-weight:600;">Total</td>
                                                                <td>:</td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text-right" style="text-align: right">
                                                                    <div id="v_total_amount" style="font-family: monospace;"></div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight:600;">Diskon</td>
                                                                <td>:</td>
                                                                <td class="text-left">
                                                                </td>
                                                                <td></td>
                                                                <td class="text-right" style="text-align: right">
                                                                    <div id="v_diskon" style="font-family: monospace;"></div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="font-weight:600;">PPN</td>
                                                                <td>:</td>
                                                                <td class="text-left">
                                                                </td>
                                                                <td></td>
                                                                <td class="text-right" style="text-align: right">
                                                                    <div id="v_ppn_amount" style="font-family: monospace;"></div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>
                                                                    <h6 style="font-weight:600;">GRAND TOTAL</h6>
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

                                        <div class="tab-pane" id="info-detail" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table w-100 table-sm" id="table-info" data-url=" <?= site_url('so_kny/get_info/' . base64url_encode($this->encrypt->encode($data->SO_ID))) ?>">
                                                    <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
                                                        <tr>
                                                            <th></th>
                                                            <th>No</th>
                                                            <th>Nama Item</th>
                                                            <th>Kode Item</th>
                                                            <th>Satuan</th>
                                                            <th>SO</th>
                                                            <th>DO</th>
                                                            <th>SISA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveMemo">Simpan</button>
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
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnSaveKeterangan">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div id="table-info-detail" class="d-none" data-url="<?= site_url('so_kny/get_info_detail/') ?>">
    <table class="table table-sm table-bordered w-100 table-sub">
        <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
            <tr class="align-middle">
                <th width="30">No</th>
                <th>No Transaksi</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Storage</th>
            </tr>
        </thead>
    </table>
</div>

<script>
    let tableDetail;
    let tableItem;
    let tableInfo;
    $(document).ready(function() {
        // Initialize input-number for static elements
        $('.input-number').inputNumber();

        setTimeout(function() {
            calculateGrandTotal();
        }, 100);

        let so_id = $('#so_id').val();
        $.ajax({
            url: '<?= base_url() ?>so_kny/getStatus',
            type: 'POST',
            dataType: 'json',
            data: {
                so_id: so_id
            },
            success: function(response) {
                $('#statusSoId').html(badgeStatus(response.data[0].DISPLAY_NAME, response.data[0].MENU_ICON));
                $('#readonlySoId').hide();

                if (response.data[0].ITEM_FLAG === 'N') {
                    $('#readonlySoId').show();
                    $('#readonlySoId').html('<span class="badge bg-secondary">READ ONLY</span>');

                    $('#myForm')
                        .find('input, select, textarea, #removeRow, #btn-modalItem, td input')
                        .prop('disabled', true);
                    $('#table-info_wrapper').find('input,select').prop('disabled', false);

                    $('#table-detail td').css('pointer-events', 'none');

                    $('#myForm button[type="submit"]').replaceWith(
                        `<span class="btn btn-success btn-sm" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan" disabled" style="pointer-events: none; opacity: 0.6; cursor: not-allowed;">
                            <i class="ri ri-save-3-fill"></i>
                        </span>`
                    );

                    $('#myForm .btn-delete').replaceWith(
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
                },
                {
                    targets: 2,
                    width: "2%",
                    className: "text-center",
                },
                {
                    targets: 3,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 4,
                    width: "20%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 5,
                    width: "15%",
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 6,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 7,
                    width: "10%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                },
                {
                    targets: 8,
                    width: "10%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 9,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                },
                {
                    targets: 10,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 11,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                },
                {
                    targets: 12,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                },
                {
                    targets: 13,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 14,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                },
            ],
        });

        tableItem = $('#table-item').DataTable({
            autoWidth: false,
            columnDefs: [{
                    targets: 0,
                    className: "text-center"
                },
                {
                    targets: 1,
                    className: "text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 2,
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 3,
                    className: "ellipsis text-center",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ? data.substring(0, limit) + '...' : data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 4,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ? data.substring(0, limit) + '...' : data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 5,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ? data.substring(0, limit) + '...' : data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 6,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ? data.substring(0, limit) + '...' : data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 7,
                    className: "ellipsis text-center",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 15;
                        let text = data.length > limit ? data.substring(0, limit) + '...' : data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 8,
                    className: "ellipsis text-end",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ? data.substring(0, limit) + '...' : data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 9,
                    className: "ellipsis text-end",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ? data.substring(0, limit) + '...' : data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    targets: 10,
                    className: "ellipsis",
                    render: function(data) {
                        if (!data) return '-';
                        let limit = 20;
                        let text = data.length > limit ? data.substring(0, limit) + '...' : data;
                        return `<span title="${data}">${text}</span>`;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    },
                },
            ],
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: false,
        });

        let oldDetail = <?= json_encode($detail ?? []) ?>;

        if (oldDetail && oldDetail.kode_item) {
            oldDetail.kode_item.forEach(function(kode, i) {
                let so_detail_id = oldDetail.so_detail_id[i] ?? '';

                if (so_detail_id !== '') {
                    return;
                }

                let nomor = tableDetail.rows().count() + 1;

                let build_id = oldDetail.build_id[i] ?? '';
                let build_detail_id = oldDetail.build_detail_id[i] ?? '';
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
                let no_mrq = oldDetail.no_mrq[i] ?? '';
                let nama_item = oldDetail.nama_item[i] ?? '';
                let jumlah = oldDetail.jumlah[i] ?? '';
                let satuan = oldDetail.satuan[i] ?? '';

                let rowNode = tableDetail.row.add([
                    nomor,
                    `<input type="hidden" name="detail[so_detail_id][]" value="">
                    <input type="hidden" name="detail[no_mrq][]" value="${no_mrq}">
                    <input type="hidden" name="detail[build_id][]" value="${build_id}">
                    <input type="hidden" name="detail[build_detail_id][]" value="${build_detail_id}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${$.inputNumber.format(base_qty)}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">`,
                    `<input type="checkbox" class="chkDetail">`,
                    `<span class="ellipsis" title="${no_mrq}">${ellipsis(no_mrq)}</span>`,
                    `<span class="ellipsis" title="${nama_item}">${ellipsis(nama_item)}</span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,
                    `<span class="ellipsis" title="${kode}">${ellipsis(kode)}</span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode}">`,
                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly>${memo}</textarea>`,
                    // qty
                    `<span class="view-mode qty-view">${$.inputNumber.format(jumlah)}</span>
                    <input type="text" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab text-end input-number" name="detail[jumlah][]" value="${$.inputNumber.format(jumlah)}" data-balance="${Number(balance)}">`,
                    // satuan
                    `<span class="ellipsis" title="${satuan}">${ellipsis(satuan)}</span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">`,
                    // harga input
                    `<span class="view-mode harga-view">${$.inputNumber.format(harga_input)}</span>
                    <input type="text" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[harga_input][]" value="${$.inputNumber.format(harga_input)}">`,
                    // harga
                    `<span class="harga-input-b">${$.inputNumber.format(harga)}</span>
                    <input type="hidden" name="detail[harga][]" value="${harga}">`,
                    // diskon rp
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
            setTimeout(refreshPPN, 200);
        }

        var flashsuccess = $('#flashSuccess').data('success');
        var flashwarning = $('#flashWarning').data('warning');
        var flasherror = $('#flashError').data('error');

        if (flashsuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: flashsuccess
            })
        }
        if (flashwarning) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: flashwarning
            })
        }
        if (flasherror) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: flasherror
            })
        }

        toggleStorageDisabled();

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
            setTimeout(function() {
                var defaultPaymentTerm = $("#payment_term").val();
                let initialCustomer = $('#customer option:selected').data('person_site_id');
                $('#person_site_id').val(initialCustomer);
                loadLocation(initialCustomer);

                var paymentTermId = $('#customer').find(':selected').data('payment_term_id');
                if (paymentTermId) {
                    var paymentTermName = $('#customer').find(':selected').data('payment_term_name');
                    var newOption = new Option(paymentTermName, paymentTermId, true, true);
                    $('#payment_term').append(newOption).trigger('change');
                } else {
                    $('#payment_term').val(defaultPaymentTerm).trigger('change');
                }

                var karyawan_id = $('#customer').find(':selected').data('karyawan_id');
                var first_name = $('#customer').find(':selected').data('first_name');
                var last_name = $('#customer').find(':selected').data('last_name');
                if (karyawan_id) {
                    var newOption = new Option(first_name + ' - [' + last_name + ']', karyawan_id, true, true);
                    $('#sales').append(newOption).trigger('change');
                } else {
                    $('#sales').val('').trigger('change');
                }
            }, 100)
        });

        $('#tanggal').on('change', function() {
            setTimeout(function() {
                updateJatuhTempo();
            }, 100);
        });
        $('#payment_term').on('change.select2', updateJatuhTempo);
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

        $("#btn-modalMrq").on("click", function() {
            resetModalItem();
            $("#checkAll").prop('checked', false);
            $('#loading').show();
            var storage = $('#storage').val();
            let customerVal = $("#customer").val() || '';
            let customer = customerVal.includes('_') ? customerVal.split('_')[0] : customerVal;

            if (!storage) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Storage tidak terisi, Mohon isi terlebih dahulu'
                });
                return;
            }

            if (!customer) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Customer tidak terisi, Mohon isi terlebih dahulu'
                });
                return;
            }

            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>so_kny/getMrq",
                data: {
                    storage: storage,
                    customer: customer
                },
                dataType: "json",
                success: function(response) {
                    $('#loading').hide();
                    tableItem.clear().draw();

                    let existingBuildDetailId = new Set();
                    tableDetail.rows().every(function() {
                        let node = this.node();
                        let buildId = $(node).find('input[name="detail[build_id][]"]').val();
                        let buildDetailId = $(node).find('input[name="detail[build_detail_id][]"]').val();
                        if (buildDetailId && buildId) {
                            existingBuildDetailId.add(`${buildDetailId}-${buildId}`);
                        }
                    });

                    if (response.status === 'success' && Array.isArray(response.data)) {
                        response.data.forEach(function(item, i) {
                            if (existingBuildDetailId.has(`${item.BUILD_DETAIL_ID}-${item.BUILD_ID}`)) return;

                            var checkbox = `
                            <input type="checkbox" class="chkRow"
                                data-build_id="${item.BUILD_ID}"
                                data-build_detail_id="${item.BUILD_DETAIL_ID}"
                                data-item_id="${item.ITEM_ID}"
                                data-base_qty="${item.BASE_QTY}"
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

        $('#modalMrq').on('shown.bs.modal', function() {
            $(this).find('.dataTables_filter input').focus();
        });
        $("#checkAllParent").change(function() {
            $(".chkDetail").prop('checked', $(this).prop('checked'));
        });
        $("#checkAll").change(function() {
            $(".chkRow").prop('checked', $(this).prop('checked'));
        });

        $("#btnSubmit").on("click", function(e) {
            e.preventDefault();
            let rowsAdded = false;

            let existingBuildDetailId = new Set();
            tableDetail.rows().every(function() {
                let node = this.node();
                let buildDetailId = $(node).find('input[name="detail[build_detail_id][]"]').val();
                if (buildDetailId) existingBuildDetailId.add(buildDetailId);
            });

            let allRows = tableItem.rows().nodes();
            $(allRows).find('.chkRow:checked:not(:disabled)').each(function() {
                let build_id = $(this).data("build_id");
                let build_detail_id = $(this).data("build_detail_id");
                let item_id = $(this).data("item_id");
                let base_qty = $(this).data("base_qty");
                let keterangan = $(this).data("note") ?? '';
                let berat = $(this).data("berat");
                let balance = $(this).data("sisa");
                let no_transaksi = $(this).data("no_transaksi");
                let nama_item = $(this).data("nama_item");
                let kode_item = $(this).data("kode_item");
                let satuan = $(this).data("satuan");

                let exists = tableDetail.column(2).data().toArray().includes(build_detail_id);
                if (exists) {
                    $(this).prop('checked', false).prop('disabled', true);
                    return;
                }

                let rowNode = tableDetail.row.add([
                    "",
                    `<input type="hidden" name="detail[so_detail_id][]" value="">
                    <input type="hidden" name="detail[no_mrq][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[build_id][]" value="${build_id}">
                    <input type="hidden" name="detail[build_detail_id][]" value="${build_detail_id}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${$.inputNumber.format(base_qty)}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">`,
                    `<input type="checkbox" class="chkDetail">`,
                    `<span class="ellipsis" title="${no_transaksi}">${ellipsis(no_transaksi)}</span>`,
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
                    `<span class="view-mode harga-view">0.00</span>
                    <input type="text" class="form-control form-control-sm harga-input edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[harga_input][]" value="0">`,
                    // harga
                    `<span class="harga-input-b">0.00</span>
                    <input type="hidden" name="detail[harga][]" value="0">`,
                    // diskonrp
                    `<span class="view-mode harga-view diskon-harga-view">0.00</span>
                    <input type="text" class="form-control form-control-sm diskon-harga edit-mode harga-edit d-none enter-as-tab text-end input-number" name="detail[diskon_harga][]" value="0">`,
                    // diskon percen
                    `<span class="view-mode"></span>
                    <input type="text" class="form-control form-control-sm edit-mode d-none enter-as-tab persen-detail" name="detail[diskon_persentase][]" value="">`,
                    // subtotal
                    `<span class="subtotal">0.00</span>
                    <input type="hidden" name="detail[subtotal][]" value="0">`,
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
            tableDetail.column(0).nodes().each(function(cell, i) {
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

        $(document).on('click', '#myForm .btn-delete', function() {
            let id = $(this).data('id');
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
                        url: '<?= base_url() ?>so_kny/del',
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

        $(document).on('input change', '.qty-edit, .harga-input', function() {
            let val = $.inputNumber.unformat($(this).val());
            if (val === '' || isNaN(val)) return;
            if (val < 1) {
                $(this).val($.inputNumber.format(1));
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

        tableInfo = $('#table-info').DataTable({
            "autoWidth": true,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "info": true,
            "order": [],
            "ajax": {
                "url": $('#table-info').data('url'),
                "type": "POST"
            },
            "createdRow": function(row, data, dataIndex) {
                $(row).attr('data-so_detail_id', data.so_detail_id);
            },
            "columns": [{
                    "className": 'details-control text-center',
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "defaultContent": '<i class="ri ri-add-line" style="cursor:pointer"></i>',
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "className": 'text-center',
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "nama_item",
                    render: function(data, type, row) {
                        return data;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "kode_item",
                    className: "text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "satuan",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "so",
                    "className": 'text-end',
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "do",
                    "className": 'text-end',
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "sisa",
                    "className": 'text-end',
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
            ]
        });

        $('#table-info tbody').on('click', 'td.details-control', function() {
            const tr = $(this).closest('tr');
            const row = tableInfo.row(tr);
            const infoDetailID = tr.data('so_detail_id');
            let icon = $(this).find('i');

            if (row.child.isShown()) {
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');
            } else {
                const childTableId = 'child-' + infoDetailID;
                const childHtml = $($('#table-info-detail').html());
                childHtml.attr('id', childTableId);

                row.child(childHtml).show();
                icon.removeClass('ri-add-line').addClass('ri-subtract-line');

                $('#' + $.escapeSelector(childTableId)).DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": $('#table-info-detail').data('url') + infoDetailID,
                        "type": "POST",
                    },
                    "columns": [{
                            "data": "no",
                            "orderable": false,
                            "className": 'text-center',
                        },
                        {
                            "data": "no_transaksi",
                        },
                        {
                            "data": "tanggal",
                            "className": 'text-center',
                        },
                        {
                            "data": "jumlah",
                            'className': 'text-end',
                        },
                        {
                            "data": "satuan",
                        },
                        {
                            "data": "s_loc",
                        },
                    ],
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": true,
                    "order": []
                });
            }
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
        const so_detail_id = input.dataset.so_detail_id;
        const value_old = parseFloat(input.dataset.value_old) || 0;
        const balance = parseFloat(input.dataset.balance);
        let value = parseFloat($.inputNumber.unformat(input.value)) || 0;
        const row = $(input).closest("tr");

        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) {
                span.textContent = $.inputNumber.format(val);
            }
        }

        if (so_detail_id) {
            const maxAllowed = balance + value_old;
            if (value > maxAllowed) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah melebihi balance',
                    text: 'Jumlah tidak boleh melebihi balance (' + $.inputNumber.format(maxAllowed) + ')',
                    confirmButtonText: 'OK'
                }).then(() => {
                    input.value = $.inputNumber.format(value_old);
                    input.focus();
                    hitungSubTotal(row, ppn_code, ppn);
                    updateSpan(value_old);
                });
                return;
            }
        } else {
            if (value > balance) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah melebihi balance',
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
        }
    });

    document.addEventListener('blur', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;

        let ppn = $('#cal_ppn_code').val();
        let ppn_code = $('#cal_ppn_code option:selected').text().trim();

        const input = e.target;
        const so_detail_id = input.dataset.so_detail_id;
        const value_old = parseFloat(input.dataset.value_old) || 0;
        const row = $(input).closest("tr");
        const balance = parseFloat(input.dataset.balance);
        let value = parseFloat($.inputNumber.unformat(input.value)) || 0;

        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) {
                span.textContent = $.inputNumber.format(val);
            }
        }

        if (so_detail_id) {
            if (value <= 0 || isNaN(value)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah tidak valid',
                    text: 'Jumlah harus lebih dari 0',
                    confirmButtonText: 'OK'
                }).then(() => {
                    input.value = $.inputNumber.format(value_old);
                    input.focus();
                    hitungSubTotal(row, ppn_code, ppn);
                    updateSpan(value_old);
                });
                return;
            }
            hitungSubTotal(row, ppn_code, ppn);
            updateSpan(value);
        } else {
            if (value <= 0 || isNaN(value)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah tidak valid',
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
            hitungSubTotal(row, ppn_code, ppn);
            updateSpan(value);
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

    let diskonPriceTimer = null;

    $('#cal_diskon_price').on('input change', function() {
        let val = parseFloat($.inputNumber.unformat($(this).val())) || 0;
        let totalAmount = parseFloat($('#hid_total_amount').val()) || 0;
        let persenInput = $('#cal_diskon_percen').val().trim();

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
            $('#table-detail tbody tr').each(function() {
                hitungSubTotal($(this), ppn_code, ppn);
            });
            calculateGrandTotal();
        }
    }

    function updatePpnHidden() {
        let ppn = $('#cal_ppn_code').val() || '0';
        let ppn_code = $('#cal_ppn_code option:selected').text().trim() || '';

        $('#hid_ppn').val(ppn);
        $('#hid_ppn_code').val(ppn_code);

        $('#table-detail tbody tr').each(function() {
            hitungSubTotal($(this), ppn_code, parseFloat(ppn));
        });
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

        if (diskon_persentase && diskon_persentase !== '' && diskon_persentase !== '0') {
            rawDiskon = calcDiscount(harga_input, diskon_persentase);
            row.find('input[name="detail[diskon_harga][]"]').val($.inputNumber.format(rawDiskon));
        }

        let rawSubtotal = (harga_input - rawDiskon) * qty;
        if (rawSubtotal < 0) rawSubtotal = 0;

        let displayHarga = harga_input;
        let displayDiskon = rawDiskon;
        let displaySubtotal = rawSubtotal;

        if (isPpnIncl && ppnRate > 0) {
            displayHarga = harga_input / (1 + ppnRate / 100);
            displayDiskon = rawDiskon / (1 + ppnRate / 100);
            displaySubtotal = rawSubtotal / (1 + ppnRate / 100);
        }

        row.find(".harga-input-b").text($.inputNumber.format(displayHarga));
        row.find('.diskon-harga-view').text($.inputNumber.format(displayDiskon));
        row.find(".subtotal").text($.inputNumber.format(displaySubtotal));

        row.find('input[name="detail[harga][]"]').val(harga_input);
        row.find('input[name="detail[subtotal][]"]').val(rawSubtotal);

        calculateGrandTotal();
    }

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

        let hasRupiah = parts.some(p => /^0\d+$/.test(p));
        if (hasRupiah) {
            let totalRp = parts.reduce((sum, p) => sum + (parseInt(p, 10) || 0), 0);
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
        let diskonPersenParsed = {
            value: 0
        };
        let diskonRpParsed = 0;

        if (diskonPersenInput !== '') {
            let baseAmount = totalAmount;
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

        if (!$('#cal_diskon_price').is(':focus')) {
            $('#cal_diskon_price').val($.inputNumber.format(diskonRpParsed));
        }

        let displayTotalAmount = totalAmount;
        let displayDiskon = diskonRpParsed;
        let displayPpnAmount = 0;
        let displayTotalNet = 0;

        let hiddenPpnAmount = 0;
        let hiddenTotalNet = 0;

        if (isIncl) {
            displayTotalAmount = totalAmount / (1 + ppnRate / 100);
            displayDiskon = diskonRpParsed / (1 + ppnRate / 100);

            let totalNetRaw = totalAmount - diskonRpParsed;
            let dpp = totalNetRaw / (1 + ppnRate / 100);
            displayPpnAmount = totalNetRaw - dpp;
            displayTotalNet = totalNetRaw;

            hiddenPpnAmount = displayPpnAmount;
            hiddenTotalNet = displayTotalNet;
        } else {
            displayPpnAmount = calculatePPN(totalAmount, diskonRpParsed, ppnRate, ppnCode, {
                PEMBULATAN_PPN: 0
            });
            displayTotalNet = totalAmount - diskonRpParsed + displayPpnAmount;

            hiddenPpnAmount = displayPpnAmount;
            hiddenTotalNet = displayTotalNet;
        }

        $('#v_total_amount').text($.inputNumber.format(displayTotalAmount));
        $('#v_diskon').text($.inputNumber.format(displayDiskon));
        $('#v_ppn_amount').text($.inputNumber.format(displayPpnAmount));
        $('#v_total_net').text($.inputNumber.format(displayTotalNet));

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
        let $customer = $("#customer");
        let $storage = $("#storage");

        if (hasDetail) {
            $customer.prop("disabled", true).trigger("change.select2");
            $storage.prop("disabled", true).trigger("change.select2");
        } else {
            $customer.prop("disabled", false).trigger("change.select2");
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

    function loadLocation(customer, selectedLocation = null) {
        $('#location').removeClass('readonly-select');
        $('#location').empty().prop('disabled', true).trigger('change');
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
        let tanggal = $('#tanggal').val();
        if (!tanggal) return;
        let days = parseInt($('#payment_term option:selected').data('number')) || 0;
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

    $(document).on('click', '#btn-print', function() {
        setTimeout(function() {
            $('#loading').hide();
        }, 300);
    });

    $('form').on('submit', function(e) {
        $('#customer').prop('disabled', false);
        $('#storage').prop('disabled', false);
    });
</script>
<script src="<?= base_url() ?>assets/admin/js/pages/fpk.js?v=<?= $version['inline-editor'] ?>"></script>