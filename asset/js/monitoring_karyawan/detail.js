const show_detail = async(data)=>{
    $('#modal-detail').modal('show');
    for (const [key,value] of Object.entries(data)) {
        if(['pos','approval_1_status','approval_2_status','approval_pengamanan_posisi'].includes(key)===false) $('#modal-detail').find(`.${key}`).text(value);
    }

    let jenis_izin = '';
    let find = _.find(mst_perizinan, o=>{ return o.kode_pamlek==data.kode_pamlek && o.kode_erp==`${data.info_type}|${data.absence_type}` });
    if( typeof find!='undefined' ) jenis_izin = find.nama;
    $('#modal-detail').find(`.jenis_izin`).html(jenis_izin);

    /** Tanggal */
    let tanggal = '';
    if( `${data.info_type}|${data.absence_type}|${data.kode_pamlek}`!==`2001|5000|0` ){
        if( data.start_date!==null ){
            tanggal += `${data.start_date} ${data.start_time} s/d `;
        } else if( data.start_date_input!==null ){
            tanggal += `${data.start_date_input} s/d `;
        }
    }
    tanggal += (data.end_date!==null ? `${data.end_date} ${data.end_time}` : data.end_date_input);
    $('#modal-detail').find(`.tanggal`).html(tanggal);

    /** Pos */
    let pos_text = '';
    if( ['null',null].includes(data.pos)===false ){
        let pos_id = JSON.parse(data.pos);
        let filter_pos = _.filter(mst_pos, o=>{ return pos_id.includes(o.id)==true; });
        let pos_mapped = _.map(filter_pos, 'nama');
        pos_text = pos_mapped.join(', ');
    }
    $('#modal-detail').find(`.pos`).html(pos_text);

    /** Approval 1 */
    let approval_1_status = '';
    let parent_approval_1_status = $('.approval_1_status').closest('.alert');
    parent_approval_1_status.removeClass();
    switch (data.approval_1_status) {
        case '1':
            approval_1_status = 'Disetujui';
            parent_approval_1_status.addClass('alert alert-info');
            break;
        case '2':
            approval_1_status = 'Ditolak';
            parent_approval_1_status.addClass('alert alert-danger');
            break;
        default:
            approval_1_status = 'Menunggu Persetujuan';
            parent_approval_1_status.addClass('alert alert-warning');
            break;
    }
    $('#modal-detail').find(`.approval_1_status`).html(approval_1_status);

    /** Approval 2 */
    if( data.approval_2_np!==null ){
        $('#div-atasan-2').show();
        let approval_2_status = '';
        let parent_approval_2_status = $('.approval_2_status').closest('.alert');
        parent_approval_2_status.removeClass();
        switch (data.approval_2_status) {
            case '1':
                approval_2_status = 'Disetujui';
                parent_approval_2_status.addClass('alert alert-info');
                break;
            case '2':
                approval_2_status = 'Ditolak';
                parent_approval_2_status.addClass('alert alert-danger');
                break;
            default:
                approval_2_status = 'Menunggu Persetujuan';
                parent_approval_2_status.addClass('alert alert-warning');
                break;
        }
        $('#modal-detail').find(`.approval_2_status`).html(approval_2_status);
    } else{
        $('#div-atasan-2').hide();
    }

    /** Pos */
    let approval_pengamanan_posisi = '';
    let parent_approval_pengamanan_posisi = $('.approval_pengamanan_posisi').closest('.alert');
    parent_approval_pengamanan_posisi.removeClass();
    if( ['null',null].includes(data.approval_pengamanan_posisi)===false ){
        let no = 1;
        let posisi = JSON.parse(data.approval_pengamanan_posisi);
        let posisi_1 = _.filter(posisi, (o)=>{ return o.status=='1';});
        let posisi_desc = _.orderBy(posisi_1,['waktu'],['desc']);
        for (const i of posisi_desc) {
            approval_pengamanan_posisi += 
            `<tr>
                <td style="padding: 2px;">${no}</td>
                <td style="padding: 2px;">${i.nama_pos} | Oleh ${i.nama_approver??i.np_approver} | ${i.posisi} pada ${i.waktu}</td>
            </tr>`;
            no++;
        }
        parent_approval_pengamanan_posisi.addClass('alert alert-info');
    } else{
        parent_approval_pengamanan_posisi.addClass('alert alert-warning');
    }
    $('#modal-detail').find(`.approval_pengamanan_posisi`).html(approval_pengamanan_posisi);
}

$('#modal-detail').on('hidden.bs.modal', function () {
    if( typeof table!='undefined' ) table.draw(false);
});