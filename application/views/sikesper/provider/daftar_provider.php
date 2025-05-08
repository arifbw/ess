<style type="text/css">
	.tata-cara-content {
		background: #f0f5f5;
		font-size: 12px;
		font-size: 1vw;

		width: 100%;
		overflow: hidden;
		text-align: justify;

		padding: 5%;
		margin-top: 5%;
		margin-bottom: 3%;
	}

	.tata-cara-content h2 {
		text-align: left;
	}

	.search-input {
		font-family:Courier
	}
	.search-input,
	.leaflet-control-search {
		max-width:400px;
	}

</style>

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
		<?php } if(!empty($warning)) { ?>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $warning;?>
		</div>
		<?php } ?>
		<div class='row'>
			<div class='col-lg-6 col-md-offset-6' style="margin-bottom: 2% !important;">
				<div class="row">
					<?php if(@$akses["tambah"]) { ?>
					<div class="col-md-9 text-right">
						<button class='btn btn-primary btn-md' data-toggle="modal" data-target="#modal-import">Import Data</button>
					</div>
					<?php }  ?>
					<?php if(@$akses["lihat log"]) { ?>
					<div class="col-md-3 text-right">
						<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php if(@$akses["tambah"]) { ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a href="<?= site_url('sikesper/ketentuan/info_provider/form/tambah'); ?>">Tambah <?php echo $judul;?></a>
						</h4>
					</div>
				</div>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->
		<?php } 

		if(@$this->akses["lihat"]) {
				// if($this->session->userdata('grup') != '5'){ 
		?>
        <div class="row">
            <div class="col-lg-2">Kab/Kota</div>
            <div class="col-lg-4">
                <select class="form-control select2" id="filter_kota" onchange="reload_table()" style="width: 100%;">
                    <option value="all" selected>Semua</option>
                    <?php
                    foreach($filter_kabupaten as $row){
                        echo '<option value="'.$row->id_kabupaten.'">'.$row->nama.'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        
		<div class="row">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_daftar_provider">
					<thead>
						<tr>
							<th class='text-center'>#</th>
							<th class='text-center'>Nama</th>
							<th class='text-center'>Tipe</th>
							<th class='text-center'>No Telepon</th>
							<th class='text-center'>Alamat</th>
							<th class='text-center' style="width:15%;">Aksi</th>
						</tr>
					</thead>
				</table>
				<!-- /.table-responsive -->
			</div>
		</div>

		
		<?php 
		}

		if($akses["ubah"]) { ?>
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
		<?php } ?>

		<div class="modal fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		 	<div class="modal-dialog modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="exampleModalLongTitle"><strong>Detail Data</strong></h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>
		      		<div class="modal-body detail-content">
		      		</div>
		    	</div>
		  	</div>
		</div>

		<div class="modal fade" id="modal-import" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		 	<div class="modal-dialog modal-dialog-centered" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title" id="import-title"><strong>Import Data</strong></h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
		      			<form id="import-excel" action="<?= base_url('sikesper/ketentuan/info_provider/import_data'); ?>" method="POST" enctype="multipart/form-data">
			      			<div class="alert alert-info alert-dismissable">
								<center>
									<a class="btn btn-primary" href="<?= base_url('file/template/IMPORT_PROVIDER.xlsx') ?>">Klik Disini Download Template File Untuk Mengisi Data Provider</a>
									<br><br>
									<h4>Upload File Pada Form Dibawah Ini Untuk Import Data Provider<br>Sesuai Template Diatas</h4>
				      				<div class="form-group">
				      					<input type="file" name="import_excel" class="form-control upload-import" />
				      				</div>
				      				<div class="form-group">
				      					<button class="btn btn-primary btn-import" type="submit">Import</button>
				      				</div>
			      				</center>
							</div>
		      			</form>
		      		</div>
		    	</div>
		  	</div>
		</div>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script type="text/javascript">
    var table;
	 $(document).on('click', '.detail', function() {
    	var id = $(this).data('id');

    	$.ajax({
    		url: "<?php echo site_url('sikesper/ketentuan/info_provider/show/'); ?>"+id,
    		type: "GET",
    		success: function(data) {
    			$('.detail-content').html(data.result);
    		}
    	});
    });

	 $(document).ready(function(){
 
        $('#import-excel').submit(function(e){
            e.preventDefault(); 
            // alert($(this).prop('action')); 2021-05-31 dicomment
                 $.ajax({
                     url: $(this).prop('action'),
                     type:"post",
                     data:new FormData(this),
                     processData:false,
                     contentType:false,
                     cache:false,
                     async:false,
                     beforeSend: function () {
					    $('.btn-import, .upload-import').prop('disabled', true);
					 },
					 complete: function () {
					    $('.btn-import, .upload-import').prop('disabled', false);
					 },
                     success: function(data){
                        alert(data.response);
                        location.replace("<?= site_url('sikesper/ketentuan/info_provider') ?>");
                   	 }
                 });
            });
    });

	 $(document).on('show.bs.modal', '#modal-detail', function() {
		setTimeout(function(){
        	map.invalidateSize();
		}, 1000);		
	});
    
    function reload_table(){
        let filter_kota = $('#filter_kota').find(':selected').val();
        table = $('#tabel_daftar_provider').DataTable({
            destroy:true,
            "iDisplayLength": 10,
            "language": {
                "url": "<?php echo base_url('asset/datatables/Indonesian.json');?>",
                "sEmptyTable": "Tidak ada data di database",
                "emptyTable": "Tidak ada data di database"
            },
            "processing": true,
            "serverSide": false,
            "ordering": false,
            "ajax": {
                "url": "<?php echo site_url("sikesper/ketentuan/info_provider/tabel/")?>"+ filter_kota,
                "type": "POST",
            }
        });
    }
</script>