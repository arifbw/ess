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
													<label>Nama Kelas</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="kelas" placeholder="Masukkan Nama Kelas" required>
												</div>
												<div id="warning_nama" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Kepangkatan</label>
												</div>
												<div class="col-lg-7">
													<select class="form-control select2" multiple name='pangkat[]' style="width: 100%;">
														<?php foreach ($pangkat as $value) { ?>
															<option value='<?php echo $value['nama_pangkat']?>'><?php echo ucwords($value['nama_pangkat'])?></option>
														<?php } ?>
													</select>
												</div>
												<div id="warning_pangkat" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Status</label>
												</div>
												<div class="col-lg-7">
													<label class='radio-inline'>
														<input type="radio" name="status" value="aktif" required>Aktif
													</label>
													<label class='radio-inline'>
														<input type="radio" name="status" value="non aktif" required>Non Aktif
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
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_kelas_perawatan">
							<thead>
								<tr>
									<th class='text-center'>No</th>
									<th class='text-center'>Nama Kelas</th>
									<th class='text-center'>Kepangkatan</th>
									<th class='text-center'>Status</th>
									<?php
										if(@$akses["ubah"] or @$akses["lihat log"]){
											echo "<th class='text-center'>Aksi</th>";
										}
									?>
								</tr>
							</thead>
							<tbody>
								<?php for($i=0;$i<count($kelas_perawatan);$i++){ ?>
									<tr>
										<td align='center'><?php echo ($i+1) ?></td>
										<td class='text-center'><?php echo $kelas_perawatan[$i]["kelas"] ?></td>
										<td><?php echo str_replace(',', '<br>', $kelas_perawatan[$i]["nama_pangkat"]) ?></td>
										<td class='text-center'><?php echo ($kelas_perawatan[$i]["status"]=='1') ? 'Aktif' : 'Non Aktif'; ?></td>
										<?php if(@$akses["ubah"]){ ?>
										<td class='text-center'>
											<button type='button' class='btn btn-primary btn-xs' data-id='<?php echo $kelas_perawatan[$i]["id"] ?>' data-kelas='<?php echo $kelas_perawatan[$i]["kelas"] ?>' data-status='<?php echo $kelas_perawatan[$i]["status"] ?>' data-pangkat='<?php echo $kelas_perawatan[$i]["nama_pangkat"] ?>' onclick='tampil_data_ubah_new(this)'>Ubah</button>
										</td>
										<?php } ?>
									</tr>
								<?php } ?>
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
							<form role="form" action="" id="formulir_ubah" method="post">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
								</div>
								<div class="modal-body">
									<input type="hidden" name="aksi" value="ubah"/>
                                    <!-- <input type="hidden" name="id_ubah" id="id_ubah"> -->
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Nama Kelas</label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="kelas" placeholder="Masukkan Nama Kelas" id="kelas_ubah" required>
											</div>
											<div id="warning_nama_ubah" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Kepangkatan</label>
											</div>
											<div class="col-lg-7">
												<select class="form-control select2" multiple name='pangkat[]' id="pangkat_ubah" style="width: 100%;">
													<?php foreach ($pangkat as $value) { ?>
														<option value='<?php echo $value['nama_pangkat']?>'><?php echo ucwords($value['nama_pangkat'])?></option>
													<?php } ?>
												</select>
											</div>
											<div id="warning_pangkat_ubah" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Status</label>
											</div>
											<div class="col-lg-7">
												<label class='radio-inline'>
													<input type="radio" name="status" value="aktif" id="status_aktif_ubah" required>Aktif
												</label>
												<label class='radio-inline'>
													<input type="radio" name="status" value="non aktif" id="status_non_aktif_ubah" required>Non Aktif
												</label>
											</div>
											<div id="warning_status_ubah" class="col-lg-3 text-danger"></div>
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
				<?php } ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		