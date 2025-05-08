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
		
		<?php if($this->akses["lihat"]){ ?>
		<div class="row table-responsive">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_header">
					<thead>
						<tr>
							<th class='text-center'>No</th>
							<th class='text-center'>Lokasi</th>	
							<th class='text-center'>Pemakaian Bulan</th>	
							<th class='text-center'>Pembayaran Bulan</th>
							<th class='text-center'>Jumlah Data</th>
							<th class='text-center'>Tgl Submit</th>
							<th class='text-center'>Status</th>
							<th class='text-center no-sort'>Aksi</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<?php } ?>

		<div class="modal fade" id="modal-approval" aria-labelledby="label_modal_approval" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content" id="modal-content-approval"></div>
			</div>
		</div>

		<!-- Modal loading -->
		<?php $this->load->view('faskar/modal_loading')?>

	</div>
</div>

<script src="<?= base_url()?>asset/jquery-validate/1.19.2/jquery.validate.min.js"></script>
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
		table = $('#tabel_header').DataTable({ 
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
				"url"	: "<?php echo site_url("faskar/persetujuan/pam/header/tabel_header")?>",					 
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
					data: 'jumlah_data',
					name: 'jumlah_data',
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
				},
				{
					render: (data, type, row) => {
						let label='';
						let url = '<?= base_url('faskar/persetujuan/pam/detail/data')?>';
						const detail = $('<a/>', {
							html: 'Detail',
							class: 'btn btn-default btn-xs detail_button',
							href: `${url}/${row.kode}`
						})
						label += detail.prop('outerHTML');

						if(row.approval_status==='0'){
							label += ' ';
							const approval = $('<button/>', {
								html: 'Persetujuan',
								class: 'btn btn-primary btn-xs',
								onclick: `approval(${JSON.stringify(row)})`
							})
							label += approval.prop('outerHTML');
						}

						return label;
					}
				}
			],
		});
	};

	const approval = (data) =>{
		$("#modal-content-approval").html('Loading...');
        $("#modal-approval").modal('show');
		$.ajax({
			type: "POST",
			url: `<?= base_url('faskar/persetujuan/pam/header/approval')?>`,
			data: data,
			dataType: 'html',
		}).then(function(response){
			$("#modal-content-approval").html(response);
		})
	}

	const view_info = (data) =>{
		data['judul'] = 'Status Pemakaian PAM';
		$("#modal-content-approval").html('Loading...');
		$("#modal-approval").modal('show');
		$.ajax({
			type: "POST",
			url: `<?= base_url('faskar/info_header/view_info')?>`,
			data: data,
			dataType: 'html',
		}).then(function(response){
			$("#modal-content-approval").html(response);
		})
	}
</script>

