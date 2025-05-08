<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Approval_lembur extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'master_data/';
			$this->folder_model = 'master_data/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/m_approval_lembur");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Approval Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."approval_lembur";

			$this->data['daftar_divisi'] = $this->m_approval_lembur->daftar_divisi();
			$this->data['panel_tambah'] = "";
			$this->data['divisi'] = "";
			$this->data['approval'] = "";

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$divisi = $this->input->post("divisi", true);
					$approval = $this->input->post("approval", true);
					
					$tambah = $this->tambah($divisi,$approval);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Approval lembur divisi <b>".$this->data['divisi']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['divisi'] = "";
						$this->data['approval'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
					$(document).ready(function() {
						$('#tabel_approval_lembur').DataTable({
							responsive: true
						});
					});
				</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_approval_lembur"] = $this->m_approval_lembur->daftar_approval_lembur();
				
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
		
		private function tambah($divisi,$approval){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_approval_lembur->cek_tambah_approval($divisi);
			if($cek){
				$data = array(
					"divisi" => $divisi,
					"approval" => $approval,
					"updated_at" => date('Y-m-d H:i:s'),
					"updated_by" => $_SESSION['no_pokok']
				);
				$this->db->set($data)->insert('mst_approval_lembur');
				
				if($this->m_approval_lembur->cek_hasil_approval($divisi,$approval)){
					$arr_data_insert = $this->m_approval_lembur->data_approval($divisi);
					
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
					$return["error_info"] = "Perubahan Approval Lembur <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}
			return $return;
		}
		
		public function change_approval(){
			$this->data['judul'] = "Approval Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$id = $this->input->post("id", true);

			$arr_data_lama = $this->db->where('id', $id)->get('mst_approval_lembur')->row_array();
			$log_data_lama = "";
			foreach($arr_data_lama as $key => $value) {
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}

			$set['approval'] = $this->input->post("approval", true);
			$set["updated_at"] = date('Y-m-d H:i:s');
			$set["updated_by"] = $_SESSION['no_pokok'];
			$this->db->set($set)->where('id', $id)->update('mst_approval_lembur');

			if($this->m_approval_lembur->cek_hasil_approval($arr_data_lama['divisi'],$set['approval'])) {
				$arr_data_insert = $this->m_approval_lembur->data_approval($arr_data_lama['divisi']);
				$set['divisi'] = $arr_data_insert['divisi'];
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
					"id_target" => $arr_data_insert['id'],
					"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);

				$return["judul"] = 'Berhasil';
				$return["alert"] = 'success';
				$return["txt"] = "Perubahan Approval Lembur Berhasil Dilakukan.";
			}
			else{
				$return["judul"] = 'Gagal';
				$return["alert"] = 'error';
				$return["txt"] = "Perubahan Approval Lembur Gagal Dilakukan.";
			}

			echo json_encode($return);
		}

		public function hapus(){
			$this->data['judul'] = "Approval Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$id = $this->input->post("id", true);

			$arr_data_lama = $this->db->where('id', $id)->get('mst_approval_lembur')->row_array();
			$log_data_lama = "";
			foreach($arr_data_lama as $key => $value) {
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}

			$this->db->where('id', $id)->delete('mst_approval_lembur');

			if($this->db->affected_rows()) {
				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $id,
					"deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);

				$return["judul"] = 'Berhasil';
				$return["alert"] = 'success';
				$return["txt"] = "Approval Lembur Berhasil Dihapus.";
			}
			else{
				$return["judul"] = 'Gagal';
				$return["alert"] = 'error';
				$return["txt"] = "Perubahan Approval Lembur Gagal Dihapus.";
			}

			echo json_encode($return);
		}
	}
	
	/* End of file jadwal_kerja.php */
	/* Location: ./application/controllers/master_data/jadwal_kerja.php */