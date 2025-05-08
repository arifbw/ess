<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Plafon_lembur extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$meta = meta_data();
		foreach ($meta as $key => $value) {
			$this->data[$key] = $value;
		}

		$this->folder_view = 'lembur/plafon_lembur/';
		$this->folder_model = 'lembur/';
		$this->folder_controller = 'lembur/';

		$this->akses = array();

		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");

		$this->data["is_with_sidebar"] = true;

		$this->load->model($this->folder_model . 'M_plafon_lembur');

		$this->data['judul'] = "Plafon Lembur";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);
		izin($this->akses["akses"]);
	}

	public function index()
	{
		$this->data["akses"] 					= $this->akses;
		$this->data["navigasi_menu"] 			= menu_helper();
		$this->data['content'] 					= $this->folder_view . "list";

		$divisi = $this->M_plafon_lembur->get_sto_divisi();
		$this->data['divisi'] = $divisi;

		$this->load->view('template', $this->data);
	}

	public function data()
	{
		$list 	= $this->M_plafon_lembur->get_datatables();

		$data = array();
		$no = $_POST['start'];


		foreach ($list as $tampil) {
			$no++;
			$row = array();
			$row[] = $tampil->id;
			$row[] = $no;
			$row[] = $tampil->object_name;
			$row[] = number_format($tampil->nominal, 0, ',', '.');
			$row[] = '<button class="btn btn-primary" data-id="' . $tampil->id . '" id="btn-edit">Edit</button> <button class="btn btn-danger" data-id="' . $tampil->id . '" id="btn-delete">Delete</button>';

			$data[] = $row;
		}

		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->M_plafon_lembur->count_all(),
			"recordsFiltered" => $this->M_plafon_lembur->count_filtered(),
			"data" => $data,
		);
		//output to json format
		echo json_encode($output);
	}

	function get_one($id) {
		echo json_encode($this->M_plafon_lembur->getDataById($id));
	}

	function store()
	{
		$id = $this->input->post('id', TRUE);

		$this->db->trans_begin();

		$data = [
			'kode_unit' => $this->input->post('kode_unit', TRUE),
			'nominal' => $this->input->post('nominal', TRUE),
			'tahun' => $this->input->post('tahun', TRUE)
		];

		if (@$id == null) {
			$cek = $this->db
				->where('deleted_at IS NULL', null, false)
				->where('kode_unit', $this->input->post('kode_unit', TRUE))
				->where('tahun', $this->input->post('tahun', TRUE))
				->get($this->M_plafon_lembur->table)
				->row();
			if($cek){
				$this->session->set_flashdata('warning', "Plafon Sudah Pernah Ditambahkan");
				redirect(base_url($this->folder_controller . 'plafon_lembur'));
			}
			// $data['tahun'] = date('Y');
			$data['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('plafon_lembur', $data);
		} else {
			$data['updated_at'] = date('Y-m-d H:i:s');
			$this->db->where('id', $id)->update('plafon_lembur', $data);
		}

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			$this->session->set_flashdata('warning', "Plafon Gagal Ditambahkan");
			redirect(base_url($this->folder_controller . 'plafon_lembur'));
		} else {
			$this->db->trans_commit();

			$this->session->set_flashdata('success', "Plafon Berhasil Ditambahkan");
			redirect(base_url($this->folder_controller . 'plafon_lembur'));
		}
	}

	function destroy($id)
	{
		$this->db->trans_begin();
		$data['deleted_at'] = date('Y-m-d H:i:s');

		$this->db->where('id', $id)->update('plafon_lembur', $data);

		if ($this->db->trans_status() === FALSE) {
			$this->db->trans_rollback();

			$this->session->set_flashdata('warning', "Gagal menghapus data!");
			redirect(base_url($this->folder_controller . 'plafon_lembur'));
		} else {
			$this->db->trans_commit();

			$this->session->set_flashdata('success', "Berhasil menghapus data!");
			redirect(base_url($this->folder_controller . 'plafon_lembur'));
		}
	}
}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */
