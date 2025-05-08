function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["tanggal"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_tanggal").innerHTML = "Tanggal Hari Libur harus diisi.";
	}
	else{
		document.getElementById("warning_tanggal").innerHTML = "";
	}
	
	if(formulir["deskripsi"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_deskripsi").innerHTML = "Deskripsi harus diisi.";
	}
	else{
		document.getElementById("warning_deskripsi").innerHTML = "";
	}
		
	if(formulir["hari_raya_keagamaan"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_hari_raya_keagamaan").innerHTML = "Hari Raya Keagamaan harus diisi.";
	}
	else{
		document.getElementById("warning_hari_raya_keagamaan").innerHTML = "";
	}
	
	return lanjut;
}

function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["tanggal_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_tanggal_ubah").innerHTML = "Tanggal harus diisi.";
	}
	else{
		document.getElementById("warning_tanggal_ubah").innerHTML = "";
	}
	
	if(formulir["hari_raya_keagamaan_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_hari_raya_keagamaan_ubah").innerHTML = "Hari Raya Keagamaan harus diisi.";
	}
	else{
		document.getElementById("warning_hari_raya_keagamaan_ubah").innerHTML = "";
	}
	
	return lanjut;
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");
	
	formulir["tanggal"].value = element.parentNode.previousSibling.previousSibling.previousSibling.children[0].value;
	formulir["tanggal_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.children[0].value;
	
	formulir["deskripsi"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["deskripsi_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	
	if(element.parentNode.previousSibling.innerHTML=="Ya"){
		document.getElementById("hari_raya_keagamaan_ubah_ya").checked = true;
	}
	else if(element.parentNode.previousSibling.innerHTML=="Tidak"){
		document.getElementById("hari_raya_keagamaan_ubah_tidak").checked = true;
	}
	formulir["hari_raya_keagamaan"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}