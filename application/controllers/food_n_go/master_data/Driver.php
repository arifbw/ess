<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Driver extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'food_n_go/master_data/';
			$this->folder_model = 'kendaraan/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_master_data_driver");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			
			$this->data['judul'] = "Driver";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."driver";
			
			array_push($this->data['js_sources'],"food_n_go/master_data/driver");

			if($this->input->post()){
                //echo json_encode($this->input->post()); exit;
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['np_karyawan'] = $this->input->post("np_karyawan");
					$this->data['nama'] = $this->input->post("nama");
					$this->data['no_hp'] = $this->input->post("no_hp");
					$this->data['jenis_sim'] = $this->input->post("jenis_sim");
					$this->data['keterangan'] = $this->input->post("keterangan");
					$this->data['posisi'] = $this->input->post("posisi");
					if(!strcmp(@$this->input->post("status"),"aktif")){
						$this->data['status'] = true;
						$stt = 1;
					}
					else if(!strcmp(@$this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
						$stt = 0;
					} else{
                        $this->data['status'] = false;
						$stt = 0;
                    }
                    
                    $data_insert = [
                        'np_karyawan'=>$this->input->post("np_karyawan"),
                        'nama'=>$this->input->post("nama"),
                        'no_hp'=>$this->input->post("no_hp"),
                        'jenis_sim'=>$this->input->post("jenis_sim"),
                        'keterangan'=>$this->input->post("keterangan"),
                        'posisi'=>$this->input->post("posisi"),
                        'id_mst_kendaraan_default'=>$this->input->post("id_mst_kendaraan_default"),
                        'status'=>$stt,
                        'sumber_data'=>'form input'
                    ];
					
					//$tambah = $this->tambah($this->data['nama'],$this->data['nopol'],$this->data['status']);
					$tambah = $this->tambah($data_insert);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Driver dengan nama <b>".$this->data['nama']." | ".$this->data['np_karyawan']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['np_karyawan'] = "";
						$this->data['nama'] = "";
						$this->data['no_hp'] = "";
						$this->data['jenis_sim'] = "";
						$this->data['keterangan'] = "";
						$this->data['posisi'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['nama'] = $this->input->post("nama");
					$this->data['nama_ubah'] = $this->input->post("nama_ubah");
					$this->data['nopol'] = $this->input->post("nopol");
					$this->data['nopol_ubah'] = $this->input->post("nopol_ubah");
					$this->data['status'] = (bool)$this->input->post("status");
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}
                    
                    $data_update = [
                        'id'=>$this->input->post("id_ubah"),
                        'np_karyawan'=>$this->input->post("np_karyawan_old"),
                        'np_karyawan_ubah'=>$this->input->post("np_karyawan_ubah"),
                        'nama'=>$this->input->post("nama_old"),
                        'nama_ubah'=>$this->input->post("nama_ubah"),
                        'no_hp'=>$this->input->post("no_hp_old"),
                        'no_hp_ubah'=>$this->input->post("no_hp_ubah"),
                        'jenis_sim'=>$this->input->post("jenis_sim_old"),
                        'jenis_sim_ubah'=>$this->input->post("jenis_sim_ubah"),
                        'keterangan'=>$this->input->post("keterangan_old"),
                        'keterangan_ubah'=>$this->input->post("keterangan_ubah"),
                        'posisi'=>$this->input->post("posisi_old"),
                        'posisi_ubah'=>$this->input->post("posisi_ubah"),
                        'status'=>$this->input->post("status_old"),
                        'id_mst_kendaraan_default'=>$this->input->post("id_mst_kendaraan_default_ubah"),
                        'status_ubah'=>$this->data['status_ubah'],
                    ];

					$ubah = $this->ubah($data_update);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan driver berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['np_karyawan'] = "";
                    $this->data['nama'] = "";
                    $this->data['no_hp'] = "";
                    $this->data['jenis_sim'] = "";
                    $this->data['keterangan'] = "";
                    $this->data['posisi'] = "";
                    $this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['np_karyawan'] = "";
                    $this->data['nama'] = "";
                    $this->data['no_hp'] = "";
                    $this->data['jenis_sim'] = "";
                    $this->data['keterangan'] = "";
                    $this->data['posisi'] = "";
                    $this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['np_karyawan'] = "";
                $this->data['nama'] = "";
                $this->data['no_hp'] = "";
                $this->data['jenis_sim'] = "";
                $this->data['keterangan'] = "";
                $this->data['posisi'] = "";
                $this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_driver').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
                
                $get_kendaraan_free = $this->db->where("(a.status=1 AND a.id NOT IN (
                    SELECT b.id_mst_kendaraan_default 
                    FROM mst_driver b 
                    WHERE b.id_mst_kendaraan_default IS NOT NULL -- AND b.status=1
                ))")->get('mst_kendaraan a');
				
				$this->data["daftar_driver"] = $this->m_master_data_driver->daftar_driver();
				$this->data["mst_kendaraan"] = $get_kendaraan_free->result_array();
				
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
		
		//private function tambah($nama,$nopol,$status){
		private function tambah($data_insert){
			$return = array("status" => false, "error_info" => "");
			if($this->m_master_data_driver->cek_tambah_driver($data_insert['np_karyawan'])){
				$data = $data_insert;
				$this->m_master_data_driver->tambah($data_insert);
				
				if($this->m_master_data_driver->cek_hasil_driver($data_insert['np_karyawan'])){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_master_data_driver->data_driver($data_insert['np_karyawan']);
					
					$log_data_baru = "";
					
					foreach($data as $key => $value){
						if(!empty($log_data_baru)){
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_insert['id'],
						"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Penambahan Driver <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Driver dengan NP <b>".$data_insert['np_karyawan']."</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_master_data_driver->cek_ubah_driver($data_update);
			if($cek["status"]){
				$set = array(
                        'np_karyawan'=>$data_update['np_karyawan_ubah'],
                        'nama'=>$data_update['nama_ubah'],
                        'no_hp'=>$data_update['no_hp_ubah'],
                        'jenis_sim'=>$data_update['jenis_sim_ubah'],
                        'keterangan'=>$data_update['keterangan_ubah'],
                        'id_mst_kendaraan_default'=>$data_update['id_mst_kendaraan_default'],
                        'posisi'=>$data_update['posisi_ubah'],
                        'status'=>$data_update['status_ubah']
                );
				
				$arr_data_lama = $this->m_master_data_driver->data_driver($data_update['np_karyawan']);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$this->m_master_data_driver->ubah($set,$data_update['id']);

				if($this->m_master_data_driver->cek_hasil_driver($data_update['np_karyawan'])){
					$return["status"] = true;
					
					$log_data_baru = "";
					foreach($set as $key => $value){
						if(!empty($log_data_baru)){
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Perubahan Driver <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
	}
	
	/* End of file jenis_cuti.php */
	/* Location: ./application/controllers/master_data/jenis_cuti.php */