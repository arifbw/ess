<link rel="stylesheet" href="<?= base_url() ?>asset/leaflet/css/leaflet.css">
<link rel="stylesheet" href="<?= base_url() ?>asset/leaflet/css/leaflet-search.css">
<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="<?php echo base_url().'asset/summernote/summernote-bs4.css';?>">

<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"><?php echo $judul;?></h1>
			</div>
			<!-- /.col-lg-12 -->
		</div>
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
		<?php } ?>
		<div class="row">
			<div class="col-lg-12">
				<form role="form" action="<?= site_url('sikesper/ketentuan/info_provider/index'); ?>" id="formulir_tambah" method="post">
					<input class="action" type="hidden" name="aksi" value="<?= $action ?>"/>
					<input type="hidden" name="id" value="<?= @$id ?>"/>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>Nama</label>
							</div>
							<div class="col-lg-7">
								<input class="form-control" name="nama" placeholder="Masukkan Nama" value="<?= @$data_provider->nama ?>" required>
							</div>
							<div id="warning_nama" class="col-lg-3 text-danger"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>Tipe</label>
							</div>
							<div class="col-lg-7">
                                <select class="form-control" name="tipe" id="tipe" required>
                                    <option value="" data-nama_mst_bbm="" disabled selected>-- Pilih --</option>
                                    <option value="Rumah Sakit" <?= @$data_provider->tipe == 'Rumah Sakit' ? 'selected' : '' ?>>Rumah Sakit</option>
                                    <option value="Klinik" <?= @$data_provider->tipe == 'Klinik' ? 'selected' : '' ?>>Klinik</option>
                                    <option value="Apotik" <?= @$data_provider->tipe == 'Apotik' ? 'selected' : '' ?>>Apotik</option>
                                    <option value="Optik" <?= @$data_provider->tipe == 'Optik' ? 'selected' : '' ?>>Optik</option>
                                </select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>No Telepon</label>
							</div>
							<div class="col-lg-7">
								<input class="form-control" name="no_telp" placeholder="Masukkan No Telepon" value="<?= @$data_provider->no_telp ?>" required>
							</div>
							<div id="warning_no_telp" class="col-lg-3 text-danger"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>Provinsi</label>
							</div>
							<div class="col-lg-7">
								<select class="provinsi-api" name="id_provinsi">
									<option value="<?= @$data_provider->id_provinsi ?>"><?= @$data_provider->provinsi ?></option>
								</select>
							</div>
							<div id="warning_provinsi" class="col-lg-3 text-danger"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>Kabupaten</label>
							</div>
							<div class="col-lg-7">
								<select class="kabupaten-api" name="id_kabupaten">
									<option value="<?= @$data_provider->id_kabupaten ?>"><?= @$data_provider->kabupaten ?></option>
								</select>
							</div>
							<div id="warning_kabupaten" class="col-lg-3 text-danger"></div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>Alamat</label>
							</div>
							<div class="col-lg-7">
								<textarea class="form-control" name="alamat" required><?= @$data_provider->alamat ?></textarea>
							</div>
							<div id="warning_alamat" class="col-lg-3 text-danger"></div>
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
								<label>Longitude Latitude</label>
							</div>
							<div class="col-lg-5">
								<input id="tambah-longitude" class="form-control" type="text" name="longitude" value="<?= @$data_provider->longitude; ?>" />
							</div>
							<div class="col-lg-5">
								<input id="tambah-latitude" class="form-control" type="text" name="latitude" value="<?= @$data_provider->latitude; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-lg-2">
								<label>Catatan</label>
							</div>
							<div class="col-lg-10">
								<textarea id="tambah-catatan" class="summernote" name="catatan"><?= @$data_provider->catatan ?></textarea>
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
									<input type="radio" name="status" value="1" <?= @$data_provider->aktif == '1' ? 'checked' : '' ?> required>Aktif
								</label>
								<label class='radio-inline'>
									<input type="radio" name="status" value="0" <?= @$data_provider->aktif == '0' ? 'checked' : '' ?> required>Non Aktif
								</label>
							</div>
							<div id="warning_status" class="col-lg-3 text-danger"></div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 text-center">
							<button type="submit" class="btn btn-primary">Simpan</button>
						</div>
					</div>
				</form>
			</div>
			<!-- /.col-lg-12 -->
		</div>
	</div>
</div>

<script type="text/javascript" src="<?php echo base_url().'asset/summernote/summernote-bs4.js';?>"></script>
<script src="<?= base_url() ?>asset/leaflet/js/leaflet.js"></script>
<script src="<?= base_url() ?>asset/leaflet/js/leaflet-esri.js"></script>
<script src="<?= base_url() ?>asset/leaflet/js/leaflet-search.js"></script>

<script type="text/javascript">
	function generateOption(el, url, text)
	{
		var option = 
			$(el).select2({
				width: "100%",
		       	allowClear: true,
		       	placeholder: text,
		       	ajax: {
		          	dataType: 'json',
		          	url: url,
		          	delay: 800,
		          	data: function(params) {
		            	return {
		              		search: params.term
		            	}
		          	},
		          	Results: function (data, page) {
		          		return {
		            		results: data
		          		};
		        	},
		      	}
		    });

		return option;
	}

	$(document).ready(function() {
		if($('.action').val() == 'ubah'){
			setTimeout(function(){
				var provinsi = $('.provinsi-api').val();
		    	var url = '<?= site_url('sikesper/ketentuan/info_provider/daftar_kabupaten/'); ?>'+provinsi;

		    	generateOption('.kabupaten-api', url, 'Masukan Kabupaten');
			}, 1000);
		}


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

	    $('.kabupaten-api').select2({
			width: "100%",
			placeholder: "Silahkan Pilih Provinsi"
		});

		generateOption('.provinsi-api', '<?= site_url('sikesper/ketentuan/info_provider/daftar_provinsi/'); ?>', 'Masukan Provinsi');

		$(document).on('change', '.provinsi-api', function() {
	    	var provinsi = $(this).val();
	    	var url = '<?= site_url('sikesper/ketentuan/info_provider/daftar_kabupaten/'); ?>'+provinsi;

	    	generateOption('.kabupaten-api', url, 'Masukan Kabupaten');
	    });
	});

    function uploadImage(image, id) {
        var data = new FormData();
        data.append("image", image);
        $.ajax({
            url: "<?php echo site_url('sikesper/ketentuan/info_provider/upload_image'); ?>",
            cache: false,
            contentType: false,
            processData: false,
            data: data,
            type: "POST",
            success: function(url) {
                $('#'+id).summernote("insertImage", url);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    function deleteImage(src) {
        $.ajax({
            data: {src : src},
            type: "POST",
            url: "<?php echo site_url('sikesper/ketentuan/info_provider/delete_image'); ?>",
            cache: false,
            success: function(response) {
                console.log(response);
            }
        });
    }
</script>

<script type="text/javascript"> 
    let map;
    let point_layers = [];
    let polygon_layers = [];
    let markers = [];
    let marker;

    $(document).ready(() => {

    	<?php if(empty($data_provider)){ ?>
        	init_map([-6.21462, 106.84513]); 
        <?php }else{ ?>
        	init_map([<?= @$data_provider->latitude ?>, <?= @$data_provider->longitude ?>], [<?= @$data_provider->latitude ?>, <?= @$data_provider->longitude ?>]);
    	<?php } ?>

    });

    function init_map(latlong, value=null) {
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

        map.addControl( new L.Control.Search({
			url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}',
			jsonpParam: 'json_callback',
			propertyName: 'display_name',
			propertyLoc: ['lat','lon'],
			marker: L.circleMarker([0,0],{radius:30}),
			autoCollapse: true,
			autoType: false,
			minLength: 2
		}) );

        var theMarker = {};
        
        if(value != null || value != undefined){
        	theMarker = L.marker(value).addTo(map);
        }

        map.on('click',function(e){
            lat = e.latlng.lat;
            lon = e.latlng.lng;

            $('#tambah-latitude').val(lat);
            $('#tambah-longitude').val(lon); 
                //Clear existing marker, 
                if (theMarker != undefined) {
                    map.removeLayer(theMarker);
                };

            //Add a marker to show where you clicked.
            theMarker = L.marker([lat,lon]).addTo(map);  
        });
    } 
</script>