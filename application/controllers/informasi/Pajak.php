<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pajak extends CI_Controller {
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
			$this->load->helper("karyawan_helper");
			$this->load->library("encrypt");
					
			$this->load->model($this->folder_model."m_pajak");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Pajak";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index() {
			// echo "yyyyyyyyy";
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pajak";

			$this->data['panel_tambah'] = "in";
			
			$this->data["tanggal"] = "";
			$this->data["deskripsi"] = "";
			$this->data["panel_tambah"] = "";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
				
				$this->load->model("master_data/m_karyawan");
				
				if($_SESSION["grup"]==5) //jika Pengguna
				{
					$this->data["daftar_akses_karyawan"] = $this->db->query('select np_karyawan as no_pokok, nama_karyawan as nama from ess_pajak where np_karyawan="'.$this->session->userdata("no_pokok").'" group by np_karyawan')->result_array();		
					// $this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
					$this->data["nomor_skep"] = array();
				}else
				if($this->akses["pilih seluruh karyawan"]){
					// $this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();
					$this->data["daftar_akses_karyawan"] = $this->db->query('select np_karyawan as no_pokok, nama_karyawan as nama from ess_pajak group by np_karyawan')->result_array();
					$this->data["nomor_skep"] = $this->m_pajak->daftar_tgl()->result_array();
				}
				else{		
				$this->data["daftar_akses_karyawan"] = $this->db->query('select np_karyawan as no_pokok, nama_karyawan as nama from ess_pajak where np_karyawan="kosong" group by np_karyawan')->result_array();		
					// $this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
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
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel_data(){
			
			$np = $this->input->post('karyawan');
			$tgl = $this->input->post('tgl');
			
			if($np=='' || $np == null)
			{
				$np='x'; //random string agar tidak ketemu data nya
			}

			$list = $this->m_pajak->get_datatable_pajak($np, $tgl);
			// $this->output->enable_profiler(TRUE);
			/*
			2021-02-12 Tri Wibowo 7648 - Jangan ambil dari nama file langsung
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
			*/
		
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $tampil) {
			
				$name = $tampil->file_asli;
				$np_karyawan = substr($name, 0, 4);

				$no++;
				$row = array();
				$row[] = "$no";
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->tahun;
				$row[] = $tampil->tgl_pajak;
				//$row[] = '<a target="_blank" href="'.base_url($tampil->id).'">'.$name.'</a>';
				
				
				if ($tampil->file_pph) {
					// $encrypted_pph = $this->encrypt_decrypt('encrypt', $tampil->file_pph);
					$encrypted_pph = $tampil->file_pph;
					$row[] = "<button class='btn btn-primary btn-xs btn-block lihat_button' data-toggle='modal' data-target='#modal_lihat' data-jenis='individu' data-id='$encrypted_pph' onclick='tampil_rincian(this)'>Lihat</button>";
				}else
				{
					$row[]='-';
				}
				
				$data[] = $row;
			}
			/*
				$row[] = 'atas yg bener';
				$row[] = 'atas yg bener';
				$row[] = 'atas yg bener';
				$row[] = 'atas yg bener';
				//$row[] = '<a target="_blank" href="'.base_url($tampil->id).'">'.$name.'</a>';
				$row[] = 'atas yg bener';
				
				$data[] = $row;
			*/	
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->m_pajak->count_all($np, $tgl),
				"recordsFiltered" => $this->m_pajak->count_filtered($np, $tgl),
				"data" => $data
			);

			echo json_encode($output);
		}

		// Tri WIbowo, Hanya Untuk Generate Tiap Tahun
		public function generate($np='0', $tgl=0){
			// error_reporting(E_ALL);
			// dd($np);
			// dd($np);
			if ($np=='0') {
				if ($tgl==0 || $tgl=="0") {
					$result = glob('./input_pph21/pph21/*[bukti_potong_pajak][Bukti_Potong_Pajak][BUKTI_POTONG_PAJAK]*');
				} else {
					$result = glob('./input_pph21/pph21/*'.$tgl.'*');
				}
			} else {
				if ($tgl==0 || $tgl=="0") {
					$result = glob('./input_pph21/pph21/*'.$np.'*');
				} else {
					$result = glob('./input_pph21/pph21/*'.$np.'_'.$tgl.'*');
				}
			}
			sort($result);
			// dd($result);
			
			$file_db = array_column($this->db->get('ess_pajak')->result_array(), 'file_asli');
			
			for ($i=0; $i<count($result); $i++) {
				$name = str_replace('./input_pph21/pph21/', '', $result[$i]);
				$np_karyawan = substr($name, 0, 4);
				// array_map('strtolower', $file_db);
				
				//17 02 2022 Tri WIbowo, check apakah ada di master karyawan
				//$validasi_nama = nama_karyawan_by_np($np_karyawan);
				$validasi_tahun = substr($name, 5, 4);
				$proses_tahun = $tgl;
				$validasi_file = substr($name, 15, 18);
				$proses_file = 'bukti_potong_pajak';
				
				if($validasi_tahun==$proses_tahun AND $validasi_file==$proses_file)
				{
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
							// dd('/input_pph21/pph21/'.$name.' jadi '.'/input_pph21/pph21/'.$encrypted_pph.'.pdf');
							$source_file = './input_pph21/pph21/'.$name;
							$copied_file = './input_pph21/pph21/'.$encrypted_pph.'.pdf';

							if (file_exists($source_file)) {
								if (file_exists($copied_file)) {
									unlink($copied_file);
									echo "File Exist, File Deleted successfully!";
								}
								// change or rename, before is rename
								if(rename($source_file, $copied_file)){
									echo "File renamed successfully!";
								} else {
									echo "Failed to rename file.";
								}
								// echo 'ada';
								// echo 'di copy dari /input_pph21/pph21/'.$name.' jadi '.'/input_pph21/pph21/'.$encrypted_pph.'.pdf';
								//  echo "<br>";
							} else {
								echo "Source file does not exist.";
							}
							
							// $a = exec('mv ./input_pph21/pph21/'.$name.' ./input_pph21/pph21/'.$encrypted_pph.'.pdf');
							// exec('./input_pph21/pph21.sh',$output);
							// print_r($output);die;
							// print_r($output);exit;
						}
					}
				}
			}

			$this->session->set_flashdata('success', 'Berhasil mereferesh data pph21');
			// redirect('informasi/pajak');
		}
		

		public function save_file() {
			// $cmd = shell_exec("cp -avR /home/file/pph/* /home/file/pph21"); echo $cmd; exit;
			if($this->akses["tambah"]) {
				$fail = array();
				if ($_FILES['file_pph']['tmp_name'] != '') {
					$this->load->library(array('upload'));
					
					
					$config['upload_path'] = './input_pph21/pph21';			
					$config['allowed_types'] = 'pdf';
					$config['max_size']	= '2000';
					
					$edit_wfh_foto[0] = null;
					$edit_wfh_foto[1] = null;	
					$files = $_FILES;

					$this->upload->initialize($config);

					if($files['file_pph']['name']) {
						$this->load->helper("file");
						if($this->upload->do_upload('file_pph')) {
							$up = $this->upload->data();
							$this->session->set_flashdata('warning', 'Berhasil Upload File : '.$up['file_name']);
						} else {
							$error =$this->upload->display_errors();
							$this->session->set_flashdata('warning', "Terjadi Kesalahan, $error");
						}
						redirect(site_url('informasi/pajak'));
					} else {
						$this->session->set_flashdata('warning', "File Tidak Ditemukan");
						redirect(site_url('informasi/pajak'));
					}


					/*$all=count($_FILES['file_pph']['tmp_name']);
					$created = date('Y-m-d H:i:s');
					
					$this->load->library(array('upload'));

					$config['upload_path'] = './file/pph21/';
					$config['allowed_types'] = 'pdf';
					$config['max_size']	= '2048';
					$config['encrypt_name'] = true;	

		            // for ($i=0;$i<$all;$i++) {
			            $tempFile = $_FILES['file_pph']['tmp_name'];
			            $fileName = $_FILES['file_pph']['name'];
			            $targetPath = 'file/pph21/';
			            $pecahnama = explode('.',$fileName);
			            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
			            $targetFile = $targetPath.$fileName;
			            $ekstensi = array('pdf','PDF');

		                if (in_array($ekstensifile, $ekstensi)) {
				            $cek = $this->db->where(array('file_pph'=>$fileName))->count_all_results('ess_pajak');
				            $jml_file = file_exists($targetFile);
			            	if ($cek == 0 && $jml_file > 0) {
			            		unlink($targetFile);
				            	$jml_file = 0;
			            	} else if ($cek == 1 && $jml_file == 0) {
				            	$this->db->where(array('file_pph'=>$fileName))->delete('ess_pajak');
				            	$cek = 0;
			            	}

		                	if ($cek == 0 && $jml_file == 0) {
		                		// $this->load->library('upload', $config);
		                		$this->upload->initialize($config);

		                		// echo $_FILES['file_pph']['name'];
		                		// move_uploaded_file($_FILES['file_pph']['tmp_name'], '/home/file/pph/'.$_FILES['file_pph']['name']);
		                		// exit();

				                if ($this->upload->do_upload('file_pph')) {
				                	// $cmd = move_uploaded_file($tempFile, './uploads/pph21/'.$this->upload->data('file_name'));
				                	$get_data = explode('_',$fileName);
				                	$data['np_karyawan'] = $get_data[0];
				                	$data['nama_karyawan'] = 'a';
				                	$data['tahun'] = $get_data[1];
				                	$data['tgl_pajak'] = $get_data[2];
				                	$data['file_pph'] = $fileName;
				                	$data['created_at'] = $created;

				                	$file_real_name = $this->upload->data('file_name');
				                	exec('cp /var/www/html/ess/file/temp/'.$file_real_name.' /home/file/temp/'.$file_real_name);

				                	var_dump("berhasil");exit;
		            			} else {
		            				$fail[] = (count($fail)+1).'. '.$fileName.' : Gagal Upload. '.$this->upload->display_errors().$fileName;
		            			}
		            		}
			                else {
			                	$fail[] = (count($fail)+1).'. '.$fileName.' : File Sudah Ada!';
					        }
		                }
			            else {
			                $fail[] = (count($fail)+1).'. '.$fileName.' : File Bukan PDF';
			            }
			        // }*/

			        if (count($fail) > 0) {
		        		$this->session->set_flashdata('warning', 'Beberapa file gagal diupload. Mohon ulangi upload file dengan nama : <br>'.implode('<br>', $fail));
		        	}
		        	else {
		        		$this->session->set_flashdata('success', 'Anda berhasil mengupload semua file pdf.');
		        	}

			        redirect('informasi/pajak');
			    }
	        }
	        else {
	        	$this->session->set_flashdata('warning', 'Gagal mengupload file. Anda tidak memiliki hak akses!');
	        }
	        redirect('informasi/pajak');
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
				
		public function ajax_get_pajak($nama_file_encrypt="", $jns="")
		{
			
			if(!empty($nama_file_encrypt)){
				
				$decrypted_txt = $this->encrypt_decrypt('decrypt', $nama_file_encrypt);
				
				//12 02 2021 Tri Wibowo 7648 - Langsung jadi nama file tanpa embel apapun kok
				//list($random,$username,$nama_file,$datetime_request,$chars) = explode("|",$decrypted_txt);
				$nama_file = $decrypted_txt;
				$path_download = base_url('input_pph21/pph21'.'/'.$nama_file);
				
				$data['data'] = $path_download;
				
				$this->load->view($this->folder_view."rincian_pajak",$data);
				
			}
		}
				
		public function ajax_get_pajak_file($nama_file_encrypt="", $jns="")
		{
			if(!empty($nama_file_encrypt)){
				if (file_exists('input_pph21/pph21'.'/'.$nama_file_encrypt)) {
					$path_download = base_url('input_pph21/pph21'.'/'.$nama_file_encrypt);
				} else {
					$path_download = base_url('input_pph21/link_pph21'.'/'.$nama_file_encrypt);
				}

				
				$data['data'] = $path_download;
				
				$this->load->view($this->folder_view."rincian_pajak",$data);
				
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
				//$datetime_request 	= $pisah[3];
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
