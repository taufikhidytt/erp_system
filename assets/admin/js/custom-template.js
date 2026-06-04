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

    function setCookie(name, value, days) {
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