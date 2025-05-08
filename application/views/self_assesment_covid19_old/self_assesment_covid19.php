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
											<a>Self Assesment Covid19</a>
										</h4>
										<h5>
									</div>
									<div>
										<div class="panel-body">
											<div class="alert alert-info alert-dismissable">
												<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>					
												Terakhir melakukan self assesment covid19 : <strong><?php echo ($last_assesment==null ? 'Belum Assesment' : $last_assesment);?></strong>
											</div>
												
												<form role="form" action="<?php echo base_url(); ?>self_assesment_covid19/self_assesment_covid19/action_insert" id="formulir_tambah" method="post">												
																								
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
																<label>Apakah pernah keluar rumah/tempat umum (pasar, fasyankes, kerumunan orang, dll ?</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
                                                                <label class="radio-inline">
																	<input type="radio" name="pernah_keluar" id="pernah_keluar1" value="1">Ya
																</label>
																<label class="radio-inline">
																	<input type="radio" name="pernah_keluar" id="pernah_keluar2" value="2">Tidak
																</label>
																
															</div>
															<!--							
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>
															-->															
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
																<label>Apakah pernah menggunakan transportasi umum ?</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
                                                                <label class="radio-inline">
																	<input type="radio" name="transportasi_umum" id="transportasi_umum1" value="1">Ya
																</label>
																<label class="radio-inline">
																	<input type="radio" name="transportasi_umum" id="transportasi_umum2" value="2">Tidak
																</label>
																
															</div>
															<!--							
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>
															-->															
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
																<label>Apakah pernah melakukan perjalanan ke luar kota/internasional (wilayah yang terjangkit/zona merah) ?</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
                                                                <label class="radio-inline">
																	<input type="radio" name="luar_kota" id="luar_kota1" value="1">Ya
																</label>
																<label class="radio-inline">
																	<input type="radio" name="luar_kota" id="luar_kota2" value="2">Tidak
																</label>
																
															</div>
															<!--							
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>
															-->															
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
																<label>Apakah anda mengikuti kegiatan yang melibatkan orang banyak ?</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
                                                                <label class="radio-inline">
																	<input type="radio" name="kegiatan_orang_banyak" id="kegiatan_orang_banyak1" value="1">Ya
																</label>
																<label class="radio-inline">
																	<input type="radio" name="kegiatan_orang_banyak" id="kegiatan_orang_banyak2" value="2">Tidak
																</label>
																
															</div>
															<!--							
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>
															-->															
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
																<label>Apakah memiliki riwayat kontak erat dengan orang yang dinyatakan ODP, PDP, atau Confirm COVID-19 ?</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
                                                                <label class="radio-inline">
																	<input type="radio" name="kontak_pasien" id="kontak_pasien1" value="1">Ya
																</label>
																<label class="radio-inline">
																	<input type="radio" name="kontak_pasien" id="kontak_pasien2" value="2">Tidak
																</label>
																
															</div>
															<!--							
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>
															-->															
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
																<label>Apakah pernah mengalami demam/batuk/pilek/sakit tenggorokan/sesak nafas dalam 14 hari terakhir ?</label>
															</div>
														</div>
													</div>
													<div class="row">
														<div class="form-group">
															<div class="col-lg-12">
                                                                <label class="radio-inline">
																	<input type="radio" name="sakit" id="sakit1" value="1">Ya
																</label>
																<label class="radio-inline">
																	<input type="radio" name="sakit" id="sakit2" value="2">Tidak
																</label>
																
															</div>
															<!--							
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>
															-->															
														</div>
													</div>
																										
													<div class="row">
														<div class="col-lg-12 text-right">
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
						<!-- filter bulan -->
                        <div class="form-group">
							<div class="row">
                                <div class="col-lg-4">
                                    <label>Bulan</label>
                                    <!--<select id="pilih_bulan_tanggal" class="form-control">-->
                                    <select class="form-control select2" id='bulan_tahun' name='bulan_tahun'  onchange="refresh_table_serverside()" style="width: 200px;">
                                        <option value='0'>Semua</option>	
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
                                        <option value='<?php echo substr($value,3,4).'-'.substr($value,0,2)?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,0,2))." ".substr($value,3,4)?></option>								

                                    <?php 
                                    }
                                    ?>
                                    </select>
                                </div>
							</div>
						</div>
						
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_cuti">
									<thead>
										<tr>
											<th class='text-center'>No</th>
											<th class='text-center'>NP</th>	
											<th class='text-center'>Nama</th>	
											<th class='text-center'>Tipe</th>			
											<th class='text-center'>Start Date</th>
											<th class='text-center'>End Date</th>
											<th class='text-center no-sort'>Lama</th>
											<th class='text-center'>Alasan</th>
											<th class='text-center'>Keterangan</th>
											<th class='text-center no-sort'>Status</th>				
											<!-- <th class='text-center no-sort'>Approval By SDM</th>	 -->	
											<th class='text-center no-sort'>Aksi</th>
											
										</tr>
									</thead>
									<tbody>
									
									</tbody>
								</table>
								<!-- /.table-responsive -->
							</div>						
						</div>
						
						<!-- Modal Status -->
						<div class="modal fade" id="modal_status" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_status">Status <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">		
										
											<table>
												<tr>
													<td>Np Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="status_np_karyawan"></a></td>
												</tr>
												<tr>
													<td>Nama Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="status_nama"></a></td>
												</tr>
												<tr>
													<td>Dibuat Tanggal</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="status_created_at"></a></td>
												</tr>
												<tr>
													<td>Dibuat Oleh</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="status_created_by"></a></td>
												</tr>
											</table>
											
											<br>
											
											<div class="alert alert-info">
												<strong><a id="status_approval_1_nama"></a></strong><br>
												<p id="status_approval_1_status"></p>
											</div>
											
											<div class="alert alert-info">
												<strong><a id="status_approval_2_nama"></a></strong><br>
												<p id="status_approval_2_status"></p>
											</div>
											
										</div>										
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
						
						
				
				<?php
					}
					
					if($akses["batal"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_batal" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_batal">Batal <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">		
										
											<table>
												<tr>
													<td>Np Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_np_karyawan"></a></td>
												</tr>
												<tr>
													<td>Nama Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_nama"></a></td>
												</tr>
												<tr>
													<td>Dibuat Tanggal</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_created_at"></a></td>
												</tr>
												<tr>
													<td>Dibuat Oleh</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_created_by"></a></td>
												</tr>
											</table>
											
											<br>
											
											<div class="alert alert-info">
												<strong><a id="batal_approval_1_nama"></a></strong><br>
												<p id="batal_approval_1_status"></p>
											</div>
											
											<div class="alert alert-info">
												<strong><a id="batal_approval_2_nama"></a></strong><br>
												<p id="batal_approval_2_status"></p>
											</div>
											
											<form role="form" action="<?php echo base_url(); ?>cuti/Permohonan_cuti/action_batal_cuti" id="formulir_tambah" method="post">	
												<div class="row">
													<div class="col-lg-12 text-right">
														<input type="hidden" name="batal_id" id="batal_id">
														<input type="submit" name="submit" value="Batalkan Cuti" class="btn btn-danger">
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
		
		  <script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		<script type="text/javascript">	
			$(document).ready(function() {
				$('.select2').select2();
				

				$('#start_date').datetimepicker({
					format: 'D-M-Y',
					<?php if($min){?>
					minDate : '<?php echo $min;?>'
					<?php } ?>
				});
				
				$('#end_date').datetimepicker({
					format: 'D-M-Y',
					<?php if($min){?>
					minDate : '<?php echo $min;?>'
					<?php } ?>
				});
				
								
							
				$("#start_date").on("dp.change", function (e) 
				{		

					var absence_type = document.getElementById("absence_type").value;
				
					if(absence_type!='2001|1010') //jika bukan cuti besar
					{	
						var oldDate = new Date(e.date);
						var newDate = new Date(e.date);
						var startDate = $('#start_date').val();
						newDate.setDate(oldDate.getDate());

						$('#end_date').data("DateTimePicker").minDate(startDate); 
						$('#end_date').val(startDate);
						
						getJumlah();
					}
					
				});
				
				
				 $("#end_date").on("dp.change", function (e) {					
					 getJumlah();						
				});
              
                
				$("#form_start_date").hide();
				$("#form_end_date").hide();
				$("#form_absence_type").hide();
				$("#form_alasan").hide();
				$("#form_keterangan").hide();
								
				$("#form_cuti_besar_pilih").hide();			
				
				$("#form_jumlah_bulan").hide();				
				$("#form_jumlah_hari").hide();
				
				$('#tabel_ess_cuti').DataTable().destroy();				
				table_serverside();
			});
			
			 function refresh_table_serverside() {
				$('#tabel_ess_cuti').DataTable().destroy();				
				table_serverside();
			}
			
			function table_serverside()
			{
				var table;
				var bulan_tahun = $('#bulan_tahun').val();
						//datatables
						table = $('#tabel_ess_cuti').DataTable({ 
							
							"iDisplayLength": 10,
							"language": {
								"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
								"sEmptyTable": "Tidak ada data di database",
								"emptyTable": "Tidak ada data di database"
							},
							
							"processing": true, //Feature control the processing indicator.
							"serverSide": true, //Feature control DataTables' server-side processing mode.
							"order": [], //Initial no order.

							// Load data for the table's content from an Ajax source
							"ajax": {
								"url": "<?php echo site_url("cuti/permohonan_cuti/tabel_ess_cuti/")?>" + bulan_tahun,
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

					};		

		</script>
		
		<script>
			function checkJumlahCuti(){
				var absence_type = document.getElementById("absence_type").value;
				
				if(absence_type=='2001|1010') //jika cuti besar
				{
					var np_karyawan = $('#np_karyawan').val();						
					var start_date = $("#start_date").val();						
					var cuti_besar_pilih = $("#cuti_besar_pilih").val();						
					var jumlah_hari = $('#jumlah_hari').val();
					var jumlah_bulan = $('#jumlah_bulan').val();
					
					var data_array = new Array();
						data_array[0] = np_karyawan;
						data_array[1] = start_date;
						data_array[2] = cuti_besar_pilih;
						data_array[3] = jumlah_hari;
						data_array[4] = jumlah_bulan;
												
						
					$.ajax({
					 type: "POST",
					 dataType: "html",
					 url: "<?php echo base_url('cuti/permohonan_cuti/ajax_checkJumlahCutiBesar');?>",
					 data: "data_array="+data_array,
						success: function(msg){
							if(msg){			
								if(msg=="kosong")
								{
									$('#end_date').val('');
									
									//tambahan bowo  20 01 2020, Bug cuti bend date bisa di klik lg, 7648-Tri Wibowo
									$('#start_date').val('');
								}else
								{
									$('#end_date').val(msg);
								}						
							}else
							{
								alert('Cuti Besar tidak mencukupi');
								$('#jumlah_hari').val('0');
								$('#jumlah_bulan').val('0');
								$('#end_date').val('');
								
								//tambahan bowo  20 01 2020, Bug cuti bend date bisa di klik lg, 7648-Tri Wibowo
								$('#start_date').val('');
							
							}							
						 }
					 }); 
				 
				}else //jika yang lain
				{
					var np_karyawan = $('#np_karyawan').val();
					var jumlah_hari = $('#jumlah_hari').val();
					var jumlah_bulan = $('#jumlah_bulan').val();
					var start_date = $('#start_date').val();
					var end_date = $('#end_date').val();
					
					var data_array = new Array();
						data_array[0] = absence_type;
						data_array[1] = np_karyawan;
						data_array[2] = jumlah_hari;
						data_array[3] = jumlah_bulan;
						data_array[4] = start_date;
						data_array[5] = end_date;
									
						
					$.ajax({
					 type: "POST",
					 dataType: "html",
					 url: "<?php echo base_url('cuti/permohonan_cuti/ajax_checkJumlahCuti');?>",
					 data: "data_array="+data_array,
						success: function(msg){
							if(msg == ''){				
								
							}else{							 
								alert(msg);
								$('#jumlah_bulan').val('0');
								$('#jumlah_hari').val('0');
								$('#end_date').val('');
								
								//tambahan bowo  20 01 2020, Bug cuti bend date bisa di klik lg, 7648-Tri Wibowo
								$('#start_date').val('');
							}													  
						 }
					 });       
					}
				
			} 
		</script>
		
		<script>
			function getCutiBesarPilih(){
				
				var cuti_besar_pilih = document.getElementById("cuti_besar_pilih").value;
				
				if(cuti_besar_pilih=='bulan')
				{
					document.getElementById('jumlah_bulan').removeAttribute('readonly');
					document.getElementById("jumlah_hari").readOnly = true;
					$("#form_jumlah_bulan").show();
					$("#form_jumlah_hari").hide();
				}else
				if(cuti_besar_pilih=='hari')
				{					
					document.getElementById("jumlah_bulan").readOnly = true;
					document.getElementById('jumlah_hari').removeAttribute('readonly');
					$("#form_jumlah_bulan").hide();
					$("#form_jumlah_hari").show();
				}else
				{
					document.getElementById("jumlah_bulan").readOnly = true;
					document.getElementById("jumlah_hari").readOnly = true;
					$("#form_jumlah_bulan").hide();
					$("#form_jumlah_hari").hide();
				}
				
				$('#jumlah_hari').val('0');
				$('#jumlah_bulan').val('0');
				$('#start_date').val('');			
				$('#end_date').val('');
			
						
			} 
		</script>
		
		
		<script>
			function getJenisCuti(){
				var jenis_cuti = document.getElementById("absence_type").value;
				
				
				
				if(jenis_cuti=='')
				{					
					$("#form_start_date").hide();				
					$("#form_end_date").hide();
					$("#form_jumlah_bulan").hide();
					$("#form_jumlah_hari").hide();
					
					$("#form_alasan").hide();
					$("#form_keterangan").hide();
					
					$("#form_cuti_besar_pilih").hide();
					
					document.getElementById("jumlah_hari").readOnly = true;
					document.getElementById("jumlah_bulan").readOnly = true;
					
				}else
				if(jenis_cuti=='2001|1010') //jika cuti besar
				{		
					$("#form_start_date").show();				
					$("#form_end_date").show();
					$("#form_jumlah_bulan").hide();
					$("#form_jumlah_hari").hide();
					
					$("#form_alasan").show();
					$("#form_keterangan").show();
					
					$("#form_cuti_besar_pilih").show();
										
					document.getElementById("jumlah_hari").readOnly = true;
					document.getElementById("jumlah_bulan").readOnly = true;
					document.getElementById("end_date").readOnly = true;
					
					
				}else
				{
					$("#form_start_date").show();				
					$("#form_end_date").show();
					$("#form_jumlah_bulan").hide();
					$("#form_jumlah_hari").show();
					
					$("#form_alasan").show();
					$("#form_keterangan").show();
					
					$("#form_cuti_besar_pilih").hide();
					
					document.getElementById("jumlah_hari").readOnly = true;
					document.getElementById("jumlah_bulan").readOnly = true;
					document.getElementById("jumlah_hari").readOnly = true;
					
					document.getElementById('end_date').removeAttribute('readonly');					
					document.getElementById('cuti_besar_pilih').removeAttribute('required');
				}
				
				getJumlah();
				
				$('#start_date').val('');
				$('#end_date').val('');
				
				$('#jumlah_hari').val('0');
				$('#jumlah_bulan').val('0');

				
			} 
		</script>
		
		<script>
			function getEndDate(){
				getJumlah();		
				var start_date = $('#start_date').val();			
				document.getElementById('end_date').setAttribute("min", start_date);			
			} 
		</script>
		
		<script>
			function getJumlah(){	
				
				var jenis_cuti = document.getElementById("absence_type").value;
				if(jenis_cuti!='2001|1010')
				{				
					//menghitung jumlah hari
					if ( ($("#start_date").val() != "") && ($("#end_date").val() != "")) {
						var oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
						//var firstDate = new Date($("#start_date").val());
						//var secondDate = new Date($("#end_date").val());
																	
						var start_date 	 	= $("#start_date").val();
						var pisah_start		= start_date.split("-");					
						var tanggal_awal 	= pisah_start[2]+'/'+pisah_start[1]+'/'+pisah_start[0];
						var firstDate 		= new Date(tanggal_awal);
						
						var end_date 	 	= $("#end_date").val();
						var pisah_end 		= end_date.split("-");					
						var tanggal_akhir 	= pisah_end[2]+'/'+pisah_end[1]+'/'+pisah_end[0];
						var secondDate 		= new Date(tanggal_akhir);
							
						
				
						
						var diffDays = Math.round(Math.round((secondDate.getTime() - firstDate.getTime()) / (oneDay)));
						$("#jumlah_hari").val(diffDays+1);
					}else
					{
						$("#end_date").val('');
						$("#jumlah_hari").val('0');
						
						//tambahan bowo  20 01 2020, Bug cuti bend date bisa di klik lg, 7648-Tri Wibowo
						$('#start_date').val('');
					}
			
					checkJumlahCuti();	
				}
				
			} 
		</script>
		
		
		
		<script>
			function getNama(){
				var np_karyawan = $('#np_karyawan').val();
				
				$.ajax({
				 type: "POST",
				 dataType: "html",
				 url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getNama');?>",
				 data: "vnp_karyawan="+np_karyawan,
					success: function(msg){
						if(msg == ''){
							alert ('Silahkan isi No. Pokok Dengan Benar.');
							$('#np_karyawan').val('');
							$('#nama').text('');
							$("#form_absence_type").hide();							
						}
						else{
							$('#nama').text(msg);
							$("#form_absence_type").show();							
							getAtasanCuti();
						}													  
					 }
				 });       
			} 
		</script>
		
		<script>
		function getAtasanCuti(){
			var np_karyawan = $('#np_karyawan').val();
			
			$.ajax({
             type: "POST",
             dataType: "JSON",
             url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getAtasanCuti');?>",
             data: "vnp_karyawan="+np_karyawan,
				success: function(msg){
					if(msg.status == false){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
						$('#approval_1').val('');
						$('#approval_2').val('');
					}else{							 
						$('#approval_1').val(msg.np_atasan_1);
						$('#approval_2').val(msg.np_atasan_2);

						if(msg.np_atasan_1!=""){
							getNamaAtasan1();
						}
						else{
							$('#approval_1_input').val('');
							$('#approval_1_input_jabatan').val('');
						}
						
						if(msg.np_atasan_2!=""){
							getNamaAtasan2();
						}
						else{
							$('#approval_2_input').val('');
							$('#approval_2_input_jabatan').val('');
						}
					}													  
				 }
			 });       
		} 		
 
		</script>
		
		<script>
			function listNp(){			
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getListNp');?>",
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
		function getNamaAtasan1(){
			var np_karyawan = $('#approval_1').val();
			
			$.ajax({
             type: "POST",
             dataType: "JSON",
             url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getNama_approval');?>",
             data: "vnp_karyawan="+np_karyawan,
				success: function(msg){
					if(msg.status == false){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
						$('#approval_1').val('');
						$('#approval_1_input').val('');
						$('#approval_1_input_jabatan').val('');
					}else{							 
						$('#approval_1_input').val(msg.data.nama);
						$('#approval_1_input_jabatan').val(msg.data.jabatan);
					}													  
				 }
			 });       
		} 		
		
		function getNamaAtasan2(){
			var np_karyawan = $('#approval_2').val();
			
			$.ajax({
             type: "POST",
             dataType: "JSON",
             url: "<?php echo base_url('cuti/permohonan_cuti/ajax_getNama_approval');?>",
             data: "vnp_karyawan="+np_karyawan,
				success: function(msg){
					if(msg.status == false){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
						$('#approval_2').val('');
						$('#approval_2_input').val('');
						$('#approval_2_input_jabatan').val('');
					}else{
						$('#approval_2_input').val(msg.data.nama);
						$('#approval_2_input_jabatan').val(msg.data.jabatan);
					}													  
				 }
			 });       
		} 
		</script>
		
		<script>
			$(document).on( "click", '.status_button',function(e) {
				var status_np_karyawan = $(this).data('np-karyawan');
				var status_nama = $(this).data('nama');
				var status_created_at = $(this).data('created-at');
				var status_created_by = $(this).data('created-by');
				var status_approval_1_nama = $(this).data('approval-1-nama');
				var status_approval_1_status = $(this).data('approval-1-status');
				var status_approval_2_nama = $(this).data('approval-2-nama');
				var status_approval_2_status = $(this).data('approval-2-status');
					
				$("#status_np_karyawan").text(status_np_karyawan);					
				$("#status_nama").text(status_nama);
				$("#status_created_at").text(status_created_at);	
				$("#status_created_by").text(status_created_by);
				$("#status_approval_1_nama").text(status_approval_1_nama);				
				$("#status_approval_1_status").text(status_approval_1_status);
				$("#status_approval_2_nama").text(status_approval_2_nama);
				$("#status_approval_2_status").text(status_approval_2_status);
				
				
				
			});
		</script>
		
		<script>
			$(document).on( "click", '.batal_button',function(e) {
				var batal_id = $(this).data('id');
				var batal_np_karyawan = $(this).data('np-karyawan');
				var batal_nama = $(this).data('nama');
				var batal_created_at = $(this).data('created-at');
				var batal_created_by = $(this).data('created-by');
				var batal_approval_1_nama = $(this).data('approval-1-nama');
				var batal_approval_1_status = $(this).data('approval-1-status');
				var batal_approval_2_nama = $(this).data('approval-2-nama');
				var batal_approval_2_status = $(this).data('approval-2-status');
					
				$("#batal_id").val(batal_id);		
				$("#batal_np_karyawan").text(batal_np_karyawan);					
				$("#batal_nama").text(batal_nama);
				$("#batal_created_at").text(batal_created_at);	
				$("#batal_created_by").text(batal_created_by);
				$("#batal_approval_1_nama").text(batal_approval_1_nama);				
				$("#batal_approval_1_status").text(batal_approval_1_status);
				$("#batal_approval_2_nama").text(batal_approval_2_nama);
				$("#batal_approval_2_status").text(batal_approval_2_status);
				
				
				
			});
		</script>
		
		