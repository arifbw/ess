<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url().'asset/summernote/summernote-bs4.css';?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/toastr-2.1.4/toastr-2.1.3.min.css')?>">
<style type="text/css">
	.tata-cara-content {
		background: #f0f5f5;
		font-size: 14px;

		width: 100%;
		overflow: hidden;
		text-align: justify;

		padding: 5%;
		margin-top: 5%;
		margin-bottom: 3%;
	}

	@media only screen and (max-width: 720px) {

	   .tata-cara-content { 
	      font-size: 2vw !important; 
	   }

	   .tata-cara-content h2 { 
	      font-size: 2.3vw !important;
	      font-weight: 600;
	   }

	}

	.tata-cara-content h2 {
		text-align: left;
	}
    #toast-container>.toast-warning {
        background-image: none !important;
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
		<?php } if(@$akses["lihat log"]) { ?>
		<div class='row text-right'>
			<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
			<br><br>
		</div>
		<?php } if(@$akses["tambah"]) { ?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse <?php echo $panel_tambah;?>">
						<div class="panel-body">
							<form role="form" action="<?= base_url('sikesper/ketentuan/reimbursement'); ?>" id="formulir_tambah" method="post">
								<input type="hidden" name="aksi" value="tambah"/>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-2">
											<label>No Urut</label>
										</div>
										<div class="col-lg-7">
											<input class="form-control" name="no_urut" placeholder="Masukkan Nomor Urut" required>
										</div>
										<div id="warning_kode" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-2">
											<label>Judul</label>
										</div>
										<div class="col-lg-7">
											<input class="form-control" name="judul" placeholder="Masukkan Judul" required>
										</div>
										<div id="warning_kategori" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-2">
											<label>Tata Cara</label>
										</div>
										<div class="col-lg-10">
											<textarea id="tambah-tata-cara" class="summernote" name="tata_cara"></textarea>
										</div>
										<div id="warning_kategori" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-lg-2">
											<label>Status</label>
										</div>
										<div class="col-lg-7">
											<label class='radio-inline'>
												<input type="radio" name="status" value="1" required>Aktif
											</label>
											<label class='radio-inline'>
												<input type="radio" name="status" value="0" required>Non Aktif
											</label>
										</div>
										<div id="warning_status" class="col-lg-3 text-danger"></div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12 text-center">
										<button type="submit" class="btn btn-primary" onclick="return cek_simpan_tambah()">Simpan</button>
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
		<?php 
			} if(@$this->akses["lihat"]) {
				if($this->session->userdata('grup') != '5'){ 
		?>
		<div class="row">
			<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_daftar_reimburse">
				<thead>
					<tr>
						<th class='text-center'>#</th>
						<th class='text-center'>No Urut</th>
						<th class='text-center'>Judul</th>
						<th class='text-center'>Status</th><!-- heru menambahkan ini, 2020-12-23, 13:58 -->
						<?php
							if(@$akses["ubah"] or @$akses["lihat log"]) {
								echo "<th class='text-center'>Aksi</th>";
							}
						?>
					</tr>
				</thead>
				<tbody>
					<?php 
						$start = 1;
						foreach($daftar_reimburse as $val){ 
					?>
					<tr>
						<td><?= $start++; ?></td>
						<td><?= $val->no_urut; ?></td>
						<td><?= $val->judul; ?></td>
						<td><?= $val->status=='1'?'Aktif':'Non Aktif' ?></td><!-- heru menambahkan ini, 2020-12-23, 13:58 -->
						<?php if(@$akses["ubah"] or @$akses["lihat log"]) { ?>
							<td class='text-center'>
								<?php if(@$akses["ubah"]){ ?>
									<button type='button' class='btn btn-primary btn-xs ubah' data-id="<?= $val->id ?>" data-toggle="modal" data-target="#modal_ubah">Ubah</button> 
									<button type='button' class='btn btn-success btn-xs tata-cara-reimburse' data-id="<?= $val->id ?>" data-toggle="modal" data-target="#cara-reimburse">Tata Cara</button> 
								<?php } ?>
								<?php if(@$akses["lihat log"]){ ?>
									<button class='btn btn-primary btn-xs' onclick='lihat_log(\"<?= $val->id ?>\",<?= $val->id ?>)'>Lihat Log</button>
								<?php } ?>
							</td>
						<?php } ?>
					</tr>
					<?php 
						} 
					?>
				</tbody>
			</table>
			<!-- /.table-responsive -->
		</div>

		<!-- modal -->
		<div class="modal fade" id="cara-reimburse" tabindex="-1" role="dialog" aria-labelledby="judul-reimbursement" aria-hidden="true">
		  	<div class="modal-dialog modal-lg" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h3 class="modal-title">Tata Cara Reimbursement</h3>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
		      			<div class="tata-cara-content">
			      			<h3 id="judul-reimbursement"></h3><br/>
			      			<div class="content-reimburse">
			      				
			      			</div>
			      		</div>
		      		</div>
		    	</div>
		  	</div>
		</div>
		<?php 
			}elseif($this->session->userdata('grup') == '5'){
		?>
		<div class="row">
			<?php foreach($daftar_reimburse as $val){ ?>
			<div class="col-md-12">
				<div class="tata-cara-content">
					<h2><strong><a href="<?= base_url('sikesper/ketentuan/reimbursement/read_more/'.$val->judul)?>" target="_blank"><?= $val->judul; ?></a></strong></h2>
					<br>
					<?php $pieces = explode(" ", strip_tags($val->tata_cara,'<img><br>'));
                    $first_part = implode(" ", array_splice($pieces, 0, 50)).'...<a href="'.base_url('sikesper/ketentuan/reimbursement/read_more/'.$val->judul).'" target="_blank">Read more</a>';
                    echo $first_part;
                    ?>
				</div>	
			</div>
			<?php } ?>
		</div>
		<?php
			}
		}if(@$akses["ubah"]) { ?>

		<!-- Modal -->
		<div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		 	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		    	<div class="modal-content">
		      		<div class="modal-header">
		        		<h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
		        		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          			<span aria-hidden="true">&times;</span>
		        		</button>
		      		</div>
		      		<div class="modal-body">
		        		<form role="form" action="<?= base_url('sikesper/ketentuan/reimbursement'); ?>" id="formulir_tambah" method="post">
							<input type="hidden" name="aksi" value="ubah"/>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>No Urut</label>
									</div>
									<div class="col-lg-7">
										<input id="no-urut" class="form-control" name="no_urut" placeholder="Masukkan Nomor Urut" required>
									</div>
									<div id="warning_kode" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Judul</label>
									</div>
									<div class="col-lg-7">
										<input id="judul-reimburse" class="form-control" name="judul" placeholder="Masukkan Judul" required>
									</div>
									<div id="warning_kategori" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Tata Cara</label>
									</div>
									<div class="col-lg-10">
										<textarea id="tata-cara" class="summernote" name="tata_cara"></textarea>
									</div>
									<div id="warning_kategori" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<div class="form-group">
								<div class="row">
									<div class="col-lg-2">
										<label>Status</label>
									</div>
									<div class="col-lg-7">
										<label class='radio-inline'>
											<input type="radio" name="status" value="1" id="1" required>Aktif
										</label>
										<label class='radio-inline'>
											<input type="radio" name="status" value="0" id="0" required>Non Aktif
										</label>
									</div>
									<div id="warning_status" class="col-lg-3 text-danger"></div>
								</div>
							</div>
							<input type="hidden" id="status-lama" name="status_lama">
							<input type="hidden" id="id-tata-cara" name="id">
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
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->


<script type="text/javascript" src="<?php echo base_url().'asset/summernote/summernote-bs4.js';?>"></script>
<script type="text/javascript" src="<?= base_url('asset/toastr-2.1.4/toastr-2.1.3.min.js')?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('.summernote').summernote({
	        height: "300px",
	        callbacks: {
	            onImageUpload: function(image) {
	                uploadImage(image[0], $(this).attr('id'));
	            },
	            onMediaDelete : function(target) {
	                deleteImage(target[0].src);
	            }
	        }
	    });

	    $(document).on('click', '.ubah', function(){
	    	var id = $(this).data('id');

	    	$.ajax({
	    		url: "<?php echo site_url('sikesper/ketentuan/reimbursement/detail/'); ?>"+id,
	    		success: function(data) {
	    			$('#no-urut').val(data.result.no_urut);
	    			$('#judul-reimburse').val(data.result.judul);
	    			$('#tata-cara').summernote('code', data.result.tata_cara);
	    			$('#status-lama').val(data.result.status);
	    			$('#id-tata-cara').val(data.result.id);
	    			
	    			$('#'+data.result.status).prop('checked', true);
	    		}
	    	});
	    });

	    $(document).on('click', '.tata-cara-reimburse', function(){
	    	var id = $(this).data('id');

	    	$.ajax({
	    		url: "<?php echo site_url('sikesper/ketentuan/reimbursement/detail/'); ?>"+id,
	    		success: function(data) {
	    			$('.content-reimburse').html(data.result.tata_cara);
	    			$('#judul-reimbursement').html('<strong>'+data.result.judul+'</strong>');
	    		}
	    	});
	    });
	});

    function uploadImage(image, id) {
        toastr.warning('Please wait', "Inserting image...", {
            "positionClass": "toast-top-center",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "0",
            "extendedTImeout": "1000",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        });
        var data = new FormData();
        data.append("image", image);
        $.ajax({
            url: "<?php echo site_url('sikesper/ketentuan/reimbursement/upload_image'); ?>",
            cache: false,
            contentType: false,
            processData: false,
            data: data,
            type: "POST",
            success: function(url) {
                $('#'+id).summernote("insertImage", url);
                toastr.clear();
                toastr.success('Done', "", {
                    "positionClass": "toast-top-center",
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "1000",
                    "extendedTImeout": "1000",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                });
            },
            error: function(data) {
                console.log(data);
                toastr.clear();
                toastr.danger('Failed to insert', "", {
                    "positionClass": "toast-top-center",
                    "showDuration": "300",
                    "hideDuration": "1000",
                    "timeOut": "1000",
                    "extendedTImeout": "1000",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                });
            }
        });
    }

    function deleteImage(src) {
        $.ajax({
            data: {src : src},
            type: "POST",
            url: "<?php echo site_url('sikesper/ketentuan/reimbursement/delete_image'); ?>",
            cache: false,
            success: function(response) {
                console.log(response);
            }
        });
    }
</script>
