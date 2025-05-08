function batal_tambah(){
	if(confirm("Batal unggah foto?")){
		document.getElementById("file_foto").value = "";
		document.getElementById("banyak_tambah_foto").value = document.getElementById("file_foto").files.length;
	}
}

function cari(){
	setHalaman(1);
	lihat();
}

function cek_simpan_tambah(){
	if(document.getElementById("warning_foto_karyawan").innerHTML==""){
		return confirm(document.getElementById("file_foto").files.length+" foto karyawan akan diunggah");
	}
	else{
		return false;
	}
}

function cek_simpan_ubah(){
	if(document.getElementById("warning_foto_karyawan_ubah").innerHTML==""){
		return confirm("Anda akan mengubah foto "+document.getElementById("ubah_nama").innerHTML);
	}
	else{
		return false;
	}
}

function hitung_tambah(){
	if(document.getElementById("file_foto").files.length>parseInt(document.getElementById("max_file").value)){
		document.getElementById("warning_foto_karyawan").innerHTML = "Foto tidak boleh lebih dari "+document.getElementById("max_file").value+" buah.";
	}
	else{
		document.getElementById("warning_foto_karyawan").innerHTML = "";
	}
}

function lihat(){
	processAjax("lihat",document.getElementById("halaman").value+"/"+document.getElementById("cari").value);
}

function hasilLihat(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("foto_karyawan").innerHTML = req.responseText;
		}
	}
}

function processAjax(report,params){
	if(report=="lihat"){
		url = "osdm/foto_karyawan/lihat/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="lihat"){
			req.onreadystatechange = hasilLihat;
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
			if(report=="lihat"){
				req.onreadystatechange = hasilLihat;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}

function setHalaman(halaman){
	document.getElementById("halaman").value=halaman;
	lihat();
}

function tampil_data_ubah(element){
	document.getElementById("no_pokok_ubah").value = element.parentNode.parentNode.childNodes[4].innerHTML;
	document.getElementById("ubah_no_pokok").innerHTML = element.parentNode.parentNode.childNodes[4].innerHTML;
	document.getElementById("ubah_nama").innerHTML = element.parentNode.parentNode.childNodes[2].innerHTML;
}

function ubah(){
	if(document.getElementById("file_foto_ubah").files[0].name.split(".")[0]!=document.getElementById("no_pokok_ubah").value){
		document.getElementById("warning_foto_karyawan_ubah").innerHTML = "Penamaan file tidak sesuai.";
	}
	else if(document.getElementById("file_foto_ubah").files[0].name.split(".")[1]!="jpg" && document.getElementById("file_foto_ubah").files[0].name.split(".")[1]!="JPG"){
		document.getElementById("warning_foto_karyawan_ubah").innerHTML = "Ekstensi file tidak sesuai.";
	}
	else{
		document.getElementById("warning_foto_karyawan_ubah").innerHTML = "";
	}
}

$(document).ready(function() {
	lihat();
});