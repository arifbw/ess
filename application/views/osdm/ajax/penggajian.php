<?php
	if(isset($kontrak_kerja_terpilih) or isset($unit_kerja_terpilih)){
?>
		<div class="row">
			<div class="col-lg-6">
				<div class="row" style="padding-left:4px;padding-right:4px;">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseModalOne">Kontrak Kerja</a> (<span id="banyak_kontrak_kerja"><?php echo count($kontrak_kerja_terpilih);?></span>)
							</h4>
						</div>
						<div id="collapseModalOne" class="panel-collapse collapse in">
							<div class="panel-body">
								<div id="pilihan_pilih_kontrak_kerja" style="max-height:100px;overflow:auto;">
									<?php
									for($i=0;$i<count($daftar_kontrak_kerja);$i++){
										if($i%2==0){
											echo "<div class='row'>";
										}
										if(in_array($daftar_kontrak_kerja[$i]["kontrak_kerja"],$kontrak_kerja_terpilih)){
											$checked = "checked='checked'";
										}
										else{
											$checked = "";
										}
										echo "<div class='col-lg-6'>";
											echo "<label><input type='checkbox' id='satuan_kerja_".$daftar_kontrak_kerja[$i]["kontrak_kerja"]."' name='pilih_kontrak_kerja' value='".$daftar_kontrak_kerja[$i]["kontrak_kerja"]."' onchange='pilih_kontrak_kerja_pilihan(this)' $checked/> ".$daftar_kontrak_kerja[$i]["kontrak_kerja"]."</label>";
										echo "</div>";
										
										/* if($i<count($daftar_kontrak_kerja)-1){
											echo "<br>";
										} */
										if($i%2==1 or $i==count($daftar_kontrak_kerja)-1){
											echo "</div>";
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row" style="padding-left:4px;padding-right:4px;">
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#accordion" href="#collapseModalTwo">Unit Kerja</a> (<span id="banyak_unit_kerja"><?php echo count($unit_kerja_terpilih);?></span>)
							</h4>
						</div>
						<div id="collapseModalTwo" class="panel-collapse collapse in">
							<div class="panel-body">
								<div id="pilihan_pilih_unit_kerja" style="max-height:100px;overflow:auto;">
									<?php								
									for($i=0;$i<count($daftar_satuan_kerja);$i++){
										if(in_array($daftar_satuan_kerja[$i]["kode_unit"],$unit_kerja_terpilih)){
											$checked = "checked='checked'";
										}
										else{
											$checked = "";
										}
										echo "<label><input type='checkbox' id='satuan_kerja_".$daftar_satuan_kerja[$i]["kode_unit"]."' name='pilih_unit_kerja' value='".$daftar_satuan_kerja[$i]["kode_unit"]."' onchange='pilih_unit_kerja_pilihan(this)' $checked/> ".$daftar_satuan_kerja[$i]["kode_unit"]." - ".$daftar_satuan_kerja[$i]["nama_unit"]."</label>";
										
										if($i<count($daftar_satuan_kerja)-1){
											echo "<br>";
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="panel panel-default">
					<div class="panel-heading">Karyawan (<span id="banyak_karyawan"><?php echo count($karyawan_terpilih)?></span>)</div>
						<div id="karyawan_terpilih" style="max-height:290px;overflow:auto;padding-left:4px;padding-right:4px;">
							<?php
								for($i=0;$i<count($karyawan_terpilih);$i++){
									echo $karyawan_terpilih[$i]["no_pokok"]." - ".$karyawan_terpilih[$i]["nama"];
									if($i<count($karyawan_terpilih)-1){
										echo "<br>";
									}
								}
							?>
						</div>
					<div class="panel-body"></div>
				</div>
			</div>
		</div>
<?php	
	}
	else if(isset($karyawan_terpilih)){
		echo count($karyawan_terpilih);
		echo "|";
		for($i=0;$i<count($karyawan_terpilih);$i++){
			echo $karyawan_terpilih[$i]["no_pokok"];
			if($i<count($karyawan_terpilih)-1){
				echo ",";
			}
		}
		echo "|";
		for($i=0;$i<count($karyawan_terpilih);$i++){
			echo $karyawan_terpilih[$i]["no_pokok"]." - ".$karyawan_terpilih[$i]["nama"];
			if($i<count($karyawan_terpilih)-1){
				echo "<br>";
			}
		}
	}
	
	/* End of file penggajian.php */
	/* Location: ./application/view/osdm/ajax/penggajian.php */