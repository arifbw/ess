<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css')?>" />
<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />

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
				<label>Range Tanggal</label>
				<input class="form-control" id='date_range' name='dates' onchange="table_serverside();" style="width: 200px;">
			</div>					
			<div class="col-lg-3">
				<label>Karyawan</label>
				<select class="form-control select2" name="karyawan" id="karyawan" onchange="table_serverside();" style="width: 100%;">
					<option value="-">Semua Karyawan</option>
					<?php
					foreach ($mst_karyawan as $key) {
						$selected = ($key->no_pokok==$this->session->userdata('no_pokok') ? 'selected':'');
						echo "<option value='{$key->no_pokok}' {$selected}>{$key->no_pokok} - {$key->nama}</option>";
					}
					?>
				</select>
			</div>					
		</div>
		<br>
		<div class="row">
			<div class="col-lg-12">
				<table width="100%" class="table table-striped table-bordered table-hover" id="log_akses_table">
					<thead>
						<tr>
							<th class="text-center no-sort" style="width: 10%">NP</th>
							<th class="text-center" style="width: 15%">Jenis Izin</th>
							<th class="text-center" style="width: 10%">Tanggal</th>
							<th class="text-center" style="width: 10%">Status Approval1</th>
							<th class="text-center" style="width: 10%">Status Approval2</th>
							<th class="text-center" style="width: 15%">Pos</th>
							<th class="text-center" style="width: 20%">Approval Pos</th>
							<th class="text-center no-sort" style="width: 10%">Aksi</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
		<?php
		$this->load->view('monitoring_karyawan/modal_detail_req_perizinan');
		}
		?>
	</div>
</div>

<script src="<?= base_url('asset/select2/select2.min.js')?>"></script>
<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js')?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js')?>"></script>
<script src="<?= base_url('asset/sweetalert2/sweetalert2.js')?>"></script>

<script src="<?= base_url('asset/js/monitoring_karyawan/home.js')?>"></script>
<script src="<?= base_url('asset/js/monitoring_karyawan/detail.js')?>"></script>
<script type="text/javascript">
	$('.select2').select2();
	var BASE_URL = "<?= base_url()?>";
	var mst_perizinan = <?= json_encode($mst_perizinan)?>;
	var mst_pos = <?= json_encode($mst_pos)?>;
</script>