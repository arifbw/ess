		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
						<h1 class="page-header"><?php echo $judul;?></h1>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<?php if(!empty($success)) { ?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $success;?>
				</div>
				<?php } ?>
				
				<?php if(!empty($warning)) { ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $warning;?>
				</div>
				<?php } ?>

				<?php if($akses["hak akses"]) { ?>
				<div class="row">
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_kry_pos">
						<thead>
							<tr>
								<th class='text-center'>No</th>
								<th class='text-center'>Nomor Pokok</th>
								<th class='text-center'>Nama</th>
								<th class='text-center'>Kode Unit Kerja</th>
								<th class='text-center'>Nama Unit Kerja</th>
								<th class='text-center'>Aksi</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<!-- /.table-responsive -->
				</div>

				<br><br>
				<div class="row">
					<h3><b>Tambah Pengguna</b></h3>
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_kry">
						<thead>
							<tr>
								<th class='text-center'>No</th>
								<th class='text-center'>Nomor Pokok</th>
								<th class='text-center'>Nama</th>
								<th class='text-center'>Kode Unit Kerja</th>
								<th class='text-center'>Nama Unit Kerja</th>
								<th class='text-center'>Aksi</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<!-- /.table-responsive -->
				</div>
				<?php } ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->


	<script>
	function load_kry_pos() {
		var pos = '<?= $this->uri->segment(4) ?>';

		$('#tabel_kry_pos').DataTable({
	        destroy: true,
	        'iDisplayLength': 10,
	        'language': {
	            'url': '<?= base_url() ?>asset/datatables/Indonesian.json',
	            'sEmptyTable': 'Tidak ada data di database',
	            'emptyTable': 'Tidak ada data di database'
	        },
	        'processing': true,
	        'searching': true,
			'responsive': true,
	        'ajax': {
	            'url': '<?= base_url() ?>master_data/pos/akses_pengguna',
	            'type': 'POST',
	            'data': {pos:pos}
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
	            'url': '<?= base_url() ?>master_data/pos/mst_pengguna',
	            'type': 'POST'
	        },
	        'columnDefs': [  
	            { 
	                'targets': 'no-sort',
	                'orderable': false,
	            },
	        ],
		});
	}

	$(document).ready(function() {

		load_kry_pos();

		$(document).on( "click", '.tambah_data',function(e) {
			var np = $(this).data('np');
			var pos = '<?= $this->uri->segment(4) ?>';

			$.ajax({
	            url: "<?php echo base_url('master_data/pos/save_kry');?>",
	            type: "POST",
	            dataType: "json",
	            data: {np:np, pos:pos},
			    success: function(response){
			    	if(response.status==true)
			    		load_kry_pos();
	            },
	            error: function(e){
	                console.log(e);
	            }
	        });
	    });

		$(document).on( "click", '.hapus_data',function(e) {
			var np = $(this).data('np');
			var pos = '<?= $this->uri->segment(4) ?>';

			$.ajax({
	            url: "<?php echo base_url('master_data/pos/hapus_kry');?>",
	            type: "POST",
	            dataType: "json",
	            data: {np:np, pos:pos},
			    success: function(response){
			    	if(response.status==true)
			    		load_kry_pos();
	            },
	            error: function(e){
	                console.log(e);
	            }
	        });
	    });
	});

	</script>

