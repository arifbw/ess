<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Rekapitulasi_bulanan extends CI_Controller {
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
			
			$this->load->model($this->folder_model."m_rekapitulasi_bulanan");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "Rekapitulasi Bulanan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
		}
		
		public function index()
		{			
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."rekapitulasi_bulanan";
			
			array_push($this->data['js_sources'],"informasi/rekapitulasi_bulanan");
			
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
									rekapitulasi_bulanan();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);				
			}
			
			$this->load->view('template',$this->data);
		}
		
		function ajax_rekapitulasi_bulanan($no_pokok,$periode){
			$periode_awal = str_replace("_","-",$periode)."-01";
			$periode_akhir = date("Y-m-t",strtotime($periode_awal));
			
			$this->data["arr_tanggal"] = array();
			for($i=1;$i<=(int)date("t",strtotime($periode_awal));$i++){
				array_push($this->data["arr_tanggal"],str_replace("_","-",$periode)."-".str_pad($i,2,"0",STR_PAD_LEFT));
			}
			
			$this->load->model("master_data/m_hari_libur");
			$arr_libur = $this->m_hari_libur->daftar_hari_libur_periode($periode_awal,$periode_akhir);
			
			$this->data["arr_tanggal_libur"] = array();
			$this->data["arr_nama_libur"] = array();
			foreach($arr_libur as $libur){
				array_push($this->data["arr_tanggal_libur"],$libur["tanggal"]);
				$this->data["arr_nama_libur"][$libur["tanggal"]] = $libur["deskripsi"];
			}
			
			$arr_jadwal_kerja = $this->m_rekapitulasi_bulanan->jadwal_kerja($no_pokok,$periode);
			$this->data["arr_jadwal_kerja"] = array();
			
			foreach($arr_jadwal_kerja as $jadwal_kerja){
				$this->data["arr_jadwal_kerja"][$jadwal_kerja["tertanggal"]]=$jadwal_kerja;
			}
			
			$this->load->model("lembur/m_pengajuan_lembur");
			$arr_lembur = $this->m_pengajuan_lembur->lembur_karyawan_per_bulan($no_pokok,$periode);
			$this->data["arr_lembur"] = array();
			
			foreach($arr_lembur as $lembur){
				$this->data["arr_lembur"][$lembur["tgl_dws"]][$lembur["jenis_lembur"]] = $lembur;
			}
			
			$this->load->model("perizinan/m_perizinan");
			$arr_perizinan = $this->m_perizinan->perizinan_karyawan_per_bulan($no_pokok,$periode);
			$this->data["arr_perizinan"] = array();
			
			/* foreach($arr_perizinan as $perizinan){
				$this->data["arr_perizinan"][$perizinan["tgl_dws"]][$perizinan["jenis_lembur"]] = $perizinan;
			} */
			
			//untuk keperluan check apakah cuti bersama
			$this->data['no_pokok'] = $no_pokok;
			
			$this->load->view($this->folder_view."ajax_rekapitulasi_bulanan",$this->data);
		}
	}
	
	/* End of file rekapitulasi_bulanan.php */
	/* Location: ./application/controllers/informasi/rekapitulasi_bulanan.php */