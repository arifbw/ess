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
					// Fix error pada tampilan view, dimana data $akses["lihat log"] tidak ada datanya
					// if($akses["lihat"]){
					// 	echo "<div class='row text-right'>";
					// 		echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
					// 		echo "<br><br>";
					// 	echo "</div>";
					// }
					
					if($this->akses["lihat"]){
						echo "<div class='row'>";
							echo "<div class='col-lg-2'>";
								echo "<img src='".base_url($karyawan["foto"])."' class='img-thumbnail'/>";
							echo "</div>";
							echo "<div class='col-lg-10'>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Nama Lengkap</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo $karyawan["nama"];
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Tempat, Tanggal Lahir</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo ucfirst(strtolower($karyawan["tempat_lahir"])).", ".tanggal($karyawan["tanggal_lahir"]);
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Tanggal Mulai Bekerja</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo tanggal($karyawan["tanggal_masuk"]);
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Unit Kerja</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo $karyawan["kode_unit"]." - ".$karyawan["nama_unit"];
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Jabatan</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo $karyawan["kode_jabatan"]." - ".$karyawan["nama_jabatan"];
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Grade Jabatan</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo $karyawan["grade_jabatan"];
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Pangkat</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo $karyawan["nama_pangkat"];
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Grade Pangkat</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo $karyawan["grade_pangkat"];
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Masa Kerja</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										$masa_kerja = "";
										if((int)$karyawan["masa_kerja_tahun"]>0){
											$masa_kerja .= $karyawan["masa_kerja_tahun"]." tahun";
										}
										if((int)$karyawan["masa_kerja_bulan"]>0){
											if(!empty($masa_kerja)){
												$masa_kerja.= " ";
											}
											$masa_kerja .= $karyawan["masa_kerja_bulan"]." bulan";
										}
										if((int)$karyawan["masa_kerja_hari"]>0){
											if(!empty($masa_kerja)){
												$masa_kerja.= " ";
											}
											$masa_kerja .= $karyawan["masa_kerja_hari"]." hari";
										}
										echo $masa_kerja;
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Tanggal Rencana Masa Pra Pensiun (MPP)</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo tanggal($karyawan["tanggal_mpp"]);
									echo "</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-5 text-right'>";
										echo "<b>Tanggal Rencana Pensiun</b>";
									echo "</div>";
									echo "<div class='col-lg-7'>";
										echo tanggal($karyawan["tanggal_pensiun"]);
									echo "</div>";
								echo "</div>";
							echo "</div>";
						echo "</div>";
		
				?>
						
				
				<?php
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
	
