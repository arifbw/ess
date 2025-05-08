<form role="form" action="" id="form" method="post">
	<input type="hidden" name="id" id="id" value="<?= $data->id; ?>">
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
							<option value="<?= $item->id; ?>" <?= $data->job_id == $item->id ? 'selected' : ''; ?>><?= $item->nama_jabatan; ?></option>
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
						<?php foreach ($pelamar as $item): ?>
							<option value="<?= $item->id; ?>" <?= in_array($data->applyer, $item->id) ? 'selected' : ''; ?>><?= $item->nama_jabatan; ?></option>
						<?php endforeach; ?>
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
					<input value="<?= $data->kegiatan; ?>" type="text" placeholder="Kegiatan" id="kegiatan" name="kegiatan" class="form-control" required>
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
					<input value="<?= $data->tanggal; ?>" required type="datetime-local" name="tanggal" id="tanggal" class="form-control">
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
					<textarea rows="3" id="tempat" class="form-control" name="tempat" placeholder="Tempat" required><?= $data->tempat; ?></textarea>
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

<script>
	function cancel() {
		$('#form').trigger('reset');
		$('[id^="warning_"]').empty();
		$('#modal-detail').modal('hide');
	}
	$(document).ready(function() {
		$('.select2').select2({
			width: '100%'
		});
		$(document).on('submit', '#form', function(e) {
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
						$('[id^="warning_"]').empty();
						$('#modal-detail').modal('hide');
						odata.ajax.reload();
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
			});
		})

		$(document).on('change', '#job_id', function() {
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
	});
</script>