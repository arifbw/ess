<?php

function generate_nomor_pemesanan_konsumsi() { //created 2020-05-15
	//19 01 2022 Tri Wibowo 7648 Bug Fixing kenapa tidak increment
	//asumsi jika nomor lembur dan rapat adalah jadi 1 urutan
	
    $ci =& get_instance();
    $return = '';
    
    # get latest order: select id,nomor_pemesanan
    $latest_lembur = $ci->db->select('id, nomor_pemesanan')->where('YEAR(created)',date('Y'))->order_by('id','DESC')->limit(1)->get('ess_pemesanan_makan_lembur');
    $latest_rapat = $ci->db->select('id, nomor_pemesanan')->where('YEAR(created)',date('Y'))->order_by('id','DESC')->limit(1)->get('ess_pemesanan_konsumsi_rapat');
	
	$pecah_nomer_lembur = explode("/", $latest_lembur->row()->nomor_pemesanan);
	$nomor_pemesanan_lembur = $pecah_nomer_lembur[0];
	
	$pecah_nomer_rapat 		= explode("/", $latest_rapat->row()->nomor_pemesanan);
	$nomor_pemesanan_rapat	 = $pecah_nomer_rapat[0];
	
	
    if ($nomor_pemesanan_lembur >= $nomor_pemesanan_rapat) {
        $latest = $nomor_pemesanan_lembur;
    } else {
        $latest = $nomor_pemesanan_rapat;
    }

    if($latest_lembur->num_rows()>0 || $latest_rapat->num_rows()>0){
        $data = $latest+1;
        $return = $data.'/FOOD/'.date('m/Y');
    } else{
        $return = '1/FOOD/'. date('m/Y');
    }
    
    return $return;
}

?>