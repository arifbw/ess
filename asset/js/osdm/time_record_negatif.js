function pilih_bulan(){
	$.ajax({
		type: "post",
		url: document.getElementById("base_url").value+"osdm/time_record_negatif/daftar_pekan/",
		dataType: "json",
		data: {
			"tahun_bulan" : document.getElementById("tahun_bulan").value
		},
		success: function (data) {
			
			$("#periode").find("option").remove();
			
			for(i=0;i<data.length;i++){
				var option = document.createElement("option");
				option.value = data[i].value;
				option.text = data[i].text;
				document.getElementById("periode").appendChild(option);
			}
			
			tampilkan_isian();
		}
	});
}

function tampilkan_isian(){
	$.ajax({
		type: "post",
		url: document.getElementById("base_url").value+"osdm/time_record_negatif/tampilkan_isian/",
		dataType: "json",
		data: {
			"periode" : document.getElementById("periode").value
		},
		success: function (data) {
			//alert(data);
			document.getElementById("tempat_isian").innerHTML = data;
			/* $("#periode").find("option").remove();
			
			for(i=0;i<data.length;i++){
				var option = document.createElement("option");
				option.value = data[i].value;
				option.text = data[i].text;
				document.getElementById("periode").appendChild(option);
			}
			
			tampilkan_isian(); */
		}
	});
}

$(document).ready(function() {
	pilih_bulan();
});