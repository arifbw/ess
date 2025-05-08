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
		<div class="row">
			<div class="col-md-12">
				<div class="tata-cara-content">
					<h2><strong><?= $val->judul; ?></strong></h2>
					<br>
					<?= $val->tata_cara;?>
				</div>	
			</div>
		</div>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
