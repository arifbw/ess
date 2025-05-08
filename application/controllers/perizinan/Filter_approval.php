<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Filter_approval extends CI_Controller {
    public function __construct(){
        parent::__construct();
        $this->load->helper("fungsi_helper");
        $this->load->model("master_data/M_karyawan");
        $this->load->model("M_approval");
    }
    
    function get_approval(){
        $response=[];
        $data=[];
        try{
            $atasan_1_np = '';
            $atasan_1_nama = '';
            $atasan_1_jabatan = '';
            $atasan_2_np = '';
            $atasan_2_nama = '';
            $atasan_2_jabatan = '';
            
            $np = $this->input->post('np');
            $absence_type = $this->input->post('absence_type');
            
            if($absence_type=='' || $np==''){
                $data['atasan_1'] = [
                    /*'no_pokok'=>$atasan_1_np,
                    'nama'=>$atasan_1_nama,
                    'nama_jabatan'=>$atasan_1_jabatan,*/
                ];
                $data['atasan_2'] = [
                    /*'no_pokok'=>$atasan_2_np,
                    'nama'=>$atasan_2_nama,
                    'nama_jabatan'=>$atasan_2_jabatan,*/
                ];
            } else if($absence_type=='SIPK|2001|5030'){
                $data['atasan_1'] = $this->minimal_kaun($np);
                $data['atasan_2'] = [];
            } else{
                $explode_absence_type = explode('|',$absence_type);
                $kode_pamlek = $explode_absence_type[0];
                $kode_erp = $explode_absence_type[1].'|'.$explode_absence_type[2];
                
                $data['atasan_1'] = $this->minimal_kasek($np);
                $data['atasan_2'] = $this->minimal_kadiv($np);
            }
            
            echo json_encode([
                'status'=>true,
                'message'=>'Approver',
                'data'=>$data
            ]);
        } catch(Exception $e){
            echo json_encode([
                'status'=>false,
                'message'=>'Error exception',
                'data'=>[]
            ]);
        }
    }
    
    private function minimal_kaun($np){
        $data = [];
        $get_data_karyawan = $this->M_karyawan->get_karyawan($np);
        $kode_unit = $get_data_karyawan['kode_unit'];
        $level_unit = level_unit($kode_unit);

        $get_approver = $this->M_approval->list_atasan_minimal_kaun([$kode_unit],$np);
        if($level_unit==5){
            $data = $get_approver;
        } else{
            foreach($get_approver as $r){
                $push = false;
                if($level_unit==5){
                    if(in_array($get_data_karyawan['grup_jabatan'],['KAUN','AHLIPTMA']) || substr($get_data_karyawan['kode_jabatan'],-3)=='700'){
                        $push = level_unit($r['kode_unit'])<=4 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=5 ? true:false;
                    }
                } else if($level_unit==4){
                    if(in_array($get_data_karyawan['grup_jabatan'],['KASEK','AHLIMUDA','AHLIMDYA']) || substr($get_data_karyawan['kode_jabatan'],-3)=='600'){
                        $push = level_unit($r['kode_unit'])<=3 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=4 ? true:false;
                    }
                } else if($level_unit==3){
                    if($get_data_karyawan['grup_jabatan']=='KADEP' || substr($get_data_karyawan['kode_jabatan'],-3)=='400'){
                        $push = level_unit($r['kode_unit'])<=2 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=3 ? true:false;
                    }
                } else if($level_unit==2){
                    if($get_data_karyawan['grup_jabatan']=='KADIV' || substr($get_data_karyawan['kode_jabatan'],-3)=='300'){
                        $push = level_unit($r['kode_unit'])<=1 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=2 ? true:false;
                    }
                } else if($level_unit==1){
                    if(in_array(substr($get_data_karyawan['kode_jabatan'],-3),['200','100'])){
                        $push = level_unit($r['kode_unit'])<=1 ? true:false;
                    }
                } 

                if($push==true){
                    $row=[];
                    $row['no_pokok'] = $r['no_pokok'];
                    $row['nama'] = $r['nama'];
                    $row['nama_jabatan'] = $r['nama_jabatan'];
                    $data[] = $row;
                }
            }
        }
        return $data;
    }
    
    private function minimal_kasek($np){
        $data = [];
        $get_data_karyawan = $this->M_karyawan->get_karyawan($np);
        $kode_unit = $get_data_karyawan['kode_unit'];
        $level_unit = level_unit($kode_unit);

        $get_approver = $this->M_approval->list_atasan_minimal_kasek([$kode_unit],$np);
        if($level_unit==5){
            $data = $get_approver;
        } else{
            foreach($get_approver as $r){
                $push = false;
                if($level_unit==4){
                    if(in_array($get_data_karyawan['grup_jabatan'],['KASEK','AHLIMUDA','AHLIMDYA']) || substr($get_data_karyawan['kode_jabatan'],-3)=='600'){
                        $push = level_unit($r['kode_unit'])<=3 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=4 ? true:false;
                    }
                } else if($level_unit==3){
                    if($get_data_karyawan['grup_jabatan']=='KADEP' || substr($get_data_karyawan['kode_jabatan'],-3)=='400'){
                        $push = level_unit($r['kode_unit'])<=2 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=3 ? true:false;
                    }
                } else if($level_unit==2){
                    if($get_data_karyawan['grup_jabatan']=='KADIV' || substr($get_data_karyawan['kode_jabatan'],-3)=='300'){
                        $push = level_unit($r['kode_unit'])<=1 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=2 ? true:false;
                    }
                } else if($level_unit==1){
                    if(in_array(substr($get_data_karyawan['kode_jabatan'],-3),['200','100'])){
                        $push = level_unit($r['kode_unit'])<=1 ? true:false;
                    }
                } 

                if($push==true){
                    $row=[];
                    $row['no_pokok'] = $r['no_pokok'];
                    $row['nama'] = $r['nama'];
                    $row['nama_jabatan'] = $r['nama_jabatan'];
                    $data[] = $row;
                }
            }
        }
        return $data;
    }
    
    private function minimal_kadep($np){
        $data = [];
        $get_data_karyawan = $this->M_karyawan->get_karyawan($np);
        $kode_unit = $get_data_karyawan['kode_unit'];
        $level_unit = level_unit($kode_unit);
        
        $get_approver = $this->M_approval->list_atasan_minimal_kadep([$kode_unit],$np);
        if(in_array($level_unit,[4,5])){
            $data = $get_approver;
        } else{
            foreach($get_approver as $r){
                $push = false;
                if($level_unit==3){
                    if($get_data_karyawan['grup_jabatan']=='KADEP' || substr($get_data_karyawan['kode_jabatan'],-3)=='400'){
                        $push = level_unit($r['kode_unit'])<=2 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=3 ? true:false;
                    }
                } else if($level_unit==2){
                    if($get_data_karyawan['grup_jabatan']=='KADIV' || substr($get_data_karyawan['kode_jabatan'],-3)=='300'){
                        $push = level_unit($r['kode_unit'])<=1 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=2 ? true:false;
                    }
                } else if($level_unit==1){
                    if(in_array(substr($get_data_karyawan['kode_jabatan'],-3),['200','100'])){
                        $push = level_unit($r['kode_unit'])<=1 ? true:false;
                    }
                }
                
                if($push==true){
                    $row=[];
                    $row['no_pokok'] = $r['no_pokok'];
                    $row['nama'] = $r['nama'];
                    $row['nama_jabatan'] = $r['nama_jabatan'];
                    $data[] = $row;
                }
            }
        }
        return $data;
    }
    
    private function minimal_kadiv($np){
        $data = [];
        $get_data_karyawan = $this->M_karyawan->get_karyawan($np);
        $kode_unit = $get_data_karyawan['kode_unit'];
        $level_unit = level_unit($kode_unit);
        
        $get_approver = $this->M_approval->list_atasan_minimal_kadiv([$kode_unit],$np);
        if(in_array($level_unit,[3,4,5])){
            $data = $get_approver;
        } else{
            foreach($get_approver as $r){
                $push = false;
                if($level_unit==2){
                    if($get_data_karyawan['grup_jabatan']=='KADIV' || substr($get_data_karyawan['kode_jabatan'],-3)=='300'){
                        $push = level_unit($r['kode_unit'])<=1 ? true:false;
                    } else{
                        $push = level_unit($r['kode_unit'])<=2 ? true:false;
                    }
                } else if($level_unit==1){
                    if(in_array(substr($get_data_karyawan['kode_jabatan'],-3),['200','100'])){
                        $push = level_unit($r['kode_unit'])<=1 ? true:false;
                    }
                } 

                if($push==true){
                    $row=[];
                    $row['no_pokok'] = $r['no_pokok'];
                    $row['nama'] = $r['nama'];
                    $row['nama_jabatan'] = $r['nama_jabatan'];
                    $data[] = $row;
                }
            }
        }
        
        return $data;
    }
}