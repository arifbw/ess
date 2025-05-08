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
		</div>

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
		if (@$akses["tambah"]) {
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
								<form role="form" action="<?php echo base_url(); ?>ijt/upload_poster/action_insert" id="formulir_tambah" method="post" enctype="multipart/form-data">
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Gambar</label>
												<span class="text-danger">*</span>
											</div>
											<div class="col-lg-7">
												<input type="file" name="gambar" accept="image/jpg, image/jpeg, image/png" required>
												<span class="text-danger">Maksimal 8 MB, ekstensi JPG/JPEG/PNG</span>
											</div>
										</div>
									</div>
                                    <div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Keterangan</label>
											</div>
											<div class="col-lg-7">
												<textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
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

		if (@$akses["lihat"]) { ?>
			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						<table width="100%" class="table table-striped table-bordered table-hover" id="table-data">
							<thead>
								<tr>
									<th class='text-center' style="width: 5%;">No</th>
									<th class='text-center'>Poster</th>
									<th class='text-center' style="width: 20%;">Keterangan</th>
									<th class='text-center no-sort' style="width: 20%;">Aksi</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		<?php
		}

        if(@$akses["ubah"]){ ?>
        <div class="modal fade" id="modal_data" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="label_modal_status">Ubah <?php echo $judul; ?></h4>
                    </div>
                    <form action="<?= site_url('ijt/upload_poster/action_update') ?>" id="formulir_update" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <input type="hidden" id="update-id" name="id">

                            <div class="form-group">
                                <label for="">Gambar</label>
                                <input type="file" name="gambar" accept="image/jpg, image/jpeg, image/png">
                                <span class="text-danger">Maksimal 8 MB, ekstensi JPG/JPEG/PNG</span>
                                <br>
                                <a href="#" target="__BLANK" class="btn btn-primary" id="lihat-gambar">Lihat Gambar</a>
                                <span class="text-danger" id="error-gambar"></span>
                            </div>

                            <div class="form-group">
                                <label for="">Keterangan</label>
                                <textarea name="keterangan" id="update-keterangan" class="form-control"></textarea>
                                <span class="text-danger" id="error-keterangan"></span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <input type="submit" name="submit" id="btn-submit" value="submit" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
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
    var table;
	$(document).ready(function() {
		table_serverside();
	});

	function table_serverside() {
        if(typeof table!='undefined') table.draw();
		else {
            table = $('#table-data').DataTable({
                "destroy": true,
                "iDisplayLength": 10,
                "language": {
                    "url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
                    "sEmptyTable": "Tidak ada data di database",
                },
                "processing": true,
                "serverSide": true,
                "ordering": false,
                "ajax": {
                    "url": "<?= base_url('ijt/upload_poster/table_data') ?>",
                    "type": "POST",
                },
                "columns": [{
                        "data": "no",
                        "orderable": false
                    },
                    {
                        "data": "img"
                    },
                    {
                        "data": "keterangan"
                    },
                    {
                        "data": "actions",
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "columnDefs": [{
                    "targets": 'no-sort',
                    "orderable": false,
                }]
            });
        }
	}

	$('#table-data').on('click', '.btn-update', function() {
		var tr = $(this).closest('tr');
		var data = table.row(tr).data();
		$.each(data, function(key, value) {
			$('#update-' + key).val(value);
		});

        $("#lihat-gambar").attr("href", data.full_path);
		$('#update-keterangan').val(data.keterangan).trigger('change');
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
				location.href = '<?php echo site_url('ijt/upload_poster/destroy/') ?>' + id;
			}
		});
	})
</script>
