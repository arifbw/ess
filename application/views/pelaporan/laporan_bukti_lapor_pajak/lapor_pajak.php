<link href="<?= base_url('asset/select2/select2.min.css') ?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') ?>" />

<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?= $judul ?></h1>
			</div>
		</div>

		<?php if (!empty($this->session->flashdata('success'))) : ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?= $this->session->flashdata('success'); ?>
			</div>
		<?php endif; ?>
		<?php if (!empty($this->session->flashdata('warning'))) : ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?= $this->session->flashdata('warning'); ?>
			</div>
		<?php endif; ?>
		<?php if (@$akses["tambah"]) : ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="btn-tambah">Unggah <?= $judul ?></a>
							</h4>
						</div>
						<div id="collapseOne" class="panel-collapse collapse">
							<div class="panel-body">
								<form role="form" action="<?= base_url(); ?>pelaporan/laporan_bukti_lapor_pajak/action_insert_lapor_pajak" id="formulir_tambah" method="post" enctype="multipart/form-data">
									<div class="form-group row">
										<input type='hidden' id='edit_id' name='edit_id'>
										<div class="col-lg-2">
											<label>NP Karyawan</label>
										</div>
										<div class="col-lg-7">
											<select class="form-control select2" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
												<option></option>
												<?php foreach ($array_daftar_karyawan->result_array() as $value) : ?>
													<option value='<?= $value['no_pokok'] ?>'><?= $value['no_pokok'] . " " . $value['nama'] ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-lg-2">
											<label>Status SPT</label>
										</div>
										<div class="col-lg-7">
											<select class="form-control select2" name="status_spt" id="status_spt" style="width: 100%" required>
												<option></option>
												<option value="Nihil">Nihil</option>
												<option value="Kurang Bayar">Kurang Bayar</option>
												<option value="Lebih Bayar">Lebih Bayar</option>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<div class="col-lg-2">
											<label>Tahun Pajak</label>
										</div>
										<div class="col-lg-7">
											<input class="form-control datepicker-tahun" name="tahun" id="tahun" placeholder="Masukkan Tahun Pajak" required>
										</div>
									</div>

									<hr>

									<div class="form-group row">
										<div class="col-lg-9">
											<label>LAMPIRAN</label>
										</div>
									</div>

									<div class="form-group row">
										<div class="col-lg-2">
											<label>Nomor Tanda Terima Elektronik</label>
										</div>
										<div class="col-lg-4">
											<input class="form-control" type="text" name="no_tanda_terima_elektronik" id="no_tanda_terima_elektronik" placeholder="Masukkan Nomor Tanda Terima Elektronik" required>
										</div>
										<div class="col-lg-3">
											<input class="form-control" type="file" name="surat_keterangan" id="surat_keterangan" accept="application/pdf,image/*" required><small class="form-text text-danger">Dokumen PDF/JPG/PNG/JPEG Max 2MB</small><!-- <strong> (wajib diisi)</strong> -->
										</div>
										<div class="col-lg-3" id="edit_surat_keterangan">
										</div>
									</div>

									<div class="row">
										<div class="col-lg-9 text-right">
											<button type="button" class="btn btn-primary" id="btn-simpan">Simpan</button>
											<button type="submit" class="btn btn-primary" id="btn-submit" style="display: none;">Submit</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ($this->akses["lihat"]) : ?>
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table width="100%" class="table table-striped table-bordered table-hover" id="daftar_lapor_pajak">
							<thead>
								<tr>
									<th class="text-center no-sort" style="max-width: 5%">No</th>
									<th class="text-center" style="width: 25%">Nama</th>
									<th class="text-center no-sort" style="width: 20%">Tahun Pajak</th>
									<th class="text-center no-sort" style="width: 20%">Status SPT</th>
									<th class="text-center no-sort" style="width: 20%">Nomor Tanda Terima Elektronik</th>
									<th class="text-center no-sort" style="width: 10%">Aksi</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_batal">Status <?= @$judul ?></h4>
						</div>
						<div class="modal-body">
							<div class="get-approve"></div>
						</div>
						<div class="modal-footer">
							<button type="button" data-dismiss="modal" class="btn btn-default">Tutup</button>
						</div>
					</div>
				</div>
			</div>
		<?php endif ?>

		<?php if (@$akses["hapus"]) : ?>
			<div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h4 class="modal-title text-danger" id="title-inactive">
								<b>Hapus <?= $judul ?></b>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</h4>
						</div>

						<div class="modal-body">
							<h4 id="message-inactive"></h4>
						</div>
						<div class="modal-footer">
							<a href="" id="inactive-action" class="btn btn-danger">Ya, Hapus</a>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<script src="<?= base_url() ?>asset/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= base_url('asset/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js') ?>"></script>
<script type="text/javascript">
	var all_atasan_1_np = [],
		all_atasan_1_jabatan = [],
		all_atasan_2_np = [],
		all_atasan_2_jabatan = [];
	var table;
	
	$('#multi_select').select2({
		closeOnSelect: false
	});
	$(document).ready(function() {
		$(".datepicker-tahun").datepicker({
			format: "yyyy",
			viewMode: "years",
			minViewMode: "years",
			autoclose: true
		});
		$(".datepicker-tahun").val(new Date().getFullYear());
		
		$('.select2').select2();
		$('#np_karyawan').select2({
			placeholder: "Nomor Pokok Karyawan"
		});
		$('#status_spt').select2({
			placeholder: "Status SPT"
		});

		table_serverside();
	});

	function table_serverside() {
		if(typeof table!='undefined') table.draw();
		else {
			table = $('#daftar_lapor_pajak').DataTable({
	
				"iDisplayLength": 10,
				"language": {
					"url": "<?= base_url('asset/datatables/Indonesian.json'); ?>",
					"sEmptyTable": "Tidak ada data di database",
					"emptyTable": "Tidak ada data di database"
				},
	
				"processing": true, //Feature control the processing indicator.
				"serverSide": true, //Feature control DataTables' server-side processing mode.
				"order": [], //Initial no order.
	
				// Load data for the table's content from an Ajax source
				"ajax": {
					"url": "<?= site_url("pelaporan/laporan_bukti_lapor_pajak/tabel_lapor_pajak/") ?>",
					"type": "POST",
				},
	
				//Set column definition initialisation properties.
				"columnDefs": [{
						"targets": 'no-sort', //first column / numbering column
						"orderable": false, //set not orderable
					},
					{
						"targets": [0],
						"className": "text-center"
					}
				],
	
			});
		}
	};
</script>

<script>
	const edit = async (data) => {
		$('#btn-tambah').html('Edit data');
		if ($("#collapseOne").is(":visible")) {
			console.log('Already shown');
		} else {
			$('#btn-tambah').trigger('click');
		}

		let fields = ['np_karyawan', 'no_tanda_terima_elektronik','keterangan'];
		for (const i of fields) {
			$(`#${i}`).val($(data).data(`${i}`));
		}

		$('#np_karyawan').trigger('change');
		$('#surat_keterangan').prop('required', false);
		$('#edit_surat_keterangan').html('<label class="text-danger"><b>Upload Ulang Jika Ingin Mengganti File</b></label>');

		if ($('#formulir_tambah').find('[name=edit_id]').length) {
			$('#formulir_tambah').find(`[name=edit_id]`).val($(data).data('id'));
		} else {
			$('<input>').attr({
				type: 'hidden',
				name: 'edit_id',
				value: $(data).data('id')
			}).appendTo('#formulir_tambah');
		}
		$("html, body").animate({
			scrollTop: 0
		}, "slow");
	}

	$(document).on('click', '.detail_button', function(e) {
		e.preventDefault();
		$("#modal_detail").modal('show');
		$.post('<?= site_url("pelaporan/laporan_bukti_lapor_pajak/view_detail") ?>', {
				id_: $(this).attr('data-id')
			},
			function(e) {
				$(".get-approve").html(e);
			}
		);
	});

	$('#no_tanda_terima_elektronik').on('keydown keyup', (e)=>{
		let input = $(e.target);
		let value = input.val().replace(/\D/g, '');
		// if (value.length > 21) {
		// 	value = value.slice(0, 21);
		// }
		input.val(value);
	});

	$('#btn-simpan').on('click', function(event) {
        event.preventDefault();

        if (validate_no_tanda_terima() === true) {
            // $('#formulir_tambah').submit();
            $('#btn-submit').trigger('click');
        } else {
            Swal.fire({
                title: '',
                text: 'Nomor Tanda Terima Elektronik 21 Digit',
                icon: 'error',
                allowOutsideClick: false,
                showCancelButton: false,
                confirmButtonText: 'OK'
            }).then(() => {

            });
        }
    });

	// document.addEventListener('DOMContentLoaded', function() {
	// 	document.getElementById('formulir_tambah').addEventListener('submit', function(event) {
	// 		event.preventDefault();

	// 		if (validate_no_tanda_terima()===true) {
	// 			$(this).submit();
	// 		} else {
	// 			Swal.fire({
	// 				title: '',
	// 				text: 'Nomor Tanda Terima Elektronik 21 Digit',
	// 				icon: 'error',
	// 				allowOutsideClick: false,
	// 				showCancelButton: false,
	// 				confirmButtonText: 'OK'
	// 			}).then(()=>{
					
	// 			});
	// 		}
	// 	});
	// });

	function validate_no_tanda_terima() {
		var input = document.getElementById('no_tanda_terima_elektronik').value;
		// if (input.trim().length !== 21) {
		// 	return false;
		// }
		return true;
	}

	<?php if(@$akses["hapus"]):?>
	$('#daftar_lapor_pajak').on('click', '.delete_button', function(e){
		Swal.fire({
			title: 'Hapus Laporan Pajak',
			icon: 'question',
			allowOutsideClick: false,
			reverseButtons: true,
			showCancelButton: true,
			confirmButtonText: 'Hapus',
			cancelButtonText: 'Tidak'
		}).then((result) => {
			if (result.isConfirmed) {
				let data = new FormData();
				data.append('id', e.target.dataset.id);
				$.ajax({
					url: '<?= base_url('pelaporan/laporan_bukti_lapor_pajak/hapus')?>',
					type: 'POST',
					data: data,
					dataType: 'json',
					processData: false,
					contentType: false,
					beforeSend: () => {
						$('#daftar_lapor_pajak').LoadingOverlay('show');
					},
				}).then((res) => {
					$('#daftar_lapor_pajak').LoadingOverlay('hide', true);
					table_serverside();
					if(res.status==true) {
						Swal.fire({
							title: '',
							text: res.msg,
							icon: 'success',
							allowOutsideClick: false,
							showCancelButton: false,
							confirmButtonText: 'OK'
						}).then(()=>{
							
						});
					} else {
						Swal.fire({
							title: '',
							text: res.msg,
							icon: 'error',
							allowOutsideClick: false,
							showCancelButton: false,
							confirmButtonText: 'OK'
						}).then(()=>{
							
						});
					}
				});
			}
		})
	});
	<?php endif?>
</script>
