var modal_bayar = $('#modal-bayar');
var form_bayar = $('#form-bayar');

// get data
$('#tabel_ess_hutang').on('click', '.btn-update', function(e){
    e.preventDefault();
    
    let data = new FormData();
    data.append('np', e.target.dataset.np)
    $.ajax({
        url: `${BASE_URL}cuti/pembayaran_hutang_cuti/form_bayar`,
        type: 'POST',
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: () => {
            form_bayar[0].reset();
            form_bayar.find('#div-confirm-cubes').html('');
            modal_bayar.modal('show');
            form_bayar.LoadingOverlay('show');
        },
    }).then((res) => {
        form_bayar.LoadingOverlay('hide', true);
        if(res.status==true && res.data!=null){
            form_bayar.find('[name="no_pokok"]').val(res.data.no_pokok);
            form_bayar.find('[name="hutang"]').val(res.data.hutang);
        } else alert('Data tidak ditemukan');
    }).fail((error) => {
        form_bayar.LoadingOverlay('hide', true);
        alert('Internal Server Error');
    });
});

// submit
form_bayar.on('submit', function(e){
    e.preventDefault();
    let data = new FormData(this);
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: () => {
            form_bayar.LoadingOverlay('show');
        },
    }).then((res) => {
        form_bayar.LoadingOverlay('hide', true);
        
        if(res.status==true) var type = 'success';
        else var type = 'error';
        
        Swal.fire('',res.message, type).then(function() {
            modal_bayar.modal('hide');
        });
    }).fail((error) => {
        form_bayar.LoadingOverlay('hide', true);
        alert('Internal Server Error');
    });
});

modal_bayar.on('hidden.bs.modal', function (e) {
    form_bayar[0].reset();
    form_bayar.find('#div-confirm-cubes').html('');
    load_table();
});

form_bayar.on('change', '#bayar_dari_mst_cuti_id, #pembayaran', function(e){
    e.preventDefault();
    let div_confirm_cubes = form_bayar.find('#div-confirm-cubes');
    let bayar_dari_mst_cuti_id = form_bayar.find('[name="bayar_dari_mst_cuti_id"]').val();
    let no_pokok = form_bayar.find('[name="no_pokok"]').val();
    let pembayaran = form_bayar.find('[name="pembayaran"]').val();
    if(['1','2'].includes(bayar_dari_mst_cuti_id)){
        let data = new FormData();
        data.append('bayar_dari_mst_cuti_id', bayar_dari_mst_cuti_id);
        data.append('no_pokok', no_pokok);
        data.append('pembayaran', pembayaran);
        $.ajax({
            url: `${BASE_URL}cuti/pembayaran_hutang_cuti/cek_sisa_kuota`,
            type: 'POST',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: () => {
                div_confirm_cubes.LoadingOverlay('show');
            },
        }).then((res) => {
            div_confirm_cubes.LoadingOverlay('hide', true);
            div_confirm_cubes.html(res.content);
        }).fail((error) => {
            div_confirm_cubes.LoadingOverlay('hide', true);
            alert('Internal Server Error');
        });
    } else{
        div_confirm_cubes.html('');
    }
    return;
});