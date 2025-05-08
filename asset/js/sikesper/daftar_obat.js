function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["merk_obat"].value == ""){
		document.getElementById("warning_merk").innerHTML = "Nama merk obat harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_merk").innerHTML = "";
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

	if(formulir["zat_aktif"].value == ""){
		document.getElementById("warning_zat_aktif").innerHTML = "Zat aktif obat harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_zat_aktif").innerHTML = "";
	}

	if(formulir["sediaan"].value == ""){
		document.getElementById("warning_sediaan").innerHTML = "Sediaan harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_sediaan").innerHTML = "";
	}

	if(formulir["dosis"].value == ""){
		document.getElementById("warning_dosis").innerHTML = "Dosis obat harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_dosis").innerHTML = "";
	}

	if(formulir["farmasi"].value == ""){
		document.getElementById("warning_farmasi").innerHTML = "Farmasi harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_farmasi").innerHTML = "";
	}

	if(formulir["id_kategori"].value == "0"){
		document.getElementById("warning_kategori").innerHTML = "Kategori harus dipilih.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_kategori").innerHTML = "";
	}

	if(formulir["status"].value == ""){
		document.getElementById("warning_status").innerHTML = "Status harus dipilih.";
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
		document.getElementById("warning_status_ubah").innerHTML = "Nama kategori harus diisi.";
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

function ubah(id) {
	$.ajax({
		method: "POST",
		url: "<?= base_url('sikesper/ketentuan/daftar_obat/ubah_view') ?>",
		data: { id: id }
	})
	.done(function( msg ) {
		console.log(msg);
		$("#set_ubah").html(msg);
	});
}