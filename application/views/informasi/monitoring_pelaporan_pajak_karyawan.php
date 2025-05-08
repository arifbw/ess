<link rel="stylesheet" type="text/css" href="<?= base_url('asset/select2/select2.min.css') ?>" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/bootstrap-datepicker-master/dist/css/bootstrap-datepicker.min.css') ?>" />

<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul; ?></h1>
			</div>
		</div>

		<div class="row">
			<!-- filter -->
			<div class="col-lg-6">
				<div class="form-group">
					<label for="filter-tahun">Tahun Pajak</label>
					<input type="text" placeholder="Filter Tahun" id="filter-tahun" class="form-control datepicker-tahun">
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group">
					<label for="filter-unit">Unit</label>
					<select placeholder="Filter Unit" id="filter-unit" class="form-control" style="width: 100%;">
						<option value="00000">-- Semua Unit --</option>
						<?php foreach ($sto as $row) : ?>
							<option value="<?= $row->kode_unit ?>"><?= $row->nama_unit ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<canvas id="pie-lapor" style="width: 100%; height: 400px;"></canvas>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3 col-md-6 col-sm-12">
				<div class="form-group">
					<button id="btnExcel" class="btn btn-success">Export Excel</button>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<h4>Daftar Karyawan</h4>
				<table width="100%" class="table table-striped table-bordered table-hover" id="karyawan-table">
					<thead>
						<tr>
							<th class="text-center" style="width: 20%;">NP - Nama</th>
							<th class="text-center" style="width: 20%;">Unit Kerja</th>
							<th class="text-center" style="width: 10%;">Tahun Pajak</th>
							<th class="text-center" style="width: 10%;">Status SPT</th>
							<th class="text-center" style="width: 15%;">Nomor Tanda Terima Elektronik</th>
							<th class="text-center" style="width: 15%;">Status Lapor</th>
							<th class="text-center" style="width: 10%;">Lihat Bukti</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>

	</div>
</div>

<script src="<?= base_url() ?>asset/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= base_url('asset/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
<script src="<?= base_url('asset/bootstrap-datepicker-master/dist/js/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js') ?>"></script>
<script src="<?= base_url('asset/chartjs/4.4.1/chart.min.js') ?>"></script>
<script src="<?= base_url('asset/chartjs/4.4.1/chartjs-plugin-datalabels.min.js') ?>"></script>

<script>
	var bulan = <?= json_encode($bulan) ?>;
	var BASE_URL = '<?= base_url() ?>';
</script>
<script src="<?= base_url('asset/js/informasi/monitoring_pelaporan_pajak_karyawan.js?q=' . random_string()) ?>"></script>
