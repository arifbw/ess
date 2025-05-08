<script src="<?php echo base_url('asset/chartjs')?>/2.5.0/Chart.min.js"></script>
<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
<script src="<?php echo base_url('asset/select2')?>/select2.min.js"></script>
<script src="<?php echo base_url('asset/jquery')?>/jquery-1.12.4.js"></script>
<script src="<?php echo base_url('asset/jquery-ui')?>/1.12.1/jquery-ui.js"></script>

<!-- <div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header"></h1>Realisasi Target Lembur Divisi
			</div>
		</div>
		<br />
    <div class="row">
      <div class="col-lg-12">
        <form role="form" action="<?php echo base_url(); ?>master_data/admin_lembur" id="filter_realisasi" method="post">												
          <div class="row">
            <div class="col-lg-3">
              <label>Tanggal Mulai</label>
              <input type="text" class="form-control" id='start_date_realisasi' name='start_date_realisasi' style="width: 200px;">
            </div>
            <div class="col-lg-3">
              <label>Tanggal Selesai</label>
              <input type="text" class="form-control" id='end_date_realisasi' name='end_date_realisasi' style="width: 200px;">
            </div>
            <div class="col-lg-3">
              <button type="submit" value="Submit" class="btn btn-success">Filter</button>
            </div>
          </div>
		    </form>
      </div>
      <canvas id="getGrafik" style="width: 100%; height: 100%; background-color: #FFFFFF;"></canvas>
      <br/>
      <div class="col-lg-12">
        <form role="form" action="<?php echo base_url(); ?>master_data/admin_lembur" id="filter_realisasi" method="post">												
          <div class="row">
            <div class="col-lg-3">
              <label>Tanggal Mulai</label>
              <input type="text" class="form-control" id='start_date_realisasi' name='start_date_realisasi' style="width: 200px;">
            </div>
            <div class="col-lg-3">
              <label>Tanggal Selesai</label>
              <input type="text" class="form-control" id='end_date_realisasi' name='end_date_realisasi' style="width: 200px;">
            </div>
            <div class="col-lg-3">
              <button type="submit" value="Submit" class="btn btn-success">Filter</button>
            </div>
          </div>
		    </form>
      </div>
      <canvas id="getGrafik" style="width: 100%; height: 100%; background-color: #FFFFFF;"></canvas>
      <br/> -->

      <!-- tambah disini -->
    <!-- </div> -->

		

<!-- <?php
	if ($realisasi_target_divisi != NULL) {
	foreach ($realisasi_target_divisi as $key => $value) {
		$divisi[] = $value->divisi;
		$realisasi[] = $value->realisasi;
	}
	} else {
	$divisi[] = 0;
	$realisasi[] = 0;
	}
?> -->
<!-- <script>
  var ctx = document.getElementById('getGrafik').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($divisi) ?>,
      datasets: [{
        label: 'Realisasi (Rp) ',
        data: <?= json_encode($realisasi) ?>,
        lineTension: 0.3,
        backgroundColor: "rgba(78, 115, 223, 0.25)",
        borderColor: "rgba(78, 115, 223, 1)",
        pointRadius: 3,
        pointBackgroundColor: "rgba(78, 115, 223, 1)",
        pointBorderColor: "rgba(78, 115, 223, 1)",
        pointHoverRadius: 3,
        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
        pointHitRadius: 10,
        pointBorderWidth: 2,
      }]
    },
    options: {
      maintainAspectRatio: false,
      layout: {
        padding: {
          left: 10,
          right: 25,
          top: 25,
          bottom: 0
        }
      },
      scales: {
        xAxes: [{
          time: {
            unit: 'date'
          },
          gridLines: {
            display: false,
            drawBorder: false
          },
          ticks: {
            maxTicksLimit: 7
          }
        }],
        yAxes: [{
          ticks: {
            maxTicksLimit: 5,
            padding: 10,
          },
          gridLines: {
            color: "rgb(234, 236, 244)",
            zeroLineColor: "rgb(234, 236, 244)",
            drawBorder: false,
            borderDash: [2],
            zeroLineBorderDash: [2]
          }
        }],
      },
      legend: {
        display: false
      }
    }
  });
  </script> -->

<!-- <script type="text/javascript"> 
	$(function(){
		$("#start_date_realisasi, #end_date_realisasi").datepicker({
			todayHighlight: true,
			dateFormat: "yy-mm-dd"
		});
})
</script> -->

<!-- buat script baru -->

<div id="page-wrapper">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-12">
				<h1 class="page-header">Dashboard Lembur Perum Peruri</h1>
			</div>
		</div>
		<br />
    <div class="row">
      <div class="col-lg-12">
      <iframe
          src="https://metabase.peruri.co.id:8443/public/dashboard/9d5631bc-6f37-418f-9261-4fc4ec363078"
          frameborder="0"
          width="900"
          height="8000"
          allowtransparency
      ></iframe>
      </div>
      <canvas id="getGrafik" style="width: 100%; height: 100%; background-color: #FFFFFF;"></canvas>
      <br/>
  
    </div>