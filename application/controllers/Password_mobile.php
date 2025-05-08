<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password_mobile extends CI_Controller {
    public function __construct(){
        parent::__construct();
        
        $meta = meta_data();
        
        foreach($meta as $key => $value){
            $this->data[$key] = $value;
        }
        
        $this->folder_view = 'password_mobile/';
        //$this->folder_model = 'sikesper/';
        //$this->folder_ajax_view = $this->folder_view.'ajax/';
        $this->akses = array();

        //$this->load->model($this->folder_model."/M_agenda");

        $this->data['success'] = "";
        $this->data['warning'] = "";
        $this->data["is_with_sidebar"] = true;
    }

    public function index(){
        $this->data['judul'] = "Password Mobile";
        $this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
        $this->akses = akses_helper($this->data['id_modul']);

        izin($this->akses["akses"]);

        $this->data["akses"] = $this->akses;
        $this->data["navigasi_menu"] = menu_helper();
        $this->data['content'] = $this->folder_view."form";

        $this->data['panel_tambah'] = "";
        $this->data['nama'] = "";
        $this->data['status'] = "";

        if($this->akses["tambah"]){
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
        $table_pengguna = 'usr_pengguna';
        $np = $this->input->post('np',true);
        
        if (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $this->input->post('password',true))) {
            if(strlen($this->input->post('password',true))<8){
                $this->session->set_flashdata('failed','Password minimal 8 digit');
                redirect('Password_mobile');
                exit;
            }
            
            $password = md5($this->input->post('password',true));
            $get = $this->db->where('no_pokok',$np)->get($table_pengguna);
            if($get->num_rows()==0){
                $this->db->insert($table_pengguna, [
                    'username'=>$np,
                    'no_pokok'=>$np,
                    'password'=>$password,
                    'status'=>1,
                    'waktu_daftar'=>date('Y-m-d H:i:s')
                ]);
            } else{
                $this->db->where('no_pokok',$np)->update($table_pengguna, [
                    'password'=>$password,
                    'last_change_password'=>date('Y-m-d H:i:s')
                ]);
            }
            
            if($this->db->affected_rows()>0){
                $this->session->set_flashdata('success','Password telah disimpan');
            } else{
                $this->session->set_flashdata('failed','Error');
            }
            
            redirect('password_mobile');
        } else{
            $this->session->set_flashdata('failed','Password harus kombinasi huruf dan angka');
            redirect('Password_mobile');
        }
    }
}
?>