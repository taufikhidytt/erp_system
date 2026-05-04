<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url($access['url']) ?>" class="text-decoration-underline"><?= $access['PROMPT'] ?></a>
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
                            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->USER_ID)) ?>">
                            <div class="row mb-2">
                                <div class="col-sm-12 text-end">
                                    <?= button_actions(['insert','save','reload']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="row form-xs">
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="name">User Name:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-barcode-box-fill"></i>
                                                </span>
                                                <input type="text" name="name" id="name" class="form-control <?= form_error('name') ? 'is-invalid' : null; ?>" value="<?= set_value('name',$data->USER_NAME ?? '') ?>" placeholder="Enter DB Name">
                                            </div>
                                            <div class="text-danger"><?= form_error('name') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="password">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-lock-password-fill"></i>
                                                </span>
                                                <input type="password" class="form-control <?= form_error('password') ? 'is-invalid' : null ?>" name="password" id="password" value="<?= set_value('password','') ?>" placeholder="Enter your password" minlength="3">
                                                <span class="input-group-text">
                                                    <i class="ri ri-eye-close-fill show-password"></i>
                                                </span>
                                            </div>
                                            <span class="text-muted">Kosongkan jika tidak ingin mengubah password</span>
                                            <span class="text-danger"><?= form_error('password') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="start_date">Start Date:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-briefcase-fill"></i>
                                                </span>
                                                <input type="datetime-local" name="start_date" id="start_date" class="form-control <?= form_error('start_date') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Start Date" value="<?= set_value('start_date',$data->START_DATE?date('Y-m-d H:i', strtotime($data->START_DATE)):''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('start_date') ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="table-responsive mt-3" style="max-height: 75dvh;">
                                    <table id="tbl-server" class="table mt-3 w-100 table-sm align-middle">
                                        <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
                                            <tr>
                                                <th class="text-center"><input type="checkbox" id="chkAll"></th>
                                                <th class="text-center">No</th>
                                                <th>Nama Server</th>
                                                <th>Nama Alias</th>
                                                <th>Hostname</th>
                                                <th class="text-center">Default</th>
                                                <th class="text-center">Action</th>
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
                                                        <input type="checkbox" name="active_flag[]" class="chk-server" value="Y" <?= set_value("active_flag[$index]",$server->ACTIVE_FLAG ?? "N") == 'Y' ? 'checked' : '' ?>>
                                                    </td>
                                                    <td class="text-center"><?= $index + 1 ?></td>
                                                    <td><?= strtoupper($server->DB_NAME) ?></td>
                                                    <td><?= $server->DB_ALIAS ?></td>
                                                    <td><?= $server->HOSTNAME ?></td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="default_server[]" class="default-server" value="Y" <?= set_value("default_server[$index]", $server->PRIMARY_FLAG ?? 'N') == 'Y' ? 'checked' : '' ?> <?= $server->ACTIVE_FLAG == 'Y' ? '' : 'disabled' ?>>
                                                    </td>
                                                    <td>
                                                        <?php if($server->USER_SERVER_ID && $server->ACTIVE_FLAG == 'Y'){ ?>
                                                            <a href="<?= site_url($access['url'].'/clone/'.base64url_encode($this->encrypt->encode($data->USER_ID.'-'.$server->SERVER_ID.'-'.$server->USER_SERVER_ID))) ?>"
                                                            title="Clone User" data-bs-toggle="tooltip" data-bs-placement="left"
                                                            class="btn btn-sm btn-success"><i class="ri ri-tools-fill"></i></a>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
                    "data" : "alias",
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
                {
                    "data": "action",
                    "orderable": false,
                    "searchable": false,
                    "className" : "text-center",
                    "width" : "100"
                },
            ]
        });

        $('.chk-server').trigger('change');
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
    $('#name').on('input', function() {
        var sanitized = $(this).val().replace(/[^a-zA-Z0-9_]/g, '');
        $(this).val(sanitized.toUpperCase());
    });
    $(document).on('click', 'a', function(){
        setTimeout(function(){
            $('#loading').hide();
        },1000);
    });
</script>