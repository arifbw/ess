var pieCharts = {};
var temp_data_lapor = [];
var karyawan_table;

$(() => {
	$("#filter-tahun").datepicker({
		format: "yyyy",
		viewMode: "years",
		minViewMode: "years",
		autoclose: true
	});
	$("#filter-tahun").val(new Date().getFullYear());
	$("#filter-unit").select2().trigger('change');
});

$("#btnExcel").click(function () {
	let tahun = $('#filter-tahun').val();
	let unit = $('#filter-unit').val();

	const form = $('<form>', {
		method: 'POST',
		action: BASE_URL + 'informasi/monitoring_pelaporan_pajak_karyawan/export_excel'
	});

	form.append($('<input>', {
		type: 'hidden',
		name: 'tahun',
		value: tahun
	}));

	form.append($('<input>', {
		type: 'hidden',
		name: 'unit',
		value: unit
	}));

	$('body').append(form);
	form.submit();
});

$("#filter-unit, #filter-tahun").on('change', function (e) {
	// load table
	if (typeof karyawan_table != 'undefined') karyawan_table.draw();
	else load_table();

	// get data
	let data = new FormData();
	data.append('tahun', $("#filter-tahun").val());
	data.append('unit', $("#filter-unit").val());

	$.ajax({
		url: BASE_URL + 'informasi/monitoring_pelaporan_pajak_karyawan/get_rekap_lapor',
		type: 'POST',
		data: data,
		dataType: 'json',
		processData: false,
		contentType: false,
		beforeSend: () => {
			$('#page-wrapper').LoadingOverlay('show');
			temp_data_lapor = [];
		},
	}).then((res) => {
		$('#page-wrapper').LoadingOverlay('hide', true);
		let data = res.data;
		temp_data_lapor = data;
		generate_pie_lapor();
	});
});

const load_table = () => {
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
		"ordering": false,
		"ajax": {
			"url": BASE_URL + 'informasi/monitoring_pelaporan_pajak_karyawan/tabel_lapor_pajak/',
			"data": function (d) {
				d.tahun = $('#filter-tahun').val();
				d.unit = $('#filter-unit').val();
			},
			"type": "POST"
		},
		"order": [[0, 'asc']],
	});
}

const generate_pie_lapor = () => {
	if (pieCharts['pie-lapor']) pieCharts['pie-lapor'].destroy();
	let tempData = {
		labels: Object.keys(temp_data_lapor),
		dataPie: Object.values(temp_data_lapor),
		backgroundColorPie: ['#34c38f', '#cf5d55'],
		borderColorPie: ['#34c38f', '#cf5d55'],
		title: 'Status Lapor Pajak Tahun ' + $('#filter-tahun').val(),
	};

	pieCharts['pie-lapor'] = new Chart(
		document.getElementById(`pie-lapor`),
		set_config_pie(tempData)
	);
}

const set_config_pie = (tempData) => {
	return {
		type: 'pie',
		data: {
			labels: tempData.labels,
			datasets: [{
				data: tempData.dataPie,
				backgroundColor: tempData.backgroundColorPie,
				borderColor: tempData.borderColorPie,
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
					display: true,
					position: 'bottom'
				},
				datalabels: {
					color: 'white',
					anchor: 'middle',
					align: 'top',
					formatter: (value, context) => {
						const label = context.chart.data.labels[context.dataIndex];
						return label + ' ' + value + ' orang';
					},
					font: {
						weight: 'bold',
					}
				}
			}
		},
		plugins: [ChartDataLabels]
	};
}
