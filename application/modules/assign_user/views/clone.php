<?php $this->load->view('user/style'); ?>
<style>
    .info-strip{display:flex;align-items:stretch;border-radius:10px;overflow:hidden;border:1.5px solid #dee2e6;transition:border-color .3s;}
    .info-strip.s-u{border-color:#B5D4F4;}
    .info-strip.s-c{border-color:#9FE1CB;}

    .db-side{display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fff;border-right:1.5px solid #dee2e6;transition:border-color .3s;white-space:nowrap;min-width:0;}
    .info-strip.s-u .db-side{border-color:#B5D4F4;}
    .info-strip.s-c .db-side{border-color:#9FE1CB;}
    .db-pulse{width:8px;height:8px;border-radius:50%;background:#639922;flex-shrink:0;animation:pulse 1.8s ease-in-out infinite;}
    @keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(99,153,34,.5);}50%{box-shadow:0 0 0 5px rgba(99,153,34,0);}}
    .db-name{font-size:12px;font-weight:600;color:#0C447C;white-space:nowrap;}
    .db-label{font-size:10px;color:#888780;white-space:nowrap;}

    .status-side{display:flex;align-items:center;gap:10px;padding:10px 16px;flex:1;transition:background .35s;}
    .s-u .status-side{background:#E6F1FB;}
    .s-c .status-side{background:#E1F5EE;}
    .s-icon{width:30px;height:30px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .3s;}
    .s-u .s-icon{background:#185FA5;}
    .s-c .s-icon{background:#1D9E75;}
    .s-title{font-weight:600;font-size:12px;margin:0 0 1px;transition:color .3s;}
    .s-u .s-title{color:#0C447C;}
    .s-c .s-title{color:#085041;}
    .s-desc{font-size:11px;margin:0;transition:color .3s;}
    .s-u .s-desc{color:#185FA5;}
    .s-c .s-desc{color:#0F6E56;}

    @media (max-width: 640px) {
        .info-strip{
            display: block !important;
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
                            <li class="breadcrumb-item">
                                <a href="<?= base_url($access['url'].'/detail/'.base64_encode($this->encrypt->encode($data->USER_ID))) ?>" class="text-decoration-underline"><?= $data->USER_NAME ?></a>
                            </li>
                            <li class="breadcrumb-item active text-decoration-underline"><?= $breadcrumb ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <form action="" method="post" id="myForm">
            <input type="hidden" name="id" value="<?= base64url_encode($this->encrypt->encode($data->USER_SERVER_ID)); ?>">
            <div class="row">
                <div class="col-12">
                    <div class="card border-2">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <div class="info-strip<?= $user?' s-u':' s-c' ?> mb-3" style="flex:1;min-width:220px;max-width:530px;">
                                        <div class="db-side">
                                            <span class="db-pulse"></span>
                                            <div>
                                                <div class="db-name"><?= $data->DB_NAME ?></div>
                                                <?= $data->DB_ALIAS ? '<div class="db-name">('.$data->DB_ALIAS.')</div>':''; ?>
                                                <div class="db-label">Active Server</div>
                                            </div>
                                        </div>
                                        <?php if($user){ ?>
                                            <div class="status-side">
                                                <div class="s-icon">
                                                    <i class="ri ri-edit-line text-white"></i>
                                                </div>
                                                <div>
                                                    <p class="s-title">The user was found in this database</p>
                                                    <p class="s-desc" id="sDesc">The data will be updated when saved</p>
                                                </div>
                                            </div>
                                        <?php } else{ ?>
                                            <div class="status-side">
                                                <div class="s-icon">
                                                    <i class="ri ri-add-line text-white"></i>
                                                </div>
                                                <div>
                                                    <p class="s-title">The user was not found in this database</p>
                                                    <p class="s-desc">New data will be created (CREATE) when saved</p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 text-end">
                                    <?= button_actions(['insert',
                                        [
                                            'key'      => 'update',
                                            'class'    => 'btn-success',
                                            'title'    => 'Save',
                                            'icon'     => 'ri-save-3-fill',
                                            'type'     => 'submit',
                                            'needs_auth'=> true,
                                        ],
                                        [
                                            'key'      => 'view',
                                            'redirect' => site_url(strtolower($access['ERP_MENU_NAME']).'/detail/' . base64url_encode($this->encrypt->encode($data->USER_ID))),
                                            'class'    => 'btn-info',
                                            'title'    => 'Detail',
                                            'icon'     => 'ri-eye-line',
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
                                                <input type="text" class="form-control" id="name" value="<?= $data->USER_NAME ?>" disabled readonly>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="full_name">Full Name:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-file-user-fill"></i>
                                                </span>
                                                <input type="text" name="full_name" id="full_name" class="form-control <?= form_error('full_name') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Full Name" value="<?= set_value('full_name', $user->ERP_USER_DESC ?? ''); ?>">
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
                                                    data-url="assign_user/get_group/<?= base64url_encode($this->encrypt->encode($data->USER_SERVER_ID)) ?>"
                                                    data-selected-id="<?= set_value('group_id',$user->ERP_GROUP_ID ?? '') ?>"
                                                    data-dropdown-parent="body"
                                                    ></select>
                                            </div>
                                            <div class="text-danger"><?= form_error('group_id') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="divisi_id">DIVISION:</label>
                                            <span class="text-danger">*</span>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-group-fill"></i>
                                                </span>
                                                <select name="divisi_id" id="divisi_id" class="select2 form-select<?= form_error('divisi_id') ? ' is-invalid' : null; ?>"
                                                    data-url="assign_user/get_divisi/<?= base64url_encode($this->encrypt->encode($data->USER_SERVER_ID)) ?>"
                                                    data-selected-id="<?= set_value('divisi_id', $user->DIVISI_ID ?? '') ?>"
                                                    data-dropdown-parent="body"
                                                    ></select>
                                            </div>
                                            <div class="text-danger"><?= form_error('divisi_id') ?></div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12">
                                        <div class="mb-3">
                                            <label for="title">Position:</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-briefcase-fill"></i>
                                                </span>
                                                <input type="text" name="title" id="title" class="form-control <?= form_error('title') ? 'is-invalid' : null; ?>" 
                                                    placeholder="Position" value="<?= set_value('title',$user->TITLE ?? ''); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('title') ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="password">Password</label>
                                            <?= (!$data->PASSWORD && !$user) ? '<span class="text-danger">*</span>' :'' ?>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="ri ri-lock-password-fill"></i>
                                                </span>
                                                <input type="password" class="form-control <?= form_error('password') ? 'is-invalid' : null ?>" name="password" id="password" placeholder="Enter your password" minlength="3">
                                                <span class="input-group-text">
                                                    <i class="ri ri-eye-close-fill show-password"></i>
                                                </span>
                                            </div>
                                            <?= (!$data->PASSWORD && !$user) ? '' :'<span class="text-muted">Leave blank if you don`t want to change your password</span>' ?>
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
                                                    placeholder="Start Date" value="<?= set_value('start_date', $user ? date('Y-m-d H:i', strtotime($user->START_DATE)) : ($data->START_DATE ? date('Y-m-d H:i', strtotime($data->START_DATE)) :'')); ?>">
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
                                                    placeholder="End Date" value="<?= set_value('end_date', (!$user || $user->END_DATE=='9999-12-31 00:00:00' ? null : $user->END_DATE)); ?>">
                                            </div>
                                            <div class="text-danger"><?= form_error('end_date') ?></div>
                                        </div>
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