<!Doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title><?= $title ?></title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/logo/logo.png') ?>">
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

    <link rel="stylesheet" href="<?= base_url() ?>assets/admin/css/aos.css">

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
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes slideInUp   { from { opacity:0; transform:translateY(16px); }  to { opacity:1; transform:translateY(0); } }
        @keyframes slideInDown { from { opacity:0; transform:translateY(-16px); } to { opacity:1; transform:translateY(0); } }
        @keyframes fadeOutUp   { to { opacity:0; transform:translateY(-12px); } }
        @keyframes fadeOutDown { to { opacity:0; transform:translateY(12px); } }

        .slide-in-up   { animation: slideInUp   0.32s cubic-bezier(.4,0,.2,1) both; }
        .slide-in-down { animation: slideInDown 0.32s cubic-bezier(.4,0,.2,1) both; }
        .fade-out-up   { animation: fadeOutUp   0.2s ease both; }
        .fade-out-down { animation: fadeOutDown 0.2s ease both; }

        .step-dots span {
            display: inline-block; width: 8px; height: 8px; border-radius: 50%;
            background: #dee2e6; margin: 0 3px; transition: background 0.3s, transform 0.3s;
        }
        .step-dots span.active { background: #0d6efd; transform: scale(1.3); }
        .step-dots span.done   { background: #198754; }
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
                        <div class="card" data-aos="fade-down" data-aos-duration="1500">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p class="text-muted">Enter your credentials to access your account.</p>
                                </div>
                                <div class="p-2 mt-4">
                                    <div class="step-dots text-center mb-4">
                                        <span class="active" id="dot-1"></span>
                                        <span id="dot-2"></span>
                                        <span id="dot-3"></span>
                                    </div>

                                    <div id="alert-box"></div>
                                    <form id="login-form" autocomplete="off" method="post">
                                        <div id="step-1" class="step-block">
                                            <label class="form-label fw-medium" for="username">
                                                <i class="ri ri-at-fill text-muted me-1"></i>Username
                                            </label>
                                            <div class="input-group">
                                                <input type="text" class="form-control<?= form_error('username') ? ' is-invalid' : null ?>" id="username" name="username"
                                                    placeholder="Enter your username"
                                                    autocomplete="off" spellcheck="false"
                                                    oninput="this.value=this.value.toLowerCase()"
                                                    onkeypress="return event.charCode!=32">
                                                <button class="btn btn-primary px-3" type="button" id="btn-check-user">
                                                    <span id="btn-check-label"><i class="ri ri-arrow-right-fill"></i></span>
                                                </button>
                                            </div>
                                            <span class="text-danger"><?= form_error('username') ?></span>
                                            <div class="form-text text-muted mt-1">
                                                <i class="ri ri-information-fill me-1"></i>Type your username then click the arrow or press Enter
                                            </div>
                                        </div>

                                        <div id="step-2" class="step-block d-none">
                                            <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 mb-3">
                                                <div class="bg-success bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px;min-width:34px">
                                                    <i class="ri ri-user-follow-line text-success" style="font-size:14px"></i>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="fw-medium small" id="badge-username"></div>
                                                    <div class="text-muted" style="font-size:11px">Username found</div>
                                                </div>
                                                <i class="ri ri-checkbox-circle-fill text-success"></i>
                                            </div>

                                            <label class="form-label fw-medium" for="server">
                                                <i class="ri ri-hard-drive-fill me-1 text-primary"></i>Select Server
                                            </label>
                                            <select class="form-select" id="server" name="server">
                                                <option value="">-- Select available server --</option>
                                            </select>
                                            <div class="form-text text-muted mt-1">
                                                <i class="ri ri-information-fill me-1"></i>Select the server you want to access
                                            </div>

                                            <div class="d-flex gap-2 mt-3">
                                                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" id="btn-back-1" style="min-width:90px">
                                                    <i class="ri ri-arrow-left-fill me-1"></i>Back
                                                </button>
                                                <button type="button" class="btn btn-primary flex-fill d-inline-flex align-items-center justify-content-center" id="btn-next-server" disabled>
                                                    Continue <i class="ri ri-arrow-right-fill ms-1"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div id="step-3" class="step-block d-none">
                                            <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 mb-3">
                                                <div class="bg-primary bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width:34px;height:34px;min-width:34px">
                                                    <i class="ri-server-fill text-primary" style="font-size:14px"></i>
                                                </div>
                                                <div class="flex-fill">
                                                    <div class="fw-medium small" id="badge-server"></div>
                                                    <div class="text-muted" style="font-size:11px">
                                                        <span id="badge-username-3"></span>
                                                    </div>
                                                </div>
                                                <i class="ri-checkbox-circle-fill text-primary"></i>
                                            </div>

                                            <label class="form-label fw-medium" for="password">
                                                <i class="ri-lock-line me-1 text-primary"></i>Password
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="ri-key-2-line text-muted"></i>
                                                </span>
                                                <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password" name="password" placeholder="Enter your password">
                                                <span class="input-group-text" style="cursor: pointer;">
                                                    <i class="ri ri-eye-close-fill" id="icon-password"></i>
                                                </span>
                                            </div>

                                            <div class="d-flex gap-2 mt-3">
                                                <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" id="btn-back-2" style="min-width:90px">
                                                    <i class="ri-arrow-left-line me-1"></i>Back
                                                </button>
                                                <button type="submit" class="btn btn-success flex-fill fw-medium d-inline-flex align-items-center justify-content-center" id="btn-submit">
                                                    <i class="ri-login-box-line me-2"></i>Sign In
                                                </button>
                                            </div>
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

    <script src="<?= base_url() ?>assets/admin/js/pages/aos.js"></script>
    <script>AOS.init();</script>

    <script>
    $(document).ready(function () {
        let xhr = null;
        let serverCount = 0;
        let focusUsername = false;
        $('#username').focus();

        $('#loading').hide();
        $('.select2').select2({ theme: 'classic' });

        const Toast = Swal.mixin({
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 5000, timerProgressBar: true,
        });
        <?php if ($this->session->flashdata('toastSuccess')): ?>
            Toast.fire({ icon: 'success', title: '<?= $this->session->flashdata('toastSuccess') ?>' });
        <?php elseif ($this->session->flashdata('toastError')): ?>
            Toast.fire({ icon: 'error', title: '<?= $this->session->flashdata('toastError') ?>' });
        <?php elseif ($this->session->flashdata('toastWarning')): ?>
            Toast.fire({ icon: 'warning', title: '<?= $this->session->flashdata('toastWarning') ?>' });
        <?php endif; ?>

        function showAlert(type, msg, icon) {
            var icons = { success: 'ri-checkbox-circle-fill', danger: 'ri-close-circle-fill', warning: 'ri-information-fill' };
            var ic = icon || icons[type] || 'ri-information-fill';
            $('#alert-box').html(
                '<div class="alert alert-' + type + ' d-flex align-items-center gap-2 py-2 small mb-3 slide-in-up" role="alert">' +
                '<i class="ri ' + ic + ' flex-shrink-0"></i><div>' + msg + '</div></div>'
            );
        }
        function clearAlert() { $('#alert-box').empty(); }

        function setDot(step) {
            for (var i = 1; i <= 3; i++) {
                $('#dot-' + i).removeClass('active done')
                    .addClass(i < step ? 'done' : i === step ? 'active' : '');
            }
        }

        function goToStep(from, to, direction) {
            var outClass = direction === 'back' ? 'fade-out-down' : 'fade-out-up';
            var inClass  = direction === 'back' ? 'slide-in-down' : 'slide-in-up';
            var $from = $('#step-' + from);
            $from.addClass(outClass);
            setTimeout(function () {
                $from.addClass('d-none').removeClass(outClass);
                var $to = $('#step-' + to).removeClass('d-none').addClass(inClass);
                setTimeout(function () { 
                    $to.removeClass(inClass);
                    $to.find('input, select').focus();
                }, 350);
                setDot(to);
            }, 200);
        }

        function abortXhr() { if (xhr) { xhr.abort(); xhr = null; } }

        function resetStep1() {
            $('#username').removeClass('is-valid is-invalid');
            $('#server').empty();
            $('#btn-next-server').prop('disabled', true);
            serverCount = 0;
        }

        function checkUsername() {
            var username = $('#username').val().trim();
            if (!username) {
                showAlert('warning', 'Please enter your username first.', 'ri-information-line');
                return $('#username').focus();
            }

            clearAlert();
            abortXhr();

            var $btn = $('#btn-check-user')
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm text-white"></span>');
            $('#username').prop('disabled', true);

            xhr = $.ajax({
                url: '<?= site_url('auth/check_username') ?>',
                method: 'POST',
                data: { username: username },
                dataType: 'json',
                success: function (res) {
                    if (!res.success) {
                        showAlert('danger', res.message);
                        $('#username').addClass('is-invalid');
                        focusUsername = true;
                        return;
                    }

                    serverCount = Object.keys(res.result).length;

                    var opts = '';
                    $.each(res.result, function (k, v) {
                        var selected = (serverCount === 1 || v.default) ? ' selected' : '';
                        opts += '<option value="' + k + '"' + selected + '>' + v.name + '</option>';
                    });
                    $('#server').html(opts).trigger('change');

                    $('#username').removeClass('is-invalid').addClass('is-valid');
                    $('#badge-username').text(username);
                    $('#badge-username-3').text('Login as: ' + username);

                    if (serverCount === 1) {
                        $('#badge-server').text($('#server option:selected').text());
                        goToStep(1, 3, 'forward');
                    } else {
                        showAlert('success', '<strong>' + serverCount + ' server</strong> available for <strong>' + username + '</strong>.');
                        goToStep(1, 2, 'forward');
                    }
                },
                error: function (_, status) {
                    if (status !== 'abort') {
                        showAlert('danger', 'An error occurred while checking the username. Please try again.');
                    }
                },
                complete: function () {
                    $('#username').prop('disabled', false);
                    $btn.prop('disabled', false).html('<i class="ri ri-arrow-right-fill"></i>');
                    if (focusUsername) {
                        $('#username').focus();
                        focusUsername = false;
                    }
                    xhr = null;
                }
            });
        }

        $('#btn-check-user').on('click', checkUsername);
        $('#username').on('keypress', function (e) {
            if (e.which === 13) { e.preventDefault(); checkUsername(); }
        });

        $('#server').on('change', function () {
            var hasVal = !!$(this).val();
            $('#btn-next-server').prop('disabled', !hasVal);
            $(this).toggleClass('is-valid', hasVal).toggleClass('is-invalid', false);
        });

        $('#btn-next-server').on('click', function () {
            if (!$('#server').val()) {
                return showAlert('warning', 'Please select a server to continue.'), $('#server').focus();
            }
            $('#badge-server').text($('#server option:selected').text());
            $('#password').val('');
            clearAlert();
            goToStep(2, 3, 'forward');
            setTimeout(function () { $('#password').focus(); }, 350);
        });

        $('#btn-back-1').on('click', function () {
            clearAlert();
            resetStep1();
            goToStep(2, 1, 'back');
            setTimeout(function () { $('#username').focus(); }, 320);
        });

        $('#btn-back-2').on('click', function () {
            clearAlert();
            $('#password').val('').attr('type', 'password');
            $('#icon-password').removeClass('ri-eye-fill').addClass('ri-eye-close-fill');

            if (serverCount === 1) {
                resetStep1();
                goToStep(3, 1, 'back');
                setTimeout(function () { $('#username').focus(); }, 320);
            } else {
                goToStep(3, 2, 'back');
            }
        });

        $('#icon-password').on('click', function () {
            var $pw = $('#password');
            var isHidden = $pw.attr('type') === 'password';
            $pw.attr('type', isHidden ? 'text' : 'password');
            $(this).toggleClass('ri-eye-close-fill ri-eye-fill');
        });

        $('#login-form').on('submit', function (e) {
            e.preventDefault();
            if (!$('#password').val()) {
                return showAlert('warning', 'Please enter your password.');
            }

            abortXhr();

            $('#btn-submit').prop('disabled', true)
                        .html('<span class="spinner-border spinner-border-sm text-white"></span>');
            xhr = $.ajax({
                url: '<?= site_url('auth/submit_signin') ?>',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    xhr = null;
                    $('#btn-submit').prop('disabled', false)
                        .html('<i class="ri-login-box-line me-2"></i>Sign In');
                    if (res.success) {
                        showAlert('success', res.message);
                        $('#btn-submit').prop('disabled', true)
                            .html('<span class="spinner-border spinner-border-sm text-white me-1"></span> Please wait...');
                        setTimeout(function () { window.location.href = res.result.redirect; }, 1500);
                    } else {
                        showAlert('danger', res.message);
                        $('#password').focus();
                    }
                },
                error: function (_, status) {
                    xhr = null;
                    $('#btn-submit').prop('disabled', false)
                        .html('<i class="ri-login-box-line me-2"></i>Sign In');
                    if (status !== 'abort') {
                        showAlert('danger', 'An error occurred during login. Please try again.');
                        $('#password').focus();
                    }
                }
            });
        });

    });
    </script>
</body>

</html>