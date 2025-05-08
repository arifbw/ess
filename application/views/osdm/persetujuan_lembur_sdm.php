		
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css')?>" />
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
				if(!empty($this->session->flashdata('warning'))) { ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('warning');?>
				</div>
				<?php }
				if(@$akses["lihat log"]) {
				echo "<div class='row text-right'>";
					echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
					echo "<br><br>";
				echo "</div>";
				} ?>
				<?php if(@$akses["lihat"]) { ?>
				<div class="form-group">
					<div class="row">
						<div class="col-md-3">
		                    <label>Filter Persetujuan</label>
							<select class='form-control' id='filter'>
								<option value='all'>Semua</option>
								<option value='0'>Belum Disetujui</option>
								<option value='1'>Disetujui</option>
								<option value='2'>Tidak Disetujui</option>
								<option value='3'>Tidak Diakui</option>
							</select>
						</div>
						<div class="col-md-6">
	                    	<label>Filter Tanggal Lembur</label>
							<div class="form-group">
								<div class="row">
									<div class="col-md-12">
										<input type="text" name="daterange" class="form-control" value="" />
									</div>
								</div>
							</div>
						</div>
						<?php if(@$akses["persetujuan"]) { ?>
						<div class="col-md-3 pull-right">
		                    <br>
							<button class="btn btn-success btn-md" data-toggle='modal' data-target='#modal_approve_all'>Setujui Semua Lembur</button>
						</div>
						<?php } ?>
					</div>
				</div>
								
				<div class="form-group">	
					<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_lembur_sdm">
						<thead>
							<tr>
								<th class='text-center'>No</th>
								<th class='text-center'>Nomor Pokok</th>	
								<th class='text-center'>Nama Pegawai</th>	
								<th class='text-center'>Tertanggal</th>			
								<th class='text-center'>Waktu Mulai</th>			
								<th class='text-center'>Waktu Selesai</th>
								<th class='text-center'>Lembur Diakui</th>											
								<!-- <th class='text-center'>Tipe</th> -->
								<th class='text-center'>Status</th>											
								<th class='text-center no-sort'>Aksi</th>											
							</tr>
						</thead>
						<tbody>
						
						</tbody>
					</table>	
				</div>
				<?php } ?>	
				
				<?php if(@$akses["persetujuan"] || @$akses["lihat"]) { ?>
				<!-- Modal -->
				<div class="modal fade" id="show_modal_approve" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_batal"><?= $judul ?></h4>
							</div>
							<div class="modal-body">
								<div class="get-approve"></div>
							</div>
						</div>
					</div>
				</div>
				<!-- /.modal -->
				<?php } ?>
				<?php if(@$akses["persetujuan"]) { ?>
				<!-- Modal -->
				<div class="modal fade" id="modal_approve_all" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_batal">Approve Semua Permohonan</h4>
								<p>Permohonan lembur pada bulan yang dipilih akan disetujui</p>
							</div>

							<div class="modal-body">		
								<form role="form" action="<?= site_url('osdm/persetujuan_lembur_sdm/approve_all') ?>" method="post">
									<div class="alert alert-info">
										<strong><strong><?= $_SESSION["no_pokok"]?> | <?= nama_karyawan_by_np($_SESSION["no_pokok"])?> </strong><br></strong>
										Sebagai Approval Lembur oleh SDM
										<br>
										<p>Anda akan menyetujui semua Permohonan Lembur yang <strong> belum diapprove </strong> oleh SDM</p>
									</div>
									
									<div class="row">
										<div class="form-group">
											<div class="col-lg-3">
												<label>Bulan</label>
											</div>
											<div class="col-lg-9">
												<select class='form-control' name="bulan_tahun">
													<?php if(!in_array($bulan, array_column($month_list, 'bln'))) { ?>
													<option value='<?= $bulan ?>' selected><?= id_to_bulan(substr($bulan, -2)).' '.substr($bulan, 0, 4) ?></option>
													<?php } ?>
													<?php foreach ($month_list as $ls) { ?>
													<option value='<?= $ls['bln'] ?>' <?= ($bulan==$ls['bln']) ? 'selected' : ''; ?> ><?= id_to_bulan(substr($ls['bln'], -2)).' '.substr($ls['bln'], 0, 4) ?></option>
													<?php } ?>
												</select>
											</div>													
										</div>
									</div>

									<div class="row">
										<br>
										<div class="form-group">
											<div class="col-lg-3">
												<label>Jenis Persetujuan</label>
											</div>
											<div class="col-lg-9">
												<select class='form-control' name="jenis_approval">
													<option value='pimpinan'>Setujui Hanya Yang Sudah Disetujui Pimpinan</option>
													<option value='semua'>Setujui Semua Lembur (Kecuali yg ditolak pimpinan)</option>
												</select>
											</div>													
										</div>
									</div>
									
									<div class="row">
										<br>
										<div class="form-group">
											<div class="col-lg-offset-3 col-lg-4 col-lg-offset-5">		
												<button type="submit" name="submit" value="submit" class="btn btn-block btn-success">Simpan</button>
											</div>
										</div>
									</div>
								</form>				
							</div>	
						</div>
					</div>
				</div>
				<!-- /.modal -->
				<?php } ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		

<script type="text/javascript" src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js')?>"></script>
<script>
$(document).ready(function() {
  $('input[name="daterange"]').daterangepicker({
    opens: 'left',
    autoUpdateInput: false,
    locale: {
        cancelLabel: 'Clear',
        format: 'DD-MM-YYYY'
    }
  });
  $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('DD-MM-YYYY') + ' s/d ' + picker.endDate.format('DD-MM-YYYY'));
      ev.preventDefault();
	  refresh_table_serverside();
  });
  $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
      ev.preventDefault();
	  refresh_table_serverside();
  });
});
</script>
		<script type="text/javascript">	
			var lembur_table;
			$(document).ready(function() {
				table_serverside()

				$(document).on('click','#modal_approve',function(e){
		            e.preventDefault();
		            $("#show_modal_approve").modal('show');
		            $.post('<?php echo site_url("osdm/persetujuan_lembur_sdm/view_approve") ?>',
		                {id_pengajuan:$(this).attr('data-id-pengajuan')},
		                function(e){
		                    $(".get-approve").html(e);
		                }
		            );
		        });

				$(document).on('change','#filter',function(e){
		            e.preventDefault();
		            refresh_table_serverside();
		        });
				// $.LoadingOverlaySetup({
		  //           image: "https://loading.io/spinners/balls/index.circle-slack-loading-icon.gif"
		  //       });	
				//table_serverside();
				

				// $('#filter').on( 'change', function () {
				//     lembur_table
				//         .columns( 7 )
				//         .search( this.value )
				//         .draw();
				// } );
			});		
			
			function refresh_table_serverside() {
				$('#tabel_ess_lembur_sdm').DataTable().destroy();				
				table_serverside();
			}

			function table_serverside() {			
				lembur_table = $('#tabel_ess_lembur_sdm').DataTable({ 					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"processing": "Sedang memuat data pengajuan lembur", //Feature control the processing indicator.
						"emptyTable": "Tidak ada data di database"
					},
					
					"stateSave": true,
					"responsive": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						 "url"	: "<?php echo site_url("osdm/persetujuan_lembur_sdm/tabel_ess_lembur_sdm/")?>",
						 "data"	: {status: $('#filter').val(), daterange: $('input[name="daterange"]').val()},						 
						 "type"	: "POST"
					},

					//Set column definition initialisation properties.
					"columnDefs": [
					{ 
						"targets": 'no-sort', //first column / numbering column
						"orderable": false, //set not orderable
					}],
	                drawCallback: function () { 
	                    //$('#tabel_ess_lembur_sdm').LoadingOverlay("hide");
	                }
				});
			};		
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
				var persetujuan_id_sdm = $(this).data('id');
				var persetujuan_np_karyawan = $(this).data('np-karyawan');
				var persetujuan_nama = $(this).data('nama');
				var persetujuan_created_at = $(this).data('created-at');
				var persetujuan_created_by = $(this).data('created-by');
				var persetujuan_approval_nama_sdm = $(this).data('approval-nama-sdm');
				var persetujuan_approval_sdm = $(this).data('approval-sdm');
				
				$("#persetujuan_id_sdm").val(persetujuan_id_sdm);		
				$("#persetujuan_np_karyawan").text(persetujuan_np_karyawan);					
				$("#persetujuan_nama").text(persetujuan_nama);
				$("#persetujuan_created_at").text(persetujuan_created_at);	
				$("#persetujuan_created_by").text(persetujuan_created_by);
				
				$("#persetujuan_approval_nama_sdm").text(persetujuan_approval_nama_sdm);	
				document.getElementById("persetujuan_approval_sdm").value = persetujuan_approval_sdm;
			});
		</script>
		
		