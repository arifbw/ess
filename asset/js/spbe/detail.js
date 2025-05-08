const show_detail = async(e)=>{
    let data = await get_detail_spbe(e.id);
    $('#modal-detail').find('.div-approval-kasek').hide();
    $('#modal-detail').find('.div-lampiran').hide();
    $('#modal-detail').modal('show');
    for (const [key,value] of Object.entries(data)) {
        if(['barang','pos_keluar','pos_masuk','approval_kasek_status','approval_atasan_status','pengecek1_status','konfirmasi_pengguna','danposko_status','approval_pengamanan_posisi','konfirmasi_pembawa_status'].includes(key)===false) $('#modal-detail').find(`.${key}`).text(value);
    }
    
    if (data.akses_lampiran == true) {
        $('#modal-detail').find('.div-lampiran').show();
        let lampiran = data.lampiran;
        var data_lampiran = '';
        if(lampiran.length){
            $.each(lampiran, function(index, item) {
                if(item.path_file!=null){
                    var pdfUrl = BASE_URL + item.path_file;
                    data_lampiran += `
                    <embed src="` + pdfUrl + `" style="margin-right:10px;margin-top:10px;" type="application/pdf" width="100%" height="500" />
                    `;
                }
            });
        } else data_lampiran = 'Tidak Ada Lampiran';
        $('.list-lampiran').html(data_lampiran);
    }

    $('#modal-detail').find(`.keluar_tanggal`).html(moment(data.keluar_tanggal).format('DD MMMM YYYY'));

    let barang_text = '';
    if( data.barang!=null ){
        let barang = JSON.parse(data.barang);
        for (const i of barang) {
            barang_text += `
                <tr>
                    <td>${i.jumlah}</td>
                    <td>${i.nama_barang}</td>
                    <td>${i.keterangan}</td>
                </tr>
            `;
        }
    }
    $('#modal-detail').find(`.barang`).html(barang_text);

    let pos_keluar_text = '';
    if( ['null',null].includes(data.pos_keluar)===false ){
        let pos_keluar = JSON.parse(data.pos_keluar);
        let pos_keluar_mapped = _.map(pos_keluar, 'nama');
        pos_keluar_text = pos_keluar_mapped.join(', ');
    }
    $('#modal-detail').find(`.pos_keluar`).html(pos_keluar_text);

    let pos_masuk_text = '';
    if( ['null',null].includes(data.pos_masuk)===false ){
        let pos_masuk = JSON.parse(data.pos_masuk);
        let pos_masuk_mapped = _.map(pos_masuk, 'nama');
        pos_masuk_text = pos_masuk_mapped.join(', ');
    }
    $('#modal-detail').find(`.pos_masuk`).html(pos_masuk_text);

    // kasek optional
    if(data.approval_kasek_np!=null){
        let approval_kasek_status = '';
        let parent_approval_kasek_status = $('.approval_kasek_status').closest('.alert');
        parent_approval_kasek_status.removeClass();
        switch (data.approval_kasek_status) {
            case '1':
                approval_kasek_status = 'Disetujui';
                parent_approval_kasek_status.addClass('alert alert-info');
                break;
            case '2':
                approval_kasek_status = 'Ditolak';
                parent_approval_kasek_status.addClass('alert alert-danger');
                break;
            default:
                approval_kasek_status = 'Menunggu Persetujuan';
                parent_approval_kasek_status.addClass('alert alert-warning');
                break;
        }
        $('#modal-detail').find(`.approval_kasek_status`).html(`Status: ${approval_kasek_status}`);
        $('#modal-detail').find(`.approval_kasek_at`).html( data.approval_kasek_updated_at!=null ? approval_kasek_status + ' pada: '+ moment(data.approval_kasek_updated_at).format('DD MMMM YYYY HH:mm:ss') : '' );
        $('#modal-detail').find('.div-approval-kasek').show();
    }
    // END kasek optional

    let approval_atasan_status = '';
    let parent_approval_atasan_status = $('.approval_atasan_status').closest('.alert');
    parent_approval_atasan_status.removeClass();
    switch (data.approval_atasan_status) {
        case '1':
            approval_atasan_status = 'Disetujui';
            parent_approval_atasan_status.addClass('alert alert-info');
            break;
        case '2':
            approval_atasan_status = 'Ditolak';
            parent_approval_atasan_status.addClass('alert alert-danger');
            break;
        default:
            approval_atasan_status = 'Menunggu Persetujuan';
            parent_approval_atasan_status.addClass('alert alert-warning');
            break;
    }
    $('#modal-detail').find(`.approval_atasan_status`).html(`Status: ${approval_atasan_status}`);
    $('#modal-detail').find(`.approval_atasan_at`).html( data.approval_atasan_updated_at!=null ? approval_atasan_status + ' pada: '+ moment(data.approval_atasan_updated_at).format('DD MMMM YYYY HH:mm:ss') : '' );

    let pengecek1_status = '';
    let parent_pengecek1_status = $('.pengecek1_status').closest('.alert');
    parent_pengecek1_status.removeClass();
    switch (data.pengecek1_status) {
        case '1':
            pengecek1_status = 'Disetujui';
            parent_pengecek1_status.addClass('alert alert-info');
            break;
        case '2':
            pengecek1_status = 'Ditolak';
            parent_pengecek1_status.addClass('alert alert-danger');
            break;
        default:
            pengecek1_status = 'Menunggu Persetujuan';
            parent_pengecek1_status.addClass('alert alert-warning');
            break;
    }
    $('#modal-detail').find(`.pengecek1_status`).html(`Status: ${pengecek1_status}`);
    $('#modal-detail').find(`.pengecek1_at`).html( data.pengecek1_updated_at!=null ? pengecek1_status + ' pada: '+ moment(data.pengecek1_tanggal).format('DD MMMM YYYY') + ` ${data.pengecek1_jam}` : '' );

    let konfirmasi_pengguna = '';
    let parent_konfirmasi_pengguna = $('.konfirmasi_pengguna').closest('.alert');
    parent_konfirmasi_pengguna.removeClass();
    switch (data.konfirmasi_pengguna) {
        case '1':
            konfirmasi_pengguna = 'Sudah Konfirmasi';
            parent_konfirmasi_pengguna.addClass('alert alert-info');
            break;
        case '2':
            konfirmasi_pengguna = 'Ditolak';
            parent_konfirmasi_pengguna.addClass('alert alert-danger');
            break;
        default:
            konfirmasi_pengguna = 'Belum Konfirmasi';
            parent_konfirmasi_pengguna.addClass('alert alert-warning');
            break;
    }
    $('#modal-detail').find(`.konfirmasi_pengguna`).html(`Status: ${konfirmasi_pengguna}`);
    $('#modal-detail').find(`.konfirmasi_pengguna_at`).html( data.konfirmasi_pengguna_at!=null ? konfirmasi_pengguna + ' pada: '+ moment(data.konfirmasi_pengguna_tanggal).format('DD MMMM YYYY') + ` ${data.konfirmasi_pengguna_jam}` : '' );

    let danposko_status = '';
    let parent_danposko_status = $('.danposko_status').closest('.alert');
    parent_danposko_status.removeClass();
    switch (data.danposko_status) {
        case '1':
            danposko_status = 'Disetujui';
            parent_danposko_status.addClass('alert alert-info');
            break;
        case '2':
            danposko_status = 'Ditolak';
            parent_danposko_status.addClass('alert alert-danger');
            break;
        default:
            danposko_status = 'Menunggu Persetujuan';
            parent_danposko_status.addClass('alert alert-warning');
            break;
    }
    $('#modal-detail').find(`.danposko_status`).html(`Status: ${danposko_status}`);
    $('#modal-detail').find(`.danposko_at`).html( data.danposko_updated_at!=null ? danposko_status + ' pada: '+ moment(data.danposko_tanggal).format('DD MMMM YYYY') + ` ${data.danposko_jam}` : '' );

    let approval_pengamanan_keluar = '';
    let parent_approval_pengamanan_keluar = $('.approval_pengamanan_keluar').closest('.alert');
    parent_approval_pengamanan_keluar.removeClass();
    if( ['null',null].includes(data.approval_pengamanan_posisi)===false ){
        let no = 1;
        let posisi = JSON.parse(data.approval_pengamanan_posisi);
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
            </tr>`;
            no++;
        }
        parent_approval_pengamanan_keluar.addClass('alert alert-info');
    } else{
        parent_approval_pengamanan_keluar.addClass('alert alert-warning');
    }
    $('#modal-detail').find(`.approval_pengamanan_keluar`).html(approval_pengamanan_keluar);

    let approval_pengamanan_masuk = '';
    let parent_approval_pengamanan_masuk = $('.approval_pengamanan_masuk').closest('.alert');
    parent_approval_pengamanan_masuk.removeClass();
    if( ['null',null].includes(data.approval_pengamanan_posisi)===false ){
        let no = 1;
        let posisi = JSON.parse(data.approval_pengamanan_posisi);
        let posisi_masuk = _.filter(posisi, (o)=>{ return o.posisi=='masuk' && o.deleted_at==null;});
        for (const i of posisi_masuk) {
            approval_pengamanan_masuk += 
            `<tr>
                <td style="padding: 2px;">${no}</td>
                <td style="padding: 2px;">${i.pos_nama} | Oleh ${i.approval_nama} | Masuk pada ${moment(i.tanggal).format('DD MMMM YYYY')}, ${i.jam}</td>
            </tr>
            <tr>
                <td style="padding: 2px;"></td>
                <td style="padding: 2px;">Keterangan: ${i.keterangan}</td>
            </tr>`;
            no++;
        }
        if( posisi_masuk.length > 0 ) parent_approval_pengamanan_masuk.addClass('alert alert-info');
        else parent_approval_pengamanan_masuk.addClass('alert alert-warning');
    } else{
        parent_approval_pengamanan_masuk.addClass('alert alert-warning');
    }
    $('#modal-detail').find(`.approval_pengamanan_masuk`).html(approval_pengamanan_masuk);

    let konfirmasi_pembawa = '';
    let parent_konfirmasi_pembawa = $('.konfirmasi_pembawa_status').closest('.alert');
    parent_konfirmasi_pembawa.removeClass();
    switch (data.konfirmasi_pembawa_status) {
        case '1':
            konfirmasi_pembawa = 'Sudah Konfirmasi';
            parent_konfirmasi_pembawa.addClass('alert alert-info');
            break;
        case '2':
            konfirmasi_pembawa = 'Belum Konfirmasi';
            parent_konfirmasi_pembawa.addClass('alert alert-danger');
            break;
        default:
            konfirmasi_pembawa = 'Belum Konfirmasi';
            parent_konfirmasi_pembawa.addClass('alert alert-warning');
            break;
    }
    $('#modal-detail').find(`.konfirmasi_pembawa_status`).html(`Status: ${konfirmasi_pembawa}`);
    $('#modal-detail').find(`.konfirmasi_pembawa_at`).html( data.konfirmasi_pembawa_at!=null ? konfirmasi_pembawa + ' pada: '+ moment(data.konfirmasi_pembawa_tanggal).format('DD MMMM YYYY') + ` ${data.konfirmasi_pembawa_jam}` : '' );

    if( data.barang_kembali=='1' ) $('#div-detail-barang-kembali').show();
    else  $('#div-detail-barang-kembali').hide();
}

const get_detail_spbe = async(id)=>{
    let req = await $.ajax({
        type: "GET",
        url: `${BASE_URL}spbe/spbe_proses/detail_spbe/${id}`,
        data: {},
    });
    return req;
}

$('#modal-detail').on('hidden.bs.modal', function () {
    if( typeof table_spbe!='undefined' ) table_spbe.draw(false);
    $('#modal-detail').find('.div-approval-kasek').hide();
    $('#modal-detail').find('.div-lampiran').hide();
    $('.list-lampiran').html('');
});

window.status_pengajuan = (data)=>{
    let text = '';
    if( data.approval_kasek_np != null && data.approval_kasek_status == null ){
        text = `Menunggu Persetujuan Kasek`;
    } else if( data.approval_kasek_status == '2' ){
        text = `Ditolak Kasek`;
    } else if( data.approval_atasan_status == null ){
        text = `Menunggu Persetujuan Atasan`;
    } else if( data.approval_atasan_status == '2' ){
        text = `Ditolak Atasan`;
    } else if( data.pengecek1_status == '2' || data.konfirmasi_pengguna == '2' || data.danposko_status == '2' ){
        if( data.pengecek1_status == '2' ) text = `Ditolak Petugas Pamsiknilmat`;
        else if( data.konfirmasi_pengguna == '2' ) text = `Ditolak Pemohon`;
        else if( data.danposko_status == '2' ) text = `Ditolak Komandan Posko`;
    } else if( data.pengecek1_status == null ){
        text = `Menunggu Petugas Pamsiknilmat`;
    } 
    else if( data.konfirmasi_pengguna == null ){
        text = `Menunggu Konfirmasi Pemohon`;
    } 
    else if( data.danposko_status == null ){
        text = `Menunggu Persetujuan Komandan Posko`;
    } 
    else if( data.approval_pengamanan_keluar == null ){
        text = `Menunggu Persetujuan Admin Pamsiknilmat`;
    } else{
        text = data.kondisi_barang_keluar=='2' ? `Barang Keluar Sebagian`:`Pengeluaran Selesai`;
        if( data.barang_kembali == '1' ){
            if( data.approval_pengamanan_masuk == null ){
                text += `<br><span class="text-warning"><i>(Menunggu Barang Masuk)</i></span>`;
            } else{
                text = `Barang Telah Selesai Masuk`;
                if( data.konfirmasi_pembawa_status == null ){
                    text += `<br><span class="text-warning"><i>(Pembawa Barang Belum Konfirmasi)</i></span>`;
                }
            } 
        }
    }
    return text;
}