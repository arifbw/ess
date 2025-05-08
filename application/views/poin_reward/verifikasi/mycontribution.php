<link href="<?= base_url('asset/select2/select2.min.css') ?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>asset/daterangepicker/daterangepicker.css" />
<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?= $judul ?></h1>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->

		<?php if (!empty($this->session->flashdata('success'))): ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?= $this->session->flashdata('success'); ?>
			</div>
		<?php endif; ?>
		<?php if (!empty($this->session->flashdata('warning'))): ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?= $this->session->flashdata('warning'); ?>
			</div>
		<?php endif; ?>

		<?php if ($this->akses["lihat"]): ?>
			<div class="row">
				<div class="col-lg-4">
					<div style="width:100%;" class="form-group">
						<label>Satuan kerja</label>
						<select class="form-control select2" id="satuan_kerja">
							<option value="all">Semua</option>
							<?php foreach ($ref_satuan_kerja as $item): ?>
								<option value="<?= $item['kode_unit']; ?>"><?= $item['nama_unit']; ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-lg-4">
					<div style="width:100%;" class="form-group">
						<label>Karyawan</label>
						<select class="form-control select2" id="karyawan" onchange="refresh_table_serverside()">
							<option value="all">Semua</option>
							<?php foreach ($ref_karyawan as $item): ?>
								<option value="<?= $item['no_pokok']; ?>"><?= $item['no_pokok']; ?> - <?= $item['nama']; ?></option>
							<?php endforeach ?>
						</select>
					</div>
				</div>
				<div class="col-lg-4">
					<div style="width:100%;" class="form-group">
						<label>Status</label>
						<select class="form-control select2" id="status" onchange="refresh_table_serverside()">
							<option value="all">Semua</option>
							<option value="0">Proses</option>
							<option value="1">Disetujui</option>
							<option value="2">Ditolak</option>
						</select>
					</div>
				</div>
				<!-- <div class="col-lg-3">
						<div class="form-group">
							<button onclick="export_excel()" class="btn btn-success" style="margin-top: 20px;">
								<i class="fa fa-print"></i> Export Excel
							</button>
						</div>
					</div> -->
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div style="width:100%;" class="form-group">
						<label>Tanggal</label>
						<input name="date_range" id="date_range" onchange="refresh_table_serverside()" class="form-control datepicker">
						<input type="hidden" id="start_date">
						<input type="hidden" id="end_date">
					</div>
				</div>
			</div>
			<div class="row table-responsive" style="padding-left: 15px;padding-right: 15px;">
				<table class="table table-striped table-bordered table-hover" id="daftar_mycontribution">
					<thead>
						<tr>
							<th class="text-center no-sort" style="max-width: 5%">No</th>
							<th class="text-center">Pegawai</th>
							<th class="text-center">Satuan Kerja</th>
							<th class="text-center no-sort">Perihal</th>
							<th class="text-center no-sort">Jenis Dokumen</th>
							<th class="text-center no-sort" style="max-width: 15%">Tanggal Dokumen</th>
							<th class="text-center no-sort">Status</th>
							<th class="text-center no-sort">Aksi</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
				<!-- /.table-responsive -->
			</div>
		<?php endif; ?>
	</div>

	<!-- Modal -->
	<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
		<div class="modal-dialog modal-2xl">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="label_modal_batal">Status <?= $judul ?></h4>
				</div>
				<div class="modal-body">
					<div class="get-approve"></div>
				</div>
			</div>
		</div>
	</div>
	<!-- /.modal -->

	<?php if (@$akses["persetujuan"] || @$akses["submit"]): ?>
		<!-- Modal -->
		<div class="modal fade" id="modal_persetujuan" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="label_modal_batal"><?= $judul ?></h4>
					</div>
					<div class="modal-body">
						<div class="set-approve"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- /.modal -->
	<?php endif; ?>
</div>
<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script src="<?= base_url('asset/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url() ?>asset/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript">
	function setMinDate(target, yype, value) {
		$(target).attr(type, value);
	}

	var all_atasan_1_np = [],
		all_atasan_1_jabatan = [],
		all_atasan_2_np = [],
		all_atasan_2_jabatan = [];
	$('#multi_select').select2({
		closeOnSelect: false
	});
	var startDate = moment().startOf('month');
	var endDate = moment().endOf('month');
	$(document).ready(function() {
		$('#date_range').daterangepicker({
			startDate,
			endDate,
			locale: {
				format: 'DD-MM-YYYY'
			}
		}, function(start, end) {
			$('#start_date').val(start.format('YYYY-MM-DD'));
			$('#end_date').val(end.format('YYYY-MM-DD'));
		})

		$('.datetimepicker5').datetimepicker({
			format: 'HH:mm'
		});
		$('.select2').select2();

		$('#satuan_kerja').on('change', function(e) {
			refresh_table_serverside()
			$.get(`<?= base_url('poin_reward/mycontribution/get_karyawan_by_satuan_kerja'); ?>/${$(this).val()}`, function(res) {
				const select = $('#karyawan');
				select.empty();
				select.append($('<option></option>')
					.val('all')
					.text(`Semua`))
				JSON.parse(res).map(function(item) {
					var $option = $('<option></option>')
						.val(item.no_pokok)
						.text(`${item.no_pokok} - ${item.nama}`);

					select.append($option);
				});
			})
		})
		$('#daftar_mycontribution').DataTable().destroy();
		table_serverside();
	});

	function refresh_table_serverside() {
		$('#daftar_mycontribution').DataTable().destroy();
		table_serverside();
	}

	function table_serverside() {
		var table;

		table = $('#daftar_mycontribution').DataTable({
			iDisplayLength: 10,
			language: {
				url: "<? base_url('asset/datatables/Indonesian.json'); ?>",
				sEmptyTable: "Tidak ada data di database",
				emptyTable: "Tidak ada data di database"
			},

			processing: true, //Feature control the processing indicator.
			serverSide: true, //Feature control DataTables' server-side processing mode.
			order: [], //Initial no order.

			// Load data for the table's content from an Ajax source
			ajax: {
				url: "<?= site_url("poin_reward/verifikasi/mycontribution/tabel_mycontribution") ?>",
				data: {
					status: $("#status").val(),
					satuan_kerja: $("#satuan_kerja").val(),
					start_date: $("#start_date").val(),
					end_date: $("#end_date").val(),
					karyawan: $("#karyawan").val()
				},
				type: "POST",
			},

			//Set column definition initialisation properties.
			columnDefs: [{
				targets: 'no-sort', //first column / numbering column
				orderable: false, //set not orderable
			}, ],
		});
	};

	function export_excel() {
		const filter = {
			status: $("#status").val()
		};

		$.ajax({
			url: '<?= base_url('poin_reward/verifikasi/export/mycontribution/excel') ?>',
			type: 'GET',
			dataType: 'JSON',
			data: filter,
			success: function(result) {
				var $a = $("<a>");
				$a.attr("href", result.file);
				$("body").append($a);
				$a.attr("download", result.name + '.xlsx');
				$a[0].click();
				$a.remove();
			}
		});
	}
</script>

<script>
	$(document).on('click', '.detail_button', function(e) {
		e.preventDefault();
		$("#modal_detail").modal('show');
		$.post('<?= site_url("poin_reward/verifikasi/mycontribution/view_detail") ?>', {
				id_: $(this).attr('data-id')
			},
			function(e) {
				$(".get-approve").html(e);
			}
		);
	});

	$(document).on('click', '.persetujuan_button', function(e) {
		e.preventDefault();
		$("#modal_persetujuan").modal('show');
		$.post('<?= site_url("poin_reward/verifikasi/mycontribution/view_approve") ?>', {
				id_: $(this).attr('data-id')
			},
			function(e) {
				$(".set-approve").html(e);
			}
		);
	});

	$('input[name="dates"]').daterangepicker({
		locale: {
			format: 'DD-MM-YYYY'
		},
	});
</script>