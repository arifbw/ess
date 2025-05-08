		<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
		<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
		
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

				<?php if(@$akses["lihat log"]) { ?>
				<div class="row text-right">
					<button class="btn btn-primary btn-md" onclick="lihat_log()">Lihat Log</button>
					<br><br>
				</div>
				<?php } if(@$akses["tambah"] && $persetujuan=='0') { ?>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a href="<?php echo base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/add') ?>">Pesan <?php echo $judul;?></a>
								</h4>
							</div>
						</div>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->
				<?php } ?>
				<!-- Modal NP -->

				<div class="modal fade" id="modal_persetujuan" tabindex="-1" role="dialog" aria-labelledby="label_modal_np" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<?php if(@$persetujuan) { ?>
							<form role="form" action="persetujuan" method="post" id="form_persetujuan">
                            <?php } else { ?>
                            <form role="form" action="#" method="post" id="form_persetujuan">
                            <?php }  ?>
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="label_modal_np"></h4>
								</div>
								<div class="modal-body">
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
												<label>No Pemesanan</label>
												<input type="text" name="no_pemesanan" class="form-control" readonly id="detail_no_pemesanan">
											</div>
										</div>
									</div>

									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-md-4">
												<label>NP Pemesan</label>
												<input type="text" class="form-control" readonly id="detail_np_pemesan">
											</div>

											<div class="col-md-8">
												<label>Nama </label>
												<input type="text" class="form-control" readonly id="detail_nama_pemesan">
											</div>
										</div>

										<div class="row">
											<div class="col-md-12">
												<label>Unit Kerja</label>
												<input type="text" class="form-control" readonly id="detail_unit_kerja">
											</div>
										</div>
									</div>

									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-md-6">
												<label>Tanggal Pelaksanaan</label>
												<input type="text" class="form-control" readonly id="detail_tgl_pemesanan">
											</div>

											<div class="col-md-6">
												<label>Waktu Pelaksanaan</label>
												<input type="text" class="form-control" readonly id="detail_waktu_pemesanan">
											</div>
										</div>

										<div class="row">
											<div class="col-md-12">
												<label>Ruangan</label>
												<input type="text" class="form-control" readonly id="detail_ruangan">
											</div>
										</div>
									</div>

									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-md-12">
												<label>Jumlah Peserta : </label> <label id="detail_jumlah_peserta"></label>
											</div>
										</div>
									</div>

									<div class="form-group pesanan_data">
										<div class="row">
											<div class="col-md-12">
												<label>Snack : </label> <label id="detail_total_snack"></label>
												<p id="detail_snack"></p>
											</div>
										</div>

										<div class="row">
											<div class="col-md-12">
												<label>Makanan : </label> <label id="detail_total_makanan"></label>
												<p id="detail_makanan"></p>
											</div>

											<div class="col-md-12">
												<label>Minuman : </label> <label id="detail_total_minuman"></label>
												<p id="detail_minuman"></p>
											</div>
										</div>

										<div class="row">
											<div class="col-md-12">
												<label>Total Harga : </label> <label id="detail_total_harga"></label>
											</div>
										</div>
									</div>

									<hr>
									<div class="form-group">
										<div class="row">
											<div class="col-md-6">
												<label>Kode Akun STO</label>
												<input type="text" class="form-control" readonly id="detail_kode_akun_sto">
											</div>

											<div class="col-md-6">
												<label>Kode Anggaran</label>
												<input type="text" class="form-control" readonly id="detail_kode_anggaran">
											</div>
										</div>
									</div>

									<hr>
									<div class="form-group">
										<div class="detail">
											<div class="row">
												<div class="col-md-12">
													<label>Approval</label>
													<textarea type="text" class="form-control" rows="3" readonly id="detail_verified"></textarea>
												</div>
											</div>

											<div class="row">
												<div class="col-md-12">
													<label>Keterangan</label>
													<textarea class="form-control" readonly rows="3" id="detail_keterangan_verified"></textarea>
												</div>
											</div>
										</div>
									</div>
									<?php if(@$this->akses["persetujuan"]) { ?>
									<div class="approval">
										<div class="form-group">
											<label>Approval</label>
											<select class="form-control" name="verified" onchange="cek_approval(this.value)" required>
												<option value='1'>Diterima</option>
												<option value='0'>Ditolak</option>
											</select>
										</div>

										<div class="form-group" id="keterangan_approval">
											<label>Keterangan</label>
											<textarea name='keterangan' class="form-control" rows="3"></textarea>
										</div>
									</div>
									<?php } ?>
								</div>
								<div class="modal-footer">
									<div class="row">
										<?php if(@$this->akses["persetujuan"]) { ?>
										<div class="col-md-10 pull-right">
											<div class="approval">
												<button name='submit' type="submit" value='submit' class="btn btn-primary">Simpan</button>
											</div>
										</div>
										<?php } ?>
										<div class="col-md-2 pull-right">
											<button type="button" class="btn btn-default" data-dismiss="modal">Kembali</button>
										</div>
                                        
                                        <!-- heru PDS menambahkan ini, 2021-02-05, buat print detail ke pdf-->
                                        <?php if($_SESSION['grup']==11){?>
										<div class="col-md-2 pull-right">
											<button type="button" class="btn btn-warning" onclick="dataToPrint()" id="btn-dataToPrint"><i class="fa fa-print"></i> Print</button>
										</div>
                                        <!-- END: heru PDS menambahkan ini, 2021-02-05, buat print detail ke pdf-->
                                        <?php } ?>
									</div>
								</div>
							</form>								
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
				<?php if(@$this->akses["lihat"]) { ?>
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
								if(!empty($this->session->flashdata('tampil_bulan_tahun'))) {
									$tampil_bulan_tahun=$this->session->flashdata('tampil_bulan_tahun');
								}

								if($tampil_bulan_tahun==$value) {
									$selected='selected';
								} else {
									$selected='';
								} ?>
								<option value='<?php echo $value?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,0,2))." ".substr($value,3,4)?></option>
							<?php } ?>
							</select>
														
						</div>
                        <input type="hidden" name="bulan" value="" id="get_month" />
                        <input type="hidden" name="bulan" value="" id="get_month_per_unit" />
                        
                        <!-- <?php if($this->uri->segment(4)=='rekap') { ?>
						<br>
						<div class="col-md-7">
                        	<button class='btn btn-primary pull-right'>Monitoring Pembelanjaan Konsumsi Rapat</button> 
                        </div>
                        <div class="col-md-2 pull-right">
                        	<button class='btn btn-primary pull-right'>Rekap Anggaran</button> 
                        </div>
                        <?php } ?> -->
					</div>
				</div>

				<div class="form-group">	
					<div class="row">
						<div class="col-md-12">
							<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_data">
								<thead>
									<tr>
										<th class='text-center'>No</th>
										<th class='text-center no-sort'>Nomor Pemesanan</th>
										<th class='text-center'>Nama Pemesan</th>	
										<th class='text-center'>Nama Acara</th>
										<th class='text-center'>Lokasi</th>
										<th class='text-center'>Tanggal<br>Pelaksanaan</th>
										<th class='text-center'>Waktu<br>Pelaksanaan</th>
										<th class='text-center no-sort'>Status</th>
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
			
				<?php } if(@$akses["ubah"] || @$akses["persetujuan"] || $_SESSION['grup']==14) { ?>
				<!-- Modal -->
				<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content edit-content">
							<div class="table-responsive-sm" id="set_ubah">
								
							</div>						
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->
				<?php } ?>
				
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<script src="<?php echo base_url('asset/sweetalert2')?>/sweetalert2.js"></script>
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
		
		<script type="text/javascript">	
			var makanan_ubah;
			var minuman_ubah;

			$('#multi_select').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
			$('#multi_select_per_unit').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});

			$(document).ready(function() {
				
				<?php if(!empty($this->session->flashdata('success'))) { ?>
					Swal.fire({
					  	icon: 'success',
					  	title: '<?= $this->session->flashdata('success') ?>'
					});
				<?php } else if(!empty($this->session->flashdata('warning'))) { ?>
					Swal.fire({
					  	icon: 'warning',
					  	title: '<?= $this->session->flashdata('warning') ?>'
					});
				<?php } else if(!empty($this->session->flashdata('failed'))) { ?>
					Swal.fire({
					  	icon: 'error',
					  	title: '<?= $this->session->flashdata('failed') ?>'
					});
				<?php } ?>

				document.getElementById('get_month').value = $('#bulan_tahun').val();
				document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
                
                // $(function () {
                    $('#insert_tgl_pemesanan').datetimepicker({
                        format: 'D-MM-Y',
						minDate : '<?php echo date('Y-m-d') ?>',
                    });
                // });
                
                $('.select2').select2();
				$('#tabel_data').DataTable().destroy();
				$('#keterangan_approval').hide();							
				table_serverside();
			});		
			
			function refresh_table_serverside() {
				document.getElementById('get_month').value = $('#bulan_tahun').val();
				document.getElementById('get_month_per_unit').value = $('#bulan_tahun').val();
				$('#tabel_data').DataTable().destroy();				
				table_serverside();
			}
			
			function refresh_bulan_tahun() {
				$('#tabel_data').DataTable().destroy();	
				table_serverside();
			}

			function cek_approval(approval) {
				if (approval==='0') {
					$('#keterangan_approval').show();
				} else {
					$('#keterangan_approval').hide();
				}
			}

            function get_approval(){
                var kode_unit_pemesan = $('#insert_unit_kerja').children("option:selected").val();
                var nama_unit_pemesan = $('#insert_unit_kerja').children("option:selected").text();
                $('#nama_unit_pemesan').val(nama_unit_pemesan);
                
                $("#insert_np_atasan").empty();
                $.ajax({
		            url: "<?php echo base_url('food_n_go/konsumsi/pemesanan_makan_lembur/get_apv');?>",
		            type: "POST",
		            dataType: "json",
		            data: {kode_unit: kode_unit_pemesan},
		            success: function(response) {
                        for (var i = 0; i < response.length; i++) {
                            $("#insert_np_atasan").append($("<option></option>").attr("value", response[i]["no_pokok"] + " - " + response[i]["nama"]).text(response[i]["no_pokok"] + " - " + response[i]["nama"]));
                            if(response[i]["kode_unit"]==kode_unit_pemesan){
                                $("#insert_np_atasan").val(response[i]["no_pokok"] + " - " + response[i]["nama"]);
                                $("#insert_np_atasan").trigger('change');
                            }
                        }
                        $('.select2').select2();
		            }
		        });
            }

			function tampil_data_approval(element){
			    $('.detail').hide();
				$('.pesanan_data').show();
			    $('.approval').show();
			    $('#label_modal_np').text('Berikan Persetujuan');

			    $.ajax({
			    	<?php if (($_SESSION['grup']=='5' || $_SESSION['grup']=='4') && $persetujuan!='1') { ?>
					url: "pemesanan_konsumsi_rapat/detail",
					<?php } else { ?>
					url: "detail",
					<?php } ?>
			        type: "POST",
			        dataType: "json",
			        data: {no_pemesanan: element.dataset.no},
			        success: function(response) {
					    $('#detail_no_pemesanan').val(element.dataset.no);
					    $('#detail_np_pemesan').val(response.detail.np_pemesan);
					    $('#detail_nama_pemesan').val(response.detail.nama_pemesan);
					    $('#detail_jumlah_peserta').text(response.detail.jumlah_peserta);
					    $('#detail_snack').html(response.snack.daftar);
					    $('#detail_total_snack').text(response.total_snack);
					    $('#detail_makanan').html(response.makanan);
					    $('#detail_total_makanan').text(response.total_makanan);
					    $('#detail_minuman').html(response.minuman);
					    $('#detail_total_minuman').text(response.total_minuman);
					    $('#detail_total_harga').text(response.total);
					    $('#detail_jenis_pemesanan').val(response.detail.jenis_pesanan);
					    $('#detail_ruangan').val(response.detail.nama_gedung+' - '+response.detail.nama_ruangan);
					    $('#detail_unit_kerja').val(response.detail.nama_unit_pemesan);
					    $('#detail_jumlah_pemesanan').val(response.detail.jumlah_pemesanan);
					    $('#detail_tgl_pemesanan').val(response.detail.tanggal_pemesanan);
					    $('#detail_waktu_pemesanan').val(response.detail.waktu_mulai+' s/d '+response.detail.waktu_selesai);
					    $('#detail_kode_akun_sto').val(response.detail.kode_akun_sto);
					    $('#detail_kode_anggaran').val(response.detail.kode_anggaran);
					    
					    /*if("<?= $_SESSION['grup'] ?>"=='5' && response.detail.np_atasan=="<?= $_SESSION['no_pokok'] ?>") {
					    	$('.pesanan_data').hide();
					    }*/

					    if(response.detail.verified==null || response.detail.verified=='1') {
					    	$('#form_persetujuan').attr("action" ,"persetujuan");
						} else{
					    	$('#form_persetujuan').attr("action" ,"persetujuan"); // heru PDS ganti action jadi persetujuan, 2021-02-10
					    }
			        }
			    });
			}

			function tampil_data_detail(element){
			    $('#label_modal_np').text('Detail Pemesanan');
				$('.pesanan_data').show();

			    $.ajax({
			        <?php if (($_SESSION['grup']=='5' || $_SESSION['grup']=='4') && $persetujuan!='1') { ?>
					url: "pemesanan_konsumsi_rapat/detail",
					<?php } else { ?>
					url: "detail",
					<?php } ?>
			        type: "POST",
			        dataType: "json",
			        data: {no_pemesanan: element.dataset.no},
			        success: function(response) {
					    $('.approval').hide();
					    $('.detail').show();

					    $('#detail_no_pemesanan').val(element.dataset.no);
					    $('#detail_np_pemesan').val(response.detail.np_pemesan);
					    $('#detail_nama_pemesan').val(response.detail.nama_pemesan);
					    $('#detail_jumlah_peserta').text(response.detail.jumlah_peserta);
					    $('#detail_snack').html(response.snack.daftar);
					    $('#detail_total_snack').text(response.total_snack);
					    $('#detail_makanan').html(response.makanan);
					    $('#detail_total_makanan').text(response.total_makanan);
					    $('#detail_minuman').html(response.minuman);
					    $('#detail_total_minuman').text(response.total_minuman);
					    $('#detail_total_harga').text(response.total);
					    $('#detail_jenis_pemesanan').val(response.detail.jenis_pesanan);
					    $('#detail_ruangan').val(response.detail.nama_gedung+' - '+response.detail.nama_ruangan);
					    $('#detail_unit_kerja').val(response.detail.nama_unit_pemesan);
					    $('#detail_jumlah_pemesanan').val(response.detail.jumlah_pemesanan);
					    $('#detail_tgl_pemesanan').val(response.detail.tanggal_pemesanan);
					    $('#detail_waktu_pemesanan').val(response.detail.waktu_mulai+' s/d '+response.detail.waktu_selesai);
					    $('#detail_keterangan_verified').val(response.detail.keterangan_verified);
					    $('#detail_kode_akun_sto').val(response.detail.kode_akun_sto);
					    $('#detail_kode_anggaran').val(response.detail.kode_anggaran);
					    
					    /*if("<?= $_SESSION['grup'] ?>"=='5' && response.detail.np_atasan=="<?= $_SESSION['no_pokok'] ?>") {
					    	$('.pesanan_data').hide();
					    }*/

					    if(response.detail.verified==null) {
					    	$('#detail_verified').val('Menunggu Persetujuan Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+')');
					    } else if(response.detail.verified=='1') {
					    	$('#detail_verified').val('- Disetujui Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan+'\n- Menunggu Persetujuan Admin');
					    } else if(response.detail.verified=='2') {
					    	$('#detail_verified').val('Ditolak Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan);
					    } else if(response.detail.verified=='3') {
					    	$('#detail_verified').val('- Disetujui Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan+'\n- Disetujui Seksi Yanum pada '+response.detail.waktu_verified_admin);
					    } else if(response.detail.verified=='4') {
					    	$('#detail_verified').val('- Disetujui Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan+'\n- Ditolak Seksi Yanum pada '+response.detail.waktu_verified_admin);
					    } else if(response.detail.verified=='6') {
					    	$('#detail_verified').val('- Disetujui Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan+'\n- Disetujui Seksi Yanum pada '+response.detail.waktu_verified_admin+'\n- Dibatalkan Seksi Yanum pada '+response.detail.waktu_batal);
						} else{
					    	$('#detail_verified').val('Waktu Pemesanan Ditolak!');
					    }
			        },
                    error: function(e){
                        console.log(e);
                    }
			    });
			}
			
			function tampil_data_ubah(element) {
				$.ajax({
					method: "POST",
                    dataType: 'html',
					data: { no_pemesanan: element.dataset.no },
					<?php if ($_SESSION['grup']=='5' || $_SESSION['grup']=='4') { ?>
					url: "pemesanan_konsumsi_rapat/ubah",
					<?php } else { ?>
					url: "ubah",
					<?php } ?>
				})
				.done(function( msg ) {
                    $('#modal_ubah').show();
					$("#set_ubah").html(msg);
				});
			}

			function table_serverside() {
				var table;
				var bulan_tahun = $('#bulan_tahun').val();
				
				//datatables
				table = $('#tabel_data').DataTable({
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
						"url": "<?php echo site_url($url_table)?>"+bulan_tahun,
						"type": "POST"
					},

					//Set column definition initialisation properties.
					"columnDefs": [{ 
						"targets": 'no-sort', //first column / numbering column
						"orderable": false, //set not orderable
					}],

					//Set column responsive.
					"responsive": true  //set responsive on

				});
			}

			function batal(no){
				Swal.fire({
					title: 'Apakah anda yakin ingin membatalkan pesanan ini?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ya, batalkan!'
				}).then((result) => {
					if (result.value) {
						$.ajax({
					        type: "POST",
					        url: "batal",
					        data: { 'no': no},
					        cache: false,
					        success: function(response) {
					        	obj = JSON.parse(response);
							    Swal.fire(
							      	obj.judul,
							      	obj.txt,
							      	obj.alert
							   	)
							   	refresh_table_serverside();
							},
							failure: function (response) {
							    Swal.fire(
							      	'Gagal!',
							      	'Pesanan tidak dibatalkan.',
							      	'error'
							   	)
							}
						})
					}
				})
			}
            
            // heru PDS menambahkan ini, 2021-02-05, buat print detail ke pdf
            const dataToPrint=()=>{
                const allData = {};
                $('#btn-dataToPrint').prop('disabled',true);
                $('#btn-dataToPrint').text('Processing...',true);
                $("#modal-body-detail input,textarea").each(function(){
                    allData[this.id] = this.value;
                });
                allData['detail_jumlah_peserta'] = $("#detail_jumlah_peserta").text();
                allData['detail_total_snack'] = $("#detail_total_snack").text();
                allData['detail_snack'] = $("#detail_snack").text();
                allData['detail_total_makanan'] = $("#detail_total_makanan").text();
                allData['detail_makanan'] = $("#detail_makanan").text();
                allData['detail_total_minuman'] = $("#detail_total_minuman").text();
                allData['detail_minuman'] = $("#detail_minuman").text();
                allData['detail_total_harga'] = $("#detail_total_harga").text();
                
                $.ajax({
                    type: "POST",
                    url: "<?= base_url()?>food_n_go/konsumsi/Invoice_konsumsi_rapat/get_request",
                    data: allData,
                    dataType: 'json',
                }).then(function(response){
                    if(response.status===true)
                        window.open(response.data,'_blank');
                    else
                        alert(xhr.responseText)
                    
                    $('#btn-dataToPrint').prop('disabled',false);
                    $('#btn-dataToPrint').text('Print',true);
                }).catch(function(xhr, status, error){
                    console.log(xhr.responseText);
                    alert(xhr.responseText)
                    $('#btn-dataToPrint').prop('disabled',false);
                    $('#btn-dataToPrint').text('Print',true);
                })
            }
            // END: heru PDS menambahkan ini, 2021-02-05
		</script>