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

    /* Paksa kolom DataTables untuk patuh pada lebar yang ditentukan tanpa mempedulikan isi bodynya */
    #table-detail_wrapper .dataTables_wrapper table { table-layout: fixed !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(1), #table-detail_wrapper .dataTables_wrapper table td:nth-child(1) { width: 50px !important; min-width: 50px !important; max-width: 50px !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(3), #table-detail_wrapper .dataTables_wrapper table td:nth-child(3) { width: 40px !important; min-width: 40px !important; max-width: 40px !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(4), #table-detail_wrapper .dataTables_wrapper table td:nth-child(4) { width: 150px !important; min-width: 150px !important; max-width: 150px !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(5), #table-detail_wrapper .dataTables_wrapper table td:nth-child(5) { width: 200px !important; min-width: 200px !important; max-width: 200px !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(6), #table-detail_wrapper .dataTables_wrapper table td:nth-child(6) { width: 100px !important; min-width: 100px !important; max-width: 100px !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(7), #table-detail_wrapper .dataTables_wrapper table td:nth-child(7) { width: 80px !important; min-width: 80px !important; max-width: 80px !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(8), #table-detail_wrapper .dataTables_wrapper table td:nth-child(8) { width: 150px !important; min-width: 150px !important; max-width: 150px !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(9), #table-detail_wrapper .dataTables_wrapper table td:nth-child(9) { width: 150px !important; min-width: 150px !important; max-width: 150px !important; }
    #table-detail_wrapper .dataTables_wrapper table th:nth-child(10), #table-detail_wrapper .dataTables_wrapper table td:nth-child(10) { width: 100px !important; min-width: 100px !important; max-width: 100px !important; }

    .keterangan-view {
        white-space: pre-line;
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
                                <a href="<?= base_url('lov') ?>" class="text-decoration-underline">Daftar Nilai</a>
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
                            <div id="deleted-detail-container"></div>
                            <div class="row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center gap-2 label-status">
                                    <h5 style="width: 100px;" id="readonlyPR"></h5>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <?= button_actions([
                                        'save',
                                        'reload',
                                        [
                                            'key'          => 'log_info',
                                            'class'        => 'btn-info btn-log-info',
                                            'title'        => 'Log & History',
                                            'icon'         => 'ri-question-line',
                                            'data-param'   => base64_encode($this->encrypt->encode(json_encode([
                                                'h' => [
                                                    't' => 'erp_lookup_set',
                                                    'w' => ['ERP_LOOKUP_SET_ID' => $data->ERP_LOOKUP_SET_ID]
                                                ],
                                                'where' => [
                                                    'TABLE_NAME'    => 'ERP_LOOKUP_VALUE',
                                                    'ORDER_ID'      => $data->ERP_LOOKUP_SET_ID
                                                ],
                                                'joins' => [
                                                    ['erp_lookup_value t1','t1.ERP_LOOKUP_VALUE_ID = a.ID','left']
                                                ],
                                                'select' => 't1.DISPLAY_NAME as text'
                                            ]))),
                                        ],
                                    ]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <input type="hidden" name="erp_lookup_set_id" id="erp_lookup_set_id" value="<?= $this->encrypt->encode($data->ERP_LOOKUP_SET_ID); ?>">
                                            <label for="nama_master">Nama Master:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text"
                                                    name="nama_master"
                                                    id="nama_master"
                                                    class="form-control <?= form_error('nama_master') ? 'is-invalid' : null; ?>"
                                                    placeholder="Enter Nama Master"
                                                    value="<?= $this->input->post('nama_master') ?? $data->ERP_LOOKUP_SET_NAME; ?>"
                                                    readonly>
                                            </div>
                                            <div class="text-danger">
                                                <?= form_error('nama_master') ?>
                                            </div>
                                        </div>
                                        <div class="form-check mt-2 d-none d-lg-block">
                                            <input type="checkbox"
                                                name="edit_flag"
                                                class="form-check-input"
                                                id="edit_flag"
                                                <?= set_value('edit_flag', $data->USER_CAN_EDIT_FLAG ?? 'N') === 'Y' ? 'checked' : '' ?>
                                                disabled
                                                style="width: 17px; height: 17px;">
                                            <label class="form-check-label">
                                                &nbsp; Bisa Edit
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="keterangan">Keterangan:</label>
                                            <div class="input-group">
                                                <textarea name="keterangan"
                                                    id="keterangan"
                                                    class="form-control <?= form_error('keterangan') ? 'is-invalid' : null ?>"
                                                    placeholder="Enter Keterangan"><?= $this->input->post('keterangan') ?? $data->DESCRIPTION; ?></textarea>
                                            </div>
                                            <div class="text-danger">
                                                <?= form_error('keterangan') ?>
                                            </div>
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
                                            <div class="mb-3">
                                                <button type="button" id="removeRow" class="btn btn-danger btn-sm" style="width: 55px;height:29px">
                                                    <i class="fa fa-trash"></i> Del
                                                </button>
                                                <button type="button" id="btn-modalItem" class="btn btn-success btn-sm" style="height:29px">
                                                    <i class="ri ri-add-box-fill"></i> Add
                                                </button>
                                            </div>

                                            <table class="table table-striped table-sm w-100" id="table-detail">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th style=" padding:0; margin:0; border:none; display: none;"></th>
                                                        <th></th>
                                                        <?php 
                                                        for ($i=3; $i <=9 ; $i++) { ?>
                                                            <th>
                                                                <?php if(in_array($i,[5,9])){ ?>
                                                                    <select class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;min-height:35.66px">
                                                                        <option value="">All</option>
                                                                        <option value="Y">✔</option>
                                                                        <option value="N">✖</option>
                                                                    </select>
                                                                <?php } else{ ?>
                                                                    <input type="text" placeholder="Cari.." class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                                <?php } ?>
                                                            </th>
                                                        <?php } ?>
                                                    </tr>
                                                    <tr style="background: #3d7bb9; z-index: 10; color: #ffff">
                                                        <th>No</th>
                                                        <th style=" padding:0; margin:0; border:none; display: none;"></th>
                                                        <th>
                                                            <input type="checkbox" name="checkAllParent" id="checkAllParent">
                                                        </th>
                                                        <th>Nilai</th>
                                                        <th>Note</th>
                                                        <th>Default</th>
                                                        <th>Urutan</th>
                                                        <th>Sejak Tanggal</th>
                                                        <th>Sampai Tanggal</th>
                                                        <th>Aktif</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $dataDetail = $this->db->query("SELECT 
                                                    erp_lookup_value.ERP_LOOKUP_VALUE_ID,
                                                    erp_lookup_value.ERP_LOOKUP_SET_ID,
                                                    erp_lookup_value.PROGRAM_CODE1,
                                                    erp_lookup_value.DISPLAY_NAME,
                                                    erp_lookup_value.DESCRIPTION,
                                                    erp_lookup_value.SEQ,
                                                    erp_lookup_value.START_DATE,
                                                    erp_lookup_value.END_DATE,
                                                    erp_lookup_value.PRIMARY_FLAG,
                                                    erp_lookup_value.ITEM_FLAG,
                                                    erp_lookup_value.TOTAL_FLAG,
                                                    erp_lookup_value.DISKON_FLAG,
                                                    erp_lookup_value.ACTIVE_FLAG,
                                                    erp_lookup_value.MENU_ICON,
                                                    erp_lookup_value.CREATED_BY,
                                                    erp_lookup_value.CREATED_DATE,
                                                    erp_lookup_value.LAST_UPDATE_BY
                                                    FROM erp_lookup_value JOIN erp_lookup_set ON erp_lookup_set.ERP_LOOKUP_SET_ID = erp_lookup_value.ERP_LOOKUP_SET_ID WHERE erp_lookup_value.ERP_LOOKUP_SET_ID = {$data->ERP_LOOKUP_SET_ID} ORDER BY erp_lookup_value.SEQ ASC");

                                                    if ($dataDetail->num_rows() > 0) { ?>
                                                        <?php
                                                        $no = 1;
                                                        $limit = 20;
                                                        $postDetail = $this->input->post('detail');
                                                        $i = 0;
                                                        foreach ($dataDetail->result() as $dd): ?>
                                                            <tr class=" tr-height-30">
                                                                <td><?= $no++ ?></td>
                                                                <td style="display: none;">
                                                                    <input type="hidden" name="detail[erp_lookup_value_id][]" value="<?= $this->encrypt->encode($dd->ERP_LOOKUP_VALUE_ID); ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="checkbox" class="chkDetail">
                                                                </td>
                                                                <td class="ellipsis editable-cell" data-field="nilai">
                                                                    <span class="ellipsis align-middle view-mode" data-toggle="tooltip" data-placement="bottom" title="<?= html_escape($dd->DISPLAY_NAME) ?>">
                                                                        <?= html_escape($dd->DISPLAY_NAME); ?>
                                                                    </span>
                                                                    <input type="text" name="detail[nilai][]" class="form-control form-control-sm edit-mode d-none" value="<?= html_escape($dd->DISPLAY_NAME) ?>" oninput="this.value = this.value.toUpperCase()">
                                                                    <input type="hidden" name="detail[program_code1][]" value="<?= html_escape($dd->PROGRAM_CODE1) ?>">
                                                                </td>
                                                                <td class="ellipsis editable-cell" data-field="description">
                                                                    <span class="ellipsis align-middle view-mode" data-toggle="tooltip" data-placement="bottom" title="<?= html_escape($dd->DESCRIPTION) ?>">
                                                                        <?= html_escape($dd->DESCRIPTION); ?>
                                                                    </span>
                                                                    <input type="text" name="detail[description][]" class="form-control form-control-sm edit-mode d-none" value="<?= html_escape($dd->DESCRIPTION) ?>" oninput="this.value = this.value.toUpperCase()">
                                                                </td>
                                                                <td class="ellipsis editable-cell text-center" data-field="primary_flag">
                                                                    <span class="ellipsis align-middle view-mode">
                                                                        <?= $dd->PRIMARY_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Default" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No Default" data-bs-toggle="tooltip" data-bs-placement="left"></i>' ?>
                                                                    </span>
                                                                    <div class="edit-mode d-none">
                                                                        <input type="hidden" name="detail[primary_flag][]" value="<?= $dd->PRIMARY_FLAG == 'Y' ? 'Y' : 'N' ?>">
                                                                        <input type="checkbox" class="form-check-input flag-checkbox default-checkbox" <?= $dd->PRIMARY_FLAG == 'Y' ? 'checked' : '' ?>>
                                                                    </div>
                                                                </td>
                                                                <td class="ellipsis editable-cell" data-field="urutan">
                                                                    <span class="ellipsis align-middle view-mode">
                                                                        <?= html_escape($dd->SEQ); ?>
                                                                    </span>
                                                                    <input type="number" name="detail[urutan][]" class="form-control form-control-sm edit-mode d-none" value="<?= html_escape($dd->SEQ) ?>">
                                                                </td>
                                                                <td class="ellipsis editable-cell" data-field="start_date">
                                                                    <span class="ellipsis align-middle view-mode">
                                                                        <?= $dd->START_DATE ? date('d M Y', strtotime($dd->START_DATE)) : '' ?>
                                                                    </span>
                                                                    <input type="date" name="detail[start_date][]" class="form-control form-control-sm edit-mode d-none" value="<?= $dd->START_DATE ? date('Y-m-d', strtotime($dd->START_DATE)) : '' ?>">
                                                                </td>
                                                                <td class="ellipsis editable-cell" data-field="end_date">
                                                                    <span class="ellipsis align-middle view-mode">
                                                                        <?= $dd->END_DATE ? date('d M Y', strtotime($dd->END_DATE)) : '' ?>
                                                                    </span>
                                                                    <input type="date" name="detail[end_date][]" class="form-control form-control-sm edit-mode d-none" value="<?= $dd->END_DATE ? date('Y-m-d', strtotime($dd->END_DATE)) : '' ?>">
                                                                </td>
                                                                <td class="ellipsis editable-cell text-center" data-field="active_flag">
                                                                    <span class="ellipsis align-middle view-mode">
                                                                        <?= $dd->ACTIVE_FLAG == 'Y' ? '<i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="Inactive" data-bs-toggle="tooltip" data-bs-placement="left"></i>' ?>
                                                                    </span>
                                                                    <div class="edit-mode d-none">
                                                                        <input type="hidden" name="detail[active_flag][]" value="<?= $dd->ACTIVE_FLAG == 'Y' ? 'Y' : 'N' ?>">
                                                                        <input type="checkbox" class="form-check-input flag-checkbox" <?= $dd->ACTIVE_FLAG == 'Y' ? 'checked' : '' ?>>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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

<script>
    let tableDetail;
    const userCanEditMaster = <?= json_encode(($data->USER_CAN_EDIT_FLAG ?? 'N') === 'Y') ?>;
    const defaultProgramCode1 = <?= json_encode($data->PROGRAM_CODE ?? '') ?>;

    $(document).ready(function() {
        tableDetail = $('#table-detail').DataTable({
            dom: 'rtip',
            ordering: false,
            autoWidth: false,
            paging: false,
            scrollY: 450,
            scrollX: true,
            columnDefs: [
                {
                    targets: 0,
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    },
                    "orderable": false,
                    "searchable": false,
                    className : "text-center"
                }, // no
                {
                    targets: 2,
                    "orderable": false,
                    "searchable": false,
                    className : "text-center"
                }, // checkbox
                {
                    targets: 3,
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // nilai
                {
                    targets: 4,
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // note
                {
                    targets: 5,
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer'
                    },
                    render: function (data, type, row, meta) {
                        if (type === 'filter') {
                            let match = data.match(/value="([YN])"/);
                            return match ? match[1] : '';
                        }
                        return data;
                    }
                }, // default
                {
                    targets: 6,
                    className: "ellipsis",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // urutan
                {
                    targets: 7,
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                        td.style.cursor = 'pointer'
                    }
                }, // sejak tanggal
                {
                    targets: 8,
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    }
                }, // sampai tanggal
                {
                    targets: 9,
                    className: "ellipsis text-center",
                    createdCell: function(td) {
                        td.style.fontFamily = 'monospace';
                    },
                    render: function (data, type, row, meta) {
                        if (type === 'filter') {
                            let match = data.match(/value="([YN])"/);
                            return match ? match[1] : '';
                        }
                        return data;
                    }
                }, // aktif
            ],
        });

        $('.column_search').on('input change', function() {
            let searchVal = this.value;
            let isExact = $(this).is('select') ? true : false;
            let finalSearch = (isExact && searchVal !== '') ? "^" + searchVal + "$" : searchVal;
            tableDetail
                .column($(this).data('column'))
                .search(finalSearch, isExact, false)
                .draw();
        });
        setTimeout(() => {
            $('.column_search:first').trigger('input');
        }, 100);

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

        tableDetail.on("draw.dt", function() {
            tableDetail
                .column(0)
                .nodes()
                .each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
        });

        syncEditFlagCheckboxes();
        applyEditState();

        $("#checkAllParent").on("change", function() {
            $("#table-detail .chkDetail").prop("checked", $(this).is(":checked"));
        });

        $('#edit_flag, #edit_flag_mobile').on('change', function() {
            $('#edit_flag, #edit_flag_mobile').prop('checked', $(this).is(':checked'));
            hideAllEditors();
            applyEditState();
        });

        $(document).on("click", "#table-detail tbody td.editable-cell", function(e) {
            if (!isDetailEditable() || $(e.target).is("input, select, textarea, option, .edit-mode, .edit-mode *")) return;

            let td = $(this);
            let span = td.find(".view-mode").first();
            let editor = td.find(".edit-mode").first();

            if (span.length && editor.length) {
                hideAllEditors(td);
                span.addClass("d-none");
                editor.removeClass("d-none").prop("disabled", false);

                const input = editor.is("input, select, textarea") ? editor : editor.find("input, select, textarea").filter(":visible").first();
                input.prop("disabled", false).focus();

                if (input.is("input[type='text'], input[type='number'], input[type='date']")) {
                    input.select();
                }
            }
        });

        $(document).on("blur change", "#table-detail .edit-mode", function() {
            let input = $(this);
            updateCellView(input);
            input.addClass("d-none");
            input.closest("td").find(".view-mode").first().removeClass("d-none");
        });

        $(document).on("change", "#table-detail .flag-checkbox", function() {
            const checkbox = $(this);
            const editor = checkbox.closest(".edit-mode");

            editor.find('input[type="hidden"]').val(checkbox.is(":checked") ? "Y" : "N");

            if (checkbox.hasClass("default-checkbox") && checkbox.is(":checked")) {
                $("#table-detail .default-checkbox").not(checkbox).prop("checked", false).each(function() {
                    const otherEditor = $(this).closest(".edit-mode");
                    otherEditor.find('input[type="hidden"]').val("N");
                    updateCellView(otherEditor);
                });
            }

            updateCellView(editor);
        });

        $(document).on("change", ".chkDetail", function() {
            let total = $("#table-detail .chkDetail").length;
            let checked = $("#table-detail .chkDetail:checked").length;

            $("#checkAllParent").prop("checked", total > 0 && total === checked);
        });

        $("#btn-modalItem").on("click", function() {
            if (!isDetailEditable()) return;

            const nomor = tableDetail.rows().count() + 1;
            const rowNode = tableDetail.row.add(buildDetailRow(nomor)).node();

            $(rowNode).addClass("tr-height-30");
            configureDetailRowCells(rowNode);
            tableDetail.draw(false);
            applyEditState();

            const firstEditableCell = $(rowNode).find("td.editable-cell").first();
            setTimeout(function() {
                firstEditableCell.trigger("click");
            }, 0);
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
                        const encodedId = $(this).find('[name="detail[erp_lookup_value_id][]"]').val();
                        if (encodedId) {
                            $('#deleted-detail-container').append(
                                `<input type="hidden" name="detail_deleted[]" value="${escapeHtml(encodedId)}">`
                            );
                        }
                        tableDetail.row(this).remove();
                    });
                    tableDetail.draw(false);

                    $("#checkAllParent").prop("checked", false);

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

        $('form').on('submit', function(e) {
            hideAllEditors();

            if (isDetailEditable() && !validateLovDetail()) {
                e.preventDefault();
                setTimeout(() => {
                    $('#loading').hide();
                }, 100);
                return false;
            }

            let detail = {}; 
    
            $('#table-detail tbody tr').each(function() {
                $(this).find('input[name]').each(function() {
                    let rawName = $(this).attr('name'); 
                    let match = rawName.match(/\[(.*?)\]/);
                    if (match && match[1]) {
                        let key = match[1]; 
                        if (!detail[key]) {
                            detail[key] = [];
                        }
                        let finalValue;
                        if ($(this).is(':checkbox')) {
                            if ($(this).is(':checked')) {
                                finalValue = ($(this).val() !== 'on' && $(this).val() !== '') ? $(this).val() : 'Y';
                            } else {
                                finalValue = 'N';
                            }
                        } else {
                            finalValue = $(this).val();
                        }
                        detail[key].push(finalValue);
                    }
                });
            });
            $(this).append(`<input type="hidden" name="detail" value='${JSON.stringify(detail)}'>`);
            $('#table-detail tbody input').prop('disabled', true);
        });
    });

    function syncEditFlagCheckboxes() {
        const checked = $('#edit_flag').is(':checked') || $('#edit_flag_mobile').is(':checked');
        $('#edit_flag, #edit_flag_mobile').prop('checked', checked);

        if (!userCanEditMaster) {
            $('#edit_flag, #edit_flag_mobile').prop('checked', false).prop('disabled', true);
            $('#readonlyPR').show().html('<span class="badge bg-secondary">READ ONLY</span>');
        }
    }

    function isDetailEditable() {
        return userCanEditMaster && ($('#edit_flag').is(':checked') || $('#edit_flag_mobile').is(':checked'));
    }

    function applyEditState() {
        const editable = isDetailEditable();

        $('#nama_master').prop('readonly', true);
        $('#keterangan').prop('disabled', !editable);
        $('#checkAllParent, .chkDetail, #removeRow, #btn-modalItem').prop('disabled', !editable);
        $('#table-detail .edit-mode').prop('disabled', !editable);
        $('#table-detail .edit-mode').find('input, select, textarea').prop('disabled', !editable);
        $('#myForm button[type="submit"]').prop('disabled', !userCanEditMaster);
        $('#table-detail tbody td.editable-cell').css('pointer-events', editable ? '' : 'none');

        if (userCanEditMaster) {
            // $('#edit_flag, #edit_flag_mobile').prop('disabled', false);
            $('#readonlyPR').toggle(!editable).html(editable ? '' : '<span class="badge bg-secondary">READ ONLY</span>');
        }
    }

    function hideAllEditors(exceptTd) {
        $('#table-detail .edit-mode').each(function() {
            const input = $(this);
            if (exceptTd && input.closest('td')[0] === exceptTd[0]) return;

            updateCellView(input);
            input.addClass('d-none');
            input.closest('td').find('.view-mode').first().removeClass('d-none');
        });
    }

    function updateCellView(input) {
        const td = input.closest('td');
        const span = td.find('.view-mode').first();
        const field = td.data('field');
        const value = getEditorValue(input);

        if (field === 'primary_flag' || field === 'active_flag') {
            const iconClass = value === 'Y' ? 'text-success fa fa-check' : 'text-danger fa fa-times';
            const title = field === 'primary_flag' ?
                (value === 'Y' ? 'Default' : 'No Default') :
                (value === 'Y' ? 'Active' : 'Inactive');
            span.html(`<i class="${iconClass}" title="${title}" data-bs-toggle="tooltip" data-bs-placement="left"></i>`);
            return;
        }

        if (field === 'start_date' || field === 'end_date') {
            span.text(formatDateForView(value));
            return;
        }

        span.text(value);
        span.attr('title', value);
    }

    function getEditorValue(editor) {
        if (editor.hasClass('edit-mode') && editor.find('input[type="hidden"]').length) {
            return $.trim(editor.find('input[type="hidden"]').val());
        }

        return $.trim(editor.val());
    }

    function buildDetailRow(nomor) {
        const today = new Date().toISOString().slice(0, 10);

        return [
            nomor,
            `<input type="hidden" name="detail[erp_lookup_value_id][]" value="">`,
            `<input type="checkbox" class="chkDetail">`,
            `<span class="ellipsis align-middle view-mode" data-toggle="tooltip" data-placement="bottom" title=""></span>
                <input type="text" name="detail[nilai][]" class="form-control form-control-sm edit-mode d-none" value="" oninput="this.value = this.value.toUpperCase()">
                <input type="hidden" name="detail[program_code1][]" value="${escapeHtml(defaultProgramCode1)}">`,
            `<span class="ellipsis align-middle view-mode" data-toggle="tooltip" data-placement="bottom" title=""></span>
                <input type="text" name="detail[description][]" class="form-control form-control-sm edit-mode d-none" value="" oninput="this.value = this.value.toUpperCase()">`,
            `<span class="ellipsis align-middle view-mode">
                    <i class="text-danger fa fa-times" title="No Default" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                </span>
                <div class="edit-mode d-none">
                    <input type="hidden" name="detail[primary_flag][]" value="N">
                    <input type="checkbox" class="form-check-input flag-checkbox default-checkbox">
                </div>`,
            `<span class="ellipsis align-middle view-mode">${nomor}</span>
                <input type="number" name="detail[urutan][]" class="form-control form-control-sm edit-mode d-none" value="${nomor}">`,
            `<span class="ellipsis align-middle view-mode">${formatDateForView(today)}</span>
                <input type="date" name="detail[start_date][]" class="form-control form-control-sm edit-mode d-none" value="${today}">`,
            `<span class="ellipsis align-middle view-mode"></span>
                <input type="date" name="detail[end_date][]" class="form-control form-control-sm edit-mode d-none" value="">`,
            `<span class="ellipsis align-middle view-mode">
                    <i class="text-success fa fa-check" title="Active" data-bs-toggle="tooltip" data-bs-placement="left"></i>
                </span>
                <div class="edit-mode d-none">
                    <input type="hidden" name="detail[active_flag][]" value="Y">
                    <input type="checkbox" class="form-check-input flag-checkbox" checked>
                </div>`
        ];
    }

    function configureDetailRowCells(rowNode) {
        const fields = {
            3: 'nilai',
            4: 'description',
            5: 'primary_flag',
            6: 'urutan',
            7: 'start_date',
            8: 'end_date',
            9: 'active_flag'
        };

        Object.keys(fields).forEach(function(index) {
            const td = $(rowNode).children('td').eq(parseInt(index, 10));
            td.addClass('ellipsis editable-cell').attr('data-field', fields[index]);

            if (fields[index] === 'primary_flag' || fields[index] === 'active_flag') {
                td.addClass('text-center');
            }
        });
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function formatDateForView(value) {
        if (!value) return '';

        const parts = value.split('-');
        if (parts.length !== 3) return value;

        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const monthIndex = parseInt(parts[1], 10) - 1;

        if (monthIndex < 0 || monthIndex > 11) return value;
        return `${parts[2]} ${months[monthIndex]} ${parts[0]}`;
    }

    function validateLovDetail() {
        const nilaiMap = {};
        const noteMap = {};
        let message = '';
        let el_invalid = null;
        $('#table-detail tbody tr').each(function(rowIndex) {
            if (message) return false;

            const row = $(this);
            const nilai = $.trim(row.find('[name="detail[nilai][]"]').val());
            const note = $.trim(row.find('[name="detail[description][]"]').val());
            const programCode = $.trim(row.find('[name="detail[program_code1][]"]').val()).toUpperCase();

            if (nilai && nilaiMap[nilai.toUpperCase()] !== undefined) {
                message = `Nilai pada baris ${rowIndex + 1} tidak boleh sama dengan baris ${nilaiMap[nilai.toUpperCase()] + 1}.`;
                return false;
            }

            if (note && noteMap[note.toUpperCase()] !== undefined) {
                message = `Note pada baris ${rowIndex + 1} tidak boleh sama dengan baris ${noteMap[note.toUpperCase()] + 1}.`;
                return false;
            }

            nilaiMap[nilai.toUpperCase()] = rowIndex;
            noteMap[note.toUpperCase()] = rowIndex;

            if (defaultProgramCode1 === 'GROUP' && note.length !== 3) {
                message = `Note untuk <b>${$('#nama_master').val()}</b> pada baris ${rowIndex + 1} wajib 3 karakter.`;
                el_invalid = row.find("td.editable-cell").eq(1);
                return false;
            }

            if (defaultProgramCode1 === 'MEREK' && note.length !== 4) {
                el_invalid = row.find("td.editable-cell").eq(1);
                message = `Note untuk <b>${$('#nama_master').val()}</b> pada baris ${rowIndex + 1} wajib 4 karakter.`;
                return false;
            }

            if (defaultProgramCode1 === 'TIPE' && nilai.length !== 3) {
                el_invalid = row.find("td.editable-cell").first();
                message = `Nilai untuk <b>${$('#nama_master').val()}</b> pada baris ${rowIndex + 1} wajib 3 karakter.`;
                return false;
            }
        });

        if (message) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Detail',
                html: message
            }).then((result) => {
                if (result.isConfirmed) {
                    el_invalid.trigger('click');
                }
            });
            return false;
        }

        return true;
    }

    $(document).on('input', 'input[name="detail[description][]"]', function(){
        let note    = $(this).val();
        let message = '';
        let max     = 0;
        if (defaultProgramCode1 === 'GROUP' && note.length > 3) {
            max = 3;
            message = `Note untuk <b>${$('#nama_master').val()}</b> wajib 3 karakter.`;
        }else if (defaultProgramCode1 === 'MEREK' && note.length > 4) {
            max = 4;
            message = `Note untuk <b>${$('#nama_master').val()}</b> wajib 4 karakter.`;
        }

        if (message) {
            $(this).val(note.substr(0,max));
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Detail',
                html: message
            });
        }
    });
    
    $(document).on('input', 'input[name="detail[nilai][]"]', function(){
        let note    = $(this).val();
        let message = '';
        let max     = 0;
        if (defaultProgramCode1 === 'TIPE' && nilai.length > 3) {
            max = 3;
            message = `Nilai untuk <b>${$('#nama_master').val()}</b> wajib 3 karakter.`;
        }

        if (message) {
            $(this).val(note.substr(0,max));
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Detail',
                html: message
            });
        }
    });
    $('#table-detail').on('keydown', 'input', function(e) {
        if (e.keyCode === 37 || e.keyCode === 39) {
            e.stopPropagation();
        }

        if (e.keyCode === 9) {
            e.preventDefault();
            $(this).blur();
        }
    });
</script>
