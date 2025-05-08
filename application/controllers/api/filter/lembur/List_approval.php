<?php defined('BASEPATH') OR exit('No direct script access allowed');

class List_approval extends MY_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("master_data/m_karyawan");
        $this->load->model('M_approval');
    }
    
    function index_post(){
        $data = [];
        try {
            if(empty($this->post('np'))){
                $this->response([
                    'status'=>false,
                    'message'=>"NP harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('tgl_dws'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tertanggal harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('tgl_mulai'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal mulai harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('tgl_selesai'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Tanggal selesai harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('jam_mulai'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jam mulai harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else if(empty($this->post('jam_selesai'))){
                $this->response([
                    'status'=>false,
                    'message'=>"Jam selesai harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $np_karyawan = $this->post('np');
                $periode_tanggal = $this->post('tgl_dws');
                $tgl_mulai = $this->post('tgl_mulai');
                $jam_mulai = $this->post('jam_mulai');
                $tgl_selesai = $this->post('tgl_selesai');
                $jam_selesai = $this->post('jam_selesai');
                $dteStart = date_create(date('Y-m-d H:i', strtotime($tgl_mulai . ' ' . $jam_mulai)));
                $dteEnd = date_create(date('Y-m-d H:i', strtotime($tgl_selesai . ' ' . $jam_selesai)));
                $diff = date_diff($dteStart, $dteEnd);
                $hari = $diff->format("%a");
                $jam = $diff->format("%h");
                $menit = $diff->format("%i");
                if (($jam > 0 || $hari > 0) && $menit >= 45) $jam = $jam + 1;
                $total = ($hari * 24) + ($jam);
                $periode = date('Y_m', strtotime($periode_tanggal));

                $karyawan = $this->m_karyawan->get_posisi_karyawan_periode_tanggal($np_karyawan, $periode, $periode_tanggal);
                if (empty($karyawan)) {
                    if ($periode == '') $periode = date("Y_m");
                    $karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan, $periode);
        
                    if (empty($karyawan)) {
                        if ($periode == '') $periode = date('Y_m', strtotime(date("Y-m-d") . " -1 months"));
                        $karyawan = $this->m_karyawan->get_posisi_karyawan_periode($np_karyawan, $periode);
                    }
                }
                $kode = $karyawan["kode_unit"];
                if (strcmp(substr($karyawan["kode_unit"], 1, 1), "0") == 0) {
                    $karyawan["kode_unit"] = substr($karyawan["kode_unit"], 0, 3);
                } else {
                    $karyawan["kode_unit"] = substr($karyawan["kode_unit"], 0, 2);
                }

                $cek = $this->db->where(array('no_pokok' => $np_karyawan, 'month(tgl_dws)' => date('m'), 'year(tgl_dws)' => date('Y')))->get('ess_lembur_transaksi')->result_array();
                $get_jml = 0;
                foreach ($cek as $dt_lembur) {
                    $dteStart = date_create(date('Y-m-d H:i', strtotime($dt_lembur['tgl_mulai'] . ' ' . $dt_lembur['jam_mulai'])));
                    $dteEnd = date_create(date('Y-m-d H:i', strtotime($dt_lembur['tgl_selesai'] . ' ' . $dt_lembur['jam_selesai'])));
                    $diff = date_diff($dteStart, $dteEnd);
                    $hari = $diff->format("%a");
                    $jam = $diff->format("%h");
                    $menit = $diff->format("%i");
                    if (($jam > 0 || $hari > 0) && $menit >= 45) $jam = $jam + 1;
                    $get_jml = $get_jml + ($hari * 24) + ($jam);
                }
                $get_jml = $get_jml + $total;

                if ($get_jml > 72) {
                    $send_data['kode_unit'] = substr($kode, 0, 2);
                    $cek_kode = $send_data['kode_unit'] . "000";
                    $cek_approval = $this->db->where('divisi', $cek_kode)->get('mst_approval_lembur');
                    if ($cek_approval->num_rows() > 0) {
                        $set_approval = 'list_atasan_minimal_' . $cek_approval->row()->approval;
                        $send_data['atasan'] = $this->M_approval->$set_approval(array($karyawan["kode_unit"]), $np_karyawan);
        
                        if ($cek_approval->row()->approval == 'kasek')
                            $send_data['kode_unit'] = substr($kode, 0, 4);
                        else if ($cek_approval->row()->approval == 'kadep')
                            $send_data['kode_unit'] = substr($kode, 0, 3);
                        else
                            $send_data['kode_unit'] = substr($kode, 0, 2);
                    } else {
                        $send_data['atasan'] = $this->M_approval->list_atasan_minimal_kadiv(array($karyawan["kode_unit"]), $np_karyawan);
                    }
                } else if ($total <= 4) {
                    $send_data['kode_unit'] = substr($kode, 0, 4);
                    $send_data['atasan'] = $this->M_approval->list_atasan_minimal_kasek(array($karyawan["kode_unit"]), $np_karyawan);
                } else {
                    $send_data['kode_unit'] = substr($kode, 0, 3);
                    $send_data['atasan'] = $this->M_approval->list_atasan_minimal_kadep(array($karyawan["kode_unit"]), $np_karyawan);
                }

                $data = $send_data['atasan'];
                
                $this->response([
                    'status'=>true,
                    'message'=>'List Approval Lembur',
                    'data'=>$data
                ], MY_Controller::HTTP_OK);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>[]
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
