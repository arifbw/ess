
<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Cuti_besar extends CI_Controller {
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

			$this->load->model($this->folder_model."/m_pengajuan_lembur");
			$this->load->model($this->folder_model."/m_tabel_pengajuan_lembur");
			$this->load->model($this->folder_model."M_tabel_mst_karyawan");
			$this->load->model("master_data/m_karyawan");
			$this->load->model("master_data/m_cuti_bersama");
			$this->load->library("pdf");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index() {				
			$this->data['judul'] = "Cuti Bersama";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."cuti_besar";
			array_push($this->data['js_sources'],"lembur/pengajuan_lembur");
			$this->data["bulan"] = date('Y-m');
			$this->data["month_list"] = $this->db->query('select distinct DATE_FORMAT(tgl_dws, "%Y-%m") as bln from ess_lembur_transaksi')->result_array();
			$this->data["daftar_cuti"] = $this->m_cuti_bersama->daftar_cuti_bersama_tahun(date('Y'));
			$this->data["daftar_tahun"] = $this->m_cuti_bersama->ambil_tahun();
			$this->load->view('template', $this->data);
		}

		public function header_ess_cuti($tahun){
			$daftar_cuti = $this->m_cuti_bersama->daftar_cuti_bersama_tahun($tahun);

			$data = array();
			$i = 2;
			$data['html'] = '';
			$data['data'][] = array('data' => 0);
			$data['html'] .= '<th class="text-center" style="width: 50px;">No</th>';
			$data['data'][] = array('data' => 1);
			$data['html'] .= '<th class="text-center" style="width: 240px;">Karyawan</th>';
			foreach($daftar_cuti as $val_cuti){
				$data['data'][] = array('data' => $i);
				$data['html'] .= '<th class="text-center"><span title="'.$val_cuti['deskripsi'].'">'.date('d/m/Y', strtotime($val_cuti['tanggal'])).'</span></th>';
				$i++;
			}

			echo json_encode($data);
		}

		public function action_cuti_besar(){
			if($this->input->is_ajax_request()){
				header('Content-Type: application/json');

				$np = $this->input->post('np_karyawan', true);
				$tgl = $this->input->post('tgl', true);
				$cuti = $this->input->post('cuti', true);

				$cek_cuti = $this->db->select('*')->where('np_karyawan', $np)->where('tanggal_cuti_bersama', $tgl)->get('ess_cuti_bersama');
				if($cek_cuti->num_rows() > 0){
					$row = $cek_cuti->row_array();
					$data = array(
							'id' => $row['id'],
							'enum' => $cuti,
							'updated_by' => $this->session->userdata('no_pokok'),
							'updated_at' => date('Y-m-d H:i:s')
						);

					$this->db->update('ess_cuti_bersama', $data, array('id' => $data['id']));
					if($this->db->affected_rows() > 0){
						echo json_encode(array('success' => true, 'message' => 'Berhasil menyimpan data cuti karyawan NP: '.$np.' pada Tanggal: '.$tgl));
					}
					else{
						echo json_encode(array('success' => false, 'message' => 'Gagal menyimpan data cuti karyawan NP: '.$np.' pada Tanggal: '.$tgl));
					}
				}
				else{
					$data = array(
							'id' => '',
							'np_karyawan' => $np,
							'tanggal_cuti_bersama' => $tgl,
							'enum' => $cuti,
							'updated_by' => $this->session->userdata('no_pokok'),
							'updated_at' => date('Y-m-d H:i:s')
						);

					$this->db->insert('ess_cuti_bersama', $data);
					if($this->db->affected_rows() > 0){
						echo json_encode(array('success' => true, 'message' => 'Berhasil menyimpan data cuti karyawan NP: '.$np.' pada Tanggal: '.$tgl));
					}
					else{
						echo json_encode(array('success' => false, 'message' => 'Gagal menyimpan data cuti karyawan NP: '.$np.' pada Tanggal: '.$tgl));
					}
				}
			}
		}
		
		public function tabel_ess_cuti($tahun) {
			$tgl = $this->input->post('bln');
			$this->data['judul'] = "Cuti Bersama";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			$list = $this->M_tabel_mst_karyawan->get_datatables();	
			$cuti = $this->m_cuti_bersama->daftar_cuti_bersama_tahun($tahun);	
			$data = array();
			$no = $_POST['start'];
			$opsi_cuti = array('Pilih Cuti', 'Cuti Besar', 'Cuti Tahunan', 'Hutang Cuti', 'Tidak Cuti');
			
			$i = 0;
			foreach ($list as $val) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $val->no_pokok.' - '.$val->nama;
				foreach ($cuti as $val_cuti) {
					$cuti_data_bersama = $this->db->select('enum')->select('submit_erp')->where('np_karyawan', $val->no_pokok)->where('tanggal_cuti_bersama', $val_cuti['tanggal'])->get('ess_cuti_bersama')->row_array();
					// $row[] = date('d/m/Y', strtotime($val_cuti['tanggal']));
					$cuti_opsi = '';
					foreach($opsi_cuti as $opsi_cuti_key => $opsi_cuti_val){
						
						if($cuti_data_bersama['submit_erp']==1)//jika suda cutoff
						{
							if($opsi_cuti_key==$cuti_data_bersama['enum']) 
							{
								$cuti_opsi .= '<option value="'.$opsi_cuti_key.'"'.' selected>'.$opsi_cuti_val.'</option>';
							}
						}else
						{
							if($opsi_cuti_key=='0') //hilangkan opsi pilih
							{
								
							}else
							if($opsi_cuti_key=='1') //Jika cuti besar
							{
								//check jatah cuti besar
								$this->load->model("osdm/m_cuti_besar");
								$masih_cubes = $this->m_cuti_besar->cek_masih_cuti_besar($val->no_pokok);
								$cubes_menunggu_submit = $this->m_cuti_besar->cuti_besar_menunggu_submit_erp($val->no_pokok);
								
								$sisa_cubes = $masih_cubes['sisa_hari'] - $cubes_menunggu_submit['menunggu'];
								
								if($cuti_data_bersama['enum']==1) //jika sudah milih
								{
									$cuti_opsi .= '<option value="'.$opsi_cuti_key.'"'.(($cuti_data_bersama['enum'] == $opsi_cuti_key)?' selected=""': "").'>'.$opsi_cuti_val.'</option>';
								}else //jika belum
								{
									if($sisa_cubes>0) //jika masih
									{
										$cuti_opsi .= '<option value="'.$opsi_cuti_key.'"'.(($cuti_data_bersama['enum'] == $opsi_cuti_key)?' selected=""': "").'>'.$opsi_cuti_val." (sisa : ".$sisa_cubes.' hari)</option>';
									}else //jika habis
									{
										$cuti_opsi .= '<option value="'.$opsi_cuti_key.'" disabled>'.$opsi_cuti_val.' (0 hari)</option>';
									}
								}
								
								
							}else
							if($opsi_cuti_key=='3') //hilangkan opsi hutang cuti
							{
								
							}else
							if($opsi_cuti_key=='4') //hilangkan opsi tidak cuti
							{
								
							}else //default cuti tahunan
							{
								$cuti_opsi .= '<option value="'.$opsi_cuti_key.'"'.(($cuti_data_bersama['enum'] == $opsi_cuti_key)?' selected=""': ($cuti_data_bersama['enum'] ? "" : "selected")).'>'.$opsi_cuti_val.'</option>';
							}
						}
						
						
						
					}
					
					
					if($cuti_data_bersama['submit_erp']==1) //jika sudah submit maka tampil ijo
					{
						$row[] = '<select style="width:180px;background-color: #d8d8d8;"  class="form-control" onchange="change_data_cuti(this.value, \''.$val->no_pokok.'\',\''.$val_cuti['tanggal'].'\')">'.$cuti_opsi.'</select>';
					}else
					{
						$row[] = '<select style="width:180px;" class="form-control" onchange="change_data_cuti(this.value, \''.$val->no_pokok.'\',\''.$val_cuti['tanggal'].'\')">'.$cuti_opsi.'</select>';
					}
				
					
					
				}

				$data[] = $row;
				$i++;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_mst_karyawan->count_all($tgl),
					"recordsFiltered" => $this->M_tabel_mst_karyawan->count_filtered($tgl),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}/*
		
		public function input_pengajuan_lembur(){		
			if($this->input->post()){
				$this->data['menu'] = "Pengajuan Lembur";
				$this->data['id_menu'] = $this->m_setting->ambil_id_modul($this->data['menu']);
				$this->data['judul'] = "Input Pengajuan Lembur";
				$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
				$this->akses = akses_helper($this->data['id_modul']);
				//var_dump($this->input->post());
				
				$arr_no_pokok    = $this->input->post("no_pokok");
				$arr_nama = $this->input->post("nama");
				$arr_nama_jabatan = $this->input->post("nama_jabatan");
				$arr_nama_unit = $this->input->post("nama_unit");
				$arr_tgl_dws   = $this->input->post("tgl_dws");
				$arr_tgl_mulai   = $this->input->post("tgl_mulai");
				$arr_tgl_selesai = $this->input->post("tgl_selesai");
				$arr_jam_mulai   = $this->input->post("jam_mulai");
				$arr_jam_selesai = $this->input->post("jam_selesai");
				$created_by = $this->session->userdata("no_pokok");
				$created_at = date("Y-m-d H:i:s");
			
				for($i=0;$i<count($arr_no_pokok);$i++){
					$data[$i] = array("no_pokok"=>$arr_no_pokok[$i],"nama"=>nama_karyawan_by_np($arr_no_pokok[$i]),"nama_jabatan"=>nama_jabatan_by_np($arr_no_pokok[$i]),"nama_unit"=>nama_unit_by_np($arr_no_pokok[$i]),"tgl_dws"=>$arr_tgl_dws[$i],"tgl_mulai"=>$arr_tgl_mulai[$i],"tgl_selesai"=>$arr_tgl_selesai[$i],"jam_mulai"=>$arr_jam_mulai[$i],"jam_selesai"=>$arr_jam_selesai[$i],"created_at"=>$created_at,"created_by"=>$created_by);
				}
				
				$tambah = $this->tambah($data);
				if($tambah['status']){
					$this->session->set_flashdata('success', 'Penambahan Data Lembur <b>Berhasil</b> Dilakukan.');
				}
				else{
					$this->session->set_flashdata('warning', $tambah['error_info']);
				}
				redirect(site_url('lembur/pengajuan_lembur'));
			}
			
			$this->data["navigasi_menu"] = menu_helper();

			//$cek = $this->m_master_menu->ambil_id_menu($nama_menu);
			$url = $this->m_setting->ambil_url_modul("Pengajuan Lembur");
			$list_np = $this->m_pengajuan_lembur->get_np();
			$this->data["list_np"] = '<option></option>';
			foreach ($list_np as $val) {
				$this->data["list_np"] .= '<option value=\"'.$val['no_pokok'].'\">'.$val['no_pokok'].' - '.$val['nama'].'</option>';
			}
			
			if(isset ($_SERVER["HTTP_REFERER"]) and strcmp(base_url($url),substr($_SERVER["HTTP_REFERER"],0,strlen(base_url($url))))==0){
				$this->data['menu'] = "Pengajuan Lembur";
				$this->data['id_menu'] = $this->m_setting->ambil_id_modul($this->data['menu']);
				$this->data['judul'] = "Input Pengajuan Lembur";
				$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
				$this->akses = akses_helper($this->data['id_modul']);
			
				izin($this->akses["akses"]);
				
				$this->data["akses"] = $this->akses;
				//$this->data['judul'] .= " : Input Pengajuan Lembur";
				//$this->data['id_menu'] = $cek["id"];
				
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
			}
			else{
				redirect(base_url($url));
			}

		}
		
		private function tambah($data){
			$return = array("status" => false, "error_info" => "");
			$return["error_info"] = '';
			for($i=0;$i<count($data);$i++){
				$get_date['start_input'] = date('Y-m-d', strtotime($data[$i]['tgl_mulai'])).' '.date('H:i:s', strtotime($data[$i]['jam_mulai']));
				$get_date['end_input'] = date('Y-m-d', strtotime($data[$i]['tgl_selesai'])).' '.date('H:i:s', strtotime($data[$i]['jam_selesai']));
				$date_dws = date('m/d/Y', strtotime($data[$i]['tgl_dws']));
				$plus1 = date('Y-m-d',strtotime($date_dws."+1 days"));
				$minus1 = date('Y-m-d',strtotime($date_dws."-1 days"));

				$cek_uniq_lembur = $this->m_pengajuan_lembur->cek_uniq_lembur($data[$i]);
				if (($get_date['start_input'] < $get_date['end_input'] || (($data[$i]['tgl_mulai'] != $data[$i]['tgl_dws'] || $data[$i]['tgl_mulai'] != $plus1 || $data[$i]['tgl_mulai'] != $minus1) && ($data[$i]['tgl_selesai'] != $data[$i]['tgl_dws'] || $data[$i]['tgl_selesai'] != $plus1 || $data[$i]['tgl_selesai'] != $minus1))) && $cek_uniq_lembur['status'] == true) {
					$get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($data[$i]);
					
					$data[$i]['waktu_mulai_fix'] = null;
					$data[$i]['waktu_selesai_fix'] = null;
					if ((bool)$get_jadwal != false && (bool)$this->m_pengajuan_lembur->cek_dws_lembur($data[$i]) == true) {
						if ($cek_uniq_lembur['message'] != 'Success') {
							$data[$i]['waktu_selesai_fix'] = $get_date['end_input'];
							$data[$i]['waktu_mulai_fix'] = $get_date['start_input'];
							$data[$i]['time_type'] = '01';
						}
						else {
							$data[$i]['waktu_selesai_fix'] = $get_jadwal['waktu_selesai_fix'];
							$data[$i]['waktu_mulai_fix'] = $get_jadwal['waktu_mulai_fix'];
							$data[$i]['time_type'] = $get_jadwal['time_type'];
						}
							//var_dump($set);exit;
						//echo 'a';exit;
						//var_dump($cek_uniq_lembur);exit;
					}
					//var_dump($get_jadwal);exit;
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
					$return["error_info"] .= "Pengajuan Data Lembur <b>".$nama." (".$data[$i]['no_pokok'].")</b> Pada <b>".$data[$i]['tgl_mulai']." ".$data[$i]['jam_mulai']."</b> s/d <b>".$data[$i]['tgl_selesai']." ".$data[$i]['jam_selesai']."</b> <b>Gagal</b> Ditambahkan.<br>";
				}
			}
			return $return;
		} 
		
	 	private function ubah($data){
			$set = array("no_pokok" => $data['no_pokok_ubah'], "nama" => nama_karyawan_by_np($data['no_pokok_ubah']), "nama_jabatan" => nama_jabatan_by_np($data['no_pokok_ubah']), "nama_unit" => nama_unit_by_np($data['no_pokok_ubah']), "tgl_dws" =>  $data['tgl_dws_ubah'], "tgl_mulai" =>  $data['tgl_mulai_ubah'], "tgl_selesai" => $data['tgl_selesai_ubah'], "jam_mulai" => $data['jam_mulai_ubah'], "jam_selesai" => $data['jam_selesai_ubah']);
			$where = array("id" => $data['id'], "no_pokok" => $data['no_pokok'], "tgl_mulai" =>  $data['tgl_mulai'], "tgl_selesai" => $data['tgl_selesai'], "jam_mulai" => $data['jam_mulai'], "jam_selesai" => $data['jam_selesai']);
			$set['updated_by']	= $this->session->userdata("no_pokok");
			$set['updated_at']	= date("Y-m-d H:i:s");
			$where_update = array("id" => $data['id']);

			$get_date['start_input'] = date('Y-m-d', strtotime($set['tgl_mulai'])).' '.date('H:i:s', strtotime($set['jam_mulai']));
			$get_date['end_input'] = date('Y-m-d', strtotime($set['tgl_selesai'])).' '.date('H:i:s', strtotime($set['jam_selesai']));
			$date_dws = date('m/d/Y', strtotime($data[$i]['tanggal_dws']));
			$plus1 = date('Y-m-d',strtotime($date_dws."+1 days"));
			$minus1 = date('Y-m-d',strtotime($date_dws."-1 days"));

			if ($get_date['start_input'] < $get_date['end_input'] || (($data[$i]['tgl_mulai'] != $data[$i]['tgl_dws'] || $data[$i]['tgl_mulai'] != $plus1 || $data[$i]['tgl_mulai'] != $minus1) && ($data[$i]['tgl_selesai'] != $data[$i]['tgl_dws'] || $data[$i]['tgl_selesai'] != $plus1 || $data[$i]['tgl_selesai'] != $minus1))) {
				$get_jadwal = $this->m_pengajuan_lembur->cek_valid_lembur($set);
				$cek_uniq_lembur = $this->m_pengajuan_lembur->cek_uniq_lembur($set, $data['id']);

				//if((bool)$this->m_pengajuan_lembur->cek_uniq_lembur($data[$i]) == true) {
				$set['waktu_mulai_fix'] = null;
				$set['waktu_selesai_fix'] = null;
				if ((bool)$get_jadwal != false && (bool)$this->m_pengajuan_lembur->cek_dws_lembur($set) == true && $cek_uniq_lembur['status'] == true) {
					if ($cek_uniq_lembur['message'] != 'Success') {
						$set['waktu_selesai_fix'] = $get_date['end_input'];
						$set['waktu_mulai_fix'] = $get_date['start_input'];
						$set['time_type'] = '01';
					}
					else {
						$set['waktu_selesai_fix'] = $get_jadwal['waktu_selesai_fix'];
						$set['waktu_mulai_fix'] = $get_jadwal['waktu_mulai_fix'];
						$set['time_type'] = $get_jadwal['time_type'];
					}
						//var_dump($set);exit;
					//echo 'a';exit;
				}
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
				$return = array("status" => false, "error_info" => "Perubahan Data Lembur <b>Gagal</b> Dilakukan.");
			}

			return $return;
		} 
		
		public function hapus($id=null)
		{
			if($id != null) {
				$get = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_id($id);
				
				if($this->m_pengajuan_lembur->hapus($id)){
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
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => '',
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					$this->session->set_flashdata('success', 'Pengajuan lembur berhasil dihapus.');
				}
				else {
					$this->session->set_flashdata('warning', 'Data Lembur <b>Gagal</b> Dihapus.');
				}
			}
			else {
				$this->session->set_flashdata('warning', 'Data Lembur <b>Gagal</b> Dihapus.');
			}
			redirect(site_url('lembur/pengajuan_lembur'));
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
		
	 	public function export(){
	        ob_start();
	        $tgl = date('Y-m-d', strtotime('2018-07-03'));
	        $data = $this->m_pengajuan_lembur->ambil_pengajuan_lembur_pegawai_tgl($tgl);
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
				</style>
			</head>
			<body>
				<div id="body">
				<center><h3 style="padding-bottom: 10px; text-align: center"><b>DAFTAR LEMBUR<br></b></h3></center>
					<table class="headTable">
						<tr class="headTable">
							<td class="headTable">SEKSI</td>
							<td class="headTable">:</td>
							<td class="headTable">Seksi Bangsis Informasi</td>
							<td class="headTable"></td>
							<td class="headTable">Tanggal</td>
							<td class="headTable">:</td>
							<td class="headTable">01-Feb-2017</td>
						</tr>
						<tr class="headTable">
							<td class="headTable">KODE BAGAN</td>
							<td class="headTable">:</td>
							<td class="headTable">93130</td>
							<td class="headTable"></td>
							<td class="headTable">Hari</td>
							<td class="headTable">:</td>
							<td class="headTable">Rabu</td>
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
	        foreach ($data as $val) {
	        $no = 1;
	        if ($val['waktu_mulai_fix'] == null) 
	        	$mulai = date('h:i A', strtotime($val['jam_mulai']));
	        else 
	        	$mulai = date('h:i A', strtotime($val['waktu_mulai_fix']));

	        if ($val['waktu_selesai_fix'] == null) 
	        	$selesai = date('h:i A', strtotime($val['jam_selesai']));
	        else 
	        	$selesai = date('h:i A', strtotime($val['waktu_selesai_fix']));

	        $pdf->WriteHTML('<tr>
							<td style="text-align: center">'.$no++.'</td>
							<td>'.$val['nama_pegawai'].'</td>
							<td style="text-align: center">'.$val['no_pokok'].'</td>
							<td style="text-align: center">'.$mulai.'</td>
							<td style="text-align: center">'.$selesai.'</td>
							<td style="text-align: right">0</td>
							<td></td>
						</tr>');
	       	}
			$pdf->WriteHTML('<tr>
							<td colspan="2" style="text-align: center">Jumlah</td>
							<td></td>
							<td></td>
							<td></td>
							<td style="text-align: right">900000</td>
							<td></td>
						</tr>
					</table>

					<p style="text-align: right; padding-bottom: -10px">06-Feb-2017</p>

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
			</div>

			</body>
			</html>');
	        $pdf->Output('DAFTAR LEMBUR.pdf', 'I');
		}*/
		
	}
	
	/* End of file Pengajuan_lembur.php */
	/* Location: ./application/controllers/master_data/jadwal_kerja.php */