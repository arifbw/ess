			<link href="<?= base_url('asset/select2/select2.min.css') ?>" rel="stylesheet" />
			<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
			<link href="<?= base_url('asset/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet">

			<form role="form" action="<?= base_url(); ?>poin_reward/verifikasi/mycontribution/save_approve/" id="form_verifikasi" method="post">
				<div class="row">
					<div class="col-md-12">
						<table>
							<tr>
								<th>Pegawai</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['np_karyawan'] . ' - ' . $detail['nama_karyawan'] ?></a></b></td>
							</tr>
							<tr>
								<th>Jenis Dokumen</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['jenis_dokumen'] ?></a></b></td>
							</tr>
							<tr>
								<th>Tanggal Dokumen</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['tanggal_dokumen'] ?></a></b></td>
							</tr>
							<tr>
								<th>Perihal</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a style="display: inline-block; max-width: 700px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= $detail['perihal'] ?></a></b></td>
							</tr>
							<tr>
								<th>Dibuat Tanggal</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['created_at'] ?></a></b></td>
							</tr>
							<?php if ($detail['asal'] == 'import'): ?>
								<tr>
									<th>Url Dokumen</th>
									<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
									<td><b><a href="<?= $detail['url'] ?>" target="_blank" style="display: inline-block; 
                      max-width: 700px; 
                      overflow: hidden; 
                      text-overflow: ellipsis; 
                      white-space: nowrap;"><?= $detail['url'] ?></a></b></td>
								</tr>
							<?php endif; ?>
						</table>
					</div>
				</div>
				<?php if ($detail['asal'] !== 'import'): ?>
					<?php if (pathinfo($detail['dokumen'], PATHINFO_EXTENSION) == 'pdf'): ?>
						<embed style="width: 100%;height: 400px;" src="<?= base_url('uploads/mycontribution/dokumen/') . $detail['dokumen']; ?>" type="application/pdf">
					<?php else: ?>
						<div class="image-container" style="max-height: 400px; overflow-y: scroll;">
							<img style="width: 100%;" src="<?= base_url('uploads/mycontribution/dokumen/') . $detail['dokumen']; ?>" class="img-fluid" alt="dokumen">
						</div>
					<?php endif ?>
				<?php endif; ?>

				<br>
				<div class="alert alert-<?= $approval_warna ?>">
					<?php if ($detail['status_verifikasi'] !== '0'): ?>
						<strong><a class="text-<?= $approval_warna ?>"><?= $detail['approval_np'] . ' | ' . $detail['approval_nama'] ?></a></strong><br>
						<p><?= $status_verifikasi ?></p>
					<?php else: ?>
						<p class="block">Tanggal Submit :</p>
						<p><?= $detail['tanggal_submit'] ?></p>
					<?php endif; ?>
					<?php if ($detail['status_verifikasi'] == '2') : ?>
						<p style="margin-top: 0">Alasan : <?= $detail['approval_alasan'] ?></p>
					<?php endif; ?>
				</div>

				<!-- <div class="form-group">
					<label>Alasan <small>(* Jika menolak</small></label>
					<textarea name="approval_alasan" id="alasan" class="form-control mb-2"></textarea>
				</div> -->
				<?php if ($detail['status_verifikasi'] == '0') { ?>
					<div class="row">
						<div class="col-lg-12">
							<input required type="radio" name="status_verifikasi" id="setuju" value="1">
							<label for="setuju">Setuju</label>
						</div>
						<div class="col-lg-12">
							<input required type="radio" name="status_verifikasi" id="tolak" value="2">
							<label for="tolak">Tolak</label>
						</div>
					</div>
					<div class="row" style="margin-bottom:8px">
						<div class="col-lg-12" id="feedback">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 text-right">
							<input type="hidden" name="id_" value="<?= $detail['id'] ?>">
							<!-- <input type="hidden" name="status_verifikasi" id="status_verifikasi"> -->
							<button type="submit" class="btn btn-block btn-primary">Simpan</button>
							<!-- <button type="submit" name="status_verifikasi" value="1" id='btn_setuju' class="btn btn-block btn-primary">Setujui</button>
							<button type="submit" name="status_verifikasi" value="2" id='btn_tolak' class="btn btn-block btn-danger">Tolak</button> -->
						</div>
					</div>
				<?php } ?>
			</form>

			<script>
				let isChecked;
				$(() => {
					$("#formulir_verifikasi").on('submit', function(e) {
						Swal.fire({
							title: "Loding...",
							showConfirmButton: false,
							allowOutsideClick: false,
							willOpen: () => {
								Swal.showLoading();
							}
						})
						$("#btn-cancel-form").prop('disabled', true);
						$("#btn-submit-form").prop('disabled', true);
					});
					// $('#form_verifikasi').on('submit', function(e) {
					// 	e.preventDefault();
					// 	const status_verifikasi = isChecked ? '1' : '2'
					// 	const data = new FormData(this);
					// 	data.append('status_verifikasi', status_verifikasi)

					// 	$.ajax({
					// 		url: $(this).attr('action'),
					// 		type: $(this).attr('method'),
					// 		data: data,
					// 		dataType: 'json',
					// 		processData: false,
					// 		contentType: false,
					// 		success: function(response, textStatus, jqXHR) {
					// 			const redirectUrl = "<?= site_url('poin_reward/verifikasi/mycontribution') ?>";
					// 			window.location.href = redirectUrl;
					// 		},
					// 	})

					// });

					$('#status').trigger('change');

					$(document).on('change', 'input[name="status_verifikasi"]', function() {
						val = $(this).val();

						const status_verifikasi = isChecked ? '1' : '2'

						const $label = $('<label>', {
							for: val == '1' ? 'poin' : 'alasan',
							text: val == '1' ? 'Poin' : 'Alasan'
						});

						const input = $('<input>', {
							name: 'poin',
							id: 'poin',
							type: 'number',
							class: 'form-control',
							required: true,
						});

						const textarea = $('<textarea>', {
							name: 'approval_alasan',
							id: 'alasan',
							class: 'form-control',
							required: true,
						});

						// $('#status_verifikasi').val(status_verifikasi)
						$('#feedback').empty().append($label, val == '1' ? input : textarea);
					});

				})
			</script>

			<script src="<?= base_url('asset') ?>/sweetalert2/sweetalert2.js"></script>
			<script type="text/javascript">
				function ubah_verif() {
					var set_approve = document.getElementById("set_approve");
					var set_erp = document.getElementById("set_erp");
					var submit_erp = document.getElementById("submit_erp");
					var ubah_verif = document.getElementById("ubah_verif");

					set_erp.style.display = "none";
					ubah_verif.style.display = "none";
					set_approve.style.display = "block";
					submit_erp.style.display = "block";
				}

				function submit_erp() {
					var set_approve = document.getElementById("set_approve");
					var set_erp = document.getElementById("set_erp");
					var submit_erp = document.getElementById("submit_erp");
					var ubah_verif = document.getElementById("ubah_verif");

					set_approve.style.display = "none";
					submit_erp.style.display = "none";
					set_erp.style.display = "block";
					ubah_verif.style.display = "block";
				}

				function form_alasan(obj) {
					var textarea = document.getElementById("form-alasan");
					var selectBox = obj;
					var selected = selectBox.options[selectBox.selectedIndex].value;

					if (selected === '4') {
						textarea.style.display = "block";
					} else {
						textarea.style.display = "none";
					}
				}

				function form_alasan_submit() {
					var textarea = document.getElementById("form-alasan-submit");
					var submit = document.getElementById("form-submit-submit");
					textarea.style.display = "block";
					submit.style.display = "block";
				}
				$(document).on('click', '#set_submit_erp', function(e) {
					e.preventDefault();
					Swal.fire({
						title: 'Anda yakin telah submit data ini di ERP?',
						icon: 'warning',
						showCancelButton: true,
						confirmButtonColor: '#3085d6',
						cancelButtonColor: '#d33',
						confirmButtonText: 'Ya, Submit',
						cancelButtonText: 'Batal'
					}).then((result) => {
						if (result.isConfirmed) {
							$.post('<?= site_url("poin_reward/verifikasi/mycontribution/dokumene_erp") ?>', {
									id_: '<?= $detail['id'] ?>'
								},
								function(get) {
									ret = JSON.parse(get);
									if (ret.status == true) {
										refresh_table_serverside();
										$("#modal_persetujuan").modal('hide');
										Swal.fire(
											ret.msg,
											'',
											'success'
										);
									} else {
										Swal.fire(
											ret.msg,
											'',
											'error'
										);
									}
								}
							);
						}
					})

				});
				$('.datetimepicker5').datetimepicker({
					format: 'YYYY-MM-DD HH:mm'
				});
				$('.select2').select2();
			</script>