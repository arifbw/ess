
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

				<?php if(!empty($success)){ ?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo ($this->session->flashdata('success') != null) ? $this->session->flashdata('success') : $success;?>
				</div>
				<?php }
				if(!empty($warning)) { ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo ($this->session->flashdata('warning') != null) ? $this->session->flashdata('warning') : $warning;?>
				</div>
				<?php }
				if($akses["lihat log"]) { ?>
				<div class='row'>
					<div class="form-inline pull-left">
						<div class="form-group">
							<label>Pilih Tahun : </label>
							<select class="form-control" onchange="change_tahun_libur(this.value)" style="width: 200px;">
								<?php
								foreach($daftar_tahun as $row){
									echo '<option value="'.$row['tahun'].'"'.(($row['tahun'] == date('Y'))?' selected=""':'').'>'.$row['tahun'].'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="pull-right">
						<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
					</div>
				</div>
				<hr />
				<?php }
				if($akses["tambah"]) { ?>
				<!-- <div class="row">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a href="<?php echo base_url('lembur/pengajuan_lembur/input_pengajuan_lembur') ?>">Tambah <?php echo $judul;?></a>
							</h4>
						</div>
					</div>
				</div> -->
				<?php }
				if($this->akses["lihat"]) { ?>
				<!-- <div class="row">
					<div class="form-group">
						<div class="pull-left">
	                        <label>Filter Bulan</label>
							<div class="row">
								<div class="col-md-12">
									<select class='form-control' id='filter'>
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

						<div class="pull-right">
							<form action="<?php echo base_url('lembur/pengajuan_lembur/export') ?>" method="POST">
								<div class="form-group">
			                        <label>Pilih Tanggal</label> <button class="btn btn-success btn-xs" type="submit">Cetak </button>
									<div class="row">
										<div class="col-md-3">
											<input type="date" name="filter_tgl">
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div> -->
				<br>
				<div class="row" id="frame_tabel_ess_cuti">
					<table style="width: 100%;" class="table table-striped table-bordered table-hover" id="tabel_ess_cuti">
						<thead>
							<tr>
								<th class='text-center' style="width: 50px;">No</th>
								<th class='text-center' style="width: 240px;">Karyawan</th>
								<?php foreach ($daftar_cuti as $val_cuti) { ?>
								<th class='text-center'><span title="<?= $val_cuti['deskripsi'] ?>"><?= date('d/m/Y', strtotime($val_cuti['tanggal'])) ?></span></th>
								<?php } ?>
							</tr>
						</thead>
					</table>
					<!-- /.table-responsive -->
				</div>
				<?php }
				if($akses["ubah"]) { ?>
				<!-- Modal -->
				<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form role="form" action="" id="formulir_ubah" method="post">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul;?></h4>
								</div>
								<div class="modal-body">
									<input type="hidden" name="aksi" value="ubah"/>
									<div id='isian_ubah_lembur'></div>
								</div>
								<div class="modal-footer">
									<button type="submit" class="btn btn-primary" onclick="return cek_simpan_ubah()">Simpan</button>
									<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
								</div>
							</form>
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->

				<!--begin: Modal Inactive -->
		      	<div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			        <div class="modal-dialog modal-sm" role="document">
			          	<div class="modal-content">
			            	<div class="modal-header">
			              		<h5 class="modal-title text-danger" id="title-inactive">
			                		<b>Hapus <?= $judul ?></b>
			              		</h5>
			              		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			                		<span aria-hidden="true">&times;</span>
			              		</button>
			            	</div>
			            
			            	<div class="modal-body">
			              		<h6 id="message-inactive"></h6>
			            	</div>
			            	<div class="modal-footer">
			              		<button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
			              		<a href="" id="inactive-action" class="btn btn-primary">Ya, Hapus</a>
			            	</div>
			          	</div>
			        </div>
		      	</div>
		      	<!--end: Modal Inactive -->
				<?php } ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->

	<script src="<?php echo base_url('asset/notify/bootstrap-notify.min.js'); ?>"></script>
	<script type="text/javascript">
		var lembur_table;
		var data_columns = [];
		var html_columns;
		
		$(function () {
		  $('[data-toggle="tooltip"]').tooltip()
		});

		$(document).ready(function() {
			get_column_header(<?php echo date('Y'); ?>);
			setTimeout(function(){
				table_serverside(<?php echo date('Y'); ?>);
			}, 1000);

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

			$(document).on('click','#cetak',function(e){
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
		});

		function change_data_cuti(cuti, np, tanggal)
		{
			$.ajax({
				url: "<?php echo site_url("lembur/cuti_besar/action_cuti_besar") ?>",
				method: "POST",
				data: {
					np_karyawan: np,
					tgl: tanggal,
					cuti: cuti
				},
				dataType: 'json',
				success:function(response) {
					if(response.success){
						$.notify({
							message: response.message
						},{
							type: 'success',
							timer: 1000,
							placement: {
								from: "top",
								align: "right"
							},
						});
					}
					else{
						$.notify({
							message: response.message
						},{
							type: 'danger',
							timer: 1000,
							placement: {
								from: "top",
								align: "right"
							},
						});
					}
				},
				error:function(){
					$.notify({
						message: "error, gagal terhubung ke server"
					},{
						type: 'success',
						timer: 1000,
						placement: {
							from: "top",
							align: "right"
						},
					});
				}
			});
		}

		function change_tahun_libur(tahun){
			$('#tabel_ess_cuti').DataTable().destroy();
			$('#tabel_ess_cuti tbody').empty();

			/*lembur_table.ajax.url('<?php echo site_url("lembur/cuti_besar/tabel_ess_cuti/")?>'+tahun).load();*/
			get_column_header(tahun);
			setTimeout(function(){
				$('#tabel_ess_cuti thead tr').html(html_columns);
				table_serverside(tahun);
			}, 1000);
		}

		function get_column_header(tahun){
			$.getJSON("<?php echo site_url("lembur/cuti_besar/header_ess_cuti/")?>"+tahun, function(hasil){
				data_columns = hasil.data;
				html_columns = hasil.html;
			});
		}
		
		function refresh_table_serverside() {
			$('#tabel_ess_lembur_sdm').DataTable().destroy();
			table_serverside();
		}

		function filter_pengajuan(isi) {
			console.log(isi);
			//$('#tabel_ess_lembur_sdm').LoadingOverlay("show");
            lembur_table.column(6).search(isi).draw();
        }

        function table_serverside(tahun) 
		{
			lembur_table = $('#tabel_ess_cuti').DataTable({
				"language": {
					"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
					"sEmptyTable": "Tidak ada data di database",
					"processing": "Sedang memuat data pengajuan cuti",
					"emptyTable": "Tidak ada data di database"
				},
				"scrollX": true,
				"processing": true,
				"serverSide": true,
				"ordering": false,
				"ajax": {
					 "url": "<?php echo site_url("lembur/cuti_besar/tabel_ess_cuti/")?>"+tahun,
					 "data": {bln: $('#filter').val()},
					 "type": "POST",
					 "dataSrc": 'data'
				},
				"columns": data_columns,
				"columnDefs": [
				{
					"targets": 'no-sort',
					"orderable": false,
				}]
			});
		};	

		function hapus(id) {
			//console.log(id);
	    	var url = "<?php echo site_url("lembur/pengajuan_lembur/hapus") ?>/";
	    	$('#inactive-action').prop('href', url+id);
	    	$('#message-inactive').text('Apakah anda yakin ingin menghapus pengajuan lembur ini ?');
	    	$('#modal-inactive').modal('show');
		}
	</script>
