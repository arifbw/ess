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

		<?php if( @$akses["tambah"] && $header->submit_date==null ){?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="btn-tambah">Tambah</a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse">
						<div class="panel-body">
							<form role="form" action="<?php echo base_url('faskar/pam/detail/action_insert'); ?>" id="formulir_tambah" method="post" enctype="multipart/form-data">
								<input type="hidden" name="faskar_pam_header_id" value="<?= $header->id?>">
								<input type="hidden" name="header_kode" value="<?= $header->kode?>">
								
								<div class="row form-group">
									<div class="col-lg-3">
										<label>NP <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="text" name="np_karyawan" id="np_karyawan" onchange="prosesNp()" required>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Nama <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="text" name="nama_karyawan" id="nama_karyawan" required>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Alamat <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="text" name="alamat" id="alamat" required>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>No. Rek <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="text" name="no_rek" id="no_rek" required>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Pemakaian (Rp) <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="number" name="pemakaian" id="pemakaian" required>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Plafon (Rp) <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="number" name="plafon" id="plafon" readonly required>
										<span id="keterangan-atcost"></span>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Beban Pegawai (Rp)</label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="number" name="beban_pegawai" id="beban_pegawai" readonly>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Beban Perusahaan (Rp)</label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="number" name="beban_perusahaan" id="beban_perusahaan" readonly>
									</div>														
								</div>

								<div class="row form-group">
									<div class="col-lg-3">
										<label>Keterangan</label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="text" name="keterangan" id="keterangan">
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
		<?php } ?>
		
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
				<a href="<?= base_url('faskar/pam/header')?>" class="btn btn-default btn-md"><i class="fa fa-arrow-left"></i> Kembali</a>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text-right">
				<?php
				if( @$akses["tambah"] && $header->submit_date==null ){
					echo '<button class="btn btn-success btn-md" data-toggle="modal" data-target="#modal-upload"><i class="fa fa-upload"></i> Import</button>&nbsp;';
				}?>
			</div>
		</div>
		<br>
		
		<?php if($this->akses["lihat"]){ ?>
		<div class="row table-responsive">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_pam">
					<thead>
						<tr>
							<th class='text-center'>No</th>
							<th class='text-center'>NP</th>	
							<th class='text-center'>Nama Karyawan</th>	
							<th class='text-center'>Alamat</th>			
							<th class='text-center'>No. Rek</th>
							<th class='text-center'>Pemakaian</th>
							<th class='text-center'>&nbsp;&nbsp;&nbsp;Plafon&nbsp;&nbsp;&nbsp;</th>
							<th class='text-center'>Beban Pegawai</th>
							<th class='text-center'>Beban Perusahaan</th>
							<th class='text-center'>Ket</th>
							<th class='text-center no-sort'>Aksi</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<?php } ?>

		<?php if(@$akses['tambah'] && $header->submit_date==null){?>
		<div class="modal fade" id="modal-upload" aria-labelledby="label_modal_upload" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		 	<div class="modal-dialog modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="upload-title"><strong>Upload Data</strong></h3>
		      		</div>
					<form role="form" action="<?php echo base_url('faskar/pam/detail/action_import'); ?>" id="formulir_upload" method="post" enctype="multipart/form-data">
		      		<div class="modal-body">
						<input type="hidden" name="faskar_pam_header_id" value="<?= $header->id?>">
						<input type="hidden" name="header_kode" value="<?= $header->kode?>">

						<div class="alert alert-info">
							Silakan upload file Excel sesuai format yang telah disediakan.<br>
							<a class="btn btn-success" href="<?= base_url('file/template/template-upload-faskar-pam.xlsx')?>" target="_blank"><b><i class="fa fa-file-excel-o"></i> Download contoh file</b></a>
						</div>
						<div class="row">
							<div class="form-group">
								<div class="col-lg-3">
									<label>Pilih File <span style="color: red">*</span></label>
								</div>
								<div class="col-lg-6">
									<input class="form-control" type="file" name="berkas" id="berkas" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
									<small class="form-text text-muted">Dokumen Excel (*.xlsx)</small>
								</div>														
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal" id="btn-cancel-upload">Cancel</button>
						<button type="submit" class="btn btn-primary" id="btn-submit-upload">Upload</button>
					</div>
					</form>
		    	</div>
		  	</div>
		</div>
		<?php } ?>

		<!-- Modal loading -->
		<?php $this->load->view('faskar/modal_loading')?>

	</div>
</div>

<script src="<?= base_url()?>asset/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script src="<?= base_url()?>asset/moment.js/2.29.1/moment.min.js"></script>
<script src="<?= base_url()?>asset/moment.js/2.29.1/locale/id.min.js"></script>
<script src="<?= base_url()?>asset/lodash.js/4.17.21/lodash.min.js"></script>
<script type="text/javascript">
	var header_pemakaian_bulan = '<?= $header->pemakaian_bulan?>';
	var header_lokasi = '<?= $header->lokasi?>';
	var table, allKaryawan, allPlafon;
	$(document).ready(function() {
		initAll();
	});

	const initAll = async() =>{
		await getAllPlafon();
		await getAllKaryawan();
		$('#page-header-title').html(`Pemakaian PAM ${header_lokasi} Bulan ${moment(header_pemakaian_bulan).format('MMMM YYYY')}`);
		$('#upload-title').html(`Import Data Pemakaian PAM ${header_lokasi} Bulan ${moment(header_pemakaian_bulan).format('MMMM YYYY')}`);
		hitung();
		tableServerside();
	}

	const tableServerside = async () => {
		let header_id = '<?= $header->id?>';
		table = $('#tabel_pam').DataTable({ 
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
				"url"	: "<?php echo site_url("faskar/pam/detail/tabel_pam")?>",					 
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
					data: 'alamat',
					name: 'alamat',
				},
				{
					data: 'no_rek',
					name: 'no_rek',
				},
				{
					render: (data, type, row) => {
						let pemakaian = row.pemakaian!==null ? parseInt(row.pemakaian):0;
						return 'Rp ' + pemakaian.toLocaleString();
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
				{
					render: (data, type, row) => {
						let label='';
						<?php if($header->submit_date==null){ ?>
						const edit = $('<button/>', {
							html: 'Edit',
							class: 'btn btn-warning btn-xs detail_button',
							onclick: `edit(${JSON.stringify(row)})`
						})
						label += edit.prop('outerHTML');
						const hapus = $('<button/>', {
							html: 'Hapus',
							class: 'btn btn-danger btn-xs detail_button',
							onclick: `hapus(${JSON.stringify(row)})`
						})
						label += hapus.prop('outerHTML');
						<?php } ?>
						return label;
					}
				}
			],
		});
	};

	const hitung = async () =>{
		let beban_pegawai, beban_perusahaan;
		let pemakaian = $('#pemakaian').val()!=='' ? parseInt($('#pemakaian').val()) : 0;
		let plafon = $('#plafon').val()!=='' ? parseInt($('#plafon').val()) : 0;
		if( $('#keterangan-atcost').text()=='at cost' ){
			beban_pegawai = 0;
			beban_perusahaan = pemakaian;
		} else{
			if( pemakaian >= plafon ){
				beban_perusahaan = plafon;
				beban_pegawai = pemakaian - plafon;
			} else{
				beban_pegawai = 0;
				beban_perusahaan = pemakaian;
			}
		}
		$('#beban_pegawai').val(beban_pegawai);
		$('#beban_perusahaan').val(beban_perusahaan);
	}

	$("#pemakaian, #plafon").on('keyup change', function(e){
		hitung();
	});

	$("#formulir_tambah").on('submit', function(e){
		$("#modal-loading").modal('show');
		$("#btn-cancel-form").prop('disabled',true);
		$("#btn-submit-form").prop('disabled',true);
	});

	$("#formulir_upload").on('submit', function(e){
		$("#modal-loading").modal('show');
		$("#btn-cancel-upload").prop('disabled',true);
		$("#btn-submit-upload").prop('disabled',true);
	});

	$("#btn-cancel-form").on('click', function(e){
		$('#keterangan-atcost').html('');
		$('#btn-tambah').html('Tambah');
		$('#btn-tambah').trigger('click');
		document.getElementById("formulir_tambah").reset();
		$('#formulir_tambah').find('[name=faskar_pam_header_id]').val('<?= $header->id?>');
		$('#formulir_tambah').find('[name=header_kode]').val('<?= $header->kode?>');
		if($('#formulir_tambah').find('[name=kode]').length){
			$('#formulir_tambah').find(`[name=kode]`).remove();
		}
		$('#np_karyawan').prop('readonly',false);
		hitung();
	});

	const hapus = async (data) =>{
		let result = confirm("Perhatian\nData yang telah dihapus tidak dapat dikembalikan.\nLanjutkan?");
		if (result) {
			$.ajax({
				type: "POST",
				url: `<?= base_url('faskar/pam/detail/hapus')?>`,
				data: {kode: data.kode},
				dataType: 'json',
			}).then(function(response){
				console.log(response.message);
				table.draw(false);
			}).catch(function(xhr, status, error){
				console.log(xhr.responseText);
				table.draw(false);
			})
		}
	}

	const edit = async (data) => {
		$('#keterangan-atcost').html('');
		$('#btn-tambah').html('Edit data');
		$('#np_karyawan').prop('readonly',true);
		if($("#collapseOne").is(":visible")){
			console.log('Already shown');
		} else{
			$('#btn-tambah').trigger('click');
		}
		
		let fields = ['np_karyawan', 'nama_karyawan', 'alamat', 'no_rek', 'pemakaian', 'plafon', 'keterangan'];
		for (const i of fields) {
			let val = data[i]!==null ? data[i]:'';
			$('#formulir_tambah').find(`[name=${i}]`).val(`${val}`);
		}

		let dataPlafon = await getPlafonByNp();
		if( dataPlafon!='undefined' ){
			if( dataPlafon.ket=='at cost' ) $('#keterangan-atcost').html('at cost');
			else $('#keterangan-atcost').html('');
		}
		hitung();

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

	const getPlafonByNp = async () =>{
		let np = $('#np_karyawan').val();
		let find = await _.find(allPlafon, (o)=>{ return o.np_karyawan==`${np}` });
		if( typeof find!='undefined' ){
			return find;
		} else{
			return 'undefined';
		}
	}

	const getKaryawanByNp = async()=>{
		let np = $('#np_karyawan').val();
		let find = await _.find(allKaryawan, (o)=>{ return o.no_pokok==`${np}` });
		if( typeof find!='undefined' ){
			return find;
		} else{
			return 'undefined';
		}
	}

	const prosesNp = async () =>{
		$('#keterangan-atcost').html('');
		let dataPlafon = await getPlafonByNp();
		let dataKaryawan = await getKaryawanByNp();

		if( dataPlafon!='undefined' ){
			$('#nama_karyawan').val(dataPlafon.nama_karyawan);
			$('#alamat').val(dataPlafon.alamat);
			$('#plafon').val(dataPlafon.plafon!=null ? dataPlafon.plafon:'');
			if( dataPlafon.ket=='at cost' ) $('#keterangan-atcost').html('at cost');
			else $('#keterangan-atcost').html('');
		} else if( dataKaryawan!='undefined' ){
			$('#nama_karyawan').val(dataKaryawan.nama);
			$('#alamat').val('');
			$('#plafon').val('0');
		} else{
			$('#nama_karyawan').val('');
			$('#alamat').val('');
			$('#plafon').val('0');
			alert('NP tidak ditemukan, silakan tambahkan data ke Master Plafon terlebih dahulu');
		}
		
		hitung();
	}

	// ambil data master
	const getAllKaryawan = async()=>{
		let data = await $.ajax({
			type: "POST",
			url: `<?= base_url('faskar/get_data/get_mst_karyawan')?>`,
			data: {},
			dataType: 'json',
		})
		console.log(data);
		return allKaryawan = data;
	}
	
	const getAllPlafon = async()=>{
		let data = await $.ajax({
			type: "POST",
			url: `<?= base_url('faskar/get_data/get_mst_plafon')?>`,
			data: { table_name: 'mst_plafon_pam' },
			dataType: 'json',
		})
		console.log(data);
		return allPlafon = data;
	}
	// END: ambil data master
</script>

