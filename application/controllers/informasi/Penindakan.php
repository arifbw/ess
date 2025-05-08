<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Penindakan extends CI_Controller {
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
			
			$this->folder_view = 'informasi/';
			$this->folder_model = 'informasi/';
			$this->folder_controller = 'informasi/';
			
			$this->akses = array();
						
			$this->load->model($this->folder_model."m_data_penindakan");
		
			$this->data["is_with_sidebar"] = true;
								
			$this->data['judul'] = "History Penindakan";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			$this->load->model("master_data/m_karyawan");
			$this->load->model("master_data/m_satuan_kerja");
			izin($this->akses["akses"]);
		}
		
		public function index(){
			//	echo __FILE__ . __LINE__;die(var_dump($this->akses));	
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."data_penindakan";
			
			if($this->akses["lihat"]){
				array_push($this->data['css_plugin_sources'],"select2/select2.min.css");
				array_push($this->data['js_plugin_sources'],"select2/select2.min.js");

				
				$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>"","nama"=>""));
				$this->data["daftar_akses_unit_kerja"] = array(array("kode_unit"=>"", "nama_unit"=>""));
				
				$pilihan_karyawan = "";
				if($this->akses["pilih seluruh karyawan"]){
					$pilihan_karyawan = "pilihan_karyawan();";
					$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja();
				}
				else if($this->akses["pilih karyawan diadministrasikan"]){
					$pilihan_karyawan = "pilihan_karyawan();";
					$this->data["daftar_akses_unit_kerja"] = $this->m_satuan_kerja->daftar_satuan_kerja_diadministrasikan();
				}
				else{
					$this->data["daftar_akses_karyawan"] = array(array("no_pokok"=>$this->session->userdata("no_pokok"),"nama"=>$this->session->userdata("nama")));
					$this->data["daftar_akses_unit_kerja"] = array(array("kode_unit"=>$this->session->userdata("kode_unit"), "nama_unit"=>$this->session->userdata("nama_unit")));
				}
			
				$this->data["arr_periode"]=periode();
				
				$js_header_script = "<script>
								$(document).ready(function() {
									$pilihan_karyawan
									$('.select2').select2();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
			}
			
			$this->load->view('template',$this->data);
		}
		
		public function tabel_data_penindakan(){
			$set['kode_unit'] = $this->input->post("kode_unit");
			$set['np_karyawan'] = $this->input->post("np_karyawan");
			$set['periode'] = $this->input->post("periode");
			
			$list = $this->m_data_penindakan->get_datatable_keterlambatan($set);
			$data = array();
			$no = $_POST['start'];

			foreach ($list as $val) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $val['np_karyawan'];
				$row[] = $val['nama'];
				$row[] = $val['nama_unit'];
				$row[] = $val['tahun'];
				$row[] = $val['tanggal_restart'];
				$row[] = "<a class='btn btn-primary btn-xs' target='_blank' href='".base_url('uploads/evidence_penindakan/'.$val['evidence'])."'>Lihat Evidence</a>";

				$data[] = $row;
			}

			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->m_data_penindakan->count_all($set),
				"recordsFiltered" => $this->m_data_penindakan->count_filtered($set),
				"data" => $data
			);
			
			$log = array(
				"id_pengguna" => $this->session->userdata("id_pengguna"),
				"id_modul" => $this->data['id_modul'],
				"deskripsi" => "lihat penindakan <br>Kode unit kerja : ".$set['kode_unit']."<br>NP Karyawan : ".$set['np_karyawan']."<br>Tahun : ".$set['periode'],
				"alamat_ip" => $this->data["ip_address"],
				"waktu" => date("Y-m-d H:i:s")
			);
			$this->m_log->tambah($log);

			//output to json format
			echo json_encode($output);
		}
		
		public function daftar_karyawan(){
			$kode_unit = $_POST["unit_kerja"];
			$hasil["np_pengguna"] = $this->session->userdata("no_pokok");
			$hasil["karyawan"] = $this->m_karyawan->get_karyawan_unit_kerja($kode_unit);
			echo json_encode($hasil);
		}

		public function restart(){
			$karyawan = $this->db->where('np_karyawan', $this->input->post('np'))->limit(1)->get('ess_cico_'.date('Y_m'))->row_array();
			$data['np_karyawan'] = $this->input->post('np');
			$data['nama'] = $karyawan['nama'];
			$data['kode_unit'] = $karyawan['kode_unit'];
			$data['nama_unit'] = $karyawan['nama_unit'];
			$data['tahun'] = $this->input->post('tahun');
			$data['tanggal_restart'] = date('Y-m-d');
			$data['restart_by'] = $this->session->userdata('no_pokok');
			$data['created'] = date('Y-m-d H:i:s');

			if ($_FILES['file_evidence']['name'] != '') {
	            $tempFile = $_FILES['file_evidence']['tmp_name'];
	            $fileName = $_FILES['file_evidence']['name'];
	            $targetPath = './uploads/evidence_penindakan/';
	            $pecahnama = explode('.',$fileName);
	            $ekstensifile = $pecahnama[(count($pecahnama)-1)];
	            $time1 = date('YmdHis');
	            $namafile = 'Penindakan_'.$time1.'.'.$ekstensifile;
	            $targetFile = $targetPath.$namafile;
	            $ekstensi = array('jpg','pdf','png','jpeg', 'JPG', 'PNG', 'JPEG', 'PDF');

	            if (in_array($ekstensifile, $ekstensi)) {
	                if (move_uploaded_file($tempFile, $targetFile)) {
						$data['evidence'] = $namafile;
						$this->db->set($data)->insert('ess_penindakan');
						$id_penindakan = $this->db->insert_id();

						if($this->db->affected_rows() > 0) {
							$log_data_baru = "";
							foreach($data as $key => $value){
								if(!empty($log_data_baru)){
									$log_data_baru .= "<br>";
								}
								$log_data_baru .= "$key = $value";
							}
							
							$log = array(
								"id_pengguna" => $this->session->userdata("id_pengguna"),
								"id_modul" => $this->data['id_modul'],
								"id_target" => $id_penindakan,
								"deskripsi" => "tambah ".strtolower(preg_replace("/_/"," ",__CLASS__)),
								"kondisi_baru" => $log_data_baru,
								"alamat_ip" => $this->data["ip_address"],
								"waktu" => date("Y-m-d H:i:s")
							);
							$this->m_log->tambah($log);

							$this->session->set_flashdata('success', 'Berhasil mereset rekap keterlambatan');
						}
						else{
							$this->session->set_flashdata('warning', 'Reset data keterlambatan tidak berhasil');
						}
					}
					else{
						$this->session->set_flashdata('warning', 'Evidence gagal diupload. Koneksi bermasalah');
					}
				}
				else{
					$this->session->set_flashdata('warning', 'Evidence gagal diupload. Ekstensi file tidak dikenali');
				}
			}
			else{
				$this->session->set_flashdata('warning', 'Reset data keterlambatan tidak berhasil');
			}
			redirect(site_url('informasi/penindakan'));
		}
	}
	
	/* End of file data_keterlambatan.php */
	/* Location: ./application/controllers/informasi/data_pamlek.php */