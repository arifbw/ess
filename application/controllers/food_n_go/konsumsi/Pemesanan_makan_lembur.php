	<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Pemesanan_makan_lembur extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_pemesanan_makan_lembur");
			$this->load->model("kendaraan/m_data_pemesanan");
			$this->load->model("M_dashboard","dashboard");

			$this->data["navigasi_menu"] 	= menu_helper();
			$this->data["is_with_sidebar"] 	= true;
			$this->data['content'] 			= $this->folder_view."pemesanan_makan_lembur";
			array_push($this->data['js_sources'],"food_n_go/pemesanan_makan_lembur");
		}
		
		public function x()
		{
			$this->session->set_flashdata('failed', 'testing');
			//bug jika pake ini
			//redirect(base_url($this->folder_controller.'pemesanan_makan_lembur'));
			
			//header("Location: https://ess.peruri.co.id/ess/food_n_go/konsumsi/pemesanan_makan_lembur/");  
				
			redirect(base_url($this->folder_controller.'kendaraan/data_pemesanan'));
			
			//$this->index();
		
		}
		
		public function y()
		{
			
			$this->session->set_userdata('notif_input', 'success');
			//$this->session->unset_userdata($_SESSION['email']);
			//$this->session->userdata("notif_input")
			//bug jika pake ini
			//redirect(base_url($this->folder_controller.'pemesanan_makan_lembur'));
			
			//header("Location: https://ess.peruri.co.id/ess/food_n_go/konsumsi/pemesanan_makan_lembur/");  
				
			//redirect(base_url($this->folder_controller.'pemesanan_makan_lembur'));
			
			//$this->index();
		
		}
		
		public function index() {
			$this->data['judul'] = "Konsumsi Makan Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
		//	array_push($this->data['js_sources'],"food_n_go/pemesanan_makan_lembur");

			izin($this->akses["akses"]);
			$this->data["akses"] = $this->akses;
			$array_tahun_bulan = $this->filter_bulan();
			
			$array_jadwal_kerja 	= $this->m_data_pemesanan->select_mst_karyawan_aktif();
			$array_daftar_karyawan	= $this->m_data_pemesanan->select_daftar_karyawan();
			// $array_daftar_unit	= $this->m_data_pemesanan->select_daftar_unit();
			
			$array_daftar_lembur	= $this->db->where('status', '1')->get('mst_jenis_lembur');
			$array_daftar_lokasi	= $this->db->where('status', '1')->get('mst_lokasi');

			//unit kerja dan approval
			$arr_unit_kerja = array();
			if(strcmp($this->session->userdata("grup"),"4")==0){ // pengadministrasi unit kerja
				foreach($this->session->userdata("list_pengadministrasi") as $kode_unit_administrasi){
					/*if(strcmp(substr($kode_unit_administrasi["kode_unit"],1,1),"0")==0){
						array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,3));
					}
					else{
						array_push($arr_unit_kerja,substr($kode_unit_administrasi["kode_unit"],0,2));
					}*/
                    $arr_unit_kerja[] = $kode_unit_administrasi['kode_unit'];
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
				}*/
                $arr_unit_kerja[] = $this->session->userdata("kode_unit");
				$np_karyawan = $this->session->userdata("no_pokok");
                $this->data['np_pic'] = $this->dashboard->getKaryawan($np_karyawan);
			}
			
			$arr_unit_kerja = array_unique($arr_unit_kerja);
            //$arr_pemesan = $this->db->where_in('SUBSTR(kode_unit,1,2)',$arr_unit_kerja)->where('SUBSTR(kode_unit,4,2)','00')->get('mst_satuan_kerja')->result_array();
            $arr_pemesan = $this->db->where_in('kode_unit',$arr_unit_kerja)->get('mst_satuan_kerja')->result_array();
            $this->data["array_daftar_unit"] = $arr_pemesan;
			
			$this->data['array_daftar_lokasi'] 		= $array_daftar_lokasi;
			$this->data['persetujuan'] 				= '0';
			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;	
			$this->data['array_jadwal_kerja'] 		= $array_jadwal_kerja;
			$this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
			// $this->data['array_daftar_unit'] 		= $array_daftar_unit;
			$this->data['array_daftar_lembur'] 		= $array_daftar_lembur;
			$this->data['url_table'] 				= 'food_n_go/konsumsi/pemesanan_makan_lembur/tabel_data_pemesanan/0/';

			$this->load->view('template',$this->data);
		}

		public function daftar_persetujuan() {
			$this->data['judul'] = "Persetujuan Konsumsi Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
			izin($this->akses["persetujuan"]);
			$this->data["akses"] = $this->akses;
			$array_tahun_bulan = $this->filter_bulan();
			$array_daftar_lokasi	= $this->db->where('status', '1')->get('mst_lokasi');
			
			$this->data['persetujuan'] 				= '1';
			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;
			$this->data['array_daftar_lokasi'] 		= $array_daftar_lokasi;
			$this->data['url_table'] 				= 'food_n_go/konsumsi/pemesanan_makan_lembur/tabel_data_pemesanan/1/';

			$this->load->view('template',$this->data);
		}

		public function rekap() {
			$this->data['judul'] = "Data Makan Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			izin($this->akses["akses"]);
			$this->data["akses"] = $this->akses;
			$array_tahun_bulan = $this->filter_bulan();
			
			$this->data['persetujuan'] 				= '1';
			$this->data['array_tahun_bulan'] 		= $array_tahun_bulan;
			$this->data['url_table'] 				= 'food_n_go/konsumsi/pemesanan_makan_lembur/tabel_data_pemesanan/2/';

			$this->load->view('template',$this->data);
		}

		public function tabel_data_pemesanan($set_verif=0, $tampil_bulan_tahun=null)
		{
			$this->load->model($this->folder_model."/M_tabel_pemesanan_makan_lembur");
			
			//akses ke menu ubah				
			if($set_verif=='0') {
				$this->data['judul'] = "Konsumsi Makan Lembur";
			} else if($set_verif=='1') {
				$this->data['judul'] = "Persetujuan Konsumsi Lembur";
			} else if($set_verif=='2')  {
				$this->data['judul'] = 'Data Makan Lembur';
			}
			
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
						
			if($tampil_bulan_tahun=='') {
				$tampil_bulan_tahun = '';				
			} else {
				$bulan = substr($tampil_bulan_tahun,0,2);
				$tahun = substr($tampil_bulan_tahun,3,4);
				$tampil_bulan_tahun = $tahun."_".$bulan;
			}
				
			$list = $this->M_tabel_pemesanan_makan_lembur->get_datatables($set_verif, $tampil_bulan_tahun);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = '<small>'.$tampil->nomor_pemesanan.'</small>';
				$row[] = '<small>'.date('d-m-Y H:i:s', strtotime($tampil->created)).'</small>';
				$row[] = '<small>'.tanggal_indonesia($tampil->tanggal_pemesanan).'<br>'.date('H:i', strtotime($tampil->waktu_pemesanan_mulai)).' s/d '.date('H:i', strtotime($tampil->waktu_pemesanan_selesai)).'</small>';
				$row[] = '<small>'.$tampil->nama_unit.'</small>';
				$row[] = $tampil->jumlah;
				$row[] = '<small>'.$tampil->jenis_lembur.'</small>';

				if($_SESSION["grup"]==4) {
					if ($tampil->verified==null && $this->akses["ubah"] && $tampil->np_pemesan==$_SESSION['no_pokok'] && $tampil->tanggal_pemesanan>=date('Y-m-d'))
						$ubah = "<a class='btn btn-warning btn-xs edit_button' data-no='".$tampil->nomor_pemesanan."' data-toggle='modal' data-target='#modal_ubah' onclick='ubah(this)'>Ubah</a> ";
					else 
						$ubah = "";
				} else if($_SESSION["grup"]==5 && $tampil->tanggal_pemesanan>=date('Y-m-d')) {
					if ($tampil->verified==null && $tampil->np_pemesan==$_SESSION['no_pokok'])
						$ubah = "<a class='btn btn-warning btn-xs edit_button' data-no='".$tampil->nomor_pemesanan."' data-toggle='modal' data-target='#modal_ubah' onclick='ubah(this)'>Ubah</a> ";
					else 
						$ubah = "";
				} else {
					if (@$this->akses['persetujuan'] && $tampil->tanggal_pemesanan>=date('Y-m-d'))
						$ubah = "<a class='btn btn-warning btn-xs edit_button' data-no='".$tampil->nomor_pemesanan."' data-toggle='modal' data-target='#modal_ubah' onclick='ubah(this)'>Ubah</a> ";
					else 
						$ubah = "";
				}

				if(@$this->akses['batal'] && $tampil->verified=='3' && $tampil->tanggal_pemesanan >= date('Y-m-d')) {
					$batal = " <a class='btn btn-danger btn-xs' data-no='".$tampil->nomor_pemesanan."' onclick='batal(\"".$tampil->nomor_pemesanan."\")'>Batalkan</a> ";
				} else {
					$batal = "";
				}

				$verif_act="";
				//26 10 2021 - Tri Wibowo 7648 - kalo di dev group nya 11 kalo di prd group nya 14 Admin Dafasum - Kantin
				if($_SESSION["grup"]==14) {
					if ($tampil->verified=='1') {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-warning btn-xs'>Menunggu<br>Persetujuan Seksi Yanum</a> <br>";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Seksi Yanum</a> <br>";

						if ($set_verif=='1' && $tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif_act = "<a class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_persetujuan' data-no='".$tampil->nomor_pemesanan."' onclick='tampil_data_approval(this)'>Berikan Approval</a> <br>";
						else
							$verif_act = "";
					}
					else if ($tampil->verified=='3')
						$verif = "<a class='btn btn-success btn-xs'>Disetujui Seksi Yanum</a> <br>";
					else if ($tampil->verified=='4')
						$verif = "<a class='btn btn-danger btn-xs'>Ditolak Seksi Yanum</a> <br>";
					else if ($tampil->verified=='6')
						$verif = "<a class='btn btn-danger btn-xs'>Dibatalkan Seksi Yanum</a> <br>";
					else
						$verif = "";
				} else if($_SESSION["grup"]==5 || $_SESSION["grup"]==4) {
					if ($tampil->verified==null && $tampil->np_atasan==$_SESSION['no_pokok']) {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-warning btn-xs'>Menunggu<br>Persetujuan Anda</a> <br>";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Anda</a> <br>";

						if ($set_verif=='1' && $this->akses['persetujuan'])
							$verif_act = "<a class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_persetujuan' data-no='".$tampil->nomor_pemesanan."' onclick='tampil_data_approval(this)'>Berikan Approval</a> <br>";
						else
							$verif_act = "";
					}
					else if ($tampil->verified==null) {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-warning btn-xs'>Menunggu<br>Persetujuan Atasan</a> <br>";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Atasan</a> <br>";
					}
					else if ($tampil->verified=='1') {
						if ($tampil->tanggal_pemesanan>=date('Y-m-d'))
							$verif = "<a class='btn btn-success btn-xs'>Menunggu<br>Persetujuan Seksi Yanum</a> <br>";
						else
							$verif = "<a class='btn btn-danger btn-xs'>Tidak Direspon<br>Oleh Seksi Yanum</a> <br>";
					}
					else if ($tampil->verified=='2' && $tampil->np_atasan==$_SESSION['no_pokok'])
						$verif = "<a class='btn btn-danger btn-xs'>Anda Tolak</a> <br>";
					else if ($tampil->verified=='2')
						$verif = "<a class='btn btn-danger btn-xs'>Ditolak Atasan</a> <br>";
					else if ($tampil->verified=='3')
						$verif = "<a class='btn btn-success btn-xs'>Disetujui Seksi Yanum</a> <br>";
					else if ($tampil->verified=='4')
						$verif = "<a class='btn btn-danger btn-xs'>Ditolak Seksi Yanum</a> <br>";
					else if ($tampil->verified=='5')
						$verif = "<a class='btn btn-danger btn-xs'>".$tampil->keterangan_verified."</a> <br>";
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
					"recordsTotal" => $this->M_tabel_pemesanan_makan_lembur->count_all($set_verif, $tampil_bulan_tahun),
					"recordsFiltered" => $this->M_tabel_pemesanan_makan_lembur->count_filtered($set_verif, $tampil_bulan_tahun),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}

		public function filter_bulan() {
			//ambil tahun bulan tabel yang tersedia
            $get_month = $this->db->select('tanggal_pemesanan')->group_by('YEAR(tanggal_pemesanan), MONTH(tanggal_pemesanan)')->order_by('YEAR(tanggal_pemesanan) DESC, MONTH(tanggal_pemesanan) DESC')->get('ess_pemesanan_makan_lembur')->result();
            
            foreach($get_month as $row){
                $array_tahun_bulan[] = date('m-Y', strtotime($row->tanggal_pemesanan));
            }

            return $array_tahun_bulan;
		}

		function set_lokasi(){
            $lks = $this->input->post('lokasi');
            $lokasi = $this->db->where('nama', $lks)->where('status', '1')->get('mst_lokasi')->row()->id;

            $get_data['makanan'] = $this->db->where(array('a.status'=>'1', 'lokasi'=>$lokasi))->get('mst_makanan a')->result_array();
            echo json_encode($get_data);
        }

		public function save()
		{	
		
		
			$no = $this->input->post('ubah_no_pemesanan');
			$waktu = date('Y-m-d H:i:s');
			$makan = array();

			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$data['jumlah_pemesanan'] = array_sum($this->input->post('insert_jumlah'));
				$data['kode_unit'] = $this->input->post('insert_unit_kerja');
				$i=0; foreach ($this->input->post('insert_jenis_pesanan') as $pesanan) {
					$detail_makanan = $this->db->where('id', $pesanan)->get('mst_makanan')->row();
					$makan[$i]['id_makanan'] = $detail_makanan->id;
					$makan[$i]['nama_makanan'] = $detail_makanan->nama;
					$makan[$i]['harga'] = $detail_makanan->harga;
					$makan[$i]['jumlah'] = $this->input->post('insert_jumlah['.$i.']');
					$i++;
				}
			} else if($_SESSION["grup"]==5) { //jika Pengguna
				$data['jumlah_pemesanan'] = 1;
				$data['kode_unit'] = substr($_SESSION["kode_unit"],0,3).'00';
				$detail_makanan = $this->db->where('id', $this->input->post('insert_jenis_pesanan'))->get('mst_makanan')->row();
				$makan[0]['id_makanan'] = $detail_makanan->id;
				$makan[0]['nama_makanan'] = $detail_makanan->nama;
				$makan[0]['harga'] = $detail_makanan->harga;
				$makan[0]['jumlah'] = 1;
			} else {
				$data['jumlah_pemesanan'] = array_sum($this->input->post('insert_jumlah'));
				$i=0; foreach ($this->input->post('insert_jenis_pesanan') as $pesanan) {
					$detail_makanan = $this->db->where('id', $pesanan)->get('mst_makanan')->row();
					$makan[$i]['id_makanan'] = $detail_makanan->id;
					$makan[$i]['nama_makanan'] = $detail_makanan->nama;
					$makan[$i]['harga'] = $detail_makanan->harga;
					$makan[$i]['jumlah'] = $this->input->post('insert_jumlah['.$i.']');
					$i++;
				}
				$pesan = $makan;
				$data['jenis_pesanan'] = (string)json_encode($pesan);
				$data['updated'] = $waktu;

				$this->db->where('nomor_pemesanan', $no)->set($data)->update('ess_pemesanan_makan_lembur');
				if ($this->db->affected_rows()>0) {
					//$this->session->set_flashdata('success', 'Berhasil mengubah pemesanan makan lembur.');
					//$this->index();
					$this->session->set_userdata('notif_input', 'success');
				} else {
					//$this->session->set_flashdata('failed', 'Gagal mengubah pemesanan makan lembur. ');
					//$this->index();
					$this->session->set_userdata('notif_input', 'failed');
				}
				//redirect('food_n_go/konsumsi/pemesanan_makan_lembur/daftar_persetujuan');
				redirect(base_url($this->folder_controller.'pemesanan_makan_lembur/daftar_persetujuan'));
			}
			
				

			$data['np_pemesan'] = $_SESSION["no_pokok"];
			$data['nama_pemesan'] = $_SESSION["nama"];
			$data['np_atasan'] = $this->input->post('insert_np_atasan');
			$data['lokasi_lembur'] = $this->input->post('insert_lokasi_lembur');
			$data['nama_unit'] = $this->db->where('kode_unit', $data['kode_unit'])->get('mst_satuan_kerja')->row()->nama_unit;
			// $data['id_makanan'] = $this->input->post('insert_jenis_pesanan');
			// $data['jenis_pesanan'] = $this->db->where('id', $data['id_makanan'])->get('mst_makanan')->row()->nama;
			$pesan = $makan;
			$data['jenis_pesanan'] = (string)json_encode($pesan);
			// echo $data['jenis_pesanan'];exit;
			$data['jenis_lembur'] = $this->input->post('insert_jenis_lembur');
			$data['waktu_pemesanan_mulai'] = date('H:i:s', strtotime($this->input->post('insert_waktu_pemesanan_mulai')));
			$data['waktu_pemesanan_selesai'] = date('H:i:s', strtotime($this->input->post('insert_waktu_pemesanan_selesai')));
			$data['tanggal_pemesanan'] = date('Y-m-d', strtotime(substr($this->input->post('insert_tanggal_pemesanan'), -4).'-'.substr($this->input->post('insert_tanggal_pemesanan'), 3,2).'-'.substr($this->input->post('insert_tanggal_pemesanan'), 0,2)));

			$lembur = $this->db->where('nama', $data['jenis_lembur'])->get('mst_jenis_lembur')->row();
			$batas_waktu = $lembur->batas_waktu;
			
	
			if ($data['tanggal_pemesanan']<date('Y-m-d') || ($data['tanggal_pemesanan']==date('Y-m-d') && date('H:i:s')>=$data['waktu_pemesanan_mulai'])) {
				//$this->session->set_flashdata('failed', 'Gagal! Waktu Pemesanan Telah Lewat.');				
				//$this->index();exit;
				$this->session->set_userdata('notif_input', 'failed');
				redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
			} else if (($data['waktu_pemesanan_selesai']-$data['waktu_pemesanan_mulai']) < 3) {
				$data['verified'] = '5';
				$data['created'] = $waktu;
				$data['updated'] = $waktu;
				$data['keterangan_verified'] = 'Waktu Lembur Kurang Dari 3 Jam. ';
			}
					
			if ($no=='') {
				$data['nomor_pemesanan'] = generate_nomor_pemesanan_konsumsi();
				
				$data['created'] = $waktu;
				# tambahan keterangan, 2021-04-23
				$data['keterangan'] = $this->input->post('insert_keterangan',true);
				$this->db->set($data)->insert('ess_pemesanan_makan_lembur');
				if ($this->db->affected_rows()>0) {
					if (($data['waktu_pemesanan_selesai']-$data['waktu_pemesanan_mulai']) < 3)
						{
						//$this->session->set_flashdata('warning', 'Anda Tidak Mendapatkan Makanan Lembur, Minimal Lembur 3 jam');
						//$this->index();
						$this->session->set_userdata('notif_input', 'warning');
						redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
						}
					else if (date('Y-m-d')==$data['tanggal_pemesanan'] && date('H:i:s')>$batas_waktu)
						{
						//$this->session->set_flashdata('warning', 'Berhasil! '.$lembur->warning);
						//$this->index();
						$this->session->set_userdata('notif_input', 'warning');
						redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
						}
					else 
						{
						//$this->session->set_flashdata('success', 'Berhasil menambahkan pemesanan makan lembur.');
						//$this->index();
						$this->session->set_userdata('notif_input', 'success');
						redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
						}
				} else {
					//$this->session->set_flashdata('failed', 'Gagal menambahkan pemesanan makan lembur. ');
					//$this->index();
					$this->session->set_userdata('notif_input', 'failed');
					redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
				}
			}
			else {
				$data['updated'] = $waktu;
				# tambahan keterangan, 2021-04-23
				$data['keterangan'] = $this->input->post('ubah_keterangan',true);
				$this->db->where('nomor_pemesanan', $no)->set($data)->update('ess_pemesanan_makan_lembur');
				if ($this->db->affected_rows()>0) {
					if (($data['waktu_pemesanan_selesai']-$data['waktu_pemesanan_mulai']) < 3)
						{
							//$this->session->set_flashdata('warning', 'Anda Tidak Mendapatkan Makanan Lembur! Pemesanan Tidak Diproses!');
							//$this->index();
							$this->session->set_userdata('notif_input', 'warning');
							redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
						}
					else if (date('Y-m-d')==$data['tanggal_pemesanan'] && date('H:i:s')>$batas_waktu)
						{
							//$this->session->set_flashdata('warning', 'Berhasil! '.$lembur->warning);
							//$this->index();
							$this->session->set_userdata('notif_input', 'warning');
							redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
						}
					else 
						{
							//$this->session->set_flashdata('success', 'Berhasil mengubah pemesanan makan lembur.');
							//$this->index();
							$this->session->set_userdata('notif_input', 'success');
							redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
						}
				} else {
					//$this->session->set_flashdata('failed', 'Gagal mengubah pemesanan makan lembur. ');
					//$this->index();
					$this->session->set_userdata('notif_input', 'failed');
					redirect('food_n_go/konsumsi/pemesanan_makan_lembur'); exit;
				}
			}
			
		}
		
        function get_apv(){
            $kode_unit = $this->input->post('kode_unit');
			$this->load->model('m_approval');	
            $get_data = $this->m_approval->list_atasan_minimal_kadep([$kode_unit],null);
            echo json_encode($get_data);
        }
        
        function detail(){
            $no = $this->input->post('no_pemesanan');
	        $detail = $this->db->select('*, (select nama from mst_karyawan where no_pokok=a.np_atasan) as nama_atasan, (select nama from mst_karyawan where no_pokok=a.np_verified) as nama_verified, (select nama from mst_lokasi where id=a.lokasi_lembur) as lokasi')->where('nomor_pemesanan', $no)->get('ess_pemesanan_makan_lembur a')->row_array();
		    $total = 0;
		    $makanan = '';

	        if($_SESSION['grup']==5 && $detail['np_atasan']==$_SESSION['no_pokok']) {
	        	$detail['total_harga'] = 0;
	        	$detail['jenis_pemesanan'] = '';
	        }
	        else {
		        $pesanan = json_decode($detail['jenis_pesanan']);
		        for ($i=0; $i<count($pesanan); $i++) {
		        	$pesan = $pesanan[$i];
		        	$total_harga = $pesan->harga*$pesan->jumlah;
		        	$makanan.= '- '.$pesan->nama_makanan.' : '.$pesan->jumlah.' x '.rupiah($pesan->harga, 1).' = '.rupiah(($total_harga), 1).' <br>';
		        	$total = $total + $total_harga;
		        }
	        }
	        $detail['total_harga'] = rupiah($total, '1');
	        $detail['jenis_pemesanan'] = $makanan;
            echo json_encode($detail);
        }
        
        function persetujuan(){
			$this->data['judul'] = "Persetujuan Konsumsi Lembur";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

        	if($this->akses["persetujuan"]) {
	            $no = $this->input->post('no_pemesanan');
	            $apv = $this->input->post('verified');
	            $detail = $this->db->where('nomor_pemesanan', $no)->get('ess_pemesanan_makan_lembur')->row_array();

	            if ($apv=='1') {
	            	if ($detail['verified']==null && $_SESSION["grup"]==5 && $_SESSION["no_pokok"]==$detail['np_atasan']) {
	            		$verified['verified'] = '1';
		        		$verified['waktu_verified_atasan'] = date('Y-m-d H:i:s');
	            	}
					//26 10 2021 - Tri Wibowo 7648 - kalo di dev group nya 11 kalo di prd group nya 14 Admin Dafasum - Kantin
	            	else if ($detail['verified']=='1' && $_SESSION["grup"]==14) {
	            		$verified['verified'] = '3';
		            	$verified['np_verified'] = $_SESSION['no_pokok'];
		        		$verified['waktu_verified_admin'] = date('Y-m-d H:i:s');
		            }
		        	$this->db->where('id', $detail['id'])->set($verified)->update('ess_pemesanan_makan_lembur');
		        	//$this->session->set_flashdata('success', 'Berhasil Memberikan Persetujuan');
					$this->session->set_userdata('notif_input', 'success');
						
						
	            } else if ($apv=='0') {
	            	$ket = $this->input->post('keterangan');
	            	$verified['keterangan_verified'] = $ket;

	            	if ($detail['verified']==null && $_SESSION["grup"]==5 && $_SESSION["no_pokok"]==$detail['np_atasan']) {
	            		$verified['verified'] = '2';
		        		$verified['waktu_verified_atasan'] = date('Y-m-d H:i:s');
	            	}
					//26 10 2021 - Tri Wibowo 7648 - kalo di dev group nya 11 kalo di prd group nya 14 Admin Dafasum - Kantin
	            	else if ($detail['verified']=='1' && $_SESSION["grup"]==14) {
	            		$verified['verified'] = '4';
		            	$verified['np_verified'] = $_SESSION['no_pokok'];
		        		$verified['waktu_verified_admin'] = date('Y-m-d H:i:s');
		            }
		        	$this->db->where('id', $detail['id'])->set($verified)->update('ess_pemesanan_makan_lembur');
	            	//$this->session->set_flashdata('success', 'Berhasil Memberikan Penolakan');
					$this->session->set_userdata('notif_input', 'success');
					
	            } else {
	            	//$this->session->set_flashdata('warning', 'Gagal Memberikan Approval');
					$this->session->set_userdata('notif_input', 'failed');
					
	            }
	        } else {
	           // $this->session->set_flashdata('warning', 'Anda Tidak Memiliki Akses!');
				$this->session->set_userdata('notif_input', 'failed');
						
	        }
	        
			//$this->daftar_persetujuan();
			redirect('food_n_go/konsumsi/pemesanan_makan_lembur/daftar_persetujuan'); exit;
	        
        }

        function save_realisasi(){
        	$no = $this->input->post('no_pemesanan');
        	$realisasi = $this->input->post('realisasi');
        	$this->db->set('realisasi_pengeluaran', $realisasi)->where('nomor_pemesanan', $no)->update('ess_pemesanan_makan_lembur');
        	
        	echo json_encode(array('status'=>1));
        }

        function batal(){
        	if ($this->input->is_ajax_request()) {
        		$no = $this->input->post('no', true);
        		$set['verified'] = '6';
        		$set['np_batal'] = $_SESSION['no_pokok'];
        		$set['waktu_batal'] = date('Y-m-d H:i:s');
        		$this->db->where('nomor_pemesanan', $no)->set($set)->update('ess_pemesanan_makan_lembur');

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
        
	}
	
	/* End of file data_kehadiran.php */
	/* Location: ./application/controllers/kehadiran/data_kehadiran.php */