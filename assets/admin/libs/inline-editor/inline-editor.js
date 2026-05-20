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
           { field: 'uom_id',       type: 'select2',
             select2: {
                 url         : '/api/get_uom',   // di-fetch tiap buka
                 dataDefault : 'Y',              // param data-default
                 placeholder : 'Select Satuan',
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
        if (cfg.type === 'checkbox') return iconFlag(val);
        if (cfg.type === 'select2') return val?.label ?? val?.id ?? val ?? '';
        return val ?? '';
    }

    // ── Inisialisasi Select2 ──────────────────────────────────────
    function initSelect2($td, cfg, curVal) {
        const $select = $td.find('select');
        $select.select2({
            width: '100%',
            dropdownParent: $td,
            placeholder: cfg.select2?.placeholder ?? 'Select...',
            ajax: cfg.select2?.url ? {
                url: cfg.select2.url,
                dataType: 'json',
                delay: 250,
                data: params => ({ q: params.term }),
                processResults: data => ({ results: data })
            } : undefined
        });
        if (curVal && curVal.id) {
            const option = new Option(curVal.label, curVal.id, true, true);
            $select.append(option).trigger('change');
        }
        setTimeout(() => $select.select2('open'), 100);
    }

    // ── Buat elemen input sesuai type ─────────────────────────────
    function makeInput(cfg, val) {
        const { field, type, maxlength, attrs = {} } = cfg;
        let $el;

        switch (type) {
            case 'checkbox':
                return $('<input type="checkbox">')
                    .attr('data-field', field)
                    .prop('checked', val === 'Y');

            case 'select2':
                // Wrapper div — select2 di-init setelah masuk DOM
                $el = $('<select>').attr({
                    'data-field': field,
                    'data-url': cfg.select2.url,
                    'data-default': cfg.select2.dataDefault ?? '',
                    placeholder: cfg.select2.placeholder ?? '',
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
                        $td.data('val', data[cfg.field]);
                        $td.html(renderView(cfg, data[cfg.field]));
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

                    if (cfg.type === 'checkbox') {
                        $td.data('val', def).html(renderView(cfg, def)).addClass('text-center');
                    } else {
                        $td.append(makeInput(cfg, def));
                    }

                    $tr.append($td);
                    trackChange($tr, cfg.field, def);

                    // Init select2 jika ada
                    if (cfg.type === 'select2') initSelect2($td, cfg, def);
                });

                $table.find('tbody').prepend($tr);

                // Init input-number jika ada
                if ($.fn.inputNumber) $tr.find('.input-number').inputNumber();

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
            // HANYA izinkan interaksi pada baris baru (insert)
            if (cfg.editable === false && !$row.hasClass('ie-new-row')) return;

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
                $td.data('orig', cur).empty().append(makeInput(cfg, cur));
                initSelect2($td, cfg, cur);
                return;
            }

            // ── TEXT / NUMBER / DATE / TIME / DATETIME ────────
            const raw = $td.text().trim();
            $td.data('orig', raw).empty().append(makeInput(cfg, raw));
            const $input = $td.find('input').focus();

            // Pindahkan kursor ke karakter paling akhir
            const tmp = $input.val();
            $input.val('').val(tmp);

            // Init input-number jika class ada
            if ($.fn.inputNumber && cfg.attrs?.class?.includes('input-number')) {
                $input.inputNumber();
            }
        });

        // ── Blur input text/number/date/time ─────────────────
        $table.on('blur', 'td.ie-cell input:not([type="checkbox"])', function () {
            const $td = $(this).closest('td');
            const $row = $td.closest('tr');
            const field = $td.attr('data-field');
            const val = $(this).val();
            const orig = $td.data('orig');

            $td.removeClass('ie-editing').text(val);

            if (val !== orig || $row.hasClass('ie-new-row')) {
                $td.addClass('ie-changed');
                trackChange($row, field, val);
            }
        });

        // ── Select2 change → tutup & catat ───────────────────
        $table.on('select2:select', 'td.ie-cell select', function (e) {
            const $td = $(this).closest('td');
            const $row = $td.closest('tr');
            const field = $td.attr('data-field');
            const val = { id: e.params.data.id, label: e.params.data.text };

            // Destroy select2, kembalikan ke view
            $(this).select2('destroy');
            $td.removeClass('ie-editing').data('val', val).html(renderView(cfgOf(field), val));
            $td.addClass('ie-changed');
            trackChange($row, field, val);
        });

        // ── Save semua pending ────────────────────────────────────
        let isSaving = false;

        $btnSave.on('click', function () {
            if (isSaving) return; // Cegah double submit

            const rows = Object.values(pending);

            // Validasi field required (type text dengan required:true)
            const requiredFields = fields.filter(f => f.required);
            for (const row of rows) {
                for (const f of requiredFields) {
                    if (!row.fields[f.field]?.toString().trim()) {
                        Swal.fire('Peringatan', `Kolom "${f.label ?? f.field}" tidak boleh kosong.`, 'warning');
                        return;
                    }
                }
            }

            isSaving = true;
            $btnSave.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            $btnCancel.prop('disabled', true);

            $.ajax({
                url: urls.save,
                type: 'POST',
                data: { rows: JSON.stringify(rows) },
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
