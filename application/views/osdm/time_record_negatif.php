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
					if($akses["lihat log"]){
						echo "<div class='row text-right'>";
							echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
							echo "<br><br>";
						echo "</div>";
					}
					
					if($this->akses["lihat"]){
				?>
						<div class="form-group">
							<div class="row">
								<div class="col-md-3">
									<label>Bulan</label>
									<select class="form-control" id='tahun_bulan' name='tahun_bulan' onchange="pilih_bulan()" style="width: 200px;">
									<?php
										foreach ($periode_bulan as $value) {
											$selected = "";
											if(strcmp($value,$tampil_bulan_tahun)==0){
												$selected = "selected='selected'";
											}
											echo "<option value='$value' $selected>".bulan_tahun($value)."</option>";
										}
									?>
									</select>
								</div>
								<div class="col-md-9">
									<label>Periode</label>
									<select class="form-control" id='periode' name='periode' onchange="tampilkan_isian()">
									</select>
								</div>
							</div>
						</div>
						<div id="tempat_isian"></div>
				<?php
					}
				?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->

<script>
	function ubah_wfh(item)
	{			
		var myString = item.value;
		myArray = myString.split("|");
		
		value 	=  myArray[0];
		np 		=  myArray[1];
		date 	=  myArray[2];
		
		var data_value 		= value;
		var data_np 		= np;
		var data_date 		= date;
		
		$.ajax({
			url: "<?php echo site_url('osdm/time_record_negatif/update/')?>",
			type: 'POST',
			data: {
					vdata_value: data_value,
					vdata_np: data_np,
					vdata_date: data_date,
				},
			error: function() {
               alert('Ada Kesalahan');
			},
			success: function(data) {
					$.notify({
					type: 'success',             // 'info', 'success', 'warning', 'danger'
					icon: 'fa fa-check mr-5',    // Icon class
					message: 'Update Berhasil'
				});

			}
        });
	
	}
</script>	

<script src="<?= base_url()?>asset/notify/bootstrap-notify.min.js"></script>