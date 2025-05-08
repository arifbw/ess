<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Pindah_agama extends CI_Controller {

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
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Laporan Pindah Agama";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
            $this->nama_db = $this->db->database;
			izin($this->akses["akses"]);
		}

        public function index() {
            $this->load->model($this->folder_model."M_pelaporan");

            $array_daftar_karyawan	= $this->M_pelaporan->select_daftar_karyawan();
            $array_daftar_agama	= $this->db->get('mst_agama');

			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."pindah_agama";
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
            $this->data['array_agama'] 				= $array_daftar_agama;
			$this->data['regulasi']					= @$this->db->where('id_laporan',$this->data['id_modul'])->get('mst_regulasi')->row()->regulasi;

			$this->load->view('template',$this->data);
		}

        public function tabel_pindah_agama() {
            $this->load->model($this->folder_model."M_tabel_pindah_agama");
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$var = array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var,$data['kode_unit']);							
				}				
			} else if($_SESSION["grup"]==5) {//jika Pengguna
				$var = $_SESSION["no_pokok"];							
			} else {
				$var = 1;				
			}
            
			$list = $this->M_tabel_pindah_agama->get_datatables($var);
			$data = array();
			$no = $_POST['start'];
            foreach ($list as $tampil) {
				$no++;
				$get_status			= trim($tampil->approval_status);
				$keterangan			= trim($tampil->keterangan);

				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan.' - '.$tampil->nama_karyawan.'<br><small>'.$tampil->nama_unit.'</small>';
				$row[] = $tampil->agama_lama;
				$row[] = $tampil->agama_baru;
				$row[] = $tampil->tanggal_pindah;
				$row[] = $keterangan;

                //DETAIL
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

                    if($this->akses["tambah"]){
						$edit['id']	= trim($tampil->id);
						$edit['np_karyawan'] = trim($tampil->np_karyawan);
						$edit['agama_lama'] = trim($tampil->agama_lama);
						$edit['agama_baru'] = trim($tampil->agama_baru);
						$edit['tanggal_pindah'] = trim($tampil->tanggal_pindah);
						$edit['no_surat_keterangan'] = trim($tampil->no_surat_keterangan);

						$edit['approval_1_np'] = trim($tampil->approval_np);
						$edit['approval_1_input'] = trim($tampil->approval_nama);
						$edit['approval_1_input_jabatan'] = trim($tampil->approval_jabatan);
						$edit['keterangan']	= trim($tampil->keterangan);

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
                "recordsTotal" => $this->M_tabel_pindah_agama->count_all($var),
                "recordsFiltered" => $this->M_tabel_pindah_agama->count_filtered($var),
                "data" => $data,
			);
        	//output to json format
			echo json_encode($output);
        }

        function action_insert_pindah_agama() {
            $this->load->helper('form');
			$this->load->library('form_validation');
            
            if($this->akses["tambah"]) {
				$fail = array();
				$error = "";

                $this->form_validation->set_rules('np_karyawan', 'Karyawan', 'required');
                $this->form_validation->set_rules('agama_lama', 'Agama Lama', 'required');
                $this->form_validation->set_rules('agama_baru', 'Agama Baru', 'required');
                $this->form_validation->set_rules('tanggal_pindah', 'Tanggal Pindah', 'required');
                $this->form_validation->set_rules('no_surat_keterangan', 'No Surat Keterangan', 'required');
				
				$this->form_validation->set_rules('approval_1_np', 'Approver 1', 'required');
				$this->form_validation->set_rules('approval_1_input', 'Approver 1', 'required');
				$this->form_validation->set_rules('approval_1_input_jabatan', 'Approver 1', 'required');

				if (($this->input->post('edit_id',true)=='' && $_FILES['surat_keterangan']['tmp_name'] == '') || $this->form_validation->run() == FALSE) {
					$this->session->set_flashdata('warning', 'Data Belum Lengkap');
		            $this->index();
				}
                else {
                    $submit = $this->input->post('submit');
                    if($submit) {
						$this->load->library('upload');

						if ($_FILES['surat_keterangan']['tmp_name'] != '') {
							//Surat Keterangan
							$config['upload_path'] = './uploads/pelaporan/pindah_agama/surat_keterangan';
							$config['allowed_types'] = 'pdf|jpg|png|jpeg';
							$config['max_size']	= '2148';
							$files = $_FILES;

							$this->upload->initialize($config);

							if($files['surat_keterangan']['name']) {
								$this->load->helper("file");
								if($this->upload->do_upload('surat_keterangan')) {
									$up = $this->upload->data();
									$surat_keterangan = $up['file_name'];
								} else {
									$error = $this->upload->display_errors();
								}
							} else {
								$error = "File Surat Keterangan Tidak Ditemukan";
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
                            
                            $data_insert = [
                                'np_karyawan' => $np_karyawan,
                                'nama_karyawan' => erp_master_data_by_np($np_karyawan, $start_date)['nama'],
                                'personel_number' => erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'],
                                'nama_jabatan' => erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'],
                                'kode_unit' => erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'],
                                'nama_unit' => erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'],
                                'tempat_lahir' => erp_master_data_by_np($np_karyawan, $start_date)['tempat_lahir'],
                                'tanggal_lahir' => erp_master_data_by_np($np_karyawan, $start_date)['tanggal_lahir'],
                                'agama' => erp_master_data_by_np($np_karyawan, $start_date)['agama'],

                                'agama_lama'=>$this->input->post('agama_lama', true),
                                'agama_baru'=>$this->input->post('agama_baru', true),
                                // 'tanggal_pindah'=>date('Y-m-d',strtotime($this->input->post('tanggal_pindah', true))),
								'tanggal_pindah'=>$this->input->post('tanggal_pindah', true),

                                'no_surat_keterangan'=>$this->input->post('no_surat_keterangan', true),

                                'keterangan'=>$this->input->post('keterangan', true),
                                'approval_np'=>$approval_np,
                                'approval_nama'=>$approval_nama,
                                'approval_jabatan'=>$approval_jabatan,
                            ];

							if (@$surat_keterangan)
								$data_insert['surat_keterangan']=$surat_keterangan;
                            
                            if ($this->input->post('edit_id', true)!='') {
								$data_lama = $this->db->where('id', $this->input->post('edit_id', true))->get('ess_laporan_pindah_agama')->row();

                                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                                $data_insert['updated_by'] = $_SESSION['no_pokok'];
                                $this->db->set($data_insert)->where('id', $this->input->post('edit_id', true))->update('ess_laporan_pindah_agama');
                            } else {
                                $data_insert['created_at'] = date('Y-m-d H:i:s');
                                $data_insert['created_by'] = $_SESSION['no_pokok'];
                                $this->db->set($data_insert)->insert('ess_laporan_pindah_agama');
                            }
                            
                            if ($this->db->affected_rows() > 0){
								if ($this->input->post('edit_id', true)!='') {
									if (@$surat_keterangan)
										unlink('./uploads/pelaporan/pindah_agama/surat_keterangan/'.$data_lama->surat_keterangan);
								}
                                $this->session->set_flashdata('success',"Berhasil Update Laporan Pindah Agama");
							}
                            else{
                                $this->session->set_flashdata('warning',"Gagal Update Laporan Pindah Agama");
							}
							redirect(base_url($this->folder_controller.'pindah_agama'));
						} else {
                            $this->session->set_flashdata('warning',"Terjadi Kesalahan Upload , $error");
                            redirect(base_url($this->folder_controller.'pindah_agama'));
						}
                    } else{
                        $this->session->set_flashdata('warning',"Terjadi Kesalahan Input Data");
						redirect(base_url($this->folder_controller.'pindah_agama'));
                    }
                }
            } else{
                $this->session->set_flashdata('warning',"Anda Tidak Memiliki Hak Akses");
				redirect(base_url($this->folder_controller.'pindah_agama'));	
            }
        }

        public function view_detail()
		{			
			$id = $this->input->post('id_');
			$tabel = 'ess_laporan_pindah_agama';

			$lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
			$data['detail'] = $lap;

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['approval_status'] = "Laporan pindah agama <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>";
				$data['approval_warna'] ='success';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan pindah agama <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>"; 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='3' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['sdm_status'] = "Laporan pindah agama <b>DIVERIFIKASI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna']= 'success';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan pindah agama <b>TIDAK DISETUJUI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna'] = 'danger';
			}

			if($lap['approval_status']=='5') {
				$data['submit_status'] = "Laporan pindah agama <b>DISUBMIT SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'info';
			}
			else if($lap['approval_status']=='6') {
				$data['submit_status'] = "Laporan pindah agama <b>DITOLAK ADMIN SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'danger';
			}
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$this->load->view($this->folder_view."detail_pindah_agama",$data);
		}

		public function ajax_getNama_approval() {
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

		public function hapus($id=null, $np=null) {
            $this->load->model($this->folder_model."M_pelaporan");
			if(@$id != null && @$np != null) {
                $get = $this->M_pelaporan->ambil_by_id($id, 'ess_laporan_pindah_agama');
                $this->db->where('id', $id)->set(array('deleted_at'=>date('Y-m-d H:i:s'), 'deleted_by'=>$_SESSION['no_pokok']))->update("ess_laporan_pindah_agama");

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
                    $return['msg'] = 'Laporan Pindah Agama Berhasil Dihapus';
                } else {
					$return['status'] = false;
                    $return['msg'] = 'Laporan Pindah Agama Gagal Dihapus';
                }
            } else {
				$return['status'] = false;
				$return['msg'] = 'Laporan Pindah Agama Gagal Dihapus';
			}

			echo json_encode($return);
		}
    }