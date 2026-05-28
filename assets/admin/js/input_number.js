/**
 * jQuery InputNumber Plugin
 * ============================================================
 * Plugin jQuery untuk format angka ribuan & desimal pada input.
 *
 * CARA PAKAI:
 *   $('.input-number').inputNumber();
 *   $('.input-number').inputNumber({ decimal: 0 });
 *   $('#price').inputNumber({ decimal: 2, thousand: '.', decimalSep: ',' });
 *
 * OPTIONS (per elemen atau via data-* attribute):
 *   decimal      {number}  Jumlah digit desimal       (default: 2)
 *   thousand     {string}  Separator ribuan           (default: ',')
 *   decimalSep   {string}  Separator desimal          (default: '.')
 *   min          {number}  Nilai minimum (opsional)
 *   max          {number}  Nilai maksimum (opsional)
 *   allowNegative {bool}   Izinkan nilai negatif      (default: false)
 *
 * DATA ATTRIBUTES (bisa override options per elemen):
 *   data-decimal="0"
 *   data-thousand="."
 *   data-decimal-sep=","
 *   data-min="0"
 *   data-max="1000000"
 *   data-allow-negative
 *
 * UTILITY:
 *   $.inputNumber.format(1234567.89, 2)          → "1,234,567.89"
 *   $.inputNumber.unformat('1,234,567.89')        → 1234567.89
 *   $('#price').inputNumber('getValue')           → 1234567.89
 *   $('#price').inputNumber('setValue', 5000)     → set & format otomatis
 * ============================================================
 */

; (function ($) {

    //setup dari aplikasi
    const setup_decimal = parseInt(config_app?.decimal) ?? 2;

    // --- Default Config ---
    var DEFAULTS = {
        decimal: setup_decimal,
        thousand: ',',
        decimalSep: '.',
        min: null,
        max: null,
        allowNegative: false,
    };

    // Flag agar event delegation hanya didaftarkan sekali
    var _delegationReady = false;

    // -------------------------------------------------------
    // PRIVATE HELPERS
    // -------------------------------------------------------

    function _escape(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function _getConfig($el, overrides) {
        var dataOpts = {
            decimal: $el.data('decimal'),
            thousand: $el.data('thousand'),
            decimalSep: $el.data('decimal-sep'),
            min: $el.data('min'),
            max: $el.data('max'),
            allowNegative: $el.is('[data-allow-negative]') || undefined,
        };

        // Bersihkan undefined agar tidak override
        $.each(dataOpts, function (k, v) {
            if (v === undefined) delete dataOpts[k];
        });

        return $.extend({}, DEFAULTS, overrides || {}, dataOpts);
    }

    function _format(value, decimal, thousand, decimalSep) {
        var num = _unformat(value, thousand, decimalSep);
        if (num === null) return '';
        if (isNaN(num)) num = 0;

        var parts = num.toFixed(decimal).split('.');

        // Pisahkan tanda minus dahulu agar regex \B tidak salah posisi
        // pada angka negatif, misal "-1234567" → regex bisa menghasilkan ",-1234,567"
        var isNeg = parts[0].charAt(0) === '-';
        var intPart = isNeg ? parts[0].slice(1) : parts[0];
        intPart = intPart.replace(new RegExp('\\B(?=(\\d{3})+(?!\\d))', 'g'), thousand);
        parts[0] = isNeg ? '-' + intPart : intPart;

        return decimal > 0 ? parts.join(decimalSep) : parts[0];
    }

    function _unformat(value, thousand, decimalSep) {
        thousand = thousand || DEFAULTS.thousand;
        decimalSep = decimalSep || DEFAULTS.decimalSep;

        if (value === null || value === undefined || value === '') return null;
        if (typeof value === 'number') return value;

        var clean = value.toString()
            .replace(new RegExp(_escape(thousand), 'g'), '')
            .replace(new RegExp(_escape(decimalSep), 'g'), '.');

        var result = parseFloat(clean);
        return isNaN(result) ? 0 : result;
    }

    function _clamp(num, min, max) {
        if (num === null) return null;
        if (min !== null && min !== undefined && num < min) return min;
        if (max !== null && max !== undefined && num > max) return max;
        return num;
    }

    function _allowedPattern(cfg) {
        var dec = _escape(cfg.decimalSep);
        var neg = cfg.allowNegative ? '\\-?' : '';
        var dPart = cfg.decimal > 0 ? '(' + dec + '\\d{0,' + cfg.decimal + '})?' : '';
        return new RegExp('^' + neg + '\\d*' + dPart + '$');
    }

    // -------------------------------------------------------
    // EVENT HANDLERS
    // -------------------------------------------------------

    function _onFocus() {
        var $el = $(this);
        var cfg = $el.data('inputNumber.cfg');
        if (!cfg) return;

        var raw = _unformat($el.val(), cfg.thousand, cfg.decimalSep);
        var val = (raw === null) ? '' : raw.toString().replace('.', cfg.decimalSep);

        $el.val(val);

        // Simpan sebagai lastValid agar jika user mengetik karakter invalid
        // (misal huruf) saat baru focus, nilainya tidak hilang/menjadi kosong.
        $el.data('inputNumber.lastValid', val);
    }

    function _onInput() {
        var $el = $(this);
        var cfg = $el.data('inputNumber.cfg');
        if (!cfg) return;

        var val = $el.val();
        var lastValid = $el.data('inputNumber.lastValid') || '';
        var pattern = _allowedPattern(cfg);

        // -------------------------------------------------------
        // LOGIKA TOGGLE TANDA MINUS (hanya jika allowNegative: true)
        // -------------------------------------------------------
        // Deteksi: user mengetik "-" di posisi mana saja.
        // Caranya: bandingkan jumlah "-" di val vs lastValid.
        // Jika val punya 1 lebih banyak "-" dari lastValid → user baru ketik "-".
        if (cfg.allowNegative) {
            var minusInVal      = (val.match(/-/g) || []).length;
            var minusInLast     = (lastValid.match(/-/g) || []).length;
            var userTypedMinus  = minusInVal > minusInLast;

            if (userTypedMinus) {
                // Ambil nilai numerik dari lastValid (kondisi sebelum user ketik "-")
                var num = _unformat(lastValid, cfg.thousand, cfg.decimalSep);

                if (num === null || num === 0 || lastValid === '') {
                    // Belum ada angka → izinkan "-" saja sebagai awalan
                    $el.val('-');
                    $el.data('inputNumber.lastValid', '-');
                } else {
                    // Toggle sign: positif → negatif, negatif → positif
                    var toggled = (-num).toString().replace('.', cfg.decimalSep);
                    $el.val(toggled);
                    $el.data('inputNumber.lastValid', toggled);
                }
                return;
            }
        }

        // Karakter "-" saat allowNegative false → tolak
        // atau karakter lain yang tidak sesuai pattern → kembalikan ke lastValid
        if (val !== '' && !pattern.test(val)) {
            $el.val(lastValid);
            return;
        }

        $el.data('inputNumber.lastValid', val);
    }

    function _onBlur() {
        var $el = $(this);
        var cfg = $el.data('inputNumber.cfg');
        if (!cfg) return;

        var val = $el.val();
        if (val === '' || val === '-') return;

        var num = _unformat(val, cfg.thousand, cfg.decimalSep);
        num = _clamp(num, cfg.min, cfg.max);
        $el.val(_format(num, cfg.decimal, cfg.thousand, cfg.decimalSep));
        $el.data('inputNumber.lastValid', null);
    }

    // -------------------------------------------------------
    // SETUP DELEGATION (dipanggil sekali saat plugin pertama dipakai)
    // -------------------------------------------------------

    function _setupDelegation() {
        if (_delegationReady) return;
        _delegationReady = true;

        $(document)
            .on('focus.inputNumber', '[data-input-number-init]', _onFocus)
            .on('input.inputNumber', '[data-input-number-init]', _onInput)
            .on('blur.inputNumber', '[data-input-number-init]', _onBlur);
    }

    // -------------------------------------------------------
    // FORMAT NILAI AWAL ELEMEN
    // -------------------------------------------------------

    function _formatInitial($el) {
        var cfg = $el.data('inputNumber.cfg');
        var val = $el.val();
        if (val !== '') {
            // Gunakan _unformat agar bisa handle nilai mentah (dari DB) maupun
            // nilai yang sudah terformat (mis. "20,000.00"), sehingga init ulang
            // tidak akan mem-truncate angka karena parseFloat berhenti di koma.
            var num = _unformat(val, cfg.thousand, cfg.decimalSep);
            if (num !== null && !isNaN(num)) {
                $el.val(_format(num, cfg.decimal, cfg.thousand, cfg.decimalSep));
            }
        }
    }

    // -------------------------------------------------------
    // JQUERY PLUGIN
    // -------------------------------------------------------

    $.fn.inputNumber = function (method, arg) {

        // -- Method: getValue --
        if (method === 'getValue') {
            var $first = this.first();
            var cfg = $first.data('inputNumber.cfg') || DEFAULTS;
            return _unformat($first.val(), cfg.thousand, cfg.decimalSep);
        }

        // -- Method: setValue --
        if (method === 'setValue') {
            return this.each(function () {
                var $el = $(this);
                var cfg = $el.data('inputNumber.cfg') || DEFAULTS;
                var num = _unformat(arg, cfg.thousand, cfg.decimalSep);
                $el.val(_format(num, cfg.decimal, cfg.thousand, cfg.decimalSep));
            });
        }

        // -- Method: destroy --
        if (method === 'destroy') {
            return this.each(function () {
                $(this)
                    .removeData('inputNumber.cfg')
                    .removeData('inputNumber.lastValid')
                    .removeAttr('data-input-number-init');
            });
        }

        // -- Init (options object atau tanpa argument) --
        var options = (typeof method === 'object') ? method : {};

        _setupDelegation();

        return this.each(function () {
            var $el = $(this);
            var cfg = _getConfig($el, options);

            // Simpan config di elemen
            $el.data('inputNumber.cfg', cfg);

            // Tandai elemen agar delegation bisa mengenalinya
            $el.attr('data-input-number-init', '');

            // Format nilai awal jika sudah ada
            _formatInitial($el);
        });
    };

    // -------------------------------------------------------
    // STATIC UTILITY: $.inputNumber
    // -------------------------------------------------------

    $.inputNumber = {
        /**
         * Format angka ke string
         * @param {number|string} value
         * @param {number} [decimal=2]
         * @param {string} [thousand=',']
         * @param {string} [decimalSep='.']
         */
        format: function (value, decimal, thousand, decimalSep) {
            return _format(
                value,
                decimal !== undefined ? decimal : DEFAULTS.decimal,
                thousand !== undefined ? thousand : DEFAULTS.thousand,
                decimalSep !== undefined ? decimalSep : DEFAULTS.decimalSep
            );
        },

        /**
         * Kembalikan string format ke number murni
         * @param {string|number} value
         * @param {string} [thousand=',']
         * @param {string} [decimalSep='.']
         */
        unformat: function (value, thousand, decimalSep) {
            return _unformat(value, thousand, decimalSep);
        },

        /** Ubah defaults global */
        setDefaults: function (opts) {
            $.extend(DEFAULTS, opts);
        },
    };

}(jQuery));


// ============================================================
// AUTO-INIT
// ============================================================
// Aktif otomatis untuk elemen dengan class .input-number
// atau attribute [data-input-number] saat DOM ready.
// ============================================================

$(function () {
    $('[data-input-number], .input-number').inputNumber();
});