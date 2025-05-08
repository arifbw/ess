function ambil_data(){
	$('#tabel_perencanaan_jadwal_kerja').DataTable().destroy();
	$('#tabel_perencanaan_jadwal_kerja').DataTable({ 
		'iDisplayLength': 10,
		'language': {
			'url': document.getElementById('base_url').value+'/asset/datatables/Indonesian.json',
			'sEmptyTable': 'Tidak ada data di database',
			'emptyTable': 'Tidak ada data di database'
		},
		'processing': true, //Feature control the processing indicator.
		'serverSide': true, //Feature control DataTables' server-side processing mode.
		'order': [], //Initial no order.

		// Load data for the table's content from an Ajax source
		'ajax': {
			'url': document.getElementById('base_url').value+'kehadiran/perencanaan_jadwal_kerja/tabel_jadwal_kerja/'+document.getElementById('bulan_tahun').value,
			'type': 'POST'
		},

		//Set column definition initialisation properties.
		'columnDefs': [
			{
				'targets': [ 0 ], //first column / numbering column
				'orderable': false, //set not orderable
			},
		],
	});
}