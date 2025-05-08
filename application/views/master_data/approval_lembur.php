		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />

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

				<?php
					if(!empty($success)){
				?>
						<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $success;?>
						</div>
				<?php
					}
					if(!empty($warning)){
				?>
						<div class="alert alert-danger alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $warning;?>
						</div>
				<?php
					}

					if($akses["tambah"]){
				?>
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
											<form role="form" action="" id="formulir_tambah" method="post">
												<input type="hidden" name="aksi" value="tambah"/>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Daftar Divisi</label>
														</div>
														<div class="col-lg-7">
															<div class="form-group">
																<select class="form-control select2" name="divisi" style="width:100%">
																	<option value=''>--- Pilih Divisi ---</option>
																	<?php foreach ($daftar_divisi as $div) { ?>
																	<option value='<?= $div['kode_unit'] ?>'><?= $div['nama_unit'] ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div id="warning_divisi" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Jabatan Minimal Approval</label>
														</div>
														<div class="col-lg-7">
															<div class="form-group">
																<select class="form-control" name="approval">
																	<option value=''>--- Pilih Jabatan ---</option>
																	<option value='kasek'>Kepala Seksi</option>
																	<option value='kadep'>Kepala Departemen</option>
																	<option value='kadiv'>Kepala Divisi</option>
																</select>
															</div>
														</div>
														<div id="warning_approval" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12 text-center">
														<button type="submit" class="btn btn-primary" onclick="return cek_simpan_tambah()">Simpan</button>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- /.col-lg-12 -->
						</div>
						<!-- /.row -->
				<?php
					}
					
					if($this->akses["lihat"]){
				?>
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_approval_lembur">
								<thead>
									<tr>
										<th class='text-center'>No</th>
										<th>Divisi</th>
										<th class='text-center'>Approval</th>
										<?php
											if($akses["ubah"] or $akses["hapus"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_approval_lembur);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}

											if($daftar_approval_lembur[$i]["approval"]=="kasek")
												$approval = "Kepala Seksi";
											else if($daftar_approval_lembur[$i]["approval"]=="kasek")
												$approval = "Kepala Departemen";
											else
												$approval = "Kepala Divisi";
											
											echo "<tr class='$class'>";
												echo "<td class='text-center'>".($i+1)."</td>";
												echo "<td>".$daftar_approval_lembur[$i]["nama_unit"]."</td>";
												
												if($akses["ubah"]) {
													echo "<td class='text-center'>
														<select class='form-control' data-id='".$daftar_approval_lembur[$i]["id"]."' data-approval='".$daftar_approval_lembur[$i]["approval"]."' onchange='change_approval(this)'>
															<option value=''>--- Pilih Jabatan ---</option>
															<option value='kasek'".($daftar_approval_lembur[$i]["approval"]=="kasek" ? 'selected':'').">Kepala Seksi</option>
															<option value='kadep'".($daftar_approval_lembur[$i]["approval"]=="kadep" ? 'selected':'').">Kepala Departemen</option>
															<option value='kadiv'".($daftar_approval_lembur[$i]["approval"]=="kadiv" ? 'selected':'').">Kepala Divisi</option>
														</select>
													</td>";
												} else {
													echo "<td>".$approval."</td>";
												}

												if($akses["hapus"]){
													echo "<td class='text-center'>";
													echo "<button class='btn btn-warning btn-xs' data-id='".$daftar_approval_lembur[$i]["id"]."' onclick='hapus(this)'>Hapus</button>";
												}
											echo "</tr>";
										}
									?>
								</tbody>
							</table>
							<!-- /.table-responsive -->
						</div>
				
				<?php
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->



		<script src="<?php echo base_url('asset/sweetalert2')?>/sweetalert2.js"></script>
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		
		<script type="text/javascript">	

			$(document).ready(function() {
				
				<?php if(!empty($this->session->flashdata('success'))) { ?>
					Swal.fire({
					  	icon: 'success',
					  	title: '<?= $this->session->flashdata('success') ?>'
					});
				<?php } else if(!empty($this->session->flashdata('warning'))) { ?>
					Swal.fire({
					  	icon: 'warning',
					  	title: '<?= $this->session->flashdata('warning') ?>'
					});
				<?php } else if(!empty($this->session->flashdata('failed'))) { ?>
					Swal.fire({
					  	icon: 'error',
					  	title: '<?= $this->session->flashdata('failed') ?>'
					});
				<?php } ?>

                $('.select2').select2();
			});	
            
            // heru menambahkan ini 2020-11-28 @08:30
            function cekData(){
                $('.status-alert').text('Memeriksa data...');
				var tgl_mulai = $('#dari-tgl').val();
				var tgl_selesai = $('#sampai-tgl').val();
				var lokasi = $('#ex-lokasi').val();
				
				if(tgl_mulai != '' && tgl_selesai != '' && lokasi != ''){
					$.ajax({
						url: '<?= base_url('food_n_go/konsumsi/excel/cekDataBon') ?>',
						type: 'POST',
						data: {tgl_mulai: tgl_mulai, tgl_selesai: tgl_selesai, lokasi: lokasi},
						success: function(data) {
							if(data.response == 'kosong') {
								$('.status-alert').text('Data yang Anda cari tidak ditemukan');
								$('.btn-cetak').prop('disabled', true);
							}else{
								$('.status-alert').text('');
								$('.btn-cetak').prop('disabled', false);
							}
						}
					});
				} else{
                    $('.status-alert').text('');
                    $('.btn-cetak').prop('disabled', false);
                }
			}
            // END

			function change_approval(data){
				id = data.getAttribute('data-id');
				old = data.getAttribute('data-approval');
				approval = $(data).val();

				Swal.fire({
					title: 'Apakah anda yakin ingin mengganti minimal jabatan approval lembur ?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ya, ganti!'
				}).then((result) => {
					if (result.value) {
						$.ajax({
					        type: "POST",
					        url: "approval_lembur/change_approval",
					        data: { 'id':id, 'approval':approval},
					        cache: false,
					        success: function(response) {
					        	obj = JSON.parse(response);
							    Swal.fire(
							      	obj.judul,
							      	obj.txt,
							      	obj.alert
							   	)
							},
							failure: function (response) {
							    Swal.fire(
							      	'Gagal!',
							      	'Pesanan tidak dibatalkan.',
							      	'error'
							   	)
							}
						})
					} else {
						$(data).val(old);
	                    // $(data).trigger('change');
	                    // return;
					}
				})
			}

			function hapus(data){
				id = data.getAttribute('data-id');

				Swal.fire({
					title: 'Apakah anda yakin ingin mengapus divisi ini dari approval lembur ?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ya, hapus!'
				}).then((result) => {
					if (result.value) {
						$.ajax({
					        type: "POST",
					        url: "approval_lembur/hapus",
					        data: { 'id':id},
					        cache: false,
					        success: function(response) {
					        	obj = JSON.parse(response);
							    Swal.fire(
							      	obj.judul,
							      	obj.txt,
							      	obj.alert
							   	);
							   	window.location.href = "<?= base_url('master_data/approval_lembur') ?>"
							},
							failure: function (response) {
							    Swal.fire(
							      	'Gagal!',
							      	'Pesanan tidak dibatalkan.',
							      	'error'
							   	)
							}
						})
					}
				})
			}
		</script>