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
				?>
				
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_cuti_besar">
								<thead>
									<tr>
										<th class='text-center'>No. Pokok</th>
										<th class='text-center'>Nama</th>
										<th class='text-center'>Tahun</th>
										<th class='text-center'>Tanggal Timbul</th>
										<th class='text-center'>Tanggal Kadaluarsa</th>
										<th class='text-center'>Muncul Bulan</th>
										<th class='text-center'>Jadi Cuti Tahunan</th>
										<th class='text-center'>Pakai</th>
										<th class='text-center'>Kompensasi</th>
										<th class='text-center'>Sisa</th>
										<?php
											if($akses["lihat log"] or $akses["konversi"] or $akses["kompensasi"]){
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
					
					if($this->akses["konversi"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_konversi" tabindex="-1" role="dialog" aria-labelledby="label_modal_konversi" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_konversi" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_konversi">Konversi <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<div id='isi_modal_konversi'></div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary">Simpan</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
						
						<!-- Modal -->
						<div class="modal fade" id="modal_perpanjang_kadaluarsa" tabindex="-1" role="dialog" aria-labelledby="label_modal_perpanjang_kadaluarsa" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_perpanjang_kadaluarsa" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_perpanjang_kadaluarsa">Perpanjang Kadaluarsa <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<div id='isi_modal_perpanjang_kadaluarsa'></div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary">Simpan</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
						
						<!-- Modal -->
						<div class="modal fade" id="modal_maintenance_kuota" tabindex="-1" role="dialog" aria-labelledby="label_modal_maintenance_kuota" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_maintenance_kuota" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_maintenance_kuota">Maintenance Kuota <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<div class="modal-body">
											<div id='isi_modal_maintenance_kuota'></div>
										</div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary">Simpan</button>
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
					if($this->akses["kompensasi"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_kompensasi" tabindex="-1" role="dialog" aria-labelledby="label_modal_kompensasi" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_kompensasi" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_kompensasi">Kompensasi <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<div id='isi_modal_kompensasi'></div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary">Simpan</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
						
						<!-- Modal -->
						<div class="modal fade" id="modal_ubcb" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubcb" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_ubcb" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubcb">Catatan Uang Bantuan Cuti Besar <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<div id='isi_modal_ubcb'></div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary">Simpan</button>
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