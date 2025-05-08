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
		        <input type='hidden' id='insert_tampil_bulan_tahun' name='insert_tampil_bulan_tahun'>
		        <?php					
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
										}
                                        else
										{
											$selected='';
										}
								?>
		                        <option value='<?php echo $value?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,0,2))." ".substr($value,3,4)?></option>
		                        <?php } ?>
		                    </select>
		                </div>
                        
                        <div class="col-md-3">
                            <label>Status</label>
                            <select class="form-control" id='konfirmasi' name='konfirmasi' onchange="refresh_table_serverside()" style="width: 200px;">
		                        <option value='tunggu' selected>Menunggu Persetujuan</option>
		                        <!-- <option value='jalan'>Jalan</option> -->
		                        <option value='semua'>Tampilkan Semua</option>
                            </select>
                        </div>
                        
		                <input type="hidden" name="bulan" value="" id="get_month" />
		                <input type="hidden" name="bulan" value="" id="get_month_per_unit" />
		            </div>
		        </div>
                
		        <div class="form-group">
		            <div class="row">
		                <table width="100%" class="table table-striped table-bordered table-hover" id="tabel_persetujuan_pemesanan">
		                    <thead>
		                        <tr>
		                            <th class='text-center' style="width: 5%;">No</th>
                                    <th class='text-center' style="width: 10%;">No. Pemesanan</th>
		                            <th class='text-center'>Nama</th>
		                            <th class='text-center'>Asal</th>
		                            <th class='text-center'>Tujuan</th>
		                            <th class='text-center'>Berangkat</th>
		                            <th class='text-center'>Penumpang</th>
		                            <!-- <th class='text-center'>Kendaraan</th>
		                            <th class='text-center'>Approver</th> -->
		                            <th class='text-center'>Status</th>
		                            <th class='text-center no-sort'>Aksi</th>
		                        </tr>
		                    </thead>
		                    <tbody></tbody>
		                </table>
		                <!-- /.table-responsive -->
		            </div>
		        </div>
		        <?php } ?>

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
            $('#multi_select').select2({
                closeOnSelect: false
		        //minimumResultsForSearch: 20
		    });
		    $('#multi_select_per_unit').select2({
                closeOnSelect: false
                //minimumResultsForSearch: 20
		    });
		    $(document).ready(function() {
                //lempar ke modal cetak
                document.getElementById('get_month').value = $('#bulan_tahun').val();
                document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();

		        $('.datetimepicker5').datetimepicker({
                    format: 'HH:mm'
		        }).on('dp.change', function(event) {});

		        $('.tanggal_mdy').datetimepicker({
                    format: 'D-MM-Y'
		        }).on('dp.change', function(event) {

		        });

		        $(function() {
		            $('#insert_tapping_fix_1_date').datetimepicker({
                        format: 'D-MM-Y',
                        <?php if (@$maxDate) { ?>
                        maxDate: '<?php echo $maxDate;?>', 
                        <?php } ?>
		            });

		            $('#insert_tapping_fix_2_date').datetimepicker({
                        format: 'D-MM-Y',
                        <?php if (@$maxDate) { ?>
                        maxDate: '<?php echo $maxDate;?>', 
                        <?php } ?>
		            });

		            $('#insert_tapping_fix_2_date').datetimepicker({
                        format: 'D-MM-Y'
		            });
		        });

		        $(function() {
		            $('#edit_tapping_1_date').datetimepicker({
                        format: 'DD-MM-Y',
                        <?php if (@$minDate) { ?>
                        minDate: '<?php echo $minDate;?>', 
                        <?php }
                        if (@$maxDate) { ?>
                        maxDate: '<?php echo $maxDate;?>', 
                        <?php } ?>
		            });

		            $('#edit_tapping_2_date').datetimepicker({
                        format: 'DD-MM-Y',
                        <?php if (@$maxDate) { ?>
                        maxDate: '<?php echo $maxDate;?>', 
                        <?php } ?>
		            });
		        });

		        $('.select2').select2();
		        $('#tabel_persetujuan_pemesanan').DataTable().destroy();
		        table_serverside();
		    });

		    function refresh_table_serverside() {
		        document.getElementById('get_month').value = $('#bulan_tahun').val();
		        document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
		        $('#tabel_persetujuan_pemesanan').DataTable().destroy();
		        table_serverside();
		    }

		    function refresh_bulan_tahun() {
		        $('#tabel_persetujuan_pemesanan').DataTable().destroy();
		        table_serverside();
		    }
		</script>

		<script>
		    function table_serverside() {
		        var bulan_tahun = $('#bulan_tahun').val();
                var konfirmasi = $('#konfirmasi').val();
		        <?php
		        if (@$akses["tambah"]) { ?>
		            document.getElementById("insert_tampil_bulan_tahun").value = bulan_tahun; 
                <?php
		        }
		        if (@$akses["ubah"]) { ?>
		            document.getElementById("edit_tampil_bulan_tahun").value = bulan_tahun; 
                <?php
		        } ?>

		        //datatables
		        table = $('#tabel_persetujuan_pemesanan').DataTable({
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
                    
		            // Load data for the table's content from an Ajax source
		            "ajax": {
		                "url": "<?php echo site_url("food_n_go/kendaraan/persetujuan_pemesanan/tabel_persetujuan_pemesanan/")?>" + konfirmasi + '/' + bulan_tahun,
		                "type": "POST"
		            },
                    
		            //Set column definition initialisation properties.
		            "columnDefs": [{
		                "targets": 'no-sort',
		                "orderable": false,
		            }, ],
		        });
		    }
		    
            function show_detail(input) {
		        var url = '<?= base_url('food_n_go/kendaraan/data_pemesanan/show_detail/')?>' + input.dataset.id;
		        $("#modal-content-detail").html('Loading...');
		        $("#modal_detail").modal('show');
		        $.post(url).done(function(data) {
		            $("#modal-content-detail").html(data);
		        });
		    }
            
		<?php 
		if(@$akses["persetujuan"]){ ?>
            function update_approval(input) {
		        var url = '<?= base_url('food_n_go/kendaraan/persetujuan_pemesanan/update_approval/')?>' + input.dataset.id;
		        $("#modal-content-detail").html('Loading...');
		        $("#modal_detail").modal('show');
		        $.get(url).done(function(data) {
		            $("#modal-content-detail").html(data);
		        });
		    }
		<?php } ?>
            
            $('#modal_detail').on('hidden.bs.modal', function () {
                table.draw(false);
            })
            
		</script>