<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />

<form role="form" action="<?= base_url(); ?>pelaporan/persetujuan/pernikahan/save_approve/" id="formulir_tambah" method="post">	
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
					<th>Nama Pasangan</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['nama_pasangan'] ?></a></b></td>
				</tr>
				<tr>
					<th>Tempat, Tanggal Lahir Pasangan</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['tempat_lahir_pasangan'].', '.$detail['tanggal_lahir_pasangan']?></a></b></td>
				</tr>
				<tr>
					<th>Agama Pasangan</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['agama_pasangan'] ?></a></b></td>
				</tr>
				<tr>
					<th>Pekerjaan Pasangan</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['pekerjaan_pasangan'] ?></a></b></td>
				</tr>
				<tr>
					<th>Alamat Pasangan</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['alamat_pasangan'] ?></a></b></td>
				</tr>
				<tr>
					<th>Hari Pernikahan</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['hari_pernikahan'] ?></a></b></td>
				</tr>
				<tr>
					<th>Tanggal Pernikahan</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['tanggal_pernikahan'] ?></a></b></td>
				</tr>
				<tr>
					<th>Tempat Pernikahan</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['tempat_pernikahan'] ?></a></b></td>
				</tr>
				<tr>
					<th>No Surat Keterangan Nikah</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['no_surat_keterangan_nikah'] ?></a></b></td>
				</tr>
				<tr>
					<th>No KTP</th>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
					<td><b><a><?= $detail['no_ktp'] ?></a></b></td>
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
							<a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/pernikahan/surat_keterangan_nikah/'.$detail['surat_keterangan_nikah']) ?>">Unduh Surat Keterangan Nikah</a>
						</b>
						<b>
							<a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/pernikahan/pas_foto/'.$detail['pas_foto']) ?>">Unduh Pas Foto Suami dan Istri</a>
						</b>
						<b>
							<a class="btn btn-primary" target="_blank" href="<?= base_url('uploads/pelaporan/pernikahan/ktp/'.$detail['ktp']) ?>">Unduh KTP</a>
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