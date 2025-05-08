<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class daftar_obat extends CI_Controller {
		public function __construct(){
			parent::__construct();
	        $this->load->library('Excel_reader');
			$this->load->library('phpexcel');
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/ketentuan/';
			$this->folder_model = 'sikesper/';
			$this->folder_controller = 'sikesper/';
			
			$this->akses = array();
					
			$this->load->helper("tanggal_helper");
					
			$this->load->model($this->folder_model."m_daftar_obat");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Daftar Obat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index() {
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."index_obat";

			$this->data['panel_tambah'] = "in";
			
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);

					if ($_FILES['file_excel']['name'] != '') {
			            $tempFile = $_FILES['file_excel']['tmp_name'];
			            $fileName = $_FILES['file_excel']['name'];
			            $targetPath = './uploads/obat_import/';
			            $pecahnama = explode('.',$fileName);
			            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
			            $time1 = date('YmdHis');
			            $targetFile = $targetPath.'Import_obat_'.$time1.'.'.$ekstensifile;
			            $ekstensi = array('xlsx','XLSX');

			            if (in_array($ekstensifile, $ekstensi)) {
			                if (move_uploaded_file($tempFile, $targetFile)) {
			                    try {
			                        // PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
			                        $inputFileType = PHPExcel_IOFactory::identify($targetFile);
			                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
			                        $objReader->setReadDataOnly(true);
			                        //load excel file
			                        $objPHPExcel = $objReader->load($targetFile);
			                        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

			                        $i = 0;
			                        $awal = 4;
			                        $mcu = array();

			                        while ($objWorksheet->getCellByColumnAndRow(1,$awal)->getValue() != '') {
		                                $obat[$i]['kode_obat'] = $objWorksheet->getCellByColumnAndRow(1,$awal)->getValue();
		                                $obat[$i]['jenis'] = $objWorksheet->getCellByColumnAndRow(2,$awal)->getValue();
		                                $obat[$i]['kategori'] = $objWorksheet->getCellByColumnAndRow(3,$awal)->getValue();
		                                $obat[$i]['zat_aktif_obat'] = $objWorksheet->getCellByColumnAndRow(4,$awal)->getValue();
		                                $obat[$i]['merek_obat'] = $objWorksheet->getCellByColumnAndRow(5,$awal)->getValue();
		                                $obat[$i]['sediaan'] = $objWorksheet->getCellByColumnAndRow(6,$awal)->getValue();
		                                $obat[$i]['dosis'] = $objWorksheet->getCellByColumnAndRow(7,$awal)->getValue();
		                                $obat[$i]['farmasi'] = $objWorksheet->getCellByColumnAndRow(8,$awal)->getValue();
		                                $obat[$i]['keterangan'] = $objWorksheet->getCellByColumnAndRow(9,$awal)->getValue();

		                                $i++;
		                            	$awal++;
		                            }
		                            $this->data['list_obat'] = $obat;
		                            $this->data['content'] = $this->folder_view."preview_obat";
		                            $this->session->set_flashdata('success', 'Berikut adalah hasil file yang anda upload. Klik tombol simpan jika data yang anda upload sudah benar<br>Kosongkan Kotak Pencarian Jika Ingin Menyimpan Semua Data !');
			                    }
			                    catch (Exception $ee) {
			                        unlink($targetFile);
			                        die("Error ! ".$ee->getMessage());
			                    }
			                }
			                else {
			                    echo 'a';exit;
			                    echo "<script>alert('UPLOAD GAGAL, File yang Anda upload harus file .XLSX');window.location='".site_url('sikesper/ketentuan/daftar_obat')."'</script>";
			                }
			            }
			            else {
			                echo "<script>alert('UPLOAD GAGAL, File yang Anda upload harus file .XLSX');window.location='".site_url('sikesper/ketentuan/daftar_obat')."'</script>";
			            }
			        }
			        else {
			            echo "<script>alert('File harus diisi !');window.location='".site_url('sikesper/ketentuan/daftar_obat')."'</script>";
			        }
					
				}
				else{
					echo "<script>alert('Anda Tidak Memiliki Akses !');window.location='".site_url('sikesper/ketentuan/daftar_obat')."'</script>";
				}
			}
			else {
				$this->data["tanggal"] = "";
				$this->data["deskripsi"] = "";
				$this->data["panel_tambah"] = "";
				
				if($this->akses["lihat"]){
					array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
					array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
					
					$this->load->model("master_data/m_karyawan");
					
					$this->data["daftar_jenis_obat"] = $this->m_daftar_obat->daftar_jenis_obat();
					$this->data["daftar_kategori_obat"] = $this->m_daftar_obat->daftar_kategori_obat();
					if($this->akses["hapus"]) {
						$this->data["tanggal_upload"] = $this->m_daftar_obat->daftar_upload()->result_array();
					}
					else{
						$this->data["tanggal_upload"] = array();
					}
							
					$js_header_script = "<script src='".base_url('asset/sweetalert2')."/sweetalert2.js'></script>
					<script>
						$(document).ready(function() {
							$('.select2').select2();
						});
					</script>";
					
					array_push($this->data["js_header_script"],$js_header_script);
				}
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel_obat($jenis=0,$ktg=0,$tgl=0){

			$list = $this->m_daftar_obat->get_datatable($jenis, $ktg, $tgl);
			
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				
				// START OF ENCRYPT NAMA FILE*/
				$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321";
				$rand_chars = "";
				$length_rand_chars=rand(8,16);

				while(strlen($rand_chars)<$length_rand_chars){
					$rand_chars .= substr($chars,rand(0,strlen($chars)),1);
				}

				$id_txt = rand(1,10000)."|"."get_id"."|".$tampil->id."|".date('Y-m-d H:i:s')."|".$rand_chars;
				$encrypted_txt_id = $this->encrypt_decrypt('encrypt', $id_txt);
				// END OF ENCRYPT NAMA FILE*/
				
				$no++;
				$row = array();
				$row[] = $no;			
				if ($this->akses['hapus']) { 
					$row[] = '<small>'.tanggal_indonesia(date('Y-m-d', strtotime($tampil->created_at))).'<br>'.date('H:i:s', strtotime($tampil->created_at)).'</small>';
				}
				$row[] = '<small><b>'.$tampil->kode_obat.'</b></small>';
				$row[] = '<small><b>'.$tampil->jenis.'</b><br>'.$tampil->kategori.'</small>';
				$row[] = '<small>'.$tampil->zat_aktif_obat.'</small>';
				$row[] = '<small>'.$tampil->merek_obat.'</small>';
				$row[] = '<small><b>Sediaan: </b>'.$tampil->sediaan.'<br><b>Dosis: </b>'.$tampil->dosis.'<br><b>Farmasi: </b>'.$tampil->farmasi.'</small>';
				$row[] = '<small>'.$tampil->keterangan.'</small>';
				if ($this->akses['hapus']) {
					$row[] = "<button class='btn btn-danger btn-xs delete' data-id='$encrypted_txt_id' data-tgl='0'>Hapus</button>";
				}
					
				$data[] = $row;
			}

			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->m_daftar_obat->count_all($jenis, $ktg, $tgl),
				"recordsFiltered" => $this->m_daftar_obat->count_filtered($jenis, $ktg, $tgl),
				"data" => $data
			);

			header('Content-Type: application/json');
        	header("Access-Control-Allow-Origin: *");
			//output to json format
			echo json_encode($output);
		}
		
		public function save() {
			if(!strcmp($this->input->post("aksi"),"tambah")){
				$obat = $this->input->post('jumlah');
				$fail = array();
				$post = array();

				$tgl_upload = date('Y-m-d H:i:s');
				for ($i=0; $i<$obat; $i++) {
					$post = json_decode($this->input->post("post[".$i."]"));
					if(!empty($post)) {
		                $data['kode_obat'] = $post->kode_obat;
		                $data['jenis'] = $post->jenis;
		                $data['kategori'] = $post->kategori;
		                $data['zat_aktif_obat'] = $post->zat_aktif_obat;
		                $data['merek_obat'] = $post->merek_obat;
		                $data['sediaan'] = $post->sediaan;
		                $data['dosis'] = $post->dosis;
		                $data['farmasi'] = $post->farmasi;
		                $data['keterangan'] = $post->keterangan;

		                $cek = $this->db->where(array('kode_obat'=>$data['kode_obat']))->get('ess_daftar_obat');
		                if ($cek->num_rows() == 1) {
		                	$data['created_at'] = $tgl_upload;
		                	$this->db->set($data)->where('id', $cek->row()->id)->update('ess_daftar_obat');
		                	// $fail[] = (count($fail)+1).'. '.$data['np_karyawan'];
		                }
		                else {
		                	$data['created_at'] = $tgl_upload;
		                	$this->db->set($data)->insert('ess_daftar_obat');
		                }
		            }
	            }

	            if (count($fail) > 0) {
	        		$this->session->set_flashdata('warning', 'Gagal mengupload file. Data sudah ada. Mohon ulangi data dengan Kode Obat : <br>'.implode('<br>', $fail));
	        	}
	        	else {
	        		$this->session->set_flashdata('success', 'Anda berhasil mengupload semua data.');
	        	}
	        }
	        else {
	        	$this->session->set_flashdata('warning', 'Gagal mengupload file. Anda tidak memiliki hak akses!');
	        }
	        redirect(base_url('sikesper/ketentuan/daftar_obat'));
		}
				
		public function ajax_get_obat($nama_file_encrypt="")
		{	
			if(!empty($nama_file_encrypt)){
				
				$decrypted_txt = $this->encrypt_decrypt('decrypt', $nama_file_encrypt);
				list($random,$username,$nama_file,$datetime_request,$chars) = explode("|",$decrypted_txt);
				
				$detail = $this->db->where('id', $nama_file)->get('ess_daftar_obat')->row();
				$data['data'] = $detail;
				
				$this->load->view($this->folder_view."detail_obat",$data);
			}
		}

		private function is_tambah($id){
			$return = array("status" => false, "error_info" => "");
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
			return $return;
		}
		
		private function encrypt_decrypt($action, $string){
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$secret_key = 'zxfajaSsd1fjDwASjA12SAGSHga3yus'.date('Ymd');
			$secret_iv = 'zxASsadkmjku4jLOIh2jfGda5'.date('Ymd');
			// hash
			$key = hash('sha256', $secret_key);
			
			// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
			$iv = substr(hash('sha256', $secret_iv), 0, 16);
			if ( $action == 'encrypt' ) {
				$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
				$output = base64_encode($output);
			  } else if( $action == 'decrypt' ) {
					$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
					
					$pisah 				= explode('|',$output);
					$datetime_request 	= $pisah[3];
					/*
					$datetime_expired 	= date('Y-m-d H:i:s',strtotime('+10 seconds',strtotime($datetime_request))); 

					$datetime_now		= date('Y-m-d H:i:s');
					
					if($datetime_now > $datetime_expired || !$datetime_request){
						$output = false;
					}	
					*/
				}
			return $output;
		}

		public function hapus()
		{
			$encrypt_id = $this->input->post('key', true);
			$tgl = $this->input->post('tgl', true);

			if($encrypt_id=='0' && $tgl=='0') {
				echo json_encode(['status' => 'error', 'result' => 401, 'txt' => 'Anda Belum Memilih Data Upload !']);
			}
			else {
				if ($encrypt_id!='0') {
					$dcr = explode("|", $this->encrypt_decrypt('decrypt', $encrypt_id));
					$id = $dcr[2];
					$this->db->where('id', $id)->delete('ess_daftar_obat');
				}
				else if ($tgl!='0') {
					$this->db->where('tanggal_mcu', $tgl)->delete('ess_daftar_obat');
				}

				if($this->db->affected_rows() > 0){
					echo json_encode(['status' => 'success', 'result' => 200, 'txt' => 'Berhasil Menghapus Data']);
				}else{
					echo json_encode(['status' => 'error', 'result' => 401, 'txt' => 'Gagal Menghapus Data']);
				}
			}
		}	
	}
	
	/* End of file skep.php */
	/* Location: ./application/controllers/informasi/skep.php */
