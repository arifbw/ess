function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["kelas"].value == ""){
		document.getElementById("warning_nama").innerHTML = "Nama Kelas harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}

	if(formulir["pangkat[]"].length == 0){
		document.getElementById("warning_pangkat").innerHTML = "Pangkat harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_pangkat").innerHTML = "";
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

	if(formulir["kelas"].value == ""){
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Kelas harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}

	if(formulir["pangkat[]"].length == 0){
		document.getElementById("warning_pangkat_ubah").innerHTML = "Pangkat harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_pangkat_ubah").innerHTML = "";
	}

	if(formulir["status"].value == ""){
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

function tampil_data_ubah_new(element){
	$(".addtodelete").remove();
    var formulir = document.getElementById("formulir_ubah");
    pangkat = element.dataset.pangkat.split(',');
    for (var i = 0; i < pangkat.length; i++) {
        $("#pangkat_ubah").append($("<option></option>").attr("value", pangkat[i]).attr("selected", true).attr("class", "addtodelete").text(pangkat[i]));
    }
    $('#kelas_ubah').val(element.dataset.kelas);
    if(element.dataset.status=='1'){
		document.getElementById("status_aktif_ubah").checked = true;
	} else{
        document.getElementById("status_non_aktif_ubah").checked = true;
    }

    $('#modal_ubah').modal('show');
}

