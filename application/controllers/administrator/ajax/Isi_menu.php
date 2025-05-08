<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Isi_menu extends CI_Controller {
		public function __construct(){
			parent::__construct();
					
			$this->folder_view = 'administrator/ajax/';
			$this->folder_model = 'administrator/';
			$this->akses = array();
			
			$this->load->model("m_setting");
			$this->load->model($this->folder_model."m_master_menu");
			$this->load->model($this->folder_model."m_isi_menu");
			$this->load->model($this->folder_model."m_modul");

			$this->meta = meta_data();
			$this->data['judul'] = "Isi Menu";
			
			$this->data['success'] = "";
			$this->data['warning'] = "";
		}

		public function hapus($id_menu,$urutan){
			//cek punya anak atau nggak
			$banyak_anak = (int)$this->m_isi_menu->hitung_sub_menu($id_menu,$urutan);//die(var_dump($banyak_anak));

			if($banyak_anak>0){
				$data["status"] = false;
				$data["error_warning"] = "Masih terdapat sub-menu pada menu ini.";
			}
			else{
				$data_lama = $this->m_isi_menu->data_isi_menu($id_menu,$urutan);
				$this->m_isi_menu->hapus($id_menu,$urutan);
				$data["status"] = true;
				
				$log_data_lama = "master menu : ".$this->m_master_menu->ambil_nama_menu($data_lama["id_master_menu"])."<br>";
				$log_data_lama .= "level : ".$data_lama["level"]."<br>";
				$log_data_lama .= "urutan : ".$data_lama["urutan"]."<br>";
				$log_data_lama .= "nama modul : ".$data_lama["nama"];
				
				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->m_setting->ambil_id_modul($this->data['judul']),
					"id_target" => $id_menu,
					"deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"alamat_ip" => $this->meta["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
			}
			
			$this->load->view($this->folder_view."hasil",$data);
		}
		
		public function simpan($id_menu,$isian_menu,$level,$induk){//echo "$id_menu,$isian_menu,$level,$induk<br>";
			if($this->m_isi_menu->cek_isi_menu_digunakan($id_menu,$isian_menu)){
				$data["status"] = false;
				$data["error_warning"] = $this->m_modul->ambil_nama_modul($isian_menu)." $isian_menu telah digunakan pada ".$this->m_master_menu->ambil_nama_menu($id_menu);
			}
			else{
				$data = array(
							"id_master_menu" => $id_menu,
							"level" => $level,
							"id_modul" => $isian_menu
						);
				$this->m_isi_menu->hapus_isi_menu($data);
				
				$data["level"] = $level;
				$data["urutan_induk"] = $induk;
				$urutan = $this->m_isi_menu->ambil_max_urutan_isi_menu($id_menu,$induk);
				$urutan = str_pad((int)substr($urutan,-2)+1,2,"0",STR_PAD_LEFT);
				if(strcmp($induk,"0")==0){
					$data["urutan"] = $urutan;
				}
				else{
					$data["urutan"] = $induk.$urutan;
				}
				$urutan = $data["urutan"];
				//die(var_dump($data));
				$this->m_isi_menu->simpan_isi_menu($data);
				
				if($this->m_isi_menu->cek_hasil_simpan_isi_menu($id_menu,$isian_menu,$level,$induk,$urutan)){
					$data["status"] = true;
					
					$log_data_baru = "master menu : ".$this->m_master_menu->ambil_nama_menu($id_menu)."<br>";
					$log_data_baru .= "level : ".$data["level"]."<br>";
					$log_data_baru .= "urutan : ".$data["urutan"]."<br>";
					$log_data_baru .= "nama modul : ".$this->m_modul->ambil_nama_modul($isian_menu);
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->m_setting->ambil_id_modul($this->data['judul']),
						"id_target" => $id_menu,
						"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__FUNCTION__)),
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->meta["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$data["status"] = false;
					$data["error_warning"] = "Penambahan isi menu gagal dilakukan.";
				}
			}
			$this->load->view($this->folder_view."hasil",$data);
		}
		
		public function tampil($id_menu){
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
		
			izin($this->akses["akses"]);
			
			$data["akses"] = $this->akses;
			
			$data["url_master_menu"] = $this->m_modul->ambil_url_modul("Master Menu");
			
			if($this->akses["lihat"]){				
				$data["daftar_menu"] = $this->m_isi_menu->daftar_pengaturan_menu($id_menu);
				$data["pilihan_modul"] = $this->m_modul->daftar_modul_aktif();
			}
            //echo json_encode($data["daftar_menu"]); exit();
			$this->load->view($this->folder_view."isi_menu",$data);
		}
		
		public function tukar_urutan($id_menu,$urutan_1,$urutan_2){
			$data_lama_1 = $this->m_isi_menu->data_isi_menu($id_menu,$urutan_1);
			$data_lama_2 = $this->m_isi_menu->data_isi_menu($id_menu,$urutan_2);
			
			$log_data_lama = "master menu : ".$this->m_master_menu->ambil_nama_menu($data_lama_1["id_master_menu"])."<br>";
			$log_data_lama .= "level : ".$data_lama_1["level"]."<br>";
			$log_data_lama .= "urutan : ".$data_lama_1["urutan"]."<br>";
			$log_data_lama .= "nama modul : ".$data_lama_1["nama"]."<br><br>";
			
			$log_data_lama .= "master menu : ".$this->m_master_menu->ambil_nama_menu($data_lama_2["id_master_menu"])."<br>";
			$log_data_lama .= "level : ".$data_lama_2["level"]."<br>";
			$log_data_lama .= "urutan : ".$data_lama_2["urutan"]."<br>";
			$log_data_lama .= "nama modul : ".$data_lama_2["nama"];
			
			$id_modul_1 = $this->m_isi_menu->ambil_id_modul_isi_menu_urutan($id_menu,$urutan_1);
			$id_modul_2 = $this->m_isi_menu->ambil_id_modul_isi_menu_urutan($id_menu,$urutan_2);
			
			$arr_id_modul_urutan_induk_1 = $this->m_isi_menu->ambil_id_modul_isi_menu_urutan_induk($id_menu,$urutan_1);
			$arr_id_modul_urutan_induk_2 = $this->m_isi_menu->ambil_id_modul_isi_menu_urutan_induk($id_menu,$urutan_2);

			$arr_id_modul_urutan_depan_1 = $this->m_isi_menu->ambil_id_modul_isi_menu_urutan_depan($id_menu,$urutan_1);
			$arr_id_modul_urutan_depan_2 = $this->m_isi_menu->ambil_id_modul_isi_menu_urutan_depan($id_menu,$urutan_2);
			
			$set = array("urutan"=>$urutan_2);
			$this->m_isi_menu->ubah_urutan_isi_menu($set,$id_menu,$id_modul_1);
			
			$set = array("urutan"=>$urutan_1);
			$this->m_isi_menu->ubah_urutan_isi_menu($set,$id_menu,$id_modul_2);
			
			if(count($arr_id_modul_urutan_induk_1)>0){
				$this->m_isi_menu->ubah_urutan_induk_isi_menu($urutan_2,$id_menu,$arr_id_modul_urutan_induk_1);
			}

			if(count($arr_id_modul_urutan_induk_2)>0){
				$this->m_isi_menu->ubah_urutan_induk_isi_menu($urutan_1,$id_menu,$arr_id_modul_urutan_induk_2);
			}
			
			if(count($arr_id_modul_urutan_depan_1)>0){
				$this->m_isi_menu->ubah_urutan_isi_menu_masal($id_menu,$urutan_2,$arr_id_modul_urutan_depan_1);
			}
			
			if(count($arr_id_modul_urutan_depan_2)>0){
				$this->m_isi_menu->ubah_urutan_isi_menu_masal($id_menu,$urutan_1,$arr_id_modul_urutan_depan_2);
			}
			
			$data["status"] = true;
			
			$data_baru_1 = $this->m_isi_menu->data_isi_menu($id_menu,$urutan_1);
			$data_baru_2 = $this->m_isi_menu->data_isi_menu($id_menu,$urutan_2);
			
			$log_data_baru = "master menu : ".$this->m_master_menu->ambil_nama_menu($data_baru_1["id_master_menu"])."<br>";
			$log_data_baru .= "level : ".$data_baru_1["level"]."<br>";
			$log_data_baru .= "urutan : ".$data_baru_1["urutan"]."<br>";
			$log_data_baru .= "nama modul : ".$data_baru_1["nama"]."<br><br>";
			
			$log_data_baru .= "master menu : ".$this->m_master_menu->ambil_nama_menu($data_baru_2["id_master_menu"])."<br>";
			$log_data_baru .= "level : ".$data_baru_2["level"]."<br>";
			$log_data_baru .= "urutan : ".$data_baru_2["urutan"]."<br>";
			$log_data_baru .= "nama modul : ".$data_baru_2["nama"];
			
			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->m_setting->ambil_id_modul($this->data['judul']),
				"id_target" => $id_menu,
				"deskripsi" => "tukar urutan ".strtolower(preg_replace("/_/"," ",__CLASS__)),
				"kondisi_lama" => $log_data_lama,
				"kondisi_baru" => $log_data_baru,
				"alamat_ip" => $this->meta["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);
			
			
			$this->load->view($this->folder_view."hasil",$data);
		}
		
		public function ubah($id_menu,$isian_menu,$level,$induk,$urutan,$isian_menu_lama){
			$log_data_lama = "master menu : ".$this->m_master_menu->ambil_nama_menu($id_menu)."<br>";
			$log_data_lama .= "level : $level<br>";
			$log_data_lama .= "urutan : $urutan<br>";
			$log_data_lama .= "nama modul : ".$this->m_modul->ambil_nama_modul($isian_menu_lama);
			
			if($this->m_isi_menu->cek_isi_menu_digunakan($id_menu,$isian_menu)){
				$data["status"] = false;
				$data["error_warning"] = $this->m_modul->ambil_nama_modul($isian_menu)." telah digunakan pada ".$this->m_master_menu->ambil_nama_menu($id_menu);
			}
			else{
				$set = array(
							"id_modul" => $isian_menu
						);
				$this->m_isi_menu->ubah_isi_menu($set,$id_menu,$level,$induk,$isian_menu_lama);
				
				if($this->m_isi_menu->cek_hasil_simpan_isi_menu($id_menu,$isian_menu,$level,$induk,$urutan)){
					$data["status"] = true;
					
					$log_data_baru = "master menu : ".$this->m_master_menu->ambil_nama_menu($id_menu)."<br>";
					$log_data_baru .= "level : $level<br>";
					$log_data_baru .= "urutan : ".$data["urutan"]."<br>";
					$log_data_baru .= "nama modul : ".$this->m_modul->ambil_nama_modul($isian_menu);
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->m_setting->ambil_id_modul($this->data['judul']),
						"id_target" => $id_menu,
						"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__FUNCTION__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->meta["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$data["status"] = false;
				}
			}
	
			$this->load->view($this->folder_view."hasil",$data);
		}
	}
	
	/* End of file isi_menu.php */
	/* Location: ./application/controllers/administrator/ajax/isi_menu.php */