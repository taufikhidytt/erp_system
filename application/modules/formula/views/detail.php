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

    .auto-width {
        width: 5ch;
        /* default awal */
        min-width: 70px;
        max-width: 590px;
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

    .keterangan-view {
        white-space: pre-line;
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
                                <a href="<?= base_url('formula') ?>" class="text-decoration-underline">Formula</a>
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
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <span class="border border-1 border-dark p-2" id="statusFromulaId"></span>
                                    <span class="border border-1 border-warning p-2" id="readonlyFormulaId"></span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <a href="<?= base_url('formula/add') ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Tambah">
                                        <i class="ri ri-add-box-fill"></i>
                                    </a>
                                    <button type="submit" class="btn btn-success btn-sm" name="submit" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan">
                                        <i class="ri ri-save-3-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" name="del-submit" id="del-submit" data-toggle="tooltip" data-placement="bottom" title="hapus" data-id_del="<?= $this->encrypt->encode($data->BOM_ID); ?>">
                                        <i class="ri ri-delete-bin-5-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.replace(window.location.pathname);" data-toggle="tooltip" data-placement="bottom" title="Reload">
                                        <i class="ri ri-reply-fill"></i>
                                    </button>
                                    <a href="<?= site_url('formula/print/' . base64url_encode($this->encrypt->encode($data->BOM_ID))) ?>" id="btn-print" target="_blank" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Print">
                                        <i class="ri ri-printer-fill"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <input type="hidden" name="bom_id" id="bom_id" value="<?= $this->encrypt->encode($data->BOM_ID); ?>">
                                            <label for="no_transaksi">No Transaksi:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="no_transaksi" id="no_transaksi" class="form-control <?= form_error('no_transaksi') ? 'is-invalid' : null; ?>" placeholder="Auto Generate" value="<?= $this->input->post('no_transaksi') ?? $data->DOCUMENT_NO; ?>" disabled readonly>
                                            </div>
                                            <div class="text-danger"><?= form_error('no_transaksi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="item_finish_goods">Item Finish Goods:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-stack-fill"></i>
                                                </span>
                                                <select name="item_finish_goods" id="item_finish_goods" class="form-control select2 <?= form_error('item_finish_goods') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Item Finish Goods --</option>
                                                    <?php $param = $this->input->post('item_finish_goods') ?? $data->ITEM_ID; ?>
                                                    <?php foreach ($item_finish_goods->result() as $ifg): ?>
                                                        <option
                                                            value="<?= $ifg->ITEM_ID ?>"
                                                            data-description="<?= htmlspecialchars($ifg->ITEM_DESCRIPTION) ?>"
                                                            <?= $ifg->ITEM_ID == $param ? 'selected' : null ?>>
                                                            <?= strtoupper($ifg->ITEM_DESCRIPTION . " ~ " . $ifg->ITEM_CODE) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <input type="hidden" name="item_description" id="item_description" value="">
                                            </div>
                                            <div class="text-danger"><?= form_error('item_finish_goods') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="satuan" id="label_satuan">Satuan:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-paint-fill"></i>
                                                </span>
                                                <select name="satuan" id="satuan" class="form-control select2 <?= form_error('satuan') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Satuan --</option>
                                                </select>
                                                <input type="hidden" name="base_qty" id="base_qty">
                                            </div>
                                            <div class="text-danger"><?= form_error('satuan') ?></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="unit">Unit:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-price-tag-3-fill"></i>
                                                        </span>
                                                        <input type="text" name="unit" id="unit" class="form-control <?= form_error('unit') ? 'is-invalid' : null; ?>" placeholder="Enter Unit" value="<?= $this->input->post('unit') ?? $data->UNIT; ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('unit') ?></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="code">Code:</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-barcode-line"></i>
                                                        </span>
                                                        <input type="text" name="code" id="code" class="form-control <?= form_error('code') ? 'is-invalid' : null; ?>" placeholder="Enter Code" value="<?= $this->input->post('code') ?? $data->LOKASI; ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('code') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan">Keterangan:</label>
                                            <div class="input-group">
                                                <textarea name="keterangan" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"><?= $this->input->post('keterangan') ?? $data->NOTE; ?></textarea>
                                            </div>
                                            <div class="text-danger"><?= form_error('keterangan') ?></div>
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
                                                <input type="datetime-local" name="tanggal" id="tanggal" class="form-control <?= form_error('tanggal') ? 'is-invalid' : null; ?>" placeholder="Enter Tanggal" value="<?= $this->input->post('tanggal') ?? $data->DOCUMENT_DATE; ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('tanggal') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="start_date">Start Date:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-calendar-2-fill"></i>
                                                </span>
                                                <input type="datetime-local" name="start_date" id="start_date" class="form-control <?= form_error('start_date') ? 'is-invalid' : null; ?>" placeholder="Enter Start Date" value="<?= $this->input->post('start_date') ?? $data->START_DATE; ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('start_date') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_date">End Date:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-calendar-2-fill"></i>
                                                </span>
                                                <input type="datetime-local" name="end_date" id="end_date" class="form-control <?= form_error('end_date') ? 'is-invalid' : null; ?>" placeholder="Enter END Date" value="<?= $this->input->post('end_date') ?? $data->END_DATE; ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('start_date') ?></div>
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
                                            <label for="status">Status:</label>
                                            <div class="input-group">
                                                <div class="form-check form-switch mb-3" dir="ltr">
                                                    <input type="checkbox" name="status" class="form-check-input" id="customSwitch1" <?= $data->ACTIVE_FLAG == 'Y' ? 'checked' : null ?>>
                                                    <label class="form-check-label" for="customSwitch1"></label>
                                                </div>
                                            </div>
                                            <div class="text-danger"><?= form_error('status') ?></div>
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
                                        <!-- <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#info-detail" role="tab" aria-selected="true">
                                                <span class="d-block d-sm-none"><i class="ri ri-eye-2-fill"></i></span>
                                                <span class="d-none d-sm-block">Info</span>
                                            </a>
                                        </li> -->
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

                                            <div class="table-responsive overflow-auto" style="max-height: 450px;">
                                                <table class="table table-striped table-bordered" id="table-detail">
                                                    <thead style="position: sticky; top: 0; background: #3d7bb9; z-index: 10; color: #ffff">
                                                        <tr style="text-align: center !important;">
                                                            <th>No</th>
                                                            <th style="padding:0; margin:0; border:none; display: none;"></th>
                                                            <th>
                                                                <input type="checkbox" name="checkAllParent" id="checkAllParent" class="">
                                                            </th>
                                                            <th>Nama Item</th>
                                                            <th>Kode Item</th>
                                                            <th>Jumlah</th>
                                                            <th>Satuan</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $dataDetail = $this->db->query("SELECT bom_detail.*, item.ITEM_CODE FROM bom_detail JOIN item ON item.ITEM_ID = bom_detail.ITEM_ID WHERE bom_detail.BOM_ID = '{$data->BOM_ID}'");

                                                        if ($dataDetail->num_rows() > 0) { ?>
                                                            <?php
                                                            $no = 1;
                                                            $postDetail = $this->input->post('detail');
                                                            $i = 0;
                                                            foreach ($dataDetail->result() as $dd): ?>
                                                                <tr class="tr-height-30">
                                                                    <td><?= $no++ ?></td>
                                                                    <td style="display: none;">
                                                                        <input type="hidden" name="detail[bom_detail_id][]" id="bom_detail_id" value="<?= $this->encrypt->encode($dd->BOM_DETAIL_ID); ?>">
                                                                        <input type="hidden" name="detail[item_id][]" value="<?= $dd->ITEM_ID ?>">
                                                                        <input type="hidden" name="detail[berat][]" value="<?= number_format(rtrim(rtrim($dd->BERAT, '0'), '.'), 0, '.', ',') ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="checkbox" class="chkDetail">
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
                                                                    <td class="ellipsis text-end">
                                                                        <span class="view-mode qty-view ellipsis align-middle">
                                                                            <?= number_format(rtrim(rtrim($dd->ENTERED_QTY, '0'), '.'), 2, '.', ','); ?>
                                                                        </span>
                                                                        <input type="number" class="form-control form-control-sm qty auto-width edit-mode qty-edit d-none jumlah" min="0" step="any" name="detail[jumlah][]" value="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>">
                                                                    </td>
                                                                    <td class="ellipsis" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->ENTERED_UOM ?>">
                                                                        <?php $data_uom_selected = $this->db->query("SELECT
                                                                            *
                                                                        FROM (
                                                                            -- Unit dasar (default)
                                                                            SELECT
                                                                                0 AS URUT,
                                                                                i.ITEM_ID,
                                                                                i.UOM_CODE,
                                                                                1 AS TO_QTY,
                                                                                CONCAT('1.00 ', i.UOM_CODE) AS KONVERSI
                                                                            FROM item i
                                                                            WHERE i.ITEM_ID = {$dd->ITEM_ID}
                                                                            UNION ALL
                                                                            -- Unit alternatif (konversi)
                                                                            SELECT
                                                                                1 AS URUT,
                                                                                iu.ITEM_ID,
                                                                                iu.UOM_CODE,
                                                                                COALESCE(iu.TO_QTY, 1) AS TO_QTY,
                                                                                CONCAT(
                                                                                    ROUND(COALESCE(iu.TO_QTY, 1) * 1, 2),
                                                                                    ' ',
                                                                                    i.UOM_CODE
                                                                                ) AS KONVERSI
                                                                            FROM item_uom iu
                                                                            INNER JOIN item i 
                                                                                ON iu.ITEM_ID = i.ITEM_ID
                                                                            WHERE iu.ITEM_ID = {$dd->ITEM_ID}
                                                                        ) AS unit_data
                                                                        ORDER BY URUT, TO_QTY"); ?>

                                                                        <select class="form-control form-control-sm uom-select border-0" name="detail[satuan][]">
                                                                            <?php
                                                                            $param = $this->input->post('detail[uom][]') ?? $dd->ENTERED_UOM;
                                                                            foreach ($data_uom_selected->result() as $dus): ?>
                                                                                <option value="<?= $dus->UOM_CODE ?>" data-code="<?= $dus->UOM_CODE ?>" data-to_qty="<?= $dus->TO_QTY ?>" data-label="<?= $dus->UOM_CODE ?> (<?= $dus->TO_QTY ?>)" <?= $param == $dus->UOM_CODE ? 'selected' : NULL ?>>
                                                                                    <?= $dus->UOM_CODE . " (" . $dus->TO_QTY . ")" ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                        <input type="hidden" class="form-control form-control-sm to-qty" name="detail[base_qty][]" value="<?= $dd->BASE_QTY ?>">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly data-toggle="tooltip" data-placement="bottom" title="<?= $dd->NOTE; ?>"><?= $postDetail['keterangan'][$i] ?? $dd->NOTE; ?></textarea>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- <div class="tab-pane" id="info-detail" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-striped w-100" id="table-info" data-url=" <?= site_url('mrq/get_info/' . base64url_encode($this->encrypt->encode($data->BUILD_ID))) ?>">
                                                    <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
                                                        <tr>
                                                            <th></th>
                                                            <th>No</th>
                                                            <th>Nama Item</th>
                                                            <th>Kode Item</th>
                                                            <th>Satuan</th>
                                                            <th>MR</th>
                                                            <th>PO</th>
                                                            <th>SISA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div> -->
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
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Assy Kode</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Jumlah</th>
                                <th>Brand</th>
                                <th>Tipe</th>
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

<!-- <div id="table-info-detail" class="d-none" data-url="<?= site_url('mrq/get_info_detail/') ?>">
    <table class="table table-sm table-bordered w-100">
        <thead>
            <tr class="align-middle">
                <th width="30">No</th>
                <th>No Transaksi</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>S.Loc</th>
            </tr>
        </thead>
    </table>
</div> -->

<script>
    let tableDetail;
    let tableItem;
    let tableInfo;
    let isReadOnly;
    $(document).ready(function() {
        let bom_id = $('#bom_id').val();
        $.ajax({
            url: '<?= base_url() ?>formula/getStatus',
            type: 'POST',
            dataType: 'json',
            data: {
                bom_id: bom_id,
            },
            success: function(response) {
                $('#statusFromulaId').text(response.data[0].DISPLAY_NAME);
                $('#readonlyFormulaId').hide();

                if (response.data[0].ITEM_FLAG === 'N') {

                    let isReadOnly = true;

                    $('#readonlyFormulaId').show();
                    $('#readonlyFormulaId').text('READ ONLY');
                    $('#myForm')
                        .find('input, select, textarea, #removeRow, #btn-modalItem, td input, .select2')
                        .prop('disabled', true);
                    $('#table-info_wrapper').find('input,select').prop('disabled', false);


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
                        `<span type="button" id="removeRow" class="btn btn-danger btn-sm" disabled style="width: 55px; pointer-events: none; opacity: 0.6; cursor: not-allowed;">
                            <i class="fa fa-trash"></i> Del
                        </span>`
                    );

                    $('#btn-modalMrq').replaceWith(
                        `<span type="button" id="btn-modalMrq" class="btn btn-success btn-sm" disabled style="pointer-events: none; opacity: 0.6; cursor: not-allowed;">
                            <i class="ri ri-add-box-fill"></i> Add
                        </span>`
                    );

                    let initialItem = $('#item_finish_goods').val();
                    let oldSatuan = "<?= set_value('satuan') ?>";

                    console.log(isReadOnly);

                    if (initialItem) {
                        loadSatuan(initialItem, oldSatuan, isReadOnly);
                    }
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
                    className: "text-right",
                }, // checkbox
                {
                    targets: 3,
                    width: "10%",
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
                    width: "10%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // jumlah
                {
                    targets: 6,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // satuan
                {
                    targets: 7,
                    width: "20%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // keterangan
            ],
        });

        toggleItemDisabled();

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
                }, // kode item
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
                }, // nama item
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
                }, // assy code
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
                }, // kategori
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
                }, // satuan
                {
                    targets: 7,
                    className: "ellipsis text-end",
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
                }, // jumlah
                {
                    targets: 8,
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
                }, // brand
                {
                    targets: 9,
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
                }, // tipe
            ],
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: false,
        });

        let oldDetail = <?= json_encode($detail ?? []) ?>;

        if (oldDetail && oldDetail.kode_item) {
            oldDetail.kode_item.forEach(function(kode, i) {

                let bom_detail_id = oldDetail.bom_detail_id[i] ?? '';

                if (bom_detail_id !== '') {
                    return;
                }

                let nomor = tableDetail.rows().count() + 1;

                let item_id = oldDetail.item_id[i];
                let nama_item = oldDetail.nama_item[i];
                let satuan = oldDetail.satuan[i];
                let jumlah = oldDetail.jumlah[i] ?? '';
                let berat = oldDetail.berat[i] ?? '';
                let keterangan = oldDetail.keterangan[i] ?? '';

                let rowNode = tableDetail.row.add([
                    nomor,

                    `
                    <input type="hidden" name="detail[bom_detail_id][]" value="">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">`,

                    `<input type="checkbox" class="chkDetail">`,


                    `<span class="ellipsis" title="${nama_item}">
                    ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,

                    `<span class="ellipsis" title="${kode}">
                        ${ellipsis(kode)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode}">`,

                    `<span class="view-mode qty-view">${formatNumber(jumlah)}</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode jumlah qty-edit d-none enter-as-tab" name="detail[jumlah][]" value="${Math.floor(Number(jumlah))}" min="0" step="any">`,

                    `<select class="form-control form-control-sm uom-select border-0 ellipsis" name="detail[satuan][]" title="${satuan}">
                        <option value="">Loading...</option>
                    </select>
                    <input type="hidden" class="form-control form-control-sm" name="detail[base_qty][]">`,

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly>${keterangan}</textarea>`,
                ]).node();
                $(rowNode).addClass('tr-height-30');
                loadUom($(rowNode), item_id, satuan);
            });
            toggleItemDisabled();
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

        $('#satuan').prop('disabled', true);

        let initialItem = $('#item_finish_goods').val();
        let oldSatuan = "<?= set_value('satuan') ?>";

        if (initialItem) {
            loadSatuan(initialItem, oldSatuan);
        }

        $('#item_finish_goods').on('change', function() {
            let description = $(this).find(':selected').data('description');
            $('#item_description').val(description);

            let itemId = $(this).val();
            var label = $('#label_satuan');

            // hapus dulu *
            label.find('span.text-danger').remove();

            // jika ada value, tambahkan *
            if (itemId) {
                label.append(' <span class="text-danger">*</span>');
            }
            loadSatuan(itemId);
        });

        // modal
        $("#btn-modalMrq").on("click", function() {
            resetmodalMrq();

            $("#checkAll").prop('checked', false);
            $('#loading').show();
            var item_finish_goods = $('#item_finish_goods').val();

            if (!item_finish_goods) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Item finish goods to tidak terisi, Mohon isi terlebih dahulu',
                });
                return;
            }

            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>formula/getItem",
                data: {
                    item_finish_goods: item_finish_goods,
                },
                dataType: "json",
                success: function(response) {
                    $('#loading').hide();
                    tableItem.clear().draw();

                    let existingItemId = new Set();
                    tableDetail.rows().every(function() {
                        let node = this.node();
                        let itemId = $(node).find('input[name="detail[item_id][]"]').val();
                        if (itemId) existingItemId.add(itemId);
                    });

                    if (response.status === 'success' && Array.isArray(response.data)) {
                        let no = 1;
                        response.data.forEach(function(item) {

                            if (existingItemId.has(item.ITEM_ID)) {
                                return;
                            }

                            var checkbox = `
                            <input type="checkbox" class="chkRow"
                                data-item_id="${item.ITEM_ID}"
                                data-kode_item="${item.ITEM_CODE}"
                                data-nama_item="${item.ITEM_DESCRIPTION.replace(/"/g, '&quot;')}"
                                data-assy_code="${item.ASSY_CODE}"
                                data-kategori="${item.CATEGORY}"
                                data-satuan="${item.ENTERED_UOM}"
                                data-jumlah="${item.STOK}"
                                data-brand="${item.BRAND}"
                                data-tipe="${item.TIPE}"
                                data-jenis_id="${item.JENIS_ID}"
                                data-berat="${item.BERAT}"
                            >
                            `;

                            tableItem.row.add([
                                checkbox,
                                no++,
                                item.ITEM_CODE,
                                item.ITEM_DESCRIPTION,
                                item.ASSY_CODE,
                                item.CATEGORY,
                                item.UOM,
                                parseFloat(item.STOK).toFixed(2),
                                item.BRAND,
                                item.TIPE,
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

            let existingItemId = new Set();
            tableDetail.rows().every(function() {
                let node = this.node();
                let itemId = $(node).find('input[name="detail[item_id][]"]').val();

                if (itemId) existingItemId.add(itemId);
            });

            let allRows = tableItem.rows().nodes();

            let nodesToDraw = [];

            $(allRows).find('.chkRow:checked:not(:disabled)').each(function() {
                let item_id = $(this).data("item_id");
                let kode_item = $(this).data("kode_item");
                let nama_item = $(this).data("nama_item");
                let assy_code = $(this).data("assy_code");
                let kategori = $(this).data("kategori");
                let satuan = $(this).data("satuan");
                let jumlah = $(this).data("jumlah");
                let brand = $(this).data("brand");
                let tipe = $(this).data("tipe");
                let jenis_id = $(this).data("jenis_id");
                let berat = $(this).data("berat");

                if (item_id && existingItemId.has(item_id)) {
                    $(this).prop('checked', false).prop('disabled', true);
                    return;
                }

                if (item_id) existingItemId.add(item_id);

                let rowNode = tableDetail.row.add([
                    "",

                    `
                    <input type="hidden" name="detail[bom_detail_id][]" value="">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    `,

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${nama_item}">
                    ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,

                    `<span class="ellipsis" title="${kode_item}">
                        ${ellipsis(kode_item)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode_item}">`,

                    `<span class="view-mode qty-view">1.00</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode jumlah qty-edit d-none" name="detail[jumlah][]" value="1" min="0" step="any">`,

                    `<select class="form-control form-control-sm uom-select border-0 ellipsis" name="detail[satuan][]" title="${satuan}">
                        <option value="">Loading...</option>
                    </select>
                    <input type="hidden" class="form-control form-control-sm" name="detail[base_qty][]">`,

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly></textarea>`,
                ]).node();

                $(rowNode).addClass('tr-height-30');

                nodesToDraw.push(rowNode);

                $(this).prop('checked', false).prop('disabled', true);

                rowsAdded = true;

                loadUom($(rowNode), item_id);
            });

            if (rowsAdded) {
                tableDetail.draw(false);
                tableDetail.columns.adjust();
                toggleItemDisabled();
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

        $(document).on("input", ".qty, .harga-input", function() {
            let row = $(this).closest("tr");
            let qty = parseFloat(row.find(".qty").val()) || 0;
            let harga_input = parseFloat(row.find(".harga-input").val()) || 0;

            let hargaInputDisplay = (harga_input === 0) ?
                '0' :
                harga_input.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

            let subtotal = qty * harga_input;

            let subTotalDisplay = (subtotal === 0) ?
                '0' :
                subtotal.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

            row.find(".harga-input-b").text(hargaInputDisplay.toLocaleString("en-US"));
            row.find(".subtotal-text").text(subTotalDisplay.toLocaleString("en-US"));

            row.find('input[name="detail[harga][]"]').val(harga_input);
            row.find('input[name="detail[subtotal][]"]').val(subtotal);
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

                    toggleItemDisabled();

                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Item berhasil dihapus didaftar detail, klik save untuk menyimpan data.',
                        // timer: 2500,
                        showConfirmButton: true
                    });
                }
            });
        });

        $(document).on('change', '.uom-select', function() {

            let toQty = $(this).find(':selected').data('to-qty') || 1;

            $(this)
                .closest('tr')
                .find('.uom-to-qty')
                .val(toQty);
        });

        $(document).on('keydown', '.qty, .jumlah, .harga-input', function(e) {
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

        $(document).on('change', '.uom-select', function() {
            let toQty = $(this).find(':selected').data('to_qty') || 1;

            let row = $(this).closest('tr');
            row.find('input[name="detail[to_qty][]"]').val(toQty);
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
                $(row).attr('data-build_detail_id', data.build_detail_id);
            },
            "columns": [{
                    "className": 'details-control',
                    "orderable": false,
                    "searchable": false,
                    "data": null,
                    "defaultContent": '<i class="ri ri-add-line" style="cursor:pointer"></i>',
                },
                {
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "className": 'text-center',
                },
                {
                    "data": "nama_item",
                    render: function(data, type, row) {
                        // if (type === 'display' && data && data.length > 20) {
                        //     let cleanData = data.replace(/"/g, '&quot;'); 
                        //     return `<span title="${cleanData}">
                        //                 ${data.substr(0, 20)}...
                        //             </span>`;
                        // }
                        return data;
                    }
                },
                {
                    "data": "kode_item"
                },
                {
                    "data": "satuan"
                },
                {
                    "data": "mr",
                    "className": 'text-end',
                },
                {
                    "data": "po",
                    "className": 'text-end',
                },
                {
                    "data": "sisa",
                    "className": 'text-end',
                },
            ]
        });

        $('#table-info tbody').on('click', 'td.details-control', function() {
            const tr = $(this).closest('tr');
            const row = tableInfo.row(tr);
            const infoDetailID = tr.data('build_detail_id');
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

    document.querySelectorAll('.auto-width').forEach(input => {
        resizeInput(input);
        input.addEventListener('input', () => resizeInput(input));
    });

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('uom-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const toQty = selectedOption.getAttribute('data-to_qty');

            // cari hidden input dalam satu baris/form yang sama
            const hiddenInput = e.target.closest('td, div, tr').querySelector('.to-qty');
            if (hiddenInput) {
                hiddenInput.value = toQty;
            }
        }
    });

    // set nilai awal saat halaman pertama kali load
    document.querySelectorAll('.uom-select').forEach(function(select) {
        const selectedOption = select.options[select.selectedIndex];
        const toQty = selectedOption.getAttribute('data-to_qty');

        const hiddenInput = select.closest('td, div, tr').querySelector('.to-qty');
        if (hiddenInput) {
            hiddenInput.value = toQty;
        }
    });

    let openedSelect = null;
    let isOpening = false;

    $(document).on('change', '.uom-select', function() {
        let selected = $(this).find('option:selected');

        $(this).closest('td')
            .find('input[name="detail[to_qty][]"]')
            .val(selected.data('to_qty') || '');
    });

    let activeKeteranganInput = null;

    $('#table-detail tbody').on(
        'click',
        'textarea[name="detail[keterangan][]"]',
        function() {

            activeKeteranganInput = $(this);

            // isi modal dengan nilai input saat ini
            $('#modalKeteranganText').val($(this).val())

            $('#modalKeterangan').modal('show');
        }
    );

    $('#btnSaveKeterangan').on('click', function() {
        if (!activeKeteranganInput) return;

        activeKeteranganInput.val(
            $('#modalKeteranganText').val()
        );

        $('#modalKeterangan').modal('hide');
    });

    $('#modalKeterangan').on('hidden.bs.modal', function() {
        activeKeteranganInput = null;
        $('#modalKeteranganText').val('');
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
                    url: '<?= base_url() ?>formula/del',
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

    function resizeInput(el) {
        el.style.width = (el.value.length + 1) + 'ch';
    }

    const $switch = $('#customSwitch1');
    const $label = $('label[for="customSwitch1"]');

    updateLabel();
    $switch.on('change', updateLabel);

    function updateLabel() {
        $label.text($switch.is(':checked') ? 'Active' : 'Inactive');
    }

    let uomLoadingCount = 0;

    function loadUom(row, itemId) {

        let uomSelect = row.find('.uom-select');
        let toQtyInput = row.find('input[name="detail[base_qty][]"]');

        uomLoadingCount++;
        $('#loading').show();

        $.ajax({
            url: "<?= base_url() ?>formula/get_uom",
            type: 'POST',
            dataType: 'json',
            data: {
                item_id: itemId
            },
            success: function(res) {
                let html = '';

                $.each(res.data, function(i, v) {
                    html += `
                    <option value="${v.UOM_CODE}" data-code="${v.UOM_CODE}" data-base_qty="${v.TO_QTY}" data-label="${v.UOM_CODE} (${v.TO_QTY})">
                        ${v.UOM_CODE} (${v.TO_QTY})
                    </option>
                `;
                });

                uomSelect.html(html);

                let firstOption = uomSelect.find('option:first');
                toQtyInput.val(firstOption.data('base_qty'));
            },
            error: function(xhr, status, error) {
                console.error("Error load Uom:", error);
                uomSelect.html('<option value="">Error loading UOM</option>');
            },
            complete: function() {
                uomLoadingCount--;

                if (uomLoadingCount <= 0) {
                    $('#loading').hide();
                }
            }
        });
    }

    function formatNumber(value, decimal = 2) {
        if (value === "" || isNaN(value)) return "0.00";
        return parseFloat(value).toLocaleString("en-US", {
            minimumFractionDigits: decimal,
            maximumFractionDigits: decimal
        });
    }

    function toggleItemDisabled() {
        if (!tableDetail) return;

        let hasDetail = tableDetail.rows().count() > 0;
        let $item_finish_goods = $('#item_finish_goods');

        if (hasDetail) {
            $item_finish_goods.prop('disabled', true).trigger('change.select2');

            // Buat hidden input agar value tetap dikirim ke server
            if ($('#item_finish_goods-hidden').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'item_finish_goods-hidden',
                    name: $item_finish_goods.attr('name'),
                    value: $item_finish_goods.val()
                }).appendTo('form');
            } else {
                $('#item_finish_goods-hidden').val($item_finish_goods.val());
            }
        } else {
            $item_finish_goods.prop('disabled', false).trigger('change.select2');
            $('#item_finish_goods-hidden').remove();
        }
        $item_finish_goods.trigger('change.select2');
    }

    function resetmodalMrq() {
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

    function loadSatuan(item, selectedSatuan = null, isReadOnly = false) {
        $('#satuan')
            .empty()
            .prop('disabled', true)
            .trigger('change');
        if (!item) return;

        if (item) {
            $.ajax({
                url: "<?= base_url('mrq/get_item_uom') ?>",
                type: "POST",
                data: {
                    item_id: item
                },
                dataType: "json",
                success: function(response) {
                    if (response.status === "success") {

                        let units = response.data;

                        $.each(units, function(i, unit) {

                            let selected = "";

                            if (selectedSatuan && selectedSatuan === unit.UOM_CODE) {
                                selected = "selected";
                            } else if (!selectedSatuan && unit.URUT == 0) {
                                selected = "selected";
                            }

                            $('#satuan').append(
                                `<option value="${unit.UOM_CODE}" ${selected}>
                                    ${unit.UOM_CODE} (${unit.TO_QTY})
                                </option>`
                            );

                            $('#base_qty').val(unit.TO_QTY);
                        });

                        $('#satuan')
                            .prop('disabled', isReadOnly)
                            .trigger('change');
                    }
                }
            });
        }
    }

    $(document).on('click', '#btn-print', function() {
        setTimeout(function() {
            $('#loading').hide();
        }, 300);
    });
</script>