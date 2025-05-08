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
			$this->load->library("encrypt");
					
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
		                            $this->session->set_flashdata('success', 'Berikut adalah hasil file yang anda upload. Klik tombol simpan jika data yang anda upload sudah benar<br>Kosongkan Kotak Pencarian Jika Ingin Menyimpan Semua Data !');
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
						// $this->data["daftar_akses_karyawan"] = $this->db->query("SELECT np_karyawan AS no_pokok,nama_karyawan as nama from ess_skep group by np_karyawan order by np_karyawan asc")->result_array();
						$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();
						$this->data["nomor_skep"] = $this->m_skep->daftar_nomor()->result_array();
					}
					else{				
						$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
						$this->data["nomor_skep"] = array();
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
		
		public function tabel_skep(){
			// dd($this->akses["pilih seluruh karyawan"]); 
			if(!$this->akses["pilih seluruh karyawan"] && $this->session->userdata("no_pokok") != $this->input->post('karyawan')){
				dd("not authorized");
				exit();
			}
			$np = $this->input->post('karyawan');
			$nomor = $this->input->post('no');
					
			$list = $this->m_skep->get_datatable_skep($np, $nomor);
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
				$encrypted_txt_1 = $tampil->file_1_skep_encrypt;
			
				$plain_txt_2 = rand(1,10000)."|"."untuk_download"."|".$tampil->file2_skep."|".date('Y-m-d H:i:s')."|".$rand_chars;
				$encrypted_txt_2 = $tampil->file_2_skep_encrypt;
				// END OF ENCRYPT NAMA FILE*/
				
				$no++;
				$row = array();
				$row[] = $no;			
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama_karyawan;
				$row[] = $tampil->nomor_skep;
				$row[] = tanggal_indonesia($tampil->aktif_tanggal_skep);
				$file_umum = './uploads/skep_umum/'.$tampil->file_1_skep_encrypt;
				$file_individu = './uploads/skep_individu/'.$tampil->file_2_skep_encrypt;

				if (file_exists('./uploads/skep_individu/'.$tampil->file_2_skep_encrypt)) {
					$file_individu = './uploads/skep_individu/'.$tampil->file_2_skep_encrypt;
				} else {
					$file_individu = './uploads/skep_home_individu/'.$tampil->file_2_skep_encrypt;
				}

				/*if (file_exists($file_umum)) {
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
				}*/

				if (file_exists($file_umum)) {
					$row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='umum' data-id='$encrypted_txt_1' onclick='tampil_rincian_file(this)'>Lihat</button>";
				}
				else {
					$row[] = "<button class='btn btn-danger btn-xs btn-block'>Belum Upload</button>";
				}

				if (file_exists($file_individu)) {
					$row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='individu' data-id='$encrypted_txt_2' onclick='tampil_rincian_file(this)'>Lihat</button>";
				}
				else {
					$row[] = "<button class='btn btn-danger btn-xs btn-block'>Belum Upload</button>";
				}

				if ($this->akses['hapus']) {
					$id_txt = rand(1,10000)."|"."get_id"."|".$tampil->id."|".date('Y-m-d H:i:s')."|".$rand_chars;
					$encrypted_txt_id = $this->encrypt_decrypt('encrypt', $id_txt);
					$row[] = " <button class='btn btn-danger btn-xs delete' data-id='$encrypted_txt_id' data-nomor='0' >Hapus</button>";
				} else {
					$row[] = '';
				}
					
				$data[] = $row;
			}

			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->m_skep->count_all($np, $nomor),
				"recordsFiltered" => $this->m_skep->count_filtered($np, $nomor),
				"data" => $data
			);
			//output to json format
			echo json_encode($output);
		}
		
		/*public function generate($np='0', $tgl=0){
			// error_reporting(E_ALL);
			if ($np=='0') {
				if ($tgl==0) {
					$result = glob('./input_pph21/pph21/*bukti_potong_pajak*');
				} else {
					$result = glob('./input_pph21/pph21/*'.$tgl.'*');
				}
			} else {
				if ($tgl==0) {
					$result = glob('./input_pph21/pph21/*'.$np.'*');
				} else {
					$result = glob('./input_pph21/pph21/*'.$np.'_'.$tgl.'*');
				}
			}
			sort($result);
			
			$file_db = array_column($this->db->get('ess_pajak')->result_array(), 'file_asli');
			
			for ($i=0; $i<count($result); $i++) {
			// for ($i=0; $i<2; $i++) {
				$name = str_replace('./input_pph21/pph21/', '', $result[$i]);
				$np_karyawan = substr($name, 0, 4);

				if (!in_array($name, $file_db)) {
					$set['np_karyawan'] = $np_karyawan;
					$set['nama_karyawan'] = nama_karyawan_by_np($np_karyawan);
					if ($set['nama_karyawan']==null)
						$set['nama_karyawan'] = nama_karyawan_by_np_old($np_karyawan);
					if ($set['nama_karyawan']==null)
						$set['nama_karyawan'] = nama_karyawan_by_np_master($np_karyawan);
					if ($set['nama_karyawan']!=null) {
						$set['tahun'] = substr($name, 5, 4);
						$set['tgl_pajak'] = id_to_bulan(substr($name, 10, 2)).' - '.id_to_bulan(substr($name, 12, 2));
						$set['file_asli'] = $name;
						$pdf_out = str_replace('.pdf', '', $name);
						$encrypted_pph = $this->encrypt_decrypt('encrypt', $pdf_out);
						$set['file_pph'] = $encrypted_pph.'.pdf';
						$set['created_at'] = date('Y-m-d H:i:s');
						$this->db->set($set)->insert('ess_pajak');
						if ($this->db->affected_rows()>0) {
							rename('./input_pph21/pph21/'.$name, './input_pph21/pph21/'.$encrypted_pph.'.pdf');
						}
					}
				}
			}

			$this->session->set_flashdata('success', 'Berhasil mereferesh data pph21');
			redirect('dashboard');
		}*/
		

		public function generate($np='0'){
			// error_reporting(E_ALL);
			if ($np=='0') {
				$result = glob('./uploads/skep_individu/*[skep][Skep][SKEP]*');
			} else {
				if ($tgl==0) {
					$result = glob('./input_pph21/pph21/*'.$np.'*');
				} else {
					$result = glob('./input_pph21/pph21/*[skep_'.$np.'][Skep_'.$np.'][SKEP_'.$np.']*');
				}
			}
			sort($result);
			
			$file_db = array_column($this->db->get('ess_skep')->result_array(), 'file2_skep');
			
			// for ($i=0; $i<count($result); $i++) {
			for ($i=0; $i<2; $i++) {
				$name = str_replace('./uploads/skep_individu/', '', $result[$i]);
				$np_karyawan = substr($name, 0, 4);
				// array_map('strtolower', $file_db);

				if (!in_array($name, $file_db)) {
					$set['np_karyawan'] = $np_karyawan;
					$set['nama_karyawan'] = nama_karyawan_by_np($np_karyawan);
					$set['tahun'] = substr($name, 5, 4);
					$set['tgl_pajak'] = id_to_bulan(substr($name, 10, 2)).' - '.id_to_bulan(substr($name, 12, 2));
					$set['file_asli'] = $name;
					$pdf_out = str_replace('.pdf', '', $name);
					$encrypted_pph = $this->encrypt_decrypt('encrypt', $pdf_out);
					$set['file_pph'] = $encrypted_pph.'.pdf';
					$set['created_at'] = date('Y-m-d H:i:s');
					
					$arr_lower = array_map('strtolower', $file_db);
					if (!in_array(strtolower($name), $arr_lower)) {
						$this->db->set($set)->insert('ess_pajak');
					} else {
						$this->db->set($set)->where('file_asli', $name)->update('ess_pajak');
					}
					if ($this->db->affected_rows()>0) {
						if (file_exists('./input_pph21/pph21/'.$name)) {
							rename('./input_pph21/pph21/'.$name, './input_pph21/pph21/'.$encrypted_pph.'.pdf');
							// echo 'ada';
						}
						// echo '/input_pph21/pph21/'.$name.' jadi '.'/input_pph21/pph21/'.$encrypted_pph.'.pdf';
						// $a = exec('mv ./input_pph21/pph21/'.$name.' ./input_pph21/pph21/'.$encrypted_pph.'.pdf');
						// exec('./input_pph21/pph21.sh',$output);
						// print_r($output);die;
						// print_r($output);exit;
					}
				}
			}

			$this->session->set_flashdata('success', 'Berhasil mereferesh data pph21');
			redirect('informasi/pajak');
		}

		public function generate_from_db(){
			$get_umum = $this->db->select('file1_skep as nama_file')->where('file_1_skep_encrypt is null')->group_by('file1_skep')->get('ess_skep');
			$get_individu = $this->db->select('file2_skep as nama_file')->where('file_2_skep_encrypt is null')->get('ess_skep');

			foreach ($get_umum->result_array() as $file_umum) {
				$name = $file_umum['nama_file'];
				$pdf_out = str_replace('.pdf', '', $name);
				$encrypted_file = $this->encrypt_decrypt('encrypt', $pdf_out);
				$set_umum['file_1_skep_encrypt'] = $encrypted_file.'.pdf';
				if(rename('./uploads/skep_umum/'.$name, './uploads/skep_umum/'.$encrypted_file.'.pdf')) {
					$this->db->where('file1_skep', $name)->set($set_umum)->update('ess_skep');
				}
			}

			foreach ($get_individu->result_array() as $file_individu) {
				$name = $file_individu['nama_file'];
				$pdf_out = str_replace('.pdf', '', $name);
				$encrypted_file = $this->encrypt_decrypt('encrypt', $pdf_out);
				$set_individu['file_2_skep_encrypt'] = $encrypted_file.'.pdf';
				if(rename('./uploads/skep_individu/'.$name, './uploads/skep_individu/'.$encrypted_file.'.pdf')) {
					$this->db->where('file2_skep', $name)->set($set_individu)->update('ess_skep');
				}
			}

			redirect('informasi/skep');
		}
			
		public function save() {
			if(!strcmp($this->input->post("aksi"),"tambah")){
				$skep = $this->input->post('jumlah');
				$fail = array();
				$post = array();	                // var_dump($this->input->post());exit;

				for ($i=0; $i<$skep; $i++) {
					$post = json_decode($this->input->post("post[".$i."]"));
					if(!empty($post)) {
		                $data['np_karyawan'] = $post->np_karyawan;
		                $data['nama_karyawan'] = $post->nama_karyawan;
		                $data['nomor_skep'] = $post->nomor_skep;
		                $data['aktif_tanggal_skep'] = $post->aktif_tanggal_skep;
		                $data['tanggal_tampil'] = $post->tanggal_tampil;
		                $data['file1_skep'] = $post->file1_skep;
		                $data['file2_skep'] = $post->file2_skep;
						$data['file_1_skep_encrypt'] = ($this->encrypt_decrypt('encrypt', str_replace('.pdf', '', $post->file1_skep))).'.pdf';
		                $data['file_2_skep_encrypt'] = ($this->encrypt_decrypt('encrypt', str_replace('.pdf', '', $post->file2_skep))).'.pdf';
                        // header('Content-type: application/json');
                        // echo json_encode($data); exit;
		                $cek = $this->db->where(array('np_karyawan'=>$data['np_karyawan'], 'nomor_skep'=>$data['nomor_skep']))->get('ess_skep');
		                if ($cek->num_rows() > 0) {
		                	// $fail[] = (count($fail)+1).'. '.$data['np_karyawan'];
		                	$data['created_at'] = date('Y-m-d H:i:s');
		                	$this->db->set($data)->where('id', $cek->row()->id)->update('ess_skep');
		                }
		                else {
		                	$data['created_at'] = date('Y-m-d H:i:s');
		                	$this->db->set($data)->insert('ess_skep');
		                }
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
						
						$pdf_out = str_replace('.pdf', '', $fileName);
						$encrypted_file = $this->encrypt_decrypt('encrypt', $pdf_out);
						$fileName_encrypt = $encrypted_file.'.pdf';
			            $targetFile_encrypt = $targetPath.$fileName_encrypt;
			            
			            $cek = $this->db->where(array('file1_skep'=>$fileName))->count_all_results('ess_skep');
		                if ($cek == 0) {
		                	$this->session->set_flashdata('warning', 'Nama file umum tidak tertera dalam table skep.');
		                }
		                else {
				            if (in_array($ekstensifile, $ekstensi)) {
				            	if (file_exists($targetFile_encrypt) > 0) {
				            		unlink($targetFile_encrypt);
				            	}

				                if (move_uploaded_file($tempFile, $targetFile_encrypt)) {
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

							$pdf_out = str_replace('.pdf', '', $fileName);
							$encrypted_file = $this->encrypt_decrypt('encrypt', $pdf_out);
							$fileName_encrypt = $encrypted_file.'.pdf';
				            $targetFile_encrypt = $targetPath.$fileName_encrypt;
				            
				            $cek = $this->db->where(array('file2_skep'=>$fileName))->count_all_results('ess_skep');
			                if ($cek == 0) {
			                	$fail[] = (count($fail)+1).'. '.$fileName.' : Nama file tidak tertera dalam tabel. Masukkan data melalui import excel!';
			                }
			                else {
			                	if (in_array($ekstensifile, $ekstensi)) {
					            	if (file_exists($targetFile_encrypt) > 0) {
					            		unlink($targetFile_encrypt);
					            	}

					                if (!move_uploaded_file($tempFile, $targetFile_encrypt)) {
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
				
				if (file_exists('./uploads/skep_'.$jns.'/'.$nama_file)) {
					$path_download = base_url('uploads/skep_'.$jns.'/'.$nama_file);
				} else {
					$path_download = base_url('uploads/skep_home_'.$jns.'/'.$nama_file);
				}
				
				$data['data'] = $path_download;
				
				$this->load->view($this->folder_view."rincian_skep",$data);
			}
		}
		
		public function ajax_get_skep_file($nama_file_encrypt="", $jns="")
		{
			
			if(!empty($nama_file_encrypt)){
				if (file_exists('./uploads/skep_'.$jns.'/'.$nama_file_encrypt)) {
					$path_download = base_url('uploads/skep_'.$jns.'/'.$nama_file_encrypt);
				} else {
					$path_download = base_url('uploads/skep_home_'.$jns.'/'.$nama_file_encrypt);
				}
				
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
		
		public function hapus()
		{
			$encrypt_id = $this->input->post('key', true);
			$skep = $this->input->post('nomor', true);

			if($encrypt_id=='0' && $skep=='0') {
				echo json_encode(['status' => 'error', 'result' => 401, 'txt' => 'Anda Belum Memilih Data Upload !']);
			}
			else {
				if ($encrypt_id!='0') {
					$dcr = explode("|", $this->encrypt_decrypt('decrypt', $encrypt_id));
					$id = $dcr[2];
					$this->db->where('id', $id)->delete('ess_skep');
				}
				else if ($skep!='0') {
					$nomor = $this->encrypt->decode($skep);
					$this->db->where('nomor_skep', $nomor)->delete('ess_skep');
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
