<?php
function menu_helper()
{
	$object = get_instance();
	$object->load->model("m_menu");
	$id_grup = $object->session->userdata("grup");

	$daftar_menu_grup_pengguna = $object->m_menu->daftar_menu_grup_pengguna($id_grup);
	for ($i = 0; $i < count($daftar_menu_grup_pengguna); $i++) {
		$menu[$daftar_menu_grup_pengguna[$i]["shortcode_posisi_menu"]] = $object->m_menu->get_menu($daftar_menu_grup_pengguna[$i]["id_master_menu"]);
	}
	//die();

	if($id_grup=='5'){
		$kode_jabatan = $object->session->userdata("kode_jabatan");
		if(substr($kode_jabatan, -3)!='300' && isset($menu['kiri'])){
			$kiri = $menu['kiri'];
			$exclude_url = ['ijt/monitoring'];
			$filtered_kiri = array_filter($kiri, function($e) use($exclude_url){
				return !in_array($e['url'], $exclude_url);
			});
			$menu['kiri'] = array_values($filtered_kiri);
		}
	}
	return $menu;
}

function menu_helper_mobile($id_grup)
{
	$object = get_instance();
	$object->load->model("api/m_menu_api", 'm_menu');

	$daftar_menu_grup_pengguna = $object->m_menu->daftar_menu_grup_pengguna($id_grup);
	for ($i = 0; $i < count($daftar_menu_grup_pengguna); $i++) {
		$menu[$daftar_menu_grup_pengguna[$i]["shortcode_posisi_menu"]] = $object->m_menu->get_menu($daftar_menu_grup_pengguna[$i]["id_master_menu"]);
	}
	if (isset($menu['kiri']) && is_array($menu['kiri'])) {
		foreach ($menu['kiri'] as $index => $item) {
			if (isset($item['url']) && $item['url'] == 'ijt/agenda') {
				// Tambahkan pengecekan kelayakan pengguna
				if (!$object->cek_kelayakan_pengguna($id_pengguna)) {
					unset($menu['kiri'][$index]);
				}
				break; // Keluar dari loop jika sudah ketemu
			}
		}
	}
	//die();
	return $menu;
}
