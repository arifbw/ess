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
					if($akses["lihat"]){
				?>
				
						<form role="form" action="" id="formulir_simpan" method="post">
							<div class="row">
								<?php
									echo "<input type='hidden' name='id_grup_pengguna' value='$id_grup_pengguna'>";
								?>
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_grup_pengguna">
									<thead>
										<tr>
											<th class='text-center'>Posisi</th>
											<th class='text-center'>Master Menu</th>
											<?php
												if($akses["lihat log"]){
													echo "<th class='text-center'>Aksi</th>";
												}
											?>
										</tr>
									</thead>
									<tbody>
										<?php
											if(!$akses["ubah"]){
												$disabled = "disabled";
											}
											else{
												$disabled = "";
											}
											
											for($i=0;$i<count($daftar_menu_grup_pengguna);$i++){
												if($i%2==0){
													$class = "even";
												}
												else{
													$class = "odd";
												}
												
												echo "<tr class='$class'>";
													echo "<td>";
														echo $daftar_menu_grup_pengguna[$i]["nama_posisi_menu"];
														echo "<input type='hidden' name='id_posisi_menu[]' value='".$daftar_menu_grup_pengguna[$i]["id_posisi_menu"]."'>";
													echo "</td>";
													echo "<td>";
														echo "<div class='form-group'>";
															echo "<select class='form-control' name='id_master_menu[]' $disabled>";
																echo "<option value=''>--- Pilih Master Menu ---</option>";
																for($j=0;$j<count($daftar_master_menu);$j++){
																	$selected = "";
																	if((int)$daftar_menu_grup_pengguna[$i]["id_master_menu"]==(int)$daftar_master_menu[$j]["id"]){
																		$selected = "selected='selected'";
																	}
																	echo "<option value='".$daftar_master_menu[$j]["id"]."' $selected>".$daftar_master_menu[$j]["nama"]."</option>";
																}
															echo "</select>";
														echo "</div>";
													echo "</td>";
													if($akses["lihat log"]){
														echo "<td class='text-center'>";
															echo "<button class='btn btn-primary btn-xs' onclick='clean_form(this);lihat_log(\"".$daftar_menu_grup_pengguna[$i]["nama_posisi_menu"]."\",".$daftar_menu_grup_pengguna[$i]["id"].")'>Lihat Log</button>";
														echo "</td>";
													}
												echo "</tr>";
											}
										?>
									</tbody>
								</table>
								<!-- /.table-responsive -->
							</div>
							<div class="row">
								<div class="col-lg-12 text-center">
									<?php
										if($akses["ubah"]){
											echo "<button type='submit' class='btn btn-primary'>Simpan</button>";
										}
									?>
									<a href="<?php echo base_url($url_grup_pengguna);?>" class="btn btn-primary"><i class='fa fa-arrow-circle-left'></i> Kembali</a>
								</div>
							</div>
						</form>
				<?php
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->