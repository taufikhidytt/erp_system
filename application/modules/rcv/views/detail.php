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
                                <a href="<?= base_url('rcv') ?>" class="text-decoration-underline">RCV</a>
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
                                    <span class="border border-1 border-dark p-2" id="statusTagId"></span>
                                    <span class="border border-1 border-warning p-2" id="readonlyTagId"></span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <a href="<?= base_url('rcv/add') ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Tambah">
                                        <i class="ri ri-add-box-fill"></i>
                                    </a>
                                    <button type="submit" class="btn btn-success btn-sm" name="submit" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan">
                                        <i class="ri ri-save-3-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" name="del-submit" id="del-submit" data-toggle="tooltip" data-placement="bottom" title="hapus" data-id_del="<?= $this->encrypt->encode($data->TAG_ID); ?>">
                                        <i class="ri ri-delete-bin-5-fill"></i>
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
                                            <input type="hidden" name="tag_id" id="tag_id" value="<?= $this->encrypt->encode($data->TAG_ID); ?>">
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
                                            <label for="site_storage">Site Storage:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-building-2-fill"></i>
                                                </span>
                                                <?php
                                                $defaultValue = null;
                                                foreach ($site_storage->result() as $ss) {
                                                    if ($ss->PRIMARY_FLAG == 'Y') {
                                                        $defaultValue = $ss->WAREHOUSE_ID;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="site_storage" id="site_storage" class="form-control select2 <?= form_error('site_storage') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Site Storage --</option>
                                                    <?php endif; ?>
                                                    <?php $param = $this->input->post('site_storage') ?? $data->DEST_WH_ID; ?>
                                                    <?php foreach ($site_storage->result() as $ss): ?>
                                                        <option
                                                            value="<?= $ss->WAREHOUSE_ID ?>"
                                                            <?= $ss->WAREHOUSE_ID == $param ? 'selected' : ($defaultValue == $ss->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($ss->WAREHOUSE_NAME) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('site_storage') ?></div>
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
                                            <label for="no_referensi">No Referensi:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-pantone-line"></i>
                                                </span>
                                                <input type="text" name="no_referensi" id="no_referensi" class="form-control <?= form_error('no_referensi') ? 'is-invalid' : null; ?>" placeholder="Enter No Referensi" value="<?= $this->input->post('no_referensi') ?? $data->DOCUMENT_REFF_NO; ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('no_referensi') ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="roow form-xs">
                                    <div class="col-12">
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
                                            <button type="button" id="removeRow" class="btn btn-danger btn-sm" style="width: 55px;">
                                                <i class="fa fa-trash"></i> Del
                                            </button>
                                            <button type="button" id="btn-modalSTS" class="btn btn-success btn-sm">
                                                <i class="ri ri-add-box-fill"></i> Add
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive overflow-auto" style="max-height: 450px;">
                                        <table class="table table-striped table-bordered" id="table-detail">
                                            <thead style="position: sticky; top: 0; background: #3d7bb9; z-index: 10; color: #ffff">
                                                <tr style="text-align: center !important;">
                                                    <th>No</th>
                                                    <th style="padding:0; margin:0; border:none; display: none;"></th>
                                                    <th>
                                                        <input type="checkbox" name="checkAllParent" id="checkAllParent" class="">
                                                    </th>
                                                    <th>No SJS</th>
                                                    <th>Nama Item</th>
                                                    <th>Kode Item</th>
                                                    <th>Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $dataDetail = $this->db->query("SELECT tag_detail.*, item.ITEM_CODE, item.ITEM_DESCRIPTION, tag_konsi.DOCUMENT_NO, tag_konsi_detail.ENTERED_QTY as konsi_ENTERED_QTY, tag_konsi_detail.BASE_QTY as konsi_BASE_QTY, tag_konsi_detail.RECEIVED_ENTERED_QTY as konsi_RECEIVED_ENTERED_QTY  FROM tag_detail JOIN item ON item.ITEM_ID = tag_detail.ITEM_ID JOIN tag_konsi_detail ON tag_konsi_detail.TAG_KONSI_DETAIL_ID = tag_detail.TAG_KONSI_DETAIL_ID JOIN tag_konsi ON tag_konsi.TAG_KONSI_ID = tag_konsi_detail.TAG_KONSI_ID WHERE tag_detail.TAG_ID = {$data->TAG_ID} ORDER BY TAG_DETAIL_ID ASC");

                                                if ($dataDetail->num_rows() > 0) { ?>
                                                    <?php
                                                    $no = 1;
                                                    foreach ($dataDetail->result() as $dd): ?>
                                                        <?php
                                                        $l = $dd->konsi_RECEIVED_ENTERED_QTY /  $dd->konsi_BASE_QTY;
                                                        $balance = $dd->konsi_ENTERED_QTY - $l; ?>
                                                        <tr class="tr-height-30">
                                                            <td><?= $no++ ?></td>
                                                            <td style="display: none;">
                                                                <input type="hidden" name="detail[tag_detail_id][]" id="tag_detail_id" value="<?= $this->encrypt->encode($dd->TAG_DETAIL_ID); ?>">
                                                                <input type="hidden" name="detail[tag_konsi_detail_id][]" value="<?= $dd->TAG_KONSI_DETAIL_ID ?>">
                                                                <input type="hidden" name="detail[no_sjs][]" value="<?= $dd->no_sjs ?>">
                                                                <input type="hidden" name="detail[po_detail_id][]" value="<?= $dd->PO_DETAIL_ID ?>">
                                                                <input type="hidden" name="detail[item_id][]" value="<?= $dd->ITEM_ID ?>">
                                                                <input type="hidden" name="detail[base_qty][]" value="<?= number_format(rtrim(rtrim($dd->BASE_QTY, '0'), '.'), 0, '.', ',') ?>">
                                                                <input type="hidden" name="detail[unit_price][]" value="<?= number_format(rtrim(rtrim($dd->UNIT_PRICE, '0'), '.'), 2, '.', ','); ?>">
                                                                <input type="hidden" name="detail[harga_input][]" value="<?= number_format(rtrim(rtrim($dd->HARGA_INPUT, '0'), '.'), 2, '.', ','); ?>">
                                                                <input type="hidden" name="detail[berat][]" value="<?= number_format(rtrim(rtrim($dd->BERAT, '0'), '.'), 0, '.', ',') ?>">
                                                                <input type="hidden" name="detail[balance][]" value="<?= number_format(rtrim(rtrim($dd->ENTERED_QTY, '0'), '.'), 0, '.', ',') ?>">
                                                                <input type="hidden" name="detail[gudang_id][]" value="<?= $dd->WAREHOUSE_ID ?>">
                                                                <input type="hidden" name="detail[gudang_tujuan_id][]" value="<?= $dd->TO_WH_ID ?>">
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" class="chkDetail">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <span class=" ellipsis align-middle" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->DOCUMENT_NO ?>">
                                                                    <?= $dd->DOCUMENT_NO; ?>
                                                                </span>
                                                                <input type="hidden" name="detail[no_sjs][]" value="<?= $dd->DOCUMENT_NO ?>">
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
                                                                <input type="number" class="form-control form-control-sm qty auto-width edit-mode qty-edit d-none enter-as-tab" min="0" step="any" name="detail[jumlah][]" data-balance="<?= ($balance == 0) ? '0' : rtrim(rtrim((string)$balance, '0'), '.') ?>" data-tag_detail_id="<?= $this->encrypt->encode($dd->TAG_DETAIL_ID) ?>" data-value_old="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>" value="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>">
                                                            </td>
                                                            <td class="ellipsis" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->ENTERED_UOM ?>">
                                                                <span class="ellipsis" title="<?= $dd->ENTERED_UOM ?>">
                                                                    <?= $dd->ENTERED_UOM ?>
                                                                </span>
                                                                <input type="hidden" name="detail[satuan][]" value="<?= $dd->ENTERED_UOM ?>">
                                                            </td>
                                                            <td class="ellipsis">
                                                                <textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly data-toggle="tooltip" data-placement="bottom" title="<?= $dd->NOTE; ?>"><?= $this->input->post('detail[keterangan]') ?? $dd->NOTE; ?></textarea>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                <?php } ?>
                                            </tbody>
                                        </table>
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
<div id="modalSTS" class="modal fade" style="font-size: 12px;">
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
        let tag_id = $('#tag_id').val();
        $.ajax({
            url: '<?= base_url() ?>rcv/getStatus',
            type: 'POST',
            dataType: 'json',
            data: {
                tag_id: tag_id,
            },
            success: function(response) {
                $('#statusTagId').text(response.data[0].DISPLAY_NAME);
                $('#readonlyTagId').hide();

                if (response.data[0].ITEM_FLAG === 'N') {
                    $('#readonlyTagId').show();
                    $('#readonlyTagId').text('READ ONLY');
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
                        `<span type="button" id="removeRow" class="btn btn-danger btn-sm" disabled style="width: 55px; pointer-events: none; opacity: 0.6; cursor: not-allowed;">
                            <i class="fa fa-trash"></i> Del
                        </span>`
                    );

                    $('#btn-modalSTS').replaceWith(
                        `<span type="button" id="btn-modalSTS" class="btn btn-success btn-sm" disabled style="pointer-events: none; opacity: 0.6; cursor: not-allowed;">
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
                    width: "25%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no grk
                {
                    targets: 4,
                    width: "13%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // nama item
                {
                    targets: 5,
                    width: "8%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // kode item
                {
                    targets: 6,
                    width: "10%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer'
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
                    width: "10%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // keterangan
            ],
        });

        toggleStorageDisabled();

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

        $("#main_storage").data("prev", $("#main_storage").val());

        $("#main_storage").on("change", function(e, data) {
            let prev = $(this).data("prev");
            let current = $(this).val();

            if (prev && current !== prev && tableDetail.rows().count() > 0) {

                Swal.fire({
                    title: "Ganti Main Storage?",
                    text: "Data SJS yang sudah dipilih akan dihapus.",
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
        $("#btn-modalSTS").on("click", function() {
            resetmodalSTS();

            $("#checkAll").prop('checked', false);
            $('#loading').show();
            var site_storage = $('#site_storage').val();

            if (!site_storage) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Site storage tidak terisi, Mohon isi terlebih dahulu',
                });
                return;
            }
            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>rcv/getSjs",
                data: {
                    site_storage: site_storage,
                },
                dataType: "json",
                success: function(response) {
                    $('#loading').hide();
                    tableItem.clear().draw();

                    let existingCodes = new Set();
                    tableDetail.rows().every(function() {
                        let node = this.node();
                        let kode = $(node).find('input[name="detail[tag_konsi_detail_id][]"]').val();
                        if (kode) {
                            existingCodes.add(kode);
                        }
                    });

                    if (response.status === 'success' && Array.isArray(response.data)) {
                        let no = 1;
                        response.data.forEach(function(item) {

                            if (existingCodes.has(item.TAG_KONSI_DETAIL_ID)) {
                                return;
                            }

                            var checkbox = `
                            <input type="checkbox" class="chkRow"
                                data-tag_konsi_detail_id="${item.TAG_KONSI_DETAIL_ID}"
                                data-item_id="${item.ITEM_ID}"
                                data-base_qty="${item.BASE_QTY}"
                                data-unit_price="${item.UNIT_PRICE}"
                                data-harga_input="${item.HARGA_INPUT}"
                                data-note="${item.NOTE}"
                                data-berat="${item.BERAT}"
                                data-gudang_id="${item.WAREHOUSE_ID}"
                                data-gudang_tujuan_id="${item.TO_WH_ID}"
                                data-po_detail_id="${item.PO_DETAIL_ID}"

                                data-status="${item.STATUS_NAME}"
                                data-tanggal="${item.DOCUMENT_DATE}"
                                data-no_sjs="${item.DOCUMENT_NO}"
                                data-no_referensi="${item.DOCUMENT_REFF_NO}"
                                data-nama_item="${item.ITEM_DESCRIPTION}"
                                data-kode_item="${item.ITEM_CODE}"
                                data-jumlah="${item.ENTERED_QTY}"
                                data-sisa="${item.BALANCE}"
                                data-satuan="${item.ENTERED_UOM}"
                                data-gudang_asal="${item.GUDANG_ASAL}"
                            >
                            `;

                            tableItem.row.add([
                                checkbox,
                                no++,
                                item.STATUS_NAME,
                                item.DOCUMENT_DATE,
                                item.DOCUMENT_NO,
                                item.DOCUMENT_REFF_NO,
                                item.ITEM_DESCRIPTION,
                                item.ITEM_CODE,
                                parseFloat(item.ENTERED_QTY).toFixed(2),
                                parseFloat(item.BALANCE).toFixed(2),
                                item.ENTERED_UOM,
                                item.GUDANG_ASAL,
                            ]);
                        });
                        tableItem.draw();
                    }
                    $('#modalTitleForm').text('List SJS');
                    $('#modalSTS').modal('show');
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

            let existingCodes = new Set();
            tableDetail.rows().every(function() {
                let node = this.node();
                let kodeText = $(node).find('td:eq(4) span').text().trim();
                existingCodes.add(kodeText);
            });

            let allRows = tableItem.rows().nodes();
            let nodesToDraw = [];

            $(allRows).find('.chkRow:checked:not(:disabled)').each(function() {
                let tag_konsi_detail_id = $(this).data("tag_konsi_detail_id");
                let item_id = $(this).data("item_id");
                let base_qty = $(this).data("base_qty");
                let unit_price = $(this).data("unit_price");
                let harga_input = $(this).data("harga_input");
                let keterangan = $(this).data("note") ?? '';
                let berat = $(this).data("berat");
                let gudang_id = $(this).data("gudang_id");
                let gudang_tujuan_id = $(this).data("gudang_tujuan_id");
                let po_detail_id = $(this).data("po_detail_id");
                let balance = $(this).data("sisa");

                let no_sjs = $(this).data("no_sjs");
                let nama_item = $(this).data("nama_item");
                let kode_item = $(this).data("kode_item");
                let jumlah = $(this).data("jumlah");
                let satuan = $(this).data("satuan");

                if (existingCodes.has(tag_konsi_detail_id)) {
                    $(this).prop('checked', false).prop('disabled', true);
                    return;
                }

                existingCodes.add(tag_konsi_detail_id);

                let rowNode = tableDetail.row.add([
                    "",

                    `<input type="hidden" name="detail[tag_detail_id][]" value="">
                    <input type="hidden" name="detail[tag_konsi_detail_id][]" value="${tag_konsi_detail_id}">
                    <input type="hidden" name="detail[po_detail_id][]" value="${po_detail_id}">
                    <input type="hidden" name="detail[no_sjs][]" value="${no_sjs}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[kode_item][]" value="${kode_item}">
                    <input type="hidden" name="detail[base_qty][]" value="${formatNumber(base_qty)}">
                    <input type="hidden" name="detail[unit_price][]" value="${unit_price}">
                    <input type="hidden" name="detail[harga_input][]" value="${harga_input}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${balance}">
                    <input type="hidden" name="detail[gudang_id][]" value="${gudang_id}">
                    <input type="hidden" name="detail[gudang_tujuan_id][]" value="${gudang_tujuan_id}">`,

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${no_sjs}">
                        ${ellipsis(no_sjs)}
                    </span>`,

                    `<span class="ellipsis" title="${nama_item}">
                        ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,

                    `<span class="ellipsis" title="${kode_item}">
                        ${ellipsis(kode_item)}
                    </span>`,

                    `<span class="view-mode qty-view">${formatNumber(balance)}</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab" name="detail[jumlah][]" value="${Math.floor(Number(balance))}" min="0" step="any" data-balance="${Math.floor(Number(balance))}">`,

                    `<span class="ellipsis" title="${satuan}">
                        ${ellipsis(satuan)}
                    </span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">`,

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly>${keterangan}</textarea>`,
                ]).draw(false).node();

                $(rowNode).addClass('tr-height-30');

                nodesToDraw.push(rowNode);

                $(this).prop('checked', false).prop('disabled', true);

                rowsAdded = true;

                if (rowsAdded) {
                    tableDetail.draw(false);
                    tableDetail.columns.adjust().draw(false); // refresh layout
                    toggleStorageDisabled();
                }

                $("#modalSTS").modal("hide");
            });
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

                    toggleStorageDisabled();

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

        $(document).on('keydown', '.qty, .harga-input', function(e) {
            if (
                e.key === 'e' || e.key === 'E' ||
                e.key === '+' || e.key === '-'
            ) {
                e.preventDefault();
            }
        });

        $(document).on('input change', '.harga-input', function() {
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

    $(document).on('mousedown', '.uom-select', function() {
        openedSelect = this;
        isOpening = true;

        $(this).find('option').each(function() {
            $(this).text($(this).data('label'));
        });
    });

    $(document).on('change', '.uom-select', function() {
        let selected = $(this).find('option:selected');

        $(this).closest('td')
            .find('input[name="detail[to_qty][]"]')
            .val(selected.data('to_qty') || '');
    });

    // dropdown ditutup
    $(document).on('click', function() {
        if (!openedSelect) return;

        setTimeout(() => {
            if (isOpening) {
                isOpening = false;
                return;
            }

            let $select = $(openedSelect);

            $select.find('option').each(function() {
                $(this).text($(this).data('code'));
            });

            openedSelect = null;
        }, 0);
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

    document.addEventListener('input', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;

        const input = e.target;
        const tag_detail_id = input.dataset.tag_detail_id;
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
        if (tag_detail_id) {
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

        const input = e.target;
        const tag_detail_id = input.dataset.tag_detail_id;
        const value_old = parseFloat(input.dataset.value_old);
        const row = $(input).closest("tr");
        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) {
                span.textContent = val.toFixed(2).replace('.', ',');
            }
        }

        const balance = parseFloat(input.dataset.balance);

        if (tag_detail_id) {
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
                    updateSpan(value_old);
                    hitungRow(row);
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
                    updateSpan(value_old);
                    hitungRow(row);
                });
                return;
            }
            updateSpan(balance);
            hitungRow(row);
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
                    updateSpan(balance);
                    hitungRow(row);
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
                    updateSpan(balance);
                    hitungRow(row);
                });
                return;
            }
            updateSpan(balance);
            hitungRow(row);
        }
    }, true);

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
                    url: '<?= base_url() ?>rcv/del',
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
        let $main_storage = $('#main_storage');
        let $site_storage = $('#site_storage');

        if (hasDetail) {
            $main_storage.prop('disabled', true).trigger('change.select2');
            $site_storage.prop('disabled', true).trigger('change.select2');

            // Buat hidden input agar value tetap dikirim ke server
            if ($('#main_storage-hidden').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'main_storage-hidden',
                    name: $main_storage.attr('name'),
                    value: $main_storage.val()
                }).appendTo('form');
            } else {
                $('#main_storage-hidden').val($main_storage.val());
            }

            if ($('#site_storage-hidden').length === 0) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'site_storage-hidden',
                    name: $site_storage.attr('name'),
                    value: $site_storage.val()
                }).appendTo('form');
            } else {
                $('#site_storage-hidden').val($site_storage.val());
            }
        } else {
            $main_storage.prop('disabled', false).trigger('change.select2');
            $site_storage.prop('disabled', false).trigger('change.select2');
            $('#main_storage-hidden').remove();
            $('#site_storage-hidden').remove();
        }

        $main_storage.trigger('change.select2');
        $site_storage.trigger('change.select2');
    }

    function resetmodalSTS() {
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
</script>