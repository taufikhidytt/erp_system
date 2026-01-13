<div id="flashSuccess" data-success="<?= $this->session->flashdata('success'); ?>"></div>
<div id="flashWarning" data-warning="<?= $this->session->flashdata('warning'); ?>"></div>
<div id="flashError" data-error="<?= $this->session->flashdata('error'); ?>"></div>

<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h1><?= $title ?></h1>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="">
                                    <?= $heading ?>
                                </a>
                            </li>
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
                        <div class="card-title">
                            <div class="row">
                                <form action="" method="post" id="formProfile" enctype="multipart/form-data">
                                    <input type="hidden" name="id" id="id" value="<?= $this->encrypt->encode($data->id) ?>">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="mb-3">
                                                <label for="nama"><?= $this->lang->line('usersName'); ?></label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-user-fill"></i>
                                                    </span>
                                                    <input type="text" name="nama" id="nama" class="form-control <?= form_error('nama') ? 'is-invalid' : null ?>" placeholder="<?= $this->lang->line('usersName'); ?>" value="<?= $this->input->post('nama') ?? $data->nama; ?>" autocomplete="off">
                                                </div>
                                                <div class="text-danger"><?= form_error('nama') ?></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="mb-3">
                                                <label for="email">Email</label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-mail-fill"></i>
                                                    </span>
                                                    <input type="text" name="email" id="email" class="form-control <?= form_error('email') ? 'is-invalid' : null ?>" placeholder="Email" value="<?= $this->input->post('email') ?? $data->email; ?>" autocomplete="off">
                                                </div>
                                                <span><?= $this->lang->line('noteEmail'); ?></span>
                                                <div class="text-danger"><?= form_error('email') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="mb-3">
                                                <label for="password">Password</label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-lock-fill"></i>
                                                    </span>
                                                    <input type="password" name="password" id="password" class="form-control <?= form_error('password') ? 'is-invalid' : null ?>" placeholder="Password" autocomplete="off">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-eye-close-fill" id="icon-password"></i>
                                                    </span>
                                                </div>
                                                <span><?= $this->lang->line('notePassword'); ?></span>
                                                <div class="text-danger"><?= form_error('password') ?></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="mb-3">
                                                <label for="passKon"><?= $this->lang->line('confirmPassword'); ?></label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-lock-fill"></i>
                                                    </span>
                                                    <input type="password" name="passKon" id="passKon" class="form-control <?= form_error('passKon') ? 'is-invalid' : null ?>" placeholder="<?= $this->lang->line('confirmPassword'); ?>" autocomplete="off">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-eye-close-fill" id="icon-passwordKon"></i>
                                                    </span>
                                                </div>
                                                <span><?= $this->lang->line('notePassword'); ?></span>
                                                <div class="text-danger"><?= form_error('passKon') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="mb-3">
                                                <label for="no_hp">No HP</label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-phone-fill"></i>
                                                    </span>
                                                    <input type="number" name="no_hp" id="no_hp" class="form-control <?= form_error('no_hp') ? 'is-invalid' : null ?>" placeholder="No HP" value="<?= $this->input->post('no_hp') ?? $data->no_hp; ?>" autocomplete="off">
                                                </div>
                                                <div class="text-danger"><?= form_error('no_hp') ?></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="mb-3">
                                                <label for="divisi"><?= $this->lang->line('divisionName'); ?></label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-user-star-fill"></i>
                                                    </span>
                                                    <input type="text" name="divisi" id="divisi" class="form-control <?= form_error('divisi') ? 'is-invalid' : null ?>" value="<?= strtoupper($data->nama_divisi) ?>" placeholder="<?= $this->lang->line('divisionName'); ?>" disabled>
                                                </div>
                                                <div class="text-danger"><?= form_error('divisi') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="mb-3">
                                                <label for="company"><?= $this->lang->line('companyName'); ?></label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-building-fill"></i>
                                                    </span>
                                                    <input type="text" name="company" id="company" class="form-control <?= form_error('company') ? 'is-invalid' : null ?>" value="<?= strtoupper($data->nama_company) ?>" placeholder="<?= $this->lang->line('companyName'); ?>" disabled>
                                                </div>
                                                <div class="text-danger"><?= form_error('company') ?></div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-12 col-sm-12">
                                            <div class="mb-3">
                                                <label for="jabatan"><?= $this->lang->line('position'); ?></label>
                                                <span class="text-danger">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="ri ri-user-follow-fill"></i>
                                                    </span>
                                                    <input type="text" name="jabatan" id="jabatan" class="form-control <?= form_error('jabatan') ? 'is-invalid' : null ?>" value="<?= strtoupper($data->nama_position) ?>" placeholder="<?= $this->lang->line('position'); ?>" disabled>
                                                </div>
                                                <div class="text-danger"><?= form_error('jabatan') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-lg-6 col-sm-12">
                                            <label for="photo">Photo Profile :</label>
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <i class="ri ri-image-fill"></i>
                                                </div>
                                                <input type="file" name="photo" id="photo" class="form-control" onchange="readURL(this);" accept="image/gif,image/jpeg,image/png">
                                            </div>
                                            <div id="error-photo" class="invalid-feedback"></div>
                                            <span class="text-warning">
                                                *format: jpg,png,jpeg <br>
                                                *max size: 500 kb
                                            </span>
                                        </div>
                                        <div class="form-group col-lg-6 col-sm-12">
                                            <div class="input-group">
                                                <?php if ($data->photo == null) { ?>
                                                    <img class="img-fluid img-bordered w-50" src="<?= base_url('assets/upload/photo-profile/default.jpg') ?>" id="image">
                                                <?php } else { ?>
                                                    <img class="img-fluid img-bordered w-50" src="<?= base_url('assets/upload/photo-profile/' . $data->photo) ?>" id="image">
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <span class="text-warning"><?= $this->lang->line('noteRequired'); ?> <span class="text-danger">(*)</span></span>
                                        </div>
                                    </div>
                                    <button type="submit" id="submit" class="btn btn-success btn-sm">Save</button>
                                </form>
                            </div>
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
    $(document).ready(function() {
        $('#loading').hide();

        $("#submit").click(function() {
            $('#loading').show();
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

        $('#icon-password').on('click', function() {
            var passwordField = $('#password');
            var passwordFieldType = passwordField.attr('type');
            if (passwordFieldType === 'password') {
                passwordField.attr('type', 'text');
                $(this).removeClass('ri-eye-close-fill').addClass('ri-eye-fill');
            } else {
                passwordField.attr('type', 'password');
                $(this).removeClass('ri-eye-fill').addClass('ri-eye-close-fill');
            }
        });

        $('#icon-passwordKon').on('click', function() {
            var passwordField = $('#passKon');
            var passwordFieldType = passwordField.attr('type');
            if (passwordFieldType === 'password') {
                passwordField.attr('type', 'text');
                $(this).removeClass('ri-eye-close-fill').addClass('ri-eye-fill');
            } else {
                passwordField.attr('type', 'password');
                $(this).removeClass('ri-eye-fill').addClass('ri-eye-close-fill');
            }
        });

        //Initialize Select2 Elements
        $('.select2').each(function() {
            $(this).select2({
                theme: 'bootstrap-5',
                dropdownParent: $(this).parent(),
            });
        });
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $('#image').attr('src', e.target.result);
                $('#image').show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>