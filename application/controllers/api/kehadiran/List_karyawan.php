<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once( APPPATH . 'core/Group_Controller.php' );

class List_karyawan extends Group_Controller {
    public function __construct(){
		parent::__construct();
	}

    function index_get(){
		$data = [];

        $date_now = date('Y-m-d');
		$date_kemarin = date('Y-m-d', strtotime($date_now . ' -1 months'));
		
		$pisah_date_now = explode('-',$date_now);
		$tahun1 = $pisah_date_now[0];
		$bulan1 = $pisah_date_now[1];
		$tahun_bulan = $tahun1."_".$bulan1;
		
		$pisah_date_kemarin	= explode('-',$date_kemarin);
		$tahun2 = $pisah_date_kemarin[0];
		$bulan2 = $pisah_date_kemarin[1];
		$tahun_bulan_kemarin= $tahun2."_".$bulan2;
		
		$tabel_master = 'erp_master_data_'.$tahun_bulan;
        $name = str_replace("-","_",$tabel_master);
		$query = $this->db->query("show tables like '$name'")->row_array();
		if(!$query)
			$tabel_master = 'erp_master_data';
		
		$tabel_master_kemarin = 'erp_master_data_'.$tahun_bulan_kemarin;
        $name_kemarin = str_replace("-","_",$tabel_master_kemarin);
		$query_kemarin = $this->db->query("show tables like '$name_kemarin'")->row_array();
		if(!$query_kemarin)
			$tabel_master_kemarin = 'erp_master_data';

        # query sekarang
        $this->db->select('a.np_karyawan, a.nama, a.personnel_number as personel_number, a.nama_jabatan, a.kode_unit, a.nama_unit');
        if( $this->id_group=='5' ){ # pengguna
			$this->db->where('a.np_karyawan', $this->data_karyawan->np_karyawan);
		} else if( $this->id_group=='4' ){ # pengadministrasi
			$list_pengadministrasi = array_column($this->list_pengadministrasi, 'kode_unit');
			$this->db->where_in('a.kode_unit', $list_pengadministrasi);
		} else if( $this->id_group=='1' ){ # superadmin
			# gak ada filter
		} else{
			$this->db->where('a.id', null);
		}
        $this->db->group_by('a.np_karyawan');
        $compile_1 = $this->db->get_compiled_select("$tabel_master a");
        # END query sekarang

        # query kemarin
        $this->db->select('b.np_karyawan, b.nama, b.personnel_number as personel_number, b.nama_jabatan, b.kode_unit, b.nama_unit');
        if( $this->id_group=='5' ){ # pengguna
			$this->db->where('b.np_karyawan', $this->data_karyawan->np_karyawan);
		} else if( $this->id_group=='4' ){ # pengadministrasi
			$list_pengadministrasi = array_column($this->list_pengadministrasi, 'kode_unit');
			$this->db->where_in('b.kode_unit', $list_pengadministrasi);
		} else if( $this->id_group=='1' ){ # superadmin
			# gak ada filter
		} else{
			$this->db->where('b.id', null);
		}
        $this->db->group_by('b.np_karyawan');
        $compile_2 = $this->db->get_compiled_select("$tabel_master_kemarin b");
        # END query kemarin

		$this->db->select('c.*');
		$this->db->group_by('c.np_karyawan');
		$get = $this->db->get("($compile_1 union all $compile_2) c")->result_array();

		$this->response([
			'status'=>true,
			'message'=>'List karyawan',
			'data'=>$get
		], MY_Controller::HTTP_OK);
	}
}