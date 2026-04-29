<style>
    @media (max-width: 768px) {
        .w-desc {
            min-width: 280px !important;
        }
        .w-act {
            min-width: 100px !important;
        }
    }
</style>
<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url(strtolower($access['ERP_MENU_NAME'])) ?>" class="text-decoration-underline"><?= $access['PROMPT'] ?></a>
                            </li>
                            <li class="breadcrumb-item active text-decoration-underline"><?= $breadcrumb ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="" method="post">
            <div class="row">
                <div class="col-12">
                    <div class="card border-2">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-12 text-end">
                                    <?= button_actions(['insert','save','reload']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12 col-xl-4"> 
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title m-0">
                                <i class="fa fa-info-circle me-1"></i> <span>Informasi Group</span>
                            </h5>
                        </div>
                        <div class="card-body border-top">
                            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->ERP_GROUP_ID)); ?>">
                            <div class="mb-3">
                                <label for="name">Group Name</label>
                                <span class="text-danger">*</span>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-group-line"></i>
                                    </span>
                                    <input type="text" name="name" id="name" class="form-control <?= form_error('name') ? 'is-invalid' : null; ?>" placeholder="Enter Group Name" value="<?= $this->input->post('name') ?? $data->ERP_GROUP_NAME; ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="note">Note</label>
                                <div class="input-group">
                                    <textarea name="note" id="note" class="form-control <?= form_error('note') ? 'is-invalid' : null; ?>" 
                                        placeholder="Enter Group Name"><?= $this->input->post('note') ?? $data->NOTE; ?></textarea>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="active_flag">Active Flag:</label>
                                <div class="input-group">
                                    <div class="form-check form-switch mb-3" dir="ltr">
                                        <input type="checkbox" name="active_flag" class="form-check-input" id="active_flag" <?= set_value('active_flag', $data->ACTIVE_FLAG ?? '') === 'Y' ? 'checked' : '' ?>>
                                        <label class="form-check-label"></label>
                                    </div>
                                </div>
                                <div class="text-danger"><?= form_error('active_flag') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title m-0">
                                <i class="fa fa-info-circle me-1"></i> <span>Hak Akses Menu</span>
                            </h5>
                        </div>
                        <div class="card-body border-top">
                            <div class="mb-3">
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fa fa-check-circle me-1"></i> Pilih Semua</button>
                                <button type="button" class="btn btn-outline-danger btn-sm"><i class="fa fa-times-circle me-1"></i> Hapus Semua</button>
                                <button type="button" class="btn btn-outline-info btn-sm"><i class="fa fa-eye me-1"></i> Aktifkan View</button>
                                <button type="button" class="btn btn-outline-success btn-sm"><i class="fa fa-plus me-1"></i> Aktifkan Tambah</button>
                                <button type="button" class="btn btn-outline-warning btn-sm"><i class="ri ri-pencil-line me-1"></i> Aktifkan Edit</button>
                                <button type="button" class="btn btn-outline-danger btn-sm"><i class="fa fa-trash me-1"></i> Aktifkan Hapus</button>
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-wrap p-2 px-3 bg-light rounded border mb-3">
                                <span class="text-dark fw-bold" style="font-size: 11px;">Keterangan:</span>
                                <div class="d-flex align-items-center gap-1 text-secondary fw-semibold" style="font-size: 11px;">
                                    <div class="rounded-circle bg-info" style="width:10px;height:10px"></div> View
                                </div>
                                <div class="d-flex align-items-center gap-1 text-secondary fw-semibold" style="font-size: 11px;">
                                    <div class="rounded-circle bg-success" style="width:10px;height:10px"></div> Tambah
                                </div>
                                <div class="d-flex align-items-center gap-1 text-secondary fw-semibold" style="font-size: 11px;">
                                    <div class="rounded-circle bg-warning" style="width:10px;height:10px"></div> Edit
                                </div>
                                <div class="d-flex align-items-center gap-1 text-secondary fw-semibold" style="font-size: 11px;">
                                    <div class="rounded-circle bg-danger" style="width:10px;height:10px"></div> Hapus
                                </div>
                                <div class="ms-auto text-secondary" style="font-size: 11px;">
                                    <i class="bi bi-info-circle"></i> Klik header kolom untuk bulk toggle
                                </div>
                            </div>

                            <div class="table-responsive" style="max-height: 50dvh;">
                                <table class="table table-hover align-middle table-sm" id="tbl-menu">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th class="text-secondary text-uppercase border-bottom w-desc">Menu / Fitur
                                            </th>
                                            <th width="150" class="link-info text-center w-act chk-all" style="cursor: pointer;"><i class="fa fa-eye me-1"></i> View</th>
                                            <th width="150" class="link-success text-center w-act chk-all" style="cursor: pointer;"><i class="fa fa-plus me-1"></i> Tambah</th>
                                            <th width="150" class="link-warning text-center w-act chk-all" style="cursor: pointer;"><i class="ri ri-pencil-line me-1"></i> Edit</th>
                                            <th width="150" class="link-danger text-center w-act chk-all" style="cursor: pointer;"><i class="fa fa-trash me-1"></i> Hapus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($menus as $m) {
                                            $have_child     = (isset($m['child']) && count($m['child']));
                                            $id_menu        = $m['ERP_MENU_ID'];
                                            $name           = strtolower($m['ERP_MENU_NAME']);
                                            $is_view        = in_array(set_value("view[$id_menu]", $group_menu[$id_menu]['VIEW_FLAG'] ?? 'N'),[1,'Y']);
                                            $is_add         = in_array(set_value("add[$id_menu]", $group_menu[$id_menu]['INSERT_FLAG'] ?? 'N'),[1,'Y']);
                                            $is_edit        = in_array(set_value("edit[$id_menu]", $group_menu[$id_menu]['UPDATE_FLAG'] ?? 'N'),[1,'Y']);
                                            $is_delete      = in_array(set_value("delete[$id_menu]", $group_menu[$id_menu]['DELETE_FLAG'] ?? 'N'),[1,'Y']);
                                            ?>
                                            <tr>
                                                <td class="fw-bold">
                                                    <div class="d-flex align-items-center">
                                                        <?php if($have_child){ ?>
                                                        <button type="button" class="btn btn-sm p-0 d-flex align-items-center justify-content-center text-secondary border-none me-2 shadow-none btn-collapse active"data-collapse="<?= $name ?>"
                                                            style="width: 22px; height: 22px; background: #fff;">
                                                            <i class="fa fa-chevron-down" style="font-size: 11px; transition: transform 0.2s;"></i>
                                                        </button>
                                                        <?php } else { echo '<div style="width: 30px; flex-shrink: 0;"></div>'; } ?>
                                                        <span class="menu-lbl fw-bold text-dark" style="font-size: 13.5px;"><i class="ri <?= $m['MENU_ICON'] ?> me-1"></i> <?= $m['PROMPT'] ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center m-0 fs-5">
                                                        <input name="view[<?= $id_menu ?>]" value="1" class="form-check-input border-secondary shadow-none chk<?= $is_view?' bg-info':'' ?>" type="checkbox" data-id="<?= $name ?>" data-type="view"
                                                            <?= $is_view?' checked':'' ?> style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center m-0 fs-5">
                                                        <input name="add[<?= $id_menu ?>]" value="1" class="form-check-input border-secondary shadow-none chk<?= $is_add?' bg-success':'' ?>" type="checkbox" data-id="<?= $name ?>" data-type="add" 
                                                            <?= $is_add?' checked':'' ?> style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center m-0 fs-5">
                                                        <input name="edit[<?= $id_menu ?>]" value="1" class="form-check-input border-secondary shadow-none chk<?= $is_edit?' bg-warning':'' ?>" type="checkbox" data-id="<?= $name ?>" data-type="edit" 
                                                            <?= $is_edit?' checked':'' ?> style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check d-flex justify-content-center m-0 fs-5">
                                                        <input name="delete[<?= $id_menu ?>]" value="1" class="form-check-input border-secondary shadow-none chk<?= $is_delete?' bg-danger':'' ?>" type="checkbox" data-id="<?= $name ?>" data-type="delete"
                                                            <?= $is_delete?' checked':'' ?> style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php if(isset($m['child']) && count($m['child'])){
                                                foreach ($m['child'] as $m2) {
                                                    $id_menu_child  = $m2['ERP_MENU_ID'];
                                                    $name2          = strtolower($m2['ERP_MENU_NAME']);
                                                    $is_view_child  = in_array(set_value("view_child[$id_menu][$id_menu_child]", $group_menu[$id_menu_child]['VIEW_FLAG'] ?? 'N'),[1,'Y']);
                                                    $is_add_child   = in_array(set_value("add_child[$id_menu][$id_menu_child]", $group_menu[$id_menu_child]['INSERT_FLAG'] ?? 'N'),[1,'Y']);
                                                    $is_edit_child  = in_array(set_value("edit_child[$id_menu][$id_menu_child]", $group_menu[$id_menu_child]['UPDATE_FLAG'] ?? 'N'),[1,'Y']);
                                                    $is_delete_child= in_array(set_value("delete_child[$id_menu][$id_menu_child]", $group_menu[$id_menu_child]['DELETE_FLAG'] ?? 'N'),[1,'Y']);
                                                    ?>
                                                <tr data-parent="<?= $name ?>">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div style="width: 24px; flex-shrink: 0;"></div>
                                                            <div style="width: 16px; height: 16px; border-left: 2px solid #dee2e6; border-bottom: 2px solid #dee2e6; border-bottom-left-radius: 6px; margin-right: 8px; margin-top: -12px; flex-shrink: 0;"></div>
                                                            <span class="menu-lbl text-dark" style="font-size: 13.5px;"><i class="ri <?= $m2['MENU_ICON'] ?> me-1"></i> <?= $m2['PROMPT'] ?></span>
                                                        </div>                                                
                                                    </td>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center m-0 fs-5">
                                                            <input name="view_child[<?= $id_menu ?>][<?= $id_menu_child ?>]" value="1" class="form-check-input border-secondary shadow-none chk<?= $is_view_child?" bg-info":'' ?>" type="checkbox" data-id="<?= $name2 ?>" data-type="view" <?= $is_view_child?' checked':'' ?> style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center m-0 fs-5">
                                                            <input name="add_child[<?= $id_menu ?>][<?= $id_menu_child ?>]" value="1" class="form-check-input border-secondary shadow-none chk<?= $is_add_child?' bg-success':'' ?>" type="checkbox" data-id="<?= $name2 ?>" data-type="add" <?= $is_add_child?' checked':'' ?> style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center m-0 fs-5">
                                                            <input name="edit_child[<?= $id_menu ?>][<?= $id_menu_child ?>]" value="1" class="form-check-input border-secondary shadow-none chk<?= $is_edit_child?' bg-warning':'' ?>" type="checkbox" data-id="<?= $name2 ?>" data-type="edit" <?= $is_edit_child?' checked':'' ?> style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check d-flex justify-content-center m-0 fs-5">
                                                            <input name="delete_child[<?= $id_menu ?>][<?= $id_menu_child ?>]" value="1" class="form-check-input border-secondary shadow-none chk<?= $is_delete_child?' bg-danger':'' ?>" type="checkbox" data-id="<?= $name2 ?>" data-type="delete" <?= $is_delete_child?' checked':'' ?> style="cursor: pointer; width: 1.25em; height: 1.25em;">
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } } ?>
                                        <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).on('click', '.btn-collapse', function(){
        const parent = $(this).attr('data-collapse');
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).find('i').attr('class','fa fa-chevron-left');
            $('#tbl-menu').find(`tr[data-parent="${parent}"]`).fadeOut(300);
        }else{
            $(this).addClass('active');
            $(this).find('i').attr('class','fa fa-chevron-down');
            $('#tbl-menu').find(`tr[data-parent="${parent}"]`).fadeIn(300).css("display", "table-row");
        }
    });
    $(document).on('change', '#active_flag', function(){
        const is_checked = $(this).is(':checked');
        $(this).closest('.input-group').find('.form-check-label').text(is_checked ? 'Yes' : 'No');
    });

    $(document).ready(function () {
        $('.form-switch').find('input').trigger('change');
        $('#tbl-menu tbody .chk:not(:checked)').trigger('change');

        const TYPES = ['view', 'add', 'edit', 'delete'];
        const BG_MAP = { view: 'bg-info', add: 'bg-success', edit: 'bg-warning', delete: 'bg-danger' };

        function getChk($row, type) {
            return $row.find(`.chk[data-type="${type}"]`);
        }

        function setChk($chk, val) {
            $chk.prop('checked', val);
            $chk.each(function () {
                const type = $(this).data('type');
                $(this).toggleClass(BG_MAP[type], val);
            });
        }

        function syncParentView($childRow) {
            const parent = $childRow.data('parent');
            if (!parent) return;

            const $parentRow = $(`#tbl-menu tr`).filter(function () {
                return !$(this).data('parent') &&
                    $(this).find('.btn-collapse').data('collapse') === parent;
            });

            const allChildViews = $(`#tbl-menu tr[data-parent="${parent}"] .chk[data-type="view"]`);
            const anyChecked = allChildViews.toArray().some(el => el.checked);

            setChk(getChk($parentRow, 'view'), anyChecked);

            if (!anyChecked) {
                TYPES.forEach(t => setChk(getChk($parentRow, t), false));
            }
        }

        function applyRowRules($row, changedType, isChecked) {
            if (changedType !== 'view' && isChecked) {
                setChk(getChk($row, 'view'), true);
            }
            if (changedType === 'view' && !isChecked) {
                TYPES.forEach(t => setChk(getChk($row, t), false));
            }
        }

        $(document).on('change', '#tbl-menu .chk', function () {
            const $row = $(this).closest('tr');
            const type = $(this).data('type');
            const isChecked = this.checked;
            const isChild = !!$row.data('parent');

            setChk($(this), isChecked); // sync bg checkbox yang diubah user
            applyRowRules($row, type, isChecked);

            if (isChild) syncParentView($row);

            if (!isChild) {
                const parentName = $row.find('.btn-collapse').data('collapse');
                if (!parentName) return;

                const $children = $(`#tbl-menu tr[data-parent="${parentName}"]`);
                if (isChecked) {
                    $children.each(function () {
                        setChk(getChk($(this), type), true);
                        applyRowRules($(this), type, true);
                    });
                } else {
                    if (type === 'view') {
                        $children.each(function () {
                            TYPES.forEach(t => setChk(getChk($(this), t), false));
                        });
                    } else {
                        $children.each(function () {
                            setChk(getChk($(this), type), false);
                        });
                    }
                }
            }
        });

        $(document).on('click', '.chk-all', function () {
            const colIdx = $(this).index();
            const type = TYPES[colIdx - 1];
            const $allChk = $(`#tbl-menu tbody .chk[data-type="${type}"]`);
            const allChecked = $allChk.toArray().every(el => el.checked);

            $allChk.each(function () {
                const $row = $(this).closest('tr');
                setChk($(this), !allChecked);
                applyRowRules($row, type, !allChecked);
            });

            $(`#tbl-menu tbody tr[data-parent]`).each(function () {
                syncParentView($(this));
            });
        });

        const bulkMap = {
            0: null,
            1: null,
            2: 'view',
            3: 'add',
            4: 'edit',
            5: 'delete',
        };

        $('.mb-3 button').each(function (i) {
            $(this).on('click', function () {
                const type = bulkMap[i];
                const $allChk = type
                    ? $(`#tbl-menu tbody .chk[data-type="${type}"]`)
                    : $('#tbl-menu tbody .chk');

                const setVal = i !== 1;

                $allChk.each(function () {
                    const $row = $(this).closest('tr');
                    const t = $(this).data('type');
                    setChk($(this), setVal);
                    if (setVal) applyRowRules($row, t, true);
                    else if (t === 'view') applyRowRules($row, 'view', false);
                });

                $(`#tbl-menu tbody tr[data-parent]`).each(function () {
                    syncParentView($(this));
                });
            });
        });
    });
</script>