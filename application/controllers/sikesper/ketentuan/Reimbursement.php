<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Reimbursement extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/reimbursement/';
			$this->folder_model = 'sikesper/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/M_reimbursement");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}

		public function index(){
			$this->data['judul'] = "Tata Cara Pengobatan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);

			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."daftar_reimbursement";
			
			array_push($this->data['js_sources'],"sikesper/daftar_obat");

			if($this->input->post()){

				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);

					if(!strcmp($this->input->post("status"),"1")) {
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"0")){
						$this->data['status'] = false;
					} else{
                        $this->data['status'] = false;
                    }

                    $insert['no_urut'] = $this->input->post('no_urut');
                    $insert['judul'] = $this->input->post('judul');
                    $insert['tata_cara'] = $this->input->post('tata_cara');
                    $insert['status'] = $this->input->post('status');


					$tambah = $this->tambah($insert, $this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Tata Cara <b>".$insert['judul']."</b> berhasil ditambahkan.";
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
					
					$this->data['id_ubah'] = $this->input->post("id");
					$this->data['judul_ubah'] = $this->input->post("judul");
					$this->data['tata_cara_ubah'] = addslashes($this->input->post("tata_cara"));
					$this->data['status'] = (bool)$this->input->post("status_lama");

					if(!strcmp($this->input->post("status"),"1")){
						$this->data['status_ubah'] = true;
					}
					else if(!strcmp($this->input->post("status"),"0")){
						$this->data['status_ubah'] = false;
					}
                    
                    $data_update = $this->input->post();
					$ubah = $this->ubah($data_update);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan Tata Cara Berhasil Dilakukan.";
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
									$('#tabel_daftar_reimburse').DataTable({
										responsive: true
									});
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
				$this->data["daftar_reimburse"] = $this->M_reimbursement->daftar_reimburse();
				
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

		private function tambah($data)
		{
			$return = array("status" => false, "error_info" => "");

			$data['created'] = date('Y-m-d H:i:s');
			$this->M_reimbursement->insert($data);
			$id_ = $this->db->insert_id();

			if($this->M_reimbursement->cek_daftar_reimburse($id_)){
				$return["status"] = true;

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
					"id_target" => $id_,
					"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);

				$this->m_log->tambah($log);
			}else{
				$return["status"] = false;
				$return["error_info"] = "Penambahan Daftar Tata Cara <b>Gagal</b> Dilakukan.";
			}

			return $return;
		}

		private function ubah($data_update){
			$return = array("status" => false, "error_info" => "");

			$cek = $this->M_reimbursement->cek_daftar_reimburse($data_update['id']);
			if($cek){
				$set = array(
					"judul" => $data_update['judul'],
					"tata_cara" => $data_update['tata_cara'],
					"no_urut" => $data_update['no_urut'],
					"status"=>$data_update['status']
				);
				
				$arr_data_lama = $cek;
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
				
				$set['updated'] = date('Y-m-d H:i:s');
				$update = $this->M_reimbursement->update($set, $data_update['id']);

				if($update){
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
						"id_target" => $arr_data_lama->id,
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
					$return["error_info"] = "Perubahan Daftar Tata Cara <b>Gagal</b> Dilakukan.";
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

			$data = $this->M_reimbursement->cek_daftar_reimburse($id);

			if($data){
				echo json_encode(['status' => 'success', 'result' => [
					'id' => $id,
					'judul' => $data->judul,
					'no_urut' => $data->no_urut,
					'tata_cara' => $data->tata_cara,
					'status' => $data->status
				]]);
			}else{
				echo json_encode(['status' => 'failed', 'result' => null]);
			}
		}

		//Upload image summernote
	    public function upload_image(){
	    	$this->load->library('upload');
	        if(isset($_FILES["image"]["name"])){
	            $config['upload_path'] = './uploads/images/sikesper/reimburse/';
	            $config['allowed_types'] = 'jpg|jpeg|png|gif';
	            $this->upload->initialize($config);
	            if(!$this->upload->do_upload('image')){
	                $this->upload->display_errors();
	                echo json_encode(['result' => $this->upload->display_errors()]);
	            }else{
	                $data = $this->upload->data();
	                //Compress Image
	                $config['image_library']='gd2';
	                $config['source_image']='./uploads/images/sikesper/reimburse/'.$data['file_name'];
	                $config['create_thumb']= FALSE;
	                $config['maintain_ratio']= TRUE;
	                $config['quality']= '60%';
	                $config['width']= 800;
	                $config['height']= 800;
	                $config['new_image']= './uploads/images/sikesper/reimburse/'.$data['file_name'];
	                $this->load->library('image_lib', $config);
	                $this->image_lib->resize();
	                echo base_url().'uploads/images/sikesper/reimburse/'.$data['file_name'];
	            }
	        }
	    }
	 
	    //Delete image summernote
	    public function delete_image(){
	        $src = $this->input->post('src');
	        $file_name = str_replace(base_url(), '', $src);
	        if(unlink($file_name))
	        {
	            echo 'File Delete Successfully';
	        }
	    }
        
        function read_more($_judul) {
            try {
                $judul = urldecode($_judul);
                $get = $this->db->select('id,no_urut,judul,tata_cara')->where('judul',$judul)->get('ess_cara_reimburse')->row();
                if($get){
                    $this->data['judul'] = "Tata Cara Pengobatan";
                    $this->data["navigasi_menu"] = menu_helper();
                    $this->data['content'] = $this->folder_view."read_more";
                    $this->data['val'] = $get;
                    $this->load->view('template',$this->data);
                }else{
                    echo json_encode(['status' => 'failed', 'result' => null]);
                }
            } catch(Exception $e){
                echo 'Error';
            }
		}
	}
?>