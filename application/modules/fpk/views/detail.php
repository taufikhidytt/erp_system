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

    #table-item {
        table-layout: fixed;
        width: 100%;
    }

    #table-item th,
    #table-item td {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* class untuk text yang mau di-ellipsis */
    .ellipsis {
        white-space: nowrap;
    }

    /* jika ada <span> / <a> di dalam cell */
    #table-item td span,
    #table-item td a {
        display: inline-block;
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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
                                <a href="<?= base_url('fpk') ?>" class="text-decoration-underline">FPK</a>
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
                                    <a href="<?= base_url('fpk/add') ?>" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Tambah">
                                        <i class="ri ri-add-box-fill"></i>
                                    </a>
                                    <button type="submit" class="btn btn-success btn-sm" name="submit" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan">
                                        <i class="ri ri-check-double-fill"></i>
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.replace(window.location.pathname);" data-toggle="tooltip" data-placement="bottom" title="Undo">
                                        <i class="ri ri-eraser-fill"></i>
                                    </button>
                                    <a href="<?= base_url('fpk') ?>" class="btn btn-sm btn-secondary" data-toggle="tooltip" data-placement="bottom" title="Kembali">
                                        <i class="ri ri-reply-fill"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <input type="hidden" name="pr_id" id="pr_id" value="<?= $this->encrypt->encode($data->PR_ID); ?>">
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
                                            <label for="no_referensi">No Referensi:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-pantone-line"></i>
                                                </span>
                                                <input type="text" name="no_referensi" id="no_referensi" class="form-control <?= form_error('no_referensi') ? 'is-invalid' : null; ?>" placeholder="Enter No Referensi" value="<?= $this->input->post('no_referensi') ?? $data->DOCUMENT_REFF_NO; ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('no_referensi') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="supplier">Supplier:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-pantone-line"></i>
                                                </span>
                                                <select name="supplier" id="supplier" class="form-control select2 <?= form_error('supplier') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Supplier --</option>
                                                    <?php $params = $this->input->post('supplier') ?? $data->PERSON_ID; ?>
                                                    <?php foreach ($supplier->result() as $sp): ?>
                                                        <option value="<?= $sp->PERSON_ID ?>" <?= $sp->PERSON_ID == $params ? 'selected' : null ?>>
                                                            <?= strtoupper($sp->Supplier) . ' - [' . strtoupper($sp->Kode) . ']' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('supplier') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="row">
                                            <div class="col-lg-6 col-md-6 col-sm-12">
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
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12">
                                                <div class="mb-3">
                                                    <label for="tanggal_dibutuhkan">Tanggal Dibutuhkan:</label>
                                                    <span class="text-danger">*</span>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            <i class="ri ri-calendar-event-fill"></i>
                                                        </span>
                                                        <input type="datetime-local" name="tanggal_dibutuhkan" id="tanggal_dibutuhkan" class="form-control <?= form_error('tanggal_dibutuhkan') ? 'is-invalid' : null; ?>" placeholder="Enter Tanggal Dibutuhkan" value="<?= $this->input->post('tanggal_dibutuhkan') ?? $data->NEED_DATE; ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('tanggal_dibutuhkan') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="gudang">Gudang:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-home-gear-fill"></i>
                                                </span>
                                                <?php
                                                $defaultValue = null;
                                                foreach ($gudang->result() as $gd) {
                                                    if ($gd->PRIMARY_FLAG == 'Y') {
                                                        $defaultValue = $gd->WAREHOUSE_ID;
                                                        break;
                                                    }
                                                }
                                                ?>
                                                <select name="gudang" id="gudang" class="form-control select2 <?= form_error('gudang') ? 'is-invalid' : null; ?>">
                                                    <?php if (!$defaultValue): ?>
                                                        <option value="">-- Selected Gudang --</option>
                                                    <?php endif; ?>
                                                    <?php $param = $this->input->post('gudang') ?? $data->WAREHOUSE_ID; ?>
                                                    <?php foreach ($gudang->result() as $gd): ?>
                                                        <option
                                                            value="<?= $gd->WAREHOUSE_ID ?>"
                                                            <?= $gd->WAREHOUSE_ID == $param ? 'selected' : ($defaultValue == $gd->WAREHOUSE_ID ? 'selected' : '') ?>>
                                                            <?= strtoupper($gd->WAREHOUSE_NAME) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('gudang') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="sales">Sales:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-2-fill"></i>
                                                </span>
                                                <select name="sales" id="sales" class="form-control select2 <?= form_error('sales') ? 'is-invalid' : null; ?>">
                                                    <option value="">-- Selected Sales --</option>
                                                    <?php $param = $this->input->post('sales') ?? $data->KARYAWAN_ID; ?>
                                                    <?php foreach ($sales->result() as $sl): ?>
                                                        <option value="<?= $sl->KARYAWAN_ID ?>" <?= $sl->KARYAWAN_ID == $param ? 'selected' : null ?>>
                                                            <?= strtoupper($sl->FIRST_NAME) . ' - [' . strtoupper($sl->LAST_NAME) . ']' ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="text-danger"><?= form_error('sales') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-xs">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="mb-3">
                                        <label for="keterangan">Keterangan:</label>
                                        <div class="input-group">
                                            <textarea name="keterangan" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"><?= $this->input->post('keterangan') ?? $data->NOTE; ?></textarea>
                                        </div>
                                        <div class="text-danger"><?= form_error('keterangan') ?></div>
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
                                            <button type="button" id="removeRow" class="btn btn-danger btn-sm" style="width: 30px;">-</button>
                                            <button type="button" id="btn-modalItem" class="btn btn-success btn-sm">
                                                <i class="ri ri-command-fill"></i> Browse Item
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="table-detail">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th></th>
                                                    <th>
                                                        <input type="checkbox" name="checkAllParent" id="checkAllParent" class="">
                                                    </th>
                                                    <th>Nama Item</th>
                                                    <th>Kode Item</th>
                                                    <th>Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>Harga Input</th>
                                                    <th>Harga</th>
                                                    <th>Subtotal</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $dataDetail = $this->db->query("SELECT pr_detail.*, item.ITEM_CODE FROM pr_detail JOIN item ON item.ITEM_ID = pr_detail.ITEM_ID WHERE pr_detail.PR_ID = {$data->PR_ID}");
                                                if ($dataDetail->num_rows() > 0) { ?>
                                                    <?php
                                                    $no = 1;
                                                    foreach ($dataDetail->result() as $dd): ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td>
                                                                <input type="hidden" name="detail[pr_detail][]" id="pr_detail" value="<?= $this->encrypt->encode($dd->PR_DETAIL_ID); ?>">
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" class="chkDetail">
                                                            </td>
                                                            <td>
                                                                <?= $dd->ITEM_DESCRIPTION ?>
                                                                <input type="hidden" name="detail[nama_item][]" value="<?= $dd->ITEM_DESCRIPTION ?>">
                                                                <input type="hidden" name="detail[id_item][]" value="<?= $dd->ITEM_ID ?>">
                                                            </td>
                                                            <td>
                                                                <?= $dd->ITEM_CODE ?>
                                                                <input type="hidden" name="detail[kode_item][]" value="<?= $dd->ITEM_CODE ?>">
                                                            </td>
                                                            <td>
                                                                <span class="view-mode qty-view"><?= number_format(rtrim(rtrim($dd->ENTERED_QTY, '0'), '.'), 2, '.', ','); ?></span>
                                                                <input type="number" class="form-control form-control-sm qty auto-width edit-mode qty-edit d-none enter-as-tab" name="detail[qty][]" value="<?= rtrim(rtrim($this->input->post('detail[qty][]') ?? rtrim(rtrim($dd->ENTERED_QTY, '0'), '.'), '0'), '.'); ?>">
                                                            </td>
                                                            <td>
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

                                                                <select class="form-control form-control-sm uom-select auto-width border-0" name="detail[uom][]">
                                                                    <?php
                                                                    $param = $this->input->post('detail[uom][]') ?? $dd->ENTERED_UOM;
                                                                    foreach ($data_uom_selected->result() as $dus): ?>
                                                                        <option value="<?= $dus->UOM_CODE ?>" data-to_qty="<?= $dus->TO_QTY ?>" <?= $param == $dus->UOM_CODE ? 'selected' : NULL ?>><?= $dus->UOM_CODE ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <input type="hidden" class="form-control form-control-sm to-qty" name="detail[to_qty][]" value="">
                                                            </td>
                                                            <td>
                                                                <span class="view-mode harga-view"><?= number_format(rtrim(rtrim($dd->HARGA_INPUT, '0'), '.'), 2, '.', ','); ?></span>
                                                                <input type="number"
                                                                    class="form-control form-control-sm harga-input auto-width edit-mode harga-edit d-none enter-as-tab"
                                                                    name="detail[harga_input][]"
                                                                    value="<?= rtrim(rtrim($dd->HARGA_INPUT, '0'), '.') ?>">
                                                            </td>
                                                            <td>
                                                                <span class="harga-input-b">
                                                                    <?= number_format(rtrim(rtrim($dd->UNIT_PRICE, '0'), '.'), 2, '.', ','); ?>
                                                                </span>
                                                                <input type="hidden" name="detail[harga][]" value="<?= $this->input->post('harga') ?? rtrim(rtrim($dd->UNIT_PRICE, '0'), '.'); ?>">
                                                            </td>
                                                            <td>
                                                                <span class="subtotal-text">
                                                                    <?= number_format(rtrim(rtrim($dd->SUBTOTAL, '0'), '.'), 2, '.', ','); ?>
                                                                </span>
                                                                <input type="hidden" name="detail[subtotal][]" value="<?= $this->input->post('subtotal') ?? rtrim(rtrim($dd->SUBTOTAL, '0'), '.'); ?>">
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm auto-width border-0 enter-as-tab" name="detail[keterangan][]" value="<?= $this->input->post('detail[keterangan][]') ?? $dd->NOTE; ?>">
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
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <h6 class="fw-bold">Total</h6>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8">
                                    <p id="total-text">0.00</p>
                                    <input type="hidden" name="total" id="total" value="<?= $this->input->post('total') ?? $data->TOTAL_AMOUNT; ?>">
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

<!-- modal -->
<div id="modalItem" class="modal fade" style="font-size: 12px;">
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
                                <th>Nama Item</th>
                                <th>Kode Item</th>
                                <th>Stock</th>
                                <th>Satuan</th>
                                <th>Kelompok</th>
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

<script>
    $(document).ready(function() {
        var tableDetail = $('#table-detail').DataTable({
            ordering: false
        });

        var tableItem = $('#table-item').DataTable({
            autoWidth: false,
            "columnDefs": [{
                "targets": [0, 1],
                "orderable": false
            }],
            columnDefs: [{
                    targets: 0,
                    width: "5%"
                }, // checkbox
                {
                    targets: 1,
                    width: "5%"
                }, // no
                {
                    targets: 2,
                    width: "25%",
                    className: "eclipse",
                    render: function(data) {
                        if (!data) return '-';
                        return `<span title="${data}">${data}</span>`;
                    }
                }, // item description
                {
                    targets: 3,
                    width: "20%",
                    className: "eclipse",
                    render: function(data) {
                        if (!data) return '-';
                        return `<span title="${data}">${data}</span>`;
                    }
                }, // item code
                {
                    targets: 4,
                    width: "10%",
                    className: "eclipse",
                    render: function(data) {
                        if (!data) return '-';
                        return `<span title="${data}">${data}</span>`;
                    }
                }, // stok
                {
                    targets: 5,
                    width: "10%",
                    className: "eclipse",
                    render: function(data) {
                        if (!data) return '-';
                        return `<span title="${data}">${data}</span>`;
                    }
                }, // uom
                {
                    targets: 6,
                    width: "25%",
                    className: "eclipse",
                    render: function(data) {
                        if (!data) return '-';
                        return `<span title="${data}">${data}</span>`;
                    }
                }, // tipe
            ],
            autoWidth: false,
            paging: true,
            searching: true,
            ordering: false,
            "order": []
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

        $("#supplier").data("prev", $("#supplier").val());

        $("#supplier").on("change", function(e, data) {
            let prev = $(this).data("prev");
            let current = $(this).val();

            if (prev && current !== prev && tableDetail.rows().count() > 0) {

                Swal.fire({
                    title: "Ganti Supplier?",
                    text: "Data item yang sudah dipilih akan dihapus.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, ganti",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        tableDetail.clear().draw();
                        $(this).data("prev", current);
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
        $("#btn-modalItem").on("click", function() {
            $("#checkAll").prop('checked', false);
            $('#loading').show();
            var supplier = $('#supplier').val();

            if (!supplier) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Supplier tidak terisi, Mohon isi supplier terlebih dahulu',
                });
                return;
            }
            $.ajax({
                type: "POST",
                url: "<?= base_url() ?>fpk/getItemBySupplier",
                data: {
                    id_supplier: supplier,
                },
                dataType: "json",
                success: function(response) {
                    $('#loading').hide();
                    tableItem.clear().draw();
                    if (response.status === 'success' && Array.isArray(response.data)) {
                        response.data.forEach(function(item, i) {

                            var stok = parseFloat(item.STOK).toFixed(2);

                            var checkbox = `
                            <input type="checkbox" class="chkRow"
                                data-id_item="${item.ITEM_ID}"
                                data-name="${item.ITEM_DESCRIPTION}"
                                data-code="${item.ITEM_CODE}"
                                data-uom="${item.UOM}"
                            >
                            `;
                            tableItem.row.add([
                                checkbox,
                                i + 1,
                                item.ITEM_DESCRIPTION,
                                item.ITEM_CODE,
                                stok,
                                item.UOM,
                                item.TIPE,
                            ]).draw();
                        });
                    }
                    $('#modalTitleForm').text('Master Item');
                    $('#modalItem').modal('show');
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
        $("#btnSubmit").on("click", function() {
            let rowsAdded = false;
            $("#table-item .chkRow:checked").each(function() {
                let id_item = $(this).data("id_item");
                let nama = $(this).data("name");
                let kode = $(this).data("code");
                let uom = $(this).data("uom");

                let exists = tableDetail
                    .column(3)
                    .data()
                    .toArray()
                    .includes(kode);
                if (exists) return;

                let rowNode = tableDetail.row.add([
                    "",

                    `<input type="hidden" name="detail[pr_detail][]" value="">`,

                    `<input type="checkbox" class="chkDetail">`,

                    `${nama}
                    <input type="hidden" name="detail[nama_item][]" value="${nama}">
                    <input type="hidden" name="detail[id_item][]" value="${id_item}">`,

                    `${kode}
                    <input type="hidden" name="detail[kode_item][]" value="${kode}">`,

                    `<span class="view-mode qty-view">0.00</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none enter-as-tab" name="detail[qty][]" value="1">`,

                    `<select class="form-control form-control-sm uom-select border-0" name="detail[uom][]">
                        <option value="">Loading...</option>
                    </select>
                    <input type="hidden" class="form-control form-control-sm to-qty" name="detail[to_qty][]">`,

                    `<span class="view-mode harga-view">0.00</span>
                    <input type="number" class="form-control form-control-sm harga-input auto-width edit-mode harga-edit d-none enter-as-tab" name="detail[harga_input][]">`,

                    `<span class="harga-input-b">0.00</span>
                    <input type="hidden" name="detail[harga][]">`,

                    `<span class="subtotal-text">0.00</span>
                    <input type="hidden" name="detail[subtotal][]">`,

                    `<input type="text" class="form-control form-control-sm auto-width border-0 enter-as-tab" name="detail[keterangan][]">`
                ]).draw(false).node();

                rowsAdded = true;
                loadUom($(rowNode), id_item);
            });


            if (rowsAdded) {
                tableDetail.draw(false);
            }
            $("#modalItem").modal("hide");
        });

        $(document).on("click", ".view-mode", function() {
            let span = $(this);
            let input = span.next(".edit-mode");

            span.addClass("d-none");
            input.removeClass("d-none").focus();
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

        hitungTotal();

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
            hitungTotal();
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
            rowsToRemove.each(function() {
                tableDetail.row(this).remove();
            });
            tableDetail.draw(false);

            $("#checkAllParent").prop("checked", false);
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

        $(document).on('input change', '.qty, .harga-input', function() {
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
            $row.find('input[name="detail[keterangan][]"]').focus();
            return;
        }
    });

    function hitungTotal() {
        let total = 0;

        $("tr").each(function() {
            let subtotalText = $(this).find(".subtotal-text").text();
            let subtotal = Number(subtotalText.replace(/,/g, '')) || 0;
            total += subtotal;
        });

        let totalDisplay = (total === 0) ?
            '0' :
            total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        $("#total-text").text(totalDisplay);
        $("#total").val(total);
    }

    function loadUom(row, itemId) {

        let uomSelect = row.find('.uom-select');
        let toQtyInput = row.find('input[name="detail[to_qty][]"]');

        $.ajax({
            url: "<?= base_url() ?>fpk/get_uom",
            type: 'POST',
            dataType: 'json',
            data: {
                item_id: itemId
            },
            success: function(res) {
                let html = '';

                $.each(res.data, function(i, v) {
                    html += `
                    <option value="${v.UOM_CODE}" data-to_qty="${v.TO_QTY}">
                        ${v.UOM_CODE}
                    </option>
                `;
                });

                uomSelect.html(html);
                uomSelect.trigger('change');
            }
        });
    }

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
</script>