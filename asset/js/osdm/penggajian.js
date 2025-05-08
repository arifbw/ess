function cek_simpan_ubah(){
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["waktu_publikasi"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_tanggal_publikasi").innerHTML = "Tanggal publikasi harus diisi.";
	}
	else{
		document.getElementById("warning_tanggal_publikasi").innerHTML = "";
	}
	
	return lanjut;
}

function semua_karyawan(element){
	document.getElementById("tombol_pilih_karyawan").style.display="none";
	document.getElementById("pilih_kontrak_kerja_cetak_gaji").innerHTML="";
	document.getElementById("pilihan_pilih_kontrak_kerja").value="";
	if(document.getElementById("value_pilihan_cetak").value==element.value){
		element.checked=false;
		document.getElementById("value_pilihan_cetak").value="";
		document.getElementById("np_karyawan").value="";
	}
	else{
		document.getElementById("value_pilihan_cetak").value=element.value;
		document.getElementById("pilihan_pilih_unit_kerja").value="";
		processAjax(element.value,document.getElementById("id_header").value);
	}
}

function pilih_karyawan(element){
	document.getElementById("np_karyawan").value="";
	if(document.getElementById("value_pilihan_cetak").value==element.value){
		element.checked=false;
		document.getElementById("tombol_pilih_karyawan").style.display="none";
		document.getElementById("value_pilihan_cetak").value="";
		document.getElementById("tombol_pilih_karyawan").style.display="none";
		document.getElementById("pilih_kontrak_kerja_cetak_gaji").innerHTML="";
		document.getElementById("pilihan_pilih_kontrak_kerja").value="";
	}
	else{
		document.getElementById("value_pilihan_cetak").value=element.value;
		document.getElementById("tombol_pilih_karyawan").style.display="inline";
		document.getElementById("pilihan_pilih_unit_kerja").value="";
	}
}

function hasil_semua_karyawan(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("np_karyawan").value = req.responseText;
		}
	}
}

function hasil_pilih_karyawan(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_tombol_pilih_karyawan").innerHTML = req.responseText;
		}
	}
	else{
		document.getElementById("isi_modal_tombol_pilih_karyawan").innerHTML = "mohon tunggu sejenak";
	}
}

function hasil_filter_karyawan(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			var hasil = req.responseText.split("|");
			document.getElementById("banyak_karyawan").innerHTML = hasil[0];
			document.getElementById("np_karyawan").value = hasil[1];
			document.getElementById("karyawan_terpilih").innerHTML = hasil[2];
		}
	}
}

function processAjax(report,params=""){
	if(report=="semua karyawan"){
		url = "osdm/penggajian/semua_karyawan/";
	}
	else if(report=="pilih karyawan"){
		url = "osdm/penggajian/pilih_karyawan/";
	}
	else if(report=="filter karyawan"){
		url = "osdm/penggajian/filter_karyawan/";
	}
	else{
		url = "";
	}
	
	var data = new FormData();
	if(report=="pilih karyawan" || report=="filter karyawan"){
		data.append("kontrak_kerja_terpilih", document.getElementById("pilihan_pilih_kontrak_kerja").value);
		data.append("unit_kerja_terpilih", document.getElementById("pilihan_pilih_unit_kerja").value);
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		
		if(report=="semua karyawan"){
			req.onreadystatechange = hasil_semua_karyawan;
		}
		else if(report=="pilih karyawan"){			
			req.onreadystatechange = hasil_pilih_karyawan;
		}
		else if(report=="filter karyawan"){			
			req.onreadystatechange = hasil_filter_karyawan;
		}
		
		try {
			req.open("POST", url, true);
		}
		catch (e) {
			alert(e);
		}
		req.send(data);
	}
	else if (window.ActiveXObject) { // IE
		req = new ActiveXObject("Microsoft.XMLHTTP");
		
		if (req) {			
			if(report=="semua karyawan"){
				req.onreadystatechange = hasil_karyawan;
			}
			else if(report=="pilih karyawan"){			
				req.onreadystatechange = hasil_pilih_karyawan;
			}
			else if(report=="filter karyawan"){
				req.onreadystatechange = hasil_filter_karyawan;
			}
			
			req.open("GET", url, true);
			req.send(data);
		}
	}
}

function cetak(np_karyawan){	
	document.getElementById("np_karyawan").value = np_karyawan;
	document.getElementById("form_cetak").submit();
}

function modal_pilih_karyawan(){
	//processAjax("pilih karyawan",document.getElementById("pilihan_pilih_kontrak_kerja").value.replace(/,/g,"-")+"="+document.getElementById("pilihan_pilih_unit_kerja").value.replace(/,/g,"-"));
	processAjax("pilih karyawan");
}

function pilih_kontrak_kerja_pilihan(element){
	var arr_kontrak_kerja_pilih  = new Array();
	
	if(document.getElementById("pilihan_pilih_kontrak_kerja").value.length>0){
		arr_kontrak_kerja_pilih  = document.getElementById("pilihan_pilih_kontrak_kerja").value.split(",");
	}

	if(element.checked){
		arr_kontrak_kerja_pilih.push(element.value);		
	}
	else{
		arr_kontrak_kerja_pilih.splice(arr_kontrak_kerja_pilih.indexOf(element.value),1);
	}
	arr_kontrak_kerja_pilih.sort();
	
	document.getElementById("pilihan_pilih_kontrak_kerja").value = arr_kontrak_kerja_pilih.join(",");
	
	buat_kontrak_kerja_pilihan();
}

function buat_kontrak_kerja_pilihan(){
	var arr_kontrak_kerja_pilih = new Array();

	if(document.getElementById("pilihan_pilih_kontrak_kerja").value.length>0){
		arr_kontrak_kerja_pilih = document.getElementById("pilihan_pilih_kontrak_kerja").value.split(",");
	}
	
	document.getElementById("pilih_kontrak_kerja_cetak_gaji").innerHTML = "";

	tombol_pilih();

	if(arr_kontrak_kerja_pilih.length>0){
		document.getElementById("pilihan_cetak_pilih_karyawan").checked = true;
		document.getElementById("tombol_pilih_karyawan").style.display="";
		for(var i = 0;i<arr_kontrak_kerja_pilih.length;i++){
			var kontrak_kerja = "<span id='pilihan_"+arr_kontrak_kerja_pilih[i]+"' class='alert alert-info'>";
				kontrak_kerja += arr_kontrak_kerja_pilih[i]+" | <a href='#' id='hapus_"+arr_kontrak_kerja_pilih[i]+"' onclick='hapus_kontrak_kerja_pilihan(this);' title='hapus'>x</a>";
			kontrak_kerja += "</span> ";
			document.getElementById("pilih_kontrak_kerja_cetak_gaji").innerHTML += kontrak_kerja;
		}
	}
	
	document.getElementById("banyak_kontrak_kerja").innerHTML = arr_kontrak_kerja_pilih.length;
	
	processAjax("filter karyawan");
}

function hapus_kontrak_kerja_pilihan(element){
	var arr_kode_unit_pilih = new Array();
	
	if(document.getElementById("pilihan_pilih_kontrak_kerja").value.length>0){
		arr_kode_unit_pilih = document.getElementById("pilihan_pilih_kontrak_kerja").value.split(",");
	}
	
	arr_kode_unit_pilih.splice(arr_kode_unit_pilih.indexOf(element.id.replace("hapus_","")),1);
	
	document.getElementById("pilihan_pilih_kontrak_kerja").value = arr_kode_unit_pilih.join(",");
	
	buat_kontrak_kerja_pilihan();
}

function pilih_unit_kerja_pilihan(element){
	var arr_unit_kerja_pilih  = new Array();
	
	if(document.getElementById("pilihan_pilih_unit_kerja").value.length>0){
		arr_unit_kerja_pilih  = document.getElementById("pilihan_pilih_unit_kerja").value.split(",");
	}

	if(element.checked){
		arr_unit_kerja_pilih.push(element.value);		
	}
	else{
		arr_unit_kerja_pilih.splice(arr_unit_kerja_pilih.indexOf(element.value),1);
	}
	arr_unit_kerja_pilih.sort();
	
	document.getElementById("pilihan_pilih_unit_kerja").value = arr_unit_kerja_pilih.join(",");
	
	buat_unit_kerja_pilihan();
}

function buat_unit_kerja_pilihan(){
	var arr_unit_kerja_pilih = new Array();

	if(document.getElementById("pilihan_pilih_unit_kerja").value.length>0){
		arr_unit_kerja_pilih = document.getElementById("pilihan_pilih_unit_kerja").value.split(",");
	}
	
	document.getElementById("pilih_unit_kerja_cetak_gaji").innerHTML = "";

	tombol_pilih();

	if(arr_unit_kerja_pilih.length>0){
		document.getElementById("pilihan_cetak_pilih_karyawan").checked = true;
		document.getElementById("tombol_pilih_karyawan").style.display="";
		for(var i = 0;i<arr_unit_kerja_pilih.length;i++){
			var unit_kerja = "<span id='pilihan_"+arr_unit_kerja_pilih[i]+"' class='alert alert-success'>";
				unit_kerja += arr_unit_kerja_pilih[i]+" | <a href='#' id='hapus_"+arr_unit_kerja_pilih[i]+"' onclick='hapus_unit_kerja_pilihan(this);' title='hapus'>x</a>";
			unit_kerja += "</span> ";
			document.getElementById("pilih_unit_kerja_cetak_gaji").innerHTML += unit_kerja;
		}
	}
	
	document.getElementById("banyak_unit_kerja").innerHTML = arr_unit_kerja_pilih.length;
	
	processAjax("filter karyawan");
}

function hapus_unit_kerja_pilihan(element){
	var arr_kode_unit_pilih = new Array();
	
	if(document.getElementById("pilihan_pilih_unit_kerja").value.length>0){
		arr_kode_unit_pilih = document.getElementById("pilihan_pilih_unit_kerja").value.split(",");
	}
	
	arr_kode_unit_pilih.splice(arr_kode_unit_pilih.indexOf(element.id.replace("hapus_","")),1);
	
	document.getElementById("pilihan_pilih_unit_kerja").value = arr_kode_unit_pilih.join(",");
	
	buat_unit_kerja_pilihan();
}


function tombol_pilih(){
	var arr_kontrak_kerja_pilih = new Array();
	var arr_unit_kerja_pilih = new Array();

	if(document.getElementById("pilihan_pilih_kontrak_kerja").value.length>0){
		arr_kontrak_kerja_pilih = document.getElementById("pilihan_pilih_kontrak_kerja").value.split(",");
	}
	if(document.getElementById("pilihan_pilih_unit_kerja").value.length>0){
		arr_unit_kerja_pilih = document.getElementById("pilihan_pilih_unit_kerja").value.split(",");
	}
	
	if(arr_kontrak_kerja_pilih.length==0 && arr_unit_kerja_pilih.length==0){
		document.getElementById("pilihan_cetak_pilih_karyawan").checked = false;
		document.getElementById("tombol_pilih_karyawan").style.display="none";
		document.getElementById("value_pilihan_cetak").value="";
	}
	else{
		document.getElementById("pilihan_cetak_pilih_karyawan").checked = true;
		document.getElementById("tombol_pilih_karyawan").style.display="";
	}
}