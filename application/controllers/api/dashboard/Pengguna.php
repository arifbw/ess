<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pengguna extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/dashboard/M_dashboard_pengguna_api','dashboard');
		$this->load->model("poin_reward/m_manajemen_poin", "poin");
    }
    
	function index_get() {
        $_get_np = $this->get('np');
        $_get_month = $this->get('bulan');
        $np = @$_get_np!='' ? $_get_np : $this->data_karyawan->np_karyawan;
        $month = @$_get_month!='' ? $_get_month : date('Y-m');
        $checkDateKehadiran = str_replace('-', '_', $month);
        $data=[];
        $value = [$np, $month];
        
        try {
            # cuti
            $total_cuti = $this->dashboard->getTotalCuti_where('both', $value, $checkDateKehadiran);
            $data['total_cuti'] = $total_cuti;
            
            # lembur
            $total_lembur = $this->dashboard->getTotalLembur_where('both', $value, $checkDateKehadiran);
            $data['total_lembur'] = $total_lembur;
            
            # izin
            $total_izin = $this->dashboard->getTotalIzin_where('both', $value, $checkDateKehadiran);
            $data['total_izin'] = $total_izin;
            
            # dinas
            $total_dinas = $this->dashboard->getTotalDinas_where('both', $value, $checkDateKehadiran);
            $data['total_dinas'] = $total_dinas;
            
            # grafik kehadiran
            $grafik_kehadiran = $this->dashboard->getGrafikKehadiran_where('both', $value, $checkDateKehadiran);
            $data['grafik_kehadiran'] = $grafik_kehadiran;

			# poin sekarang
			$poin = $this->poin->poin_sekarang($np);
            $data['poin'] = $poin;
            
            $this->response([
                'status'=>true,
                'message'=>'Success',
                'data'=>$data
            ], MY_Controller::HTTP_OK);
        } catch(Exception $e) {
            $this->response([
                'status'=>false,
                'message'=>'Not found',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
	}
}
