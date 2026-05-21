<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="" class="text-decoration-underline">
                                    <?= $breadcrumb ?>
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">

            <div class="col-12 col-lg-7">
                <div class="card rounded-3 p-4" id="card-sync-main">

                    <div id="state-idle">
                        <div class="text-center py-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:64px;height:64px">
                                <i class="ri-database-2-line text-white fs-3"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Sinkronisasi HPP</h5>
                            <p class="text-muted small mb-4">
                                Klik tombol di bawah untuk memulai proses sinkronisasi.<br>
                                Proses berjalan di background, kamu bisa tetap menggunakan aplikasi.
                            </p>
                            <button id="btn-start" class="btn btn-primary px-4" onclick="startSync()">
                                <i class="ri-play-fill me-2"></i>Mulai Sinkronisasi
                            </button>
                        </div>
                    </div>

                    <div id="state-queued" class="d-none">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width:44px;height:44px">
                                <i class="ri-history-fill text-white fs-5"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-semibold mb-0">Dalam Antrian</h6>
                                    <span class="badge badge-status-queued">Queued</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1" id="queue-message">
                                    Menunggu slot tersedia...
                                </p>
                            </div>
                        </div>
                        <div class="progress" style="height:6px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning w-100"></div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-outline-danger btn-sm" onclick="cancelSync()">
                                <i class="ri-close-circle-line me-1"></i>Batalkan
                            </button>
                        </div>
                    </div>

                    <div id="state-running" class="d-none">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                style="width:44px;height:44px">
                                <span class="spinner-border spinner-border-sm text-white" role="status"></span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="fw-semibold mb-0">Sedang Berjalan</h6>
                                    <span class="badge badge-status-running">Running</span>
                                </div>
                                <p class="text-muted small mb-0 mt-1" id="running-message">
                                    Memproses...
                                </p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted" id="eta-text"></small>
                            <small class="fw-semibold" id="progress-pct">0%</small>
                        </div>
                        <div class="progress rounded-pill" style="height:10px">
                            <div id="progress-bar"
                                class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar"
                                style="width:0%;transition:width .6s ease"
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted" id="running-job-id"></small>
                            <button id="btn-cancel" class="btn btn-outline-danger btn-sm" onclick="cancelSync()">
                                <i class="ri-close-circle-line me-1"></i>Batalkan
                            </button>
                        </div>
                    </div>

                    <div id="state-done" class="d-none">
                        <div class="text-center py-2">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:64px;height:64px">
                                <i class="ri-checkbox-circle-fill text-white fs-3"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Sinkronisasi Berhasil!</h5>
                            <p class="text-muted small mb-1" id="done-message"></p>
                            <p class="text-muted small mb-4" id="done-duration"></p>
                            <button class="btn btn-primary px-4" onclick="resetToIdle()">
                                <i class="ri-refresh-line me-2"></i>Sinkronisasi Lagi
                            </button>
                        </div>
                    </div>

                    <div id="state-failed" class="d-none">
                        <div class="text-center py-2">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:64px;height:64px">
                                <i class="ri-error-warning-fill text-white fs-3"></i>
                            </div>
                            <h5 class="fw-semibold mb-1">Sync Gagal</h5>
                            <p class="text-muted small mb-4" id="failed-message"></p>
                            <button class="btn btn-danger px-4" onclick="resetToIdle()">
                                <i class="ri-refresh-line me-2"></i>Coba Lagi
                            </button>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-12 col-lg-5 d-flex flex-column gap-3">
                <div class="card border-0 bg-primary bg-opacity-10 rounded-3 p-3">
                    <h6 class="fw-semibold text-white mb-2">
                        <i class="ri-information-line me-1"></i>Informasi Proses
                    </h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="d-flex gap-2 mb-1 text-white">
                            <i class="ri-check-line text-success mt-1 flex-shrink-0"></i>
                            Berjalan di background, halaman bisa tetap digunakan
                        </li>
                        <li class="d-flex gap-2 mb-1 text-white">
                            <i class="ri-check-line text-success mt-1 flex-shrink-0"></i>
                            Progress diperbarui setiap 3 detik
                        </li>
                        <li class="d-flex gap-2 mb-1 text-white">
                            <i class="ri-check-line text-success mt-1 flex-shrink-0"></i>
                            Estimasi waktu berdasarkan riwayat sebelumnya
                        </li>
                    </ul>
                </div>

                <div class="card border-0 rounded-3 p-3" style="box-shadow:0 .125rem .5rem rgba(0,0,0,.08)">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold mb-0">
                            <i class="ri-history-line me-1 text-muted"></i>Riwayat Sinkronisasi
                        </h6>
                        <button class="btn btn-link btn-sm p-0 text-decoration-none" onclick="loadHistory()">
                            <i class="ri-refresh-line"></i>
                        </button>
                    </div>

                    <div id="history-list">

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    let currentJobId = '<?= isset($active_job) && $active_job ? base64url_encode($this->encrypt->encode($active_job['JOB_ID'])) : '' ?>';
    let pollInterval = null;
    let xhr = null,
        xhr_history = null,
        xhr_delete = null;
    $(document).ready(function() {
        if (currentJobId) {
            startPolling(100);
        }
        history();
    });

    function startSync() {
        Swal.fire({
            title: 'Yakin ingin proses sinkronisasi hpp?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#70bcff',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.close();
                $('#btn-start').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...');

                if (xhr) {
                    xhr.abort();
                }
                xhr = $.ajax({
                    url: '<?= base_url("recalculate_hpp/start_sync") ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(res) {
                        if (res.status) {
                            currentJobId = res.job_id;
                            showState('queued');
                            startPolling();
                        } else {
                            alert(res.message);
                            $('#btn-start').prop('disabled', false).html('<i class="ri-play-fill me-2"></i>Mulai Sync');
                        }
                    },
                    error: function() {
                        alert('Gagal menghubungi server.');
                        $('#btn-start').prop('disabled', false).html('<i class="ri-play-fill me-2"></i>Mulai Sync');
                    }
                });
            }
        });
    }

    function startPolling(timer) {
        if (pollInterval) clearInterval(pollInterval);

        pollProgress(); // Panggil langsung pertama kali

        let currentTimer = timer ?? 3000;

        pollInterval = setInterval(function() {
            pollProgress();

            if (currentTimer !== 3000) {
                clearInterval(pollInterval);
                currentTimer = 3000;
                pollInterval = setInterval(pollProgress, currentTimer);
            }
        }, currentTimer);
    }

    function pollProgress() {
        if (!currentJobId) return;

        if (xhr) {
            xhr.abort();
        }
        xhr = $.ajax({
            url: '<?= base_url("recalculate_hpp/check_progress/") ?>' + currentJobId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status && res.data) {
                    updateUI(res.data);
                } else {
                    clearInterval(pollInterval);
                }
            },
            error: function() {
                // Silently fail and retry next tick
            }
        });
    }

    function updateUI(job) {
        if (job.STATUS === 'queued') {
            showState('queued');
            $('#queue-message').text(job.MESSAGE || 'Menunggu antrian...');
        } else if (job.STATUS === 'running') {
            showState('running');
            $('#running-message').text(job.MESSAGE || 'Memproses...');
            $('#progress-pct').text(job.PROGRESS + '%');
            $('#progress-bar').css('width', job.PROGRESS + '%').attr('aria-valuenow', job.PROGRESS);
        } else if (job.STATUS === 'done') {
            clearInterval(pollInterval);
            showState('done');
            $('#done-message').text(job.MESSAGE || 'Proses selesai.');
            // $('#done-duration').text('Waktu proses: ' + (job.DURATION_SEC || 0) + ' detik');
            $('#done-duration').text('');
            currentJobId = null;
            history();
        } else if (job.STATUS === 'failed') {
            clearInterval(pollInterval);
            showState('failed');
            $('#failed-message').text(job.MESSAGE || 'Proses gagal.');
            currentJobId = null;
            history();
        }
    }

    function showState(state) {
        $('#state-idle, #state-queued, #state-running, #state-done, #state-failed').addClass('d-none');
        $('#state-' + state).removeClass('d-none');
    }

    function cancelSync() {
        if (!currentJobId) return;

        Swal.fire({
            title: 'Yakin ingin membatalkan proses ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#70bcff',
            confirmButtonText: 'Ya, batalkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                if (xhr_delete) {
                    xhr_delete.abort();
                }
                xhr_delete = $.ajax({
                    url: '<?= base_url("recalculate_hpp/cancel_sync") ?>',
                    type: 'POST',
                    data: {
                        job_id: currentJobId
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Membatalkan...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(res) {
                        Swal.close();
                        if (res.status) {
                            clearInterval(pollInterval);
                            showState('failed');
                            $('#failed-message').text('Dibatalkan oleh user.');
                            currentJobId = null;
                        }
                        history();
                    }
                });
            }
        });
    }

    function resetToIdle() {
        showState('idle');
        $('#btn-start').prop('disabled', false).html('<i class="ri-play-fill me-2"></i>Mulai Sinkronisasi');
        $('#progress-bar').css('width', '0%').attr('aria-valuenow', 0);
        $('#progress-pct').text('0%');
    }

    function loadHistory() {
        location.reload();
    }

    function history() {
        if (xhr_history) {
            xhr_history.abort();
        }
        xhr_history = $.ajax({
            url: '<?= base_url("recalculate_hpp/history") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    $('#history-list').html(res.result);
                } else {
                    $('#history-list').html('<p class="text-muted small text-center py-2 mb-0">Belum ada riwayat sinkronisasi.</p>');
                }
            },
            error: function() {
                $('#history-list').html('<p class="text-muted small text-center py-2 mb-0">Gagal mengambil data.</p>');
            }
        });
    }
</script>