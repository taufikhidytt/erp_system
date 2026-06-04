<style>
    .dt-buttons .btn {
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
                                            <?php for ($i = 1; $i <= 9; $i++) { ?>
                                                <th>
                                                    <input type="text" placeholder="Cari.." class="column_search" data-column="<?= $i ?>" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                                </th>
                                            <?php } ?>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="10" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;min-height:35.66px;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="11" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="12" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="13" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="date" placeholder="Cari.." class="column_search" data-column="14" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th>
                                                <input type="date" placeholder="Cari.." class="column_search" data-column="15" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                            <th style="min-width: 50px;">
                                                <select class="column_search" data-column="16" style="border-radius: 5%; border: 1px solid #CED4DA; padding: 8px; width: 100%;min-height:35.66px;">
                                                    <option value="">All</option>
                                                    <option value="Y">✔</option>
                                                    <option value="N">✖</option>
                                                </select>
                                            </th>
                                            <th>
                                                <input type="text" placeholder="Cari.." class="column_search" data-column="17" style="border-radius: 5%; box-sizing: border-box; border: 1px solid #CED4DA; padding: 8px; width: 100%;">
                                            </th>
                                        </tr>
                                        <tr class="align-middle text-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                            <th rowspan="2">No</th>
                                            <th rowspan="2">Kode Asset</th>
                                            <th rowspan="2">Nama Asset</th>
                                            <th rowspan="2">Qty</th>
                                            <th rowspan="2">Metode Depresiasi</th>
                                            <th rowspan="2">Nilai Asset</th>
                                            <th rowspan="2">Nilai Penyusutan</th>

                                            <th colspan="3" class="text-center">Account</th>

                                            <th rowspan="2">Intangible Asset</th>
                                            <th rowspan="2">Umur Asset</th>
                                            <th rowspan="2">Rate Depresiasi</th>
                                            <th rowspan="2">Tipe Asset</th>

                                            <th colspan="2" class="text-center">Tanggal</th>

                                            <th rowspan="2">Aktif</th>
                                            <th rowspan="2">Note</th>
                                        </tr>
                                        <tr class="align-content-center" style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                                            <th>Asset</th>
                                            <th>Debet</th>
                                            <th>Kredit</th>
                                            <th>Aktif</th>
                                            <th>Non Aktif</th>
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
        let columns = [{
                "data": "no",
                "orderable": false,
                "searchable": false,
                "className": "text-center",
                "width": "30px"
            },
            {
                "data": "kode_asset",
                "width": "150px"
            },
            {
                "data": "nama_asset",
            },
            {
                "data": "qty",
            },
            {
                "data": "metode_depresiasi",
            },
            {
                "data": "nilai_asset",
            },
            {
                "data": "nilai_penyusutan",
            },
            {
                "data": "asset",
            },
            {
                "data": "debet",
            },
            {
                "data": "kredit",
            },
            {
                "data": "intantangible_asset",
                "className": "text-center",
                "width": "70px"
            },
            {
                "data": "umur_asset",
            },
            {
                "data": "rate_depresiasi",
            },
            {
                "data": "tipe_asset",
            },
            {
                "data": "aktif",
            },
            {
                "data": "non_aktif",
            },
            {
                "data": "active_flag",
                "className": "text-center",
                "width": "70px"
            },
            {
                "data": "note",
            },
        ];
        var table = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>rtip',
            buttons: getButtons(<?= json_encode(button_actions(['insert'], 'dt')) ?>),
            "autoWidth": true,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "order": [],
            "ajax": {
                "url": "<?= site_url('asset/get_data'); ?>",
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