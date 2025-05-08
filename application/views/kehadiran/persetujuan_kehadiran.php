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
				?>
				<?php
					
					
					if($this->akses["lihat"]){
				?>		
						<!-- filter bulan -->
                        <div class="form-group">
							<div class="row">
                                <div class="col-lg-4">
                                    <label>Bulan</label>
                                    <!--<select id="pilih_bulan_tanggal" class="form-control">-->
                                    <select class="form-control select2" id='bulan_tahun' name='bulan_tahun'  onchange="refresh_table_serverside()" style="width: 200px;">
                                        <option value=''></option>	
                                    <?php 
									
									$tampil_bulan_tahun=date("m-Y");
									
                                    foreach ($array_tahun_bulan as $value) {
                                        if(!empty($this->session->flashdata('tampil_bulan_tahun')))
                                        {
                                            $tampil_bulan_tahun=$this->session->flashdata('tampil_bulan_tahun');
                                        } else if(@$get_bulan){
                                            $tampil_bulan_tahun=$get_bulan;
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
                                <div class="col-lg-4">
									<?php
										if(!$sudah_cutoff){
											echo "<div class='alert alert-info alert-dismissable'>";
												echo "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>";
												echo "<span style=' font-size: 200%;'><b>$banyak_bulan_lalu</b></span> Permohonan Persetujuan Kehadiran bulan <b>$nama_bulan_lalu</b> menanti tindak lanjut Saudara.";
											echo "</div>";
										}
									?>
                                </div>
                                <div class="col-lg-4">
									<div class="alert alert-info alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										<?php
											echo "<span style=' font-size: 200%;'><b>$banyak_bulan_ini</b></span> Permohonan Persetujuan Kehadiran bulan <b>$nama_bulan_ini</b> menanti tindak lanjut Saudara.";
										?>
									</div>
                                </div>
							</div>
						</div>
						
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_kehadiran">
									<thead>
										<tr>
											<th class='text-center'>No</th>
											<th class='text-center'>NP</th>	
											<th class='text-center'>Nama</th>	
											<th class='text-center'>Tertanggal</th>			
											<th class='text-center no-sort'>Kehadiran Semula</th>
											<th class='text-center no-sort'>Permohonan Ubah</th>
											<th class='text-center no-sort'>Status</th>
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
													<td>NP Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="status_np_karyawan"></a></td>
												</tr>
												<tr>
													<td>Nama Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="status_nama"></a></td>
												</tr>
												
												
											</table>
											
											<br>
											
											<div class="alert alert-info">
												<strong><a id="status_approval_nama"></a></strong><br>
												<p id="status_approval_status"></p>
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
					
					if($akses["persetujuan"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_persetujuan" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_batal">Permohonan <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">		
											<form role="form" action="<?php echo base_url(); ?>kehadiran/persetujuan_kehadiran/action_persetujuan_kehadiran" id="formulir_tambah" method="post">	
											
											<table>
												<tr>
													<td>NP Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="persetujuan_np_karyawan"></a></td>
												</tr>
												<tr>
													<td>Nama Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="persetujuan_nama"></a></td>
												</tr>	
												<tr>
													<td>Alasan Perubahan</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="persetujuan_approval_ket"></a></td>
												</tr>	
												<tr class='wfh_foto'>
													<td>Alasan Khusus</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="persetujuan_approval_wfh"></a></td>
												</tr>
												<tr class='wfh_foto evidence'>
													<td><a>Evidence Berangkat</a></td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td>
														<a id="download_wfh_foto_1" href='' target='_blank'>
															<img id="preview_wfh_foto_1" src="" style="width:50px">									
														</a>
													</td>
												</tr>
												<tr class='wfh_foto evidence'>
													<td><a>Evidence Pulang</a></td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td>
														<a id="download_wfh_foto_2" href='' target='_blank'>
															<img id="preview_wfh_foto_2" src="" style="width:50px">									
														</a>
													</td>
												</tr>
																							
											</table>
											
											<div class="dinas_luar">
												<a class='btn btn-warning btn-xs '>Dinas Luar</a>
												<br>
											</div>
											
											<br>
											
											<div id="form-referensi-data-pamlek">
													<b>Referensi Data Pamlek</b>
													<br>
													<textarea rows="5" class="form-control" name='persetujuan_referensi_data_pamlek' id="persetujuan_referensi_data_pamlek" readonly></textarea>
											</div>
												
											<br>
											
											<div class="alert alert-info">
												<strong><a id="persetujuan_approval_nama"></a></strong><br>
												<p id="persetujuan_approval_status"></p>
												<br>
												<select class="form-control" name='persetujuan_status_1' id="persetujuan_status_1" onchange="form_alasan_1(this)" style="width: 150px;" disabled>
													<option value='0'></option>
													<option value='1'>Setuju</option>
													<option value='2'>Tidak Setuju</option>
												</select>
												<div id="form-alasan-1" style="display: none;">
													<b>Alasan Tidak Disetujui</b>
													<br>
													<textarea rows="2" class="form-control" name='persetujuan_alasan_1' id="persetujuan_alasan_1"></textarea>
												</div>
											</div>
											
																						
											
												<div class="row">
													<div class="col-lg-12 text-right">
														<input type="hidden" name="persetujuan_tahun_bulan" id="persetujuan_tahun_bulan">
														<input type="hidden" name="persetujuan_id" id="persetujuan_id">
														<input type="hidden" name="persetujuan_tapping_fix_1_temp" id="persetujuan_tapping_fix_1_temp">
														<input type="hidden" name="persetujuan_tapping_fix_2_temp" id="persetujuan_tapping_fix_2_temp">
														<input type="submit" name="submit" id='persetujuan_button' value="Submit" class="btn btn-block btn-success" disabled>
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
		
		
		<script type="text/javascript">
	
			$(document).ready(function() {
				$('#tabel_ess_kehadiran').DataTable().destroy();	
				table_serverside();
			});
			
			 function refresh_table_serverside() {
				$('#tabel_ess_kehadiran').DataTable().destroy();				
				table_serverside();
			}
			
			function table_serverside()
			{
				var table;
				var bulan_tahun = $('#bulan_tahun').val();
				//datatables
				table = $('#tabel_ess_kehadiran').DataTable({ 
					
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
						"url": "<?php echo site_url("kehadiran/persetujuan_kehadiran/tabel_ess_kehadiran/")?>" + bulan_tahun,
						"data": {np: "<?= $get_np ?>"},
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
		<script type="text/javascript">
			function form_alasan_1(obj){
                var selectBox = obj;
                var selected = selectBox.options[selectBox.selectedIndex].value;
                var textarea = document.getElementById("form-alasan-1");

                if(selected === '2'){
                    textarea.style.display = "block";
                }
                else{
                    textarea.style.display = "none";
                }
            }
			function form_alasan_2(obj){
                var selectBox2 = obj;
                var selected2 = selectBox2.options[selectBox2.selectedIndex].value;
                var textarea2 = document.getElementById("form-alasan-2");

                if(selected2 === '2'){
                    textarea2.style.display = "block";
                }
                else{
                    textarea2.style.display = "none";
                }
            }
		</script>
		
		<script>
			$(document).on( "click", '.status_button',function(e) {
				var status_np_karyawan = $(this).data('np-karyawan');
				var status_nama = $(this).data('nama');
				
				var status_approval_nama = $(this).data('approval-nama');
				var status_approval_status = $(this).data('approval-status');
							
				$("#status_np_karyawan").text(status_np_karyawan);					
				$("#status_nama").text(status_nama);
				
				$("#status_approval_nama").text(status_approval_nama);				
				$("#status_approval_status").text(status_approval_status);
			
				
				
				
			});
		</script>

	
		<script>
			$(document).on( "click", '.persetujuan_button',function(e) {
				var persetujuan_id = $(this).data('id');
				var persetujuan_np_karyawan = $(this).data('np-karyawan');
				var persetujuan_nama = $(this).data('nama');				
				var persetujuan_approval_nama = $(this).data('approval-nama');
				var persetujuan_approval_status_id = $(this).data('approval-status-id');								
				var persetujuan_approval_status = $(this).data('approval-status');	
				var persetujuan_approval_ket = $(this).data('approval-ket');	
				var persetujuan_approval_wfh = $(this).data('approval-wfh');
				var persetujuan_approval_wfh_foto_1 = $(this).data('approval-wfh-foto-1');	
				var persetujuan_approval_wfh_foto_2 = $(this).data('approval-wfh-foto-2');	
				var persetujuan_approval_np = $(this).data('approval-np');		
				var persetujuan_tahun_bulan = $(this).data('approval-tahun-bulan');	
				var persetujuan_tapping_fix_1_temp = $(this).data('approval-tapping-fix-1-temp');	
				var persetujuan_tapping_fix_2_temp = $(this).data('approval-tapping-fix-2-temp');
				var persetujuan_referensi_data_pamlek = $(this).data('approval-referensi-data-pamlek');					
				var is_dinas_luar = $(this).data('dinas-luar');

				$("#persetujuan_id").val(persetujuan_id);		
				$("#persetujuan_np_karyawan").text(persetujuan_np_karyawan);					
				$("#persetujuan_nama").text(persetujuan_nama);		
				$("#persetujuan_approval_nama").text(persetujuan_approval_nama);				
				$("#persetujuan_approval_status").text(persetujuan_approval_status);				
				$("#persetujuan_approval_ket").text(persetujuan_approval_ket);	
				$("#persetujuan_approval_wfh").text(persetujuan_approval_wfh);
				$("#persetujuan_approval_np").text(persetujuan_approval_np);
				$("#persetujuan_tahun_bulan").val(persetujuan_tahun_bulan);
				$("#persetujuan_tapping_fix_1_temp").val(persetujuan_tapping_fix_1_temp);
				$("#persetujuan_tapping_fix_2_temp").val(persetujuan_tapping_fix_2_temp);
				$("#persetujuan_referensi_data_pamlek").val(persetujuan_referensi_data_pamlek);
				console.log(persetujuan_tapping_fix_1_temp);
				if(persetujuan_approval_wfh != "-") {					
					$(".wfh_foto").show();
						 
					document.getElementById('preview_wfh_foto_1').src=persetujuan_approval_wfh_foto_1;	
					document.getElementById('download_wfh_foto_1').href=persetujuan_approval_wfh_foto_1;	
					
					document.getElementById('preview_wfh_foto_2').src=persetujuan_approval_wfh_foto_2;	
					document.getElementById('download_wfh_foto_2').href=persetujuan_approval_wfh_foto_2;	
									 
				}else {
					$(".wfh_foto").hide();
				}


				//edit by wina 11-02-20
				if(is_dinas_luar == "1") {
					$(".dinas_luar").show();
				} else {
					$(".dinas_luar").hide();
				}

				if(persetujuan_approval_ket == "WFH MOBILE") {
					$(".evidence").hide();
				} else {
					$(".evidence").show();
				}
				
				//document.getElementById("persetujuan_status").value = persetujuan_status;
				
			
			
			<?php	
				if($_SESSION["grup"] == 5) //jika pengguna
				{
			?>				
					if(persetujuan_approval_status_id==0 && persetujuan_approval_np=="<?php echo $_SESSION['no_pokok']?>")
					{
							
						$('#persetujuan_status_1').prop('disabled', false);
						$('#persetujuan_button').prop('disabled', false);
					}
			<?php
				}else
				if($_SESSION["grup"]  == 1 || $_SESSION["grup"]  == 2 || $_SESSION["grup"]  == 3) //jika superadmin, admin TI, admin SDM
				{
			?>
					$('#persetujuan_status_1').prop('disabled', false);				
					$('#persetujuan_button').prop('disabled', false);
			<?php	
				}
			?>
				
				
				
			});
		</script>

		