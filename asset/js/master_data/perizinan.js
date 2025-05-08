function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama Perizinan harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}

	if(formulir["keterangan"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_keterangan").innerHTML = "Keterangan harus diisi.";
	}
	else{
		document.getElementById("warning_keterangan").innerHTML = "";
	}
	
	if(formulir["kode_pamlek"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kode_pamlek").innerHTML = "Kode Pamlek harus diisi.";
	}
	else{
		document.getElementById("warning_kode_pamlek").innerHTML = "";
	}
	
	if(formulir["kode_erp"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kode_erp").innerHTML = "Kode ERP harus diisi.";
	}
	else{
		document.getElementById("warning_kode_erp").innerHTML = "";
	}

	if(formulir["status"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_status").innerHTML = "Status harus diisi.";
	}
	else{
		document.getElementById("warning_status").innerHTML = "";
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
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Perizinan harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}
	
	if(formulir["keterangan_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_keterangan_ubah").innerHTML = "Keterangan harus diisi.";
	}
	else{
		document.getElementById("warning_keterangan_ubah").innerHTML = "";
	}

	if(formulir["kode_pamlek_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_kode_pamlek_ubah").innerHTML = "Kode Pamlek harus diisi.";
	}
	else{
		document.getElementById("warning_kode_pamlek_ubah").innerHTML = "";
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

	if(formulir["status_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_status_ubah").innerHTML = "Status harus diisi.";
	}
	else{
		document.getElementById("warning_status_ubah").innerHTML = "";
	}
	
	return lanjut;
}

function hari_kerja_libur(element){
	var formulir = element.form;
	
	if(formulir.id=="formulir_tambah"){
		var id_waktu_hari_kerja = "waktu_hari_kerja";
	}
	else if(formulir.id=="formulir_ubah"){
		var id_waktu_hari_kerja = "waktu_hari_kerja_ubah";
	}
	
	if(element.value=="0"){ // kerja
		document.getElementById(id_waktu_hari_kerja).style.display="inline";
	}
	else if(element.value=="1"){ // libur
		document.getElementById(id_waktu_hari_kerja).style.display="none";
		formulir["lintas_hari_masuk"].checked = false;
		formulir["lintas_hari_mulai_istirahat"].checked = false;
		formulir["lintas_hari_akhir_istirahat"].checked = false;
		formulir["lintas_hari_pulang"].checked = false;
		document.getElementById("istirahat_terjadwal").checked = false;
		document.getElementById("istirahat_bergantian").checked = false;
		
		formulir["jam_mulai_istirahat"].value = "";
		formulir["jam_akhir_istirahat"].value = "";

	}
}

function hasilUbah(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isian_ubah_jadwal").innerHTML = req.responseText;
		}
	}
}

function icon_pilihan(element){
	if(element.checked){
		document.getElementById("pilihan_icon").value=element.value;
	}
}

function lintas_hari(element){
	var formulir = element.form;
	var ubah = "";
	
	if(formulir.id.substr(9,4)=="ubah"){
		ubah="_ubah";
	}

	if(element.name=="lintas_hari_masuk"+ubah){
		if(element.checked){
			formulir["lintas_hari_mulai_istirahat"+ubah].checked = true;
			formulir["lintas_hari_akhir_istirahat"+ubah].checked = true;
			formulir["lintas_hari_pulang"+ubah].checked = true;
		}
	}
	else if(element.name=="lintas_hari_mulai_istirahat"+ubah){
		if(element.checked){
			formulir["lintas_hari_akhir_istirahat"+ubah].checked = true;
			formulir["lintas_hari_pulang"+ubah].checked = true;
		}
		else{
			formulir["lintas_hari_masuk"+ubah].checked = false;
		}
	}
	else if(element.name=="lintas_hari_akhir_istirahat"+ubah){
		if(element.checked){
			formulir["lintas_hari_pulang"+ubah].checked = true;
		}
		else{
			formulir["lintas_hari_masuk"+ubah].checked = false;
			formulir["lintas_hari_mulai_istirahat"+ubah].checked = false;
		}
	}
	else if(element.name=="lintas_hari_pulang"+ubah){
		if(!element.checked){
			formulir["lintas_hari_masuk"+ubah].checked = false;
			formulir["lintas_hari_mulai_istirahat"+ubah].checked = false;
			formulir["lintas_hari_akhir_istirahat"+ubah].checked = false;
		}
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

function processAjax(report,params){
	if(report=="ubah"){
		url = "master_data/ajax/perizinan/ubah/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="ubah"){
			req.onreadystatechange = hasilUbah;
		}
		
		try {
			req.open("GET", url, true);
		}
		catch (e) {
			alert(e);
		}
		req.send(null);
	}
	else if (window.ActiveXObject) { // IE
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) {
			if(report=="salin_ubah"){
				req.onreadystatechange = hasilUbah;
			}
			
			req.open("GET", url, true);
			req.send();
		}
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

	formulir["nama"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["nama_ubah"].value = formulir["nama"].value;
	
	formulir["keterangan"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["keterangan_ubah"].value = formulir["keterangan"].value;
	
	formulir["kode_pamlek"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["kode_pamlek_ubah"].value = formulir["kode_pamlek"].value;
	
	formulir["kode_erp"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["kode_erp_ubah"].value = formulir["kode_erp"].value;
	
	if(element.parentNode.previousSibling.innerHTML=="Aktif"){
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if(element.parentNode.previousSibling.innerHTML=="Non Aktif"){
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
}

function waktu_istirahat(element){
	var formulir = document.getElementById("formulir_tambah");
	
	if(element.value=="bergantian"){
		document.getElementById("waktu_istirahat").style.display="none";
		
		formulir["lintas_hari_mulai_istirahat"].checked = false;
		formulir["lintas_hari_akhir_istirahat"].checked = false;
		formulir["jam_mulai_istirahat"].value = "";
		formulir["jam_akhir_istirahat"].value = "";
		
	}
	else if(element.value=="terjadwal"){
		document.getElementById("waktu_istirahat").style.display="inline";
		
		if(formulir["lintas_hari_masuk"].checked){
			formulir["lintas_hari_mulai_istirahat"].checked = true;
			formulir["lintas_hari_akhir_istirahat"].checked = true;
		}
		else if(!formulir["lintas_hari_pulang"].checked){
			formulir["lintas_hari_mulai_istirahat"].checked = false;
			formulir["lintas_hari_akhir_istirahat"].checked = false;
		}
	}
}