<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ttd extends CI_Controller {
    public function __construct(){
        parent::__construct();

        $meta = meta_data();
        foreach($meta as $key => $value){
            $this->data[$key] = $value;
        }

        $this->folder_view = 'food_n_go/master_data/';
        $this->folder_model = 'kendaraan/';
        $this->folder_ajax_view = $this->folder_view.'ajax/';
        $this->akses = array();

        $this->load->model($this->folder_model."/m_master_data_ttd");

        $this->data['success'] = "";
        $this->data['warning'] = "";

        $this->data["is_with_sidebar"] = true;
    }

    public function index(){
        $this->data['judul'] = "Tandatangan Laporan Kendaraan";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);

        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."ttd";

        array_push($this->data['js_sources'],"food_n_go/master_data/ttd");

        if($this->input->post()) {
            $this->data['panel_tambah'] = "in";
            izin($this->akses["tambah"]);
        } else {
            $this->data['panel_tambah'] = "in";
        }

        if($this->akses["lihat"]){
            $js_header_script = "<script>
                            $(document).ready(function() {
                                $('#tabel_ttd').DataTable({
                                    responsive: true
                                });
                            });
                        </script>";

            array_push($this->data["js_header_script"],$js_header_script);

            $get_mst_karyawan = $this->m_master_data_ttd->get_mst_karyawan();
            $this->data["mst_karyawan"] = $get_mst_karyawan->result_array();
            
            # existing 
            $get_kiri = $this->db->where(['type'=>'kiri', 'status'=>'1'])->order_by('id','DESC')->limit(1)->get('mst_ttd_kendaraan')->row();
            $this->data["existing_kiri"] = $get_kiri;
            $get_kanan = $this->db->where(['type'=>'kanan', 'status'=>'1'])->order_by('id','DESC')->limit(1)->get('mst_ttd_kendaraan')->row();
            $this->data["existing_kanan"] = $get_kanan;
            
            # histori
            $get_histori_ttd = $this->db->order_by('id','DESC')->get('mst_ttd_kendaraan')->result();
            $this->data["histori_ttd"] = $get_histori_ttd;

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
    
    function simpan(){
        /*echo json_encode($this->input->post()); 
        exit;*/
        # kiri
        $kiri_np = $this->input->post('kiri_np',true);
        $kiri_nama = $this->input->post('kiri_nama',true);
        $kiri_nama_unit = $this->input->post('kiri_nama_unit',true);
        $kiri_jabatan = $this->input->post('kiri_jabatan',true);
        
        # check if np kiri exist
        $where_kiri = ['type'=>'kiri', 'np'=>$kiri_np, 'status'=>'1'];
        $cek_kiri = $this->db->where($where_kiri)->get('mst_ttd_kendaraan');
        if($cek_kiri->num_rows()==0){
            $this->db->where(['type'=>'kiri', 'status'=>'1'])->update('mst_ttd_kendaraan',['status'=>'0','deleted'=>date('Y-m-d H:i:s')]);
            # insert
            $this->db->insert('mst_ttd_kendaraan', [
                'kode'=>$this->uuid->v4(),
                'type'=>'kiri',
                'keterangan'=>'Mengetahui',
                'nama_unit'=>$kiri_nama_unit,
                'np'=>$kiri_np,
                'nama'=>$kiri_nama,
                'jabatan'=>$kiri_jabatan,
                'created'=>date('Y-m-d H:i:s'),
                'status'=>'1'
            ]);
        } else{
            # update
            $this->db->where('id',$cek_kiri->result()[0]->id)->update('mst_ttd_kendaraan', [
                'nama_unit'=>$kiri_nama_unit,
                'np'=>$kiri_np,
                'nama'=>$kiri_nama,
                'jabatan'=>$kiri_jabatan,
                'updated'=>date('Y-m-d H:i:s'),
                'status'=>'1'
            ]);
        }
        # END kiri
        
        # kanan
        $kanan_np = $this->input->post('kanan_np',true);
        $kanan_nama = $this->input->post('kanan_nama',true);
        $kanan_nama_unit = $this->input->post('kanan_nama_unit',true);
        $kanan_jabatan = $this->input->post('kanan_jabatan',true);
        
        # check if np kanan exist
        $where_kanan = ['type'=>'kanan', 'np'=>$kanan_np, 'status'=>'1'];
        $cek_kanan = $this->db->where($where_kanan)->get('mst_ttd_kendaraan');
        if($cek_kanan->num_rows()==0){
            $this->db->where(['type'=>'kanan', 'status'=>'1'])->update('mst_ttd_kendaraan',['status'=>'0','deleted'=>date('Y-m-d H:i:s')]);
            # insert
            $this->db->insert('mst_ttd_kendaraan', [
                'kode'=>$this->uuid->v4(),
                'type'=>'kanan',
                'nama_unit'=>$kanan_nama_unit,
                'np'=>$kanan_np,
                'nama'=>$kanan_nama,
                'jabatan'=>$kanan_jabatan,
                'created'=>date('Y-m-d H:i:s'),
                'status'=>'1'
            ]);
        } else{
            # update
            $this->db->where('id',$cek_kanan->result()[0]->id)->update('mst_ttd_kendaraan', [
                'nama_unit'=>$kanan_nama_unit,
                'np'=>$kanan_np,
                'nama'=>$kanan_nama,
                'jabatan'=>$kanan_jabatan,
                'updated'=>date('Y-m-d H:i:s'),
                'status'=>'1'
            ]);
        }
        # END kanan
        
        $this->session->set_flashdata('success','Konfigurasi tanda tangan telah diupdate');
        redirect('food_n_go/master_data/ttd');
    }
}