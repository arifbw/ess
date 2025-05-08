function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["insert_nama_acara"].value == ""){
		document.getElementById("warning_nama_acara").innerHTML = "Nama acara harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_nama_acara").innerHTML = "";
	}

	if(formulir["insert_tanggal_pemesanan"].value == ""){
		document.getElementById("warning_tanggal_pemesanan").innerHTML = "Tanggal pemesanan harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_tanggal_pemesanan").innerHTML = "";
	}

	if(formulir["insert_waktu_mulai"].value == ""){
		document.getElementById("warning_waktu_pemesanan").innerHTML = "Waktu mulai pemesanan harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_waktu_pemesanan").innerHTML = "";
	}

	if(formulir["insert_jumlah_peserta"].value == ""){
		document.getElementById("warning_jumlah_peserta").innerHTML = "Jumlah peserta harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_jumlah_peserta").innerHTML = "";
	}

	if(formulir["insert_lokasi_acara"].value == ""){
		document.getElementById("warning_lokasi_acara").innerHTML = "Lokasi acara harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_lokasi_acara").innerHTML = "";
	}

	if(formulir["insert_id_ruangan"].value == ""){
		document.getElementById("warning_id_ruangan").innerHTML = "Ruangan harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_id_ruangan").innerHTML = "";
	}

	if((formulir["insert_snack"].value).length == 0){
		document.getElementById("warning_snack").innerHTML = "Snack harus dipilih.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_snack").innerHTML = "";
	}

	var jml_peserta = formulir["insert_jumlah_peserta"].value;
	var sum_makanan = 0;
	var sum_minuman = 0;

	$('.jumlah_pesan_makanan').each(function(){
	    sum_makanan += parseFloat($(this).val());
	});

	$('.jumlah_pesan_minuman').each(function(){
	    sum_minuman += parseFloat($(this).val());
	});

	console.log(sum_makanan);

	if(sum_makanan > jml_peserta){
		document.getElementById("warning_makanan").innerHTML = "Jumlah makanan melebihi jumlah peserta.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_makanan").innerHTML = "";
	}

	if(sum_minuman > jml_peserta){
		document.getElementById("warning_minuman").innerHTML = "Jumlah minuman melebihi jumlah peserta.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_minuman").innerHTML = "";
	}

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

	if(formulir["insert_kode_akun_sto"].value == ""){
		document.getElementById("warning_kode_akun_sto").innerHTML = "Kode akun STO harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_kode_akun_sto").innerHTML = "";
	}

	if(formulir["insert_kode_anggaran"].value == ""){
		document.getElementById("warning_kode_anggaran").innerHTML = "Kode anggaran harus diisi.";
		if(lanjut){
			lanjut = false;
			return lanjut;
		}
	}
	else{
		document.getElementById("warning_kode_anggaran").innerHTML = "";
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
	
}

function tampil_data_approval(element){
    $('.detail').hide();
    $('.approval').show();
    $('#label_modal_np').text('Berikan Persetujuan');
    $.ajax({
        url: "detail",
        type: "POST",
        dataType: "json",
        data: {no_pemesanan: element.dataset.no},
        success: function(response) {
		    $('#detail_no_pemesanan').val(element.dataset.no);
		    $('#detail_np_pemesan').val(response.detail.np_pemesan);
		    $('#detail_nama_pemesan').val(response.detail.nama_pemesan);
		    $('#detail_snack').html(response.snack.daftar);
		    $('#detail_makanan').html(response.makanan.daftar);
		    $('#detail_minuman').html(response.minuman.daftar);
		    $('#detail_jenis_pemesanan').val(response.detail.jenis_pesanan);
		    $('#detail_ruangan').val(response.detail.nama_gedung+' - '+response.detail.nama_ruangan);
		    $('#detail_unit_kerja').val(response.detail.nama_unit_pemesan);
		    $('#detail_jumlah_pemesanan').val(response.detail.jumlah_pemesanan);
		    $('#detail_tgl_pemesanan').val(response.detail.tanggal_pemesanan);
		    $('#detail_waktu_pemesanan').val(response.detail.waktu_mulai+' s/d '+response.detail.waktu_selesai);

		    if(response.detail.verified==null || response.detail.verified=='1') {
		    	$('#form_persetujuan').attr("action" ,"persetujuan");
			} else{
		    	$('#form_persetujuan').attr("action" ,"#");
		    }
        }
    });
}

function tampil_data_detail(element){
    $('#label_modal_np').text('Detail Pemesanan');
    $.ajax({
        url: "detail",
        type: "POST",
        dataType: "json",
        data: {no_pemesanan: element.dataset.no},
        success: function(response) {
		    $('.approval').hide();
		    $('.detail').show();

		    $('#detail_no_pemesanan').val(element.dataset.no);
		    $('#detail_np_pemesan').val(response.detail.np_pemesan);
		    $('#detail_nama_pemesan').val(response.detail.nama_pemesan);
		    $('#detail_snack').html(response.snack.daftar);
		    $('#detail_makanan').html(response.makanan.daftar);
		    $('#detail_minuman').html(response.minuman.daftar);
		    $('#detail_jenis_pemesanan').val(response.detail.jenis_pesanan);
		    $('#detail_ruangan').val(response.detail.nama_gedung+' - '+response.detail.nama_ruangan);
		    $('#detail_unit_kerja').val(response.detail.nama_unit_pemesan);
		    $('#detail_jumlah_pemesanan').val(response.detail.jumlah_pemesanan);
		    $('#detail_tgl_pemesanan').val(response.detail.tanggal_pemesanan);
		    $('#detail_waktu_pemesanan').val(response.detail.waktu_mulai+' s/d '+response.detail.waktu_selesai);
		    $('#detail_keterangan_verified').val(response.detail.keterangan_verified);
		    if(response.detail.verified==null) {
		    	$('#detail_verified').val('Menunggu Persetujuan Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+')');
		    } else if(response.detail.verified=='1') {
		    	$('#detail_verified').val('Disetujui Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan+'\nMenunggu Persetujuan Admin');
		    } else if(response.detail.verified=='2') {
		    	$('#detail_verified').val('Ditolak Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan);
		    } else if(response.detail.verified=='3') {
		    	$('#detail_verified').val('Disetujui Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan+'\nDisetujui Admin : '+response.detail.nama_verified+' ('+response.detail.np_verified+') pada '+response.detail.waktu_verified_admin);
		    } else if(response.detail.verified=='4') {
		    	$('#detail_verified').val('Disetujui Atasan : '+response.detail.nama_atasan+' ('+response.detail.np_atasan+') pada '+response.detail.waktu_verified_atasan+'\nDitolak Admin : '+response.detail.nama_verified+' ('+response.detail.np_verified+') pada '+response.detail.waktu_verified_admin);
			} else{
		    	$('#detail_verified').val('Waktu Pemesanan Ditolak!');
		    }
        }
    });
}
