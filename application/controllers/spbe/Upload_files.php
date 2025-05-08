<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Upload_files extends CI_Controller {
	public function __construct(){
		parent::__construct();
		
		$meta = meta_data();
		foreach($meta as $key => $value){
			$this->data[$key] = $value;
		}
		
		$this->folder_view = 'spbe/';
		$this->folder_model = 'spbe/';
		$this->folder_controller = 'spbe/';
		
		$this->akses = array();
		
		$this->load->helper("cutoff_helper");
		$this->load->helper("tanggal_helper");
		$this->load->helper("karyawan_helper");
		$this->load->helper("reference_helper");
		
		$this->load->model($this->folder_model."M_tabel_permohonan_spbe");
		$this->load->model($this->folder_model."M_permohonan_spbe");
		
		$this->data["is_with_sidebar"] = true;
		
		$this->data['judul'] = "Permohonan SPBE";
		$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
		$this->akses = akses_helper($this->data['id_modul']);				
		$this->nama_db = $this->db->database;
		izin($this->akses["akses"]);
	}
	
	public function index(){
		$this->data["akses"] = $this->akses;
		$this->data["navigasi_menu"] = menu_helper();
		$this->data['content'] = $this->folder_view."permohonan_spbe";

		$this->load->view('template',$this->data);
	}

	function simpan($id){
        if (isset($_FILES['upload_file'])) {
            if(!is_dir('./uploads/spbe')) mkdir('./uploads/spbe');
            if(!is_dir('./uploads/spbe/'.$id)) mkdir('./uploads/spbe/'.$id);
            $foldername='./uploads/spbe/'.$id;
            $insert=[];
            foreach($_FILES['upload_file']['name'] as $i => $name) {
                if(strlen($_FILES['upload_file']['name'][$i]) > 1 && ($_FILES['upload_file']['type'][$i]=='application/pdf')) {
                    $name_file = md5($name) . '.' . pathinfo($name, PATHINFO_EXTENSION); // Mengenkripsi nama file dengan md5
                    if (move_uploaded_file($_FILES['upload_file']['tmp_name'][$i],$foldername."/".$name_file)) { 
                        $row = [
                            'id' => $this->uuid->v4(),
                            'ess_permohonan_spbe_id' => $id,
                            'nama_file' => $name_file,
                            'path_file' => 'uploads/spbe/'. $id . "/". $name_file, 
                            'jenis_file' => 'lampiran',
                            'created_at' => date('Y-m-d H:i:s')
                        ];       
                        $insert[]=$row;        
                    }
                }else{
                    $response = [
                        'status' => false,
                        'message' => "File Harus PDF"
                    ];
                    exit();
                }
            }
            $this->db->insert_batch('ess_permohonan_spbe_file', $insert);
            $response = [
                'status' => true,
                'message' => "Berhasil Menambahkan Data"
            ];
        }else {
            $nama_file_img = 'uploads/noimage.png';
            $response = [
                'status' => true,
                'message' => "Berhasil Menambahkan Data"
            ];
        }

        header("Content-Type: application/json");
		echo json_encode($response);
	}

    public function upload_pdf($element = null, $nama_file_pdf = null)
    {
        $config['upload_path']   = "uploads/spbe/";
        $config['allowed_types'] = 'pdf';
        $config['max_size']      = 10048; //10MB 
        $config['file_name']     = $nama_file_pdf;
        
        $this->load->library('upload');
        $this->upload->initialize($config);
        
        print_r($this->upload->do_upload($element));
        die;
        if (!$this->upload->do_upload($element)) {
            $data = [
                'is_upload' => false,
                'file_name' => null,
                'error'     => $this->upload->display_errors(),
            ];
        } else {
            $upload_pdf = $this->upload->data();
            $data = [
                'is_upload' => false,
                'file_name' => $upload_pdf['file_name'],
                'error'     => $this->upload->display_errors(),
            ];
        }
        return $data;
    }

    public function upload_img($element = null, $nama_file_img = null)
    {
        $config['upload_path']   = "uploads/spbe/";
        $config['allowed_types'] = 'jpeg|jpg|png';
        // $config['encrypt_name'] = TRUE; 
        $config['max_size']      = 2048; //1MB 
        $config['file_name']     = $nama_file_img;

        $this->load->library('upload');
        $this->upload->initialize($config);
        if (!$this->upload->do_upload($element)) {
            $data = [
                'is_upload' => false,
                'file_name' => null,
                'error'     => $this->upload->display_errors(),
            ];
        } else {
            $upload_img = $this->upload->data();
            $data = [
                'is_upload' => false,
                'file_name' => $upload_img['file_name'],
                'error'     => $this->upload->display_errors(),
            ];
        }
        return $data;
    }

	function get_atasan(){
		$this->load->model('m_approval');
		$np_karyawan = $this->input->post('np_karyawan');
		$kode_unit = [$this->input->post('kode_unit')];
		$list = $this->m_approval->list_atasan_minimal_kadep($kode_unit, $np_karyawan);
		header("Content-Type: application/json");
		echo json_encode($list);
	}
}