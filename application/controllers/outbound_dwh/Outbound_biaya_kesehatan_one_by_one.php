<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_biaya_kesehatan extends CI_Controller {    
	function __construct() {
		parent::__construct();
		$this->load->model('outbound_dwh/M_outbound_biaya_kesehatan');
		$this->folder_biaya_kesehatan	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_dwh/sikesper/dwh_hr_hcm_biaya_kesehatan/";
        // $this->table_to_insert = 'ess_biaya_kesehatan_demo';
        $this->table_to_insert = 'ess_biaya_kesehatan';
	}
    
    function get_files(){
        try{
            $msc = microtime(true);
            $ignored = array('.', '..', '.svn', '.htaccess');
            
            $data_files = array();
            # echo '<br>Scanning dir '.$this->folder_biaya_kesehatan.' ...<br><br>';
            # echo 'Start : '.date('Y-m-d H:i:s').'<br>';
            if(is_dir($this->folder_biaya_kesehatan)){
                foreach (scandir($this->folder_biaya_kesehatan) as $file) {
                    if (in_array($file, $ignored)) continue;
                    $data_files = [
                        'nama_file'=>$file,
                        'size'=>filesize($this->folder_biaya_kesehatan.$file),
                        'last_modified'=>date('Y-m-d H:i:s', filemtime($this->folder_biaya_kesehatan.$file)),
                        'baris_data'=>$this->count_rows($this->folder_biaya_kesehatan.$file)
                    ];
                    
                    $this->M_outbound_biaya_kesehatan->check_name_then_insert_data($file, $data_files);
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
        
        set_time_limit('0');
		$ada_yang_diproses = false;
		
        # get files that process=0
        $get_proses_is_nol = $this->M_outbound_biaya_kesehatan->get_proses_is_nol()->result();
        foreach($get_proses_is_nol as $row){
            if(is_file($this->folder_biaya_kesehatan.$row->nama_file)){
                $this->db->truncate($this->table_to_insert); //kosongkan tabel
                $this->read_process($row->nama_file);
				$ada_yang_diproses = true;
            }
        }
        $msc = microtime(true)-$msc;
        echo "Done. Execution time: $msc seconds. Inserted to database.";
        
		/*if($ada_yang_diproses == true) {
			# masukan data ke tabel monitoring
			# insert ke tabel 'ess_status_proses_input', id proses = 3
			$this->db->insert('ess_status_proses_input', ['id_proses'=>3, 'waktu'=>date('Y-m-d H:i:s')]);
		}*/
    }
    
    function read_process($file){
        # demo: $file = 'SAP_HCM_DATA_BIAYA_KESEHATAN_20200722_103600_CURRENTMONTH.csv';
        $msc = microtime(true);
        $handle = fopen($this->folder_biaya_kesehatan.$file, "r");
        
        $count = 0;
        $count_success = 0;
        while (($row = fgetcsv($handle,0,','))) {
            $count++;
            if ($count == 1) continue;
            
            try{
                $array = [
                    'bill_no' => addslashes((@$row[0]!=''?$row[0]:null)),
                    'np_karyawan' => addslashes((@$row[1]!=''?$row[1]:null)),
                    'personal_number' => addslashes((@$row[2]!=''?$row[2]:null)),
                    'nama_pegawai' => addslashes((@$row[3]!=''?$row[3]:null)),
                    'bagian' => addslashes((@$row[4]!=''?$row[4]:null)),
                    'nama_pasien' => addslashes((@$row[20]!=''?$row[20]:null)),
                    'kode_vendor' => addslashes((@$row[21]!=''?$row[21]:null)),
                    'nama_vendor' => addslashes((@$row[22]!=''?$row[22]:null)),
                    'jumlah_pengobatan' => addslashes((@$row[23]!=''?$row[23]:null)),
                    'tgl_berobat' => addslashes((@$row[24]!=''?$row[24]:null)),
                    'status' => addslashes((@$row[25]!=''?$row[25]:null)),
                    'kode_periksa' => addslashes((@$row[26]!=''?$row[26]:null)),
                    'deskripsi_periksa' => addslashes((@$row[27]!=''?$row[27]:null)),
                    'tagihan' => addslashes((@$row[28]!=''?$row[28]:null)),
                    'tgl_input' => addslashes((@$row[29]!=''?$row[29]:null)),
                    'catatan' => addslashes((@$row[30]!=''?$row[30]:null)),
                    'referral' => addslashes((@$row[31]!=''?$row[31]:null)),
                    'jumlah_hari' => addslashes((@$row[32]!=''?$row[32]:null)),
                    'beban_karyawan' => addslashes((@$row[33]!=''?$row[33]:null)),
                    'tanggungan_karyawan' => addslashes((@$row[34]!=''?$row[34]:null)),
                    'tanggungan_perusahaan' => addslashes((@$row[35]!=''?$row[35]:null)),
                    'melebihi_batas' => addslashes((@$row[36]!=''?$row[36]:null)),
                    'updated' => date('Y-m-d H:i:s')
                ];
                
                $this->db->insert($this->table_to_insert, $array);
                if($this->db->affected_rows()>0){
                    $count_success++;
                }
            } catch(Exception $e){
                break;
            }
        }
        $this->db->where('tgl_berobat','0000-00-00')->update($this->table_to_insert, ['tgl_berobat'=>null]);
        $this->db->where('tgl_input','0000-00-00')->update($this->table_to_insert, ['tgl_input'=>null]);
        $msc = microtime(true)-$msc;
        
        $update_file = array(
            'proses' => 1,
            'waktu_proses' => date('Y-m-d H:i:s'),
            'baris_data_success' => $count_success,
            'execution_time_second' => $msc
        );
        $this->M_outbound_biaya_kesehatan->update_files($file, $update_file);
        # echo 'Done';
    }
    
	public function get_biaya_kesehatan(){
		$this->get_files();
		$this->get_data();
	}
}
