		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
		
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
				<?php if(@$akses["tambah"]) { ?>
				<div class="row">
					<div class="col-lg-2">
						<a data-toggle="modal" data-target="#list_snack" class="btn btn-primary">Lihat Snack</a>
						<!-- Modal -->
						<div id="list_snack" class="modal fade" role="dialog">
						  	<div class="modal-dialog modal-lg">
							    <!-- Modal content-->
							    <div class="modal-content">
							      	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							        	<h4 class="modal-title">Daftar Snack</h4>
							      	</div>
							      	<div class="modal-body">
							        	<table width="100%" class="table table-striped table-bordered table-hover tb-katalog">
											<thead>
												<tr>
													<th class='text-center'>No</th>
													<th class='text-center'>Lokasi</th>
													<th class='text-center'>Nama Penyedia</th>
													<th class='text-center'>Nama Menu</th>
													<th class='text-center'>Harga</th>
												</tr>
											</thead>
											<tbody>
												<?php $start = 1; foreach ($daftar_snack as $val) { ?>
												<tr>
													<td><?= $start++; ?></td>
													<td><?= $val->lokasi; ?></td>
													<td><?= $val->nama_penyedia; ?></td>
													<td><?= $val->nama; ?></td>
													<td><?= $val->harga; ?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
							      	</div>
							      	<div class="modal-footer">
							        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      	</div>
							    </div>
						  	</div>
						</div>
					</div>
					<div class="col-lg-2">
						<a data-toggle="modal" data-target="#list_makanan" class="btn btn-primary">Lihat Makanan</a>
						<!-- Modal -->
						<div id="list_makanan" class="modal fade" role="dialog">
						  	<div class="modal-dialog modal-lg">
							    <!-- Modal content-->
							    <div class="modal-content">
							      	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							        	<h4 class="modal-title">Daftar Makanan</h4>
							      	</div>
							      	<div class="modal-body">
							        	<table width="100%" class="table table-striped table-bordered table-hover tb-katalog">
											<thead>
												<tr>
													<th class='text-center'>No</th>
													<th class='text-center'>Lokasi</th>
													<th class='text-center'>Nama Penyedia</th>
													<th class='text-center'>Nama Menu</th>
													<th class='text-center'>Harga</th>
												</tr>
											</thead>
											<tbody>
												<?php $start = 1; foreach ($daftar_makanan as $val) { ?>
												<tr>
													<td><?= $start++; ?></td>
													<td><?= $val->lokasi; ?></td>
													<td><?= $val->nama_penyedia; ?></td>
													<td><?= $val->nama; ?></td>
													<td><?= $val->harga; ?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
							      	</div>
							      	<div class="modal-footer">
							        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      	</div>
							    </div>
						  	</div>
						</div>
					</div>
					<div class="col-lg-2">
						<a data-toggle="modal" data-target="#list_minuman" class="btn btn-primary">Lihat Minuman</a>
						<!-- Modal -->
						<div id="list_minuman" class="modal fade" role="dialog">
						  	<div class="modal-dialog modal-lg">
							    <!-- Modal content-->
							    <div class="modal-content">
							      	<div class="modal-header">
							        	<button type="button" class="close" data-dismiss="modal">&times;</button>
							        	<h4 class="modal-title">Daftar Minuman</h4>
							      	</div>
							      	<div class="modal-body">
							        	<table width="100%" class="table table-striped table-bordered table-hover tb-katalog">
											<thead>
												<tr>
													<th class='text-center'>No</th>
													<th class='text-center'>Lokasi</th>
													<th class='text-center'>Nama Penyedia</th>
													<th class='text-center'>Nama Menu</th>
													<th class='text-center'>Harga</th>
												</tr>
											</thead>
											<tbody>
												<?php $start = 1; foreach ($daftar_minuman as $val) { ?>
												<tr>
													<td><?= $start++; ?></td>
													<td><?= $val->lokasi; ?></td>
													<td><?= $val->nama_penyedia; ?></td>
													<td><?= $val->nama; ?></td>
													<td><?= $val->harga; ?></td>
												</tr>
												<?php } ?>
											</tbody>
										</table>
							      	</div>
							      	<div class="modal-footer">
							        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							      	</div>
							    </div>
						  	</div>
						</div>
					</div>
				</div>

				<br>
				<div class="row">
					<div class="col-lg-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									Tambah <?php echo $judul;?>
								</h4>
							</div>
							<div class="panel-body">
								<form role="form" action="<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/save'); ?>" id="formulir_tambah" method="post">
									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Nama Pemesan</label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control" value="<?= $_SESSION["no_pokok"].' - '.$_SESSION["nama"] ?>" readonly>
											</div>	
										</div>
									</div>

									<?php if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja ?>
									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Unit Kerja</label>
											</div>
											<div class="col-lg-6">
												<select class="form-control select2" id='insert_unit_kerja' name='insert_unit_kerja' onChange="get_approval();" style="width: 100%;" required>
													<option value=''>Pilih Unit Kerja</option>
													<?php foreach ($array_daftar_unit as $value) { ?>
														<option value='<?php echo $value['kode_unit']?>'><?php echo $value['kode_unit']." - ".$value['nama_unit']?></option>
													<?php } ?>
												</select>
											</div>	
											<div id="warning_unit_kerja" class="col-lg-3 text-danger"></div>
										</div>
									</div>
									<?php } ?>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Nama Acara</label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="insert_nama_acara" required>
											</div>
											<div id="warning_nama_acara" class="col-lg-3 text-danger"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Tanggal Pelaksanaan</label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="insert_tanggal_pemesanan" id="insert_tanggal_pemesanan" required>
											</div>
											<div id="warning_tanggal_pemesanan" class="col-lg-3 text-danger"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Waktu Pelaksanaan</label>
											</div>
											<div class="col-lg-6">
												<div class="row">
													<div class="col-lg-6">
														<input type="time" class="form-control" name="insert_waktu_mulai" id="insert_waktu_mulai" onchange="check_availability();" required>
													</div>
													<div class="col-lg-6">
														<input type="time" class="form-control" name="insert_waktu_selesai" id="insert_waktu_selesai">
													</div>
												</div>
											</div>
											<div id="warning_waktu_pemesanan" class="col-lg-3 text-danger"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Lokasi Acara</label>
											</div>
											<div class="col-lg-6">
												<select class="form-control select2" id='insert_lokasi_acara' name='insert_lokasi_acara' style="width: 100%;" onChange="set_lokasi();" required>
													<option value=''></option>	
													<?php foreach ($array_daftar_lokasi->result_array() as $value) { ?>
														<option value='<?php echo $value['id']?>'><?php echo $value['nama']?></option>
													<?php } ?>
												</select>
											</div>
											<div id="warning_lokasi_acara" class="col-lg-3 text-danger"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Pilih Gedung</label>
											</div>
											<div class="col-lg-6">
												<select class="form-control select2" id='insert_gedung' style="width: 100%;" onChange="get_ruangan();" required></select>
											</div>
											<div id="warning_gedung" class="col-lg-3 text-danger"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Pilih Ruang Rapat</label>
											</div>
											<div class="col-lg-6">
												<!--<select class="form-control select2" name='insert_id_ruangan' id='insert_id_ruangan' style="width: 100%;" required></select>-->
												<select class="form-control select2" name='insert_id_ruangan' id='insert_id_ruangan' style="width: 100%;" onchange="get_ruang_info(); check_availability();" required></select> <!-- heru mengganti jadi line ini 2020-11-25 @09:46 -->
											</div>
											<div id="warning_id_ruangan" class="col-lg-3 text-danger"></div>
										</div>
									</div>
                                    
                                    <!-- START: heru menambahkan ini 2020-11-25 @09:37 -->
									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Kapasitas Normal</label>
											</div>
											<div class="col-lg-6">
												<input class="form-control" id='insert_kapasitas' style="width: 100%;" readonly>
											</div>
											<div id="warning_kapasitas" class="col-lg-3 text-danger"></div>
										</div>
										<div class="row">
											<div class="col-lg-3"></div>
											<div class="col-lg-6">
												<div class="text-danger">* Kapasitas maksimal menjadi 50% selama masa pandemi Covid-19</div>
											</div>
										</div>
									</div>
                                    <!-- END: heru menambahkan ini 2020-11-25 @09:37 -->
                                    
                                    <!-- Dipindah disini by heru 2020-11-25 @09:37 -->
									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Jumlah Peserta</label>
											</div>
											<div class="col-lg-6">
												<input type="number" class="form-control" name="insert_jumlah_peserta" required>
											</div>
											<div id="warning_jumlah_peserta" class="col-lg-3 text-danger"></div>
										</div>
									</div>
                                    <!-- Dipindah disini -->

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Pilih Snack</label>
											</div>
											<div class="col-lg-6">
												<select class="form-control select2" multiple name='insert_snack[]' id='insert_snack' style="width: 100%;"></select>
											</div>
											<div id="warning_snack" class="col-lg-3 text-danger"></div>
										</div>
										<div class="row">
											<div class="col-lg-3"></div>
											<div class="col-lg-6">
												<div class="text-danger">* Harga yang tertera adalah harga estimasi</div>
											</div>
										</div>
									</div>

									<input type="hidden" id="maxMakananAdd" />
									<div class="form-group" id="bodyMakanan">
										<div class="row">
											<div class="col-lg-3">
												<label>Pilih Makanan</label>
											</div>
											<div class="col-lg-4">
												<div id="warning_makanan" class="text-danger"></div>
												<select class="form-control select2 pesan_makanan" name='insert_makanan[]' style="width: 100%;" required></select>
											</div>

											<div class="col-lg-2">
												<input type="number" class="form-control jumlah_pesan_makanan" name="insert_jumlah_makanan[]" placeholder="Jumlah" required>
											</div>

											<div class="col-lg-1">
												<button class="btn btn-primary" type="button" id="addNewRowMakanan"><i class="fa fa-plus"></i></button>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-3"></div>
											<div class="col-lg-6">
												<div class="text-danger">* Harga yang tertera adalah harga estimasi</div>
											</div>
										</div>
									</div>

									<input type="hidden" id="maxMinumanAdd" />
									<div class="form-group" id="bodyMinuman">
										<div class="row">
											<div class="col-lg-3">
												<label>Pilih Minuman</label>
											</div>
											<div class="col-lg-4">
												<div id="warning_minuman" class="text-danger"></div>
												<select class="form-control select2 pesan_minuman" name='insert_minuman[]' style="width: 100%;" required></select>
											</div>

											<div class="col-lg-2">
												<input type="number" class="form-control jumlah_pesan_minuman" name="insert_jumlah_minuman[]" placeholder="Jumlah" required>
											</div>

											<div class="col-lg-1">
												<button class="btn btn-primary" type="button" id="addNewRowMinuman"><i class="fa fa-plus"></i></button>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-3"></div>
											<div class="col-lg-6">
												<div class="text-danger">* Harga yang tertera adalah harga estimasi</div>
											</div>
										</div>
									</div>
										
									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Kode Akun STO</label>
											</div>
											<div class="col-lg-6">
												<!-- heru PDS ubah input STO jadi select, 2021-01-27
                                                <input type="text" class="form-control" name="insert_kode_akun_sto" required>
                                                -->
                                                <select class="form-control select2" name="insert_kode_akun_sto" id="insert_kode_akun_sto"></select>
											</div>
											<div id="warning_kode_akun_sto" class="col-lg-3 text-danger"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Kode Anggaran</label>
											</div>
											<div class="col-lg-6">
												<!-- heru PDS ubah input kode anggaran jadi select, 2021-01-27
												<input type="text" class="form-control" name="insert_kode_anggaran" required>
                                                -->
                                                <select class="form-control select2" name="insert_kode_anggaran" required>
                                                    <?php foreach($kode_anggaran as $r){ ?>
                                                    <option value="<?= $r->nama?>"><?= $r->nama?></option>
                                                    <?php } ?>
                                                </select>
											</div>
											<div id="warning_kode_anggaran" class="col-lg-3 text-danger"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Pilih Approver</label>
											</div>
											<div class="col-lg-6">
												<select class="form-control select2" id='insert_np_atasan' name='insert_np_atasan' style="width: 100%;" required></select>
											</div>	
											<div id="warning_np_atasan" class="col-lg-3 text-danger"></div>
										</div>
									</div>

									<div class="form-group">
										<div class="row">
											<div class="col-lg-3">
												<label>Keterangan</label>
											</div>
											<div class="col-lg-6">
												<textarea class="form-control" name="insert_keterangan"></textarea>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-9 text-right">
											<button type="submit" class="btn btn-primary" id="btn-simpan" onclick="return cek_simpan_tambah()">Simpan</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->
				<?php } ?>
                
                
                <div class="modal fade" id="modal-availibility" tabindex="-1" role="dialog" aria-labelledby="modal-availibility" data-backdrop="static" data-keyboard="false" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" id="modal-availibility-detail"></div>
                    </div>
                </div>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		
		<script type="text/javascript">	
			$('#multi_select').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
			$(document).ready(function() {
                
                $(function () {
                    $('#insert_tanggal_pemesanan').datetimepicker({
                        format: 'DD-MM-Y',
						minDate : '<?php echo date('Y-m-d') ?>',
                    }).on('dp.change', function (event) {
                        check_availability();
                    });
                });

                <?php if($_SESSION['grup']==5) { ?>
                var kode_unit_pemesan = '<?= substr($_SESSION['kode_unit'],0,3) ?>';
                $("#insert_np_atasan").empty();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/get_apv');?>",
		            type: "POST",
		            dataType: "json",
		            data: {kode_unit: kode_unit_pemesan},
		            success: function(response) {
                        for (var i = 0; i < response.length; i++) {
                            $("#insert_np_atasan").append($("<option></option>").attr("value", response[i]["no_pokok"]).text(response[i]["no_pokok"] + " - " + response[i]["nama"]));
                            if(response[i]["kode_unit"]==kode_unit_pemesan){
                                $("#insert_np_atasan").val(response[i]["no_pokok"]);
                                $("#insert_np_atasan").trigger('change');
                            }
                        }
                        $('.select2').select2();
		            }
		        });
                <?php } ?>
                
                $('.select2').select2();
			});

			function cek_approval(approval) {
				if (approval==='0') {
					$('#keterangan_approval').show();
				} else {
					$('#keterangan_approval').hide();
				}
			}

			function get_approval() {
				var kode_unit_pemesan = $('#insert_unit_kerja').val();
                $("#insert_np_atasan").empty();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/get_apv');?>",
		            type: "POST",
		            dataType: "json",
		            data: {kode_unit: kode_unit_pemesan},
		            success: function(response) {
                        for (var i = 0; i < response.length; i++) {
                            $("#insert_np_atasan").append($("<option></option>").attr("value", response[i]["no_pokok"]).text(response[i]["no_pokok"] + " - " + response[i]["nama"]));
                            if(response[i]["kode_unit"]==kode_unit_pemesan){
                                $("#insert_np_atasan").val(response[i]["no_pokok"]);
                                $("#insert_np_atasan").trigger('change');
                            }
                        }
                        $('.select2').select2();
		            }
		        });
			}

            function set_lokasi(){
                var lokasi = $('#insert_lokasi_acara').children("option:selected").text();
                
                $("#insert_id_ruangan").empty();
                $("#insert_gedung").empty();
                $("#insert_snack").empty();
                $("#insert_kapasitas").val(''); // heru menambahkan ini 2020-11-25 @09:46
                $(".pesan_makanan").empty();
                $(".pesan_minuman").empty();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/set_lokasi');?>",
		            type: "POST",
		            dataType: "json",
		            data: {lokasi: lokasi},
		            success: function(response) {
                        for (var i = 0; i < response.gedung.length; i++) {
                            $("#insert_gedung").append($("<option></option>").attr("value", response.gedung[i]["id"]).text(response.gedung[i]["nama"]));
                            if (i==0) {
                            	$("#insert_gedung").val(response.gedung[i]["id"]);
                            	$("#insert_gedung").trigger('change');
                            }
                        }
                        for (var i = 0; i < response.snack.length; i++) {
                            $("#insert_snack").append($("<option></option>").attr("value", response.snack[i]["id"]).text(response.snack[i]["nama_penyedia"] + " - " + response.snack[i]["nama"] + " - Rp." + response.snack[i]["harga"]));
                        }
                        for (var i = 0; i < response.makanan.length; i++) {
                            $(".pesan_makanan").append($("<option></option>").attr("value", response.makanan[i]["id"]).text(response.makanan[i]["nama_penyedia"] + " - " + response.makanan[i]["nama"] + " - Rp." + response.makanan[i]["harga"]));
                        }
                        for (var i = 0; i < response.minuman.length; i++) {
                            $(".pesan_minuman").append($("<option></option>").attr("value", response.minuman[i]["id"]).text(response.minuman[i]["nama_penyedia"] + " - " + response.minuman[i]["nama"] + " - Rp." + response.minuman[i]["harga"]));
                        }
                        $('.select2').select2();
		            }
		        });
            }

            function get_ruangan(){
                var gedung = $('#insert_gedung').children("option:selected").text();
                
                $("#insert_id_ruangan").empty();
                $("#insert_kapasitas").val(''); // heru menambahkan ini 2020-11-25 @09:46
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/get_ruangan');?>",
		            type: "POST",
		            dataType: "json",
		            data: {gedung: gedung},
		            success: function(response) {
                        for (var i = 0; i < response.length; i++) {
                            $("#insert_id_ruangan").append($("<option></option>").attr("value", response[i]["id"]).text(response[i]["nama"]));
                        }
                        $('.select2').select2();
		            }
		        });
            }
            
            // START: heru menambahkan ini 2020-11-25 @09:46
            function get_ruang_info(){
                let ruang = $('#insert_id_ruangan').val();
                $("#insert_kapasitas").val('');
                
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/get_ruang_info');?>",
		            type: "POST",
		            dataType: "json",
		            data: {id_ruang: ruang},
		            success: function(response) {
                        if(response !== null)
                            $("#insert_kapasitas").val(response.kapasitas);
		            }
		        });
            }
            // END: heru menambahkan ini 2020-11-25 @09:46

            jQuery(document).ready(function() {
				$('#maxMakananAdd').val(0);
				$('#maxMinumanAdd').val(0);
				$('#addNewRowMakanan').click(addNewRowMakanan);
				$('#addNewRowMinuman').click(addNewRowMinuman);
			});
			
			function addNewRowMakanan(){
				var lastIndexTable = Number($('#maxMakananAdd').val());

				lastIndexTable = lastIndexTable + 1;
				var newRow ='<div class="row" id="makanan'+ lastIndexTable +'">'+
								'<div class="col-lg-3"></div>'+
								'<div class="col-lg-4">'+
									'<select class="form-control select2 pesan_makanan" name="insert_makanan[]" style="width: 100%;" required></select>'+
								'</div>'+
								'<div class="col-lg-2">'+
									'<input type="number" class="form-control jumlah_pesan_makanan" name="insert_jumlah_makanan[]" placeholder="Jumlah" required>'+
								'</div>'+
								'<div class="col-lg-1">'+
									'<button class="btn btn-danger" type="button" onclick="deleteRow(\'makanan'+ lastIndexTable +'\')"><i class="fa fa-trash-o"></i></button>'+
								'</div>'+
							'</div>';
				$('#bodyMakanan').append(newRow);
				$('.select2').select2();
				set_lokasi();
				$('#maxMakananAdd').val(lastIndexTable);
			}

			function addNewRowMinuman(){
				var lastIndexTable = Number($('#maxMinumanAdd').val());

				lastIndexTable = lastIndexTable + 1;
				var newRow ='<div class="row" id="minuman'+ lastIndexTable +'">'+
								'<div class="col-lg-3"></div>'+
								'<div class="col-lg-4">'+
									'<select class="form-control select2 pesan_minuman" name="insert_minuman[]" style="width: 100%;" required></select>'+
								'</div>'+
								'<div class="col-lg-2">'+
									'<input type="number" class="form-control jumlah_pesan_minuman" name="insert_jumlah_minuman[]" placeholder="Jumlah" required>'+
								'</div>'+
								'<div class="col-lg-1">'+
									'<button class="btn btn-danger" type="button" onclick="deleteRow(\'minuman'+ lastIndexTable +'\')"><i class="fa fa-trash-o"></i></button>'+
								'</div>'+
							'</div>';
				$('#bodyMinuman').append(newRow);
				$('.select2').select2();
				set_lokasi();
				$('#maxMinumanAdd').val(lastIndexTable);
			}

			function deleteRow(tag){
				$('#'+tag).remove();
			}

			$(document).ready(function() {
				$('.tb-katalog').DataTable({
					responsive: true
				});
			});
            
            // heru menambahkan ini 2020-12-03 @15:35
            function check_availability(){
                let insert_tanggal_pemesanan = $('#insert_tanggal_pemesanan').val();
                let insert_waktu_mulai = $('#insert_waktu_mulai').val();
                let insert_waktu_selesai = $('#insert_waktu_selesai').val();
                let insert_ruang = $('#insert_id_ruangan').val();
                
                if(insert_tanggal_pemesanan!='' && insert_waktu_mulai!='' && insert_ruang!=''){
                    $('#modal-availibility-detail').html('<h4>Memeriksa ketersediaan ruang...</h4>');
                    $('#modal-availibility').modal('show');
                    $('#btn-simpan').prop('disabled',true);
                    
                    $.ajax({
                        url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/check_availability');?>",
                        type: "POST",
                        dataType: "json",
                        data: {id_ruang: insert_ruang, tanggal: insert_tanggal_pemesanan, waktu_mulai: insert_waktu_mulai, waktu_selesai: insert_waktu_selesai}
                    }).then(function(response){
                        $('#modal-availibility-detail').html(response.message);
                        if(response.status==true)
                            $('#btn-simpan').prop('disabled',false);
                        else
                            $('#btn-simpan').prop('disabled',true);
                    }).catch(function(){
                        $('#modal-availibility-detail').html(`<div class="modal-body">
                                                                <h4>Something Error</h4>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
                                                            </div>`);
                        $('#btn-simpan').prop('disabled',false);
                    });
                }
            }
            // END: heru menambahkan ini 2020-12-03 @15:35
            
            // heru PDS menambahkan ini 2021-01-27 @10:08, munculin STO sesuai tanggal pemesanan yg dipilih
            const getStoByDate=()=>{
                $('#insert_kode_akun_sto').html('');
                let getDate = $('#insert_tanggal_pemesanan').val();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/getStoByDate');?>",
		            type: "POST",
		            dataType: "json",
		            data: {date: getDate}
		        }).then(function(response){
                    let allData = response.data;
                    allData.forEach(function(item) {
                        let o = new Option(item.nama_unit, item.kode_unit);
                        $(o).html(item.kode_unit+' - '+item.nama_unit);
                        $("#insert_kode_akun_sto").append(o);
                    });
                }).catch(function(){
                    console.log('Masuk catch')
                });
            }
            // END: heru PDS menambahkan ini 2021-01-27 @10:08
		</script>