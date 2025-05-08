<?php
	function get_tabel($tgl)
	{			
		$bulan = date('Y_m', strtotime($tgl));
		$ci =& get_instance();
        $table_schema = $ci->db->database;
		$table = $this->db->query('SELECT count(*) as htg FROM information_schema.tables WHERE table_schema = "'.$table_schema.'" AND table_name = "ess_cico_'.$bulan.'"')->row_array();
		if($table['htg'] == 1) {
			$table_name = "ess_cico_".$bulan;
		}
		else {
			$table_name = "ess_cico";
		}

		return $table_name;
	}
	
?>