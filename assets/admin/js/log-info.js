let log_info_table = null;
let log_last_btn   = null;
let log_url        = '';   

function init_log_info_table() {
    let log_columns = [
        { data: 'no',        orderable: false, searchable: false, className: 'text-center', width: '50px' },
        { data: 'tanggal',   className: 'text-center', width: '150px' },
        { data: 'user' },
        { data: 'transaksi' },
        { data: 'log' },
    ];

    log_info_table = $('#log-info-table').DataTable({
        autoWidth    : false,
        processing   : true,
        serverSide   : true,
        searching    : true,
        order        : [],
        scrollX      : true,
        scrollY      : '40dvh',
        drawCallback : function() {
            this.api().columns.adjust();
        },
        ajax: {
            url  : log_url,
            type : 'POST',
            data : function(d) {
                $.each(log_last_btn[0].attributes, function(index, attr) {
                    if (attr.name.startsWith('data-')) {
                        let key = attr.name.replace('data-', '');
                        if(['params', 'id'].includes(key)) return;
                        let value = attr.value;
                        d[key] = value;
                    }
                });
                d.params = log_last_btn ? log_last_btn.attr('data-param') : '';
                if(log_last_btn.attr('data-id')) {
                    d.id = log_last_btn.attr('data-id');
                }
            },
            dataSrc: function (json) {
                if (json.header) {
                    const header = json.header;
                    $('#log-info-modal').find('#log-created-by').text(header.user_created);
                    $('#log-info-modal').find('#log-created-at').text(header.created_date);
                    $('#log-info-modal').find('#log-updated-by').text(header.user_updated);
                    $('#log-info-modal').find('#log-updated-at').text(header.last_update_date);
                }
                return json.data;
            }
        },
        columns: log_columns,
    });
}

$(document).on('click', '.btn-log-info', function() {
    log_last_btn = $(this);
    log_last_btn.removeAttr('data-id');
    if(log_last_btn.attr('data-url')) {
        log_url = config_app.url + log_last_btn.attr('data-url');
    }else{
        log_url = config_app.url + 'dashboard/log_data';
    }
    $('#log-info-modal').modal('show');
});

$('#log-info-modal').on('shown.bs.modal', function() {
    if (log_info_table === null) {
        init_log_info_table();
    } else {
        log_info_table.ajax.url(log_url);
        log_info_table.ajax.reload();
    }
});


$(document).ready(function() {
    if ($('#dt-context-menu').length === 0) {
        $('body').append(`
            <ul id="dt-context-menu" class="dropdown-menu shadow-sm" style="display:none; position:absolute; z-index:1055; margin:0;">
                <li>
                    <a class="dropdown-item d-flex align-items-center" href="#" id="dt-menu-log-history">
                        <i class="ri-question-fill me-2 text-info"></i> Log & History
                    </a>
                </li>
            </ul>
        `);
    }
});

let dtContextMenuRow = null;

function showDtContextMenu(e, rowElem) {
    e.preventDefault();
    dtContextMenuRow = $(rowElem);

    let pageX = e.pageX;
    let pageY = e.pageY;

    if (e.type === 'touchstart' || e.type === 'touchend') {
        let touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
        pageX = touch.pageX;
        pageY = touch.pageY;
    }

    $('#dt-context-menu').css({
        top: pageY + 'px',
        left: pageX + 'px'
    }).fadeIn(150);
}

$(document).on('click touchstart', function(e) {
    if (!$(e.target).closest('#dt-context-menu').length && e.type !== 'contextmenu') {
        $('#dt-context-menu').fadeOut(100);
    }
});

$(document).on('click', '#dt-menu-log-history', function(e) {
    e.preventDefault();
    $('#dt-context-menu').hide();
    
    if (dtContextMenuRow) {
        let _table = dtContextMenuRow.closest('table').DataTable();
        let rowData = _table.row(dtContextMenuRow).data();   
        $(document).find('.btn-log-info').attr('data-id', rowData.id);
        
        log_last_btn = $(document).find('.btn-log-info');
        if(log_last_btn.attr('data-url')) {
            log_url = config_app.url + log_last_btn.attr('data-url');
        }else{
            log_url = config_app.url + 'dashboard/log_data';
        }
        $('#log-info-modal').modal('show');
    }
});

// Desktop
$(document).on('contextmenu', 'table[data-info="true"] tbody tr', function(e) {
    if ($(this).find('.dataTables_empty').length === 0) {
        showDtContextMenu(e, this);
    }
});

// Mobile
let pressTimer;
$(document).on('touchstart', 'table[data-info="true"] tbody tr', function(e) {
    let row = this;
    if ($(row).find('.dataTables_empty').length > 0) return;
    pressTimer = window.setTimeout(function() {
        showDtContextMenu(e, row);
    }, 600);
})
.on('touchend touchmove touchcancel', 'table[data-info="true"] tbody tr', function() {
    clearTimeout(pressTimer);
});