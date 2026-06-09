<style>
    /* ═══ WORKSPACE ═══ */
    .coa-workspace { background:#f4f6f9; border-radius:10px; overflow:hidden; border:1px solid #e2e6ed; }
    body[data-theme="dark"] .coa-workspace { background:#222736; border-color:#2d3448; }

    /* ═══ TREE PANEL ═══ */
    .coa-tree-panel { background:#fff; border-right:1px solid #e9ecef; display:flex; flex-direction:column; max-height:400px; overflow:hidden; }
    body[data-theme="dark"] .coa-tree-panel { background:#2a3042; border-right-color:#32394e; }

    @media (min-width:768px) {
        .coa-workspace { height:calc(100vh - 260px); min-height:600px; }
        .coa-tree-panel { max-height:100% !important; height:100%; border-bottom:none; }
        .coa-detail-panel { height:100%; }
    }
    @media (max-width:767.98px) {
        .coa-tree-panel { border-right:none; border-bottom:1px solid #e9ecef; }
        body[data-theme="dark"] .coa-tree-panel { border-bottom-color:#32394e; }
    }

    /* ═══ TREE HEADER ═══ */
    .coa-tree-header { background:var(--app-primary,#556ee6); color:#fff; padding:12px 16px; font-size:.8rem; font-weight:600; display:flex; align-items:center; justify-content:space-between; flex-shrink:0; }
    .coa-tree-title { display:flex; align-items:center; gap:8px; }
    .btn-collapse-tree { background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.25); color:#fff; font-size:.7rem; padding:2px 10px; border-radius:4px; cursor:pointer; font-family:inherit; }
    .btn-collapse-tree:hover { background:rgba(255,255,255,.28); }

    /* ═══ TREE SEARCH ═══ */
    .coa-tree-search { padding:8px 12px; border-bottom:1px solid #eef0f4; flex-shrink:0; }
    body[data-theme="dark"] .coa-tree-search { border-bottom-color:#32394e; }
    .coa-tree-search-wrap { position:relative; }
    .coa-tree-search-wrap i { position:absolute; left:10px; top:50%; transform:translateY(-50%); font-size:.85rem; color:#94a3b8; pointer-events:none; }
    .coa-tree-search input { width:100%; border:1px solid #e2e6ed; border-radius:6px; padding:6px 10px 6px 32px; font-size:.78rem !important; background:#f8f9fb; color:#4a5568; outline:none; font-family:inherit !important; }
    body[data-theme="dark"] .coa-tree-search input { background:#222736; border-color:#32394e; color:#a6b0cf; }
    .coa-tree-search input:focus { border-color:var(--app-primary,#556ee6); background:#fff; }
    body[data-theme="dark"] .coa-tree-search input:focus { background:#2a3042; }

    /* ═══ TREE BODY & NODES ═══ */
    .coa-tree-body { flex:1; overflow-y:auto; padding:10px 12px; }
    .coa-tree-body::-webkit-scrollbar { width:5px; }
    .coa-tree-body::-webkit-scrollbar-thumb { background:#ccc; border-radius:10px; }
    body[data-theme="dark"] .coa-tree-body::-webkit-scrollbar-thumb { background:#32394e; }

    .account-tree, .account-tree ul { list-style:none; margin:0; padding:0; }
    .account-tree ul { padding-left:18px; position:relative; }
    .account-tree ul::before { content:""; position:absolute; top:0; left:7px; bottom:0; border-left:1px dashed #d0d5dd; }
    body[data-theme="dark"] .account-tree ul::before { border-left-color:#3a4055; }

    .tree-node { padding:7px 10px; cursor:pointer; display:flex; align-items:center; gap:4px; font-size:.82rem; color:#4a5568; border-radius:6px; margin:1px 0; transition:background .15s; position:relative; }
    body[data-theme="dark"] .tree-node { color:#a6b0cf; }
    .tree-node:hover { background:rgba(var(--app-primary-rgb,85,110,230),.06); }
    .tree-node.active-node { background:rgba(var(--app-primary-rgb,85,110,230),.12) !important; color:var(--app-primary,#556ee6); font-weight:600; }
    .tree-node.active-node::before { content:""; position:absolute; left:0; top:4px; bottom:4px; width:3px; border-radius:0 3px 3px 0; background:var(--app-primary,#556ee6); }
    .tree-node.node-inactive { opacity:.5; }

    .node-toggle { width:20px; height:20px; display:inline-flex; align-items:center; justify-content:center; color:#94a3b8; flex-shrink:0; }
    .node-icon-folder { color:#f59e0b; font-size:.95rem; }
    .node-icon-file { color:var(--app-primary,#556ee6); font-size:.9rem; opacity:.8; }
    .node-code { font-family:monospace; font-size:.78rem; color:#64748b; min-width:52px; font-weight:500; }
    body[data-theme="dark"] .node-code { color:#8590a5; }
    .tree-node.active-node .node-code { color:var(--app-primary,#556ee6); }
    .node-label { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

    .node-active-dot { width:6px; height:6px; border-radius:50%; background:#22c55e; margin-left:auto; flex-shrink:0; }
    .tree-node.node-inactive .node-active-dot { background:#ff3d60; }

    /* ═══ TREE FOOTER ═══ */
    .coa-tree-footer { padding:8px 12px; border-top:1px solid #eef0f4; background:#fafbfc; flex-shrink:0; display:flex; align-items:center; gap:12px; }
    body[data-theme="dark"] .coa-tree-footer { background:#222736; border-top-color:#32394e; }
    .coa-stat { display:flex; align-items:center; gap:5px; font-size:.7rem; color:#94a3b8; }
    .coa-stat strong { color:#4a5568; font-weight:600; }
    body[data-theme="dark"] .coa-stat strong { color:#a6b0cf; }

    /* ═══ DETAIL PANEL ═══ */
    .coa-detail-panel { background:#f8f9fb; display:flex; flex-direction:column; overflow-y:auto; }
    body[data-theme="dark"] .coa-detail-panel { background:#262b3c; }
    .coa-detail-panel::-webkit-scrollbar { width:5px; }
    .coa-detail-panel::-webkit-scrollbar-thumb { background:#ccc; border-radius:10px; }

    /* ═══ INFO CARD ═══ */
    .coa-info-card { background:#fff; border-bottom:1px solid #eef0f4; padding:16px 20px; flex-shrink:0; }
    body[data-theme="dark"] .coa-info-card { background:#2a3042; border-bottom-color:#32394e; }
    .coa-info-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .coa-info-title { font-size:.8rem; font-weight:600; color:#374151; display:flex; align-items:center; gap:8px; margin:0; }
    body[data-theme="dark"] .coa-info-title { color:#e2e8f0; }
    .coa-info-title i { color:var(--app-primary,#556ee6); }

    .coa-info-badges { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
    .coa-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; font-size:.68rem; font-weight:600; border-radius:20px; font-family:inherit; }
    .coa-badge-level { background:rgba(var(--app-primary-rgb,85,110,230),.1); color:var(--app-primary,#556ee6); }
    .coa-badge-active { background:rgba(34,197,94,.1); color:#16a34a; }
    .coa-badge-inactive { background:rgba(148,163,184,.12); color:#64748b; }
    body[data-theme="dark"] .coa-badge-active { background:rgba(52,195,143,.15); color:#34c38f; }
    body[data-theme="dark"] .coa-badge-inactive { background:rgba(255,61,96,.15); color:#ff3d60; }

    .coa-info-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; }
    @media (max-width:767.98px) { .coa-info-grid { grid-template-columns:1fr 1fr; } }
    .coa-info-item { display:flex; flex-direction:column; gap:3px; }
    .info-label { font-size:.68rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:.4px; }
    .info-value { font-size:.82rem; font-weight:600; color:#1e293b; font-family:monospace; padding:6px 10px; background:#f4f6f9; border-radius:6px; border:1px solid #eef0f4; }
    body[data-theme="dark"] .info-value { color:#e2e8f0; background:#222736; border-color:#32394e; }

    /* ═══ SUB-ACCOUNT AREA ═══ */
    .coa-sub-area { flex:1; padding:16px 20px; display:flex; flex-direction:column; }
    @media (max-width:767.98px) { .coa-sub-area { padding:12px; } }
    .coa-sub-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; flex-wrap:wrap; gap:8px; }
    .coa-sub-title { font-size:.78rem; font-weight:600; color:#374151; display:flex; align-items:center; gap:6px; margin:0; }
    body[data-theme="dark"] .coa-sub-title { color:#e2e8f0; }
    .coa-sub-title i { color:var(--app-primary,#556ee6); }
    .coa-sub-count { font-size:.68rem; font-weight:700; padding:2px 8px; border-radius:10px; background:rgba(var(--app-primary-rgb,85,110,230),.1); color:var(--app-primary,#556ee6); }

    .coa-sub-toolbar { display:flex; align-items:center; gap:6px; }
    .btn-sub { display:inline-flex; align-items:center; gap:5px; padding:5px 14px; font-size:.75rem; font-weight:500; border-radius:6px; border:1px solid #e2e6ed; background:#fff; color:#4a5568; cursor:pointer; transition:all .18s; font-family:inherit; }
    body[data-theme="dark"] .btn-sub { background:#222736; border-color:#32394e; color:#a6b0cf; }
    .btn-sub:hover { border-color:var(--app-primary,#556ee6); color:var(--app-primary,#556ee6); }
    .btn-sub-add { background:var(--app-primary,#556ee6) !important; border-color:var(--app-primary,#556ee6) !important; color:#fff !important; }
    .btn-sub-add:hover { filter:brightness(.9); }

    .coa-empty-state { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:40px 20px; color:#94a3b8; text-align:center; }
    .coa-empty-state i { font-size:2.2rem; margin-bottom:10px; opacity:.4; }
    .coa-empty-state p { font-size:.8rem; margin:0; }
</style>

<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="" class="text-decoration-underline"><?= $breadcrumb ?></a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <div class="card-body p-0">
                        <div class="row g-0 coa-workspace">

                            <!-- ══════ LEFT: TREE PANEL ══════ -->
                            <div class="col-12 col-md-4 coa-tree-panel">
                                <div class="coa-tree-header">
                                    <div class="coa-tree-title">
                                        <i class="ri-node-tree"></i>
                                        <span>Account Tree</span>
                                    </div>
                                    <div>
                                        <button class="btn-collapse-tree" id="btn-collapse-all">
                                            <i class="ri-contract-up-down-line"></i> Collapse
                                        </button>
                                        <button class="btn-collapse-tree" id="btn-expand-all">
                                            <i class="ri-contract-down-up-line"></i> Expand
                                        </button>
                                    </div>
                                </div>

                                <div class="coa-tree-search">
                                    <div class="coa-tree-search-wrap">
                                        <i class="ri-search-line"></i>
                                        <input type="text" id="tree-search" placeholder="Cari kode atau nama akun...">
                                    </div>
                                </div>

                                <div class="coa-tree-body">
                                    <ul class="account-tree">
                                        <?php $this->load->view('coa_tree', ['account' => $account, 'n' => 0, 'level' => 1, 'active' => $this->session->flashdata($access['url'].'_saved')]); ?>
                                    </ul>
                                </div>

                                <div class="coa-tree-footer">
                                    <div class="coa-stat"><i class="ri-folder-3-line"></i> Group: <strong id="stat-group">0</strong></div>
                                    <div class="coa-stat"><i class="ri-file-text-line"></i> Detail: <strong id="stat-detail">0</strong></div>
                                    <div class="coa-stat"><i class="ri-checkbox-circle-line" style="color:#22c55e;"></i> Aktif: <strong id="stat-active">0</strong></div>
                                </div>
                            </div>

                            <!-- ══════ RIGHT: DETAIL PANEL ══════ -->
                            <div class="col-12 col-md-8 coa-detail-panel">

                                <!-- INFO CARD -->
                                <div class="coa-info-card">
                                    <div class="coa-info-header">
                                        <h6 class="coa-info-title">
                                            <i class="ri-information-line"></i>
                                            Informasi Account
                                        </h6>
                                        <div class="coa-info-badges">
                                            <span class="coa-badge coa-badge-level" id="info-badge-level">
                                                <i class="ri-stack-line"></i> Level —
                                            </span>
                                            <button class="btn btn-sm btn-primary d-md-none" id="btn-jump-tree" style="font-size:0.72rem;">
                                                <i class="ri-arrow-up-circle-line"></i> Tree
                                            </button>
                                        </div>
                                    </div>

                                    <div class="coa-info-grid">
                                        <div class="coa-info-item">
                                            <span class="info-label">Kode Account</span>
                                            <span class="info-value" id="info-kode">—</span>
                                        </div>
                                        <div class="coa-info-item">
                                            <span class="info-label">Nama Account</span>
                                            <span class="info-value" id="info-nama">—</span>
                                        </div>
                                        <div class="coa-info-item">
                                            <span class="info-label">Tipe Account</span>
                                            <span class="info-value" id="info-tipe">—</span>
                                        </div>
                                        <div class="coa-info-item">
                                            <span class="info-label">Aktif</span>
                                            <div class="mt-2">
                                                <span class="info-value" id="info-status">—</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="coa-sub-area">
                                    <div class="coa-sub-header">
                                        <h6 class="coa-sub-title">
                                            <i class="ri-list-unordered"></i>
                                            Sub-Account
                                            <span class="coa-sub-count d-none" id="sub-count">0</span>
                                        </h6>
                                        <span class="coa-badge coa-badge-level" id="info-badge-sub-level">
                                            <i class="ri-stack-line"></i> Level —
                                        </span>
                                    </div>

                                    <div id="sub-table-wrapper">
                                        <div class="coa-empty-state" id="sub-empty-state">
                                            <i class="ri-folder-open-line"></i>
                                            <p>Pilih akun di pohon untuk melihat sub-account</p>
                                        </div>
                                        <table id="table" class="table table-sm w-100" style="display:none;">
                                            <thead style="background: var(--app-primary-th); color: var(--app-primary-contrast)">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode</th>
                                                    <th>Nama Account</th>
                                                    <th>Tipe</th>
                                                    <th>Aktif</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="<?= base_url() ?>assets/admin/libs/inline-editor/inline-editor.css?v=<?= $version['inline-editor'] ?>" rel="stylesheet" type="text/css" />
<script src="<?= base_url() ?>assets/admin/libs/inline-editor/inline-editor.js?v=<?= $version['inline-editor'] ?>"></script>
<script>
$(function () {

    var state = { id: null, dt: null, editor: null };
    var PRIMARY = getComputedStyle(document.documentElement).getPropertyValue('--app-primary').trim() || '#556ee6';

    /* ── AMBIL CHILDREN DARI DOM TREE ── */
    function getChildren(id) {
        var children = [];
        $('.tree-node[data-id="' + id + '"]').closest('li').find('> ul > li > .tree-node').each(function () {
            var $n = $(this);
            children.push({
                id    : $n.data('id'),
                code  : $n.data('code'),
                name  : $n.data('name'),
                type  : {id : $n.data('type_id'), label : $n.data('type')},
                active_flag: $n.data('active')
            });
        });
        return children;
    }

    /* ── DATATABLES (dari data lokal, bukan AJAX) ── */
    function initDT() {
        if (state.dt) return;
        state.dt = $('#table').DataTable({
            dom: '<"d-flex justify-content-between mb-2 align-items-center"lB>rtip',
            buttons: getButtons(<?= json_encode(button_actions([
                [
                    'key'      => 'insert',
                    'class'    => 'btn-insert btn-primary',
                    'url'      => '',
                    'raw_url'  => true
                ],
            ], 'dt')) ?>),
            data    : [],
            columns : [
                { data: null, width: '20px', className: 'text-center',
                  render: function (d, t, r, meta) { return meta.row + 1; } },
                { data: 'code', width: '130px' },
                { data: 'name' },
                { data: 'type', width: '180px' },
                { data: 'active_flag', width: '90px', className: 'text-center'
                }
            ],
            drawCallback: function () { $('#sub-count').text(this.api().page.info().recordsTotal); }
        });

        state.editor = InlineEditor.init({
            table : state.dt,
            add   : <?= $access['insert']?'true':'false' ?>,
            edit  : <?= $access['update']?'true':'false' ?>,
            urls  : {
                save : config_app.url+'gl_account/save'
            },
            fields: [
                { field: 'code',         type: 'number', maxlength: 18, required: true, label: 'Kode Account' },
                { field: 'name',         type: 'text', maxlength: 18, required: true, label: 'Nama Account'},
                { field: 'type',       type: 'select2',required: true, label: 'Tipe Account', 
                    select2: {
                        url         : '/gl_account/get_type',
                        placeholder : 'Pilih Tipe',
                    },
                    attrs: {
                        'data-dropdown-parent' : 'body',
                    }
                },
                { field: 'active_flag',  type: 'checkbox', value: 'Y'},
            ],
            beforeAdd: beforeAdd,
            afterSave: afterSave,
        });
    }

    function beforeAdd(e){
        const listData = getChildren(state.id);
        let maxCode = listData.reduce((max, item) => item.code > max ? item.code : max, 0);
        const $node = $(document).find(`[data-id="${state.id}"]`);
        if($('.ie-new-row [data-field="code"]').length>0){
            maxCode = parseInt($('.ie-new-row:first [data-field="code"]').text()) || 0;
        }
        if(maxCode === 0){
            maxCode = parseInt($node.data('code')) || 0;
        }
        state.editor.setDefaultValue('code', maxCode+1);
        return true;
    }

    function afterSave(res){
        window.location.reload();
    }

    /* ── LOAD TABLE DARI DOM ── */
    function loadSub(id) {
        const $node = $(document).find(`[data-id="${id}"]`);
        $('#sub-empty-state').hide();
        $('#table').show();
        state.id = id;
        initDT();
        state.dt.clear().rows.add(getChildren(id)).draw();
        state.editor.setExtraData({parent_id : state.id});
        state.editor.setDefaultValue('code', $node.data('code'));
        state.editor.setDefaultValue('type', { id: $node.data('type_id'), label: $node.data('type') });

        $('#sub-count').text(state.dt.page.info().recordsTotal);
    }

    /* ── INFO CARD ── */
    function updateInfo($node) {
        var code   = $node.data('code').toString();
        var active = $node.data('active') === 'Y';
        var level  = parseInt($node.data('level')) || 1;
        var digit  = ((code.length==4?(code.length+1):code.length)+1);

        if(level>=5){
            $('.coa-sub-area').fadeOut();
        }else{
            $('.coa-sub-area').fadeIn();
        }

        $('#info-kode').text(code);
        $('#info-nama').text($node.data('name'));
        $('#info-tipe').text($node.data('type'));
        $('#info-status').html(active ? '<i class="text-success fa fa-check" title="Yes" data-bs-toggle="tooltip" data-bs-placement="left"></i>' : '<i class="text-danger fa fa-times" title="No" data-bs-toggle="tooltip" data-bs-placement="left"></i>');
        $('#info-badge-level').html('<i class="ri-stack-line"></i> Level ' + level + ' (Digit ' + code.length + ')');
        $('#info-badge-sub-level').html('<i class="ri-stack-line"></i> Level ' + (level+1) + ' (Digit ' + digit + ')');
    }

    /* ── TREE INIT ── */
    $('.account-tree ul').hide();
    $('.node-toggle i').removeClass('ri-subtract-line').addClass('ri-add-line');
    $('.node-icon-folder').removeClass('ri-folder-3-fill').addClass('ri-folder-3-line');

    $('.tree-node').each(function () {
        $(this).append('<span class="node-active-dot"></span>');
        if ($(this).data('active') !== 'Y') $(this).addClass('node-inactive');
    });

    updateStats();

    var $active = $('.tree-node.active-node');
    if ($active.length) {
        $('.tree-node').removeClass('active-node');
        $active.addClass('active-node');

        $active.parents('ul').slideDown(150, function() {
            // Logika mencocokkan icon folder terbuka (ri-folder-3-fill & ri-subtract-line)
            var $li = $(this).closest('li');
            $li.find('> .node-toggle i').removeClass('ri-add-line').addClass('ri-subtract-line');
            $li.find('> .tree-node .node-icon-folder').removeClass('ri-folder-3-line').addClass('ri-folder-3-fill');
        });
        
        updateInfo($active);
        loadSub($active.data('id'));

        setTimeout(function() {
            $('.coa-tree-body').animate({
                scrollTop: $('.coa-tree-body').scrollTop() + ($active.offset().top - $('.coa-tree-body').offset().top) - 50
            }, 500);
        }, 300);
    }

    /* ── TREE TOGGLE ── */
    $(document).on('click', '.node-toggle', function (e) {
        e.stopPropagation();
        var $li = $(this).closest('li'), $ul = $li.find('> ul');
        if (!$ul.length) return;
        var open = $ul.is(':visible');
        $ul[open ? 'slideUp' : 'slideDown'](150);
        $(this).find('i').toggleClass('ri-add-line', open).toggleClass('ri-subtract-line', !open);
        $li.find('> .tree-node .node-icon-folder').toggleClass('ri-folder-3-line', open).toggleClass('ri-folder-3-fill', !open);
    });

    /* ── TREE NODE CLICK ── */
    $(document).on('click', '.tree-node', function (e) {
        if ($(e.target).closest('.node-toggle').length) return;
        var $node = $(this), id = $node.data('id');
        if (id === state.id) return;
        $('.tree-node').removeClass('active-node');
        $node.addClass('active-node');
        updateInfo($node);
        loadSub(id);
        if ($(window).width() < 768)
            $('html,body').animate({ scrollTop: $('.coa-detail-panel').offset().top - 20 }, 400);
    });

    $('#btn-collapse-all').on('click', function () {
        $('.account-tree ul').slideUp(150);
        $('.node-toggle i').removeClass('ri-subtract-line').addClass('ri-add-line');
        $('.node-icon-folder').removeClass('ri-folder-3-fill').addClass('ri-folder-3-line');
    });
    $('#btn-expand-all').on('click', function () {
        $('.account-tree ul').slideDown(150);
        $('.node-toggle i').removeClass('ri-add-line').addClass('ri-subtract-line');
        $('.node-icon-folder').removeClass('ri-folder-3-line').addClass('ri-folder-3-fill');
    });

    /* ── SEARCH ── */
    $('#tree-search').on('input', function () {
        var kw = $(this).val().toLowerCase().trim();
        if (!kw) return $('.account-tree li').show();
        $('.account-tree li').hide();
        $('.tree-node').each(function () {
            if (($(this).data('code') + ' ' + $(this).data('name')).toLowerCase().includes(kw))
                $(this).closest('li').show().parents('li').show().parents('ul').show();
        });
    });

    /* ── STATS ── */
    function updateStats() {
        $('#stat-group').text($('.node-icon-folder').length);
        $('#stat-detail').text($('.node-icon-file').length);
        $('#stat-active').text($('.tree-node:not(.node-inactive)').length);
    }

    /* ── RELOAD ── */
    $('#btn-reload-sub').on('click', function () {
        if (state.id) loadSub(state.id);
    });

    /* ── MOBILE JUMP ── */
    $('#btn-jump-tree').on('click', function () {
        $('html,body').animate({ scrollTop: $('.coa-tree-panel').offset().top - 20 }, 400);
    });

});
</script>