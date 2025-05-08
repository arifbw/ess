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
															<label>Nama Gedung</label>
														</div>
														<div class="col-lg-7">
															<select class="gedung-api" name="id_gedung">
																
															</select>
														</div>
													</div>
												</div>
                                                
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Nama Ruangan</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control" name="nama" value="<?php echo $nama;?>" placeholder="Nama Ruangan">
														</div>
														<div id="warning_nama" class="col-lg-3 text-danger"></div>
													</div>
												</div>
                                                
                                                <!-- START: heru menambahkan ini 2020-11-25 @08:46 -->
												<div class="row">
													<div class="form-group">
														<div class="col-lg-2">
															<label>Kapasitas</label>
														</div>
														<div class="col-lg-7">
															<input type="number" class="form-control" name="kapasitas" value="<?php echo $kapasitas;?>" placeholder="Kapasitas Ruangan">
														</div>
														<div id="warning_kapasitas" class="col-lg-3 text-danger"></div>
													</div>
												</div>
												<!-- END: heru menambahkan ini 2020-11-25 @08:46 -->
                                                
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
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_daftar_ruangan">
								<thead>
									<tr>
										<th class='text-center'>#</th>
										<th class='text-center'>Nama Ruangan</th>
										<th class='text-center'>Nama Gedung</th>
										<th class='text-center'>Kapasitas</th> <!-- heru menambahkan ini 2020-11-25 @08:46 -->
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
										for($i=0;$i<count($daftar_ruangan);$i++){
											if($i%2==0){
												$class = "even";
											}
											else{
												$class = "odd";
											}
											
											echo "<tr class='$class'>";
												echo "<td align='center'>".($i+1)."</td>";
												echo "<td>".$daftar_ruangan[$i]["nama"]."</td>";
												echo "<td>".$daftar_ruangan[$i]["gedung"]."</td>";
												echo "<td style='text-align: right;'>".$daftar_ruangan[$i]["kapasitas"]."</td>"; # heru menambahkan ini 2020-11-25 @08:46
												echo "<td class='text-center'>";
													if((int)$daftar_ruangan[$i]["status"]==1){
														echo "Aktif";
													}
													else if((int)$daftar_ruangan[$i]["status"]==0){
														echo "Non Aktif";
													}
												echo "</td>";
												if(@$akses["ubah"] or @$akses["lihat log"]){
													echo "<td class='text-center'>";
														if(@$akses["ubah"]){
															echo "<button type='button' class='btn btn-primary btn-xs ubah' data-toggle='modal' data-target='#modal_ubah' data-id='".$daftar_ruangan[$i]["id"]."' data-nama='".$daftar_ruangan[$i]["nama"]."' data-kapasitas='".$daftar_ruangan[$i]["kapasitas"]."' data-status='".$daftar_ruangan[$i]["status"]."'>Ubah</button> ";
														}
														if(@$akses["lihat log"]){
															echo "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_ruangan[$i]["nama"]."\",".$daftar_ruangan[$i]["id"].")'>Lihat Log</button>";
														}
													echo "</td>";
												}
											echo "</tr>";
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
						<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="<?= base_url('food_n_go/master_data/daftar_ruangan')?>" id="formulir_ubah" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="aksi" value="ubah"/>
                                            <input type="hidden" name="id_ubah" id="id_ubah">
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
														<label>Nama Gedung</label>
													</div>
													<div class="col-lg-7">
														<input id="gedung-old" type="hidden" name="id_gedung_old">
														<select class="gedung-ubah" name="id_gedung">
															
														</select>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Nama Ruangan</label>
													</div>
													<div class="col-lg-7">
														<input id="nama_ubah" class="form-control" name="nama_ubah" placeholder="Nama Ruangan">
														<input type="hidden" id="nama_old" class="form-control" name="nama_old" placeholder="Nama Ruangan">
													</div>
													<div id="warning_nama_ruang" class="col-lg-3 text-danger"></div>
												</div>
											</div>
                                            
                                            <!-- START: heru menambahkan ini 2020-11-25 @08:46 -->
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Kapasitas</label>
													</div>
													<div class="col-lg-7">
														<input type="number" id="kapasitas_ubah" class="form-control" name="kapasitas_ubah" placeholder="Kapasitas Ruangan">
														<input type="hidden" id="kapasitas_old" class="form-control" name="kapasitas_old" placeholder="Kapasitas Ruangan">
													</div>
													<div id="warning_kapasitas_ruang" class="col-lg-3 text-danger"></div>
												</div>
											</div>
                                            <!-- END: heru menambahkan ini 2020-11-25 @08:46 -->
                                            
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Status</label>
													</div>
													<div class="col-lg-5">
                                                        <input type="hidden" name="status_old" id="status_old">
														<div class="radio">
															<label>
																<input type="radio" name="status_ubah" id="1" value="aktif">Aktif
															</label>
															<label>
																<input type="radio" name="status_ubah" id="0" value="non aktif">Non Aktif
															</label>
														</div>
													</div>
													<div id="warning_status_ubah" class="col-lg-4 text-danger"></div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary" onclick="/*return cek_simpan_ubah()*/">Simpan</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</form>
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
		$('.gedung-api').select2({
			width: "100%",
			placeholder: "Silahkan Pilih Lokasi"
		});

		generateOption('.lokasi-api', '<?= site_url('food_n_go/master_data/daftar_ruangan/daftar_lokasi/'); ?>', 'Masukan Lokasi');

		$(document).on('change', '.lokasi-api', function() {
	    	var lokasi = $(this).val();
	    	var url = '<?= site_url('food_n_go/master_data/daftar_ruangan/daftar_gedung/'); ?>'+lokasi;

	    	generateOption('.gedung-api', url, 'Masukan Gedung');
	    });

	    $(document).on('change', '.lokasi-ubah', function() {
	    	var lokasi = $(this).val();
	    	var url = '<?= site_url('food_n_go/master_data/daftar_ruangan/daftar_gedung/'); ?>'+lokasi;

	    	generateOption('.gedung-ubah', url, 'Masukan Gedung');

	    	$('.gedung-ubah').val(null).trigger('change');
	    });

	    $(document).on('click', '.ubah', function() {
			var id = $(this).data('id');
			var lokasiOption = $('.lokasi-ubah');
			var gedungOption = $('.gedung-ubah');
			
			$.ajax({
				url: '<?= site_url('food_n_go/master_data/daftar_ruangan/detail/'); ?>'+id,
				success: function(data) {
					var val = data.result;

					var lokasi = generateOption('.lokasi-ubah', '<?= site_url('food_n_go/master_data/daftar_ruangan/daftar_lokasi/'); ?>', 'Masukan Lokasi');

					var gedung = generateOption('.gedung-ubah', '<?= site_url('food_n_go/master_data/daftar_ruangan/daftar_gedung/'); ?>'+val.lokasi, 'Masukan Gedung');

					$('#id_ubah').val(val.id);
				    $('#nama_old').val(val.nama);
				    $('#nama_ubah').val(val.nama);
				    $('#kapasitas_ubah').val(val.kapasitas); // heru menambahkan ini, 2020-11-25 @09:00
				    $('#gedung-old').val(val.gedung);
				    
				    $('#status_old').val(val.status);

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

			    var optionGedung = new Option(value.nama_gedung, value.gedung, true, true);
			    gedungOption.append(optionGedung).trigger('change');

			    var data = {id: value.gedung, text: value.nama_gedung};
			    // manually trigger the `select2:select` event
			    gedungOption.trigger({
			        type: 'select2:select',
			        params: {
			            data: data
			        }
			    });
			});
		});
	});
</script>