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
					<div class='col-lg-2'>
						<select class="form-control select2" name="karyawan" id="karyawan" onchange="refresh_table_serverside()">
							<?php if ($_SESSION["grup"]!=5) { ?>
							<option value='all'>Pilih Semua Karyawan</option>
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

					<div class='col-lg-1'>Status</div>
					<div class='col-lg-2'>
						<select class="form-control select2" name="filter_status" id="filter_status" onchange="refresh_table_serverside()">
							<option value='all'>Semua Status</option>
							<option value='1'>Disetujui</option>
							<option value='2'>Dalam proses</option>
						</select>
					</div>

					<div class='col-lg-1'>Vendor</div>
					<div class='col-lg-2'>
						<select class="form-control select2" name="vendor" id="vendor" onchange="refresh_table_serverside()">
							<option value='all' selected>Semua Vendor</option>
							<option value='1'>Reimbursement</option>
							<option value='2'>Non Reimbursement</option>
						</select>
					</div>

					<div class='col-lg-1'>Tahun</div>
					<div class='col-lg-2'>
						<select class="form-control select2" name="tahun" id="tahun" onchange="refresh_table_serverside()">
							<option value='all'>Semua Tahun</option>
                            <?php foreach($list_tahun as $row){?>
							<option value='<?= $row->tahun?>'><?= $row->tahun?></option>
                            <?php } ?>
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
										<th class='text-center no-sort'>NO</th>
										<th class='text-center no-sort'>Nomor Bill</th>
										<th class='text-center no-sort'>Karyawan</th>
										<th class='text-center no-sort'>Nama Vendor</th>
										<th class='text-center no-sort'>Tanggal Berobat</th>
										<th class='text-center no-sort'>Status</th>
										<th class='text-center no-sort'>Deskripsi Periksa</th>
										<th class='text-center no-sort'>Jumlah<br>Hari</th>
										<th class='text-center no-sort'>Total Beban<br>Karyawan</th>
										<th class='text-center no-sort'>Total Tanggungan<br>Karyawan</th>
										<th class='text-center no-sort'>Total Tanggungan<br>Perusahaan</th>
										<th class='text-center no-sort'>Aksi</th>
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
		
		
        <script src="<?= base_url('asset/fixedcolumns/3.3.2/js/dataTables.fixedColumns.min.js')?>"></script>
		<script type="text/javascript">	
            var table;
			$(document).ready(function() {
				$("#log_mcu").val("yes");
				table_serverside();
				$("#log_mcu").val("no");
			});		
			
			function refresh_table_serverside() {
				$("#log_mcu").val("yes");
                table.destroy();
				table_serverside();
				$("#log_mcu").val("no");
			}
            
			function table_serverside(){
				var karyawan = $('#karyawan').find(':selected').val();
				var vendor = $('#vendor').find(':selected').val();
				var filter_status = $('#filter_status').find(':selected').val();
				var tahun = $('#tahun').find(':selected').val();
				
				//$('#tabel_mcu').DataTable().destroy();				
				//datatables
				table = $('#tabel_mcu').DataTable({
                    scrollX:true,
                    scrollCollapse: true,
                    fixedColumns: {
                        leftColumns: 5
                    },
					destroy:true,
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
                    searching:true,
					"bFilter": false,
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("sikesper/biaya_kesehatan/tabel/")?>"+karyawan.toString()+"/"+vendor.toString()+'/'+filter_status+'/'+tahun,
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
                    "drawCallback": function() {
                        sum_tagihan_perusahaan();
                    }
				});
                return true;
			}
            
            function sum_tagihan_perusahaan(){
                let karyawan = $('#karyawan').val();
				let vendor = $('#vendor').val();
				let filter_status = $('#filter_status').val();
				let tahun = $('#tahun').val();
                $('#tabel_mcu_filter').html('Menghitung Total...');
                $.ajax({
                    url: '<?= base_url('sikesper/biaya_kesehatan/sum_tagihan_perusahaan') ?>',
				    type: "POST",
                    dataType: "json",
                    data: {np: karyawan, vendor: vendor, status: filter_status, tahun: tahun},
                    success: function(response){
                        $('#tabel_mcu_filter').html('<h4><b>Total: '+response.value+'</b></h4>'+response.teks);
                    },
                    error: function(e){
                        console.log(e);
                        $('#tabel_mcu_filter').html('<h4><b>Total: Rp. 0</b></h4>');
                    }
				});
                return true;
            }
			
			function tampil_rincian(element){
				document.getElementById("judul_rincian").innerHTML = element.parentElement.previousSibling.previousSibling.innerHTML;
			}
			
			$(document).on( "click", '.lihat_button',function(e) {		
				var id = $(this).data('id');
	
				$.ajax({
				type: "POST",
				dataType: "html",
				url: document.getElementById("base_url").value+"sikesper/hasil_mcu/ajax_get_mcu/"+id+"/",
				data: "id="+id,
				success: function(msg){
						if(msg == ''){
							alert ('Terjadi Kesalahan');						
							$('#list_detail_payment').text('');
						}else{								
							$('#list_detail_payment').html(msg);														
						}													  
					 }
				 });       
			 
									
			});

			$(document).on('click', '.detail-periksa', function() {
				let np = $(this).data('no');
				let bill = $(this).data('bill');
				let tgl = $(this).data('tgl');

				$.ajax({
					type: "POST",
					dataType: "json",
					url: "<?= base_url('sikesper/biaya_kesehatan/getDetailPeriksa') ?>",
					data: {np: np, bill: bill, tgl: tgl},
					success: function(res) {
						$('#detail-content').html(res.response);
					}
				});
			});	

		</script>