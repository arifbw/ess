<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class M_mycontribution extends CI_Model
{

	private $table = "manajemen_poin";
	public function __construct()
	{
		parent::__construct();
		$this->table_schema = $this->db->database;
	}

	function get_tabel_perizinan_from_schema()
	{
		return $this->db->select('TABLE_NAME')->where('TABLE_SCHEMA', $this->table_schema)->like('TABLE_NAME', 'ess_perizinan_', 'after')->group_by('TABLE_NAME')->order_by('TABLE_NAME', 'DESC')->get('information_schema.TABLES');
	}

	function get_mst_perizinan()
	{
		$not_in = ['AB', 'ATU', 'TK', 'TM'];
		return $this->db->select('nama, kode_pamlek, kode_erp')->where('status', '1')->where_not_in('kode_pamlek', $not_in)->order_by('kode_pamlek')->get('mst_perizinan');
	}

	function get_mst_pos()
	{
		return $this->db->select('id, nama, kode_pos')->where('status', '1')->order_by('kode_pos')->get('mst_pos');
	}

	function select_np_by_kode_unit($list_kode_unit)
	{
		$this->db->select('*');
		$this->db->from('mst_karyawan');
		$this->db->where_in('kode_unit', $list_kode_unit);
		$this->db->order_by('no_pokok', 'ASC');
		$data = $this->db->get();

		return $data;
	}

	function insert_error($data)
	{
		return  $this->db->insert('ess_error', $data);
	}

	function setting()
	{
		$this->db->select('*');
		$this->db->from('ess_sppd_setting');
		$query = $this->db->get();

		return $query->row_array();
	}

	public function select_pamlek_files()
	{
		$this->db->select('*');
		$this->db->from('pamlek_files');
		$this->db->order_by("nama_file", "ASC");

		$query = $this->db->get();
		return $query;
	}

	public function select_pamlek_files_limit($max)
	{
		$this->db->select('*');
		$this->db->from('ess_sppd_files');
		$this->db->where("proses", "0");
		$this->db->limit($max);
		$this->db->order_by("nama_file", "ASC");

		$query = $this->db->get();
		return $query;
	}

	function insert_files($data)
	{
		return  $this->db->insert('pamlek_files', $data);
	}

	function cek_id_then_insert_data($id, $data)
	{
		$cek = $this->db->where('id_sppd', $id)->get('ess_sppd')->num_rows();
		if ($cek < 1) {
			$this->db->insert('ess_sppd', $data);
		}
	}

	function insert_data_batch($data)
	{
		$this->db->insert_batch('ess_sppd', $data);
	}

	function update_files($nama_file, $data)
	{
		$this->db->where('nama_file', $nama_file);
		$this->db->update('ess_sppd_files', $data);
	}

	function select_distinc_tapping_time_pamlek_data()
	{
		$this->db->distinct("tapping_time");
		$this->db->from('pamlek_data');

		$query = $this->db->get();
		return $query;
	}

	function create_table_data($name)
	{
		$this->db->query("CREATE TABLE $name AS SELECT * FROM pamlek_data");
	}

	function truncate_table($name)
	{
		$this->db->from($name);
		$this->db->truncate();
	}

	function alter_table($name)
	{
		$this->db->query("ALTER TABLE $name MODIFY id INT AUTO_INCREMENT PRIMARY KEY");
	}

	function copy_isi($name, $tahun_bulan)
	{
		$this->db->query("INSERT INTO $name 
		(no_pokok_convert, no_pokok_original, no_pokok, tapping_time, in_out, machine_id, tapping_type, file) 
		SELECT no_pokok_convert, no_pokok_original, no_pokok, tapping_time, in_out, machine_id, tapping_type, file FROM pamlek_data 
		WHERE tapping_time like '$tahun_bulan%'");
	}

	function check_table_exist($name)
	{
		$query = $this->db->query("show tables like '$name'")->row_array();

		return $query;
	}

	function insert_ess_tabel_cico($data)
	{
		return  $this->db->insert('ess_tabel_cico', $data);
	}

	function update_ess_tabel_cico($nama_tabel, $data)
	{
		$this->db->where('nama_tabel', $nama_tabel);
		$this->db->update('ess_tabel_cico', $data);
	}

	public function check_ess_tabel_cico_exist($nama_tabel)
	{
		$this->db->select('*');
		$this->db->from('ess_tabel_cico');
		$this->db->where('nama_tabel', $nama_tabel);

		$query 	= $this->db->get();

		return $query->row_array();
	}

	function select_daftar_karyawan()
	{
		if ($_SESSION["grup"] == 4) //jika Pengadministrasi Unit Kerja
		{
			$ada_data = 0;
			$var = array();
			$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
			foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
			{
				array_push($var, $data['kode_unit']);
				$ada_data = 1;
			}
			if ($ada_data == 0) {
				$var = '';
			}
		} else
		if ($_SESSION["grup"] == 5) //jika Pengguna
		{
			$var 	= $_SESSION["no_pokok"];
		} else {
			$var = '';
		}

		$this->db->select('*');
		$this->db->from('mst_karyawan');

		if ($_SESSION["grup"] == 4) //jika Pengadministrasi Unit Kerja
		{
			$this->db->where_in('mst_karyawan.kode_unit', $var);
		} else
		if ($_SESSION["grup"] == 5) //jika Pengguna
		{
			$this->db->where_in('mst_karyawan.no_pokok', $var);
		} else {
		}

		$data = $this->db->get();

		return $data;
	}

	public function ambil_by_id($id, $table)
	{
		$data = $this->db->from($table)
			->where("id", $id)
			->get()
			->row_array();
		return $data;
	}

	public function add_poin($poin, $data)
	{
		$np_decode = $data['np_karyawan'];

		$karyawan = $this->db->where('no_pokok', $np_decode)->get('mst_karyawan')->row();

		if (empty($karyawan)) {
			$return["message"] = "Karyawan dengan no pokok $np_decode tidak ditemukan";
			return $return;
		}

		$data_insert = [
			'tipe' => 'Debit',
			'poin' => $poin,
			'sumber' => 'My Contribution',
			'created_at' => date('Y-m-d H:i:s'),
			'mycontribution_id' => $data['id'],
			'created_by_np' => $karyawan->no_pokok,
			'created_by_nama' => $karyawan->nama,
			'created_by_kode_unit' => $karyawan->kode_unit,
		];

		$poin_sekarang = $this->poin_sekarang($data_insert['created_by_np']);
		$data_insert['poin_awal'] = $poin_sekarang;
		$data_insert['poin_hasil'] = $poin_sekarang + (int)$data_insert['poin'];
		$this->db->insert("log_poin", $data_insert);
		$params = [
			'np' => $data_insert['created_by_np'],
			'nama' => $data_insert['created_by_nama'],
			'poin' => $data_insert['poin_hasil'],
		];
		$result = $this->tambah_poin($params);
		return $result;
	}

	public function tambah_poin($params)
	{
		$return = false;

		$data = $this->db->from($this->table)
			->where('np', $params['np'])
			->where('tahun', date('Y'))
			->get();

		if ($data->num_rows() == 0) {
			$params['tahun'] = date('Y');
			$return = $this->db->insert($this->table, $params);
		} else {
			$this->db->set('poin', $params['poin'], false);
			$this->db->where('np', $params['np']);
			$this->db->where('tahun', date('Y'));
			$return = $this->db->update($this->table);
		}
		return $return;
	}

	public function poin_sekarang($np)
	{
		$tahun = date('Y');
		$data = $this->db->select("poin")
			->from($this->table)
			->where("np", $np)
			->where('tahun', $tahun)
			->get();
		$poin = '0';

		if ($data->num_rows() == 1) {
			$poin = $data->result_array()[0]["poin"];
		}
		return $poin;
	}

	public function import($file_path)
	{
		$spreadsheet = IOFactory::load($file_path);
		$sheet = $spreadsheet->getActiveSheet();
		$data = $sheet->toArray();
		array_shift($data);

		return $data;
	}

	public function insert_batch_old($data, $data_karyawan)
	{
		$this->db->trans_start();

		foreach ($data as $row) {
			$ref_dokumen = $this->db->where('lower(nama)', strtolower($row[2]))->get('ref_jenis_dokumen_contribution')->row_array();
			$ref_karyawan = $this->db->where('no_pokok', $row[0])->get('mst_karyawan')->row_array();
			$insert_data = array(
				'nama_karyawan' => $ref_karyawan['nama'],
				'np_karyawan' => $row[0],
				'perihal' => $row[1],
				'jenis_dokumen' => $ref_dokumen['nama'],
				'jenis_dokumen_id' => $ref_dokumen['id'],
				'tanggal_dokumen' => $row[3],
				'url' => $row[4],
				'asal' => 'import',
				'created_at' => date('Y-m-d H:i:s'),
				'created_by_np' => $data_karyawan['no_pokok'],
				'created_by_nama' => $data_karyawan['nama'],
				'tanggal_submit' => date('Y-m-d H:i:s'),
				'kode_unit' => $ref_karyawan['kode_unit'],
				'nama_unit' => $ref_karyawan['nama_unit'],
			);
			$this->db->insert('my_contribution', $insert_data);
		}

		$this->db->trans_complete();

		return ['message' => "Berhasil menyimpan data ke database."];
		if ($this->db->trans_status() === FALSE) {
			return ['message' => "Terjadi kesalahan saat menyimpan data ke database."];
		}
	}
	public function insert_batch($data, $data_karyawan)
	{
		$this->db->trans_start();

		$nama_dokumen_list = array_unique(array_column($data, 2));
		$no_pokok_list = array_unique(array_column($data, 0));

		$ref_dokumen_list = $this->db->where_in('lower(nama)', array_map('strtolower', $nama_dokumen_list))
			->get('ref_jenis_dokumen_contribution')
			->result_array();
		$ref_dokumen_map = array_change_key_case(array_column($ref_dokumen_list, null, 'nama'), CASE_LOWER);

		$ref_karyawan_list = $this->db->where_in('no_pokok', $no_pokok_list)
			->get('mst_karyawan')
			->result_array();
		$ref_karyawan_map = array_column($ref_karyawan_list, null, 'no_pokok');

		$insert_data = [];
		$invalid_data = [];
		foreach ($data as $row) {
			$ref_dokumen = $ref_dokumen_map[strtolower($row[2])] ? $ref_dokumen_map[strtolower($row[2])] : null;
			$ref_karyawan = $ref_karyawan_map[$row[0]] ? $ref_karyawan_map[$row[0]] : null;
			if ($ref_dokumen && $ref_karyawan) {
				$insert_data[] = [
					'nama_karyawan' => $ref_karyawan['nama'],
					'np_karyawan' => $row[0],
					'perihal' => $row[1],
					'jenis_dokumen' => $ref_dokumen['nama'],
					'jenis_dokumen_id' => $ref_dokumen['id'],
					'tanggal_dokumen' => $row[3],
					'url' => $row[4],
					'asal' => 'import',
					'created_at' => date('Y-m-d H:i:s'),
					'created_by_np' => $data_karyawan['no_pokok'],
					'created_by_nama' => $data_karyawan['nama'],
					'tanggal_submit' => date('Y-m-d H:i:s'),
					'kode_unit' => $ref_karyawan['kode_unit'],
					'nama_unit' => $ref_karyawan['nama_unit'],
				];
			} else {
				$invalid_data[] = $row;
			}
		}

		if (!empty($insert_data)) {
			$this->db->insert_batch('my_contribution', $insert_data);
		}

		$this->db->trans_complete();

		return [
			'status' => $this->db->trans_status(),
			'inserted_count' => count($insert_data),
			'invalid_count' => count($invalid_data),
			'invalid_data' => $invalid_data
		];
	}
}
