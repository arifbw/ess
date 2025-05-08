<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
		</div>

		<?php
			if( @$this->session->flashdata('success') ){
		?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('success');?>
				</div>
		<?php
			}
			if( @$this->session->flashdata('failed') ){
		?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('failed');?>
				</div>
		<?php
			} ?>
		
		<div class="row">
			<div class="col-lg-9 col-md-9 col-sm-12 text-left">
				<label>Filter Unit</label>
				<select class="form-control" id="filter-unit" style="width: 50%;" onchange="count_data()">
					<option value="all">Semua data</option>
				</select>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 text-right">
				<button class="btn btn-default btn-md" onclick="get_all_data()"><i class="fa fa-refresh"></i> Refresh</button>&nbsp;
				<?php
				
				if( @$akses["lihat log"] ){
					echo '<button class="btn btn-default btn-md" onclick="lihat_log()">Lihat Log</button>';
				}
				?>
			</div>
		</div>
		<br>
		
		<?php
			if($this->akses["lihat"]){
		?>

		<div class="row">
			<div class="col-lg-12">
				<i><label style="color: #337ab7;" id="text-note">Data updated at: </label></i>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-12">
				<div class="col-lg-4 col-md-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-users fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div style=' font-size: 200%;' id="count-karyawan">0</div>
									<div>Jumlah Karyawan</div>
								</div>
							</div>
						</div>
						<a href="javascript:;" onclick="showTable('all')">
							<div class="panel-footer">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>

				<div class="col-lg-4 col-md-4">
					<div class="panel panel-green">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-check fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div style=' font-size: 200%;' id="count-submit">0</div>
									<div>Sudah Submit</div>
								</div>
							</div>
						</div>
						<a href="javascript:;" onclick="showTable('1')">
							<div class="panel-footer">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>

				<div class="col-lg-4 col-md-4">
					<div class="panel panel-yellow">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-hourglass-end fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div style=' font-size: 200%;' id="count-penyesuaian">0</div>
									<div>Butuh Penyesuaian</div>
								</div>
							</div>
						</div>
						<a href="javascript:;" onclick="showTable('2')">
							<div class="panel-footer">
								<span class="pull-left">View Details</span>
								<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
								<div class="clearfix"></div>
							</div>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="row" id="div-table">
			<div class="col-lg-12">
				<div class="form-group">	
					<div class="row">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_vaksin">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>NP</th>	
									<th class='text-center'>Nama Karyawan</th>	
									<th class='text-center'>Tipe Keluarga</th>			
									<th class='text-center'>Nama Keluarga</th>
									<th class='text-center'>Tanggal Lahir</th>
									<th class='text-center'>Usia (per 2021)</th>
									<th class='text-center'>Status Vaksin</th>
								</tr>
							</thead>
						</table>
					</div>						
				</div>
			</div>
		</div>
		<?php
			}
		?>

	</div>
</div>

<script src="<?= base_url()?>asset/moment.js/2.29.1/moment.min.js"></script>
<script src="<?= base_url()?>asset/moment.js/2.29.1/locale/id.min.js"></script>
<script src="<?= base_url()?>asset/lodash.js/4.17.21/lodash.min.js"></script>
<script type="text/javascript">
	var all_data;
	var table;
	$(document).ready(function() {
		get_all_data();
		$('#filter-unit').select2();
	});

	const get_all_data = async () => {
		$('#text-note').html('Requesting data...');
		await $.ajax({
			type: "POST",
			url: `<?= base_url('vaksinasi/rekapitulasi_vaksin_keluarga/get_all_data')?>`,
			data: {},
			dataType: 'json',
		}).then(function(response){
			all_data = response;
			set_unit();
			$('#text-note').html(response.message);
		}).catch(function(xhr, status, error){
			console.log(xhr.responseText);
			$('#text-note').html(xhr.responseText);
		})
	}

	const set_unit = async () => {
		$('#filter-unit').html('');
		$('#filter-unit').append(new Option('Semua data', 'all'));
		let group_by_unit = await _.uniqBy(all_data.data, 'kode_unit');
		for (const item of group_by_unit) {
			if(item.kode_unit!==null) $('#filter-unit').append(new Option(item.nama_unit, item.kode_unit));
		}
		$('#filter-unit').select2();
		count_data();
		return true;
	}

	const count_data = async () => {
		let count_karyawan, count_submit, count_penyesuaian;
		let unit = $('#filter-unit').val();
		let data = all_data.data;
		if(unit==='all'){
			count_karyawan = await _.uniqBy(data, 'np_karyawan');
			count_submit = await _.filter(data, function(o) { return o.created_at !== null; });
			count_penyesuaian = await _.filter(data, function(o) { return o.created_at === null; });
		} else{
			let filter_by_unit = await _.filter(data, function(o) { return o.kode_unit === unit; });
			count_karyawan = await _.uniqBy(filter_by_unit, 'np_karyawan');
			count_submit = await _.filter(filter_by_unit, function(o) { return o.created_at !== null; });
			count_penyesuaian = await _.filter(filter_by_unit, function(o) { return o.created_at === null; });
		}
		$('#count-karyawan').html(count_karyawan.length);
		$('#count-submit').html(count_submit.length);
		$('#count-penyesuaian').html(count_penyesuaian.length);
		showTable('all');
	}

	const showTable = async (submitted) => {
		let unit = $('#filter-unit').val();
		let dataTable, dataFix;
		if(unit==='all'){
			dataTable = all_data.data;
		} else{
			dataTable = await _.filter(all_data.data, function(o) { return o.kode_unit === unit; });
		}

		switch(submitted) {
  			case '1':
				dataFix = await _.filter(dataTable, function(o) { return o.created_at !== null; });
				break;
  			case '2':
				dataFix = await _.filter(dataTable, function(o) { return o.created_at === null; });
				break;
  			default:
			  dataFix = dataTable;
		}


		let no = 1;
		table = $('#tabel_vaksin').DataTable({ 
			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
				"sEmptyTable": "Tidak ada data di database",
				"emptyTable": "Tidak ada data di database"
			},
			"destroy": true,
			"stateSave": true,
			"processing": true,
			"serverSide": false,
			"ordering": false,
			"data": dataFix,
			"columnDefs": [
				{ 
					"targets": 'no-sort',
					"orderable": false
				},
			],
			columns: [
				{
					render: () => {
						return no++;
					}
				},
				{
					data: 'np_karyawan',
					name: 'np_karyawan',
				},
				{
					data: 'nama_karyawan',
					name: 'nama_karyawan',
				},
				{
					data: 'tipe_keluarga',
					name: 'tipe_keluarga',
				},
				{
					data: 'nama_lengkap',
					name: 'nama_lengkap',
				},
				{
					render: (data, type, row) => {
						return moment(row.tanggal_lahir).format('DD MMMM YYYY');
					}
				},
				{
					render: (data, type, row) => {
						let text='';
						if(row.usia!==null){
							text += row.usia;
						} else{
							text += 2021 - parseInt(moment(row.tanggal_lahir).format('YYYY'));
						}
						return text;
					}
				},
				{
					render: (data, type, row) => {
						let label='';
						if( 2021 - parseInt(moment(row.tanggal_lahir).format('YYYY')) >= 12 ){
							if(row.dibatalkan_admin==='1'){
								const status = $('<span/>', {
									html: 'Dibatalkan Admin',
									class: 'label label-danger'
								})
								label += status.prop('outerHTML');
							} else{
								if(row.created_at!=null){
									if(row.status_vaksin==='1'){
										if(row.tanggal_vaksin_2!==null){
											const status = $('<span/>', {
												html: 'Sudah Vaksin 2',
												class: 'label label-success'
											})
											label += status.prop('outerHTML');
										} else{
											const status = $('<span/>', {
												html: 'Sudah Vaksin 1',
												class: 'label label-success'
											})
											label += status.prop('outerHTML');
										}
									} else{
										const status = $('<span/>', {
											html: 'Belum Vaksin',
											class: 'label label-warning'
										})
										label += status.prop('outerHTML');
									}
								} else{
									const status = $('<span/>', {
										html: 'Belum Input',
										class: 'label label-danger'
									})
									label += status.prop('outerHTML');
								}
							}
						} else{
							label += '-';
						}
						return label;
					}
				}
			],
		});
	};
</script>

