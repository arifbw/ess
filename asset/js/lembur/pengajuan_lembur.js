

function cek_simpan_ubah(){
	//console.log('a');
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["no_pokok_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_no_pokok_ubah").innerHTML = "Nomor pokok pegawai harus diisi.";
	}
	else{
		document.getElementById("warning_no_pokok_ubah").innerHTML = "";
	}

	if(formulir["nama_pegawai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama_pegawai_ubah").innerHTML = "Nama pegawai lembur harus diisi.";
	}
	else{
		document.getElementById("warning_nama_pegawai_ubah").innerHTML = "";
	}

	if(formulir["tgl_mulai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_tgl_mulai_ubah").innerHTML = "Tanggal mulai lembur harus diisi.";
	}
	else{
		document.getElementById("warning_tgl_mulai_ubah").innerHTML = "";
	}

	if(formulir["tgl_selesai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_tgl_selesai_ubah").innerHTML = "Tanggal selesai lembur harus diisi.";
	}
	else{
		document.getElementById("warning_tgl_selesai_ubah").innerHTML = "";
	}

	if(formulir["jam_mulai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_jam_mulai_ubah").innerHTML = "Jam mulai lembur harus diisi.";
	}
	else{
		document.getElementById("warning_jam_mulai_ubah").innerHTML = "";
	}

	if(formulir["jam_selesai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_jam_selesai_ubah").innerHTML = "Jam selesai lembur harus diisi.";
	}
	else{
		document.getElementById("warning_jam_selesai_ubah").innerHTML = "";
	}

	if(formulir["tgl_dws_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_tgl_dws_ubah").innerHTML = "Tertanggal lembur harus diisi.";
	}
	else{
		document.getElementById("warning_tgl_dws_ubah").innerHTML = "";
	}

	if(formulir["np_approver_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_np_approver_ubah").innerHTML = "Nomor pokok approver lembur harus diisi.";
	}
	else{
		document.getElementById("warning_np_approver_ubah").innerHTML = "";
	}

	if(formulir["nama_approver_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama_approver_ubah").innerHTML = "Nama approver lembur harus diisi.";
	}
	else{
		document.getElementById("warning_nama_approver_ubah").innerHTML = "";
	}
	return lanjut;
}

function copyToRow(){
	var coloumnHeader = $('#sel1').val();
	var copyValue = $('#copyValue').val();

	if (coloumnHeader == 'NP'){
		$('.no_pokok').each(function(index,item){
			if(item.value.trim() == ''){
				this.value = copyValue;
				getNama((item.id).replace("no_pokok",""));
			}
		});
	}else if (coloumnHeader == 'NP Approver'){
		$('.no_pokok').each(function(index,item){
			if(item.value.trim() == ''){
				this.value = copyValue;
				getNama((item.id).replace("np_approver",""));
			}
		});
	}else if (coloumnHeader == 'Tanggal Mulai'){
		$('.tgl_mulai').each(function(index,item){
			if(item.value.trim() == ''){
				this.value = copyValue;
			}
		});
	}else if (coloumnHeader == 'Tanggal selesai'){
		$('.tgl_selesai').each(function(index,item){
			if(item.value.trim() == ''){
				this.value = copyValue;
			}
		});
	}else if (coloumnHeader == 'Jam Mulai'){
		$('.jam_mulai').each(function(index,item){
			if(item.value.trim() == ''){
				this.value = copyValue;
			}
		});
	}else if (coloumnHeader == 'Jam Selesai'){
		$('.jam_selesai').each(function(index,item){
			if(item.value.trim() == ''){
				this.value = copyValue;
			}
		});
	}

}

function getNama(){
	var no_pokok = $('.no_pokok').val();
	//console.log("<?= base_url('lembur/pengajuan_lembur/ajax_getNama') ?>");
	url_ = "lembur/pengajuan_lembur/ajax_getNama";
	$.ajax({
	 type: "POST",
	 dataType: "html",
	 url: document.getElementById("base_url").value+url_,
	 data: "vno_pokok="+no_pokok,
		success: function(msg){
			if(msg == ''){
				alert ('Silahkan isi No. Pokok Dengan Benar.');
			}else{							 
				$('#nama_pegawai').val(msg);
			}													  
		 }
	 });    
} 

function getNamaApv(){
	var no_pokok = $('#np_approver').val();
	
	url_ = "lembur/pengajuan_lembur/ajax_getNama";
	$.ajax({
     type: "POST",
     dataType: "html",
     url: document.getElementById("base_url").value+url_,
     data: "vno_pokok="+no_pokok,
		success: function(msg){
			if(msg == ''){
				alert ('Silahkan isi No. Pokok Dengan Benar.');
			}else{							 
				$('#nama_approver').val(msg);
			}													  
		 }
	 });       
}
		
function simpan(){
	var totalRow = Number($('#maxIndexTable').val());
	jsonObj = [];
	var i;
	for (i=1; i <= totalRow; i++) {
		item ={}
		item["no_pokok"]		=$('#no_pokok'+i).val();
		item["approval_pimpinan_np"]		=$('#np_approver'+i).val();
		item["nama"]			=$('#nama'+i).val();
		item["tgl_mulai"]		=$('#tgl_mulai'+i).val();
		item["tgl_selesai"]		=$('#tgl_selesai'+i).val();
		item["jamMulai"]		=$('#jamMulai'+i).val();
		item["jamSelesai"]		=$('#jamSelesai'+i).val();
		item["ket"]				=$('#ket'+i).val();
		
		jsonObj.push(item);
	}
}

function hasilSalinTambah(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			var arr_hasil = req.responseText.split("|");

			var formulir = document.getElementById("formulir_tambah");
			
			formulir["hari"].value = parseInt(arr_hasil[2]);
			
			for(var i=0;i<formulir["hari"].length;i++){
				if(formulir["hari"][i].checked){
					hari_kerja_libur(formulir["hari"][i]);
					i=formulir["hari"].length;
				}
			}
			
			formulir["formasi_gilir"].value = arr_hasil[3];
			
			if(parseInt(arr_hasil[4])==1){
				formulir["lintas_hari_masuk"].checked = true;
			}
			else if(parseInt(arr_hasil[4])==0){
				formulir["lintas_hari_masuk"].checked = false;
			}
			
			formulir["jam_masuk"].value = arr_hasil[5].substr(0,5);
			
			formulir["istirahat"].value = arr_hasil[6];
			waktu_istirahat(formulir["istirahat"]);
			
			if(parseInt(arr_hasil[7])==1){
				formulir["lintas_hari_mulai_istirahat"].checked = true;
			}
			else if(parseInt(arr_hasil[7])==0){
				formulir["lintas_hari_mulai_istirahat"].checked = false;
			}
			
			formulir["jam_mulai_istirahat"].value = arr_hasil[8].substr(0,5);
			
			if(parseInt(arr_hasil[9])==1){
				formulir["lintas_hari_akhir_istirahat"].checked = true;
			}
			else if(parseInt(arr_hasil[9])==0){
				formulir["lintas_hari_akhir_istirahat"].checked = false;
			}
			
			formulir["jam_akhir_istirahat"].value = arr_hasil[10].substr(0,5);
			
			if(parseInt(arr_hasil[11])==1){
				formulir["lintas_hari_pulang"].checked = true;
			}
			else if(parseInt(arr_hasil[11])==0){
				formulir["lintas_hari_pulang"].checked = false;
			}
			
			formulir["jam_pulang"].value = arr_hasil[12].substr(0,5);
			
			formulir["kode_erp"].value = arr_hasil[13];
			
			if(arr_hasil[14]=="A"){
				formulir["varian"].checked=true;
			}
			else{
				formulir["varian"].checked=false;
			}
			
			if(parseInt(arr_hasil[15])==1){
				formulir["status"].value = "aktif";
			}
			else if(parseInt(arr_hasil[15])==0){
				formulir["status"].value = "non aktif";
			}
		}
	}
}

function hasilUbah(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isian_ubah_lembur").innerHTML = req.responseText;
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
	if(report=="salin_tambah"){
		url = "lembur/ajax/pengajuan_lembur/salin/";
	}
	else if(report=="ubah"){
		url = "lembur/ajax/pengajuan_lembur/ubah/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	$('.select2').select2();
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="salin_tambah"){
			req.onreadystatechange = hasilSalinTambah;
			update='';
		}
		else if(report=="ubah"){
			req.onreadystatechange = hasilUbah;
			update='ubah';
		}
		
		try {
			req.open("GET", url, true);
		}
		catch (e) {
			alert(e);
		}
		req.send(null);
		// getPilihanAtasanLembur(update);
	}
	else if (window.ActiveXObject) { // IE
		req = new ActiveXObject("Microsoft.XMLHTTP");
		if (req) {
			if(report=="salin_tambah"){
				req.onreadystatechange = hasilSalinTambah;
			}
			else if(report=="salin_ubah"){
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

function salin_tambah(element){
	processAjax("salin_tambah",element.value);
}

function ubah(element){
	var id_pengajuan_lembur = element.previousSibling.value;
	processAjax("ubah",id_pengajuan_lembur);
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
