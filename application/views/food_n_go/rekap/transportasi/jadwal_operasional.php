		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />

		<!-- Page Content -->
		<div id="page-wrapper">
		    <div class="container-fluid">
		        <div class="row">
		            <div class="col-lg-12">
		                <h1 class="page-header"><?php echo $judul;?></h1>
		            </div>
		        </div>
                
                <div class="alert alert-dismissable" id="alert-message" style="display: none;"></div>
                
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
					if(@$akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
				?>
		        
		        <?php					
					if($this->akses["lihat"]){
				?>
		        <p id="demo"></p>
		        <div class="form-group">
		            <div class="row">
		                <div class="col-md-3">
		                    <label>Pilih Tanggal</label>
		                    <input class="form-control" type="text" id="tanggal" style="width: 200px;" value="<?= date('d-m-Y')?>">
                            <input type="checkbox" id="regenerate"> <small>Perbarui ulang laporan harian?</small>
		                </div>
                        
		                <div class="col-md-3">
		                    <label>Pilih Unit</label>
		                    <select class="form-control" id="unit" style="width: 200px;" onchange="refresh_table_serverside()">
                                <option value="semua" selected>Semua Unit</option>
                                <option value="jakarta">Unit Kendaraan Jakarta</option>
                                <option value="karawang">Unit Kendaraan Karawang</option>
                            </select>
		                </div>
                        
                        <div class="col-md-3">
                            <div style="padding-top: 25px">
                                <button type="button" class="btn btn-success" onclick="proses()" id="btn-cetak">Lanjut <i class="fa fa-chevron-right"></i></button>
                            </div>
                        </div>
		            </div>
		        </div>
                
		        <div class="form-group">
		            <div class="row">
		                <table width="100%" class="table table-striped table-bordered table-hover" id="tabel_laporan_harian">
		                    <thead>
		                        <tr>
		                            <th class='text-center' style="width: 5%;">No</th>
                                    <th class='text-center' style="width: 10%;">No. Pemesanan</th>
		                            <th class='text-center'>Jenis Kendaraan</th>
		                            <th class='text-center'>No Mobil</th>
		                            <th class='text-center'>Bahan Bakar</th>
		                            <th class='text-center'>Driver</th>
		                            <th class='text-center'>Unit Kerja</th>
		                            <th class='text-center'>Asal</th>
		                            <th class='text-center'>Tujuan</th>
		                            <th class='text-center'>Berangkat</th>
		                            <th class='text-center'>PIC</th>
		                            <th class='text-center'>Jam</th>
		                        </tr>
		                    </thead>
		                </table>
		            </div>
		        </div>
		        <?php } ?>

		        <div class="modal fade" id="modal_detail" role="dialog" aria-labelledby="label_modal_detail" aria-hidden="true">
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
                
                $('#tanggal').datetimepicker({
                    format: 'DD-MM-Y'
		        }).on('dp.change', function (event) {
                    refresh_table_serverside();
				});
                
                table_serverside();
		    });
            
            function proses() {
		        var url = '<?= base_url('food_n_go/rekap/transportasi/jadwal_operasional/proses/')?>';
                var tanggal = $('#tanggal').val();
                var unit = $('#unit').val();
                if ($('#regenerate').is(':checked')) {
                    var regenerate = 1;
                } else{
                    var regenerate = 0;
                }
                
		        $("#modal-content-detail").html(`<div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                        <h4 class="modal-title" id="label_modal_detail">Memproses...</h4>
                                                    </div>
                                                </div>`);
		        $("#modal_detail").modal('show');
		        $.get(url + tanggal + '/' + unit + '/' + regenerate).done(function(data) {
		            $("#modal-content-detail").html(data);
		        });
		    }
            
            function refresh_table_serverside() {
				$('#tabel_laporan_harian').DataTable().destroy();				
				table_serverside();
			}
            
            function table_serverside() {
                var tanggal = $('#tanggal').val();
                var unit = $('#unit').val();
                
                table = $('#tabel_laporan_harian').DataTable({ 
                    "iDisplayLength": 10,
                    "language": {
                        "url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
                        "sEmptyTable": "Tidak ada data di database",
                        "emptyTable": "Tidak ada data di database"
                    },
                    "stateSave": true,
                    "processing": true,
                    "serverSide": true,
                    "ordering": false,
                    "ajax": {
                        "url": "<?php echo site_url("food_n_go/rekap/transportasi/jadwal_operasional/tabel_data_pemesanan/")?>"+tanggal + '/' +unit,
                        "type": "POST"
                    },
                });
            }
		</script>
		