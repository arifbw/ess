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
					
					if($this->akses["lihat"]){
				?>
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_payslip">
									<thead>
										<tr>
											<th class='text-center no-sort'>No</th>
											<th class='text-center no-sort'>Nama Pembayaran</th>
											<th class='text-center no-sort'>Payment Date (SAP)</th>
											<th class='text-center no-sort'>Penerima</th>
											<th class='text-center no-sort'>Dengan Slip</th>
											<th class='text-center no-sort'>Waktu Publikasi</th>
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
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		
		<script type="text/javascript">	
			$(document).ready(function() {				
				table_serverside();
			});		
			
			function refresh_table_serverside() {
				$('#tabel_payslip').DataTable().destroy();				
				table_serverside();
			}
		</script>
		
		<script>		
			function table_serverside(){
				var table;				
				
				//datatables
				table = $('#tabel_payslip').DataTable({ 
					
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
						"url": "<?php echo site_url("osdm/penggajian/tabel_gaji/")?>",
						"type": "POST"
					},

					//Set column definition initialisation properties.
					"columnDefs": [
						{ 						
							"targets": [ 0 ], //first column / numbering column
							"targets": 'no-sort', //first column / numbering column
							"orderable": false, //set not orderable
						}
					],

				});
			
			}		
		</script>
