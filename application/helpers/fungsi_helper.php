<?php
function tanggal($date)
{
	$return = "";

	if (strlen($date) > 0) {
		$arr_bulan = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

		$return = (int)substr($date, 8, 2) . " " . $arr_bulan[(int)substr($date, 5, 2)] . " " . (int)substr($date, 0, 4);
	}
	return $return;
}

function hari_tanggal($date)
{
	$return = "";
	if (strlen($date) > 0) {
		$arr_hari = array("", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu");
		$return = $arr_hari[date("N", strtotime($date))] . ", " . tanggal($date);
	}
	return $return;
}

function tanggal_waktu($datetime)
{
	$return = "";
	if (strlen($datetime) > 0) {
		$return = tanggal(substr($datetime, 0, 10)) . " " . substr($datetime, 11, 8);
	}
	return $return;
}

function bulan_tahun($date)
{
	$return = "";
	if (strlen($date) > 0) {
		$tanggal = tanggal($date);
		$return = substr($tanggal, strpos($tanggal, " ") + 1);
	}

	return $return;
}

function get_tables_from_schema($keyword)
{
	$ci = &get_instance();
	$nama_db = $ci->db->database;
	$arrays = [];
	$get = $ci->db->select('TABLE_NAME')->where("TABLE_SCHEMA", "$nama_db")->like('TABLE_NAME', $keyword, 'AFTER')->order_by('TABLE_NAME', 'DESC')->get('information_schema.TABLES')->result_array();
	foreach ($get as $row) {
		$arrays[] = $row['TABLE_NAME'];
	}
	return $arrays;
}

function check_table_exist($table_name)
{
	$ci = &get_instance();
	$nama_db = $ci->db->database;
	$get = $ci->db->where('TABLE_SCHEMA', $nama_db)->where('TABLE_NAME', $table_name)->get('information_schema.TABLES');

	$result = ($get->num_rows() > 0 ? 'ada' : 'belum ada');

	return $result;
}

function get_machine($type)
{
	$ci = &get_instance();
	$get = $ci->db->select('isi')->where('id', 12)->get('sys_pengaturan')->row();

	if ($type == 'array') {
		$result = explode(',', $get->isi);
	} else if ($type == 'string') {
		$pisah = explode(',', $get->isi);
		$result = "'" . implode("','", $pisah) . "'";
	}

	return $result;
}

function date_minus_days($get_date, $count_day)
{
	$date = date_create($get_date);
	date_sub($date, date_interval_create_from_date_string("$count_day days"));
	return date_format($date, "Y-m-d");
}

function coming_soon()
{
	$ci = &get_instance();
	$ci->load->model("administrator/m_setting");

	if (strcmp($ci->m_setting->ambil_pengaturan("Coming Soon"), "true") == 0) {
		if (!in_array($_SESSION["kode_unit"], explode(",", $ci->m_setting->ambil_pengaturan("Unit Kerja Developer"))) and !in_array($_SESSION["browse_as_kode_unit_original"], explode(",", $ci->m_setting->ambil_pengaturan("Unit Kerja Developer")))) {
			if (strcmp($ci->m_setting->ambil_pengaturan("Tanggal Launching"), date("Y-m-d")) > 0) {
				redirect(base_url("coming_soon"));
			}
		}
	}
}

function periode()
{
	$arr_tabel = get_tables_from_schema("ess_cico_");
	$arr_periode = array();
	for ($i = 0; $i < count($arr_tabel); $i++) {
		$arr_periode[$i]["value"] = str_replace("ess_cico_", "", $arr_tabel[$i]);
		$arr_periode[$i]["text"] = bulan_tahun(str_replace("ess_cico_", "", $arr_tabel[$i]) . "_01");
	}
	return $arr_periode;
}

function periode_tahun()
{
	$arr_tabel = get_tables_from_schema("ess_cico_");
	$arr_periode = array();
	for ($i = 0; $i < count($arr_tabel); $i++) {
		if (!in_array(substr(str_replace("ess_cico_", "", $arr_tabel[$i]), 0, 4), $arr_periode)) {
			array_push($arr_periode, substr(str_replace("ess_cico_", "", $arr_tabel[$i]), 0, 4));
		}
	}

	return $arr_periode;
}

function rupiah($value, $rp = '')
{
	if ($value == '0' || empty($value)) {
		if (!$rp) {
			$formated = 0;
			return $formated;
		} else {
			$formated = 'Rp. 0';
			return $formated;
		}
	} elseif ($value) {
		if (!$rp) {
			$formated = str_replace(',', '.', number_format($value));
			return $formated;
		} else {
			$formated = 'Rp. ' . str_replace(',', '.', number_format($value));
			return $formated;
		}
	} else {
		return '';
	}
}

function level_unit($kode_unit)
{
	if (substr($kode_unit, -4) == '0000') {
		$level_unit = 1; // dir
	} else if (substr($kode_unit, -3) == '000' && substr($kode_unit, 1) != '0') {
		$level_unit = 2; // div
	} else if (substr($kode_unit, -2) == '00' && substr($kode_unit, 2) != '0') {
		$level_unit = 3; // dep
	} else if (substr($kode_unit, -1) == '0' && substr($kode_unit, 3) != '0') {
		$level_unit = 4; // sek
	} else if (substr($kode_unit, 4) != '0') {
		$level_unit = 5; // unit
	}
	return $level_unit;
}

function array_multi_search($array, $search)
{
	// Create the result array
	$result = array();

	// Iterate over each array element
	foreach ($array as $key => $value) {
		// Iterate over each search condition
		foreach ($search as $k => $v) {
			// If the array element does not meet the search condition then continue to the next element
			if (!isset($value[$k]) || $value[$k] != $v) {
				continue 2;
			}
		}
		// Add the array element's key to the result array
		$result[] = $key;
	}
	// Return the result array
	return $result;
}

function search_array_by_keys($array, $keys, $searchValues)
{
	foreach ($array as $item) {
		$matchedCount = 0;
		foreach ($keys as $index => $key) {
			if (isset($item[$key]) && $item[$key] === $searchValues[$index]) {
				$matchedCount++;
			}
		}
		if ($matchedCount === count($keys)) {
			return $item;
		}
	}
	return null; // Return null if no match is found
}

function format_bytes($bytes)
{
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';
	} elseif ($bytes >= 1048576) {
		$bytes = number_format($bytes / 1048576, 2) . ' MB';
	} elseif ($bytes >= 1024) {
		$bytes = number_format($bytes / 1024, 2) . ' KB';
	} elseif ($bytes > 1) {
		$bytes = $bytes . ' bytes';
	} elseif ($bytes == 1) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}
	return $bytes;
}

if (!function_exists('dd')) {
	function dd($data, $json = true)
	{
		if ($json) {
			header('Content-Type: application/json');
			echo json_encode($data, JSON_PRETTY_PRINT);
		} else {
			echo '<pre>';
			var_dump($data);
			echo '</pre>';
		}
		die();
	}
}

if (!function_exists('format_time')) {
	/**
	 * Format seconds into human-readable time (e.g., 1 minute, 12 seconds).
	 *
	 * @param float $seconds The time in seconds.
	 * @return string The formatted time string.
	 */
	function format_time($seconds)
	{
		$hours = floor($seconds / 3600);
		$minutes = floor(($seconds % 3600) / 60);
		$remaining_seconds = $seconds % 60;

		$time_parts = [];
		if ($hours > 0) {
			$time_parts[] = $hours . " jam";
		}
		if ($minutes > 0) {
			$time_parts[] = $minutes . " menit";
		}
		if ($remaining_seconds > 0 || empty($time_parts)) {
			$time_parts[] = round($remaining_seconds) . " detik";
		}

		return implode(", ", $time_parts);
	}
}

if (!function_exists('debug_start_timer')) {
	/**
	 * Start the timer.
	 * Saves the start time in a global variable.
	 */
	function debug_start_timer()
	{
		$CI = &get_instance();
		$CI->start_time = microtime(true);

		// Start output buffering
		ob_start();
	}
}

if (!function_exists('debug_end_timer')) {
	/**
	 * End the timer and calculate the elapsed time.
	 * Echoes the time as a comment to the client.
	 */
	function debug_end_timer()
	{
		$CI = &get_instance();
		if (isset($CI->start_time)) {
			$elapsed_time = microtime(true) - $CI->start_time;

			$formatted_time = format_time($elapsed_time);
			$final_time = $formatted_time == "0 detik" ? round($elapsed_time, 2) . " detik" : $formatted_time;
			// Get current output buffer contents and clean it
			ob_end_clean();

			$output = "
					<style>
						body{
							background: #ffc1c1;
							color: #5a5a5a;
							display: flex;
							justify-content: center;
							align-items: center;
						}
					</style>
					<h1 style='font-size: 50px;'> Load Time: " . $final_time . "</h1>
				";

			header_remove(); // Clear all existing headers
			// Force content to be displayed (override headers if necessary)
			header("Content-type: text/html; charset=UTF-8");

			// Output the content
			echo $output;
		}

		exit;
	}

	if (!function_exists('uuid')) {
		function uuid()
		{
			return sprintf(
				'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				mt_rand(0, 0xffff),
				mt_rand(0, 0xffff),
				mt_rand(0, 0xffff),
				mt_rand(0, 0x0fff) | 0x4000,
				mt_rand(0, 0x3fff) | 0x8000,
				mt_rand(0, 0xffff),
				mt_rand(0, 0xffff),
				mt_rand(0, 0xffff)
			);
		}
	}

	function format_tanggal($tanggal)
	{
		$bulan = [
			'January' => 'Januari',
			'February' => 'Februari',
			'March' => 'Maret',
			'April' => 'April',
			'May' => 'Mei',
			'June' => 'Juni',
			'July' => 'Juli',
			'August' => 'Agustus',
			'September' => 'September',
			'October' => 'Oktober',
			'November' => 'November',
			'December' => 'Desember'
		];

		$tanggal_format = date('j F Y', strtotime($tanggal));
		return strtr($tanggal_format, $bulan);
	}
}
