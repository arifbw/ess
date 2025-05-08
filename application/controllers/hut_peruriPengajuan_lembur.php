<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pengajuan_lembur extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'lembur/';
			$this->folder_model = 'lembur/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			
			$this->akses = array();

			$this->load->helper("karyawan_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("cutoff_helper");

			$this->load->model($this->folder_model."/m_pengajuan_lembur");
			$this->load->model($this->folder_model."/m_tabel_pengajuan_lembur");
			$this->load->library("pdf");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		

		public function index() {				
			$this->data['judul'] = "Pengajuan Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pengajuan_lembur";
			array_push($this->data['js_sources'],"lembur/pengajuan_lembur");
			$this->data["bulan"] = date('Y-m');
			
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
											
			if($list_kode_unit==false)
			{
				$this->data["month_list"] = $this->db->query("select distinct DATE_FORMAT(tgl_dws, '%Y-%m') as bln from ess_lembur_transaksi order by bln desc")->result_array();
			}else
			{
				if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
				{					
					$this->data["month_list"] = $this->db->query("select distinct DATE_FORMAT(tgl_dws, '%Y-%m') as bln from ess_lembur_transaksi where kode_unit IN ('".$list_kode_unit."') order by bln desc")->result_array();
				}else
				if($_SESSION["grup"]==5) //pengguna
				{
					$np_karyawan = $_SESSION["no_pokok"];
					$this->data["month_list"] = $this->db->query("select distinct DATE_FORMAT(tgl_dws, '%Y-%m') as bln from ess_lembur_transaksi where no_pokok='$np_karyawan' order by bln desc")->result_array();
				}else
				{
					$this->data["month_list"] = $this->db->query("select distinct DATE_FORMAT(tgl_dws, '%Y-%m') as bln from ess_lembur_transaksi order by bln desc")->result_array();
				}
			}
			
			
			$this->data["list_np"] = $this->m_pengajuan_lembur->get_np();

			if($this->input->post()){
				if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					$this->data['id'] = $this->input->post("id_pengajuan_lembur");
					$this->data['no_pokok'] = $this->input->post("no_pokok");
					$this->data['approval_pimpinan_np'] = $this->input->post("np_approver");
					$this->data['no_pokok_ubah'] = $this->input->post("no_pokok_ubah");
					$this->data['approval_pimpinan_np_ubah'] = $this->input->post("np_approver_ubah");
					$this->data['tgl_dws_ubah'] = $this->input->post("tgl_dws_ubah");
					$this->data['tgl_dws'] = $this->input->post("tgl_dws");
					$kry = erp_master_data_by_np($this->data['no_pokok_ubah'], $this->data['tgl_dws']);
					$apv = erp_master_data_by_np($this->data['approval_pimpinan_np_ubah'], $this->data['tgl_dws']);
					$this->data['nama'] = $kry['nama'];
					$this->data['nama_jabatan'] = $kry['nama_jabatan'];
					$this->data['nama_unit'] = $kry['nama_unit'];
					$this->data['kode_unit'] = $kry['kode_unit'];
					$this->data['approval_pimpinan_nama'] = $apv['nama'];
					$this->data['approval_pimpinan_nama_jabatan'] = $apv['nama_jabatan'];
					$this->data['approval_pimpinan_nama_unit'] = $apv['nama_unit'];
					$this->data['approval_pimpinan_kode_unit'] = $apv['kode_unit'];
					$this->data['personel_number'] = $kry['personnel_number'];
					$this->data['tgl_mulai'] = $this->input->post("tgl_mulai");
					$this->data['tgl_selesai'] = $this->input->post("tgl_selesai");
					$this->data['jam_mulai'] = $this->input->post("jam_mulai");
					$this->data['jam_selesai'] = $this->input->post("jam_selesai");
					$this->data['tgl_mulai_ubah'] = $this->input->post("tgl_mulai_ubah");
					$this->data['tgl_selesai_ubah'] = $this->input->post("tgl_selesai_ubah");
					$this->data['jam_mulai_ubah'] = $this->input->post("jam_mulai_ubah");
					$this->data['jam_selesai_ubah'] = $this->input->post("jam_selesai_ubah");

					$ubah = $this->ubah($this->data);
					if($ubah['status'] == true){
						$this->session->set_flashdata('success', 'Perubahan Data Lembur <b>Berhasil</b> Dilakukan.');
					}
					else{
						$this->session->set_flashdata('warning', $ubah['error_info']);
					}

					$this->data['no_pokok'] = "";
					$this->data['approver_pimpinan_np'] = "";
					$this->data['tgl_dws'] = "";
					$this->data['tgl_mulai'] = "";
					$this->data['tgl_selesai'] = "";
					$this->data['jam_mulai'] = "";
					$this->data['jam_selesai'] = "";
				}
				else{
					$this->data['no_pokok'] = "";
					$this->data['approver_pimpinan_np'] = "";
					$this->data['tgl_dws'] = "";
					$this->data['tgl_mulai'] = "";
					$this->data['tgl_selesai'] = "";
					$this->data['jam_mulai'] = "";
					$this->data['jam_selesai'] = "";
				}
			}
			else{
				$this->data['no_pokok'] = "";
				$this->data['approver_pimpinan_np'] = "";
				$this->data['tgl_dws'] = "";
				$this->data['tgl_mulai'] = "";
				$this->data['tgl_selesai'] = "";
				$this->data['jam_mulai'] = "";
				$this->data['jam_selesai'] = "";
			}
            
            $array_daftar_karyawan	= $this->m_pengajuan_lembur->select_daftar_karyawan();
			$array_daftar_unit		= $this->m_pengajuan_lembur->select_daftar_unit();
			
            $this->data['array_daftar_karyawan']	= $array_daftar_karyawan;	
			$this->data['array_daftar_unit'] 		= $array_daftar_unit;
			
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_ess_lembur() {		
			$tgl = $this->input->post('bln');
			$this->data['judul'] = "Pengajuan Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			$this->load->model($this->folder_model."M_tabel_pengajuan_lembur");
			$list 	= $this->M_tabel_pengajuan_lembur->get_datatables($tgl);	
			$data = array();
			$no = $_POST['start'];
			
			$i = 0;
			foreach ($list as $val) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $val->no_pokok;
				$row[] = $val->nama;
				$row[] = tanggal_indonesia($val->tgl_dws);
				$row[] = tanggal_indonesia($val->tgl_mulai).' '.date('H:i', strtotime($val->jam_mulai));
				$row[] = tanggal_indonesia($val->tgl_selesai).' '.date('H:i', strtotime($val->jam_selesai));
				$row[] = ($val->waktu_mulai_fix==null || $val->waktu_selesai_fix==null || $val->waktu_mulai_fix=='' || $val->waktu_selesai_fix=='') ? '-' : datetime_indo($val->waktu_mulai_fix).' <br>s/d<br> '.datetime_indo($val->waktu_selesai_fix);
				
				if($val->waktu_mulai_fix==null || $val->waktu_selesai_fix==null || $val->waktu_mulai_fix=='' || $val->waktu_selesai_fix=='' || $val->waktu_mulai_fix=='00:00:00' || $val->waktu_selesai_fix=='00:00:00') {
					$row[] = "<button class='btn btn-warning btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'> Tidak Diakui</button>";
				}
				else if($val->approval_status=='1') {
					$row[] = "<button class='btn btn-success btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'> Disetujui SDM</button>";
				}
				else if($val->approval_status=='2') {
					$row[] = "<button class='btn btn-danger btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'> Ditolak SDM</button>";
				}
				else if($val->approval_status=='0' || $val->approval_status==null || $val->approval_status=='') {
					if($val->approval_pimpinan_status=='1') {
						$row[] = "<button class='btn btn-default btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'> Disetujui Atasan</button>";
					}
					else if($val->approval_pimpinan_status=='2') {
						$row[] = "<button class='btn btn-default btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'> Ditolak Atasan</button>";
					}
					else if($val->approval_pimpinan_status=='0' || $val->approval_pimpinan_status==null || $val->approval_pimpinan_status=='') {
						$row[] = "<button class='btn btn-default btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'> Menunggu Persetujuan</button>";
					}
				}
				else {
					$row[] = "<button class='btn btn-danger btn-xs status_button' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'> Tidak Valid</button>";
				}

				if ($this->akses["ubah"] && ($val->approval_pimpinan_status=='0' || $val->approval_pimpinan_status==null) && (($val->approval_status=='0' || $val->approval_status==null) || $val->waktu_mulai_fix==null || $val->waktu_selesai_fix==null || $val->waktu_mulai_fix=='' || $val->waktu_selesai_fix=='' || $val->waktu_mulai_fix=='00:00:00' || $val->waktu_selesai_fix=='00:00:00')) {
					$aksi1 = "<input type='hidden' name='id_pengajuan_lembur_$i' value='".$val->id."'/><button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='ubah(this)'>Ubah</button>";
				}
				else {
					$aksi1 = "";
				}
				if ($this->akses["hapus"] && (($val->approval_status=='0' || $val->approval_status==null) || $val->waktu_mulai_fix==null || $val->waktu_selesai_fix==null || $val->waktu_mulai_fix=='' || $val->waktu_selesai_fix=='' || $val->waktu_mulai_fix=='00:00:00' || $val->waktu_selesai_fix=='00:00:00')) {
					$aksi2 = "<button class='btn btn-danger btn-xs' onclick='hapus(".$val->id.")'>Hapus</button>";
				}
				else {
					$aksi2 = "";
				}
				
				//cutoff ERP
				$sudah_cutoff = sudah_cutoff($val->tgl_dws);
								
				if ($aksi1 == "" && $aksi2 == "") {
					
					if($sudah_cutoff) //jika sudah lewat masa cutoff
					{
						$row[] = "<button class='btn btn-default btn-xs' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'>Submit ERP</button>";
					}else
					{					
						$row[] = "<button class='btn btn-default btn-xs' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'>Detail</button>"."<button class='btn btn-danger btn-xs' onclick='hapus(".$val->id.")'>Hapus</button>";			
					}
				
					
				}
				else {
					
					if($sudah_cutoff) //jika sudah lewat masa cutoff
					{
						$row[] = "<button class='btn btn-default btn-xs' data-id-pengajuan='".$val->id."' data-akses='lihat' id='modal_approve'>Submit ERP</button>";
					}else
					{					
						$row[] = $aksi1.$aksi2;		
					}
					
					
				}

				$data[] = $row;
				$i++;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_pengajuan_lembur->count_all($tgl),
					"recordsFiltered" => $this->M_tabel_pengajuan_lembur->count_filtered($tgl),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}
		
		public function input_pengajuan_lembur(){	
			
			//if(isset ($_SERVER["HTTP_REFERER"]) and strcmp(base_url($url),substr($_SERVER["HTTP_REFERER"],0,strlen(base_url($url))))==0){
			$this->data['menu'] = "Pengajuan Lembur";
			$this->data['id_menu'] = $this->m_setting->ambil_id_modul($this->data['menu']);
			$this->data['judul'] = "Input Pengajuan Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
		
			$this->data["navigasi_menu"] = menu_helper();
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			//$this->data['judul'] .= " : Input Pengajuan Lembur";
			//$this->data['id_menu'] = $cek["id"];
			
			$list_np = $this->m_pengajuan_lembur->get_np();
			$this->data["list_np"] = '<option></option>';
			foreach ($list_np as $val) {
				$this->data["list_np"] .= '<option value=\"'.$val['no_pokok'].'\">'.$val['no_pokok'].' - '.str_replace("'", " ", $val['nama']).'</option>';
			}
			
			$arr_unit_kerja = array();
			
			$np_karyawan = "";
			
			if(strcmp($this->session->userdata("grup"),"4")==0){
				foreach($this->session->userdata("list_pengadministrasi") as $kode_unit_administrasi){
					if(strcmp(substr($kode_unit_administrasi["kode_unit"],1,1),"0")==0){
						array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,3));
					}
					else{
						array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,2));
					}
				}
			}
			else if(strcmp($this->session->userdata("grup"),"5")==0){
				if(strcmp(substr($this->session->userdata("kode_unit"),1,1),"0")==0){
					array_push($arr_unit_kerja,substr($this->session->userdata("kode_unit"),0,3));
				}
				else{
					array_push($arr_unit_kerja,substr($this->session->userdata("kode_unit"),0,2));
				}
				$np_karyawan = $this->session->userdata("no_pokok");
			}
			
			$arr_unit_kerja = array_unique($arr_unit_kerja);

			$list_apv = $this->m_pengajuan_lembur->get_apv($arr_unit_kerja,$np_karyawan);
			$this->data["list_apv"] = '<option></option>';
			foreach ($list_apv as $val) {
				$this->data["list_apv"] .= '<option value=\"'.$val['no_pokok'].'\">'.$val['no_pokok'].' - '.str_replace("'", " ", $val['nama']).'</option>';
			}
			
			$list_unit_kerja = $this->m_pengajuan_lembur->get_unit_kerja();
			$this->data["list_unit_kerja"] = '<option></option>';
			foreach ($list_unit_kerja as $val) {
				$this->data["list_unit_kerja"] .= '<option value=\"'.$val['kode_unit'].'\">'.$val['kode_unit'].' - '.$val['nama_unit'].'</option>';
			}
			
			$_SERVER["PHP_SELF"] = substr_replace($_SERVER["PHP_SELF"],"pengajuan_lembur",strpos($_SERVER["PHP_SELF"],__FUNCTION__));

			$this->data['content'] = $this->folder_view."input_pengajuan_lembur";
			//array_push($this->data['js_sources'],"administrator/isi_menu");
			
			$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $this->data['id_menu'],
					"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__FUNCTION__))." : Input Pengajuan Lembur",
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
			$this->m_log->tambah($log);
			
			$this->load->view('template',$this->data);
			// }
			// else{
			// 	redirect(base_url($url));
			// }

		}

		public function save_input_pengajuan_lembur(){	
			if($this->input->post()){
				$this->data['menu'] = "Pengajuan Lembur";
				$this->data['id_menu'] = $this->m_setting->ambil_id_modul($this->data['menu']);
				$this->data['judul'] = "Input Pengajuan Lembur";
				$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
				$this->akses = akses_helper($this->data['id_modul']);
				//var_dump($this->input->post());
				
				$arr_no_pokok    = $this->input->post("no_pokok");
				$arr_np_approver = $this->input->post("np_approver");
				$arr_tgl_dws   	 = $this->input->post("tgl_dws");
				$arr_tgl_mulai   = $this->input->post("tgl_mulai");
				$arr_tgl_selesai = $this->input->post("tgl_selesai");
				$arr_jam_mulai   = $this->input->post("jam_mulai");
				$arr_jam_selesai = $this->input->post("jam_selesai");
				$created_by = $this->session->userdata("no_pokok");
				$created_at = date("Y-m-d H:i:s");
			
				for($i=0;$i<count($arr_no_pokok);$i++){
					$kry = erp_master_data_by_np($arr_no_pokok[$i], $arr_tgl_dws[$i]);
					$apv = erp_master_data_by_np($arr_np_approver[$i], $arr_tgl_dws[$i]);
					$data[$i] = array("no_pokok"=>$arr_no_pokok[$i],"nama"=>$kry['nama'],"nama_jabatan"=>$kry['nama_jabatan'],"nama_unit"=>$kry['nama_unit'],"kode_unit"=>$kry['kode_unit'],"approval_pimpinan_np"=>$arr_np_approver[$i],"approval_pimpinan_nama"=>$apv['nama'],"approval_pimpinan_nama_jabatan"=>$apv['nama_jabatan'],"approval_pimpinan_nama_unit"=>$apv['nama_unit'],"approval_pimpinan_kode_unit"=>$apv['kode_unit'],"personel_number"=>$kry['personnel_number'],"tgl_dws"=>$arr_tgl_dws[$i],"tgl_mulai"=>$arr_tgl_mulai[$i],"tgl_selesai"=>$arr_tgl_selesai[$i],"jam_mulai"=>$arr_jam_mulai[$i],"jam_selesai"=>$arr_jam_selesai[$i],"created_at"=>$created_at,"created_by"=>$created_by);
				}
				
				$tambah = $this->tambah($data);
				if($tambah['status'] == true){
					$this->session->set_flashdata('success', 'Penambahan Data Lembur <b>Berhasil</b> Dilakukan.');
				}
				else{
					$this->session->set_flashdata('warning', $tambah['error_info']);
				}
				redirect(site_url('lembur/pengajuan_lembur'));
			}	
		}

		private function tambah($data){
			$return = array("status" => true, "error_info" => "");
			$return["error_info"] = '';
			for($i=0;$i<count($data);$i++){

				$get_date['start_input'] = date('Y-m-d', strtotime($data[$i]['tgl_mulai'])).' '.date('H:i:s', strtotime($data[$i]['jam_mulai']));
				$get_date['end_input'] = date('Y-m-d', strtotime($data[$i]['tgl_selesai'])).' '.date('H:i:s', strtotime($data[$i]['jam_selesai']));
				$date_dws = date('Y-m-d', strtotime($data[$i]['tgl_dws']));
				$plus1 = date('Y-m-d',strtotime($date_dws."+1 days"));
				$minus1 = date('Y-m-d',strtotime($date_dws."-1 days"));

				$get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($data[$i]);
				$cek_uniq_lembur = $this->m_pengajuan_lembur->cek_uniq_lembur($data[$i], null, null, null);
					
				//var_dump($cek_uniq_lembur);exit;
				if (($get_date['start_input'] < $get_date['end_input'] || (($data[$i]['tgl_mulai'] != $data[$i]['tgl_dws'] || $data[$i]['tgl_mulai'] != $plus1 || $data[$i]['tgl_mulai'] != $minus1) && ($data[$i]['tgl_selesai'] != $data[$i]['tgl_dws'] || $data[$i]['tgl_selesai'] != $plus1 || $data[$i]['tgl_selesai'] != $minus1))) && $cek_uniq_lembur['status'] == true) {
					$get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($data[$i]);

					$data[$i]['waktu_mulai_fix'] = null;
					$data[$i]['waktu_selesai_fix'] = null;
					if ((bool)$get_jadwal != false && (bool)$this->m_pengajuan_lembur->cek_dws_lembur($data[$i]) == true) {
						if ($cek_uniq_lembur['message'] == 'Not Valid') {
							$data[$i]['waktu_mulai_fix'] = $get_date['start_input'];
							$data[$i]['waktu_selesai_fix'] = $get_date['end_input'];
							$data[$i]['time_type'] = $get_jadwal['time_type'];
							//echo 'a';
						}
						else if ($cek_uniq_lembur['message'] == 'Not DWS') {
							$data[$i]['waktu_mulai_fix'] = $data[$i]['waktu_mulai_fix'];
							$data[$i]['waktu_selesai_fix'] = $data[$i]['waktu_selesai_fix'];
							$data[$i]['time_type'] = null;
							//echo 'b';
						}
						else {
							$data[$i]['waktu_selesai_fix'] = $get_jadwal['waktu_selesai_fix'];
							$data[$i]['waktu_mulai_fix'] = $get_jadwal['waktu_mulai_fix'];
							$data[$i]['time_type'] = $get_jadwal['time_type'];
							//echo 'c';
						}
						
						//jika waktu mulai >= waktu selesai
						if($data[$i]['waktu_mulai_fix']>=$data[$i]['waktu_selesai_fix']) 
						{
							$data[$i]['waktu_mulai_fix'] = null;
							$data[$i]['waktu_selesai_fix'] = null;
						}
						
						
							//var_dump($set);exit;
						//echo 'a';exit;
					}
					//var_dump($data[$i]);exit;
					//echo 'd';exit;

					$id = $this->m_pengajuan_lembur->tambah($data[$i]);

					if($id != null || $id != '') {
						$return["status"] = true;
						$arr_data_insert = $this->m_pengajuan_lembur->data_lembur($data[$i]);
						$log_data_baru = "";
						foreach($arr_data_insert as $key => $value){
							if(!empty($log_data_baru)){
								$log_data_baru .= "<br>";
							}
							$log_data_baru .= "$key = $value";
						}
						$log = array(
							"id_pengguna" => $this->session->userdata("id_pengguna"),
							"id_modul" => $this->data['id_modul'],
							"id_target" => $arr_data_insert['id'],
							"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
							"kondisi_baru" => $log_data_baru,
							"alamat_ip" => $this->data["ip_address"],
							"waktu" => date("Y-m-d H:i:s")
						);
						$this->m_log->tambah($log);
					}
					else {
						$return["status"] = false;
						$nama = nama_karyawan_by_np($data[$i]['no_pokok']);
						$return["error_info"] .= "Pengajuan Data Lembur <b>".$nama." (".$data[$i]['no_pokok'].")</b> Pada <b>".$data[$i]['tgl_mulai']." ".$data[$i]['jam_mulai']."</b> s/d <b>".$data[$i]['tgl_selesai']." ".$data[$i]['jam_selesai']."</b> <b>Gagal</b> Ditambahkan.<br>";
					}
				}
				else {
					$return["status"] = false;
					$nama = nama_karyawan_by_np($data[$i]['no_pokok']);
					$return["error_info"] .= "Pengajuan Data Lembur <b>".$nama." (".$data[$i]['no_pokok'].")</b> Pada <b>".$data[$i]['tgl_mulai']." ".$data[$i]['jam_mulai']."</b> s/d <b>".$data[$i]['tgl_selesai']." ".$data[$i]['jam_selesai']."</b> <b>Gagal</b> Ditambahkan. ".$cek_uniq_lembur['message']."<br>";
				}
			}
			return $return;
		} 
		
	 	private function ubah($data){
	 		$kry = erp_master_data_by_np($data['no_pokok'], $data['tgl_dws']);
	 		$apv = erp_master_data_by_np($data['approval_pimpinan_np_ubah'], $data['tgl_dws']);
			$set = array("no_pokok" => $data['no_pokok'], "nama" => $kry['nama'], "nama_jabatan" => $kry['nama_jabatan'], "nama_unit" => $kry['nama_unit'],"kode_unit" => $kry['kode_unit'], "approval_pimpinan_np" => $data['approval_pimpinan_np_ubah'], "approval_pimpinan_nama" => $apv['nama'], "approval_pimpinan_nama_jabatan" => $apv['nama_jabatan'], "approval_pimpinan_nama_unit" => $apv['nama_unit'], "approval_pimpinan_kode_unit" => $apv['kode_unit'], "personel_number" => $kry['personnel_number'], "tgl_dws" =>  $data['tgl_dws_ubah'], "tgl_mulai" =>  $data['tgl_mulai_ubah'], "tgl_selesai" => $data['tgl_selesai_ubah'], "jam_mulai" => $data['jam_mulai_ubah'], "jam_selesai" => $data['jam_selesai_ubah']);
			$where = array("id" => $data['id'], "no_pokok" => $data['no_pokok'], "tgl_mulai" =>  $data['tgl_mulai'], "tgl_selesai" => $data['tgl_selesai'], "jam_mulai" => $data['jam_mulai'], "jam_selesai" => $data['jam_selesai']);
			$set['updated_by']	= $this->session->userdata("no_pokok");
			$set['updated_at']	= date("Y-m-d H:i:s");
			$where_update = array("id" => $data['id']);

			$get_date['start_input'] = date('Y-m-d', strtotime($set['tgl_mulai'])).' '.date('H:i:s', strtotime($set['jam_mulai']));
			$get_date['end_input'] = date('Y-m-d', strtotime($set['tgl_selesai'])).' '.date('H:i:s', strtotime($set['jam_selesai']));
			$date_dws = date('Y-m-d', strtotime($set['tgl_dws']));
			$plus1 = date('Y-m-d',strtotime($date_dws."+1 days"));
			$minus1 = date('Y-m-d',strtotime($date_dws."-1 days"));

			$get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($set);
			$cek_uniq_lembur = $this->m_pengajuan_lembur->cek_uniq_lembur($set, $data['id'], null, null);
			//echo (int)$cek_uniq_lembur['status'];exit;
			if (($get_date['start_input'] < $get_date['end_input'] || (($set['tgl_mulai'] != $set['tgl_dws'] || $set['tgl_mulai'] != $plus1 || $set['tgl_mulai'] != $minus1) && ($set['tgl_selesai'] != $set['tgl_dws'] || $set['tgl_selesai'] != $plus1 || $data[$i]['tgl_selesai'] != $minus1))) && $cek_uniq_lembur['status'] == true) {
				$get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($set);
				
				//if((bool)$this->m_pengajuan_lembur->cek_uniq_lembur($data[$i]) == true) {
				$set['waktu_mulai_fix'] = null;
				$set['waktu_selesai_fix'] = null;
				if ((bool)$get_jadwal != false && (bool)$this->m_pengajuan_lembur->cek_dws_lembur($set) == true) {
					//var_dump($get_jadwal);exit;
					if ($cek_uniq_lembur['message'] == 'Not Valid') {
						$set['waktu_mulai_fix'] = $get_date['start_input'];
						$set['waktu_selesai_fix'] = $get_date['end_input'];
						$set['time_type'] = $get_jadwal['time_type'];
					}
					else if ($cek_uniq_lembur['message'] == 'Not DWS') {
						$set['waktu_mulai_fix'] = $set['waktu_mulai_fix'];
						$set['waktu_selesai_fix'] = $set['waktu_selesai_fix'];
						$set['time_type'] = null;
					}
					else {
						$set['waktu_selesai_fix'] = $get_jadwal['waktu_selesai_fix'];
						$set['waktu_mulai_fix'] = $get_jadwal['waktu_mulai_fix'];
						$set['time_type'] = $get_jadwal['time_type'];
					}
					//echo 'a';exit;
					
					//jika waktu mulai >= waktu selesai
					if($set['waktu_mulai_fix']>=$set['waktu_selesai_fix']) 
					{
						$set['waktu_selesai_fix'] 	= null;
						$set['waktu_mulai_fix'] 	= null;
					}
				}
				//		var_dump($get_jadwal);exit;
				//echo  $cek_uniq_lembur['status'] ;exit;
					//var_dump($cek_uniq_lembur);exit;
					
				$arr_data_lama = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_id($data['id']);
				$log_data_lama = "";
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				$this->m_pengajuan_lembur->ubah($set, $where_update);
				$return["status"] = true;
					
				$log_data_baru = "";
				foreach($set as $key => $value){
					if(!empty($log_data_baru)){
						$log_data_baru .= "<br>";
					}
					$log_data_baru .= "$key = $value";
				}
				
				$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $arr_data_lama["id"],
					"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
					"kondisi_lama" => $log_data_lama,
					"kondisi_baru" => $log_data_baru,
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
				$this->m_log->tambah($log);
				$return = array("status" => true, "success" => "Perubahan Data Lembur <b>Berhasil</b> Dilakukan.");
				// }
				// else {
				// 	$return = array("status" => false, "error_info" => "Perubahan Data Lembur <b>Gagal</b> Dilakukan.");
				// }
				
			}
			else {
				$return = array("status" => false, "error_info" => "Perubahan Data Lembur <b>Gagal</b> Dilakukan. ".$cek_uniq_lembur['message']);
			}

			return $return;
		} 
		
		public function hapus($id=null)
		{
			if($id != null) {
				$get = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_id($id);
				$hps = $this->m_pengajuan_lembur->hapus($id);
				if((bool)$hps == true) {
					$return["status"] = true;
					
					$log_data_lama = "";
					foreach($get as $key => $value){
						if(strcmp($key,"id")!=0){
							if(!empty($log_data_lama)){
								$log_data_lama .= "<br>";
							}
							$log_data_lama .= "$key = $value";
						}
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $get["id"],
						"deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => '',
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					$this->session->set_flashdata('success', 'Pengajuan Lembur <b>Berhasil</b> Dihapus.');
				}
				else {
					$this->session->set_flashdata('warning', 'Data Lembur <b>Gagal</b> Dihapus.');
				}
				redirect('lembur/pengajuan_lembur');
			}
			else {
				$this->session->set_flashdata('warning', 'Data Lembur <b>Gagal</b> Dihapus.');
				redirect('lembur/pengajuan_lembur');
			}
		}
		
		public function ajax_getNama()
		{
			$no_pokok	 = $this->input->post('vno_pokok');
			$data   	 = $this->m_pengajuan_lembur->select_pegawai($no_pokok);
			
			if ($data->num_rows() < 1 ||$data=='') {
				echo "";
			}else{
				$row = $data->row(); 
				echo $row->nama;
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

	 	public function export(){
	        ob_start();
	        $set['np_karyawan'] = $this->input->post('np_karyawan');
	        $set['tgl'] = date('Y-m-d', strtotime($this->input->post('filter_tgl')));
	        $unit = $this->m_pengajuan_lembur->ambil_unit_pegawai_tgl($set);
	        $pdf = $this->pdf->load();
	        $pdf->AddPage('P','','','','',10,10,20,10,10,10);
	        $pdf->WriteHTML('<html><head>
				<style type="text/css">
				::selection { background-color: #E13300; color: white; }
				::-moz-selection { background-color: #E13300; color: white; }
				body {
					background-color: #fff;
					margin: 40px;
					font: 13px/20px normal Helvetica, Arial, sans-serif;
					color: #4F5155;
					font-size: 10px;
				}
				code {
					font-family: Consolas, Monaco, Courier New, Courier, monospace;
					font-size: 12px;
					background-color: #f9f9f9;
					border: 1px solid #D0D0D0;
					color: #002166;
					display: block;
					margin: 14px 0 14px 0;
					padding: 12px 10px 12px 10px;
				}
				#body {
					margin: 0 15px 0 15px;
				}
				table.trueTable {
					width: 100%;
					border-collapse: collapse;
				}
				table.headTable {
					width: 100%;
				}
				table.trueTable, td, th {
				    border: 1px solid black;
				    padding: 8px;
				}
				table.headTable, td.headTable, th.headTable {
				    border: 0;
				    padding: 0;
				}
				td.footTable, th.footTable {
					padding-bottom: 75px;
					text-align: center;
				}
				@media print {
				  	footer {page-break-after: always;}
				}
				</style>
			</head>
			<body>');
	  			foreach ($unit as $ut) {
				$pdf->WriteHTML('<div id="body">
				<center><h3 style="padding-bottom: 10px; text-align: center"><b>DAFTAR LEMBUR<br></b></h3></center>
					<table class="headTable">
						<tr class="headTable">
							<td class="headTable">SEKSI</td>
							<td class="headTable">:</td>
							<td class="headTable">'.$ut['nama_unit'].'</td>
							<td class="headTable"></td>
							<td class="headTable">Tanggal</td>
							<td class="headTable">:</td>
							<td class="headTable">'.date('d-M-Y', strtotime($set['tgl'])).'</td>
						</tr>
						<tr class="headTable">
							<td class="headTable">KODE BAGAN</td>
							<td class="headTable">:</td>
							<td class="headTable">'.$ut['kode_unit'].'</td>
							<td class="headTable"></td>
							<td class="headTable">Hari</td>
							<td class="headTable">:</td>
							<td class="headTable">'.tanggal_ke_hari(date('Y-m-d', strtotime($set['tgl']))).'</td>
						</tr>
					</table>
					<br>
					<table class="trueTable">
						<tr>
							<th rowspan="2" style="width:5%">NO</th>
							<th rowspan="2" style="width:30%">NAMA</th>
							<th rowspan="2" style="width:10%">NP</th>
							<th colspan="2" style="width:20%">JAM LEMBUR</th>
							<th rowspan="2" style="width:15%">JUMLAH UANG</td>
							<th rowspan="2" style="width:20%">Tanda Tangan</th>
						</tr>
						<tr>
							<th style="width:20%">DARI</th>
							<th style="width:20%">SAMPAI</td>
						</tr>');
		        $data = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_pegawai_tgl($set, $ut['kode_unit']);
		        foreach ($data as $val) {
			        $no = 1;
			        $mulai = date('h:i A', strtotime($val['waktu_mulai_fix']));
			        $selesai = date('h:i A', strtotime($val['waktu_selesai_fix']));

			        $pdf->WriteHTML('<tr>
							<td style="text-align: center">'.$no++.'</td>
							<td>'.$val['nama_pegawai'].'</td>
							<td style="text-align: center">'.$val['no_pokok'].'</td>
							<td style="text-align: center">'.$mulai.'</td>
							<td style="text-align: center">'.$selesai.'</td>
							<td style="text-align: right"></td>
							<td></td>
						</tr>');
			    }
				$pdf->WriteHTML('<tr>
							<td colspan="2" style="text-align: center">Jumlah</td>
							<td></td>
							<td></td>
							<td></td>
							<td style="text-align: right"></td>
							<td></td>
						</tr>
					</table>

					<p style="text-align: right; padding-bottom: -10px">'.date('d-M-Y', strtotime($set['tgl'])).'</p>

					<table class="trueTable">
						<tr>
							<td class="footTable" style="width:20%">Diajukan Oleh :<br>Kasek</td>
							<td class="footTable" style="width:20%">Disetujui oleh : Kadep</td>
							<td class="footTable" style="width:20%">Lembur<br>dicek oleh<br>Seksi Manfik</td>
							<td class="footTable" style="width:20%">Acc<br>Kadep,<br>Keuangan</td>
							<td class="footTable" style="width:20%">Tanda<br>terima<br>uang</td>
						</tr>
					</table>

					<p>Dikuasakan kepada : <br>
					untuk menerima uang lembur dari Bagian Kas <br><br>
					Tembusan : <br>
					Seksi Yanum</p>
				</div>
				<footer></footer>');
			}
			$pdf->WriteHTML('</div>
			</body>
			</html>');
	        $pdf->Output('DAFTAR LEMBUR.pdf', 'I');
		}
		
		public function export_unit(){
	        ob_start();
			$set['kode_unit'] = $this->input->post('kode_unit');
	        $set['tgl'] = date('Y-m-d', strtotime($this->input->post('filter_tgl')));
			
		 $unit = $this->m_pengajuan_lembur->ambil_unit_pegawai_tgl_unit($set);
	        $pdf = $this->pdf->load();
	        $pdf->AddPage('P','','','','',10,10,20,10,10,10);
	        $pdf->WriteHTML('<html><head>
				<style type="text/css">
				::selection { background-color: #E13300; color: white; }
				::-moz-selection { background-color: #E13300; color: white; }
				body {
					background-color: #fff;
					margin: 40px;
					font: 13px/20px normal Helvetica, Arial, sans-serif;
					color: #4F5155;
					font-size: 10px;
				}
				code {
					font-family: Consolas, Monaco, Courier New, Courier, monospace;
					font-size: 12px;
					background-color: #f9f9f9;
					border: 1px solid #D0D0D0;
					color: #002166;
					display: block;
					margin: 14px 0 14px 0;
					padding: 12px 10px 12px 10px;
				}
				#body {
					margin: 0 15px 0 15px;
				}
				table.trueTable {
					width: 100%;
					border-collapse: collapse;
				}
				table.headTable {
					width: 100%;
				}
				table.trueTable, td, th {
				    border: 1px solid black;
				    padding: 8px;
				}
				table.headTable, td.headTable, th.headTable {
				    border: 0;
				    padding: 0;
				}
				td.footTable, th.footTable {
					padding-bottom: 75px;
					text-align: center;
				}
				@media print {
				  	footer {page-break-after: always;}
				}
				</style>
			</head>
			<body>');
	  			foreach ($unit as $ut) {
				$pdf->WriteHTML('<div id="body">
				<center><h3 style="padding-bottom: 10px; text-align: center"><b>DAFTAR LEMBUR<br></b></h3></center>
					<table class="headTable">
						<tr class="headTable">
							<td class="headTable">SEKSI</td>
							<td class="headTable">:</td>
							<td class="headTable">'.$ut['nama_unit'].'</td>
							<td class="headTable"></td>
							<td class="headTable">Tanggal</td>
							<td class="headTable">:</td>
							<td class="headTable">'.date('d-M-Y', strtotime($set['tgl'])).'</td>
						</tr>
						<tr class="headTable">
							<td class="headTable">KODE BAGAN</td>
							<td class="headTable">:</td>
							<td class="headTable">'.$ut['kode_unit'].'</td>
							<td class="headTable"></td>
							<td class="headTable">Hari</td>
							<td class="headTable">:</td>
							<td class="headTable">'.tanggal_ke_hari(date('Y-m-d', strtotime($set['tgl']))).'</td>
						</tr>
					</table>
					<br>
					<table class="trueTable">
						<tr>
							<th rowspan="2" style="width:5%">NO</th>
							<th rowspan="2" style="width:30%">NAMA</th>
							<th rowspan="2" style="width:10%">NP</th>
							<th colspan="2" style="width:20%">JAM LEMBUR</th>
							<th rowspan="2" style="width:15%">JUMLAH UANG</td>
							<th rowspan="2" style="width:20%">Tanda Tangan</th>
						</tr>
						<tr>
							<th style="width:20%">DARI</th>
							<th style="width:20%">SAMPAI</td>
						</tr>');
		        $data = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_pegawai_tgl_unit($set, $ut['kode_unit']);
		        foreach ($data as $val) {
			        $no = 1;
			        $mulai = date('h:i A', strtotime($val['waktu_mulai_fix']));
			        $selesai = date('h:i A', strtotime($val['waktu_selesai_fix']));

			        $pdf->WriteHTML('<tr>
							<td style="text-align: center">'.$no++.'</td>
							<td>'.$val['nama_pegawai'].'</td>
							<td style="text-align: center">'.$val['no_pokok'].'</td>
							<td style="text-align: center">'.$mulai.'</td>
							<td style="text-align: center">'.$selesai.'</td>
							<td style="text-align: right"></td>
							<td></td>
						</tr>');
			    }
				$pdf->WriteHTML('<tr>
							<td colspan="2" style="text-align: center">Jumlah</td>
							<td></td>
							<td></td>
							<td></td>
							<td style="text-align: right"></td>
							<td></td>
						</tr>
					</table>

					<p style="text-align: right; padding-bottom: -10px">'.date('d-M-Y', strtotime($set['tgl'])).'</p>

					<table class="trueTable">
						<tr>
							<td class="footTable" style="width:20%">Diajukan Oleh :<br>Kasek</td>
							<td class="footTable" style="width:20%">Disetujui oleh : Kadep</td>
							<td class="footTable" style="width:20%">Lembur<br>dicek oleh<br>Seksi Manfik</td>
							<td class="footTable" style="width:20%">Acc<br>Kadep,<br>Keuangan</td>
							<td class="footTable" style="width:20%">Tanda<br>terima<br>uang</td>
						</tr>
					</table>

					<p>Dikuasakan kepada : <br>
					untuk menerima uang lembur dari Bagian Kas <br><br>
					Tembusan : <br>
					Seksi Yanum</p>
				</div>
				<footer></footer>');
			}
			$pdf->WriteHTML('</div>
			</body>
			</html>');
	        $pdf->Output('DAFTAR LEMBUR.pdf', 'I');
		}
	
		public function ajax_getAtasanLembur(){
            echo $this->getAtasanLembur($this->input->post('vnp_karyawan'));
		}
		
		private function getAtasanLembur($np_karyawan){
			$this->load->model("master_data/m_karyawan");
			$karyawan = $this->m_karyawan->get_posisi_karyawan($np_karyawan);
			
			if(empty($karyawan) or empty($karyawan["kode_unit"])){
				$periode = date("Y_m");
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				
				if(empty($karyawan) or empty($karyawan["kode_unit"])){
					$periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
					$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				}
			}
			
			if(strcmp($karyawan["jabatan"],"kepala")==0){
				$kode_unit_atasan = str_pad(substr($karyawan["kode_unit"],0,strlen($karyawan["kode_unit"])-1),5,0);
			}
			else{
				$kode_unit_atasan = str_pad($karyawan["kode_unit"],5,0);
			}
			
			$kode_unit_atasan = str_pad(substr($kode_unit_atasan,0,4),5,0);
			
			do{
				$np_atasan = $this->m_karyawan->get_atasan($kode_unit_atasan);
				
				$kode_unit_atasan = preg_replace("/0+$/","",$kode_unit_atasan);
				$kode_unit_atasan = str_pad(substr($kode_unit_atasan,0,strlen($kode_unit_atasan)-1),5,"0");
				
			}while(empty($np_atasan) and strlen(preg_replace("/0+$/","",$kode_unit_atasan))>1);
			
			return $np_atasan;
		}
		
		public function ajax_getPilihanAtasanLembur(){
			$np_karyawan = $this->input->post('vnp_karyawan');
			//$np_karyawan = $vnp_karyawan;
			$this->load->model("master_data/m_karyawan");
			$karyawan = $this->m_karyawan->get_posisi_karyawan($np_karyawan);

			if(empty($karyawan) or empty($karyawan["kode_unit"])){
				$periode = date("Y_m");
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				if(empty($karyawan) or empty($karyawan["kode_unit"])){
					$periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
					$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				}
			}

			if(strcmp(substr($karyawan["kode_unit"],1,1),"0")==0){
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,3);
			}
			else{
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,2);
			}
			
            $arr_pilihan = $this->m_pengajuan_lembur->get_apv(array($karyawan["kode_unit"]),$np_karyawan);
            //$arr_pilihan = $this->m_pengajuan_lembur->get_apv(array($karyawan["kode_unit"]),$this->input->post('vnp_karyawan'));
			echo json_encode($arr_pilihan);
		}
		
		public function ajax_getKaryawanUnitKerjaLembur(){
			$kode_unit	= $this->input->post('vkode_unit');
			
			$this->load->model("master_data/m_karyawan");
			$arr_karyawan = $this->m_karyawan->get_karyawan_unit_kerja($kode_unit);
			
			for($i=0;$i<count($arr_karyawan);$i++){
				$arr_karyawan[$i]["np_atasan"] = $this->getAtasanLembur($arr_karyawan[$i]["no_pokok"]);
			}
			
			echo json_encode($arr_karyawan);
		}
	}
	
	
	
	/* End of file Pengajuan_lembur.php */
	/* Location: ./application/controllers/master_data/jadwal_kerja.php */