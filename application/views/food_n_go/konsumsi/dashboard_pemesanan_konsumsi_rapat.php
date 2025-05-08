<link rel="stylesheet" type="text/css" href="<?= base_url('asset/daterangepicker/daterangepicker.css')?>" />
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $judul;?></h1>
            </div>
        </div>
        
        <?php
        if(@$akses["lihat log"]){
            echo "<div class='row text-right'>";
                echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
                echo "<br><br>";
            echo "</div>";
        }
        
        if(@$this->akses["lihat"]){
        ?>
        
        <div class="form-group">
            <div class="row">
                <div class="col-md-3">
                    <label>Pilih Tanggal</label>
                    <input class="form-control" type="text" id="tanggal" name="dates" style="width: 200px;">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <table width="100%" class="table table-striped table-bordered table-hover" id="tabel-data-rapat">
                    <thead>
                        <tr>
                            <th class='text-center'>Tanggal</th>
                            <th class='text-center'>Kegiatan</th>
                            <th class='text-center'>Pukul</th>
                            <th class='text-center'>Tempat</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-lg-12">
                <table width="100%" class="table table-striped table-bordered table-hover" id="tabel-data-lembur">
                    <thead>
                        <tr>
                            <th class='text-center'>Tanggal</th>
                            <th class='text-center'>Kegiatan</th>
                            <th class='text-center'>Pukul</th>
                            <th class='text-center'>Tempat</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<script type="text/javascript" src="<?= base_url('asset/moment.js/2.29.1/moment.min.js')?>"></script>
<script type="text/javascript" src="<?= base_url('asset/daterangepicker/daterangepicker.min.js')?>"></script>
<script>
    $(document).ready(function() {
        $('input[name="dates"]').daterangepicker({
            
        }, function(start, end, label){
            loadTableRapat(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
            loadTableLembur(start.format('YYYY-MM-DD'),end.format('YYYY-MM-DD'));
        });
    });
</script>