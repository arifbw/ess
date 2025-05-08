function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama Survey harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}
	
	if(formulir["konten"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_konten").innerHTML = "Judull harus diisi.";
	}
	else{
		document.getElementById("warning_konten").innerHTML = "";
	}
	
	if(formulir["link"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_link").innerHTML = "Link harus diisi.";
	}
	else{
		document.getElementById("warning_link").innerHTML = "";
	}
	
	if(formulir["poin"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_poin").innerHTML = "Poin harus diisi.";
	}
	else{
		document.getElementById("warning_poin").innerHTML = "";
	}
	
	if(formulir["durasi_baca"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_durasi_baca").innerHTML = "Durasi Baca harus diisi.";
	}
	else{
		document.getElementById("warning_durasi_baca").innerHTML = "";
	}
	
	if(formulir["start_date"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_start_date").innerHTML = "Tanggal awal harus diisi.";
	}
	else{
		document.getElementById("warning_start_date").innerHTML = "";
	}
	
	if(formulir["end_date"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_end_date").innerHTML = "Tanggal akhir harus diisi.";
	}
	else{
		document.getElementById("warning_end_date").innerHTML = "";
	}

	if (formulir["gambar"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_gambar").innerHTML = "Gambar harus diisi.";
	}
	else {
		document.getElementById("warning_gambar").innerHTML = "";
	}

	if(formulir["status"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_status").innerHTML = "Status harus diisi.";
	}
	else{
		document.getElementById("warning_status").innerHTML = "";
	}
	
	return lanjut;
}

function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["nama_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Survey harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}
	
	if(formulir["konten_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_konten_ubah").innerHTML = "Judull harus diisi.";
	}
	else{
		document.getElementById("warning_konten_ubah").innerHTML = "";
	}
	
	if(formulir["link_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_link_ubah").innerHTML = "Link harus diisi.";
	}
	else{
		document.getElementById("warning_link_ubah").innerHTML = "";
	}
	
	if(formulir["poin_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_poin_ubah").innerHTML = "Poin harus diisi.";
	}
	else{
		document.getElementById("warning_poin_ubah").innerHTML = "";
	}
	
	if(formulir["durasi_baca_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_durasi_baca_ubah").innerHTML = "Durasi Baca harus diisi.";
	}
	else{
		document.getElementById("warning_durasi_baca_ubah").innerHTML = "";
	}
	
	if(formulir["start_date_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_start_date_ubah").innerHTML = "Tanggal awal harus diisi.";
	}
	else{
		document.getElementById("warning_start_date_ubah").innerHTML = "";
	}
	
	if(formulir["end_date_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_end_date_ubah").innerHTML = "Tanggal akhir harus diisi.";
	}
	else{
		document.getElementById("warning_end_date_ubah").innerHTML = "";
	}

	if(formulir["status_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_status_ubah").innerHTML = "Status harus diisi.";
	}
	else{
		document.getElementById("warning_status_ubah").innerHTML = "";
	}
	
	return lanjut;
}

function hasilSalinTambah(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			var arr_hasil = req.responseText.split("|");

			var formulir = document.getElementById("formulir_tambah");
			
			formulir["nama"].value = arr_hasil[1];
			formulir["konten"].value = arr_hasil[2];
			formulir["link"].value = arr_hasil[3];
			formulir["poin"].value = arr_hasil[4];
			formulir["durasi_baca"].value = arr_hasil[5];
			formulir["start_date"].value = arr_hasil[6];
			formulir["end_date"].value = arr_hasil[7];
			
			if(parseInt(arr_hasil[8])==1){
				formulir["status"].value = "aktif";
			}
			else if(parseInt(arr_hasil[8])==0){
				formulir["status"].value = "non aktif";
			}
		}
	}
}

function hasilUbah(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isian_ubah_survey").innerHTML = req.responseText;
		}
	}
}

function processAjax(report,params){
	if(report=="salin_tambah"){
		url = "poin_reward/ajax/manajemen_survey/salin/";
	}
	else if(report=="ubah"){
		url = "poin_reward/ajax/manajemen_survey/ubah/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="salin_tambah"){
			req.onreadystatechange = hasilSalinTambah;
		}
		else if(report=="ubah"){
			req.onreadystatechange = hasilUbah;
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
			if(report=="salin_tambah"){
				req.onreadystatechange = hasilSalinTambah;
			}
			else if(report=="salin_ubah"){
				req.onreadystatechange = hasilUbah;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}

function set_modal(aksi){
	document.getElementById("modal_aksi").value=aksi;
	if(aksi=="ubah"){
		var formulir = document.getElementById("formulir_ubah");
		document.getElementById("icon_"+formulir["icon_ubah"].value).checked=true;
	}
}

function salin_tambah(element){
	processAjax("salin_tambah",element.value);
}

function ubah(element){
	var id_manajemen_survey = element.previousSibling.value;
	processAjax("ubah",id_manajemen_survey);
}
