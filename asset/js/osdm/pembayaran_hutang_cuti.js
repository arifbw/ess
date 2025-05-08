function ambil_data(){
	table = $('#tabel_pembayaran_hutang_cuti').DataTable({ 
		"iDisplayLength": 10,
		"language": {
			"url": document.getElementById("base_url").value+"asset/datatables/Indonesian.json",
			"sEmptyTable": "Tidak ada data di database",
			"emptyTable": "Tidak ada data di database"
		},
		"stateSave": true,
		"processing": true, //Feature control the processing indicator.
		"serverSide": true, //Feature control DataTables' server-side processing mode.
		"order": [], //Initial no order.

		// Load data for the table's content from an Ajax source
		"ajax": {
			"url": document.getElementById("base_url").value + "osdm/Pembayaran_hutang_cuti/tabel_pembayaran_hutang_cuti/",
			"type": "POST"
		},

		//Set column definition initialisation properties.
		"columnDefs": [
			{ 
				"targets": 'no-sort', //first column / numbering column
				"orderable": false, //set not orderable
			},
		],

	});
}


function hasilTampilKonversi(){
	if (req.readyState == 4) { // Complete
		if (req.status == 200) { // OK response
			document.getElementById("isi_modal_konversi").innerHTML = req.responseText;
		}
	}
}

function tampil_bayar(element){
	var no_pokok = element.parentNode.parentNode.childNodes[0].innerHTML;
	processAjax("tampil_bayar",no_pokok);
}

function processAjax(report,params){
	if(report=="tampil_bayar"){
		url = "osdm/ajax/pembayaran_hutang_cuti/tampil_bayar/";
	}
	else{
		url = "";
	}
	
	if(url!=""){
		url = document.getElementById("base_url").value+url+params;
	}
	
	if (window.XMLHttpRequest) { // Non-IE browsers
		req = new XMLHttpRequest();
		if(report=="tampil_bayar"){
			req.onreadystatechange = hasilTampilBayar;
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
			if(report=="tampil_bayar"){
				req.onreadystatechange = hasilTampilBayar;
			}
			
			req.open("GET", url, true);
			req.send();
		}
	}
}