<!Doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap Css -->
    <link href="<?= base_url() ?>assets/admin/auth/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= base_url() ?>assets/admin/auth/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?= base_url() ?>assets/admin/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert-->
    <link href="<?= base_url() ?>assets/admin/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- App Css-->
    <link href="<?= base_url() ?>assets/admin/auth/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

    <!-- Select2-->
    <link href="<?= base_url() ?>assets/admin/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner-border {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body data-layout="horizontal" data-topbar="dark">
    <div id="loading" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

    <div class="authentication-bg min-vh-100">
        <div class="bg-overlay"></div>
        <div class="container">
            <div class="d-flex flex-column min-vh-100 px-3 pt-4">
                <div class="row justify-content-center my-auto">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p class="text-muted">Enter your credentials to access your account.</p>
                                </div>
                                <div class="p-2 mt-4">
                                    <form action="" method="post">
                                        <div class="mb-3">
                                            <label class="form-label" for="username">Username</label>
                                            <input type="text" class="form-control <?= form_error('username') ? 'is-invalid' : null ?>" name="username" id="username" placeholder="Enter your username" autocomplete="off" value="<?= $this->input->post('username'); ?>" oninput="this.value = this.value.toLowerCase()" onkeypress="return event.charCode != 32">
                                            <span class="text-danger"><?= form_error('username') ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="password">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control <?= form_error('password') ? 'is-invalid' : null ?>" name="password" id="password" placeholder="Enter your password">
                                                <span class="input-group-text">
                                                    <i class="ri ri-eye-close-fill" id="icon-password"></i>
                                                </span>
                                            </div>
                                            <span class="text-danger"><?= form_error('password') ?></span>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary w-sm waves-effect waves-light float-end" type="submit" id="submit">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center text-muted p-4">
                            <p class="text-white-50"> &copy; <?= date('Y') ?> PT Intinusa Sejahtera International &sdot; Team IT</p>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end container -->
    </div>
    <!-- end authentication section -->

    <!-- JAVASCRIPT -->
    <script src="<?= base_url() ?>assets/admin/libs/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/auth/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/auth/libs/metismenujs/metismenujs.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/auth/libs/simplebar/simplebar.min.js"></script>
    <script src="<?= base_url() ?>assets/admin/auth/libs/feather-icons/feather.min.js"></script>
    <!-- Sweet Alerts js -->
    <script src="<?= base_url() ?>assets/admin/libs/sweetalert2/sweetalert2.min.js"></script>
    <!-- Select 2 -->
    <script src="<?= base_url() ?>assets/admin/libs/select2/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#loading').hide();

            $("#submit").click(function() {
                $('#loading').show();
            });

            $('.select2').select2({
                theme: 'classic',
            });

            //Initialize Select2 Elements
            // $('.select2').each(function() {
            //     $(this).select2({
            //         theme: 'bootstrap-5',
            //         dropdownParent: $(this).parent(),
            //     });
            // });

            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
            });

            <?php if ($this->session->flashdata('toastSuccess')) { ?>
                Toast.fire({
                    icon: 'success',
                    title: '<?= $this->session->flashdata('toastSuccess') ?>'
                })
            <?php } elseif ($this->session->flashdata('toastError')) { ?>
                Toast.fire({
                    icon: 'error',
                    title: '<?= $this->session->flashdata('toastError') ?>'
                })
            <?php } elseif ($this->session->flashdata('toastWarning')) { ?>
                Toast.fire({
                    icon: 'warning',
                    title: '<?= $this->session->flashdata('toastWarning') ?>'
                })
            <?php } ?>

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
        });
    </script>
</body>

</html>