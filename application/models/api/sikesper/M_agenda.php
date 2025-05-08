<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_agenda extends CI_Model
{

	private $table = "ess_agenda";

	public function __construct()
	{
		parent::__construct();
	}

	var $column_order = array(null, 'a.agenda', 'a.tanggal', 'b.nama'); //field yang ada di table user
	var $column_search = array('a.agenda', 'a.tanggal', 'b.nama'); //field yang diizin untuk pencarian 
	var $order = array('a.tanggal' => 'DESC'); // default order 

	public function np_karyawan()
	{
		return $this->session->userdata('no_pokok');
	}

	private function _get_datatables_query()
	{
		$this->db->select('a.*, b.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar');
		$this->db->from($this->table . ' a');
		$this->db->join('mst_lokasi b', 'a.lokasi = b.id', 'LEFT');
		$this->db->join('mst_kategori_agenda c', 'a.id_kategori = c.id', 'LEFT');
		$this->db->where('a.status', '1');
		//$this->db->where('a.tanggal >=', date('Y-m-d'));

		//if($this->session->userdata('grup') != 5){
		if (!in_array($this->session->userdata('grup'), [5, 4])) {
			$this->db->where('FIND_IN_SET("' . $this->session->userdata('grup') . '", c.grup_pengguna)');
		}

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
		$this->db->from($this->table);
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
		if ($_SESSION["grup"] == 5)
			$this->db->select('a.*, b.nama as nama_provinsi, c.nama as nama_kabupaten, d.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, (select count(*) from ess_agenda_pendaftaran where np_karyawan="' . $_SESSION['no_pokok'] . '" and id_agenda=a.id) as id_daftar, e.nama_kategori');
		else
			$this->db->select('a.*, b.nama as nama_provinsi, c.nama as nama_kabupaten, d.nama as nama_lokasi, (select count(*) from ess_agenda_pendaftaran where id_agenda=a.id) as jml_daftar, 0 as id, e.nama_kategori');

		$this->db->from($this->table . ' a');
		$this->db->join('provinsi b', 'a.provinsi = b.kode_wilayah', 'left');
		$this->db->join('kabupaten c', 'a.kabupaten = c.kode_wilayah', 'left');
		$this->db->join('mst_lokasi d', 'a.lokasi = d.id', 'left');
		$this->db->join('mst_kategori_agenda e', 'a.id_kategori = e.id', 'left');
		$this->db->join('mst_karyawan k', "FIND_IN_SET(p.nomor_pokok, a.np_tergabung) and a.np_tergabung!='all'", 'left');
		$this->db->where('a.id', $id);

		return $this->db->get()->row();
	}

	public function daftarPeserta($id)
	{
		$this->db->select('a.id,a.daftar_at, a.np_karyawan, a.verifikasi_hadir, b.nama');
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
}
