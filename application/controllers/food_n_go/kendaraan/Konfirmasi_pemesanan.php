<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Konfirmasi_pemesanan extends CI_Controller {
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
			
			// $this->load->model($this->folder_model."m_konfirmasi_pemesanan");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Konfirmasi Pemesanan Kendaraan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			//$this->output->enable_profiler(true);
		}
		
		public function index() {
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."konfirmasi_pemesanan";
			
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
		
		public function tabel_konfirmasi_pemesanan($unit, $konfirmasi, $tampil_bulan_tahun = null) {
			$this->load->model($this->folder_model."/M_tabel_konfirmasi_pemesanan");
			
			if($_SESSION["grup"]==8){ //jika admin dafasum transport
				$var = true;
			}
            else {
				$var = false;				
			}			
				
			$list = $this->M_tabel_konfirmasi_pemesanan->get_datatables($var, $unit, $konfirmasi, $tampil_bulan_tahun);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
                
                $btn = '';
                
                # btn detail
                $btn .= '<button class="btn btn-default btn-xs edit_button" type="button" data-id="'.$tampil->id.'" onclick="show_detail(this)">Detail</button> ';
                
                if(@$this->akses["ubah"] && $_SESSION["grup"]==13 && $tampil->verified==1 && $tampil->tanggal_berangkat>=date('Y-m-d') && $tampil->id_mst_kendaraan==null && !in_array($tampil->status_persetujuan_admin,[1,2,3])){
                    // old: $btn .= '<button class="btn btn-primary btn-xs edit_button" type="button" data-id="'.$tampil->id.'" onclick="update_konfirmasi(this)">Konfirmasi</button> ';
                    $btn .= '<a class="btn btn-primary btn-xs" href="'.base_url('food_n_go/kendaraan/konfirmasi_pemesanan/update_konfirmasi_dist/'.$tampil->kode).'">Konfirmasi</a> ';
                } 
                
                if(@$this->akses["ubah"] && $_SESSION["grup"]==13 && $tampil->verified==1 && $tampil->id_mst_kendaraan!=null && in_array($tampil->status_persetujuan_admin,[1]) /*&& $tampil->pesanan_selesai!=1*/ && $tampil->is_canceled_by_admin!='1'){
                    // $btn .= '<button class="btn btn-primary btn-xs edit_button" type="button" data-kode="'.$tampil->kode.'" onclick="show_biaya(this)">Biaya Perjalanan</button> ';
                }
                
                # edit kendaraan ketika sudah terlanjur diplot
                if(@$this->akses["ubah"] && $_SESSION["grup"]==13 && $tampil->verified==1 && $tampil->id_mst_kendaraan!=null && in_array($tampil->status_persetujuan_admin,[1]) && $tampil->rating_driver==null && $tampil->pesanan_selesai!=1 && $tampil->is_canceled_by_admin!='1'){
                    $btn .= '<a class="btn btn-warning btn-xs" href="'.base_url('food_n_go/kendaraan/konfirmasi_pemesanan/update_konfirmasi_dist/'.$tampil->kode.'/edit').'">Edit Kendaraan</a> ';
                    // $btn .= '<button class="btn btn-danger btn-xs" type="button" data-kode="'.$tampil->kode.'" onclick="batalkan(this)">Batalkan</button> ';
                }
                
                if($tampil->verified==1 && $tampil->id_mst_kendaraan!=null && in_array($tampil->status_persetujuan_admin,[1]) && $tampil->pesanan_selesai!=1 && $tampil->rating_driver!=null && $tampil->submit_biaya==1){
                    $btn .= '<button class="btn btn-danger btn-xs" type="button" data-kode="'.$tampil->kode.'" onclick="selesaikan(this)">Selesaikan</button> ';
                } 
                
                # status
                $status = status_pemesanan_admin([
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
                
                # nama
                $nama_pemesan = $tampil->no_hp_pemesan.'<br>'.$tampil->np_karyawan.' - '.$tampil->nama;
                if(!in_array($tampil->status_persetujuan_admin,[1,2,3])){
                    $nama_pemesan = '<b>'.$tampil->no_hp_pemesan.'<br>'.$tampil->np_karyawan.' - '.$tampil->nama.'</b>';
                }
                
				$row = array();
				$row[] = $no;
				$row[] = $tampil->nomor_pemesanan;
				$row[] = $nama_pemesan;
				$row[] = $berangkat_dari;
				$row[] = get_pemesanan_tujuan_small($tampil->id);
				$row[] = $waktu_berangkat;
				$row[] = $tampil->jumlah_penumpang;
				// $row[] = $tampil->nama_mst_kendaraan!=null?$tampil->nama_mst_kendaraan:$tampil->jenis_kendaraan_request;
				// $row[] = $tampil->insert_as_pengelola==1 ? '<small><i>Diinput oleh pengelola transportasi.</i></small>':$tampil->verified_by_nama;
				$row[] = $status;
				$row[] = $btn;
								
				$data[] = $row;
			}

			$output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->M_tabel_konfirmasi_pemesanan->count_all($var, $unit, $konfirmasi, $tampil_bulan_tahun),
                "recordsFiltered" => $this->M_tabel_konfirmasi_pemesanan->count_filtered($var, $unit, $konfirmasi, $tampil_bulan_tahun),
                "data" => $data,
            );
			//output to json format
			echo json_encode($output);
		}        
        
        function update_konfirmasi($id){
            //echo $id; exit;
            $get_pesan = $this->db->where('id',$id)->get('ess_pemesanan_kendaraan')->row();
            
            # get driver bertugas (can't used)
            $get_driver_go = $this->db->select('GROUP_CONCAT(a.id_mst_driver) as drivers_go')->from('ess_pemesanan_kendaraan a')
                ->where("((a.tanggal_berangkat >= DATE(NOW()) AND a.tanggal_berangkat='$get_pesan->tanggal_berangkat') OR ('$get_pesan->tanggal_berangkat' BETWEEN a.tanggal_awal AND a.tanggal_akhir))")->get()->row();
            
            # get driver
            $this->db->select('id,np_karyawan,nama,jenis_sim,keterangan,id_mst_kendaraan_default')->where('status',1)->where('id_mst_kendaraan_default is not null',null,false);
            if($get_driver_go->drivers_go!=null){
                $driv = explode(',',$get_driver_go->drivers_go);
                $this->db->where_not_in('id',$driv);
            }
            $get_driver = $this->db->get('mst_driver');
            
            $get_mst_kendaraan = $this->db->where('status',1)->get('mst_kendaraan');
            $data=[
                'row'=>$get_pesan,
                'driver'=>$get_driver->result(),
                'mst_kendaraan'=>$get_mst_kendaraan->result()
            ];
            $this->load->view($this->folder_view.'update_konfirmasi_pemesanan', $data);
        }
        
        function update_konfirmasi_dist($kode=null, $type=null){
            if(@$kode){
                $_get_pesan = $this->db->where('kode',$kode)->get('ess_pemesanan_kendaraan');
                if($_get_pesan->num_rows()==1){
                    # detail pemesanan
                    $get_pesan = $_get_pesan->row();
                    
                    $status = status_pemesanan_admin([
                        'tanggal_berangkat'=>$get_pesan->tanggal_berangkat,
                        'verified'=>$get_pesan->verified,
                        'status_persetujuan_admin'=>$get_pesan->status_persetujuan_admin,
                        'id_mst_kendaraan'=>$get_pesan->id_mst_kendaraan,
                        'id_mst_driver'=>$get_pesan->id_mst_driver,
                        'pesanan_selesai'=>$get_pesan->pesanan_selesai,
                        'rating_driver'=>$get_pesan->rating_driver,
                        'is_canceled_by_admin'=>$get_pesan->is_canceled_by_admin
                    ]);
                    // echo $get_pesan->id_mst_driver; exit;
                    if(/*strpos($status, 'Jalan') == true*/ $get_pesan->verified==1 && $get_pesan->rating_driver==null){
                        $this->data["akses"] = $this->akses;
                        $this->data["navigasi_menu"] = menu_helper();
                        if(@$type){
                            $this->data['content'] = $this->folder_view."update_konfirmasi_pemesanan_view_edit";
                            $this->data['is_edited'] = true;
                        } else{
                            $this->data['content'] = $this->folder_view."update_konfirmasi_pemesanan_view";
                        }

                        $this->data['row'] = $get_pesan;

                        # get driver
                        $this->db->select('a.id,a.np_karyawan,a.nama,a.jenis_sim,a.keterangan,a.id_mst_kendaraan_default, (select count(x.id) from ess_pemesanan_kendaraan x where x.id_mst_driver=a.id AND (x.tanggal_berangkat="'.$get_pesan->tanggal_berangkat.'" OR "'.$get_pesan->tanggal_berangkat.'" BETWEEN x.tanggal_awal AND x.tanggal_akhir)) as countt')
                            ->where('a.status',1)->where('a.id_mst_kendaraan_default is not null',null,false);
                        $get_driver = $this->db->get('mst_driver a');
                        $this->data['driver'] = $get_driver->result();

                        # get kendaraan
                        $get_mst_kendaraan = $this->db->select('a.*, b.harga')->where('a.status',1)->from('mst_kendaraan a')->join('mst_bbm b','a.id_mst_bbm=b.id','LEFT')->get();
                        $this->data['mst_kendaraan'] = $get_mst_kendaraan->result();

                        $this->load->view('template',$this->data);
                    } else{
                        if(@$type){
                            $warning = 'Pemesan sudah menilai driver.';
                        } else{
                            $warning = 'Plotting driver oleh Pengelola Transportasi harus sudah mendapat persetujuan dari Atasan.';
                        }
                        $this->session->set_flashdata('warning',$warning);
                        redirect('food_n_go/kendaraan/konfirmasi_pemesanan');
                    }
                } else{
                    $this->session->set_flashdata('warning','Data tidak ditemukan');
                    redirect('food_n_go/kendaraan/konfirmasi_pemesanan');
                }
            } else{
                $this->session->set_flashdata('warning','Missing parameters');
                redirect('food_n_go/kendaraan/konfirmasi_pemesanan');
            }
        }
        
        function save_update_konfirmasi(){
            $response = [];
            $data_update = [];
            $kode = $this->input->post('kode',true);
            $status_persetujuan_admin = $this->input->post('status_persetujuan_admin',true);
            $catatan_admin_dafasum = $this->input->post('catatan_admin_dafasum',true);
            $id_mst_driver = $this->input->post('id_mst_driver',true);
            $nama_mst_driver = $this->input->post('nama_mst_driver',true);
            $id_mst_kendaraan = $this->input->post('id_mst_kendaraan_post',true);
            $nama_mst_kendaraan = $this->input->post('nama_mst_kendaraan',true);
            $id_mst_bbm = $this->input->post('id_mst_bbm',true);
            $nama_mst_bbm = $this->input->post('nama_mst_bbm',true);
            $harga_bbm_per_liter = $this->input->post('harga_bbm_per_liter',true);
            $status_persetujuan_admin_date = date('Y-m-d H:i:s');
            
            $data_update['status_persetujuan_admin'] = $status_persetujuan_admin;
            $data_update['status_persetujuan_admin_date'] = $status_persetujuan_admin_date;
            $data_update['admin_dafasum_np'] = $_SESSION["no_pokok"];
            
            switch ($status_persetujuan_admin) {
                case "2":
                    $data_update['catatan_admin_dafasum'] = $catatan_admin_dafasum;
                    break;
                case "1":
                    # set driver
                    // $data_update['id_mst_driver'] = $id_mst_driver;
                    // $data_update['nama_mst_driver'] = substr($nama_mst_driver,0,-4);
                    
                    # set kendaraan
                    // $data_update['id_mst_kendaraan'] = $id_mst_kendaraan;
                    // $data_update['nama_mst_kendaraan'] = $nama_mst_kendaraan;
                    
                    # set bbm
                    // $data_update['id_mst_bbm'] = $id_mst_bbm;
                    // $data_update['nama_mst_bbm'] = $nama_mst_bbm;
                    // $data_update['harga_bbm_per_liter'] = $harga_bbm_per_liter;
                    break;
                default:
                    continue;
            }
            
            $process = $this->db->where('kode',$kode)->update('ess_pemesanan_kendaraan',$data_update);
            if($process){
                $response['status']=true;
                $response['message']='Konfirmasi Admin Dafasum telah diupdate';
            } else{
                $response['status']=false;
                $response['message']='Gagal saat mengudate';
            }
            
            echo json_encode($response);
        }
        
        function detail_driver($id, $tanggal){
            $return='';
            
            $get_mst = $this->db->where('id',$id)->get('mst_driver')->row();
            $get_go = $this->db->select('id,nama_mst_driver,tanggal_berangkat, tanggal_awal, tanggal_akhir, nama_kota_asal, lokasi_jemput, jam, is_pp, is_inap')
                ->where('id_mst_driver',$id)
                ->where("(tanggal_berangkat='$tanggal' OR '$tanggal' BETWEEN tanggal_awal AND tanggal_akhir)")
                ->order_by('tanggal_berangkat','DESC')->get('ess_pemesanan_kendaraan');
            
            # echo driver biodata
            $return .= $get_mst->np_karyawan.' - '.$get_mst->nama;
            $count = $get_go->num_rows();
            $return .= " ($count)";
            
            $return .= "\nJenis SIM: $get_mst->jenis_sim";
            
            $return .= "\n=======================\n";
            
            if($get_go->num_rows()>0){
                foreach($get_go->result() as $row){
                    $return .= hari_tanggal($row->tanggal_berangkat).' @'.$row->jam;
                    
                    if($row->is_inap==1){
                        $return .= "\nMenginap";
                    } else if($row->is_pp==1){
                        $return .= "\nPP";
                    }
                    
                    if($row->tanggal_awal!=null){
                        $return .= ": ".hari_tanggal($row->tanggal_awal);
                        if($row->tanggal_akhir!=null and $row->tanggal_akhir!=$row->tanggal_awal){
                            $return .= " s/d ".hari_tanggal($row->tanggal_akhir);
                        }
                    }
                    
                    $return .= "\nAsal\t\t";
                    $return .= $row->nama_kota_asal;
                    $return .= "\nTujuan\t";
                    $return .= str_replace('<br>',"\n\t\t",get_pemesanan_tujuan($row->id));
                    $return .= "\n";
                }
            } else{
                $return .= "Driver bisa ditugaskan pada ".hari_tanggal($tanggal);
            }
            
            echo $return;
        }
        
        function tabel_driver($id){
            $list = $this->db->select('id,nama_mst_driver,tanggal_berangkat, tanggal_awal, tanggal_akhir, nama_kota_asal, lokasi_jemput, jam')
                ->where('id_mst_driver',$id)
                ->order_by('tanggal_berangkat','DESC')->get('ess_pemesanan_kendaraan')->result();
            $data = array();
            $no = 0;
            foreach ($list as $field) {
                # lokasi asal/jemput
                $berangkat_dari='';
                if($field->lokasi_jemput!=null and $field->lokasi_jemput!=''){
                    $berangkat_dari .= $field->lokasi_jemput;
                }
                if($field->nama_kota_asal!=null and $field->nama_kota_asal!=''){
                    $berangkat_dari .= '<br><small>('.$field->nama_kota_asal.')</small>';
                }
                
                # tanggal
                $tanggal='';
                if($field->tanggal_berangkat!=null){
                    $tanggal .= hari_tanggal($field->tanggal_berangkat);
                }
                if($field->jam!=null){
                    $tanggal .= ' @'.$field->jam;
                }
                
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->nama_mst_driver;
                $row[] = $berangkat_dari;
                $row[] = get_pemesanan_tujuan($field->id);
                $row[] = $tanggal;
                
                //$row[] = '<button class="btn btn-sm btn-danger mb-10" onclick="hapus(\''.encode_id($field->id).'\')"><i class="fa fa-trash"></i> Hapus</button>';
                $data[] = $row;
            }

            $output = array(
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);
        }
        
        # START: ini untuk yg dinamis => model inputan add row, tampilkan bentuk tabel
        function show_biaya($kode){ 
            //echo $kode; exit;
            $get_pesan = $this->db->where('kode',$kode)->get('ess_pemesanan_kendaraan')->row();
            $data=[
                'row'=>$get_pesan
            ];
            
            $this->load->view($this->folder_view.'update_biaya_perjalanan', $data);
        }
        
        public function tabel_biaya($id_pemesanan) {
			$data = array();
            # get bbm
            $get_bbm = $this->db->select('kode,id_mst_bbm,nama_mst_bbm,harga_bbm_per_liter,jumlah_liter_bbm,total_harga_bbm')->where('id',$id_pemesanan)->get('ess_pemesanan_kendaraan')->row();
            $data[] = [
                $get_bbm->nama_mst_bbm,
                $get_bbm->harga_bbm_per_liter,
                0,
                '<a href="javascript:;" data-kode="'.$get_bbm->kode.'" title="Edit"><i class="fa fa-edit"></i></a> <a href="javascript:;" data-kode="'.$get_bbm->kode.'" title="Hapus"><i class="fa fa-trash"></i></a>',
            ];
            
			$list = $this->db->where('id_pemesanan_kendaraan', $id_pemesanan)->get('ess_pemesanan_kendaraan_biaya')->result();
			$no = 0;
			foreach ($list as $tampil) {
				$no++;
                
                $btn = '';
                
                # btn detail
                //$btn .= '<button class="btn btn-default btn-xs edit_button" type="button" data-id="'.$tampil->id.'" onclick="show_detail(this)">Detail</button> ';
                
				$row = array();
				$row[] = $tampil->nama_pengeluaran;
				$row[] = '';
				$row[] = $tampil->total_rp;
				$row[] = $btn;
								
				$data[] = $row;
			}

			$output = array(
                "data" => $data,
            );
			
			echo json_encode($output);
		}
        
        function save_update_biaya(){
            $response = [];
            
            $data_update = [];
            $id_pemesanan_kendaraan = $this->input->post('id_pemesanan',true);
            $nama_pengeluaran = $this->input->post('nama_pengeluaran',true);
            $total_rp = $this->input->post('total_rp',true);
            
            $data_update['kode'] = $this->uuid->v4();
            $data_update['id_pemesanan_kendaraan'] = $id_pemesanan_kendaraan;
            $data_update['nama_pengeluaran'] = $nama_pengeluaran;
            $data_update['total_rp'] = $total_rp;
            
            $process = $this->db->insert('ess_pemesanan_kendaraan_biaya',$data_update);
            if($process){
                $response['status']=true;
                $response['message']='Biaya telah ditambahkan';
            } else{
                $response['status']=false;
                $response['message']='Gagal saat menambah';
            }
            echo json_encode($response);
        }
        # END: ini untuk yg dinamis => model inputan add row, tampilkan bentuk tabel
        
        function show_biaya_statis($kode){
            $get_pesan = $this->db->where('kode',$kode)->get('ess_pemesanan_kendaraan')->row();
            $get_mst_bbm = $this->db->where('status',1)->get('mst_bbm')->result();
            $data=[
                'row'=>$get_pesan,
                'bbm'=>$get_mst_bbm
            ];
            
            $this->load->view($this->folder_view.'update_biaya_perjalanan_statis', $data);
        }
        
        function save_update_biaya_statis(){
            $response = [];
            
            $data_update = [];
            $kode = $this->input->post('kode',true);
            $id_mst_bbm = $this->input->post('id_mst_bbm',true);
            $nama_mst_bbm = $this->input->post('nama_mst_bbm',true);
            $harga_bbm_per_liter = $this->input->post('harga_bbm_per_liter',true);
            $jumlah_liter_bbm = $this->input->post('jumlah_liter_bbm',true);
            $biaya_tol = $this->input->post('biaya_tol',true);
            $biaya_parkir = $this->input->post('biaya_parkir',true);
            $biaya_lainnya = $this->input->post('biaya_lainnya',true);
            $biaya_total = $this->input->post('biaya_total',true);
            
            $data_update['id_mst_bbm'] = $id_mst_bbm;
            $data_update['nama_mst_bbm'] = $nama_mst_bbm;
            $data_update['harga_bbm_per_liter'] = $harga_bbm_per_liter;
            $data_update['jumlah_liter_bbm'] = $jumlah_liter_bbm;
            $data_update['total_harga_bbm'] = $harga_bbm_per_liter * $jumlah_liter_bbm;
            $data_update['biaya_tol'] = $biaya_tol;
            $data_update['biaya_parkir'] = $biaya_parkir;
            $data_update['biaya_lainnya'] = $biaya_lainnya;
            if($biaya_lainnya>0){
                $data_update['ket_lainnya'] = $this->input->post('ket_lainnya',true);
            } else{
                $data_update['ket_lainnya'] = null;
            }
            $data_update['biaya_total'] = $biaya_total;
            $data_update['submit_biaya'] = 1;
            $data_update['submit_biaya_date'] = date('Y-m-d H:i:s');
            
            $process = $this->db->where('kode',$kode)->update('ess_pemesanan_kendaraan',$data_update);
            if($process){
                $response['status']=true;
                $response['message']='Biaya perjalanan telah diupdate';
            } else{
                $response['status']=false;
                $response['message']='Gagal saat update';
            }
            echo json_encode($response);
        }
        
        function cancel_order($kode=null){
            if(@$kode){
                $this->db->where('kode',$kode)->update('ess_pemesanan_kendaraan',['is_canceled_by_admin'=>'1', 'date_canceled_by_admin'=>date('Y-m-d H:i:s')]);
                if($this->db->affected_rows()>0){
                    $this->session->set_flashdata('success','Pesanan telah dibatalkan.');
                    redirect('food_n_go/kendaraan/konfirmasi_pemesanan');
                } else{
                    $this->session->set_flashdata('error','Gagal saat melakukan pembatalan.');
                    redirect('food_n_go/kendaraan/konfirmasi_pemesanan/update_konfirmasi_dist/'.$kode.'/edit');
                }
            } else{
                $this->session->set_flashdata('error','Parameter tidak ditemukan.');
                redirect('food_n_go/kendaraan/konfirmasi_pemesanan');
            }
        }
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */