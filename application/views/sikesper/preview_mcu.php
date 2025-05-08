		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul;?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<form action="<?= (count($list_mcu)>0) ? site_url('sikesper/hasil_mcu/save') : '' ?>" method="post">
					<div class="alert alert-success alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<h4><?php echo $this->session->flashdata('success');?></h4>
						<?php if (count($list_mcu)>0) { ?>
						<input type="hidden" name="aksi" value="tambah"/>
						<input type="hidden" name="jumlah" value="<?= count($list_mcu) ?>"/>
						<input type="hidden" name="tanggal_mcu" value="<?= $tanggal_mcu ?>"/>
						<input type="hidden" name="vendor" value="<?= $vendor ?>"/>
						<button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin?')">SIMPAN</button>
						<?php } ?>
						<a href="<?= site_url('sikesper/hasil_mcu') ?>" class="btn btn-danger">Kembali</a>
					</div>

					<div class="form-group table-responsive">
						<h4>REKAPITULASI HASIL MEDICAL CHECK UP <?= $tanggal_mcu ?> VENDOR <?= $vendor ?></h4>
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_mcu">
							<thead>
								<tr>
									<th class='text-center no-sort'>NO</th>
									<th class='text-center no-sort'>NO REG</th>
									<th class='text-center no-sort'>NAMA KARYAWAN</th>
									<th class='text-center no-sort'>NP KARWAYAN</th>
									<th class='text-center no-sort'>DEPARTEMEN</th>
									<th class='text-center no-sort'>DOB</th>
									<th class='text-center no-sort'>USIA</th>
									<th class='text-center no-sort'>SEX</th>
									<th class='text-center no-sort'>RIWAYAT PENYAKIT DAHULU</th>
									<th class='text-center no-sort'>AYAH</th>
									<th class='text-center no-sort'>IBU</th>
									<th class='text-center no-sort'>ALERGI</th>
									<th class='text-center no-sort'>MEROKOK</th>
									<th class='text-center no-sort'>ALKOHOL</th>
									<th class='text-center no-sort'>OLAHRAGA</th>
									<th class='text-center no-sort'>TB</th>
									<th class='text-center no-sort'>BB</th>
									<th class='text-center no-sort'>BMI</th>
									<th class='text-center no-sort'>KESAN</th>
									<th class='text-center no-sort'>TEKANAN DARAH</th>
									<th class='text-center no-sort'>VISUS KANAN</th>
									<th class='text-center no-sort'>VISUS KIRI</th>
									<th class='text-center no-sort'>KENAL WARNA</th>
									<th class='text-center no-sort'>GIGI</th>
									<th class='text-center no-sort'>FISIK</th>
									<th class='text-center no-sort'>HEMATOLOGI</th>
									<th class='text-center no-sort'>KIMIA</th>
									<th class='text-center no-sort'>HBsAg</th>
									<th class='text-center no-sort'>ANTI HBs</th>
									<th class='text-center no-sort'>URINALISA</th>
									<th class='text-center no-sort'>RONTGEN</th>
									<th class='text-center no-sort'>EKG</th>
									<th class='text-center no-sort'>AUDIOMETRI</th>
									<th class='text-center no-sort'>KESIMPULAN</th>
									<th class='text-center no-sort'>SARAN</th>
									<th class='text-center no-sort'>FITNES CATEGORY</th>
									<th class='text-center no-sort'>KOLESTEROL</th>
									<th class='text-center no-sort'>GULA DARAH</th>
									<th class='text-center no-sort'>ASAM URAT</th>
									<th class='text-center no-sort'>MASSA LEMAK</th>
									<th class='text-center no-sort'>KLASIFIKASI TUBUH</th>
									<th class='text-center no-sort'>KELUHAN SAAT INI</th>
								</tr>
							</thead>
							<tbody>
								<?php for ($i=0; $i<count($list_mcu); $i++) { ?>
								<tr>
									<td><?= ($i+1) ?></td>
									<td><?php $mcu[$i]['no_reg'] = $list_mcu[$i]['no_reg'] ?><?= ($list_mcu[$i]['no_reg']) ?></td>
									<td><?php $mcu[$i]['nama_karyawan'] = $list_mcu[$i]['nama_karyawan'] ?><?= ($list_mcu[$i]['nama_karyawan']) ?></td>
									<td><?php $mcu[$i]['np_karyawan'] = $list_mcu[$i]['np_karyawan'] ?><?= ($list_mcu[$i]['np_karyawan']) ?></td>
									<td><?php $mcu[$i]['departemen'] = $list_mcu[$i]['departemen'] ?><?= ($list_mcu[$i]['departemen']) ?></td>
									<td><?php $mcu[$i]['dob'] = $list_mcu[$i]['dob'] ?><?= tanggal_indonesia($list_mcu[$i]['dob']) ?></td>
									<td><?php $mcu[$i]['usia'] = $list_mcu[$i]['usia'] ?><?= ($list_mcu[$i]['usia']) ?></td>
									<td><?php $mcu[$i]['sex'] = $list_mcu[$i]['sex'] ?><?= ($list_mcu[$i]['sex']) ?></td>
									<td><?php $mcu[$i]['riwayat_penyakit'] = $list_mcu[$i]['riwayat_penyakit'] ?><?= ($list_mcu[$i]['riwayat_penyakit']) ?></td>
									<td><?php $mcu[$i]['ayah'] = $list_mcu[$i]['ayah'] ?><?= ($list_mcu[$i]['ayah']) ?></td>
									<td><?php $mcu[$i]['ibu'] = $list_mcu[$i]['ibu'] ?><?= ($list_mcu[$i]['ibu']) ?></td>
									<td><?php $mcu[$i]['alergi'] = $list_mcu[$i]['alergi'] ?><?= ($list_mcu[$i]['alergi']) ?></td>
									<td><?php $mcu[$i]['merokok'] = $list_mcu[$i]['merokok'] ?><?= ($list_mcu[$i]['merokok']) ?></td>
									<td><?php $mcu[$i]['alkohol'] = $list_mcu[$i]['alkohol'] ?><?= ($list_mcu[$i]['alkohol']) ?></td>
									<td><?php $mcu[$i]['olahraga'] = $list_mcu[$i]['olahraga'] ?><?= ($list_mcu[$i]['olahraga']) ?></td>
									<td><?php $mcu[$i]['tb'] = $list_mcu[$i]['tb'] ?><?= ($list_mcu[$i]['tb']) ?></td>
									<td><?php $mcu[$i]['bb'] = $list_mcu[$i]['bb'] ?><?= ($list_mcu[$i]['bb']) ?></td>
									<td><?php $mcu[$i]['bmi'] = $list_mcu[$i]['bmi'] ?><?= ($list_mcu[$i]['bmi']) ?></td>
									<td><?php $mcu[$i]['kesan'] = $list_mcu[$i]['kesan'] ?><?= ($list_mcu[$i]['kesan']) ?></td>
									<td><?php $mcu[$i]['tekanan_darah'] = $list_mcu[$i]['tekanan_darah'] ?><?= ($list_mcu[$i]['tekanan_darah']) ?></td>
									<td><?php $mcu[$i]['visus_kanan'] = $list_mcu[$i]['visus_kanan'] ?><?= ($list_mcu[$i]['visus_kanan']) ?></td>
									<td><?php $mcu[$i]['visus_kiri'] = $list_mcu[$i]['visus_kiri'] ?><?= ($list_mcu[$i]['visus_kiri']) ?></td>
									<td><?php $mcu[$i]['kenal_warna'] = $list_mcu[$i]['kenal_warna'] ?><?= ($list_mcu[$i]['kenal_warna']) ?></td>
									<td><?php $mcu[$i]['gigi'] = $list_mcu[$i]['gigi'] ?><?= ($list_mcu[$i]['gigi']) ?></td>
									<td><?php $mcu[$i]['fisik'] = $list_mcu[$i]['fisik'] ?><?= ($list_mcu[$i]['fisik']) ?></td>
									<td><?php $mcu[$i]['hematologi'] = $list_mcu[$i]['hematologi'] ?><?= ($list_mcu[$i]['hematologi']) ?></td>
									<td><?php $mcu[$i]['kimia'] = $list_mcu[$i]['kimia'] ?><?= ($list_mcu[$i]['kimia']) ?></td>
									<td><?php $mcu[$i]['hbsag'] = $list_mcu[$i]['hbsag'] ?><?= ($list_mcu[$i]['hbsag']) ?></td>
									<td><?php $mcu[$i]['anti_hbs'] = $list_mcu[$i]['anti_hbs'] ?><?= ($list_mcu[$i]['anti_hbs']) ?></td>
									<td><?php $mcu[$i]['urinalisa'] = $list_mcu[$i]['urinalisa'] ?><?= ($list_mcu[$i]['urinalisa']) ?></td>
									<td><?php $mcu[$i]['rontgen'] = $list_mcu[$i]['rontgen'] ?><?= ($list_mcu[$i]['rontgen']) ?></td>
									<td><?php $mcu[$i]['ekg'] = $list_mcu[$i]['ekg'] ?><?= ($list_mcu[$i]['ekg']) ?></td>
									<td><?php $mcu[$i]['audiometri'] = $list_mcu[$i]['audiometri'] ?><?= ($list_mcu[$i]['audiometri']) ?></td>
									<td><?php $mcu[$i]['kesimpulan'] = $list_mcu[$i]['kesimpulan'] ?><?= ($list_mcu[$i]['kesimpulan']) ?></td>
									<td><?php $mcu[$i]['saran'] = $list_mcu[$i]['saran'] ?><?= ($list_mcu[$i]['saran']) ?></td>
									<td><?php $mcu[$i]['fitnes_category'] = $list_mcu[$i]['fitnes_category'] ?><?= ($list_mcu[$i]['fitnes_category']) ?>
									<td><?php $mcu[$i]['kolestrol'] = $list_mcu[$i]['kolestrol'] ?><?= ($list_mcu[$i]['kolestrol']) ?>
									<td><?php $mcu[$i]['gula_darah'] = $list_mcu[$i]['gula_darah'] ?><?= ($list_mcu[$i]['gula_darah']) ?>
									<td><?php $mcu[$i]['asam_urat'] = $list_mcu[$i]['asam_urat'] ?><?= ($list_mcu[$i]['asam_urat']) ?>
									<td><?php $mcu[$i]['massa_lemak'] = $list_mcu[$i]['massa_lemak'] ?><?= ($list_mcu[$i]['massa_lemak']) ?>
									<td><?php $mcu[$i]['klasifikasi_tubuh'] = $list_mcu[$i]['klasifikasi_tubuh'] ?><?= ($list_mcu[$i]['klasifikasi_tubuh']) ?>
									<td><?php $mcu[$i]['keluhan_saat_ini'] = $list_mcu[$i]['keluhan_saat_ini'] ?><?= ($list_mcu[$i]['keluhan_saat_ini']) ?>
									<input type="hidden" name="post[<?= $i ?>]" value='<?= json_encode($mcu[$i]) ?>'></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<!-- /.table-responsive -->
					</div>	
				</form>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<script>	
			$(document).ready(function() {
				$('#tabel_mcu').dataTable({
					paging: false,
				});
			});
		</script>