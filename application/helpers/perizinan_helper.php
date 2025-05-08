<?php
    function get_out_perizinan($table, $np, $tapping_time) {
        //perlu dicek lagi
		$ci =& get_instance();
        $where = [
            'no_pokok'=>$np,
            'in_out'=>'0',
            "DATE_FORMAT(tapping_time,'%Y-%m-%d')"=>$tapping_time
        ];
		$ambil_data = $ci->db->select("tapping_time")->where($where)->order_by('tapping_time', 'DESC')->limit(1)->get($table)->row_array();
		$ambil = $ambil_data['tapping_time'];

		return $ambil ;
	}

	function get_perizinan_name($tapping_type) {
        $ci =& get_instance();
        $get = $ci->db->select('nama, kode_pamlek')->where('kode_pamlek', $tapping_type)->get('mst_perizinan')->row();

		return $get;
	}

	function convert_pamlek_to_erp($tapping_type) {
        $ci =& get_instance();
        $get = $ci->db->select('kode_erp')->where('kode_pamlek', $tapping_type)->get('mst_perizinan')->row();
        $pisah = explode('|', $get->kode_erp);
        $result = [
            'info_type'=>$pisah[0],
            'absence_type'=>$pisah[1]
        ];
		return $result;
	}

	function get_mst_attendance($kode=null, $biaya=null) {
        if(@$kode!=null){
            $info_type = '2002';
            if($kode=='LN'){
                $absence_type = '6120';
            } else {
				$absence_type = '6100';
				/*
                if($biaya==0 || $biaya==null){
                    $absence_type = '6100';
                } else if($biaya>0){
                    $absence_type = '6110';
                }
				*/
            }
            
            $result = [
                'info_type'=>$info_type,
                'absence_type'=>$absence_type
            ];
            
        } else {
            $result = [
                'info_type'=>'',
                'absence_type'=>''
            ];
        }
        return $result;
	}
	
	function perizinan_by_id($tahun_bulan, $id)
	{					
		$ci =& get_instance();	
		
		$tabel = "ess_perizinan_".$tahun_bulan;
		
	
		
		$name=str_replace("-","_",$tabel);
		$query = $ci->db->query("show tables like '$name'")->row_array();
		
		if(!$query)
		{
			$tabel = "ess_perizinan";
		}
			
		$ambil_data = $ci->db->query("SELECT * FROM  $tabel WHERE id='$id'")->row_array();
		
		return $ambil_data;
	}
	
	function nama_perizinan_by_kode_erp($kode_erp)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT * FROM mst_perizinan WHERE kode_erp='$kode_erp'")->row_array();
		
		return $ambil_data['nama'] ;
	}
	
	function hari_libur_by_tanggal($tanggal)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT deskripsi FROM mst_hari_libur WHERE tanggal='$tanggal'")->row_array();
		
		return $ambil_data['deskripsi'] ;
	}

    function status_perizinan($params){
        $return = '';
        /*
        $params = [
            'kode_pamlek'=>value,
            'approval_1_status'=>value,
            'approval_2_status'=>value,
            'is_machine'=>value,
            'pengguna_status'=>value
        ]
        */
        
        if($params['is_machine']=='1'){
            $return = 'Disetujui';
        } else{
            if($params['pengguna_status']=='3'){
                $return = 'Dibatalkan';
            } else{
                if($params['approval_1_status']==null){
                    $return = 'Menunggu Atasan 1';
                } else if($params['approval_1_status']=='1'){
                    # jenis izin [3,4,6,7] / [H,C,F,E]
                    if(in_array($params['kode_pamlek'],['H','C','F','E'])){
                        if($params['approval_2_status']==null){
                            $return = 'Menunggu Atasan 2';
                        } else if($params['approval_2_status']=='1'){
                            $return = 'Disetujui';
                        } else if($params['approval_2_status']=='2'){
                            $return = 'Ditolak Atasan 2';
                        }
                    } else{
                        $return = 'Disetujui';
                    }
                } else if($params['approval_1_status']=='2'){
                    $return = 'Ditolak Atasan 1';
                }
            }
        }
        
        return $return;
    }
?>