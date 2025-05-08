				<div class="row">
					<div class="col-md-10">
						<table>
							<tr>
								<th>Pegawai</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['np_karyawan'].' - '.$detail['nama_karyawan'] ?></a></b></td>
							</tr>
							<tr>
								<th>Unit Kerja</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['nama_unit'] ?></a></b></td>
							</tr>
							<tr>
								<th>Nama Anak</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['nama_anak'] ?></a></b></td>
							</tr>
							<tr>
								<th>Tempat Lahir</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['tempat_lahir_anak'] ?></a></b></td>
							</tr>
							<tr>
								<th>Tanggal Lahir</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['tanggal_lahir_anak'] ?></a></b></td>
							</tr>
							<tr>
								<th>Anak Ke</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['anak_ke'] ?></a></b></td>
							</tr>
							<tr>
								<th>Nama Sekolah/Universitas</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['nama_sekolah'] ?></a></b></td>
							</tr>
							<tr>
								<th>Kelas/Semester</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['kelas'] ?></a></b></td>
							</tr>
							<tr>
								<th>Keterangan</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['keterangan'] ?></a></b></td>
							</tr>
							<tr>
								<th>Dibuat Tanggal</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['created_at'] ?></a></b></td>
							</tr>
							<tr>
								<td style="padding-top:20px" colspan="3">
									<b>
										<a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/anak_usia/dok_keterangan_sekolah/'.$detail['dok_keterangan_sekolah']) ?>">Unduh Bukti Pembayaran <br>Sekolah/Surat Keterangan dari Sekolah</a>
										<a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/anak_usia/dok_pernyataan/'.$detail['dok_pernyataan']) ?>">Surat Pernyataan Anak <br> Belum Menikah dan Bekerja</a>
									</b>
								</td>
							</tr>
						</table>
					</div>
				</div>

				<br>

				<div class="alert alert-<?= $approval_warna ?>">
					<strong><a class="text-<?= $approval_warna ?>"><?= $detail['approval_np'].' | '.$detail['approval_nama'] ?></a></strong><br>
					<p><?= $approval_status ?></p>
					<?php if ($detail['approval_status']=='2') { ?>
					<p style="margin-top: 0">Alasan : <?= $detail['approval_alasan'] ?></p>
					<?php } ?>
				</div>

				<?php if ($detail['approval_status']=='3' || $detail['approval_status']=='4' || $detail['approval_status']=='5' || $detail['approval_status']=='6') { ?>
				<div class="alert alert-<?= $sdm_warna ?>">
					<strong><a class="text-<?= $sdm_warna ?>">Verifikasi Persetujuan SDM</a></strong><br>
					<p><?= $sdm_status ?></p>
					<?php if ($detail['approval_status']=='4') { ?>
					<p style="margin-top: 0">Alasan : <?= $detail['sdm_alasan'] ?></p>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ($detail['approval_status']=='5' || $detail['approval_status']=='6') { ?>
				<div class="alert alert-<?= $submit_warna ?>">
					<strong><a class="text-<?= $submit_warna ?>">Verifikasi Submit ERP SDM</a></strong><br>
					<p><?= $submit_status ?></p>
					<?php if ($detail['approval_status']=='6') { ?>
					<p style="margin-top: 0">Alasan : <?= $detail['sdm_submit_alasan'] ?></p>
					<?php } ?>
				</div>
				<?php } ?>