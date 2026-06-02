$(document).ready(function () {

    /* =================================================================
       SHORTCUT MAP
       /        → fokus search
       Alt+I    → buka / tutup bottom sheet shortcut
       Alt+N    → tambah data baru
       [        → toggle sidebar
       Alt+H    → dashboard
       Alt+R    → reload
       Alt+Q    → logout
       Esc      → tutup dropdown / bottom sheet
    ================================================================= */

    function isTyping() {
        var tag = (document.activeElement || {}).tagName || '';
        return /^(INPUT|TEXTAREA|SELECT)$/i.test(tag)
            || $(document.activeElement).attr('contenteditable') === 'true';
    }

    /* =================================================================
       1 — DATA MENU SIDEBAR
    ================================================================= */
    var menuData = [];

    $('#side-menu li').each(function () {
        var $li   = $(this);
        var $link = $li.children('a').not('.has-arrow');
        if (!$link.length) return;
        var href = $.trim($link.attr('href') || '');
        if (!href || href === '#' || href.toLowerCase().indexOf('javascript') === 0) return;
        var text = $.trim($link.find('span').first().text() || $link.text());
        if (!text) return;
        var $parentLi  = $li.closest('ul.sub-menu').closest('li');
        var parentText = '';
        var parentIcon = '';
        if ($parentLi.length) {
            parentText = $.trim($parentLi.children('a').find('span').first().text() || $parentLi.children('a').text());
            var ic = $parentLi.children('a').find('i').attr('class') || '';
            if (ic) parentIcon = ic;
        }
        menuData.push({ text: text, href: href, parent: parentText, parentIcon: parentIcon });
    });

    /* =================================================================
       2 — DROPDOWN SEARCH
    ================================================================= */
    var $dropdown = $([
        '<div id="topbar-search-dropdown">',
        '  <ul id="topbar-search-results"></ul>',
        '  <div id="topbar-search-empty" style="display:none;">',
        '    <i class="ri-file-search-line"></i> Menu tidak ditemukan',
        '  </div>',
        '</div>'
    ].join('')).appendTo('body');

    /* =================================================================
       3 — BOTTOM SHEET SHORTCUT (UBAH KE GRID)
    ================================================================= */
    var scGroups = [
        {
            title: 'Navigasi',
            icon: 'ri-compass-3-line',
            items: [
                { keys: ['Alt', 'H'],  desc: 'Kembali ke Dashboard' },
                { keys: ['Alt', 'R'],  desc: 'Reload halaman' },
                { keys: ['['],         desc: 'Minimize / maximize sidebar' },
            ]
        },
        {
            title: 'Pencarian',
            icon: 'ri-search-line',
            items: [
                { keys: ['/'],         desc: 'Fokus ke pencarian menu' },
                { keys: ['↑', '↓'],   desc: 'Navigasi hasil pencarian' },
                { keys: ['Enter'],     desc: 'Buka menu yang dipilih' },
            ]
        },
        {
            title: 'Aksi Halaman',
            icon: 'ri-apps-line',
            items: [
                { keys: ['Alt', 'N'],  desc: 'Tambah data baru' },
                { keys: ['Alt', 'I'],  desc: 'Tampilkan daftar shortcut' },
            ]
        },
        {
            title: 'Sistem',
            icon: 'ri-settings-3-line',
            items: [
                { keys: ['Alt', 'Q'],  desc: 'Logout' },
                { keys: ['Esc'],       desc: 'Tutup dropdown / panel' },
            ]
        }
    ];

    function buildKbd(keys) {
        return keys.map(function (k) {
            return '<kbd class="sc-kbd">' + k + '</kbd>';
        }).join('<span class="sc-plus">+</span>');
    }

    var groupsHtml = '';
    $.each(scGroups, function (i, g) {
        var cards = '';
        $.each(g.items, function (j, item) {
            // Ubah menjadi struktur Grid Card
            cards += [
                '<div class="sc-grid-card">',
                '  <div class="sc-key-wrap">' + buildKbd(item.keys) + '</div>',
                '  <div class="sc-desc-wrap">' + item.desc + '</div>',
                '</div>'
            ].join('');
        });
        groupsHtml += [
            '<div class="sc-group">',
            '  <div class="sc-group-title">',
            '    <i class="' + g.icon + '"></i>' + g.title,
            '  </div>',
            '  <div class="sc-group-grid">' + cards + '</div>',
            '</div>'
        ].join('');
    });

    var $bs = $([
        '<div id="sc-backdrop"></div>',
        '<div id="sc-sheet" role="dialog" aria-modal="true" aria-label="Keyboard Shortcuts">',
        '  <div id="sc-drag-handle-wrap">',
        '    <div id="sc-drag-handle"></div>',
        '  </div>',
        '  <div id="sc-sheet-header">',
        '    <div id="sc-sheet-title">',
        '      <i class="ri-keyboard-line"></i>',
        '      <span>Keyboard Shortcuts</span>',
        '    </div>',
        '    <button id="sc-sheet-close" aria-label="Tutup"><i class="ri-close-line"></i></button>',
        '  </div>',
        '  <div id="sc-sheet-body">' + groupsHtml + '</div>',
        '  <div id="sc-sheet-footer">',
        '    Tekan <kbd class="sc-kbd sc-kbd-sm">Alt</kbd><span class="sc-plus">+</span><kbd class="sc-kbd sc-kbd-sm">I</kbd> atau <kbd class="sc-kbd sc-kbd-sm">Esc</kbd> untuk menutup',
        '  </div>',
        '</div>'
    ].join('')).appendTo('body');

    var $backdrop  = $('#sc-backdrop');
    var $sheet     = $('#sc-sheet');
    var sheetOpen  = false;

    /* =================================================================
       4 — CSS (DIPERBARUI UNTUK GRID)
    ================================================================= */
    $('<style id="topbar-search-style">').text([

        /* Dropdown search */
        '#topbar-search-dropdown{display:none;position:fixed;z-index:9999;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:280px;max-width:360px;max-height:360px;overflow-y:auto;font-family:"Poppins",sans-serif}',
        '#topbar-search-results{list-style:none;margin:0;padding:6px 0}',
        '#topbar-search-results li a{display:flex;align-items:center;gap:10px;padding:8px 16px;color:#333;text-decoration:none;font-size:13px;transition:background .15s;outline:none}',
        '#topbar-search-results li a:hover,#topbar-search-results li a.is-active{background:#eff6ff;color:#0d6efd}',
        '#topbar-search-results li a.is-active .search-name,#topbar-search-results li a:hover .search-name{color:#0d6efd}',
        '#topbar-search-results li a .search-icon{font-size:16px;color:#64748b;flex-shrink:0}',
        '#topbar-search-results li a.is-active .search-icon{color:#0d6efd}',
        '#topbar-search-results li a .search-info{display:flex;flex-direction:column;line-height:1.3}',
        '#topbar-search-results li a .search-name{font-weight:600;color:#1e293b}',
        '#topbar-search-results li a .search-parent{font-size:11px;color:#94a3b8}',
        '.search-highlight{color:#0d6efd;font-weight:700}',
        '#topbar-search-empty{padding:20px;text-align:center;color:#94a3b8;font-size:13px}',
        '#topbar-search-empty i{display:block;font-size:24px;margin-bottom:4px}',
        '#topbar-search-dropdown::-webkit-scrollbar{width:4px}',
        '#topbar-search-dropdown::-webkit-scrollbar-track{background:transparent}',
        '#topbar-search-dropdown::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px}',

        /* Backdrop */
        '#sc-backdrop{display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,0);transition:background .3s ease}',
        '#sc-backdrop.sc-open{display:block;background:rgba(0,0,0,.45)}',

        /* Bottom sheet */
        '#sc-sheet{',
        '  position:fixed;bottom:0;left:0;right:0;',
        '  z-index:10001;',
        '  background:#fff;',
        '  border-radius:20px 20px 0 0;',
        '  box-shadow:0 -8px 40px rgba(0,0,0,.18);',
        '  font-family:"Poppins",sans-serif;',
        '  max-height:85vh;',
        '  display:flex;flex-direction:column;',
        '  transform:translateY(100%);',
        '  transition:transform .35s cubic-bezier(.32,1,.45,1);',
        '  will-change:transform;',
        '  touch-action:none;',
        '}',
        '#sc-sheet.sc-open{transform:translateY(0)}',

        /* Drag handle */
        '#sc-drag-handle-wrap{padding:10px 0 4px;display:flex;justify-content:center;cursor:grab;flex-shrink:0}',
        '#sc-drag-handle-wrap:active{cursor:grabbing}',
        '#sc-drag-handle{width:40px;height:4px;background:#d1d5db;border-radius:4px;transition:background .2s}',
        '#sc-drag-handle-wrap:hover #sc-drag-handle{background:#94a3b8}',

        /* Sheet header */
        '#sc-sheet-header{display:flex;align-items:center;justify-content:space-between;padding:4px 20px 12px;flex-shrink:0}',
        '#sc-sheet-title{display:flex;align-items:center;gap:8px;font-size:15px;font-weight:600;color:#1e293b}',
        '#sc-sheet-title i{font-size:18px;color:#6366f1}',
        '#sc-sheet-close{border:none;background:#f1f5f9;width:30px;height:30px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:#64748b;transition:background .15s,color .15s;padding:0}',
        '#sc-sheet-close:hover{background:#e0e7ff;color:#6366f1}',

        /* Sheet body — scrollable (UBAH GAP & PADDING UNTUK GRID) */
        '#sc-sheet-body{overflow-y:auto;padding:16px 20px;flex:1;min-height:0;display:flex;flex-direction:column;gap:20px;}',
        '#sc-sheet-body::-webkit-scrollbar{width:3px}',
        '#sc-sheet-body::-webkit-scrollbar-track{background:transparent}',
        '#sc-sheet-body::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:4px}',

        /* Groups Header */
        '.sc-group{background:#fff;}',
        '.sc-group-title{',
        '  display:flex;align-items:center;gap:8px;',
        '  font-size:11.5px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;',
        '  color:#64748b;margin-bottom:12px;',
        '}',
        '.sc-group-title i{font-size:14px;color:#6366f1}',

        /* Grid Layout Utama */
        '.sc-group-grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:12px;}',

        /* Grid Cards / Kotak Menu */
        '.sc-grid-card{',
        '  background:#f8fafc;',
        '  border:1px solid #e2e8f0;',
        '  border-radius:10px;',
        '  padding:14px;',
        '  display:flex;flex-direction:column;gap:10px;',
        '  transition:all .2s ease;',
        '}',
        '.sc-grid-card:hover{',
        '  background:#fff;',
        '  border-color:#cbd5e1;',
        '  box-shadow:0 3px 10px rgba(0,0,0,.04);',
        '  transform:translateY(-2px);',
        '}',
        '.sc-desc-wrap{font-size:12.5px;color:#334155;line-height:1.4;}',
        '.sc-key-wrap{display:flex;align-items:center;flex-wrap:wrap;}',

        /* kbd style */
        '.sc-kbd{',
        '  display:inline-flex;align-items:center;justify-content:center;',
        '  background:#fff;',
        '  border:1px solid #cbd5e1;',
        '  border-bottom-width:2px;',
        '  border-radius:6px;',
        '  padding:3px 8px;',
        '  font-size:12px;',
        '  font-weight:600;',
        '  font-family:monospace;',
        '  color:#334155;',
        '  line-height:1.5;',
        '  min-width:24px;',
        '  text-align:center;',
        '}',
        '.sc-kbd-sm{font-size:11px;padding:1px 5px}',
        '.sc-plus{color:#94a3b8;font-size:12px;margin:0 4px;font-weight:bold;}',

        /* Sheet footer */
        '#sc-sheet-footer{',
        '  padding:12px 20px 16px;',
        '  text-align:center;',
        '  font-size:11.5px;',
        '  color:#94a3b8;',
        '  border-top:1px solid #f1f5f9;',
        '  flex-shrink:0;',
        '  display:flex;align-items:center;justify-content:center;gap:3px;',
        '}',

        /* Toast */
        '#sc-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(12px);background:#1e293b;color:#fff;font-family:"Poppins",sans-serif;font-size:12px;padding:8px 16px;border-radius:8px;z-index:10002;opacity:0;pointer-events:none;transition:opacity .2s,transform .2s;white-space:nowrap}',
        '#sc-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}',

    ].join('\n')).appendTo('head');

    /* =================================================================
       5 — TOAST
    ================================================================= */
    var $toast    = $('<div id="sc-toast">').appendTo('body');
    var toastTimer;
    function showToast(msg) {
        clearTimeout(toastTimer);
        $toast.text(msg).addClass('show');
        toastTimer = setTimeout(function () { $toast.removeClass('show'); }, 1800);
    }

    /* =================================================================
       6 — BOTTOM SHEET OPEN / CLOSE / DRAG
    ================================================================= */
    window.openSheet = function () {
        sheetOpen = true;
        $backdrop.addClass('sc-open');
        $sheet.addClass('sc-open');
        document.body.style.overflow = 'hidden';
    };

    window.closeSheet = function () {
        sheetOpen = false;
        $sheet.removeClass('sc-open');
        $backdrop.removeClass('sc-open');
        document.body.style.overflow = '';
    };

    window.toggleSheet = function () {
        sheetOpen ? window.closeSheet() : window.openSheet();
    };

    $('#sc-sheet-close').on('click', window.closeSheet);
    $backdrop.on('click', window.closeSheet);

    /* Drag-to-close (touch & mouse) */
    var dragStart = null;
    var dragCurrent = 0;

    function onDragStart(clientY) {
        dragStart   = clientY;
        dragCurrent = 0;
        $sheet.css('transition', 'none');
    }
    function onDragMove(clientY) {
        if (dragStart === null) return;
        var dy = Math.max(0, clientY - dragStart);
        dragCurrent = dy;
        $sheet.css('transform', 'translateY(' + dy + 'px)');
    }
    function onDragEnd() {
        if (dragStart === null) return;
        dragStart = null;
        $sheet.css('transition', '');
        if (dragCurrent > 120) {
            window.closeSheet();
        } else {
            $sheet.css('transform', '');
        }
        dragCurrent = 0;
    }

    $('#sc-drag-handle-wrap')
        .on('mousedown', function (e) { onDragStart(e.clientY); })
        .on('touchstart', function (e) { onDragStart(e.originalEvent.touches[0].clientY); }, { passive: true });

    $(document)
        .on('mousemove', function (e) { if (dragStart !== null) onDragMove(e.clientY); })
        .on('mouseup',   function ()  { onDragEnd(); })
        .on('touchmove', function (e) {
            if (dragStart !== null) onDragMove(e.originalEvent.touches[0].clientY);
        }, { passive: true })
        .on('touchend',  function ()  { onDragEnd(); });

    /* =================================================================
       7 — FUNGSI SEARCH
    ================================================================= */
    var activeIndex = -1;

    function escapeRegex(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }

    function highlight(text, q) {
        if (!q) return text;
        return text.replace(new RegExp('(' + escapeRegex(q) + ')', 'gi'),
            '<span class="search-highlight">$1</span>');
    }

    function positionDropdown($input) {
        var off  = $input.offset();
        var left = off.left;
        var ddW  = Math.min(360, Math.max(280, $input.outerWidth()));
        if (left + ddW > $(window).width() - 10) left = $(window).width() - ddW - 10;
        $dropdown.css({ top: off.top + $input.outerHeight() + 4, left: left, width: ddW }).show();
    }

    function getItems() { return $('#topbar-search-results li a'); }

    function setActive(idx) {
        var $items = getItems(), total = $items.length;
        if (!total) return;
        if (idx < -1)     idx = total - 1;
        if (idx >= total) idx = 0;
        activeIndex = idx;
        $items.removeClass('is-active');
        if (activeIndex >= 0) {
            var $a  = $items.eq(activeIndex).addClass('is-active');
            var el  = $dropdown[0];
            var top = $a.position().top + el.scrollTop;
            var bot = top + $a.outerHeight();
            if (bot > el.scrollTop + el.clientHeight) el.scrollTop = bot - el.clientHeight;
            else if (top < el.scrollTop)              el.scrollTop = top;
        }
    }

    function resetActive() { activeIndex = -1; getItems().removeClass('is-active'); }
    function closeDropdown() { $dropdown.hide(); resetActive(); }

    function renderResults(query, $input) {
        var $ul = $('#topbar-search-results').empty();
        $('#topbar-search-empty').hide();
        var q = $.trim(query).toLowerCase();
        resetActive();
        if (!q) { $dropdown.hide(); return; }

        var matches = $.grep(menuData, function (item) {
            return item.text.toLowerCase().indexOf(q) !== -1
                || item.parent.toLowerCase().indexOf(q) !== -1;
        });

        if (!matches.length) { $('#topbar-search-empty').show(); positionDropdown($input); return; }

        $.each(matches, function (i, item) {
            $ul.append(
                $('<li>').append(
                    $('<a>').attr('href', item.href).html([
                        '<i class="' + (item.parentIcon || 'ri-arrow-right-s-line') + ' search-icon"></i>',
                        '<span class="search-info">',
                        '<span class="search-name">' + highlight(item.text, query) + '</span>',
                        item.parent ? '<span class="search-parent">' + item.parent + '</span>' : '',
                        '</span>'
                    ].join(''))
                )
            );
        });
        positionDropdown($input);
    }

    /* =================================================================
       8 — BINDING INPUT SEARCH
    ================================================================= */
    var $desktopInput = $('.app-search .form-control');
    var $mobileInput  = $('#page-header-search-dropdown').closest('.dropdown')
                            .find('.dropdown-menu .form-control');

    $('.app-search').on('submit', function (e) { e.preventDefault(); });
    $('#page-header-search-dropdown').closest('.dropdown').find('.dropdown-menu form')
        .on('submit', function (e) { e.preventDefault(); });

    $desktopInput.add($mobileInput).on('input', function () {
        renderResults($(this).val(), $(this));
    }).on('focus', function () {
        if ($.trim($(this).val())) renderResults($(this).val(), $(this));
    }).on('keydown', function (e) {
        var isOpen = $dropdown.is(':visible');
        var total  = getItems().length;
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                isOpen ? setActive(activeIndex + 1) : renderResults($(this).val(), $(this));
                break;
            case 'ArrowUp':
                e.preventDefault();
                if (isOpen) activeIndex <= 0 ? resetActive() : setActive(activeIndex - 1);
                break;
            case 'Enter':
                e.preventDefault();
                var $items = getItems();
                var href = (activeIndex >= 0 && total > 0)
                    ? $items.eq(activeIndex).attr('href')
                    : (total === 1 ? $items.eq(0).attr('href') : null);
                if (href) { closeDropdown(); window.location.href = href; }
                break;
            case 'Escape':
                closeDropdown();
                $(this).blur();
                break;
        }
    });

    $dropdown.on('mouseenter', '#topbar-search-results li a', function () {
        activeIndex = getItems().index($(this));
        getItems().removeClass('is-active');
        $(this).addClass('is-active');
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('#topbar-search-dropdown, .app-search, #page-header-search-dropdown').length)
            closeDropdown();
    });

    $(window).on('resize', function () {
        var $a = $desktopInput.is(':focus') ? $desktopInput
               : $mobileInput.is(':focus')  ? $mobileInput : null;
        $a && $.trim($a.val()) ? positionDropdown($a) : closeDropdown();
    });

    $('#page-header-search-dropdown').on('click', function () {
        setTimeout(function () { $mobileInput.trigger('focus'); }, 200);
    });

    /* =================================================================
       9 — GLOBAL KEYBOARD SHORTCUTS
    ================================================================= */
    $(document).on('keydown', function (e) {

        /* / → fokus search */
        if (e.key === '/' && !isTyping()) {
            e.preventDefault();
            var $t = $desktopInput.is(':visible') ? $desktopInput : $mobileInput;
            $t.trigger('focus').select();
            return;
        }

        /* [ → toggle sidebar */
        if (e.key === '[' && !isTyping()) {
            e.preventDefault();
            $('#vertical-menu-btn').trigger('click');
            var collapsed = $('body').hasClass('sidebar-enable') || $('body').hasClass('vertical-collpsed');
            showToast(collapsed ? '⬅ Sidebar disembunyikan' : '➡ Sidebar ditampilkan');
            return;
        }

        /* Esc → tutup sheet / dropdown */
        if (e.key === 'Escape') {
            if (sheetOpen) { window.closeSheet(); return; }
            closeDropdown();
            return;
        }

        var altOnly = e.altKey && !e.ctrlKey && !e.metaKey && !e.shiftKey;
        if (!altOnly) return;

        switch (e.key.toLowerCase()) {

            /* Alt+I → bottom sheet shortcut */
            case 'i':
                e.preventDefault();
                window.toggleSheet();
                break;

            /* Alt+N → tambah data baru */
            case 'n':
                e.preventDefault();
                var $btn = $(
                    `.dt-buttons button[title="Tambah"], a[title="Tambah"]`
                ).filter(':visible').first();
                if ($btn.length) { $btn[0].click(); showToast('Alt+N  →  Tambah data baru'); }
                else             { showToast('Tombol "Tambah" tidak ditemukan di halaman ini'); }
                break;

            /* Alt+H → Dashboard */
            case 'h':
                e.preventDefault();
                showToast('Alt+H  →  Dashboard');
                setTimeout(function () {
                    window.location.href = $('#side-menu a[href*="dashboard"]').first().attr('href')
                        || (typeof config_app !== 'undefined' ? config_app.url : '/') + 'dashboard';
                }, 300);
                break;

            /* Alt+R → Reload */
            case 'r':
                e.preventDefault();
                showToast('Alt+R  →  Reload halaman...');
                setTimeout(function () { window.location.reload(); }, 400);
                break;

            /* Alt+Q → Logout */
            case 'q':
                e.preventDefault();
                var logoutHref = $('a[href*="logout"]').first().attr('href') || '#';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'question',
                        title: 'Keluar?',
                        text: 'Yakin ingin logout dari aplikasi?',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Keluar',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                    }).then(function (r) { if (r.isConfirmed) window.location.href = logoutHref; });
                } else {
                    if (confirm('Yakin ingin logout?')) window.location.href = logoutHref;
                }
                break;
        }
    });

});