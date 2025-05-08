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

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Cuti Besar";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."cuti_besar";
			
			array_push($this->data['js_sources'],"osdm/cuti_besar");
			
			if($this->input->post()){
				if($this->akses["konversi"]){
					if(!empty($this->input->post("id_cuti_besar"))){
						$id_cuti_besar = $this->input->post("id_cuti_besar");
						$no_pokok = $this->input->post("no_pokok");
						$tahun = $this->input->post("tahun");
						
						$perpanjang_kadaluarsa = $this->input->post("perpanjang_kadaluarsa");
						$sisa_edit = $this->input->post("sisa_edit");
						
						$kompensasi_bulan = $this->input->post("kompensasi_bulan");
						
						$ubcb_tanggal_keluar 	 = $this->input->post("ubcb_tanggal_keluar");
						
						if(@$perpanjang_kadaluarsa) //jika dari form perpanjang_kadaluarsa
						{						
							$this->perpanjang_kadaluarsa($id_cuti_besar,$no_pokok,$tahun,$perpanjang_kadaluarsa);
						}else
						if(@$sisa_edit) //jika dari form maintenance_kuota
						{	
				
							$sisa_bulan_asli 	= $this->input->post("sisa_bulan_asli");
							$sisa_hari_asli 	= $this->input->post("sisa_hari_asli");
							$sisa_bulan			= $this->input->post("sisa_bulan");
							$sisa_hari 			= $this->input->post("sisa_hari");
							$sisa_edit_alasan 	= $this->input->post("sisa_edit_alasan");

						
							$this->maintenance_kuota($id_cuti_besar,$no_pokok,$tahun,$sisa_edit,$sisa_bulan_asli,$sisa_hari_asli,$sisa_bulan,$sisa_hari,$sisa_edit_alasan);
						}
						if(@$kompensasi_bulan) //jika dari form kompensasi bulan
						{
							$this->kompensasi_bulan($id_cuti_besar,$no_pokok,$tahun,$kompensasi_bulan);
						}else
						if(@$ubcb_tanggal_keluar) //jika dari form catatan ubcb
						{
							$ubcb_tanggal_cuti 	 = $this->input->post("ubcb_tanggal_cuti");
							
							$this->catatan_ubcb($id_cuti_besar,$no_pokok,$tahun,$ubcb_tanggal_keluar,$ubcb_tanggal_cuti);
						}else
						{
							$konversi_bulan = 0;
							$konversi_hari = 0;
							
							if(!empty($this->input->post("konversi_dari_bulan")) and !empty($this->input->post("konversi_jadi_hari"))){
								$konversi_bulan = $this->input->post("konversi_dari_bulan");
								$konversi_hari = $this->input->post("konversi_jadi_hari");
							}
							else if(!empty($this->input->post("konversi_dari_hari")) and !empty($this->input->post("konversi_jadi_bulan"))){
								$konversi_hari = -1*(int)$this->input->post("konversi_dari_hari");
								$konversi_bulan = -1*(int)$this->input->post("konversi_jadi_bulan");
							}

							if($konversi_bulan!=0 and $konversi_hari!=0){
								$this->konversi($id_cuti_besar,$no_pokok,$tahun,$konversi_bulan,$konversi_hari);							
							}
						}
						
												
					}
				}
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {									
									ambil_data();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			
			$this->load->view('template',$this->data);
		}
		
		private function konversi($id_cuti_besar,$no_pokok,$tahun,$konversi_bulan,$konversi_hari){
			$arr_data_lama = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_lama = "";
			
			foreach($arr_data_lama as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
			
			$this->m_cuti_besar->konversi($id_cuti_besar,$konversi_bulan,$konversi_hari);
			
			$arr_data_baru = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_baru = "";
			
			foreach($arr_data_baru as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
			}
			
			if($this->cek_konversi($arr_data_lama,$arr_data_baru,$konversi_bulan,$konversi_hari)){
				$this->data["success"] = "Konversi Cuti Besar Tahun $tahun untuk ".$arr_data_baru["nama"]." berhasil dilakukan";
			
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "konversi ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			else{
				$this->data["success"] = "Konversi Cuti Besar Tahun $tahun untuk ".$arr_data_baru["nama"]." gagal dilakukan";
			}
		}
		
		private function perpanjang_kadaluarsa($id_cuti_besar,$no_pokok,$tahun,$perpanjang_kadaluarsa){
			$arr_data_lama = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_lama = "";
			
			foreach($arr_data_lama as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
						
			$this->m_cuti_besar->perpanjang_kadaluarsa($id_cuti_besar,$perpanjang_kadaluarsa);
			
			$arr_data_baru = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_baru = "";
			
			foreach($arr_data_baru as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
			}
			
			if(1==1){
				$this->data["success"] = "Perpanjang Kadaluarsa Cuti Besar Tahun $tahun untuk ".$arr_data_baru["nama"]." berhasil dilakukan";
			
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "konversi ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			else{
				$this->data["success"] = "Perpanjang Kadaluarsa Cuti Besar Tahun $tahun untuk ".$arr_data_baru["nama"]." gagal dilakukan";
			}
		}
		
		private function maintenance_kuota($id_cuti_besar,$no_pokok,$tahun,$sisa_edit,$sisa_bulan_asli,$sisa_hari_asli,$sisa_bulan,$sisa_hari,$sisa_edit_alasan){
			
			$arr_data_lama = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_lama = "";
			
			foreach($arr_data_lama as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
			
			
			$this->m_cuti_besar->maintenance_kuota($id_cuti_besar,$no_pokok,$tahun,$sisa_edit,$sisa_bulan_asli,$sisa_hari_asli,$sisa_bulan,$sisa_hari,$sisa_edit_alasan);
			
			$arr_data_baru = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_baru = "";
			
			foreach($arr_data_baru as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
			}
			
			if(1==1){
				$this->data["success"] = "Maintenance Kuota Cuti Besar Tahun $tahun untuk ".$arr_data_baru["nama"]." berhasil dilakukan";
			
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "konversi ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			else{
				$this->data["success"] = "Maintenance Kuota Cuti Besar Tahun $tahun untuk ".$arr_data_baru["nama"]." gagal dilakukan";
			}
		}
		
		private function catatan_ubcb($id_cuti_besar,$no_pokok,$tahun,$ubcb_tanggal_keluar,$ubcb_tanggal_cuti){
			
			$arr_data_lama = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_lama = "";
			
					
			foreach($arr_data_lama as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
					
			
			
			$this->m_cuti_besar->catatan_ubcb($id_cuti_besar,$no_pokok,$tahun,$ubcb_tanggal_keluar,$ubcb_tanggal_cuti);
			
			$arr_data_baru = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_baru = "";
			
			foreach($arr_data_baru as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
			}
			
			if(1==1){
				$this->data["success"] = "Catatan UBCB Tahun $tahun untuk ".$arr_data_baru["nama"]." berhasil dilakukan";
			
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "konversi ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			else{
				$this->data["success"] = "Update Catatan UBCB Tahun $tahun untuk ".$arr_data_baru["nama"]." gagal dilakukan";
			}
		}
		
		private function kompensasi_bulan($id_cuti_besar,$no_pokok,$tahun,$kompensasi_bulan){
			
			$arr_data_lama = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_lama = "";
			
					
			foreach($arr_data_lama as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
			
			
			$data_kompensasi_bulan		= $arr_data_lama['kompensasi_bulan'];	 
			$data_sisa_bulan			= $arr_data_lama['sisa_bulan'];	 	 
			
			$hasil_sisa_bulan			= $data_sisa_bulan - $kompensasi_bulan;
			$hasil_kompensasi_bulan		= $data_kompensasi_bulan + $kompensasi_bulan;
			$hasil_kompensasi_at		=  date('Y-m-d H:i:s');
			
			$this->m_cuti_besar->kompensasi_bulan($id_cuti_besar,$no_pokok,$tahun,$hasil_sisa_bulan,$hasil_kompensasi_bulan,$hasil_kompensasi_at);
			
			$arr_data_baru = $this->m_cuti_besar->data_cuti_besar($no_pokok,$tahun);
			$log_data_baru = "";
			
			foreach($arr_data_baru as $key => $value){
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
			}
			
			if(1==1){
				$this->data["success"] = "Kompensasi Besar Tahun $tahun untuk ".$arr_data_baru["nama"]." berhasil dilakukan";
			
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "konversi ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			else{
				$this->data["success"] = "Kompensasi Cuti Besar Tahun $tahun untuk ".$arr_data_baru["nama"]." gagal dilakukan";
			}
		}
		
		public function tabel_cuti_besar(){
			$this->data['judul'] = "Cuti Besar";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$list = $this->m_cuti_besar->get_datatables();
			//$no = 1;
			$data = array();
			foreach ($list as $tampil) {
				$row = array();
				//$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = $tampil->tahun;
				$row[] = tanggal($tampil->tanggal_timbul);
				$row[] = tanggal($tampil->tanggal_kadaluarsa);
				$row[] = $tampil->muncul_bulan;
				$row[] = $tampil->jadi_cuti_tahunan;
				$row[] = $tampil->pakai_bulan." bulan ".$tampil->pakai_hari." hari";
				$row[] = $tampil->kompensasi_bulan." bulan";
				$row[] = $tampil->sisa_bulan." bulan ".$tampil->sisa_hari." hari";
				
				$kolom_aksi = "";
				if($this->akses["konversi"]){
					if(!empty($kolom_aksi)){
						$kolom_aksi .= " ";
					}
					$kolom_aksi .= "<button class='btn btn-info btn-xs' data-toggle='modal' data-target='#modal_konversi' onclick='tampil_konversi(this)'>Konversi</button>&nbsp;";
					$kolom_aksi .= "<button class='btn btn-info btn-xs' data-toggle='modal' data-target='#modal_perpanjang_kadaluarsa' onclick='tampil_perpanjang_kadaluarsa(this)'>Perpanjang Kadaluarsa</button>&nbsp;";
					$kolom_aksi .= "<button class='btn btn-info btn-xs' data-toggle='modal' data-target='#modal_maintenance_kuota' onclick='tampil_maintenance_kuota(this)'>Maintenance Kuota</button>&nbsp;";
				
				}
				
				if($this->akses["kompensasi"] and strcmp($tampil->bisa_konversi,"ya")==0){
					if(!empty($kolom_aksi)){
						$kolom_aksi .= " ";
					}
					$kolom_aksi .= "<button class='btn btn-warning btn-xs' data-toggle='modal' data-target='#modal_ubcb' onclick='tampil_ubcb(this)'>Catatan UBCB</button>&nbsp;";
						
					$kolom_aksi .= "<button class='btn btn-warning btn-xs' data-toggle='modal' data-target='#modal_kompensasi' onclick='tampil_kompensasi(this)'>Kompensasi</button>";
				}
				
				if($this->akses["lihat log"]){
					if(!empty($kolom_aksi)){
						$kolom_aksi .= " ";
					}
					$kolom_aksi .= "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$tampil->nama."\",".$tampil->id.")'>Lihat Log</button>";
				}
				$row[] = $kolom_aksi; 

				$data[] = $row;
				//$no++;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->m_cuti_besar->count_all(),
							"recordsFiltered" => $this->m_cuti_besar->count_filtered(),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
		
		private function cek_konversi($arr_data_lama,$arr_data_baru,$konversi_bulan,$konversi_hari){
			$return = true;
			
			if($return){
				if($arr_data_lama["konversi_bulan"]+$konversi_bulan != $arr_data_baru["konversi_bulan"]){
					$return = false;
				}
			}
			
			if($return){
				if($arr_data_lama["konversi_hari"]+$konversi_hari != $arr_data_baru["konversi_hari"]){
					$return = false;
				}
			}
			
			if($return){
				if($arr_data_lama["total_bulan"]-$konversi_bulan != $arr_data_baru["total_bulan"]){
					$return = false;
				}
			}
			
			if($return){
				if($arr_data_lama["total_hari"]+$konversi_hari != $arr_data_baru["total_hari"]){
					$return = false;
				}
			}
			
			if($return){
				if($arr_data_lama["sisa_bulan"]-$konversi_bulan != $arr_data_baru["sisa_bulan"]){
					$return = false;
				}
			}
			
			if($return){
				if($arr_data_lama["sisa_hari"]+$konversi_hari != $arr_data_baru["sisa_hari"]){
					$return = false;
				}
			}
			
			return $return;
		}
	}
	
	/* End of file cuti_besar.php */
	/* Location: ./application/controllers/osdm/cuti_besar.php */