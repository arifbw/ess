<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pembaruan_email extends CI_Controller {
    public function __construct(){
        parent::__construct();
        
        $meta = meta_data();
        foreach($meta as $key => $value){
            $this->data[$key] = $value;
        }
        $this->load->model('pelaporan/M_perubahan_email');
        $this->folder_view = 'pelaporan/';
        $this->folder_model = 'pelaporan/';
        $this->folder_controller = 'pelaporan/';
        
        $this->akses = array();
        
        $this->load->helper("cutoff_helper");
        $this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
        $this->load->helper("reference_helper");
        
        #$this->load->model($this->folder_model."m_permohonan_cuti");
        
        $this->data["is_with_sidebar"] = true;
        
        $this->data['judul'] = "Pembaruan Email";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);			
        $this->nama_db = $this->db->database;
        $this->data['pembaruan_email'] = 
        izin($this->akses["akses"]);
    }
    
    public function index()
    {
        $this->load->model($this->folder_model."M_pelaporan");

        $array_daftar_karyawan	= $this->M_pelaporan->select_daftar_karyawan();

        // echo json_encode($outsource); exit;
        $this->data["akses"] 					= $this->akses;
        $this->data["navigasi_menu"] 			= menu_helper();
        $this->data['content'] 					= $this->folder_view."lihat_pembaruan_email";
        $this->data['array_daftar_karyawan'] 	= $array_daftar_karyawan;
        $this->data['regulasi']					= @$this->db->where('id_laporan',$this->data['id_modul'])->get('mst_regulasi')->row()->regulasi;

        $this->load->view('template',$this->data);
    }	

    public function ajax_getNama_approval()
		{
			$np_atasan = $this->input->post('np_aprover');
			if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				$np_karyawan = $this->input->post('np_karyawan');
				$kode_unit = array(kode_unit_by_np($np_karyawan));
			} else if($_SESSION["grup"]==5) { //jika Pengguna
				$np_karyawan = $_SESSION["no_pokok"];
				$kode_unit = array($_SESSION["kode_unit"]);
			} else {
				$np_karyawan = $this->input->post('np_karyawan');
				$kode_unit = array(kode_unit_by_np($np_karyawan));
			}

			$return = [
                'status'=>false,
                'data'=>[],
                'message'=>'Silahkan isi No. Pokok Atasan Dengan Benar',
            ];

			if ($np_atasan==$np_karyawan) {
				$return['message'] = 'No. Pokok Approver Tidak Valid';
			} else {
				$this->load->model('m_approval');

				$list = $this->m_approval->list_atasan_minimal_kasek($kode_unit, $np_karyawan);
				$return['message'] = 'Approval Pelaporan Minimal Kasek';

				$list_np = array_column($list, 'no_pokok');
				if (in_array($np_atasan, $list_np)) {
					$key = array_search($np_atasan, $list_np);
					$data['nama'] = $list[$key]['nama'];
					$data['nama_jabatan'] = $list[$key]['nama_jabatan'];
				}


				if (@$data) {
	                $return = [
	                    'status'=>true,
	                    'data'=>[
	                        'nama'=>$data['nama'],
	                        'jabatan'=>$data['nama_jabatan']
	                    ]
	                ];
				} else {
					$start_date			= date('Y-m-d');
					$end_date			= date('Y-m-d');
	                $tahun_bulan     	= $start_date!=null ? str_replace('-','_',substr("$start_date", 0, 7)) : str_replace('-','_',substr("$end_date", 0, 7)) ;
					$nama_karyawan 		= erp_master_data_by_np($np_atasan, $start_date)['nama'];
					$nama_jabatan		= erp_master_data_by_np($np_atasan, $start_date)['nama_jabatan'];
					
					$return = [
	                    'status'=>true,
	                    'data'=>[
	                        'nama'=>$nama_karyawan,
	                        'jabatan'=>$nama_jabatan
	                    ]
	                ];
				}
			}

            echo json_encode($return);
		}

    public function tabel_laporan_perubahan_email() {
        if($this->akses["lihat"]) {
            $this->load->model($this->folder_model . "M_perubahan_email");
    
            $list = $this->M_perubahan_email->get_datatables();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $tampil) {
                $status_text = '';
                $status_class = '';

                if ($tampil->status_approve == 'Menunggu Persetujuan') {
                    $status_text = 'Menunggu Persetujuan';
                    $status_class = 'text-primary'; // warna teks biru
                } elseif ($tampil->status_approve == 'Disetujui') {
                    $status_text = 'Disetujui';
                    $status_class = 'text-success'; // warna teks hijau
                } elseif ($tampil->status_approve == 'Ditolak') {
                    $status_text = 'Ditolak';
                    $status_class = 'text-danger'; // warna teks merah
                } else {
                    $status_text = 'Status tidak valid';
                    $status_class = 'text-warning'; // warna teks kuning
                }
                $no++;
    
                $row = array();
                $row[] = $no;
                $row[] = $tampil->nama_karyawan;
                $row[] = $tampil->email_lama;
                $row[] = $tampil->email_baru;
                $row[] = $tampil->np_atasan;
                $row[] = $tampil->nama_atasan;
                $row[] = $tampil->keterangan;
                $row[] = '<span class="' . $status_class . '">' . $status_text . '</span>';
                if($tampil->status_approve=='Ditolak' || $tampil->status_approve=='Disetujui'){
                    $row[] = 'Sudah diproses';
                }else{
                    $row[] = '<button type="button" data-id="'. $tampil->id .'" class="btn btn-danger" data-toggle="modal" data-target="#modal-inactive">Hapus</button>';
                }
                
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->M_perubahan_email->count_all(),
                "recordsFiltered" => $this->M_perubahan_email->count_filtered(),
                "data" => $data,
            );
    
            //output to json format
            echo json_encode($output);
        }
    }

    public function delete_data($id) {
        if($this->akses["hapus"]) {
            // Memuat model yang diperlukan
            $this->load->model($this->folder_model . "M_perubahan_email");
    
            // Menghapus data dengan ID yang diberikan
            $result = $this->M_perubahan_email->delete_data($id);
    
            if ($result) {
                // Jika penghapusan berhasil
                $response = 'success';
                $this->session->set_flashdata('success', 'Data berhasil dihapus');
            } else {
                // Jika terjadi kesalahan saat menghapus data
                $response = 'failed';
                $this->session->set_flashdata('warning', 'Gagal menghapus data');
            }
    
            // Mengirimkan respons ke klien
            echo $response;
        }
    }
    
    

    public function action_insert() {
        // Memuat model yang diperlukan
        $this->load->model($this->folder_model . "M_perubahan_email");
    
        // Mendapatkan data dari form dengan validasi
        $data = array(
            'np_karyawan' => $this->input->post('np_karyawan', TRUE), // Validasi jenis data sebagai string
            'nama_karyawan' => $this->input->post('nama_karyawan', TRUE), // Validasi jenis data sebagai string
            'email_lama' => $this->input->post('email_lama', TRUE), // Validasi jenis data sebagai string
            'email_baru' => $this->input->post('email_baru', TRUE), // Validasi jenis data sebagai string
            'np_atasan' => $this->input->post('np_atasan', TRUE), // Validasi jenis data sebagai string
            'nama_atasan' => $this->input->post('nama_atasan', TRUE), // Validasi jenis data sebagai string
            'jabatan_atasan' => $this->input->post('jabatan_atasan', TRUE), // Validasi jenis data sebagai string
            'keterangan' => $this->input->post('keterangan', TRUE), // Validasi jenis data sebagai string
            'status_approve' => $this->input->post('status_approve', TRUE) // Nilai default 'Menunggu Persetujuan'
        );
    
        // Validasi input
        $this->form_validation->set_rules('np_karyawan', 'NP Karyawan', 'required');
        $this->form_validation->set_rules('nama_karyawan', 'Nama Karyawan', 'required');
        $this->form_validation->set_rules('email_lama', 'Email Lama', 'required|valid_email');
        $this->form_validation->set_rules('email_baru', 'Email Baru', 'required|valid_email');
        $this->form_validation->set_rules('np_atasan', 'NP Atasan', 'required|min_length[4]');
        $this->form_validation->set_rules('nama_atasan', 'Nama Atasan', 'required');
        $this->form_validation->set_rules('jabatan_atasan', 'Jabatan Atasan', 'required');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            // Jika validasi gagal, kembali ke halaman form dengan pesan kesalahan
            $this->session->set_flashdata('warning', validation_errors()); // Menyimpan pesan error dalam flashdata
            redirect('pelaporan/pembaruan_email'); // Redirect ke halaman form
        } else {
            // Memanggil fungsi model untuk menyimpan data
            $result = $this->M_perubahan_email->insert_data($data);
        
            if ($result) {
                // Jika data berhasil disimpan
                $this->session->set_flashdata('success', 'Data berhasil disimpan!'); // Menyimpan pesan sukses dalam flashdata
                redirect('pelaporan/pembaruan_email'); // Redirect ke halaman setelah penyimpanan sukses
            } else {
                // Jika terjadi kesalahan saat menyimpan data
                $this->session->set_flashdata('warning', 'Gagal menyimpan data!'); // Menyimpan pesan error dalam flashdata
                redirect('pelaporan/pembaruan_email'); // Redirect ke halaman form
            }
        }
        
    }
    
    
}
