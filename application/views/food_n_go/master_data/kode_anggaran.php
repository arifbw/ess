<link href="<?php echo base_url('asset/select2')?>/select2.min.css" rel="stylesheet" />
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?php echo $judul;?></h1>
            </div>
        </div>
        
        <div class="alert alert-success alert-dismissable" style="display: none;"></div>
        <div class="alert alert-danger alert-dismissable" style="display: none;"></div>
        
        <?php 
        
        if(@$akses["lihat log"]){
            echo "<div class='row text-right'>";
                echo "<button class='btn btn-primary btn-md' onclick='lihat_log()'>Lihat Log</button>";
                echo "<br><br>";
            echo "</div>";
        }
        
        if(@$akses["tambah"]){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?php echo $judul;?></a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse <?php echo $panel_tambah;?>">
                        <div class="panel-body">
                            <form role="form" action="" id="formulir_tambah" method="post" onsubmit="return false;">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-lg-2">
                                            <label>Kode Anggaran</label>
                                        </div>
                                        <div class="col-lg-7">
                                            <input class="form-control" name="nama" id="nama" placeholder="Kode Anggaran">
                                        </div>
                                        <div id="text-success" class="col-lg-3 text-success" style="display: none;"></div>
                                        <div id="text-danger" class="col-lg-3 text-danger" style="display: none;"></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <button type="button" class="btn btn-primary" id="btn-save" onclick="saveNew()">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
        }
        
        if(@$this->akses["lihat"]){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <table width="100%" class="table table-striped table-bordered table-hover" id="tabel_anggaran">
                    <thead>
                        <tr>
                            <th class='text-center'>#</th>
                            <th class='text-center'>Kode Anggaran</th>
                            <th class='text-center'>Created</th>
                            <th class='text-center'>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <?php } ?>
    </div>
</div>