function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["kelompok_modul"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kelompok_modul").innerHTML = "Kelompok Modul harus diisi.";
	}
	else{
		document.getElementById("warning_kelompok_modul").innerHTML = "";
	}
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama Modul harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}

	if(formulir["icon"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_icon").innerHTML = "Icon harus diisi.";
	}
	else{
		document.getElementById("warning_icon").innerHTML = "";
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
	
	if(formulir["kelompok_modul_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kelompok_modul_ubah").innerHTML = "Kelompok Modul harus diisi.";
	}
	else{
		document.getElementById("warning_kelompok_modul_ubah").innerHTML = "";
	}
	
	if(formulir["nama_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Modul harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}

	if(formulir["icon_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_icon_ubah").innerHTML = "Icon harus diisi.";
	}
	else{
		document.getElementById("warning_icon").innerHTML = "";
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

function icon_pilihan(element){
	if(element.checked){
		document.getElementById("pilihan_icon").value=element.value;
	}
}

function pilih_icon(){
	if(document.getElementById("modal_aksi").value=="tambah"){
		document.getElementById("icon").value=document.getElementById("pilihan_icon").value;
		document.getElementById("gambar_icon").className="fa "+document.getElementById("pilihan_icon").value+" fa-fw";
	}
	else if(document.getElementById("modal_aksi").value=="ubah"){
		document.getElementById("icon_ubah").value=document.getElementById("pilihan_icon").value;
		document.getElementById("gambar_icon_ubah").className="fa "+document.getElementById("pilihan_icon").value+" fa-fw";
	}
}

function set_modal(aksi){
	document.getElementById("modal_aksi").value=aksi;
	if(aksi=="ubah"){
		var formulir = document.getElementById("formulir_ubah");
		document.getElementById("icon_"+formulir["icon_ubah"].value).checked=true;
	}
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");

	var nama_kelompok_modul_ubah = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	for(var i=0;i<formulir["kelompok_modul_ubah"].options.length;i++){
		if(formulir["kelompok_modul_ubah"].options[i].text==nama_kelompok_modul_ubah){
			formulir["kelompok_modul_ubah"].selectedIndex = i;
		}
		
	}
	formulir["kelompok_modul"].value = formulir["kelompok_modul_ubah"].value;

	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	
	formulir["url"].value = element.parentNode.previousSibling.previousSibling.previousSibling.childNodes[0].innerHTML;
	formulir["url_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.childNodes[0].innerHTML;
	
	formulir["icon"].value = element.parentNode.previousSibling.previousSibling.childNodes[0].className.split(" ")[1];
	formulir["icon_ubah"].value = formulir["icon"].value;
	document.getElementById("gambar_icon_ubah").className = element.parentNode.previousSibling.previousSibling.childNodes[0].className;
	
	if(element.parentNode.previousSibling.innerHTML=="Aktif"){
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if(element.parentNode.previousSibling.innerHTML=="Non Aktif"){
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}