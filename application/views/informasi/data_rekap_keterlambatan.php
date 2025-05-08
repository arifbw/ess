		
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

				<!-- <div class="alert alert-info alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>					
					Data keterlambatan yang ditampilkan berikut ini dihasilkan dari membandingkan data kehadiran dengan jadwal kerja.
				</div> -->
			<?php
				if($akses["lihat log"]){
					echo "<div class='row text-right'>";
						echo "<div class='col-md-12'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					echo "</div>";
				}
				
				if($this->akses["lihat"]){
			?>
				
					<form action="<?= site_url('informasi/rekap_data_keterlambatan/export') ?>" method="POST" target="_blank">
						<div class="row">
							<div class='col-lg-6 col-md-6'>
								<span>Unit Kerja</span>
								<select class="form-control select2" name="kode_unit" id="unit_kerja" onchange="pilihan_karyawan()">
								<?php
									/*	heru PDS comment ini, sto ambil sesuai periode, 2021-01-11
									for($i=0;$i<count($daftar_akses_unit_kerja);$i++){
										if(strcmp($daftar_akses_unit_kerja[$i]["kode_unit"],$this->session->userdata("kode_unit"))==0){
											$selected="selected=selected";
										}
										else{
											$selected="";
										}
										echo "<option value='".$daftar_akses_unit_kerja[$i]["kode_unit"]."' $selected>".$daftar_akses_unit_kerja[$i]["kode_unit"]." - ".$daftar_akses_unit_kerja[$i]["nama_unit"]."</option>";
									}*/
								?>
								</select>
							</div>
							<div class='col-lg-4 col-md-4'>
								<span>Karyawan</span>
								<select class="form-control select2" name="np_karyawan" id="karyawan">
								<?php
									/* heru PDS comment ini, karyawan ambil sesuai STO, 2021-01-11
									for($i=0;$i<count($daftar_akses_karyawan);$i++){
										if(strcmp($daftar_akses_karyawan[$i]["no_pokok"],$this->session->userdata("no_pokok"))==0){
											$selected="selected=selected";
										}
										else{
											$selected="";
										}
										echo "<option value='".$daftar_akses_karyawan[$i]["no_pokok"]."' $selected>".$daftar_akses_karyawan[$i]["no_pokok"]." - ".$daftar_akses_karyawan[$i]["nama"]."</option>";
									}*/
								?>
								</select>
							</div>
							<div class='col-lg-2'>
								<span>Periode</span>
								<select class="form-control select2" name="periode" id="insert_year" onchange="getUnitByPeriode()">
									<?php for($i=0; $i<count($arr_tahun); $i++) { ?>
									<option value="<?= $arr_tahun[$i] ?>" <?= ($arr_tahun[$i]==date('Y')) ? 'selected' : ''; ?>><?= $arr_tahun[$i] ?></option>
									<?php } ?>
								</select>
								<!-- <input type="text" class="form-control" name="periode" autocomplete="off" value="<?= date('Y') ?>" placeholder="Tahun"> -->
							</div>
						</div>
						<br>
						<div class='row text-right'>
							<div class='col-md-12'>
								<a class='btn btn-primary btn-md' id="btn-refresh" onclick="refresh_table_serverside()">Refresh</a> <!-- heru PDS tambahkan ID button, 2021-01-11 -->
								<button target="_blank" class='btn btn-warning btn-md' type='submit'>Export</button>
							</div>
						</div>
					</form>

					<br>
					<input type="hidden" name="log_data_keterlambatan" id="log_data_keterlambatan" value="no">
					<div class="form-group">	
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data_keterlambatan">
								<thead>
									<tr>
										<th class='text-center no-sort'>No</th>
										<th class='text-center no-sort'>NP</th>
										<th class='text-center no-sort'>Nama</th>
										<th class='text-center no-sort'>Nama Unit</th>
										<th class='text-center no-sort'>Jumlah</th>
										<th class='text-center no-sort'>Aksi</th>
									</tr>
								</thead>
								<tbody>
								
								</tbody>
							</table>
							<!-- /.table-responsive -->
						</div>						
					</div>
			
			<?php
				}
			?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<!-- Modal -->
		<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content edit-content">
					<div class="table-responsive-sm">
						<form role="form" action="" id="form_penindakan" method="post" enctype='multipart/form-data'> 
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_ubah">Upload Evidence Hasil Penindakan</h4>
							</div>
							<div class="modal-body">
								<div class="text-center">
									<div class="row">
										<div class="col-lg-offset-3 col-lg-6">
											<div class="alert alert-danger alert-dismissable">
												<b>Upload Evidence Hasil Penindakan</b>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-lg-4"></div>
										<div class="col-lg-4">
                                            <input type="hidden" name="np" id="np_penindakan"/>
                                            <input type="hidden" name="tahun" id="tahun_penindakan"/>
                                            <div class="form-group">
                                                <input class="form-control" type="file" name="file_evidence" placeholder="Pilih File">
                                                <span class="text-danger">file image/pdf</span>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
										</div>
										<div class="col-lg-4"></div>
									</div>
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

		

        <script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		<script type="text/javascript">
            var table;
			$(document).ready(function() {
				$("#log_data_keterlambatan").val("yes");
				// setTimeout(function(){ table_serverside(); }, 5000);
                // heru PDS tambahkan ini, 2021-01-11
                getUnitByPeriode(); 
                setTimeout(function(){ $(`#btn-refresh`).trigger('click'); }, 5000);
                // END heru PDS tambahkan ini, 2021-01-11
				$("#log_data_keterlambatan").val("no");
			});		
			
			function refresh_table_serverside() {
				$("#log_data_keterlambatan").val("yes");
				table_serverside();
				$("#log_data_keterlambatan").val("no");
			}

            $(function () {
                $('#insert_year').datetimepicker({
                    format: 'YYYY-MM-DD',
                    viewMode: "years", 
    				minViewMode: "years"
                });
			});
		</script>
		
		<script>		
			function table_serverside(){
				//$('#tabel_data_keterlambatan').DataTable().destroy();				
				//datatables
				table = $('#tabel_data_keterlambatan').DataTable({ 
					destroy: true,
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"bFilter": false,
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("informasi/rekap_data_keterlambatan/tabel_data_rekap_keterlambatan/")?>",
						"type": "POST",
						"data": {
							"kode_unit" : document.getElementById("unit_kerja").value,
							"np_karyawan" : document.getElementById("karyawan").value,
							"periode" : document.getElementById("insert_year").value,
						}
					},

					//Set column definition initialisation properties.
					"columnDefs": [
					{ 						
						"targets": [ 0 ], //first column / numbering column
						"targets": 'no-sort', //first column / numbering column
						"orderable": false, //set not orderable
					},
					],

				});
			
			}
			
			function pilihan_karyawan(){
				var unit_kerja = $('#unit_kerja').val();
                // heru PDS tambahkan variable ini, 2021-01-11
				let insert_year = $('#insert_year').val();
				$.ajax({
					type: "post",
                    // heru PDS mengganti url mjd daftar_karyawan_revisi, 2021-01-11
					url: document.getElementById("base_url").value+"informasi/rekap_data_keterlambatan/daftar_karyawan_revisi/",
					dataType: "json",
					data: {
						"unit_kerja" : unit_kerja,
                        // heru PDS tambahkan parameter ini, 2021-01-11
                        insert_year: insert_year
					},
					success: function (data) {
						while(document.getElementById("karyawan").length>0){
							document.getElementById("karyawan").remove(document.getElementById("karyawan").length-1);
						}
						
						banyak_karyawan = data.karyawan.length;
						
						if(banyak_karyawan>1){
							var option = document.createElement("option");
							option.value = "";
							option.text = "(semua karyawan)";
							document.getElementById("karyawan").add(option);
						}
						
						for(var i=0;i<banyak_karyawan;i++){
							var option = document.createElement("option");
							option.value = data.karyawan[i].no_pokok;
							option.text = data.karyawan[i].no_pokok+" - "+data.karyawan[i].nama;
							document.getElementById("karyawan").add(option);
						}
						document.getElementById("karyawan").value = data.np_pengguna;
						
						// refresh_table_serverside();
					}
				});
			
			}		

			function export_keterlambatan(){
				var kode_unit = document.getElementById("unit_kerja").value;
				var np_karyawan = document.getElementById("karyawan").value;
				var periode_awal = document.getElementById("insert_date_awal").value;
				var periode_akhir = document.getElementById("insert_date_akhir").value;
				
				$.ajax({
					type: "post",
					url: document.getElementById("base_url").value+"informasi/data_keterlambatan/cetak/",
					dataType: "json",
					data: {
						"unit_kerja" : unit_kerja,
						"np_karyawan" : np_karyawan,
						"periode_awal" : periode_awal,
						"periode_akhir" : periode_akhir,
					},
					success: function () {
						alert('Berhasil Export Data');
					}
				});
			
			}

			$(document).on('click', '.restart', function(e) {
				e.preventDefault();
				Swal.fire({
				  	title: 'Apakah anda yakin ingin mereset jumlah keterlambatan?',
				  	icon: 'warning',
				  	showCancelButton: true,
				  	confirmButtonColor: '#3085d6',
				  	cancelButtonColor: '#d33',
				  	confirmButtonText: 'Ya, reset!',
				  	cancelButtonText: 'Batal',
				}).then((result) => {
					if (result.value) {
						$('#modal_form').modal('show');
						$('#np_penindakan').val($(this).data('key'));
						$('#tahun_penindakan').val(document.getElementById("insert_year").value);
						$('#form_penindakan').attr('action', '<?php echo base_url('informasi/penindakan/restart'); ?>');
					}
				});

			});

			function save() {
					  		/*success: function(hasil) {
					  			console.log(hasil);
					  			get = JSON.parse(hasil);
					  			Swal.fire({
								  position: 'top-end',
								  icon: get.status,
								  title: get.title,
								  showConfirmButton: true,
								}).then((result) => {
					  			  location.reload();
					  			});
					  		},
					  		error: function(){
				                Swal.fire(
								  'Gagal',
								  'Reset data keterlambatan tidak berhasil !',
								  'warning'
								);
				            }*/
			};
            
            // heru PDS tambahkan ini, 2021-01-11
            const getUnitByPeriode=()=>{
                console.log('Get Unit...');
                $(`#unit_kerja`).html('');
                let insert_year = $(`#insert_year`).val();
                let url = document.getElementById("base_url").value;
                
                $.ajax({
					type: "POST",
					url: `${url}informasi/rekap_data_keterlambatan/getUnitByPeriode`,
					dataType: "html",
					data: {
						insert_year : insert_year
					},
					success: function (response) {
                        console.log('Unit Printed.');
						$(`#unit_kerja`).html(response);
                        pilihan_karyawan();
					},
                    error: function(){
                        $(`#unit_kerja`).html('');
                    }
				});
            }
            // END heru PDS tambahkan ini, 2021-01-11
		</script>