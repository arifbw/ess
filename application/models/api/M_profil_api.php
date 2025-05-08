<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_profil_api extends CI_Model {
    function get_profil($token){
        $y_m = date('Y_m');
        if(check_table_exist("erp_master_data_{$y_m}")=='ada'){
            $tb_kry = "erp_master_data_{$y_m}";
            $field_np = 'np_karyawan';
            return $this->db->select('c.np_karyawan, c.personnel_number, c.nama, c.tempat_lahir, c.tanggal_lahir, c.tanggal_masuk, c.kode_unit, c.object_id_unit, c.nama_unit_singkat, c.nama_unit, c.jenis_kelamin, c.agama, c.kontrak_kerja, c.nama_pangkat, c.grade_pangkat, c.grup_jabatan, c.grade_jabatan, c.kode_jabatan, c.object_id_jabatan, c.nama_jabatan_singkat, c.nama_jabatan')
                ->where(['a.key'=>$token])
                ->from('keys a')
                ->join('usr_pengguna b','a.user_id=b.id')
                ->join("{$tb_kry} c","b.no_pokok=c.{$field_np}",'LEFT')
                ->order_by('c.tanggal_dws','DESC')
                ->limit(1)
                ->get();
        } else{
            $tb_kry = "mst_karyawan";
            $field_np = 'no_pokok';
            return $this->db->select('c.no_pokok AS np_karyawan, c.personnel_number, c.nama, c.tempat_lahir, c.tanggal_lahir, c.tanggal_masuk, c.kode_unit, c.object_id_unit, c.nama_unit_singkat, c.nama_unit, c.jenis_kelamin, c.agama, c.kontrak_kerja, c.nama_pangkat, c.grade_pangkat, c.grup_jabatan, c.grade_jabatan, c.kode_jabatan, c.object_id_jabatan, c.nama_jabatan_singkat, c.nama_jabatan')
                ->where(['a.key'=>$token])
                ->from('keys a')
                ->join('usr_pengguna b','a.user_id=b.id')
                ->join("{$tb_kry} c","b.no_pokok=c.{$field_np}",'LEFT')
                ->get();
        }
    }
}