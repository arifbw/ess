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
					if(!empty($this->session->flashdata('success'))){
				?>
						<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $this->session->flashdata('success');?>
						</div>
				<?php
					}
					if(!empty($this->session->flashdata('warning'))){
				?>
						<div class="alert alert-danger alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $this->session->flashdata('warning');?>
						</div>
				<?php
					}
					if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					
					if($this->akses["lihat"]){
				?>
				
						<div class="row">
							<div class='col-lg-1'>Karyawan</div>
							<div class='col-lg-5'>
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
							<div class='col-lg-5'>
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
						<input type="hidden" name="log_data_pamlek" id="log_data_pamlek" value="no">
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data_pamlek">
									<thead>
										<tr>
											<th class='text-center no-sort'>No</th>
											<th class='text-center no-sort'>Jenis</th>
											<th class='text-center no-sort'>Tipe</th>
											<th class='text-center no-sort'>Nomor Mesin Pamlek</th>
											<th class='text-center no-sort'>Waktu</th>
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
				$("#log_data_pamlek").val("yes");
				table_serverside();
				$("#log_data_pamlek").val("no");
			});		
			
			function refresh_table_serverside() {
				$("#log_data_pamlek").val("yes");
				table_serverside();
				$("#log_data_pamlek").val("no");
			}
		</script>
		
		<script>		
			function table_serverside(){
				var table;				
				var periode = $('#periode').val();
				var karyawan = $('#karyawan').val();
				
				$('#tabel_data_pamlek').DataTable().destroy();				
				//datatables
				table = $('#tabel_data_pamlek').DataTable({ 
					
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
						"url": "<?php echo site_url("informasi/data_pamlek/tabel_data_pamlek/")?>"+periode+"/"+karyawan,
						"type": "POST"
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
		</script>