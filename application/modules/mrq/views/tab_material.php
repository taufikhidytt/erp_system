<style>
    #table-material tbody tr {
        transition: background-color 0.2s ease;
    }
    #table-material tbody tr.selected {
        background-color: #e7f1ff !important;
        border-left: 4px solid #3d7bb9;
    }
    #tab-material table tbody td{
        font-family: monospace;
    }
</style>
<script>
    let tblMaterial, tblMaterialDetail;
    let selectedKey = null, selectedId = null;
    $(document).on('change','#item_finish_goods', function(){
        selectedId  = null;
        selectedKey = null;
        tblMaterial.ajax.reload();
        tblMaterialDetail.ajax.reload();
    });

    $(document).ready(function(){
        renderMaterial();
        tblMaterial = $('#table-material').DataTable({
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "info": true,
            "order": [],
            "ajax": {
                "url": $('#tab-material').data('url'),
                "data": function (d) {
                    d.item_id = $('#item_finish_goods option:selected').val(); 
                },
                "type": "POST"
            },
            "drawCallback": function(settings) {
                if (selectedKey !== null) {
                    $(`#table-material tr[data-key="${selectedKey}"]`).addClass('selected');
                }else{
                    const e = $('#table-material tbody tr').first();
                    if(e.find('td').length>1){
                        selectedId = e.attr('data-id');  
                        selectedKey = e.attr('data-key');
                        e.addClass('selected');
                        tblMaterialDetail.ajax.reload();
                    }
                }
            },
            "createdRow": function(row, data, dataIndex) {
                $(row).attr('data-id', data.bom_id);
                $(row).attr('data-key', data.document_no.replace(/[^a-zA-Z0-9]/g, ''));
                $(row).css('cursor', 'pointer');
            },
            "columns": [
                {
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "className": 'text-center',
                },
                {
                    "data": "document_no"
                },
                {
                    "data": "nama_item",
                },
                {
                    "data": "kode_item",
                    "className" : "text-center"
                },
                {
                    "data": "qty",
                    "className": 'text-end',
                },
                {
                    "data": "satuan"
                },
                {
                    "data": "unit",
                },
                {
                    "data": "code",
                },
                {
                    "data": "note",
                },
            ]
        });

        tblMaterialDetail = $('#table-material-detail').DataTable({
            "autoWidth": false,
            "searching": true,
            "processing": true,
            "serverSide": true,
            "ordering": true,
            "info": true,
            "order": [],
            "ajax": {
                "url": $('#tab-material').data('url_detail'),
                "data": function (d) {
                    d.bom_id = selectedId; 
                },
                "type": "POST"
            },
            "columns": [
                {
                    "data": "no",
                    "orderable": false,
                    "searchable": false,
                    "className": 'text-center',
                },
                {
                    "data": "document_no"
                },
                {
                    "data": "nama_item",
                },
                {
                    "data": "kode_item",
                    "className" : "text-center"
                },
                {
                    "data": "qty",
                    "className": 'text-end',
                },
                {
                    "data": "satuan"
                },
                {
                    "data": "note",
                },
            ]
        });
    });

    $(document).on('click', '#table-material tbody tr', function () {
        $('#table-material tbody tr').removeClass('selected');
        if($(this).find('td').length>1){
            $(this).addClass('selected');
            selectedId = $(this).data('id');
            selectedKey = $(this).data('key');
            tblMaterialDetail.ajax.reload();
        }
    });


    function renderMaterial(){
        $('#tab-material').html(`
            <div class="table-responsive overflow-auto mb-5" style="max-height: 450px;">
                <table class="table table-bordered table-hover table-sm" id="table-material">
                    <thead style="position: sticky; top: 0; background: #3d7bb9; z-index: 10; color:#ffff;">
                        <tr><th colspan="9">Finish Goods</th></tr>
                        <tr>
                            <th>No</th>
                            <th>No Formula</th>
                            <th>Nama Item</th>
                            <th>Kode Item</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Code</th>
                            <th>Unit</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered table-sm" id="table-material-detail">
                    <thead style="position: sticky; top: 0; background: #3d7bb9; z-index: 10; color:#ffff;">
                        <tr><th colspan="7">RAW Material</th></tr>
                        <tr>
                            <th width="30">No</th>
                            <th>No Formula</th>
                            <th>Nama Item</th>
                            <th>Kode Item</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        `);
    }
</script>