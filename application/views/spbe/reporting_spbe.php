<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css')?>" />

<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
		</div>

		<?php if(!empty($this->session->flashdata('success'))){ ?>
		<div class="alert alert-success alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $this->session->flashdata('success');?>
		</div>
		<?php
		}
		
		if(!empty($this->session->flashdata('warning'))){ ?>
		<div class="alert alert-danger alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<?php echo $this->session->flashdata('warning');?>
		</div>
		<?php
		}
			/*if($akses["lihat log"]){
				echo "<div class='row text-right'>";
					echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
					echo "<br><br>";
				echo "</div>";
			}*/
		if(@$akses["export"]){
		?>
		<div class="row">
			<div class="col-lg-12">
				<button type="button" class="btn btn-success pull-right" onclick="export_excel();"><i class="fa fa-file-excel-o"></i> Export Excel</button>
			</div>
		</div>
		<?php
		}
		if($this->akses["lihat"]){
		?>
		<div class="row">
			<div class="col-lg-3">
				<label>Filter Tanggal Keluar</label>
				<input class="form-control" id='date_range' name='dates' onchange="table_serverside();" style="width: 200px;">
			</div>					
		</div>
		<br>
		<div class="row">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="reporting_spbe">
					<thead>
						<tr>
							<th class="text-center no-sort" style="width: 5%">#</th>
							<th class="text-center" style="width: 10%">Nomor</th>
							<th class="text-center" style="width: 10%">Pemohon</th>
							<th class="text-center" style="width: 10%">Tanggal Diajukan</th>
							<th class="text-center" style="width: 10%">Tanggal Keluar</th>
							<th class="text-center" style="width: 15%">Status</th>
							<th class="text-center" style="width: 10%">Barang Kembali</th>
							<th class="text-center" style="width: 15%">Posisi Barang</th>
							<th class="text-center no-sort" style="width: 5%">Aksi</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<?php
		$this->load->view('spbe/modal_detail_spbe');
		}
		?>
	</div>
</div>

<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js')?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js')?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js')?>"></script>

<script src="<?= base_url('asset/js/spbe/reporting_spbe/index.js')?>"></script>
<script src="<?= base_url('asset/js/spbe/detail.js')?>"></script>
<script type="text/javascript">
	var BASE_URL = "<?= base_url()?>";
</script>