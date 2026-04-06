<div class="card border-2">
    <div class="card-header">
        <div class="card-title"><h5>Daftar Perbandingan Item</h5></div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped text-center w-100" id="table-mr">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No. SO</th>
                        <th>No. MR</th>
                        <th>No. PO Customer</th>
                        <th>Nama Item SO</th>
                        <th>Nama Item MR</th>
                        <th class="text-end">QTY MR</th>
                        <th class="text-end">QTY PO</th>
                        <th class="text-end">Selisih</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $last_id = 0;
                        foreach ($mrq as $k => $item) {
                            $is_rowspan = false;
                            if($last_id != $item['BUILD_ID']){
                                $is_rowspan = true;
                                $last_id = $item['BUILD_ID'];
                            }    
                        ?>
                        <tr>
                            <td><?= ($no++) ?></td>
                            <td><?= $item['SO_No'] ?></td>
                            <td><?= $item['MR_No'] ?></td>
                            <td><?= $item['PO_NO'] ?></td>
                            <td><?= $item['SO_Nama_Item'] ?></td>
                            <td><?= $item['MR_Nama_Item'] ?></td>
                            <td class="text-end"><?= number_format($item['MR_Qty'],2,'.',',') ?></td>
                            <td class="text-end"><?= number_format($item['PO_Qty'],2,'.',',') ?></td>
                            <td class="text-end"><?= number_format($item['Qty_Difference'],2,'.',',') ?></td>
                            <td>
                                <span class="badge bg-<?= $item['Match_Status']=='MATCH'?'success':($item['Match_Status']=='OVER_QTY'?'warning':'danger') ?>">
                                    <?= str_replace('_',' ',$item['Match_Status']) ?>
                                </span>
                                <?php if($item['APPROVED_FLAG'] == 'Y'){
                                    echo '<br/> <span class="badge bg-success">Approved</span>';
                                } ?>
                            </td>
                            <?php if($is_rowspan){ ?>
                                <td rowspan="<?= $mrq_count[$item['BUILD_ID']] ?>" class="text-center align-middle">
                                    <?php if($item['APPROVED_FLAG'] == 'Y' && ((float) $item['RECEIVED_ENTERED_QTY'])==0){ ?>
                                        <button class="btn btn-sm btn-danger btn-approve" title="Unapprove" data-bs-toggle="tooltip" data-bs-placement="top" data-value="N" data-id="<?= base64url_encode($this->encrypt->encode($item['BUILD_ID'])) ?>"><i class="fa fa-times"></i></button>
                                    <?php } else if($item['APPROVED_FLAG'] != 'Y'){ ?>
                                        <button class="btn btn-sm btn-primary btn-approve" title="Approve" data-bs-toggle="tooltip" data-bs-placement="top" data-value="Y" data-id="<?= base64url_encode($this->encrypt->encode($item['BUILD_ID'])) ?>"><i class="fa fa-check"></i></button>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                        </tr>

                        <?php if(isset($mrq_detail[$item['BUILD_ID']])){
                            echo '<tr><td colspan="10" style="padding-left:2rem">'.($this->load->view('val_mr_po/mr_detail',['details' => $mrq_detail[$item['BUILD_ID']]],true)).'</td></tr>';
                        } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>