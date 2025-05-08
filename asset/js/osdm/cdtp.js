function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["karyawan"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_karyawan").innerHTML = "Karyawan harus diisi.";
	}
	else{
		document.getElementById("warning_karyawan").innerHTML = "";
	}
		
	if(formulir["tanggal_mulai"].value == "" && formulir["tanggal_selesai"].value != ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode").innerHTML = "Tanggal mulai harus diisi.";
	}
	else if(formulir["tanggal_mulai"].value != "" && formulir["tanggal_selesai"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode").innerHTML = "Tanggal selesai harus diisi.";
	}
	else if(formulir["tanggal_mulai"].value == "" && formulir["tanggal_selesai"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode").innerHTML = "Tanggal mulai dan tanggal selesai harus diisi.";
	}
	else if(formulir["tanggal_mulai"].value > formulir["tanggal_selesai"].value){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode").innerHTML = "Tanggal selesai harus setelah tanggal mulai.";
	}
	else{
		document.getElementById("warning_periode").innerHTML = "";
	}
	
	if(formulir["skep"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_skep").innerHTML = "SKEP harus diisi.";
	}
	else{
		document.getElementById("warning_skep").innerHTML = "";
	}
	
	return lanjut;
}

function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["karyawan_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_karyawan_ubah").innerHTML = "Karyawan harus diisi.";
	}
	else{
		document.getElementById("warning_karyawan_ubah").innerHTML = "";
	}
		
	if(formulir["tanggal_mulai_ubah"].value == "" && formulir["tanggal_selesai_ubah"].value != ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode_ubah").innerHTML = "Tanggal mulai harus diisi.";
	}
	else if(formulir["tanggal_mulai_ubah"].value != "" && formulir["tanggal_selesai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode_ubah").innerHTML = "Tanggal selesai harus diisi.";
	}
	else if(formulir["tanggal_mulai_ubah"].value == "" && formulir["tanggal_selesai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode_ubah").innerHTML = "Tanggal mulai dan tanggal selesai harus diisi.";
	}
	else if(formulir["tanggal_mulai_ubah"].value > formulir["tanggal_selesai_ubah"].value){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode_ubah").innerHTML = "Tanggal selesai harus setelah tanggal mulai.";
	}
	else{
		document.getElementById("warning_periode_ubah").innerHTML = "";
	}
	
	if(formulir["skep_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_skep_ubah").innerHTML = "SKEP harus diisi.";
	}
	else{
		document.getElementById("warning_skep_ubah").innerHTML = "";
	}
	
	return lanjut;
}
	
function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");

	formulir["karyawan"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML+" - "+element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["karyawan_ubah"].value = formulir["karyawan"].value;
	
//    var a = Date.parse(formulir["tanggal_mulai"].value);
//    var date = new Date(a);
//    var start_date = date.toString("dd-MM-yyyy");
	formulir["tanggal_mulai"].value = element.parentNode.previousSibling.previousSibling.childNodes[0].value;
	formulir["tanggal_mulai_ubah"].value = formulir["tanggal_mulai"].value;
    
//	var date = new Date(formulir["tanggal_selesai"].value);
//    var end_date = date.toString("dd-MM-yyyy");
	formulir["tanggal_selesai"].value = element.parentNode.previousSibling.previousSibling.childNodes[1].value;
	formulir["tanggal_selesai_ubah"].value = formulir["tanggal_selesai"].value;
	
	formulir["skep"].value = element.parentNode.previousSibling.innerHTML;
	formulir["skep_ubah"].value = formulir["skep"].value;
}

