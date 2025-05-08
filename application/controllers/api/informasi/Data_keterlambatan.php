<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Data_keterlambatan extends Group_Controller {

	public function __construct(){
		parent::__construct();			
		$this->load->model("api/informasi/m_data_keterlambatan");
	
		$this->data["is_with_sidebar"] = true;
		$this->load->model("master_data/m_karyawan");
		$this->load->model("master_data/m_satuan_kerja");
	}
	
	public function index_post(){
		$data=[];
        $params=[];
        try {
            if(empty($this->post())){
            	$this->response([
                    'status'=>false,
                    'message'=>"Data harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else {
				$kode_unit = $this->input->post("kode_unit");
				$np_karyawan = $this->input->post("np_karyawan");
				$periode_awal = $this->input->post("periode_awal");
				$periode_akhir = $this->input->post("periode_akhir");
				$keterangan = $this->input->post("keterangan");
				// $periode_akhir = '2020-09-30';
				// $periode_awal = '2020-01-01';

				if ($periode_awal=="" && $periode_akhir=="") {
					$periode = date('Y_m');
					// $periode = '2020_09';
					$list[0] = $this->m_data_keterlambatan->get_all($kode_unit,$np_karyawan,$periode,$keterangan);

				} else if (date('Y', strtotime($periode_awal))==date('Y', strtotime($periode_akhir))) {
					$tahun_awal = date('Y', strtotime($periode_awal));
					$bulan_awal = date('n', strtotime($periode_awal));
					$tahun_akhir = date('Y', strtotime($periode_akhir));
					$bulan_akhir = date('n', strtotime($periode_akhir));
					$bulan = array('00', '01','02','03','04','05','06','07','08','09','10','11','12');

					$no=0;
					for($i=($bulan_awal); $i<=$bulan_akhir; $i++) {
						$periode = $tahun_awal."_".$bulan[$i];
						
						$list[$no] = $this->m_data_keterlambatan->get_all($kode_unit,$np_karyawan,$periode,$keterangan);
						$no++;
					}
				}

				$awal = 0;
				$akhir = count($list);

				$data = array();
				$recordsFiltered = 0;
				$recordsTotal = 0;
				$no = 0;

				for($b=0; $b<count($list); $b++) {
					foreach ($list[$b] as $tampil) {
						$no++;
						$row = array();
						$row['no'] = $no;
						$row['np_karyawan'] = $tampil->np_karyawan;
						$row['nama_karyawab'] = $tampil->nama;
						$row['tanggal'] = tanggal($tampil->tanggal);
						$row['tgl'] = $tampil->tanggal;
						$row['tanggal_dws'] = $tampil->jadwal;
						$row['waktu_masuk_id'] = tanggal_waktu($tampil->jadwal_masuk);
						$row['waktu_masuk'] = $tampil->jadwal_masuk;
						//MACHINE ID
						$machine_id_1 = '';
						if($tampil->tapping_fix_1==null || $tampil->tapping_fix_1=='') {
							if($tampil->tapping_time_1)
								$machine_id_1 = "Machine id : ".$tampil->tapping_terminal_1;
						} else {
							if($tampil->tapping_fix_1) {
								if(substr($tampil->tapping_time_1,0,16) != substr($tampil->tapping_fix_1,0,16)) //dirubah oleh ess
									$machine_id_1 = "Machine id : ".'ESS';					
								else //tidak dirubah
									$machine_id_1 = "Machine id : ".$tampil->tapping_terminal_1;	
							}			
						}
						$row['machine_id'] = $machine_id_1;
						$row['waktu_datang'] = $tampil->datang;
						$row['kedatangan'] = '<b>'.tanggal_waktu($tampil->datang).'</b>'.$machine_id_1;
						$arr_keterangan = explode("|",$tampil->keterangan);
						$keterangan = "";
						for($i=0;$i<count($arr_keterangan);$i++){
							if(!empty($keterangan[$i])){
								$keterangan.="<br><br>";
							}
							$keterangan.=$arr_keterangan[$i];
						}
						$row['keterangan'] = $keterangan;
						
						$data[] = $row;
					}
				}

				$this->response([
		            'status'=>true,
		            'message'=>'Data Keterlambatan',
		            'data'=>array_slice($data, $awal, $akhir)
		        ], MY_Controller::HTTP_OK);
			}
		} catch(Exception $e) {
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
	
}