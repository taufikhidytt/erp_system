<div id="flashSuccess" data-success="<?= $this->session->flashdata('success'); ?>"></div>
<div id="flashWarning" data-warning="<?= $this->session->flashdata('warning'); ?>"></div>
<div id="flashError" data-error="<?= $this->session->flashdata('error'); ?>"></div>

<div class="page-content" data-aos="zoom-in">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="" class="text-decoration-underline">
                                    <?= $breadcrumb ?>
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xxl-4 col-md-6">
                                <div class="mb-3">
                                    <label for="so_id" class="form-label">Nomor PO Customer / Sales Order</label>
                                    <span class="text-danger">*</span>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-profile-fill"></i>
                                        </span>
                                        <select name="so_id" id="so_id" class="form-select <?= form_error('so_id') ? 'is-invalid' : null; ?>"></select>
                                    </div>
                                    <div class="text-danger small"><?= form_error('so_id') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div id="result"></div>
            </div>
        </div>
    </div>
</div>

<script>
    let xhr = null;
    $(document).ready(function(){
        var flashsuccess = $('#flashSuccess').data('success');
        var flashwarning = $('#flashWarning').data('warning');
        var flasherror = $('#flashError').data('error');

        if (flashsuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: flashsuccess,
            })
        }

        if (flashwarning) {
            Swal.fire({
                icon: 'warning',
                title: 'Warning',
                text: flashwarning,
            })
        }

        if (flasherror) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: flasherror,
            })
        }

        $('#so_id').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('body'),
            minimumInputLength: 2,
            ajax: {
                url: '<?= site_url('val_mr_po/get_so_list') ?>',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.text,
                                id: item.SO_ID
                            }
                        })
                    };
                },
                cache: true
            }
        });

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
    });
    $(document).on('change','#so_id', function(){
        const val = $(this).val();
        if(val){
            if(xhr){
                xhr.abort();
            }
            $('#loading').show();
            xhr = $.ajax({
                url: '<?= site_url('val_mr_po/get_so') ?>',
                type: "POST",
                data: {
                    so_id : val
                },
                dataType: "json",
                success: function(response) {
                    $('#loading').hide();
                    $('#result').html('');
                    if(response.success){
                        $('#result').html(response.result);
                    }else{
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        })
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loading').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan',
                    })
                }
            });
        }
    });
    $(document).on('click','.btn-approve', function(){
        const id    = $(this).attr('data-id');
        const value = $(this).attr('data-value');
        if(!id){
            return false;
        }
        let message = 'Apakah yakin ingin approve data ini?';
        if(value == 'N'){
            message = 'Apakah yakin ingin mengambalikan status yang sudah diapprove?';
        }

        Swal.fire({
            title: message,
            text: '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#70bcff',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= site_url('val_mr_po/update_status') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        id: id,
                        value : value
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Sedang Memproses...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: res.message,
                                icon: 'success'
                            }).then(() => {
                                $('#so_id').trigger('change');
                            });
                        } else {
                            Swal.fire({
                                title: 'Warning!',
                                text: res.message,
                                icon: 'warning'
                            }).then(() => {
                                $('#so_id').trigger('change');
                            });
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal memproses data!', 'error');
                    }
                });
            }
        });
    });
    
</script>