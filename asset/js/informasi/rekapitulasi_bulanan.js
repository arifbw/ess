function hasilRekapitulasiBulanan(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("rekapitulasi_bulanan").innerHTML = req.responseText;
		}
	}
}

function rekapitulasi_bulanan(){
	if(document.getElementById("periode").value!=""){
		processAjax("rekapitulasi_bulanan",document.getElementById("karyawan").value+"/"+document.getElementById("periode").value);
	}
}

function processAjax(report,params){
	if(report=="rekapitulasi_bulanan"){
		url = "informasi/rekapitulasi_bulanan/ajax_rekapitulasi_bulanan/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="rekapitulasi_bulanan"){
			req.onreadystatechange = hasilRekapitulasiBulanan;
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
			if(report=="rekapitulasi_bulanan"){
				req.onreadystatechange = hasilRekapitulasiBulanan;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}