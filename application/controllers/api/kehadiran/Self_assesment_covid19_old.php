<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Self_assesment_covid19 extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
		
			$this->folder_view = 'self_assesment_covid19/';
			$this->folder_model = 'self_assesment_covid19/';
			$this->folder_controller = 'self_assesment_covid19/';
				
			$this->akses = array();
						
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
	
			$this->load->model($this->folder_model."M_self_assesment_covid19");
			$this->load->model("master_data/M_karyawan");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Self Assesment Covid19";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
			
		}
		
		public function index()
		{			
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
                
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."self_assesment_covid19";
			    
			
			$last_assesment 				= $this->M_self_assesment_covid19->last_assesment($this->session->userdata('no_pokok'));
			$this->data['last_assesment']   = $last_assesment['created_at'];
			
			$this->load->view('template',$this->data);
			
		}	
		
		public function assesment()
		{	
			$id 							= $this->input->post('id');
			$tgl 							= $this->input->post('tgl');
			$last_assesment 				= $this->M_self_assesment_covid19->last_assesment($id, $tgl);
			$this->data['last_assesment']   = $last_assesment['created_at'];
			
			echo json_encode(array('last_assesment'=>$this->data['last_assesment']));
		}	
		
		public function tabel_ess_cuti($bulan_tahun=null)
		{	
			if(@$bulan_tahun!=0){
                $month = $bulan_tahun;
            } else{
                $month = 0;
            }
			
			$this->load->model($this->folder_model."M_tabel_permohonan");
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$ada_data=0;
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($var,$data['kode_unit']);
					$ada_data=1;
				}
				if($ada_data==0)
				{
					$var='';
				}		
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				$var 	= $_SESSION["no_pokok"];							
			}else
			{
				$var = 1;				
			}
			
			$list 	= $this->M_tabel_permohonan->get_datatables($var,$month);	
			
			
			$data = array();
			$no = $_POST['start'];
			
	
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = $tampil->uraian;
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
				
				if($tampil->keterangan=='1')
				{
					$row[] ='Dalam Kota';
				}else
				if($tampil->keterangan=='2')
				{
					$row[] ='Luar Kota';
				}else
				{
					$row[] ='';
				}
					
				
				
				$id				= $tampil->id;
				$np_karyawan	= $tampil->np_karyawan;
				$nama			= $tampil->nama;
				$approval_1		= $tampil->approval_1;
				$approval_2		= $tampil->approval_2;	
				$status_1		= $tampil->status_1;
				$status_2		= $tampil->status_2;	
				$approval_1_date= $tampil->approval_1_date;
				$approval_2_date= $tampil->approval_2_date;	
				$approval_sdm	= $tampil->approval_sdm;
				$created_at		= $tampil->created_at;
				$created_by		= nama_karyawan_by_np($tampil->created_by);
				
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
						$btn_text		='Menunggu Atasan 2';
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
					$btn_text		='Disetujui Atasan 2';
					$btn_disabled 	='disabled';
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
				
				$status_pembatalan_cuti ='';
				$pembatalan_cuti		='';
				$pembatalan_cuti_header	=false;
				$pembatalan_cuti_tampil	='';
				$pem_cut = $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE np_karyawan='$np_karyawan' AND id_cuti='$id'");
				foreach ($pem_cut->result_array() as $pem) 
				{
					if($pembatalan_cuti_header==false)
					{
						$pembatalan_cuti_tampil="Terdapat Pembatalan Cuti :";
						
						$pembatalan_cuti_header=true;
					}
						
						$pembatalan_cuti_tampil=$pembatalan_cuti_tampil."<br>". $pem['date'];			
				}
				
				if($pembatalan_cuti_header==true)
				{
					$status_pembatalan_cuti="<br><br><button class='btn btn-default btn-xs'>$pembatalan_cuti_tampil</button>";
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
				>$btn_text</button>".$status_pembatalan_cuti;				
				
				/*if($approval_sdm==1)
				{
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
					$row[] = "<button class='btn btn-primary btn-xs batal_button'  data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
				}else
				{					
					$row[] = "<button class='btn btn-primary btn-xs batal_button' data-toggle='modal'  data-target='#modal_batal'
					data-id='$id'
					data-np-karyawan='$np_karyawan'
					data-nama='$nama'
					data-created-at='$created_at'
					data-created-by='$created_by'
					data-approval-1-nama='$approval_1_nama'
					data-approval-2-nama='$approval_2_nama'
					data-approval-1-status='$approval_1_status'
					data-approval-2-status='$approval_2_status'			
					$btn_disabled>Batal</button>";
					
				}
				
				
				
			
				
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_permohonan->count_all($var,$month),
							"recordsFiltered" => $this->M_tabel_permohonan->count_filtered($var,$month),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
					
		public function action_insert()
		{			

			$submit 		= $this->input->post('submit');
			$np_karyawan 	= $this->input->post('id');
			$data_karyawan 	= $this->M_karyawan->get_karyawan($np_karyawan);
			$tgl 			= $this->input->post('tgl');
			$nama_karyawan	= $data_karyawan['nama'];
						
			//var_dump($data_karyawan);die();
						
			if($submit)
			{
                //echo json_encode($this->input->post()); exit();
				$data_insert['np_karyawan']				= $data_karyawan['no_pokok'];
				$data_insert['personel_number']			= $data_karyawan['personnel_number'];
				$data_insert['nama']					= $data_karyawan['nama'];
				$data_insert['nama_jabatan']			= $data_karyawan['nama_jabatan'];
				$data_insert['kode_unit']				= $data_karyawan['kode_unit'];
				$data_insert['nama_unit']				= $data_karyawan['nama_unit'];
				$data_insert['tanggal']					= $this->input->post('tgl');
				$data_insert['pernah_keluar']			= $this->input->post("pernah_keluar");
				$data_insert['transportasi_umum']		= $this->input->post("transportasi_umum");
				$data_insert['luar_kota']				= $this->input->post("luar_kota");
				$data_insert['kegiatan_orang_banyak']	= $this->input->post("kegiatan_orang_banyak");
				$data_insert['kontak_pasien']			= $this->input->post("kontak_pasien");
				$data_insert['sakit']					= $this->input->post("sakit");
				$data_insert['tanggal']				= date('Y-m-d', strtotime($this->input->post("tgl")));
			
				$insert = $this->M_self_assesment_covid19->insert($data_insert);
				
				if($insert!="0")
				{	
					$this->session->set_flashdata('success',"Self Assesment Covid 19 <b>$np_karyawan | $nama_karyawan </b> berhasil ditambahkan.");
					
					//===== Log Start =====
					$arr_data_baru = $this->M_self_assesment_covid19->select_data_by_id($insert);					
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
						"deskripsi" => "insert ".strtolower(preg_replace("/_/"," ",__CLASS__)),						
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
					$response['status']=true;
		            $response['message']='Data assesment telah tersimpan';
		            $response['data']=[];
				}else
				{
					$response['status']=false;
		            $response['message']='Data assesment gagal tersimpan';
		            $response['data']=[];
				}	
				
				
			}else
			{					
				$response['status']=false;
	            $response['message']='Terjadi Kesalahan';
	            $response['data']=[];
			}

			echo json_encode($response);
		}
					
		public function action_insert_old()
		{			

			$submit 		= $this->input->post('submit');
			
			$np_karyawan 	= $this->session->userdata('no_pokok');
			$data_karyawan 	= $this->M_karyawan->get_karyawan($np_karyawan);
						
			//var_dump($data_karyawan);die();
						
			if($submit)
			{
                //echo json_encode($this->input->post()); exit();
				$data_insert['np_karyawan']				= $data_karyawan['no_pokok'];
				$data_insert['personel_number']			= $data_karyawan['personel_number'];
				$data_insert['nama']					= $data_karyawan['nama'];
				$data_insert['nama_jabatan']			= $data_karyawan['nama_jabatan'];
				$data_insert['kode_unit']				= $data_karyawan['kode_unit'];
				$data_insert['nama_unit']				= $data_karyawan['nama_unit'];
				$data_insert['pernah_keluar']			= $this->input->post('pernah_keluar');
				$data_insert['transportasi_umum']		= $this->input->post('transportasi_umum');
				$data_insert['luar_kota']				= $this->input->post('luar_kota');
				$data_insert['kegiatan_orang_banyak']	= $this->input->post('kegiatan_orang_banyak');
				$data_insert['kontak_pasien']			= $this->input->post('kontak_pasien');
				$data_insert['sakit']					= $this->input->post('sakit');
			
				$insert = $this->M_self_assesment_covid19->insert($data_insert);
				
				if($insert!="0")
				{	
					$this->session->set_flashdata('success',"Self Assesment Covid 19 <b>$np_karyawan | $nama_karyawan </b> berhasil ditambahkan.");
					
					//===== Log Start =====
					$arr_data_baru = $this->M_self_assesment_covid19->select_data_by_id($insert);					
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
						"deskripsi" => "insert ".strtolower(preg_replace("/_/"," ",__CLASS__)),						
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
				}else
				{
					$this->session->set_flashdata('warning',"Input Gagal");
				}	
				
				redirect(base_url($this->folder_controller.'self_assesment_covid19'));			
				
			}else
			{					
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'self_assesment_covid19'));	
			}	
		}
		
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */