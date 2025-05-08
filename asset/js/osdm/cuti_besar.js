function ambil_data(){
	table = $('#tabel_cuti_besar').DataTable({ 
		"iDisplayLength": 10,
		"language": {
			"url": document.getElementById("base_url").value+"asset/datatables/Indonesian.json",
			"sEmptyTable": "Tidak ada data di database",
			"emptyTable": "Tidak ada data di database"
		},
		"stateSave": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.

		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": document.getElementById("base_url").value + "osdm/cuti_besar/tabel_cuti_besar/",
			"type": "POST"
		},

		//Set column definition initialisation properties.
		"columnDefs": [
			{ 
				"targets": 'no-sort', //first column / numbering column
				"orderable": false, //set not orderable
			},
		],

	});
}

function hitung_konversi_bulan(){
	var bulan = document.getElementById("konversi_dari_bulan").value;
	processAjax("hitung_konversi_bulan",bulan);
}

function hitung_konversi_hari(){
	var hari = document.getElementById("konversi_dari_hari").value;
	processAjax("hitung_konversi_hari",hari);
}

function hasilHitungKonversiBulan(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("konversi_jadi_hari").value = req.responseText;
		}
	}
}

function hasilHitungKonversiHari(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("konversi_jadi_bulan").value = req.responseText;
		}
	}
}

function hasilTampilKonversi(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_konversi").innerHTML = req.responseText;
		}
	}
}

function hasilTampilPerpanjangKadaluarsa(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_perpanjang_kadaluarsa").innerHTML = req.responseText;
		}
	}
}

function hasilTampilMaintenanceKuota(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_maintenance_kuota").innerHTML = req.responseText;
		}
	}
}

function hasilTampilUbcb(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_ubcb").innerHTML = req.responseText;
		}
	}
}

function hasilTampilKompensasi(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_kompensasi").innerHTML = req.responseText;
		}
	}
}

function tampil_konversi(element){
	var no_pokok = element.parentNode.parentNode.childNodes[0].innerHTML;
	var tahun = element.parentNode.parentNode.childNodes[2].innerHTML;
	processAjax("tampil_konversi",no_pokok+"/"+tahun);
}

function tampil_perpanjang_kadaluarsa(element){
	var no_pokok = element.parentNode.parentNode.childNodes[0].innerHTML;
	var tahun = element.parentNode.parentNode.childNodes[2].innerHTML;
	processAjax("tampil_perpanjang_kadaluarsa",no_pokok+"/"+tahun);
}

function tampil_maintenance_kuota(element){
	var no_pokok = element.parentNode.parentNode.childNodes[0].innerHTML;
	var tahun = element.parentNode.parentNode.childNodes[2].innerHTML;
	processAjax("tampil_maintenance_kuota",no_pokok+"/"+tahun);
}

function tampil_ubcb(element){
	var no_pokok = element.parentNode.parentNode.childNodes[0].innerHTML;
	var tahun = element.parentNode.parentNode.childNodes[2].innerHTML;
	processAjax("tampil_ubcb",no_pokok+"/"+tahun);	
}

function tampil_kompensasi(element){
	var no_pokok = element.parentNode.parentNode.childNodes[0].innerHTML;
	var tahun = element.parentNode.parentNode.childNodes[2].innerHTML;
	processAjax("tampil_kompensasi",no_pokok+"/"+tahun);
}

function processAjax(report,params){
	if(report=="tampil_konversi"){
		url = "osdm/ajax/cuti_besar/tampil_konversi/";
	}
	else if(report=="tampil_perpanjang_kadaluarsa"){
		url = "osdm/ajax/cuti_besar/tampil_perpanjang_kadaluarsa/";
	}
	else if(report=="tampil_maintenance_kuota"){
		url = "osdm/ajax/cuti_besar/tampil_maintenance_kuota/";
	}else if(report=="tampil_ubcb"){		
		url = "osdm/ajax/cuti_besar/tampil_ubcb/";
	}else if(report=="tampil_kompensasi"){
		url = "osdm/ajax/cuti_besar/tampil_kompensasi/";
	}
	else if(report=="hitung_konversi_bulan"){
		url = "osdm/ajax/cuti_besar/hitung_konversi_bulan/";
	}
	else if(report=="hitung_konversi_hari"){
		url = "osdm/ajax/cuti_besar/hitung_konversi_hari/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="tampil_konversi"){
			req.onreadystatechange = hasilTampilKonversi;
		}
		else if(report=="tampil_perpanjang_kadaluarsa"){
			req.onreadystatechange = hasilTampilPerpanjangKadaluarsa;
		}
		else if(report=="tampil_maintenance_kuota"){
			req.onreadystatechange = hasilTampilMaintenanceKuota;
		}
		else if(report=="tampil_ubcb"){
			req.onreadystatechange = hasilTampilUbcb;
		}
		else if(report=="tampil_kompensasi"){
			req.onreadystatechange = hasilTampilKompensasi;
		}
		else if(report=="hitung_konversi_bulan"){
			req.onreadystatechange = hasilHitungKonversiBulan;
		}
		else if(report=="hitung_konversi_hari"){
			req.onreadystatechange = hasilHitungKonversiHari;
		}
		
		try {
			req.open("GET", url, true);
		}
		catch (e) {
			alert(e);
		}
		req.send(null);
	}
	else if (window.ActiveXObject) { // IE
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) {
			if(report=="tampil_konversi"){
				req.onreadystatechange = hasilTampilKonversi;
			}
			else if(report=="tampil_perpanjang_kadaluarsa"){
				req.onreadystatechange = hasilTampilPerpanjangKadaluarsa;
			}
			else if(report=="tampil_maintenance_kuota"){
				req.onreadystatechange = hasilTampilMaintenanceKuota;
			}
			else if(report=="tampil_ubcb"){
				req.onreadystatechange = hasilTampilUbcb;
			}
			else if(report=="hitung_konversi"){
				req.onreadystatechange = hasilHitungKonversi;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}