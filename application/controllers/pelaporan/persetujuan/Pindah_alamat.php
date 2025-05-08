<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
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
			
			#$this->load->model($this->folder_model."m_permohonan_cuti");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Persetujuan Pindah Alamat";
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
			$this->data['content'] 					= $this->folder_view."persetujuan/pindah_alamat";
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;

			$this->load->view('template',$this->data);
		}	
		
		public function tabel_pindah_alamat() {
            $jenis = array();
            
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
            
			$list 	= $this->M_tabel_pindah_alamat->get_datatables(null, 'persetujuan');
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$get_approval 		= trim($tampil->approval_np);
				$get_status			= trim($tampil->approval_status);
				$approval_alasan	= trim($tampil->approval_alasan);
				$keterangan			= trim($tampil->keterangan);
				$created_at			= trim($tampil->created_at);

				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan.' - '.$tampil->nama_karyawan.'<br><small>'.$tampil->nama_unit.'</small>';
				$row[] = $tampil->alamat_lama.'<br><small>'.$tampil->kelurahan_lama.', '.$tampil->kecamatan_lama.', '.$tampil->kabupaten_lama.', '.$tampil->provinsi_lama.'</small>';
				$row[] = $tampil->alamat_baru.'<br><small>'.$tampil->kelurahan_baru.', '.$tampil->kecamatan_baru.', '.$tampil->kabupaten_baru.', '.$tampil->provinsi_baru.'</small>';
				$row[] = $keterangan;

				//DETAIL
				$approval_np = $get_approval." | ".nama_karyawan_by_np($get_approval);
				if($get_status=='1') {
					$btn_warna		='btn-success';
					$btn_text		='Disetujui Atasan';
				}else if($get_status=='2') {
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Atasan';
				} else if($get_status=='0' || $get_status==null) {
					$approval_status 	= "Menunggu Persetujuan Atasan"; 
					
					$btn_warna		='btn-default';
					$btn_text		='Menunggu<br>Persetujuan Atasan';
				}

				if($get_status=='1') {
					$sdm_status 	= "Menunggu Verifikasi SDM";
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

                    if($this->akses["persetujuan"])
						$aksi = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal' data-target='#modal_persetujuan' data-id=".$tampil->id.">Persetujuan</button>";
                    else
                        $aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id.">Detail</button>";
				} else {
					$aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id.">Detail</button>";
				}
                
				$row[] = $aksi;
				
				$data[] = $row;
			}

			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->M_tabel_pindah_alamat->count_all(null, 'persetujuan'),
				"recordsFiltered" => $this->M_tabel_pindah_alamat->count_filtered(null, 'persetujuan'),
				"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		} 

		public function view_detail() {			
			$id = $this->input->post('id_');
			$tabel = 'ess_laporan_pindah_alamat';

			$lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
			$data['detail'] = $lap;

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5') {
				$data['approval_status'] = "Laporan Pindah Alamat <b>TELAH DISETUJUI</b> Atasan pada ".datetime_indo($lap['approval_date']);
				$data['approval_warna'] ='success';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan Pindah Alamat <b>TIDAK DISETUJI</b> Atasan pada ".datetime_indo($lap['approval_date']); 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='3') {
				$data['sdm_status'] = "Laporan Pindah Alamat <b>TELAH DIVERIFIKASI</b> SDM";
				$data['sdm_warna']= 'success';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan Pindah Alamat <b>TIDAK DISETUJUI</b> SDM";
				$data['sdm_warna'] = 'danger';
			}else if($lap['approval_status']=='5') {
				$data['sdm_status'] = "Laporan Pindah Alamat <b>TELAH DISUBMIT</b> SDM ke ERP";
				$data['sdm_warna'] ='info';
			}
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$this->load->view($this->folder_view."detail_pindah_alamat",$data);
		}

        public function view_approve() {
			$id = $this->input->post('id_');
			$tabel = 'ess_laporan_pindah_alamat';

			$lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
			$data['detail'] = $lap;

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5') {
				$data['approval_status'] = "Laporan Pindah Alamat <b>TELAH DISETUJUI</b> Atasan pada ".datetime_indo($lap['approval_date']);
				$data['approval_warna'] ='success';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan Pindah Alamat <b>TIDAK DISETUJI</b> Atasan pada ".datetime_indo($lap['approval_date']); 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='3') {
				$data['sdm_status'] = "Laporan Pindah Alamat <b>TELAH DIVERIFIKASI</b> SDM";
				$data['sdm_warna']= 'success';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan Pindah Alamat <b>TIDAK DISETUJUI</b> SDM";
				$data['sdm_warna'] = 'danger';
			}else if($lap['approval_status']=='5') {
				$data['sdm_status'] = "Laporan Pindah Alamat <b>TELAH DISUBMIT</b> SDM ke ERP";
				$data['sdm_warna'] ='info';
			}
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$this->load->view($this->folder_view."persetujuan/approve_pindah_alamat",$data);
		}
		
		public function save_approve() {
			$this->load->helper('form');
			$this->load->library('form_validation');

			$simpan = array();
			$this->form_validation->set_rules('id_', 'Laporan Pindah Alamat', 'required');
			$this->form_validation->set_rules('status_approval', 'Persetujuan', 'required');
			if ($this->input->post('status_approval',true)=='2')
				$this->form_validation->set_rules('alasan', 'Alasan', 'required');

			if ($this->form_validation->run() == TRUE) {
				$id_ = $this->input->post('id_', true);

				$pdd = $this->db->where('id', $id_)->get('ess_laporan_pindah_alamat');
				if ($pdd->num_rows() == 1) {
					$tanggal = date('Y-m-d H:i:s');
					$status = $this->input->post('status_approval',true);

					$set['approval_date'] = $tanggal;
					$set['approval_status'] = $status;
					if($status=='2')
						$set['approval_alasan'] = $this->input->post('alasan');
					else
						$set['approval_alasan'] = null;

					$this->db->where('id', $id_)->set($set)->update('ess_laporan_pindah_alamat');

					if ($this->db->affected_rows() > 0) {
						$this->session->set_flashdata('success', 'Berhasil Memberikan Approval');
					}
					else {
						$this->session->set_flashdata('warning', 'Gagal Memberikan Approval! Cek Koneksi Anda.');
					}
				} else {
					$this->session->set_flashdata('warning', 'Data laporan Pindah Alamat tidak valid!');
				}
			} else {
				$this->session->set_flashdata('warning', 'Persetujuan laporan Pindah Alamat belum lengkap!');
			}
			redirect(site_url($this->folder_controller.'persetujuan/pindah_alamat'));
		}
	}