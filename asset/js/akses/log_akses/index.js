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
            url: `${BASE_URL}akses/log_akses/get_data`,
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
                data: 'np',
                name: 'np',
                render: function ( data, type, row, meta ) {
                    let text = row.username;
                    if( row.np!==null ) text = row.np;
                    return text;
                }
            },
            {
                data: 'modul',
                name: 'modul',
            },
            {
                data: 'description',
                name: 'description',
            },
            {
                data: 'input_from',
                name: 'input_from',
            },
            {
                data: 'ip_address',
                name: 'ip_address',
            },
            {
                data: 'timestamp',
                name: 'timestamp',
            },
            {
                data: 'user_agent',
                name: 'user_agent',
            },
            {
                data: 'id',
                render: function ( data, type, row, meta ) {
                    let btn = ''
                    let detail = $('<button/>', {
                        html: 'Detail',
                        class: 'btn btn-default',
                        // onclick: `show_detail(${JSON.stringify(row)})`
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
    window.open(`${BASE_URL}akses/Export_log_akses/generate?start_date=${start_date}&end_date=${end_date}`,'_blank');
}