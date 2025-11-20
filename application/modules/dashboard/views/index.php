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

<!-- modal -->
<div id="modalForm" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"></span>
                <form action="" method="post" id="formInspection">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" id="url">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="waktu_keberangkatan"><?= $this->lang->line('inspectionDate'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-time-fill"></i>
                                    </span>
                                    <input type="datetime-local" name="waktu_keberangkatan" id="waktu_keberangkatan" class="form-control" placeholder="<?= $this->lang->line('inspectionDate'); ?>" autocomplete="off" disabled>
                                </div>
                                <div id="error-waktu_keberangkatan" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="users"><?= $this->lang->line('users'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-user-2-fill"></i>
                                    </span>
                                    <input type="text" name="users" id="users" class="form-control" placeholder="<?= $this->lang->line('users'); ?>" autocomplete="off" disabled>
                                </div>
                                <div id="error-user" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="group_name"><?= $this->lang->line('workServiceGroupName'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-building-2-fill"></i>
                                    </span>
                                    <input type="text" name="group_name" id="group_name" class="form-control" placeholder="<?= $this->lang->line('workServiceGroupName'); ?>" autocomplete="off" disabled>
                                </div>
                                <div id="error-group_name" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="end_user"><?= $this->lang->line('endUser'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-building-3-fill"></i>
                                    </span>
                                    <input type="text" name="end_user" id="end_user" class="form-control" placeholder="<?= $this->lang->line('endUser'); ?>" autocomplete="off" disabled>
                                </div>
                                <div id="error-end_user" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="days"><?= $this->lang->line('daysQty'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-arrow-up-down-fill"></i>
                                    </span>
                                    <input type="number" name="days" id="days" class="form-control" placeholder="<?= $this->lang->line('daysQty'); ?>" autocomplete="off" disabled>
                                </div>
                                <div id="error-days" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="engineer"><?= $this->lang->line('engineer'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-user-star-fill"></i>
                                    </span>
                                    <select name="engineer[]" id="engineer" class="form-control select2" autocomplete="off" multiple="multiple" disabled data-placeholder="-- Selected <?= $this->lang->line('engineer'); ?> --">
                                        <option></option>

                                    </select>
                                </div>
                                <div id="error-engineer" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="description"><?= $this->lang->line('inspectionDescription'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-sticky-note-fill"></i>
                                    </span>
                                    <textarea name="description" id="description" class="form-control" placeholder="<?= $this->lang->line('inspectionDescription'); ?>" autocomplete="off" disabled></textarea>
                                </div>
                                <div id="error-description" class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-sm-12">
                            <div class="mb-3">
                                <label for="other_detail"><?= $this->lang->line('otherDetail'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ri ri-sticky-note-fill"></i>
                                    </span>
                                    <textarea name="other_detail" id="other_detail" class="form-control" placeholder="<?= $this->lang->line('otherDetail'); ?>" disabled autocomplete="off"></textarea>
                                </div>
                                <div id="error-other_detail" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-bs-dismiss="modal"><?= $this->lang->line('cancel'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- / modal -->

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


        var calendarEl = document.getElementById('calendar');
        var calendar;
        var events = [];
        $.ajax({
            type: "POST",
            url: "<?= base_url('dashboard/dataInspection') ?>",
            dataType: "json",
            success: function(response) {
                if (!!response) {
                    Object.keys(response).map(k => {
                        var row = response[k];
                        events.push({
                            id: row.id,
                            title: row.kodeInspection,
                            start: row.waktu_keberangkatan,
                            end: row.waktu_selesai,
                            backgroundColor: row.color,
                            textColor: row.textColor,
                        });
                    })
                }
                calendar = new FullCalendar.Calendar(calendarEl, {
                    plugins: ['dayGrid', 'timeGrid', 'list', 'interaction'],
                    displayEventTime: false,
                    header: {
                        left: 'prevYear,prev,next,nextYear today',
                        center: 'title',
                        right: 'listMonth'
                    },
                    defaultDate: new Date(),
                    editable: true,
                    events: events,
                    eventTimeFormat: false,
                    displayEventEnd: true,
                    eventOrder: 'title',
                    eventClick: function(info) {
                        var id = info.event.id
                        showInspection(id);
                    },
                    eventRender: function(info) {
                        if (info.el) {
                            var textColor = info.event.textColor;
                            if (textColor) {
                                info.el.style.color = textColor;
                            }

                            // Jika ada elemen judul
                            var titleEl = info.el.querySelector('.fc-title');
                            if (titleEl) {
                                titleEl.style.color = textColor;
                            }
                        }
                    }
                });
                calendar.render();
            }
        });
    });

    function showInspection(id) {
        $.ajax({
            type: "POST",
            url: "<?= base_url('dashboard/getDetailInspection') ?>",
            data: {
                id: id,
            },
            dataType: "json",
            success: function(response) {
                $('#formInspection')[0].reset();
                $('#engineer').empty();

                $("#modalTitle").text('<?= $this->lang->line('inspectionDetail'); ?>');
                $("#waktu_keberangkatan").val(response.data.waktu_keberangkatan);
                $("#users").val(response.data.nama_lengkap);
                $("#group_name").val(response.data.group_company);
                $("#end_user").val(response.data.end_users);
                $("#days").val(response.data.day_qty);
                $("#description").val(response.data.description);
                $("#other_detail").val(response.data.note);

                $.each(response.engineer, function(index, value) {
                    $('#engineer').append($('<option>', {
                        text: value.nama
                    }));
                });
                $("#modalForm").modal('show');
            }
        });
    }
</script>