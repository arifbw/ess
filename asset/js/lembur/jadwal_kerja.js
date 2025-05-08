

function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["nama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_nama").innerHTML = "Nama Jadwal Kerja harus diisi.";
	}
	else{
		document.getElementById("warning_nama").innerHTML = "";
	}
	
	if(formulir["formasi_gilir"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_formasi_gilir").innerHTML = "Formasi Gilir harus diisi.";
	}
	else{
		document.getElementById("warning_formasi_gilir").innerHTML = "";
	}

	if(formulir["hari"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_hari").innerHTML = "Hari Kerja / Libur harus diisi.";
	}
	else if(parseInt(formulir["hari"].value)==0){
		document.getElementById("warning_hari").innerHTML = "";

		if(formulir["jenis_gilir"].value == ""){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jenis_gilir").innerHTML = "Jenis gilir harus diisi.";
		}
		else{
			document.getElementById("warning_jenis_gilir").innerHTML = "";
		}
		
		if(formulir["jam_masuk"].value == ""){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jam_masuk").innerHTML = "Jam Masuk harus diisi.";
		}
		else{
			document.getElementById("warning_jam_masuk").innerHTML = "";
		}
		
		if(formulir["istirahat"].value == ""){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_istirahat").innerHTML = "Jenis Istirahat harus diisi.";
		}
		else{
			document.getElementById("warning_istirahat").innerHTML = "";

			if(formulir["istirahat"].value == "terjadwal"){				
				if(formulir["jam_mulai_istirahat"].value == ""){
					if(lanjut){
						lanjut = false;
					}
					document.getElementById("warning_jam_mulai_istirahat").innerHTML = "Jam Mulai Istirahat harus diisi.";
				}
				else if(formulir["jam_mulai_istirahat"].value < formulir["jam_masuk"].value && formulir["lintas_hari_masuk"].checked==formulir["lintas_hari_mulai_istirahat"].checked){
					if(lanjut){
						lanjut = false;
					}
					document.getElementById("warning_jam_mulai_istirahat").innerHTML = "Jam Mulai Istirahat tidak boleh sebelum Jam Datang";
				}
				else{
					document.getElementById("warning_jam_mulai_istirahat").innerHTML = "";
				}
				
				if(formulir["jam_akhir_istirahat"].value == ""){
					if(lanjut){
						lanjut = false;
					}
					document.getElementById("warning_jam_akhir_istirahat").innerHTML = "Jam Akhir Istirahat harus diisi.";
				}
				else if(formulir["jam_akhir_istirahat"].value < formulir["jam_mulai_istirahat"].value && formulir["lintas_hari_mulai_istirahat"].checked==formulir["lintas_hari_akhir_istirahat"].checked){
					if(lanjut){
						lanjut = false;
					}
					document.getElementById("warning_jam_akhir_istirahat").innerHTML = "Jam Akhir Istirahat tidak boleh sebelum Jam Mulai Istirahat";
				}
				else{
					document.getElementById("warning_jam_akhir_istirahat").innerHTML = "";
				}
			}
		}
		
		if(formulir["jam_pulang"].value == ""){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jam_pulang").innerHTML = "Jam Pulang harus diisi.";
		}
		else if(formulir["jam_pulang"].value < formulir["jam_masuk"].value && formulir["lintas_hari_masuk"].checked==formulir["lintas_hari_pulang"].checked){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jam_pulang").innerHTML = "Jam Pulang tidak boleh sebelum Jam Datang";
		}
		else if(formulir["jam_pulang"].value < formulir["jam_akhir_istirahat"].value && formulir["lintas_hari_akhir_istirahat"].checked==formulir["lintas_hari_pulang"].checked){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jam_pulang").innerHTML = "Jam Pulang tidak boleh sebelum Jam Akhir Istirahat";
		}
		else{
			document.getElementById("warning_jam_pulang").innerHTML = "";
		}
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
		document.getElementById("warning_nama_ubah").innerHTML = "Nama Jadwal Kerja harus diisi.";
	}
	else{
		document.getElementById("warning_nama_ubah").innerHTML = "";
	}

	if(formulir["hari_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_hari_ubah").innerHTML = "Hari Kerja / Libur harus diisi.";
	}
	else if(parseInt(formulir["hari_ubah"].value)==0){
		document.getElementById("warning_hari_ubah").innerHTML = "";

		if(formulir["gilir_ubah"].value == ""){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_gilir_ubah").innerHTML = "Gilir harus diisi.";
		}
		else{
			document.getElementById("warning_gilir_ubah").innerHTML = "";
		}
		
		if(formulir["jam_masuk_ubah"].value == ""){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jam_masuk_ubah").innerHTML = "Jam Masuk harus diisi.";
		}
		else{
			document.getElementById("warning_jam_masuk_ubah").innerHTML = "";
		}
		
		if(formulir["istirahat_ubah"].value == ""){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_istirahat_ubah").innerHTML = "Jenis Istirahat harus diisi.";
		}
		else{
			document.getElementById("warning_istirahat_ubah").innerHTML = "";

			if(formulir["istirahat_ubah"].value == "terjadwal"){				
				if(formulir["jam_mulai_istirahat_ubah"].value == ""){
					if(lanjut){
						lanjut = false;
					}
					document.getElementById("warning_jam_mulai_istirahat_ubah").innerHTML = "Jam Mulai Istirahat harus diisi.";
				}
				else if(formulir["jam_mulai_istirahat_ubah"].value < formulir["jam_masuk_ubah"].value && formulir["lintas_hari_masuk_ubah"].checked==formulir["lintas_hari_mulai_istirahat_ubah"].checked){
					if(lanjut){
						lanjut = false;
					}
					document.getElementById("warning_jam_mulai_istirahat_ubah").innerHTML = "Jam Mulai Istirahat tidak boleh sebelum Jam Datang";
				}
				else{
					document.getElementById("warning_jam_mulai_istirahat_ubah").innerHTML = "";
				}
				
				if(formulir["jam_akhir_istirahat_ubah"].value == ""){
					if(lanjut){
						lanjut = false;
					}
					document.getElementById("warning_jam_akhir_istirahat_ubah").innerHTML = "Jam Akhir Istirahat harus diisi.";
				}
				else if(formulir["jam_akhir_istirahat_ubah"].value < formulir["jam_mulai_istirahat_ubah"].value && formulir["lintas_hari_mulai_istirahat_ubah"].checked==formulir["lintas_hari_akhir_istirahat_ubah"].checked){
					if(lanjut){
						lanjut = false;
					}
					document.getElementById("warning_jam_akhir_istirahat_ubah").innerHTML = "Jam Akhir Istirahat tidak boleh sebelum Jam Mulai Istirahat";
				}
				else{
					document.getElementById("warning_jam_akhir_istirahat_ubah").innerHTML = "";
				}
			}
		}
		
		if(formulir["jam_pulang_ubah"].value == ""){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jam_pulang_ubah").innerHTML = "Jam Pulang harus diisi.";
		}
		else if(formulir["jam_pulang_ubah"].value < formulir["jam_masuk_ubah"].value && formulir["lintas_hari_masuk_ubah"].checked==formulir["lintas_hari_pulang_ubah"].checked){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jam_pulang_ubah").innerHTML = "Jam Pulang tidak boleh sebelum Jam Datang";
		}
		else if(formulir["jam_pulang_ubah"].value < formulir["jam_akhir_istirahat_ubah"].value && formulir["lintas_hari_akhir_istirahat_ubah"].checked==formulir["lintas_hari_pulang_ubah"].checked){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_jam_pulang_ubah").innerHTML = "Jam Pulang tidak boleh sebelum Jam Akhir Istirahat";
		}
		else{
			document.getElementById("warning_jam_pulang_ubah").innerHTML = "";
		}
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
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="salin_tambah"){
			req.onreadystatechange = hasilSalinTambah;
		}
		else if(report=="ubah"){
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