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
						echo "<input type='hidden' id='akses_ubah_grup' name='akses_ubah_grup' value='".(int)$this->akses["ubah"]."'/>";
				?>

						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_pengadministrasi_unit_kerja">
								<thead>
									<tr>
										<th class='text-center'>Username</th>
										<th class='text-center'>Nomor Pokok</th>
										<th class='text-center'>Nama</th>
										<th class='text-center'>Pengadministrasi Unit Kerja</th>
										<?php
											if($akses["ubah"] or $akses["menu"] or $akses["hak akses"] or $akses["lihat log"]){
												echo "<th class='text-center'>Aksi</th>";
											}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
										for($i=0;$i<count($daftar_pengguna_pengadministrasi);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td>".$daftar_pengguna_pengadministrasi[$i]["username"]."</td>";
												echo "<td>".$daftar_pengguna_pengadministrasi[$i]["no_pokok"]."</td>";
												echo "<td>".$daftar_pengguna_pengadministrasi[$i]["nama"]."</td>";
												echo "<td>";
													if(strlen($daftar_pengguna_pengadministrasi[$i]["nama_unit"])>0){
														echo "<ul>";
															echo "<li>".str_replace("|","</li><li>",$daftar_pengguna_pengadministrasi[$i]["nama_unit"])."</li>";
														echo "</ul>";
													}
												"</td>";
												if($akses["ubah"] or $akses["lihat log"]){
													echo "<td class='text-center'>";
														if($akses["ubah"]){
															echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_pengadministrasi' onclick='pengadministrasi(this)'>Ubah</button> ";
														}
														if($akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_pengguna_pengadministrasi[$i]["nama"]."\",".$daftar_pengguna_pengadministrasi[$i]["id"].")'>Lihat Log</button>";
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
						<div class="modal fade" id="modal_pengadministrasi" tabindex="-1" role="dialog" aria-labelledby="label_modal_grup" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post" id="formulir_ubah_grup">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_unit_kerja">Pengadministrasi Unit Kerja : <span id='username_pengadministrasi'></span></h4>
										</div>
										<div class="modal-body">
											<div id='isi_modal_pengadministrasi'></div>
										</div>
										<div class="modal-footer">
											<?php
												if($akses["ubah"]){
													echo "<button type='button' onclick='cek_pengadministrasi();' class='btn btn-primary'>Simpan</button>";
												}
											?>
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