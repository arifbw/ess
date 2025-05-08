<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Data_pemesanan extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_data_pemesanan");
			$this->load->model("M_dashboard","dashboard");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Data Pemesanan Kendaraan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			//$this->output->enable_profiler(true);
		}
		
		public function index()
		{			
			//	echo _FILE_ . _LINE_;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_pemesanan";
					
			
			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			
			$nama_db = $this->db->database;
            
            $get_month = $this->db->select('tanggal_berangkat')->group_by('YEAR(tanggal_berangkat), MONTH(tanggal_berangkat)')->order_by('YEAR(tanggal_berangkat) DESC, MONTH(tanggal_berangkat) DESC')->get($nama_db.'.ess_pemesanan_kendaraan')->result();
            
            foreach($get_month as $row){
                $array_tahun_bulan[] = date('m-Y', strtotime($row->tanggal_berangkat));
            }
            
			/*$query = $this->db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = '$nama_db' AND table_name like '%ess_cico_%' GROUP BY table_name ORDER BY table_name DESC;");
            
            $get_master_data = $this->db->select("min(TABLE_NAME) as minn, max(TABLE_NAME) as maxx")->where('TABLE_SCHEMA',$nama_db)->like('TABLE_NAME','erp_master_data_20','AFTER')->order_by('TABLE_NAME','ASC')->get('information_schema.TABLES')->row();
            if($get_master_data->minn!=NULL){
                $th_min = substr($get_master_data->minn,16,4);
                $bl_min = substr($get_master_data->minn,21,2);
                $this->data['minDate'] = date("$th_min/$bl_min/01");
            }
            if($get_master_data->maxx!=NULL){
                $th_max = substr($get_master_data->maxx,16,4);
                $bl_max = substr($get_master_data->maxx,21,2);
                $this->data['maxDate'] = date("$th_max/$bl_max/t");
            }
            
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['table_name'],-2);
				$tahun = substr($data['table_name'],9,4);
				
				$bulan_tahun = $bulan."-".$tahun;				
				
				$array_tahun_bulan[] = $bulan_tahun; 
			}*/
				
			//ambil mst dws	
			
			$array_jadwal_kerja 	= $this->m_data_pemesanan->select_mst_karyawan_aktif();
			$array_daftar_karyawan	= $this->m_data_pemesanan->select_daftar_karyawan();
			$array_daftar_unit		= $this->m_data_pemesanan->select_daftar_unit();
			
			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;	
			$this->data['array_jadwal_kerja'] 		= $array_jadwal_kerja;
			$this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
			$this->data['array_daftar_unit'] 		= $array_daftar_unit;
			//echo json_encode($this->data['array_tahun_bulan']);exit();
			$this->data['class_name'] = __CLASS__;
			$this->load->view('template',$this->data);	
		}
		
		public function ajax_getListNp()
		{			
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
				$np_list=$this->m_data_pemesanan->select_np_by_kode_unit($list_kode_unit);						
								
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
			$nama			= nama_karyawan_by_np($np_karyawan);
			
			if ($nama) 
			{				
				echo $nama; 			
			}else			
			{						 
				echo '';
			}			
		}
		
		public function ajax_getAtasanKehadiran(){
            echo $this->getAtasanKehadiran($this->input->post('vnp_karyawan'));
		}
		
		private function getAtasanKehadiran($np_karyawan){
			$this->load->model("master_data/m_karyawan");
			$karyawan = $this->m_karyawan->get_posisi_karyawan($np_karyawan);
			
			if(empty($karyawan)){
				$periode = date("Y_m");
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				
				if(empty($karyawan)){
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
				
			}while(empty($np_atasan) && strlen(preg_replace("/0+$/","",$kode_unit_atasan))>1);
			
			return $np_atasan;
		}
		
		public function ajax_getPilihanAtasanKehadiran(){
			
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			$periode = null;
			
			$np_karyawan = $this->input->post('vnp_karyawan');
			
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			$pisah = explode('#',$np_karyawan);
			$np_karyawan = $pisah[0];
			$periode     = $pisah[1];
			
			$pisah_periode = explode('-',$periode);
			$d = $pisah_periode[0];
			$m = $pisah_periode[1];
			$y = $pisah_periode[2];
			
			$periode = $y.'_'.$m;
			$periode_tanggal 	= $y.'-'.$m.'-'.$d;
			
			//jika tidak ada tanggal terpilih maka pake tanggal sekarang
			if(!$periode_tanggal)
			{
				$periode_tanggal=date('Y-m-d');
			}
			
			//$np_karyawan = $vnp_karyawan;
			$this->load->model("master_data/m_karyawan");
			//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
			//$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);			
			//20-01-2020 - 7648 Tri Wibowo menambah periode tanggal per tanggal dws karyawan tersebut
			$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
			
			if(empty($karyawan)){
				//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
				if($periode==null)
				{
					$periode = date("Y_m");
				}
				$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				
				if(empty($karyawan)){
					//2-12-2020 - 7648 Tri Wibowo menambah periode sehingga atasanya pas saat periode tersebut
					if($periode==null)
					{
						$periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
					}
					$karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan,$periode);
				}
			}
			
			if(strcmp(substr($karyawan["kode_unit"],1,1),"0")==0){
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,3);
			}
			else{
				$karyawan["kode_unit"] = substr($karyawan["kode_unit"],0,2);
			}
			
			$this->load->model("lembur/m_pengajuan_lembur");
            $arr_pilihan = $this->m_pengajuan_lembur->get_apv(array($karyawan["kode_unit"]),$np_karyawan);
			echo json_encode($arr_pilihan);
		}
		
		public function tabel_data_pemesanan($tampil_bulan_tahun = null) {
			$this->load->model($this->folder_model."/M_tabel_data_pemesanan");
			
			//akses ke menu ubah				
			if($this->akses["ubah"]) //jika pengguna
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
				$var=array();
				$var_unit=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) {
					$var_unit[] = $data['kode_unit'];
				}
                
                if($var_unit!=[]){
                    $get_list = $this->db->select('GROUP_CONCAT(no_pokok) as np')->where_in('kode_unit',$var_unit)->get('mst_karyawan')->row();
                    if($get_list->np!=null){
                        $var = explode(',',$get_list->np);
                        $var[] = $_SESSION["no_pokok"];
                    } else{
                        $var = 1;
                    }
                } else{
                    $var = 1;
                }
			}else
			if($_SESSION["grup"]==5) //jika Pengguna
			{
				$var 	= $_SESSION["no_pokok"];
				
			}else
			{
				$var = 1;
			}			
				
			$list = $this->M_tabel_data_pemesanan->get_datatables($var,$tampil_bulan_tahun);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
                
                $btn = '';
                
                # btn detail
                $btn .= '<button class="btn btn-default btn-xs edit_button" type="button" data-id="'.$tampil->id.'" onclick="show_detail(this)">Detail</button> ';
                
                if($this->akses["ubah"] && $_SESSION["grup"]==5 && $tampil->verified==0){
                    /*$btn .= "<a class='btn btn-primary btn-xs edit_button' data-toggle='modal' data-target='#modal_ubah'
                        data-id='$tampil->id'
						data-nama='$tampil->nama'
						data-np-karyawan='$tampil->np_karyawan'
						data-no-hp='$tampil->no_hp_pemesan'
						data-tanggal='".date('d-m-Y', strtotime($tampil->tanggal_berangkat))."'
						data-tujuan='$tampil->tujuan'
						data-lokasi-jemput='$tampil->lokasi_jemput'
						data-jumlah-penumpang='$tampil->jumlah_penumpang'
						data-keterangan='$tampil->keterangan'
						data-jam='".date('H:i', strtotime($tampil->jam))."'
                        >Ubah</a>";*/
                }

				if($this->akses["hapus"] /*&& $_SESSION["grup"]==5*/ && $tampil->verified==0 && $tampil->status_persetujuan_admin==null && $tampil->pesanan_selesai==null && $tampil->is_canceled_by_admin=='0'){
					$btn .= '<button class="btn btn-danger btn-xs cancel_button" type="button" data-id="'.$tampil->id.'">Batalkan</button> ';
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
                
                if (strpos($status, 'Jalan') !== false && $tampil->rating_driver==null) {
                    // $btn .= '<button class="btn btn-warning btn-xs" type="button" data-id="'.$tampil->id.'" onclick="add_rate(this)">Beri nilai!</button> ';
                }
                
                /*if($tampil->verified==1 && $tampil->id_mst_kendaraan!=null && in_array($tampil->status_persetujuan_admin,[1]) && $tampil->pesanan_selesai!=1 && $tampil->rating_driver!=null){
                    $btn .= '<button class="btn btn-danger btn-xs" type="button" data-kode="'.$tampil->kode.'" onclick="selesaikan(this)">Selesaikan</button> ';
                }*/
                
                # lokasi asal/jemput
                $berangkat_dari='';
                if($tampil->lokasi_jemput!=null && $tampil->lokasi_jemput!=''){
                    $berangkat_dari .= $tampil->lokasi_jemput;
                }
                if($tampil->nama_kota_asal!=null && $tampil->nama_kota_asal!=''){
                    $berangkat_dari .= '<br><small>('.$tampil->nama_kota_asal.')</small>';
                }
                
				$row = array();
				$row[] = $no;
				$row[] = $tampil->nomor_pemesanan;
				$row[] = $tampil->no_hp_pemesan.'<br>'.$tampil->np_karyawan.' - '.$tampil->nama.'<br><small>('.$tampil->nama_unit_pemesan.')</small>';
				$row[] = $berangkat_dari;
				$row[] = get_pemesanan_tujuan_small($tampil->id);
				$row[] = hari_tanggal($tampil->tanggal_berangkat).' @'.$tampil->jam;
				$row[] = $tampil->jumlah_penumpang;
				// $row[] = $tampil->nama_mst_kendaraan!=null?$tampil->nama_mst_kendaraan:$tampil->jenis_kendaraan_request;
				if($this->session->userdata('grup')!='5'){
					$row[] = $tampil->verified_by_nama;
				}
				$row[] = $status;
				$row[] = $btn;
								
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_data_pemesanan->count_all($var,$tampil_bulan_tahun),
							"recordsFiltered" => $this->M_tabel_data_pemesanan->count_filtered($var,$tampil_bulan_tahun),
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
                
                $np_karyawan_pic = $this->input->post('np_karyawan_pic',true);
                $explode_pic = explode(' - ',$np_karyawan_pic);
				$nama_departemen_pic = null;
				$get_kode_unit_pic = $this->db->select('kode_unit')->where('no_pokok',$explode_pic[0])->get('mst_karyawan')->row();
				if($get_kode_unit_pic){
					$kode_unit_pic = $get_kode_unit_pic->kode_unit;
					if(substr($kode_unit_pic, 2,1)!='0'){
						$get_dept_pic = $this->db->select('object_name_lengkap')->where('object_type','O')->where('object_abbreviation',substr($kode_unit_pic, 0,3).'00')->get('ess_sto')->row();
						if($get_dept_pic) $nama_departemen_pic = $get_dept_pic->object_name_lengkap;
					}
				}
								
				$data_insert['np_karyawan'] = $np_karyawan;
				$data_insert['nama'] = $nama;
				$data_insert['no_hp_pemesan'] = $this->input->post('no_hp',true);
				$data_insert['kode_unit'] = $get_mst_karyawan['kode_unit'];
				$data_insert['nama_unit'] = $get_mst_karyawan['nama_unit'];
				$data_insert['kode_unit_pemesan'] = $this->input->post('kode_unit_pemesan',true);
				$data_insert['nama_unit_pemesan'] = $this->input->post('nama_unit_pemesan',true);
				$data_insert['no_ext_pemesan'] = $this->input->post('no_ext_pemesan',true);
				$data_insert['jumlah_penumpang'] = $this->input->post('jumlah_penumpang',true);
				$data_insert['tanggal_berangkat'] = date('Y-m-d', strtotime($tanggal));
				$data_insert['jam'] = $this->input->post('jam',true);
                $data_insert['np_karyawan_pic'] = $explode_pic[0];
                $data_insert['nama_pic'] = $explode_pic[1];
                $data_insert['no_hp_pic'] = $this->input->post('no_hp_pic',true);
                // $data_insert['no_ext_pic'] = $this->input->post('no_ext_pic',true);
				//$data_insert['tujuan'] = $this->input->post('tujuan',true);
				$data_insert['unit_pemroses'] = $this->input->post('unit_pemroses',true);
				// $data_insert['jenis_kendaraan_request'] = $this->input->post('jenis_kendaraan_request',true);
				$data_insert['kode_kota_asal'] = $this->input->post('kode_kota_asal',true);
				$data_insert['nama_kota_asal'] = $this->input->post('nama_kota_asal',true);
				$data_insert['lokasi_jemput'] = $this->input->post('lokasi_jemput',true);
				$data_insert['keterangan'] = $this->input->post('keterangan',true);
				$data_insert['created'] = date('Y-m-d H:i:s');
				$data_insert['verified'] = 0;
				$data_insert['verified_by_np'] = $verified_by_np;
				$data_insert['verified_by_nama'] = $verified_by_nama;
				$data_insert['is_read'] = 0;
				$data_insert['nomor_pemesanan'] = generate_nomor_pemesanan();
				$data_insert['kode'] = $this->uuid->v4();
				$data_insert['nama_departemen_pic'] = $nama_departemen_pic;
                
                # ket_list
                $ket_list = $this->input->post('ket_list',true);
                switch ($ket_list) {
                    case "1": // sekali jalan
                        $data_insert['is_pp']=0;
                        $data_insert['is_inap']=0;
                        break;
                    case "2": // pp + range tanggal
                        $data_insert['is_pp']=1;
                        $data_insert['is_inap']=0;
                        $data_insert['tanggal_awal'] = date('Y-m-d', strtotime($this->input->post('tanggal_awal',true)));
                        $data_insert['tanggal_akhir'] = date('Y-m-d', strtotime($this->input->post('tanggal_akhir',true)));
                        break;
                    case "3": // menginap > otomatis pp + range tanggal
                        $data_insert['is_pp']=1;
                        $data_insert['is_inap']=1;
                        $data_insert['tanggal_awal'] = date('Y-m-d', strtotime($this->input->post('tanggal_awal',true)));
                        $data_insert['tanggal_akhir'] = date('Y-m-d', strtotime($this->input->post('tanggal_akhir',true)));
                        break;
                    default:
                        $data_insert['is_pp']=0;
                        $data_insert['is_inap']=0;
                }
                
                /*$data_insert['is_pp']=(@$this->input->post('is_pp',true)=='on'?1:0);
                
                if(@$this->input->post('is_inap',true)=='on'){
                    $data_insert['is_inap']=1;
                    $data_insert['tanggal_awal'] = date('Y-m-d', strtotime($this->input->post('tanggal_awal',true)));
                    $data_insert['tanggal_akhir'] = date('Y-m-d', strtotime($this->input->post('tanggal_akhir',true)));
                } else{
                    $data_insert['is_inap']=0;
                }*/
				
				$insert = $this->m_data_pemesanan->insert_data_pemesanan($data_insert);
					
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
                                'kode'=>$this->uuid->v4(),
                                'kode_kota_tujuan'=>$arr_kode_kota_tujuan[$i],
                                'nama_kota_tujuan'=>$this->m_data_pemesanan->get_nama_kota_by_kode($arr_kode_kota_tujuan[$i]),
                                'keterangan_tujuan'=>$arr_keterangan_tujuan[$i],
                                'status'=>1
                            ];
                        }
                        $this->db->insert_batch('ess_pemesanan_kendaraan_kota',$arr_insert_tujuan);
                    }
                    # END insert kota tujuan
                    
					$this->session->set_flashdata('success',"Berhasil, data pemesanan kendaraan <b>$np_karyawan | $nama</b> tanggal <b>$tanggal</b> telah diajukan");
					
					//===== Log Start =====
					$arr_data_baru = $this->m_data_pemesanan->select_pemesanan_by_id($insert);					
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
				$arr_data_lama = $this->m_data_pemesanan->select_pemesanan_by_id($id,$tahun_bulan);
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
				
				$update = $this->m_data_pemesanan->update_data_kehadiran($data_update);
					
				if($update=='0')
				{
					$this->session->set_flashdata('warning',"Update Gagal");
				}else
				{
					$this->session->set_flashdata('success',"Update Berhasil, ".$update);
					
					//===== Log Start =====
					$arr_data_baru = $this->m_data_pemesanan->select_pemesanan_by_id($id,$tahun_bulan);
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
		
		public function action_update_kode_unit()
		{			
			//echo json_encode($this->input->post()); exit();
            $submit = $this->input->post('submit');
					
					
			if($submit)
			{					
				$id 				= $this->input->post('kd_id');
				$np_karyawan 		= $this->input->post('kd_np_karyawan');
				$nama 				= $this->input->post('kd_nama');
				$dws_tanggal 		= date('Y-m-d', strtotime($this->input->post('kd_dws_tanggal')));
				$dws_name 			= $this->input->post('kd_dws_name');			
				$kode_unit 			= $this->input->post('kd_kode_unit');			
							
				$tampil_bulan_tahun	= $this->input->post('kd_tampil_bulan_tahun');
				
				$bulan	= substr($tampil_bulan_tahun,0,2);
				$tahun 	= substr($tampil_bulan_tahun,3,4);
				$tahun_bulan		= $tahun."_".$bulan;
												
				//===== Log Start =====
				$arr_data_lama = $this->m_data_pemesanan->select_pemesanan_by_id($id,$tahun_bulan);
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
				
				$data_update['id'] 			= $id;
				$data_update['np_karyawan'] = $np_karyawan;
				$data_update['nama'] 		= $nama;
				$data_update['dws_tanggal'] = $dws_tanggal;
				$data_update['kode_unit'] 	= $kode_unit;
				$data_update['tahun_bulan'] = $tahun_bulan;
											
				$update = $this->m_data_pemesanan->update_kode_unit($data_update);
					
				if($update=='0')
				{
					$this->session->set_flashdata('warning',"Update Gagal");
				}else
				{
					$this->session->set_flashdata('success',"Update Berhasil, ".$update);
					
					//===== Log Start =====
					$arr_data_baru = $this->m_data_pemesanan->select_pemesanan_by_id($id,$tahun_bulan);
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
											
				$this->session->set_flashdata('tampil_bulan_tahun',$tampil_bulan_tahun);
				redirect(base_url($this->folder_controller.'data_kehadiran'));				
				
			}else
			{
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				
				redirect(base_url($this->folder_controller.'data_kehadiran'));	
			}	
		}
		
		public function cetak() {
			$this->load->library('phpexcel'); 
			$this->load->model($this->folder_model."/M_tabel_data_pemesanan");

			$set['np_karyawan'] = $this->input->post('np_karyawan');
			$tampil_bulan_tahun = $this->input->post('bulan');
			
			if($tampil_bulan_tahun=='')
				$tampil_bulan_tahun = '';				
			else {
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
			
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja		
				$ada_data=0;
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var,$data['kode_unit']);
					$ada_data=1;
				}

				if($ada_data==0)
					$var='';
			}
			else if($_SESSION["grup"]==5) //jika Pengguna
				$var 	= $_SESSION["no_pokok"];
			else
				$var = 1;
			$get_data = $this->M_tabel_data_pemesanan->_get_excel($var,$tampil_bulan_tahun,$set);
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Data_kehadiran.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_data_kehadiran.xlsx');

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 4;
	        $no = 1;

			foreach ($get_data as $tampil) {
				$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, date('d-m-Y', strtotime($tampil->dws_tanggal)), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->dws_name_fix==null || $tampil->dws_name_fix=='')
	            	$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, strtoupper(nama_dws_by_kode($tampil->dws_name)), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, strtoupper(nama_dws_by_kode($tampil->dws_name_fix)), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='') {
					$tapping_1 = $tampil->tapping_time_1;
					if($tapping_1) {
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						$machine_id_1			= '';
					}
					else {
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}
				}
				else {
					$tapping_1					= $tampil->tapping_fix_1;
					if($tapping_1) {
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						$machine_id_1			= '';
					}
					else {
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}					
				}
				
				if($tapping_1)
	            	$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ucwords(tanggal_indonesia(substr($tapping_1,0,10))." ".substr($tapping_1,10,6)." ".$machine_id_1), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ' ', PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->tapping_fix_2==null || $tampil->tapping_fix_2=='') {
					$tapping_2  				= $tampil->tapping_time_2;
					if($tapping_2) {
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						//$machine_id_2			= "<br>Machine id : ".$tampil->tapping_terminal_2;
						$machine_id_2			= '';
					}
					else {
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}					
				}
				else {					
					$tapping_2 					= $tampil->tapping_fix_2;
					if($tapping_2) {
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						$machine_id_2			= "";
					}
					else {
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}		
				}
				
				if($tapping_2)
	            	$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, ucwords(tanggal_indonesia(substr($tapping_2,0,10))." ".substr($tapping_2,10,6)."  ".$machine_id_2), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
				
				$tampil_keterangan 	= '';
				$hari_libur 		= hari_libur_by_tanggal($tampil->dws_tanggal);
								
				if($hari_libur) {
					if($tampil_keterangan=='')
						$tampil_keterangan = $hari_libur;
					else
						$tampil_keterangan = $tampil_keterangan."<br><br>".$hari_libur;
				}
				
				$hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
								
				//7648 Tri Wibowo, 6 Januari 2019 - ketika sudah di pembatalan maka tidak tampil
				$hari_pembatalan =  $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE is_cuti_bersama='1' AND date='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
				//jika ada pembatalan
				$id_cuti_bersama = null;
				if($hari_pembatalan['id']==null)
				{
					$id_cuti_bersama = $hari_cuti_bersama['id'];
				}
				
				if($tampil->id_cuti) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Cuti';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Cuti';
				}
				else if($tampil->id_sppd) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Dinas';
					else
						$tampil_keterangan = $tampil_keterangan.". >".'Dinas';
				}
				else if($id_cuti_bersama) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Cuti Bersama';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Cuti Bersama';
				}
				else{
					$id_perizinan=explode(",",$tampil->id_perizinan);
					$isi='';
					foreach($id_perizinan as $value) {
						$tahun_bulan = substr($tampil->dws_tanggal,0,7);
						$tahun_bulan = str_replace('-','_',$tahun_bulan);
						$izin = perizinan_by_id($tahun_bulan,$value);
						$kode_erp = $izin['info_type']."|".$izin['absence_type'];
						$nama_perizinan=nama_perizinan_by_kode_erp($kode_erp);
						if($nama_perizinan)
							$isi=$isi."".$nama_perizinan.". ";		
					}
					
					if(!$hari_libur) {
						if($tampil_keterangan=='') {
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) || (strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {
								$tampil_keterangan =$isi;
							}
							else {	
								if($tampil->keterangan)
									$tampil_keterangan = $tampil->keterangan.". ".$isi;
								else
									$tampil_keterangan =$isi;	
							}
						}
						else {
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
								(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {								
								$tampil_keterangan = $tampil_keterangan.". ".$isi;
							}
							else {
								if($tampil->keterangan)
									$tampil_keterangan = $tampil_keterangan.". ".$tampil->keterangan.". ".$isi;	
								else
									$tampil_keterangan = $tampil_keterangan.". ".$isi;
							}
						}
					}										
				}
				
				if($tampil->wfh==1)
				{
					$tampil_keterangan = "Work From Home".". ".$tampil_keterangan;
				}
				
				$excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $tampil_keterangan, PHPExcel_Cell_DataType::TYPE_STRING);
	            $awal += 1;	
			}

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
		}
		
		public function cetak_per_unit() {
			$this->load->library('phpexcel'); 
			$this->load->model($this->folder_model."/M_tabel_data_pemesanan");

			$set['kode_unit'] = $this->input->post('kode_unit');
			$tampil_bulan_tahun = $this->input->post('bulan');
			
			if($tampil_bulan_tahun=='')
				$tampil_bulan_tahun = '';				
			else {
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
			
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja		
				$ada_data=0;
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					array_push($var,$data['kode_unit']);
					$ada_data=1;
				}

				if($ada_data==0)
					$var='';
			}
			else if($_SESSION["grup"]==5) //jika Pengguna
				$var 	= $_SESSION["no_pokok"];
			else
				$var = 1;
			$get_data = $this->M_tabel_data_pemesanan->_get_excel_per_unit($var,$tampil_bulan_tahun,$set);
	        error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);

	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Data_kehadiran.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_data_kehadiran.xlsx');

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 4;
	        $no = 1;

			foreach ($get_data as $tampil) {
				$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('C'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, date('d-m-Y', strtotime($tampil->dws_tanggal)), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->dws_name_fix==null || $tampil->dws_name_fix=='')
	            	$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, strtoupper(nama_dws_by_kode($tampil->dws_name)), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, strtoupper(nama_dws_by_kode($tampil->dws_name_fix)), PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='') {
					$tapping_1 = $tampil->tapping_time_1;
					if($tapping_1) {
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						//$machine_id_1			= "<br>Machine id : ".$tampil->tapping_terminal_1;
						$machine_id_1			= '';
					}
					else {
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}
				}
				else {
					$tapping_1					= $tampil->tapping_fix_1;
					if($tapping_1) {
						$pisah_tapping_1		= explode(' ',$tapping_1);
						$tapping_1_value_date	= $pisah_tapping_1[0];
						$tapping_1_value_time	= $pisah_tapping_1[1];
						$tapping_1_value_time   = substr($tapping_1_value_time,0,5);
						$machine_id_1			= '';
					}
					else {
						$tapping_1_value_date	= $tampil->dws_tanggal;
						$tapping_1_value_time	= '';
					}					
				}
				
				if($tapping_1)
	            	$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ucwords(tanggal_indonesia(substr($tapping_1,0,10))." ".substr($tapping_1,10,6)." ".$machine_id_1), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ' ', PHPExcel_Cell_DataType::TYPE_STRING);
				
				if($tampil->tapping_fix_2==null || $tampil->tapping_fix_2=='') {
					$tapping_2  				= $tampil->tapping_time_2;
					if($tapping_2) {
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						//$machine_id_2			= "<br>Machine id : ".$tampil->tapping_terminal_2;
						$machine_id_2			= '';
					}
					else {
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}					
				}
				else {					
					$tapping_2 					= $tampil->tapping_fix_2;
					if($tapping_2) {
						$pisah_tapping_2		= explode(' ',$tapping_2);
						$tapping_2_value_date	= $pisah_tapping_2[0];
						$tapping_2_value_time	= $pisah_tapping_2[1];
						$tapping_2_value_time   = substr($tapping_2_value_time,0,5);
						$machine_id_2			= "";
					}
					else {
						$tapping_2_value_date	= $tampil->dws_tanggal;
						$tapping_2_value_time	= '';
					}		
				}
				
				if($tapping_2)
	            	$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, ucwords(tanggal_indonesia(substr($tapping_2,0,10))." ".substr($tapping_2,10,6)."  ".$machine_id_2), PHPExcel_Cell_DataType::TYPE_STRING);
				else
	            	$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, '', PHPExcel_Cell_DataType::TYPE_STRING);
				
				$tampil_keterangan 	= '';
				$hari_libur 		= hari_libur_by_tanggal($tampil->dws_tanggal);
								
				if($hari_libur) {
					if($tampil_keterangan=='')
						$tampil_keterangan = $hari_libur;
					else
						$tampil_keterangan = $tampil_keterangan."<br><br>".$hari_libur;
				}
				
				$hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
								
				//7648 Tri Wibowo, 6 Januari 2019 - ketika sudah di pembatalan maka tidak tampil
				$hari_pembatalan =  $this->db->query("SELECT * FROM ess_pembatalan_cuti WHERE is_cuti_bersama='1' AND date='$tampil->dws_tanggal' AND np_karyawan='$tampil->np_karyawan' LIMIT 1")->row_array();
				//jika ada pembatalan
				$id_cuti_bersama = null;
				if($hari_pembatalan['id']==null)
				{
					$id_cuti_bersama = $hari_cuti_bersama['id'];
				}
				
				if($tampil->id_cuti) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Cuti';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Cuti';
				}
				else if($tampil->id_sppd) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Dinas';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Dinas';
				}
				else if($id_cuti_bersama) {
					if($tampil_keterangan=='')
						$tampil_keterangan = 'Cuti Bersama';
					else
						$tampil_keterangan = $tampil_keterangan.". ".'Cuti Bersama';
				}
				else{
					$id_perizinan=explode(",",$tampil->id_perizinan);
					$isi='';
					foreach($id_perizinan as $value) {
						$tahun_bulan = substr($tampil->dws_tanggal,0,7);
						$tahun_bulan = str_replace('-','_',$tahun_bulan);
						$izin = perizinan_by_id($tahun_bulan,$value);
						$kode_erp = $izin['info_type']."|".$izin['absence_type'];
						$nama_perizinan=nama_perizinan_by_kode_erp($kode_erp);
						if($nama_perizinan)
							$isi=$isi."".$nama_perizinan."<br><br>";		
					}
					
					if(!$hari_libur) {
						if($tampil_keterangan=='') {
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) || (strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {
								$tampil_keterangan =$isi;
							}
							else {	
								if($tampil->keterangan)
									$tampil_keterangan = $tampil->keterangan.". ".$isi;
								else
									$tampil_keterangan =$isi;	
							}
						}
						else {
							if ((strpos($isi, 'Izin Dinas Keluar Perusahaan Non-Pendidikan') !== false) || (strpos($isi, 'Izin Dinas Luar Lokasi Pendidikan') !== false) ||
								(strpos($isi, 'Izin Pribadi dengan Potongan') !== false) || (strpos($isi, 'Izin Pribadi Tanpa Potongan') !== false)) {								
								$tampil_keterangan = $tampil_keterangan.". ".$isi;
							}
							else {
								if($tampil->keterangan)
									$tampil_keterangan = $tampil_keterangan.". ".$tampil->keterangan.". ".$isi;	
								else
									$tampil_keterangan = $tampil_keterangan.". ".$isi;
							}
						}
					}										
				}
				
				if($tampil->wfh==1)
				{
					$tampil_keterangan = "Work From Home".". ".$tampil_keterangan;
				}
				
				$excel->getActiveSheet()->setCellValueExplicit('H'.$awal, $tampil_keterangan, PHPExcel_Cell_DataType::TYPE_STRING);
	            $awal += 1;	
			}

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
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
            
            $this->data['content'] = $this->folder_view."input_data_pemesanan";
			
			$list_np = $this->m_pengajuan_lembur->get_np();
			$this->data["list_np"] = '<option></option>';
			foreach ($list_np as $val) {
				$this->data["list_np"] .= '<option value=\"'.$val['no_pokok'].'\">'.$val['no_pokok'].' - '.str_replace("'", " ", $val['nama']).'</option>';
			}
            
            # get kota: ambil yg jawa saja
            $get_kota = $this->m_data_pemesanan->get_kota(['010000','020000','030000','040000','050000'])->result_array();
            $this->data["list_kota"] = '<option></option>';
            foreach ($get_kota as $val) {
				$this->data["list_kota"] .= '<option value=\"'.$val['kode_wilayah'].'\">'.$val['kota'].', '.str_replace('Prop. ','',$val['prov']).'</option>';
			}

			$filtered_kode_kab = ['016000','016100','016200','016300','016400','280300','022100','022200','026500','020800','026000','022300','020500','026100'];
			$this->data["arr_kota"] = array_values( array_filter($get_kota, function($e) use($filtered_kode_kab){
				return in_array($e['kode_wilayah'], $filtered_kode_kab);
			}) );
            
			$arr_unit_kerja = array();
			
			$np_karyawan = "";
            
            $arr_unit_pic = [];
			
			if(strcmp($this->session->userdata("grup"),"4")==0){ // pengadministrasi unit kerja
				foreach($this->session->userdata("list_pengadministrasi") as $kode_unit_administrasi){
					/*if(strcmp(substr($kode_unit_administrasi["kode_unit"],1,1),"0")==0){
						array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,3));
					}
					else{
						array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,2));
					}*/
                    if(substr($kode_unit_administrasi['kode_unit'], -2)=='00'){
                        $arr_unit_kerja[] = $kode_unit_administrasi['kode_unit'];
                    }
                    
                    $arr_unit_pic[]=$kode_unit_administrasi['kode_unit'];
				}
                $this->data['np_pic'] = $this->dashboard->getKaryawan($arr_unit_pic);
			}
			else if(strcmp($this->session->userdata("grup"),"5")==0){ // pengguna
				/*if(strcmp(substr($this->session->userdata("kode_unit"),1,1),"0")==0){
					array_push($arr_unit_kerja,substr($this->session->userdata("kode_unit"),0,3));
				}
				else{
					array_push($arr_unit_kerja,substr($this->session->userdata("kode_unit"),0,2));
				}
                foreach($this->session->userdata("list_pengadministrasi") as $kode_unit_administrasi){
                    if(substr($kode_unit_administrasi['kode_unit'], -2)=='00'){
                        $arr_unit_kerja[] = $kode_unit_administrasi['kode_unit'];
                    }
                }*/
                $arr_unit_kerja[] = $_SESSION['kode_unit'];
				$np_karyawan = $this->session->userdata("no_pokok");
                $this->data['np_pic'] = $this->dashboard->getKaryawan($np_karyawan);
			}
			
			$arr_unit_kerja = array_unique($arr_unit_kerja);
            //$arr_pemesan = $this->db->where_in('SUBSTR(kode_unit,1,2)',$arr_unit_kerja)->where('SUBSTR(kode_unit,4,2)','00')->get('mst_satuan_kerja')->result_array();
            $arr_pemesan = $this->db->where_in('kode_unit',$arr_unit_kerja)->get('mst_satuan_kerja')->result_array();
            $this->data["arr_pemesan"] = $arr_pemesan;
            //echo json_encode(['status'=>'Dev','views'=>'food_n_go/kendaraan/input_data_pemesanan','array_pemesan'=>$arr_pemesan]); exit;
            
			/*$list_apv = $this->m_pengajuan_lembur->get_apv($arr_unit_kerja,$np_karyawan);
			$this->data["list_apv"] = '<option></option>';
			foreach ($list_apv as $val) {
				$this->data["list_apv"] .= '<option value=\"'.$val['no_pokok'].'\">'.$val['no_pokok'].' - '.str_replace("'", " ", $val['nama']).'</option>';
			}*/
			
			$list_unit_kerja = $this->m_pengajuan_lembur->get_unit_kerja();
			$this->data["list_unit_kerja"] = '<option></option>';
			foreach ($list_unit_kerja as $val) {
				$this->data["list_unit_kerja"] .= '<option value=\"'.$val['kode_unit'].'\">'.$val['kode_unit'].' - '.$val['nama_unit'].'</option>';
			}
            
            # jenis kendaraan			
           //$get_jenis_kendaraan = $this->db->distinct()->select('nama')->get('mst_kendaraan')->result();
			$get_jenis_kendaraan = $this->db->distinct()->select('nama')->get_where('mst_kendaraan',array('status'=>'1'))->result();
            $this->data["jenis_kendaraan"] = $get_jenis_kendaraan;
            //echo json_encode($arr_unit_pic);exit;
            //echo json_encode($arr_unit_kerja); exit;
            //echo json_encode($_SESSION['list_pengadministrasi']); exit;
            $this->load->view('template',$this->data);
			
			/*$_SERVER["PHP_SELF"] = substr_replace($_SERVER["PHP_SELF"],"pengajuan_lembur",strpos($_SERVER["PHP_SELF"],__FUNCTION__));

			$this->data['content'] = $this->folder_view."input_pengajuan_lembur";
			
			$log = array(
					"id_pengguna" => $this->session->userdata("id_pengguna"),
					"id_modul" => $this->data['id_modul'],
					"id_target" => $this->data['id_menu'],
					"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__FUNCTION__))." : Input Pemesanan Kendaraan",
					"alamat_ip" => $this->data["ip_address"],
					"waktu" => date("Y-m-d H:i:s")
				);
			$this->m_log->tambah($log);
			$this->load->view('template',$this->data);*/
        }
        
        function get_apv(){
            $kode_unit = $this->input->post('kode_unit', true);
			$kode_unit_3_digit_pertama = substr($kode_unit, 0, 3);
			$kode_unit_2_digit_pertama = substr($kode_unit, 0, 2);
			// $this->load->model('m_approval');
            // $get_data = $this->m_approval->list_atasan_minimal_kaun([$kode_unit],null);
			$get_data = $this->db->select('no_pokok, nama, kode_unit, nama_unit, nama_unit_singkat, nama_jabatan, tanggal_pensiun, grup_jabatan, kode_jabatan')
				->group_start()
					->group_start()
						->where('grup_jabatan','KASEK')
						->or_where_in("substr(kode_jabatan,-3)",['600'])
					->group_end()
					->like('kode_unit', $kode_unit_3_digit_pertama, 'AFTER')
				->group_end()
				->or_group_start()
					->group_start()
						->where('grup_jabatan','KADEP')
						->or_where_in("substr(kode_jabatan,-3)",['400'])
					->group_end()
					->like('kode_unit', $kode_unit_2_digit_pertama, 'AFTER')
				->group_end()
				->get('mst_karyawan')->result_array();
			$map_data = array_map(function($e) use($kode_unit){
				$dept1 = substr($e['kode_unit'], 0, 3);
				$dept2 = substr($kode_unit, 0, 3);
				if($dept1==$dept2 && ( in_array($e['grup_jabatan'], ['KADEP']) || in_array(substr($e['kode_jabatan'], -3), ['400']) ) ){
					$e['as_default'] = 1;
				} else{
					$e['as_default'] = 0;
				}
				return $e;
			}, $get_data);
            echo json_encode($map_data);
        }
        
        function get_pic(){
            $kode_unit = $this->input->post('kode_unit');
            $get_data = $this->db->select('no_pokok,nama')->where_in('kode_unit', $kode_unit)->get('mst_karyawan')->result();
            echo json_encode($get_data);
        }
        
        function show_detail($id){
            //echo $id;
            $get_pesan = $this->db->where('id',$id)->get('ess_pemesanan_kendaraan')->row();
            $data=[
                'row'=>$get_pesan
            ];

			if(@$this->input->post('caller',true)) $data['class_name'] = $this->input->post('caller',true);
			$data['data'] = $data;
            
            # update read status
            $this->db->where('id',$id)->update('ess_pemesanan_kendaraan',['is_read'=>1, 'last_read'=>date('Y-m-d H:i:s')]);
            $this->load->view($this->folder_view.'detail_pemesanan', $data);
        }
        
        function selesaikan_pesanan(){
            $response = [];
            $kode = $this->input->post('kode',true);
            
            $this->db->where('kode',$kode)->update('ess_pemesanan_kendaraan',['pesanan_selesai'=>1]);
            if($this->db->affected_rows()>0){
                $response['status'] = true;
                $response['message'] = 'Pesanan telah diselesaikan';
            } else{
                $response['status'] = false;
                $response['message'] = 'Gagal saat memproses';
            }
            
            echo json_encode($response);
        }
        
        function add_rate($id){
            $get_pesan = $this->db->where('id',$id)->get('ess_pemesanan_kendaraan')->row();
            $data=[
                'row'=>$get_pesan
            ];
            
            $this->load->view($this->folder_view.'rate_driver', $data);
        }
        
        function save_rate(){
            $response = [];
            $data_update = [];
            $kode = $this->input->post('kode',true);
            $rating_driver = $this->input->post('rating_driver',true);
            $catatan_rating_driver = $this->input->post('catatan_rating_driver',true);
            
            $data_update['rating_driver'] = $rating_driver;
            if($catatan_rating_driver!=''){
                $data_update['catatan_rating_driver'] = $catatan_rating_driver;
            }
            
            $process = $this->db->where('kode',$kode)->update('ess_pemesanan_kendaraan',$data_update);
            if($process){
                $response['status']=true;
                $response['message']='Rating telah disimpan';
            } else{
                $response['status']=false;
                $response['message']='Gagal saat menyimpan';
            }
            
            echo json_encode($response);
        }

		function pemesan_batalkan_pesanan(){
            $response = [];
            $id = $this->input->post('id',true);

			$this->db
				->where('id',$id)
				->where('verified',0)
				->where('status_persetujuan_admin is null',null,false)
				->where('pesanan_selesai is null',null,false)
				->where('is_canceled_by_admin','0')
				->update('ess_pemesanan_kendaraan',['deleted_at'=>date('Y-m-d H:i:s')]);
            if($this->db->affected_rows()>0){
                $response['status'] = true;
                $response['message'] = 'Pesanan telah dibatalkan';
            } else{
                $response['status'] = false;
                $response['message'] = 'Gagal saat memproses';
            }
            
            echo json_encode($response);
        }
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */