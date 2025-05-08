<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Persetujuan_lembur extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'lembur/';
			$this->folder_model = 'lembur/';
			$this->folder_controller = 'lembur/';
			
			$this->akses = array();
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			$this->load->helper("cutoff_helper");
			
			$this->load->library('phpexcel'); 
						
			$this->load->model($this->folder_model."m_persetujuan_lembur");
			$this->load->model("lembur/m_pengajuan_lembur");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Persetujuan Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
		}
		
		public function index() {				
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."persetujuan_lembur";
			
			$this->data["bulan"] = date('Y-m');
			$this->data["month_list"] = $this->db->query('select distinct DATE_FORMAT(tgl_dws, "%Y-%m") as bln from ess_lembur_transaksi ORDER BY tgl_dws ASC')->result_array();

			if (@$this->input->get('np'))
				$this->data['get_np'] = $this->input->get('np');
			else
				$this->data['get_np'] = 0;

			if (@$this->input->get('bulan'))
				$this->data['bulan'] = $this->input->get('bulan');
			else
				$this->data['bulan'] = 0;

			$this->data["approve"] = '';
			$this->data["list_np"] = $this->m_pengajuan_lembur->get_np_approval_otoritas();
			
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_ess_lembur_sdm() {
			if ($this->input->post('np')!="0")
				$np = $this->input->post('np');
			else 
				$np = null;

			$approve = $this->input->post('status');
			$daterange = $this->input->post('daterange');

			$this->load->model($this->folder_model."M_tabel_persetujuan_lembur");
			$list 	= $this->M_tabel_persetujuan_lembur->get_datatables($approve, $daterange, $np);	
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $val) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $val->no_pokok;
				$row[] = $val->nama;
				$row[] = tanggal_indonesia($val->tgl_dws);
				$row[] = tanggal_indonesia($val->tgl_mulai).' '.date('H:i', strtotime($val->jam_mulai));
				$row[] = tanggal_indonesia($val->tgl_selesai).' '.date('H:i', strtotime($val->jam_selesai));
				$row[] = $val->alasan;
				$row[] = $val->keterangan;
				$row[] = ($val->waktu_mulai_fix==null || $val->waktu_selesai_fix==null || $val->waktu_mulai_fix=='0000-00-00 00:00:00' || $val->waktu_selesai_fix=='0000-00-00 00:00:00') ? '-' : datetime_indo($val->waktu_mulai_fix).' <br>s/d<br> '.datetime_indo($val->waktu_selesai_fix);
                                
				if($val->waktu_mulai_fix==null || $val->waktu_selesai_fix==null || $val->waktu_mulai_fix=='0000-00-00 00:00:00' || $val->waktu_selesai_fix=='0000-00-00 00:00:00') {
					$row[] = "<button class='btn btn-warning btn-xs'>Tidak Diakui</button>";
				}
				else {
					if($val->approval_status=='1') {
						$row[] = "<button class='btn btn-success btn-xs'>Disetujui SDM</button>";
					}
					else if($val->approval_status=='2') {
						$row[] = "<button class='btn btn-danger btn-xs'>Ditolak SDM</button>";
					}
					else if($val->approval_status=='0' || $val->approval_status==null || $val->approval_status=='') {
						if($val->approval_pimpinan_status=='1') {
							$row[] = "<button class='btn btn-success btn-xs'>Disetujui Atasan</button>";
						}
						else if($val->approval_pimpinan_status=='2') {
							$row[] = "<button class='btn btn-danger btn-xs'>Ditolak Atasan</button>";
						}
						else if($val->approval_pimpinan_status=='0' || $val->approval_pimpinan_status==null || $val->approval_pimpinan_status=='') {
							$row[] = "<button class='btn btn-default btn-xs'>Menunggu Persetujuan</button>";
						}
					}
					else {
						$row[] = "<button class='btn btn-danger btn-xs'>Tidak Valid</button>";
					}
				}
				
				//cutoff ERP
				$sudah_cutoff = sudah_cutoff($val->tgl_dws);
				
				
				
				if((($val->approval_status=='1' || $val->approval_status=='2') || ($val->approval_pimpinan_status=='1' || $val->approval_pimpinan_status=='2') || (!@$this->akses['persetujuan'])) || ($val->waktu_mulai_fix==null || $val->waktu_selesai_fix==null || $val->waktu_mulai_fix=='0000-00-00 00:00:00' || $val->waktu_selesai_fix=='0000-00-00 00:00:00')) {
					
					if($sudah_cutoff) //jika sudah lewat masa cutoff
					{
						$row[] = "<a class='btn btn-default btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'>Submit ERP</a>";		
					}else
					{					
						$row[] = "<a class='btn btn-default btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'>Detail</a>";						
					}
				
					
				}
				else if(@$this->akses['persetujuan']) {
					
					if($sudah_cutoff) //jika sudah lewat masa cutoff
					{
						$row[] = "<a class='btn btn-default btn-xs status_button'>Submit ERP</a>";			
						// $row[] = "<a class='btn btn-default btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'>Submit ERP</a>";			
						// $row[] = "<a class='btn btn-primary btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='' id='modal_approve'>Persetujuan</a>";					
					}else
					{					
						$row[] = "<a class='btn btn-primary btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='' id='modal_approve'>Persetujuan</a>";					
					}
					
					
				}
				//$row[] = $val->approval_status;
				$data[] = $row;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_persetujuan_lembur->count_all($approve, $daterange, $np),
					"recordsFiltered" => $this->M_tabel_persetujuan_lembur->count_filtered($approve, $daterange, $np),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}
		
		private function persetujuan($id,$approval_sdm,$approval_sdm_by) {
			//===== Log Start =====
			$arr_data_lama = $this->m_persetujuan_lembur->select_lembur_by_id($id);
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
											
			$persetujuan_lembur_sdm = $this->m_persetujuan_lembur->persetujuan_lembur_sdm($data_persetujuan);
			
			if($persetujuan_lembur_sdm!="0") {	
				$this->session->set_flashdata('success',"Aksi Persetujuan/tolak lembur oleh Anda, berhasil.");
				
				//===== Log Start =====
				$arr_data_baru = $this->m_persetujuan_lembur->select_lembur_by_id($id);
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
			$data['akses_lihat'] = $this->input->post('akses');
			$data["function"] = __FUNCTION__;
			$data["id_"] = $data["id"];
			$data["no_pokok"] = $data["no_pokok"];
			$data["nama_pegawai"] = $data["nama_pegawai"];
			$data["tgl_dws"] = $data["tgl_dws"];
			$data["tgl_mulai"] = $data["tgl_mulai"];
			$data["tgl_selesai"] = $data["tgl_selesai"];
			$data["waktu_mulai_fix"] = $data["waktu_mulai_fix"];
			$data["waktu_selesai_fix"] = $data["waktu_selesai_fix"];
			$data["jam_mulai"] = date('H:i', strtotime($data["jam_mulai"]));
			$data["jam_selesai"] = date('H:i', strtotime($data["jam_selesai"]));

			$dteStart = date_create(date('Y-m-d H:i', strtotime($data['tgl_mulai'].' '.$data["jam_mulai"])));
			$dteEnd = date_create(date('Y-m-d H:i', strtotime($data['tgl_selesai'].' '.$data["jam_selesai"])));

			$diff = date_diff($dteStart,$dteEnd);
			$hari = $diff->format("%a");
			$jam = $diff->format("%h");
			$menit = $diff->format("%i");
			if (($jam>0 || $hari>0) && $menit >=45)
				$jam = $jam+1;

			$total = ($hari*24)+($jam);
			$data['jam_diakui'] = $total;
		
			$data["alasan"] = $data["alasan"];
			$data["created_at"] = $data["created_at"];
			$kry = erp_master_data_by_np($data["created_by"], $data['tgl_dws']);
			$data["created_name"] = $kry['nama'];
			$data["created_np"] = $data["created_by"];
			$kry2 = erp_master_data_by_np($this->session->userdata('no_pokok'), $data['tgl_dws']);
			$data["sdm_name"] = $kry2['nama'];
			$data["sdm_np"] = $this->session->userdata('no_pokok');
			$data["approval_status"] = $data["approval_status"];
			$data["approval_date"] = $data["approval_date"];
			$data["approval_nama"] = $data["approval_nama"];
			$data["approval_nama_jabatan"] = $data["approval_nama_jabatan"];
			$data["approval_nama_unit"] = $data["approval_nama_unit"];
			$data["approval_np"] = $data["approval_np"];
			$data["approval_pimpinan_status"] = $data["approval_pimpinan_status"];
			$data["approval_pimpinan_date"] = $data["approval_pimpinan_date"];
			$data["approval_pimpinan_nama"] = $data["approval_pimpinan_nama"];
			$data["approval_pimpinan_nama_jabatan"] = $data["approval_pimpinan_nama_jabatan"];
			$data["approval_pimpinan_nama_unit"] = $data["approval_pimpinan_nama_unit"];
			$data["approval_pimpinan_np"] = $data["approval_pimpinan_np"];
			$data["judul"] = ucwords(preg_replace("/_/"," ",__CLASS__));
			$data["akses"] = $this->akses;
			$this->load->view($this->folder_view."approve_lembur_atasan",$data);
		}
		
		public function save() {	
			$id_ = $this->input->post('id_pengajuan');
			$pengajuan = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_pegawai_id($id_);
			$mulai = date('Y-m-d', strtotime($pengajuan['tgl_mulai'])).' '.date('H:i', strtotime($pengajuan['jam_mulai']));
			$selesai = date('Y-m-d', strtotime($pengajuan['tgl_selesai'])).' '.date('H:i', strtotime($pengajuan['jam_selesai']));
			$data['approval_pimpinan_status'] = $this->input->post('persetujuan_approval_pimpinan');
			if ($data['approval_pimpinan_status'] == '2') {
				$data['approval_pimpinan_alasan'] = $this->input->post('persetujuan_alasan_pimpinan');
			}
			else {
				$data['approval_pimpinan_alasan'] = null;
			}
			$kry = erp_master_data_by_np($this->session->userdata('no_pokok'), $pengajuan['tgl_dws']);
			$data['approval_pimpinan_date'] = date('Y-m-d H:i:s');
			$approve = $this->m_persetujuan_lembur->save_approval($id_, $data);

			if ($data['approval_pimpinan_status'] == '1') {
				$get_lembur['no_pokok'] = $pengajuan['no_pokok'];
				$get_lembur['tgl_dws'] = $pengajuan['tgl_dws'];
				$get_lembur['id'] = $id_;
				$this->m_pengajuan_lembur->set_cico($get_lembur);
			}
			if ($approve == true) {
				$this->session->set_flashdata('success', 'Berhasil Melakukan Persetujuan Lembur Kepada <b>'.$pengajuan['nama_pegawai'].' ('.$pengajuan['no_pokok'].') pada '.tanggal_waktu($mulai).' - '.tanggal_waktu($selesai).'</b>');
			}
			else {
				$this->session->set_flashdata('failed', 'Gagal Melakukan Persetujuan Lembur Kepada <b>'.$pengajuan['nama_pegawai'].' ('.$pengajuan['no_pokok'].') pada '.tanggal_waktu($mulai).' - '.tanggal_waktu($selesai).'</b>');
			}
			redirect(site_url($this->folder_controller.'persetujuan_lembur'));
		}
		
		public function approve_all() {
			$bulan = $this->input->post('bulan_tahun');
			//$kry = erp_master_data_by_np($this->session->userdata('no_pokok'), $pengajuan['tgl_dws']);
			$kry = erp_master_data_by_np($this->session->userdata('no_pokok'), $bulan.'-01');
			$data['approval_pimpinan_status'] = '1';
			$data['approval_pimpinan_np'] = $this->session->userdata('no_pokok');
			$data['approval_pimpinan_nama'] = $kry['nama'];
			$data['approval_pimpinan_nama_jabatan'] = $kry['nama_jabatan'];
			$data['approval_pimpinan_nama_unit'] = $kry['nama_unit'];
			$data['approval_pimpinan_kode_unit'] = $kry['kode_unit'];
			$data['approval_pimpinan_date'] = date('Y-m-d H:i:s');
			$approve = $this->m_persetujuan_lembur->approve_all($data, $bulan);
			
			if ($approve != false) {
				foreach ($approve->result_array() as $val) {
					$get_lembur['no_pokok'] = $val['no_pokok'];
					$get_lembur['tgl_dws'] = $val['tgl_dws'];
					$get_lembur['id'] = $val['id'];
					$this->m_pengajuan_lembur->set_cico($get_lembur);
				}
				$this->session->set_flashdata('success', 'Berhasil Melakukan Persetujuan Lembur Pada Bulan '.id_to_bulan(substr($bulan,-2)).' '.substr($bulan,0,4));
			}
			else {
				$this->session->set_flashdata('failed', 'Gagal Melakukan Persetujuan Lembur Pada Bulan '.id_to_bulan(substr($bulan,-2)).' '.substr($bulan,0,4));
			}
			redirect(site_url($this->folder_controller.'persetujuan_lembur'));
		}
		
		public function action_persetujuan_lembur_sdm_all()
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
				$query = $this->m_persetujuan_lembur->select_lembur_siap_approve_all();
				foreach ($query->result_array() as $data) 
				{				
					$id				= $data['id'];
					$approval_sdm	= '1';				
					$approval_sdm_by= $_SESSION["no_pokok"];
					
					$this->persetujuan($id,$approval_sdm,$approval_sdm_by);
				}
				
				redirect(base_url($this->folder_controller.'persetujuan_lembur'));			
				
			}else
			{					
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'persetujuan_lembur'));	
			}	
		}
		
	 	public function export(){
	        ob_start();
	        $set['np_karyawan'] = $this->input->post('np_karyawan');
	        $set['bulan'] = $this->input->post('filter_tgl');
	        $data = $this->m_persetujuan_lembur->getDataCetak($set);
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);
	        header("Content-type: application/vnd.ms-excel");\
	        header("Content-Disposition: attachment; filename=Persetujuan_lembur.xlsx");
	        header('Cache-Control: max-age=0');
	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_persetujuan_lembur.xlsx');

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

	        $excel->getActiveSheet()->setCellValueExplicit('A1', 'Persetujuan Lembur '.id_to_bulan(substr($set['bulan'],0,2)).' '.substr($set['bulan'],-4), PHPExcel_Cell_DataType::TYPE_STRING);
	        foreach ($data as $row) {
	        	$waktu = explode(':', $row['waktu']);
	        	$jml_jam=$waktu[0];
	        	$jml_menit=$waktu[1];
	        	$jml_detik=$waktu[2];
	            $excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($row['no_pokok']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, strtoupper($row['nama']), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, $jml_jam, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('E'.$awal, $jml_menit, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('F'.$awal, $jml_detik, PHPExcel_Cell_DataType::TYPE_STRING);
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
	
	/* End of file persetujuan_lembur_sdm.php */
	/* Location: ./application/controllers/lembur/persetujuan_lembur_sdm.php */