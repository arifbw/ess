<?php
	defined('BASEPATH') OR exit('No direct script access allowed');
	
	class Sppd extends CI_Controller {
		public function __construct(){
			parent::__construct();
			
			$meta = meta_data();
			foreach($meta as $key => $value){
				$this->data[$key] = $value;
			}
			
			$this->folder_view = 'perjalanan_dinas/';
			$this->folder_model = 'perjalanan_dinas/';
			$this->folder_controller = 'perjalanan_dinas/';
			
			$this->akses = array();
			
			$this->load->helper("tanggal_helper");
			$this->load->helper("karyawan_helper");
			$this->load->helper("reference_helper");
			
			#$this->load->model($this->folder_model."m_permohonan_cuti");
			
			$this->data["is_with_sidebar"] = true;
			
			$this->data['judul'] = "SPPD";
			$this->data['id_modul'] = $this->m_setting->ambil_id_modul($this->data['judul']);
			$this->akses = akses_helper($this->data['id_modul']);				
			izin($this->akses["akses"]);
		}
		
		public function index()
		{				
			//echo __FILE__ . __LINE__;die(var_dump($this->akses));
            $this->load->model($this->folder_model."M_sppd");
			$this->data["akses"] 					= $this->akses;
			$this->data["navigasi_menu"] 			= menu_helper();
			$this->data['content'] 					= $this->folder_view."sppd";
			//$this->data['select_mst_cuti']= $this->m_permohonan_cuti->select_mst_cuti();
            
			// $query = $this->db->select("DATE_FORMAT(tgl_berangkat,'%Y-%m') as tahun_bulan")->where('tgl_berangkat is not null', null, false)->where('YEAR(STR_TO_DATE(tgl_berangkat, "%Y-%m-%d")) <=', date('Y'))->where('YEAR(STR_TO_DATE(tgl_berangkat, "%Y-%m-%d")) >=', date('Y')-1)->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_sppd');
            $array_tahun_bulan = array();
			$query = $this->db->select("DATE_FORMAT(tgl_berangkat,'%Y-%m') as tahun_bulan")->where('tgl_berangkat is not null', null, false)->group_by('tahun_bulan')->order_by('tahun_bulan','DESC')->get('ess_sppd');
			foreach ($query->result_array() as $data) 
			{					
				$bulan = substr($data['tahun_bulan'],-2);
				$tahun = substr($data['tahun_bulan'],0,4);
				
				$bulan_tahun = $bulan."-".$tahun;				
				
				$array_tahun_bulan[] = $bulan_tahun; 
			}
            
            $this->data['array_tahun_bulan'] 	= $array_tahun_bulan;
            $this->data['array_daftar_karyawan'] = $this->M_sppd->select_daftar_karyawan();
            $this->data['array_jenis_perjalanan'] = ["Perjalanan Dinas Tugas Belajar", "Perjalanan Dinas Tugas Kerja"];

			// dd($this->data['array_jenis_perjalanan']);
			
			$this->load->view('template',$this->data);
		}	
		
		public function tabel_sppd($bulan_tahun=null, $np=null, $jenis_perjalan=null)
		{
            if(@$bulan_tahun!=0){
                $month = $bulan_tahun;
            } else{
                $month = 0;
            }
            
            if(@$np!='' && @$np!='-'){
                $np_cari = $np;
            } else{
                $np_cari = '';
            }

			if(@$jenis_perjalan!='' && @$jenis_perjalan!='-'){
                $jenis_perjalan = str_replace("%20", " ", $jenis_perjalan);
            } else{
                $jenis_perjalan = '';
            }

			$this->load->model($this->folder_model."M_tabel_sppd");
			
			if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
			{
				$var=array();
				$list_pengadministrasi = $_SESSION["list_pengadministrasi"];
				foreach ($list_pengadministrasi as $data) //looping list_pengadministrasi
				{	
					array_push($var,$data['kode_unit']);							
				}				
			} else if($_SESSION["grup"]==5) //jika Pengguna
			{
				$var 	= $_SESSION["no_pokok"];							
			} else
			{
				$var = 1;				
			}

			// dd($jenis_perjalan);
			
			$list 	= $this->M_tabel_sppd->get_datatables($var,$month,$np_cari,$jenis_perjalan);	
			
			
			$data = array();
			$no = $_POST['start'];
			
	
			foreach ($list as $tampil) {
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = $tampil->np_karyawan;
				$row[] = $tampil->nama;
				$row[] = $tampil->perihal;
				$row[] = $tampil->tipe_perjalanan;
				$row[] = tanggal_indonesia($tampil->tgl_berangkat);	
				$row[] = tanggal_indonesia($tampil->tgl_pulang);
				$row[] = $tampil->catatan;
				                
                $data[] = $row;
			}

			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->M_tabel_sppd->count_all($var, $month, $np_cari,$jenis_perjalan),
				"recordsFiltered" => $this->M_tabel_sppd->count_filtered($var, $month, $np_cari,$jenis_perjalan),
				"data" => $data,
			);
			//output to json format
			echo json_encode($output);
		}
		
		public function cetak() {
			$cetak_bulan = $this->input->post('cetak_bulan');
			$np = $this->input->post('np');
			$jenis_perjalanan = $this->input->post('jenis_perjalanan');

			$this->load->library('phpexcel');
			$this->load->model($this->folder_model."M_sppd");
			
			
			$get_data = $this->M_sppd->get_sppd_bulan_new($cetak_bulan, $np, $jenis_perjalanan);
									
			error_reporting(E_ALL);
	        ini_set('display_errors', TRUE);
	        ini_set('display_startup_errors', TRUE);
		
	        header("Content-type: application/vnd.ms-excel");
	        //nama file
	        header("Content-Disposition: attachment; filename=Data_sppd.xlsx");
	        header('Cache-Control: max-age=0');

	        $excel = PHPExcel_IOFactory::createReader('Excel2007');
	        $excel = $excel->load('./asset/Template_data_sppd.xlsx');

	        //anggota
	        $excel->setActiveSheetIndex(0);
	        $kolom 	= 2;
	        $awal 	= 4;
	        $no = 1;

			foreach ($get_data as $tampil) {
				$excel->getActiveSheet()->setCellValueExplicit('A'.$awal, $no++, PHPExcel_Cell_DataType::TYPE_STRING);
	       
				$excel->getActiveSheet()->setCellValueExplicit('B'.$awal, strtoupper($tampil->np_karyawan), PHPExcel_Cell_DataType::TYPE_STRING);
				 
				$excel->getActiveSheet()->setCellValueExplicit('C'.$awal, strtoupper($tampil->personel_number), PHPExcel_Cell_DataType::TYPE_STRING);
	            $excel->getActiveSheet()->setCellValueExplicit('D'.$awal, ucwords($tampil->nama), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('E'.$awal, ucwords($tampil->nama_jabatan), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('F'.$awal, ucwords($tampil->kode_unit), PHPExcel_Cell_DataType::TYPE_STRING);			
				$excel->getActiveSheet()->setCellValueExplicit('G'.$awal, ucwords($tampil->nama_unit), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('H'.$awal, ucwords($tampil->perihal), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('I'.$awal, ucwords($tampil->tipe_perjalanan), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('J'.$awal, ucwords($tampil->tujuan), PHPExcel_Cell_DataType::TYPE_STRING);			
				$excel->getActiveSheet()->setCellValueExplicit('K'.$awal, date('d-m-Y', strtotime($tampil->tgl_berangkat)), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('L'.$awal, date('d-m-Y', strtotime($tampil->tgl_pulang)), PHPExcel_Cell_DataType::TYPE_STRING);
				$excel->getActiveSheet()->setCellValueExplicit('M'.$awal, $tampil->no_surat, PHPExcel_Cell_DataType::TYPE_STRING);	
				$excel->getActiveSheet()->setCellValueExplicit('N'.$awal, ucwords($tampil->justifikasi), PHPExcel_Cell_DataType::TYPE_STRING);	
				$excel->getActiveSheet()->setCellValueExplicit('O'.$awal, ucwords($tampil->catatan), PHPExcel_Cell_DataType::TYPE_STRING);	
				
	            $awal += 1;	
			}

	        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
	        $objWriter->setIncludeCharts(TRUE);
	        $objWriter->setPreCalculateFormulas(TRUE);
	        PHPExcel_Calculation::getInstance($excel)->clearCalculationCache();
	        $objWriter->save('php://output');
	        exit();
		}
	
        //Fungsi untuk mengambil data dalam file .txt yang ada di outbound_portal/sppd
        public function get_data($last_date=null) {
            //run program selamanya untuk menghindari maximal execution
            //ini_set('MAX_EXECUTION_TIME', -1);
            set_time_limit('0');

            //$this->output->enable_profiler(TRUE);

            echo "Proses ambil data";
            echo "<br>mulai ".date('Y-m-d H:i:s')."<br>";
            
            //ambil data di database setting
            $this->load->model($this->folder_model."M_sppd");
            
			$setting	= $this->M_sppd->setting();
            
            $pamlek_url	= dirname($_SERVER["SCRIPT_FILENAME"]).$setting['url'];
            //$pamlek_max	= $setting['max_files'];
            
			if($last_date=="today")
			{
				$last_date = date("Y-m-d");	
				$last_date = date('Y-m-d', strtotime($last_date . ' -2 day'));
				
			}else
			{
				$last_date = date('Y-m-d', strtotime($last_date . ' -2 day'));
			}
			
			$last_date = str_replace("-","",$last_date);
			
			
            if(@$last_date){
                $file = 'PORTAL_BIAYASPPD_'.$last_date.'.txt';
                if(is_file($pamlek_url.$file)){
                    $this->read_process($pamlek_url, $file);
                    
                } else{
                    $data_error['modul'] 		= "perjalanan_dinas/sppd/get_data"; 
                    $data_error['error'] 		= "Gagal konek ke Directory"; 
                    $data_error['status'] 		= "0";
                    $data_error['created_at'] 	= date("Y-m-d H:i:s");
                    $data_error['created_by'] 	= "scheduler";

                    $this->M_sppd->insert_error($data_error);
                    echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
                }
            } else{
                
				/*
				//ambil data mana saja yang belum di proses
                $result = $this->M_sppd->select_pamlek_files_limit($pamlek_max);

                $arr_registered_pamlek_files = array();
                foreach ($result->result_array() as $data) {
                    array_push($arr_registered_pamlek_files,$data['nama_file']);	 
                }
				*/
                
                //check server pamlek menyala
                if(is_dir($pamlek_url)) {
                    //scan file .txt dalam server ftp pamlek 
                    $arr_scan_pamlek_files = scandir($pamlek_url);

                    $pamlek_files = array();		
                    foreach($arr_scan_pamlek_files as $file){
                        if(in_array($file,$arr_registered_pamlek_files)){
                            array_push($pamlek_files,$file);
                        }
                    }

                    foreach($pamlek_files as $file){
                        $this->read_process($pamlek_url, $file);
                    }
                } else {
                    $data_error['modul'] 		= "perjalanan_dinas/sppd/get_data"; 
                    $data_error['error'] 		= "Gagal konek ke Directory"; 
                    $data_error['status'] 		= "0";
                    $data_error['created_at'] 	= date("Y-m-d H:i:s");
                    $data_error['created_by'] 	= "scheduler";

                    $this->M_sppd->insert_error($data_error);
                    echo "<br>status = ".$data_error['error'].", ".$data_error['modul'];
                }
            }
            
            echo "<br>selesai ".date('Y-m-d H:i:s');
        }
        
        function read_process($pamlek_url, $file){
        	$this->load->model($this->folder_model."M_tabel_sppd");
            echo "<br>".$file."<br><br>";

            $rows = explode("\n",trim(file_get_contents($pamlek_url.$file)));

            $i =1;
            $banyak_data=0;
            $count = 0;
            
            //parsing data di file .txt
            $array_insert_data = array();
            foreach($rows as $row){
                if(!empty(trim($row))){

                    $banyak_data++;
                    if($banyak_data>1){
                        $count++;
                        $pisah = explode("\t",trim($row));

                        $insert_data = array(
							'id_biaya_sppd'   	=> @$pisah[0],
                            'id_sppd'       	=> @$pisah[1],
                            'id_user'           => @$pisah[2],
                            'kode_sto' 			=> @$pisah[3],
                            'jenis_fasilitas'	=> @$pisah[4],	
                            'biaya'			    => @$pisah[5],	
                            'biayaus'		    => @$pisah[6],	
                            'perihal'   		=> @$pisah[7],	
							'tipe_perjalanan'   => @$pisah[8],
							'tgl_berangkat'   	=> @$pisah[9],
							'tgl_pulang'   		=> @$pisah[10],
                            'tgl_selesai'		=> @$pisah[11]
                        );
                        
                        $get_id = $this->M_sppd->cek_id_then_insert_data($pisah[0], $insert_data);
                        if ($get_id != false) {
                        	$this->M_sppd->update_to_cico($get_id, $insert_data);
                        }
                    }
                }
            }
            
			/*
            $update_file = array(
                'proses'			=> '1',
                'baris_data' 		=> $count,
                'waktu_proses'		=> date('Y-m-d H:i:s')
            );

            $this->M_sppd->update_files($file, $update_file);
			*/
		}
	}
	
	/* End of file perencanaan_jadwal_kerja.php */
	/* Location: ./application/controllers/kehadiran/perencanaan_jadwal_kerja.php */