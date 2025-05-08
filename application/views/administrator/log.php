        <link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
        <!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul;?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>

				<?php					
					if(!empty($url_modul)){
						echo "<div class='row text-right'>";
							echo "<a class='btn btn-primary btn-md' href='$url_modul'><i class='fa fa-arrow-circle-left'></i> Kembali</a>";
							echo "<br><br>";
						echo "</div>";
					}
					if($this->akses["lihat"]){
				?>
						<form role="form" action="" id="formulir_log" method="post">
							<div class="row">
								<div class="form-group">
									<div class="col-lg-3">
										<label>Nama Modul</label>
									</div>
									<div class="col-lg-9">
										<div class="form-group">
											<select class="form-control" name="modul" onchange="this.form.submit()">
												<option value=''>--- Semua Modul ---</option>
												<?php
													$optgroup = "";
													for($i=0;$i<count($daftar_modul_aksi);$i++){
														if(strcmp($optgroup,$daftar_modul_aksi[$i]["nama_kelompok_modul"])!=0){
															$optgroup=$daftar_modul_aksi[$i]["nama_kelompok_modul"];
															echo "<optgroup label='$optgroup'>";
														}
														
														$selected="";
														if($id_modul==(int)$daftar_modul_aksi[$i]["id"]){
															$selected="selected=selected";
														}
														echo "<option value='".$daftar_modul_aksi[$i]["id"]."' $selected>".$daftar_modul_aksi[$i]["nama"]."</option>";
														
														if($i==count($daftar_modul_aksi)-1 or strcmp($optgroup,$daftar_modul_aksi[$i+1]["nama_kelompok_modul"])!=0){
															echo "</optgroup>";
														}
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<div class="col-lg-3">
										<label>Pengguna</label>
									</div>
									<div class="col-lg-9">
										<div class="form-group">
											<select class="form-control select2" name="pengguna" onchange="this.form.submit()">
												<?php
													if($this->akses["lihat log pengguna lain"]){
														echo "<option value=''>--- Semua Pengguna ---</option>";
													}
													for($i=0;$i<count($daftar_pengguna);$i++){
														$selected="";
														if($id_pengguna==(int)$daftar_pengguna[$i]["id"]){
															$selected="selected=selected";
														}
														echo "<option value='".$daftar_pengguna[$i]["id"]."' $selected>".$daftar_pengguna[$i]["no_pokok"]." - ".$daftar_pengguna[$i]["username"]." - ".$daftar_pengguna[$i]["nama"]." - ".$daftar_pengguna[$i]["kode_unit"]." - ".$daftar_pengguna[$i]["nama_unit"]."</option>";
													}
												?>
											</select>
										</div>
									</div>
								</div>
							</div>
						
							<?php
								echo "<input type='hidden' name='target' value='$id_target'>";
								echo "<input type='hidden' name='nama_target_judul' value='$nama_target_judul'>";
								echo "<input type='hidden' name='isi_target_judul' value='$isi_target_judul'>";
								echo "<input type='hidden' name='url_modul' value='$url_modul'>";
								
								if(!empty($nama_target_judul) and !empty($isi_target_judul)){
							?>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-3">
												<label><?php echo str_replace(" : ".$isi_target_judul,"",$nama_target_judul);?></label>
											</div>
											<div class="col-lg-9">
												<?php echo $isi_target_judul?>
											</div>
										</div>
									</div>
							<?php
								}
							?>
						</form>
					<?php
						if(isset($daftar_log)){
					?>

							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_log">
									<thead>
										<tr>
											<th class='text-center'>Username</th>
											<th class='text-center'>Nomor Pokok</th>
											<th class='text-center'>Nama</th>
											<th class='text-center'>Modul</th>
											<th class='text-center'>Deskripsi</th>
											<th class='text-center'>Kondisi Lama</th>
											<th class='text-center'>Kondisi Baru</th>
											<th class='text-center'>Alamat IP</th>
											<th class='text-center'>Waktu</th>
										</tr>
									</thead>
									<tbody>
										<?php
											for($i=0;$i<count($daftar_log);$i++){
												if($i%2==0){
													$class = "even";
												}
												else{
													$class = "odd";
												}
												
												echo "<tr class='$class'>";
													echo "<td>".$daftar_log[$i]["username"]."</td>";
													echo "<td>".$daftar_log[$i]["no_pokok"]."</td>";
													echo "<td>".$daftar_log[$i]["nama"]."</td>";
													echo "<td>".$daftar_log[$i]["nama_modul"]."</td>";
													echo "<td>".$daftar_log[$i]["deskripsi"]."</td>";
													echo "<td>".$daftar_log[$i]["kondisi_lama"]."</td>";
													echo "<td>".$daftar_log[$i]["kondisi_baru"]."</td>";
													echo "<td>".$daftar_log[$i]["alamat_ip"]."</td>";
													echo "<td>".tanggal_waktu($daftar_log[$i]["waktu"])."</td>";
												echo "</tr>";
											}
										?>
									</tbody>
								</table>
								<!-- /.table-responsive -->
							</div>
				<?php
						}
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#wrapper -->
        <script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>