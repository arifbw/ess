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
					if(!empty($success) || @$this->session->flashdata('success')){
				?>
						<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $success;?>
							<?php echo $this->session->flashdata('success'); ?>
						</div>
				<?php
					}
					if(!empty($warning || @$this->session->flashdata('warning'))){
				?>
						<div class="alert alert-danger alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $warning;?>
							<?php echo $this->session->flashdata('warning'); ?>
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
															<label>Jabatan</label>
														</div>
														<div class="col-lg-6">
															<div class="form-group">
																<select class="form-control select2" name="jabatan" id="jabatan" style="width:100%" onchange="pilih_jabatan('tambah');">
																	<option value=''>--- Pilih Jabatan yang akan di-POH-kan ---</option>
																	<?php
																		$optgroup = "";
																		for($i=0;$i<count($daftar_jabatan_struktural);$i++){
																			if(strcmp($optgroup,$daftar_jabatan_struktural[$i]["kode_unit"]." - ".$daftar_jabatan_struktural[$i]["nama_unit"])!=0){
																				$optgroup=$daftar_jabatan_struktural[$i]["kode_unit"]." - ".$daftar_jabatan_struktural[$i]["nama_unit"];
																				echo "<optgroup label='$optgroup'>";
																			}
																			
																			if(strcmp($kode_jabatan,$daftar_jabatan_struktural[$i]["kode_jabatan"])==0){
																				$selected="selected='selected'";
																			}
																			else{
																				$selected="";
																			}
																			
																			echo "<option value='".$daftar_jabatan_struktural[$i]["kode_jabatan"]."' $selected>".$daftar_jabatan_struktural[$i]["kode_jabatan"]." - ".$daftar_jabatan_struktural[$i]["nama_jabatan"]."</option>";
																			
																			if($i==count($daftar_jabatan_struktural)-1 or strcmp($optgroup,$daftar_jabatan_struktural[$i+1]["kode_unit"]." - ".$daftar_jabatan_struktural[$i+1]["nama_unit"])!=0){
																				echo "</optgroup>";
																			}
																		}
																	?>
																</select>
															</div>
														</div>
														<div id="warning_jabatan" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Pejabat Definitif</label>
														</div>
														<div class="col-lg-6">
															<span id="pejabat_definitif"></span>
															<input type="hidden" id="np_definitif" name="np_definitif" value=""/>
															<input type="hidden" id="nama_definitif" name="nama_definitif" value=""/>
														</div>
														<div id="warning_pejabat_definitif" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Nama Karyawan POH</label>
														</div>
														<div class="col-lg-9">
															<div class="row">
																<div class="col-lg-6">
																	<label>
																		<input type="radio" name="sesuai_skep" id="sesuai_skep" value="sesuai" onchange="pilih_jabatan('tambah')" checked> Sesuai <?php echo $skep?>
																	</label>
																</div>
																<div class="col-lg-6">
																	<label>
																		<input type="radio" name="sesuai_skep" id="tidak_sesuai_skep" value="tidak_sesuai" onchange="pilih_jabatan('tambah')"> Tidak Sesuai <?php echo $skep?>
																	</label>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-3"></div>
													<div class="col-lg-6">
														<div class="form-group">
															<select class="form-control select2" name="karyawan" id="karyawan" style="width:100%">
																<option value=''>--- Pilih Karyawan yang akan dijadikan POH ---</option>
															</select>
															<?php
																if(isset($_POST["karyawan"])){
																	echo "<input id='karyawan_poh' name='karyawan_poh' type='hidden' value='".$_POST["karyawan"]."'>";
																}
															?>
														</div>
													</div>
													<div id="warning_karyawan" class="col-lg-3 text-danger"></div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Periode</label>
														</div>
														<div class="col-lg-2">
															<input class="form-control" id="tanggal_mulai" name="tanggal_mulai" type="date" onchange="pilih_tanggal_mulai()" placeholder="tanggal mulai" value="<?php echo $tanggal_mulai;?>">
														</div>
														<div class="col-lg-2 text-center">
															sampai dengan
														</div>
														<div class="col-lg-2">
															<input class="form-control" id="tanggal_selesai" name="tanggal_selesai" type="date" onchange="pilih_tanggal_selesai()" placeholder="tanggal selesai" value="<?php echo $tanggal_selesai;?>">
														</div>
														<div id="warning_periode" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Nomor Nota Dinas</label>
														</div>
														<div class="col-lg-6">
															<input class="form-control" id="nomor_nota_dinas" name="nomor_nota_dinas" placeholder="Nomor Nota Dinas" value="<?php echo $nomor_nota_dinas;?>">
														</div>
														<div id="warning_nomor_nota_dinas" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Keterangan</label>
														</div>
														<div class="col-lg-6">
															<input class="form-control" id="keterangan" name="keterangan" placeholder="Keterangan" value="<?php echo $keterangan;?>">
														</div>
														<div id="warning_keterangan" class="col-lg-3 text-danger"></div>
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
							<div class="col-lg-2">
								<div class="form-group">
									<select class="form-control select2" name="display_poh" id="display_poh" onchange="table_serverside()">
										<option value='semua'>Semua POH</option>
										<option value='hari ini'>POH Hari Ini</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_poh">
								<thead>
									<tr>
										<th class='text-center'>No</th>
										<th class='text-center'>Waktu</th>
										<th class='text-center'>Unit Kerja</th>
										<th class='text-center'>Jabatan</th>
										<th class='text-center'>Pejabat Definitif</th>
										<th class='text-center'>POH</th>
										<th class='text-center'>Nota Dinas</th>
										<th class='text-center'>Keterangan</th>
										<?php
											if($akses["ubah"] or $akses["lihat log"] or $akses["hapus"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
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
													<label>Jabatan</label>
												</div>
												<div class="col-lg-9">
													<div class="form-group">
														<input type="hidden" name="jabatan" value="">
														<select class="form-control select2" name="jabatan_ubah" id="jabatan_ubah" style="width:100%" onchange="pilih_jabatan('ubah');">
															<option value=''>--- Pilih Jabatan yang akan di-POH-kan ---</option>
															<?php
																$optgroup = "";
																for($i=0;$i<count($daftar_jabatan_struktural);$i++){
																	if(strcmp($optgroup,$daftar_jabatan_struktural[$i]["kode_unit"]." - ".$daftar_jabatan_struktural[$i]["nama_unit"])!=0){
																		$optgroup=$daftar_jabatan_struktural[$i]["kode_unit"]." - ".$daftar_jabatan_struktural[$i]["nama_unit"];
																		echo "<optgroup label='$optgroup'>";
																	}
																	
																	echo "<option value='".$daftar_jabatan_struktural[$i]["kode_jabatan"]."'>".$daftar_jabatan_struktural[$i]["kode_jabatan"]." - ".$daftar_jabatan_struktural[$i]["nama_jabatan"]."</option>";
																	
																	if($i==count($daftar_jabatan_struktural)-1 or strcmp($optgroup,$daftar_jabatan_struktural[$i+1]["kode_unit"]." - ".$daftar_jabatan_struktural[$i+1]["nama_unit"])!=0){
																		echo "</optgroup>";
																	}
																}
															?>
														</select>
													</div>
													<span id="warning_jabatan_ubah" class="col-lg-9 text-danger"></span>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-3">
													<label>Pejabat Definitif</label>
												</div>
												<div class="col-lg-9">
													<span id="pejabat_definitif_ubah"></span>
													<input type="hidden" id="np_definitif_ubah" name="np_definitif_ubah" value=""/>
													<input type="hidden" id="nama_definitif_ubah" name="nama_definitif_ubah" value=""/>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-3">
													<label>Nama Karyawan POH</label>
												</div>
												<div class="col-lg-9">
													<div class="row">
														<div class="col-lg-6">
															<label>
																<input type="radio" name="sesuai_skep" id="sesuai_skep" value="sesuai" onchange="pilih_jabatan('ubah')" checked> Sesuai <?php echo $skep?>
															</label>
														</div>
														<div class="col-lg-6">
															<label>
																<input type="radio" name="sesuai_skep" id="tidak_sesuai_skep" value="tidak_sesuai" onchange="pilih_jabatan('ubah')"> Tidak Sesuai <?php echo $skep?>
															</label>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-3">
												</div>
												<div class="col-lg-9">
													<div class="form-group">
														<input type="hidden" id="karyawan_poh_ubah" name="karyawan_poh_ubah" value=""/>
														<select class="form-control select2" name="karyawan_ubah" id="karyawan_ubah" style="width:100%">
															<option value=''>--- Pilih Karyawan yang akan dijadikan POH ---</option>
														</select>
													</div>
												</div>
												<span class="col-lg-3"></span>
												<span id="warning_karyawan_ubah" class="col-lg-9 text-danger"></span>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-3">
													<label>Periode</label>
												</div>
												<div class="col-lg-4">
													<input type="hidden" name="tanggal_mulai"/>
													<input class="form-control" id="tanggal_mulai_ubah" name="tanggal_mulai_ubah" type="date" onchange="pilih_tanggal_mulai('ubah')" value=""/>
												</div>
												<div class="col-lg-1">
													s/d
												</div>
												<div class="col-lg-4">
													<input type="hidden" name="tanggal_selesai">
													<input class="form-control" id="tanggal_selesai_ubah" name="tanggal_selesai_ubah" type="date" onchange="pilih_tanggal_selesai('ubah')" value=""/>
												</div>
												<span class="col-lg-3"></span>
												<span id="warning_periode_ubah" class="col-lg-9 text-danger"></span>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-3">
													<label>Nomor Nota Dinas</label>
												</div>
												<div class="col-lg-5">
													<input type="hidden" name="nomor_nota_dinas"/>
													<input class="form-control" id="nomor_nota_dinas_ubah" name="nomor_nota_dinas_ubah" placeholder="Nomor Nota Dinas"/>
												</div>
												<div id="warning_nota_dinas_ubah" class="col-lg-4 text-danger">
												</div>
											</div>
										</div>
										<div class="row">
											<div class="form-group">
												<div class="col-lg-3">
													<label>Keterangan</label>
												</div>
												<div class="col-lg-5">
													<input type="hidden" name="keterangan"/>
													<input class="form-control" id="keterangan_ubah" name="keterangan_ubah" placeholder="Keterangan"/>
												</div>
												<div id="warning_keterangan_ubah" class="col-lg-4 text-danger">
												</div>
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
				
				<?php
					if($akses["hapus"]){
				?>
				<!--begin: Modal Inactive -->
		      	<div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			        <div class="modal-dialog modal-sm" role="document">
			          	<div class="modal-content">
			            	<div class="modal-header">
			              		<h5 class="modal-title text-danger" id="title-inactive">
			                		<b>Hapus <?= $judul ?></b>
			              		</h5>
			              		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                		<span aria-hidden="true">&times;</span>
			              		</button>
			            	</div>
			            
			            	<div class="modal-body">
			              		<h6 id="message-inactive"></h6>
			            	</div>
			            	<div class="modal-footer">
			              		<button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
			              		<a href="" id="inactive-action" class="btn btn-primary">Ya, Hapus</a>
			            	</div>
			          	</div>
			        </div>
		      	</div>
				
				<?php
					}
				?>
				
				
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->