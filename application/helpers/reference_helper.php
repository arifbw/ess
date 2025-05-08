<?php
function uraian_by_code($code)
	{			
		$ci =& get_instance();	
		$ambil_data = $ci->db->query("SELECT * FROM mst_reference WHERE code='$code'")->row_array();
		$ambil = $ambil_data['uraian'];

		return $ambil ;
	}	

function get_jadwal_from_dws($dws){
    $ci =& get_instance();
    $get = $ci->db->select('description')->where('dws',$dws)->get('mst_jadwal_kerja')->row_array()['description'];
    return $get;
}
?>