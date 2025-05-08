<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Cuti_besar extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."m_cuti_besar");

			$this->konversi_bulan_ke_hari = (int)$this->m_setting->ambil_pengaturan("Konversi Bulan ke Hari Kerja");
			
			$this->data['judul'] = "Cuti Besar";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
		}
		
		public function hitung_konversi_bulan($bulan=0){
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			if($this->akses["konversi"]){
				$this->load->model($this->folder_model."m_cuti_besar");
				
				
				if($bulan>0){
					$this->data["hasil_konversi"] = $bulan * $this->konversi_bulan_ke_hari;
				}
				else{
					$this->data["hasil_konversi"] = "";
				}
				
				$this->data["function"] = __FUNCTION__;
			}
			//var_dump($this->data);
			$this->load->view($this->folder_ajax_view."cuti_besar",$this->data);
		}
		
		public function hitung_konversi_hari($hari=0){
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			if($this->akses["konversi"]){
				$this->load->model($this->folder_model."m_cuti_besar");
				
				
				if($hari>0){
					$this->data["hasil_konversi"] = $hari / $this->konversi_bulan_ke_hari;
				}
				else{
					$this->data["hasil_konversi"] = "";
				}
				
				$this->data["function"] = __FUNCTION__;
			}
			//var_dump($this->data);
			$this->load->view($this->folder_ajax_view."cuti_besar",$this->data);
		}
		
		public function tampil_konversi($no_pokok,$tahun){
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			if($this->akses["konversi"]){
				$this->load->model($this->folder_model."m_cuti_besar");
				
				$this->data = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
				
				list($this->data["cuti_besar_wajib_jumlah"],$this->data["cuti_besar_wajib_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Wajib Dijalankan"));
				
				if((int)$this->data["pakai_bulan"] < (int)$this->data["cuti_besar_wajib_jumlah"] and strcmp($this->data["cuti_besar_wajib_satuan"],"bulan")){
					$this->data["batas_konversi_bulan"] = (int)$this->data["sisa_bulan"] - (int)$this->data["cuti_besar_wajib_jumlah"];
				}
				else{
					$this->data["batas_konversi_bulan"] = (int)$this->data["sisa_bulan"];
				}
				
				$this->data["konversi_bulan_ke_hari"] = $this->konversi_bulan_ke_hari;
				$this->data["batas_konversi_hari"] = floor((int)$this->data["sisa_hari"] / $this->konversi_bulan_ke_hari)*$this->konversi_bulan_ke_hari;
				
				$this->data["function"] = __FUNCTION__;
			}
			
			$this->load->view($this->folder_ajax_view."cuti_besar",$this->data);
		}
		
		//7648 - Tri Wibowo. Update tanggal 04-04-2019
		public function tampil_maintenance_kuota($no_pokok,$tahun){
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			if($this->akses["konversi"]){
				$this->load->model($this->folder_model."m_cuti_besar");
				
				$data_cuti = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
				
				list($this->data["cuti_besar_wajib_jumlah"],$this->data["cuti_besar_wajib_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Wajib Dijalankan"));
				
				
				//jika belum pernah edit
				$sisa_bulan_asli = 	$data_cuti["sisa_bulan_asli"];
				if(!$sisa_bulan_asli)
				{
					$sisa_bulan_asli= $data_cuti["sisa_bulan"];
				}
				
				$sisa_hari_asli = 	$data_cuti["sisa_hari_asli"];
				if(!$sisa_hari_asli)
				{
					$sisa_hari_asli=$data_cuti["sisa_hari"];
				}
				
				$this->data["id"] = $data_cuti["id"];
				$this->data["np_karyawan"] = $data_cuti["np_karyawan"];
				$this->data["nama"] = $data_cuti["nama"];
				$this->data["tahun"] = $data_cuti["tahun"];
				
				$this->data["sisa_bulan"] = $data_cuti["sisa_bulan"];
				$this->data["sisa_hari"] = $data_cuti["sisa_hari"];
				
				$this->data["sisa_edit"] = $data_cuti["batas_konversi_bulan"];
				$this->data["sisa_edit_by"] = $data_cuti["batas_konversi_bulan"];
				$this->data["sisa_edit_at"] = $data_cuti["batas_konversi_bulan"];
				$this->data["sisa_edit_alasan"] = $data_cuti["sisa_edit_alasan"];			
				
				$this->data["sisa_bulan_asli"] = $sisa_bulan_asli;
				$this->data["sisa_hari_asli"] = $sisa_hari_asli;
							
				
				$this->data["function"] = __FUNCTION__;
			}
			
			$this->load->view($this->folder_ajax_view."cuti_besar",$this->data);
		}
		
		//7648 - Tri Wibowo. Update tanggal 08-03-2019
		public function tampil_perpanjang_kadaluarsa($no_pokok,$tahun){
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			if($this->akses["konversi"]){
				$this->load->model($this->folder_model."m_cuti_besar");
				
				$this->data = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
				
				list($this->data["cuti_besar_wajib_jumlah"],$this->data["cuti_besar_wajib_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Wajib Dijalankan"));
				
				if((int)$this->data["pakai_bulan"] < (int)$this->data["cuti_besar_wajib_jumlah"] and strcmp($this->data["cuti_besar_wajib_satuan"],"bulan")){
					$this->data["batas_konversi_bulan"] = (int)$this->data["sisa_bulan"] - (int)$this->data["cuti_besar_wajib_jumlah"];
				}
				else{
					$this->data["batas_konversi_bulan"] = (int)$this->data["sisa_bulan"];
				}
				
				$this->data["konversi_bulan_ke_hari"] = $this->konversi_bulan_ke_hari;
				$this->data["batas_konversi_hari"] = floor((int)$this->data["sisa_hari"] / $this->konversi_bulan_ke_hari)*$this->konversi_bulan_ke_hari;
				
				$this->data["function"] = __FUNCTION__;
			}
			
			$this->load->view($this->folder_ajax_view."cuti_besar",$this->data);
		}
				
		public function tampil_ubcb($no_pokok,$tahun){
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			if($this->akses["kompensasi"]){
				$this->load->model($this->folder_model."m_cuti_besar");
				
				$this->data = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
				
				list($this->data["cuti_besar_wajib_jumlah"],$this->data["cuti_besar_wajib_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Wajib Dijalankan"));
				
				list($this->data["cuti_besar_bulan_bisa_dikompensasi_jumlah"],$this->data["cuti_besar_bulan_bisa_dikompensasi_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Bulan Bisa Dikompensasi"));
				
				list($this->data["cuti_besar_waktu_pengajuan_kompensasi_jumlah"],$this->data["cuti_besar_waktu_pengajuan_kompensasi_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Waktu Pengajuan Kompensasi"));
				
				if((int)$this->data["pakai_bulan"] < (int)$this->data["cuti_besar_wajib_jumlah"] and strcmp($this->data["cuti_besar_wajib_satuan"],"bulan")){
					$this->data["batas_konversi_bulan"] = (int)$this->data["sisa_bulan"] - (int)$this->data["cuti_besar_wajib_jumlah"];
				}
				else{
					$this->data["batas_konversi_bulan"] = (int)$this->data["sisa_bulan"];
				}
				
				$this->data["konversi_bulan_ke_hari"] = $this->konversi_bulan_ke_hari;
				$this->data["batas_konversi_hari"] = floor((int)$this->data["sisa_hari"] / $this->konversi_bulan_ke_hari)*$this->konversi_bulan_ke_hari;
				
				
				$this->data["function"] = __FUNCTION__;
			}
			
			$this->load->view($this->folder_ajax_view."cuti_besar",$this->data);
		}	
		
		public function tampil_kompensasi($no_pokok,$tahun){
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			if($this->akses["kompensasi"]){
				$this->load->model($this->folder_model."m_cuti_besar");
				
				$this->data = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
				
				list($this->data["cuti_besar_wajib_jumlah"],$this->data["cuti_besar_wajib_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Wajib Dijalankan"));
				
				list($this->data["cuti_besar_bulan_bisa_dikompensasi_jumlah"],$this->data["cuti_besar_bulan_bisa_dikompensasi_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Bulan Bisa Dikompensasi"));
				
				list($this->data["cuti_besar_waktu_pengajuan_kompensasi_jumlah"],$this->data["cuti_besar_waktu_pengajuan_kompensasi_satuan"]) = explode(" ",$this->m_setting->ambil_pengaturan("Cuti Besar Waktu Pengajuan Kompensasi"));
				
				if((int)$this->data["pakai_bulan"] < (int)$this->data["cuti_besar_wajib_jumlah"] and strcmp($this->data["cuti_besar_wajib_satuan"],"bulan")){
					$this->data["batas_konversi_bulan"] = (int)$this->data["sisa_bulan"] - (int)$this->data["cuti_besar_wajib_jumlah"];
				}
				else{
					$this->data["batas_konversi_bulan"] = (int)$this->data["sisa_bulan"];
				}
				
				$this->data["konversi_bulan_ke_hari"] = $this->konversi_bulan_ke_hari;
				$this->data["batas_konversi_hari"] = floor((int)$this->data["sisa_hari"] / $this->konversi_bulan_ke_hari)*$this->konversi_bulan_ke_hari;
				
				//echo $this->data["konversi_bulan_ke_hari"]." ".$this->data["batas_konversi_hari"];
				
				$this->data["function"] = __FUNCTION__;
			}
			
			$this->load->view($this->folder_ajax_view."cuti_besar",$this->data);
		}
	}
	
	/* End of file cuti_besar.php */
	/* Location: ./application/controllers/osdm/cuti_besar.php */