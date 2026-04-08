<div class="section-title">
    <h1 class="report-title">FORM PENGAJUAN KONSINYASI</h1>
</div>

<table class="info-container" cellspacing="0" cellpadding="0">
    <tr>
        <td class="info-card">
            <table width="100%" class="info-row">
                <tr><td>No Transaksi</td><td class="info-dots">:</td><td><?= $fpk->DOCUMENT_NO ?></td></tr>
                <tr><td>No Referensi</td><td class="info-dots">:</td><td><?= $fpk->DOCUMENT_REFF_NO ?? '-' ?></td></tr>
                <tr><td>Supplier</td><td class="info-dots">:</td><td><?= $fpk->SUPPLIER ?? '-' ?></td></tr>
            </table>
        </td>
        <td width="4%"></td>
        <td class="info-card">
            <table width="100%" class="info-row">
                <tr><td>Tanggal</td><td class="info-dots">:</td><td><?= date('d-m-Y H:i', strtotime($fpk->DOCUMENT_DATE)) ?></td></tr>
                <tr><td>Gudang</td><td class="info-dots">:</td><td><?= $fpk->WAREHOUSE_NAME ?? '-' ?></td></tr>
                <tr><td>Sales</td><td class="info-dots">:</td><td><?= $fpk->SALES_FIRST_NAME.($fpk->SALES_LAST_NAME?' ['.$fpk->SALES_LAST_NAME.']':'') ?></td></tr>

            </table>
        </td>
    </tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <td colspan="3" class="info-card">
            <table width="100%" class="info-row">
                <tr><td>Note : <?= $fpk->NOTE??'-' ?></td>
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
            <th width="28%">Nama Item</th> <th width="8%" style="text-align:right !important">Qty</th>
            <th width="10%">Satuan</th>
            <th width="15%" style="text-align:right !important">Harga</th>
            <th width="15%" style="text-align:right !important">Subtotal</th>
            <th width="21%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($fpk_detail as $k => $v) { ?>
            <tr>
                <td><?=  $k+1 ?></td>
                <td><?= $v->Item_Name ?></td>
                <td style="text-align:right !important"><?= number_format($v->QTY,2,'.',',') ?></td>
                <td><?= $v->ENTERED_UOM ?></td>
                <td style="text-align:right !important"><?= number_format($v->PRICE,2,'.',',') ?></td>
                <td style="text-align:right !important"><?= number_format($v->TOTAL,2,'.',',') ?></td>
                <td><?= $v->NOTE ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="5" style="text-align:right !important">TOTAL</td>
            <td colspan="2" style="text-align:right !important"><?= number_format($fpk->TOTAL_AMOUNT,2,'.',',') ?></td>
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