<?php foreach ($account as $k => $v) {
    $n++;
    $has_child = count($v['children']) > 0;
    $active_flag = isset($v['ACTIVE_FLAG']) ? $v['ACTIVE_FLAG'] : 'Y';
    ?>
    <li>
        <div class="tree-node<?= (($n==1 && !$active) || $active == $v['ACCOUNT_ID'])?" active-node":'' ?><?= $active_flag=='N'?" node-inactive":'' ?>" data-id="<?= base64url_encode($this->encrypt->encode($v['ACCOUNT_ID'])) ?>" data-code="<?= $v['ACCOUNT_CODE'] ?>" data-name="<?= $v['ACCOUNT_NAME'] ?>" data-type="<?= $v['ACCOUNT_TYPE'] ?>" data-type_id="<?= $v['ACCOUNT_TYPE_ID'] ?>" data-active="<?= $active_flag ?>" data-level="<?= $level ?>">
            <?= $has_child?'<span class="node-toggle"><i class="ri-add-line"></i></span>':'' ?>
            <i class="<?= $has_child?"ri-folder-3-fill node-icon-folder":"ri-file-text-line node-icon-file"; ?> me-2"></i>
            <span class="node-code"><?= $v['ACCOUNT_CODE'] ?></span>
            <span class="node-label"><?= $v['ACCOUNT_NAME'] ?></span>
        </div> 
<?php 
    if($has_child){
        echo '<ul>';
            $this->load->view('coa_tree', ['account' => $v['children'], 'n' => $n, 'level' => $level+1, 'active' => $active]);
        echo '</ul>';
    }

    echo '</li>';
} ?>