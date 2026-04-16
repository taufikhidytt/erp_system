<!-- Plugin css -->
<link rel="stylesheet" href="<?= base_url() ?>assets/admin/libs/@fullcalendar/core/main.min.css" type="text/css">

<style>
    #table_log_sign_in td {
        white-space: nowrap;
        padding-right: 6px !important;
        padding-left: 6px !important;
        font-size: 0.75rem !important;
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

<div class="page-content" data-aos="zoom-in">
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
                                        <h3 class="text-center fw-bold fst-italic">Data hebat dimulai dari nol. <br><br> Konsisten lebih penting daripada sempurna.</h4>
                                            <div id="calendar"></div>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12"></div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="card border-2" id="cardHistory">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="card-title mb-0"><b>History Sign In</b></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-xs btn-icon" onclick="collapseCard(this)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-icon" onclick="maximizeCard(this)">
                                    <i class="fas fa-expand"></i>
                                </button>
                                <button type="button" class="btn btn-xs btn-icon" onclick="removeCard(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm text-center table-striped" id="table_log_sign_in">
                                <thead>
                                    <tr>
                                        <th>
                                        </th>
                                        <th>
                                            <input type="text" placeholder="Cari.." class="column_search" data-column="1" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                        </th>
                                        <th>
                                            <input type="text" placeholder="Cari.." class="column_search" data-column="2" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                        </th>
                                        <th>
                                            <input type="text" placeholder="Cari.." class="column_search" data-column="3" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                        </th>
                                        <th>
                                            <input type="text" placeholder="Cari.." class="column_search" data-column="4" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                        </th>
                                        <th>
                                            <input type="text" placeholder="Cari.." class="column_search" data-column="5" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>IP</th>
                                        <th>OS</th>
                                        <th>Browser</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                            </table>
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
        var table_log_sign_in = $('#table_log_sign_in').DataTable({
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('dashboard/get_log_sign_in'); ?>",
                "type": "POST"
            },
            "columns": [{
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                },
                {
                    "data": "nama",
                    "width": "5%",
                },
                {
                    "data": "ip",
                    "width": "10%",
                },
                {
                    "data": "os",
                    "width": "10%",
                },
                {
                    "data": "browser",
                    "width": "10%",
                },
                {
                    "data": "date",
                    "width": "10%",
                },
            ]
        });

        $('.column_search').on('keyup change', function() {
            table_log_sign_in
                .column($(this).data('column'))
                .search(this.value)
                .draw();
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

    function collapseCard(btn) {
        const card = btn.closest('.card');
        const body = card.querySelector('.table-responsive');
        const icon = btn.querySelector('i');

        if (body.style.display === 'none') {
            body.style.display = 'block';
            icon.classList.remove('fa-plus');
            icon.classList.add('fa-minus');
        } else {
            body.style.display = 'none';
            icon.classList.remove('fa-minus');
            icon.classList.add('fa-plus');
        }
    }

    let originalParent = null;
    let nextSibling = null;

    function maximizeCard(btn) {
        const card = btn.closest('.card');
        const icon = btn.querySelector('i');

        const isMax = card.classList.contains('maximized');

        if (!isMax) {
            // SIMPAN posisi awal
            originalParent = card.parentElement;
            nextSibling = card.nextElementSibling;

            document.body.appendChild(card);

            card.classList.add('maximized');
            card.style.position = 'fixed';
            card.style.top = '0';
            card.style.left = '0';
            card.style.width = '100vw';
            card.style.height = '100vh';
            card.style.zIndex = '9999';
            card.style.background = '#fff';
            card.style.margin = '0';
            document.body.style.overflow = 'hidden';
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        } else {
            if (nextSibling) {
                originalParent.insertBefore(card, nextSibling);
            } else {
                originalParent.appendChild(card);
            }

            card.classList.remove('maximized');

            card.removeAttribute('style');
            document.body.style.overflow = '';

            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }

    function removeCard(btn) {
        const card = btn.closest('.card');
        card.remove();
    }
</script>