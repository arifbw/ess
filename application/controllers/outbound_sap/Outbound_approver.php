<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_approver extends CI_Controller {    
	function __construct() {
		parent::__construct();
		$this->load->model('outbound_sap/M_outbound_approver');
		$this->folder_approver	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_sap/Approver/";
	}
	
	public function index() {
		//redirect(base_url('dashboard'));
	}
    
    function get_files(){
        $msc = microtime(true);
        $ignored = array('.', '..', '.svn', '.htaccess');
        
        $data_files = array();
        echo '<br>Scanning dir '.$this->folder_approver.' ...<br><br>';
        echo 'Start : '.date('Y-m-d H:i:s').'<br>';
        if(is_dir($this->folder_approver)){
            foreach (scandir($this->folder_approver) as $file) {
                if (in_array($file, $ignored)) continue;
                $data_files = [
                    'nama_file'=>$file,
                    'size'=>filesize($this->folder_approver.$file),
                    'last_modified'=>date('Y-m-d H:i:s', filemtime($this->folder_approver.$file)),
                    'baris_data'=>$this->count_rows($this->folder_approver.$file)
                ];
                $this->M_outbound_approver->check_name_then_insert_data($file, $data_files);
            }
            $msc = microtime(true)-$msc;
            echo "Done. Execution time: $msc seconds.<br>Inserted to database.";
            
        } else{
            echo 'Dir not found!';
        }
    }
    
    function count_rows($file_name){
        $rows = explode("\n",trim(file_get_contents($file_name)));
        return count($rows);
    }
    
    function get_data($date_input=null) {
        $msc = microtime(true);
        echo '<br>Scanning files...<br>';
        echo 'Start : '.date('Y-m-d H:i:s').'<br><br>';
        
        set_time_limit('0');
        
		$ada_yang_diproses = false;
				
        //get files that process=0
        $get_proses_is_noll = $this->M_outbound_approver->get_proses_is_nol()->result();
        foreach($get_proses_is_noll as $row){
			$this->db->truncate('ess_approver'); //kosongkan tabel
            if(is_file($this->folder_approver.$row->nama_file)){
                $this->read_process($row->nama_file);
				
				$ada_yang_diproses = true;
            }
        }
        $msc = microtime(true)-$msc;
        
        echo "Done. Execution time: $msc seconds.<br>Inserted to database.";
        
		if($ada_yang_diproses == true)
		{
			//insert ke tabel 'ess_status_proses_input', id proses = 4
			$this->db->insert('ess_status_proses_input', ['id_proses'=>4, 'waktu'=>date('Y-m-d H:i:s')]);				
		}
    }
    
    function read_process($file){
        //echo "<br>".$file."<br><br>";

        $rows = explode("\n",trim(file_get_contents($this->folder_approver.$file)));
        
        $num_rows = 0;
        $count_inserted = 0;
        $row_first = 0;

        //parsing data di file .txt
        foreach($rows as $row){
        	if ($row_first == 0) {
        		$row_first++;
        		continue;
        	}

            if(!empty(trim($row))){
                $num_rows+=1;
                $pisah = explode("\t",trim($row));
                
                //need to be check !!!
                $insert_data = array(	
                    'np_karyawan'       => @$pisah[0],
                    'personel_number'   => @$pisah[1],
                    'nama_karyawan'     => @$pisah[2],
                    'np_approver_1'     => @$pisah[3],
                    'nama_approver_1'   => @$pisah[4],
                    'np_approver_2'     => @$pisah[5],
                    'nama_approver_2'   => @$pisah[6],
                    'np_approver_3'     => @$pisah[7],
                    'nama_approver_3'   => @$pisah[8]
                );
                if($this->M_outbound_approver->check_id_then_insert_data($pisah[0], $insert_data)==true){
                // if($this->M_outbound_approver->check_id_then_insert_data($pisah[0], $pisah[3], $pisah[5], $pisah[7], $insert_data)==true){
                    $count_inserted+=1;
                }
            }
        }
        $proses = ($count_inserted==$num_rows ? '1':'0');
        $update_file = array(
            'proses'			=> $proses,
            'baris_data'        => $num_rows,
            'waktu_proses'		=> date('Y-m-d H:i:s')
        );
        
        $this->M_outbound_approver->update_files($file, $update_file);
    }
	
	public function get_approver(){
		$this->get_files();
		$this->get_data();
	}
    
}
