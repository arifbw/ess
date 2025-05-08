<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_klaimkacamata extends CI_Controller {    
	function __construct() {
		parent::__construct();
		$this->load->model('outbound_dwh/M_outbound_klaimkacamata');
		$this->folder_kacamata	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_dwh/sikesper/dwh_hr_hcm_klaimkacamata/";
        $this->table_to_insert = 'ess_riwayat_kacamata';
	}
    
    function get_files(){
        try{
            $msc = microtime(true);
            $ignored = array('.', '..', '.svn', '.htaccess');
            
            $data_files = array();
            # echo '<br>Scanning dir '.$this->folder_kacamata.' ...<br><br>';
            # echo 'Start : '.date('Y-m-d H:i:s').'<br>';
            if(is_dir($this->folder_kacamata)){
                foreach (scandir($this->folder_kacamata) as $file) {
                    if (in_array($file, $ignored)) continue;
                    $data_files = [
                        'nama_file'=>$file,
                        'size'=>filesize($this->folder_kacamata.$file),
                        'last_modified'=>date('Y-m-d H:i:s', filemtime($this->folder_kacamata.$file)),
                        'baris_data'=>$this->count_rows($this->folder_kacamata.$file)
                    ];
                    
                    $this->M_outbound_klaimkacamata->check_name_then_insert_data($file, $data_files);
                }
                $msc = microtime(true)-$msc;
                echo "Done. Execution time: $msc seconds. Files inserted to database.";
                
            } else{
                echo 'Dir not found!';
            }
            
        } catch(Exception $e){
            echo 'Error Exception '.$e->getMessage();
        }
    }
    
    function count_rows($file_name){
        $rows = explode("\n",trim(file_get_contents($file_name)));
        return count($rows) - 1;
    }
    
    function get_data() {
        $msc = microtime(true);
        # echo '<br>Scanning files...<br>';
        # echo 'Start : '.date('Y-m-d H:i:s').'<br><br>';
        
        //set_time_limit('0');
        ini_set('max_execution_time', '0');
		$ada_yang_diproses = false;
		
        # get files that process=0
        $get_proses_is_nol = $this->M_outbound_klaimkacamata->get_proses_is_nol()->result();
        foreach($get_proses_is_nol as $row){
            if(is_file($this->folder_kacamata.$row->nama_file)){
                //$this->db->truncate($this->table_to_insert); //kosongkan tabel
                $this->read_process($row->nama_file);
				$ada_yang_diproses = true;
            }
        }
        $msc = microtime(true)-$msc;
        //echo "Done. Execution time: $msc seconds. Inserted to database.";
        
		/*if($ada_yang_diproses == true) {
			# masukan data ke tabel monitoring
			# insert ke tabel 'ess_status_proses_input', id proses = 3
			$this->db->insert('ess_status_proses_input', ['id_proses'=>3, 'waktu'=>date('Y-m-d H:i:s')]);
		}*/
    }
    
    function read_process($file){
        ini_set('max_execution_time', '0');
        $msc = microtime(true);
        $handle = fopen($this->folder_kacamata.$file, "r");
        
        $count = 0;
        $count_temp = 0;
        $array_temp = [];
        $count_success = 0;
        while (($row = fgetcsv($handle,0,'|'))) {
            $count++;
            if ($count == 1) continue;
            
            try{
                $item = [
                    'personal_number' => addslashes((@$row[0]!=''?$row[0]:null)),
                    'np_karyawan' => addslashes((@$row[1]!=''?$row[1]:null)),
                    'nama_karyawan' => addslashes((@$row[2]!=''?$row[2]:null)),
                    'tt' => addslashes((@$row[3]!=''?$row[3]:null)),
                    'task_type' => addslashes((@$row[4]!=''?$row[4]:null)),
                    'task_on' => addslashes((@$row[5]!=''?$row[5]:null)),
                    //'task_claim' => addslashes((@$row[23]!=''?$row[23]:null)),
                    'nama_keluarga' => addslashes((@$row[7]!=''?$row[7]:null)),
                    'updated' => date('Y-m-d H:i:s')
                ];
                
                $cekExist = $this->db
                    ->where('np_karyawan',addslashes(@$row[1]))
                    ->where('tt',addslashes(@$row[3]))
                    ->where('task_on',addslashes(@$row[5]))
                    ->get($this->table_to_insert);
                
                if($cekExist->num_rows()==0){
                    /*if($count_temp<1000){
                        $count_temp++;
                    } else{
                        $this->db->insert_batch($this->table_to_insert, $array_temp);
                        $count_temp = 0;
                        $array_temp = [];
                    } 

                    $array_temp[] = $item;*/
                    $this->db->insert($this->table_to_insert, $item);
                } else{
                    $this->db
                        ->where('np_karyawan',addslashes(@$row[1]))
                        ->where('tt',addslashes(@$row[3]))
                        ->where('task_on',addslashes(@$row[5]))
                        ->update($this->table_to_insert, $item);
                }
                
            } catch(Exception $e){
                continue;
            }
        }
        
        # insert last array_temp
        if($array_temp!=[]){
            $this->db->insert_batch($this->table_to_insert, $array_temp);
            $array_temp = [];
        }
        
        $this->db->where('task_on','0000-00-00')->update($this->table_to_insert, ['task_on'=>null]);
        $msc = microtime(true)-$msc;
        
        $update_file = array(
            'proses' => 1,
            'waktu_proses' => date('Y-m-d H:i:s'),
            'baris_data_success' => null,
            'execution_time_second' => $msc
        );
        $this->M_outbound_klaimkacamata->update_files($file, $update_file);
        # echo 'Done';
    }
    
	public function get_kacamata(){
		$this->get_files();
		$this->get_data();
	}
}
