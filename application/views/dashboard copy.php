<link href="<?php echo base_url('asset/select2') ?>/select2.min.css" rel="stylesheet" />
<script src="<?php echo base_url('asset/select2') ?>/select2.min.js"></script>
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

    .slider-agenda {
        margin: 2% 0%;
        height: 300px;
    }

    .slider-agenda img {
        max-height: 300px;
    }


	.panel-violet {
	  border-color: #9900CC;
	}
	.panel-violet > .panel-heading {
	  color: #fff;
	  background-color: #9900CC;
	  border-color: #9900CC;
	}
	.panel-violet > .panel-heading + .panel-collapse > .panel-body {
	  border-top-color: #9900CC;
	}
	.panel-violet > .panel-heading .badge {
	  color: #9900CC;
	  background-color: #fff;
	}
	.panel-violet > .panel-footer + .panel-collapse > .panel-body {
	  border-bottom-color: #9900CC;
	}
	.panel-violet > a {
	  color: #9900CC;
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
				$tampil_tahun_bulan = $this->session->userdata('tampil_tahun_bulan');
				$tampil_tanggal = $tampil_tahun_bulan . "-" . $cutoff_erp_tanggal;

				$tanggal_cutoff    = tanggal(date('Y-m-d', strtotime('+1 months', strtotime($tampil_tanggal))));
				?>
				<div class="alert alert-info">
					<marquee direction="left">
						Batas akhir persetujuan dan input data (Kehadiran, Lembur, Perizinan, Cuti, Perjalanan Dinas) bulan <strong><?php echo bulan_tahun($tampil_tanggal);?></strong> adalah pada tanggal <strong><?php echo $tanggal_cutoff;?> pukul 09.00 </strong>
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
        <div class="col-md-6">
			<div class="row">
				<div class="col-lg-6 col-md-6">
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
				<div class="col-lg-6 col-md-6">
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
				<div class="col-lg-6 col-md-6">
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
				<div class="col-lg-6 col-md-6">
					<div class="panel panel-violet">
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

				<!-- On Dev -->
				<div class="col-lg-6 col-md-6">
					<div class="panel panel-red">
						<div class="panel-heading">
							<div class="row">
								<div class="col-xs-3">
									<i class="fa fa-user fa-5x"></i>
								</div>
								<div class="col-xs-9 text-right">
									<div style=' font-size: 200%;'><?php echo $total_ab; ?> Hari</div>
									<div>AB</div>
								</div>
							</div>
						</div>
						<?php
						if ($_SESSION["grup"] == 3) { //jika SDM - Remunerasi
						?>
							<a href="<?php echo base_url('informasi/ab_karyawan'); ?>">
								<div class="panel-footer">
									<span class="pull-left">View Details</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						<?php
						}
						?>
					</div>
				</div>


				<!-- <div class="col-lg-6 col-md-12">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-hourglass-end fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div style=' font-size: 200%;'><?php echo $total_terlambat; ?> Kali</div>
                                    <div>Terlambat Dalam Satu Tahun</div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo base_url('informasi/data_keterlambatan?np=' . $tampil_np_karyawan); ?>">
                            <div class="panel-footer">
                                <span class="pull-left">View Details</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div> -->
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<div class="col-lg-12 slider-agenda">
					<div id="tes-carousel" class="carousel slide" data-ride="carousel">
						<!-- indikator -->
						<ol class="carousel-indicators">
							<?php
								$data = 0; 
								if(count($agenda_slide)>0){
								foreach ($agenda_slide as $val) {
							?>
						    <li data-target="#tes-carousel" data-slide-to="<?= $data; ?>" <?= $data <= 1 ? 'class="active"' : '' ?>></li>
						    <?php
						    	$data++;
								}
								} else{
                                    echo '<li data-target="#tes-carousel" data-slide-to="0" class="active"></li>';
                                }
							?>
						</ol>
					    
					  	<div class="carousel-inner">
					    	<?php
								$active = 1; 
								if(count($agenda_slide)>0){
								foreach ($agenda_slide as $value) { 
							?>
									<div class="item <?= $active <= 1 ? 'active' : '' ?>" align="center" onclick="go_to_agenda('<?= $value->id ?>','<?= $value->agenda ?>')">
										<img src="<?= base_url('uploads/images/sikesper/agenda/' . $value->image) ?>" alt="Gambar Tidak Tersedia" style="width: 100%; max-height: 300px;">
										<?php if ($value->next_date != null && ($value->tanggal < date('Y-m-d'))) {
											echo '<p>Agenda selanjutnya tanggal: <b>' . tanggal_indonesia($value->next_date) . '</b></p>';
										} ?>
									</div>
							<?php
									$active++;
								}
							} else {
								echo '<div class="item active" align="center">
                                            <img src="https://www.peruri.co.id/upload/homepage/image_atas_1631590173_6140171d5d3b1.jpg" alt="" style="width: 100%; max-height: 300px;"><p>Belum ada agenda</p>
                                        </div>';
                                }
							?>

						</div>
                        
                        <!-- Left and right controls -->
                        <a class="left carousel-control" href="#tes-carousel" data-slide="prev">
                          <span class="glyphicon glyphicon-chevron-left"></span>
                          <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href="#tes-carousel" data-slide="next">
                          <span class="glyphicon glyphicon-chevron-right"></span>
                          <span class="sr-only">Next</span>
                        </a>
					</div>
				</div>
			</div>
		</div>
	</div>
    <br>
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
		<div class="panel panel-success">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Menunggu Persetujuan Atasan
                </div>
                <div class="panel-body">
                    
                    <div class="list-group" style="height: 350px; overflow-y: auto">
					
					<?php if(!empty($lembur_belum_diapprove)) {?>
							
                            <?php foreach($lembur_belum_diapprove as $row): ?>
							
                                <a href="<?php echo base_url('lembur/persetujuan_lembur/index?np='.$row['no_pokok'].'&bulan='.date('Y-m-d', strtotime($row['tgl_dws']))); ?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
                                    <table>
                                        
                                        <tr>
                                            <td style='vertical-align: top;'>Tertanggal</td>
                                            <td style='vertical-align: top;'>:</td>
                                            <td style='vertical-align: top;'><?php echo tanggal($row['tgl_dws']); ?></td>
                                        </tr>
										<tr>
                                            <td style='vertical-align: top; width: 110px;'>NP Approval</td>
                                            <td style='vertical-align: top; width: 10px;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['approval_np']; ?></td>
                                        </tr>
                                        <tr>
                                            <td style='vertical-align: top; width: 110px;'>Aproval</td>
                                            <td style='vertical-align: top; width: 10px;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['approval_nama']; ?></td>
                                        </tr>
                                    </table>">
                                    <i class="changeText"></i>&nbsp;<strong>Lembur</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['no_pokok'].' '.$row['nama']; ?></strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?php echo tanggal($row['tgl_dws']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <hr>
                        <?php } ?>
                        
                    </div>
            
                </div>
            </div>

            <div class="panel panel-primary">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Menunggu Persetujuan Anda
                </div>
                <div class="panel-body">
                    
                    <div class="list-group" style="height: 500px; overflow-y: auto">
                        <?php if(!empty($daftar_kehadiran)): 
                        $empty_kehadiran = false;
                        ?>
                            <?php foreach($daftar_kehadiran as $row): ?>
                                <a href="<?php echo base_url('kehadiran/persetujuan_kehadiran/index?np='.$row['np_karyawan'].'&bulan='.date('m-Y', strtotime($row['dws_tanggal']))); ?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
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
                                    <i class="changeText"></i>&nbsp;<strong>Kehadiran</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['np_karyawan'].' '.$row['nama']; ?></strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Tanggal <?php echo tanggal($row['dws_tanggal']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <hr>
                        <?php else: 
                        $empty_kehadiran = true;
                        endif; ?>
                        
                        <?php if(!empty($daftar_perizinan)): 
                        $empty_perizinan = false;
                        ?>
                            <?php foreach($daftar_perizinan as $row): ?>
                                <a href="<?php echo base_url('perizinan/persetujuan_perizinan'); ?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="<table>
                                        <tr>
                                            <td style='vertical-align: top; width: 110px;'>Tanggal Izin</td>
                                            <td style='vertical-align: top; width: 10px;'>:</td>
                                            <td style='vertical-align: top;'><?php echo tanggal($row['dws_tanggal']); ?></td>
                                        </tr>
                                        <tr>
                                            <td style='vertical-align: top;'>Alasan</td>
                                            <td style='vertical-align: top;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['alasan']; ?></td>
                                        </tr>
                                    </table>">
                                    <i class="changeText"></i>&nbsp;<strong>Perizinan</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['np_karyawan'].' '.$row['nama']; ?></strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Tanggal <?php echo tanggal($row['dws_tanggal']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <hr>
                        <?php else: 
                        $empty_perizinan = true;
                        endif; ?>
                        
                        <?php if(!empty($daftar_lembur)): 
                        $empty_lembur = false;
                        ?>
                            <?php foreach($daftar_lembur as $row): ?>
                                <a href="<?php echo base_url('lembur/persetujuan_lembur/index?np='.$row['no_pokok'].'&bulan='.date('Y-m-d', strtotime($row['tgl_dws']))); ?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
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
                                    <i class="changeText"></i>&nbsp;<strong>Lembur</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['no_pokok'].' '.$row['nama']; ?></strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span><?php echo tanggal($row['tgl_dws']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <hr>
                        <?php else: 
                        $empty_lembur = ($empty_kehadiran==true ? true:false);
                        endif; ?>
                        
                        <?php if(!empty($daftar_cuti)): 
                        $empty_cuti = false;
                        ?>
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
                                <a href="<?php echo base_url('cuti/persetujuan_cuti'); ?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
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
                                    <i class="changeText"></i>&nbsp;<strong>Cuti</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['np_karyawan'].' '.$row['nama'].' ('.$waktu.')'; ?></strong>
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
                        <hr>
                        <?php else: 
                        $empty_cuti = ($empty_lembur==true ? true:false);
                        endif; ?>

                        <?php if(in_array($_SESSION["grup"],[5,14,11])){?>
                        <?php if(in_array($_SESSION["grup"],[5,14])){
                            $link_makan_lembur = 'food_n_go/konsumsi/pemesanan_makan_lembur/daftar_persetujuan';
                            if(!empty($daftar_makan_lembur)): 
                            $empty_makan_lembur = false;
                        ?>
                            <?php foreach($daftar_makan_lembur as $row): ?>
                                <a href="<?= base_url($link_makan_lembur)?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
                                    <table>
                                        <tr>
                                            <td style='vertical-align: top;'>Jenis Lembur</td>
                                            <td style='vertical-align: top;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['jenis_lembur']; ?></td>
                                        </tr>
                                        <tr>
                                            <td style='vertical-align: top;'>Waktu Lembur</td>
                                            <td style='vertical-align: top;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['tanggal_pemesanan'] . ' ' . $row['waktu_pemesanan_mulai'] . ' s/d ' . $row['waktu_pemesanan_selesai']; ?></td>
                                        </tr>
                                    </table>">
                                    <i class="changeText"></i>&nbsp;<strong>Makan Lembur</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['np_pemesan'].' '.$row['nama_pemesan']; ?></strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Tanggal <?php echo tanggal($row['tanggal_pemesanan']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <hr>
                       <?php //else: 
                        // $empty_makan_lembur = ($empty_cuti==true ? true:false);
                        endif; ?>
                        <?php //} ?>

                        <?php //if(in_array($_SESSION["grup"],[5,14])){
                            $link_konsumsi_rapat = 'food_n_go/konsumsi/pemesanan_konsumsi_rapat/daftar_persetujuan';
                            if(!empty($daftar_konsumsi_rapat)): 
                            $empty_konsumsi_rapat = false;
                        ?>
                            <?php foreach($daftar_konsumsi_rapat as $row): ?>
                                <a href="<?= base_url($link_konsumsi_rapat)?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
                                    <table>
                                        <tr>
                                            <td style='vertical-align: top;'>Acara</td>
                                            <td style='vertical-align: top;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['nama_acara'] ?></td>
                                        </tr>
                                        <tr>
                                            <td style='vertical-align: top;'>Waktu Rapat</td>
                                            <td style='vertical-align: top;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['tanggal_pemesanan'] ?></td>
                                        </tr>
                                    </table>">
                                    <i class="changeText"></i>&nbsp;<strong>Ruang Konsumsi Rapat</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['np_pemesan'].' '.$row['nama_pemesan']; ?></strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Tanggal <?php echo tanggal($row['tanggal_pemesanan']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <hr>
                        <?php //else: 
                        //$empty_konsumsi_rapat = ($empty_konsumsi_rapat==true ? true:false);
                        endif; ?>
                        <?php } ?>
                        
                        <?php if(in_array($_SESSION["grup"],[5,11])){
                            $link_kendaraan = ($_SESSION["grup"]==11?'food_n_go/kendaraan/konfirmasi_pemesanan':'food_n_go/kendaraan/persetujuan_pemesanan');
                            if(!empty($daftar_kendaraan)): 
                            $empty_kendaraan = false;
                        ?>
                            <?php foreach($daftar_kendaraan as $row): ?>
                                <a href="<?= base_url($link_kendaraan)?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
                                    <table>
                                        <tr>
                                            <td style='vertical-align: top;'>Lokasi Jemput</td>
                                            <td style='vertical-align: top;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['nama_kota_asal'] . ', ' . $row['lokasi_jemput']; ?></td>
                                        </tr>
                                        <tr>
                                            <td style='vertical-align: top; width: 110px;'>No HP Pemesan</td>
                                            <td style='vertical-align: top; width: 10px;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['no_hp_pemesan']; ?></td>
                                        </tr>
                                    </table>">
                                    <i class="changeText"></i>&nbsp;<strong>Kendaraan</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['np_karyawan'].' '.$row['nama']; ?></strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Tanggal <?php echo tanggal($row['tanggal_berangkat']); ?></span>
                                </a>
                            <?php endforeach; ?>
                        <hr>
                        <?php else: 
                        if(@$empty_konsumsi_rapat){
                            $empty_kendaraan = ($empty_konsumsi_rapat==true  ? true:false);
                        } else{
                            $empty_kendaraan = ($empty_cuti==true  ? true:false);
                        }
                        
                        endif; ?>
                        <?php } ?>

                        <?php 
                        if($_SESSION["grup"]==5){
                            if(!empty($daftar_penilaian)){
                            $empty_penilaian = false;
                        ?>
                            <?php foreach($daftar_penilaian as $row){ ?>
                                <a href="<?= base_url('food_n_go/kendaraan/data_pemesanan')?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
                                    <table>
                                        <tr>
                                            <td style='vertical-align: top;'>Lokasi Jemput</td>
                                            <td style='vertical-align: top;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['nama_kota_asal'] . ', ' . $row['lokasi_jemput']; ?></td>
                                        </tr>
                                        <tr>
                                            <td style='vertical-align: top; width: 110px;'>Waktu</td>
                                            <td style='vertical-align: top; width: 10px;'>:</td>
                                            <td style='vertical-align: top;'><?php echo $row['jam']; ?></td>
                                        </tr>
                                    </table>">
                                    <i class="changeText"></i>&nbsp;<strong>Penilaian Driver</strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['nama_mst_driver']; ?></strong>
                                    <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Tanggal <?php echo tanggal($row['tanggal_berangkat']); ?></span>
                                </a>
                            <?php } ?>
                        <?php 
                            }
                            /* else{
                                $empty_penilaian = ($empty_kendaraan==true ? true:false);
                                if($empty_penilaian==true){
                                    echo '<a href="javascript:;" class="list-group-item"><strong>Tidak Ada Notifikasi</strong></a>';
                                }
                            }*/
                        }
                        
                      }
                        /*else{
                            if($empty_cuti==true){
                                echo '<a href="javascript:;" class="list-group-item"><strong>Tidak Ada Notifikasi</strong></a>';
                            }
                        }*/ ?>


						<!-- pelaporan -->
						<?php 
						foreach($all_pelaporan as $array){
							if(!empty($array['data'])): 
							$link = $array['url'];
							?>
								<?php foreach($array['data'] as $row): ?>
									<a href="<?php echo $link; ?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
										<table>
											<tr>
												<td style='vertical-align: top; width: 110px;'>Keterangan</td>
												<td style='vertical-align: top; width: 10px;'>:</td>
												<td style='vertical-align: top;'><?php echo $row['keterangan']; ?></td>
											</tr>
										</table>">
										<i class="changeText"></i>&nbsp;<strong><?= $array['title']?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['np_karyawan'].' '.$row['nama_karyawan']; ?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Dibuat Tanggal <?php echo tanggal($row['created_at']); ?></span>
									</a>
								<?php endforeach; ?>
							<hr>
							<?php endif; 
						}?>
						<!-- pelaporan -->
						
						<!-- faskar -->
						<?php  foreach($all_faskar as $array) {
							if(!empty($array['data'])): 
							$link = $array['url'];
							?>
								<?php foreach($array['data'] as $row): ?>
									<a href="<?php echo $link; ?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true">
										<i class="changeText"></i>&nbsp;<strong><?= $array['title']?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo 'Pemakaian Bulan '.Ym_to_MY($row['pemakaian_bulan']); ?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Disubmit Tanggal <?php echo tanggal($row['submit_date']); ?></span>
									</a>
								<?php endforeach; ?>
							<hr>
							<?php endif; 
						}?>
						<!-- faskar -->
                    </div>
            
                </div>
            </div>
            

            <div class="panel panel-red">
                <div class="panel-heading">
                    <i class="fa fa-bell fa-fw"></i> Persetujuan Ditolak
                </div>
                <div class="panel-body">
                    
                    <div class="list-group" style="height: 150px; overflow-y: auto">
						<!-- pelaporan -->
						<?php foreach($all_pelaporan_tolak as $array) {
							if(!empty($array['data'])):
								$link = $array['url']; ?>
								<?php foreach($array['data'] as $row): ?>
									<a href="<?php echo $link; ?>" class="list-group-item" data-toggle="tooltip" data-placement="left" data-html="true" title="
										<table>
											<tr>
												<td style='vertical-align: top; width: 110px;'>Keterangan</td>
												<td style='vertical-align: top; width: 10px;'>:</td>
												<td style='vertical-align: top;'><?php echo $row['keterangan']; ?></td>
											</tr>
										</table>">
										<i class="changeText"></i>&nbsp;<strong><?= $array['title']?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php echo $row['np_karyawan'].' '.$row['nama_karyawan']; ?></strong>
										<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>Dibuat Tanggal <?php echo tanggal($row['created_at']); ?></span>
									</a>
								<?php endforeach; ?>
							<hr>
							<?php endif; 
						}?>

						<?php if(empty(array_filter(array_column($all_pelaporan_tolak, 'data')))) { ?>
                            <a href="javascript:;" class="list-group-item"><strong>Tidak Ada Notifikasi</strong></a>
                        <?php } ?>
						<!-- pelaporan -->
                    </div>
            
                </div>
            </div>
            
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
		
		
		
		alert("<?php echo $daftar_libur;?>\n\nCuti Bersama <?php echo date('Y');?> akan memotong jatah cuti diawal tahun\n\nPastikan jika anda melakukan perizinan (termasuk izin datang terlambat), anda melakukan tapping di mesin perizinan bagian pengamanan.\n\n#Perhatikan Masa Aktif Cuti Tahunan Jika Ingin Mengajukan Cuti\n#Permohonan Cuti Tahunan yang 'tidak memiliki jatah cuti tahunan' / 'jatah cuti tahunan belum aktif' akan tertolak");
		
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