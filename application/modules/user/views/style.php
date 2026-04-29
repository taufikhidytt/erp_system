<style>
    /* Split panel height: ikut tinggi viewport dikurangi estimasi
    topbar + page padding. Gunakan min-height agar tidak terpotong. */
    .perm-panel-left {
    width: 280px;
    min-width: 280px;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    flex-shrink: 0;
    }
    .perm-panel-right {
    flex: 1;
    min-width: 0;
    max-height: calc(100vh - 200px);
    overflow-y: auto;
    }

    /* Thin scrollbar agar tidak makan ruang */
    .perm-panel-left::-webkit-scrollbar,
    .perm-panel-right::-webkit-scrollbar { width: 4px; }
    .perm-panel-left::-webkit-scrollbar-thumb,
    .perm-panel-right::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 2px; }

    /* Menu leaf row */
    .menu-leaf {
    padding: .45rem .75rem;
    cursor: pointer;
    border-radius: 6px;
    display: flex; align-items: center; gap: .5rem;
    transition: background .1s;
    font-size: .82rem;
    }
    .menu-leaf:hover { background: #f0f4ff; }
    .menu-leaf.active { background: #dde7ff; color: #3b5bdb; font-weight: 600; }

    /* Override indicator dot */
    .ov-dot {
    width: 7px; height: 7px; border-radius: 50%;
    background: #fd7e14; flex-shrink: 0; display: none;
    }
    .has-ov .ov-dot { display: block; }

    /* CRUD toggle face */
    .crud-face {
    width: 54px; height: 46px; border-radius: 8px;
    border: 1.5px solid #dee2e6; background: #f8f9fa;
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; gap: 2px;
    transition: all .12s; font-size: .62rem; font-weight: 700;
    color: #6c757d; font-family: monospace;
    }
    .crud-face i { font-size: .82rem; }
    .crud-face.on-i { background:#cfe2ff; border-color:#0d6efd; color:#0d6efd; }
    .crud-face.on-v { background:#d1e7dd; border-color:#198754; color:#198754; }
    .crud-face.on-u { background:#fff3cd; border-color:#ffc107; color:#856404; }
    .crud-face.on-d { background:#f8d7da; border-color:#dc3545; color:#dc3545; }

    /* Permission pill */
    .perm-pill {
    cursor: pointer; user-select: none;
    font-size: .78rem; transition: all .12s;
    border: 1.5px solid #dee2e6 !important;
    }
    .perm-pill.on-action { background:#dde7ff; border-color:#3b5bdb !important; color:#3b5bdb; }
    .perm-pill.on-field  { background:#d1e7dd; border-color:#198754 !important; color:#198754; }
    .perm-pill:not(.on-action):not(.on-field) { background:#f8f9fa; color:#6c757d; }

    /* Tab visibility switch */
    .tab-sw {
    width: 36px; height: 20px; border-radius: 10px;
    background: #adb5bd; border: none; position: relative;
    cursor: pointer; transition: background .18s; flex-shrink: 0;
    }
    .tab-sw::after {
    content:''; position:absolute; top:2px; left:2px;
    width:16px; height:16px; border-radius:50%;
    background:#fff; transition:transform .18s;
    box-shadow:0 1px 2px rgba(0,0,0,.2);
    }
    .tab-sw.on { background:#fd7e14; }
    .tab-sw.on::after { transform:translateX(16px); }

    /* Field chip inside tabs */
    .f-chip {
    cursor:pointer; user-select:none; font-size:.73rem;
    transition:all .12s; border:1.5px solid #dee2e6 !important;
    }
    .f-chip.on { background:#fff3cd; border-color:#ffc107 !important; color:#856404; }
    .f-chip:not(.on) { background:#f8f9fa; color:#6c757d; }

    /* JSON dark block */
    .json-block {
    background:#212529; color:#adb5bd;
    font-family:'Courier New',monospace; font-size:.72rem;
    line-height:1.7; white-space:pre; overflow-x:auto;
    border-radius:0 0 .375rem .375rem; padding:1rem;
    }
    .jk{color:#79c0ff} .jv{color:#56d364} .js{color:#e3b341}

    /* Accordion custom: hilangkan border & bg Bootstrap default */
    .perm-accordion .accordion-button {
    font-size: .83rem; font-weight: 600; padding: .55rem .85rem;
    background: transparent; box-shadow: none; color: #212529;
    }
    .perm-accordion .accordion-button:not(.collapsed) { color: #3b5bdb; background:#f0f4ff; }
    .perm-accordion .accordion-button::after { width:14px; height:14px; background-size:14px; }
    .perm-accordion .accordion-item { border: none; border-bottom: 1px solid #f0f0f0; }
    .perm-accordion .accordion-body { padding: .35rem .75rem .5rem; }

    /* Mobile: stack */
    @media(max-width:767.98px) {
    .perm-panel-left, .perm-panel-right {
        width: 100% !important; min-width: 0 !important;
        max-height: none; overflow-y: visible;
    }
    .perm-split { flex-direction: column !important; }
    }
</style>