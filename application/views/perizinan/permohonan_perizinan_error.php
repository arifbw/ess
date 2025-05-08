<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
        <link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css') ?>" rel="stylesheet" />

        <link href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" rel="stylesheet" />

        <!--Tri WIbowo 7648 24 12 2021, pakai css yang sudah ada di local -->
        <!--<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css') ?>" />-->

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

        		<?php if (count($perizinan_belum_approve_atasan) > 0) { ?>
        			<div class="row">
        				<div class="alert alert-warning">
        					Permohonan yang <b>belum diapprove</b> Atasan:
        					<?php
							foreach ($perizinan_belum_approve_atasan as $key) {
								$tanggal_izin = $key->start_date != null ? tanggal_indonesia($key->start_date) : tanggal_indonesia($key->end_date);
								$kalimat = "- <b>{$key->np_karyawan} {$key->nama}</b>, {$key->nama_izin}, Tanggal <b>{$tanggal_izin}</b>. Belum diapprove ";
								if ($key->approval_1_status == null) {
									$kalimat .= "<b>{$key->approval_1_np} {$key->approval_1_nama}</b>, ";
								}
								if ($key->approval_2_np != null && $key->approval_2_status == null) {
									$kalimat .= "<b>{$key->approval_2_np} {$key->approval_2_nama}</b>";
								}
								echo "<br><span class='detail_button' data-toggle='modal' data-target='#modal_detail' data-id='" . $key->id . "' data-tgl='" . ($key->start_date != null ? $key->start_date : $key->end_date) . "'>{$kalimat}</span>";
							}
							?>
        				</div>
        			</div>
        		<?php } ?>

        		<?php
				if (!empty($this->session->flashdata('success'))) {
				?>
        			<div class="alert alert-success alert-dismissable">
        				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        				<?php echo $this->session->flashdata('success'); ?>
        			</div>
        		<?php
				}
				if (!empty($this->session->flashdata('warning'))) {
				?>
        			<div class="alert alert-danger alert-dismissable">
        				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        				<?php echo $this->session->flashdata('warning'); ?>
        			</div>
        		<?php
				}
				/*if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}*/
				if (@$akses["tambah"]) {
				?>
        			<div class="row">
        				<div class="panel panel-default">
        					<div class="panel-heading" id="tambah-panel-color-izin">
        						<h4 class="panel-title">
        							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul; ?></a>
        						</h4>
        					</div>
        					<div id="collapseOne" class="panel-collapse collapse">
        						<div class="panel-body">

        							<form role="form" action="<?php echo base_url(); ?>perizinan/permohonan_perizinan/action_insert_perizinan" id="formulir_tambah" method="post">

        								<div class="row">
        									<div class="form-group">
        										<div class="col-lg-2">
        											<label>NP Karyawan</label>
        										</div>
        										<div class="col-lg-7">
        											<!--<input class="form-control" name="np_karyawan" id="np_karyawan" onChange="getNama()" required> kode_unit_by_np-->
        											<select class="form-control select2" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
        												<option value=''></option>
        												<?php
														foreach ($array_daftar_karyawan->result_array() as $value) {
														?>
        													<option value='<?php echo $value['no_pokok'] ?>'><?php echo $value['no_pokok'] . " " . $value['nama'] ?></option>

        												<?php
														}

														foreach ($array_daftar_outsource as $value) {
															echo '<option value="' . $value['np_karyawan'] . '">' . $value['np_karyawan'] . ' ' . $value['nama'] . '</option>';
														}
														?>
        											</select>
        										</div>
        										<!--
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>	
															-->
        									</div>
        								</div>

        								<!--<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label></label>
															</div>
															<div class="col-lg-7">
																 <textarea class="form-control" name="nama" id="nama" rows="5" readonly required></textarea>
															</div>														
														</div>
													</div>-->

        								<div class="row" id="">
        									<div class="form-group">
        										<div class="col-lg-2">
        											<label>Jenis Izin</label>
        										</div>
        										<div class="col-lg-7">
        											<select class="form-control" name="absence_type" onchange="take_action_date(); show_approval();" id="add_absence_type" required>
        												<option value="">-- Pilih jenis perizinan --</option>
        												<?php foreach ($jenis_izin as $row) { ?>
        													<option value="<?= $row->kode_pamlek . '|' . $row->kode_erp ?>"><?= $row->nama ?></option>
        												<?php } ?>
        											</select>
        										</div>
        									</div>
        								</div>

        								<?php
										$bulan_lalu = $data_tanggal	= date('Y-m-d', strtotime('-1 months', strtotime(date('Y-m-d'))));
										$sudah_cutoff = sudah_cutoff($bulan_lalu);

										if ($sudah_cutoff) {
											$min = date('Y-m') . "-01";
										} else {
											$min = '';
										}

										?>
        								<div id="form-start-date" style="display: block;">
        									<div class="row">
        										<div class="form-group">
        											<div class="col-lg-2">
        												<label>Start Date</label>
        											</div>
        											<div class="col-lg-7">
        												<input type="text" class="form-control" name="start_date" id="start_date" required>
        											</div>
        										</div>
        									</div>

        									<div class="row">
        										<div class="form-group">
        											<div class="col-lg-2">
        												<label>Start Time</label>
        											</div>
        											<div class="col-lg-7">
        												<input type="text" class="form-control datetimepicker5" name="start_time" id="start_time" required>
        											</div>
        										</div>
        									</div>
        								</div>

        								<div class="row">
        									<div class="form-group">
        										<div class="col-lg-2">
        											<label>End Date</label>
        										</div>
        										<div class="col-lg-7">
        											<input type="text" class="form-control" name="end_date" id="end_date" required>
        										</div>
        									</div>
        								</div>

        								<div class="row">
        									<div class="form-group">
        										<div class="col-lg-2">
        											<label>End Time</label>
        										</div>
        										<div class="col-lg-7">
        											<input type="text" class="form-control datetimepicker5" name="end_time" required>
        										</div>
        									</div>
        								</div>

        								<!-- <div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Atasan 1</label>								
															</div>
															<div class="col-lg-7">
                                                                <select class="form-control select2" name="approval_1_np" id="approval_1_np" style="width: 100%" onchange="fill_jabatan(this,'approval_1_jabatan')"></select>
															</div>								
														</div>
													</div>
                                                    
                                                    <div class="row">
														<div class="form-group">
															<div class="col-lg-2"></div>
															<div class="col-lg-7">
																<input class="form-control" name="approval_1_jabatan" id="approval_1_jabatan" readonly>
															</div>								
														</div>
													</div>
                                                    
                                                    <div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Atasan 2</label>								
															</div>
                                                                <select class="form-control select2" name="approval_2_np" id="approval_2_np" style="width: 100%" onchange="fill_jabatan(this,'approval_2_jabatan')"></select>
															</div>								
														</div>
													</div>
                                                    
                                                    <div class="row">
														<div class="form-group">
															<div class="col-lg-2"></div>
															<div class="col-lg-7">
																<input class="form-control" name="approval_2_jabatan" id="approval_2_jabatan" readonly>
															</div>								
														</div>
													</div> -->
        								<div id="atasan_1" style="display:none;">
        									<div class="form-group row">
        										<div class="col-lg-2">
        											<label>NP Atasan 1</label>
        										</div>
        										<div class="col-lg-7">
        											<input class="form-control" name="approval_1_np" id="approval_1_np" value="" onchange="getNamaAtasan1()" min="4" required>
        										</div>
        									</div>

        									<div class="form-group row">
        										<div class="col-lg-2">
        											<label></label>
        										</div>
        										<div class="col-lg-7">
        											<input class="form-control" name="approval_1_input" id="approval_1_input" value="" readonly required>
        										</div>
        									</div>

        									<div class="form-group row">
        										<div class="col-lg-2">
        											<label></label>
        										</div>
        										<div class="col-lg-7">
        											<input class="form-control" name="approval_1_input_jabatan" id="approval_1_input_jabatan" readonly required><small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>
        										</div>
        									</div>
        								</div>

        								<div id="atasan_2" style="display:none;">
        									<div class="form-group row">
        										<div class="col-lg-2">
        											<label>NP Atasan 2</label>
        										</div>
        										<div class="col-lg-7">
        											<input class="form-control" name="approval_2_np" id="approval_2_np" value="" onchange="getNamaAtasan2()" min="4" required>
        										</div>
        									</div>

        									<div class="form-group row">
        										<div class="col-lg-2">
        											<label></label>
        										</div>
        										<div class="col-lg-7">
        											<input class="form-control" name="approval_2_input" id="approval_2_input" value="" readonly required>
        										</div>
        									</div>

        									<div class="form-group row">
        										<div class="col-lg-2">
        											<label></label>
        										</div>
        										<div class="col-lg-7">
        											<input class="form-control" name="approval_2_input_jabatan" id="approval_2_input_jabatan" readonly required><small class="form-text text-muted">Atasan Langsung</small><strong> (wajib diisi)</strong>
        										</div>
        									</div>
        								</div>

        								<div class="row" id="">
        									<div class="form-group">
        										<div class="col-lg-2">
        											<label>Pos Yang Dilewati</label>
        										</div>
        										<div class="col-lg-7">
        											<select class="form-control select2" name="pos[]" multiple id="add_pos" required style="width:100%">
        												<?php foreach ($pos as $row) { ?>
        													<option value="<?= $row->id ?>"><?= $row->nama ?></option>
        												<?php } ?>
        											</select>
        										</div>
        									</div>
        								</div>

        								<!-- tambahan untuk alasan, 2021-03-10 -->
        								<div class="form-group row">
        									<div class="col-lg-2">
        										<label>Alasan</label>
        									</div>
        									<div class="col-lg-7">
        										<input class="form-control" name="alasan" id="alasan" required>
        									</div>
        								</div>
        								<!-- END tambahan untuk alasan, 2021-03-10 -->

        								<div class="row">
        									<div class="col-lg-9 text-right">
        										<input type="submit" name="submit" value="submit" class="btn btn-primary">
        									</div>
        								</div>
        							</form>


        						</div>
        					</div>
        				</div>
        				<!-- /.col-lg-12 -->
        			</div>
        			<!-- /.row -->

        			<!-- Modal NP -->
        			<div class="modal fade" id="modal_np" tabindex="-1" role="dialog" aria-labelledby="label_modal_np" aria-hidden="true">
        				<div class="modal-dialog">
        					<div class="modal-content">

        						<div class="modal-header">
        							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        							<h4 class="modal-title" id="label_modal_np">Daftar List NP <?php echo $judul; ?></h4>
        						</div>
        						<div class="modal-body" align='center'>
        							<textarea name='list_np' id='list_np' rows="10" cols="50" readonly></textarea>
        						</div>
        					</div>
        					<!-- /.modal-content -->
        				</div>
        				<!-- /.modal-dialog -->
        			</div>
        			<!-- /.modal -->
        		<?php
				}

				if ($this->akses["lihat"]) {
				?>

        			<!-- filter bulan -->
        			<div class="form-group">
        				<div class="row">
        					<form id="forms">
        						<div class="col-lg-6">
        							<label>Jenis Izin</label>
        							<?php foreach ($jenis_izin as $row) { ?>
        								<div class="checkbox">
        									<label>
        										<input name='izin_<?= $row->kode_pamlek ?>' id='izin_<?= $row->kode_pamlek ?>' class='filter_jenis' type="checkbox" value="1" onclick='refresh_table_serverside()'> <?= $row->nama ?>
        									</label>
        								</div>
        							<?php } ?>
        							<!--                                    <input type="hidden" id="get_jenis">-->
        						</div>
        						<!--<div class="col-lg-3">
	                                    <label>Bulan</label>
	                                    <select id="pilih_bulan_tanggal" class="form-control">
	                                    <select class="form-control" id='bulan_tahun' name='bulan_tahun'  onchange="refresh_table_serverside()" style="width: 200px;">
	                                        <option value="ess_perizinan"></option>
	                                    <?php $count = 1;
										foreach ($array_tahun_bulan as $value) {
											$explode_value = explode('_', $value->TABLE_NAME);
											$bulan_tahun_text = id_to_bulan($explode_value[3]) . ' ' . $explode_value[2];
										?>
	                                        <option value="<?= $value->TABLE_NAME ?>"><?= $bulan_tahun_text ?></option>	
	                                    <?php
											$count++;
										}
										?>
	                                    </select>
	                                </div>-->
        						<div class="col-lg-3">
        							<label>Range Tanggal</label>
        							<input class="form-control" id='date_range' name='dates' onchange="refresh_table_serverside()" style="width: 200px;">
        						</div>
        					</form>
        					<div class="col-md-3">
        						<div style="padding-top: 25px">
        							<button type="button" onClick="otoritas()" class="btn btn-success"><i class="fa fa-print"></i> Cetak</button>
        						</div>
        						<!--begin: Modal Inactive -->
        						<div class="modal fade" id="show_otoritas" srole="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        							<div class="modal-dialog modal-md" role="document">
        								<form method="post" target="_blank" action="<?php echo base_url('perizinan/permohonan_perizinan_x/cetak') ?>">
        									<div class="modal-content">
        										<div class="modal-header">
        											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        											<h4 class="modal-title" id="label_modal_ubah">Pilih Otoritas</h4>
        										</div>
        										<div class="modal-body">
        											<input type="hidden" name="bulan" value="" id="get_month" />
        											<input type="hidden" name="izin_0" value="" id="get_izin_0" />
        											<input type="hidden" name="izin_C" value="" id="get_izin_C" />
        											<input type="hidden" name="izin_E" value="" id="get_izin_E" />
        											<input type="hidden" name="izin_F" value="" id="get_izin_F" />
        											<input type="hidden" name="izin_G" value="" id="get_izin_G" />
        											<input type="hidden" name="izin_H" value="" id="get_izin_H" />
        											<!--<input type="hidden" name="izin_TM" value="" id="get_izin_TM" />
														<input type="hidden" name="izin_TK" value="" id="get_izin_TK" />
														-->
        											<select multiple="multiple" class="form-control select2" id="multi_select" name='np_karyawan[]' style="width: 100%;" required>
        												<?php foreach ($array_daftar_karyawan->result_array() as $val) { ?>
        													<option value='<?php echo $val['no_pokok'] ?>'><?php echo $val['no_pokok'] . " " . $val['nama'] ?></option>
        												<?php } ?>
        											</select>
        										</div>
        										<div class="modal-footer">
        											<button type="submit" class="btn btn-success">Cetak</button>
        											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        										</div>
        									</div>
        								</form>
        							</div>
        						</div>
        					</div>
        				</div>
        			</div>

        			<div class="form-group">
        				<div class="row table-responsive">
        					<table width="100%" class="table table-striped table-bordered table-hover" id="permohonan_perizinan">
        						<thead>
        							<tr>
        								<th class='text-center no-sort' style="max-width: 10%">No</th>
        								<th class='text-center' style="max-width: 15%">Nama</th>
        								<!--<th class='text-center no-sort'>Kode</th>-->
        								<th class='text-center no-sort'>Izin</th>
        								<th class='text-center'>From</th>
        								<th class='text-center'>To</th>
        								<th class='text-center no-sort'>Pos</th>
        								<th class='text-center no-sort'>Status</th>
        								<th class='text-center no-sort'>Posisi</th>
        								<th class='text-center no-sort'>Aksi</th>

        							</tr>
        						</thead>
        						<tbody>

        						</tbody>
        					</table>
        					<!-- /.table-responsive -->
        				</div>
        			</div>

        			<!-- Modal Status -->
        			<div class="modal fade" id="modal_status" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
        				<div class="modal-dialog">
        					<div class="modal-content">

        						<div class="modal-header">
        							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        							<h4 class="modal-title" id="label_modal_status">Status <?php echo $judul; ?></h4>
        						</div>
        						<div class="modal-body">

        							<table>
        								<tr>
        									<td>NP Pemohon</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="status_np_karyawan"></a></td>
        								</tr>
        								<tr>
        									<td>Nama Pemohon</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="status_nama"></a></td>
        								</tr>
        								<tr>
        									<td>Start Date</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="status_start"></a></td>
        								</tr>
        								<tr>
        									<td>End Date</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="status_end"></a></td>
        								</tr>
        								<tr>
        									<td>Dibuat Tanggal</td>
        									<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        									<td><a id="status_created_at"></a></td>
        								</tr>
        								<!-- <tr>
													<td>Dibuat Oleh</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="status_created_by"></a></td>
												</tr> -->
        							</table>

        							<br>

        							<div class="alert alert-info" id="approver_1">
        								<strong><a id="status_approval_1_nama"></a></strong><br>
        								<p id="status_approval_1_status"></p>
        								<p id="status_approval_1_alasan" style="margin: 0;padding: 0;"></p>
        							</div>

        							<div class="alert alert-info" id="approver_2">
        								<strong><a id="status_approval_2_nama"></a></strong><br>
        								<p id="status_approval_2_status"></p>
        								<p id="status_approval_2_alasan" style="margin: 0;padding: 0;"></p>
        							</div>

        							<div class="alert alert-danger" id="batal">
        								<strong><a id="status_batal_np" class="text-danger"></a></strong><br>
        								<p id="status_batal_waktu"></p>
        								<p id="status_batal_alasan" style="margin: 0;padding: 0;"></p>
        							</div>

        						</div>
        					</div>
        					<!-- /.modal-content -->
        				</div>
        				<!-- /.modal-dialog -->
        			</div>
        			<!-- /.modal -->

        			<!-- Modal -->
        			<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
        				<div class="modal-dialog modal-lg">
        					<div class="modal-content">
        						<div class="modal-header">
        							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        							<h4 class="modal-title" id="label_modal_batal">Status <?= $judul ?></h4>
        						</div>
        						<div class="modal-body">
        							<div class="get-approve"></div>
        						</div>
        					</div>
        				</div>
        			</div>
        			<!-- /.modal -->
        		<?php
				}

				if (@$akses["hapus"]) {
				?>
        			<!--begin: Modal Inactive -->
        			<div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        				<div class="modal-dialog modal-sm" role="document">
        					<div class="modal-content">
        						<div class="modal-header">
        							<h4 class="modal-title text-danger" id="title-inactive">
        								<b>Hapus Data Izin</b>
        								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
        									<span aria-hidden="true">&times;</span>
        								</button>
        							</h4>
        						</div>

        						<div class="modal-body">
        							<h4 id="message-inactive"></h4>
        						</div>
        						<div class="modal-footer">
        							<a href="" id="inactive-action" class="btn btn-danger">Ya, Hapus</a>
        							<button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
        						</div>
        					</div>
        				</div>
        			</div>
        			<!--end: Modal Inactive -->
        		<?php
				}
				if (@$akses["ubah"]) {
				?>
        			<!-- Modal Ubah -->
        			<div class="modal fade" id="modal_ubah" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
        				<div class="modal-dialog">
        					<div class="modal-content">
        						<form method="post" action="<?php echo base_url(); ?>perizinan/permohonan_perizinan/permohonan_perizinan1">
        							<div class="modal-header">
        								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        								<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul; ?></h4>
        							</div>
        							<div class="modal-body">

        								<div class="form-group row">
        									<div class="col-lg-2">
        										<label>NP Karyawan</label>
        									</div>
        									<div class="col-lg-10">
        										<select class="form-control select2insidemodal" name="edit_np_karyawan" id="edit_np_karyawan" style="width: 100%" required>
        											<option value=''></option>
        											<?php
													foreach ($array_daftar_karyawan->result_array() as $value) {
													?>
        												<option value='<?php echo $value['no_pokok'] ?>'><?php echo $value['no_pokok'] . " " . $value['nama'] ?></option>

        											<?php
													}
													?>
        										</select>
        									</div>
        								</div>

        								<div class="form-group row">
        									<div class="col-lg-2">
        										<label>Jenis Izin</label>
        									</div>
        									<div class="col-lg-10">
        										<select class="form-control" name='edit_absence_type' id="edit_absence_type" required>
        											<?php foreach ($jenis_izin as $row) { ?>
        												<option value="<?= $row->kode_pamlek . '|' . $row->kode_erp ?>"><?= '(' . $row->kode_pamlek . ') ' . $row->nama ?></option>
        											<?php } ?>
        										</select>
        									</div>
        								</div>

        								<div class="form-group row">
        									<div class="col-lg-2">
        										<label>Start</label>
        									</div>
        									<div class='col-lg-4'>
        										<input type="date" class="form-control" name="edit_start_date" id="edit_start_date">
        									</div>
        									<div class='col-lg-3'>
        										<input type="time" class="form-control" name="edit_start_time" id="edit_start_time">
        									</div>
        								</div>

        								<div class="form-group row">
        									<div class="col-lg-2">
        										<label>End</label>
        									</div>
        									<div class='col-lg-4'>
        										<input type="date" class="form-control" name="edit_end_date" id="edit_end_date">
        									</div>
        									<div class='col-lg-3'>
        										<input type="time" class="form-control" name="edit_end_time" id="edit_end_time">
        									</div>
        								</div>

        								<div class="modal-footer">.
        									<input type='hidden' id='edit_id' name='edit_id'>
        									<button name='submit' type="submit" value='submit' class="btn btn-primary">Simpan</button>
        									<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
        								</div>


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

        <script src="<?php echo base_url('asset/select2') ?>/select2.min.js"></script>
        <script src="<?= base_url('asset/js/moment.min.js') ?>"></script>
        <script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js') ?>"></script>

        <script src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>
        <script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js') ?>"></script>
        <!--Tri WIbowo 7648 24 12 2021, pakai js yang sudah ada di local -->
        <!--<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js') ?>"></script>-->

        <script type="text/javascript">
        	var all_atasan_1_np = [],
        		all_atasan_1_jabatan = [],
        		all_atasan_2_np = [],
        		all_atasan_2_jabatan = [];
        	var ref_jenis_izin = <?= json_encode($jenis_izin) ?>;
        	var mst_alasan_sipk = [];
        	$('#multi_select').select2({
        		closeOnSelect: false
        		//minimumResultsForSearch: 20
        	});
        	$(document).ready(function() {
        		// get_mst_alasan_sipk();
        		$('.datetimepicker5').datetimepicker({
        			format: 'HH:mm'
        		});
        		$('.select2').select2();
        		$(".select2insidemodal").select2({
        			dropdownParent: $("#modal_ubah")
        		});

        		$(function() {
        			$('#start_date').datetimepicker({
        				format: 'D-M-Y',
        				<?php if (@$min) { ?>
        					minDate: '<?php echo $min; ?>'
        				<?php } ?>
        			});

        			$('#end_date').datetimepicker({
        				format: 'D-M-Y'
        			});

        			$("#start_date").on("dp.change", function(e) {
        				var oldDate = new Date(e.date);
        				var newDate = new Date(e.date);
        				newDate.setDate(oldDate.getDate());

        				$('#end_date').data("DateTimePicker").minDate(newDate);

        				var start_date = $('#start_date').val();;
        				$('#end_date').val(start_date);

        			});

        		});

        		$("#form_absence_type").hide();
        		$("#form_jumlah_bulan").hide();
        		$("#form_jumlah_hari").hide();
        		$('#permohonan_perizinan').DataTable().destroy();
        		table_serverside();
        	});

        	function refresh_table_serverside() {

        		$('#permohonan_perizinan').DataTable().destroy();
        		table_serverside();
        	}

        	function table_serverside() {
        		let date_range = $('#date_range').val();
        		var bulan_tahun = $('#bulan_tahun').val();
        		var izin_0 = $('#izin_0:checked').val();
        		var izin_C = $('#izin_C:checked').val();
        		//var izin_D 		= $('#izin_D:checked').val();
        		var izin_E = $('#izin_E:checked').val();
        		var izin_F = $('#izin_F:checked').val();
        		var izin_G = $('#izin_G:checked').val();
        		var izin_H = $('#izin_H:checked').val();
        		var izin_TM = $('#izin_TM:checked').val();
        		var izin_TK = $('#izin_TK:checked').val();
        		//var izin_AB 	= $('#izin_AB:checked').val();
        		//var izin_ATU 	= $('#izin_ATU:checked').val();

        		var table;
        		//var jenis = $('#get_jenis').val(arr_jenis);
        		//datatables
        		table = $('#permohonan_perizinan').DataTable({

        			"iDisplayLength": 10,
        			"language": {
        				"url": "/ess/asset/datatables/Indonesian.json",
        				"sEmptyTable": "Tidak ada data di database",
        				"emptyTable": "Tidak ada data di database"
        			},

        			"stateSave": true,
					"processing": false, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

        			// Load data for the table's content from an Ajax source
        			//ganti site_url ke base_url karena error jika di refress add or delete ... .
        			//tri wibowo 31 03 2023
        			//setelah sosialisasi
        			//push
        			"ajax": {
        				"url": "<?php echo base_url("perizinan/permohonan_perizinan/permohonan_perizinan/") ?>" + bulan_tahun + '/' + izin_0 + '/' + izin_C + '/' + izin_E + '/' + izin_F + '/' + izin_G + '/' + izin_H,
        				"type": "POST",
        				data: {
        					date_range: date_range
        				} // tambahan untuk date range, 2021-02-24
        				//data: data_save
        			},

        			//Set column definition initialisation properties.
        			"columnDefs": [{
        				"targets": 'no-sort', //first column / numbering column
        				"orderable": false, //set not orderable
        			}, ],

        		});

        	};

        	function otoritas() {
        		document.getElementById('get_month').value = $('#bulan_tahun').val();
        		document.getElementById('get_izin_0').value = $('#izin_0:checked').val();
        		document.getElementById('get_izin_C').value = $('#izin_C:checked').val();
        		//document.getElementById('get_izin_D').value = $('#izin_D:checked').val();
        		document.getElementById('get_izin_E').value = $('#izin_E:checked').val();
        		document.getElementById('get_izin_F').value = $('#izin_F:checked').val();
        		document.getElementById('get_izin_G').value = $('#izin_G:checked').val();
        		document.getElementById('get_izin_H').value = $('#izin_H:checked').val();
        		/*document.getElementById('get_izin_TM').value = $('#izin_TM:checked').val();
                document.getElementById('get_izin_TK').value = $('#izin_TK:checked').val();
				*/
        		$("#show_otoritas").modal('show');
        	}
        </script>

        <script>
        	function checkJumlahCuti() {
        		var absence_type = document.getElementById("absence_type").value;
        		var np_karyawan = $('#np_karyawan').val();
        		var jumlah_hari = $('#jumlah_hari').val();
        		var jumlah_bulan = $('#jumlah_bulan').val();

        		var data_array = new Array();
        		data_array[0] = absence_type;
        		data_array[1] = np_karyawan;
        		data_array[2] = jumlah_hari;
        		data_array[3] = jumlah_bulan;


        		$.ajax({
        			type: "POST",
        			dataType: "html",
        			url: "<?php echo base_url('perjalanan_dinas/Sppd/ajax_checkJumlahCuti'); ?>",
        			data: "data_array=" + data_array,
        			success: function(msg) {
        				if (msg == '') {

        				} else {
        					alert(msg);
        					$('#jumlah_bulan').val('0');
        					$('#jumlah_hari').val('0');
        				}
        			}
        		});
        	}
        </script>

        <script>
        	function getJenisCuti() {
        		var jenis_cuti = document.getElementById("absence_type").value;

        		if (jenis_cuti == '') {
        			$("#form_jumlah_bulan").hide();
        			$("#form_jumlah_hari").hide();
        		} else
        		if (jenis_cuti == '1010') {
        			$("#form_jumlah_bulan").show();
        			$("#form_jumlah_hari").show();
        		} else {
        			$("#form_jumlah_bulan").hide();
        			$("#form_jumlah_hari").show();
        		}

        	}
        </script>

        <script>
        	function getEndDate() {
        		var start_date = $('#start_date').val();
        		document.getElementById('end_date').setAttribute("min", start_date);
        	}
        </script>

        <script>
        	function getNama() {
        		var np_karyawan = $('#np_karyawan').val();

        		$.ajax({
        			type: "POST",
        			dataType: "html",
        			url: "<?php echo base_url('perizinan/permohonan_perizinan/ajax_getNama'); ?>",
        			data: "vnp_karyawan=" + np_karyawan,
        			success: function(msg) {
        				if (msg == '') {
        					alert('Silahkan isi No. Pokok Dengan Benar.');
        					$('#np_karyawan').val('');
        					$('#nama').text('');
        					$("#form_absence_type").hide();
        				} else {
        					$('#nama').text(msg);
        					$("#form_absence_type").show();
        				}
        			}
        		});
        	}
        </script>

        <script>
        	function listNp() {
        		$.ajax({
        			type: "POST",
        			dataType: "html",
        			url: "<?php echo base_url('perizinan/permohonan_perizinan/ajax_getListNp'); ?>",
        			success: function(msg) {
        				if (msg == '') {
        					alert('Silahkan isi No. Pokok Dengan Benar.');
        					$('#list_np').text('');
        				} else {
        					$('#list_np').text(msg);
        				}
        			}
        		});
        	}
        </script>

        <script>
        	$(document).on("click", '.status_button', function(e) {
        		var status_np_karyawan = $(this).data('np-karyawan');
        		var status_nama = $(this).data('nama');
        		var status_created_at = $(this).data('created-at');
        		var status_start = $(this).data('start-date');
        		var status_end = $(this).data('end-date');
        		var status_approval_1_nama = $(this).data('approval-1-nama');
        		var status_approval_1_status = $(this).data('approval-1-status');
        		var status_approval_1_alasan = $(this).data('approval-1-alasan');
        		var status_approval_2_nama = $(this).data('approval-2-nama');
        		var status_approval_2_status = $(this).data('approval-2-status');
        		var status_approval_2_alasan = $(this).data('approval-2-alasan');
        		var status_pamlek = $(this).data('pamlek');
        		var batal_waktu = $(this).data('batal-waktu');
        		var batal_alasan = $(this).data('batal-alasan');
        		var batal_np = $(this).data('batal-np');

        		$('#approver_2').hide();
        		$('#batal').hide();
        		if (status_pamlek != 'G') {
        			$('#approver_2').show();
        		}
        		if (batal_np != '' && batal_np != null) {
        			$('#batal').show();
        		}

        		$('#approver_1').removeClass('alert-info');
        		$('#approver_1').removeClass('alert-danger');
        		$('#approver_2').removeClass('alert-info');
        		$('#approver_2').removeClass('alert-danger');
        		$('#status_approval_1_nama').removeClass('text-primary');
        		$('#status_approval_1_nama').removeClass('text-danger');
        		$('#status_approval_2_nama').removeClass('text-primary');
        		$('#status_approval_2_nama').removeClass('text-danger');

        		$('#status_approval_1_alasan').css('display', 'none');
        		if (status_approval_1_status.includes("TIDAK") == true) {
        			$('#approver_1').addClass('alert-danger');
        			$('#status_approval_1_nama').addClass('text-danger');
        			$('#status_approval_1_alasan').css('display', '');
        			$("#status_approval_1_alasan").text('Alasan : ' + status_approval_1_alasan);
        		} else {
        			$('#approver_1').addClass('alert-info');
        			$('#status_approval_1_nama').addClass('text-primary');
        		}
        		// console.log(status_approval_1_status.includes("TIDAK"));
        		$('#status_approval_2_alasan').css('display', 'none');
        		if (status_approval_2_status.includes("TIDAK") == true) {
        			$('#approver_2').addClass('alert-danger');
        			$('#status_approval_2_nama').addClass('text-danger');
        			$('#status_approval_2_alasan').css('display', '');
        			$("#status_approval_2_alasan").text('Alasan : ' + status_approval_2_alasan);
        		} else {
        			$('#approver_2').addClass('alert-info');
        			$('#status_approval_2_nama').addClass('text-primary');
        		}

        		$("#status_np_karyawan").text(status_np_karyawan);
        		$("#status_nama").text(status_nama);
        		$("#status_created_at").text(status_created_at);
        		$("#status_start").text(status_start);
        		$("#status_end").text(status_end);
        		$("#status_approval_1_nama").text(status_approval_1_nama);
        		$("#status_approval_1_status").text(status_approval_1_status);
        		$("#status_approval_2_nama").text(status_approval_2_nama);
        		$("#status_approval_2_status").text(status_approval_2_status);
        		$("#status_batal_np").text(batal_np);
        		$("#status_batal_alasan").text(batal_alasan);
        		$("#status_batal_waktu").text(batal_waktu);
        	});
        </script>

        <script>
        	$(document).on('click', '.detail_button', function(e) {
        		e.preventDefault();
        		$("#modal_detail").modal('show');
        		$.post('<?php echo base_url("perizinan/permohonan_perizinan/view_detail") ?>', {
        				id_perizinan: $(this).attr('data-id'),
        				tgl: $(this).attr('data-tgl')
        			},
        			function(e) {
        				$(".get-approve").html(e);
        			}
        		);
        	});

        	$(document).on("click", '.edit_button', function(e) {
        		var edit_id = $(this).data('id');
        		var edit_np_karyawan = $(this).data('np-karyawan');
        		var edit_absence_type = $(this).data('absence-type');
        		var edit_start_date = $(this).data('start-date');
        		var edit_start_time = $(this).data('start-time');
        		var edit_end_date = $(this).data('end-date');
        		var edit_end_time = $(this).data('end-time');

        		$("#edit_id").val(edit_id);
        		$("#edit_start_date").val(edit_start_date);
        		$("#edit_start_time").val(edit_start_time);
        		$("#edit_end_date").val(edit_end_date);
        		$("#edit_end_time").val(edit_end_time);

        		document.getElementById("edit_np_karyawan").value = edit_np_karyawan;
        		document.getElementById("edit_absence_type").value = edit_absence_type;
        		take_action_date();
        	});

        	function hapus(id, np, tanggal_end, tanggal_start) {
        		//                console.log(id);
        		//                console.log(np);
        		//                console.log(tanggal);
        		var url = "<?php echo base_url('perizinan/permohonan_perizinan/hapus') ?>/";
        		$('#inactive-action').prop('href', url + id + '/' + np + '/' + tanggal_end + '/' + tanggal_start);
        		$('#message-inactive').text('Apakah anda yakin ingin menghapus perizinan ini ?');
        		$('#modal-inactive').modal('show');
        	}

        	function take_action_date() {
        		var add_absence_type = $('#add_absence_type').val();
        		if (add_absence_type == '0|2001|5000') {
        			$('#form-start-date').css('display', 'none');
        			$('#start_date').prop('required', false);
        			$('#start_time').prop('required', false);
        			$('#start_date').val('');
        			$('#start_time').val('');
        		} else {
        			$('#form-start-date').css('display', 'block');
        			$('#start_date').prop('required', true);
        			$('#start_time').prop('required', true);
        		}
        	}

        	function get_approval() {
        		let insert_np_karyawan = $('#np_karyawan').find(':selected').val();
        		let insert_absence_type = $('#add_absence_type').val();
        		$('#approval_1_np').find('option').remove();
        		$('#approval_2_np').find('option').remove();
        		all_atasan_1_np = [];
        		all_atasan_1_jabatan = [];
        		all_atasan_2_np = [];
        		all_atasan_2_jabatan = [];

        		$.ajax({
        			url: "<?php echo base_url('perizinan/filter_approval/get_approval'); ?>",
        			type: "POST",
        			dataType: "json",
        			data: {
        				np: insert_np_karyawan,
        				absence_type: insert_absence_type
        			},
        			success: function(response) {
        				if (response.data.atasan_1.length > 0) {
        					$('#approval_1_jabatan').val(response.data.atasan_1[0].nama_jabatan);
        					$.each(response.data.atasan_1, function(i, item) {
        						all_atasan_1_np.push(response.data.atasan_1[i].no_pokok);
        						all_atasan_1_jabatan.push(response.data.atasan_1[i].nama_jabatan);
        						$('#approval_1_np').append(`<option value="` + response.data.atasan_1[i].no_pokok + `">` +
        							response.data.atasan_1[i].no_pokok + ` - ` + response.data.atasan_1[i].nama +
        							`</option>`);
        					});
        				}
        				if (response.data.atasan_2.length > 0) {
        					$('#approval_2_jabatan').val(response.data.atasan_2[0].nama_jabatan);
        					$.each(response.data.atasan_2, function(i, item) {
        						all_atasan_2_np.push(response.data.atasan_2[i].no_pokok);
        						all_atasan_2_jabatan.push(response.data.atasan_2[i].nama_jabatan);
        						$('#approval_2_np').append(`<option value="` + response.data.atasan_2[i].no_pokok + `">` +
        							response.data.atasan_2[i].no_pokok + ` - ` + response.data.atasan_2[i].nama +
        							`</option>`);
        					});
        				}
        			},
        			error: function(e) {
        				console.log(e);
        			}
        		});
        	}

        	function show_approval() {
        		let insert_np_karyawan = $('#np_karyawan').find(':selected').val();
        		let insert_absence_type = $('#add_absence_type').val();
        		let value = insert_absence_type;

        		if (value == 'G|2001|5050' || value == '0|2001|5000') {
        			$('#approval_2_np').prop('required', false);
        			$('#approval_2_input').prop('required', false);
        			$('#approval_2_input_jabatan').prop('required', false);

        			$('#atasan_2').css('display', 'none');
        			$('#atasan_1').css('display', '');
        		} else if (value == 'H|2001|5030' || value == 'D|2001|5040' || value == 'C|2001|5040' || value == 'F|2001|5020') {
        			$('#approval_2_np').prop('required', true);
        			$('#approval_2_input').prop('required', true);
        			$('#approval_2_input_jabatan').prop('required', true);

        			$('#atasan_1').css('display', '');
        			$('#atasan_2').css('display', '');
        		} else if (value == 'E|2001|5010') {
        			$('#approval_2_np').prop('required', true);
        			$('#approval_2_input').prop('required', true);
        			$('#approval_2_input_jabatan').prop('required', true);

        			$('#atasan_1').css('display', '');
        			$('#atasan_2').css('display', '');
        		} else if (value == 'SIPK|2001|5030') {
        			$('#approval_2_np').prop('required', false);
        			$('#approval_2_input').prop('required', false);
        			$('#approval_2_input_jabatan').prop('required', false);

        			$('#atasan_2').hide();
        			$('#atasan_1').show();
        		} else {
        			$('#approval_2_np').prop('required', false);
        			$('#approval_2_input').prop('required', false);
        			$('#approval_2_input_jabatan').prop('required', false);

        			$('#atasan_1').css('display', 'none');
        			$('#atasan_2').css('display', 'none');
        		}

        		/*$('#add_pos').prop('multiple', false);
        		if (value=='H|2001|5030') {
        			$('#add_pos').prop('multiple', true);
        		} else {
        			$('#add_pos').prop('multiple', false);
        		}
        		$('#add_pos').val('').change();*/
        	}

        	function fill_jabatan(input, id) {
        		if (id.indexOf("1") >= 0) {
        			let index_of = all_atasan_1_np.indexOf(input.value);
        			$('#' + id).val(all_atasan_1_jabatan[index_of]);
        		} else if (id.indexOf("2") >= 0) {
        			let index_of = all_atasan_2_np.indexOf(input.value);
        			$('#' + id).val(all_atasan_2_jabatan[index_of]);
        		}
        	}

        	function getNamaAtasan1() {
        		var approval_2_np = $('#approval_2_np').val();
        		var np_atasan = $('#approval_1_np').val();
        		if (np_atasan == " ") {
        			$('#approval_1_np').val('');
        			$('#approval_1_input').val('');
        			$('#approval_1_input_jabatan').val('');
        		} else if (np_atasan !== approval_2_np) {
        			if (np_atasan.length > 3) {
        				var np_karyawan = $('#np_karyawan').val();
        				var insert_absence_type = $('#add_absence_type').val();

        				$.ajax({
        					type: "POST",
        					dataType: "JSON",
        					url: "permohonan_perizinan/ajax_getNama_approval/1",
        					data: {
        						"np_aprover": np_atasan,
        						"np_karyawan": np_karyawan,
        						"izin": insert_absence_type
        					},
        					success: function(msg) {
        						if (msg.status == false) {
        							alert(msg.message);
        							$('#approval_1_np').val('');
        							$('#approval_1_input').val('');
        							$('#approval_1_input_jabatan').val('');
        						} else {
        							$('#approval_1_input').val(msg.data.nama);
        							$('#approval_1_input_jabatan').val(msg.data.jabatan);
        						}
        					}
        				});
        			} else if (np_atasan.length < 4) {
        				$('#approval_1_input').val('');
        				$('#approval_1_input_jabatan').val('');
        			}
        		} else {
        			alert('NP Atasan 1 tidak boleh sama dengan Atasan 2');
        			$('#approval_1_np').val('');
        			$('#approval_1_input').val('');
        			$('#approval_1_input_jabatan').val('');
        		}
        	}

        	function getNamaAtasan2() {
        		var approval_1_np = $('#approval_1_np').val();
        		var np_atasan = $('#approval_2_np').val();
        		if (np_atasan == " ") {
        			$('#approval_2_np').val('');
        			$('#approval_2_input').val('');
        			$('#approval_2_input_jabatan').val('');
        		} else if (np_atasan !== approval_1_np) {
        			if (np_atasan.length > 3) {
        				var np_karyawan = $('#np_karyawan').val();
        				var insert_absence_type = $('#add_absence_type').val();

        				$.ajax({
        					type: "POST",
        					dataType: "JSON",
        					url: "permohonan_perizinan/ajax_getNama_approval/2",
        					data: {
        						"np_aprover": np_atasan,
        						"np_karyawan": np_karyawan,
        						"izin": insert_absence_type
        					},
        					success: function(msg) {
        						if (msg.status == false) {
        							alert(msg.message);
        							$('#approval_2_np').val('');
        							$('#approval_2_input').val('');
        							$('#approval_2_input_jabatan').val('');
        						} else {
        							$('#approval_2_input').val(msg.data.nama);
        							$('#approval_2_input_jabatan').val(msg.data.jabatan);
        						}
        					}
        				});
        			} else if (np_atasan.length < 4) {
        				$('#approval_2_input').val('');
        				$('#approval_2_input_jabatan').val('');
        			}
        		} else {
        			alert('NP Atasan 2 tidak boleh sama dengan Atasan 1');
        			$('#approval_2_np').val('');
        			$('#approval_2_input').val('');
        			$('#approval_2_input_jabatan').val('');
        		}
        	}

        	// tambahan untuk date range, 2021-02-24
        	$('input[name="dates"]').daterangepicker({
        		locale: {
        			format: 'DD-MM-YYYY'
        		}
        	});
        	// END: tambahan untuk date range, 2021-02-24

        	// untuk ubah background izin, 2022-05-31
        	// $('#add_absence_type').on('change', (e) => {
        	// 	let parent = $('#tambah-panel-color-izin').parent().closest('div');
        	// 	if (e.target.value !== '') {
        	// 		let explode = e.target.value.split('|');
        	// 		let find = _.find(ref_jenis_izin, (o) => {
        	// 			return o.kode_pamlek == explode[0] && o.kode_erp == `${explode[1]}|${explode[2]}`
        	// 		});
        	// 		if (typeof find != 'undefined') {
        	// 			$('#tambah-panel-color-izin').css({
        	// 				"background-color": find.theme_color,
        	// 				opacity: 0.75,
        	// 				color: `${find.font_color}`
        	// 			});
        	// 			parent.css({
        	// 				"border-color": `${find.theme_color!=='#fff' ? find.theme_color:'#ddd'}`
        	// 			});
        	// 		} else {
        	// 			$('#tambah-panel-color-izin').css({
        	// 				"background-color": "#fff",
        	// 				color: '#333333'
        	// 			});
        	// 			parent.css({
        	// 				"border-color": "#ddd"
        	// 			});
        	// 		}
        	// 	} else {
        	// 		$('#tambah-panel-color-izin').css({
        	// 			"background-color": "#fff",
        	// 			color: '#333333'
        	// 		});
        	// 		parent.css({
        	// 			"border-color": "#ddd"
        	// 		});
        	// 	}

        	// 	if (e.target.value == 'SIPK|2001|5030') alasan_set_to_option();
        	// 	else alasan_set_to_input();
        	// });
        	// END: untuk ubah background izin, 2022-05-31

        	const get_mst_alasan_sipk = async () => {
        		$.ajax({
        			type: "POST",
        			dataType: "JSON",
        			url: "<?php echo base_url('perizinan/permohonan_perizinan/get_mst_alasan_sipk'); ?>",
        			data: {},
        			success: function(res) {
        				mst_alasan_sipk = res;
        			}
        		});
        	}

        	const alasan_set_to_option = () => {
        		let html_select = `<select class="form-control" name="alasan" style="width: 100%;" required>`;
        		for (const i of mst_alasan_sipk) {
        			html_select += `<option value="${i.alasan}">${i.alasan}</option>`;
        		}
        		html_select += `</select>`;
        		$('#formulir_tambah').find('[name=alasan]')
        			.replaceWith(html_select);
        	}

        	const alasan_set_to_input = () => {
        		let html_input = `<input class="form-control" name="alasan" id="alasan" required>`;
        		$('#formulir_tambah').find('[name=alasan]')
        			.replaceWith(html_input);
        	}
        </script>