<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" />

<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div id="overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.5); color: white; text-align: center; line-height: 100vh; z-index: 9999;">
			<div>
				<p>Sedang memproses cek data import...</p>
				<div class="spinner-border text-light" role="status"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Usulan Kebutuhan Pelatihan</h1>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->

		<?php
		if (!empty($this->session->flashdata('success_pelatihan'))) {
		?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('success_pelatihan'); ?>
			</div>
		<?php
		}
		if (!empty($this->session->flashdata('warning_pelatihan'))) {
		?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('warning_pelatihan'); ?>
			</div>
		<?php
		}
		if ($akses["lihat log"]) {
			echo "<div class='row text-right'>";
			echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
			echo "<br><br>";
			echo "</div>";
		}
		if ($akses["tambah"]) {
		?>
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" id="button_tambah" data-parent="#accordion" href="#collapseOne">Tambah Usulan Kebutuhan Pelatihan</a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse">
							<div class="panel-body">

								<form role="form" action="<?php echo base_url(); ?>pelatihan/pelatihan/action_insert_pelatihan" id="formulir_tambah" method="post">
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Karyawan</label>
											</div>
											<div class="col-lg-7">
												<select class="form-control select2" onchange="getNama()" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
													<option value='' disabled selected>---Pilih Karyawan---</option>
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
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Kategori Pelatihan</label>
											</div>
											<div class="col-lg-7">
												<select id="kategori" class="form-control select2" name="kategori" onchange="inputPelatihan()" style="width: 100%">
													<option value="" disabled selected>---Pilih Kategori---</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Pelatihan</label>
											</div>
											<div id="pelatihan_select" class="col-lg-7" style="display: block;">
												<select id="nama_pelatihan_select" class="form-control select2" name="nama_pelatihan" onchange = 'getCategoryPelatihan()' style="width: 100%" required>
												</select>
											</div>
											<div id="pelatihan_text" class="col-lg-7" style="display: none;">
												<input class="form-control" name="nama_pelatihan" id="nama_pelatihan_text" value="" disabled required />
											</div>
										</div>
									</div>
									<!-- <div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Tanggal Pelatihan</label>
											</div>
											<div class="col-lg-2">
												<input type="date" class="form-control" name="tgl_pelatihan" id="tgl_pelatihan" required>
											</div>
										</div>
									</div> -->
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Vendor</label>
											</div>
											<div class="col-lg-7">
												<input type="text" class="form-control" name="vendor" id="vendor">
												<small class="form-text text-muted">Opsional</small>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Skala Prioritas</label>
											</div>
											<div class="col-lg-7">
												<select id="skala_prioritas" class="form-control" name="skala_prioritas" required>
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-2">
										</div>
										<div class="col-lg-7">
											<small class="form-text text-muted">Angka yang lebih kecil memiliki prioritas lebih tinggi</small>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>NP Atasan 1</label>
											</div>
											<div class="col-lg-7">
												<!-- <select class="form-control select2" id="approval_1" name="approval_1" style="width: 100%;" required></select> -->
												<input class="form-control" name="approval_1" id="approval_1" value="" onChange="getNamaAtasan1()" required />
												<input name="is_poh_approval_1" id="is_poh_approval_1" type="hidden" value="" style="text-transform:uppercase" />
												<small class="form-text text-muted">Dimohon menggunakan Huruf Kapital</small>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label></label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="approval_1_input" id="approval_1_input" value="" readonly required>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label></label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="approval_1_input_jabatan" id="approval_1_input_jabatan" required><small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>NP Atasan 2</label>
											</div>
											<div class="col-lg-7">
												<!-- <select class="form-control select2" id="approval_2" name="approval_2" style="width: 100%;" required></select> -->
												<input class="form-control" name="approval_2" id="approval_2" value="" onChange="getNamaAtasan2()">
												<input name="is_poh_approval_2" id="is_poh_approval_2" type="hidden" value="" style="text-transform:uppercase" />
												<small class="form-text text-muted">Dimohon menggunakan Huruf Kapital</small>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label></label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="approval_2_input" id="approval_2_input" value="" readonly>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label></label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="approval_2_input_jabatan" id="approval_2_input_jabatan"><small class="form-text text-muted">Pejabat Penanggung Jawab</small>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-9 text-right">
											<input type="submit" name="submit" value="submit" class="btn btn-primary">
										</div>
									</div>
								</form>


							</div>
						</div>
					</div>
					<?php
					if ($akses["import"]) { 
					?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" id="btn-import">Import Usulan Kebutuhan Pelatihan</a>
							</h4>
						</div>
						<div id="collapseTwo" class="panel-collapse collapse">
							<div class="panel-body">
								<form id="formulir_import" method="post" enctype="multipart/form-data">
									<div class="form-group row">
										<div class="col-lg-2">
											<label for="dokumen">File Excel</label>
										</div>
										<div class="col-lg-7">
											<input class="form-control" type="file" name="dokumen" id="dokumen" accept=".xls,.xlsx" required>
											<small class="form-text text-danger">Dokumen Excel Max 2MB</small><!-- <strong> (wajib diisi)</strong> -->
										</div>
									</div>

									<div class="row">
										<div class="col-lg-9 text-right">
											<input type="submit" name="submit" value="Submit" class="btn btn-primary mr-2 btn-submit-import">
											<a href="<?= base_url('pelatihan/pelatihan/create_template'); ?>" class="btn btn-info">Unduh Template</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<?php
					}
					?>
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
			<!-- filter bulan -->
			<div class="form-group">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label>Bulan</label>
							<!--<select id="pilih_bulan_tanggal" class="form-control">-->
							<select class="form-control select2" id='bulan_tahun' name='bulan_tahun' onchange="refresh_table_serverside()" style="width: 100%;">
								<option value='0'>Semua</option>
								<?php
								foreach ($array_tahun_bulan as $value) {

									$tampil_bulan_tahun = '';
									if (!empty($this->session->flashdata('tampil_bulan_tahun'))) {
										$tampil_bulan_tahun = $this->session->flashdata('tampil_bulan_tahun');
									}
									if ($tampil_bulan_tahun == $value) {
										$selected = 'selected';
									} else {
										$selected = '';
									}
								?>
									<option value='<?php echo substr($value, 3, 4) . '-' . substr($value, 0, 2) ?>' <?php echo $selected; ?>><?php echo id_to_bulan(substr($value, 0, 2)) . " " . substr($value, 3, 4) ?></option>

								<?php
								}
								?>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_pelatihan">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>NP</th>
									<th class='text-center'>Nama</th>
									<th class='text-center'>Kategori Pelatihan</th>
									<th class='text-center'>Kode Pelatihan</th>
									<th class='text-center'>Pelatihan</th>
									<th class='text-center'>Tanggal Pengajuan</th>
									<th class='text-center'>Skala Prioritas</th>
									<th class='text-center no-sort'>Vendor</th>
									<th class='text-center no-sort'>Status</th>
									<th class='text-center no-sort'>Aksi</th>

								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
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
									<td><a id="status_updated_by"></a></td>
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

			<div class="modal fade" id="modal-export-excel" tabindex="-1" role="dialog" aria-labelledby="label-modal-export-excel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label-modal-export-excel">Pilih Rentang Tanggal</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<label for="date-range-export">Maksimal 10 hari</label>
								<input class="form-control" id="date-range-export" name="dates" style="width: 100%;">
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success" id="generate-export-excel">Export</button>
						</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>

		<?php
		}

		if ($akses["batal"]) {
		?>
			<!-- Modal -->
			<div class="modal fade" id="modal_batal" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_batal">Batal <?php echo $judul; ?></h4>
						</div>
						<div class="modal-body">

							<table>
								<tr>
									<td>Np Pemohon</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a id="batal_np_karyawan"></a></td>
								</tr>
								<tr>
									<td>Nama Pemohon</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a id="batal_nama"></a></td>
								</tr>
								<tr>
									<td>Dibuat Tanggal</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a id="batal_created_at"></a></td>
								</tr>
								<tr>
									<td>Dibuat Oleh</td>
									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><a id="batal_updated_by"></a></td>
								</tr>
							</table>

							<br>

							<div class="alert alert-info">
								<strong><a id="batal_approval_1_nama"></a></strong><br>
								<p id="batal_approval_1_status"></p>
							</div>

							<div class="alert alert-info">
								<strong><a id="batal_approval_2_nama"></a></strong><br>
								<p id="batal_approval_2_status"></p>
							</div>

							<form role="form" action="<?php echo base_url(); ?>pelatihan/pelatihan/action_batal_pelatihan" id="formulir_tambah" method="post">
								<div class="row">
									<div class="col-lg-12 text-right">
										<input type="hidden" name="batal_id" id="batal_id">
										<input type="submit" name="submit" value="Batalkan Usulan" class="btn btn-danger">
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

		<?php }
		if ($akses["ubah"]) {
		?>
			<!-- Modal -->
			<div class="modal fade" id="modal_ubah" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content" style="overflow-y: initial !important">

						<form enctype="multipart/form-data" method="POST" accept-charset="utf-8" action="<?php echo base_url('pelatihan/pelatihan/action_update_data_pelatihan'); ?>" method="post" id="form-ubah">

							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul; ?></h4>
							</div>
							<div class="modal-body" style="height: 340px;overflow-y: auto;">

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
										<label>Pelatihan</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control" name="edit_nama_pelatihan" id="edit_nama_pelatihan" readonly>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-2">
										<label>Vendor</label>
									</div>
									<div class="col-lg-10">
										<input class="form-control" name="edit_vendor" id="edit_vendor" readonly>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-lg-2">
										<label>Skala Prioritas</label>
									</div>
									<div class="col-lg-10">
										<select id="edit_skala_prioritas" class="form-control" name="edit_skala_prioritas">
											<option value="1">1</option>
											<option value="2">2</option>
											<option value="3">3</option>
											<option value="4">4</option>
											<option value="5">5</option>
										</select>
									</div>
								</div>

								<div class="modal-footer">.
									<input type='hidden' id='edit_id' name='edit_id'>
									<button name='submit' type="submit" value='submit' id="submit_ubah" class="btn btn-primary">Simpan</button>
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
		<?php } ?>

		<?php if ($akses["import"]) 
		{ ?>
			<!-- Modal -->
			<div class="modal fade" id="modal_validasi_import" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_batal">Validasi Import <?php echo $judul;?></h4>
							</div>
							<div class="modal-body">		
								<div class="form-group" style="margin-left: 15px; margin-right: 15px;">

									<div class="row">
										<br>
										<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_validasi_import">
											<thead>
												<tr>
													<th class='text-center'>No</th>
													<th class='text-center'>NP</th>
													<th class='text-center'>Pelatihan</th>
													<th class='text-center'>Skala Prioritas</th>
													<th class='text-center'>Status</th>
												</tr>
											</thead>
											<tbody>
											
											</tbody>
										</table>
										<!-- /.table-responsive -->
									</div>
								</div>
							</div>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->
		<?php } ?>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script src="<?php echo base_url('asset/select2') ?>/select2.min.js"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js') ?>"></script>

<script type="text/javascript">
	$(document).ready(function() {

		$('#date-range-export').daterangepicker({
			locale: {
				format: 'DD-MM-YYYY'
			},
			startDate: moment().subtract(1, 'months').startOf('month').format('DD-MM-YYYY'),
			endDate: moment().subtract(1, 'months').endOf('month').format('DD-MM-YYYY')
		});


		$('#date-range-export').on('apply.daterangepicker', function(ev, picker) {
			// Get the selected start date
			var startDate = picker.startDate;
			var endDate = picker.endDate;

			// Calculate the maximum end date based on the start date
			var maxEndDate = moment(startDate).add(9, 'days').endOf('day');

			// Set the new maximum end date
			if (endDate > maxEndDate) picker.setEndDate(maxEndDate);
		});

		$('.select2').select2();


		// $('#start_date').datetimepicker({
		// 	format: 'DD-MM-YYYY',
		// 	<?php if ($min) { ?>
		// 		minDate: '<?php echo $min; ?>'
		// 	<?php } ?>
		// });

		$('#start_date').datetimepicker({
			format: 'DD-MM-YYYY',
			useCurrent: false,
			icons: {
				previous: 'fa fa-chevron-left',
				next: 'fa fa-chevron-right',
				today: 'fa fa-calendar-check-o',
				clear: 'fa fa-trash',
				close: 'fa fa-times'
			}
		});

		// $('#start_date').on('click', function() {
		// 	$(this).datetimepicker('toggle');
		// });

		$('#end_date').datetimepicker({
			format: 'DD-MM-YYYY',
			<?php if ($min) { ?>
				minDate: '<?php echo $min; ?>'
			<?php } ?>
		});



		$("#start_date").on("dp.change", function(e) {
			// get_atasan_cuti_new();
			var absence_type = document.getElementById("absence_type").value;

			if (absence_type != '2001|1010') //jika bukan cuti besar
			{
				var oldDate = new Date(e.date);
				var newDate = new Date(e.date);
				var startDate = $('#start_date').val();
				newDate.setDate(oldDate.getDate());

				$('#end_date').data("DateTimePicker").minDate(startDate);
				$('#end_date').val(startDate);

				getJumlah();
			} else getJumlah();
			validate_date();
		});


		$("#end_date").on("dp.change", function(e) {
			getJumlah();
			validate_date();
		});


		$("#form_start_date").hide();
		$("#form_end_date").hide();
		$("#form_absence_type").hide();
		$("#form_alasan").hide();
		$("#form_keterangan").hide();

		$("#form_cuti_besar_pilih").hide();
		$("#form_type_cuber").hide();

		$("#form_jumlah_bulan").hide();
		$("#form_jumlah_hari").hide();

		$('#tabel_ess_pelatihan').DataTable().destroy();
		table_serverside();
	});

	$(document).on("click", '.ubah_button', function(e) {
		var id = $(this).data('id');
		var nama = $(this).data('nama');
		var np_karyawan = $(this).data('np-karyawan');
		var pelatihan = $(this).data('pelatihan');
		var vendor = $(this).data('vendor');
		var skala_prioritas = $(this).data('skala_prioritas');

		$("#edit_id").val(id);
		$("#edit_nama").val(nama);
		$("#edit_np_karyawan").val(np_karyawan);
		$("#edit_nama_pelatihan").val(pelatihan);
		$("#edit_vendor").val(vendor);

		getEditSkalaPrioritas(np_karyawan);
		$("#edit_skala_prioritas").val(skala_prioritas);
	});

	$(document).on("click", '#button_tambah', function(e) {
		getCategory();
	});

	function refresh_table_serverside() {
		$('#tabel_ess_pelatihan').DataTable().destroy();
		table_serverside();
	}

	function table_serverside() {
		var table;
		var bulan_tahun = $('#bulan_tahun').val();
		//datatables
		
		table = $('#tabel_ess_pelatihan').DataTable({

			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
				"sEmptyTable": "Tidak ada data di database",
				"emptyTable": "Tidak ada data di database"
			},

			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.

			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "<?php echo site_url("pelatihan/pelatihan/tabel_ess_pelatihan/") ?>" + bulan_tahun,
				"type": "POST"
			},

			//Set column definition initialisation properties.
			"columnDefs": [{
				"targets": 'no-sort', //first column / numbering column
				"orderable": false, //set not orderable
			}, ],

		});
	};

	$(document).ready(function() {
  		$('#formulir_import').on('submit', function(e) {
    		e.preventDefault();
			var formData = new FormData(this);
			$('#overlay').show();
			$.ajax({
				type: "POST",
				dataType: "json",
				url: "<?php echo base_url('pelatihan/pelatihan/import_excel'); ?>",
				data: formData,
				contentType: false, // Penting: Jangan set contentType karena FormData sudah menangani ini
				processData: false, // Penting: Jangan proses data untuk file
				success: function(res) {
					if (res.status == false) {
					} else {
						if (res.data == 'berhasil'){
							location.reload();
						} else {
							var tableBody = $('#tabel_validasi_import tbody');

							tableBody.empty();

							$.each(res.error, function(index, data) {
								var row = $('<tr></tr>'); // Membuat baris baru

								// Menambahkan kolom ke baris
								row.append('<td class="text-center">' + (index + 1) + '</td>');
								var numbersStr = data.replace('Duplikasi pada baris: ', ''); // Menghapus kata "Duplikasi pada baris: "

								var numbersArray = numbersStr.split(',').map(function(num) {
									return parseInt(num.trim());
								});

								row.append('<td class="text-center">' + res.data[numbersArray[0] - 1][0] + '</td>');
								row.append('<td class="text-center">' + res.data[numbersArray[0] - 1][3] + '</td>');
								row.append('<td class="text-center">' + res.data[numbersArray[0] - 1][4] + '</td>');
								row.append('<td class="text-center">' + data + '</td>');
								
								tableBody.append(row);
								$('#overlay').hide();
								$('#modal_validasi_import').modal('show');
							})	
						}
					}
					
				}
			});
		})
	});
	
	$('#kategori').on('select2:open', function () {
		document.getElementById('kategori').setAttribute('onchange', 'inputPelatihan()');
	});
</script>

<script>
	function getCategoryPelatihan() {
		document.getElementById('kategori').setAttribute('onchange', '');
		var val_pelatihan = $('#nama_pelatihan_select').val();
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getCategoryPelatihan'); ?>",
			data: { val_pelatihan: val_pelatihan }, 
			success: function(res) {
				if (res.status == false) {
				} else {
					$('#kategori').val(res.data['id']).trigger('change');					
				}
			}
		});
	}

	function getEndDate() {
		getJumlah();
		var start_date = $('#start_date').val();
		document.getElementById('end_date').setAttribute("min", start_date);
	}
</script>

<script>
	function getNama() {
		var np_karyawan = $('#np_karyawan').val();
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getNama'); ?>",
			data: { vnp_karyawan: np_karyawan }, // "vnp_karyawan=" + np_karyawan,
			beforeSend: ()=>{
				$("#type_cuber option[value='2001|1010']").show();
				$("#type_cuber option[value='2001|2080']").show();
				$('#absence_type option').show();
			},
			success: function(res) {
				if (res.status == false) {
					alert('Silakan isi No. Pokok Dengan Benar.');
					$('#np_karyawan').val('');
					$('#nama').text('');
					$("#form_absence_type").hide();
				} else {
					$('#nama').text(res.message);
					$("#form_absence_type").show();
					getAtasanPelatihan();
					// get_atasan_cuti_new();
					if(res.data.is_pkwt==true){
						$("#type_cuber option[value='2001|1010']").hide();
						$("#type_cuber option[value='2001|2080']").hide();
						$('#absence_type option').each(function() {
							if (!res.allowed_cuti.includes($(this).val()) && $(this).val()!='') {
								if ($(this).is(':selected')) $(this).text('');
								$(this).hide();
							}
						});
					}
				}
				getSkalaPrioritas();
			}
		});
	}

	function getCategory() {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getCategory'); ?>",
			data: {}, 
			success: function(res) {
				if (res.status == false) {
				} else {
					let selectElement = document.getElementById('kategori');
					let option = document.createElement('option');
					option.value = 'Semua';
					option.text = 'Semua';
					selectElement.appendChild(option);
					res.data.forEach(item => {
						let option = document.createElement('option');
						option.value = item['id'];
						option.text = item['nama_kategori_pelatihan'];
						selectElement.appendChild(option);
					});
					// let option = document.createElement('option');
					// 	option.value = "Lainnya";
					// 	option.text = "Lainnya";
					// 	selectElement.appendChild(option);
				}
			}
		});
		getPelatihanAll();
	}

	function getPelatihanAll() {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getPelatihanAll'); ?>",
			data: {}, 
			success: function(res) {
				if (res.status == false) {
				} else {
					let selectElement = document.getElementById('nama_pelatihan_select');
					selectElement.innerHTML = '';
					let option = document.createElement('option');
					option.value = '';
					option.text = '---Pilih Pelatihan---';
					option.selected = true;
					option.disabled = true;
					selectElement.appendChild(option);
					res.data.forEach(item => {
						let option = document.createElement('option');
						option.value = item['id'];
						option.text = item['nama_pelatihan'];
						selectElement.appendChild(option);
					});
				}
			}
		});
	}

	function getPelatihan() {
		var val_kategori = $('#kategori').val();
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getPelatihan'); ?>",
			data: {val_kategori: val_kategori}, 
			success: function(res) {
				if (res.status == false) {
				} else {
					let selectElement = document.getElementById('nama_pelatihan_select');
					selectElement.innerHTML = '';
					let option = document.createElement('option');
					option.value = '';
					option.text = '---Pilih Pelatihan---';
					option.selected = true;
					option.disabled = true;
					selectElement.appendChild(option);
					res.data.forEach(item => {
						let option = document.createElement('option');
						option.value = item['id'];
						option.text = item['nama_pelatihan'];
						selectElement.appendChild(option);
					});
				}
			}
		});
	}

	function getSkalaPrioritas() {
		var np_karyawan = $('#np_karyawan').val();
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getSkalaPrioritas'); ?>",
			data: { vnp_karyawan: np_karyawan }, 
			success: function(res) {
				if (res.status == false) {
				} else {
					let selectElement = document.getElementById('skala_prioritas');
					selectElement.innerHTML = '';
					let option = document.createElement('option');
					option.value = '';
					option.text = '--';
					option.selected = true;
					option.disabled = true;
					selectElement.appendChild(option);
					let array = [1, 2, 3, 4, 5];
					res.data.forEach(item => {
						if (item['skala_prioritas'] === '1'){
							array = array.filter(item1 => item1 !== 1);
						} 
						if (item['skala_prioritas'] === '2'){
							array = array.filter(item1 => item1 !== 2);
						}
						if (item['skala_prioritas'] === '3'){
							array = array.filter(item1 => item1 !== 3);
						}
						if (item['skala_prioritas'] === '4'){
							array = array.filter(item1 => item1 !== 4);
						}
						if (item['skala_prioritas'] === '5'){
							array = array.filter(item1 => item1 !== 5);
						}
					});
					array.forEach(value => {
						let option = document.createElement('option');
						option.value = value;
						option.text = value;
						selectElement.appendChild(option);
					});
				}
			}
		});
	}

	function getEditSkalaPrioritas(np) {
		var np_karyawan = np;
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getSkalaPrioritas'); ?>",
			data: { vnp_karyawan: np_karyawan }, 
			success: function(res) {
				if (res.status == false) {
				} else {
					let selectElement = document.getElementById('edit_skala_prioritas');
					selectElement.innerHTML = '';
					let option = document.createElement('option');
					option.value = '';
					option.text = '--';
					option.selected = true;
					option.disabled = true;
					selectElement.appendChild(option);
					let array = [1, 2, 3, 4, 5];
					res.data.forEach(item => {
						if (item['skala_prioritas'] === '1'){
							array = array.filter(item1 => item1 !== 1);
						} 
						if (item['skala_prioritas'] === '2'){
							array = array.filter(item1 => item1 !== 2);
						}
						if (item['skala_prioritas'] === '3'){
							array = array.filter(item1 => item1 !== 3);
						}
						if (item['skala_prioritas'] === '4'){
							array = array.filter(item1 => item1 !== 4);
						}
						if (item['skala_prioritas'] === '5'){
							array = array.filter(item1 => item1 !== 5);
						}
					});
					array.forEach(value => {
						let option = document.createElement('option');
						option.value = value;
						option.text = value;
						selectElement.appendChild(option);
					});
				}
			}
		});
	}
</script>

<script>
	function inputPelatihan() {
		const category = document.getElementById('kategori').options[document.getElementById('kategori').selectedIndex].text;
		const trainingSelect = document.getElementById('pelatihan_select');
		const trainingText = document.getElementById('pelatihan_text');

		if (category === 'Lainnya') {
			trainingSelect.style.display = 'none';
			trainingText.style.display = 'block';
			// if (!trainingSelect.disabled) {
			// 	trainingSelect.disabled = true;
			// }
			if (document.getElementById('nama_pelatihan_select').required) {
				document.getElementById('nama_pelatihan_select').required = false;
			} 
			if (document.getElementById('nama_pelatihan_text').disabled) {
				document.getElementById('nama_pelatihan_text').disabled = false;
			}
			if (!document.getElementById('nama_pelatihan_text').required) {
				document.getElementById('nama_pelatihan_text').required = true;
			} 
		} else {
			getPelatihan()
			trainingSelect.style.display = 'block';
			trainingText.style.display = 'none';
			// if (trainingSelect.disabled) {
			// 	trainingSelect.disabled = false;
			// }
			if (!document.getElementById('nama_pelatihan_select').required) {
				document.getElementById('nama_pelatihan_select').required = true;
			} 
			if (!document.getElementById('nama_pelatihan_text').disabled) {
				document.getElementById('nama_pelatihan_text').disabled = true;
			}
			if (document.getElementById('nama_pelatihan_text').required) {
				document.getElementById('nama_pelatihan_text').required = false;
			} 
		}
	}
</script>

<script>
	function getAtasanPelatihan() {
		var np_karyawan = $('#np_karyawan').val();

		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getAtasanPelatihan'); ?>",
			data: "vnp_karyawan=" + np_karyawan,
			success: function(msg) {
				if (msg.status == false) {
					alert('Silakan isi No. Pokok Dengan Benar1.');
					$('#approval_1').val('');
					$('#is_poh_approval_1').val('');
					$('#approval_2').val('');
					$('#is_poh_approval_2').val('');
				} else {
					$('#approval_1').val(msg.np_atasan_1);
					$('#is_poh_approval_1').val(msg.is_poh_atasan_1);
					$('#approval_2').val(msg.np_atasan_2);
					$('#is_poh_approval_2').val(msg.is_poh_atasan_2);

					if (msg.np_atasan_1 != "") {
						getNamaAtasan1();
					} else {
						$('#approval_1_input').val('');
						$('#approval_1_input_jabatan').val('');
					}

					if (msg.np_atasan_2 != "") {
						getNamaAtasan2();
					} else {
						$('#approval_2_input').val('');
						$('#approval_2_input_jabatan').val('');
					}
				}
			}
		});
	}
</script>

<script>
	function listNp() {
		$.ajax({
			type: "POST",
			dataType: "html",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getListNp'); ?>",
			success: function(msg) {
				if (msg == '') {
					alert('Silakan isi No. Pokok Dengan Benar.');
					$('#list_np').text('');
				} else {
					$('#list_np').text(msg);
				}
			}
		});
	}
</script>

<script>
	function getNamaAtasan1() {
		console.log('getNamaAtasan1');
		var np_karyawan = $('#approval_1').val();
		var is_poh = $('#is_poh_approval_1').val();

		var data_array = new Array();
		data_array[0] = np_karyawan;
		data_array[1] = is_poh;

		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getNama_approval'); ?>",
			data: "data_array=" + data_array,
			success: function(msg) {
				if (msg.status == false) {
					alert('Silakan isi No. Pokok Dengan Benar.');
					$('#approval_1').val('');
					$('#approval_1_input').val('');
					$('#approval_1_input_jabatan').val('');
				} else {
					$('#approval_1_input').val(msg.data.nama);
					$('#approval_1_input_jabatan').val(msg.data.jabatan);
				}
			}
		});
	}

	function getNamaAtasan2() {
		console.log('getNamaAtasan2');
		var np_karyawan = $('#approval_2').val();
		var is_poh = $('#is_poh_approval_2').val();

		var data_array = new Array();
		data_array[0] = np_karyawan;
		data_array[1] = is_poh;

		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: "<?php echo base_url('pelatihan/pelatihan/ajax_getNama_approval'); ?>",
			data: "data_array=" + data_array,
			success: function(msg) {
				if (msg.status == false) {
					alert('Silakan isi No. Pokok Dengan Benar.');
					$('#approval_2').val('');
					$('#approval_2_input').val('');
					$('#approval_2_input_jabatan').val('');
				} else {
					$('#approval_2_input').val(msg.data.nama);
					$('#approval_2_input_jabatan').val(msg.data.jabatan);
				}
			}
		});
	}
</script>

<script>
	$(document).on("click", '.status_button', function(e) {
		var status_np_karyawan = $(this).data('np-karyawan');
		var status_nama = $(this).data('nama');
		var status_created_at = $(this).data('created-at');
		var status_updated_by = $(this).data('updated-by');
		var status_approval_1_nama = $(this).data('approval-1-nama');
		var status_approval_1_status = $(this).data('approval-1-status');
		var status_approval_2_nama = $(this).data('approval-2-nama');
		var status_approval_2_status = $(this).data('approval-2-status');

		$("#status_np_karyawan").text(status_np_karyawan);
		$("#status_nama").text(status_nama);
		$("#status_created_at").text(status_created_at);
		$("#status_updated_by").text(status_updated_by);
		$("#status_approval_1_nama").text(status_approval_1_nama);
		$("#status_approval_1_status").text(status_approval_1_status);
		$("#status_approval_2_nama").text(status_approval_2_nama);
		$("#status_approval_2_status").text(status_approval_2_status);



	});
</script>

<script>
	$(document).on("click", '.batal_button', function(e) {
		var batal_id = $(this).data('id');
		var batal_np_karyawan = $(this).data('np-karyawan');
		var batal_nama = $(this).data('nama');
		var batal_created_at = $(this).data('created-at');
		var batal_updated_by = $(this).data('updated-by');
		var batal_approval_1_nama = $(this).data('approval-1-nama');
		var batal_approval_1_status = $(this).data('approval-1-status');
		var batal_approval_2_nama = $(this).data('approval-2-nama');
		var batal_approval_2_status = $(this).data('approval-2-status');

		$("#batal_id").val(batal_id);
		$("#batal_np_karyawan").text(batal_np_karyawan);
		$("#batal_nama").text(batal_nama);
		$("#batal_created_at").text(batal_created_at);
		$("#batal_updated_by").text(batal_updated_by);
		$("#batal_approval_1_nama").text(batal_approval_1_nama);
		$("#batal_approval_1_status").text(batal_approval_1_status);
		$("#batal_approval_2_nama").text(batal_approval_2_nama);
		$("#batal_approval_2_status").text(batal_approval_2_status);



	});

	

	function getPilihanAtasanPelatihan(jenis) {
		// alert(jenis);
		var no_pokok = $('#np_karyawan').val();
		var tgl_mulai = $('#start_date').val();
		var tgl_selesai = $('#end_date').val();

		//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
		var periode = tgl_mulai;
		// alert(periode);
		if (periode != "") {
			$("#" + jenis).empty();

			$.ajax({
				type: "POST",
				dataType: "html",
				url: "<?php echo base_url('pelatihan/pelatihan/ajax_getPilihanAtasanPelatihan'); ?>",

				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				//10-07-2021 - Wina menambah parameter perhitungan jam lembur
				data: {
					vnp_karyawan: no_pokok + "#" + periode,
					vno_pokok: no_pokok,
					tgl_mulai: tgl_mulai,
					tgl_selesai: tgl_selesai
				},
				success: function(msg) {
					if (msg != '') {
						//console.log(msg);
						var get_data = JSON.parse(msg);
						var arr_atasan = get_data.atasan;
						var kode_unit = get_data.kode_unit;
						for (var i = 0; i < arr_atasan.length; i++) {
							$("#" + jenis).append($("<option></option>").attr("value", arr_atasan[i]["no_pokok"]).text(arr_atasan[i]["no_pokok"] + " - " + arr_atasan[i]["nama"]));
							//10-07-2021 - Wina mengganti setting selected approval
							get_unit = arr_atasan[i]["kode_unit"];
							if (get_unit.substr(0, kode_unit.length) == kode_unit) {
								$("#" + jenis).val(arr_atasan[i]["no_pokok"]).trigger("change");
							}
						}
						$('.select2').select2();
						// getAtasanLembur(id);

					} else {
						alert('Atasan tidak ditemukan!');
					}
				}
			});
		}
	}
</script>
