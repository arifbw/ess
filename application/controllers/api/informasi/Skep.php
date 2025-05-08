<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Skep extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("api/informasi/m_skep");
        $this->load->library("encrypt");
        $this->load->helper("akses_helper");
        $this->load->helper("tanggal_helper");
        $this->load->helper("fungsi_helper");
        
        if(!in_array($this->id_group,[5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }

    function index_get() {
        $data=[];
        $params=[];
        try {
            if(empty($this->get('np'))) {
                $this->response([
                    'status'=>false,
                    'message'=>"Data harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else {
                $np = $this->get('np');
                $list = $this->m_skep->get_all($np);
                $data = array();
                $no = 0;
                foreach ($list as $tampil) {
                    // START OF ENCRYPT NAMA FILE*/
                    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0987654321";
                    $rand_chars = "";
                    $length_rand_chars=rand(8,16);

                    while(strlen($rand_chars)<$length_rand_chars){
                        $rand_chars .= substr($chars,rand(0,strlen($chars)),1);
                    }

                    $plain_txt_1 = rand(1,10000)."|"."untuk_download"."|".$tampil->file1_skep."|".date('Y-m-d H:i:s')."|".$rand_chars;
                    $encrypted_txt_1 = $this->encrypt_decrypt('encrypt', $plain_txt_1);
                
                    $plain_txt_2 = rand(1,10000)."|"."untuk_download"."|".$tampil->file2_skep."|".date('Y-m-d H:i:s')."|".$rand_chars;
                    $encrypted_txt_2 = $this->encrypt_decrypt('encrypt', $plain_txt_2);
                    // END OF ENCRYPT NAMA FILE*/
                    
                    $no++;
                    $row = array();
                    $row['no'] = $no;           
                    $row['np'] = $tampil->np_karyawan;
                    $row['nama'] = $tampil->nama_karyawan;
                    $row['nomor_skep'] = $tampil->nomor_skep;
                    $row['tgl'] = tanggal_indonesia($tampil->aktif_tanggal_skep);
                    $file_umum = base_url().'uploads/skep_umum/'.$tampil->file_1_skep_encrypt; # heru ganti filenya arahkan ke _encrypt, 2021-04-08
                    // $file_individu = base_url().'uploads/skep_individu/'.$tampil->file_2_skep_encrypt; # heru ganti filenya arahkan ke _encrypt, 2021-04-08
                    $file_individu = base_url().'uploads/skep_home_individu/'.$tampil->file_2_skep_encrypt; # heru ganti path/dir ke symlink, 2021-05-27
                    $row['file_umum'] = $file_umum;
                    $row['file_umum_name'] = $tampil->file1_skep; # tambahan untuk ditampilkan nama file aslinya, 2021-04-09
                    $row['file_individu'] = $file_individu;
                    $row['file_individu_name'] = $tampil->file2_skep; # tambahan untuk ditampilkan nama file aslinya, 2021-04-09
                        
                    $data[] = $row;
                }

                $this->response([
                    'status'=>true,
                    'message'=>'Data Skep',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
        
    private function encrypt_decrypt($action, $string){
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'zxfajaSsd1fjDwASjA12SAGSHga3yus'.date('Ymd');
        $secret_iv = 'zxASsadkmjku4jLOIh2jfGda5'.date('Ymd');
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
            
            $pisah              = explode('|',$output);
            $datetime_request   = $pisah[3];
            /*
            $datetime_expired   = date('Y-m-d H:i:s',strtotime('+10 seconds',strtotime($datetime_request))); 

            $datetime_now       = date('Y-m-d H:i:s');
            
            if($datetime_now > $datetime_expired || !$datetime_request){
                $output = false;
            }   
            */
        }
        return $output;
    }
}
