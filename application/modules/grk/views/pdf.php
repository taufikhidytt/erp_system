<div class="section-title">
    <h1 class="report-title"><?= strtoupper('Goods Receipt Konsinyasi') ?></h1>
</div>

<table class="info-container" cellspacing="0" cellpadding="0">
    <tr>
        <td class="info-card">
            <table width="100%" class="info-row">
                <tr><td style="min-width: 80px;">No Transaksi</td><td class="info-dots">:</td><td><?= $grk->DOCUMENT_NO ?></td></tr>
                <tr><td>Supplier</td><td class="info-dots">:</td><td><?= $grk->SUPPLIER ?? '-' ?></td></tr>
                <tr><td>No Referensi</td><td class="info-dots">:</td><td><?= $grk->DOCUMENT_REFF_NO ?? '-' ?></td></tr>
            </table>
        </td>
        <td width="4%"></td>
        <td class="info-card">
            <table width="100%" class="info-row">
                <tr><td>Tanggal</td><td class="info-dots">:</td><td><?= date('d-m-Y H:i', strtotime($grk->DOCUMENT_DATE)) ?></td></tr>
                <tr><td>Gudang</td><td class="info-dots">:</td><td><?= $grk->WAREHOUSE_NAME ?? '-' ?></td></tr>

            </table>
        </td>
    </tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <td colspan="3" class="info-card">
            <table width="100%" class="info-row">
                <tr><td>Note : <?= $grk->NOTE??'-' ?></td>
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
            <th width="12%">No FPK</th>
            <th width="20%">Nama Item</th>
            <th width="5%" style="text-align:right !important">Qty</th>
            <th width="10%">Satuan</th>
            <th width="12%" style="text-align:right !important">Harga</th>
            <th width="13%" style="text-align:right !important">Subtotal</th>
            <th width="12%">Sales</th>
            <th width="13%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($grk_detail as $k => $v) { ?>
            <tr>
                <td><?=  $k+1 ?></td>
                <td><?= $v->No_FPK ?></td>
                <td><?= $v->Nama_Item ?></td>
                <td style="text-align:right !important"><?= number_format($v->Qty,2,'.',',') ?></td>
                <td><?= $v->UoM ?></td>
                <td style="text-align:right !important"><?= number_format($v->Harga,2,'.',',') ?></td>
                <td style="text-align:right !important"><?= number_format($v->Subtotal,2,'.',',') ?></td>
                <td><?= $v->Sales ?></td>
                <td><?= $v->Note ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="6" align="center">TOTAL</td>
            <td colspan="3" style="text-align:right !important"><?= number_format($grk->TOTAL_AMOUNT,2,'.',',') ?></td>
        </tr>
    </tfoot>
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