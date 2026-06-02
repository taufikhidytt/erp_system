(function ($) {
    'use strict';

    const TABLE_SEL = '#table-detail';
    const ARROW_KEYS = [37, 38, 39, 40]; // Left, Up, Right, Down

    // ── Helper: cari td aktif (Keys focus atau browser focus) ─────
    function getActiveTd() {
        const $table = $(TABLE_SEL);

        // Prioritas 1: class .focus dari DataTables Keys
        const $keysFocus = $table.find('tbody td.focus').first();
        if ($keysFocus.length) return $keysFocus;

        // Prioritas 2: td yang mengandung elemen yang sedang fokus browser
        const $parentTd = $(document.activeElement).closest(TABLE_SEL + ' tbody td');
        if ($parentTd.length) return $parentTd;

        return $();
    }

    // ── Helper: apakah sedang dalam mode edit di tabel ───────────
    function isEditing() {
        const $table = $(TABLE_SEL);
        // input / textarea visible (bukan d-none)
        if ($table.find('tbody .edit-mode:not(.d-none)').length) return true;
        // select sedang difokus
        const $active = $(document.activeElement);
        if ($active.is('select') && $active.closest(TABLE_SEL).length) return true;
        // textarea keterangan sedang difokus (atau modal keterangan terbuka)
        if ($active.is('textarea') && $active.closest(TABLE_SEL).length) return true;
        if ($('#modalKeterangan').hasClass('show')) return true;
        return false;
    }

    // ═══════════════════════════════════════════════════════════════
    //  F2 — buka edit mode
    // ═══════════════════════════════════════════════════════════════
    $(document).on('keydown.detail-f2', function (e) {
        if (e.keyCode !== 113) return; // 113 = F2

        const $td = getActiveTd();
        if (!$td.length) return;
        if (!$td.closest(TABLE_SEL).length) return;

        e.preventDefault();
        e.stopPropagation();

        // ── input (qty / harga) ──────────────────────────────────
        const $input = $td.find('.edit-mode');
        const $span  = $td.find('.view-mode');
        if ($input.length && $input.hasClass('d-none')) {
            // simpan orig sebelum buka
            $input.data('ie-orig', $input.val());
            $span.addClass('d-none');
            $input.removeClass('d-none').focus().select();
            return;
        }

        // ── select (uom) ──────────────────────────────────────────
        const $select = $td.find('select.uom-select');
        if ($select.length) {
            // simpan selectedIndex sebelum buka
            $select.data('ie-orig-index', $select[0].selectedIndex);
            $select.focus();
            // buka dropdown native dengan simulasi mousedown + click
            $select.trigger('mousedown');
            return;
        }

        // ── textarea (keterangan) → buka modal ───────────────────
        const $textarea = $td.find('textarea');
        if ($textarea.length) {
            $textarea.trigger('click'); // handler di detail.php sudah handle ini
            return;
        }
    });

    // ═══════════════════════════════════════════════════════════════
    //  ESC — keluar edit mode, revert nilai
    // ═══════════════════════════════════════════════════════════════

    // ESC untuk input (edit-mode) — delegasi di tbody tabel
    $(document).on('keydown.detail-esc-input', TABLE_SEL + ' tbody', function (e) {
        if (e.keyCode !== 27) return;

        const $active  = $(document.activeElement);
        const $editEl  = $active.hasClass('edit-mode')
            ? $active
            : $active.closest('.edit-mode');

        if (!$editEl.length) return;
        if (!$editEl.closest(TABLE_SEL).length) return;

        e.preventDefault();
        e.stopPropagation();

        const $td   = $editEl.closest('td');
        const $span = $td.find('.view-mode');
        const orig  = $editEl.data('ie-orig');

        // Restore nilai
        if (orig !== undefined) {
            $editEl.val(orig);
            // Update teks span sesuai tipe
            if ($editEl.hasClass('harga-edit') || $editEl.hasClass('qty-edit')) {
                $span.text(typeof $.inputNumber !== 'undefined'
                    ? $.inputNumber.format(orig)
                    : orig);
            } else {
                $span.text(orig === '' ? '0' : orig);
            }
        }

        // Tutup edit mode
        $editEl.addClass('d-none').removeData('ie-orig');
        $span.removeClass('d-none');

        // Recalc agar subtotal/total tetap konsisten
        const $row = $td.closest('tr');
        if (typeof hitungRow === 'function') hitungRow($row);
        else if (typeof hitungTotal === 'function') hitungTotal();
    });

    // ESC untuk select (uom) — restore selectedIndex
    $(document).on('keydown.detail-esc-select', TABLE_SEL + ' tbody select', function (e) {
        if (e.keyCode !== 27) return;

        const $select   = $(this);
        const origIndex = $select.data('ie-orig-index');

        e.preventDefault();
        e.stopPropagation();

        if (origIndex !== undefined) {
            $select[0].selectedIndex = origIndex;
            $select.trigger('change'); // update to_qty hidden input
            $select.removeData('ie-orig-index');
        }

        $select.blur();
    });

    // ESC untuk modal keterangan — tutup modal tanpa simpan
    $(document).on('keydown.detail-esc-modal', function (e) {
        if (e.keyCode !== 27) return;
        if (!$('#modalKeterangan').hasClass('show')) return;

        e.preventDefault();
        e.stopPropagation();

        $('#modalKeterangan').modal('hide');
    });

    // ═══════════════════════════════════════════════════════════════
    //  Enter / Tab — Tutup Edit Mode (Blur)
    // ═══════════════════════════════════════════════════════════════
    $(document).on('keydown.detail-tab-enter', TABLE_SEL + ' tbody', function (e) {
        const isTab   = e.keyCode === 9;   // Tab
        const isEnter = e.keyCode === 13;  // Enter
        
        if (!isTab && !isEnter) return;

        // Jika sedang dalam edit mode, cukup blur elemen aktifnya
        // Setelah blur, DataTables Keys akan otomatis mengambil alih navigasi
        if (isEditing()) {
            e.preventDefault();
            e.stopPropagation();

            const $active = $(document.activeElement);
            if ($active.hasClass('edit-mode')) {
                $active.trigger('blur');
            } else if ($active.is('select') || $active.is('textarea')) {
                $active.blur();
            }
        }
    });

    // ═══════════════════════════════════════════════════════════════
    //  Arrow keys — cegah DataTables Keys menggeser sel saat editing
    // ═══════════════════════════════════════════════════════════════

    // Arrow di input / textarea
    $(document).on('keydown.detail-arrow-input', TABLE_SEL + ' tbody', function (e) {
        if (!ARROW_KEYS.includes(e.keyCode)) return;
        if (!isEditing()) return;

        // Biarkan arrow bekerja normal di dalam input/textarea
        // tapi cegah propagasi ke DataTables Keys
        const $active = $(document.activeElement);
        if ($active.is('input, textarea') && $active.closest(TABLE_SEL).length) {
            e.stopPropagation();
        }
    });

    // Arrow di select — stopPropagation agar DT Keys tidak ikut campur
    $(document).on('keydown.detail-arrow-select', TABLE_SEL + ' tbody select', function (e) {
        if (!ARROW_KEYS.includes(e.keyCode)) return;
        e.stopPropagation();
    });

    // Arrow di modal keterangan (textarea di dalam modal)
    $(document).on('keydown.detail-arrow-modal', '#modalKeteranganText', function (e) {
        if (!ARROW_KEYS.includes(e.keyCode)) return;
        e.stopPropagation();
    });

    // ═══════════════════════════════════════════════════════════════
    //  Simpan orig saat edit mode dibuka via klik (bukan F2)
    //  Agar ESC tetap bisa revert meski dibuka dengan klik biasa
    // ═══════════════════════════════════════════════════════════════

    // input: simpan orig saat focus
    $(document).on('focus.detail-orig-input', TABLE_SEL + ' tbody .edit-mode', function () {
        const $input = $(this);
        if ($input.data('ie-orig') === undefined) {
            $input.data('ie-orig', $input.val());
        }
    });

    // input: hapus orig saat blur normal (bukan ESC)
    $(document).on('blur.detail-orig-input', TABLE_SEL + ' tbody .edit-mode', function () {
        const $input = $(this);
        setTimeout(function () { $input.removeData('ie-orig'); }, 150);
    });

    // select: simpan selectedIndex saat focus
    $(document).on('focus.detail-orig-select', TABLE_SEL + ' tbody select.uom-select', function () {
        const $select = $(this);
        if ($select.data('ie-orig-index') === undefined) {
            $select.data('ie-orig-index', $select[0].selectedIndex);
        }
    });

    // select: hapus orig saat blur normal (bukan ESC)
    $(document).on('blur.detail-orig-select', TABLE_SEL + ' tbody select.uom-select', function () {
        const $select = $(this);
        setTimeout(function () { $select.removeData('ie-orig-index'); }, 150);
    });

    $('#modalKeterangan').on('show.bs.modal', function() {
        setTimeout(function() {
            $('#modalKeteranganText').focus();
        }, 500);
    });
})(jQuery);