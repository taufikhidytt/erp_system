<div class="section-title">
    <h1 class="report-title"><?= strtoupper('Receive in Site') ?></h1>
</div>

<table class="info-container" cellspacing="0" cellpadding="0">
    <tr>
        <td class="info-card">
            <table width="100%" class="info-row">
                <tr><td>No Transaksi</td><td class="info-dots">:</td><td><?= $rcv->DOCUMENT_NO ?></td></tr>
                <tr><td>Site Storage</td><td class="info-dots">:</td><td><?= $rcv->TO_WAREHOUSE_NAME ?? '-' ?></td></tr>
                <tr><td>Main Storage</td><td class="info-dots">:</td><td><?= $rcv->WAREHOUSE_NAME ?? '-' ?></td></tr>
            </table>
        </td>
        <td width="4%"></td>
        <td class="info-card">
            <table width="100%" class="info-row">
                <tr><td>Tanggal</td><td class="info-dots">:</td><td><?= date('d-m-Y H:i', strtotime($rcv->DOCUMENT_DATE)) ?></td></tr>
                <tr><td>No Referensi</td><td class="info-dots">:</td><td><?= $rcv->DOCUMENT_REFF_NO ?? '-' ?></td></tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <td colspan="3" class="info-card">
            <table width="100%" class="info-row">
                <tr><td>Note : <?= $rcv->NOTE??'-' ?></td>
            </table>
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th colspan="5" style="background-color: #ffffff; color:#ffffff">&nbsp;</th>
        </tr>
        <tr>
            <th width="3%">No</th>
            <th width="29%">No SJS</th>
            <th width="29%">Nama Item</th>
            <th width="8%" style="text-align:right !important">Jumlah</th>
            <th width="10%">Satuan</th>
            <th width="21%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rcv_detail as $k => $v) { ?>
            <tr>
                <td><?=  $k+1 ?></td>
                <td><?= $v->No_SJS ?></td>
                <td><?= $v->Nama_Item ?></td>
                <td style="text-align:right !important"><?= number_format($v->Qty,2,'.',',') ?></td>
                <td><?= $v->UoM ?></td>
                <td><?= $v->Note ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<table class="signature-table">
    <tr>
        <td width="33%">
            <div class="sig-title">DIBUAT</div>
            <div style="height: 60px;"></div>
            <div class="sig-name"><?= $this->session->nama ?></div>
        </td>
        <td width="33%">
            <div class="sig-title">APPROVAL</div>
            <div style="height: 60px;"></div>
            <div class="sig-name"></div>
        </td>
        <td width="33%">
            <div class="sig-title">PENERIMA</div>
            <div style="height: 60px;"></div>
            <div class="sig-name"></div>
        </td>
    </tr>
</table>