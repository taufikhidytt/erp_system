<style>
table.dataTable.table-sm>thead>tr>th :not(.sorting_disabled){
    padding-right: 0;
}
</style>
<div class="page-content" data-aos="zoom-in" data-date="<?= date('Y-m-d') ?>">
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
                            <li class="breadcrumb-item active text-decoration-underline"><?= $breadcrumb ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Preview Data Kurs Bank Indonesia</h5>
                            <span class="text-muted">Kurs Tengah yang akan digunakan</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" id="btn-sync" style="display: none;">
                                <i class="ri-refresh-line align-middle me-1"></i> Proses Sync
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="preview-container" style="display: none;">
                            <div class="table-responsive">
                                <table class="table align-middle table-sm w-100" id="preview-table">
                                    <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)" class="align-middle">
                                        <tr>
                                            <th rowspan="2" class="text-center">
                                                <div class="form-check d-flex justify-content-center">
                                                    <input type="checkbox" class="form-check-input fs-5" id="check-all" checked>
                                                </div>
                                            </th>
                                            <th rowspan="2" class="text-center" style="width: 50px;">No</th>
                                            <th rowspan="2">Kode</th>
                                            <th rowspan="2">Nama</th>
                                            <th rowspan="2">Negara</th>
                                            <th colspan="2" class="text-center">Data Terakhir</th>
                                            <th colspan="4" class="text-center">Data Terbaru Pada BI</th>
                                            <th rowspan="2" class="text-center">Status</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-end">Rate</th>
                                            <th class="text-center">Tanggal Scrape</th>
                                            <th class="text-end">Kurs Jual</th>
                                            <th class="text-end">Kurs Tengah</th>
                                            <th class="text-end">Kurs Beli</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data populated by JS -->
                                    </tbody>
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
$(document).ready(function() {
    let dataTableInstance = null;

    Swal.fire({
        title: 'Memuat Data...',
        text: 'Mengambil data dari Bank Indonesia',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading()
        }
    });

    // Load Preview
    $.ajax({
        url: "<?= site_url('kursdate/get_sync_preview') ?>",
        type: "GET",
        dataType: "json",
        success: function(response) {
            Swal.close();
            if (response.success) {
                const res = response.result;
                $('#preview-container').show();
                if (res.data.length > 0) {
                    $('#btn-sync').show();
                    let tbody = '';
                    res.data.forEach(function(item, index) {
                        let statusBadge = item.status == 'INSERT' 
                            ? '<span class="badge bg-success">Baru</span>' 
                            : '<span class="badge bg-warning text-dark">Perbarui</span>';
                            
                        let lastDateStr = item.last_date ? item.last_date : '<i class="text-muted">Belum ada</i>';

                        tbody += `
                            <tr>
                                <td class="text-center">
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox" class="form-check-input row-check fs-5" data-index="${index}" value="${item.id}" checked>
                                    </div>
                                </td>
                                <td class="text-center">${index + 1}</td>
                                <td>${item.code}</td>
                                <td>${item.name}</td>
                                <td>${item.state}</td>
                                <td class="text-center">${lastDateStr}</td>
                                <td class="text-end${item.is_match?' bg-success text-white':''}">${(item.last_rate?$.inputNumber.format(item.last_rate):'-')}</td>
                                <td class="text-center">${item.bi_date}</td>
                                <td class="text-end">${(item.selling?$.inputNumber.format(item.selling):'-')}</td>
                                <td class="text-end${item.is_match?' bg-success text-white':''}">${(item.middle?$.inputNumber.format(item.middle):'-')}</td>
                                <td class="text-end">${(item.buying?$.inputNumber.format(item.buying):'-')}</td>
                                <td class="text-center">${statusBadge}</td>
                            </tr>
                        `;
                    });
                    $('#preview-table tbody').html(tbody);
                    
                    // Initialize DataTables
                    dataTableInstance = $('#preview-table').DataTable({
                        "destroy" : true,
                        "paging": true,
                        "ordering": false,
                        "info": true,
                        "searching": true,
                        "autoWidth": false,
                        "pageLength": 10
                    });
                } else {
                    $('#preview-table tbody').html('<tr><td colspan="9" class="text-center">Tidak ada data aktif yang bisa disinkronisasi.</td></tr>');
                }
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            Swal.fire('Error', 'Gagal memuat preview data BI.', 'error');
        }
    });

    // Check All Checkbox
    $('#check-all').on('change', function() {
        let isChecked = $(this).is(':checked');
        if (dataTableInstance) {
            dataTableInstance.$('.row-check').prop('checked', isChecked);
        }
    });

    // Process Sync
    $('#btn-sync').click(function() {
        let selectedData = [];
        if (dataTableInstance) {
            dataTableInstance.$('.row-check:checked').each(function() {
                selectedData.push($(this).val());
            });
        }

        if (selectedData.length === 0) {
            Swal.fire('Peringatan', 'Silakan pilih minimal satu data untuk disinkronisasi.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Proses Sinkronisasi pada tanggal '+$('.page-content').attr('data-date')+'?',
            text: `Terdapat ${selectedData.length} data kurs akan diperbarui ke database.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                $.ajax({
                    url: "<?= site_url('kursdate/save_sync') ?>",
                    type: "POST",
                    dataType: "json",
                    data: { sync_data: selectedData },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                window.location.href = res.result;
                            });
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Terjadi kesalahan pada server saat sinkronisasi.', 'error');
                    }
                });
            }
        });
    });
});
</script>
