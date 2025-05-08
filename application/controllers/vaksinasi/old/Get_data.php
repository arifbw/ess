<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Get_data extends CI_Controller {
	public function __construct(){
		parent::__construct();
	}

	function get_all_klinik(){
		$get = $this->db->select('*')
			->from('mst_klinik')
			->get()->result();
		echo json_encode([
			'message'=>'Data klinik',
			'data'=>$get
		]);
	}

	function data_penyintas(){
		$group = $this->input->post('group');
		$np = $this->input->post('np');
		$listPengadministrasi = $this->input->post('listPengadministrasi');

		$this->db->select("mst_karyawan.personnel_number as personal_number, mst_karyawan.no_pokok as np_karyawan, mst_karyawan.nama as nama_karyawan, mst_karyawan.tempat_lahir, 'Diri Sendiri' as tipe_keluarga, mst_karyawan.tempat_lahir as tempat_lahir_keluarga, mst_karyawan.tanggal_lahir, mst_karyawan.nama as nama_lengkap, NOW() as updated, 'Ditanggung' as status_tanggungan, (CASE WHEN mst_karyawan.jenis_kelamin='Laki-laki' THEN 'L' WHEN mst_karyawan.jenis_kelamin='Perempuan' THEN 'P' ELSE null END) as jenis_kelamin, ess_kesehatan_data_penyintas.id as id
				, data_vaksin_keluarga.uuid
				, data_vaksin_keluarga.nik
				, data_vaksin_keluarga.no_hp
				, data_vaksin_keluarga.email
				, data_vaksin_keluarga.status_kawin
				, data_vaksin_keluarga.alamat
				, data_vaksin_keluarga.status_vaksin
				, data_vaksin_keluarga.created_at
				, data_vaksin_keluarga.updated_at
				, data_vaksin_keluarga.mst_klinik_id
				, data_vaksin_keluarga.usia
				, data_vaksin_keluarga.tanggal_pcr_negatif
				, mst_klinik.kelurahan
				, mst_klinik.kecamatan
				, mst_klinik.kabupaten
				, mst_klinik.provinsi");
		switch ($group) {
			case "5":
				$this->db->where('mst_karyawan.no_pokok', $np);
			  	break;
			case "4":
				$this->db->where_in('kode_unit', $listPengadministrasi);
			  	break;
			default:
				$this->db->where('mst_karyawan.no_pokok is null', null, false);
		}
		$data = $this->db->from('mst_karyawan')
			->join('ess_kesehatan_data_penyintas', 'ess_kesehatan_data_penyintas.np_karyawan=mst_karyawan.no_pokok', 'LEFT')
			->join('data_vaksin_keluarga', 'data_vaksin_keluarga.np_karyawan=ess_kesehatan_data_penyintas.np_karyawan AND data_vaksin_keluarga.tipe_keluarga=ess_kesehatan_data_penyintas.tipe_keluarga', 'LEFT')
			->join('mst_klinik', 'mst_klinik.id=data_vaksin_keluarga.mst_klinik_id', 'LEFT')
			->get()
			->result();

		echo json_encode([
			'message'=>'Data penyintas',
			'lastRequest'=>date('Y-m-d H:i:s'),
			'data'=>$data
		]);
	}
}