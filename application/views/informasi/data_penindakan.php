		
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

				<?php if(@$this->session->userdata('success')) { ?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>					
					<?= $this->session->userdata('success') ?>
				</div>
				<?php } ?>
				<?php if(@$this->session->userdata('warning')) { ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>					
					<?= $this->session->userdata('warning') ?>
				</div>
				<?php } ?>
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
										
									for($i=0;$i<count($daftar_akses_unit_kerja);$i++){
										if(strcmp($daftar_akses_unit_kerja[$i]["kode_unit"],$this->session->userdata("kode_unit"))==0){
											$selected="selected=selected";
										}
										else{
											$selected="";
										}
										echo "<option value='".$daftar_akses_unit_kerja[$i]["kode_unit"]."' $selected>".$daftar_akses_unit_kerja[$i]["kode_unit"]." - ".$daftar_akses_unit_kerja[$i]["nama_unit"]."</option>";
									}
								?>
								</select>
							</div>
							<div class='col-lg-4 col-md-4'>
								<span>Karyawan</span>
								<select class="form-control select2" name="np_karyawan" id="karyawan">
								<?php
										
									for($i=0;$i<count($daftar_akses_karyawan);$i++){
										if(strcmp($daftar_akses_karyawan[$i]["no_pokok"],$this->session->userdata("no_pokok"))==0){
											$selected="selected=selected";
										}
										else{
											$selected="";
										}
										echo "<option value='".$daftar_akses_karyawan[$i]["no_pokok"]."' $selected>".$daftar_akses_karyawan[$i]["no_pokok"]." - ".$daftar_akses_karyawan[$i]["nama"]."</option>";
									}
								?>
								</select>
							</div>
							<div class='col-lg-2'>
								<span>Periode</span>
								<select class="form-control select2" name="periode" id="insert_year" onchange="refresh_table_serverside()">
									<option value="2019" <?= ('2019'==date('Y')) ? 'selected' : ''; ?>>2019</option>
									<option value="2020" <?= ('2020'==date('Y')) ? 'selected' : ''; ?>>2020</option>
									<option value="2021" <?= ('2021'==date('Y')) ? 'selected' : ''; ?>>2021</option>
								</select>
								<!-- <input type="text" class="form-control" name="periode" autocomplete="off" value="<?= date('Y') ?>" placeholder="Tahun"> -->
							</div>
						</div>
						<br>
						<div class='row text-right'>
							<div class='col-md-12'>
								<a class='btn btn-primary btn-md' onclick="refresh_table_serverside()">Refresh</a>
								<button target="_blank" class='btn btn-warning btn-md' type='submit'>Export</button>
							</div>
						</div>
					</form>

					<br>
					<input type="hidden" name="log_data_keterlambatan" id="log_data_keterlambatan" value="no">
					<div class="form-group">	
						<div class="row">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data_penindakan">
								<thead>
									<tr>
										<th class='text-center no-sort'>No</th>
										<th class='text-center no-sort'>NP</th>
										<th class='text-center no-sort'>Nama</th>
										<th class='text-center no-sort'>Nama Unit</th>
										<th class='text-center no-sort'>Tahun</th>
										<th class='text-center no-sort'>Tanggal Restart</th>
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
		


		

        <script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		<script type="text/javascript">	
			$(document).ready(function() {
				$("#log_data_keterlambatan").val("yes");
				// setTimeout(function(){ table_serverside(); }, 5000);
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
				var table;
				
				$('#tabel_data_penindakan').DataTable().destroy();				
				//datatables
				table = $('#tabel_data_penindakan').DataTable({ 
					
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
						"url": "<?php echo site_url("informasi/penindakan/tabel_data_penindakan/")?>",
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
				
				$.ajax({
					type: "post",
					url: document.getElementById("base_url").value+"informasi/rekap_data_keterlambatan/daftar_karyawan/",
					dataType: "json",
					data: {
						"unit_kerja" : unit_kerja
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
						
						refresh_table_serverside();
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

				let np = $(this).data('key');
				var tahun = document.getElementById("insert_year").value;

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
					  	$.ajax({
					  		url: "<?= base_url('informasi/penindakan/restart/') ?>",
					  		type: "POST",
							data: {np: np, tahun: tahun},
					  		success: function(hasil) {
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
				            }
					  	});
				  	}
				});
			});
		</script>