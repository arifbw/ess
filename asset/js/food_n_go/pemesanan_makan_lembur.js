function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	/*if(formulir["insert_unit_kerja"].value == ""){
		document.getElementById("warning_unit_kerja").innerHTML = "Unit kerja harus dipilih.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_unit_kerja").innerHTML = "";
	}

	if(formulir["insert_jumlah"].value == ""){
		document.getElementById("warning_jumlah").innerHTML = "Jumlah pesanan harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jumlah").innerHTML = "";
	}*/

	if(formulir["insert_np_atasan"].value == ""){
		document.getElementById("warning_np_atasan").innerHTML = "Approver harus dipilih.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_np_atasan").innerHTML = "";
	}

	/*if(formulir["insert_jenis_pesanan"].value == ""){
		document.getElementById("warning_jenis_pesanan").innerHTML = "Jenis pesanan harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jenis_pesanan").innerHTML = "";
	}*/

	if(formulir["insert_jenis_lembur"].value == ""){
		document.getElementById("warning_jenis_lembur").innerHTML = "Jenis lembur harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jenis_lembur").innerHTML = "";
	}

	var a = moment([formulir["insert_waktu_pemesanan_selesai"].value.substring(0,2), formulir["insert_waktu_pemesanan_selesai"].value.substring(3,2), 00], "HH:mm:ss");
	var b = moment([formulir["insert_waktu_pemesanan_mulai"].value.substring(0,2), formulir["insert_waktu_pemesanan_mulai"].value.substring(3,2), 00], "HH:mm:ss")
	var diff_time = a.diff(b, 'hours');

	if(formulir["insert_tanggal_pemesanan"].value == ""){
		document.getElementById("warning_tanggal_pemesanan").innerHTML = "Tanggal lembur harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_tanggal_pemesanan").innerHTML = "";
	}

	if(formulir["insert_waktu_pemesanan_mulai"].value == "" || formulir["insert_waktu_pemesanan_selesai"].value == ""){
		document.getElementById("warning_waktu_pemesanan").innerHTML = "Waktu lembur harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_waktu_pemesanan").innerHTML = "";
	}

	if(formulir["insert_waktu_pemesanan_mulai"].value >= formulir["insert_waktu_pemesanan_selesai"].value){
		document.getElementById("warning_waktu_pemesanan").innerHTML = "Waktu lembur tidak valid.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_waktu_pemesanan").innerHTML = "";
	}

	if(diff_time < 2) {
		document.getElementById("warning_waktu_pemesanan").innerHTML = "Rentang waktu lembur kurang dari 2 jam. Anda tidak mendapatkan makan lembur.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_waktu_pemesanan").innerHTML = "";
	}

	if((formulir["insert_waktu_pemesanan_mulai"].value <= moment().format('HH-mm')) && formulir["insert_tanggal_pemesanan"].value == moment().format('DD-MM-YYYY')) {
		document.getElementById("warning_waktu_pemesanan").innerHTML = "Waktu lembur sudah lewat.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_waktu_pemesanan").innerHTML = "";
	}
	
	return lanjut;

}

function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["insert_unit_kerja"].value == ""){
		document.getElementById("warning_unit_kerja_ubah").innerHTML = "Unit kerja harus dipilih.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_unit_kerja_ubah").innerHTML = "";
	}

	if(formulir["insert_jumlah"].value == ""){
		document.getElementById("warning_jumlah_ubah").innerHTML = "Jumlah pesanan harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jumlah_ubah").innerHTML = "";
	}

	if(formulir["insert_np_atasan"].value == ""){
		document.getElementById("warning_np_atasan_ubah").innerHTML = "Approver harus dipilih.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_np_atasan_ubah").innerHTML = "";
	}

	if(formulir["insert_jenis_pesanan"].value == ""){
		document.getElementById("warning_jenis_pesanan_ubah").innerHTML = "Jenis pesanan harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jenis_pesanan_ubah").innerHTML = "";
	}

	if(formulir["insert_jenis_lembur"].value == ""){
		document.getElementById("warning_jenis_lembur_ubah").innerHTML = "Jenis lembur harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jenis_lembur_ubah").innerHTML = "";
	}

	if(formulir["insert_tanggal_pemesanan"].value == ""){
		document.getElementById("warning_tanggal_pemesanan_ubah").innerHTML = "Tanggal lembur harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_waktu_pemesanan_ubah").innerHTML = "";
	}

	if(formulir["insert_waktu_pemesanan"].value == ""){
		document.getElementById("warning_waktu_pemesanan_ubah").innerHTML = "Waktu lembur harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_waktu_pemesanan_ubah").innerHTML = "";
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
