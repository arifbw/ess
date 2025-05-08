<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" />
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

		<?php
		if (!empty($this->session->flashdata('success'))) {
		?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('success'); ?>
			</div>
		<?php
		}
		if (!empty($this->session->flashdata('failed'))) {
		?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('failed'); ?>
			</div>
		<?php
		}
		if ($akses["lihat log"]) {
			echo "<div class='row text-right'>";
			echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
			echo "<br><br>";
			echo "</div>";
		}
		?>
		<?php


		if ($this->akses["lihat"]) {
		?>
			<?php

			$filter_belum			= $this->session->flashdata('persetujuan_filter_belum');
			if ($filter_belum == 1) {
				$checked_filter_belum = 'checked';
			} else {
				$checked_filter_belum = '';
			}

			$filter_atasan_1			= $this->session->flashdata('persetujuan_filter_atasan_1');
			if ($filter_atasan_1 == 1) {
				$checked_filter_atasan_1 = 'checked';
			} else {
				$checked_filter_atasan_1 = '';
			}

			$filter_atasan_2			= $this->session->flashdata('persetujuan_filter_atasan_2');
			if ($filter_atasan_2 == 1) {
				$checked_filter_atasan_2 = 'checked';
			} else {
				$checked_filter_atasan_2 = '';
			}

			$filter_sdm			= $this->session->flashdata('persetujuan_filter_sdm');
			if ($filter_sdm == 1) {
				$checked_filter_sdm = 'checked';
			} else {
				$checked_filter_sdm = '';
			}

			$filter_belum_sdm			= $this->session->flashdata('persetujuan_filter_belum_sdm');
			if ($filter_belum_sdm == 1) {
				$checked_filter_belum_sdm = 'checked';
			} else {
				$checked_filter_belum_sdm = '';
			}

			$filter_batal			= $this->session->flashdata('persetujuan_filter_batal');
			if ($filter_batal == 1) {
				$checked_filter_batal = 'checked';
			} else {
				$checked_filter_batal = '';
			}


			$filter_tolak_atasan			= $this->session->flashdata('persetujuan_filter_tolak_atasan');
			if ($filter_tolak_atasan == 1) {
				$checked_filter_tolak_atasan = 'checked';
			} else {
				$checked_filter_tolak_atasan = '';
			}

			$filter_tolak_sdm			= $this->session->flashdata('persetujuan_filter_tolak_sdm');
			if ($filter_tolak_sdm == 1) {
				$checked_filter_tolak_sdm = 'checked';
			} else {
				$checked_filter_tolak_sdm = '';
			}



			?>
			<div class="form-group">
				<label>Filter</label>
				<div class="row">
					<div class="col-lg-3">
						<div class="checkbox">
							<label>
								<input name='filter_belum' id='filter_belum' class='filter_status' type="checkbox" value="1" onclick='refresh_table_serverside()' <?php echo $checked_filter_belum; ?>>Belum disetujui Atasan
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input name='filter_atasan_1' id='filter_atasan_1' class='filter_status' type="checkbox" value="1" onclick='refresh_table_serverside()' <?php echo $checked_filter_atasan_1; ?>>Atasan 1 Approve
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input name='filter_atasan_2' id='filter_atasan_2' class='filter_status' type="checkbox" value="1" onclick='refresh_table_serverside()' <?php echo $checked_filter_atasan_2; ?>>Atasan 2 Approve
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input name='filter_sdm' id='filter_sdm' class='filter_status' type="checkbox" value="1" onclick='refresh_table_serverside()' <?php echo $checked_filter_sdm; ?>>SDM Approve
							</label>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="checkbox">
							<label>
								<input name='filter_belum_sdm' id='filter_belum_sdm' class='filter_status' type="checkbox" value="1" onclick='refresh_table_serverside()' <?php echo $checked_filter_belum_sdm; ?>>Belum disetujui SDM
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input name='filter_batal' id='filter_batal' class='filter_status' type="checkbox" value="1" onclick='refresh_table_serverside()' <?php echo $checked_filter_batal; ?>>Dibatalkan Pemohon
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input name='filter_tolak_atasan' id='filter_tolak_atasan' class='filter_status' type="checkbox" value="1" onclick='refresh_table_serverside()' <?php echo $checked_filter_tolak_atasan; ?>>Tidak Disetujui Atasan
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input name='filter_tolak_sdm' id='filter_tolak_sdm' class='filter_status' type="checkbox" value="1" onclick='refresh_table_serverside()' <?php echo $checked_filter_tolak_sdm; ?>>Tidak Disetujui SDM
							</label>
						</div>
					</div>
					<?php if ($this->akses["persetujuan"]) { ?>
						<div class="col-lg-3">
							<button class="btn btn-success" data-toggle='modal' data-target='#modal_approve_all'>Setujui semua permohonan </button>
						</div>
					<?php } ?>
					<!-- <div class="col-lg-3">
									<label>Filter Tanggal Lembur</label>
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
												<input type="text" name="daterange" class="form-control filter_tanggal" value="" />
											</div>
										</div>
									</div>
								</div> -->
				</div>


				</label>
			</div>


			<div class="form-group">
				<div class="row">
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_cuti_sdm">
						<thead>
							<tr>
								<th class='text-center'>No</th>
								<th class='text-center'>NP</th>
								<th class='text-center'>Nama</th>
								<th class='text-center'>Tipe</th>
								<th class='text-center'>Start Date</th>
								<th class='text-center'>End Date</th>
								<th class='text-center'>Jumlah</th>
								<th class='text-center no-sort'>Alasan</th>
								<th class='text-center no-sort'>Persetujuan Atasan</th>
								<th class='text-center no-sort'>Persetujuan SDM</th>
								<th class='text-center no-sort'>Aksi</th>

							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
					<!-- /.table-responsive -->
				</div>
			</div>

			<!-- Modal Status -->
			<div class="modal fade" id="modal_status" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_status">Status <?php echo $judul; ?></h4>
						</div>
						<div class="modal-body">

							<table>
								<tr>
									<td>Np Pemohon</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a id="status_np_karyawan"></a></td>
								</tr>
								<tr>
									<td>Nama Pemohon</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a id="status_nama"></a></td>
								</tr>
								<tr>
									<td>Dibuat Tanggal</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a id="status_created_at"></a></td>
								</tr>
								<tr>
									<td>Dibuat Oleh</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a id="status_created_by"></a></td>
								</tr>
							</table>

							<br>

							<div class="alert alert-info">
								<strong><a id="status_approval_1_nama"></a></strong><br>
								<p id="status_approval_1_status"></p>
							</div>

							<div class="alert alert-info">
								<strong><a id="status_approval_2_nama"></a></strong><br>
								<p id="status_approval_2_status"></p>
							</div>

						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->



		<?php
		}

		if ($akses["persetujuan"]) {
		?>
			<!-- Modal -->
			<div class="modal fade" id="modal_persetujuan" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_batal"><?php echo $judul; ?></h4>
						</div>
						<div class="modal-body">
							<form role="form" action="<?php echo base_url(); ?>osdm/persetujuan_cuti_sdm/action_persetujuan_cuti_sdm" id="formulir_tambah" method="post">

								<table>
									<tr>
										<td>Np Pemohon</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="persetujuan_np_karyawan"></a></td>
									</tr>
									<tr>
										<td>Nama Pemohon</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="persetujuan_nama"></a></td>
									</tr>
									<tr>
										<td>Dibuat Tanggal</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="persetujuan_created_at"></a></td>
									</tr>
									<tr>
										<td>Dibuat Oleh</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="persetujuan_created_by"></a></td>
									</tr>
									<tr>
										<td>Jumlah Hari</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="persetujuan_jumlah_cuti"> Hari</a></td>
									</tr>
								</table>

								<br>

								<div class="alert alert-info">
									<strong><a id="persetujuan_approval_nama_sdm"></a></strong><br>
									Sebagai Approval Cuti oleh SDM
									<br>
									<select class="form-control" name='persetujuan_approval_sdm' id="persetujuan_approval_sdm" style="width: 150px;" onchange="form_alasan(this)">
										<option value='0'></option>
										<option value='1'>Setuju</option>
										<option value='2'>Tidak Setuju</option>
									</select>
								</div>

								<div id="form-alasan" style="display: none;">
									Alasan Tidak Disetujui
									<br>
									<textarea rows="2" class="form-control" name='persetujuan_alasan_sdm' id="persetujuan_alasan_sdm"></textarea>
								</div>
								<br>


								<div class="row">
									<div class="col-lg-12 text-right">

										<input type="hidden" name="persetujuan_id_sdm" id="persetujuan_id_sdm" class='persetujuan_id_sdm'>

										<input type="hidden" name="persetujuan_filter_belum" id="persetujuan_filter_belum" class="persetujuan_filter_belum">

										<input type="hidden" name="persetujuan_filter_atasan_1" id="persetujuan_filter_atasan_1" class="persetujuan_filter_atasan_1">

										<input type="hidden" name="persetujuan_filter_atasan_2" id="persetujuan_filter_atasan_2" class="persetujuan_filter_atasan_2">

										<input type="hidden" name="persetujuan_filter_sdm" id="persetujuan_filter_sdm" class="persetujuan_filter_sdm">

										<input type="hidden" name="persetujuan_filter_belum_sdm" id="persetujuan_filter_belum_sdm" class="persetujuan_filter_belum_sdm">

										<input type="hidden" name="persetujuan_filter_batal" id="persetujuan_filter_batal" class="persetujuan_filter_batal">

										<input type="hidden" name="persetujuan_filter_tolak_atasan" id="persetujuan_filter_tolak_atasan" class="persetujuan_filter_tolak_atasan">

										<input type="hidden" name="persetujuan_filter_tolak_sdm" id="persetujuan_filter_tolak_sdm" class="persetujuan_filter_tolak_sdm">

										<input type="hidden" name="persetujuan_jumlah_cuti1" id="persetujuan_jumlah_cuti1" class="persetujuan_jumlah_cuti1">

										<input type="submit" name="submit" id='persetujuan_button' value="Submit" class="btn btn-block btn-success">
									</div>
								</div>
							</form>

						</div>

					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->

			<!-- Modal -->
			<div class="modal fade" id="modal_approve_all" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_batal">Approve Semua Permohonan</h4>
							<p>Permohonan cuti yang telah disetujui Atasan 2 akan disetujui</p>
						</div>
						<div class="modal-body">
							<form role="form" action="<?php echo base_url(); ?>osdm/persetujuan_cuti_sdm/action_persetujuan_cuti_sdm_all" id="formulir_tambah" method="post">



								<div class="alert alert-info">
									<strong><strong><?php echo $_SESSION["no_pokok"] ?> | <?php echo nama_karyawan_by_np($_SESSION["no_pokok"]) ?> </strong><br></strong>
									Sebagai Approval Cuti oleh SDM
									<br>
									<p>Anda akan menyetujui semua Permohonan Cuti yang telah diapprove oleh <strong>kedua atasan (atasan 1 & atasan 2)</strong> atau <strong>atasan 1 (jika tidak mempunyai atasan 2)</strong></p>
								</div>


								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Bulan</label>
										</div>
										<div class="col-lg-12">
											<select class="form-control" id='bulan_tahun' name='bulan_tahun' style="width: 100%;" required>
												<option value=''></option>
												<?php
												foreach ($array_tahun_bulan as $value) {

													$olah 	= explode('-', $value);
													$bulan 	= $olah[0];
													$tahun	= $olah[1];

													$tampil_tanggal = $tahun . "-" . $bulan . "-01";

													$nama_bulan = bulan($tampil_tanggal);

													$sudah_cutoff = sudah_cutoff($tampil_tanggal);

													if ($sudah_cutoff) {
														$disabled = 'disabled';
														$tanggal_cutoff = ", Submit ERP pada " . tanggal_indonesia($sudah_cutoff);
													} else {
														$disabled = '';
														$tanggal_cutoff = '';
													}

												?>
													<option value='<?php echo $value ?>' <?php echo $disabled; ?>><?php echo id_to_bulan(substr($value, 0, 2)) . " " . substr($value, 3, 4) . "" . $tanggal_cutoff ?></option>

												<?php
												}
												?>
											</select>

										</div>
									</div>
								</div>



								<div class="row">
									<div class="col-lg-12 text-right">

										<input type="hidden" name="persetujuan_id_sdm" id="persetujuan_id_sdm" class='persetujuan_id_sdm'>

										<input type="hidden" name="persetujuan_filter_belum" id="persetujuan_filter_belum" class="persetujuan_filter_belum">

										<input type="hidden" name="persetujuan_filter_atasan_1" id="persetujuan_filter_atasan_1" class="persetujuan_filter_atasan_1">

										<input type="hidden" name="persetujuan_filter_atasan_2" id="persetujuan_filter_atasan_2" class="persetujuan_filter_atasan_2">

										<input type="hidden" name="persetujuan_filter_sdm" id="persetujuan_filter_sdm" class="persetujuan_filter_sdm">

										<input type="hidden" name="persetujuan_filter_belum_sdm" id="persetujuan_filter_belum_sdm" class="persetujuan_filter_belum_sdm">

										<input type="hidden" name="persetujuan_filter_batal" id="persetujuan_filter_batal" class="persetujuan_filter_batal">

										<input type="hidden" name="persetujuan_filter_tolak_atasan" id="persetujuan_filter_tolak_atasan" class="persetujuan_filter_tolak_atasan">

										<input type="hidden" name="persetujuan_filter_tolak_sdm" id="persetujuan_filter_tolak_sdm" class="persetujuan_filter_tolak_sdm">

										<input type="hidden" name="persetujuan_jumlah_cuti" id="persetujuan_jumlah_cuti" class="persetujuan_jumlah_cuti">

										<br>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-12">
													<input type="submit" name="submit" id='persetujuan_button' value="Submit" class="btn btn-block btn-success">
												</div>
											</div>
										</div>
									</div>
								</div>
							</form>

						</div>

					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->
		<?php
		}

		?>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<!-- Untuk filter tanggal -->


<script type="text/javascript" src="<?= base_url('asset/moment.js/2.29.1/moment.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>
<script>
	$(document).ready(function() {
		$('input[name="daterange"]').daterangepicker({
			opens: 'left',
			autoUpdateInput: false,
			locale: {
				cancelLabel: 'Clear',
				format: 'DD-MM-YYYY'
			}
		});
		$('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
			$(this).val(picker.startDate.format('DD-MM-YYYY') + ' s/d ' + picker.endDate.format('DD-MM-YYYY'));
			ev.preventDefault();
			refresh_table_serverside();
		});
		$('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
			$(this).val('');
			ev.preventDefault();
			refresh_table_serverside();
		});
	});
</script>
<!-- Untuk filter tanggal -->

<script type="text/javascript">
	$(document).ready(function() {
		table_serverside();
	});

	function refresh_table_serverside() {
		$('#tabel_ess_cuti_sdm').DataTable().destroy();
		table_serverside();
	}

	function form_alasan(obj) {
		var selectBox = obj;
		var selected = selectBox.options[selectBox.selectedIndex].value;
		var textarea = document.getElementById("form-alasan");

		if (selected === '2') {
			textarea.style.display = "block";
		} else {
			textarea.style.display = "none";
		}
	}

	$("#modal_persetujuan").on("hidden.bs.modal", function() {
		var textarea = document.getElementById("form-alasan");
		textarea.style.display = "none";
		$('#persetujuan_alasan_sdm').val('');
	});
</script>

<script type="text/javascript">
	function table_serverside() {
		var table;

		var filter_belum = $('#filter_belum:checked').val();
		var filter_atasan_1 = $('#filter_atasan_1:checked').val();
		var filter_atasan_2 = $('#filter_atasan_2:checked').val();
		var filter_sdm = $('#filter_sdm:checked').val();
		var filter_belum_sdm = $('#filter_belum_sdm:checked').val();
		var filter_batal = $('#filter_batal:checked').val();
		var filter_tolak_atasan = $('#filter_tolak_atasan:checked').val();
		var filter_tolak_sdm = $('#filter_tolak_sdm:checked').val();
		// var filter_tanggal      = $('input[name="daterange"]').val();

		$(".persetujuan_filter_belum").val(filter_belum);
		$(".persetujuan_filter_atasan_1").val(filter_atasan_1);
		$(".persetujuan_filter_atasan_2").val(filter_atasan_2);
		$(".persetujuan_filter_sdm").val(filter_sdm);
		$(".persetujuan_filter_belum_sdm").val(filter_belum_sdm);
		$(".persetujuan_filter_batal").val(filter_batal);
		$(".persetujuan_filter_tolak_atasan").val(filter_tolak_atasan);
		$(".persetujuan_filter_tolak_sdm").val(filter_tolak_sdm);

		table = $('#tabel_ess_cuti_sdm').DataTable({

			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
				"sEmptyTable": "Tidak ada data di database",
				"emptyTable": "Tidak ada data di database"
			},

			"stateSave": true,
			"responsive": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.

			// Load data for the table's content from an Ajax source
			"ajax": {

				"url": "<?php echo site_url("osdm/persetujuan_cuti_sdm/tabel_ess_cuti_sdm/") ?>" + filter_belum + "/" + filter_atasan_1 + "/" + filter_atasan_2 + "/" + filter_sdm + "/" + filter_belum_sdm + "/" + filter_batal + "/" + filter_tolak_atasan + "/" + filter_tolak_sdm,
				//  "data"	: {daterange: $('input[name="daterange"]').val()},				 
				"type": "POST"

			},

			//Set column definition initialisation properties.
			"columnDefs": [{
				"targets": 'no-sort', //first column / numbering column
				"orderable": false, //set not orderable

			}, ],

		});

	};
</script>

<script>
	$(document).on("click", '.status_button', function(e) {
		var status_np_karyawan = $(this).data('np-karyawan');
		var status_nama = $(this).data('nama');
		var status_created_at = $(this).data('created-at');
		var status_created_by = $(this).data('created-by');
		var status_approval_1_nama = $(this).data('approval-1-nama');
		var status_approval_1_status = $(this).data('approval-1-status');
		var status_approval_2_nama = $(this).data('approval-2-nama');
		var status_approval_2_status = $(this).data('approval-2-status');

		$("#status_np_karyawan").text(status_np_karyawan);
		$("#status_nama").text(status_nama);
		$("#status_created_at").text(status_created_at);
		$("#status_created_by").text(status_created_by);
		$("#status_approval_1_nama").text(status_approval_1_nama);
		$("#status_approval_1_status").text(status_approval_1_status);
		$("#status_approval_2_nama").text(status_approval_2_nama);
		$("#status_approval_2_status").text(status_approval_2_status);



	});
</script>

<script>
	$(document).on("click", '.persetujuan_button', function(e) {
		var persetujuan_id_sdm = $(this).data('id');
		var persetujuan_np_karyawan = $(this).data('np-karyawan');
		var persetujuan_nama = $(this).data('nama');
		var persetujuan_created_at = $(this).data('created-at');
		var persetujuan_created_by = $(this).data('created-by');
		var persetujuan_approval_nama_sdm = $(this).data('approval-nama-sdm');
		var persetujuan_approval_sdm = $(this).data('approval-sdm');
		var persetujuan_jumlah_cuti = $(this).data('jumlah-cuti');
		var persetujuan_jumlah_cuti1 = $(this).data('jumlah-cuti');

		$("#persetujuan_id_sdm").val(persetujuan_id_sdm);
		$("#persetujuan_np_karyawan").text(persetujuan_np_karyawan);
		$("#persetujuan_nama").text(persetujuan_nama);
		$("#persetujuan_created_at").text(persetujuan_created_at);
		$("#persetujuan_created_by").text(persetujuan_created_by);
		$("#persetujuan_approval_nama_sdm").text(persetujuan_approval_nama_sdm);

		//untuk menampilkan data jumlah cuti
		$("#persetujuan_jumlah_cuti").text(persetujuan_jumlah_cuti);
		document.getElementById("persetujuan_jumlah_cuti1").value = persetujuan_jumlah_cuti1;
		//untuk menampilkan data jumlah cuti

		document.getElementById("persetujuan_approval_sdm").value = persetujuan_approval_sdm;


		//if(persetujuan_approval_sdm===0)
		//	{
		//		$('#persetujuan_approval_sdm').prop('disabled', false);
		//		$('#persetujuan_button').prop('disabled', false);
		//	}




	});
</script>