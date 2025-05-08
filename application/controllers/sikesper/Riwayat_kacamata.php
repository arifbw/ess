<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Riwayat_kacamata extends CI_Controller {
		public function __construct(){
			parent::__construct();
	        $this->load->library('Excel_reader');
			$this->load->library('phpexcel');
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'sikesper/';
			$this->folder_model = 'sikesper/';
			$this->folder_controller = 'sikesper/';
			
			$this->akses = array();
					
			$this->load->helper("tanggal_helper");
					
			$this->load->model($this->folder_model."m_riwayat_kacamata");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Riwayat Kacamata";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index() {
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."riwayat_kacamata";

			$this->data['panel_tambah'] = "in";

			$this->data["tanggal"] = "";
			$this->data["deskripsi"] = "";
			$this->data["panel_tambah"] = "";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
				
				$this->load->model("master_data/m_karyawan");
				
				if($this->akses["pilih seluruh karyawan"]){
					$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();
				}
				else{				
					$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
				}
						
				$js_header_script = "<script>
					$(document).ready(function() {
						$('.select2').select2();
					});
				</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel($np=null){
			
			$list = $this->m_riwayat_kacamata->get_datatable($np);
					
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan.' - '.$tampil->nama.'<br><small>('.$tampil->nama_unit_singkat.')</small>';
				$row[] = $tampil->task_type;
				$row[] = tanggal_indonesia($tampil->task_on);
				$tgl_claim = date_create($tampil->task_on);
				date_add($tgl_claim, date_interval_create_from_date_string('2 years'));
                $row[] = tanggal_indonesia(date('Y-m-d', strtotime(date_format($tgl_claim, 'Y-m-d') . ' +1 day')));
				$row[] = $tampil->nama_keluarga;
					
				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_riwayat_kacamata->count_all($np),
						"recordsFiltered" => $this->m_riwayat_kacamata->count_filtered($np),
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
		}
		
		private function encrypt_decrypt($action, $string){
			$output = false;
			$encrypt_method = "AES-256-CBC";
			$secret_key = 'zxfajaSsd1fjDwASjA12SAGSHga3yus'.date('Ymd');
			$secret_iv = 'zxASsadkmjku4jLOIh2jfGda5'.date('Ymd');
			// hash
			$key = hash('sha256', $secret_key);
			
			// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
			$iv = substr(hash('sha256', $secret_iv), 0, 16);
			if ( $action == 'encrypt' ) {
				$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
				$output = base64_encode($output);
			  } else if( $action == 'decrypt' ) {
					$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
					
					$pisah 				= explode('|',$output);
					$datetime_request 	= $pisah[3];
					/*
					$datetime_expired 	= date('Y-m-d H:i:s',strtotime('+10 seconds',strtotime($datetime_request))); 

					$datetime_now		= date('Y-m-d H:i:s');
					
					if($datetime_now > $datetime_expired || !$datetime_request){
						$output = false;
					}	
					*/
				}
			return $output;
		}
		
		public function getDetailPeriksa()
		{
			$bill = $this->input->post('bill');
			$np = $this->input->post('np');
			$tgl = $this->input->post('tgl');
			$karyawan = $this->m_riwayat_kacamata->detailKaryawan($np);
			$riwayat = $this->m_riwayat_kacamata->riwayatPemeriksaan($np, $bill, $tgl);

			if($karyawan){
				$display = $this->load->view('sikesper/detail_riwayat_kacamata', [
					'data' => $karyawan,
					'riwayat' => $riwayat
				], TRUE);

				echo json_encode(['response' => $display, 'status' => 200]);
			}

		}
	}
	
	/* End of file skep.php */
	/* Location: ./application/controllers/informasi/skep.php */
