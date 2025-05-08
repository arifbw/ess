function cek_simpan_tambah() {
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;

	if (formulir["nama"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama harus diisi.";
	}
	else {
		document.getElementById("warning_nama").innerHTML = "";
	}

	if (formulir["gambar"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_gambar").innerHTML = "Gambar harus diisi.";
	}
	else {
		if (document.getElementById("warning_gambar").innerHTML != "") {
			if (lanjut) {
				lanjut = false;
			}
		} else {
			document.getElementById("warning_gambar").innerHTML = "";
		}
	}

	if (formulir["status"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_status").innerHTML = "Status harus diisi.";
	}
	else {
		document.getElementById("warning_status").innerHTML = "";
	}

	return lanjut;
}

function cek_simpan_ubah() {
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;

	if (formulir["nama_ubah"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_nama_ubah").innerHTML = "Nama harus diisi.";
	}
	else {
		if (document.getElementById("warning_nama_ubah").innerHTML != "") {
			if (lanjut) {
				lanjut = false;
			}
		} else {
			document.getElementById("warning_nama_ubah").innerHTML = "";
		}
	}

	if (formulir["gambar_ubah"].value == "") {
	}
	else {
		if (document.getElementById("warning_gambar_ubah").innerHTML != "") {
			if (lanjut) {
				lanjut = false;
			}
		} else {
			document.getElementById("warning_gambar_ubah").innerHTML = "";
		}
	}

	if (formulir["status_ubah"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_status_ubah").innerHTML = "Status harus diisi.";
	}
	else {
		document.getElementById("warning_status_ubah").innerHTML = "";
	}

	return lanjut;
}

function tampil_data_ubah(element) {
	var formulir = document.getElementById("formulir_ubah");

	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;

	if (element.parentNode.previousSibling.innerHTML == "Aktif") {
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if (element.parentNode.previousSibling.innerHTML == "Non Aktif") {
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}

function checkPhoto(target) {
	if (target.files[0].type.indexOf("image") == -1) {
		document.getElementById("warning_gambar").innerHTML = "Tipe file tidak didukung";
		return false;
	}
	var size = target.files[0].size / 1024 / 1024;
	if (size > 2) {
		document.getElementById("warning_gambar").innerHTML = "Ukuran gambar terlalu besar " + size.toFixed(2) + 'MB' + " > (maks 2MB)";
		return false;
	}
	document.getElementById("warning_gambar").innerHTML = "";
	return true;
}

function checkPhotoUbah(target) {
	if (target.files[0].type.indexOf("image") == -1) {
		document.getElementById("warning_gambar_ubah").innerHTML = "Tipe file tidak didukung";
		return false;
	}
	var size = target.files[0].size / 1024 / 1024;
	if (size > 2) {
		document.getElementById("warning_gambar_ubah").innerHTML = "Ukuran gambar terlalu besar " + size.toFixed(2) + 'MB' + " > (maks 2MB)";
		return false;
	}
	document.getElementById("warning_gambar_ubah").innerHTML = "";
	return true;
}
