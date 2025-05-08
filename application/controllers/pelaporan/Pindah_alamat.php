<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Pindah_alamat extends CI_Controller {

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
			
			$this->data['judul'] = "Laporan Pindah Alamat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
            $this->nama_db = $this->db->database;
			izin($this->akses["akses"]);
		}

        public function index() {
            $this->load->model($this->folder_model."M_pelaporan");

            $array_daftar_karyawan	= $this->M_pelaporan->select_daftar_karyawan();

			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."pindah_alamat";
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
			$this->data['provinsi']					= $this->db->get('provinsi')->result();

			$this->load->view('template',$this->data);
		}

        public function tabel_pindah_alamat() {
            $this->load->model($this->folder_model."M_tabel_pindah_alamat");
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
            
			$list 	= $this->M_tabel_pindah_alamat->get_datatables($var);
			$data = array();
			$no = $_POST['start'];
            foreach ($list as $tampil) {
				$no++;
				$get_status			= trim($tampil->approval_status);
				$keterangan			= trim($tampil->keterangan);

				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan.' - '.$tampil->nama_karyawan.'<br><small>'.$tampil->nama_unit.'</small>';
				$row[] = $tampil->alamat_lama.'<br><small>'.$tampil->kelurahan_lama.', '.$tampil->kecamatan_lama.', '.$tampil->kabupaten_lama.', '.$tampil->provinsi_lama.'</small>';
				$row[] = $tampil->alamat_baru.'<br><small>'.$tampil->kelurahan_baru.', '.$tampil->kecamatan_baru.', '.$tampil->kabupaten_baru.', '.$tampil->provinsi_baru.'</small>';
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

						$edit['alamat_lama'] = trim($tampil->alamat_lama);
						$edit['kode_kelurahan_lama'] = trim($tampil->kode_kelurahan_lama);
						$edit['kode_kecamatan_lama'] = trim($tampil->kode_kecamatan_lama);
						$edit['kode_kabupaten_lama'] = trim($tampil->kode_kabupaten_lama);
						$edit['kode_provinsi_lama'] = trim($tampil->kode_provinsi_lama);
						$edit['kode_pos_lama'] = trim($tampil->kode_pos_lama);

						$edit['alamat_baru'] = trim($tampil->alamat_baru);
						$edit['kode_kelurahan_baru'] = trim($tampil->kode_kelurahan_baru);
						$edit['kode_kecamatan_baru'] = trim($tampil->kode_kecamatan_baru);
						$edit['kode_kabupaten_baru'] = trim($tampil->kode_kabupaten_baru);
						$edit['kode_provinsi_baru'] = trim($tampil->kode_provinsi_baru);
						$edit['kode_pos_baru'] = trim($tampil->kode_pos_baru);

						$edit['no_ktp'] = trim($tampil->no_ktp);

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
                "recordsTotal" => $this->M_tabel_pindah_alamat->count_all($var),
                "recordsFiltered" => $this->M_tabel_pindah_alamat->count_filtered($var),
                "data" => $data,
			);
        	//output to json format
			echo json_encode($output);
        }

        function action_insert_pindah_alamat() {
			$this->load->model($this->folder_model."M_pelaporan");
            $this->load->helper('form');
			$this->load->library('form_validation');
            
            if($this->akses["tambah"]) {
				$fail = array();
				$error = "";

                $this->form_validation->set_rules('np_karyawan', 'Karyawan', 'required');
                $this->form_validation->set_rules('alamat_lama', 'Alamat Lama', 'required');
				$this->form_validation->set_rules('jenis_alamat_lama', 'Jenis Alamat Lama', 'required');
                $this->form_validation->set_rules('kode_kelurahan_lama', 'Kelurahan Lama', 'required');
                $this->form_validation->set_rules('kode_kecamatan_lama', 'Kecamatan Lama', 'required');
                $this->form_validation->set_rules('kode_kabupaten_lama', 'Kabupaten Lama', 'required');
                $this->form_validation->set_rules('kode_provinsi_lama', 'Provinsi Lama', 'required');
                $this->form_validation->set_rules('kode_pos_lama', 'Kode Pos Lama', 'required');
				$this->form_validation->set_rules('alamat_baru', 'Alamat Baru', 'required');
				$this->form_validation->set_rules('jenis_alamat_baru', 'Jenis Alamat Baru', 'required');
                $this->form_validation->set_rules('kode_kelurahan_baru', 'Kelurahan Baru', 'required');
                $this->form_validation->set_rules('kode_kecamatan_baru', 'Kecamatan Baru', 'required');
                $this->form_validation->set_rules('kode_kabupaten_baru', 'Kabupaten Baru', 'required');
                $this->form_validation->set_rules('kode_provinsi_baru', 'Provinsi Baru', 'required');
                $this->form_validation->set_rules('kode_pos_baru', 'Kode Pos Baru', 'required');
                $this->form_validation->set_rules('no_ktp', 'No KTP', 'required');
				
				$this->form_validation->set_rules('approval_1_np', 'Approver 1', 'required');
				$this->form_validation->set_rules('approval_1_input', 'Approver 1', 'required');
				$this->form_validation->set_rules('approval_1_input_jabatan', 'Approver 1', 'required');

				if (($this->input->post('edit_id',true)=='' && $_FILES['ktp']['tmp_name'] == '') || $this->form_validation->run() == FALSE) {
					$this->session->set_flashdata('warning', 'Data Belum Lengkap');
		            $this->index();
				}
                else {
                    $submit = $this->input->post('submit');
                    if($submit) {
						$this->load->library('upload');

						if ($_FILES['ktp']['tmp_name'] != '') {
							//Surat Keterangan
							$config['upload_path'] = './uploads/pelaporan/pindah_alamat/ktp';
							$config['allowed_types'] = 'pdf|jpg|png|jpeg';
							$config['max_size']	= '2148';
							$files = $_FILES;

							$this->upload->initialize($config);

							if($files['ktp']['name']) {
								$this->load->helper("file");
								if($this->upload->do_upload('ktp')) {
									$up = $this->upload->data();
									$ktp = $up['file_name'];
								} else {
									$error = $this->upload->display_errors();
								}
							} else {
								$error = "File KTP Tidak Ditemukan";
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

                                'alamat_lama'=>$this->input->post('alamat_lama', true),
                                'jenis_alamat_lama'=>$this->input->post('jenis_alamat_lama', true),
                                'kode_kelurahan_lama'=>$this->input->post('kode_kelurahan_lama', true),
                                'kode_kecamatan_lama'=>$this->input->post('kode_kecamatan_lama', true),
                                'kode_kabupaten_lama'=>$this->input->post('kode_kabupaten_lama', true),
                                'kode_provinsi_lama'=>$this->input->post('kode_provinsi_lama', true),
                                'kode_pos_lama'=>$this->input->post('kode_pos_lama', true),
								'kelurahan_lama'=>$this->M_pelaporan->nama_kelurahan($this->input->post('kode_kelurahan_lama', true)),
								'kecamatan_lama'=>$this->M_pelaporan->nama_kecamatan($this->input->post('kode_kecamatan_lama', true)),
								'kabupaten_lama'=>$this->M_pelaporan->nama_kabupaten($this->input->post('kode_kabupaten_lama', true)),
								'provinsi_lama'=>$this->M_pelaporan->nama_provinsi($this->input->post('kode_provinsi_lama', true)),

								'alamat_baru'=>$this->input->post('alamat_baru', true),
                                'jenis_alamat_baru'=>$this->input->post('jenis_alamat_baru', true),
                                'kode_kelurahan_baru'=>$this->input->post('kode_kelurahan_baru', true),
                                'kode_kecamatan_baru'=>$this->input->post('kode_kecamatan_baru', true),
                                'kode_kabupaten_baru'=>$this->input->post('kode_kabupaten_baru', true),
                                'kode_provinsi_baru'=>$this->input->post('kode_provinsi_baru', true),
                                'kode_pos_baru'=>$this->input->post('kode_pos_baru', true),
								'kelurahan_baru'=>$this->M_pelaporan->nama_kelurahan($this->input->post('kode_kelurahan_baru', true)),
								'kecamatan_baru'=>$this->M_pelaporan->nama_kecamatan($this->input->post('kode_kecamatan_baru', true)),
								'kabupaten_baru'=>$this->M_pelaporan->nama_kabupaten($this->input->post('kode_kabupaten_baru', true)),
								'provinsi_baru'=>$this->M_pelaporan->nama_provinsi($this->input->post('kode_provinsi_baru', true)),

                                'no_ktp'=>$this->input->post('no_ktp', true),
                                'keterangan'=>$this->input->post('keterangan', true),
                                'approval_np'=>$approval_np,
                                'approval_nama'=>$approval_nama,
                                'approval_jabatan'=>$approval_jabatan,
                            ];

							if (@$ktp)
								$data_insert['ktp']=$ktp;

                            if ($this->input->post('edit_id', true)!='') {
								$data_lama = $this->db->where('id', $this->input->post('edit_id', true))->get('ess_laporan_pindah_alamat')->row();

                                $data_insert['updated_at'] = date('Y-m-d H:i:s');
                                $data_insert['updated_by'] = $_SESSION['no_pokok'];
                                $this->db->set($data_insert)->where('id', $this->input->post('edit_id', true))->update('ess_laporan_pindah_alamat');
                            } else {
                                $data_insert['created_at'] = date('Y-m-d H:i:s');
                                $data_insert['created_by'] = $_SESSION['no_pokok'];
                                $this->db->set($data_insert)->insert('ess_laporan_pindah_alamat');
                            }
                            
                            if ($this->db->affected_rows() > 0) {
								if ($this->input->post('edit_id', true)!='') {
									if (@$ktp)
										unlink('./uploads/pelaporan/pindah_alamat/ktp/'.$data_lama->ktp);
								}
								$this->session->set_flashdata('success',"Berhasil Update Laporan Pindah Alamat");
							}
                            else {
								$this->session->set_flashdata('warning',"Gagal Update Laporan Pindah Alamat");
							}
                            redirect(base_url($this->folder_controller.'pindah_alamat'));
						} else {
                            $this->session->set_flashdata('warning',"Terjadi Kesalahan Upload , $error");
                            redirect(base_url($this->folder_controller.'pindah_alamat'));
						}
                    } else{
                        $this->session->set_flashdata('warning',"Terjadi Kesalahan Input Data");
						redirect(base_url($this->folder_controller.'pindah_alamat'));
                    }
                }
            } else{
                $this->session->set_flashdata('warning',"Anda Tidak Memiliki Hak Akses");
				redirect(base_url($this->folder_controller.'pindah_alamat'));	
            }
        }

		public function get_kab() {
			$result = '';
			$kode = $this->input->get('kode');
	
			$kabupaten = $this->db->select('kode_wilayah, nama')->where('kode_prop', $kode)->order_by('nama', 'asc')->get('kabupaten')->result();
			$result .='<option value="" selected disabled hidden></option>';
			foreach ($kabupaten as $kab) {
				$result .= '<option value="' . $kab->kode_wilayah . '">' . $kab->nama . '</option>';
			}
			header('Content-Type: application/json');
			echo json_encode($result);
		}

		public function get_kec() {
			$result = '';
			$kode = $this->input->get('kode');
	
			$kabupaten = $this->db->select('kode_wilayah, nama')->where('kode_kab', $kode)->order_by('nama', 'asc')->get('kecamatan')->result();
			$result .='<option value="" selected disabled hidden></option>';
			foreach ($kabupaten as $kab) {
				$result .= '<option value="' . $kab->kode_wilayah . '">' . $kab->nama . '</option>';
			}
			header('Content-Type: application/json');
			echo json_encode($result);
		}

		public function get_kel() {
			$result = '';
			$kode = $this->input->get('kode');
	
			$kabupaten = $this->db->select('kode_wilayah, nama')->where('kode_kec', $kode)->order_by('nama', 'asc')->get('kelurahan')->result();
			$result .='<option value="" selected disabled hidden></option>';
			foreach ($kabupaten as $kab) {
				$result .= '<option value="' . $kab->kode_wilayah . '">' . $kab->nama . '</option>';
			}
			header('Content-Type: application/json');
			echo json_encode($result);
		}

		public function view_detail()
		{			
			$id = $this->input->post('id_');
			$tabel = 'ess_laporan_pindah_alamat';

			$lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
			$data['detail'] = $lap;

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['approval_status'] = "Laporan pindah alamat <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>";
				$data['approval_warna'] ='success';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan pindah alamat <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>"; 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='3' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['sdm_status'] = "Laporan pindah alamat <b>DIVERIFIKASI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna']= 'success';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan pindah alamat <b>TIDAK DISETUJUI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna'] = 'danger';
			}

			if($lap['approval_status']=='5') {
				$data['submit_status'] = "Laporan pindah alamat <b>DISUBMIT SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'info';
			}
			else if($lap['approval_status']=='6') {
				$data['submit_status'] = "Laporan pindah alamat <b>DITOLAK ADMIN SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'danger';
			}
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$this->load->view($this->folder_view."detail_pindah_alamat",$data);
		}

		public function hapus($id=null, $np=null) {
            $this->load->model($this->folder_model."M_pelaporan");
			if(@$id != null && @$np != null) {
                $get = $this->M_pelaporan->ambil_by_id($id, 'ess_laporan_pindah_alamat');
                $this->db->where('id', $id)->set(array('deleted_at'=>date('Y-m-d H:i:s'), 'deleted_by'=>$_SESSION['no_pokok']))->update("ess_laporan_pindah_alamat");

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
                    $return['msg'] = 'Laporan Pindah Alamat Berhasil Dihapus';
                } else {
					$return['status'] = false;
                    $return['msg'] = 'Laporan Pindah Alamat Gagal Dihapus';
                }
            } else {
				$return['status'] = false;
				$return['msg'] = 'Laporan Pindah Alamat Gagal Dihapus';
			}

			echo json_encode($return);
		}
    }