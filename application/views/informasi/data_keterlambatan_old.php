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

				<div class="alert alert-info alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>					
					Data keterlambatan yang ditampilkan berikut ini dihasilkan dari membandingkan data kehadiran dengan jadwal kerja.
				</div>
			<?php
				if($akses["lihat log"]){
					echo "<div class='row text-right'>";
						echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
						echo "<br><br>";
					echo "</div>";
				}
				
				if($this->akses["lihat"]){
			?>
			
					<div class="row">
						<div class='col-lg-2'>Unit Kerja</div>
						<div class='col-lg-3'>
							<select class="form-control select2" name="unit_kerja" id="unit_kerja" onchange="pilihan_karyawan()">
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
						<div class='col-lg-1'>Karyawan</div>
						<div class='col-lg-3'>
							<select class="form-control select2" name="karyawan" id="karyawan" onchange="refresh_table_serverside()">
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
						<div class='col-lg-1'>Periode</div>
						<div class='col-lg-2'>
							<select class="form-control select2" name="periode" id="periode" onchange="refresh_table_serverside()">
							<?php
									
								for($i=0;$i<count($arr_periode);$i++){
									if(strcmp($arr_periode[$i]["value"],date("Y_m"))==0){
										$selected="selected=selected";
									}
									else{
										$selected="";
									}
									echo "<option value='".$arr_periode[$i]["value"]."' $selected>".$arr_periode[$i]["text"]."</option>";
								}
							?>
							</select>
						</div>
					</div>
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
										<th class='text-center no-sort'>Tanggal</th>
										<th class='text-center no-sort'>Nama Jadwal</th>
										<th class='text-center no-sort'>Jadwal Masuk</th>
										<th class='text-center no-sort'>Waktu Datang</th>
										<th class='text-center no-sort'>Keterangan</th>
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
		
		
		<script type="text/javascript">	
			$(document).ready(function() {
				$("#log_data_keterlambatan").val("yes");
				table_serverside();
				$("#log_data_keterlambatan").val("no");
			});		
			
			function refresh_table_serverside() {
				$("#log_data_keterlambatan").val("yes");
				table_serverside();
				$("#log_data_keterlambatan").val("no");
			}
		</script>
		
		<script>		
			function table_serverside(){
				var table;
				
				$('#tabel_data_keterlambatan').DataTable().destroy();				
				//datatables
				table = $('#tabel_data_keterlambatan').DataTable({ 
					
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
						"url": "<?php echo site_url("informasi/data_keterlambatan/tabel_data_keterlambatan/")?>",
						"type": "POST",
						"data": {
							"kode_unit" : document.getElementById("unit_kerja").value,
							"np_karyawan" : document.getElementById("karyawan").value,
							"periode" : document.getElementById("periode").value,
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
					url: document.getElementById("base_url").value+"informasi/data_keterlambatan/daftar_karyawan/",
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
				var periode = document.getElementById("periode").value;
				
				$.ajax({
					type: "post",
					url: document.getElementById("base_url").value+"informasi/data_keterlambatan/cetak/",
					dataType: "json",
					data: {
						"unit_kerja" : unit_kerja,
						"np_karyawan" : np_karyawan,
						"periode" : periode
					},
					success: function () {
						alert('Berhasil Export Data');
					}
				});
			
			}		
		</script>