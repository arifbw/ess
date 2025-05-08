function tampil_data_ubah(element){
	var formulir = document.getElementById("formulir_ubah");

	document.getElementById("jabatan").innerHTML = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ",2)[1];
	formulir["kode_kelompok_jabatan"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ",2)[0];
	formulir["nama_kelompok_jabatan"].value = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ",2)[1];
	
	var arr_kelompok_jabatan_poh = element.parentNode.previousSibling.previousSibling.innerHTML.split("<br>");
	var arr_pangkat_poh = element.parentNode.previousSibling.innerHTML.split("<br>");
	
	var banyak_kelompok_jabatan = formulir["kelompok_jabatan[]"].length;
	var banyak_pangkat = formulir["pangkat[]"].length;
	
	for(var i=0;i<banyak_kelompok_jabatan;i++){
		if(arr_kelompok_jabatan_poh.indexOf(formulir["kelompok_jabatan[]"][i].nextSibling.textContent.trim())>-1){
			formulir["kelompok_jabatan[]"][i].checked = true;
		}
		else{
			formulir["kelompok_jabatan[]"][i].checked = false;
		}
	}
	
	for(var i=0;i<banyak_pangkat;i++){
		if(arr_pangkat_poh.indexOf(formulir["pangkat[]"][i].nextSibling.textContent.trim())>-1){
			formulir["pangkat[]"][i].checked = true;
		}
		else{
			formulir["pangkat[]"][i].checked = false;
		}
	}
}