<div class="section-title">
    <h1 class="report-title"><?= strtoupper('Material Requirement') ?></h1>
</div>

<table class="info-container" cellspacing="0" cellpadding="0">
    <tr>
        <td class="info-card" style="border-left: 4px solid #8a9e8c;">
            <table width="100%" class="info-row">
                <tr><td>No Transaksi</td><td class="info-dots">:</td><td><?= $mrq->DOCUMENT_NO ?></td></tr>
                <tr><td>Ship To</td><td class="info-dots">:</td><td><?= $mrq->SHIP_TO ?? '-' ?></td></tr>
                <tr><td>Location</td><td class="info-dots">:</td><td><?= $mrq->SITE_NAME ?? '-' ?></td></tr>
                <tr><td>&nbsp;</td><td class="info-dots"></td><td><?= $mrq->ADDRESS1 ?? '-' ?><?= '. '.$mrq->CITY ?? '-' ?></td></tr>
                <tr><td>Storage</td><td class="info-dots">:</td><td><?= $mrq->WAREHOUSE_NAME ?? '-' ?></td></tr>
                <tr><td>Item Finish Goods</td><td class="info-dots">:</td><td><?= $mrq->ITEM_NAME ?? '-' ?></td></tr>
                <tr><td>Jumlah</td><td class="info-dots">:</td><td><?= ($mrq->ITEM_NAME && $mrq->ENTERED_QTY)? number_format($mrq->ENTERED_QTY,2,'.',',') : '-' ?></td></tr>
                <tr><td>Satuan</td><td class="info-dots">:</td><td><?= $mrq->ENTERED_UOM ?? '-' ?></td></tr>
            </table>
        </td>
        <td width="4%"></td>
        <td class="info-card" style="border-left: 4px solid #c4a49a;">
            <table width="100%" class="info-row">
                <tr><td>Tanggal</td><td class="info-dots">:</td><td><?= date('d-m-Y H:i', strtotime($mrq->DOCUMENT_DATE)) ?></td></tr>
                <tr><td>Ship Date</td><td class="info-dots">:</td><td><?= date('d-m-Y H:i', strtotime($mrq->SHIP_DATE)) ?></td></tr>
                <tr><td>Reff Cust (MO)</td><td class="info-dots">:</td><td><?= $mrq->DOCUMENT_REFF_NO ?? '-' ?></td></tr>
                <tr><td>Reff PR</td><td class="info-dots">:</td><td><?= $mrq->REFF_PR ?? '-' ?></td></tr>
                <tr><td>Unit</td><td class="info-dots">:</td><td><?= $mrq->UNIT ?? '-' ?></td></tr>
                <tr><td>Code</td><td class="info-dots">:</td><td><?= $mrq->LOKASI ?? '-' ?></td></tr>
                <tr><td>Hour Minutes</td><td class="info-dots">:</td><td><?= $mrq->HOUR_MINUTES ?? '-' ?></td></tr>
            </table>
        </td>
    </tr>
    <tr><td colspan="3">&nbsp;</td></tr>
    <tr>
        <td colspan="3" class="info-card" style="border-left: 4px solid #8a9e8c;">
            <table width="100%" class="info-row">
                <tr><td>Note : <?= $mrq->NOTE??'-' ?></td>
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
            <th width="29%">No Transaksi</th>
            <th width="29%">Nama Item</th>
            <th width="8%" style="text-align:right !important">Jumlah</th>
            <th width="10%">Satuan</th>
            <th width="21%">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($mrq_detail as $k => $v) { ?>
            <tr>
                <td><?=  $k+1 ?></td>
                <td><?= $v->Reff_Trx ?></td>
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