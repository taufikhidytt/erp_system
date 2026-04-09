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

    .table-sub tbody td{
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
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('do_kny') ?>" class="text-decoration-underline">DO KNY</a>
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
                                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center gap-2 label-status">
                                    <h5 id="statusDoKnyId" style="width: 100px;"></h5>
                                    <h5 style="width: 100px;" id="readonlyDoKnyId"></h5>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <a href="<?= base_url('do_kny/add') ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Tambah">
                                        <i class="ri ri-add-box-fill"></i>
                                    </a>
                                    <button type="submit" class="btn btn-success btn-sm" name="submit" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan">
                                        <i class="ri ri-save-3-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" name="del-submit" id="del-submit" data-toggle="tooltip" data-placement="bottom" title="hapus" data-id_del="<?= $this->encrypt->encode($data->INVENTORY_OUT_ID); ?>">
                                        <i class="ri ri-delete-bin-5-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.replace(window.location.pathname);" data-toggle="tooltip" data-placement="bottom" title="Reload">
                                        <i class="ri ri-reply-fill"></i>
                                    </button>
                                    <a href="<?= site_url('do_kny/print/'.base64url_encode($this->encrypt->encode($data->INVENTORY_OUT_ID))) ?>" id="btn-print" target="_blank" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="bottom" title="Print">
                                        <i class="ri ri-printer-fill"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <input type="hidden" name="inventory_out_id" id="inventory_out_id" value="<?= $this->encrypt->encode($data->INVENTORY_OUT_ID); ?>">
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
                                            <label for="customer">Customer:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-3-fill"></i>
                                                </span>
                                                <select name="customer" id="customer" class="form-control select2 <?= form_error('customer') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Customer --</option>
                                                    <?php $param = $this->input->post('location_id') ?? $data->PERSON_SITE_ID; ?>
                                                    <?php foreach ($customer->result() as $cs): ?>
                                                        <option value="<?= $cs->PERSON_ID ?>" <?= $cs->PERSON_SITE_ID == $param ? 'selected' : null ?> data-person_site_id="<?= $cs->PERSON_SITE_ID ?>">
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
                                                <select name="location" id="location" class="form-control select2 <?= form_error('location') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Location --</option>
                                                </select>
                                                <input type="hidden" name="location_id" id="location_id" class="form-control" readonly value="<?= $this->input->post('location_id') ?? $data->PERSON_SITE_ID; ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('location') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <textarea name="address" id="address" class="form-control" disabled></textarea>
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
                                            <label for="sales">Sales:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-2-fill"></i>
                                                </span>
                                                <input type="text" name="sales" id="sales" class="form-control" placeholder="Enter Sales" value="<?= $this->input->post('sales') ?? $data->FIRST_NAME . " [" . $data->LAST_NAME . "]"; ?>" readonly>
                                                <input type="hidden" name="sales_id" id="sales_id" value="<?= $this->input->post('sales_id') ?? $data->KARYAWAN_ID; ?>">
                                                <input type="hidden" name="so_id" id="so_id" value="<?= $this->input->post('so_id') ?? $data->SO_ID; ?>">
                                                <input type="hidden" name="ppn_code" id="ppn_code" value="<?= $this->input->post('ppn_code') ?? $data->PPN_CODE; ?>">
                                                <input type="hidden" name="ppn_percen" id="ppn_percen" value="<?= $this->input->post('ppn_percen') ?? $data->PPN_PERCEN; ?>">
                                                <input type="hidden" name="pph_code" id="pph_code" value="<?= $this->input->post('pph_code') ?? $data->PPH_CODE; ?>">
                                                <input type="hidden" name="pph_percen" id="pph_percen" value="<?= $this->input->post('pph_percen') ?? $data->PPH_PERCEN; ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('sales') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="po_customer">PO Customer:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-profile-fill"></i>
                                                </span>
                                                <input type="text" name="po_customer" id="po_customer" class="form-control" placeholder="Enter PO Customer" value="<?= $this->input->post('po_customer') ?? $data->DOCUMENT_REFF_NO; ?>" readonly>
                                            </div>
                                            <div class="text-danger"><?= form_error('po_customer') ?></div>
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
                                                    <?php foreach ($storage->result() as $st): ?>
                                                        <option
                                                            value="<?= $st->WAREHOUSE_ID ?>"
                                                            <?= $st->WAREHOUSE_ID == $param ? 'selected' : ($defaultValue == $st->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($st->WAREHOUSE_NAME) ?>
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
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#info-detail" role="tab" aria-selected="true">
                                                <span class="d-block d-sm-none"><i class="ri ri-eye-2-fill"></i></span>
                                                <span class="d-none d-sm-block">Info</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <!-- Tab panes -->
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
                                                    <thead style="position: sticky; top: 0; background: #3d7bb9; z-index: 10; color: #ffff">
                                                        <tr style="text-align: center !important;">
                                                            <th>No</th>
                                                            <th style="padding:0; margin:0; border:none; display: none;"></th>
                                                            <th>
                                                                <input type="checkbox" name="checkAllParent" id="checkAllParent" class="">
                                                            </th>
                                                            <th>No SO</th>
                                                            <th>No MR</th>
                                                            <th>Nama Item</th>
                                                            <th>Kode Item</th>
                                                            <th>Memo</th>
                                                            <th>Jumlah</th>
                                                            <th>Satuan</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $dataDetail = $this->db->query("SELECT COALESCE
                                                            (
                                                            CASE
                                                                WHEN b.BASE_QTY = 0
                                                                OR b.BASE_QTY IS NULL THEN
                                                                    b.ENTERED_QTY ELSE b.ENTERED_QTY - ( b.RECEIVED_ENTERED_QTY / b.BASE_QTY )
                                                                    END) AS BALANCE,
                                                            a.*,
                                                            i.ITEM_CODE,
                                                            i.ITEM_DESCRIPTION,
                                                            inventory_out.DOCUMENT_NO,
                                                            bl.DOCUMENT_NO AS no_transaksi,
                                                            so.DOCUMENT_NO AS no_so,
                                                            inventory_out.KARYAWAN_ID
                                                        FROM
                                                            inventory_out_detail a
                                                            JOIN inventory_out ON inventory_out.INVENTORY_OUT_ID = a.INVENTORY_OUT_ID
                                                            JOIN so_detail b ON a.SO_DETAIL_ID = b.SO_DETAIL_ID
                                                            JOIN so ON so.SO_ID = b.SO_ID
                                                            JOIN build bl ON b.BUILD_ID = bl.BUILD_ID
                                                            JOIN item i ON a.ITEM_ID = i.ITEM_ID
                                                        WHERE
                                                            a.INVENTORY_OUT_ID = '{$data->INVENTORY_OUT_ID}' 
                                                        ORDER BY
                                                            a.INVENTORY_OUT_DETAIL_ID ASC ;");

                                                        if ($dataDetail->num_rows() > 0) { ?>
                                                            <?php
                                                            $no = 1;
                                                            $postDetail = $this->input->post('detail');
                                                            $i = 0;
                                                            foreach ($dataDetail->result() as $dd): ?>
                                                                <tr class="tr-height-30">
                                                                    <td><?= $no++ ?></td>
                                                                    <td style="display: none;">
                                                                        <input type="hidden" name="detail[inventory_out_detail_id][]" value="<?= $this->encrypt->encode($dd->INVENTORY_OUT_DETAIL_ID); ?>">
                                                                        <input type="hidden" name="detail[no_transaksi][]" value="<?= $dd->no_transaksi ?>">
                                                                        <input type="hidden" name="detail[so_detail_id][]" value="<?= $dd->SO_DETAIL_ID ?>">
                                                                        <input type="hidden" name="detail[build_id][]" value="<?= $dd->BUILD_ID  ?>">
                                                                        <input type="hidden" name="detail[item_id][]" value="<?= $dd->ITEM_ID  ?>">
                                                                        <input type="hidden" name="detail[base_qty][]" value="<?= $dd->BASE_QTY  ?>">
                                                                        <input type="hidden" name="detail[unit_price][]" value="<?= $dd->UNIT_PRICE  ?>">
                                                                        <input type="hidden" name="detail[subtotal][]" value="<?= $dd->SUBTOTAL  ?>">
                                                                        <input type="hidden" name="detail[diskon_price][]" value="<?= $dd->DISCOUNT_PRICE  ?>">
                                                                        <input type="hidden" name="detail[hpp][]" value="<?= $dd->HPP  ?>">
                                                                        <input type="hidden" name="detail[diskon_persen][]" value="<?= $dd->DISCOUNT_PERCEN  ?>">
                                                                        <input type="hidden" name="detail[diskon_input][]" value="<?= $dd->DISKON_INPUT  ?>">
                                                                        <input type="hidden" name="detail[harga_input][]" value="<?= $dd->HARGA_INPUT  ?>">
                                                                        <input type="hidden" name="detail[berat][]" value="<?= $dd->BERAT  ?>">
                                                                        <input type="hidden" name="detail[balance][]" value="<?= $dd->BALANCE  ?>">
                                                                        <input type="hidden" name="detail[karyawan_id][]" value="<?= $dd->KARYAWAN_ID  ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input type="checkbox" class="chkDetail">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class="ellipsis align-middle" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->no_so ?>">
                                                                            <?= $dd->no_so; ?>
                                                                        </span>
                                                                        <input type="hidden" name="detail[no_so][]" value="<?= $dd->no_so ?>">
                                                                    </td>
                                                                    <td class="ellipsis">
                                                                        <span class="ellipsis align-middle" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->no_transaksi ?>">
                                                                            <?= $dd->no_transaksi; ?>
                                                                        </span>
                                                                        <input type="hidden" name="detail[no_transaksi][]" value="<?= $dd->no_transaksi ?>">
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
                                                                        <textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly><?= $dd->KET ?></textarea>
                                                                    </td>
                                                                    <td class="ellipsis text-end">
                                                                        <span class="view-mode qty-view ellipsis align-middle">
                                                                            <?= number_format(rtrim(rtrim($dd->ENTERED_QTY, '0'), '.'), 2, '.', ','); ?>
                                                                        </span>
                                                                        <input type="number" class="form-control form-control-sm qty auto-width edit-mode qty-edit d-none enter-as-tab jumlah" min="0" step="any" name="detail[jumlah][]" data-balance="<?= ($dd->BALANCE == 0) ? '0' : rtrim(rtrim((string)$dd->BALANCE, '0'), '.') ?>" data-inventory_out_detail_id="<?= $this->encrypt->encode($dd->INVENTORY_OUT_DETAIL_ID) ?>" data-value_old="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>" value="<?= ($dd->ENTERED_QTY == 0) ? '0' : rtrim(rtrim((string)$dd->ENTERED_QTY, '0'), '.') ?>">
                                                                    </td>
                                                                    <td class="ellipsis" data-toggle="tooltip" data-placement="bottom" title="<?= $dd->ENTERED_UOM ?>">
                                                                        <span class="ellipsis" title="<?= $dd->ENTERED_UOM ?>">
                                                                            <?= $dd->ENTERED_UOM ?>
                                                                        </span>
                                                                        <input type="hidden" name="detail[satuan][]" value="<?= $dd->ENTERED_UOM ?>">
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

                                        <div class="tab-pane" id="info-detail" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table w-100 table-sm" id="table-info" data-url=" <?= site_url('do_kny/get_info/' . base64url_encode($this->encrypt->encode($data->INVENTORY_OUT_ID))) ?>">
                                                    <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
                                                        <tr>
                                                            <th></th>
                                                            <th>No</th>
                                                            <th>Nama Item</th>
                                                            <th>Kode Item</th>
                                                            <th>Satuan</th>
                                                            <th>DO</th>
                                                            <th>INV</th>
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
                    <table class="table table-bordered table-sm" id="table-item">
                        <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
                            <tr class="text-nowrap">
                                <th></th>
                                <th></th>
                                <th>No</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>No Transaksi</th>
                                <th>No Referensi</th>
                                <th>Nama Customer</th>
                                <th>Gudang</th>
                                <th>Sales</th>
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

<div id="table-info-detail" class="d-none" data-url="<?= site_url('do_kny/get_info_detail/') ?>">
    <table class="table table-sm table-bordered w-100 table-sub">
        <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
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
</div>

<script>
    let tableDetail;
    let tableItem;
    let tableInfo;
    let existingSoDetailId = new Set(); // Global variable untuk track so_detail_id yang sudah ada di table detail

    $(document).ready(function() {
        let inventory_out_id = $('#inventory_out_id').val();
        $.ajax({
            url: '<?= base_url() ?>do_kny/getStatus',
            type: 'POST',
            dataType: 'json',
            data: {
                inventory_out_id: inventory_out_id,
            },
            success: function(response) {
                $('#statusDoKnyId').html(badgeStatus(response.data[0].DISPLAY_NAME,response.data[0].MENU_ICON));
                $('#readonlyDoKnyId').hide();

                if (response.data[0].ITEM_FLAG === 'N') {
                    $('#readonlyDoKnyId').show();
                    $('#readonlyDoKnyId').html('<span class="badge bg-secondary">READ ONLY</span>');
                    $('#myForm')
                        .find('input, select, textarea, #removeRow, #btn-modalItem, td input')
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
                }, // no so
                {
                    targets: 4,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no mr
                {
                    targets: 5,
                    width: "20%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // nama item
                {
                    targets: 6,
                    width: "15%",
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // kode item
                {
                    targets: 7,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // memo
                {
                    targets: 8,
                    width: "15%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // jumlah
                {
                    targets: 9,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // satuan
                {
                    targets: 10,
                    width: "15%",
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
                    className : "text-center",
                }, // checkbox
                {
                    targets: 1,
                    className: 'details-control text-center',
                    defaultContent: '<i class="ri ri-add-line" style="cursor:pointer"></i>',
                }, // expand child
                {
                    targets: 2,
                    className : "text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // no
                {
                    targets: 3,
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
                    targets: 4,
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
                }, // no transaksi
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
                }, // no refrensi
                {
                    targets: 7,
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
                }, // nama customer
                {
                    targets: 8,
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
                }, // gudang
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
                }, // sales
                {
                    targets: 10,
                    visible: false,
                }, // so_id
            ],
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: false,
        });

        let oldDetail = <?= json_encode($detail ?? []) ?>;

        if (oldDetail && oldDetail.kode_item) {
            oldDetail.kode_item.forEach(function(kode, i) {

                let inventory_out_id = oldDetail.inventory_out_id[i] ?? '';

                if (inventory_out_id !== '') {
                    return;
                }

                let nomor = tableDetail.rows().count() + 1;

                let no_transaksi = oldDetail.no_transaksi[i] ?? '';
                let no_mr = oldDetail.no_mr[i] ?? '';
                let so_detail_id = oldDetail.so_detail_id[i] ?? '';
                let item_id = oldDetail.item_id[i] ?? '';
                let build_id = oldDetail.build_id[i] ?? '';
                let nama_item = oldDetail.nama_item[i] ?? '';
                let jumlah = oldDetail.jumlah[i] ?? '';
                let base_qty = oldDetail.base_qty[i] ?? '';
                let sisa = oldDetail.balance[i] ?? '';
                let satuan = oldDetail.satuan[i] ?? '';
                let unit_price = oldDetail.unit_price[i] ?? '';
                let subtotal = oldDetail.subtotal[i] ?? '';
                let hpp = oldDetail.hpp[i] ?? '';
                let harga_input = oldDetail.harga_input[i] ?? '';
                let berat = oldDetail.berat[i] ?? '';
                let note = oldDetail.keterangan[i] ?? '';
                let memo = oldDetail.memo[i] ?? '';
                let diskon_price = oldDetail.diskon_price[i] ?? '';
                let diskon_persen = oldDetail.diskon_persen[i] ?? '';
                let diskon_input = oldDetail.diskon_input[i] ?? '';

                let rowNode = tableDetail.row.add([
                    nomor,

                    `
                    <input type="hidden" name="detail[no_transaksi][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[so_detail_id][]" value="${so_detail_id}">
                    <input type="hidden" name="detail[build_id][]" value="${build_id}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${formatNumber(base_qty)}">
                    <input type="hidden" name="detail[unit_price][]" value="${unit_price}">
                    <input type="hidden" name="detail[subtotal][]" value="${subtotal}">
                    <input type="hidden" name="detail[diskon_price][]" value="${diskon_price}">
                    <input type="hidden" name="detail[hpp][]" value="${hpp}">
                    <input type="hidden" name="detail[diskon_persen][]" value="${diskon_persen}">
                    <input type="hidden" name="detail[diskon_input][]" value="${diskon_input}">
                    <input type="hidden" name="detail[harga_input][]" value="${harga_input}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${sisa}">`,

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${no_transaksi}">
                    ${ellipsis(no_transaksi)}
                    </span>
                    <input type="hidden" name="detail[no_transaksi][]" value="${no_transaksi}">
                    `,

                    `<span class="ellipsis" title="${no_mr}">
                        ${ellipsis(no_mr)}
                    </span>
                    <input type="hidden" name="detail[no_mr][]" value="${no_mr}">
                    `,

                    `<span class="ellipsis" title="${nama_item}">
                    ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">`,

                    `<span class="ellipsis" title="${kode}">
                        ${ellipsis(kode)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode}">`,

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly>${memo}</textarea>`,

                    `<span class="view-mode qty-view">${formatNumber(jumlah)}</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode jumlah qty-edit d-none enter-as-tab" name="detail[jumlah][]" value="${Math.floor(Number(jumlah))}" min="0" step="any" data-balance="${Math.floor(Number(balance))}">`,

                    `<span class="ellipsis" title="${satuan}">
                        ${ellipsis(satuan)}
                    </span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">`,

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

        <?php if (isset($warning)): ?>
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: <?= json_encode($warning) ?>
            });
        <?php endif; ?>

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

        let initialCustomer = $('#location_id').val();

        let oldLocation = "<?= set_value('location') ?>";

        if (initialCustomer) {
            loadLocation(initialCustomer, oldLocation);
        }

        $('#customer').on('change', function() {
            let initialCustomer = $(this).find(':selected').data('person_site_id');
            loadLocation(initialCustomer);
        });

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
            $('#loading').show();
            var customer = $('#customer').val();
            var storage = $('#storage').val();

            if (!customer) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Customer tidak terisi, Mohon isi terlebih dahulu',
                });
                return;
            }

            if (!storage) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Storage tidak terisi, Mohon isi terlebih dahulu',
                });
                return;
            }

            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>do_kny/getSo",
                data: {
                    customer: customer,
                    storage: storage,
                },
                dataType: "json",
                success: function(response) {
                    $('#loading').hide();
                    tableItem.clear().draw();

                    // Reset global variable
                    existingSoDetailId.clear();

                    // Kumpulkan so_detail_id yang sudah ada di table detail
                    let existingSoId = new Map(); // Map no_transaksi -> array of so_detail_id

                    tableDetail.rows().every(function() {
                        let node = this.node();
                        let soDetailId = $(node).find('input[name="detail[so_detail_id][]"]').val();
                        let noTransaksi = $(node).find('input[name="detail[no_transaksi][]"]').val();

                        if (soDetailId) {
                            existingSoDetailId.add(String(soDetailId));

                            // Group by no_transaksi untuk referensi
                            if (!existingSoId.has(noTransaksi)) {
                                existingSoId.set(noTransaksi, []);
                            }
                            existingSoId.get(noTransaksi).push(String(soDetailId));
                        }
                    });

                    if (response.status === 'success' && Array.isArray(response.data)) {
                        response.data.forEach(function(item, i) {
                            var checkbox = `
                            <input type="checkbox" class="chkRow"
                                data-so_detail_id="${item.SO_DETAIL_ID}"
                                data-so_id="${item.SO_ID}"
                                data-build_id="${item.BUILD_ID}"
                                data-status="${item.STATUS_NAME}"
                                data-tanggal="${item.DOCUMENT_DATE}"
                                data-no_transaksi="${item.DOCUMENT_NO}"
                                data-no_referensi="${item.DOCUMENT_REFF_NO}"
                                data-nama_person="${item.PERSON_NAME} - [${item.PERSON_CODE}]"
                                data-gudang="${item.WAREHOUSE_NAME}"
                                data-nama_sales="${item.FIRST_NAME} - [${item.LAST_NAME}]"
                                data-sales_id="${item.KARYAWAN_ID}"
                            >
                            `;
                            var expand = `<i class="ri ri-add-line" style="cursor:pointer"></i>`;
                            tableItem.row.add([
                                checkbox,
                                expand,
                                i + 1,
                                badgeStatus(item.STATUS_NAME,item.MENU_ICON),
                                item.DOCUMENT_DATE,
                                item.DOCUMENT_NO,
                                item.DOCUMENT_REFF_NO,
                                item.PERSON_NAME + " - [" + item.PERSON_CODE + "]",
                                item.WAREHOUSE_NAME,
                                item.FIRST_NAME + " - [" + item.LAST_NAME + "]",
                                item.SO_ID,
                            ]);
                        });
                        tableItem.draw();

                        // Show semua parent row setelah draw
                        $('#table-item tbody tr').show();

                        // Disable semua child checkbox karena belum ada parent yang ter-check
                        $('#table-item tbody tr').each(function() {
                            var tr = tableItem.row($(this));
                            var trData = tr.data();
                            var trSoId = trData && trData[10] ? trData[10] : null;
                            if (trSoId) {
                                var childTable = $('#child-' + trSoId);
                                if (childTable.length) {
                                    childTable.find('tbody .childCheckbox, #checkAllChild_' + trSoId).prop('disabled', true);
                                }
                            }
                        });
                    }
                    $('#modalTitleForm').text('List Data');
                    $('#modalMrq').modal('show');
                }
            });
        });

        // Add event listener for opening and closing details
        $('#table-item tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = tableItem.row(tr);
            var icon = $(this).find('i');

            var rowData = row.data();
            var so_id = rowData && rowData[10] ? rowData[10] : null;

            if (!so_id) return; // Exit jika so_id tidak ada

            var customer = $('#customer').val();
            var storage = $('#storage').val();

            // Ambil flag dari row data
            var shouldCheckAllChildren = tr.data('shouldCheckAllChildren') || false;

            if (row.child.isShown()) {
                // Close row
                row.child.hide();
                icon.removeClass('ri-subtract-line').addClass('ri-add-line');

                // Reset flag
                tr.data('shouldCheckAllChildren', false);

                // Saat row di-close, pastikan child checkbox tetap enabled jika parent ter-check
                var parentCheckbox = tr.find('.chkRow');
                if (parentCheckbox.prop('checked')) {
                    var childTable = $('#child-' + so_id);
                    if (childTable.length) {
                        // Uncheck semua child saat row di-close
                        childTable.find('tbody .childCheckbox').prop('checked', false);
                        $('#checkAllChild_' + so_id).prop('checked', false);
                    }
                }
            } else {
                // Close other open rows
                tableItem.rows().every(function() {
                    if (this.child.isShown()) {
                        this.child.hide();
                        $(this.node()).find('td.details-control i')
                            .removeClass('ri-subtract-line').addClass('ri-add-line');

                        // Reset flag untuk row yang ditutup
                        $(this.node()).data('shouldCheckAllChildren', false);
                    }
                });

                // Open row dengan child row datatable
                var childTableId = 'child-' + so_id;
                var childHtml = `<table id="${childTableId}" class="table table-sm table-bordered w-100">
                            <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
                                <tr class="align-middle">
                                    <th class="text-center">
                                        <input type="checkbox" name="checkAllChild" id="checkAllChild_${so_id}">
                                    </th>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No Transaksi</th>
                                    <th class="text-center">Kode Item</th>
                                    <th class="text-center">Nama Item</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Sisa</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Note</th>
                                </tr>
                            </thead>
                        </table>`;
                row.child(childHtml).show();
                icon.removeClass('ri-add-line').addClass('ri-subtract-line');

                // Simpan referensi tr untuk digunakan di initComplete
                var currentTr = tr;

                // Init DataTable pada child row
                var childTable = $('#' + $.escapeSelector(childTableId)).DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "<?= site_url('do_kny/getDetailSo'); ?>",
                        "type": "POST",
                        "data": {
                            so_id: so_id,
                            customer: customer,
                            storage: storage,
                        }
                    },
                    "columns": [{
                            "data": "checkbox",
                            "className": "text-center",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "no",
                            "className": "text-center",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "no_mr",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "kode_item",
                            "className": "text-center",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "nama_item",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "jumlah",
                            "className": "text-end",
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        },
                        {
                            "data": "sisa",
                            "className": "text-end",
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
                            "data": "note",
                            "render": function(data, type, row) {
                                if (!data) return '';

                                const limit = 30;
                                if (data.length > limit) {
                                    return `<span title="${data.replace(/"/g, '&quot;')}">
                                    ${data.substring(0, limit)}...
                                </span>`;
                                }
                                return data;
                            },
                            createdCell: function(td) {
                                td.style.fontFamily = 'monospace';
                            }
                        }
                    ],
                    "paging": true,
                    "searching": false,
                    "ordering": false,
                    "info": true,
                    "autoWidth": true,
                    "initComplete": function(settings, json) {
                        var childTableElem = $('#' + $.escapeSelector(childTableId));

                        // Setelah data selesai di-load, check semua child jika flag aktif
                        var shouldCheck = currentTr.data('shouldCheckAllChildren') || false;

                        if (shouldCheck) {
                            var childCheckboxes = childTableElem.find('tbody .childCheckbox');
                            var checkAllChild = $('#checkAllChild_' + so_id);

                            // Check semua child dan pastikan enabled
                            childCheckboxes.prop('checked', true).prop('disabled', false);
                            checkAllChild.prop('checked', true).prop('disabled', false);

                            // Reset flag
                            currentTr.data('shouldCheckAllChildren', false);
                        }

                        // Disable child checkbox yang sudah ada di table detail (tapi jangan hide row-nya)
                        childTableElem.find('tbody .childCheckbox').each(function() {
                            var soDetailId = $(this).data('so_detail_id');
                            if (soDetailId && existingSoDetailId.has(String(soDetailId))) {
                                $(this).prop('disabled', true).prop('checked', false);
                            }
                        });
                    },
                    "drawCallback": function() {
                        var childTableElem = $('#' + $.escapeSelector(childTableId));

                        // Tampilkan semua row, disable checkbox yang sudah ada di table detail
                        var hasEnabledChild = false;

                        childTableElem.find('tbody .childCheckbox').each(function() {
                            var soDetailId = $(this).data('so_detail_id');
                            var row = $(this).closest('tr');

                            // Tampilkan semua row
                            row.show();

                            if (soDetailId && existingSoDetailId.has(String(soDetailId))) {
                                // Disable checkbox yang sudah ada di detail, uncheck juga
                                $(this).prop('disabled', true).prop('checked', false);
                            } else {
                                // Enable checkbox yang belum ada di detail
                                $(this).prop('disabled', false);
                                hasEnabledChild = true;
                            }
                        });

                        // Update checkAllChild checkbox state
                        var allChild = childTableElem.find('tbody .childCheckbox:not(:disabled)');
                        var checkedChild = childTableElem.find('tbody .childCheckbox:not(:disabled):checked');
                        $('#checkAllChild_' + so_id).prop('checked', allChild.length > 0 && allChild.length === checkedChild.length);
                    }
                });
            }
        });

        // Handle parent checkbox (chkRow)
        $(document).on('change', '.chkRow', function() {
            var currentCheckbox = $(this);
            var currentRow = currentCheckbox.closest('tr');
            var row = tableItem.row(currentRow);
            var rowData = row.data();
            var currentSoId = rowData && rowData[10] ? rowData[10] : null;

            if (!currentSoId) return; // Exit jika so_id tidak ada

            if (currentCheckbox.prop('checked')) {
                // Uncheck parent checkbox lain (tapi tidak disable)
                $('.chkRow').not(this).prop('checked', false);

                // Disable semua child checkbox dari parent lain
                $('#table-item tbody tr').each(function() {
                    var tr = tableItem.row($(this));
                    var trData = tr.data();
                    var trSoId = trData && trData[10] ? trData[10] : null;
                    if (trSoId && trSoId !== currentSoId) {
                        // Disable child checkbox dari parent lain
                        var childTable = $('#child-' + trSoId);
                        if (childTable.length) {
                            childTable.find('tbody .childCheckbox, #checkAllChild_' + trSoId).prop('disabled', true);
                        }
                    }
                });

                // Enable child checkbox dari parent yang ter-check (kecuali yang sudah ada di table detail)
                var childTableCurrent = $('#child-' + currentSoId);
                if (childTableCurrent.length) {
                    childTableCurrent.find('tbody .childCheckbox').each(function() {
                        var soDetailId = $(this).data('so_detail_id');
                        // Jangan enable jika sudah ada di table detail
                        if (soDetailId && existingSoDetailId.has(String(soDetailId))) {
                            $(this).prop('disabled', true);
                        } else {
                            $(this).prop('disabled', false);
                        }
                    });
                    $('#checkAllChild_' + currentSoId).prop('disabled', false);
                }

                // Expand row jika belum expanded
                if (!row.child.isShown()) {
                    // Set flag sebelum expand
                    currentRow.data('shouldCheckAllChildren', true);
                    currentRow.find('td.details-control').trigger('click');
                } else {
                    // Row sudah expanded, check semua child sekarang (kecuali yang sudah ada di table detail)
                    var childTableElem = $('#child-' + currentSoId);
                    if (childTableElem.length) {
                        var childCheckboxes = childTableElem.find('tbody .childCheckbox');
                        childCheckboxes.each(function() {
                            var soDetailId = $(this).data('so_detail_id');
                            // Jangan check jika sudah ada di table detail
                            if (soDetailId && existingSoDetailId.has(String(soDetailId))) {
                                $(this).prop('checked', false).prop('disabled', true);
                            } else {
                                $(this).prop('checked', true).prop('disabled', false);
                            }
                        });
                        $('#checkAllChild_' + currentSoId).prop('checked', true).prop('disabled', false);
                    }
                }

                // Trigger check semua child untuk row yang sudah expanded sebelumnya
                setTimeout(function() {
                    var childTableElem = $('#child-' + currentSoId);
                    if (childTableElem.length) {
                        var childCheckboxes = childTableElem.find('tbody .childCheckbox');
                        if (childCheckboxes.length > 0) {
                            // Child sudah ada, check semua (kecuali yang sudah ada di table detail)
                            childCheckboxes.each(function() {
                                var soDetailId = $(this).data('so_detail_id');
                                // Jangan check jika sudah ada di table detail
                                if (soDetailId && existingSoDetailId.has(String(soDetailId))) {
                                    $(this).prop('checked', false).prop('disabled', true);
                                } else {
                                    $(this).prop('checked', true).prop('disabled', false);
                                }
                            });
                            $('#checkAllChild_' + currentSoId).prop('checked', true).prop('disabled', false);
                        }
                    }
                }, 500);
            } else {
                // Uncheck semua child
                var childTableElem = $('#child-' + currentSoId);
                if (childTableElem.length) {
                    var childCheckboxes = childTableElem.find('tbody .childCheckbox');
                    childCheckboxes.prop('checked', false);
                    $('#checkAllChild_' + currentSoId).prop('checked', false);
                }

                // Disable semua child checkbox karena tidak ada parent yang ter-check
                $('#table-item tbody tr').each(function() {
                    var tr = tableItem.row($(this));
                    var trData = tr.data();
                    var trSoId = trData && trData[10] ? trData[10] : null;
                    if (trSoId) {
                        var childTable = $('#child-' + trSoId);
                        if (childTable.length) {
                            childTable.find('tbody .childCheckbox, #checkAllChild_' + trSoId).prop('disabled', true);
                        }
                    }
                });
            }
        });

        // Handle child checkbox change - update checkAllChild checkbox state
        $(document).on('change', '.childCheckbox', function() {
            var childCheckbox = $(this);
            var childTable = childCheckbox.closest('table');
            var tableId = childTable.attr('id');

            // Extract so_id from table ID (child-{so_id})
            var soId = tableId.replace('child-', '');

            // Cek apakah ada parent yang ter-check
            var checkedParent = $('#table-item tbody .chkRow:checked').first();
            if (checkedParent.length > 0) {
                var checkedParentRow = tableItem.row(checkedParent.closest('tr'));
                var checkedParentData = checkedParentRow.data();
                var checkedParentSoId = checkedParentData && checkedParentData[10] ? checkedParentData[10] : null;

                // Jika child yang di-check bukan dari parent yang sedang ter-check, uncheck kembali
                if (checkedParentSoId && soId !== checkedParentSoId) {
                    childCheckbox.prop('checked', false);
                    return;
                }
            }

            var allChild = childTable.find('tbody .childCheckbox');
            var checkedChild = childTable.find('tbody .childCheckbox:checked');

            // Update checkAllChild checkbox di header child table
            $('#checkAllChild_' + soId).prop('checked', allChild.length === checkedChild.length);
        });

        // Prevent child checkbox dari parent lain untuk di-check
        $(document).on('click', '.childCheckbox', function(e) {
            var childCheckbox = $(this);
            var childTable = childCheckbox.closest('table');
            var tableId = childTable.attr('id');
            var soId = tableId.replace('child-', '');

            // Cek apakah ada parent yang ter-check
            var checkedParent = $('#table-item tbody .chkRow:checked').first();
            if (checkedParent.length > 0) {
                var checkedParentRow = tableItem.row(checkedParent.closest('tr'));
                var checkedParentData = checkedParentRow.data();
                var checkedParentSoId = checkedParentData && checkedParentData[10] ? checkedParentData[10] : null;

                // Jika child yang akan di-check bukan dari parent yang sedang ter-check, cegah
                if (checkedParentSoId && soId !== checkedParentSoId) {
                    e.preventDefault();
                    e.stopPropagation();
                    childCheckbox.prop('checked', false);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Hanya bisa memilih child dari parent yang sedang ter-check!',
                        timer: 2000
                    });

                    return false;
                }
            }
        });

        // Handle checkAllChild checkbox change - check/uncheck semua child
        $(document).on('change', '[id^="checkAllChild_"]', function() {
            var checkAllCheckbox = $(this);
            var soId = $(this).attr('id').replace('checkAllChild_', '');
            var childTableElem = $('#child-' + soId);

            // Cek apakah ada parent yang ter-check
            var checkedParent = $('#table-item tbody .chkRow:checked').first();
            if (checkedParent.length > 0) {
                var checkedParentRow = tableItem.row(checkedParent.closest('tr'));
                var checkedParentData = checkedParentRow.data();
                var checkedParentSoId = checkedParentData && checkedParentData[10] ? checkedParentData[10] : null;

                // Jika checkAllChild bukan dari parent yang sedang ter-check, cegah
                if (checkedParentSoId && soId !== checkedParentSoId) {
                    checkAllCheckbox.prop('checked', false);

                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Hanya bisa memilih child dari parent yang sedang ter-check!',
                        timer: 2000
                    });

                    return;
                }
            }

            if (childTableElem.length) {
                var isChecked = checkAllCheckbox.prop('checked');
                var childCheckboxes = childTableElem.find('tbody .childCheckbox');
                childCheckboxes.prop('checked', isChecked);
            }
        });

        // Centang semua
        $("#checkAllParent").change(function() {
            $(".chkDetail").prop('checked', $(this).prop('checked'));
        });

        // submit
        $("#btnSubmit").on("click", function(e) {
            e.preventDefault();
            let rowsAdded = false;

            let existingSoDetailId = new Set();
            let existingNoTransaksi = new Set();

            // Kumpulkan so_detail_id dan no_transaksi yang sudah ada di table detail
            tableDetail.rows().every(function() {
                let node = this.node();

                let soDetailId = $(node)
                    .find('input[name="detail[so_detail_id][]"]')
                    .val();
                let noTransaksi = $(node)
                    .find('input[name="detail[no_so][]"]')
                    .val();

                if (soDetailId) existingSoDetailId.add(String(soDetailId));
                if (noTransaksi) existingNoTransaksi.add(noTransaksi);
            });

            // Ambil no_transaksi dari child yang baru dipilih
            let newNoTransaksiSet = new Set();
            $('.childCheckbox:checked').each(function() {
                let childCheckbox = $(this);
                let so_id = childCheckbox.data("so_id");

                // Cari parent row untuk mendapatkan no_transaksi
                let parentRow = $('#table-item tbody tr').filter(function() {
                    var row = tableItem.row($(this));
                    var rowData = row.data();
                    return rowData && rowData[10] == so_id;
                });

                var parentDataTable = tableItem.row(parentRow).data();
                let noTransaksi = parentDataTable ? parentDataTable[5] : '-';
                if (noTransaksi) newNoTransaksiSet.add(noTransaksi);
            });

            // Cek apakah parent baru sama atau berbeda dengan yang sudah ada di table detail
            let hasDifferentParent = false;
            let hasSameParent = false;

            newNoTransaksiSet.forEach(function(newNoTrans) {
                if (existingNoTransaksi.size > 0) {
                    if (existingNoTransaksi.has(newNoTrans)) {
                        // Parent ini sudah ada di table detail
                        hasSameParent = true;
                    } else {
                        // Parent ini berbeda dari yang ada di table detail
                        hasDifferentParent = true;
                    }
                }
            });

            // Hapus data lama HANYA jika:
            // 1. Ada parent baru yang BERBEDA
            // 2. TIDAK ada parent yang SAMA
            // 3. Table detail sudah ada isinya
            if (hasDifferentParent && !hasSameParent && existingNoTransaksi.size > 0) {
                // Hapus semua data lama dari table detail
                tableDetail.clear();
                existingSoDetailId.clear();
                existingNoTransaksi.clear();
            }

            // Ambil data dari child checkbox yang ter-check
            $('.childCheckbox:checked').each(function() {
                let childCheckbox = $(this);
                let so_detail_id = childCheckbox.data("so_detail_id");
                let no_mr = childCheckbox.data("no_mr");
                let so_id = childCheckbox.data("so_id");
                let item_id = childCheckbox.data("item_id");
                let build_id = childCheckbox.data("build_id");
                let kode_item = childCheckbox.data("kode_item");
                let nama_item = childCheckbox.data("nama_item");
                let jumlah = childCheckbox.data("jumlah");
                let base_qty = childCheckbox.data("base_qty");
                let sisa = childCheckbox.data("sisa");
                let satuan = childCheckbox.data("satuan");
                let unit_price = childCheckbox.data("unit_price");
                let subtotal = childCheckbox.data("subtotal");
                let hpp = childCheckbox.data("hpp");
                let harga_input = childCheckbox.data("harga_input");
                let berat = childCheckbox.data("berat");
                let note = childCheckbox.data("note") ?? '';
                let memo = childCheckbox.data("memo") ?? '';
                let po_no = childCheckbox.data("po_no");
                let karyawan_id = childCheckbox.data("karyawan_id");
                let nama_karyawan = childCheckbox.data("karyawan");
                let ppn_code = childCheckbox.data("ppn_code");
                let ppn_percen = childCheckbox.data("ppn_percen");
                let pph_code = childCheckbox.data("pph_code");
                let pph_percen = childCheckbox.data("pph_percen");
                let diskon_price = childCheckbox.data("diskon_price");
                let diskon_persen = childCheckbox.data("diskon_persen");
                let diskon_input = childCheckbox.data("diskon_input");

                // Cari parent row untuk mendapatkan data parent
                let parentRow = $('#table-item tbody tr').filter(function() {
                    var row = tableItem.row($(this));
                    var rowData = row.data();
                    return rowData && rowData[10] == so_id;
                });

                // Ambil data parent dari tableItem
                var parentDataTable = tableItem.row(parentRow).data();
                let no_transaksi = parentDataTable ? parentDataTable[5] : '-'; // No Transaksi ada di index 5

                // ==========================================
                // Cegah duplicate - skip jika sudah ada di table detail
                // ==========================================
                if (so_detail_id && existingSoDetailId.has(String(so_detail_id))) {
                    // Skip data yang sudah ada, hanya uncheck checkbox
                    childCheckbox.prop("checked", false);
                    return;
                }

                // Simpan supaya tidak double dalam 1x submit dan update global variable
                if (so_detail_id) existingSoDetailId.add(String(so_detail_id));

                $('#sales').val(nama_karyawan);
                $('#sales_id').val(karyawan_id);
                $('#so_id').val(so_id);
                $('#ppn_code').val(ppn_code);
                $('#ppn_percen').val(ppn_percen);
                $('#pph_code').val(pph_code);
                $('#pph_percen').val(pph_percen);
                $('#po_customer').val(po_no);

                let rowNode = tableDetail.row.add([
                    "",

                    `
                    <input type="hidden" name="detail[inventory_out_detail_id][]" value="">
                    <input type="hidden" name="detail[no_transaksi][]" value="${no_transaksi}">
                    <input type="hidden" name="detail[so_detail_id][]" value="${so_detail_id}">
                    <input type="hidden" name="detail[build_id][]" value="${build_id}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[base_qty][]" value="${formatNumber(base_qty)}">
                    <input type="hidden" name="detail[unit_price][]" value="${unit_price}">
                    <input type="hidden" name="detail[subtotal][]" value="${subtotal}">
                    <input type="hidden" name="detail[diskon_price][]" value="${diskon_price}">
                    <input type="hidden" name="detail[hpp][]" value="${hpp}">
                    <input type="hidden" name="detail[diskon_persen][]" value="${diskon_persen}">
                    <input type="hidden" name="detail[diskon_input][]" value="${diskon_input}">
                    <input type="hidden" name="detail[harga_input][]" value="${harga_input}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    <input type="hidden" name="detail[balance][]" value="${sisa}">
                    <input type="hidden" name="detail[karyawan_id][]" value="${karyawan_id}">
                    `,

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${no_transaksi}">
                    ${ellipsis(no_transaksi)}
                    </span>
                    <input type="hidden" name="detail[no_so][]" value="${no_transaksi}">
                    `,

                    `<span class="ellipsis" title="${no_mr}">
                    ${ellipsis(no_mr)}
                    </span>
                    <input type="hidden" name="detail[no_transaksi][]" value="${no_mr}">
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

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[memo][]" rows="1" readonly>${memo}</textarea>`,

                    `<span class="view-mode qty-view">${formatNumber(sisa)}</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab" name="detail[jumlah][]" value="${Math.floor(Number(sisa))}" min="0" step="any" data-balance="${Math.floor(Number(sisa))}">`,

                    `<span class="ellipsis" title="${satuan}">
                        ${ellipsis(satuan)}
                    </span>
                    <input type="hidden" name="detail[satuan][]" value="${satuan}">
                    `,

                    `<textarea class="form-control form-control-sm border-0 enter-as-tab" name="detail[keterangan][]" rows="1" readonly">${note}</textarea>`,
                ]).node();

                $(rowNode).addClass('tr-height-30');

                rowsAdded = true;
            });


            if (rowsAdded) {
                tableDetail.draw(false);
                tableDetail.columns.adjust();
                toggleStorageDisabled();

                // Update existingSoDetailId dengan data terbaru dari table detail
                existingSoDetailId.clear();
                tableDetail.rows().every(function() {
                    let node = this.node();
                    let soDetailId = $(node).find('input[name="detail[so_detail_id][]"]').val();
                    if (soDetailId) {
                        existingSoDetailId.add(String(soDetailId));
                    }
                });

                // Disable child checkbox yang sudah ada di table detail
                $('.childCheckbox').each(function() {
                    let so_detail_id = $(this).data("so_detail_id");
                    if (so_detail_id && existingSoDetailId.has(String(so_detail_id))) {
                        $(this).prop('checked', false).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', true); // Disable semua child karena parent di-uncheck
                    }
                });

                // Reset checkAllChild checkbox
                $('[id^="checkAllChild_"]').prop('checked', false).prop('disabled', true);

                // Uncheck parent checkbox
                $('.chkRow:checked').prop('checked', false);
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
                        url: '<?= base_url() ?>do_kny/del',
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

        $(document).on('change', '.uom-select', function() {

            let toQty = $(this).find(':selected').data('to-qty') || 1;

            $(this)
                .closest('tr')
                .find('.uom-to-qty')
                .val(toQty);
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
                $(row).attr('data-do_detail_id', data.do_detail_id);
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
                    className : "ellipsis",
                    render: function(data, type, row) {
                        // if (type === 'display' && data && data.length > 20) {
                        //     let cleanData = data.replace(/"/g, '&quot;'); 
                        //     return `<span title="${cleanData}">
                        //                 ${data.substr(0, 20)}...
                        //             </span>`;
                        // }
                        return data;
                    },
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "kode_item",
                    className : "text-center",
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
                    "data": "do",
                    "className": 'text-end',
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                },
                {
                    "data": "inv",
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
            const infoDetailID = tr.data('do_detail_id');
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
                span.textContent = val.toFixed(2).replace('.', ',');
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
                updateSpan(balance);
            });
            return;
        }

        updateSpan(value);
    });

    // jika jumlah kosong
    document.addEventListener('blur', function(e) {
        if (!e.target.classList.contains('qty-edit')) return;

        const input = e.target;
        const row = $(input).closest("tr");
        const updateSpan = (val) => {
            const span = input.closest('td').querySelector('.qty-view');
            if (span) {
                span.textContent = val.toFixed(2).replace('.', ',');
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
                updateSpan(balance);
            });
            return;
        }
        updateSpan(balance);
    }, true);

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
        $('#location')
            .empty()
            .prop('disabled', true)
            .trigger('change');
        if (!customer) return;

        $.ajax({
            url: "<?= base_url('do_kny/get_location_by_customer') ?>",
            type: "POST",
            data: {
                customer: customer
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    let locations = response.data;
                    $.each(locations, function(i, item) {
                        // let address = `${item.SITE_NAME ?? ''} ${item.ADDRESS1 ?? ''} ${item.CITY ?? ''}`;
                        let selected = "";

                        if (selectedLocation && selectedLocation == item.PERSON_SITE_ID) {
                            selected = "selected";
                        } else if (!selectedLocation && item.PRIMARY_SHIP === "Y") {
                            selected = "selected";
                        }

                        $('#location').append(
                            `<option value="${item.PERSON_SITE_ID}" ${selected}>
                                ${item.SITE_NAME}
                            </option>`
                        );

                        $('#location_id').val(item.PERSON_SITE_ID);

                        $('#address').val((item.ADDRESS1 ?? '') + '\n' + (item.CITY ?? ''));
                    });

                    $('#location')
                        .prop('disabled', true)
                        .trigger('change');
                }
            }
        });
    }
    $(document).on('click','#btn-print', function(){
        setTimeout(function(){
            $('#loading').hide();
        },300);
    });
</script>