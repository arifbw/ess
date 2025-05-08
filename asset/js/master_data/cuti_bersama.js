function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["tanggal"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_tanggal").innerHTML = "Tanggal Cuti Bersama harus diisi.";
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
	
	return lanjut;
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");
	
	formulir["tanggal"].value = element.parentNode.previousSibling.previousSibling.children[0].value;
	formulir["tanggal_ubah"].value = element.parentNode.previousSibling.previousSibling.children[0].value;
	
	formulir["deskripsi"].value = element.parentNode.previousSibling.innerHTML;
	formulir["deskripsi_ubah"].value = element.parentNode.previousSibling.innerHTML;
}