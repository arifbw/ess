<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Outbound_master_data extends CI_Controller {    
	function __construct() {
		parent::__construct();
        $this->load->helper(['tanggal_helper', 'fungsi_helper']);
		$this->load->model('outbound_sap/M_outbound_master_data');
		$this->load->model("m_setting");
		$this->folder_master_data	= dirname($_SERVER["SCRIPT_FILENAME"])."/outbound_sap/MasterData/";
	}
	
	public function index() {
		//redirect(base_url('dashboard'));
	}
    
    function get_files(){
        $msc = microtime(true);
        $ignored = array('.', '..', '.svn', '.htaccess');
        
        $data_files = array();
        echo __FUNCTION__ ." LINE ". __LINE__ ." Scanning dir ".$this->folder_master_data."\n";
        echo __FUNCTION__ ." LINE ". __LINE__ ." Start : ".date('Y-m-d H:i:s')."\n";
        if(is_dir($this->folder_master_data)){
            foreach (scandir($this->folder_master_data) as $file) {
                if (in_array($file, $ignored)) continue;
                $data_files = [
                    'nama_file'=>$file,
                    'size'=>filesize($this->folder_master_data.$file),
                    'last_modified'=>date('Y-m-d H:i:s', filemtime($this->folder_master_data.$file)),
                    'baris_data'=>$this->count_rows($this->folder_master_data.$file)
                ];
                
                $this->M_outbound_master_data->check_name_then_insert_data($file, $data_files);
            }
            $msc = microtime(true)-$msc;
            echo __FUNCTION__ ." LINE ". __LINE__ ." Done. Execution time: $msc seconds.\nInserted to database.\n";
            
        } else{
            echo __FUNCTION__ ." LINE ". __LINE__ ." Dir not found!\n";
        }
    }
    
    function count_rows($file_name){
        $rows = explode("\n",trim(file_get_contents($file_name)));
        return count($rows) - 1;
    }
    
    function get_data() {
        $msc = microtime(true);
        echo __FUNCTION__ ." LINE ". __LINE__ ." Scanning files...\n";
        echo __FUNCTION__ ." LINE ". __LINE__ ." Start : ".date('Y-m-d H:i:s')."\n";
        
		$alternate = false;
		if(strcmp($this->m_setting->ambil_pengaturan("Alternate Master Data Organisasi dan Karyawan"),"true")==0){
			$alternate = true;
		}
		
        set_time_limit('0');
        				
		$ada_yang_diproses = false;
		
        //get files that process=0
        $get_proses_is_noll = $this->M_outbound_master_data->get_proses_is_nol()->result();
		
        foreach($get_proses_is_noll as $row){
            if($row->nama_file!='index.php' && (is_file($this->folder_master_data.$row->nama_file))){
                $bulan_tahun = $this->read_process($row->nama_file);echo $bulan_tahun."\n";
				$tanggal_dws = $this->M_outbound_master_data->max_tanggal_dws($bulan_tahun);
				echo __FUNCTION__ ." LINE ". __LINE__ ." Tanggal DWS : ".$tanggal_dws."\n";
				$this->M_outbound_master_data->update_master_karyawan($bulan_tahun);
				
				$ada_yang_diproses = true;
            }
        }
        $msc = microtime(true)-$msc;
		
        echo __FUNCTION__ ." LINE ". __LINE__ ." Done. Execution time: $msc seconds.<br>Inserted to database.\n";
        
		
		if($ada_yang_diproses == true)
		{
			//update ke tabel monitoring
			//insert ke tabel 'ess_status_proses_input', id proses = 1
			$this->db->insert('ess_status_proses_input', ['id_proses'=>1, 'waktu'=>date('Y-m-d H:i:s')]);
		}
		
		
    }
    
    function read_process($file){
        $rows = explode("\n",trim(file_get_contents($this->folder_master_data.$file)));
        
        $num_rows = 0;
        $count_inserted = 0;
		
		$bulan_tahun = "";

        //parsing data di file .txt
		foreach($rows as $row){
            if(!empty(trim($row))){
                $num_rows+=1;
                if($num_rows>1){
					
					//ilang salah satu data jika di trim tetapi yg kosong datanya di pinggir (ikut ke trim)
                    //$data = explode("\t",trim($row));
					$data = explode("\t",$row);

                    $jenis_kelamin = "";

                    if(strcmp(@$data[10],"Male")==0){
                        $jenis_kelamin = "Laki-laki";
                    }
                    else if(strcmp(@$data[10],"Female")==0){
                        $jenis_kelamin = "Perempuan";
                    }

                    //need to be check !!!
				
					//kebutuhan karena dari ERP masih melempar P009
					//semua data kecuali off dijadikan umum jakarta
					//kayanya sudah SOLVED jadi bisa di hapus
					/*
					if($data[20]!='OFF')
					{
						$data[20]='P001'; //umum 1 jakarta
						$data[21]=$data[22]; //jadwal dws in nya						
					}
					*/
			
			/* testing
				//start
				if($data[0]=='5742')
				{
					echo $data[0]."<br><br>";
			*/	

				
                    $insert_data = array(	
                        "np_karyawan" => @$data[0],
                        "personnel_number" => @$data[1],
                        "nama" => preg_replace("/\s+/"," ",@$data[2]),
                        "tempat_lahir" => @$data[3],
                        "tanggal_lahir" => substr(@$data[4],0,4)."-".substr(@$data[4],4,2)."-".substr(@$data[4],6,2),
                        "tanggal_masuk" => substr(@$data[5],0,4)."-".substr(@$data[5],4,2)."-".substr(@$data[5],6,2),
                        "kode_unit" => @$data[6],
                        "object_id_unit" => @$data[7],
                        "nama_unit_singkat" => preg_replace("/\s+/"," ",@$data[8]),
                        "nama_unit" => preg_replace("/\s+/"," ",@$data[9]),
                        "jenis_kelamin" => $jenis_kelamin,
                        "agama" => @$data[11],
                        "kontrak_kerja" => @$data[12],
                        "nama_pangkat" => substr(@$data[13],0,strrpos(@$data[13]," ")),
                        "grade_pangkat" => (int)substr(@$data[13],strpos(@$data[13],"(")+1,strpos(@$data[13],")")-strpos(@$data[13],"(")-1),
                        "grup_jabatan" => @$data[14],
                        "grade_jabatan" => @$data[15],
                        "kode_jabatan" => @$data[16],
                        "object_id_jabatan" => @$data[17],
                        "nama_jabatan_singkat" => preg_replace("/\s+/"," ",@$data[18]),
                        "nama_jabatan" => preg_replace("/\s+/"," ",@$data[19]),
                        "dws" => @$data[20],
                        "tanggal_dws" => substr(@$data[21],0,4)."-".substr(@$data[21],4,2)."-".substr(@$data[21],6,2),
                        "tanggal_start_dws" => substr(@$data[22],0,4)."-".substr(@$data[22],4,2)."-".substr(@$data[22],6,2),
                        "start_time" => substr(@$data[23],0,2).":".substr(@$data[23],2,2).":".substr(@$data[23],4,2),
                        "tanggal_end_dws" => substr(@$data[24],0,4)."-".substr(@$data[24],4,2)."-".substr(@$data[24],6,2),
                        "end_time" => substr(@$data[25],0,2).":".substr(@$data[25],2,2).":".substr(@$data[25],4,2),
                        "start_break" => substr(@$data[26],0,2).":".substr(@$data[26],2,2).":".substr(@$data[26],4,2),
                        "end_break" => substr(@$data[27],0,2).":".substr(@$data[27],2,2).":".substr(@$data[27],4,2),
                        "personnel_area" => str_replace("PERURI ","",@$data[28]),
                        "action" => @$data[29],
                        "tm_status" => @$data[30],
                        "masa_kerja_tahun" => (int)@$data[31],
                        "masa_kerja_bulan" => (int)@$data[32],
                        "masa_kerja_hari" => (int)@$data[33],
						"tanggal_mpp" => substr(@$data[34],0,4)."-".substr(@$data[34],4,2)."-".substr(@$data[34],6,2),
						"tanggal_pensiun" => substr(@$data[35],0,4)."-".substr(@$data[35],4,2)."-".substr(@$data[35],6,2),
                        "waktu_ambil" => date("Y-m-d H:i:s")
                    );

                    if($this->M_outbound_master_data->check_id_then_insert_data($insert_data)==true && $insert_data['kontrak_kerja']!='PKWT'){
						$tanggal_pra_mpp = date("Y-m-d",strtotime(date("Y-m-d", strtotime($insert_data["tanggal_mpp"]))."-3 months"));
																		
						if($insert_data["tanggal_dws"]!=$tanggal_pra_mpp){
							
							if($insert_data["masa_kerja_tahun"]>0 and $insert_data["masa_kerja_tahun"]%6==0 and $insert_data["masa_kerja_bulan"]==0 and $insert_data["masa_kerja_hari"]==0){
								//echo "<br><br>".$data[0]." masuk sini 1"."<br><br>";
								$kategori = 1;										
								$this->generate_cuti_besar($insert_data,$kategori);
							}
						}
						else if(strcmp($insert_data["tanggal_dws"],$tanggal_pra_mpp)==0){
							//echo "<br><br>".$data[0]." masuk sini 2"."<br><br>";
														
							$kategori = 2;
							$this->generate_cuti_besar($insert_data,$kategori);
						}
						
                        $count_inserted+=1;
                    }

					if(strcmp($bulan_tahun,str_replace("-","_",substr($insert_data["tanggal_dws"],0,7)))<0){
						$bulan_tahun = str_replace("-","_",substr($insert_data["tanggal_dws"],0,7));
					}
                }
			/* testing	
				//end
				}
			*/	
            
			}
        }
		
        $this->M_outbound_master_data->update_hari_libur_dws_off($bulan_tahun);
		
		$proses = ($count_inserted==($num_rows - 1) ? '1':'0');
		$update_file = array(
			'proses'			=> $proses,
			'baris_data'        => $num_rows - 1,
			'waktu_proses'		=> date('Y-m-d H:i:s')
		);        
		$this->M_outbound_master_data->update_files($file, $update_file);
		
		
		return $bulan_tahun;
    }
	
	private function generate_cuti_besar($insert_data,$kategori){
		$data["np_karyawan"] = $insert_data["np_karyawan"];
		$data["tahun"] = substr($insert_data["tanggal_start_dws"],0,4);
		$data["tanggal_timbul"] = $insert_data["tanggal_start_dws"];
		$data["tanggal_kadaluarsa"] = date("Y-m-d",strtotime($insert_data["tanggal_start_dws"]." +5 years"));
		
		//tanggal timbul cuti besar terakhir		
		$np_karyawan		 	= $data["np_karyawan"];
		$ambil 					= $this->db->query("SELECT * FROM cuti_cubes_jatah WHERE np_karyawan='$np_karyawan' ORDER BY tahun DESC LIMIT 1")->row_array();
		$tanggal_timbul_terakhir 	= $ambil['tanggal_timbul'];
		
		if($tanggal_timbul_terakhir!=null) //jika sudah pernah cubes
		{
			$tanggal_awal 	= $tanggal_timbul_terakhir; //pakai tanggal terakhir timbul cubes
			
		}else //jika belum pernah cubes
		{
			$tanggal_awal = $insert_data["tanggal_masuk"]; //pakai tanggal masuk kerja
			
		}
		
		if($kategori==1) //jika masuk kategori pas masa kerja 6 tahun
		{
			$tanggal_akhir 	= $insert_data["tanggal_start_dws"]; //pakai tanggal dws dia kena 6 tahun
		}else //jika masuk kategori mpp
		{
			$tanggal_akhir 	= $insert_data["tanggal_mpp"]; //pakai tanggal dia mpp nya
		}
		
		$interval = date_diff(date_create($tanggal_awal), date_create($tanggal_akhir));
		
		//7648 | Tri Wibowo, 2 April 2019.. karena array berbentuk object jadi langsung bisa diambil saja int nya
		//$jarak_ke_mpp = $interval->format('%m months')+($interval->format('%m years')*12);
		$jarak_ke_mpp = $interval->m+($interval->y*12);
		$bulan	= $interval->m;
		$tahun	= $interval->y;	
		$jarak_ke_mpp = $bulan+($tahun*12);
				
		//echo "<br><br>$tanggal_awal".$insert_data["np_karyawan"]." jarak mpp nya".$jarak_ke_mpp."(bulan $bulan tahun $tahun) tanggal awal $tanggal_awal tanggal dws".$insert_data["tanggal_start_dws"]."<br><br>";
		
		/* $data["muncul_bulan"] = 3;
		$data["muncul_hari"] = 0;
		$data["konversi_bulan"] = 1;
		$data["konversi_hari"] = 22;
		$data["jadi_cuti_tahunan"] = 12;
		
		$data["konversi_bulan"] = 0;
		$data["konversi_hari"] = 0;
		$data["jadi_cuti_tahunan"] = 0; */
		echo "kategorinyatrue : ".$kategori."<br>";
		echo "jarak mpp : ".$jarak_ke_mpp."<br>";
		echo "interval : ".$interval."<br>";
		echo "tanggal_awal : ".$tanggal_awal."<br>";
		echo "tanggal_akhir : ".$tanggal_akhir."<br>";
		// jarak ke MPP <= 5 tahun ================>  0 s/d 60 : 0 bulan
		if($kategori==2 and $jarak_ke_mpp<=60){
			$data["muncul_bulan"] = 0;
			$data["muncul_hari"] = 0;
			$data["konversi_bulan"] = 0;
			$data["konversi_hari"] = 0;
			$data["jadi_cuti_tahunan"] = 0;
		}
		// jarak ke MPP 5 tahun + 1 s.d 4 bulan ===> 61 s/d 64 : 1 bulan
		else if($kategori==2 and $jarak_ke_mpp>= 61 and $jarak_ke_mpp<=64){
			$data["muncul_bulan"] = 1;
			$data["muncul_hari"] = 0;
			$data["konversi_bulan"] = 0;
			$data["konversi_hari"] = 0;
			$data["jadi_cuti_tahunan"] = 0;
		}
		// jarak ke MPP 5 tahun + 5 s.d 8 bulan ===> 65 s/d 68 : 2 bulan
		else if($kategori==2 and $jarak_ke_mpp>= 65 and $jarak_ke_mpp<=68){
			$data["muncul_bulan"] = 2;
			$data["muncul_hari"] = 0;
			$data["konversi_bulan"] = 0;
			$data["konversi_hari"] = 0;
			$data["jadi_cuti_tahunan"] = 0;
		}
		// jarak ke MPP 5 tahun + 9 s.d 12 bulan ==> 69 s/d 72 : 3 bulan ( >= 69)
		else if($kategori==2 and $jarak_ke_mpp>= 69 /*and $jarak_ke_mpp<=72*/){
			$data["muncul_bulan"] = 3;
			$data["muncul_hari"] = 0;
			$data["konversi_bulan"] = 1;
			$data["konversi_hari"] = 22;
			$data["jadi_cuti_tahunan"] = 12;
		}
		//pas kena 6 bulan
		else if($kategori==1){ 
			$data["muncul_bulan"] = 3;
			$data["muncul_hari"] = 0;
			$data["konversi_bulan"] = 1;
			$data["konversi_hari"] = 22;
			$data["jadi_cuti_tahunan"] = 12;
		}
		$data["total_bulan"] = $data["muncul_bulan"] - $data["konversi_bulan"];
		$data["total_hari"] = $data["muncul_hari"] + $data["konversi_hari"] - $data["jadi_cuti_tahunan"];

		$data["pakai_bulan"] = 0;
		$data["pakai_hari"] = 0;
		$data["kompensasi_bulan"] = 0;
		$data["kompensasi_hari"] = 0;
		$data["sisa_bulan"] = $data["total_bulan"];
		$data["sisa_hari"] = $data["total_hari"];
		$data["waktu_buat"] = date("Y-m-d H:i:s");
		$data["username_buat"] = __FILE__ ." : ". __FUNCTION__;
		
		$this->load->model("osdm/m_cuti_besar");
		
		//15-11-2020, 7648 Tri Wibowo, Check apakah sudah pernah timbul ditahun itu dan tanggal timbul nya sama, karena di check ternyata banyak yg double
		//22-12-2021, 7648 Tri Wibowo, masih banyak yg double dan ternyata variabel np_karyawan belum di tambahkan dan $ di tanggal timbul belum ada
		$np_karyawan 	= $data["np_karyawan"];
		$tahun 			= $data["tahun"];
		$tanggal_timbul = $data["tanggal_timbul"];
		
		$check_double = $this->db->query("SELECT * FROM cuti_cubes_jatah WHERE np_karyawan='$np_karyawan' and tahun='$tahun' and tanggal_timbul='$tanggal_timbul' ORDER BY tahun DESC LIMIT 1")->row_array();
		if($check_double['id']!=null) //jika sudah ada
		{
			//do nothing
		}else
		{
			//jika ada jatah cuti
			if($data["muncul_bulan"]>0){
				$this->m_cuti_besar->generate_cuti_besar($data);
			}
		}
		
		
	}
	
	public function get_master_data(){
		$this->get_files();
		$this->get_data();
	}

	
	//15 12 2020 - 7648 Tri Wibowo, cleansing double cubes, aktifkan jika au di pakai
	/*
	public function cleansing_cubes()
	{
		$np_karyawan_sebelumnya='';		
		
		$table = "cuti_cubes_jatah";
		
		$query = $this->db->query("SELECT
										np_karyawan,
										tahun,
										tanggal_timbul,
										count(*) AS jum 
									FROM
										$table 
									WHERE 
										np_karyawan not like '%x' AND np_karyawan not like '%x1'
									GROUP BY
										np_karyawan,
										tahun 
									ORDER BY
										jum DESC,
										np_karyawan DESC");
		
		
		
		foreach($query->result_array() as $data)
		{
			$np_karyawan 	= $data['np_karyawan']; 
			$tahun 			= $data['tahun'];
			$tanggal_timbul	= $data['tanggal_timbul'];
			$jum 			= $data['jum'];
			
			if($jum>1)
			{
				echo "<hr>";
				echo $np_karyawan;
				echo "<br>";
				echo $tahun;
				echo "<br>";
				//echo $tanggal_timbul;
				//echo "<br>";
				echo $jum;
				echo "<br>";
				
				$query2 = $this->db->query("SELECT
												*
											FROM
												$table 
											WHERE
												np_karyawan='$np_karyawan' AND
												tahun = '$tahun' 
												ORDER BY
												sisa_bulan,sisa_hari ASC");
				
				
				foreach($query2->result_array() as $data2)
				{
					$id = $data2['id'];
					$tahun = $data2['tahun'];
					$sisa_bulan= $data2['sisa_bulan'];
					$sisa_hari= $data2['sisa_hari'];
					
					
					//jika sama maka hapus
					echo "<strong>";
					echo $np_karyawan_sebelumnya;
					echo "vs";
					echo $np_karyawan;
					echo "<br>";
					echo "</strong>";
					if($np_karyawan_sebelumnya == $np_karyawan)
					{
						echo "[hapus]".$np_karyawan."#".$id."#".$tahun."#".$sisa_bulan."#".$sisa_hari;
						echo "<br>";
						
						$np_karyawan_edit = $np_karyawan."x1";
						$username_buat_edit = $username_buat." Cleansing data double 22/12/2021";
												
						$data_update = array(               
								'np_karyawan' 		=> $np_karyawan_edit, 
								'username_buat' 	=> $username_buat_edit
								);
	
						$this->db->where('id',$id);	
						$this->db->update($table, $data_update); 
						
					}else
					{
						echo $id."#".$np_karyawan."#".$tahun."#".$sisa_bulan."#".$sisa_hari;
						echo "<br>";
					}
					
					
					
					$np_karyawan_sebelumnya = $np_karyawan;
				
				}
			}
			
			
			
		}
		
		//$this->output->enable_profiler(TRUE);
		
	}
	*/
}
