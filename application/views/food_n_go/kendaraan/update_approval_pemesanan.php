<style>
    td{
        vertical-align: top;
    }
</style>
<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="btn-x">×</button>
        <h4 class="modal-title" id="label_modal_detail">Permohonan Persetujuan Pemesanan Kendaraan</h4>
    </div>
    <div class="modal-body">
        <div class="get-approve">
            <?php $this->load->view('food_n_go/kendaraan/info_pemesanan', ['row'=>$row]); ?>
            <br>
            
            <?php if(@$row->verified==0){?>
            <form method="post" action="<?= base_url('/food_n_go/kendaraan/persetujuan_pemesanan/save_update_approval')?>" id="form-update-approval">
                <div class="alert alert-info">
                    <input type="hidden" name="id" value="<?= @$row->id?>">
                    <strong><a id="verified_by_nama"><?= @$row->verified_by_nama?></a></strong><br>
                    <!--<p id="persetujuan_approval_status">kehadiran BELUM disetujui.</p>-->
                    <br>
                    <select class="form-control" name="verified" id="verified" onchange="form_alasan_1()" style="width: 150px;" required>
                        <option value="0"></option>
                        <option value="1">Setuju</option>
                        <option value="2">Tolak</option>
                    </select>
                    <div id="form-alasan-1" style="display: none;">
                        <b>Alasan Ditolak</b>
                        <br>
                        <textarea rows="2" class="form-control" name="catatan_atasan" id="catatan_atasan" onkeyup="set_button()"></textarea>
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
    function form_alasan_1(){
        var verified = $('#verified').children("option:selected").val();
        if(verified==2){
            $('#form-alasan-1').show();
            $('#catatan_atasan').prop('required',true);
        } else{
            $('#form-alasan-1').hide();
            $('#catatan_atasan').prop('required',false);
        }
        set_button();
    }
    
    function set_button(){
        var verified = $('#verified').children("option:selected").val();
        var catatan_atasan = $('#catatan_atasan').val();
        if(verified==2 && catatan_atasan.replace(/\s/g, '').length){
            $('#btn-approval').prop('disabled',false);
        } else if(verified==1){
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
                $('#message').html(response.message);
                $('#btn-approval').prop('disabled',false);
                setTimeout(function(){ $('#message').html(''); }, 3000);
            }
         });
        
        return false;
    })
</script>