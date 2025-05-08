<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_pemesanan_konsumsi_rapat extends CI_Controller {
    function __construct(){
        parent::__construct();

        $meta = meta_data();
        foreach($meta as $key => $value){
            $this->data[$key] = $value;
        }

        $this->folder_view = 'food_n_go/konsumsi/';
        //$this->folder_model = 'kendaraan/';
        $this->folder_ajax_view = $this->folder_view.'ajax/';
        $this->akses = array();

        $this->data['success'] = "";
        $this->data['warning'] = "";

        $this->data["is_with_sidebar"] = true;
        $this->load->helper('tanggal');
    }

    function index(){
        $this->data['judul'] = "Dashboard Pemesanan Konsumsi Rapat";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);

        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."dashboard_pemesanan_konsumsi_rapat";

        array_push($this->data['js_sources'],"food_n_go/konsumsi/dashboard_pemesanan_konsumsi_rapat");

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
    
    function get_data_table_rapat(){
        $start_date = (@$this->input->post('start_date',true)?$this->input->post('start_date',true):date('Y-m-d'));
        $end_date = (@$this->input->post('end_date',true)?$this->input->post('end_date',true):date('Y-m-d'));
        
        $list = $this->db->select("ess_pemesanan_konsumsi_rapat.tanggal_pemesanan, ess_pemesanan_konsumsi_rapat.nama_acara, CONCAT(ess_pemesanan_konsumsi_rapat.waktu_mulai,' - ',ess_pemesanan_konsumsi_rapat.waktu_selesai) as pukul, mst_ruangan.nama as nama_ruang, mst_lokasi.nama as nama_lokasi")
            ->from('ess_pemesanan_konsumsi_rapat')
            ->join('mst_ruangan','ess_pemesanan_konsumsi_rapat.id_ruangan=mst_ruangan.id','LEFT')
            ->join('mst_lokasi','ess_pemesanan_konsumsi_rapat.lokasi_acara=mst_lokasi.id','LEFT')
            ->where('ess_pemesanan_konsumsi_rapat.verified',3)
            ->where("(ess_pemesanan_konsumsi_rapat.tanggal_pemesanan BETWEEN '$start_date' AND '$end_date')")
            ->order_by('ess_pemesanan_konsumsi_rapat.tanggal_pemesanan','DESC')
            ->order_by('ess_pemesanan_konsumsi_rapat.waktu_mulai','ASC')
            ->get()->result();
        $data = array();
        $no = 0;
        
        foreach($list as $field){
            $no++;
            $row = array();
            $row[] = tanggal_indonesia($field->tanggal_pemesanan);
            $row[] = $field->nama_acara;
            $row[] = $field->pukul;
            $row[] = '('.$field->nama_lokasi.') '.$field->nama_ruang;
            $data[] = $row;
        }

        $output = array(
            "data" => $data
        );
        header('Content-Type: application/json');
        echo json_encode($output);
	}
    
    function get_data_table_lembur(){
        $start_date = (@$this->input->post('start_date',true)?$this->input->post('start_date',true):date('Y-m-d'));
        $end_date = (@$this->input->post('end_date',true)?$this->input->post('end_date',true):date('Y-m-d'));
        
        $list = $this->db->select("ess_pemesanan_makan_lembur.tanggal_pemesanan, CONCAT('Lembur ',ess_pemesanan_makan_lembur.jenis_lembur) as nama_acara, CONCAT(ess_pemesanan_makan_lembur.waktu_pemesanan_mulai,' - ',ess_pemesanan_makan_lembur.waktu_pemesanan_selesai) as pukul, mst_lokasi.nama as nama_lokasi, ess_pemesanan_makan_lembur.jenis_lembur")
            ->from('ess_pemesanan_makan_lembur')
            ->join('mst_lokasi','ess_pemesanan_makan_lembur.lokasi_lembur=mst_lokasi.id','LEFT')
            ->where('ess_pemesanan_makan_lembur.verified',3)
            ->where("(ess_pemesanan_makan_lembur.tanggal_pemesanan BETWEEN '$start_date' AND '$end_date')")
            ->order_by('ess_pemesanan_makan_lembur.tanggal_pemesanan','DESC')
            ->order_by('ess_pemesanan_makan_lembur.waktu_pemesanan_mulai','ASC')
            ->get()->result();
        $data = array();
        $no = 0;
        
        foreach($list as $field){
            $no++;
            $row = array();
            $row[] = tanggal_indonesia($field->tanggal_pemesanan);
            $row[] = $field->nama_acara;
            $row[] = $field->pukul;
            $row[] = $field->nama_lokasi;
            $data[] = $row;
        }

        $output = array(
            "data" => $data
        );
        header('Content-Type: application/json');
        echo json_encode($output);
	}
}