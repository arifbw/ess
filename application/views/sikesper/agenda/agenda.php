<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />

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

		<?php if(!empty($success)) { ?>
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $success;?>
		</div>
		<?php } if(!empty($this->session->flashdata('success'))) { ?>
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $this->session->flashdata('success');?>
		</div>
		<?php } if(!empty($warning)) { ?>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $warning;?>
		</div>
		<?php } if(!empty($this->session->flashdata('warning'))){ ?>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $this->session->flashdata('warning');?>
		</div>
		<?php } if(@$akses["lihat log"]) { ?>
		<div class='row text-right'>
			<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
			<br><br>
		</div>
		<?php } if(@$akses["scan"]) { ?>
		<div class='row text-right'>
			<button class='btn btn-primary btn-md' data-toggle='modal' data-target='#modal_scan'>Scan</button>
			<br><br>
		</div>
		<?php } if(@$akses["tambah"]) { ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a href="<?= site_url('sikesper/agenda/form/tambah'); ?>">Tambah <?php echo $judul;?></a>
						</h4>
					</div>
				</div>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->
		<?php 
			} if(@$this->akses["lihat"]) {
		?>
		<div class="row" style="margin-bottom: 2% !important;">
			<div class="col-md-4">
				<select id="tahun-agenda">
					<option value="">Semua Tahun Agenda</option>
				</select>
			</div>
			<div class="col-md-4 col-md-offset-4">
				<input type="text" class="form-control datatable-searchable" placeholder="Pencarian" />
			</div>
		</div>
		<div class="row">
			<table width="100%" class="table table-striped table-bordered table-hover" id="agenda-datatable">
				<thead>
					<tr>
						<th class='text-center'>#</th>
						<th class='text-center'>Agenda</th>
						<?php if($_SESSION['grup']!=5) { ?>
						<th class='text-center jumlah-daftar'>Jumlah<br>Pendaftar</th>
						<?php } ?>
						<th class='text-center'>Aksi</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
			<!-- /.table-responsive -->
		</div>

		<!-- Modal -->
		<div class="modal fade" id="modal_detail" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
			<div class="modal-dialog modal-lg" id="detail_content">
			</div>
		</div>
		<?php
			}if(@$akses["ubah"]) { 
		?>

		<!-- Modal -->
		<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		 	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="exampleModalLongTitle"><strong>Ubah Data</strong></h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
		        		<form role="form" action="<?= site_url('sikesper/ketentuan/info_provider/index'); ?>" id="formulir_tambah" method="post">
							<input type="hidden" name="aksi" value="ubah"/>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Nama</label>
									</div>
									<div class="col-lg-7">
										<input id="nama-provider" class="form-control" name="nama" placeholder="Masukkan Nama" required>
									</div>
									<div id="warning_nama" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Tipe</label>
									</div>
									<div class="col-lg-7">
                                        <select class="form-control" name="tipe" id="tipe-provider" required>
                                            <option value="" data-nama_mst_bbm="">-- Pilih --</option>
                                            <option value="Rumah Sakit">Rumah Sakit</option>
                                            <option value="Klinik">Klinik</option>
                                        </select>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>No Telepon</label>
									</div>
									<div class="col-lg-7">
										<input id="telp-provider" class="form-control" name="no_telp" placeholder="Masukkan No Telepon" required>
									</div>
									<div id="warning_no_telp" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Provinsi</label>
									</div>
									<div class="col-lg-7">
										<select class="provinsi-ubah" name="id_provinsi">
											
										</select>
									</div>
									<div id="warning_provinsi" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Kabupaten</label>
									</div>
									<div class="col-lg-7">
										<select class="kabupaten-ubah" name="id_kabupaten">
											
										</select>
									</div>
									<div id="warning_kabupaten" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Alamat</label>
									</div>
									<div class="col-lg-7">
										<textarea id="alamat-provider" class="form-control" name="alamat" required></textarea>
									</div>
									<div id="warning_alamat" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Catatan</label>
									</div>
									<div class="col-lg-10">
										<textarea id="catatan-provider" class="summernote" name="catatan"></textarea>
									</div>
									<div id="warning_catatan" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Status</label>
									</div>
									<div class="col-lg-7">
										<label class='radio-inline'>
											<input id="1" type="radio" name="status" value="1" required>Aktif
										</label>
										<label class='radio-inline'>
											<input id="0" type="radio" name="status" value="0" required>Non Aktif
										</label>
									</div>
									<div id="warning_status" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<input type="hidden" id="nomor-provider" name="no">
							<div class="row">
								<div class="col-lg-12 text-center">
									<button type="submit" class="btn btn-primary">Simpan</button>
								</div>
							</div>
						</form>
		      		</div>
		    	</div>
		  	</div>
		</div>
		<?php }
					
					if(@$akses["scan"]){
				?>
						<!-- Modal -->
						<div class="modal fade" id="modal_scan" tabindex="-1" role="dialog" aria-labelledby="label_modal_scan" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form role="form" action="" id="formulir_scan" method="post">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
											<h4 class="modal-title" id="label_modal_scan">Scan <?php echo $judul;?></h4>
										</div>
										<div class="modal-body">
											<input type="hidden" name="aksi" value="scan"/>
											<div class="row">
												<div class="form-group">
													<div class="col-lg-3">
														<label>Kode Scan</label>
													</div>
													<div class="col-lg-6">
														<input class="form-control" name="kode_scan" value="" placeholder="Kode Scan">
													</div>
													<div id="warning_kode_scan" class="col-lg-3 text-danger"></div>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary" onclick="return cek_simpan_scan()">Simpan</button>
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
										</div>
									</form>
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
						</div>
						<!-- /.modal -->
				<?php
					} ?>

		<div class="modal fade" id="modal-detail" role="dialog" aria-labelledby="detail-title" aria-hidden="true">
		 	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="detail-title"><strong>Detail Data</strong></h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>
		      		<div class="modal-body detail-content">
		      		</div>
		      		<div class="modal-footer">
		      			<div class="row" style="padding:5%;">
							<div class="col-12 text-center" id="btn-action">
								
							</div>
						</div>
		      		</div>
		    	</div>
		  	</div>
		</div>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script type="text/javascript">
	var odata;

	function RefreshTable(tableId, urlData) {
	    $.getJSON(urlData, null, function(json) {
	        table = $(tableId).dataTable();
	        oSettings = table.fnSettings();

	        table.fnClearTable(this);

	        for (var i = 0; i < json.aaData.length; i++) {
	            table.oApi._fnAddData(oSettings, json.aaData[i]);
	        }

	        oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
	        table.fnDraw();
	    });
	}

	function generateOption(el, url, text)
	{
		var option = 
			$(el).select2({
				width: "100%",
		       	allowClear: true,
		       	placeholder: text,
		       	ajax: {
		          	dataType: 'json',
		          	url: url,
		          	delay: 800,
		          	data: function(params) {
		            	return {
		              		search: params.term
		            	}
		          	},
		          	Results: function (data, page) {
		          		return {
		            		results: data
		          		};
		        	},
		      	}
		    });

		return option;
	}

	$(document).ready(function() {
		generateOption('#tahun-agenda', '<?= base_url('sikesper/agenda/tahunAgenda'); ?>', 'Semua Tahun');
	});

</script>

<script type="text/javascript">
	$(document).ready(function() {
		$(document).on('click', '.detail', function() {
			let key = $(this).data('id');

			$('#detail-title').text('Detail Data');

			$.ajax({
				url: "<?= base_url().'sikesper/agenda/show/'; ?>"+key,
				type: "GET",
				success: function(data) {
					$('.detail-content').html(data.result);
				}
			});
		});

		$(document).on('click', '.daftar-peserta', function() {
			let key = $(this).data('agd');

			$('#detail-title').text('Daftar Peserta');

			$.ajax({
				url: "<?= base_url().'sikesper/agenda/daftarpeserta/'; ?>"+key,
				type: "GET",
				success: function(data) {
					$('.detail-content').html(data.result);
				}
			});
		});

		$(document).on('click', '.delete', function(e) {
			e.preventDefault();

			let no = $(this).data('key');

			$.ajax({
				url: "<?= base_url('sikesper/agenda/checkDisable/') ?>",
				type: "POST",
				data: {agenda: no}
			}).then(function(data) {
				if(data.response == 'bisa'){
					Swal.fire({
					  title: 'Apakah ingin menghapus data?',
					  icon: 'warning',
					  showCancelButton: true,
					  confirmButtonColor: '#3085d6',
					  cancelButtonColor: '#d33',
					  confirmButtonText: 'Ya, hapus!',
					  cancelButtonText: 'Batal',
					}).then((result) => {
					  if (result.value) {
					  	$.ajax({
					  		url: "<?= base_url('sikesper/agenda/hapus/') ?>"+no,
					  		type: "GET",
					  		success: function() {
					  			location.reload();
					  		}
					  	});
					  }
					});
				}else if(data.response == 'tidak bisa'){
					Swal.fire(
					  'Tidak bisa dihapus !',
					  'Agenda ini sudah memiliki peserta',
					  'warning'
					);
				}
			});
		});
	});
</script>

<!-- javascript untuk di modal -->
<script type="text/javascript">

	function generateHtml(response=null) {
        <?php if ($_SESSION["grup"]!=4){?>
		if(response === 'tersedia'){
			$('#btn-action').html('<div class="form-check"><input name="setuju" class="form-check-input accept-btn" type="checkbox" value="" id="defaultCheck1"><label class="form-check-label" for="defaultCheck1"> Saya ingin mendaftar ke agenda ini.</label></div><br/><button class="btn btn-primary btn-block daftar-agenda" type="button" disabled>Daftar Ke Agenda</button>');
		}else if(response === 'penuh'){
			$('#btn-action').html('<button class="btn btn-sm btn-danger">Maaf, kuota agenda sudah penuh.</button>');
		}else if(response === 'terdaftar'){
			$('#btn-action').html('<button class="btn btn-sm btn-success">Anda sudah terdaftar pada agenda ini.</button>');
		}
        <?php } else{ ?>
        if(response === 'tersedia'){
            $('#btn-action').html(`<div class="form-check">
                                        <input name="setuju" class="form-check-input accept-btn" type="checkbox" value="" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1"> Daftarkan karyawan ke agenda ini.</label>
                                    </div>
                                    <div class="" style="display: none;" id="div-list_karyawan">
                                        <select style="width: 100%;" name="list_karyawan" class="select2 form-check-input" id="list_karyawan" onchange="check_btn_enable()" multiple></select>
                                    </div>
                                    <div id="div-message"></div>
                                    <br/>
                                    <button class="btn btn-primary btn-block daftarkan-karyawan" type="button" disabled id="btn-register" onclick="registrasi_karyawan();">Daftar Ke Agenda</button>`);
            generateOption('#list_karyawan', '<?= base_url('sikesper/agenda/list_karyawan/'); ?>' + $('#form-detail').data('agenda'), 'Pilih karyawan (bisa lebih dari satu)');
		}
        <?php }?>
	}
    
    <?php if($_SESSION['grup']==4){?>
    function check_btn_enable(){
        let list_karyawan = $('#list_karyawan').val();
        if(list_karyawan.length>0) {
            $('#btn-register').prop('disabled',false);
        } else {
            $('#btn-register').prop('disabled',true);
        }
    }
    
    function registrasi_karyawan(){
        $('#btn-register').prop('disabled',true);
        $('#btn-register').html('Memproses...');
        let form = $('#form-detail');
        let agenda = form.data('agenda');
        let kry = $('#list_karyawan').val();
        
        $.ajax({
            url: "<?= base_url('sikesper/agenda/registrasi_karyawan') ?>",
            type: "POST",
            dataType: "json",
            data: {kry: kry, agenda: agenda},
            success: function(response) {	
                return response;
            },
            error: function(){
                console.log('error');
            }
        }).then(function(response) {
            $('#div-message').html(response.message);
            $('#btn-register').prop('disabled',false);
            $('#btn-register').html('Daftar Ke Agenda');
            odata.ajax.reload();
            
            setTimeout(function(){
                generateHtml('tersedia');
            }, 2000);
            
        }).catch((error) => {
            console.error(error);
            generateHtml('tersedia');
            odata.ajax.reload();
        });
    }
    <?php } ?>

	$(document).ready(function() {			
		if($('.jumlah-daftar').length){
			var ocolumn = [
		            {
		                data: 'agenda',
		                searchable: false,
		                className: 'text-center',
		                render: function(data, type, row, meta){
		                    return meta.row + meta.settings._iDisplayStart + 1;
		                }
		            }, {
		                data: 'agenda',
		                name: 'agenda'
		            }, {
		                data: 'jml_daftar',
		                name: 'jml_daftar',
		                className: 'text-center'
		            }, {
		                data: 'action',
		                name: 'pilihan',
		                orderable: false,
		                searchable: false,
		                className: 'text-center'
		            }
		        ];
		}else{
			var ocolumn = [
		            {
		                data: 'agenda',
		                searchable: false,
		                className: 'text-center',
		                render: function(data, type, row, meta){
		                    return meta.row + meta.settings._iDisplayStart + 1;
		                }
		            }, {
		                data: 'agenda',
		                name: 'agenda'
		            }, {
		                data: 'action',
		                name: 'pilihan',
		                orderable: false,
		                searchable: false,
		                className: 'text-center'
		            }
		        ];
		}

		odata = 
		    generateDataTable(
		        '#agenda-datatable', 
		        "sikesper/agenda/getAgenda", 
		        ocolumn, 
		        [{
		            id: "#tahun-agenda",
		            param: "tahun"
		        }]
		    );
        
        <?php if(@$kegiatan_id!=''){?>
        
        jQuery.when(odata.search('<?= $kegiatan_name?>').draw()).done(
             setTimeout(function(){ search_attr(); }, 1000)
        );
        
        function search_attr(){
            let i=0;
            while ($('*[data-id="<?= $kegiatan_id?>"]').length==0 && i<100) {
                console.log('searching attribute...');
                i++;
            }
            $('.detail[data-id="<?= $kegiatan_id?>"]').get(0).click();
        }
        
        <?php }?>
	});

	$(document).on('show.bs.modal', '#modal-detail', function() {
		setTimeout(function(){
			var form = $('#form-detail');
			var kry = form.data('kry');
			var agenda = form.data('agenda');
			var role = form.attr('role');

			if(role == 'pengguna' || role == 'admin'){
				$.ajax({
					url: "<?= base_url('sikesper/agenda/cekAgenda') ?>",
					type: "POST",
			  		dataType: "json",
			  		data: {kry: kry, agenda: agenda},
			  		success: function(data) {
			  			generateHtml(data.response);		  			
			  		}
				});
			}
			
        	map.invalidateSize();
		}, 1000);		
	});

	$(document).on('hide.bs.modal', '#modal-detail', function() {
		$('#btn-action').html('');
	});

	$(document).on('click', '.accept-btn', function() {
		if($('input[name="setuju"]:checked').length > 0){
            <?php if($_SESSION["grup"]!=4){?>
                $('.daftar-agenda').prop("disabled", false);
            <?php } else{?>
                $('#div-list_karyawan').show();
                check_btn_enable();
            <?php } ?>
		}else{
            <?php if($_SESSION["grup"]!=4){?>
                $('.daftar-agenda').prop("disabled", true);
            <?php } else{?>
                $('#div-list_karyawan').hide();
                $('#btn-register').prop("disabled", true);
            <?php } ?>
		}
	});

	$(document).on('click', '.daftar-agenda', function() {
		var form = $('#form-detail');
		var kry = form.data('kry');
		var agenda = form.data('agenda');

		Swal.fire({
		  title: 'Apakah ingin mendaftar ke agenda ini?',
		  icon: 'warning',
		  showCancelButton: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Ya, daftar!',
		  cancelButtonText: 'Batal',
		}).then((result) => {
		  if (result.value) {
		  	$.ajax({
		  		url: "<?= base_url('sikesper/agenda/daftarAgenda') ?>",
		  		type: "POST",
		  		dataType: "json",
		  		data: {kry: kry, agenda: agenda}
		  	}).then(function() {
		  		$.ajax({
					url: "<?= base_url('sikesper/agenda/cekAgenda') ?>",
					type: "POST",
			  		dataType: "json",
			  		data: {kry: kry, agenda: agenda},
			  		success: function(data) {
			  			generateHtml(data.response);
			  			odata.ajax.reload();	
			  		}
				});
		  	});
		  }
		});
	});
</script>
