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
				?>
				
				<div class="row" style="margin-bottom:20px;">
					<div class="col-lg-12">
						<button class='btn btn-primary btn-sm' data-toggle='modal' data-target='#modal_tambah'><b>Tambah Pegawai</b></button>
					</div>
				</div>

				<div class="row">
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_daftar_pegawai">
						<thead>
							<tr>
								<th class='text-center'>Nomor Pokok</th>
								<th class='text-center'>Nama</th>
								<th class='text-center'>Jenis Kelamin</th>
								<th class='text-center'>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
								for($i=0;$i<count($daftar_pegawai);$i++){
									if($i%2==0){
										$class = "even";
									}
									else{
										$class = "odd";
									}
									
									echo "<tr class='$class'>";
										echo "<td>".$daftar_pegawai[$i]["no_pokok"]."</td>";
										echo "<td>".$daftar_pegawai[$i]["nama_lengkap"]."</td>";
										echo "<td>".$daftar_pegawai[$i]["jenis_kelamin"]."</td>";
										echo "<td class='text-center'>";
											echo "<a href='".base_url()."' class='btn btn-primary btn-xs'>detail</button>";
										echo "</td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
					<!-- /.table-responsive -->
				</div>
				
				<!-- Modal -->
				<div class="modal fade" id="modal_tambah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<form role="form" action="" id="formulir_tambah" method="post">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="label_modal_ubah">Tambah Pegawai</h4>
								</div>
								<div class="modal-body">
									<input type="hidden" name="aksi" value="tambah"/>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Nomor Pokok</label>
											</div>
											<div class="col-lg-8">
												<input class="form-control" name="no_pokok" value="" placeholder="Nomor Pokok"/>
											</div>
											<div id="warning_nama" class="col-lg-2 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Nama Lengkap</label>
											</div>
											<div class="col-lg-8">
												<input class="form-control" name="nama" value="" placeholder="Nama Pegawai"/>
											</div>
											<div id="warning_nama" class="col-lg-2 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Jenis Kelamin</label>
											</div>
											<div class="col-lg-8">
												<div class="radio">
													<label>
														<input type="radio" name="jenis_kelamin" id="status_ubah_laki_laki" value="L">Laki-laki
													</label>
													<label>
														<input type="radio" name="jenis_kelamin" id="status_ubah_perempuan" value="P">Perempuan
													</label>
												</div>
											</div>
											<div id="warning_status_ubah" class="col-lg-2 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Nomor KTP</label>
											</div>
											<div class="col-lg-8">
												<input class="form-control" name="nomor_ktp" value="" placeholder="Nomor KTP"/>
											</div>
											<div id="warning_nama" class="col-lg-2 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Tanggal Lahir</label>
											</div>
											<div class="col-lg-8">
												<input type='date' class="form-control" id="tanggal_lahir" name="tanggal_lahir" dateformat="dd-mm-yyyy" value="" placeholder="Tanggal Lahir" onchange="document.getElementById('tanggal_masuk').min=(parseInt(this.value.substr(0,4))+17).toString()+'-'+this.value.substr(5,2)+'-'+this.value.substr(8,2)"/>
											</div>
											<div id="warning_nama" class="col-lg-2 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Tanggal Masuk</label>
											</div>
											<div class="col-lg-8">
												<input type='date' class="form-control" id="tanggal_masuk" name="tanggal_masuk" dateformat="dd-mm-yyyy" value="" placeholder="Tanggal Masuk"/>
											</div>
											<div id="warning_nama" class="col-lg-2 text-danger"></div>
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
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->