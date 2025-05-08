<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proses_delete extends CI_Controller {
	
	 
	function __construct() {
		parent::__construct();
		$this->load->model('backup/M_proses_delete');
		$this->load->helper(['fungsi_helper','tanggal_helper']);
		
	}
    
    public function index() {
		redirect(base_url('dashboard'));
	}
    
    function proses($date=null){
        
        set_time_limit('0');
        
        if($date==null){
            echo 'Butuh parameter: (Y-m)';
        } else{
            if($date!='today' && validateMonth($date)==false){
                echo 'Bulan tidak valid.';
                exit();
            }
            
            if($date=='today'){
                $date_proses = date("Y-m", strtotime("-3 months"));
            } else{
                $date_proses = $date;
            }
            
            $tahun_bulan = str_replace('-','_',$date_proses);
            
            $return = [];
            
            $arr_tables = [
                ['table_name'=>"ess_cico_$tahun_bulan", 'action'=>'delete tabel', 'month'=>$date_proses, 'function'=>'drop_table'],
                ['table_name'=>"ess_perizinan_$tahun_bulan", 'action'=>'delete tabel', 'month'=>$date_proses, 'function'=>'drop_table'],
                ['table_name'=>"pamlek_data_$tahun_bulan", 'action'=>'delete tabel', 'month'=>$date_proses, 'function'=>'drop_table'],
                ['table_name'=>"erp_master_data_$tahun_bulan", 'action'=>'delete tabel', 'month'=>$date_proses, 'function'=>'drop_table'],
                ['table_name'=>"ess_lembur_transaksi", 'action'=>'delete data', 'month'=>$date_proses, 'function'=>'delete_data_lembur'],
                ['table_name'=>"ess_sppd", 'action'=>'delete data', 'month'=>$date_proses, 'function'=>'delete_data_sppd']
            ];
            
            foreach($arr_tables as $row){
                $status = $this->proses_tabel($row)['status'];
                $return[] = [
                    'table_name'=>$row['table_name'],
                    'action'=>$row['action'],
                    'status'=>$status
                ];
            }
            
            //insert ke tabel 'ess_status_proses_input', id proses = 14
            $this->db->insert('ess_status_proses_input', ['id_proses'=>14, 'waktu'=>date('Y-m-d H:i:s')]);
            
            echo json_encode($return);
        }
        
    }
    
    function proses_tabel($row){
        $return = [];
        if(check_table_exist($row['table_name'])=='ada'){
            $proses = $this->M_proses_delete->$row['function']($row);
            $return['status'] = $proses['ket'];
        } else{
            $return['status'] = 'tabel tidak ada';
        }
        
        return $return;
    }
	
	
}
