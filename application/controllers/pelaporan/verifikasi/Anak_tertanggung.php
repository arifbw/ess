<?php defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Anak_tertanggung extends CI_Controller {

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
			
			$this->data['judul'] = "Verifikasi Anak Tertanggung";
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
			$this->data['content'] 					= $this->folder_view."verifikasi/anak_tertanggung";
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;

			$this->load->view('template',$this->data);
		}	
		
		public function tabel_anak_tertanggung() {
            $this->load->model($this->folder_model."M_tabel_anak_tertanggung");
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
            
			$list 	= $this->M_tabel_anak_tertanggung->get_datatables(null, 'verifikasi');
			$data = array();
			$no = $_POST['start'];
            foreach ($list as $tampil) {
				$no++;
                $get_approval 		= trim($tampil->approval_np);
				$get_status			= trim($tampil->approval_status);
				$approval_alasan	= trim($tampil->approval_alasan);
				$keterangan			= trim($tampil->keterangan);
				$approval_date		= $tampil->approval_date;

				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan.' - '.$tampil->nama_karyawan.'<br><small>'.$tampil->nama_unit.'</small>';
				$row[] = $tampil->nama_anak.'<br><small>'.$tampil->tempat_lahir_anak.', '.$tampil->tanggal_lahir_anak.'</small>';
				$row[] = $tampil->status_pekerjaan;
				$row[] = $keterangan;

                //DETAIL
				$approval_np 	= $get_approval." | ".nama_karyawan_by_np($get_approval);
				if($get_status=='1') {
					// $approval_status 	= "Laporan Anak Tertanggung TELAH DISETUJUI Atasan pada $approval_date.";

					$btn_warna		='btn-success';
					$btn_text		='Disetujui Atasan';
				}else if($get_status=='2') {
					// $approval_status 	= "Laporan Anak Tertanggung TIDAK DISETUJI Atasan pada $approval_date.<br>Alasan : ".$approval_alasan; 

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
					$sdm_status 	= "Laporan Anak Tertanggung DIVERIFIKASI KAUN SDM";

					$btn_warna		='btn-success';
					$btn_text		='Verifikasi KAUN SDM';
				}else if($get_status=='4') {
					$sdm_status 	= "Laporan Anak Tertanggung TIDAK DISETUJUI SDM";

					$btn_warna		='btn-danger';
					$btn_text		='Ditolak KAUN SDM';
				}else if($get_status=='5') {
					$sdm_status 	= "Laporan Anak Tertanggung DISUBMIT SDM ke ERP";

					$btn_warna		='btn-primary';
					$btn_text		='SUBMIT ERP';
				}else if($get_status=='6') {
					$sdm_status 	= "Laporan Anak Tertanggung DITOLAK Admin SDM";

					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Admin SDM';
				}

				$row[] = "<button class='btn $btn_warna btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id." data-np=".$tampil->np_karyawan.">$btn_text</button>";

				if ($tampil->approval_status=='1') {
					$np_hapus = $tampil->np_karyawan;

                    if($this->akses["persetujuan"])
					    $aksi = "<button class='btn btn-warning btn-xs persetujuan_button' data-toggle='modal' data-target='#modal_persetujuan' data-id=".$tampil->id.">Verifikasi</button>";
                    else
                        $aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id.">Detail</button>";
				} else if ($tampil->approval_status=='3') {
                    if($this->akses["submit"])
					    $aksi = "<button class='btn btn-warning btn-xs persetujuan_button' data-toggle='modal' data-target='#modal_persetujuan' data-id=".$tampil->id.">Submit ERP</button>";
                    else
                        $aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id.">Detail</button>";
				} else {
					$aksi = "<button class='btn btn-primary btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id.">Detail</button>";
				}
				$aksi .= '<a target="_blank" href="'.base_url('pelaporan/verifikasi/anak_tertanggung/export_pdf/').$tampil->id.'" class="btn btn-success btn-xs"><i class="fa fa-print"></i> Cetak PDF</a>';
                
				$row[] = $aksi;
				
				$data[] = $row;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_anak_tertanggung->count_all(null, 'verifikasi'),
					"recordsFiltered" => $this->M_tabel_anak_tertanggung->count_filtered(null, 'verifikasi'),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		} 

		public function view_detail()
		{			
			$id = $this->input->post('id_');
			$tabel = 'ess_laporan_anak_tertanggung';

			$lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
			$data['detail'] = $lap;

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['approval_status'] = "Laporan anak tertanggung <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>";
				$data['approval_warna'] ='success';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan anak tertanggung <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>"; 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='3' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['sdm_status'] = "Laporan anak tertanggung <b>DIVERIFIKASI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna']= 'success';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan anak tertanggung <b>TIDAK DISETUJUI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna'] = 'danger';
			}

			if($lap['approval_status']=='5') {
				$data['submit_status'] = "Laporan anak tertanggung <b>DISUBMIT SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'info';
			}
			else if($lap['approval_status']=='6') {
				$data['submit_status'] = "Laporan anak tertanggung <b>DITOLAK ADMIN SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'danger';
			}
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$this->load->view($this->folder_view."detail_anak_tertanggung",$data);
		}

        public function view_approve()
		{			
			$id = $this->input->post('id_');
			$tabel = 'ess_laporan_anak_tertanggung';

			$lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
			$data['detail'] = $lap;

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['approval_status'] = "Laporan anak tertanggung <b>TELAH DISETUJUI</b> Atasan pada ".datetime_indo($lap['approval_date']);
				$data['approval_warna'] ='success';
				$data['sdm_warna']= 'info';
				$data['submit_warna'] = 'info';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan anak tertanggung <b>TIDAK DISETUJI</b> Atasan pada ".datetime_indo($lap['approval_date']); 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['approval_status'] = "Laporan anak tertanggung <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>";
				$data['approval_warna'] ='success';
				$data['sdm_warna']= 'info';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan anak tertanggung <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>"; 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='3' || $lap['approval_status']=='5' || $lap['approval_status']=='6') {
				$data['sdm_status'] = "Laporan anak tertanggung <b>DIVERIFIKASI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna']= 'success';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan anak tertanggung <b>TIDAK DISETUJUI KAUN SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna'] = 'danger';
			}

			if($lap['approval_status']=='5') {
				$data['submit_status'] = "Laporan anak tertanggung <b>DISUBMIT SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'info';
			}else if($lap['approval_status']=='6') {
				$data['submit_status'] = "Laporan anak tertanggung <b>DITOLAK ADMIN SDM ke ERP</b> pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
				$data['submit_warna'] = 'danger';
			}
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$this->load->view($this->folder_view."verifikasi/approve_anak_tertanggung",$data);
		}
		
		public function save_approve() {
			$tabel = 'ess_laporan_anak_tertanggung';
        	$this->load->helper('form');
			$this->load->library('form_validation');

			$simpan = array();
		    if ($this->input->post('alasan_submit',true)=='') {
			    $this->form_validation->set_rules('id_', 'Laporan anak tertanggung', 'required');
			    $this->form_validation->set_rules('status_approval', 'Verifikasi', 'required');
			    if ($this->input->post('status_approval',true)=='4')
			    	$this->form_validation->set_rules('alasan', 'Alasan', 'required');

				if ($this->form_validation->run() == TRUE) {
					$id_ = $this->input->post('id_', true);

					$pdd = $this->db->where('id', $id_)->get($tabel);
					if ($pdd->num_rows() == 1) {
						$tanggal = date('Y-m-d H:i:s');
						$status = $this->input->post('status_approval',true);

						$set['sdm_verif_date'] = $tanggal;
						$set['sdm_verif_np'] = $_SESSION['no_pokok'];
						$set['approval_status'] = $status;
						if($status=='4')
							$set['sdm_alasan'] = $this->input->post('alasan');
						else
							$set['sdm_alasan'] = null;

						$this->db->where('id', $id_)->set($set)->update($tabel);

						if ($this->db->affected_rows() > 0) {
							$this->session->set_flashdata('success', 'Berhasil Memberikan Verifikasi Laporan');
						}
						else {
							$this->session->set_flashdata('warning', 'Gagal Memberikan Verifikasi Laporan! Cek Koneksi Anda.');
						}
					} else {
						$this->session->set_flashdata('warning', 'Data laporan anak tertanggung tidak valid!');
					}
				} else {
					$this->session->set_flashdata('warning', 'Data verifikasi laporan anak tertanggung belum lengkap!');
				}
			} else {
				$this->form_validation->set_rules('id_', 'Laporan anak tertanggung', 'required');
			    $this->form_validation->set_rules('alasan_submit', 'Alasan', 'required');

				if ($this->form_validation->run() == TRUE) {
					$id_ = $this->input->post('id_', true);

					$pdd = $this->db->where('id', $id_)->get($tabel);
					if ($pdd->num_rows() == 1) {
						$tanggal = date('Y-m-d H:i:s');

						$set['approval_status'] = '6';
						$set['sdm_submit_date'] = $tanggal;
						$set['sdm_submit_np'] = $_SESSION['no_pokok'];
						$set['sdm_submit_alasan'] = $this->input->post('alasan_submit');

						$this->db->where('id', $id_)->set($set)->update($tabel);

						if ($this->db->affected_rows() > 0) {
							$this->session->set_flashdata('success', 'Berhasil Memberikan Verifikasi Laporan');
						}
						else {
							$this->session->set_flashdata('warning', 'Gagal Memberikan Verifikasi Laporan! Cek Koneksi Anda.');
						}
					} else {
						$this->session->set_flashdata('warning', 'Data laporan anak tertanggung tidak valid!');
					}
				} else {
					$this->session->set_flashdata('warning', 'Data verifikasi laporan anak tertanggung belum lengkap!');
				}
			}
			redirect(site_url($this->folder_controller.'verifikasi/anak_tertanggung'));
		}
		
		public function save_erp() {
			$tabel = 'ess_laporan_anak_tertanggung';
        	$this->load->helper('form');
			$this->load->library('form_validation');

			$simpan = array();
		    $this->form_validation->set_rules('id_', 'Laporan anak tertanggung', 'required');

			if ($this->form_validation->run() == TRUE) {
				$id_ = $this->input->post('id_', true);

				$pdd = $this->db->where('id', $id_)->get($tabel);
				if ($pdd->num_rows() == 1) {
					$tanggal = date('Y-m-d H:i:s');
					$status = $this->input->post('status_approval',true);

					$set['sdm_submit_date'] = $tanggal;
					$set['sdm_submit_np'] = $_SESSION['no_pokok'];
					$set['approval_status'] = '5';

					$this->db->where('id', $id_)->set($set)->update($tabel);

					if ($this->db->affected_rows() > 0) {
						$return['status'] = true;
						$return['msg'] = 'Berhasil Mengubah Status Submit ERP';
					}
					else {
						$return['status'] = false;
						$return['msg'] = 'Gagal Memberikan Mengubah Status Submit ERP! Cek Koneksi Anda.';
					}
				} else {
					$return['status'] = false;
					$return['msg'] = 'Data laporan anak tertanggung tidak valid!';
				}
			} else {
				$return['status'] = false;
				$return['msg'] = 'Data verifikasi laporan anak tertanggung belum lengkap!';
			}

			echo json_encode($return);
		}

		function export_pdf($id){
			$mpdf = $this->pdf->load_custom();
			$lap = $this->db->select("*")->where('id', $id)->get('ess_laporan_anak_tertanggung')->row_array();
			$data = array(
				'data' => $lap,
				'title' => "Laporan Anak Tertanggung"
			);

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5') {
				$data['approval_status'] = "Laporan Anak Tertanggung <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>";
				$data['approval_warna'] ='green';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan Anak Tertanggung <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>"; 
				$data['approval_warna'] ='red';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='blue';
			}

			if($lap['approval_status']=='3' || $lap['approval_status']=='5') {
				$data['sdm_status'] = "Laporan Anak Tertanggung <b>TELAH DIVERIFIKASI SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna']= 'green';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan Anak Tertanggung <b>TIDAK DISETUJUI SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna'] = 'red';
			}

			if($lap['approval_status']=='5') {
				$data['submit_status'] = "Laporan Anak Tertanggung <b>TELAH DISUBMIT SDM</b> ke ERP pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
			}

			$html=$this->load->view($this->folder_view."verifikasi/cetak_anak_tertanggung", $data,true);
			$mpdf->WriteHTML($html);
			$mpdf->Output();
		}
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */