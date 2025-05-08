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
														<div class="col-lg-2">
															<label>Nama Modul</label>
														</div>
														<div class="col-lg-7">
															<div class="form-group">
																<select class="form-control" name="modul">
																	<option value=''>--- Pilih Modul ---</option>
																	<?php
																		$optgroup = "";
																		for($i=0;$i<count($daftar_modul_aksi);$i++){
																			if(strcmp($optgroup,$daftar_modul_aksi[$i]["nama_kelompok_modul"])!=0){
																				$optgroup=$daftar_modul_aksi[$i]["nama_kelompok_modul"];
																				echo "<optgroup label='$optgroup'>";
																			}
																			
																			echo "<option value='".$daftar_modul_aksi[$i]["id"]."'>".$daftar_modul_aksi[$i]["nama"]."</option>";
																			
																			if($i==count($daftar_modul_aksi)-1 or strcmp($optgroup,$daftar_modul_aksi[$i+1]["nama_kelompok_modul"])!=0){
																				echo "</optgroup>";
																			}
																		}
																	?>
																</select>
															</div>
														</div>
														<div id="warning_modul" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Nama <?php echo $judul;?></label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="nama" value="<?php echo $nama;?>" placeholder="Nama <?php echo $judul;?>">
														</div>
														<div id="warning_nama" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Status</label>
														</div>
														<div class="col-lg-7">
															<div class="radio">
																<label>
																	<?php
																		if(strcmp($status,"1")==0){
																			$checked="checked='checked'";
																		}
																		else{
																			$checked="";
																		}
																	?>
																	<input type="radio" name="status" id="status_tambah_aktif" value="aktif" <?php echo $checked;?>>Aktif
																</label>
																<label>
																	<?php
																		if(strcmp($status,"0")==0){
																			$checked="checked='checked'";
																		}
																		else{
																			$checked="";
																		}
																	?>
																	<input type="radio" name="status" id="status_tambah_non_aktif" value="non aktif" <?php echo $checked;?>>Non Aktif
																</label>
															</div>
														</div>
														<div id="warning_status" class="col-lg-3 text-danger"></div>
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
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_aksi_modul">
								<thead>
									<tr>
										<th class='text-center'>Nama Kelompok Modul</th>
										<th class='text-center'>Nama Modul</th>
										<th class='text-center'>Nama <?php echo $judul?></th>
										<th class='text-center'>Status</th>
										<?php
											if($akses["ubah"] or $akses["lihat log"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_aksi_modul);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td>".$daftar_aksi_modul[$i]["nama_kelompok_modul"]."</td>";
												echo "<td>".$daftar_aksi_modul[$i]["nama_modul"]."</td>";
												echo "<td>".$daftar_aksi_modul[$i]["nama_aksi"]."</td>";
												echo "<td class='text-center'>";
													if(strlen($daftar_aksi_modul[$i]["status"])>0){
														if((int)$daftar_aksi_modul[$i]["status"]==1){
															echo "Aktif";
														}
														else if((int)$daftar_aksi_modul[$i]["status"]==0){
															echo "Non Aktif";
														}
													}
												echo "</td>";
												if($akses["ubah"] or $akses["lihat log"]){
													echo "<td class='text-center'>";
														if($akses["ubah"]){
															if(strlen($daftar_aksi_modul[$i]["status"])>0){
																echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button> ";
															}
														}
														if($akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_aksi_modul[$i]["nama_modul"]."\",".$daftar_aksi_modul[$i]["id"].")'>Lihat Log</button>";
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
													<label>Nama Modul</label>
												</div>
												<div class="col-lg-5">
													<div class="form-group">
														<input type="hidden" name="modul" value="">
														<select class="form-control" name="modul_ubah">
															<option value=''>--- Pilih Modul ---</option>
															<?php
																$optgroup = "";
																for($i=0;$i<count($daftar_modul_aksi);$i++){
																	if(strcmp($optgroup,$daftar_modul_aksi[$i]["nama_kelompok_modul"])!=0){
																		$optgroup=$daftar_modul_aksi[$i]["nama_kelompok_modul"];
																		echo "<optgroup label='$optgroup'>";
																	}

																	echo "<option value='".$daftar_modul_aksi[$i]["nama"]."'>".$daftar_modul_aksi[$i]["nama"]."</option>";

																	if($i==count($daftar_modul_aksi)-1 or strcmp($optgroup,$daftar_modul_aksi[$i+1]["nama_kelompok_modul"])!=0){
																		echo "</optgroup>";
																	}
																}
															?>
														</select>
													</div>
												</div>
												<div id="warning_modul_ubah" class="col-lg-4 text-danger"></div>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-3">
													<label>Nama <?php echo $judul;?></label>
												</div>
												<div class="col-lg-5">
													<input type="hidden" name="nama" value="">
													<input class="form-control" name="nama_ubah" value="" placeholder="Nama <?php echo $judul;?>">
												</div>
												<div id="warning_nama_ubah" class="col-lg-4 text-danger"></div>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-3">
													<label>Status</label>
												</div>
												<div class="col-lg-5">
													<input type="hidden" name="status" value="">
													<div class="radio">
														<label>
															<input type="radio" name="status_ubah" id="status_ubah_aktif" value="aktif">Aktif
														</label>
														<label>
															<input type="radio" name="status_ubah" id="status_ubah_non_aktif" value="non aktif">Non Aktif
														</label>
													</div>
												</div>
												<div id="warning_status_ubah" class="col-lg-4 text-danger"></div>
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