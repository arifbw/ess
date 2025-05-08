function cek_ubah_password(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["password_lama"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_password_lama").innerHTML = "<i>Password</i> Lama harus diisi.";
	}
	else{
		document.getElementById("warning_password_lama").innerHTML = "";
	}
	
	if(formulir["password_baru"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_password_baru").innerHTML = "<i>Password</i> Baru harus diisi.";
	}
	else{
		document.getElementById("warning_password_baru").innerHTML = "";
	}
	
	if(formulir["password_baru"].value == formulir["password_lama"].value){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_password_baru").innerHTML = "<i>Password</i> Baru harus berbeda dengan <i>Password</i> Lama.";
	}
	else{
		document.getElementById("warning_password_baru").innerHTML = "";
	}
	
	if(formulir["konfirmasi_password_baru"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_konfirmasi_password_baru").innerHTML = "Konfirmasi <i>Password</i> Baru harus diisi.";
	}
	else{
		document.getElementById("warning_konfirmasi_password_baru").innerHTML = "";
	}
	
	if(formulir["konfirmasi_password_baru"].value != formulir["password_baru"].value){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_konfirmasi_password_baru").innerHTML = "Konfirmasi <i>Password</i> Baru berbeda dengan <i>Password</i> Baru harus diisi.";
	}
	else{
		document.getElementById("warning_konfirmasi_password_baru").innerHTML = "";
	}
	
	return lanjut;
}