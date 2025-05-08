function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama makanan harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}

	if(formulir["lokasi"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_lokasi").innerHTML = "Lokasi harus dipilih.";
	}
	else{
		document.getElementById("warning_lokasi").innerHTML = "";
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
		document.getElementById("warning_nama_ubah").innerHTML = "Nama makanan harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}
	
	if(formulir["lokasi_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
        }
		document.getElementById("warning_lokasi_ubah").innerHTML = "Lokasi harus diisi.";
	}
	else{
		document.getElementById("warning_lokasi_ubah").innerHTML = "";
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
	console.log(element.dataset);
    var formulir = document.getElementById("formulir_ubah");
    $('#id_ubah').val(element.dataset.id);
    $('#nama_old').val(element.dataset.nama);
    $('#nama_ubah').val(element.dataset.nama);
    $('#harga_old').val(element.dataset.harga);
    $('#harga_ubah').val(element.dataset.harga);
    $('#lokasi_old').val(element.dataset.lokasi);
    $('#lokasi_ubah').val(element.dataset.lokasi);
    
    $('#status_old').val(element.dataset.status);
    if(element.dataset.status==1){
		document.getElementById("status_ubah_aktif").checked = true;
	} else{
        document.getElementById("status_ubah_non_aktif").checked = true;
    }
	
    $('#modal_ubah').modal('show');
}