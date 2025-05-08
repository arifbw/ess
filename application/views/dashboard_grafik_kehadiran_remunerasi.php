<div class="col-lg-8" id="div-grafik-kehadiran">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bar-chart-o fa-fw"></i> Grafik Kehadiran
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="filter-kehadiran-bulan">Bulan</label>
                        <select id="filter-kehadiran-bulan" class="form-control">
                            <?php foreach($filter_kehadiran_bulan as $row):?>
                            <option value="<?= $row->extracted_month?>" <?= $row->extracted_month == $this->session->userdata('tampil_tahun_bulan') ? 'selected':''?>><?= bulan_tahun($row->extracted_month) ?></option>
                            <?php endforeach?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="filter-kehadiran-unit">Unit</label>
                        <select id="filter-kehadiran-unit" class="form-control"></select>
                    </div>
                </div>
            </div>

            <div id="chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;"></div>

            <a href="<?php echo base_url('kehadiran/data_kehadiran'); ?>" class="btn btn-default btn-block">View Details</a>
        </div>
    </div>

    <?php if($hutang_cuti > 0):?>
    <div class="panel panel-success" id="notif-hutang-cuti" style="cursor: pointer;">
        <div class="panel-heading">
            Ada <b><?= $hutang_cuti?> karyawan</b> hutang cuti yang sudah muncul cuti besar <i class="changeText"></i>
        </div>
    </div>
    <?php endif ?>
</div>