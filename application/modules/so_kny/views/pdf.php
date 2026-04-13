<style>
    .items-table thead th {
        font-size: 10px !important;
    }
    .items-table td {
        font-size: 9px !important;
    }
</style>

<div class="section-title" style="margin-top:30px">
    <h1 class="report-title"><?= strtoupper('SALES ORDER') ?></h1>
</div>

<table class="info-container" cellspacing="0" cellpadding="0">
    <tr>
        <td class="info-card">
            <table width="100%" class="info-row">
                <tr><td style="min-width: 70px !important;">No Transaksi</td><td class="info-dots">:</td><td><?= $so->DOCUMENT_NO ?></td></tr>
                <tr><td>Customer</td><td class="info-dots">:</td><td><?= $so->Customer ?? '-' ?></td></tr>
                <tr><td>Location</td><td class="info-dots">:</td><td><?= $so->SITE_NAME ?? '-' ?></td></tr>
                <tr><td></td><td class="info-dots"></td><td><?= $so->ADDRESS1 ?? '-' ?></td></tr>
                <tr><td>Payment Term</td><td class="info-dots">:</td><td><?= $so->PAYMENT_TERM_NAME ?? '-' ?></td></tr>
            </table>
        </td>
        <td width="4%"></td>
        <td class="info-card">
            <table width="100%" class="info-row">
                <tr><td>Tanggal</td><td class="info-dots">:</td><td><?= date('d-m-Y H:i', strtotime($so->DOCUMENT_DATE)) ?></td></tr>
                <tr><td>Sales</td><td class="info-dots">:</td><td><?= ($so->SALES ?? '-').($so->SALES_LAST_NAME?' ['.$so->SALES_LAST_NAME.']':'') ?></td></tr>
                <tr><td>PO Customer</td><td class="info-dots">:</td><td><?= $so->PO_Customer ?? '-' ?></td></tr>
                <tr><td>No Referensi</td><td class="info-dots">:</td><td><?= $so->DOCUMENT_REFF_NO ?? '-' ?></td></tr>
                <tr><td>Storage</td><td class="info-dots">:</td><td><?= $so->WAREHOUSE_NAME ?? '-' ?></td></tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <td colspan="3" class="info-card">
            <table width="100%" class="info-row">
                <tr><td>Note : <?= $so->NOTE??'-' ?></td>
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
            <th width="5%">No</th>
            <th width="12%">No MR</th>
            <th width="33%">Nama Item</th>
            <th width="8%" style="text-align:right !important">Jumlah</th>
            <th width="7%">Satuan</th>
            <th width="10%" style="text-align:right !important">Harga</th>
            <th width="7%" style="text-align:right !important">Diskon (Rp)</th>
            <th width="10%" style="text-align:right !important">Subtotal</th>
            <th width="8%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($so_detail as $k => $v) {
            $diskon     = (float) $v->Diskon;
            $deskripsi  = trim($v->DESKRIPSI);
            ?>
            <tr>
                <td><?=  $k+1 ?></td>
                <td><?= $v->No_MR ?></td>
                <td><?= $deskripsi?: $v->Nama_Item ?></td>
                <td style="text-align:right !important"><?= number_format($v->Qty,2,'.',',') ?></td>
                <td><?= $v->UoM ?></td>
                <td style="text-align:right !important"><?= number_format($v->Harga,2,'.',',') ?></td>
                <td style="text-align:right !important"><?= $diskon?number_format($diskon,2,'.',','):'-' ?></td>
                <td style="text-align:right !important"><?= number_format($v->Total,2,'.',',') ?></td>
                <td><?= $v->Note ?></td>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="7" style="text-align:right !important">TOTAL</td>
            <td colspan="2" style="text-align:right !important"><?= number_format($so->TOTAL_AMOUNT,2,'.',',') ?></td>
        </tr>
        <tr class="total-row">
            <td colspan="7" style="text-align:right !important">Diskon</td>
            <td colspan="2" style="text-align:right !important"><?= number_format($so->TOTAL_DISCOUNT,2,'.',',') ?></td>
        </tr>
        <tr class="total-row">
            <td colspan="7" style="text-align:right !important">PPN</td>
            <td colspan="2" style="text-align:right !important"><?= number_format($so->PPN_AMOUNT,2,'.',',') ?></td>
        </tr>
        <tr class="total-row">
            <td colspan="7" style="text-align:right !important">GRAND TOTAL</td>
            <td colspan="2" style="text-align:right !important"><?= number_format($so->TOTAL_NET,2,'.',',') ?></td>
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