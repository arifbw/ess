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
					if($akses["lihat log"]){
						echo "<div class='row text-right'>";
						echo "<div class='col-md-12'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
						echo "</div>";
					}

					if($akses["unggah"]){ 
						if($is_preview){
					?>
							<form action="<?= site_url('master_data/bypass_master_data_organisasi/save1') ?>" method="post">
								<div class="alert alert-info alert-dismissable">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									Berikut ini adalah data unit kerja beserta jabatan yang akan Anda unggah. Pastikan data ini telah sesuai dan tepat.
									<?php
										if (count($unit_kerja)>0) {
											echo "<input type='hidden' name='aksi' value='unggah'/>";
											echo "<input type='hidden' name='jumlah' value='".count($unit_kerja)."'/>";
											echo "<button type='submit' class='btn btn-success' onclick='return confirm(\'Apakah anda yakin?\')'>SIMPAN</button>";
										}
									?>
									<a href="<?= base_url('master_data/bypass_master_data_organisasi') ?>" class="btn btn-danger">Kembali</a>
								</div>
							</form>
					<?php
						}
						else{
					?>
							<div class="row">
								<div class="col-lg-12">
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Unggah <?php echo $judul;?></a>
											</h4>
										</div>
										<div id="collapseOne" class="panel-collapse collapse <?php echo $panel_unggah;?>">
											<div class="panel-body">
												<div class="text-center">
													<div class="row">
														<div class="col-lg-4">
															<a href="<?= base_url('file/template/IMPORT_ORGANISASI.xlsx') ?>" class="btn btn-primary">Download File Template</a>
															<br><br><br>
														</div>
														<div class="col-lg-8">
															<div class="form-group">
																<form role="form" action="" id="formulir_tambah" method="post" enctype="multipart/form-data">
																	<input type="hidden" name="aksi" value="unggah"/>
																	<label>File Excel</label>
																	<input class="form-control" type="file" name="file_excel" placeholder="Pilih File">
																	<span class="text-danger">File Excel Sesuai Template</span>
																	
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
								</div>
								<!-- /.col-lg-12 -->
							</div>
							<!-- /.row -->
							<?php
							}
						}

		
/* 		<script>	
			$(document).ready(function() {
				$('#tabel_preview_unit_kerja').dataTable({
					paging: false,
				});
				$('#tabel_preview_jabatan').dataTable({
					paging: false,
				});
			});
		</script> */
				

				if($this->akses["lihat"]){
					if($is_preview){
					?>	
						<div class="panel panel-default">
							<div class="panel-heading">Pratinjau Unit Kerja</div>
							<div class="panel-body" style="height: 120px; overflow-y: auto">
								<div class="form-group table-responsive">
									<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_preview_unit_kerja">
										<thead>
											<tr>
												<th class='text-center no-sort'>Kode Unit Kerja</th>
												<th class='text-center no-sort'>Nama Unit Kerja</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												for ($i=0; $i<count($unit_kerja); $i++) {
													echo "<tr>";
														echo "<td>".$unit_kerja[$i]['kode_unit']."</td>";
														echo "<td>".$unit_kerja[$i]['nama_unit']."</td>";
													echo "</tr>";
												}
											?>
										</tbody>
									</table>
									<!-- /.table-responsive -->
								</div>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">Pratinjau Jabatan</div>
							<div class="panel-body" style="height: 120px; overflow-y: auto">
								<div class="form-group table-responsive">
									<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_preview_jabatan">
										<thead>
											<tr>
												<th class='text-center no-sort'>Kode Unit Kerja</th>
												<th class='text-center no-sort'>Nama Unit Kerja</th>
												<th class='text-center no-sort'>Kode Jabatan</th>
												<th class='text-center no-sort'>Nama Jabatan</th>
												<th class='text-center no-sort'>Grade Jabatan</th>
												<th class='text-center no-sort'>Kelompok Jabatan</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												for ($i=0; $i<count($jabatan); $i++) {
													echo "<tr>";
														echo "<td>".$jabatan[$i]["kode_unit"]."</td>";
														echo "<td>".$jabatan[$i]["nama_unit"]."</td>";
														echo "<td>".$jabatan[$i]["kode_jabatan"]."</td>";
														echo "<td>".$jabatan[$i]["nama_jabatan"]."</td>";
														echo "<td>".$jabatan[$i]["grade_jabatan"]."</td>";
														echo "<td>".$jabatan[$i]["grup_jabatan"]."</td>";
													echo "</tr>";
												}
											?>
										</tbody>
									</table>
									<!-- /.table-responsive -->
								</div>
							</div>
						</div>
				<?php	
					}
				}
			?>
		</div>
	</div>