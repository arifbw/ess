<style>
    td{
        vertical-align: top;
    }
</style>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="btn-x">×</button>
        <h4 class="modal-title" id="label_modal_detail">Konfirmasi Pemesanan Kendaraan</h4>
    </div>
    <div class="modal-body">
        <div class="get-approve">
            <?php $this->load->view('food_n_go/kendaraan/info_pemesanan', ['row'=>$row]); ?>
            
            <br>
            
            <?php if(@$row->verified==1 and @$row->status_persetujuan_admin==null){?>
            <form method="post" action="<?= base_url('food_n_go/kendaraan/konfirmasi_pemesanan/save_update_konfirmasi')?>" id="form-update-approval">
                <div class="alert alert-info">
                    <input type="hidden" name="id" value="<?= @$row->id?>">
                    <strong><a id="verified_by_nama">Persetujuan admin</a></strong><br>
                    <!--<p id="persetujuan_approval_status">kehadiran BELUM disetujui.</p>-->
                    <select class="form-control" name="status_persetujuan_admin" id="status_persetujuan_admin" onchange="form_alasan_1()" style="width: 150px;" required>
                        <option value="0"></option>
                        <option value="1">Setuju</option>
                        <option value="2">Tolak</option>
                    </select>
                    
                    <div id="form-alasan-2" style="display: none;">
                        <b>Alasan Ditolak</b>
                        <br>
                        <textarea rows="2" class="form-control" name="catatan_admin_dafasum" id="catatan_admin_dafasum" onkeyup="set_button()"></textarea>
                    </div>
                    
                    <div id="form-alasan-1" style="display: none;">
                        <b>Pilih Driver</b>
                        <br>
                        <select class="form-control select2" name="id_mst_driver" id="id_mst_driver" onchange="set_driver(); set_button();" style="width: 100%;">
                            <option value=""></option>
                            <?php foreach($driver as $x){?>
                            <option value="<?= $x->id?>" data-kode="<?= $x->id_mst_kendaraan_default?>"><?= $x->np_karyawan.' - '.$x->nama?></option>
                            <?php }?>
                        </select>
                        <input type="hidden" name="nama_mst_driver" id="nama_mst_driver">
                        
                        <br>
                        <b>Pilih Kendaraan</b>
                        <br>
                        <select class="form-control select2" name="id_mst_kendaraan" id="id_mst_kendaraan" onchange="set_kendaraan(); set_button();" style="width: 100%;" disabled>
                            <option value=""></option>
                            <?php foreach($mst_kendaraan as $x){?>
                            <option value="<?= $x->id?>"><?= $x->nopol.' | '.$x->nama?></option>
                            <?php }?>
                        </select>
                        <input type="hidden" name="id_mst_kendaraan_post" id="id_mst_kendaraan_post">
                        <input type="hidden" name="nama_mst_kendaraan" id="nama_mst_kendaraan">
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center" id="message"></div>
                    <div class="col-lg-12 text-right">
                        <button type="submit" id="btn-approval" class="btn btn-block btn-success" disabled>Submit</button>
                    </div>
                </div>
            </form>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    
    $('.select2').select2();
    function form_alasan_1(){
        var status_persetujuan_admin = $('#status_persetujuan_admin').children("option:selected").val();
        if(status_persetujuan_admin==2){
            $('#catatan_admin_dafasum').prop('required',true);
            $('#id_mst_driver').prop('required',false);
            $('#form-alasan-1').hide();
            $('#form-alasan-2').show();
        } else if(status_persetujuan_admin==1){
            $('#catatan_admin_dafasum').prop('required',false);
            $('#id_mst_driver').prop('required',true);
            $('#form-alasan-1').show();
            $('#form-alasan-2').hide();
        } else{
            $('#form-alasan-1').hide();
            $('#form-alasan-2').hide();
            $('#catatan_admin_dafasum').prop('required',false);
            $('#id_mst_driver').prop('required',false);
        }
        set_button();
    }
    
    function set_driver(){
        var id_mst_kendaraan = $('#id_mst_driver').children("option:selected").attr('data-kode');
        var nama_mst_driver = $('#id_mst_driver').children("option:selected").text();
        
        //console.log(nama_mst_driver);
        $('#nama_mst_driver').val(nama_mst_driver);
        
        $('#id_mst_kendaraan').val(id_mst_kendaraan);
        $('#id_mst_kendaraan').select2().trigger('change');
    }
    
    function set_kendaraan(){
        var id_mst_kendaraan = $('#id_mst_kendaraan').children("option:selected").val();
        var nama_mst_kendaraan = $('#id_mst_kendaraan').children("option:selected").text();
        $('#id_mst_kendaraan_post').val(id_mst_kendaraan);
        $('#nama_mst_kendaraan').val(nama_mst_kendaraan);
        //console.log(nama_mst_kendaraan);
    }
    
    function set_button(){
        var status_persetujuan_admin = $('#status_persetujuan_admin').children("option:selected").val();
        var id_mst_driver = $('#id_mst_driver').children("option:selected").val();
        var id_mst_kendaraan = $('#id_mst_kendaraan').children("option:selected").val();
        var catatan_admin_dafasum = $('#catatan_admin_dafasum').val();
        if(status_persetujuan_admin==2 && catatan_admin_dafasum.replace(/\s/g, '').length){
            $('#btn-approval').prop('disabled',false);
        } else if(status_persetujuan_admin==1 && id_mst_driver!='' && id_mst_kendaraan!=''){
            $('#btn-approval').prop('disabled',false);
        } else{
            $('#btn-approval').prop('disabled',true);
        }
    }
    
    $('#form-update-approval').on('submit', function(){
        $('#btn-approval').prop('disabled',true);
        $('#message').html('Menyimpan...');
        
        var that = $(this),
            url = that.attr('action'),
            type = that.attr('method');
        $.ajax({
            url: url,
            type: type,
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData:false,
            dataType: "JSON",
            success: function(response){
                $('#message').html(response.message);
                //$('#btn-approval').prop('disabled',false);
                setTimeout(function(){ 
                    $('#message').html(''); 
                    $('#btn-x').trigger('click');
                }, 3000);
            },
            error: function(){
                alert('Gagal terhubung ke server');
                $('#message').html('Gagal terhubung ke server');
                $('#btn-approval').prop('disabled',false);
                setTimeout(function(){ $('#message').html(''); }, 3000);
            }
         });
        
        return false;
    })
</script>