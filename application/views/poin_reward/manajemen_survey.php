		<head>
			<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
		</head>
		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul; ?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<?php
				if (!empty($success)) {
				?>
					<div class="alert alert-success alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<?php echo $success; ?>
					</div>
				<?php
				}
				if (!empty($warning)) {
				?>
					<div class="alert alert-danger alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<?php echo $warning; ?>
					</div>
				<?php
				}
				if ($akses["tambah"]) {
				?>
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul; ?></a>
									</h4>
								</div>
								<div id="collapseOne" class="panel-collapse collapse <?php echo $panel_tambah; ?>">
									<div class="panel-body">
										<form role="form" action="" id="formulir_tambah" method="post" enctype="multipart/form-data">
											<input type="hidden" name="aksi" value="tambah" />
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Salin dari</label>
													</div>
													<div class="col-lg-7">
														<div class="form-group">
															<select class="form-control" name="modul" onchange='salin_tambah(this)'>
																<option value=''>--- Pilih Manajemen Survey ---</option>
																<?php
																for ($i = 0; $i < count($daftar_manajemen_survey); $i++) {
																	echo "<option value='" . $daftar_manajemen_survey[$i]["id"] . "'>";
																	echo $daftar_manajemen_survey[$i]["nama"];
																	echo " : " . $daftar_manajemen_survey[$i]["poin"] . " Poin";
																	echo "</option>";
																}
																?>
															</select>
														</div>
													</div>
													<!--<div id="warning_nama" class="col-lg-3 text-danger"></div>-->
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Nama <?php echo $judul; ?></label>
													</div>
													<div class="col-lg-7">
														<input class="form-control" name="nama" value="<?php echo $nama; ?>" placeholder="Nama <?php echo $judul; ?>">
													</div>
													<div id="warning_nama" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Konten</label>
													</div>
													<div class="col-lg-7">
														<input class="form-control" name="konten" value="<?php echo $konten; ?>" placeholder="Konten">
													</div>
													<div id="warning_konten" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Link</label>
													</div>
													<div class="col-lg-7">
														<input class="form-control" name="link" value="<?php echo $link; ?>" placeholder="Link">
													</div>
													<div id="warning_link" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Poin</label>
													</div>
													<div class="col-lg-7">
														<input class="form-control" type="number" name="poin" value="<?php echo $poin; ?>" placeholder="Poin">
													</div>
													<div id="warning_poin" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Durasi Baca (Detik)</label>
													</div>
													<div class="col-lg-7">
														<input class="form-control" name="durasi_baca" value="<?php echo $durasi_baca; ?>" placeholder="Durasi Baca">
													</div>
													<div id="warning_durasi_baca" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Tgl Awal</label>
													</div>
													<div class="col-lg-7">
														<input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>" id="start_date" onChange="getEndDate()" required>
													</div>
													<div id="warning_start_date" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Tgl Akhir</label>
													</div>
													<div class="col-lg-7">
														<input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>" id="end_date" min="" required>
													</div>
													<div id="warning_end_date" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="form-group">
												<div class="row">
													<div class="col-lg-2">
														<label>Upload Gambar <code>(png|jpeg|jpg)</code></label>
													</div>
													<div class="col-lg-7">
														<input type="file" class="form-control" accept=".jpg,.jpeg,.png" name="gambar" id="gambar" onchange="checkPhoto(this)" />
													</div>
													<div id="warning_gambar" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="form-group">
												<div class="row">
													<div class="col-lg-2">
														<label>NP Tergabung</label>
													</div>
													<div class="col-lg-7">
														<select id="np_tergabung" name="np_tergabung[]" multiple style="width: max-content;" class="form-control">
															<?php foreach ($ref_karyawan as $item): ?>
																<option value="<?= $item['no_pokok']; ?>" <?= isset($data->np_tergabung) && in_array($item['no_pokok'], explode(',', $data->np_tergabung)) ? 'selected' : null ?>><?= $item['no_pokok']; ?> - <?= $item['nama']; ?></option>
															<?php endforeach ?>
														</select>
													</div>
													<div id="warning_np_tergabung" class="col-lg-3 text-danger"></div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Status</label>
													</div>
													<div class="col-lg-6">
														<label class='radio-inline'>
															<?php
															if (strcmp($status, "1") == 0) {
																$checked = "checked='checked'";
															} else {
																$checked = "";
															}
															?>
															<input type="radio" name="status" id="status_tambah_aktif" value="aktif" <?php echo $checked; ?>>Aktif
														</label>
														<label class='radio-inline'>
															<?php
															if (strcmp($status, "0") == 0) {
																$checked = "checked='checked'";
															} else {
																$checked = "";
															}
															?>
															<input type="radio" name="status" id="status_tambah_non_aktif" value="non aktif" <?php echo $checked; ?>>Non Aktif
														</label>
													</div>
													<div class="col-lg-1">
														<label class='checkbox-inline'>
															<input type='checkbox' name='notifikasi' id='notifikasi' /> Notifikasi
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
				<?php
				}

				if ($akses["lihat"]) {
				?>
					<div class="row">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_manajemen_survey">
							<thead>
								<tr>
									<th class='text-center'>Gambar</th>
									<th class='text-center'>Nama</th>
									<th class='text-center'>Konten</th>
									<th class='text-center'>Link</th>
									<th class='text-center'>Poin</th>
									<th class='text-center'>Durasi Baca (Detik)</th>
									<th class='text-center'>Tgl Awal</th>
									<th class='text-center'>Tgl Akhir</th>
									<th class='text-center'>Status</th>
									<?php
									if ($akses["ubah"]) {
										echo "<th class='text-center'>Aksi</th>";
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?php
								for ($i = 0; $i < count($daftar_manajemen_survey); $i++) {
									if ($i % 2 == 0) {
										$class = "even";
									} else {
										$class = "odd";
									}

									echo "<tr class='$class'>";
									echo "<td align='center'><img src=" . base_url() . "uploads/images/survey/" . $daftar_manajemen_survey[$i]["gambar"] . " style='width: 100%; max-height: 150px;' alt='Gambar Belum Diupload'></td>";
									echo "<td>" . $daftar_manajemen_survey[$i]["nama"] . "</td>";
									echo "<td>" . $daftar_manajemen_survey[$i]["konten"] . "</td>";
									echo "<td>" . $daftar_manajemen_survey[$i]["link"] . "</td>";
									echo "<td>" . $daftar_manajemen_survey[$i]["poin"] . "</td>";
									echo "<td>" . $daftar_manajemen_survey[$i]["durasi_baca"] . "</td>";
									echo "<td align='center'>" . $daftar_manajemen_survey[$i]["start_date"] . "</td>";
									echo "<td align='center'>" . $daftar_manajemen_survey[$i]["end_date"] . "</td>";
									echo "<td class='text-center'>";
									if ((int)$daftar_manajemen_survey[$i]["status"] == 1) {
										echo "Aktif";
									} else if ((int)$daftar_manajemen_survey[$i]["status"] == 0) {
										echo "Non Aktif";
									}
									echo "</td>";
									if ($akses["ubah"]) {
										echo "<td class='text-center'>";
										if ($akses["ubah"]) {
											echo "<input type='hidden' name='id_manajemen_survey_$i' value='" . $daftar_manajemen_survey[$i]["id"] . "'/>";
											echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='ubah(this)'>Ubah</button> ";
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

				if ($akses["ubah"]) {
				?>
					<!-- Modal -->
					<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<form role="form" action="" id="formulir_ubah" method="post" enctype="multipart/form-data">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul; ?></h4>
									</div>
									<div class="modal-body">
										<input type="hidden" name="aksi" value="ubah" />
										<div id='isian_ubah_survey'></div>
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
				<?php
				}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script>
			$(() => {
				$('select[multiple]').select2({
					width: '100%',
					placeholder: 'Pilih np tergabung bisa lebih dari satu',
				})
			})
		</script>
