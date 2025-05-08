<?php 
	function meta_data() {
		$object = get_instance();
		$object->load->model("m_setting");
		$arr_meta = array(
			"folder_asset" => "/asset/",
			"folder_js" => "/asset/js/",
			"folder_css" => "/asset/css/",
			"css_plugin_sources" => array(),
			"css_sources" => array(),
			"css_script" => array(),
			"js_sources" => array(),
			"js_header_script" => array(),
			"js_footer_script" => array(),
			"js_plugin_sources" => array(),
			"meta_header" => array(
				"description" => $object->m_setting->ambil_pengaturan("Deskripsi Aplikasi"),
				"author" => $object->m_setting->ambil_pengaturan("Dibuat Oleh"),
				"year" => $object->m_setting->ambil_pengaturan("Tahun Pembuatan")
				),
			"meta_footer" => array(
				"author" => $object->m_setting->ambil_pengaturan("Dibuat Oleh"),
				"year" => $object->m_setting->ambil_pengaturan("Tahun Pembuatan")
				),
			"themes" => "/asset/themes/",
			"themes_css" => array(),
			"themes_js" => array(),
			"title" => $object->m_setting->ambil_pengaturan("Nama Aplikasi"),
			"ip_address" => $_SERVER["REMOTE_ADDR"],
			"url_log" => $object->m_setting->ambil_url_modul("Log"),
			"id_modul" => "",
			"judul" => ""
		);
		
		// Bootstrap Core CSS
		array_push($arr_meta["themes_css"], $arr_meta["themes"]."vendor/bootstrap/css/bootstrap.min.css");

		// MetisMenu CSS
		array_push($arr_meta["themes_css"], $arr_meta["themes"]."vendor/metisMenu/metisMenu.min.css");
		
		// Custom CSS
		array_push($arr_meta["themes_css"], $arr_meta["themes"]."dist/css/sb-admin-2.css");

		// Morris Charts CSS
		// array_push($arr_meta["themes_css"], $arr_meta["themes"]."vendor/morrisjs/morris.css");

		// Custom Fonts
		array_push($arr_meta["themes_css"], $arr_meta["themes"]."vendor/font-awesome/css/font-awesome.min.css");

		// DataTables CSS
		array_push($arr_meta["themes_css"], $arr_meta["themes"]."vendor/datatables-plugins/dataTables.bootstrap.css");

		// DataTables Responsive CSS
		array_push($arr_meta["themes_css"], $arr_meta["themes"]."vendor/datatables-responsive/dataTables.responsive.css");

		// jQuery
		array_push($arr_meta["themes_js"], $arr_meta["themes"]."vendor/jquery/jquery.min.js");

		// Bootstrap Core JavaScript
		array_push($arr_meta["themes_js"], $arr_meta["themes"]."vendor/bootstrap/js/bootstrap.min.js");

		// Metis Menu Plugin JavaScript
		array_push($arr_meta["themes_js"], $arr_meta["themes"]."vendor/metisMenu/metisMenu.min.js");

		// Morris Charts JavaScript
		// array_push($arr_meta["themes_js"], $arr_meta["themes"]."vendor/raphael/raphael.min.js");
		// array_push($arr_meta["themes_js"], $arr_meta["themes"]."vendor/morrisjs/morris.min.js");
		// array_push($arr_meta["themes_js"], $arr_meta["themes"]."data/morris-data.js");

		// Custom Theme JavaScript
		array_push($arr_meta["themes_js"], $arr_meta["themes"]."dist/js/sb-admin-2.js");

		// DataTables JavaScript
		array_push($arr_meta["themes_js"], $arr_meta["themes"]."vendor/datatables/js/jquery.dataTables.min.js");
		array_push($arr_meta["themes_js"], $arr_meta["themes"]."vendor/datatables-plugins/dataTables.bootstrap.min.js");
		array_push($arr_meta["themes_js"], $arr_meta["themes"]."vendor/datatables-responsive/dataTables.responsive.js");
		
		array_push($arr_meta["css_sources"], $arr_meta["folder_css"]."custom.css");
		array_push($arr_meta["js_sources"], "menu");
		array_push($arr_meta["js_sources"], "akses");
		array_push($arr_meta["js_sources"], "log");
		
		if($object->session->userdata("browse_as_mode")){
			array_push($arr_meta["js_sources"], "switch_off");
		}

		return $arr_meta;
	}
?>
