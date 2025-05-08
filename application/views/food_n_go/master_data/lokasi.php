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

		<?php if(!empty($success)) { ?>
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $success;?>
		</div>
		<?php } if(!empty($warning)) { ?>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $warning;?>
		</div>
		<?php } if(@$akses["lihat log"]) { ?>
		<div class='row text-right'>
			<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
			<br><br>
		</div>
		<?php } if(@$akses["tambah"]) { ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse <?php echo $panel_tambah;?>">
						<div class="panel-body">
							<form role="form" action="<?= site_url('food_n_go/master_data/lokasi/index'); ?>" id="formulir_tambah" method="post">
								<input type="hidden" name="aksi" value="tambah"/>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-2">
											<label>Nama Lokasi</label>
										</div>
										<div class="col-lg-7">
											<input class="form-control" name="nama" placeholder="Masukkan Nama Lokasi" required>
										</div>
										<div id="warning_nama" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-2">
											<label>Status</label>
										</div>
										<div class="col-lg-7">
											<label class='radio-inline'>
												<input type="radio" name="status" value="1" required>Aktif
											</label>
											<label class='radio-inline'>
												<input type="radio" name="status" value="0" required>Non Aktif
											</label>
										</div>
										<div id="warning_status" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12 text-center">
										<button type="submit" class="btn btn-primary" onclick="return cek_simpan_tambah()">Simpan</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->
		<?php 
			} if(@$this->akses["lihat"]) {
		?>
		<div class="row">
			<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_daftar_lokasi">
				<thead>
					<tr>
						<th class='text-center'>#</th>
						<th class='text-center'>Lokasi</th>
						<th class='text-center'>Status</th>
						<?php
							if(@$akses["ubah"] or @$akses["lihat log"]) {
								echo "<th class='text-center'>Aksi</th>";
							}
						?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$start = 1;
						foreach($daftar_lokasi as $val){ 
					?>
					<tr>
						<td><?= $start++; ?></td>
						<td><?= $val->nama; ?></td>
						<td><?= $val->status == '1' ? 'Aktif' : 'Non Aktif'; ?></td>
						<?php if(@$akses["ubah"] or @$akses["lihat log"]) { ?>
							<td class='text-center'>
								<?php if(@$akses["ubah"]){ ?>
									<button type='button' class='btn btn-primary btn-xs ubah' data-id="<?= $val->id ?>" data-toggle="modal" data-target="#modal_ubah">Ubah</button>
								<?php } ?>
								<?php if(@$akses["lihat log"]){ ?>
									<button class='btn btn-primary btn-xs' onclick='lihat_log(\"<?= $val->id ?>\",<?= $val->id ?>)'>Lihat Log</button>
								<?php } ?>
							</td>
						<?php } ?>
					</tr>
					<?php 
						} 
					?>
				</tbody>
			</table>
			<!-- /.table-responsive -->
		</div>

		<?php
			} if(@$akses["ubah"]) { 
		?>

		<!-- Modal -->
		<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		 	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h5 class="modal-title ubah-title" id="exampleModalLongTitle">Ubah Data</h5>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
		        		<form role="form" action="<?= site_url('food_n_go/master_data/lokasi/index'); ?>" id="formulir_tambah" method="post">
							<input type="hidden" name="aksi" value="ubah"/>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Nama Lokasi</label>
									</div>
									<div class="col-lg-7">
										<input id="nama-lokasi" class="form-control" name="nama" placeholder="Masukkan Nama Lokasi" required>
									</div>
									<div id="warning_nama" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Status</label>
									</div>
									<div class="col-lg-7">
										<label class='radio-inline'>
											<input type="radio" name="status" value="1" id="1" required>Aktif
										</label>
										<label class='radio-inline'>
											<input type="radio" name="status" value="0" id="0" required>Non Aktif
										</label>
									</div>
									<div id="warning_status" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<input type="hidden" id="no-lokasi" name="no">
							<input type="hidden" id="status-lama" name="status_lama">
							<div class="row">
								<div class="col-lg-12 text-center">
									<button type="submit" class="btn btn-primary">Simpan</button>
								</div>
							</div>
						</form>
		      		</div>
		    	</div>
		  	</div>
		</div>
		<?php } ?>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->


<script type="text/javascript">
	$(document).ready(function() {
	    $(document).on('click', '.ubah', function(){
	    	var id = $(this).data('id');

	    	$.ajax({
	    		url: "<?php echo site_url('food_n_go/master_data/lokasi/detail/'); ?>"+id,
	    		success: function(data) {
	    			$('#nama-lokasi').val(data.result.nama);
	    			$('#status-lama').val(data.result.status);
	    			$('#no-lokasi').val(data.result.id);
	    			
	    			$('#'+data.result.status).prop('checked', true);
	    		}
	    	});
	    });
	});
</script>
