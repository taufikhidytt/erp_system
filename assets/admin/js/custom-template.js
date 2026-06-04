// 1. Konfigurasi Default & Kustom Ekstensi DataTables
$.extend(true, $.fn.dataTable.defaults, {
    "keys": true,
    "showScrollToggle": true,
    "preDrawCallback": function(settings) {
        var api = new $.fn.dataTable.Api(settings);
        var info = api.page.info();
        settings._oldPage = info ? info.page : 0;
    },
    "initComplete": function(settings) {
        setTimeout(function() { 
            $('td div input[tabindex="0"]').attr('aria-label', 'Pilihan baris');
            syncTableHeader(settings); 
        }, 150);
    },
    "drawCallback": function(settings) {
        var api   = this.api();
        var $body = $(api.table().body());
        var info  = api.page.info();
        var oldPage = settings._oldPage !== undefined ? settings._oldPage : info.page;
        var newPage = info.page;

        if (!$('#dt-anim').length) {
            $('head').append(`<style id="dt-anim">
                .dt-next { animation: dtNext 0.25s cubic-bezier(0.25, 0.8, 0.25, 1) both; }
                .dt-prev { animation: dtPrev 0.25s cubic-bezier(0.25, 0.8, 0.25, 1) both; }
                .dt-fade { animation: dtFade 0.25s ease both; }
                @keyframes dtNext { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: none; } }
                @keyframes dtPrev { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: none; } }
                @keyframes dtFade { from { opacity: 0; } to { opacity: 1; } }
            </style>`);
        }

        $body.removeClass('dt-next dt-prev dt-fade');
        void $body[0].offsetWidth;

        if (settings._isFirstDraw !== false) {
            settings._isFirstDraw = false;
            $body.addClass('dt-fade');
        } else if (newPage > oldPage) {
            $body.addClass('dt-next');
        } else if (newPage < oldPage) {
            $body.addClass('dt-prev');
        } else {
            $body.addClass('dt-fade');
        }

        setTimeout(function() { syncTableHeader(settings); }, 100);
    }
});

$(document).ready(function() {
    $(document).on('init.dt', function(e, settings) {
        if (e.namespace !== 'dt' || settings.oInit.showScrollToggle === false || !settings.nScrollBody) return;

        const api = new $.fn.dataTable.Api(settings);
        const $wrapper = $(api.table().container());
        let isEnabled = settings.oInit.autoScrollPage === true; 
        settings.oInit.autoScrollPage = isEnabled; 

        if (!$('#dt-switch-style').length) {
            $('head').append(`
                <style id="dt-switch-style">
                    .dt-switch { position: relative; display: inline-block; width: 34px; height: 20px; margin: 0; vertical-align: middle; }
                    .dt-scroll-checkbox { opacity: 0; width: 0; height: 0; position: absolute; }
                    .dt-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .3s; border-radius: 20px; }
                    .dt-slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
                    .dt-scroll-checkbox:checked + .dt-slider { background-color: #0d6efd; }
                    .dt-scroll-checkbox:checked + .dt-slider:before { transform: translateX(14px); }
                    .dt-autoscroll-toggle { display: inline-flex; align-items: center; margin-left: 15px; user-select: none; }
                    .dt-autoscroll-label { margin-right: 8px; font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; }
                </style>
            `);
        }

        const $toggleContainer = $(`
            <div class="dt-autoscroll-toggle align-middle">
                <label class="dt-switch">
                    <input type="checkbox" id="toggle_${settings.sTableId}" class="dt-scroll-checkbox" ${isEnabled ? 'checked' : ''}>
                    <span class="dt-slider"></span>
                </label>
                <label class="dt-autoscroll-label mt-2 ms-1" for="toggle_${settings.sTableId}">Scroll Off</label>
            </div>
        `);

        $toggleContainer.find('.dt-scroll-checkbox').on('change', function() {
            $(this).closest('.dt-autoscroll-toggle').find('.dt-autoscroll-label').text('Scroll '+(this.checked?'On':'Off'));
            settings.oInit.autoScrollPage = this.checked;
        });

        $wrapper.find('.dataTables_length, .dt-length').append($toggleContainer);
    });

    $(document).on('wheel', '.dataTables_scrollBody', function(e) {
        const oe = e.originalEvent;
        if (oe.shiftKey || Math.abs(oe.deltaX) > Math.abs(oe.deltaY)) return;

        const $scrollBody = $(this);
        const $table = $scrollBody.find('table');
        const dt = $table.DataTable();

        if (!dt || dt.settings()[0].oInit.autoScrollPage !== true || $table.data('isPageChanging')) return;

        const scrollTop = $scrollBody.scrollTop();
        const isBottom = Math.ceil(scrollTop + $scrollBody.innerHeight()) >= $scrollBody[0].scrollHeight;
        const isTop = scrollTop === 0;
        const info = dt.page.info();
        
        let action = null;

        if (oe.deltaY > 0 && isBottom && info.page < info.pages - 1) action = 'next';
        else if (oe.deltaY < 0 && isTop && info.page > 0) action = 'previous';

        if (action) {
            $table.data('isPageChanging', true);
            dt.one('draw.dt', function() {
                setTimeout(function() {
                    $scrollBody.scrollTop(action === 'previous' ? $scrollBody[0].scrollHeight : 0);
                    setTimeout(() => $table.data('isPageChanging', false), 800);
                }, action === 'previous' ? 50 : 0); 
            });
            dt.page(action).draw('page');
        }
    });
});

function syncTableHeader(settings) {
    if (!settings || settings.oInit.showScrollToggle === false || !settings.nScrollBody) return;

    var api = new $.fn.dataTable.Api(settings);
    var $wrapper = $(api.table().container());

    var $scrollHead = $wrapper.find('.dataTables_scrollHead table');
    var $scrollBody = $wrapper.find('.dataTables_scrollBody table');

    var $headThs   = $scrollHead.find('thead tr');
    var $firstRow  = $scrollBody.find('tbody tr:first');
    var $bodyTds   = $firstRow.find('td');

    var isEmpty    = $bodyTds.length === 0 
                    || ($bodyTds.length === 1 && $firstRow.hasClass('odd') && $bodyTds.first().attr('colspan'));

    if (isEmpty) {
        $headThs.each(function () {
            $(this).find('th').each(function () {
                var $th = $(this);
                var w   = $th.outerWidth();
                $th.css({
                    'box-sizing': 'border-box',
                    'width'    : w + 'px',
                    'min-width': w + 'px',
                });
            });
        });
        return;
    }

    $bodyTds.each(function (k) {
        var $td_w = $(this).outerWidth();
        $headThs.each(function () {
            $(this).find('th').eq(k).css({
                'box-sizing': 'border-box',
                'width'    : $td_w + 'px',
                'min-width': $td_w + 'px',
                });
            });
        });
}

function getContrastColor(hexColor) {
    let hex = hexColor.replace('#', '');

    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }

    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;

    return (yiq >= 128) ? '#212529' : '#ffffff';
}

function hexToRgb(hexColor) {
    let hex = hexColor.replace('#', '');

    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }

    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);
    
    return {
        r: r,
        g: g,
        b: b,
        string: `${r}, ${g}, ${b}`
    };
}
// Fungsi pembantu untuk menggelapkan warna HEX secara dinamis
function adjustBrightness(hex, percent) {
    let num = parseInt(hex.replace("#",""), 16),
        amt = Math.round(2.55 * percent),
        R = (num >> 16) + amt,
        G = (num >> 8 & 0x00FF) + amt,
        B = (num & 0x0000FF) + amt;
    return "#" + (0x1000000 + (R<255?R<0?0:R:255)*0x10000 + (G<255?G<0?0:G:255)*0x100 + (B<255?B<0?0:B:255)).toString(16).slice(1);
}

// Fungsi utama untuk mengatur tema dinamis
function setDynamicTheme(primaryHex) {
    // 1. Bersihkan format hex
    let hex = primaryHex.replace('#', '');
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }

    // 2. Hitung RGB
    const r = parseInt(hex.substring(0, 2), 16);
    const g = parseInt(hex.substring(2, 4), 16);
    const b = parseInt(hex.substring(4, 6), 16);

    // 3. Hitung Warna Hover (Dikurangi kecerahannya 12%)
    const hoverHex = adjustBrightness(hex, -12);

    // 4. Hitung Kontras Teks (YIQ) untuk Primary dan Hover
    const yiqPrimary = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    const contrastColor = (yiqPrimary >= 128) ? '#212529' : '#ffffff';

    // 5. Suntikkan langsung ke CSS :root HTML
    const root = document.documentElement;
    root.style.setProperty('--app-primary', `#${hex}`);
    root.style.setProperty('--app-primary-hover', hoverHex);
    root.style.setProperty('--app-primary-contrast', contrastColor);
    root.style.setProperty('--app-primary-hover-contrast', contrastColor); // Umumnya sama
    root.style.setProperty('--app-primary-rgb', `${r}, ${g}, ${b}`);
    root.style.setProperty('--app-primary-th', `#${hex}`);
}

function deleteAllCookies() {
    const cookies = document.cookie.split(";");

    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i];
        const eqPos = cookie.indexOf("=");
        const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();
        
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/;";
        document.cookie = name + `=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; domain=${window.location.hostname};`;
    }
    
    window.location.reload();
}

$(document).on('keydown', function(e) {
    if (e.which === 32) {
        const focusedCell = $(document).find('.dataTables_wrapper table tbody td.focus');
        if (focusedCell.length > 0) {
            const chk = focusedCell.find('input[type="checkbox"]');
            if (chk.length > 0) {
                e.preventDefault();
                const currentState = chk.prop('checked');
                chk.prop('checked', !currentState);
                chk.trigger('change');
            }
        }
    }
});

// 2. Loading State, Flash Alerts & SweetAlerts
$(document).ready(function() {
    $('#loading').hide();

    $("a:not(.has-arrow):not(.page-link):not(.nav-link)").click(function() {
        $('#loading').show();
    });

    $("form").on("submit", function() {
        $('#loading').show();
    });

    const flashsuccess = $('#flashSuccess').data('success');
    const flashwarning = $('#flashWarning').data('warning');
    const flasherror = $('#flashError').data('error');
    if (flashsuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            html: `<pre>${flashsuccess}</pre>`,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: true,
            confirmButtonText: 'OK',
        })
    }

    if (flashwarning) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            html: `<pre>${flashwarning}</pre>`,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: true,
            confirmButtonText: 'OK',
        })
    }

    if (flasherror) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: `<pre>${flasherror}</pre>`,
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: true,
            confirmButtonText: 'OK',
        })
    }
});

// 3. Realtime Jam & Search Enabler
function updateJam() {
    const sekarang = new Date();
    const jam = String(sekarang.getHours()).padStart(2, '0');
    const menit = String(sekarang.getMinutes()).padStart(2, '0');
    const detik = String(sekarang.getSeconds()).padStart(2, '0');

    const waktuLengkap = `${jam}:${menit}:${detik}`;
    const jamEl = document.getElementById('jam');
    if(jamEl) jamEl.textContent = waktuLengkap;
}

updateJam();
setInterval(updateJam, 1000);

function enableDataTableSearch() {
    $('.dataTables_wrapper').each(function() {
        let wrapper = $(this);
        wrapper.find('input[type="search"]').prop('disabled', false).removeAttr('disabled');
        wrapper.find('.dataTables_filter').css('pointer-events', 'auto');
    });
}

$(document).ready(function() { enableDataTableSearch(); });
$(document).ajaxComplete(function() { enableDataTableSearch(); });
setTimeout(enableDataTableSearch, 300);
setTimeout(enableDataTableSearch, 800);

$(document).on('click','a', function(){
    setTimeout(() => { $('#loading').hide(); }, 3000);
});

// 4. AOS & Theme Toggle (Cookies)
AOS.init();

$(document).ready(function() {
    const baseUrl = config_app.url;

    window.setCookie = function(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    let currentTheme = $('body').attr('data-theme') || 'dark';

    $('#theme-toggle-btn').on('click', function() {
        currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setCookie('app-theme', currentTheme, 30);
        if (currentTheme === 'light') {
            $('#bootstrap-style').attr('href', baseUrl + 'assets/admin/css/bootstrap.min.css');
            $('#app-style').attr('href', baseUrl + 'assets/admin/css/app.min.css');
            $('#theme-icon').removeClass('ri-sun-line').addClass('ri-moon-line');
            $('body').attr('data-theme', 'light');
        } else {
            $('#bootstrap-style').attr('href', baseUrl + 'assets/admin/css/bootstrap-dark.min.css');
            $('#app-style').attr('href', baseUrl + 'assets/admin/css/app-dark.min.css');
            $('#theme-icon').removeClass('ri-moon-line').addClass('ri-sun-line');
            $('body').attr('data-theme', 'dark');
        }
    });
});
// ===== Settings Drawer Panel =====
$(function () {
    const baseUrl  = config_app.url;
    const $overlay  = $('#settings-panel-overlay');
    const $panel    = $('#settings-panel');
    const $backdrop = $('#settings-panel-backdrop');
    const $openBtn  = $('#settings-panel-btn');
    const $closeBtn = $('#settings-panel-close');

    function getCookie(name) {
        const m = document.cookie.match('(?:^|; )' + name + '=([^;]*)');
        return m ? decodeURIComponent(m[1]) : null;
    }

    // State sementara (sebelum disimpan)
    let pending = {
        theme   : getCookie('app-theme')          || 'light',
        color   : getCookie('app-primary')        || '#556ee6',
        lang    : getCookie('app-lang')           || 'id',
        sidebar : getCookie('app-sidebar-size')   || 'default',
        density : getCookie('app-table-density')  || 'normal',
        datetime: getCookie('app-show-datetime')  !== null ? getCookie('app-show-datetime') : '1',
    };

    // Buka panel
    $openBtn.on('click', function () {
        $overlay.addClass('show');
        setTimeout(() => $panel.addClass('slide-in'), 10);
    });

    // Tutup panel
    function close() {
        $panel.removeClass('slide-in');
        setTimeout(() => $overlay.removeClass('show'), 220);
    }
    $closeBtn.on('click', close);
    $backdrop.on('click', close);
    window.spClose = close;
    window.spReset = function() {
        swal.fire({
            title: 'Reset ke Default?',
            text: 'Semua pengaturan akan dikembalikan ke nilai awal.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                deleteAllCookies();
            }
        });
    };

    // --- TEMA ---
    window.spSetTheme = function (val) {
        pending.theme = val;
        $('.sp-theme-preview').removeClass('active');
        $('#sp-theme-' + val).addClass('active');

        setCookie('app-theme', val, 30);
        if (val === 'light') {
            $('#bootstrap-style').attr('href', baseUrl + 'assets/admin/css/bootstrap.min.css');
            $('#app-style').attr('href', baseUrl + 'assets/admin/css/app.min.css');
            $('#theme-icon').removeClass('ri-sun-line').addClass('ri-moon-line');
            $('body').attr('data-theme', 'light');
        } else {
            $('#bootstrap-style').attr('href', baseUrl + 'assets/admin/css/bootstrap-dark.min.css');
            $('#app-style').attr('href', baseUrl + 'assets/admin/css/app-dark.min.css');
            $('#theme-icon').removeClass('ri-moon-line').addClass('ri-sun-line');
            $('body').attr('data-theme', 'dark');
        }
    };

    // --- WARNA PRIMER (preset swatch) ---
    window.spSetColor = function (hex) {
        pending.color = hex;
        _applyColor(hex);
        $('.sp-swatch').removeClass('active');
        $('.sp-swatch[data-color="' + hex + '"]').addClass('active');
        
        // update hex input & color picker
        $('#sp-color-hex-input').val(hex);
        $('#sp-color-picker').val(hex);
        
        // reset custom swatch icon
        $('#sp-swatch-custom-icon').show();
        $('#sp-swatch-custom').css('background', '#cccccc');

        setCookie('app-primary', hex, 30);
    };

    // --- WARNA CUSTOM (dari color picker) ---
    window.spSetCustomColor = function (hex) {
        pending.color = hex;
        _applyColor(hex);
        $('.sp-swatch').removeClass('active');
        
        const $customSwatch = $('#sp-swatch-custom');
        if ($customSwatch.length) {
            $customSwatch.css('background', hex).addClass('active');
        }
        
        $('#sp-swatch-custom-icon').hide();
        $('#sp-color-hex-input').val(hex);
        $('#sp-color-picker').val(hex);

        setCookie('app-primary', hex, 30);
    };

    // --- HEX INPUT ---
    window.spOnHexInput = function (val) {
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            $('.sp-hex-preview').css('background', val);
        }
    };
    window.spOnHexBlur = function (val) {
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            spSetCustomColor(val);
        } else if (/^#[0-9a-fA-F]{3}$/.test(val)) {
            // expand 3-digit hex
            const hex6 = '#' + val[1] + val[1] + val[2] + val[2] + val[3] + val[3];
            $('#sp-color-hex-input').val(hex6);
            spSetCustomColor(hex6);
        }
    };

    function _applyColor(hex) {
        setDynamicTheme(hex);
    }

    // --- TOGGLE DATETIME ---
    window.spToggleDatetime = function (checked) {
        pending.datetime = checked ? '1' : '0';
        if(checked){
            $('#topbar-datetime').removeClass('d-none');
        }else{
            $('#topbar-datetime').addClass('d-none');
        }
        setCookie('app-show-datetime', pending.datetime, 30);
    };

    // --- FULLSCREEN ---
    window.spToggleFullscreen = function () {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            $('#sp-fs-icon').attr('class', 'ri-fullscreen-exit-line');
            $('#sp-fs-label').text('Exit Fullscreen');
        } else {
            document.exitFullscreen();
            $('#sp-fs-icon').attr('class', 'ri-fullscreen-line');
            $('#sp-fs-label').text('Fullscreen');
        }
    };
    
    $(document).on('fullscreenchange', function () {
        if (!document.fullscreenElement) {
            $('#sp-fs-icon').attr('class', 'ri-fullscreen-line');
            $('#sp-fs-label').text('Fullscreen');
        }
    });

    // --- INFORMASI (panggil fungsi existing openSheet) ---
    window.spOpenInfo = function () {
        close();
        if (typeof openSheet === 'function') openSheet();
    };

    // --- BAHASA ---
    window.spSetLang = function (code) {
        pending.lang = code;
        $('.sp-lang-opt').removeClass('empty active'); // Menjaga class tetap bersih
        $('.sp-lang-opt[data-lang="' + code + '"]').addClass('active');
    };

    // --- SIMPAN & RELOAD ---
    window.spSave = function () {
        setCookie('app-theme',          pending.theme,    30);
        setCookie('app-primary',         pending.color,    30);
        setCookie('app-lang',            pending.lang,     30);
        setCookie('app-sidebar-size',    pending.sidebar,  30);
        setCookie('app-table-density',   pending.density,  30);
        setCookie('app-show-datetime',   pending.datetime, 30);
        location.reload();
    };
});