function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["judul"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_judul").innerHTML = "Judul harus diisi.";
	}
	else{
		document.getElementById("warning_judul").innerHTML = "";
	}
		
	if(formulir["unit"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_unit").innerHTML = "unit harus diisi.";
	}
	else{
		document.getElementById("warning_unit").innerHTML = "";
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
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Modul harus diisi.";
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
	
	return lanjut;
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");

	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	
	formulir["unit"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["unit_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;

	formulir["poin"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["poin_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	
	if(element.parentNode.previousSibling.innerHTML=="Aktif"){
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if(element.parentNode.previousSibling.innerHTML=="Non Aktif"){
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}
