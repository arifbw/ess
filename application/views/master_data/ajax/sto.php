<?php
	#echo "<button id='tombol_kembali' class='btn btn-outline btn-primary btn-xs' onclick='masterMenu(\"".base_url(@$url_master_menu)."\")' title='kembali ke master menu'><i class='fa fa-arrow-circle-left fa-fw'></i> Kembali</button><br/><br/>";
	
	if(@$akses["lihat"] and count($daftar_menu)>0){
		if(!@$akses["ubah"]){
			$disabled = "disabled";
		}
		else{
			$disabled = "";
		}
		
		for($i=0;$i<count($daftar_menu);$i++){
            if($daftar_menu[$i]["object_type"]=='O'){
                $obj_type = '<p class="fa fa-building-o"></p> ';
            } else if($daftar_menu[$i]["object_type"]=='S'){
                $obj_type = '<p class="fa fa-black-tie fa-fw"></p> ';
            } else if($daftar_menu[$i]["object_type"]=='P'){
                $obj_type = '<p class="fa fa-user"></p> ';
            }
			echo "<div class='well well-sm'>";
				echo "<div class='row'>";
					echo "<div class='col-md-6'>";
                        echo $obj_type.$daftar_menu[$i]["object_name"];
						/*echo "<input type='hidden' id='isian_menu_lama_$i' value='".$daftar_menu[$i]["id"]."'/>";
						echo "<select class='form-control' onchange='ubah(this)' id='isian_menu_$i' $disabled>";
							$optgroup = "";
							for($j=0;$j<count($pilihan_modul);$j++){
								if((int)$daftar_menu[$i]["id"]==(int)$pilihan_modul[$j]["id"]){
									$selected = "selected='selected'";
								}
								else{
									$selected = "";
								}
								
								if(strcmp($optgroup,$pilihan_modul[$j]["object_name"])!=0){
									$optgroup=$pilihan_modul[$j]["object_name"];
									echo "<optgroup label='$optgroup'>";
								}
								
								echo "<option value=".$pilihan_modul[$j]["id"]." $selected>".$pilihan_modul[$j]["object_name"]."</option>";
								
								if($j==count($pilihan_modul)-1 or strcmp($optgroup,$pilihan_modul[$j+1]["object_name"])!=0){
									echo "</optgroup>";
								}
							}
						echo "</select>";
						echo "<input type='hidden' value='".$daftar_menu[$i]["id"]."'/>";
						echo "<input type='hidden' id='urutan_$i' value='".$daftar_menu[$i]["urutan"]."'/>";
						echo "<input type='hidden' id='induk_$i' value='".$daftar_menu[$i]["urutan_induk"]."'/>";
						echo "<input type='hidden' id='level_$i' value='".$daftar_menu[$i]["level"]."'/>";*/
					echo "</div>";
					echo "<div class='col-md-6'>";
						//echo "URL : <a href=''>".base_url()."</a>";
					echo "</div>";
					
					if(@$akses["ubah"]){
						echo "<div class='col-md-2'>";
							echo "<div class='pull-right'>";
								echo "<button class='btn btn-outline btn-primary btn-xs' onclick='buatIsianSub(this)' title='tambah isian menu'><i class='fa fa-plus fa-fw'></i></button>";
								if(!empty($daftar_menu[$i]["sebelum"])){
									echo "<button class='btn btn-outline btn-primary btn-xs' onclick='tukar(\"".$daftar_menu[$i]["urutan"]."\",\"".$daftar_menu[$i]["sebelum"]."\")'><i class='fa fa-arrow-up fa-fw'></i></button>";
								}
								else{
									echo "<button class='btn btn-outline btn-primary btn-xs'><i class='fa fa-fw'>&nbsp;</i></button>";
								}
								if(!empty($daftar_menu[$i]["setelah"])){
									echo "<button class='btn btn-outline btn-primary btn-xs' onclick='tukar(\"".$daftar_menu[$i]["urutan"]."\",\"".$daftar_menu[$i]["setelah"]."\")'><i class='fa fa-arrow-down fa-fw'></i></button>";
								}
								else{
									echo "<button class='btn btn-outline btn-primary btn-xs'><i class='fa fa-fw'>&nbsp;</i></button>";
								}
								echo "<button class='btn btn-outline btn-primary btn-xs' onclick='hapus(\"".$daftar_menu[$i]["urutan"]."\")'><i class='fa fa-trash fa-fw'></i></button>";
							echo "</div>";
						echo "</div>";
					}
				echo "</div>";
			if(($i<count($daftar_menu)-1 and $daftar_menu[$i]["level"]>=$daftar_menu[$i+1]["level"]) or $i==count($daftar_menu)-1){
				if($i<count($daftar_menu)-1){
					$level_akhir = $daftar_menu[$i+1]["level"];
				}
				else{
					$level_akhir = 1;
				}

				for($j=$daftar_menu[$i]["level"];$j>=$level_akhir;$j--){
					//echo "$j $level_akhir";
					echo "</div>";
				}
			}
			else{
				echo "<br/>";
			}
		}
	}
	
	if(@$akses["ubah"]){
		echo "<button id='tombol_tambah' class='btn btn-outline btn-primary btn-xs' onclick='buatIsian(this)' title='tambah isi menu'><i class='fa fa-plus fa-fw'></i></button>";
		
		echo "<input type='hidden' id='id_tambah_menu' value='".count($daftar_menu)."'/>";
		
		echo "<div id='isian' style='display:none'>";
			echo "<div class='col-md-1'>";
				echo "&nbsp;";
			echo "</div>";
			echo "<div class='col-md-3'>";
				echo "<select class='form-control' onchange='simpan(this)'>";
					echo "<option></option>";
					$optgroup = "";
					for($i=0;$i<count($pilihan_modul);$i++){
						if(strcmp($optgroup,$pilihan_modul[$i]["nama_kelompok_modul"])!=0){
							$optgroup=$pilihan_modul[$i]["nama_kelompok_modul"];
							echo "<optgroup label='$optgroup'>";
						}
						echo "<option value=".$pilihan_modul[$i]["id"].">".$pilihan_modul[$i]["nama"]."</option>";
						if($i==count($pilihan_modul)-1 or strcmp($optgroup,$pilihan_modul[$i+1]["nama_kelompok_modul"])!=0){
							echo "</optgroup>";
						}
					}
				echo "</select>";
				
				echo "<input type='hidden' value='0'/>";
				echo "<input type='hidden' value='1'/>";
			echo "</div>";
			echo "<div class='col-md-6'>";
				echo "&nbsp;";
			echo "</div>";
			echo "<div class='col-md-2'>";
				echo "<div class='pull-right'>";
					echo "<button class='btn btn-outline btn-primary btn-xs' onclick='batalIsian(this)'><i class='fa fa-trash fa-fw'></i></button>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
?>