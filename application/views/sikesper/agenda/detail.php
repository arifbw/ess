<link rel="stylesheet" href="<?= base_url() ?>asset/leaflet/css/leaflet.css">
<link rel="stylesheet" href="<?= base_url() ?>asset/leaflet/css/leaflet-search.css">

<form role="<?= $this->session->userdata('grup') == '5' ? 'pengguna' : 'admin' ?>" data-agenda="<?= $agenda; ?>" data-kry="<?= $this->session->userdata('no_pokok'); ?>" id="form-detail">
	<div class="form-group">
		<h3><?= @$data_agenda->agenda; ?></h3>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-6">
				<img src="<?= base_url(); ?>uploads/images/sikesper/agenda/<?= $data_agenda->image; ?>" style="width: 100%; max-height: 150px;" alt="Gambar Belum Diupload">
			</div>
			<div class="col-lg-6">
				<p><?= @$data_agenda->deskripsi; ?></p>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-lg-3">
				<label>Tanggal</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= !empty($data_agenda->tanggal) ? tanggal_indonesia(date('Y-m-d', strtotime($data_agenda->tanggal))) : ''; ?></label>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3">
				<label>Pukul</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= ' Pukul '.@$data_agenda->waktu_mulai.' s/d '.@$data_agenda->waktu_selesai; ?></label>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3">
				<label>Jumlah Kuota</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= @$data_agenda->kuota > 0 ? @$data_agenda->kuota : 'Tidak Terbatas'; ?></label>
			</div>
		</div>
		
		<?php if($_SESSION["grup"] != 5) { ?>
		<div class="row">
			<div class="col-lg-3">
				<label>Jumlah Pendaftar</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= @$data_agenda->jml_daftar ?></label>
			</div>
		</div>
		<?php } ?>

		<div class="row">
			<div class="col-lg-3">
				<label>Lokasi</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= @$data_agenda->nama_lokasi; ?></label>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3">
				<label>Alamat</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= @$data_agenda->alamat.' '.@$data_agenda->nama_kabupaten.' '.@$data_agenda->nama_provinsi; ?></label>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3">
				<label>Jumlah Poin</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= @$data_agenda->poin > 0 ? @$data_agenda->poin : 'Tidak ada poin'; ?></label>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3">
				<label>Nama Contact Person</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= !empty($data_agenda->cp_nama) ? @$data_agenda->cp_nama : 'Belum diisi'; ?></label>
			</div>
		</div>

		<div class="row">
			<div class="col-lg-3">
				<label>Nomor Contact Person</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= !empty($data_agenda->cp_nomor) ? @$data_agenda->cp_nomor : 'Belum diisi'; ?></label>
			</div>
		</div>
		
		<?php if($_SESSION["grup"] != 5) { ?>
		<div class="row">
			<div class="col-lg-3">
				<label>Status</label>
			</div>
			<div class="col-lg-1"> : </div>
			<div class="col-lg-8">
				<label><?= @$data_agenda->status == 1 ? 'Aktif' : 'Non Aktif' ?></label>
			</div>
		</div>
		<?php } ?>

		<div class="row">
			<div class="col-lg-12">
				<div style="margin-top: 2%; min-height: 200px;" id="map"></div>
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

    	<?php if(empty($data_agenda) || $data_agenda->longitude == ''){ ?>
        	init_map([-6.21462, 106.84513]); 
        <?php }else{ ?>
        	init_map([<?= @$data_agenda->latitude ?>, <?= @$data_agenda->longitude ?>], [<?= @$data_agenda->latitude ?>, <?= @$data_agenda->longitude ?>]);
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
