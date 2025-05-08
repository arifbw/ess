<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pendidikan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'pelaporan/';
			$this->folder_model = 'pelaporan/';
			$this->folder_controller = 'pelaporan/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			
			#$this->load->model($this->folder_model."m_permohonan_cuti");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Laporan Pendidikan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
            $this->nama_db = $this->db->database;
			izin($this->akses["akses"]);
		}
		
		public function index()
		{
            $this->load->model($this->folder_model."M_pelaporan");

            $array_daftar_karyawan	= $this->M_pelaporan->select_daftar_karyawan();

            // echo json_encode($outsource); exit;
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."pendidikan";
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
			$this->data['regulasi']					= @$this->db->where('id_laporan',$this->data['id_modul'])->get('mst_regulasi')->row()->regulasi;

			$this->load->view('template',$this->data);
		}	
		
		public function tabel_pendidikan() {
			if($this->akses["lihat"]) {
	            
				$this->load->model($this->folder_model."M_tabel_pendidikan");
				if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
					$var=array();
					$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
					foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
						array_push($var,$data['kode_unit']);							
					}				
				} else if($_SESSION["grup"]==5) {//jika Pengguna
					$var 	= $_SESSION["no_pokok"];							
				} else {
					$var = 1;				
				}
	            
				$list 	= $this->M_tabel_pendidikan->get_datatables($var);
				$data = array();
				$no = $_POST['start'];
				foreach ($list as $tampil) {
					$no++;

					$row = array();
					$row[] = $no;
					$row[] = $tampil->np_karyawan.' - '.$tampil->nama_karyawan.'<br><small>'.$tampil->nama_unit.'</small>';

					$np_karyawan		= trim($tampil->np_karyawan);
					$nama_karyawan		= trim($tampil->nama_karyawan);
					$perguruan_tinggi	= trim($tampil->perguruan_tinggi);
					$fakultas			= trim($tampil->fakultas);
					$jurusan			= trim($tampil->jenjang).' '.trim($tampil->jurusan);
					$get_approval 		= trim($tampil->approval_np);
					$get_status			= trim($tampil->approval_status);
					$approval_alasan	= trim($tampil->approval_alasan);
					$approval_date		= trim($tampil->approval_date);
					$keterangan			= trim($tampil->keterangan);
					$created_at			= trim($tampil->created_at);

					$row[] = $perguruan_tinggi;
					$row[] = $fakultas;
					$row[] = $jurusan;
					$row[] = $keterangan;

					//DETAIL
					$approval_np 	= $get_approval." | ".nama_karyawan_by_np($get_approval);
					if($get_status=='1') {
						$btn_warna		='btn-success';
						$btn_text		='Disetujui Atasan';
					}else if($get_status=='2') {
						$btn_warna		='btn-danger';
						$btn_text		='Ditolak Atasan';
					} else if($get_status=='0' || $get_status==null) {
						$btn_warna		='btn-default';
						$btn_text		='Menunggu<br>Persetujuan Atasan';
					}

					if($get_status=='1') {
					}else if($get_status=='3') {
						$btn_warna		='btn-success';
						$btn_text		='Verifikasi KAUN SDM';
					}else if($get_status=='4') {
						$btn_warna		='btn-danger';
						$btn_text		='Ditolak KAUN SDM';
					}else if($get_status=='5') {
						$btn_warna		='btn-primary';
						$btn_text		='SUBMIT ERP';
					}else if($get_status=='6') {
						$btn_warna		='btn-danger';
						$btn_text		='Ditolak Admin SDM';
					}

					$row[] = "<button class='btn $btn_warna btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id." data-np=".$tampil->np_karyawan.">$btn_text</button>";

					if ($tampil->approval_status==null || $tampil->approval_status=='0') {
						$np_hapus = $tampil->np_karyawan;

	                    if($this->akses["tambah"]) {
							$edit['id']							= trim($tampil->id);
							$edit['np_karyawan']				= trim($tampil->np_karyawan);
							$edit['perguruan_tinggi']			= trim($tampil->perguruan_tinggi);
							$edit['fakultas']					= trim($tampil->fakultas);
							$edit['jenjang']					= trim($tampil->jenjang);
							$edit['jurusan']					= trim($tampil->jurusan);
							$edit['akreditasi']					= trim($tampil->akreditasi);
							$edit['approval_1_np'] 				= trim($tampil->approval_np);
							$edit['approval_1_input']			= trim($tampil->approval_nama);
							$edit['approval_1_input_jabatan']	= trim($tampil->approval_jabatan);
							$edit['keterangan']					= trim($tampil->keterangan);
							$edit['no_ijazah']					= trim($tampil->no_ijazah);
							$edit['no_transkrip']				= trim($tampil->no_transkrip);
							$set_edit = json_encode($edit);

						    $aksi = '<button class="btn btn-warning btn-xs"';
						    foreach ($edit as $key => $value) {
						    	$aksi .= 'data-'.$key.'="'.$value.'"';
						    }
						    $aksi .= 'onclick="edit(this)">Edit</button> <button class="btn btn-danger btn-xs hapus" data-id="'.$tampil->id.'" data-np="'.$np_hapus.'">Hapus</button>';
	                    }
	                    else
	                        $aksi = '';
					} else {
						$aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id.">Detail</button>";
					}
	                
					$row[] = $aksi;
					
					$data[] = $row;
				}

				$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->M_tabel_pendidikan->count_all($var),
						"recordsFiltered" => $this->M_tabel_pendidikan->count_filtered($var),
						"data" => $data,
				);
				//output to json format
				echo json_encode($output);
			}
		}
		
        function action_insert_pendidikan(){
        	$this->load->helper('form');
			$this->load->library('form_validation');

        	if($this->akses["tambah"]) {
				$fail = array();
				$error = "";

	        	$this->form_validation->set_rules('np_karyawan', 'Karyawan', 'required');
		    	$this->form_validation->set_rules('perguruan_tinggi', 'Perguruan Tinggi', 'required');
		    	$this->form_validation->set_rules('fakultas', 'Fakultas', 'required');
		    	$this->form_validation->set_rules('tgl_masuk', 'Tanggal Masuk Pendidikan', 'required');
		    	$this->form_validation->set_rules('tgl_selesai', 'Tanggal Selesai Pendidikan', 'required');
		    	$this->form_validation->set_rules('jurusan', 'Jurusan', 'required');
		    	$this->form_validation->set_rules('jenjang', 'Jenjang', 'required');
		    	$this->form_validation->set_rules('akreditasi', 'Akreditasi', 'required');
		    	$this->form_validation->set_rules('no_ijazah', 'No Ijazah', 'required');
		    	$this->form_validation->set_rules('no_transkrip', 'No Transrip', 'required');
		    	$this->form_validation->set_rules('approval_1_np', 'Approver 1', 'required');
		    	$this->form_validation->set_rules('approval_1_input', 'Approver 1', 'required');
		    	$this->form_validation->set_rules('approval_1_input_jabatan', 'Approver 1', 'required');

				if (($this->input->post('edit_id',true)=='' && ($_FILES['file_ijazah']['tmp_name'] == '' || $_FILES['file_transkrip']['tmp_name'] == '')) || $this->form_validation->run() == FALSE) {
					$this->session->set_flashdata('warning', 'Data Belum Lengkap');
		            $this->index();
				} else {
		            $submit = $this->input->post('submit');
		            if($submit) {
						$this->load->library('upload');

						if ($_FILES['file_ijazah']['tmp_name'] != '') {
							//IJAZAH
							$config['upload_path'] = './uploads/pelaporan/pendidikan/ijazah';
							$config['allowed_types'] = 'pdf';
							$config['max_size']	= '2148';
							$files = $_FILES;

							$this->upload->initialize($config);

							if($files['file_ijazah']['name']) {
								$this->load->helper("file");
								if($this->upload->do_upload('file_ijazah')) {
									$up = $this->upload->data();
									$file_ijazah = $up['file_name'];
								} else {
									$error = $this->upload->display_errors();
								}
							} else {
								$error = "File Ijazah Tidak Ditemukan";
							}
						}

						if ($_FILES['file_transkrip']['tmp_name'] != '') {
							//TRANSKRIP
							$config['upload_path'] = './uploads/pelaporan/pendidikan/transkrip';
							$config['allowed_types'] = 'pdf';
							$config['max_size']	= '2148';
							$files = $_FILES;

							$this->upload->initialize($config);

							if($files['file_transkrip']['name']) {
								$this->load->helper("file");
								if($this->upload->do_upload('file_transkrip')) {
									$up = $this->upload->data();
									$file_transkrip = $up['file_name'];
								} else {
									$error = $this->upload->display_errors();
				            		// $this->load->view('pelaporan/pendidikan');
								}
							} else {
								$error = "File Transkrip Tidak Ditemukan";
								// $this->load->view('pelaporan/pendidikan');
							}
						}

						if ($error=="") {
				            $data_insert = [];
			                $np_karyawan		= $this->input->post('np_karyawan', true);
							$approval_np		= $this->input->post('approval_1_np', true);
							$approval_nama		= $this->input->post('approval_1_input', true);
							$approval_jabatan	= $this->input->post('approval_1_input_jabatan', true);
			                
							$start_date			= date('Y-m-d');
							$end_date			= date('Y-m-d');
			                $tahun_bulan     	= $start_date!=null ? str_replace('-','_',substr("$start_date", 0, 7)) : str_replace('-','_',substr("$end_date", 0, 7)) ;
							$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
							$personel_number	= erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'];
							$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
							$kode_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'];
							$nama_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'];
			                
			                $data_insert = [
			                    'np_karyawan'=>$np_karyawan,
			                    'nama_karyawan'=>$nama_karyawan,
			                    'personel_number'=>$personel_number,
			                    'nama_jabatan'=>$nama_jabatan,
			                    'kode_unit'=>$kode_unit,
			                    'nama_unit'=>$nama_unit,

			                    'tgl_masuk'=>$this->input->post('tgl_masuk', true),
			                    'tgl_selesai'=>$this->input->post('tgl_selesai', true),
			                    'fakultas'=>$this->input->post('fakultas', true),
			                    'perguruan_tinggi'=>$this->input->post('perguruan_tinggi', true),
			                    'jenjang'=>$this->input->post('jenjang', true),
			                    'akreditasi'=>$this->input->post('akreditasi', true),
			                    'jurusan'=>$this->input->post('jurusan', true),
			                    'no_ijazah'=>$this->input->post('no_ijazah', true),
			                    'no_transkrip'=>$this->input->post('no_transkrip', true),
			                    'keterangan'=>$this->input->post('keterangan', true),

			                    'approval_np'=>$approval_np,
			                    'approval_nama'=>$approval_nama,
			                    'approval_jabatan'=>$approval_jabatan,
			                ];

			                if (@$file_ijazah)
			                    $data_insert['file_ijazah']=$file_ijazah;
			                if (@$file_transkrip)
			                    $data_insert['file_transkrip']=$file_transkrip;

			                if ($this->input->post('edit_id', true)!='') {
			                    $data_lama = $this->db->where('id', $this->input->post('edit_id', true))->get('ess_laporan_pendidikan')->row();

			                    $data_insert['updated_at'] = date('Y-m-d H:i:s');
			                    $data_insert['updated_by'] = $_SESSION['no_pokok'];
			                    $this->db->set($data_insert)->where('id', $this->input->post('edit_id', true))->update('ess_laporan_pendidikan');
			                } else {
			                    $data_insert['created_at'] = date('Y-m-d H:i:s');
			                    $data_insert['created_by'] = $_SESSION['no_pokok'];
			                    $this->db->set($data_insert)->insert('ess_laporan_pendidikan');
			                }

			                if ($this->db->affected_rows() > 0) {
			                	if ($this->input->post('edit_id', true)!='') {
					                if (@$file_ijazah)
					                	unlink('./uploads/pelaporan/pendidikan/ijazah/'.$data_lama->file_ijazah);
					                if (@$file_transkrip)
					                	unlink('./uploads/pelaporan/pendidikan/transkrip/'.$data_lama->file_transkrip);
					            }
		                		$this->session->set_flashdata('success',"Berhasil Update Laporan Pendidikan");
			                } else {
		                		$this->session->set_flashdata('warning',"Gagal Update Laporan Pendidikan");
			                }

							redirect(base_url($this->folder_controller.'pendidikan'));
						} else {
		                	$this->session->set_flashdata('warning',"Terjadi Kesalahan Upload , $error");
							$this->load->view('pelaporan/pendidikan');
						}
		            } else{
		                $this->session->set_flashdata('warning',"Terjadi Kesalahan Input Data");
						redirect(base_url($this->folder_controller.'pendidikan'));	
		            }
		        }
		    } else{
                $this->session->set_flashdata('warning',"Anda Tidak Memiliki Hak Akses");
				redirect(base_url($this->folder_controller.'pendidikan'));	
            }
        }

		public function view_detail()
		{			
			$id = $this->input->post('id_');
			$tabel = 'ess_laporan_pendidikan';

			$lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
			$data['detail'] = $lap;

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['approval_status'] = "Laporan pendidikan <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>";
				$data['approval_warna'] ='success';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan pendidikan <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>"; 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='3' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['sdm_status'] = "Laporan pendidikan <b>DIVERIFIKASI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna']= 'success';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan pendidikan <b>TIDAK DISETUJUI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna'] = 'danger';
			}

			if($lap['approval_status']=='5') {
				$data['submit_status'] = "Laporan pendidikan <b>DISUBMIT SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'info';
			}
			else if($lap['approval_status']=='6') {
				$data['submit_status'] = "Laporan pendidikan <b>DITOLAK ADMIN SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'danger';
			}
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$this->load->view($this->folder_view."detail_pendidikan",$data);
		}

        public function hapus($id=null, $np=null) {
            $this->load->model($this->folder_model."M_pelaporan");
			if(@$id != null && @$np != null) {
                $get = $this->M_pelaporan->ambil_by_id($id, 'ess_laporan_pendidikan');
                $this->db->where('id', $id)->set(array('deleted_at'=>date('Y-m-d H:i:s'), 'deleted_by'=>$_SESSION['no_pokok']))->update("ess_laporan_pendidikan");

				if($this->db->affected_rows() > 0) {
                    $return["status"] = true;

                    $log_data_lama = "";
                    foreach($get as $key => $value){
                        if(strcmp($key,"id")!=0){
                            if(!empty($log_data_lama)){
                                $log_data_lama .= "<br>";
                            }
                            $log_data_lama .= "$key = $value";
                        }
                    }

                    $log = array(
                        "id_pengguna" => $this->session->userdata("id_pengguna"),
                        "id_modul" => $this->data['id_modul'],
                        "id_target" => $get["id"],
                        "deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
                        "kondisi_lama" => $log_data_lama,
                        "kondisi_baru" => '',
                        "alamat_ip" => $this->data["ip_address"],
                        "waktu" => date("Y-m-d H:i:s")
                    );
                    $this->m_log->tambah($log);

                    $return['status'] = true;
                    $return['msg'] = 'Laporan Pendidikan Berhasil Dihapus';
                } else {
                	$return['status'] = false;
                    $return['msg'] = 'Laporan Pendidikan Gagal Dihapus';
                }
            } else {
				$return['status'] = false;
				$return['msg'] = 'Laporan Pendidikan Gagal Dihapus';
			}

			echo json_encode($return);
		}

		public function ajax_getNama_approval()
		{
			$np_atasan = $this->input->post('np_aprover');
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$np_karyawan = $this->input->post('np_karyawan');
				$kode_unit = array(kode_unit_by_np($np_karyawan));
			} else if($_SESSION["grup"]==5) { //jika Pengguna
				$np_karyawan = $_SESSION["no_pokok"];
				$kode_unit = array($_SESSION["kode_unit"]);
			} else {
				$np_karyawan = $this->input->post('np_karyawan');
				$kode_unit = array(kode_unit_by_np($np_karyawan));
			}

			$return = [
                'status'=>false,
                'data'=>[],
                'message'=>'Silahkan isi No. Pokok Atasan Dengan Benar',
            ];

			if ($np_atasan==$np_karyawan) {
				$return['message'] = 'No. Pokok Approver Tidak Valid';
			} else {
				$this->load->model('m_approval');

				$list = $this->m_approval->list_atasan_minimal_kasek($kode_unit, $np_karyawan);
				$return['message'] = 'Approval Pelaporan Minimal Kasek';

				$list_np = array_column($list, 'no_pokok');
				if (in_array($np_atasan, $list_np)) {
					$key = array_search($np_atasan, $list_np);
					$data['nama'] = $list[$key]['nama'];
					$data['nama_jabatan'] = $list[$key]['nama_jabatan'];
				}


				if (@$data) {
	                $return = [
	                    'status'=>true,
	                    'data'=>[
	                        'nama'=>$data['nama'],
	                        'jabatan'=>$data['nama_jabatan']
	                    ]
	                ];
				} else {
					$start_date			= date('Y-m-d');
					$end_date			= date('Y-m-d');
	                $tahun_bulan     	= $start_date!=null ? str_replace('-','_',substr("$start_date", 0, 7)) : str_replace('-','_',substr("$end_date", 0, 7)) ;
					$nama_karyawan 		= erp_master_data_by_np($np_atasan, $start_date)['nama'];
					$nama_jabatan		= erp_master_data_by_np($np_atasan, $start_date)['nama_jabatan'];
					
					$return = [
	                    'status'=>true,
	                    'data'=>[
	                        'nama'=>$nama_karyawan,
	                        'jabatan'=>$nama_jabatan
	                    ]
	                ];
				}
			}

            echo json_encode($return);
		}

        public function cetak() {
			$this->load->library('phpexcel'); 
			$this->load->model($this->folder_model."M_tabel_pendidikan");
			$set['np_karyawan'] = $this->input->post('np_karyawan');
			$jenis = array();
			$filter_bulan   = $this->input->post('bulan');
            if($this->input->post('izin_D')==1)
                $jenis[] = 'D';
            if($this->input->post('izin_E')==1)
                $jenis[] = 'E';
            if($this->input->post('izin_F')==1)
                $jenis[] = 'F';
            if($this->input->post('izin_G')==1)
                $jenis[] = 'G';
            if($this->input->post('izin_H')==1)
                $jenis[] = 'H';
            if($this->input->post('izin_TM')==1)
                $jenis[] = 'TM';
            if($this->input->post('izin_TK')==1)
                $jenis[] = 'TK';
            if($this->input->post('izin_0')==1)
                $jenis[] = '0';

            $target_table   = $filter_bulan;
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$var = array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var,$data['kode_unit']);							
				}
			} 
			else if($_SESSION["grup"]==5) //jika Pengguna
				$var = $_SESSION["no_pokok"];							
			else
				$var = 1;
			$get_data = $this->M_tabel_pendidikan->_get_excel($var,$target_table,$set,@$jenis);

	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        header("Content-Disposition: attachment; filename=pendidikan.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_pendidikan.xlsx');

	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 4;
	        $no = 1;
			
			foreach ($get_data as $tampil) {
                $absence_type = $tampil->kode_pamlek.'|'.$tampil->info_type.'|'.$tampil->absence_type;
				$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, get_perizinan_name($tampil->kode_pamlek)->nama, PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->start_date)
					$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, tanggal_indonesia($tampil->start_date).' '.$tampil->start_time, PHPExcel_Cell_DataType::TYPE_STRING);
				else
					$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->end_date)
					$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, tanggal_indonesia($tampil->end_date).' '.$tampil->end_time, PHPExcel_Cell_DataType::TYPE_STRING);
				else
					$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
				$awal += 1;	
			}

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
		}
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */