<?php defined('BASEPATH') OR exit('No direct script access allowed');

	class Listrik extends CI_Controller {

		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'master_data/plafon/';
			$this->folder_model = 'master_data/plafon/';
			$this->folder_controller = 'master_data/plafon/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Plafon Listrik";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
            $this->nama_db = $this->db->database;
			izin($this->akses["akses"]);
		}

        public function index() {
            $this->load->model($this->folder_model."M_plafon");

            $array_daftar_karyawan	= $this->M_plafon->select_daftar_karyawan();
			
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."listrik";
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;

			$this->load->view('template',$this->data);
		}

        /*public function tabel_listrik() {
            $this->load->model($this->folder_model."M_tabel_listrik");
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
            
			$list 	= $this->M_tabel_listrik->get_datatables($var);
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
					$approval_status 	= "Laporan Anak Tertanggung TELAH DISETUJUI Atasan pada $approval_date.";

					$btn_warna		='btn-success';
					$btn_text		='Disetujui Atasan';
				}else if($get_status=='2') {
					$approval_status 	= "Laporan Anak Tertanggung TIDAK DISETUJI Atasan pada $approval_date.<br>Alasan : ".$approval_alasan; 

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
					$sdm_status 	= "Laporan Anak Tertanggung TELAH DIVERIFIKASI SDM";

					$btn_warna		='btn-success';
					$btn_text		='Disetujui SDM';
				}else if($get_status=='4') {
					$sdm_status 	= "Laporan Anak Tertanggung TIDAK DISETUJUI SDM";

					$btn_warna		='btn-danger';
					$btn_text		='Ditolak SDM';
				}else if($get_status=='5') {
					$sdm_status 	= "Laporan Anak Tertanggung Telah Disubmit SDM ke ERP";

					$btn_warna		='btn-primary';
					$btn_text		='SUBMIT ERP';
				}

				$row[] = "<button class='btn $btn_warna btn-xs detail_button' data-toggle='modal' data-target='#modal_detail' data-id=".$tampil->id." data-np=".$tampil->np_karyawan.">$btn_text</button>";

				if ($tampil->approval_status==null || $tampil->approval_status=='0') {
					$np_hapus = $tampil->np_karyawan;

                    if($this->akses["tambah"])
                        $aksi = '<button class="btn btn-warning btn-xs" onclick="edit(\''.$tampil->id.'\')">Edit</button> <button class="btn btn-danger btn-xs" onclick="hapus(\''.$tampil->id.'\',\''.$np_hapus.'\')">Hapus</button>';
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
                "recordsTotal" => $this->M_tabel_listrik->count_all($var),
                "recordsFiltered" => $this->M_tabel_listrik->count_filtered($var),
                "data" => $data,
			);
        	//output to json format
			echo json_encode($output);
        }

        function action_insert_listrik() {
            $this->load->helper('form');
			$this->load->library('form_validation');
            
            if($this->akses["tambah"]) {
				$fail = array();

                $this->form_validation->set_rules('np_karyawan', 'Karyawan', 'required');
                $this->form_validation->set_rules('alamat', 'Alamat', 'required');
                $this->form_validation->set_rules('no_kontrol', 'No Kontrol', 'required');
                $this->form_validation->set_rules('plafon', 'Plafon', 'required');

				if ($this->form_validation->run() == FALSE) {
					$this->session->flashdata('warning', 'Data Belum Lengkap');
					redirect(base_url($this->folder_controller.'listrik'));
				}
                else {
                    $submit = $this->input->post('submit');
                    if($submit) {

                        $data_insert = [];
                        $np_karyawan		= $this->input->post('alamat', true);
						$approval_np		= $this->input->post('no_kontrol', true);
						$approval_nama		= $this->input->post('plafon', true);

						$start_date			= date('Y-m-d');
						$end_date			= date('Y-m-d');
                        $tahun_bulan     	= $start_date!=null ? str_replace('-','_',substr("$start_date", 0, 7)) : str_replace('-','_',substr("$end_date", 0, 7)) ;
                        
                        $data_insert = [
                            'np_karyawan' => $np_karyawan,
                            'nama_karyawan' => erp_master_data_by_np($np_karyawan, $start_date)['nama'],
                            'nama_jabatan' => erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'],
                            'alamat'=>$this->input->post('alamat', true),
                            'no_kontrol'=>$this->input->post('no_kontrol', true),
                            'plafon'=>$this->input->post('plafon', true),
                        ];
                        
                        if ($this->input->post('edit_id', true)!='') {
                            $data_insert['updated_at'] = date('Y-m-d H:i:s');
                            $data_insert['updated_by'] = $_SESSION['no_pokok'];
                            $this->db->set($data_insert)->where('id', $this->input->post('edit_id', true))->update('ess_laporan_listrik');
                        } else {
                            $data_insert['created_at'] = date('Y-m-d H:i:s');
                            $data_insert['created_by'] = $_SESSION['no_pokok'];
                            $this->db->set($data_insert)->insert('ess_laporan_listrik');
                        }
                        
                        if ($this->db->affected_rows() > 0)
                            $this->session->set_flashdata('success',"Berhasil Update Laporan Anak Tertanggung");
                        else
                            $this->session->set_flashdata('warning',"Gagal Update Laporan Anak Tertanggung");
                            redirect(base_url($this->folder_controller.'listrik'));
                    } else{
                        $this->session->set_flashdata('warning',"Terjadi Kesalahan Input Data");
						redirect(base_url($this->folder_controller.'listrik'));
                    }
                }
            } else{
                $this->session->set_flashdata('warning',"Anda Tidak Memiliki Hak Akses");
				redirect(base_url($this->folder_controller.'listrik'));	
            }
        }

        public function view_detail()
		{
			$id = $this->input->post('id_');
			$tabel = 'ess_laporan_listrik';

			$lap = $this->db->select("*")->where('id', $id)->get($tabel.' a')->row_array();
			$data['detail'] = $lap;

			//DETAIL
			if($lap['approval_status']=='1' || $lap['approval_status']=='3' || $lap['approval_status']=='4' || $lap['approval_status']=='5') {
				$data['approval_status'] = "Laporan Anak Tertanggung <b>TELAH DISETUJUI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>";
				$data['approval_warna'] ='success';
			}else if($lap['approval_status']=='2') {
				$data['approval_status'] = "Laporan Anak Tertanggung <b>TIDAK DISETUJI Atasan</b> pada <b>".datetime_indo($lap['approval_date'])."</b>"; 
				$data['approval_warna'] ='danger';
			} else if($lap['approval_status']=='0' || $lap['approval_status']==null) {
				$data['approval_status'] = "Menunggu Persetujuan Atasan"; 
				$data['approval_warna'] ='info';
			}

			if($lap['approval_status']=='3' || $lap['approval_status']=='5') {
				$data['sdm_status'] = "Laporan Anak Tertanggung <b>TELAH DIVERIFIKASI SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna']= 'success';
			}else if($lap['approval_status']=='4') {
				$data['sdm_status'] = "Laporan Anak Tertanggung <b>TIDAK DISETUJUI SDM</b> pada <b>".datetime_indo($lap['sdm_verif_date'])."</b>";
				$data['sdm_warna'] = 'danger';
			}

			if($lap['approval_status']=='5') {
				$data['submit_status'] = "Laporan pendidikan <b>TELAH DISUBMIT SDM</b> ke ERP pada <b>".datetime_indo($lap['sdm_submit_date'])."</b>";
			}
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));

			$this->load->view($this->folder_view."detail_listrik",$data);
		}*/

		public function tabel_listrik() {
			$this->load->model($this->folder_model."M_plafon_listrik");
			$list = $this->M_plafon_listrik->get_datatables();
			$data = array();
			$no = $_POST['start'];
	
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->M_plafon_listrik->count_all(),
				"recordsFiltered" => $this->M_plafon_listrik->count_filtered(),
				"data" => $list
			);
			echo json_encode($output);
		}

		function action_insert(){
			$data_insert = [];
			foreach($this->input->post() as $key=>$value){
				if(!in_array($key, ['id','plafon'])) $data_insert[$key] = $value;
			}

			if( $this->input->post('plafon')!='' )
				$data_insert['plafon'] = $this->input->post('plafon');
			else{
				$data_insert['plafon'] = null;
				$data_insert['ket'] = 'at cost';
			}
			// echo json_encode($data_insert); exit;

			# jika edit ada atribute 'id'
			if(@$this->input->post('id')){
				$data_insert['updated_at'] = date('Y-m-d H:i:s');
				$data_insert['updated_by'] = $_SESSION['no_pokok'];
				$this->db->where('id',$this->input->post('id'))->update('mst_plafon_listrik', $data_insert);
				$this->session->set_flashdata('success',"Master plafon listrik a.n <b>{$data_insert['nama_karyawan']}</b> telah diupdate");
			} else{ # ini untuk kondisi input => tidak ada atribute 'id'
				$cek = $this->db->where([
					'np_karyawan'=>$data_insert['np_karyawan'],
					'no_kontrol'=>$data_insert['no_kontrol'],
				])->get('mst_plafon_listrik');
				if($cek->num_rows()>0){
					$row = $cek->row();
					if($row->deleted_at==null)
						$this->session->set_flashdata('failed','Data sudah pernah diinput');
					else{
						$data_insert['deleted_at'] = null;
						$data_insert['deleted_by'] = null;
						$this->db->where('id',$row->id)->update('mst_plafon_listrik', $data_insert);
						$this->session->set_flashdata('success',"Master plafon listrik a.n <b>{$data_insert['nama_karyawan']}</b> berhasil ditambahkan");
					}
				} else{
					$data_insert['created_at'] = date('Y-m-d H:i:s');
					$data_insert['created_by'] = $_SESSION['no_pokok'];
					$this->db->insert('mst_plafon_listrik', $data_insert);
					$this->session->set_flashdata('success',"Master plafon listrik a.n <b>{$data_insert['nama_karyawan']}</b> berhasil ditambahkan");
				}
			}

			redirect('master_data/plafon/listrik');
		}

		function hapus(){
			$id = $this->input->post('id');
			$this->db->where('id',$id)->update('mst_plafon_listrik', [
				'deleted_at'=>date('Y-m-d H:i:s'),
				'deleted_by'=>$_SESSION['no_pokok']
			]);
			echo json_encode([
				'status'=>true,
				'message'=>'Data dihapus'
			]);
		}
    }