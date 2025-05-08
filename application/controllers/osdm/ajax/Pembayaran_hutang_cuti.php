<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pembayaran_hutang_cuti extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_pembayaran_hutang_cuti");
			
			$this->data['judul'] = "Pembayaran Hutang Cuti";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
		}
		
		public function tampil_bayar($no_pokok){
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			
			if($this->akses["kompensasi"]){
				$this->load->model($this->folder_model."m_pembayaran_hutang_cuti");
				
				$this->data = $this->m_pembayaran_hutang_cuti->data_cuti_besar($no_pokok,$tahun);
				
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
				
				echo $this->data["konversi_bulan_ke_hari"]." ".$this->data["batas_konversi_hari"];
				
				$this->data["function"] = __FUNCTION__;
			}
			
			$this->load->view($this->folder_ajax_view."pembayaran_hutang_cuti",$this->data);
		}
	}
	
	/* End of file pembayaran_hutang_cuti.php */
	/* Location: ./application/controllers/ajax/osdm/pembayaran_hutang_cuti.php */