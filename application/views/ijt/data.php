<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" />
<style>
	.upload-container {
		background: #f9f9f9;
		padding: 10px;
		border-radius: 5px;
		border: 1px solid #ddd;
		margin-bottom: 10px;
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

								<form role="form" action="<?php echo base_url(); ?>ijt/Data/action_insert_data" id="formulir_tambah" method="post" enctype="multipart/form-data">

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Posisi</label>
												<span class="text-danger">*</span>
											</div>
											<div class="col-lg-7">
												<select name="nama_jabatan" id="posisi" class="form-control select2" style="width: 100%">
													<option value="" selected disabled>-Pilih Posisi-</option>
													<?php foreach ($jabatan as $row) : ?>
														<option value="<?= $row->nama_jabatan ?>" data-kode-jabatan="<?= $row->kode_jabatan ?>" data-kode-unit="<?= $row->kode_unit ?>"><?= $row->nama_jabatan ?> (<?= $row->kode_unit ?>)</option>
													<?php endforeach; ?>
												</select>
											</div>
											<input type="hidden" name="kode_jabatan" id="kode_jabatan">
											<input type="hidden" name="kode_unit" id="kode_unit">
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Deskripsi</label>
												<span class="text-danger">*</span>
											</div>
											<div class="col-lg-7">
												<textarea class="form-control" name="deskripsi" id="deskripsi" rows="18"></textarea>
											</div>
										</div>
									</div>

									<!-- <div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Gambar</label>
												<span class="text-danger">*</span>
											</div>
											<div class="col-lg-7">
												<input type="file" name="gambar" accept="image/jpg, image/jpeg, image/png">
												<span class="text-danger">Maksimal 2 MB, ekstensi JPG/JPEG/PNG</span>
											</div>
										</div>
									</div> -->


									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Start Date</label>
												<span class="text-danger">*</span>
											</div>
											<div class="col-lg-7">
												<input type="date" class="form-control" name="start_date" id="start_date" autocomplete="off" required>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>End Date</label>
												<span class="text-danger">*</span>
											</div>
											<div class="col-lg-7">
												<input type="date" class="form-control" name="end_date" id="end_date" autocomplete="off" required>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-9 text-right">
											<input type="submit" name="submit" id="btn-submit" value="submit" class="btn btn-primary">
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

		if ($akses["lihat"]) {
		?>
			<!-- <div class="form-group">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label>Bulan</label>							
							<select class="form-control select2" id='bulan_tahun' name='bulan_tahun' onchange="refresh_table_serverside()" style="width: 100%;">
								<option value='0'>Semua</option>
							</select>
						</div>
					</div>

					<div class="col-lg-8 pull-right">
						<label>&nbsp;</label>
						<button type="button" class="btn btn-success pull-right" id="btn-export-excel"><i class="fa fa-file-excel-o"></i> Export Excel</button>
					</div>
				</div>
			</div> -->

			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						<table width="100%" class="table table-striped table-bordered table-hover" id="table-data">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>Posisi</th>
									<th class='text-center'>Deskripsi / Kualifikasi</th>
									<th class='text-center'>Jumlah Pendaftar</th>
									<th class='text-center'>Start Date</th>
									<th class='text-center'>End Date</th>
									<?php if ($this->session->userdata('grup') == '5'): ?>
										<th class='text-center'>Informasi</th>
									<?php endif ?>
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

			<div class="modal fade" id="modal_apply" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_status">Upload CV</h4>
						</div>
						<form action="<?= site_url('ijt/Data/action_apply_data') ?>" id="form_apply" method="post" enctype="multipart/form-data">
							<div class="modal-body">
								<input type="hidden" id="apply-id" name="job_id">
								<div class="form-group">
									<label for="">Curriculum Vitae <span class="text-danger">*</span></label>
									<div id="cv-container" class="upload-container">
										<div class="cv-upload input-group">
											<input type="file" name="file_cv[]" class="form-control" accept="application/pdf, image/jpg, image/jpeg, image/png" required style="margin-bottom: 10px;">
											<span class="text-danger"><b><small>Dokumen PDF/JPG/JPEG/PNG Max 8 MB</small></b></span>
											<span class="text-danger error-file_cv"></span>
											<span class="input-group-btn">
												<button type="button" class="btn btn-danger remove-cv" style="display: none;"><i class="fa fa-trash"></i></button>
											</span>
										</div>
									</div>
									<button type="button" id="add-cv" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah CV</button>
								</div>

								<div class="form-group">
									<label for="">Dokumen Pendukung <span class="text-danger">*</span></label>
									<div id="doc-container" class="upload-container">
										<div class="doc-upload input-group">
											<input type="file" name="file_doc[]" class="form-control" accept="application/pdf, image/jpg, image/jpeg, image/png" required style="margin-bottom: 10px;">
											<span class="text-danger"><b><small>Dokumen PDF/JPG/JPEG/PNG Max 8 MB</small></b></span>
											<span class="input-group-btn">
												<button type="button" class="btn btn-danger remove-doc" style="display: none;"><i class="fa fa-trash"></i></button>
											</span>
										</div>
									</div>
									<button type="button" id="add-doc" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Dokumen Pendukung</button>
								</div>

								<div class="form-group">
									<label for="">Motivasi <span class="text-danger">*</span></label>
									<textarea name="motivasi" id="motivasi" class="form-control" required></textarea>
									<span class="text-danger error-motivasi"></span>
								</div>

							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
								<input type="submit" name="submit" id="btn-submit" value="Submit" class="btn btn-primary">
							</div>
						</form>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>

		<?php
		}

		if (@$akses['ubah']) { ?>
			<!-- Modal Status -->
			<div class="modal fade" id="modal_data" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_status">Ubah <?php echo $judul; ?></h4>
						</div>
						<form action="<?= site_url('ijt/Data/action_update_data') ?>" id="formulir_update" method="post" enctype="multipart/form-data">
							<div class="modal-body">

								<input type="hidden" id="update-id" name="id">
								<div class="form-group">
									<label for="">Posisi</label>
									<select name="nama_jabatan" id="update-nama_jabatan" class="form-control select2" style="width: 100%">
										<?php foreach ($jabatan as $row) : ?>
											<option value="<?= $row->nama_jabatan ?>" data-kode-jabatan="<?= $row->kode_jabatan ?>" data-kode-unit="<?= $row->kode_unit ?>"><?= $row->nama_jabatan ?></option>
										<?php endforeach; ?>
									</select>
									<input type="hidden" id="update-kode_jabatan" name="kode_jabatan">
									<input type="hidden" id="update-kode_unit" name="kode_unit">
									<span class="text-danger" id="error-nama_jabatan"></span>
								</div>

								<div class="form-group">
									<label for="">Deskripsi</label>
									<textarea name="deskripsi" id="update-deskripsi" class="form-control"></textarea>
									<span class="text-danger" id="error-deskripsi"></span>
								</div>

								<!-- <div class="form-group">
								<label for="">Gambar</label>
								<input type="file" name="gambar" accept="image/jpg, image/jpeg, image/png">
								<span class="text-danger">Maksimal 2 MB, ekstensi JPG/JPEG/PNG</span>
								<br>
								<a href="#" target="__BLANK" class="btn btn-primary" id="lihat-gambar">Lihat Gambar</a>
								<span class="text-danger" id="error-gambar"></span>
							</div> -->

								<div class="form-group">
									<label for="">Start Date</label>
									<input type="date" class="form-control" name="start_date" id="update-start_date">
									<span class="text-danger" id="error-start_date"></span>
								</div>

								<div class="form-group">
									<label for="">End Date</label>
									<input type="date" class="form-control" name="end_date" id="update-end_date">
									<span class="text-danger" id="error-end_date"></span>
								</div>

							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
								<input type="submit" name="submit" id="btn-submit" value="submit" class="btn btn-primary">
							</div>
						</form>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
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
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js') ?>"></script>

<script type="text/javascript">
	$(document).ready(function() {

		$('#add-cv').click(function() {
			let newCvInput = $('<div class="cv-upload input-group">\n' +
				'<input type="file" name="file_cv[]" class="form-control" accept="application/pdf, image/jpg, image/jpeg, image/png" required style="margin-bottom: 10px;">\n' +
				'<span class="input-group-btn">\n' +
				'<button type="button" class="btn btn-danger remove-cv" style="margin-bottom: 10px;"><i class="fa fa-trash"></i></button>\n' +
				'</span>\n' +
				'</div>');
			$('#cv-container').append(newCvInput);
		});

		$(document).on('click', '.remove-cv', function() {
			$(this).closest('.cv-upload').remove();
		});

		$('#add-doc').click(function() {
			let newDocInput = $('<div class="doc-upload input-group">\n' +
				'<input type="file" name="file_doc[]" class="form-control" accept="application/pdf, image/jpg, image/jpeg, image/png" required style="margin-bottom: 10px;">\n' +
				'<span class="input-group-btn">\n' +
				'<button type="button" class="btn btn-danger remove-doc" style="margin-bottom: 10px;"><i class="fa fa-trash"></i></button>\n' +
				'</span>\n' +
				'</div>');
			$('#doc-container').append(newDocInput);
		});

		$(document).on('click', '.remove-doc', function() {
			$(this).closest('.doc-upload').remove();
		});

		var table;

		$('.select2').select2();

		$('#lihat-data').addClass('d-none');

		$('#posisi').on('change', function() {
			let selectedOption = $(this).find('option:selected');
			let namaJabatan = selectedOption.val();
			let kodeUnit = selectedOption.data('kode-unit');
			let kodeJabatan = selectedOption.data('kode-jabatan');

			$('#kode_jabatan').val(kodeJabatan)
			$('#kode_unit').val(kodeUnit)
		});

		$('#update-nama_jabatan').on('change', function() {
			let selectedOption = $(this).find('option:selected');
			let namaJabatan = selectedOption.val();
			let kodeUnit = selectedOption.data('kode-unit');
			let kodeJabatan = selectedOption.data('kode-jabatan');

			$('#update-kode_jabatan').val(kodeJabatan)
			$('#update-kode_unit').val(kodeUnit)
		})

		$('#start_date').on('change', function() {
			const startDate = new Date($(this).val());
			const endDateInput = $('#end_date');

			endDateInput.attr('min', $(this).val());

			if (endDateInput.val()) {
				const endDate = new Date(endDateInput.val());
				if (endDate < startDate) {
					alert('End Date tidak boleh kurang dari Start Date.');
					endDateInput.val('');
				}
			}
		});

		$(document).on('change', 'input[type="file"]', function() {
			const file = this.files[0];			
			if (file) {
				const fileSize = file.size / 1024 / 1024;
				const fileExtension = file.name.split('.').pop().toLowerCase();

				if (fileSize > 8) {
					Swal.fire('Error', 'Ukuran file tidak boleh lebih dari 8 MB', 'error');
					$(this).val('');
					return;
				}

				if (!['jpg', 'jpeg', 'pdf', 'png'].includes(fileExtension)) {
					Swal.fire('Error', 'Ekstensi file harus PDF atau JPG/JPEG/PNG', 'error');
					$(this).val('');
				}
			}
		});

		$('#formulir_update').on('submit', function(e) {
			e.preventDefault();

			$('.text-danger').text('');

			let formData = new FormData(this);


			Swal.fire({
				title: 'Sedang Memproses...',
				showConfirmButton: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			$.ajax({
				url: $(this).attr('action'),
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response) {
					if (!response.status) {
						Swal.close();

						$.each(response.errors, function(field, error) {
							$(`#error-${field}`).text(error);
						});

					} else {
						Swal.fire({
							icon: 'success',
							title: response.message
						}).then(() => {
							$('#modal_data').modal('hide');
							$('#table-data').DataTable().ajax.reload();
						});
					}
				},
				error: function(xhr) {
					Swal.fire({
						icon: 'error',
						title: 'Kesalahan Server',
						text: xhr.responseText
					});
				}
			});

		});

		$('#end_date').on('change', function() {
			const endDate = new Date($(this).val());
			const startDateInput = $('#start_date');

			if (startDateInput.val()) {
				const startDate = new Date(startDateInput.val());
				if (endDate < startDate) {
					alert('End Date tidak boleh kurang dari Start Date.');
					$(this).val('');
				}
			}
		});

		$('#update-start_date').on('change', function() {
			const startDate = new Date($(this).val());
			const endDateInput = $('#update-end_date');

			endDateInput.attr('min', $(this).val());

			if (endDateInput.val()) {
				const endDate = new Date(endDateInput.val());
				if (endDate < startDate) {
					alert('End Date tidak boleh kurang dari Start Date.');
					endDateInput.val('');
				}
			}
		});

		$('#update-end_date').on('change', function() {
			const endDate = new Date($(this).val());
			const startDateInput = $('#update-start_date');

			if (startDateInput.val()) {
				const startDate = new Date(startDateInput.val());
				if (endDate < startDate) {
					alert('End Date tidak boleh kurang dari Start Date.');
					$(this).val('');
				}
			}
		});

		$('#table-data').DataTable().destroy();
		table_serverside();
	});

	$('#form_apply').on('submit', function(e) {
		e.preventDefault();

		Swal.fire({
			title: "Setelah Anda mengirimkan CV, sistem secara automatis akan mengirimkan notifikasi kepada Kadiv.",
			showCancelButton: true,
			confirmButtonText: "Lanjut",
			cancelButtonText: "Batal",
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				$('.text-danger').text('');
				let formData = new FormData(this);
				$.ajax({
					url: $(this).attr('action'),
					type: $(this).attr('method'),
					data: formData,
					processData: false,
					contentType: false,
					dataType: 'json',
					success: function(response) {
						if (response.status) {
							Swal.fire('Success', response.message, 'success');
							$('#modal_apply').modal('hide');
							$('#table-data').DataTable().ajax.reload();
						} else {
							if (response.errors.file_cv) {
								$('#cv').next('.text-danger').text(response.errors.file_cv);
							}
							if (response.errors.motivasi) {
								$('.error-motivasi').text(response.errors.motivasi);
							}
							if (response.errors.job_id) {
								alert(response.errors.job_id);
							}
						}
					}
				});
			}
		});
	});

	function table_serverside() {
		// Inisialisasi DataTable
		table = $('#table-data').DataTable({
			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
				"sEmptyTable": "Tidak ada data di database",
			},
			"processing": true, // Tampilkan indikator pemrosesan
			"serverSide": true, // Aktifkan mode server-side
			"order": [], // Tanpa urutan awal

			// Load data dari sumber Ajax
			"ajax": {
				"url": "<?php echo site_url('ijt/data/table_data') ?>",
				"type": "POST",
			},

			// Definisikan kolom berdasarkan JSON key
			"columns": [{
					"data": "no",
					"orderable": false
				},
				{
					"data": "nama_jabatan"
				},
				{
					"data": "deskripsi"
				},
				{
					"data": "jumlah_pendaftar"
				},
				{
					"data": "start_date"
				},
				{
					"data": "end_date"
				},
				<?php if ($this->session->userdata('grup') == '5'): ?> {
						"data": "info_pengguna"
					},
				<?php endif ?> {
					"data": "actions",
					"orderable": false,
					"searchable": false
				}
			],

			// Nonaktifkan sorting untuk kolom tertentu
			"columnDefs": [{
				"targets": 'no-sort',
				"orderable": false,
			}]
		});
	}


	$('#table-data').on('click', '.btn-update', function() {
		var tr = $(this).closest('tr');
		var data = table.row(tr).data();
		$.each(data, function(key, value) {
			$('#update-' + key).val(value);
		});

		// if (data.gambar == null) {
		// 	$('#lihat-gambar').addClass('d-none');
		// }

		$('#update-nama_jabatan').val(data.nama_jabatan).trigger('change');
		$('#update-start_date').val(data.m_date).trigger('change');
		$('#update-end_date').val(data.e_date).trigger('change');

		$('#modal_data').modal('show');

	})

	$('#table-data').on('click', '.btn-lihat-poster', function() {
		var tr = $(this).closest('tr');
		var data = table.row(tr).data();

		if (data && data.gambar && data.gambar.file_name) {
			var fileUrl = '<?= site_url('uploads/images/job_tender/') ?>' + encodeURIComponent(data.gambar.file_name);
			window.open(fileUrl, '_blank');
		} else {
			Swal.fire({
				icon: 'error',
				title: 'Gagal',
				text: 'Tidak ada poster!'
			});
		}
	});

	$('#table-data').on('click', '.btn-apply', function() {

		var tr = $(this).closest('tr');
		var data = table.row(tr).data();

		$.each(data, function(key, value) {
			$('#apply-' + key).val(value);
		});

		$('#modal_apply').modal('show');

	})

	$('#table-data').on('click', '.btn-hapus', function() {
		var tr = $(this).closest('tr');
		let data = table.row(tr).data();

		let {
			id
		} = data;

		Swal.fire({
			title: "Apakah anda yakin ingin menghapus data?",
			showCancelButton: true,
			confirmButtonText: "Hapus",
		}).then((result) => {
			if (result.isConfirmed) {
				location.href = '<?php echo site_url('ijt/data/destroy/') ?>' + id;
			}
		});
	})
</script>
