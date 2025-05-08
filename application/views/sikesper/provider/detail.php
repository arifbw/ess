<link rel="stylesheet" href="<?= base_url() ?>asset/leaflet/css/leaflet.css">
<style type="text/css">
	.content-catatan {
		background: #f0f5f5;
		font-size: 12px;
		font-size: 1vw;

		width: 100%;
		overflow: hidden;
		text-align: justify;

		padding: 5%;
		margin-top: 5%;
		margin-bottom: 3%;
	}

	.content-catatan h2 {
		text-align: left;
	}

</style>

<form role="form">
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Nama</label>
			</div>
			<div class="col-lg-9">
				<input class="form-control" value="<?= $nama ?>" readonly>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Tipe</label>
			</div>
			<div class="col-lg-9">
                <input class="form-control" value="<?= $tipe ?>" readonly>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>No Telepon</label>
			</div>
			<div class="col-lg-9">
				<input class="form-control" value="<?= $no_telp ?>" readonly>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Provinsi</label>
			</div>
			<div class="col-lg-9">
				<input class="form-control" value="<?= $nama_provinsi ?>" readonly>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Kabupaten</label>
			</div>
			<div class="col-lg-9">
				<input class="form-control" value="<?= $nama_kabupaten ?>" readonly>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Alamat</label>
			</div>
			<div class="col-lg-9">
				<textarea class="form-control" readonly><?= $alamat ?></textarea>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-3">
			<label>Maps</label>
		</div>
		<div class="col-lg-9">
			<div style="min-height: 200px; margin-bottom: 2%;" id="map"></div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Latitude</label>
			</div>
			<div class="col-lg-9">
				<input class="form-control" value="<?= $latitude ?>" readonly>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Longitude</label>
			</div>
			<div class="col-lg-9">
				<input class="form-control" value="<?= $longitude ?>" readonly>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Status</label>
			</div>
			<div class="col-lg-9">
				<input class="form-control" value="<?= $aktif == '1' ? 'Aktif' : 'Non Aktif'; ?>" readonly>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Catatan</label>
			</div>
			<div class="col-lg-10">
				<div class="content-catatan">
					<?= $catatan ?>
				</div>
			</div>
		</div>
	</div>
</form>

<script src="<?= base_url() ?>asset/leaflet/js/leaflet.js"></script>
<script src="<?= base_url() ?>asset/leaflet/js/leaflet-esri.js"></script>
<script type="text/javascript"> 
    var map;
    var point_layers = [];
    var polygon_layers = [];
    var markers = [];
    var marker;

    $(document).ready(() => {

    	<?php if(!isset($latitude)){ ?>
        	init_map([-6.21462, 106.84513]); 
        <?php }else{ ?>
        	init_map([<?= @$latitude ?>, <?= @$longitude ?>], [<?= @$latitude ?>, <?= @$longitude ?>]);
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

        var theMarker = {};
        
        if(value != null || value != undefined){
        	theMarker = L.marker(value).addTo(map);
        }
    } 
</script>