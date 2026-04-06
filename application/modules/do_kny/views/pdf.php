<style>
    .items-table thead th {
        font-size: 10px !important;
    }
    .items-table td {
        font-size: 9px !important;
    }
</style>

<div class="section-title">
    <h1 class="report-title"><?= strtoupper('DELIVERY ORDER') ?></h1>
</div>

<table class="info-container" cellspacing="0" cellpadding="0">
    <tr>
        <td class="info-card" style="border-left: 4px dolid #8a9e8c;">
            <table width="100%" class="info-row">
                <tr><td style="min-width: 70px !important;">No Transaksi</td><td class="info-dots">:</td><td><?= $do->DOCUMENT_NO ?></td></tr>
                <tr><td>Customer</td><td class="info-dots">:</td><td><?= $do->Customer ?? '-' ?></td></tr>
                <tr><td>Location</td><td class="info-dots">:</td><td><?= $do->SITE_NAME ?? '-' ?></td></tr>
                <tr><td></td><td class="info-dots"></td><td><?= ($do->ADDRESS1 ?? '-').($do->CITY ?' '.$do->CITY: '') ?></td></tr>
            </table>
        </td>
        <td width="4%"></td>
        <td class="info-card" style="border-left: 4px dolid #c4a49a;">
            <table width="100%" class="info-row">
                <tr><td>Tanggal</td><td class="info-dots">:</td><td><?= date('d-m-Y H:i', strtotime($do->DOCUMENT_DATE)) ?></td></tr>
                <tr><td>Sales</td><td class="info-dots">:</td><td><?= ($do->SALES ?? '-').($do->SALES_LAST_NAME?' ['.$do->SALES_LAST_NAME.']':'') ?></td></tr>
                <tr><td>PO Customer</td><td class="info-dots">:</td><td><?= $do->DOCUMENT_REFF_NO ?? '-' ?></td></tr>
                <tr><td>Storage</td><td class="info-dots">:</td><td><?= $do->WAREHOUSE_NAME ?? '-' ?></td></tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <td colspan="3" class="info-card" style="border-left: 4px dolid #8a9e8c;">
            <table width="100%" class="info-row">
                <tr><td>Note : <?= $do->NOTE??'-' ?></td>
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
            <th width="12%">No SO</th>
            <th width="12%">No MR</th>
            <th width="22%">Nama Item</th> <th width="18%">Memo</th>
            <th width="8%" style="text-align:right !important">Jumlah</th>
            <th width="8%">Satuan</th>    <th width="15%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($do_detail as $k => $v) { ?>
            <tr>
                <td><?=  $k+1 ?></td>
                <td><?= $v->No_SO ?></td>
                <td><?= $v->No_MR ?></td>
                <td><?= $v->Nama_Item ?></td>
                <td><?= $v->MEMO ?? '-' ?></td>
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