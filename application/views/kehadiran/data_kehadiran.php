<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url() ?>asset/toastr-2.1.4/toastr.min.css">
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" />

<style>
	#slider-range{
		box-shadow: 0 -1px #eaeaea, 0 1px #fff, inset 0 1px 4px #8c8c8c;
		background: linear-gradient(#c3c3c3, #f1f1f1);
		border-radius: 2.25em;
		height: 25px;
		margin-bottom: 30px;
	}

	#slider-range-by-date-range{
		box-shadow: 0 -1px #eaeaea, 0 1px #fff, inset 0 1px 4px #8c8c8c;
		background: linear-gradient(#c3c3c3, #f1f1f1);
		border-radius: 2.25em;
		height: 25px;
		margin-bottom: 30px;
	}
	.ui-widget-header{
		box-shadow: inset 0 1px 4px #8c8c8c;
		background: linear-gradient(#f8dd36, #d68706) no-repeat, linear-gradient(#efefef, #c9c9c9) !important;
	}
	.ui-slider-handle{
		height: 35px !important;
		width: 35px !important;
		cursor: ew-resize !important;
		display: flex;
		justify-content: center;
		align-items: center;
		position: relative;
		border-radius: 50% !important;
		box-shadow: -2px -2px 7px rgb(151 151 151 / 45%), 5px 5px 9px rgba(94,104,121,0.3);
	}
	.ui-slider-handle:nth-child(3){
		transform: translateX(-20px) !important;
	}
	.ui-slider-handle::after{
		content: '';
		position: absolute;
		height: 70%;
		width: 70%;
		background-color: transparent;
		border-radius: 50%;
		box-shadow: inset -3px -3px 7px rgb(217 217 217 / 0.5), inset 3px 3px 7px rgba(94,104,121,0.5);
	}

	/* tabel */
	

	#anomaly_tbl {
		font-family: Arial, Helvetica, sans-serif;
		border-collapse: collapse;
		width: 100%;
	}

	#anomaly_tbl td, #anomaly_tbl th {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: center;
	}

	#anomaly_tbl tr:nth-child(even){background-color: #f2f2f2;}

	#anomaly_tbl tr:hover {background-color: #ddd;}

	#anomaly_tbl th {
		padding-top: 12px;
		padding-bottom: 12px;
		text-align: center;
		background-color: #04AA6D;
		color: white;
	}

	.no-link{
		color: blue;
		text-decoration: underline;
		cursor: pointer;
	}

	.swal2-popup{
		transform: scale(1.3);
	}

	.loading_custom_anomali{
		display: none;
		position: fixed;
		top: 0px;
		left: 0px;
		width: 100vw;
		height: 100vh;
		background: rgba(0, 0, 0, 0.5);
		align-items: center;
		justify-content: center;
		z-index: 99999;
		flex-direction: column;
		gap: 40px;
	}

	.loading_custom_anomali > .loading-text{
		background: #CE2827;
		color: white;
		padding: 10px 20px;
		border-radius: 15px;
		font-weight: 900;
	}
</style>

<style>
	@-webkit-keyframes rotating /* Safari and Chrome */ {
		from {
			-webkit-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		to {
			-webkit-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		}
		}
		@keyframes rotating {
		from {
			-ms-transform: rotate(0deg);
			-moz-transform: rotate(0deg);
			-webkit-transform: rotate(0deg);
			-o-transform: rotate(0deg);
			transform: rotate(0deg);
		}
		to {
			-ms-transform: rotate(360deg);
			-moz-transform: rotate(360deg);
			-webkit-transform: rotate(360deg);
			-o-transform: rotate(360deg);
			transform: rotate(360deg);
		}
		}
		.rotating {
		-webkit-animation: rotating 2s linear infinite;
		-moz-animation: rotating 2s linear infinite;
		-ms-animation: rotating 2s linear infinite;
		-o-animation: rotating 2s linear infinite;
		animation: rotating 2s linear infinite;
	}
</style>
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
		if (!empty($this->session->flashdata('warning'))) {
		?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('warning'); ?>
			</div>
		<?php
		} ?>
		
		<?php if ($akses["lihat log"]) { ?>
			<div class='row' style='display: flex; justify-content: end; margin-bottom: 20px;'>;
				<div class="dropdown">
					<button class="btn btn-primary btn-md dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						Lihat Log
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
						<li onclick="lihat_log()"><a href="#">Cek Logs</a></li>
						<li onclick="cek_anomaly()"><a href="#">Cek Anomaly</a></li>
					</ul>
				</div>
			</div>

			<div class="modal fade" id="modal_anomali" tabindex="-1" role="dialog" aria-labelledby="label_modal_anomali" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_anomali">Anomali Data Bulan - 2024</h4>
						</div>
						<div class="modal-body" >
							test aja
						</div>
						<div class="modal-footer">
							<button type="submit" class="btn btn-danger" onclick="getDataAnomaly()">
								<i class="fa fa-refresh"></i>
								Segarkan Data
							</button>
							<button type="submit" class="btn btn-info" onclick="download_anomaly_data()">
								<i class="fa fa-download"></i>
								Download Semua Data
							</button>
							<button onclick="perbaiki_semua_data_anomali()" type="submit" class="btn btn-success">
								<i class="fa fa-check"></i>
								Perbaiki Semua Data
							</button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<div class="loading_custom_anomali">
				<img src="<?php base_url()?>/ess/asset/cartenz/loading-29.gif" style="width: 150px;">

				<div class="loading-text">
					Memuat data ...
				</div>
			</div>
		<?php } ?>
		
		<?php if ($akses["tambah"]) {
		?>
			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul; ?></a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse">
						<div class="panel-body">

							<form role="form" action="<?php echo base_url(); ?>kehadiran/data_kehadiran/action_insert_data_kehadiran" id="formulir_tambah" method="post">

								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Karyawan</label>
										</div>
										<div class="col-lg-7">
											<select class="form-control select2" id='insert_np_karyawan' name='insert_np_karyawan' onChange="getNama()" style="width: 100%;" required>
												<option value=''></option>
												<?php
												foreach ($array_daftar_karyawan->result_array() as $value) {
												?>
													<option value='<?php echo $value['no_pokok'] ?>'><?php echo $value['no_pokok'] . " " . $value['nama'] ?></option>

												<?php
												}
												?>
											</select>


										</div>

									</div>
								</div>

								<input type='hidden' class="form-control" name="insert_nama" id="insert_nama" readonly required>

								<?php
								$bulan_lalu = $data_tanggal	= date('Y-m-d', strtotime('-1 months', strtotime(date('Y-m-d'))));
								$sudah_cutoff = sudah_cutoff($bulan_lalu);

								if ($sudah_cutoff) {
									$min = date('Y-m') . "-01";
								} else {
									$min = '';
								}

								?>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Tertanggal</label>
										</div>
										<div class="col-lg-7">
											<input type="text" class="form-control" name="insert_dws_tanggal" id="insert_dws_tanggal" required>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Jadwal Kerja</label>
										</div>
										<div class="col-lg-7">
											<select class="form-control" id='insert_dws_id' name='insert_dws_id' style="width: 200px;" required>
												<option value=''></option>
												<?php
												foreach ($array_jadwal_kerja->result_array() as $value) {
												?>
													<option value='<?php echo $value['id'] ?>'><?php echo $value['description'] ?></option>

												<?php
												}
												?>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Berangkat</label>
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="insert_tapping_fix_1_date" id="insert_tapping_fix_1_date" required>
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control datetimepicker5" name="insert_tapping_fix_1_time" id="insert_tapping_fix_1_time" required>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Pulang</label>
										</div>
										<div class="col-lg-4">
											<input type="text" class="form-control" name="insert_tapping_fix_2_date" id="insert_tapping_fix_2_date" required>
										</div>
										<div class="col-lg-3">
											<input type="text" class="form-control datetimepicker5" name="insert_tapping_fix_2_time" id="insert_tapping_fix_2_time" required>
										</div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-9 text-right">
										<input type='hidden' id='insert_tampil_bulan_tahun' name='insert_tampil_bulan_tahun'>
										<input type="submit" name="submit" value="submit" class="btn btn-primary">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<!-- /.col-lg-12 -->
			</div>
			<!-- /.row -->

			<!-- Modal NP -->
			<div class="modal fade" id="modal_np" tabindex="-1" role="dialog" aria-labelledby="label_modal_np" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_np">Daftar List NP <?php echo $judul; ?></h4>
						</div>
						<div class="modal-body" align='center'>
							<textarea name='list_np' id='list_np' rows="10" cols="50" readonly></textarea>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->
		<?php
		}

		if ($this->akses["lihat"]) {
		?>


			<p id="demo"></p>
			<div class="form-group">
				<div class="row">
					<div class="col-md-3">
						<label>Bulan</label>
						<!--<select id="pilih_bulan_tanggal" class="form-control">-->
						<select class="form-control" id='bulan_tahun' name='bulan_tahun' onchange="refresh_table_serverside()" style="width: 200px;">
							<option value=''></option>
							<?php
							$tampil_bulan_tahun = date("m-Y");
							foreach ($array_tahun_bulan as $value) {

								if (!empty($this->session->flashdata('tampil_bulan_tahun'))) {
									$tampil_bulan_tahun = $this->session->flashdata('tampil_bulan_tahun');
								}
								if ($tampil_bulan_tahun == $value) {
									$selected = 'selected';
								} else {
									$selected = '';
								}
							?>
								<option value='<?php echo $value ?>' <?php echo $selected; ?>><?php echo id_to_bulan(substr($value, 0, 2)) . " " . substr($value, 3, 4) ?></option>

							<?php
							}
							?>
						</select>

					</div>
					<div class="col-md-8">
						<div style="padding-top: 25px">
							<button type="button" onClick="otoritas()" class="btn btn-success"><i class="fa fa-print"></i> Cetak Per NP</button>
							<button type="button" onClick="otoritas_per_unit()" class="btn btn-success"><i class="fa fa-print"></i> Cetak Per Unit</button>
							<?php if (in_array($_SESSION["grup"], [1, 3, 4])) { ?>
								<button type="button" onClick="cetak_all_unit()" class="btn btn-success"><i class="fa fa-print"></i> Cetak All Unit</button>
							<?php } ?>
						</div>
						<!--begin: Modal Inactive -->
						<div class="modal fade" id="show_otoritas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-md" role="document">
								<form method="post" target="_blank" action="<?php echo base_url('kehadiran/data_kehadiran/cetak') ?>">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Cetak Per NP</h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="bulan" value="" id="get_month" />
											<select multiple="multiple" class="form-control select2" id="multi_select" name='np_karyawan[]' style="width: 100%;" required>
												<?php foreach ($array_daftar_karyawan->result_array() as $val) { ?>
													<option value='<?php echo $val['no_pokok'] ?>'><?php echo $val['no_pokok'] . " " . $val['nama'] ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-success">Cetak</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<div class="modal fade" id="show_otoritas_per_unit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-md" role="document">
								<form method="post" target="_blank" action="<?php echo base_url('kehadiran/data_kehadiran/cetak_per_unit') ?>">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Cetak Per Unit</h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="bulan" value="" id="get_month_per_unit" />
											<select multiple="multiple" class="form-control select2" id="multi_select_per_unit" name='kode_unit[]' style="width: 100%;" required>
												<?php foreach ($array_daftar_unit->result_array() as $val) { ?>
													<option value='<?php echo $val['kode_unit'] ?>'><?php echo $val['kode_unit'] . " " . $val['nama_unit'] ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-success">Cetak</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</div>
								</form>
							</div>
						</div>

					</div>
				</div>
			</div>



			<div class="form-group">
				<div class="row">
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data_kehadiran">
						<thead>
							<tr>
								<th class='text-center'>No</th>
								<th class='text-center no-sort'>KODE UNIT</th>
								<th class='text-center'>NP</th>
								<th class='text-center'>NAMA</th>
								<th class='text-center'>Tertanggal</th>
								<th class='text-center'>Jadwal Kerja</th>
								<th class='text-center' width='15%'>Berangkat</th>
								<th class='text-center' width='15%'>Pulang</th>
								<th class='text-center no-sort' width='5%'>Ket</th>
								<th class='text-center no-sort'>Aksi</th>

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

		if ($akses["ubah"]) {
		?>
			<!-- Modal -->
			<div class="modal fade" id="modal_ubah" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content" style="overflow-y: initial !important">

						<form enctype="multipart/form-data" method="POST" accept-charset="utf-8" action="<?php echo base_url('kehadiran/data_kehadiran/action_update_data_kehadiran'); ?>" method="post" id="form-ubah">

							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul; ?></h4>
							</div>
							<div class="modal-body" style="height: 500px;overflow-y: auto;">

								<div class="form-group row">
									<div class="col-lg-2">
										<label>NP</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control" name="edit_np_karyawan" id="edit_np_karyawan" readonly>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-2">
										<label>Nama</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control" name="edit_nama" id="edit_nama" readonly>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-2">
										<label>Tertanggal</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control tanggal_mdy" name="edit_dws_tanggal" id="edit_dws_tanggal" readonly>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-2">
										<label>DWS</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control" name="edit_dws_name" id="edit_dws_name" readonly>
									</div>


								</div>

								<div class="form-group row">
									<div class="col-lg-2">
										<label>Berangkat</label>
									</div>
									<div class='col-lg-4'>
										<input type="text" class="form-control" name="edit_tapping_1_date" id="edit_tapping_1_date">
										<small class="form-text text-muted">dd-mm-yyyy</small>

									</div>
									<div class='col-lg-3'>
										<input type="text" class="form-control datetimepicker5" name="edit_tapping_1_time" id="edit_tapping_1_time">
										<small class="form-text text-muted">hh:mm</small>
									</div>
									<div class='col-lg-2'>
										<small class="form-text text-muted" id='edit_keterangan'></small>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-lg-2">
										<label>Pulang</label>
									</div>
									<div class='col-lg-4'>
										<input type="text" class="form-control" name="edit_tapping_2_date" id="edit_tapping_2_date">
										<small class="form-text text-muted">dd-mm-yyyy</small>
									</div>
									<div class='col-lg-3'>
										<input type="text" class="form-control datetimepicker5" name="edit_tapping_2_time" id="edit_tapping_2_time">
										<small class="form-text text-muted">hh:mm</small>
									</div>
								</div>

								<!--<div class="form-group row">
												<div class="col-lg-2">
													<label>Approval</label>
												</div>
												<div class="col-lg-10">
													<select style='width:100%;' class="form-control select2 edit_approval" id="edit_approval" name="edit_approval[]" required></select>
													
												</div>											                     
											</div>-->


								<div class="form-group row">
									<div class="col-lg-2">
										<label>Approval</label>
									</div>
									<div class="col-lg-4">
										<input class="form-control" name="edit_approval" id="edit_approval" value="" onChange="getNamaAtasan()" required>
										<!-- <select class="form-control select2" name="edit_approval" id="edit_approval" style="width: 100%;" required></select> -->
									</div>
									<!-- START: heru menambahkan ini 2020-11-14 @08:00 -->
									<div class="col-lg-6">
										<input type="checkbox" id="asdf" onchange="use_last_approver()"><label for="asdf">&nbsp;Gunakan data sebelumnya?</label>
									</div>
									<!-- END: heru menambahkan ini 2020-11-14 @08:00 -->
								</div>

								<div class="form-group row">
									<div class="col-lg-2">
										<label></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" name="approval_input" id="approval_input" value="" readonly required>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-lg-2">
										<label></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" name="approval_input_jabatan" id="approval_input_jabatan" required><small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>
									</div>
								</div>

								<div class="form-group row">
									<div class="col-lg-2">
										<label>Alasan Perubahan</label>
									</div>
									<div class='col-lg-10'>
										<input type="text" class="form-control" name="edit_tapping_fix_approval_ket" id="edit_tapping_fix_approval_ket" required>
									</div>
								</div>

								<div class="form-group row" style="background-color:yellow">
									<div class="col-lg-2">
										<label>Work From Home</label>
									</div>
									<div class='col-lg-10'>
										<input type="checkbox" id="edit_wfh" name="edit_wfh" value="1" onclick='wfh()'>
										<label for="edit_wfh"> Saya berkomitmen untuk bekerja dari rumah</label>
										<br>

										<img class='wfh_foto' id="preview_wfh_foto_1" src="" style="width:50px">
										<input type='hidden' id="hidden_wfh_foto_1" value="">
										<br class='wfh_foto'>
										<input type="file" id="edit_wfh_foto_1" name="edit_wfh_foto[]" style="display: none;" />
										<label class='wfh_foto'> Evidence Berangkat</label>
										<br class='wfh_foto'>

										<br class='wfh_foto'>
										<img class='wfh_foto' id="preview_wfh_foto_2" src="" style="width:50px">
										<input type='hidden' id="hidden_wfh_foto_2" value="">
										<br class='wfh_foto'>

										<input type="file" id="edit_wfh_foto_2" name="edit_wfh_foto[]" style="display: none;" />
										<label class='wfh_foto'> Evidence Pulang</label>

										<br class='wfh_foto'>
										<small class="form-text text-muted wfh_foto">Maksimal Upload 1 MB, dengan format jpg/jpeg</small>

										<input type="hidden" id="edit_assesment_form" value="0" />
									</div>
								</div>

								<div class="modal-footer">.
									<input type='hidden' id='edit_id' name='edit_id'>
									<input type='hidden' id='edit_tampil_bulan_tahun' name='edit_tampil_bulan_tahun'>
									<input type="button" name="btn" value="Health Passport" id="submitwfh" onclick="cek_wfh()" class="btn btn-success" style="display: none;" />
									<button name='submit' type="submit" value='submit' id="submitnonwfh" class="btn btn-primary">Simpan</button>
									<!-- onclick='return cek_wfh()'  -->
									<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
								</div>


							</div>

						</form>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->

			<!-- Modal -->
			<div class="modal fade" id="modal-assesment" role="dialog">
				<div class="modal-dialog">

					<!-- Modal content-->
					<div class="modal-content">
						<form role="form" action="#" method="post" id="formulir_tambah_assesment">

							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Health Passport</h4>
								<span id="tanggal_kehadiran_assesment"></span>
							</div>
							<div class="modal-body">

								<div id="alert_form"></div>

								<input type="hidden" name="tanggal_kehadiran" id="tanggal_kehadiran">


								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label>Apakah pernah keluar rumah/tempat umum (pasar, fasyankes, kerumunan orang, dll) ?</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label class="radio-inline">
												<input type="radio" name="pernah_keluar" id="pernah_keluar1" value="1" required>Ya
											</label>
											<label class="radio-inline">
												<input type="radio" name="pernah_keluar" id="pernah_keluar2" value="2">Tidak
											</label>

										</div>
										<!--							
													<div class="col-lg-1">
														 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
													</div>
													-->
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label>Apakah pernah menggunakan transportasi umum ?</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label class="radio-inline">
												<input type="radio" name="transportasi_umum" id="transportasi_umum1" value="1" required>Ya
											</label>
											<label class="radio-inline">
												<input type="radio" name="transportasi_umum" id="transportasi_umum2" value="2">Tidak
											</label>

										</div>
										<!--							
													<div class="col-lg-1">
														 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
													</div>
													-->
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label>Apakah pernah melakukan perjalanan ke luar kota/internasional (wilayah yang terjangkit/zona merah) ?</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label class="radio-inline">
												<input type="radio" name="luar_kota" id="luar_kota1" value="1" required>Ya
											</label>
											<label class="radio-inline">
												<input type="radio" name="luar_kota" id="luar_kota2" value="2" required>Tidak
											</label>

										</div>
										<!--							
													<div class="col-lg-1">
														 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
													</div>
													-->
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label>Apakah anda mengikuti kegiatan yang melibatkan orang banyak ?</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label class="radio-inline">
												<input type="radio" name="kegiatan_orang_banyak" id="kegiatan_orang_banyak1" value="1" required>Ya
											</label>
											<label class="radio-inline">
												<input type="radio" name="kegiatan_orang_banyak" id="kegiatan_orang_banyak2" value="2">Tidak
											</label>

										</div>
										<!--							
													<div class="col-lg-1">
														 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
													</div>
													-->
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label>Apakah memiliki riwayat kontak erat dengan orang yang dinyatakan ODP, PDP, atau Confirm COVID-19 ?</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label class="radio-inline">
												<input type="radio" name="kontak_pasien" id="kontak_pasien1" value="1" required>Ya
											</label>
											<label class="radio-inline">
												<input type="radio" name="kontak_pasien" id="kontak_pasien2" value="2">Tidak
											</label>

										</div>
										<!--							
													<div class="col-lg-1">
														 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
													</div>
													-->
									</div>
								</div>

								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label>Apakah pernah mengalami demam/batuk/pilek/sakit tenggorokan/sesak nafas dalam 14 hari terakhir ?</label>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<label class="radio-inline">
												<input type="radio" name="sakit" id="sakit1" value="1" required>Ya
											</label>
											<label class="radio-inline">
												<input type="radio" name="sakit" id="sakit2" value="2">Tidak
											</label>

										</div>
										<!--							
													<div class="col-lg-1">
														 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
													</div>
													-->
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<input name="submit" value="submit" class="btn btn-primary" id="SubForm">
								<!-- onclick="return save_assesment()" -->
							</div>
						</form>
					</div>

				</div>
			</div>
		<?php
		}

		if ($akses["ubah kode unit"]) {
		?>
			<!-- Modal -->
			<div class="modal fade" id="modal_ubah_kode_unit" role="dialog" aria-labelledby="label_modal_ubah_kode_unit" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form method="post" action="<?php echo base_url(); ?>kehadiran/data_kehadiran/action_update_kode_unit">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_ubah">Ubah Kode Unit<?php echo $judul; ?></h4>
							</div>
							<div class="modal-body">

								<div class="form-group row">
									<div class="col-lg-2">
										<label>NP</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control" name="kd_np_karyawan" id="kd_np_karyawan" readonly>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-2">
										<label>Nama</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control" name="kd_nama" id="kd_nama" readonly>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-2">
										<label>Tertanggal</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control tanggal_mdy" name="kd_dws_tanggal" id="kd_dws_tanggal" readonly>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-2">
										<label>DWS</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control" name="kd_dws_name" id="kd_dws_name" readonly>
									</div>


								</div>

								<div class="form-group row">
									<div class="col-lg-2">
										<label>Kode Unit</label>
									</div>
									<div class="col-lg-10">
										<select class="form-control" id="kd_kode_unit" name='kd_kode_unit' style="width: 100%;" required>
											<?php foreach ($array_daftar_unit->result_array() as $val) { ?>
												<option value='<?php echo $val['kode_unit'] ?>'><?php echo $val['kode_unit'] . " " . $val['nama_unit'] ?></option>
											<?php } ?>
										</select>
									</div>
								</div>

								<div class="modal-footer">.
									<input type='hidden' id='kd_id' name='kd_id'>
									<input type='hidden' id='kd_tampil_bulan_tahun' name='kd_tampil_bulan_tahun'>
									<button name='submit' type="submit" value='submit' class="btn btn-primary">Simpan</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
								</div>
							</div>
						</form>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->
		<?php
		} ?>

		<!-- modal range tanggal -->
		<div class="modal fade" id="modal-export-excel" tabindex="-1" role="dialog" aria-labelledby="label-modal-export-excel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="label-modal-export-excel">Pilih Rentang Tanggal</h4>
					</div>
					<div class="modal-body">
						<div class="form-group" style="margin-bottom: 20px;">
							<label for="date-range-export">Rentang Tanggal Data :</label>
							<input class="form-control" id="date-range-export" name="dates" style="width: 100%;">
						</div>
						<div style="margin-bottom: 20px;">
							<label for="days-slider">Bagi File Setiap  : </label>
							<input type="range" id="days-slider" min="1" max="10" value="3" />
							<div style="text-align: center;" id="days-value">3 hari</div>
						</div>
						<div>
							<label for="days-slider">Progress Download File : </label>
							<progress id="download-progress" value="0" max="100" style="width: 100%;"></progress>
							<div style="text-align: center;" id="progress-text">0%</div>
						</div>
						<div class="list_download_wrapper" style="display: none; margin-top:20px; padding: 20px 10px; background: #ededed; border-radius: 10px;">
							<ul class="list_download">
							</ul>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-success" id="generate-export-excel">Export</button>
					</div>
				</div>
			</div>
		</div>
		<!-- END: modal range tanggal -->
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script src="<?php echo base_url('asset/select2') ?>/select2.min.js"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>
<script src="<?= base_url() ?>asset/toastr-2.1.4/toastr.min.js"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js') ?>"></script>
<script src="<?= base_url('asset/js/') ?>jszip.min.js"></script>

<script type="text/javascript">
	//alert("Mulai tertanggal 11 April 2019 diberlakukan approval untuk perubahan kehadiran karyawan.");


	$('#multi_select').select2({
		closeOnSelect: false
		//minimumResultsForSearch: 20
	});
	$('#multi_select_per_unit').select2({
		closeOnSelect: false
		//minimumResultsForSearch: 20
	});
	$(document).ready(function() {
		$('#date-range-export').daterangepicker({
			locale: {
				format: 'DD-MM-YYYY'
			},
			startDate: moment().startOf('month').format('DD-MM-YYYY'),
			endDate: moment().endOf('month').format('DD-MM-YYYY'),
			// endDate: moment().startOf('month').add(9, 'days').endOf('day').format('DD-MM-YYYY')
		});

		/* $('#date-range-export').on('apply.daterangepicker', function(ev, picker) {
			// Get the selected start date
			var startDate = picker.startDate;
			var endDate = picker.endDate;
			var endOfMonth = moment(startDate).endOf('month');

			// Calculate the maximum end date based on the start date
			var maxEndDate = moment(startDate).add(9, 'days').endOf('day');

			// Set the new maximum end date
			if (endDate > maxEndDate) {
				if (maxEndDate > endOfMonth) picker.setEndDate(endOfMonth);
				else picker.setEndDate(maxEndDate);
			} else if (endDate > endOfMonth) picker.setEndDate(endOfMonth);
		}); */

		//lempar ke modal cetak
		document.getElementById('get_month').value = $('#bulan_tahun').val();
		document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
		/*document.getElementById('get_month_all_unit').value = $('#bulan_tahun').val();*/

		$('.datetimepicker5').datetimepicker({
			format: 'HH:mm'
		}).on('dp.change', function(event) {
			wfh();
		});

		$('.tanggal_mdy').datetimepicker({
			format: 'D-MM-Y'
		}).on('dp.change', function(event) {
			wfh();
		});

		$(function() {
			$('#insert_dws_tanggal').datetimepicker({
				format: 'D-MM-Y',
				<?php if (@$minDate) { ?>
					minDate: '<?php echo $minDate; ?>',
				<?php }
				if (@$maxDate) { ?>
					maxDate: '<?php echo $maxDate; ?>',
				<?php } ?>
			});

			$("#insert_dws_tanggal").on("dp.change", function(e) {
				var newDate = $('#insert_dws_tanggal').val();
				var datebaru = new Date(newDate.split("-").reverse().join("-"));
				var datebaru_plus = new Date(newDate.split("-").reverse().join("-"));
				var datebaru_minus = new Date(newDate.split("-").reverse().join("-"));

				//Ketika tanggal 1 dia jadi 0 kalo dikurangi 1
				//var tambah = (datebaru.getDate()+1)+'-'+(datebaru.getUTCMonth()+1)+'-'+datebaru.getUTCFullYear();
				//var kurang = (datebaru.getDate()-1)+'-'+(datebaru.getUTCMonth()+1)+'-'+datebaru.getUTCFullYear();

				$('#insert_tapping_fix_1_date').val('');
				$('#insert_tapping_fix_2_date').val('');

				var newdate = datebaru;
				newdate.setDate(newdate.getDate());
				var dd = newdate.getDate();
				var mm = newdate.getMonth() + 1;
				var y = newdate.getFullYear();
				var asli = dd + '-' + mm + '-' + y;

				var start_newdate = datebaru_plus;
				start_newdate.setDate(start_newdate.getDate() + 1);
				var start_dd = start_newdate.getDate();
				var start_mm = start_newdate.getMonth() + 1;
				var start_y = start_newdate.getFullYear();
				var tambah = start_dd + '-' + start_mm + '-' + start_y;

				var end_newdate = datebaru_minus;
				end_newdate.setDate(end_newdate.getDate() - 1);
				var end_dd = end_newdate.getDate();
				var end_mm = end_newdate.getMonth() + 1;
				var end_y = end_newdate.getFullYear();
				var kurang = end_dd + '-' + end_mm + '-' + end_y;

				console.log(asli);
				console.log("plus " + tambah);
				console.log("min " + kurang);


				/*        
                        $('#insert_tapping_fix_1_date').data("DateTimePicker").maxDate(tambah);
						$('#insert_tapping_fix_1_date').data("DateTimePicker").minDate(kurang);
						
                     
                        $('#insert_tapping_fix_2_date').data("DateTimePicker").maxDate(tambah);
						   $('#insert_tapping_fix_2_date').data("DateTimePicker").minDate(kurang);
				*/
				$('#insert_tapping_fix_1_date').val(asli);
				$('#insert_tapping_fix_2_date').val(asli);


			});

			$('#insert_tapping_fix_1_date').datetimepicker({
				format: 'D-MM-Y',
				<?php
				if (@$maxDate) { ?>
					maxDate: '<?php echo $maxDate; ?>',
				<?php } ?>
			});

			$('#insert_tapping_fix_2_date').datetimepicker({
				format: 'D-MM-Y',
				<?php if (@$maxDate) { ?>
					maxDate: '<?php echo $maxDate; ?>',
				<?php } ?>
			});

			$('#insert_tapping_fix_2_date').datetimepicker({
				format: 'D-MM-Y'
			});

			$("#insert_tapping_fix_1_date").on("dp.change", function(e) {
				// var oldDate = new Date(e.date);
				var newDate = $('#insert_tapping_fix_1_date').val();
				//  newDate.setDate(oldDate.getDate());
				$('#insert_tapping_fix_2_date').val(newDate);
				$('#insert_tapping_fix_2_date').data("DateTimePicker").minDate(newDate);
			});

		});

		$(function() {
			$('#edit_tapping_1_date').datetimepicker({
				format: 'DD-MM-Y',
				<?php if (@$minDate) { ?>
					minDate: '<?php echo $minDate; ?>',
				<?php }
				if (@$maxDate) { ?>
					maxDate: '<?php echo $maxDate; ?>',
				<?php } ?>
			});

			$('#edit_tapping_2_date').datetimepicker({
				format: 'DD-MM-Y',
				<?php if (@$maxDate) { ?>
					maxDate: '<?php echo $maxDate; ?>',
				<?php } ?>
			});

			$("#edit_tapping_1_date").on("dp.change", function(e) {
				// var oldDate = new Date(e.date);
				var newDate = $('#edit_tapping_1_date').val();
				// newDate.setDate(oldDate.getDate());
				$('#edit_tapping_2_date').val(newDate);
				$('#edit_tapping_2_date').data("DateTimePicker").minDate(newDate);
			});

		});

		$('.select2').select2();
		$('#tabel_data_kehadiran').DataTable().destroy();
		table_serverside();
	});

	function refresh_table_serverside() {
		document.getElementById('get_month').value = $('#bulan_tahun').val();
		document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
		/*document.getElementById('get_month_all_unit').value = $('#bulan_tahun').val();*/
		$('#tabel_data_kehadiran').DataTable().destroy();
		table_serverside();
	}

	function refresh_bulan_tahun() {
		$('#tabel_data_kehadiran').DataTable().destroy();
		table_serverside();
	}

	function otoritas() {
		$("#show_otoritas").modal('show');
	}

	function otoritas_per_unit() {
		$("#show_otoritas_per_unit").modal('show');
	}
</script>

<script>
	function table_serverside() {
		var table;
		var bulan_tahun = $('#bulan_tahun').val();

		<?php
		if ($akses["tambah"]) {
		?>

			document.getElementById("insert_tampil_bulan_tahun").value = bulan_tahun;
		<?php
		}
		if ($akses["ubah"]) {
		?>
			document.getElementById("edit_tampil_bulan_tahun").value = bulan_tahun;
		<?php
		}
		if ($akses["ubah kode unit"]) {
		?>
			document.getElementById("kd_tampil_bulan_tahun").value = bulan_tahun;
		<?php } ?>

		//datatables
		table = $('#tabel_data_kehadiran').DataTable({

			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
				"sEmptyTable": "Tidak ada data di database",
				"emptyTable": "Tidak ada data di database"
			},
			"stateSave": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.

			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "<?php echo site_url("kehadiran/data_kehadiran/tabel_data_kehadiran/") ?>" + bulan_tahun,
				"type": "POST",
				"data": function ( d ) {
					var value = $("#search_tbl_by").val();
					d.search.by = value;
					// d.custom = $('#myInput').val();
					// etc
				}
			},

			//Set column definition initialisation properties.
			"columnDefs": [{
				"targets": 'no-sort', //first column / numbering column
				"orderable": false, //set not orderable
			}, ],

		});

		setTimeout(() => {
			$(".dataTables_filter input").before(`
					<select class="form-control" id="search_tbl_by">
						<option disabled selected>Berdasarkan :</option>
						<option value="all">Semua</option>
						<option value="nip">NIP</option>
						<option value="name">Nama</option>
					</select>
			`);

			$('.dataTables_filter input').unbind().keyup(function() {
				var value = $(this).val();
				if (value.length>3) {
					table.search(value).draw();
				} 
			});
		}, 500);

	}

	function cetak_all_unit() {
		$('#modal-export-excel').modal('show');
		// let get_month_per_unit = $('#bulan_tahun').val();
		// window.open(
		//     '<?php echo base_url('kehadiran/data_kehadiran/cetak_all_unit?bulan=') ?>' + get_month_per_unit,
		//     '_blank'
		// );
	}
	
	$('#generate-export-excel').on('click', async () => {
		let zip = new JSZip();

		let dates = $('#date-range-export').val();
		let start_date = dates.split(' - ')[0];
		let end_date = dates.split(' - ')[1];

		let start = new Date(start_date.split('-').reverse().join('-'));
		let end = new Date(end_date.split('-').reverse().join('-'));

		let daysPerDownload = parseInt(document.getElementById('days-slider').value);

		let totalDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        let iterations = Math.ceil(totalDays / daysPerDownload);

		// Initialize progress bar
        let progressBar = document.getElementById('download-progress');
        let progressText = document.getElementById('progress-text');
        progressBar.value = 0;
        progressBar.max = iterations;

		$(".list_download").html("");
		$(".list_download_wrapper").show();
		while (start < end) {
			let nextDate = new Date(start);
			nextDate.setDate(start.getDate() + daysPerDownload); 

			// Ensure the next start date skips one day
            let downloadEnd = new Date(nextDate); // Make the end date inclusive
            downloadEnd.setDate(nextDate.getDate() - 1);

			if (downloadEnd > end) {
                downloadEnd = end;
            }
			let nama_file = `Data_kehadiran_${formatDateExport(start)}_sampai_${formatDateExport(downloadEnd)}.xlsx`;

			$(".list_download").append(`
				<li class="file_${formatDateExport(start)}">
					Mendownload <strong>${nama_file}</strong> <i style="margin-left: 10px;" class="fa fa-spinner rotating"></i>
				</li>
			`)

			console.log(nama_file);
            start = new Date(nextDate);
		}

		$(".list_download").append(`
			<li class="file_zip">
				Menggabungkan Semua File menjadi <strong>File ZIP</strong> <i style="margin-left: 10px;" class="fa fa-spinner rotating"></i>
			</li>
		`)

		// return;

		start = new Date(start_date.split('-').reverse().join('-'));
		end = new Date(end_date.split('-').reverse().join('-'));

		while (start < end) {
			let nextDate = new Date(start);
			nextDate.setDate(start.getDate() + daysPerDownload); // Increment by 1 day
			
			// Ensure the next start date skips one day
            let downloadEnd = new Date(nextDate); // Make the end date inclusive
            downloadEnd.setDate(nextDate.getDate() - 1);

			if (downloadEnd > end) {
                downloadEnd = end;
            }

			const fileData = await fetchFile(start, downloadEnd, progressBar.value + 1);

			if (fileData) {
                zip.file(`Data_kehadiran_${formatDateExport(start)}_sampai_${formatDateExport(downloadEnd)}.xlsx`, fileData);
            }

			// Update progress bar
            progressBar.value += 1;
            progressText.textContent = `${Math.round((progressBar.value / iterations) * 100)}%`;

            // Move to the next range
            start = new Date(nextDate);
		}

		zip.generateAsync({ type: 'blob' }).then((content) => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(content);
            link.download = `Data_kehadiran_${start_date}_sampai_${end_date}.zip`;
            link.click();

			link.remove();
            
			swal.fire("Sukses Download", "Semua Data berhasil di download.", "success");

			$(".file_zip > i").removeClass("rotating").removeClass("fa-spinner").addClass("fa-check").css("color","green");
        });
	});
</script>

<?php
if ($akses["tambah"]) {
?>
	<script>
		function listNp() {
			$.ajax({
				type: "POST",
				dataType: "html",
				url: "<?php echo base_url('kehadiran/data_kehadiran/ajax_getListNp'); ?>",
				success: function(msg) {
					if (msg == '') {
						alert('Silahkan isi No. Pokok Dengan Benar.');
						$('#list_np').text('');
					} else {
						$('#list_np').text(msg);
					}
				}
			});
		}
	</script>
	<script>
		function getNama() {
			var insert_np_karyawan = $('#insert_np_karyawan').val();

			$.ajax({
				type: "POST",
				dataType: "html",
				url: "<?php echo base_url('kehadiran/data_kehadiran/ajax_getNama'); ?>",
				data: "vnp_karyawan=" + insert_np_karyawan,
				success: function(msg) {
					if (msg == '') {
						alert('Silahkan isi No. Pokok Dengan Benar.');
						$('#insert_np_karyawan').val('');
						$('#insert_nama').val('');
					} else {
						$('#insert_nama').val(msg);
					}
				}
			});
		}
	</script>
	<script>
		function getEndDate() {
			var insert_tapping_fix_1_date = $('#insert_tapping_fix_1_date').val();
			document.getElementById('insert_tapping_fix_2_date').setAttribute("min", insert_tapping_fix_1_date);
		}
	</script>
<?php }
if ($akses["ubah"]) {
?>
	<script>
		function cek_assesment() {
			$.ajax({
				type: "POST",
				data: {
					id: $("#edit_np_karyawan").val(),
					tgl: $("#edit_dws_tanggal").val()
				},
				url: "<?php echo base_url('kehadiran/self_assesment_covid19/assesment'); ?>",
				success: function(data) {
					get_data = JSON.parse(data);

					if (get_data.last_assesment == null) {
						$("#submitwfh").show();
						$("#submitnonwfh").hide();
					} else {
						$("#submitwfh").hide();
						$("#submitnonwfh").show();
					}
				}
			});
		}


		$(document).on("click", '.edit_button', function(e) {
			var id = $(this).data('id');
			var nama = $(this).data('nama');
			var np_karyawan = $(this).data('np-karyawan');
			var dws_tanggal = $(this).data('dws-tanggal');
			var dws_name = $(this).data('dws-name');
			var tapping_1_date = $(this).data('tapping-1-date');
			var tapping_1_time = $(this).data('tapping-1-time');
			var tapping_2_date = $(this).data('tapping-2-date');
			var tapping_2_time = $(this).data('tapping-2-time');
			var tapping_fix_approval_ket = $(this).data('tapping-fix-approval-ket');
			var tapping_fix_approval_np = $(this).data('tapping-fix-approval-np');
			var ada_sidt = $(this).data('ada-sidt');
			var wfh = $(this).data('wfh');
			var wfh_foto_1 = $(this).data('wfh_foto_1');
			var wfh_foto_2 = $(this).data('wfh_foto_2');

			$("#edit_id").val(id);
			$("#edit_nama").val(nama);
			$("#edit_np_karyawan").val(np_karyawan);
			$("#edit_dws_tanggal").val(dws_tanggal);
			$("#edit_dws_name").val(dws_name);
			$("#edit_tapping_1_date").val(tapping_1_date);
			$("#edit_tapping_1_time").val(tapping_1_time);
			$("#edit_tapping_2_date").val(tapping_2_date);
			$("#edit_tapping_2_time").val(tapping_2_time);
			$("#edit_tapping_fix_approval_ket").val(tapping_fix_approval_ket);

			document.getElementById('preview_wfh_foto_1').src = wfh_foto_1;
			document.getElementById('preview_wfh_foto_2').src = wfh_foto_2;

			document.getElementById('hidden_wfh_foto_1').val = wfh_foto_1;
			document.getElementById('hidden_wfh_foto_2').val = wfh_foto_2;


			if (wfh >= "1") {
				document.getElementById("edit_wfh").checked = true;
				$("#edit_wfh_foto_1").show();
				$("#edit_wfh_foto_2").show();
				$(".wfh_foto").show();

				// START: heru menambahkan ini 2020-11-18 @15:38
				$("#submitwfh").show();
				$("#submitnonwfh").hide();
				// END: heru menambahkan ini 2020-11-18 @15:38


				/*$("#submitwfh").show();
				$("#submitnonwfh").hide();*/

				var tapping_2_time = $('#edit_tapping_2_time').val();
				var hidden_wfh_foto_1 = document.getElementById("hidden_wfh_foto_1").val;
				var hidden_wfh_foto_2 = document.getElementById("hidden_wfh_foto_2").val;

				if (hidden_wfh_foto_1 !== null && hidden_wfh_foto_1 !== '') { //jika isi
					document.getElementById("edit_wfh_foto_1").required = false;
				} else {
					document.getElementById("edit_wfh_foto_1").required = true;
				}


				if (tapping_2_time !== null && tapping_2_time !== '' && tapping_2_time !== '00:00' && tapping_2_time !== ' 00:00') //jika isi
				{
					document.getElementById("edit_wfh_foto_2").required = true;
				} else {
					document.getElementById("edit_wfh_foto_2").required = false;
				}
				//cek_assesment(); heru comment function ini 2020-11-18 @15:38


			} else {
				document.getElementById("edit_wfh").checked = false;
				$("#edit_wfh_foto_1").hide();
				$("#edit_wfh_foto_2").hide();
				$(".wfh_foto").hide();

				$("#submitwfh").hide();
				$("#submitnonwfh").show();

				document.getElementById("edit_wfh_foto_1").required = false;
				document.getElementById("edit_wfh_foto_2").required = false;
			}

			//Jika sidt maka tidak bisa edit kehadiran
			if (ada_sidt < "1") {
				document.getElementById("edit_tapping_1_date").readOnly = false;
				document.getElementById("edit_tapping_1_time").readOnly = false;

				document.getElementById("edit_keterangan").innerText = "";
			} else {
				document.getElementById("edit_tapping_1_date").readOnly = true;
				document.getElementById("edit_tapping_1_time").readOnly = true;
				document.getElementById("edit_tapping_1_date").required = false;
				document.getElementById("edit_tapping_1_time").required = false;

				document.getElementById("edit_keterangan").innerText = "SIDT";
			}

			// getPilihanAtasanLembur();
			// heru mengganti jadi ini, 2020-11-14 @08:03
			$("#edit_approval").val('');
			$('#approval_input').val('');
			$('#approval_input_jabatan').val('');
			if ($('#asdf').prop('checked'))
				$('#asdf').trigger('click');
			setTimeout(function() {
				if (tapping_fix_approval_np)
					$("#edit_approval").val(tapping_fix_approval_np).trigger("change");
			}, 500);
			// END of: heru mengganti jadi ini, 2020-11-14 @08:03
			// getNamaAtasan(); //wina ganti function

			get_atasan_kehadiran_new();
		});

		// START: heru menambahkan ini 2020-11-14 @08:05
		function use_last_approver() {
			if ($('#asdf').prop('checked'))
				getAtasanKehadiran();
			else
				console.log('unchecked');
		}
		// END: heru menambahkan ini 2020-11-14 @08:05

		function getAtasanKehadiran() {
			var no_pokok = $('#edit_np_karyawan').val();
			console.log('masuk getAtasanKehadiran');
			$.ajax({
				type: "POST",
				dataType: "html",
				url: "<?php echo base_url('kehadiran/data_kehadiran/ajax_getAtasanKehadiran'); ?>",
				data: "vnp_karyawan=" + no_pokok,
				success: function(msg) {
					if (msg != '') {
						let find = _.find(data_atasan1_temp, o => {
							return o.no_pokok == msg;
						});
						if (typeof find != 'undefined') {
							$("#edit_approval").val(msg).trigger("change");
						} else alert('Atasan tidak ditemukan!');
					}
					// else if($("#edit_approval").children().length>0){
					// 	$("#edit_approval").get(0).selectedIndex = 0;
					// 	$("#edit_approval").trigger("change");
					// }
					else {
						alert('Atasan tidak ditemukan!');
					}
				}
			});
		}

		function getPilihanAtasanLembur() {
			//alert("asd");
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			var no_pokok = $('#edit_np_karyawan').val();
			var periode = $('#edit_dws_tanggal').val();
			$("#edit_approval").empty();

			$.ajax({
				type: "POST",
				dataType: "html",
				url: "<?php echo base_url('kehadiran/data_kehadiran/ajax_getPilihanAtasanKehadiran'); ?>",
				data: "vnp_karyawan=" + no_pokok + "#" + periode,
				success: function(msg) {
					if (msg != '') {
						//console.log(msg);
						var arr_atasan = JSON.parse(msg);
						for (var i = 0; i < arr_atasan.length; i++) {
							$("#edit_approval").append($("<option></option>").attr("value", arr_atasan[i]["no_pokok"]).text(arr_atasan[i]["no_pokok"] + " - " + arr_atasan[i]["nama"]));
						}
						$('.select2').select2();
						getAtasanKehadiran();
					} else {
						alert('Atasan tidak ditemukan!');
					}
				}
			});
		}
	</script>

	<script>
		function cek_wfh() {
			$('#formulir_tambah_assesment').trigger("reset");
			get_wfh = document.getElementById("edit_wfh").checked;

			if (get_wfh == true) {
				$.ajax({
					type: "POST",
					data: {
						id: $("#edit_np_karyawan").val(),
						tgl: $("#edit_dws_tanggal").val()
					},
					url: "<?php echo base_url('kehadiran/self_assesment_covid19/assesment'); ?>",
					success: function(data) {
						get_data = JSON.parse(data);

						if (get_data.last_assesment == null) {
							$("#tanggal_kehadiran").val($("#edit_dws_tanggal").val());
							$("#tanggal_kehadiran_assesment").text('Tanggal: ' + $("#edit_dws_tanggal").val());
							$("#modal-assesment").modal('show');

							// $('#form-ubah').submit();
						} else {
							$("#submitwfh").hide();
							$("#submitnonwfh").show();
							loading_toastr_success('Anda Sudah Mengisi Self Assesment');
						}
					}
				});
			} else {
				$("#submitwfh").hide();
				$("#submitnonwfh").show();
			}
		}


		$('#SubForm').click(function() {
			var data_assesment = document.getElementById("formulir_tambah_assesment");
			if (data_assesment['tanggal_kehadiran'].value && data_assesment['pernah_keluar'].value != '' && data_assesment['transportasi_umum'].value != '' && data_assesment['luar_kota'].value != '' && data_assesment['kegiatan_orang_banyak'].value != '' && data_assesment['kontak_pasien'].value != '' && data_assesment['sakit'].value != '')
				save_assesment();
			else {
				$("#alert_form").html('<div class="alert alert-danger alert-dismissable"><strong id="get_last_assesment">Semua Pertanyaan Harus Dijawab!</strong></div>');
				loading_toastr_error('Semua Pertanyaan Harus Dijawab!');
			}
		});

		function save_assesment() {
			var data_assesment = document.getElementById("formulir_tambah_assesment");

			$.ajax({
				type: "POST",
				data: {
					id: $("#edit_np_karyawan").val(),
					tgl: data_assesment['tanggal_kehadiran'].value,
					submit: true,
					pernah_keluar: data_assesment['pernah_keluar'].value,
					transportasi_umum: data_assesment['transportasi_umum'].value,
					luar_kota: data_assesment['luar_kota'].value,
					kegiatan_orang_banyak: data_assesment['kegiatan_orang_banyak'].value,
					kontak_pasien: data_assesment['kontak_pasien'].value,
					sakit: data_assesment['sakit'].value
				},
				url: "<?php echo base_url(); ?>kehadiran/self_assesment_covid19/action_insert",
				success: function(get_data) {
					data = JSON.parse(get_data);

					msg = data.message;
					if (data.status == true) {
						loading_toastr_success(msg);

						$("#submitwfh").hide();
						$("#submitnonwfh").show();

						$('#form-ubah').submit();
					} else {
						loading_toastr_error(msg);
					}
					$("#modal-assesment").modal('hide');
				}
			});
		}

		function wfh() {
			var checkbox = document.getElementsByName("edit_wfh");
			var tapping_2_time = $('#edit_tapping_2_time').val();

			var hidden_wfh_foto_1 = document.getElementById("hidden_wfh_foto_1").val;
			var hidden_wfh_foto_2 = document.getElementById("hidden_wfh_foto_2").val;

			var var_array = "";
			for (var i = 0; i < checkbox.length; i++) {
				if (checkbox[i].checked) {
					//var_array = var_array + checkbox[i].value +", ";
					var_array = var_array + checkbox[i].value;
				}
			}

			if (var_array >= 1) {
				document.getElementById("edit_wfh").checked = true;
				$("#edit_wfh_foto_1").show();
				$("#edit_wfh_foto_2").show();
				$(".wfh_foto").show();
				// START: heru menambahkan ini 2020-11-18 @15:38
				$("#submitwfh").show();
				$("#submitnonwfh").hide();
				// END: heru menambahkan ini 2020-11-18 @15:38

				//cek_assesment(); heru comment function ini 2020-11-18 @15:38

				/*$("#submitwfh").show();
				$("#submitnonwfh").hide();*/

				if (hidden_wfh_foto_1 !== null && hidden_wfh_foto_1 !== '') { //jika isi
					document.getElementById("edit_wfh_foto_1").required = false;
				} else {
					document.getElementById("edit_wfh_foto_1").required = true;
				}


				if (tapping_2_time !== null && tapping_2_time !== '' && tapping_2_time !== '00:00' && tapping_2_time !== ' 00:00') //jika isi
				{
					document.getElementById("edit_wfh_foto_2").required = true;
				} else {
					document.getElementById("edit_wfh_foto_2").required = false;
				}

			} else {
				document.getElementById("edit_wfh").checked = false;
				$("#edit_wfh_foto_1").hide();
				$("#edit_wfh_foto_2").hide();
				$(".wfh_foto").hide();
				$("#submitwfh").hide();
				$("#submitnonwfh").show();
				document.getElementById("edit_wfh_foto_1").required = false;
				document.getElementById("edit_wfh_foto_2").required = false;
			}



		}

		function loading_toastr_success(msg) {
			toastr.success(msg, "Berhasil", {
				"positionClass": "toast-top-center",
				"showDuration": "300",
				"hideDuration": "500",
				"timeOut": "3000",
				"extendedTImeout": "1000",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			});
		}

		function loading_toastr_error(msg) {
			toastr.error(msg, "Gagal", {
				"positionClass": "toast-top-center",
				"showDuration": "300",
				"hideDuration": "500",
				"timeOut": "3000",
				"extendedTImeout": "1000",
				"showMethod": "fadeIn",
				"hideMethod": "fadeOut"
			});
		}

		function getNamaAtasan() {
			var np_karyawan = $('#edit_approval').val().trim();
			var np_karyawan_request = $('#edit_np_karyawan').val();

			$.ajax({
				type: "POST",
				dataType: "JSON",
				url: "<?php echo base_url('pilih_approval/ajax_getNama_approval'); ?>",
				data: {
					vnp_karyawan: np_karyawan,
					vnp_karyawan_request: np_karyawan_request
				},
				success: function(msg) {
					if (msg.status == false) {
						// alert('Silahkan isi No. Pokok Atasan Dengan Benar.');
						/* $('#edit_approval').val('');
						$('#approval_input').val('');
						$('#approval_input_jabatan').val(''); */
						if(np_karyawan) loading_toastr_error('Silahkan isi No. Pokok Atasan Dengan Benar.');
					} else {
						if(np_karyawan_request){
							if(msg.data.nama && msg.data.nama != "") $('#approval_input').val(msg.data.nama);
							if(msg.data.jabatan && msg.data.jabatan != "") $('#approval_input_jabatan').val(msg.data.jabatan);
						}
					}
				}
			});
		}
	</script>
<?php }
if ($akses["ubah kode unit"]) {
?>
	<script>
		$(document).on("click", '.edit_kode_unit', function(e) {
			var id = $(this).data('id');
			var nama = $(this).data('nama');
			var np_karyawan = $(this).data('np-karyawan');
			var dws_tanggal = $(this).data('dws-tanggal');
			var dws_name = $(this).data('dws-name');
			var kode_unit = $(this).data('kode-unit');

			$("#kd_id").val(id);
			$("#kd_nama").val(nama);
			$("#kd_np_karyawan").val(np_karyawan);
			$("#kd_dws_tanggal").val(dws_tanggal);
			$("#kd_dws_name").val(dws_name);
			$("#kd_kode_unit").val(kode_unit);
		});
	</script>
<?php } 
if ($akses["lihat log"]) {
?>
	<script src="<?php base_url()?>/ess/asset/cartenz/sweetalert2.all.min.js"></script>
	<link rel="stylesheet" href="<?php base_url()?>/ess/asset/cartenz/jquery-ui.css" />
	<script src="<?php base_url()?>/ess/asset/cartenz/jquery-ui.js"></script>

	<script>
		var curr_data_anomaly = [];
		var curr_data_group_anomaly = [];
		var curr_result_date_range = null;
		
		function sleep(seconds) {
			const milliseconds = seconds * 1000; // Convert seconds to milliseconds
			return new Promise(resolve => setTimeout(resolve, milliseconds));
		}

		function downloadJSONAsCSV(jsonData, filename) {
			// Convert JSON data to CSV format
			const items = jsonData;
			const replacer = (key, value) => value === null ? '' : value; // Handle null values
			const header = Object.keys(items[0]);
			let csv = items.map(row => header.map(fieldName => JSON.stringify(row[fieldName], replacer)).join(','));
			csv.unshift(header.join(','));
			csv = csv.join('\r\n');

			// Create a Blob from the CSV string
			const blob = new Blob([csv], { type: 'text/csv' });

			// Create a link element and trigger download
			const link = document.createElement('a');
			link.href = URL.createObjectURL(blob);
			link.download = filename;

			// Append link to the body
			document.body.appendChild(link);

			// Trigger download by simulating click
			link.click();

			// Remove link from the body
			document.body.removeChild(link);
		}

		function groupEmployeesByDate(data) {
			// Create an object to hold the grouped data
			const grouped = data.reduce((acc, item) => {
				const { Tertanggal, ...employeeData } = item;

				// If the date is not in the accumulator, add it
				if (!acc[Tertanggal]) {
					acc[Tertanggal] = {
						Tertanggal: Tertanggal,
						employee_list: []
					};
				}

				// Push the employee data into the corresponding date
				acc[Tertanggal].employee_list.push(employeeData);

				return acc;
			}, {});

			// Convert the grouped object into the desired array format
			return Object.values(grouped);
		}

		function formatDate(value) {
            let date = new Date(value);
            const day = date.toLocaleString('default', { day: '2-digit' });
            const month = date.toLocaleString('default', { month: 'short' });
            const year = date.toLocaleString('default', { year: 'numeric' });
            return month + ', ' +  day + ' ' +  + year;
        }

		function reFormatDate(inputDate) {
			// Parse the input date string
			const date = new Date(inputDate);

			// Extract the year, month, and day
			const year = date.getFullYear();
			const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
			const day = String(date.getDate()).padStart(2, '0');

			// Format the date as "YYYY/MM/DD"
			return `${year}-${month}-${day}`;
		}

		function getDateRange(monthYear) {
			// Split the input string to get month and year
			const [month, year] = monthYear.split('-');

			// Create the first date of the month
			const firstDay = new Date(year, month - 1, 1);

			// Create the last date of the month
			const lastDay = new Date(year, month, 0);

			// Format the dates as MM/DD/YYYY
			const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
			const firstDayFormatted = firstDay.toLocaleDateString('en-US', options);
			const lastDayFormatted = lastDay.toLocaleDateString('en-US', options);

			// Return the array of range time
			return [firstDayFormatted, lastDayFormatted];
		}
		
		async function confirm_date_range(bulan){
			var {value} = await Swal.fire({
                showCancelButton: true,
                confirmButtonText: 'Cek Anomali',
                cancelButtonText: 'Batalkan',
                html: `
                        <div style>
                            <label for="amount" style="font-weight: 900; font-size: 18px;">Cek Anomali Data</label>
							<div>
                            	<img class="illustrasi" style="width: 50%;" src="<?php base_url()?>/ess/asset/cartenz/mark-calender.gif">
							</div>
							<label style="font-weight: 500; font-size: 16px;">Periode Tanggal : </label>
                            <div type="text" id="amount" style="color: #f8dd36; margin-bottom: 30px; font-weight: 900; font-size: 16px; width: 100%; text-shadow: 2px 2px gray; white-space: nowrap;"></div>
                            <input type="hidden" id="nilai_startDate"><input type="hidden" id="nilai_endDate">
                        </div>
        
                        <div id="slider-range"></div>
                `,
                onBeforeOpen: () => {
                    var batas_waktu_start = bulan[0];
                    var batas_waktu_end = bulan[1];

                    var date_selected_start = new Date(batas_waktu_start);
                    var date_selected_end = new Date(batas_waktu_end);

                    var var_jml = 7;
                    date_selected_start.setDate(date_selected_start.getDate() + var_jml);
                    date_selected_end.setDate(date_selected_end.getDate() - var_jml);

                    $( "#slider-range" ).slider({
                        range: true,
                        min: new Date(batas_waktu_start).getTime() / 1000,
                        max: new Date(batas_waktu_end).getTime() / 1000,
                        step: 86400,
                        values: [ date_selected_start.getTime() / 1000, date_selected_end.getTime() / 1000 ],
                        slide: function( event, ui ) {
                            $( "#nilai_startDate" ).val(new Date(ui.values[0] *1000));
                            $( "#nilai_endDate" ).val(new Date(ui.values[1] *1000));
                            $( "#amount" ).html( formatDate(new Date(ui.values[ 0 ] *1000)) + " - " + formatDate(new Date(ui.values[ 1 ] *1000)));
                        }
                    });
                    
                    $( "#nilai_startDate" ).val(date_selected_start);
                    $( "#nilai_endDate" ).val(date_selected_end);
                    $( "#amount" ).html( formatDate(new Date($( "#slider-range" ).slider( "values", 0 )*1000)) + " - " + formatDate(new Date($( "#slider-range" ).slider( "values", 1 )*1000)));
                },
                onOpen: ()=>{
                    $(".swal2-popup.swal2-modal.swal2-show").css("background", "#F8F8F8");
                },
                preConfirm: () => {
                    var nilai_startDate = new Date(document.getElementById('nilai_startDate').value);
                    var nilai_endDate = new Date(document.getElementById('nilai_endDate').value);

                    var trim_startDate = (nilai_startDate.getMonth()+1) + "-" + (nilai_startDate.getDate());
                    var trim_endDate = (nilai_endDate.getMonth()+1) + "-" + (nilai_endDate.getDate());

                    var periode = {
                        'start_date': trim_startDate,
                        'end_date': trim_endDate,
                        'waktu_awal': reFormatDate(nilai_startDate),
                        'waktu_akhir': reFormatDate(nilai_endDate)
                    };

                    return periode;
                }
            });

			return value;
		}

		async function cek_anomaly(){
			var bulan = $("#bulan_tahun").val();
			var result = await confirm_date_range(getDateRange(bulan));

			if(result){
				curr_result_date_range = result;
				await getDataAnomaly();
			}
		}

		async function getDataAnomaly(){
			loading("Memuat data anomaly ...");
			var res = curr_result_date_range;

			var bulan = $("#bulan_tahun").val();
			var path_param = bulan + "/" + res["waktu_awal"] + "/" + res["waktu_akhir"];
			var result =  await $.ajax({
				url: '<?php echo base_url() ?>pamlek/pamlek_to_ess/check_anomaly/' + path_param,
				type: 'GET',
				dataType: 'json'
			});

			var raw_result = result;
			curr_data_anomaly = result;
			var group_result = groupEmployeesByDate(result);
			curr_data_group_anomaly = group_result;
			var tabel_data = '';

			if(group_result.length > 0){
				for (let i = 0; i < group_result.length; i++) {
					tabel_data += `
						<tr>
							<td>
								${ i + 1 }
							</td>
							<td>
								${ group_result[i]["Tertanggal"] }
							</td>
							<td>
								<a class="no-link" onclick="view_anom_employee(${i})">${ group_result[i]["employee_list"].length }</a> pegawai

							</td>
							<td>
								<button class="btn btn-warning" onclick="mulai_perbaiki(${i})">
									<i class="fa fa-wrench"></i>
									Perbaiki
								</button>
							</td>
						</tr>
					`;
				}

				$("#modal_anomali").find(".modal-title").html("Anomali Data Bulan : " + bulan);
				
				$("#modal_anomali").find(".modal-body").html(`
					<table id="anomaly_tbl">
						<tr>
							<th>No.</th>
							<th>Tertanggal</th>
							<th>Total Anomali</th>
							<th>Aksi</th>
						</tr>
						${tabel_data}
						<tr>
							<td>
								${ group_result.length + 1 }
							</td>
							<td>
								<input class="form-control" type="date" style="width: 160px; margin: 0px auto; background: unset; border: 1px solid black;"/>
							</td>
							<td>
								∞ pegawai
							</td>
							<td>
								<button class="btn btn-warning" onclick="mulai_perbaiki_method_all(this)">
									<i class="fa fa-wrench"></i>
									Perbaiki
								</button>
							</td>
						</tr>
					</table>
				`);

				$("#modal_anomali").modal("show");
			} else {
				swal.fire("Tidak Ada Anomali", "Semua data di rentan waktu ini tidak ditemukan anomali data kehadiran", "success");
			}

			unloading();
		}

		async function perbaiki_data_per_karyawan(data, delay){
			var is_delay = delay ? "" : "/nodelay";
			var path_param = data["waktu"] + "/" + data["waktu"] + "/" + data["np_karyawan"] + is_delay;
			var result = null;

			try {
				result =  await $.ajax('<?php echo base_url() ?>pamlek/pamlek_to_ess/inisialisasi/' + path_param);
			} catch (error) {
				console.log(error);
			}

			return result;
		}

		async function mulai_perbaiki(idx,single_mode=true){
			var data = curr_data_group_anomaly[idx];
			loading(`Memperbaiki data tgl : ${data["Tertanggal"]}`);

			await sleep(2);
			var banyak_data = data["employee_list"].length;
			
			for (let i = 0; i < banyak_data; i++) {
				var x = data["employee_list"][i];
				var data_kirim = {
					waktu: data["Tertanggal"],
					np_karyawan: x["np_karyawan"]
				};

				loading(`Memproses : ${x["nama"]} (${i+1}/${banyak_data})`);
				await perbaiki_data_per_karyawan(data_kirim, false);
				await sleep(1);
			}

			if(single_mode){
				swal.fire("Sukses", `Sukses memperbaiki data anomaly tgl ${data["Tertanggal"]}`, "success");

				getDataAnomaly();
				unloading();
			}
			
		}

		function download_anomaly_data(){
			downloadJSONAsCSV(curr_data_anomaly, "test.csv");
		}

		function view_anom_employee(idx){
			var dataNya = curr_data_group_anomaly[idx];
			var win = window.open("", "Employee List :", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=780,height=600,top="+(screen.height-400)+",left="+(screen.width-840));
			var data_tampil = "";

			for (let i = 0; i < dataNya["employee_list"].length; i++) {
				var data = dataNya["employee_list"][i];
				var warna = i%2==0 ? "#e7e7e7" : "#f3f3f3";

				data_tampil += `
					<tr style="background: ${warna};">
						<td>
							${ i + 1 }
						</td>
						<td>
							${ data["nama"] }
						</td>
						<td>
							${ data["np_karyawan"] }
						</td>
						<td>
							${ data["In (Pamlek)"] }
						</td>
						<td>
							${ data["Out (Pamlek)"] }
						</td>
						<td>
							${ data["In (Kehadiran)"] }
						</td>
						<td>
							${ data["Out (Kehadiran)"] }
						</td>
					</tr>
				`;
			}

			win.document.body.innerHTML = `
			<h1>Anomali Data : ${dataNya["Tertanggal"]}</h1>
			<table id="anomaly_tbl">
				<tr style="background: yellow; font-size: 18px;">
					<th>No.</th>
					<th>Nama</th>
					<th>NIP</th>
					<th>In (Pamlek)</th>
					<th>Out (Pamlek)</th>
					<th>In (Kehadiran)</th>
					<th>Out (Kehadiran)</th>
				</tr>
				${data_tampil}
			</table>`;
		}

		async function perbaiki_semua_data_anomali(){
			var cons = await Swal.fire({
				title: "Perbaiki Semua Data",
				type: "question",
				html: `Proses ini membutuhkan waktu lumayan lama, tergantung banyak data anomaly dan jumlah rentang waktu yang tampil. Tetap lanjutkan ?`,
				showCloseButton: true,
				showCancelButton: true,
				confirmButtonText: `
					<i class="fa fa-thumbs-up"></i> Lanjutkan!
				`,
				cancelButtonText: `
					<i class="fa fa-thumbs-down"></i> Batalkan
				`,
			});

			if(cons.value){
				for (let i = 0; i < curr_data_group_anomaly.length; i++) {
					await mulai_perbaiki(i,false);
				}

				swal.fire("Sukses", `Sukses memperbaiki data anomaly`, "success");
				getDataAnomaly();
				unloading();
			}
			
		}

		function loading(text="Memuat data ..."){
			$(".loading_custom_anomali").css("display", "flex").find(".loading-text").html(text);
		}

		function unloading(){
			$(".loading_custom_anomali").css("display", "none");
		}

		async function mulai_perbaiki_method_all(that){
			var tgl = $(that).parent().parent().find("input[type='date']").val();
			// var data = curr_data_group_anomaly[idx];
			
			var cons = await Swal.fire({
				title: "Perbaiki Semua Data",
				type: "question",
				html: `Proses ini membutuhkan waktu lumayan lama, tergantung banyak data anomaly dan jumlah rentang waktu yang tampil. Tetap lanjutkan ?`,
				showCloseButton: true,
				showCancelButton: true,
				confirmButtonText: `
					<i class="fa fa-thumbs-up"></i> Lanjutkan!
				`,
				cancelButtonText: `
					<i class="fa fa-thumbs-down"></i> Batalkan
				`,
			});

			if(cons.value){
				var data_kirim = {
					waktu: tgl,
					np_karyawan: "all"
				};

				loading(`Memproses data tgl ${tgl} dengan metode All.`);
				await perbaiki_data_per_karyawan(data_kirim, false);
				await sleep(1);
				$("#modal_anomali").modal("hide");

				swal.fire("Sukses", `Sukses memproses data tgl ${tgl} dengan metode All.`, "success");
				unloading();
			}
			
		}
	</script>
<?php } ?>

<script>
	var data_atasan1_temp = [];

	function get_atasan_kehadiran_new() {
		let tanggal_mulai = $('#edit_dws_tanggal').val();
		let np = $('#edit_np_karyawan').val();
		if (np != '' && tanggal_mulai != '') {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: `<?= base_url() ?>kehadiran/data_kehadiran/get_atasan_kehadiran_new`,
				data: {
					np,
					tanggal_mulai
				},
				beforeSend: () => {
					data_atasan1_temp = [];

					$('#edit_approval').empty().trigger('change');
				}
			}).then((res) => {
				// console.log(res);
				data_atasan1_temp = res.data_atasan1;

				$('#edit_approval').select2({
					dropdownParent: $("#modal_ubah"),
					placeholder: 'Pilih Atasan',
					data: res.data_atasan1.map((o) => {
						return {
							id: o.no_pokok,
							text: `(${o.no_pokok}) ${o.nama}`
						};
					})
				}).trigger('change');
			});
		} else {
			data_atasan1_temp = [];

			$('#edit_approval').empty().trigger('change');
		}
	}

	$('#edit_approval').on('change', (e) => {
		let find = _.find(data_atasan1_temp, o => {
			return o.no_pokok == e.target.value;
		});
		if (typeof find != 'undefined') {
			$('#approval_input').val(find.nama);
			$('#approval_input_jabatan').val(find.nama_jabatan);
		} else {
			$('#approval_input').val('');
			$('#approval_input_jabatan').val('');
		}
	});
</script>

<script>
	function formatDateExport(date) {
		let d = date.getDate().toString().padStart(2, '0');
		let m = (date.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
		let y = date.getFullYear();
		return `${d}-${m}-${y}`;
	}

	document.getElementById('days-slider').addEventListener('input', (event) => {
        document.getElementById('days-value').textContent = event.target.value + " hari";
    });

	async function fetchFile(start, end, index) {
		let url = '<?= base_url('kehadiran/data_kehadiran/cetak_all_unit_date_range') ?>' +
			`?start_date=${formatDateExport(start)}&end_date=${formatDateExport(end)}`;

		try {
			// console.log(url);
			const response = await fetch(url);
			$(`.file_${formatDateExport(start)} > i`).removeClass("rotating").removeClass("fa-spinner");
			if (response.ok) {
				$(`.file_${formatDateExport(start)} > i`).addClass("fa-check").css("color","green");

				return await response.blob();
				/* const link = document.createElement('a');
				link.href = URL.createObjectURL(blob);
				link.download = `Data_kehadiran_${formatDateExport(start)}_to_${formatDateExport(end)}.xlsx`;
				link.click(); */

				
			} else {
				console.error('Error downloading file:', response.status);
				
				$(`.file_${formatDateExport(start)} > i`).addClass("fa-times").css("color","red");
			}
		} catch (error) {
			console.error('Error:', error);
			$(`.file_${formatDateExport(start)} > i`).removeClass("rotating").removeClass("fa-spinner").addClass("fa-times").css("color","red");
		}
	}
</script>