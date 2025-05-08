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
    <div class="col-md-10 col-12 tag-name">
        <table>
            <tr>
                <td>
                    <h3><strong><?= $data->nama_pegawai; ?></strong></h3>
                    <h4><strong>Jumlah Donor : <?= !empty($riwayat) ? count($riwayat) : 0; ?> Kali</strong></h4>
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
		<h4><strong>Riwayat Pemeriksaan</strong></h4>
		<hr>
	</div>
	<div class="col-md-12 col-12">
		<div class="table-responsive">
			<table class="table table-striped" id="tabel_donor_detail">
				<thead>
					<tr>
						<th width="10%">No</th>
						<th width="25%">Tipe</th>
						<th width="25%" class="text-center">Diagnosa</th>
						<th width="40%">Tanggal Pengujian</th>
					</tr>
				</thead>
				<tbody>
					<?php
						if(!empty($riwayat)){
							$no=1; foreach($riwayat as $val){
					?>
					<tr>
						<td><?= $no++; ?></td>
						<td><?= $val->examination_type; ?></td>
						<td class="text-center"><?= $val->diagnosa; ?></td>
						<td><?= ($val->exam_date!=null) ? tanggal_indonesia($val->exam_date) : ''; ?></td>
					</tr>
					<?php
							}
						}
					?>
				</tbody>
			</table>
		</div>
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