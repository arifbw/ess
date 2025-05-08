<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Info_provider extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/provider/';
			$this->folder_model = 'sikesper/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."/M_provider");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}

		public function index(){
			$this->data['judul'] = "Info RS dan Klinik Provider";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);

			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."daftar_provider";
			
			// array_push($this->data['js_sources'],"sikesper/daftar_obat");

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

                    $insert['nama'] = $this->input->post('nama');
                    $insert['tipe'] = $this->input->post('tipe');
                    $insert['no_telp'] = $this->input->post('no_telp');
                    $insert['id_provinsi'] = $this->input->post('id_provinsi');
                    $insert['id_kabupaten'] = $this->input->post('id_kabupaten');
                    $insert['alamat'] = $this->input->post('alamat');
                    $insert['catatan'] = $this->input->post('catatan');
                    $insert['latitude'] = $this->input->post('latitude');
                    $insert['longitude'] = $this->input->post('longitude');
                    $insert['aktif'] = $this->input->post('status');


					$tambah = $this->tambah($insert, $this->data['status']);
					
					$this->data['panel_tambah'] = "in";

					if($tambah['status']){
						$this->data['success'] = "Tata Cara <b>".$insert['nama']."</b> berhasil ditambahkan.";
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
					
					$insert['id'] = $this->input->post('id');
					$insert['nama'] = $this->input->post('nama');
                    $insert['tipe'] = $this->input->post('tipe');
                    $insert['no_telp'] = $this->input->post('no_telp');
                    $insert['id_provinsi'] = $this->input->post('id_provinsi');
                    $insert['id_kabupaten'] = $this->input->post('id_kabupaten');
                    $insert['alamat'] = $this->input->post('alamat');
                    $insert['latitude'] = $this->input->post('latitude');
                    $insert['longitude'] = $this->input->post('longitude');
                    $insert['catatan'] = $this->input->post('catatan');
                    $insert['aktif'] = $this->input->post('status');

					if(!strcmp($this->input->post("status"),"1")){
						$this->data['status'] = true;
					}
					else if(!strcmp($this->input->post("status"),"0")){
						$this->data['status'] = false;
					}
                    
					$ubah = $this->ubah($insert);
					
					if($ubah["status"]){
						$this->data['success'] = "Perubahan Provider Kesehatan Berhasil Dilakukan.";
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
                array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				$js_header_script = "
							<script src='".base_url('asset/select2')."/select2.min.js'></script>

							<script>
								$(document).ready(function() {
									/*$('#tabel_daftar_provider').DataTable({
										responsive: true
									});*/
                                    reload_table();
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
                $this->data["filter_kabupaten"] = $this->M_provider->filter_kabupaten();
				//$this->data["daftar_provider"] = $this->M_provider->daftar_provider();
				
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

		public function form($action, $id=null){
			$this->data['judul'] = "Info RS dan Klinik Provider";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);

			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."form";
			
			array_push($this->data['js_sources'],"sikesper/daftar_obat");

			$this->data['panel_tambah'] = "";
			$this->data['nama'] = "";
			$this->data['status'] = "";
			
			if($this->akses["tambah"]){
				$js_header_script = "
							<script src='".base_url('asset/select2')."/select2.min.js'></script>

							<script>
								$(document).ready(function() {
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);

				if($id != ''){
					$this->data['data_provider'] = $this->M_provider->cek_daftar_provider($id);
					if($this->data['data_provider']){
						$this->data["action"] = "ubah";
						$this->data["id"] = $id;
					}else{
						redirect(site_url('sikesper/info_provider'));
					}
				}else{

					$this->data["action"] = "tambah";

				}
				
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
			
			$id_ = $this->M_provider->insert($data);;

			if($this->M_provider->cek_daftar_provider($id_)){
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

			$cek = $this->M_provider->cek_daftar_provider($data_update['id']);
			if($cek){
				$set = array(
					"nama" => $data_update['nama'],
					"tipe" => $data_update['tipe'],
					"no_telp" => $data_update['no_telp'],
					"id_provinsi" => $data_update['id_provinsi'],
					"id_kabupaten" => $data_update['id_kabupaten'],
					"alamat" => $data_update['alamat'],
					"catatan" => $data_update['catatan'],
					"latitude" => $data_update['latitude'],
					"longitude" => $data_update['longitude'],
					"aktif"=>$data_update['aktif']
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
				$update = $this->M_provider->update($set, $data_update['id']);

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

			$data = $this->M_provider->cek_daftar_provider($id);

			if($data){
				echo json_encode(['status' => 'success', 'result' => [
					'id' => $id,
					'nama' => $data->nama,
					'tipe' => $data->tipe,
					'no_telp' => $data->no_telp,
					'provinsi' => $data->id_provinsi,
					'nama_provinsi' => $data->provinsi,
					'kabupaten' => $data->id_kabupaten,
					'nama_kabupaten' => $data->kabupaten,
					'alamat' => $data->alamat,
					'latitude' => $data->latitude,
					'longitude' => $data->longitude,
					'catatan' => $data->catatan,
					'aktif' => $data->aktif
				]]);
			}else{
				echo json_encode(['status' => 'failed', 'result' => null]);
			}
		}

		public function show($id)
		{
			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

			$data = $this->M_provider->cek_daftar_provider($id);

			if($data){
				$view = $this->load->view($this->folder_view.'detail', 
					[
						'id' => $id,
						'nama' => $data->nama,
						'tipe' => $data->tipe,
						'no_telp' => $data->no_telp,
						'provinsi' => $data->id_provinsi,
						'nama_provinsi' => $data->provinsi,
						'kabupaten' => $data->id_kabupaten,
						'nama_kabupaten' => $data->kabupaten,
						'alamat' => $data->alamat,
						'latitude' => $data->latitude,
						'longitude' => $data->longitude,
						'catatan' => $data->catatan,
						'aktif' => $data->aktif
					], TRUE
				);
				
				echo json_encode(['status' => 'success', 'result' => $view]);
			}else{
				echo json_encode(['status' => 'failed', 'result' => null]);
			}
		}

		public function daftar_provinsi()
		{
			header('Content-Type: application/json');
	    	header("Access-Control-Allow-Origin: *");

			$data = $this->M_provider->daftar_provinsi();

			echo json_encode(['results' => $data, 'status' => 200]);
		}

		public function daftar_kabupaten($provinsi)
		{
			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

        	if($provinsi != ''){
				$data = $this->M_provider->daftar_kabupaten($provinsi);

				echo json_encode(['results' => $data, 'status' => 200]);
			}else{
				echo json_encode(['results' => NULL, 'status' => 404]);
			}
		}

		//Upload image summernote
	    public function upload_image(){
	    	$this->load->library('upload');
	        if(isset($_FILES["image"]["name"])){
	            $config['upload_path'] = './uploads/images/sikesper/provider/';
	            $config['allowed_types'] = 'jpg|jpeg|png|gif';
	            $this->upload->initialize($config);
	            if(!$this->upload->do_upload('image')){
	                $this->upload->display_errors();
	                echo json_encode(['result' => $this->upload->display_errors()]);
	            }else{
	                $data = $this->upload->data();
	                //Compress Image
	                $config['image_library']='gd2';
	                $config['source_image']='./uploads/images/sikesper/provider/'.$data['file_name'];
	                $config['create_thumb']= FALSE;
	                $config['maintain_ratio']= TRUE;
	                $config['quality']= '60%';
	                $config['width']= 800;
	                $config['height']= 800;
	                $config['new_image']= './uploads/images/sikesper/provider/'.$data['file_name'];
	                $this->load->library('image_lib', $config);
	                $this->image_lib->resize();
	                echo base_url().'uploads/images/sikesper/provider/'.$data['file_name'];
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

	    public function import_data()
	    {
	        // Load plugin PHPExcel nya
	        include APPPATH.'third_party/phpexcel/PHPExcel.php';

	        $config['upload_path'] = './uploads/sikesper/provider/';
	        $config['allowed_types'] = 'xlsx|xls|csv';
	        $config['max_size'] = '10000';
	        $config['overwrite'] = true;
            $config['file_name'] = 'provider-'.date('YmdHis');
            $this->load->library('upload');
	        $this->upload->initialize($config);
            if(!$this->upload->do_upload('import_excel')){
            	$status = 400;
            	$res = 'Terjadi Kesalahan';
	        } else {
	            $data_upload = $this->upload->data();

	            $excelreader       = new PHPExcel_Reader_Excel2007();
	            $loadexcel         = $excelreader->load($config['upload_path'].$data_upload['file_name']); // Load file yang telah diupload ke folder excel
	            $sheet             = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

	            $data = array();

	            $numrow = 1;
	            $tgl_import = date('Y-m-d H:i:s');
	            foreach($sheet as $row){
	            	if($numrow > 3) {
		            	$kab = $this->db->where('nama', $row['D'])->get('kabupaten')->row();
		            	$cek = $this->db->where(array('nama'=>$row['B'], 'tipe'=>$row['C']))->get('ess_provider_kesehatan');

		            	if ($cek->num_rows() == 0) 
		            		$cek_tgl = 'created';
		            	else
		            		$cek_tgl = 'updated';

	                    $data = array(
	                        'nama' => $row['B'],
	                        'tipe' => $row['C'],
	                        'id_provinsi' => $kab->kode_prop,
	                        'id_kabupaten' => $kab->kode_wilayah,
	                        'alamat' => $row['E'],
	                        'latitude' => $row['G'],
	                        'longitude' => $row['H'],
	                        'no_telp' => $row['F'],
	                        'catatan' => $row['I'],
	                        $cek_tgl => $tgl_import
	                    );

		            	if ($cek->num_rows() == 0) 
	            			$this->db->set($data)->insert('ess_provider_kesehatan');
	            		else
	            			$this->db->where(array('nama'=>$row['B'], 'tipe'=>$row['C']))->set($data)->update('ess_provider_kesehatan');
	                }
	                $numrow++;
	            }
	            $status = 200;
            	$res = 'Berhasil Melakukan Import Data';
	        }

	        header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");

        	echo json_encode([
        		'status' => $status,
        		'response' => $res
        	]);
	    }
        
        function tabel($kota){
            $judul = "Info RS dan Klinik Provider";
			$id_modul = $this->m_setting->ambil_id_modul($judul);
			$akses = akses_helper($id_modul);
            
            $list = $this->M_provider->daftar_provider($kota);
					
			$data = array();
			$no = 0;
			foreach ($list as $tampil) {
                $btn = '<button type="button" class="btn btn-success btn-xs detail" data-id="'.$tampil->id.'" data-toggle="modal" data-target="#modal-detail">Detail</button>';
                
                if(@$akses["ubah"] || @$akses["lihat log"]){
                    if(@$akses["ubah"]){
                        $btn .= ' <a href="'.base_url('sikesper/ketentuan/info_provider/form/ubah/'.$tampil->id).'" class="btn btn-primary btn-xs ubah">Ubah</a>';
                    }
                }
                
                $no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->nama;
				$row[] = $tampil->tipe;
				$row[] = $tampil->no_telp;
				$row[] = $tampil->alamat;
				$row[] = $btn;
					
				$data[] = $row;
            }
            
            $output = array(
                "data" => $data
            );
			echo json_encode($output);
        }
	}
?>