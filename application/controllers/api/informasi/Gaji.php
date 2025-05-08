<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Gaji extends Group_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->helper("tanggal_helper");
        $this->folder_view = 'informasi/';
        $this->folder_model = 'informasi/';
        $this->folder_controller = 'informasi/';
        $this->load->model("api/informasi/m_gaji");
        $this->load->model("api/informasi/m_tabel_gaji");

        if(!in_array($this->id_group,[2,5])) {
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
            $list = $this->m_tabel_gaji->get_all($this->data_karyawan->np_karyawan);
            $no = 0;
            foreach ($list as $tampil) {
                $no++;
                $row = array();
                $row['no'] = $no;           
                $row['payslip'] = $tampil->nama_payslip;
                $row['id_payslip'] = $tampil->id_payslip_karyawan;
                
                $data[] = $row;
            }
                
            $this->response([
                'status'=>true,
                'message'=>'Tabel gaji',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
    
    public function index_post()
    {
        $data=[];
        $params=[];
        try {
            if(empty($this->post('id_payslip'))) {
                $this->response([
                    'status'=>false,
                    'message'=>"Data harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else {
                $id_payslip_karyawan = $this->post('id_payslip');
                $np     = $this->data_karyawan->np_karyawan;
                $gaji   = $this->m_gaji->select_payment_by_np($id_payslip_karyawan);
                
                $data["gaji"] = array();
                $data["total"]["Pendapatan"] = 0;
                $data["total"]["Potongan"] = 0;
                
                foreach($gaji as $rincian){
                    if(!isset($data["gaji"][$rincian["jenis"]])){
                        $data["gaji"][$rincian["jenis"]] = array();
                    }
                    $row = [
                        'jenis' => $rincian["jenis"],
                        'nama_slip' => $rincian['nama_slip'],
                        'amount' => $rincian["amount"],
                    ];
                    array_push($data["gaji"][$rincian["jenis"]],$row);
                    
                    if(in_array($rincian["jenis"],array("Pendapatan","Potongan"))){
                        $data["total"][$rincian["jenis"]] += (int)$rincian["amount"];
                    }
                }
                
                if(isset($data["gaji"]["Pendapatan"]) and isset($data["gaji"]["Potongan"])){
                    $num_rows = max(count($data["gaji"]["Pendapatan"]),count($data["gaji"]["Potongan"]));
                }
                else if(isset($data["gaji"]["Pendapatan"]) and !isset($data["gaji"]["Potongan"])){
                    $num_rows = count($data["gaji"]["Pendapatan"]);
                }
                else if(!isset($data["gaji"]["Pendapatan"]) and isset($data["gaji"]["Potongan"])){
                    $num_rows = count($data["gaji"]["Potongan"]);
                }
                
                if(isset($data["gaji"]["Pendapatan"])){
                    while(count($data["gaji"]["Pendapatan"])<$num_rows){
                        $data["gaji"]["Pendapatan"][count($data["gaji"]["Pendapatan"])] = [
                            "jenis" => "",
                            'nama_slip' => "",
                            "amount" => "",
                        ];
                    }
                }
                
                if(isset($data["gaji"]["Potongan"])){
                    while(count($data["gaji"]["Potongan"])<$num_rows){
                        $data["gaji"]["Potongan"][count($data["gaji"]["Potongan"])] = [
                            "jenis" => "",
                            'nama_slip' => "",
                            "amount" => "",
                        ];
                    }
                }

                $this->response([
                    'status'=>true,
                    'message'=>'Rincian gaji',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}

/* End of file payslip.php */
/* Location: ./application/controllers/informasi/payslip.php */