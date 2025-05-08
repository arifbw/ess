var table_spbi;
$(()=>{
    $('#date_range').daterangepicker({
        locale: {
            format: 'DD-MM-YYYY'
        },
        startDate: moment().subtract(6,'d').format('DD-MM-YYYY'),
        endDate: moment().format('DD-MM-YYYY'),
    });
});

function table_serverside() {
    let date_range = $('#date_range').val();
    let date_range_array = date_range.split(' - ');
    let start_date = date_range_array[0];
    let end_date = date_range_array[1];
    
    table_spbi = $('#persetujuan_spbi').DataTable({ 
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
            url: `${BASE_URL}spbi/persetujuan_spbi/get_data_persetujuan`,
            type: "POST",
            data: { start_date: start_date, end_date: end_date }
        },
        columns: [
            {
                data: 'id',
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'nomor_surat',
                name: 'nomor_surat',
            },
            {
                data: 'nama',
                render: function (data, type, row, meta) {
                    let text = '';
                    text += `${row.np_karyawan} - ${row.nama}`;
                    return text;
                }
            },
            {
                data: 'id',
                render: function (data, type, row, meta) {
                    let text = '';
                    if( ['null',null].includes(row.pos_keluar)===false ){
                        let pos_keluar = JSON.parse(row.pos_keluar);
                        let pos_keluar_mapped = _.map(pos_keluar, 'nama');
                        text = pos_keluar_mapped.join(', ');
                    }
                    return text;
                }
            },
            {
                data: 'id',
                render: function (data, type, row, meta) {
                    let text = '';
                    if( ['null',null].includes(row.pos_masuk)===false ){
                        let pos_masuk = JSON.parse(row.pos_masuk);
                        let pos_masuk_mapped = _.map(pos_masuk, 'nama');
                        text = pos_masuk_mapped.join(', ');
                    }
                    return text;
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function ( data, type, row, meta ) { 
                    return moment(row.created_at).format('DD MMMM YYYY'); 
                }
            },
            {
                data: 'tanggal_keluar',
                name: 'tanggal_keluar',
                render: function ( data, type, row, meta ) { 
                    return moment(row.tanggal_keluar).format('DD MMMM YYYY'); 
                }
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) { 
                    const status = status_pengajuan(row);
                    return status;
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

                    if( row.approval_pengamanan_updated_at!=null ){
                        let cetak = $('<button/>', {
                            html: 'Cetak',
                            class: 'btn btn-danger',
                            onclick: `proses_cetak(${JSON.stringify(row)})`
                        });
                        btn += cetak.prop('outerHTML');
                    }

                    if( row.approval_atasan_status===null ){
                        let approval = $('<button/>', {
                            html: 'Persetujuan',
                            class: 'btn btn-primary',
                            onclick: `show_approval_atasan(${JSON.stringify(row)})`
                        });
                        btn += approval.prop('outerHTML');
                    }
                    return btn;
                }
            },
        ],
    });
};

// cetak
const proses_cetak = async(data)=>{
    $('#form-lokasi-ttd').find('[name=uuid]').val(data.uuid);
    $('#modal-lokasi-ttd').modal('show');
}

const cetak_pdf = async()=>{
    let uuid = $('#form-lokasi-ttd').find('[name=uuid]').val();
    let lokasi = $('#form-lokasi-ttd').find('[name=pilih_lokasi_ttd]').val();
    window.open(`${BASE_URL}spbi/spbi_proses/export_pdf?uuid=${uuid}&lokasi=${lokasi}`, '_blank');
}
// end cetak