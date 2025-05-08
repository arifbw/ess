						<form enctype="multipart/form-data" method="POST" accept-charset="utf-8" action="<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/save/'.$detail['id']);?>" id="formulir_tambah" >
								 
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_ubah">Ubah Pemesanan Konsumsi Rapat</h4>
							</div>
							<div class="modal-body">
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
								
								<?php if ($_SESSION['grup']=='5' || $_SESSION['grup']=='4') { ?>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Nama Acara</label>
										</div>
										<div class="col-lg-6">
											<input type="text" class="form-control" name="insert_nama_acara" value="<?= $detail['nama_acara'] ?>" required>
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
											<input type="text" class="form-control" name="insert_tanggal_pemesanan" id="insert_tanggal_pemesanan" value="<?= date('d-m-Y', strtotime($detail['tanggal_pemesanan'])) ?>" required>
										</div>
										<div id="warning_tanggal_pemesanan" class="col-lg-3 text-danger"></div>
									</div>
								</div>

								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Waktu Pemesanan</label>
										</div>
										<div class="col-lg-6">
											<div class="row">
												<div class="col-lg-6">
													<input type="time" class="form-control" name="insert_waktu_mulai" value="<?= date('H:i', strtotime($detail['waktu_mulai'])) ?>" required>
												</div>
												<div class="col-lg-6">
													<input type="time" class="form-control" name="insert_waktu_selesai" value="<?= date('H:i', strtotime($detail['waktu_selesai'])) ?>">
												</div>
											</div>
										</div>
										<div id="warning_waktu_pemesanan" class="col-lg-3 text-danger"></div>
									</div>
								</div>

								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Jumlah Peserta</label>
										</div>
										<div class="col-lg-6">
											<input type="number" class="form-control" name="insert_jumlah_peserta" value="<?= $detail['jumlah_peserta'] ?>" required>
										</div>
										<div id="warning_jumlah_peserta" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								<?php } ?>

								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Lokasi Acara</label>
										</div>
										<div class="col-lg-6">
											<select class="form-control select2" id='insert_lokasi_acara' name='insert_lokasi_acara' style="width: 100%;" onChange="set_lokasi();" required>
												<option value=''></option>	
												<?php foreach ($array_daftar_lokasi->result_array() as $value) { ?>
													<option value='<?php echo $value['id']?>' <?= ($detail['lokasi_acara']==$value['id']) ? 'selected' : '' ?>><?php echo $value['nama']?></option>
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
											<select class="form-control select2" id='insert_gedung' style="width: 100%;" onChange="get_ruangan();" value="" required>
											<?php foreach ($array_daftar_gedung->result_array() as $value) { ?>
												<option value='<?php echo $value['id']?>' <?= ($detail['id_gedung']==$value['id']) ? 'selected' : '' ?>><?php echo $value['nama']?></option>
											<?php } ?>
											</select>
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
											<select class="form-control select2" name='insert_id_ruangan' id='insert_id_ruangan' style="width: 100%;" required>
											<?php foreach ($array_daftar_ruangan->result_array() as $value) { ?>
												<option value='<?php echo $value['id']?>' <?= ($detail['id_ruangan']==$value['id']) ? 'selected' : '' ?>><?php echo $value['nama']?></option>
											<?php } ?>
											</select>
										</div>
										<div id="warning_id_ruangan" class="col-lg-3 text-danger"></div>
									</div>
								</div>

								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Pilih Snack</label>
										</div>
										<div class="col-lg-6">
											<select class="form-control select2" multiple name='insert_snack[]' id='insert_snack' style="width: 100%;" required>
											<?php foreach ($array_daftar_snack->result_array() as $value) { ?>
												<option value='<?php echo $value['id']?>' <?= (in_array($value['id'], explode(',', $detail['snack']))) ? 'selected' : '' ?>><?php echo $value['nama']?></option>
											<?php } ?>
											</select>
										</div>
										<div id="warning_snack" class="col-lg-3 text-danger"></div>
									</div>
								</div>

								<input type="hidden" id="maxMakananAdd" />
								<div class="form-group" id="bodyMakanan">

								</div>

								<input type="hidden" id="maxMinumanAdd" />
								<div class="form-group" id="bodyMinuman">

								</div>

								<div class="form-group">
									<div class="row">
										<div class="col-lg-3">
											<label>Kode Akun STO</label>
										</div>
										<div class="col-lg-6">
											<input type="text" class="form-control" name="insert_kode_akun_sto" value="<?= $detail['kode_akun_sto'] ?>" required>
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
											<input type="text" class="form-control" name="insert_kode_anggaran" value="<?= $detail['kode_anggaran'] ?>" required>
										</div>
										<div id="warning_kode_anggaran" class="col-lg-3 text-danger"></div>
									</div>
								</div>

								<?php if ($_SESSION['grup']=='5' || $_SESSION['grup']=='4') { ?>
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
											<textarea class="form-control" name="insert_keterangan" value="<?= $detail['keterangan'] ?>"></textarea>
										</div>
									</div>
								</div>
								<?php } ?>

							</div>
							<div class="modal-footer">
								<div class="row">
									<div class="col-md-1 pull-left">
										<button type="button" class="btn btn-default" data-dismiss="modal">Kembali</button>
									</div>
									<div class="col-md-10 pull-right">
										<div class="approval">
											<button type="submit" class="btn btn-primary" onclick="return cek_simpan_tambah()">Simpan</button>
										</div>
									</div>
								</div>
							</div>
						</form>

		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		
		<script type="text/javascript">	
			$('#multi_select').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
			$(document).ready(function() {

				makanan = JSON.parse('<?= $detail['makanan'] ?>');
				minuman = JSON.parse('<?= $detail['minuman'] ?>');
			    $('#maxMakananAdd').val(makanan.length);
			    $('#maxMinumanAdd').val(minuman.length);

				for(i=0; i<makanan.length; i++) {
					if (i==0) {
						$('#bodyMakanan').append('<div class="row">'+
							'<div class="col-lg-3">'+
								'<label>Pilih Makanan</label>'+
							'</div>'+
							'<div class="col-lg-4">'+
								'<div id="warning_makanan" class="text-danger"></div>'+
								'<select class="form-control select2 pesan_makanan" name="insert_makanan[]" id="insert_makanan'+i+'" style="width: 100%;" required></select>'+
							'</div>'+
							'<div class="col-lg-2">'+
								'<input type="number" class="form-control jumlah_pesan_makanan" name="insert_jumlah_makanan[]" placeholder="Jumlah" id="insert_jumlah_makanan'+i+'" required>'+
							'</div>'+
							'<div class="col-lg-1">'+
								'<button class="btn btn-primary" type="button" onclick="addNewRowMakanan()"><i class="fa fa-plus"></i></button>'+
							'</div>'+
						'</div>');
					} else {
						var newRow ='<div class="row" id="makanan'+i+'">'+
							'<div class="col-lg-3"></div>'+
							'<div class="col-lg-4">'+
								'<div id="warning_minuman" class="text-danger"></div>'+
								'<select class="form-control select2 pesan_makanan" name="insert_makanan[]" id="insert_makanan'+i+'" style="width: 100%;" required></select>'+
							'</div>'+
							'<div class="col-lg-2">'+
								'<input type="number" class="form-control jumlah_pesan_makanan" name="insert_jumlah_makanan[]" placeholder="Jumlah" id="insert_jumlah_makanan'+i+'" required>'+
							'</div>'+
							'<div class="col-lg-1">'+
								'<button class="btn btn-danger" type="button" onclick="deleteRow(\'makanan'+ i +'\')"><i class="fa fa-trash-o"></i></button>'+
							'</div>'+
						'</div>';
						$('#bodyMakanan').append(newRow);
					}
				}
				
				for(i=0; i<minuman.length; i++) {
					if (i==0) {
						$('#bodyMinuman').append('<div class="row">'+
							'<div class="col-lg-3">'+
								'<label>Pilih Minuman</label>'+
							'</div>'+
							'<div class="col-lg-4">'+
								'<select class="form-control select2 pesan_minuman" name="insert_minuman[]" id="insert_minuman'+i+'" style="width: 100%;" required></select>'+
							'</div>'+
							'<div class="col-lg-2">'+
								'<input type="number" class="form-control jumlah_pesan_minuman" name="insert_jumlah_minuman[]" placeholder="Jumlah" id="insert_jumlah_minuman'+i+'" required>'+
							'</div>'+
							'<div class="col-lg-1">'+
								'<button class="btn btn-primary" type="button" onclick="addNewRowMakanan()"><i class="fa fa-plus"></i></button>'+
							'</div>'+
						'</div>');
					} else {
						var newRow ='<div class="row" id="minuman'+i+'">'+
							'<div class="col-lg-3"></div>'+
							'<div class="col-lg-4">'+
								'<select class="form-control select2 pesan_minuman" name="insert_minuman[]" id="insert_minuman'+i+'" style="width: 100%;" required></select>'+
							'</div>'+
							'<div class="col-lg-2">'+
								'<input type="number" class="form-control jumlah_pesan_minuman" name="insert_jumlah_minuman[]" placeholder="Jumlah" id="insert_jumlah_minuman'+i+'" required>'+
							'</div>'+
							'<div class="col-lg-1">'+
								'<button class="btn btn-danger" type="button" onclick="deleteRow(\'minuman'+ i +'\')"><i class="fa fa-trash-o"></i></button>'+
							'</div>'+
						'</div>';
						$('#bodyMinuman').append(newRow);
					}
				}
				
			    $('#insert_lokasi_acara').val('<?= $detail['lokasi_acara'] ?>').change();

			    makanan_ubah = makanan;
			    minuman_ubah = minuman;
                
                $(function () {
                    $('#insert_tanggal_pemesanan').datetimepicker({
                        format: 'DD-MM-Y',
						minDate : '<?php echo date('Y-m-d') ?>',
                    });
                });


                var kode_unit_pemesan = '<?php echo $_SESSION['kode_unit'] ?>';
                
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
                
                $('.select2').select2();
			});

			function cek_approval(approval) {
				if (approval==='0') {
					$('#keterangan_approval').show();
				} else {
					$('#keterangan_approval').hide();
				}
			}

            function set_lokasi(){
                var lokasi = $('#insert_lokasi_acara').children("option:selected").text();
                
                $("#insert_id_ruangan").empty();
                $("#insert_gedung").empty();
                $("#insert_snack").empty();
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
                            if ("<?= $detail['id_gedung'] ?>" == response.gedung[i]["id"]) {
                            	$("#insert_gedung").val("<?= $detail['id_gedung'] ?>");
                            	$("#insert_gedung").trigger('change');
                            } else if (i==0) {
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

                        for(i=0; i<makanan_ubah.length; i++) {
					    	makanan_ubah_2 = makanan_ubah[i];
					    	$('#insert_jumlah_makanan'+i).val(makanan_ubah_2['jumlah']);
					    	$('#insert_makanan'+i).val(makanan_ubah_2['id_makanan']).change();
					    }

                        for(i=0; i<minuman_ubah.length; i++) {
					    	minuman_ubah_2 = minuman_ubah[i];
					    	$('#insert_jumlah_minuman'+i).val(minuman_ubah_2['jumlah']);
					    	$('#insert_minuman'+i).val(minuman_ubah_2['id_makanan']).change();
					    }

					    var values="<?= $detail['snack'] ?>";
						$.each(values.split(","), function(i,e){
						    $("#insert_snack option[value='" + e + "']").prop("selected", true);
						});

                        $('.select2').select2();
		            }
		        });
            }

            function get_ruangan(){
                var gedung = $('#insert_gedung').children("option:selected").text();
                
                $("#insert_id_ruangan").empty();
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
		</script>