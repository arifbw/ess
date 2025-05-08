<?php
function insert_login_history($data=[]){
	$ci =& get_instance();
	$ci->load->model('M_login_history');
	$logs = [
		'id' => $ci->uuid->v4(),
		// 'file' => __FILE__ . '/' . __FUNCTION__,
		'app_directory' => FCPATH,
		'modul' => 'login system',
		// 'input_from' => 'web',
		'ip_address' => getenv('HTTP_CLIENT_IP') ?: getenv('HTTP_X_FORWARDED_FOR') ?: getenv('HTTP_X_FORWARDED') ?: getenv('HTTP_FORWARDED_FOR') ?: getenv('HTTP_FORWARDED') ?: getenv('REMOTE_ADDR'),
		'user_agent' => $_SERVER['HTTP_USER_AGENT'],
		'timestamp' => date('Y-m-d H:i:s')
	];

	if($data!=[]){
		$logs = array_merge($logs,$data);
	}

	$ci->M_login_history->insert_log($logs);
}