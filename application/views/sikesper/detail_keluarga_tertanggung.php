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
        <img class="ft-profil" src="<?= is_file('./foto/profile/'.$data->nama_file) ? base_url('foto/profile/'.$data->nama_file):base_url('foto/profile/default.jpg'); ?>" alt="Gambar Tidak Ditemukan" />
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
                    <p style="color: grey;">NP Karyawan : <?= $data->np ?> | Unit : <?= $data->unit ?></p>
                    <p style="color: grey;"><?= $data->tempat_lahir ?>, <?= tanggal_indonesia($data->tanggal_lahir) ?> (<?= $data->usia ?> Tahun)</p>
                    <p style="color: grey;">Jenis Kelamin : <?= $data->jenis_kelamin ?> | Agama : <?= $data->agama ?></p>
                    <!--<p style="color: grey;">BPJS ID : <?= $data->bpjs_id ?> (<?= $data->class_bpjs ?>)</p>-->
                    <p style="color: grey;">BPJS ID : <?= $data->bpjs_kesehatan ?> (<?= $data->class_bpjs!=null?$data->class_bpjs:'I' ?>)</p>
                    <p style="color: grey;">Tanggal Daftar : <?= tanggal_indonesia($data->start_date) ?></p>
                    <p style="color: grey;">Kelas Perawatan : <?= $data->kelas ?></p>
                </td>
            </tr>
        </table>
    </div>
	<div class="col-lg-12">
		<div class="alert alert-success alert-dismissable">
			<!--<b>Kartu BPJS Bisa Didownload Di Mobile JKN Dengan USER ID (<?= $data->bpjs_id ?>)</b>-->
			<b>Kartu BPJS Bisa Didownload Di Mobile JKN Dengan USER ID (<?= $data->bpjs_kesehatan ?>)</b>
		</div>
	</div>
</div>

<div class="row content-riwayat">
	<div class="col-md-12 col-12">
		<h5><strong>Keluarga Tertanggung</strong></h5>
		<hr>
	</div>
	<div class='col-md-12 table-responsive'>
		<?php if ($keluarga->num_rows()>0) { ?>
		<table style="width:100%" class="table table-bordered">
			<thead>
				<tr>
					<th>No</th>
					<th>Keluarga</th>
					<th>Nama Lengkap</th>
					<th>Tempat Lahir</th>
					<th>Tanggal Lahir</th>
					<th>Usia</th>
					<th>ID BPJS</th>
					<th>Kelas BPJS</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php $no=1; foreach ($keluarga->result_array() as $klt) { ?>
				<tr>
					<td><?= $no++ ?></td>
					<td><?= $klt['tipe_keluarga'] ?></td>
					<td><?= $klt['nama_lengkap'] ?></td>
					<td><?= $klt['tempat_lahir_keluarga'] ?></td>
					<td><?= tanggal_indonesia($klt['tanggal_lahir']) ?></td>
					<td><?= $klt['usia'] ?></td>
					<td><?= $klt['bpjs_id_keluarga'] ?></td>
					<td><?= $klt['class_bpjs_keluarga']!=null?$klt['class_bpjs_keluarga']:'I' ?></td>
					<td><?= $klt['status_tanggungan'].($klt['tanggal_efektif']!=null ? ' sejak '.tanggal_indonesia($klt['tanggal_efektif']):'') ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
		<br><br>
		<?php } else { ?>
		<h3>Tidak Ada Keluarga Tertanggung</h3>
		<?php } ?>
	</div>
</div>
<br><br><br><br>
<div class="row">
	<div class="col-md-12">
		<button type="button" class="pull-left btn btn-danger btn-small" data-dismiss="modal">Kembali</button>
	</div>
</div>

<script>
	$('#tabel_donor_detail').DataTable({ 
					
					"iDisplayLength": 10,
				});
</script>