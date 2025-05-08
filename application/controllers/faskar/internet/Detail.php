<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'faskar/internet/';
		$this->folder_model = 'faskar/internet/';
		$this->folder_controller = 'faskar/internet/';
		
		$this->akses = array();
		
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Internet";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}
	
	public function data($kode){
		$cek_kode = $this->db->where('kode', $kode)->get('ess_faskar_internet_header');
		if($cek_kode->num_rows()!=1){
			$this->session->set_flashdata('failed', 'Kode tidak valid');
			redirect('faskar/internet/header');
		} else{
			$header = $cek_kode->row();
			$bulan = [
				['id'=>'01', 'value'=>'Januari'],
				['id'=>'02', 'value'=>'Februari'],
				['id'=>'03', 'value'=>'Maret'],
				['id'=>'04', 'value'=>'April'],
				['id'=>'05', 'value'=>'Mei'],
				['id'=>'06', 'value'=>'Juni'],
				['id'=>'07', 'value'=>'Juli'],
				['id'=>'08', 'value'=>'Agustus'],
				['id'=>'09', 'value'=>'September'],
				['id'=>'10', 'value'=>'Oktober'],
				['id'=>'11', 'value'=>'November'],
				['id'=>'12', 'value'=>'Desember']
			];
			
			$this->data["akses"] = $this->akses;
			$this->data["navigasi_menu"] = menu_helper();
			$this->data['content'] = $this->folder_view."detail";
			$this->data['array_daftar_bulan'] = $bulan;
			$this->data['header'] = $header;
	
			$this->load->view('template',$this->data);
		}
	}	
	
	public function tabel_internet() {
		$params=[];
		$params['header_id'] = $_POST['header_id'];
		$this->load->model($this->folder_model."M_tabel_internet_detail");
		$list = $this->M_tabel_internet_detail->get_datatables($params);
		$data = array();
		$no = $_POST['start'];

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_internet_detail->count_all($params),
			"recordsFiltered" => $this->M_tabel_internet_detail->count_filtered($params),
			"data" => $list
		);
		echo json_encode($output);
	}

	function action_insert(){
		$data_insert = [];
		foreach($this->input->post() as $key=>$value){
			if(!in_array($key, ['header_kode','kode'])){
				$data_insert[$key] = $value;
			}
		}

		# jika edit ada atribute 'kode'
		if(@$this->input->post('kode')){
			$data_insert['updated_at'] = date('Y-m-d H:i:s');
			$data_insert['updated_by'] = $_SESSION['no_pokok'];
			$this->db->where('kode',$this->input->post('kode'))->update('ess_faskar_internet_detail', $data_insert);
			$this->session->set_flashdata('success',"Data pemakaian internet a.n <b>{$data_insert['nama_karyawan']}</b> telah diupdate");
		} else{ # ini untuk kondisi input => tidak ada atribute 'kode'
			$cek = $this->db->where([
				'np_karyawan'=>$data_insert['np_karyawan'],
				'no_hp'=>$data_insert['no_hp'],
				'faskar_internet_header_id'=>$data_insert['faskar_internet_header_id']
			])->get('ess_faskar_internet_detail');
			if($cek->num_rows()>0){
				$this->session->set_flashdata('failed','Data sudah pernah diinput');
			} else{
				$data_insert['kode'] = $this->uuid->v4();
				$data_insert['created_at'] = date('Y-m-d H:i:s');
				$data_insert['created_by'] = $_SESSION['no_pokok'];
				$this->db->insert('ess_faskar_internet_detail', $data_insert);
				$this->session->set_flashdata('success',"Data pemakaian internet a.n <b>{$data_insert['nama_karyawan']}</b> berhasil ditambahkan");
			}
		}

		redirect('faskar/internet/detail/data/'.$this->input->post('header_kode'));
	}

	function hapus(){
		$kode = $this->input->post('kode');
		$this->db->where('kode',$kode)->delete('ess_faskar_internet_detail');
		echo json_encode([
			'status'=>true,
			'message'=>'Data dihapus'
		]);
	}

	function action_import(){
		$faskar_internet_header_id = $this->input->post('faskar_internet_header_id');
		$header_kode = $this->input->post('header_kode');
		include APPPATH.'third_party/phpexcel/PHPExcel.php';
		$config['upload_path'] = './uploads/faskar/internet/';
		$config['allowed_types'] = 'xlsx|xls';
		$config['max_size'] = '10000';
		$config['overwrite'] = true;
		$config['file_name'] = 'upload-data-'.date('YmdHis');
		$this->load->library('upload');
		$this->upload->initialize($config);
		if(!$this->upload->do_upload('berkas')){
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
			$data_batch = [];
			foreach($sheet as $row){
				if($numrow > 7 && $row['B']!='') {
					$cek = $this->db->where(array('np_karyawan'=>$row['B'], 'no_hp'=>$row['D'], 'faskar_internet_header_id'=>$faskar_internet_header_id))->get('ess_faskar_internet_detail');
					$cek_in_batch = array_multi_search($data_batch, array('np_karyawan' => $row['B'], 'no_hp' => $row['D']));
					if ($cek->num_rows() == 0 && count($cek_in_batch)==0) { # hanya insert yg belum ada datanya
						$pemakaian = $row['E']!='' ? $row['E']:0;
						$data = array(
							'kode' => $this->uuid->v4(),
							'faskar_internet_header_id' => $faskar_internet_header_id,
							'np_karyawan' => $row['B'],
							// 'nama_karyawan' => $nama_karyawan,
							'no_hp' => $row['D'],
							'pemakaian' => $pemakaian,
							'keterangan' => $row['F'],
							// 'plafon' => $plafon,
							// 'beban_pegawai' => $beban_pegawai,
							// 'beban_perusahaan' => $beban_perusahaan,
							'created_at' => date('Y-m-d H:i:s'),
							'created_by' => $_SESSION['no_pokok']
						);

						# cek ke plafon
						$cek_plafon = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$row['B'])->where('no_hp',$row['D'])->get('mst_plafon_internet');
						# cek ke mst karyawan
						$cek_mst = $this->db->select('no_pokok as np_karyawan, nama as nama_karyawan, nama_jabatan')->where('no_pokok', $row['B'])->get('mst_karyawan');

						if( $cek_plafon->num_rows()==1 ){
							$row_plafon = $cek_plafon->row();
							$data['nama_karyawan'] = $row_plafon->nama_karyawan;
							$data['plafon'] = $row_plafon->plafon;
							if( $row_plafon->ket=='at cost'){
								$data['beban_pegawai'] = 0;
								$data['beban_perusahaan'] = $pemakaian;
							} else{
								$plafon = $row_plafon->plafon;
								if( $pemakaian >= $plafon ){
									$data['beban_pegawai'] = $pemakaian - $plafon;
									$data['beban_perusahaan'] = $plafon;
								} else{
									$data['beban_pegawai'] = 0;
									$data['beban_perusahaan'] = $pemakaian;
								}
							}
							$data_batch[] = $data;
						} else if($cek_mst->num_rows()==1){
							$row_mst = $cek_mst->row();
							$data['nama_karyawan'] = $row_mst->nama_karyawan;
							$data['plafon'] = 0;
							$data['beban_pegawai'] = $pemakaian;
							$data['beban_perusahaan'] = 0;
							$data_batch[] = $data;
						}
					}
				}
				$numrow++;
			}

			if($data_batch!=[]) $this->db->insert_batch('ess_faskar_internet_detail', $data_batch);
			$flash = 'success';
			$status = 200;
			$res = 'Berhasil Melakukan Import Data';

			unlink($config['upload_path'].$data_upload['file_name']);
		}

		$this->session->set_flashdata($flash,$res);
		redirect('faskar/internet/detail/data/'.$header_kode);
	}

	private function cek_np($np_karyawan) {
		$start_date			= date('Y-m-d');
        $tahun_bulan     	= str_replace('-','_',substr("$start_date", 0, 7));
		$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
		$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
		if ($nama_karyawan=='' || $nama_karyawan==null) {
			$start_date			= date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );
	        $tahun_bulan     	= str_replace('-','_',substr("$start_date", 0, 7));
			$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
			$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
			if ($nama_karyawan=='' || $nama_karyawan==null) {
				$start_date			= date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-2 month" ) );
		        $tahun_bulan     	= str_replace('-','_',substr("$start_date", 0, 7));
				$nama_karyawan 		= erp_master_data_by_np($np_karyawan, $start_date)['nama'];
				$nama_jabatan		= erp_master_data_by_np($np_karyawan, $start_date)['nama_jabatan'];
			}
		}

		$data['nama_karyawan'] = $nama_karyawan;
		$data['nama_jabatan'] = $nama_jabatan;

        return $data;
	}

	function ajax_getNama($np_karyawan) {
		$data = $this->cek_np($np_karyawan);

		$return = [
            'status'=>true,
            'data'=>[
                'nama'=>$data['nama_karyawan'],
                'jabatan'=>$data['nama_jabatan']
            ]
        ];

        echo json_encode($return);
	}

}