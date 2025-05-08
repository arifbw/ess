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
					if($akses["tambah"]){
				?>
						<div class="row">						
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
										</h4>
									</div>
									<div id="collapseOne" class="panel-collapse collapse">
										<div class="panel-body">
											
												<form role="form" action="<?php echo base_url(); ?>osdm/pembatalan_cuti/action_insert_pembatalan_cuti" id="formulir_tambah" method="post">	
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Cuti Yang Akan Dibatalkan</label>
															</div>
															<div class="col-lg-7">
																
																<select class="form-control select2" id='insert_id_cuti' name='insert_id_cuti' onChange="getDate()" style="width: 100%;" required>
																<option value=''></option>	
																	<?php 
																	foreach ($array_daftar_cuti->result_array() as $value) {		
																	?>
																		<option value='Cuti,<?php echo $value['id']?>'><?php echo $value['np_karyawan']." ".tanggal_indonesia($value['start_date'])." - ".tanggal_indonesia($value['end_date']).", ".$value['uraian']?></option>								
																		
																	<?php 
																	}
																	?>
																	<?php 
																	foreach ($array_daftar_cuti_bersama->result_array() as $value) {		
																	?>
																		<option value='Cuti Bersama,<?php echo $value['id']?>'><?php echo $value['np_karyawan']." ".tanggal_indonesia($value['tanggal_cuti_bersama'])." - ".tanggal_indonesia($value['tanggal_cuti_bersama']).", Cuti Bersama"?></option>								
																		
																	<?php 
																	}
																	?>
																</select>
																 
																 
															</div>													
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Tanggal Awal Pembatalan</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control" name="insert_date_awal" id="insert_date_awal" required>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Tanggal Pembatalan</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control" name="insert_date_akhir" id="insert_date_akhir" required>
															</div>
														</div>
													</div>
																												
													<div class="row">
														<div class="col-lg-9 text-right">
															<input type="submit" name="submit" value="submit" class="btn btn-primary">
														</div>
													</div>
												</form>
											
									</div>
								</div>
							</div>
							<!-- /.col-lg-12 -->
						</div>
						<!-- /.row -->
						
						
				<?php
					}
					
					if($this->akses["lihat"]){
				?>				
				
					
					<p id="demo"></p>
						<div class="form-group">
							<div class="row">
								<form method="post" target="_blank" action="<?php echo base_url('osdm/pembatalan_cuti/cetak')?>">
								<div class="col-lg-6" style="padding-left: 0px">
									<label>Bulan</label>
									<!--<select id="pilih_bulan_tanggal" class="form-control">-->
									<select class="form-control" id='bulan_tahun' name='bulan_tahun'  onchange="refresh_table_serverside()" style="width: 200px;" required>
										<option value=''></option>	
									<?php 
									foreach ($array_tahun_bulan as $value) {
										
										$tampil_bulan_tahun='';
										if(!empty($this->session->flashdata('tampil_bulan_tahun')))
										{
											$tampil_bulan_tahun=$this->session->flashdata('tampil_bulan_tahun');
										}
										if($tampil_bulan_tahun==$value)
										{
											$selected='selected';
										}else
										{
											$selected='';
										}
									?>
										<option value='<?php echo $value?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,0,2))." ".substr($value,3,4)?></option>								
										
									<?php 
									}
									?>
									</select>
								</div>	
								
																	
								<div class="col-lg-6" style="padding-left: 0px;padding-right: 0px; text-align: right;">
										<label><font color="white">Cetak</font></label><br>
										<button type="submit" class="btn btn-success"><i class="fa fa-print"></i> Cetak</button>
								</div>				
								</form>			
							</div>
						</div>
													
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_pembatalan_cuti">
									<thead>
										<tr>
											<th class='text-center'>No</th>
											<th class='text-center'>NP</th>	
											<th class='text-center'>NAMA</th>			
											<th class='text-center'>URAIAN</th>
											<th class='text-center'>TERTANGGAL</th>
											<th class='text-center'>TANGGAL SUBMIT</th>	
											<th class='text-center no-sort'>AKSI</th>		
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
					
					if($akses["batal"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_batal" tabindex="-1" role="dialog" aria-labelledby="label_modal_batal" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form method="post" action="<?php echo base_url(); ?>osdm/pembatalan_cuti/action_batal_pembatalan_cuti">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Batal <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">		
											
											<table>
												<tr>
													<td>Np Karyawan</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_np_karyawan"></a></td>
												</tr>
												<tr>
													<td>Nama Karyawan</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_nama"></a></td>
												</tr>
												<tr>
													<td>Jenis Cuti</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_uraian"></a></td>
												</tr>
												<tr>
													<td>Tanggal</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_date"></a></td>
												</tr>
											
												<!--
												<tr>
													<td>DWS Variant</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_dws_variant"></a></td>
												</tr>
												-->
											</table>
																		
											<div class="modal-footer">.
												<input type='hidden' id='batal_id' name='batal_id'>
												<button name='submit' type="submit" value='Batalkan Cuti' class="btn btn-warning">Batalkan Cuti</button>											
											</div>
										
											
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
		
        <script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
        <script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
        
		<script type="text/javascript">
            $('.select2').select2();
            $(function () {
                $('#insert_date_awal').datetimepicker({
                    format: 'D-M-Y'
                });

                $('#insert_date_akhir').datetimepicker({
                    format: 'D-M-Y'
                });

                $("#insert_date_awal").on("dp.change", function (e) {
                    //var oldDate = new Date(e.date);
                    //var newDate = new Date(e.date);
                    //newDate.setDate(oldDate.getDate());
					var insert_date_awal = $('#insert_date_awal').val();
						
                    $('#insert_date_akhir').data("DateTimePicker").minDate(insert_date_awal);    
					$('#insert_date_akhir').val('');
                });

            });
            
			function get_tgl_awal(){
				var tgl_awal = $('#tgl_awal').val();
				var tgl_akhir = $('#tgl_akhir').val();
				if ((tgl_akhir != '') && (tgl_awal.substr(0, 7) != tgl_akhir.substr(0, 7))) {
					document.getElementById("tgl_awal").valueAsDate = null;
					document.getElementById("tgl_akhir").valueAsDate = null;
					alert("Tanggal harus dalam bulan yang sama");
				}else{
					document.getElementById('tgl_akhir').setAttribute("min", tgl_awal);
				}
			} 
			function get_tgl_akhir(){
				var tgl_awal = $('#tgl_awal').val();
				var tgl_akhir = $('#tgl_akhir').val();
				if ((tgl_awal != '') && (tgl_awal.substr(0, 7) != tgl_akhir.substr(0, 7))) {
					document.getElementById("tgl_awal").valueAsDate = null;
					document.getElementById("tgl_akhir").valueAsDate = null;
					alert("Tanggal harus dalam bulan yang sama");
				}else{
					document.getElementById('tgl_awal').setAttribute("max", tgl_akhir);
				}
			}
		</script>

		<script type="text/javascript">	
			$(document).ready(function() {				
				table_serverside();
			});		
			
			function refresh_table_serverside() {
				$('#tabel_pembatalan_cuti').DataTable().destroy();				
				table_serverside();
			}
		</script>
		
		<script>
		function table_serverside()
		{
			var table;
			var bulan_tahun = $('#bulan_tahun').val();
			
			
				//datatables
				table = $('#tabel_pembatalan_cuti').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("osdm/pembatalan_cuti/tabel_pembatalan_cuti/")?>"+bulan_tahun,
						"type": "POST"
					},

					//Set column definition initialisation properties.
					"columnDefs": [
					{ 
						"targets": 'no-sort', //first column / numbering column
						"orderable": false, //set not orderable
					},
					],

				});
			
		}
		</script>
		
		<?php
			if($akses["tambah"]){
		?>
			<script>
				function listNp(){			
				$.ajax({
				 type: "POST",
				 dataType: "html",
				 url: "<?php echo base_url('kehadiran/perencanaan_jadwal_kerja/ajax_getListNp');?>",             
					success: function(msg){
						if(msg == ''){
							alert ('Silahkan isi No. Pokok Dengan Benar.');
							$('#list_np').text('');
						}else{							 
							$('#list_np').text(msg);
						}													  
					 }
				 });       
				} 
			</script>		
			<script>
				//batasi tanggal input pembatalan cuti 
				function getDate(){
				var insert_id_cuti = $('#insert_id_cuti').val();
				
				$.ajax({
				 type: "POST",
				 dataType: "html",
				 url: "<?php echo base_url('osdm/pembatalan_cuti/ajax_getDate');?>",
				 data: "vid_cuti="+insert_id_cuti,
					success: function(msg){
						if(msg == ''){
							alert ('Cuti tidak ditemukan, hubungi administrator');
							
						}else{	
							
							var split = msg.split(",");
							
							var start_date = split[0];
							var end_date = split[1];
							
							var start_split 	= start_date.split("-");
							var start_tahun 	= start_split[0];
							var start_bulan		= start_split[1];
							var start_tanggal	= start_split[2];
							var start_date_dmy	= start_tanggal+'-'+start_bulan+'-'+start_tahun;
							
							var end_split 		= end_date.split("-");
							var end_tahun 		= end_split[0];
							var end_bulan		= end_split[1];
							var end_tanggal		= end_split[2];
							var end_date_dmy	= end_tanggal+'-'+end_bulan+'-'+end_tahun;
							
							if(start_date_dmy==end_date_dmy)
							{
								$('#insert_date_awal').val(start_date_dmy);
								document.getElementById("insert_date_awal").readOnly = true;
								$('#insert_date_akhir').val(start_date_dmy);
								document.getElementById("insert_date_akhir").readOnly = true;
							}else
							{
								document.getElementById("insert_date_awal").readOnly = false;
								document.getElementById("insert_date_akhir").readOnly = false;
								
								$('#insert_date_awal').data("DateTimePicker").minDate(start_date_dmy);  
								$('#insert_date_awal').data("DateTimePicker").maxDate(end_date_dmy);				
								$('#insert_date_awal').val('');
								
								$('#insert_date_akhir').data("DateTimePicker").minDate(start_date_dmy);
								$('#insert_date_akhir').data("DateTimePicker").maxDate(end_date_dmy);    
								$('#insert_date_akhir').val('');
							}
							
						
					
					
						}													  
					 }
				 });       
			} 
			</script>
			<script>
				function getEndDate(){
					var insert_date_awal = $('#insert_date_awal').val();			
					document.getElementById('insert_date_akhir').setAttribute("min", insert_date_awal);					
					document.getElementById("insert_date_akhir").value = insert_date_awal;
				} 
			</script>
		<?php } 
		if($akses["batal"]){
		?>		
			<script>
				$(document).on( "click", '.batal_button',function(e) {
					var id = $(this).data('id');
					var np_karyawan = $(this).data('np-karyawan');
					var nama = $(this).data('nama');
					var uraian = $(this).data('uraian');					
					var date = $(this).data('date');
																					
					$("#batal_id").val(id);					
					$("#batal_np_karyawan").text(np_karyawan);
					$("#batal_nama").text(nama);
					$("#batal_uraian").text(uraian);
					$("#batal_date").text(date);
											
				});
			</script>
		<?php } ?>
	
	