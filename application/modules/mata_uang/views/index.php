<style>
    .dt-buttons .btn-primary {
        background-color: #0d6efd;
        /* warna biru Bootstrap primary */
        border-color: #0d6efd;
        color: white;
    }
</style>
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
        <!-- end page title -->
        <div class="row">
            <div class="col-12">
                <div class="card border-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table text-center w-100 table-sm" id="table">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <?php for ($i=1; $i <=5 ; $i++) { ?> 
                                                <th>
                                                    <input type="text" placeholder="Cari.." class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                </th>
                                            <?php } ?>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="6" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;min-height:35.66px">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="7" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;min-height:35.66px">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                        </tr>
                                        <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Negara</th>
                                            <th>Kurs Awal</th>
                                            <th>Simbol</th>
                                            <th>Default</th>
                                            <th>Aktif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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
        let columns = [
            {
                "data": "no",
                "orderable": false,
                "searchable": false,
                "className" : "text-center",
                "width" : "30px"
            },
            {
                "data": "code",
                "width" : "100px"
            },
            {
                "data": "name",
            },
            {
                "data": "state",
            },
            {
                "data": "saldo_awal",
                "className" : "text-end",
            },
            {
                "data": "symbol",
                "className" : "text-center",
            },
            {
                "data": "primary_flag",
                "className" : "text-center",
                "width" : "80px"
            },
            {
                "data": "active_flag",
                "className" : "text-center",
                "width" : "70px"
            },
        ];
        var table = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>rtip',
            buttons: getButtons(<?= json_encode(button_actions(['insert',
                [
                    'key'      => 'sync',
                    'class'    => 'btn-success btn-sync',
                    'title'    => 'Sync',
                    'icon'     => 'ri-refresh-fill',
                    'needs_auth' => true,
                ],
            ], 'dt')) ?>),
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('mata_uang/get_data'); ?>",
                "type": "POST"
            },
            "columns": columns
        });
        
        $('.column_search').on('input', function() {
            table
                .column($(this).data('column'))
                .search(this.value)
                .draw();
        });
    });
</script>
<?php if(isset($access['sync']) && $access['sync']){ ?>
    <div class="modal fade" id="modal_sync" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="modal_syncLabel">Sinkronisasi Mata Uang</h5>
                        <div class="d-flex"><i class="ri-information-fill me-1"></i>Kurs tengah yang akan dijadikan kurs awal</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table text-center w-100 table-sm" id="tb-sync">
                            <thead>
                                <tr class="align-content-center" style="background: #3d7bb9; z-index: 10; color: #ffff">
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Negara</th>
                                    <th>Simbol</th>
                                    <th class="text-end">Kurs Jual</th>
                                    <th class="text-end">Kurs Tengah</th>
                                    <th class="text-end">Kurs Beli</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="save-sync">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let xhr_sync = null;
        $(document).on('click', '.btn-sync', function(){
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Mengambil data dari Bank Indonesia',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading()
                }
            });

            if(xhr_sync){
                xhr_sync.abort();
            }

            xhr_sync = $.ajax({
                url: "<?= site_url('mata_uang/get_sync_preview') ?>",
                type: "GET",
                dataType: "json",
                success: function(res) {
                    Swal.close();
                    xhr_sync = null;
                    if (res.success) {
                        if(res.result.length>0){
                            let content = '';
                            res.result.forEach(function(item, index) {
                                content += `<tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.code}</td>
                                    <td>${item.name}</td>
                                    <td>${item.state}</td>
                                    <td>${item.symbol}</td>
                                    <td class="text-end">${$.inputNumber.format(item.selling)}</td>
                                    <td class="text-end">${$.inputNumber.format(item.middle)}</td>
                                    <td class="text-end">${$.inputNumber.format(item.buying)}</td>
                                    <td class="text-center"><span class="badge bg-${item.status=='baru'?'success':'warning'} text-capitalize">${item.status}</span></td>
                                </tr>`;
                            });
                            $('#tb-sync tbody').html(content);
                            $('#tb-sync').DataTable({
                                "destroy" : true,
                                "paging": true,
                                "ordering": false,
                                "info": true,
                                "searching": true,
                                "autoWidth": false,
                                "pageLength": 10
                            });
                        }else{
                            $('#tb-sync tbody').html(`<tr><td colspan="8" class="text-center">Tidak ada data yang bisa disinkronisasi.</td></tr>`);
                        }
                        $('#modal_sync').modal('show');
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'Gagal memuat preview data BI.', 'error');
                    xhr_sync = null;
                }
            });
        });
        $(document).on('click', '#save-sync', function(){
            Swal.fire({
                title: 'Apakah Anda yakin ingin menyimpan data?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Lanjutkan!',
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
                    
                    if(xhr_sync){
                        xhr_sync.abort();
                    }
                    
                    xhr_sync = $.ajax({
                        url: "<?= site_url('mata_uang/save_sync') ?>",
                        type: "POST",
                        dataType: "json",
                        success: function(res) {
                            xhr_sync = null;
                            if (res.success) {
                                Swal.fire('Berhasil!', res.message, 'success').then(() => {
                                    window.location.href = res.result;
                                });
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function() {
                            xhr_sync = null;
                            Swal.fire('Error', 'Terjadi kesalahan pada server saat sinkronisasi.', 'error');
                        }
                    });
                }
            });
        });
</script>
<?php } ?>
