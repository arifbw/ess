<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_sto extends CI_Controller {    
	function __construct() {
		parent::__construct();
		$this->load->model('outbound_sap/M_outbound_sto');
		$this->folder_sto	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_sap/MasterSTO/";
	}
	
	public function index() {
		//redirect(base_url('dashboard'));
	}
    
    function get_files(){
        $msc = microtime(true);
        $ignored = array('.', '..', '.svn', '.htaccess');
        
        $data_files = array();
        echo '<br>Scanning dir '.$this->folder_sto.' ...<br><br>';
        echo 'Start : '.date('Y-m-d H:i:s').'<br>';
        if(is_dir($this->folder_sto)){
            foreach (scandir($this->folder_sto) as $file) {
                if (in_array($file, $ignored)) continue;
                $data_files = [
                    'nama_file'=>$file,
                    'size'=>filesize($this->folder_sto.$file),
                    'last_modified'=>date('Y-m-d H:i:s', filemtime($this->folder_sto.$file)),
                    'baris_data'=>$this->count_rows($this->folder_sto.$file)
                ];
                
                $this->M_outbound_sto->check_name_then_insert_data($file, $data_files);
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
        $get_proses_is_noll = $this->M_outbound_sto->get_proses_is_nol()->result();
        foreach($get_proses_is_noll as $row){
			$this->db->truncate('ess_sto'); //kosongkan tabel
			echo "<br><br>".$this->db->last_query();
			$this->db->truncate('mst_satuan_kerja'); //kosongkan tabel
            if(is_file($this->folder_sto.$row->nama_file)){
                $this->read_process($row->nama_file);
				
				$ada_yang_diproses = true;
            }
        }
        $msc = microtime(true)-$msc;
        
		$this->M_outbound_sto->update_satuan_kerja();
		$this->M_outbound_sto->update_jabatan();
		
        echo "Done. Execution time: $msc seconds.<br>Inserted to database.";
        
		if($ada_yang_diproses == true)
		{
			//insert ke tabel 'ess_status_proses_input', id proses = 5
			$this->db->insert('ess_status_proses_input', ['id_proses'=>5, 'waktu'=>date('Y-m-d H:i:s')]);			
		}
    }
    
    function read_process($file){
        //echo "<br>".$file."<br><br>";

        $rows = explode("\n",trim(file_get_contents($this->folder_sto.$file)));
        
        $num_rows = 0;
        $count_inserted = 0;

		echo "<hr>".$file;
		
        //parsing data di file .txt
        foreach($rows as $row){
			echo "<br><br>".$row;
            if(!empty(trim($row))){
                $num_rows+=1;
                $pisah = explode("\t",trim($row));
                
				if(strcmp(substr($pisah[6],0,strlen($pisah[6])/2),substr($pisah[6],strlen($pisah[6])/2))==0){
					$pisah[6] = substr($pisah[6],0,strlen($pisah[6])/2);
				}
				
                //need to be check !!!
                $insert_data = array(	
					'sequence'  	    	=> @$pisah[0],
                    'level'     	    	=> @$pisah[1],
                    'object_type'	    	=> @$pisah[2],
					'object_id'	        	=> @$pisah[3],
					'object_abbreviation'	=> @$pisah[4],
                    'object_name'	    	=> preg_replace("/\s+/"," ",@$pisah[5]),
					'object_name_lengkap'	=> preg_replace("/\s+/"," ",@$pisah[6]),
                    'down'      	    	=> @$pisah[7],
                    'up'          	    	=> @$pisah[8],
                    'next'         	    	=> @$pisah[9],
                    'previous'     	    	=> @$pisah[10],
                    'relationship_spec' 	=> @$pisah[11],
					'relationship_id' 		=> @$pisah[12],
					'start_date' 			=> @$pisah[13]
                );
                
				/* 
				//insert yang object type nya O / organization
				if($pisah[2]=='O')
				{					
					$insert_satuan_kerja = array(	
						'kode_unit'	=> @$pisah[4],
						'nama_unit'	=> @$pisah[6]
					);
					
					$this->M_outbound_sto->check_id_then_insert_satuan_kerja($insert_satuan_kerja);
				} */
				
                if($this->M_outbound_sto->check_id_then_insert_data($insert_data)==true){
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
        
        $this->M_outbound_sto->update_files($file, $update_file);
    }
	
	public function get_sto(){
		$this->get_files();
		$this->get_data();
	}
    
}
