<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="robots" content="noindex, nofollow">


	<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
	<?php
	foreach ($meta_header as $key => $value) {
		echo "<meta name='$key' content='$value'/>";
	}
	?>
	<title>
		<?php
		echo $title;
		if (!empty($judul)) {
			echo " | $judul";
		}
		?>
	</title>
	<link rel="icon" href="<?php echo base_url() . "asset/icon/ess.png?" . date("YmdHis") ?>">

	<?php
	foreach ($themes_css as $css) {
		echo "<link href='" . base_url($css) . "?" . date("YmdHis") . "' rel='stylesheet'/>";
	}

	foreach ($themes_js as $js) {
		echo "<script type='text/javascript' src='" . base_url($js) . "?" . date("YmdHis") . "'></script>";
	}

	if (isset($css_sources)) {
		foreach ($css_sources as $css) {
			echo "<link href='" . base_url($css) . "?" . date("YmdHis") . "' rel='stylesheet'/>";
		}
	}

	if (isset($js_sources)) {
		foreach ($js_sources as $js) {
			echo "<script type='text/javascript' src='" . base_url($folder_js . "$js.js?") . date("YmdHis") . "'></script>";
		}
	}

	if (isset($css_script)) {
		foreach ($css_script as $css) {
			echo $css;
		}
	}

	if (isset($css_plugin_sources)) {
		foreach ($css_plugin_sources as $css) {
			echo "<link href='" . base_url($folder_asset . $css) . "?" . date("YmdHis") . "' rel='stylesheet'/>";
		}
	}

	foreach ($js_plugin_sources as $js) {
		echo "<script type='text/javascript' src='" . base_url($folder_asset . $js) . "?" . date("YmdHis") . "'></script>";
	}

	if (isset($js_header_script)) {
		foreach ($js_header_script as $js) {
			echo $js;
		}
	}
	?>
</head>

<body>
	<div id="wrapper">
