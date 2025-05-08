function buat_unit_kerja_terpilih(arr_kode_unit_pilih){
	document.getElementById("daftar_pengadministrasi_unit_kerja").innerHTML = "";
	
	for(var i = 0;i<arr_kode_unit_pilih.length;i++){
		var admin_unit = "<span id='pilihan_"+arr_kode_unit_pilih[i]+"' class='alert alert-warning'>";

		if(document.getElementById("satuan_kerja_"+arr_kode_unit_pilih[i])!=null) admin_unit += document.getElementById("satuan_kerja_"+arr_kode_unit_pilih[i]).nextSibling.textContent;
			
		if(document.getElementById("akses_ubah_grup").value==1){
			admin_unit += " | <a href='#' id='hapus_"+arr_kode_unit_pilih[i]+"' onclick='hapus_pengadministrasi(this);' title='hapus'>x</a>";
		}
		admin_unit += "</span>";
		document.getElementById("daftar_pengadministrasi_unit_kerja").innerHTML += admin_unit;
	}
	
	document.getElementById("admin_unit_kerja_ubah").value = arr_kode_unit_pilih.join();
}

function cek_pengadministrasi(){
	if(document.getElementById("is_pilih_unit_kerja").value=="ya"){
		if(document.getElementById("admin_unit_kerja_ubah").value==""){
			document.getElementById("warning_pengadministrasi").innerHTML="Unit kerja harus dipilih.";
		}
		else{
			document.getElementById("warning_pengadministrasi").innerHTML="";
		}
	}
	else if(document.getElementById("is_pilih_unit_kerja").value=="tidak"){
		document.getElementById("warning_pengadministrasi").innerHTML="";
	}
	
	if(document.getElementById("warning_pengadministrasi").innerHTML==""){
		document.getElementById("formulir_ubah_grup").submit();
	}
}

function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["username"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_username").innerHTML = "Username harus diisi.";
	}
	else{
		document.getElementById("warning_username").innerHTML = "";
	}
	
	if(formulir["karyawan"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_karyawan").innerHTML = "Karyawan harus diisi.";
	}
	else{
		document.getElementById("warning_karyawan").innerHTML = "";
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

function cek_simpan_ubah() {
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["karyawan_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_karyawan_ubah").innerHTML = "Karyawan harus diisi.";
	}
	else{
		document.getElementById("warning_karyawan_ubah").innerHTML = "";
	}
	
	if(formulir["username_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_username_ubah").innerHTML = "Username harus diisi.";
	}
	else{
		document.getElementById("warning_username_ubah").innerHTML = "";
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

function grup(element){
	var username = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	processAjax("grup",username);
}

function hapus_pengadministrasi(element){
	document.getElementById(element.id.replace("hapus_","satuan_kerja_")).checked=false;
	
	var arr_admin_unit_kerja_ubah = document.getElementById("admin_unit_kerja_ubah").value.split(",");
	arr_admin_unit_kerja_ubah.splice(arr_admin_unit_kerja_ubah.indexOf(element.id.replace("hapus_","")),1);
	document.getElementById("admin_unit_kerja_ubah").value = arr_admin_unit_kerja_ubah.join(",");
	
	element.parentElement.parentElement.removeChild(element.parentElement);
}

function hasilGrup(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_grup").innerHTML = req.responseText;
		}
	}
}

function hasilSwitchTo(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			location.replace(window.location.href);
		}
	}
}

function hasilUnitKerja(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_unit_kerja").innerHTML = req.responseText;

			var arr_kode_unit_pilih = document.getElementById("admin_unit_kerja_ubah").value.split(",");
			
			for(var i=0;i<arr_kode_unit_pilih.length;i++){
				if(document.getElementById("satuan_kerja_"+arr_kode_unit_pilih[i])!=null) document.getElementById("satuan_kerja_"+arr_kode_unit_pilih[i]).checked=true;
			}
			
			buat_unit_kerja_terpilih(arr_kode_unit_pilih);
		}
	}
}

function pilih_unit_kerja(element){
	element.form.addEventListener("submit", function(event){
		event.preventDefault()
	});
	
	var form = element.form;
	processAjax("unit_kerja",form["username"].value);
}

function processAjax(report,params){
	if(report=="grup"){
		url = "administrator/ajax/pengguna_grup_pengguna/lihat/";
	}
	else if(report=="switch_to"){
		url = "login/user_switch/";
	}
	else if(report=="unit_kerja"){
		url = "administrator/ajax/pengguna_grup_pengguna/unit_kerja/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="grup"){//alert(url);
			req.onreadystatechange = hasilGrup;
		}
		else if(report=="switch_to"){
			req.onreadystatechange = hasilSwitchTo;
		}
		else if(report=="unit_kerja"){//alert(url);
			req.onreadystatechange = hasilUnitKerja;
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
			if(report=="grup"){
				req.onreadystatechange = hasilGrup;
			}
			else if(report=="switch_to"){
				req.onreadystatechange = hasilSwitchTo;
			}
			else if(report=="unit_kerja"){
				req.onreadystatechange = hasilUnitKerja;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}

function switch_to(username){
	processAjax("switch_to",username);
}

function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");

	formulir["karyawan"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["karyawan_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	
	formulir["username"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	formulir["username_ubah"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;

	if(element.parentNode.previousSibling.innerHTML=="Aktif"){
		document.getElementById("status_ubah_aktif").checked = true;
	}
	else if(element.parentNode.previousSibling.innerHTML=="Non Aktif"){
		document.getElementById("status_ubah_non_aktif").checked = true;
	}
	formulir["status"].value = element.parentNode.previousSibling.innerHTML.toLowerCase();
}

function tombol_pilih_unit_kerja(element){
	if(element.checked){
		document.getElementById("tombol_unit_kerja").style.display="inline";
		document.getElementById("is_pilih_unit_kerja").value="ya";
	}
	else{
		document.getElementById("tombol_unit_kerja").style.display="none";
		document.getElementById("is_pilih_unit_kerja").value="tidak";
	}
}

function unit_kerja_pilihan(element){
	if(element.checked){
		var arr_kode_unit_pilih = new Array();
		
		for(var i = 0;i<document.getElementById("daftar_pengadministrasi_unit_kerja").childNodes.length;i++){
			if(arr_kode_unit_pilih.indexOf(document.getElementById("daftar_pengadministrasi_unit_kerja").childNodes[i].id)==-1){
				arr_kode_unit_pilih.push(document.getElementById("daftar_pengadministrasi_unit_kerja").childNodes[i].id.replace("pilihan_",""));
			}
		}
		arr_kode_unit_pilih.push(element.value);
		
		arr_kode_unit_pilih.sort();
		
		buat_unit_kerja_terpilih(arr_kode_unit_pilih);		
	}
	else{
		hapus_pengadministrasi(document.getElementById(element.id.replace("satuan_kerja_","hapus_")));
	}
}

// heru menambahkan ini 2020-12-03 @04:46
function load_table_pengguna(){
    $('#tabel_pengguna').DataTable({
        destroy: true,
        "iDisplayLength": 10,
        "language": {
            "url": BASE_URL + "/asset/datatables/Indonesian.json",
            "sEmptyTable": "Tidak ada data di database",
            "emptyTable": "Tidak ada data di database"
        },
        "stateSave": true,
        "processing": true,
        "serverSide": true,
        "ordering": false,
        "ajax": {
            "url": BASE_URL + "/administrator/pengguna/tabel_data_pengguna",
            "type": "POST"
        },
        "columnDefs": [
            { 
                "targets": 'no-sort',
                "orderable": false,
            },
        ],
    });
}
// END heru menambahkan ini 2020-12-03 @04:46