		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />

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
					echo "<div class='row text-right'>";
							echo "<a class='btn btn-danger btn-md' href='".base_url($url)."'>Kembali</a> ";
						if($akses["lihat log"]){
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
					}
					echo "<br><br>";
					echo "</div>";
					
					echo "<div class='row'>";
						if($this->akses["ubah"]){
						?>
							<div class="col-lg-8">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><?php echo $judul;?></a>
										</h4>
									</div>
									<div id="collapseOne" class="panel-collapse collapse <?php echo $panel_ubah;?>">
										<div class="panel-body">
											<form role="form" action="" id="formulir_ubah" method="post" onsubmit="return cek_simpan_ubah()">
												<input type="hidden" name="aksi" value="ubah"/>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-4">
															<label>Payment Date (SAP)</label>
														</div>
														<div class="col-lg-5">
															<?php echo tanggal($header_penggajian["payment_date"]);?>
														</div>
														<div class="col-lg-3"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-4">
															<label>Nama Pembayaran</label>
														</div>
														<div class="col-lg-5">
															<?php echo $header_penggajian["nama_payslip"];?>
														</div>
														<div class="col-lg-3"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-4">
															<label>Publikasi</label>
														</div>
														<div class="col-lg-5">
															<input class="form-control" id="waktu_publikasi" name="waktu_publikasi" value="<?php echo $header_penggajian["start_display"];?>" placeholder="Waktu Publikasi">
														</div>
														<div id="warning_tanggal_publikasi" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-4">
															<label>Pesan Baris 1</label>
														</div>
														<div class="col-lg-5">
															<input class="form-control" id="pesan_baris_1" name="pesan_baris_1" value="<?php echo $header_penggajian["pesan_1"];?>" placeholder="Pesan Baris 1">
														</div>
														<div id="warning_pesan_baris_1" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-4">
															<label>Pesan Baris 2</label>
														</div>
														<div class="col-lg-5">
															<input class="form-control" id="pesan_baris_2" name="pesan_baris_2" value="<?php echo $header_penggajian["pesan_2"];?>" placeholder="Pesan Baris 2">
														</div>
														<div id="warning_pesan_baris_2" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<div class="row">
													<div class="col-lg-12 text-right">
														<button type="submit" class="btn btn-primary">Simpan</button>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- /.col-lg-12 -->
					<?php
						}
						if($this->akses["cetak"]){
							
					?>
							<div class="col-lg-4">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Cetak</a>
										</h4>
									</div>
									<div id="collapseTwo" class="panel-collapse collapse <?php echo $panel_cetak;?>">
										<div class="panel-body">
											<form action="" method="post" id="form_cetak">
												<label for="pilihan_cetak_semua">
													<input type="radio" name="pilihan_cetak" id="pilihan_cetak_semua" onclick="semua_karyawan(this);" value="semua karyawan"/> Semua Karyawan
												</label><br/>
												<label for="pilihan_cetak_pilih_karyawan">
													<input type="radio" name="pilihan_cetak" id="pilihan_cetak_pilih_karyawan" onclick="pilih_karyawan(this);" value="unit kerja"/> Pilih Karyawan
												</label>
												<button type="button" class="btn btn-xs btn-primary" style="display:none" id="tombol_pilih_karyawan" data-toggle='modal' data-target='#modal_tombol_pilih_karyawan' onclick="modal_pilih_karyawan();">pilih</button>
												<input type="hidden" id="value_pilihan_cetak" value=""/>
												<input type="hidden" id="np_karyawan" name="np_karyawan" value=""/>
												<input type="hidden" id="pilihan_pilih_kontrak_kerja" name="pilihan_pilih_kontrak_kerja" value=""/>
												<input type="hidden" id="pilihan_pilih_unit_kerja" name="pilihan_pilih_unit_kerja" value=""/>
												<input type="hidden" id="id_header" name="id_header" value="<?php echo $id_header?>"/>
												<input type="hidden" id="url" name="url" value="<?php echo $url?>"/>
												<div id='pilih_kontrak_kerja_cetak_gaji' style='max-height:70px;overflow:auto;'>
												</div>

												<div id='pilih_unit_kerja_cetak_gaji' style='max-height:180px;overflow:auto;'></div>
												<div class="row">
													<div class="col-lg-12 text-right">
														<button type="submit" class="btn btn-primary">Cetak</button>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<!-- /.col-lg-12 -->
					<?php
						}
					?>
					</div>
					<!-- /.row -->
				<?php
					if($this->akses["lihat pembayaran"]){
				?>
						
						<div class="form-group">
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_payslip">
									<thead>
										<tr>
											<th class='text-center no-sort'>No</th>
											<th class='text-center no-sort'>Nomor Pokok</th>
											<th class='text-center no-sort'>Nama</th>
											<th class='text-center no-sort'>Pilih Karyawan</th>
											<th class='text-center no-sort'>Jabatan</th>
											<th class='text-center no-sort'>Nama Pembayaran</th>
											<th class='text-center no-sort'>Aksi</th>
										</tr>
									</thead>
									<tbody>
									
									</tbody>
								</table>
								<!-- /.table-responsive -->
							</div>						
						</div>
				<?php
					}
				?>
				<!-- Modal -->
				<div class="modal fade" id="modal_tombol_pilih_karyawan" tabindex="-1" role="dialog" aria-labelledby="label_modal_tombol_pilih_karyawan" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_pilih_karyawan">Pilih Karyawan</h4>
							</div>
							<div class="modal-body">
								<div id='isi_modal_tombol_pilih_karyawan'></div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
							</div>
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
		
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		<script type="text/javascript">	
			$(document).ready(function() {
				table_serverside();
				$("#waktu_publikasi").datetimepicker({
					format: 'Y-MM-DD HH:mm'
				});
				$("#waktu_publikasi").val('<?php echo $header_penggajian["start_display"];?>');
			});
			
			function refresh_table_serverside() {
				$('#tabel_payslip').DataTable().destroy();				
				table_serverside();
			}
		</script>
		
		<script>		
			function table_serverside(){
				
				table = $('#tabel_payslip').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"stateSave": true,
					"responsive":true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("osdm/penggajian/tabel_rincian_gaji/$id_header");?>",
						"type": "POST"
					},

					//Set column definition initialisation properties.
					"columnDefs": [
						{ "className": "dt-right", "targets": [0] },
						{ "className": "dt-center", "targets": [1,5] },
						{ 						
							"targets": [ 0 ], //first column / numbering column
							"targets": 'no-sort', //first column / numbering column
							"orderable": false, //set not orderable
						}
					],
					drawCallback: function () { 
						//$('#tabel_ess_lembur_sdm').LoadingOverlay("hide");
					}

				});
			
			}		
		</script>
		