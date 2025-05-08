const show_approval = async(data)=>{
    show_log_keluar(data.approval_pengamanan_posisi);
    $('#form-approval').find('[name=ess_permohonan_spbi_id]').val(data.id);
    $('#form-approval').find('[name=posisi]').val('keluar');
    $('#modal-approval').modal('show');
}

$('#modal-approval').on('hidden.bs.modal', function () {
    if( typeof table_spbi!='undefined' ) table_spbi.draw(false);
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
    data['kondisi_barang_keluar'] = $('#form-approval').find('[name=kondisi_barang_keluar]:checked').val();

    $.ajax({
        type: "POST",
        url: `${BASE_URL}spbi/pemeriksaan_keluar_admin_pamsiknilmat/simpan_approval`,
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

const show_log_keluar = (data)=>{
    let approval_pengamanan_keluar = '';
    if( ['null',null].includes(data)===false ){
        let no = 1;
        let posisi = JSON.parse(data);
        let posisi_keluar = _.filter(posisi, (o)=>{ return o.posisi=='keluar' && o.deleted_at==null;});
        for (const i of posisi_keluar) {
            approval_pengamanan_keluar += 
            `<tr>
                <td style="padding: 2px;">${no}</td>
                <td style="padding: 2px;">${i.pos_nama} | Oleh ${i.approval_nama} | Keluar pada ${moment(i.tanggal).format('DD MMMM YYYY')}, ${i.jam}</td>
            </tr>
            <tr>
                <td style="padding: 2px;"></td>
                <td style="padding: 2px;">Keterangan: ${i.keterangan}</td>
            </tr>
            <tr>
                <td style="padding: 2px;"></td>
                <td style="padding: 2px;">Kondisi barang: ${i.barang_sesuai=='2' ? 'Keluar Parsial':'Lengkap'}</td>
            </tr>`;
            no++;
        }
    } 
    $('#form-approval').find(`.approval_pengamanan_keluar`).html(approval_pengamanan_keluar);
}