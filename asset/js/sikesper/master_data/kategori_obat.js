function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama_kategori"].value == ""){
		document.getElementById("warning_nama").innerHTML = "Nama kategori harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}

	if(formulir["no_kode"].value == ""){
		document.getElementById("warning_kode").innerHTML = "Nomor kode harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_kode").innerHTML = "";
	}

	if(formulir["jenis"].value == ""){
		document.getElementById("warning_jenis").innerHTML = "Jenis kategori harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jenis").innerHTML = "";
	}

	if(formulir["status"].value == ""){
		document.getElementById("warning_status").innerHTML = "Status harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_status").innerHTML = "";
	}

	return lanjut;

}

function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	if(formulir["nama_kategori_ubah"].value == ""){
		document.getElementById("warning_nama_ubah").innerHTML = "Nama kategori harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}
	
	if(formulir["no_kode_ubah"].value == ""){
		document.getElementById("warning_kode_ubah").innerHTML = "Nomor kode harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_kode_ubah").innerHTML = "";
	}

	if(formulir["jenis_ubah"].value == ""){
		document.getElementById("warning_jenis_ubah").innerHTML = "Jenis kategori harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jenis_ubah").innerHTML = "";
	}

	if(formulir["status_ubah"].value == ""){
		document.getElementById("warning_status_ubah").innerHTML = "Status harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_status_ubah").innerHTML = "";
	}
	
	return lanjut;
}

/*function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");
	
	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	
	formulir["nopol"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["nopol_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	
	if(element.parentNode.previousSibling.innerHTML=="Aktif"){
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if(element.parentNode.previousSibling.innerHTML=="Non Aktif"){
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
    console.log(element.parentNode.previousSibling.previousSibling.innerHTML);
}*/

function tampil_data_ubah_new(element){
    var formulir = document.getElementById("formulir_ubah");
    $('#id_ubah').val(element.dataset.id);
    $('#no_kode_ubah').val(element.dataset.no);
    $('#nama_kategori_ubah').val(element.dataset.nama);
    $('#jenis_ubah').val(element.dataset.jenis).change();
    $('#id_parent_ubah').val(element.dataset.parent).change();
    
    $('#status_old').val(element.dataset.status);
    if(element.dataset.status==1){
		document.getElementById("status_ubah_aktif").checked = true;
	} else{
        document.getElementById("status_ubah_non_aktif").checked = true;
    }
	
    $('#modal_ubah').modal('show');
}

