		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
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

				<?php if(@$akses["lihat log"]) { ?>
				<div class="row text-right">
					<button class="btn btn-primary btn-md" onclick="lihat_log()">Lihat Log</button>
					<br><br>
				</div>
				<?php } if(@$akses["tambah"] && $persetujuan=='0') { ?>
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse">
								<div class="panel-body">
									<form role="form" action="<?php echo base_url('food_n_go/konsumsi/pemesanan_makan_lembur/save'); ?>" id="formulir_tambah" method="post">
										<?php if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Unit Kerja</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2" id='insert_unit_kerja' name='insert_unit_kerja' onChange="get_approval();" style="width: 100%;" required>
														<option value=''>Pilih Unit Kerja</option>
														<?php foreach ($array_daftar_unit as $value) { ?>
															<option value='<?php echo $value['kode_unit']?>'><?php echo $value['kode_unit']." - ".$value['nama_unit']?></option>
														<?php } ?>
													</select>
												</div>	
												<div id="warning_unit_kerja" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<?php } else if($_SESSION["grup"]==5) { //jika Pengguna ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Penerima</label>
												</div>
												<div class="col-lg-6">
													<input type="text" class="form-control" value="<?= $_SESSION["no_pokok"].' - '.$_SESSION["nama"] ?>">
												</div>	
											</div>
										</div>
										<?php } ?>

										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Pilih Approver</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2" id='insert_np_atasan' name='insert_np_atasan' style="width: 100%;" required></select>
												</div>	
												<div id="warning_np_atasan" class="col-lg-3 text-danger"></div>
											</div>
										</div>

										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Lokasi Lembur</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2" id='insert_lokasi_lembur' name='insert_lokasi_lembur' style="width: 100%;" onChange="set_lokasi();" required>
														<option value=''></option>	
														<?php foreach ($array_daftar_lokasi->result_array() as $value) { ?>
															<option value='<?php echo $value['id']?>'><?php echo $value['nama']?></option>
														<?php } ?>
													</select>
													<span class="text-danger">Sesuaikan jumlah jenis pesanan sebelum memilih lokasi lembur</span>
												</div>
												<div id="warning_lokasi_lembur" class="col-lg-3 text-danger"></div>
											</div>
										</div>

										<?php if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja ?>
										<input type="hidden" id="maxIndexTableAdd" />
										<div class="form-group" id="bodyPesanan">
											<div class="row">
												<div class="col-lg-3">
													<label>Jenis Pesanan</label>
												</div>
												<div class="col-lg-4">
													<select class="form-control select2 pesan_makanan" name='insert_jenis_pesanan[]' style="width: 100%;" required></select>
												</div>

												<div class="col-lg-2">
													<input type="number" class="form-control" name="insert_jumlah[]" placeholder="Jumlah" required>
												</div>

												<div class="col-lg-1">
													<button class="btn btn-primary" type="button" id="addNewRowPesanan"><i class="fa fa-plus"></i></button>
												</div>
											</div>
										</div>
										<?php } else if($_SESSION["grup"]==5) { //jika Pengguna ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Jenis Pesanan</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2 pesan_makanan" name='insert_jenis_pesanan' style="width: 100%;" required></select>
												</div>	
												<div id="warning_jenis_pesanan" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<?php } ?>
											
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Jenis Lembur</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2" name='insert_jenis_lembur' style="width: 100%;" required>
														<?php foreach($array_daftar_lembur->result_array() as $lembur) { ?>
														<option value='<?= $lembur['nama'] ?>'><?= $lembur['nama'] ?></option>	
														<?php } ?>
													</select>
												</div>	
												<div id="warning_jenis_lembur" class="col-lg-3 text-danger"></div>
											</div>
										</div>

										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Tanggal Lembur</label>
												</div>
												<div class="col-lg-6">
													 <input type="text" class="form-control" name="insert_tanggal_pemesanan" id="insert_tgl_pemesanan">
												</div>
												<div id="warning_tanggal_pemesanan" class="col-lg-3 text-danger"></div>
											</div>
										</div>

										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Jam Lembur</label>
												</div>
												<div class="col-lg-3">
													 <input type="time" class="form-control" name="insert_waktu_pemesanan_mulai" required>
												</div>
												<div class="col-lg-3">
													 <input type="time" class="form-control" name="insert_waktu_pemesanan_selesai" required>
												</div>
												<div id="warning_waktu_pemesanan" class="col-lg-3 text-danger"></div>
											</div>
										</div>

										<!-- # tambahan keterangan, 2021-04-23 -->
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Keterangan</label>
												</div>
												<div class="col-lg-6">
													<textarea class="form-control" name="insert_keterangan" id="insert_keterangan" style="max-width: 100%;"></textarea>
												</div>
												<div id="insert_keterangan" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<!-- # tambahan keterangan, 2021-04-23 -->

										<div class="row">
											<div class="col-lg-9 text-right">
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

				<?php } ?>
				<!-- Modal NP -->
				<div class="modal fade" id="modal_persetujuan" tabindex="-1" role="dialog" aria-labelledby="label_modal_np" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form role="form" action="#" method="post" id="form_persetujuan">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="label_modal_np">Berikan Persetujuan</h4>
								</div>
								<div class="modal-body">
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
												<label>No Pemesanan</label>
												<input type="text" name="no_pemesanan" class="form-control" readonly id="detail_no_pemesanan">
											</div>
										</div>

										<div class="row">
											<div class="col-md-4">
												<label>NP Pemesan</label>
												<input type="text" class="form-control" readonly id="detail_np_pemesan">
											</div>

											<div class="col-md-8">
												<label>Nama </label>
												<input type="text" class="form-control" readonly id="detail_nama_pemesan">
											</div>
										</div>
										
										<div class="row">
											<div class="col-md-12">
												<label>Unit Kerja</label>
												<input type="text" class="form-control" readonly id="detail_unit_kerja">
											</div>
										</div>

										<div class="row">
											<div class="col-md-6">
												<label>Tanggal Pemesanan</label>
												<input type="text" class="form-control" readonly id="detail_tgl_pemesanan">
											</div>
											
											<div class="col-md-6">
												<label>Waktu</label>
												<input type="text" class="form-control" readonly id="detail_waktu_pemesanan">
											</div>
										</div>
																				
										<div class="row">
											<div class="col-md-6">
												<label>Lokasi Lembur</label>
												<input type="text" class="form-control" readonly id="detail_lokasi_lembur">
											</div>

											<div class="col-md-6">
												<label>Jenis Lembur</label>
												<input type="text" class="form-control" readonly id="detail_jenis_lembur">
											</div>
										</div>
										
										<div class="row">
												<div class="col-md-12">
													<label>Keterangan</label>
													<textarea class="form-control" readonly rows="3" id="detail_keterangan"></textarea>
												</div>
										</div>

										<div class="row">
											<br>
											<div class="col-md-12">
												<label>Total Pesanan : </label> <label id="detail_jumlah_pemesanan"></label>
											</div>
										</div>

										<div class="pesanan_data">
											<div class="row">
												<div class="col-md-12">
													<label>Jenis Pesanan : </label>
													<p id="detail_jenis_pemesanan"></p>
												</div>
											</div>

											<div class="row">
												<div class="col-md-12 text-danger">
													<label>Total Harga : </label> <label id="detail_harga">Total Harga : </label>
												</div>
											</div>
										</div>
										
										

										<div class="form-group realisasi_pengeluaran">
											<div class="row">
												<div class="col-md-12">
													<label>Realisasi Pengeluaran : </label>
												</div>
											</div>
											<div class="row">
												<div class="col-md-8">
													<input id="input_pengeluaran" type="number" class="form-control" placeholder="Masukkan Jumlah Pengeluaran" disabled>
												</div>
												<div class="col-md-4">
													<a class="btn btn-primary" onclick="save_pengeluaran()" id="btn-dataToSave" style="display: none"> Simpan</a>
													<a class="btn btn-warning" onclick="edit_pengeluaran()" id="btn-dataToEdit"> Ubah</a>
												</div>
											</div>
										</div>
										
										

										<div class="detail">
											<div class="row">
												<div class="col-md-12">
													<label>Approval</label>
													<textarea type="text" class="form-control" rows="3" readonly id="detail_verified"></textarea>
												</div>
											</div>
											
											
											
											<!--19 01 2022 7648 Tri Wibowo keterangan verified dimatikan diganti detail_keterangan
											<div class="row">
												<div class="col-md-12">
													<label>Keteranganss</label>
													<textarea class="form-control" readonly rows="3" id="detail_keterangan_verified"></textarea>
												</div>
											</div>
											-->
										</div>
									</div>
									<?php if(@$this->akses["persetujuan"]) { ?>
									
									<div class="approval">
										<div class="form-group">
											<label>Approval</label>
											<select class="form-control" name="verified" onchange="cek_approval(this.value)" required>
												<option value='1'>Diterima</option>
												<option value='0'>Ditolak</option>
											</select>
										</div>

										<div class="form-group" id="keterangan_approval">
											<label>Keterangan</label>
											<textarea name='keterangan' class="form-control" rows="3"></textarea>
										</div>
									</div>
									<?php } ?>
								</div>
								<div class="modal-footer">
									<div class="row">
										<?php if(@$this->akses["persetujuan"]) { ?>
										<div class="col-md-10 pull-right">
											<div class="approval">
												<button name='submit' type="submit" value='submit' class="btn btn-primary">Simpan</button>
											</div>
										</div>
										<?php } ?>
										<div class="col-md-2 pull-right">
											<button type="button" class="btn btn-default" data-dismiss="modal">Kembali</button>
										</div>
									</div>
								</div>
							</form>								
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
				<?php if(@$akses["lihat"]) { ?>
				<p id="demo"></p>
				<div class="form-group">
					<div class="row">
						<div class="col-md-3">
							<label>Bulan</label>
							<!--<select id="pilih_bulan_tanggal" class="form-control">-->
							<select class="form-control" id='bulan_tahun' name='bulan_tahun' onchange="refresh_table_serverside()" style="width: 200px;">
								<option value=''></option>
							<?php 
							$tampil_bulan_tahun=date("m-Y");
							foreach ($array_tahun_bulan as $value) {									
								if(!empty($this->session->flashdata('tampil_bulan_tahun'))) {
									$tampil_bulan_tahun=$this->session->flashdata('tampil_bulan_tahun');
								}

								if($tampil_bulan_tahun==$value) {
									$selected='selected';
								} else {
									$selected='';
								} ?>
								<option value='<?php echo $value?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,0,2))." ".substr($value,3,4)?></option>
							<?php } ?>
							</select>
														
						</div>
                        <input type="hidden" name="bulan" value="" id="get_month" />
                        <input type="hidden" name="bulan" value="" id="get_month_per_unit" />
                        
                        <?php if($this->uri->segment(4)=='rekap') { ?>
						<br>
						<div class="col-md-7">
                        	<button class='btn btn-primary pull-right btn-monitoring' data-action="<?= base_url('food_n_go/konsumsi/excel/monitoring'); ?>" data-toggle="modal" data-target="#rekap-lembur">Monitoring Pengiriman Pemesanan Makan Lembur</button> 
                        </div>
						<div class="col-md-2 pull-right">
                        	<button class='btn btn-primary pull-right btn-lembur' data-action="<?= base_url('food_n_go/konsumsi/excel/index'); ?>" data-toggle="modal" data-target="#rekap-lembur">Rekap Lembur</button> 
                        </div>
                        <?php } ?>
					</div>
				</div>

				<div class="form-group">	
					<div class="row">
						<div class="col-lg-12">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data">
								<thead>
									<tr>
										<th class='text-center'>No</th>
										<th class='text-center no-sort'>Nomor<br>Pemesanan</th>
										<th class='text-center'>Tanggal<br>Input</th>
										<th class='text-center'>Tanggal<br>Lembur</th>
										<th class='text-center'>Unit Kerja</th>	
										<th class='text-center'>Jumlah</th>
										<th class='text-center'>Lembur</th>
										<th class='text-center no-sort'>Status</th>
										<th class='text-center no-sort'>Aksi</th>
										
									</tr>
								</thead>
								<tbody>
								
								</tbody>
							</table>
							<!-- /.table-responsive -->
						</div>					
					</div>					
				</div>
			
				<?php } if(@$akses["ubah"] || @$akses["persetujuan"]) { ?>
				<!-- Modal -->
				<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content edit-content">
							<div class="table-responsive-sm">
								<form role="form" action="<?php echo base_url('food_n_go/konsumsi/pemesanan_makan_lembur/save'); ?>" id="formulir_ubah" method="post"> 
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
									</div>
									<div class="modal-body">

									<?php if(@$akses["ubah"] || @$akses["persetujuan"]) { ?>
										<input type="hidden" class="form-control" name="ubah_no_pemesanan" id="ubah_no_pemesanan" required>
									<?php } ?>

									<?php if(@$akses["ubah"]) { ?>
										<?php if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Unit Kerja</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2" id='ubah_unit_kerja' name='insert_unit_kerja' onChange="get_approval_ubah();" style="width: 100%;" requuired>
														<option value=''>Pilih Unit Kerja</option>	
														<?php foreach ($array_daftar_unit as $value) { ?>
															<option value='<?php echo $value['kode_unit']?>'><?php echo $value['kode_unit']." - ".$value['nama_unit']?></option>
														<?php } ?>
													</select>
												</div>	
												<div id="warning_unit_kerja_ubah" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<?php } else if($_SESSION["grup"]==5) { //jika Pengguna ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Penerima</label>
												</div>
												<div class="col-lg-6">
													<input type="text" class="form-control" value="<?= $_SESSION["no_pokok"].' - '.$_SESSION["nama"] ?>">
												</div>	
											</div>
										</div>
										<?php } ?>

										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Pilih Approver</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2" id='ubah_np_atasan' name='insert_np_atasan' style="width: 100%;" required></select>
												</div>	
												<div id="warning_np_atasan_ubah" class="col-lg-3 text-danger"></div>
											</div>
										</div>
									<?php } ?>

									<?php if(@$akses["ubah"] || @$akses["persetujuan"]) { ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Lokasi Lembur</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2" id='ubah_lokasi_lembur' name='insert_lokasi_lembur' style="width: 100%;" onChange="set_lokasi_ubah();" required>
														<option value=''></option>
														<?php foreach ($array_daftar_lokasi->result_array() as $value) { ?>
															<option value='<?php echo $value['id']?>'><?php echo $value['nama']?></option>
														<?php } ?>
													</select>
													<span class="text-danger">Sesuaikan jumlah jenis pesanan sebelum memilih lokasi lembur</span>
												</div>
												<div id="warning_lokasi_lembur_ubah" class="col-lg-3 text-danger"></div>
											</div>
										</div>

										<?php if($_SESSION["grup"]==5) { //jika Pengguna ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Jenis Pesanan</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2 pesan_makanan_ubah" name='insert_jenis_pesanan' id='ubah_jenis_pemesanan' style="width: 100%;" required></select>
												</div>	
												<div id="warning_jenis_pesanan_ubah" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<?php } else { ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Jumlah Pesanan</label>
												</div>
												<div class="col-lg-6">
													<input class="form-control" id='ubah_jumlah_pemesanan' style="width: 100%;" readonly disabled>
												</div>
											</div>
										</div>

										<input type="hidden" id="maxIndexTableUpdate" />
										<div class="form-group" id="bodyPesananUbah">
											
										</div>
										<?php } ?>
									<?php } ?>
									
									<?php if(@$akses["ubah"]) { ?>
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Jenis Lembur</label>
												</div>
												<div class="col-lg-6">
													<select class="form-control select2" name='insert_jenis_lembur' id="ubah_jenis_lembur" style="width: 100%;" required>
														<?php foreach($array_daftar_lembur->result_array() as $lembur) { ?>
														<option value='<?= $lembur['nama'] ?>'><?= $lembur['nama'] ?></option>	
														<?php } ?>
													</select>
												</div>	
												<div id="warning_jenis_lembur_ubah" class="col-lg-3 text-danger"></div>
											</div>
										</div>

										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Tanggal Lembur</label>
												</div>
												<div class="col-lg-6">
													 <input type="text" class="form-control" name="insert_tanggal_pemesanan" id="ubah_tgl_pemesanan" required>
												</div>
												<div id="warning_tanggal_pemesanan_ubah" class="col-lg-3 text-danger"></div>
											</div>
										</div>

										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Jam Lembur</label>
												</div>
												<div class="col-lg-3">
													 <input type="time" class="form-control" name="insert_waktu_pemesanan_mulai" id="ubah_waktu_pemesanan_mulai" required>
												</div>
												<div class="col-lg-3">
													 <input type="time" class="form-control" name="insert_waktu_pemesanan_selesai" id="ubah_waktu_pemesanan_selesai" required>
												</div>
												<div id="warning_waktu_pemesanan_ubah" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										
										<!-- # tambahan keterangan, 2021-04-23 -->
										<div class="form-group">
											<div class="row">
												<div class="col-lg-3">
													<label>Keterangan</label>
												</div>
												<div class="col-lg-6">
													<textarea class="form-control" name="ubah_keterangan" id="ubah_keterangan" style="max-width: 100%;"></textarea>
												</div>
												<div id="warning_keterangan_ubah" class="col-lg-3 text-danger"></div>
											</div>
										</div>
										<!-- # tambahan keterangan, 2021-04-23 -->
									<?php } ?>
										<div class="row">
											<div class="col-lg-9 text-right">
												<button type="submit" class="btn btn-primary" onclick="return cek_simpan_ubah()">Simpan</button>
											</div>
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
		
		<!-- Modal -->
		<div class="modal fade" id="rekap-lembur" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		 	<div class="modal-dialog modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h5 class="modal-title ubah-title" id="exampleModalLongTitle">Rekap Excel</h5>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
		      			<div class="text-center"><h4><strong class="text-danger status-alert"></strong></h4></div>
		      	
		        		<form role="form" action="#" id="form-excel" method="post" target="_blank">
							<div class="form-group">
								<div class="row">
									<div class="col-lg-3">
										<label>Lokasi</label>
									</div>
									<div class="col-lg-7">
										<select id="ex-lokasi" name="lokasi" class="form-control">
											<option value="1">Jakarta</option>
											<option value="2">Karawang</option>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3">
										<label>Dari Tanggal</label>
									</div>
									<div class="col-lg-7">
										 <input type="text" class="form-control" name="tgl_mulai" id="dari-tgl" required>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-3">
										<label>Sampai Tanggal</label>
									</div>
									<div class="col-lg-7">
										 <input type="text" class="form-control" name="tgl_selesai" id="sampai-tgl" required>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12 text-center">
									<button type="submit" class="btn btn-primary btn-cetak" disabled>Cetak</button>
								</div>
							</div>
						</form>
		      		</div>
		    	</div>
		  	</div>
		</div>

		<script src="<?php echo base_url('asset/sweetalert2')?>/sweetalert2.js"></script>
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		
		<script type="text/javascript">	
			var pesanan_ubah,table;

			$('#maxIndexTableAdd').val(0);
			$('#addNewRowPesanan').click(addNewRow);

			$('#multi_select').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
			$('#multi_select_per_unit').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});

			$(document).ready(function() {
				// document.getElementById('get_month').value = $('#bulan_tahun').val();
				// document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();

				<?php if(!empty($this->session->flashdata('warning')) || $this->session->userdata('notif_input')=='success') { ?>
					Swal.fire({
					  	icon: 'success',
					  	title: 'Berhasil'
					});
				<?php 
						$this->session->unset_userdata('notif_input');;
					} 
				?>
				<?php if(!empty($this->session->flashdata('warning')) || $this->session->userdata('notif_input')=='warning') { ?>
					Swal.fire({
					  	icon: 'warning',
					  	title: 'Gagal, terdapat kesalahan data. Anda Tidak Mendapatkan Makanan Lembur, Minimal Lembur 3 jam'
					});
				<?php 
						$this->session->unset_userdata('notif_input');;
					} 
				?>
				<?php if(!empty($this->session->flashdata('failed')) || $this->session->userdata('notif_input')=='failed') { ?>
					Swal.fire({
					  	icon: 'error',
					  	title: 'Gagal'
					});
				<?php 
						$this->session->unset_userdata('notif_input');;
					} 
				?>
                
                // $(function () {
                    $('#insert_tgl_pemesanan').datetimepicker({
                        format: 'DD-MM-Y',
						minDate : '<?php echo date('Y-m-d') ?>',
                    });

                    $('#ubah_tgl_pemesanan').datetimepicker({
                        format: 'DD-MM-Y',
						minDate : '<?php echo date('Y-m-d') ?>',
                    });

                    //rekap excel
                    $('#dari-tgl, #sampai-tgl').datetimepicker({
                        format: 'DD-MM-Y'
                    });

		            <?php if($_SESSION["grup"]==5) { ?>
                    $.ajax({
			            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_makan_lembur/get_apv');?>",
			            type: "POST",
			            dataType: "json",
			            data: {kode_unit: '<?= $_SESSION['kode_unit'] ?>'},
			            success: function(response) {
	                        for (var i = 0; i < response.length; i++) {
	                            $("#insert_np_atasan").append($("<option></option>").attr("value", response[i]["no_pokok"]).text(response[i]["no_pokok"] + " - " + response[i]["nama"]));
	                            if(response[i]["kode_unit"]=='<?= $_SESSION['kode_unit'] ?>'){
	                                $("#insert_np_atasan").val(response[i]["no_pokok"]);
	                                $("#insert_np_atasan").trigger('change');
	                            }
	                        }
	                        $('.select2').select2();
			            }
			        });
			        <?php } ?>
                // });
                
                $('.select2').select2();
				// $('#tabel_data').DataTable().destroy();
				$('#keterangan_approval').hide();						
				refresh_table_serverside();
			});	
			
			function refresh_table_serverside() {
				// document.getElementById('get_month').value = $('#bulan_tahun').val();
				// document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
				$('#tabel_data').DataTable().destroy();				
				table_serverside();
			}
			
			function refresh_bulan_tahun() {
				$('#tabel_data').DataTable().destroy();	
				table_serverside();
			}

			function cek_approval(approval) {
				if (approval==='0') {
					$('#keterangan_approval').show();
				} else {
					$('#keterangan_approval').hide();
				}
			}

            function get_approval(){
                var kode_unit_pemesan = $('#insert_unit_kerja').children("option:selected").val();
                var nama_unit_pemesan = $('#insert_unit_kerja').children("option:selected").text();
                
                $("#insert_np_atasan").empty();
		        
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_makan_lembur/get_apv');?>",
		            type: "POST",
		            dataType: "json",
		            data: {kode_unit: kode_unit_pemesan},
		            success: function(response) {
                        for (var i = 0; i < response.length; i++) {
                            $("#insert_np_atasan").append($("<option></option>").attr("value", response[i]["no_pokok"]).text(response[i]["no_pokok"] + " - " + response[i]["nama"]));
                            if(response[i]["kode_unit"]==kode_unit_pemesan){
                                $("#insert_np_atasan").val(response[i]["no_pokok"]);
                                $("#insert_np_atasan").trigger('change');
                            }
                        }
                        $('.select2').select2();
		            }
		        });
            }

            function get_approval_ubah(){
                var kode_unit_pemesan = $('#ubah_unit_kerja').children("option:selected").val();
                var nama_unit_pemesan = $('#ubah_unit_kerja').children("option:selected").text();
                
                $("#ubah_np_atasan").empty();
		        
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_makan_lembur/get_apv');?>",
		            type: "POST",
		            dataType: "json",
		            data: {kode_unit: kode_unit_pemesan},
		            success: function(response) {
                        for (var i = 0; i < response.length; i++) {
                            $("#ubah_np_atasan").append($("<option></option>").attr("value", response[i]["no_pokok"]).text(response[i]["no_pokok"] + " - " + response[i]["nama"]));
                            if(response[i]["kode_unit"]==kode_unit_pemesan){
                                $("#ubah_np_atasan").val(response[i]["no_pokok"]);
                                $("#ubah_np_atasan").trigger('change');
                            }
                        }
                        $('.select2').select2();
		            }
		        });
            }

            function set_lokasi(){
                var lokasi = $('#insert_lokasi_lembur').children("option:selected").text();
                var lastSelected = getLastSelected(); // tambahan 2021-04-23, ambil last selected value sebelum ada request
                $(".pesan_makanan").empty();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_makan_lembur/set_lokasi');?>",
		            type: "POST",
		            dataType: "json",
		            data: {lokasi: lokasi},
		            success: function(response) {
                        for (var i = 0; i < response.makanan.length; i++) {
                            $(".pesan_makanan").append($("<option></option>").attr("value", response.makanan[i]["id"]).text(response.makanan[i]["nama"]));
                        }
                        $('.select2').select2();

						// tambahan 2021-04-23, set ke nilai sebelum ada tambahan row
						for( let i=0; i<$('.pesan_makanan').length; i++ ){
							if( typeof lastSelected[i]!=='undefined' )
								$('.pesan_makanan')[i].value = lastSelected[i];
						}
						$('.pesan_makanan').trigger('change');
						// END tambahan 2021-04-23
		            }
		        });
            }

            function set_lokasi_ubah(){
                var lokasi = $('#ubah_lokasi_lembur').children("option:selected").text();
                
                $(".pesan_makanan_ubah").empty();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_makan_lembur/set_lokasi');?>",
		            type: "POST",
		            dataType: "json",
		            data: {lokasi: lokasi},
		            success: function(response) {
                        for (var i = 0; i < response.makanan.length; i++) {
                            $(".pesan_makanan_ubah").append($("<option></option>").attr("value", response.makanan[i]["id"]).text(response.makanan[i]["nama"]));
                        }
                        
					    for(i=0; i<pesanan_ubah.length; i++) {
					    	pesanan_ubah_2 = pesanan_ubah[i];
					    	$('#ubah_jumlah_pemesanan'+i).val(pesanan_ubah_2['jumlah']);
					    	$('#ubah_jenis_pemesanan'+i).val(pesanan_ubah_2['id_makanan']).change();
					    }
                        $('.select2').select2();
		            }
		        });
            }
			
			function addNewRow(){
				var lastIndexTable = Number($('#maxIndexTableAdd').val());

				lastIndexTable = lastIndexTable + 1;
				var newRow ='<div class="row" id="pesanan'+ lastIndexTable +'">'+
								'<div class="col-lg-3"></div>'+
								'<div class="col-lg-4">'+
									'<select class="form-control select2 pesan_makanan" name="insert_jenis_pesanan[]" style="width: 100%;" required></select>'+
								'</div>'+
								'<div class="col-lg-2">'+
									'<input type="number" class="form-control" name="insert_jumlah[]" placeholder="Jumlah" required>'+
								'</div>'+
								'<div class="col-lg-1">'+
									'<button class="btn btn-danger" type="button" onclick="deleteRow(\'pesanan'+ lastIndexTable +'\')"><i class="fa fa-trash-o"></i></button>'+
								'</div>'+
							'</div>';
				$('#bodyPesanan').append(newRow);
				$('.select2').select2();
				set_lokasi();
				$('#maxIndexTableAdd').val(lastIndexTable);
			}
			
			function updateNewRow(){
				var lastIndexTableUpdate = Number($('#maxIndexTableUpdate').val());

				lastIndexTableUpdate = lastIndexTableUpdate + 1;
				var newRow ='<div class="row" id="pesananUbah'+ lastIndexTableUpdate +'">'+
								'<div class="col-lg-3"></div>'+
								'<div class="col-lg-4">'+
									'<select class="form-control select2 pesan_makanan_ubah" name="insert_jenis_pesanan[]" style="width: 100%;" required></select>'+
								'</div>'+
								'<div class="col-lg-2">'+
									'<input type="number" class="form-control" name="insert_jumlah[]" placeholder="Jumlah" required>'+
								'</div>'+
								'<div class="col-lg-1">'+
									'<button class="btn btn-danger" type="button" onclick="deleteRow(\'pesananUbah'+ lastIndexTableUpdate +'\')"><i class="fa fa-trash-o"></i></button>'+
								'</div>'+
							'</div>';
				$('#bodyPesananUbah').append(newRow);
				$('.select2').select2();
				set_lokasi_ubah();
				$('#maxIndexTableUpdate').val(lastIndexTableUpdate);
			}
			
			function deleteRow(tag){
				$('#'+tag).remove();
			}

			function table_serverside() {
				var bulan_tahun = $('#bulan_tahun').val();
				
				//datatables
				table = $('#tabel_data').DataTable({
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"orderable": false, //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo base_url($url_table)?>"+bulan_tahun,
						"type": "POST"
					},

					//Set column definition initialisation properties.
					// "columnDefs": [{ 
					// 	"targets": 'no-sort',
					// 	"orderable": false,
					// }],

					//Set column responsive.
					"responsive": true  //set responsive on

				});
			}

			function tampil_data_approval(element){
			    $('.detail').hide();
			    $('.pesanan_data').show();
			    $('.approval').show();
			    $('#label_modal_np').text('Berikan Persetujuan');

			    $.ajax({
			    	<?php if (($_SESSION['grup']=='5' || $_SESSION['grup']=='4') && $persetujuan!='1') { ?>
			    	//url: "pemesanan_makan_lembur/detail",
					url: "<?php echo base_url("food_n_go/konsumsi/pemesanan_makan_lembur/detail")?>",
			    	<?php } else { ?>
			        url: "detail",
			    	<?php } ?>
			        type: "POST",
			        dataType: "json",
			        data: {no_pemesanan: element.dataset.no},
			        success: function(response) {
					    $('#detail_no_pemesanan').val(element.dataset.no);
					    $('#detail_np_pemesan').val(response.np_pemesan);
					    $('#detail_nama_pemesan').val(response.nama_pemesan);
					    $('#detail_jenis_lembur').val(response.jenis_lembur);
					    $('#detail_jenis_pemesanan').html(response.jenis_pemesanan);
					    $('#detail_harga').text(response.total_harga);
					    $('#detail_lokasi_lembur').val(response.lokasi);
					    $('#detail_unit_kerja').val(response.nama_unit);
					    $('#detail_jumlah_pemesanan').text(response.jumlah_pemesanan+' Orang');
					    $('#detail_tgl_pemesanan').val(response.tanggal_pemesanan);
					    $('#detail_waktu_pemesanan').val(response.waktu_pemesanan_mulai+' s/d '+response.waktu_pemesanan_selesai);
					    $('#detail_keterangan').text(response.keterangan);
						
					    if("<?= $_SESSION['grup'] ?>"=='5' && response.np_atasan=="<?= $_SESSION['no_pokok'] ?>") {
					    	$('.pesanan_data').hide();
					    }

					    if(response.verified==null || response.verified=='1') {
					    	$('#form_persetujuan').attr("action" ,"persetujuan");
						} else{
					    	$('#form_persetujuan').attr("action" ,"#");
					    }
					}
				});
			}

			function tampil_data_detail(element){
			    $('.approval').hide();
			    $('.detail').show();
			    $('.pesanan_data').show();
			    $('#label_modal_np').text('Detail Pemesanan');

			    $.ajax({
			        <?php if (($_SESSION['grup']=='5' || $_SESSION['grup']=='4') && $persetujuan!='1') { ?>
					url: "<?php echo base_url("food_n_go/konsumsi/pemesanan_makan_lembur/detail")?>",
			    	<?php } else { ?>
			        url: "detail",
			    	<?php } ?>
			        type: "POST",
			        dataType: "json",
			        data: {no_pemesanan: element.dataset.no},
			        success: function(response) {
					    $('#detail_no_pemesanan').val(element.dataset.no);
					    $('#detail_np_pemesan').val(response.np_pemesan);
					    $('#detail_nama_pemesan').val(response.nama_pemesan);
					    $('#detail_jenis_lembur').val(response.jenis_lembur);
					    $('#detail_lokasi_lembur').val(response.lokasi);
					    $('#detail_jenis_pemesanan').html(response.jenis_pemesanan);
					    $('#detail_harga').text(response.total_harga);
					    $('#detail_unit_kerja').val(response.nama_unit);
					    $('#detail_jumlah_pemesanan').text(response.jumlah_pemesanan+' Orang');
					    $('#detail_tgl_pemesanan').val(response.tanggal_pemesanan);
					    $('#detail_waktu_pemesanan').val(response.waktu_pemesanan_mulai+' s/d '+response.waktu_pemesanan_selesai);
					    $('#detail_keterangan_verified').val(response.keterangan_verified);
						$('#detail_keterangan').val(response.keterangan);

					    if("<?= $_SESSION['grup'] ?>"=='5' && response.np_atasan=="<?= $_SESSION['no_pokok'] ?>") {
					    	$('.pesanan_data').hide();
					    }
					    
					    $('.realisasi_pengeluaran').css('display','none');
					    
					    if(response.verified==null) {
					    	$('#detail_verified').val('Menunggu Persetujuan Atasan : '+response.nama_atasan+' ('+response.np_atasan+')');
					    } else if(response.verified=='1') {
					    	$('#detail_verified').val('- Disetujui Atasan : '+response.nama_atasan+' ('+response.np_atasan+') pada '+response.waktu_verified_atasan+'\n- Menunggu Persetujuan Admin');
					    } else if(response.verified=='2') {
					    	$('#detail_verified').val('Ditolak Atasan : '+response.nama_atasan+' ('+response.np_atasan+') pada '+response.waktu_verified_atasan);
					    } else if(response.verified=='3') {
					    	$('#detail_verified').val('- Disetujui Atasan : '+response.nama_atasan+' ('+response.np_atasan+') pada '+response.waktu_verified_atasan+'\n- Disetujui Seksi Yanum pada '+response.waktu_verified_admin);

							<?php if ($_SESSION['grup']=='5' || $_SESSION['grup']=='4') { ?>
								$('.realisasi_pengeluaran').css('display','none');
							<?php } else { ?>
								$('.realisasi_pengeluaran').css('display','');
								$('#input_pengeluaran').val(response.realisasi_pengeluaran);
							<?php } ?>

					    } else if(response.verified=='4') {
					    	$('#detail_verified').val('- Disetujui Atasan : '+response.nama_atasan+' ('+response.np_atasan+') pada '+response.waktu_verified_atasan+'\n- Ditolak Seksi Yanum pada '+response.waktu_verified_admin);
					    } else if(response.verified=='6') {
					    	$('#detail_verified').val('- Disetujui Atasan : '+response.nama_atasan+' ('+response.np_atasan+') pada '+response.waktu_verified_atasan+'\n- Disetujui Seksi Yanum pada '+response.waktu_verified_admin+'\n- Dibatalkan Seksi Yanum pada '+response.waktu_batal);
						} else{
					    	$('#detail_verified').val('Waktu Pemesanan Ditolak!');
					    }

			        }
			    });
			}

			function edit_pengeluaran() {
				$('#btn-dataToSave').css('display', '');
				$('#btn-dataToEdit').css('display', 'none');
				$('#input_pengeluaran').prop('disabled', false);
			}

			function save_pengeluaran() {
				realisasi = $('#input_pengeluaran').val();
				no_pemesanan = $('#detail_no_pemesanan').val();

				<?php if ($_SESSION['grup']!='5' && $_SESSION['grup']!='4') { ?>
				$.ajax({
					method: "POST",
					dataType: 'JSON',
					data: { no_pemesanan: no_pemesanan, realisasi: realisasi },
					url: "save_realisasi",
				})
				.done(function( msg ) {
					$('#btn-dataToSave').css('display', 'none');
					$('#btn-dataToEdit').css('display', '');
					$('#input_pengeluaran').prop('disabled', true);
				});
				<?php } ?>
			}

			function ubah(element) {
				$.ajax({
					<?php if (($_SESSION['grup']=='5' || $_SESSION['grup']=='4') && $persetujuan!='1') { ?>
			    	url: "<?php echo base_url("food_n_go/konsumsi/pemesanan_makan_lembur/detail")?>",
			    	<?php } else { ?>
			        url: "detail",
			    	<?php } ?>
			        type: "POST",
			        dataType: "json",
			        data: {no_pemesanan: element.dataset.no},
			        success: function(response) {
			        	console.log(response);
			        	$('#bodyPesananUbah').empty();

			        	var kode_unit = response.kode_unit;
			        	$.ajax({
				            //url: "pemesanan_makan_lembur/get_apv",
							url: "<?php echo base_url("food_n_go/konsumsi/pemesanan_makan_lembur/get_apv")?>",
				            type: "POST",
				            dataType: "json",
				            data: {kode_unit: kode_unit},
				            success: function(result) {
			                    for (var i = 0; i < result.length; i++) {
			                        $("#ubah_np_atasan").append($("<option></option>").attr("value", result[i]["no_pokok"]).text(result[i]["no_pokok"] + " - " + result[i]["nama"]));
			                        if(result[i]["kode_unit"]==kode_unit){
			                            $("#ubah_np_atasan").val(result[i]["no_pokok"]);
			                            $("#ubah_np_atasan").trigger('change');
			                        }
			                    }
			                    $('.select2').select2();
				            }
				        });

					    $('#ubah_no_pemesanan').val(element.dataset.no);
					    $('#ubah_unit_kerja').val(response.kode_unit);
					    $('#ubah_jumlah_pemesanan').val(response.jumlah_pemesanan);
					    $('#ubah_np_atasan').val(response.np_atasan).change();
					    // $('#ubah_jenis_pemesanan').val(response.id_makanan).change();
					    $('#ubah_jenis_lembur').val(response.jenis_lembur).change();
					    $('#ubah_waktu_pemesanan_mulai').val(response.waktu_pemesanan_mulai);
					    $('#ubah_waktu_pemesanan_selesai').val(response.waktu_pemesanan_selesai);
					    tgl_lembur = response.tanggal_pemesanan;
					    newdate = tgl_lembur.split("-").reverse().join("-");
					    $('#ubah_tgl_pemesanan').val(newdate);
					    $('#ubah_keterangan').val(response.keterangan); // tambahan keterangan, 2021-04-23

					    pesanan = JSON.parse(response.jenis_pesanan);
					    $('#maxIndexTableUpdate').val(pesanan.length);

						for(i=0; i<pesanan.length; i++) {
							if (i==0) {
								if (response.jumlah_pemesanan > 1) {
									add_buton = '<div class="col-lg-2">'+
										'<input type="number" class="form-control" name="insert_jumlah[]" placeholder="Jumlah" id="ubah_jumlah_pemesanan'+i+'" required>'+
									'</div>'+
									'<div class="col-lg-1"><button class="btn btn-primary" type="button" onclick="updateNewRow()"><i class="fa fa-plus"></i></button></div></div>';
								} else {
									add_buton = '<div class="col-lg-2">'+
										'<input type="number" class="form-control" name="insert_jumlah[]" placeholder="Jumlah" id="ubah_jumlah_pemesanan'+i+'" readonly required>'+
									'</div>'+
									'</div>';
								}

								$('#bodyPesananUbah').append('<div class="row">'+
									'<div class="col-lg-3">'+
										'<label>Jenis Pesanan</label>'+
									'</div>'+
									'<div class="col-lg-4">'+
										'<select class="form-control select2 pesan_makanan_ubah" name="insert_jenis_pesanan[]" id="ubah_jenis_pemesanan'+i+'" style="width: 100%;" required></select>'+
									'</div>'+add_buton);
							} else {
								var newRow ='<div class="row" id="pesananUbah'+i+'">'+
									'<div class="col-lg-3"></div>'+
									'<div class="col-lg-4">'+
										'<select class="form-control select2 pesan_makanan_ubah" name="insert_jenis_pesanan[]" id="ubah_jenis_pemesanan'+i+'" style="width: 100%;" required></select>'+
									'</div>'+
									'<div class="col-lg-2">'+
										'<input type="number" class="form-control" name="insert_jumlah[]" placeholder="Jumlah" id="ubah_jumlah_pemesanan'+i+'" required>'+
									'</div>'+
									'<div class="col-lg-1">'+
										'<button class="btn btn-danger" type="button" onclick="deleteRow(\'pesananUbah'+ i +'\')"><i class="fa fa-trash-o"></i></button>'+
									'</div>'+
								'</div>';
								$('#bodyPesananUbah').append(newRow);
							}
						}
						
					    $('#ubah_lokasi_lembur').val(response.lokasi_lembur).change();

					    pesanan_ubah = pesanan;

						$('.select2').select2();
			        }
			    });
			}

			function batal(no){
				Swal.fire({
					title: 'Apakah anda yakin ingin membatalkan pesanan ini?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ya, batalkan!'
				}).then((result) => {
					if (result.value) {
						$.ajax({
					        type: "POST",
					        url: "batal",
					        data: { 'no': no},
					        cache: false,
					        success: function(response) {
					        	obj = JSON.parse(response);
							    Swal.fire(
							      	obj.judul,
							      	obj.txt,
							      	obj.alert
							   	)
							   	refresh_table_serverside();
							},
							failure: function (response) {
							    Swal.fire(
							      	'Gagal!',
							      	'Pesanan tidak dibatalkan.',
							      	'error'
							   	)
							}
						})
					}
				})
			}

			// function tambahan 2021-04-23, ambil last value selected sebelum ada perubahan
			function getLastSelected(){
				let selected = [];
				for(let item of $('.pesan_makanan')){
					selected.push(item.value);
				}
				return selected;
			}
			// END function tambahan 2021-04-23
		</script>

		<script type="text/javascript">
			function cekData(){
				var tgl_mulai = $('#dari-tgl').val();
				var tgl_selesai = $('#sampai-tgl').val();
				var lokasi = $('#ex-lokasi').val();
				
				if(tgl_mulai != '' && tgl_selesai != '' && lokasi != ''){
					$.ajax({
						url: '<?= base_url('food_n_go/konsumsi/excel/cekData') ?>',
						type: 'POST',
						data: {tgl_mulai: tgl_mulai, tgl_selesai: tgl_selesai, lokasi: lokasi},
						success: function(data) {
							if(data.response == 'kosong') {
								$('.status-alert').text('Data yang anda cari tidak ditemukan');
								$('.btn-cetak').prop('disabled', true);
							}else{
								$('.btn-cetak').prop('disabled', false);
								$('.status-alert').text('');
							}
						}
					});
				}
			}

			$(document).ready(function() {
				$(document).on('change', '#ex-lokasi', function() {
					cekData()
				});

				$(document).on('blur', '#dari-tgl, #sampai-tgl', function() {
					cekData()
				});

				$(document).on('blur', '#dari-tgl, #sampai-tgl', function() {
					cekData()
				});

				$(document).on('click', '.btn-lembur, .btn-monitoring', function() {
					$('#form-excel').prop('action', $(this).data('action'));
				});
			})
		</script>