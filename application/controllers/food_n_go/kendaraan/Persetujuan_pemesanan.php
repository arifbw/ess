<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Persetujuan_pemesanan extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'food_n_go/kendaraan/';
			$this->folder_model = 'kendaraan/';
			$this->folder_controller = 'food_n_go/kendaraan/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("perizinan_helper");
			$this->load->helper('form');
			$this->load->helper('kendaraan');
			
			$this->load->model($this->folder_model."m_persetujuan_pemesanan");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Persetujuan Pemesanan Kendaraan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			//$this->output->enable_profiler(true);
		}
		
		public function index() {
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."persetujuan_pemesanan";
			
			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			
			$nama_db = $this->db->database;
            
            $get_month = $this->db->select('tanggal_berangkat')->group_by('YEAR(tanggal_berangkat), MONTH(tanggal_berangkat)')->order_by('YEAR(tanggal_berangkat) DESC, MONTH(tanggal_berangkat) DESC')->get($nama_db.'.ess_pemesanan_kendaraan')->result();
            
            foreach($get_month as $row){
                $array_tahun_bulan[] = date('m-Y', strtotime($row->tanggal_berangkat));
            }
			
			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;
			//echo json_encode($this->data['array_tahun_bulan']);exit();
			$this->load->view('template',$this->data);	
		}
		
		public function tabel_persetujuan_pemesanan($konfirmasi, $tampil_bulan_tahun = null) {
			$this->load->model($this->folder_model."/M_tabel_persetujuan_pemesanan");
			
			//akses ke menu ubah				
			if(@$this->akses["ubah"]) //jika pengguna
			{
				$disabled_ubah = '';
			}else
			{
				$disabled_ubah = 'disabled';
			}
						
			if($tampil_bulan_tahun=='')
			{
				$tampil_bulan_tahun = '';				
			}else
			{
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
			
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
				
			$list = $this->M_tabel_persetujuan_pemesanan->get_datatables($var, $konfirmasi, $tampil_bulan_tahun);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
                
                $btn = '';
                
                # btn detail
                $btn .= '<button class="btn btn-default btn-xs edit_button" type="button" data-id="'.$tampil->id.'" onclick="show_detail(this)">Detail</button> ';
                
                if(@$this->akses["persetujuan"] && $_SESSION["grup"]==5 && !in_array($tampil->verified,[1,2]) && $tampil->tanggal_berangkat>=date('Y-m-d')){
                    $btn .= '<button class="btn btn-primary btn-xs edit_button" type="button" data-id="'.$tampil->id.'" onclick="update_approval(this)">Persetujuan</button> ';
                }
                
                # status
                $status = status_pemesanan([
                    'tanggal_berangkat'=>$tampil->tanggal_berangkat,
                    'verified'=>$tampil->verified,
                    'status_persetujuan_admin'=>$tampil->status_persetujuan_admin,
                    'id_mst_kendaraan'=>$tampil->id_mst_kendaraan,
                    'id_mst_driver'=>$tampil->id_mst_driver,
                    'pesanan_selesai'=>$tampil->pesanan_selesai,
                    'rating_driver'=>$tampil->rating_driver,
                    'is_canceled_by_admin'=>$tampil->is_canceled_by_admin
                ]);
                
                # lokasi asal/jemput
                $berangkat_dari='';
                if($tampil->lokasi_jemput!=null and $tampil->lokasi_jemput!=''){
                    $berangkat_dari .= $tampil->lokasi_jemput;
                }
                if($tampil->nama_kota_asal!=null and $tampil->nama_kota_asal!=''){
                    $berangkat_dari .= '<br><small>('.$tampil->nama_kota_asal.')</small>';
                }
                
                # waktu berangkat
                $waktu_berangkat=hari_tanggal($tampil->tanggal_berangkat).' @'.$tampil->jam;
                if($tampil->is_inap==1 || $tampil->is_pp==1){
                    $waktu_berangkat .= ($tampil->is_inap==1?'<br><small>(Menginap)</small>':'<br><small>(PP');
                    if($tampil->tanggal_awal!=null){
                        $waktu_berangkat .= ': '.hari_tanggal($tampil->tanggal_awal);
                    }
                    if($tampil->tanggal_akhir!=null){
                        $waktu_berangkat .= ' s/d '.hari_tanggal($tampil->tanggal_akhir);
                    }
                    $waktu_berangkat .= ')</small>';
                } else{
                    $waktu_berangkat .= '<br><small>(Sekali Jalan)</small>';
                }
                
				$row = array();
				$row[] = $no;
				$row[] = $tampil->nomor_pemesanan;
				$row[] = $tampil->is_read==0 ? '<b>'.$tampil->no_hp_pemesan.'<br>'.$tampil->np_karyawan.' - '.$tampil->nama.'</b>' : $tampil->no_hp_pemesan.'<br>'.$tampil->np_karyawan.' - '.$tampil->nama;
				$row[] = $berangkat_dari;
				$row[] = get_pemesanan_tujuan_small($tampil->id);
				$row[] = $waktu_berangkat;
				$row[] = $tampil->jumlah_penumpang;
				// $row[] = $tampil->nama_mst_kendaraan!=null?$tampil->nama_mst_kendaraan:$tampil->jenis_kendaraan_request;
				// $row[] = $tampil->verified_by_nama;
				$row[] = $status;
				$row[] = $btn;
								
				$data[] = $row;
			}

			$output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->M_tabel_persetujuan_pemesanan->count_all($var, $konfirmasi, $tampil_bulan_tahun),
                "recordsFiltered" => $this->M_tabel_persetujuan_pemesanan->count_filtered($var, $konfirmasi, $tampil_bulan_tahun),
                "data" => $data,
            );
			//output to json format
			echo json_encode($output);
		}
		
		public function action_insert_data_pemesanan() {
            //echo json_encode($this->input->post()); exit;
			$submit = $this->input->post('submit');
            $data_insert = [];
			if($submit) {
                
                //$tampil_bulan_tahun = $this->input->post('insert_tampil_bulan_tahun',true);
                
                $get_mst_karyawan = mst_karyawan_by_np($this->input->post('insert_np_karyawan',true));
                $np_karyawan = $this->input->post('insert_np_karyawan',true);
                $nama = $this->input->post('insert_nama',true);
                $tanggal = $this->input->post('tanggal_berangkat',true);
                $verified_by = $this->input->post('verified_by',true);
                $explode = explode(' - ',$verified_by);
                $verified_by_np = $explode[0];
                $verified_by_nama = $explode[1];
								
				$data_insert['np_karyawan'] = $np_karyawan;
				$data_insert['nama'] = $nama;
				$data_insert['kode_unit'] = $get_mst_karyawan['kode_unit'];
				$data_insert['nama_unit'] = $get_mst_karyawan['nama_unit'];
				$data_insert['kode_unit_pemesan'] = $this->input->post('kode_unit_pemesan',true);
				$data_insert['nama_unit_pemesan'] = $this->input->post('nama_unit_pemesan',true);
				$data_insert['jumlah_penumpang'] = $this->input->post('jumlah_penumpang',true);
				$data_insert['tanggal_berangkat'] = date('Y-m-d', strtotime($tanggal));
				$data_insert['jam'] = $this->input->post('jam',true);
				//$data_insert['tujuan'] = $this->input->post('tujuan',true);
				$data_insert['kode_kota_asal'] = $this->input->post('kode_kota_asal',true);
				$data_insert['nama_kota_asal'] = $this->input->post('nama_kota_asal',true);
				$data_insert['lokasi_jemput'] = $this->input->post('lokasi_jemput',true);
				$data_insert['no_hp_pemesan'] = $this->input->post('no_hp',true);
				$data_insert['no_ext_pemesan'] = $this->input->post('no_ext_pemesan',true);
				$data_insert['keterangan'] = $this->input->post('keterangan',true);
				$data_insert['created'] = date('Y-m-d H:i:s');
				$data_insert['verified'] = 0;
				$data_insert['verified_by_np'] = $verified_by_np;
				$data_insert['verified_by_nama'] = $verified_by_nama;
				$data_insert['is_read'] = 0;
                
                $data_insert['is_pp']=(@$this->input->post('is_pp',true)=='on'?1:0);
                
                if(@$this->input->post('is_inap',true)=='on'){
                    $data_insert['is_inap']=1;
                    $data_insert['tanggal_inap_awal'] = date('Y-m-d', strtotime($this->input->post('tanggal_inap_awal',true)));
                    $data_insert['tanggal_inap_akhir'] = date('Y-m-d', strtotime($this->input->post('tanggal_inap_akhir',true)));
                } else{
                    $data_insert['is_inap']=0;
                }
				
				$insert = $this->m_persetujuan_pemesanan->insert_data_pemesanan($data_insert);
					
				if($insert==0) {
					$this->session->set_flashdata('warning',"<b>Gagal</b> input pemesanan");
				} else {
                    # START insert kota tujuan
                    $new_id = $this->db->insert_id();
                    $arr_insert_tujuan=[];
                    if(@$this->input->post('kode_kota_tujuan')){
                        $arr_kode_kota_tujuan = $this->input->post('kode_kota_tujuan');
                        $arr_keterangan_tujuan = $this->input->post('keterangan_tujuan');
                        for($i=0;$i<count($arr_kode_kota_tujuan);$i++){
                            $arr_insert_tujuan[]=[
                                'id_pemesanan_kendaraan'=>$new_id,
                                'kode_kota_tujuan'=>$arr_kode_kota_tujuan[$i],
                                'nama_kota_tujuan'=>$this->m_persetujuan_pemesanan->get_nama_kota_by_kode($arr_kode_kota_tujuan[$i]),
                                'keterangan_tujuan'=>$arr_keterangan_tujuan[$i],
                                'status'=>1
                            ];
                        }
                        $this->db->insert_batch('ess_pemesanan_kendaraan_kota',$arr_insert_tujuan);
                    }
                    # END insert kota tujuan
                    
					$this->session->set_flashdata('success',"Berhasil, data pemesanan kendaraan <b>$np_karyawan | $nama</b> tanggal <b>$tanggal</b> telah diajukan");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_persetujuan_pemesanan->select_pemesanan_by_id($insert);					
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
						"deskripsi" => "insert ".strtolower(preg_replace("//"," ",CLASS_)),						
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
				}
				
				//$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'data_pemesanan'));				
				
			} else {
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'data_pemesanan'));	
			}	
		}
					
		public function action_update_data_pemesanan()
		{			
			echo json_encode($this->input->post()); exit;
            $submit = $this->input->post('submit');
								
			if($submit)
			{					
				$id 				= $this->input->post('edit_id');
				$np_karyawan 		= $this->input->post('edit_np_karyawan');
				$nama 				= $this->input->post('edit_nama');
				$dws_tanggal 		= date('Y-m-d', strtotime($this->input->post('edit_dws_tanggal')));
				$dws_name 			= $this->input->post('edit_dws_name');			
				
				$tapping_1_date 	= date('Y-m-d', strtotime($this->input->post('edit_tapping_1_date')));		
				$tapping_1_time 	= $this->input->post('edit_tapping_1_time');		
				$tapping_1 			= $tapping_1_date." ".$tapping_1_time;						
								
				$tapping_2_date 	= date('Y-m-d', strtotime($this->input->post('edit_tapping_2_date')));		
				$tapping_2_time 	= $this->input->post('edit_tapping_2_time');		
				$tapping_2 			= $tapping_2_date." ".$tapping_2_time;
								
				$np_approval_array 	= $this->input->post('edit_approval');
				$np_approval		= $np_approval_array[0];
							
				$tapping_fix_approval_ket 	= $this->input->post('edit_tapping_fix_approval_ket');
				
				$tampil_bulan_tahun	= $this->input->post('edit_tampil_bulan_tahun');
							
				$bulan	= substr($tampil_bulan_tahun,0,2);
				$tahun 	= substr($tampil_bulan_tahun,3,4);
				$tahun_bulan		= $tahun."_".$bulan;
				
				//16 03 2020 - Tri Wibowo, WORK FROM HOME
				$wfh		= $this->input->post('edit_wfh');
				$wfh_foto	= $this->input->post('edit_wfh_foto');
				
				
				//validasi ketika tapping keluar lebih kecil dari tapping masuk
				if(strtotime($tapping_2)<=strtotime($tapping_1))
				{
					if($wfh=='1' && ($tapping_2_time=='' || $tapping_2_time==null || $tapping_2_time=='00:00'|| $tapping_2_time==' 00:00') && @$tapping_1_time) //jika wfh boleh isi masuk nya dulu
					{
						//do nothing
					}else
					{						
						$this->session->set_flashdata('warning',"Gagal, Tapping Keluar harus lebih besar dari Tapping Masuk, wfh=$wfh, tapping in = $tapping_1, tapping out = $tapping_2");				
							redirect(base_url($this->folder_controller.'data_kehadiran'));	
					}	
				}
				
				//===== Log Start =====
				$arr_data_lama = $this->m_persetujuan_pemesanan->select_pemesanan_by_id($id,$tahun_bulan);
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
				
			
				$this->load->library(array('upload'));
				$this->load->helper(array('form', 'url'));
					
				$config['upload_path'] = 'file/kehadiran';			
				$config['allowed_types'] 	= 'gif|jpg|jpeg|image/jpeg|image|png';
				$config['max_size']			= '2000';	
				$config['encrypt_name'] 	= true;	
				
				$edit_wfh_foto[0] 	= null;
				$edit_wfh_foto[1]	= null;	
				$files = $_FILES;
				
				//check apakah ada file sebelumnya :
				if($tampil_bulan_tahun=='')
				{
					$tabel = 'ess_cico';
				}else
				{
					$tabel = 'ess_cico'."_".$tahun_bulan;
				}
					
				$ambil = $this->db->query("SELECT wfh_foto_1,wfh_foto_2 FROM $tabel WHERE id='$id'")->row_array();
				$ambil_wfh_foto_1 = $ambil['wfh_foto_1'];
				$ambil_wfh_foto_2 = $ambil['wfh_foto_2'];
					
				if(@files && $wfh=='1')
				{
					
					$cpt = count($_FILES['edit_wfh_foto']['name']);
					for($i=0; $i<$cpt; $i++)
					{
						
						$_FILES['edit_wfh_foto']['name']= $files['edit_wfh_foto']['name'][$i];
						$_FILES['edit_wfh_foto']['type']= $files['edit_wfh_foto']['type'][$i];
						$_FILES['edit_wfh_foto']['tmp_name']= $files['edit_wfh_foto']['tmp_name'][$i];
						$_FILES['edit_wfh_foto']['error']= $files['edit_wfh_foto']['error'][$i];
						$_FILES['edit_wfh_foto']['size']= $files['edit_wfh_foto']['size'][$i];
						
						$this->upload->initialize($config);
																				
						if($i==0)
						{
							if($files['edit_wfh_foto']['name'][$i])
							{								
								if(@$ambil_wfh_foto_1)
								{
									$path		= './file/kehadiran/'.$ambil_wfh_foto_1;
									$this->load->helper("file");
									unlink($path);
								}
																
								$gambar='edit_wfh_foto';
								if($this->upload->do_upload($gambar))
								{	
									$up=$this->upload->data();								
									$edit_wfh_foto[$i]=$up['file_name'];
								}else
								{
									$error =$this->upload->display_errors();
									$this->session->set_flashdata('warning',"Terjadi Kesalahan, $error");				
									redirect(base_url($this->folder_controller.'data_kehadiran'));	
								}
								
							}else
							{
								$edit_wfh_foto[$i]= $ambil_wfh_foto_1;
							}								
						}

						if($i==1)
						{
							if($files['edit_wfh_foto']['name'][$i])
							{
								
								if(@$ambil_wfh_foto_2)
								{
									$path		= './file/kehadiran/'.$ambil_wfh_foto_2;
									$this->load->helper("file");
									unlink($path);
								}
																							
								$gambar='edit_wfh_foto';
								if($this->upload->do_upload($gambar))
								{	
									$up=$this->upload->data();								
									$edit_wfh_foto[$i]=$up['file_name'];
								}else
								{
									$error =$this->upload->display_errors();
									$this->session->set_flashdata('warning',"Terjadi Kesalahan, $error");				
									redirect(base_url($this->folder_controller.'data_kehadiran'));	
								}
								
								
							}else
							{
								$edit_wfh_foto[$i]= $ambil_wfh_foto_2;
							}
						}
						
							
							
							
										
					}
				}else
				{	
					//pake data sebelum	
					$edit_wfh_foto[0]= $ambil_wfh_foto_1;					
					$edit_wfh_foto[1]= $ambil_wfh_foto_1;					
				}
				
				$data_update['id'] 			= $id;
				$data_update['np_karyawan'] = $np_karyawan;
				$data_update['nama_unit'] 	= nama_unit_by_np($np_karyawan);
				$data_update['nama_jabatan']= nama_jabatan_by_np($np_karyawan);
				$data_update['nama'] 		= $nama;
				$data_update['dws_tanggal'] = $dws_tanggal;
				$data_update['dws_name'] 	= $dws_name;
				$data_update['tapping_1']	= $tapping_1;
				$data_update['tapping_2'] 	= $tapping_2;
				$data_update['tahun_bulan'] = $tahun_bulan;
				$data_update['tapping_fix_approval_ket'] = $tapping_fix_approval_ket;
									
				$this->load->model("master_data/m_karyawan");
				$approval = $this->m_karyawan->get_posisi_karyawan($np_approval);
									
				$data_update['tapping_fix_approval_status'] 		= "0"; //default belum di approve
				$data_update['tapping_fix_approval_np'] 			= $approval['no_pokok'];
				$data_update['tapping_fix_approval_nama'] 			= $approval['nama'];
				$data_update['tapping_fix_approval_nama_jabatan'] 	= $approval['nama_jabatan'];
				$data_update['tapping_fix_approval_date'] 			= date('Y-m-d H:i:s');
				
				//16 03 2020 - Tri Wibowo, WORK FROM HOME
				$data_update['wfh'] = $wfh;
				$data_update['wfh_foto_1'] = $edit_wfh_foto[0];
				$data_update['wfh_foto_2'] = $edit_wfh_foto[1];
				
				$update = $this->m_persetujuan_pemesanan->update_data_kehadiran($data_update);
					
				if($update=='0')
				{
					$this->session->set_flashdata('warning',"Update Gagal");
				}else
				{
					$this->session->set_flashdata('success',"Update Berhasil, ".$update);
					
					//===== Log Start =====
					$arr_data_baru = $this->m_persetujuan_pemesanan->select_pemesanan_by_id($id,$tahun_bulan);
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
						"deskripsi" => "update ".strtolower(preg_replace("//"," ",CLASS_)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
					
				}	
				
				//tembak LEMBUR
				//update id_lembur
				$this->load->model('lembur/m_pengajuan_lembur');
				$get_lembur['no_pokok'] = $np_karyawan;
				$get_lembur['tgl_dws'] = $dws_tanggal;
				$this->m_pengajuan_lembur->set_cico($get_lembur);
				//refresh lembur fix
				$check = $this->m_pengajuan_lembur->update_dws($np_karyawan, $dws_tanggal);
				
				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'data_kehadiran'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'data_kehadiran'));	
			}	
		}
        
        function input_data_pemesanan(){
            $this->load->model("lembur/m_pengajuan_lembur");
			$this->data['menu'] = "Data Pemesanan Kendaraan";
			$this->data['id_menu'] = $this->m_setting->ambil_id_modul($this->data['menu']);
			$this->data['judul'] = "Input Pemesanan Kendaraan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
		
			$this->data["navigasi_menu"] = menu_helper();
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
            
            $this->data['content'] = $this->folder_view."input_persetujuan_pemesanan";
			
			$list_np = $this->m_pengajuan_lembur->get_np();
			$this->data["list_np"] = '<option></option>';
			foreach ($list_np as $val) {
				$this->data["list_np"] .= '<option value=\"'.$val['no_pokok'].'\">'.$val['no_pokok'].' - '.str_replace("'", " ", $val['nama']).'</option>';
			}
            
            # get kota: ambil yg jawa saja
            $get_kota = $this->m_persetujuan_pemesanan->get_kota(['010000','020000','030000','040000','050000'])->result_array();
            $this->data["list_kota"] = '<option></option>';
            foreach ($get_kota as $val) {
				$this->data["list_kota"] .= '<option value=\"'.$val['kode_wilayah'].'\">'.$val['kota'].', '.str_replace('Prop. ','',$val['prov']).'</option>';
			}
			$this->data["arr_kota"] = $get_kota;
            
			$arr_unit_kerja = array();
			
			$np_karyawan = "";
			
			if(strcmp($this->session->userdata("grup"),"4")==0){ // pengadministrasi unit kerja
				foreach($this->session->userdata("list_pengadministrasi") as $kode_unit_administrasi){
					if(strcmp(substr($kode_unit_administrasi["kode_unit"],1,1),"0")==0){
						array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,3));
					}
					else{
						array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,2));
					}
				}
			}
			else if(strcmp($this->session->userdata("grup"),"5")==0){ // pengguna
				if(strcmp(substr($this->session->userdata("kode_unit"),1,1),"0")==0){
					array_push($arr_unit_kerja,substr($this->session->userdata("kode_unit"),0,3));
				}
				else{
					array_push($arr_unit_kerja,substr($this->session->userdata("kode_unit"),0,2));
				}
				$np_karyawan = $this->session->userdata("no_pokok");
			}
			
			$arr_unit_kerja = array_unique($arr_unit_kerja);
            $arr_pemesan = $this->db->where_in('SUBSTR(kode_unit,1,2)',$arr_unit_kerja)->where('SUBSTR(kode_unit,4,2)','00')->get('mst_satuan_kerja')->result_array();
            $this->data["arr_pemesan"] = $arr_pemesan;
            //echo json_encode(['status'=>'Dev','views'=>'food_n_go/kendaraan/input_data_pemesanan','array_pemesan'=>$arr_pemesan]); exit;
            
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
            $this->load->view('template',$this->data);
			
			/*$_SERVER["PHP_SELF"] = substr_replace($_SERVER["PHP_SELF"],"pengajuan_lembur",strpos($_SERVER["PHP_SELF"],__FUNCTION__));

			$this->data['content'] = $this->folder_view."input_pengajuan_lembur";
			
			$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $this->data['id_menu'],
					"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__FUNCTION__))." : Input Pengajuan Lembur",
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
			$this->m_log->tambah($log);
			$this->load->view('template',$this->data);*/
        }
        
        function get_apv(){
            $kode_unit = $this->input->post('kode_unit');
			$this->load->model('m_approval');	
            $get_data = $this->m_approval->list_atasan_minimal_kadep([$kode_unit],null);
            echo json_encode($get_data);
        }
        
        function update_approval($id){
            //echo $id;
            $get_pesan = $this->db->where('id',$id)->get('ess_pemesanan_kendaraan')->row();
            $data=[
                'row'=>$get_pesan
            ];
            
            # update read status
            $this->db->where('id',$id)->update('ess_pemesanan_kendaraan',['is_read'=>1, 'last_read'=>date('Y-m-d H:i:s')]);
            $this->load->view($this->folder_view.'update_approval_pemesanan', $data);
        }
        
        function save_update_approval(){
            $response = [];
            $data_update = [];
            $id = $this->input->post('id',true);
            $verified = $this->input->post('verified',true);
            $catatan_atasan = $this->input->post('catatan_atasan',true);
            $verified_date = date('Y-m-d H:i:s');
            
            $data_update['verified'] = $verified;
            $data_update['verified_date'] = $verified_date;
            $data_update['catatan_atasan'] = ($verified==2?$catatan_atasan:null);
            $process = $this->db->where('id',$id)->update('ess_pemesanan_kendaraan',$data_update);
            if($process){
                $response['status']=true;
                $response['message']='Approval telah diupdate';
            } else{
                $response['status']=false;
                $response['message']='Gagal saat mengudate';
            }
            
            echo json_encode($response);
        }
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */