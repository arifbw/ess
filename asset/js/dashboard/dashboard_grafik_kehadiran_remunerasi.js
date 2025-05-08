var filter_kehadiran_unit = [];
var temp_data_kehadiran = [];
$(()=>{
    $('#filter-kehadiran-bulan').trigger('change');
});

$('#notif-hutang-cuti').on('click', ()=>{
    location.href = `${BASE_URL}cuti/pembayaran_hutang_cuti`;
});

// bulan change
$('#filter-kehadiran-bulan').on('change', (e)=>{
    let bulan = e.target.value;
    let data = new FormData();
    data.append('bulan', bulan);
    $.ajax({
        url: BASE_URL + 'home/get_filter_kehadiran_unit',
        type: 'POST',
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#div-grafik-kehadiran').LoadingOverlay('show');
            filter_kehadiran_unit = [];
        },
    }).then((res) => {
        $('#div-grafik-kehadiran').LoadingOverlay('hide', true);
        filter_kehadiran_unit = res.data;
        set_filter_kehadiran_unit();
    });
});

const set_filter_kehadiran_unit = () => {
    let div_filter_unit = $('#filter-kehadiran-unit');
    div_filter_unit.empty();
    div_filter_unit.append(new Option('-- Semua Unit --', ''));
    for (const i of filter_kehadiran_unit) {
        div_filter_unit.append(new Option(i.nama_unit, i.kode_unit));
    }
    div_filter_unit.select2();
    div_filter_unit.trigger('change');
    return;
}

// unit change
$('#filter-kehadiran-unit').on('change', (e)=>{
    let bulan = $('#filter-kehadiran-bulan').val();
    let unit = e.target.value;
    let data = new FormData();
    data.append('bulan', bulan);
    data.append('unit', unit);
    $.ajax({
        url: BASE_URL + 'home/get_data_kehadiran_by_unit',
        type: 'POST',
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#div-grafik-kehadiran').LoadingOverlay('show');
            temp_data_kehadiran = [];
        },
    }).then((res) => {
        $('#div-grafik-kehadiran').LoadingOverlay('hide', true);
        temp_data_kehadiran = res.data;
        set_grafik_kehadiran();
    });
});

const set_grafik_kehadiran = () => {
    AmCharts.makeChart("chartdiv", {
        "type": "pie",
        "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]] hari</b> ([[percents]]%)</span>",
        "innerRadius": "40%",
        "titleField": "category",
        "valueField": "column-1",
        "theme": "dashboard_peruri",
        "allLabels": [],
        "balloon": {},
        "legend": {
            "enabled": true,
            "align": "center",
            "markerType": "circle"
        },
        "titles": [],
        "dataProvider": temp_data_kehadiran.map(item => ({
            category: item.nama,
            "column-1": parseInt(item.jml)
        }))
    });
}