<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Persetujuan_kehadiran extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'kehadiran/';
			$this->folder_model = 'kehadiran/';
			$this->folder_controller = 'kehadiran/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
						
			$this->load->model($this->folder_model."m_persetujuan_kehadiran");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Persetujuan Kehadiran";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
		}
		
		public function index()
		{				
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."persetujuan_kehadiran";
			
			$this->load->model($this->folder_model."M_tabel_persetujuan_kehadiran");
			
			$var = 1;
			
			//jika Pengguna
			if (@$this->input->get('np'))
				$this->data['get_np'] = $this->input->get('np');
			else
				$this->data['get_np'] = 0;

			if($_SESSION["grup"]==5){
				$var = $_SESSION["no_pokok"];
			}
				
			$this->data['sudah_cutoff'] = sudah_cutoff(date("Y-m-d", strtotime('-1 months')));//var_dump($this->data['sudah_cutoff']);
			if(!$this->data['sudah_cutoff']){
				$tampil_tahun_bulan_lalu = date("Y_m", strtotime('-1 months'));
				
				//bulan lalu
				$this->data["nama_bulan_lalu"] = id_to_bulan(substr($tampil_tahun_bulan_lalu,-2))." ".substr($tampil_tahun_bulan_lalu,0,4);
				//banyak bulan lalu
				$this->data["banyak_bulan_lalu"] = $this->M_tabel_persetujuan_kehadiran->count_filtered($var,$tampil_tahun_bulan_lalu);
			}
			//var_dump($this->session->flashdata());
			$tampil_tahun_bulan = date("Y_m");//var_dump($tampil_tahun_bulan);
			//bulan ini
			$this->data["nama_bulan_ini"] = id_to_bulan(substr($tampil_tahun_bulan,-2))." ".substr($tampil_tahun_bulan,0,4);
			//banyak bulan ini
			$this->data["banyak_bulan_ini"] = $this->M_tabel_persetujuan_kehadiran->count_filtered($var,$tampil_tahun_bulan);
			
			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			
			$nama_db = $this->db->database;		
			$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$nama_db' AND table_name like '%ess_cico_%' GROUP BY table_name ORDER BY table_name DESC;");
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['table_name'],-2);
				$tahun = substr($data['table_name'],9,4);
				
				$bulan_tahun = $bulan."-".$tahun;				
				
				$array_tahun_bulan[] = $bulan_tahun; 
			}
            
            $this->data['array_tahun_bulan'] 	= $array_tahun_bulan;
			if(@$this->input->get('bulan')) $this->data['get_bulan'] = $this->input->get('bulan');
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_ess_kehadiran($bulan_tahun=null)
		{						
			if ($this->input->post('np')!="0")
				$_POST['search']['value'] = $this->input->post('np');

			if(@$bulan_tahun!=0){
                $month = $bulan_tahun;
            } else{
                $month = 0;
            }
			
			$tampil_tahun_bulan = str_replace("-","_",$month);
			
			$this->load->model($this->folder_model."M_tabel_persetujuan_kehadiran");
		
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				$var 	= $_SESSION["no_pokok"];
				
			}else
			{
				$var = 1;				
			}			
				
			$list = $this->M_tabel_persetujuan_kehadiran->get_datatables($var,$tampil_tahun_bulan);			
					
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = tanggal_indonesia($tampil->dws_tanggal);
				
				//check apakah sudah ada tapping fix yg diakui
				if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='')
				{
					$tapping_1	= $tampil->tapping_time_1;					
				}else
				{				
					$tapping_1	= $tampil->tapping_fix_1;				
				}
				
				if($tampil->tapping_fix_2==null || $tampil->tapping_fix_2=='')
				{
					$tapping_2	= $tampil->tapping_time_2;					
				}else
				{					
					$tapping_2	= $tampil->tapping_fix_2;				
				}
				
								
				$row[] = $tapping_1."<br>s.d.<br>".$tapping_2;
				
				
				
				if($tampil->wfh==1 && $tampil->is_dinas_luar == 0)
				{
					$tampil_wfh = "<mark>WFH</mark>"."<br><br>";
				}else
				{
					$tampil_wfh = '';
				}
				
				$row[] = $tampil_wfh.$tampil->tapping_fix_1_temp."<br>s.d.<br>".$tampil->tapping_fix_2_temp;
				
				
				$id					= trim($tampil->id);
				$np_karyawan		= trim($tampil->np_karyawan);
				$nama				= trim($tampil->nama);
				$approval_np		= trim($tampil->tapping_fix_approval_np);
				$approval_nama		= trim($tampil->tapping_fix_approval_nama);		
				$approval_status	= trim($tampil->tapping_fix_approval_status);	
				$approval_date		= trim($tampil->tapping_fix_approval_date);	
				$tapping_fix_1_temp	= trim($tampil->tapping_fix_1_temp);	
				$tapping_fix_2_temp	= trim($tampil->tapping_fix_2_temp);	
				
				
				
				
				if($approval_status=='1')
				{						
					$approval_1_nama 	= $approval_np." | ".$approval_nama;
					$approval_1_status 	= "kehadiran Telah Disetujui pada $approval_date."; 
				}else
				if($approval_status=='2')
				{					
					$approval_1_nama 	= $approval_np." | ".$approval_nama;
					$approval_1_status 	= "kehadiran TIDAK disetujui pada $approval_date."; 
				}else
				if($approval_status=='3')
				{					
					$approval_1_nama 	= $np_karyawan." | ".$tampil->nama;
					$approval_1_status 	= "Permohonan kehadiran Dibatalkan oleh pemohon pada $approval_date."; 
				}else
				if($approval_status==''||$approval_status=='0')
				{				
					$approval_status = '0';
					$approval_1_nama 	= $approval_np." | ".$approval_nama;
					$approval_1_status 	= "kehadiran BELUM disetujui."; 
				}
				
				$btn_warna		='btn-default';
				$btn_text		='menunggu persetujuan';
				$btn_disabled 	='';
				
				if(($approval_status=='' || $approval_status=='0' || $approval_status == null)) //menunggu atasan 1
				{
					$btn_warna		='btn-warning';
					$btn_text		='Menunggu Atasan';
					$btn_disabled 	='';
				}
				if(($approval_status=='1')) //disetujui atasan 
				{
					$btn_warna		='btn-success';
					$btn_text		='Disetujui atasan';
					$btn_disabled 	='';					
				}
				if(($approval_status=='2')) //ditolak atasan 
				{
					$btn_warna		='btn-danger';
					$btn_text		='Ditolak Atasan';
					$btn_disabled 	='disabled';
				}					
				if($approval_status=='3') //dibatalkan
				{
					$btn_warna		='btn-danger';
					$btn_text		='dibatalkan';
					$btn_disabled 	='disabled';
				}
				
								
				
				$row[] = "<button class='btn ".$btn_warna." btn-xs status_button' data-toggle='modal' data-target='#modal_status'
					data-np-karyawan='$np_karyawan'
					data-nama='$nama'				
					data-approval-nama='$approval_nama'				
					data-approval-status='$approval_1_status'									
				>$btn_text</button>";				
			
						
				
				
				//cutoff ERP
				$sudah_cutoff = sudah_cutoff($tampil->dws_tanggal);
				
				if($sudah_cutoff) //jika sudah lewat masa cutoff
				{
					$row[] = "<button class='btn btn-primary btn-xs persetujuan_button'   data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
				}else
				{	
/*			
					if(!$approval_1)
					{
						$approval_1="xxxx";
					}
					
					if(!$approval_2)
					{
						$approval_2="xxxx";
					}
	*/				
					$referensi_data_pamlek='';
					$ambil_data_pamlek = $this->m_persetujuan_kehadiran->referensi_pamlek_by_tanggal($tampil->dws_tanggal,$tampil->np_karyawan);
				
				
				
					foreach ($ambil_data_pamlek->result_array() as $pam_dat) 
					{						
						if($pam_dat['in_out']=='1')
						{
							$in_out="Masuk";
						}else
						{
							$in_out="Keluar";
						}
						
						if($pam_dat['nama']=='Izin Datang Terlambat')
						{
							$nama_tapping = 'Kehadiran';
						}else
						{
							$nama_tapping=$pam_dat['nama'];
						}
						
						$referensi_data_pamlek=$referensi_data_pamlek.$pam_dat['tapping_time'].' - '.$in_out.' - '.$nama_tapping."\n";
					}
					
					if($tampil->wfh=='1') 
					{
						$wfh = "WORK FROM HOME";	
					}else
					{
						$wfh = "-";	
					}
					
					$foto_1 = base_url()."file/kehadiran2/$tampil->wfh_foto_1";
					$foto_2 = base_url()."file/kehadiran2/$tampil->wfh_foto_2";
					
					//edit by wina deleted evidence 11-02-21, wfh mobile 
					if(strtolower($tampil->tapping_fix_approval_ket)!='wfh mobile' && $tampil->wfh=='1' && $tampil->dws_tanggal>'2020-03-18') //berlaku setelah tanggal 18
					{
						if($tampil->wfh_foto_1==null || $tampil->wfh_foto_1=='')
						{
							$row[] = "<button class='btn btn-warning btn-xs' data-toggle='modal'  					
							disabled>Belum Upload Evidence WFH<br>Berangkat</button>";
						}else
						if($tampil->wfh_foto_2==null || $tampil->wfh_foto_2=='')
						{
							$row[] = "<button class='btn btn-warning btn-xs' data-toggle='modal'  					
							disabled>Belum Upload Evidence WFH<br>Pulang</button>";
						}else
						{
							$row[] = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal'  data-target='#modal_persetujuan'
							data-id='$id'
							data-np-karyawan='$np_karyawan'
							data-nama='$nama'			
							data-approval-nama='$approval_nama'
							data-approval-status-id='$approval_status'	
							data-approval-status='$approval_1_status'	
							data-approval-ket='$tampil->tapping_fix_approval_ket'	
							data-approval-wfh='$wfh'
							data-approval-wfh-foto-1='$foto_1'
							data-approval-wfh-foto-2='$foto_2'
							data-approval-np='$approval_np'	
							data-approval-tahun-bulan='$tampil_tahun_bulan'	
							data-approval-tapping-fix-1-temp='$tapping_fix_1_temp'	
							data-approval-tapping-fix-2-temp='$tapping_fix_2_temp'	
							data-approval-referensi-data-pamlek='$referensi_data_pamlek'					
							data-dinas-luar='$tampil->is_dinas_luar'
							$btn_disabled>Persetujuan</button>";
							}
						
					}else
					{					
						$row[] = "<button class='btn btn-primary btn-xs persetujuan_button' data-toggle='modal'  data-target='#modal_persetujuan'
						data-id='$id'
						data-np-karyawan='$np_karyawan'
						data-nama='$nama'			
						data-approval-nama='$approval_nama'
						data-approval-status-id='$approval_status'	
						data-approval-status='$approval_1_status'	
						data-approval-ket='$tampil->tapping_fix_approval_ket'	
						data-approval-wfh='$wfh'
						data-approval-wfh-foto-1='$foto_1'
						data-approval-wfh-foto-2='$foto_2'
						data-approval-np='$approval_np'	
						data-approval-tahun-bulan='$tampil_tahun_bulan'	
						data-approval-tapping-fix-1-temp='$tapping_fix_1_temp'	
						data-approval-tapping-fix-2-temp='$tapping_fix_2_temp'	
						data-approval-referensi-data-pamlek='$referensi_data_pamlek'					
						data-dinas-luar='$tampil->is_dinas_luar'
						$btn_disabled>Persetujuan</button>";
					}
					
					

//$row[]= $ambil_data_pamlek;
				
				}
				
				
				
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_persetujuan_kehadiran->count_all($var,$tampil_tahun_bulan),
							"recordsFiltered" => $this->M_tabel_persetujuan_kehadiran->count_filtered($var,$tampil_tahun_bulan),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
		
		
		public function action_persetujuan_kehadiran()
		{			
			$submit = $this->input->post('submit');
					
			if($submit)
			{			
				$tahun_bulan	= $this->input->post('persetujuan_tahun_bulan');	
				$status_1		= $this->input->post('persetujuan_status_1');				
				$id				= $this->input->post('persetujuan_id');
				$tapping_fix_1_temp	= $this->input->post('persetujuan_tapping_fix_1_temp');
				$tapping_fix_2_temp	= $this->input->post('persetujuan_tapping_fix_2_temp');
				
				if ($status_1 == '2')
					$alasan_1	= $this->input->post('persetujuan_alasan_1');
				else 
					$alasan_1	= null;	

								
				//===== Log Start =====
				$arr_data_lama = $this->m_persetujuan_kehadiran->select_kehadiran_by_id($id,$tahun_bulan);
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
				$data_persetujuan['persetujuan_tahun_bulan']	= $tahun_bulan;	
				$data_persetujuan['tapping_fix_1_temp']			= $tapping_fix_1_temp;				
				$data_persetujuan['tapping_fix_1']				= $arr_data_lama['tapping_fix_1'];
				$data_persetujuan['tapping_fix_2_temp']			= $tapping_fix_2_temp;	
				$data_persetujuan['tapping_fix_2']				= $arr_data_lama['tapping_fix_2'];	
				$data_persetujuan['status_1']					= $status_1;				
				$data_persetujuan['alasan_1']					= $alasan_1;				
				
				$data_persetujuan['id']							= $id;
								
				$persetujuan_kehadiran = $this->m_persetujuan_kehadiran->persetujuan_kehadiran($data_persetujuan);
				
				$bulan_tahun = substr($tahun_bulan,-2)."-".substr($tahun_bulan,0,4);
				$this->session->set_flashdata('tampil_bulan_tahun',$bulan_tahun);
				
				if($persetujuan_kehadiran!="0")
				{	
					$this->session->set_flashdata('success',"Aksi Persetujuan/tolak kehadiran berhasil.");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_persetujuan_kehadiran->select_kehadiran_by_id($id,$tahun_bulan);
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
				
				redirect(base_url($this->folder_controller.'persetujuan_kehadiran'));			
				
			}else
			{					
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'persetujuan_kehadiran'));	
			}	
		}
	

	}
	
	/* End of file persetujuan_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/persetujuan_kehadiran.php */