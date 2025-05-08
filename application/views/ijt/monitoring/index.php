<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />

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

		<?php if (!empty($success)) { ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $success; ?>
			</div>
		<?php }
		if (!empty($this->session->flashdata('success'))) { ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('success'); ?>
			</div>
		<?php }
		if (!empty($warning)) { ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $warning; ?>
			</div>
		<?php }
		if (!empty($this->session->flashdata('warning'))) { ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('warning'); ?>
			</div>
		<?php } ?>
		<?php
		if (@$this->akses["lihat"]) {
		?>
			<div class="row" style="margin-bottom: 2% !important;">
				<div class="col-md-4">
				</div>
				<div class="col-md-4 col-md-offset-4">
					<input type="text" class="form-control datatable-searchable" placeholder="Pencarian" />
				</div>
			</div>
			<div class="row">
				<table width="100%" class="table table-striped table-bordered table-hover" id="table">
					<thead>
						<tr>
							<th class='text-center'>Posisi</th>
							<th class='text-center'>No</th>
							<th class='text-center'>Nama Karyawan</th>
							<th class='text-center'>Seleksi Administrasi</th>
							<th class='text-center'>Seleksi HR</th>
							<th class='text-center'>Hasil Interview</th>
							<th class='text-center'>Kesimpulan</th>
							<th class='text-center'>Eviden</th>
							<th class='text-center'>Keterangan</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<!-- /.table-responsive -->
			</div>

			<!-- Modal -->
			<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
				<div class="modal-dialog modal-lg" id="detail_content">
				</div>
			</div>
		<?php
		}
		?>
		<div class="modal fade" id="modal-keterangan" role="dialog" aria-labelledby="detail-title" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content" style="overflow: hidden;">
					<div class="modal-header">
						<h3 class="modal-title" id="keterangan-title"><strong>Keterangan</strong></h3>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<form method="post" id="form_keterangan">
						<input type="hidden" id="update-apply_id" name="apply_id">
						<input type="hidden" id="jenis_verval" name="jenis_verval" value="keterangan">
						<div class="modal-body">
							<div class="form-group">
								<label for="">Keterangan <span class="text-danger">*</span></label>
								<textarea name="keterangan" id="update-keterangan" class="form-control" <?= (!$akses) ? 'readonly' : '' ?> style="max-width: 100%"></textarea>
							</div>
						</div>
						<?php if ($akses) : ?>
							<div class="modal-footer">
								<div class="form-group">
									<button type="button" class="btn btn-primary btn-simpan">Simpan</button>
									<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
								</div>
							</div>
						<?php else : ?>
							<div class="modal-footer">
								<div class="form-group">
									<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
								</div>
							</div>
						<?php endif; ?>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modal-verif" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="label_modal_status"></h4>
				</div>
				<form method="post" id="form_verif">
					<div class="modal-body" style="overflow: hidden;">

						<input type="hidden" id="update-apply_id" name="apply_id">
						<input type="hidden" id="jenis_verval" name="jenis_verval">

						<div class="form-group">
							<label for="">Status</label>
							<div>
								<label class="mr-3">
									<input type="radio" name="status" value="1"> Lolos
								</label>
								<label>
									<input type="radio" name="status" value="2"> Tidak Lolos
								</label>
							</div>
						</div>

						<div class="form-group">
							<label for="">Keterangan <span class="text-danger">*</span></label>
							<textarea name="keterangan" id="keterangan" class="form-control" style="max-width: 100%"></textarea>
						</div>

					</div>
					<?php if (isset($akses)) : ?>
						<div class="modal-footer">
							<div class="form-group">
								<button type="button" class="btn btn-primary btn-simpan">Simpan</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
							</div>
						</div>
					<?php endif; ?>
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
						<div id="file-container"></div>

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
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script type="text/javascript">
	var odata;

	function RefreshTable(tableId, urlData) {
		$.getJSON(urlData, null, function(json) {
			table = $(tableId).dataTable();
			oSettings = table.fnSettings();

			table.fnClearTable(this);

			for (var i = 0; i < json.aaData.length; i++) {
				table.oApi._fnAddData(oSettings, json.aaData[i]);
			}

			oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
			table.fnDraw();
		});
	}
</script>
<!-- javascript untuk di modal -->
<script type="text/javascript">
	function generateHtml(response = null) {
		<?php if ($_SESSION["grup"] != 4) { ?>
			if (response === 'tersedia') {
				$('#btn-action').html('<div class="form-check"><input name="setuju" class="form-check-input accept-btn" type="checkbox" value="" id="defaultCheck1"><label class="form-check-label" for="defaultCheck1"> Saya ingin mendaftar ke agenda ini.</label></div><br/><button class="btn btn-primary btn-block daftar-agenda" type="button" disabled>Daftar Ke Agenda</button>');
			} else if (response === 'penuh') {
				$('#btn-action').html('<button class="btn btn-sm btn-danger">Maaf, kuota agenda sudah penuh.</button>');
			} else if (response === 'terdaftar') {
				$('#btn-action').html('<button class="btn btn-sm btn-success">Anda sudah terdaftar pada agenda ini.</button>');
			}
		<?php } else { ?>
			if (response === 'tersedia') {
				$('#btn-action').html(`<div class="form-check">
                                        <input name="setuju" class="form-check-input accept-btn" type="checkbox" value="" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1"> Daftarkan karyawan ke agenda ini.</label>
                                    </div>
                                    <div class="" style="display: none;" id="div-list_karyawan">
                                        <select style="width: 100%;" name="list_karyawan" class="select2 form-check-input" id="list_karyawan" onchange="check_btn_enable()" multiple></select>
                                    </div>
                                    <div id="div-message"></div>
                                    <br/>
                                    <button class="btn btn-primary btn-block daftarkan-karyawan" type="button" disabled id="btn-register" onclick="registrasi_karyawan();">Daftar Ke Agenda</button>`);
				generateOption('#list_karyawan', '<?= base_url('sikesper/agenda/list_karyawan/'); ?>' + $('#form-detail').data('agenda'), 'Pilih karyawan (bisa lebih dari satu)');
			}
		<?php } ?>
	}

	function showCV(apply_id) {
		$.ajax({
			url: '<?= base_url("ijt/monitoring/get_files") ?>',
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

				// Tetap munculkan modal meskipun tidak ada file
				$('#modal-show-cv').modal('show');
			}
		});
	}


	$(document).ready(function() {

		var akses = <?= json_encode($akses); ?>;

		var ocolumn = [{
				data: 'nama_jabatan',
			},
			{
				data: 'nama_karyawan',
				searchable: false,
				className: 'text-center',
				render: function(data, type, row, meta) {
					return meta.row + meta.settings._iDisplayStart + 1;
				}
			},
			{
				data: 'nama_karyawan',
			},
			{
				data: 'jenis_verval',
				searchable: false,
				className: 'text-center',
				render: function(data, type, row, meta) {
					return renderVervalStatus(data, row, 'administrasi');
				}
			},
			{
				data: 'jenis_verval',
				searchable: false,
				className: 'text-center',
				render: function(data, type, row, meta) {
					return renderVervalStatus(data, row, 'hr');
				}
			},
			{
				data: 'jenis_verval',
				searchable: false,
				className: 'text-center',
				render: function(data, type, row, meta) {
					return renderVervalStatus(data, row, 'interview');
				}
			},
			{
				data: 'jenis_verval',
				searchable: false,
				className: 'text-center',
				render: function(data, type, row, meta) {
					return renderVervalStatus(data, row, 'kesimpulan');
				}
			},
			{
				data: 'file',
				render: function(data, type, row, meta) {
					const file = JSON.parse(data);
					return `<button class="btn btn-primary preview-cv btn-sm" onclick="showCV(${row.apply_id})"> Lihat CV</button>`;
				}
			},
			{
				data: 'keterangan',
				render: function(data, type, row, meta) {
					return `<button class="btn btn-primary btn-detail btn-sm" type="button">Detail</button>`;
				}
			}
		];


		function renderVervalStatus(data, row, jenis) {
			const jenisVervalArray = data.split(',');
			const isVervalArray = row.is_verval.split(',');

			const vervalMapping = jenisVervalArray.map((jenis, index) => ({
				jenis: jenis.trim(),
				isVerval: isVervalArray[index]?.trim()
			}));

			const status = vervalMapping.find(v => v.jenis === jenis);

			const isAdministrasiTidakLolos = vervalMapping.some(v => v.jenis === 'administrasi' && v.isVerval === '2');

			if (isAdministrasiTidakLolos) {
				if (jenis === 'kesimpulan') {
					return '<span style="color: red;">Tidak Lolos</span>';
				} else if (jenis === 'interview' || jenis === 'hr') {
					return '';
				}
			}

			if (status && status.isVerval === '1') {
				return `<span style="color: green;">Lolos</span>`;
			} else if (status && status.isVerval === '2') {
				return `<span style="color: red;">Tidak Lolos</span>`;
			} else {
				if (akses) {
					return `<button type="button" class="btn btn-primary btn-sm" href="#" id="${jenis}">Verifikasi</button>`;
				} else {
					return '';
				}
			}
		}


		odata = generateDataTable('#table', "ijt/monitoring/data", ocolumn);

		$("#table").on('click', '.btn-detail', function() {
			const tr = $(this).closest('tr');
			const data = odata.row(tr).data();

			$('#update-apply_id').val(data.apply_id);

			$.ajax({
				url: `<?= base_url('ijt/monitoring/get_keterangan/'); ?>` + data.apply_id,
				method: 'GET',
				dataType: 'json',
				success: function(response) {
					$('#form_keterangan')[0].reset();

					$('#update-keterangan').val(response.keterangan);

					$("#modal-keterangan").modal("show");
				},
				error: function(xhr) {
					Swal.fire({
						title: "Error",
						text: "Gagal memuat data keterangan.",
						icon: "error"
					});
				}
			});
		});


		<?php if (@$kegiatan_id != '') { ?>

			jQuery.when(odata.search('<?= $kegiatan_name ?>').draw()).done(
				setTimeout(function() {
					search_attr();
				}, 1000)
			);

			function search_attr() {
				let i = 0;
				while ($('*[data-id="<?= $kegiatan_id ?>"]').length == 0 && i < 100) {
					console.log('searching attribute...');
					i++;
				}
				$('.detail[data-id="<?= $kegiatan_id ?>"]').get(0).click();
			}

		<?php } ?>
	});


	$("#table").on('click', '#hr, #interview, #kesimpulan', function() {
		var tr = $(this).closest('tr');
		var data = odata.row(tr).data();

		$('#form_verif')[0].reset();

		var jenisVerval = $(this).attr('id');
		$('#jenis_verval').val(jenisVerval);

		$.each(data, function(key, value) {
			$('#update-' + key).val(value);
		});

		if (jenisVerval === 'hr') {
			$('#label_modal_status').text('Seleksi HR');
		} else if (jenisVerval === 'interview') {
			$('#label_modal_status').text('Interview');
		} else if (jenisVerval === 'kesimpulan') {
			$('#label_modal_status').text('Kesimpulan');
		}

		$('#modal-verif').modal('show');
	});

	$('#form_verif').on('click', '.btn-simpan', function() {
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

		let status = $('input[name="status"]:checked').val();

		if (!status) {
			Swal.fire({
				icon: 'error',
				title: 'Oops...',
				text: 'Status tidak boleh kosong',
				confirmButtonText: 'OK'
			});
			return;
		}

		let is_verval = status ? 1 : 2;

		let actionText = status == 1 ? 'Data ini akan diterima' : 'Data ini akan ditolak';
		let confirmText = status == 1 ? 'Ya, Terima' : 'Ya, Tolak';
		let successMessage = status == 1 ? 'Data berhasil diterima.' : 'Data berhasil ditolak.';


		Swal.fire({
			title: 'Apakah Anda yakin?',
			text: actionText,
			icon: status == 1 ? 'question' : 'warning',
			showCancelButton: true,
			confirmButtonText: confirmText,
			cancelButtonText: 'Batal',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "<?= base_url('ijt/monitoring/action_verval'); ?>",
					type: 'POST',
					data: {
						apply_id: $('#update-apply_id').val(),
						jenis_verval: $('#jenis_verval').val(),
						is_verval: status,
						keterangan: keterangan
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Memproses....',
							text: 'Silahkan tunggu',
							allowOutsideClick: false,
							showConfirmButton: false,
							didOpen: () => {
								Swal.showLoading();
								$
							}
						});
					},
					success: function() {
						Swal.fire({
							icon: 'success',
							title: 'Berhasil!',
							text: successMessage,
							confirmButtonText: 'OK'
						}).then(() => {
							$('#modal-verif').modal('hide');
							$('#table').DataTable().ajax.reload();
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
	});

	$('#form_keterangan').on('click', '.btn-simpan', function() {
		let a = $('#update-apply_id').val();
		let keterangan = $('#update-keterangan').val();
		Swal.fire({
			title: 'Apakah Anda yakin?',
			text: 'Ingin simpan data ini?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonText: 'Ya, Simpan',
			cancelButtonText: 'Batal',
			reverseButtons: true
		}).then((result) => {
			if (result.isConfirmed) {
				$.ajax({
					url: "<?= base_url('ijt/monitoring/action_verval'); ?>",
					type: 'POST',
					data: {
						apply_id: $('#update-apply_id').val(),
						jenis_verval: $('#jenis_verval').val(),
						keterangan: keterangan
					},
					beforeSend: function() {
						Swal.fire({
							title: 'Memproses....',
							text: 'Silahkan tunggu',
							allowOutsideClick: false,
							showConfirmButton: false,
							didOpen: () => {
								Swal.showLoading();
								$
							}
						});
					},
					success: function() {
						Swal.fire({
							icon: 'success',
							title: 'Berhasil!',
							text: 'Data berhasil disimpan',
							confirmButtonText: 'OK'
						}).then(() => {
							$('#modal-keterangan').modal('hide');
							$('#table').DataTable().ajax.reload();
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
	});

	$(document).on('show.bs.modal', '#modal-detail', function() {
		setTimeout(function() {
			var form = $('#form-detail');
			var kry = form.data('kry');
			var agenda = form.data('agenda');
			var role = form.attr('role');

			if (role == 'pengguna' || role == 'admin') {
				$.ajax({
					url: "<?= base_url('sikesper/agenda/cekAgenda') ?>",
					type: "POST",
					dataType: "json",
					data: {
						kry: kry,
						agenda: agenda
					},
					success: function(data) {
						generateHtml(data.response);
					}
				});
			}

			map.invalidateSize();
		}, 1000);
	});

	$(document).on('hide.bs.modal', '#modal-detail', function() {
		$('#btn-action').html('');
	});
</script>
