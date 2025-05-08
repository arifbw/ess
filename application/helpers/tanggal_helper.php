<?php

//
function tanggal_indonesia($tanggal=null)
{
	if ($tanggal != null) {
		//merubah format 2015-01-13
		$tanggal=explode("-",$tanggal);
		$taketgl = $tanggal[2];
		$tahun = $tanggal[0];
		$bulan = $tanggal[1];

			switch ($bulan) {
			case "01":
				$bulan = "Januari";
				break;
			case "02":
			   $bulan = "Februari";
				break;
			case "03":
				$bulan = "Maret";
				break;
			case "04":
				$bulan = "April";
				break;
			case "05":
				$bulan = "Mei";
				break;
			case "06":
				$bulan = "Juni";
				break;
			case "07":
				 $bulan = "Juli";
				break;
			case "08":
				$bulan = "Agustus";
				break;
			case "09":
				$bulan = "September";
				break;
			case "10":
				$bulan = "Oktober";
				break;
			case "11":
				$bulan = "November";
				break;
			case "12":
				$bulan = "Desember";
				break;
			default:			
		}
		
		$tgl = $taketgl." ".$bulan." ".$tahun;
		
		if($tgl) {
			return $tgl;
		} else {
			return ''; 
		}
	} else {
			return ''; 
	}
}

//
function datetime_indo($tanggal_)
{
	//merubah format 2015-01-13
	$tanggal=explode("-",date('Y-m-d', strtotime($tanggal_)));
	$taketgl = $tanggal[2];
	$tahun = $tanggal[0];
	$bulan = $tanggal[1];

	switch ($bulan) {
		case "01":
			$bulan = "Januari";
			break;
		case "02":
		   $bulan = "Februari";
			break;
		case "03":
			$bulan = "Maret";
			break;
		case "04":
			$bulan = "April";
			break;
		case "05":
			$bulan = "Mei";
			break;
		case "06":
			$bulan = "Juni";
			break;
		case "07":
			 $bulan = "Juli";
			break;
		case "08":
			$bulan = "Agustus";
			break;
		case "09":
			$bulan = "September";
			break;
		case "10":
			$bulan = "Oktober";
			break;
		case "11":
			$bulan = "November";
			break;
		case "12":
			$bulan = "Desember";
			break;
		default:			
	}
	$tgl = $taketgl." ".$bulan." ".$tahun." ".date('H:i', strtotime($tanggal_));
	
	if(@$tgl)
		return $tgl;
	else
		return ''; 
}


function bulan($input)
	{
	$tanggal=substr($input,0,10);
	
	$tanggal=explode("-",$tanggal);

	$bulan = $tanggal[1];

	switch ($bulan) {
		case "01":
			$bulan = "Januari";
			break;
		case "02":
		   $bulan = "Februari";
			break;
		case "03":
			$bulan = "Maret";
			break;
		case "04":
			$bulan = "April";
			break;
		case "05":
			$bulan = "Mei";
			break;
		case "06":
			$bulan = "Juni";
			break;
		case "07":
			 $bulan = "Juli";
			break;
		case "08":
			$bulan = "Agustus";
			break;
		case "09":
			$bulan = "September";
			break;
		case "10":
			$bulan = "Oktober";
			break;
		case "11":
			$bulan = "November";
			break;
		case "12":
			$bulan = "Desember";
			break;
		default:
			
		}

	$bln =$bulan;

	return $bln;
	}
	
function id_to_bulan($input)
	{
	
	$bulan = $input;

	switch ($bulan) {
		case "01":
			$bulan = "Januari";
			break;
		case "02":
		   $bulan = "Februari";
			break;
		case "03":
			$bulan = "Maret";
			break;
		case "04":
			$bulan = "April";
			break;
		case "05":
			$bulan = "Mei";
			break;
		case "06":
			$bulan = "Juni";
			break;
		case "07":
			 $bulan = "Juli";
			break;
		case "08":
			$bulan = "Agustus";
			break;
		case "09":
			$bulan = "September";
			break;
		case "10":
			$bulan = "Oktober";
			break;
		case "11":
			$bulan = "November";
			break;
		case "12":
			$bulan = "Desember";
			break;
		default:
			
		}

	$bln =$bulan;

	return $bln;
	}

function tanggalindo($tanggal)
	{
	$tanggal=explode(" ",$tanggal);
	$taketgl = $tanggal[0];
	$tahun = $tanggal[2];
	$bulan = $tanggal[1];

		switch ($bulan) {
		case "January":
			$bulan = "Januari";
			break;
		case "February":
		   $bulan = "Februari";
			break;
		case "March":
			$bulan = "Maret";
			break;
		case "April":
			$bulan = "April";
			break;
		case "May":
			$bulan = "Mei";
			break;
		case "June":
			$bulan = "Juni";
			break;
		case "July":
			 $bulan = "Juli";
			break;
		case "August":
			$bulan = "Agustus";
			break;
		case "September":
			$bulan = "September";
			break;
		case "October":
			$bulan = "Oktober";
			break;
		case "November":
			$bulan = "November";
			break;
		case "December":
			$bulan = "Desember";
			break;
		default:
			
		}
	$tgl = $taketgl." ".$bulan." ".$tahun;

	return $tgl;
	}
	
	function ubah_dari_tanggal_id($tanggal)
	{
	
	$taketgl = substr($tanggal,0,2);
	$bulan = substr($tanggal,2,2);
	$tahun = substr($tanggal,4,2);

		switch ($bulan) {
		case "01":
			$bulan = "Januari";
			break;
		case "02":
		   $bulan = "Februari";
			break;
		case "03":
			$bulan = "Maret";
			break;
		case "04":
			$bulan = "April";
			break;
		case "05":
			$bulan = "Mei";
			break;
		case "06":
			$bulan = "Juni";
			break;
		case "07":
			 $bulan = "Juli";
			break;
		case "08":
			$bulan = "Agustus";
			break;
		case "09":
			$bulan = "September";
			break;
		case "10":
			$bulan = "Oktober";
			break;
		case "11":
			$bulan = "November";
			break;
		case "12":
			$bulan = "Desember";
			break;
		default:			
		}
		
		$tgl = $taketgl." ".$bulan." 20".$tahun;

		return $tgl;
	}
	
	function tanggal_ke_id($tanggal)
	{
	
		$tanggal=explode("-",$tanggal);
		$tahun = $tanggal[0];
		$bulan = $tanggal[1];
		$taketgl = $tanggal[2];

			
		$tgl = $taketgl."".$bulan."".$tahun;

		return $tgl;
	}
	
	function tanggal_ke_hari($tanggal)
	{
		$day = date('D', strtotime($tanggal));
		$dayList = array(
			'Sun' => 'Minggu',
			'Mon' => 'Senin',
			'Tue' => 'Selasa',
			'Wed' => 'Rabu',
			'Thu' => 'Kamis',
			'Fri' => 'Jumat',
			'Sat' => 'Sabtu'
		);
		return $dayList[$day];
	}
	
	function dateRange($s, $e)
	{
        $s = strtotime($s);
        $e = strtotime($e);
 
        return ($e - $s)/ (24 *3600);
	}
 
    function validateDateTime($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
 
    function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
 
    function validateMonth($date, $format = 'Y-m') {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
	
	function indonesia_to_date($tanggal)
	{
	//merubah format 03 Juni 2020
	$tanggal=explode(" ",$tanggal);
	$taketgl = $tanggal[0];
	$bulan = $tanggal[1];
	$tahun = $tanggal[2];
	

		switch ($bulan) {
		case "Januari":
			$bulan = "01";
			break;
		case "Februari":
		   $bulan = "02";
			break;
		case "Maret":
			$bulan = "03";
			break;
		case "April":
			$bulan = "04";
			break;
		case "Mei":
			$bulan = "05";
			break;
		case "Juni":
			$bulan = "06";
			break;
		case "Juli":
			 $bulan = "07";
			break;
		case "Agustus":
			$bulan = "08";
			break;
		case "September":
			$bulan = "09";
			break;
		case "Oktober":
			$bulan = "10";
			break;
		case "November":
			$bulan = "11";
			break;
		case "Desember":
			$bulan = "12";
			break;
		default:			
		}
	$tgl = $tahun."-".$bulan."-".sprintf("%02d", $taketgl);
	
		if($tgl)
		{
			return $tgl;
		}else
		{
			return ''; 
		}
	}

	function Ym_to_MY($input){
		$explode = explode('-',$input);
		$tahun = $explode[0];
		$b = $explode[1];
		switch ($b) {
			case "01":
				$bulan = "Januari";
				break;
			case "02":
			   $bulan = "Februari";
				break;
			case "03":
				$bulan = "Maret";
				break;
			case "04":
				$bulan = "April";
				break;
			case "05":
				$bulan = "Mei";
				break;
			case "06":
				$bulan = "Juni";
				break;
			case "07":
				 $bulan = "Juli";
				break;
			case "08":
				$bulan = "Agustus";
				break;
			case "09":
				$bulan = "September";
				break;
			case "10":
				$bulan = "Oktober";
				break;
			case "11":
				$bulan = "November";
				break;
			case "12":
				$bulan = "Desember";
				break;
			default:
				$bulan = "";
		}
	
		return "$bulan $tahun";
	}

	function bulan_to_romawi($bulan){
		$list = ['','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
		$bulan_int = (int)$bulan;
		return $list[$bulan_int];
	}

	function periode_perencanaan_lembur($startDate, $periodsCount) {
		$periods = [];
		$currentDate = strtotime($startDate);
	
		for ($i = 0; $i < $periodsCount; $i++) {
			$startPeriod = date("Y-m-d", $currentDate);
			$endPeriod = date("Y-m-d", strtotime("+6 days", $currentDate)); // Add 6 days because the start date is included
			$periods[] = [
				'periode' => "Periode ". tanggal_indonesia($startPeriod) ." s/d ". tanggal_indonesia($endPeriod),
				'start_date' => $startPeriod,
				'end_date' => $endPeriod,
			];
			$currentDate = strtotime("+1 day", strtotime($endPeriod)); // Move to the next start date
		}
	
		return $periods;
	}

	function get_surrounding_periods($staticStartDate, $currentDate, $periodsBefore, $periodsAfter) {
		$periods = [];
		$startDate = strtotime($staticStartDate);
		$today = strtotime($currentDate);
		
		// Calculate the difference in days from the static start date to the current date
		$daysDifference = floor(($today - $startDate) / (60 * 60 * 24));
		
		// Calculate the current period number
		$currentPeriod = floor($daysDifference / 7) + 1;
		
		// Calculate the start period for generating output
		$startPeriod = $currentPeriod - $periodsBefore;
	
		for ($i = 0; $i < $periodsBefore + $periodsAfter + 1; $i++) {
			$periodStartDate = date("Y-m-d", strtotime("+".(7 * ($startPeriod + $i - 1))." days", $startDate));
			$periodEndDate = date("Y-m-d", strtotime("+6 days", strtotime($periodStartDate)));
			
			$periods[] = [
				'periode' => "Periode ". tanggal_indonesia($periodStartDate) ." s/d ". tanggal_indonesia($periodEndDate),
				'start_date' => $periodStartDate,
				'end_date' => $periodEndDate,
			];
		}
	
		return $periods;
	}
	
?>