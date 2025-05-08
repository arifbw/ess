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
					
					if($this->akses["lihat"]){
				?>

						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_pengatauran_poh">
								<thead>
									<tr>
										<th class='text-center'>Kelompok Jabatan</th>
										<th class='text-center'>POH Berdasarkan Kelompok Jabatan</th>
										<th class='text-center'>POH Berdasarkan Pangkat</th>
										<?php
											if($akses["ubah"] or $akses["lihat_log"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_pengaturan_poh);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td>";
													echo $daftar_pengaturan_poh[$i]["kode_kelompok_jabatan"]." - ".$daftar_pengaturan_poh[$i]["nama_kelompok_jabatan"];
												echo "</td>";
												echo "<td>".$daftar_pengaturan_poh[$i]["kelompok_jabatan_poh"]."</td>";
												echo "<td>".$daftar_pengaturan_poh[$i]["pangkat_poh"]."</td>";
												
												if($akses["ubah"]){
													echo "<td class='text-center'>";
														if($akses["ubah"]){
															echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button> ";
														}
														
														if($akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_pengaturan_poh[$i]["nama_kelompok_jabatan"]."\",".$daftar_pengaturan_poh[$i]["kode_kelompok_jabatan"].")'>Lihat Log</button>";
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
							<div class="modal-dialog modal-lg">
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
													<div class="col-lg-2">
														<label>Jabatan</label>
													</div>
													<div class="col-lg-6">
														<label> : <span id="jabatan"/></span></label>
														<input type="hidden" id="kode_kelompok_jabatan" name="kode_kelompok_jabatan"/>
														<input type="hidden" id="nama_kelompok_jabatan" name="nama_kelompok_jabatan"/>
													</div>
													<div id="warning_nama_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_pengatauran_poh">
													<thead>
														<tr>
															<th class='text-center' width='55%'>Kelompok Jabatan</th>
															<th class='text-center' width='45%'>Pangkat</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td>
																<?php
																	$count_daftar_kelompok_jabatan = count($daftar_kelompok_jabatan);
																	$baris = ceil($count_daftar_kelompok_jabatan/2);
																	
																	echo "<div class='row'>";
																			for($i=0;$i<$count_daftar_kelompok_jabatan;$i++){
																				if($i==0 or $i==$baris){
																					echo "<div class='col-lg-6'>";
																				}
																					echo "<label><input type='checkbox' name='kelompok_jabatan[]' value='".$daftar_kelompok_jabatan[$i]["id"]."'/> ".$daftar_kelompok_jabatan[$i]["nama_kelompok_jabatan"]."</label>";
																				if($i==$baris-1 or $i==$count_daftar_kelompok_jabatan-1){
																					echo "</div>";
																				}
																				else{
																					echo "<br>";
																				}
																			}
																		echo "</div>";
																	echo "</div>";
																?>
															</td>
															<td>
																<?php
																	$count_daftar_pangkat = count($daftar_pangkat);
																	$baris = ceil($count_daftar_pangkat/2);
																	
																	echo "<div class='row'>";
																			for($i=0;$i<$count_daftar_pangkat;$i++){
																				if($i==0 or $i==$baris){
																					echo "<div class='col-lg-6'>";
																				}
																					echo "<label><input type='checkbox' name='pangkat[]' value='".$daftar_pangkat[$i]["id"]."'/> ".$daftar_pangkat[$i]["nama_pangkat"]."</label>";
																				if($i==$baris-1 or $i==$count_daftar_pangkat-1){
																					echo "</div>";
																				}
																				else{
																					echo "<br>";
																				}
																			}
																		echo "</div>";
																	echo "</div>";
																?>
															</td>
														</tr>
													</tbody>
												</table>
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