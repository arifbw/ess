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
					// if($akses["lihat log"]){
					// 	echo "<div class='row text-right'>";
					// 		echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
					// 		echo "<br><br>";
					// 	echo "</div>";
					// }
					if($akses["scan"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' data-toggle='modal' data-target='#modal_scan'>Scan</button>";
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
											<form role="form" action="" id="formulir_tambah" method="post" enctype="multipart/form-data">
												<input type="hidden" name="aksi" value="tambah"/>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Nama</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="nama" value="<?php echo $nama;?>" placeholder="Nama">
														</div>
														<div id="warning_nama" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Konten</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="konten" value="<?php echo $konten;?>" placeholder="Konten">
														</div>
														<div id="warning_konten" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Poin</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" type="number" name="poin" value="<?php echo $poin;?>" placeholder="Poin">
														</div>
														<div id="warning_poin" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Kuota</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" type="number" name="kuota" value="<?php echo $kuota;?>" placeholder="Kuota">
														</div>
														<div id="warning_kuota" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Tgl Awal</label>
														</div>
														<div class="col-lg-7">
															 <input type="date" class="form-control" name="start_date" value="<?php echo $start_date;?>" id="start_date" onChange="getEndDate()" required>
														</div>
														<div id="warning_start_date" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Tgl Akhir</label>
														</div>
														<div class="col-lg-7">
															 <input type="date" class="form-control" name="end_date" value="<?php echo $end_date;?>"  id="end_date" min="" required>
														</div>
														<div id="warning_end_date" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="form-group">
													<div class="row">
														<div class="col-lg-2">
															<label>Upload Gambar <code>(png|jpg|jpeg)</code></label>
														</div>
														<div class="col-lg-7">
															<input type="file" class="form-control"accept=".jpg,.jpeg,.png" name="gambar" id="gambar" onchange="checkPhoto(this)"/>
														</div>
														<div id="warning_gambar" class="col-lg-3 text-danger"></div>
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
					
					if($akses["lihat"]){
				?>
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_manajemen_poin_reward">
								<thead>
									<tr>
										<th class='text-center'>Gambar</th>
										<th class='text-center'>Nama</th>
										<th class='text-center'>Konten</th>
										<th class='text-center'>Poin</th>
										<th class='text-center'>Kuota</th>
										<th class='text-center'>Tgl Awal</th>
										<th class='text-center'>Tgl Akhir</th>
										<th class='text-center'>Status</th>
										<th class='text-center'>Jumlah Klaim</th>
										<?php
											if($akses["ubah"] or $akses["riwayat"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_manajemen_poin_reward);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td align='center'><img src=".base_url()."uploads/images/poin_reward/".$daftar_manajemen_poin_reward[$i]["gambar"]." style='width: 100%; max-height: 150px;' alt='Gambar Belum Diupload'></td>";
												echo "<td align='center'>".$daftar_manajemen_poin_reward[$i]["nama"]."</td>";
												echo "<td>".$daftar_manajemen_poin_reward[$i]["konten"]."</td>";
												echo "<td align='center'>".$daftar_manajemen_poin_reward[$i]["poin"]."</td>";
												echo "<td align='center'>".$daftar_manajemen_poin_reward[$i]["kuota"]."</td>";
												echo "<td align='center'>".$daftar_manajemen_poin_reward[$i]["start_date"]."</td>";
												echo "<td align='center'>".$daftar_manajemen_poin_reward[$i]["end_date"]."</td>";
												echo "<td class='text-center'>";
													if((int)$daftar_manajemen_poin_reward[$i]["status"]==1){
														echo "Aktif";
													}
													else if((int)$daftar_manajemen_poin_reward[$i]["status"]==0){
														echo "Non Aktif";
													}
												echo "</td>";
												echo "<td align='center'>".$daftar_manajemen_poin_reward[$i]["jumlah_klaim"]."</td>";
												if($akses["ubah"] or $akses["riwayat"]){
													echo "<td class='text-center'>";
														if($akses["ubah"]){
															echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button> ";
														}
														if ($akses["riwayat"]) {
															echo "<a href='".base_url($url_riwayat)."/".$daftar_manajemen_poin_reward[$i]["created_by_np"]."/".$daftar_manajemen_poin_reward[$i]["nama"]."' class='btn btn-primary btn-xs'>Riwayat</a> ";
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
									<form role="form" action="" id="formulir_ubah" method="post" enctype="multipart/form-data">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="aksi" value="ubah"/>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Nama</label>
													</div>
													<div class="col-lg-6">
														<input type="hidden" name="nama" value="">
														<input class="form-control" name="nama_ubah" value="" placeholder="Nama">
													</div>
													<div id="warning_nama_ubah" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Konten</label>
													</div>
													<div class="col-lg-6">
														<input type="hidden" name="konten" value="">
														<input class="form-control" name="konten_ubah" value="" placeholder="Konten">
													</div>
													<div id="warning_konten_ubah" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Poin</label>
													</div>
													<div class="col-lg-6">
														<input type="hidden" name="poin" value="">
														<input class="form-control" type="number" name="poin_ubah" value="" placeholder="Poin">
													</div>
													<div id="warning_poin_ubah" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Kuota</label>
													</div>
													<div class="col-lg-6">
														<input type="hidden" name="kuota" value="">
														<input class="form-control" type="number" name="kuota_ubah" value="" placeholder="Kuota">
													</div>
													<div id="warning_kuota_ubah" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Tgl Awal</label>
													</div>
													<div class="col-lg-6">
														<input type="hidden" name="start_date" value="">
														<input type="date" class="form-control" name="start_date_ubah" id="start_date_ubah" onChange="getEndDate()" required>
													</div>
													<div id="warning_start_date_ubah" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Tgl Akhir</label>
													</div>
													<div class="col-lg-6">
														<input type="hidden" name="end_date" value="">
														<input type="date" class="form-control" name="end_date_ubah" id="end_date_ubah" min="" required>
													</div>
													<div id="warning_end_date_ubah" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="form-group">
												<div class="row">
													<div class="col-lg-3">
														<label>Upload Gambar <code>(png|jpg|jpeg)</code></label>
													</div>
													<div class="col-lg-6">
														<input type="hidden" name="gambar" value="">
														<input type="file" class="form-control" accept=".jpg,.jpeg,.png" name="gambar_ubah" id="gambar_ubah" onchange="checkPhotoUbah(this)"/>
													</div>
													<div id="warning_gambar_ubah" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Status</label>
													</div>
													<div class="col-lg-6">
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
													<div id="warning_status_ubah" class="col-lg-3 text-danger"></div>
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
					
					if($akses["scan"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_scan" tabindex="-1" role="dialog" aria-labelledby="label_modal_scan" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_scan" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_scan">Scan <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="aksi" value="scan"/>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Kode Scan</label>
													</div>
													<div class="col-lg-6">
														<input class="form-control" name="kode_scan" value="" placeholder="Kode Scan">
													</div>
													<div id="warning_kode_scan" class="col-lg-3 text-danger"></div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary" onclick="return cek_simpan_scan()">Simpan</button>
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

		<script>
			function getEndDate(){
				var start_date = $('#start_date').val();			
				document.getElementById('end_date').setAttribute("min", start_date);			
			} 
		</script>
