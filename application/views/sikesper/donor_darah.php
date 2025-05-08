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

				<?php if(!empty($this->session->flashdata('success'))){ ?>
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
					<div class='col-lg-1'>Karyawan</div>
					<div class='col-lg-5'>
						<select class="form-control select2" name="karyawan" id="karyawan" onchange="refresh_table_serverside()">
						<?php if ($_SESSION["grup"]!=5) { ?>
							<option value='0'>Pilih Semua Karyawan</option>
						<?php } ?>
						<?php for($i=0;$i<count($daftar_akses_karyawan);$i++){
							if(strcmp($daftar_akses_karyawan[$i]["no_pokok"],$this->session->userdata("no_pokok"))==0){
								$selected="selected=selected";
							}
							else{
								$selected="";
							}
							echo "<option value='".$daftar_akses_karyawan[$i]["no_pokok"]."' $selected>".$daftar_akses_karyawan[$i]["no_pokok"]." - ".$daftar_akses_karyawan[$i]["nama"]."</option>";
						} ?>
						</select>
					</div>			
				</div>

				<br>
				<div class="form-group">	
					<div class="row">
						<div class="col-lg-12 table-responsive">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_mcu">
								<thead>
									<tr>
										<th class='text-center'>NO</th>
										<th class='text-center no-sort'>Karyawan</th>
										<th class='text-center no-sort'>Position</th>
										<th class='text-center no-sort'>Tipe</th>
										<th class='text-center no-sort'>Jumlah<br>Donor</th>
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
						<h4 class="modal-title" id="detail-title"><strong>Riwayat Donor</strong></h4>
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
				$("#log_mcu").val("yes");
				table_serverside();
				$("#log_mcu").val("no");
			});		
			
			function refresh_table_serverside() {
				$("#log_mcu").val("yes");
				table_serverside();
				$("#log_mcu").val("no");
			}
		</script>
		
		<script>		
			function table_serverside(){
				var table;				
				var karyawan = $('#karyawan').val();
				
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
					"order": true, //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("sikesper/donor_darah/tabel/")?>"+karyawan,
						"type": "POST"
					},

					//Set column definition initialisation properties.
					"columnDefs": [
					{ 						
						"targets": [ 0 ], //first column / numbering column
						"targets": 'no-sort', //first column / numbering column
						"orderable": [ 0,5 ], //set not orderable
					},
					],

				});
			
			}	
			
			function tampil_rincian(element){
				document.getElementById("judul_rincian").innerHTML = element.parentElement.previousSibling.previousSibling.innerHTML;
			}
			
			$(document).on('click', '.detail-donor', function() {
				let np = $(this).data('no');

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "<?= base_url('sikesper/donor_darah/getDetailDonor') ?>",
					data: {np: np},
					success: function(res) {
						$('#detail-content').html(res.response);
					}
				});
			});	
		</script>