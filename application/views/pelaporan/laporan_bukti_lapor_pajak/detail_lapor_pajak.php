				<div class="row">
					<div class="col-md-10">
						<table>
							<tr>
								<th>Nama</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['np_karyawan'] . ' - ' . $detail['nama_karyawan'] ?></a></b></td>
							</tr>
							<tr>
								<th>Unit Kerja</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['nama_unit'] ?></a></b></td>
							</tr>
							<tr>
								<th>Tahun Pajak</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['tahun'] ?></a></b></td>
							</tr>
							<tr>
								<th>Status SPT</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['status_spt'] ?></a></b></td>
							</tr>
							<tr>
								<th>No Tanda Terima Elektronik</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['no_tanda_terima_elektronik'] ?></a></b></td>
							</tr>
							<tr>
								<th>Diunggah Tanggal</th>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;</td>
								<td><b><a><?= $detail['updated_at'] ?: $detail['created_at'] ?></a></b></td>
							</tr>
							<tr>
								<td style="padding-top:20px" colspan="3">
									<b>
										<a class="btn btn-primary" target="_blank" href="<?= base_url($file_path) ?>">Lihat Lampiran</a>
									</b>
								</td>
							</tr>
						</table>
					</div>
				</div>
