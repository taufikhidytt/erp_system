/* ================================================================
   inline-editor.js
   Reusable inline-editing layer untuk DataTables.
   Dependensi: jQuery, DataTables, Bootstrap 5 (tooltip), 
               Select2 (opsional), inputNumber plugin (opsional)

   Cara pakai:
   ─────────────────────────────────────────────────────────────
   const editor = InlineEditor.init({
       table  : '#table',          // selector string ATAU instance DataTable
       add    : true,              // tampilkan tombol insert
       edit   : true,              // izinkan edit cell
       urls   : {
           save   : '/uom/save',   // wajib
       },
       fields : [
           { field: 'name',         type: 'text', maxlength: 100 },
           { field: 'description',  type: 'text' },
           { field: 'qty',          type: 'number', min: 0, max: 9999, step: 1,
             attrs: { 'data-min': 1, 'data-decimal': 0, class: 'input-number' } },
           { field: 'start_date',   type: 'date' },
           { field: 'active_flag',  type: 'checkbox' },
           { field: 'uom_id',       type: 'select2',
             select2: {
                 url         : '/api/get_uom',
                 dataDefault : 'Y',
                 placeholder : 'Select Satuan',
             }
           },
           // syncTo: saat field ini dipilih, otomatis update field lain
           { field: 'kode_account', type: 'select2', required: true,
             select2: {
                 url         : '/api/get_coa',
                 placeholder : 'Pilih Kode',
                 labelFn     : row => row.code,
                 syncTo      : 'nama_account',
             }
           },
           { field: 'nama_account', type: 'select2', required: true,
             select2: {
                 url         : '/api/get_coa',
                 placeholder : 'Pilih Nama',
                 labelFn     : row => row.name,
                 syncTo      : 'kode_account',
             }
           },
           { field: 'keterangan', inputable: false,
             compute: function(vals) {
                 const from = vals.from_uom?.label ?? '';
                 const to   = vals.to_uom?.label   ?? '';
                 const qty  = vals.to_qty           ?? 1;
                 return $.inputNumber.format(1) + ' ' + from + ' = ' + $.inputNumber.format(qty) + ' ' + to;
             }
           },
       ],
   });
   ================================================================ */

const InlineEditor = (() => {

    // ── Icon helper ───────────────────────────────────────────────
    const iconFlag = val => val === 'Y'
        ? `<i class="text-success fa fa-check" data-bs-toggle="tooltip" data-bs-placement="left" title="Yes"></i>`
        : `<i class="text-danger fa fa-times"  data-bs-toggle="tooltip" data-bs-placement="left" title="No"></i>`;

    // ── Render nilai ke tampilan (view mode) ──────────────────────
    function renderView(cfg, val) {
        if (!cfg) return val ?? '';
        if (cfg.type === 'checkbox') return iconFlag(val);
        if (cfg.type === 'select2') return val?.label ?? val?.id ?? val ?? '';
        return val ?? '';
    }

    // ── Buat elemen input sesuai type ─────────────────────────────
    function makeInput(cfg, val) {
        const { field, type, maxlength, attrs = {} } = cfg;
        let $el;

        if (cfg.attrs?.class?.includes('input-number')) {
            val = $.inputNumber.unformat(val);
        }

        switch (type) {
            case 'checkbox':
                return $('<input type="checkbox">')
                    .attr('data-field', field)
                    .prop('checked', val === 'Y');

            case 'select2':
                $el = $('<select class="select2">').attr({
                    'data-field'   : field,
                    'data-url'     : cfg.select2.url,
                    'data-default' : cfg.select2.dataDefault ?? '',
                    placeholder    : cfg.select2.placeholder ?? '',
                });
                break;

            default:
                // text, number, date, time, datetime-local
                $el = $('<input>').attr({ type, 'data-field': field, value: val ?? '' });
                if (maxlength) $el.attr('maxlength', maxlength);
                if (cfg.min !== undefined) $el.attr('min', cfg.min);
                if (cfg.max !== undefined) $el.attr('max', cfg.max);
                if (cfg.step !== undefined) $el.attr('step', cfg.step);
                if (cfg.class !== undefined) $el.addClass(cfg.class);
        }

        // Terapkan custom attrs (class, data-*, dll)
        if (Object.keys(attrs).length) $el.attr(attrs);

        if (cfg.attrs?.class?.includes('input-number')) {
            console.log(attrs);
            $el.inputNumber();
        }

        return $el;
    }

    // ================================================================
    //  INIT — entry point utama
    // ================================================================
    function init(config) {

        const { fields, urls, extraData, add = true, edit = true } = config;

        // Terima string selector atau instance DataTable
        const dt = typeof config.table === 'string'
            ? $(config.table).DataTable()
            : config.table;
        const $table = $(dt.table().node());

        // ── Helper: pindah fokus ke cell inputable berikutnya di baris yang sama ──
        // Dipakai saat Tab ditekan di dalam input/select pada ie-new-row maupun edit mode.
        // Urutan kolom mengikuti array fields, bukan urutan DOM/tabindex browser,
        // sehingga select2 (hidden element) pun bisa dimasuki dengan benar.
        function focusNextCell($currentTd, $row) {
            // Kumpulkan semua td inputable di baris ini, sesuai urutan fields
            const inputableTds = fields
                .filter(f => f.inputable !== false && f.type !== 'checkbox')
                .map(f => $row.find(`td.ie-cell[data-field="${f.field}"]`).get(0))
                .filter(Boolean);

            const currentIndex = inputableTds.indexOf($currentTd.get(0));
            const $next = $(inputableTds[currentIndex + 1]);

            if (!$next.length) return; // sudah kolom terakhir, biarkan Tab keluar normal

            const nextField = $next.attr('data-field');
            const nextCfg   = fields.find(f => f.field === nextField);
            if (!nextCfg) return;

            if (nextCfg.type === 'select2') {
                // Trigger klik agar select2 terbuka via handler klik cell yang sudah ada
                $next.trigger('click');
            } else {
                // Input biasa: fokus langsung
                const $input = $next.find('input');
                if ($input.length) {
                    $input.focus();
                } else {
                    // Belum ada input (baris baru), init dulu via trigger klik
                    $next.trigger('click');
                }
            }
        }

        // ── Keydown: tangkap Tab & Enter di input, Arrow di select2 ──────────────
        // Ini menggantikan handler lama. Logika:
        // - Jika sedang dalam mode inputan (ie-new-row atau ie-editing):
        //   Tab → navigasi manual ke kolom berikutnya (bypass DataTables Keys)
        //   Enter → selesaikan edit (blur)
        //   Arrow → stop propagation agar DataTables Keys tidak ikut campur
        // - Jika tidak dalam mode inputan → biarkan DataTables Keys bekerja normal
        $table.on('keydown', 'td.ie-cell input:not([type="checkbox"])', function (e) {
            const $td  = $(this).closest('td');
            const $row = $td.closest('tr');
            const isNewRow = $row.hasClass('ie-new-row');
            const isEditing = $td.hasClass('ie-editing');

            // Arrow keys: selalu stop propagation saat mengetik agar kursor bisa bergerak
            if ([37, 38, 39, 40].includes(e.keyCode)) {
                e.stopPropagation();
            }

            // Tab: navigasi manual ke kolom berikutnya
            if (e.keyCode === 9) {
                e.preventDefault();
                e.stopPropagation(); // cegah DataTables Keys
                if (isNewRow) {
                    // Mode add: blur dulu agar nilai tersimpan, lalu pindah fokus
                    $(this).blur();
                    setTimeout(() => focusNextCell($td, $row), 0);
                } else {
                    // Mode edit: selesaikan edit saja
                    $(this).blur();
                }
            }

            // Enter: selesaikan edit
            if (e.keyCode === 13) {
                e.preventDefault();
                $(this).blur();
            }

            // Escape: batalkan edit, kembalikan nilai ke semula (tidak simpan perubahan)
            if (e.keyCode === 27) {
                e.preventDefault();
                e.stopPropagation();

                const orig  = $td.data('orig');
                const field = $td.attr('data-field');
                const cfg   = cfgOf(field);

                // Tandai agar blur handler tidak menyimpan perubahan
                $td.data('ie-escaping', true);
                $(this).blur();

                // Revert tampilan ke nilai semula
                $td.removeClass('ie-editing').html(renderView(cfg, orig) || orig || '');

                // Kembalikan nilai di pending ke data asli dari DataTable
                const key = $row.data('key');
                if (key && pending[key]) {
                    const rowData = dt.row($row).data();
                    if (rowData && field in rowData) {
                        pending[key].fields[field] = rowData[field];
                    } else {
                        delete pending[key].fields[field];
                    }
                    // Jika semua field di baris ini sudah kembali ke semula, hapus entri pending
                    if (rowData) {
                        const hasActualChange = Object.entries(pending[key].fields).some(([k, v]) => {
                            return JSON.stringify(v) !== JSON.stringify(rowData[k]);
                        });
                        if (!hasActualChange) {
                            delete pending[key];
                        }
                    }
                    toggleButtons(hasPending());
                }
            }
        });

        // ── Keydown untuk select2: cegah Arrow & Tab dibajak DataTables Keys ─────
        // select2 membuat elemen .select2-search__field saat dropdown terbuka —
        // tangkap di level document agar tidak terblokir oleh stopPropagation select2
        $(document).on('keydown.ie-select2', '.select2-search__field', function (e) {
            const $openTd = $table.find('td.ie-cell.ie-editing');
            if (!$openTd.length) return;

            const $row = $openTd.closest('tr');

            // Arrow: stop agar DataTables tidak navigasi baris
            if ([37, 38, 39, 40].includes(e.keyCode)) {
                e.stopPropagation();
            }

            // Tab: tutup dropdown lalu pindah ke kolom berikutnya
            if (e.keyCode === 9) {
                e.preventDefault();
                e.stopPropagation();
                // Tutup select2 — select2:close akan handle destroy & render
                const $select = $openTd.find('select');
                if ($select.length && $select.hasClass('select2-hidden-accessible')) {
                    $select.select2('close');
                }
                if ($row.hasClass('ie-new-row')) {
                    setTimeout(() => focusNextCell($openTd, $row), 50);
                }
            }

            // Escape: batalkan pilihan select2, revert ke nilai semula
            if (e.keyCode === 27) {
                e.stopPropagation();

                const $select = $openTd.find('select');
                const field   = $openTd.attr('data-field');

                // Tandai sebagai ESC (bukan pilih) agar select2:close tidak commit
                $openTd.data('ie-escaping', true);

                if ($select.length && $select.hasClass('select2-hidden-accessible')) {
                    $select.select2('close');
                }
                // select2:close akan membaca ie-escaping dan revert ke orig
            }
        });

        // ── F2: masuk edit mode dari DataTables Keys focus ────────
        // DataTables Keys mengelola fokus via class "focus" pada td, bukan
        // via browser focus, sehingga handler harus dipasang di document
        // (sama polanya dengan handler select2 di atas).
        // Tekan F2 saat td.focus aktif → identik dengan klik di td tersebut.
        $(document).on('keydown.ie-f2', function (e) {
            if (e.keyCode !== 113) return; // 113 = F2

            // Cari td yang sedang difokus DataTables Keys di tabel ini
            const $td = $table.find('td.ie-cell.focus').first();
            if (!$td.length) return;

            // Jangan buka lagi jika sudah dalam mode editing
            if ($td.hasClass('ie-editing')) return;

            e.preventDefault();
            e.stopPropagation();

            // Trigger click — handler click sudah menangani semua skenario:
            // guard edit/add, checkbox toggle, select2 open, text/number/date input
            $td.trigger('click');
        });

        // ── Pending changes ───────────────────────────────────────
        let pending = {};
        const hasPending = () => Object.keys(pending).length > 0;
        const cfgOf = field => fields.find(f => f.field === field);

        function toggleButtons(show) {
            $btnSave.toggle(show);
            $btnCancel.toggle(show);
        }

        function trackChange($row, field, val) {
            const key = $row.data('key');
            if (!pending[key]) {
                const isNew = $row.hasClass('ie-new-row');
                pending[key] = { id: $row.data('id') || '', isNew, fields: {} };

                // Jika edit baris lama, salin semua data aslinya dulu ke dalam pending
                if (!isNew) {
                    const rowData = dt.row($row).data();
                    if (rowData) {
                        fields.forEach(cfg => {
                            pending[key].fields[cfg.field] = rowData[cfg.field];
                        });
                    }
                }
            }
            pending[key].fields[field] = val;
            toggleButtons(hasPending());
        }

        // ── Helper: ambil snapshot semua nilai row saat ini ───────
        function getRowSnapshot($row) {
            const snapshot = {};
            fields.forEach(cfg => {
                const $td = $row.find(`td.ie-cell[data-field="${cfg.field}"]`);
                if (!$td.length) return;

                const key = $row.data('key');
                if (pending[key]?.fields?.[cfg.field] !== undefined) {
                    snapshot[cfg.field] = pending[key].fields[cfg.field];
                } else {
                    snapshot[cfg.field] = $td.data('val') ?? $td.text().trim();
                }
            });
            return snapshot;
        }

        // ── Helper: jalankan semua field compute dalam satu baris ─
        function runCompute($row) {
            const computeFields = fields.filter(f => typeof f.compute === 'function');
            if (!computeFields.length) return;

            const snapshot = getRowSnapshot($row);

            computeFields.forEach(cfg => {
                let result;
                try { result = cfg.compute(snapshot); } catch (_) { result = ''; }

                const $td = $row.find(`td.ie-cell[data-field="${cfg.field}"]`);
                if (!$td.length) return;

                $td.data('val', result).text(result ?? '');
                $td.addClass('ie-changed');
                trackChange($row, cfg.field, result);
            });
        }

        // ── Tandai cell editable setiap draw ──────────────────────
        dt.on('draw', function () {
            $table.find('tbody tr').each(function () {
                const $row = $(this);
                if ($row.hasClass('ie-new-row')) return;

                const data = dt.row(this).data();
                if (!data) return;

                $row.data({ id: data.id, key: data.id });

                fields.forEach((cfg, i) => {
                    const $td = $($row.find('td')[i + 1]); // +1 skip kolom No
                    $td.addClass('ie-cell').attr('data-field', cfg.field);

                    if (cfg.type === 'checkbox') {
                        $td.data('val', data[cfg.field]);
                        $td.html(renderView(cfg, data[cfg.field]));
                    }
                    if (cfg.type === 'select2') {
                        // Normalisasi: terima object {id,label} atau string/number plain
                        // ...raw membawa semua field dari API untuk kebutuhan compute/syncTo di client
                        const raw = data[cfg.field];
                        const val = (raw && typeof raw === 'object')
                            ? { ...raw, id: raw.id, label: raw.label ?? raw.text ?? raw.id }
                            : (raw != null ? { id: raw, label: String(raw) } : null);
                        $td.data('val', val);
                        $td.html(renderView(cfg, val));
                    }
                });
            });
        });

        // ── Inject tombol Save & Cancel ke toolbar DT ────────────
        const $btnSave = $('<button id="ie-btn-save" class="btn btn-success btn-sm" style="display: none;" title="Simpan" data-toggle="tooltip" data-bs-placement="left">' +
            '<i class="ri-save-line"></i></button>')
            .appendTo($table.closest('.card').find('.dt-buttons'));

        const $btnCancel = $('<button id="ie-btn-cancel" class="btn btn-danger btn-sm" style="display: none;" title="Batalkan Perubahan" data-toggle="tooltip" data-bs-placement="left">' +
            '<i class="ri-close-line"></i></button>')
            .appendTo($table.closest('.card').find('.dt-buttons'));

        $btnCancel.on('click', function () {
            Swal.fire({
                title: 'Batalkan Semua Perubahan?',
                text: 'Semua perubahan yang belum disimpan akan dikembalikan seperti semula.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    pending = {};
                    toggleButtons(false);
                    dt.draw(false);
                }
            });
        });

        // ── Tombol Insert ─────────────────────────────────────────
        if (add) {
            $(document).on('click', '.dt-button-insert, .btn-insert, [data-action="insert"]', function (e) {
                e.stopImmediatePropagation();
                const key = 'new-' + Date.now();
                const $tr = $('<tr class="ie-new-row">').data({ id: '', key });

                $tr.append('<td class="text-center">*</td>');

                fields.forEach(cfg => {
                    const def = cfg.value !== undefined ? cfg.value : (cfg.type === 'checkbox' ? 'N' : '');
                    const $td = $('<td class="ie-cell ie-changed">').attr('data-field', cfg.field);

                    if (cfg.inputable !== false) {
                        if (cfg.type === 'checkbox') {
                            $td.data('val', def).html(renderView(cfg, def)).addClass('text-center');
                        } else if (cfg.type === 'select2') {
                            // Di mode add, select2 tidak langsung di-init semua sekaligus.
                            // Render sebagai placeholder text dulu, baru init saat diklik/Tab masuk.
                            $td.data('val', null).data('orig', null)
                               .html(`<span class="ie-select2-placeholder text-muted">${cfg.select2?.placeholder ?? ''}</span>`);
                        } else {
                            const $input = makeInput(cfg, def);
                            $td.data('orig', def).append($input);
                        }
                    }

                    $tr.append($td);

                    const $last_td = $tr.find('td:last');
                    const $last_th = $table.find('thead tr:last th').eq($last_td.index());
                    const $th_attr = $last_th.attr('class');
                    if ($th_attr) {
                        const th_class = $th_attr.replace(/sorting/g, "");
                        $last_td.addClass(th_class);
                    }

                    trackChange($tr, cfg.field, def);
                });

                $table.find('tbody').prepend($tr);

                // Fokus ke input text/number pertama saja
                $tr.find('input[type="text"], input[type="number"]').first().focus();
            });
        }

        // ── Klik cell → masuk edit mode ───────────────────────────
        $table.on('click', 'td.ie-cell', function () {
            const $td = $(this);
            const $row = $td.closest('tr');

            if (!edit && !$row.hasClass('ie-new-row')) return;

            const field = $td.attr('data-field');
            const cfg = cfgOf(field);

            if ((cfg.editable === false && !$row.hasClass('ie-new-row')) || cfg.inputable === false) return;

            // ── CHECKBOX: toggle langsung ─────────────────────
            if (cfg.type === 'checkbox') {
                const cur = $td.data('val') === 'Y' ? 'N' : 'Y';

                if (cfg.exclusive && cur === 'Y') {
                    $table.find(`tbody td.ie-cell[data-field="${field}"]`).each(function () {
                        if ($(this).data('val') === 'Y') {
                            $(this).data('val', 'N').html(iconFlag('N')).addClass('ie-changed');
                            trackChange($(this).closest('tr'), field, 'N');
                        }
                    });
                }

                $td.data('val', cur).html(iconFlag(cur)).addClass('ie-changed');
                trackChange($row, field, cur);
                return;
            }

            // Input sudah terbuka → skip
            if ($td.hasClass('ie-editing')) return;
            $td.addClass('ie-editing');

            // ── SELECT2 ───────────────────────────────────────
            if (cfg.type === 'select2') {
                const cur = $td.data('val');
                const $select = makeInput(cfg, cur);
                $td.data('orig', cur).empty().append($select);

                if (typeof initSelect2 === 'function') {
                    initSelect2($select[0]);
                }

                setTimeout(() => $select.select2('open'), 0);
                return;
            }

            // ── TEXT / NUMBER / DATE / TIME / DATETIME ────────
            const raw = $td.text().trim();
            $td.data('orig', raw).empty().append(makeInput(cfg, raw));
            const $input = $td.find('input').focus();

            let tmp = $input.val();
            $input.val('').val(tmp);
        });

        // ── Blur input text/number/date/time ─────────────────────
        $table.on('blur', 'td.ie-cell input:not([type="checkbox"])', function () {
            const $td   = $(this).closest('td');
            const $row  = $td.closest('tr');
            const field = $td.attr('data-field');
            let   val   = $(this).val();
            const orig  = $td.data('orig');

            // Jika blur dipicu oleh ESC, lewati — ESC handler sudah revert sendiri
            if ($td.data('ie-escaping')) {
                $td.removeData('ie-escaping');
                return;
            }

            if ($(this).hasClass('input-number')) {
                val = $.inputNumber.format(val);
            }

            setTimeout(function () {
                $td.removeClass('ie-editing').text(val);

                if (val !== orig || $row.hasClass('ie-new-row')) {
                    $td.addClass('ie-changed');
                    trackChange($row, field, val);
                }

                runCompute($row);
            }, 0);
        });

        // ── Select2: pilih nilai → simpan val, tandai flag ────────
        // Destroy dan render view dilakukan di select2:close
        $table.on('select2:select', 'td.ie-cell select', function (e) {
            const $td   = $(this).closest('td');
            const $row  = $td.closest('tr');
            const field = $td.attr('data-field');
            const cfg   = cfgOf(field);

            let val = {};
            if (e.params.data.id === '__empty__') {
                val = { id: '', label: '' };
            } else {
                const raw2 = e.params.data;
                // ...raw2 membawa semua field dari API untuk kebutuhan compute/syncTo di client
                val = { ...raw2, id: raw2.id, label: cfg.select2?.labelFn ? cfg.select2.labelFn(raw2) : raw2.text };
            }

            // Simpan val & tandai sudah memilih — destroy dilakukan di select2:close
            $td.data('ie-val-selected', val);
            $(this).data('ie-selected', true);

            $td.addClass('ie-changed');
            trackChange($row, field, val);

            // ── Sync ke field lain jika ada syncTo ────────────────
            const syncField = cfg.select2?.syncTo;
            if (syncField && val.id !== undefined) {
                const syncCfg = cfgOf(syncField);
                const $syncTd = $row.find(`td.ie-cell[data-field="${syncField}"]`);
                if ($syncTd.length && syncCfg) {
                    const syncVal = {
                        ...val,
                        label: syncCfg.select2?.labelFn ? syncCfg.select2.labelFn(val) : val.label
                    };

                    // Jika field tujuan sedang ie-editing (select2 aktif), jangan replace html —
                    // cukup update val-nya saja agar tidak merusak select2 yang sedang terbuka
                    if ($syncTd.hasClass('ie-editing')) {
                        $syncTd.data('val', syncVal);
                    } else {
                        $syncTd.data('val', syncVal).html(renderView(syncCfg, syncVal));
                    }

                    $syncTd.addClass('ie-changed');
                    trackChange($row, syncField, syncVal);
                }
            }

            runCompute($row);
        });

        // ── Select2: close → destroy & render view ────────────────
        // Selalu fired setelah select2:select, sehingga destroy cukup di sini
        $table.on('select2:close', 'td.ie-cell select', function () {
            const $select = $(this);

            setTimeout(() => {
                const didSelect  = $select.data('ie-selected');
                const isEscaping = $select.closest('td').data('ie-escaping');
                $select.removeData('ie-selected');

                const $td   = $select.closest('td');
                const $row  = $td.closest('tr');
                const field = $td.attr('data-field');
                const cfg   = cfgOf(field);

                $td.removeData('ie-escaping');

                // Destroy select2 jika masih aktif
                if ($select.hasClass('select2-hidden-accessible')) {
                    try { $select.select2('destroy'); } catch (_) {}
                }
                $td.removeClass('ie-editing');

                if (didSelect && !isEscaping) {
                    // Ambil val yang disimpan oleh select2:select lalu render
                    const val = $td.data('ie-val-selected');
                    $td.removeData('ie-val-selected');
                    $td.data('val', val).html(renderView(cfg, val));
                } else {
                    // Batal pilih (tutup tanpa pilih) atau ESC → revert ke nilai semula
                    $td.removeData('ie-val-selected');
                    const orig = $td.data('orig');

                    if ($row.hasClass('ie-new-row')) {
                        // Mode add: kembalikan ke placeholder span
                        $td.html(`<span class="ie-select2-placeholder text-muted">${cfg.select2?.placeholder ?? ''}</span>`);
                    } else {
                        $td.data('val', orig).html(renderView(cfg, orig));
                    }

                    // Jika ESC, kembalikan juga pending ke nilai asli DataTable
                    if (isEscaping) {
                        const key = $row.data('key');
                        if (key && pending[key]) {
                            const rowData = dt.row($row).data();
                            if (rowData && field in rowData) {
                                pending[key].fields[field] = rowData[field];
                            } else {
                                delete pending[key].fields[field];
                            }
                            if (rowData) {
                                const hasActualChange = Object.entries(pending[key].fields).some(([k, v]) => {
                                    return JSON.stringify(v) !== JSON.stringify(rowData[k]);
                                });
                                if (!hasActualChange) {
                                    delete pending[key];
                                }
                            }
                            toggleButtons(hasPending());
                        }
                    }
                }
            }, 0);
        });

        // ── Save semua pending ────────────────────────────────────
        let isSaving = false;

        $btnSave.on('click', function () {
            if (isSaving) return;

            const rows = Object.values(pending);

            // Validasi field required
            const requiredFields = fields.filter(f => f.required);
            for (const row of rows) {
                for (const f of requiredFields) {
                    const raw = row.fields[f.field];
                    const isEmpty = (f.type === 'select2')
                        ? !raw?.id?.toString().trim()
                        : !raw?.toString().trim();
                    if (isEmpty) {
                        Swal.fire('Peringatan', `Kolom "${f.label ?? f.field}" tidak boleh kosong.`, 'warning');
                        return;
                    }
                }
            }

            // Serialisasi: select2 → kirim id saja; input-number → unformat
            const serializedRows = rows.map(row => {
                const serializedFields = {};
                for (const [key, val] of Object.entries(row.fields)) {
                    const cfg = cfgOf(key);
                    if (cfg?.type === 'select2') {
                        serializedFields[key] = val?.id ?? val ?? '';
                    } else if (cfg?.attrs?.class?.includes('input-number') && $.fn.inputNumber) {
                        serializedFields[key] = $.inputNumber.unformat(val);
                    } else {
                        serializedFields[key] = val;
                    }
                }
                return { ...row, fields: serializedFields };
            });

            isSaving = true;
            $btnSave.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            $btnCancel.prop('disabled', true);

            const additionalData = typeof config.extraData === 'function' 
                                   ? config.extraData() 
                                   : (config.extraData || {});
            
            const payload = {
                rows: JSON.stringify(serializedRows),
                ...additionalData
            };

            $.ajax({
                url: urls.save,
                type: 'POST',
                data: payload,
                dataType: 'json',
            })
                .done(res => {
                    if (res.status !== 'success') {
                        return Swal.fire('Gagal', res.message ?? 'Gagal menyimpan.', 'error');
                    }
                    pending = {};
                    toggleButtons(false);

                    Swal.fire({
                        title: 'Berhasil',
                        text: res.message,
                        icon: 'success',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true,
                        confirmButtonText: 'OK',
                        willClose: () => {
                            dt.ajax.reload(null, false);
                        }
                    });
                })
                .fail(() => Swal.fire('Error', 'Koneksi gagal.', 'error'))
                .always(() => {
                    isSaving = false;
                    $btnSave.prop('disabled', false).html('<i class="ri-save-line"></i>');
                    $btnCancel.prop('disabled', false);
                });
        });

        // ── Guard: Paging, Filter, Sorting DT ────────────────────
        let isPrompting = false;
        let ignorePendingWarning = false;

        dt.on('preDraw.dt', function (e, settings) {
            if (hasPending() && !ignorePendingWarning) {
                if (!isPrompting) {
                    isPrompting = true;
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Peringatan',
                            text: 'Ada perubahan yang belum disimpan. Yakin ingin melanjutkan dan membuang perubahan tersebut?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Lanjutkan',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            isPrompting = false;
                            if (result.isConfirmed) {
                                pending = {};
                                toggleButtons(false);
                                ignorePendingWarning = true;
                                dt.draw(false);
                            }
                        });
                    }, 10);
                }
                return false;
            }
            ignorePendingWarning = false;
        });

        // ── Guard: navigasi browser ───────────────────────────────
        $(window).on('beforeunload.ie', () => hasPending() || undefined);

        $(document).on('click.ie', 'a[href]', function (e) {
            const href = $(this).attr('href');
            if (!hasPending() || !href || /^(#|javascript)/.test(href)) return;

            e.preventDefault();
            setTimeout(() => { $('#loading').hide(); }, 100);

            Swal.fire({
                title: 'Peringatan',
                text: 'Ada perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tinggalkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    pending = {};
                    window.location.href = href;
                }
            });
        });

        // ── Public API ────────────────────────────────────────────
        return {
            getPending: () => ({ ...pending }),
            clearPending() { pending = {}; toggleButtons(false); },
            setSaveUrl(newUrl) { urls.save = newUrl; },
            setExtraData(newData) { config.extraData = newData; },
            destroy() {
                $(window).off('beforeunload.ie');
                $(document).off('click.ie');
                $(document).off('keydown.ie-select2');
                $(document).off('keydown.ie-f2');
                $btnSave.remove();
                $btnCancel.remove();
            },
        };
    }

    return { init };

})();