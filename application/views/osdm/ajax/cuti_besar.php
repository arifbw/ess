<?php
	if(strcmp($function,"tampil_konversi")==0){
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>No. Pokok</div>";
			echo "<div class='col-lg-9'>$np_karyawan</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Nama</div>";
			echo "<div class='col-lg-9'>$nama</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Tahun Cuti Besar</div>";
			echo "<div class='col-lg-9'>$tahun</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Timbul</div>";
			echo "<div class='col-lg-9'>".tanggal($tanggal_timbul)."</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Kadaluarsa</div>";
			echo "<div class='col-lg-9'>".tanggal($tanggal_kadaluarsa)."</div>";
		echo "</div>";
		
		echo "<hr>";
		
		echo "<input class='form-control' type='hidden' id='id_cuti_besar' name='id_cuti_besar' value='$id'/>";
		echo "<input class='form-control' type='hidden' id='no_pokok' name='no_pokok' value='$np_karyawan'/>";
		echo "<input class='form-control' type='hidden' id='tahun' name='tahun' value='$tahun'/>";
		
		echo "<div class='row'>";
			echo "<div class='col-lg-12' align='center'><b>Sisa : $sisa_bulan bulan $sisa_hari hari</b></div>";
		echo "</div>";
		
		echo "<div class='row'>";
			echo "<div class='col-lg-2'><b>Konversi</b></div>";
			echo "<div class='col-lg-2'>";
				echo "<select class='form-control' id='konversi_dari_bulan' name='konversi_dari_bulan' onchange='hitung_konversi_bulan()'>";
					echo "<option></option>";
					for($i=1;$i<=$batas_konversi_bulan;$i++){
						echo "<option value='$i'>$i</option>";
					}
				echo "</select>";
			echo "</div>";
			echo "<div class='col-lg-2'>";
				echo "bulan";
			echo "</div>";
			echo "<div class='col-lg-2' align='right'>";
				echo "menjadi";
			echo "</div>";
			echo "<div class='col-lg-2'>";
				echo "<input class='form-control' id='konversi_jadi_hari' name='konversi_jadi_hari' value='' readonly/>";
			echo "</div>";
			echo "<div class='col-lg-2'>";
				echo "hari";
			echo "</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-2'></div>";
			echo "<div class='col-lg-2'>";
				echo "<select class='form-control' id='konversi_dari_hari' name='konversi_dari_hari' onchange='hitung_konversi_hari()'>";
					echo "<option></option>";
					for($i=$konversi_bulan_ke_hari;$i<=$batas_konversi_hari;$i+=$konversi_bulan_ke_hari){
						echo "<option value='$i'>$i</option>";
					}
				echo "</select>";
			echo "</div>";
			echo "<div class='col-lg-2'>";
				echo "hari";
			echo "</div>";
			echo "<div class='col-lg-2' align='right'>";
				echo "menjadi";
			echo "</div>";
			echo "<div class='col-lg-2'>";
				echo "<input class='form-control' id='konversi_jadi_bulan' name='konversi_jadi_bulan' value='' readonly/>";
			echo "</div>";
			echo "<div class='col-lg-2'>";
				echo "bulan";
			echo "</div>";
		echo "</div>";
	}
	else if(strcmp($function,"tampil_maintenance_kuota")==0){
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>No. Pokok</div>";
			echo "<div class='col-lg-9'>$np_karyawan</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Nama</div>";
			echo "<div class='col-lg-9'>$nama</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Tahun Cuti Besar</div>";
			echo "<div class='col-lg-9'>$tahun</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Sisa Bulan</div>";
			echo "<div class='col-lg-9'>".$sisa_bulan."</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Sisa Hari</div>";
			echo "<div class='col-lg-9'>".$sisa_hari."</div>";
		echo "</div>";
		echo "<br>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Sisa Bulan Asli</div>";
			echo "<div class='col-lg-9'>".$sisa_bulan_asli."</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Sisa Hari Asli</div>";
			echo "<div class='col-lg-9'>".$sisa_hari_asli."</div>";
		echo "</div>";
		
		echo "<hr>";
		
		echo "<input class='form-control' type='hidden' id='id_cuti_besar' name='id_cuti_besar' value='$id'/>";
		echo "<input class='form-control' type='hidden' id='no_pokok' name='no_pokok' value='$np_karyawan'/>";
		echo "<input class='form-control' type='hidden' id='tahun' name='tahun' value='$tahun'/>";
		echo "<input class='form-control' type='hidden' id='sisa_edit' name='sisa_edit' value='1'/>";
		echo "<input class='form-control' type='hidden' id='sisa_bulan_asli' name='sisa_bulan_asli' value='$sisa_bulan_asli'/>";
		echo "<input class='form-control' type='hidden' id='sisa_hari_asli' name='sisa_hari_asli' value='$sisa_hari_asli'/>";
						
		echo "<div class='row'>";
			echo "<div class='col-lg-4'><b>Maintenance Sisa Bulan</b></div>";
			echo "<div class='col-lg-8'>";
				echo "<input type='number' class='form-control' id='sisa_bulan' name='sisa_bulan' value='$sisa_bulan' required/>";
			echo "</div>";
		echo "</div>";	
		echo "<div class='row'>";
			echo "<div class='col-lg-4'><b>Maintenance Sisa Hari</b></div>";
			echo "<div class='col-lg-8'>";
				echo "<input type='number' class='form-control' id='sisa_hari' name='sisa_hari' value='$sisa_hari' required/>";
			echo "</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-4'><b>Alasan Maintenance</b></div>";
			echo "<div class='col-lg-8'>";
				echo "<input type='text' class='form-control' id='sisa_edit_alasan' name='sisa_edit_alasan' value='$sisa_edit_alasan' required/>";
			echo "</div>";
		echo "</div>";
			
	}
	else if(strcmp($function,"tampil_perpanjang_kadaluarsa")==0){
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>No. Pokok</div>";
			echo "<div class='col-lg-9'>$np_karyawan</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Nama</div>";
			echo "<div class='col-lg-9'>$nama</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Tahun Cuti Besar</div>";
			echo "<div class='col-lg-9'>$tahun</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Timbul</div>";
			echo "<div class='col-lg-9'>".tanggal($tanggal_timbul)."</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Kadaluarsa</div>";
			echo "<div class='col-lg-9'>".tanggal($tanggal_kadaluarsa)."</div>";
		echo "</div>";
		
		echo "<hr>";
		
		echo "<input class='form-control' type='hidden' id='id_cuti_besar' name='id_cuti_besar' value='$id'/>";
		echo "<input class='form-control' type='hidden' id='no_pokok' name='no_pokok' value='$np_karyawan'/>";
		echo "<input class='form-control' type='hidden' id='tahun' name='tahun' value='$tahun'/>";
						
		echo "<div class='row'>";
			echo "<div class='col-lg-2'><b>Perpanjang Kadaluarsa</b></div>";
			echo "<div class='col-lg-10'>";
				echo "<input type='date' class='form-control' id='perpanjang_kadaluarsa' name='perpanjang_kadaluarsa' value='' required/>";
			echo "</div>";
			
	}
	else if(strcmp($function,"tampil_ubcb")==0){
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>No. Pokok</div>";
			echo "<div class='col-lg-9'>$np_karyawan</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Nama</div>";
			echo "<div class='col-lg-9'>$nama</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Tahun Cuti Besar</div>";
			echo "<div class='col-lg-9'>$tahun</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Timbul</div>";
			echo "<div class='col-lg-9'>".tanggal($tanggal_timbul)."</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Kadaluarsa</div>";
			echo "<div class='col-lg-9'>".tanggal($tanggal_kadaluarsa)."</div>";
		echo "</div>";
		
		echo "<hr>";
		
		echo "<input class='form-control' type='hidden' id='id_cuti_besar' name='id_cuti_besar' value='$id'/>";
		echo "<input class='form-control' type='hidden' id='no_pokok' name='no_pokok' value='$np_karyawan'/>";
		echo "<input class='form-control' type='hidden' id='tahun' name='tahun' value='$tahun'/>";
		
		echo "<div class='row'>";
			echo "<div class='col-lg-2'><b>Tanggal Cuti</b></div>";
			echo "<div class='col-lg-10'>";
				echo "<input type='date' class='form-control' id='ubcb_tanggal_cuti' name='ubcb_tanggal_cuti' value='$ubcb_tanggal_cuti' required/>";	
			echo "</div>";
		echo "</div>";	
			
		echo "<div class='row'>";
			echo "<div class='col-lg-2'><b>Tanggal UBCB</b></div>";
			echo "<div class='col-lg-10'>";
				echo "<input type='date' class='form-control' id='ubcb_tanggal_keluar' name='ubcb_tanggal_keluar' value='$ubcb_tanggal_keluar' required/>";	
			echo "</div>";	
		echo "</div>";
		
		
	}
	else if(strcmp($function,"tampil_kompensasi")==0){
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>No. Pokok</div>";
			echo "<div class='col-lg-9'>$np_karyawan</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Nama</div>";
			echo "<div class='col-lg-9'>$nama</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Tahun Cuti Besar</div>";
			echo "<div class='col-lg-9'>$tahun</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Timbul</div>";
			echo "<div class='col-lg-9'>".tanggal($tanggal_timbul)."</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='col-lg-3'>Kadaluarsa</div>";
			echo "<div class='col-lg-9'>".tanggal($tanggal_kadaluarsa)."</div>";
		echo "</div>";
		
		echo "<hr>";
		
		echo "<input class='form-control' type='hidden' id='id_cuti_besar' name='id_cuti_besar' value='$id'/>";
		echo "<input class='form-control' type='hidden' id='no_pokok' name='no_pokok' value='$np_karyawan'/>";
		echo "<input class='form-control' type='hidden' id='tahun' name='tahun' value='$tahun'/>";
		
		echo "<div class='row'>";
			echo "<div class='col-lg-12' align='center'><b>Sisa : $sisa_bulan bulan $sisa_hari hari</b></div>";
		echo "</div>";
		
		echo "<div class='row'>";
			echo "<div class='col-lg-2'><b>Kompensasi</b></div>";
			echo "<div class='col-lg-2'>";
				echo "<select class='form-control' id='kompensasi_bulan' name='kompensasi_bulan' required>";
					echo "<option></option>";
					for($i=1;$i<=$batas_konversi_bulan;$i++){
						echo "<option value='$i'>$i</option>";
					}
				echo "</select>";
			echo "</div>";
			echo "<div class='col-lg-2'>";
				echo "bulan";
			echo "</div>";
		echo "</div>";
		
	}
	else if(in_array($function,array("hitung_konversi_bulan","hitung_konversi_hari"))){
		echo $hasil_konversi;
	}
?>