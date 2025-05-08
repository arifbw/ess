<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	Class Pembayaran_hutang_cuti extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'osdm/';
			$this->folder_model = 'osdm/';
			$this->folder_ajax_view = $this->folder_view.'ajax/';
			$this->akses = array();
			
			$this->load->model($this->folder_model."m_pembayaran_hutang_cuti");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index(){
			$this->data['judul'] = "Pembayaran Hutang Cuti";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."pembayaran_hutang_cuti";
			
			array_push($this->data['js_sources'],"osdm/pembayaran_hutang_cuti");
			
			if($this->input->post()){
				/* if($this->akses["konversi"]){
					if(!empty($this->input->post("id_pembayaran_cuti_besar"))){
						$id_pembayaran_cuti_besar = $this->input->post("id_pembayaran_cuti_besar");
						$no_pokok = $this->input->post("no_pokok");
						$tahun = $this->input->post("tahun");
						$konversi_bulan = 0;
						$konversi_hari = 0;
						
						if(!empty($this->input->post("konversi_dari_bulan")) and !empty($this->input->post("konversi_jadi_hari"))){
							$konversi_bulan = $this->input->post("konversi_dari_bulan");
							$konversi_hari = $this->input->post("konversi_jadi_hari");
						}
						else if(!empty($this->input->post("konversi_dari_hari")) and !empty($this->input->post("konversi_jadi_bulan"))){
							$konversi_hari = -1*(int)$this->input->post("konversi_dari_hari");
							$konversi_bulan = -1*(int)$this->input->post("konversi_jadi_bulan");
						}

						if($konversi_bulan!=0 and $konversi_hari!=0){
							$this->konversi($id_pembayaran_cuti_besar,$no_pokok,$tahun,$konversi_bulan,$konversi_hari);							
						}
					}
				} */
			}
			
			if($this->akses["lihat"]){
				$js_header_script = "<script>
								$(document).ready(function() {									
									ambil_data();
								});
							</script>";
				
				array_push($this->data["js_header_script"],$js_header_script);
				
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
		
		public function tabel_pembayaran_hutang_cuti(){
			$this->data['judul'] = "Pembayaran Hutang Cuti";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$list = $this->m_pembayaran_hutang_cuti->get_datatables();
			$data = array();
			foreach ($list as $tampil) {
				$row = array();
				$row[] = $tampil->no_pokok;
				$row[] = $tampil->nama;
				$row[] = $tampil->hutang." hari";
				
				$kolom_aksi = "";
				
				if($this->akses["tambah"] or $this->akses["ubah"]){
					if(!empty($kolom_aksi)){
						$kolom_aksi .= " ";
					}
					$kolom_aksi .= "<button class='btn btn-warning btn-xs' data-toggle='modal' data-target='#modal_bayar' onclick='tampil_bayar(this)'>Bayar</button>";
				}
				
				if($this->akses["lihat log"]){
					if(!empty($kolom_aksi)){
						$kolom_aksi .= " ";
					}
					$kolom_aksi .= "<button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$tampil->nama."\",".$tampil->id.")'>Lihat Log</button>";
				}
				$row[] = $kolom_aksi;

				$data[] = $row;
			}

			$output = array(
							"draw" => $_POST['draw'],
							"recordsTotal" => $this->m_pembayaran_hutang_cuti->count_all(),
							"recordsFiltered" => $this->m_pembayaran_hutang_cuti->count_filtered(),
							"data" => $data,
					);
			//output to json format
			echo json_encode($output);
		}
	}
	
	/* End of file Pembayaran_hutang_cuti.php */
	/* Location: ./application/controllers/osdm/Pembayaran_hutang_cuti.php */