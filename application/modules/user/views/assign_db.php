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
                                    <?= button_actions([
                                        'insert',
                                        [
                                            'key'      => 'assign_database',
                                            'class'    => 'btn-success',
                                            'title'    => 'Simpan',
                                            'icon'     => 'ri-save-3-fill',
                                            'type'     => 'submit',
                                        ],
                                        [
                                            'key'      => 'view',
                                            'redirect' => site_url(strtolower($access['ERP_MENU_NAME']).'/detail/' . base64url_encode($this->encrypt->encode($data->ERP_USER_ID))),
                                            'class'    => 'btn-info',
                                            'title'    => 'Detail',
                                            'icon'     => 'ri-eye-line',
                                        ],

                                        'reload',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card border-2">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Informasi User</h5>
                            <div class="mb-3">
                                <label for="name">Nama User</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-user-fill"></i>
                                    </span>
                                    <input type="text" id="name" class="form-control" value="<?= $data->ERP_USER_NAME ?>" disabled readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="full_name">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-user-fill"></i>
                                    </span>
                                    <input type="text" id="full_name" class="form-control" value="<?= $data->ERP_USER_DESC ?>" disabled readonly>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="group_id">Group</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-user-fill"></i>
                                    </span>
                                    <select name="group_id" id="group_id" class="select2 form-select<?= form_error('group_id') ? ' is-invalid' : null; ?>"
                                        data-url="user/get_group"
                                        data-selected-id="<?= set_value('group_id',$data->ERP_GROUP_ID) ?>"
                                        data-dropdown-parent="body" disabled readonly
                                        ></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-6 col-sm-12">
                    <div class="card mb-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <h6 class="m-0 fw-bold text-dark"><i class="ri ri-database-2-fill me-1"></i>Database Server</h6>
                            <button class="btn btn-link text-secondary p-0 border-0 shadow-none btn-toggle" 
                                    type="button"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#serverContent" 
                                    aria-expanded="true">
                                <i class="ri-arrow-down-s-line ri-xl"></i>
                            </button>
                        </div>

                        <div class="collapse show" id="serverContent" data-prefix="srv">
                            <div class="card-body border-top">
                                <div class="table-responsive" style="max-height: 75dvh;">
                                    <table id="tbl-server" class="table mt-3 w-100 table-sm align-middle">
                                        <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                            <tr>
                                                <th class="text-center"><input type="checkbox" id="chkAll"></th>
                                                <th class="text-center">No</th>
                                                <th>Nama Server</th>
                                                <th>Hostname</th>
                                                <th class="text-center">Default</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($servers as $index => $server):
                                                $server_id      = base64url_encode($this->encrypt->encode($server->SERVER_ID));
                                                $user_server_id = base64url_encode($this->encrypt->encode($server->USER_SERVER_ID??0));
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="hidden" name="servers[]" value="<?= $server_id ?>">
                                                        <input type="hidden" name="user_servers[]" value="<?= $user_server_id ?>">
                                                        <input type="checkbox" name="active_flag[]" class="chk-server" value="Y" <?= $server->ACTIVE_FLAG == 'Y' ? 'checked' : '' ?>>
                                                    </td>
                                                    <td class="text-center"><?= $index + 1 ?></td>
                                                    <td><?= strtoupper($server->DB_NAME) ?></td>
                                                    <td><?= $server->HOSTNAME ?></td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="default_server[]" class="default-server" value="Y" <?= $server->PRIMARY_FLAG == 'Y' ? 'checked' : '' ?> <?= $server->ACTIVE_FLAG == 'Y' ? '' : 'disabled' ?>>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
    let tbl = null;
    $(document).ready(function(){
        tbl = $('#tbl-server').DataTable({
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": false,
            "ordering": true,
            "order": [],
            "columns": [
                {
                    "data": "chk",
                    "orderable": false,
                    "searchable": false,
                    "className" : "text-center",
                    "width" : "80"
                },
                {
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "className" : "text-center",
                    "width" : "30"
                },
                {
                    "data" : "db_name",
                },
                {
                    "data" : "host",
                },
                {
                    "data": "default",
                    "orderable": false,
                    "searchable": false,
                    "className" : "text-center",
                    "width" : "80"
                },
            ]
        });

        $('.default-server').trigger('change');
    });

    $('#chkAll').on('click', function(){
        if($(this).is(':checked')){
            $('.chk-server').prop('checked', true).trigger('change');
        }else{
            $('.chk-server').prop('checked', false).trigger('change');
        }
    });
    $(document).on('click change','.chk-server', function(){
        const is_checked = $(this).is(':checked');
        const tr = $(this).closest('tr');
        if(is_checked){
            tr.find('.default-server').attr('disabled',false);
        }else{
            $('#chkAll').prop('checked',false);
            tr.find('.default-server').prop('checked',false).attr('disabled',true);
        }
    });
    $(document).on('click', '.default-server', function(){
        var $context = $(this).closest('tbody');
        $context.find('.default-server').not(this).prop('checked', false);
    });

    $('form').on('submit', function(e){
        $('#tbl-server').find('input[type="checkbox"]').each(function() {
            if (!$(this).is(':checked')) { 
                $(this).after('<input type="hidden" name="' + $(this).attr('name') + '" value="N">');
            }
        });

        HTMLFormElement.prototype.submit.call(this);
    });
</script>