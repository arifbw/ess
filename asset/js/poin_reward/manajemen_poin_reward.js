function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}
	
	if(formulir["konten"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_konten").innerHTML = "Konten harus diisi.";
	}
	else{
		document.getElementById("warning_konten").innerHTML = "";
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
	
	if(formulir["kuota"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kuota").innerHTML = "Kuota harus diisi.";
	}
	else{
		document.getElementById("warning_kuota").innerHTML = "";
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
		if (document.getElementById("warning_gambar").innerHTML != "") {
			if (lanjut) {
				lanjut = false;
			}
		} else {
			document.getElementById("warning_gambar").innerHTML = "";
		}
	}

	if (formulir["status"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_status").innerHTML = "Status harus diisi.";
	}
	else {
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
		document.getElementById("warning_nama_ubah").innerHTML = "Nama harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}
	
	if(formulir["konten_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_konten_ubah").innerHTML = "Konten harus diisi.";
	}
	else{
		document.getElementById("warning_konten_ubah").innerHTML = "";
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
	
	if(formulir["kuota_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kuota_ubah").innerHTML = "Kuota harus diisi.";
	}
	else{
		document.getElementById("warning_kuota_ubah").innerHTML = "";
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

	if (formulir["gambar_ubah"].value == "") {
	}
	else {
		if (document.getElementById("warning_gambar_ubah").innerHTML != "") {
			if (lanjut) {
				lanjut = false;
			}
		} else {
			document.getElementById("warning_gambar_ubah").innerHTML = "";
		}
	}

	if (formulir["status_ubah"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_status_ubah").innerHTML = "Status harus diisi.";
	}
	else {
		document.getElementById("warning_status_ubah").innerHTML = "";
	}
	
	return lanjut;
}

function cek_simpan_scan(){
	var formulir = document.getElementById("formulir_scan");
	var lanjut = true;
	
	if(formulir["kode_scan"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kode_scan").innerHTML = "Kode Scan harus diisi.";
	}
	else{
		document.getElementById("warning_kode_scan").innerHTML = "";
	}
	
	return lanjut;
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");
	
	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	
	formulir["konten"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["konten_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	
	formulir["poin"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["poin_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	
	formulir["kuota"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["kuota_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	
	formulir["start_date"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["start_date_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	
	formulir["end_date"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["end_date_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	
	if(element.parentNode.previousSibling.previousSibling.innerHTML=="Aktif"){
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if(element.parentNode.previousSibling.previousSibling.innerHTML=="Non Aktif"){
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}

function checkPhoto(target) {
	if (target.files[0].type.indexOf("image") == -1) {
		document.getElementById("warning_gambar").innerHTML = "Tipe file tidak didukung";
		return false;
	}
	var size = target.files[0].size / 1024 / 1024;
	if (size > 2) {
		document.getElementById("warning_gambar").innerHTML = "Ukuran gambar terlalu besar " + size.toFixed(2) + 'MB' + " > (maks 2MB)";
		return false;
	}
	document.getElementById("warning_gambar").innerHTML = "";
	return true;
}

function checkPhotoUbah(target) {
	if (target.files[0].type.indexOf("image") == -1) {
		document.getElementById("warning_gambar_ubah").innerHTML = "Tipe file tidak didukung";
		return false;
	}
	var size = target.files[0].size / 1024 / 1024;
	if (size > 2) {
		document.getElementById("warning_gambar_ubah").innerHTML = "Ukuran gambar terlalu besar " + size.toFixed(2) + 'MB' + " > (maks 2MB)";
		return false;
	}
	document.getElementById("warning_gambar_ubah").innerHTML = "";
	return true;
}
