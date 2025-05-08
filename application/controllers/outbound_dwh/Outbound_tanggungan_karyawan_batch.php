<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_tanggungan_karyawan_batch extends CI_Controller {    
	function __construct() {
		parent::__construct();
		$this->load->model('outbound_dwh/M_outbound_tanggungan_karyawan');
		$this->folder_tanggungan	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_dwh/sikesper/dwh_hr_hcm_tanggungan_karyawan/";
        $this->table_to_insert = 'ess_kesehatan_keluarga_tertanggung';
	}
    
    function get_files(){
        try{
            $msc = microtime(true);
            $ignored = array('.', '..', '.svn', '.htaccess');
            
            $data_files = array();
            if(is_dir($this->folder_tanggungan)){
                foreach (scandir($this->folder_tanggungan) as $file) {
                    if (in_array($file, $ignored)) continue;
                    $data_files = [
                        'nama_file'=>$file,
                        'size'=>filesize($this->folder_tanggungan.$file),
                        'last_modified'=>date('Y-m-d H:i:s', filemtime($this->folder_tanggungan.$file)),
                        'baris_data'=>$this->count_rows($this->folder_tanggungan.$file)
                    ];
                    
                    $this->M_outbound_tanggungan_karyawan->check_name_then_insert_data($file, $data_files);
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
        $get_proses_is_nol = $this->M_outbound_tanggungan_karyawan->get_proses_is_nol()->result();
        foreach($get_proses_is_nol as $row){
            if(is_file($this->folder_tanggungan.$row->nama_file)){
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
        $handle = fopen($this->folder_tanggungan.$file, "r");
        
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
                    //'tempat_lahir' => addslashes((@$row[4]!=''?$row[4]:null)),
                    //'bpjs_id' => addslashes((@$row[20]!=''?$row[20]:null)),
                    //'class_bpjs' => addslashes((@$row[21]!=''?$row[21]:null)),
                    //'start_date' => addslashes((@$row[22]!=''?$row[22]:null)),
                    'tipe_keluarga' => addslashes((@$row[21]!=''?$row[21]:null)),
                    'tempat_lahir_keluarga' => addslashes((@$row[27]!=''?$row[27]:null)),
                    'tanggal_lahir' => addslashes((@$row[28]!=''?$row[28]:null)),
                    //'obj_id' => addslashes((@$row[26]!=''?$row[26]:null)),
                    'nama_lengkap' => addslashes((@$row[20]!=''?$row[20]:null)),
                    'bpjs_id_keluarga' => addslashes((@$row[24]!=''?$row[24]:null)),
                    //'class_bpjs_keluarga' => addslashes((@$row[29]!=''?$row[29]:null)),
                    'updated' => date('Y-m-d H:i:s'),
                    'no_urut' => addslashes((@$row[22]!=''?$row[22]:null)),
                    'status_tanggungan' => addslashes((@$row[23]!=''?$row[23]:null)),
                    'benefit_class_tanggungan' => addslashes((@$row[25]!=''?$row[25]:null)),
                    'tanggal_efektif' => addslashes((@$row[26]!=''?$row[26]:null)),
                    'jenis_kelamin' => addslashes((@$row[29]!=''?$row[29]:null))
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
        
        $this->db->where('tanggal_lahir','0000-00-00')->update($this->table_to_insert, ['tanggal_lahir'=>null]);
        $msc = microtime(true)-$msc;
        
        $update_file = array(
            'proses' => 1,
            'waktu_proses' => date('Y-m-d H:i:s'),
            'baris_data_success' => null,
            'execution_time_second' => $msc
        );
        $this->M_outbound_tanggungan_karyawan->update_files($file, $update_file);

        # heru nambah ini, kalau sudah selesai diproses=>file didelete, 2021-05-17
        exec("rm -rf /var/www/html/ess/outbound_dwh/sikesper/dwh_hr_hcm_tanggungan_karyawan/$file");
        # echo 'Done';
    }
    
	public function get_tanggungan(){
		$this->get_files();
		$this->get_data();
	}
}
