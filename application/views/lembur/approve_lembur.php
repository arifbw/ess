<form role="form" action="<?php echo site_url('osdm/persetujuan_lembur_sdm/save') ?>" method="post">
	<table>
		<tr>
			<td>NP Pemohon</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><a><?= $no_pokok ?></a></td>
		</tr>
		<tr>
			<td>Nama Pemohon</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><a><?= $nama_pegawai ?></a></td>
		</tr>
		<tr>
			<td>Tertanggal</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><a><?= $tgl_dws ?></a></td>
		</tr>
		<tr>
			<td>Input Mulai Lembur</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><a><?= $tgl_mulai ?> <?= $jam_mulai ?></a></td>
		</tr>
		<tr>
			<td>Input Selesai Lembur</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><a><?= $tgl_selesai ?> <?= $jam_selesai ?></a></td>
		</tr>
		<tr>
			<td>Waktu Lembur Diakui</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><a><?= ($waktu_mulai_fix != null || $waktu_mulai_fix != '' || $waktu_selesai_fix != null || $waktu_selesai_fix != '') ? $waktu_mulai_fix . ' - ' . $waktu_selesai_fix : '-' ?></a></td>
		</tr>
		<!-- <tr>
			<td>Total Jam Diakui</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><b><a><?= $jam_diakui ?> Jam</a></b></td>
		</tr> -->
		<tr>
			<td>Jenis Alasan</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><b><a><?= $alasan ?></a></b></td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><b><a><?= $keterangan ?></a></b></td>
		</tr>
		<tr>
			<td>Dibuat Tanggal</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><a><?= $created_at ?></a></td>
		</tr>
		<tr>
			<td>Dibuat Oleh</td>
			<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
			<td><a><?= $created_name . ' (' . $created_np . ')' ?></a></td>
		</tr>

	</table>

	<br>

	<?php if ($approval_status != null || $approval_status != '0' || $approval_status != '') { ?>
		<?php
		if ($approval_status == '1') {
			$setuju = 'selected';
			$tolak = ''; ?>
			<div class="alert alert-info">
				<b><span class="text-danger">Sudah Disetujui</span><span> Oleh <?= $approval_nama . '(' . $approval_np . ')' ?> Pada <?= $approval_date ?></span></b>
			</div>
		<?php } else if ($approval_status == '2') {
			$setuju = '';
			$tolak = 'selected'; ?>
			<div class="alert alert-info">
				<b><span class="text-danger">Tidak Disetujui</span><span> Oleh <?= $approval_nama . '(' . $approval_np . ')' ?> Pada <?= $approval_date ?></span></b><br>
				<span><b>Dengan alasan</b> " <?= $approval_alasan ?> "</span>
			</div>
		<?php } else {
			$setuju = '';
			$tolak = '';
		} ?>
	<?php } else {
		$setuju = '';
		$tolak = '';
	}
	?>

	<?php
	if (@$akses["persetujuan"] && (($waktu_mulai_fix != null || $waktu_mulai_fix != '') && ($waktu_selesai_fix != null || $waktu_selesai_fix != '')) && ($approval_status == '' || $approval_status == null || $approval_status == '0')) {
	?>
		<div class="alert alert-warning">
			<b><span><a><?= $sdm_name . ' (' . $sdm_np . ')' ?></a></span><br>
				Sebagai Approval Lembur oleh SDM</b>
			<br>
			<select class="form-control" onchange="form_alasan(this)" name="persetujuan_approval_sdm" style="width: 50%;" required>
				<option value='0'>Pilih Persetujuan</option>
				<option value='1' <?= $setuju ?>>Setuju</option>
				<option value='2' <?= $tolak ?>>Tidak Setuju</option>
			</select>
			<div id="form-alasan" style="display: none;">
				<b>Alasan Tidak Disetujui</b>
				<br>
				<textarea rows="2" class="form-control" name='persetujuan_alasan_sdm' id="persetujuan_alasan_sdm"></textarea>
			</div>
			<input type="hidden" name="id_pengajuan" value="<?= $id_ ?>" /><br>
			<button type="submit" class='btn btn-primary btn-xs status_button'>Simpan</button>
		</div>
	<?php } ?>
</form>

<script type="text/javascript">
	function form_alasan(obj) {
		var selectBox = obj;
		var selected = selectBox.options[selectBox.selectedIndex].value;
		var textarea = document.getElementById("form-alasan");

		if (selected === '2') {
			textarea.style.display = "block";
		} else {
			textarea.style.display = "none";
		}
	}
</script>