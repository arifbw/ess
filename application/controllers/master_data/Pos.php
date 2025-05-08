<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pos extends CI_Controller {
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
			
			$this->load->model("administrator/m_pengguna_table");
			$this->load->model($this->folder_model."/m_pos");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Pos";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pos";
			
			array_push($this->data['js_sources'],"master_data/pos");

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);
				
					$this->data['nama'] = $this->input->post("nama");
					$this->data['kode_pos'] = $this->input->post("kode_pos");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					}
					
					$tambah = $this->tambah($this->data['nama'],$this->data['kode_pos'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Pos dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['kode_pos'] = "";
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
					$this->data['kode_pos'] = $this->input->post("kode_pos");
					$this->data['kode_pos_ubah'] = $this->input->post("kode_pos_ubah");
					$this->data['status'] = (bool)$this->input->post("status");
					if(!strcmp($this->input->post("status_ubah"),"aktif")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status_ubah"),"non aktif")){
						$this->data['status_ubah'] = false;
					}

					$ubah = $this->ubah($this->data['nama'],$this->data['nama_ubah'],$this->data['kode_pos_ubah'],$this->data['status_ubah']);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan Pos berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['kode_pos'] = "";
					$this->data['status'] = "";
				}
				else{
					$this->data['panel_tambah'] = "";
					$this->data['nama'] = "";
					$this->data['kode_pos'] = "";
					$this->data['status'] = "";
				}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['kode_pos'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {
									$('#tabel_pos').DataTable({
										responsive: true
									});
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_pos"] = $this->m_pos->daftar_pos();
				
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
		
		private function tambah($nama,$kode_pos,$status){
			$return = array("status" => false, "error_info" => "");
			if($this->m_pos->cek_tambah_pos($nama)){
				$data = array(
							"nama" => $nama,
							"kode_pos" => $kode_pos,
							"status" => $status
						);
				$this->m_pos->tambah($data);
				
				if($this->m_pos->cek_hasil_pos($nama,$kode_pos,$status)){
					$return["status"] = true;
					
					$arr_data_insert = $this->m_pos->data_pos($nama);
					
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
					$return["error_info"] = "Penambahan Pos <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = "Pos dengan nama <b>$nama</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($nama,$nama_ubah,$kode_pos_ubah,$status_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_pos->cek_ubah_pos($nama,$nama_ubah);
			if($cek["status"]){
				$set = array("nama" => $nama_ubah, "kode_pos"=>$kode_pos_ubah, "status"=>$status_ubah);
				
				$arr_data_lama = $this->m_pos->data_pos($nama);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$this->m_pos->ubah($set,$nama);

				if($this->m_pos->cek_hasil_pos($nama_ubah,$kode_pos_ubah,$status_ubah)){
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
					$return["error_info"] = "Perubahan Pos <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}

		public function hak_akses($id_pos=null){
			$this->data['judul'] = "Pos";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pos_akses";
			
			array_push($this->data['js_sources'],"master_data/pos");

			if($this->input->post()) {
				// if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["hak akses"]);
				
					$this->data['nama'] = $this->input->post("nama");
					$this->data['kode_pos'] = $this->input->post("kode_pos");
					if(!strcmp($this->input->post("status"),"aktif")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"non aktif")){
						$this->data['status'] = false;
					}
					
					$tambah = $this->tambah($this->data['nama'],$this->data['kode_pos'],$this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Pos dengan nama <b>".$this->data['nama']."</b> berhasil ditambahkan.";
						$this->data['panel_tambah'] = "";
						$this->data['nama'] = "";
						$this->data['kode_pos'] = "";
						$this->data['status'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
			}
			else{
				$this->data['panel_tambah'] = "";
				$this->data['nama'] = "";
				$this->data['kode_pos'] = "";
				$this->data['status'] = "";
			}
			
			if($this->akses["hak akses"]) {
				// $js_header_script = $this->load->view('master_data/ajax/pos');
				
				// array_push($this->data["js_header_script"],$js_header_script);
				
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

        public function mst_pengguna() {
			$this->load->model($this->folder_model."m_pengguna_table");
			$pos = $this->input->post('pos', true);

			$list = $this->m_pengguna_table->get_datatables('1', $pos);
			// echo $this->db->last_query();exit;
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$row = array();
                $no++;
				$row[] = $no;
				$row[] = $tampil->no_pokok;
				$row[] = $tampil->nama;
				$row[] = $tampil->kode_unit;
				$row[] = $tampil->nama_unit;
				$row[] = "<a class='btn btn-primary btn-xs tambah_data' data-np='".$tampil->no_pokok."'>Tambah</a> ";
								
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->m_pengguna_table->count_all('1', $pos),
							"recordsFiltered" => $this->m_pengguna_table->count_filtered('1', $pos),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
	
        public function akses_pengguna() {
        	$pos = $this->input->post('pos');

			$list = $this->db->join('mst_karyawan b', 'FIND_IN_SET(b.no_pokok, a.no_pokok)')->select('b.no_pokok,b.nama,b.kode_unit,b.nama_unit')->where('a.id', $pos)->get('mst_pos a');
			$data = array();
			$no = 1;
			foreach ($list->result() as $tampil) {
				$row = array();
				$row[] = $no++;
				$row[] = $tampil->no_pokok;
				$row[] = $tampil->nama;
				$row[] = $tampil->kode_unit;
				$row[] = $tampil->nama_unit;
				$row[] = "<a class='btn btn-danger btn-xs hapus_data' data-np='".$tampil->no_pokok."'>Hapus</a> ";
								
				$data[] = $row;
			}

			$output = array(
					"recordsTotal" => $list->num_rows(),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}

        public function save_kry() {
        	$pos = $this->input->post('pos');
        	$np = (string)$this->input->post('np');

        	$get_pos = $this->db->where('id', $pos)->where('status', '1')->get('mst_pos');
        	if($get_pos->num_rows()>0) {
        		if ($get_pos->row()->no_pokok==null || $get_pos->row()->no_pokok=='')
        			$arr_np = array();
        		else 
        			$arr_np = explode(',', $get_pos->row()->no_pokok);
        		array_push($arr_np, $np);
        		$set_np = implode(',', $arr_np);
        		$this->db->where('id', $get_pos->row()->id)->set('no_pokok', $set_np)->update('mst_pos');

        		if ($this->db->affected_rows() > 0)
        			echo json_encode(array("status" => true));
        		else 
        			echo json_encode(array("status" => false));
        	}
		}
	
        public function hapus_kry() {
        	$pos = $this->input->post('pos');
        	$np = (string)$this->input->post('np');

        	$get_pos = $this->db->where('id', $pos)->where('status', '1')->get('mst_pos');
        	if($get_pos->num_rows()>0) {
        		$arr_np = explode(',', $get_pos->row()->no_pokok);
        		$arr_np = array_diff($arr_np, [$np]);
        		$set_np = implode(',', $arr_np);
        		$this->db->where('id', $get_pos->row()->id)->set('no_pokok', $set_np)->update('mst_pos');

        		if ($this->db->affected_rows() > 0)
        			echo json_encode(array("status" => true));
        		else 
        			echo json_encode(array("status" => false));
        	}
		}
	
	}

	/* End of file pos.php */
	/* Location: ./application/controllers/master_data/pos.php */