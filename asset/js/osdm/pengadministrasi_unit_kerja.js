function cekPengadministrasi(element){
	var unit_kerja = document.getElementsByName("unit_kerja");
	if(element.checked){
		for(i=0;i<unit_kerja.length;i++){
			unit_kerja[i].disabled=false;
		}
		
		document.getElementById("is_pilih_unit_kerja").value="ya";
	}
	else{
		for(i=0;i<unit_kerja.length;i++){
			if(unit_kerja[i].checked){
				unit_kerja[i].checked=false;
				unit_kerja_pilihan(unit_kerja[i]);
			}
			unit_kerja[i].disabled=true;
		}
		document.getElementById("is_pilih_unit_kerja").value="tidak";
	}
}

function hasilPengadministrasi(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_pengadministrasi").innerHTML = req.responseText;
		}
	}
}

function pengadministrasi(element){
	var username = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	document.getElementById("username_pengadministrasi").innerHTML = username;
	processAjaxPengadministrasi("pengadministrasi",username);
}

function processAjaxPengadministrasi(report,params){
	if(report=="pengadministrasi"){
		url = "osdm/ajax/pengadministrasi_unit_kerja/pengadministrasi/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="pengadministrasi"){
			req.onreadystatechange = hasilPengadministrasi;
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
			if(report=="pengadministrasi"){
				req.onreadystatechange = hasilPengadministrasi;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}
