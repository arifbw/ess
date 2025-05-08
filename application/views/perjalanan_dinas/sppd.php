		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
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
					
					echo "<div class='row text-right'>";
					
					if($akses["lihat log"]){
						echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
					}
					
					if($this->akses["download"]){
						echo '<button type="button" style="margin-left: 10px;" onClick="cetak()" class="btn btn-success"><i class="fa fa-print"></i> Cetak</button>';
					}
					
					echo "<br><br>";
					echo "</div>";
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
											
												<form role="form" action="<?php echo base_url(); ?>perjalanan_dinas/Sppd/action_insert_cuti" id="formulir_tambah" method="post">												
																								
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Karyawan</label>
															</div>
															<div class="col-lg-7">
																 <input class="form-control" name="np_karyawan" id="np_karyawan" onChange="getNama()" required>
															</div>	
															<div class="col-lg-1">
																 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
															</div>	
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label></label>
															</div>
															<div class="col-lg-7">
																 <textarea class="form-control" name="nama" id="nama" rows="5" readonly required></textarea>
															</div>														
														</div>
													</div>
													
													<div class="row" id='form_absence_type'>
														<div class="form-group">
															<div class="col-lg-2">
																<label>Jenis Cuti</label>
															</div>
															<div class="col-lg-7">
																<select class="form-control" name='absence_type' id="absence_type" onChange="getJenisCuti()" required>
																	<option value=''></option>
																	<?php 
																		foreach ($select_mst_cuti->result_array() as $value) 
																		{														
																			echo "<option value='".$value['kode_erp'] ."'>".$value['uraian']."</option>";
																		}
																	?>
																</select>
															</div>														
														</div>
													</div>
													
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Start Date</label>
															</div>
															<div class="col-lg-7">
																 <input type="date" class="form-control" name="start_date" id="start_date" onChange="getEndDate()" required>
															</div>
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>End Date</label>
															</div>
															<div class="col-lg-7">
																 <input type="date" class="form-control" name="end_date" id="end_date" min="" required>
															</div>														
														</div>
													</div>
													
													<div class="row" id='form_jumlah_bulan'>
														<div class="form-group">
															<div class="col-lg-2">
																<label>Jumlah Bulan</label>
															</div>
															<div class="col-lg-7">
																<input type="number" class="form-control" name="jumlah_bulan" id="jumlah_bulan"  onChange="checkJumlahCuti()" value="0" min="0" required>
															</div>														
														</div>
													</div>
													
													<div class="row" id='form_jumlah_hari'>
														<div class="form-group">
															<div class="col-lg-2">
																<label>Jumlah Hari</label>
															</div>
															<div class="col-lg-7">
																<input type="number" class="form-control" name="jumlah_hari" id="jumlah_hari" onChange="checkJumlahCuti()"  value="0" min="0" required>
															</div>														
														</div>
													</div>
													
													
													
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>Alasan</label>
															</div>
															<div class="col-lg-7">
																<input class="form-control" name="alasan"  id='alasan' value="" required>
															</div>														
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Atasan 1</label>
															</div>
															<div class="col-lg-7">
																<input class="form-control" name="approval_1" id="approval_1" value="" onChange="getNamaAtasan1()" required>
															</div>								
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label></label>
															</div>
															<div class="col-lg-7">														
																<input class="form-control" name="approval_1_input" id="approval_1_input"value="" readonly required>												
															</div>														
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label>NP Atasan 2</label>
															</div>
															<div class="col-lg-7">
																<input class="form-control" name="approval_2" id="approval_2" value="" onChange="getNamaAtasan2()" required>
															</div>														
														</div>
													</div>
													
													<div class="row">
														<div class="form-group">
															<div class="col-lg-2">
																<label></label>
															</div>
															<div class="col-lg-7">														
																<input class="form-control" name="approval_2_input" id="approval_2_input"value="" readonly required>												
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
								<div class="col-lg-3">
                                    <label>NP</label>
                                    <!--<select id="pilih_bulan_tanggal" class="form-control">-->
                                    <select class="form-control select2" id='np' name='np'  onchange="refresh_table_serverside()" style="width: 100%;">
                                        <option value=''>Semua</option>
                                        <?php 
                                        foreach ($array_daftar_karyawan->result_array() as $value) {		
                                        ?>
                                            <option value='<?php echo $value['no_pokok']?>'><?php echo $value['no_pokok']." ".$value['nama']?></option>								

                                        <?php 
                                        }
                                        ?>
                                    </select>
                                </div>			
                                <div class="col-lg-3">
                                    <label>Bulan</label>
                                    <!--<select id="pilih_bulan_tanggal" class="form-control">-->
                                    <select class="form-control select2" id='bulan_tahun' name='bulan_tahun'  onchange="refresh_table_serverside()" style="width: 100%;">
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
									
									<?php
										if($this->akses["download"]){
									?>
										<!-- <button type="button" onClick="cetak()" class="btn btn-success"><i class="fa fa-print"></i> Cetak</button>											 -->
										
										<!--begin: Modal Inactive -->
										<div class="modal fade" id="show_cetak" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-md" role="document">
												<form method="post" target="_blank" action="<?php echo base_url('perjalanan_dinas/sppd/cetak')?>">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
															<h4 class="modal-title" id="label_modal_ubah">Cetak Per Bulan (Berdasarkan tanggal berangkat)</h4>
														</div>
														<div class="modal-body">	
															 <select class="form-control select2" id='cetak_bulan' name='cetak_bulan' style="width: 100%;" required>
																	<option value=''>Pilih Bulan</option>	
																<?php 
																foreach ($array_tahun_bulan as $value) {
																?>
																	<option value='<?php echo substr($value,3,4).'-'.substr($value,0,2)?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,0,2))." ".substr($value,3,4)?></option>								

																<?php 
																}
																?>
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
										<!--end: Modal Inactive -->
									<?php 
										}
									?>
                                </div>
								<div class="col-lg-3">
                                    <label>Jenis Perjalanan</label>
                                    <select class="form-control select2" onchange="refresh_table_serverside()" id='jenis_perjalanan' name='jenis_perjalanan'  style="width: 100%;">
                                        <option value=''>Semua</option>
										<?php 
											foreach ($array_jenis_perjalanan as $value) {
										?>
											<option value='<?php echo $value ?>'>
												<?php echo $value ?>
											</option>
										<?php 
											}
										?>
                                    </select>
                                </div>	                              									
							</div>
						</div>
                        
                        <div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_sppd">
									<thead>
										<tr>
											<th class='text-center no-sort'>No</th>
											<th class='text-center'>NP</th>	
											<th class='text-center no-sort'>Nama</th>	
											<th class='text-center no-sort'>Perihal</th>
											<th class='text-center no-sort'>Tipe Perjalanan</th>
											<th class='text-center'>Start Date</th>
											<th class='text-center'>End Date</th>
											<th class='text-center'>Jenis Perjalanan</th>
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
					if(@$akses["batal"]){
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
											
											<form role="form" action="<?php echo base_url(); ?>perjalanan_dinas/Sppd/action_batal_cuti" id="formulir_tambah" method="post">	
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
		<script src="<?php base_url()?>/ess/asset/cartenz/sweetalert2.all.min.js"></script>
		<script type="text/javascript">	
			async function cetak() {
				// $("#show_cetak").modal('show');

				var bulan_tahun = $('#bulan_tahun').val() ? $('#bulan_tahun').val() : 0;
                var np = $('#np').val() ? $('#np').val() :  "-";
                var jenis_perjalanan = $('#jenis_perjalanan').val() ? $('#jenis_perjalanan').val() : "-";

				var url = "<?php echo base_url('perjalanan_dinas/sppd/cetak')?>";
				Swal.showLoading();
				$.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        cetak_bulan: bulan_tahun,
                        np: np,
						jenis_perjalanan: jenis_perjalanan
                    },
					xhrFields: {
						responseType: 'blob' // Ensures binary data is handled properly
					},
                    success: function (response) {
                        const blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
						const link = document.createElement('a');
						link.href = URL.createObjectURL(blob);
						link.download = `Data_sppd_${bulan_tahun}_${np}_${jenis_perjalanan} .xlsx`;
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);
						swal.fire("Sukses", "File Berhasil di Export.", "success");
                    },
                    error: function (xhr, status, error) {
                    //    console.log(error);
					   swal.fire("Gagal", error, "error");
                    }
                });

				/* try {
					const myHeaders = new Headers();
					myHeaders.append("Content-Type", "application/json");

					var cfg = {
						method: "POST",
						body: {
							cetak_bulan: bulan_tahun,
							np: np,
							jenis_perjalanan: jenis_perjalanan
						},
						headers: myHeaders
					};
					// console.log(url);
					const response = await fetch(url, cfg);
					// console.log(response);
					if (response.ok) {
						const blob = await response.blob();
						const link = document.createElement('a');
						link.href = URL.createObjectURL(blob);
						link.download = `Data_sppd_${bulan_tahun}_${np}_${jenis_perjalanan} .xlsx`;
						link.click();
					} else {
						console.error('Error downloading file:', response.status);
					}
				} catch (error) {
					console.error('Error:', error);
				} */
			}
			
			$(document).ready(function() {
                $('.select2').select2();
				$("#form_absence_type").hide();
				$("#form_jumlah_bulan").hide();
				$("#form_jumlah_hari").hide();
				$('#tabel_sppd').DataTable().destroy();				
				table_serverside();
			});
            
            function refresh_table_serverside() {
				$('#tabel_sppd').DataTable().destroy();				
				table_serverside();
			}
			
			function table_serverside()
			{
				var table;
                var bulan_tahun = $('#bulan_tahun').val() ? $('#bulan_tahun').val() : 0;
                var np = $('#np').val() ? $('#np').val() :  "-";
                var jenis_perjalanan = $('#jenis_perjalanan').val() ? $('#jenis_perjalanan').val() : "-";

                //datatables
                table = $('#tabel_sppd').DataTable({ 

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
                        "url": "<?php echo site_url("perjalanan_dinas/Sppd/tabel_sppd/")?>" + bulan_tahun + '/' + np + '/' + jenis_perjalanan,
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
             url: "<?php echo base_url('perjalanan_dinas/Sppd/ajax_getNama');?>",
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
             url: "<?php echo base_url('perjalanan_dinas/Sppd/ajax_getListNp');?>",
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
		
		