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

				<?php if(!empty($this->session->flashdata('success'))) { ?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('success');?>
				</div>
				<?php }
				if(!empty($this->session->flashdata('warning'))){ ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('warning');?>
				</div>
				<?php }

				if($this->akses["lihat"]){ ?>
				<div class="row">
					<div class='col-lg-1 text-right'>Unit</div>
					<div class='col-lg-5'>
						<select class="form-control select2" name="unit" id="unit" onchange="ambil_karyawan()">
							<?php if ($this->akses["pilih seluruh karyawan"]) { ?>
							<!--<option value='0'>Pilih Semua Unit</option>-->
							<?php } ?>
							<?php for($i=0;$i<count($daftar_akses_unit);$i++){
								if(strcmp($daftar_akses_unit[$i]["kode_unit"],$this->session->userdata("kode_unit"))==0) {
									$selected="selected=selected";
								}
								else{
									$selected="";
								}
								echo "<option value='".$daftar_akses_unit[$i]["kode_unit"]."' $selected>".$daftar_akses_unit[$i]["kode_unit"]." - ".$daftar_akses_unit[$i]["nama_unit"]."</option>";
							} ?>
						</select>
					</div>			

					<div class='col-lg-1'></div>
					<div class='col-lg-1 text-right'>Karyawan</div>
					<div class='col-lg-4'>
						
						<select class="form-control select2" name="karyawan" id="karyawan" onchange="refresh_table_serverside()"></select>
					</div>			
				</div>

				<br>
				<div class="form-group">	
					<div class="row">
						<div class="col-lg-12 table-responsive">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_mcu">
								<thead>
									<tr>
										<th class='text-center no-sort'>No</th>
										<th class='text-center' style="width:25%">Karyawan</th>
										<th class='text-center'>Tempat<br>Tanggal Lahir</th>
										<th class='text-center'>Usia</th>
										<!--<th class='text-center no-sort'>Tanggal<br>Daftar</th>-->
										<th class='text-center'>Jenis<br>Kelamin</th>
										<th class='text-center'>ID BPJS</th>
										<th class='text-center'>Kelas BPJS</th>
                                        <th class='text-center'>Kelas Perawatan</th>
										<th class='text-center'>Aksi</th>
									</tr>
								</thead>
								<tbody>
								
								</tbody>
							</table>
							<!-- /.table-responsive -->
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<?php if($this->akses["lihat"]){ ?>
		<!-- Modal lihat -->
		<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="detail-title" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="detail-title"><strong>Detail Biodata</strong></h4>
					</div>
					<div class="modal-body">
						<div id="detail-content"></div>	
					</div>										
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->
		<?php } ?>
		
		
		<script type="text/javascript">	
			$(document).ready(function() {
				
				ambil_karyawan();
			});		
			
			function refresh_table_serverside() {
				table_serverside();
			}
		</script>
		
		<script>		
			function table_serverside(){
				var table;				
				var karyawan = $('#karyawan').val();
				var unit = $('#unit').val();
				
				$('#tabel_mcu').DataTable().destroy();				
				//datatables
				table = $('#tabel_mcu').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"bFilter": true,
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("sikesper/biodata/tabel/")?>"+karyawan+"/"+unit,
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

			$(document).on('click', '.detail-keluarga', function() {
				let np = $(this).data('no');
                $('#detail-content').html('');
				$.ajax({
					type: "POST",
					dataType: "html",
					url: "<?= base_url('sikesper/biodata/getDetailkeluarga') ?>",
					data: {np: np},
					success: function(res) {
						//$('#detail-content').html(res.response);
						$('#detail-content').html(res);
					}
				});
			});
            
            function ambil_karyawan(){
                let param_unit = $('#unit').find(':selected').val();
                <?php if ($this->akses["pilih seluruh karyawan"]) { ?>
                $('#karyawan').html('<option value="all">Semua karyawan</option>');
            	<?php } ?>
                $.ajax({
					url: "<?= base_url('sikesper/biodata/ambil_karyawan') ?>",
					type: "POST",
					dataType: "html",
					data: {unit: param_unit},
					success: function(res) {
						$('#karyawan').html(res);
                        table_serverside();
					},
                    error: function(){
                        table_serverside();
                    }
				});
                return true;
            }
		</script>