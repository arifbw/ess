function cek_simpan_tambah(){
	var formulir = document.getElementById("formulir_tambah");
	var lanjut = true;
	
	if(formulir["jabatan"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_jabatan").innerHTML = "Jabatan harus diisi.";
	}
	else{
		document.getElementById("warning_jabatan").innerHTML = "";
	}
	
	if(formulir["karyawan"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_karyawan").innerHTML = "Nama Karyawan POH harus diisi.";
	}
	else{
		document.getElementById("warning_karyawan").innerHTML = "";
	}
	
	if(formulir["tanggal_mulai"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode").innerHTML = "Tanggal awal periode harus diisi.";
	}
	else{
		document.getElementById("warning_periode").innerHTML = "";
	}
	if(formulir["tanggal_selesai"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		if(document.getElementById("warning_periode").innerHTML != ""){
			document.getElementById("warning_periode").innerHTML += " ";
		}
		document.getElementById("warning_periode").innerHTML += "Tanggal akhir periode harus diisi.";
	}
	else{
		document.getElementById("warning_periode").innerHTML = "";
	}
	
	if(formulir["tanggal_selesai"].value != ""){
		if(formulir["tanggal_mulai"].value > formulir["tanggal_selesai"].value){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_periode").innerHTML = "Tanggal awal periode tidak boleh setelah tanggal akhir periode.";
		}
		else{
			document.getElementById("warning_periode").innerHTML = "";
		}
	}
	
	return lanjut;
}

function cek_simpan_ubah() {
	var formulir = document.getElementById("formulir_ubah");
	var lanjut = true;
	
	if(formulir["jabatan_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_jabatan_ubah").innerHTML = "Jabatan harus diisi.";
	}
	else{
		document.getElementById("warning_jabatan_ubah").innerHTML = "";
	}
	
	if(formulir["karyawan_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_karyawan_ubah").innerHTML = "Nama Karyawan POH harus diisi.";
	}
	else{
		document.getElementById("warning_karyawan_ubah").innerHTML = "";
	}
	
	if(formulir["tanggal_mulai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		document.getElementById("warning_periode_ubah").innerHTML = "Tanggal awal periode harus diisi.";
	}
	else{
		document.getElementById("warning_periode_ubah").innerHTML = "";
	}
	
	if(formulir["tanggal_selesai_ubah"].value == ""){
		if(lanjut){
			lanjut = false;
		}
		if(document.getElementById("warning_periode_ubah").innerHTML != ""){
			document.getElementById("warning_periode_ubah").innerHTML += " ";
		}
		document.getElementById("warning_periode_ubah").innerHTML += "Tanggal akhir periode harus diisi.";
	}
	else{
		document.getElementById("warning_periode_ubah").innerHTML = "";
	}
	
	if(formulir["tanggal_selesai_ubah"].value != ""){
		if(formulir["tanggal_mulai_ubah"].value > formulir["tanggal_selesai_ubah"].value){
			if(lanjut){
				lanjut = false;
			}
			document.getElementById("warning_periode_ubah").innerHTML = "Tanggal awal periode tidak boleh setelah tanggal akhir periode.";
		}
		else{
			document.getElementById("warning_periode_ubah").innerHTML = "";
		}
	}
	
	return lanjut;
}

function tampil_data_ubah(element){
	document.getElementById("warning_jabatan_ubah").innerHTML = "";
	document.getElementById("warning_karyawan_ubah").innerHTML = "";
	document.getElementById("warning_periode_ubah").innerHTML = "";
	
	var formulir = document.getElementById("formulir_ubah");

	formulir["jabatan"].value = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ")[0];
	formulir["jabatan_ubah"].value = formulir["jabatan"].value;
	
	document.getElementById("pejabat_definitif_ubah").innerHTML = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
	
	if(document.getElementById("pejabat_definitif_ubah").innerHTML==""){
		document.getElementById("pejabat_definitif_ubah").innerHTML = "<i>(tidak ada pejabat definitif)</i>";
	}
	else{
		formulir["np_definitif_ubah"].value = document.getElementById("pejabat_definitif_ubah").innerHTML.split(" - ")[0];
		formulir["nama_definitif_ubah"].value = document.getElementById("pejabat_definitif_ubah").innerHTML.split(" - ")[1];
	}
	
	if(element.nextSibling.value == "0"){
		formulir["sesuai_skep"].value = "tidak_sesuai";
	}
	else if(element.nextSibling.value == "1"){
		formulir["sesuai_skep"].value = "sesuai";
	}
	
	var mulai = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML.split("<br>sampai dengan<br>")[0].split(" ");
	var tanggal_mulai = mulai[2]+"-";
	if(mulai[1]=="Januari"){
		tanggal_mulai += "01";
	}
	else if(mulai[1]=="Februari"){
		tanggal_mulai += "02";
	}
	else if(mulai[1]=="Maret"){
		tanggal_mulai += "03";
	}
	else if(mulai[1]=="April"){
		tanggal_mulai += "04";
	}
	else if(mulai[1]=="Mei"){
		tanggal_mulai += "05";
	}
	else if(mulai[1]=="Juni"){
		tanggal_mulai += "06";
	}
	else if(mulai[1]=="Juli"){
		tanggal_mulai += "07";
	}
	else if(mulai[1]=="Agustus"){
		tanggal_mulai += "08";
	}
	else if(mulai[1]=="September"){
		tanggal_mulai += "09";
	}
	else if(mulai[1]=="Oktober"){
		tanggal_mulai += "10";
	}
	else if(mulai[1]=="November"){
		tanggal_mulai += "11";
	}
	else if(mulai[1]=="Desember"){
		tanggal_mulai += "12";
	}
	tanggal_mulai += "-";
	if(mulai[0].length==1){
		tanggal_mulai+="0";
	}
	tanggal_mulai+=mulai[0];
	
	formulir["tanggal_mulai"].value = tanggal_mulai;
	formulir["tanggal_mulai_ubah"].value = tanggal_mulai;
	
	var selesai = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML.split("<br>sampai dengan<br>")[1].split(" ");
	var tanggal_selesai = selesai[2]+"-";
	if(selesai[1]=="Januari"){
		tanggal_selesai += "01";
	}
	else if(selesai[1]=="Februari"){
		tanggal_selesai += "02";
	}
	else if(selesai[1]=="Maret"){
		tanggal_selesai += "03";
	}
	else if(selesai[1]=="April"){
		tanggal_selesai += "04";
	}
	else if(selesai[1]=="Mei"){
		tanggal_selesai += "05";
	}
	else if(selesai[1]=="Juni"){
		tanggal_selesai += "06";
	}
	else if(selesai[1]=="Juli"){
		tanggal_selesai += "07";
	}
	else if(selesai[1]=="Agustus"){
		tanggal_selesai += "08";
	}
	else if(selesai[1]=="September"){
		tanggal_selesai += "09";
	}
	else if(selesai[1]=="Oktober"){
		tanggal_selesai += "10";
	}
	else if(selesai[1]=="November"){
		tanggal_selesai += "11";
	}
	else if(selesai[1]=="Desember"){
		tanggal_selesai += "12";
	}
	tanggal_selesai += "-";
	if(selesai[0].length==1){
		tanggal_selesai+="0";
	}
	tanggal_selesai+=selesai[0];
	
	formulir["tanggal_selesai"].value = tanggal_selesai;
	formulir["tanggal_selesai_ubah"].value = tanggal_selesai;
	
	pilih_tanggal_selesai("ubah");
	pilih_tanggal_mulai("ubah");
	
	formulir["nomor_nota_dinas"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	formulir["nomor_nota_dinas_ubah"].value = element.parentNode.previousSibling.previousSibling.innerHTML;
	
	formulir["keterangan"].value = element.parentNode.previousSibling.innerHTML;
	formulir["keterangan_ubah"].value = element.parentNode.previousSibling.innerHTML;
	
	$("#karyawan_poh_ubah").val(element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ")[0]);
	
	$('#jabatan_ubah').select2().trigger('change');
	setTimeout(function(){$('#karyawan_ubah').select2().trigger('change');}, 500);
}

function pilih_jabatan(aksi){
	if(aksi=="ubah"){
		aksi = "_"+aksi;
	}
	else if(aksi=="tambah"){
		aksi = "";
	}
	//alert(aksi);
	var jabatan = $("#jabatan"+aksi).val();
	var kelompok_jabatan = jabatan.substring(jabatan.length - 3, jabatan.length);
	
	$.ajax({
		type: "post",
		url: document.getElementById("base_url").value+"osdm/poh/pejabat_definitif/",
		dataType: "json",
		data: {
			"jabatan" : jabatan
		},
		success: function (data) {
			var np_pejabat_definitif = "";
			
			if(data.length>0){
				document.getElementById("pejabat_definitif"+aksi).innerHTML = data[0].no_pokok+" - "+data[0].nama;
				np_pejabat_definitif = data[0].no_pokok;
				document.getElementById("np_definitif"+aksi).value=data[0].no_pokok;
				document.getElementById("nama_definitif"+aksi).value=data[0].nama;
			}
			else{
				document.getElementById("pejabat_definitif"+aksi).innerHTML = "<i>(tidak ada pejabat definitif)</i>";
				document.getElementById("np_definitif"+aksi).value="";
				document.getElementById("nama_definitif"+aksi).value="";
			}
			
			pilihan_karyawan(kelompok_jabatan,np_pejabat_definitif,aksi);
		}
	});
}

function pilihan_karyawan(kelompok_jabatan,np_pejabat_definitif,aksi){
	$.ajax({
		type: "post",
		url: document.getElementById("base_url").value+"osdm/poh/karyawan_calon_poh/",
		dataType: "json",
		data: {
			"kelompok_jabatan" : kelompok_jabatan,
			"np_pejabat_definitif" : np_pejabat_definitif,
			"sesuai_skep" : document.getElementById("formulir_tambah")["sesuai_skep"].value
		},
		success: function (data) {

			var karyawan = document.getElementById("karyawan"+aksi).value;
			$("#karyawan"+aksi).find("optgroup").remove();

			while(document.getElementById("karyawan"+aksi).length>1){
				document.getElementById("karyawan"+aksi).remove(document.getElementById("karyawan"+aksi).length-1);
			}

			var banyak_calon_poh = data.length;

			var optgroup_label = "";
			
			for(var i=0;i<banyak_calon_poh;i++){
				if(optgroup_label!=data[i].kode_unit+" - "+data[i].nama_unit){
					optgroup_label = data[i].kode_unit+" - "+data[i].nama_unit;
				
					var optgroup = document.createElement("optgroup");
					optgroup.label = data[i].kode_unit+" - "+data[i].nama_unit;
				}
				
				var option = document.createElement("option");
				option.value = data[i].no_pokok;
				option.text = data[i].no_pokok+" - "+data[i].nama;
				optgroup.appendChild(option);
				
				if(i==banyak_calon_poh-1 || optgroup_label!=data[i].kode_unit+" - "+data[i+1].nama_unit){
					document.getElementById("karyawan"+aksi).appendChild(optgroup);
				}
			}
			document.getElementById("karyawan"+aksi).value = karyawan;
			
			if(document.getElementById("karyawan_poh")!=null){
				document.getElementById("karyawan"+aksi).value = document.getElementById("karyawan_poh").value;
			}
			
			
			if(aksi=="_ubah"){
				document.getElementById("karyawan_ubah").value = document.getElementById("karyawan_poh_ubah").value;
			}
		}
	});

}

function pilih_tanggal_mulai(aksi=""){
	if(aksi=="ubah"){
		aksi="_"+aksi;
	}
	document.getElementById("tanggal_selesai"+aksi).min=document.getElementById("tanggal_mulai"+aksi).value;
}

function pilih_tanggal_selesai(aksi=""){
	if(aksi=="ubah"){
		aksi="_"+aksi;
	}
	document.getElementById("tanggal_mulai"+aksi).max=document.getElementById("tanggal_selesai"+aksi).value;
}

function table_serverside(){
	var table;
	
	$('#tabel_poh').DataTable().destroy();				
	//datatables
	table = $('#tabel_poh').DataTable({ 
		"iDisplayLength": 10,
		"language": {
			"url": document.getElementById("base_url").value+"/asset/datatables/Indonesian.json",
			"sEmptyTable": "Tidak ada data di database",
			"emptyTable": "Tidak ada data di database"
		},
		"bFilter": true,
		"stateSave": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.

		// Load data for the table's content from an Ajax source
		"ajax": {
			type: "POST",
			url: document.getElementById("base_url").value+"osdm/poh/tabel_poh/",
			data: {
				"display_poh" : document.getElementById("display_poh").value,
			}
		},

		//Set column definition initialisation properties.
		"columnDefs": [
			{ 						
				"targets": [ 0 ], //first column / numbering column
				"targets": 'no-sort', //first column / numbering column
				"orderable": false, //set not orderable
			},
		],

	});
}

$(document).ready(function() {
	//pilih_jabatan();
	table_serverside();
});

function hapus(element) {
	
	var tanggal_mulai = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML.split("<br>sampai dengan<br>")[0];   	
	
	var tanggal_selesai = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML.split("<br>sampai dengan<br>")[1];  	
		
	var kode_jabatan = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ")[0];
	
	var nama_jabatan = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ")[1];
	
	var np_definitif = element.parentNode.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ")[0];
	
	var np_poh = element.parentNode.previousSibling.previousSibling.previousSibling.innerHTML.split(" - ")[0];
	
	var data = tanggal_mulai+"|"+tanggal_selesai+"|"+kode_jabatan+"|"+np_definitif+"|"+np_poh;
	
	var url = document.getElementById("base_url").value+"osdm/poh/hapus/";
   	$('#inactive-action').prop('href', url+data);
   	$('#message-inactive').text('Apakah anda yakin ingin menghapus pengajuan data POH '+kode_jabatan+' '+nama_jabatan+' ?');
   	$('#modal-inactive').modal('show');

}
