<?php
class Pembaruan_email extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('pelaporan/M_perubahan_email');
        $this->load->model('m_setting');
        $this->folder_model = 'pelaporan/';
        $this->folder_view = 'pelaporan/';
        $this->data["is_with_sidebar"] = true;

        $this->akses = array();
			
        $this->load->helper("cutoff_helper");
        $this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
        $this->load->helper("reference_helper");
			
        $this->data['judul'] = "Pembaruan Email";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);				
        $this->nama_db = $this->db->database;
        // print_r($this->akses);
      
        izin($this->akses["akses"]);
        
    }

    public function input_data() {
        // Validasi form jika diperlukan

        $data = array(
            'np_karyawan' => $this->input->post('np_karyawan'),
            'email_lama' => $this->input->post('email_lama'),
            'email_baru' => $this->input->post('email_baru'),
            'np_atasan' => $this->input->post('np_atasan'),
            'nama_atasan' => $this->input->post('nama_atasan'),
            'keterangan' => $this->input->post('keterangan'),
            'status_approve' => 'Menunggu Persetujuan' // Default status approve
        );

        $this->M_pembaruan_email->insert_data($data);

        // Redirect atau tampilkan pesan berhasil
    }

    public function index() {
        $this->load->model($this->folder_model."M_pelaporan");

        $array_daftar_karyawan	= $this->M_pelaporan->select_daftar_karyawan();
        $this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] 		= $this->folder_view."lihat_pembaruan_email";
        $data['laporan_data'] = $this->M_perubahan_email->get_laporan_data();
        $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
		$this->data['regulasi']					= @$this->db->where('id_laporan',$this->data['id_modul'])->get('mst_regulasi')->row()->regulasi;

        // Load view untuk menampilkan data
        $this->load->view('template',$this->data);
    }
}
