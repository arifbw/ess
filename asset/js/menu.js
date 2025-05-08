function cari_menu(){
	var cari_menu = document.getElementById("pencarian_menu").value;
	
	if(cari_menu.length>0){		
		$.ajax({
			type: "post",
			url: document.getElementById("base_url").value+"menu/cari_menu/",
			dataType: "json",
			data: {
				"cari_menu" : cari_menu			
			},
			success: function (data) {
				document.getElementById("judul_hasil_cari_menu").innerHTML = "<li><b>Hasil Pencarian Menu ("+data.banyak+")</b></li>";
				
				document.getElementById("hasil_cari_menu").innerHTML = "<li>"+data.hasil+"</li>";
				document.getElementById("hasil_cari_menu").style.display = "inline-block";
			}
		});
	}
	else{
		document.getElementById("judul_hasil_cari_menu").innerHTML = "";
		
		document.getElementById("hasil_cari_menu").innerHTML = "";
		document.getElementById("hasil_cari_menu").style.display = "";
	}
}