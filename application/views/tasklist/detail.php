<style>
    td{
        vertical-align: top;
    }
</style>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="label_modal_detail">Detail <?= @$jenis?></h4>
    </div>
    <div class="modal-body">
        <div class="get-approve">
            <form method="post" action="<?= base_url('tasklist/all_activities/update')?>">
                <input type="hidden" name="id" id="id" value="<?= @$data->id?>">
                <input type="hidden" name="kode" id="kode" value="<?= @$data->kode?>">
                <div class="row">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label>Nama <?= @$jenis?> <span style="color: red;">*</span></label>
                            <input type="text" class="form-control" name="nama" id="nama" value="<?= @$data->name?>" required>
                        </div>
                    </div>
                </div>
                    
                <div class="row">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label>Deskripsi</label>
                            <input type="text" class="form-control" name="deskripsi" id="deskripsi" value="<?= @$data->description?>">
                        </div>
                    </div>
                </div>
                
                <?php
                if(@$jenis=='Activity'){?>
                <div class="row">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <label>Progres (%)</label>
                            <input type="text" class="form-control" name="progress" id="progress" value="<?= @$data->progress?>" readonly>
                        </div>
                    </div>
                </div>
                <?php }
                ?>
                
                <hr>
                <div class="row">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <p><label>Dibuat oleh: </label><small><i><?= @$data->created_by_nama.', '.@$data->created_at?></i></small></p>
                        </div>
                    </div>
                </div>
                
            </form>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
        <button type="button" id="btn-biaya" class="btn btn-primary">Simpan</button>
    </div>
</div>
