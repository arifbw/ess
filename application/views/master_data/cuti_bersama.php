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
											<form role="form" action="" id="formulir_tambah" method="post">
												<input type="hidden" name="aksi" value="tambah"/>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Tanggal <?php echo $judul;?></label>
														</div>
														<div class="col-lg-6">
															<input class="form-control" name="tanggal" type="date" value="<?php echo $tanggal;?>" placeholder="Tanggal <?php echo $judul;?>">
														</div>
														<div id="warning_tanggal" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Deskripsi</label>
														</div>
														<div class="col-lg-6">
															<input class="form-control" name="deskripsi" value="<?php echo $deskripsi;?>" placeholder="Deskripsi">
														</div>
														<div id="warning_deskripsi" class="col-lg-3 text-danger"></div>
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
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_cuti_bersama">
								<thead>
									<tr>
										<th class='text-center'>Tanggal</th>
										<th class='text-center'>Deskripsi</th>
										<?php
											if($akses["ubah"] or $akses["lihat log"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_cuti_bersama);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td>";
													echo "<input type='hidden' value='".$daftar_cuti_bersama[$i]["tanggal"]."'/>";
													echo tanggal($daftar_cuti_bersama[$i]["tanggal"]);
												echo "</td>";
												echo "<td>".$daftar_cuti_bersama[$i]["deskripsi"]."</td>";
												if($akses["ubah"] or $akses["lihat log"]){
													echo "<td class='text-center'>";
													if($akses["ubah"]){
														echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button> ";
													}
													if($akses["lihat log"]){
														echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_cuti_bersama[$i]["tanggal"]."\",".$daftar_cuti_bersama[$i]["id"].")'>Lihat Log</button>";
													}
													echo "</td>";
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
														<label>Tanggal <?php echo $judul;?></label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="tanggal" value="">
														<input class="form-control" name="tanggal_ubah" type="date" value="" placeholder="Tanggal <?php echo $judul;?>">
													</div>
													<div id="warning_tanggal_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Deskripsi</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="deskripsi" value="">
														<input class="form-control" name="deskripsi_ubah" value="" placeholder="deskripsi">
													</div>
													<div id="warning_deskripsi_ubah" class="col-lg-4 text-danger"></div>
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