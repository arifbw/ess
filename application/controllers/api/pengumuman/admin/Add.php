<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Add extends MY_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function index_post(){
        $data_insert = [];
        
        if(empty($this->post('judul'))){
            $this->response([
                'status'=>false,
                'message'=>"Judul harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else if(empty($this->post('pesan'))){
            $this->response([
                'status'=>false,
                'message'=>"Pesan harus diisi",
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        } else{
            $created_by_np = $this->data_karyawan->np_karyawan;
            $new_id = $this->uuid->v4();
            $data = $this->post();
            $data['kode'] = $new_id;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by_np'] = $created_by_np;
            $data['publish_at'] = date('Y-m-d H:i:s');
            $this->db->insert('mobile_pengumuman', $data);
            
            if( $this->db->affected_rows()>0 ){
                $this->db->query("INSERT INTO mobile_pengumuman_delivered (np, mobile_pengumuman_kode, created_at)
                                SELECT no_pokok, '$new_id', NOW()
                                FROM usr_pengguna 
                                WHERE no_pokok IN (SELECT no_pokok FROM mst_karyawan)");
                
                # broadcast notification to user
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS =>'{
                        "notification": {
                            "title": "'.$this->post('judul').'",
                            "body": "'.$this->post('pesan').'",
                            "click_action": "FLUTTER_NOTIFICATION_CLICK"
                        },
                        "priority": "high",
                        "data": {
                            "type" : "broadcast_message",
                        },
                        "to": "/topics/broadcast_message"
                    }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: key=AAAAK1KRpvw:APA91bHL2jhnA3FbzRDzIbyQRjxpmqkDPUClO5XxxW1WABAy1_WzfZeV43L71AGo_QDBB0dR-j3QG8fLcB0GiaV94WUeoUP5wf99REFPfpqrxPcxJdKZdwxIjCUuZY-WIvcYrYN2THkm'
                    ),
                ));
                curl_exec($curl);
                curl_close($curl);
                # END of broadcast notification to user
            }
            
            $this->response([
                'status'=>true,
                'message'=>'Pengumuman telah dibuat.',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        }
    }
}
