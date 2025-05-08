<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );

class Perencanaan_jadwal_kerja extends Group_Controller {
	public function __construct(){
		parent::__construct();
		$this->load->helper("cutoff_helper");
		$this->load->helper("karyawan_helper");
		$this->load->model("kehadiran/M_perencanaan_jadwal_kerja");
	}
	
	function index_get(){
		$data = [];
		if(@$this->get('bulan')){
			$bulan = date('m', strtotime($this->get('bulan')));
			$tahun = date('Y', strtotime($this->get('bulan')));
		} else{
			$bulan = date('m');
			$tahun = date('Y');
		}

		if(@$this->get('dws_code')){
			$dws_code = $this->get('dws_code');
		} else{
			$dws_code = '';
		}

		if(@$this->get('dws_variant')){
			$dws_variant = $this->get('dws_variant');
		} else{
			$dws_variant = '';
		}

		$this->db->select('ess_substitution.id, ess_substitution.np_karyawan, ess_substitution.nama, ess_substitution.date, ess_substitution.dws, ess_substitution.dws_variant, ess_substitution.transaction_type, mst_jadwal_kerja.description as dws_name')
			->where('MONTH(date)', $bulan)
			->where('YEAR(ess_substitution.date)', $tahun)
			->where('ess_substitution.deleted', '0')
			->join('mst_jadwal_kerja', 'ess_substitution.dws=mst_jadwal_kerja.dws','LEFT');
		if( $this->id_group=='5' ){ # pengguna
			$this->db->where('ess_substitution.np_karyawan', $this->data_karyawan->np_karyawan);
		} else if( $this->id_group=='4' ){ # pengadministrasi
			$list_pengadministrasi = array_column($this->list_pengadministrasi, 'kode_unit');
			$this->db->where_in('ess_substitution.kode_unit', $list_pengadministrasi);
		} else if( $this->id_group=='1' ){ # superadmin
			# gak ada filter
		} else{
			$this->db->where('ess_substitution.id', null);
		}

		$this->db->where('ess_substitution.dws_variant', $dws_variant);
		if( $dws_code!='' )
			$this->db->where('ess_substitution.dws', $dws_code);

		$get = $this->db->get('ess_substitution')->result_array();

		foreach($get as $field){
			$row = $field;
			$dws_variant = ($field['dws_variant']=='A' ? 'Jumat':'');
			$row['sudah_cutoff'] = sudah_cutoff($field['date']);
			$data[] = $row;
		}

		$this->response([
			'status'=>true,
			'message'=>'Success',
			'data'=>$data
		], MY_Controller::HTTP_OK);
	}

	function index_post(){
		$this->response([
			'status'=>true,
			'message'=>'Under development...',
			'data'=>$this->post()
		], MY_Controller::HTTP_OK);
		exit;

		$np_karyawan = $this->post('list_np');
		$date = date('Y-m-d',strtotime($this->post('date_awal')));
		$date_akhir = date('Y-m-d',strtotime($this->post('date_akhir')));
		$dws = $this->post('data_dws')['dws'];
		$dws_variant = $this->post('data_dws')['dws_variant'];
		
		if( $date_akhir < $date ){
			$this->response([
				'status'=>true,
				'message'=>'Tanggal akhir tidak valid',
				'data'=>[]
			], MY_Controller::HTTP_BAD_REQUEST);
		}
		
		if( count($np_karyawan)==0 ){
			$this->response([
				'status'=>true,
				'message'=>'Karyawan harus diisi',
				'data'=>[]
			], MY_Controller::HTTP_BAD_REQUEST);
		}

		$error_exist 	= '';
		$error 			= '';
		$success 		= '';
		
		while (strtotime($date) <= strtotime($date_akhir)) 
		{
			for($i=0; $i<count($np_karyawan); $i++){
				$data_insert = [];
				//validasi data sudah ada
				$data_exist = [
					'np_karyawan'=>$np_karyawan[$i]['np_karyawan'],
					'date'=>$date
				];

				$exist = $this->M_perencanaan_jadwal_kerja->check_substitution_exist($data_exist);

				//check data di erp master data / mst_karyawan
				$erp =  erp_master_data_by_np($np_karyawan[$i]['np_karyawan'], $date);

				if($exist!=0 || $erp==null){
					if($erp==null) {
						$error_exist = $error_exist."<b>Gagal validasi</b>, Pengajuan Perencanaan Kerja <b>".$np_karyawan[$i]['np_karyawan']. "|" .$erp['nama']."</b> pada <b>$date</b> belum terdapat di master data karyawan.<br>";	
					} else {
						$error_exist = $error_exist."<b>Gagal validasi</b>, Pengajuan Perencanaan Kerja <b>".$np_karyawan[$i]['np_karyawan']. "|" .$erp['nama']."</b> pada <b>$date</b> sudah tersedia.<br>";
					}
				} else {					
					# insert
					$data_insert['np_karyawan'] 	= $np_karyawan[$i]['np_karyawan'];
					$data_insert['personel_number']	= $np_karyawan[$i]['personel_number'];
					$data_insert['nama'] 			= $np_karyawan[$i]['nama'];
					$data_insert['nama_jabatan'] 	= $np_karyawan[$i]['nama_jabatan'];
					$data_insert['kode_unit'] 		= $np_karyawan[$i]['kode_unit'];
					$data_insert['nama_unit'] 		= $np_karyawan[$i]['nama_unit'];
					$data_insert['date'] 			= $date;
					$data_insert['dws'] 			= $dws;
					$data_insert['dws_variant'] 	= $dws_variant;
					$data_insert['transaction_type']= '1';	//perencanaan	
					$data_insert['created_at']		= date('Y-m-d H:i:s');		
					$data_insert['created_by']		= $this->data_karyawan->np_karyawan;
					
					$insert = $this->db->insert('ess_substitution', $data_insert);

					if($this->db->affected_rows()==0) {
						$error = $error."<b>Gagal input</b>, Pengajuan Perencanaan Kerja <b>".$np_karyawan[$i]['np_karyawan'] ."|". $erp['nama']."</b> pada <b>$date</b> gagal masuk database.<br>";
					} else {
						$success = $success."<b>Berhasil input</b>, data Perencanaan Jadwal Kerja <b>".$np_karyawan[$i]['np_karyawan'] ."|". $erp['nama']."</b> pada <b>$date</b> telah masuk database.<br>";

						# update substitution di table cico


						# refresh ess cico berdasarkan dws yang baru
						// $this->refresh_ess_cico_by_np_date($np_karyawan[$i]['np_karyawan'],$date);
					}
				}
			}
			
			$date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
		}
	}
}