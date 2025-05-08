<?php
	function akses_helper($id_modul){
		
		coming_soon();
		
		$object = get_instance();
		
		$url_ganti_password = base_url($object->m_setting->ambil_url_modul("Profil"));
		$url_login = base_url($object->m_setting->ambil_url_modul("Login"));

		if((int)$object->session->userdata("sisa_usia_password")<=0 and !in_array(str_replace("index.php/","",current_url()),array($url_ganti_password,$url_login)) and strcmp($object->session->userdata("asal_login"),$object->m_setting->ambil_pengaturan("Nama Aplikasi"))==0){
			redirect($url_ganti_password);
		}
				
		$username=$object->session->userdata("username");//var_dump($username);die();
				
		if(empty($username)){
			redirect(base_url());
		}
		
		$id_grup=$object->session->userdata("grup");
		$arr_hak_akses = $object->m_setting->ambil_hak_akses($id_grup,$id_modul);
		
		$arr_akses["akses"] = false;
		
		for($i=0;$i<count($arr_hak_akses);$i++){
			$arr_akses[$arr_hak_akses[$i]["nama"]] = (bool)$arr_hak_akses[$i]["akses"];
			
			if($arr_akses[$arr_hak_akses[$i]["nama"]]){
				$arr_akses["akses"] = true;
			}
		}
		
		//var_dump($arr_akses);die();
		return $arr_akses;
	}
	
	function izin($akses){
		if(!$akses){
			redirect(base_url());
		}
	}
?>