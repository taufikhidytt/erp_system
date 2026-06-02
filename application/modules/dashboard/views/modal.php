<style>
    #log-info-modal .modal-dialog {
        animation: modalSlideIn 0.35s cubic-bezier(0.34, 1.26, 0.64, 1) both;
    }
    @keyframes modalSlideIn {
        from { opacity: 0; transform: translateY(-40px) scale(0.97); }
        to   { opacity: 1; transform: translateY(0)    scale(1);    }
    }

    /* ---- Info bar Created / Updated ---- */
    .log-meta-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 10px 14px;
        background: #f0f6ff;
        border-bottom: 1px solid #d0e4f7;
        font-family: 'Poppins', sans-serif !important;
        font-size: 0.72rem;
        animation: fadeSlideDown 0.4s ease both;
        animation-delay: 0.15s;
    }
    @keyframes fadeSlideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0);    }
    }
    .log-meta-card {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        background: #fff;
        border: 1px solid #d6e8f7;
        border-radius: 8px;
        padding: 7px 12px;
        flex: 1 1 200px;
        min-width: 180px;
        box-shadow: 0 1px 4px rgba(61,123,185,0.08);
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .log-meta-card:hover {
        box-shadow: 0 4px 12px rgba(61,123,185,0.18);
        transform: translateY(-1px);
    }
    .log-meta-icon {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .log-meta-icon.created { background: #e0f2e9; color: #1a8a4a; }
    .log-meta-icon.updated { background: #fff3cd; color: #c87d00; }
    .log-meta-label {
        font-weight: 600;
        color: #5a7a9e;
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        line-height: 1;
        margin-bottom: 3px;
    }
    .log-meta-value {
        color: #1e3a5f;
        font-weight: 500;
        font-size: 0.72rem;
        line-height: 1.3;
    }
    .log-meta-value .meta-user {
        color: #3d7bb9;
        font-weight: 600;
    }
    .log-meta-value .meta-date {
        color: #555;
        font-size: 0.67rem;
    }

    /* ---- Badge dot animasi ---- */
    .meta-dot {
        display: inline-block;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        margin-right: 4px;
        vertical-align: middle;
    }
    .meta-dot.created {
        background: #1a8a4a;
        animation: dotPulseGreen 2s ease-in-out infinite;
    }
    .meta-dot.updated {
        background: #c87d00;
        animation: dotPulseOrange 2s ease-in-out infinite;
    }
    @keyframes dotPulseGreen {
        0%, 100% { box-shadow: 0 0 0 0 rgba(26,138,74,0.4); }
        50%       { box-shadow: 0 0 0 5px rgba(26,138,74,0);  }
    }
    @keyframes dotPulseOrange {
        0%, 100% { box-shadow: 0 0 0 0 rgba(200,125,0,0.4); }
        50%       { box-shadow: 0 0 0 5px rgba(200,125,0,0);  }
    }

    /* ---- Footer ---- */
    #log-info-modal .modal-footer {
        background: #f8fafd;
        border-top: 1px solid #d6e4f0;
        padding: 8px 14px;
        font-family: 'Poppins', sans-serif !important;
        font-size: 0.72rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .log-footer-info {
        color: #7a97b5;
        font-size: 0.68rem;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .log-footer-info i {
        font-size: 0.8rem;
    }
</style>

<div class="modal fade" id="log-info-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border:none; border-radius:12px; overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,0.18);">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title" id="log-info-modalLabel">Log &amp; History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- META BAR: Created & Updated -->
            <div class="log-meta-bar" id="log-meta-bar">

                <!-- Created -->
                <div class="log-meta-card">
                    <div class="log-meta-icon created">
                        <i class="mdi mdi-plus-circle-outline"></i>
                    </div>
                    <div>
                        <div class="log-meta-label">
                            <span class="meta-dot created"></span>Created
                        </div>
                        <div class="log-meta-value">
                            <span class="meta-user" id="log-created-by">—</span><br>
                            <span class="meta-date" id="log-created-at">—</span>
                        </div>
                    </div>
                </div>

                <!-- Updated -->
                <div class="log-meta-card">
                    <div class="log-meta-icon updated">
                        <i class="mdi mdi-pencil-circle-outline"></i>
                    </div>
                    <div>
                        <div class="log-meta-label">
                            <span class="meta-dot updated"></span>Last Updated
                        </div>
                        <div class="log-meta-value">
                            <span class="meta-user" id="log-updated-by">—</span><br>
                            <span class="meta-date" id="log-updated-at">—</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BODY: Tabel Log -->
            <div class="modal-body">
                <table class="table text-center w-100 table-sm mb-0" id="log-info-table">
                    <thead>
                        <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                            <th width="50px">No</th>
                            <th width="100px">Tanggal</th>
                            <th>User</th>
                            <th>Transaksi</th>
                            <th>Log</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <div class="log-footer-info">
                    <i class="mdi mdi-information-outline"></i>
                    Menampilkan riwayat perubahan data
                </div>
                <button type="button" class="btn btn-light waves-effect" data-bs-dismiss="modal">
                    <i class="mdi mdi-close me-1"></i>Tutup
                </button>
            </div>

        </div>
    </div>
</div>

<script src="<?= base_url('assets/admin/js/log-info.js?v=1.3') ?>"></script>

<script>
function fillLogMeta(data) {
    const fmt = (val) => val && val !== 'null' ? val : '—';

    document.getElementById('log-created-by').textContent = fmt(data.created_by);
    document.getElementById('log-created-at').textContent = fmt(data.created_at);
    document.getElementById('log-updated-by').textContent = fmt(data.updated_by);
    document.getElementById('log-updated-at').textContent = fmt(data.updated_at);

    /* Re-trigger animasi bar setiap kali modal dibuka */
    const bar = document.getElementById('log-meta-bar');
    bar.style.animation = 'none';
    void bar.offsetWidth; // reflow
    bar.style.animation = '';
}

/* Animasi row table: set --row-index agar delay stagger bekerja */
document.getElementById('log-info-modal').addEventListener('shown.bs.modal', function () {
    document.querySelectorAll('#log-info-table tbody tr').forEach(function(tr, i) {
        tr.style.setProperty('--row-index', i);
    });
});

/* Reset meta ke "—" saat modal ditutup */
document.getElementById('log-info-modal').addEventListener('hidden.bs.modal', function () {
    ['log-created-by','log-created-at','log-updated-by','log-updated-at'].forEach(function(id) {
        document.getElementById(id).textContent = '—';
    });
});
</script>