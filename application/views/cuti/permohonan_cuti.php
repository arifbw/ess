<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
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
		if (!empty($this->session->flashdata('warning'))) {
		?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('warning'); ?>
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
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul; ?></a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse">
							<div class="panel-body">

								<form role="form" action="<?php echo base_url(); ?>cuti/Permohonan_cuti/action_insert_cuti" id="formulir_tambah" method="post">

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Karyawan</label>
											</div>
											<div class="col-lg-7">
												<select class="form-control select2" onchange="getNama()" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
													<option value=''>---Pilih Karyawan---</option>
													<?php
													foreach ($array_daftar_karyawan->result_array() as $value) {
													?>
														<option value='<?php echo $value['no_pokok'] ?>'><?php echo $value['no_pokok'] . " " . $value['nama'] ?></option>

													<?php
													}
													?>
												</select>
												<!--<input class="form-control" name="np_karyawan" id="np_karyawan" onchange="getNama()" required>-->
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
											<div class="col-lg-2">
												<label></label>
											</div>
											<div class="col-lg-7">
												<textarea class="form-control" name="nama" id="nama" rows="18" readonly required></textarea>
											</div>
										</div>
									</div>

									<!--
														<div class="row">
															<div class="form-group">
																<div class="col-lg-2">
																	<label></label>
																</div>
																<div class="col-lg-7">
														-->
									<input type='hidden' class="form-control" name="nama" id="nama" readonly required>
									<!--		
																</div>		
															</div>
														</div>
														-->

									<div class="row" id='form_absence_type'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>Jenis Cuti</label>
											</div>
											<div class="col-lg-7">
												<select class="form-control" name='absence_type' id="absence_type" onchange="getJenisCuti()" required>
													<option value=''></option>
													<?php
													foreach ($select_mst_cuti->result_array() as $value) {
														echo "<option value='" . "{$value['id']}-{$value['kode_erp']}" . "'>" . $value['uraian'] . "</option>";
													}
													?>
												</select>
											</div>
										</div>
									</div>

									<div class="row" id='form_type_cuber'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>Ambil cuti dari</label>
											</div>
											<div class="col-lg-7">
												<select class="form-control" name='type_cuber' id="type_cuber" onchange="getJenisCuti()" required>
													<option value=''>---Pilih---</option>
													<option value='2001|1000'>Cuti Tahunan</option>
													<option value='2001|1010'>Cuti Besar</option>
													<option value='2001|2080'>Hutang Cuti</option>
												</select>
											</div>
										</div>
									</div>

									<?php
									$bulan_lalu = $data_tanggal	= date('Y-m-d', strtotime('-1 months', strtotime(date('Y-m-d'))));
									$sudah_cutoff = sudah_cutoff($bulan_lalu);

									if ($sudah_cutoff) {
										$min = date('Y-m') . "-01";
									} else {
										$min = '';
									}

									?>

									<div class="row" id='form_cuti_besar_pilih'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>Bulan / Hari</label>
											</div>

											<div class="col-lg-7">
												<select class="form-control" name="cuti_besar_pilih" id="cuti_besar_pilih" style="width: 100%" onchange='getCutiBesarPilih()' required>
													<option value=''>---Pilih---</option>
													<option value='bulan'>Bulan</option>
													<option value='hari'>Hari</option>
												</select>
												<small class="form-text text-muted">
													Pilih apakah akan menggunakan Jatah Cuti Besar Bulan/Hari, Kemudian isikan di kolom "Jumlah Bulan"/"Jumlah Hari". Kolom "End Date" akan memprediksi secara otomatis.
													<br>
													<b>Untuk konversi cuti bulan ke hari silakan hubungi SDM</b>
												</small>
											</div>


										</div>
									</div>

									<div class="row" id='form_start_date'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>Start Date</label>
											</div>
											<div class="col-lg-7">
												<input type="text" class="form-control" name="start_date" id="start_date" onchange="getJumlah()" autocomplete="off" required>
											</div>
										</div>
									</div>

									<div class="row" id='form_end_date'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>End Date</label>
											</div>
											<div class="col-lg-7">
												<input type="text" class="form-control" name="end_date" id="end_date" onchange="getJumlah()" autocomplete="off" required>
											</div>
										</div>
									</div>



									<div class="row" id='form_jumlah_bulan'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>Jumlah Bulan</label>
											</div>
											<div class="col-lg-7">
												<input type="number" class="form-control" name="jumlah_bulan" id="jumlah_bulan" onchange="checkJumlahCuti()" value="0" min="0" required>
											</div>
										</div>
									</div>

									<div class="row" id='form_jumlah_hari'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>Jumlah Hari</label>
											</div>
											<div class="col-lg-7">
												<input type="number" class="form-control" name="jumlah_hari" id="jumlah_hari" onchange="checkJumlahCuti()" value="0" min="0" readonly required>
											</div>
										</div>
									</div>





									<div class="row" id='form_alasan'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>Alasan</label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="alasan" id='alasan' value="" required>
											</div>
										</div>
									</div>

									<div class="row" id='form_keterangan'>
										<div class="form-group">
											<div class="col-lg-2">
												<label>Keterangan</label>
											</div>
											<div class="col-lg-7">
												<select class="form-control" name='keterangan' id="keterangan" required>
													<option value='1'>Dalam Kota</option>
													<option value='2'>Luar Kota</option>
												</select>
											</div>
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
												<input name="is_poh_approval_1" id="is_poh_approval_1"  type="hidden" value="" style="text-transform:uppercase" />
												<input name="np_approvel_poh_1" id="np_approvel_poh_1"  type="hidden" value="" style="text-transform:uppercase" />
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
												<input name="np_approvel_poh_2" id="np_approvel_poh_2" type="hidden" value="" style="text-transform:uppercase" />
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
											<input type="submit" name="submit" id="btn-submit" value="submit" class="btn btn-primary" disabled>
										</div>
									</div>
								</form>


							</div>
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

					<div class="col-lg-8 pull-right">
						<label>&nbsp;</label>
						<button type="button" class="btn btn-success pull-right" id="btn-export-excel"><i class="fa fa-file-excel-o"></i> Export Excel</button>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_cuti">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>NP</th>
									<th class='text-center'>Nama</th>
									<th class='text-center'>Tipe</th>
									<th class='text-center'>Start Date</th>
									<th class='text-center'>End Date</th>
									<th class='text-center no-sort'>Lama</th>
									<th class='text-center'>Alasan</th>
									<th class='text-center'>Keterangan</th>
									<th class='text-center no-sort'>Status</th>
									<!-- <th class='text-center no-sort'>Approval By SDM</th>	 -->
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

			<div class="modal fade" id="modal-export-excel" tabindex="-1" role="dialog" aria-labelledby="label-modal-export-excel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label-modal-export-excel">Pilih Rentang Tanggal</h4>
						</div>
						<div class="modal-body">
							<div class="form-group">
								<!-- <label for="date-range-export">Maksimal 10 hari</label> -->
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
									<td><a id="batal_created_by"></a></td>
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

							<form role="form" action="<?php echo base_url(); ?>cuti/Permohonan_cuti/action_batal_cuti" id="formulir_tambah" method="post">
								<div class="row">
									<div class="col-lg-12 text-right">
										<input type="hidden" name="batal_id" id="batal_id">
										<input type="submit" name="submit" value="Batalkan Cuti" class="btn btn-danger">
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

<script src="<?php echo base_url('asset/select2') ?>/select2.min.js"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js') ?>"></script>

<script type="text/javascript">
	$(document).ready(function() {
		// $('#date-range-export').daterangepicker({
		// 	locale: {
		// 		format: 'DD-MM-YYYY'
		// 	},
		// 	startDate: moment().startOf('month').format('DD-MM-YYYY'),
		// 	// endDate: moment().endOf('month').format('DD-MM-YYYY'),
		// 	endDate: moment().startOf('month').add(9, 'days').endOf('day').format('DD-MM-YYYY')
		// });

		$('#date-range-export').daterangepicker({
			locale: {
				format: 'DD-MM-YYYY'
			},
			startDate: moment().subtract(1, 'months').startOf('month').format('DD-MM-YYYY'),
			endDate: moment().subtract(1, 'months').endOf('month').format('DD-MM-YYYY')
		});


		/* $('#date-range-export').on('apply.daterangepicker', function(ev, picker) {
			// Get the selected start date
			var startDate = picker.startDate;
			var endDate = picker.endDate;

			// Calculate the maximum end date based on the start date
			var maxEndDate = moment(startDate).add(9, 'days').endOf('day');

			// Set the new maximum end date
			if (endDate > maxEndDate) picker.setEndDate(maxEndDate);
		}); */

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
			// var absence_type = document.getElementById("absence_type").value;
			var mst_cuti = document.getElementById("absence_type").value;
			var expld = mst_cuti.split('-');
			var mst_cuti_id = expld[0];
			var absence_type = expld[1];

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

		$('#tabel_ess_cuti').DataTable().destroy();
		table_serverside();
	});

	function refresh_table_serverside() {
		$('#tabel_ess_cuti').DataTable().destroy();
		table_serverside();
	}

	function table_serverside() {
		var table;
		var bulan_tahun = $('#bulan_tahun').val();
		//datatables
		table = $('#tabel_ess_cuti').DataTable({

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
				"url": "<?php echo site_url("cuti/permohonan_cuti/tabel_ess_cuti/") ?>" + bulan_tahun,
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
	function checkJumlahCuti() {
		// var absence_type = document.getElementById("absence_type").value;
		var mst_cuti = document.getElementById("absence_type").value;
		var expld = mst_cuti.split('-');
		var mst_cuti_id = expld[0];
		var absence_type = expld[1];
		var type_cuber = document.getElementById("type_cuber").value;

		if (absence_type == '2001|1010' || type_cuber == '2001|1010') //jika cuti besar
		{
			var np_karyawan = $('#np_karyawan').val();
			var start_date = $("#start_date").val();
			var cuti_besar_pilih = $("#cuti_besar_pilih").val();
			var jumlah_hari = $('#jumlah_hari').val();
			var jumlah_bulan = $('#jumlah_bulan').val();

			var data_array = new Array();
			data_array[0] = np_karyawan;
			data_array[1] = start_date;
			data_array[2] = cuti_besar_pilih;
			data_array[3] = jumlah_hari;
			data_array[4] = jumlah_bulan;
			data_array[5] = type_cuber;


			$.ajax({
				type: "POST",
				dataType: "html",
				url: "<?php echo base_url('cuti/permohonan_cuti/ajax_checkJumlahCutiBesar'); ?>",
				data: "data_array=" + data_array,
				success: function(msg) {
					if (msg) {
						if (msg == "kosong") {
							$('#end_date').val('');

							//tambahan bowo  20 01 2020, Bug cuti bend date bisa di klik lg, 7648-Tri Wibowo
							$('#start_date').val('');
						} else {
							$('#end_date').val(msg);
							validate_date();
						}
					} else {
						alert('Cuti Besar tidak mencukupi');
						$('#jumlah_hari').val('0');
						$('#jumlah_bulan').val('0');
						$('#end_date').val('');

						//tambahan bowo  20 01 2020, Bug cuti bend date bisa di klik lg, 7648-Tri Wibowo
						$('#start_date').val('');

					}
				}
			});

		} else //jika yang lain
		{
			var np_karyawan = $('#np_karyawan').val();
			var jumlah_hari = $('#jumlah_hari').val();
			var jumlah_bulan = $('#jumlah_bulan').val();
			var start_date = $('#start_date').val();
			var end_date = $('#end_date').val();
			var type_cuber = $('#type_cuber').val();

			var data_array = new Array();
			data_array[0] = absence_type;
			data_array[1] = np_karyawan;
			data_array[2] = jumlah_hari;
			data_array[3] = jumlah_bulan;
			data_array[4] = start_date;
			data_array[5] = end_date;
			data_array[6] = type_cuber;


			$.ajax({
				type: "POST",
				dataType: "html",
				url: "<?php echo base_url('cuti/permohonan_cuti/ajax_checkJumlahCuti'); ?>",
				data: "data_array=" + data_array,
				success: function(msg) {
					if (msg == '') {

					} else {
						alert(msg);
						$('#jumlah_bulan').val('0');
						$('#jumlah_hari').val('0');
						$('#end_date').val('');

						//tambahan bowo  20 01 2020, Bug cuti bend date bisa di klik lg, 7648-Tri Wibowo
						$('#start_date').val('');
					}
				}
			});
		}

	}
</script>

<script>
	function checkSisaCutiTahunan() {
		var np_karyawan = $('#np_karyawan').val();

		var data_array = new Array();
		data_array[0] = np_karyawan;

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('cuti/permohonan_cuti/ajax_checkValidateHutangCuti'); ?>",
			data: { data_array : np_karyawan }, // "data_array=" + data_array,
			success: function(res) {
				if(res.data.is_pkwt==true){
					$("#type_cuber option[value='2001|1010']").hide();
					$("#type_cuber option[value='2001|2080']").hide();
				} else{
					$("#type_cuber option[value='2001|1010']").show();
					if (res.status == true) {
						$("#type_cuber option[value='2001|2080']").show();
					} else {
						$("#type_cuber option[value='2001|2080']").hide();
					}
				}
			},
			error: ((xhr)=>{

			})
		});
	}
</script>

<script>
	function checkSisaHutangCuti() {
		var np_karyawan = $('#np_karyawan').val();

		var data_array = new Array();
		data_array[0] = np_karyawan;

		$.ajax({
			type: "POST",
			dataType: "html",
			url: "<?php echo base_url('cuti/permohonan_cuti/ajax_checkSisaHutangCuti'); ?>",
			data: "data_array=" + data_array,
			success: function(res) {
				let jumlah_hari = parseInt($('#jumlah_hari').val());
				let totalHari = parseInt(res) + jumlah_hari; 
				if (totalHari > 32) {
					$("#jumlah_hari").val('0');
					$("#start_date").val('');
					$("#end_date").val('');

					alert(`Sisa hutang cuti karyawan ${np_karyawan} tidak mencukupi \nHutang Cuti = ${res} Hari\nPermohonan = ${jumlah_hari} Hari\nTotal Pengambilan Cuti = ${totalHari} Hari\nMaksimal Hutang Cuti = 32 Hari`);
				}
			}
		});
	}
</script>

<script>
	function getCutiBesarPilih() {

		var cuti_besar_pilih = document.getElementById("cuti_besar_pilih").value;

		if (cuti_besar_pilih == 'bulan') {
			document.getElementById('jumlah_bulan').removeAttribute('readonly');
			document.getElementById("jumlah_hari").readOnly = true;
			$("#form_jumlah_bulan").show();
			$("#form_jumlah_hari").hide();
		} else if (cuti_besar_pilih == 'hari') {
			document.getElementById("jumlah_bulan").readOnly = true;
			document.getElementById('jumlah_hari').removeAttribute('readonly');
			$("#form_jumlah_bulan").hide();
			$("#form_jumlah_hari").show();
		} else {
			document.getElementById("jumlah_bulan").readOnly = true;
			document.getElementById("jumlah_hari").readOnly = true;
			$("#form_jumlah_bulan").hide();
			$("#form_jumlah_hari").hide();
		}

		$('#jumlah_hari').val('0');
		$('#jumlah_bulan').val('0');
		$('#start_date').val('');
		$('#end_date').val('');
	}
</script>


<script>
	function getJenisCuti() {
		// var jenis_cuti = document.getElementById("absence_type").value;
		var mst_cuti = document.getElementById("absence_type").value;
		var expld = mst_cuti.split('-');
		var mst_cuti_id = expld[0];
		var jenis_cuti = expld[1];
		var jenis_cuti_bersama = document.getElementById("type_cuber").value;
		document.getElementById('type_cuber').removeAttribute('required');

		if (jenis_cuti == '') {
			$("#form_start_date").hide();
			$("#form_end_date").hide();
			$("#form_jumlah_bulan").hide();
			$("#form_jumlah_hari").hide();
			$("#form_type_cuber").hide();

			$("#form_alasan").hide();
			$("#form_keterangan").hide();

			$("#form_cuti_besar_pilih").hide();

			document.getElementById("jumlah_hari").readOnly = true;
			document.getElementById("jumlah_bulan").readOnly = true;

			$('#type_cuber').val('').trigger('change');
		} else if (jenis_cuti == '2001|1010') //jika cuti besar
		{
			$("#form_start_date").show();
			$("#form_end_date").show();
			$("#form_jumlah_bulan").hide();
			$("#form_jumlah_hari").hide();
			$("#form_type_cuber").hide();

			$("#form_alasan").show();
			$("#form_keterangan").show();

			$("#form_cuti_besar_pilih").show();
			$('#cuti_besar_pilih').val('').trigger('change');

			document.getElementById("jumlah_hari").readOnly = true;
			document.getElementById("jumlah_bulan").readOnly = true;
			document.getElementById("end_date").readOnly = true;

			$('#type_cuber').val('').trigger('change');
		} else if (jenis_cuti == '2001|1020') //jika cuti bersama
		{
			checkSisaCutiTahunan();

			$("#form_start_date").hide();
			$("#form_end_date").hide();
			$("#form_jumlah_bulan").hide();
			$("#form_jumlah_hari").hide();
			$("#form_type_cuber").show();

			$("#form_alasan").hide();
			$("#form_keterangan").hide();

			$("#form_cuti_besar_pilih").hide();

			document.getElementById("jumlah_hari").readOnly = true;
			document.getElementById("jumlah_bulan").readOnly = true;
			document.getElementById('type_cuber').setAttribute('required', true);

			if (jenis_cuti_bersama == '2001|1000') { //jika ambil dari cuti tahunan
				$("#form_start_date").show();
				$("#form_end_date").show();
				$("#form_jumlah_bulan").hide();
				$("#form_jumlah_hari").show();

				$("#form_alasan").show();
				$("#form_keterangan").show();
				$("#form_type_cuber").show();

				$("#form_cuti_besar_pilih").hide();

				document.getElementById("jumlah_hari").readOnly = true;
				document.getElementById("jumlah_bulan").readOnly = true;
				document.getElementById("jumlah_hari").readOnly = true;

				document.getElementById('end_date').removeAttribute('readonly');
				document.getElementById('cuti_besar_pilih').removeAttribute('required');
			} else if (jenis_cuti_bersama == '2001|1010') { //jika ambil dari cuti besar
				$("#form_start_date").show();
				$("#form_end_date").show();
				$("#form_jumlah_bulan").hide();
				$("#form_jumlah_hari").hide();
				$("#form_type_cuber").show();

				$("#form_alasan").show();
				$("#form_keterangan").show();

				$("#form_cuti_besar_pilih").show();
				$('#cuti_besar_pilih').val('').trigger('change');

				document.getElementById("jumlah_hari").readOnly = true;
				document.getElementById("jumlah_bulan").readOnly = true;
				document.getElementById("end_date").readOnly = true;
			} else if (jenis_cuti_bersama == '2001|2080') { //jika ambil dari hutang cuti
				$("#form_start_date").show();
				$("#form_end_date").show();
				$("#form_jumlah_bulan").hide();
				$("#form_jumlah_hari").show();

				$("#form_alasan").show();
				$("#form_keterangan").show();
				$("#form_type_cuber").show();

				$("#form_cuti_besar_pilih").hide();

				document.getElementById("jumlah_hari").readOnly = true;
				document.getElementById("jumlah_bulan").readOnly = true;
				document.getElementById("jumlah_hari").readOnly = true;

				document.getElementById('end_date').removeAttribute('readonly');
				document.getElementById('cuti_besar_pilih').removeAttribute('required');
			}
		} else {
			$("#form_start_date").show();
			$("#form_end_date").show();
			$("#form_jumlah_bulan").hide();
			$("#form_jumlah_hari").show();

			$("#form_alasan").show();
			$("#form_keterangan").show();
			$("#form_type_cuber").hide();

			$("#form_cuti_besar_pilih").hide();

			document.getElementById("jumlah_hari").readOnly = true;
			document.getElementById("jumlah_bulan").readOnly = true;
			document.getElementById("jumlah_hari").readOnly = true;

			document.getElementById('end_date').removeAttribute('readonly');
			document.getElementById('cuti_besar_pilih').removeAttribute('required');

			$('#type_cuber').val('').trigger('change');
		}

		getJumlah();

		$('#start_date').val('').trigger('change');
		$('#end_date').val('');

		$('#jumlah_hari').val('0');
		$('#jumlah_bulan').val('0');
	}
</script>

<script>
	function getEndDate() {
		getJumlah();
		var start_date = $('#start_date').val();
		document.getElementById('end_date').setAttribute("min", start_date);
	}
</script>

<script>
	function getJumlah() {

		// var jenis_cuti = document.getElementById("absence_type").value;
		var mst_cuti = document.getElementById("absence_type").value;
		var expld = mst_cuti.split('-');
		var mst_cuti_id = expld[0];
		var jenis_cuti = expld[1];
		var jenis_cuti_bersama = document.getElementById("type_cuber").value;

		if (jenis_cuti != '2001|1010') {
			if (jenis_cuti == '2001|1020' && jenis_cuti_bersama == '2001|2080') { // jika cuti bersama dan memilih hutang cuti
				checkSisaHutangCuti();
			}

			//menghitung jumlah hari
			if (($("#start_date").val() != "") && ($("#end_date").val() != "")) {
				var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds

				var start_date = $("#start_date").val();
				var pisah_start = start_date.split("-");
				var tanggal_awal = pisah_start[2] + '/' + pisah_start[1] + '/' + pisah_start[0];
				var firstDate = new Date(tanggal_awal);

				var end_date = $("#end_date").val();
				var pisah_end = end_date.split("-");
				var tanggal_akhir = pisah_end[2] + '/' + pisah_end[1] + '/' + pisah_end[0];
				var secondDate = new Date(tanggal_akhir);

				//menghitung selisih hari
				var diffDays = Math.round(Math.round((secondDate.getTime() - firstDate.getTime()) / (oneDay)));
				$("#jumlah_hari").val(diffDays + 1);

				//menghitung selisih HARI TANPA SABTU & MINGGU
				// var diffDays = 0; // Inisialisasi dengan 0 agar tidak bertambah
				// var currentDate = new Date(firstDate);
				// while (currentDate <= secondDate) {
				// 	var day = currentDate.getDay();
				// 	if (day != 0 && day != 6) { // Jika bukan hari sabtu dan minggu
				// 		diffDays++;
				// 	}
				// 	currentDate.setDate(currentDate.getDate() + 1);
				// }

				// $("#jumlah_hari").val(diffDays);
			} else {
				$("#end_date").val('');
				$("#jumlah_hari").val('0');

				//tambahan bowo  20 01 2020, Bug cuti bend date bisa di klik lg, 7648-Tri Wibowo
				$('#start_date').val('');
			}

			checkJumlahCuti();
		} else {
			if ($('#form_jumlah_bulan').is(':visible')) {
				$('#jumlah_bulan').trigger('change');
			} else if ($('#form_jumlah_hari').is(':visible')) {
				$('#jumlah_hari').trigger('change');
			}
		}

	}
</script>



<script>
	function getNama() {
		var np_karyawan = $('#np_karyawan').val();
		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getNama'); ?>",
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
					getAtasanCuti();
					// get_atasan_cuti_new();
					if(res.data.is_pkwt==true){
						$("#type_cuber option[value='2001|1010']").hide();
						$("#type_cuber option[value='2001|2080']").hide();
						$('#absence_type option').each(function() {
							var mst_cuti = $(this).val();
							var expld = mst_cuti.split('-');
							var mst_cuti_id = expld[0];
							var jenis_cuti = expld[1];
							if (!res.allowed_cuti.includes(jenis_cuti) && $(this).val()!='') {
								if ($(this).is(':selected')) $(this).text('');
								$(this).hide();
							}
						});
					}
				}
			}
		});
	}
</script>

<script>
	function getAtasanCuti() {
		console.log('getAtasanCuti');
		var np_karyawan = $('#np_karyawan').val();

		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getAtasanCuti'); ?>",
			data: "vnp_karyawan=" + np_karyawan,
			success: function(msg) {
				if (msg.status == false) {
					alert('Silakan isi No. Pokok Dengan Benar1.');
					$('#approval_1').val('');
					$('#is_poh_approval_1').val('');
					$('#np_approvel_poh_1').val('');
					$('#approval_2').val('');
					$('#is_poh_approval_2').val('');
					$('#np_approvel_poh_2').val('');
				} else {
					$('#approval_1').val(msg.np_atasan_1);
					$('#is_poh_approval_1').val(msg.is_poh_atasan_1);
					$('#np_approvel_poh_1').val(msg.np_atasan_1);
					$('#approval_2').val(msg.np_atasan_2);
					$('#is_poh_approval_2').val(msg.is_poh_atasan_2);
					$('#np_approvel_poh_2').val(msg.np_atasan_2);

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
		console.log('listNp');
		$.ajax({
			type: "POST",
			dataType: "html",
			url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getListNp'); ?>",
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
		var np_approver = $('#approval_1').val();
		var is_poh = $('#is_poh_approval_1').val();
		var np_poh_approvel = $('#np_approvel_poh_1').val();
		var np_karyawan = $('#np_karyawan').val();
		var data_array = new Array();
		data_array[0] = np_approver;
		data_array[1] = is_poh;
		data_array[2] = 1;
		data_array[3] = np_karyawan;
		data_array[4] = np_poh_approvel;

		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getNama_approval'); ?>",
			data: "data_array=" + data_array,
			success: function(msg) {
				if (msg.status == false) {
					// alert('Silakan isi No. Pokok Dengan Benar.');
					alert(msg.data.message);
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
		var np_approver = $('#approval_2').val();
		var is_poh = $('#is_poh_approval_2').val();
		var np_poh_approvel = $('#np_approvel_poh_2').val();
		var np_karyawan = $('#np_karyawan').val();
		
		var data_array = new Array();
		data_array[0] = np_approver;
		data_array[1] = is_poh;
		data_array[2] = 2;
		data_array[3] = np_karyawan;
		data_array[4] = np_poh_approvel;

		$.ajax({
			type: "POST",
			dataType: "JSON",
			url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getNama_approval'); ?>",
			data: "data_array=" + data_array,
			success: function(msg) {
				if (msg.status == false) {
					// alert('Silakan isi No. Pokok Dengan Benar.');
					alert(msg.data.message);
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
	$(document).on("click", '.batal_button', function(e) {
		var batal_id = $(this).data('id');
		var batal_np_karyawan = $(this).data('np-karyawan');
		var batal_nama = $(this).data('nama');
		var batal_created_at = $(this).data('created-at');
		var batal_created_by = $(this).data('created-by');
		var batal_approval_1_nama = $(this).data('approval-1-nama');
		var batal_approval_1_status = $(this).data('approval-1-status');
		var batal_approval_2_nama = $(this).data('approval-2-nama');
		var batal_approval_2_status = $(this).data('approval-2-status');

		$("#batal_id").val(batal_id);
		$("#batal_np_karyawan").text(batal_np_karyawan);
		$("#batal_nama").text(batal_nama);
		$("#batal_created_at").text(batal_created_at);
		$("#batal_created_by").text(batal_created_by);
		$("#batal_approval_1_nama").text(batal_approval_1_nama);
		$("#batal_approval_1_status").text(batal_approval_1_status);
		$("#batal_approval_2_nama").text(batal_approval_2_nama);
		$("#batal_approval_2_status").text(batal_approval_2_status);



	});

	function validate_date() {
		$('#btn-submit').prop('disabled', true);
		var validate_start_date = $('#start_date').val();
		var validate_end_date = $('#end_date').val();

		// var validate_absence_type = $('#absence_type').find(":selected").val();
		var mst_cuti = $('#absence_type').find(":selected").val();
		var expld = mst_cuti.split('-');
		var mst_cuti_id = expld[0];
		var validate_absence_type = expld[1];
		var validate_np_karyawan = $('#np_karyawan').find(":selected").val();

		//absence_type=='2001|1010' hanya pakai start date

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo base_url('cuti/permohonan_cuti/ajax_check_validate_date'); ?>",
			data: {
				start_date: validate_start_date,
				end_date: validate_end_date,
				np_karyawan: validate_np_karyawan,
				absence_type: validate_absence_type
			},
			success: function(response) {
				if (response.status == true) {
					$('#btn-submit').prop('disabled', false);
				} else {
					alert(response.message);
					$('#btn-submit').prop('disabled', true);
				}
			},
			error: function() {
				$('#btn-submit').prop('disabled', true);
			}
		});
	}

	function getPilihanAtasanCuti(jenis) {
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
				url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getPilihanAtasanCuti'); ?>",

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

	$('#btn-export-excel').on('click', () => {
		$('#modal-export-excel').modal('show');
	})

	$('#generate-export-excel').on('click', () => {
		let dates = $('#date-range-export').val();
		let start_date = dates.split(' - ')[0];
		let end_date = dates.split(' - ')[1];
		window.open(`<?= base_url() ?>cuti/export_excel/generate?start_date=${start_date}&end_date=${end_date}`, '_blank');
	})
</script>

<!-- atasan cuti -->
<!-- <script>
	var data_atasan1_temp = [];
	var data_atasan2_temp = [];

	function get_atasan_cuti_new() {
		let tanggal_mulai = $('#start_date').val();
		let np = $('#np_karyawan').val();
		if (np != '' && tanggal_mulai != '') {
			$.ajax({
				type: "POST",
				dataType: "json",
				url: `<?= base_url() ?>cuti/permohonan_cuti/get_atasan_cuti_new`,
				data: {
					np,
					tanggal_mulai
				},
				beforeSend: () => {
					data_atasan1_temp = [];
					data_atasan2_temp = [];

					$('#approval_1').empty().trigger('change');
					$('#approval_2').empty().trigger('change');
				}
			}).then((res) => {
				// console.log(res);
				data_atasan1_temp = res.data_atasan1;
				data_atasan2_temp = res.data_atasan2;

				$('#approval_1').select2({
					placeholder: 'Pilih Atasan',
					data: res.data_atasan1.map((o) => {
						return {
							id: o.no_pokok,
							text: `(${o.no_pokok}) ${o.nama}`
						};
					})
				}).trigger('change');

				$('#approval_2').select2({
					placeholder: 'Pilih Atasan',
					data: res.data_atasan2.map((o) => {
						return {
							id: o.no_pokok,
							text: `(${o.no_pokok}) ${o.nama}`
						};
					})
				}).trigger('change');
			});
		} else {
			data_atasan1_temp = [];
			data_atasan2_temp = [];

			$('#approval_1').empty().trigger('change');
			$('#approval_2').empty().trigger('change');
		}
	}

	$('#approval_1').on('change', (e) => {
		let find = _.find(data_atasan1_temp, o => {
			return o.no_pokok == e.target.value;
		});
		if (typeof find != 'undefined') {
			$('#approval_1_input').val(find.nama);
			$('#approval_1_input_jabatan').val(find.nama_jabatan);
		} else {
			$('#approval_1_input').val('');
			$('#approval_1_input_jabatan').val('');
		}
	});

	$('#approval_2').on('change', (e) => {
		let find = _.find(data_atasan2_temp, o => {
			return o.no_pokok == e.target.value;
		});
		if (typeof find != 'undefined') {
			$('#approval_2_input').val(find.nama);
			$('#approval_2_input_jabatan').val(find.nama_jabatan);
		} else {
			$('#approval_2_input').val('');
			$('#approval_2_input_jabatan').val('');
		}
	});
</script> -->
<!-- END: atasan cuti -->
