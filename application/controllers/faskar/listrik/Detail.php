<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Detail extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'faskar/listrik/';
		$this->folder_model = 'faskar/listrik/';
		$this->folder_controller = 'faskar/listrik/';
		
		$this->akses = array();
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Listrik dari PLN";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}
	
	public function data($kode){
		$cek_kode = $this->db->where('kode', $kode)->get('ess_faskar_listrik_header');
		if($cek_kode->num_rows()!=1){
			$this->session->set_flashdata('failed', 'Kode tidak valid');
			redirect('faskar/listrik/header');
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
	
	public function tabel_listrik() {
		$params=[];
		$params['header_id'] = $_POST['header_id'];
		$this->load->model($this->folder_model."M_tabel_listrik_detail");
		$list = $this->M_tabel_listrik_detail->get_datatables($params);
		$data = array();
		$no = $_POST['start'];

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_tabel_listrik_detail->count_all($params),
			"recordsFiltered" => $this->M_tabel_listrik_detail->count_filtered($params),
			"data" => $list
		);
		echo json_encode($output);
	}

	function action_insert(){
		$data_insert = [];
		foreach($this->input->post() as $key=>$value){
			if(!in_array($key, ['header_kode','pemakaian','kode'])){
				$data_insert[$key] = $value;
			}
		}

		# jika edit ada atribute 'kode'
		if(@$this->input->post('kode')){
			$data_insert['updated_at'] = date('Y-m-d H:i:s');
			$data_insert['updated_by'] = $_SESSION['no_pokok'];
			$this->db->where('kode',$this->input->post('kode'))->update('ess_faskar_listrik_detail', $data_insert);
			$this->session->set_flashdata('success',"Data pemakaian listrik a.n <b>{$data_insert['nama_karyawan']}</b> telah diupdate");
		} else{ # ini untuk kondisi input => tidak ada atribute 'kode'
			$cek = $this->db->where([
				'np_karyawan'=>$data_insert['np_karyawan'],
				'no_kontrol'=>$data_insert['no_kontrol'],
				'faskar_listrik_header_id'=>$data_insert['faskar_listrik_header_id']
			])->get('ess_faskar_listrik_detail');
			if($cek->num_rows()>0){
				$this->session->set_flashdata('failed','Data sudah pernah diinput');
			} else{
				$data_insert['kode'] = $this->uuid->v4();
				$data_insert['created_at'] = date('Y-m-d H:i:s');
				$data_insert['created_by'] = $_SESSION['no_pokok'];
				$this->db->insert('ess_faskar_listrik_detail', $data_insert);
				$this->session->set_flashdata('success',"Data pemakaian listrik a.n <b>{$data_insert['nama_karyawan']}</b> berhasil ditambahkan");
			}
		}

		redirect('faskar/listrik/detail/data/'.$this->input->post('header_kode'));
	}

	function hapus(){
		$kode = $this->input->post('kode');
		$this->db->where('kode',$kode)->delete('ess_faskar_listrik_detail');
		echo json_encode([
			'status'=>true,
			'message'=>'Data dihapus'
		]);
	}

	function action_import(){
		$faskar_listrik_header_id = $this->input->post('faskar_listrik_header_id');
		$header_kode = $this->input->post('header_kode');
		include APPPATH.'third_party/phpexcel/PHPExcel.php';
		$config['upload_path'] = './uploads/faskar/listrik/';
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
					$cek = $this->db->where(array('np_karyawan'=>$row['B'], 'no_kontrol'=>$row['E'], 'faskar_listrik_header_id'=>$faskar_listrik_header_id))->get('ess_faskar_listrik_detail');
					// $cek_in_batch = array_search($row['B'], array_column($data_batch, 'np_karyawan'));
					$cek_in_batch = array_multi_search($data_batch, array('np_karyawan' => $row['B'], 'no_kontrol' => $row['E']));
					if ($cek->num_rows() == 0 && count($cek_in_batch)==0) { # hanya insert yg belum ada datanya

						$tagihan = $row['F']!='' ? $row['F']:0;
						$biaya_admin = $row['G']!='' ? $row['G']:0;
						$pemakaian = $tagihan + $biaya_admin;
						$data = array(
							'kode' => $this->uuid->v4(),
							'faskar_listrik_header_id' => $faskar_listrik_header_id,
							'np_karyawan' => $row['B'],
							// 'nama_karyawan' => $nama_karyawan, // $row['C'],
							// 'alamat' => $alamat, // $row['D'],
							'no_kontrol' => $row['E'],
							'tagihan' => $tagihan,
							'biaya_admin' => $biaya_admin,
							// 'plafon' => $plafon,
							// 'beban_pegawai' => $beban_pegawai,
							// 'beban_perusahaan' => $beban_perusahaan,
							'keterangan' => $row['L'],
							'created_at' => date('Y-m-d H:i:s'),
							'created_by' => $_SESSION['no_pokok']
						);

						# cek ke plafon
						$cek_plafon = $this->db->where('deleted_at IS NULL',null,false)->where('np_karyawan',$row['B'])->where('no_kontrol',$row['E'])->get('mst_plafon_listrik');
						# cek ke mst karyawan
						$cek_mst = $this->db->select('no_pokok as np_karyawan, nama as nama_karyawan, nama_jabatan')->where('no_pokok', $row['B'])->get('mst_karyawan');

						if( $cek_plafon->num_rows()==1 ){
							$row_plafon = $cek_plafon->row();
							$data['nama_karyawan'] = $row_plafon->nama_karyawan;
							$data['alamat'] = $row_plafon->alamat;
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
							$data['alamat'] = null;
							$data['plafon'] = 0;
							$data['beban_pegawai'] = $pemakaian;
							$data['beban_perusahaan'] = 0;
							$data_batch[] = $data;
						}
					}
				}
				$numrow++;
			}

			if($data_batch!=[]) $this->db->insert_batch('ess_faskar_listrik_detail', $data_batch);
			$flash = 'success';
			$status = 200;
			$res = 'Berhasil Melakukan Import Data';

			unlink($config['upload_path'].$data_upload['file_name']);
		}

		$this->session->set_flashdata($flash,$res);
		redirect('faskar/listrik/detail/data/'.$header_kode);
	}
}