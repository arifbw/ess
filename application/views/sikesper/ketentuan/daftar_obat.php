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
				<?php } ?>

				<div class='row'>
					<?php if(@$akses["lihat"]) { ?>
					<div class='col-lg-4 text-left'>
						<select class="form-control select2" id="jenis" onchange="refresh_table_serverside()">
							<option value='0'>Semua Jenis</option>
							<option value='umum'>Umum</option>
							<option value='khusus'>Khusus</option>
							<option value='kondisi_khusus'>Kondisi Khusus</option>
						</select>
					</div>
					<div class='col-lg-6'></div>
					<?php }
					if(@$akses["lihat log"]) { ?>
					<div class='col-lg-2 text-right'>
						<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
						<br><br>
					</div>
					<?php } ?>
				</div>

				<?php if(@$akses["tambah"]) { ?>
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
													<label>Pilih Kategori</label>
												</div>
												<div class="col-lg-7">
													<select class="form-control select2" name='id_kategori' style="width: 100%;" id="pilih_jenis">
														<option value='0'>-Pilih Kategori-</option>
														<?php foreach ($parent_kategori as $value) { ?>
															<option value='<?php echo $value['id']?>' data-jenis="<?php echo $value['jenis'] ?>"><?php echo ucwords($value['jenis'])." - ".$value['nama_kategori']?></option>
														<?php } ?>
													</select>
												</div>
												<div id="warning_kategori" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Zat Aktif Obat</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="zat_aktif" placeholder="Masukkan Zat Aktif Obat" required>
												</div>
												<div id="warning_zat_aktif" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Merek Obat</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="merk_obat" placeholder="Masukkan Nama Merek Obat" required>
												</div>
												<div id="warning_merk" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Sediaan</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="sediaan" placeholder="Masukkan Sediaan Obat" required>
												</div>
												<div id="warning_sediaan" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Dosis</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="dosis" placeholder="Masukkan Dosis Obat" required>
												</div>
												<div id="warning_dosis" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Farmasi</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="farmasi" placeholder="Masukkan Farmasi" required>
												</div>
												<div id="warning_farmasi" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-2">
													<label>Keterangan</label>
												</div>
												<div class="col-lg-7">
													<textarea class="form-control" name="keterangan" placeholder="Masukkan Keterangan (Jika Ada)" row="3" required></textarea>
												</div>
												<div id="warning_keterangan" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<div class="form-group" id="view_umum">
											<div class="row">
												<div class="col-lg-2">
													<label>Cover</label>
												</div>
												<div class="col-lg-7">
													<label class='radio-inline'>
														<input type="radio" name="cover" value="1">Dicover(Y)
													</label>
													<label class='radio-inline'>
														<input type="radio" name="cover" value="0">Tidak Dicover(T)
													</label>
												</div>
												<div id="warning_cover" class="col-lg-3 text-danger"></div>
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
				<?php }

				if(@$this->akses["lihat"]) { ?>
				<div class="row">
					<div class="col-lg-12 table-responsive">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_daftar_obat">
							<thead>
								<tr>
									<!-- <th class='text-center'>No</th> -->
									<th class='text-center'>Kode</th>
									<th class='text-center'>Kategori</th>
									<th class='text-center'>Zat Aktif</th>
									<th class='text-center'>Merek Obat</th>
									<th class='text-center'>Sediaan</th>
									<th class='text-center'>Dosis</th>
									<th class='text-center'>Farmasi</th>
									<th class='text-center'>Cover</th>
									<th class='text-center'>Status</th>
									<?php
										if(@$akses["ubah"] or @$akses["lihat log"]) {
											echo "<th class='text-center'>Aksi</th>";
										}
									?>
								</tr>
							</thead>
							<tbody>
								<?php $i=0; foreach ($daftar_obat as $daftar_obat) { ?>
								<tr>
									<!-- <td align='center'><?= ($i+1) ?></td> -->
									<td><?= $daftar_obat["no_kode"] ?></td>
									<td><?= $daftar_obat["nama_kategori"] ?></td>
									<td><?= $daftar_obat["zat_aktif"] ?></td>
									<td><?= $daftar_obat["merk_obat"] ?></td>
									<td><?= $daftar_obat["sediaan"] ?></td>
									<td><?= $daftar_obat["dosis"] ?></td>
									<td><?= $daftar_obat["farmasi"] ?></td>
									<td><?= ($daftar_obat["cover"]=='1') ? 'Ya' : 'Tidak' ?></td>
									<td class='text-center'>
										<?php if((int)$daftar_obat["status"]==1){
											echo 'Aktif';
										} else if((int)$daftar_obat["status"]==0){
											echo 'Non Aktif';
										} ?>
									</td>
									<?php if(@$akses["ubah"]) { ?>
									<td class='text-center'>
										<?php if(@$akses["ubah"]){ ?>
											<button type='button' class='btn btn-primary btn-xs ubah' data-id='<?= $daftar_obat["id"] ?>' data-toggle="modal" data-target="#modal_ubah">Ubah</button> 
										<?php } ?>
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
				<div class="modal fade" id="modal_ubah" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_np">Ubah Data Obat</h4>
							</div>
							<div class="modal-body">
								<form role="form" action="" id="formulir_ubah" method="post">
									<input type="hidden" name="aksi" value="ubah"/>
									<input id="id-obat" type="hidden" name="id">
									<input id="id-ubah" type="hidden" name="id_ubah">
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>No. Kode</label>
											</div>
											<div class="col-lg-7">
												<input id="no-kode" class="form-control" name="no_kode" placeholder="Masukkan Nomor Kode" required>
											</div>
											<div id="warning_no_kode" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Pilih Kategori</label>
											</div>
											<div class="col-lg-7">
												<select id="id-kategori" class="form-control select2" name='id_kategori' style="width: 100%;">
													<option value='0'>-Pilih Kategori-</option>
													<?php foreach ($parent_kategori as $value) { ?>
														<option value='<?php echo $value['id']?>'><?php echo ucwords($value['jenis'])." - ".$value['nama_kategori']?></option>
													<?php } ?>
												</select>
											</div>
											<div id="warning_id_kategori" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Zat Aktif Obat</label>
											</div>
											<div class="col-lg-7">
												<input id="zat-aktif" class="form-control" name="zat_aktif" placeholder="Masukkan Zat Aktif Obat" required>
											</div>
											<div id="warning-zat-aktif" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Merek Obat</label>
											</div>
											<div class="col-lg-7">
												<input id="merk-obat" class="form-control" name="merk_obat" placeholder="Masukkan Nama Merek Obat" required>
											</div>
											<div id="warning-merk-obat" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Sediaan</label>
											</div>
											<div class="col-lg-7">
												<input id="sediaan-obat" class="form-control" name="sediaan" placeholder="Masukkan Sediaan Obat" required>
											</div>
											<div id="warning-sediaan" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Dosis</label>
											</div>
											<div class="col-lg-7">
												<input id="dosis-obat" class="form-control" name="dosis" placeholder="Masukkan Dosis Obat" required>
											</div>
											<div id="warning-dosis" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Farmasi</label>
											</div>
											<div class="col-lg-7">
												<input id="farmasi-obat" class="form-control" name="farmasi" placeholder="Masukkan Farmasi" required>
											</div>
											<div id="warning-farmasi" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Keterangan</label>
											</div>
											<div class="col-lg-7">
												<textarea id="keterangan-obat" class="form-control" name="keterangan" placeholder="Masukkan Keterangan (Jika Ada)" row="3"></textarea>
											</div>
											<div id="warning-keterangan" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group" id="view_umum_ubah">
										<div class="row">
											<div class="col-lg-2">
												<label>Cover</label>
											</div>
											<div class="col-lg-7">
												<label class='radio-inline'>
													<input id="cover-1" type="radio" name="cover" value="1">Dicover(Y)
												</label>
												<label class='radio-inline'>
													<input id="cover-0" type="radio" name="cover" value="0">Tidak Dicover(T)
												</label>
											</div>
											<div id="warning-cover" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-2">
												<label>Status</label>
											</div>
											<div class="col-lg-7">
												<label class='radio-inline'>
													<input id="status-1" type="radio" name="status" value="1" required>Aktif
												</label>
												<label class='radio-inline'>
													<input id="status-0" type="radio" name="status" value="0" required>Non Aktif
												</label>
											</div>
											<div id="warning-status" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12 text-center">
											<button type="submit" class="btn btn-primary">Simpan</button>
										</div>
									</div>
								</form>
							</div>
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
		

		<script type="text/javascript">
			$("#view_umum").hide();

			$(document).ready(function() {
				$(document).on('click', '.ubah', function() {
					var id = $(this).data('id');

					$.ajax({
						url: '<?= site_url('sikesper/ketentuan/daftar_obat/detail/'); ?>'+id,
						type: 'GET',
						success: function(data){
							var val = data.result;

							$("#view_umum_ubah").hide();
							$('#cover-1').attr('checked', false);
							$('#cover-0').attr('checked', false);

							$('#id-obat').val(val.id);
							$('#id-ubah').val(val.id);
							$('#no-kode').val(val.no_kode);
							$('#id-kategori').val(val.id_kategori);
							$('#zat-aktif').val(val.zat_aktif);
							$('#merk-obat').val(val.merk_obat);
							$('#sediaan-obat').val(val.sediaan);
							$('#dosis-obat').val(val.dosis);
							$('#farmasi-obat').val(val.farmasi);
							$('#keterangan-obat').val(val.keterangan);

							$('#cover-'+val.cover).attr('checked', true);
							$('#status-'+val.status).attr('checked', true);

							$('#id-kategori').select2().trigger('change');

							if(val.jenis=='umum') {
								$("#view_umum_ubah").show();
								$('#cover-'+val.cover).attr('checked', true);
							}
						}
					});
				});
				
				$(document).on('change', '#pilih_jenis', function() {
					jenis = $(this).find(':selected').attr('data-jenis');
					if(jenis=='umum')
						$("#view_umum").show();
					else
						$("#view_umum").hide();
				});
			});	

			function refresh_table_serverside() {
				var jns = $('#jenis').val();

				$.ajax({
					url: '<?= site_url('sikesper/ketentuan/daftar_obat/change_jenis/'); ?>'+jns,
					type: 'GET',
					success: function(json){
						var data = jQuery.parseJSON(json);
						var content = '';

						if (data.length==0) {
							content += '<tr><td colspan="10" style="background-color:#F9F9F9">Tidak ditemukan data yang sesuai</td></tr>';
						}
						else {
				            for (var i=0; i<data.length; i++) {
					            content += '<tr>';
					            content += '<td>' + data[i].no_kode + '</td>';
					            content += '<td>' + data[i].nama_kategori + '</td>';
					            content += '<td>' + data[i].zat_aktif + '</td>';
					            content += '<td>' + data[i].merk_obat + '</td>';
					            content += '<td>' + data[i].sediaan + '</td>';
					            content += '<td>' + data[i].dosis + '</td>';
					            content += '<td>' + data[i].farmasi + '</td>';
					            if (data[i].cover=='1')
					            content += '<td>Ya</td>';
					        	else
					            content += '<td>Tidak</td>';
					            if (data[i].status=='1')
					            content += '<td class="text-center">Aktif</td>';
					        	else
					            content += '<td class="text-center">Non Aktif</td>';
					        	<?php if(@$akses["ubah"]) { ?>
					            content += '<td class="text-center"><button type="button" class="btn btn-primary btn-xs ubah" data-id="' + data[i].id + '" data-toggle="modal" data-target="#modal_ubah">Ubah</button></td>';
					        	<?php } else { ?>
					            content += '<td class="text-center"></td>';
					        	<?php } ?>
					            content += '</tr>';
				            }
				        }

			            $('#tabel_daftar_obat tbody').html(content);
					}
				});
			};
		</script>