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

				<?php if($this->akses["lihat"]){ 
					$this->load->view('sikesper/detail_donor_darah', $send);
				} ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->