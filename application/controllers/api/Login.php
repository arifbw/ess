<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("m_login");
        $this->load->model("api/m_login_api");
    }
    
	function index_post() {        
        $data = [];
        $username = $this->input->post('username');
        $password = md5($this->input->post('password'));
        header('Content-type: application/json');
        try {
            header('Content-Type: application/json');
            $get = $this->m_login_api->auth($username, $password);

            if($get->num_rows() > 0){
                $row = $get->row();
                $key = hash('sha256','ess-'.date('Ymd').'-'.$row->id);

                $_token = $this->m_login_api->_token($row->id);
                if($_token->num_rows() == 1){
                    $_token_row = $_token->row();
                    $this->m_login_api->update_token($row->id,$key);
                } else{
                    $this->m_login_api->insert_token(['user_id'=>$row->id,'key'=>$key,'date_created'=>date('Y-m-d H:i:s')]);
                }
                
                $data["status"] = true;
                $data["id"] = $row->id;
                $data["username"] = $row->username;
                $data["no_pokok"] = $row->no_pokok;
                
                # data karyawan
                //$karyawan = $this->m_login->ambil_data_karyawan($row->no_pokok);
                # heru PDS ganti query ambil data karyawan, 2021-02-12
                if(check_table_exist('erp_master_data_'.date('Y_m'))=='ada'){
                    $karyawan = $this->db->select('nama, kode_unit, nama_unit, kode_jabatan, nama_jabatan')
                        ->where('np_karyawan',$row->no_pokok)
                        ->order_by('tanggal_dws','DESC')
                        ->from('erp_master_data_'.date('Y_m'))
                        ->limit(1)->get()->row_array();
                } else{
                    $karyawan = $this->db->select('nama, kode_unit, nama_unit, kode_jabatan, nama_jabatan')
                        ->where('no_pokok',$row->no_pokok)
                        ->from('mst_karyawan')
                        ->get()->row_array();
                }
                $data['nama'] = $karyawan["nama"];
                $data["kode_unit"] = $karyawan["kode_unit"];
                $data["nama_unit"] = $karyawan["nama_unit"];
                $data["kode_jabatan"] = $karyawan["kode_jabatan"];
                $data["nama_jabatan"] = $karyawan["nama_jabatan"];
                
                # foto
                $foto_profile = base_url("foto/profile/".$this->m_login->ambil_foto_karyawan($row->no_pokok));
                $data["foto_profile"] = $foto_profile;
                
                # group
                $grup = $this->m_login->ambil_grup($row->id);
                $data["group"] = $grup;
                
                # menu
                /*$menu = [];
                foreach($grup as $g){
                    $menu[] = [
                        'id_group' => $g['id'],
                        'list_menu'=>menu_helper_mobile($g['id'])
                    ];
                }
                $data['menu'] = $menu;*/
                
                # pengadministrasi
                $list_pengadministrasi = $this->m_login->list_pengadministrasi($row->id);
                $data["list_pengadministrasi"] = $list_pengadministrasi;

                # insert log
				insert_login_history([
					'file' => __FILE__ . '/' . __FUNCTION__,
					'description' => 'success',
					'input_from' => 'mobile',
					'np' => $row->no_pokok,
					'user_id' => $row->id
				]);
				# END: insert log

                echo json_encode([
                    'status'=>true,
                    'message'=>'Login success',
                    'token'=>$key,
                    'data'=>$data
                ]);
            } else{
                # insert log
				insert_login_history([
					'file' => __FILE__ . '/' . __FUNCTION__,
					'description' => 'failed',
					'input_from' => 'mobile',
					'username' => $username
				]);
				# END: insert log

                echo json_encode([
                    'status'=>false,
                    'message'=>'User not found',
                    'data'=>[]
                ]);
            }
        } catch(Exception $e) {
            echo json_encode([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ]);
        }
	}
}
