<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Poh extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			
			// Report all errors
            error_reporting(E_ALL);

            // Display errors in output
            ini_set('display_errors', 1);
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_model_master_data = 'master_data/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."m_poh");
			$this->load->model($this->folder_model_master_data."m_jabatan");
			$this->load->model($this->folder_model_master_data."m_karyawan");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data["skep"] = "SKEP-832/IX/2016";
			$this->data['judul'] = "Pemegang Operasional Harian (POH)";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
		}
		
		public function index(){
			izin($this->akses["akses"]);
			//echo $this->akses["lihat"];die();
			$this->data["akses"] = $this->akses;
			$this->data['content'] = $this->folder_view."poh";
			$this->data["navigasi_menu"] = menu_helper();
			
			array_push($this->data['js_sources'],"osdm/poh");
			if($this->akses["tambah"] or $this->akses["ubah"]){
				$this->data["daftar_jabatan_struktural"] = $this->m_jabatan->daftar_jabatan_struktural();
			}

			if($this->input->post()){
				
				if(!strcmp($this->input->post("aksi"),"tambah")){
					izin($this->akses["tambah"]);

					$this->data['kode_jabatan'] = $this->input->post("jabatan");
					$this->data['np_definitif'] = $this->input->post("np_definitif");
					$this->data['nama_definitif'] = $this->input->post("nama_definitif");
					$this->data['sesuai_skep'] = $this->input->post("sesuai_skep");
					$this->data['np_poh'] = $this->input->post("karyawan");
					$this->data['tanggal_mulai'] = $this->input->post("tanggal_mulai");
					$this->data['tanggal_selesai'] = $this->input->post("tanggal_selesai");
					$this->data['nomor_nota_dinas'] = $this->input->post("nomor_nota_dinas");
					$this->data['keterangan'] = $this->input->post("keterangan");
					
					$tambah = $this->tambah($this->data['kode_jabatan'],$this->data['np_definitif'],$this->data['nama_definitif'],$this->data['np_poh'],$this->data['tanggal_mulai'],$this->data['tanggal_selesai'],$this->data['nomor_nota_dinas'],$this->data['keterangan']);
					
					$this->data['panel_tambah'] = "in";
					
					if($tambah['status']){
						$this->data['success'] = $tambah["success_info"];
						$this->data['panel_tambah'] = "";
						$this->data['kode_jabatan'] = "";
						$this->data['np_definitif'] = "";
						$this->data['nama_definitif'] = "";
						$this->data['sesuai_skep'] = "";
						$this->data['np_poh'] = "";
						$this->data['tanggal_mulai'] = "";
						$this->data['tanggal_selesai'] = "";
						$this->data['nomor_nota_dinas'] = "";
						$this->data['keterangan'] = "";
					}
					else{
						$this->data['warning'] = $tambah['error_info'];
					}
				}
				else if(!strcmp($this->input->post("aksi"),"ubah")){
					izin($this->akses["ubah"]);
					
					$this->data['jabatan'] = $this->input->post("jabatan");
					$this->data['jabatan_ubah'] = $this->input->post("jabatan_ubah");
					$this->data['np_definitif_ubah'] = $this->input->post("np_definitif_ubah");
					$this->data['nama_definitif_ubah'] = $this->input->post("nama_definitif_ubah");
					$this->data['sesuai_skep_ubah'] = $this->input->post("sesuai_skep");
					$this->data['karyawan_poh_ubah'] = $this->input->post("karyawan_poh_ubah");
					$this->data['karyawan_ubah'] = $this->input->post("karyawan_ubah");
					$this->data['tanggal_mulai'] = $this->input->post("tanggal_mulai");
					$this->data['tanggal_mulai_ubah'] = $this->input->post("tanggal_mulai_ubah");
					$this->data['tanggal_selesai'] = $this->input->post("tanggal_selesai");
					$this->data['tanggal_selesai_ubah'] = $this->input->post("tanggal_selesai_ubah");
					$this->data['nomor_nota_dinas'] = $this->input->post("nomor_nota_dinas");
					$this->data['nomor_nota_dinas_ubah'] = $this->input->post("nomor_nota_dinas_ubah");
					$this->data['keterangan'] = $this->input->post("keterangan");
					$this->data['keterangan_ubah'] = $this->input->post("keterangan_ubah");
					
					$ubah = $this->ubah($this->data["jabatan"],$this->data["tanggal_mulai"],$this->data["tanggal_selesai"],$this->data["jabatan_ubah"],$this->data["np_definitif_ubah"],$this->data["nama_definitif_ubah"],$this->data["sesuai_skep_ubah"],$this->data["karyawan_ubah"],$this->data["tanggal_mulai_ubah"],$this->data["tanggal_selesai_ubah"],$this->data['nomor_nota_dinas_ubah'],$this->data['keterangan_ubah']);

					if($ubah["status"]){
						$this->data['success'] = "Perubahan Pemegang Operasional Harian (POH) berhasil dilakukan.";
					}
					else{
						$this->data['warning'] = $ubah['error_info'];
					}

					$this->data["nama"] = "";
					$this->data["status"] = "";
					$this->data['panel_tambah'] = "";
				}
				else{
					$this->data['kode_jabatan'] = "";
					$this->data['np_definitif'] = "";
					$this->data['nama_definitif'] = "";
					$this->data['sesuai_skep'] = "";
					$this->data['np_poh'] = "";
					$this->data['tanggal_mulai'] = "";
					$this->data['tanggal_selesai'] = "";
					$this->data['nomor_nota_dinas'] = "";
					$this->data['keterangan'] = "";
					$this->data['panel_tambah'] = "";
				}
			}
			else{
				$this->data['kode_jabatan'] = "";
				$this->data['np_definitif'] = "";
				$this->data['nama_definitif'] = "";
				$this->data['sesuai_skep'] = "";
				$this->data['np_poh'] = "";
				$this->data['tanggal_mulai'] = "";
				$this->data['tanggal_selesai'] = "";
				$this->data['nomor_nota_dinas'] = "";
				$this->data['keterangan'] = "";
				$this->data['panel_tambah'] = "";
			}
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");
				$js_header_script = "<script>
										$(document).ready(function() {
											$('.select2').select2();
										});
									</script>";
/* 											$('#tabel_poh').DataTable({
												responsive: true
											});
 */				array_push($this->data["js_header_script"],$js_header_script);
								
				$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
				$this->m_log->tambah($log);
			}
			
			$this->load->view('template',$this->data);
		}
		
		private function tambah($kode_jabatan,$np_definitif,$nama_definitif,$np_poh,$tanggal_mulai,$tanggal_selesai,$nomor_nota_dinas,$keterangan){
			$return = array("status" => false, "error_info" => "", "success_info" => "");
			$data_jabatan = $this->m_jabatan->data_jabatan($kode_jabatan);
			if($this->m_poh->cek_tambah_poh($kode_jabatan,$tanggal_mulai,$tanggal_selesai)){
				
				$data_poh = $this->m_karyawan->get_karyawan($np_poh);
											
				$data = array(
							"tanggal_mulai" => $tanggal_mulai,
							"tanggal_selesai" => $tanggal_selesai,
							"kode_unit" => $data_jabatan[0]['kode_unit'],
							"nama_unit" => $data_jabatan[0]['nama_unit'],
							"kode_jabatan" => $kode_jabatan,
							"nama_jabatan" => $data_jabatan[0]['nama_jabatan'],
							"np_definitif" => $np_definitif,
							"nama_definitif" => $nama_definitif,
							"np_poh" => $np_poh,
							"nama_poh" => $data_poh['nama'],
							"nomor_nota_dinas" => $nomor_nota_dinas,
							"keterangan" => $keterangan,
							"dibuat_pada" => date('Y-m-d H:i:s'),
							"dibuat_oleh_np" => $this->session->userdata('no_pokok')
						);
						
				$this->m_poh->tambah($data);
				
								
				if($this->m_poh->cek_hasil_poh($tanggal_mulai,$tanggal_selesai,$kode_jabatan,$np_definitif,$np_poh,$nomor_nota_dinas,$keterangan)){
					$return["status"] = true;
										
					//===== Log Start =====
					$arr_data_baru = $this->m_poh->data_poh($kode_jabatan,$tanggal_mulai,$tanggal_selesai);
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
						"deskripsi" => "insert ".strtolower(preg_replace("/_/"," ",__CLASS__)),						
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					//===== Log end =====
					
					$return["success_info"] = "Penambahan data ".$data_poh['nama']." sebagai Pemegang Operasional Harian (POH) untuk ".$data_jabatan[0]['nama_jabatan']." <b>berhasil</b> dilakukan.";
					
					
					
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Penambahan Pemegang Operasional Harian (POH) <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = false;
				$return["error_info"] = $this->data['judul']." untuk jabatan <b>".$data_jabatan[0]['nama_jabatan']."</b> pada periode <b>".tanggal($tanggal_mulai)."</b> sampai dengan <b>".tanggal($tanggal_selesai)."</b> sudah ada.";
			}
			return $return;
		}
	
		private function ubah($jabatan,$tanggal_mulai,$tanggal_selesai,$jabatan_ubah,$np_definitif_ubah,$nama_definitif_ubah,$sesuai_skep_ubah,$karyawan_ubah,$tanggal_mulai_ubah,$tanggal_selesai_ubah,$nomor_nota_dinas_ubah,$keterangan_ubah){
			$return = array("status" => false, "error_info" => "");
			$cek = $this->m_poh->cek_ubah_poh($jabatan,$tanggal_mulai,$tanggal_selesai,$jabatan_ubah,$np_definitif_ubah,$nama_definitif_ubah,$sesuai_skep_ubah,$karyawan_ubah,$tanggal_mulai_ubah,$tanggal_selesai_ubah,$nomor_nota_dinas_ubah,$keterangan_ubah);
			if($cek["status"]){
				$data_jabatan = $this->m_jabatan->data_jabatan($jabatan_ubah);
				$data_poh = $this->m_karyawan->get_karyawan($karyawan_ubah);
				
				$set = array("tanggal_mulai"=>$tanggal_mulai_ubah,
							"tanggal_selesai"=>$tanggal_selesai_ubah,
							"kode_unit" => $data_jabatan[0]['kode_unit'],
							"nama_unit" => $data_jabatan[0]['nama_unit'],
							"kode_jabatan" => $jabatan_ubah,
							"nama_jabatan" => $data_jabatan[0]['nama_jabatan'],
							"np_definitif"=>$np_definitif_ubah,
							"nama_definitif"=>$nama_definitif_ubah,
							"sesuai_skep"=>$sesuai_skep_ubah,
							"np_poh" => $karyawan_ubah,
							"nama_poh" => $data_poh['nama'],
							"nomor_nota_dinas"=>$nomor_nota_dinas_ubah,
							"keterangan"=>$keterangan_ubah);
				
				$arr_data_lama = $this->m_poh->data_poh($jabatan,$tanggal_mulai,$tanggal_selesai);
				$log_data_lama = "";
				
				foreach($arr_data_lama as $key => $value){
					if(strcmp($key,"id")!=0){
						if(!empty($log_data_lama)){
							$log_data_lama .= "<br>";
						}
						$log_data_lama .= "$key = $value";
					}
				}
				$this->m_poh->ubah($set,$jabatan,$tanggal_mulai,$tanggal_selesai);

				if($this->m_poh->cek_hasil_poh($tanggal_mulai_ubah,$tanggal_selesai_ubah,$jabatan_ubah,$np_definitif_ubah,$karyawan_ubah,$nomor_nota_dinas_ubah,$keterangan_ubah)){
					$return["status"] = true;
					
					$log_data_baru = "";
					foreach($set as $key => $value){
						if(!empty($log_data_baru)){
							$log_data_baru .= "<br>";
						}
						$log_data_baru .= "$key = $value";
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $arr_data_lama["id"],
						"deskripsi" => "ubah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => $log_data_baru,
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
				}
				else{
					$return["status"] = false;
					$return["error_info"] = "Perubahan Pemegang Operasional Harian (POH) <b>Gagal</b> Dilakukan.";
				}
			}
			else{
				$return["status"] = $cek["status"];
				$return["error_info"] = $cek["error_info"];	
			}

			return $return;
		}
		
		public function pejabat_definitif(){
			$kode_jabatan = $_POST["jabatan"];
			$pemegang = $this->m_jabatan->get_pejabat($kode_jabatan);
			
			echo json_encode($pemegang);
		}
		
		public function karyawan_calon_poh(){
			$kelompok_jabatan = $_POST["kelompok_jabatan"];
			$np_Pemegang_definitif = $_POST["np_pejabat_definitif"];
			$sesuai_skep = $_POST["sesuai_skep"];
			$karyawan_calon_poh = $this->m_poh->karyawan_calon_poh($kelompok_jabatan,$np_Pemegang_definitif,$sesuai_skep);

			echo json_encode($karyawan_calon_poh);
		}
		
		public function tabel_poh(){//var_dump($this);
			$display_poh = $_POST['display_poh'];
			$list = $this->m_poh->get_datatable_poh($display_poh);
			$data = array();
			$no = $_POST['start'];
			
			$tombol_ubah = "";
			if($this->akses["ubah"]){
				$tombol_ubah = "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button>";
			}
			
			$tombol_hapus = "";
			if($this->akses["hapus"]){				
				$tombol_hapus = " <button class='btn btn-danger btn-xs' onclick='hapus(this)'>Hapus</button>";
			}
			
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$tanggal = tanggal($tampil->tanggal_mulai);
				if(strcmp($tampil->tanggal_mulai,$tampil->tanggal_selesai)!=0){
					$tanggal .= "<br>sampai dengan<br>".tanggal($tampil->tanggal_selesai);
				}
				$row[] = $tanggal;
				$row[] = $tampil->kode_unit." - ".$tampil->nama_unit;
				$row[] = $tampil->kode_jabatan." - ".$tampil->nama_jabatan;
				$definitif = $tampil->np_definitif;
				if(!empty($definitif)){
					$definitif .= " - ".$tampil->nama_definitif;
				}
				$row[] = $definitif;
				$row[] = $tampil->np_poh." - ".$tampil->nama_poh;
				$row[] = $tampil->nomor_nota_dinas;
				$row[] = $tampil->keterangan;
				
				$row[] = $tombol_ubah.$tombol_hapus."<input type='hidden' value='".$tampil->sesuai_skep."'>";
				$data[] = $row;
			}

			$recordsFiltered = $this->m_poh->count_filtered($display_poh);
			$recordsTotal = $this->m_poh->count_all();
//var_dump($data);
//var_dump($recordsFiltered);
//var_dump($recordsTotal);
			$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $recordsTotal,
						"recordsFiltered" => $recordsFiltered,
						"data" => $data
					);
			//output to json format
			echo json_encode($output);
		}
		
		public function hapus($data=null)
		{
			$this->load->helper('tanggal_helper');
			$pisah = explode('%7C',$data);
			$mulai 				= $pisah[0];
			$selesai 			= $pisah[1];
			$kode_jabatan 		= $pisah[2];
			$np_definitif 		= $pisah[3];
			$np_poh				= $pisah[4];
			
			$mulai 				= str_replace('%20',' ',$mulai);
			$selesai 			= str_replace('%20',' ',$selesai);
						
			$tanggal_mulai 		= indonesia_to_date($mulai);
			$tanggal_selesai 	= indonesia_to_date($selesai);
			
			if($tanggal_mulai != null && $tanggal_selesai != null && $kode_jabatan != null && $np_definitif != null && $np_poh != null) {
				$get = $this->m_poh->ambil_poh_by_data($tanggal_mulai,$tanggal_selesai,$kode_jabatan,$np_definitif,$np_poh);
				$hps = $this->m_poh->hapus_by_data($tanggal_mulai,$tanggal_selesai,$kode_jabatan,$np_definitif,$np_poh);
				if((bool)$hps == true) {
					$return["status"] = true;
					
					$log_data_lama = "";
					foreach($get as $key => $value){
						if(strcmp($key,"id")!=0){
							if(!empty($log_data_lama)){
								$log_data_lama .= "<br>";
							}
							$log_data_lama .= "$key = $value";
						}
					}
					
					$log = array(
						"id_pengguna" => $this->session->userdata("id_pengguna"),
						"id_modul" => $this->data['id_modul'],
						"id_target" => $get["id"],
						"deskripsi" => "hapus ".strtolower(preg_replace("/_/"," ",__CLASS__)),
						"kondisi_lama" => $log_data_lama,
						"kondisi_baru" => '',
						"alamat_ip" => $this->data["ip_address"],
						"waktu" => date("Y-m-d H:i:s")
					);
					$this->m_log->tambah($log);
					$this->session->set_flashdata('success', 'Data POH <b>Berhasil</b> Dihapus.');
				}
				else {
					$this->session->set_flashdata('warning', 'Data POH <b>Gagal</b> Dihapus.');
				}
				redirect('osdm/poh');
			}
			else {
				$this->session->set_flashdata('warning', 'Data POH <b>Gagal</b> Dihapus.');
				redirect('osdm/poh');
			}
		}
	}
	
	/* End of file poh.php */
	/* Location: ./application/controllers/osdm/poh.php */