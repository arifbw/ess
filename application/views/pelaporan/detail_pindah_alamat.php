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
				<th style="vertical-align: top">Alamat Lama</th>
				<td style="vertical-align: top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
				<td>
					<b>
					<a>
					<?= 
					'('.$detail['jenis_alamat_lama'].') '.$detail['alamat_lama'].'<br>'.$detail['kelurahan_lama'].', '.$detail['kecamatan_lama'].', '.$detail['kabupaten_lama'].', '.$detail['provinsi_lama'] 
					?></a>
					</b>
				</td>
			</tr>
			<tr>
				<th style="vertical-align: top">Alamat Baru</th>
				<td style="vertical-align: top">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
				<td>
					<b>
					<a>
					<?= 
					'('.$detail['jenis_alamat_baru'].') '.$detail['alamat_baru'].'<br>'.$detail['kelurahan_baru'].', '.$detail['kecamatan_baru'].', '.$detail['kabupaten_baru'].', '.$detail['provinsi_baru'] 
					?></a>
					</b>
				</td>
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
						<a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/pindah_alamat/ktp/'.$detail['ktp']) ?>">Unduh KTP</a>
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
	<?php if ($detail['approval_status']=='2'): ?>
		<p style="margin-top: 0">Alasan : <?= $detail['approval_alasan'] ?></p>
	<?php endif; ?>
</div>

<?php if ($detail['approval_status']=='3' || $detail['approval_status']=='4' || $detail['approval_status']=='5'): ?>
	<div class="alert alert-<?= $sdm_warna ?>">
		<strong><a class="text-<?= $sdm_warna ?>">Verifikasi Persetujuan SDM</a></strong><br>
		<p><?= $sdm_status ?></p>
		<?php if ($detail['approval_status']=='4'): ?>
			<p style="margin-top: 0">Alasan : <?= $detail['sdm_alasan'] ?></p>
		<?php endif; ?>
	</div>
<?php endif; ?>

<?php if ($detail['approval_status']=='5'): ?>
	<div class="alert alert-info">
		<strong><a class="text-info">Verifikasi Submit ERP SDM</a></strong><br>
		<p><?= $submit_status ?></p>
	</div>
<?php endif; ?>