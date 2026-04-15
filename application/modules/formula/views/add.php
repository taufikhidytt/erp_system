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
                                            <label for="item_finish_goods">Item Finish Goods:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-stack-fill"></i>
                                                </span>
                                                <select name="item_finish_goods" id="item_finish_goods" class="form-control select2 <?= form_error('item_finish_goods') ? 'is-invalid' : null; ?>">

                                                </select>
                                                <input type="hidden" name="item_description" id="item_description" value="">
                                            </div>
                                            <div class="text-danger"><?= form_error('item_finish_goods') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="satuan" id="label_satuan">Satuan:</label>
                                            <!-- <span class="text-danger">*</span> -->
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
                                                        <input type="text" name="unit" id="unit" class="form-control <?= form_error('unit') ? 'is-invalid' : null; ?>" placeholder="Enter Unit" value="<?= $this->input->post('unit'); ?>">
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
                                                        <input type="text" name="code" id="code" class="form-control <?= form_error('code') ? 'is-invalid' : null; ?>" placeholder="Enter Code" value="<?= $this->input->post('code'); ?>">
                                                    </div>
                                                    <div class="text-danger"><?= form_error('code') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan">Keterangan:</label>
                                            <div class="input-group">
                                                <textarea name="keterangan" id="keterangan" class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>" placeholder="Enter Keterangan"><?= $this->input->post('keterangan'); ?></textarea>
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
                                                <?php date_default_timezone_set('Asia/Jakarta'); ?>
                                                <input type="datetime-local" name="tanggal" id="tanggal" class="form-control <?= form_error('tanggal') ? 'is-invalid' : null; ?>" placeholder="Enter Tanggal" value="<?= $this->input->post('tanggal') ?? date('Y-m-d\TH:i') ?>">
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
                                                <?php date_default_timezone_set('Asia/Jakarta'); ?>
                                                <input type="datetime-local" name="start_date" id="start_date" class="form-control <?= form_error('start_date') ? 'is-invalid' : null; ?>" placeholder="Enter Start Date" value="<?= $this->input->post('start_date') ?>">
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
                                                <?php date_default_timezone_set('Asia/Jakarta'); ?>
                                                <input type="datetime-local" name="end_date" id="end_date" class="form-control <?= form_error('end_date') ? 'is-invalid' : null; ?>" placeholder="Enter End Date" value="<?= $this->input->post('end_date') ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('end_date') ?></div>
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
                                            <label for="status">Status:</label>
                                            <div class="input-group">
                                                <div class="form-check form-switch mb-3" dir="ltr">
                                                    <input type="checkbox" name="status" class="form-check-input" id="customSwitch1" checked>
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
                                    </ul>
                                    <!-- Tab panes -->
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
                                                    <th>Nama Item</th>
                                                    <th>Kode Item</th>
                                                    <th>Jumlah</th>
                                                    <th>Satuan</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
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
                                <th>Kode Item</th>
                                <th>Nama Item</th>
                                <th>Assy Kode</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Stok</th>
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
                    width: "10%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // nama item
                {
                    targets: 3,
                    width: "15%",
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // kode item
                {
                    targets: 4,
                    width: "10%",
                    className: "ellipsis text-end",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // jumlah
                {
                    targets: 5,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer';
                    }
                }, // satuan
                {
                    targets: 6,
                    width: "15%",
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
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
                let nomor = tableDetail.rows().count() + 1;

                let item_id = oldDetail.item_id[i];
                let kode_item = oldDetail.kode_item[i];
                let nama_item = oldDetail.nama_item[i];
                let assy_code = oldDetail.assy_code[i];
                let kategori = oldDetail.kategori[i];
                let satuan = oldDetail.satuan[i];
                let jumlah = oldDetail.jumlah[i] ?? '';
                let brand = oldDetail.brand[i];
                let tipe = oldDetail.tipe[i];
                let jenis_id = oldDetail.jenis_id[i];
                let berat = oldDetail.berat[i] ?? '';
                let keterangan = oldDetail.keterangan[i] ?? '';

                let rowNode = tableDetail.row.add([
                    nomor,

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${nama_item}">
                        ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[assy_code][]" value="${assy_code}">
                    <input type="hidden" name="detail[kategori][]" value="${kategori}">
                    <input type="hidden" name="detail[stok][]" value="${jumlah}">
                    <input type="hidden" name="detail[brand][]" value="${brand}">
                    <input type="hidden" name="detail[tipe][]" value="${tipe}">
                    <input type="hidden" name="detail[jenis_id][]" value="${jenis_id}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    `,

                    `<span class="ellipsis" title="${kode}">
                        ${ellipsis(kode)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode}">
                    `,

                    `<span class="view-mode qty-view">${formatNumber(jumlah)}</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none jumlah" name="detail[jumlah][]" value="${Number(jumlah)}" min="0" step="any">`,

                    `<select class="form-control form-control-sm uom-select border-0 ellipsis" name="detail[satuan][]" title="${satuan}">
                        <option value="">Loading...</option>
                    </select>
                    <input type="hidden" class="form-control form-control-sm" name="detail[base_qty][]">`,

                    `<textarea class="form-control form-control-sm border-0" name="detail[keterangan][]" rows="1" readonly>${keterangan}</textarea>`,
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

        $('#item_finish_goods').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Select Item Finish Goods --',
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: '<?= base_url("formula/get_item_finish_goods_ajax") ?>',
                dataType: 'json',
                delay: 250,

                data: function(params) {
                    return {
                        search: params.term
                    };
                },

                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        $('#item_finish_goods').on('select2:select', function(e) {
            let data = e.params.data;

            $('#item_description').val(data.description);
            $('#item_note').val(data.note);
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

        if ($('#tanggal').val()) {
            let tgl = $('#tanggal').val();

            if (!$('#start_date').val()) {
                $('#start_date').val(tgl);
            }
            $('#start_date').attr('min', tgl);

            if (!$('#end_date').val()) {
                $('#end_date').val(addOneYear($('#start_date').val()));
            }
            $('#end_date').attr('min', $('#start_date').val());
        }

        $('#tanggal').on('change', function() {
            let tgl = $(this).val();

            $('#start_date').val(tgl);
            $('#start_date').attr('min', tgl);

            $('#end_date').val(addOneYear(tgl));
            $('#end_date').attr('min', tgl);
        });

        $('#start_date').on('change', function() {
            let start = $(this).val();
            let tgl = $('#tanggal').val();
            if (start < tgl) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Start Date tidak boleh lebih kecil dari Tanggal',
                })
                $(this).val(tgl);
                start = tgl;
            }
            $('#end_date').attr('min', start);
            if ($('#end_date').val() < start) {
                $('#end_date').val(addOneYear(start));
            }
        });

        $('#end_date').on('change', function() {
            let end = $(this).val();
            let start = $('#start_date').val();

            if (end < start) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'End Date tidak boleh lebih kecil dari Start Date',
                })
                $(this).val(addOneYear(start));
            }
        });

        $('#satuan').prop('disabled', true);

        let initialItem = $('#item_finish_goods').val();
        let oldSatuan = "<?= set_value('satuan') ?>";

        if (initialItem) {
            loadSatuan(initialItem, oldSatuan);
        }
        $('#item_finish_goods').on('change', function() {
            let description = $(this).find(':selected').data('description');
            let note = $(this).find(':selected').data('note');
            $('#item_description').val(description);
            $('#keterangan').val(note);

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
            resetModalItem();
            $("#checkAll").prop('checked', false);
            $('#loading').show();
            var item_finish_goods = $('#item_finish_goods').val();

            if (!item_finish_goods) {
                $('#loading').hide();
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Item tidak terisi, Mohon isi terlebih dahulu',
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
                        response.data.forEach(function(item, i) {

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
                                i + 1,
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

                let itemId = $(node)
                    .find('input[name="detail[item_id][]"]')
                    .val();

                if (itemId) existingItemId.add(String(itemId));
            });

            let allRows = tableItem.rows().nodes();
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

                // ==========================================
                // Cegah duplicate item id
                // ==========================================
                if (item_id && existingItemId.has(item_id)) {
                    checkbox.prop("checked", false).prop("disabled", true);
                    return;
                }

                // Simpan supaya tidak double dalam 1x submit
                if (item_id) existingItemId.add(item_id);

                let rowNode = tableDetail.row.add([
                    "",

                    `<input type="checkbox" class="chkDetail">`,

                    `<span class="ellipsis" title="${nama_item}">
                        ${ellipsis(nama_item)}
                    </span>
                    <input type="hidden" name="detail[nama_item][]" value="${nama_item}">
                    <input type="hidden" name="detail[item_id][]" value="${item_id}">
                    <input type="hidden" name="detail[assy_code][]" value="${assy_code}">
                    <input type="hidden" name="detail[kategori][]" value="${kategori}">
                    <input type="hidden" name="detail[stok][]" value="${jumlah}">
                    <input type="hidden" name="detail[brand][]" value="${brand}">
                    <input type="hidden" name="detail[tipe][]" value="${tipe}">
                    <input type="hidden" name="detail[jenis_id][]" value="${jenis_id}">
                    <input type="hidden" name="detail[berat][]" value="${berat}">
                    `,

                    `<span class="ellipsis" title="${kode_item}">
                        ${ellipsis(kode_item)}
                    </span>
                    <input type="hidden" name="detail[kode_item][]" value="${kode_item}">
                    `,

                    `<span class="view-mode qty-view">1.00</span>
                    <input type="number" class="form-control form-control-sm qty edit-mode qty-edit d-none jumlah" name="detail[jumlah][]" value="1" min="1" step="any">`,

                    `<select class="form-control form-control-sm uom-select border-0 ellipsis" name="detail[satuan][]" title="${satuan}">
                        <option value="">Loading...</option>
                    </select>
                    <input type="hidden" class="form-control form-control-sm" name="detail[base_qty][]">`,

                    `<textarea class="form-control form-control-sm border-0" name="detail[keterangan][]" rows="1" readonly></textarea>`,
                ]).node();

                $(rowNode).addClass('tr-height-30');

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
                        text: 'Item berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        $(document).on('change', '.uom-select', function() {

            let toQty = $(this).find(':selected').data('base_qty') || 1;

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

        // $(document).on('input change', '.jumlah', function() {
        //     let val = $(this).val();
        //     if (val === '') return;

        //     val = parseFloat(val);
        //     if (val < 1) {
        //         $(this).val(1);
        //     }
        // });

        $(document).on('change', '.uom-select', function() {
            let toQty = $(this).find(':selected').data('base_qty') || 1;

            let row = $(this).closest('tr');
            row.find('input[name="detail[base_qty][]"]').val(toQty);
        });
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
            $('#modalKeteranganText').val($(this).val());

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

        const balance = 1.00;

        if (input.value === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Input kosong',
                text: 'Jumlah tidak boleh kosong',
                confirmButtonText: 'OK'
            }).then(() => {
                input.value = balance;
                input.focus();
                updateSpan(balance);
            });
            return;
        }

        // Tidak boleh minus atau nol
        if (input.value <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Jumlah tidak valid',
                text: 'Jumlah harus lebih dari 0',
                confirmButtonText: 'OK'
            }).then(() => {
                input.value = balance;
                input.focus();
                updateSpan(balance);
            });
            return;
        }
    }, true);

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
                        ${v.UOM_CODE}
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

    function loadSatuan(item, selectedSatuan = null) {
        $('#satuan')
            .empty()
            .prop('disabled', true)
            .trigger('change');
        if (!item) return;

        if (item) {
            $.ajax({
                url: "<?= base_url('formula/get_item_uom') ?>",
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
                            .prop('disabled', false)
                            .trigger('change');
                    }
                }
            });
        }
    }

    function addOneYear(dateString) {
        let date = new Date(dateString);

        // tambah 1 tahun
        date.setFullYear(date.getFullYear() + 1);

        // format ke datetime-local (LOCAL TIME, bukan UTC)
        const pad = (n) => String(n).padStart(2, '0');

        let year = date.getFullYear();
        let month = pad(date.getMonth() + 1);
        let day = pad(date.getDate());
        let hours = pad(date.getHours());
        let minutes = pad(date.getMinutes());

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }
</script>