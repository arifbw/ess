function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama Posisi Menu harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}
	
	if(formulir["shortcode"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_shortcode").innerHTML = "Shortcode Posisi Menu harus diisi.";
	}
	else{
		document.getElementById("warning_shortcode").innerHTML = "";
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
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Posisi Menu harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}
	
	if(formulir["shortcode_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_shortcode_ubah").innerHTML = "Shortcode Posisi Menu harus diisi.";
	}
	else{
		document.getElementById("warning_shortcode_ubah").innerHTML = "";
	}
	
	return lanjut;
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");

	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;

	formulir["shortcode"].value = element.parentNode.previousSibling.innerHTML;
	formulir["shortcode_ubah"].value = element.parentNode.previousSibling.innerHTML;

}