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
											
												<form role="form" action="<?php echo base_url(); ?>kehadiran/perencanaan_jadwal_kerja/action_insert_perencanaan_jadwal_kerja" id="formulir_tambah" method="post">	
																									
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Karyawan</label>
															</div>
															<div class="col-lg-7">
																
																<select class="form-control select2" id='insert_np_karyawan' multiple='multiple' name='insert_np_karyawan[]'  style="width: 100%;" required>
																<option value=''></option>	
																	<?php 
																	foreach ($array_daftar_karyawan->result_array() as $value) {		
																	?>
																		<option value='<?php echo $value['no_pokok']?>'><?php echo $value['no_pokok']." ".$value['nama']?></option>								
																		
																	<?php 
																	}
																	?>
																</select>
																 
																 
															</div>															
															<!--
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>	
															-->
														</div>
													</div>
													
													<!--
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label></label>
															</div>
															<div class="col-lg-7">
													-->
																 <input type='hidden' class="form-control" name="insert_nama" id="insert_nama" readonly required>
													<!--		
															</div>		
														</div>
													</div>
													-->
													
													<?php
														$bulan_lalu = $data_tanggal	= date('Y-m-d',strtotime('-1 months',strtotime(date('Y-m-d'))));
														$sudah_cutoff = sudah_cutoff($bulan_lalu);
														
														if($sudah_cutoff)
														{										
															$min = date('Y-m')."-01";
														}else
														{
															$min = '';
														}
														
													?>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Tanggal Awal</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control" name="insert_date_awal" id="insert_date_awal" autocomplete="off" required>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Tanggal Akhir</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control" name="insert_date_akhir" id="insert_date_akhir" autocomplete="off" required>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Jadwal Kerja</label>
															</div>
															<div class="col-lg-7">											
																<select class="form-control" id='insert_dws_id' name='insert_dws_id'  style="width: 200px;" required>
																<option value=''></option>	
																	<?php 
																	foreach ($array_jadwal_kerja->result_array() as $value) {		
																	?>
																		<option value='<?php echo $value['id']?>'><?php echo $value['description']?></option>								
																		
																	<?php 
																	}
																	?>
																</select>
															</div>
														</div>
													</div>
													
																		
													<div class="row">
														<div class="col-lg-9 text-right">
															<input type='hidden' id='insert_tampil_bulan_tahun' name='insert_tampil_bulan_tahun'>
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
						
						<!-- Modal NP -->
						<div class="modal fade" id="modal_np" tabindex="-1" role="dialog" aria-labelledby="label_modal_np" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_np">Daftar List NP <?php echo $judul;?></h4>
										</div>
										<div class="modal-body" align='center'>		
											<textarea name='list_np' id='list_np' rows="10" cols="50" readonly></textarea>
										</div>										
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
				<?php
					}
					
					if($this->akses["lihat"]){
				?>				
				
					
					<p id="demo"></p>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2 col-md-6 col-sm-6" style="padding-left: 0px">
									<label>Bulan</label>
									<!--<select id="pilih_bulan_tanggal" class="form-control">-->
									<select class="form-control" id='bulan_tahun' name='bulan_tahun'  onchange="refresh_table_serverside()" style="width: 200px;">
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
								<div class="col-lg-1 col-md-6 col-sm-6"></div>
								<div class="col-lg-4 col-md-6 col-sm-6">
									<label>Jadwal Kerja</label>
									<!--<select id="pilih_bulan_tanggal" class="form-control">-->
									<select class="form-control" id='mst_jadwal' name='mst_jadwal' onchange="refresh_table_serverside()" style="width: 200px;">
										<option value='0'></option>	
										<?php foreach ($array_mst_jadwal as $jadwal) { ?>
										<option value='<?php echo $jadwal['dws'].$jadwal['dws_variant'] ?>'><?php echo $jadwal['description'] ?></option>
										<?php } ?>
									</select>
								</div>	
								<form method="post" target="_blank" action="<?php echo base_url('kehadiran/perencanaan_jadwal_kerja/cetak')?>">
									<div class="col-lg-2 col-md-6 col-sm-6" style="padding-left: 0px">
										<label>Tanggal Awal</label>
										<input type="date" class="form-control" name="tgl_awal" id="tgl_awal" onchange="get_tgl_awal()" required>
									</div>				
									<div class="col-lg-2 col-md-6 col-sm-6" style="padding-left: 0px">
										<label>Tanggal Akhir</label>
										<input type="date" class="form-control" name="tgl_akhir" id="tgl_akhir" onchange="get_tgl_akhir()" required>
									</div>		
									<div class="col-lg-1 col-md-6 col-sm-6" style="padding-left: 0px;padding-right: 0px; text-align: right;">
										<label><font color="white">Cetak</font></label><br>
										<button type="button" onClick="otoritas()" class="btn btn-success"><i class="fa fa-print"></i> Cetak</button>
									</div>				
									<!--begin: Modal Inactive -->
							      	<div class="modal fade" id="show_otoritas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								        <div class="modal-dialog modal-md" role="document">
								          	<div class="modal-content">
								            	<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title" id="label_modal_ubah">Pilih Otoritas</h4>
												</div>
												<div class="modal-body">
													<select multiple="multiple" class="form-control select2" name='np_karyawan[]' id="multi_select" style="width: 100%;" required>
														<?php foreach ($array_daftar_karyawan->result_array() as $val) { ?>
														<option value='<?php echo $val['no_pokok']?>'><?php echo $val['no_pokok']." ".$val['nama']?></option>
														<?php } ?>
													</select>
												</div>
												<div class="modal-footer">
													<button type="submit" class="btn btn-success">Cetak</button>
													<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
												</div>
								          	</div>
								        </div>
							      	</div>
							      	<!--end: Modal Inactive -->
								</form>			
							</div>
						</div>
													
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_perencanaan_jadwal_kerja">
									<thead>
										<tr>
											<th class='text-center'>No</th>
											<th class='text-center'>NP</th>	
											<th class='text-center'>NAMA</th>			
											<th class='text-center'>Tertanggal</th>
											<th class='text-center'>Jadwal Kerja</th>
											<!--<th class='text-center'>Variant</th>-->
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
					
					if($akses["batal"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_batal" tabindex="-1" role="dialog" aria-labelledby="label_modal_batal" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form method="post" action="<?php echo base_url(); ?>kehadiran/perencanaan_jadwal_kerja/action_batal_perencanaan_jadwal_kerja">
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
													<td>Tanggal</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_date"></a></td>
												</tr>
												<tr>
													<td>Jadwal Kerja</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_dws"></a></td>
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
												<input type='hidden' id='batal_tampil_bulan_tahun' name='batal_tampil_bulan_tahun'>
												<button name='submit' type="submit" value='Batalkan Permohonan' class="btn btn-warning">Batalkan Perencanaan Jadwal Kerja</button>											
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
            $('#insert_np_karyawan').select2();
            $('#multi_select').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
            $(function () {
                $('#insert_date_awal').datetimepicker({
                    format: 'D-M-Y',
					<?php if($min){?>
						minDate : '<?php echo $min;?>'
					<?php } ?>
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
				$('#tabel_perencanaan_jadwal_kerja').DataTable().destroy();				
				table_serverside();
			}

			function otoritas() {
				$("#show_otoritas").modal('show');
			}
		</script>
		
		<script>
		function table_serverside()
		{
			var table;
			var bulan_tahun = $('#bulan_tahun').val();
			var mst_jadwal = $('#mst_jadwal').val();
			
			<?php
			if($akses["tambah"]){
			?>				
				document.getElementById("insert_tampil_bulan_tahun").value = bulan_tahun;
			<?php
			}
			if($akses["batal"]){
			?>
				document.getElementById("batal_tampil_bulan_tahun").value = bulan_tahun;
			<?php } ?>
			
				//datatables
				table = $('#tabel_perencanaan_jadwal_kerja').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					//"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("kehadiran/perencanaan_jadwal_kerja/tabel_perencanaan_jadwal_kerja/")?>"+mst_jadwal+"/"+bulan_tahun,
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
				function getNama(){
				var insert_np_karyawan = $('#insert_np_karyawan').val();
				
				$.ajax({
				 type: "POST",
				 dataType: "html",
				 url: "<?php echo base_url('kehadiran/perencanaan_jadwal_kerja/ajax_getNama');?>",
				 data: "vnp_karyawan="+insert_np_karyawan,
					success: function(msg){
						if(msg == ''){
							alert ('Silahkan isi No. Pokok Dengan Benar.');
							$('#insert_np_karyawan').val('');
							$('#insert_nama').val('');
						}else{							 
							$('#insert_nama').val(msg);
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
					var date = $(this).data('date');
					var dws = $(this).data('dws');
					var dws_variant = $(this).data('dws-variant');
																	
					$("#batal_id").val(id);					
					$("#batal_np_karyawan").text(np_karyawan);
					$("#batal_nama").text(nama);
					$("#batal_date").text(date);
					$("#batal_dws").text(dws);
					$("#batal_dws_variant").text(variant);
											
				});
			</script>
		<?php } ?>
	
	