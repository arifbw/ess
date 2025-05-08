<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_absence_quota extends CI_Controller {    
	function __construct() {
		parent::__construct();
		$this->load->model('outbound_sap/M_outbound_absence_quota');
		$this->folder_absence_quota	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_sap/AbsenceQuota/";
	}
	
	public function index() {
		//redirect(base_url('dashboard'));
	}
    
    function get_files(){
        $msc = microtime(true);
        $ignored = array('.', '..', '.svn', '.htaccess');
        
        $data_files = array();
        echo '<br>Scanning dir '.$this->folder_absence_quota.' ...<br><br>';
        echo 'Start : '.date('Y-m-d H:i:s').'<br>';
        if(is_dir($this->folder_absence_quota)){
            foreach (scandir($this->folder_absence_quota) as $file) {
                if (in_array($file, $ignored)) continue;
                $data_files = [
                    'nama_file'=>$file,
                    'size'=>filesize($this->folder_absence_quota.$file),
                    'last_modified'=>date('Y-m-d H:i:s', filemtime($this->folder_absence_quota.$file)),
                    'baris_data'=>$this->count_rows($this->folder_absence_quota.$file)
                ];
                
                $this->M_outbound_absence_quota->check_name_then_insert_data($file, $data_files);
            }
            $msc = microtime(true)-$msc;
            echo "Done. Execution time: $msc seconds.<br>Inserted to database.";
            
        } else{
            echo 'Dir not found!';
        }
    }
    
    function count_rows($file_name){
        $rows = explode("\n",trim(file_get_contents($file_name)));
        return count($rows) - 1;
    }
    
    function get_data($date_input=null) {
        $msc = microtime(true);
        echo '<br>Scanning files...<br>';
        echo 'Start : '.date('Y-m-d H:i:s').'<br><br>';
        
        set_time_limit('0');
        
		$ada_yang_diproses = false;
		
        //get files that process=0
        $get_proses_is_noll = $this->M_outbound_absence_quota->get_proses_is_nol()->result();
        foreach($get_proses_is_noll as $row){
			$this->db->truncate('erp_absence_quota'); //kosongkan tabel
            if(is_file($this->folder_absence_quota.$row->nama_file)){
                $this->read_process($row->nama_file);
				
				$ada_yang_diproses = true;
            }
        }
        $msc = microtime(true)-$msc;
        
        echo "Done. Execution time: $msc seconds.<br>Inserted to database.";
        
		if($ada_yang_diproses == true)
		{	
			//masukan data ke tabel monitoring
			//insert ke tabel 'ess_status_proses_input', id proses = 3
			$this->db->insert('ess_status_proses_input', ['id_proses'=>3, 'waktu'=>date('Y-m-d H:i:s')]);
		}
       
    }
    
    function read_process($file){
        //echo "<br>".$file."<br><br>";

        $rows = explode("\n",trim(file_get_contents($this->folder_absence_quota.$file)));
        
        $num_rows = 0;
        $count_inserted = 0;

        //parsing data di file .txt
        foreach($rows as $row){
            if(!empty(trim($row))){
                $num_rows+=1;
                if($num_rows>1){
                    $pisah = explode("\t",trim($row));

                    //need to be check !!!
                    $insert_data = array(	
                        'np_karyawan'       => @$pisah[0],
                        'personel_number'   => @$pisah[1],
                        'nama'              => @$pisah[2],
                        'start_date'	    => substr(@$pisah[3], 0,4).'-'.substr(@$pisah[3], 4,2).'-'.substr(@$pisah[3], 6,2),
                        'end_date'  	    => substr(@$pisah[4], 0,4).'-'.substr(@$pisah[4], 4,2).'-'.substr(@$pisah[4], 6,2),
                        'absence_quota_type'=> @$pisah[5],
                        'deduction_from'	=> substr(@$pisah[6], 0,4).'-'.substr(@$pisah[6], 4,2).'-'.substr(@$pisah[6], 6,2),
                        'deduction_to'      => substr(@$pisah[7], 0,4).'-'.substr(@$pisah[7], 4,2).'-'.substr(@$pisah[7], 6,2),
                        'number'          	=> @$pisah[8],
                        'deduction'         => @$pisah[9]
                    );

                    if($this->M_outbound_absence_quota->check_id_then_insert_data($pisah[0], $pisah[3], $pisah[4], $pisah[6], $pisah[7], $insert_data)==true){
                        $count_inserted+=1;
                    }
                }
            }
        }
        $proses = ($count_inserted==($num_rows - 1) ? '1':'0');
        $update_file = array(
            'proses'			=> $proses,
            'baris_data'        => $num_rows - 1,
            'waktu_proses'		=> date('Y-m-d H:i:s')
        );
        
        $this->M_outbound_absence_quota->update_files($file, $update_file);
    }
    
	public function get_absence_quota(){
		$this->get_files();
		$this->get_data();
	}
}
