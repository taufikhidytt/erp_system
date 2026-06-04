<ul class="nav nav-tabs mt-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#rv-success" role="tab" aria-selected="true">
            <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Data Siap Import"><i class="ri ri-checkbox-circle-line"></i></span>
            <span class="d-none d-sm-block">Data Siap Import</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#rv-failed" role="tab" aria-selected="false">
            <span class="d-block d-sm-none" data-toggle="tooltip" data-placement="bottom" title="Data Perlu Diperbaiki"><i class=" ri ri-close-circle-line"></i></span>
            <span class="d-none d-sm-block">Data Perlu Diperbaiki</span>
        </a>
    </li>
</ul>
<div class="tab-content px-0 py-3 text-muted">
    <div class="tab-pane active form-xs" id="rv-success" role="tabpanel">
        <?php if($success_count>0){ ?>
            <div class="table-responsive">
                <table id="tbl-rv-success" class="table mt-3 w-100 table-sm align-middle table-striped">
                    <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                        <tr>
                            <th>Baris</th>
                            <?php foreach ($header as $k => $v) { ?>
                                <th<?= in_array($k,['COA_SALDO','KURS','SALDO_AWAL_KURS'])?' class="text-end"':'' ?>><?= $v ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['success'] as $k => $v) { ?>
                        <tr>
                            <td><?= $v['row_number'] ?></td>
                            <?php foreach ($header as $key => $v2) { ?>
                                <td<?= in_array($key,['COA_SALDO','KURS','SALDO_AWAL_KURS'])?' class="text-end"':'' ?>>
                                    <?= in_array($key,['COA_SALDO','KURS','SALDO_AWAL_KURS'])? numb_format($v[$key]):$v[$key] ?>
                                </td>
                            <?php } ?>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="mdi mdi-alert-outline me-2"></i>
                Tidak ditemukan data siap import, periksa kembali data nya.
            </div>
        <?php }?>
    </div>
    <div class="tab-pane form-xs" id="rv-failed" role="tabpanel">
        <?php if($failed_count>0){ ?>
            <div class="table-responsive">
                <table id="tbl-rv-failed" class="table mt-3 w-100 table-sm align-middle table-striped">
                    <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
                        <tr>
                            <th>Baris</th>
                            <?php foreach ($header as $k => $v) { ?>
                                <th<?= in_array($k,['COA_SALDO','KURS','SALDO_AWAL_KURS'])?' class="text-end"':'' ?>><?= $v ?></th>
                            <?php } ?>
                            <th style="min-width: 150px;">Pesan Gagal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result['failed'] as $k => $v) {
                            $error_reason = explode(';', $v['error_reason']);
                            ?>
                        <tr>
                            <td><?= $v['row_number'] ?></td>
                            <?php foreach ($header as $key => $v2) { ?>
                                <td<?= in_array($key,['COA_SALDO','KURS','SALDO_AWAL_KURS'])?' class="text-end"':'' ?>>
                                    <?= in_array($key,['COA_SALDO','KURS','SALDO_AWAL_KURS'])? numb_format($v[$key]):$v[$key] ?>
                                </td>
                            <?php } ?>
                            <td>
                                <ul class="m-0 ms-3 p-0">
                                    <?php foreach ($error_reason as $err) { echo '<li>'.$err.'</li>'; } ?>
                                </ul>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-all me-2"></i>
                Tidak ditemukan data yang perlu diperbaiki 
            </div>
        <?php }?>
    </div>
</div>