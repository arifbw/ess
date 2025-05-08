<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Data_pamlek extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'informasi/';
			$this->folder_model = 'informasi/';
			$this->folder_controller = 'informasi/';
			
			$this->akses = array();
						
			$this->load->model($this->folder_model."m_data_pamlek");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Data Pamlek";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_pamlek";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");

				
				$this->load->model("master_data/m_karyawan");
				
				if($this->akses["pilih seluruh karyawan"]){
					$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan();				
				}
				else if($this->akses["pilih karyawan diadministrasikan"]){
					$this->data["daftar_akses_karyawan"] = $this->m_karyawan->daftar_karyawan_diadministrasikan();
				}
				else{				
					$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
				}
			
				$this->data["arr_periode"]=periode();
				
				$js_header_script = "<script>
					$(document).ready(function() {
						$('.select2').select2();
					});
				</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel_data_pamlek($periode,$np){			
			$mesin_perizinan = "'".str_replace(",","','",$this->m_setting->ambil_pengaturan("mesin perizinan"))."'";

			$list = $this->m_data_pamlek->get_datatable_pamlek($periode,$np,$mesin_perizinan);
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;			
				$row[] = $tampil->jenis;
				$row[] = $tampil->tipe;
				$row[] = $tampil->machine_id;
				$row[] = tanggal_waktu($tampil->tapping_time);
								
				$data[] = $row;
			}

			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->m_data_pamlek->count_all($periode),
						"recordsFiltered" => $this->m_data_pamlek->count_filtered($periode,$np,$mesin_perizinan),
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
		}
	}
	
	/* End of file data_pamlek.php */
	/* Location: ./application/controllers/informasi/data_pamlek.php */