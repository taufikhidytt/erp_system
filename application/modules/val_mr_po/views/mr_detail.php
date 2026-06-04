<table class="table table-sm table-bordered w-100 no-footer table-sm" role="grid">
    <thead style="background: var(--app-primary-th); z-index: 10; color: var(--app-primary-contrast)">
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
                <td class="text-end"><?= numb_format($v['MRD_Qty']) ?></td>
                <td><?= $v['MRD_Satuan'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>