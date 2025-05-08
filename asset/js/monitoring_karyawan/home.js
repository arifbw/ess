var table;
$(()=>{
    $('#date_range').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY'
        },
        startDate: moment().startOf('month').format('DD-MM-YYYY'),
        endDate: moment().endOf('month').format('DD-MM-YYYY'),
    });
});

function table_serverside() {
    let date_range = $('#date_range').val();
    let date_range_array = date_range.split(' - ');
    let start_date = date_range_array[0];
    let end_date = date_range_array[1];
    let np = $('#karyawan').val();
    
    table = $('#log_akses_table').DataTable({ 
        destroy: true,
        iDisplayLength: 10,
        language: {
            "url": `${BASE_URL}asset/datatables/Indonesian.json`,
            "sEmptyTable": "Tidak ada data di database",
            "emptyTable": "Tidak ada data di database"
        },
        processing: true,
        serverSide: true,
        ordering: false,
        ajax: {
            url: `${BASE_URL}monitoring_karyawan/home/get_data`,
            type: "POST",
            data: { start_date: start_date, end_date: end_date, np: np }
        },
        columns: [
            // {
            //     data: 'id',
            //     render: function (data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     }
            // },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    return `${row.np_karyawan}<br>${row.nama}`;
                }
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    let text = '';
                    let find = _.find(mst_perizinan, o=>{ return o.kode_pamlek==row.kode_pamlek && o.kode_erp==`${row.info_type}|${row.absence_type}` });
                    if( typeof find!='undefined' ) text = find.nama;
                    return text;
                }
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    let tanggal = '';
                    if( `${row.info_type}|${row.absence_type}|${row.kode_pamlek}`!==`2001|5000|0` ){
                        if( row.start_date!==null ){
                            tanggal += `${row.start_date} ${row.start_time} <br> s/d <br>`;
                        } else if( row.start_date_input!==null ){
                            tanggal += `${row.start_date_input} <br> s/d <br>`;
                        }
                    }

                    tanggal += (row.end_date!==null ? `${row.end_date} ${row.end_time}` : row.end_date_input);
                    return tanggal;
                }
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    let label;
                    switch (row.approval_1_status) {
                        case '1':
                            label = $('<label/>', {
                                html: 'Disetujui Atasan 1',
                                class: 'label label-success',
                            });
                            break;
                        case '2':
                            label = $('<label/>', {
                                html: 'Ditolak Atasan 1',
                                class: 'label label-danger',
                            });
                            break;
                        default:
                            label = $('<label/>', {
                                html: 'Menunggu Persetujuan',
                                class: 'label label-default',
                            });
                            break;
                    }
                    return label.prop('outerHTML');
                }
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    if( row.approval_2_np!=null ){
                        let label;
                        switch (row.approval_2_status) {
                            case '1':
                                label = $('<label/>', {
                                    html: 'Disetujui Atasan 2',
                                    class: 'label label-success',
                                });
                                break;
                            case '2':
                                label = $('<label/>', {
                                    html: 'Ditolak Atasan 2',
                                    class: 'label label-danger',
                                });
                                break;
                            default:
                                label = $('<label/>', {
                                    html: 'Menunggu Persetujuan',
                                    class: 'label label-default',
                                });
                                break;
                        }
                        return label.prop('outerHTML');
                    } else return '';
                }
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    let text = '';
                    if( [null,'null',''].includes(row.pos)===false ){
                        let array_pos = JSON.parse(row.pos);
                        let filter = _.filter(mst_pos, o=>{ return array_pos.includes(o.id)==true; });
                        let filter_map = _.map(filter, 'nama');
                        text = filter_map.join('<br>');
                    }
                    return text;
                }
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    let pos = '';
                    if( ['null',null].includes(row.approval_pengamanan_posisi)===false ){
                        let posisi = JSON.parse(row.approval_pengamanan_posisi);
                        let ordered = _.orderBy(posisi,['waktu'],['desc']);
                        for (const i of ordered) {
                            if( i.status==='1' ){
                                pos += `${i.nama_pos} | Oleh ${i.nama_approver??i.np_approver} | ${i.posisi} pada ${i.waktu};<br><br>`;
                            }
                        }
                    }
                    return pos;
                }
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    let btn = ''
                    let detail = $('<button/>', {
                        html: 'Detail',
                        class: 'btn btn-default',
                        onclick: `show_detail(${JSON.stringify(row)})`
                    });
                    btn += detail.prop('outerHTML');
                    return btn;
                }
            },
        ],
    });
};

const export_excel = ()=>{
    let date_range = $('#date_range').val();
    let date_range_array = date_range.split(' - ');
    let start_date = date_range_array[0];
    let end_date = date_range_array[1];
    let np = $('#karyawan').val();
    window.open(`${BASE_URL}monitoring_karyawan/Export_monitoring_karyawan/generate?start_date=${start_date}&end_date=${end_date}&np_input=${np}`,'_blank');
}