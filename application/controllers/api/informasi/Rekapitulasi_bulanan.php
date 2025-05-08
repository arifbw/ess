<?php defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );
class Rekapitulasi_bulanan extends Group_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model("informasi/m_rekapitulasi_bulanan");
        $this->load->helper("fungsi_helper");
        
        if(!in_array($this->id_group,[5])){
            $this->response([
                'status'=>false,
                'message'=>"Hanya bisa diakses pengguna",
                'data'=>[]
            ], MY_Controller::HTTP_FORBIDDEN);
        }
    }

    function index_post() {
        $data=[];
        $params=[];
        try {
            if(empty($this->post())){
                $this->response([
                    'status'=>false,
                    'message'=>"Data harus diisi",
                    'data'=>[]
                ], MY_Controller::HTTP_BAD_REQUEST);
            } else{
                $periode = $this->post('periode'); //2020_11
                $no_pokok = $this->post('np'); //7648

                $periode_awal = str_replace("_","-",$periode)."-01";
                $periode_akhir = date("Y-m-t",strtotime($periode_awal));
                
                $this->data["arr_tanggal"] = array();
                for($i=1;$i<=(int)date("t",strtotime($periode_awal));$i++){
                    array_push($this->data["arr_tanggal"],str_replace("_","-",$periode)."-".str_pad($i,2,"0",STR_PAD_LEFT));
                }
                
                $this->load->model("master_data/m_hari_libur");
                $arr_libur = $this->m_hari_libur->daftar_hari_libur_periode($periode_awal,$periode_akhir);
                
                $this->data["arr_tanggal_libur"] = array();
                $this->data["arr_nama_libur"] = array();
                foreach($arr_libur as $libur){
                    array_push($this->data["arr_tanggal_libur"],$libur["tanggal"]);
                    $this->data["arr_nama_libur"][$libur["tanggal"]] = $libur["deskripsi"];
                }
                
                $arr_jadwal_kerja = $this->m_rekapitulasi_bulanan->jadwal_kerja($no_pokok,$periode);
                $this->data["arr_jadwal_kerja"] = array();
                
                foreach($arr_jadwal_kerja as $jadwal_kerja){
                    $this->data["arr_jadwal_kerja"][$jadwal_kerja["tertanggal"]]=$jadwal_kerja;
                }
                
                $this->load->model("lembur/m_pengajuan_lembur");
                $arr_lembur = $this->m_pengajuan_lembur->lembur_karyawan_per_bulan($no_pokok,$periode);
                $this->data["arr_lembur"] = array();
                
                foreach($arr_lembur as $lembur){
                    $this->data["arr_lembur"][$lembur["tgl_dws"]][$lembur["jenis_lembur"]] = $lembur;
                }
                
                $this->load->model("perizinan/m_perizinan");
                $arr_perizinan = $this->m_perizinan->perizinan_karyawan_per_bulan($no_pokok,$periode);
                $this->data["arr_perizinan"] = array();
                
                $this->data['no_pokok'] = $no_pokok;

                $kehadiran = array();
                $no = 0;
                foreach($this->data["arr_tanggal"] as $tanggal) {
                    $nama_hari_libur = "";
                    if(in_array($tanggal,$this->data['arr_tanggal_libur'])){
                        $nama_hari_libur = $this->data['arr_nama_libur'][$tanggal];
                    }

                    $jenis_kehadiran = "";
                    $tidak_lengkap_hadir = "";
                                    
                    $hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tanggal' AND np_karyawan='$no_pokok' LIMIT 1")->row_array();
                    $id_cuti_bersama = $hari_cuti_bersama['id'];
                    
                    if(!isset($this->data["arr_jadwal_kerja"][$tanggal])){
                        $jenis_kehadiran = "";
                    }
                    else if(!empty($id_cuti_bersama)){ //jika cuti bersama
                        $jenis_kehadiran = "Cuti Bersama";
                    }
                    else if(!empty($this->data["arr_jadwal_kerja"][$tanggal]["id_sppd"])){
                        $jenis_kehadiran = "Perjalanan Dinas / Pendidikan";
                    }
                    else if(!empty($this->data["arr_jadwal_kerja"][$tanggal]["id_cuti"])){
                        $jenis_kehadiran = "Cuti";
                    }
                    else if(strcmp($this->data["arr_jadwal_kerja"][$tanggal]["dws_name"],"OFF")==0 and empty($this->data["arr_jadwal_kerja"][$tanggal]["datang"]) and empty($this->data["arr_jadwal_kerja"][$tanggal]["pulang"])){
                        $jenis_kehadiran = "Libur";
                    }
                    else if(strcmp($this->data["arr_jadwal_kerja"][$tanggal]["dws_name"],"OFF")==0 and !(empty($this->data["arr_jadwal_kerja"][$tanggal]["datang"]) and empty($this->data["arr_jadwal_kerja"][$tanggal]["pulang"]))){
                        $jenis_kehadiran = "Lembur di Hari Libur";
                    }
                    else if(strcmp($this->data["arr_jadwal_kerja"][$tanggal]["dws_name"],"OFF")!=0 and !empty($this->data["arr_jadwal_kerja"][$tanggal]["datang"]) and !empty($this->data["arr_jadwal_kerja"][$tanggal]["pulang"])){
                        $jenis_kehadiran = "Hadir";
                        $tidak_lengkap_hadir = "Lengkap";
                    }
                    else if(strcmp($this->data["arr_jadwal_kerja"][$tanggal]["dws_name"],"OFF")!=0 and empty($this->data["arr_jadwal_kerja"][$tanggal]["datang"]) and !empty($this->data["arr_jadwal_kerja"][$tanggal]["pulang"])){
                        $jenis_kehadiran = "Hadir";
                        $tidak_lengkap_hadir = "Tidak Lengkap : Tidak Slide Masuk";
                    }
                    else if(strcmp($this->data["arr_jadwal_kerja"][$tanggal]["dws_name"],"OFF")!=0 and !empty($this->data["arr_jadwal_kerja"][$tanggal]["datang"]) and empty($this->data["arr_jadwal_kerja"][$tanggal]["pulang"])){
                        $jenis_kehadiran = "Hadir";
                        $tidak_lengkap_hadir = "Tidak Lengkap : Tidak Slide Keluar";
                    }
                    else if(strcmp($this->data["arr_jadwal_kerja"][$tanggal]["dws_name"],"OFF")!=0 and empty($this->data["arr_jadwal_kerja"][$tanggal]["datang"]) and empty($this->data["arr_jadwal_kerja"][$tanggal]["pulang"])){                
                        $jenis_kehadiran = "Hadir";
                        $tidak_lengkap_hadir = "Tidak Lengkap : Tidak Slide Masuk dan Tidak Slide Keluar";              
                    }
                    
                    if(strcmp($jenis_kehadiran,"Hadir")==0){
                        if(strcmp($this->data["arr_jadwal_kerja"][$tanggal]["wfh"],"1")==0){
                            $jenis_kehadiran .= " Kerja dari Rumah / <i>Work From Home</i> (WFH)";
                        }
                    }

                    $no++;
                    $row = array();
                    $row['no'] = $no;
                    $row['tgl_dws'] = $tanggal;
                    $row['hari_libur'] = $nama_hari_libur;
                    $row['tgl_indonesia'] = hari_tanggal($tanggal);
                    if(isset($this->data['arr_jadwal_kerja'][$tanggal])) {
                        $row['nama_jadwal_kerja'] = $this->data['arr_jadwal_kerja'][$tanggal]["dws_name"];
                        $row['deskripsi_jadwal_kerja'] = $this->data['arr_jadwal_kerja'][$tanggal]["description"];
                        $row['jadwal_tanggal_masuk'] = $this->data['arr_jadwal_kerja'][$tanggal]["jadwal_tanggal_masuk"];
                        $row['jadwal_jam_masuk'] = $this->data['arr_jadwal_kerja'][$tanggal]["jadwal_jam_masuk"];
                        $row['jadwal_tanggal_pulang'] = $this->data['arr_jadwal_kerja'][$tanggal]["jadwal_tanggal_pulang"];
                        $row['jadwal_jam_pulang'] = $this->data['arr_jadwal_kerja'][$tanggal]["jadwal_jam_pulang"];
                        if(strcmp($this->data['arr_jadwal_kerja'][$tanggal]["istirahat"],"terjadwal")==0){
                            $row['jadwal_istirahat'] = $this->data['arr_jadwal_kerja'][$tanggal]["dws_break_start_time"]." - ".$this->data['arr_jadwal_kerja'][$tanggal]["dws_break_end_time"];
                        } else if(strcmp($this->data['arr_jadwal_kerja'][$tanggal]["istirahat"],"bergantian")==0){
                            $row['jadwal_istirahat'] = 'Bergantian';
                        }
                        $row['realisasi_jenis'] = $jenis_kehadiran;
                        if(!empty($tidak_lengkap_hadir)){
                            $row['realisasi_kelengkapan'] = $tidak_lengkap_hadir;
                        }
                        if(strcmp($jenis_kehadiran,"Perjalanan Dinas / Pendidikan")==0){
                            $row['realisasi'][0]['jenis_kehadiran'] = 'Kegiatan';
                            $row['realisasi'][0]['keterangan_kehadiran'] = $this->data['arr_jadwal_kerja'][$tanggal]["perihal_dinas"];
                        }
                        else if(in_array($jenis_kehadiran,array("Hadir","Lembur di Hari Libur"))){
                            $row['realisasi'][0]['jenis_kehadiran'] = 'Datang';
                            $row['realisasi'][0]['keterangan_kehadiran'] = tanggal_waktu($this->data['arr_jadwal_kerja'][$tanggal]["datang"]);
                            $row['realisasi'][1]['jenis_kehadiran'] = 'Pulang';
                            $row['realisasi'][1]['keterangan_kehadiran'] = tanggal_waktu($this->data['arr_jadwal_kerja'][$tanggal]["pulang"]);
                        }

                        if(isset($this->data['arr_lembur'][$tanggal])){
                            $l = 0; foreach($this->data['arr_lembur'][$tanggal] as $lembur){
                                $row['lembur'][$l]['jenis_lembur'] = $lembur["jenis_lembur"];
                                $row['lembur'][$l]['waktu_mulai_lembur'] = $lembur["waktu_mulai_fix"];
                                $row['lembur'][$l]['waktu_selesai_lembur'] = $lembur["waktu_selesai_fix"];
                                $l++;
                            }
                        }
                    }
                    
                    $kehadiran[] = $row;
                }

                $this->response([
                    'status'=>true,
                    'message'=>'Rekapitulasi Bulanan',
                    'data'=>$kehadiran
                ], MY_Controller::HTTP_OK);
            
                // $this->load->view($this->folder_view."ajax_rekapitulasi_bulanan",$this->data);
            }
        } catch(Exception $e){
            $this->response([
                'status'=>false,
                'message'=>'Error Exception',
                'data'=>$data
            ], MY_Controller::HTTP_BAD_REQUEST);
        }
    }
}
