function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama Grup Pengguna harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
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

function cek_simpan_ubah() {
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["nama_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Grup Pengguna harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
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

	if(lanjut){
		var konfirmasi = "";
		if(formulir["nama_ubah"].value != formulir["nama"].value){
			konfirmasi += "Nama Grup Pengguna \""+formulir["nama"].value+"\" menjadi \""+formulir["nama_ubah"].value+"\"";
		}
		if(formulir["status_ubah"].value != formulir["status"].value){
			if(konfirmasi!=""){
				konfirmasi += "\n";
			}
			konfirmasi += "Status Grup Pengguna \""+formulir["nama"].value+"\" diubah menjadi \""+formulir["status_ubah"].value+"\"";
		}
		if(konfirmasi!=""){
			lanjut = confirm("Anda yakin akan mengubah :\n"+konfirmasi);
		}
	}
	
	return lanjut;
}

function hasilDaftarPengguna(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("lihat_pengguna").innerHTML = req.responseText;
		}
	}
	else{
		document.getElementById("lihat_pengguna").innerHTML = "loading";
	}
}

function processAjax(report,params){
	if(report=="daftar_pengguna"){
		url = "administrator/grup_pengguna/daftar_pengguna/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="daftar_pengguna"){
			req.onreadystatechange = hasilDaftarPengguna;
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
			if(report=="daftar_pengguna"){
				req.onreadystatechange = hasilDaftarPengguna;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}


function tampil_lihat_pengguna(element){
	document.getElementById("nama_grup_pengguna_lihat").innerHTML = element.parentNode.previousSibling.previousSibling.innerHTML;
	
	processAjax("daftar_pengguna",document.getElementById("nama_grup_pengguna_lihat").innerHTML);
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");

	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;

	if(element.parentNode.previousSibling.innerHTML=="Aktif"){
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if(element.parentNode.previousSibling.innerHTML=="Non Aktif"){
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}