<?php
	//var_dump($arr_jadwal_kerja);
	foreach($arr_tanggal as $tanggal){
		echo "<div class='row'>";
			echo "<div class='col-lg-12'>";
				$class = "well well-sm";
				$nama_hari_libur = "";
				if(in_array($tanggal,$arr_tanggal_libur)){
					$class = "alert alert-danger";
					$nama_hari_libur = "(".$arr_nama_libur[$tanggal].")";
				}
				
				$jenis_kehadiran = "";
				$tidak_lengkap_hadir = "";
								
				$hari_cuti_bersama = $this->db->query("SELECT * FROM ess_cuti_bersama WHERE tanggal_cuti_bersama='$tanggal' AND np_karyawan='$no_pokok' LIMIT 1")->row_array();
				$id_cuti_bersama = $hari_cuti_bersama['id'];
				
				if(!isset($arr_jadwal_kerja[$tanggal])){
					
				}
				else if(!empty($id_cuti_bersama)){ //jika cuti bersama
					$jenis_kehadiran = "Cuti Bersama";
				}
				else if(!empty($arr_jadwal_kerja[$tanggal]["id_sppd"])){
					$jenis_kehadiran = "Perjalanan Dinas / Pendidikan";
				}
				else if(!empty($arr_jadwal_kerja[$tanggal]["id_cuti"])){
					$jenis_kehadiran = "Cuti";
				}
				else if(strcmp($arr_jadwal_kerja[$tanggal]["dws_name"],"OFF")==0 and empty($arr_jadwal_kerja[$tanggal]["datang"]) and empty($arr_jadwal_kerja[$tanggal]["pulang"])){
					$jenis_kehadiran = "Libur";
				}
				else if(strcmp($arr_jadwal_kerja[$tanggal]["dws_name"],"OFF")==0 and !(empty($arr_jadwal_kerja[$tanggal]["datang"]) and empty($arr_jadwal_kerja[$tanggal]["pulang"]))){
					$jenis_kehadiran = "Lembur di Hari Libur";
				}
				else if(strcmp($arr_jadwal_kerja[$tanggal]["dws_name"],"OFF")!=0 and !empty($arr_jadwal_kerja[$tanggal]["datang"]) and !empty($arr_jadwal_kerja[$tanggal]["pulang"])){
					$jenis_kehadiran = "Hadir";
					$tidak_lengkap_hadir = "Lengkap";
				}
				else if(strcmp($arr_jadwal_kerja[$tanggal]["dws_name"],"OFF")!=0 and empty($arr_jadwal_kerja[$tanggal]["datang"]) and !empty($arr_jadwal_kerja[$tanggal]["pulang"])){
					$jenis_kehadiran = "Hadir";
					$tidak_lengkap_hadir = "Tidak Lengkap : Tidak Slide Masuk";
				}
				else if(strcmp($arr_jadwal_kerja[$tanggal]["dws_name"],"OFF")!=0 and !empty($arr_jadwal_kerja[$tanggal]["datang"]) and empty($arr_jadwal_kerja[$tanggal]["pulang"])){
					$jenis_kehadiran = "Hadir";
					$tidak_lengkap_hadir = "Tidak Lengkap : Tidak Slide Keluar";
				}
				else if(strcmp($arr_jadwal_kerja[$tanggal]["dws_name"],"OFF")!=0 and empty($arr_jadwal_kerja[$tanggal]["datang"]) and empty($arr_jadwal_kerja[$tanggal]["pulang"])){				
					$jenis_kehadiran = "Hadir";
					$tidak_lengkap_hadir = "Tidak Lengkap : Tidak Slide Masuk dan Tidak Slide Keluar";				
				}
				else{
				}
				
				if(strcmp($jenis_kehadiran,"Hadir")==0){
					if(strcmp($arr_jadwal_kerja[$tanggal]["wfh"],"1")==0){
						$jenis_kehadiran .= " Kerja dari Rumah / <i>Work From Home</i> (WFH)";
					}
				}
				
                echo "<div class='$class'>";
                    echo "<h4>".hari_tanggal($tanggal)." $nama_hari_libur</h4>";
					if(isset($arr_jadwal_kerja[$tanggal])){
						echo "<div class='row'>";
							echo "<div class='col-lg-4'>";
								echo "<div class='row'>";
									echo "<div class='col-lg-12'><b>Jadwal Kerja</b></div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-4'>Nama Jadwal</div>";
									echo "<div class='col-lg-8'>".$arr_jadwal_kerja[$tanggal]["dws_name"]." - ".$arr_jadwal_kerja[$tanggal]["description"]."</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-4'>Mulai</div>";
									echo "<div class='col-lg-8'>".tanggal($arr_jadwal_kerja[$tanggal]["jadwal_tanggal_masuk"])." ".$arr_jadwal_kerja[$tanggal]["jadwal_jam_masuk"]."</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-4'>Selesai</div>";
									echo "<div class='col-lg-8'>".tanggal($arr_jadwal_kerja[$tanggal]["jadwal_tanggal_pulang"])." ".$arr_jadwal_kerja[$tanggal]["jadwal_jam_pulang"]."</div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-4'>Istirahat</div>";
									if(strcmp($arr_jadwal_kerja[$tanggal]["istirahat"],"terjadwal")==0){
										echo "<div class='col-lg-8'>".$arr_jadwal_kerja[$tanggal]["dws_break_start_time"]." - ".$arr_jadwal_kerja[$tanggal]["dws_break_end_time"]."</div>";
									}
									else if(strcmp($arr_jadwal_kerja[$tanggal]["istirahat"],"bergantian")==0){
										echo "<div class='col-lg-8'>Bergantian</div>";
									}
								echo "</div>";
							echo "</div>";
							echo "<div class='col-lg-4'>";
								echo "<div class='row'>";
									echo "<div class='col-lg-12'><b>Realisasi</b></div>";
								echo "</div>";
								echo "<div class='row'>";
									echo "<div class='col-lg-4'>Jenis</div>";
									echo "<div class='col-lg-8'>";
										echo "$jenis_kehadiran";
										if(!empty($tidak_lengkap_hadir)){
											echo " ($tidak_lengkap_hadir)";
										}
									echo "</div>";
								echo "</div>";
								if(strcmp($jenis_kehadiran,"Perjalanan Dinas / Pendidikan")==0){
									echo "<div class='row'>";
										echo "<div class='col-lg-4'>Kegiatan</div>";
										echo "<div class='col-lg-8'>".$arr_jadwal_kerja[$tanggal]["perihal_dinas"]."</div>";
									echo "</div>";
								}
								else if(in_array($jenis_kehadiran,array("Hadir","Lembur di Hari Libur"))){
									echo "<div class='row'>";
										echo "<div class='col-lg-4'>Datang</div>";
										echo "<div class='col-lg-8'>".tanggal_waktu($arr_jadwal_kerja[$tanggal]["datang"])."</div>";
									echo "</div>";
									echo "<div class='row'>";
										echo "<div class='col-lg-4'>Pulang</div>";
										echo "<div class='col-lg-8'>".tanggal_waktu($arr_jadwal_kerja[$tanggal]["pulang"])."</div>";
									echo "</div>";
								}
							echo "</div>";
							echo "<div class='col-lg-4'>";
								if(isset($arr_lembur[$tanggal])){									
									echo "<div class='row'>";
										echo "<div class='col-lg-12'><b>Lembur</b></div>";
									echo "</div>";
									foreach($arr_lembur[$tanggal] as $lembur){									
										echo "<div class='row'>";
											echo "<div class='col-lg-4'>Jenis Lembur</div>";
											echo "<div class='col-lg-8'>".$lembur["jenis_lembur"]."</div>";
										echo "</div>";
										echo "<div class='row'>";
											echo "<div class='col-lg-4'>Mulai</div>";
											echo "<div class='col-lg-8'>".$lembur["waktu_mulai_fix"]."</div>";
										echo "</div>";
										echo "<div class='row'>";
											echo "<div class='col-lg-4'>Selesai</div>";
											echo "<div class='col-lg-8'>".$lembur["waktu_selesai_fix"]."</div>";
										echo "</div>";
									}
								}
							echo "</div>";
						echo "</div>";
					}
				echo "</div>";
			echo "</div>";
		echo "</div>";
	}

?>
