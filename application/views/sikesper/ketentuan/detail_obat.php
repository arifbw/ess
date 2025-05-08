
	<div>
		<h3 class="text-center">Riwayat Kesehatan Karyawan PERUM PERURI</h3>
		<table width="100%" class="table table-bordered table-hover" id="tabel_mcu">
			<tbody>
				<tr>
					<th class='text-center no-sort'>NO REG</th>
					<th class='text-center no-sort' colspan='5' style="text-align:left"><?= $data->no_reg ?></th>
				</tr>
				<tr>
					<th class='text-center no-sort'>NAMA KARYAWAN</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->nama_karyawan ?></span></td>
					<th class='text-center no-sort'>NP KARYAWAN</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->np_karyawan ?></span></td>
					<th class='text-center no-sort'>DEPARTEMEN</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->departemen ?></span></td>
				</tr>
				<tr>
				</tr>
				<tr>
					<th class='text-center no-sort'>DOB</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= tanggal_indonesia($data->dob) ?></span></td>
					<th class='text-center no-sort'>TEKANAN DARAH</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;color:red"><?= $data->tekanan_darah ?></span></td>
					<th class='text-center no-sort' rowspan='4'>KESIMPULAN</th>
					<td class='text-center no-sort' rowspan='4' style="text-align:left;width:20%"><span style="font-size:12px;"><?= $data->kesimpulan ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>USIA</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->usia ?></span></td>
					<th class='text-center no-sort'>VISUS KANAN</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->visus_kanan ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>SEX</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->sex ?></span></td>
					<th class='text-center no-sort'>VISUS KIRI</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->visus_kiri ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>RIWAYAT PENYAKIT DAHULU</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->riwayat_penyakit ?></span></td>
					<th class='text-center no-sort'>KENAL WARNA</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->kenal_warna ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>AYAH</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->ayah ?></span></td>
					<th class='text-center no-sort'>GIGI</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->gigi ?></span></td>
					<th class='text-center no-sort' rowspan='9'>SARAN</th>
					<td class='text-center no-sort' rowspan='9' style="text-align:left;width:20%"><span style="font-size:12px;"><?= $data->saran ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>IBU</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->ibu ?></span></td>
					<th class='text-center no-sort'>FISIK</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->fisik ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>ALERGI</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->alergi ?></span></td>
					<th class='text-center no-sort'>HEMATOLOGI</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;color:red"><?= $data->hematologi ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>MEROKOK</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->merokok ?></span></td>
					<th class='text-center no-sort'>KIMIA</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->kimia ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>ALKOHOL</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->alkohol ?></span></td>
					<th class='text-center no-sort'>HBsAg</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->hbsag ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>OLAHRAGA</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->olahraga ?></span></td>
					<th class='text-center no-sort'>ANTI HBs</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->anti_hbs ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>TB</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->tb ?></span></td>
					<th class='text-center no-sort'>URINALISA</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->urinalisa ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>BB</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->bb ?></span></td>
					<th class='text-center no-sort'>RONTGEN</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->rontgen ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>BMI</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->bmi ?></span></td>
					<th class='text-center no-sort'>EKG</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->ekg ?></span></td>
				</tr>
				<tr>
					<th class='text-center no-sort'>KESAN</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;color:red"><?= $data->kesan ?></span></td>
					<th class='text-center no-sort'>AUDIOMETRI</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;"><?= $data->audiometri ?></span></td>
					<th class='text-center no-sort'>FITNESS CATEGORY</th>
					<td class='text-center no-sort' style="text-align:left"><span style="font-size:12px;color:red"><?= $data->fitnes_category ?></span></td>
				</tr>
			</tbody>
		</table>
	</div>