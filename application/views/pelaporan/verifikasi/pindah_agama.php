<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url()?>asset/daterangepicker/daterangepicker.css" />
<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?= $judul ?></h1>
			</div>
		</div>

		<?php if(!empty($this->session->flashdata('success'))): ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?= $this->session->flashdata('success');?>
			</div>
		<?php endif; ?>
		<?php if(!empty($this->session->flashdata('warning'))): ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?= $this->session->flashdata('warning');?>
			</div>
		<?php endif; ?>

		<?php if($this->akses["lihat"]): ?>		
			<div class="row">
				<div style="float: right;" class="col-lg-6 row">
					<div class="col-lg-3"></div>
					<div class="col-lg-6">
						<div style="width:100%;" class="form-group">
							<label>Status Pelaporan</label>
							<select class="form-control select2" id="status"  onchange="refresh_table_serverside()">
								<option value="all">Semua</option>
								<option value="0">Menunggu Persetujuan Atasan</option>
								<option value="1">Disetujui Atasan</option>
								<option value="2">Ditolak Atasan</option>
								<option value="3">Verifikasi KAUN SDM</option>
								<option value="4">Ditolak KAUN SDM</option>
								<option value="5">Submit ERP</option>
								<option value="6">Ditolak Admin SDM</option>
							</select>
						</div>
					</div>
					<div class="col-lg-3">
						<div class="form-group">
							<button onclick="export_excel()" class="btn btn-success" style="margin-top: 20px;">
								<i class="fa fa-print"></i> Export Excel
							</button>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">	
				<div class="row table-responsive">
					<table width="100%" class="table table-striped table-bordered table-hover" id="daftar_pindah_agama">
						<thead>
							<tr>
								<th class="text-center no-sort" style="max-width: 5%">No</th>
								<th class="text-center">Pegawai</th>
								<th class="text-center no-sort">Agama Lama</th>
								<th class="text-center no-sort">Agama Baru</th>
								<th class="text-center no-sort">Tanggal Perpindahan</th>
								<th class="text-center no-sort" style="max-width: 15%">Keterangan</th>
								<th class="text-center no-sort">Status</th>
								<th class="text-center no-sort">Aksi</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog modal-lg">
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
		<?php endif; ?>

		<?php if(@$akses["persetujuan"] || @$akses["submit"]): ?>
			<!-- Modal -->
			<div class="modal fade" id="modal_persetujuan" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
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

<script src="<?= base_url('asset/select2/select2.min.js')?>"></script>
<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
<script type="text/javascript" src="<?= base_url()?>asset/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript">
	var all_atasan_1_np=[], all_atasan_1_jabatan=[], all_atasan_2_np=[], all_atasan_2_jabatan=[];
	$('#multi_select').select2({
		closeOnSelect: false
	});
	$(document).ready(function() {
		$('.datetimepicker5').datetimepicker({
			format: 'HH:mm'
		});
		$('.select2').select2();

		$('#daftar_pindah_agama').DataTable().destroy();				
		table_serverside();
	});

	function refresh_table_serverside() {
		$('#daftar_pindah_agama').DataTable().destroy();				
		table_serverside();
	}

	function table_serverside() {
		var table;

		table = $('#daftar_pindah_agama').DataTable({
			iDisplayLength: 10,
			language: {
				url: "<? base_url('asset/datatables/Indonesian.json');?>",
				sEmptyTable: "Tidak ada data di database",
				emptyTable: "Tidak ada data di database"
			},

			processing: true, //Feature control the processing indicator.
			serverSide: true, //Feature control DataTables' server-side processing mode.
			order: [], //Initial no order.

			// Load data for the table's content from an Ajax source
			ajax: {
				url: "<?= site_url("pelaporan/verifikasi/pindah_agama/tabel_pindah_agama/")?>",
				data:{
					status: $("#status").val()
				},
				type: "POST",
			},

			//Set column definition initialisation properties.
			columnDefs: [
				{
					targets: 'no-sort', //first column / numbering column
					orderable: false, //set not orderable
				},
			],
		});
	};

	function export_excel(){
		const filter = {
			status: $("#status").val()
		};

		$.ajax({
			url: '<?= base_url('pelaporan/verifikasi/export/pindah_agama/excel') ?>',
			type: 'GET',
			dataType: 'JSON',
			data: filter,
			success: function(result) {
				var $a = $("<a>");
				$a.attr("href",result.file);
				$("body").append($a);
				$a.attr("download",result.name+'.xlsx');
				$a[0].click();
				$a.remove();
			}
		});
	}
</script>

<script>
	$(document).on( "click", '.status_button',function(e) {
		var status_np_karyawan = $(this).data('np-karyawan');
		var status_nama = $(this).data('nama');
		var status_created_at = $(this).data('created-at');
		var status_start = $(this).data('start-date');
		var status_end = $(this).data('end-date');
		var status_approval_1_nama = $(this).data('approval-1-nama');
		var status_approval_1_status = $(this).data('approval-1-status');
		var status_approval_1_alasan = $(this).data('approval-1-alasan');
		var status_approval_2_nama = $(this).data('approval-2-nama');
		var status_approval_2_status = $(this).data('approval-2-status');
		var status_approval_2_alasan = $(this).data('approval-2-alasan');
		var status_pamlek = $(this).data('pamlek');
		var batal_waktu = $(this).data('batal-waktu');
		var batal_alasan = $(this).data('batal-alasan');
		var batal_np = $(this).data('batal-np');

		$('#approver_2').hide();
		$('#batal').hide();
		if (status_pamlek != 'G') {
			$('#approver_2').show();
		}
		if (batal_np != '' && batal_np != null) {
			$('#batal').show();
		}

		$('#approver_1').removeClass('alert-info');
		$('#approver_1').removeClass('alert-danger');
		$('#approver_2').removeClass('alert-info');
		$('#approver_2').removeClass('alert-danger');
		$('#status_approval_1_nama').removeClass('text-primary');
		$('#status_approval_1_nama').removeClass('text-danger');
		$('#status_approval_2_nama').removeClass('text-primary');
		$('#status_approval_2_nama').removeClass('text-danger');

		$('#status_approval_1_alasan').css('display', 'none');
		if (status_approval_1_status.includes("TIDAK")==true) {
			$('#approver_1').addClass('alert-danger');
			$('#status_approval_1_nama').addClass('text-danger');
			$('#status_approval_1_alasan').css('display', '');
			$("#status_approval_1_alasan").text('Alasan : '+status_approval_1_alasan);
		} else {
			$('#approver_1').addClass('alert-info');
			$('#status_approval_1_nama').addClass('text-primary');
		}

		$('#status_approval_2_alasan').css('display', 'none');
		if (status_approval_2_status.includes("TIDAK")==true) {
			$('#approver_2').addClass('alert-danger');
			$('#status_approval_2_nama').addClass('text-danger');
			$('#status_approval_2_alasan').css('display', '');
			$("#status_approval_2_alasan").text('Alasan : '+status_approval_2_alasan);
		} else {
			$('#approver_2').addClass('alert-info');
			$('#status_approval_2_nama').addClass('text-primary');
		}

		$("#status_np_karyawan").text(status_np_karyawan);					
		$("#status_nama").text(status_nama);
		$("#status_created_at").text(status_created_at);	
		$("#status_start").text(status_start);
		$("#status_end").text(status_end);
		$("#status_approval_1_nama").text(status_approval_1_nama);				
		$("#status_approval_1_status").text(status_approval_1_status);
		$("#status_approval_2_nama").text(status_approval_2_nama);
		$("#status_approval_2_status").text(status_approval_2_status);
		$("#status_batal_np").text(batal_np);
		$("#status_batal_alasan").text(batal_alasan);
		$("#status_batal_waktu").text(batal_waktu);
	});
</script>

<script>
	$(document).on('click','.detail_button',function(e){
		e.preventDefault();
		$("#modal_detail").modal('show');
		$.post('<?= site_url("pelaporan/verifikasi/pindah_agama/view_detail") ?>',
			{id_:$(this).attr('data-id')},
			function(e){
				$(".get-approve").html(e);
			}
		);
	});

	$(document).on('click','.persetujuan_button',function(e){
		e.preventDefault();
		$("#modal_persetujuan").modal('show');
		$.post('<?= site_url("pelaporan/verifikasi/pindah_agama/view_approve") ?>',
			{id_:$(this).attr('data-id')},
			function(e){
				$(".set-approve").html(e);
			}
		);
	});

	$('input[name="dates"]').daterangepicker({
		locale: {
			format: 'DD-MM-YYYY'
		}
	});
</script>