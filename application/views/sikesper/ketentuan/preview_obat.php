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

				<form action="<?= (count($list_obat)>0) ? site_url('sikesper/ketentuan/daftar_obat/save') : '' ?>" method="post">
					<div class="alert alert-success alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<h4><?php echo $this->session->flashdata('success');?></h4>
						<?php if (count($list_obat)>0) { ?>
						<input type="hidden" name="aksi" value="tambah"/>
						<input type="hidden" name="jumlah" value="<?= count($list_obat) ?>"/>
						<button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin?')">SIMPAN</button>
						<?php } ?>
						<a href="<?= site_url('sikesper/ketentuan/daftar_obat') ?>" class="btn btn-danger">Kembali</a>
					</div>

					<div class="form-group table-responsive">
						<h4>DAFTAR OBAT PERURI</h4>
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_obat">
							<thead>
								<tr>
									<th class='text-center no-sort'>NO</th>
									<th class='text-center no-sort'>KODE OBAT</th>
									<th class='text-center no-sort'>JENIS</th>
									<th class='text-center no-sort'>KATEGORI</th>
									<th class='text-center no-sort'>ZAT AKTIF</th>
									<th class='text-center no-sort'>MEREK OBAT</th>
									<th class='text-center no-sort'>SEDIAAN</th>
									<th class='text-center no-sort'>DOSIS</th>
									<th class='text-center no-sort'>FARMASI</th>
									<th class='text-center no-sort'>KETERANGAN</th>
								</tr>
							</thead>
							<tbody>
								<?php for ($i=0; $i<count($list_obat); $i++) { ?>
								<tr>
									<td><?= ($i+1) ?></td>
									<td><?php $obat[$i]['kode_obat'] = $list_obat[$i]['kode_obat'] ?><?= ($list_obat[$i]['kode_obat']) ?></td>
									<td><?php $obat[$i]['jenis'] = $list_obat[$i]['jenis'] ?><?= ($list_obat[$i]['jenis']) ?></td>
									<td><?php $obat[$i]['kategori'] = $list_obat[$i]['kategori'] ?><?= ($list_obat[$i]['kategori']) ?></td>
									<td><?php $obat[$i]['zat_aktif_obat'] = $list_obat[$i]['zat_aktif_obat'] ?><?= ($list_obat[$i]['zat_aktif_obat']) ?></td>
									<td><?php $obat[$i]['merek_obat'] = $list_obat[$i]['merek_obat'] ?><?= ($list_obat[$i]['merek_obat']) ?></td>
									<td><?php $obat[$i]['sediaan'] = $list_obat[$i]['sediaan'] ?><?= ($list_obat[$i]['sediaan']) ?></td>
									<td><?php $obat[$i]['dosis'] = $list_obat[$i]['dosis'] ?><?= ($list_obat[$i]['dosis']) ?></td>
									<td><?php $obat[$i]['farmasi'] = $list_obat[$i]['farmasi'] ?><?= ($list_obat[$i]['farmasi']) ?></td>
									<td><?php $obat[$i]['keterangan'] = $list_obat[$i]['keterangan'] ?><?= ($list_obat[$i]['keterangan']) ?>
									<input type="hidden" name="post[<?= $i ?>]" value='<?= json_encode($obat[$i]) ?>'></td>
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
				$('#tabel_obat').dataTable({
					paging: false,
				});
			});
		</script>