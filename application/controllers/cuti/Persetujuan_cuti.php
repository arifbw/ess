<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Persetujuan_cuti extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'cuti/';
			$this->folder_model = 'cuti/';
			$this->folder_controller = 'cuti/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
						
			$this->load->model($this->folder_model."m_persetujuan_cuti");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Persetujuan Cuti";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
		}
		
		public function index()
		{				
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."persetujuan_cuti";
			
			$array_tahun_bulan = array();
			$query = $this->db->select("DATE_FORMAT(start_date,'%Y-%m') as tahun_bulan")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_cuti');
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
		
		public function tabel_ess_cuti($bulan_tahun=null,$filter='all')
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

				if($tampil->is_cuti_bersama=='1') {
					if(in_array($tampil->absence_type, ['2001|1000','2001|1010'])) $row[] = "Cuti Bersama (Memotong {$tampil->uraian})";
					else if($tampil->absence_type == '2001|2080') $row[] = "Cuti Bersama (Mengambil {$tampil->uraian})";
					else $row[] = $tampil->uraian;
				} else $row[] = $tampil->uraian;
				
				$row[] = tanggal_indonesia($tampil->start_date);	
				$row[] = tanggal_indonesia($tampil->end_date);
				
				if($tampil->jumlah_bulan)
				{
					$row[] = $tampil->jumlah_bulan." bulan ".$tampil->jumlah_hari." hari";		
				}else
				{
					$row[] = $tampil->jumlah_hari." hari ";
				}		
				
				$row[] = $tampil->alasan;
				
				$id				= trim($tampil->id);
				$np_karyawan	= trim($tampil->np_karyawan);
				$nama			= trim($tampil->nama);
				$approval_1		= trim($tampil->approval_1);
				$approval_2		= trim($tampil->approval_2);	
				$status_1		= trim($tampil->status_1);
				$status_2		= trim($tampil->status_2);
				$approval_1_date= trim($tampil->approval_1_date);
				$approval_2_date= trim($tampil->approval_2_date);
				$approval_sdm	= trim($tampil->approval_sdm);				
				$created_at		= trim($tampil->created_at);
				$created_by		= trim(nama_karyawan_by_np($tampil->created_by));
				
				
				if($status_1=='1')
				{						
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Cuti Telah Disetujui pada $approval_1_date."; 
				}else
				if($status_1=='2')
				{					
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Cuti TIDAK disetujui pada $approval_1_date."; 
				}else
				if($status_1=='3')
				{					
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada $approval_1_date."; 
				}else
				if($status_1==''||$status_1=='0')
				{				
					$status_1 = '0';
					$approval_1_nama 	= $approval_1." | ".nama_karyawan_by_np($approval_1);
					$approval_1_status 	= "Cuti BELUM disetujui."; 
				}
				
				if($status_2=='1')
				{						
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Cuti Telah Disetujui pada $approval_2_date."; 
				}else
				if($status_2=='2')
				{					
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Cuti TIDAK disetujui pada $approval_2_date."; 
				}else
				if($status_2=='3')
				{					
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Permohonan Cuti Dibatalkan oleh pemohon pada $approval_2_date."; 
				}else
				if($status_2==''||$status_2=='0')
				{
					$status_2 = '0';
					$approval_2_nama 	= $approval_2." | ".nama_karyawan_by_np($approval_2);
					$approval_2_status 	= "Cuti BELUM disetujui."; 
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

					$this->db->where('id', $id);
					$this->db->where_in('absence_type', '2001|2080'); // Pastikan format 'absence_type' sesuai database
					$this->db->where_in('batal_hutang_cuti', '0'); // Pastikan format 'absence_type' sesuai database
					$this->db->where_in('status_1', [0,1]); // Pastikan format 'absence_type' sesuai database
					$query = $this->db->get('ess_cuti');

					if ($query->num_rows() > 0) {
						$this->db->set('batal_hutang_cuti', 1, FALSE); // Mengurangi nilai 'hutang'
						$this->db->where('id', $id);
						$this->db->update('ess_cuti');

						// Jika absence_type adalah 2001|2080, kurangi hutang
						$this->db->set('hutang', 'hutang - 1', FALSE); // Mengurangi nilai 'hutang'
						$this->db->where('no_pokok', $np_karyawan);
						$this->db->update('cuti_hutang');
					}
				}
				if($status_2=='1') //disetujui atasan  2
				{
					$btn_warna		='btn-success';
					$btn_text		='Disetujui Atasan 2';
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

					$this->db->where('id', $id);
					$this->db->where_in('absence_type', '2001|2080'); // Pastikan format 'absence_type' sesuai database
					$this->db->where_in('batal_hutang_cuti', '0'); // Pastikan format 'absence_type' sesuai database
					$query = $this->db->get('ess_cuti');

					if ($query->num_rows() > 0) {
						$this->db->set('batal_hutang_cuti', 1, FALSE); // Mengurangi nilai 'hutang'
						$this->db->where('id', $id);
						$this->db->update('ess_cuti');

						// Jika absence_type adalah 2001|2080, kurangi hutang
						$this->db->set('hutang', 'hutang - 1', FALSE); // Mengurangi nilai 'hutang'
						$this->db->where('no_pokok', $np_karyawan);
						$this->db->update('cuti_hutang');
					}
				}
					
				if($status_1=='3' || $status_2=='3') //dibatalkan
				{
					$btn_warna		='btn-danger';
					$btn_text		='dibatalkan';
					$btn_disabled 	='disabled';

					$this->db->where('id', $id);
					$this->db->where_in('absence_type', '2001|2080'); // Pastikan format 'absence_type' sesuai database
					$this->db->where_in('batal_hutang_cuti', '0'); // Pastikan format 'absence_type' sesuai database
					$this->db->where_in('status_1', [0,1]);
					$query = $this->db->get('ess_cuti');

					if ($query->num_rows() > 0) {
						$this->db->set('batal_hutang_cuti', 1, FALSE); // Mengurangi nilai 'hutang'
						$this->db->where('id', $id);
						$this->db->update('ess_cuti');

						// Jika absence_type adalah 2001|2080, kurangi hutang
						$this->db->set('hutang', 'hutang - 1', FALSE); // Mengurangi nilai 'hutang'
						$this->db->where('no_pokok', $np_karyawan);
						$this->db->update('cuti_hutang');
					}
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
					data-created-at='$created_at'
					data-created-by='$created_by'
					data-approval-1-nama='$approval_1_nama'
					data-approval-2-nama='$approval_2_nama'
					data-approval-1-status='$approval_1_status'
					data-approval-2-status='$approval_2_status'						
				>$btn_text</button>";				
				
				
			
			
				/*if($approval_sdm==1)
				{
					//$btn_text		='Disetujui Oleh SDM';
					$row[] = "<button class='btn btn-success btn-xs'	
					><i class='fa fa-check'></i></button>";	
					
					$btn_disabled 	='disabled';
				}else
				if($approval_sdm==2)
				{
					$row[] = "<button class='btn btn-danger btn-xs'	
					><i class='fa fa-times  '></i></button>";

					$btn_disabled 	='disabled';					
				}else
				{
					$row[] = "<button class='btn btn-xs'	
					><i class='fa fa-minus'></i></button>";	
				}*/
			
			
				
				
				//cutoff ERP
				$sudah_cutoff = sudah_cutoff($tampil->start_date);
				
				if($sudah_cutoff) //jika sudah lewat masa cutoff
				{
					$row[] = "<button class='btn btn-primary btn-xs persetujuan_button'   data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
				}else
				{			
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
				}
				
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
		
		
		public function action_persetujuan_cuti()
		{			
			$submit = $this->input->post('submit');
			$is_ajax = $this->input->is_ajax_request();
					
			if($submit || $is_ajax)
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
				$arr_data_lama = $this->m_persetujuan_cuti->select_cuti_by_id($id);
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
								
				$persetujuan_cuti = $this->m_persetujuan_cuti->persetujuan_cuti($data_persetujuan);
				
				if($persetujuan_cuti!="0")
				{	
					$this->session->set_flashdata('success',"Aksi Persetujuan/tolak Cuti berhasil.");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_persetujuan_cuti->select_cuti_by_id($id);
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
				
				redirect(base_url($this->folder_controller.'persetujuan_cuti'));			
				
			}else
			{					
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'persetujuan_cuti'));	
			}	
		}
	

	}
	
	/* End of file persetujuan_cuti.php */
	/* Location: ./application/controllers/cuti/persetujuan_cuti.php */