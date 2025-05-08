<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Daftar_obat extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/ketentuan/';
			$this->folder_model = 'sikesper/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_daftar_obat");
			$this->load->model($this->folder_model."/m_master_data_kategori_obat");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index($jns=null){
			$jenis = ucwords(str_replace("_", " ", $jns));
			if ($jns==null)
				$this->data['judul'] = "Daftar Obat";
			else 
				$this->data['judul'] = "Obat ".$jenis;
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."daftar_obat";
			
			array_push($this->data['js_sources'],"sikesper/daftar_obat");

			$this->data["parent_kategori"] = $this->m_daftar_obat->daftar_obat_parent();
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$insert['no_kode'] = $this->input->post("no_kode");
					$insert['merk_obat'] = $this->input->post("merk_obat");
					$insert['zat_aktif'] = $this->input->post("zat_aktif");
					$insert['id_kategori'] = $this->input->post("id_kategori");
					$insert['sediaan'] = $this->input->post("sediaan");
					$insert['dosis'] = $this->input->post("dosis");
					$insert['farmasi'] = $this->input->post("farmasi");
					$jenis = $this->m_master_data_kategori_obat->kategori_obat_new($insert['id_kategori'])['jenis'];
					if ($jenis=='umum')
						$insert['cover'] = $this->input->post("cover");
					$insert['keterangan'] = $this->input->post("keterangan");
					$insert['status'] = $this->input->post("status");
					if(!strcmp($this->input->post("status"),"1")) {
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"0")){
						$this->data['status'] = false;
					} else{
                        $this->data['status'] = false;
                    }
					$tambah = $this->tambah($insert, $this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Daftar obat dengan merk <b>".$insert['merk_obat']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['no_kode'] = $this->input->post("no_kode");
					$this->data['merk_obat'] = $this->input->post("merk_obat");
					$this->data['zat_aktif'] = $this->input->post("zat_aktif");
					$this->data['id_kategori'] = $this->input->post("id_kategori");
					$this->data['sediaan'] = $this->input->post("sediaan");
					$this->data['dosis'] = $this->input->post("dosis");
					$this->data['farmasi'] = $this->input->post("farmasi");
					$jenis = $this->m_master_data_kategori_obat->kategori_obat_new($this->data['id_kategori'])['jenis'];
					if ($jenis=='umum')
						$this->data['cover'] = $this->input->post("cover");
					else if ($jenis=='kondisi khusus')
						$this->data['keterangan'] = $this->input->post("keterangan");
					$this->data['status'] = $this->input->post("status");

					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}
                    
                    $data_update = $this->input->post();
					$ubah = $this->ubah($data_update);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan Daftar Obat Berhasil Dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "
							<script src='".base_url('asset/select2')."/select2.min.js'></script>
							<script>
								$(document).ready(function() {
									$('#tabel_daftar_obat').DataTable();
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_obat"] = $this->m_daftar_obat->daftar_obat(null,$jenis);
				
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
		
		public function change_jenis($jenis){
			echo json_encode($this->m_daftar_obat->daftar_obat(null, str_replace('_', ' ', $jenis)));
		}

		private function tambah($data){
			$return = array("status" => false, "error_info" => "");
			if($this->m_daftar_obat->cek_tambah_daftar_obat($data['merk_obat'], $data['zat_aktif'], $data['id_kategori'])){
				$data['created'] = date('Y-m-d H:i:s');
				$this->m_daftar_obat->tambah($data);
				$id_ = $this->db->insert_id();
				
				if($this->m_daftar_obat->cek_hasil_daftar_obat($data['merk_obat'], $data['zat_aktif'], $data['id_kategori'], $data['status'])){
					$return["status"] = true;
					$arr_data_insert = $this->m_daftar_obat->daftar_obat($id_);
					
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
					$return["error_info"] = "Penambahan Daftar Obat <b>Gagal</b> Dilakukan.";
				}
			}
			else {
				$return["status"] = false;
				$return["error_info"] = "Daftar Obat dengan nama <b>".$data['nama_kategori']."</b> dan jenis <b>".$data['jenis']."</b> sudah ada.";
			}
			return $return;
		}
	
		//private function ubah($nama,$nama_ubah,$nopol_ubah,$status_ubah){
		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");
			//$cek = $this->m_daftar_obat->cek_ubah_daftar_obat($nama,$nama_ubah);
			$cek = $this->m_daftar_obat->cek_ubah_daftar_obat($data_update);
			if($cek["status"]){
				$set = array(
					'no_kode' => $data_update['no_kode'],
					'merk_obat' => $data_update['merk_obat'],
					'zat_aktif' => $data_update['zat_aktif'],
					'id_kategori' => $data_update['id_kategori'],
					'sediaan' => $data_update['sediaan'],
					'dosis' => $data_update['dosis'],
					'farmasi' => $data_update['farmasi'],
					'cover' => $data_update['cover'],
					'keterangan' => $data_update['keterangan'],
					'status' => $data_update['status']
				);
				
				$arr_data_lama = $this->m_daftar_obat->daftar_obat($data_update['id']);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$set['updated'] = date('Y-m-d H:i:s');
				
				$this->m_daftar_obat->ubah($set, $data_update['id']);

				if($this->m_daftar_obat->cek_hasil_daftar_obat($data_update['merk_obat'],$data_update['zat_aktif'],$data_update['id_kategori'],$data_update['status'])){
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
					$return["error_info"] = "Perubahan Daftar Obat <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}

		public function detail($id)
		{
			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

			$data = $this->m_daftar_obat->cekDaftarObat($id);

			if($data){
				echo json_encode(['status' => 'success', 'result' => [
					'id' => $id,
					'no_kode' => $data->no_kode,
					'id_kategori' => $data->id_kategori,
					'zat_aktif' => $data->zat_aktif,
					'merk_obat' => $data->merk_obat,
					'sediaan' => $data->sediaan,
					'dosis' => $data->dosis,
					'farmasi' => $data->farmasi,
					'cover' => $data->cover,
					'jenis' => $data->jenis,
					'keterangan' => $data->keterangan,
					'status' => $data->status
				]]);
			}else{
				echo json_encode(['status' => 'failed', 'result' => null]);
			}
		}
        
        function ubah_view(){
        	if ($this->input->is_ajax_request()) {
        		$id_ = $this->input->post('id');
        		$detail = $this->m_daftar_obat->daftar_obat($id_);
        		$data['status'] = true;
        		$data['view'] = $this->load->view('sikesper/ketentuan/ubah_obat', $detail);
        	}
        	else {
        	}
            echo json_encode($data);
        }

        function update_data(){
            echo json_encode($this->input->post());
        }
	}
	
	/* End of file jenis_cuti.php */
	/* Location: ./application/controllers/master_data/jenis_cuti.php */