<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Persetujuan_lembur_manual extends CI_Controller {
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
			
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
        	
        	$this->load->library('phpexcel'); 
						
			$this->load->model($this->folder_model."m_persetujuan_lembur_manual");
			$this->load->model("lembur/m_pengajuan_lembur");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Persetujuan Lembur Manual";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);	
			izin($this->akses["akses"]);
		}
		
		public function index() {	
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			$this->session->unset_userdata('tampil_tahun_bulan');
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."persetujuan_lembur_manual";
			
			//$this->data["bulan"] = date('Y-m');
			$this->data["month_list"] = $this->m_persetujuan_lembur_manual->get_month_list();
			$this->data["approve"] = '';
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_ess_lembur_sdm() {		
			$approve = $this->input->post('status');
			$tahun_bulan = $this->input->post('tahun_bulan');
			$this->load->model($this->folder_model."M_tabel_persetujuan_lembur_manual");
			$list 	= $this->M_tabel_persetujuan_lembur_manual->get_datatables($approve, $tahun_bulan);	
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $val) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $val->no_pokok;
				$row[] = $val->nama;
				$row[] = $val->tgl_dws;
				$row[] = $val->tgl_mulai.' '.date('H:i', strtotime($val->jam_mulai));
				$row[] = $val->tgl_selesai.' '.date('H:i', strtotime($val->jam_selesai));
				$row[] = empty($val->waktu_mulai_fix)?'-':substr($val->waktu_mulai_fix, 0, 16).' <br>s/d<br> '.substr($val->waktu_selesai_fix, 0, 16);
                $row[] = get_ket_lembur($val->no_pokok, $val->tgl_dws);
				/*if($val->waktu_mulai_fix==null || $val->waktu_selesai_fix==null || $val->waktu_mulai_fix=='' || $val->waktu_selesai_fix=='') {
					$row[] = "<button class='btn btn-warning btn-xs'>Tidak Diakui</button>";
				}
				else if($val->approval_status=='1') {
					$row[] = "<button class='btn btn-success btn-xs'>Disetujui SDM</button>";
				}
				else if($val->approval_status=='2') {
					$row[] = "<button class='btn btn-danger btn-xs'>Ditolak SDM</button>";
				}
				else if($val->approval_status=='0' || $val->approval_status==null) {
					$row[] = "<button class='btn btn-default btn-xs'>Menunggu Persetujuan</button>";
				}
				else {
					$row[] = "<button class='btn btn-danger btn-xs'>Tidak Valid</button>";
				}*/
				//$row[] = $val->approval_status;
				$data[] = $row;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_persetujuan_lembur_manual->count_all($approve, $tahun_bulan),
					"recordsFiltered" => $this->M_tabel_persetujuan_lembur_manual->count_filtered($approve, $tahun_bulan),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}
		
		private function persetujuan($id,$approval_sdm,$approval_sdm_by) {
			//===== Log Start =====
			$arr_data_lama = $this->m_persetujuan_lembur_manual->select_lembur_by_id($id);
			$log_data_lama = "";				
			foreach($arr_data_lama as $key => $value) {
				if(strcmp($key,"id")!=0){
					if(!empty($log_data_lama)){
						$log_data_lama .= "<br>";
					}
					$log_data_lama .= "$key = $value";
				}
			}
			//===== Log end =====
			
			//insert
			$data_persetujuan['id']				= $id;
			$data_persetujuan['approval_sdm']	= $approval_sdm;				
			$data_persetujuan['approval_sdm_by']= $approval_sdm_by;
											
			$persetujuan_lembur_manual = $this->m_persetujuan_lembur_manual->persetujuan_lembur_manual($data_persetujuan);
			
			if($persetujuan_lembur_manual!="0") {	
				$this->session->set_flashdata('success',"Aksi Persetujuan/tolak lembur oleh SDM, berhasil.");
				
				//===== Log Start =====
				$arr_data_baru = $this->m_persetujuan_lembur_manual->select_lembur_by_id($id);
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
				
			}
			else {
				$this->session->set_flashdata('warning',"Aksi Persetujuan/tolak Gagal");
			}
		}
		
		public function view_approve()
		{			
			$id = $this->input->post('id_pengajuan');
			$data = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_pegawai_id($id);
			$data["function"] = __FUNCTION__;
			$data["id_"] = $data["id"];
			$data["no_pokok"] = $data["no_pokok"];
			$data["nama_pegawai"] = $data["nama_pegawai"];
			$data["tgl_mulai"] = $data["tgl_mulai"];
			$data["tgl_selesai"] = $data["tgl_selesai"];
			$data["jam_mulai"] = date('H:i', strtotime($data["jam_mulai"]));
			$data["jam_selesai"] = date('H:i', strtotime($data["jam_selesai"]));
			$data["overtime_break"] = $data["overtime_break"];
			$data["created_at"] = $data["created_at"];
			$data["created_name"] = nama_karyawan_by_id($data["created_by"]);
			$data["created_np"] = np_karyawan_by_id($data["created_by"]);
			$data["sdm_name"] = nama_karyawan_by_np($this->session->userdata('no_pokok'));
			$data["sdm_np"] = $this->session->userdata('no_pokok');
			$data["approval_status"] = $data["approval_status"];
			$data["approval_date"] = $data["approval_date"];
			$data["approval_nama"] = $data["approval_nama"];
			$data["approval_nama_jabatan"] = $data["approval_nama_jabatan"];
			$data["approval_nama_unit"] = $data["approval_nama_unit"];
			$data["approval_np"] = $data["approval_np"];
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));
			$data["akses"] = $this->akses;
			$this->load->view($this->folder_view."approve_lembur_sdm",$data);
		}
		
		public function save() {	
			$id_ = $this->input->post('id_pengajuan');
			$pengajuan = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_pegawai_id($id_);
			$mulai = date('Y-m-d', strtotime($pengajuan['tgl_mulai'])).' '.date('H:i', strtotime($pengajuan['jam_mulai']));
			$selesai = date('Y-m-d', strtotime($pengajuan['tgl_selesai'])).' '.date('H:i', strtotime($pengajuan['jam_selesai']));
			$data['approval_status'] = $this->input->post('persetujuan_approval_sdm');
			$data['approval_np'] = $this->session->userdata('no_pokok');
			$data['approval_nama'] = nama_karyawan_by_np($this->data['no_pokok']);
			$data['approval_nama_jabatan'] = nama_jabatan_by_np($this->data['no_pokok']);
			$data['approval_nama_unit'] = nama_unit_by_np($this->data['no_pokok']);
			$data['approval_date'] = date('Y-m-d H:i:s');
			$approve = $this->m_persetujuan_lembur_manual->save_approval($id_, $data);

			$get_lembur['no_pokok'] = $pengajuan['no_pokok'];
			$get_lembur['tgl_dws'] = $pengajuan['tgl_dws'];
			$get_lembur['id'] = $id_;
			$this->m_pengajuan_lembur->set_cico($get_lembur);
			
			if ($approve == true) {
				$this->session->set_flashdata('success', 'Berhasil Melakukan Persetujuan Lembur Kepada <b>'.$pengajuan['nama_pegawai'].' ('.$pengajuan['no_pokok'].') Pada '.$mulai.' - '.$selesai.'</b>');
			}
			else {
				$this->session->set_flashdata('failed', 'Gagal Melakukan Persetujuan Lembur Kepada <b>'.$pengajuan['nama_pegawai'].' ('.$pengajuan['no_pokok'].') Pada '.$mulai.' - '.$selesai.'</b>');
			}
			redirect(site_url($this->folder_controller.'persetujuan_lembur_manual'));
		}
		
		public function action_persetujuan_lembur_manual_all()
		{		

			$submit = $this->input->post('submit');
			
			$persetujuan_filter_belum			= $this->input->post('persetujuan_filter_belum');		
			$persetujuan_filter_atasan_1		= $this->input->post('persetujuan_filter_atasan_1');		
			$persetujuan_filter_atasan_2		= $this->input->post('persetujuan_filter_atasan_2');		
			$persetujuan_filter_sdm				= $this->input->post('persetujuan_filter_sdm');
			$persetujuan_filter_belum_sdm		= $this->input->post('persetujuan_filter_belum_sdm');				
			$persetujuan_filter_batal			= $this->input->post('persetujuan_filter_batal');		
			$persetujuan_filter_tolak_atasan	= $this->input->post('persetujuan_filter_tolak_atasan');
			$persetujuan_filter_tolak_sdm		= $this->input->post('persetujuan_filter_tolak_sdm');
			
			$this->session->set_flashdata('persetujuan_filter_belum',$persetujuan_filter_belum);
			$this->session->set_flashdata('persetujuan_filter_atasan_1',$persetujuan_filter_atasan_1);
			$this->session->set_flashdata('persetujuan_filter_atasan_2',$persetujuan_filter_atasan_2);
			$this->session->set_flashdata('persetujuan_filter_sdm',$persetujuan_filter_sdm);
			$this->session->set_flashdata('persetujuan_filter_belum_sdm',$persetujuan_filter_belum_sdm);
			$this->session->set_flashdata('persetujuan_filter_batal',$persetujuan_filter_batal);
			$this->session->set_flashdata('persetujuan_filter_tolak_atasan',$persetujuan_filter_tolak_atasan);
			$this->session->set_flashdata('persetujuan_filter_tolak_sdm',$persetujuan_filter_tolak_sdm);
			
			if($submit)
			{			
				$query = $this->m_persetujuan_lembur_manual->select_lembur_siap_approve_all();
				foreach ($query->result_array() as $data) 
				{				
					$id				= $data['id'];
					$approval_sdm	= '1';				
					$approval_sdm_by= $_SESSION["no_pokok"];
					
					$this->persetujuan($id,$approval_sdm,$approval_sdm_by);
				}
				
				redirect(base_url($this->folder_controller.'persetujuan_lembur_manual'));			
				
			}else
			{					
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'persetujuan_lembur_manual'));	
			}
		}
	
		public function cetak($approve, $bulan){
	        $data = $this->m_persetujuan_lembur_manual->getDataCetak($approve, $bulan);
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Persetujuan_lembur_manual.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_persetujuan_lembur_manual.xlsx');

	        $border = array (
	        'borders' => array (
	            'allborders' => array (
	                'style' => PHPExcel_Style_Border::BORDER_THIN,
	                ),
	            ),
	        );
	        $valign = array (
	            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
	            'rotation'   => 0,
	            'wrap'     => true
	        );
	        $valign2 = array (
	            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
	            'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
	            'rotation'   => 0,
	            'wrap'     => true
	        );

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $awal 	= 4;
	        $no 	= 1;

	        foreach ($data as $row) {
                $lembur_diakui = '-';
                $jam = ''; $menit = '';
                
                if(($row['waktu_mulai_fix']!=NULL && trim($row['waktu_mulai_fix'])!='') && ($row['waktu_selesai_fix']!=NULL && trim($row['waktu_selesai_fix'])!='')){
                    $diff = strtotime($row['waktu_selesai_fix']) - strtotime($row['waktu_mulai_fix']); // in seconds
                    $jam = intval($diff/3600);
                    $menit = intval(($diff%3600) / 60);
                }
                
                if($row['waktu_mulai_fix']!=NULL && trim($row['waktu_mulai_fix'])!=''){
                    $explode_waktu_mulai_diakui = explode(' ', $row['waktu_mulai_fix']);
                    $tanggal_mulai_diakui = tanggal_indonesia($explode_waktu_mulai_diakui[0]);
                    $jam_mulai_diakui = substr($explode_waktu_mulai_diakui[1], 0, 5);
                } else{
                    $tanggal_mulai_diakui = '';
                    $jam_mulai_diakui = '';
                }
                
                if($row['waktu_selesai_fix']!=NULL && trim($row['waktu_selesai_fix'])!=''){
                    $explode_waktu_selesai_diakui = explode(' ', $row['waktu_selesai_fix']);
                    $tanggal_selesai_diakui = tanggal_indonesia($explode_waktu_selesai_diakui[0]);
                    $jam_selesai_diakui = substr($explode_waktu_selesai_diakui[1], 0, 5);
                } else{
                    $tanggal_selesai_diakui = '';
                    $jam_selesai_diakui = '';
                }
	        	//$lembur_diakui = empty($row['waktu_mulai_fix'])?'-':substr($row['waktu_mulai_fix'], 0, 16).' s/d '.substr($row['waktu_selesai_fix'], 0, 16);
				/*$status = "Tidak Valid";
	        	if($row['approval_status']=='1') {
					$status = "Disetujui";
				} else if($row['approval_status']=='2') {
					$status = "Ditolak";
				} else if($row['approval_status']=='0') {
					$status = "Belum Approve";
				}*/

	            $excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($row['no_pokok']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, strtoupper($row['nama']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, tanggal_indonesia($row['tgl_dws']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, tanggal_indonesia($row['tgl_mulai']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, substr($row['jam_mulai'], 0, 5), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('G'.$awal, tanggal_indonesia($row['tgl_selesai']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('H'.$awal, substr($row['jam_selesai'], 0, 5), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('I'.$awal, $tanggal_mulai_diakui, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('J'.$awal, $jam_mulai_diakui, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('K'.$awal, $tanggal_selesai_diakui, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('L'.$awal, $jam_selesai_diakui, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('M'.$awal, $jam, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('N'.$awal, $menit, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('O'.$awal, get_ket_lembur($row['no_pokok'], $row['tgl_dws']), PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->getStyle('O'.$awal)->getAlignment()->setWrapText(true);
	            //$excel->getActiveSheet()->setCellValueExplicit('I'.$awal, strtoupper($status), PHPExcel_Cell_DataType::TYPE_STRING);
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
	
	/* End of file persetujuan_lembur_manual.php */
	/* Location: ./application/controllers/lembur/persetujuan_lembur_manual.php */