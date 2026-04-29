<style>
    /* Transisi halus untuk icon */
    .btn-toggle i {
        display: inline-block;
        transition: transform 0.3s ease;
    }

    /* Saat tertutup, putar icon */
    .btn-toggle.collapsed i {
        transform: rotate(-180deg);
    }

    /* Menghilangkan outline biru saat diklik (opsional) */
    .btn-toggle:focus {
        box-shadow: none;
        outline: none;
    }
    .badge-remove {
        cursor: pointer;
        display: inline-flex;
        transition: all 0.2s ease;
        padding: 2px;
    }

    .badge-remove:hover {
        color: #9093a3 !important;
        opacity: 1;
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
                                <a href="<?= base_url('management_menu') ?>" class="text-decoration-underline">Management Menu</a>
                            </li>
                            <li class="breadcrumb-item active text-decoration-underline"><?= $breadcrumb ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <form action="" method="post">
            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($menu->ERP_MENU_ID)) ?>">
            <div class="row">
                <div class="text-end mb-3">
                    <button type="submit" class="btn btn-success btn-sm" name="submit" id="submit" data-toggle="tooltip" data-placement="bottom" title="Simpan">
                        <i class="ri ri-save-3-fill"></i>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="window.location.replace(window.location.pathname);" data-toggle="tooltip" data-placement="bottom" title="Reload">
                        <i class="ri ri-reply-fill"></i>
                    </button>
                </div>
                <div class="col">
                    <div class="card border-2">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Informasi Menu</h5>
                            <div class="mb-3">
                                <label for="name">Menu Name</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-barcode-box-fill"></i>
                                    </span>
                                    <input type="text" id="name" class="form-control" value="<?= $menu->ERP_MENU_NAME ?>" disabled readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="prmopt">Prompt</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-barcode-box-fill"></i>
                                    </span>
                                    <input type="text" id="prmopt" class="form-control" value="<?= $menu->PROMPT ?>" disabled readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="active_flag">Active Flag</label>
                                <div class="input-group">
                                    <div class="form-check form-switch mb-3" dir="ltr">
                                        <input type="checkbox" class="form-check-input" id="active_flag"<?= $menu->ACTIVE_FLAG=='Y'?" checked":'' ?> disabled>
                                        <label class="form-check-label"><?= $menu->ACTIVE_FLAG=='Y'?"Active":' Inactive' ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 col-sm-12">
                    <div class="card mb-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-dark">Actions</h6>
                            <button class="btn btn-link text-secondary p-0 border-0 shadow-none btn-toggle" 
                                    type="button"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#actionsContent" 
                                    aria-expanded="true">
                                <i class="ri-arrow-down-s-line ri-xl"></i>
                            </button>
                        </div>

                        <div class="collapse show" id="actionsContent" data-prefix="act">
                            <div class="card-body border-top">
                                <div class="mb-3 result">
                                    <?php if(isset($permissions['actions'])){
                                        foreach ($permissions['actions'] as $v) { 
                                            $label = str_replace('_',' ',$v);
                                            ?>
                                            <span class="badge bg-primary fw-bold fs-6 me-1 d-items text-capitalize">
                                                <?= $label ?>
                                                <span class="badge-remove remove-actions ms-1"><i class="fa fa-times"></i></span>
                                                <input type="hidden" class="val-permissions" name="act[]" value="<?= $v ?>">
                                            </span>
                                    <?php } } ?>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control input-permissions" placeholder="Enter Actions Permissions">
                                    <button class="btn btn-outline-secondary btn-add" type="button">+ Add</button>
                                </div>
                                <div class="form-text">Example : Approval / Export PDF / Export Excel</div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-dark">Fields</h6>
                            <button class="btn btn-link text-secondary p-0 border-0 shadow-none btn-toggle" 
                                    type="button"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#fieldContent" 
                                    aria-expanded="true">
                                <i class="ri-arrow-down-s-line ri-xl"></i>
                            </button>
                        </div>

                        <div class="collapse show" id="fieldContent"  data-prefix="field">
                            <div class="card-body border-top">
                                <div class="mb-3 result">
                                    <?php if(isset($permissions['fields'])){
                                        foreach ($permissions['fields'] as $v) { 
                                            $label = str_replace('_',' ',$v);
                                            ?>
                                            <span class="badge bg-primary fw-bold fs-6 me-1 d-items text-capitalize">
                                                <?= $label ?>
                                                <span class="badge-remove remove-actions ms-1"><i class="fa fa-times"></i></span>
                                                <input type="hidden" class="val-permissions" name="field[]" value="<?= $v ?>">
                                            </span>
                                    <?php } } ?>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control input-permissions" placeholder="Enter Fields">
                                    <button class="btn btn-outline-secondary btn-add" type="button">+ Add</button>
                                </div>
                                <div class="form-text">Example : Item Code / Item Name / Price</div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-dark">Tabs</h6>
                            <button class="btn btn-link text-secondary p-0 border-0 shadow-none btn-toggle" 
                                    type="button"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#tabContent" 
                                    aria-expanded="true">
                                <i class="ri-arrow-down-s-line ri-xl"></i>
                            </button>
                        </div>

                        <div class="collapse show" id="tabContent" data-prefix="tab">
                            <div class="card-body border-top">
                                <div class="input-group">
                                    <input type="text" class="form-control input-permissions" placeholder="Enter Tab Name">
                                    <button class="btn btn-outline-secondary btn-add" type="button">+ Add</button>
                                </div>
                                <div class="form-text">Example : Stock / Summary Stock / Stock Card</div>

                                <div class="mt-3 result">
                                    <?php if(isset($permissions['tabs'])){
                                        $i = 0;
                                        foreach ($permissions['tabs'] as $k => $v) { 
                                            $i++;
                                            $val    = is_array($v)?$k:$v;
                                            $label  = str_replace('_',' ',$val);
                                            ?>
                                            <div class="card mb-3 d-items">
                                                <div class="card-header d-flex justify-content-between align-items-center py-3">
                                                    <h6 class="m-0 fw-bold text-dark text-capitalize">Tab <?= $label ?></h6>
                                                    <input type="hidden" class="val-permissions" name="tab[]" value="<?= $val ?>">
                                                    <div class="d-flex align-items-center">
                                                        <button class="btn btn-link text-secondary p-0 border-0 shadow-none btn-toggle" 
                                                                type="button"
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#fieldContent<?= $i ?>" 
                                                                aria-expanded="true"
                                                                style="width: 30px; height: 30px;">
                                                            <i class="ri-arrow-down-s-line ri-xl" style="line-height: 1;"></i>
                                                        </button>
                                                        <span class="badge-remove remove-actions ms-1"><i class="fa fa-times"></i></span>
                                                    </div>
                                                </div>

                                                <div class="collapse show" id="fieldContent<?= $i ?>" data-prefix="field" data-tab_field="<?= $val ?>">
                                                    <div class="card-body border-top">
                                                        <div class="mb-3 result">
                                                            <?php if(is_array($v)){
                                                                foreach ($v as $v2) { 
                                                                    $label2 = str_replace('_',' ',$v2);
                                                                    ?>
                                                                    <span class="badge bg-primary fw-bold fs-6 me-1 d-items text-capitalize">
                                                                        <?= $label2 ?>
                                                                        <span class="badge-remove remove-actions ms-1"><i class="fa fa-times"></i></span>
                                                                        <input type="hidden" class="val-permissions" name="tab_field[<?= $val ?>][]" value="<?= $v2 ?>">
                                                                    </span>
                                                            <?php } } ?>
                                                        </div>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control input-permissions" placeholder="Enter Fields">
                                                            <button class="btn btn-outline-secondary btn-add" type="button">+ Add</button>
                                                        </div>
                                                        <div class="form-text">Example : Item Code / Item Name / Price</div>
                                                    </div>
                                                </div>
                                            </div>
                                    <?php } } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    let counter = 0;
    $(document).on('click keydown', '.btn-add, .input-permissions', function(e) {
        if ((e.type === 'click' && $(this).attr('type') == 'button') || e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            const $parent = $(this).closest('.collapse');
            
            if ($parent.data('loading')) return;

            $parent.data('loading', true);
            add_permission($parent);
            
            setTimeout(() => $parent.data('loading', false), 500);
        }
    });
    function add_permission($parent){
        const $input    = $parent.find('.input-permissions');
        let   label     = $input.val().trim();

        if(!label) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: `Data is empty`,
            });
            return false;
        }

        label = label.replace(/[^a-zA-Z0-9 _]/g, '');
        const val = label.toLocaleLowerCase();
        const prefix = $parent.attr('data-prefix');
        const is_duplicate = $parent.find(`.val-permissions[value="${val}"]`).length;
        if(is_duplicate>0){
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: `Data ${label} already exists`,
            });
            return false;
        }

        if(prefix == 'tab'){
            append_tabs($parent,{label : label, val : val, prefix : prefix});
        }else{
            let nm = `${prefix}[]`;
            if($parent.attr('data-tab_field')){
                nm = `tab_field[${$parent.attr('data-tab_field')}][]`;
            }
            $parent.find('.result').append(`<span class="badge bg-primary fw-bold fs-6 me-1 d-items text-capitalize">
                ${label}
                <span class="badge-remove remove-actions ms-1"><i class="fa fa-times"></i></span>
                <input type="hidden" class="val-permissions" name="${nm}" value="${val}">
            </span>`);
        }
        $input.val('');
    }

    function append_tabs($parent, param){
        counter++;
        $parent.find('.result:first').append(`<div class="card mb-3 d-items">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
                <h6 class="m-0 fw-bold text-dark text-capitalize">Tab ${param.label}</h6>
                <input type="hidden" class="val-permissions" name="${param.prefix}[]" value="${param.val}">
                <div class="d-flex align-items-center">
                    <button class="btn btn-link text-secondary p-0 border-0 shadow-none btn-toggle" 
                            type="button"
                            data-bs-toggle="collapse" 
                            data-bs-target="#fieldContent${counter}" 
                            aria-expanded="true"
                            style="width: 30px; height: 30px;">
                        <i class="ri-arrow-down-s-line ri-xl" style="line-height: 1;"></i>
                    </button>
                    <span class="badge-remove remove-actions ms-1"><i class="fa fa-times"></i></span>
                </div>
            </div>

            <div class="collapse show" id="fieldContent${counter}" data-prefix="field" data-tab_field="${param.val}">
                <div class="card-body border-top">
                    <div class="mb-3 result"></div>
                    <div class="input-group">
                        <input type="text" class="form-control input-permissions" placeholder="Enter Fields">
                        <button class="btn btn-outline-secondary btn-add" type="button">+ Add</button>
                    </div>
                    <div class="form-text">Example : Item Code / Item Name / Price</div>
                </div>
            </div>
        </div>`);
    }

    $(document).on('click', '.remove-actions', function(){
        $(this).closest('.d-items').remove();
    });
</script>