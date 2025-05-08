<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
<style type="text/css">
	.select2-container--default .select2-selection--single{
		height: 34px !important;
	    padding: 6px 12px !important;
	    font-size: 14px !important;
	    line-height: 1.42857143 !important;
	    color: #555 !important;
	    background-color: #fff !important;
	    background-image: none !important;
	    border: 1px solid #ccc !important;
	    border-radius: 4px !important;
	}
	.select2-container--default .select2-selection--single .select2-selection__rendered{
		line-height: 21px;
	}
	.select2-container .select2-selection--single .select2-selection__rendered{
		padding-left: 0px;
		padding-right: 0px;
	}

	@media (min-width: 1200px){
		#filter_karyawan{
			width: 11%;
		}
		#filter_bulan{
			text-align: right;
			width: 8%;
		}
	}

	.large.tooltip-inner {
	    max-width: 350px;
	    width: 350px;
	    text-align: justify;
	}
</style>

<!-- Page Content -->
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Dashboard</h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row" style="margin-bottom: 15px">
		<div class="form-group">
			<div class="col-lg-12">
				<?php					
					$tampil_tahun_bulan=$this->session->userdata('tampil_tahun_bulan');
					$tampil_tanggal = $tampil_tahun_bulan."-".$cutoff_erp_tanggal;
					
					$tanggal_cutoff	= tanggal(date('Y-m-d',strtotime('+1 months',strtotime($tampil_tanggal))));				
				?>
				<div class="alert alert-info">
					<marquee direction="left">
						Batas akhir persetujuan dan input data (Kehadiran, Lembur, Perizinan, Cuti, Perjalanan Dinas) bulan <strong><?php echo bulan_tahun($tampil_tanggal);?></strong> adalah pada tanggal <strong><?php echo $tanggal_cutoff;?> pukul 09.00 </strong></font>
					</marquee>
				</div>
			</div><br>
		</div>
		<div class="form-group">
			<div class="col-lg-2 col-md-6 col-sm-6" id="filter_karyawan">
				<label>Karyawan</label>
			</div>
			<div class="col-lg-3 col-md-6 col-sm-6" style="padding-bottom: 5px">
				<select class="form-control js-example-basic-single" id='filter_np_karyawan' name='filter_np_karyawan' style="width: 100%;" onchange="filter_np_karyawan()">
					<?php
						if($_SESSION["grup"]!=5) { //jika Pengguna
							//echo "<option value=''>Semua Karyawan</option>	";
						}
					?>
					<?php 
						foreach ($np_karyawan as $value) {
							$tampil_np_karyawan='';
							if(!empty($this->session->userdata('tampil_np_karyawan')))
							{
								$tampil_np_karyawan=$this->session->userdata('tampil_np_karyawan');
							}
							if($tampil_np_karyawan==$value->no_pokok)
							{
								$selected='selected';
							}else
							{
								$selected='';
							}
					?>
						<option value='<?php echo $value->no_pokok?>' <?php echo $selected;?>><?php echo $value->no_pokok." ".$value->nama?></option>
					<?php 
						}
					?>
				</select>
			</div>
			<div class="col-lg-2 col-md-6 col-sm-6" id="filter_bulan">
				<label>Bulan</label>
			</div>
			<div class="col-lg-2 col-md-6 col-sm-6" style="padding-bottom: 5px">
				<select class="form-control js-example-basic-single" id='filter_tahun_bulan' name='filter_tahun_bulan' style="width: 100%;" onchange="filter_tahun_bulan()">
					<!-- <option value=''>Semua Bulan</option> -->
					<?php 
						foreach ($array_tahun_bulan as $value) {
							$tampil_tahun_bulan='';
							if(!empty($this->session->userdata('tampil_tahun_bulan')))
							{
								$tampil_tahun_bulan=$this->session->userdata('tampil_tahun_bulan');
							}
							if($tampil_tahun_bulan==$value)
							{
								$selected='selected';
							}else
							{
								$selected='';
							}
					?>
						<option value='<?php echo $value?>' <?php echo $selected;?>><?php echo id_to_bulan(substr($value,5,2))." ".substr($value,0,4)?></option>
					<?php 
						}
					?>
				</select>
			</div>	
				<?php
				//if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
				if(1==4) 
				{
				?>
					<a type="button" class="btn btn-success" href='<?php echo base_url('/osdm/inisialisasi_pengadministrasi_unit_kerja')?>'>Inisialisasi Pengadministrasi</a>
				<?php } ?>
				
				<?php 
					if($_SESSION["grup"]==4) { //jika Pengadministrasi Unit Kerja
				?>
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_list_administrasi">List Administrasi Kode Unit</button>
				<?php 
					}
				?>
				
		</div>
	</div>

	<div class="row">
		<div class="col-lg-3 col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-fighter-jet fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div style=' font-size: 200%;'><?php echo $total_cuti; ?> Hari</div>
							<div>Permohonan Cuti</div>
						</div>
					</div>
				</div>
				<a href="<?php echo base_url('cuti/permohonan_cuti'); ?>">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div class="col-lg-3 col-md-6">
			<div class="panel panel-green">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-moon-o fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div style=' font-size: 200%;'><?php echo $total_lembur; ?> Data</div>
							<div>Pengajuan Lembur</div>
						</div>
					</div>
				</div>
				<a href="<?php echo base_url('lembur/pengajuan_lembur'); ?>">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div class="col-lg-3 col-md-6">
			<div class="panel panel-yellow">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-cab fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div style=' font-size: 200%;'><?php echo $total_izin; ?> Data</div>
							<div>Perizinan</div>
						</div>
					</div>
				</div>
				<a href="<?php echo base_url('perizinan/data_perizinan'); ?>">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
		<div class="col-lg-3 col-md-6">
			<div class="panel panel-red">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-3">
							<i class="fa fa-automobile fa-5x"></i>
						</div>
						<div class="col-xs-9 text-right">
							<div style=' font-size: 200%;'><?php echo $total_dinas; ?> Hari</div>
							<div>Dinas</div>
						</div>
					</div>
				</div>
				<a href="<?php echo base_url('perjalanan_dinas/sppd'); ?>">
					<div class="panel-footer">
						<span class="pull-left">View Details</span>
						<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
						<div class="clearfix"></div>
					</div>
				</a>
			</div>
		</div>
	</div>
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-bar-chart-o fa-fw"></i> Grafik Kehadiran
					<!-- <div class="pull-right">
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
								Bulan
								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu pull-right" role="menu">
								<li><a href="#">Januari 2018</a>
								</li>
								<li><a href="#">Februari 2018</a>
								</li>
							</ul>
						</div>
					</div> -->
				</div>
				<div class="panel-body">
										
					<?php 
						$tampil_grafik = false;
						foreach ($grafik_kehadiran as $row)
						{ 
							$ada_data =  $row->jml;
							
							if($ada_data!=0 AND $tampil_grafik==false)
							{
								$tampil_grafik = true;
							}
						}
					?>		
					
					
			<!--		
					
					      <div style="display: table; height: 400px; overflow: hidden;">
         <div style="display: table-cell; vertical-align: middle;">
           <div>
             everything is vertically centered in modern IE8+ and others.
           </div>
         </div>
       </div>
		-->			
					
					<?php if($tampil_grafik==true){?>
						<div id="chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>					
					<?php }else{ ?>
					
						<div style="display: table;width: 100%; height: 400px;">
							<div style="display: table-cell;vertical-align: middle;text-align: center">
							   <div>
								 <font color='grey'>Belum ada data yang perlu ditampilkan</font>
							   </div>
							</div>
						</div>
					
						
					<?php } ?>
					
					<a href="<?php echo base_url('kehadiran/data_kehadiran'); ?>" class="btn btn-default btn-block">View Details</a>
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-8 -->
		
		<div class="col-lg-4">	
			<!-- /.col-lg-4 -->
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-bell fa-fw"></i> Kehadiran Menunggu Persetujuan Anda
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="list-group" style="height: 75px; overflow-y: auto">
							<?php if(!empty($daftar_kehadiran)): ?>
								<?php foreach($daftar_kehadiran as $row): ?>
									<a href="#" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
										<table>
											<tr>
												<td style='vertical-align: top; width: 110px;'>Tanggal Kehadiran</td>
												<td style='vertical-align: top; width: 10px;'>:</td>
												<td style='vertical-align: top;'><?php echo tanggal($row['dws_tanggal']); ?></td>
											</tr>
											<tr>
												<td style='vertical-align: top;'>Alasan</td>
												<td style='vertical-align: top;'>:</td>
												<td style='vertical-align: top;'><?php echo $row['tapping_fix_approval_ket']; ?></td>
											</tr>
										</table>">
										<i class="changeText"></i>&nbsp;<strong><?php echo $row['np_karyawan'].' '.$row['nama']; ?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Tanggal <?php echo tanggal($row['dws_tanggal']); ?></span>
									</a>
								<?php endforeach; ?>
							<?php else: ?>
								<a href="#" class="list-group-item">
									<h5 style="text-align: center;">Tidak Ada Permohonan Perubahan Kehadiran yang Menunggu Persetujuan</h5>
								</a>
							<?php endif; ?>
						</div>
						<!-- /.list-group -->
						<a href="<?php echo base_url('kehadiran/persetujuan_kehadiran'); ?>" class="btn btn-default btn-block">Lihat Rincian</a>
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
			</div>
			<!-- /.col-lg-4 -->
		
			<!-- /.row -->
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-bell fa-fw"></i> Lembur Menunggu Persetujuan Anda
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="list-group" style="height: 75px; overflow-y: auto">
							<?php if(!empty($daftar_lembur)): ?>
								<?php foreach($daftar_lembur as $row): ?>
									<a href="#" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
										<table>
											<tr>
												<td style='vertical-align: top; width: 110px;'>NP</td>
												<td style='vertical-align: top; width: 10px;'>:</td>
												<td style='vertical-align: top;'><?php echo $row['no_pokok']; ?></td>
											</tr>
											<tr>
												<td style='vertical-align: top; width: 110px;'>Nama</td>
												<td style='vertical-align: top; width: 10px;'>:</td>
												<td style='vertical-align: top;'><?php echo $row['nama']; ?></td>
											</tr>
											<tr>
												<td style='vertical-align: top;'>Tertanggal</td>
												<td style='vertical-align: top;'>:</td>
												<td style='vertical-align: top;'><?php echo tanggal($row['tgl_dws']); ?></td>
											</tr>
										</table>">
										<i class="changeText"></i>&nbsp;<strong><?php echo $row['no_pokok'].' '.$row['nama']; ?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?php echo tanggal($row['tgl_dws']); ?></span>
									</a>
								<?php endforeach; ?>
							<?php else: ?>
								<a href="#" class="list-group-item">
									<h5 style="text-align: center;">Tidak Ada Permohonan Lembur yang Menunggu Persetujuan</h5>
								</a>
							<?php endif; ?>
						</div>
						<!-- /.list-group -->
						<a href="<?php echo base_url('lembur/persetujuan_lembur'); ?>" class="btn btn-default btn-block">Lihat Rincian</a>
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
			</div>
			<!-- /.col-lg-4 -->
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<i class="fa fa-bell fa-fw"></i> Cuti Menunggu Persetujuan Anda
					</div>
					<!-- /.panel-heading -->
					<div class="panel-body">
						<div class="list-group" style="height: 75px; overflow-y: auto">
							<?php if(!empty($daftar_cuti)): ?>
								<?php foreach($daftar_cuti as $row):
									$waktu = "";
									if((int)$row['jumlah_bulan']>0){
										$waktu .= $row['jumlah_bulan']." bulan";
									}
									if((int)$row['jumlah_hari']>0){
										if(!empty($waktu)){
											$waktu .= " ";
										}
										$waktu .= $row['jumlah_hari']." hari";
									}//var_dump($row);
								?>
									<a href="#" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
										<table>
											<tr>
												<td style='vertical-align: top; width: 110px;'>Lama Cuti</td>
												<td style='vertical-align: top; width: 10px;'>:</td>
												<td style='vertical-align: top;'>
													<?php
														echo $waktu;
													?>
												</td>
											</tr>
											<tr>
												<td style='vertical-align: top;'>Tanggal Cuti</td>
												<td style='vertical-align: top;'>:</td>
												<td style='vertical-align: top;'>
													<?php
														echo tanggal($row['start_date']);
														if((int)$row['jumlah_hari']>1){
															echo " s.d ".tanggal($row['end_date']);
														}
													?>
												</td>
											</tr>
											<tr>
												<td style='vertical-align: top;'>Tipe</td>
												<td style='vertical-align: top;'>:</td>
												<td style='vertical-align: top;'><?php echo $row['uraian']; ?></td>
											</tr>
											<tr>
												<td style='vertical-align: top;'>Alasan</td>
												<td style='vertical-align: top;'>:</td>
												<td style='vertical-align: top;'><?php echo $row['alasan']; ?></td>
											</tr>
										</table>">
										<i class="changeText"></i>&nbsp;<strong><?php echo $row['np_karyawan'].' '.$row['nama'].' ('.$waktu.')'; ?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<span>
											<?php
												echo tanggal($row['start_date']);
												if((int)$row['jumlah_hari']>1){
													echo " s.d ".tanggal($row['end_date']);
												}
											?>
										</span>
									</a>
								<?php endforeach; ?>
							<?php else: ?>
								<a href="#" class="list-group-item">
									<h5 style="text-align: center;">Tidak Ada Permohonan Cuti yang Menunggu Persetujuan</h5>
								</a>
							<?php endif; ?>
						</div>
						<!-- /.list-group -->
						<a href="<?php echo base_url('cuti/persetujuan_cuti'); ?>" class="btn btn-default btn-block">Lihat Rincian</a>
					</div>
					<!-- /.panel-body -->
				</div>
				<!-- /.panel -->
			</div>
			<!-- /.col-lg-4 -->
		</div>
		
		
	
	
	
	<?php
		if($_SESSION["grup"]==4) //jika Pengadministrasi Unit Kerja
		{
	?>
	
	<!-- Modal List Administrasi Kode Unit-->
	<div id="modal_list_administrasi" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">List Administrasi Kode Unit</h4>
		  </div>
		  <div class="modal-body">
			<?php
				foreach ($list_pengadministrasi as $data) { //looping list_pengadministrasi
					echo $data['kode_unit']." | ".nama_unit_by_kode_unit($data['kode_unit'])."<br>";
				}		
			?>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
	
	<?php } ?>
	
	
	
	
	
	
	
	
	
	
	
	
	
</div>
<!-- /#page-wrapper -->

<!-- amCharts javascript sources -->
<script src="<?php echo base_url("asset/amchart/amcharts.js")."?".date("YmdHis");?>"></script>
<script src="<?php echo base_url("asset/amchart/pie.js")."?".date("YmdHis");?>"></script>
<script src="<?php echo base_url("asset/amchart/dashboard_peruri.js")."?".date("YmdHis");?>"></script>

<script type="text/javascript">
	function filter_np_karyawan(){
        var np_karyawan = $("#filter_np_karyawan").val();
        var url = "<?php echo base_url();?>home/filter_np_karyawan/"+np_karyawan; // get selected value
        if (url) { // require a URL
            window.location = url; // redirect
        }
        return false;
    }

	function filter_tahun_bulan(){
        var tahun_bulan = $("#filter_tahun_bulan").val();
        var url = "<?php echo base_url();?>home/filter_tahun_bulan/"+tahun_bulan; // get selected value
        if (url) { // require a URL
            window.location = url; // redirect
        }
        return false;
    }

    $(document).ready(function() {
	   $('.js-example-basic-single').select2();
	});
</script>

<!-- amCharts javascript code -->
<script type="text/javascript">
    
	$(document).ready(function(){
		<?php
			$daftar_libur = "";

			for($i=0;$i<count($hari_libur);$i++){
				if(!empty($daftar_libur)){
					$daftar_libur .= "\\n";
				}
				$daftar_libur .= "Tanggal ".tanggal($hari_libur[$i]["tanggal"])." ".$hari_libur[$i]["deskripsi"];
			}
		?>
		
		
		//alert("<?php echo $daftar_libur;?>\n\n#Kebijakan SDM untuk  inputan-inputan bulan Februari Cutoff di undur sampai tanggal 11 Februari 2019 Pukul 23.59");
		
		
		alert("<?php echo $daftar_libur;?>\n\nCuti Bersama 2020 akan memotong jatah cuti diawal tahun\n\nPastikan jika anda melakukan perizinan (termasuk izin datang terlambat), anda melakukan tapping di mesin perizinan bagian pengamanan.\n\n#Perhatikan Masa Aktif Cuti Tahunan Jika Ingin Mengajukan Cuti\n#Permohonan Cuti Tahunan yang 'tidak memiliki jatah cuti tahunan' / 'jatah cuti tahunan belum aktif' akan tertolak");
		
		//alert("<?php echo $daftar_libur;?>\n\n#Jika Lembur di hari libur pastikan RENCANA JADWAL KERJA 'OFF', agar lembur diakui sebagai lembur di hari libur\n#Jika dijadwalkan kerja reguler pastikan RENCANA JADWAL KERJA diisi dengan 'Jadwal Kerjanya'\n\nPastikan Kehadiran sudah benar tanggal dan waktunya\nPastikan Cuti telah disetujui pimpinan sebelum cutoff\nPastikan SPPD sudah disetujui di NDE\nPastikan lembur anda diakui dan disetujui pimpinan sebelum cutoff");
				
        var text = ['<i class="fa fa-tags fa-fw"></i>', "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"];
        var counter = 0;
        var elem = document.getElementsByClassName("changeText");
        var inst = setInterval(change, 500);

        function change() {
            for(var i = 0; i < elem.length; i++){
                elem[i].innerHTML= text[counter];    // Change the content
            }
            //elem.innerHTML = text[counter];
            counter++;
            if (counter >= text.length) {
                counter = 0;
                // clearInterval(inst); // uncomment this if you want to stop refreshing after one cycle
            }
        }
        
	    //$('[data-toggle="tooltip"]').tooltip();
	    $('[data-toggle="tooltip"]').tooltip({
			template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner large"></div></div>'
		});
        
        /*setInterval(function(){
            $(".berkedip").toggle();
        }, 500);*/
	});
	AmCharts.makeChart("chartdiv",
		{
			"type": "pie",
			"balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]] hari</b> ([[percents]]%)</span>",
			"innerRadius": "40%",
			"titleField": "category",
			"valueField": "column-1",
			"theme": "dashboard_peruri",
			"allLabels": [],
			"balloon": {},
			"legend": {
				"enabled": true,
				"align": "center",
				"markerType": "circle"
			},
			"titles": [],
			"dataProvider": [
				<?php foreach ($grafik_kehadiran as $row): ?>
					{
						"category": "<?= $row->nama?> :",
						"column-1": <?= $row->jml?>
					},
				<?php endforeach ?>
				// {
				// 	"category": "Kehadiran",
				// 	"column-1": 8
				// },
				// {
				// 	"category": "TK",
				// 	"column-1": 6
				// },
				// {
				// 	"category": "TM",
				// 	"column-1": 2
				// },
				// {
				// 	"category": "AB",
				// 	"column-1": "1"
				// }
			]
		}
	);
</script>