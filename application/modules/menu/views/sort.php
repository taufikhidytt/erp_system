<style>
/* ── Sortable group ───────────────────────────────────── */
    .sort-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    /* ── Parent row ───────────────────────────────────────── */
    .parent-item {
        background: #fff;
        border: 1.5px solid #dbeafe;
        border-left: 4px solid #3d7bb9;
        border-radius: 6px;
        transition: box-shadow .15s, border-color .15s;
    }
    .parent-item:hover { box-shadow: 0 3px 10px rgba(61,123,185,.15); }
    .parent-item.sortable-chosen { box-shadow: 0 6px 20px rgba(61,123,185,.25); border-color: #3d7bb9; opacity: .9; }
    .parent-item.sortable-ghost  { opacity: .35; }

    .parent-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        cursor: default;
        user-select: none;
    }
    .parent-drag-handle {
        cursor: grab;
        color: #94a3b8;
        font-size: 18px;
        flex-shrink: 0;
        transition: color .15s;
    }
    .parent-drag-handle:active { cursor: grabbing; }
    .parent-item:hover .parent-drag-handle { color: #3d7bb9; }

    .toggle-btn {
        border: none; background: none;
        color: #3d7bb9; font-size: 16px;
        padding: 0 2px;
        line-height: 1;
        transition: transform .2s;
        flex-shrink: 0;
    }
    .toggle-btn.collapsed { transform: rotate(-90deg); }

    .seq-badge {
        background: #eff6ff;
        color: #3d7bb9;
        border-radius: 4px;
        padding: 1px 7px;
        font-size: 11px;
        font-weight: 700;
        min-width: 32px;
        text-align: center;
        flex-shrink: 0;
    }
    .parent-name {
        font-weight: 600;
        font-size: 13px;
        color: #1e3a5f;
        flex: 1;
    }
    .parent-icon-badge {
        font-size: 11px;
        color: #64748b;
        background: #f1f5f9;
        border-radius: 4px;
        padding: 2px 7px;
        display: flex; align-items: center; gap: 4px;
    }

    /* ── Children container ───────────────────────────────── */
    .children-container {
        padding: 0 14px 10px 42px;
    }
    .children-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-height: 6px; /* allow drop on empty */
    }

    /* ── Child row ────────────────────────────────────────── */
    .child-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2f0dc;
        border-left: 3px solid #22c55e;
        border-radius: 5px;
        padding: 7px 12px;
        cursor: default;
        user-select: none;
        transition: box-shadow .15s, background .15s;
    }
    .child-item:hover { background: #f0fdf4; box-shadow: 0 2px 6px rgba(34,197,94,.12); }
    .child-item.sortable-chosen { box-shadow: 0 4px 12px rgba(34,197,94,.22); opacity: .9; }
    .child-item.sortable-ghost  { opacity: .35; }

    .child-drag-handle {
        cursor: grab;
        color: #94a3b8;
        font-size: 16px;
        flex-shrink: 0;
        transition: color .15s;
    }
    .child-drag-handle:active { cursor: grabbing; }
    .child-item:hover .child-drag-handle { color: #22c55e; }

    .child-seq-badge {
        background: #dcfce7;
        color: #166534;
        border-radius: 4px;
        padding: 1px 6px;
        font-size: 10px;
        font-weight: 700;
        min-width: 28px;
        text-align: center;
        flex-shrink: 0;
    }
    .child-name {
        font-size: 12px;
        color: #374151;
        flex: 1;
    }
    .child-prompt {
        font-size: 10px;
        color: #94a3b8;
        font-style: italic;
        max-width: 160px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ── Empty drop zone ──────────────────────────────────── */
    .empty-drop {
        border: 2px dashed #d1fae5;
        border-radius: 5px;
        padding: 10px;
        text-align: center;
        color: #a7f3d0;
        font-size: 11px;
    }

    /* ── Drag-over highlight ──────────────────────────────── */
    .children-list.drag-over { background: #f0fdf4; border-radius: 5px; }

    /* ── Save bar ─────────────────────────────────────────── */
    .save-bar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        padding: 12px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 1001;
        box-shadow: 0 -2px 8px rgba(0,0,0,.07);
    }
    .save-hint { font-size: 11px; color: #94a3b8; }
    .save-hint i { color: #f59e0b; }

    /* ── Seq number live update ───────────────────────────── */
    .seq-live { transition: background .3s; }
    .seq-live.updated {
        background: #fef3c7 !important;
        color: #92400e !important;
    }

    /* ── Result preview panel ─────────────────────────────── */
    #result-panel pre {
        font-size: 11px;
        max-height: 260px;
        overflow-y: auto;
        background: #1e293b;
        color: #a5f3fc;
        border-radius: 6px;
        padding: 12px;
    }

    /* bottom padding so save-bar doesn't cover content */
    .page-wrapper { padding-bottom: 72px; }
    .main-content {
    overflow: visible !important;
}
</style>
<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('menu') ?>" class="text-decoration-underline"><?= $access['PROMPT'] ?></a>
                            </li>
                            <li class="breadcrumb-item active text-decoration-underline"><?= $breadcrumb ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <form action="" method="post">
            <input type="hidden" name="menus" id="menus">
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-info d-flex align-items-center justify-content-between text-white">
                            <span><i class="ri-list-ordered me-2"></i>Susun Urutan Menu</span>
                        </div>
                        <div class="card-body p-3">
                            <!-- Search filter -->
                            <div class="input-group input-group-sm mb-3">
                                <span class="input-group-text bg-white"><i class="ri-search-line text-muted"></i></span>
                                <input type="text" id="filterInput" class="form-control" placeholder="Filter nama menu…">
                                <button type="button" class="btn btn-outline-secondary" id="clearFilter" title="Hapus filter">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>

                            <div class="sort-group" id="parent-sortable">
                                <?php foreach ($menus as $k => $m) {
                                    
                                    ?>
                                    <div class="parent-item" data-id="<?= $m['ERP_MENU_ID'] ?>" data-seq="<?= $m['SEQ'] ?>">
                                        <div class="parent-header">
                                            <span class="parent-drag-handle" title="Seret Parent"><i class="ri-drag-move-2-line"></i></span>
                                            <span class="seq-badge seq-live">1</span>
                                            <button type="button" class="toggle-btn" title="Tampilkan/sembunyikan child menu" onclick="toggleChildren(this)">
                                                <i class="ri-arrow-down-s-line"></i>
                                            </button>
                                            <span class="parent-name"><?= $m['PROMPT'] ?></span>
                                            <span class="parent-icon-badge"><i class="<?= $m['MENU_ICON'] ?>"></i> <?= $m['MENU_ICON'] ?></span>
                                            <span class="badge bg-primary-subtle text-primary ms-1"><?= count($m['child']) ?></span>
                                        </div>

                                        <?php if(!empty($m['child'])){ ?>
                                        <div class="children-container collapse show">
                                            <div class="children-list" data-parent="<?= $m['ERP_MENU_ID'] ?>">
                                                <?php foreach ($m['child'] as $k2 => $m2) { ?>
                                                    <div class="child-item" data-id="<?= $m2['ERP_MENU_ID'] ?>" data-seq="<?= $m2['SEQ'] ?>">
                                                        <span class="child-drag-handle" title="Seret"><i class="ri-drag-move-2-line"></i></span>
                                                        <span class="child-seq-badge seq-live"><?= $k2+1 ?></span>
                                                        <span class="child-name">
                                                            <?= $m2['PROMPT'] ?>
                                                            <?= $m2['ACTIVE_FLAG']=='Y'?'<span class="ms-1 badge bg-success"><i class="ri ri-check-fill me-1"></i>Aktif</span>':'<span class="ms-1 badge bg-danger"><i class="ri ri-close-fill me-1"></i>Tidak Aktif</span>' ?>
                                                        </span>
                                                        <span class="child-prompt"><?= $m2['ERP_MENU_NAME'] ?></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4" style="align-self: flex-start; position: sticky; top: 80px;">
                    <div id="sticky-info" class="card mb-3">
                        <div class="card-body p-3">
                            <div class="text-end">
                                <a href="<?= site_url('menu/add') ?>" 
                                    type="button" class="btn btn-sm btn-primary" title="Add" data-toggle="tooltip" data-bs-placement="left">
                                    <i class="ri-add-circle-fill"></i>
                                </a>
                                <button type="submit" class="btn btn-sm btn-success" title="Save" data-toggle="tooltip" data-bs-placement="left">
                                    <i class="ri-save-3-fill"></i>
                                </button>
                                <button type="button" onclick="location.reload()" class="btn btn-sm btn-warning" title="Reload" data-toggle="tooltip" data-bs-placement="left">
                                    <i class="ri-reply-fill"></i>
                                </button>
                            </div>
                            <h6 class="fw-bold text-primary mb-2"><i class="ri-information-line me-1"></i>Cara Penggunaan</h6>
                            <ul class="mb-0 ps-3" style="font-size:12px; line-height:2;">
                                <li>Tahan ikon <i class="ri ri-drag-move-2-line text-muted"></i> lalu seret untuk mengubah urutan</li>
                                <li><span class="badge bg-primary">Parent</span> dapat diurutkan satu sama lain</li>
                                <li><span class="badge bg-info">Child</span> hanya dapat diurutkan dalam grup parent-nya sendiri</li>
                                <li>Klik <i class="ri-arrow-down-s-line text-primary"></i> untuk collapse / expand child menu</li>
                                <li>Nomor urut otomatis diperbarui saat diseret</li>
                                <li>Klik <strong>Simpan Urutan</strong> untuk menyimpan perubahan</li>
                            </ul>
                            <hr class="my-2">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary w-auto" id="btnExpandAll">
                                    <i class="ri-arrow-down-s-line me-1"></i>Expand Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary w-auto" id="btnCollapseAll">
                                    <i class="ri-arrow-up-s-line me-1"></i>Collapse Semua
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('assets/admin/libs/sortablejs/sortable.min.js') ?>"></script>
<script>
    $(function() {
        $('html').css('scroll-behavior', 'auto');
        setTimeout(function() {
        $(window).scrollTop(0);
            $('html').css('scroll-behavior', '');
        }, 300);
    });

    /* -- renumber badges after a sort event -- */
    function renumberParents(status) {
        $('#parent-sortable > .parent-item').each(function(i) {
            var $badge = $(this).find('.parent-header .seq-badge').first();
            var newSeq = (i + 1)*100;
            $(this).attr('data-seq', newSeq);
            $badge.text(newSeq);
            flashBadge($badge[0]);
            if(status){
                renumberChildren($(this).find('.children-list'));
            }
        });
        updateTotal();
        buildPreview();
    }

    function renumberChildren(list) {
        $(list).children('.child-item').each(function(i) {
            var $parent_seq = parseInt($(this).closest('.parent-item').attr('data-seq')) || 0;
            var $badge = $(this).find('.child-seq-badge').first();
            var newSeq = $parent_seq + (i + 1);
            $(this).attr('data-seq', newSeq);
            $badge.text(newSeq);
            flashBadge($badge[0]);
        });
        buildPreview();
    }

    function flashBadge(badge) {
        $(badge).addClass('updated');
        setTimeout(function() { $(badge).removeClass('updated'); }, 600);
    }

    function updateTotal() {
        var parents  = $('#parent-sortable > .parent-item').length;
        var children = $('.children-list > .child-item').length;
    }

    /* -- toggle children -- */
    function toggleChildren(btn) {
        var $container = $(btn).closest('.parent-item').find('.children-container').first();
        if ($container.hasClass('show')) {
            $container.removeClass('show');
            $(btn).addClass('collapsed');
        } else {
            $container.addClass('show');
            $(btn).removeClass('collapsed');
        }
    }

    /* -- build JSON preview -- */
    function buildPreview() {
        var result = [];
        $('#parent-sortable > .parent-item').each(function() {
            var $p = $(this);
            var parentObj = {
                id       : $p.attr('data-id'),
                seq      : parseInt($p.attr('data-seq')),
                children : []
            };
            $p.find('.children-list > .child-item').each(function() {
                var $c = $(this);
                parentObj.children.push({
                    id        : $c.attr('data-id'),
                    seq       : parseInt($c.attr('data-seq')),
                    parent_id : $p.attr('data-id')
                });
            });
            result.push(parentObj);
        });
        $('#menus').val(btoa(unescape(encodeURIComponent(JSON.stringify(result, null, 2)))));
    }

    /* ── PARENT sortable ── */
    Sortable.create($('#parent-sortable')[0], {
        handle: '.parent-drag-handle',
        animation: 160,
        easing: 'cubic-bezier(.25,.8,.25,1)',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: renumberParents
    });

    /* ── CHILD sortables (locked to own parent, no cross-group) ── */
    $('.children-list').each(function() {
        var parentId = $(this).attr('data-parent');
        Sortable.create(this, {
            group: { name: 'children-' + parentId, pull: false, put: false },
            handle: '.child-drag-handle',
            animation: 140,
            easing: 'cubic-bezier(.25,.8,.25,1)',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function(evt) {
                renumberChildren(evt.from);
            }
        });
    });

    /* ── Filter ── */
    $('#filterInput').on('input', function() {
        var q = $(this).val().toLowerCase().trim();
        $('.parent-item').each(function() {
            var $p = $(this);
            var anyVisible = false;
            $p.find('.child-item').each(function() {
                var name   = $(this).find('.child-name').text().toLowerCase();
                var prompt = $(this).find('.child-prompt').text().toLowerCase();
                var show   = !q || name.includes(q) || prompt.includes(q);
                $(this).toggle(show);
                if (show) anyVisible = true;
            });
            var parentMatch = !q || $p.find('.parent-name').text().toLowerCase().includes(q);
            $p.toggle(parentMatch || anyVisible);
            if (parentMatch || anyVisible) {
                $p.find('.children-container').first().addClass('show');
                $p.find('.toggle-btn').first().removeClass('collapsed');
            }
        });
    });

    $('#clearFilter').on('click', function() {
        $('#filterInput').val('');
        $('.parent-item, .child-item').show();
    });
    $('#btnExpandAll').on('click', function() {
        $('.children-container').addClass('show');
        $('.toggle-btn').removeClass('collapsed');
    });

    $('#btnCollapseAll').on('click', function() {
        $('.children-container').removeClass('show');
        $('.toggle-btn').addClass('collapsed');
    });

    /* ── Init ── */
    renumberParents(true);

</script>