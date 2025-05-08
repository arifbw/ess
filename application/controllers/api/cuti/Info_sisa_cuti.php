<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Info_sisa_cuti extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("cuti/m_permohonan_cuti");
        $this->load->helper("tanggal_helper");
        $this->load->helper("karyawan_helper");
    }
    
    function index_get(){        
        try {
            if(empty($this->get('np'))){
                $np_karyawan = $this->data_karyawan->np_karyawan;
                $nama_karyawan = $this->data_karyawan->nama;
            }else{
                $np_karyawan = $this->get('np');
                $cek_karyawan = $this->db->select('nama')->where('no_pokok',$np_karyawan)->get('mst_karyawan');
                if($cek_karyawan->num_rows()==1){
                    $nama_karyawan = $cek_karyawan->row()->nama;
                } else{
                    $this->response([
                        'status'=>false,
                        'message'=>'NP tidak ditemukan',
                        'data'=>''
                    ], MY_Controller::HTTP_BAD_REQUEST);
                }
            }
            
            $absence_quota = $this->m_permohonan_cuti->select_absence_quota_by_np($np_karyawan);
            
            $sisa_cuti = null;
			foreach ($absence_quota->result_array() as $data) 
			{
				$sisa = $data['number']-$data['deduction'];
				$cuti = substr($data['start_date'],0,4)." : ".$sisa." (masa aktif cuti ".tanggal($data['deduction_from'])." s/d ".tanggal($data['deduction_to']).")";
				if($sisa_cuti==null)
					$sisa_cuti=$cuti;
				else
					$sisa_cuti = $sisa_cuti."<br>$cuti";
			}
            
            $cubes = $this->m_permohonan_cuti->select_cubes_by_np($np_karyawan);
            $sisa_cubes = null;
			foreach ($cubes->result_array() as $data) 
			{				
				$cuti = $data['tahun']." : ".$data['sisa_bulan']. " bulan ". $data['sisa_hari']." hari (masa aktif cuti ".tanggal($data['tanggal_timbul'])." s/d ".tanggal($data['tanggal_kadaluarsa']).")";
				if($sisa_cubes==null)
					$sisa_cubes=$cuti;
				else
					$sisa_cubes = $sisa_cubes."<br>$cuti";
			}
            
            $hutang = $this->m_permohonan_cuti->select_hutang_by_np($np_karyawan)->result_array();
            
            $data = $nama_karyawan;
            if ($sisa_cuti){
				$data = $data."<br>==================<br>"."Sisa Cuti Tahunan"."<br>==================<br>$sisa_cuti";
				$cuti_tahunan_menunggu_sdm  = cuti_tahunan_menunggu_sdm($np_karyawan);
				if($cuti_tahunan_menunggu_sdm=='')
					$cuti_tahunan_menunggu_sdm=0;
															
				$cuti_tahunan_menunggu_cutoff  = cuti_tahunan_menunggu_cutoff($np_karyawan);
				if($cuti_tahunan_menunggu_cutoff=='')
					$cuti_tahunan_menunggu_cutoff=0;
				
				$data = $data."<br><br>Cuti Tahunan Menunggu Persetujuan SDM : $cuti_tahunan_menunggu_sdm";
				$data = $data."<br>Cuti Tahunan Menunggu Cutoff ERP : $cuti_tahunan_menunggu_cutoff<br>";
			} else{
				$data = $data."<br>==================<br>"."Belum ada Detail"."<br>==================<br>";
			}
			
			if($sisa_cubes){
				$data = $data."<br>==================<br>"."Sisa Cuti Besar"."<br>==================<br>"."$sisa_cubes";
				$cuti_besar_menunggu_sdm  = cuti_besar_menunggu_sdm($np_karyawan);
				if($cuti_besar_menunggu_sdm['menunggu_sdm_bulan']=='')
					$menunggu_sdm_bulan=0;
				else
                    $menunggu_sdm_bulan=$cuti_besar_menunggu_sdm['menunggu_sdm_bulan'];
				
				if($cuti_besar_menunggu_sdm['menunggu_sdm_hari']=='')
					$menunggu_sdm_hari=0;
				else
					$menunggu_sdm_hari=$cuti_besar_menunggu_sdm['menunggu_sdm_hari'];
                
				$data = $data."<br><br>Cuti Besar Menunggu Persetujuan SDM : $menunggu_sdm_bulan Bulan $menunggu_sdm_hari Hari";
			}
			
			if(!empty($hutang))
				$data = $data."<br><br>==================<br>"."Hutang Cuti : ".$hutang[0]["hutang"]." hari"."<br>==================<br>";
                        
            $this->response([
                'status'=>true,
                'message'=>'Info sisa Cuti',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>$e->getMessage(),
                'data'=>''
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
