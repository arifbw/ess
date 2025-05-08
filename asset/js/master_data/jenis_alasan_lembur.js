function cek_simpan_tambah() {
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;

	if (formulir["kategori_lembur"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_kategori_lembur").innerHTML = "kategori_lembur harus diisi.";
	}
	else {
		document.getElementById("warning_kategori_lembur").innerHTML = "";
	}
	return lanjut;
}

function cek_simpan_ubah() {
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;

	if (formulir["kategori_lembur_ubah"].value == "") {
		if (lanjut) {
			lanjut = false;
		}
		document.getElementById("warning_kategori_lembur_ubah").innerHTML = "kategori_lembur harus diisi.";
	}
	else {
		document.getElementById("warning_kategori_lembur_ubah").innerHTML = "";
	}

	return lanjut;
}

function tampil_data_ubah(element) {
	var formulir = document.getElementById("formulir_ubah");

	formulir["kategori_lembur"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["kategori_lembur_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;

	if (element.parentNode.previousSibling.innerHTML == "Aktif") {
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if (element.parentNode.previousSibling.innerHTML == "Non Aktif") {
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}