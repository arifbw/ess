<link href="<?= base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<link href="<?= base_url('asset/bootstrap-datetimepicker-master/build/css/bootstrap-datetimepicker.min.css')?>" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css')?>" />

<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo 'Tambah '. $judul;?></h1>
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
		?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel">
					<a href="<?= base_url('spbe/permohonan_spbe')?>" class="btn btn-default">Back</a>
				</div>
			</div>
		</div>
		<?php
		if($this->akses["tambah"]){
		?>
		<div class="row alert alert-danger">
			<div class="col-lg-12">
				<form role="form" action="#" id="formulir_tambah" method="post" onsubmit="return false;">
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>NP Pemohon</label>
							</div>
							<div class="col-lg-6">
								<select class="form-control input-detail select2 np_karyawan" name="np_karyawan" style="width: 100%;" required>
									<?php foreach($array_daftar_karyawan as $row){?>
									<option value="<?= $row->no_pokok?>"><?= "{$row->no_pokok} - {$row->nama}"?></option>
									<?php } ?>
								</select>

								<input type="hidden" class="form-control input-detail nama" name="nama">
								<input type="hidden" class="form-control input-detail nama_jabatan" name="nama_jabatan">
								<input type="hidden" class="form-control input-detail kode_unit" name="kode_unit">
								<input type="hidden" class="form-control input-detail nama_unit" name="nama_unit">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Milik</label>
							</div>
							<div class="col-lg-6">
								<input type="text" class="form-control input-detail milik" name="milik" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Maksud</label>
							</div>
							<div class="col-lg-6">
								<input type="text" class="form-control input-detail maksud" name="maksud" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Dikirim ke</label>
							</div>
							<div class="col-lg-6">
								<input type="text" class="form-control input-detail dikirim_ke" name="dikirim_ke" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Keluar Tanggal</label>
							</div>
							<div class="col-lg-6">
								<input type="date" class="form-control input-detail keluar_tanggal" name="keluar_tanggal" required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Pos Keluar yang Dilewati</label>
							</div>
							<div class="col-lg-6">
								<select class="form-control select2 multi_select pos_keluar" name="pos_keluar[]" style="width: 100%;" multiple required>
									<?php foreach($pos as $row){?>
									<option value="<?= $row->id?>"><?= $row->nama?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Atasan Kasek (Opsional)</label>
							</div>
							<div class="col-lg-6">
								<select class="form-control input-detail select2 approval_kasek_np" name="approval_kasek_np" style="width: 100%;"></select>
								<input type="hidden" class="form-control input-detail approval_kasek_nama" name="approval_kasek_nama">
								<input type="hidden" class="form-control input-detail approval_kasek_jabatan" name="approval_kasek_jabatan">
								<input type="hidden" class="form-control input-detail approval_kasek_kode_unit" name="approval_kasek_kode_unit">
								<input type="hidden" class="form-control input-detail approval_kasek_nama_unit" name="approval_kasek_nama_unit">
								<input type="hidden" class="form-control input-detail approval_kasek_nama_unit_singkat" name="approval_kasek_nama_unit_singkat">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Atasan Minimal Kadep</label>
							</div>
							<div class="col-lg-6">
								<select class="form-control input-detail select2 approval_atasan_np" name="approval_atasan_np" style="width: 100%;" required></select>
								<input type="hidden" class="form-control input-detail approval_atasan_nama" name="approval_atasan_nama">
								<input type="hidden" class="form-control input-detail approval_atasan_jabatan" name="approval_atasan_jabatan">
								<input type="hidden" class="form-control input-detail approval_atasan_kode_unit" name="approval_atasan_kode_unit">
								<input type="hidden" class="form-control input-detail approval_atasan_nama_unit" name="approval_atasan_nama_unit">
								<input type="hidden" class="form-control input-detail approval_atasan_nama_unit_singkat" name="approval_atasan_nama_unit_singkat">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Pengawas / Pengawal / Penyegel</label>
							</div>
							<div class="col-lg-2">
								<div class="radio">
									<label>
										<input class="input-detail" type="radio" name="pilih_pengawal" value="1" checked>Sama dengan pemohon
									</label>
								</div>
							</div>
							<div class="col-lg-2">
								<div class="radio">
									<label>
										<input class="input-detail" type="radio" name="pilih_pengawal" value="2">Karyawan Peruri
									</label>
								</div>
							</div>
							<div class="col-lg-2">
								<div class="radio">
									<label>
										<input class="input-detail" type="radio" name="pilih_pengawal" value="3">Orang Lain
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="inputan-penyegel" style="display: none;">
						<div class="form-group">
							<div class="col-lg-3">
								<label>&nbsp;</label>
							</div>
							<div class="col-lg-6">
								<select class="form-control input-detail select2 konfirmasi_pengguna_np" name="konfirmasi_pengguna_np" style="width: 100%;"></select>
								<input type="hidden" class="form-control input-detail konfirmasi_pengguna_nama" name="konfirmasi_pengguna_nama">
								<input type="hidden" class="form-control input-detail konfirmasi_pengguna_jabatan" name="konfirmasi_pengguna_jabatan">
							</div>
						</div>
					</div>
					<div class="row" id="inputan-nama-pembawa" style="display: none;">
						<div class="form-group">
							<div class="col-lg-3">
								<label>&nbsp;</label>
							</div>
							<div class="col-lg-6">
								<input class="form-control input-detail" name="nama_pembawa_barang" placeholder="Nama pembawa barang">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Barang Kembali ke Perusahaan?</label>
							</div>
							<div class="col-lg-2">
								<div class="radio">
									<label>
										<input class="input-detail" type="radio" name="barang_kembali" value="1" checked>Ya
									</label>
								</div>
							</div>
							<div class="col-lg-2">
								<div class="radio">
									<label>
										<input class="input-detail" type="radio" name="barang_kembali" value="2">Tidak
									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="row" id="inputan-pos-masuk">
						<div class="form-group">
							<div class="col-lg-3">
								<label>Pos Masuk yang Dilewati</label>
							</div>
							<div class="col-lg-6">
								<select class="form-control select2 multi_select pos_masuk" name="pos_masuk[]" style="width: 100%;" multiple>
									<?php foreach($pos as $row){?>
									<option value="<?= $row->id?>"><?= $row->nama?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<hr>

					<div class="col-12" id="all-input">
						<div class="form-group row col-12 group-input">
							<div class="form-group col-lg-3 col-md-6 col-sm-12">
								<label for="">Jumlah</label>
								<input type="text" class="form-control jumlah">
							</div>

							<div class="form-group col-lg-3 col-md-6 col-sm-12">
								<label for="">Nama Barang</label>
								<input type="text" class="form-control nama_barang">
							</div>

							<div class="form-group col-lg-3 col-md-6 col-sm-12">
								<label for="">Keterangan</label>
								<input type="text" class="form-control keterangan">
							</div>

							<div class="form-group col-lg-3 col-md-6 col-sm-12">
								<label for="">&nbsp;</label>
								<div class="" style="border: none; padding: 0px;">
									<button type="button" class="btn btn-primary btn-add"><i class="fa fa-plus"></i></button>
									<button type="button" class="btn btn-danger btn-rm"><i class="fa fa-trash"></i></button>
								</div>
							</div>
							<hr>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-sm-12">
							<label for="">Lampiran File</label>
							<div>
								<button type="button" class="btn btn-primary btn-add-file">Tambah File</button>
							</div>
						</div>
						<div class="col-lg-10 col-md-6 col-sm-12 form-input">
							<input type="hidden" id="jumlah_files" value="">
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-lg-3"></div>
						<div class="col-lg-6">
							<button type="button" class="btn btn-default" id="btn-cancel">Batal</button>
							<button type="submit" class="btn btn-primary">Submit</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<?php
		}
		?>
	</div>
</div>

<script src="<?= base_url('asset/select2')?>/select2.min.js"></script>
<script src="<?= base_url('asset/js/moment.min.js')?>"></script>
<script src="<?= base_url('asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js')?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js')?>"></script>
<script src="<?= base_url('asset/lodash.js/4.17.21/lodash.min.js')?>"></script>
<script src="<?= base_url('asset/js/sweetalert2@11.js')?>"></script>

<script src="<?= base_url('asset/js/spbe/permohonan_spbe/tambah.js')?>"></script>
<script type="text/javascript">
	var BASE_URL = "<?= base_url()?>";
	var table;
	var daftar_pos_temp = <?= json_encode($pos)?>;
	var daftar_karyawan_temp = <?= json_encode($array_daftar_karyawan)?>;
	var daftar_atasan_temp = [];
	var daftar_kasek_temp = [];

	let upload_file = 0;
	$('.btn-add-file').click(function(e){
		upload_file += 1;
		let html = `<input type="file" name="upload_file[]" id="upload_file" class="form-control" style="margin-bottom:10px;margin-right:10px;">`;
		$(".form-input").append(html);
		$("#jumlah_files").val(upload_file);
	});
</script>