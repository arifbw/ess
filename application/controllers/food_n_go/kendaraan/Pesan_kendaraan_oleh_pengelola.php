<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pesan_kendaraan_oleh_pengelola extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'food_n_go/kendaraan/pengelola/';
			$this->folder_model = 'kendaraan/pengelola/';
			$this->folder_controller = 'food_n_go/kendaraan/';
			
			$this->akses = array();
			
			$this->load->helper("tanggal_helper");
            $this->load->helper("karyawan_helper");
			$this->load->helper('form');
			$this->load->helper('kendaraan');
			
			$this->load->model($this->folder_model."/M_tabel_data_pemesanan");
			$this->load->model($this->folder_model."/m_data_pemesanan");
            $this->load->model("M_dashboard","dashboard");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Pesan Kendaraan oleh Pengelola";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			//$this->output->enable_profiler(true);
		}
        
        public function index() {
			//	echo _FILE_ . _LINE_;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pesan_kendaraan_oleh_pengelola";
			
			//ambil tahun bulan tabel yang tersedia
			$array_tahun_bulan = array();
			
			$nama_db = $this->db->database;
            
            $get_month = $this->db->select('tanggal_berangkat')->group_by('YEAR(tanggal_berangkat), MONTH(tanggal_berangkat)')->order_by('YEAR(tanggal_berangkat) DESC, MONTH(tanggal_berangkat) DESC')->get($nama_db.'.ess_pemesanan_kendaraan')->result();
            
            foreach($get_month as $row){
                $array_tahun_bulan[] = date('m-Y', strtotime($row->tanggal_berangkat));
            }
			
			$this->data['array_tahun_bulan'] = $array_tahun_bulan;
			
			$this->load->view('template',$this->data);	
		}
        
        public function tabel_data_pemesanan($tampil_bulan_tahun = null) {
			//akses ke menu ubah				
			if(@$this->akses["ubah"]) {
				$disabled_ubah = '';
			} else {
				$disabled_ubah = 'disabled';
			}
						
			if($tampil_bulan_tahun=='') {
				$tampil_bulan_tahun = '';				
			} else {
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
            
            $var 	= $_SESSION["no_pokok"];
				
			$list = $this->M_tabel_data_pemesanan->get_datatables($var,$tampil_bulan_tahun);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
                
                $btn = '';
                
                # btn detail
                $btn .= '<button class="btn btn-default btn-xs edit_button" type="button" data-id="'.$tampil->id.'" onclick="show_detail(this)">Detail</button> ';
                
                if(@$this->akses["ubah"] && $_SESSION["grup"]==5 && $tampil->verified==0){
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
                    $btn .= '<button class="btn btn-warning btn-xs" type="button" data-id="'.$tampil->id.'" onclick="add_rate(this)">Beri nilai!</button> ';
                }
                
                # lokasi asal/jemput
                $berangkat_dari='';
                if($tampil->lokasi_jemput!=null and $tampil->lokasi_jemput!=''){
                    $berangkat_dari .= $tampil->lokasi_jemput;
                }
                if($tampil->nama_kota_asal!=null and $tampil->nama_kota_asal!=''){
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
				$row[] = $tampil->nama_mst_kendaraan!=null?$tampil->nama_mst_kendaraan:$tampil->jenis_kendaraan_request;
				$row[] = '<small><i>Diinput oleh pengelola transportasi.</i></small>'; //$tampil->verified_by_nama;
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
        
        function input_data_pemesanan(){
            //echo 'Dalam pengembangan. <br><a href="'.base_url('food_n_go/kendaraan/pesan_kendaraan_oleh_pengelola').'">Kembali</a>'; exit;
            //echo json_encode($this->akses);exit;
            
			$this->data['menu'] = "Pesan Kendaraan oleh Pengelola";
			$this->data['id_menu'] = $this->m_setting->ambil_id_modul($this->data['menu']);
			$this->data['judul'] = "Pesan Kendaraan oleh Pengelola";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
		
			$this->data["navigasi_menu"] = menu_helper();
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
            
            $this->data['content'] = $this->folder_view."input_data_pemesanan";
            
            # get kota: ambil yg jawa saja
            $get_kota = $this->m_data_pemesanan->get_kota(['010000','020000','030000','040000','050000'])->result_array();
            $this->data["list_kota"] = '<option></option>';
            foreach ($get_kota as $val) {
				$this->data["list_kota"] .= '<option value=\"'.$val['kode_wilayah'].'\">'.$val['kota'].', '.str_replace('Prop. ','',$val['prov']).'</option>';
			}
			$this->data["arr_kota"] = $get_kota;
            
			$arr_unit_kerja = array();
			
			$np_karyawan = "";
            
            $arr_unit_pic = [];
            
            $arr_unit_kerja[] = $_SESSION['kode_unit'];
            $np_karyawan = $this->session->userdata("no_pokok");
            $this->data['np_pic'] = $this->dashboard->getKaryawan($np_karyawan);
			
			$arr_unit_kerja = array_unique($arr_unit_kerja);
            $arr_pemesan = $this->db->select('object_abbreviation as kode_unit, object_name as nama_unit')->where("(LENGTH(object_abbreviation)=5 AND level<=6 AND object_type='O' AND object_abbreviation NOT IN ('00000','99997','99999'))")->order_by('object_abbreviation')->get('ess_sto')->result_array();
            $this->data["arr_pemesan"] = $arr_pemesan;
			
			/*$this->data["list_unit_kerja"] = '<option></option>';
			foreach ($list_unit_kerja as $val) {
				$this->data["list_unit_kerja"] .= '<option value=\"'.$val['kode_unit'].'\">'.$val['kode_unit'].' - '.$val['nama_unit'].'</option>';
			}*/
            
            # jenis kendaraan
            $get_jenis_kendaraan = $this->db->distinct()->select('nama')->get('mst_kendaraan')->result();
            $this->data["jenis_kendaraan"] = $get_jenis_kendaraan;
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
        
        function action_insert_data_pemesanan(){
            //echo json_encode($this->input->post()); exit;
            
            $submit = $this->input->post('submit');
            $data_insert = [];
			if($submit) {
                
                //$tampil_bulan_tahun = $this->input->post('insert_tampil_bulan_tahun',true);
                
                $get_mst_karyawan = mst_karyawan_by_np($this->input->post('insert_np_karyawan',true));
                $np_karyawan = $this->input->post('insert_np_karyawan',true);
                $nama = $this->input->post('insert_nama',true);
                $tanggal = $this->input->post('tanggal_berangkat',true);
                /*$verified_by = $this->input->post('verified_by',true);
                $explode = explode(' - ',$verified_by);
                $verified_by_np = $explode[0];
                $verified_by_nama = $explode[1];*/
                
                $np_karyawan_pic = $this->input->post('np_karyawan_pic',true);
                $explode_pic = explode(' - ',$np_karyawan_pic);
								
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
                $data_insert['no_ext_pic'] = $this->input->post('no_ext_pic',true);
				//$data_insert['tujuan'] = $this->input->post('tujuan',true);
				$data_insert['unit_pemroses'] = $this->input->post('unit_pemroses',true);
				$data_insert['jenis_kendaraan_request'] = $this->input->post('jenis_kendaraan_request',true);
				$data_insert['kode_kota_asal'] = $this->input->post('kode_kota_asal',true);
				$data_insert['nama_kota_asal'] = $this->input->post('nama_kota_asal',true);
				$data_insert['lokasi_jemput'] = $this->input->post('lokasi_jemput',true);
				$data_insert['keterangan'] = $this->input->post('keterangan',true);
				$data_insert['created'] = date('Y-m-d H:i:s');
				$data_insert['verified'] = 1;
				$data_insert['verified_date'] = date('Y-m-d');
				/*$data_insert['verified_by_np'] = $verified_by_np;
				$data_insert['verified_by_nama'] = $verified_by_nama;*/
				$data_insert['is_read'] = 1;
				$data_insert['nomor_pemesanan'] = generate_nomor_pemesanan();
				$data_insert['kode'] = $this->uuid->v4();
				$data_insert['insert_as_pengelola'] = 1;
                
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
				redirect(base_url($this->folder_controller.'pesan_kendaraan_oleh_pengelola'));				
				
			} else {
				$this->session->set_flashdata('warning',"Terjadi Kesalahan");
				redirect(base_url($this->folder_controller.'pesan_kendaraan_oleh_pengelola'));	
			}
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
            
            # update read status
            $this->db->where('id',$id)->update('ess_pemesanan_kendaraan',['is_read'=>1, 'last_read'=>date('Y-m-d H:i:s')]);
            $this->load->view('food_n_go/kendaraan/detail_pemesanan', $data);
        }
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */