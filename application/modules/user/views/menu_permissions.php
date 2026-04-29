<div class="card border shadow-sm">
    <div class="card-body p-0 d-flex perm-split" style="min-height:500px">
        <div class="perm-panel-left border-end bg-white" style="border-radius:.375rem 0 0 .375rem">
            <div class="px-3 pt-3 pb-2 border-bottom sticky-top bg-white">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="fw-semibold text-muted text-uppercase" style="font-size:.65rem;letter-spacing:.06em">
                        <i class="fa fa-solid fa-sitemap me-1"></i>Accessible Menu</small>
                    <span class="badge bg-light text-secondary border d-none" id="role-badge" style="font-size:.65rem"></span>
                </div>
                <input type="text" class="form-control form-control-sm" id="search-menu" placeholder="Searching menu…">
            </div>
            <div id="menu-tree" class="py-2">
                <div div class="accordion perm-accordion" id="perm-acc">
                    <?php foreach ($menus as $m) {
                        $have_child     = (isset($m['child']) && count($m['child']));
                        $id_menu        = $m['ERP_MENU_ID'];
                        $name           = strtolower($m['ERP_MENU_NAME']);
                        ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="label_<?= $name ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $name ?>" aria-expanded="false" aria-controls="<?= $name ?>">
                                <i class="ri <?= $m['MENU_ICON'] ?> me-1"></i><?= $m['PROMPT'] ?> </button>
                            </h2>
                            <div id="<?= $name ?>" class="accordion-collapse collapse" aria-labelledby="label_<?= $name ?>">
                                <div class="accordion-body">
                                    <?php if($have_child){ ?>
                                        <?php foreach ($m['child'] as $m2) {
                                            $id_menu_child  = $m2['ERP_MENU_ID'];
                                            $name_child     = strtolower($m2['ERP_MENU_NAME']); ?>
                                            <div class="menu-leaf" data-id="<?= $name_child ?>" style="padding-left:2rem">
                                                <span class="flex-grow-1 text-truncate"><?= $m2['PROMPT'] ?></span>
                                                <span class="ov-dot"></span>
                                                <div class="d-none d-permissions"><?= base64_encode(json_encode([
                                                    'actions'      => ['view' => $m2['VIEW_FLAG'],'insert' => $m2['INSERT_FLAG'],'update' => $m2['UPDATE_FLAG'],'delete' => $m2['DELETE_FLAG']],
                                                    'permissions'   => json_decode($m2['PERMISSIONS']?:'[]',true)
                                                ])) ?></div>
                                                <input type="hidden" name="menu[<?= $id_menu_child ?>]" value="<?= isset($old_menu[$id_menu_child]) ? $old_menu[$id_menu_child] : base64_encode($m2['USER_PERMISSIONS']?:'[]') ?>"/>
                                            </div>
                                        <?php } ?>
                                    <?php } else{ ?>
                                        <div class="menu-leaf" data-id="<?= $name ?>" style="padding-left:2rem">
                                            <span class="flex-grow-1 text-truncate"><?= $m['PROMPT'] ?></span>
                                            <span class="ov-dot"></span>
                                            <div class="d-none d-permissions"><?= base64_encode(json_encode([
                                                'actions'      => ['view' => $m['VIEW_FLAG'],'insert' => $m['INSERT_FLAG'],'update' => $m['UPDATE_FLAG'],'delete' => $m['DELETE_FLAG']],
                                                'permissions'   => json_decode($m['PERMISSIONS']?:'[]',true)
                                            ])) ?></div>
                                            <input type="hidden" name="menu[<?= $id_menu ?>]" value="<?= isset($old_menu[$id_menu]) ? $old_menu[$id_menu] : base64_encode($m['USER_PERMISSIONS']?:'[]') ?>"/>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- RIGHT: Permission Builder -->
        <div class="perm-panel-right bg-light" style="border-radius:0 .375rem .375rem 0">
            <div id="builder" class="p-3 p-md-4">
                <!-- Default placeholder -->
                <div id="builder-placeholder" class="d-flex flex-column align-items-center justify-content-center text-muted" style="min-height:420px">
                <div class="rounded-3 bg-white border shadow-sm d-flex align-items-center justify-content-center mb-3" style="width:60px;height:60px;font-size:1.4rem;color:#ced4da">
                    <i class="ri  ri-filter-2-fill"></i>
                </div>
                <p class="mb-0" style="font-size:.83rem">Pilih menu dari tree di kiri</p>
                </div>
            </div>
        </div>
    </div>
</div>