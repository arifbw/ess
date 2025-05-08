<?php
	function tambah_pengguna($karyawan,$username,$status){			
		$object =& get_instance();
		
		$return = array("status" => false, "error_info" => "");
		
		$object->load->model("administrator/m_pengguna");
		
		if($object->m_pengguna->cek_tambah_pengguna($username)){
			$data = array(
					"username" => $username,
					"no_pokok" => "$karyawan",
					"status" => $status,
					"waktu_daftar" => date("Y-m-d H:i:s")
				);
			$object->m_pengguna->tambah($data);
			
			if($object->m_pengguna->cek_hasil_pengguna($karyawan,$username,$status)){
				$return["status"] = true;
				$log_data_baru = "";
				
				$arr_data_insert = $object->m_pengguna->data_pengguna($username);
				
				foreach($data as $key => $value){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
				
				if(empty($object->session->userdata("id_pengguna"))){
					$id_pengguna = 0;
				}
				else{
					$id_pengguna = $object->session->userdata("id_pengguna");
				}
				
				$log = array(
					"id_pengguna" => $id_pengguna,
					"id_modul" => $object->data['id_modul'],
					"id_target" => $arr_data_insert['id'],
					"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $object->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$object->m_log->tambah($log);
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Penambahan Pengguna <b>Gagal</b> Dilakukan.";
			}
		}
		else{
			$return["status"] = false;
			$return["error_info"] = "Pengguna dengan username <b>$username</b> sudah ada.";
		}
		return $return;
	}
	
	function simpan_grup($username,$arr_grup_pengguna){
		$object =& get_instance();
		
		$return = array("status" => false, "error_info" => "");
		
		$object->load->model("administrator/m_pengguna_grup_pengguna");
		
		$data_pengguna = $object->m_pengguna->data_pengguna($username);
		$arr_grup_pengguna_lama = $object->m_pengguna_grup_pengguna->grup_pengguna_user($username);
		
		$log_data_lama = "";
		if(count($arr_grup_pengguna_lama)>0){
			for($i=0;$i<count($arr_grup_pengguna_lama);$i++){
				$log_data_lama .= "<li>".$arr_grup_pengguna_lama[$i]["nama"]."</li>";
			}
		}
		
		if(!empty($log_data_lama)){
			$log_data_lama = "grup pengguna :<ul>".$log_data_lama."</ul>";
		}
		
		$data = array(
					"id_pengguna" => $data_pengguna["id"]
				);
		
		$object->m_pengguna_grup_pengguna->hapus($data);
		
		for($i=0;$i<count($arr_grup_pengguna);$i++){
			$data = array("id_pengguna" => $data_pengguna["id"],"id_grup_pengguna" => $arr_grup_pengguna[$i]);
			$object->m_pengguna_grup_pengguna->tambah($data);
		}
		
		if($object->m_pengguna_grup_pengguna->cek_grup_pengguna($data_pengguna["id"],$arr_grup_pengguna)){
			$return["status"] = true;
			
			$arr_grup_pengguna_baru = $object->m_pengguna_grup_pengguna->grup_pengguna_user($username);
		
			$log_data_baru = "";
			if(count($arr_grup_pengguna_baru)>0){
				$log_data_lama .= "<ul>";
				for($i=0;$i<count($arr_grup_pengguna_baru);$i++){
					$log_data_baru .= "<li>".$arr_grup_pengguna_baru[$i]["nama"]."</li>";
				}
				$log_data_baru .= "</ul>";
			}
			
			if(!empty($log_data_baru)){
				$log_data_baru = "grup pengguna :<ul>".$log_data_baru."</ul>";
			}
			
			if(empty($object->session->userdata("id_pengguna"))){
				$id_pengguna = 0;
			}
			else{
				$id_pengguna = $object->session->userdata("id_pengguna");
			}
			
			$log = array(
				"id_pengguna" => $id_pengguna,
				"id_modul" => $object->data['id_modul'],
				"id_target" => $data_pengguna["id"],
				"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
				"kondisi_lama" => $log_data_lama,
				"kondisi_baru" => $log_data_baru,
				"alamat_ip" => $object->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$object->m_log->tambah($log);
			
			$return["success_info"] = "Berhasil mengubah grup pengguna untuk pengguna dengan <i>username</i> <b>$username</b>.";
		}

		return $return;
	}
?>