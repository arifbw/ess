		<style>
			.modal-dialog {
			  width: 95%;
			  height: 90%;
			  margin-right: 40px;
			  margin-left: 40px;
			  padding: 0;
			}

			.modal-content {
			  height: auto;
			  min-height: 100%;
			  border-radius: 0;
			}
		</style>

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

				if($this->akses["lihat"]) { ?>
				<div class="row">
					<div class='col-lg-1'>Karyawan</div>
					<div class='col-lg-4'>
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
				<input type="hidden" name="log_mcu" id="log_mcu" value="no">

				<div class="form-group">	
					<div class="row">
						<div class="col-lg-12 table-responsive">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_mcu">
								<thead>
									<tr>
										<th class='text-center no-sort'>No</th>
										<th class='text-center no-sort'>NP Karyawan</th>
										<th class='text-center no-sort'>Task Type</th>
										<th class='text-center no-sort'>Task On</th>
										<th class='text-center no-sort'>Claim Selanjutnya</th>
										<th class='text-center no-sort'>Nama Keluarga</th>
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
		<!-- Modal lihat -->
		<div class="modal fade" id="modal_lihat" tabindex="-1" role="dialog" aria-labelledby="label_modal_lihat" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="label_modal_np"><span id="judul_rincian"></span></h4>
					</div>
					<div class="modal-body">	
						<p name='list_detail_payment' id='list_detail_payment'></p>
					</div>										
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
		<!-- /.modal -->

		<!-- Modal lihat -->
		<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="detail-title" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="detail-title"><strong>Riwayat Periksa</strong></h4>
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
				//var vendor = $('#vendor').val(); heru PDS comment vendor, id di html nya gak ada, 2021-02-10
				
				$('#tabel_mcu').DataTable().destroy();				
				//datatables
				table = $('#tabel_mcu').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"bFilter": false,
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("sikesper/riwayat_kacamata/tabel/")?>"+karyawan,//+"/"+vendor, heru PDS comment vendor, id di html nya gak ada, 2021-02-10
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
			
			function tampil_rincian(element){
				document.getElementById("judul_rincian").innerHTML = element.parentElement.previousSibling.previousSibling.innerHTML;
			}

			$(document).on('click', '.detail-periksa', function() {
				let np = $(this).data('no');
				let bill = $(this).data('bill');
				let tgl = $(this).data('tgl');

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "<?= base_url('sikesper/riwayat_kacamata/getDetailPeriksa') ?>",
					data: {np: np, bill: bill, tgl: tgl},
					success: function(res) {
						$('#detail-content').html(res.response);
					}
				});
			});	

		</script>