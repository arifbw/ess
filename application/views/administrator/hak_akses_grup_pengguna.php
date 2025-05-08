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
						echo "<div class='alert alert-success alert-dismissable'>";
							echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
							echo $success;
						echo "</div>";
					}
					if(!empty($warning)){
						echo "<div class='alert alert-danger alert-dismissable'>";
							echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
							echo $warning;
						echo "</div>";
					}
					
					$nama_modul = "";
					
					if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}

				?>	
				<form action='' method='post'>
					<?php
						if($akses["lihat"]){
						
							if(!$akses["ubah"]){
								$disabled = "disabled";
							}
							else{
								$disabled = "";
							}
							
							echo "<input type='hidden' name='id_grup_pengguna' value='$id_grup_pengguna'>";
							
							$max_kolom = 5;
							$baris = 0;
							$awal_kelompok = 0;
							
							
							foreach($banyak_per_kelompok as $nama_kelompok_modul=>$banyak){
								$data_per_kolom = ceil(max(log($banyak,2),$banyak/$max_kolom));

								echo "<hr>";
								echo "<div class='row text-center'>";
									echo "<b>$nama_kelompok_modul</b>";
								echo "</div>";
								echo "<hr>";
								
								echo "<table width='100%' class='table'>";
									echo "<tr valign='top'>";
										echo "<td>";
								
										for($i=$awal_kelompok;$i<count($daftar_hak_akses) and strcmp($nama_kelompok_modul,$daftar_hak_akses[$i]["nama_kelompok_modul"])==0;$i++){
											
											$nama_kelompok_modul = $daftar_hak_akses[$i]["nama_kelompok_modul"];
											
											if(strcmp($nama_modul,$daftar_hak_akses[$i]["nama_modul"])!=0){
												echo "<div class='row'>";
													echo "<div class='col-lg-12'>";
														echo "<b>".$daftar_hak_akses[$i]["nama_modul"]."</b>";
													echo "</div>";
												echo "</div>";
												$nama_modul = $daftar_hak_akses[$i]["nama_modul"];
											}
											
											echo "<div class='row'>";
												echo "<div class='col-lg-12'>";
													echo "<div class='checkbox'>";
														echo "<label>";
															$checked = "";
															if(in_array($daftar_hak_akses[$i]["id_aksi"],$daftar_hak_akses_grup_pengguna)){
																$checked = "checked=checked";
															}
															
															echo "<input type='checkbox' name='id_aksi[]' value='".$daftar_hak_akses[$i]["id_aksi"]."' $checked $disabled> ".$daftar_hak_akses[$i]["nama_aksi"];
														echo "</label>";
													echo "</div>";
												echo "</div>";
											echo "</div>";
											
											$baris++;
											
											if($i<count($daftar_hak_akses)-1 and strcmp($nama_modul,$daftar_hak_akses[$i+1]["nama_modul"])!=0){
												if($baris>=$data_per_kolom){
													echo "</td>";
													echo "<td>";
													$baris=0;
												}
												else{
													echo "<br>";
												}
											}
										}
										$awal_kelompok=$i;
										echo "</td>";
									echo "</tr>";
								echo "</table>";
							}
						}
					?>
					<div class="row">
						<div class="col-lg-12 text-center">
							<?php
								if($this->akses["ubah"]){
									echo "<button type='submit' class='btn btn-primary'>Simpan</button>";
								}
							?>
							<a href="<?php echo base_url($url_grup_pengguna);?>" class="btn btn-primary"><i class='fa fa-arrow-circle-left'></i> Kembali</a>
						</div>
					</div>
				</form>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->