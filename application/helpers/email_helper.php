<?php
	function kirim_email($subject,$content,$to,$cc="",$bcc=""){
		$object = get_instance();
		$object->load->library('email');
		$config = array();
		$config['protocol'] = 'smtp';
		//$config['smtp_host'] = '10.10.10.111';
		$config['smtp_host'] = 'mail.peruri.co.id';
		$config['smtp_user'] = 'ess.notif@peruri.co.id';
		$config['smtp_pass'] = 'P3ruri355';
		$config['smtp_port'] = 25;
		$object->email->initialize($config);

		$object->email->set_newline("\r\n");
		$object->email->set_mailtype("html");
		
		$object->load->model("m_setting");
		$object->from_name = $object->m_setting->ambil_pengaturan("Nama Aplikasi");
		$object->from_address = $config['smtp_user'];
		
		$aplikasi = $object->m_setting->ambil_pengaturan("Deskripsi Aplikasi");
		
		$footer = "<hr>";
		$footer .= "Email ini terkirim otomatis oleh ".$aplikasi." pada ".tanggal_waktu(date("Y-m-d H:i:s")).".<br>";
		$footer .= "Mohon tidak membalas email ini.<br>";
		$footer .= "<br><br>";
		$footer .= "Terima kasih.";
		$footer .= "<br><br>";
		$footer .= "<b>$aplikasi</b><br>";
		$footer .= "<b>".$object->m_setting->ambil_pengaturan("Dibuat Oleh")."</b>";
		
		$content .= "<br><br>".$footer;
		
		$object->email->from($object->from_address, $object->from_name); 
		$object->email->subject($subject); 
		$object->email->message($content);
		$object->email->to(array($to));
		$object->email->cc(array($cc));
		//$object->email->bcc(array($bcc,"arief.furqon@peruri.co.id"));
		var_dump($object->email);
		var_dump($object->email->send());
	}
?>