        <link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
        <link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
    	<link rel="stylesheet" href="<?= base_url()?>asset/toastr-2.1.4/toastr.min.css">
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
					if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					if($akses["tambah"]){ ?>

					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">
									<a data-toggle='modal' data-target='#modal_add' onclick="form_add()">Isi Health Passport</a>
								</div>
							</div>
						</div>
						<!-- /.col-lg-12 -->
					</div>


					<!-- Modal -->
					<div class="modal fade" id="modal_add" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title" id="label_modal_batal">Isi Health Passport</h4>
								</div>
								<div class="modal-body">		
									<div class="row">
										<div class="col-md-12">
											<form role="form" action="#" method="post" id="formulir_tambah_assesment">											
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>NP Karyawan</label>
														</div>
														<div class="col-lg-7">
															<select class="form-control select2 input-sm" name="npk" id="edit_np_karyawan" style="width:100%"><?= $list_np ?></select>
														</div>											                  
													</div>
												</div>
												<br>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-3">
															<label>Tanggal</label>
														</div>
														<div class="col-lg-7">
															<input class="form-control tanggal_mdy" name="tgl" id="tanggal_kehadiran"> 
														</div>											                  
													</div>
												</div>		
												<br>

												<div class="alert alert-danger alert-dismissable" id="cancel_view" style="display: none;">
													<span id="cancel_assesment"></span>
												</div>

												<div class="alert alert-info alert-dismissable" id="isi_view">
													Isi Form 
												</div>

												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
															<label>Apakah pernah keluar rumah/tempat umum (pasar, fasyankes, kerumunan orang, dll) ?</label>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
					                                        <label class="radio-inline">
																<input type="radio" name="pernah_keluar" id="pernah_keluar1" value="1">Ya
															</label>
															<label class="radio-inline">
																<input type="radio" name="pernah_keluar" id="pernah_keluar2" value="2">Tidak
															</label>
															
														</div>													
													</div>
												</div>
												
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
															<label>Apakah pernah menggunakan transportasi umum ?</label>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
					                                        <label class="radio-inline">
																<input type="radio" name="transportasi_umum" id="transportasi_umum1" value="1">Ya
															</label>
															<label class="radio-inline">
																<input type="radio" name="transportasi_umum" id="transportasi_umum2" value="2">Tidak
															</label>
															
														</div>
														<!--							
														<div class="col-lg-1">
															 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
														</div>
														-->															
													</div>
												</div>
												
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
															<label>Apakah pernah melakukan perjalanan ke luar kota/internasional (wilayah yang terjangkit/zona merah) ?</label>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
					                                        <label class="radio-inline">
																<input type="radio" name="luar_kota" id="luar_kota1" value="1">Ya
															</label>
															<label class="radio-inline">
																<input type="radio" name="luar_kota" id="luar_kota2" value="2">Tidak
															</label>
															
														</div>
														<!--							
														<div class="col-lg-1">
															 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
														</div>
														-->															
													</div>
												</div>
												
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
															<label>Apakah anda mengikuti kegiatan yang melibatkan orang banyak ?</label>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
					                                        <label class="radio-inline">
																<input type="radio" name="kegiatan_orang_banyak" id="kegiatan_orang_banyak1" value="1">Ya
															</label>
															<label class="radio-inline">
																<input type="radio" name="kegiatan_orang_banyak" id="kegiatan_orang_banyak2" value="2">Tidak
															</label>
															
														</div>
														<!--							
														<div class="col-lg-1">
															 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
														</div>
														-->															
													</div>
												</div>
												
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
															<label>Apakah memiliki riwayat kontak erat dengan orang yang dinyatakan ODP, PDP, atau Confirm COVID-19 ?</label>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
					                                        <label class="radio-inline">
																<input type="radio" name="kontak_pasien" id="kontak_pasien1" value="1">Ya
															</label>
															<label class="radio-inline">
																<input type="radio" name="kontak_pasien" id="kontak_pasien2" value="2">Tidak
															</label>
															
														</div>
														<!--							
														<div class="col-lg-1">
															 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
														</div>
														-->															
													</div>
												</div>
												
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
															<label>Apakah pernah mengalami demam/batuk/pilek/sakit tenggorokan/sesak nafas dalam 14 hari terakhir ?</label>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="form-group">
														<div class="col-lg-12">
					                                        <label class="radio-inline">
																<input type="radio" name="sakit" id="sakit1" value="1">Ya
															</label>
															<label class="radio-inline">
																<input type="radio" name="sakit" id="sakit2" value="2">Tidak
															</label>
															
														</div>
														<!--							
														<div class="col-lg-1">
															 <button class='btn btn-primary btn-md btn-block' data-toggle='modal'  data-target='#modal_np' onclick="listNp()"><i class='fa fa-users'></i></button>
														</div>
														-->															
													</div>
												</div>
												
												<br>
												<div class="row">
													<div class="col-lg-4">
														<?php if($this->akses["batal"]){ ?>
														<div class="text-left" id="cancel_button" style="display: none;">
															<input type="button" value="Batalkan Assesment Covid-19" class="btn btn-danger" id="CanForm">
															<!-- <input type="submit" name="submit" value="submit" class="btn btn-primary"> -->
														</div>
														<?php } else { ?>
														<div class="text-left" id="cancel_button" style="display: none;"></div>
														<?php } ?>
													</div>

													<div class="col-lg-8">
														<div class="text-right" id="submit_button">
															<input name="submit" type="button" value="Simpan" class="btn btn-primary" id="SubForm">
															<!-- <input type="submit" name="submit" value="submit" class="btn btn-primary"> -->
														</div>
													</div>
												</div>
											</form>
										</div>
										<!-- /.col-lg-12 -->
									</div>
									<!-- /.row -->
								</div>
									
							</div>
							<!-- /.modal-content -->
						</div>
						<!-- /.modal-dialog -->
					</div>
					<!-- /.modal -->
				<?php
					}
					
					if($this->akses["lihat"]){ ?>

						<!-- filter bulan -->
                        <div class="form-group">
							<div class="row">
                                <div class="col-lg-4">
                                    <label>Bulan</label>
                                    <!--<select id="pilih_bulan_tanggal" class="form-control">-->
                                    <select class="form-control" id='bulan_tahun' name='bulan_tahun'  onchange="refresh_table_serverside()" style="width: 200px;"> <!-- heru PDS, delete class "select2", 2021-01-05 @10:47 -->
                                        <option value='0'>Semua</option>	
                                    <?php 
                                    foreach ($array_tahun_bulan as $value) {

                                        $tampil_bulan_tahun='';
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
                                        <option value='<?php echo substr($value,3,4).'-'.substr($value,0,2)?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,0,2))." ".substr($value,3,4)?></option>								

                                    <?php 
                                    }
                                    ?>
                                    </select>
                                </div>
								<?php
									if($akses["cetak"]){
										echo "<div class='col-lg-8 text-right'>";
											echo "<a class='btn btn-primary btn-md' href='".site_url('kehadiran/self_assesment_covid19/cetak_beresiko')."'>Cetak Karyawan Beresiko</a>";
											echo "<br><br>";
										echo "</div>";
									}
								?>
								
								
								
								
								
							</div>
						</div>
						
						<div class="form-group">	
							<div class="row table-responsive">
								<div class="col-lg-12">
									<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_assesment_covid">
										<thead>
											<tr>
												<th class='text-center'>No</th>
												<th class='text-center'>Karyawan</th>
												<th class='text-center'>Tertanggal</th>			
												<th class='text-center no-sort'>Pernah<br>Keluar</th>
												<th class='text-center no-sort'>Transportasi<br>Umum</th>
												<th class='text-center no-sort'>Luar<br>Kota</th>
												<th class='text-center no-sort'>Kegiatan<br>Orang Banyak</th>
												<th class='text-center no-sort'>Riwayat<br>Kontak</th>
												<th class='text-center no-sort'>Pernah<br>Gejala</th>
												<th class='text-center no-sort'>Last<br>Update</th>
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
						
				<?php
					}
					
					if($akses["batal"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_batal" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_batal">Batal <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">		
										
											<table>
												<tr>
													<td>Np Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_np_karyawan"></a></td>
												</tr>
												<tr>
													<td>Nama Pemohon</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_nama"></a></td>
												</tr>
												<tr>
													<td>Dibuat Tanggal</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_created_at"></a></td>
												</tr>
												<tr>
													<td>Dibuat Oleh</td>
													<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
													<td><a id="batal_created_by"></a></td>
												</tr>
											</table>
											
											<br>
											
											<div class="alert alert-info">
												<strong><a id="batal_approval_1_nama"></a></strong><br>
												<p id="batal_approval_1_status"></p>
											</div>
											
											<div class="alert alert-info">
												<strong><a id="batal_approval_2_nama"></a></strong><br>
												<p id="batal_approval_2_status"></p>
											</div>
											
											<form role="form" action="<?php echo base_url(); ?>cuti/Permohonan_cuti/action_batal_cuti" id="formulir_tambah" method="post">	
												<div class="row">
													<div class="col-lg-12 text-right">
														<input type="hidden" name="batal_id" id="batal_id">
														<input type="submit" name="submit" value="Batalkan Cuti" class="btn btn-danger">
													</div>
												</div>
											</form>
											
										</div>
										
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
				<?php
					}
					
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->
		
		<script src="<?php echo base_url('asset/sweetalert2')?>/sweetalert2.js"></script>
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
        <script src="<?= base_url()?>asset/toastr-2.1.4/toastr.min.js"></script>

		<script type="text/javascript">	
			$(document).ready(function() {
				$('.select2').select2({
			        dropdownParent: $('#modal_add')
			    });
				
				
				$('.tanggal_mdy').datetimepicker({
                    format: "DD-MM-Y",
                    maxDate: new Date("<?= date('Y-m-d') ?>")
				});

				$('#SubForm').click(function(){
					var data_assesment = document.getElementById("formulir_tambah_assesment");
					if (data_assesment['npk'].value && data_assesment['tgl'].value && data_assesment['pernah_keluar'].value!='' && data_assesment['transportasi_umum'].value!='' && data_assesment['luar_kota'].value!='' && data_assesment['kegiatan_orang_banyak'].value!='' && data_assesment['kontak_pasien'].value!='' && data_assesment['sakit'].value!='')
				    	save_assesment();
				    else {
		                $("#alert_form").html('<div class="alert alert-danger alert-dismissable"><strong id="get_last_assesment">Semua Pertanyaan Harus Dijawab!</strong></div>');
				    	loading_toastr_error('Semua Pertanyaan Harus Dijawab!');
				    }
				});

				function save_assesment()
				{
					var data_assesment = document.getElementById("formulir_tambah_assesment");

					$.ajax({
		                type: "POST",
		                data: {id: $("#edit_np_karyawan").val(), tgl: data_assesment['tgl'].value, submit: true, pernah_keluar: data_assesment['pernah_keluar'].value, transportasi_umum: data_assesment['transportasi_umum'].value, luar_kota: data_assesment['luar_kota'].value, kegiatan_orang_banyak: data_assesment['kegiatan_orang_banyak'].value, kontak_pasien: data_assesment['kontak_pasien'].value, sakit: data_assesment['sakit'].value},
		                url: "<?php echo base_url(); ?>kehadiran/self_assesment_covid19/action_insert",
		                success: function(get_data){
		                	data = JSON.parse(get_data);

		                	msg=data.message;
		                	if(data.status==true) {
		                		loading_toastr_success(msg);
		                		$("#modal_add").modal('hide');
		                		form_add();

		                		$('#tabel_ess_assesment_covid').DataTable().destroy();
		                		table_serverside();
		                	}
		                	else {
		                		loading_toastr_error(msg);
		                	}
		                }
		            });	
				}

				$(document).on( "click", '.status_button',function(e) {
					$('#formulir_tambah_assesment').trigger("reset");

					newdate = $(this).data('tanggal').split("-").reverse().join("-");
					$("#tanggal_kehadiran").val(newdate);
					$("#edit_np_karyawan").val($(this).data('np-karyawan')).trigger("change");

					if($(this).data('pernah-keluar')=='1') {
						document.getElementById("pernah_keluar1").checked = true;
						document.getElementById("pernah_keluar2").checked = false;
					} else {
						document.getElementById("pernah_keluar1").checked = false;
						document.getElementById("pernah_keluar2").checked = true;
					}

					if($(this).data('transportasi-umum')=='1') {
						document.getElementById("transportasi_umum1").checked = true;
						document.getElementById("transportasi_umum2").checked = false;
					} else {
						document.getElementById("transportasi_umum1").checked = false;
						document.getElementById("transportasi_umum2").checked = true;
					}

					if($(this).data('luar-kota')=='1') {
						document.getElementById("luar_kota1").checked = true;
						document.getElementById("luar_kota2").checked = false;
					} else {
						document.getElementById("luar_kota1").checked = false;
						document.getElementById("luar_kota2").checked = true;
					}

					if($(this).data('kegiatan-orang-banyak')=='1') {
						document.getElementById("kegiatan_orang_banyak1").checked = true;
						document.getElementById("kegiatan_orang_banyak2").checked = false;
					} else {
						document.getElementById("kegiatan_orang_banyak1").checked = false;
						document.getElementById("kegiatan_orang_banyak2").checked = true;
					}

					if($(this).data('kontak-pasien')=='1') {
						document.getElementById("kontak_pasien1").checked = true;
						document.getElementById("kontak_pasien2").checked = false;
					} else {
						document.getElementById("kontak_pasien1").checked = false;
						document.getElementById("kontak_pasien2").checked = true;
					}

					if($(this).data('sakit')=='1') {
						document.getElementById("sakit1").checked = true;
						document.getElementById("sakit2").checked = false;
					} else {
						document.getElementById("sakit1").checked = false;
						document.getElementById("sakit2").checked = true;
					}

					if($(this).data('is-status')=='0') {
						$("#cancel_assesment").text('Assesment Dibatalkan Oleh NP : '+$(this).data('canceled-by')+' Pada '+$(this).data('canceled-at'));
					}

					if('<?= $this->session->userdata("grup") ?>'=='1' || (('<?= $this->session->userdata("grup") ?>'=='4' || '<?= $this->session->userdata("grup") ?>'=='5') && $(this).data('created-at').substring(0, 10)=='<?= date('Y-m-d') ?>')) {
						$("#formulir_tambah_assesment :input").attr("disabled", false);

						if($(this).data('is-status')=='0') {
							$("#cancel_view").show();
							$("#isi_view").hide();
							$("#formulir_tambah_assesment :input").attr("disabled", true);
							$("#cancel_button").hide();
							$("#submit_button").hide();
						} else {
							$("#isi_view").show();
							$("#cancel_view").hide();
							$("#cancel_button").show();
							$("#submit_button").show();
						}

					} else {
						$("#formulir_tambah_assesment :input").attr("disabled", true);
						$("#cancel_button").hide();	
						$("#submit_button").hide();
						$("#isi_view").hide();
					}
				});

				function loading_toastr_success(msg) {
	                toastr.success(msg, "Berhasil", {
	                    "positionClass": "toast-top-center",
	                    "showDuration": "300",
	                    "hideDuration": "500",
	                    "timeOut": "5000",
	                    "extendedTImeout": "1000",
	                    "showMethod": "fadeIn",
	                    "hideMethod": "fadeOut"
	                });
	            }

	            function loading_toastr_error(msg) {
	                toastr.error(msg, "Gagal", {
	                    "positionClass": "toast-top-center",
	                    "showDuration": "300",
	                    "hideDuration": "500",
	                    "timeOut": "5000",
	                    "extendedTImeout": "1000",
	                    "showMethod": "fadeIn",
	                    "hideMethod": "fadeOut"
	                });
	            }
				
				$('#tabel_ess_assesment_covid').DataTable().destroy();				
				table_serverside();
			});
			
			function refresh_table_serverside() {
				$('#tabel_ess_assesment_covid').DataTable().destroy();				
				table_serverside();
			}
			
			function table_serverside() {
				var table;
				var bulan_tahun = $('#bulan_tahun').val();
				//datatables
				table = $('#tabel_ess_assesment_covid').DataTable({ 
					
					"iDisplayLength": 10,
					"language": {
						"url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
						"sEmptyTable": "Tidak ada data di database",
						"emptyTable": "Tidak ada data di database"
					},
					
					"processing": true, //Feature control the processing indicator.
					"serverSide": true, //Feature control DataTables' server-side processing mode.
					"order": [], //Initial no order.

					// Load data for the table's content from an Ajax source
					"ajax": {
						"url": "<?php echo site_url("kehadiran/self_assesment_covid19/tabel_ess_assesment_covid/")?>" + bulan_tahun,
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

			function form_add() {
				$("#formulir_tambah_assesment :input").attr("disabled", false);
		        $('#edit_np_karyawan').val("").trigger("change");
				$('#formulir_tambah_assesment').trigger("reset");
				$('#isi_view').show();
				$('#submit_button').show();
				$('#cancel_button').hide();
				$('#cancel_view').hide();
			}

			$('#CanForm').click(function(){
				Swal.fire({
					title: 'Apakah anda yakin ingin membatalkan assesment ini?',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Ya, batalkan!'
				}).then((result) => {
					if (result.value) {
						$.ajax({
					        type: "POST",
					        url: "action_cancel",
					        data: { 'id': $("#edit_np_karyawan").val(), 'tgl': $("#tanggal_kehadiran").val() },
					        cache: false,
					        success: function(response) {
					        	obj = JSON.parse(response);
							    Swal.fire(
							      	obj.judul,
							      	obj.txt,
							      	obj.alert
							   	)

		                		$("#modal_add").modal('hide');
		                		$('#formulir_tambah_assesment').trigger("reset");
		                		$('#edit_np_karyawan').val("").trigger("change");
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
			});
		</script>