			<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />
			<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
			<link href="<?= base_url('asset/sweetalert2/sweetalert2.min.css') ?>" rel="stylesheet">

			<form role="form" action="<?= base_url(); ?>pelaporan/verifikasi/pindah_agama/save_approve/" id="formulir_tambah" method="post">	
				<div class="row">
					<div class="col-md-12">
						<table>
							<tr>
								<th>Pegawai</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['np_karyawan'].' - '.$detail['nama_karyawan'] ?></a></b></td>
								<td></td>
							</tr>
							<tr>
								<th>Unit Kerja</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['nama_unit'] ?></a></b></td>
								<td></td>
							</tr>
							<tr>
								<th>Agama Lama</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['agama_lama'] ?></a></b></td>
							</tr>
							<tr>
								<th>Agama Baru</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['agama_baru'] ?></a></b></td>
							</tr>
							<tr>
								<th>Tanggal Perpindahan</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['tanggal_pindah'] ?></a></b></td>
							</tr>
							<tr>
								<th>No Surat Keterangan</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['no_surat_keterangan'] ?></a></b></td>
							</tr>
							<tr>
								<th>Keterangan</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['keterangan'] ?></a></b></td>
								<td></td>
							</tr>
							<tr>
								<th>Dibuat Tanggal</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['created_at'] ?></a></b></td>
								<td></td>
							</tr>
							<tr>
								<td style="padding-top:20px" colspan="3">
									<b><a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/pindah_agama/surat_keterangan/'.$detail['surat_keterangan']) ?>">Unduh Surat Keterangan</a></b>
								</td>
								<td></td>
							</tr>
						</table>
					</div>
				</div>

				<br>
				<div class="alert alert-<?= $approval_warna ?>">
					<strong><a class="text-<?= $approval_warna ?>"><?= $detail['approval_np'].' | '.$detail['approval_nama'] ?></a></strong><br>
					<p><?= $approval_status ?></p>
					<?php if ($detail['approval_status']=='2'): ?>
						<p style="margin-top: 0">Alasan : <?= $detail['approval_alasan'] ?></p>
					<?php endif; ?>
				</div>

				<div id="set_approve">
					<div class="alert alert-<?= $sdm_warna ?>">
						<strong><a class="text-<?= $sdm_warna ?>">Verifikasi Laporan <?= $judul ?></a></strong><br>
						<?php if ($detail['approval_status']=='1' && $this->akses["persetujuan"]): ?>
							<br>
							<select class="form-control select2" name='status_approval' onchange="form_alasan(this)" style="width : 100%" required>
								<option value=''>Berikan Verifikasi</option>
								<option value='3' <?= ($detail['approval_status']=='3') ? 'selected' : ''; ?>>Setujui</option>
								<option value='4' <?= ($detail['approval_status']=='4') ? 'selected' : ''; ?>>Tidak Setuju</option>
							</select>

							<div id="form-alasan" style="display: <?= ($detail['approval_status']=='4') ? '':'none' ?>;">
								<b>Alasan Tidak Disetujui</b>
								<br>
								<textarea rows="2" class="form-control" name='alasan'><?= $detail['sdm_alasan'] ?></textarea>
							</div>
						<?php else: ?>
							<p><?= $sdm_status ?></p>
							<?php if ($detail['approval_status']=='4'): ?>
								<p style="margin-top: 0">Alasan : <?= $detail['sdm_alasan'] ?></p>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ($detail['approval_status']=='5'): ?>
							<a class="text-<?= $sdm_warna ?>"><?= 'Tanggal Verifikasi : '.$detail['sdm_verif_date'] ?></a><br>
						<?php endif; ?>
					</div>
				</div>

				<?php if ($detail['approval_status']=='3' && $this->akses["submit"]) { ?>
				<div id="set_erp" style="padding-bottom: 20px" class=" text-center">
					<a class="btn btn-success" id="set_submit_erp" href="javascript:;">Submit Jika Data Telah Masuk di ERP</a>
					<a class="btn btn-danger" onclick="form_alasan_submit()" href="javascript:;">Tolak Laporan</a>
				</div>

				<div id="form-alasan-submit" style="padding-bottom: 20px;display: <?= ($detail['approval_status']=='4') ? '':'none' ?>;">
					<b>Alasan Tidak Disetujui</b>
					<br>
					<textarea rows="2" class="form-control" name='alasan_submit'><?= $detail['sdm_alasan'] ?></textarea>
				</div>
				<?php } ?>

				<?php if ($detail['approval_status']=='1') { ?>
				<div class="row">
					<div class="col-lg-12 text-right">
						<input type="hidden" name="id_" value="<?= $detail['id'] ?>">
						<input type="submit" name="submit" id='persetujuan_button' value="Simpan" class="btn btn-block btn-<?= $sdm_warna ?>">
					</div>
				</div>
				<?php } else if ($detail['approval_status']=='3') { ?>
				<div class="row" id="form-submit-submit" style="display: none;">
					<div class="col-lg-12 text-right">
						<input type="hidden" name="id_" value="<?= $detail['id'] ?>">
						<input type="submit" name="submit" id='persetujuan_button' value="Simpan" class="btn btn-block btn-<?= $sdm_warna ?>">
					</div>
				</div>
				<?php } ?>
			</form>


        	<script src="<?= base_url('asset') ?>/sweetalert2/sweetalert2.js"></script>
			<script type="text/javascript">
			function ubah_verif(){
                var set_approve = document.getElementById("set_approve");
                var set_erp = document.getElementById("set_erp");
                var submit_erp = document.getElementById("submit_erp");
                var ubah_verif = document.getElementById("ubah_verif");

                set_erp.style.display = "none";
                ubah_verif.style.display = "none";
                set_approve.style.display = "block";
                submit_erp.style.display = "block";
            }
			function submit_erp(){
                var set_approve = document.getElementById("set_approve");
                var set_erp = document.getElementById("set_erp");
                var submit_erp = document.getElementById("submit_erp");
                var ubah_verif = document.getElementById("ubah_verif");

                set_approve.style.display = "none";
                submit_erp.style.display = "none";
               	set_erp.style.display = "block";
                ubah_verif.style.display = "block";
            }
			function form_alasan(obj){
                var textarea = document.getElementById("form-alasan");
                var selectBox = obj;
                var selected = selectBox.options[selectBox.selectedIndex].value;

                if(selected === '4'){
                    textarea.style.display = "block";
                }
                else{
                    textarea.style.display = "none";
                }
            }
			function form_alasan_submit(){
                var textarea = document.getElementById("form-alasan-submit");
                var submit = document.getElementById("form-submit-submit");
                textarea.style.display = "block";
                submit.style.display = "block";
            }
			$(document).on('click','#set_submit_erp',function(e){
				e.preventDefault();
				Swal.fire({
					title: 'Anda yakin telah submit data ini di ERP?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ya, Submit',
					cancelButtonText: 'Batal'
				}).then((result) => {
					if (result.isConfirmed) {
						$.post('<?= site_url("pelaporan/verifikasi/pindah_agama/save_erp") ?>',
							{id_: '<?= $detail['id'] ?>'},
							function(get){
								ret = JSON.parse(get);
								if (ret.status==true) {
									refresh_table_serverside();
									$("#modal_persetujuan").modal('hide');
									Swal.fire(
										ret.msg,
										'',
										'success'
									);
								} else {
									Swal.fire(
										ret.msg,
										'',
										'error'
									);
								}
							}
						);
					}
				})

			});
			$('.datetimepicker5').datetimepicker({
				format: 'YYYY-MM-DD HH:mm'
			});
			$('.select2').select2();
			</script>