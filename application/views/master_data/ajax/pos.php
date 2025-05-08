<script>
	$(document).ready(function() {
		$('#tabel_kry_pos').DataTable({
	        destroy: true,
	        'iDisplayLength': 10,
	        'language': {
	            'url': '".base_url()."asset/datatables/Indonesian.json',
	            'sEmptyTable': 'Tidak ada data di database',
	            'emptyTable': 'Tidak ada data di database'
	        },
	        'processing': true,
	        'searching': true,
			'responsive': true,
	        'ajax': {
	            'url': '<?= base_url() ?>master_data/pos/akses_pengguna',
	            'type': 'POST'
	        }
	    });
	    
		$('#tabel_kry').DataTable({
			destroy: true,
	        'iDisplayLength': 10,
	        'language': {
	            'url': '<?= base_url() ?>asset/datatables/Indonesian.json',
	            'sEmptyTable': 'Tidak ada data di database',
	            'emptyTable': 'Tidak ada data di database'
	        },
	        'stateSave': true,
	        'processing': true,
	        'serverSide': true,
	        'ordering': false,
			'responsive': true,
	        'ajax': {
	            'url': '".base_url()."master_data/pos/mst_pengguna',
	            'type': 'POST'
	        },
	        'columnDefs': [  
	            { 
	                'targets': 'no-sort',
	                'orderable': false,
	            },
	        ],
		});
	});
</script>