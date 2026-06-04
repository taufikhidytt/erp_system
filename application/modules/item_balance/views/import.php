<style>
h5, button{
font-family: Tahoma !important;
}
.rv-wrap { padding: 1.5rem 0; }
.rv-header { text-align: center; margin-bottom: 1.5rem; }
.rv-header h5 { font-weight: 600; margin: 0 0 4px; color: #212529; }
.rv-header p  { font-size: 13px; color: #6c757d; margin: 0; }
 
.rv-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 1.25rem; }
.rv-stat  { border-radius: 10px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 14px; }
.rv-stat.ok  { background: #EAF3DE; border: 1px solid #97C459; }
.rv-stat.err { background: #FCEBEB; border: 1px solid #F09595; }
 
.rv-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 20px; }
.rv-stat.ok  .rv-icon { background: #C0DD97; color: #27500A; }
.rv-stat.err .rv-icon { background: #F7C1C1; color: #791F1F; }
 
.rv-label { font-size: 12px; margin: 0 0 2px; font-weight: 500; color: #3B6D11; }
.rv-stat.err .rv-label { color: #A32D2D; }
.rv-num { font-size: 28px; font-weight: 700; line-height: 1; margin: 0 0 6px; color: #27500A; }
.rv-stat.err .rv-num { color: #791F1F; }
.rv-pill { display: inline-block; font-size: 11px; font-weight: 600; padding: 2px 9px; border-radius: 999px; letter-spacing: 0.02em; background: #3B6D11; color: #EAF3DE; }
.rv-stat.err .rv-pill { background: #A32D2D; color: #FCEBEB; }
 
.rv-bar-wrap { margin-bottom: 1.25rem; }
.rv-bar-meta { display: flex; justify-content: space-between; font-size: 12px; color: #6c757d; margin-bottom: 5px; }
.rv-bar-meta strong { color: #212529; }
.rv-bar { height: 6px; border-radius: 999px; background: #e9ecef; overflow: hidden; display: flex; }
.rv-bar-ok  { background: #639922; }
.rv-bar-err { background: #E24B4A; }
 
.rv-alert { display: flex; align-items: flex-start; gap: 10px; background: #FAEEDA; border: 1px solid #EF9F27; border-radius: 8px; padding: 10px 14px; margin-bottom: 1.25rem; font-size: 13px; color: #633806; }
.rv-alert i { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
 
.rv-actions { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; }
.rv-btn { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; padding: 7px 18px; border-radius: 8px; border: 1px solid transparent; cursor: pointer; text-decoration: none; line-height: 1.4; }
.rv-btn-primary { background: #3B6D11; color: #EAF3DE; border-color: #3B6D11; }
.rv-btn-primary:hover { background: #27500A; border-color: #27500A; color: #EAF3DE; }
.rv-btn-danger  { background: transparent; color: #A32D2D; border-color: #F09595; }
.rv-btn-danger:hover { background: #FCEBEB; }
.rv-btn-neutral { background: transparent; color: #6c757d; border-color: #ced4da; }
.rv-btn-neutral:hover { background: #f8f9fa; }

#result-list table th{
    white-space: nowrap;
}

/* Tampilan Tabel History */
.history-card { border: none; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); border-radius: 12px; }
.table-history thead { background-color: #f8f9fa; }
.table-history th { font-size: 13px; font-weight: 600; text-transform: uppercase; color: #495057; padding: 12px; }
.table-history td { font-size: 14px; vertical-align: middle; padding: 12px; }

/* Status Badges Custom */
.badge-import { padding: 5px 10px; border-radius: 6px; font-weight: 500; font-size: 12px; }
.badge-completed { background: #EAF3DE; color: #3B6D11; border: 1px solid #97C459; }
.badge-failed { background: #FCEBEB; color: #A32D2D; border: 1px solid #F09595; }
.badge-running { background: #E7F1FF; color: #004085; border: 1px solid #B8DAFF; }

.text-num-history { font-family: 'Courier New', Courier, monospace; font-weight: bold; }
</style>

<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">

        <!-- Breadcrumb -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url($access['url']) ?>" class="text-decoration-underline">
                                    <?= $access['PROMPT'] ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item active"><?= $breadcrumb ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12">
                <div class="card rounded-3 p-4" id="card-import-main">

                    <div id="state-idle">
                        <!-- Filter: Gudang & Periode -->
                        <div class="row form-xs mb-3">
                            <div class="col-xxl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="warehouse" class="form-label">Gudang</label>
                                    <span class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-building-fill"></i>
                                        </span>
                                        <input type="hidden" id="period" value="<?= base64url_encode($this->encrypt->encode($period->PERIOD_NAME)) ?>">
                                        <select name="warehouse" id="warehouse" class="form-control select2"
                                            data-url="item_balance/get_warehouse"
                                            data-default="Y"
                                            data-dropdown-parent="body"
                                            placeholder="Pilih Gudang">
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="periode_awal" class="form-label">Periode Awal</label>
                                    <span class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-calendar-fill"></i>
                                        </span>
                                        <input type="text" class="form-control" id="periode_awal" value="<?= $period->PERIOD_NAME ?>" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Upload area — tampil setelah gudang dipilih -->
                        <div id="upload-area" style="display:none;">
                            <?php if ($period->OPEN_FLAG === 'Y'): ?>
                                <div class="text-center py-4">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width:72px;height:72px;">
                                        <i class="ri-file-upload-line text-white fs-2"></i>
                                    </div>

                                    <h5 class="fw-semibold mb-1">Import <?= $access['PROMPT'] ?></h5>
                                    <p class="text-muted small mb-4">
                                        Download template terlebih dahulu, isi data <?= strtolower($access['PROMPT']) ?> mulai dari <strong>baris ke-8</strong>,
                                        kemudian upload file untuk memulai proses validasi.<br>
                                        Proses impor data akan lebih cepat ketika hanya memasukkan data yang berubah saja. Hapus data lama yang tidak perlu diubah dari template.<br>
                                        Proses berjalan di <em>background</em> — kamu tetap bisa menggunakan aplikasi.
                                    </p>
                                    <form id="form-upload" enctype="multipart/form-data" class="d-none">
                                        <input type="file" id="import_file" name="import_file"
                                            accept=".csv,.xlsx,.xls">
                                    </form>

                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <button id="btn-trigger-upload" class="btn btn-success btn-sm px-4">
                                            <i class="ri-file-upload-fill me-2"></i>Upload File
                                        </button>
                                        <a id="btn-download-template" href="<?= base_url($access['url'] . '/template_import') ?>"
                                            target="_blank" class="btn btn-primary btn-sm px-4">
                                            <i class="ri-file-download-fill me-2"></i>Download Template
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4" data-aos="fade-up" data-aos-delay="100">
                                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                        style="width:72px;height:72px;animation: pulse 2s ease-in-out infinite;">
                                        <i class="ri-lock-line fs-2 text-white"></i>
                                    </div>
                                    <h5 class="fw-semibold mb-1 text-danger">Periode Sudah Ditutup</h5>
                                    <p class="text-muted small mb-0">
                                        <?= ucfirst($access['PROMPT']) ?> tidak dapat diubah karena periode <strong><?= $period->PERIOD_NAME ?></strong> sudah ditutup.<br>
                                        Hubungi administrator jika perlu membuka kembali periode ini.
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Placeholder sebelum gudang dipilih -->
                        <div id="upload-placeholder" class="text-center py-4 text-muted">
                            <i class="ri-building-line fs-1 mb-2 d-block"></i>
                            <p class="small mb-0">Pilih gudang terlebih dahulu untuk melanjutkan import <?= strtolower($access['PROMPT']) ?>.</p>
                        </div>
                    </div>

                    <div id="state-running" class="d-none">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary mb-3" role="status" style="width:3rem;height:3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5 class="fw-semibold mb-1" id="progress-title">Mengunggah File...</h5>
                            <p class="text-muted small mb-3" id="progress-message">
                                Mohon tunggu, jangan tutup halaman ini jika ingin melihat progres.
                            </p>

                            <!-- Progress Bar -->
                            <div class="progress mb-2" style="height:22px; border-radius:8px;">
                                <div id="progress-bar"
                                    class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                    role="progressbar"
                                    style="width:0%; transition: width 0.5s ease;"
                                    aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    0%
                                </div>
                            </div>
                            <div class="text-muted small text-end" id="progress-text">0 / 0 Baris</div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2 btn-cancel">
                                    <i class="ri ri-close-line me-1"></i>Batalkan
                                </button>
                            </div>

                            <!-- Info lanjutan -->
                            <p class="text-muted small mt-3 mb-0">
                                <i class="ri-information-line me-1"></i>
                                Kamu boleh beralih ke halaman lain. Status akan tersimpan.
                            </p>
                        </div>
                    </div>

                    <div id="state-review" class="d-none">
                        <div class="rv-wrap container">
                            <div class="rv-header">
                                <h5>
                                    <i class="ri-checkbox-circle-line text-success me-1"></i>
                                    Validasi selesai — review hasil
                                </h5>
                                <p>Periksa ringkasan berikut sebelum melanjutkan simpan data.</p>
                            </div>
                    
                            <div class="rv-stats">
                                <div class="rv-stat ok">
                                    <div class="rv-icon"><i class="ri-checkbox-circle-line"></i></div>
                                    <div>
                                        <p class="rv-label">Baris valid</p>
                                        <p class="rv-num" id="review-success-count"></p>
                                        <span class="rv-pill">Siap diimport</span>
                                    </div>
                                </div>
                                <div class="rv-stat err">
                                    <div class="rv-icon"><i class="ri-close-circle-line"></i></div>
                                    <div>
                                        <p class="rv-label">Baris gagal</p>
                                        <p class="rv-num" id="review-failed-count"></p>
                                        <span class="rv-pill">Perlu diperbaiki</span>
                                    </div>
                                </div>
                            </div>
                    
                            <div class="rv-bar-wrap">
                                <div class="rv-bar-meta">
                                    <span>Total <strong id="total-count"></strong> baris diproses</span>
                                    <span><strong id="pct"></strong> siap diimport</span>
                                </div>
                                <div class="rv-bar">
                                    <div class="rv-bar-ok"  id="bar-ok"></div>
                                    <div class="rv-bar-err" id="bar-err"></div>
                                </div>
                            </div>
                    
                            <div class="rv-alert" id="alert-failed-info" role="alert">
                                <i class="ri-alert-line"></i>
                                <span>Terdapat baris yang gagal validasi. Download file gagal, perbaiki data, lalu upload ulang file tersebut.</span>
                            </div>
                    
                            <div class="rv-actions">
                                <button type="button" id="btn-finalize" class="rv-btn rv-btn-primary">
                                    <i class="ri-check-line"></i> Lanjutkan import (data valid saja)
                                </button>
                                <a id="btn-download-failed" href="#" class="rv-btn rv-btn-danger" target="_blank">
                                    <i class="ri-download-2-line"></i> Download data gagal
                                </a>
                                <button type="button" class="rv-btn rv-btn-danger btn-cancel">
                                    <i class="ri-close-line"></i> Batalkan
                                </button>
                            </div>
                    
                        </div>
                        <div id="result-list"></div>
                    </div>

                    <div id="state-finalizing" class="d-none">
                        <div class="text-center py-5">
                            <div class="spinner-border text-success mb-3" role="status" style="width:3rem;height:3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5 class="fw-semibold mb-1">Menyimpan Data ke Database...</h5>
                            <p class="text-muted small mb-0">Mohon jangan menutup atau merefresh halaman ini.</p>
                        </div>
                    </div>

                    <div id="state-cancelled" class="d-none">
                        <div class="text-center py-5">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width:72px;height:72px;">
                                <i class="ri-forbid-2-line text-white fs-2"></i>
                            </div>
                            <h5 class="fw-semibold mb-1 text-danger">Proses Dibatalkan</h5>
                            <p class="text-muted small mb-4">
                                Validasi import telah dibatalkan.<br>
                                Data tidak tersimpan ke database.
                            </p>
                            <button id="btn-cancelled-reset" class="btn btn-outline-secondary btn-sm px-4">
                                <i class="ri-refresh-line me-2"></i>Upload File Baru
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card border-2">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold"><i class="ri-history-line me-2"></i>Riwayat Import</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table text-center table-sm" id="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="1" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="2" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="date" placeholder="Cari.." class="column_search" data-column="3" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="4" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th></th>
                                        </tr>
                                        <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                            <th>No</th>
                                            <th>Pesan</th>
                                            <th>Diimport oleh</th>
                                            <th>Diimport pada</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function () {
        let currentJobId = '<?= isset($active_job) && $active_job ? base64url_encode($this->encrypt->encode($active_job['IMPORT_HISTORY_ID'])) : '' ?>';
        let pollInterval  = null;
        const POLL_DELAY  = 2000;
        let xhr = {'upload' : null, 'cancel' : null, 'save' : null};

        const URLS = {
            history       : '<?= base_url("item_balance/import_history") ?>',
            upload        : '<?= base_url("item_balance/upload") ?>',
            get_status    : '<?= base_url("item_balance/get_status") ?>',
            finalize      : '<?= base_url("item_balance/finalize_import") ?>',
            download_failed: '<?= base_url("item_balance/download_failed/") ?>',
            cancel        : '<?= base_url("item_balance/import_cancel/") ?>',
        };

        var table = $('#table').DataTable({
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": URLS.history,
                "type": "POST"
            },
            "columns": [
                {
                    "data": "no",
                    "width" : "50px",
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center"
                },
                {
                    "data": "message",
                },
                {
                    "data": "user",
                    "width" : "150px",
                },
                {
                    "data": "tanggal",
                    "width" : "120px",
                    "className": "text-center"
                },
                {
                    "data": "status",
                    "width" : "50px",
                    "className": "text-center"
                },
                {
                    "data": "action",
                    "width" : "50px",
                    "orderable": false,
                    "searchable": false,
                    "className": "text-center",
                }
            ]
        });
        $('.column_search').on('keyup change', function() {
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
        });

        // Tampilkan/sembunyikan area upload berdasarkan pilihan gudang
        $(document).on('change', '#warehouse', function () {
            const val = $(this).val();
            if (val && val !== '__empty__') {
                $('#upload-placeholder').hide();
                $('#upload-area').fadeIn(300);

                // Update URL template dengan warehouse & period
                const period  = $('#period').val();
                const baseUrl = '<?= base_url($access['url'] . '/template_import') ?>';
                $('#btn-download-template').attr('href', baseUrl + '?w=' + encodeURIComponent(val) + '&p=' + encodeURIComponent(period));
            } else {
                $('#upload-area').fadeOut(200, function () {
                    $('#upload-placeholder').show();
                });
            }
        });

        $('#btn-trigger-upload').on('click', function () {
            <?php if ($period->OPEN_FLAG !== 'Y'): ?>
            Swal.fire({ icon: 'error', title: 'Periode Ditutup', text: '<?= ucfirst($access['PROMPT']) ?> tidak dapat diubah karena periode sudah ditutup.' });
            return;
            <?php endif; ?>
            if (!$('#warehouse').val() || $('#warehouse').val() === '__empty__') {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih gudang terlebih dahulu.' });
                return;
            }
            $('#import_file').click();
        });

        $('#import_file').on('change', function () {
            if (!$(this).val()) return;
            <?php if ($period->OPEN_FLAG !== 'Y'): ?>
            Swal.fire({ icon: 'error', title: 'Periode Ditutup', text: '<?= ucfirst($access['PROMPT']) ?> tidak dapat diubah karena periode sudah ditutup.' });
            $(this).val('');
            return;
            <?php endif; ?>

            const formData = new FormData($('#form-upload')[0]);
            formData.append('warehouse_id', $('#warehouse').val());
            formData.append('period', $('#period').val());

            switchState('running');
            setProgress(0, 0, 'Mengunggah File...');

            if(xhr.upload){
                xhr.upload.abort();
            }

            xhr.upload = $.ajax({
                url        : URLS.upload,
                type       : 'POST',
                data       : formData,
                processData: false,
                contentType: false,
                dataType   : 'json',
                success    : function (resp) {
                    if (resp.status === 'success') {
                        currentJobId = resp.result;
                        startPolling();
                    } else {
                        showAlert('danger', resp.message);
                        switchState('idle');
                    }
                },
                error: function (xhr) {
                    showAlert('danger', 'Terjadi kesalahan pada server saat upload.');
                    switchState('idle');
                }
            });

            $(this).val('');
        });

        
        function startPolling() {
            clearInterval(pollInterval);
            pollInterval = setInterval(poll, POLL_DELAY);
        }

        function poll() {
            if (!currentJobId) {
                clearInterval(pollInterval);
                return;
            }

            $.ajax({
                url     : URLS.get_status,
                type    : 'GET',
                data    : { job_id: currentJobId },
                dataType: 'json',
                success : function (res) {
                    handlePollResponse(res.result);
                },
                error: function () {
                    console.warn('Polling error, akan coba lagi...');
                }
            });
        }

        function handlePollResponse(res) {
            const status = res.status;

            if (status === 'queued' || status === 'running') {
                const prog = parseInt(res.progress) || 0;
                const tot  = parseInt(res.total)    || 1;
                setProgress(prog, tot, res.message || 'Memvalidasi data...');
                $('#progress-title').text('Validasi Berjalan...');

            } else if (status === 'done' || status === 'completed') {
                clearInterval(pollInterval);
                showReview(res);

            } else if (status === 'failed') {
                clearInterval(pollInterval);
                showAlert('danger', 'Proses validasi gagal: ' + (res.message || 'Unknown error'));
                switchState('idle');

            } else if (status === 'error') {
                clearInterval(pollInterval);
                showAlert('danger', res.message || 'Terjadi kesalahan.');
                switchState('idle');
            }
        }

        function showReview(res) {
            switchState('review');

            const sCount = parseInt(res.success_count) || 0;
            const fCount = parseInt(res.failed_count)  || 0;

            $('#review-success-count').attr('data-val',sCount).text($.inputNumber.format(sCount,0));
            $('#review-failed-count').attr('data-val',fCount).text($.inputNumber.format(fCount,0));

            if (sCount > 0) {
                $('#btn-finalize').show();
            } else {
                $('#btn-finalize').hide();
            }

            if (fCount > 0) {
                $('#btn-download-failed')
                    .attr('href', URLS.download_failed + currentJobId)
                    .show();
                $('#alert-failed-info').removeClass('d-none');
            } else {
                $('#btn-download-failed').hide();
                $('#alert-failed-info').addClass('d-none');
            }

            if (sCount === 0 && fCount === 0) {
                showAlert('warning', 'Tidak ada data yang berhasil divalidasi. Periksa format file Anda.');
            }

            if(res.result_list){
                $('#result-list').html(res.result_list);
                setTimeout(() => {
                    const tbl_opt = {
                        destroy: true,
                        autoWidth: false,
                        paging: true,
                        searching: true,
                        ordering: false,
                        deferRender: true,
                        scrollY: "70dvh",
                        scrollX: true,
                        scrollCollapse: true,
                        scroller: true,
                        info: false,
                    };
                    if($('#result-list').find('#tbl-rv-success')){
                        $('#tbl-rv-success').DataTable(tbl_opt);
                    }
                    if($('#result-list').find('#tbl-rv-failed')){
                        $('#tbl-rv-failed').DataTable(tbl_opt);
                    }
                }, 100);
            }

            resultProgress();
            table.ajax.reload();
        }

        $('#btn-finalize').on('click', function () {
            if (!currentJobId) return;
    
            Swal.fire({
                title: 'Yakin ingin menyimpan '+$('#review-success-count').text()+' data?',
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#70bcff', cancelButtonColor: '#d33', confirmButtonText: 'Ya, simpan!', cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    switchState('finalizing');
                    if(xhr.save){
                        xhr.save.abort();
                    }
                    xhr.save = $.ajax({
                        url: URLS.finalize,
                        type: 'POST',
                        data: { job_id: currentJobId },
                        dataType: 'json',
                        beforeSend: function() { Swal.fire({ title: 'Menyimpan data...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } }); },
                        success: function(res) {
                            Swal.close();
                            if (res.success) {
                                showAlert('success', res.message);
                                resetView();
                                table.ajax.reload();
                            } else {
                                showAlert('danger', res.message);
                                switchState('review');
                            }
                        },
                        error: function () {
                            showAlert('danger', 'Terjadi kesalahan pada server saat menyimpan data.');
                            switchState('review');
                        }
                    });
                }
            });
        });

        $('#btn-reset').on('click', function () {
            if (currentJobId) {
                
            }
            resetView();
        });

        function switchState(state) {
            $('#state-idle, #state-running, #state-review, #state-finalizing, #state-cancelled').addClass('d-none');
            $('#state-' + state).removeClass('d-none');
        }

        /**
         * Update progress bar dan teks pendamping.
         */
        function setProgress(current, total, message) {
            const pct = total > 0 ? Math.min(100, Math.round((current / total) * 100)) : 0;
            $('#progress-bar').css('width', pct + '%').text(pct + '%').attr('aria-valuenow', pct);
            $('#progress-text').text(current.toLocaleString('id-ID') + ' / ' + total.toLocaleString('id-ID') + ' Baris');
            if (message) {
                $('#progress-message').text(message);
            }
        }

        function resetView() {
            currentJobId = null;
            clearInterval(pollInterval);

            switchState('idle');

            // Kembalikan tampilan upload sesuai kondisi gudang
            if ($('#warehouse').val() && $('#warehouse').val() !== '__empty__') {
                $('#upload-placeholder').hide();
                $('#upload-area').show();
            } else {
                $('#upload-area').hide();
                $('#upload-placeholder').show();
            }

            // Reset progress bar
            $('#progress-bar').css('width', '0%').text('0%').attr('aria-valuenow', 0);
            $('#progress-text').text('0 / 0 Baris');
            $('#progress-title').text('Memvalidasi Data...');
            $('#progress-message').text('Mohon tunggu, jangan tutup halaman ini.');

            // Reset tombol finalize
            $('#btn-finalize').prop('disabled', false)
                .html('<i class="ri-check-line me-2"></i>Lanjutkan Import (Data Valid Saja)');

            // Reset form
            $('#form-upload')[0].reset();

            // Hapus alert yang mungkin muncul
            $('.import-alert-dynamic').remove();
        }

        function showAlert(type, message) {
            const swalType = type === 'danger' ? 'error' : type;

            Swal.fire({
                icon: swalType,
                title: swalType.charAt(0).toUpperCase() + swalType.slice(1),
                text: message,
                timer: 5000, // Auto close dalam 5 detik
                timerProgressBar: true,
                showConfirmButton: true,
                confirmButtonColor: '#3085d6'
            });
        }

        if(currentJobId){
            switchState('running');
            setProgress(0, 0, 'Memeriksa Data...');
            startPolling();
        }

        $('.btn-cancel').on('click', function(){
            if (!currentJobId) return;
    
            Swal.fire({
                title: 'Yakin ingin membatalkan proses ini?',
                icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#70bcff', confirmButtonText: 'Ya, batalkan!', cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    clearInterval(pollInterval);
                    if(xhr.cancel){
                        xhr.cancel.abort();
                    }
                    xhr.cancel = $.ajax({
                        url: URLS.cancel,
                        type: 'POST',
                        data: { job_id: currentJobId },
                        dataType: 'json',
                        beforeSend: function() { Swal.fire({ title: 'Membatalkan...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } }); },
                        success: function(res) {
                            Swal.close();
                            if (res.success) {
                                clearInterval(pollInterval);
                                switchState('cancelled');
                                currentJobId = null;
                                table.ajax.reload();
                            }else{
                                startPolling();
                                showAlert('danger', res.message);
                            }
                        },
                        error: function (xhr) {
                            showAlert('danger', 'Terjadi kesalahan pada server saat upload.');
                            startPolling();
                        }
                    });
                }
            });
        });
        $('#btn-cancelled-reset').on('click', function () {
            resetView();
        });

        $(document).on('click','.btn-proccess-import', function () {
            const val = $(this).attr('data-id');
            if(val){
                currentJobId = val;
                switchState('running');
                setProgress(0, 0, 'Memeriksa Data...');
                startPolling();
            }
        });
    });

    function resultProgress()
    {
        var s = parseInt($('#review-success-count').attr('data-val')) || 0;
        var f = parseInt($('#review-failed-count').attr('data-val')) || 0;
        var total = s + f;
        var pct = total > 0 ? Math.round((s / total) * 100) : 0;
    
        document.getElementById('total-count').textContent = total;
        document.getElementById('pct').textContent = pct + '%';
        document.getElementById('bar-ok').style.width  = pct + '%';
        document.getElementById('bar-err').style.width = (100 - pct) + '%';
    
        if (f === 0) {
            document.getElementById('alert-failed-info').style.display = 'none';
        }
    }

    $(document).on('shown.bs.tab','a[data-bs-toggle="tab"]', function (e) {
        var targetId = $(e.target).attr('href') || $(e.target).data('bs-target');
        var $table = $(targetId).find('table.dataTable');
        if ($table.length > 0) {
            var api = $table.DataTable();
            syncTableHeader(api.settings()[0]);
        }
    });

    $(document).on('click', function(){
        setTimeout(() => {
            $('#loading').hide();
        },3000);
    })
</script>