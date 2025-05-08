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

								<form role="form" action="<?php echo base_url(); ?>ijt/Data/action_insert_data" id="formulir_tambah" method="post">

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Posisi</label>
											</div>
											<div class="col-lg-7">
												<input type="text" class="form-control" name="posisi" id="posisi">
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Deskripsi</label>
											</div>
											<div class="col-lg-7">
												<textarea class="form-control" name="deskripsi" id="deskripsi" rows="18"></textarea>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Start Date</label>
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
		<?php
		}

		if ($akses["lihat"]) {
		?>

			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						<table width="100%" class="table table-striped table-bordered table-hover" id="table-verval">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>Posisi</th>
									<th class='text-center'>Nama Karyawan</th>
									<th class='text-center'>Curiculum Vitae</th>
									<th class='text-center'>Hasil Verifikasi</th>
									<th class='text-center'>Keterangan</th>
									<th class='text-center'>Aksi</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
					<!-- /.table-responsive -->
				</div>
			</div>

		<?php
		}

		?>

		<div class="modal fade" id="modal_verval" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="label_modal_status">Verifikasi Dokumen</h4>
					</div>
					<form method="post" id="form-verif">
						<div class="modal-body">

							<input type="hidden" id="update-id" name="id">
							<input type="hidden" id="update-nama">
							<div id="file-container-verif">
							</div>

							<div class="form-group">
								<label for="">Status</label>
								<div>
									<label class="mr-3">
										<input type="radio" name="status" data-id="1" value="1"> Lolos
									</label>
									<label>
										<input type="radio" name="status" data-id="2" value="2"> Tidak Lolos
									</label>
								</div>
							</div>

							<div class="form-group">
								<label for="">Keterangan <span class="text-danger">*</span></label>
								<textarea name="keterangan" id="keterangan" class="form-control"></textarea>
							</div>

						</div>
						<div class="modal-footer">
							<div class="form-group">
								<button type="button" class="btn btn-primary btn-simpan">Simpan</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
							</div>
						</div>
					</form>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>

		<div class="modal fade" id="modal-show-cv" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="label_modal_status">Preview CV</h4>
					</div>
					<form method="post">
						<div class="modal-body">

							<input type="hidden" id="update-id" name="id">
							<div id="file-container">
							</div>
							<!-- <div class="form-group">
								<label for="">CV</label>
								<iframe id="cv-preview" style="width: 100%; height: 400px; display: none" frameborder="0"></iframe>
							</div> -->

							<div class="modal-footer">
								<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
							</div>

						</div>
					</form>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
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
		var table;

		$('.select2').select2();

		$('#table-verval').DataTable().destroy();
		table_serverside();
	});

	function showCV(apply_id) {
		$.ajax({
			url: '<?= base_url("ijt/verifikasi_dokumen/get_files") ?>',
			type: 'POST',
			data: {
				apply_id
			},
			dataType: 'JSON',
			success: function(response) {
				let fileContainer = $("#file-container");
				fileContainer.empty();

				if (response.status && response.files.length > 0) {
					response.files.forEach(file => {
						let fileData = JSON.parse(file.file);
						let fileUrl = "<?= base_url('uploads/job_tender/') ?>" + file.np + '/' + fileData.file_name;
						let fileType = fileData.file_type.toLowerCase();

						let displayName = file.nama_dokumen.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());

						let fileElement = "";
						if (fileType.includes("pdf")) {
							fileElement = `<iframe src="${fileUrl}" style="width:100%; height:400px;" frameborder="0"></iframe>`;
						} else if (fileType.includes("jpg") || fileType.includes("jpeg") || fileType.includes("png")) {
							fileElement = `<img src="${fileUrl}" style="max-width:100%; height:auto;" class="img-thumbnail">`;
						} else {
							fileElement = `<p><a href="${fileUrl}" target="_blank">${fileData.file_name}</a></p>`;
						}

						fileContainer.append(`<div class="form-group">
                        <label>${displayName}</label>
                        ${fileElement}
                    </div>`);
					});
				} else {
					fileContainer.html("<p class='text-center text-muted'>Tidak ada file.</p>");
				}
				$('#modal-show-cv').modal('show');
			}
		});
	}

	function showVerif(apply_id) {
		
		$('#form-verif')[0].reset();

		$.ajax({
			url: '<?= base_url("ijt/verifikasi_dokumen/get_files") ?>',
			type: 'POST',
			data: {
				apply_id: apply_id,
			},
			dataType: 'json',
			success: function(response) {
				let fileContainer = $("#file-container-verif");
				fileContainer.empty();

				if (response.status && response.files.length > 0) {
					response.files.forEach(file => {
						let fileData = JSON.parse(file.file);
						let fileUrl = "<?= base_url('uploads/job_tender/') ?>" + file.np + '/' + fileData.file_name;
						let fileType = fileData.file_type.toLowerCase();

						let displayName = file.nama_dokumen.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());

						let fileElement = "";
						if (fileType.includes("pdf")) {
							fileElement = `<iframe src="${fileUrl}" style="width:100%; height:400px;" frameborder="0"></iframe>`;
						} else if (fileType.includes("jpg") || fileType.includes("jpeg") || fileType.includes("png")) {
							fileElement = `<img src="${fileUrl}" style="max-width:100%; height:auto;" class="img-thumbnail">`;
						} else {
							fileElement = `<p><a href="${fileUrl}" target="_blank">${fileData.file_name}</a></p>`;
						}

						fileContainer.append(`<div class="form-group">
                            <label>${displayName}</label>
                            ${fileElement}
                        </div>`);
					});
				} else {
					fileContainer.html("<p class='text-center text-muted'>Tidak ada file tersedia.</p>");
				}				
				$('#update-id').val(apply_id);				
				$('#update-nama').val(response.files[0].nama);

				$('#modal_verval').modal('show');
			}
		});
	}

	function table_serverside() {
		// Inisialisasi DataTable
		table = $('#table-verval').DataTable({
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
				"url": "<?php echo site_url('ijt/verifikasi_dokumen/table_data') ?>",
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
					"data": "nama"
				},
				{
					"data": "file_name",
					render: function(data, type, row) {
						return `<button class="btn btn-primary preview-cv btn-sm" onclick="showCV(${row.id})">Lihat CV</button>`;
					}
				},
				{
					"data": "is_verval",
					render: function(data, type, row) {
						if (data == 0 || data == null) {
							return '<span>Belum diverifikasi</span>';
						} else if (data == 1) {
							return '<span style="color:green;">Lolos</span>';
						} else if (data == 2) {
							return '<span style="color:red;">Tidak Lolos</span>';
						}
					}
				},
				{
					"data": 'keterangan',
					render: function(data, type, row) {
						if (data == null || data == 0) {
							return '<span>Belum diverifikasi</span>'
						}
						return data;
					}
				},
				{
					"data": "actions",
				}
			],

			// Nonaktifkan sorting untuk kolom tertentu
			"columnDefs": [{
				"targets": 'no-sort',
				"orderable": false,
			}]
		});
	}

	$('#modal_verval').on('click', '.btn-simpan', function() {
		let is_verval = $('input[name="status"]:checked').val();
		let keterangan = $('#keterangan').val();
		let nama = $('#update-nama').val();
		let pesan = '';

		if(is_verval == 1) {
			pesan = nama + ` lolos verifikasi`
		}else {
			pesan = nama + ` <b>tidak lolos</b> verifikasi dan tidak dapat lanjut ke tahap berikutnya.`
		}

		if (!is_verval) {
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Status tidak boleh kosong!',
				confirmButtonText: 'OK'
			});
			return;
		}

		if (keterangan === '') {
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Keterangan tidak boleh kosong!',
				confirmButtonText: 'OK'
			});
			return;
		}

		Swal.fire({
			title: 'Apakah anda yakin?',
			html: pesan,
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Simpan',
			cancelButtonText: 'Batal'
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "<?= base_url('ijt/verifikasi_dokumen/action_verval'); ?>",
					type: 'POST',
					data: {
						id: $('#update-id').val(),
						is_verval: is_verval,
						keterangan: keterangan
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Memproses....',
							text: 'Silahkan tunggu',
							allowOutsideClick: false,
							didOpen: () => {
								swal.showLoading();
							}
						});
					},
					success: function(response) {
						Swal.fire({
							icon: 'success',
							title: 'Berhasil',
							text: 'Data berhasil diterima',
							confirmButtonText: 'OK'
						}).then(() => {
							$('#modal_verval').modal('hide');
							$('#table-verval').DataTable().ajax.reload();
						});
					},
					error: function() {
						Swal.fire({
							icon: 'error',
							title: 'Gagal',
							text: 'Terjadi kesalahan saat memproses data',
							confirmButtonText: 'OK'
						});
					}
				})
			}
		})
	})

	$('#modal_verval').on('click', '.btn-tolak', function() {
		let keterangan = $('#keterangan').val();

		if (keterangan === '') {
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Keterangan tidak boleh kosong!',
				confirmButtonText: 'OK'
			});
			return;
		}

		let is_verval = $(this).data('id');

		Swal.fire({
			title: 'Apakah Anda yakin?',
			text: "Data ini akan ditolak!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya, Tolak!',
			cancelButtonText: 'Batal',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "<?php echo base_url('ijt/verifikasi_dokumen/action_verval'); ?>",
					type: "POST",
					data: {
						id: $('#update-id').val(),
						is_verval: is_verval,
						keterangan: keterangan
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Memproses...',
							text: 'Silakan tunggu',
							allowOutsideClick: false,
							didOpen: () => {
								Swal.showLoading();
							}
						});
					},
					success: function(response) {
						Swal.fire({
							icon: 'success',
							title: 'Berhasil!',
							text: 'Data berhasil ditolak.',
							confirmButtonText: 'OK'
						}).then(() => {
							$('#modal_verval').modal('hide');
							$('#table-verval').DataTable().ajax.reload();
						});
					},
					error: function() {
						Swal.fire({
							icon: 'error',
							title: 'Gagal!',
							text: 'Terjadi kesalahan saat memproses data.',
							confirmButtonText: 'OK'
						});
					}
				});
			}
		});
	})
</script>
