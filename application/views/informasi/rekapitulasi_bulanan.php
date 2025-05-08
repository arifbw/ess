		<!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?php echo $judul;?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

			<?php
					if(!empty($this->session->flashdata('success'))){
				?>
						<div class="alert alert-success alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $this->session->flashdata('success');?>
						</div>
				<?php
					}
					if(!empty($this->session->flashdata('warning'))){
				?>
						<div class="alert alert-danger alert-dismissable">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
							<?php echo $this->session->flashdata('warning');?>
						</div>
				<?php
					}
					if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					
					if($this->akses["lihat"]){
				?>
						<div class="row">
							<div class='col-lg-1'>Karyawan</div>
							<div class='col-lg-5'>
								<select class="form-control select2" name="karyawan" id="karyawan" onchange="rekapitulasi_bulanan()">
								<?php
										
									for($i=0;$i<count($daftar_akses_karyawan);$i++){
										if(strcmp($daftar_akses_karyawan[$i]["no_pokok"],$this->session->userdata("no_pokok"))==0){
											$selected="selected=selected";
										}
										else{
											$selected="";
										}
										echo "<option value='".$daftar_akses_karyawan[$i]["no_pokok"]."' $selected>".$daftar_akses_karyawan[$i]["no_pokok"]." - ".$daftar_akses_karyawan[$i]["nama"]."</option>";
									}
								?>
								</select>
							</div>
							<div class='col-lg-1'>Periode</div>
							<div class='col-lg-5'>
								<select class="form-control select2" name="periode" id="periode" onchange="rekapitulasi_bulanan()">
								<?php
										
									for($i=0;$i<count($arr_periode);$i++){
										if(strcmp($arr_periode[$i]["value"],date("Y_m"))==0){
											$selected="selected=selected";
										}
										else{
											$selected="";
										}
										echo "<option value='".$arr_periode[$i]["value"]."' $selected>".$arr_periode[$i]["text"]."</option>";
									}
								?>
								</select>
							</div>
						</div>
						<div class="form-group">	
							<div class="row">
								<br><br>
								<div id="rekapitulasi_bulanan"></div>
							</div>						
						</div>
				
				<?php
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->