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
			<div class="col-lg-12 text-right">
				<?php
				if( @$akses["export"] ){
					// echo '<button class="btn btn-success btn-md" onclick="">Export</button>&nbsp;';
				}
				if( @$akses["tambah"] ){
					echo '<button class="btn btn-primary btn-md" data-toggle="modal" data-target="#modal-import">Import</button>&nbsp;';
				}
				if( @$akses["lihat log"] ){
					echo '<button class="btn btn-default btn-md" onclick="lihat_log()">Lihat Log</button>';
				}
				?>
			</div>
		</div>
		<br>
		
		<?php if(@$akses['tambah']){?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse <?php echo @$panel_tambah;?>">
						<div class="panel-body">
							<div id="alert-formulir-tambah"></div>

							<form role="form" id="formulir-tambah" method="post" onsubmit="return false;">

								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>NP <code>*</code></label>
										</div>
										<div class="col-lg-7">
											<input type="text" class="form-control" name="np_karyawan" placeholder="NP Karyawan" required>
										</div>
										<div id="warning_np_karyawan" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Nama <code>*</code></label>
										</div>
										<div class="col-lg-7">
											<input type="text" class="form-control" name="nama" placeholder="Nama Karyawan" required>
										</div>
										<div id="warning_nama" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Start Date <code>*</code></label>
										</div>
										<div class="col-lg-7">
											<input type="date" class="form-control" name="start_date" id="start_date" placeholder="Start Date" onchange="setEndDate();" value="<?= date('Y-m-d')?>" required>
										</div>
										<div id="warning_start_date" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>End Date <code>*</code></label>
										</div>
										<div class="col-lg-7">
											<input type="date" class="form-control" name="end_date" id="end_date" placeholder="End Date" min="<?= date('Y-m-d')?>" value="<?= date('Y-m-d')?>" required>
										</div>
										<div id="warning_end_date" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Kode Unit <code>*</code></label>
										</div>
										<div class="col-lg-7">
											<input type="text" class="form-control" name="kode_unit" placeholder="Kode Unit" required>
										</div>
										<div id="warning_kode_unit" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Nama Unit <code>*</code></label>
										</div>
										<div class="col-lg-7">
											<input type="text" class="form-control" name="nama_unit" placeholder="Nama Unit" required>
										</div>
										<div id="warning_nama_unit" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Nama Unit SAP <code>*</code></label>
										</div>
										<div class="col-lg-7">
											<input type="text" class="form-control" name="nama_unit_sap" placeholder="Nama Unit SAP" required>
										</div>
										<div id="warning_nama_unit_sap" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>Keterangan</label>
										</div>
										<div class="col-lg-7">
											<input type="text" class="form-control" name="keterangan" placeholder="Keterangan">
										</div>
										<div id="warning_keterangan" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								
								<div class="row">
									<div class="form-group">
										<div class="col-lg-2">
											<label>SPK</label>
										</div>
										<div class="col-lg-7">
											<input type="text" class="form-control" name="spk" placeholder="SPK">
										</div>
										<div id="warning_spk" class="col-lg-3 text-danger"></div>
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12 text-center">
										<button type="button" class="btn btn-primary" id="btn-submit-baru" onclick="doSubmit()">Simpan</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
			}
			if($this->akses["lihat"]){
		?>				
				<div class="form-group">	
					<div class="row">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_karyawan_outsource">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>NP</th>	
									<th class='text-center'>Nama</th>	
									<th class='text-center'>Unit</th>			
									<th class='text-center'>Start Date</th>
									<th class='text-center'>End Date</th>
									<th class='text-center'>Keterangan</th>
									<th class='text-center no-sort'>Aksi</th>
								</tr>
							</thead>
						</table>
					</div>						
				</div>
				
				<!-- Modal Status -->
				<div class="modal fade" id="modal_status" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_status">Status <?php echo $judul;?></h4>
							</div>
							<div class="modal-body">		
								<table>
									<tr>
										<td>Np Pemohon</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_np_karyawan"></a></td>
									</tr>
									<tr>
										<td>Nama Pemohon</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_nama"></a></td>
									</tr>
									<tr>
										<td>Dibuat Tanggal</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_created_at"></a></td>
									</tr>
									<tr>
										<td>Dibuat Oleh</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_created_by"></a></td>
									</tr>
								</table>
								
								<br>
								
								<div class="alert alert-info">
									<strong><a id="status_approval_1_nama"></a></strong><br>
									<p id="status_approval_1_status"></p>
								</div>
								
								<div class="alert alert-info">
									<strong><a id="status_approval_2_nama"></a></strong><br>
									<p id="status_approval_2_status"></p>
								</div>
								
							</div>										
						</div>
					</div>
				</div>
		<?php
			}
		?>

		<?php if(@$akses['tambah']){?>
		<div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="label_modal_import" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		 	<div class="modal-dialog modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="import-title"><strong>Import Data</strong></h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">×</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
		      			<form id="import-excel" action="<?= base_url('osdm/Karyawan_outsource/import_data')?>" method="POST" enctype="multipart/form-data">
			      			<div class="alert alert-info alert-dismissable text-center">
								<a class="btn btn-primary" target="_blank" href="<?= base_url('file/template/template-upload-karyawan-outsource.xlsx')?>">Klik Disini Download Template File Untuk Mengisi Data Outsource</a>
								<br><br>
								<h4>Upload File Pada Form Dibawah Ini Untuk Import Data Outsource<br>Sesuai Template Diatas</h4>
								<div class="form-group">
									<input type="file" name="import_excel" class="form-control upload-import" required>
								</div>
								<div class="form-group">
									<button class="btn btn-primary btn-import" type="submit">Import</button>
								</div>
							</div>
		      			</form>
		      		</div>
		    	</div>
		  	</div>
		</div>
		<?php } ?>

		<?php if(@$akses['ubah']){?>
		<div class="modal fade" id="modal-ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true" data-backdrop="static" data-keyboard="false">
		 	<div class="modal-dialog modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="ubah-title"><strong>Ubah Data</strong></h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">×</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
					  	<div id="alert-formulir-ubah"></div>
					  	<form role="form" id="formulir-ubah" method="post" onsubmit="return false;">

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>NP <code>*</code></label>
									</div>
									<div class="col-lg-8">
										<input type="text" class="form-control" name="ubah_np_karyawan" id="ubah_np_karyawan" placeholder="NP Karyawan" readonly required>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>Nama <code>*</code></label>
									</div>
									<div class="col-lg-8">
										<input type="text" class="form-control" name="ubah_nama" id="ubah_nama" placeholder="Nama Karyawan" required>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>Start Date <code>*</code></label>
									</div>
									<div class="col-lg-8">
										<input type="date" class="form-control" name="ubah_start_date" id="ubah_start_date" placeholder="Start Date" onchange="setEndDate();" value="<?= date('Y-m-d')?>" required>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>End Date <code>*</code></label>
									</div>
									<div class="col-lg-8">
										<input type="date" class="form-control" name="ubah_end_date" id="ubah_end_date" placeholder="End Date" min="<?= date('Y-m-d')?>" value="<?= date('Y-m-d')?>" required>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>Kode Unit <code>*</code></label>
									</div>
									<div class="col-lg-8">
										<input type="text" class="form-control" name="ubah_kode_unit" id="ubah_kode_unit" placeholder="Kode Unit" required>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>Nama Unit <code>*</code></label>
									</div>
									<div class="col-lg-8">
										<input type="text" class="form-control" name="ubah_nama_unit" id="ubah_nama_unit" placeholder="Nama Unit" required>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>Nama Unit SAP <code>*</code></label>
									</div>
									<div class="col-lg-8">
										<input type="text" class="form-control" name="ubah_nama_unit_sap" id="ubah_nama_unit_sap" placeholder="Nama Unit SAP" required>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>Keterangan</label>
									</div>
									<div class="col-lg-8">
										<input type="text" class="form-control" name="ubah_keterangan" id="ubah_keterangan" placeholder="Keterangan">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="form-group">
									<div class="col-lg-4">
										<label>SPK</label>
									</div>
									<div class="col-lg-8">
										<input type="text" class="form-control" name="ubah_spk" id="ubah_spk" placeholder="SPK">
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-12 text-center">
									<button type="button" class="btn btn-primary" id="btn-submit-ubah" onclick="doUbah()">Simpan</button>
								</div>
							</div>
						</form>
		      		</div>
		    	</div>
		  	</div>
		</div>
		<?php } ?>

	</div>
</div>

<script src="<?= base_url('asset/jquery-validate/1.19.2/jquery.validate.min.js')?>"></script>
<script type="text/javascript">
	var table;
	$(document).ready(function() {
		table_serverside();

		$('#formulir-tambah').validate({
            rules: {
                np_karyawan: {
                    required: true
                }, 
                nama: {
                    required: true
                }, 
                start_date: {
                    required: true
                },
                end_date: {
                    required: true
                },
                kode_unit: {
                    required: true
                },
                nama_unit: {
                    required: true
                },
                nama_unit_sap: {
                    required: true
                }
            },
            submitHandler: function (form) {
                saveKaryawan()
            }
        });

		$('#formulir-ubah').validate({
            rules: {
                ubah_np_karyawan: {
                    required: true
                }, 
                ubah_nama: {
                    required: true
                }, 
                ubah_start_date: {
                    required: true
                },
                ubah_end_date: {
                    required: true
                },
                ubah_kode_unit: {
                    required: true
                },
                ubah_nama_unit: {
                    required: true
                },
                ubah_nama_unit_sap: {
                    required: true
                }
            },
            submitHandler: function (form) {
                updateKaryawan()
            }
        });
	});

	function doSubmit(){
        $('#formulir-tambah').submit();
    }

	function saveKaryawan(){
		let allData = {};
        $("#formulir-tambah input").each(function(){
            if( $(this).attr('type')=='radio' ){
                if( $(this).is(':checked') )
                    allData[$(this).attr('name')] = this.value;
            } else{
                allData[$(this).attr('name')] = this.value;
            }
        });
		$('#alert-formulir-tambah').html('Menyimpan...');
		$('#btn-submit-baru').prop('disabled',true);
		$.ajax({
            type: "POST",
            url: `<?= base_url('osdm/karyawan_outsource/simpan')?>`,
            data: allData,
            dataType: 'json',
        }).then(function(response){
            $('#btn-submit-baru').prop('disabled',false);
            $('#alert-formulir-tambah').html(response.message);
			if(response.status===true){
				$("#formulir-tambah")[0].reset();
				table_serverside();
				setTimeout(function(){ $('#alert-formulir-tambah').html(''); }, 5000);
			}
			console.log(response)
        }).catch(function(xhr, status, error){
            $('#btn-submit-baru').prop('disabled',false);
            $('#alert-formulir-tambah').html(xhr.responseText);
        })
	}

	function table_serverside() {
		table = $('#tabel_ess_karyawan_outsource').DataTable({ 
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
			"order": [],
			"ajax": {				
				"url"	: "<?php echo site_url("osdm/Karyawan_outsource/tabel_karyawan_outsource")?>",					 
				"type"	: "POST"
			},
			"columnDefs": [
				{ 
					"targets": 'no-sort',
					"orderable": false
				},
			]
		});
	};

	function setEndDate(){
		let startDate = $('#start_date').val();
		let endDate = $('#end_date').val();
		if( startDate > endDate ) document.getElementById("end_date").min = startDate; 
	}

	function deleteItem(np) {
		if (confirm("Hapus Karyawan?")) {
			$.ajax({
				type: "POST",
				url: `<?= base_url('osdm/karyawan_outsource/hapus_by_np')?>`,
				data: {np: np},
				dataType: 'json',
			}).then(function(response){
				table.draw(false);
			}).catch(function(xhr, status, error){
				console.log(xhr.responseText);
			})
		}
		return false;
	}

	$(document).on( "click", '.ubah-data-karyawan',function(e) {
		let att = this.dataset;
		let fields = ['np_karyawan','nama','start_date','end_date','kode_unit','nama_unit','nama_unit_sap','keterangan','spk'];
		$.each(att, function(key, value){
			if(fields.includes(key)){
				$(`#ubah_${key}`).val(value);
			}
		});

		$('#modal-ubah').modal('show');
	});

	function doUbah(){
        $('#formulir-ubah').submit();
    }

	function updateKaryawan(){
		let allData = {};
        $("#formulir-ubah input").each(function(){
			if( $(this).attr('type')=='radio' ){
				if( $(this).is(':checked') )
				allData[$(this).attr('name')] = this.value;
            } else{
				allData[$(this).attr('name')] = this.value;
            }
        });
		
		$('#alert-formulir-ubah').html('Menyimpan...');
		$('#btn-submit-ubah').prop('disabled',true);
		$.ajax({
            type: "POST",
            url: `<?= base_url('osdm/karyawan_outsource/update')?>`,
            data: allData,
            dataType: 'json',
        }).then(function(response){
            $('#btn-submit-ubah').prop('disabled',false);
            $('#alert-formulir-ubah').html(response.message);
			if(response.status===true){
				table.draw(false);
				setTimeout(function(){ $('#alert-formulir-ubah').html(''); }, 5000);
			}
        }).catch(function(xhr, status, error){
            $('#btn-submit-ubah').prop('disabled',false);
            $('#alert-formulir-ubah').html(xhr.responseText);
        })
	}

	$('#modal-ubah').on('hidden.bs.modal', function () {
		$("#formulir-ubah")[0].reset();
	})
</script>

