		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
        <link rel="stylesheet" href="<?= base_url('asset/rateYo/2.3.2/jquery.rateyo.min.css')?>">
		
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
                
                <div class="alert alert-dismissable" id="alert-message" style="display: none;"></div>
                
			<?php
					if(!empty($this->session->flashdata('success'))){
				?>
						<div class="alert alert-success alert-dismissable" id="alert-success">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $this->session->flashdata('success');?>
						</div>
				<?php
					}
					if(!empty($this->session->flashdata('warning'))){
				?>
						<div class="alert alert-danger alert-dismissable" id="alert-danger">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $this->session->flashdata('warning');?>
						</div>
				<?php
					}
					if(@$akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					if($akses["tambah"]){
				?>
                    
                    <div class="alert alert-info alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>					
                        Dalam rangka <i>physical distancing</i> selama masa pandemi Covid-19, maka kapasitas kendaraan tersedia untuk Innova 4 orang, Hiace 8 orang, Elf 11 orang, Bus 20 orang.
                    </div>
                
                    <div class="row">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a href="<?php echo base_url('food_n_go/kendaraan/pesan_kendaraan_oleh_pengelola/input_data_pemesanan') ?>">Tambah Pesanan</a>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <input type='hidden' id='insert_tampil_bulan_tahun' name='insert_tampil_bulan_tahun'>
				<?php
					}
					
					if($this->akses["lihat"]){
				?>
					
					<p id="demo"></p>
						<div class="form-group">
							<div class="row">
								<div class="col-md-3">
									<label>Bulan</label>
									<!--<select id="pilih_bulan_tanggal" class="form-control">-->
									<select class="form-control" id='bulan_tahun' name='bulan_tahun' onchange="refresh_table_serverside()" style="width: 200px;">
										<option value=''></option>	
									<?php 
									$tampil_bulan_tahun=date("m-Y");
									foreach ($array_tahun_bulan as $value) {
										
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
										<option value='<?php echo $value?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,0,2))." ".substr($value,3,4)?></option>								
										
									<?php 
									}
									?>
									</select>
								</div>
                                <input type="hidden" name="bulan" value="" id="get_month" />
                                <input type="hidden" name="bulan" value="" id="get_month_per_unit" />
							</div>
						</div>
                        
						<div class="form-group">	
							<div class="row">
								<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data_pemesanan">
									<thead>
										<tr>
											<th class='text-center' style="width: 5%;">No</th>
											<th class='text-center' style="width: 10%;">No. Pemesanan</th>
											<th class='text-center'>Nama</th>
											<th class='text-center'>Asal</th>
											<th class='text-center'>Tujuan</th>
											<th class='text-center'>Berangkat</th>
											<th class='text-center'>Penumpang</th>
											<th class='text-center'>Kendaraan</th>
											<th class='text-center'>Approver</th>
											<th class='text-center'>Status</th>
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
					
					if(@$akses["ubah"]){
				?>
						<!-- Modal -->
						
				<?php
					}
				?>
                <div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_detail" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content" id="modal-content-detail"></div>
                    </div>
                </div>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		
		<script type="text/javascript">	
            var table;			
			$(document).ready(function() {
				
				//lempar ke modal cetak
				document.getElementById('get_month').value = $('#bulan_tahun').val();
				document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
                
                $('.select2').select2();
				$('#tabel_data_pemesanan').DataTable().destroy();									
				table_serverside();
			});		
			
			function refresh_table_serverside() {
				document.getElementById('get_month').value = $('#bulan_tahun').val();
				document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
				$('#tabel_data_pemesanan').DataTable().destroy();				
				table_serverside();
			}
            
            function table_serverside() {
                var bulan_tahun = $('#bulan_tahun').val();
			
				//datatables
				table = $('#tabel_data_pemesanan').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					"stateSave": true,
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"ordering": false, //Initial no order.
					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("food_n_go/kendaraan/pesan_kendaraan_oleh_pengelola/tabel_data_pemesanan/")?>"+bulan_tahun,
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
            }
            
            function show_detail(input){
                var url = '<?= base_url('food_n_go/kendaraan/pesan_kendaraan_oleh_pengelola/show_detail/')?>'+input.dataset.id;

                $("#modal-content-detail").html('Loading...');
                $("#modal_detail").modal('show');
                $.post(url).done(function (data) {
                    $("#modal-content-detail").html(data);
                });
            }

            function selesaikan(input){
                var alert_text = '';
                if($('#alert-message').hasClass('alert-success')){
                    $('#alert-message').removeClass('alert-success');
                }
                if($('#alert-message').hasClass('alert-danger')){
                    $('#alert-message').removeClass('alert-danger');
                }

                var result = confirm("Selesaikan pesanan ini?");
                if (result) {
                    $.when(
                        $.ajax({
                            url: "<?php echo base_url('food_n_go/kendaraan/data_pemesanan/selesaikan_pesanan');?>",
                            type: "POST",
                            dataType: "json",
                            data: {kode: input.dataset.kode},
                            success: function(response){
                                if(response.status==true){
                                    $('#alert-message').addClass('alert-success');
                                } else if(response.status==false){
                                    $('#alert-message').addClass('alert-danger');
                                }
                                $('#alert-message').html(alert_text + response.message);
                                $('#alert-message').show();
                                table.draw(false);
                            },
                            error: function(){
                                $('alert-message').html(alert_text + 'Gagal terhubung ke server');
                                $('#alert-message').show();
                            }
                        })
                    ).done(function(){
                        setTimeout(function(){ $('#alert-message').hide(); }, 3000);
                    });
                }
            }

            function add_rate(input){
                var url = '<?= base_url('food_n_go/kendaraan/data_pemesanan/add_rate/')?>'+input.dataset.id;

                $("#modal-content-detail").html('Loading...');
                $("#modal_detail").modal('show');
                $.get(url).done(function (data) {
                    $("#modal-content-detail").html(data);
                });
            }

            $('#modal_detail').on('hidden.bs.modal', function () {
                table.draw(false);
            })
        </script>
	