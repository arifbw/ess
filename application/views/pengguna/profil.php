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
					
					if($this->akses["lihat"]){
				?>
						<form role="form" action="" id="formulir_tambah" method="post">
							<div class="row">
								<div class="form-group">
									<div class="col-lg-3">
										<label>Username</label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="hidden" name="username" value="<?php echo $profil["username"];?>"/><?php echo $profil["username"];?>
									</div>
									<div id="warning_username" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<div class="col-lg-3">
										<label>Nomor Pokok</label>
									</div>
									<div class="col-lg-6">
										<input class="form-control" type="hidden" name="no_pokok" value="<?php echo $profil["no_pokok"];?>"/><?php echo $profil["no_pokok"];?>
									</div>
									<div id="warning_no_pokok" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							
							<?php
								if($profil["default_password"]){
									echo "<div class='alert alert-info alert-dismissable'>";
										echo "<i>Password</i> yang Anda gunakan saat ini adalah <i>password</i> yang dihasilkan oleh $title. Demi kenyamanan dan keamanan Anda dalam menggunakan $title, kami mohon kesediaan Anda untuk mengganti <i>password</i> tersebut. Terima kasih.";
									echo "</div>";
								}
								if((int)$profil["sisa_usia_password"]<=0){
									echo "<div class='alert alert-danger alert-dismissable'>";
										echo "<i>Password</i> yang Anda gunakan saat ini telah memasuki masa kadaluarsa. Demi kenyamanan dan keamanan Anda dalam menggunakan $title, kami mohon kesediaan Anda untuk mengganti <i>password</i> tersebut. Terima kasih.";
									echo "</div>";
								}
							?>
							
							<div class="row">
								<div class="form-group">
									<div class="col-lg-3">
										<label>Masa Aktif Password</label>
									</div>
									<div class="col-lg-6">
										<?php echo $profil["sisa_usia_password"]." ".$profil["satuan_usia_password"];?>
									</div>
									<div id="warning_masa_aktif_password" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<hr>
							<div class="row">
								<div class="form-group">
									<div class="col-lg-3">
										<label><i>Password</i> lama</label>
									</div>
									<div class="col-lg-4">
										<input class="form-control" type="password" name="password_lama"/>
									</div>
									<div id="warning_password_lama" class="col-lg-5 text-danger"></div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<div class="col-lg-3">
										<label><i>Password</i> baru</label>
									</div>
									<div class="col-lg-4">
										<input class="form-control" type="password" name="password_baru"/>
									</div>
									<div id="warning_password_baru" class="col-lg-5 text-danger"></div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<div class="col-lg-3">
										<label>Konfirmasi <i>Password</i> baru</label>
									</div>
									<div class="col-lg-4">
										<input class="form-control" type="password" name="konfirmasi_password_baru"/>
									</div>
									<div id="warning_konfirmasi_password_baru" class="col-lg-5 text-danger"></div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12 text-center">
									<button type="submit" class="btn btn-primary" onclick="return cek_ubah_password()">Simpan</button>
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