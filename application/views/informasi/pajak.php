		<!-- Page Content -->
		<div id="page-wrapper">
			<!-- xxxxxxxxxxxxxxxxxx -->
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

				if($akses["tambah"]){ ?>
				<div class="row">
					<div class="col-lg-12">
						<!-- <div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Upload File</a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse <?php echo $panel_tambah;?>">
								<div class="panel-body">
									<div class="text-center">
										<div class="row">
											<div class="col-lg-4"></div>
											<div class="col-lg-4">
												<form role="form" action="<?= site_url('informasi/pajak/save_file') ?>" id="formulir_tambah" method="post" enctype="multipart/form-data">
													<input type="hidden" name="aksi" value="tambah"/>
													<div class="form-group">
														<label>File PPH21</label>
													</div>
													<div class="form-group">
														<input class="form-control" type="file" name="file_pph" placeholder="Pilih File" accept="application/pdf" >
														<span class="text-danger">Maksimal 100 File PDF</span>
													</div>
													<div class="form-group">
														<button type="submit" class="btn btn-primary" onclick="return confirm('Apakah anda yakin ingin mengupload file?')">Simpan</button>
													</div>
												</form>
											</div>
											<div class="col-lg-4"></div>
										</div>
									</div>
								</div>
							</div>
						</div> -->
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
					<div class='col-lg-2 text-right'>Tahun</div>
					<div class='col-lg-3'>
						<select class="form-control select2" name="tahun" id="tahun" onchange="refresh_table_serverside()">
							<option value="0" selected>Semua Tahun</option>
							<?php for($i=2019;$i<=date('Y');$i++) {
								echo "<option value='".$i."'>".$i."</option>";
							} ?>
						</select>
					</div>
					<!-- 17 02 2022 - Tri Wibowo , batasi hanya TI yg melakukan -->
					<div class='col-lg-2 text-right'>
						<!-- <a class="btn btn-primary" href="<?= base_url('informasi/pajak/generate') ?>" onclick="">Refresh</a> -->
						<button class="btn btn-primary" onclick="generateData()">
							Refresh Data
						</button>
					</div>
					
					<?php } ?>
					
				</div>

				<br>
				<div class="form-group">	
					<div class="row">
						<div class="col-lg-12 table-responsive">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_pajak">
								<thead>
									<tr>
										<th class='text-center no-sort'>NO</th>
										<th class='text-center no-sort'>NP Karyawan</th>
										<th class='text-center no-sort'>Tahun</th>
										<th class='text-center no-sort'>Masa Berkala</th>
										<th class='text-center no-sort'>File</th>
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

		<script src="<?php base_url()?>/ess/asset/cartenz/sweetalert2.all.min.js"></script>
		
		
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
			async function generateData(){
				var karyawan = $('#karyawan').val();
				var tahun = $('#tahun').val();

				if(tahun=="0") {
					swal.fire("Tahun Kosong", `Mohon memasukan Tahun untuk mengenrate data`, "error");
					return;
				}

				Swal.fire({
					title: 'Mengupdate Data...',
					text: 'Tunggu Sampai Data di Update',
					onBeforeOpen: () => {
						Swal.showLoading();
					},
					allowOutsideClick: false,
					allowEscapeKey: false,
					allowEnterKey: false,
					showConfirmButton: false
				});

				await $.ajax(`<?= base_url('informasi/pajak/generate') ?>/${karyawan}/${tahun}/`);

				swal.fire("Sukses", `Sukses Update Data ${karyawan!="0" ? karyawan : "Semua Karyawan"} Tahun ${tahun!="0" ? tahun : "(ALL)"}`, "success");
				
				refresh_table_serverside();
			}

			function table_serverside(){
				var table;				
				var karyawan = $('#karyawan').val();
				var tahun = $('#tahun').val();
				
				$('#tabel_pajak').DataTable().destroy();				
				//datatables
				table = $('#tabel_pajak').DataTable({ 
					
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
						"url": "<?php echo site_url("informasi/pajak/tabel_data/")?>",
						"type": "POST",
						"data": {karyawan: karyawan, tgl:tahun}
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
				document.getElementById("judul_rincian").innerHTML = element.parentElement.previousSibling.innerHTML;
			}
			
			$(document).on( "click", '.lihat_button',function(e) {		
				var id = $(this).data('id');
				var jns = $(this).data('jenis');
	
				$.ajax({
				type: "POST",
				dataType: "html",
				url: document.getElementById("base_url").value+"informasi/pajak/ajax_get_pajak_file/"+id+"/"+jns,
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