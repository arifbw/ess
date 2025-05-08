<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />

<form role="form" action="<?= base_url(); ?>pelaporan/persetujuan/kelahiran_anak/save_approve/" id="formulir_tambah" method="post">	
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
					<th>Jenis Kelamin Anak</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= ($detail['jenis_kelamin_anak']=='L')?'Laki-Laki':'Perempuan' ?></a></b></td>
				</tr>
				<tr>
					<th>Tempat, Tanggal Lahir Anak</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['tempat_lahir_anak'].', '.$detail['tanggal_lahir_anak'] ?></a></b></td>
				</tr>
				<tr>
					<th>Anak Ke</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['anak_ke'] ?></a></b></td>
				</tr>
				<tr>
					<th>No Akta Kelahiran</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['no_akta_kelahiran'] ?></a></b></td>
				</tr>
				<?php if ($detail['no_dokumen_lain'] != '' && $detail['no_dokumen_lain'] != NULL) : ?>
					<tr>
						<th>No Dokumen Lain</th>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
						<td><b><a><?= $detail['no_dokumen_lain'] ?></a></b></td>
					</tr>
				<?php endif; ?>
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
							<a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/kelahiran_anak/akta_kelahiran/'.$detail['akta_kelahiran']) ?>">Unduh Akta Kelahiran</a>
							<?php if ($detail['dokumen_lain'] != '' && $detail['dokumen_lain'] != NULL) : ?>
								<a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/kelahiran_anak/dokumen_lain/'.$detail['dokumen_lain']) ?>">Unduh Dokumen Lain</a>
							<?php endif; ?>
						</b>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<br>

	<div class="alert alert-<?= $approval_warna ?>">
		<strong><a class="text-<?= $approval_warna ?>"><?= $detail['approval_np'].' | '.$detail['approval_nama'] ?></a></strong><br>
		<?php if (($detail['approval_status']=='0' || $detail['approval_status']==null) && $detail['approval_np']==$_SESSION["no_pokok"]): ?>
			<br>
			<select class="form-control select2" name='status_approval' onchange="form_alasan(this)" style="width : 100%" required>
				<option value=''>Berikan Persetujuan</option>
				<option value='1' <?= ($detail['approval_status']=='1') ? 'selected' : ''; ?>>Setuju</option>
				<option value='2' <?= ($detail['approval_status']=='2') ? 'selected' : ''; ?>>Tidak Setuju</option>
			</select>

			<div id="form-alasan" style="display: <?= ($detail['approval_status']=='2') ? '':'none' ?>;">
				<b>Alasan Tidak Disetujui</b>
				<br>
				<textarea rows="2" class="form-control" name='alasan'><?= $detail['approval_alasan'] ?></textarea>
			</div>
		<?php else: ?>
			<p><?= $approval_status ?></p>
			<?php if ($detail['approval_status']=='2'): ?>
				<p style="margin-top: 0">Alasan : <?= $detail['approval_alasan'] ?></p>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($detail['approval_status']=='1' || $detail['approval_status']=='2'): ?>
			<a class="text-<?= $approval_warna ?>"><?= 'Tanggal Persetujuan : '.$detail['approval_date'] ?></a><br>
		<?php endif; ?>
	</div>

	<?php if (($detail['approval_status']=='0' || $detail['approval_status']==null) && $detail['approval_np']==$_SESSION["no_pokok"]): ?>
		<div class="row">
			<div class="col-lg-12 text-right">
				<input type="hidden" name="id_" value="<?= $detail['id'] ?>">
				<input type="submit" name="submit" id='persetujuan_button' value="Simpan" class="btn btn-block btn-<?= $approval_warna ?>">
			</div>
		</div>
	<?php endif; ?>
</form>


<script type="text/javascript">
	function form_alasan(obj){
		var textarea = document.getElementById("form-alasan");
		var selectBox = obj;
		var selected = selectBox.options[selectBox.selectedIndex].value;

		if(selected === '2'){
			textarea.style.display = "block";
		}
		else{
			textarea.style.display = "none";
		}
	}
	$('.datetimepicker5').datetimepicker({
		format: 'YYYY-MM-DD HH:mm'
	});
	$('.select2').select2();
</script>