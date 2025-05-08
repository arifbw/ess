<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Cuti extends CI_Controller {
		
		public function __construct(){
			parent::__construct();
			
			meta_data();
			$this->load->model("notifikasi/m_cuti");
		}

		public function index(){
			$this->notifikasi_cuti();
		}
		
		private function notifikasi_cuti(){
			$tanggal_cutoff = $this->m_setting->ambil_pengaturan("cutoff_erp_tanggal");
			$result = $this->m_cuti->get_cuti_belum_persetujuan($tanggal_cutoff);
			$arr_hasil = array();
			
			$arr["np_pimpinan"] = "";

			foreach($result as $cuti){
				if(strcmp($arr["np_pimpinan"],$cuti["np_pimpinan"])!=0){
					if(!empty($arr["np_pimpinan"])){
						array_push($arr_hasil,$arr);
					}
					$arr["np_pimpinan"] = $cuti["np_pimpinan"];
					$arr["username"] = $cuti["username"];
					$arr["nama"] = $cuti["nama"];
					$arr["nama_jabatan"] = $cuti["nama_jabatan"];

					$arr["sapaan"] = "";
					if(strcmp($cuti["jenis_kelamin"],"Laki-laki")==0){
						$arr["sapaan"] = "Bapak";
					}
					else if(strcmp($cuti["jenis_kelamin"],"Perempuan")==0){
						$arr["sapaan"] = "Ibu";
					}
					$arr["permohonan"] = array();
				}
				
				$arr["permohonan"]["cuti"][$cuti["tgl_dws"]] = $cuti["permohonan_per_hari"];
				$arr["permohonan"]["batas"][$cuti["tgl_dws"]] = $cuti["batas"];
			}
			
			if(!empty($arr["np_pimpinan"])){
				array_push($arr_hasil,$arr);
			}
			
			//var_dump($arr_hasil);
			
			for($i=0;$i<count($arr_hasil);$i++){
				$this->notifikasi($arr_hasil[$i]);
			}
		}
		
		public function notifikasi($permohonan){
			$this->load->helper("email_helper");
			
			$subject = "Permohonan Persetujuan cuti";
			$content = "";
			
			$sapaan = "";
			
			$content .= "<head>";
				$content .= "<style>";
					$content .= "table{";
						$content .= "font-family: 'Trebuchet MS', Arial, Helvetica, sans-serif;";
						$content .= "border-collapse: collapse;";
					$content .= "}";
					$content .= "tr th{";
						$content .= "border: 1px solid #00539B;";
						$content .= "padding: 8px;";
						$content .= "text-align: center;";
						$content .= "background-color: #00539B;";
						$content .= "color: white;";
						$content .= "font-size: 14px;";
					$content .= "}";
					$content .= "tr td {";
						$content .= "border: 1px solid #00539B;";
						$content .= "padding: 4px;";
						$content .= "font-size: 14px;";
					$content .= "}";

				$content .= "</style>";
			$content .= "</head>";
			$content .= "Yang terhormat ".$permohonan["sapaan"]." ".implode(".",array_map("ucfirst",explode(".",ucwords(strtolower($permohonan["nama"])))))." (".$permohonan["nama_jabatan"]."),";
			
			$content .= "<br><br>";
			
			$content .= "Terdapat ".array_sum($permohonan["permohonan"]["cuti"])." pengajuan cuti yang menunggu persetujuan ".$permohonan["sapaan"].". Pengajuan cuti tersebut adalah sebagai berikut.";
			$hari = count($permohonan["permohonan"]);
			$j=0;
			if(count($permohonan["permohonan"]["cuti"])>0){
				$content .= "<table>";
					$content .= "<tr>";
						$content .= "<th>Nomor</th>";
						$content .= "<th>Tanggal</th>";
						$content .= "<th>Banyak Pengajuan</th>";
						$content .= "<th>Batas Akhir Persetujuan</th>";
					$content .= "</tr>";
				foreach($permohonan["permohonan"]["cuti"] as $tanggal => $banyak_permohonan){
					$j++;
					$content .= "<tr>";
						$content .= "<td align='right'>$j.</td>";
						$content .= "<td align='right'>".tanggal($tanggal)."</td>";
						$content .= "<td align='right'>".$banyak_permohonan."</td>";
						$content .= "<td align='center'>".tanggal($permohonan["permohonan"]["batas"][$tanggal])."</td>";
					$content .= "</tr>";
				}
				$content .= "</table>";
			}
			$content .= "<br><br>";
			
			$content .= "Persetujuan cuti dapat dilakukan pada ".$this->m_setting->ambil_pengaturan("Deskripsi Aplikasi")." yang diakses melalui Portal Peruri.";
			
			$to = $permohonan["username"]."@peruri.co.id";
			//echo $subject."<br><br>".$content."<br><br>$to<hr>";
			
			kirim_email($subject,$content,$to);
		}
	}
	
	/* End of file cuti.php */
	/* Location: ./application/controllers/notifikasi/cuti.php */