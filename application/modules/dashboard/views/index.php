<!-- Plugin css -->
<link rel="stylesheet" href="<?= base_url() ?>assets/admin/libs/@fullcalendar/core/main.min.css" type="text/css">

<style>
    .fc-unthemed td.fc-today {
        background-color: #B6F500 !important;
    }

    .fc-event {
        border: none !important;
    }
</style>


<!-- plugin js -->
<script src="<?= base_url() ?>assets/admin/libs/moment/min/moment.min.js"></script>
<script src="<?= base_url() ?>assets/admin/libs/jquery-ui-dist/jquery-ui.min.js"></script>
<script src="<?= base_url() ?>assets/admin/libs/@fullcalendar/core/main.min.js"></script>
<script src="<?= base_url() ?>assets/admin/libs/@fullcalendar/bootstrap/main.min.js"></script>
<script src="<?= base_url() ?>assets/admin/libs/@fullcalendar/daygrid/main.min.js"></script>
<script src="<?= base_url() ?>assets/admin/libs/@fullcalendar/timegrid/main.min.js"></script>
<script src="<?= base_url() ?>assets/admin/libs/@fullcalendar/interaction/main.min.js"></script>

<!-- Calendar init -->
<!-- <script src="<?= base_url() ?>assets/admin/js/pages/calendar.init.js"></script> -->

<div id="flashSuccess" data-success="<?= $this->session->flashdata('success'); ?>"></div>
<div id="flashWarning" data-warning="<?= $this->session->flashdata('warning'); ?>"></div>
<div id="flashError" data-error="<?= $this->session->flashdata('error'); ?>"></div>

<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="">
                                    <?= $breadcrumb ?>
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
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-center">
                                    <span>
                                        <h3 class="text-center fw-bold fst-italic">Data hebat dimulai dari nol. Saatnya buat karya yang bermanfaat.</h4>
                                            <div id="calendar"></div>
                                    </span>
                                </div>
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
    });
</script>