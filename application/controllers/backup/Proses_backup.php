<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proses_backup extends CI_Controller {
	
	 
	function __construct() {
		parent::__construct();
		$this->load->model('backup/M_proses_delete');
		$this->load->helper(['fungsi_helper','tanggal_helper']);
		
	}
    
    public function index() {
		redirect(base_url('dashboard'));
	}
    
    function insert_status_proses($date=null){
        
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
            
            //insert ke tabel 'ess_status_proses_input', id proses = 15
            $this->db->insert('ess_status_proses_input', ['id_proses'=>15, 'waktu'=>date('Y-m-d H:i:s')]);
            
        }
        
    }
    
    function get_file(){
        $this->load->dbutil();
        $prefs = array(     
            'format'      => 'zip',             
            'filename'    => 'ess_db_backup.sql'
            );

        $backup =& $this->dbutil->backup($prefs); 

        $db_name = 'ess_db_backup_'. date("Y_m_d_H_i_s") .'.zip';
        $save = '.asset/'.$db_name;

        $this->load->helper('file');
        //write_file($save, $backup); 

        $this->load->helper('download');
        force_download($db_name, $backup);
    }
	
	
}
