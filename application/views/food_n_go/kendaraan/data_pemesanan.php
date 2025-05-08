		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
        <link rel="stylesheet" href="<?= base_url('asset/rateYo/2.3.2/jquery.rateyo.min.css')?>">
		
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
                
                <div class="alert alert-dismissable" id="alert-message" style="display: none;"></div>
                
			<?php
					if(!empty($this->session->flashdata('success'))){
				?>
						<div class="alert alert-success alert-dismissable" id="alert-success">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $this->session->flashdata('success');?>
						</div>
				<?php
					}
					if(!empty($this->session->flashdata('warning'))){
				?>
						<div class="alert alert-danger alert-dismissable" id="alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $this->session->flashdata('warning');?>
						</div>
				<?php
					}
					if(@$akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					if($akses["tambah"]){
				?>
                    
                    <!-- <div class="alert alert-info alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>					
                        Dalam rangka <i>physical distancing</i> selama masa pandemi Covid-19, maka kapasitas kendaraan tersedia untuk Innova 4 orang, Hiace 8 orang, Elf 11 orang, Bus 20 orang.
                    </div> -->
                
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a href="<?php echo base_url('food_n_go/kendaraan/data_pemesanan/input_data_pemesanan') ?>">Tambah <?php echo $judul;?></a>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <input type='hidden' id='insert_tampil_bulan_tahun' name='insert_tampil_bulan_tahun'>
						<!--<div class="row">						
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
									</h4>
								</div>
								<div id="collapseOne" class="panel-collapse collapse">
									<div class="panel-body">
											
										<form role="form" action="<?php echo base_url(); ?>food_n_go/kendaraan/data_pemesanan/action_insert_data_pemesanan" id="formulir_tambah" method="post">	
												
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Karyawan</label>
													</div>
													<div class="col-lg-7">
														<select class="form-control select2" id='insert_np_karyawan' name='insert_np_karyawan'  onChange="getNama()" style="width: 100%;" required>
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
												</div>
											</div>
												
											<input type='hidden' class="form-control" name="insert_nama" id="insert_nama" readonly required>
											
											<?php
												$bulan_lalu = $data_tanggal	= date('Y-m-d',strtotime('-1 months',strtotime(date('Y-m-d'))));
												$sudah_cutoff = sudah_cutoff($bulan_lalu);
												
												if($sudah_cutoff){										
													$min = date('Y-m')."-01";
												}
												else{
													$min = '';
												}
												
											?>
                                            
                                            <div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Telp/HP Pemesan</label>
													</div>
													<div class="col-lg-7">											
														<input type="text" class="form-control" name='no_hp' style="width: 100%;" placeholder="Harus diisi" title="Sertakan No. HP yang bisa dihubungi" required>
													</div>
												</div>
											</div>
                                            
                                            <div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Tujuan</label>
													</div>
													<div class="col-lg-7">											
														<input type="text" class="form-control" name='tujuan' style="width: 100%;" placeholder="Harus diisi" required>
													</div>
												</div>
											</div>
                                            
                                            <div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Lokasi Penjemputan</label>
													</div>
													<div class="col-lg-7">											
														<input type="text" class="form-control" name='lokasi_jemput' style="width: 100%;" placeholder="Harus diisi" required>
													</div>
												</div>
											</div>
                                            
                                            <div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Jumlah Penumpang</label>
													</div>
													<div class="col-lg-7">											
														<input type="number" class="form-control" name='jumlah_penumpang' style="width: 100%;" placeholder="Harus diisi" required>
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Berangkat</label>
													</div>
													<div class="col-lg-4">
														 <input type="text" class="form-control" name="tanggal_berangkat" id="insert_tapping_fix_1_date" placeholder="Harus diisi" required>
													</div>
													<div class="col-lg-3">
														 <input type="text" class="form-control datetimepicker5" name="jam" id="insert_tapping_fix_1_time" placeholder="Harus diisi" required>
													</div>
												</div>
											</div>
											
											<div class="row">
												<div class="form-group">
													<div class="col-lg-2">
														<label>Keterangan</label>
													</div>
													<div class="col-lg-7">											
														<textarea class="form-control" name='keterangan' style="width: 100%;" placeholder="Optional"></textarea>
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
						</div>-->
						
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
								<label>Bulan</label>
								<!--<select id="pilih_bulan_tanggal" class="form-control">-->
								<select class="form-control" id='bulan_tahun' name='bulan_tahun' onchange="refresh_table_serverside()" style="width: 200px;">
									<option value=''></option>	
								<?php 
								$tampil_bulan_tahun=date("m-Y");
								foreach ($array_tahun_bulan as $value) {
									
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
                                <input type="hidden" name="bulan" value="" id="get_month" />
                                <input type="hidden" name="bulan" value="" id="get_month_per_unit" />
							</div>
						</div>
                        
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data_pemesanan">
									<thead>
										<tr>
											<th class='text-center' style="width: 5%;">No</th>
											<th class='text-center' style="width: 10%;">No. Pemesanan</th>
											<th class='text-center'>Nama</th>
											<th class='text-center'>Asal</th>
											<th class='text-center'>Tujuan</th>
											<th class='text-center'>Berangkat</th>
											<th class='text-center'>Penumpang</th>
											<!-- <th class='text-center'>Kendaraan</th> -->
											<?php if($this->session->userdata('grup')!='5'):?>
											<th class='text-center'>Approver</th>
											<?php endif?>
											<th class='text-center'>Status</th>
											<th class='text-center no-sort'>Aksi</th>
										</tr>
									</thead>
									<tbody></tbody>
								</table>
							</div>						
						</div>
				<?php
					}
					
					if(@$akses["ubah"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									
									<form enctype="multipart/form-data" method="POST" accept-charset="utf-8" action="<?php echo base_url('food_n_go/kendaraan/data_pemesanan/action_update_data_pemesanan');?>" method="post">
									 
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<div class="form-group row">
												<div class="col-lg-2">
													<label>NP</label>
												</div>
												<div class="col-lg-10">
													<input class="form-control" name="edit_np_karyawan" id="edit_np_karyawan" readonly>
												</div>	
											</div>
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Nama</label>
												</div>
												<div class="col-lg-10">
													<input class="form-control" name="edit_nama" id="edit_nama" readonly>
												</div>											
											</div>
                                            
											<div class="form-group row">
												<div class="col-lg-2">
													<label>No. HP</label>
												</div>
												<div class="col-lg-10">
													<input class="form-control" name="edit_no_hp" id="edit_no_hp">
												</div>											
											</div>
                                            
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Tujuan</label>
												</div>
												<div class="col-lg-10">
													<input type="text" class="form-control" name="edit_tujuan" id="edit_tujuan">
												</div>											
											</div>
                                            
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Lokasi Penjemputan</label>
												</div>
												<div class="col-lg-10">
													<input type="text" class="form-control" name="edit_lokasi_jemput" id="edit_lokasi_jemput">
												</div>											
											</div>
                                            
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Jumlah Penumpang</label>
												</div>
												<div class="col-lg-10">
													<input type="number" class="form-control" name="edit_jumlah_penumpang" id="edit_jumlah_penumpang">
												</div>											
											</div>
                                            
											<div class="form-group row">										
												<div class="col-lg-2">
													<label>Berangkat</label>
												</div>
												<div class='col-lg-4'>
													<input type="text" class="form-control" name="edit_tanggal" id="edit_tapping_1_date"> 
													<small class="form-text text-muted">dd-mm-yyyy</small>
													
											    </div>
												<div class='col-lg-4'>
													<input type="text" class="form-control datetimepicker5" name="edit_jam" id="edit_tapping_1_time" >
													 <small class="form-text text-muted">hh:mm</small>
												</div>	
											</div>
											
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Approval</label>
												</div>
												<div class="col-lg-10">
													<select style='width:100%;' class="form-control select2 edit_approval" id="edit_approval" name="edit_approval" required></select>
													
												</div>											                     
											</div>
											
											<div class="form-group row">										
												<div class="col-lg-2">
													<label>Keterangan</label>
												</div>												
												<div class='col-lg-10'>
                                                    <textarea class="form-control" name="edit_keterangan" id="edit_keterangan" required></textarea>													
												</div>											
											</div>
											
											<div class="modal-footer">.
												<input type='hidden' id='edit_id' name='edit_id'>
												<input type='hidden' id='edit_tampil_bulan_tahun' name='edit_tampil_bulan_tahun'>
												<button name='submit' type="submit" value='submit' class="btn btn-primary">Simpan</button>
												<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
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
					
					if(@$akses["ubah kode unit"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_ubah_kode_unit" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah_kode_unit" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form method="post" action="<?php echo base_url(); ?>food_n_go/kendaraan/data_pemesanan/action_update_kode_unit">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Ubah Kode Unit<?php echo $judul;?></h4>
										</div>
										<div class="modal-body">		
										
											<div class="form-group row">
												<div class="col-lg-2">
													<label>NP</label>
												</div>
												<div class="col-lg-10">
													<input class="form-control" name="kd_np_karyawan" id="kd_np_karyawan" readonly>								
												</div>	
											</div>
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Nama</label>
												</div>
												<div class="col-lg-10">
													<input class="form-control" name="kd_nama" id="kd_nama" readonly>							
												</div>											
											</div>
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Tertanggal</label>
												</div>
												<div class="col-lg-10">
													<input class="form-control tanggal_mdy" name="kd_dws_tanggal" id="kd_dws_tanggal" readonly>
												</div>											                  
											</div>
											<div class="form-group row">
												<div class="col-lg-2">
													<label>DWS</label>
												</div>
												<div class="col-lg-10">
													<input class="form-control" name="kd_dws_name" id="kd_dws_name" readonly>
												</div>
											</div>
											
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Kode Unit</label>
												</div>
												<div class="col-lg-10">
													<select class="form-control" id="kd_kode_unit" name='kd_kode_unit' style="width: 100%;" required>
															<?php foreach ($array_daftar_unit->result_array() as $val) { ?>
															<option value='<?php echo $val['kode_unit']?>'><?php echo $val['kode_unit']." ".$val['nama_unit']?></option>
															<?php } ?>
													</select> 							
												</div>                     
											</div>
											
											<div class="modal-footer">.
												<input type='hidden' id='kd_id' name='kd_id'>
												<input type='hidden' id='kd_tampil_bulan_tahun' name='kd_tampil_bulan_tahun'>
												<button name='submit' type="submit" value='submit' class="btn btn-primary">Simpan</button>
												<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
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
                <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_detail" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" id="modal-content-detail"></div>
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
            var table;
			//alert("Mulai tertanggal 11 April 2019 diberlakukan approval untuk perubahan kehadiran karyawan.");
		
		
			$('#multi_select').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
			$('#multi_select_per_unit').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
			$(document).ready(function() {
				
				//lempar ke modal cetak
				document.getElementById('get_month').value = $('#bulan_tahun').val();
				document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
											
                $('.datetimepicker5').datetimepicker({
                    format: 'HH:mm'
					}).on('dp.change', function (event) {
				  //wfh();
				});
                
                $('.tanggal_mdy').datetimepicker({
                    format: 'D-MM-Y'
                }).on('dp.change', function (event) {
				  wfh();
				});
                
                $(function () {
                     $('#insert_dws_tanggal').datetimepicker({
                        format: 'D-MM-Y',
						<?php if(@$minDate){?>
						minDate : '<?php echo $minDate;?>',
						<?php } 
						if(@$maxDate){?>
						maxDate : '<?php echo $maxDate;?>',
						<?php } ?>
                    });
                    
                   $("#insert_dws_tanggal").on("dp.change", function (e) {
                        var newDate = $('#insert_dws_tanggal').val();
						var datebaru	 = new Date(newDate.split("-").reverse().join("-"));
                        var datebaru_plus = new Date(newDate.split("-").reverse().join("-"));
						var datebaru_minus = new Date(newDate.split("-").reverse().join("-"));
                        
						//Ketika tanggal 1 dia jadi 0 kalo dikurangi 1
						//var tambah = (datebaru.getDate()+1)+'-'+(datebaru.getUTCMonth()+1)+'-'+datebaru.getUTCFullYear();
                        //var kurang = (datebaru.getDate()-1)+'-'+(datebaru.getUTCMonth()+1)+'-'+datebaru.getUTCFullYear();
						
						$('#insert_tapping_fix_1_date').val('');
						$('#insert_tapping_fix_2_date').val('');
						
						var newdate = datebaru;
						newdate.setDate(newdate.getDate());						
						var dd = newdate.getDate();
						var mm = newdate.getMonth() + 1;
						var y = newdate.getFullYear();
						var asli = dd + '-' + mm + '-' + y;
						
						var start_newdate = datebaru_plus;
						start_newdate.setDate(start_newdate.getDate() + 1);						
						var start_dd = start_newdate.getDate();
						var start_mm = start_newdate.getMonth() + 1;
						var start_y = start_newdate.getFullYear();
						var tambah = start_dd + '-' + start_mm + '-' + start_y;
						
						var end_newdate = datebaru_minus;
						end_newdate.setDate(end_newdate.getDate() - 1);						
						var end_dd = end_newdate.getDate();
						var end_mm = end_newdate.getMonth() + 1;
						var end_y = end_newdate.getFullYear();
						var kurang = end_dd + '-' + end_mm + '-' + end_y;
						
                        console.log(asli);
                        console.log("plus "+tambah);
                        console.log("min "+kurang);
                        
						
                /*        
                        $('#insert_tapping_fix_1_date').data("DateTimePicker").maxDate(tambah);
						$('#insert_tapping_fix_1_date').data("DateTimePicker").minDate(kurang);
						
                     
                        $('#insert_tapping_fix_2_date').data("DateTimePicker").maxDate(tambah);
						   $('#insert_tapping_fix_2_date').data("DateTimePicker").minDate(kurang);
				*/		
						$('#insert_tapping_fix_1_date').val(asli);
						$('#insert_tapping_fix_2_date').val(asli);
						
						
                    });
					
					$('#insert_tapping_fix_1_date').datetimepicker({
                        format: 'D-MM-Y',
						<?php  
						if(@$maxDate){?>
						maxDate : '<?php echo $maxDate;?>',
						<?php } ?>
                    });
					
					$('#insert_tapping_fix_2_date').datetimepicker({
                        format: 'D-MM-Y',
						<?php if(@$maxDate){?>
						maxDate : '<?php echo $maxDate;?>',
						<?php } ?>
                    });
                    
                    $('#insert_tapping_fix_2_date').datetimepicker({
                        format: 'D-MM-Y'
                    });
                    
                    /*$("#insert_tapping_fix_1_date").on("dp.change", function (e) {
                       // var oldDate = new Date(e.date);
                        var newDate =$('#insert_tapping_fix_1_date').val();
                      //  newDate.setDate(oldDate.getDate());
						$('#insert_tapping_fix_2_date').val(newDate);
                        $('#insert_tapping_fix_2_date').data("DateTimePicker").minDate(newDate);
                    });*/

                });
                
                $(function () {
                    $('#edit_tapping_1_date').datetimepicker({
                        format: 'DD-MM-Y',
						<?php if(@$minDate){?>
						minDate : '<?php echo $minDate;?>',
						<?php } 
						if(@$maxDate){?>
						maxDate : '<?php echo $maxDate;?>',
						<?php } ?>
                    });
                    
                    $('#edit_tapping_2_date').datetimepicker({
                        format: 'DD-MM-Y',
						<?php if(@$maxDate){?>
						maxDate : '<?php echo $maxDate;?>',
						<?php } ?>
                    });
                    
                    /*$("#edit_tapping_1_date").on("dp.change", function (e) {
                       // var oldDate = new Date(e.date);
                        var newDate = $('#edit_tapping_1_date').val();
                       // newDate.setDate(oldDate.getDate());
						$('#edit_tapping_2_date').val(newDate);
                        $('#edit_tapping_2_date').data("DateTimePicker").minDate(newDate);          
                    });*/

                });
                
                $('.select2').select2();
				$('#tabel_data_pemesanan').DataTable().destroy();									
				table_serverside();
			});		
			
			function refresh_table_serverside() {
				document.getElementById('get_month').value = $('#bulan_tahun').val();
				document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
				$('#tabel_data_pemesanan').DataTable().destroy();				
				table_serverside();
			}
			
			function refresh_bulan_tahun() {
				$('#tabel_data_pemesanan').DataTable().destroy();	
				table_serverside();
			}
			function otoritas() {
				$("#show_otoritas").modal('show');
			}
			function otoritas_per_unit() {
				$("#show_otoritas_per_unit").modal('show');
			}
		</script>
		
		<script>
		function table_serverside()
		{
			
			var bulan_tahun = $('#bulan_tahun').val();

			<?php
			if(@$akses["tambah"]){
			?>
				
				document.getElementById("insert_tampil_bulan_tahun").value = bulan_tahun;
			<?php
			}
			if(@$akses["ubah"]){
			?>
				document.getElementById("edit_tampil_bulan_tahun").value = bulan_tahun;
			<?php
			}
			if(@$akses["ubah kode unit"]){
			?>
				document.getElementById("kd_tampil_bulan_tahun").value = bulan_tahun;
			<?php } ?>
			
				//datatables
				table = $('#tabel_data_pemesanan').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"ordering": false, //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("food_n_go/kendaraan/data_pemesanan/tabel_data_pemesanan/")?>"+bulan_tahun,
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
			if(@$akses["tambah"]){
		?>
			<script>
				function listNp(){			
				$.ajax({
				 type: "POST",
				 dataType: "html",
				 url: "<?php echo base_url('food_n_go/kendaraan/data_pemesanan/ajax_getListNp');?>",             
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
				 url: "<?php echo base_url('food_n_go/kendaraan/data_pemesanan/ajax_getNama');?>",
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
					var insert_tapping_fix_1_date = $('#insert_tapping_fix_1_date').val();			
					document.getElementById('insert_tapping_fix_2_date').setAttribute("min", insert_tapping_fix_1_date);			
				} 
			</script>		
		<?php } 
		if(@$akses["ubah"]){
		?>		
			<script>
				$(document).on( "click", '.edit_button',function(e) {
					var id = $(this).data('id');
					var nama = $(this).data('nama');
					var np_karyawan = $(this).data('np-karyawan');
					var no_hp = $(this).data('no-hp');
					var tanggal = $(this).data('tanggal');
					var jam = $(this).data('jam');
					var tujuan = $(this).data('tujuan');
					var lokasi_jemput = $(this).data('lokasi-jemput');
					var jumlah_penumpang = $(this).data('jumlah-penumpang');
					var keterangan = $(this).data('keterangan');
																	
					$("#edit_id").val(id);
					$("#edit_nama").val(nama);
					$("#edit_np_karyawan").val(np_karyawan);
					$("#edit_no_hp").val(no_hp);
					$("#edit_tapping_1_date").val(tanggal);
					$("#edit_tapping_1_time").val(jam);
					$("#edit_tujuan").val(tujuan);
					$("#edit_lokasi_jemput").val(lokasi_jemput);
					$("#edit_jumlah_penumpang").val(jumlah_penumpang);
					$("#edit_keterangan").val(keterangan);

					getPilihanAtasanLembur();	
				});
				
				
				function getAtasanKehadiran(){
					var no_pokok = $('#edit_np_karyawan').val();
                    
					$.ajax({
                        type: "POST",
                        dataType: "html",
				        url: "<?php echo base_url('food_n_go/kendaraan/data_pemesanan/ajax_getAtasanKehadiran');?>",
				        data: "vnp_karyawan="+no_pokok,
						success: function(msg){
							if(msg != ''){
								console.log(msg);
								//$("select[id=np_approver"+id+"] option[value="+msg+"]").attr('selected','selected');
								$("#edit_approval").val(msg).trigger("change");
							}
							else if($("#edit_approval").children().length>0){
								$("#edit_approval").get(0).selectedIndex = 0;
								$("#edit_approval").trigger("change");
							}
							else{
								alert('Atasan tidak ditemukan!');
							}													  
                        }
				    });     	
				}
				
				function getPilihanAtasanLembur(){
					//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
					var no_pokok = $('#edit_np_karyawan').val();
					var periode = $('#edit_tapping_1_date').val();
					$("#edit_approval").empty();
                    
					$.ajax({
					 type: "POST",
					 dataType: "html",
					 url: "<?php echo base_url('food_n_go/kendaraan/data_pemesanan/ajax_getPilihanAtasanKehadiran');?>",
					 data: "vnp_karyawan="+no_pokok+"#"+periode,
						success: function(msg){
							if(msg != ''){
								var arr_atasan = JSON.parse(msg);
								for(var i=0;i<arr_atasan.length;i++){
									$("#edit_approval").append($("<option></option>").attr("value",arr_atasan[i]["no_pokok"]).text(arr_atasan[i]["no_pokok"]+" - "+arr_atasan[i]["nama"]));
								}
								$('.select2').select2();
								getAtasanKehadiran();
							}
							else{
								alert('Atasan tidak ditemukan!');
							}
						 }
					 });
				} 
				
			</script>
			
			<script>
				function wfh()
				{				
					var checkbox 		= document.getElementsByName("edit_wfh");
					var tapping_2_time =  $('#edit_tapping_2_time').val(); 
					
					var hidden_wfh_foto_1 = document.getElementById("hidden_wfh_foto_1").val;
					var hidden_wfh_foto_2 = document.getElementById("hidden_wfh_foto_2").val;
								
					var var_array = "";
					for(var i = 0; i < checkbox.length; i++){
						if(checkbox[i].checked){
							//var_array = var_array + checkbox[i].value +", ";
							var_array = var_array + checkbox[i].value;
						}
					}
					
					if(var_array >= 1)
					{
						document.getElementById("edit_wfh").checked = true;
						$("#edit_wfh_foto_1").show();
						$("#edit_wfh_foto_2").show();
						$(".wfh_foto").show();
						
						if(hidden_wfh_foto_1 !== null && hidden_wfh_foto_1 !== '') { //jika isi
						    document.getElementById("edit_wfh_foto_1").required = false;
						}else
						{
							document.getElementById("edit_wfh_foto_1").required = true;
						}
												
						
						if(tapping_2_time!== null && tapping_2_time !== '' && tapping_2_time !== '00:00' && tapping_2_time !== ' 00:00') //jika isi
						{
							document.getElementById("edit_wfh_foto_2").required = true;
						}else
						{
							document.getElementById("edit_wfh_foto_2").required = false;
						}
						
					}else
					{
						document.getElementById("edit_wfh").checked = false;
						$("#edit_wfh_foto_1").hide();
						$("#edit_wfh_foto_2").hide();
						$(".wfh_foto").hide();
						document.getElementById("edit_wfh_foto_1").required = false;
						document.getElementById("edit_wfh_foto_2").required = false;
					}
						
					
							
				}
			</script>
		<?php } 
		if(@$akses["ubah kode unit"]){
		?>	
			<script>
				$(document).on( "click", '.edit_kode_unit',function(e) {
					var id = $(this).data('id');
					var nama = $(this).data('nama');
					var np_karyawan = $(this).data('np-karyawan');
					var dws_tanggal = $(this).data('dws-tanggal');
					var dws_name = $(this).data('dws-name');
					var kode_unit = $(this).data('kode-unit');
																			
					$("#kd_id").val(id);
					$("#kd_nama").val(nama);
					$("#kd_np_karyawan").val(np_karyawan);
					$("#kd_dws_tanggal").val(dws_tanggal);
					$("#kd_dws_name").val(dws_name);
					$("#kd_kode_unit").val(kode_unit);
				});
			</script>
		<?php } ?>
            <script>
                function show_detail(input){
                    var url = '<?= base_url('food_n_go/kendaraan/data_pemesanan/show_detail/')?>'+input.dataset.id;
					var params = {
						caller: '<?= $class_name?>'
					};
                    
                    $("#modal-content-detail").html('Loading...');
                    $("#modal_detail").modal('show');
                    $.post(url, params).done(function (data) {
                        $("#modal-content-detail").html(data);
                    });
                }
                
                function selesaikan(input){
                    var alert_text = '';
                    if($('#alert-message').hasClass('alert-success')){
                        $('#alert-message').removeClass('alert-success');
                    }
                    if($('#alert-message').hasClass('alert-danger')){
                        $('#alert-message').removeClass('alert-danger');
                    }
                    
                    var result = confirm("Selesaikan pesanan ini?");
                    if (result) {
                        $.when(
                            $.ajax({
                                url: "<?php echo base_url('food_n_go/kendaraan/data_pemesanan/selesaikan_pesanan');?>",
                                type: "POST",
                                dataType: "json",
                                data: {kode: input.dataset.kode},
                                success: function(response){
                                    if(response.status==true){
                                        $('#alert-message').addClass('alert-success');
                                    } else if(response.status==false){
                                        $('#alert-message').addClass('alert-danger');
                                    }
                                    $('#alert-message').html(alert_text + response.message);
                                    $('#alert-message').show();
                                    table.draw(false);
                                },
                                error: function(){
                                    $('alert-message').html(alert_text + 'Gagal terhubung ke server');
                                    $('#alert-message').show();
                                }
                            })
                        ).done(function(){
                            setTimeout(function(){ $('#alert-message').hide(); }, 3000);
                        });
                    }
                }
                
                function add_rate(input){
                    var url = '<?= base_url('food_n_go/kendaraan/data_pemesanan/add_rate/')?>'+input.dataset.id;
                    
                    $("#modal-content-detail").html('Loading...');
                    $("#modal_detail").modal('show');
                    $.get(url).done(function (data) {
                        $("#modal-content-detail").html(data);
                    });
                }
                
                $('#modal_detail').on('hidden.bs.modal', function () {
                    table.draw(false);
                })
            </script>

			<?php if(@$akses['hapus']):?>
			<script>
				$('#tabel_data_pemesanan').on('click', '.cancel_button', function(e){
					e.preventDefault();
					var id = e.target.dataset.id;

					var alert_text = '';
                    if($('#alert-message').hasClass('alert-success')) $('#alert-message').removeClass('alert-success');
                    if($('#alert-message').hasClass('alert-danger')) $('#alert-message').removeClass('alert-danger');

					var result = confirm("Batalkan pesanan ini?");
                    if (result) {
                        $.when(
                            $.ajax({
                                url: "<?= base_url('food_n_go/kendaraan/data_pemesanan/pemesan_batalkan_pesanan');?>",
                                type: "POST",
                                dataType: "json",
                                data: {id: id},
                                success: function(response){
                                    if(response.status==true){
                                        $('#alert-message').addClass('alert-success');
                                    } else if(response.status==false){
                                        $('#alert-message').addClass('alert-danger');
                                    }
                                    $('#alert-message').html(alert_text + response.message);
                                    $('#alert-message').show();
                                    table.draw(false);
                                },
                                error: function(){
                                    $('alert-message').html(alert_text + 'Gagal terhubung ke server');
                                    $('#alert-message').show();
                                }
                            })
                        ).done(function(){
                            setTimeout(function(){ $('#alert-message').hide(); }, 3000);
                        });
                    }
				});
			</script>
			<?php endif?>
	