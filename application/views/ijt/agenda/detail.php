<?php
setlocale(LC_TIME, 'id_ID.utf8', 'id_ID', 'Indonesian');
?>
<table class="text-start" style="margin-bottom: 20px;">
	<tbody>
		<tr>
			<th>Jabatan</th>
			<td>: <?= @$detail->nama_jabatan ?></td>
		</tr>
		<tr>
			<th>Kegiatan</th>
			<td>: <?= @$detail->kegiatan ?></td>
		</tr>
		<tr>
			<th>Tanggal</th>
			<td>: <?= strftime("%e %B %Y", strtotime(@$detail->tanggal)) ?>, <?= strftime("%H:%M", strtotime(@$detail->tanggal)); ?></td>
		</tr>
		<tr>
			<th>Lokasi</th>
			<td>: <?= @$detail->tempat ?></td>
		</tr>
		<?php if ($this->session->userdata("grup") == '5'): ?>
			<tr>
				<th>Status Konfirmasi</th>
				<td>: <span style="border-radius: 999px;">
						<?= (@$detail->status == '3') ? 'Reschedule' : ((@$detail->status == '1') ? 'Hadir' : ((@$detail->status == '2') ? 'Tidak Hadir' : 'Belum Konfirmasi')); ?>
					</span></td>
			</tr>
			<tr>
				<th>Waktu Konfirmasi</th>
				<td>: <?= @$detail->waktu_konfirm != null ? strftime("%e %B %Y", strtotime(@$detail->waktu_konfirm)) . ', ' . strftime("%H:%M", strtotime(@$detail->waktu_konfirm)) : '-'; ?></td>
			</tr>
			<?php if (@$detail->status == '3'): ?>
				<tr>
					<th>Alasan Reschedule</th>
					<td>: <?= @$detail->alasan_reschedule; ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>
	</tbody>
</table>
<?php if ($this->session->userdata("grup") !== '5'): ?>
	<table class="table" id="table-data">
		<thead>
			<tr>
				<th style="width:10px;">No</th>
				<th style="width: max-content;">Nama</th>
				<th style="width:fit-content;">Waktu Konfirmasi</th>
				<th style="width:250px; text-align:center;">Status Kehadiran</th>
			</tr>
		</thead>

		<php>
			<?php $no = 1;
			foreach ($applyer as $item): ?>
				<tr>
					<td><?= $no; ?></td>
					<td><b><?= $item->no_pokok; ?></b> <?= $item->nama; ?></td>
					<td><?= $item->waktu_konfirm != null ? strftime("%e %B %Y, %H:%M", strtotime($item->waktu_konfirm)) : '-' ?></td>
					<td>
						<?php if ($item->status == '3'): ?>
							<strong style="color:orange">Reschedule</strong> <br> <small style="color:gray;"><b>Alasan:</b> <?= $item->alasan_reschedule ?></small>
							<!-- <?= $item->alasan_reschedule ?> -->
						<?php else: ?>
							<span style="border-radius: 999px;" class="btn btn-pill btn-sm <?= (($item->status == '1') ? 'text-success' : (($item->status == '2') ? 'text-danger' : 'text-secondary')); ?>">
								<?= (($item->status == '1') ? 'Hadir' : (($item->status == '2') ? 'Tidak Hadir' : 'Belum Konfirmasi')); ?>
							</span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach;
			$no++; ?>
		</php>
	</table>
	<script>
		$('#table-data').DataTable({
			columnDefs: [{
					width: "10px",
					targets: 0
				},
				{
					width: "max-content",
					targets: 1
				},
				{
					width: "160px",
					class: 'text-center',
					targets: [2, 3]
				}
			],
			autoWidth: false

		});
	</script>
<?php endif; ?>