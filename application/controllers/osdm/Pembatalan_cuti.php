<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pembatalan_cuti extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_controller = 'osdm/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
								
			$this->load->model($this->folder_model."m_pembatalan_cuti");
			$this->load->model("lembur/m_pengajuan_lembur");
		
        	$this->load->library('phpexcel'); 
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Pembatalan Cuti";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index()
		{			
		//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pembatalan_cuti";
					
			
			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			$query = $this->db->query("select DISTINCT(substr(date,1,7)) AS tahun_bulan from ess_pembatalan_cuti ORDER BY date DESC");
			foreach ($query->result_array() as $data) 
			{					
				if(strlen($data['tahun_bulan'])==7) {
					$pisah = explode('-',$data['tahun_bulan']);
					$tahun = $pisah[0];
					$bulan = $pisah[1];
					$bulan_tahun = $bulan."-".$tahun;
					$array_tahun_bulan[] = $bulan_tahun; 
				}
			}
				
			//ambil mst dws	

			$array_jadwal_kerja 	= $this->m_pembatalan_cuti->select_mst_karyawan_aktif();
			
			$array_daftar_cuti	= $this->m_pembatalan_cuti->select_daftar_cuti();
			$array_daftar_cuti_bersama	= $this->m_pembatalan_cuti->select_daftar_cuti_bersama();
			
			
			$this->data['array_tahun_bulan'] 	= $array_tahun_bulan;	
			$this->data['array_jadwal_kerja'] 	= $array_jadwal_kerja;	
			$this->data['array_daftar_cuti']= $array_daftar_cuti;
			$this->data['array_daftar_cuti_bersama']= $array_daftar_cuti_bersama;				
			
			$this->load->view('template',$this->data);
		}
		
		//check date di tabel ess_cuti
		public function ajax_getDate()
		{
			$insert_id_cuti		= $this->input->post('vid_cuti');
			
			$pisah = explode(',',$insert_id_cuti);
			$jenis_cuti = $pisah[0];
			$id_cuti	= $pisah[1];
				
			if($jenis_cuti=='Cuti')
			{
				$ambil			= $this->m_pembatalan_cuti->select_cuti_by_id($id_cuti);
				
				$start_date = $ambil['start_date'];
				$end_date 	= $ambil['end_date'];
			}else
			if($jenis_cuti=='Cuti Bersama')
			{
				$ambil			= $this->m_pembatalan_cuti->select_cuti_bersama_by_id($id_cuti);
				
				$start_date = $ambil['tanggal_cuti_bersama'];
				$end_date 	= $ambil['tanggal_cuti_bersama'];
			}
				
			
									
			$today 				= date('Y-m-d');
			$today_tahun_bulan	= date('Y-m');
			$today_cutoff		= $today_tahun_bulan."-10";
			
			//today  melebihi batas cutoff		
			if($today>$today_cutoff)
			{
				$tanggal_awal_bulan = $today_tahun_bulan."-01";
				
				if($start_date<$tanggal_awal_bulan) //jika startdate lebih awal dari awal bulan
				{
					$start_date = $tanggal_awal_bulan;
				}				
			}else //jika belum melebihi cutoff, masih tampil bulan sebelumnya
			{
				$tahun_bulan_sebelum = date("Y-m", strtotime("-1 months"));
				$tanggal_bulan_sebelumnya = $tahun_bulan_sebelum."-01";
				
				if($start_date<$tanggal_bulan_sebelumnya) //jika startdate lebih awal dari bulan sebelumnya
				{
					$start_date = $tanggal_bulan_sebelumnya;
				}
			}
			
			
			$date = $start_date.','.$end_date;
			
			if ($date) 
			{				
				echo $date; 			
			}else			
			{						 
				echo '';
			}			
		}
		

		
		public function tabel_pembatalan_cuti($tampil_bulan_tahun = null)
		{		
			$this->load->model($this->folder_model."/M_tabel_pembatalan_cuti");
			
			//akses ke menu ubah				
			if($this->akses["batal"]) //jika pengguna
			{
				$disabled_batal = '';
			}else
			{
				$disabled_batal = 'disabled';
			}
						
			$bulan = substr($tampil_bulan_tahun,0,2);
			$tahun = substr($tampil_bulan_tahun,3,4);
			
			$arr_bulan_tahun = array();
			$arr_bulan_tahun['bulan'] = $bulan;
			$arr_bulan_tahun['tahun'] = $tahun;
						
			$list = $this->M_tabel_pembatalan_cuti->get_datatables($arr_bulan_tahun);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;	
				$row[] = $tampil->uraian;	
				$row[] = tanggal_indonesia($tampil->date);
				$row[] = tanggal_indonesia($tampil->date_submit);	
				
							
				//jika sudah lewat date submit erp
				if($tampil->date_submit<=date("Y-m-d")) //jika sudah lewat masa cutoff
				{
					$row[] = "<button class='btn btn-primary btn-xs batal_button' data-toggle='modal' data-target='#modal_batal'
					data-id='$tampil->id'	
					data-np-karyawan='$tampil->np_karyawan'	
					data-nama='$tampil->nama'	
					data-uraian='$tampil->uraian'	
					data-date='$tampil->date'						
					$disabled_batal>Batal";						
				}else
				{				
					$row[] = "<button class='btn btn-primary btn-xs batal_button' data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada H+1 Pembatalan' disabled>Submit ERP</button>";	
					
				}
				
				
				
			
				
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_pembatalan_cuti->count_all($arr_bulan_tahun),
							"recordsFiltered" => $this->M_tabel_pembatalan_cuti->count_filtered($arr_bulan_tahun),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function action_insert_pembatalan_cuti()
		{			
			$submit = $this->input->post('submit');
									
			if($submit)
			{					
				
				$insert_date_awal 		= date('Y-m-d',strtotime($this->input->post('insert_date_awal')));
				$insert_date_akhir 		= date('Y-m-d',strtotime($this->input->post('insert_date_akhir')));
				$insert_id_cuti 		= $this->input->post('insert_id_cuti');			
				
				$pisah = explode(',',$insert_id_cuti);
				$jenis_cuti = $pisah[0];
				$id_cuti	= $pisah[1];
				
				if($jenis_cuti=='Cuti')
				{
					$data_cuti				= $this->m_pembatalan_cuti->select_cuti_by_id($id_cuti);
					$data_cuti_uraian		= $data_cuti['uraian'];
					$data_cuti_np_karyawan	= $data_cuti['np_karyawan'];
					$data_cuti_nama			= $data_cuti['nama'];
					$data_cuti_start_date	= $data_cuti['start_date'];
					$data_cuti_end_date		= $data_cuti['end_date'];
					$data_is_cuti_bersama	= '0';
				}else
				if($jenis_cuti=='Cuti Bersama')
				{
					$data_cuti				= $this->m_pembatalan_cuti->select_cuti_bersama_by_id($id_cuti);
					$data_cuti_uraian		= 'Cuti Bersama';
					$data_cuti_np_karyawan	= $data_cuti['np_karyawan'];
					$data_cuti_nama			= erp_master_data_by_np($data_cuti_np_karyawan, date('Y-m-d'))['nama'];
					$data_cuti_start_date	= $data_cuti['tanggal_cuti_bersama'];
					$data_cuti_end_date		= $data_cuti['tanggal_cuti_bersama'];
					$data_is_cuti_bersama	= '1';
									
				}
				
				
				$error_exist 	= '';
				$error 			= '';
				$success 		= '';
				$date			= $insert_date_awal;
				$date_akhir		= $insert_date_akhir;
				while (strtotime($date) <= strtotime($date_akhir)) 
				{					
					//validasi data sudah ada
					$data_exist['id_cuti'] 			= $id_cuti;
					$data_exist['is_cuti_bersama'] 	= $data_is_cuti_bersama;
					$data_exist['date'] 			= $date;
					
					$exist	= $this->m_pembatalan_cuti->check_pembatalan_exist($data_exist);
					
					if($exist!=0)
					{
						$error_exist = $error_exist."<b>Gagal validasi</b>, <b>$data_cuti_np_karyawan | $data_cuti_nama </b>Data Pembatalan <b>$data_cuti_uraian</b> Sudah Tersedia <b>$data_cuti_uraian</b> pada <b>$date</b>.<br>";			
					}else
					{
						//insert
						if($jenis_cuti=='Cuti')
						{
							$data_insert['np_karyawan'] 	= $data_cuti['np_karyawan'];
							$data_insert['personel_number']	= $data_cuti['personel_number'];
							$data_insert['nama'] 			= $data_cuti['nama'];
							$data_insert['nama_jabatan'] 	= $data_cuti['nama_jabatan'];
							$data_insert['kode_unit'] 		= $data_cuti['kode_unit'];
							$data_insert['nama_unit'] 		= $data_cuti['nama_unit'];
							$data_insert['absence_type']	= $data_cuti['absence_type'];
							$data_insert['id_cuti']			= $id_cuti;
							$data_insert['is_cuti_bersama']	= $data_is_cuti_bersama;
							$data_insert['date'] 			= $date;
							$data_insert['date_submit']		= date('Y-m-d');			
						}else
						if($jenis_cuti=='Cuti Bersama')
						{
							$nama_karyawan 		= erp_master_data_by_np($data_cuti_np_karyawan, date('Y-m-d'))['nama'];
							$personel_number	= erp_master_data_by_np($data_cuti_np_karyawan, date('Y-m-d'))['personnel_number'];
							$nama_jabatan		= erp_master_data_by_np($data_cuti_np_karyawan, date('Y-m-d'))['nama_jabatan'];
							$kode_unit 			= erp_master_data_by_np($data_cuti_np_karyawan, date('Y-m-d'))['kode_unit'];
							$nama_unit 			= erp_master_data_by_np($data_cuti_np_karyawan, date('Y-m-d'))['nama_unit'];
						
							$data_insert['np_karyawan'] 	= $data_cuti['np_karyawan'];
							$data_insert['personel_number']	= $personel_number;
							$data_insert['nama'] 			= $nama_karyawan;
							$data_insert['nama_jabatan'] 	= $nama_jabatan;
							$data_insert['kode_unit'] 		= $kode_unit;
							$data_insert['nama_unit'] 		= $nama_unit;
							$data_insert['absence_type']	= '2001|1020'; //cuti bersama
							$data_insert['id_cuti']			= $id_cuti;
							$data_insert['is_cuti_bersama']	= $data_is_cuti_bersama;
							$data_insert['date'] 			= $date;
							$data_insert['date_submit']		= date('Y-m-d');		
						
						
						}
									
															
						$insert = $this->m_pembatalan_cuti->insert_pembatalan_cuti($data_insert);
						
						//update id_cuti
						$this->load->model('osdm/m_persetujuan_cuti_sdm');
						$this->m_persetujuan_cuti_sdm->update_cico_cuti($data_cuti_np_karyawan,$date);						
						if($insert==0)
						{
							$error = $error."<b>Gagal input</b>, <b>$data_cuti_np_karyawan | $data_cuti_nama</b> Pembatalan <b>$data_cuti_uraian</b> pada <b>$date</b> gagal masuk database.<br>";				
						}else
						{
							$success = $success."<b>Berhasil input</b>, <b>$data_cuti_np_karyawan | $data_cuti_nama</b> Pembatalan <b>$data_cuti_uraian</b> pada <b>$date</b> telah masuk database.<br>";
																			
							//===== Log Start =====
							$arr_data_baru = $this->m_pembatalan_cuti->select_pembatalan_cuti_by_id($insert);					
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
							
									
						}	
						
					}
					
					
											
					$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
				}

				if($error_exist!='')
				{
					$this->session->set_flashdata('warning',$error_exist);
				}
				
				if($error!='')
				{
					$this->session->set_flashdata('warning',$error);
				}
				
				if($success!='')
				{
					$this->session->set_flashdata('success',$success);
				}

				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'pembatalan_cuti/'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'pembatalan_cuti/'));	
			}	
		}
		
		public function action_batal_pembatalan_cuti()
		{			
			$submit = $this->input->post('submit');
					
					
			if($submit)
			{					
				$id 				= $this->input->post('batal_id');				
							
				//===== Log Start =====
				$arr_data_lama = $this->m_pembatalan_cuti->select_pembatalan_cuti_by_id($id);
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
				
				
				$np_karyawan 	= $arr_data_lama['np_karyawan'];
				$nama		 	= nama_karyawan_by_np($np_karyawan);
				$date 			= $arr_data_lama['date'];
				
				$data_batal['id'] 			= $id;
				
				$batal = $this->m_pembatalan_cuti->batal_pembatalan_cuti($data_batal);
				
				//update id_cuti
				$this->load->model('osdm/m_persetujuan_cuti_sdm');
				$this->m_persetujuan_cuti_sdm->update_cico_cuti($np_karyawan,$date);
					
				if($batal==0)
				{
					$this->session->set_flashdata('warning',"Update Gagal");
				}else
				{
					$this->session->set_flashdata('success',"<b>Pembatalan Berhasil</b>, <br><b>$np_karyawan | $nama</b> Pada Perencanaan jadwal kerja tanggal <b>$date</b>");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_pembatalan_cuti->select_pembatalan_cuti_by_id($id);
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
						"deskripsi" => "update ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
					
					
				}	
				
				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'pembatalan_cuti/'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'pembatalan_cuti/'));	
			}	
		}
		
	
	
		public function cetak(){
			
				$bulan_tahun 	= $this->input->post('bulan_tahun');
				$pisah = explode('-',$bulan_tahun);
				$tampil_bulan_tahun['tahun'] = $pisah[1];
				$tampil_bulan_tahun['bulan'] = $pisah[0];
				
				$data_pembatalan = $this->m_pembatalan_cuti->select_pembatalan_cuti_by_month($tampil_bulan_tahun);
			   

				
				error_reporting(E_ALL);
				ini_set('display_errors', TRUE);
				ini_set('display_startup_errors', TRUE);

				header("Content-type: application/vnd.ms-excel");
				//nama file
				header("Content-Disposition: attachment; filename=Pembatalan_cuti.xlsx");
				header('Cache-Control: max-age=0');

				$excel = PHPExcel_IOFactory::createReader('Excel2007');
				$excel = $excel->load('./asset/Template_pembatalan_cuti.xlsx');

				//anggota
				$excel->setActiveSheetIndex(0);
				$kolom 	= 2;
				$kolom2	= 2;
				$baris 	= 4;
				$no 	= 1;
				$length = 0;
				$alphabet = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH');

	
				foreach ($data_pembatalan as $row) {
					$excel->getActiveSheet()->setCellValueExplicit('A'.$baris, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
					$excel->getActiveSheet()->getStyleByColumnAndRow(0, $baris)->applyFromArray(array(
						'alignment' => array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						),
						'borders' => array(
							'allborders' => array (
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								),
							)
						)
					);
					
					if($row->is_cuti_bersama=='1') //JIKA CUTI BERSAMA
					{
						$np_karyawan	= $row->np_karyawan;
						
						$nama_karyawan 		= erp_master_data_by_np($np_karyawan, date('Y-m-d'))['nama'];	
						
						$nama				= $nama_karyawan;
						$uraian				='CUTI BERSAMA';
						
					}else
					{
						$np_karyawan	= $row->np_karyawan;
						$nama			= $row->nama;
						$uraian			=$row->uraian;
					}
					
					$excel->getActiveSheet()->setCellValueExplicit('B'.$baris, strtoupper($np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
					$excel->getActiveSheet()->getStyleByColumnAndRow(1, $baris)->applyFromArray(array(
						'borders' => array(
							'allborders' => array (
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								),
							)
						)
					);
					$excel->getActiveSheet()->setCellValueExplicit('C'.$baris, strtoupper($nama), PHPExcel_Cell_DataType::TYPE_STRING);
					$excel->getActiveSheet()->getStyleByColumnAndRow(1, $baris)->applyFromArray(array(
						'borders' => array(
							'allborders' => array (
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								),
							)
						)
					);
										 
					$excel->getActiveSheet()->setCellValueExplicit('D'.$baris, strtoupper($uraian),
					PHPExcel_Cell_DataType::TYPE_STRING);
					$excel->getActiveSheet()->getStyleByColumnAndRow(1, $baris)->applyFromArray(array(
						'borders' => array(
							'allborders' => array (
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								),
							)
						)
					);
					$excel->getActiveSheet()->setCellValueExplicit('E'.$baris, strtoupper(tanggal_indonesia($row->date)), PHPExcel_Cell_DataType::TYPE_STRING);
					$excel->getActiveSheet()->getStyleByColumnAndRow(1, $baris)->applyFromArray(array(
						'borders' => array(
							'allborders' => array (
								'style' => PHPExcel_Style_Border::BORDER_THIN,
								),
							)
						)
					);
					
				/*	
					$excel->getActiveSheet()->getColumnDimensionByColumn(1)->setWidth(35);
					foreach ($data_tgl as $isi) {
						if ($row->nama == $isi->nama) {
							foreach ($period as $key => $value) {
								if ($isi->date == $value->format('Y-m-d')) {
									$excel->getActiveSheet()->setCellValueByColumnAndRow($kolom2, $baris, get_jadwal_from_dws(strtoupper($isi->dws)));
									$excel->getActiveSheet()->getStyleByColumnAndRow($kolom2, $baris)->applyFromArray(array(
										'alignment' => array(
											'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
										),
										'borders' => array(
											'allborders' => array (
												'style' => PHPExcel_Style_Border::BORDER_THIN,
												),
											)
										)
									);
									for ($i=$kolom2; $i < $length+2; $i++) { 
										$excel->getActiveSheet()->getStyleByColumnAndRow($i, $baris)->applyFromArray(array(
											'borders' => array(
												'allborders' => array (
													'style' => PHPExcel_Style_Border::BORDER_THIN,
													),
												)
											)
										);
									}
									$kolom2 = 2;
									break;
								}else{
									$excel->getActiveSheet()->getStyleByColumnAndRow($kolom2, $baris)->applyFromArray(array(
										'borders' => array(
											'allborders' => array (
												'style' => PHPExcel_Style_Border::BORDER_THIN,
												),
											)
										)
									);
									$kolom2 += 1;
								}
							}
						}
					}
					*/
					$kolom2 = 2;
					$baris += 1;
				}

				$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
				$objWriter->setIncludeCharts(TRUE);
				$objWriter->setPreCalculateFormulas(TRUE);
				PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
				$objWriter->save('php://output');
				exit();
			}
		
		}
		
	
	
	/* End of file pembatalan_cuti.php */
	/* Location: ./application/controllers/kehadiran/pembatalan_cuti.php */