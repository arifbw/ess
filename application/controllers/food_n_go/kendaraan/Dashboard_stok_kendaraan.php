<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_stok_kendaraan extends CI_Controller {
    function __construct(){
        parent::__construct();

        $meta = meta_data();
        foreach($meta as $key => $value){
            $this->data[$key] = $value;
        }

        $this->folder_view = 'food_n_go/kendaraan/';
        //$this->folder_model = 'kendaraan/';
        $this->folder_ajax_view = $this->folder_view.'ajax/';
        $this->akses = array();

        $this->data['success'] = "";
        $this->data['warning'] = "";

        $this->data["is_with_sidebar"] = true;
        $this->load->helper('tanggal');
    }

    function index(){
        $this->data['judul'] = "Dashboard Stok Kendaraan";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);

        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."dashboard_stok_kendaraan";

        array_push($this->data['js_sources'],"food_n_go/kendaraan/dashboard_stok_kendaraan");

        if($this->akses["lihat"]){
            $log = array(
                "id_pengguna" => $this->session->userdata("id_pengguna"),
                "id_modul" => $this->data['id_modul'],
                "deskripsi" => "lihat ".strtolower(preg_replace("/_/"," ",__CLASS__)),
                "alamat_ip" => $this->data["ip_address"],
                "waktu" => date("Y-m-d H:i:s")
            );
            $this->m_log->tambah($log);
        }

        $this->load->view('template',$this->data);
    }
    
    function get_data_table(){
        $start_date = (@$this->input->post('start_date',true)?$this->input->post('start_date',true):date('Y-m-d'));
        $end_date = (@$this->input->post('end_date',true)?$this->input->post('end_date',true):date('Y-m-d'));
        $lokasi = $this->input->post('lokasi',true);
        
        $list = $this->db->select("a.jenis_kendaraan_request
                                    , a.id_mst_kendaraan, a.nama_mst_kendaraan, b.nopol
                                    , a.id_mst_driver, a.nama_mst_driver
                                    , a.kode_unit_pemesan, a.nama_unit_pemesan
                                    , a.tanggal_berangkat, a.jam
                                    , GROUP_CONCAT(CONCAT(c.keterangan_tujuan,', ',c.nama_kota_tujuan) SEPARATOR '|') as tujuan
                                    , a.unit_pemroses")
            ->from('ess_pemesanan_kendaraan a')
            ->join('mst_kendaraan b','a.id_mst_kendaraan=b.id','LEFT')
            ->join('ess_pemesanan_kendaraan_kota c','a.id=c.id_pemesanan_kendaraan','LEFT')
            ->where('a.tanggal_berangkat IS NOT NULL',null,false)
            ->where('a.status_persetujuan_admin',1)
            ->where('a.is_canceled_by_admin','0')
            ->where("(a.tanggal_berangkat BETWEEN '$start_date' AND '$end_date')")
            ->where('a.unit_pemroses',$lokasi)
            ->group_by('a.id')
            ->order_by('a.tanggal_berangkat','DESC')
            ->order_by('a.jam','ASC')
            ->get()->result();
        $data = array();
        $no = 0;
        
        foreach($list as $field){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->jenis_kendaraan_request;
            $row[] = $field->nopol;
            $row[] = $field->nama_mst_driver;
            $row[] = $field->nama_unit_pemesan;
            $row[] = tanggal_indonesia($field->tanggal_berangkat).', '.$field->jam;
            $row[] = str_replace('|','<br>',$field->tujuan);
            $data[] = $row;
        }

        $output = array(
            "data" => $data
        );
        header('Content-Type: application/json');
        echo json_encode($output);
	}
	
	function get_data_table_status(){
        $start_date = (@$this->input->post('start_date',true)?$this->input->post('start_date',true):date('Y-m-d'));
        $end_date = (@$this->input->post('end_date',true)?$this->input->post('end_date',true):date('Y-m-d'));
        $lokasi = $this->input->post('lokasi',true);
       		
		$list = $this->db->query("SELECT
									b.nama,
									b.nopol,
									(
									SELECT
										count(*) 
									FROM
										ess_pemesanan_kendaraan a 
									WHERE
										a.id_mst_kendaraan=b.id
										AND a.tanggal_berangkat IS NOT NULL 
										AND a.status_persetujuan_admin = '1' 
										AND a.is_canceled_by_admin = '0' 
									AND ( a.tanggal_berangkat BETWEEN '$start_date' AND '$end_date' )) AS pesanan 
								FROM
									mst_kendaraan b
								ORDER BY 
									pesanan DESC");
		
        $data = array();
        $no = 0;
        
        foreach($list->result_array() as $field){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field['nama'];
            $row[] = $field['nopol'];
			$row[] = $field['pesanan'];

            $data[] = $row;
        }

        $output = array(
            "data" => $data
        );
        header('Content-Type: application/json');
        echo json_encode($output);
	}
}