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
           // tambah url custom lain jika perlu
       },
       fields : [
           { field: 'name',         type: 'text', maxlength: 100 },
           { field: 'description',  type: 'text' },
           { field: 'qty',          type: 'number', min: 0, max: 9999, step: 1,
             attrs: { 'data-min': 1, 'data-decimal': 0, class: 'input-number' } },
           { field: 'start_date',   type: 'date' },
           { field: 'created_at',   type: 'datetime-local' },
           { field: 'open_time',    type: 'time' },
           { field: 'primary_flag', type: 'checkbox', exclusive: true },
           { field: 'active_flag',  type: 'checkbox' },
           { field: 'keterangan',  editable: false,
             compute: function(vals) {
                 // vals = snapshot semua nilai row saat ini { field: value, ... }
                 // Contoh: auto-generate teks dari field lain
                 const from = vals.from_uom?.label ?? '';
                 const to   = vals.to_uom?.label   ?? '';
                 const qty  = vals.to_qty           ?? '';
                 if (!from || !to || !qty) return '';
                 return $.inputNumber.format(1) + ' ' + from + ' = ' + $.inputNumber.format(qty) + ' ' + to;
             }
           },
           { field: 'uom_id',       type: 'select2',
             select2: {
                 url         : '/api/get_uom',   // di-fetch tiap buka
                 dataDefault : 'Y',              // param data-default
                 placeholder : 'Select Satuan',
             }
           },
           { field: 'keterangan', inputable : false,
                compute: function(vals) {
                    console.log(vals);
                    const from = vals.from_uom?.label ?? '';
                    const to   = vals.to_uom?.label   ?? '';
                    const qty  = vals.to_qty          ?? 1;
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

                if ($.fn.inputNumber && cfg.attrs?.class?.includes('input-number')) {
                    $el.inputNumber();
                }
        }

        // Terapkan custom attrs (class, data-*, dll)
        if (Object.keys(attrs).length) $el.attr(attrs);

        return $el;
    }

    // ================================================================
    //  INIT — entry point utama
    // ================================================================
    function init(config) {

        const { fields, urls, add = true, edit = true } = config;

        // Terima string selector atau instance DataTable
        const dt = typeof config.table === 'string'
            ? $(config.table).DataTable()
            : config.table;
        const $table = $(dt.table().node());
        // ── Mencegah DataTables KeyTable membajak tombol navigasi saat mengetik ──
        $table.on('keydown', 'input', function (e) {
            // 37: Left, 38: Up, 39: Right, 40: Down
            if ([37, 38, 39, 40].includes(e.keyCode)) {
                e.stopPropagation();
            }

            // 9: Tab
            if (e.keyCode === 9) {
                if ($(this).closest('tr').hasClass('ie-new-row')) {
                    // Mode Add: Stop propagasi agar KeyTable tidak menculik Tab.
                    // Jangan preventDefault, agar browser memindahkan fokus ke kolom input selanjutnya.
                    e.stopPropagation();
                } else {
                    // Mode Edit: Selesaikan edit (blur)
                    e.preventDefault();
                    $(this).blur();
                }
            }

            // 13: Enter
            if (e.keyCode === 13) {
                e.preventDefault();
                $(this).blur();
            }
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

                // Ambil dari pending dulu (sudah ada perubahan), fallback ke data di td
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

        // ── Tandai cell editable setiap draw ───────────────
        dt.on('draw', function () {
            $table.find('tbody tr').each(function () {
                const $row = $(this);
                // Lewati baris baru yang sedang ditambah (insert mode)
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
                        const raw = data[cfg.field];
                        const val = (raw && typeof raw === 'object')
                            ? { id: raw.id, label: raw.label ?? raw.text ?? raw.id }
                            : (raw != null ? { id: raw, label: String(raw) } : null);
                        $td.data('val', val);
                        $td.html(renderView(cfg, val));
                    }
                });
            });
        });

        // ── Inject tombol Save & Cancel ke toolbar DT ─────────────────────
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

                    if(cfg.inputable !== false){
                        if (cfg.type === 'checkbox') {
                            $td.data('val', def).html(renderView(cfg, def)).addClass('text-center');
                        } else {
                            const $input = makeInput(cfg, def);
                            $td.data('orig', def).append($input);

                            // Untuk select2: init setelah di-append ke DOM (dilakukan setelah prepend)
                            if (cfg.type === 'select2') {
                                $td.addClass('ie-editing');
                                $td.data('_ie_select2_init', true);
                            }
                        }
                    }
                    

                    $tr.append($td);
                    
                    $last_td = $tr.find('td:last');
                    $last_th = $table.find('thead tr:last th').eq($last_td.index());
                    $th_attr = $last_th.attr('class');
                    if($th_attr){
                        const th_class = $th_attr.replace(/sorting/g, "");
                        $last_td.addClass(th_class);
                    }

                    trackChange($tr, cfg.field, def);
                });

                $table.find('tbody').prepend($tr);

                // Inisialisasi select2 untuk baris baru setelah masuk DOM
                $tr.find('td[data-field]').each(function () {
                    const $td = $(this);
                    if ($td.data('_ie_select2_init')) {
                        $td.removeData('_ie_select2_init');
                        const $select = $td.find('select');
                        if ($select.length && typeof initSelect2 === 'function') {
                            initSelect2($select[0]);
                        }
                    }
                });

                $tr.find('input[type="text"], input[type="number"]').first().focus();
            });
        }

        // ── Klik cell → masuk edit mode ───────────────────────────
        $table.on('click', 'td.ie-cell', function () {
            const $td = $(this);
            const $row = $td.closest('tr');

            // Jika hak akses edit false, HANYA izinkan interaksi pada baris baru (insert)
            if (!edit && !$row.hasClass('ie-new-row')) return;

            const field = $td.attr('data-field');
            const cfg = cfgOf(field);

            // Jika field diset tidak bisa diedit secara spesifik (editable: false),
            if ((cfg.editable === false && !$row.hasClass('ie-new-row')) || cfg.inputable === false) return;

            // ── CHECKBOX: toggle langsung ─────────────────────
            if (cfg.type === 'checkbox') {
                const cur = $td.data('val') === 'Y' ? 'N' : 'Y';

                // exclusive: reset semua row lain ke N dulu
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

                // Inisialisasi select2 via initSelect2 dari custom.js
                if (typeof initSelect2 === 'function') {
                    initSelect2($select[0]);
                }

                // Buka dropdown langsung setelah inisialisasi
                setTimeout(() => $select.select2('open'), 0);
                return;
            }

            // ── TEXT / NUMBER / DATE / TIME / DATETIME ────────
            const raw = $td.text().trim();
            $td.data('orig', raw).empty().append(makeInput(cfg, raw));
            const $input = $td.find('input').focus();

            // Pindahkan kursor ke karakter paling akhir
            let tmp = $input.val();
            $input.val('').val(tmp);
        });

        // ── Blur input text/number/date/time ─────────────────
        $table.on('blur', 'td.ie-cell input:not([type="checkbox"])', function () {
            const $td = $(this).closest('td');
            const $row = $td.closest('tr');
            const field = $td.attr('data-field');
            let   val = $(this).val();
            const orig = $td.data('orig');

            if($(this).hasClass('input-number')){
                val = $.inputNumber.format(val);
            }

            setTimeout(function() {
                $td.removeClass('ie-editing').text(val);

                if (val !== orig || $row.hasClass('ie-new-row')) {
                    $td.addClass('ie-changed');
                    trackChange($row, field, val);
                }

                // Hitung ulang field compute setelah nilai berubah
                runCompute($row);
            }, 0);
        });

        // ── Select2: pilih nilai → catat & tutup ─────────────────
        $table.on('select2:select', 'td.ie-cell select', function (e) {
            const $td  = $(this).closest('td');
            const $row = $td.closest('tr');
            const field = $td.attr('data-field');
            let val = {};
            if (e.params.data.id === '__empty__') {
                val  = { id: '', label: '' };
            }else{
                val  = { id: e.params.data.id, label: e.params.data.text };
            }

            // Tandai sudah memilih agar select2:close tidak revert
            $(this).data('ie-selected', true);

            // Destroy select2, kembalikan ke view
            try { $select.select2('destroy'); } catch (_) {}
            $td.removeClass('ie-editing').data('val', val).html(renderView(cfgOf(field), val));
            $td.addClass('ie-changed');
            trackChange($row, field, val);

            // Hitung ulang field compute setelah pilih select2
            runCompute($row);
        });

        // ── Select2: blur / tutup tanpa memilih → revert ke view ─
        $table.on('select2:close', 'td.ie-cell select', function () {
            const $select = $(this);

            // Jika sudah memilih, select2:select yang handle — skip
            if ($select.data('ie-selected')) {
                $select.removeData('ie-selected');
                return;
            }

            // Beri jeda agar select2:select sempat jalan lebih dulu
            setTimeout(() => {
                if ($select.data('ie-selected')) {
                    $select.removeData('ie-selected');
                    return;
                }

                const $td   = $select.closest('td');
                const $row  = $td.closest('tr');
                const field = $td.attr('data-field');
                const orig  = $td.data('orig');

                // Destroy select2 lalu kembalikan ke tampilan semula
                try { $select.select2('destroy'); } catch (_) {}
                $td.removeClass('ie-editing');

                // Baris baru: tampilkan teks placeholder, baris edit: nilai semula
                if ($row.hasClass('ie-new-row')) {
                    $td.html(renderView(cfgOf(field), orig));
                } else {
                    $td.data('val', orig).html(renderView(cfgOf(field), orig));
                }
            }, 150);
        });

        // ── Save semua pending ────────────────────────────────────
        let isSaving = false;

        $btnSave.on('click', function () {
            if (isSaving) return; // Cegah double submit

            const rows = Object.values(pending);

            // Validasi field required
            const requiredFields = fields.filter(f => f.required);
            for (const row of rows) {
                for (const f of requiredFields) {
                    const raw = row.fields[f.field];
                    // select2: cek id-nya; yang lain: cek string-nya
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

            $.ajax({
                url: urls.save,
                type: 'POST',
                data: { rows: JSON.stringify(serializedRows) },
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

        // ── Guard: Paging, Filter, Sorting DT ─────────────────────
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
                return false; // Batalkan proses draw (menjaga UI tidak ter-refresh)
            }
            ignorePendingWarning = false;
        });

        // ── Guard: navigasi browser ───────────────────────────────
        $(window).on('beforeunload.ie', () => hasPending() || undefined);

        $(document).on('click.ie', 'a[href]', function (e) {
            const href = $(this).attr('href');
            if (!hasPending() || !href || /^(#|javascript)/.test(href)) return;

            e.preventDefault();
            setTimeout(() => {
                $('#loading').hide();
            }, 100);
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
            destroy() {
                $(window).off('beforeunload.ie');
                $(document).off('click.ie');
                $btnSave.remove();
                $btnCancel.remove();
            },
        };
    }

    return { init };

})();