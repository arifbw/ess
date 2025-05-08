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
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
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
											<form action="" method="post" enctype="multipart/form-data">
												<input type="hidden" name="aksi" value="tambah"/>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Foto Karyawan</label>
														</div>
														<div class="col-lg-7">
															<div class="form-group">
																<input type="hidden" id="max_file" name="max_file" value="<?php echo $max_file;?>"/>
																<input type="file" id="file_foto" name="file_foto[]" accept="image/jpeg" multiple onchange="hitung_tambah()"/>
															</div>
														</div>
														<div id="warning_foto_karyawan" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12">
														Maksimal <?php echo $max_file;?> Foto. Format nama file : np.jpg. Contoh : 1234.jpg
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12 text-center">
														<button type="submit" class="btn btn-primary" onclick="return cek_simpan_tambah()">Simpan</button>
														<button type="button" class="btn btn-danger" onclick="return batal_tambah()">Batal</button>
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
						<input type="hidden" id="halaman" value="1"/>
						<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
							<div class="row">
								<div class="col-sm-6"></div>
								<div class="col-sm-6">
									<div class="dataTables_filter">
										<label>
										Search : <input class="form-control input-sm" id="cari" type="search" placeholder="" onkeyup="cari()"/>
										</label>
									</div>
								</div>
							</div>
						</div>
						<div id="foto_karyawan"></div>
				<?php
					}
					
					if($akses["ubah"]){
				?>
				
						<!-- Modal -->
						<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_ubah" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="aksi" value="ubah"/>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Karyawan</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" id="no_pokok_ubah" name="no_pokok_ubah" value=""/>
														<span id="ubah_no_pokok"></span> - <span id="ubah_nama"></span>
													</div>
													<div id="warning_karyawan_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Foto Karyawan</label>
													</div>
													<div class="col-lg-5">
														<div class="form-group">
															<input type="file" id="file_foto_ubah" name="file_foto_ubah" accept="image/jpeg" onchange="ubah()"/>
														</div>
													</div>
													<div id="warning_foto_karyawan_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-12">
													Format nama file : np.jpg. Contoh : 1234.jpg
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary" onclick="return cek_simpan_ubah()">Simpan</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
				<?php
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->