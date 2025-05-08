
<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Transaksi_cuti_bersama extends CI_Controller {
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

			$this->load->helper("karyawan_helper");
			$this->load->helper("tanggal_helper");

			$this->load->model("lembur/m_pengajuan_lembur");
			$this->load->model("lembur/m_tabel_pengajuan_lembur");
			//$this->load->model($this->folder_model."M_tabel_mst_karyawan");
			$this->load->model("master_data/m_karyawan");
			$this->load->model("master_data/m_cuti_bersama");
			$this->load->library("pdf");

			$this->data['success'] = "";
			$this->data['warning'] = "";
			
			$this->data["is_with_sidebar"] = true;
		}
		
		public function index() {				
			$this->data['judul'] = "Transaksi Cuti Bersama";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);
			
			izin($this->akses["akses"]);
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."transaksi_cuti_bersama";
			array_push($this->data['js_sources'],"osdm/pengajuan_lembur");
			$this->data["bulan"] = date('Y-m');
			$this->data["month_list"] = $this->db->query('select distinct DATE_FORMAT(tgl_dws, "%Y-%m") as bln from ess_lembur_transaksi')->result_array();
			$this->data["daftar_cuti"] = $this->m_cuti_bersama->daftar_cuti_bersama_tahun(date('Y'));
			$this->data["daftar_tahun"] = $this->m_cuti_bersama->ambil_tahun();
			$this->load->view('template', $this->data);
		}

		public function header_ess_cuti($tahun){
			$daftar_cuti = $this->m_cuti_bersama->daftar_cuti_bersama_tahun($tahun);

			$data = array();
			$i = 2;
			$data['html'] = '';
			$data['data'][] = array('data' => 0);
			$data['html'] .= '<th class="text-center" style="width: 50px;">No</th>';
			$data['data'][] = array('data' => 1);
			$data['html'] .= '<th class="text-center" style="width: 240px;">Karyawan</th>';
			foreach($daftar_cuti as $val_cuti){
				$data['data'][] = array('data' => $i);
				$data['html'] .= '<th class="text-center"><span title="'.$val_cuti['deskripsi'].'">'.date('d/m/Y', strtotime($val_cuti['tanggal'])).'</span></th>';
				$i++;
			}

			echo json_encode($data);
		}

		public function action_cuti_bersama(){
			if($this->input->is_ajax_request()){
				header('Content-Type: application/json');

				$np = $this->input->post('np_karyawan', true);
				$tgl = $this->input->post('tgl', true);
				$cuti = $this->input->post('cuti', true);

				$cek_cuti = $this->db->select('*')->where('np_karyawan', $np)->where('tanggal_cuti_bersama', $tgl)->get('ess_cuti_bersama');
				if($cek_cuti->num_rows() > 0){
					$row = $cek_cuti->row_array();
					$data = array(
							'id' => $row['id'],
							'enum' => $cuti,
							'updated_by' => $this->session->userdata('no_pokok'),
							'updated_at' => date('Y-m-d H:i:s')
						);

					$this->db->update('ess_cuti_bersama', $data, array('id' => $data['id']));
					if($this->db->affected_rows() > 0){
						echo json_encode(array('success' => true, 'message' => 'Berhasil menyimpan data cuti karyawan NP: '.$np.' pada Tanggal: '.$tgl));
					}
					else{
						echo json_encode(array('success' => false, 'message' => 'Gagal menyimpan data cuti karyawan NP: '.$np.' pada Tanggal: '.$tgl));
					}
				}
				else{
					$data = array(
							'id' => '',
							'np_karyawan' => $np,
							'tanggal_cuti_bersama' => $tgl,
							'enum' => $cuti,
							'updated_by' => $this->session->userdata('no_pokok'),
							'updated_at' => date('Y-m-d H:i:s')
						);

					$this->db->insert('ess_cuti_bersama', $data);
					if($this->db->affected_rows() > 0){
						echo json_encode(array('success' => true, 'message' => 'Berhasil menyimpan data cuti karyawan NP: '.$np.' pada Tanggal: '.$tgl));
					}
					else{
						echo json_encode(array('success' => false, 'message' => 'Gagal menyimpan data cuti karyawan NP: '.$np.' pada Tanggal: '.$tgl));
					}
				}
			}
		}
	/*	SALAH
		public function tabel_ess_cuti($tahun) {
			$tgl = $this->input->post('bln');
			$this->data['judul'] = "Cuti Bersama";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			$list = $this->M_tabel_mst_karyawan->get_datatables();	
			$cuti = $this->m_cuti_bersama->daftar_cuti_bersama_tahun($tahun);	
			$data = array();
			$no = $_POST['start'];
			$opsi_cuti = array('Pilih Cuti', 'Cuti Besar', 'Cuti Tahunan', 'Hutang Cuti');
			
			$i = 0;
			foreach ($list as $val) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $val->no_pokok.' - '.$val->nama;
				foreach ($cuti as $val_cuti) {
					$cuti_data_bersama = $this->db->select('enum')->where('np_karyawan', $val->no_pokok)->where('tanggal_cuti_bersama', $val_cuti['tanggal'])->get('ess_cuti_bersama')->row_array();
					// $row[] = date('d/m/Y', strtotime($val_cuti['tanggal']));
					$cuti_opsi = '';
					foreach($opsi_cuti as $opsi_cuti_key => $opsi_cuti_val){
						$cuti_opsi .= '<option value="'.$opsi_cuti_key.'"'.(($cuti_data_bersama['enum'] == $opsi_cuti_key)?' selected=""':'').'>'.$opsi_cuti_val.'</option>';
					}
					$row[] = '<select class="form-control" onchange="change_data_cuti(this.value, \''.$val->no_pokok.'\',\''.$val_cuti['tanggal'].'\')">'.$cuti_opsi.'</select>';
				}

				$data[] = $row;
				$i++;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_mst_karyawan->count_all($tgl),
					"recordsFiltered" => $this->M_tabel_mst_karyawan->count_filtered($tgl),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}
	*/	
		
		public function tabel_ess_cuti($tahun) {
			$tgl = $this->input->post('bln');
			$this->data['judul'] = "Cuti Bersama";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);

			$list = $this->M_tabel_mst_karyawan->get_datatables();	
			$cuti = $this->m_cuti_bersama->daftar_cuti_bersama_tahun($tahun);	
			$data = array();
			$no = $_POST['start'];
			$opsi_cuti = array('Pilih Cuti', 'Cuti Besar', 'Cuti Tahunan', 'Hutang Cutix');
			
			$i = 0;
			foreach ($list as $val) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $val->no_pokok.' - '.$val->nama;
				foreach ($cuti as $val_cuti) {
					$cuti_data_bersama = $this->db->select('enum')->where('np_karyawan', $val->no_pokok)->where('tanggal_cuti_bersama', $val_cuti['tanggal'])->get('ess_cuti_bersama')->row_array();
					// $row[] = date('d/m/Y', strtotime($val_cuti['tanggal']));
					$cuti_opsi = '';
					foreach($opsi_cuti as $opsi_cuti_key => $opsi_cuti_val){
						$cuti_opsi .= '<option value="'.$opsi_cuti_key.'"'.(($cuti_data_bersama['enum'] == $opsi_cuti_key)?' selected=""':'').'>'.$opsi_cuti_val.'</option>';
					}
					$row[] = '<select class="form-control" onchange="change_data_cuti(this.value, \''.$val->no_pokok.'\',\''.$val_cuti['tanggal'].'\')">'.$cuti_opsi.'</select>';
				}

				$data[] = $row;
				$i++;
			}

			$output = array(
					"draw" => $_POST['draw'],
					"recordsTotal" => $this->M_tabel_mst_karyawan->count_all($tgl),
					"recordsFiltered" => $this->M_tabel_mst_karyawan->count_filtered($tgl),
					"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}
		
	}
	