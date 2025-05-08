<style type="text/css">
	.ft-profil {
		border-radius: 50%; 
		margin-top: 5%;
		margin-left: 50%;

		width: 50px;
		height: 50px;
	}

	.content-riwayat {
		margin-top: 3%;
	}

	@media only screen and (max-width: 800px) {
	  	.ft-profil {
			margin-left: 0%;

			width: 100px;
			height: 100px;
		}

		.tag-name {
			margin-left: 25%;
		}

		.tag-name table tr td {
			text-align: center;
		}
	}

	@media only screen and (max-width: 760px) {

		.tag-name {
			margin-left: 10%;
		}

		.tag-name table tr td {
			text-align: center;
		}
	}
</style>

<div class="row">
    <div class="col-md-2 col-12 text-center">
        <img class="ft-profil" src="<?= base_url('foto/profile/'.$data->nama_file); ?>" alt="Gambar Tidak Ditemukan" />
    </div>
    <div class="col-md-10 col-12 tag-name">
        <table>
            <tr>
                <td>
                    <h4>
                        <strong><?= $data->nama_pegawai; ?></strong>
                    </h4>
                </td>
            </tr>
            <tr>
                <td class="p-0">
                    <p style="color: grey;">No Pokok : <?= $data->np ?> | Unit : <?= $data->unit ?></p>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row content-riwayat">
	<div class="col-md-12 col-12">
		<h5><strong>Riwayat Pemeriksaan</strong></h5>
		<hr>
	</div>
	<div class="col-md-12 col-12">
		<div class="table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Nama Pasien</th>
						<th>Jumlah Hari</th>
						<th>Jumlah <br>Pengobatan</th>
						<th>Jumlah Beban<br>Karyawan</th>
						<th>Jumlah Tanggungan<br>Karyawan</th>
						<th>Jumlah Tanggungan<br>Perusahaan</th>
						<th>Catatan</th>
						<th>Referral</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$total_hari=0; $total_pengobatan=0; $total_beban_karyawan=0; $total_tanggungan_karyawan=0; $total_tanggungan_perusahaan=0; 
						if(!empty($riwayat)){
							foreach($riwayat as $val){
								$total_hari = $total_hari+$val->jumlah_hari;
								$total_pengobatan = $total_pengobatan+$val->jumlah_pengobatan;
								$total_beban_karyawan = $total_beban_karyawan+$val->beban_karyawan;
								$total_tanggungan_karyawan = $total_tanggungan_karyawan+$val->tanggungan_karyawan;
								$total_tanggungan_perusahaan = $total_tanggungan_perusahaan+$val->tanggungan_perusahaan;
					?>
					<tr>
						<td><?= $val->nama_pasien; ?></td>
						<td><?= $val->jumlah_hari; ?></td>
						<td><?= rupiah($val->jumlah_pengobatan, '1'); ?></td>
						<td><?= $val->beban_karyawan; ?></td>
						<td><?= rupiah($val->tanggungan_karyawan, '1'); ?></td>
						<td><?= rupiah($val->tanggungan_perusahaan, '1'); ?></td>
						<td><?= $val->catatan; ?></td>
						<td><?= $val->referral; ?></td>
					</tr>
					<?php
							}
						}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th>Total</th>
						<th><?= $total_hari; ?></th>
						<th><?= rupiah($total_pengobatan, '1'); ?></th>
						<th><?= $total_beban_karyawan; ?></th>
						<th><?= rupiah($total_tanggungan_karyawan, '1'); ?></th>
						<th><?= rupiah($total_tanggungan_perusahaan, '1'); ?></th>
						<th colspan="2"></th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<!-- <?php
		if(!empty($riwayat)){ ?>
	<div class="col-md-12 col-12 pull-right">
		<h5><strong>TOTAL PENGOBATAN : <?= $total ?></strong></h5>
	</div>
	<?php } ?> -->
</div>
<div class="row">
	<div class="col-md-12">
		<button type="button" class="pull-left btn btn-danger btn-small" data-dismiss="modal">Kembali</button>
	</div>
</div>