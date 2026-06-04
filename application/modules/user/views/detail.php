<?php $this->load->view('user/style'); ?>
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

        <form action="" method="post" id="myForm">
            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->ERP_USER_ID)); ?>">
            <div class="row">
                <div class="col-12">
                    <div class="card border-2">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12 d-flex align-items-center gap-2 label-status">
                                    <h5 style="width: 100px;" id="statusTagKonsiId"></h5>
                                    <h5 style="width: 100px;" id="readonlyTagKonsiId"></h5>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <?= button_actions(['insert','save',
                                        [
                                            'key'          => 'log_info',
                                            'class'        => 'btn-info btn-log-info',
                                            'title'        => 'Log & History',
                                            'icon'         => 'ri-question-line',
                                            'data-url'     => 'user/get_log_info',
                                            'data-param'   => base64_encode($this->encrypt->encode(json_encode([
                                                'id' => $data->ERP_USER_ID,
                                            ]))),
                                        ],
                                        'reload']) ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="name">Nama User:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-user-fill"></i>
                                                </span>
                                                <input type="text" name="name" id="name" class="form-control <?= form_error('name') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Nama User" value="<?= set_value('name',$data->ERP_USER_NAME); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('name') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="full_name">Nama Lengkap:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-file-user-fill"></i>
                                                </span>
                                                <input type="text" name="full_name" id="full_name" class="form-control <?= form_error('full_name') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Nama Lengkap" value="<?= set_value('full_name',$data->ERP_USER_DESC); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('full_name') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="group_id">Group:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-shield-user-fill"></i>
                                                </span>
                                                <select name="group_id" id="group_id" class="select2 form-select<?= form_error('group_id') ? ' is-invalid' : null; ?>"
                                                    data-url="user/get_group"
                                                    data-selected-id="<?= set_value('group_id',$data->ERP_GROUP_ID) ?>"
                                                    data-dropdown-parent="body"
                                                    ></select>
                                            </div>
                                            <div class="text-danger"><?= form_error('group_id') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="divisi_id">Divisi:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-group-fill"></i>
                                                </span>
                                                <select name="divisi_id" id="divisi_id" class="select2 form-select<?= form_error('divisi_id') ? ' is-invalid' : null; ?>"
                                                    data-url="user/get_divisi"
                                                    data-selected-id="<?= set_value('divisi_id',$data->DIVISI_ID) ?>"
                                                    data-dropdown-parent="body"
                                                    ></select>
                                            </div>
                                            <div class="text-danger"><?= form_error('divisi_id') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="title">Jabatan:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-briefcase-fill"></i>
                                                </span>
                                                <input type="text" name="title" id="title" class="form-control <?= form_error('title') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Jabatan" value="<?= set_value('title',$data->TITLE); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('title') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="password">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-lock-password-fill"></i>
                                                </span>
                                                <input type="password" class="form-control <?= form_error('password') ? 'is-invalid' : null ?>" name="password" id="password" placeholder="Enter your password" minlength="3">
                                                <span class="input-group-text">
                                                    <i class="ri ri-eye-close-fill show-password"></i>
                                                </span>
                                            </div>
                                            <span class="text-muted">Kosongkan jika tidak ingin mengubah password</span>
                                            <span class="text-danger"><?= form_error('password') ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="start_date">Start Date:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-briefcase-fill"></i>
                                                </span>
                                                <input type="datetime-local" name="start_date" id="start_date" class="form-control <?= form_error('start_date') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Start Date" value="<?= set_value('start_date', $data->START_DATE); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('start_date') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_date">End Date:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-briefcase-fill"></i>
                                                </span>
                                                <input type="datetime-local" name="end_date" id="end_date" class="form-control <?= form_error('end_date') ? 'is-invalid' : null; ?>" 
                                                    placeholder="End Date" value="<?= set_value('end_date', ($data->END_DATE=='9999-12-31 00:00:00' ? null : $data->END_DATE)); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('end_date') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-menu" role="tab" aria-selected="true">
                                        <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Menu">
                                            <i class="ri-menu-line"></i>
                                        </span>
                                        <span class="d-none d-sm-block">Menu</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab-account" role="tab" aria-selected="true">
                                        <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Akun/COA">
                                            <i class="ri-bank-card-line"></i>
                                        </span>
                                        <span class="d-none d-sm-block">Akun/COA</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab-warehouse" role="tab" aria-selected="true">
                                        <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Warehouse">
                                            <i class="ri-store-2-line"></i>
                                        </span>
                                        <span class="d-none d-sm-block">Warehouse</span>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab-sales" role="tab" aria-selected="true">
                                        <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Sales">
                                            <i class="ri-shopping-cart-2-line"></i>
                                        </span>
                                        <span class="d-none d-sm-block">Sales</span>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane active form-xs" id="tab-menu" role="tabpanel">
                                    <div class="mt-3" id="d-menu"></div>
                                </div>
                                <div class="tab-pane form-xs pt-3 pb-3" id="tab-account" role="tabpanel">
                                    <button type="button" id="addAccount" class="btn btn-success btn-sm" style="width: 30px;">+</button>
                                    <button type="button" id="removeAccount" class="btn btn-danger btn-sm" style="width: 30px;">-</button>
                                    <div class="table-responsive mt-3" style="max-height: 50dvh;">
                                        <table id="tbl-account" class="table mt-3 w-100 table-sm align-top">
                                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)" class="sticky-top">
                                                <tr>
                                                    <th class="text-center" width="50"><input type="checkbox" id="chkAll"></th>
                                                    <th class="text-center" width="50">No</th>
                                                    <th>Kas/Bank</th>
                                                    <th width="20%">Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    foreach ($accounts as $k => $v) { ?>
                                                        <tr>
                                                            <td class="text-center"><input type="checkbox" class="chk"></td>
                                                            <td class="text-center rowNo"></td>
                                                            <td>
                                                                <select name="account[]" 
                                                                    data-url="user/get_accounts"
                                                                    data-dropdown-parent="body"
                                                                    placeholder="Pilih Kas/Bank"
                                                                    data-selected-id="<?= $v['account'] ?>"
                                                                    class="form-select select-coa select2<?= form_error('account[' . $k . ']') ? ' is-invalid' : null; ?>"><option value=""></option></select>
                                                                <div class="text-danger"><?= form_error('account[' . $k . ']') ?></div>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="account_id[]" value="<?= $v['account_id'] ?>">
                                                                <textarea class="form-control form-control-sm border-0 coa-note" name="account_note[]" readonly><?= isset($v['account_note']) ? $v['account_note'] : '' ?></textarea>
                                                                <div class="text-danger"><?= form_error('account_note[' . $k . ']') ?></div>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane form-xs pt-3 pb-3" id="tab-warehouse" role="tabpanel">
                                    <button type="button" id="addWarehouse" class="btn btn-success btn-sm" style="width: 30px;">+</button>
                                    <button type="button" id="removeWarehouse" class="btn btn-danger btn-sm" style="width: 30px;">-</button>
                                    <div class="table-responsive mt-3" style="max-height: 50dvh;">
                                        <table id="tbl-warehouse" class="table mt-3 w-100 table-sm align-top">
                                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)" class="sticky-top">
                                                <tr>
                                                    <th class="text-center" width="50"><input type="checkbox" id="chkAllWarehouse"></th>
                                                    <th class="text-center" width="50">No</th>
                                                    <th>Warehouse</th>
                                                    <th width="100">Default</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    foreach ($warehouses as $k => $v) { ?>
                                                        <tr>
                                                            <td class="text-center"><input type="checkbox" class="chk"></td>
                                                            <td class="text-center rowNo"></td>
                                                            <td>
                                                                <select name="warehouses[]" 
                                                                    data-url="user/get_warehouses"
                                                                    data-dropdown-parent="body"
                                                                    placeholder="Pilih Warehouse"
                                                                    data-selected-id="<?= $v['warehouse'] ?>"
                                                                    class="form-select select-warehouse select2<?= form_error('warehouses[' . $k . ']') ? ' is-invalid' : null; ?>"><option value=""></option></select>
                                                                <div class="text-danger"><?= form_error('warehouses[' . $k . ']') ?></div>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" name="warehouses_id[]" value="<?= $v['warehouse_id'] ?>">
                                                                <input type="checkbox" name="default_warehouse[]" value="Y" class="form-check-input default-warehouse fs-5"<?= $v['is_default'] == 'Y' ? 'checked' : '' ?>>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane form-xs pt-3 pb-3" id="tab-sales" role="tabpanel">
                                    <button type="button" id="addSales" class="btn btn-success btn-sm" style="width: 30px;">+</button>
                                    <button type="button" id="removeSales" class="btn btn-danger btn-sm" style="width: 30px;">-</button>
                                    <div class="table-responsive mt-3" style="max-height: 50dvh;">
                                        <table id="tbl-sales" class="table mt-3 w-100 table-sm align-top">
                                            <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)" class="sticky-top">
                                                <tr>
                                                    <th class="text-center" width="50"><input type="checkbox" id="chkAllSales"></th>
                                                    <th class="text-center" width="50">No</th>
                                                    <th>Sales</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    foreach ($sales as $k => $v) { ?>
                                                        <tr>
                                                            <td class="text-center"><input type="checkbox" class="chk"></td>
                                                            <td class="text-center rowNo"></td>
                                                            <td>
                                                                <input type="hidden" name="sales_id[]" value="<?= $v['sales_id'] ?>">
                                                                <select name="sales[]" 
                                                                    data-url="user/get_sales"
                                                                    data-dropdown-parent="body"
                                                                    placeholder="Pilih Sales"
                                                                    data-selected-id="<?= $v['sales'] ?>"
                                                                    class="form-select select-sales select2<?= form_error('sales[' . $k . ']') ? ' is-invalid' : null; ?>"><option value=""></option></select>
                                                                <div class="text-danger"><?= form_error('sales[' . $k . ']') ?></div>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="old_menu" class="d-none"><?= base64_encode(json_encode($old_menu)) ?></div>

<div class="modal fade" id="modalNote" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="modalNoteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNoteLabel">Keterangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="noteText" rows="5"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    let xhr = null;
    let tblAccount      = null;
    let tblWarehouse    = null;
    let tblSales        = null;
    $(document).on('change', '#group_id', function(){
        const group_id = $(this).val();
        if(group_id){
            $('#loading').show();
            if(xhr){
                xhr.abort();
            }
            xhr = $.ajax({
                type: "POST",
                url: config_app.url+"user/menu_permissions",
                data: {
                    group_id: group_id,
                    id : $('#myForm [name="id"]').val()
                },
                success: function(response) {
                    $('#loading').hide();
                    $('#d-menu').html(response);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loading').hide();
                    if (textStatus === 'abort') {
                        return;
                    }
                    $('#d-menu').html(`<div class="alert alert-danger mb-0" role="alert">
                        failed to load data menu and permissions.
                    </div>`);
                }
            });
        }else{
            $('#d-menu').html(`<div class="alert alert-dark mb-0" role="alert">
                Choose a group user to display the menus and permissions
            </div>`);
        }
    });

    let S = { flat:{}, active:null, crud:{}, overrides:{} };
    $(document).off('click','.menu-leaf').on('click','.menu-leaf',function(e){
        e.stopPropagation();
        const id=+$(this).data('id');
        if(S.active===id) return;
        S.active=id;
        $('.menu-leaf').removeClass('active');
        $(this).addClass('active');
        buildPermissions($(this));
    });

    function buildPermissions(g){
        $('body #builder').attr('data-id',btoa(g.attr('data-id')));
        let permissions;
        try {
            permissions = JSON.parse(atob(g.find('.d-permissions').text()));
        } catch (error) {
            permissions = {};
        }

        let input_permissions = '';
        try {
            input_permissions = JSON.parse(atob(g.find('input').val()));
        } catch (error) {
            input_permissions = {};
        }

        let crud_txt = '';
        $.each(permissions.actions, function(k,v){
            crud_txt += `<div class="d-flex flex-column align-items-center gap-1">
                <div class="crud-face${v=='Y'?(' on-'+k.substr(0,1)):''}">
                    <i class="fa fa-solid ${v=='Y'?'fa-check':'fa-times'}"></i>
                    <span class="text-uppercase">${k}</span>
                </div>
            </div>`;
        });

        let actions_txt = '';
        const userActions = input_permissions.actions || {};
        $.each(permissions.permissions.actions, function(k,v){
            const isActive = userActions[v] === 1;
            actions_txt += `<span class="badge perm-pill rounded-pill text-capitalize${isActive?' on-action':''}" data-type="action" data-key="${v}">
                <i class="fa fa-solid${isActive?' fa-check':' fa-times'} me-1"></i> ${v.replace(/_/g, ' ')}
            </span>`;
        });

        let fields_txt = '';
        const userFields = input_permissions.fields || {};
        $.each(permissions.permissions.fields, function(k,v){
            const isActive = userFields[v] === 1;
            fields_txt += `<span class="badge perm-pill rounded-pill text-capitalize${isActive?' on-field':''}" data-type="field" data-key="${v}">
                <i class="fa fa-solid${isActive?' fa-eye':' fa-eye-slash'} me-1"></i> ${v.replace(/_/g, ' ')}
            </span>`;
        });

        let tab_txt = '';
        const userTabs          = input_permissions.tabs || {};
        const userTabFields  = input_permissions.tab_fields || {};
        $.each(permissions.permissions.tabs, function(k,v){
            let tab_field_txt = '';
            if($.isNumeric(k)){
                k = v;
            }else{
                tab_field_txt += `<div class="mt-2">
                    <small class="text-muted d-block mb-1">Fields in tab (click for toggle):</small>
                    <div class="d-flex flex-wrap gap-1" data-tab-chips="${k}">`;

                const fieldInTabs = userTabFields[k] || {};
                $.each(v, function(k2,v2){
                    const isActive = fieldInTabs[v2] === 1;
                    tab_field_txt += `<span class="badge f-chip font-monospace text-capitalize${isActive?' on':''}" style="font-size:.8rem" data-field="${v2}">
                        <i class="fa fa-solid${isActive?' fa-eye':' fa-eye-slash'} me-1"></i>${v2.replace(/_/g, ' ')}
                        </span>`;
                });
                tab_field_txt += `</div></div>`;
            }
            
            const isActive = userTabs[k] === 1;
            tab_txt += `<div class="border rounded p-2${isActive?' border-warning':''}" data-tab-item="${k}">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="tab-sw${isActive?' on':''}" data-tab="${k}"></button>
                    <span class="fw-semibold font-monospace text-capitalize" style="font-size:.8rem">${k.replace(/_/g, ' ')}</span>
                    <span class="tab-status text-muted ms-auto" style="font-size:.8rem">${isActive?'Visible':'Hidden'}</span>
                </div>${tab_field_txt}
            </div>`;
        });

        let content = `
            <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-2 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px;font-size:1rem">
                        <i class="ri ri-menu-fill text-light"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">${g.find('.flex-grow-1').text()}</h6>
                    </div>
                </div>    
            </div>
            <div class="card mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-2 px-3 border-bottom">
                    <span class="fw-semibold d-inline-flex align-items-center" style="font-size:.82rem">
                        <i class="ri ri-handbag-fill text-primary me-2"></i>
                        CRUD Access
                    </span>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap">${crud_txt}</div>
                </div>
            </div>`;
        
        if(actions_txt){
            content += `<div class="card mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-2 px-3 border-bottom">
                    <span class="fw-semibold d-inline-flex align-items-center" style="font-size:.82rem">
                        <i class="ri ri-flashlight-fill text-primary me-2"></i>
                        Actions
                    </span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.7rem" id="act-all-on">All ON</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.7rem" id="act-all-off">All OFF</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">${actions_txt}</div>
                </div>
            </div>`;
        }

        if(fields_txt){
            content += `<div class="card mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-2 px-3 border-bottom">
                    <span class="fw-semibold d-inline-flex align-items-center" style="font-size:.82rem">
                        <i class="ri ri-table-fill text-success me-2"></i>
                        Actions
                    </span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.7rem" id="field-all-on">All ON</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.7rem" id="field-all-off">All OFF</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">${fields_txt}</div>
                </div>
            </div>`;
        }

        if(tab_txt){
            content += `<div class="card mb-3">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-2 px-3 border-bottom">
                    <span class="fw-semibold d-inline-flex align-items-center" style="font-size:.82rem">
                        <i class="ri ri-node-tree text-success me-2"></i>
                        Tabs
                    </span>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.7rem" id="tabs-all-on">All ON</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:.7rem" id="tabs-all-off">All OFF</button>
                    </div>
                </div>
                <div class="card-body py-2 px-3 d-flex flex-column gap-2">${tab_txt}</div>
            </div>`;
        }

        $('body #builder').html(content);
    }

    //actions check all
    $(document).on('click', '#act-all-on',function(){
        $('body #builder .perm-pill[data-type="action"]').addClass('on-action').find('i').attr('class','fa fa-solid fa-check me-1');
        changePermission();
    });
    $(document).on('click', '#act-all-off',function(){
        $('body #builder .perm-pill[data-type="action"]').removeClass('on-action').find('i').attr('class','fa fa-solid fa-times me-1');
        changePermission();
    });

    //fields check all
    $(document).on('click', '#field-all-on',function(){
        $('body #builder .perm-pill[data-type="field"]').addClass('on-field').find('i').attr('class','fa fa-solid fa-eye me-1');
        changePermission();
    });
    $(document).on('click', '#field-all-off',function(){
        $('body #builder .perm-pill[data-type="field"]').removeClass('on-field').find('i').attr('class','fa fa-solid fa-eye-slash me-1');
        changePermission();
    });
    
    //tabs check all
    $(document).on('click', '#tabs-all-on',function(){
        const g = $(this).closest('.card').find('[data-tab-item]');
        g.toggleClass('border-warning',true);
        g.find('.tab-sw').toggleClass('on',true);
        g.find('.tab-status').text('Visible');

        g.find('.f-chip').toggleClass('on',true);
        g.find('.f-chip').find('i').attr('class', 'fa fa-solid fa-eye me-1');

        changePermission();
    });
    $(document).on('click', '#tabs-all-off',function(){
        const g = $(this).closest('.card').find('[data-tab-item]');
        g.toggleClass('border-warning',false);
        g.find('.tab-sw').toggleClass('on',false);
        g.find('.tab-status').text('Hidden');

        g.find('.f-chip').toggleClass('on',false);
        g.find('.f-chip').find('i').attr('class', 'fa fa-solid fa-eye-slash me-1');

        changePermission();
    });

    $(document).on('click', '.perm-pill', function(){
        const type = $(this).data('type');
        const cls  = type==='action'?'on-action':'on-field';
        const isOn = $(this).hasClass(cls);
        const iOn  = type==='field'?'fa-eye':'fa-check';
        const iOff = type==='field'?'fa-eye-slash':'fa-times';
        $(this).toggleClass(cls,!isOn).find('i').attr('class',`fa fa-solid ${!isOn?iOn:iOff} me-1`);

        changePermission();
    });

    $(document).on('click','.tab-sw', function(){
        const isOn=$(this).hasClass('on');
        $(this).toggleClass('on',!isOn);
        const item=$(this).closest('[data-tab-item]');
        item.toggleClass('border-warning',!isOn);
        item.find('.tab-status').text(!isOn?'Visible':'Hidden');

        //event tab field
        item.find('.f-chip').toggleClass('on',!isOn);
        item.find('.f-chip').find('i').attr('class', !isOn ? 'fa fa-solid fa-eye me-1':'fa fa-solid fa-eye-slash me-1');

        changePermission();
    });

    $(document).on('click', '.f-chip', function(){
        const g = $(this).closest('[data-tab-item]');
        if(g.find('.tab-sw').hasClass('on')){
            const isOn=$(this).hasClass('on');
            $(this).toggleClass('on',!isOn);
            $(this).find('i').attr('class', !isOn ? 'fa fa-solid fa-eye me-1':'fa fa-solid fa-eye-slash me-1');
            changePermission();
        }
    });

    function changePermission(){
        const menu_id   = $('body #builder').attr('data-id');
        let permissions = {};
        $.each($('body #builder .perm-pill'), function(k,v){
            const type  = $(this).attr('data-type');
            const key   = $(this).attr('data-key');

            permissions[`${type}s`] = permissions[`${type}s`] || {};
            permissions[`${type}s`][key] = $(this).hasClass(`on-${type}`)?1:0;
        });

        $.each($('body #builder .tab-sw'), function(k,v){
            const key   = $(this).attr('data-tab');
            const g     = $(this).closest('[data-tab-item]');

            permissions[`tabs`] = permissions[`tabs`] || {};
            permissions[`tabs`][key] = $(this).hasClass(`on`)?1:0;

            if(g.find('.f-chip').length>0){
                permissions[`tab_fields`] = permissions[`tab_fields`] || {};
                permissions[`tab_fields`][key] = permissions[`tab_fields`][key] || {};
                $.each(g.find('.f-chip'), function(k2,v2){
                    const key2 = $(this).attr('data-field');
                    permissions[`tab_fields`][key][key2] = $(this).hasClass(`on`)?1:0;
                });
            }
        });

        $(`body .menu-leaf[data-id="${atob(menu_id)}"]`).find('input').val(btoa(JSON.stringify(permissions)));
    }


    $(document).ready(function(){
        tblAccount = $('#tbl-account').DataTable({
            columnDefs: [{
                    targets: 0,
                    className: 'text-center',
                }, // checkbox
                {
                    targets: 1,
                    className: 'text-center',
                }, // no
                {
                    targets: 2,
                }, // kas/bank
                {
                    targets: 3,
                }, // keterangan
            ],
            autoWidth: false,
            paging: false,
            searching: true,
            ordering: false,
            
        });

        tblWarehouse = $('#tbl-warehouse').DataTable({
            columnDefs: [{
                    targets: 0,
                    className: 'text-center',
                }, // checkbox
                {
                    targets: 1,
                    className: 'text-center',
                }, // no
                {
                    targets: 2,
                }, // warehouse
                {
                    targets: 3,
                    className: 'text-center',
                }, // default
            ],
            autoWidth: false,
            paging: false,
            searching: true,
            ordering: false,
        });

        tblSales = $('#tbl-sales').DataTable({
            columnDefs: [{
                    targets: 0,
                    className: 'text-center',
                }, // checkbox
                {
                    targets: 1,
                    className: 'text-center',
                }, // no
                {
                    targets: 2,
                }, // sales
            ],
            autoWidth: false,
            paging: false,
            searching: true,
            ordering: false,
        });

        updateAccountRowNumber();
        updateWarehouseRowNumber();
        updateSalesRowNumber();
    });
    $(document).on('click', '#addAccount', function(){
        tblAccount.row.add([
            `<input type="checkbox" class="chk">`,
            `<span class="rowNo"></span>`,
            `<select name="account[]" 
                data-url="user/get_accounts"
                data-dropdown-parent="body"
                placeholder="Pilih Kas/Bank"
                class="form-select select-coa"><option value=""></option></select>`,
            `<input type="hidden" name="account_id[]" value="0">
            <textarea class="form-control form-control-sm border-0 coa-note" name="account_note[]" readonly></textarea>`,
        ]).draw(false);
        const $tr = $('#tbl-account tbody tr:last');
        initSelect2($tr.find('.select-coa'));
        updateAccountRowNumber();
    });
    function updateAccountRowNumber() {
        $("#tbl-account tbody tr").each(function(index) {
            $(this).find(".rowNo").text((index + 1));
        });
    }
    $(document).on('click', '#removeAccount', function(){
        $('#tbl-account tbody .chk:checked').each(function(){
            tblAccount.row($(this).closest('tr')).remove().draw();
        });
        $('#chkAll').prop('checked', false);
        updateAccountRowNumber();
    });
    $(document).on('click', '#chkAll', function(){
        const isChecked = $(this).is(':checked');
        $('#tbl-account tbody .chk').prop('checked', isChecked);
    });
    $(document).on('change', '.select-coa', function(){
        const $this = this;
        setTimeout(() => {
            const val = $($this).val();
            if(val){
                // Cek duplikat
                let duplicate = false;
                $('#tbl-account tbody .select-coa').not($this).each(function(){
                    if($(this).val() === val){
                        duplicate = true;
                        return false; // break loop
                    }
                });
                if(duplicate){
                    $($this).val('').trigger('change');
                    Swal.fire({
                        title: 'Warning',
                        text: 'Akun Kas/Bank sudah tersedia!',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    });
                }
            }
        }, 100);      
    });
    $(document).on('change', '.select-coa, .select-warehouse, .select-sales', function(){
        $(this).removeClass('is-invalid').closest('td').find('.text-danger').text('');        
    });

    let $activeNote = null;
    $(document).on('click', '.coa-note', function(){
        $activeNote = $(this);
        $('#noteText').val($activeNote.val());
        $('#modalNote').modal('show');
    });
    $('#modalNote .btn-primary').on('click', function(){
        if($activeNote){
            const newNote = $('#noteText').val();
            $activeNote.val(newNote);
            $('#modalNote').modal('hide');
        }
    });
    $('#modalNote').on('shown.bs.modal', function () {
        $('#noteText').trigger('focus');
    });

    $(document).on('click', '#addWarehouse', function(){
        tblWarehouse.row.add([
            `<input type="checkbox" class="chk">`,
            `<span class="rowNo"></span>`,
            `<select name="warehouses[]" 
                data-url="user/get_warehouses"
                data-dropdown-parent="body"
                placeholder="Pilih Warehouse"
                class="form-select select-warehouse"><option value=""></option></select>`,
            `<input type="hidden" name="warehouses_id[]" value="0">
            <input type="checkbox" name="default_warehouse[]" value="Y" class="form-check-input default-warehouse fs-5">`,
        ]).draw(false);
        const $tr = $('#tbl-warehouse tbody tr:last');
        initSelect2($tr.find('.select-warehouse'));
        updateWarehouseRowNumber();
    });
    function updateWarehouseRowNumber() {
        $("#tbl-warehouse tbody tr").each(function(index) {
            $(this).find(".rowNo").text((index + 1));
        });
    }
    $(document).on('click', '#removeWarehouse', function(){
        $('#tbl-warehouse tbody .chk:checked').each(function(){
            tblWarehouse.row($(this).closest('tr')).remove().draw();
        });
        $('#chkAllWarehouse').prop('checked', false);
        updateWarehouseRowNumber();
    });
    $(document).on('click', '#chkAllWarehouse', function(){
        const isChecked = $(this).is(':checked');
        $('#tbl-warehouse tbody .chk').prop('checked', isChecked);
    });
    $(document).on('change', '.default-warehouse', function(){
        if($(this).is(':checked')){
            $('.default-warehouse').not(this).prop('checked', false);
        }
    });
    $(document).on('change', '.select-warehouse', function(){
        const $this = this;
        setTimeout(() => {
            const val = $($this).val();
            if(val){
                // Cek duplikat
                let duplicate = false;
                $('#tbl-warehouse tbody .select-warehouse').not($this).each(function(){
                    if($(this).val() === val){
                        duplicate = true;
                        return false; // break loop
                    }
                });
                if(duplicate){
                    $($this).val('').trigger('change');
                    Swal.fire({
                        title: 'Warning',
                        text: 'Warehouse sudah tersedia!',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    });
                }
            }
        }, 100);      
    });


    //sales
    $(document).on('click', '#addSales', function(){
        tblSales.row.add([
            `<input type="checkbox" class="chk">`,
            `<span class="rowNo"></span>`,
            `<input type="hidden" name="sales_id[]" value="0">
            <select name="sales[]" 
                data-url="user/get_sales"
                data-dropdown-parent="body"
                placeholder="Pilih Sales"
                class="form-select select-sales"><option value=""></option></select>`,
        ]).draw(false);
        const $tr = $('#tbl-sales tbody tr:last');
        initSelect2($tr.find('.select-sales'));
        updateSalesRowNumber();
    });
    function updateSalesRowNumber() {
        $("#tbl-sales tbody tr").each(function(index) {
            $(this).find(".rowNo").text((index + 1));
        });
    }
    $(document).on('click', '#removeSales', function(){
        $('#tbl-sales tbody .chk:checked').each(function(){
            tblSales.row($(this).closest('tr')).remove().draw();
        });
        $('#chkAllSales').prop('checked', false);
        updateSalesRowNumber();
    });
    $(document).on('click', '#chkAllSales', function(){
        const isChecked = $(this).is(':checked');
        $('#tbl-sales tbody .chk').prop('checked', isChecked);
    });
    $(document).on('change', '.select-sales', function(){
        const $this = this;
        setTimeout(() => {
            const val = $($this).val();
            if(val){
                // Cek duplikat
                let duplicate = false;
                $('#tbl-sales tbody .select-sales').not($this).each(function(){
                    if($(this).val() === val){
                        duplicate = true;
                        return false; // break loop
                    }
                });
                if(duplicate){
                    $($this).val('').trigger('change');
                    Swal.fire({
                        title: 'Warning',
                        text: 'Sales sudah tersedia!',
                        icon: 'warning',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok'
                    });
                }
            }
        }, 100);      
    });

    $('form').on('submit', function(e){
        $('#tbl-warehouse').find('input[type="checkbox"]').each(function() {
            if (!$(this).is(':checked')) {
                // Buat input hidden sementara untuk mengirimkan nilai 
                $(this).after('<input type="hidden" name="' + $(this).attr('name') + '" value="N">');
            }
        });

        HTMLFormElement.prototype.submit.call(this);
    });

    $(document).on("keyup", '#search-menu', function() {
        var value = $(this).val().toLowerCase();
        if (value === "") {
            $('.accordion-item').show();
            $('.menu-leaf').show();
            return; 
        }

        $('.accordion-item').each(function() {
            var $item = $(this);
            var hasMatch = false;

            $item.find('.menu-leaf').each(function() {
                var text = $(this).find('.text-truncate').text().toLowerCase();
                if (text.indexOf(value) > -1) {
                    $(this).show();
                    hasMatch = true;
                } else {
                    $(this).hide();
                }
            });

            if (hasMatch) {
                $item.show();
                var $collapse = $item.find('.accordion-collapse');
                if (!$collapse.hasClass('show')) {
                    $collapse.addClass('show');
                    $item.find('.accordion-button').removeClass('collapsed').attr('aria-expanded', 'true');
                }
            } else {
                $item.hide();
            }
        });
    });
</script>