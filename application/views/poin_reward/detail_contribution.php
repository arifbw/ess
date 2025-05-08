<div class="row">
	<div class="col-md-10">
		<table>
			<tr>
				<th>Pegawai</th>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
				<td><b><a><?= $detail['np_karyawan'] . ' - ' . $detail['nama_karyawan'] ?></a></b></td>
			</tr>
			<tr>
				<th>Jenis Dokumen</th>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
				<td><b><a><?= $detail['jenis_dokumen'] ?></a></b></td>
			</tr>
			<tr>
				<th>Tanggal Dokumen</th>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
				<td><b><a><?= $detail['tanggal_dokumen'] ?></a></b></td>
			</tr>
			<tr>
				<th>Perihal</th>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
				<td><b><a style="display: inline-block; 
                      max-width: 700px; 
                      overflow: hidden; 
                      text-overflow: ellipsis; 
                      white-space: nowrap;"><?= $detail['perihal'] ?></a></b></td>
			</tr>
			<tr>
				<th>Dibuat Tanggal</th>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
				<td><b><a><?= $detail['created_at'] ?></a></b></td>
			</tr>
			<?php if ($detail['asal'] == 'import'): ?>
				<tr>
					<th>Url Dokumen</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a href="<?= $detail['url'] ?>" target="_blank" style="display: inline-block; 
                      max-width: 700px; 
                      overflow: hidden; 
                      text-overflow: ellipsis; 
                      white-space: nowrap;"><?= $detail['url'] ?></a></b></td>
				</tr>
			<?php endif; ?>
		</table>
	</div>
</div>
<?php if ($detail['asal'] !== 'import'): ?>
	<?php if (pathinfo($detail['dokumen'], PATHINFO_EXTENSION) == 'pdf'): ?>
		<embed style=" width: 100%;height: 400px;" src="<?= base_url('uploads/contribution/dokumen/') . $detail['dokumen']; ?>" type="application/pdf">
	<?php else: ?>
		<div class="image-container" style="max-height: 400px; overflow-y: scroll;">
			<img style="width: 100%;" src="<?= base_url('uploads/contribution/dokumen/') . $detail['dokumen']; ?>" class="img-fluid" alt="dokumen">
		</div>
	<?php endif ?>
<?php endif; ?>
<br>

<div class="alert alert-<?= $approval_warna ?>">
	<?php if ($detail['status_verifikasi'] !== '0'): ?>
		<strong><a class="text-<?= $approval_warna ?>"><?= $detail['approval_np'] . ' | ' . $detail['approval_nama'] ?></a></strong><br>
		<p><?= $status_verifikasi ?></p>
	<?php else: ?>
		<p class="block">Tanggal Submit :</p>
		<p><?= $detail['tanggal_submit'] ?></p>
	<?php endif; ?>
	<?php if ($detail['status_verifikasi'] == '2') : ?>
		<p style="margin-top: 0">Alasan : <?= $detail['approval_alasan'] ?></p>
	<?php endif; ?>
	<?php if ($detail['status_verifikasi'] == '1') : ?>
		<p style="margin-top: 0">Poin : <?= $detail['poin'] ?></p>
	<?php endif; ?>
</div>