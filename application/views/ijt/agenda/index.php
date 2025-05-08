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

		<?php if (@$this->akses["tambah"]): ?>
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
								<form role="form" action="" id="form" method="post">
									<input type="hidden" name="id" id="id">
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Job Tender</label>
											</div>
											<div class="col-lg-7">
												<div class="form-group">
													<select class="form-control select2" name="job_id" id="job_id" required>
														<option value="">Pilih job tender</option>
														<?php foreach ($ref_job_tender as $item): ?>
															<option value="<?= $item->id; ?>"><?= $item->nama_jabatan; ?></option>
														<?php endforeach; ?>
													</select>
												</div>
											</div>
											<div id="warning_job_id" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>NP Diundang</label>
											</div>
											<div class="col-lg-7">
												<div class="form-group">
													<select class="form-control select2" name="applyer[]" id="applyer" multiple placeholder="Pilih pelamar">
														<option value="" disabled>Pilih pelamar</option>
													</select>
												</div>
											</div>
											<div id="warning_applyer" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Kegiatan</label>
											</div>
											<div class="col-lg-7">
												<div class="form-group">
													<input type="text" placeholder="Kegiatan" id="kegiatan" name="kegiatan" class="form-control" required>
												</div>
											</div>
											<div id="warning_kegiatan" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Tanggal</label>
											</div>
											<div class="col-lg-7">
												<div class="form-group">
													<input required type="datetime-local" name="tanggal" id="tanggal" class="form-control">
												</div>
											</div>
											<div id="warning_tanggal" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Tempat</label>
											</div>
											<div class="col-lg-7">
												<div class="form-group">
													<textarea rows="3" id="tempat" class="form-control" name="tempat" placeholder="Tempat" required></textarea>
												</div>
											</div>
											<div id="warning_tempat" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row mt-3">
										<div class="col-lg-12 text-center mt-3">
											<button type="submit" class="btn btn-primary">Simpan</button>
											<button type="button" class="btn btn-danger" onclick="cancel()">Batal</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<!-- /.col-lg-12 -->
			</div>
		<?php endif; ?>

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
				<div class="col-lg-12">
					<table width="100%" class="table table-striped table-bordered table-hover" id="table">
						<thead>
							<tr>
								<th class='text-center' style="width:30px;">No</th>
								<th class='text-center' style="width:max-content;">Agenda</th>
								<?php if ($this->session->userdata('grup') == '5'): ?>
									<th class='text-center' style="width: 200px;;">Status Kehadiran</th>
								<?php endif; ?>
								<th class='text-center' style="width: 200px;white-space: nowrap;">Aksi</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
					<!-- /.table-responsive -->
				</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
				<div class="modal-dialog modal-lg" id="detail_content">
				</div>
			</div>
		<?php
		}
		?>

		<div class="modal fade" id="modal-detail" role="dialog" aria-labelledby="detail-title" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="" style="width:100%; display:flex; justify-content: space-between; align-content: center; padding: 1rem; border-bottom: 1px solid #dee2e6;
">
						<h3 class="modal-title" id="detail-title"><strong>Detail Data</strong></h3>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body detail-content">
					</div>
				</div>
			</div>
		</div>
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
	const base_url = "<?= base_url('/'); ?>"

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

	function cancel() {
		$('#form').trigger('reset');
		$('#id').val('');
		$('#collapseOne').collapse('hide');
		$('.select2').trigger('change');
		$('[id^="warning_"]').empty();
	}


	$(document).ready(function() {
		$('.select2').select2({
			width: '100%'
		});
		// var ocolumn = [{
		// 	data: 'kegiatan',
		// 	searchable: false,
		// 	className: 'text-center',
		// 	render: function(data, type, row, meta) {
		// 		return meta.row + meta.settings._iDisplayStart + 1;
		// 	}
		// }, {
		// 	data: 'kegiatan',
		// 	render: function(data, type, row, meta) {
		// 		let dateObj = new Date(row.tanggal);
		// 		let options = {
		// 			day: "numeric",
		// 			month: "long",
		// 			year: "numeric"
		// 		};
		// 		let tanggalFormatted = new Intl.DateTimeFormat("id-ID", options).format(dateObj);

		// 		return `<div class="d-flex flex-column gap-0">
		// 		  <p class="mb-0">Kegiatan <strong>${data}</strong></p>
		// 		  <p class="mb-0">${tanggalFormatted}, ${row.tanggal.split(' ')[1]}</p>
		// 		  <p class="mb-0">${row.tempat}</p>
		// 		</div>`;
		// 	}
		// }, {
		// 	data: 'id',
		// 	className: 'text-center',
		// 	render: function(data, type, row, meta) {
		// 		return `
		// 		<div class="btn-group">
		// 		    <div class="dropdown show">
		//                 <button class="btn btn-warning dropdown-toggle" id="dropdown-content" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		//                   Reschedule
		//                 </button>
		//                 <div class="dropdown-menu" aria-labelledby="dropdown-content">
		//                     <button class="dropdown-item btn btn-light btn-hadir" type="button">Hadir</button>
		//                     <button class="dropdown-item btn btn-light btn-tidak-hadir" type="button">Tidak Hadir</button>
		//                     <button class="dropdown-item btn btn-light btn-reschedule" type="button">Reschedule</button>
		//                 </div>
		//             </div>
		//             <button  class="btn btn-primary btn-detail" type="button">Detail</button>
		//             <button  class="btn btn-warning btn-edit" type="button">Edit</button>
		//             <button  class="btn btn-danger btn-delete" type="button">Hapus</button>
		// 		<div>`

		// 	}
		// }];

		var ocolumn = [

			{
				"data": 'no',
				"orderable": false,
				'class': 'text-center',
				"searchable": false
			},
			{
				"data": "kegiatan",
				render: function(data, type, row, meta) {
					let tanggalFormatted;
					if (row.tanggal) {
						let dateObj = new Date(row.tanggal);
						let options = {
							day: "numeric",
							month: "long",
							year: "numeric"
						};
						tanggalFormatted = new Intl.DateTimeFormat("id-ID", options).format(dateObj);
					}
					return `<div class="d-flex flex-column gap-0">
					<p class="mb-0">Kegiatan <strong>${data??'-'}</strong></p>
					<p class="mb-0">${tanggalFormatted??'-'}, ${row.tanggal?row.tanggal.split(' ')[1]:'-'}</p>
					<p class="mb-0">${row.tempat??'-'}</p>
					<p class="mb-0"><strong>${row.nama_jabatan??'-'}</strong></p>
				</div>`;
				}
			},
			<?php if ($this->session->userdata('grup') == '5'): ?> {
					"data": 'status',
					'class': 'text-center',
					render: function(data, type, row, meta) {
						return `<div class='' style='color:${row.color}'>${data}</div>`;
					}
				},
			<?php endif; ?> {
				"data": 'aksi',
				'class': 'text-center'
			},
		]
		odata =
			generateDataTable(
				'#table',
				"ijt/agenda/data",
				ocolumn
			);

		$('#form').on('submit', function(e) {
			e.preventDefault();
			let data = new FormData(this);
			$.ajax({
				url: base_url + '/ijt/agenda/save',
				type: 'post',
				data,
				contentType: false,
				processData: false,
				beforeSend: function() {
					Swal.fire({
						title: 'Loading...',
						text: 'Mohon tunggu sebentar',
						allowOutsideClick: false,
						showConfirmButton: false,
						showCancelButton: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
				},
				success: function(response) {
					Swal.close();
					const res = JSON.parse(response);

					if (res.status == 'success') {
						$('#form').trigger('reset');
						$('.select2').trigger('change');
						odata.ajax.reload();
						$('#collapseOne').collapse('hide');
						$('[id^="warning_"]').empty()

					} else {
						$.each(res.errors, function(key, value) {
							$("#warning_" + key).text(value);
						})
					}
				},
				error: function(xhr, status, error) {
					console.log({
						xhr,
						error
					});

					Swal.fire({
						title: "gagal!",
						text: "gagal mendambah agenda.",
						icon: "error",
					});
				}
			})
		})

		$("#table").on('click', '.btn-edit', function() {
			$.ajax({
				url: base_url + '/ijt/agenda/edit/' + $(this).data('id'),
				type: 'GET',
				dataType: 'html',
				beforeSend: function() {
					Swal.fire({
						title: 'Loading...',
						text: 'Mohon tunggu sebentar',
						allowOutsideClick: false,
						showConfirmButton: false,
						showCancelButton: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
				},
				success: function(res) {
					Swal.close();
					$("#modal-detail").modal("show");
					$("#modal-title").html("<strong>Edit Agenda</strong>");
					$(".detail-content").html(res);
				},
				error: function() {
					Swal.fire({
						title: 'Error!',
						text: 'Gagal mengambil data',
						icon: 'error'
					});
				}
			});

			// $("#collapseOne").collapse("show");
			// const tr = $(this).closest('tr');
			// const data = odata.row(tr).data();


			// $('#job_id').val(data.job_id).trigger('change');
			// $('#id').val(data.id);
			// setTimeout(function() {
			// 	if (data.applyer) {
			// 		$('#applyer').val(data.applyer ? data.applyer.split(',') : '').trigger('change');
			// 	}
			// }, 900)
			// $('#tempat').val(data.tempat);
			// $('#tanggal').val(data.tanggal.replace(' ', 'T').slice(0, 16));
			// $('#kegiatan').val(data.kegiatan);
		})

		$("#table").on('click', '.btn-delete', function() {
			const tr = $(this).closest('tr');
			const data = odata.row(tr).data();
			Swal.fire({
				title: "Anda akan menghapus agenda?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Ya, Hapus",
				cancelButtonText: "Batal"
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: base_url + '/ijt/agenda/delete/' + data.id,
						type: 'post',
						beforeSend: function() {
							Swal.fire({
								title: 'Loading...',
								text: 'Mohon tunggu sebentar',
								allowOutsideClick: false,
								showConfirmButton: false,
								showCancelButton: false,
								didOpen: () => {
									Swal.showLoading();
								}
							});
						},
						success: function(response) {
							Swal.close();
							const res = JSON.parse(response);
							Swal.fire({
								title: res.status == 'success' ? "Berhasil!" : 'Gagal!',
								text: res.message,
								icon: res.status == 'success' ? "success" : 'error',
							});
							odata.ajax.reload();
						},
						error: function(xhr, status, error) {
							Swal.fire({
								title: "gagal!",
								text: "Maaf, Ada yang salah",
								icon: "error",
							});
						}
					})
				}
			});
		})


		$("#table").on('click', '.btn-hadir', function() {
			const tr = $(this).closest('tr');
			const data = odata.row(tr).data();
			Swal.fire({
				title: "Anda akan hadir?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Ya, Hadir",
				cancelButtonText: "Batal"
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: base_url + '/ijt/agenda/update_kehadiran/' + data.id,
						type: 'post',
						data: {
							status: '1'
						},
						beforeSend: function() {
							Swal.fire({
								title: 'Loading...',
								text: 'Mohon tunggu sebentar',
								allowOutsideClick: false,
								showConfirmButton: false,
								showCancelButton: false,
								didOpen: () => {
									Swal.showLoading();
								}
							});
						},
						success: function(response) {
							Swal.close();
							const res = JSON.parse(response);
							Swal.fire({
								title: res.status == 'success' ? "Berhasil!" : 'Gagal!',
								text: res.message,
								icon: res.status == 'success' ? "success" : 'error',
							});
							odata.ajax.reload();
						},
						error: function(xhr, status, error) {
							Swal.fire({
								title: "gagal!",
								text: "Maaf, Ada yang salah",
								icon: "error",
							});
						}
					})
				}
			});
		})
		$('#job_id').on('change', function() {
			if ($(this).val()) {
				$.ajax({
					url: base_url + "ijt/agenda/find/" + $(this).val(),
					type: "GET",
					dataType: "json",
					success: function(response) {
						if (response.status === "success") {
							let data = response.data;

							$('#applyer').empty().append('<option value="" disabled>Pilih pelamar</option>');

							data.forEach(function(item) {
								$('#applyer').append(`<option value="${item.no_pokok}">${item.no_pokok} - ${item.nama}</option>`);
							});
						} else {
							$('#applyer').empty().append('<option value="" disabled>Pilih pelamar</option>');
						}
					},
					error: function(xhr, status, error) {
						console.error("Error:", error);
					}
				});
			}
		})

		$("#table").on('click', '.btn-tidak-hadir', function() {
			const tr = $(this).closest('tr');
			const data = odata.row(tr).data();
			Swal.fire({
				title: "Anda tidak akan hadir?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Ya, Tidak Hadir",
				cancelButtonText: "Batal"
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: base_url + '/ijt/agenda/update_kehadiran/' + data.id,
						type: 'post',
						data: {
							status: '2'
						},
						beforeSend: function() {
							Swal.fire({
								title: 'Loading...',
								text: 'Mohon tunggu sebentar',
								allowOutsideClick: false,
								showConfirmButton: false,
								showCancelButton: false,
								didOpen: () => {
									Swal.showLoading();
								}
							});
						},
						success: function(response) {
							Swal.close();
							const res = JSON.parse(response);
							Swal.fire({
								title: res.status == 'success' ? "Berhasil!" : 'Gagal!',
								text: res.message,
								icon: res.status == 'success' ? "success" : 'error',
							});
							odata.ajax.reload();
						},
						error: function(xhr, status, error) {
							Swal.fire({
								title: "gagal!",
								text: "Maaf, Ada yang salah",
								icon: "error",
							});
						}
					})
				}
			});
		})

		$("#table").on('click', '.btn-reschedule', function() {
			const tr = $(this).closest('tr');
			const data = odata.row(tr).data();

			Swal.fire({
				title: "Anda akan mereschedule jadwal hadir?",
				icon: "warning",
				html: '<textarea cols="5" name="alasan-reschedule" id="alasan-reschedule" class="form-control" placeholder="Alasan reschedule"></textarea>',
				showCancelButton: true,
				confirmButtonColor: "#3085d6",
				cancelButtonColor: "#d33",
				confirmButtonText: "Kirim",
				cancelButtonText: "Batal"
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: base_url + '/ijt/agenda/update_kehadiran/' + data.id,
						type: 'post',
						data: {
							status: '3',
							alasan_reschedule: $('#alasan-reschedule').val(),
						},
						beforeSend: function() {
							Swal.fire({
								title: 'Loading...',
								text: 'Mohon tunggu sebentar',
								allowOutsideClick: false,
								showConfirmButton: false,
								showCancelButton: false,
								didOpen: () => {
									Swal.showLoading();
								}
							});
						},
						success: function(response) {
							Swal.close();
							const res = JSON.parse(response);
							Swal.fire({
								title: res.status == 'success' ? "Berhasil!" : 'Gagal!',
								text: res.message,
								icon: res.status == 'success' ? "success" : 'error',
							});
							odata.ajax.reload();
						},
						error: function(xhr, status, error) {
							Swal.fire({
								title: "gagal!",
								text: "Maaf, Ada yang salah",
								icon: "error",
							});
						}
					})
				}
			});
		})

		$("#table").on('click', '.btn-detail', function() {
			const tr = $(this).closest('tr');
			const data = odata.row(tr).data();


			let dateObj = new Date(data.tanggal);
			let options = {
				day: "numeric",
				month: "long",
				year: "numeric"
			};
			let tanggalFormatted = new Intl.DateTimeFormat("id-ID", options).format(dateObj);

			$.ajax({
				url: base_url + '/ijt/agenda/detail/' + data.id,
				type: 'GET',
				dataType: 'html',
				beforeSend: function() {
					Swal.fire({
						title: 'Loading...',
						text: 'Mohon tunggu sebentar',
						allowOutsideClick: false,
						showConfirmButton: false,
						showCancelButton: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});
				},
				success: function(res) {
					Swal.close();
					$("#modal-detail").modal("show");
					$("#modal-title").html("<strong>Detail Agenda</strong>");
					$(".detail-content").html(res);
				},
				error: function() {
					Swal.fire({
						title: 'Error!',
						text: 'Gagal mengambil data',
						icon: 'error'
					});
				}
			});

			// $("#modal-detail").modal("show");
			// $("#modal-title").html("<strong>Keterangan</strong>");
			// $(".detail-content").html(`<div class="d-flex flex-column gap-0">
			//       <h4><strong>${data.nama_jabatan}</strong></h4>
			// 	  <p class="mb-0">Kegiatan <strong>${data.kegiatan}</strong></p>
			// 	  <div class="d-flex gap-3">
			// 	  <p class="mb-0">${tanggalFormatted}, ${data.tanggal.split(' ')[1]}</p><span class="badge badge-info">${data.status}</span>
			// 	  </div>
			// 	  <p class="mb-0">Lokasi : ${data.tempat}</p>
			// 	</div>`);
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
</script>