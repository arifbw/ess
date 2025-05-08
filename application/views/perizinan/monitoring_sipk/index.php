<link rel="stylesheet" type="text/css" href="<?= base_url('asset/select2/select2.min.css') ?>" />
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul; ?></h1>
			</div>
		</div>
		
        <?php if (@$this->session->flashdata('success')) { ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('success'); ?>
			</div>
		<?php } else if (@$this->session->flashdata('warning')) { ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $this->session->flashdata('warning'); ?>
			</div>
		<?php }
		if ($akses["lihat log"]) { ?>
			<div class='row text-right'>
				<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
				<br><br>
			</div>
		<?php } ?>
		<?php
		if ($this->akses["lihat"]) { ?>
			<div class="row">
				<!-- filter -->
				<div class="col-lg-6">
					<div class="form-group">
						<label for="filter-periode">Periode Bulan</label>
						<select placeholder="Filter Periode" id="filter-periode" class="form-control" style="width: 100%;">
							<?php foreach($filter_periode as $row){
							$prefix = "ess_perizinan_";
							if(strpos($row->TABLE_NAME, $prefix) === 0){ 
								$bulan = substr($row->TABLE_NAME, strlen($prefix));
								$monthsIndonesian = [
									1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
								];
								list($year, $month) = explode("_", $bulan);
							?>
							<option value="<?= $bulan ?>"><?= $monthsIndonesian[(int) $month] . " {$year}" ?></option>
							<?php } }?>
						</select>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="form-group">
						<label for="filter-unit">Unit</label>
						<select placeholder="Filter Unit" id="filter-unit" class="form-control" style="width: 100%;"></select>
					</div>
				</div>
			</div>
			
			<div class="row mt-2">
				<div class="col-lg-12">
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel-monitoring-sipk">
						<thead>
							<tr>
								<th class='text-center' style="width: 5%;">No</th>
								<th class='text-center' style="width: 25%;">NP - Nama</th>
								<th class='text-center' style="width: 30%;">Unit</th>
								<th class='text-center' style="width: 20%;">Planning</th>
								<th class='text-center' style="width: 20%;">Realisasi</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<script src="<?= base_url() ?>asset/jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js')?>"></script>
<script src="<?= base_url('asset/select2/select2.min.js') ?>"></script>
<script>
    var izin_table;
    $(()=>{
		$("#filter-periode").select2().trigger('change');
    });

	$("#filter-periode").on('change', (e)=>{
		let data = new FormData();
		data.append('bulan', e.target.value);
		$.ajax({
			url: '<?= base_url('perizinan/monitoring_sipk/get_filter_unit')?>',
			type: 'POST',
			data: data,
			dataType: 'json',
			processData: false,
			contentType: false,
			beforeSend: () => {
				$('#filter-unit').empty();
			},
		}).then((res) => {
			let el = $('#filter-unit');
			el.append(new Option('-- Semua Unit --', ''));
			for (const i of res.data) {
				el.append(new Option(i.nama_unit, i.kode_unit));
			}
			el.select2().trigger('change');
		});
	});

	$("#filter-unit").on('change', (e)=>{
		load_table();
	});

    const load_table = ()=>{
		if(typeof izin_table!='undefined') izin_table.draw();
		else {
			izin_table = $('#tabel-monitoring-sipk').DataTable({
				"iDisplayLength": 10,
				"language": {
					"url": "<?= base_url('asset/datatables/Indonesian.json'); ?>",
					"sEmptyTable": "Tidak ada data di database",
					"processing": "Sedang memuat",
					"emptyTable": "Tidak ada data di database"
				},
				"stateSave": true,
				"responsive": true,
				"processing": true,
				"serverSide": true,
				"ordering": false,
				"ajax": {
					"url": "<?= base_url("perizinan/monitoring_sipk/get_data/") ?>",
					"data": function(e){
						e.bulan = $("#filter-periode").val();
						e.unit = $("#filter-unit").val();
					},
					"type": "POST"
				},
				columns: [
					{
						data: 'no',
					}, {
						data: 'np_karyawan',
						render: (data, type, row)=>{
							return `${row.np_karyawan} - ${row.nama}`;
						}
					}, {
						data: 'nama_unit',
					}, {
						data: 'id',
						render: (data, type, row)=>{
							let text = [];
							text.push(`Start: ${row.start_date_input ?? ''}`);
							text.push(`End: ${row.end_date_input ?? ''}`);
							return text.join('<br>');
						}
					}, {
						data: 'id',
						render: (data, type, row)=>{
							let text = [];
							text.push(`Start: ${row.start_date ?? ''} ${row.start_time ?? ''}`);
							text.push(`End: ${row.end_date ?? ''} ${row.end_time ?? ''}`);
							return text.join('<br>');
						}
					}
				],
			});
		}
    }
</script>