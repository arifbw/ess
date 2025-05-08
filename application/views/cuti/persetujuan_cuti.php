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
                                <div class="col-lg-3">
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
								<div class="col-lg-4">
                                    <label>Filter Persetujuan</label>
									<select class='form-control' id='filter' onchange="refresh_table_serverside()">
										<option value='all'>Semua</option>
										<option value='0'>Menunggu Persetujuan</option>									
										<option value='1'>Disetujui Atasan</option>
										<option value='2'>Disetujui SDM</option>
										<option value='3'>Ditolak Atasan</option>
										<option value='4'>Dibatalkan Pemohon</option>
										<option value='5'>Ditolak SDM</option>										
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
											<th class='text-center no-sort'>Alasan</th>
											<th class='text-center no-sort'>Status</th>
											<!-- <th class='text-center no-sort'>Approval By SDM</th>			 -->								
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
											<form id="formulir_tambah">	
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
														<td>Dibuat Tanggal</td>
														<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
														<td><a id="persetujuan_created_at"></a></td>
													</tr>
													<tr>
														<td>Dibuat Oleh</td>
														<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
														<td><a id="persetujuan_created_by"></a></td>
													</tr>
												</table>
												<br>
												<div class="alert alert-info">
													<strong><a id="persetujuan_approval_1_nama"></a></strong><br>
													<p id="persetujuan_approval_1_status"></p>
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
												<div class="alert alert-info">
													<strong><a id="persetujuan_approval_2_nama"></a></strong><br>
													<p id="persetujuan_approval_2_status"></p>
													<br>
													<select class="form-control" name='persetujuan_status_2' id="persetujuan_status_2" onchange="form_alasan_2(this)" style="width: 150px;" disabled>
														<option value='0'></option>
														<option value='1'>Setuju</option>
														<option value='2'>Tidak Setuju</option>
													</select>
													<div id="form-alasan-2" style="display: none;">
														<b>Alasan Tidak Disetujui</b>
														<br>
														<textarea rows="2" class="form-control" name='persetujuan_alasan_2' id="persetujuan_alasan_2"></textarea>
													</div>			
												</div>
												<div class="row">
													<div class="col-lg-12 text-right">
														<input type="hidden" name="persetujuan_id" id="persetujuan_id">
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
				var filter = $('#filter').val();
				//datatables
				table = $('#tabel_ess_cuti').DataTable({ 
					
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
						"url": "<?php echo site_url("cuti/persetujuan_cuti/tabel_ess_cuti/")?>" + bulan_tahun + "/" + filter,
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
			$(document).on( "click", '.persetujuan_button',function(e) {
				var persetujuan_id = $(this).data('id');
				var persetujuan_np_karyawan = $(this).data('np-karyawan');
				var persetujuan_nama = $(this).data('nama');
				var persetujuan_created_at = $(this).data('created-at');
				var persetujuan_created_by = $(this).data('created-by');
				var persetujuan_approval_1_nama = $(this).data('approval-1-nama');
				var persetujuan_approval_1_status = $(this).data('approval-1-status');
				var persetujuan_approval_2_nama = $(this).data('approval-2-nama');
				var persetujuan_approval_2_status = $(this).data('approval-2-status');
				var persetujuan_approval_1 = $(this).data('approval-1');
				var persetujuan_approval_2 = $(this).data('approval-2');
				var persetujuan_status_1 = $(this).data('status-1');
				var persetujuan_status_2 = $(this).data('status-2');
					
				$("#persetujuan_id").val(persetujuan_id);		
				$("#persetujuan_np_karyawan").text(persetujuan_np_karyawan);					
				$("#persetujuan_nama").text(persetujuan_nama);
				$("#persetujuan_created_at").text(persetujuan_created_at);	
				$("#persetujuan_created_by").text(persetujuan_created_by);
				$("#persetujuan_approval_1_nama").text(persetujuan_approval_1_nama);				
				$("#persetujuan_approval_1_status").text(persetujuan_approval_1_status);
				$("#persetujuan_approval_2_nama").text(persetujuan_approval_2_nama);
				$("#persetujuan_approval_2_status").text(persetujuan_approval_2_status);
				
				document.getElementById("persetujuan_status_1").value = persetujuan_status_1;
				document.getElementById("persetujuan_status_2").value = persetujuan_status_2;
				
				if(<?php echo $_SESSION["grup"]?> == 4 || <?php echo $_SESSION["grup"]?> == 5) //jika pengguna, administrator unit kerja
				{
					if(persetujuan_status_1==0 && persetujuan_approval_1=="<?php echo $_SESSION['no_pokok']?>")
					{
						$('#persetujuan_status_1').prop('disabled', false);
						$('#persetujuan_button').prop('disabled', false);
					}
					
					if(persetujuan_status_2==0 && persetujuan_approval_2=="<?php echo $_SESSION['no_pokok']?>")
					{
						$('#persetujuan_status_2').prop('disabled', false);
						$('#persetujuan_button').prop('disabled', false);
					}
				}else
				if(<?php echo $_SESSION["grup"]?> == 1 || <?php echo $_SESSION["grup"]?> == 2 || <?php echo $_SESSION["grup"]?> == 3) //jika superadmin, admin TI, admin SDM
				{
					$('#persetujuan_status_1').prop('disabled', false);
					$('#persetujuan_status_2').prop('disabled', false);
					$('#persetujuan_button').prop('disabled', false);
				}
				
				
				
			});
		</script>

		<script>
			document.getElementById('formulir_tambah').addEventListener('submit', function(e) {
				e.preventDefault(); // Prevent page refresh

				const formData = new FormData(this);

				fetch('<?php echo base_url(); ?>cuti/persetujuan_cuti/action_persetujuan_cuti', {
					method: 'POST',
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					},
					body: formData
				})
				.then(response => response.text()) // or .json() depending on response type
				.then(data => {
					console.log('Success:', data);
					$('#tabel_ess_cuti').DataTable().ajax.reload();
					$("#modal_persetujuan").modal("hide");
				})
				.catch(error => {
					console.error('Error:', error);
				});
			});
		</script>
		
		