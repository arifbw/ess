const show_approval_atasan = async(data)=>{
    $('#form-approval').find('[name=id]').val(data.id);
    $('#modal-approval-atasan').modal('show');
}

$('#modal-approval-atasan').on('hidden.bs.modal', function () {
    if( typeof table_spbi!='undefined' ) table_spbi.draw(false);
    $('#form-approval')[0].reset();
    $('#form-approval').find('[name=approval_atasan_status]').trigger('change');
});

$('#form-approval').find('[name=approval_atasan_status]').on('change', (e)=>{
    switch (e.target.value) {
        case '1':
            $('#form-approval').find('.div-keterangan').hide();
            $('#form-approval').find('[name=approval_atasan_keterangan]').prop('required',false);
            break;
        default:
            $('#form-approval').find('.div-keterangan').show();
            $('#form-approval').find('[name=approval_atasan_keterangan]').prop('required',true);
            break;
    }
})

$('#form-approval').on('submit', ()=>{
    Swal.fire({
        title: 'Konfirmasi',
        text: "Sebelum submit pastikan isian Anda sudah benar",
        icon: 'warning',
        allowOutsideClick: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Tidak'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                text: "Data sedang diproses...",
                allowOutsideClick: false,
                showConfirmButton: false
            })
            do_submit();
        }
    })
})

const do_submit = async()=>{
    let data = {};
    let form_data = $('#form-approval').serializeArray();
    for (const i of form_data) {
        data[i.name] = i.value;
    }

    $.ajax({
        type: "POST",
        url: `${BASE_URL}spbi/persetujuan_spbi/simpan_approval_atasan`,
        data: data,
    }).then( (response)=>{
        if(response.status==true) var type = 'success';
        else var type = 'error';
        
        Swal.fire('',response.message,type).then(function() {
            $('#modal-approval-atasan').modal('hide');
        });
    }).catch((xhr, textStatus, errorThrown)=>{
        Swal.fire('',xhr.responseText,'error');
    })
}