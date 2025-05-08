<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header" id="page-header-title"></h1>
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
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
				<a href="<?= base_url('faskar/persetujuan/tv/header')?>" class="btn btn-default btn-md"><i class="fa fa-arrow-left"></i> Kembali</a>
			</div>
		</div>
		<br>
		
		<?php if($this->akses["lihat"]){ ?>
		<div class="row table-responsive">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_tv">
					<thead>
						<tr>
							<th class='text-center'>No</th>
							<th class='text-center'>NP Karyawan</th>	
							<th class='text-center'>Nama Karyawan</th>	
							<th class='text-center'>No. ID</th>
							<th class='text-center'>Pemakaian</th>
							<th class='text-center'>Plafon</th>
							<th class='text-center'>Beban Pegawai</th>
							<th class='text-center'>Beban Perusahaan</th>
							<th class='text-center'>Ket</th>
							<!-- <th class='text-center no-sort'>Aksi</th> -->
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<?php } ?>

	</div>
</div>

<script src="<?= base_url()?>asset/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script src="<?= base_url()?>asset/moment.js/2.29.1/moment.min.js"></script>
<script src="<?= base_url()?>asset/moment.js/2.29.1/locale/id.min.js"></script>
<script src="<?= base_url()?>asset/lodash.js/4.17.21/lodash.min.js"></script>
<script type="text/javascript">
	var header_pemakaian_bulan = '<?= $header->pemakaian_bulan?>';
	var header_lokasi = '<?= $header->lokasi?>';
	var header_layanan = '<?= $header->layanan?>';
	var table;
	$(document).ready(function() {
		$('#page-header-title').html(`Pemakaian TV Kabel ${header_layanan} ${header_lokasi} Bulan ${moment(header_pemakaian_bulan).format('MMMM YYYY')}`);
		Promise.all([
			
		]).then(() => {
			tableServerside();
		});
	});

	const tableServerside = async () => {
		let header_id = '<?= $header->id?>';
		table = $('#tabel_tv').DataTable({ 
			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
				"sEmptyTable": "Tidak ada data di database",
				"emptyTable": "Tidak ada data di database"
			},
			"destroy": true,
			"stateSave": true,
			"processing": true,
			"serverSide": true,
			"ordering": false,
			"ajax": {				
				"url"	: "<?php echo site_url("faskar/persetujuan/tv/detail/tabel_tv")?>",					 
				"type"	: "POST",
				"data"	: {header_id: header_id}
			},
			"columnDefs": [
				{ 
					"targets": 'no-sort',
					"orderable": false
				},
			],
			columns: [
				{
					render: function (data, type, row, meta) {
                 		return meta.row + meta.settings._iDisplayStart + 1;
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
					data: 'no_id',
					name: 'no_id',
				},
				{
					render: (data, type, row) => {
						return 'Rp ' + parseInt(row.pemakaian).toLocaleString();
					}
				},
				{
					render: (data, type, row) => {
						return 'Rp ' + parseInt(row.plafon).toLocaleString();
					}
				},
				{
					render: (data, type, row) => {
						return 'Rp ' + parseInt(row.beban_pegawai).toLocaleString();
					}
				},
				{
					render: (data, type, row) => {
						return 'Rp ' + parseInt(row.beban_perusahaan).toLocaleString();
					}
				},
				{
					data: 'keterangan',
					name: 'keterangan',
				},
			],
		});
	};
</script>

