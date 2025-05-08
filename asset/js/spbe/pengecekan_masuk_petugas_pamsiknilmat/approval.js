const show_approval = async(data)=>{
    await get_data_barang(data.id);
    show_list_barang();
    $('#form-approval').find('[name=ess_permohonan_spbe_id]').val(data.id);
    $('#form-approval').find('[name=posisi]').val('masuk');
    $('#modal-approval').modal('show');
}

$('#modal-approval').on('hidden.bs.modal', function () {
    if( typeof table_spbe!='undefined' ) table_spbe.draw(false);
    $('#form-approval')[0].reset();
    $('#form-approval').find('[name=pos_id]').trigger('change');
});

$('#form-approval').find('[name=pos_id]').on('change', (e)=>{
    let find = _.find(ref_mst_pos, (o)=>{ return o.id==e.target.value; });
    if( typeof find!='undefined' ) $('#form-approval').find('[name=pos_nama]').val(find.nama);
    else $('#form-approval').find('[name=pos_nama]').val('');
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

    // kondisi barangnya
    let kondisi_barang = [];
    for (let i = 0; i < $('#modal-approval').find(`.barang`).length; i++) {
        const groupInput = $($('#modal-approval').find(`.barang`)[i]);

        const row = {
            ess_permohonan_spbe_barang_id: groupInput.find('.ess_permohonan_spbe_barang_id').val(),
            kondisi: groupInput.find('.kondisi').val(),
            keterangan: groupInput.find('.keterangan').val(),
        }
        kondisi_barang.push(row);
    }
    data['kondisi_barang'] = kondisi_barang;

    if( kondisi_barang.length > 0 ){
        let tidak_lengkap = _.filter(kondisi_barang, (o)=>{ return o.kondisi!=='lengkap'; });
        data['barang_sesuai'] = tidak_lengkap.length > 0 ? '2':'1';
    } else{
        data['barang_sesuai'] = '1';
    }

    $.ajax({
        type: "POST",
        url: `${BASE_URL}spbe/pengecekan_masuk_petugas_pamsiknilmat/simpan_approval`,
        data: data,
    }).then( (response)=>{
        if(response.status==true) var type = 'success';
        else var type = 'error';
        
        Swal.fire('',response.message,type).then(function() {
            $('#modal-approval').modal('hide');
        });
    }).catch((xhr, textStatus, errorThrown)=>{
        Swal.fire('',xhr.responseText,'error');
    })
}

const get_data_barang = async(spbe_id)=>{
    data_barang_temp = [];
    let req = await $.ajax({
        type: "GET",
        url: `${BASE_URL}spbe/spbe_proses/get_data_barang`,
        data: { spbe_id: spbe_id },
    });
    return data_barang_temp = req;
}

const show_list_barang = async()=>{
    let barang_text = '';
    for (const i of data_barang_temp) {
        barang_text += `
            <tr>
                <td>${i.jumlah}</td>
                <td>${i.nama_barang}</td>
                <td>${i.keterangan}</td>
                <td>
                    <input class="ess_permohonan_spbe_barang_id" type="hidden" value="${i.id}">
                    <select class="form-control kondisi" style="width: 100%">
                        <option value="lengkap">Lengkap</option>
                        <option value="kurang">Kurang</option>
                        <option value="lebih">Lebih</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </td>
                <td>
                    <textarea class="form-control keterangan" style="max-width: 100%"></textarea>
                </td>
            </tr>
        `;
    }
    $('#modal-approval').find(`.barang`).html(barang_text);
}