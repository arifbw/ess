<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_monitoring extends CI_Model
{

	private $table = "ijt_verval";

	public function __construct()
	{
		parent::__construct();
	}

	var $column_order = array(null, null, 'b.nama'); //field yang ada di table user
	var $column_search = array('b.nama'); //field yang diizin untuk pencarian 
	var $order = array('a.created_at' => 'DESC'); // default order 

	private function _get_datatables_query()
	{
		$this->db->select('a.apply_id, MAX(a.id) as id, GROUP_CONCAT(a.is_verval SEPARATOR ", ") as is_verval, 
						GROUP_CONCAT(a.jenis_verval SEPARATOR ", ") as jenis_verval,
						e.file,
						b.nama as nama_karyawan, c.nama_jabatan, b.no_pokok');
		$this->db->from($this->table . ' a');
		$this->db->join('ijt_apply d', 'd.id = a.apply_id', 'left');
		$this->db->join('mst_karyawan b', 'd.np = b.no_pokok', 'left');
		$this->db->join('ijt_data c', 'd.job_id = c.id', 'left');		
		$this->db->join('ijt_apply_dokumen e', 'e.ijt_apply_id = a.apply_id', 'left');
		$this->db->where('a.deleted_at is null');

		// Tambahkan GROUP BY apply_id
		$this->db->group_by('a.apply_id, e.ijt_apply_id, b.nama, c.nama_jabatan');

		$i = 0;

		foreach ($this->column_search as $item) // looping awal
		{
			if ($_POST['search']['value']) // jika datatable mengirimkan pencarian dengan metode POST
			{

				if ($i === 0) // looping awal
				{
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				} else {
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if (count($this->column_search) - 1 == $i)
					$this->db->group_end();
			}
			$i++;
		}

		if (!empty($this->input->post('filter'))) {
			$this->db->like('YEAR(a.tanggal)', $this->input->post('filter')[0]['tahun']);
		}

		if (isset($_POST['order'])) {
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if (isset($this->order)) {
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	public function get_datatables()
	{
		$this->_get_datatables_query();
		if ($_POST['length'] != -1)
			$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->_get_datatables_query();
		return $this->db->count_all_results();
	}

	public function tahunAgenda()
	{
		$this->db->select('YEAR(tanggal) as id, YEAR(tanggal) as text');
		$this->db->from($this->table);

		$this->db->where('status', '1');
		$this->db->group_by('YEAR(tanggal)');

		return $this->db->get()->result_array();
	}

	public function insert($data)
	{
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	public function cek_daftar_agenda($id)
	{
		if (@$_SESSION["grup"] == 5)
			$this->db->select('a.*, b.nama as nama_provinsi, c.nama as nama_kabupaten, d.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, (select count(*) from ess_agenda_pendaftaran where np_karyawan="' . $_SESSION['no_pokok'] . '" and id_agenda=a.id) as id_daftar, e.nama_kategori');
		else
			$this->db->select('a.*, b.nama as nama_provinsi, c.nama as nama_kabupaten, d.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, 0 as id_daftar, e.nama_kategori');

		$this->db->from($this->table . ' a');
		$this->db->join('provinsi b', 'a.provinsi = b.kode_wilayah', 'left');
		$this->db->join('kabupaten c', 'a.kabupaten = c.kode_wilayah', 'left');
		$this->db->join('mst_lokasi d', 'a.lokasi = d.id', 'left');
		$this->db->join('mst_kategori_agenda e', 'a.id_kategori = e.id', 'left');
		$this->db->where('a.id', $id);

		return $this->db->get()->row();
	}

	public function api_cek_daftar_agenda($id, $np, $group_id)
	{
		if ($group_id == 5)
			$this->db->select('a.*, b.nama as nama_provinsi, c.nama as nama_kabupaten, d.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, (select count(*) from ess_agenda_pendaftaran where np_karyawan="' . $np . '" and id_agenda=a.id) as id_daftar, e.nama_kategori');
		else
			$this->db->select('a.*, b.nama as nama_provinsi, c.nama as nama_kabupaten, d.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, 0 as id_daftar, e.nama_kategori');

		$this->db->from($this->table . ' a');
		$this->db->join('provinsi b', 'a.provinsi = b.kode_wilayah', 'left');
		$this->db->join('kabupaten c', 'a.kabupaten = c.kode_wilayah', 'left');
		$this->db->join('mst_lokasi d', 'a.lokasi = d.id', 'left');
		$this->db->join('mst_kategori_agenda e', 'a.id_kategori = e.id', 'left');
		$this->db->where('a.id', $id);

		return $this->db->get()->row();
	}

	public function daftarPeserta($id)
	{
		$this->db->select('a.daftar_at, a.np_karyawan, a.verifikasi_hadir, b.nama');
		$this->db->from('ess_agenda_pendaftaran a');
		$this->db->join('mst_karyawan b', 'a.np_karyawan=b.no_pokok');

		$this->db->where('a.id_agenda', $id);

		return $this->db->get()->result();
	}

	public function update($data, $id)
	{
		$this->db->where('id', $id);

		return $this->db->update($this->table, $data);
	}

	public function daftar_lokasi()
	{
		$this->db->from('mst_lokasi');
		$this->db->where('status', '1');

		return $this->db->get()->result();
	}

	public function daftar_provinsi()
	{
		$this->db->select('kode_wilayah as id, nama as text');
		$this->db->from('provinsi');

		$search = $this->input->get('search');
		if (!empty($search)) {
			$this->db->like('nama', $search);
		}

		return $this->db->get()->result_array();
	}

	public function daftar_kabupaten($provinsi = null)
	{
		$this->db->select('kode_wilayah as id, nama as text');
		$this->db->from('kabupaten');

		if ($provinsi != null) {
			$this->db->where('kode_prop', $provinsi);
		}

		$search = $this->input->get('search');
		if (!empty($search)) {
			$this->db->like('nama', $search);
		}

		return $this->db->get()->result_array();
	}

	public function kategoriAgenda()
	{
		$this->db->select('id, nama_kategori as text');
		$this->db->from('mst_kategori_agenda');

		$this->db->where('status', '1');
		$this->db->where('FIND_IN_SET("' . $this->session->userdata('grup') . '", grup_pengguna)');


		$search = $this->input->get('search');
		if (!empty($search)) {
			$this->db->like('nama_kategori', $search);
		}

		return $this->db->get()->result_array();
	}

	public function cekDataKaryawan($id)
	{
		$this->db->select('no_pokok');
		$this->db->from('mst_karyawan');
		$this->db->where('no_pokok', $id);

		return $this->db->get()->row();
	}

	public function countPeserta($id)
	{
		$this->db->select('COUNT(np_karyawan) as peserta');
		$this->db->from('ess_agenda_pendaftaran');
		$this->db->where([
			'id_agenda' => $id,
			'batal_at' => null
		]);

		return $this->db->get()->row();
	}

	public function cekTerdaftarAgenda($np, $agd)
	{
		$this->db->from('ess_agenda_pendaftaran');
		$this->db->where([
			'np_karyawan' => $np,
			'id_agenda' => $agd,
			'batal_at' => null
		]);

		return $this->db->get()->row();
	}

	public function daftarPesertaAgenda($id)
	{
		$this->db->select('b.agenda, c.no_pokok, c.nama, c.jenis_kelamin, c.tanggal_lahir, c.nama_unit, a.verifikasi_hadir')
			->from('ess_agenda_pendaftaran a')
			->join('ess_agenda b', 'a.id_agenda = b.id')
			->join('mst_karyawan c', 'a.np_karyawan = c.no_pokok')
			->where('a.id_agenda', $id)
			->where('a.batal_at is null', null, false);

		return $this->db->get()->result();
	}

	public function detailAgenda($id)
	{
		$this->db->from('ess_agenda')
			->where('id', $id);

		return $this->db->get()->row();
	}

	public function insertVerval($data)
	{
		$data = [
			'apply_id' => $data['apply_id'],
			'is_verval' => $data['is_verval'],
			'keterangan' => $data['keterangan'],
			'jenis_verval' => $data['jenis_verval'],
			'verif_at' => date('Y-m-d H:i:s'),
			'verif_by' => $data['verif_by']
		];

		$this->db->insert('ijt_verval', $data);

		if ($this->db->affected_rows() > 0) {
			return $this->db->insert_id();
		} else {
			return "0";
		}		
	}
}
