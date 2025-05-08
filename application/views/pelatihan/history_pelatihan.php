<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" />

<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">History Pelatihan</h1>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->
	
		<?php
		if ($akses["lihat log"]) {
			echo "<div class='row text-right'>";
			echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
			echo "<br><br>";
			echo "</div>";
		}

		if ($this->akses["lihat"]) {
		?>
			<!-- filter bulan -->
			<div class="form-group">
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group">
							<label>Bulan</label>
							<!--<select id="pilih_bulan_tanggal" class="form-control">-->
							<select class="form-control select2" id='bulan_tahun' name='bulan_tahun' onchange="refresh_table_serverside()" style="width: 100%;">
								<option value='0'>Semua</option>
								<?php
								foreach ($array_tahun_bulan as $value) {

									$tampil_bulan_tahun = '';
									if (!empty($this->session->flashdata('tampil_bulan_tahun'))) {
										$tampil_bulan_tahun = $this->session->flashdata('tampil_bulan_tahun');
									}
									if ($tampil_bulan_tahun == $value) {
										$selected = 'selected';
									} else {
										$selected = '';
									}
								?>
									<option value='<?php echo substr($value, 3, 4) . '-' . substr($value, 0, 2) ?>' <?php echo $selected; ?>><?php echo id_to_bulan(substr($value, 0, 2)) . " " . substr($value, 3, 4) ?></option>

								<?php
								}
								?>
							</select>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-lg-12">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_history_pelatihan">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>NP</th>
									<th class='text-center'>Nama</th>
									<th class='text-center'>Jabatan</th>
									<th class='text-center'>Pelatihan</th>
									<th class='text-center'>Tanggal Pelatihan</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
					<!-- /.table-responsive -->
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
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js') ?>"></script>

<script type="text/javascript">
	$(document).ready(function() {

		$('#date-range-export').daterangepicker({
			locale: {
				format: 'DD-MM-YYYY'
			},
			startDate: moment().subtract(1, 'months').startOf('month').format('DD-MM-YYYY'),
			endDate: moment().subtract(1, 'months').endOf('month').format('DD-MM-YYYY')
		});


		$('#date-range-export').on('apply.daterangepicker', function(ev, picker) {
			// Get the selected start date
			var startDate = picker.startDate;
			var endDate = picker.endDate;

			// Calculate the maximum end date based on the start date
			var maxEndDate = moment(startDate).add(9, 'days').endOf('day');

			// Set the new maximum end date
			if (endDate > maxEndDate) picker.setEndDate(maxEndDate);
		});

		$('.select2').select2();


		// $('#start_date').datetimepicker({
		// 	format: 'DD-MM-YYYY',
		// 	<?php if ($min) { ?>
		// 		minDate: '<?php echo $min; ?>'
		// 	<?php } ?>
		// });

		$('#start_date').datetimepicker({
			format: 'DD-MM-YYYY',
			useCurrent: false,
			icons: {
				previous: 'fa fa-chevron-left',
				next: 'fa fa-chevron-right',
				today: 'fa fa-calendar-check-o',
				clear: 'fa fa-trash',
				close: 'fa fa-times'
			}
		});

		// $('#start_date').on('click', function() {
		// 	$(this).datetimepicker('toggle');
		// });

		$('#end_date').datetimepicker({
			format: 'DD-MM-YYYY',
			<?php if ($min) { ?>
				minDate: '<?php echo $min; ?>'
			<?php } ?>
		});



		$("#start_date").on("dp.change", function(e) {
			// get_atasan_cuti_new();
			var absence_type = document.getElementById("absence_type").value;

			if (absence_type != '2001|1010') //jika bukan cuti besar
			{
				var oldDate = new Date(e.date);
				var newDate = new Date(e.date);
				var startDate = $('#start_date').val();
				newDate.setDate(oldDate.getDate());

				$('#end_date').data("DateTimePicker").minDate(startDate);
				$('#end_date').val(startDate);

				getJumlah();
			} else getJumlah();
			validate_date();
		});


		$("#end_date").on("dp.change", function(e) {
			getJumlah();
			validate_date();
		});


		$("#form_start_date").hide();
		$("#form_end_date").hide();
		$("#form_absence_type").hide();
		$("#form_alasan").hide();
		$("#form_keterangan").hide();

		$("#form_cuti_besar_pilih").hide();
		$("#form_type_cuber").hide();

		$("#form_jumlah_bulan").hide();
		$("#form_jumlah_hari").hide();

		$('#tabel_ess_history_pelatihan').DataTable().destroy();
		table_serverside();
	});

	function refresh_table_serverside() {
		$('#tabel_ess_history_pelatihan').DataTable().destroy();
		table_serverside();
	}

	function table_serverside() {
		var table;
		var bulan_tahun = $('#bulan_tahun').val();
		//datatables
		table = $('#tabel_ess_history_pelatihan').DataTable({

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
				"url": "<?php echo site_url("pelatihan/history_pelatihan/tabel_ess_history_pelatihan/") ?>" + bulan_tahun,
				"type": "POST"
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
	function getEndDate() {
		getJumlah();
		var start_date = $('#start_date').val();
		document.getElementById('end_date').setAttribute("min", start_date);
	}
</script>