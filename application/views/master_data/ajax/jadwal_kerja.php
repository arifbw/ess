<?php
	if(strcmp($function,"salin")==0){
		echo $id."|".$description."|".$libur."|".$gilir."|".$lintas_hari_masuk."|".$dws_start_time."|".$istirahat."|".$lintas_hari_mulai_istirahat."|".$dws_break_start_time."|".$lintas_hari_akhir_istirahat."|".$dws_break_end_time."|".$lintas_hari_pulang."|".$dws_end_time."|".$dws."|".$dws_variant."|".$status;
	}
	else if(strcmp($function,"ubah")==0){
		echo "<div class='row'>";
			echo "<div class='form-group'>";
				echo "<div class='col-lg-4'>";
					echo "<label>Nama $judul</label>";
				echo "</div>";
				echo "<div class='col-lg-5'>";
					echo "<input class='form-control' type='hidden' name='id_jadwal_kerja' value='$id'/>";
					echo "<input class='form-control' type='hidden' name='nama' value='$description'/>";
					echo "<input class='form-control' name='nama_ubah' value='$description' placeholder='Nama $judul'>";
				echo "</div>";
				echo "<div id='warning_nama_ubah' class='col-lg-3 text-danger'></div>";
			echo "</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='form-group'>";
				echo "<div class='col-lg-4'>";
					echo "<label>Hari Kerja / Libur</label>";
				echo "</div>";
				echo "<div class='col-lg-5'>";
					$style_display = "";

					echo "<input class='form-control' type='hidden' name='hari' value='$libur'/>";
					if(!(bool)$libur){
						$checked="checked='checked'";
					}
					else{
						$style_display = "style='display:none'";
						$checked="";
					}
					echo "<label class='radio-inline'>";
						echo "<input type='radio' name='hari_ubah' id='hari_kerja' value='0' onclick='hari_kerja_libur(this)' $checked> Kerja";
					echo "</label>";

					if((bool)$libur){
						$checked="checked='checked'";
					}
					else{
						$checked="";
					}
					echo "<label class='radio-inline'>";
						echo "<input type='radio' name='hari_ubah' id='hari_libur' value='1' onclick='hari_kerja_libur(this)' $checked> Libur ";
					echo "</label>";
					
				echo "</div>";
				echo "<div id='warning_hari_ubah' class='col-lg-3 text-danger'></div>";
			echo "</div>";
		echo "</div>";
		
		echo "<div id='waktu_hari_kerja_ubah' $style_display>";
			
			echo "<div class='row'>";
				echo "<div class='form-group'>";
					echo "<div class='col-lg-4'>";
						echo "<label>Formasi Gilir</label>";
					echo "</div>";
					echo "<div class='col-lg-5'>";
						$style_display = "";

						echo "<input class='form-control' type='hidden' name='formasi_gilir' value='$libur'/>";
						if(!(bool)$gilir){
							$checked="checked='checked'";
						}
						else{
							$style_display = "style='display:none'";
							$checked="";
						}
						echo "<label class='radio-inline'>";
							echo "<input type='radio' name='formasi_gilir_ubah' id='formasi_gilir_non_gilir' value='0' $checked> Non Gilir";
						echo "</label>";

						if((bool)$gilir){
							$checked="checked='checked'";
						}
						else{
							$checked="";
						}
						echo "<label class='radio-inline'>";
							echo "<input type='radio' name='formasi_gilir_ubah' id='formasi_gilir_gilir' value='1' $checked> Gilir ";
						echo "</label>";
						
					echo "</div>";
					echo "<div id='warning_hari_ubah' class='col-lg-3 text-danger'></div>";
				echo "</div>";
			echo "</div>";
		
			echo "<div class='row'>";
				echo "<div class='form-group'>";
					echo "<div class='col-lg-4'>";
						echo "<label>Jam Masuk</label>";
					echo "</div>";
					echo "<div class='col-lg-2'>";
						
						if((bool)$lintas_hari_masuk){
							$checked="checked=checked";
						}
						else{
							$style_display = "style='display:none'";
							$checked="";
						}
						echo "<label class='checkbox-inline'>";
							echo "<input type='hidden' name='lintas_hari_masuk' value='$lintas_hari_masuk'/>";
							echo "<input type='checkbox' name='lintas_hari_masuk_ubah' onchange='lintas_hari(this)' $checked> Lintas Hari";
						echo "</label>";

					echo "</div>";
					echo "<div class='col-lg-3'>";
						echo "<input type='hidden' name='jam_masuk' value='$dws_start_time'/>";
						echo "<input type='time' class='form-control' name='jam_masuk_ubah' value='$dws_start_time' placeholder='Jam Masuk'/>";
					echo "</div>";
					echo "<div id='warning_jam_masuk_ubah' class='col-lg-3 text-danger'></div>";
				echo "</div>";
			echo "</div>";
			echo "<div class='row'>";
				echo "<div class='form-group'>";
					echo "<div class='col-lg-4'>";
						echo "<label>Istirahat</label>";
					echo "</div>";
					echo "<div class='col-lg-5'>";
						$style_display = "";
						
						if(strcmp($istirahat,"terjadwal")==0){
							$checked="checked=checked";
						}
						else{
							$style_display = "style='display:none'";
							$checked="";
						}
						echo "<label class='radio-inline'>";
							echo "<input type='hidden' name='istirahat' id='istirahat_terjadwal' value='$istirahat'/>";
							echo "<input type='radio' name='istirahat_ubah' id='istirahat_terjadwal' value='terjadwal' onclick='waktu_istirahat(this)' $checked> Terjadwal";
						echo "</label>";
						
						if(strcmp($istirahat,"bergantian")==0){
							$checked="checked=checked";
						}
						else{
							$checked="";
						}
						
						echo "<label class='radio-inline'>";
							echo "<input type='radio' name='istirahat_ubah' id='istirahat_bergantian' value='bergantian' onclick='waktu_istirahat(this)' $checked> Bergantian";
						echo "</label>";
					echo "</div>";
					echo "<div id='warning_istirahat_ubah' class='col-lg-3 text-danger'></div>";
				echo "</div>";
			echo "</div>";
			echo "<div id='waktu_istirahat' $style_display>";
				echo "<div class='row'>";
					echo "<div class='form-group'>";
						echo "<div class='col-lg-1'>";
						echo "</div>";
						echo "<div class='col-lg-3'>";
							echo "<label>Jam Mulai Istirahat</label>";
						echo "</div>";
						echo "<div class='col-lg-2'>";
							if((bool)$lintas_hari_mulai_istirahat){
								$checked="checked=checked";
							}
							else{
								$style_display = "style='display:none'";
								$checked="";
							}
							echo "<label class='checkbox-inline'>";
								echo "<input type='hidden' name='lintas_hari_mulai_istirahat' value='$lintas_hari_mulai_istirahat'/>";
								echo "<input type='checkbox' name='lintas_hari_mulai_istirahat_ubah' onchange='lintas_hari(this)' $checked> Lintas Hari";
							echo "</label>";
						echo "</div>";
						echo "<div class='col-lg-3'>";
							echo "<input type='hidden' class='form-control' name='jam_mulai_istirahat' value='$dws_break_start_time'>";
							echo "<input type='time' class='form-control' name='jam_mulai_istirahat_ubah' value='$dws_break_start_time' placeholder='Jam Mulai Istirahat'>";
						echo "</div>";
						echo "<div id='warning_jam_mulai_istirahat_ubah' class='col-lg-3 text-danger'></div>";
					echo "</div>";
				echo "</div>";
				echo "<div class='row'>";
					echo "<div class='form-group'>";
						echo "<div class='col-lg-1'>";
						echo "</div>";
						echo "<div class='col-lg-3'>";
							echo "<label>Jam Akhir Istirahat</label>";
						echo "</div>";
						echo "<div class='col-lg-2'>";
							if((bool)$lintas_hari_akhir_istirahat){
								$checked="checked=checked";
							}
							else{
								$style_display = "style='display:none'";
								$checked="";
							}
							echo "<label class='checkbox-inline'>";
								echo "<input type='hidden' name='lintas_hari_akhir_istirahat' value='$lintas_hari_akhir_istirahat'/>";
								echo "<input type='checkbox' name='lintas_hari_akhir_istirahat_ubah' onchange='lintas_hari(this)' $checked> Lintas Hari";
							echo "</label>";
						echo "</div>";
						echo "<div class='col-lg-3'>";
							echo "<input type='hidden' class='form-control' name='jam_akhir_istirahat' value='$dws_break_end_time'>";
							echo "<input type='time' class='form-control' name='jam_akhir_istirahat_ubah' value='$dws_break_end_time' placeholder='Jam Akhir Istirahat'>";
						echo "</div>";
						echo "<div id='warning_jam_akhir_istirahat_ubah' class='col-lg-3 text-danger'></div>";
					echo "</div>";
				echo "</div>";
			echo "</div>";
			echo "<div class='row'>";
				echo "<div class='form-group'>";
					echo "<div class='col-lg-4'>";
						echo "<label>Jam Pulang</label>";
					echo "</div>";
					echo "<div class='col-lg-2'>";
						
						if((bool)$lintas_hari_pulang){
							$checked="checked=checked";
						}
						else{
							$style_display = "style='display:none'";
							$checked="";
						}
						echo "<label class='checkbox-inline'>";
							echo "<input type='hidden' name='lintas_hari_pulang' value='$lintas_hari_pulang'/>";
							echo "<input type='checkbox' name='lintas_hari_pulang_ubah' onchange='lintas_hari(this)' $checked> Lintas Hari";
						echo "</label>";

					echo "</div>";
					echo "<div class='col-lg-3'>";
						echo "<input type='hidden' name='jam_pulang' value='$dws_end_time'/>";
						echo "<input type='time' class='form-control' name='jam_pulang_ubah' value='$dws_end_time' placeholder='Jam Masuk'/>";
					echo "</div>";
					echo "<div id='warning_jam_pulang_ubah' class='col-lg-3 text-danger'></div>";
				echo "</div>";
			echo "</div>";
		echo "</div>";
		echo "<div class='row'>";
			echo "<div class='form-group'>";
				echo "<div class='col-lg-4'>";
					echo "<label>Kode ERP</label>";
				echo "</div>";
				echo "<div class='col-lg-3'>";
					echo "<input class='form-control' type='hidden' name='kode_erp' value='$dws'/>";
					echo "<input class='form-control' name='kode_erp_ubah' value='$dws' placeholder='Kode ERP'/>";
				echo "</div>";
				echo "<div class='col-lg-2'>";
					if((bool)$dws_variant){
						$checked="checked=checked";
					}
					else{
						$checked="";
					}
					echo "<label class='checkbox-inline'>";
						echo "<input type='hidden' name='varian' value='$dws_variant'/>";
						echo "<input type='checkbox' name='varian_ubah' $checked> Varian";
					echo "</label>";
				echo "</div>";
				echo "<div id='warning_kode_erp_ubah' class='col-lg-3 text-danger'></div>";
			echo "</div>";
		echo "</div>";		
		echo "<div class='row'>";
			echo "<div class='form-group'>";
				echo "<div class='col-lg-4'>";
					echo "<label>Status</label>";
				echo "</div>";
				echo "<div class='col-lg-5'>";
					echo "<label class='radio-inline'>";
						echo "<input type='hidden' name='status' value='$status'>";
						if(strcmp($status,"1")==0){
							$checked="checked='checked'";
						}
						else{
							$checked="";
						}
						
						echo "<input type='radio' name='status_ubah' id='status_tambah_aktif' value='aktif' $checked>Aktif ";
					echo "</label>";
					echo "<label class='radio-inline'>";
						if(strcmp($status,"0")==0){
							$checked="checked='checked'";
						}
						else{
							$checked="";
						}
						echo "<input type='radio' name='status_ubah' id='status_tambah_non_aktif' value='non aktif' $checked>Non Aktif";
					echo "</label>";
				echo "</div>";
				echo "<div id='warning_status_ubah' class='col-lg-3 text-danger'></div>";
			echo "</div>";
		echo "</div>";
	}
	/* End of file isi_menu.php */
	/* Location: ./application/controllers/administrator/ajax/isi_menu.php */