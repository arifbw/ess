<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" />

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
		if ($akses["tambah"]) {
		?>
			<button type="button" class="btn btn-success pull-right" id="btn-tambah" style="margin-bottom: 20px;"><i class="fa fa-plus"></i> Tambah Data</button>

			<div class="modal fade" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="label-modal-form" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						</div>
						<form role="form" action="<?php echo base_url(); ?>lembur/plafon_lembur/store" id="formulir_tambah" method="post">
							<div class="modal-body">
								<div class="form-group">
									<label for="kode_unit">Divisi</label>
									<input type="hidden" id="id" name="id">
									<select class="form-control" id="kode_unit" name="kode_unit" style="width: 100%;" required>
										<option value="">Pilih</option>
										<?php foreach($divisi as $row):?>
											<option value="<?= $row->object_abbreviation?>"><?= "{$row->object_abbreviation} - {$row->object_name}"?></option>
										<?php endforeach?>
									</select>
								</div>
								<div class="form-group">
									<label for="nominal">Budget (Rp)</label>
									<input type="number" class="form-control" id="nominal" name="nominal" style="width: 100%;">
								</div>
								<div class="form-group">
									<label for="tahun">Tahun</label>
									<input type="number" class="form-control" id="tahun" name="tahun" style="width: 100%;" required>
								</div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-success">Simpan</button>
							</div>
						</form>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
		<?php
		}

		if ($this->akses["lihat"]) {
		?>
			<div class="col-sm-6" style="margin-bottom: 10px; margin-left: -30px;">
				<input type="text" placeholder="Filter Tahun" id="filter-tahun" class="form-control datepicker-tahun" style="width: 150px;" autocomplete="off">
			</div>
			<div class="form-group">
				<div class="row">
					<table width="100%" class="table table-striped table-bordered table-hover" id="table-data">
						<thead>
							<tr>
								<th class='text-center invisible'>id</th>
								<th class='text-center' style="width: 5%;">No</th>
								<th class='text-center' style="width: 40%;">Divisi</th>
								<th class='text-center' style="width: 40%;">Budget (Rp)</th>
								<th class='text-center no-sort' style="width: 15%;">Aksi</th>

							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>
		<?php
		}
		?>

	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script src="<?php echo base_url('asset/select2') ?>/select2.min.js"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js') ?>"></script>

<script type="text/javascript">
	$(document).ready(function() {
		$('#kode_unit').select2();

		$(".datepicker-tahun").datepicker({
			format: "yyyy",
			viewMode: "years",
			minViewMode: "years",
			autoclose: true
		}).datepicker("setDate", new Date());

		$('#filter-tahun').on('change', function() {
			let val = $(this).val();

			refresh_table_serverside(val);
		});


		$('#table-data').on('click', '#btn-edit', function() {
			var tr = $(this).closest('tr');
			let data = table.row(tr).data();

			let id = data[0];

			$.ajax({
				url: '<?= base_url('lembur/plafon_lembur/get_one/') ?>' + id,
				type: 'GET',
				dataType: 'json',
				processData: false,
				contentType: false,
				success: (res) => {
					$('#id').val(res.id);
					$('#kode_unit').val(res.kode_unit).trigger('change');
					$('#nominal').val(res.nominal);
					$('#tahun').val(res.tahun);
				}
			})

			document.getElementById("formulir_tambah").reset();


			$('#modal-form').modal('show');
		});

		$('#table-data').on('click', '#btn-delete', function() {
			var tr = $(this).closest('tr');
			let data = table.row(tr).data();

			let id = data[0];

			Swal.fire({
				title: "Apakah anda yakin ingin menghapus data?",
				showCancelButton: true,
				confirmButtonText: "Hapus",
			}).then((result) => {
				if (result.isConfirmed) {
					location.href = '<?= base_url('lembur/plafon_lembur/destroy/') ?>' + id;
				}
			});
		});

		$('#table-data').DataTable().destroy();
		table_serverside();
	});

	function refresh_table_serverside(tahun = null) {
		$('#table-data').DataTable().destroy();
		table_serverside(tahun);
	}

	var table;

	function table_serverside(tahun = null) {
		//datatables
		table = $('#table-data').DataTable({

			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
				"sEmptyTable": "Tidak ada data di database",
				"emptyTable": "Tidak ada data di database"
			},

			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.
			"stateSave": true,

			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "<?php echo site_url("lembur/plafon_lembur/data") ?>",
				"type": "POST",
				"data": {
					tahun
				}
			},

			//Set column definition initialisation properties.
			"columnDefs": [{
				"targets": 'no-sort', //first column / numbering column
				"orderable": false, //set not orderable
			}, {
				"targets": 'invisible', //first column / numbering column
				"orderable": false, //set not orderable
				"visible": false, //set invisible
			}, ],

		});

	};
</script>

<script>
	$('#btn-tambah').on('click', () => {
		document.getElementById("formulir_tambah").reset();
		$('#id').val('');

		$('#modal-form').modal('show');
		$('#kode_unit').val('').trigger('change');
	});

	$('#modal-form').on('show.bs.modal', (e)=>{
		$('#kode_unit').select2({
			dropdownParent: $('#modal-form')
		});
	});
</script>