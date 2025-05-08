		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />

		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul; ?></h1>
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
					<form action="<?= site_url('informasi/ab_karyawan/export') ?>" method="POST" target="_blank">
						<div class="row">
							<div class='col-lg-8 col-md-8'>
								<span>Unit Kerja</span>
								<select class="form-control select2" name="kode_unit" id="unit_kerja" onchange="pilihan_karyawan()">
								</select>


								<div class='row'>
									<div class='col-lg-7 col-md-7'>
										<span>Karyawan </span>
										<select class="form-control select2" name="np_karyawan" id="karyawan">
											<?php

											if ($np != "") {
												for ($i = 0; $i < count($daftar_akses_karyawan); $i++) {
													if (strcmp($daftar_akses_karyawan[$i]["no_pokok"], $np) == 0) {
														$selected = "selected=selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $daftar_akses_karyawan[$i]["no_pokok"] . "' $selected>" . $daftar_akses_karyawan[$i]["no_pokok"] . " - " . $daftar_akses_karyawan[$i]["nama"] . "</option>";
												}
											} else {
												for ($i = 0; $i < count($daftar_akses_karyawan); $i++) {
													if (strcmp($daftar_akses_karyawan[$i]["no_pokok"], $this->session->userdata("no_pokok")) == 0) {
														$selected = "selected=selected";
													} else {
														$selected = "";
													}
													echo "<option value='" . $daftar_akses_karyawan[$i]["no_pokok"] . "' $selected>" . $daftar_akses_karyawan[$i]["no_pokok"] . " - " . $daftar_akses_karyawan[$i]["nama"] . "</option>";
												}
											}
											?>
										</select>
									</div>

								</div>
							</div>
							<div class='col-lg-4 col-md-4'>
								<span>Periode Awal</span>
								<!-- <select class="form-control select2" name="periode" id="periode" onchange="refresh_table_serverside()">
								<?php

								for ($i = 0; $i < count($arr_periode); $i++) {
									if (strcmp($arr_periode[$i]["value"], date("Y_m")) == 0) {
										$selected = "selected=selected";
									} else {
										$selected = "";
									}
									echo "<option value='" . $arr_periode[$i]["value"] . "' $selected>" . $arr_periode[$i]["text"] . "</option>";
								}
								?>
								</select> -->
								<input type="text" class="form-control" name="periode_awal" id="insert_date_awal" autocomplete="off" value="<?= ($np != "" ? date('Y-01') : date('Y-m')) ?>" placeholder="Tanggal Awal">
								<span>Periode Awal</span>
								<input type="text" class="form-control" name="periode_akhir" id="insert_date_akhir" autocomplete="off" value="<?= date('Y-m') ?>" placeholder="Tanggal Akhir">
							</div>
						</div>
						<br>
						<div class='row text-right'>
							<div class='col-md-12'>
								<a class='btn btn-primary btn-md' onclick="refresh_table_serverside()">Refresh</a>
								<button target="_blank" class='btn btn-warning btn-md' type='submit'>Export</button>
							</div>
						</div>
					</form>

					<br><br>
					<input type="hidden" name="log_data_keterlambatan" id="log_data_keterlambatan" value="no">
					<div class="form-group">
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ab_karyawan">
								<thead>
									<tr>
										<th class='text-center no-sort'>No</th>
										<th class='text-center no-sort'>NP</th>
										<th class='text-center no-sort'>Nama</th>
										<th class='text-center no-sort'>Jumlah AB</th>
										<th class='text-center no-sort'>Detail</th>
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
				$("#log_data_keterlambatan").val("yes");
				getUnitByPeriode(); // heru PDS tambahkan ini, 2021-01-09
				// table_serverside();
				setTimeout(function() {
					table_serverside();
				}, 5000);
				$("#log_data_keterlambatan").val("no");
			});

			function refresh_table_serverside() {
				$("#log_data_keterlambatan").val("yes");
				table_serverside();
				$("#log_data_keterlambatan").val("no");
			}

			$(function() {
				$('#insert_date_awal').datetimepicker({
					format: 'YYYY-MM',
				}).on('dp.change', function(e) { // heru PDS tambahkan onchange, 2021-01-09
					getUnitByPeriode()
				});

				$('#insert_date_akhir').datetimepicker({
					format: 'YYYY-MM',
				}).on('dp.change', function(e) { // heru PDS tambahkan onchange, 2021-01-09
					getUnitByPeriode()
				});
			});
		</script>

		<script>
			function table_serverside() {
				// $('#tabel_ab_karyawan').DataTable().destroy();				
				//datatables
				table = $('#tabel_ab_karyawan').DataTable({
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
						"url": "<?php echo site_url("informasi/ab_karyawan/tabel_ab_karyawan/") ?>",
						"type": "POST",
						"data": {
							"kode_unit": document.getElementById("unit_kerja").value,
							"np_karyawan": document.getElementById("karyawan").value,
							"periode_awal": document.getElementById("insert_date_awal").value,
							"periode_akhir": document.getElementById("insert_date_akhir").value,
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

			function pilihan_karyawan() {
				var unit_kerja = $('#unit_kerja').val();
				// heru PDS tambahkan variable ini, 2021-01-11
				let insert_date_awal = $('#insert_date_awal').val();
				let insert_date_akhir = $('#insert_date_akhir').val();

				$.ajax({
					type: "post",
					// heru PDS mengganti url mjd daftar_karyawan_revisi, 2021-01-11
					url: document.getElementById("base_url").value + "informasi/ab_karyawan/daftar_karyawan_revisi/",
					dataType: "json",
					data: {
						"unit_kerja": unit_kerja,
						// heru PDS tambahkan parameter ini, 2021-01-11
						insert_date_awal: insert_date_awal,
						insert_date_akhir: insert_date_akhir
					},
					success: function(data) {
						console.log(data);
						while (document.getElementById("karyawan").length > 0) {
							document.getElementById("karyawan").remove(document.getElementById("karyawan").length - 1);
						}

						banyak_karyawan = data.karyawan.length;

						if (banyak_karyawan > 1) {
							var option = document.createElement("option");
							option.value = "";
							option.text = "(semua karyawan)";
							document.getElementById("karyawan").add(option);
						}

						for (var i = 0; i < banyak_karyawan; i++) {
							var option = document.createElement("option");
							option.value = data.karyawan[i].no_pokok;
							option.text = data.karyawan[i].no_pokok + " - " + data.karyawan[i].nama;
							document.getElementById("karyawan").add(option);
						}
						if ("<?= $np ?>" != "")
							document.getElementById("karyawan").value = "<?= $np ?>";
						else
							document.getElementById("karyawan").value = data.np_pengguna;

						// refresh_table_serverside();
					}
				});

			}

			const getUnitByPeriode = () => {
				console.log('Get Unit...');
				$(`#unit_kerja`).html('');
				let insert_date_awal = $(`#insert_date_awal`).val();
				let insert_date_akhir = $(`#insert_date_akhir`).val();
				let url = document.getElementById("base_url").value;

				$.ajax({
					type: "POST",
					url: `${url}informasi/ab_karyawan/getUnitByPeriode`,
					dataType: "html",
					data: {
						insert_date_awal: insert_date_awal,
						insert_date_akhir: insert_date_akhir
					},
					success: function(response) {
						console.log('Unit Printed.');
						$(`#unit_kerja`).html(response);
						pilihan_karyawan(); // heru PDS tambahkan function ini, 2021-01-11
					},
					error: function() {
						$(`#unit_kerja`).html('');
					}
				});
			}
			// END heru PDS tambahkan ini, 2021-01-09
		</script>