<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Mail extends CI_Controller {
		
		public function __construct(){
			parent::__construct();
			
		
		}

		public function notifikasi_pap(){
			
			$from_email = "tri.wibowo.1@peruri.co.id"; 
			$to_email = "tri.wibowo.1@peruri.co.id"; 

			$config = Array(
					'protocol' => 'smtp',
					'smtp_host' => 'ssl://mail.peruri.co.id',
					'smtp_port' => 25,
					'smtp_user' => $from_email,
					'smtp_pass' => 'null1sempty',
					'mailtype'  => 'html', 
					'charset'   => 'iso-8859-1'
			);

			$this->load->library('email', $config);
			$this->email->set_newline("\r\n");   

			$this->email->from($from_email, 'Tri Wibowo'); 
			$this->email->to($to_email);
			$this->email->subject('Test Pengiriman Email'); 
			$this->email->message('Coba mengirim Email dengan CodeIgniter.'); 

			//Send mail 
			if($this->email->send()){
				echo "berhasil kirim";
			}else {
				echo "gagal kirim"."<br>";
				echo $this->email->print_debugger();
			} 
			
		}
		
		
	}
	
	/* End of file Mail.php */
	/* Location: ./application/controllers/mail/Mail.php */