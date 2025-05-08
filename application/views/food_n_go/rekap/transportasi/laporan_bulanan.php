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
		                    <label>Pilih Bulan</label>
		                    <input class="form-control" type="text" id="tanggal" style="width: 200px;" value="<?= date('m-Y')?>">
                            <input type="checkbox" id="regenerate"> <small>Perbarui ulang laporan bulanan?</small>
		                </div>
                        
		                <div class="col-md-3">
		                    <label>Pilih Unit</label>
		                    <select class="form-control" id="unit" style="width: 200px;">
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
            
		    $(document).ready(function() {
                
                $('#tanggal').datetimepicker({
                    format: 'MM-Y'
		        });
                
		    });
            
            function proses() {
		        var url = '<?= base_url('food_n_go/rekap/transportasi/laporan_bulanan/proses/')?>';
                var tanggal = '01-' + $('#tanggal').val();
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
            
		</script>
		