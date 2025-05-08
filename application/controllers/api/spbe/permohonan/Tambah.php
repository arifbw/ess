<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Tambah extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        if(!in_array($this->id_group,[4,5])){
            $this->response([
                'status'=>false,
                'message'=>"Otoritas tidak diizinkan",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
        $this->load->helper("tanggal_helper");
    }
    
    function index_post(){
        $data_insert = [];
		$array_detail = ['np_karyawan','nama','nama_jabatan','kode_unit','nama_unit','milik','maksud','dikirim_ke','keluar_tanggal','approval_atasan_np','approval_atasan_nama','approval_atasan_jabatan','approval_atasan_kode_unit','approval_atasan_nama_unit','approval_atasan_nama_unit_singkat','barang_kembali'];
		foreach ($this->post() as $key => $value) {
			if(in_array($key,$array_detail)) $data_insert[$key] = trim($value);
		}

		$array_pos_keluar = json_decode($this->post('pos_keluar_id',true));
        if( $array_pos_keluar!=[] ){
            $integer_pos_keluar = array_map('intval', $array_pos_keluar);
            $pos_keluar_id = implode(',',$integer_pos_keluar);
            $data_insert['pos_keluar'] = $this->post('pos_keluar',true);
            $data_insert['pos_keluar_id'] = $pos_keluar_id;
        } else{
            $data_insert['pos_keluar'] = json_encode([]);
            $data_insert['pos_keluar_id'] = null;
        }

		$array_pos_masuk = json_decode($this->post('pos_masuk_id',true));
        if( $array_pos_masuk!=[] ){
            $integer_pos_masuk = array_map('intval', $array_pos_masuk);
            $pos_masuk_id = implode(',',$integer_pos_masuk);

			$barang_kembali = $this->post('barang_kembali');
			switch ($barang_kembali) {
				case '1':
					$data_insert['pos_masuk'] = $this->post('pos_masuk',true);
					$data_insert['pos_masuk_id'] = $pos_masuk_id;
					break;
				default:
					$data_insert['pos_masuk'] = 'null';
					$data_insert['pos_masuk_id'] = null;
					break;
			}
        } else{
            $data_insert['pos_masuk'] = json_encode([]);
            $data_insert['pos_masuk_id'] = null;
        }

        $barang = json_decode($this->post('barang'));
        $last_counter = $this->db->select('MAX(no_urut) AS counter')->where("DATE_FORMAT(created_at,'%Y')", date('Y'))->where("approval_atasan_kode_unit", $this->post('approval_atasan_kode_unit'))->get('ess_permohonan_spbe')->row_array()['counter'];
		$no_urut = ($last_counter!=null ? $last_counter+1 : 1);
		$nomor_surat = str_pad($no_urut, 6, "0", STR_PAD_LEFT) . '/' . $this->post('approval_atasan_nama_unit_singkat') . '/' . bulan_to_romawi(date('m')) . '/' . date('Y');
		$data_insert['nomor_surat'] = $nomor_surat;
		$data_insert['no_urut'] = $no_urut;
		$data_insert['created_at'] = date('Y-m-d H:i:s');
		$data_insert['created_by'] = $this->data_karyawan->np_karyawan;
		$data_insert['uuid'] = $this->uuid->v4();
		$data_insert['sumber_input'] = 'mobile';

        $pilih_pengawal = $this->post('pilih_pengawal');
		switch ($pilih_pengawal) {
			case '2':
				$data_insert['konfirmasi_pengguna_np'] = $this->post('konfirmasi_pengguna_np');
				$data_insert['konfirmasi_pengguna_nama'] = $this->post('konfirmasi_pengguna_nama');
				$data_insert['konfirmasi_pengguna_jabatan'] = $this->post('konfirmasi_pengguna_jabatan');
				$data_insert['nama_pembawa_barang'] = $this->post('konfirmasi_pengguna_nama');
				break;
			case '3':
				$data_insert['konfirmasi_pengguna_np'] = $this->post('konfirmasi_pengguna_np');
				$data_insert['konfirmasi_pengguna_nama'] = $this->post('konfirmasi_pengguna_nama');
				$data_insert['konfirmasi_pengguna_jabatan'] = $this->post('konfirmasi_pengguna_jabatan');
				$data_insert['nama_pembawa_barang'] = $this->post('nama_pembawa_barang');
				break;
			default:
				$data_insert['konfirmasi_pengguna_np'] = $this->post('np_karyawan');
				$data_insert['konfirmasi_pengguna_nama'] = $this->post('nama');
				$data_insert['konfirmasi_pengguna_jabatan'] = $this->post('nama_jabatan');
				$data_insert['nama_pembawa_barang'] = $this->post('nama');
				break;
		}

        $this->db->insert('ess_permohonan_spbe',$data_insert);

        if($this->db->affected_rows()>0){
			$new_id = $this->db->insert_id();
			$data_barang = [];
			foreach ($barang as $row) {
				if(trim($row->nama_barang)!=''){
					$value = (array) $row;
					$value['id'] = $this->uuid->v4();
					$value['ess_permohonan_spbe_id'] = $new_id;
					$value['created_at'] = date('Y-m-d H:i:s');
					$data_barang[] = $value;
				}
			}
			if( count($data_barang)>0 ) $this->db->insert_batch('ess_permohonan_spbe_barang',$data_barang);
			$this->db->where('id',$new_id)->update('ess_permohonan_spbe',['barang'=>json_encode($data_barang)]);
			$status = true;
			$message = 'Data telah ditambahkan';
		} else{
            $status = false;
			$message = 'Gagal menambahkan';
		}
        
        $this->response([
            'status'=>$status,
            'message'=>$message,
            'data'=>$data_insert
        ], MY_Controller::HTTP_OK);
    }
}
