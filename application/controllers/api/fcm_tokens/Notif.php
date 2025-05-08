<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notif extends CI_Controller {
    
    function __construct(){
        parent::__construct();
    }
    
    function send(){
        //echo (is_callable('curl_init')) ? '<h1>Enabled</h1>' : '<h1>Not enabled</h1>' ;
        //exit;
        # first, karyawan from tasklist
        $data_get1=[];
        $get1 = $this->db->select("a.created_by_np as np, a.created_by_nama as nama, GROUP_CONCAT(CONCAT(a.task_name,' (',a.description, ')') SEPARATOR '<br>') as tasks, b.fcm_token")
            ->where('a.task_type','task')
            ->group_start()
                ->where("(DATE('".date('Y-m-d')."') BETWEEN a.start_date AND (CASE WHEN a.end_date_fix IS NOT NULL THEN a.end_date_fix ELSE a.end_date END))")
                ->or_where('a.progress<',100)
                ->or_where('a.progress is null',null,false)
                ->or_where('DATE(a.updated_at)',date('Y-m-d'))
            ->group_end()
            ->where( 'a.deleted_at is null',null,false )
            ->join('mobile_fcm_tokens b','a.created_by_np=b.np')
            ->group_by('a.created_by_np')
            ->get('ess_project_tasklists a')
            ->result();
        foreach($get1 as $row){
            $tasks = [];
            $explode_tasks = explode('<br>',$row->tasks);
            foreach($explode_tasks as $t){
                $tasks[] = [
                    'title'=>$t
                ];
            }
            /*$data_get1[] = [
                'np'=>$row->np,
                'nama'=>$row->nama,
                'tasks'=>$tasks,
                'fcm_token'=>$row->fcm_token
            ];*/
            $data_get1[] = [
                'notification'=>[
                    'title'=>'Judul Notifikasi',
                    'body'=>'Deskripsi Notifikasi',
                    'click_action'=>'FLUTTER_NOTIFICATION_CLICK',
                ],
                'data'=>$tasks,
                'to'=>$row->fcm_token
            ];
        }
        
        # second, karyawan belum clock out
        $get2 = $this->db->select('a.np_karyawan as np, a.nama, a.is_dinas_luar, a.tapping_fix_approval_status as status_approval, a.tapping_fix_approval_alasan as alasan_approval, a.tapping_fix_approval_np as np_approval
        , (CASE WHEN a.tapping_fix_1 is null THEN DATE_FORMAT(a.tapping_fix_1_temp, "%H:%i:%s") ELSE DATE_FORMAT(a.tapping_fix_1, "%H:%i:%s") END) as clock_in
        , (CASE WHEN a.tapping_fix_2 is null THEN DATE_FORMAT(a.tapping_fix_2_temp, "%H:%i:%s") ELSE DATE_FORMAT(a.tapping_fix_2, "%H:%i:%s") END) as clock_out, b.fcm_token')
            ->where('a.dws_tanggal',date('Y-m-d'))
            ->where('((CASE WHEN a.tapping_fix_1 is null THEN DATE_FORMAT(a.tapping_fix_1_temp, "%H:%i:%s") ELSE DATE_FORMAT(a.tapping_fix_1, "%H:%i:%s") END) IS NOT NULL)')
            ->where('((CASE WHEN a.tapping_fix_2 is null THEN DATE_FORMAT(a.tapping_fix_2_temp, "%H:%i:%s") ELSE DATE_FORMAT(a.tapping_fix_2, "%H:%i:%s") END) IS NULL)')
            ->join('mobile_fcm_tokens b','a.np_karyawan=b.np')
            ->get('ess_cico_'.date('Y_m').' a')
            ->result();
        foreach($get2 as $row){
            if( (int)array_search($row->np, array_column($data_get1, 'np'))>=0 ){
                continue;
            } else{
                /*$data_get1[] = [
                    'np'=>$row->np,
                    'nama'=>$row->nama,
                    'tasks'=>[],
                    'fcm_token'=>$row->fcm_token
                ];*/
                $data_get1[] = [
                    'notification'=>[
                        'title'=>'Judul Notifikasi',
                        'body'=>'Deskripsi Notifikasi',
                        'click_action'=>'FLUTTER_NOTIFICATION_CLICK',
                    ],
                    'data'=>[],
                    'to'=>$row->fcm_token
                ];
            }
        }
        
        foreach($data_get1 as $row){
            $this->curPostRequest($row);
        }
        
        header('Content-type: application/json');
        echo json_encode($data_get1);
    }
    
    public function curPostRequest($_data){
        /* Endpoint */
        $url = 'https://fcm.googleapis.com/fcm/send';
   
        /* eCurl */
        $curl = curl_init($url);
   
        /* Data */
        $data = $_data;
   
        /* Set JSON data to POST */
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
        /* Define content type */
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization: key=AAAAK1KRpvw:APA91bHL2jhnA3FbzRDzIbyQRjxpmqkDPUClO5XxxW1WABAy1_WzfZeV43L71AGo_QDBB0dR-j3QG8fLcB0GiaV94WUeoUP5wf99REFPfpqrxPcxJdKZdwxIjCUuZY-WIvcYrYN2THkm'
        ));
            
        /* Return json */
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
        /* make request */
        $result = curl_exec($curl);
             
        /* close curl */
        curl_close($curl);
    }
}
