        <link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
        <link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="<?= base_url()?>asset/daterangepicker/daterangepicker.css" />
        <!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header">Laporan Menyelesaikan Pendidikan Tinggi</h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<?php if(!empty($this->session->flashdata('success'))){ ?>
				<div class="alert alert-success alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('success');?>
				</div>
				<?php } if(!empty($this->session->flashdata('warning'))){ ?>
				<div class="alert alert-danger alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $this->session->flashdata('warning');?>
				</div>
				<?php }
				
				if(@$akses["tambah"]){ ?>
				<div class="row">
					<div class="col-lg-12">
					
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne" id="btn-tambah">Tambah Laporan Pendidikan</a>
								</h4>
							</div>
							<div id="collapseOne" class="panel-collapse collapse">
								<div class="panel-body">
									<form role="form" action="<?php echo base_url(); ?>pelaporan/pendidikan/action_insert_pendidikan" id="formulir_tambah" method="post" enctype="multipart/form-data">
										<div class="row">
											<input type='hidden' id='edit_id' name='edit_id' value="<?= set_value("edit_id") ?>">

											<div class="form-group">
												<div class="col-lg-2">
													<label>NP Karyawan</label>
												</div>
												<div class="col-lg-7">
	                                                <select class="form-control select2" name="np_karyawan" id="np_karyawan" style="width: 100%" required>
	                                                    <option value=''></option>
	                                                    <?php foreach ($array_daftar_karyawan->result_array() as $value) { ?>
														<option value='<?php echo $value['no_pokok']?>' <?= ($value['no_pokok']==set_value("np_karyawan")?'selected':'') ?>><?php echo $value['no_pokok']." ".$value['nama']?></option>
														<?php }

														?>
	                                                </select>
												</div>
											</div>
										</div>

										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Perguruan Tinggi</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" type="text" name="perguruan_tinggi" value="<?= set_value("perguruan_tinggi") ?>" id="perguruan_tinggi" required>
												</div>														
											</div>
										</div>

										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Fakultas</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" type="text" name="fakultas" value="<?= set_value("fakultas") ?>" id="fakultas" required>
												</div>														
											</div>
										</div>

										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Jurusan</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" type="text" name="jurusan" value="<?= set_value("jurusan") ?>" id="jurusan" required>
												</div>														
											</div>
										</div>

										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Jenjang</label>
												</div>
												<div class="col-lg-7">
													<select class="form-control select2" name="jenjang" id="jenjang" style="width: 100%" required>
	                                                    <option value="">-- Pilih jenjang --</option>
	                                                    <option value="D1" <?= ("D1"==set_value("np_karyawan")?'selected':'') ?>>D1</option>
	                                                    <option value="D2" <?= ("D2"==set_value("np_karyawan")?'selected':'') ?>>D2</option>
	                                                    <option value="D3" <?= ("D3"==set_value("np_karyawan")?'selected':'') ?>>D3</option>
	                                                    <option value="D4" <?= ("D4"==set_value("np_karyawan")?'selected':'') ?>>D4</option>
	                                                    <option value="S1" <?= ("S1"==set_value("np_karyawan")?'selected':'') ?>>S1</option>
	                                                    <option value="S2" <?= ("S2"==set_value("np_karyawan")?'selected':'') ?>>S2</option>
	                                                    <option value="S3" <?= ("S3"==set_value("np_karyawan")?'selected':'') ?>>S3</option>
													</select>
												</div>														
											</div>
										</div>

										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Akreditasi</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control" type="text" name="akreditasi" value="<?= set_value("akreditasi") ?>" id="akreditasi" required>
												</div>														
											</div>
										</div>

										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Tanggal Masuk Pendidikan</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control datependidikan" type="text" name="tgl_masuk" value="<?= set_value("tgl_masuk") ?>" id="tgl_masuk" required>
												</div>														
											</div>
										</div>

										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Tanggal Selesai Pendidikan</label>
												</div>
												<div class="col-lg-7">
													<input class="form-control datependidikan" type="text" name="tgl_selesai" value="<?= set_value("tgl_selesai") ?>" id="tgl_selesai" required>
												</div>														
											</div>
										</div>
											
										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Upload Ijazah</label>
												</div>
												<div class="col-lg-4">
													<input class="form-control" type="text" name="no_ijazah" value="<?= set_value("no_ijazah") ?>" id="no_ijazah" placeholder="Masukkan Nomor Dokumen" required>
												</div>
												<div class="col-lg-3">
													<input class="form-control" type="file" name="file_ijazah" id="file_ijazah" accept="application/pdf" required><small class="form-text text-muted">Dokumen PDF/JPG Max 2MB</small><!-- <strong> (wajib diisi)</strong> -->
												</div>
												<div class="col-lg-3" id="edit_ijazah">
												</div>
											</div>
										</div>

										<div class="row" id="">
											<div class="form-group">
												<div class="col-lg-2">
													<label>Upload Transkrip</label>
												</div>
												<div class="col-lg-4">
													<input class="form-control" type="text" name="no_transkrip" value="<?= set_value("no_transkrip") ?>" id="no_transkrip" placeholder="Masukkan Nomor Dokumen" required>
												</div>
												<div class="col-lg-3">
													<input class="form-control" type="file" name="file_transkrip" id="file_transkrip" accept="application/pdf" required><small class="form-text text-muted">Dokumen PDF/JPG Max 2MB</small><!-- <strong> (wajib diisi)</strong> -->
												</div>
												<div class="col-lg-3" id="edit_transkrip">
												</div>							
											</div>
										</div>

										<div id="atasan_1">
											<div class="form-group row">
												<div class="col-lg-2">
													<label>NP Atasan</label>								
												</div>
												<div class="col-lg-7">
													<input class="form-control" name="approval_1_np" value="<?= set_value("approval_1_np") ?>" id="approval_1_np" value="" onchange="getNamaAtasan1()" min="4"><small class="form-text text-muted">Atasan Langsung Minimal Kepala Seksi</small><strong> (wajib diisi)</strong>
												</div>								
											</div>

											<div class="form-group row">
												<div class="col-lg-2">
													<label>Nama Atasan</label>
												</div>
												<div class="col-lg-7">														
													<input class="form-control" type="text" name="approval_1_input" value="<?= set_value("approval_1_input") ?>" id="approval_1_input" value="" readonly>
												</div>														
											</div>
											
											<div class="form-group row">
												<div class="col-lg-2">
													<label>Jabatan Atasan</label>
												</div>
												<div class="col-lg-7">														
													<input class="form-control" type="text" name="approval_1_input_jabatan" value="<?= set_value("approval_1_input_jabatan") ?>" id="approval_1_input_jabatan" readonly>	
												</div>														
											</div>
										</div>
	                                        
	                                    <div class="form-group row">
											<div class="col-lg-2">
												<label>Keterangan</label>
											</div>
											<div class="col-lg-7">														
												<input class="form-control" name="keterangan" value="<?= set_value("keterangan") ?>" id="keterangan">
											</div>														
										</div>

										<div class="row">
											<div class="col-lg-9 text-right">
												<input type="submit" name="submit" value="Submit Atasan" class="btn btn-primary">
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->
				
				<?php }
					
				
				if($this->akses["lihat"]){ ?>			
                        
				<!-- filter bulan -->
                <div class="form-group">	
					<div class="row table-responsive">
						<table width="100%" class="table table-striped table-bordered table-hover" id="daftar_pendidikan">
							<thead>
								<tr>
									<th class="text-center no-sort" style="max-width: 5%">No</th>
									<th class="text-center">Pegawai</th>  <!-- style="max-width: 15%" -->
									<th class="text-center no-sort">Nama PT</th>
									<th class="text-center no-sort">Fakultas</th>
									<th class="text-center">Jurusan</th>
									<th class="text-center no-sort" style="max-width: 15%">Keterangan</th>
									<th class="text-center no-sort">Status</th>
									<th class="text-center no-sort">Aksi</th>
								</tr>
							</thead>
							<tbody>
							
							</tbody>
						</table>
						<!-- /.table-responsive -->
					</div>						
				</div>

				<!-- Modal Status -->
				<div class="modal fade" id="modal_status" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_status">Status Laporan Pendidikan</h4>
							</div>
							<div class="modal-body">
								<table>
									<tr>
										<td>Pegawai</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_np_karyawan"></a></td>
									</tr>
									<tr>
										<td>Unit Kerja</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_unit_kerja"></a></td>
									</tr>
									<tr>
										<td>Nama PT</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_perguruan_tinggi"></a></td>
									</tr>
									<tr>
										<td>Fakultas</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_fakultas"></a></td>
									</tr>
									<tr>
										<td>Jurusan</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_jurusan"></a></td>
									</tr>
									<tr>
										<td>Jenjang</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_jenjang"></a></td>
									</tr>
									<tr>
										<td>Akreditasi</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_akreditasi"></a></td>
									</tr>
									<tr>
										<td>Keterangan</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="detail_keterangan"></a></td>
									</tr>
									<tr>
										<td>Dibuat Tanggal</td>
										<td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
										<td><a id="status_created_at"></a></td>
									</tr>
								</table>
								
								<br>
								
								<div class="alert alert-info" id="approver_atasan">
									<strong><a id="status_approval_nama"></a></strong><br>
									<p id="status_approval_atasan_status"></p>
									<p id="status_approval_atasan_alasan" style="margin: 0;padding: 0;"></p>
								</div>
								
								<div class="alert alert-info" id="approver_sdm" style="display:none">
									<p id="status_approval_sdm_status"></p>
									<p id="status_approval_sdm_alasan" style="margin: 0;padding: 0;"></p>
								</div>
							</div>						
						</div>
						<!-- /.modal-content -->
					</div>
					<!-- /.modal-dialog -->
				</div>
				<!-- /.modal -->

				<!-- Modal -->
				<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_batal">Status Laporan Pendidikan</h4>
							</div>
							<div class="modal-body">
								<div class="get-approve"></div>
							</div>
						</div>
					</div>
				</div>
				<!-- /.modal -->
                <?php }


                if(@$akses["hapus"]){ ?>
				<!--begin: Modal Inactive -->
                <div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title text-danger" id="title-inactive">
                                    <b>Hapus Laporan Pendidikan</b>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </h4>
                            </div>

                            <div class="modal-body">
                                <h4 id="message-inactive"></h4>
                            </div>
                            <div class="modal-footer">
								<a href="" id="inactive-action" class="btn btn-danger">Ya, Hapus</a>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
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
		
		<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
		<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
		<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
        <script type="text/javascript" src="<?= base_url()?>asset/daterangepicker/daterangepicker.min.js"></script>
        <script src="<?= base_url('asset') ?>/sweetalert2/sweetalert2.js"></script>

		<script type="text/javascript">
            var all_atasan_1_np=[], all_atasan_1_jabatan=[], all_atasan_2_np=[], all_atasan_2_jabatan=[];
            $('#multi_select').select2({
  				closeOnSelect: false
  				//minimumResultsForSearch: 20
			});
			$(document).ready(function() {
                $('.datetimepicker5').datetimepicker({
                    format: 'HH:mm'
                });
                $('.datependidikan').datetimepicker({
                    format: 'YYYY-MM-DD'
                });
                $('.select2').select2();

				$('#daftar_pendidikan').DataTable().destroy();				
				table_serverside();
			});
            
            function refresh_table_serverside() {
				$('#daftar_pendidikan').DataTable().destroy();				
				table_serverside();
			}
			
			function table_serverside()
			{
                var table;

				table = $('#daftar_pendidikan').DataTable({ 
					
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
						"url": "<?php echo site_url("pelaporan/pendidikan/tabel_pendidikan/")?>",
						"type": "POST",
					},

					//Set column definition initialisation properties.
					"columnDefs": [
					{ 
						"targets": 'no-sort', //first column / numbering column
						"orderable": false, //set not orderable
					},
					],

				});

			};
		</script>
		
		<script>
			function getNama(){
			var np_karyawan = $('#np_karyawan').val();
			
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('perizinan/permohonan_perizinan/ajax_getNama');?>",
             data: "vnp_karyawan="+np_karyawan,
				success: function(msg){
					if(msg == ''){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
						$('#np_karyawan').val('');
						$('#nama').text('');
						$("#form_absence_type").hide();
					}else{							 
						$('#nama').text(msg);
						$("#form_absence_type").show();
					}													  
				 }
			 });       
		} 
		</script>
		
		<script>
			function listNp(){			
			$.ajax({
             type: "POST",
             dataType: "html",
             url: "<?php echo base_url('perizinan/permohonan_perizinan/ajax_getListNp');?>",
				success: function(msg){
					if(msg == ''){
						alert ('Silahkan isi No. Pokok Dengan Benar.');
						$('#list_np').text('');
					}else{							 
						$('#list_np').text(msg);
					}													  
				 }
			 });       
			} 
		</script>
		
		<script>
			const edit = async (data) => {
				$('#btn-tambah').html('Edit data');
				if($("#collapseOne").is(":visible")){
					console.log('Already shown');
				} else{
					$('#btn-tambah').trigger('click');
				}
				
				let fields = ['np_karyawan', 'perguruan_tinggi', 'fakultas', 'jurusan', 'jenjang', 'akreditasi', 'tgl_masuk', 'tgl_selesai', 'no_ijazah', 'no_transkrip', 'approval_1_np', 'keterangan'];
				for (const i of fields) {
					$(`#${i}`).val($(data).data(`${i}`));
				}

				$('#np_karyawan').trigger('change');
				$('#jenjang').trigger('change');
				$('#approval_1_np').trigger('change');
		        $('#file_ijazah').prop('required',false);
				$('#edit_ijazah').html('<label class="text-danger"><b>Upload Ulang Jika Ingin Mengganti File</b></label>');
        		$('#file_transkrip').prop('required',false);
				$('#edit_transkrip').html('<label class="text-danger"><b>Upload Ulang Jika Ingin Mengganti File</b></label>');

				if($('#formulir_tambah').find('[name=edit_id]').length){
					$('#formulir_tambah').find(`[name=edit_id]`).val($(data).data('id'));
				} else{
					$('<input>').attr({
						type: 'hidden',
						name: 'edit_id',
						value: $(data).data('id')
					}).appendTo('#formulir_tambah');
				}
				$("html, body").animate({ scrollTop: 0 }, "slow");
			}

			$(document).on('click','.detail_button',function(e){
	            e.preventDefault();
	            $("#modal_detail").modal('show');
	            $.post('<?php echo site_url("pelaporan/pendidikan/view_detail") ?>',
	                {id_:$(this).attr('data-id')},
	                function(e){
	                    $(".get-approve").html(e);
	                }
	            );
	        });
            
            function get_approval(){
                let insert_np_karyawan = $('#np_karyawan').find(':selected').val();
                let insert_absence_type = $('#add_absence_type').val();
                $('#approval_1_np').find('option').remove();
                $('#approval_2_np').find('option').remove();
                all_atasan_1_np=[];
                all_atasan_1_jabatan=[];
                all_atasan_2_np=[];
                all_atasan_2_jabatan=[];
                
                $.ajax({
                    url: "<?php echo base_url('perizinan/filter_approval/get_approval');?>",
                    type: "POST",
                    dataType: "json",
                    data: {np:insert_np_karyawan, absence_type:insert_absence_type},
				    success: function(response){
                        if(response.data.atasan_1.length>0){
                            $('#approval_1_jabatan').val(response.data.atasan_1[0].nama_jabatan);
                            $.each(response.data.atasan_1, function(i, item) {
                                all_atasan_1_np.push(response.data.atasan_1[i].no_pokok);
                                all_atasan_1_jabatan.push(response.data.atasan_1[i].nama_jabatan);
                                $('#approval_1_np').append(`<option value="`+response.data.atasan_1[i].no_pokok+`">`
                                    +response.data.atasan_1[i].no_pokok+` - `+response.data.atasan_1[i].nama+
                                `</option>`);
                            });
                        }
                        if(response.data.atasan_2.length>0){
                            $('#approval_2_jabatan').val(response.data.atasan_2[0].nama_jabatan);
                            $.each(response.data.atasan_2, function(i, item) {
                                all_atasan_2_np.push(response.data.atasan_2[i].no_pokok);
                                all_atasan_2_jabatan.push(response.data.atasan_2[i].nama_jabatan);
                                $('#approval_2_np').append(`<option value="`+response.data.atasan_2[i].no_pokok+`">`
                                    +response.data.atasan_2[i].no_pokok+` - `+response.data.atasan_2[i].nama+
                                `</option>`);
                            });
                        }
                    },
                    error: function(e){
                        console.log(e);
                    }
                });
            }

            function getNamaAtasan1() {
				var np_atasan = $('#approval_1_np').val();
                if (np_atasan.length>3) {
                    var np_karyawan = $('#np_karyawan').val();
                    var insert_absence_type = $('#add_absence_type').val();

                    $.ajax({
                        type: "POST",
                        dataType: "JSON",
                        url: "pendidikan/ajax_getNama_approval",
                        data: {"np_aprover":np_atasan, "np_karyawan":np_karyawan},
                        success: function(msg){
                            if(msg.status == false){
                                alert (msg.message);
                                $('#approval_1_np').val('');
                                $('#approval_1_input').val('');
                                $('#approval_1_input_jabatan').val('');
                            }else{							 
                                $('#approval_1_input').val(msg.data.nama);
                                $('#approval_1_input_jabatan').val(msg.data.jabatan);
                            }													  
                        }
                    });
                } else if (np_atasan.length<4) {
                    $('#approval_1_input').val('');
                    $('#approval_1_input_jabatan').val('');
                }
			}
            

            $('input[name="dates"]').daterangepicker({
                locale: {
                    format: 'DD-MM-YYYY'
                }
            });

            $(document).on('click','.hapus',function(e){
	            e.preventDefault();
	            Swal.fire({
				  	title: 'Anda yakin ingin menghapus data ini?',
				  	icon: 'warning',
				  	showCancelButton: true,
				  	confirmButtonColor: '#3085d6',
				  	cancelButtonColor: '#d33',
				  	confirmButtonText: 'Ya, Hapus',
				  	cancelButtonText: 'Batal'
				}).then((result) => {
				  	if (result.isConfirmed) {
			            $.get('<?php echo site_url("pelaporan/pendidikan/hapus") ?>/'+$(this).data('id')+'/'+$(this).data('np'),
			                function(get){
			                	ret = JSON.parse(get);
			                	if (ret.status==true) {
								    Swal.fire(
								      ret.msg,
								      '',
								      'success'
								    );
								    refresh_table_serverside();
								} else {
									Swal.fire(
								      ret.msg,
								      '',
								      'error'
								    );
								}
			                }
			            );
				  	}
				})

	        });
		</script>
		
		