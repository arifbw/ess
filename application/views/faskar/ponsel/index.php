<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
		</div>

		<!-- <div class="alert alert-info">
			Info tulis di sini
		</div> -->

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
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="btn-tambah">Tambah Periode</a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse">
						<div class="panel-body">
							<form role="form" action="<?php echo base_url('faskar/ponsel/header/save_new_header'); ?>" id="formulir_tambah" method="post" enctype="multipart/form-data">
								<div class="row form-group">
									<div class="col-lg-3">
										<label>Bulan Pemakaian <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<select class="form-control" name="periode_pemakaian_bulan" id="periode_pemakaian_bulan" style="width: 100%" required>
										<?php
										foreach($array_daftar_bulan as $row){
											$selected = $row['id'] == date('m', strtotime("-1 months")) ? ' selected':'';
											echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['value'].'</option>';
										}
										?>
										</select>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Tahun Pemakaian <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="number" name="periode_pemakaian_tahun" id="periode_pemakaian_tahun" value="<?= date('Y')?>" required>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Bulan Pembayaran <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<select class="form-control" name="periode_pembayaran_bulan" id="periode_pembayaran_bulan" style="width: 100%" required>
										<?php
										foreach($array_daftar_bulan as $row){
											$selected = $row['id'] == date('m') ? ' selected':'';
											echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['value'].'</option>';
										}
										?>
										</select>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Tahun Pembayaran <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="number" name="periode_pembayaran_tahun" id="periode_pembayaran_tahun" value="<?= date('Y')?>" required>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-9 text-right">
										<button type="button" class="btn btn-default" id="btn-cancel-form">Cancel</button>
										<button type="submit" class="btn btn-primary" id="btn-submit-form">Simpan</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php
			if($this->akses["lihat"]){
		?>
		<div class="row table-responsive">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_header">
					<thead>
						<tr>
							<th class='text-center'>No</th>
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
		<?php
			}
		?>

		<div class="modal fade" id="modal-ajukan" aria-labelledby="label_modal_ajukan" aria-hidden="true" data-backdrop="static" data-keyboard="false">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content" id="modal-content-ajukan"></div>
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
				"url"	: "<?php echo site_url("faskar/ponsel/header/tabel_header")?>",					 
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
										class: 'btn btn-xs btn-warning'
									}).prop('outerHTML');
									break;
								case '1':
									status = $('<button/>', {
										html: 'Disetujui Atasan',
										class: 'btn btn-xs btn-success'
									}).prop('outerHTML');
									break;
								case '2':
									status = $('<button/>', {
										html: 'Ditolak Atasan',
										class: 'btn btn-xs btn-danger'
									}).prop('outerHTML');
									break;
								case '3':
									status = $('<button/>', {
										html: 'Disetujui SDM',
										class: 'btn btn-xs btn-success'
									}).prop('outerHTML');
									break;
								case '4':
									status = $('<button/>', {
										html: 'Ditolak SDM',
										class: 'btn btn-xs btn-danger'
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
						let action='';

						if( row.submit_date===null ){
							if( parseInt(row.jumlah_data) > 0 ){
								const ajukan = $('<button/>', {
									html: 'Ajukan',
									class: 'btn btn-primary btn-xs',
									onclick: `ajukan(${JSON.stringify(row)})`
								})
								action += ajukan.prop('outerHTML');
								action += ' ';
							}

							const edit = $('<button/>', {
								html: 'Edit',
								class: 'btn btn-warning btn-xs',
								onclick: `edit(${JSON.stringify(row)})`
							})
							action += edit.prop('outerHTML');
							action += ' ';
						}

						let urlDetail = '<?= base_url('faskar/ponsel/detail/data')?>';
						const detail = $('<a/>', {
							html: 'Detail',
							class: 'btn btn-default btn-xs detail_button',
							href: `${urlDetail}/${row.kode}`
						})
						action += detail.prop('outerHTML');
						return action;
					}
				}
			],
		});
	};

	$("#formulir_tambah").on('submit', function(e){
		$("#modal-loading").modal('show');
	});

	const ajukan = (data) => {
		$("#modal-content-ajukan").html('Loading...');
        $("#modal-ajukan").modal('show');
		$.ajax({
			type: "POST",
			url: `<?= base_url('faskar/ponsel/header/ajukan')?>`,
			data: data,
			dataType: 'html',
		}).then(function(response){
			$("#modal-content-ajukan").html(response);
		})
	}

	const edit = async (data) => {
		$('#btn-tambah').html('Edit periode');
		if($("#collapseOne").is(":visible")){
			// do nothing
		} else{
			$('#btn-tambah').trigger('click');
		}
		
		let pemakaian_bulan = data.pemakaian_bulan.split('-');
		let periode_pemakaian_bulan = pemakaian_bulan[1];
		$('#formulir_tambah').find(`[name=periode_pemakaian_bulan]`).val(`${periode_pemakaian_bulan}`);
		let periode_pemakaian_tahun = pemakaian_bulan[0];
		$('#formulir_tambah').find(`[name=periode_pemakaian_tahun]`).val(`${periode_pemakaian_tahun}`);

		let pembayaran_bulan = data.pembayaran_bulan.split('-');
		let periode_pembayaran_bulan = pembayaran_bulan[1];
		$('#formulir_tambah').find(`[name=periode_pembayaran_bulan]`).val(`${periode_pembayaran_bulan}`);
		let periode_pembayaran_tahun = pembayaran_bulan[0];
		$('#formulir_tambah').find(`[name=periode_pembayaran_tahun]`).val(`${periode_pembayaran_tahun}`);

		if($('#formulir_tambah').find('[name=kode]').length){
			$('#formulir_tambah').find(`[name=kode]`).val(`${data.kode}`);
		} else{
			$('<input>').attr({
				type: 'hidden',
				name: 'kode',
				value: `${data.kode}`
			}).appendTo('#formulir_tambah');
		}
		$("html, body").animate({ scrollTop: 0 }, "slow");
	}

	$("#btn-cancel-form").on('click', function(e){
		$('#btn-tambah').html('Tambah Periode');
		$('#btn-tambah').trigger('click');
		document.getElementById("formulir_tambah").reset();
		if($('#formulir_tambah').find('[name=kode]').length){
			$('#formulir_tambah').find(`[name=kode]`).remove();
		}
	});
</script>

