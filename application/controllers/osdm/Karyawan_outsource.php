<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Karyawan_outsource extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'osdm/karyawan_outsource/';
		$this->folder_model = 'osdm/karyawan_outsource/';
		$this->folder_controller = 'osdm/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
					
		$this->load->model($this->folder_model."M_tabel_karyawan_outsource");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Karyawan Outsource";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index() {
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."index";
		$this->load->view('template',$this->data);
	}	
	
	public function tabel_karyawan_outsource(){	
		$list = $this->M_tabel_karyawan_outsource->get_datatables();
		$data = array();
		$no = $_POST['start'];
		
		foreach ($list as $tampil) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $tampil->np_karyawan;
			$row[] = $tampil->nama;
			$row[] = $tampil->nama_unit;
			$row[] = tanggal_indonesia($tampil->start_date);	
			$row[] = tanggal_indonesia($tampil->end_date);
			$row[] = $tampil->keterangan;
			if( $tampil->deleted_at!=null ){
				$row[] = '<small><i>Sudah dihapus pada '.datetime_indo($tampil->deleted_at).'</i></small>';
			} else{
				$row[] = "<button class='btn btn-default btn-xs ubah-data-karyawan' 
					data-np_karyawan='{$tampil->np_karyawan}' 
					data-nama='{$tampil->nama}' 
					data-kode_unit='{$tampil->kode_unit}' 
					data-nama_unit='{$tampil->nama_unit}' 
					data-nama_unit_sap='{$tampil->nama_unit_sap}' 
					data-start_date='{$tampil->start_date}' 
					data-end_date='{$tampil->end_date}' 
					data-keterangan='{$tampil->keterangan}' 
					data-spk='{$tampil->spk}' 
				>Edit</button>&nbsp;
				<button class='btn btn-danger btn-xs' 
					onclick='deleteItem(\"{$tampil->np_karyawan}\")'
				>Hapus</button>";
			}
			
			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_karyawan_outsource->count_all(),
			"recordsFiltered" => $this->M_tabel_karyawan_outsource->count_filtered(),
			"data" => $data,
		);
		
		echo json_encode($output);
	}

	function simpan(){
		$input=[];
		$response = [];
		foreach( $this->input->post() as $key=>$value){
			$input[$key] = (trim($value)!='' ? trim($value) : null);
		}

		$cekNp = $this->db->where('np_karyawan', $input['np_karyawan'])->get('ess_karyawan_outsource');
		if( $cekNp->num_rows() >0 ){
			$response['status'] = false;
			$response['message'] = '<div class="alert alert-danger alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										NP '.$input['np_karyawan'].' sudah pernah diinput. Silakan gunakan NP lain.
									</div>';
			// $response['data'] = $this->input->post();
		} else{
			$input['created_at'] = date('Y-m-d H:i:s');
			$input['created_by_np'] = $_SESSION['no_pokok'];
			$this->db->insert('ess_karyawan_outsource', $input);

			$response['status'] = true;
			$response['message'] = '<div class="alert alert-success alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										NP '.$input['np_karyawan'].' berhasil ditambahkan.
									</div>';
			// $response['data'] = $this->db->where('np_karyawan', $input['np_karyawan'])->get('ess_karyawan_outsource')->row();
		}
		echo json_encode($response);
	}

	function import_data(){
		include APPPATH.'third_party/phpexcel/PHPExcel.php';
		$config['upload_path'] = './uploads/outsourcing/';
		$config['allowed_types'] = 'xlsx|xls|csv';
		$config['max_size'] = '10000';
		$config['overwrite'] = true;
		$config['file_name'] = 'outsourcing-'.date('YmdHis');
		$this->load->library('upload');
		$this->upload->initialize($config);
		if(!$this->upload->do_upload('import_excel')){
			$flash = 'failed';
			$status = 400;
			$res = 'Terjadi Kesalahan';
		} else {
			$data_upload = $this->upload->data();
			$excelreader = new PHPExcel_Reader_Excel2007();
			$loadexcel = $excelreader->load($config['upload_path'].$data_upload['file_name']);
			$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);

			$data = array();

			$numrow = 1;
			$tgl_import = date('Y-m-d H:i:s');
			foreach($sheet as $row){
				if($numrow > 5 && $row['B']!='') {
					$cek = $this->db->where(array('np_karyawan'=>$row['B']))->get('ess_karyawan_outsource');

					if ($cek->num_rows() == 0) {
						$cek_tgl = 'created_at';
						$eck_np = 'created_by_np';
					} else{
						$cek_tgl = 'updated_at';
						$eck_np = 'updated_by_np';
					}

					$data = array(
						'np_karyawan' => $row['B'],
						'nama' => $row['C'],
						'np_lama' => $row['D']!='' ? $row['D']:null,
						'kode_unit' => $row['K'],
						'nama_unit' => $row['F'],
						'nama_unit_sap' => $row['M'],
						'keterangan' => $row['I'],
						'spk' => $row['J'],
						'start_date' => date('Y-m-d', strtotime($row['G'])),
						'end_date' => date('Y-m-d', strtotime($row['H'])),
						$cek_tgl => date('Y-m-d H:i:s'),
						$eck_np => $_SESSION['no_pokok']
					);

					if ($cek->num_rows() == 0) 
						$this->db->insert('ess_karyawan_outsource', $data);
					else
						$this->db->where(array('np_karyawan'=>$row['B']))->update('ess_karyawan_outsource', $data);
				}
				$numrow++;
			}
			$flash = 'success';
			$status = 200;
			$res = 'Berhasil Melakukan Import Data';
		}

		$this->session->set_flashdata($flash,$res);
		redirect('osdm/karyawan_outsource');
	}

	function hapus_by_np(){
		$response=[];
		$np = $this->input->post('np');
		$this->db->where('np_karyawan',$np)->update('ess_karyawan_outsource', [
			'deleted_at'=>date('Y-m-d H:i:s'),
			'deleted_by_np'=>$_SESSION['no_pokok']
		]);
		$response['status'] = true;
		$response['message'] = "Karyawan dengan NP {$np} telah dihapus";
		echo json_encode($response);
	}

	function update(){
		$input=[];
		$response = [];
		foreach( $this->input->post() as $attr=>$value){
			$key = str_replace('ubah_','',$attr);
			$input[$key] = (trim($value)!='' ? trim($value) : null);
		}
		$input['updated_at'] = date('Y-m-d H:i:s');
		$input['updated_by_np'] = $_SESSION['no_pokok'];

		$this->db->where('np_karyawan', $input['np_karyawan'])->update('ess_karyawan_outsource', $input);
		if( $this->db->affected_rows() > 0 ){
			$response['status'] = true;
			$response['message'] = '<div class="alert alert-success alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										Data karyawan dengan NP '.$input['np_karyawan'].' berhasil diupdate.
									</div>';
			// $response['data'] = $this->db->where('np_karyawan', $input['np_karyawan'])->get('ess_karyawan_outsource')->row();
		} else{
			$response['status'] = false;
			$response['message'] = '<div class="alert alert-danger alert-dismissable">
										<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
										Data karyawan dengan NP '.$input['np_karyawan'].' gagal diupdate.
									</div>';
			// $response['data'] = $this->input->post();
		}
		echo json_encode($response);
	}
}