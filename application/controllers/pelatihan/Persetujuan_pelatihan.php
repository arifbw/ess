<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Persetujuan_pelatihan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'pelatihan/';
			$this->folder_model = 'pelatihan/';
			$this->folder_controller = 'pelatihan/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
						
			$this->load->model($this->folder_model."m_persetujuan_pelatihan");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Persetujuan Pelatihan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
		}
		
		public function index()
		{				
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."persetujuan_pelatihan";
			
			$array_tahun_bulan = array();
			$query = $this->db->select("DATE_FORMAT(tanggal_pelatihan,'%Y-%m') as tahun_bulan")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_diklat_kebutuhan_pelatihan');
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['tahun_bulan'],-2);
				$tahun = substr($data['tahun_bulan'],0,4);
				
				$bulan_tahun = $bulan."-".$tahun;				
				
				$array_tahun_bulan[] = $bulan_tahun; 
			}
            
            $this->data['array_tahun_bulan'] 	= $array_tahun_bulan;
			
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_ess_pelatihan($bulan_tahun=null,$filter='all')
		{
			if(@$bulan_tahun!=0){
                $month = $bulan_tahun;
            } else{
                $month = 0;
            }
					
			
			$this->load->model($this->folder_model."M_tabel_persetujuan");
			
			$list 	= $this->M_tabel_persetujuan->get_datatables($month,$filter);
						
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = $tampil->pelatihan;
				// $row[] = $tampil->tanggal_pelatihan;

				
				$id				= trim($tampil->id);
				$np_karyawan	= trim($tampil->np_karyawan);
				$nama			= trim($tampil->nama);
				$approval_1		= trim($tampil->approval_1);
				$approval_2		= trim($tampil->approval_2);	
				$status_1		= trim($tampil->status_1);
				$status_2		= trim($tampil->status_2);
				$approval_1_date= trim($tampil->approval_1_date);
				$approval_2_date= trim($tampil->approval_2_date);
				// $approval_sdm	= trim($tampil->approval_sdm);				
				$created_at		= trim($tampil->created_at);
				$created_by		= trim(nama_karyawan_by_np($tampil->created_by));
					
				if($status_1=='1')
				{						
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "pelatihan Telah Disetujui pada $approval_1_date."; 
				}else
				if($status_1=='2')
				{					
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "pelatihan TIDAK disetujui pada $approval_1_date."; 
				}else
				if($status_1=='3')
				{					
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Permohonan pelatihan Dibatalkan oleh pemohon pada $approval_1_date."; 
				}else
				if($status_1==''||$status_1=='0')
				{				
					$status_1 = '0';
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "pelatihan BELUM disetujui."; 
				}
				
				if($status_2=='1')
				{						
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "pelatihan Telah Disetujui pada $approval_2_date."; 
				}else
				if($status_2=='2')
				{					
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "pelatihan TIDAK disetujui pada $approval_2_date."; 
				}else
				if($status_2=='3')
				{					
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Permohonan pelatihan Dibatalkan oleh pemohon pada $approval_2_date."; 
				}else
				if($status_2==''||$status_2=='0')
				{
					$status_2 = '0';
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "pelatihan BELUM disetujui."; 
				}
				
			
			
				
				$btn_warna		='btn-default';
				$btn_text		='menunggu persetujuan';
				$btn_disabled 	='';
				
				if(($status_1=='' || $status_1=='0' || $status_1 == null) && ($status_2!='2' || $status_2!='1')) //menunggu atasan 1
				{
					$btn_warna		='btn-warning';
					$btn_text		='Menunggu Atasan 1';
					$btn_disabled 	='';
				}
				if(($status_1=='1') && ($status_2!='2' || $status_2!='1')) //disetujui atasan 1
				{
					if($tampil->approval_2==null || $tampil->approval_2=='') //jika tidak ada atasan 2
					{
						$btn_warna		='btn-success';
						$btn_text		='Disetujui atasan 1';
						$btn_disabled 	='';
					}else //jika ada atasan 2
					{
						$btn_warna		='btn-warning';
						$btn_text		='Disetujui Atasan 1, Menunggu Atasan 2';
						$btn_disabled 	='';
					}
				}
				if(($status_1=='2') && ($status_2!='2' || $status_2!='1')) //ditolak atasan 1
				{
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Atasan 1';
					$btn_disabled 	='disabled';
				}
				if($status_2=='1') //disetujui atasan  2
				{
					$btn_warna		='btn-success';
					$btn_text		='Disetujui Atasan';
					$btn_disabled 	='disabled';
					
					if($status_1=='0') //jika paralel atasan 2 belum approve
					{
						$btn_warna		='btn-warning';
						$btn_text		='Disetujui Atasan 2, Menunggu Atasan 1';
						$btn_disabled 	='';
					}
				}
				if($status_2=='2') //ditolak atasan 2
				{
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Atasan 2';
					$btn_disabled 	='disabled';
				}
					
				if($status_1=='3' || $status_2=='3') //dibatalkan
				{
					$btn_warna		='btn-danger';
					$btn_text		='dibatalkan';
					$btn_disabled 	='disabled';
				}
				
			
				if($approval_sdm==1)
				{
					$btn_warna		='btn-success';
					$btn_text		='Disetujui Oleh SDM';
					$btn_disabled 	='disabled';
				}else
				if($approval_sdm==2)
				{
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Oleh SDM';
					$btn_disabled 	='disabled';
				}
					
				
				$row[] = "<button class='btn ".$btn_warna." btn-xs status_button' data-toggle='modal' data-target='#modal_status'
					data-np-karyawan='$np_karyawan'
					data-nama='$nama'
					data-pelatihan='$pelatihan'
					data-created-at='$created_at'
					data-created-by='$created_by'
					data-approval-1-nama='$approval_1_nama'
					data-approval-2-nama='$approval_2_nama'
					data-approval-1-status='$approval_1_status'
					data-approval-2-status='$approval_2_status'						
				>$btn_text</button>";				
				
				
				//cutoff ERP
					// $sudah_cutoff = sudah_cutoff($tampil->start_date);
					
					// if($sudah_cutoff) //jika sudah lewat masa cutoff
					// {
					// 	$row[] = "<button class='btn btn-primary btn-xs persetujuan_button'   data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
					// }else
					// {			
						// if(!$approval_1)
						// {
						// 	$approval_1="xxxx";
						// }
						
						// if(!$approval_2)
						// {
						// 	$approval_2="xxxx";
						// }
						
						
						// $row[] = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal'  data-target='#modal_persetujuan'
						// data-id='$id'
						// data-np-karyawan='$np_karyawan'
						// data-nama='$nama'
						// data-created-at='$created_at'
						// data-created-by='$created_by'
						// data-approval-1-nama='$approval_1_nama'
						// data-approval-2-nama='$approval_2_nama'
						// data-approval-1-status='$approval_1_status'
						// data-approval-2-status='$approval_2_status'	
						// data-approval-1='$approval_1'
						// data-approval-2='$approval_2'
						// data-status-1='$status_1'
						// data-status-2='$status_2'	
						// $btn_disabled>Persetujuan</button>";					
					// }
				
				if(!$approval_1)
				{
					$approval_1="xxxx";
				}
				
				if(!$approval_2)
				{
					$approval_2="xxxx";
				}
				
				
				$row[] = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal'  data-target='#modal_persetujuan'
				data-id='$id'
				data-np-karyawan='$np_karyawan'
				data-nama='$nama'
				data-created-at='$created_at'
				data-created-by='$created_by'
				data-approval-1-nama='$approval_1_nama'
				data-approval-2-nama='$approval_2_nama'
				data-approval-1-status='$approval_1_status'
				data-approval-2-status='$approval_2_status'	
				data-approval-1='$approval_1'
				data-approval-2='$approval_2'
				data-status-1='$status_1'
				data-status-2='$status_2'	
				$btn_disabled>Persetujuan</button>";
				
				$data[] = $row;
				
				
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_persetujuan->count_all($month,$filter),
							"recordsFiltered" => $this->M_tabel_persetujuan->count_filtered($month,$filter),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}

		//tabel riwayat pelatihan
		public function tabel_ess_riwayat_pelatihan($npk='', $filter='all')
		{
			// if (@$bulan_tahun != 0) {
			// 	$month = $bulan_tahun;
			// } else {
			// 	$month = 0;
			// }

			// Menambahkan parameter np_karyawan untuk filter
			$this->load->model($this->folder_model . "M_tabel_riwayat_pelatihan");

			// Mengambil data berdasarkan bulan, filter, dan np_karyawan
			$list = $this->M_tabel_riwayat_pelatihan->get_datatables($npk, $filter);

			// log_message('ERROR','bawa data testing '.$np_karyawan);

			$data = array();
			$no = 0;

			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = $tampil->perihal;
				$row[] = $tampil->tgl_berangkat;

				$data[] = $row;
			}
			
			$records_total = count($data);
			$data = array_slice($data, $_POST['start'], $_POST['length']);

			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $records_total,
				"recordsFiltered" => $this->M_tabel_riwayat_pelatihan->count_filtered($month, $filter, $np_karyawan),
				"data" => $data,
			);
			// Output in JSON format
			echo json_encode($output);
		}

		public function approve_all() {
			// $bulan = $this->input->post('bulan_tahun');
				// $kry = erp_master_data_by_np($this->session->userdata('no_pokok'), $pengajuan['tgl_dws']);
				// $kry = erp_master_data_by_np($this->session->userdata('no_pokok'), $bulan.'-01');
				// $data['approval_pimpinan_status'] = '1';
				// $data['approval_pimpinan_np'] = $this->session->userdata('no_pokok');
				// $data['approval_pimpinan_nama'] = $kry['nama'];
				// $data['approval_pimpinan_nama_jabatan'] = $kry['nama_jabatan'];
				// $data['approval_pimpinan_nama_unit'] = $kry['nama_unit'];
				// $data['approval_pimpinan_kode_unit'] = $kry['kode_unit'];
				// $data['approval_pimpinan_date'] = date('Y-m-d H:i:s');
				// $approve = $this->m_persetujuan_lembur->approve_all($data, $bulan);
				
				// if ($approve != false) {
				// 	foreach ($approve->result_array() as $val) {
				// 		$get_lembur['no_pokok'] = $val['no_pokok'];
				// 		$get_lembur['tgl_dws'] = $val['tgl_dws'];
				// 		$get_lembur['id'] = $val['id'];
				// 		$this->m_pengajuan_lembur->set_cico($get_lembur);
				// 	}
					// $this->session->set_flashdata('success', 'Berhasil Melakukan Persetujuan Lembur Pada Bulan '.id_to_bulan(substr($bulan,-2)).' '.substr($bulan,0,4));
				// }
				// else {
				// 	$this->session->set_flashdata('failed', 'Gagal Melakukan Persetujuan Lembur Pada Bulan '.id_to_bulan(substr($bulan,-2)).' '.substr($bulan,0,4));
			// }
			$this->load->model($this->folder_model."M_tabel_persetujuan");
			$data=[];
			$data['approval_pimpinan_np'] = $this->session->userdata('no_pokok');
			$data['approval_pimpinan_status'] = '1';
			// Pastikan model di-load dengan benar sebelum digunakan
			if ($this->M_tabel_persetujuan) {
				$approve = $this->M_tabel_persetujuan->approve_all($data);
			} else {
				log_message('ERROR', 'Model m_tabel_persetujuan tidak ditemukan');
			}
			// log_message('ERROR','NP atasan '. $data['approval_pimpinan_status']);
			redirect(site_url($this->folder_controller.'persetujuan_pelatihan'));
		}
		
		public function action_persetujuan_pelatihan()
		{			
			$submit = $this->input->post('submit');
					
			if($submit)
			{			
				$status_1	= $this->input->post('persetujuan_status_1');				
				$status_2	= $this->input->post('persetujuan_status_2');
				$id			= $this->input->post('persetujuan_id');

				if ($status_1 == '2')
					$alasan_1	= $this->input->post('persetujuan_alasan_1');
				else 
					$alasan_1	= null;	

				if ($status_2 == '2')
					$alasan_2	= $this->input->post('persetujuan_alasan_2');
				else 
					$alasan_2	= null;		
					
				//===== Log Start =====
				$arr_data_lama = $this->m_persetujuan_pelatihan->select_pelatihan_by_id($id);
				$log_data_lama = "";				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				//===== Log end =====
				
				//insert
				$data_persetujuan['status_1']	= $status_1;
				$data_persetujuan['approval_alasan_1']	= $alasan_1;
				$data_persetujuan['status_2']	= $status_2;
				$data_persetujuan['approval_alasan_2']	= $alasan_2;
				$data_persetujuan['id']			= $id;
								
				$persetujuan_pelatihan = $this->m_persetujuan_pelatihan->persetujuan_pelatihan($data_persetujuan);

				
				if($persetujuan_pelatihan!="0")
				{
					$this->session->set_flashdata('success',"Aksi Persetujuan/tolak pelatihan berhasil.");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_persetujuan_pelatihan->select_pelatihan_by_id($id);
					$log_data_baru = "";					
					foreach($arr_data_baru as $key => $value){
						if(strcmp($key,"id")!=0){
							if(!empty($log_data_baru)){
								$log_data_baru .= "<br>";
							}
							$log_data_baru .= "$key = $value";
						}
					}									
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "Setuju ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
				}else
				{
					$this->session->set_flashdata('warning',"Aksi Persetujuan/tolak Gagal");
				}
				
				redirect(base_url($this->folder_controller.'persetujuan_pelatihan'));			
				
			}else
			{					
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'persetujuan_pelatihan'));	
			}	
		}
	

	}
	
	/* End of file persetujuan_pelatihan.php */
	/* Location: ./application/controllers/pelatihan/persetujuan_pelatihan.php */

