<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
		</div>
		
		<?php if($this->akses["lihat"]){ ?>
		<div class="row table-responsive">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_detail">
					<thead>
						<tr>
							<th class='text-center'>No</th>
							<th class='text-center'>Lokasi</th>	
							<th class='text-center'>Pemakaian Bulan</th>	
							<th class='text-center'>Pembayaran Bulan</th>
							<th class='text-center'>Pemakaian</th>
							<th class='text-center'>Plafon</th>
							<th class='text-center'>Beban Pegawai</th>
							<th class='text-center'>Beban Perusahaan</th>
							<th class='text-center'>Tgl Submit</th>
							<th class='text-center'>Status</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<?php } ?>

		<div class="modal fade" id="modal-ajukan" aria-labelledby="label_modal_ajukan" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content" id="modal-content-ajukan"></div>
			</div>
		</div>

	</div>
</div>

<script src="<?= base_url()?>asset/moment.js/2.29.1/moment.min.js"></script>
<script src="<?= base_url()?>asset/moment.js/2.29.1/locale/id.min.js"></script>
<script src="<?= base_url()?>asset/lodash.js/4.17.21/lodash.min.js"></script>
<script type="text/javascript">
	var table;
	$(document).ready(function() {
		Promise.all([
			
		]).then(() => {
			tableServerside();
		});
	});

	const tableServerside = async () => {
		table = $('#tabel_detail').DataTable({ 
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
				"url"	: "<?php echo site_url("faskar/info/pam/detail/tabel_detail")?>",					 
				"type"	: "POST",
				"data"	: {}
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
					data: 'lokasi',
					name: 'lokasi',
				},
				{
					render: (data, type, row) => {
						return moment(row.pemakaian_bulan).format('MMMM YYYY');
					}
				},
				{
					render: (data, type, row) => {
						return moment(row.pembayaran_bulan).format('MMMM YYYY');
					}
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
					render: (data, type, row) => {
						if( row.submit_date!==null )
							return moment(row.submit_date).format('DD MMMM YYYY, HH:mm') + ' WIB';
						else
							return '-';
					}
				},
				{
					render: (data, type, row) => {
						let status;
						if( row.submit_date===null ){
							status = $('<button/>', {
								html: 'Belum Submit',
								class: 'btn btn-xs btn-default'
							}).prop('outerHTML');
						} else{
							switch(row.approval_status){
								case '0':
									status = $('<button/>', {
										html: 'Menunggu Persetujuan Atasan',
										class: 'btn btn-xs btn-warning',
										onclick: `view_info(${JSON.stringify(row)})`
									}).prop('outerHTML');
									break;
								case '1':
									status = $('<button/>', {
										html: 'Disetujui Atasan',
										class: 'btn btn-xs btn-success',
										onclick: `view_info(${JSON.stringify(row)})`
									}).prop('outerHTML');
									break;
								case '2':
									status = $('<button/>', {
										html: 'Ditolak Atasan',
										class: 'btn btn-xs btn-danger',
										onclick: `view_info(${JSON.stringify(row)})`
									}).prop('outerHTML');
									break;
								case '3':
									status = $('<button/>', {
										html: 'Disetujui SDM',
										class: 'btn btn-xs btn-success',
										onclick: `view_info(${JSON.stringify(row)})`
									}).prop('outerHTML');
									break;
								case '4':
									status = $('<button/>', {
										html: 'Ditolak SDM',
										class: 'btn btn-xs btn-danger',
										onclick: `view_info(${JSON.stringify(row)})`
									}).prop('outerHTML');
									break;
								default:
									status = '-';
							}
						}
						return status;
					}
				}
			],
		});
	};

	const view_info = (data) =>{
		data['judul'] = 'Status Pemakaian Air PAM';
		$("#modal-content-ajukan").html('Loading...');
		$("#modal-ajukan").modal('show');
		$.ajax({
			type: "POST",
			url: `<?= base_url('faskar/info/pam/detail/view_info')?>`,
			data: data,
			dataType: 'html',
		}).then(function(response){
			$("#modal-content-ajukan").html(response);
		})
	}
</script>

