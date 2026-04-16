const badgeStatus = (label, color = '') => {
    if (!label) return '-';

    const colors = getBadgeStyle(color);
    
    return `
        <span class="badge" style="
            font-size: 12px;width: 82px;
            background-color: ${colors.bg}; 
            color: ${colors.text};
        ">
            ${label}
        </span>`.trim();
};

const getBadgeStyle = (hexColor, opacity = 0.2) => {
    let hex = hexColor.replace("#", "");

    if (!hex) hex = "cccccc";

    let r, g, b;

    if (hex.length === 3) {
        r = parseInt(hex.substring(0, 1).repeat(2), 16);
        g = parseInt(hex.substring(1, 2).repeat(2), 16);
        b = parseInt(hex.substring(2, 3).repeat(2), 16);
    } else {
        r = parseInt(hex.substring(0, 2), 16);
        g = parseInt(hex.substring(2, 4), 16);
        b = parseInt(hex.substring(4, 6), 16);
    }

    const yiq = ((r * 299) + (g * 587) + (b * 114)) / 1000;
    const textColorAuto = (yiq >= 128) ? "#000000" : "#ffffff";

    return {
        bg: `rgba(${r}, ${g}, ${b}, ${opacity})`,
        text: hexColor,
        border: hexColor
    };
};

$(document).on('mouseenter', '[data-bs-toggle="tooltip"]', function() {
    $('.tooltip').remove();

    var tooltip = new bootstrap.Tooltip(this, {
        container: 'body',
        trigger: 'manual',
        boundary: 'viewport'
    });

    tooltip.show();

    $(this).one('mouseleave click', function() {
        tooltip.dispose();
        $('.tooltip').remove();
    });
});

// untuk resize width status
function equalizeBadgeWidth() {
    let maxWidth = 100;
    
    $('.label-status h5').css('width', 'auto');
    
    $('.label-status h5').each(function() {
        if ($(this).outerWidth() > maxWidth) {
            maxWidth = $(this).outerWidth();
        }
    });
    
    $('.label-status h5').css('width', maxWidth + 'px');
}
$(document).on('ajaxComplete', function(event, xhr, settings) {
    if (settings.url.includes('getStatus')) {
        equalizeBadgeWidth();
    }
});

function initSelect2(element) {
    const $select = $(element);
    if ($select.hasClass('select2-hidden-accessible')) return;

    const url = $select.data('url');
    const parentsAttr = $select.data('parent');
    const selectedId = $select.data('selected-id');
    const selectedText = $select.data('selected-text');
    const dropdownParent = $select.data('dropdown-parent');
    const dataDefault = $select.data('default');
    const allow_clear = $select.data('clear') === true
    const min_input_length = parseInt($select.data('min-input-length')) || 0;

    const getParentObj = (selector) => {
        selector = selector.trim();
        if (selector.startsWith('.')) {
            return $select.closest('.row-item').find(selector);
        }
        return $(selector.startsWith('#') ? selector : '#' + selector);
    };

    const placeholder_txt = `-- ${($select.attr('placeholder') || 'Select...')} --`;
    const config = {
        theme: 'bootstrap-5',
        placeholder: placeholder_txt,
        allowClear: allow_clear,
        minimumInputLength: min_input_length,
        dropdownParent: dropdownParent ? $select.closest(dropdownParent) : ($select.closest('.modal').length ? $select.closest('.modal') : $select.parent()),
        ajax: url ? {
            url: config_app.url+url,
            type: ($select.data('method') || 'GET').toUpperCase(),
            dataType: 'json',
            delay: 250,
            data: function(params) {
                const req = { q: params.term};
                if (parentsAttr) {
                    parentsAttr.split(',').forEach(p => {
                        const key = p.trim().replace(/[.#]/g, '');
                        req[key] = getParentObj(p).val();
                    });
                }
                return req;
            },
            processResults: function(data) {
                const results = data.results || data || [];
                
                results.unshift({ id: '__empty__', text: placeholder_txt });
                
                return { results };
            },
            cache: true
        } : null
    };

    $select.select2(config);
    $select.on('select2:select', function(e) {
        const data = e.params.data;
        const $option = $(data.element);
        
        // Jika pilih placeholder, reset value ke kosong
        if (data.id === '__empty__') {
            $select.val(null).trigger('change');
            return;
        }

        const fullData = $.extend({}, $option.data(), data);

        const ignoredKeys = ['element', 'selected', 'disabled', '_resultId','id','text'];

        $.each(fullData, function(key, value) {
            if (!ignoredKeys.includes(key) && typeof value !== 'object') {
                const attrName = key.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
                
                $select.find('option:selected').attr('data-' + attrName, value);
            }
        });
        $select.data('_select2_selecting', true);
        // $select.trigger('change');
    });

    $select.on('select2:unselect', function() {
        const currentData = $select.data();
        $.each(currentData, function(key) {
            if (key !== 'select2') $select.removeAttr('data-' + key.toLowerCase());
        });
    });

    // --- HANDLE EDIT MODE ---
    if (selectedId) {
        if (selectedText) {
            const newOption = new Option(selectedText, selectedId, true, true);
            $select.append(newOption).trigger('change');
        } else if (url) {
            $.ajax({ url: config_app.url+url, data: { id: selectedId }, dataType: 'json' }).done(function(data) {
                const item = Array.isArray(data) ? data[0] : data;
                if (item) {
                    const newOption = new Option(item.text, item.id, true, true);
                    $select.append(newOption).trigger('change');
                    $select.trigger({ type: 'select2:select', params: { data: item } });
                }
            });
        }
    }else if(dataDefault){
        $.ajax({ url: config_app.url+url, data: { default: dataDefault }, dataType: 'json' }).done(function(data) {
            const item = Array.isArray(data) ? data[0] : data;
            if (item) {
                const newOption = new Option(item.text, item.id, true, true);
                $select.append(newOption).trigger('change');
                $select.trigger({ type: 'select2:select', params: { data: item } });
            }
        });
    }

    if (parentsAttr) {
        const parentList = parentsAttr.split(',');
        parentList.forEach(p => {
            const $parentObj = getParentObj(p);
            $parentObj.on('change', function() {
                $select.val(null).trigger('change');
                let allFilled = true;
                parentList.forEach(sp => { if (!getParentObj(sp).val()) allFilled = false; });
                $select.prop('disabled', !allFilled);
            });
        });
        let initialFilled = true;
        parentList.forEach(p => { if (!getParentObj(p).val()) initialFilled = false; });
        $select.prop('disabled', !initialFilled);
    }
}

$(document).ready(function(){
    if($('select').hasClass('select2')){
        $.each($('.select2'), function(){
            initSelect2(this);
        })
    }
});