<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kode_anggaran extends CI_Controller {
    function __construct(){
        parent::__construct();

        $meta = meta_data();
        foreach($meta as $key => $value){
            $this->data[$key] = $value;
        }

        $this->folder_view = 'food_n_go/master_data/';
        $this->folder_model = 'konsumsi/';
        $this->folder_ajax_view = $this->folder_view.'ajax/';
        $this->akses = array();

        $this->data['success'] = "";
        $this->data['warning'] = "";

        $this->data["is_with_sidebar"] = true;
    }

    function index(){
        $this->data['judul'] = "Kode Anggaran";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);

        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."kode_anggaran";

        array_push($this->data['js_sources'],"food_n_go/master_data/kode_anggaran");

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
        $list = $this->db->order_by('created','DESC')->get('mst_kode_anggaran')->result();
        $data = array();
        $no = 0;
        
        foreach($list as $field){
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->nama;
            $row[] = $field->created;
            $row[] = '<select class="form-control" onchange="changeStatus(this)" id="status-'.$field->id.'" data-ids="'.$field->id.'">
                        <option value="1" '.($field->status==1?'selected':'').'>Aktif</option>
                        <option value="2" '.($field->status==0?'selected':'').'>Tidak Aktif</option>
                    </select>';
            $data[] = $row;
        }

        $output = array(
            "data" => $data
        );
        header('Content-Type: application/json');
        echo json_encode($output);
	}
    
    function save_new(){
        $response=[];
        $nama = trim($this->input->post('nama',true));
        if($nama!=''){
            $cek = $this->db->where('nama',$nama)->get('mst_kode_anggaran');
            if($cek->num_rows()==0){
                $this->db->insert('mst_kode_anggaran',[
                    'nama'=>$nama,
                    'created'=>date('Y-m-d H:i:s'),
                    'status'=>1
                ]);
                $response['status']=true;
                $response['message']='Kode Anggaran telah ditambahkan';
            } else{
                $response['status']=false;
                $response['message']="Kode Anggaran $nama sudah ada, gunakan Kode Lain";
            }
        } else{
            $response['status']=false;
            $response['message']="Kode Anggaran harus diisi";
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    function change_status(){
        $response=[];
        $id = $this->input->post('id',true);
        $value = $this->input->post('newValue',true);
        
        $this->db->where('id',$id)->update('mst_kode_anggaran',[
            'status'=>($value==1 ? 1:0),
            'updated'=>date('Y-m-d H:i:s')
        ]);
        
        $response['status']=true;
        $response['message']='Status telah diubah';
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}