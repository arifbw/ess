<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />

<!-- Page Content -->
<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul; ?></h1>
			</div>
			<!-- /.col-lg-12 -->
		</div>
		<!-- /.row -->

		<!-- <div class="alert alert-info alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>					
			Karyawan yang mendapatkan uang lembur adalah :
			<br>1) Karyawan dengan Grade Pangkat 7 - 12 (termasuk PKWT) pada hari kerja atau hari libur
			<br>2) Karyawan dengan Grade Pangkat 13 pada hari libur
			<br>3) Karyawan dengan Grade Pangkat 14 dan Grup Jabatan KAUN pada hari libur
		</div> -->


		<div class="alert alert-info alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			Pengajuan jam lembur <= (lebih kecil / sama dengan) dari 4 jam/transaksi, maka Approver setingkat Kepala Seksi. <br>Pengajuan jam lembur > (Lebih besar) dari 4 jam/transaksi, maka Approver setingkat Kepala Departemen.
				<br>Pengajuan jam lembur akumulasi dalam 1 bulan > (Lebih besar) dari 72 jam/bulan, maka Approver setingkat Kepala Divisi.
		</div>








		<?php //if ($this->session->flashdata('success') != null || $this->session->flashdata('success')) { 
		?>
		<?php if (@$this->session->flashdata('success')) { ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

				<?php echo $this->session->flashdata('success'); ?>
			</div>
			<?php //} if ($this->session->flashdata('warning') != null || $this->session->flashdata('warning') != '') { 
			?>
		<?php }
		if (@$this->session->flashdata('warning')) { ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>

				<?php echo $this->session->flashdata('warning'); ?>
			</div>
		<?php }
		if ($akses["lihat log"]) { ?>
			<div class='row text-right'>
				<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>
				<br><br>
			</div>
		<?php }
		if ($akses["tambah"]) { ?>
			<div class="row">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a href="<?php echo base_url('lembur/pengajuan_lembur/input_pengajuan_lembur') ?>">Tambah <?php echo $judul; ?></a>
						</h4>
					</div>
				</div>
			</div>
		<?php }
		if ($this->akses["lihat"]) { ?>
			<div class="row">
				<div class="form-group">
					<div class="pull-left">
						<label>Filter Bulan</label>
						<div class="row">
							<div class="col-md-12">
								<select class='form-control' id='filter'>
									<?php if (!in_array($bulan, array_column($month_list, 'bln'))) { ?>
										<option value='<?= $bulan ?>' selected><?= id_to_bulan(substr($bulan, -2)) . ' ' . substr($bulan, 0, 4) ?></option>
									<?php } ?>
									<?php foreach ($month_list as $ls) { ?>
										<option value='<?= $ls['bln'] ?>' <?= ($bulan == $ls['bln']) ? 'selected' : ''; ?>><?= id_to_bulan(substr($ls['bln'], -2)) . ' ' . substr($ls['bln'], 0, 4) ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>

					<div class="pull-right">
						<div class="form-group">
							<label>Cetak Daftar Lembur</label>
							<div class="row">
								<div class="col-md-12">
									<button onClick="otoritas()" class="btn btn-success btn-xs" type="button"><i class="fa fa-print"></i> Per NP </button>
									<button onClick="otoritas_unit()" class="btn btn-success btn-xs" type="button"><i class="fa fa-print"></i> Per Unit</button>
								</div>

							</div>

						</div>
						<!--begin: Modal Inactive -->
						<div class="modal fade" id="show_otoritas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="label_modal_ubah">Pilih Otoritas</h4>
									</div>
									<form action="<?php echo base_url('lembur/pengajuan_lembur/export') ?>" method="POST" target="_blank">
										<div class="modal-body">
											<label>Pilih Tanggal</label>
											<div class="row">
												<div class="col-md-12">
													<input type="date" class="form-control" name="filter_tgl" required>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<select multiple="multiple" class="form-control select2" name='np_karyawan[]' id="multi_select" style="width: 100%;" required>
														<?php foreach ($list_np as $val) { ?>
															<option value='<?php echo $val['no_pokok'] ?>'><?php echo $val['no_pokok'] . " " . $val['nama'] ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="modal-footer">
												<button type="submit" class="btn btn-success btn-xs">Cetak</button>
												<button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Batal</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
						<!--end: Modal Inactive -->
						<!--begin: Modal Inactive -->
						<div class="modal fade" id="show_otoritas_unit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="label_modal_ubah">Pilih Otoritas</h4>
									</div>
									<form action="<?php echo base_url('lembur/pengajuan_lembur/export_unit') ?>" method="POST" target="_blank">
										<div class="modal-body">
											<label>Pilih Tanggal</label>
											<div class="row">
												<div class="col-md-12">
													<input type="date" class="form-control" name="filter_tgl" required>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<select multiple="multiple" class="form-control select2" id="multi_select_unit" name='kode_unit[]' style="width: 100%;" required>
														<?php foreach ($array_daftar_unit->result_array() as $val) { ?>
															<option value='<?php echo $val['kode_unit'] ?>'><?php echo $val['kode_unit'] . " " . $val['nama_unit'] ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<div class="modal-footer">
												<button type="submit" class="btn btn-success btn-xs">Cetak</button>
												<button type="button" class="btn btn-default btn-xs" data-dismiss="modal">Batal</button>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>

						<!--end: Modal Inactive -->


					</div>
				</div>
			</div>
			<br>
			<div class="row">
				<table width="100%" class="table table-striped table-bordered table-hover" id="tabel_ess_lembur_sdm">
					<thead>
						<tr>
							<th class='text-center'>No</th>
							<th class='text-center'>No Pokok</th>
							<th class='text-center'>Nama Pegawai</th>
							<th class='text-center'>Tertanggal</th>
							<th class='text-center'>Input Mulai</th>
							<th class='text-center'>Input Selesai</th>
							<th class='text-center'>Lembur Diakui</th>
							<th class='text-center'>Jenis Alasan</th>
							<th class='text-center'>Keterangan</th>
							<th class='text-center'>Status</th>
							<?php
							if ($akses["ubah"] or $akses["lihat log"]) {
								echo "<th class='text-center'>Aksi</th>";
							}
							?>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<!-- /.table-responsive -->
			</div>
		<?php }
		if ($akses["ubah"]) { ?>
			<!-- Modal -->
			<div class="modal fade" id="modal_ubah" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<form role="form" action="" id="formulir_ubah" method="post">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title" id="label_modal_ubah">Ubah <?php echo $judul; ?></h4>
							</div>
							<div class="modal-body">
								<input type="hidden" name="aksi" value="ubah" />
								<div id='isian_ubah_lembur'></div>
							</div>
							<div class="modal-footer">
								<button type="submit" class="btn btn-primary" onclick="return cek_simpan_ubah()">Simpan</button>
								<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
							</div>
						</form>
					</div>
					<!-- /.modal-content -->
				</div>
				<!-- /.modal-dialog -->
			</div>
			<!-- /.modal -->

			<!--begin: Modal Inactive -->
			<div class="modal fade" id="modal-inactive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title text-danger" id="title-inactive">
								<b>Hapus <?= $judul ?></b>
							</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>

						<div class="modal-body">
							<h6 id="message-inactive"></h6>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
							<a href="" id="inactive-action" class="btn btn-primary">Ya, Hapus</a>
						</div>
					</div>
				</div>
			</div>
			<!--end: Modal Inactive -->
		<?php } ?>

		<?php if (@$akses["persetujuan"] || @$akses["lihat"]) { ?>
			<!-- Modal -->
			<div class="modal fade" id="show_modal_approve" tabindex="-1" role="dialog" aria-labelledby="label_modal_status" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
							<h4 class="modal-title" id="label_modal_batal"><?= $judul ?></h4>
						</div>
						<div class="modal-body">
							<div class="get-approve"></div>
						</div>
					</div>
				</div>
			</div>
			<!-- /.modal -->
		<?php } ?>
	</div>
	<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

<script src="<?php echo base_url('asset/select2') ?>/select2.min.js"></script>
<script type="text/javascript">
	var lembur_table;
	$(document).ready(function() {
		table_serverside();

		/*$(document).on('click','#modal_approve',function(e){
		e.preventDefault();
		$("#show_modal_approve").modal('show');
		$.post('<?php echo site_url("osdm/persetujuan_lembur_sdm/view_approve") ?>',
			{id_pengajuan:$(this).attr('data-id-pengajuan')},
			function(e){
				$(".get-approve").html(e);
			}
		);
	});*/

		$(document).on('click', '#modal_approve', function(e) {
			e.preventDefault();
			$("#show_modal_approve").modal('show');
			$.post('<?php echo site_url("lembur/pengajuan_lembur/view_approve") ?>', {
					id_pengajuan: $(this).attr('data-id-pengajuan'),
					akses: $(this).attr('data-akses')
				},
				function(e) {
					$(".get-approve").html(e);
				}
			);
		});

		$(document).on('click', '#cetak', function(e) {
			e.preventDefault();
			$("#show_modal_approve").modal('show');
			$.post('<?php echo site_url("osdm/persetujuan_lembur_sdm/view_approve") ?>', {
					id_pengajuan: $(this).attr('data-id-pengajuan')
				},
				function(e) {
					$(".get-approve").html(e);
				}
			);
		});

		$(document).on('change', '#filter', function(e) {
			e.preventDefault();
			refresh_table_serverside();
		});

		$('#multi_select').select2({
			closeOnSelect: false
			//minimumResultsForSearch: 20
		});
		$('#multi_select_unit').select2({
			closeOnSelect: false
			//minimumResultsForSearch: 20
		});
		// $.LoadingOverlaySetup({
		//           image: "https://loading.io/spinners/balls/index.circle-slack-loading-icon.gif"
		//       });

		// $('#filter').on( 'change', function () {
		//     lembur_table
		//         .columns( 1 )
		//         .search( this.value )
		//         .draw();
		// } );

	});

	function refresh_table_serverside() {
		$('#tabel_ess_lembur_sdm').DataTable().destroy();
		table_serverside();
	}

	function filter_pengajuan(isi) {
		console.log(isi);
		//$('#tabel_ess_lembur_sdm').LoadingOverlay("show");
		lembur_table.column(6).search(isi).draw();
	}

	function table_serverside() {

		lembur_table = $('#tabel_ess_lembur_sdm').DataTable({
			"iDisplayLength": 10,
			"language": {
				"url": "<?php echo base_url('asset/datatables/Indonesian.json'); ?>",
				"sEmptyTable": "Tidak ada data di database",
				"processing": "Sedang memuat data pengajuan lembur", //Feature control the processing indicator.
				"emptyTable": "Tidak ada data di database"
			},

			"stateSave": true,
			"responsive": true,
			"processing": true, //Feature control the processing indicator.
			"serverSide": true, //Feature control DataTables' server-side processing mode.
			"order": [], //Initial no order.

			// Load data for the table's content from an Ajax source
			"ajax": {
				"url": "<?php echo site_url("lembur/pengajuan_lembur/tabel_ess_lembur/") ?>",
				"data": {
					bln: $('#filter').val()
				},
				"type": "POST"
			},

			//Set column definition initialisation properties.
			"columnDefs": [{
				"targets": 'no-sort', //first column / numbering column
				"orderable": false, //set not orderable
			}],
			drawCallback: function() {
				//$('#tabel_ess_lembur_sdm').LoadingOverlay("hide");
			}
		});
	};

	function otoritas() {
		$("#show_otoritas").modal('show');
	}

	function otoritas_unit() {
		$("#show_otoritas_unit").modal('show');
	}

	function hapus(id) {
		//console.log(id);
		var url = "<?php echo site_url("lembur/pengajuan_lembur/hapus") ?>/";
		$('#inactive-action').prop('href', url + id);
		$('#message-inactive').text('Apakah anda yakin ingin menghapus pengajuan lembur ini ?');
		$('#modal-inactive').modal('show');
	}

	function getWaktu(update = '') {
		var no_pokok = $('.no_pokok').val();
		var tgl_mulai = $('.tgl_mulai').val();
		var jam_mulai = $('.jam_mulai').val();
		var tgl_selesai = $('.tgl_selesai').val();
		var jam_selesai = $('.jam_selesai').val();
		$('#set_jam').text('');

		if (no_pokok != "" && tgl_mulai != "" && jam_mulai != "" && tgl_selesai != "" && jam_selesai != "") {
			$.ajax({
				type: "POST",
				dataType: "html",
				url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getWaktu'); ?>",
				data: {
					vno_pokok: no_pokok,
					jam_mulai: jam_mulai,
					jam_selesai: jam_selesai,
					tgl_mulai: tgl_mulai,
					tgl_selesai: tgl_selesai
				},
				success: function(msg) {
					if (msg == '') {
						alert('Silakan isi No. Pokok dan Waktu Dengan Benar.');
					} else {
						$('#set_jam').text(msg);
						getPilihanAtasanLembur(update);
					}
				}
			});
		}
	}

	function getPilihanAtasanLembur(update = '') {
		//alert("asd");
		var no_pokok = $('.no_pokok').val();
		var tgl_mulai = $('.tgl_mulai').val();
		var jam_mulai = $('.jam_mulai').val();
		var tgl_selesai = $('.tgl_selesai').val();
		var jam_selesai = $('.jam_selesai').val();

		//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
		var periode = $('.tgl_dws').val();

		if (update != 'ubah')
			$("#np_approver").empty();

		$.ajax({
			type: "POST",
			dataType: "html",
			url: "<?php echo base_url('lembur/pengajuan_lembur/ajax_getPilihanAtasanLembur'); ?>",

			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			//10-07-2021 - Wina menambah parameter perhitungan jam lembur
			data: {
				vnp_karyawan: no_pokok + "#" + periode,
				vno_pokok: no_pokok,
				jam_mulai: jam_mulai,
				jam_selesai: jam_selesai,
				tgl_mulai: tgl_mulai,
				tgl_selesai: tgl_selesai
			},
			success: function(msg) {
				if (msg != '') {
					//console.log(msg);
					var get_data = JSON.parse(msg);
					var arr_atasan = get_data.atasan;
					var kode_unit = get_data.kode_unit;
					for (var i = 0; i < arr_atasan.length; i++) {
						$("#np_approver").append($("<option></option>").attr("value", arr_atasan[i]["no_pokok"]).text(arr_atasan[i]["no_pokok"] + " - " + arr_atasan[i]["nama"]));
						//10-07-2021 - Wina mengganti setting selected approval
						if (update != 'ubah') {
							get_unit = arr_atasan[i]["kode_unit"];
							if (get_unit.substr(0, kode_unit.length) == kode_unit) {
								$("#np_approver").val(arr_atasan[i]["no_pokok"]).trigger("change");
							}
						}
					}
					if (update == 'ubah')
						$("#np_approver").val($('.get_approver').val()).trigger("change");

					$('.select2').select2();
					// getAtasanLembur();

				} else {
					alert('Atasan tidak ditemukan!');
				}
			}
		});
	}
</script>