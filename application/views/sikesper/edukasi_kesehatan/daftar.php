
<style type="text/css">
	.card {
		border: 1px solid #ccc !important;
		padding: 2%;

		margin-bottom: 5%;
	}

	@media only screen and (max-width: 720px) {

	}

	.img-label {
		display: flex !important;
	}

	.edukasi-card h2 {
		text-align: left;
	}

	.image-thumbnail {
		max-width: 50%;
		
		padding: 0%;
		margin: 2%;
	}

	.image-thumbnail img {
		max-width: 100% !important;
		max-height: 300px !important;
	}

	.text-card {
		margin: 2%;
		max-width: 50%;
		
		text-align: justify;
		font-size: 12px;
	}

	.btn-read {
		max-width: 100%;
		margin: 3%;
		text-align: right;
	}

	.text-thumbnail {
		max-height: 100px;
		overflow: hidden;
	}

	.easyPaginateNav {
		text-align: center;
	}

	.easyPaginateNav a {
		padding:5px;
		margin: 2px;
	}

	.easyPaginateNav a.current {
		font-weight:bold;text-decoration:underline;
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
		<?php } 

			if(@$this->akses["lihat"]) {
				if($this->session->userdata('grup') != '5'){ 
		?>
		<div class="row">
			<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_daftar_reimburse">
				<thead>
					<tr>
						<th class='text-center'>#</th>
						<th class='text-center'>No Urut</th>
						<th class='text-center'>Judul</th>
						<?php
							if(@$akses["ubah"] or @$akses["lihat log"]) {
								echo "<th class='text-center'>Aksi</th>";
							}
						?>
					</tr>
				</thead>
				<tbody></tbody>
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
		<div class="row" id="page-article">
			<?php 
				$start = 1;
				foreach($feed as $val){ 
			?>
			<div class="col-md-12 pagination-article">
				<div class="card">
					<div class="card-body">
							<div class="img-label">
								<div class="image-thumbnail">
									<img src="<?= base_url('uploads/images/sikesper/agenda/default.jpg') ?>">
								</div>
								<div class="text-card">
									<h4><strong><?= $val['title'] ?></strong></h4>
									<br/>

									<div class="text-thumbnail">
										<?= $val['description']; ?>
									</div>

									<div class="btn-read">
										<a target="_blank" href="<?= $val['link'] ?>" class="btn btn-primary btn-md btn-read-more">Baca Selengkapnya</a>
									</div>
								</div>
							</div>
					</div>
				</div>	
			</div>
			<?php } ?>
		</div>		
		<?php
			}
		}if(@$akses["ubah"]) { ?>

		<?php } ?>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script src="<?= base_url('asset/js/agenda/easy.paginate.js') ?>"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#page-article').easyPaginate({
	        paginateElement: '.pagination-article',
	        elementsPerPage: 4,
	        effect: 'climb'
	    });
	});
</script>