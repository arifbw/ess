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

				if ($this->akses["lihat"]) {
				?>
        			<!-- filter bulan -->
        			<div class="form-group">
        				<div class="row">
        					<div class="col-lg-3">
        						<div class="form-group">
        							<label for="date-range-filter">Filter Karyawan</label>
        							<select class="form-control select2" name="karyawan" id="karyawan" onchange="refresh_table_serverside();" style="width: 100%;">
        								<option value="">Semua Karyawan</option>
        								<?php
										foreach ($np_karyawan as $key) {
											$selected = ($key->no_pokok == $this->session->userdata('no_pokok') ? 'selected' : '');
											echo "<option value='{$key->no_pokok}' {$selected}>{$key->no_pokok} - {$key->nama}</option>";
										}
										?>
        							</select>
        						</div>
        					</div>
        					<div class="col-lg-3">
        						<div class="form-group">
        							<label for="date-range-filter">Filter Tipe Perjalanan</label>
        							<select class="form-control select2" name="tipe_perjalanan" id="tipe_perjalanan" onchange="refresh_table_serverside();" style="width: 100%;">
        								<option value="">Semua</option>
        								<option value="LN">LN (Luar Negeri)</option>
        								<option value="DN">DN (Dalam Negeri)</option>
        							</select>
        						</div>
        					</div>
        					<div class="col-lg-4">
        						<div class="form-group">
        							<label for="date-range-filter">Filter Rentang tanggal</label>
        							<input class="form-control" id="date-range-filter" name="dates" style="width: 100%;">
        						</div>
        					</div>

        					<div class="col-lg-8 pull-right">
        						<label>&nbsp;</label>
        						<button type="button" class="btn btn-success pull-right" id="btn-export-excel"><i class="fa fa-file-excel-o"></i> Export Excel</button>
        					</div>
        					<br>
        				</div>
        			</div>

        			<div class="form-group">
        				<div class="row">
        					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_monitoring_sppd">
        						<thead>
        							<tr>
        								<th class='text-center no-sort'>No</th>
        								<th class='text-center'>NP</th>
        								<th class='text-center'>Nama</th>
        								<th class='text-center'>Tipe Perjalanan</th>
        								<th class='text-center'>Tujuan</th>
        								<th class='text-center'>Tanggal Berangkat</th>
        								<th class='text-center'>Tanggal Pulang</th>
        								<th class='text-center'>Jenis Fasilitas</th>
        								<th class='text-center'>Jenis Transportasi</th>
        								<th class='text-center'>Biaya (Rupiah)</th>
        								<th class='text-center'>Biaya (US)</th>
        								<th class='text-center'>Detail</th>
        							</tr>
        						</thead>
        						<tbody>

        						</tbody>
        					</table>
        					<!-- /.table-responsive -->
        				</div>
        			</div>

        			<!-- Modal Detail -->
        			<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_detail" aria-hidden="true">
        				<div class="modal-dialog">
        					<div class="modal-content">

        						<div class="modal-header">
        							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        							<h4 class="modal-title" id="label_modal_detail">Detail <?php echo $judul; ?></h4>
        						</div>
        						<div class="modal-body">

        							<table>
        								<tr>
        									<td>Perihal</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="detail_perihal"></a></td>
        								</tr>
        								<tr>
        									<td>Tgl Selesai</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="detail_tgl_selesai"></a></td>
        								</tr>
        								<tr>
        									<td>No Surat</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="detail_no_surat"></a></td>
        								</tr>
        								<tr>
        									<td>Hotel</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="detail_hotel"></a></td>
        								</tr>
        								<tr>
        									<td>Nama Jabatan</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="detail_nama_jabatan"></a></td>
        								</tr>
        								<tr>
        									<td>Pangkat</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="detail_pangkat"></a></td>
        								</tr>
        								<tr>
        									<td>Unit</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="detail_unit"></a></td>
        								</tr>
        								<tr>
        									<td>Kode Unit</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="detail_kode_unit"></a></td>
        								</tr>
        							</table>


        						</div>
        					</div>
        					<!-- /.modal-content -->
        				</div>
        				<!-- /.modal-dialog -->
        			</div>
        			<!-- /.modal -->

        		<?php
				}

				?>
        	</div>
        	<!-- /.container-fluid -->
        </div>
        <!-- /#page-wrapper -->

        <script src="<?php echo base_url('asset/select2') ?>/select2.min.js"></script>
        <script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
        <script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>
        <script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>
        <script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js') ?>"></script>

        <script type="text/javascript">
        	$(function() {
        		$('#insert_date_awal').datetimepicker({
        			format: 'YYYY-MM',
        		}).on('dp.change', function(e) { // heru PDS tambahkan onchange, 2021-01-09
        			getUnitByPeriode()
        		});

        		$('#insert_date_akhir').datetimepicker({
        			format: 'YYYY-MM',
        		}).on('dp.change', function(e) { // heru PDS tambahkan onchange, 2021-01-09
        			getUnitByPeriode()
        		});
        	});

        	$(document).ready(function() {
        		$('#date-range-filter').daterangepicker({
        			locale: {
        				format: 'DD-MM-YYYY'
        			},
        			startDate: moment().startOf('month').format('DD-MM-YYYY'),
        			endDate: moment().endOf('month').format('DD-MM-YYYY'),
        			// endDate: moment().startOf('month').add(9, 'days').endOf('day').format('DD-MM-YYYY')
        		});

        		$('#date-range-filter').on('apply.daterangepicker', function(ev, picker) {
        			refresh_table_serverside()
        		});

        		$('.select2').select2();


        		$('#start_date').datetimepicker({
        			format: 'DD-MM-YYYY',
        			<?php if ($min) { ?>
        				minDate: '<?php echo $min; ?>'
        			<?php } ?>
        		});

        		$('#end_date').datetimepicker({
        			format: 'DD-MM-YYYY',
        			<?php if ($min) { ?>
        				minDate: '<?php echo $min; ?>'
        			<?php } ?>
        		});

        		$("#start_date").on("dp.change", function(e) {
        			validate_date();
        		});


        		$("#end_date").on("dp.change", function(e) {
        			validate_date();
        		});

        		$("#form_start_date").hide();
        		$("#form_end_date").hide();

        		$('#tabel_monitoring_sppd').DataTable().destroy();
        		table_serverside();
        	});

        	function refresh_table_serverside() {
        		$('#tabel_monitoring_sppd').DataTable().destroy();
        		table_serverside();
        	}

        	function table_serverside() {
        		var table;
        		var dateRange = $("#date-range-filter").val();
        		var dateArray = dateRange.split(' - ');
        		var startDate = moment($.trim(dateArray[0]), "DD-MM-YYYY").format("YYYY-MM-DD");
        		var endDate = moment($.trim(dateArray[1]), "DD-MM-YYYY").format("YYYY-MM-DD");
        		var np_karyawan = $("#karyawan").val();
        		var tipe_perjalanan = $("#tipe_perjalanan").val();

        		//datatables
        		table = $('#tabel_monitoring_sppd').DataTable({

        			"iDisplayLength": 10,
        			"language": {
        				"url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
        				"sEmptyTable": "Tidak ada data di database",
        				"emptyTable": "Tidak ada data di database"
        			},

        			"processing": true, //Feature control the processing indicator.
        			"serverSide": true, //Feature control DataTables' server-side processing mode.
        			"order": [], //Initial no order.

        			// Load data for the table's content from an Ajax source
        			"ajax": {
        				"url": "<?php echo site_url("perjalanan_dinas/monitoring_sppd/tabel_monitoring_sppd/") ?>",
        				"type": "POST",
        				"data": {
        					'start_date': startDate,
        					'end_date': endDate,
        					'np_karyawan': np_karyawan,
        					'tipe_perjalanan': tipe_perjalanan,
        				}
        			},

        			//Set column definition initialisation properties.
        			"columnDefs": [{
        				"targets": 'no-sort', //first column / numbering column
        				"orderable": false, //set not orderable
        			}, ],

        		});

        	};
        </script>

        <script>
        	$(document).on("click", '.detail_button', function(e) {
        		$("#detail_perihal").text($(this).data('perihal'));
        		$("#detail_tgl_selesai").text($(this).data('tgl_selesai'));
        		$("#detail_no_surat").text($(this).data('no_surat'));
        		$("#detail_hotel").text($(this).data('hotel'));
        		$("#detail_nama_jabatan").text($(this).data('nama_jabatan'));
        		$("#detail_pangkat").text($(this).data('pangkat'));
        		$("#detail_unit").text($(this).data('unit'));
        		$("#detail_kode_unit").text($(this).data('kode_unit'));
        	});
        </script>

        <script>
        	function getEndDate() {
        		var start_date = $('#start_date').val();
        		document.getElementById('end_date').setAttribute("min", start_date);
        	}
        </script>

        <script>
        	$('#btn-export-excel').on('click', () => {
        		let dates = $('#date-range-filter').val();
        		let start_date = dates.split(' - ')[0];
        		let end_date = dates.split(' - ')[1];
        		var np_karyawan = $("#karyawan").val();
        		var tipe_perjalanan = $("#tipe_perjalanan").val();
        		window.open(`<?= base_url() ?>perjalanan_dinas/monitoring_sppd/generateExcel?start_date=${start_date}&end_date=${end_date}&np_karyawan=${np_karyawan}&tipe_perjalanan=${tipe_perjalanan}`, '_blank');
        	})
        </script>
