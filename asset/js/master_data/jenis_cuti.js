function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["uraian"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_uraian").innerHTML = "Uraian harus diisi.";
	}
	else{
		document.getElementById("warning_uraian").innerHTML = "";
	}
	
	if(formulir["kode_erp"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kode_erp").innerHTML = "Kode ERP harus diisi.";
	}
	else{
		document.getElementById("warning_kode_erp").innerHTML = "";
	}
		
	return lanjut;
}

function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["uraian_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_uraian_ubah").innerHTML = "Uraian harus diisi.";
	}
	else{
		document.getElementById("warning_uraian_ubah").innerHTML = "";
	}
	
	if(formulir["kode_erp_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kode_erp_ubah").innerHTML = "Kode ERP harus diisi.";
	}
	else{
		document.getElementById("warning_kode_erp_ubah").innerHTML = "";
	}
	
	return lanjut;
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");
	
	formulir["uraian"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["uraian_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	
	formulir["kode_erp"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["kode_erp_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	
	if(element.parentNode.previousSibling.innerHTML=="Aktif"){
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if(element.parentNode.previousSibling.innerHTML=="Non Aktif"){
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}