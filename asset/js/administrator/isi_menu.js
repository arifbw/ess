function batalIsian(element){
	tampil();
}

function buatIsian(element){
	var newDiv = document.createElement("div");
	var hitung = document.getElementById("id_tambah_menu").value;
	
	hitung = hitung.toString();
	if(hitung.length==1){
		hitung = "0"+hitung;
	}
	var id = "isian_"+hitung;
	newDiv.setAttribute("id",id);
	element.parentElement.insertBefore(newDiv,element);
	document.getElementById(id).className = "row well well-sm";
	document.getElementById(id).innerHTML = document.getElementById("isian").innerHTML;
	document.getElementById(id).childNodes[1].childNodes[0].id="isian_menu_"+hitung;
	document.getElementById(id).childNodes[1].childNodes[1].id="induk_"+hitung;
	document.getElementById(id).childNodes[1].childNodes[2].id="level_"+hitung;
	element.parentElement.removeChild(element);
	document.getElementById(id).childNodes[1].childNodes[0].focus();
}

function buatIsianSub(element){
	var newDiv = document.createElement("div");
	var hitung = document.getElementById("id_tambah_menu").value;//element.parentElement.parentElement.parentElement.parentElement.childElementCount;
	hitung = hitung.toString();
	if(hitung.length==1){
		hitung = "0"+hitung;
	}
	var id = "isian_"+hitung;
	newDiv.setAttribute("id",id);
	element.parentElement.parentElement.parentElement.parentElement.appendChild(newDiv);
	document.getElementById(id).className = "well well-sm";
	document.getElementById(id).style.marginTop = "10px";
	
 	var newDiv = document.createElement("div");
	document.getElementById(id).appendChild(newDiv);
	document.getElementById(id).childNodes[0].className = "row";
	
	document.getElementById(id).childNodes[0].innerHTML = document.getElementById("isian").innerHTML;
	document.getElementById(id).childNodes[0].childNodes[1].childNodes[0].id="isian_menu_"+hitung;
	document.getElementById(id).childNodes[0].childNodes[1].childNodes[0].focus();
	document.getElementById(id).childNodes[0].childNodes[1].childNodes[1].id="induk_"+hitung;
	document.getElementById(id).childNodes[0].childNodes[1].childNodes[1].value=element.parentElement.parentElement.parentElement.childNodes[1].childNodes[3].value;
	document.getElementById(id).childNodes[0].childNodes[1].childNodes[2].id="level_"+hitung;
	document.getElementById(id).childNodes[0].childNodes[1].childNodes[2].value=parseInt(element.parentElement.parentElement.parentElement.childNodes[1].childNodes[5].value)+1;
	element.parentElement.removeChild(element);
}

function hapus(urutan){
	processAjax("hapus",document.getElementById("id_menu").value+"/"+urutan);
}

function hasilSimpan(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			if(req.responseText.split("|")[0]=="GAGAL"){
				alert(req.responseText.split("|")[1]);
			}
			tampil();
		}
	}
}

function hasilTampil(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("pengaturan_menu").innerHTML = req.responseText;
		}
	}
}

function masterMenu(url){
	window.location=url;
}

function processAjax(report,params){
	if(report=="hapus"){
		url = "administrator/ajax/isi_menu/hapus/";
	}
	else if(report=="simpan"){
		url = "administrator/ajax/isi_menu/simpan/";
	}
	else if(report=="tampil"){
		url = "administrator/ajax/isi_menu/tampil/";
	}
	else if(report=="tukar"){
		url = "administrator/ajax/isi_menu/tukar_urutan/";
	}
	else if(report=="ubah"){
		url = "administrator/ajax/isi_menu/ubah/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="simpan" || report=="tukar" || report=="hapus" || report=="ubah"){//alert(url);
			req.onreadystatechange = hasilSimpan;
		}
		else if(report=="tampil"){
			req.onreadystatechange = hasilTampil;
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
			if(report=="simpan" || report=="tukar" || report=="hapus" || report=="ubah"){
				req.onreadystatechange = hasilSimpan;
			}
			else if(report=="tampil"){
				req.onreadystatechange = hasilTampil;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}

function simpan(element){
	var id_menu = document.getElementById("id_menu").value;
	var i = element.id.replace("isian_menu_","");
	var isian_menu = document.getElementById("isian_menu_"+i).value;
	var level = document.getElementById("level_"+i).value;
	var induk = document.getElementById("induk_"+i).value;
	processAjax("simpan",id_menu+"/"+isian_menu+"/"+level+"/"+induk);
}

function tampil(){
	processAjax("tampil",document.getElementById("id_menu").value);
}

function tukar(urutan_1,urutan_2){
	processAjax("tukar",document.getElementById("id_menu").value+"/"+urutan_1+"/"+urutan_2);
}

function ubah(element){
	var id_menu = document.getElementById("id_menu").value;
	var i = element.id.replace("isian_menu_","");
	var isian_menu = document.getElementById("isian_menu_"+i).value;
	var isian_menu_lama = document.getElementById("isian_menu_lama_"+i).value;
	var level = document.getElementById("level_"+i).value;
	var induk = document.getElementById("induk_"+i).value;
	var urutan = document.getElementById("urutan_"+i).value;
	processAjax("ubah",id_menu+"/"+isian_menu+"/"+level+"/"+induk+"/"+urutan+"/"+isian_menu_lama);
}

$(document).ready(function() {
	tampil();
});