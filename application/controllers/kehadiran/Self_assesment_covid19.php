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
			$this->load->helper("tanggal_helper");
	
			$this->load->model($this->folder_model."M_self_assesment_covid19");
			$this->load->model("master_data/M_karyawan");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Health Passport";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			// izin($this->akses["akses"]);
			
		}
		
		public function index()
		{			
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
                
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."self_assesment_covid19";

			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$list_kode_unit=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($list_kode_unit,$data['kode_unit']);								
				}	
				
				$list_kode_unit = implode("','",$list_kode_unit);
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				$list_kode_unit=array();
				array_push($list_kode_unit,$_SESSION["kode_unit"]);	
				
				$list_kode_unit = implode("','",$list_kode_unit);
			}else
			{
				$list_kode_unit=false;
			}

			$array_tahun_bulan = array();
			if($list_kode_unit==false)
			{				
				$query = $this->db->select("DATE_FORMAT(tanggal,'%Y-%m') as tahun_bulan")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_self_assesment_covid19');
			}else
			{
				$query = $this->db->select("DATE_FORMAT(tanggal,'%Y-%m') as tahun_bulan")->where("kode_unit IN ('".$list_kode_unit."')")->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_self_assesment_covid19');
			}					
			
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['tahun_bulan'],-2);
				$tahun = substr($data['tahun_bulan'],0,4);
				
				$bulan_tahun = $bulan."-".$tahun;				
				
				$array_tahun_bulan[] = $bulan_tahun; 
			}
            
            $this->data['array_tahun_bulan'] 	= $array_tahun_bulan;

			$this->load->model("lembur/m_pengajuan_lembur");
			$list_np = $this->m_pengajuan_lembur->get_np();
			$this->data["list_np"] = '<option></option>';
			foreach ($list_np as $val) {
				$this->data["list_np"] .= '<option value="'.$val['no_pokok'].'">'.$val['no_pokok'].' - '.str_replace("'", " ", $val['nama']).'</option>';
			}
			
			$this->load->view('template',$this->data);
			
		}	
		
		public function form()
		{			
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
                
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."self_assesment_covid19";
			    
			
			// $last_assesment 				= $this->M_self_assesment_covid19->last_assesment($this->session->userdata('no_pokok'));
			// $this->data['last_assesment']   = $last_assesment['created_at'];

			$this->load->model("lembur/m_pengajuan_lembur");
			$list_np = $this->m_pengajuan_lembur->get_np();
			$this->data["list_np"] = '<option></option>';
			foreach ($list_np as $val) {
				$this->data["list_np"] .= '<option value="'.$val['no_pokok'].'">'.$val['no_pokok'].' - '.str_replace("'", " ", $val['nama']).'</option>';
			}
			
			$this->load->view('template',$this->data);
			
		}	
		
		public function assesment()
		{	
			$id 							= $this->input->post('id');
			$tgl 							= $this->input->post('tgl');
			$last_assesment 				= $this->M_self_assesment_covid19->last_assesment($id, $tgl);
			$this->data['last_assesment']   = $last_assesment['created_at'];
			
			echo json_encode(array('last_assesment'=>$this->data['last_assesment'], 'data'=>json_encode($last_assesment)));
		}	
		
		public function tabel_ess_assesment_covid($bulan_tahun=null)
		{	
			if(@$bulan_tahun!=0){
                $month = $bulan_tahun;
            } else{
                $month = 0;
            }
			
			$this->load->model($this->folder_model."M_tabel_assesment");
			
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
			
			$list 	= $this->M_tabel_assesment->get_datatables($var,$month);	
			
			
			$data = array();
			$no = $_POST['start'];
			
	
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = '<small>'.$tampil->np_karyawan.' - '.$tampil->nama.'<br>('.$tampil->nama_unit.')</small>';
				$row[] = '<small>'.tanggal_indonesia($tampil->tanggal).'</small>';	
				$row[] = '<small>'.(($tampil->pernah_keluar == '1') ? 'Ya' : 'Tidak').'</small>';
				$row[] = '<small>'.(($tampil->transportasi_umum == '1') ? 'Ya' : 'Tidak').'</small>';
				$row[] = '<small>'.(($tampil->luar_kota == '1') ? 'Ya' : 'Tidak').'</small>';
				$row[] = '<small>'.(($tampil->kegiatan_orang_banyak == '1') ? 'Ya' : 'Tidak').'</small>';
				$row[] = '<small>'.(($tampil->kontak_pasien == '1') ? 'Ya' : 'Tidak').'</small>';
				$row[] = '<small>'.(($tampil->sakit == '1') ? 'Ya' : 'Tidak').'</small>';
				$row[] = '<small>'.$tampil->updated_at.'</small>';

				if (date('Y-m-d', strtotime($tampil->created_at))==date('Y-m-d') && $tampil->is_status=='1') {
					$btn_warna		='btn-warning';
					$btn_text		='Edit Assesment';
				} else if ($tampil->is_status=='0') {
					$btn_warna		='btn-danger';
					$btn_text		='Telah Dibatalkan';
				} else {
					$btn_warna		='btn-primary';
					$btn_text		='Lihat';
				}

				$row[] = "<button class='btn ".$btn_warna." btn-xs status_button' data-toggle='modal' data-target='#modal_add' 
					data-np-karyawan='".$tampil->np_karyawan."'
					data-nama='".$tampil->nama."'			
					data-tanggal='".$tampil->tanggal."'			
					data-pernah-keluar='".$tampil->pernah_keluar."'			
					data-transportasi-umum='".$tampil->transportasi_umum."'			
					data-luar-kota='".$tampil->luar_kota."'			
					data-kegiatan-orang-banyak='".$tampil->kegiatan_orang_banyak."'			
					data-kontak-pasien='".$tampil->kontak_pasien."'			
					data-sakit='".$tampil->sakit."'			
					data-is-status='".$tampil->is_status."'			
					data-created-at='".date('Y-m-d', strtotime($tampil->created_at))."'			
					data-created-by='".$tampil->created_by."'			
					data-canceled-at='".$tampil->canceled_at."'			
					data-canceled-by='".$tampil->canceled_by."'>".$btn_text."</button>";
				
				$data[] = $row;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_assesment->count_all($var,$month),
					"recordsFiltered" => $this->M_tabel_assesment->count_filtered($var,$month),
					"data" => $data,
			);

			echo json_encode($output);
		}
					
		public function action_insert()
		{			

			$submit 		= $this->input->post('submit');
			$np_karyawan 	= $this->input->post('id');
			$data_karyawan 	= $this->M_karyawan->get_detail_np($np_karyawan);
			$tgl 			= $this->input->post('tgl');
			$nama_karyawan	= $data_karyawan['nama'];
			//var_dump($data_karyawan);die();
			if($submit && $this->input->is_ajax_request())
			{
				$last_assesment = $this->M_self_assesment_covid19->last_assesment($np_karyawan, $tgl);
				if ($this->session->userdata('grup')=='1' || ($last_assesment['created_at']==null || $last_assesment['is_status']=='0' || ($last_assesment['is_status']=='1' && date('Y-m-d', strtotime($last_assesment['created_at']))==date('Y-m-d') && ($this->session->userdata('grup')=='4'||$this->session->userdata('grup')=='5')))) {
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
					$data_insert['tanggal']					= date('Y-m-d', strtotime($this->input->post("tgl")));
					
					if ($last_assesment['created_at'] == null || $last_assesment['is_status'] == '0') {
						$data_insert['created_at'] = date('Y-m-d H:i:s');
						$data_insert['created_by'] = $this->session->userdata('no_pokok');
						$insert = $this->M_self_assesment_covid19->insert($data_insert);
					}
					else {
						$data_where['np_karyawan'] = $data_karyawan['no_pokok'];
						$data_where['tanggal'] = date('Y-m-d', strtotime($this->input->post("tgl")));
						$insert = $this->M_self_assesment_covid19->update($data_insert, $data_where);
					}
					
					if($insert!="0")
					{
						if($insert=="edit") 
							$arr_data_baru = $this->M_self_assesment_covid19->select_data_by_id($last_assesment['id']);
						else
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

						$log_data_lama = "";					
						if($insert=="edit") {					
							foreach($last_assesment as $key => $value){
								if(strcmp($key,"id")!=0){
									if(!empty($log_data_lama)){
										$log_data_lama .= "<br>";
									}
									$log_data_lama .= "$key = $value";
								}
							}
						}

						$log = array(
							"id_pengguna" => $this->session->userdata("id_pengguna"),
							"id_modul" => $this->data['id_modul'],
							"deskripsi" => (($insert=="edit") ? "update " : "insert ").strtolower(preg_replace("/_/"," ",__CLASS__)),						
							"kondisi_baru" => $log_data_baru,
							"kondisi_lama" => $log_data_lama,
							"alamat_ip" => $this->data["ip_address"],
							"waktu" => date("Y-m-d H:i:s")
						);
						$this->m_log->tambah($log);
						
						$response['status']=true;
			            $response['message']='Data assesment <b>('.$np_karyawan.') '.$nama_karyawan.' </b> Tanggal '.$tgl.' telah tersimpan';
			            $response['data']=[];
					} else {
						$response['status']=false;
			            $response['message']='Data assesment <b>('.$np_karyawan.') '.$nama_karyawan.' </b> Tanggal '.$tgl.' gagal tersimpan';
			            $response['data']=[];
					}
				} else {
					$response['status']=false;
			        $response['message']='<b>('.$np_karyawan.') '.$nama_karyawan.' </b> Tanggal '.$tgl.' Sudah Mengisi Health Passport.';
			        $response['data']=[];
				}
			} else {					
				$response['status']=false;
	            $response['message']='Terjadi Kesalahan';
	            $response['data']=[];
			}

			echo json_encode($response);
		}

		public function action_cancel()
		{
			if ($this->input->is_ajax_request()) {
				$np_karyawan 	= $this->input->post('id');
				$tgl 			= $this->input->post('tgl');
				$data_karyawan 	= $this->M_karyawan->get_detail_np($np_karyawan);
				$nama_karyawan	= $data_karyawan['nama'];
						
				$last_assesment = $this->M_self_assesment_covid19->last_assesment($np_karyawan, $tgl);
				if (($this->session->userdata('grup')=='1') || (date('Y-m-d', strtotime($last_assesment['created_at']))==date('Y-m-d') && ($this->session->userdata('grup')=='4'||$this->session->userdata('grup')=='5'))) {

					if ($last_assesment['created_at'] != null) {
						$data_where['np_karyawan'] = $np_karyawan;
						$data_where['tanggal'] = date('Y-m-d', strtotime($tgl));
						$data_insert['is_status'] = '0';
						$data_insert['canceled_at'] = date('Y-m-d H:i:s');
						$data_insert['canceled_by']	= $this->session->userdata('no_pokok');

						$insert = $this->M_self_assesment_covid19->update($data_insert, $data_where);

						if($insert=="edit") {
							$arr_data_baru = $this->M_self_assesment_covid19->select_data_by_id($last_assesment['id']);

							$log_data_baru = "";					
							foreach($arr_data_baru as $key => $value){
								if(strcmp($key,"id")!=0){
									if(!empty($log_data_baru)){
										$log_data_baru .= "<br>";
									}
									$log_data_baru .= "$key = $value";
								}
							}

							$log_data_lama = "";					
							if($insert=="edit") {					
								foreach($last_assesment as $key => $value){
									if(strcmp($key,"id")!=0){
										if(!empty($log_data_lama)){
											$log_data_lama .= "<br>";
										}
										$log_data_lama .= "$key = $value";
									}
								}
							}

							$log = array(
								"id_pengguna" => $this->session->userdata("id_pengguna"),
								"id_modul" => $this->data['id_modul'],
								"deskripsi" => "cancel ".strtolower(preg_replace("/_/"," ",__CLASS__)),						
								"kondisi_baru" => $log_data_baru,
								"kondisi_lama" => $log_data_lama,
								"alamat_ip" => $this->data["ip_address"],
								"waktu" => date("Y-m-d H:i:s")
							);
							$this->m_log->tambah($log);
							
							$response['status']=true;
							$response['judul']='Berhasil';
				            $response['txt']='Data assesment <b>('.$np_karyawan.') '.$nama_karyawan.' </b> Tanggal '.$tgl.' telah dibatalkan';
				            $response['alert']='success';
						} else {
							$response['status']=false;
							$response['judul']='Gagal';
				            $response['txt']='Data assesment <b>('.$np_karyawan.') '.$nama_karyawan.' </b> Tanggal '.$tgl.' gagal dibatalkan!';
				            $response['alert']='error';
						}
					}
					else {
						$response['status']=false;
						$response['judul']='Gagal';
			            $response['txt']='Data assesment <b>('.$np_karyawan.') '.$nama_karyawan.' </b> Tanggal '.$tgl.' tidak valid!';
			            $response['alert']='error';
					}
				} else {
					$response['status']=false;
					$response['judul']='Gagal';
		            $response['txt']='Anda Tidak Memiliki Akses!';
		            $response['alert']='error';
				}
			}
			else {
				$response['status']=false;
				$response['judul']='Gagal';
	            $response['txt']='Terjadi Kesalahan!';
	            $response['alert']='error';
			}

			echo json_encode($response);
		}
					
		public function action_insert_old()
		{			

			$submit 		= $this->input->post('submit');
			
			$np_karyawan 	= $this->session->userdata('no_pokok');
			$data_karyawan 	= $this->M_karyawan->get_detail_np($np_karyawan);
						
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
		
		//04-01-2021 7648 Tri Wibowo, tambah fitur download excel berisiko 
		public function cetak_beresiko() 
		{		
			$this->load->library('phpexcel'); 						
			
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Karyawan_beresiko.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_karyawan_beresiko.xlsx');

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 4;
	        $no = 1;

			$query = $this->db->query("SELECT * FROM v_ess_health_passport");
			foreach ($query->result_array() as $data) 
			{
				
				$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('B'.$awal, date('d-m-Y', strtotime($data['tanggal'])), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, strtoupper($data['np_karyawan']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, ucwords($data['nama']), PHPExcel_Cell_DataType::TYPE_STRING);
				
				$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, ucwords($data['nama_unit']), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ucwords($data['kode_unit']), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, ucwords($data['Pergi_Tempat_Umum']), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('H'.$awal, ucwords($data['Menggunakan_Transportasi_Umum']), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('I'.$awal, ucwords($data['Penah_Keluar_Kota']), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('J'.$awal, ucwords($data['Berkerumun']), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('K'.$awal, ucwords($data['Kontak_Pasien']), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('K'.$awal, ucwords($data['Sakit']), PHPExcel_Cell_DataType::TYPE_STRING);
	           			   					
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