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
					//if($this->akses["lihat"]){
				?>		
                        <div class="form-group">
							<div class="row">
								<div class="col-lg-3">
									<div class="form-group">
										<label for="date-range-filter">Filter Karyawan</label>
										<select class="form-control select2" name="karyawan" id="karyawan" onchange="refresh_table_serverside();" style="width: 100%;">
											<option value="">Semua Karyawan</option>
											<?php
											foreach ($np_karyawan as $key) {
												//$selected = ($key->no_pokok == $this->session->userdata('no_pokok') ? 'selected' : '');
												echo "<option value='{$key->no_pokok}'>{$key->no_pokok} - {$key->nama}</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label for="date-range-filter">Filter Kategori Pelatihan</label>
										<select class="form-control select2" name="kategori_pelatihan" id="kategori_pelatihan" onchange="pilihan_pelatihan()" style="width: 100%;">
										<!-- <select class="form-control select2" name="kategori_pelatihan" id="kategori_pelatihan" onchange="refresh_table_serverside();" style="width: 100%;"> -->

											<option value="">Semua Kategori Pelatihan</option>
											<?php
											foreach ($kategori_pelatihan as $key) {
												//$selected = ($key->no_pokok == $this->session->userdata('no_pokok') ? 'selected' : '');
												echo "<option value='{$key->id}'>{$key->nama_kategori_pelatihan}</option>";
											}
			
									
											// for($i=0;$i<count($daftar_akses_unit_kerja);$i++){
											// 	if(strcmp($daftar_akses_unit_kerja[$i]["kode_unit"],$this->session->userdata("kode_unit"))==0){
											// 		$selected="selected=selected";
											// 	}
											// 	else{
											// 		$selected="";
											// 	}
											// 	echo "<option value='".$daftar_akses_unit_kerja[$i]["kode_unit"]."' $selected>".$daftar_akses_unit_kerja[$i]["kode_unit"]." - ".$daftar_akses_unit_kerja[$i]["nama_unit"]."</option>";
											// }
						
											?>
										</select>
									</div>
								</div>
								<div class="col-lg-3">
									<div class="form-group">
										<label for="date-range-filter">Filter Pelatihan</label>
										<select class="form-control select2" name="pelatihan" id="pelatihan" onchange="refresh_table_serverside();" style="width: 100%;">
											<option value="">Semua Pelatihan</option>
											<?php
											foreach ($pelatihan as $key) {
												echo "<option value='{$key->id}'>{$key->kode_pelatihan} - {$key->nama_pelatihan}</option>";
											}
											?>
										</select>
									</div>
								</div>
                                <!-- <div class="col-lg-3">
                                    <label>Bulan</label>
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
                                </div> -->

								<div class="col-lg-8 pull-right">
        							<label>&nbsp;</label>
        							<button type="button" class="btn btn-success pull-right" id="btn-export-excel"><i class="fa fa-file-excel-o"></i> Export Excel</button>
        						</div>
							</div>																				
						</div>
						
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_pelatihan">
									<thead>
										<tr>
											<th class='text-center no-sort'>No</th>
											<th class='text-center'>NP</th>	
											<th class='text-center'>Nama</th>
											<th class='text-center no-sort'>Unit Kerja</th>	
											<th class='text-center no-sort'>Kategori Pelatihan</th>
											<th class='text-center no-sort'>Kode Pelatihan</th>
											<th class='text-center no-sort'>Pelatihan</th>
											<th class='text-center no-sort'>Skala Prioritas</th>
											<th class='text-center no-sort'>Rekomendasi Vendor</th>			
										</tr>
									</thead>
									<tbody>
									
									</tbody>
								</table>
								<!-- /.table-responsive -->
							</div>						
						</div>
	

				
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?php base_url()?>/ess/asset/cartenz/sweetalert2.all.min.js"></script>
		<script type="text/javascript">
	
			$(document).ready(function() {
				$('#tabel_ess_pelatihan').DataTable().destroy();	
				$('.select2').select2();	
				table_serverside();
			});
			
			 function refresh_table_serverside() {
				$('#tabel_ess_pelatihan').DataTable().destroy();				
				table_serverside();
			}
			
			function table_serverside()
			{
				var table;
				var bulan_tahun = $('#bulan_tahun').val(); // x
				var np_karyawan = $('#karyawan').val();
				var id_kategori_pelatihan = $('#kategori_pelatihan').val();
				var id_pelatihan = $('#pelatihan').val();
				var filter = $('#filter').val(); // x
				
				//datatables
				table = $('#tabel_ess_pelatihan').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"searching": true,
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
							"url": "<?php echo site_url('pelatihan/kebutuhan_pelatihan/tabel_ess_pelatihan/')?>" + bulan_tahun + "/" + filter,
							"type": "POST",
							"data": {
								'np_karyawan': np_karyawan,
								'id_kategori_pelatihan' : id_kategori_pelatihan,
								'id_pelatihan' : id_pelatihan,
        					}
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
			function pilihan_pelatihan(){
				var id_kategori_pelatihan = $('#kategori_pelatihan').val();
				
				$.ajax({
					type: "post",
					url: document.getElementById("base_url").value+"pelatihan/kebutuhan_pelatihan/daftar_pelatihan/",
					dataType: "json",
					data: {
						id_kategori_pelatihan : id_kategori_pelatihan,
					},
					success: function (data) {
						let selectElement = document.getElementById('pelatihan');
							selectElement.innerHTML = '';
							let option = document.createElement('option');
							option.value = '';
							option.text = 'Semua Pelatihan';
							// option.selected = true;
							// option.disabled = true;
							selectElement.appendChild(option);
							data.data.forEach(item => {
								let option = document.createElement('option');
								option.value = item['id'];
								option.text = item['kode_pelatihan'] + " - " + item['nama_pelatihan'];
								selectElement.appendChild(option);
							});
						
						refresh_table_serverside();
					}
				});
			}
		</script>

		 <script>
        	$('#btn-export-excel').on('click', () => {
        		var np_karyawan = $("#karyawan").val();
				var id_kategori_pelatihan = $("#kategori_pelatihan").val();
				var id_pelatihan = $("#pelatihan").val();
        		window.open(`<?= base_url() ?>pelatihan/kebutuhan_pelatihan/generateExcel?np_karyawan=${np_karyawan}&id_kategori_pelatihan=${id_kategori_pelatihan}&id_pelatihan=${id_pelatihan}`, '_blank');
        	})
        </script>
		
		