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
					if(@$akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					if(@$akses["tambah"]){
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
															<label>Nama Makanan</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="nama" placeholder="Nama Makanan">
														</div>
														<div id="warning_nama" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Harga</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="harga" placeholder="Harga">
														</div>
														<div id="warning_harga" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Lokasi</label>
														</div>
														<div class="col-lg-7">
															<!-- <select class="form-control" name='lokasi' style="width: 100%;" required>
																<option value='jakarta'>Jakarta</option>
																<option value='karawang'>Karawang</option>
															</select> -->
															<select class="form-control" name="lokasi" style="width: 100%;" required>
																<?php
																	foreach ($daftar_lokasi as $value) {
																?>
																<option value="<?= $value->id; ?>"><?= $value->nama; ?></option>
																<?php
																	}
																?>
															</select>
														</div>
														<div id="warning_lokasi" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Status</label>
														</div>
														<div class="col-lg-7">
															<label class='radio-inline'>
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
															<label class='radio-inline'>
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
					
					if(@$this->akses["lihat"]){
				?>
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_daftar_makanan">
								<thead>
									<tr>
										<th class='text-center'>#</th>
										<th class='text-center'>Lokasi</th>
										<th class='text-center'>Nama Makanan</th>
										<th class='text-center'>Harga</th>
										<th class='text-center'>Status</th>
										<?php
											if(@$akses["ubah"] or @$akses["lihat log"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_makanan);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td align='center'>".($i+1)."</td>";
												echo "<td>".$daftar_makanan[$i]["lokasi"]."</td>";
												echo "<td>".$daftar_makanan[$i]["nama"]."</td>";
												echo "<td>".$daftar_makanan[$i]["harga"]."</td>";
												echo "<td class='text-center'>";
													if((int)$daftar_makanan[$i]["status"]==1){
														echo "Aktif";
													}
													else if((int)$daftar_makanan[$i]["status"]==0){
														echo "Non Aktif";
													}
												echo "</td>";
												if(@$akses["ubah"] or @$akses["lihat log"]){
													echo "<td class='text-center'>";
														if(@$akses["ubah"]){
															echo "<button type='button' class='btn btn-primary btn-xs' data-id='".$daftar_makanan[$i]["id"]."' data-nama='".$daftar_makanan[$i]["nama"]."' data-harga='".$daftar_makanan[$i]["harga"]."' data-lokasi='".strtolower($daftar_makanan[$i]["id_lokasi"])."' data-status='".$daftar_makanan[$i]["status"]."' onclick='tampil_data_ubah_new(this)'>Ubah</button> ";
														}
														if(@$akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_makanan[$i]["nama"]."\",".$daftar_makanan[$i]["id"].")'>Lihat Log</button>";
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
					
					if(@$akses["ubah"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="<?= base_url('food_n_go/master_data/daftar_makanan')?>" id="formulir_ubah" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="aksi" value="ubah"/>
                                            <input type="hidden" name="id_ubah" id="id_ubah">
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Nama Makanan</label>
													</div>
													<div class="col-lg-5">
                                                        <input type="hidden" name="nama_old" id="nama_old">
														<input type="text" class="form-control" name="nama_ubah" id="nama_ubah" value="" placeholder="Nama Makanan" required>
													</div>
													<div id="warning_nama_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Harga</label>
													</div>
													<div class="col-lg-5">
                                                        <input type="hidden" name="harga_old" id="harga_old">
														<input type="text" class="form-control" name="harga_ubah" id="harga_ubah" value="" placeholder="Harga Makanan" required>
													</div>
													<div id="warning_nama_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Lokasi</label>
													</div>
													<div class="col-lg-5">
                                                        <input type="hidden" name="lokasi_old" id="lokasi_old">
														<select class="form-control" name='lokasi_ubah' id="lokasi_ubah" style="width: 100%;" required>
															<?php
																foreach ($daftar_lokasi as $value) {
															?>
															<option value="<?= $value->id; ?>"><?= $value->nama; ?></option>
															<?php
																}
															?>
														</select>
													</div>
													<div id="warning_lokasi_ubah" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Status</label>
													</div>
													<div class="col-lg-5">
                                                        <input type="hidden" name="status_old" id="status_old">
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
											<button type="submit" class="btn btn-primary" onclick="/*return cek_simpan_ubah()*/">Simpan</button>
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