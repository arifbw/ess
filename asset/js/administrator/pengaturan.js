function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama Pengaturan harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}

	if(formulir["isi"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_isi").innerHTML = "Isi harus diisi.";
	}
	else{
		document.getElementById("warning_isi").innerHTML = "";
	}
	
	return lanjut;
}

function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Pengaturan harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}

	if(formulir["isi_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_isi_ubah").innerHTML = "Isi harus diisi.";
	}
	else{
		document.getElementById("warning_isi_ubah").innerHTML = "";
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

	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	
	formulir["isi"].value = element.parentNode.previousSibling.innerHTML;
	formulir["isi_ubah"].value = element.parentNode.previousSibling.innerHTML;
}