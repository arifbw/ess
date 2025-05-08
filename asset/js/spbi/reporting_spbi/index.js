var table_spbi;
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
    
    table_spbi = $('#reporting_spbi').DataTable({ 
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
            url: `${BASE_URL}spbi/reporting_spbi/get_data_reporting`,
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
                    text += `${row.np_karyawan} - ${row.nama}
                    <br>
                    <small>(${row.nama_unit})</small>
                    `;
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
                    let proses = '-';
                    if( row.approval_pengamanan_keluar==null ){
                        if( row.approval_atasan_status!='2' ) proses = `Proses Persetujuan`;
                    } else if( row.approval_pengamanan_keluar!=null ){
                        proses = `Sudah Keluar Perusahaan`;
                        if( row.approval_pengamanan_masuk!=null ){
                            proses = `Sudah Kembali ke Perusahaan`;
                        }
                    }
                    return proses;
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

                    let ekspor = $('<button/>', {
                        html: 'Export Excel',
                        class: 'btn btn-success',
                        onclick: `export_row(${JSON.stringify(row)})`
                    });
                    btn += ekspor.prop('outerHTML');

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
    window.open(`${BASE_URL}spbi/Export_excel/generate?start_date=${start_date}&end_date=${end_date}`,'_blank');
}

const export_row = (data)=>{
    let date_range = $('#date_range').val();
    let date_range_array = date_range.split(' - ');
    let start_date = date_range_array[0];
    let end_date = date_range_array[1];
    window.open(`${BASE_URL}spbi/Export_excel/generate?start_date=${start_date}&end_date=${end_date}&id=${data.uuid}`,'_blank');
}