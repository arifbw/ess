<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_donor_darah_batch extends CI_Controller {    
	function __construct() {
		parent::__construct();
		$this->load->model('outbound_dwh/M_outbound_donor_darah');
		$this->folder_donor_darah	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_dwh/sikesper/dwh_hr_hcm_donor_darah/";
        $this->table_to_insert = 'ess_donor_darah';
	}
    
    function get_files(){
        try{
            $msc = microtime(true);
            $ignored = array('.', '..', '.svn', '.htaccess');
            
            $data_files = array();
            if(is_dir($this->folder_donor_darah)){
                foreach (scandir($this->folder_donor_darah) as $file) {
                    if (in_array($file, $ignored)) continue;
                    $data_files = [
                        'nama_file'=>$file,
                        'size'=>filesize($this->folder_donor_darah.$file),
                        'last_modified'=>date('Y-m-d H:i:s', filemtime($this->folder_donor_darah.$file)),
                        'baris_data'=>$this->count_rows($this->folder_donor_darah.$file)
                    ];
                    
                    $this->M_outbound_donor_darah->check_name_then_insert_data($file, $data_files);
                }
                $msc = microtime(true)-$msc;
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
        
        ini_set('max_execution_time', '0');
		$ada_yang_diproses = false;
		
        # get files that process=0
        $get_proses_is_nol = $this->M_outbound_donor_darah->get_proses_is_nol()->result();
        foreach($get_proses_is_nol as $row){
            if(is_file($this->folder_donor_darah.$row->nama_file)){
                $this->db->truncate($this->table_to_insert); //kosongkan tabel
                $this->read_process($row->nama_file);
				$ada_yang_diproses = true;
            }
        }
        $msc = microtime(true)-$msc;
    }
    
    function read_process($file){
        ini_set('max_execution_time', '0');
        $msc = microtime(true);
        $handle = fopen($this->folder_donor_darah.$file, "r");
        
        $count = 0;
        $count_temp = 0;
        $array_temp = [];
        $count_success = 0;
        while (($row = fgetcsv($handle,0,'|'))) {
            $count++;
            if ($count == 1) continue;
            
            try{
                $item = [
                    'np_karyawan' => addslashes((@$row[1]!=''?$row[1]:null)),
                    'personal_number' => addslashes((@$row[0]!=''?$row[0]:null)),
                    'nama_pegawai' => addslashes((@$row[2]!=''?$row[2]:null)),
                    'kode_position' => addslashes((@$row[3]!=''?$row[3]:null)),
                    'position' => addslashes((@$row[5]!=''?$row[5]:null)),
                    'kode_unit' => addslashes((@$row[22]!=''?$row[22]:null)),
                    'nama_unit' => addslashes((@$row[23]!=''?$row[23]:null)),
                    'type' => addslashes((@$row[27]!=''?$row[27]:null)),
                    'examination_type' => addslashes((@$row[24]!=''?$row[24]:null)),
                    'diagnosa' => null,
                    'exam_date' => addslashes((@$row[25]!=''?$row[25]:null)),
                    'last_exam' => addslashes((@$row[26]!=''?$row[26]:null)),
                    'value' => addslashes((@$row[28]!=''?$row[28]:null)),
                    'updated' => date('Y-m-d H:i:s')
                ];
                
                if($count_temp<1000){
                    $count_temp++;
                } else{
                    $this->db->insert_batch($this->table_to_insert, $array_temp);
                    $count_temp = 0;
                    $array_temp = [];
                } 

                $array_temp[] = $item;
                
            } catch(Exception $e){
                continue;
            }
        }
        
        # insert last array_temp
        if($array_temp!=[]){
            $this->db->insert_batch($this->table_to_insert, $array_temp);
            $array_temp = [];
        }
        
        $this->db->where('exam_date','0000-00-00')->update($this->table_to_insert, ['exam_date'=>null]);
        $this->db->where('last_exam','0000-00-00')->update($this->table_to_insert, ['last_exam'=>null]);
        $msc = microtime(true)-$msc;
        
        $update_file = array(
            'proses' => 1,
            'waktu_proses' => date('Y-m-d H:i:s'),
            'baris_data_success' => null,
            'execution_time_second' => $msc
        );
        $this->M_outbound_donor_darah->update_files($file, $update_file);

        # heru nambah ini, kalau sudah selesai diproses=>file didelete, 2021-05-17
        exec("rm -rf /var/www/html/ess/outbound_dwh/sikesper/dwh_hr_hcm_donor_darah/$file");
        # echo 'Done';
    }
    
	public function get_donor_darah(){
		$this->get_files();
		$this->get_data();
	}
}
