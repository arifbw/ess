<!-- Navigation -->
<style>
	@media (min-width: 768px) {
		.sidebar {
			margin-top: 0px;
			top: 0px;
			z-index: 1001;
			box-shadow: 0px 2px 10px 1px rgba(0, 0, 0, 0.5);
			height: 100vh;
			position: fixed;
		}

		#side-menu{
			max-height: 400px;
    		overflow-y: auto;
		}

		.sidebar-nav > img{
			width: 100%;
		}
	}
</style>
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="background: #f9f1e1; margin-bottom: 0; position: sticky; top: 0; box-shadow: 2px 0px 10px 1px rgba(0,0,0,0.5);">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="<?php echo base_url(); ?>" style="font-weight: 900;" title="<?php echo $title; ?>"><?php echo $title; ?></a>
	</div>
	<!-- /.navbar-header -->

	<ul class="nav navbar-top-links navbar-right">
		<?php
			echo "<input type='hidden' id='base_url' value='" . base_url() . "'/>";
			echo "<input type='hidden' id='url_log' value='" . base_url($url_log) . "'/>";
			echo "<input type='hidden' id='id_modul' value='$id_modul'/>";
			echo "<input type='hidden' id='judul' value='$judul'/>";
		?>

		<li class="dropdown" style="margin-right: 0px; background: #ffe4c6;">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#" style="padding: 10px 15px;">
				<img src="<?= $_SESSION["foto_profile"] . '?' . date("YmdHis") ?>" class="img-circle" width="25" height="25" style="margin-right: 5px;">
				<?php
					$nama_lengkap = $_SESSION["nama"];
					$nama_depan = explode(" ", $nama_lengkap)[0];
					echo "Hi, {$nama_depan} 👋";
				?>
				<i class="fa fa-caret-down"></i>
			</a>
			<ul class="dropdown-menu dropdown-user" style="min-width: 300px;">
				<li class="text-center" style="padding: 10px; display: flex; flex-direction: column; align-items: center;">
					<img src="<?= $_SESSION["foto_profile"] . '?' . date("YmdHis") ?>" class="img-thumbnail" style="margin-bottom: 10px;" width="80" height="80"><br>
					<strong><?= $_SESSION["no_pokok"] ?> - <?= $_SESSION["nama"] ?></strong>
					<div style="font-size: 12px; width: 80%;"><?= $_SESSION["kode_unit"] ?> - <?= $_SESSION["nama_unit"] ?></div>
				</li>
				<li class="divider"></li>
				<li style="padding: 10px;">
					<label><b>Peran :</b></label>

					<select class="form-control" id="set_session_grup" onclick='event.stopPropagation();' onchange="set_session()">
						<?php
						$arr_session_id_grup = explode("|", $_SESSION["list_id_grup"]);
						$arr_session_nama_grup = explode("|", $_SESSION["list_nama_grup"]);
						foreach ($arr_session_id_grup as $index => $id_grup) {
							$selected = ($_SESSION["grup"] == $id_grup) ? "selected" : "";
							echo "<option value='$id_grup' $selected>{$arr_session_nama_grup[$index]}</option>";
						}
						?>
					</select>
				</li>
				<li class="divider"></li>
				<?php
					if (isset($navigasi_menu["atas"])) {
						$arr_ordinal = array("first", "second", "third");
						for ($i = 0; $i < count($navigasi_menu["atas"]); $i++) {
							echo "<li>";
							if (strcmp($navigasi_menu["atas"][$i]["url"], "#") != 0) {
								$url = base_url($navigasi_menu["atas"][$i]["url"]);
							} else {
								$url = $navigasi_menu["atas"][$i]["url"];
							}

							echo "<a href='$url'>";
							echo "<i class='fa " . $navigasi_menu["atas"][$i]["icon"] . " fa-fw'></i> ";
							echo $navigasi_menu["atas"][$i]["nama"];

							if ($i < count($navigasi_menu["atas"]) - 1 and strcmp($navigasi_menu["atas"][$i]["urutan"], $navigasi_menu["atas"][$i + 1]["urutan_induk"]) == 0) {
								echo "<span class='fa arrow'></span>";
							}
							echo "</a>";
							if ($i < count($navigasi_menu["atas"]) - 1) {
								if ((int)$navigasi_menu["atas"][$i]["level"] < (int)$navigasi_menu["atas"][$i + 1]["level"]) {
									echo "<ul class='nav nav-" . $arr_ordinal[(int)$navigasi_menu["atas"][$i + 1]["level"] - 1] . "-level'>";
								} else if ((int)$navigasi_menu["atas"][$i]["level"] > (int)$navigasi_menu["atas"][$i + 1]["level"]) {
									for ($j = 0; $j < (int)$navigasi_menu["atas"][$i]["level"] - (int)$navigasi_menu["atas"][$i + 1]["level"]; $j++) {
										echo "</li></ul>";
									}
								}
							}
							echo "</li>";
							echo '<li class="divider"></li>';
						}
					}
				?>
			</ul>
		</li>
	</ul>

	<!-- /.navbar-top-links -->

	
</nav>

<div class="navbar-default sidebar" role="navigation">
	<div class="sidebar-nav navbar-collapse">
		<img src="<?php base_url()?>/asset/cartenz/logo_ess_bg.png" alt="" srcset="">
		<ul class="nav" id="search-menu">
			<li class="sidebar-search">
				<div class="input-group custom-search-form">
					<input type="text" class="form-control" placeholder="Search..." onkeyup="cari_menu()" id="pencarian_menu">
					<span class="input-group-btn">
						<button class="btn btn-default" type="button">
							<i class="fa fa-search"></i>
						</button>
					</span>
				</div>
				<!-- /input-group -->
			</li>
			<span id="judul_hasil_cari_menu"></span>
			<span id="hasil_cari_menu"></span>
		</ul>
		<ul class="nav" id="side-menu">
			<?php
				if (isset($navigasi_menu["kiri"])) {
					$arr_ordinal = array("first", "second", "third");

					for ($i = 0; $i < count($navigasi_menu["kiri"]); $i++) {

						// if ($navigasi_menu["kiri"][$i]['url'] === 'ijt/agenda') {
						// 	if (!@$is_tampilkan_agenda) {
						// 		continue;
						// 	}
						// }
						echo "<li>";
						if (strcmp($navigasi_menu["kiri"][$i]["url"], "#") != 0) {
							$url = base_url($navigasi_menu["kiri"][$i]["url"]);
						} else {
							$url = $navigasi_menu["kiri"][$i]["url"];
						}

						echo "<a href='$url'>";
						echo "<i class='fa " . $navigasi_menu["kiri"][$i]["icon"] . " fa-fw'></i> ";
						echo $navigasi_menu["kiri"][$i]["nama"];

						if ($i < count($navigasi_menu["kiri"]) - 1 and strcmp($navigasi_menu["kiri"][$i]["urutan"], $navigasi_menu["kiri"][$i + 1]["urutan_induk"]) == 0) {
							echo "<span class='fa arrow'></span>";
						}
						echo "</a>";
						if ($i < count($navigasi_menu["kiri"]) - 1) {
							if ((int)$navigasi_menu["kiri"][$i]["level"] < (int)$navigasi_menu["kiri"][$i + 1]["level"]) {
								echo "<ul class='nav nav-" . $arr_ordinal[(int)$navigasi_menu["kiri"][$i + 1]["level"] - 1] . "-level'>";
							} else if ((int)$navigasi_menu["kiri"][$i]["level"] > (int)$navigasi_menu["kiri"][$i + 1]["level"]) {
								for ($j = 0; $j < (int)$navigasi_menu["kiri"][$i]["level"] - (int)$navigasi_menu["kiri"][$i + 1]["level"]; $j++) {
									echo "</li></ul>";
								}
							}
						}
						echo "</li>";
					}
				}
			?>
		</ul>
	</div>
	<!-- /.sidebar-collapse -->
</div>
<!-- /.navbar-static-side -->