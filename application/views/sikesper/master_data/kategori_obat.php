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
				<!-- /.row -->

				<?php if(!empty($success)) { ?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $success;?>
				</div>
				<?php } if(!empty($warning)) { ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $warning;?>
				</div>
				<?php } if(@$akses["lihat log"]) { ?>
				<div class='row text-right'>
					<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
					<br><br>
				</div>
				<?php } if(@$akses["tambah"]) { ?>
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
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>No. Kode</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="no_kode" placeholder="Masukkan Nomor Kode" required>
												</div>
												<div id="warning_kode" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Jenis</label>
												</div>
												<div class="col-lg-7">
													<select class="form-control select2" name='jenis' style="width: 100%;" required>
														<option value='umum'>Umum</option>
														<option value='khusus'>Khusus</option>
														<option value='kondisi khusus'>Kondisi Khusus</option>
													</select>
												</div>
												<div id="warning_jenis" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Nama Kategori</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="nama_kategori" placeholder="Masukkan Nama Kategori" required>
												</div>
												<div id="warning_nama" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Parent Kategori *jika ada</label>
												</div>
												<div class="col-lg-7">
													<select class="form-control select2" name='id_parent' style="width: 100%;">
														<option value='0'>-Pilih Kategori-</option>
														<?php foreach ($parent_kategori as $value) { ?>
															<option value='<?php echo $value['id']?>'><?php echo ucwords($value['jenis'])." - ".$value['nama_kategori']?></option>
														<?php } ?>
													</select>
												</div>
												<div id="warning_parent" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Status</label>
												</div>
												<div class="col-lg-7">
													<label class='radio-inline'>
														<input type="radio" name="status" value="1" required>Aktif
													</label>
													<label class='radio-inline'>
														<input type="radio" name="status" value="0" required>Non Aktif
													</label>
												</div>
												<div id="warning_status" class="col-lg-3 text-danger"></div>
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
				<?php } if(@$this->akses["lihat"]) { ?>
				<div class="row">
					<div class="col-lg-12">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_kategori_obat">
							<thead>
								<tr>
									<!-- <th class='text-center'>#</th> -->
									<th class='text-center'>No Kode</th>
									<th class='text-center'>Nama Kategori</th>
									<th class='text-center'>Parent Kategori</th>
									<th class='text-center'>Jenis</th>
									<th class='text-center'>Status</th>
									<?php
										if(@$akses["ubah"] or @$akses["lihat log"]){
											echo "<th class='text-center'>Aksi</th>";
										}
									?>
								</tr>
							</thead>
							<tbody>
								<?php
									for($i=0;$i<count($kategori_obat);$i++){
										if($i%2==0){
											$class = "even";
										}
										else{
											$class = "odd";
										}
										
										echo "<tr class='$class'>";
											// echo "<td align='center'>".($i+1)."</td>";
											echo "<td class='text-center'>".$kategori_obat[$i]["no_kode"]."</td>";
											echo "<td>".$kategori_obat[$i]["nama_kategori"]."</td>";
											echo "<td>".($kategori_obat[$i]["id_parent"]==0 ? '-' : $kategori_obat[$i]["parent_kategori"])."</td>";
											echo "<td>".ucwords($kategori_obat[$i]["jenis"])."</td>";
											echo "<td class='text-center'>";
												if((int)$kategori_obat[$i]["status"]==1){
													echo "Aktif";
												}
												else if((int)$kategori_obat[$i]["status"]==0){
													echo "Non Aktif";
												}
											echo "</td>";
											if(@$akses["ubah"] or @$akses["lihat log"]){
												echo "<td class='text-center'>";
													if(@$akses["ubah"]){
														echo "<button type='button' class='btn btn-primary btn-xs' data-id='".$kategori_obat[$i]["id"]."' data-no='".$kategori_obat[$i]["no_kode"]."' data-jenis='".$kategori_obat[$i]["jenis"]."' data-parent='".$kategori_obat[$i]["id_parent"]."' data-nama='".$kategori_obat[$i]["nama_kategori"]."' data-status='".$kategori_obat[$i]["status"]."' onclick='tampil_data_ubah_new(this)'>Ubah</button> ";
													}
													if(@$akses["lihat log"]){
														echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$kategori_obat[$i]["nama_kategori"]."\",".$kategori_obat[$i]["id"].")'>Lihat Log</button>";
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
				</div>

				<!-- Modal -->
				<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
					<div class="modal-dialog modal-lg" id="detail_content">
					</div>
				</div>
				<?php }

				if(@$akses["ubah"]) { ?>
				<!-- Modal -->
				<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<form role="form" action="<?= base_url('sikesper/master_data/kategori_obat')?>" id="formulir_ubah" method="post">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
								</div>
								<div class="modal-body">
									<input type="hidden" name="aksi" value="ubah"/>
                                    <input type="hidden" name="id_ubah" id="id_ubah">
									<div class="form-group">
										<div class="row">
											<div class="col-lg-5">
												<label>No. Kode</label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="no_kode_ubah" id="no_kode_ubah" placeholder="Masukkan Nomor Kode" required>
											</div>
											<div id="warning_kode_ubah" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-5">
												<label>Jenis</label>
											</div>
											<div class="col-lg-7">
												<select class="form-control select2" name='jenis_ubah' id='jenis_ubah' style="width: 100%;" required>
													<option value='umum'>Umum</option>
													<option value='khusus'>Khusus</option>
													<option value='kondisi khusus'>Kondisi Khusus</option>
												</select>
											</div>
											<div id="warning_jenis_ubah" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-5">
												<label>Nama Kategori Obat</label>
											</div>
											<div class="col-lg-7">
												<input type="text" class="form-control" name="nama_kategori_ubah" id="nama_kategori_ubah" value="" placeholder="Masukkan Nama Kategori" required>
											</div>
											<div id="warning_nama_ubah" class="col-lg-4 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-5">
												<label>Parent Kategori *jika ada</label>
											</div>
											<div class="col-lg-7">
												<select class="form-control select2" name='id_parent_ubah' id='id_parent_ubah' style="width: 100%;">
													<option value='0'>-Pilih Kategori-</option>
													<?php foreach ($parent_kategori as $value) { ?>
														<option value='<?php echo $value['id']?>'><?php echo ucwords($value['jenis'])." - ".$value['nama_kategori']?></option>
													<?php } ?>
												</select>
											</div>
											<div id="warning_parent_ubah" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-5">
												<label>Status</label>
											</div>
											<div class="col-lg-7">
                                                <input type="hidden" name="status_old" id="status_old">
												<div class="radio">
													<label>
														<input type="radio" name="status_ubah" id="status_ubah_aktif" value="1">Aktif
													</label>
													<label>
														<input type="radio" name="status_ubah" id="status_ubah_non_aktif" value="0">Non Aktif
													</label>
												</div>
											</div>
											<div id="warning_status_ubah" class="col-lg-4 text-danger"></div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-primary" onclick="/*return cek_simpan_ubah()*/">Simpan</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
								</div>
							</form>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
				<?php } ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		