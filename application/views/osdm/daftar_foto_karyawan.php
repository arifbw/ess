<?php
	$banyak_data = min(count($daftar_foto),$foto_per_baris*$baris);
	
	if($banyak_data>0){
		for($i=0;$i<$banyak_data;$i++){	
			if($i%$foto_per_baris==0){
				echo "<div class='row'>";
			}
			echo "<div class='col-lg-".(12/$foto_per_baris)."'>";
				echo "<span class='frame'>";
					echo "<img src='".base_url($folder_lihat_biodata.$daftar_foto[$i]["nama_file"])."?".date("YmdHis")."'/>";
					echo "<br>";
					echo "<span>".$daftar_foto[$i]["nama"]."</span>";
					echo "<br>";
					echo "<span>".$daftar_foto[$i]["no_pokok"]."</span>";
					if($akses["lihat log"] or $akses["ubah"]){
						echo "<span class='frame_button'>";
							if($akses["ubah"]){
								echo "<button class='btn btn-primary btn-xs' data-toggle='modal' data-target='#modal_ubah' onclick='tampil_data_ubah(this)'>Ubah</button>";
							}
							if($akses["lihat log"]){
								echo " <button class='btn btn-primary btn-xs' onclick='lihat_log(\"".$daftar_foto[$i]["nama"]."\",".$daftar_foto[$i]["id"].")'>Lihat Log</button>";
							}
						echo "</span>";
					}
				echo "</span>";
			echo "</div>";
			if($i%$foto_per_baris==$foto_per_baris-1 or $i==$banyak_data-1){
				echo "</div>";
			}
		}
		echo "<div class='row'>";
			echo "<div class='col-lg-6'>";
				echo "menampilkan $banyak_data dari $banyak_foto foto";
			echo "</div>";
			echo "<div class='col-lg-6'>";
				echo "<div id='foto_paginate' class='dataTables_paginate paging_simple_numbers'>";
					echo "<ul class='pagination'>";
						$disabled="";
						if($halaman==1){
							$disabled="disabled";
							$onclick = "";
						}
						else{
							$onclick = "onclick=setHalaman(".($halaman-1).")";
						}
						echo "<li id='foto_previous' class='paginate_button previous $disabled' $onclick><a href='#'>Previous</a></li>";
						
						$batas_atas_minimal = 5;
						$batas_bawah_maksimal = $banyak_halaman - 5;
						if($banyak_halaman<$batas_atas_minimal){
							$halaman_awal = 1;
							$halaman_akhir = $banyak_halaman;
						}
						else if($halaman<$batas_atas_minimal){
							$halaman_awal = 1;
							$halaman_akhir = $batas_atas_minimal;
						}
						else if($halaman>$batas_bawah_maksimal){
							$halaman_awal = $batas_bawah_maksimal;
							$halaman_akhir = $banyak_halaman;
						}
						else{
							$halaman_awal = 1;
							$halaman_akhir = $banyak_halaman;
						}
						
						if($halaman_awal!=1){
							echo "<li class='paginate_button' onclick=setHalaman(1)><a href='#'>1</a></li>";
							echo "<li id='foto_ellipsis' class='paginate_button disabled'><a href='#'>...</a></li>";
						}
						
						for($i=$halaman_awal;$i<=$halaman_akhir;$i++){
							$active = "";
							if($i==$halaman){
								$active = "active";
							}
							echo "<li class='paginate_button $active' onclick=setHalaman($i)><a href='#'>$i</a></li>";
						}
						
						if($halaman_akhir!=$banyak_halaman){
							echo "<li id='foto_ellipsis' class='paginate_button disabled'><a href='#'>...</a></li>";
							echo "<li class='paginate_button' onclick=setHalaman($banyak_halaman)><a href='#'>$banyak_halaman</a></li>";
						}
						
						$disabled="";
						if($halaman==$banyak_halaman){
							$disabled="disabled";
							$onclick = "";
						}
						else{
							$onclick = "onclick=setHalaman(".($halaman+1).")";
						}
						echo "<li id='foto_next' class='paginate_button next $disabled' $onclick><a href='#'>Next</a></li>";
					echo "</ul>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}
	else if(empty($cari)){
		echo "Belum ada foto karyawan";
	}
	else{
		echo "Data yang dicari tidak ditemukan";
	}
?>