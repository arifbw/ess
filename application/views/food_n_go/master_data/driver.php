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
															<label>NP</label>
														</div>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="np_karyawan" value="<?php echo @$np_karyawan;?>" placeholder="No Pokok" required>
														</div>
														<div id="warning_np_karyawan" class="col-lg-3 text-danger"></div>
													</div>
												</div>
                                                
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Nama</label>
														</div>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="nama" value="<?php echo @$nama;?>" placeholder="Nama" required>
														</div>
														<div id="warning_nama" class="col-lg-3 text-danger"></div>
													</div>
												</div>
                                                
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Nomor HP</label>
														</div>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="no_hp" value="<?php echo @$no_hp;?>" placeholder="Nomor HP" required>
														</div>
														<div id="warning_no_hp" class="col-lg-3 text-danger"></div>
													</div>
												</div>
                                                
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Jenis SIM</label>
														</div>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="jenis_sim" value="<?php echo @$jenis_sim;?>" placeholder="Jenis SIM: A/B I/B II/C/D/Lainnya" required>
														</div>
														<div id="warning_jenis_sim" class="col-lg-3 text-danger"></div>
													</div>
												</div>
                                                
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Keterangan</label>
														</div>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="keterangan" value="<?php echo @$keterangan;?>" placeholder="Keterangan">
														</div>
														<div id="warning_keterangan" class="col-lg-3 text-danger"></div>
													</div>
												</div>
                                                
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Posisi</label>
														</div>
														<div class="col-lg-7">
															<input type="text" class="form-control" name="posisi" value="<?php echo @$posisi;?>" placeholder="Jakarta / Karawang" required>
														</div>
														<div id="warning_posisi" class="col-lg-3 text-danger"></div>
													</div>
												</div>
                                                
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Pilih Kendaraan</label>
														</div>
														<div class="col-lg-7">
															<select class="form-control select2" name="id_mst_kendaraan_default" required>
                                                                <?php foreach($mst_kendaraan as $r){?>
                                                                <option value="<?= $r['id']?>"><?= $r['nopol'].' - '.$r['nama']?></option>
                                                                <?php } ?>
                                                            </select>
														</div>
														<div id="warning_kendaraan" class="col-lg-3 text-danger"></div>
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
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_driver">
								<thead>
									<tr>
										<th class='text-center'>#</th>
										<th class='text-center'>Nama</th>
										<th class='text-center'>No. HP</th>
										<th class='text-center'>Jenis SIM</th>
										<th class='text-center'>Lokasi</th>
										<th class='text-center'>Keterangan</th>
										<th class='text-center'>Kendaraan</th>
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
										for($i=0;$i<count($daftar_driver);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td align='center'>".($i+1)."</td>";
												echo "<td>".$daftar_driver[$i]["np_karyawan"].' - '.$daftar_driver[$i]["nama"]."</td>";
												echo "<td>".$daftar_driver[$i]["no_hp"]."</td>";
												echo "<td>".$daftar_driver[$i]["jenis_sim"]."</td>";
												echo "<td>".$daftar_driver[$i]["posisi"]."</td>";
												echo "<td>".$daftar_driver[$i]["keterangan"]."</td>";
												echo "<td>".$daftar_driver[$i]["kendaraan"]."</td>";
												echo "<td class='text-center'>";
													if((int)$daftar_driver[$i]["status"]==1){
														echo "Aktif";
													}
													else if((int)$daftar_driver[$i]["status"]==0){
														echo "Non Aktif";
													}
												echo "</td>";
												if(@$akses["ubah"] or @$akses["lihat log"]){
													echo "<td class='text-center'>";
														if(@$akses["ubah"]){
															echo "<button type='button' class='btn btn-primary btn-xs' 
                                                            data-id='".$daftar_driver[$i]["id"]."'
                                                            data-np_karyawan='".$daftar_driver[$i]["np_karyawan"]."'
                                                            data-nama='".$daftar_driver[$i]["nama"]."'
                                                            data-no_hp='".$daftar_driver[$i]["no_hp"]."'
                                                            data-jenis_sim='".$daftar_driver[$i]["jenis_sim"]."'
                                                            data-posisi='".$daftar_driver[$i]["posisi"]."'
                                                            data-keterangan='".$daftar_driver[$i]["keterangan"]."'
                                                            data-id_mst_kendaraan='".$daftar_driver[$i]["id_mst_kendaraan_default"]."'
                                                            data-nopol='".$daftar_driver[$i]["nopol"]."'
                                                            data-kendaraan='".$daftar_driver[$i]["kendaraan"]."'
                                                            data-status='".$daftar_driver[$i]["status"]."'
                                                            onclick='tampil_data_ubah_new(this)'>Ubah</button> ";
														}
														if(@$akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_driver[$i]["nama"]."\",".$daftar_driver[$i]["id"].")'>Lihat Log</button>";
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
									<form role="form" action="<?= base_url('food_n_go/master_data/driver')?>" id="formulir_ubah" method="post">
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
														<label>NP</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="np_karyawan_old" id="np_karyawan_old" value="">
														<input type="text" class="form-control" name="np_karyawan_ubah" id="np_karyawan_ubah" value="" placeholder="Nama" readonly>
													</div>
													<div id="warning_np_karyawan_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
                                            
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Nama</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="nama_old" id="nama_old" value="">
														<input type="text" class="form-control" name="nama_ubah" id="nama_ubah" value="" placeholder="Nama" required>
													</div>
													<div id="warning_nama_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
                                            
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Nomor HP</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="no_hp_old" id="no_hp_old" value="">
														<input type="text" class="form-control" name="no_hp_ubah" id="no_hp_ubah" value="" placeholder="Nomor HP" required>
													</div>
													<div id="warning_no_hp_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
                                            
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Jenis SIM</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="jenis_sim_old" id="jenis_sim_old" value="">
														<input type="text" class="form-control" name="jenis_sim_ubah" id="jenis_sim_ubah" value="" placeholder="Jenis SIM: A/B I/B II/C/D/Lainnya" required>
													</div>
													<div id="warning_jenis_sim_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
                                            
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Keterangan</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="keterangan_old" id="keterangan_old" value="">
														<input type="text" class="form-control" name="keterangan_ubah" id="keterangan_ubah" value="" placeholder="Keterangan" required>
													</div>
													<div id="warning_keterangan_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
                                            
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Posisi</label>
													</div>
													<div class="col-lg-5">
														<input type="hidden" name="posisi_old" id="posisi_old" value="">
														<input type="text" class="form-control" name="posisi_ubah" id="posisi_ubah" value="" placeholder="Jakarta / Karawang" required>
													</div>
													<div id="warning_posisi_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
                                            
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-lg-3">
                                                        <label>Pilih Kendaraan</label>
                                                    </div>
                                                    <div class="col-lg-5">
                                                        <select class="form-control select2" name="id_mst_kendaraan_default_ubah" id="id_mst_kendaraan_default_ubah" required>
                                                            <?php foreach($mst_kendaraan as $r){?>
                                                            <option value="<?= $r['id']?>"><?= $r['nopol'].' - '.$r['nama']?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div id="warning_kendaraan_ubah" class="col-lg-4 text-danger"></div>
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