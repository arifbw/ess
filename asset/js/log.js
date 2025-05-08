function lihat_log(isi_target="",id_target=""){
	var form=document.createElement('form');
	form.method='post';
	form.action=document.getElementById("url_log").value;

	var input_nama_target_judul=document.createElement('input');
	input_nama_target_judul.type='hidden';
	input_nama_target_judul.name='nama_target_judul';
	input_nama_target_judul.value=document.getElementById("judul").value;
	form.appendChild(input_nama_target_judul);
	
	var input_isi_target_judul=document.createElement('input');
	input_isi_target_judul.type='hidden';
	input_isi_target_judul.name='isi_target_judul';
	input_isi_target_judul.value=isi_target;
	form.appendChild(input_isi_target_judul);
	
	var input_modul=document.createElement('input');
	input_modul.type='hidden';
	input_modul.name='modul';
	input_modul.value=document.getElementById("id_modul").value;
	form.appendChild(input_modul);

	var input_target=document.createElement('input');
	input_target.type='hidden';
	input_target.name='target';
	input_target.value=id_target;
	form.appendChild(input_target);
	
	var input_url_modul=document.createElement('input');
	input_url_modul.type='hidden';
	input_url_modul.name='url_modul';
	input_url_modul.value=window.location;
	form.appendChild(input_url_modul);
	
	document.body.appendChild(form);
	
	form.submit();
}
