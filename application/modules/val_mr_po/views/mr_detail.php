<table class="table table-sm table-bordered w-100 no-footer table-sm" role="grid">
    <thead style="background: #3d7bb9; z-index: 10; color: #ffff">
        <tr>
            <th class="text-center">No</th>
            <th>Nama Item</th>
            <th class="text-center">Kode Item</th>
            <th class="text-end">Jumlah</th>
            <th>Satuan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($details as $k => $v) { ?>
            <tr>
                <td class="text-center"><?=  $k+1 ?></td>
                <td><?= $v['MRD_Nama_Item'] ?></td>
                <td class="text-center"><?= $v['MRD_Kode_Item'] ?></td>
                <td class="text-end"><?= number_format($v['MRD_Qty'],2,'.',',') ?></td>
                <td><?= $v['MRD_Satuan'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>