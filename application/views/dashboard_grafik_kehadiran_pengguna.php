<div class="col-lg-8">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bar-chart-o fa-fw"></i> Grafik Kehadiran
        </div>
        <div class="panel-body">
            <?php
            $tampil_grafik = false;
            foreach ($grafik_kehadiran as $row) {
                $ada_data =  $row->jml;
                if ($ada_data != 0 && $tampil_grafik == false) {
                    $tampil_grafik = true;
                }
            }
            
            if ($tampil_grafik == true) { ?>
            <div id="chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;"></div>
            <?php } else { ?>
            <div style="display: table;width: 100%; height: 400px;">
                <div style="display: table-cell;vertical-align: middle;text-align: center">
                    <div>
                        <span style='color: grey;'>Belum ada data yang perlu ditampilkan</span>
                    </div>
                </div>
            </div>
            <?php } ?>
            <a href="<?php echo base_url('kehadiran/data_kehadiran'); ?>" class="btn btn-default btn-block">View Details</a>
        </div>
    </div>
</div>