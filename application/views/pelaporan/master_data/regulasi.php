<link href="<?= base_url('asset/select2/select2.min.css')?>" rel="stylesheet" />
        <!-- Page Content -->
		<div id="page-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12">
						<h1 class="page-header"><?= $judul ?></h1>
					</div>
					<!-- /.col-lg-12 -->
				</div>
				<!-- /.row -->

				<?php if(!empty($this->session->flashdata('success'))): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?= $this->session->flashdata('success');?>
                    </div>
				<?php endif; ?>
				<?php if(!empty($this->session->flashdata('warning'))): ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?= $this->session->flashdata('warning');?>
                    </div>
				<?php endif; ?>

				<?php if($akses["tambah"]): ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Tambah <?= $judul ?></a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <form role="form" action="<?= base_url(); ?>pelaporan/master_data/regulasi/tambah" id="formulir_tambah" method="post">
                                            <div class="form-group row">
                                                <div class="col-lg-2">
                                                    <label>Pelaporan</label>
                                                </div>
                                                <div class="col-lg-10">
                                                    <select class="form-control" name="pelaporan" id="pelaporan" style="width: 100%;">
                                                        <option></option>
                                                        <?php foreach($pelaporan as $row): ?>
                                                            <option value="<?= $row->id ?>"><?= $row->nama ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-2">
                                                    <label>Regulasi</label>
                                                </div>
                                                <div class="col-lg-10">
                                                    <textarea class="form-control" name="regulasi" placeholder="Masukkan regulasi" rows="5"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-2">
                                                    <label>Status</label>
                                                </div>
                                                <div class="col-lg-10">
                                                    <label class='radio-inline'>
                                                        <input type="radio" name="status" id="status_tambah_aktif" value="aktif">Aktif
                                                    </label>
                                                    <label class='radio-inline'>
                                                        <input type="radio" name="status" id="status_tambah_non_aktif" value="non aktif">Non Aktif
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12 text-center">
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.col-lg-12 -->
                    </div>
                    <!-- /.row -->
				<?php endif; ?>

				<?php if($this->akses["lihat"]): ?>
                    <div class="row">
                        <table width="100%" class="table table-striped table-bordered table-hover" id="tabel_pos">
                            <thead>
                                <tr>
                                    <th class='text-center' style="width: 20%;">Pelaporan</th>
                                    <th class='text-center' style="width: 60%;">Regulasi</th>
                                    <th class='text-center' style="width: 10%;">Status</th>
                                    <?php if($akses["ubah"]): ?>
                                        <th class='text-center' style="width: 10%;">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    for($i=0;$i<count($daftar_regulasi);$i++){
                                        if($i%2==0){
                                            $class = "even";
                                        }
                                        else{
                                            $class = "odd";
                                        }
                                        
                                        echo "<tr class='$class'>";
                                            echo "<td align='center'>".$daftar_regulasi[$i]["laporan"]."</td>";
                                            echo "<td>".$daftar_regulasi[$i]["regulasi"]."</td>";
                                            echo "<td class='text-center'>";
                                                if((int)$daftar_regulasi[$i]["status"]==1){
                                                    echo "Aktif";
                                                }
                                                else if((int)$daftar_regulasi[$i]["status"]==0){
                                                    echo "Non Aktif";
                                                }
                                            echo "</td>";
                                            if($akses["ubah"]){
                                                echo "<td class='text-center'>";
                                                    if($akses["ubah"]){
                                                        echo "<button class='btn btn-primary btn-xs' onclick='ubah(".$daftar_regulasi[$i]['id'].")'>Ubah</button> ";
                                                    }
                                                echo "</td>";
                                            }
                                        echo "</tr>";
                                    }
                                ?>
                            </tbody>
                        </table>
                        <!-- /.table-responsive -->
                    </div>
				<?php endif; ?>

				<?php if($akses["ubah"]): ?>
                    <!-- Modal -->
                    <div class="modal fade" id="modal_ubah" tabindex="-1" role="dialog" aria-labelledby="label_modal_ubah" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form role="form" action="<?= base_url(); ?>pelaporan/master_data/regulasi/ubah" id="formulir_ubah" method="post">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="label_modal_ubah">Ubah <?= $judul ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group row">
                                            <div class="col-lg-2">
                                                <label>Pelaporan</label>
                                            </div>
                                            <div class="col-lg-10">
                                                <input type="hidden" name="id" id="id" value="">
                                                <select class="form-control" name="pelaporan" id="pelaporan_ubah" style="width: 100%;">
                                                    <option></option>
                                                    <?php foreach($pelaporan_ubah as $row): ?>
                                                        <option value="<?= $row->id ?>"><?= $row->nama ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-2">
                                                <label>Regulasi</label>
                                            </div>
                                            <div class="col-lg-10">
                                                <textarea class="form-control" name="regulasi" id="regulasi_ubah" placeholder="Masukkan regulasi" rows="5"></textarea>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-lg-2">
                                                    <label>Status</label>
                                                </div>
                                                <div class="col-lg-10">
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="status" id="status_ubah_aktif" value="aktif">Aktif
                                                        </label>
                                                        <label>
                                                            <input type="radio" name="status" id="status_ubah_non_aktif" value="non aktif">Non Aktif
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                    <!-- /.modal -->
				<?php endif; ?>
			</div>
			<!-- /.container-fluid -->
		</div>
		<!-- /#page-wrapper -->

<script src="<?= base_url('asset/select2/select2.min.js')?>"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#pelaporan').select2({
            placeholder: "Pilih Pelaporan"
        });
        $('#pelaporan_ubah').select2({
            placeholder: "Pilih Pelaporan"
        });
        $('#tabel_pos').DataTable({
            responsive: true
        });
    });

    function ubah(id){
        $.ajax({
            type: "POST",
            dataType: "JSON",
            url: "<?= base_url('pelaporan/master_data/regulasi/get_data') ?>",
            data: {"id":id},
            success: function(msg){
                if(msg.status == true){
                    $('#id').val(id);
                    $('#pelaporan_ubah').val(msg.data.id_laporan).change();
                    $('#regulasi_ubah').val(msg.data.regulasi);
                    if(msg.data.status=='1'){
                        $('#status_ubah_aktif').attr('checked', 'checked');
                    }else{
                        $('#status_ubah_non_aktif').attr('checked', 'checked');
                    }
                    $("#modal_ubah").modal('show');
                }else{							 
                    alert(msg.message);
                }													  
            }
        });
    }
</script>