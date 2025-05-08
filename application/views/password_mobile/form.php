<style>
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

		<?php if(!empty($this->session->flashdata('success'))) { ?>
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $this->session->flashdata('success');?>
		</div>
		<?php } else if(!empty($this->session->flashdata('failed'))) { ?>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $this->session->flashdata('failed');?>
		</div>
		<?php } if(@$akses["tambah"]) { ?>
		<div class="row">
			<div class="col-lg-12">
				<form role="form" action="<?= site_url('password_mobile/simpan'); ?>" id="formulir_tambah" method="post" enctype="multipart/form-data">
					<input type="hidden" name="np" value="<?= $_SESSION['no_pokok']?>" required>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>Username / NP</label>
							</div>
							<div class="col-lg-7">
								<input class="form-control" value="<?= $_SESSION['no_pokok']?>" disabled>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>Password</label>
							</div>
							<div class="col-lg-7">
								<input type="password" class="form-control" name="password" placeholder="Masukkan Password" autofocus required>
							</div>
							<div id="warning_password" class="col-lg-3 text-danger"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 text-center">
							<button type="submit" class="btn btn-primary">Simpan</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
			}
		?>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script type="text/javascript">
	$(document).ready(function() {
		$('input').keypress(function( e ) {
            if(e.which === 32) 
            return false;
        });
	});
</script>
