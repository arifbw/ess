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
					if($this->akses["lihat"]){
				?>

						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_posisi_menu">
								<thead>
									<tr>
										<th class='text-center'>Nama</th>
										<th class='text-center'>Shortcode</th>
										<?php
											if($akses["ubah"] or $akses["lihat log"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_posisi_menu);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td>".$daftar_posisi_menu[$i]["nama"]."</td>";
												echo "<td>".$daftar_posisi_menu[$i]["shortcode"]."</a></td>";
												if($akses["ubah"] or $akses["lihat log"]){
													echo "<td class='text-center'>";
														if($akses["ubah"]){
															echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button> ";
														}
														if($akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_log' onclick='tampil_log(\"$nama_kelompok_modul > $judul\",\"$id_modul\",\"".$daftar_posisi_menu[$i]["id"]."\")'>Log</button>";
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
													<div class="col-lg-4">
														<label>Nama <?php echo $judul;?></label>
													</div>
													<div class="col-lg-4">
														<input type="hidden" name="nama" value="">
														<input class="form-control" name="nama_ubah" value="" placeholder="Nama <?php echo $judul;?>">
													</div>
													<div id="warning_nama_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-4">
														<label>Shortcode</label>
													</div>
													<div class="col-lg-4">
														<input type="hidden" name="shortcode" value="">
														<input class="form-control" name="shortcode_ubah" value="" placeholder="Shortcode">
													</div>
													<div id="warning_shortcode_ubah" class="col-lg-4 text-danger"></div>
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
					
					if($akses["lihat log"]){
				?>				
						<!-- Modal -->
						<div class="modal fade" id="modal_log" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_ubah" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_log"></h4>
										</div>
										<div class="modal-body">
											<div id="isi_log"></div>
										</div>
										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
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