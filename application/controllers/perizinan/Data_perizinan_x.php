<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Data_perizinan_x extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'perizinan/';
			$this->folder_model = 'perizinan/';
			$this->folder_controller = 'perizinan/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			$this->load->helper("perizinan_helper");
			
			#$this->load->model($this->folder_model."m_permohonan_cuti");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Data Perizinan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
            $this->nama_db = $this->db->database;
			izin($this->akses["akses"]);
		}
		
		public function index()
		{
            //$this->output->enable_profiler(true);
            $this->load->model($this->folder_model."M_data_perizinan");
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));
            $izin = $this->M_data_perizinan->get_mst_perizinan()->result();
            $array_daftar_karyawan	= $this->M_data_perizinan->select_daftar_karyawan();
            
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."data_perizinan";
            $this->data['jenis_izin']               = $izin;
			//$this->data['select_mst_cuti']= $this->m_permohonan_cuti->select_mst_cuti();
            $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;				
                        
            $this->data['array_tahun_bulan'] 	= $this->M_data_perizinan->get_tabel_perizinan_from_schema()->result();
			
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_data_perizinan() {
            $jenis = array();
            
            $filter_bulan = $this->uri->segment(4);
            if($this->uri->segment(5)==1) {
                $jenis[] = 'D';
            }
            if($this->uri->segment(6)==1) {
                $jenis[] = 'E';
            }
            if($this->uri->segment(7)==1) {
                $jenis[] = 'F';
            }
            if($this->uri->segment(8)==1) {
                $jenis[] = 'G';
            }
            if($this->uri->segment(9)==1) {
                $jenis[] = 'H';
            }
            if($this->uri->segment(10)==1) {
                $jenis[] = 'TM';
            }
            if($this->uri->segment(11)==1) {
                $jenis[] = 'TK';
            }
            if($this->uri->segment(12)==1) {
                $jenis[] = '0';
            }
            /*if($this->uri->segment(14)==1){
                $jenis[] = 'AB';
            }
            if($this->uri->segment(15)==1){
                $jenis[] = 'ATU';
            }*/
            
            $target_table   = $filter_bulan;
            
			$this->load->model($this->folder_model."M_tabel_data_perizinan");
            
//            if($this->akses["ubah"]){ //jika pengguna
//				$disabled_ubah = '';
//			} else{
//				$disabled_ubah = 'disabled';
//			}
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($var,$data['kode_unit']);							
				}				
			} else if($_SESSION["grup"]==5) //jika Pengguna
			{
				$var 	= $_SESSION["no_pokok"];							
			} else
			{
				$var = 1;				
			}
			
			$list 	= $this->M_tabel_data_perizinan->get_datatables($var,$target_table,@$jenis);	
			
			
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $tampil) {
				$no++;
                $absence_type = $tampil->kode_pamlek.'|'.$tampil->info_type.'|'.$tampil->absence_type;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				//$row[] = $tampil->absence_type;	
				$row[] = get_perizinan_name($tampil->kode_pamlek)->nama;	
				
				if($tampil->start_date)
				{
					$row[] = tanggal_indonesia($tampil->start_date).'<br>'.$tampil->start_time;
				}else
				{
					$row[] = '';
				}
				
				if($tampil->end_date)
				{
					$row[] = tanggal_indonesia($tampil->end_date).'<br>'.$tampil->end_time;
				}else
				{
					$row[] = '';
				}
				
                if ($this->akses["hapus"]) {
					
					//cutoff ERP
					if($tampil->start_date)
					{
						$tanggal_check = $tampil->start_date;
					}else
					{
						$tanggal_check = $tampil->end_date;
					}
					
					$sudah_cutoff = sudah_cutoff($tanggal_check);
					
					if($sudah_cutoff) //jika sudah lewat masa cutoff
					{
						$aksi = "<button class='btn btn-primary btn-xs' data-toggle='tooltip' title='Tidak bisa melakukan aksi setelah Cutoff data di ERP pada $sudah_cutoff' disabled>Submit ERP</button>";
						
					}else
					{
						$np_hapus = $tampil->np_karyawan;
						$tanggal_hapus = ($tampil->start_date!=NULL ? $tampil->start_date : $tampil->end_date);
						$time_hapus = ($tampil->start_time!=NULL ? $tampil->start_time : $tampil->end_time);
						$date_time_hapus = date('Y-m-d H:i:s', strtotime($tanggal_hapus.' '.$time_hapus));
						$aksi = '<button class="btn btn-danger btn-xs" onclick="hapus(\''.$tampil->id.'\',\''.$np_hapus.'\',\''.$tampil->start_date.'\',\''.$tampil->end_date.'\')">Hapus</button>';				
					}
                   
                
				}
                else {
                    $aksi = "";
                }
                
				$row[] = $aksi;
				
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_data_perizinan->count_all($var, $target_table, @$jenis),
							"recordsFiltered" => $this->M_tabel_data_perizinan->count_filtered($var, $target_table, @$jenis),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
	
        //Fungsi untuk mengambil data dalam file .txt yang ada di outbound_portal/sppd
        public function get_data($last_date=null) {
            //run program selamanya untuk menghindari maximal execution
            //ini_set('MAX_EXECUTION_TIME', -1);
            set_time_limit('0');

            //$this->output->enable_profiler(TRUE);

            echo "Proses ambil data dari pamlek";
            echo "<br>mulai ".date('Y-m-d H:i:s')."<br>";

            //ambil data di database setting
            $this->load->model($this->folder_model."M_sppd");
            $setting	= $this->M_sppd->setting();
            
            $pamlek_url	= dirname($_SERVER["SCRIPT_FILENAME"]).$setting['url'];
            $pamlek_max	= $setting['max_files_hapus'];
            // $pamlek_max	= $setting['max_files'];
            
            if(@$last_date){
                $file = 'biaya-sppd-'.$last_date.'.txt';
                if($last_date==date('Y-m-d') && is_file($pamlek_url.$file)){
                    $this->read_process($pamlek_url, $file);
                    
                } else{
                    $data_error['modul'] 		= "perizinan/sppd/get_data"; 
                    $data_error['error'] 		= "Gagal konek ke Server Pamlek"; 
                    $data_error['status'] 		= "0";
                    $data_error['created_at'] 	= date("Y-m-d H:i:s");
                    $data_error['created_by'] 	= "scheduler";

                    $this->M_sppd->insert_error($data_error);
                    echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
                }
            } else{
                //ambil data mana saja yang belum di proses
                $result = $this->M_sppd->select_pamlek_files_limit($pamlek_max);

                $arr_registered_pamlek_files = array();
                foreach ($result->result_array() as $data) {
                    array_push($arr_registered_pamlek_files,$data['nama_file']);	 
                }
                
                //check server pamlek menyala
                if(is_dir($pamlek_url)) {
                    //scan file .txt dalam server ftp pamlek 
                    $arr_scan_pamlek_files = scandir($pamlek_url);

                    $pamlek_files = array();		
                    foreach($arr_scan_pamlek_files as $file){
                        if(in_array($file,$arr_registered_pamlek_files)){
                            array_push($pamlek_files,$file);
                        }
                    }

                    foreach($pamlek_files as $file){
                        $this->read_process($pamlek_url, $file);
                    }
                } else {
                    $data_error['modul'] 		= "perizinan/sppd/get_data"; 
                    $data_error['error'] 		= "Gagal konek ke Server Pamlek"; 
                    $data_error['status'] 		= "0";
                    $data_error['created_at'] 	= date("Y-m-d H:i:s");
                    $data_error['created_by'] 	= "scheduler";

                    $this->M_sppd->insert_error($data_error);
                    echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
                }
            }
            
            echo "<br>selesai ".date('Y-m-d H:i:s');
        }
        
        function read_process($pamlek_url, $file){
            echo "<br>".$file."<br><br>";

            $rows = explode("\n",trim(file_get_contents($pamlek_url.$file)));

            $i =1;
            $banyak_data=0;			
            
            //parsing data di file .txt
            $array_insert_data = array();
            foreach($rows as $row){
                if(!empty(trim($row))){

                    $banyak_data++;					
                    $pisah = explode("\t",trim($row));
                    
                    $insert_data = array(
                        'id_sppd'       	=> $pisah[0],
                        'id_user'           => $pisah[1],
                        'kode_sto' 			=> $pisah[2],
                        'jenis_fasilitas'	=> $pisah[3],	
                        'biaya'			    => $pisah[4],	
                        'catatan'   		=> @$pisah[6],	
                        'tgl_pulang'		=> @$pisah[7]
                    );

                    $this->M_sppd->cek_id_then_insert_data($pisah[0], $insert_data);
                }
            }
            
            $update_file = array(
                'proses'			=> '1',
                'baris_data' 		=> $banyak_data,
                'waktu_proses'		=> date('Y-m-d H:i:s')
            );

            $this->M_sppd->update_files($file, $update_file);
        }
        
        public function ajax_getListNp()
		{			
            $this->load->model($this->folder_model."M_data_perizinan");
			$tampil='';
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$list_kode_unit=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($list_kode_unit,$data['kode_unit']);								
				}
				
				$np_list=array();
				$np_list=$this->M_data_perizinan->select_np_by_kode_unit($list_kode_unit);						
								
				foreach ($np_list->result_array() as $np) 
				{		
					if($tampil)
					{				
						$tampil=$tampil."".$np['no_pokok']." | ".$np['nama']."\n";
					}else
					{
						$tampil=$np['no_pokok']." | ".$np['nama']."\n";
					}
					
				}
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				$np 	= $_SESSION["no_pokok"];
				$tampil	= $np." | ".nama_karyawan_by_np($np)."\n";
			}else
			{
				$tampil = "Anda Memiliki Hak untuk semua nomer pokok Karyawan";
			}
			
			
	
			echo $tampil;				
		}
        
        public function ajax_getNama()
		{
			$np_karyawan	= $this->input->post('vnp_karyawan');	
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$temp='';
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{					
					if(kode_unit_by_np($np_karyawan)==$data['kode_unit']) //check apakah ada disalah satu unit 
					{
						$temp=$np_karyawan;
					}				
				}
				
				$np_karyawan	=$temp;				
				
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				if($np_karyawan==$_SESSION["no_pokok"])
				{
					$np_karyawan	= $this->input->post('vnp_karyawan');						
				}else
				{
					$np_karyawan	= '';					
				}
			}
			
			$nama 			= nama_karyawan_by_np($np_karyawan);
			
			echo $nama; 	
		}
        
        function action_insert_perizinan(){
            $data_insert = [];
            $submit = $this->input->post('submit');
            if($submit){
				$absence_type		= $this->input->post('absence_type');
                $explode = explode('|', $absence_type);
                $info_type = $explode[1];
                $absence_type = $explode[2];
                $kode_pamlek = $explode[0];
                
                $np_karyawan		= $this->input->post('np_karyawan');				
				$start_date			= date('Y-m-d',strtotime($this->input->post('start_date')));
				$start_time			= $this->input->post('start_time');
				$end_date			= date('Y-m-d',strtotime($this->input->post('end_date')));
				$end_time			= $this->input->post('end_time');
                
                $end_date_time = date('Y-m-d H:i', strtotime($end_date.' '.$end_time));
                if($kode_pamlek!='0'){
                    $start_date_time = date('Y-m-d H:i', strtotime($start_date.' '.$start_time));
                    $tahun_bulan     = str_replace('-','_',substr("$start_date", 0, 7));
                } else{
                    $tahun_bulan     = str_replace('-','_',substr("$end_date", 0, 7));
                }
                
				$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
				$personel_number	= erp_master_data_by_np($np_karyawan, $start_date)['personnel_number'];
				$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
				$kode_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['kode_unit'];
				$nama_unit 			= erp_master_data_by_np($np_karyawan, $start_date)['nama_unit'];
                
                
                $data_insert = [
                    'np_karyawan'=>$np_karyawan,
                    'nama'=>$nama_karyawan,
                    'personel_number'=>$personel_number,
                    'nama_jabatan'=>$nama_jabatan,
                    'kode_unit'=>$kode_unit,
                    'nama_unit'=>$nama_unit,
                    'info_type'=>$info_type,
                    'absence_type'=>$absence_type,
                    'kode_pamlek'=>$kode_pamlek,
                    'start_date'=>$start_date,
                    'start_time'=>$start_time,
                    'end_date'=>$end_date,
                    'end_time'=>$end_time
                ];
                
                echo '<br>under maintenance. <br>';
                echo json_encode($data_insert); exit();
                
                if($nama_karyawan=='' || $nama_karyawan==null){
					$this->session->set_flashdata('warning',"NP Karyawan <b>$np_karyawan</b> tidak ditemukan.");
					redirect(base_url($this->folder_controller.'data_perizinan'));
				} else if($start_date=='' || $start_date==null){
					$this->session->set_flashdata('warning',"Start date tidak boleh kosong.");
					redirect(base_url($this->folder_controller.'data_perizinan'));
				} else if($end_date=='' || $end_date==null){
					$this->session->set_flashdata('warning',"End date tidak boleh kosong.");
					redirect(base_url($this->folder_controller.'data_perizinan'));
				} else if($start_date_time >= $end_date_time){
					$this->session->set_flashdata('warning',"Tanggal Akhir Perizinan harus lebih besar dari Tanggal Mulai Perizinan.");
					redirect(base_url($this->folder_controller.'data_perizinan'));
				}
                
                $this->db->query("CREATE TABLE IF NOT EXISTS ess_perizinan_$tahun_bulan LIKE ess_perizinan");
                
                //old $cek = $this->db->where(['np_karyawan'=>$nama_karyawan, 'start_date'=>$start_date, 'end_date'=>$end_date, 'start_time'=>$start_time, 'end_time'=>$end_time, 'kode_pamlek'=>$kode_pamlek])->get("ess_perizinan_$tahun_bulan");
                $cek = $this->db->query("SELECT * 
                FROM ess_perizinan_$tahun_bulan
                WHERE np_karyawan='$np_karyawan' 
                AND (
                    ('$start_date_time' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
                    OR ('$end_date_time' BETWEEN DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') AND DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i'))
                    OR (DATE_FORMAT(CONCAT(start_date,' ',start_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time' AND '$end_date_time')
                    OR (DATE_FORMAT(CONCAT(end_date,' ',end_time),'%Y-%m-%d %H:%i') BETWEEN '$start_date_time' AND '$end_date_time')
                ) ");
                if($cek->num_rows()>0){
                    $this->session->set_flashdata('warning',"Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time sudah ada.");
					redirect(base_url($this->folder_controller.'data_perizinan'));
                } else{
                    $this->db->insert("ess_perizinan_$tahun_bulan", $data_insert);
                    
                    $parameter_perizinan = [
                        'id_row_baru'=>$this->db->insert_id(),
                        'np_karyawan'=>$np_karyawan,
                        'date_start'=>$start_date,
                        'date_end'=>$end_date
                    ];
                    $this->update_cico($parameter_perizinan);
                    
                    $this->session->set_flashdata('success',"Data perizinan dengan nama $nama_karyawan, pada rentang tanggal $start_date $start_time sampai $end_date $end_time berhasil ditambahkan.");
					redirect(base_url($this->folder_controller.'data_perizinan'));
                }
                
                echo json_encode($data_insert);
                
            } else{
                $this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'data_perizinan'));	
            }
        }
        
        public function hapus($id=null, $np=null, $tanggal_start=null, $tanggal_end=null) {
            $this->load->model($this->folder_model."M_data_perizinan");
            echo 'under maintenance'; exit();
			if($id != null && $np != null && $tanggal_start != null && $tanggal_end != null) {
                $tanggal_proses = $tanggal_start;
                while($tanggal_proses <= $tanggal_end){
                    $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
                    $cek = $this->db->query("SELECT TABLE_NAME FROM information_schema.`TABLES` WHERE table_schema='$this->nama_db' AND TABLE_NAME='ess_cico_$tahun_bulan'");
                    
                    if($cek->num_rows()>0){
                        $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $np)->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                        
                        if($get_cico->num_rows()>0){
                            $str_fix = '';
                            $row = $get_cico->row_array();
                            
                            //str awal diambil dari id_perizinan di cico
                            $str_awal = $row['id_perizinan'];
                            //convert str_awal to array_awal
                            $arr_awal = explode(',', $str_awal);

                            //concat dari id tabel perizinan
                            $str_datang = $id;

                            if (($key = array_search($str_datang, $arr_awal)) !== false) {
                                unset($arr_awal[$key]);
                                $arr_awal = array_values($arr_awal);
                            }

                            //convert arr_awal to str
                            $str_awal = implode(',', $arr_awal);
                            $str_fix = trim($str_awal,',');

                            $this->db->where('id', $row['id'])->update('ess_cico_'.$tahun_bulan, ['id_perizinan'=>$str_fix]);
                        }
                    }
                    
                    $tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
                }
                
                $tahun_bulan_perizinan = str_replace('-','_',substr("$tanggal_start", 0, 7));
                $get = $this->M_data_perizinan->ambil_perizinan_id($id, 'ess_perizinan_'.$tahun_bulan_perizinan);
                if($this->db->where('id', $id)->delete("ess_perizinan_$tahun_bulan_perizinan")){
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
                    $this->session->set_flashdata('success', 'Perizinan berhasil dihapus.');
                }
                
			} else {
				$this->session->set_flashdata('warning', 'Data Perizinan <b>Gagal</b> Dihapus.');
			}
			redirect(base_url('perizinan/data_perizinan'));
		}
        
        function update_cico($data_lempar){
            $tanggal_proses = $data_lempar['date_start'];
            
            while($tanggal_proses <= $data_lempar['date_end']){
                $tahun_bulan = str_replace('-','_',substr("$tanggal_proses", 0, 7));
                
                //cek table exist
                $get_table = $this->db->select('TABLE_NAME')->where('table_schema', $this->nama_db)->where('TABLE_NAME', 'ess_cico_'.$tahun_bulan)->get('information_schema.`TABLES`');
                if($get_table->num_rows()>0){
                    //get cico
                    $get_cico = $this->db->select('id, np_karyawan, id_perizinan, dws_tanggal')->where('np_karyawan', $data_lempar['np_karyawan'])->where('dws_tanggal', $tanggal_proses)->get('ess_cico_'.$tahun_bulan);
                    
                    if($get_cico->num_rows()>0){
                        $data_to_process = array();
                        $row = $get_cico->row_array();
                        
                        $data_to_process = [
                            'id'=>$row['id'],
                            'id_perizinan'=>$row['id_perizinan'],
                            'tahun_bulan'=>str_replace('-','_',substr($row['dws_tanggal'], 0, 7)),
                            'id_row_baru'=>$data_lempar['id_row_baru']
                        ];
                        $this->process_update_cico($data_to_process);
                        
                    }
                }
                
                $tanggal_proses = date ("Y-m-d", strtotime("+1 day", strtotime($tanggal_proses)));
            }
        }
        
        function process_update_cico($data_lempar){
            //$data_cico = $get_cico->row();
            $str_fix = '';
            $new_element = [];

            //str awal diambil dari id_perizinan di cico
            $str_awal = $data_lempar['id_perizinan'];
            //convert str_awal to array_awal
            $arr_awal = explode(',', $str_awal);

            //concat dari id tabel perizinan
            $str_datang = $data_lempar['id_row_baru'];
            //convert str_datang to array_datang
            $arr_datang = explode(',', $str_datang);

            //found elements of arr_datang where not in arr_awal
            $new_elements=array_diff($arr_datang, $arr_awal);

            foreach($new_elements as $value){
                //push new element to arr_awal
                $arr_awal[] = $value;
            }

            //convert arr_awal to str
            $str_awal = implode(',', $arr_awal);
            $str_fix = trim($str_awal,',');
            
            $this->db->where('id', $data_lempar['id'])->update('ess_cico_'.$data_lempar['tahun_bulan'], ['id_perizinan'=>$str_fix]);
        }

        public function cetak() {
            // print_r($this->input->post('izin_0'));
            // die();
			$this->load->library('phpexcel'); 
			$this->load->model($this->folder_model."M_tabel_data_perizinan");
           
            # tambahan untuk cetak semua NP, 2024-04-04 Robi Purnomo
            if (in_array('all_karyawan', $this->input->post('np_karyawan'))) {
                $this->load->model("perizinan/M_data_perizinan");
                $query_result = $this->M_data_perizinan->select_daftar_karyawan_and_outsource();
                $array_daftar_karyawan = $query_result;
                $set['np_karyawan'] = array_column($array_daftar_karyawan, 'no_pokok');
            } else {
                $set['np_karyawan'] = $this->input->post('np_karyawan');
            }
             # tambahan untuk cetak semua NP, 2024-04-04 Robi Purnomo
            
			$jenis = array();
			$filter_bulan   = $this->input->post('bulan');
            // die($this->input->post('bulan'));
            if($this->input->post('izin_C')==1)
                $jenis[] = 'C';
            if($this->input->post('izin_E')==1)
                $jenis[] = 'E';
            if($this->input->post('izin_F')==1)
                $jenis[] = 'F';
            if($this->input->post('izin_G')==1)
                $jenis[] = 'G';
            if($this->input->post('izin_H')==1)
                $jenis[] = 'H';
            if($this->input->post('izin_TM')==1)
                $jenis[] = 'TM';
            if($this->input->post('izin_TK')==1)
                $jenis[] = 'TK';
            if($this->input->post('izin_0')==1)
                $jenis[] = '0';
            if($this->input->post('izin_SIPK')==1)
                $jenis[] = 'SIPK';

            $target_table   = $filter_bulan;
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$var = array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var,$data['kode_unit']);							
				}
			} 
			else if($_SESSION["grup"]==5) //jika Pengguna
				$var = $_SESSION["no_pokok"];							
			else
				$var = 1;

            
			$get_data = $this->M_tabel_data_perizinan->_get_excel($var,$target_table,$set,@$jenis);
            // print_r($get_data);
            // die();
	        // error_reporting(E_ALL);
	        // ini_set('display_errors', TRUE);
	        // ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        header("Content-Disposition: attachment; filename=Data_perizinan.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_data_perizinan.xlsx');

	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 2;
	        $no = 1;
			
			foreach ($get_data as $tampil) {
                // Decode JSON from the database column
                $data = json_decode($tampil->approval_pengamanan_posisi, true);
                $pos_pengamanan_posisi = '';
            
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Check if data is parsed correctly
                    if (is_array($data)) {
                        // Iterate over the array and format "nama pos" and "waktu"
                        foreach ($data as $entry) {
                            if (isset($entry['nama_pos']) && isset($entry['waktu'])) {
                                $pos_pengamanan_posisi .= "Nama Pos: " . $entry['nama_pos'] . "\n";
                                $pos_pengamanan_posisi .= "Waktu: " . $entry['waktu'] . "\n\n";
                            } else {
                                $pos_pengamanan_posisi .= "";
                                $pos_pengamanan_posisi .= "";
                            }
                        }
                    } else {
                        $pos_pengamanan_posisi = "";
                    }
                } else {
                    $pos_pengamanan_posisi = "";
                }
            
                // Generate absence type
                $absence_type = $tampil->kode_pamlek . '|' . $tampil->info_type . '|' . $tampil->absence_type;
            
                // Populate Excel sheet
                $excel->getActiveSheet()->setCellValueExplicit('A' . $awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->setCellValueExplicit('B' . $awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->setCellValueExplicit('C' . $awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->setCellValueExplicit('D' . $awal, ucwords($tampil->nama_unit), PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->setCellValueExplicit('E' . $awal, get_perizinan_name($tampil->kode_pamlek)->nama, PHPExcel_Cell_DataType::TYPE_STRING);
                // Set pos pengamanan posisi
                $excel->getActiveSheet()->setCellValueExplicit('F' . $awal, strtoupper($pos_pengamanan_posisi), PHPExcel_Cell_DataType::TYPE_STRING);
                
            
                // Populate start date and time
                if ($tampil->start_date) {
                    $excel->getActiveSheet()->setCellValueExplicit('G' . $awal, tanggal_indonesia($tampil->start_date) . ' ' . $tampil->start_time, PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $excel->getActiveSheet()->setCellValueExplicit('G' . $awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
                }
            
                // Populate end date and time
                if ($tampil->end_date) {
                    $excel->getActiveSheet()->setCellValueExplicit('H' . $awal, tanggal_indonesia($tampil->end_date) . ' ' . $tampil->end_time, PHPExcel_Cell_DataType::TYPE_STRING);
                } else {
                    $excel->getActiveSheet()->setCellValueExplicit('H' . $awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
                }
                
                //keperluan
                $excel->getActiveSheet()->setCellValueExplicit('I' . $awal, strtoupper($tampil->alasan), PHPExcel_Cell_DataType::TYPE_STRING);
            
                
                // Approval details
                $excel->getActiveSheet()->setCellValueExplicit('J' . $awal, strtoupper($tampil->approval_1_np . " - " . $tampil->approval_1_nama), PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->setCellValueExplicit('K' . $awal, strtoupper($tampil->approval_1_status), PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->setCellValueExplicit('L' . $awal, strtoupper($tampil->approval_2_np . " - " . $tampil->approval_2_nama), PHPExcel_Cell_DataType::TYPE_STRING);
                $excel->getActiveSheet()->setCellValueExplicit('M' . $awal, strtoupper($tampil->approval_2_status), PHPExcel_Cell_DataType::TYPE_STRING);
            
                // Increment the row counter
                $awal++;
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