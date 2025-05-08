<?php
	if($cek_pengadministrasi){
		$checked="checked=checked";
		$is_pilih_unit_kerja="ya";
	}
	else{
		$checked="";
		$is_pilih_unit_kerja="tidak";
	}

	if($akses["ubah"]){		
		$disabled = "";
		if($cek_pengadministrasi){
			$disabled_unit_kerja = "";
		}
		else{
			$disabled_unit_kerja = "disabled='disabled'";
		}
	}
	else{
		$disabled = "disabled='disabled'";
		$disabled_unit_kerja = "disabled='disabled'";
	}
	
	echo "<label><input type='checkbox' id='is_pengadministrasi' name='is_pengadministrasi' $checked $disabled onchange='cekPengadministrasi(this)'> ".$nama_grup_pengadministrasi."</label> <span id='warning_pengadministrasi' class='text-danger'></span>";
	
	echo "<input type='hidden' id='is_pilih_unit_kerja' name='is_pilih_unit_kerja' value='$is_pilih_unit_kerja'>";
	echo "<input type='hidden' id='is_pilih_unit_kerja_awal' name='is_pilih_unit_kerja_awal' value='$is_pilih_unit_kerja'>";
	echo "<input type='hidden' id='username' name='username' value='$username'>";
	
	echo "<div id='osdm_pengadministrasi'>";
		$daftar_pengadministrasi = "";
			
		$arr_kode_unit_pengadministrasi = array();//var_dump($akses);
		
		foreach($pengadministrasi as $admin_unit){
			$daftar_pengadministrasi .= "<span id='pilihan_".$admin_unit["kode_unit"]."' class='alert alert-warning'>";
				$daftar_pengadministrasi .= $admin_unit["kode_unit"]." - ".$admin_unit["nama_unit"];
				if($akses["ubah"]){
					$daftar_pengadministrasi .= " | <a href='#' id='hapus_".$admin_unit["kode_unit"]."' onclick='hapus_pengadministrasi(this);' title='hapus'>x</a>";
				}
			$daftar_pengadministrasi .= "</span>";
			
			array_push($arr_kode_unit_pengadministrasi,$admin_unit["kode_unit"]);
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
				echo "<label><input type='checkbox' id='satuan_kerja_".$daftar_satuan_kerja[$i]["kode_unit"]."' name='unit_kerja' value='".$daftar_satuan_kerja[$i]["kode_unit"]."' $disabled_unit_kerja onchange='unit_kerja_pilihan(this)' $checked/> ".$daftar_satuan_kerja[$i]["kode_unit"]." - ".$daftar_satuan_kerja[$i]["nama_unit"]."</label>";
				
				if($i<count($daftar_satuan_kerja)-1){
					echo "<br>";
				}
			}
		echo "</div>";

		echo "<input type='hidden' id='admin_unit_kerja' name='admin_unit_kerja' value='$admin_unit_kerja'/>";
		echo "<input type='hidden' id='admin_unit_kerja_ubah' name='admin_unit_kerja_ubah' value='$admin_unit_kerja'/>";
		
	echo "</div>";
	
	/* End of file pengadministrasi_unit_kerja.php */
	/* Location: ./application/controllers/administrator/ajax/pengadministrasi_unit_kerja.php */