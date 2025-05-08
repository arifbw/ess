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
															<label>Salin dari</label>
														</div>
														<div class="col-lg-7">
															<div class="form-group">
																<select class="form-control" name="modul" onchange='salin_tambah(this)'>
																	<option value=''>--- Pilih Jadwal Kerja ---</option>
																	<?php
																		for($i=0;$i<count($daftar_jadwal_kerja);$i++){
																			echo "<option value='".$daftar_jadwal_kerja[$i]["id"]."'>";
																				echo $daftar_jadwal_kerja[$i]["dws"];
																				if(!empty($daftar_jadwal_kerja[$i]["dws_variant"])){
																					echo "-".$daftar_jadwal_kerja[$i]["dws_variant"];
																				}
																				echo " : ".$daftar_jadwal_kerja[$i]["description"];
																			echo "</option>";
																		}
																	?>
																</select>
															</div>
														</div>
														<!--<div id="warning_nama" class="col-lg-3 text-danger"></div>-->
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
															<label>Hari Kerja / Libur</label>
														</div>
														<div class="col-lg-7">
															<?php
																$style_display = "";
																
																if(strcmp(gettype($hari),"boolean")==0 and !$hari){
																	$checked="checked=checked";
																}
																else{
																	$style_display = "style='display:none'";
																	$checked="";
																}
																echo "<label class='radio-inline'>";
																	echo "<input type='radio' name='hari' id='hari_kerja' value='0' onclick='hari_kerja_libur(this)' $checked> Kerja";
																echo "</label>";
																
																if(strcmp(gettype($hari),"boolean")==0 and $hari){
																	$checked="checked=checked";
																}
																else{
																	$checked="";
																}
																echo "<label class='radio-inline'>";
																	echo "<input type='radio' name='hari' id='hari_libur' value='1' onclick='hari_kerja_libur(this)'> Libur";
																echo "</label>";
															?>
														</div>
														<div id="warning_hari" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div id="waktu_hari_kerja" <?php echo $style_display;?>>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Formasi Gilir</label>
															</div>
															<div class="col-lg-7">
															
															<?php																
																if(strcmp(gettype($formasi_gilir),"boolean")==0 and !$formasi_gilir){
																	$checked="checked=checked";
																}
																else{
																	$checked="";
																}
																echo "<label class='radio-inline'>";
																	echo "<input type='radio' name='formasi_gilir' id='formasi_gilir_non_gilir' value='0' $checked> Non-Gilir";
																echo "</label>";
																
																if(strcmp(gettype($formasi_gilir),"boolean")==0 and $formasi_gilir){
																	$checked="checked=checked";
																}
																else{
																	$checked="";
																}
																echo "<label class='radio-inline'>";
																	echo "<input type='radio' name='formasi_gilir' id='formasi_gilir_gilir' value='1'> Gilir";
																echo "</label>";
															?>
															
															</div>
															<div id="warning_formasi_gilir" class="col-lg-3 text-danger"></div>
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Jam Masuk</label>
															</div>
															<div class="col-lg-2">
																<?php
																	if(strcmp(gettype($lintas_hari_masuk),"boolean")==0 and $lintas_hari_masuk){
																		$checked="checked=checked";
																	}
																	else{
																		$style_display = "style='display:none'";
																		$checked="";
																	}
																	echo "<label class='checkbox-inline'>";
																		echo "<input type='checkbox' name='lintas_hari_masuk' onchange='lintas_hari(this)'> Lintas Hari";
																	echo "</label>";
																?>

															</div>
															<div class="col-lg-5">
																<input type="time" class="form-control" name="jam_masuk" value="<?php echo $jam_masuk;?>" placeholder="Jam Masuk">
															</div>
															<div id="warning_jam_masuk" class="col-lg-3 text-danger"></div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Istirahat</label>
															</div>
															<div class="col-lg-7">
																<?php
																	$style_display = "";
																	
																	if(strcmp($istirahat,"terjadwal")==0){
																		$checked="checked=checked";
																	}
																	else{
																		$style_display = "style='display:none'";
																		$checked="";
																	}
																	echo "<label class='radio-inline'>";
																		echo "<input type='radio' name='istirahat' id='istirahat_terjadwal' value='terjadwal' onclick='waktu_istirahat(this)' $checked> Terjadwal";
																	echo "</label>";
																	
																	if(strcmp($istirahat,"bergantian")==0){
																		$checked="checked=checked";
																	}
																	else{
																		$checked="";
																	}
																	
																	echo "<label class='radio-inline'>";
																		echo "<input type='radio' name='istirahat' id='istirahat_bergantian' value='bergantian' onclick='waktu_istirahat(this)' $checked> Bergantian";
																	echo "</label>";
																?>
															</div>
															<div id="warning_istirahat" class="col-lg-3 text-danger"></div>
														</div>
													</div>
													<div id="waktu_istirahat" <?php echo $style_display;?>>
														<div class="row">
															<div class="form-group">
																<div class="col-lg-2">
																</div>
																<div class="col-lg-2">
																	<label>Jam Mulai Istirahat</label>
																</div>
																<div class="col-lg-2">
																	<?php
																		if(strcmp(gettype($lintas_hari_mulai_istirahat),"boolean")==0 and $lintas_hari_mulai_istirahat){
																			$checked="checked=checked";
																		}
																		else{
																			$style_display = "style='display:none'";
																			$checked="";
																		}
																	?>
																	<label class="checkbox-inline">
																		<input type="checkbox" name='lintas_hari_mulai_istirahat' onchange='lintas_hari(this)'<?php echo $checked?>> Lintas Hari
																	</label>
																</div>
																<div class="col-lg-3">
																	<input type='time' class="form-control" name="jam_mulai_istirahat" value="<?php echo $jam_mulai_istirahat;?>" placeholder="Jam Mulai Istirahat">
																</div>
																<div id="warning_jam_mulai_istirahat" class="col-lg-3 text-danger"></div>
															</div>
														</div>
														<div class="row">
															<div class="form-group">
																<div class="col-lg-2">
																</div>
																<div class="col-lg-2">
																	<label>Jam Akhir Istirahat</label>
																</div>
																<div class="col-lg-2">
																	<?php
																		if(strcmp(gettype($lintas_hari_akhir_istirahat),"boolean")==0 and $lintas_hari_mulai_istirahat){
																			$checked="checked=checked";
																		}
																		else{
																			$style_display = "style='display:none'";
																			$checked="";
																		}
																	?>
																	<label class="checkbox-inline">
																		<input type="checkbox" name='lintas_hari_akhir_istirahat' onchange='lintas_hari(this)'<?php echo $checked?>> Lintas Hari
																	</label>
																</div>
																<div class="col-lg-3">
																	<input type='time' class="form-control" name="jam_akhir_istirahat" value="<?php echo $jam_akhir_istirahat;?>" placeholder="Jam Akhir Istirahat">
																</div>
																<div id="warning_jam_akhir_istirahat" class="col-lg-3 text-danger"></div>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Jam Pulang</label>
															</div>
															<div class="col-lg-2">
																<?php
																	if(strcmp(gettype($lintas_hari_pulang),"boolean")==0 and $lintas_hari_pulang){
																		$checked="checked=checked";
																	}
																	else{
																		$style_display = "style='display:none'";
																		$checked="";
																	}
																?>
																<label class="checkbox-inline">
																	<input type="checkbox" name='lintas_hari_pulang' onchange='lintas_hari(this)' <?php echo $checked;?>> Lintas Hari
																</label>
															</div>
															<div class="col-lg-5">
																<input type='time' class="form-control" name="jam_pulang" value="<?php echo $jam_pulang;?>" placeholder="Jam Pulang">
															</div>
															<div id="warning_jam_pulang" class="col-lg-3 text-danger"></div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Kode ERP</label>
														</div>
														<div class="col-lg-6">
															<input class="form-control" name="kode_erp" value="<?php echo $kode_erp;?>" placeholder="Kode ERP">
														</div>
														<div class="col-lg-1">
															<label class='checkbox-inline'>
																<input type='checkbox' name='varian' id='varian_tambah'/> Varian
															</label>
														</div>
														<div id="warning_kode_erp" class="col-lg-3 text-danger"></div>
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
					
					if($this->akses["lihat"]){
				?>
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_jadwal_kerja">
								<thead>
									<tr>
										<th class='text-center'>Kode ERP</th>
										<th class='text-center'>Nama</th>
										<th class='text-center'>Hari Kerja / Libur</th>
										<th class='text-center'>Gilir</th>
										<th class='text-center'>Jam Masuk</th>
										<th class='text-center'>Jam Mulai Istirahat</th>
										<th class='text-center'>Jam Akhir Istirahat</th>
										<th class='text-center'>Jam Pulang</th>
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
										for($i=0;$i<count($daftar_jadwal_kerja);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td>";
													echo $daftar_jadwal_kerja[$i]["dws"];
													if(!empty($daftar_jadwal_kerja[$i]["dws_variant"])){
														echo "-".$daftar_jadwal_kerja[$i]["dws_variant"];
													}
												echo "</td>";
												echo "<td>".$daftar_jadwal_kerja[$i]["description"]."</td>";
												echo "<td class='text-center'>";
													if((int)$daftar_jadwal_kerja[$i]["libur"]==1){
														echo "Hari Libur";
													}
													else{
														echo "Hari Kerja";
													}
												echo "</td>";
												echo "<td class='text-center'>";
													if((int)$daftar_jadwal_kerja[$i]["gilir"]==1){
														echo "Gilir";
													}
													else{
														echo "Non-Gilir";
													}
												echo "</td>";
												echo "<td class='text-center'>".substr($daftar_jadwal_kerja[$i]["dws_start_time"],0,5)."</td>";
												echo "<td class='text-center'>".substr($daftar_jadwal_kerja[$i]["dws_break_start_time"],0,5)."</td>";
												echo "<td class='text-center'>".substr($daftar_jadwal_kerja[$i]["dws_break_end_time"],0,5)."</td>";
												echo "<td class='text-center'>".substr($daftar_jadwal_kerja[$i]["dws_end_time"],0,5)."</td>";
												echo "<td class='text-center'>";
													if((int)$daftar_jadwal_kerja[$i]["status"]==1){
														echo "Aktif";
													}
													else if((int)$daftar_jadwal_kerja[$i]["status"]==0){
														echo "Non Aktif";
													}
												echo "</td>";
												if($akses["ubah"] or $akses["lihat log"]){
													echo "<td class='text-center'>";
														if($akses["ubah"]){
															echo "<input type='hidden' name='id_jadwal_kerja_$i' value='".$daftar_jadwal_kerja[$i]["id"]."'/>";
															echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='ubah(this)'>Ubah</button> ";
														}
														if($akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_jadwal_kerja[$i]["description"]."\",".$daftar_jadwal_kerja[$i]["id"].")'>Lihat Log</button>";
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
											<div id='isian_ubah_jadwal'></div>
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