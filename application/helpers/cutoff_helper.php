<?php

//
function sudah_cutoff($tanggal)
	{
		$ci =& get_instance();
		$ci->load->model("administrator/m_pengaturan");
		

		//cutoff ERP
		$cutoff_erp_aktif 		= $ci->m_pengaturan->ambil_isi_pengaturan('cutoff_erp_aktif');
		$cutoff_erp_tanggal  	= $ci->m_pengaturan->ambil_isi_pengaturan('cutoff_erp_tanggal');	
		
		//satu bulan berikutnya
		$tgl	= explode('-',$tanggal);
		$tgl_tahun		= $tgl[0];
		$tgl_bulan		= $tgl[1];
		
		$tgl_tahun_bulan = $tgl_tahun."-".$tgl_bulan;
		
		$data_tanggal	= date('Y-m',strtotime('+1 months',strtotime($tgl_tahun_bulan)));
		$olah	= explode('-',$data_tanggal);
		$data_tahun		= $olah[0];
		$data_bulan		= $olah[1];
				
		$cutoff_erp_tanggal 	= $data_tahun."-".$data_bulan."-".$cutoff_erp_tanggal;
		$cutoff_erp_tanggal		= date('Y-m-d', strtotime($cutoff_erp_tanggal));

		if($cutoff_erp_aktif=='1' && date('Y-m-d') > $cutoff_erp_tanggal) //jika sudah lewat masa cutoff
		{
			return $cutoff_erp_tanggal;
		}else
		{
			return false;
		}
	
/*		
		// Percepat Cut OFF
		if($tanggal<'2020-01-01' && $cutoff_erp_aktif=='1')
		{
			return $cutoff_erp_tanggal;
		}else
		{
			return false;
		}
	*/	
		
	}


?>