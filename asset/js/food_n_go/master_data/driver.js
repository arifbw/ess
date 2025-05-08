function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["np_karyawan"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_np_karyawan").innerHTML = "NP harus diisi.";
	}
	else{
		document.getElementById("warning_np_karyawan").innerHTML = "";
	}
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}
	
	if(formulir["jenis_sim"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_jenis_sim").innerHTML = "Jenis SIM harus diisi.";
	}
	else{
		document.getElementById("warning_jenis_sim").innerHTML = "";
	}
		
	return lanjut;
}

function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["uraian_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_uraian_ubah").innerHTML = "Uraian harus diisi.";
	}
	else{
		document.getElementById("warning_uraian_ubah").innerHTML = "";
	}
	
	if(formulir["kode_erp_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kode_erp_ubah").innerHTML = "Kode ERP harus diisi.";
	}
	else{
		document.getElementById("warning_kode_erp_ubah").innerHTML = "";
	}
	
	return lanjut;
}

function tampil_data_ubah(element){
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
}

var opt_prev = '';
function tampil_data_ubah_new(element){
    var formulir = document.getElementById("formulir_ubah");
    $('#id_ubah').val(element.dataset.id);
    $('#np_karyawan_old').val(element.dataset.np_karyawan);
    $('#np_karyawan_ubah').val(element.dataset.np_karyawan);
    $('#nama_old').val(element.dataset.nama);
    $('#nama_ubah').val(element.dataset.nama);
    $('#no_hp_old').val(element.dataset.no_hp);
    $('#no_hp_ubah').val(element.dataset.no_hp);
    $('#jenis_sim_old').val(element.dataset.jenis_sim);
    $('#jenis_sim_ubah').val(element.dataset.jenis_sim);
    $('#posisi_old').val(element.dataset.posisi);
    $('#posisi_ubah').val(element.dataset.posisi);
    $('#keterangan_old').val(element.dataset.keterangan);
    $('#keterangan_ubah').val(element.dataset.keterangan);
    
    var id_mst_kendaraan = element.dataset.id_mst_kendaraan;
    var nopol = element.dataset.nopol;
    var kendaraan = element.dataset.kendaraan;
    var select = document.getElementById("id_mst_kendaraan_default_ubah");
    var length = select.options.length;
    for (i = length-1; i >= 0; i--) {
        if(select.options[i].getAttribute('value')==opt_prev){
            select.options[i] = null;
        }
    }
    if(id_mst_kendaraan!=''){
        $('#id_mst_kendaraan_default_ubah').val(id_mst_kendaraan);
        $('#id_mst_kendaraan_default_ubah').prepend('<option value="'+id_mst_kendaraan+'">'+nopol+' - '+kendaraan+'</option>');
        $('#id_mst_kendaraan_default_ubah').trigger('change');
        
    }
    
    $('#status_old').val(element.dataset.status);
    if(element.dataset.status==1){
		document.getElementById("status_ubah_aktif").checked = true;
	} else{
        document.getElementById("status_ubah_non_aktif").checked = true;
    }
	
    $('#modal_ubah').modal('show');
    opt_prev=id_mst_kendaraan;
}