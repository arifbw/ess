		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul;?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<?php if(!empty($this->session->flashdata('success'))){ ?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('success');?>
				</div>
				<?php }
				if(!empty($this->session->flashdata('warning'))){ ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('warning');?>
				</div>
				<?php }
				
				echo "<div class='row text-right'>";
				if($akses["refresh"]){
					echo "<div class='col-md-11'>";
						echo "<a class='btn btn-danger btn-md' href='".base_url('informasi/skep/generate_from_db')."'><i class='fa fa-refresh'></i> Refresh</a>";
						echo "<br><br>";
					echo "</div>";
				}
				if($akses["lihat log"]){
					echo "<div class='col-md-1'>";
						echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
						echo "<br><br>";
					echo "</div>";
				}
				echo "</div>";


				if($akses["tambah"]){ ?>
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse <?php echo $panel_tambah;?>">
								<div class="panel-body">
									<div class="text-center">
										<div class="row">
											<div class="col-lg-12">
												<a href="<?= base_url('file/template/IMPORT_SKEP.xlsx') ?>" class="btn btn-primary">Download File Template</a>
												<br><br><br>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-offset-3 col-lg-6">
												<div class="alert alert-danger alert-dismissable">
													MAKSIMAL IMPORT FILE SEBANYAK 1000 DATA
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-4">
												<form role="form" action="" id="formulir_tambah" method="post" enctype="multipart/form-data">
													<input type="hidden" name="aksi" value="tambah"/>
													<div class="form-group">
														<label>1. File Excel</label>
													</div>
													<div class="form-group">
														<input class="form-control" type="file" name="file_excel" placeholder="Pilih File">
														<span class="text-danger">File Excel Sesuai Template</span>
													</div>
													<div class="form-group">
														<button type="submit" class="btn btn-primary" onclick="return cek_simpan_tambah()">Preview</button>
													</div>
												</form>
											</div>
											<div class="col-lg-4">
												<form role="form" action="<?= site_url('informasi/skep/save_file/umum') ?>" id="formulir_tambah" method="post" enctype="multipart/form-data">
													<input type="hidden" name="aksi" value="tambah"/>
													<div class="form-group">
														<label>2. File Umum</label>
													</div>
													<div class="form-group">
														<input class="form-control" type="file" name="file_umum" placeholder="Pilih File">
														<span class="text-danger">File PDF</span>
													</div>
													<div class="form-group">
														<button type="submit" class="btn btn-primary" onclick="return confirm('Apakah anda yakin ingin mengupload file?')">Simpan</button>
													</div>
												</form>
											</div>
											<div class="col-lg-4">
												<form role="form" action="<?= site_url('informasi/skep/save_file/individu') ?>" id="formulir_tambah" method="post" enctype="multipart/form-data">
													<input type="hidden" name="aksi" value="tambah"/>
													<div class="form-group">
														<label>3. File Individu</label>
													</div>
													<div class="form-group">
														<input class="form-control" type="file" name="file_individu[]" placeholder="Pilih File" multiple>
														<span class="text-danger">Maksimal 100 File PDF</span>
													</div>
													<div class="form-group">
														<button type="submit" class="btn btn-primary" onclick="return confirm('Apakah anda yakin ingin mengupload file?')">Simpan</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->
				<?php }

				if($this->akses["lihat"]){ ?>
				<div class="row">
					<div class='col-lg-1'>Karyawan</div>
					<div class='col-lg-4'>
						<select class="form-control select2" name="karyawan" id="karyawan" onchange="refresh_table_serverside()">
						<?php if ($akses["pilih seluruh karyawan"]) { ?>
							<option value='0'>Pilih Semua Karyawan</option>
						<?php } ?>
						<?php
							for($i=0;$i<count($daftar_akses_karyawan);$i++){
								if(strcmp($daftar_akses_karyawan[$i]["no_pokok"],$this->session->userdata("no_pokok"))==0){
									$selected="selected=selected";
								}
								else{
									$selected="";
								}
								echo "<option value='".$daftar_akses_karyawan[$i]["no_pokok"]."' $selected>".$daftar_akses_karyawan[$i]["no_pokok"]." - ".$daftar_akses_karyawan[$i]["nama"]."</option>";
							}
						?>
						</select>
					</div>

					<?php if ($akses["pilih seluruh karyawan"]) { ?>
					<div class='col-lg-2 text-right'>Nomor SKEP</div>
					<div class='col-lg-4'>
						<select class="form-control select2" name="nomor" id="nomor" onchange="refresh_table_serverside()">
							<option value='0'>Semua Nomor</option>
							<?php for($i=0;$i<count($nomor_skep);$i++){
								if(strcmp($nomor_skep[$i]["nomor"],$this->session->userdata("nomor"))==0) {
									$selected="selected=selected";
								}
								else{
									$selected="";
								}
								echo "<option value='".$this->encrypt->encode($nomor_skep[$i]["nomor"])."' $selected>".$nomor_skep[$i]["nomor"]."</option>";
							} ?>
						</select>
					</div>
					<?php if ($akses['hapus']) { ?>
					<div class='col-lg-1'><button class='btn btn-danger delete' data-id='0'><i class="fa fa-trash"></i></button></div>
					<?php } ?>
					<?php } ?>
				</div>

				<br>
				<input type="hidden" name="log_skep" id="log_skep" value="no">
				<div class="form-group">	
					<div class="row">
						<div class="col-lg-12 table-responsive">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_skep">
								<thead>
									<tr>
										<th class='text-center no-sort'>NO</th>
										<th class='text-center no-sort'>NP</th>
										<th class='text-center no-sort'>NAMA</th>
										<th class='text-center no-sort'>SKEP</th>
										<th class='text-center no-sort'>Aktif</th>
										<th class='text-center no-sort'>File 1</th>
										<th class='text-center no-sort'>File 2</th>
										<?php if ($akses['hapus']) { ?>
										<th class='text-center no-sort'>Aksi</th>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
								
								</tbody>
							</table>
							<!-- /.table-responsive -->
						</div>
					</div>						
				</div>
						
				<!-- Modal lihat -->
				<div class="modal fade" id="modal_lihat" tabindex="-1" role="dialog" aria-labelledby="label_modal_lihat" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="label_modal_np"><span id="judul_rincian"></span></h4>
								</div>
								<div class="modal-body">	
									<p name='list_detail_payment' id='list_detail_payment'></p>
								</div>										
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
				
				<?php } ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		
		<script type="text/javascript">	
			$(document).ready(function() {
				$("#log_skep").val("yes");
				table_serverside();
				$("#log_skep").val("no");
			});		
			
			function refresh_table_serverside() {
				$("#log_skep").val("yes");
				table_serverside();
				$("#log_skep").val("no");
			}
		</script>
		
		<script>		
			function table_serverside(){
				var table;				
				var karyawan = $('#karyawan').val();
				var no = $('#nomor').val();
				
				$('#tabel_skep').DataTable().destroy();				
				//datatables
				table = $('#tabel_skep').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"bFilter": true,
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("informasi/skep/tabel_skep/")?>",
						"type": "POST",
						"data": {karyawan: karyawan, no:no}
					},

					//Set column definition initialisation properties.
					"columnDefs": [
					{ 						
						"targets": [ 0 ], //first column / numbering column
						"targets": 'no-sort', //first column / numbering column
						"orderable": false, //set not orderable
					},
					],

				});
			
			}	
			
			function tampil_rincian(element){
				document.getElementById("judul_rincian").innerHTML = element.parentElement.previousSibling.previousSibling.innerHTML;
			}
			
			function tampil_rincian_file(element){
				document.getElementById("judul_rincian").innerHTML = 'File '.$(element).data('jenis');
			}
			
			$(document).on( "click", '.lihat_button',function(e) {		
				var id = $(this).data('id');
				var jns = $(this).data('jenis');
	
				$.ajax({
				type: "POST",
				dataType: "html",
				url: document.getElementById("base_url").value+"informasi/skep/ajax_get_skep_file/"+id+"/"+jns,
				data: "id="+id,
				success: function(msg){
						if(msg == ''){
							alert ('Terjadi Kesalahan');						
							$('#list_detail_payment').text('');
						}else{								
							$('#list_detail_payment').html(msg);														
						}													  
					 }
				 });
			});

			<?php if ($akses['hapus']) { ?>
			$(document).on('click', '.delete', function(e) {
				e.preventDefault();

				let key = $(this).data('id');
				let tgl = '';
				if (key!='0')
				nomor = $(this).data('nomor');
				else
				nomor = $('#nomor').val();


				Swal.fire({
				  title: 'Apakah ingin menghapus data?',
				  icon: 'warning',
				  showCancelButton: true,
				  confirmButtonColor: '#3085d6',
				  cancelButtonColor: '#d33',
				  confirmButtonText: 'Ya, hapus!',
				  cancelButtonText: 'Batal',
				}).then((result) => {
				  if (result.value) {
				  	$.ajax({
				  		url: "<?= base_url('informasi/skep/hapus/') ?>",
				  		data: {key:key, nomor:nomor},
				  		type: "POST",
				  		success: function(data) {
				  			rslt = JSON.parse(data);
				  			refresh_table_serverside();
				  			Swal.fire({
							  icon: rslt.status,
							  title: rslt.txt
							});
				  		}
				  	});
				  }
				});
			});
			<?php } ?>
		</script>