<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Hasil_mcu extends CI_Controller {
		public function __construct(){
			parent::__construct();
	        $this->load->library('Excel_reader');
			$this->load->library('phpexcel');
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/';
			$this->folder_model = 'sikesper/';
			$this->folder_controller = 'sikesper/';
			
			$this->akses = array();
					
			$this->load->helper("tanggal_helper");
					
			$this->load->model($this->folder_model."m_hasil_mcu");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Hasil MCU";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index() {
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."index_mcu";

			$this->data['panel_tambah'] = "in";
			
			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);

					if ($_FILES['file_excel']['name'] != '') {
			            $tempFile = $_FILES['file_excel']['tmp_name'];
			            $fileName = $_FILES['file_excel']['name'];
			            $targetPath = './uploads/mcu_import/';
			            $pecahnama = explode('.',$fileName);
			            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
			            $time1 = date('YmdHis');
			            $targetFile = $targetPath.'Import_MCU_'.$time1.'.'.$ekstensifile;
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
			                        //$awal = 7; sebelum dikasih tahun di row 4 (di bawah tanggal)
			                        $awal = 8;
			                        $mcu = array();

			                        while ($objWorksheet->getCellByColumnAndRow(1,$awal)->getValue() != '') {
		                                $mcu[$i]['no_reg'] = $objWorksheet->getCellByColumnAndRow(1,$awal)->getValue();
		                                $mcu[$i]['nama_karyawan'] = $objWorksheet->getCellByColumnAndRow(2,$awal)->getValue();
		                                $mcu[$i]['np_karyawan'] = $objWorksheet->getCellByColumnAndRow(3,$awal)->getValue();
		                                $mcu[$i]['departemen'] = $objWorksheet->getCellByColumnAndRow(4,$awal)->getValue();
		                                $mcu[$i]['dob'] = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($objWorksheet->getCellByColumnAndRow(5,$awal)->getValue()));
		                                $mcu[$i]['usia'] = $objWorksheet->getCellByColumnAndRow(6,$awal)->getValue();
		                                $mcu[$i]['sex'] = $objWorksheet->getCellByColumnAndRow(7,$awal)->getValue();
		                                $mcu[$i]['riwayat_penyakit'] = $objWorksheet->getCellByColumnAndRow(8,$awal)->getValue();
		                                $mcu[$i]['ayah'] = $objWorksheet->getCellByColumnAndRow(9,$awal)->getValue();
		                                $mcu[$i]['ibu'] = $objWorksheet->getCellByColumnAndRow(10,$awal)->getValue();
		                                $mcu[$i]['alergi'] = $objWorksheet->getCellByColumnAndRow(11,$awal)->getValue();
		                                $mcu[$i]['merokok'] = $objWorksheet->getCellByColumnAndRow(12,$awal)->getValue();
		                                $mcu[$i]['alkohol'] = $objWorksheet->getCellByColumnAndRow(13,$awal)->getValue();
		                                $mcu[$i]['olahraga'] = $objWorksheet->getCellByColumnAndRow(14,$awal)->getValue();
		                                $mcu[$i]['tb'] = $objWorksheet->getCellByColumnAndRow(15,$awal)->getValue();
		                                $mcu[$i]['bb'] = $objWorksheet->getCellByColumnAndRow(16,$awal)->getValue();
		                                $mcu[$i]['bmi'] = $objWorksheet->getCellByColumnAndRow(17,$awal)->getValue();
		                                $mcu[$i]['kesan'] = $objWorksheet->getCellByColumnAndRow(18,$awal)->getValue();
		                                $mcu[$i]['tekanan_darah'] = $objWorksheet->getCellByColumnAndRow(19,$awal)->getValue();
		                                $mcu[$i]['visus_kanan'] = $objWorksheet->getCellByColumnAndRow(20,$awal)->getValue();
		                                $mcu[$i]['visus_kiri'] = $objWorksheet->getCellByColumnAndRow(21,$awal)->getValue();
		                                $mcu[$i]['kenal_warna'] = $objWorksheet->getCellByColumnAndRow(22,$awal)->getValue();
		                                $mcu[$i]['gigi'] = $objWorksheet->getCellByColumnAndRow(23,$awal)->getValue();
		                                $mcu[$i]['fisik'] = $objWorksheet->getCellByColumnAndRow(24,$awal)->getValue();
		                                $mcu[$i]['hematologi'] = $objWorksheet->getCellByColumnAndRow(25,$awal)->getValue();
		                                $mcu[$i]['kimia'] = $objWorksheet->getCellByColumnAndRow(26,$awal)->getValue();
		                                $mcu[$i]['hbsag'] = $objWorksheet->getCellByColumnAndRow(27,$awal)->getValue();
		                                $mcu[$i]['anti_hbs'] = $objWorksheet->getCellByColumnAndRow(28,$awal)->getValue();
		                                $mcu[$i]['urinalisa'] = $objWorksheet->getCellByColumnAndRow(29,$awal)->getValue();
		                                $mcu[$i]['rontgen'] = $objWorksheet->getCellByColumnAndRow(30,$awal)->getValue();
		                                $mcu[$i]['ekg'] = $objWorksheet->getCellByColumnAndRow(31,$awal)->getValue();
		                                $mcu[$i]['audiometri'] = $objWorksheet->getCellByColumnAndRow(32,$awal)->getValue();
		                                $mcu[$i]['kesimpulan'] = $objWorksheet->getCellByColumnAndRow(33,$awal)->getValue();
		                                $mcu[$i]['saran'] = $objWorksheet->getCellByColumnAndRow(34,$awal)->getValue();
		                                $mcu[$i]['fitnes_category'] = $objWorksheet->getCellByColumnAndRow(35,$awal)->getValue();
		                                $mcu[$i]['kolestrol'] = $objWorksheet->getCellByColumnAndRow(36,$awal)->getValue();
		                                $mcu[$i]['gula_darah'] = $objWorksheet->getCellByColumnAndRow(37,$awal)->getValue();
		                                $mcu[$i]['asam_urat'] = $objWorksheet->getCellByColumnAndRow(38,$awal)->getValue();
		                                $mcu[$i]['massa_lemak'] = $objWorksheet->getCellByColumnAndRow(39,$awal)->getValue();
		                                $mcu[$i]['klasifikasi_tubuh'] = $objWorksheet->getCellByColumnAndRow(40,$awal)->getValue();
		                                $mcu[$i]['keluhan_saat_ini'] = $objWorksheet->getCellByColumnAndRow(41,$awal)->getValue();
		                                $mcu[$i]['tahun'] = preg_replace('/\D/', '', $objWorksheet->getCellByColumnAndRow(0,4)->getValue());

		                                $i++;
		                            	$awal++;
		                            }
		                            $this->data['tanggal_mcu'] = $objWorksheet->getCellByColumnAndRow(0,3)->getValue();
		                            //$this->data['vendor'] = $objWorksheet->getCellByColumnAndRow(0,4)->getValue(); sebelum dikasih tahun di row 4 (di bawah tanggal)
		                            $this->data['vendor'] = $objWorksheet->getCellByColumnAndRow(0,5)->getValue();
		                            $this->data['list_mcu'] = $mcu;
		                            $this->data['content'] = $this->folder_view."preview_mcu";
		                            $this->session->set_flashdata('success', 'Berikut adalah hasil file yang anda upload. Klik tombol simpan jika data yang anda upload sudah benar<br>Kosongkan Kotak Pencarian Jika Ingin Menyimpan Semua Data !');
			                    }
			                    catch (Exception $ee) {
			                        unlink($targetFile);
			                        die("Error ! ".$ee->getMessage());
			                    }
			                }
			                else {
			                    echo 'a';exit;
			                    echo "<script>alert('UPLOAD GAGAL, File yang Anda upload harus file .XLSX');window.location='".site_url('sikesper/hasil_mcu')."'</script>";
			                }
			            }
			            else {
			                echo "<script>alert('UPLOAD GAGAL, File yang Anda upload harus file .XLSX');window.location='".site_url('sikesper/hasil_mcu')."'</script>";
			            }
			        }
			        else {
			            echo "<script>alert('File harus diisi !');window.location='".site_url('sikesper/hasil_mcu')."'</script>";
			        }
					
				}
				else{
					echo "<script>alert('Anda Tidak Memiliki Akses !');window.location='".site_url('sikesper/hasil_mcu')."'</script>";
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
					
					
					$this->data["daftar_vendor"] = $this->m_hasil_mcu->daftar_vendor()->result_array();
					if($this->akses["pilih seluruh karyawan"]){
						$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();
						//$this->data["tanggal_upload"] = $this->m_hasil_mcu->daftar_upload()->result_array();
						$this->data["tahun"] = $this->m_hasil_mcu->daftar_tahun()->result_array();
					}
					else{				
						$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
						//$this->data["tanggal_upload"] = array();
						$this->data["tahun"] = $this->m_hasil_mcu->daftar_tahun()->result_array();
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
		
		public function tabel_mcu($np=0,$tgl=0,$vendor=0,$tahun=0){
			$list = $this->m_hasil_mcu->get_datatable($np, $tgl, $vendor,$tahun);
					
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
				$row[] = tanggal_indonesia(date('Y-m-d', strtotime($tampil->created_at)));
				$row[] = $tampil->tanggal_mcu;
				$row[] = $tampil->vendor;
				$row[] = $tampil->np_karyawan.' - '.$tampil->nama_karyawan.'<br><small>('.$tampil->departemen.')</small>';
				$row[] = $tampil->no_reg;
				if ($this->akses['hapus']) 
				$hps = " <button class='btn btn-danger btn-xs delete' data-id='$encrypted_txt_id' data-tgl='0' >Hapus</button>";
				else
				$hps = '';
				$row[] = "<button class='btn btn-primary btn-xs lihat_button' data-toggle='modal' data-target='#modal_lihat' data-id='$encrypted_txt_id' onclick='tampil_detail(this)'>Detail</button>".$hps;
					
				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_hasil_mcu->count_all($np, $tgl, $vendor,$tahun),
						"recordsFiltered" => $this->m_hasil_mcu->count_filtered($np, $tgl, $vendor,$tahun),
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function save() {
			if(!strcmp($this->input->post("aksi"),"tambah")){
				$mcu = $this->input->post('jumlah');
				$tgl_mcu = $this->input->post('tanggal_mcu');
				$vendor = $this->input->post('vendor');
				$fail = array();
				$post = array();

	            $data['tanggal_mcu'] = $tgl_mcu;
	            $data['vendor'] = $vendor;
				for ($i=0; $i<$mcu; $i++) {
					$post = json_decode($this->input->post("post[".$i."]"));
					if(!empty($post)) {
		                $data['no_reg'] = $post->no_reg;
		                $data['nama_karyawan'] = $post->nama_karyawan;
		                $data['np_karyawan'] = $post->np_karyawan;
		                $data['departemen'] = $post->departemen;
		                $data['dob'] = $post->dob;
		                $data['usia'] = $post->usia;
		                $data['sex'] = $post->sex;
		                $data['riwayat_penyakit'] = $post->riwayat_penyakit;
		                $data['ayah'] = $post->ayah;
		                $data['ibu'] = $post->ibu;
		                $data['alergi'] = $post->alergi;
		                $data['merokok'] = $post->merokok;
		                $data['alkohol'] = $post->alkohol;
		                $data['olahraga'] = $post->olahraga;
		                $data['tb'] = $post->tb;
		                $data['bb'] = $post->bb;
		                $data['bmi'] = $post->bmi;
		                $data['kesan'] = $post->kesan;
		                $data['tekanan_darah'] = $post->tekanan_darah;
		                $data['visus_kanan'] = $post->visus_kanan;
		                $data['visus_kiri'] = $post->visus_kiri;
		                $data['kenal_warna'] = $post->kenal_warna;
		                $data['gigi'] = $post->gigi;
		                $data['fisik'] = $post->fisik;
		                $data['hematologi'] = $post->hematologi;
		                $data['kimia'] = $post->kimia;
		                $data['hbsag'] = $post->hbsag;
		                $data['anti_hbs'] = $post->anti_hbs;
		                $data['urinalisa'] = $post->urinalisa;
		                $data['rontgen'] = $post->rontgen;
		                $data['ekg'] = $post->ekg;
		                $data['audiometri'] = $post->audiometri;
		                $data['kesimpulan'] = $post->kesimpulan;
		                $data['saran'] = $post->saran;
		                $data['fitnes_category'] = $post->fitnes_category;
		                $data['kolestrol'] = $post->kolestrol;
		                $data['gula_darah'] = $post->gula_darah;
		                $data['asam_urat'] = $post->asam_urat;
		                $data['massa_lemak'] = $post->massa_lemak;
		                $data['klasifikasi_tubuh'] = $post->klasifikasi_tubuh;
		                $data['keluhan_saat_ini'] = $post->keluhan_saat_ini;

		                $cek = $this->db->where(array('np_karyawan'=>$data['np_karyawan'],'tanggal_mcu'=>$data['tanggal_mcu'],'vendor'=>$data['vendor']))->get('ess_hasil_mcu');
		                if ($cek->num_rows() == 1) {
		                	$data['created_at'] = date('Y-m-d H:i:s');
		                	$this->db->set($data)->where('id', $cek->row()->id)->update('ess_hasil_mcu');
		                	// $fail[] = (count($fail)+1).'. '.$data['np_karyawan'];
		                }
		                else {
		                	$data['created_at'] = date('Y-m-d H:i:s');
		                	$this->db->set($data)->insert('ess_hasil_mcu');
		                }
		            }
	            }

	            if (count($fail) > 0) {
	        		$this->session->set_flashdata('warning', 'Gagal mengupload file. Data sudah ada. Mohon ulangi data dengan NP : <br>'.implode('<br>', $fail));
	        	}
	        	else {
	        		$this->session->set_flashdata('success', 'Anda berhasil mengupload semua data.');
	        	}
	        }
	        else {
	        	$this->session->set_flashdata('warning', 'Gagal mengupload file. Anda tidak memiliki hak akses!');
	        }
	        redirect(base_url('sikesper/hasil_mcu'));
		}
				
		public function ajax_get_mcu($nama_file_encrypt="")
		{
			
			if(!empty($nama_file_encrypt)){
				
				$decrypted_txt = $this->encrypt_decrypt('decrypt', $nama_file_encrypt);
				list($random,$username,$nama_file,$datetime_request,$chars) = explode("|",$decrypted_txt);
				
				$detail = $this->db->where('id', $nama_file)->get('ess_hasil_mcu')->row();
				$data['data'] = $detail;
				
				$this->load->view($this->folder_view."detail_mcu",$data);
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
					$this->db->where('id', $id)->delete('ess_hasil_mcu');
				}
				else if ($tgl!='0') {
					$this->db->where('tanggal_mcu', $tgl)->delete('ess_hasil_mcu');
				}

				if($this->db->affected_rows() > 0){
					echo json_encode(['status' => 'success', 'result' => 200, 'txt' => 'Berhasil Menghapus Data']);
				}else{
					echo json_encode(['status' => 'error', 'result' => 401, 'txt' => 'Gagal Menghapus Data']);
				}
			}
		}
        
        function ambil_tanggal(){
            try {
                $return='<option value="0">Semua Tanggal Upload</option>';
                $tahun = $this->input->post('tahun',true);
                $this->db->select('tanggal_mcu');
                if($tahun!='0'){
                    $this->db->where('tahun',$tahun);
                }
                $this->db->group_by('tanggal_mcu');
                $get = $this->db->get('ess_hasil_mcu')->result();
                foreach($get as $row){
                    $return .= '<option value="'.$row->tanggal_mcu.'">'.$row->tanggal_mcu.'</option>';
                }
                echo $return;
            } catch(Exception $e){
                echo '';
            }
        }

		/*public function save_file($jenis) {
			if(!strcmp($this->input->post("aksi"),"tambah")){
				$fail = array();
				if ($_FILES['file_individu']['tmp_name'] != '') {
					$all=count($_FILES['file_individu']['tmp_name']);
		            for ($i=0;$i<$all;$i++) {
			            $tempFile = $_FILES['file_individu']['tmp_name'][$i];
			            $fileName = $_FILES['file_individu']['name'][$i];
			            $targetPath = './uploads/mcu_individu/';
			            $pecahnama = explode('.',$fileName);
			            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
			            $targetFile = $targetPath.$fileName;
			            $ekstensi = array('pdf','PDF');

			            $cek = $this->db->where(array('file_mcu'=>$fileName))->count_all_results('ess_hasil_mcu');
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

			        redirect(site_url('sikesper/hasil_mcu'));
		        }
		        else {
		        	echo "<script>alert('UPLOAD GAGAL, File harus diisi !');window.location='".site_url('sikesper/hasil_mcu')."'</script>";
		        }
	        }
	        else {
	        	$this->session->set_flashdata('warning', 'Gagal mengupload file. Anda tidak memiliki hak akses!');
	        }
	        redirect(site_url('sikesper/hasil_mcu'));
		}*/
	}
	
	/* End of file skep.php */
	/* Location: ./application/controllers/informasi/skep.php */
