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
					if(@$akses["tambah"]){
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
											
												<form role="form" action="<?php echo base_url(); ?>perizinan/data_perizinan/action_insert_perizinan" id="formulir_tambah" method="post">												
																								
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Karyawan</label>
															</div>
															<div class="col-lg-7">
																 <!--<input class="form-control" name="np_karyawan" id="np_karyawan" onChange="getNama()" required>-->
                                                                <select class="form-control select2" onChange="getNama()" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
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
													
													<!--<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label></label>
															</div>
															<div class="col-lg-7">
																 <textarea class="form-control" name="nama" id="nama" rows="5" readonly required></textarea>
															</div>														
														</div>
													</div>-->
													
													<div class="row" id=''>
														<div class="form-group">
															<div class="col-lg-2">
																<label>Jenis Izin</label>
															</div>
															<div class="col-lg-7">
																<select class="form-control" name='absence_type' onchange="take_action_date()" id="add_absence_type" required>
                                                                    <option value="">-- Pilih jenis perizinan --</option>
                                                                    <?php foreach($jenis_izin as $row){?>
                                                                    <option value="<?= $row->kode_pamlek.'|'.$row->kode_erp?>"><?= $row->nama?></option>
                                                                    <?php } ?>
																</select>
															</div>														
														</div>
													</div>
													
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
													<div id="form-start-date" style="display: block;">
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Start Date</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control" name="start_date" id="start_date" required>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Start Time</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control datetimepicker5" name="start_time" id="start_time" required>
															</div>
														</div>
													</div></div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>End Date</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control" name="end_date" id="end_date" required>
															</div>														
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>End Time</label>
															</div>
															<div class="col-lg-7">
																 <input type="text" class="form-control datetimepicker5" name="end_time" required>
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
                        
						<!-- filter bulan -->
                        <div class="form-group">
							<div class="row">
	                            <form id="forms">
	                                <div class="col-lg-6">
	                                    <label>Jenis Izin</label>
	                                    <?php foreach($jenis_izin as $row){?>
	                                    <div class="checkbox">
	                                        <label>
	                                            <input name='izin_<?= $row->kode_pamlek?>' id='izin_<?= $row->kode_pamlek?>' class='filter_jenis' type="checkbox" value="1" onclick='refresh_table_serverside()'> <?= $row->nama?>
	                                        </label>
	                                     </div>
	                                    <?php } ?>
	<!--                                    <input type="hidden" id="get_jenis">-->
	                                </div>
	                                <div class="col-lg-3">
	                                    <label>Bulan</label>
	                                    <!--<select id="pilih_bulan_tanggal" class="form-control">-->
	                                    <select class="form-control" id='bulan_tahun' name='bulan_tahun'  onchange="refresh_table_serverside()" style="width: 200px;">
	                                        <option value="ess_perizinan"></option>
	                                    <?php $count=1;
	                                    foreach ($array_tahun_bulan as $value) {
	                                        $explode_value = explode('_', $value->TABLE_NAME);
	                                        $bulan_tahun_text = id_to_bulan($explode_value[3]).' '.$explode_value[2];
	                                    ?>
	                                        <option value="<?= $value->TABLE_NAME?>"><?= $bulan_tahun_text?></option>	
	                                    <?php 
	                                    $count++;}
	                                    ?>
	                                    </select>
	                                </div>
	                            </form>
                                <div class="col-md-3">
									<div style="padding-top: 25px">
										<button type="button" onClick="otoritas()" class="btn btn-success"><i class="fa fa-print"></i> Cetak</button>
									</div>
									<!--begin: Modal Inactive -->
							      	<div class="modal fade" id="show_otoritas" srole="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
								        <div class="modal-dialog modal-md" role="document">
								        	<form method="post" target="_blank" action="<?php echo base_url('perizinan/data_perizinan_x/cetak')?>">
									          	<div class="modal-content">
									            	<div class="modal-header">
														<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
														<h4 class="modal-title" id="label_modal_ubah">Pilih Otoritas</h4>
													</div>
													<div class="modal-body">
														<input type="hidden" name="bulan" value="" id="get_month" />
														<input type="hidden" name="izin_0" value="" id="get_izin_0" />
														<input type="hidden" name="izin_C" value="" id="get_izin_C" />
														<input type="hidden" name="izin_E" value="" id="get_izin_E" />
														<input type="hidden" name="izin_F" value="" id="get_izin_F" />
														<input type="hidden" name="izin_G" value="" id="get_izin_G" />
														<input type="hidden" name="izin_H" value="" id="get_izin_H" />
														<!--<input type="hidden" name="izin_TM" value="" id="get_izin_TM" />
														<input type="hidden" name="izin_TK" value="" id="get_izin_TK" />
														-->
														<select multiple="multiple" class="form-control select2" id="multi_select" name='np_karyawan[]' style="width: 100%;" required>
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
									        </form>
								        </div>
							      	</div>
								</div>					
							</div>
						</div>
                        
                        <div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data_perizinan">
									<thead>
										<tr>
											<th class='text-center no-sort' style="max-width: 10%">No</th>
											<th class='text-center' style="max-width: 15%">No. pokok</th>			
											<th class='text-center no-sort'>Nama</th>	
											<!--<th class='text-center no-sort'>Kode</th>-->
											<th class='text-center no-sort'>Izin</th>
											<th class='text-center'>From</th>
											<th class='text-center'>To</th>
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
					
					if(@$akses["hapus"]){
				?>
						<!--begin: Modal Inactive -->
                        <div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-sm" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger" id="title-inactive">
                                            <b>Hapus <?= $judul ?></b>
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <h6 id="message-inactive"></h6>
                                    </div>
                                    <div class="modal-footer">
										<a href="" id="inactive-action" class="btn btn-primary">Ya, Hapus</a>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
									</div>
                                </div>
                            </div>
                        </div>
                        <!--end: Modal Inactive -->
				<?php
					}
					if(@$akses["ubah"]){
				?>
                        <!-- Modal Ubah -->
						<div class="modal fade" id="modal_ubah" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form method="post" action="<?php echo base_url(); ?>perizinan/data_perizinan/action_update_data_perizinan">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">		
										
											<div class="form-group row">
												<div class="col-lg-2">
													<label>NP Karyawan</label>
												</div>
												<div class="col-lg-10">
													<select class="form-control select2insidemodal" name="edit_np_karyawan" id="edit_np_karyawan" style="width: 100%" required>
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
                                            
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Jenis Izin</label>
												</div>
												<div class="col-lg-10">
													<select class="form-control" name='edit_absence_type' id="edit_absence_type" required>
                                                        <?php foreach($jenis_izin as $row){?>
                                                        <option value="<?= $row->kode_pamlek.'|'.$row->kode_erp?>"><?= '('.$row->kode_pamlek.') '.$row->nama?></option>
                                                        <?php } ?>
                                                    </select>
												</div>											
											</div>
											
											<div class="form-group row">										
												<div class="col-lg-2">
													<label>Start</label>
												</div>
												<div class='col-lg-4'>
													<input type="date" class="form-control" name="edit_start_date" id="edit_start_date">
											    </div>
												<div class='col-lg-3'>
													<input type="time" class="form-control" name="edit_start_time" id="edit_start_time">
												</div>											
											</div>
										
											<div class="form-group row">
												<div class="col-lg-2">
													<label>End</label>
												</div>
												<div class='col-lg-4'>
													<input type="date" class="form-control" name="edit_end_date" id="edit_end_date">
											    </div>
												<div class='col-lg-3'>
													<input type="time" class="form-control" name="edit_end_time" id="edit_end_time">
												</div>
											</div>
                                            
											<div class="modal-footer">.
												<input type='hidden' id='edit_id' name='edit_id'>
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
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		<script type="text/javascript">
            $('#multi_select').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
			$(document).ready(function() {
                $('.datetimepicker5').datetimepicker({
                    format: 'HH:mm'
                });
                $('.select2').select2();
                $(".select2insidemodal").select2({
                    dropdownParent: $("#modal_ubah")
                });
                
                $(function () {
                    $('#start_date').datetimepicker({
                        format: 'D-M-Y',
						<?php if(@$min){?>
						minDate : '<?php echo $min;?>'
						<?php } ?>
                    });
                    
                    $('#end_date').datetimepicker({
                        format: 'D-M-Y'
                    });
                    
                    $("#start_date").on("dp.change", function (e) {
                        var oldDate = new Date(e.date);
                        var newDate = new Date(e.date);
                        newDate.setDate(oldDate.getDate());

                        $('#end_date').data("DateTimePicker").minDate(newDate); 
						
						var start_date = $('#start_date').val();;
						$('#end_date').val(start_date);
						
                    });

                });
                
				$("#form_absence_type").hide();
				$("#form_jumlah_bulan").hide();
				$("#form_jumlah_hari").hide();
				$('#tabel_data_perizinan').DataTable().destroy();				
				table_serverside();
			});
            
            function refresh_table_serverside() {
            	
				$('#tabel_data_perizinan').DataTable().destroy();				
				table_serverside();
			}
			
			function table_serverside()
			{
                var bulan_tahun = $('#bulan_tahun').val();
                var izin_0 		= $('#izin_0:checked').val();
                var izin_C 		= $('#izin_C:checked').val();
                //var izin_D 		= $('#izin_D:checked').val();
                var izin_E 		= $('#izin_E:checked').val();
                var izin_F 		= $('#izin_F:checked').val();
                var izin_G 		= $('#izin_G:checked').val();
                var izin_H 		= $('#izin_H:checked').val();
                var izin_TM 	= $('#izin_TM:checked').val();
                var izin_TK 	= $('#izin_TK:checked').val();
                //var izin_AB 	= $('#izin_AB:checked').val();
                //var izin_ATU 	= $('#izin_ATU:checked').val();
                
                var table;
                //var jenis = $('#get_jenis').val(arr_jenis);
				//datatables
				table = $('#tabel_data_perizinan').DataTable({ 
					
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
						"url": "<?php echo site_url("perizinan/Data_perizinan/tabel_data_perizinan/")?>"+ bulan_tahun + '/' + izin_0 + '/' + izin_C + '/' + izin_E + '/' + izin_F + '/' + izin_G + '/' + izin_H,
						"type": "POST",
                        //data: data_save
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
			function otoritas() {
				document.getElementById('get_month').value = $('#bulan_tahun').val();
                document.getElementById('get_izin_0').value = $('#izin_0:checked').val();
				document.getElementById('get_izin_C').value = $('#izin_C:checked').val();
                //document.getElementById('get_izin_D').value = $('#izin_D:checked').val();
                document.getElementById('get_izin_E').value = $('#izin_E:checked').val();
                document.getElementById('get_izin_F').value = $('#izin_F:checked').val();
                document.getElementById('get_izin_G').value = $('#izin_G:checked').val();
                document.getElementById('get_izin_H').value = $('#izin_H:checked').val();
                /*document.getElementById('get_izin_TM').value = $('#izin_TM:checked').val();
                document.getElementById('get_izin_TK').value = $('#izin_TK:checked').val();
				*/
				$("#show_otoritas").modal('show');
			}
		</script>
		
		<script>
			function checkJumlahCuti(){
				var absence_type = document.getElementById("absence_type").value;
				var np_karyawan = $('#np_karyawan').val();
				var jumlah_hari = $('#jumlah_hari').val();
				var jumlah_bulan = $('#jumlah_bulan').val();
				
				var data_array = new Array();
					data_array[0] = absence_type;
					data_array[1] = np_karyawan;
					data_array[2] = jumlah_hari;
					data_array[3] = jumlah_bulan;
								
					
				$.ajax({
				 type: "POST",
				 dataType: "html",
				 url: "<?php echo base_url('perjalanan_dinas/Sppd/ajax_checkJumlahCuti');?>",
				 data: "data_array="+data_array,
					success: function(msg){
						if(msg == ''){				
							
						}else{							 
							alert(msg);
							$('#jumlah_bulan').val('0');
							$('#jumlah_hari').val('0');
						}													  
					 }
				 });       
			} 
		</script>
		
		<script>
			function getJenisCuti(){
				var jenis_cuti = document.getElementById("absence_type").value;
				
				if(jenis_cuti=='')
				{					
					$("#form_jumlah_bulan").hide();
					$("#form_jumlah_hari").hide();
				}else
				if(jenis_cuti=='1010')
				{					
					$("#form_jumlah_bulan").show();
					$("#form_jumlah_hari").show();
				}else
				{
					$("#form_jumlah_bulan").hide();
					$("#form_jumlah_hari").show();
				}	
				
			}
		</script>
		
		<script>
			function getEndDate(){
				var start_date = $('#start_date').val();			
				document.getElementById('end_date').setAttribute("min", start_date);			
			} 
		</script>
		
		<script>
			function getNama(){
			var np_karyawan = $('#np_karyawan').val();
			
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('perizinan/data_perizinan/ajax_getNama');?>",
             data: "vnp_karyawan="+np_karyawan,
				success: function(msg){
					if(msg == ''){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
						$('#np_karyawan').val('');
						$('#nama').text('');
						$("#form_absence_type").hide();
					}else{							 
						$('#nama').text(msg);
						$("#form_absence_type").show();
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
             url: "<?php echo base_url('perizinan/data_perizinan/ajax_getListNp');?>",
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
             dataType: "html",
             url: "<?php echo base_url('perjalanan_dinas/Sppd/ajax_getNama_approval');?>",
             data: "vnp_karyawan="+np_karyawan,
				success: function(msg){
					if(msg == ''){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
						$('#approval_1').val('');
						$('#approval_1_input').val('');
					}else{							 
						$('#approval_1_input').val(msg);
					}													  
				 }
			 });       
		} 		
		
		function getNamaAtasan2(){
			var np_karyawan = $('#approval_2').val();
			
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('perjalanan_dinas/Sppd/ajax_getNama_approval');?>",
             data: "vnp_karyawan="+np_karyawan,
				success: function(msg){
					if(msg == ''){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
						$('#approval_2').val('');
						$('#approval_2_input').val('');
					}else{							 
						$('#approval_2_input').val(msg);
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
			$(document).on( "click", '.edit_button',function(e) {
				var edit_id = $(this).data('id');
				var edit_np_karyawan = $(this).data('np-karyawan');
				var edit_absence_type = $(this).data('absence-type');
				var edit_start_date = $(this).data('start-date');
				var edit_start_time = $(this).data('start-time');
				var edit_end_date = $(this).data('end-date');
				var edit_end_time = $(this).data('end-time');
					
				$("#edit_id").val(edit_id);
				$("#edit_start_date").val(edit_start_date);	
				$("#edit_start_time").val(edit_start_time);
				$("#edit_end_date").val(edit_end_date);				
				$("#edit_end_time").val(edit_end_time);
				
				document.getElementById("edit_np_karyawan").value = edit_np_karyawan;
				document.getElementById("edit_absence_type").value = edit_absence_type;
				take_action_date();
			});
            
            function hapus(id, np, tanggal_end, tanggal_start) {
//                console.log(id);
//                console.log(np);
//                console.log(tanggal);
                var url = "<?php echo site_url('perizinan/data_perizinan/hapus') ?>/";
                $('#inactive-action').prop('href', url+id+'/'+np+'/'+tanggal_end+'/'+tanggal_start);
                $('#message-inactive').text('Apakah anda yakin ingin menghapus perizinan ini ?');
                $('#modal-inactive').modal('show');
            }
            
            function take_action_date(){
                var add_absence_type = $('#add_absence_type').val();
                if(add_absence_type=='0|2001|5000'){
                    $('#form-start-date').css('display', 'none');
                    $('#start_date').prop('required', false);
                    $('#start_time').prop('required', false);
                    $('#start_date').val('');
                    $('#start_time').val('');
                } else{
                    $('#form-start-date').css('display', 'block');
                    $('#start_date').prop('required', true);
                    $('#start_time').prop('required', true);
                }
                console.log(add_absence_type);
            }
		</script>
		
		