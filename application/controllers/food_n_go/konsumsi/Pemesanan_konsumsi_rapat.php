<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pemesanan_konsumsi_rapat extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'food_n_go/konsumsi/';
			$this->folder_model = 'konsumsi/';
			$this->folder_controller = 'food_n_go/konsumsi/';
			
			$this->akses = array();
			
			$this->load->helper("cutoff_helper");
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("perizinan_helper");
			$this->load->helper("konsumsi_helper");
			$this->load->helper('form');
			
			$this->load->model($this->folder_model."m_pemesanan_konsumsi_rapat");
			$this->load->model($this->folder_model."/m_katalog");
			$this->load->model("kendaraan/m_data_pemesanan");
			$this->load->model("M_dashboard","dashboard");
		
			$this->data["navigasi_menu"] = menu_helper();
			$this->data["is_with_sidebar"] = true;
			$this->data['content'] = $this->folder_view."pemesanan_konsumsi_rapat";
			array_push($this->data['js_sources'],"food_n_go/pemesanan_konsumsi_rapat");
		}
		
		public function index() {
			// phpinfo();exit;
			$this->data['judul'] = "Ruang dan Konsumsi Rapat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
			$this->data["akses"] = $this->akses;
			$array_tahun_bulan = $this->filter_bulan();
			
			$array_jadwal_kerja 	= $this->m_data_pemesanan->select_mst_karyawan_aktif();
			$array_daftar_karyawan	= $this->m_data_pemesanan->select_daftar_karyawan();
			$array_daftar_unit		= $this->m_data_pemesanan->select_daftar_unit();
			$array_daftar_lokasi	= $this->db->where('status', '1')->get('mst_lokasi');
			
			$this->data['array_jadwal_kerja'] 		= $array_jadwal_kerja;
			$this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
			$this->data['array_daftar_unit'] 		= $array_daftar_unit;
			$this->data['array_daftar_lokasi'] 		= $array_daftar_lokasi;

			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;	
			$this->data['persetujuan'] 				= '0';
			$this->data['url_table'] 				= 'food_n_go/konsumsi/pemesanan_konsumsi_rapat/tabel_data_pemesanan/0/';

			$this->load->view('template',$this->data);
		}

		public function daftar_persetujuan() {
			$this->data['judul'] = "Persetujuan Ruang dan Konsumsi Rapat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
			izin($this->akses["persetujuan"]);
			$this->data["akses"] = $this->akses;
			$array_tahun_bulan = $this->filter_bulan();
			
			$this->data['persetujuan'] 				= '1';
			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;
			$this->data['url_table'] 				= 'food_n_go/konsumsi/pemesanan_konsumsi_rapat/tabel_data_pemesanan/1/';

			$this->load->view('template',$this->data);
		}

		public function rekap() {
			$this->data['judul'] = "Data Konsumsi Rapat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
			$this->data["akses"] = $this->akses;
			$array_tahun_bulan = $this->filter_bulan();
			
			$this->data['persetujuan'] 				= '1';
			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;
			$this->data['url_table'] 				= 'food_n_go/konsumsi/pemesanan_konsumsi_rapat/tabel_data_pemesanan/2/';

			$this->load->view('template',$this->data);
		}

		public function tabel_data_pemesanan($set_verif=0, $tampil_bulan_tahun = null) {
			if ($set_verif=='0')
				$this->data['judul'] = "Ruang dan Konsumsi Rapat";
			else if ($set_verif=='1')
				$this->data['judul'] = "Persetujuan Ruang dan Konsumsi Rapat";
			else if ($set_verif=='2')
				$this->data['judul'] = "Data Konsumsi Rapat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			$this->load->model($this->folder_model."/M_tabel_pemesanan_konsumsi_rapat");

			if($tampil_bulan_tahun=='') {
				$tampil_bulan_tahun = '';				
			} else {
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
				
			$list = $this->M_tabel_pemesanan_konsumsi_rapat->get_datatables($set_verif, $tampil_bulan_tahun);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->nomor_pemesanan;
				$row[] = '('.$tampil->np_pemesan.') '.$tampil->nama_pemesan;
				$row[] = $tampil->nama_acara;
				$row[] = $tampil->lokasi;
				$row[] = tanggal_indonesia($tampil->tanggal_pemesanan);
				$row[] = date('H:i', strtotime($tampil->waktu_mulai)).' s/d '.(($tampil->waktu_selesai==null || $tampil->waktu_selesai=='00:00:00') ? 'Selesai' : date('H:i', strtotime($tampil->waktu_selesai)));

				if ((($tampil->verified==null && $_SESSION['grup']=='5') || (($tampil->verified==null || $tampil->verified=='1') && $_SESSION['grup']=='14')) && (@$this->akses["ubah"] || @$this->akses["persetujuan"]) && ($tampil->np_pemesan==$_SESSION['no_pokok'] || $_SESSION["grup"]==14) && $tampil->tanggal_pemesanan>=date('Y-m-d'))
					$ubah = "<a class='btn btn-warning btn-xs edit_button' data-toggle='modal' data-target='#modal_ubah' data-no='".$tampil->nomor_pemesanan."' onclick='tampil_data_ubah(this)'>Ubah</a> ";
				else 
					$ubah = "";

				if(@$this->akses['batal'] && $tampil->verified=='3' && $tampil->tanggal_pemesanan >= date('Y-m-d')) {
					$batal = " <a class='btn btn-danger btn-xs' data-no='".$tampil->nomor_pemesanan."' onclick='batal(\"".$tampil->nomor_pemesanan."\")'>Batalkan</a> ";
				} else {
					$batal = "";
				}

				$verif_act = "";
				if($_SESSION["grup"]==14) {
					if ($tampil->verified==null) {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-warning btn-xs'>Menunggu<br>Persetujuan Atasan</a> ";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Atasan</a> ";
					}
					else if ($tampil->verified=='1') {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-warning btn-xs'>Menunggu<br>Persetujuan Seksi Yanum</a> ";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Seksi Yanum</a> ";

						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif_act = "<a class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_persetujuan' data-no='".$tampil->nomor_pemesanan."' onclick='tampil_data_approval(this)'>Berikan Approval</a> ";
						else
							$verif_act = "";
					}
					else if ($tampil->verified=='3')
						$verif = "<a class='btn btn-success btn-xs'>Disetujui Seksi Yanum</a> ";
					else if ($tampil->verified=='4')
						$verif = "<a class='btn btn-danger btn-xs'>Ditolak Seksi Yanum</a> ";
					else if ($tampil->verified=='6')
						$verif = "<a class='btn btn-danger btn-xs'>Dibatalkan Seksi Yanum</a> <br>";
					else
						$verif = "";
				} else if($_SESSION["grup"]==5 || $_SESSION["grup"]==4) {
					if ($tampil->verified==null && $tampil->np_atasan==$_SESSION['no_pokok']) {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-warning btn-xs'>Menunggu<br>Persetujuan Anda</a> ";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Anda</a> ";

						if($set_verif=='1' && $tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif_act = "<a class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_persetujuan' data-no='".$tampil->nomor_pemesanan."' onclick='tampil_data_approval(this)'>Berikan Approval</a> ";
						else
							$verif_act = "";
					}
					else if ($tampil->verified==null) {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-warning btn-xs'>Menunggu<br>Persetujuan Atasan</a> ";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Atasan</a> ";
					}
					else if ($tampil->verified=='1') {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-warning btn-xs'>Menunggu<br>Persetujuan Seksi Yanum</a> ";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Seksi Yanum</a> ";
					}
					else if ($tampil->verified=='2' && $tampil->np_atasan==$_SESSION['no_pokok'])
						$verif = "<a class='btn btn-danger btn-xs'>Anda Tolak</a> ";
					else if ($tampil->verified=='2')
						$verif = "<a class='btn btn-danger btn-xs'>Ditolak Atasan</a> ";
					else if ($tampil->verified=='3')
						$verif = "<a class='btn btn-success btn-xs'>Disetujui Seksi Yanum</a> ";
					else if ($tampil->verified=='4')
						$verif = "<a class='btn btn-danger btn-xs'>Ditolak Seksi Yanum</a> ";
					else if ($tampil->verified=='5')
						$verif = "<a class='btn btn-danger btn-xs'>".$tampil->keterangan_verified."</a> ";
					else if ($tampil->verified=='6')
						$verif = "<a class='btn btn-danger btn-xs'>Dibatalkan Seksi Yanum</a> <br>";
					else
						$verif = "";
				} else {
					$verif = "";
				}

				$row[] = $verif;
				$row[] = $verif_act.$ubah."<a class='btn btn-default btn-xs edit_button' data-toggle='modal' data-target='#modal_persetujuan' data-no='".$tampil->nomor_pemesanan."' onclick='tampil_data_detail(this)'>Lihat</a>".$batal;
								
				$data[] = $row;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_pemesanan_konsumsi_rapat->count_all($set_verif, $tampil_bulan_tahun),
					"recordsFiltered" => $this->M_tabel_pemesanan_konsumsi_rapat->count_filtered($set_verif, $tampil_bulan_tahun),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}

		public function add() {
			$this->data['judul'] = "Ruang dan Konsumsi Rapat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			$this->data["akses"] = $this->akses;
			$this->load->model("lembur/m_pengajuan_lembur");

			if($this->akses["tambah"]) {
				$this->data['daftar_snack'] = $this->m_katalog->daftar_katalog('snack', '1');
				$this->data['daftar_minuman'] = $this->m_katalog->daftar_katalog('minuman', '1');
				$this->data['daftar_makanan'] = $this->m_katalog->daftar_katalog('makanan', '1');
				$this->data['content'] = $this->folder_view."tambah_pemesanan_konsumsi_rapat";

				array_push($this->data['js_sources'],"food_n_go/pemesanan_konsumsi_rapat");
				
				$array_daftar_lokasi	= $this->db->where('status', '1')->get('mst_lokasi');
				$this->data['array_daftar_lokasi'] 	= $array_daftar_lokasi;

				//unit kerja dan approval
				$arr_unit_kerja = array();
				if(strcmp($this->session->userdata("grup"),"4")==0){ // pengadministrasi unit kerja
					foreach($this->session->userdata("list_pengadministrasi") as $kode_unit_administrasi){
						if(strcmp(substr($kode_unit_administrasi["kode_unit"],1,1),"0")==0){
							array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,3));
						}
						else{
							array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,2));
						}
	                    
	                    $arr_unit_pic[]=$kode_unit_administrasi['kode_unit'];
					}
	                $this->data['np_pic'] = $this->dashboard->getKaryawan($arr_unit_pic);
				}
				else if(strcmp($this->session->userdata("grup"),"5")==0){ // pengguna
					if(strcmp(substr($this->session->userdata("kode_unit"),1,1),"0")==0){
						array_push($arr_unit_kerja,substr($this->session->userdata("kode_unit"),0,3));
					}
					else{
						array_push($arr_unit_kerja,substr($this->session->userdata("kode_unit"),0,2));
					}
					$np_karyawan = $this->session->userdata("no_pokok");
	                $this->data['np_pic'] = $this->dashboard->getKaryawan($np_karyawan);
				}
				
				$arr_unit_kerja = array_unique($arr_unit_kerja);
	            $arr_pemesan = $this->db->where_in('SUBSTR(kode_unit,1,2)',$arr_unit_kerja)->where('SUBSTR(kode_unit,4,2)','00')->get('mst_satuan_kerja')->result_array();
	            $this->data["array_daftar_unit"] = $arr_pemesan;
                
                # heru PDS tambahkan query get master kode anggaran, 2021-01-27
	            $this->data["kode_anggaran"] = $this->db->where('status',1)->get('mst_kode_anggaran')->result();

				$this->load->view('template',$this->data);
			} else {
				redirect(site_url($this->folder_controller.'pemesanan_konsumsi_rapat'));
			}
		}

		public function filter_bulan() {
			//ambil tahun bulan tabel yang tersedia
            $get_month = $this->db->select('tanggal_pemesanan')->group_by('YEAR(tanggal_pemesanan), MONTH(tanggal_pemesanan)')->order_by('YEAR(tanggal_pemesanan) DESC, MONTH(tanggal_pemesanan) DESC')->get('ess_pemesanan_konsumsi_rapat')->result();
            
            foreach($get_month as $row){
                $array_tahun_bulan[] = date('m-Y', strtotime($row->tanggal_pemesanan));
            }

            return $array_tahun_bulan;
		}

		public function save($id=null) {
			if(@$this->input->post()) {
				$waktu = date('Y-m-d H:i:s');
				$data['np_pemesan'] = $_SESSION["no_pokok"];
				$data['nama_pemesan'] = $_SESSION["nama"];
				$data['nama_acara'] = $this->input->post('insert_nama_acara');
				$data['tanggal_pemesanan'] = date('Y-m-d', strtotime(substr($this->input->post('insert_tanggal_pemesanan'), -4).'-'.substr($this->input->post('insert_tanggal_pemesanan'), 3,2).'-'.substr($this->input->post('insert_tanggal_pemesanan'), 0,2)));
				$data['waktu_mulai'] = $this->input->post('insert_waktu_mulai');
				$data['waktu_selesai'] = $this->input->post('insert_waktu_selesai');
				$data['jumlah_peserta'] = $this->input->post('insert_jumlah_peserta');
				$data['lokasi_acara'] = $this->input->post('insert_lokasi_acara');
				$data['id_ruangan'] = $this->input->post('insert_id_ruangan');
				$data['snack'] = implode(',', $this->input->post('insert_snack'));
				$data['np_atasan'] = $this->input->post('insert_np_atasan');
				$data['kode_akun_sto'] = $this->input->post('insert_kode_akun_sto');
				$data['kode_anggaran'] = $this->input->post('insert_kode_anggaran');
				$data['keterangan'] = $this->input->post('insert_keterangan');
				
				$makan=array();
				$i=0; foreach ($this->input->post('insert_makanan') as $makanan) {
					$detail_makanan = $this->db->where('id', $makanan)->where('status', '1')->get('mst_jenis_katalog')->row();
					$makan[$i]['id_makanan'] = $detail_makanan->id;
					$makan[$i]['nama_makanan'] = $detail_makanan->nama;
					$makan[$i]['harga'] = $detail_makanan->harga;
					$makan[$i]['jumlah'] = $this->input->post('insert_jumlah_makanan['.$i.']');
					$i++;
				}
				$minum=array();
				$i=0; foreach ($this->input->post('insert_minuman') as $minuman) {
					$detail_minuman = $this->db->where('id', $minuman)->get('mst_jenis_katalog')->row();
					$minum[$i]['id_makanan'] = $detail_minuman->id;
					$minum[$i]['nama_makanan'] = $detail_minuman->nama;
					$minum[$i]['harga'] = $detail_minuman->harga;
					$minum[$i]['jumlah'] = $this->input->post('insert_jumlah_minuman['.$i.']');
					$i++;
				}
				$data['makanan'] = (string)json_encode($makan);
				$data['minuman'] = (string)json_encode($minum);

				if ($_SESSION['grup']=='5' || $_SESSION['grup']=='4') {
					if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
						$data['kode_unit_pemesan'] = $this->input->post('insert_unit_kerja');
					} else if($_SESSION["grup"]==5) { //jika Pengguna
						$data['kode_unit_pemesan'] = substr($_SESSION["kode_unit"],0,3).'00';
					}
					$data['nama_unit_pemesan'] = $this->db->where('kode_unit', $data['kode_unit_pemesan'])->get('mst_satuan_kerja')->row()->nama_unit;
				


					// $data['makanan'] = implode(',', $this->input->post('insert_makanan'));
					// $data['minuman'] = implode(',', $this->input->post('insert_minuman'));

					if ($data['tanggal_pemesanan']<date('Y-m-d')) {
						$this->session->set_flashdata('failed', 'Gagal! Waktu Pemesanan Telah Lewat.');
						if ($_SESSION['grup']=='5' || $_SESSION['grup']=='4')
							redirect(site_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat'));
						else
							redirect(site_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/daftar_persetujuan'));
					} else if (date('Y-m-d')==$data['tanggal_pemesanan'] && date('H:i')>'16:00') {
						$data['verified'] = '5';
						$data['updated'] = $waktu;
						$data['keterangan_verified'] = 'Melebihi Batas Waktu Pesan';
					}
				}

				if ($id==null) {
					$data['created'] = $waktu;
					$data['nomor_pemesanan'] = generate_nomor_pemesanan_konsumsi();
					$this->db->set($data)->insert('ess_pemesanan_konsumsi_rapat');
					if ($this->db->affected_rows()>0) {
						$this->session->set_flashdata('success', 'Berhasil menambahkan pesanan konsumsi rapat.');
					} else {
						$this->session->set_flashdata('success', 'Berhasil menambahkan pesanan konsumsi rapat.');
					}
				}
				else {
					$data['updated'] = $waktu;
					if ($_SESSION['grup']=='5' || $_SESSION['grup']=='4')
						$update = $data;
					else {
						$ket_detail = $this->db->where('id', $id)->get('ess_pemesanan_konsumsi_rapat')->row_array();
						$update['kode_akun_sto'] = $this->input->post('insert_kode_akun_sto');
						$update['kode_anggaran'] = $this->input->post('insert_kode_anggaran');
						$update['lokasi_acara'] = $this->input->post('insert_lokasi_acara');
						$update['id_ruangan'] = $this->input->post('insert_id_ruangan');
						$update['snack'] = implode(',', $this->input->post('insert_snack'));
						// $update['makanan'] = .implode(',', $this->input->post('insert_makanan'));
						// $update['minuman'] = implode(',', $this->input->post('insert_minuman'));
						$update['makanan'] = $data['makanan'];
						$update['minuman'] = $data['minuman'];
						$update['updated'] = $waktu;
					}

					$this->db->where('id', $id)->set($update)->update('ess_pemesanan_konsumsi_rapat');
					if ($this->db->affected_rows()>0) {
						$this->session->set_flashdata('success', 'Berhasil mengubah pesanan konsumsi rapat.');
					} else {
						$this->session->set_flashdata('success', 'Berhasil mengubah pesanan konsumsi rapat.');
					}
				}
			}

			if ($_SESSION['grup']=='5' || $_SESSION['grup']=='4')
				redirect(site_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat'));
			else
				redirect(site_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/daftar_persetujuan'));
		}
		
        function get_apv(){
            $kode_unit = $this->input->post('kode_unit');
			$this->load->model('m_approval');	
            $get_data = $this->m_approval->list_atasan_minimal_kadep([$kode_unit],null);
            echo json_encode($get_data);
        }
        
        function set_lokasi(){
            $lks = $this->input->post('lokasi');
            $lokasi = $this->db->where('nama', $lks)->get('mst_lokasi')->row()->id;

            $get_data['gedung']  = $this->db->where(array('status'=>'1', 'lokasi'=>$lokasi))->get('mst_gedung')->result_array();
            $get_data['snack'] 	 = $this->db->select('a.*, nama_penyedia')->join('mst_penyedia_makanan b', 'a.id_penyedia=b.id')->where(array('a.status'=>'1', 'lokasi'=>$lokasi, 'jenis'=>'Snack'))->get('mst_jenis_katalog a')->result_array();
            $get_data['makanan'] = $this->db->select('a.*, nama_penyedia')->join('mst_penyedia_makanan b', 'a.id_penyedia=b.id')->where(array('a.status'=>'1', 'lokasi'=>$lokasi, 'jenis'=>'Makanan'))->get('mst_jenis_katalog a')->result_array();
            $get_data['minuman'] = $this->db->select('a.*, nama_penyedia')->join('mst_penyedia_makanan b', 'a.id_penyedia=b.id')->where(array('a.status'=>'1', 'lokasi'=>$lokasi, 'jenis'=>'Minuman'))->get('mst_jenis_katalog a')->result_array();
            echo json_encode($get_data);
        }
        
        function get_ruangan(){
            $gdg = $this->input->post('gedung');
            $gedung = $this->db->where('nama', $gdg)->get('mst_gedung')->row()->id;

            $get_data  = $this->db->where(array('status'=>'1', 'id_gedung'=>$gedung))->get('mst_ruangan')->result_array();
            echo json_encode($get_data);
        }
        
        # START: heru menambahkan ini 2020-11-25 @09:53
        function get_ruang_info(){
            $id = $this->input->post('id_ruang');
            $return = null;
            $info = $this->db->where('id', $id)->get('mst_ruangan');
            if($info->num_rows()==1)
                $return = $info->row();
            echo json_encode($return);
        }
        # END: heru menambahkan ini 2020-11-25 @09:53
        
        function ubah(){
            $no = $this->input->post('no_pemesanan');
	        $data['detail'] = $this->m_pemesanan_konsumsi_rapat->detail($no)->row_array();
            $data['array_daftar_lokasi'] = $this->db->where('status', '1')->get('mst_lokasi');
	        $data['array_daftar_snack'] = $this->db->where('id_penyedia in (select id from mst_penyedia_makanan where lokasi='.$data['detail']['lokasi_acara'].')')->where(array('jenis'=>'Snack','status'=>'1'))->get('mst_jenis_katalog');
	        // $data['array_daftar_makanan'] = $this->db->where('id_penyedia in (select id from mst_penyedia_makanan where lokasi='.$data['detail']['lokasi_acara'].')')->where(array('jenis'=>'Makanan','status'=>'1'))->get('mst_jenis_katalog');
	        // $data['array_daftar_minuman'] = $this->db->where('id_penyedia in (select id from mst_penyedia_makanan where lokasi='.$data['detail']['lokasi_acara'].')')->where(array('jenis'=>'Minuman','status'=>'1'))->get('mst_jenis_katalog');
	        $data['array_daftar_gedung'] = $this->db->where('lokasi', $data['detail']['lokasi_acara'])->where(array('status'=>'1'))->get('mst_gedung');
	        $data['id_gedung'] = $this->db->where('id', $data['detail']['id_ruangan'])->get('mst_ruangan')->row()->id_gedung;
	        $data['array_daftar_ruangan'] = $this->db->where('id_gedung', $data['id_gedung'])->where(array('status'=>'1'))->get('mst_ruangan');

            $this->load->view($this->folder_view.'ubah_pemesanan_konsumsi_rapat', $data);
        }
        
        function detail(){
            $no = $this->input->post('no_pemesanan');
	        $data['detail'] = $this->m_pemesanan_konsumsi_rapat->detail($no)->row_array();
	        $data['snack'] = $this->m_pemesanan_konsumsi_rapat->katalog_pemesanan(explode(',', $data['detail']['snack']))->row_array();

	        $pesanan_makanan = json_decode($data['detail']['makanan']);
	        $makanan = '';
	        $total = 0;
	        $total_snack = $data['snack']['total_harga']*$data['detail']['jumlah_peserta'];
	        $total_makanan = 0;
	        $total_minuman = 0;
	        for ($i=0; $i<count($pesanan_makanan); $i++) {
	        	$pesan_makan = $pesanan_makanan[$i];
	        	$total_harga = $pesan_makan->harga*$pesan_makan->jumlah;
	        	$makanan.= '* '.$pesan_makan->nama_makanan.' : '.$pesan_makan->jumlah.' x '.rupiah($pesan_makan->harga, 1).' = '.rupiah(($total_harga), 1).' <br>';
	        	$total_makanan += $total_harga;
	        }
	        $data['makanan'] = $makanan;

	        $pesanan_minuman = json_decode($data['detail']['minuman']);
	        $minuman = '';
	        for ($i=0; $i<count($pesanan_minuman); $i++) {
	        	$pesan_minum = $pesanan_minuman[$i];
	        	$total_harga = $pesan_minum->harga*$pesan_minum->jumlah;
	        	$minuman.= '* '.$pesan_minum->nama_makanan.' : '.$pesan_minum->jumlah.' x '.rupiah($pesan_minum->harga, 1).' = '.rupiah(($total_harga), 1).' <br>';
	        	$total_minuman += $total_harga;
	        }
	        $data['minuman'] = $minuman;

	        $data['total_makanan'] = rupiah($total_makanan, 1);
	        $data['total_minuman'] = rupiah($total_minuman, 1);
	        $data['total_snack'] = $data['detail']['jumlah_peserta'].' x '.rupiah($data['snack']['total_harga'], 1).' = '.rupiah($total_snack, 1);
	        $total = $total_makanan+$total_minuman+$total_snack;
	        $data['total'] = rupiah($total, 1);
	        // $data['makanan'] = $this->m_pemesanan_konsumsi_rapat->katalog_pemesanan(explode(',', $data['detail']['makanan']))->row_array();
	        // $data['minuman'] = $this->m_pemesanan_konsumsi_rapat->katalog_pemesanan(explode(',', $data['detail']['minuman']))->row_array();
            echo json_encode($data);
        }
        
        function persetujuan(){
			$this->data['judul'] = "Persetujuan Ruang dan Konsumsi Rapat";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
            $verified=[];
        	if($this->akses["persetujuan"]) {
	            $no = $this->input->post('no_pemesanan');
	            $apv = $this->input->post('verified');
	            $detail = $this->db->where('nomor_pemesanan', $no)->get('ess_pemesanan_konsumsi_rapat')->row_array();

	            if ($apv=='1') {
	            	if ($detail['verified']==null && $_SESSION["grup"]==5 && $_SESSION["no_pokok"]==$detail['np_atasan']) {
	            		$verified['verified'] = '1';
		        		$verified['waktu_verified_atasan'] = date('Y-m-d H:i:s');
	            	}
	            	else if ($detail['verified']=='1' && $_SESSION["grup"]==14) {
	            		$verified['verified'] = '3';
		            	$verified['np_verified'] = $_SESSION['no_pokok'];
		        		$verified['waktu_verified_admin'] = date('Y-m-d H:i:s');
		            }
		        	$this->db->where('id', $detail['id'])->set($verified)->update('ess_pemesanan_konsumsi_rapat');
		        	$this->session->set_flashdata('success', 'Berhasil Memberikan Persetujuan');
	            } else if ($apv=='0') {
	            	$ket = $this->input->post('keterangan');
	            	$verified['keterangan_verified'] = $ket;

	            	if ($detail['verified']==null && $_SESSION["grup"]==5 && $_SESSION["no_pokok"]==$detail['np_atasan']) {
	            		$verified['verified'] = '2';
		        		$verified['waktu_verified_atasan'] = date('Y-m-d H:i:s');
	            	}
	            	else if ($detail['verified']=='1' && $_SESSION["grup"]==14) {
	            		$verified['verified'] = '4';
		            	$verified['np_verified'] = $_SESSION['no_pokok'];
		        		$verified['waktu_verified_admin'] = date('Y-m-d H:i:s');
		            }
		        	$this->db->where('id', $detail['id'])->set($verified)->update('ess_pemesanan_konsumsi_rapat');
	            	$this->session->set_flashdata('success', 'Berhasil Memberikan Penolakan');
	            } else {
	            	$this->session->set_flashdata('failed', 'Gagal Memberikan Approval');
	            }
	        } else {
	            $this->session->set_flashdata('failed', 'Anda Tidak Memiliki Akses!');
	        }
	        redirect(base_url('food_n_go/konsumsi/pemesanan_konsumsi_rapat/daftar_persetujuan'));
        }

        function batal(){
        	if ($this->input->is_ajax_request()) {
        		$no = $this->input->post('no', true);
        		$set['verified'] = '6';
        		$set['np_batal'] = $_SESSION['no_pokok'];
        		$set['waktu_batal'] = date('Y-m-d H:i:s');
        		$this->db->where('nomor_pemesanan', $no)->set($set)->update('ess_pemesanan_konsumsi_rapat');

        		$send['alert'] = 'success';
        		$send['txt'] = 'Pesanan telah dibatalkan.';
        		$send['judul'] = 'Berhasil';
        	}
        	else {
        		$send['alert'] = 'error';
        		$send['txt'] = 'Pesanan tidak dibatalkan.';
        		$send['judul'] = 'Gagal';
        	}
            echo json_encode($send);
        }
        
        # heru menambahkan ini 2020-12-03 @15:35
        function check_availability(){
            header('Content-Type: application/json');
            $message = '';
            $status = false;
            
            $tanggal = date('Y-m-d', strtotime($this->input->post('tanggal',true)));
            $id_ruang = $this->input->post('id_ruang',true);
            $waktu_mulai = $this->input->post('waktu_mulai',true);
            $waktu_selesai = $this->input->post('waktu_selesai',true);
            
            if($id_ruang=='')
                $message = 'Silakan pilih ruangan';
            else{
                $cek = $this->db
                    ->where('id_ruangan',$id_ruang)
                    ->where('tanggal_pemesanan',$tanggal)
                    ->where('verified',3)
                    ->where("('$waktu_mulai' BETWEEN waktu_mulai AND waktu_selesai)")
                    ->get('ess_pemesanan_konsumsi_rapat');
                if($cek->num_rows()>0)
                    $message = '<font color="red">Ruangan sudah digunakan</font>';
                else{
                    $status = true;
                    $message = '<font color="green">Ruangan bisa digunakan</font>';
                }
            }            
            
            echo json_encode([
                'status'=>$status,
                'message'=>'<div class="modal-body">
                                <h4>'.$message.'</h4>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
                            </div>'
            ]);
        }
        # END: heru menambahkan ini 2020-12-03 @15:35
        
        // heru PDS menambahkan ini 2021-01-27 @10:08, munculin STO sesuai tanggal pemesanan yg dipilih
        function getStoByDate(){
            header('Content-Type: application/json');
            
            $date = $this->input->post('date',true)!='' ? date('Y-m-d', strtotime($this->input->post('date',true))):date('Y-m-d');
            if( date('Y-m', strtotime($date)) > date('Y-m') )
                $month = date('Y_m');
            else
                $month = date('Y_m', strtotime($date));
            $table = 'erp_master_data_'.$month;
            
            $get = $this->db->distinct()
                ->select('kode_unit, nama_unit')
                ->where('kode_unit IS NOT NULL',null,false)
                ->where('kode_unit!=','')
                ->order_by('kode_unit')
                ->get($table)->result_array();            
            
            echo json_encode([
                'status'=>true,
                'message'=>'OK',
                'data'=>$get
            ]);
        }
        // END: heru PDS menambahkan ini 2021-01-27 @10:08
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */