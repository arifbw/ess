		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />

		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo 'Detail ' .  $judul; ?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<div class="alert alert-info alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					AB Karyawan yang ditampilkan berikut ini dihasilkan dari membandingkan data kehadiran dengan jadwal kerja.
				</div>
				<?php
				// if($akses["lihat log"]){
				// 	echo "<div class='row text-right'>";
				// 		echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
				// 		echo "<br><br>";
				// 	echo "</div>";
				// }

				if ($this->akses["lihat"]) {
				?>
					<input type="text" id="kode_unit" name="kode_unit" value="<?php echo $kode_unit ?>" class="hidden">
					<input type="text" id="periode_awal" name="periode_awal" value="<?php echo $arr_periode[0] ?>" class="hidden">
					<input type="text" id="periode_akhir" name="periode_akhir" value="<?php echo $arr_periode[1] ?>" class="hidden">
					<input type="text" id="np" name="np" value="<?php echo $np ?>" class="hidden">

					<div class='row text-right'>
						<div class='col-md-12'>
							<a class='btn btn-primary btn-md' onclick="refresh_table_serverside()">Refresh</a>
						</div>
					</div>

					<br><br>
					<div class="form-group">
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="data_detail">
								<thead>
									<tr>
										<th class='text-center no-sort'>No</th>
										<th class='text-center no-sort'>NP</th>
										<th class='text-center no-sort'>Nama</th>
										<th class='text-center no-sort'>Start Date</th>
										<th class='text-center no-sort'>End Date</th>
										<th class='text-center no-sort'>Keterangan</th>
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
							<!-- /.table-responsive -->
						</div>
					</div>

				<?php
				}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->



		<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>
		<script type="text/javascript">
			var table;
			$(document).ready(function() {
				table_serverside();
			});

			function refresh_table_serverside() {
				table_serverside();
			}
		</script>

		<script>
			function table_serverside() {
				// $('#data_detail').DataTable().destroy();				
				//datatables
				table = $('#data_detail').DataTable({
					destroy: true,
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"bFilter": true,
					"stateSave": false,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("informasi/ab_karyawan/data_detail/") ?>",
						"type": "POST",
						"data": {
							"kode_unit": document.getElementById("kode_unit").value,
							"np_karyawan": document.getElementById("np").value,
							"periode_awal": document.getElementById("periode_awal").value,
							"periode_akhir": document.getElementById("periode_akhir").value,
						}
					},

					//Set column definition initialisation properties.
					"columnDefs": [{
						"targets": [0], //first column / numbering column
						"targets": 'no-sort', //first column / numbering column
						"orderable": false, //set not orderable
					}, ],

				});

			}

			// END heru PDS tambahkan ini, 2021-01-09
		</script>