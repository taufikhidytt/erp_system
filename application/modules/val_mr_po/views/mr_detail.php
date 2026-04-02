<table class="table table-sm table-bordered w-100 no-footer" role="grid">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Item</th>
            <th>Kode Item</th>
            <th class="text-end">Jumlah</th>
            <th>Satuan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($details as $k => $v) { ?>
            <tr>
                <td><?=  $k+1 ?></td>
                <td><?= $v['MRD_Nama_Item'] ?></td>
                <td><?= $v['MRD_Kode_Item'] ?></td>
                <td class="text-end"><?= number_format($v['MRD_Qty'],2,'.',',') ?></td>
                <td><?= $v['MRD_Satuan'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>