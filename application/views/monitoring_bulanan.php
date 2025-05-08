<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<style type="text/css">
/*
    .fc-event-time, .fc-event-title {
        padding: 0 1px;
        white-space: normal !important;
    }
*/
	/*.select2-container--default .select2-selection--single{
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
	}*/
</style>

<!-- Page Content -->
<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Monitoring <small>(Auto refresh setalah 5 menit.)</small></h1>
		</div>
		<!-- /.col-lg-12 -->
	</div>
	<!-- /.row -->
	<div class="row" style="margin-bottom: 15px">
<!--        <form method="post" action="<?= base_url('monitoring')?>">-->
		<div class="form-group">
			<div class="col-lg-2 col-md-6 col-sm-6" id="filter_bulan">
				<label>Bulan</label>
			</div>
			<div class="col-lg-2 col-md-6 col-sm-6" style="padding-bottom: 5px">
				<select class="form-control js-example-basic-single select2" id='bulan_tahun' name='filter_tahun_bulan' style="width: 100%;" onchange="refresh_page()" required>
                    <option value="">--Pilih bulan--</option>
                    <?php foreach ($tahun_bulan as $row) {?>
						<option value='<?= $row->tahun_bulan?>' <?= $row->tahun_bulan==$tahun_bulan_tampil ? 'selected':''?>><?= id_to_bulan(substr($row->tahun_bulan,5,2))." ".substr($row->tahun_bulan,0,4)?></option>
					<?php } ?>
				</select>
			</div>
            <!--<div class="col-lg-2 col-md-6 col-sm-6" id="filter_bulan">
				<button type="submit">Tampilkan</button>
			</div>-->
		</div>
<!--        </form>-->
	</div>
	<!-- /.row -->
	<!-- /.row -->
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<i class="fa fa-spinner fa-fw"></i> Bulanan
				</div>
				<div class="panel-body">
                    <table width="100%" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class='text-center' style="max-width: 10%">No</th>
                                <th class='text-center no-sort'>Nama File</th>
                                <th class='text-center no-sort'>In_Out</th>
                                <th class='text-center no-sort'>Waktu Proses Terakhir</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $no=1; foreach($tabel_bulanan as $row){
                                if($row->waktu_in!=NULL){
                                    $tgl_proses = $row->waktu_in;
                                } else if($row->waktu_out!=NULL){
                                    $tgl_proses = $row->waktu_out;
                                } else{
                                    $tgl_proses = '-';
                                }
                            ?>
                            <tr>
                                <td><?= $no?></td>
                                <td><?= $row->nama_file?></td>
                                <td class='text-center'><?= $row->in_out?></td>
                                <td><?= $tgl_proses?></td>
                            </tr>
                            <?php $no++;}?>
                        </tbody>
                    </table>
                    <!-- /.table-responsive -->
				</div>
				<!-- /.panel-body -->
			</div>
			<!-- /.panel -->
		</div>
		<!-- /.col-lg-8 -->
	</div>
	<!-- /.row -->
</div>
<!-- /#page-wrapper -->

<!-- amCharts javascript sources -->
<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>

<script type="text/javascript">
    $('.select2').select2();
    function refresh_page() {				
        var bulan_tahun = $('#bulan_tahun').val();
        window.location.href = "<?= base_url('monitoring/bulanan/')?>" + bulan_tahun;
    }
        
    setInterval(function() {
        window.location.reload();
    }, 300000);
    
</script>