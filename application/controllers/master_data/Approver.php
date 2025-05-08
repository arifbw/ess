<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Approver extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'master_data/';
			$this->folder_model = 'master_data/';
			$this->folder_controller = 'master_data/';
			
			$this->akses = array();
			
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			$this->load->helper("perizinan_helper");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "Approver";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
		}
		
		public function index()
		{
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));			
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."approver";
                        
            //$this->data['array_tahun_bulan'] 	= $this->db->select('TABLE_NAME')->like('TABLE_NAME','pamlek_data_','after')->order_by('TABLE_NAME','DESC')->get('information_schema.TABLES')->result();
			
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_approver()
		{
            
			$this->load->model($this->folder_model."M_tabel_approver");
			
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
			
			$list 	= $this->M_tabel_approver->get_datatables($var);	
			
			
			$data = array();
			$no = $_POST['start'];
			
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->personel_number;
				$row[] = $tampil->nama_karyawan;
				$row[] = $tampil->np_approver_1;
				$row[] = $tampil->nama_approver_1;
				$row[] = $tampil->np_approver_2;
				$row[] = $tampil->nama_approver_2;
				$row[] = $tampil->np_approver_3;
				$row[] = $tampil->nama_approver_3;
				
				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->M_tabel_approver->count_all($var),
							"recordsFiltered" => $this->M_tabel_approver->count_filtered($var),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */