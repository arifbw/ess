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

				<form action="<?= site_url('informasi/skep/save') ?>" method="post">
					<div class="alert alert-success alert-dismissable">
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						<h4><?php echo $this->session->flashdata('success');?></h4>
						<?php if (count($list_skep)>0) { ?>
						<input type="hidden" name="aksi" value="tambah"/>
						<input type="hidden" name="jumlah" value="<?= count($list_skep) ?>"/>
						<button type="submit" class="btn btn-success" onclick="return confirm('Apakah anda yakin?')">SIMPAN</button>
						<?php } ?>
						<a href="<?= site_url('informasi/skep') ?>" class="btn btn-danger">Kembali</a>
					</div>

					<div class="form-group table-responsive">
						<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_skep">
							<thead>
								<tr>
									<th class='text-center no-sort'>NO</th>
									<th class='text-center no-sort' style="width:15%">NP</th>
									<th class='text-center no-sort'>NAMA</th>
									<th class='text-center no-sort' style="width:50%">NOMOR</th>
									<th class='text-center no-sort' style="width:20%">TANGGAL AKTIF</th>
									<th class='text-center no-sort' style="width:20%">TANGGAL TAMPIL</th>
									<th class='text-center no-sort'>FILE 1</th>
									<th class='text-center no-sort'>FILE 2</th>
								</tr>
							</thead>
							<tbody>
								<?php for ($i=0; $i<count($list_skep); $i++) { ?>
								<tr>
									<td><?= ($i+1) ?></td>
									<td><?php $skep[$i]['np_karyawan'] = $list_skep[$i]['np'] ?><?= ($list_skep[$i]['np']) ?></td>
									<td><?php $skep[$i]['nama_karyawan'] = $list_skep[$i]['nama'] ?><?= ($list_skep[$i]['nama']) ?></td>
									<td><?php $skep[$i]['nomor_skep'] = $list_skep[$i]['nomor'] ?><?= ($list_skep[$i]['nomor']) ?></td>
									<td><?php $skep[$i]['aktif_tanggal_skep'] = $list_skep[$i]['tanggal'] ?><?= ($list_skep[$i]['tanggal']) ?></td>
									<td><?php $skep[$i]['tanggal_tampil'] = $list_skep[$i]['tanggal_tampil'] ?><?= ($list_skep[$i]['tanggal_tampil']) ?></td>
									<td><?php $skep[$i]['file1_skep'] = $list_skep[$i]['file_umum'] ?><?= ($list_skep[$i]['file_umum']) ?></td>
									<td><?php $skep[$i]['file2_skep'] = $list_skep[$i]['file_individu'] ?><?= ($list_skep[$i]['file_individu']) ?> <input type="hidden" name="post[<?= $i ?>]" value='<?= json_encode($skep[$i]) ?>'></td>
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
				$('#tabel_skep').dataTable({
					paging: false,
				});
			});
		</script>