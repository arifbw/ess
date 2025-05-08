var barCharts = {};
var temp_data_realisasi = [];
var karyawan_table;
var kolom_hitung = ['total_3001_uang_lembur_15','total_3002_uang_lembur_2','total_3003_uang_lembur_3','total_3004_uang_lembur_4','total_3005_uang_lembur_5','total_3006_uang_lembur_6','total_3007_uang_lembur_7','total_3100_uang_lembur_manual','total_3110_insentif_lembur','total_3400_uang_lembur_susulan'];

$(()=>{
    $("#filter-tahun").datepicker({
        format: "yyyy",
        viewMode: "years",
        minViewMode: "years",
        autoclose: true
    });
    $("#filter-tahun").val(new Date().getFullYear());
    $("#filter-unit").select2().trigger('change');
});

$('#btnExcel').on('click', () => {
    window.open(`${BASE_URL}lembur/dashboard_realisasi/export_excel?tahun=${$('#filter-tahun').val()}&unit=${$('#filter-unit').val()}`);
});

$('#btnRincian').on('click', () => {
    window.open(`${BASE_URL}lembur/dashboard_realisasi/export_excel_rincian?tahun=${$('#filter-tahun').val()}&unit=${$('#filter-unit').val()}`);
});

$("#filter-unit, #filter-tahun").on('change', function(e){
    if($("#filter-unit").val()=='00000') {
        $('#label-plafon').html('Plafon');
        $('#label-realisasi').html('Realisasi');
    } else if($("#filter-unit").val().charAt(1)==='0') {
        $('#label-plafon').html('Plafon Direktorat');
        $('#label-realisasi').html('Realisasi Direktorat');
    } else if($("#filter-unit").val().charAt(1)!=='0') {
        $('#label-plafon').html('Plafon Divisi');
        $('#label-realisasi').html('Realisasi Divisi');
    }

    // load table
    if(typeof karyawan_table!='undefined') karyawan_table.draw();
    else load_table();

    // get data
    let data = new FormData();
    data.append('tahun', $("#filter-tahun").val());
    data.append('unit', $("#filter-unit").val());
    $.ajax({
        url: BASE_URL + 'lembur/dashboard_realisasi/get_plafon',
        type: 'POST',
        data: data,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#page-wrapper').LoadingOverlay('show');
            temp_data_realisasi = [];
            $('#field-plafon').val('');
            $('#field-realisasi').val('');
            $('#field-sisa').val('');
        },
    }).then((res) => {
        $('#page-wrapper').LoadingOverlay('hide', true);
        let data = res.data;
        temp_data_realisasi = data.realisasi;
        generate_realisasi_bar();
        let default_plafon = 0;
        if(data.plafon!=null){
            let plafon = new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(data.plafon.nominal);
            default_plafon = parseFloat(data.plafon.nominal);
            $('#field-plafon').val(plafon);
        } else {
            $('#field-plafon').val('-');
        }

        // realisasi
        let sum = 0;
        for (const i of data.realisasi) {
            for (const [k,v] of Object.entries(i)) {
                if(kolom_hitung.includes(k)) sum += parseFloat(v);
            }
        }
        let realisasi = new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(sum);
        $('#field-realisasi').val(realisasi);

        // sisa
        let sisa = '-';
        if(default_plafon > 0){
            sisa = new Intl.NumberFormat("id-ID", {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(default_plafon - sum);
        }
        $('#field-sisa').val(sisa);
    });
});

const load_table = ()=>{
    karyawan_table = $('#karyawan-table').DataTable({
        "iDisplayLength": 10,
        "language": {
            "url": BASE_URL + 'asset/datatables/Indonesian.json',
            "sEmptyTable": "Tidak ada data di database",
            "processing": "Sedang memuat data pengajuan lembur",
            "emptyTable": "Tidak ada data di database"
        },
        "destroy": true,
        "stateSave": true,
        "responsive": true,
        "processing": true,
        "serverSide": true,
        // "ordering": false,
        "ajax": {
            "url": BASE_URL + 'lembur/dashboard_realisasi/get_data_karyawan',
            "data": function(d) {
            	d.tahun= $('#filter-tahun').val();
            	d.unit= $('#filter-unit').val();
            },
            "type": "POST"
        },
        "order": [[0, 'asc']],
        "columnDefs": [
            { "orderable": true, "targets": [1, 2, 4, 5] },
            { "orderable": false, "targets": "_all" }
        ],
        columns: [
            {
                data: 'no',
            }, {
                data: 'np_karyawan',
            }, {
                data: 'nama',
            }, {
                data: 'unit_kerja',
            }, {
                data: 'id',
                render: (id, type, row) => {
                    let sum = 0;
                    for (const [k,v] of Object.entries(row)) {
                        if(kolom_hitung.includes(k)) sum += parseFloat(v);
                    }
                    return new Intl.NumberFormat("id-ID", {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(sum);
                }
            }, {
                data: 'total_jam',
            }
        ]
    });
}

const generate_realisasi_bar = () => {
    if(barCharts['bar-realisasi']) barCharts['bar-realisasi'].destroy();
    let data_bar = [];
    let data_line = [];
    let sum_line = 0;
    for (const i of bulan) {
        let data_match = temp_data_realisasi.find(o=>{ return o.periode_bulan==i.month; });
        if(typeof data_match!='undefined') {
            let sum = 0;
            for (const [k,v] of Object.entries(data_match)) {
                if(kolom_hitung.includes(k)) sum += parseFloat(v);
            }
            data_bar.push(sum / 1000000);
            sum_line += sum;
        } else {
            data_bar.push(0);
            sum_line += 0;
        }
        data_line.push(sum_line / 1000000);
    }

    let tempData = {
        labels: bulan.map(o=>{ return o.name; }),
        dataBar: data_bar,
        labelBar: 'Per Bulan',
        backgroundColorBar: ['#34c38f'],
        borderColorBar: ['#34c38f'],
        title: 'Realisasi Lembur Tahun ' + $('#filter-tahun').val(),
        dataLine: data_line,
        labelLine: 'Akumulasi',
        backgroundColorLine: ['#556ee6'],
        borderColorLine: ['#556ee6']
    };

    barCharts['bar-realisasi'] = new Chart(
        document.getElementById(`bar-realisasi`),
        set_config_bar(tempData)
    );
}

const set_config_bar = (tempData) =>{
    return {
        // type: 'bar',
        data: {
            labels: tempData.labels,
            datasets: [{
                type: 'bar',
                label: tempData.labelBar,
                data: tempData.dataBar,
                backgroundColor: tempData.backgroundColorBar,
                borderColor: tempData.borderColorBar,
                borderWidth: 1
            }, {
                type: 'line',
                label: tempData.labelLine,
                data: tempData.dataLine,
                backgroundColor: tempData.backgroundColorLine,
                borderColor: tempData.borderColorLine,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: tempData.title,
                    padding: 30
                },
                legend: {
                    display: false
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    formatter: (value, context) => {
                        return value.toFixed(2);
                    },
                    font: {
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Realisasi (Juta)'
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    };
}