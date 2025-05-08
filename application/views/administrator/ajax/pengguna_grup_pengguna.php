<?php
	if(strcmp($modal,"grup")==0){
		echo "<label>Username : $username</label>";
		echo "<br>";
		
		echo "<input type='hidden' name='username' value='$username'/>";

		if($akses["ubah grup"]){
			$disabled = "";
		}
		else{
			$disabled = "disabled='disabled'";
		}
		
		$is_pilih_unit_kerja = "";
		
		for($i=0;$i<count($daftar_grup_pengguna);$i++){
			if(in_array($daftar_grup_pengguna[$i]["id"],$arr_grup_pengguna_user)){
				$checked = "checked=checked";
			}
			else{
				$checked = "";
			}
			
			if(strpos(strtolower($daftar_grup_pengguna[$i]["nama"]),"unit kerja")!==false){
				$onchange = "onchange='tombol_pilih_unit_kerja(this)'";
			}
			else{
				$onchange = "";
			}
			
			echo "<label><input type='checkbox' name='grup_pengguna[]' value='".$daftar_grup_pengguna[$i]["id"]."' $disabled $checked $onchange/> ".$daftar_grup_pengguna[$i]["nama"]."</label>";
			
			if(strpos(strtolower($daftar_grup_pengguna[$i]["nama"]),"unit kerja")!==false){
				$warning_pengadministrasi="<span id='warning_pengadministrasi' class='text-danger'></span>";
				
				if(!empty($checked)){
					
					$style="style='display:inline;'";
					
					if(empty($is_pilih_unit_kerja)){
						$is_pilih_unit_kerja = "ya";
					}
				}
				else{
					$style="style='display:none;'";
				}
				
			}
			else{
				$style="style='display:none;'";
				$warning_pengadministrasi="";
			}
			
			if(strpos(strtolower($daftar_grup_pengguna[$i]["nama"]),"unit kerja")!==false){
				echo " <button id='tombol_unit_kerja' class='btn btn-primary btn-xs' onclick='pilih_unit_kerja(this)' data-toggle='modal' data-target='#modal_unit_kerja' $style>Unit Kerja</button> $warning_pengadministrasi";
			}
			
			if($i<count($daftar_grup_pengguna)-1){
				echo "<br>";
			}
		}
		
		if(empty($is_pilih_unit_kerja)){
			$is_pilih_unit_kerja = "tidak";
		}
		
		echo "<input type='hidden' id='is_pilih_unit_kerja' name='is_pilih_unit_kerja' value='$is_pilih_unit_kerja'/>";
		echo "<input type='hidden' id='admin_unit_kerja' name='admin_unit_kerja' value='$admin_unit_kerja'/>";
		echo "<input type='hidden' id='admin_unit_kerja_ubah' name='admin_unit_kerja_ubah' value='$admin_unit_kerja'/>";
	}
	else if(strcmp($modal,"unit_kerja")==0){
		if($akses["ubah grup"]){
			$disabled = "";
		}
		else{
			$disabled = "disabled='disabled'";
		}
		
		$daftar_pengadministrasi = "";
		
		$arr_kode_unit_pengadministrasi = array();//var_dump($akses);
		
		foreach($pengadministrasi as $admin_unit_kerja){
			$daftar_pengadministrasi .= "<span id='pilihan_".$admin_unit_kerja["kode_unit"]."' class='alert alert-warning'>";
				$daftar_pengadministrasi .= $admin_unit_kerja["kode_unit"]." - ".$admin_unit_kerja["nama_unit"];
				if($akses["ubah grup"]){
					$daftar_pengadministrasi .= " | <a href='#' id='hapus_".$admin_unit_kerja["kode_unit"]."' onclick='hapus_pengadministrasi(this);' title='hapus'>x</a>";
				}
			$daftar_pengadministrasi .= "</span>";
			
			array_push($arr_kode_unit_pengadministrasi,$admin_unit_kerja["kode_unit"]);
		}
		
		echo "<div id='daftar_pengadministrasi_unit_kerja'>$daftar_pengadministrasi</div>";
		
		echo "<div id='pilihan_unit_kerja' style='max-height:250px;overflow:auto;'>";
			for($i=0;$i<count($daftar_satuan_kerja);$i++){
				if(in_array($daftar_satuan_kerja[$i]["kode_unit"],$arr_kode_unit_pengadministrasi)){
					$checked = "checked='checked'";
				}
				else{
					$checked = "";
				}
				echo "<label><input type='checkbox' id='satuan_kerja_".$daftar_satuan_kerja[$i]["kode_unit"]."' value='".$daftar_satuan_kerja[$i]["kode_unit"]."' $disabled onchange='unit_kerja_pilihan(this)' $checked/> ".$daftar_satuan_kerja[$i]["kode_unit"]." - ".$daftar_satuan_kerja[$i]["nama_unit"]."</label>";
				
				if($i<count($daftar_satuan_kerja)-1){
					echo "<br>";
				}
			}
		echo "</div>";
	}
?>