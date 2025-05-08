<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Skep extends CI_Controller {
		public function __construct(){
			parent::__construct();
	        $this->load->library('Excel_reader');
			$this->load->library('phpexcel');
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'informasi/';
			$this->folder_model = 'informasi/';
			$this->folder_controller = 'informasi/';
			
			$this->akses = array();
					
			$this->load->helper("tanggal_helper");
					
			$this->load->model($this->folder_model."m_skep");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "SKEP";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index() {
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."skep";

			$this->data['panel_tambah'] = "in";
			
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);

					if ($_FILES['file_excel']['name'] != '') {
			            $tempFile = $_FILES['file_excel']['tmp_name'];
			            $fileName = $_FILES['file_excel']['name'];
			            $targetPath = './uploads/skep_import/';
			            $pecahnama = explode('.',$fileName);
			            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
			            $time1 = date('YmdHis');
			            $targetFile = $targetPath.'Import_SKEP_'.$time1.'.'.$ekstensifile;
			            $ekstensi = array('xlsx','XLSX');

			            if (in_array($ekstensifile, $ekstensi)) {
			                if (move_uploaded_file($tempFile, $targetFile)) {
			                    try {
			                        PHPExcel_Settings::setZipClass(PHPExcel_Settings::PCLZIP);
			                        $inputFileType = PHPExcel_IOFactory::identify($targetFile);
			                        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
			                        $objReader->setReadDataOnly(true);
			                        //load excel file
			                        $objPHPExcel = $objReader->load($targetFile);
			                        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

			                        $i = 0;
			                        $awal = 3;

			                        while ($objWorksheet->getCellByColumnAndRow(1,$awal)->getValue() != '') {
		                                $np = $objWorksheet->getCellByColumnAndRow(1,$awal)->getValue();
		                                $nama = $objWorksheet->getCellByColumnAndRow(2,$awal)->getValue();
		                                $nomor = $objWorksheet->getCellByColumnAndRow(3,$awal)->getValue();
		                                $tanggal = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow(4,$awal)->getValue()));
		                                $tanggal_tampil = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow(5,$awal)->getValue()));
		                                $file_umum = $objWorksheet->getCellByColumnAndRow(6,$awal)->getValue();
		                                $file_individu = $objWorksheet->getCellByColumnAndRow(7,$awal)->getValue();
		                                
		                                $skep[$i]['np'] = $np;
		                                $skep[$i]['nama'] = $nama;
		                                $skep[$i]['nomor'] = $nomor;
		                                $skep[$i]['tanggal'] = $tanggal;
		                                $skep[$i]['tanggal_tampil'] = $tanggal_tampil;
		                                $skep[$i]['file_umum'] = $file_umum;
		                                $skep[$i]['file_individu'] = $file_individu;
		                                // $skep[$i]['keterangan'] = "ok";

		                                $i++;
		                            	$awal++;
		                            }
		                            $this->data['list_skep'] = $skep;
		                            $this->data['content'] = $this->folder_view."preview_skep";
		                            $this->session->set_flashdata('success', 'Berikut adalah hasil file yang anda upload. Klik tombol simpan jika data yang anda upload sudah benar');
			                    }
			                    catch (Exception $ee) {
			                        unlink($targetFile);
			                        die("Error ! ".$ee->getMessage());
			                    }
			                }
			                else {
			                    echo 'a';exit;
			                    echo "<script>alert('UPLOAD GAGAL, File yang Anda upload harus file .XLSX');window.location='".site_url('informasi/skep')."'</script>";
			                }
			            }
			            else {
			                echo "<script>alert('UPLOAD GAGAL, File yang Anda upload harus file .XLSX');window.location='".site_url('informasi/skep')."'</script>";
			            }
			        }
			        else {
			            echo "<script>alert('File harus diisi !');window.location='".site_url('informasi/skep')."'</script>";
			        }
					
				}
				else{
					echo "<script>alert('Anda Tidak Memiliki Akses !');window.location='".site_url('informasi/skep')."'</script>";
				}
			}
			else{
				$this->data["tanggal"] = "";
				$this->data["deskripsi"] = "";
				$this->data["panel_tambah"] = "";
				
				if($this->akses["lihat"]){
					array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
					array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
					
					$this->load->model("master_data/m_karyawan");
					
					
					if($this->akses["pilih seluruh karyawan"]){
						//$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();
						$this->data["daftar_akses_karyawan"] = $this->db->query("SELECT np_karyawan AS no_pokok,nama_karyawan as nama from ess_skep group by np_karyawan order by np_karyawan asc")->result_array();
					}
					else{				
						$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
					}
							
					$js_header_script = "<script>
						$(document).ready(function() {
							$('.select2').select2();
						});
					</script>";
					
					array_push($this->data["js_header_script"],$js_header_script);
				}
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function daftar() {
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."skep";
			$this->data["tanggal"] = "";
			$this->data["deskripsi"] = "";
			$this->data["panel_tambah"] = "";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
				
				$this->load->model("master_data/m_karyawan");
				
				
				if($this->akses["pilih seluruh karyawan"]){
					//$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();
					$this->data["daftar_akses_karyawan"] = $this->db->query("SELECT np_karyawan AS no_pokok,nama_karyawan as nama from ess_skep group by np_karyawan order by np_karyawan asc")->result_array();
					
				}
				else{				
					$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
				}
						
				$js_header_script = "<script>
					$(document).ready(function() {
						$('.select2').select2();
					});
				</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel_skep($np=null){			
			
			$list = $this->m_skep->get_datatable_skep($np);
					
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

				$plain_txt_1 = rand(1,10000)."|"."untuk_download"."|".$tampil->file1_skep."|".date('Y-m-d H:i:s')."|".$rand_chars;
				$encrypted_txt_1 = $this->encrypt_decrypt('encrypt', $plain_txt_1);
			
				$plain_txt_2 = rand(1,10000)."|"."untuk_download"."|".$tampil->file2_skep."|".date('Y-m-d H:i:s')."|".$rand_chars;
				$encrypted_txt_2 = $this->encrypt_decrypt('encrypt', $plain_txt_2);
				// END OF ENCRYPT NAMA FILE*/
				
				$no++;
				$row = array();
				$row[] = $no;			
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama_karyawan;
				$row[] = $tampil->nomor_skep;
				$row[] = tanggal_indonesia($tampil->aktif_tanggal_skep);
				// $row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='umum' data-id='$encrypted_txt_1' onclick='tampil_rincian(this)'>Lihat</button>";
				// $row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='individu' data-id='$encrypted_txt_2' onclick='tampil_rincian(this)'>Lihat</button>";
				$file_umum = './uploads/skep_umum/'.$tampil->file1_skep;
				$file_individu = './uploads/skep_individu/'.$tampil->file2_skep;
				if (file_exists($file_umum)) {
					$row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='umum' data-id='$encrypted_txt_1' onclick='tampil_rincian(this)'>Lihat</button>";
				}
				else {
					$row[] = "<button class='btn btn-danger btn-xs btn-block'>Belum Upload</button>";
				}

				if (file_exists($file_individu)) {
					$row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='individu' data-id='$encrypted_txt_2' onclick='tampil_rincian(this)'>Lihat</button>";
				}
				else {
					$row[] = "<button class='btn btn-danger btn-xs btn-block'>Belum Upload</button>";
				}
					
				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_skep->count_all($np),
						"recordsFiltered" => $this->m_skep->count_filtered($np),
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function daftar_skep(){
			
			$list = $this->m_skep->get_datatable_skep($np);
					
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

				$plain_txt_1 = rand(1,10000)."|"."untuk_download"."|".$tampil->file1_skep."|".date('Y-m-d H:i:s')."|".$rand_chars;
				$encrypted_txt_1 = $this->encrypt_decrypt('encrypt', $plain_txt_1);
			
				$plain_txt_2 = rand(1,10000)."|"."untuk_download"."|".$tampil->file2_skep."|".date('Y-m-d H:i:s')."|".$rand_chars;
				$encrypted_txt_2 = $this->encrypt_decrypt('encrypt', $plain_txt_2);
				// END OF ENCRYPT NAMA FILE*/
				
				$no++;
				$row = array();
				$row[] = $no;			
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama_karyawan;
				$row[] = $tampil->nomor_skep;
				$row[] = tanggal_indonesia($tampil->aktif_tanggal_skep);
				// $row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='umum' data-id='$encrypted_txt_1' onclick='tampil_rincian(this)'>Lihat</button>";
				// $row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='individu' data-id='$encrypted_txt_2' onclick='tampil_rincian(this)'>Lihat</button>";
				$file_umum = './uploads/skep_umum/'.$tampil->file1_skep;
				$file_individu = './uploads/skep_individu/'.$tampil->file2_skep;
				if (file_exists($file_umum)) {
					$row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='umum' data-id='$encrypted_txt_1' onclick='tampil_rincian(this)'>Lihat</button>";
				}
				else {
					$row[] = "<button class='btn btn-danger btn-xs btn-block'>Belum Upload</button>";
				}

				if (file_exists($file_individu)) {
					$row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='individu' data-id='$encrypted_txt_2' onclick='tampil_rincian(this)'>Lihat</button>";
				}
				else {
					$row[] = "<button class='btn btn-danger btn-xs btn-block'>Belum Upload</button>";
				}
					
				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_skep->count_all($np),
						"recordsFiltered" => $this->m_skep->count_filtered($np),
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function save() {
			if(!strcmp($this->input->post("aksi"),"tambah")){
				$skep = $this->input->post('skep_np[]');
				$fail = array();
				// echo count($skep);exit;
	                // var_dump($this->input->post());exit;

				for ($i=0; $i<count($skep); $i++) {
	                $data['np_karyawan'] = $this->input->post("skep_np[".$i."]");
	                $data['nama_karyawan'] = $this->input->post("skep_nama[".$i."]");
	                $data['nomor_skep'] = $this->input->post("skep_nomor[".$i."]");
	                $data['aktif_tanggal_skep'] = $this->input->post("skep_tanggal[".$i."]");
	                $data['tanggal_tampil'] = $this->input->post("skep_tanggal_tampil[".$i."]");
	                $data['file1_skep'] = $this->input->post("skep_file_umum[".$i."]");
	                $data['file2_skep'] = $this->input->post("skep_file_individu[".$i."]");

	                $cek = $this->db->where(array('np_karyawan'=>$data['np_karyawan'], 'file2_skep'=>$data['file2_skep']))->get('ess_skep');
	                if ($cek->num_rows() > 0) {
	                	$fail[] = (count($fail)+1).'. '.$data['np_karyawan'];
	                }
	                else {
	                	$data['created_at'] = date('Y-m-d H:i:s');
	                	$this->db->set($data)->insert('ess_skep');
	                }
	            }

	            if (count($fail) > 0) {
	        		$this->session->set_flashdata('warning', 'Gagal mengupload file. Data sudah ada. Mohon ulangi data dengan NP : <br>'.implode('<br>', $fail));
	        	}
	        	else {
	        		$this->session->set_flashdata('success', 'Anda berhasil mengupload semua data. Silahkan upload file pdf sesuai nama file pada excel.');
	        	}
	        }
	        else {
	        	$this->session->set_flashdata('warning', 'Gagal mengupload file. Anda tidak memiliki hak akses!');
	        }
	        redirect(site_url('informasi/skep/'));
		}

		public function save_file($jenis) {
			if(!strcmp($this->input->post("aksi"),"tambah")){
				if ($jenis=='umum') {
					if ($_FILES['file_umum']['name'] != '') {
			            $tempFile = $_FILES['file_umum']['tmp_name'];
			            $fileName = $_FILES['file_umum']['name'];
			            $targetPath = './uploads/skep_umum/';
			            $pecahnama = explode('.',$fileName);
			            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
			            $targetFile = $targetPath.$fileName;
			            $ekstensi = array('pdf','PDF');
			            
			            $cek = $this->db->where(array('file1_skep'=>$fileName))->count_all_results('ess_skep');
		                if ($cek == 0) {
		                	$this->session->set_flashdata('warning', 'Nama file umum tidak tertera dalam table skep.');
		                }
		                else {
				            if (in_array($ekstensifile, $ekstensi)) {
				            	if (file_exists($targetFile) > 0) {
				            		unlink($targetFile);
				            	}

				                if (move_uploaded_file($tempFile, $targetFile)) {
		            				$this->session->set_flashdata('success', 'Anda berhasil mengupload file.');
		        					redirect(site_url('informasi/skep/'));
		            			}
		            			else {
		            				echo "<script>alert('warning, Cek koneksi anda !');window.location='".site_url('informasi/skep')."'</script>";
		            			}
		            		}
				            else {
				                echo "<script>alert('warning, File yang Anda upload harus file .pdf !');window.location='".site_url('informasi/skep')."'</script>";
				            }
				        }
			        }
			        else {
			        	echo "<script>alert('warning, File harus diisi !');window.location='".site_url('informasi/skep')."'</script>";
			        }
				}
				else if ($jenis=='individu') {
					$fail = array();
					if ($_FILES['file_individu']['tmp_name'] != '') {
						$all=count($_FILES['file_individu']['tmp_name']);
			            for ($i=0;$i<$all;$i++) {
				            $tempFile = $_FILES['file_individu']['tmp_name'][$i];
				            $fileName = $_FILES['file_individu']['name'][$i];
				            $targetPath = './uploads/skep_individu/';
				            $pecahnama = explode('.',$fileName);
				            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
				            $targetFile = $targetPath.$fileName;
				            $ekstensi = array('pdf','PDF');

				            $cek = $this->db->where(array('file2_skep'=>$fileName))->count_all_results('ess_skep');
			                if ($cek == 0) {
			                	$fail[] = (count($fail)+1).'. '.$fileName.' : Nama file tidak tertera dalam tabel. Masukkan data melalui import excel!';
			                }
			                else {
			                	if (in_array($ekstensifile, $ekstensi)) {
					            	if (file_exists($targetFile) > 0) {
					            		unlink($targetFile);
					            	}

					                if (!move_uploaded_file($tempFile, $targetFile)) {
			            				$fail[] = (count($fail)+1).'. '.$fileName.' : Error Koneksi/File';
			            			}
			            		}
					            else {
					                $fail[] = (count($fail)+1).'. '.$fileName.' : File Bukan PDF';
					            }
					        }
				        }
				        
				        if (count($fail) > 0) {
			        		$this->session->set_flashdata('warning', 'Beberapa file gagal diupload. Mohon ulangi upload file dengan nama : <br>'.implode('<br>', $fail));
			        	}
			        	else {
			        		$this->session->set_flashdata('success', 'Anda berhasil mengupload semua file pdf.');
			        	}

				        redirect(site_url('informasi/skep'));
			        }
			        else {
			        	echo "<script>alert('warning, File harus diisi !');window.location='".site_url('informasi/skep')."'</script>";
			        }
					/*$output = '';  
      				if($_FILES['file_individu']['name'] != '')  {
			           	$tempFile = $_FILES['file_individu']['tmp_name'];
			            $fileName = $_FILES['file_individu']['name']; 
			           	$targetPath = './uploads/skep_individu/';
			            $pecahnama = explode('.',$fileName);
			            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
			            $name = $pecahnama[0];
			            $targetFile = $targetPath.$fileName;
			            $ekstensi = array('zip','ZIP');

			           	if (in_array($ekstensifile, $ekstensi)) { 
			                if(move_uploaded_file($_FILES['file_individu']['tmp_name'], $targetFile)) {  
			                    $zip = new ZipArchive;  
			                    if($zip->open($targetFile)) {  
			                        $zip->extractTo($targetPath);  
			                        $zip->close();  
			                    }  
			                    $files = scandir($targetFile.$name);  

			                    //$name is extract folder from zip file  
			                    foreach($files as $file) {  
			                        $file_ext = end(explode(".", $file));
			                        $allowed_ext = array('pdf', 'PDF');
			                        if(in_array($file_ext, $allowed_ext)) {
			                            // $new_name = md5(rand()).'.' . $file_ext;
			                            // $output .= '<div class="col-md-6"><div style="padding:16px; border:1px solid #CCC;"><img src="upload/'.$new_name.'" width="170" height="240" /></div></div>';
			                            copy($targetFile.$name.'/'.$file, $targetPath.$file);
			                            unlink($targetPath.$name.'/'.$file);
			                        }     
			                    }  
			                    unlink($targetFile);  
			                    rmdir($targetPath.$name);  
			                }
			                else {
	            				echo "<script>alert('UPLOAD GAGAL, Cek koneksi anda !');window.location='".site_url('informasi/skep')."'</script>";
	            			}
			            }
			            else {
			                echo "<script>alert('UPLOAD GAGAL, File yang Anda upload harus file .zip !');window.location='".site_url('informasi/skep')."'</script>";
			            }
			     	} */
				}
	        }
	        else {
	        	$this->session->set_flashdata('warning', 'Gagal mengupload file. Anda tidak memiliki hak akses!');
	        }
	        redirect(site_url('informasi/skep'));
		}

	/*tidak digunakan	
		public function download_skep($nama_file_encrypt)
		{	
		
			$decrypted_txt = $this->encrypt_decrypt('decrypt', $nama_file_encrypt);
			list($random,$username,$nama_file,$datetime_request,$chars) = explode("|",$decrypted_txt);
		
		
			$this->load->helper('download');
			
			$path_download = base_url('file/skep/'.$nama_file);
			$data = file_get_contents($path_download); // Read the file's contents
			$name = $nama_file;

			force_download($name, $data);

		}
	*/
				
		public function ajax_get_skep($nama_file_encrypt="", $jns="")
		{
			
			if(!empty($nama_file_encrypt)){
				
				$decrypted_txt = $this->encrypt_decrypt('decrypt', $nama_file_encrypt);
				list($random,$username,$nama_file,$datetime_request,$chars) = explode("|",$decrypted_txt);
				
				$path_download = base_url('uploads/skep_'.$jns.'/'.$nama_file);
				
				$data['data'] = $path_download;
				
				$this->load->view($this->folder_view."rincian_skep",$data);
			}
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
		
	}
	
	/* End of file skep.php */
	/* Location: ./application/controllers/informasi/skep.php */
