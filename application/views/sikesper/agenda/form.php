<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
<link href="<?php echo base_url('asset/bootstrap-datetimepicker-master/build/css/') ?>bootstrap-datetimepicker.min.css" rel="stylesheet" />
<link rel="stylesheet" href="<?= base_url() ?>asset/leaflet/css/leaflet.css">
<link rel="stylesheet" href="<?= base_url() ?>asset/leaflet/css/leaflet-search.css">

<style>
	.search-input {
		font-family: Courier
	}

	.search-input,
	.leaflet-control-search {
		max-width: 400px;
	}
</style>
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

		<?php if (!empty($success)) { ?>
			<div class="alert alert-success alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $success; ?>
			</div>
		<?php }
		if (!empty($warning)) { ?>
			<div class="alert alert-danger alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<?php echo $warning; ?>
			</div>
		<?php }
		if (/* @$akses["tambah"] */ true) { ?>
			<div class="row">
				<div class="col-lg-12">
					<form role="form" action="<?= site_url('sikesper/agenda/simpan_agenda'); ?>" id="formulir_tambah" method="post" enctype="multipart/form-data">
						<input type="hidden" name="aksi" value="<?= @$action ?>" />
						<input type="hidden" name="no" value="<?= @$id ?>">
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Nama *</label>
								</div>
								<div class="col-lg-7">
									<input class="form-control" name="agenda" placeholder="Masukkan Nama" value="<?= @$data_agenda->agenda; ?>" required>
								</div>
								<div id="warning_agenda" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Kategori *</label>
								</div>
								<div class="col-lg-7">
									<select class="kategori-api" name="id_kategori" required>
										<?php if (isset($data_agenda)) { ?>
											<option value="<?= $data_agenda->id_kategori; ?>" selected><?= $data_agenda->nama_kategori; ?></option>
										<?php } ?>
									</select>
								</div>
								<div id="warning_kategori" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Deskripsi *</label>
								</div>
								<div class="col-lg-7">
									<textarea class="form-control" name="deskripsi" required><?= @$data_agenda->deskripsi; ?></textarea>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Waktu *</label>
								</div>
								<div class="col-lg-7">
									<div class='input-group date' id='datetimepicker1'>
										<input type='text' class="form-control" name="tanggal" value="<?= !empty($data_agenda) ? date('d/m/Y', strtotime($data_agenda->tanggal)) : ''; ?>" required />
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-calendar"></span>
										</span>
									</div>
								</div>
								<div id="warning_tanggal" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Waktu Mulai *</label>
								</div>
								<div class="col-lg-7">
									<div class='input-group date' id='waktu-mulai'>
										<input type='text' class="form-control" name="waktu_mulai" value="<?= @$data_agenda->waktu_mulai; ?>" required />
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-time"></span>
										</span>
									</div>
								</div>
								<div id="warning_mulai" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Waktu Selesai *</label>
								</div>
								<div class="col-lg-7">
									<div class='input-group date' id='waktu-selesai'>
										<input type='text' class="form-control" name="waktu_selesai" value="<?= @$data_agenda->waktu_selesai; ?>" required />
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-time"></span>
										</span>
									</div>
								</div>
								<div id="warning_selesai" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Kuota *</label>
								</div>
								<div class="col-lg-7">
									<input type="number" class="form-control" name="kuota" placeholder="Masukkan Kuota" value="<?= @$data_agenda->kuota; ?>" min=1 required />
								</div>
								<div id="warning_kuota" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Nama Lokasi *</label>
								</div>
								<div class="col-lg-7">
									<select class="select2" name="lokasi" style="width: 100%;" required>
										<?php
										foreach ($daftar_lokasi as $value) {
										?>
											<option value="<?= $value->id; ?>" <?= @$data_agenda->lokasi == $value->id ? 'selected' : ''; ?>><?= $value->nama; ?></option>
										<?php
										}
										?>
									</select>
								</div>
								<div id="warning_nama_lokasi" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Provinsi *</label>
								</div>
								<div class="col-lg-7">
									<select class="provinsi-api" name="id_provinsi" required>
										<?php if (isset($data_agenda)) { ?>
											<option value="<?= $data_agenda->provinsi; ?>" selected><?= $data_agenda->nama_provinsi; ?></option>
										<?php } ?>
									</select>
								</div>
								<div id="warning_provinsi" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Kabupaten *</label>
								</div>
								<div class="col-lg-7">
									<select class="kabupaten-api" name="id_kabupaten" required>
										<?php if (isset($data_agenda)) { ?>
											<option value="<?= $data_agenda->kabupaten; ?>" selected><?= $data_agenda->nama_kabupaten; ?></option>
										<?php } ?>
									</select>
								</div>
								<div id="warning_kabupaten" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Alamat *</label>
								</div>
								<div class="col-lg-7">
									<textarea class="form-control" name="alamat" required><?= @$data_agenda->alamat ?></textarea>
								</div>
								<div id="warning_alamat" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Upload Gambar * <code>(jpg|png|jpeg)</code></label>
								</div>
								<div class="col-lg-7">
									<input type="file" class="form-control" accept=".jpg,.jpeg,.png" name="image" <?= @$data_agenda->image ? '' : 'required' ?>>
									<?php if (!empty($data_agenda)): ?>
									<div class="col-12">
										<img style="max-width:50%;" src="<?= base_url() . "uploads/images/sikesper/agenda/" . @$data_agenda->image; ?>" alt="image">
									</div>
									<?php endif ?>
								</div>
								<div id="warning_image" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Lokasi Maps</label>
								</div>
								<div class="col-lg-7">
									<div style="min-height: 300px;" id="map"></div>
								</div>
								<div id="warning_map" class="col-lg-3 text-danger"></div>
							</div>
							<div class="row" style="margin-top: 2%;">
								<div class="col-lg-2">
									<label>Longitude Latitude *</label>
								</div>
								<div class="col-lg-3">
									<input id="tambah-longitude" class="form-control" type="text" name="longitude" value="<?= @$data_agenda->longitude; ?>" required />
								</div>
								<div class="col-lg-4">
									<input id="tambah-latitude" class="form-control" type="text" name="latitude" value="<?= @$data_agenda->latitude; ?>" required />
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Poin *</label>
								</div>
								<div class="col-lg-7">
									<input required type="number" class="form-control" type="number" name="poin" placeholder="Masukkan Poin" value="<?= @$data_agenda->poin; ?>" min=0>
								</div>
								<div id="warning_poin" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Nama Contact Person *</label>
								</div>
								<div class="col-lg-7">
									<input class="form-control" name="cp_nama" placeholder="Masukkan Nama Contact Person" value="<?= @$data_agenda->cp_nama; ?>" min=1 required>
								</div>
								<div id="warning_cp_nama" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Nomor Contact Person *</label>
								</div>
								<div class="col-lg-7">
									<input class="form-control" name="cp_nomor" placeholder="Masukkan Nomor Contact Person" value="<?= @$data_agenda->cp_nomor; ?>" min=1 required>
								</div>
								<div id="warning_cp_nomor" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>NP Tergabung</label>
								</div>
								<div class="col-lg-7">
									<select id="np_tergabung" name="np_tergabung[]" multiple style="width: max-content;" class="form-control">
										<?php foreach ($ref_karyawan as $item): ?>
											<option value="<?= $item['no_pokok']; ?>" <?= isset($data_agenda->np_tergabung) && in_array($item['no_pokok'], explode(',', $data_agenda->np_tergabung)) ? 'selected' : null ?>><?= $item['no_pokok']; ?> - <?= $item['nama']; ?></option>
										<?php endforeach ?>
									</select>
								</div>
								<div id="warning_np_tergabung" class="col-lg-3 text-danger"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Status *</label>
								</div>
								<div class="col-lg-5">
									<label class='radio-inline'>
										<input required type="radio" name="status" value="1" <?= @$data_agenda->status == 1 ? 'checked' : '' ?>>Aktif
									</label>
									<label class='radio-inline'>
										<input required type="radio" name="status" value="0" <?= isset($data_agenda) ? ($data_agenda->status == 0 ? 'checked' : '') : '' ?>>Non Aktif
									</label>
								</div>
								<div class="col-lg-1">
									<label class='checkbox-inline'>
										<input type='checkbox' name='notifikasi' id='notifikasi' /> Notifikasi
									</label>
								</div>
								<div id="warning_status" class="col-lg-3 text-danger"></div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-lg-2">
									<label>Kegiatan Berkala?</label>
								</div>
								<div class="col-lg-7">
									<input type="checkbox" name="is_berkala" value="1" <?= @$data_agenda->is_berkala == 1 ? 'checked' : '' ?> onclick="show_hide_berkala()">
								</div>
							</div>
						</div>

						<div class="form-group" id="div-tanggal-berkala" style="display: none;">
							<div class="row">
								<div class="col-lg-3" style="margin-top: 50px;">
									<section class="panel">
										<div class="panel panel-default">
											<div class="panel-heading form-inline">
												<div class="form-group">
													<input type="number" class="form-control input-sm" id="tambah_baris" onkeypress="return event.charCode >= 48" min="1" max="5" value="1" style="width:75px;">
												</div>
												<div class="form-group">
													<button type="button" class="btn btn-success btn-sm" id="addNewRowTable" onclick="addNewRow()">Add Date</button>
												</div>
											</div>
										</div>
									</section>
								</div>
							</div>

							<div class="row">
								<div class="col-lg-9">
									<section class="panel">
										<div class="form-horizontal scrollable-form" xml_error_string style="margin-top: -20px">
											<input type="hidden" id="maxIndexTable" />
											<table class="table table-striped table-hover table-bordered" id="editable-sample" width="100%">
												<tbody id="bodyTable"></tbody>
											</table>
										</div>
									</section>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-12 text-center">
								<a href="<?= base_url('sikesper/agenda')?>" class="btn btn-default">Batal</a>
								<button type="submit" class="btn btn-primary">Simpan</button>
							</div>
						</div>
					</form>
				</div>
			<?php
		}
			?>
			</div>
			<!-- /.container-fluid -->
	</div>
	<!-- /#page-wrapper -->

	<script src="<?= base_url() ?>asset/leaflet/js/leaflet.js"></script>
	<script src="<?= base_url() ?>asset/leaflet/js/leaflet-esri.js"></script>
	<script src="<?= base_url() ?>asset/leaflet/js/leaflet-search.js"></script>
	<script src="<?php echo base_url('asset/bootstrap-multiselect') ?>/0.9.13/js/bootstrap-multiselect.js"></script>
	<link href="<?php echo base_url('asset/bootstrap-multiselect') ?>/0.9.13/css/bootstrap-multiselect.css" rel="stylesheet" />

	<script type="text/javascript" src="<?php echo base_url() . 'asset/bootstrap-datetimepicker-master/build/js/moment.min.js'; ?>"></script>
	<script type="text/javascript" src="<?php echo base_url() . 'asset/bootstrap-datetimepicker-master/build/js/bootstrap-datetimepicker.min.js'; ?>"></script>
	<script type="text/javascript">
		function generateOption(el, url, text) {
			var option =
				$(el).select2({
					width: "100%",
					allowClear: true,
					placeholder: text,
					ajax: {
						type: 'POST',
						dataType: 'json',
						url: url,
						delay: 800,
						data: function(params) {
							return {
								search: params.term
							}
						},
						Results: function(data, page) {
							return {
								results: data
							};
						},
					}
				});

			return option;
		}

		$(document).ready(function() {
			$('#formulir_tambah').keydown(function(e) {
				if (e.keyCode == 13) {
					e.preventDefault();
					return false;
				}
			});

			$('.datetimepicker1').datetimepicker({
				format: 'DD/MM/YYYY'
			});

			$('#datetimepicker1').datetimepicker({
				format: 'DD/MM/YYYY'
			});

			$('#waktu-mulai, #waktu-selesai').datetimepicker({
				format: 'H:mm'
			});

			$(document).on('click', '.detail', function() {
				var id = $(this).data('id');

				$.ajax({
					url: "<?php echo site_url('sikesper/ketentuan/info_provider/show/'); ?>" + id,
					type: "GET",
					success: function(data) {
						$('.detail-content').html(data.result);
					}
				});
			});

			generateOption('.kategori-api', '<?= site_url('sikesper/agenda/kategoriAgenda/'); ?>', 'Masukan Kategori');

			$('.kabupaten-api').select2({
				width: "100%",
				placeholder: "Silahkan Pilih Provinsi"
			});

			generateOption('.provinsi-api', '<?= site_url('sikesper/agenda/daftar_provinsi/'); ?>', 'Masukan Provinsi');

			$(document).on('change', '.provinsi-api', function() {
				var provinsi = $(this).val();
				var url = '<?= site_url('sikesper/agenda/daftar_kabupaten/'); ?>' + provinsi;

				generateOption('.kabupaten-api', url, 'Masukan Kabupaten');
			});
		});

		function show_hide_berkala() {
			$('#maxIndexTable').val(1);
			$('#tambah_baris').val(1);
			if ($('input[name="is_berkala"]:checked').length > 0) {
				$('#div-tanggal-berkala').show();
				addNewRow();
			} else {
				var i;
				$('#bodyTable tr').remove();
				$('#div-tanggal-berkala').hide();
			}
		}

		function addNewRow() {
			var lastIndexTable = Number($('#maxIndexTable').val());
			var baris = Number($('#tambah_baris').val());

			var i;
			for (i = 0; i < baris; i++) {
				lastIndexTable = lastIndexTable + 1;
				var newRow = `<tr id="tableRow` + lastIndexTable + `">
                            <td style="width:40%;">Tanggal selanjutnya</td>
                            <td style="width:50%;">
                                <div class='input-group date datetimepicker1'>
				                    <input type='text' class="form-control tanggal_berkala" id="tanggal_berkala` + lastIndexTable + `" name="tanggal_berkala[]">
				                    <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
				                </div>
                            </td>
                            <td><button class="btn btn-danger btn-sm" type="button" onclick="deleteRow('tableRow` + lastIndexTable + `')"><i class="fa fa-trash-o"></i></button></td>
                        </tr>`;

				$('#bodyTable').append(newRow);
				$('.datetimepicker1').datetimepicker({
					format: 'DD/MM/YYYY'
				});
			}
			$('#maxIndexTable').val(lastIndexTable);
		}

		function deleteRow(tag) {
			$('#' + tag).remove();
		}
	</script>

	<script type="text/javascript">
		let map;
		let point_layers = [];
		let polygon_layers = [];
		let markers = [];
		let marker;

		$(document).ready(() => {

			<?php if (empty($data_agenda)) { ?>
				init_map([-6.21462, 106.84513]);
			<?php } else { ?>
				init_map([<?= @$data_agenda->latitude ?>, <?= @$data_agenda->longitude ?>], [<?= @$data_agenda->latitude ?>, <?= @$data_agenda->longitude ?>]);
			<?php } ?>

		});

		function init_map(latlong, value = null) {
			map = L.map('map', {
				attributionControl: false,
				zoomControl: true
			}).setView(latlong, 15);

			basemap = {
				osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					maxZoom: 19,
					// attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
				}).addTo(map),
				google_roadmap: L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
					maxZoom: 20,
					subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
				}),
				google_satellite: L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
					maxZoom: 20,
					subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
				}),
				google_hybrid: L.tileLayer('http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
					maxZoom: 20,
					subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
				}),
				google_terrain: L.tileLayer('http://{s}.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
					maxZoom: 20,
					subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
				}),
				esri_world_imagery: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
					maxZoom: 17
				}),
				esri_world_street_map: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}'),
				esri_world_topo_map: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}'),
				citra_satelit: L.esri.imageMapLayer({
					url: 'https://portal.ina-sdi.or.id/arcgis/rest/services/CITRASATELIT/JawaBaliNusra_2015_ImgServ1/ImageServer',
					attribution: 'Badan Informasi Geospasial'
				}),
				peta_rbi: L.esri.dynamicMapLayer({
					url: 'https://portal.ina-sdi.or.id/arcgis/rest/services/IGD/RupabumiIndonesia/MapServer',
					attribution: 'Badan Informasi Geospasial'
				}),
				peta_rbi_opensource: L.tileLayer.wms('http://palapa.big.go.id:8080/geoserver/gwc/service/wms', {
					maxZoom: 20,
					layers: "basemap_rbi:basemap",
					format: "image/png",
					attribution: 'Badan Informasi Geospasial'
				})

			}

			L.control.layers(basemap).addTo(map);

			map.addControl(new L.Control.Search({
				url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}',
				jsonpParam: 'json_callback',
				propertyName: 'display_name',
				propertyLoc: ['lat', 'lon'],
				marker: L.circleMarker([0, 0], {
					radius: 30
				}),
				autoCollapse: true,
				autoType: false,
				minLength: 2
			}));

			var theMarker = {};

			if (value != null || value != undefined) {
				theMarker = L.marker(value).addTo(map);
			}

			map.on('click', function(e) {
				lat = e.latlng.lat;
				lon = e.latlng.lng;

				$('#tambah-latitude').val(lat);
				$('#tambah-longitude').val(lon);
				//Clear existing marker, 
				if (theMarker != undefined) {
					map.removeLayer(theMarker);
				};

				//Add a marker to show where you clicked.
				theMarker = L.marker([lat, lon]).addTo(map);
			});
		}
	</script>

	<script>
		$(() => {
			$('select[multiple]').select2({
				width: '100%',
				placeholder: 'Pilih np tergabung bisa lebih dari satu',
			})
		})
	</script>
