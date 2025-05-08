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
				if($akses["lihat log"]){
					echo "<div class='row text-right'>";
						echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
						echo "<br><br>";
					echo "</div>";
				}
				
				if($this->akses["lihat"]){
			?>
					<div class="row">
						<div class='col-lg-3'>
							<b>Unit Kerja</b>
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
						<div class='col-lg-3'>
							<b>Karyawan</b>
							<select class="form-control select2" name="karyawan" id="karyawan" onchange="refresh_table_serverside()">
							</select>
						</div>
						<div class='col-lg-3'>
							<b>Periode</b>
							<div class="row">
								<div class='col-lg-6'>
									<select class="form-control select2" name="bulan" id="bulan" onchange="refresh_table_serverside()">
									</select>
								</div>
								<div class='col-lg-6'>
									<select class="form-control select2" name="tahun" id="tahun" onchange="pilihan_bulan();refresh_table_serverside();">
									<?php
											
										for($i=0;$i<count($arr_tahun);$i++){
											if(strcmp($arr_tahun[$i]["value"],date("Y_m"))==0){
												$selected="selected=selected";
											}
											else{
												$selected="";
											}
											echo "<option value='".$arr_tahun[$i]."' $selected>".$arr_tahun[$i]."</option>";
										}
									?>
									</select>
								</div>
							</div>
						</div>
						<div class='col-lg-3' id='pilihan_akumulasi' style='display:none'>
							<label><input type='radio' name='tipe_akumulasi' id='akumulasi_rincian' onchange='refresh_table_serverside()' value='rincian' checked='checked'> rincian</label><br>
							<label><input type='radio' name='tipe_akumulasi' id='akumulasi_karyawan' onchange='refresh_table_serverside()' value='akumulasi karyawan'> akumulasi tiap karyawan</label><br>
							<label><input type='radio' name='tipe_akumulasi' id='akumulasi_bulan' onchange='refresh_table_serverside()' value='akumulasi bulan'> akumulasi tiap bulan</label>
						</div>
					</div>
					<div class="panel-body">
						<!-- Nav tabs -->
						<ul class="nav nav-tabs">
							<li class="active"><a href="#hasil_data_lembur_nominal" data-toggle="tab" onclick="set_tab_aktif('nominal');"><b>Nominal</b></a>
							</li>
							<li><a href="#hasil_data_lembur_peringkat_nominal" data-toggle="tab" onclick="set_tab_aktif('peringkat nominal');"><b>Peringkat Nominal</b></a>
							</li>
							<li><a href="#hasil_data_lembur_peringkat_persentase" data-toggle="tab" onclick="set_tab_aktif('peringkat persentase');"><b>Peringkat Persentase</b></a>
							</li>
						</ul>
						<input type="hidden" name="tab_aktif" id="tab_aktif" value="nominal"/>
						<br>
						<!-- Tab panes -->
						<div class="tab-content">
							<div class="tab-pane fade in active" id="hasil_data_lembur_nominal"></div>
							<div class="tab-pane fade in active" id="hasil_data_lembur_peringkat_nominal"></div>
							<div class="tab-pane fade in active" id="hasil_data_lembur_peringkat_persentase"></div>
						</div>
					</div>
					<input type="hidden" name="log_data_lembur" id="log_data_lembur" value="no"/>			
			<?php
				}
			?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		
		<script type="text/javascript">	
			/* $(document).ready(function() {
				$("#log_data_lembur").val("yes");
				table_serverside();
				$("#log_data_lembur").val("no");
			});	 */	
			
			function refresh_table_serverside() {
				$("#log_data_lembur").val("yes");
					lembur_karyawan();
				$("#log_data_lembur").val("no");
			}
		</script>
		
		<script>		
			function lembur_karyawan(){
				if(document.getElementById("karyawan").value=="" && document.getElementById("bulan").value==""){
					document.getElementById("pilihan_akumulasi").style.display="inline";
				}
				else{
					document.getElementById("pilihan_akumulasi").style.display="none";
					document.getElementById("akumulasi_rincian").checked=true;
				}
				
				var akumulasi = "";
				if(document.getElementById("akumulasi_rincian").checked){
					akumulasi = document.getElementById("akumulasi_rincian").value;
				}
				else if(document.getElementById("akumulasi_bulan").checked){
					akumulasi = document.getElementById("akumulasi_bulan").value;
				}
				else if(document.getElementById("akumulasi_karyawan").checked){
					akumulasi = document.getElementById("akumulasi_karyawan").value;
				}
				
				var url = "";
				var area = "";
				var area_1 = "hasil_data_lembur_nominal";
				var area_2 = "hasil_data_lembur_peringkat_nominal";
				var area_3 = "hasil_data_lembur_peringkat_persentase";
				
				if(document.getElementById("tab_aktif").value == "nominal"){
					url = "header_nominal_lembur_karyawan";
					area = area_1;
				}
				else if(document.getElementById("tab_aktif").value == "peringkat nominal"){
					url = "header_peringkat_lembur";
					area = area_2;
				}
				else if(document.getElementById("tab_aktif").value == "peringkat persentase"){
					url = "header_peringkat_lembur";
					area = area_3;
				}
					
				document.getElementById(area_1).innerHTML = "";
				document.getElementById(area_2).innerHTML = "";
				document.getElementById(area_3).innerHTML = "";
				
				if(url!=""){
					$.ajax({
						type: "post",
						url: document.getElementById("base_url").value+"informasi/data_lembur/"+url,
						data: {
							"kode_unit" : document.getElementById("unit_kerja").value,
							"np_karyawan" : document.getElementById("karyawan").value,
							"bulan" : document.getElementById("bulan").value,
							"tahun" : document.getElementById("tahun").value,
							"tab" : document.getElementById("tab_aktif").value,
							"akumulasi" : akumulasi,
						},
						success: function (data) {
							document.getElementById(area).innerHTML = data;
							
							if(document.getElementById("tab_aktif").value == "nominal"){
								table_serverside_lembur_karyawan_nominal();
							}
							else if(document.getElementById("tab_aktif").value == "peringkat nominal" || document.getElementById("tab_aktif").value == "peringkat persentase"){
								table_serverside_lembur_karyawan_peringkat();
							}
						}
					});
				}
			}
			
			function table_serverside_lembur_karyawan_nominal(){
				
				var arr_right_col = [];
				var akumulasi = "";
				
				if(document.getElementById("akumulasi_rincian").checked){
					akumulasi = document.getElementById("akumulasi_rincian").value;
					arr_right_col = [0, 2, 3];
					if(document.getElementById("karyawan").value == ""){
						arr_right_col = [0, 4, 5];
					}
				}
				else if(document.getElementById("akumulasi_bulan").checked){
					akumulasi = document.getElementById("akumulasi_bulan").value;
					arr_right_col = [0, 2, 3];
				}
				else if(document.getElementById("akumulasi_karyawan").checked){
					akumulasi = document.getElementById("akumulasi_karyawan").value;
					arr_right_col = [0, 3, 4];
				}
				
				$('#tabel_nominal_lembur_karyawan').DataTable().destroy();				
				//datatables
				$('#tabel_nominal_lembur_karyawan').DataTable({ 
					
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
						"url": "<?php echo site_url("informasi/data_lembur/nominal_lembur_karyawan/")?>",
						"type": "POST",
						"data": {
							"kode_unit" : document.getElementById("unit_kerja").value,
							"np_karyawan" : document.getElementById("karyawan").value,
							"bulan" : document.getElementById("bulan").value,
							"tahun" : document.getElementById("tahun").value,
							"akumulasi" : akumulasi,
						}
					},

					//Set column definition initialisation properties.
					"columnDefs": [
						{ 						
							"targets": [ 0 ], //first column / numbering column
							"targets": 'no-sort', //first column / numbering column
							"orderable": false, //set not orderable
						},
						{ 
							"targets": arr_right_col,
							"className": "text-right", //set not orderable
						},
					],
				});
			}

			function table_serverside_lembur_karyawan_peringkat(){
				
				var banyak_kolom = document.getElementById("tabel_peringkat_lembur_karyawan").rows[0].cells.length + document.getElementById("tabel_peringkat_lembur_karyawan").rows[1].cells.length - 1;
			
				var arr_right_col = [];
				var akumulasi = "";
				var start_right = 2;
				
				if(document.getElementById("akumulasi_rincian").checked){
					akumulasi = document.getElementById("akumulasi_rincian").value;
					
					if(document.getElementById("karyawan").value == ""){
						start_right = 3;
					}
				}
				else if(document.getElementById("akumulasi_bulan").checked){
					akumulasi = document.getElementById("akumulasi_bulan").value;
				}
				else if(document.getElementById("akumulasi_karyawan").checked){
					akumulasi = document.getElementById("akumulasi_karyawan").value;
				}
				
				for(var i=start_right;i<banyak_kolom;i++){
					arr_right_col.push(i);
				}
				
				$('#tabel_peringkat_lembur_karyawan').DataTable().destroy();
				//datatables
				$('#tabel_peringkat_lembur_karyawan').DataTable({ 
					
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
						"url": "<?php echo site_url("informasi/data_lembur/peringkat_lembur_karyawan/")?>",
						"type": "POST",
						"data": {
							"kode_unit" : document.getElementById("unit_kerja").value,
							"np_karyawan" : document.getElementById("karyawan").value,
							"bulan" : document.getElementById("bulan").value,
							"tahun" : document.getElementById("tahun").value,
							"tab" : document.getElementById("tab_aktif").value,
							"akumulasi" : akumulasi,
						}
					},

					//Set column definition initialisation properties.
					"columnDefs": [
						{ 						
							"targets": [ 0 ], //first column / numbering column
							"targets": 'no-sort', //first column / numbering column
							"orderable": false, //set not orderable
						},
						{ 
							"targets": arr_right_col,
							"className": "text-right", //set not orderable
						},
					],
				});
			}
			
			function pilihan_bulan(){
				var tahun = $('#tahun').val();
				var bulan = $('#bulan').val();
				if(bulan==null){
					bulan = "";
				}
				
				$.ajax({
					type: "post",
					url: document.getElementById("base_url").value+"informasi/data_lembur/pilih_tahun/",
					dataType: "json",
					data: {
						"tahun" : tahun
					},
					success: function (data) {
						while(document.getElementById("bulan").length>0){
							document.getElementById("bulan").remove(document.getElementById("bulan").length-1);
						}
						
						banyak_bulan = data.length;
						
						if(banyak_bulan>1){
							var option = document.createElement("option");
							option.value = "";
							option.text = "(semua bulan)";
							document.getElementById("bulan").add(option);
						}
						
						for(var i=0;i<banyak_bulan;i++){
							var option = document.createElement("option");
							option.value = data[i].bulan;
							option.text = data[i].nama_bulan;
							document.getElementById("bulan").add(option);
						}
						document.getElementById("bulan").value = bulan;
					}
				});
			}
			
			function pilihan_karyawan(){
				var unit_kerja = $('#unit_kerja').val();
				
				$.ajax({
					type: "post",
					url: document.getElementById("base_url").value+"informasi/data_lembur/daftar_karyawan/",
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
						if(document.getElementById("karyawan").selectedIndex<0){
							document.getElementById("karyawan").selectedIndex = 0;
						}
						
						refresh_table_serverside();
					}
				});
			}
			
			function set_tab_aktif(nama_tab){
				if(document.getElementById("tab_aktif").value != nama_tab){
					document.getElementById("tab_aktif").value = nama_tab;
					refresh_table_serverside();
				}
			}
		</script>