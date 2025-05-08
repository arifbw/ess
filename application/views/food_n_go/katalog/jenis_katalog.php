<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />

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

		<?php
			if(!empty($success)){
		?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $success;?>
				</div>
		<?php
			}
			if(!empty($warning)){
		?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $warning;?>
				</div>
		<?php
			}
			if(@$akses["lihat log"]){
				echo "<div class='row text-right'>";
					echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
					echo "<br><br>";
				echo "</div>";
			}
			if(@$akses["tambah"]){
		?>
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
								<form role="form" action="" id="formulir_tambah" method="post">
									<input type="hidden" name="aksi" value="tambah"/>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Nama Lokasi</label>
											</div>
											<div class="col-lg-7">
												<select class="lokasi-api" name="id_lokasi">
													
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Nama Penyedia</label>
											</div>
											<div class="col-lg-7">
												<select class="penyedia-api" name="id_penyedia" style="width: 100%;">
													
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Menu</label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="nama" value="" placeholder="Nama Menu">
											</div>
											<div id="warning_nama_menu" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Jenis</label>
											</div>
											<div class="col-lg-7">
                                                <select class="form-control" name="jenis" id="jenis_menu" required>
                                                    <option value="" data-nama_mst_bbm="">-- Pilih --</option>
                                                    <option value="Snack">Snack</option>
                                                    <option value="Makanan">Makanan</option>
                                                    <option value="Minuman">Minuman</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
											</div>
											<div id="warning_id_mst_bbm" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Harga</label>
											</div>
											<div class="col-lg-7">
												<input class="form-control" name="harga" value="" placeholder="Harga Menu">
											</div>
											<div id="warning_harga" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Status</label>
											</div>
											<div class="col-lg-7">
												<label class='radio-inline'>
													<?php
														if(strcmp($status,"1")==0){
															$checked="checked='checked'";
														}
														else{
															$checked="";
														}
													?>
													<input type="radio" name="status" id="status_tambah_aktif" value="aktif" <?php echo $checked;?>>Aktif
												</label>
												<label class='radio-inline'>
													<?php
														if(strcmp($status,"0")==0){
															$checked="checked='checked'";
														}
														else{
															$checked="";
														}
													?>
													<input type="radio" name="status" id="status_tambah_non_aktif" value="non aktif" <?php echo $checked;?>>Non Aktif
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
			}
			
			if(@$this->akses["lihat"]){
		?>
				<div class="row">
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_jenis_katalog">
						<thead>
							<tr>
								<th class='text-center'>#</th>
								<th class='text-center'>Nama Menu</th>
								<th class='text-center'>Jenis</th>
								<th class='text-center'>Harga</th>
								<th class='text-center'>Nama Penyedia</th>
								<th class='text-center'>Lokasi</th>
								<th class='text-center'>Status</th>
								<?php
									if(@$akses["ubah"] or @$akses["lihat log"]){
										echo "<th class='text-center'>Aksi</th>";
									}
								?>
							</tr>
						</thead>
						<tbody>
							<?php
								$start = 1;
								foreach ($daftar_katalog as $val) {
							?>
							<tr>
								<td><?= $start++; ?></td>
								<td><?= $val->nama; ?></td>
								<td><?= $val->jenis; ?></td>
								<td><?= $val->harga; ?></td>
								<td><?= $val->nama_penyedia; ?></td>
								<td><?= $val->lokasi; ?></td>
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
			}
			
			if(@$akses["ubah"]){
		?>
				<!-- Modal -->
				<div class="modal fade" id="modal_ubah" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-body">
								<form role="form" action="" id="formulir_tambah" method="post">
									<input type="hidden" name="aksi" value="ubah"/>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Nama Lokasi</label>
											</div>
											<div class="col-lg-7">
												<select class="lokasi-ubah" name="id_lokasi">
													
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Nama Penyedia</label>
											</div>
											<div class="col-lg-7">
												<select id="penyedia-ubah" name="id_penyedia" style="width: 100%;">
													<!-- <?php foreach ($penyedia as $value) { ?>
														<option value="<?= $value['id'] ?>"><?= $value['text'] ?></option>
													<?php } ?> -->
												</select>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Menu</label>
											</div>
											<div class="col-lg-7">
												<input id="menu-makanan" class="form-control" name="nama" value="" placeholder="Nama Menu">
											</div>
											<div id="warning_nama_menu" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Jenis</label>
											</div>
											<div class="col-lg-7">
                                                <select class="form-control" name="jenis" id="jenis-makanan" required>
                                                    <option value="" data-nama_mst_bbm="">-- Pilih --</option>
                                                    <option value="Snack">Snack</option>
                                                    <option value="Makanan">Makanan</option>
                                                    <option value="Minuman">Minuman</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
											</div>
											<div id="warning_id_mst_bbm" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Harga</label>
											</div>
											<div class="col-lg-7">
												<input id="harga-makanan" class="form-control" name="harga" value="" placeholder="Harga Menu">
											</div>
											<div id="warning_harga" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<div class="row">
										<div class="form-group">
											<div class="col-lg-2">
												<label>Status</label>
											</div>
											<div class="col-lg-7">
												<label class='radio-inline'>
													<input type="radio" name="status" id="1" value="aktif">Aktif
												</label>
												<label class='radio-inline'>
													<input type="radio" name="status" id="0" value="non aktif">Non Aktif
												</label>
											</div>
											<div id="warning_status" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<input id="no-katalog" type="hidden" name="id">
									<div class="row">
										<div class="col-lg-12 text-center">
											<button type="submit" class="btn btn-primary">Simpan</button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
		<?php
			}
		?>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script type="text/javascript">
	function generateOption(el, url, text)
	{
		var option = 
			$(el).select2({
				width: "100%",
		       	allowClear: true,
		       	placeholder: text,
		       	ajax: {
		          	dataType: 'json',
		          	url: url,
		          	delay: 800,
		          	data: function(params) {
		            	return {
		              		search: params.term
		            	}
		          	},
		          	Results: function (data, page) {
		          		return {
		            		results: data
		          		};
		        	},
		      	}
		    });

		return option;
	}

	$(document).ready(function() {
		
		$('.penyedia-api').select2({
	    	placeholder: "Silahkan Pilih Lokasi"
	    });

		$('#penyedia-ubah').select2();

		generateOption('.lokasi-api', '<?= site_url('food_n_go/master_data/jenis_katalog/daftar_lokasi/'); ?>', 'Masukan Lokasi');

		$(document).on('click', '.ubah', function() {
			var id = $(this).data('id');
			var lokasiOption = $('.lokasi-ubah');
			var penyediaOption = $('#penyedia-ubah');
			
			$.ajax({
				url: '<?= site_url('food_n_go/master_data/jenis_katalog/detail/'); ?>'+id,
				success: function(data) {
					var val = data.result;

					var lokasi = generateOption('.lokasi-ubah', '<?= site_url('food_n_go/master_data/jenis_katalog/daftar_lokasi/'); ?>', 'Masukan Lokasi');

					var penyedia = generateOption('#penyedia-ubah', '<?= site_url('food_n_go/master_data/jenis_katalog/daftar_penyedia/'); ?>'+val.lokasi, 'Masukan Penyedia');

					$('#no-katalog').val(val.id);

					$('#menu-makanan').val(val.nama);
					$('#jenis-makanan').val(val.jenis);
					$('#harga-makanan').val(val.harga);

					$('#'+val.status).prop('checked', true);
				}
			}).then(function(data){
				var value = data.result;

				var optionLokasi = new Option(value.nama_lokasi, value.lokasi, true, true);
			    lokasiOption.append(optionLokasi).trigger('change');

			    var data = {id: value.lokasi, text: value.nama_lokasi};
			    // manually trigger the `select2:select` event
			    lokasiOption.trigger({
			        type: 'select2:select',
			        params: {
			            data: data
			        }
			    });

			    var optionPenyedia = new Option(value.nama_penyedia, value.penyedia, true, true);
			    penyediaOption.append(optionPenyedia).trigger('change');

			    var data = {id: value.penyedia, text: value.nama_penyedia};
			    // manually trigger the `select2:select` event
			    penyediaOption.trigger({
			        type: 'select2:select',
			        params: {
			            data: data
			        }
			    });
			});
		});

		$(document).on('change', '.lokasi-api', function() {
	    	var lokasi = $(this).val();
	    	var url = '<?= site_url('food_n_go/master_data/jenis_katalog/daftar_penyedia/'); ?>'+lokasi;

	    	generateOption('.penyedia-api', url, 'Masukan Penyedia');
	    });

	    $(document).on('change', '.lokasi-ubah', function() {
	    	var lokasi = $(this).val();
	    	var url = '<?= site_url('food_n_go/master_data/jenis_katalog/daftar_penyedia/'); ?>'+lokasi;

	    	generateOption('#penyedia-ubah', url, 'Masukan Penyedia');
	    	$('#penyedia-ubah').val(null).trigger('change');
	    });
	});
</script>

