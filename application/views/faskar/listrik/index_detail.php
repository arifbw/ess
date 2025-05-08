<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
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
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah</a>
					</h4>
				</div>
				<div id="collapseOne" class="panel-collapse collapse">
					<div class="panel-body">
						<form role="form" action="<?php echo base_url('faskar/listrik/action_insert'); ?>" id="formulir_tambah" method="post" enctype="multipart/form-data">
							<!-- <div class="row">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Karyawan <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<select class="form-control select2" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
											<?php foreach ($array_daftar_karyawan->result_array() as $value) { ?>
												<option value='<?php echo $value['no_pokok']?>'><?php echo $value['no_pokok']." ".$value['nama']?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div> -->

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>NP <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="np_karyawan" id="np_karyawan" required>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Nama <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="nama_karyawan" id="nama_karyawan" required>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Alamat <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="alamat" id="alamat" required>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>No. Kontrol <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="no_kontrol" id="no_kontrol" required>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Lokasi <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<select class="form-control" name="lokasi" id="lokasi" style="width: 100%" required>
											<option value="JAKARTA">JAKARTA</option>
											<option value="KARAWANG">KARAWANG</option>
										</select>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Bulan <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<select class="form-control" name="periode_bulan" id="periode_bulan" style="width: 100%" required>
										<?php
										foreach($array_daftar_bulan as $row){
											$selected = $row['id'] == date('m') ? ' selected':'';
											echo '<option value="'.$row['id'].'" '.$selected.'>'.$row['value'].'</option>';
										}
										?>
										</select>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Tahun <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="number" name="periode_tahun" id="periode_tahun" value="<?= date('Y')?>" required>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Tagihan (Rp) <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="number" name="tagihan" id="tagihan" required>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Biaya Admin (Rp) <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="number" name="biaya_admin" id="biaya_admin" required>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Pemakaian (Rp)</label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="number" name="pemakaian" id="pemakaian" readonly>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Plafon (Rp) <span style="color: red">*</span></label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="number" name="plafon" id="plafon" required>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Beban Pegawai (Rp)</label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="number" name="beban_pegawai" id="beban_pegawai" readonly>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Beban Perusahaan (Rp)</label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="number" name="beban_perusahaan" id="beban_perusahaan" readonly>
									</div>														
								</div>
							</div>

							<div class="row" id="">
								<div class="form-group">
									<div class="col-lg-2">
										<label>Keterangan</label>
									</div>
									<div class="col-lg-7">
										<input class="form-control" type="text" name="keterangan" id="keterangan">
									</div>														
								</div>
							</div>

							<div class="row">
								<div class="col-lg-9 text-right">
									<button type="submit" class="btn btn-primary">Simpan</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-12 text-left">
				<label>Lokasi</label>
				<select class="form-control" id="filter-lokasi" style="width: 100%;" onchange="tableServerside()">
					<option value="all">Semua data</option>
					<option value="JAKARTA">JAKARTA</option>
					<option value="KARAWANG">KARAWANG</option>
				</select>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 text-left">
				<label>Bulan</label>
				<select class="form-control" id="filter-bulan" style="width: 100%;" onchange="tableServerside()">
					<option value="all">Semua data</option>
				</select>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-12 text-right">
				<?php
				if( @$akses["tambah"] ){
					echo '<button class="btn btn-success btn-md"><i class="fa fa-upload"></i> Import</button>&nbsp;';
				}
				?>
			</div>
		</div>
		<br>
		
		<?php
			if($this->akses["lihat"]){
		?>
		<div class="row table-responsive">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_listrik">
					<thead>
						<tr>
							<th class='text-center'>No</th>
							<th class='text-center'>NP</th>	
							<th class='text-center'>Nama Karyawan</th>	
							<th class='text-center'>Alamat</th>			
							<th class='text-center'>No. Kontrol</th>
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
		<?php
			}
		?>

		<?php if(@$akses['ubah']){?>
		<div class="modal fade" id="modal-ubah" aria-labelledby="label_modal_ubah" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		 	<div class="modal-dialog modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="ubah-title"><strong>Insert Data</strong></h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">×</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
					  	<?php $this->load->view('vaksinasi/data_vaksin_keluarga/input')?>
		      		</div>
		    	</div>
		  	</div>
		</div>
		<?php } ?>

	</div>
</div>

<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
<script src="<?= base_url('asset/jquery-validate/1.19.2/jquery.validate.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script src="<?= base_url('asset/moment.js/2.29.1/locale/id.min.js')?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js')?>"></script>
<script type="text/javascript">
	var table;
	var listBulan;
	$(document).ready(function() {
		$('.select2').select2();
		Promise.all([
			getBulan(),
		]).then(() => {
			hitung();
			tableServerside();
		});
	});

	const tableServerside = async () => {
		let lokasi = $('#filter-lokasi').val();
		let bulan = $('#filter-bulan').val();
		table = $('#tabel_listrik').DataTable({ 
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
				"url"	: "<?php echo site_url("faskar/listrik/tabel_listrik")?>",					 
				"type"	: "POST",
				"data"	: {lokasi: lokasi, bulan: bulan}
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
					data: 'no_kontrol',
					name: 'no_kontrol',
				},
				{
					render: (data, type, row) => {
						let tagihan = row.tagihan!==null ? parseInt(row.tagihan):0;
						let admin = row.biaya_admin!==null ? parseInt(row.biaya_admin):0;
						return 'Rp ' + (tagihan + admin).toLocaleString();
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
						const detail = $('<button/>', {
							html: 'Detail',
							class: 'btn btn-primary btn-xs detail_button'
						})
						label += detail.prop('outerHTML');
						return label;
					}
				}
			],
		});
	};

	const getBulan = async () =>{
		const data = await $.ajax({
			type: "POST",
			url: `<?= base_url('faskar/listrik/get_bulan')?>`,
			data: {},
			dataType: 'json',
		}).catch(function(xhr, status, error){
			console.log(xhr.responseText);
		})
		listBulan = data;
		if(listBulan.length > 0){
			for (const i of listBulan) {
				$( "#filter-bulan" ).append(new Option(moment(i.bulan).format('MMMM YYYY'), i.bulan));
			}
		}
	}

	const hitung = async () =>{
		let beban_pegawai, beban_perusahaan;
		let tagihan = $('#tagihan').val()!=='' ? parseInt($('#tagihan').val()) : 0;
		let admin = $('#biaya_admin').val()!=='' ? parseInt($('#biaya_admin').val()) : 0;
		let plafon = $('#plafon').val()!=='' ? parseInt($('#plafon').val()) : 0;
		let pemakaian = tagihan + admin;
		$('#pemakaian').val(pemakaian);
		if( pemakaian >= plafon ){
			beban_perusahaan = plafon;
			beban_pegawai = pemakaian - plafon;
		} else{
			beban_pegawai = 0;
			beban_perusahaan = pemakaian;
		}
		$('#beban_pegawai').val(beban_pegawai);
		$('#beban_perusahaan').val(beban_perusahaan);
	}

	$("#tagihan, #biaya_admin, #plafon").on('keyup change', function(e){
		hitung();
	});
</script>

